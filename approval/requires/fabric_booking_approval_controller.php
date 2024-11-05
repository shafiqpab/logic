<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_ip=$_SESSION['logic_erp']['user_ip'];

extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');
include('../../includes/class4/class.conditions.php');
include('../../includes/class4/class.reports.php');
include('../../includes/class4/class.fabrics.php');
include('../../includes/class4/class.yarns.php');
include('../../includes/class4/class.trims.php');
include('../../includes/class4/class.emblishments.php');
require_once('../../mailer/class.phpmailer.php');
$from_mail="";

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];

if($action=="load_drop_down_buyer")
{
	$load="load_drop_down( 'requires/fabric_booking_approval_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'requires/fabric_booking_approval_controller', this.value, 'load_drop_down_brand', 'brand_td');";
	echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, $load );

	exit();
}
if($action=="report_formate_setting")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=12 and report_id=176 and is_deleted=0 and status_active=1");
	echo "print_report_button_setting('$print_report_format');\n";
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
if ($action=="load_drop_down_brand")
{
	$data_arr = explode("*", $data);
	
	echo create_drop_down( "cbo_brand_id", 60, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data_arr[0]' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $data_arr[2], "" );
	
	exit();
}

if ($action=="load_drop_down_season")
{
	$data_arr = explode("*", $data);
	
	echo create_drop_down( "cbo_season_id", 70, "select id, season_name from lib_buyer_season where buyer_id='$data_arr[0]' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", $data_arr[2], "" );
	
	exit();
}


if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$sequence_no='';
	$company_name=str_replace("'","",$cbo_company_name);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$cbo_brand_id=str_replace("'","",$cbo_brand_id);
	$cbo_season_id=str_replace("'","",$cbo_season_id);
	$cbo_season_year=str_replace("'","",$cbo_season_year);
	$cbo_style_owner_id=str_replace("'","",$cbo_style_owner_id);
	$txt_alter_user_id=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;

	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$company_fullname_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$season=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$brand=return_library_array("select id,brand_name from lib_buyer_brand","id","brand_name");


	if($txt_alter_user_id!="")
	{
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			$log_sql = sql_select("SELECT user_level,buyer_id,unit_id,is_data_level_secured FROM user_passwd WHERE id = '$txt_alter_user_id' AND valid = 1");
			foreach($log_sql as $r_log)
			{
				if($r_log[csf('is_data_level_secured')]==1)
				{
					if($r_log[csf('buyer_id')]!="") $buyer_id_cond2=" and a.buyer_id in (".$r_log[csf('buyer_id')].")"; else $buyer_id_cond2="";
				}
				else $buyer_id_cond2="";
			}
		}
		else $buyer_id_cond2=" and a.buyer_id=$cbo_buyer_name";
		
		if(str_replace("'","",$cbo_brand_id)==0)
		{
			foreach($log_sql as $r_log)
			{
				if($r_log[csf('is_data_level_secured')]==1)
				{
					if($r_log[BRAND_ID]!=""){$brand_id_cond=" and a.BRAND_ID in (".$r_log[BRAND_ID].")";} else {$brand_id_cond="";}
				}
				else $brand_id_cond="";
			}
		}
		else {$brand_id_cond=" and a.BRAND_ID=$cbo_brand_id";}
		
		
		$user_id=$txt_alter_user_id;
	}
	else
	{
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="")
				{
					$buyer_id_cond2=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
				}
				else $buyer_id_cond2="";
			}
			else $buyer_id_cond2="";
		}
		else $buyer_id_cond2=" and a.buyer_id=$cbo_buyer_name";
		
		if(str_replace("'","",$cbo_brand_id)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["brand_id"]!=""){$brand_id_cond=" and a.BRAND_ID in (".$_SESSION['logic_erp']["brand_id"].")";} else {$brand_id_cond="";}
			}
			else $brand_id_cond="";
		}
		else{$brand_id_cond=" and a.BRAND_ID=$cbo_brand_id";}
	}

	$file_no=str_replace("'","",$txt_file_no);
	$internal_ref=str_replace("'","",$txt_internal_ref);
	$booking_no=str_replace("'","",$txt_booking_no);
	$booking_year=str_replace("'","",$cbo_booking_year);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and c.file_no='".trim($file_no)."'";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and c.grouping like '%".trim($internal_ref)."%' ";
	if ($booking_no=="") $booking_no_cond=""; else $booking_no_cond=" and a.booking_no_prefix_num='".trim($booking_no)."' ";
	if ($booking_year=="" || $booking_year==0) $booking_year_cond=""; else $booking_year_cond=" and to_char(a.insert_date,'YYYY')='".trim($booking_year)."' ";

	if($cbo_brand_id>0){	$brand_cond_3="and c.brand_id='$cbo_brand_id'";
		$brand_cond_3="and a.brand_id='$cbo_brand_id'";	}else{	$brand_cond_3="";$brand_cond_3="";	}
	if($cbo_season_id>0){	 $season_cond_3="and c.season_buyer_wise='$cbo_season_id'"; $season_cond_3="and a.season_id='$cbo_season_id'";	}else{	$season_cond_3="";$season_cond_3="";		}
	if($cbo_season_year>0){	 $season_year_cond_3="and c.season_year='$cbo_season_year'"; 	 $season_year_cond_3="and a.season_year='$cbo_season_year'";}else{$season_year_cond_3="";	$season_year_cond_3="";}

	$date_cond='';
	if(str_replace("'","",$txt_date)!="")
	{
		if(str_replace("'","",$cbo_get_upto)==1) $date_cond=" and a.booking_date>$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==2) $date_cond=" and a.booking_date<=$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==3) $date_cond=" and a.booking_date=$txt_date";
		else $date_cond='';
	}

	$approval_type = str_replace("'","",$cbo_approval_type);
	if($previous_approved==1 && $approval_type==1)
	{
		$previous_approved_type=1;
	}
	 
	//$user_id=137;
	$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
	//$job_dealing_merchant_array = return_library_array("select job_no, dealing_marchant from wo_po_details_master","job_no","dealing_marchant");

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");

	//echo $cbo_company_name;die;
	
	
	$min_sequence_no=return_field_value("min(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0","seq");


	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Fabric Booking.</font>";
		die;
	}
	
	$buyer_ids_array=array();
	//$buyerData=sql_select("select user_id, sequence_no, buyer_id,BRAND_ID from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0 and bypass=2 and sequence_no<=$user_sequence_no");
	
	$buyerData=sql_select("select user_id, sequence_no, buyer_id,BRAND_ID from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");
	foreach($buyerData as $row)
	{
		$buyer_ids_array[$row[csf('user_id')]]['u']=$row[csf('buyer_id')];
		$buyer_ids_array[$row[csf('sequence_no')]]['s']=$row[csf('buyer_id')];
		$brand_ids_array[$row[csf('user_id')]]['u']=$row[csf('BRAND_ID')];
		$brand_ids_array[$row[csf('sequence_no')]]['s']=$row[csf('BRAND_ID')];

	}
	
	// var_dump($buyer_ids_array);die;
	 //echo $menu_id;die;
	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Fabric Booking.</font>";
		die;
	}

	if($db_type==0)
	{
		$year_field="YEAR(a.insert_date) as year";
		$orderBy_cond="IFNULL";
	}
	else if($db_type==2)
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
		$orderBy_cond="NVL";
	}
	else
	{
		$year_field="";//defined Later
		$orderBy_cond="ISNULL";
	}

	
	
	if($previous_approved==1 && $approval_type==1)
	{
		$buyer_ids=$buyer_ids_array[$user_id]['u'];
		if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
		$sequence_no_cond=" and b.sequence_no<'$user_sequence_no'";
		$approved_user_cond=" and b.approved_by='$user_id'";

		$brand_ids = $brand_ids_array[$user_id]['u'];
		if($brand_ids=="") $brand_id_cond2=""; else $brand_id_cond2=" and a.BRAND_ID in($brand_ids)";
		
		$sql="select a.id, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, b.id as approval_id, b.sequence_no, b.approved_by, a.is_apply_last_update,a.entry_form,max(b.approved_no) as revised_no,c.grouping from wo_booking_mst a, approval_history b, wo_po_break_down c,wo_booking_dtls d where a.id=b.mst_id and a.booking_no=d.booking_no and d.job_no=c.job_no_mst  and b.entry_form=7 and a.company_id=$company_name and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.ready_to_approved=1 and a.is_approved  in (1,3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $internal_ref_cond $file_no_cond $date_cond $booking_no_cond $booking_year_cond $approved_user_cond $brand_cond_3 $brand_id_cond $brand_id_cond2 $season_cond_3 $season_year_cond_3
		group by a.id, a.booking_no_prefix_num, a.booking_no,a.entry_form, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, b.id, b.sequence_no, b.approved_by, a.is_apply_last_update,a.update_date, a.insert_date,c.grouping
		 order by $orderBy_cond(a.update_date, a.insert_date) desc";
		 //echo $sql;die;
		//$buyer_id_cond
	}
	else if($approval_type==0)
	{
		if($db_type==0)
		{
			$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id='' and brand_id='' and is_deleted=0","seq");
		}
		else
		{
			$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and brand_id is null and is_deleted=0","seq");
		}

	  //	echo $user_sequence_no.'='.$min_sequence_no;die;
		if($user_sequence_no==$min_sequence_no)
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
			
			$brand_ids = $brand_ids_array[$user_id]['u'];
			if($brand_ids=="") $brand_id_cond2=""; else $brand_id_cond2=" and a.BRAND_ID in($brand_ids)";
			
			$approved_user_cond=" and c.approved_by='$user_id'";
			$sql="select a.id,a.entry_form, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.is_apply_last_update,(select max(c.approved_no) as revised_no from approval_history c where a.id=c.mst_id $approved_user_cond) as revised_no from wo_booking_mst a, wo_booking_dtls b , wo_po_break_down c where a.booking_no=b.booking_no and b.job_no=c.job_no_mst and a.company_id=$company_name and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond $internal_ref_cond $buyer_id_cond2 $booking_no_cond $date_cond $booking_year_cond 	$brand_cond_3 $brand_id_cond $brand_id_cond2 $season_cond_3 $season_year_cond_3 group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,a.entry_form order by $orderBy_cond(a.update_date, a.insert_date) desc";
           //echo $sql;die;
		}
		else if($sequence_no=="")
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";

			$brand_ids=$brand_ids_array[$user_id]['u'];
			if($brand_ids=="") $brand_id_cond2=""; else $brand_id_cond2=" and a.brand_id in($brand_ids)";
			
		
			if($db_type==0)
			{
				$booking_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_name and a.is_short=2 and a.booking_type=1 and a.item_category in(2,3,13) and b.sequence_no=$user_sequence_no and b.entry_form=7 and b.current_approval_status=1","booking_id");
			}
			else
			{
				$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_name and a.is_short=2 and a.booking_type=1 and a.item_category in(2,3,13) and b.sequence_no=$user_sequence_no and b.entry_form=7 and b.current_approval_status=1","booking_id");
			}
			
			
			$seqSql="select sequence_no, bypass, buyer_id,brand_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
			$seqData=sql_select($seqSql);
			//$sequence_no_by=$seqData[0][csf('sequence_no_by')];
			//$buyerIds=$seqData[0][csf('buyer_ids')];//die("with seq");
			
			
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
			//var_dump($check_buyerIds_arr);die;
			$buyerIds=chop($buyerIds,',');
			if($buyerIds=="")
			{
				$buyerIds_cond="";
				$seqCond="";
			}
			else
			{
				$buyerIds_cond=" and a.buyer_id not in($buyerIds)";
				$seqCond=" and (".chop($query_string,'or ').")";
			}
			//echo $seqCond;die;
			
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
			
			
			$sequence_no_by_no=chop($sequence_no_by_no,',');
			$sequence_no_by_yes=chop($sequence_no_by_yes,',');

			if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
			if($sequence_no_by_no=="") $sequence_no_by_no=0;

			$booking_id='';
			$booking_id_sql="select distinct (mst_id) as booking_id from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_name and a.is_short=2 and a.booking_type=1 and a.item_category in(2,3,13) and b.sequence_no in ($sequence_no_by_no) and b.entry_form=7 and b.current_approval_status=1 $buyer_id_cond $date_cond $seqCond
			union
			select distinct (mst_id) as booking_id from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_name and a.is_short=2 and a.booking_type=1 and a.item_category in(2,3,13) and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=7 and b.current_approval_status=1 $buyer_id_cond $date_cond";
			
			
		   //echo $booking_id_sql;die;

			
			$bResult=sql_select($booking_id_sql);
			foreach($bResult as $bRow)
			{
				$booking_id.=$bRow[csf('booking_id')].",";
			}

			$booking_id=chop($booking_id,',');
			$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
			//echo $booking_id;die;
			$result=array_diff(explode(',',$booking_id),explode(',',$booking_id_app_byuser));
			$booking_id=implode(",",$result);

			$booking_id_cond="";
			if($booking_id!="")
			{
				if($db_type==2 && count($result)>999)
				{
					$booking_id_chunk_arr=array_chunk($result,999) ;
					foreach($booking_id_chunk_arr as $chunk_arr)
					{
						$chunk_arr_value=implode(",",$chunk_arr);
						$bokIds_cond.=" a.id in($chunk_arr_value) or ";
					}
					$booking_id_cond.=" and (".chop($bokIds_cond,'or ').")";
					//echo $booking_id_cond;die;
				}
				else $booking_id_cond=" and a.id in($booking_id)";
			}
			else $booking_id_cond="";

			if($db_type==0)
			{
				if($booking_id!="")
				{
					$approved_user_cond=" and c.approved_by='$user_id'";
					$sql="select a.id, a.entry_form,a.update_date as ob_update, a.insert_date as ob_insertdate, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id,  a.is_approved, a.is_apply_last_update,(select max(c.approved_no) as revised_no from approval_history c where a.id=c.mst_id $approved_user_cond) as revised_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1  and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond  $date_cond $buyerIds_cond $buyer_id_cond2 $booking_no_cond $brand_cond_3 $season_cond_3 $season_year_cond_3 group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,a.entry_form
						union all
						select a.id, a.entry_form,a.update_date as ob_update, a.insert_date as ob_insertdate, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.is_apply_last_update,(select max(c.approved_no) as revised_no from approval_history c where a.id=c.mst_id $approved_user_cond) as revised_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.is_short in(2,3) and a.booking_type=1  and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved in(3) and b.fin_fab_qnty>0 and a.id in($booking_id) $buyer_id_cond $buyer_id_cond2 $date_cond $booking_no_cond $brand_cond_3 $season_cond_3 $season_year_cond_3 group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date,a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,a.entry_form order by $orderBy_cond(ob_update, ob_insertdate) desc";
				}
				else
				{
					$approved_user_cond=" and c.approved_by='$user_id'";
					$sql="select a.id, a.entry_form,$year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.is_apply_last_update,(select max(c.approved_no) as revised_no from approval_history c where a.id=c.mst_id $approved_user_cond) as revised_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0  and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond $buyer_id_cond2 $date_cond $buyerIds_cond $booking_no_cond $booking_year_cond $brand_cond_3 $season_cond_3 $season_year_cond_3 group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,a.entry_form order by $orderBy_cond(a.update_date, a.insert_date) desc";
				}
				//echo $sql;
			}
			else
			{		
				
				
				if($booking_id!="")
				{   // and a.id in($booking_id)
					
					$preUserBrand=array();
					 if($approval_type==0 && $brand_ids_array[$user_sequence_no]['s']!=''){
						 foreach($seqData as $sqRow){
							if($sqRow[csf('bypass')]==2){
								foreach(explode(',',$sqRow[csf('brand_id')]) as $brid){
										$preUserBrand[$brid] = $brid;
								}
							} 
						 }
					 
						
						$preUserBrandArr=array();
						foreach(explode(',',$brand_ids_array[$user_sequence_no]['s']) as $brid){
							if(!in_array($brid,$preUserBrand)){
								$preUserBrandArr[$brid] = $brid;
							}
						}
						
						if(implode($preUserBrand)){$brand_cond_4= "and a.BRAND_ID in(".implode(',',$preUserBrandArr).")";}
					 }
					
					
					
					$approved_user_cond=" and c.approved_by='$user_id'";
					$sql="select * from(select a.id, a.entry_form,a.update_date, a.insert_date, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.is_apply_last_update,(select max(c.approved_no) as revised_no from approval_history c where a.id=c.mst_id $approved_user_cond) as revised_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0  and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond  $date_cond $buyerIds_cond $buyer_id_cond2 $booking_no_cond $brand_cond_3 $brand_cond_4 $brand_id_cond2 $season_cond_3 $season_year_cond_3 group by a.id, a.booking_no,a.entry_form, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num
						union all
						select a.id, a.entry_form,a.update_date, a.insert_date, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.is_apply_last_update,(select max(c.approved_no) as revised_no from approval_history c where a.id=c.mst_id $approved_user_cond) as revised_no from wo_booking_mst a, wo_booking_dtls b  where a.booking_no=b.booking_no and a.company_id=$company_name and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1  and a.is_approved in (3) and b.fin_fab_qnty>0 $booking_id_cond $buyer_id_cond $buyer_id_cond2 $date_cond $booking_no_cond 	$brand_cond_3 $brand_id_cond2 $season_cond_3 $season_year_cond_3 group by a.id, a.booking_no,a.entry_form, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num) order by $orderBy_cond(update_date, insert_date) desc";
                }
				else
				{
					
				$buyer_ids=$buyer_ids_array[$user_id]['u'];
				if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
				$brand_ids=$brand_ids_array[$user_id]['u'];
				if($brand_ids=="") $brand_id_cond2=""; else $brand_id_cond2=" and a.brand_id in($brand_ids)";
					
					
					
					$approved_user_cond=" and c.approved_by='$user_id'";
					$sql="select a.id, a.entry_form,$year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.is_apply_last_update,(select max(c.approved_no) as revised_no from approval_history c where a.id=c.mst_id $approved_user_cond) as revised_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0  and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond $buyer_id_cond2  $brand_id_cond2  $date_cond $buyerIds_cond $booking_no_cond $booking_year_cond $brand_cond_3 $season_cond_3 $season_year_cond_3 $brandIds_cond group by a.id, a.booking_no,a.entry_form, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num order by $orderBy_cond(a.update_date, a.insert_date) desc";
                   // echo $sql;die;
                }


			}
			
			
			// echo $brandIds_cond;die;
			 //echo $sql;die;
			
			 
		}
		else
		{ 
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";

			$user_sequence_no = $user_sequence_no-1;
			//echo $sequence_no;
			/*	if($sequence_no==$user_sequence_no)
			{
				$sequence_no_by_pass='';
			}
			else
			{*/
				if($db_type==0)
				{
					$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");
				}
				else
				{
					$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");
				}

				if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
				else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
				$approved_user_cond=" and c.approved_by='$user_id'";

				$sql="select a.id, a.entry_form,$year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date,a.is_approved, b.id as approval_id, b.sequence_no, b.approved_by, a.is_apply_last_update,(select max(c.approved_no) as revised_no from approval_history c where a.id=c.mst_id $approved_user_cond) as revised_no from wo_booking_mst a, approval_history b where a.id=b.mst_id  and b.entry_form=7 and a.company_id=$company_name and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.status_active=1  and a.is_deleted=0 and b.current_approval_status=1 and a.ready_to_approved=1 and a.is_approved in (1,3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond   $date_cond $booking_no_cond $booking_year_cond 	$brand_cond_3 $season_cond_3 $season_year_cond_3 order by $orderBy_cond(a.update_date, a.insert_date) desc";
				 //echo $sql;
			//}

		}
	
	}
	else
	{
		$buyer_ids=$buyer_ids_array[$user_id]['u'];
		if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
		$sequence_no_cond=" and b.approved_by='$user_id'";
		
		$brand_ids=$brand_ids_array[$user_id]['u'];
		if($brand_ids=="") $brand_id_cond2=""; else $brand_id_cond2=" and a.brand_id in($brand_ids)";

		$sql="select a.id, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, b.id as approval_id, b.sequence_no, b.approved_by, a.is_apply_last_update,a.entry_form,max(b.approved_no) as revised_no,c.grouping from wo_booking_mst a, approval_history b, wo_po_break_down c,wo_booking_dtls d where a.id=b.mst_id and a.booking_no=d.booking_no and d.job_no=c.job_no_mst   and b.entry_form=7 and a.company_id=$company_name and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.current_approval_status=1 and a.ready_to_approved=1 and a.is_approved  in (1,3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $internal_ref_cond $file_no_cond $date_cond $booking_no_cond $booking_year_cond $brand_cond_3 $season_cond_3 $season_year_cond_3 $brand_id_cond2
		group by a.id, a.booking_no_prefix_num, a.booking_no,a.entry_form, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, b.id, b.sequence_no, b.approved_by, a.is_apply_last_update,a.update_date, a.insert_date,c.grouping
		 order by $orderBy_cond(a.update_date, a.insert_date) desc";
    }

	
	
	/// echo $sql;
	$nameArray=sql_select($sql);

	$booking_id_arr=array();
	$bookidstr="";
	foreach ($nameArray as $row)
	{
		if($bookidstr=="") $bookidstr=$row[csf('id')]; else $bookidstr.=','.$row[csf('id')];
		$booking_id_arr[$row[csf('id')]]=$row[csf('id')];
	}
	
	$booknoId=implode(",",array_filter(array_unique(explode(",",$bookidstr))));
	$book_ids=count(explode(",",$bookidstr)); $bookingidCond="";
	if($db_type==2 && $book_ids>1000)
	{
		$bookingidCond=" and (";
		$bookingnoIdArr=array_chunk(explode(",",$booknoId),999);
		foreach($bookingnoIdArr as $ids)
		{
			$ids=implode(",",$ids);
			$bookingidCond.=" a.id in($ids) or"; 
		}
		$bookingidCond=chop($bookingidCond,'or ');
		$bookingidCond.=")";
	}
	else $bookingidCond=" and a.id in($booknoId)"; 


	if($cbo_brand_id>0){	$brand_cond="and d.brand_id='$cbo_brand_id'";	}else{	$brand_cond="";	}
	if($cbo_season_id>0){	 $season_cond="and d.season_buyer_wise='$cbo_season_id'";	}else{	$season_cond="";		}
	if($cbo_season_year>0){	 $season_year_cond="and d.season_year='$cbo_season_year'";}else{$season_year_cond="";			}
	
	 
	if($cbo_style_owner_id){$whereCon = " and d.STYLE_OWNER=$cbo_style_owner_id";}	
	$sql_job=sql_select("select a.pay_mode, a.booking_no, a.booking_no_prefix_num, b.po_break_down_id, b.job_no, c.grouping, c.file_no, d.dealing_marchant, d.quotation_id,d.brand_id,
	d.season_buyer_wise,d.season_year,d.style_ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d where a.booking_no=b.booking_no and b.po_break_down_id =c.id and b.job_no=c.job_no_mst  and c.job_no_mst=d.job_no and b.job_no=d.job_no and a.company_id=$company_name and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 $whereCon $brand_cond 	$season_cond $season_year_cond and  b.is_deleted=0 and a.ready_to_approved=1 and b.fin_fab_qnty>0  $booking_no_cond $date_cond $internal_ref_cond $booking_year_cond $bookingidCond ");
//group by a.pay_mode, a.booking_no, b.po_break_down_id, b.job_no, c.grouping, c.file_no, d.dealing_marchant, d.quotation_id,d.brand_id,d.season_buyer_wise,d.season_year, a.booking_no_prefix_num,d.style_ref_no
	$job_information_arr=array();
	foreach( $sql_job as $jval)
	{
		/*$jobDataArr[$jval[csf('job_no')]]['jobno'][]=$jval[csf('job_no')];
		$jobDataArr[$jval[csf('job_no')]]['deal_march'][]=$jval[csf('dealing_marchant')].'=='.$jval[csf('job_no')];
		$jobDataArr[$jval[csf('job_no')]]['po_break_down_id'][]=$jval[csf('po_break_down_id')];
		$jobDataArr[$jval[csf('job_no')]]['grouping'][]=$jval[csf('grouping')];
		$jobDataArr[$jval[csf('job_no')]]['file_no'][]=$jval[csf('file_no')];*/
		
		//$job_information_arr[$jval[csf('booking_no')]]['jobno'][]=$jval[csf('job_no')];
		$job_information_arr[$jval[csf('booking_no')]]['quot_no'][]=$jval[csf('quotation_id')];
		$job_information_arr[$jval[csf('booking_no')]]['deal_march'][]=$jval[csf('dealing_marchant')].'=='.$jval[csf('job_no')];
		$job_information_arr[$jval[csf('booking_no')]]['po_break_down_id'][]=$jval[csf('po_break_down_id')];
		$job_information_arr[$jval[csf('booking_no')]]['grouping'][]=$jval[csf('grouping')];
		$job_information_arr[$jval[csf('booking_no')]]['file_no'][]=$jval[csf('file_no')];
		$job_paymode_arr[$jval[csf('booking_no')]]=$jval[csf('pay_mode')];

			 $job_sise_data[$jval[csf('booking_no')]]['brand'] =$brand[$jval[csf('brand_id')]];
			 $job_sise_data[$jval[csf('booking_no')]]['season'] =$season[$jval[csf('season_buyer_wise')]];
			 $job_sise_data[$jval[csf('booking_no')]]['style_ref'] =$jval[csf('style_ref_no')];
			 $job_sise_data[$jval[csf('booking_no')]]['season_year'] =$jval[csf('season_year')];
			 $season_data_arr[$jval[csf('booking_no_prefix_num')]]['season'] =$season[$jval[csf('season_buyer_wise')]];
	}
	?>
    <script>
		function openmypage_app_cause(wo_id,app_type,i)
		{ 
			var txt_alter_user_id = $("#txt_alter_user_id").val(); 
			var txt_appv_cause = $("#txt_appv_cause_"+i).val();
			var approval_id = $("#approval_id_"+i).val();
			var data=wo_id+"_"+app_type+"_"+txt_appv_cause+"_"+approval_id;
			var title = 'Approval Cause Info';
			var page_link = 'requires/fabric_booking_approval_controller.php?data='+data+'&action=appcause_popup&txt_alter_user_id='+txt_alter_user_id;
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
			var page_link = 'requires/fabric_booking_approval_controller.php?data='+data+'&action=appinstra_popup';
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
			var page_link = 'requires/fabric_booking_approval_controller.php?data='+data+'&action=unappcause_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=180px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var unappv_request=this.contentDoc.getElementById("hidden_unappv_request");
				$('#txt_unappv_req_'+i).val(unappv_request.value.trim());
			}
		}
	</script>
    <?
		$print_report_format_ids_partial_wvn = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=138 and is_deleted=0 and status_active=1");
		$format_ids_partial_wvn=explode(",",$print_report_format_ids_partial_wvn);

	 
		
		
		$print_report_format_ids_partial = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=35 and is_deleted=0 and status_active=1");
		$format_ids_partial=explode(",",$print_report_format_ids_partial);
	
		$print_report_format_ids_short = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=2 and is_deleted=0 and status_active=1");
		$print_report_ids_short=explode(",",$print_report_format_ids_short);

		$print_report_format_ids2 = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
		$format_ids=explode(",",$print_report_format_ids2);
		

		$print_report_format_ids_3 = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=43 and is_deleted=0 and status_active=1");
		$format_ids_3=explode(",",$print_report_format_ids_3);

		$print_report_format_ids_122 = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=122 and is_deleted=0 and status_active=1");
		$format_ids_122=explode(",",$print_report_format_ids_122);


		$cost_control_source=return_field_value("cost_control_source","variable_order_tracking","company_name=".$cbo_company_name." and variable_list in (53) and status_active=1 and is_deleted=0","cost_control_source");
		
		if($approval_type==0)
		{
			$fset=2000; $table1=1980; $table2=1960;
		}
		else if($approval_type==1)
		{
			$fset=2162; $table1=1980; $table2=1960;
		}
		
		if($cost_control_source==1) 
		{
			$fset=2232; $table1=2032; $table2=2032;
			$qcCostSheetNo = return_library_array("select qc_no, cost_sheet_no from qc_mst","qc_no","cost_sheet_no");
		}
		
		//echo $table1;die;
	
		$sql_req="select booking_id,approval_cause from fabric_booking_approval_cause where entry_form=7 ".where_con_using_array($booking_id_arr,0,'booking_id')."  and approval_type=2 and status_active=1 and is_deleted=0";				
		$nameArray_req=sql_select($sql_req);
		foreach($nameArray_req as $row)
		{
			$unappv_req_arr[$row[csf('booking_id')]]=$row[csf('approval_cause')];
		}	

 
 

	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:<?=$fset; ?>px; margin-top:10px">
        <legend>Fabric Booking Approval</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?=$table1; ?>" class="rpt_table" >
                <thead>
                	<th width="40">&nbsp;</th>
                    <th width="30">SL</th>
                    <th width="60">Mkt Cost</th>
                    <? if($cost_control_source==1) { ?>
                    <th width="60">QC Id</th>
                    <? } ?>
                    <th width="70">Booking No</th>
                    <th width="70">Last Version</th>
                    <th width="70">Fabric Source</th>
                    <th width="50">Year</th>
                    <th width="80">Type</th>
                    <th width="100">Booking Date</th>
                    <th width="125">Buyer</th>
                    <th width="160">Supplier</th>
					<th width="80">Brand</th>
					<th width="80">Season</th>
					<th width="60">Season Year</th>
					<th width="100">Style Ref.</th>
                    <th width="100">Job No</th>
                    <th width="100">Master Style/Internal Ref.</th>
                    <th width="70">File</th>
                    <th width="110">Dealing Merchant</th>
                    <th width="50">Image</th>
                    <th width="50">File</th>
                    <th width="90">Delivery Date</th>
                    <?
					if($approval_type==0) echo "<th width='80'>Appv Instra</th>";
					if($approval_type==1) echo "<th width='80'>Un-appv request</th>";
					?>
                    <th ><? if($approval_type==0) echo "Not Appv. Cause" ; else echo "Not Un-Appv. Cause"; ?></th>
                </thead>
            </table>
            <div style="width:<?=$table1; ?>px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?=$table2; ?>" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
					//	 echo $sql;

                            $i=1; $all_approval_id='';
                            $nameArray=sql_select( $sql );
                            foreach ($nameArray as $row)
                            { 
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								

								$value=$row[csf('id')];
								if($row[csf('booking_type')]==4)
								{
									$booking_type="Sample";
									$type=3;
								}
								else
								{
									if($row[csf('is_short')]==1) $booking_type="Short";
                                    elseif($row[csf('is_short')]==3) $booking_type="Dia Wise";
                                    else { $booking_type="Main"; $type=$row[csf('is_short')]; }
								}

								//=========== for job file internal reff===========
								$dealing_merchant='';
								$dealing_merchant_arr=array();
								$job_no_arr=array();
								$all_job_no='';
								foreach( $job_information_arr[$row[csf('booking_no')]]['deal_march'] as $key=>$dl_data )
								{
									$exda="";
									$exda=explode("==",$dl_data);
									$job_no_arr[]=$exda[1];
									$dealing_merchant_arr[$exda[0]]=$dealing_merchant_array[$exda[0]];
								}

								if($cbo_style_owner_id && count($job_no_arr) ==0){continue;}


								$job_no_arr=array_unique($job_no_arr);
								$all_job_no=implode(",",$job_no_arr);
								$dealing_merchant_arr=array_unique($dealing_merchant_arr);
								$dealing_merchant=implode(",",$dealing_merchant_arr);
								
								$qc_arr=array();
								$all_qc_no='';
								 
								foreach( $job_information_arr[$row[csf('booking_no')]]['quot_no'] as $key=>$qcdata )
								{
									$qc_arr[]=$qcdata;
								}
								//print_r($qc_arr);
								$all_qc_no=implode(",",array_unique($qc_arr));
								

								// file no information..........................................
								$file_no_arr=array();
								$all_file_no='';
								foreach( $job_information_arr[$row[csf('booking_no')]]['file_no'] as $key=>$file_data )
								{
									$file_no_arr[]=$file_data;
								}
								$file_no_arr=array_unique($file_no_arr);
								$all_file_no=implode(",",$file_no_arr);
								// internal reference information.....................................
								$all_internal_ref='';
								$internal_ref_arr=array();
								foreach( $job_information_arr[$row[csf('booking_no')]]['grouping'] as $key=>$internalref_data )
								{
									$internal_ref_arr[]=$internalref_data;
								}
								$internal_ref_arr=array_unique($internal_ref_arr);
								$all_internal_ref=implode(",",$internal_ref_arr);

								// order no information....................................
								$all_po_id='';
								$po_id_arr=array();
								foreach( $job_information_arr[$row[csf('booking_no')]]['po_break_down_id'] as $key=>$po_data )
								{
									$po_id_arr[]=$po_data;
								}
								$po_id_arr=array_unique($po_id_arr);
								$all_po_id=implode(",",$po_id_arr);

								if($row[csf('approval_id')]==0) $print_cond=1;
								else
								{
									if($duplicate_array[$row[csf('entry_form')]][$row[csf('id')]][$row[csf('sequence_no')]][$row[csf('approved_by')]]=="")
									{
										$duplicate_array[$row[csf('entry_form')]][$row[csf('id')]][$row[csf('sequence_no')]][$row[csf('approved_by')]]=$row[csf('id')];
										$print_cond=1;
									}
									else
									{
										if($all_approval_id=="") $all_approval_id=$row[csf('approval_id')]; else $all_approval_id.=",".$row[csf('approval_id')];
										$print_cond=0;
									}
								}
								//print_r($format_ids_partial_wvn);
								if($print_cond==1)
								{
									//$fabric_nature=$_SESSION['fabric_nature'];
									$fabric_nature=$row[csf('item_category')];
									if($row[csf('booking_type')]==1 && $row[csf('entry_form')]==108) $row_id=$format_ids_partial[0];
									else if($row[csf('booking_type')]==1 && $row[csf('entry_form')]==271) $row_id=$format_ids_partial_wvn[0];
									else if($row[csf('booking_type')]==1 && $row[csf('entry_form')]==118) $row_id=$format_ids[0];
									else if($row[csf('booking_type')]==1 && $row[csf('entry_form')]==86) $row_id=$format_ids[0];
									else $row_id=$print_report_ids_short[0];

								//	echo $row[csf('entry_form')].'='.$row_id.'DDD';
								
									$variable='';$print_button="";
									
									// echo $row_id.'**'.$row[csf('entry_form')];
								
									if($row[csf('entry_form')]==86) //Budget wise fab booking
									{
										
										if($row_id==73)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_b6','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}else if($row_id==274)
										{
										
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_10','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($row_id==53)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_jk','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}  

										
										else if($row_id==45)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_urmi','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($row_id==6)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report4','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($row_id==7)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report5','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($row_id==5)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report2','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($row_id==3)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report3','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($row_id==1)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_gr','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
									}
									else if($row[csf('entry_form')]==271){


										if($row_id==143){
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_urmi','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($row_id==84){ 
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_urmi_per_job','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($row_id==85){
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_3','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($row_id==151){
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_4','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($row_id==160){
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_5','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($row_id==175){
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_6','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($row_id==241){
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_11','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($row_id==155){
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','fabric_booking_report','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($row_id==274){
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_10','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($row_id==72){
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print6booking','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($row_id==428){

											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_eg1','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}

										


									}
									// else if($row[csf('entry_form')]==118){


									// 	if($row_id==786){
									// 		$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report25','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
									// 	}
									// 	else if($row_id==426){
									// 		$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_print23','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
									// 	}
									// }
									
									else
									{
										
									
									   if($format_ids[0]==719)
										{
											
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report16','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}else if($format_ids[0]==404)
										{
											
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report21','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}else if($format_ids[0]==274)
										{
										
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_10','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($format_ids[0]==1)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_gr','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]." <a/>";
										}
										else if($format_ids[0]==2)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report','".$i."',".$fabric_nature.")\">  ".$row[csf('booking_no_prefix_num')]. "<a/>";
										}
										else if($format_ids[0]==3)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report3','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]." <a/>";
										}
										else if($format_ids[0]==4)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report1','".$i."',".$fabric_nature.")\">" .$row[csf('booking_no_prefix_num')]. "<a/>";
										}
										else if($format_ids[0]==5)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report2','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]." <a/>";
										}
										else if($format_ids[0]==6)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report4','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]." <a/>";
										}
										else if($format_ids[0]==7)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report5','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]." <a/>";
										}
										else if($format_ids[0]==45)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_urmi','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]." <a/>";
										}
										else  if($format_ids[0]==53)
										{
											$variable="<a href='#'  onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_jk','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]." <a/>";
										}
										else if($format_ids[0]==93)
										{
											$variable="<a href='#'  onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_libas','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]." <a/>";
										}
										else if($format_ids[0]==73)
										{
											$variable="<a href='#'  onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_mf','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]." <a/>";
										}
										else if($format_ids[0]==85)
										{
											$variable="<a href='#'  onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_3','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($format_ids[0]==143)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_urmi','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($format_ids[0]==220)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_northern_new','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($format_ids[0]==160)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_5','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($format_ids[0]==269)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_knit','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										if($variable=="") $variable="".$row[csf('booking_no_prefix_num')]."";
										
										if($format_ids[0]==220)
										{
											$print_button="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_northern_new','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]."<a/>";
										}else if($format_ids[0]==719)
										{
											$print_button="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report16','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]."<a/>";
										}
										else if($format_ids[0]==723)
										{
											$print_button="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_17','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]."<a/>";
										}
										else if($format_ids[0]==502)
										{
											$print_button="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report26','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]."<a/>";
										}
										else
										{
											$print_button="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_urmi','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
										}
										
										
										if($row_id==155)//woven partial booking
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','fabric_booking_report','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}else if($row_id==723)//woven partial booking
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_17','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										if($format_ids[0]==274)
										{
											$print_button="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_10','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]."<a/>";

										}if($format_ids[0]==723)
										{
											$print_button="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_17','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]."<a/>";

										}else if($row_id==155){ //woven partial booking
											$print_button="<a href='#'  onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','fabric_booking_report','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]."<a/>";
										
										}
										else
										{
											$print_button="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_urmi','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
										}

	
										
									
									}
									
									$variable1='';if($print_button=="") $print_button=$fabric_source[$row[csf('fabric_source')]];
									if($row[csf('revised_no')]>0)
									{
										$variable1="<a href='#' onClick=\"generate_fabric_report_history('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$fabric_nature."','".$row[csf('fabric_source')]."','".($row[csf('revised_no')])."','".$all_job_no."'".")\"> ".($row[csf('revised_no')])."<a/>";
									}
									
									
									$app_cause=return_field_value("approval_cause", "fabric_booking_approval_cause", "id in(select MAX(id) as id from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=7 and user_id='$user_id' and booking_id='$value' and approval_type=$approval_type and status_active=1 and is_deleted=0) and status_active=1 and is_deleted=0");

								 
									if($row[csf('entry_form')]==118){
										if($row_id==3){
										$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report3','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";

									}else if($row_id==2){
										$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";

									}else if($row_id==502){
										$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report26','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";

									}else if($row_id==426){
										$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_print23','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";

									}else if($row_id==786){
										$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report25','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";

									}
									else{
										$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report17_v1','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
									}
								}

								if($row[csf('entry_form')]==108){
									if($row_id==143){
									$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_urmi','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
								}
								else{
									$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_b6','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
								}
							}

									?>
									<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
										<td width="40" align="center" valign="middle">
											<input type="checkbox" id="tbl_<?=$i;?>" name="tbl[]" onClick="check_last_update(<?=$i;?>);" />
											<input id="booking_id_<?=$i;?>" name="booking_id[]" type="hidden" value="<?=$value; ?>" />
											<input id="booking_no_<?=$i;?>" name="booking_no[]" type="hidden" value="<?=$row[csf('booking_no')]; ?>" />
											<input id="approval_id_<?=$i;?>" name="approval_id[]" type="hidden" value="<?=$row[csf('approval_id')]; ?>" />
                                            <input id="last_update_<?=$i;?>" name="last_update[]" type="hidden" value="<?=$row[csf('is_apply_last_update')]; ?>" />
                                            <input id="<?=strtoupper($row[csf('booking_no')]); ?>" name="no_booook[]" type="hidden" value="<?=$i;?>" />
										</td>
										<td width="30" id="td_<?=$i; ?>" style="cursor:pointer" align="center" onClick="generate_worder_report2(<?=$type; ?>,'<?=$row[csf('booking_no')]; ?>',<?=$row[csf('company_id')]; ?>,'<?=$row[csf('po_break_down_id')]; ?>',<?=$row[csf('item_category')].','.$row[csf('fabric_source')]; ?>,'<?=$row[csf('job_no')]; ?>','<?=$row[csf('is_approved')]; ?>','show_fabric_booking_report3')"><?=$i; ?></td>
                                        <td width="60" align="center"><a href='##' style='color:#000' onClick="generate_mkt_report('<?=$all_job_no; ?>','<?=$row[csf('booking_no')]; ?>','<?=$all_po_id; ?>','<?=$row[csf('item_category')]; ?>','<?=$row[csf('fabric_source')]; ?>','show_fabric_comment_report')">View</a></td>
                                        <? if($cost_control_source==1) { ?>
                                        <td width="60" align="center" style="word-break:break-all">&nbsp;&nbsp;<a href='##' style='color:#000' onClick="generate_qc_report('<?=$all_qc_no; ?>','<?=$qcCostSheetNo[$all_qc_no]; ?>','quick_costing_print2')"><?=$all_qc_no; ?></a></td>
                                        <? } ?>
										<td width="70" align="center" style="word-break:break-all">&nbsp;&nbsp;<?=$variable; ?></td>
										<td width="70" align="center" style="word-break:break-all">&nbsp;&nbsp;<?=$variable1; ?></td>
                                        <td width="70" align="center" style="word-break:break-all"><?=$print_button; ?></td>
                                        <td width="50" align="center"><?=$row[csf('year')]; ?></td>
										<td width="80" align="center" style="word-break:break-all"><?=$booking_type; ?></td>
										<td width="100" align="center" style="word-break:break-all"><? if($row[csf('booking_date')]!="0000-00-00") echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</td>
										<td width="125" align="center" style="word-break:break-all"><?=$buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</td>
										<td width="160" align="center" style="word-break:break-all">
										<?
											if($job_paymode_arr[$row[csf('booking_no')]]==3 || $job_paymode_arr[$row[csf('booking_no')]]==5)
											{
												echo $company_fullname_arr[$row[csf('supplier_id')]];
											}
											else
											{
												echo $supplier_arr[$row[csf('supplier_id')]];
											}
										?>&nbsp;</td>
										
										<?
									
									
										?>
										<td width="80" align="center" style="word-break:break-all"><?=$job_sise_data[$row[csf('booking_no')]]['brand']; ?>&nbsp;</td>
										<td width="80"align="center" style="word-break:break-all"><?=$job_sise_data[$row[csf('booking_no')]]['season'];?>&nbsp;</td>
										<td width="60" align="center"style="word-break:break-all"><?
										if($job_sise_data[$row[csf('booking_no')]]['season_year']==0){echo "";}else{ echo $job_sise_data[$row[csf('booking_no')]]['season_year'];} ?>&nbsp;</td>
										<td width="100" align="center"style="word-break:break-all"><?=$job_sise_data[$row[csf('booking_no')]]['style_ref']; ?>&nbsp;</td>
										<??>
										<td width="100" align="center" style="word-break:break-all">
										
										<?
											
										foreach($job_no_arr as $jobNo){
											echo "<a href='#' onClick=\"generate_worder_report4('".$jobNo."','')\"> ".$jobNo." <a/>";
											// if($format_ids_3[0]==730){
											// 	echo "<a href='#' onClick=\"generate_worder_report4('".$jobNo."','budgetsheet')\"> ".$jobNo." <a/>";
											// }else if($row[csf('entry_form')]==271){
											// 	echo "<a href='#' onClick=\"generate_worder_report3('".$jobNo."','basic_cost')\"> ".$jobNo." <a/>";
											// }else{
											// 	echo "<a href='#' onClick=\"generate_worder_report4('".$jobNo."','preCostRpt4')\"> ".$jobNo." <a/>";
											// }
										}
										?>
                                        &nbsp;</td>
                                        <td width="100" align="center" style="word-break:break-all"><?=$all_internal_ref; ?></td>
                    					<td width="70" align="center"style="word-break:break-all"><?=$all_file_no; ?></td>
										<td width="110" id="dealing_merchant_<?=$i;?>" style="word-break:break-all"><?=$dealing_merchant; ?>&nbsp;</td>
										<td width="50" align="center"><a href="##" onClick="openImgFile('<?=$all_job_no;?>','img');">View</a></td>
										<td width="50" align="center"><a href="##" onClick="openImgFile('<?=$all_job_no;?>','file');">View</a></td>
										<td align="center" width="90" style="word-break:break-all"><? if($row[csf('delivery_date')]!="0000-00-00") echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</td>
                                        <?
										if($approval_type==0)echo "<td align='center' width='80'>
                                        		<input name='txt_appv_instra[]' class='text_boxes' readonly placeholder='Please Browse' ID='txt_appv_instra_".$i."' style='width:70px' maxlength='50' onClick='openmypage_app_instrac(".$value.",".$approval_type.",".$i.")'></td>";
											if($approval_type==1)echo "<td align='center' width='80'>
                                        		<input name='txt_unappv_req[]' class='text_boxes' readonly placeholder='Please Browse' ID='txt_unappv_req_".$i."' style='width:70px' maxlength='50' value='".$unappv_req_arr[$row[csf('id')]]."'  onClick='openmypage_unapp_request(".$value.",".$approval_type.",".$i.")'></td>";
                                        ?>
                                        <td align="center" style="word-break:break-all">
                                        	<input name="txt_appv_cause[]" class="text_boxes" readonly placeholder="Please Browse" ID="txt_appv_cause_<?=$i;?>" style="width:100px" maxlength="50" title="Maximum 50 Character" onClick="openmypage_app_cause(<?=$value; ?>,<?=$approval_type; ?>,<?=$i;?>)" value="<?=$app_cause; ?>">&nbsp;</td>
									</tr>
									<?
									$i++;
								}

								if($all_approval_id!="")
								{
									$con = connect();
									$rID=sql_multirow_update("approval_history","current_approval_status",0,"id",$all_approval_id,1);
									disconnect($con);
								}
							}
							$denyBtn=""; $isApp="";
							if($approval_type==0) $denyBtn=""; else $denyBtn=" display:none";
							if($approval_type==1) $isApp=" display:none"; else $isApp="";

						

						?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="<?=$table1; ?>" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" style=" <?=$isApp; ?>">
                    	<input type="checkbox" id="all_check" onClick="check_all('all_check')" />
                        <input type="hidden" name="hide_approval_type" id="hide_approval_type" value="<?=$approval_type; ?>">
                        <font style="display:none"><?=$all_approval_id; ?></font>
                    </td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<?=$i; ?>,<?=$approval_type; ?>);"/>&nbsp;&nbsp;&nbsp;<input type="button" value="<? if($approval_type==0) echo "Deny"; ?>" class="formbutton" style="width:100px;<?=$denyBtn; ?>" onClick="submit_approved(<?=$i; ?>,5);"/></td>
				</tfoot>
			</table>
        </fieldset>
    </form>
 <?
	exit();
}


if($action=="generate_show_2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$sequence_no='';
	$company_name=str_replace("'","",$cbo_company_name);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$cbo_brand_id=str_replace("'","",$cbo_brand_id);
	$cbo_season_id=str_replace("'","",$cbo_season_id);
	$cbo_season_year=str_replace("'","",$cbo_season_year);
 
	//$txt_alter_user_id=($txt_alter_user_id)?$txt_alter_user_id:$user_id;

	$txt_alter_user_id=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;
 

	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$company_fullname_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$season=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$brand=return_library_array("select id,brand_name from lib_buyer_brand","id","brand_name");

	$_SESSION['logic_erp']["brand_id"]=($_SESSION['logic_erp']["brand_id"]==0)?"":$_SESSION['logic_erp']["brand_id"];

	if($txt_alter_user_id!="")
	{
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			$log_sql = sql_select("SELECT user_level,buyer_id,unit_id,is_data_level_secured FROM user_passwd WHERE id = '$txt_alter_user_id' AND valid = 1");
			foreach($log_sql as $r_log)
			{
				if($r_log[csf('is_data_level_secured')]==1)
				{
					if($r_log[csf('buyer_id')]!="") $buyer_id_cond2=" and a.buyer_id in (".$r_log[csf('buyer_id')].")"; else $buyer_id_cond2="";
				}
				else $buyer_id_cond2="";
			}
		}
		else $buyer_id_cond2=" and a.buyer_id=$cbo_buyer_name";
		
		if(str_replace("'","",$cbo_brand_id)==0)
		{
			foreach($log_sql as $r_log)
			{
				if($r_log[csf('is_data_level_secured')]==1)
				{
					if($r_log[BRAND_ID]!=""){$brand_id_cond=" and a.BRAND_ID in (".$r_log[BRAND_ID].")";} else {$brand_id_cond="";}
				}
				else $brand_id_cond="";
			}
		}
		else {$brand_id_cond=" and a.BRAND_ID=$cbo_brand_id";}
		
		
		$user_id=$txt_alter_user_id;
	}
	else
	{
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="")
				{
					$buyer_id_cond2=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
				}
				else $buyer_id_cond2="";
			}
			else $buyer_id_cond2="";
		}
		else $buyer_id_cond2=" and a.buyer_id=$cbo_buyer_name";
		
		if(str_replace("'","",$cbo_brand_id)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["brand_id"]!=""){$brand_id_cond=" and a.BRAND_ID in (".$_SESSION['logic_erp']["brand_id"].")";} else {$brand_id_cond="";}
			}
			else $brand_id_cond="";
		}
		else{$brand_id_cond=" and a.BRAND_ID=$cbo_brand_id";}
	}

	$file_no=str_replace("'","",$txt_file_no);
	$internal_ref=str_replace("'","",$txt_internal_ref);
	$booking_no=str_replace("'","",$txt_booking_no);
	$booking_year=str_replace("'","",$cbo_booking_year);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and c.file_no='".trim($file_no)."'";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and c.grouping like '%".trim($internal_ref)."%' ";
	if ($booking_no=="") $booking_no_cond=""; else $booking_no_cond=" and a.booking_no_prefix_num='".trim($booking_no)."' ";
	if ($booking_year=="" || $booking_year==0) $booking_year_cond=""; else $booking_year_cond=" and to_char(a.insert_date,'YYYY')='".trim($booking_year)."' ";

	if($cbo_brand_id>0){	$brand_cond_3="and c.brand_id='$cbo_brand_id'";
		$brand_cond_3="and a.brand_id='$cbo_brand_id'";	}else{	$brand_cond_3="";$brand_cond_3="";	}
	if($cbo_season_id>0){	 $season_cond_3="and c.season_buyer_wise='$cbo_season_id'"; $season_cond_3="and a.season_id='$cbo_season_id'";	}else{	$season_cond_3="";$season_cond_3="";		}
	if($cbo_season_year>0){	 $season_year_cond_3="and c.season_year='$cbo_season_year'"; 	 $season_year_cond_3="and a.season_year='$cbo_season_year'";}else{$season_year_cond_3="";	$season_year_cond_3="";}

	$date_cond='';
	if(str_replace("'","",$txt_date)!="")
	{
		if(str_replace("'","",$cbo_get_upto)==1) $date_cond=" and a.booking_date>$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==2) $date_cond=" and a.booking_date<=$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==3) $date_cond=" and a.booking_date=$txt_date";
		else $date_cond='';
	}

	$approval_type = str_replace("'","",$cbo_approval_type);
	if($previous_approved==1 && $approval_type==1)
	{
		$previous_approved_type=1;
	}
	 
	//$user_id=137;
	$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
	
	//$job_dealing_merchant_array = return_library_array("select job_no, dealing_marchant from wo_po_details_master","job_no","dealing_marchant");

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");

	//echo $cbo_company_name;die;
	$min_sequence_no=return_field_value("min(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0","seq");

	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Fabric Booking.</font>";
		die;
	}
	
	$buyer_ids_array=array();
	//$buyerData=sql_select("select user_id, sequence_no, buyer_id,BRAND_ID from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0 and bypass=2 and sequence_no<=$user_sequence_no");
	
	$buyerData=sql_select("select user_id, sequence_no, buyer_id,BRAND_ID from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");
	foreach($buyerData as $row)
	{
		$buyer_ids_array[$row[csf('user_id')]]['u']=$row[csf('buyer_id')];
		$buyer_ids_array[$row[csf('sequence_no')]]['s']=$row[csf('buyer_id')];
		$brand_ids_array[$row[csf('user_id')]]['u']=$row[csf('BRAND_ID')];
		$brand_ids_array[$row[csf('sequence_no')]]['s']=$row[csf('BRAND_ID')];
	}
	
	// var_dump($buyer_ids_array);die;
	//echo $menu_id;die;
	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Fabric Booking.</font>";
		die;
	}

	if($db_type==0)
	{
		$year_field="YEAR(a.insert_date) as year";
		$orderBy_cond="IFNULL";
	}
	else if($db_type==2)
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
		$orderBy_cond="NVL";
	}
	else
	{
		$year_field="";//defined Later
		$orderBy_cond="ISNULL";
	}
	
	if($previous_approved==1 && $approval_type==1)
	{
		$buyer_ids=$buyer_ids_array[$user_id]['u'];
		if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
		$sequence_no_cond=" and b.sequence_no<'$user_sequence_no'";
		$approved_user_cond=" and b.approved_by='$user_id'";

		$brand_ids = $brand_ids_array[$user_id]['u'];
		if($brand_ids=="") $brand_id_cond2=""; else $brand_id_cond2=" and a.BRAND_ID in($brand_ids)";
		
		$sql="select a.id, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, b.id as approval_id, b.sequence_no, b.approved_by, a.is_apply_last_update,a.entry_form,max(b.approved_no) as revised_no,c.grouping from wo_booking_mst a, approval_history b, wo_po_break_down c,wo_booking_dtls d where a.id=b.mst_id and a.booking_no=d.booking_no and d.job_no=c.job_no_mst  and b.entry_form=7 and a.company_id=$company_name and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.ready_to_approved=1 and a.is_approved  in (1,3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $internal_ref_cond $file_no_cond $date_cond $booking_no_cond $booking_year_cond $approved_user_cond $brand_cond_3 $brand_id_cond $brand_id_cond2 $season_cond_3 $season_year_cond_3
		group by a.id, a.booking_no_prefix_num, a.booking_no,a.entry_form, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, b.id, b.sequence_no, b.approved_by, a.is_apply_last_update,a.update_date, a.insert_date,c.grouping
		 order by $orderBy_cond(a.update_date, a.insert_date) desc";
		 //echo $sql;die;
		//$buyer_id_cond
	}
	else if($approval_type==0)
	{
		if($db_type==0)
		{
			$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id='' and is_deleted=0","seq");
		}
		else
		{
			$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
		}

		//echo $user_sequence_no;die;
		
		if($user_sequence_no==$min_sequence_no)
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
			
			$brand_ids = $brand_ids_array[$user_id]['u'];
			if($brand_ids=="") $brand_id_cond2=""; else $brand_id_cond2=" and a.BRAND_ID in($brand_ids)";
			
			$approved_user_cond=" and c.approved_by='$user_id'";
			$sql="select a.id,a.entry_form, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.is_apply_last_update,(select max(c.approved_no) as revised_no from approval_history c where a.id=c.mst_id $approved_user_cond) as revised_no from wo_booking_mst a, wo_booking_dtls b , wo_po_break_down c where a.booking_no=b.booking_no and b.job_no=c.job_no_mst and a.company_id=$company_name and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1  and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond $internal_ref_cond $buyer_id_cond2 $booking_no_cond $date_cond $booking_year_cond 	$brand_cond_3 $brand_id_cond $brand_id_cond2 $season_cond_3 $season_year_cond_3 group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,a.entry_form order by $orderBy_cond(a.update_date, a.insert_date) desc";
           // echo $sql;die;
		}
		else if($sequence_no=="")
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";

			$brand_ids=$brand_ids_array[$user_id]['u'];
			if($brand_ids=="") $brand_id_cond2=""; else $brand_id_cond2=" and a.brand_id in($brand_ids)";
			
		
			if($db_type==0)
			{
				$booking_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_name and a.is_short=2 and a.booking_type=1 and a.item_category in(2,3,13) and b.sequence_no=$user_sequence_no and b.entry_form=7 and b.current_approval_status=1","booking_id");
			}
			else
			{
				$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_name and a.is_short=2 and a.booking_type=1 and a.item_category in(2,3,13) and b.sequence_no=$user_sequence_no and b.entry_form=7 and b.current_approval_status=1","booking_id");
			}
			
			
			$seqSql="select sequence_no, bypass, buyer_id,brand_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
			$seqData=sql_select($seqSql);
			//$sequence_no_by=$seqData[0][csf('sequence_no_by')];
			//$buyerIds=$seqData[0][csf('buyer_ids')];//die("with seq");
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
			//var_dump($check_buyerIds_arr);die;
			$buyerIds=chop($buyerIds,',');
			if($buyerIds=="")
			{
				$buyerIds_cond="";
				$seqCond="";
			}
			else
			{
				$buyerIds_cond=" and a.buyer_id not in($buyerIds)";
				$seqCond=" and (".chop($query_string,'or ').")";
			}
			//echo $seqCond;die;
			
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
			
			
			$sequence_no_by_no=chop($sequence_no_by_no,',');
			$sequence_no_by_yes=chop($sequence_no_by_yes,',');

			if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
			if($sequence_no_by_no=="") $sequence_no_by_no=0;

			$booking_id='';
			$booking_id_sql="select distinct (mst_id) as booking_id from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_name and a.is_short=2 and a.booking_type=1 and a.item_category in(2,3,13) and b.sequence_no in ($sequence_no_by_no) and b.entry_form=7 and b.current_approval_status=1 $buyer_id_cond $date_cond $seqCond
			union
			select distinct (mst_id) as booking_id from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_name and a.is_short=2 and a.booking_type=1 and a.item_category in(2,3,13) and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=7 and b.current_approval_status=1 $buyer_id_cond $date_cond";
			
			
		  // echo $booking_id_sql;die;
			$bResult=sql_select($booking_id_sql);
			foreach($bResult as $bRow)
			{
				$booking_id.=$bRow[csf('booking_id')].",";
			}

			$booking_id=chop($booking_id,',');
			$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
			//echo $booking_id;die;
			$result=array_diff(explode(',',$booking_id),explode(',',$booking_id_app_byuser));
			$booking_id=implode(",",$result);

			$booking_id_cond="";
			if($booking_id!="")
			{
				if($db_type==2 && count($result)>999)
				{
					$booking_id_chunk_arr=array_chunk($result,999) ;
					foreach($booking_id_chunk_arr as $chunk_arr)
					{
						$chunk_arr_value=implode(",",$chunk_arr);
						$bokIds_cond.=" a.id in($chunk_arr_value) or ";
					}
					$booking_id_cond.=" and (".chop($bokIds_cond,'or ').")";
					//echo $booking_id_cond;die;
				}
				else $booking_id_cond=" and a.id in($booking_id)";
			}
			else $booking_id_cond="";

			$allow_partial_sql=sql_select("SELECT b.approval_need,b.allow_partial
						  FROM approval_setup_mst a, approval_setup_dtls b
						 WHERE     a.id = b.mst_id
						       AND a.status_active = 1
						       AND a.is_deleted = 0
						       AND b.status_active = 1
						       AND b.is_deleted = 0
						       AND a.company_id=$company_name
						       AND b.page_id=5
						  order by A.SETUP_DATE desc");
			
			$allow_partial=0;
			$approval_cond=' and a.is_approved in(3,1)';
			if(count($allow_partial_sql))
			{
				$allow_partial=$allow_partial_sql[0][csf('allow_partial')];
				if($allow_partial==1)
				{
					$approval_cond=' and a.is_approved in(0)';
				}
			}
			if($db_type==0)
			{
				if($booking_id!="")
				{
					$approved_user_cond=" and c.approved_by='$user_id'";
					$sql="select a.id, a.entry_form,a.update_date as ob_update, a.insert_date as ob_insertdate, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id,  a.is_approved, a.is_apply_last_update,(select max(c.approved_no) as revised_no from approval_history c where a.id=c.mst_id $approved_user_cond) as revised_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1  and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond  $date_cond $buyerIds_cond $buyer_id_cond2 $booking_no_cond $brand_cond_3 $season_cond_3 $season_year_cond_3 group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,a.entry_form
						union all
						select a.id, a.entry_form,a.update_date as ob_update, a.insert_date as ob_insertdate, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.is_apply_last_update,(select max(c.approved_no) as revised_no from approval_history c where a.id=c.mst_id $approved_user_cond) as revised_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.is_short in(2,3) and a.booking_type=1  and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 $approval_cond and b.fin_fab_qnty>0 and a.id in($booking_id) $buyer_id_cond $buyer_id_cond2 $date_cond $booking_no_cond $brand_cond_3 $season_cond_3 $season_year_cond_3 group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date,a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,a.entry_form order by $orderBy_cond(ob_update, ob_insertdate) desc";
				}
				else
				{
					$approved_user_cond=" and c.approved_by='$user_id'";
					$sql="select a.id, a.entry_form,$year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.is_apply_last_update,(select max(c.approved_no) as revised_no from approval_history c where a.id=c.mst_id $approved_user_cond) as revised_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0  and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond $buyer_id_cond2 $date_cond $buyerIds_cond $booking_no_cond $booking_year_cond $brand_cond_3 $season_cond_3 $season_year_cond_3 group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,a.entry_form order by $orderBy_cond(a.update_date, a.insert_date) desc";
				}
				//echo $sql;
			}
			else
			{		
				if($booking_id!="")
				{   // and a.id in($booking_id)
					$approved_user_cond=" and c.approved_by='$user_id'";
					$sql="select * from(select a.id, a.entry_form,a.update_date, a.insert_date, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.is_apply_last_update,(select max(c.approved_no) as revised_no from approval_history c where a.id=c.mst_id $approved_user_cond) as revised_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0  and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond  $date_cond $buyerIds_cond $buyer_id_cond2 $booking_no_cond 	$brand_cond_3 $brand_id_cond2 $season_cond_3 $season_year_cond_3 group by a.id, a.booking_no,a.entry_form, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num
						union all
						select a.id, a.entry_form,a.update_date, a.insert_date, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.is_apply_last_update,(select max(c.approved_no) as revised_no from approval_history c where a.id=c.mst_id $approved_user_cond) as revised_no from wo_booking_mst a, wo_booking_dtls b  where a.booking_no=b.booking_no and a.company_id=$company_name and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1  $approval_cond and b.fin_fab_qnty>0 $booking_id_cond $buyer_id_cond $buyer_id_cond2 $date_cond $booking_no_cond 	$brand_cond_3 $brand_id_cond2 $season_cond_3 $season_year_cond_3 group by a.id, a.booking_no,a.entry_form, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num) order by $orderBy_cond(update_date, insert_date) desc";
                }
				else
				{
					
				$buyer_ids=$buyer_ids_array[$user_id]['u'];
				if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
				$brand_ids=$brand_ids_array[$user_id]['u'];
				if($brand_ids=="") $brand_id_cond2=""; else $brand_id_cond2=" and a.brand_id in($brand_ids)";
					
					
					
					$approved_user_cond=" and c.approved_by='$user_id'";
					$sql="select a.id, a.entry_form,$year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.is_apply_last_update,(select max(c.approved_no) as revised_no from approval_history c where a.id=c.mst_id $approved_user_cond) as revised_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0  and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond $buyer_id_cond2  $brand_id_cond2  $date_cond $buyerIds_cond $booking_no_cond $booking_year_cond $brand_cond_3 $season_cond_3 $season_year_cond_3 $brandIds_cond group by a.id, a.booking_no,a.entry_form, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num order by $orderBy_cond(a.update_date, a.insert_date) desc";
                   // echo $sql;die;
                }
			}
			 //echo $sql;die;
		}
		else
		{ 
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";

			$user_sequence_no = $user_sequence_no-1;
			//echo $sequence_no;
			/*	if($sequence_no==$user_sequence_no)
			{
				$sequence_no_by_pass='';
			}
			else
			{*/
				if($db_type==0)
				{
					$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");
				}
				else
				{
					$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");
				}

				if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
				else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
				$approved_user_cond=" and c.approved_by='$user_id'";

				$sql="select a.id, a.entry_form,$year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date,a.is_approved, b.id as approval_id, b.sequence_no, b.approved_by, a.is_apply_last_update,(select max(c.approved_no) as revised_no from approval_history c where a.id=c.mst_id $approved_user_cond) as revised_no from wo_booking_mst a, approval_history b where a.id=b.mst_id  and b.entry_form=7 and a.company_id=$company_name and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.status_active=1  and a.is_deleted=0 and b.current_approval_status=1 and a.ready_to_approved=1 and a.is_approved in (1,3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond   $date_cond $booking_no_cond $booking_year_cond 	$brand_cond_3 $season_cond_3 $season_year_cond_3 order by $orderBy_cond(a.update_date, a.insert_date) desc";
				//echo $sql;
			//}
		}
	
	}
	else
	{
		$buyer_ids=$buyer_ids_array[$user_id]['u'];
		if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
		$sequence_no_cond=" and b.approved_by='$user_id'";
		
		$brand_ids=$brand_ids_array[$user_id]['u'];
		if($brand_ids=="") $brand_id_cond2=""; else $brand_id_cond2=" and a.brand_id in($brand_ids)";

		$sql="select a.id, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, b.id as approval_id, b.sequence_no, b.approved_by, a.is_apply_last_update,a.entry_form,max(b.approved_no) as revised_no,c.grouping from wo_booking_mst a, approval_history b, wo_po_break_down c,wo_booking_dtls d where a.id=b.mst_id and a.booking_no=d.booking_no and d.job_no=c.job_no_mst   and b.entry_form=7 and a.company_id=$company_name and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.current_approval_status=1 and a.ready_to_approved=1 and a.is_approved  in (1,3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $internal_ref_cond $file_no_cond $date_cond $booking_no_cond $booking_year_cond $brand_cond_3 $season_cond_3 $season_year_cond_3 $brand_id_cond2
		group by a.id, a.booking_no_prefix_num, a.booking_no,a.entry_form, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, b.id, b.sequence_no, b.approved_by, a.is_apply_last_update,a.update_date, a.insert_date,c.grouping
		 order by $orderBy_cond(a.update_date, a.insert_date) desc";
    }
	 //echo $sql;die;
	
	$nameArray=sql_select($sql); $bookidstr="";
	foreach ($nameArray as $row)
	{
		if($bookidstr=="") $bookidstr=$row[csf('id')]; else $bookidstr.=','.$row[csf('id')];

	}
	
	$booknoId=implode(",",array_filter(array_unique(explode(",",$bookidstr))));
	$book_ids=count(explode(",",$bookidstr)); $bookingidCond="";
	if($db_type==2 && $book_ids>1000)
	{
		$bookingidCond=" and (";
		$bookingnoIdArr=array_chunk(explode(",",$booknoId),999);
		foreach($bookingnoIdArr as $ids)
		{
			$ids=implode(",",$ids);
			$bookingidCond.=" a.id in($ids) or"; 
		}
		$bookingidCond=chop($bookingidCond,'or ');
		$bookingidCond.=")";
	}
	else $bookingidCond=" and a.id in($booknoId)"; 


	if($cbo_brand_id>0){	$brand_cond="and d.brand_id='$cbo_brand_id'";	}else{	$brand_cond="";	}
	if($cbo_season_id>0){	 $season_cond="and d.season_buyer_wise='$cbo_season_id'";	}else{	$season_cond="";		}
	if($cbo_season_year>0){	 $season_year_cond="and d.season_year='$cbo_season_year'";}else{$season_year_cond="";			}
	
	if($cbo_style_owner_id){$whereCon = " and d.STYLE_OWNER=$cbo_style_owner_id";}	
	
	$job_sql="select d.id as job_id,a.pay_mode, a.booking_no, a.booking_no_prefix_num, b.po_break_down_id, b.job_no, c.grouping, c.file_no, d.dealing_marchant, d.quotation_id,d.brand_id,
	d.season_buyer_wise,d.season_year,d.style_ref_no, a.remarks,d.product_dept,d.pro_sub_dep,a.inserted_by,c.is_confirmed from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d where a.booking_no=b.booking_no and b.job_no=c.job_no_mst and b.po_break_down_id =c.id and c.job_no_mst=d.job_no and b.job_no=d.job_no and a.company_id=$company_name $whereCon and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 $brand_cond 	$season_cond $season_year_cond and  b.is_deleted=0 and a.ready_to_approved=1 and b.fin_fab_qnty>0  $booking_no_cond $date_cond $internal_ref_cond $booking_year_cond $bookingidCond";
	//echo $job_sql;
	// remove it for taking  too much time 
	// group by a.pay_mode, a.booking_no, b.po_break_down_id, b.job_no, c.grouping, c.file_no, d.dealing_marchant, d.quotation_id,d.brand_id,d.season_buyer_wise,d.season_year, a.booking_no_prefix_num,d.style_ref_no, a.remarks,d.product_dept,d.pro_sub_dep,a.inserted_by

	$sql_job=sql_select($job_sql);

	$job_information_arr=array();
	$job_sise_data=array();
	$po_arr=array();
	$sub_department_arr = return_library_array("select id,sub_department_name from lib_pro_sub_deparatment","id","sub_department_name");
	$lib_user = return_library_array("select id,user_name from user_passwd","id","user_name");
	foreach( $sql_job as $jval)
	{
		/*$jobDataArr[$jval[csf('job_no')]]['jobno'][]=$jval[csf('job_no')];
		$jobDataArr[$jval[csf('job_no')]]['deal_march'][]=$jval[csf('dealing_marchant')].'=='.$jval[csf('job_no')];
		$jobDataArr[$jval[csf('job_no')]]['po_break_down_id'][]=$jval[csf('po_break_down_id')];
		$jobDataArr[$jval[csf('job_no')]]['grouping'][]=$jval[csf('grouping')];
		$jobDataArr[$jval[csf('job_no')]]['file_no'][]=$jval[csf('file_no')];*/
		//echo $jval[csf('job_id')];
		//$job_information_arr[$jval[csf('booking_no')]]['jobno'][]=$jval[csf('job_no')];
		$po_wise_po_status_arr[$jval[csf('po_break_down_id')]]=$jval[csf('is_confirmed')];
		
		$job_information_arr[$jval[csf('booking_no')]]['quot_no'][]=$jval[csf('quotation_id')];
		$job_information_arr[$jval[csf('booking_no')]]['deal_march'][]=$jval[csf('dealing_marchant')].'=='.$jval[csf('job_no')].'=='.$jval[csf('is_confirmed')];
		$job_information_arr[$jval[csf('booking_no')]]['po_break_down_id'][]=$jval[csf('po_break_down_id')];
		$job_information_arr[$jval[csf('booking_no')]]['grouping'][]=$jval[csf('grouping')];
		$job_information_arr[$jval[csf('booking_no')]]['file_no'][]=$jval[csf('file_no')];
		$job_information_arr[$jval[csf('booking_no')]]['remarks'][]=$jval[csf('remarks')];
		$job_information_arr[$jval[csf('booking_no')]]['product_dept'][]=$product_dept[$jval[csf('product_dept')]];
		$job_information_arr[$jval[csf('booking_no')]]['pro_sub_dep'][]=$sub_department_arr[$jval[csf('pro_sub_dep')]];
		$job_information_arr[$jval[csf('booking_no')]]['brand'][]=$brand[$jval[csf('brand_id')]];
		$job_paymode_arr[$jval[csf('booking_no')]]=$jval[csf('pay_mode')];

			 $job_sise_data[$jval[csf('booking_no')]]['inserted_by'] =$lib_user[$jval[csf('inserted_by')]];
			 $job_sise_data[$jval[csf('booking_no')]]['brand'] =$brand[$jval[csf('brand_id')]];
			 $job_sise_data[$jval[csf('booking_no')]]['season'] =$season[$jval[csf('season_buyer_wise')]];
			 $job_sise_data[$jval[csf('booking_no')]]['style_ref'] .=$jval[csf('style_ref_no')].',';
			 $job_sise_data[$jval[csf('booking_no')]]['job_id'] .=$jval[csf('job_id')].',';
			 $job_sise_data[$jval[csf('booking_no')]]['season_year'] =$jval[csf('season_year')];
			 $season_data_arr[$jval[csf('booking_no_prefix_num')]]['season'] =$season[$jval[csf('season_buyer_wise')]];
		array_push($po_arr, $jval[csf('po_break_down_id')]);
	}
	$po_cond=where_con_using_array($po_arr,0,"a.id");
	$po_res =sql_select("SELECT b.booking_no, max(a.shipment_date) as shipment_end,min(a.shipment_date) as shipment_start from   wo_po_break_down a, wo_booking_dtls b where a.id=b.po_break_down_id and a.is_deleted=0 and b.is_deleted=0  $po_cond group by b.booking_no ");

	$shipment_date_arr=array();
	foreach ($po_res as $row) {
		$shipment_date_arr[$row[csf('booking_no')]]['shipment_end']=$row[csf('shipment_end')];
		$shipment_date_arr[$row[csf('booking_no')]]['shipment_start']=$row[csf('shipment_start')];
	}
	$bookingidCond=str_replace("a.id", "mst_id", $bookingidCond);
	$refusing_res =sql_select("SELECT mst_id,refusing_reason from   refusing_cause_history where entry_form=7 $bookingidCond  order by id desc ");
	$refusing_reason_arr=array();
	foreach ($refusing_res as $row) {
		$refusing_reason_arr[$row[csf('mst_id')]]=$row[csf('refusing_reason')];
	}

	$booking_id=str_replace("mst_id", "booking_id", $bookingidCond);
	$sql_req="select approval_cause,approval_no,booking_id from fabric_booking_approval_cause where entry_form=7  and approval_type=2 and status_active=1 and is_deleted=0 $booking_id order by approval_no ";
	//echo $sql_req;
	$nameArray_req=sql_select($sql_req);
	$unappv_req_arr=array();
	foreach($nameArray_req as $row)
	{
		$unappv_req_arr[$row[csf('booking_id')]]=$row[csf('approval_cause')];
	}

	$bookingidCond=str_replace("mst_id", "a.id", $bookingidCond);
	$sql_app=sql_select("select  a.booking_no, max(to_date(to_char(b.APPROVED_DATE,'DD-MON-YYYY'))) as last_approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and b.entry_form=7 and a.status_active=1 and a.is_deleted=0   $bookingidCond group by a.booking_no ");
	$last_approved_date_arr=array();
	foreach ($sql_app as $row) {
		$last_approved_date_arr[$row[csf('booking_no')]]=$row[csf('last_approved_date')];
	}
 
	?>
    <script>
		function openmypage_app_cause(wo_id,app_type,i)
		{
			var txt_alter_user_id = $("#txt_alter_user_id").val();
			var txt_appv_cause = $("#txt_appv_cause_"+i).val();
			var approval_id = $("#approval_id_"+i).val();
			var data=wo_id+"_"+app_type+"_"+txt_appv_cause+"_"+approval_id;
			var title = 'Approval Cause Info';
			var page_link = 'requires/fabric_booking_approval_controller.php?data='+data+'&action=appcause_popup&txt_alter_user_id='+txt_alter_user_id;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
				$('#txt_appv_cause_'+i).val(appv_cause.value);
			}
		}
		function openmypage_app_remark(wo_id,app_type,i)
		{
			var txt_remark = $("#txt_remark_"+i).val();
			var approval_id = $("#approval_id_"+i).val();
			var data=txt_remark;
			var title = 'Remark';
			var page_link = 'requires/fabric_booking_approval_controller.php?data='+data+'&action=remark_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
				$('#txt_remark_'+i).val(appv_cause.value);
			}
		}
		
		function openmypage_app_instrac(wo_id,app_type,i)
		{
			var txt_appv_instra = $("#txt_appv_instra_"+i).val();
			var approval_id = $("#approval_id_"+i).val();
			var data=wo_id+"_"+app_type+"_"+txt_appv_instra+"_"+approval_id;
			var title = 'Approval Instruction';
			var page_link = 'requires/fabric_booking_approval_controller.php?data='+data+'&action=appinstra_popup';
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
			var page_link = 'requires/fabric_booking_approval_controller.php?data='+data+'&action=unappcause_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=180px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var unappv_request=this.contentDoc.getElementById("hidden_unappv_request");
				$('#txt_unappv_req_'+i).val(unappv_request.value);
			}
		}
	</script>
    <?
		$print_report_format_ids_partial_wvn = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=138 and is_deleted=0 and status_active=1");
		$format_ids_partial_wvn=explode(",",$print_report_format_ids_partial_wvn);
		
		
		$print_report_format_ids_partial = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=35 and is_deleted=0 and status_active=1");
		$format_ids_partial=explode(",",$print_report_format_ids_partial);
		//print_r($format_ids_partial);
		$print_report_format_ids_short = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=2 and is_deleted=0 and status_active=1");
		$print_report_ids_short=explode(",",$print_report_format_ids_short);

		$print_report_format_ids2 = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
		$format_ids=explode(",",$print_report_format_ids2);
		
		$print_report_format_ids_3 = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=43 and is_deleted=0 and status_active=1");
		$format_ids_3=explode(",",$print_report_format_ids_3);
	
		$cost_control_source=return_field_value("cost_control_source","variable_order_tracking","company_name=".$cbo_company_name." and variable_list in (53) and status_active=1 and is_deleted=0","cost_control_source");
		
		if($approval_type==0)
		{
			$fset=2262; $table1=2252; $table2=2232;
		}
		else if($approval_type==1)
		{
			$fset=2362; $table1=2352; $table2=2332;
		}
		$qcCostSheetNo = return_library_array("select qc_no, cost_sheet_no from qc_mst","qc_no","cost_sheet_no");
		
	 
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:<?=$fset; ?>px; margin-top:10px">
        <legend>Fabric Booking Approval</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?=$table1; ?>" class="rpt_table" >
                <thead>
                	<th width="30">&nbsp;</th>
                    <th width="30">SL</th>
                    <th width="70">Insert By/ Merchandiser</th>
                   	<th width="50">Image</th>
                   	<th width="100">Booking Date</th>
                   	<th width="70">Approved Date</th>
                    <th width="60">Mkt Cost</th>
                    <th width="50">Year</th>
                    <th width="100">Job No</th>
                    <th width="100">Style Ref.</th>
					<th width="100">Status</th>
                    <th width="125">Buyer</th>
                    <th width="70">Booking No</th>
                    <th width="70">Last Version</th>
                    <th width="70">Fabric Source</th>
                    <th width="80">Type</th>
                    <th width="160">Supplier</th>
                    <th width="100">Product Dept</th>
                    <th width="100">Sub Dept</th>
                    <th width="80">Brand</th>
                    <th width="70">Ship Start</th>
                    <th width="70">Ship End</th>
                    <th width="90">Delivery Date</th>
                    <?
					if($approval_type==1) echo "<th width='100'>Un-appv request</th>";
					?>
                    <th width="120">Refusing Cause</th>
                    <th>Remarks</th>
                </thead>
            </table>
            <div style="width:<?=$table1; ?>px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?=$table2; ?>" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
					//	 echo $sql;
					$imge_arr=return_library_array( "select master_tble_id, image_location from  common_photo_library where file_type=1",'master_tble_id','image_location');
                            $i=1; $all_approval_id='';
                            $nameArray=sql_select( $sql );
                            $booking_mst_id=array();

                            foreach ($nameArray as $row)
                            {
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$value=$row[csf('id')];
								if($row[csf('booking_type')]==4)
								{
									$booking_type="Sample";
									$type=3;
								}
								else
								{
									if($row[csf('is_short')]==1) $booking_type="Short";
                                    elseif($row[csf('is_short')]==3) $booking_type="Dia Wise";
                                    else { $booking_type="Main"; $type=$row[csf('is_short')]; }
								}

								//=========== for job file internal reff===========
								$dealing_merchant='';
								$dealing_merchant_arr=array();
								$job_no_arr=array();
								$all_job_no=''; $jobNo="";
								foreach( $job_information_arr[$row[csf('booking_no')]]['deal_march'] as $key=>$dl_data )
								{
									$exda="";
									$exda=explode("==",$dl_data);
									$job_no_arr[]=$exda[1];
									//$order_status_arr[]=$order_status[$exda[2]];
									$dealing_merchant_arr[$exda[0]]=$dealing_merchant_array[$exda[0]];
									$jobNo=$exda[1];
								}

								if($cbo_style_owner_id && count($job_no_arr) ==0){continue;}

								$job_no_arr=array_unique($job_no_arr);
								$all_job_no=implode(",",$job_no_arr);
								
								//$all_order_status=implode(",",array_unique($order_status_arr));
						
								$dealing_merchant_arr=array_unique($dealing_merchant_arr);
								$dealing_merchant=implode(",",$dealing_merchant_arr);
								
								$qc_arr=array();
								$all_qc_no='';
								
								foreach( $job_information_arr[$row[csf('booking_no')]]['quot_no'] as $key=>$qcdata )
								{
									$qc_arr[]=$qcdata;
								}
								//print_r($qc_arr);
								$all_qc_no=implode(",",array_unique($qc_arr));
								

								// file no information..........................................
								$file_no_arr=array();
								$all_file_no='';
								foreach( $job_information_arr[$row[csf('booking_no')]]['file_no'] as $key=>$file_data )
								{
									$file_no_arr[]=$file_data;
								}
								$file_no_arr=array_unique($file_no_arr);
								$all_file_no=implode(",",$file_no_arr);
								// internal reference information.....................................
								$all_internal_ref='';
								$internal_ref_arr=array();
								foreach( $job_information_arr[$row[csf('booking_no')]]['grouping'] as $key=>$internalref_data )
								{
									$internal_ref_arr[]=$internalref_data;
								}
								$internal_ref_arr=array_unique($internal_ref_arr);
								$all_internal_ref=implode(",",$internal_ref_arr);
								$booking_remark='';
								$remark_arr=array();
								foreach( $job_information_arr[$row[csf('booking_no')]]['remarks'] as $key=>$remarks_data )
								{
									$remark_arr[]=$remarks_data;
								}
								$remark_arr=array_unique($remark_arr);
								$booking_remark=implode(",",$remark_arr);

								$product_dept='';
								$product_dept_arr=array();
								foreach( $job_information_arr[$row[csf('booking_no')]]['product_dept'] as $key=>$product_dept_data )
								{
									$product_dept_arr[]=$product_dept_data;
								}
								$product_dept_arr=array_unique($product_dept_arr);
								$product_dept=implode(",",$product_dept_arr);

								$pro_sub_dep='';
								$pro_sub_dep_arr=array();
								foreach( $job_information_arr[$row[csf('booking_no')]]['pro_sub_dep'] as $key=>$pro_sub_dep_data )
								{
									$pro_sub_dep_arr[]=$pro_sub_dep_data;
								}
								$pro_sub_dep_arr=array_unique($pro_sub_dep_arr);
								$pro_sub_dep=implode(",",$pro_sub_dep_arr);

								$brand='';
								$brand_arr=array();
								foreach( $job_information_arr[$row[csf('booking_no')]]['brand'] as $key=>$brand_data )
								{
									$brand_arr[]=$brand_data;
								}
								$brand_arr=array_unique($brand_arr);
								$brand=implode(",",$brand_arr);

								// order no information....................................
								$all_po_id='';
								$po_id_arr=array();
								foreach( $job_information_arr[$row[csf('booking_no')]]['po_break_down_id'] as $key=>$po_data )
								{
									$po_id_arr[]=$po_data;
								}
								$po_id_arr=array_unique($po_id_arr);
								$all_po_id=implode(",",$po_id_arr);
								$order_status_arr=array();
								foreach($po_id_arr as $pi){
									$order_status_arr[$po_wise_po_status_arr[$pi]]=$order_status[$po_wise_po_status_arr[$pi]];
								}
								$all_order_status=implode(",",$order_status_arr);
			
								
								
								if($row[csf('approval_id')]==0) $print_cond=1;
								else
								{
									if($duplicate_array[$row[csf('entry_form')]][$row[csf('id')]][$row[csf('sequence_no')]][$row[csf('approved_by')]]=="")
									{
										$duplicate_array[$row[csf('entry_form')]][$row[csf('id')]][$row[csf('sequence_no')]][$row[csf('approved_by')]]=$row[csf('id')];
										$print_cond=1;
									}
									else
									{
										if($all_approval_id=="") $all_approval_id=$row[csf('approval_id')]; else $all_approval_id.=",".$row[csf('approval_id')];
										$print_cond=0;
									}
								}
								
								
								
								//print_r($format_ids_partial_wvn);
								if($print_cond==1)
								{
									//$fabric_nature=$_SESSION['fabric_nature'];
									$fabric_nature=$row[csf('item_category')];
									if($row[csf('booking_type')]==1 && $row[csf('entry_form')]==108) $row_id=$format_ids_partial[0];
									else if($row[csf('booking_type')]==1 && $row[csf('entry_form')]==271) $row_id=$format_ids_partial_wvn[0];
									else if($row[csf('booking_type')]==1 && $row[csf('entry_form')]==118) $row_id=$format_ids[0];
									else if($row[csf('booking_type')]==1 && $row[csf('entry_form')]==86) $row_id=$format_ids[0];
									else $row_id=$print_report_ids_short[0];

								//	echo $row[csf('entry_form')].'='.$row_id.'DDD';
								
									$variable='';$print_button="";
									 
								
									if($row[csf('entry_form')]==86) //Budget wise fab booking
									{
										
										if($row_id==73)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_b6','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}else if($row_id==274)
										{
										
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_10','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($row_id==53)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_jk','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($row_id==45)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_urmi','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($row_id==6)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report4','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($row_id==7)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report5','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($row_id==5)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report2','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($row_id==3)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report3','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($row_id==1)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_gr','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
									}

									else if($row[csf('entry_form')]==108)
									 {

									
										if($row_id==218){
										$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_7','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_17','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
									 }						
									else
									{
									   if($format_ids[0]==719)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report16','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}else if($format_ids[0]==274)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_10','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($format_ids[0]==1)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_gr','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]." <a/>";
										}
										else if($format_ids[0]==2)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report','".$i."',".$fabric_nature.")\">  ".$row[csf('booking_no_prefix_num')]. "<a/>";
										}
										else if($format_ids[0]==3)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report3','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]." <a/>";
										}
										else if($format_ids[0]==4)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report1','".$i."',".$fabric_nature.")\">" .$row[csf('booking_no_prefix_num')]. "<a/>";
										}
										else if($format_ids[0]==5)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report2','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]." <a/>";
										}
										else if($format_ids[0]==6)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report4','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]." <a/>";
										}
										else if($format_ids[0]==7)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report5','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]." <a/>";
										}
										else if($format_ids[0]==28)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_akh','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]." <a/>";
										}
										else if($format_ids[0]==39)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_39','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]." <a/>";
										}
										else if($format_ids[0]==45)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_urmi','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]." <a/>";
										}
										else  if($format_ids[0]==53)
										{
											$variable="<a href='#'  onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_jk','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]." <a/>";
										}
										else if($format_ids[0]==93)
										{
											$variable="<a href='#'  onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_libas','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]." <a/>";
										}
										else if($format_ids[0]==73)
										{
											$variable="<a href='#'  onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_mf','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]." <a/>";
										}
										else if($format_ids[0]==85)
										{
											$variable="<a href='#'  onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_3','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($format_ids[0]==143)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_urmi','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($format_ids[0]==220)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_northern_new','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($format_ids[0]==160)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_5','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($format_ids[0]==269)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_knit','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($format_ids[0]==84)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_islam','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($format_ids[0]==129)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_libas','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($format_ids[0]==193)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_print4','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($format_ids[0]==280)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_print14','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($format_ids[0]==274)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_10','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($format_ids[0]==304)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report10','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										else if($format_ids[0]==723)
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report17','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}

										if($variable=="") $variable="".$row[csf('booking_no_prefix_num')]."";
										
										if($format_ids[0]==220)
										{
											$print_button="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_northern_new','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]."<a/>";
										}else if($format_ids[0]==719)
										{
											$print_button="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report16','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]."<a/>";
										}
										else
										{
											$print_button="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_urmi','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
										}
										 if($row_id==155)//woven partial booking
										{
											$variable="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','fabric_booking_report','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";
										}
										if($format_ids[0]==274)
										{
											$print_button="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','print_booking_10','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]."<a/>";

										}else if($row_id==155){ //woven partial booking
											$print_button="<a href='#'  onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','fabric_booking_report','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]."<a/>";
										}
										else
										{
											$print_button="<a href='#' onClick=\"generate_worder_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."','".$row_id."','".$row[csf('entry_form')]."','show_fabric_booking_report_urmi','".$i."',".$fabric_nature.")\"> ".$fabric_source[$row[csf('fabric_source')]]." <a/>";
										}
									}
									
									$variable1=''; 
									$print_button=$fabric_source[$row[csf('fabric_source')]];
									if($row[csf('revised_no')]>0)
									{
										for($q=1; $q<=$row[csf('revised_no')]; $q++)
										{
											if($variable1=="")
												$variable1="<a href='#' onClick=\"generate_fabric_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$fabric_nature."','".$row[csf('fabric_source')]."','".$q."','".$approval_type."','".$all_job_no."',".$row[csf('entry_form')].")\"> ".$q."<a/>";
											else
												$variable1.=", "."<a href='#' onClick=\"generate_fabric_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$fabric_nature."','".$row[csf('fabric_source')]."','".$q."','".$approval_type."','".$all_job_no."',".$row[csf('entry_form')].")\"> ".$q."<a/>";
										}
									}
									
									$job_id=rtrim($job_sise_data[$row[csf('booking_no')]]['job_id'],',');
									$job_ids=implode(",",array_unique(explode(",",$job_id)));
									// echo $job_ids.',';
									?>
									<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
										<td width="30" align="center" valign="middle" title="<?=$row[csf('entry_form')]?>">
											<input type="checkbox" id="tbl_<?=$i;?>" name="tbl[]" onClick="check_last_update(<?=$i;?>);" />
											<input id="booking_id_<?=$i;?>" name="booking_id[]" type="hidden" value="<?=$value; ?>" />
											<input id="booking_no_<?=$i;?>" name="booking_no[]" type="hidden" value="<?=$row[csf('booking_no')]; ?>" />
											<input id="approval_id_<?=$i;?>" name="approval_id[]" type="hidden" value="<?=$row[csf('approval_id')]; ?>" />
                                            <input id="last_update_<?=$i;?>" name="last_update[]" type="hidden" value="<?=$row[csf('is_apply_last_update')]; ?>" />
                                            <input id="<?=strtoupper($row[csf('booking_no')]); ?>" name="no_booook[]" type="hidden" value="<?=$i;?>" />
										</td>
										<td width="30" id="td_<?=$i; ?>" style="cursor:pointer" align="center" onClick="generate_worder_report2(<?=$type; ?>,'<?=$row[csf('booking_no')]; ?>',<?=$row[csf('company_id')]; ?>,'<?=$row[csf('po_break_down_id')]; ?>',<?=$row[csf('item_category')].','.$row[csf('fabric_source')]; ?>,'<?=$row[csf('job_no')]; ?>','<?=$row[csf('is_approved')]; ?>','show_fabric_booking_report3')"><?=$i; ?></td>
										<td width="70"><?=$job_sise_data[$row[csf('booking_no')]]['inserted_by'];?></td>
										<td width="50" onClick="openmypage_image('requires/fabric_booking_approval_controller.php?action=show_image&job_no=<?=$all_job_no; ?>','Image View')"><img src='../<?=$imge_arr[$jobNo]; ?>' height='25' width='30' /></td>	
										<td width="100" align="center" style="word-break:break-all"><? if($row[csf('booking_date')]!="0000-00-00") echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</td>

										<td width="70" align="center" style="word-break:break-all"><? if($last_approved_date_arr[$row[csf('booking_no')]]!="0000-00-00") echo change_date_format($last_approved_date_arr[$row[csf('booking_no')]]); ?>&nbsp;</td>

                                        <td width="60" align="center">
                                        <a href='##' style='color:#000' onClick="generate_mkt_report('<?=$all_job_no; ?>','<?=$row[csf('booking_no')]; ?>','<?=$all_po_id; ?>','<?=$row[csf('item_category')]; ?>','<?=$row[csf('fabric_source')]; ?>','show_fabric_comment_report')">View</a></td>
                                        <td width="50" align="center"><?=$row[csf('year')]; ?></td>
                                        <td width="100" align="center" style="word-break:break-all">
											<?
											foreach($job_no_arr as $jobNo){

												echo "<a href='#' onClick=\"generate_worder_report4('".$jobNo."','')\"> ".$jobNo." <a/>";
												
												// if($format_ids_3[0]==730){
												// 	echo "<a href='#' onClick=\"generate_worder_report4('".$jobNo."','budgetsheet')\"> ".$jobNo." <a/>";
												// }else if($row[csf('entry_form')]==271){
												// 	echo "<a href='#' onClick=\"generate_worder_report3('".$jobNo."','basic_cost')\"> ".$jobNo." <a/>";
												// }else{
												// 	echo "<a href='#' onClick=\"generate_worder_report4('".$jobNo."','preCostRpt4')\"> ".$jobNo." <a/>";
												// }
											}
											
											$style_no=rtrim($job_sise_data[$row[csf('booking_no')]]['style_ref'],',');
											$style_nos=implode(",",array_unique(explode(",",$style_no)));
											?>
											&nbsp;
                                        </td>
                                        <td width="100" align="center" style="word-break:break-all"><a href='##' style='color:#000' onClick="generate_mkt_report('<?=$all_job_no; ?>','<?=$row[csf('booking_no')]; ?>','<?=$all_po_id; ?>','<?=$row[csf('item_category')]; ?>','<?=$job_ids; ?>','show_fabric_approval_report')"><?=$style_nos; ?></a>&nbsp;</td>
										<td width="100" align="center" style="word-break:break-all"><?=$all_order_status; ?>&nbsp;</td>
                                        <td width="125" align="center" style="word-break:break-all"><?=$buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</td>
                                        <td width="70" align="center" style="word-break:break-all">&nbsp;&nbsp;<?=$variable; ?></td>
                                        <td width="70" align="center" style="word-break:break-all">&nbsp;&nbsp;<?=$variable1; ?></td>
                                        <td width="70" align="center" style="word-break:break-all"><?=$print_button; ?></td>
										<td width="80" align="center" style="word-break:break-all"><?=$booking_type; ?></td>
										<td width="160" align="center" style="word-break:break-all">
											<?
												if($job_paymode_arr[$row[csf('booking_no')]]==3 || $job_paymode_arr[$row[csf('booking_no')]]==5)
												{
													echo $company_fullname_arr[$row[csf('supplier_id')]];
												}
												else
												{
													echo $supplier_arr[$row[csf('supplier_id')]];
												}
												//echo $row[csf('supplier_id')];
											?>&nbsp;
										</td>
										<td width="100"><?=$product_dept; ?>&nbsp;</td>
										<td width="100"><?=$pro_sub_dep; ?>&nbsp;</td>
										<td width="80" align="center" style="word-break:break-all"><?=$brand; ?>&nbsp;</td>
										<td width="70" align="center" style="word-break:break-all">
											<? if($shipment_date_arr[$row[csf('booking_no')]]['shipment_start']!="0000-00-00") echo change_date_format($shipment_date_arr[$row[csf('booking_no')]]['shipment_start']); ?>&nbsp;
										</td>
										<td width="70" align="center" style="word-break:break-all">
											<? if($shipment_date_arr[$row[csf('booking_no')]]['shipment_end']!="0000-00-00") echo change_date_format($shipment_date_arr[$row[csf('booking_no')]]['shipment_end']); ?>&nbsp;
										</td>
										<td align="center" width="90" style="word-break:break-all"><? if($row[csf('delivery_date')]!="0000-00-00") echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</td>
                                        <?
											$unp_cause=$unappv_req_arr[$row[csf('id')]];
											if($approval_type==1)echo "<td align='center' width='100'>
                                        		<input name='txt_unappv_req[]' value='".$unp_cause."' class='text_boxes' readonly placeholder='Please Browse' ID='txt_unappv_req_".$i."' style='width:70px' maxlength='50' onClick='openmypage_unapp_request(".$value.",".$approval_type.",".$i.")'></td>";
											$refusing_reason=$refusing_reason_arr[$row[csf('id')]];
                                        ?>
                                        <td align="center" style="word-break:break-all" width="120">
                                        	<input name="txtCause[]" class="text_boxes" readonly placeholder="Please Browse" ID="txtCause_<?=$i;?>" style="width:80px" maxlength="50" title="Maximum 50 Character" onClick="openmypage_refusing_cause('requires/fabric_booking_approval_controller.php?action=refusing_cause_popup','Refusing Cause','<? echo $row[csf('id')];?>');"  value="<?  echo $refusing_reason;?>" >&nbsp;</td>
                                        <td><input name="txt_remark[]" class="text_boxes" readonly placeholder="Please Browse" ID="txt_remark_<?=$i;?>" style="width:80px" maxlength="50" title="Maximum 50 Character" onClick="openmypage_app_remark(<?=$value; ?>,<?=$approval_type; ?>,<?=$i;?>)" value="<?=$booking_remark?>"></td>
									</tr>
									<?
									$i++;
								}

								if($all_approval_id!="")
								{
									$con = connect();
									$rID=sql_multirow_update("approval_history","current_approval_status",0,"id",$all_approval_id,1);
									disconnect($con);
								}
							}
							$denyBtn="";
							if($approval_type==0) $denyBtn=""; else $denyBtn=" display:none";
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="<?=$table1; ?>" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" >
                    	<input type="checkbox" id="all_check" onClick="check_all('all_check')" />
                        <input type="hidden" name="hide_approval_type" id="hide_approval_type" value="<?=$approval_type; ?>">
                        <font style="display:none"><?=$all_approval_id; ?></font>
                    </td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<?=$i; ?>,<?=$approval_type; ?>);"/>&nbsp;&nbsp;&nbsp;<input type="button" value="<? if($approval_type==0) echo "Deny"; ?>" class="formbutton" style="width:100px;<?=$denyBtn; ?>" onClick="submit_approved(<?=$i; ?>,5);"/></td>
				</tfoot>
			</table>
        </fieldset>
    </form>
 <?
	exit();
}
if($action=="show_fabric_approval_report")
{
	//extract($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//echo $job_ids.'f';
	$txt_job_no=$job_no;
	$all_job_noArr=array_unique(explode(',',$all_job_no));
	
	//$txt_costing_date=change_date_format(str_replace("'","",$txt_costing_date),'yyyy-mm-dd','-');
	//if($txt_job_no=="") $job_no=''; else $job_no=" and a.job_no=".$txt_job_no."";
	//if($cbo_company_name=="") $company_name=''; else $company_name=" and a.company_name=".$cbo_company_name."";
	//if($cbo_buyer_name=="") $cbo_buyer_name=''; else $cbo_buyer_name=" and a.buyer_name=".$cbo_buyer_name."";
	//if($txt_style_ref=="") $txt_style_ref=''; else $txt_style_ref=" and a.style_ref_no=".$txt_style_ref."";
	
 	
	//array for display name
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$imge_arr=return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	
	$colorArr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$sizeArr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$supplierArr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
	$trimGroupArr=return_library_array( "select id, item_name from lib_item_group where status_active=1 and is_deleted=0", "id", "item_name");
	$season_nameArr=return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0", "id", "season_name");
	
	//foreach($all_job_noArr as $job_no )
	//{
		
	/*$costingPerid=return_field_value("costing_per", "wo_pre_cost_mst", "job_id in($job_ids)  and status_active=1 and is_deleted=0");
	
	$costingPerQty=12;
	if($costingPerid==1) $costingPerQty=12;
	elseif($costingPerid==2) $costingPerQty=1;	
	elseif($costingPerid==3) $costingPerQty=24;
	elseif($costingPerid==4) $costingPerQty=36;
	elseif($costingPerid==5) $costingPerQty=48;
	else $costingPerQty=12;*/
	//echo $gsm_weight_bottom.'DD';
	$gmtsitem_ratio_array=array();
	$grmnt_items = "";
    $grmts_sql = sql_select("select job_no,gmts_item_id, set_item_ratio from wo_po_details_mas_set_details where job_id in($job_ids) ");
	foreach($grmts_sql as $key=>$val)
	{
		$grmnt_itemsArr[$val[csf('job_no')]] .=$garments_item[$val[csf("gmts_item_id")]].'::'.$val[csf("set_item_ratio")].",";
		$gmtsitem_ratio_array[$val[csf('job_no')]][$val[csf('gmts_item_id')]]=$val[csf('set_item_ratio')];	
	}
	
	
	 $sql="select a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.gmts_item_id, a.order_uom, a.avg_unit_price, b.id, b.po_number, b.pub_shipment_date, c.country_id, c.item_number_id, c.color_number_id, c.size_number_id, c.order_quantity, c.plan_cut_qnty, c.pack_qty,b.details_remarks,a.season_buyer_wise,c.article_number from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.id=b.job_id and b.job_id=c.job_id and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id in($job_ids) $poidCond order by c.size_order ASC";
	

	$data_array=sql_select($sql); $poColorSizeArr=array(); $jobSizeArr=array(); $poItemColorSizeArr=array(); $poCountryItemColorSizeArr=array(); $orderNo=""; $poQtyPcs=0; $packQty=0;
	foreach($data_array as $row)
	{
		$PoNoArr[$row[csf("id")]]=$row[csf("po_number")];
		$jobSizeArr[$row[csf("size_number_id")]]=$row[csf("size_number_id")];
		$poColorSizeArr[$row[csf("id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['poqty']+=$row[csf("order_quantity")];
		$poColorSizeArr[$row[csf("id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['planqty']+=$row[csf("plan_cut_qnty")];
		
		
		
		$poItemColorSizeArr[$row[csf("id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['poqty']+=$row[csf("order_quantity")];
		$poItemColorSizeArr[$row[csf("id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['planqty']+=$row[csf("plan_cut_qnty")];
		$poItemColorSizeArr[$row[csf("id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['article_number']=$row[csf("article_number")];
		$poCountryItemColorSizeArr[$row[csf("id")]][$row[csf("country_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['poqty']+=$row[csf("order_quantity")];
		$poArr[$row[csf("job_no")]].=$row[csf("po_number")].',';
		$JobStyleArr[$row[csf("job_no")]]['style']=$row[csf("style_ref_no")];
		$JobStyleArr[$row[csf("job_no")]]['buyer_name']=$row[csf("buyer_name")];
		$poQtyPcsArr[$row[csf("job_no")]]+=$row[csf("order_quantity")];
		$packQtyArr[$row[csf("job_no")]]+=$row[csf("pack_qty")];
		$company_name=$row[csf("company_name")];

		$po_wise_remarks[$row[csf("id")]]['details_remarks']=$row[csf("details_remarks")];
		$po_wise_ship_date[$row[csf("id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
		$season_buyer_wise_arr[$row[csf("job_no")]]=$season_nameArr[$row[csf("season_buyer_wise")]];
	}
	//$styleref=$data_array[0][csf('style_ref_no')];
	//$buyerid=$data_array[0][csf('buyer_name')];
	//$styleref=$data_array[0][csf('style_ref_no')];
	
	unset($data_array);
	//$countSize=count($jobSizeArr);
	//$colorsizetablewtd=450+($countSize*60);
	if ($zero_value==1) $exclucolor="#FFFF00"; else $exclucolor="";
	
	
			$sqlContrast="Select pre_cost_fabric_cost_dtls_id, gmts_color_id, contrast_color_id from wo_pre_cos_fab_co_color_dtls where  job_id in($job_ids)  and status_active=1 and is_deleted=0";
			$sqlContrastRes=sql_select($sqlContrast); $contrastColorArr=array();
			foreach($sqlContrastRes as $crow)
			{
				$contrastColorArr[$crow[csf('pre_cost_fabric_cost_dtls_id')]][$crow[csf('gmts_color_id')]]=$crow[csf('contrast_color_id')];
			}
			unset($sqlContrastRes);
			 $sqlfab="select b.id as avg_dtls_id,a.id, a.job_no, a.item_number_id,a.costing_per, a.body_part_id,a.color_type_id, a.lib_yarn_count_deter_id, a.fabric_description, a.gsm_weight, a.nominated_supp_multi, a.budget_on, a.color_size_sensitive, a.uom, b.po_break_down_id, b.color_number_id, b.gmts_sizes, b.cons, b.cons_pcs, b.remarks, b.requirment,a.fabric_source,b.dia_width,b.gmts_sizes from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b where a.id=b.pre_cost_fabric_cost_dtls_id and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and b.cons>0 and b.cons_pcs>0 and a.job_id in($job_ids) $poidFabCond";
			// echo $sqlfab;die;
			$sqlfabRes=sql_select($sqlfab); $fabricGmtsFabricColorArr=array();
			foreach($sqlfabRes as $frow)
			{
				$poQty=$set_item_ratio=$rowReqQtyPcs=$rowReqPlanQtyPcs=$planQty=0;
				$set_item_ratio=$gmtsitem_ratio_array[$frow[csf('job_no')]][$frow[csf('item_number_id')]];
				if($frow[csf("budget_on")]==2)//Plan
					$poQty=$poItemColorSizeArr[$frow[csf("po_break_down_id")]][$frow[csf("item_number_id")]][$frow[csf("color_number_id")]][$frow[csf("gmts_sizes")]]['planqty'];
				else
					$poQty=$poItemColorSizeArr[$frow[csf("po_break_down_id")]][$frow[csf("item_number_id")]][$frow[csf("color_number_id")]][$frow[csf("gmts_sizes")]]['poqty'];
				$planQty=$poItemColorSizeArr[$frow[csf("po_break_down_id")]][$frow[csf("item_number_id")]][$frow[csf("color_number_id")]][$frow[csf("gmts_sizes")]]['planqty'];	
				//$rowReqQtyPcs=($poQty/$set_item_ratio)*($frow[csf("cons")]/$costingPerQty);
				$cons=0;
				//if ($zero_value==1) $cons=$frow[csf("cons_pcs")]/12; else $cons=$frow[csf("requirment")];
				//$rowReqQtyPcs=($poQty/$set_item_ratio)*($cons/$costingPerQty);
				$costingPerid=$frow[csf("costing_per")];
				$article_number=$poItemColorSizeArr[$frow[csf("po_break_down_id")]][$frow[csf("item_number_id")]][$frow[csf("color_number_id")]][$frow[csf("gmts_sizes")]]['article_number'];
				
				$costingPerQty=12;
				if($costingPerid==1) $costingPerQty=12;
				elseif($costingPerid==2) $costingPerQty=1;	
				elseif($costingPerid==3) $costingPerQty=24;
				elseif($costingPerid==4) $costingPerQty=36;
				elseif($costingPerid==5) $costingPerQty=48;
				else $costingPerQty=12;
				
				$cons=$frow[csf("cons_pcs")]/12;
				$rowReqQtyPcs=($poQty)*($cons/$costingPerQty);
				$rowReqPlanQtyPcs=($planQty)*($cons/$costingPerQty);
				$str=""; 
				$str=$frow[csf("id")].'_'.$frow[csf("body_part_id")].'_'.$frow[csf("item_number_id")].'_'.$frow[csf("nominated_supp_multi")].'_'.$frow[csf("gsm_weight")].'_'.$frow[csf("uom")].'_'.$frow[csf("fabric_description")].'_'.$frow[csf("color_type_id")].'_'.$frow[csf("dia_width")].'_'.$frow[csf("gmts_sizes")].'_'.$frow[csf("po_break_down_id")].'_'.$frow[csf("avg_dtls_id")];
				if($frow[csf("color_size_sensitive")]==3)
				{
					$fabriccolor=$contrastColorArr[$frow[csf("id")]][$frow[csf("color_number_id")]];
				}
				else $fabriccolor=$frow[csf("color_number_id")];
				$job_no=$frow[csf('job_no')];
				$fabricGmtsFabricColorArr[$job_no][$str][$frow[csf("color_number_id")]][$fabriccolor]['req']+=$rowReqQtyPcs;
				$fabricGmtsFabricColorArr[$job_no][$str][$frow[csf("color_number_id")]][$fabriccolor]['fin_dzn_cons']=$frow[csf("cons_pcs")];
				$fabricGmtsFabricColorArr[$job_no][$str][$frow[csf("color_number_id")]][$fabriccolor]['reqPlan']+=$rowReqPlanQtyPcs;
				$fabricGmtsFabricColorArr[$job_no][$str][$frow[csf("color_number_id")]][$fabriccolor]['po']+=$poQty;
				$fabricGmtsFabricColorArr[$job_no][$str][$frow[csf("color_number_id")]][$fabriccolor]['article_number']=$article_number;
				$fabricGmtsFabricColorArr[$job_no][$str][$frow[csf("color_number_id")]][$fabriccolor]['remarks']=$frow[csf("remarks")];
				$fabricGmtsFabricColorArr[$job_no][$str][$frow[csf("color_number_id")]][$fabriccolor]['fabric_source']=$frow[csf("fabric_source")];
			}
			unset($sqlfabRes);
			//if ($zero_value==1) $captd="Total Req. Qty"; else $captd="Qty Incl. Allow.";
			if ($zero_value==1) 
			{
				$dispexacc="display:none";
				$acccolspn=2;
			}
			else 
			{
				$dispexacc="";
				$acccolspn=4; 
			}
			$captd="GMT Size";
			
			foreach($fabricGmtsFabricColorArr as $job_no=>$fabricGmtsFabricArr)
			{
				$grmnt_items='';
				$grmnt_itemsJob=$grmnt_itemsArr[$job_no];
				$grmnt_items = rtrim($grmnt_itemsJob,",");
				$poArrAll=rtrim($poArr[$job_no],',');
				$orderNos=implode(",",array_unique(explode(",",$poArrAll)));
				$JobStyle=$JobStyleArr[$job_no]['style'];
				$buyerid=$JobStyleArr[$job_no]['buyer_name'];
				//$orderNo=implode(",",$poArr);
			$img_path="../../";	
		?>
 <div style="width:972px; margin:0 auto">
	 
    <div style="width:972px; margin:0 auto">
        <div style="width:970px; font-size:20px; font-weight:bold" align="center"><b style="float:left"><img src='<?=$img_path.$imge_arr[$company_name]; ?>' height='40px' width='100px' /></b><?=$comp[str_replace("'","",$company_name)]; ?><b style="float:right; font-size:14px; font-weight:bold"><?='&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;';?> </b></div>
        <div style="width:970px; font-size:18px; font-weight:bold" align="center"><b style="float:left"></b>Bill Of Materials [BOM] Report For Style Ref. : <?=$JobStyle.' ['.str_replace("'","",$job_no).'] '; if ($zero_value==1) echo "[Total Req. Qty]"; else echo "[Qty Inclu. Allowance]"; ?></div>
        
        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px" rules="all">
            <tr>
                <td width="100px">Job No:</td>
                <td width="100px"><b><?=$job_no; ?></b></td>
                <td width="100px">Buyer:</td>
                <td width="120px" style="word-break:break-all"><b><?=$buyer_arr[$buyerid]; ?></b></td>
                <td width="100px">Garments Item:</td>
                <td style="word-break:break-all"><b><?=$grmnt_items; ?></b></td>
            </tr>
            <tr>
            	<td width="100px">PO No:</td>
                <td style="word-break:break-all" colspan="5"><b><?=$orderNos; ?></b></td>
            </tr>
            <tr> 
                <td>PO Qty.:</td>
                <td colspan="3"><b><?=fn_number_format($poQtyPcsArr[$job_no],0).'-[PCS]; '.fn_number_format(($poQtyPcsArr[$job_no]/12),2).'-[DZN]; '.fn_number_format($packQtyArr[$job_no],0).'-[Pack];'; ?></b></td>
				<td>Season:</td>
				<td><?=$season_buyer_wise_arr[$job_no];?></td>
            </tr>
        </table>
        <br>
        
        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:1050px" rules="all">
        	<thead>
            	<tr>
                	<th colspan="10"><b>Fabric Details</b></th>
                </tr>
                <tr>
                	<th width="130">Gmts. Color</th>
                    <th width="130">Fabric Color</th>
                    <th width="130">Body Part</th>
                  
                    <th width="80">Color Type</th>
                    <th width="80">Fabric Source</th>
                    <th width="80">PO NO</th>
                    <th width="80">Dia</th>
					<th width="80">Article No</th>
                    <th width="80"><?=$captd; ?></th>
                    <th width="80" bgcolor="<?=$exclucolor; ?>">Fin Cons.[Dzn]</th>
					
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;$fin_dzn_consTotal=0;$str_array=array();
			foreach($fabricGmtsFabricArr as $strval=>$strdata)
			{
				$fabricTotal=0;
				foreach($strdata as $gmtcolor=>$gmtdata)
				{
					foreach($gmtdata as $fabriccolor=>$fabricdata)
					{
						 $exstr=explode("_",$strval);
						 
						 $nomisupp="";
						 if($exstr[3]!="")
						 {
						 	$exsupp=explode("_",$exstr[3]);
						 	foreach($exsupp as $supid)
							{
								if($nomisupp=="") $nomisupp=$supplierArr[$supid]; else $nomisupp.=', '.$supplierArr[$supid];
							}
						 }
						 $color_typeId=$exstr[7];
						 $fab_dia=$exstr[8];
						 $gmt_size=$exstr[9];
						 $po_id=$exstr[10];
						 $avg_dtlsId=$exstr[11];
						  $fab_id=$exstr[0];
						 						 
						 if (!in_array($fab_id,$str_array) )
						 {
							?>
                            <tr bgcolor="#FFFFFF">
                                <td style="word-break:break-all"><?=$garments_item[$exstr[2]]; ?></td>
                                <td style="word-break:break-all" colspan="6"><?=$exstr[6].', '.$exstr[4].' GSM; '.$color_type[$exstr[7]].' UOM: '.$unit_of_measurement[$exstr[5]]; ?></td>
                                <td style="word-break:break-all" colspan="2"><?=$nomisupp; ?></td>
                            </tr>
                            <?
							$str_array[]=$fab_id;            
                        	$i++; 
						 }
						 $poqtyDzn=($fabricdata['po']/12);
						 $reqqtyDzn=($fabricdata['req']/$fabricdata['po'])*12;
						 $fin_dzn_cons=$fabricdata['fin_dzn_cons'];
						// $reqPlanQty=$fabricdata['reqPlan'];
						 //$fabricTotal+=$reqQty;
						 $fin_dzn_consTotal+=$fin_dzn_cons;
						 ?>
                         <tr>
                            <td style="word-break:break-all"><?=$colorArr[$gmtcolor]; ?></td>
                            <td style="word-break:break-all"><?=$colorArr[$fabriccolor]; ?></td>
                            <td style="word-break:break-all"><?=$body_part[$exstr[1]]; ?></td>
                            <td style="word-break:break-all" align="center"><?=$color_type[$color_typeId]; ?></td>
                            <td style="word-break:break-all" title="poNo=<?=$PoNoArr[$po_id]; ?>" align="center"><?=$fabric_source[$fabricdata['fabric_source']]; ?></td>
                            <td style="word-break:break-all" align="center"><?=$PoNoArr[$po_id]; ?></td>
                            <td style="word-break:break-all" align="center"><?=$fab_dia; ?></td>
							<td style="word-break:break-all" align="center"><?=$fabricdata['article_number']; ?></td>
                            <td style="word-break:break-all" align="center"><?=$sizeArr[$gmt_size]; ?></td>
                            <td style="word-break:break-all" align="center" bgcolor="<?=$exclucolor; ?>"><?=fn_number_format($fin_dzn_cons,5); ?></td>
							
                        </tr>
                        <?
						//$i++;
					}
				}
				?>
                <!--<tr bgcolor="#CCCCCC">
                    <td colspan="7" align="right">Sub Total=</td>
                    <td style="word-break:break-all" align="center"><? //fn_number_format($fin_dzn_consTotal,2); ?></td>
					
                </tr>-->
                <?
			}
			//echo $i;
			?>
            </tbody>
        </table>
        <br>
       
     <? //signature_table(237, $cbo_company_name, "970px"); ?>
     </div>
     
	 <?
	} //Job End
	?>
    <br>
         <?

		 $lib_designation=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");

	 $data_array=sql_select("select b.approved_by,b.approved_no, b.approved_date, c.user_full_name,c.designation  from  wo_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.booking_no='$booking_no' and b.entry_form=7 order by b.id asc");
	 
	// echo "select b.approved_by,b.approved_no, b.approved_date, c.user_full_name,c.designation  from  wo_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.booking_no='$booking_no' and b.entry_form=7 order by b.id asc";

 	?>
       <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="4" style="border:1px solid black;">Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th>
                <th width="50%" style="border:1px solid black;">Name/Designation</th>
                <th width="27%" style="border:1px solid black;">Approval Date</th>
                <th width="20%" style="border:1px solid black;">Approval No</th>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($data_array as $row){
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
            <tr style="border:1px solid black;" bgcolor="<? echo $bgcolor;?>">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
                <td width="50%" style="border:1px solid black;"><? echo $row[csf('user_full_name')]." / ". $lib_designation[$row[csf('designation')]];?></td>
                <td width="27%" style="border:1px solid black;"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); //echo change_date_format($row[csf('approved_date')],"dd-mm-yyyy","-");?></td>
                <td width="20%" style="border:1px solid black;"><? echo $row[csf('approved_no')];?></td>
                </tr>
                <?
				$i++;
			}
				?>
            </tbody>
        </table>
    </div>
    <?
	
	 disconnect($con);
	 exit();
}

if($action=="show_image")
{
	echo load_html_head_contents("Image PopUp","../../", 1, 1, $unicode);
    extract($_REQUEST);
	//echo "select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1";
	$jobNos="'".implode(",",explode(',',$job_no))."'";
	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id in ($jobNos) and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
	?>
    <table>
        <tr>
        <?
        foreach ($data_array as $row)
        {
			?>
			<td><img src='../../<? echo $row[csf('image_location')]; ?>' height='250' width='300' /></td>
			<?
        }
        ?>
        </tr>
    </table>
    <?
	exit();
}


if ($action=="check_unapprove_req"){
	
		list($booking_id,$booking_no)=explode('**',$data);
		
		$max_app_no_arr=return_library_array("select MST_ID,max(APPROVED_NO) as MAX_APPROVED_NO from APPROVAL_HISTORY where MST_ID in($booking_id) group by MST_ID","MST_ID","MAX_APPROVED_NO");
		
		$sqlidPre="select b.APPROVAL_NO,B.BOOKING_ID,A.BOOKING_NO FROM WO_BOOKING_MST A,fabric_booking_approval_cause B WHERE A.ID=B.BOOKING_ID AND A.ID IN($booking_id)  and B.entry_form in(7,12)  and B.approval_type=2";
		
		$idPreRes=sql_select($sqlidPre);
		foreach($idPreRes as $idrow)
		{
			if($max_app_no_arr[$idrow[BOOKING_ID]]==$idrow[APPROVAL_NO]){
				$bookingIdArr[$idrow[BOOKING_ID]]=$idrow[BOOKING_NO];
			}
		}
		
		
		$sqlidPre="select a.ID,A.BOOKING_NO FROM WO_BOOKING_MST A WHERE A.BOOKING_NO IN($booking_no)";
		$idPreRes=sql_select($sqlidPre);
		foreach($idPreRes as $idrow)
		{
			if($bookingIdArr[$idrow[ID]]){unset($bookingIdArr[$idrow[ID]]);}
			else{$bookingIdArr[$idrow[ID]]=$idrow[BOOKING_NO];}
		}
		
		$sqlis=sql_select("select BOOKING_NO from inv_receive_master where BOOKING_NO in($booking_no) and status_active=1 and is_deleted=0 and RECEIVE_BASIS=2
		union all
		select BOOKING_NO from INV_ISSUE_MASTER where BOOKING_NO in($booking_no) and status_active=1 and is_deleted=0");
		
		
		$issueIdArr=array();
		foreach($sqlis as $rows){
			$issueIdArr[$rows[BOOKING_NO]]=$rows[BOOKING_NO];
		}
		echo trim(implode(',',$bookingIdArr)).'***'.trim(implode(',',$issueIdArr));

}

function auto_approved($dataArr=array()){
		global $pc_date_time;
		global $user_id;
		$sys_id_arr=explode(',',$dataArr['sys_id']);
		
		$queryText = "select a.id,a.SETUP_DATE,b.APPROVAL_NEED,b.ALLOW_PARTIAL,b.PAGE_ID from APPROVAL_SETUP_MST a,APPROVAL_SETUP_DTLS b where a.id=b.MST_ID and a.COMPANY_ID=$dataArr[company_id] and b.PAGE_ID=$dataArr[app_necessity_page_id] and a.STATUS_ACTIVE =1 and a.IS_DELETED=0  and b.STATUS_ACTIVE =1 and b.IS_DELETED=0 order by a.SETUP_DATE desc";
		$queryTextRes = sql_select($queryText);
		
		if($queryTextRes[0]['ALLOW_PARTIAL']==1){
			$con = connect();
		
			$query="UPDATE $dataArr[mst_table] SET IS_APPROVED=1,approved_by=$dataArr[approval_by],approved_date='$pc_date_time' WHERE id in ($dataArr[sys_id])";
			$rID1=execute_query($query,1);
			//echo $query;die;
			
			if($rID1==1){ oci_commit($con);}
			else{oci_rollback($con);}
			
			
			disconnect($con);
			//return $query;
		}
		//return $ALLOW_PARTIAL;
}
	
if ($action=="approve")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//$user_id=137;
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	

	$bookingSql="select READY_TO_APPROVED from WO_BOOKING_MST WHERE id  in ($booking_ids)";
	$bookingSqlRes=sql_select($bookingSql); 
	foreach($bookingSqlRes as $rows)
	{
		if($rows['READY_TO_APPROVED'] !=1){
			echo "25**Please select ready to approved yes for approved this booking";exit();
		}
		
	}
	unset($bookingSqlRes);




	$brand_arr=return_library_array("select id,brand_name from lib_buyer_brand where IS_DELETED=0 and STATUS_ACTIVE=1","id","brand_name");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where IS_DELETED=0 and STATUS_ACTIVE=1", "id", "buyer_name"  );
	
	$msg=''; $flag=''; $response='';
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($txt_alter_user_id!="") 	$user_id_approval=$txt_alter_user_id;
	else $user_id_approval=$user_id;
	//echo "0**";
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id_approval and is_deleted=0");

	$user_return_name=return_field_value("user_name","user_passwd","id=$user_id");
	if ($user_sequence_no=="")
	{
		echo "500**You Have No Authority To Sign Fabric Booking, User Name: ".$user_return_name;
		disconnect($con);
		die;
	}
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");

	
	//echo "select b.USER_ID,b.BUYER_ID,B.BRAND_ID from electronic_approval_setup b where b.company_id=$cbo_company_name and b.page_id=$menu_id and b.user_id <> $user_id_approval and sequence_no>$user_sequence_no and b.is_deleted=0 group by b.USER_ID,b.BUYER_ID,B.BRAND_ID";die;
	
	
	$electronic_setup_sql = sql_select("select b.USER_ID,b.BUYER_ID,B.BRAND_ID from electronic_approval_setup b where b.company_id=$cbo_company_name and b.page_id=$menu_id and b.user_id <> $user_id_approval and sequence_no>$user_sequence_no and b.is_deleted=0 group by b.USER_ID,b.BUYER_ID,B.BRAND_ID"); 
	foreach ($electronic_setup_sql as $row) {
		if($row[BUYER_ID]==''){$row[BUYER_ID]=implode(',',array_keys($buyer_arr));}
		if($row[BRAND_ID]==''){$row[BRAND_ID]=implode(',',array_keys($brand_arr));}
		
		$otherUserBuyerArr[$row[USER_ID]] = $row[BUYER_ID];
		$otherUserBrandArr[$row[USER_ID]] = $row[BRAND_ID];
	}
	
	$otherUserBuyerArr=array_filter(array_unique(explode(',',implode(',',$otherUserBuyerArr))));
	$otherUserBrandArr=array_filter(array_unique(explode(',',implode(',',$otherUserBrandArr))));
	$otherUserBuyerArr[]=0;
	$otherUserBrandArr[]=0;
	
	//print_r($otherUserBuyerArr);die;
 	
	
	if($approval_type==0)
	{
		$response=$booking_ids;

		$buyer_arr=return_library_array( "select id, buyer_id  from wo_booking_mst where id in ($booking_ids)", "id", "buyer_id"  );
		$brand_arr=return_library_array( "select id, brand_id  from wo_booking_mst where id in ($booking_ids)", "id", "brand_id"  );

		if($db_type==2) {$buyer_id_cond=" and a.buyer_id is null "; $buyer_id_cond2=" and b.buyer_id is not null ";}
		else{$buyer_id_cond=" and a.buyer_id='' "; $buyer_id_cond2=" and b.buyer_id!='' ";}

		$is_not_last_user=return_field_value("a.sequence_no as sequence_no","electronic_approval_setup a"," a.company_id=$cbo_company_name and a.page_id=$menu_id and a.sequence_no>$user_sequence_no and a.bypass=2 and a.is_deleted=0","sequence_no");// $buyer_id_cond
		
		$partial_approval = "";
		if($is_not_last_user == "")
		{
			//$credentialUserBuyersArr = [];
			$sql = sql_select("select (b.buyer_id) as buyer_id,b.brand_id from electronic_approval_setup b where b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2  group by b.buyer_id,b.brand_id");
			foreach ($sql as $key => $buyerID) {
				$credentialUserBuyersArr[] = $buyerID[csf('buyer_id')];
				$credentialUserBrandArr[] = $buyerID[csf('brand_id')];
			}
			//$credentialUserBuyersArr = implode(',',$credentialUserBuyersArr);
			//$credentialUserBuyersArr = explode(',',$credentialUserBuyersArr);
			//$credentialUserBuyersArr = array_unique($credentialUserBuyersArr);
			
			$credentialUserBuyersArr=array_unique(explode(',',implode(',',$credentialUserBuyersArr)));
			//$credentialUserBrandArr=array_unique(explode(',',implode(',',$credentialUserBrandArr)));
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
			//print_r($credentialUserBuyersArr);die;
		}

		$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, user_ip,comments,inserted_by,insert_date";
		$id=return_next_id( "id","approval_history", 1 ) ;
		$appid=return_next_id( "id","approval_mst", 1 ) ;

		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=7 group by mst_id","mst_id","approved_no");
		$approved_status_arr = return_library_array("select id, is_approved from wo_booking_mst where id in($booking_ids)","id","is_approved");

		$approved_no_array=array();
		$booking_ids_all=explode(",",$booking_ids);
		$booking_nos_all=explode(",",$booking_nos);
		$app_instru_all=explode(",",$appv_instras);
		$book_nos='';

		for($i=0; $i<count($booking_nos_all); $i++)
		{
			$val=$booking_nos_all[$i];
			$booking_id=$booking_ids_all[$i];
			$app_instru=$app_instru_all[$i];

			$approved_no=$max_approved_no_arr[$booking_id];
			$approved_status=$approved_status_arr[$booking_id];
			$buyer_id=$buyer_arr[$booking_id];
			$brand_id=$brand_arr[$booking_id];			
			
			if($approved_status==0)
			{
				$approved_no=$approved_no+1;
				$approved_no_array[$val]=$approved_no;
				if($book_nos=="") $book_nos=$val; else $book_nos.=",".$val;
			}
			
			if($is_not_last_user == "")
			{
				/*if(in_array($buyer_id,$credentialUserBuyersArr))
				{
					$partial_approval=3;
				}
				else $partial_approval=1;*/
				$partial_approval=1;
			}
			else
			{
				if( (count($otherUserBuyerArr)>0) && (in_array($buyer_id,$otherUserBuyerArr)) && (count($otherUserBrandArr)==0)){
					$partial_approval=3;
				}
				else if((count($otherUserBuyerArr)>0) && (in_array($buyer_id,$otherUserBuyerArr)) && (count($otherUserBrandArr)>0) && (in_array($brand_id,$otherUserBrandArr))){
					$partial_approval=3;
				}
				else
				{
					$partial_approval=1;
				}
				
				/*				
				if(count($credentialUserBuyersArr)>0)
				{
					if(in_array($buyer_id,$credentialUserBuyersArr))
					{
						$partial_approval=3;
					}
					else $partial_approval=1;
				}
				else $partial_approval=3;
				*/				
			}
			
			    //echo $partial_approval;die;
			 
			$booking_id_arr[]=$booking_id;
			$data_array_booking_update[$booking_id]=explode("*",($partial_approval));
			if(($user_sequence_no*1)==0) { echo "seq**".$user_sequence_no; disconnect($con);die; }
			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",7,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."','".$user_ip."',".$app_instru.",".$user_id.",'".$pc_date_time."')";
			$id=$id+1;

			//app mst data.......................
			if($app_data_array!=''){$app_data_array.=",";}
			$app_data_array.="(".$appid.",7,".$booking_id.",".$user_sequence_no.",".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
			$appid++;

		}
	    //echo "10**".$data_array;die;
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

			$approved_string_mst="CASE booking_no ".$approved_string." END";
			$approved_string_dtls="CASE booking_no ".$approved_string." END";

			$sql_insert="insert into wo_booking_mst_hstry(id, approved_no, booking_id, booking_type, is_short, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, source, booking_date, delivery_date, booking_month, booking_year, supplier_id, attention, booking_percent, colar_excess_percent, cuff_excess_percent, is_approved, is_deleted, status_active, inserted_by, insert_date, updated_by, update_date, ready_to_approved, is_apply_last_update, rmg_process_breakdown,revised_date)
				select
				'', $approved_string_mst, id, booking_type, is_short, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, source, booking_date, delivery_date, booking_month, booking_year, supplier_id, attention, booking_percent, colar_excess_percent, cuff_excess_percent, is_approved, is_deleted, status_active, inserted_by, insert_date, updated_by, update_date, ready_to_approved, is_apply_last_update, rmg_process_breakdown,'".date('d-M-Y',time())."' from wo_booking_mst where booking_no in ($book_nos)";

			/*$rID3=execute_query($sql_insert,0);
			if($flag==1)
			{
				if($rID3) $flag=1; else $flag=0;
			} */
			//$booking_next_id=return_next_id( "id", "wo_booking_dtls_hstry", 1 ) ;
			$sql_insert_dtls="insert into wo_booking_dtls_hstry(id, approved_no, booking_dtls_id, job_no, po_break_down_id, pre_cost_fabric_cost_dtls_id, color_size_table_id, booking_no, booking_type, is_short, fabric_color_id, item_size, fin_fab_qnty, grey_fab_qnty, rate, amount, color_type, construction, copmposition, gsm_weight, dia_width, process_loss_percent, trim_group, description, brand_supplier, uom, process, sensitivity, wo_qnty, delivery_date, cons_break_down, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, rmg_qty, gmt_item, responsible_dept, responsible_person, reason, gmts_size, gmts_color_id,revised_date)
				select
				'', $approved_string_dtls, id, job_no, po_break_down_id, pre_cost_fabric_cost_dtls_id, color_size_table_id, booking_no, booking_type, is_short, fabric_color_id, item_size, fin_fab_qnty, grey_fab_qnty, rate, amount, color_type, construction, copmposition, gsm_weight, dia_width, process_loss_percent, trim_group, description, brand_supplier, uom, process, sensitivity, wo_qnty, delivery_date, cons_break_down, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, rmg_qty, gmt_item, responsible_dept, responsible_person, reason, gmts_size, gmts_color_id,'".date('d-M-Y',time())."' from wo_booking_dtls where booking_no in ($book_nos)";
			//echo "21**".$sql_insert_dtls;die;
			/*$rID4=execute_query($sql_insert_dtls,1);
			if($flag==1)
			{
				if($rID4) $flag=1; else $flag=0;
			} */
		}

		$field_array_booking_update = "is_approved";
		//$rID=sql_multirow_update("wo_booking_mst","is_approved",$partial_approval,"id",$booking_ids,0);
		$queryText = "select a.id,a.SETUP_DATE,b.APPROVAL_NEED,b.ALLOW_PARTIAL,b.PAGE_ID from APPROVAL_SETUP_MST a,APPROVAL_SETUP_DTLS b where a.id=b.MST_ID and a.COMPANY_ID=$cbo_company_name and b.PAGE_ID=5 and a.STATUS_ACTIVE =1 and a.IS_DELETED=0  and b.STATUS_ACTIVE =1 and b.IS_DELETED=0 order by a.SETUP_DATE desc";
				//echo $queryText;
		$queryTextRes = sql_select($queryText);
		$flag=1;
		
		if($queryTextRes[0]['ALLOW_PARTIAL']!=1)
		{
			$rID=execute_query(bulk_update_sql_statement( "wo_booking_mst", "id", $field_array_booking_update, $data_array_booking_update, $booking_id_arr));
			if($rID) $flag=1; else $flag=0;
		}
		else
		{
			global $pc_date_time;
			
			$booking_id_arrs=array_filter(array_unique($booking_id_arr));
			$booking_all_ids=implode(",", $booking_id_arrs);
			$query_mst="UPDATE wo_booking_mst SET is_approved=1,approved_by=$user_id_approval,approved_date='$pc_date_time' WHERE id in ($booking_all_ids)";
			$rID1=execute_query($query_mst,0);
			if($rID1) $flag=1; else $flag=0;
		}



		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=7 and mst_id in ($booking_ids)";
		$rIDapp=execute_query($query,1);
		if($flag==1)
		{
			if($rIDapp) $flag=1; else $flag=0;
		}

		/*
		if($approval_ids!="")
		{
			$rIDapp=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
			if($flag==1)
			{
				if($rIDapp) $flag=1; else $flag=0;
			}
		}*/

		//echo "18**".$sql_insert_dtls;die;
		//echo "18**insert into approval_history (".$field_array.") Values ".$data_array;die;
		$rID2=sql_insert("approval_history",$field_array,$data_array,0);
		
		//echo "18**".$rID2;die;
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}

		if($flag==1) 
		{
			$field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
			$rID6=sql_insert("approval_mst",$field_array,$app_data_array,0);
			if($rID6) $flag=1; else $flag=0; 
		}


		if(count($approved_no_array)>0)
		{
			$rID3=execute_query($sql_insert,0);
			if($flag==1)
			{
				if($rID3) $flag=1; else $flag=0;
			}

			$rID4=execute_query($sql_insert_dtls,1);
			if($flag==1)
			{
				if($rID4) $flag=1; else $flag=0;
			} //echo $sql_insert_dtls;die;
		}
		/*oci_rollback($con);
			echo "21**".$approval_ids;die;*/
			
		if($flag==1)
		{
			auto_approved(array('company_id'=>$cbo_company_name,'app_necessity_page_id'=>5,'mst_table'=>'wo_booking_mst','sys_id'=>$booking_ids,'approval_by'=>$user_id_approval));//,user_sequence=>$user_sequence_no,entry_form=>15,page_id=>$menu_id
		}

		foreach($data_array_booking_update as $booking_id=>$dataStrt){
			if(implode(',',$dataStrt)==1){send_final_app_notification($booking_id);}
		}

			
		if($flag==1) $msg='19'; else $msg='21';
	}
	else if($approval_type==1)
	{
		
		$checkSql="select BOOKING_ID from INV_RECEIVE_MASTER WHERE BOOKING_ID  in ($booking_ids) and ITEM_CATEGORY=13 and STATUS_ACTIVE=1
		UNION ALL
		select id as BOOKING_ID from WO_NON_ORD_KNITDYE_BOOKING_MST WHERE id  in ($booking_ids) and STATUS_ACTIVE=1";
		$checkSqlRes=sql_select($checkSql); 
		foreach($checkSqlRes as $rows)
		{
			 echo "25**Please Check Knit Finish Fabric Receive & Knitting Production To Unapproved or Deny";exit();
		}
		unset($checkSqlRes);


		
		$flag=1;
		$rID=sql_multirow_update("wo_booking_mst","is_approved*ready_to_approved","0*0","id",$booking_ids,0);
		if($rID) $flag=1; else $flag=0;

		//$rID2=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
		
		/*if(is_duplicate_field( "approval_cause", "approval_cause_refusing_his", "approval_cause='".str_replace("'", "", $unappv_request)."' and entry_form=7 and booking_id='".str_replace("'", "", $wo_id)."' and approval_type=2 and status_active=1 and is_deleted=0" )==1)
		{
		}
		else
		{*/
		//echo "10**";
		$sqlidPre="select booking_id, max(id) as id from fabric_booking_approval_cause where booking_id in ($booking_ids) and entry_form=7 and approval_type=2 group by booking_id";
		$idPreRes=sql_select($sqlidPre); $idpre="";
		foreach($idPreRes as $idrow)
		{
			if($idpre=="") $idpre=$idrow[csf('id')]; else $idpre.=','.$idrow[csf('id')];
		}
		unset($idPreRes);
		
		//print_r($idpre);
		if($idpre!="")
		{
			$sqlHis="insert into approval_cause_refusing_his( id, cause_id, page_id, entry_form, user_id, booking_id, approval_type, approval_no, approval_history_id, approval_cause, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, not_approval_cause)
					select '', id, page_id, entry_form, user_id, booking_id, approval_type, approval_no, approval_history_id, approval_cause, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, not_approval_cause from fabric_booking_approval_cause where booking_id in ($booking_ids) and approval_type=2 and entry_form=7 and id in ($idpre)";
			//echo "10**".$sqlHis; die;
			
			if(count($sqlHis)>0)
			{
				$rID3=execute_query($sqlHis,0);
				if($flag==1)
				{
					if($rID3==1) $flag=1; else $flag=0;
				}
			}
		}
		//}
		
		/*$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=7 and mst_id in ($booking_ids)";
		$rID2=execute_query($query,1);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}*/
		$currAppStatus=0;
		$data=$currAppStatus."*".$user_id_approval."*'".$pc_date_time."'*".$user_id."*'".$pc_date_time."'";
		$rID3=sql_multirow_update("approval_history","current_approval_status*un_approved_by*un_approved_date*updated_by*update_date",$data,"id",$approval_ids,1);
		if($flag==1)
		{
			if($rID3) $flag=1; else $flag=0;
		}

		$rID4=sql_multirow_update("fabric_sales_order_mst","is_apply_last_update","2","booking_id",$booking_ids,1);
		if($flag==1)
		{
			if($rID4) $flag=1; else $flag=0;
		}

		$response=$booking_ids;

		if($flag==1) $msg='20'; else $msg='22';
	}
	else if($approval_type==5)
	{
		$checkSql="select BOOKING_ID from INV_RECEIVE_MASTER WHERE BOOKING_ID  in ($booking_ids) and ITEM_CATEGORY=13 and STATUS_ACTIVE=1
		UNION ALL
		select id as BOOKING_ID from WO_NON_ORD_KNITDYE_BOOKING_MST WHERE id  in ($booking_ids) and STATUS_ACTIVE=1";
		$checkSqlRes=sql_select($checkSql); 
		foreach($checkSqlRes as $rows)
		{
			 echo "25**Please Check Knit Finish Fabric Receive & Knitting Production To Unapproved or Deny";exit();
		}
		unset($checkSqlRes);

		
		
		$sqlBookinghistory="select id, mst_id from approval_history where current_approval_status=1 and entry_form=7 and mst_id in ($booking_ids) ";
		//echo "10**".$sqlBookinghistory;
		$nameArray=sql_select($sqlBookinghistory); $bookidstr=""; $approval_id="";
		foreach ($nameArray as $row)
		{
			if($bookidstr=="") $bookidstr=$row[csf('mst_id')]; else $bookidstr.=','.$row[csf('mst_id')];
			if($approval_id=="") $approval_id=$row[csf('id')]; else $approval_id.=','.$row[csf('id')];
		}
		
		$appBookNoId=implode(",",array_filter(array_unique(explode(",",$bookidstr))));
		$approval_ids=implode(",",array_filter(array_unique(explode(",",$approval_id))));
		/*$book_ids=count(explode(",",$bookidstr)); $bookingidCond="";
		if($db_type==2 && $book_ids>1000)
		{
			$bookingidCond=" and (";
			$bookingnoIdArr=array_chunk(explode(",",$booknoId),999);
			foreach($bookingnoIdArr as $ids)
			{
				$ids=implode(",",$ids);
				$bookingidCond.=" mst_id in($ids) or"; 
			}
			$bookingidCond=chop($bookingidCond,'or ');
			$bookingidCond.=")";
		}
		else $bookingidCond=" and mst_id in($booknoId)";*/ 
		
		$rID=sql_multirow_update("wo_booking_mst","is_approved*ready_to_approved","0*0","id",$booking_ids,0);
		if($rID) $flag=1; else $flag=0;

		//$rID2=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
		if($approval_ids!="")
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=7 and current_approval_status=1 and id in ($approval_ids)";
			//echo "10**".$query;
			$rID2=execute_query($query,1);
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}
		}

			
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=7 and mst_id in ($booking_ids)";
			$rID3=execute_query($query,1); 
			if($rID3) $flag=1; else $flag=0; 
		}

		/*$data=$user_id_approval."*'".$pc_date_time."'*".$user_id."*'".$pc_date_time."'";
		$rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date*updated_by*update_date",$data,"id",$approval_ids,1);
		if($flag==1)
		{
			if($rID3) $flag=1; else $flag=0;
		}*/

		$rID4=sql_multirow_update("fabric_sales_order_mst","is_apply_last_update","2","booking_id",$booking_ids,1);
		if($flag==1)
		{
			if($rID4) $flag=1; else $flag=0;
		}

		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=7 and mst_id in ($booking_ids)";
			$rID5=execute_query($query,1); 
			if($rID4) $flag=1; else $flag=0; 
		}
		//echo "10**".$rID.'='.$rID2.'='.$rID4.'='.$flag; die;
		$response=$booking_ids;
		if($flag==1) $msg='50'; else $msg='51';
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
                    $sql="select image_location from common_photo_library where master_tble_id in('$job_no') and form_name='knit_order_entry' and file_type=1";
                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
						$i++;
                    ?>
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

if($action=="file")
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
                    $sql="select image_location from common_photo_library where master_tble_id in('$job_no') and form_name='knit_order_entry' and file_type=2";
                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
						$i++;
                    ?>
                    	<td width="100" align="center"><a target="_blank" href="../../<? echo $row[csf('image_location')]; ?>"><img width="89" height="97" src="../../file_upload/blank_file.png"><br>File-<? echo $i; ?></a></td>
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
		$sql_cause="select MAX(id) as id from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=7 and user_id=$txt_alter_user and booking_id='$wo_id' and approval_type=$app_type and status_active=1 and is_deleted=0";
		//echo $sql_cause; //die;
		$nameArray_cause=sql_select($sql_cause);
		if(count($nameArray_cause)>0){
			foreach($nameArray_cause as $row)
			{
				$app_cause1=return_field_value("approval_cause", "fabric_booking_approval_cause", "id='".$row[csf("id")]."' and status_active=1 and is_deleted=0");
				$app_cause = str_replace(array("\r", "\n"), ' ', $app_cause1);
			}
		}
		else
		{
			$app_cause = '';
		}
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
				http.open("POST","fabric_booking_approval_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange=fnc_appv_entry_Reply_info;
			}
		}

		function fnc_appv_entry_Reply_info()
		{
			if(http.readyState == 4)
			{
				//release_freezing();
				//alert(http.responseText);return;

				var reponse=trim(http.responseText).split('**');
				show_msg(reponse[0]);

				set_button_status(1, permission, 'fnc_appv_entry',1);
				release_freezing();

				generate_worder_mail(reponse[2],reponse[3],reponse[4],reponse[5]);
				fnc_close();
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
			http.open("POST","fabric_booking_approval_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fnc_appv_mail_Reply_info;

		}

		function fnc_appv_mail_Reply_info()
		{
			if(http.readyState == 4)
			{
				var response=trim(http.responseText).split('**');
				/*if(response[0]==222)
				{
					show_msg(reponse[0]);
				}*/
			}
			release_freezing();
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
                        <input type="hidden" name="wo_id" class="text_boxes" ID="wo_id" value="<? echo $wo_id; ?>" style="width:30px" />
                        <input type="hidden" name="appv_type" class="text_boxes" ID="appv_type" value="<? echo $app_type; ?>" style="width:30px" />
                        <input type="hidden" name="page_id" class="text_boxes" ID="page_id" value="<? echo $menu_id; ?>" style="width:30px" />
                        <input type="hidden" name="user_id" class="text_boxes" ID="user_id" value="<? echo $txt_alter_user_id; ?>" style="width:30px" />
                        <input type="hidden" name="approval_id" class="text_boxes" ID="approval_id" value="<? echo $approval_id; ?>" style="width:30px" />
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

if ($action=="remark_popup")
{
	echo load_html_head_contents("Approval Cause Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	$data_all=explode('_',$data);
	$remark=$data_all[0];
	?>
    <script>
    </script>
    <body>
		<div align="center" style="width:100%;">
        	<h5><?php echo $remark; ?></h5>
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
                        <input type="hidden" name="wo_id" class="text_boxes" ID="wo_id" value="<? echo $wo_id; ?>" style="width:30px" />
                        <input type="hidden" name="appv_type" class="text_boxes" ID="appv_type" value="<? echo $app_type; ?>" style="width:30px" />
                        <input type="hidden" name="page_id" class="text_boxes" ID="page_id" value="<? echo $menu_id; ?>" style="width:30px" />
                        <input type="hidden" name="user_id" class="text_boxes" ID="user_id" value="<? echo $user_id; ?>" style="width:30px" />
                        <input type="hidden" name="approval_id" class="text_boxes" ID="approval_id" value="<? echo $approval_id; ?>" style="width:30px" />
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
	//print_r($_REQUEST);
	$wo_id=$data;
	$sql_req="select approval_cause,approval_no from fabric_booking_approval_cause where entry_form=7 and booking_id='$wo_id' and approval_type=2 and status_active=1 and is_deleted=0 order by approval_no ";
	//echo $sql_req;
	$nameArray_req=sql_select($sql_req);
	foreach($nameArray_req as $row)
	{
		$unappv_req=$row[csf('approval_cause')];
	}

	?>
    <script>

		//var permission='<?// echo $permission; ?>';

		//$( document ).ready(function() {
			//document.getElementById("unappv_req").value='<?// echo $unappv_req; ?>';
		//});


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
                    	<textarea name="unappv_req" id="unappv_req" class="text_area" style="width:430px; height:100px;" readonly><?= $unappv_req; ?></textarea>
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
        <?
			$sqlHis="select approval_cause from approval_cause_refusing_his where entry_form=7 and booking_id='$wo_id' and approval_type=2 and status_active=1 and is_deleted=0 order by id Desc";
			$sqlHisRes=sql_select($sqlHis);
		?>
		<table align="center" cellspacing="0" width="420" class="rpt_table" border="1" rules="all">
			<thead>
				<th width="30">SL</th>
				<th>Unapproved Request History</th>
			</thead>
		</table>
		<div style="width:420px; overflow-y:scroll; max-height:260px;" align="center">
			<table align="center" cellspacing="0" width="403" class="rpt_table" border="1" rules="all">
			<?
			$i=1;
			foreach($sqlHisRes as $hrow)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');">
					<td width="30"><?=$i; ?></td>
					<td style="word-break:break-all"><?=$hrow[csf('approval_cause')]; ?></td>
				</tr>
				<?
				$i++;
			}
			?>
			</table>
		</div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
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
$custom_designation = return_library_array("select id,custom_designation from lib_designation ", 'id', 'custom_designation');
$Department = return_library_array("select id,department_name from  lib_department ", 'id', 'department_name');
//$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
$sql = "select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.page_id=$menu_id order by sequence_no";
//echo $sql;
$arr = array(2 => $custom_designation, 3 => $Department);
echo create_list_view("list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,", "630", "220", 0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr, "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);');
?>

	</form>
	<script language="javascript" type="text/javascript">
	  setFilterGrid("tbl_style_ref");
	</script>
	<?
}

if ($action=="save_update_delete_appv_cause")
{
	//$approval_id
	$approval_type=str_replace("'","",$appv_type);


	if($approval_type==0)
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

			$approved_no_history=return_field_value("approved_no","approval_history","entry_form=7 and mst_id=$wo_id and approved_by=$user_id");
			$approved_no_cause=return_field_value("approval_no","fabric_booking_approval_cause","entry_form=7 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

			//echo "shajjad_".$approved_no_history.'_'.$approved_no_cause; die;

			if($approved_no_history=="" && $approved_no_cause=="")
			{
				//echo "insert"; die;

				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",7,".$user_id.",".$wo_id." ,".$appv_type.",0,".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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
			else if($approved_no_history=="" && $approved_no_cause!="")
			{
				$con = connect();
				if($db_type==0)
				{
					mysql_query("BEGIN");
				}

				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=7 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
				$data_array="".$page_id."*7*".$user_id."*".$wo_id."*".$appv_type."*0*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

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
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=7 and mst_id=$wo_id and approved_by=$user_id");
				$max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=7 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$page_id.",7,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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

					$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=7 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

					$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
					$data_array="".$page_id."*7*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

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
			else if($approved_no_history!="" && $approved_no_cause=="")
			{
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=7 and mst_id=$wo_id and approved_by=$user_id");
				$max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=7 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$page_id.",7,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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

					$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=7 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

					$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
					$data_array="".$page_id."*7*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

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

			$unapproved_cause_id=return_field_value("id","fabric_booking_approval_cause","entry_form=7 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");


			$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=7 and mst_id=$wo_id and approved_by=$user_id");

			if($unapproved_cause_id=="")
			{

				//echo "shajjad_".$unapproved_cause_id; die;

				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_history_id,approval_cause,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",7,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$approval_id.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

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
			else
			{

				//echo "10**entry_form=7 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id";die;
				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=7 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");

				$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_history_id*approval_cause*updated_by*update_date*status_active*is_deleted";
				$data_array="".$page_id."*7*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$approval_id."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

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
	?>

        <table width="800" cellpadding="0" cellspacing="0" border="1">
            <tr>
                <td valign="top" align="center"><strong><font size="+2">Subject : Fabric Booking &nbsp;<?  if($appvtype==0) echo "Approval Request"; else echo "Un-Approval Request"; ?>&nbsp;Refused</font></strong></td>
            </tr>
            <tr>
                <td valign="top">
                    Dear Mr. <?
								$to="";

								$sql ="SELECT c.team_member_name FROM wo_booking_mst a,wo_po_details_master b,lib_mkt_team_member_info c where b.job_no=a.job_no and b.dealing_marchant=c.id and a.id=$woid";
								$result=sql_select($sql);
								foreach($result as $row)
								{
									if ($to=="")  $to=$row[csf('team_member_name')]; else $to=$to.", ".$row[csf('team_member_name')];
								}
								echo $to;
							?>
                            <br> Your Fabric Booking No. &nbsp;
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

if($action=="check_booking_last_update")
{
	$last_update=return_field_value("is_apply_last_update","wo_booking_mst","booking_no='".trim($data)."'");
	echo $last_update;
	exit();
}

if($action=="check_sales_order_approved")
{
	$last_update=return_field_value("is_approved","fabric_sales_order_mst","sales_booking_no='".trim($data)."'");
	
	if($last_update==''){
		$last_update=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id and b.work_order_no='".trim($data)."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0");
		if($last_update){$last_update=4;}
	}
	
	
	echo $last_update;
	exit();
}

if($action=="pre_cost_data")
{
	
	$sql =  "select a.JOB_NO, a.COMPANY_NAME, a.BUYER_NAME, a.STYLE_REF_NO,b.COSTING_DATE,b.COSTING_PER,b.ENTRY_FROM from wo_po_details_master a,wo_pre_cost_mst b where a.job_no=b.job_no and b.job_no='".trim($data)."'";
	
	$jobData=sql_select($sql);
	foreach($jobData as $row)
	{
		$last_job_data=implode('***',$row);;
	}
	echo $last_job_data;
	exit();
}

if($action=="budgetsheet3")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	?>
	<style>
		.trims-gallery{
			margin: 5px;
			border: 1px solid #ccc;
			float: left;
		}
		.trims-gallery:hover {
			border: 1px solid #777;
		}
	</style>
	<?
	
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name");

	if($txt_job_no=="") $job_no=''; else $job_no=" and a.job_no=".$txt_job_no."";
	$costing_date=str_replace("'","",$txt_costing_date);

	$txt_costing_date=change_date_format(str_replace("'","",$txt_costing_date),'yyyy-mm-dd','-');
	if($cbo_company_name=="") $company_name=''; else $company_name=" and a.company_name=".$cbo_company_name."";
	if($cbo_buyer_name=="") $cbo_buyer_name=''; else $cbo_buyer_name=" and a.buyer_name=".$cbo_buyer_name."";
	if($txt_style_ref=="") $txt_style_ref=''; else $txt_style_ref=" and a.style_ref_no=".$txt_style_ref."";
	if($txt_costing_date=="") $txt_costing_date=''; else $txt_costing_date=" and b.costing_date='".$txt_costing_date."'";

	if(str_replace("'",'',$txt_po_breack_down_id)=="")
	{
		$txt_po_breack_down_id_cond='';
		$txt_po_breack_down_id_cond1='';
		$txt_po_breack_down_id_cond2='';
		$txt_po_breack_down_id_cond3='';
	}
	else
	{
		$selected_po_breack_down_id = str_replace("'",'',$txt_po_breack_down_id);
		$txt_po_breack_down_id_cond=" and b.id in(".$selected_po_breack_down_id.")";
		$txt_po_breack_down_id_cond1=" and id in(".$selected_po_breack_down_id.")";
	}
	
	$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=".$cbo_company_name."  and variable_list=22 and status_active=1 and is_deleted=0");
	if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;

	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

	//array for display name
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$body_part_type_arr=return_library_array("select id, body_part_full_name from lib_body_part","id","body_part_full_name");



	 $po_qty=0; $po_plun_cut_qty=0; $total_set_qnty=0;$total_fob_value=0;$job_in_orders=''; $pulich_ship_date=''; $job_in_file=''; $job_in_ref='';
	 $sql_po="SELECT a.job_no,a.location_name, a.total_set_qnty, b.id, b.po_number, b.grouping, b.file_no, b.pub_shipment_date, c.order_total,c.item_number_id, c.country_id, c.color_number_id, c.size_number_id, c.order_quantity, c.plan_cut_qnty  from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.job_no =".$txt_job_no." $txt_po_breack_down_id_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id";
	 
	$sql_po_data=sql_select($sql_po);
	foreach($sql_po_data as $sql_po_row)
	{
		$po_qty+=$sql_po_row[csf('order_quantity')];
		$po_plun_cut_qty+=$sql_po_row[csf('plan_cut_qnty')];
		$total_set_qnty=$sql_po_row[csf('total_set_qnty')];
		$location_name_id=$sql_po_row[csf('location_name')];
		
		$pulich_ship_date = $sql_po_row[csf('pub_shipment_date')];
		$job_in_file .= $sql_po_row[csf('file_no')].",";
		$job_in_ref .= $sql_po_row[csf('grouping')].",";
		$total_fob_value+=$sql_po_row[csf('order_total')];
		$po_id_arr[$sql_po_row[csf('id')]]=$sql_po_row[csf('id')];
	}
	$po_id_str= implode( ",",$po_id_arr);
	
	$min_max_ship_date=sql_select("select job_no_mst, min(pub_shipment_date) as min_pub_shipment_date, max(pub_shipment_date) as max_pub_shipment_date from wo_po_break_down where job_no_mst=".$txt_job_no." and status_active=1 and is_deleted=0 group by job_no_mst");
	foreach($min_max_ship_date as $row){
		$min_pub_ship_date=$row[csf('min_pub_shipment_date')];
		$max_pub_ship_date=$row[csf('max_pub_shipment_date')];
	}
	$shipment_country=sql_select("SELECT country_id from wo_po_color_size_breakdown where job_no_mst=".$txt_job_no." and status_active=1 and is_deleted=0 group by country_id");
	foreach($shipment_country as $row){
		$all_country_arr[$row[csf('country_id')]]=$country_library[$row[csf('country_id')]];
	}
	$fab_knit_req_kg_avg=0; $fab_woven_req_yds_avg=0;
 	$sql_mst = "SELECT a.id as job_id, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.quotation_id, a.gmts_item_id, a.order_uom, a.job_quantity, a.avg_unit_price, a.style_description, b.costing_per, b.budget_minute, b.costing_date, b.sew_smv, b.cut_smv, b.sew_effi_percent, b.cut_effi_percent, b.approved, b.exchange_rate, b.remarks, a.qlty_label, a.packing, a.sustainability_standard from wo_po_details_master a , wo_pre_cost_mst b where  a.id=b.job_id and  b.status_active=1 and b.is_deleted=0  and a.status_active=1 and a.job_no=$txt_job_no $company_name $cbo_buyer_name  order by a.job_no";
	//echo $sql_mst; die;
	$data_array=sql_select($sql_mst);
		
	$uom=""; $sew_smv=0; $cut_smv=0; $sew_effi_percent=0; $cut_effi_percent=0;
	foreach ($data_array as $row)
	{
		$order_price_per_dzn=0; $order_job_qnty=0; $avg_unit_price=0;
		$buyer_name=$row[csf("buyer_name")];
		$job_id=$row[csf("job_id")];
		$style_ref_no=$row[csf("style_ref_no")];
		$style_desc=$row[csf("style_description")];
		$uom_id=$row[csf("order_uom")];
		$approved=$row[csf("approved")];
		$budget_minute=$row[csf("budget_minute")];
		$costing_per_id=$row[csf("costing_per")];
		$job_quantity=$row[csf("job_quantity")];
		$quotation_id= $row[csf("quotation_id")];
		$avg_unit_price=$row[csf("avg_unit_price")];
		$fab_yarn_req_kg=$row[csf("fab_yarn_req_kg")];
		$remarks=$row[csf("remarks")];
		$qlty_label=$quality_label[$row[csf("qlty_label")]];
		$packing_type=$packing[$row[csf("packing")]];		
		$sew_smv=$row[csf("sew_smv")];
	    $cut_smv=$row[csf("cut_smv")]; 
		$costing_date=$row[csf("costing_date")];
	    $sew_effi_percent=$row[csf("sew_effi_percent")];
	    $cut_effi_percent=$row[csf("cut_effi_percent")];
		$order_values = $row[csf("job_quantity")]*$avg_unit_price;
		$exchange_rate=$row[csf("exchange_rate")];
		$sustainability_standard=$sustainability_standard[$row[csf("sustainability_standard")]];
		 
		
		$job_in_ref=rtrim($job_in_ref,", ");
		$job_in_file=rtrim($job_in_file,", ");
		$job_ref=array_unique(explode(",",$job_in_ref));
		$job_file=array_unique(explode(",",$job_in_file));
		
		?>
        <table style="width:850px" >
			<tr>
				<td align="left"></td>
				<td align="left"><b style="font-size:25px; text-align:center; margin-left:35px"><? echo $comp[str_replace("'","",$cbo_company_name)]; ?></b></b>
				</td>
			</tr>
			<tr>
				<td align="left"><b style="font-size:18px; text-align:left;">DATE: <? echo change_date_format($costing_date); ?></b></td>
				<td align="left"><div style="font-size:18px; width:220px; font-weight:bold; text-align:left; padding:10px; background-color:yellow; color:#00008B;">Factory Pre-Costing Sheet</div>
				</td>
				<td align="left"><div style="font-size:18px; width:220px; font-weight:bold; text-align:right; padding:10px;  color:#00FF00;"><? if(str_replace("'","",$approved) ==2){ echo "Approved";}else{echo "";}; ?></div>
				</td>
			</tr>
		</table>
        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px" rules="all">
			<tr>
				<td width="140">Buyer</td>
                <td width="180" style="word-break:break-all"><? echo $buyer_arr[$buyer_name]; ?></td>
				<td rowspan="5" style="text-align: center; background-color:yellow;"><b>Style Ref. <? echo $style_ref_no; ?></b></td>
				<td colspan="2" style="text-align: center; background-color:green;">Condition</td>
			</tr>
			<tr>
				<td>Description</td>
                <td><? echo $style_desc; ?></td>
                <td colspan="2" style="text-align: center; background-color:#ff8c00;">Inspection By</td>
			</tr>
			<tr>
				<td>LC Company</td>
                <td><? echo $comp[str_replace("'","",$cbo_company_name)]; ?></td>
                <td colspan="2" align="center"><?= $qlty_label ?></td>
			</tr>
			<tr>
				<td>Remarks</td>
                <td><?= $remarks ?></td>
                <td>Packing</td>
                <td><?= $packing_type ?></td>
			</tr>
			<tr>
				<td>Order QTY</td>
                <td><? echo $job_quantity." ". $unit_of_measurement[$uom_id]; ?></td>
                <td>Country</td>
                <td><?echo implode(",", $all_country_arr)?></td>
			</tr>
			<tr>
				<td>Del</td>
				<td colspan="4"><?= change_date_format($min_pub_ship_date)?>  to <?= change_date_format($max_pub_ship_date)?></td>
			</tr>
            
        </table>
    	<?
		if($costing_per_id==1){
			$order_price_per_dzn=12;
			$costing_for=" DZN";
		}
		else if($costing_per_id==2){
			$order_price_per_dzn=1;
			$costing_for=" PCS";
		}
		else if($costing_per_id==3){
			$order_price_per_dzn=24;
			$costing_for=" 2 DZN";
		}
		else if($costing_per_id==4){
			$order_price_per_dzn=36;
			$costing_for=" 3 DZN";
		}
		else if($costing_per_id==5){
			$order_price_per_dzn=48;
			$costing_for=" 4 DZN";
		}
		$order_job_qnty=$job_quantity;
		$avg_unit_price=$avg_unit_price;
	}//end first foearch
 	

	//start	Trims Cost part report here -------------------------------------------
	$sql_trim = "select id, job_no, trim_group,description,brand_sup_ref,remark, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active,seq
	from wo_pre_cost_trim_cost_dtls
	where job_no=".$txt_job_no." and status_active=1 and is_deleted=0 order by seq";
	$data_array_trim=sql_select($sql_trim);
	$TrimData=array();
	foreach( $data_array_trim as $row_trim )
	{
		$TrimData[$row_trim[csf('id')]]['trim_group']=$row_trim[csf('trim_group')];
		$TrimData[$row_trim[csf('id')]]['rate']=$row_trim[csf('rate')];
		$TrimData[$row_trim[csf('id')]]['amount']=$row_trim[csf('amount')];
	}
	//End	Trims Cost part report here -------------------------------------------

	$sql_new = "select job_no, comm_cost, comm_cost_percent, commission, commission_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, depr_amor_pre_cost, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche, design_cost, design_percent, studio_cost, studio_percent, deffdlc_cost, interest_cost, incometax_cost from wo_pre_cost_dtls where job_no=".$txt_job_no." and status_active=1 and is_deleted=0";
	$data_array_new=sql_select($sql_new);
	$labtest_others_attr=array('lab_test','inspection', 'freight', 'currier_pre_cost', 'certificate_pre_cost', 'deffdlc_cost', 'design_cost', 'studio_cost', 'common_oh', 'interest_cost', 'incometax_cost', 'depr_amor_pre_cost');

	foreach( $data_array_new as $row_new )
	{
		foreach($labtest_others_attr as $attr){
			$labtest_others_total+=$row_new[csf($attr)];
		}
		$cm_cost=$row_new[csf('cm_cost')];
	}

	//Emb cost Cost part report here -------------------------------------------

	$sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pre_cost_embe_cost_dtls where job_no=".$txt_job_no." and emb_name in(1,2,4,5,6,99) and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	$EmbData=array();
	$EmbData['Print']['cons_dzn_gmts']=0;
	$EmbData['Embroidery / Special Works / Gmts Dyeing']['cons_dzn_gmts']=0;
	foreach( $data_array as $row )
	{
		if($row[csf("emb_name")]==1){
			$EmbData['Print']['amount']+=$row[csf("amount")];
		}
		else{
			$EmbData['Embroidery / Special Works / Gmts Dyeing']['amount']+=$row[csf("amount")];
		}
	}
	//End Emb cost Cost part report here 
	//Wash cost Cost part report here 
	$sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pre_cost_embe_cost_dtls where job_no=".$txt_job_no." and emb_name =3 and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	foreach( $data_array as $row ){
		$washData['Wash']['amount']+=$row[csf("amount")];
	}
	//End Wash cost Cost part report here
	//Commarcial cost Cost part report here
	$sql = "select id, job_no, item_id, rate, amount, status_active from  wo_pre_cost_comarci_cost_dtls  where job_no=".$txt_job_no." and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	$Commar_rate=0;
	foreach( $data_array as $row ){
		$Commar_rate+=$row[csf("rate")];
	}
	//End Commarcial cost Cost part report here
	//2	All Fabric Cost part here
	$sql = "select id, seq, job_no, body_part_id, fab_nature_id, color_type_id, fabric_description, uom, avg_cons, avg_cons_yarn, fabric_source, gsm_weight, rate, amount, avg_finish_cons, status_active, construction, composition  from wo_pre_cost_fabric_cost_dtls where job_no=".$txt_job_no."  and status_active=1 and is_deleted=0 order by seq";
	$data_array=sql_select($sql);

	$conv_sql = sql_select("SELECT b.id, a.charge_unit from wo_pre_cost_fab_conv_cost_dtls a  join wo_pre_cost_fabric_cost_dtls b on a.job_id=b.job_id and a.fabric_description=b.id where b.status_active=1 and b.is_deleted=0 and  a.job_no=".$txt_job_no." and a.status_active=1 and a.is_deleted=0 and b.color_type_id=5 and a.cons_process=35");
	$con_charg_rate=array();
	foreach($conv_sql as $conrow){
		$con_charg_rate[$conrow[csf('id')]]=$conrow[csf('charge_unit')];
	}

	$knit_fab=""; 
	$i=1; $j=1;
	foreach( $data_array as $row )
	{
		$uom=$unit_of_measurement[$row[csf("uom")]];
		$item_descrition = $row[csf("construction")].", ".$row[csf("composition")].", ".$row[csf("gsm_weight")].", ".$color_type[$row[csf("color_type_id")]].", ".$body_part_type_arr[$row[csf('body_part_id')]].", ".$sustainability_standard ;

		if($row[csf("fab_nature_id")]==2){//knit fabrics
			if($row[csf('color_type_id')]==5){
				$rate=$row[csf("rate")]+$con_charg_rate[$row[csf('id')]];
				$amount=$row[csf("avg_cons")]*$rate;
			}
			else{
				$rate=$row[csf("rate")];
				$amount=$row[csf("avg_cons")]*$rate;
			}			
		}
		if($row[csf("fab_nature_id")]==3){//woven fabrics
			if($row[csf('color_type_id')]==5){
				$rate=$row[csf("rate")]+$con_charg_rate[$row[csf('id')]];
				$amount=$row[csf("avg_cons")]*$rate;
			}
			else{
				$rate=$row[csf("rate")];
				$amount=$row[csf("avg_cons")]*$rate;
			}
		}

		$fabric_dtls_data_arr[$item_descrition]['description']=$item_descrition;
		$fabric_dtls_data_arr[$item_descrition]['avg_cons']+=$row[csf("avg_cons")];
		$fabric_dtls_data_arr[$item_descrition]['rate']+=$rate;
		$fabric_dtls_data_arr[$item_descrition]['amount']+=$amount;
	}
	foreach( $fabric_dtls_data_arr as $fabdata )
	{
		$knit_fab .= '<tr>
			<td align="left">'.$i.'</td>
			<td align="left">'.$fabdata['description'].'</td>
			<td align="right">'.fn_number_format($fabdata['avg_cons'],4).'</td>
			<td align="right"></td>
			<td align="right">'.fn_number_format($fabdata['rate'],4).'</td>
			<td align="right"></td>
			<td align="right">'.fn_number_format($fabdata['amount'],4).'</td>
		</tr>';	

			$Grandtotalavg_cons+=$fabdata['avg_cons'];
			$Grandtotal_rate+=$fabdata['rate'];
			$GrandtotalAmount+=$fabdata['amount'];
			$grand_total+=$fabdata['amount'];
			$i++;	
	}
		
	$knit_fab= '<div style="margin-top:15px">
			<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
					<tr><td colspan="7" align="left"><b>1.Fabric Cost  </b></td></tr>
					<tr style="font-weight:bold"  align="center">
						<td width="80">Sl</td>
						<td width="350">Description</td>
						<td width="100">Cons./'.$costing_for.'</td>
						<td width="100"></td>
						<td width="100">Kg/Price</td>
						<td width="100"></td>
						<td width="100">Cost/'.$costing_for.'</td>
					</tr>'.$knit_fab;
	
	echo $knit_fab;

	$knit_fab_total .='<tr class="rpt_bottom" style="font-weight:bold">
				<td colspan="2" align="right">Total</td>
				<td align="right">'.fn_number_format(($Grandtotalavg_cons),4).'</td>
				<td></td>
				<td align="right">'.fn_number_format(($Grandtotal_rate),4).'</td>
				<td align="right"></td>
				<td align="right">'.fn_number_format(($GrandtotalAmount),4).'</td>
			</tr></table></div>';
	echo $knit_fab_total;
	//end 	All Fabric Cost part report-------------------------------------------
  	//start	Trims Cost part report here -------------------------------------------
	$trim_group=return_library_array( "select item_name,id from  lib_item_group", "id", "item_name"  );
		?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
				<tr style="font-weight:bold">
					<td colspan="4" align="left">2. Accessories</td>
				</tr>
                <tr style="font-weight:bold">
                    <td width="320">Garments Image</td>
                    <td width="40">SL</td>
                    <td width="250">Item Group</td>
                    <td width="100">Cost/<? echo $costing_for; ?></td>
                </tr>
				<?
                $TotalDznAmount=0; $TotalAmount=0;
				$i=1;/* 
				echo '<pre>';
				print_r($TrimData); die; */
                foreach( $TrimData as $index=>$row )
                {
					if($i==1){ ?>
						<tr>							
                            <td align="left" rowspan="<?= count($TrimData) ?>">
							<?
								$nameArray_img =sql_select("SELECT image_location FROM common_photo_library where master_tble_id=$txt_job_no and form_name='knit_order_entry' and file_type=1");
								$path=($path)?$path:'../../';
											
								?>								
								<? foreach($nameArray_img AS $inf){ ?>
									<div class="trims-gallery">
										<img  src='<? echo $path.$inf[csf("image_location")]; ?>' height='120' width='160' />
									</div>
								<?  } ?>
								
							</td>
                            <td align="left"><? echo $i; ?></td>
                            <td align="left"><? echo $trim_group[$row["trim_group"]]; ?></td>
                            <td align="right">&dollar;<? echo fn_number_format($row["amount"],4); ?></td>
                        </tr>
					<? }
					else{
                    ?>
                        <tr>
							<td align="left"><? echo $i; ?></td>							
                            <td align="left"><? echo $trim_group[$row["trim_group"]]; ?></td>
                            <td align="right">&dollar;<? echo fn_number_format($row["amount"],4); ?></td>
                        </tr>
                    <?
					}
                     $TotalDznAmount += $row["amount"];
                     $grand_total += $row["amount"];
                     $TotalAmount += $row["tot_amount"];
                     $totalcons_dzn_gmts += $row["cons_dzn_gmts"];
					 $i++;
                }
                ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td align="left">Total</td>
                    <td align="right"></td>
                    <td align="right"></td>
                    <td align="right">&dollar;<? echo fn_number_format($TotalDznAmount,4); ?></td>
                </tr>
            </table>
        </div>
        <?
	//End Trims Cost Part report here -------------------------------------------
	//start	Embellishment Details part report here -------------------------------------------
	?>
	<div style="margin-top:15px">
		<table class="rpt_table" border="1" cellpadding="
		1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
			<tr style="font-weight:bold">
				<td width="40">SL</td>
				<td width="400">Particulars</td>
				<td width="240" colspan="2">Cost/<? echo $costing_for; ?></td>
				</tr>
			<?
			$TotalDznAmount=0; $TotalAmount =0;
			$sl=3;
			foreach( $EmbData as $index=>$row )
			{
				?>
					<tr>
						<td align="left" width="40"><? echo $sl; ?></td>
						<td align="left" width="400"><? echo $index; ?></td>
						<td align="right" width="240" colspan="2" style="font-weight:bold;">&dollar;<? echo fn_number_format($row["amount"],4); ?></td>
					</tr>
				<?
					$grand_total += $row["amount"];
					$sl++;
			}
			?>
			<tr>
				<td align="left"><?= $sl++; ?></td>
				<td align="left">CM</td>
				<td align="right" colspan="2" style="font-weight:bold;">&dollar;<? $grand_total+=$cm_cost; echo $cm_cost;  ?></td>
			</tr>
			<tr>
				<td align="left"><?= $sl++ ?></td>
				<td align="left">Wash</td>
				<td align="right" colspan="2" style="font-weight:bold;">&dollar;<? $grand_total+=$washData['Wash']['amount']; echo $washData['Wash']['amount'];  ?></td>
			</tr>
			<tr>
				<td align="left"><?= $sl++ ?></td>
				<td align="left" title="lab Test,Inspection,Freight,Courier Cost,Certificate Cost,Deffd. LC Cost,Design Cost,Studio Cost,Opert. Exp.,Interest,Income Tax,Depc. & Amort.">Inspection / Lab test / Others</td>
				<td align="right" colspan="2" style="font-weight:bold;">&dollar;<? $grand_total+=$labtest_others_total; echo $labtest_others_total ?></td>
			</tr>
			<tr>
				<td align="left"><?= $sl++ ?></td>
				<td align="left">Total</td>
				<td align="right" colspan="2" style="font-weight:bold;">&dollar;<?= fn_number_format($grand_total,2) ?></td>
			</tr>
			<tr>
				<td align="left"><?= $sl++ ?></td>
				<td align="left" style="font-weight:bold;">Commercial Charge</td>
				<td align="right" style="font-weight:bold;">Commercial Charge <?= fn_number_format($Commar_rate,2) ?>&percnt;</td>
				<td align="right" title="Agreed Price*Commercial Charge% *12" style="font-weight:bold;">&dollar;<?
					$commercial_charge=$avg_unit_price*($Commar_rate/100)*12;
					echo fn_number_format($commercial_charge,2) 
				?></td>
			</tr>
			<tr>
				<td align="right" colspan="3" style="font-weight:bold;">Grand Total price/dzn</td>
				<td align="right" title="Total+Commercial Charge" style="font-weight:bold;">&dollar;<?= fn_number_format($commercial_charge+$grand_total,2) ?></td>
			</tr>
			<tr>
				<td align="right" colspan="3" style="font-weight:bold;">Unit Price</td>
				<td align="right" title="Grand Total price/dzn/12" style="font-weight:bold;">&dollar;<?
					$factory_unit_price=($commercial_charge+$grand_total)/12;
				 	echo fn_number_format($factory_unit_price,2) 
				 ?></td>
			</tr>
			<tr>
				<?
					$factory_profit=$avg_unit_price-$factory_unit_price
				 ?>
				<td align="right" colspan="3" style="font-weight:bold;">Factory Profit <?= fn_number_format(($factory_profit/$avg_unit_price)*100,2)  ?>&percnt;</td>
				<td align="right" title="Agreed Price-Unit Price" style="font-weight:bold;">&dollar;<?= fn_number_format($factory_profit,2) ?></td>
			</tr>
			<tr>
				<td align="right" style="color:blue; font-weight:bold;" colspan="3">Final Unit Price</td>
				<td align="right" style="color:blue; font-weight:bold;" title="Factory Profit+Unit Price">&dollar;<?= fn_number_format($factory_profit+$factory_unit_price,2) ?></td>
			</tr>
			<tr>
				<td align="right" style="color:blue ; font-weight:bold;" colspan="3">Quoted Price</td>
				<td align="right" style="color:blue ; font-weight:bold;" title="Factory Profit+Unit Price">&dollar;<?= fn_number_format($factory_profit+$factory_unit_price,2) ?></td>
			</tr>
			<tr>
				<td align="right" style="color:blue ; font-weight:bold;" colspan="3">Target Price</td>
				<td align="right"></td>
			</tr>
			<tr>
				<td align="right" style="color:blue ; font-weight:bold;" colspan="3">Agreed Price</td>
				<td align="right" style="font-weight:bold;">&dollar;<?= fn_number_format($avg_unit_price,2) ?></td>
			</tr>
		</table>
		<?
			$nameArray_request=sql_select("SELECT id, job_id, heading, value_field_one, value_field_two, value_field_three, value_field_four from wo_pre_cost_fabric_price_dtls where job_id=$job_id order by id ASC");			
		?>
		<table id="fabric_price_date_tbl" cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" style="margin-top:10px ;">
		 	<? if(count($nameArray_request)>0){
				$i=1;
				foreach($nameArray_request as $row){
				if($i<=2){
				?>
				<tr>
					<td><strong><?= $row[csf('heading')] ?></td>
					<td><strong><?= $row[csf('value_field_one')] ?></strong></td>
					<td><strong><?= $row[csf('value_field_two')] ?></strong></td>
					<td><strong><?= $row[csf('value_field_three')] ?></strong></td>
					<td><strong><?= $row[csf('value_field_four')] ?></strong></td>
				</tr>
				<? } 
					else if ($i>2 && $i<14){ ?>
						<tr>
							<td><strong><?= $row[csf('heading')] ?></strong></td>
							<td><?= $row[csf('value_field_one')] ?></td>
							<td><?= $row[csf('value_field_two')] ?></td>
							<td><?= $row[csf('value_field_three')] ?></td>
							<td><?= $row[csf('value_field_four')] ?></td>
						</tr>
					<?
					$total_field_one +=$row[csf('value_field_one')]*1;
					$total_field_two +=$row[csf('value_field_two')]*1;
					$total_field_three +=$row[csf('value_field_three')]*1;
					$total_field_four +=$row[csf('value_field_four')]*1;
					}
				else{
				?>
				<tr>
					<td><strong><?= $row[csf('heading')] ?></strong></td>
					<td><?= $row[csf('value_field_one')] ?></td>
					<td><?= $row[csf('value_field_two')] ?></td>
					<td><?= $row[csf('value_field_three')] ?></td>
					<td><?= $row[csf('value_field_four')] ?></td>
				</tr>
				<? 
					$wastage_field_one = $total_field_one*($row[csf('value_field_one')]/100)*1;
					$wastage_field_two = $total_field_two*($row[csf('value_field_two')]/100)*1;
					$wastage_field_three = $total_field_three*($row[csf('value_field_three')]/100)*1;
					$wastage_field_four = $total_field_four*($row[csf('value_field_four')]/100)*1;
				} 
				$i++;
				}
				?>        
				<tr>
					<td><strong>Total</strong></td>
					<td><?= $total_field_one ?></td>
					<td><?= $total_field_two ?></td>
					<td><?= $total_field_three ?></td>
					<td><?= $total_field_four ?></td>
				</tr>
				<tr>
					<td><strong>wastage % price</strong></td>
					<td><?= number_format($wastage_field_one,2) ?></td>
					<td><?= number_format($wastage_field_two,2) ?></td>
					<td><?= number_format($wastage_field_three,2) ?></td>
					<td><?= number_format($wastage_field_four,2) ?></td>
				</tr>
				<tr>
					<td><strong>Fabric Price</strong></td>
					<td><?= number_format($total_field_one+$wastage_field_one,2) ?></td>
					<td><?= number_format($total_field_two+$wastage_field_two,2) ?></td>
					<td><?= number_format($total_field_three+$wastage_field_three,2) ?></td>
					<td><?= number_format($total_field_four+$wastage_field_four,2) ?></td>
				</tr>
		</table>
		<? } ?>

	</div>
	<?

	//End Commission Cost Part report here -------------------------------------------
	echo signature_table(109, $cbo_company_name, "860px");
	$mailBody=ob_get_contents();
	ob_clean();
	echo $mailBody;

	
	
	//------------------------------------End;
	
	
	exit();
}


if($action=="get_pre_cost_print_button")
{
	list($company_id,$reort_id)=explode('**',$data);
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company_id."' and module_id=2 and report_id=$reort_id and is_deleted=0 and status_active=1");
	$print_report_format_arr = explode(',',$print_report_format);
	echo $print_report_format_arr[0];
	exit();
}


// For Comments
if($action=="show_fabric_comment_report")
{
	echo load_html_head_contents("Approval Cause Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	//$last_update=return_field_value("is_apply_last_update","wo_booking_mst","booking_no='".trim($data)."'");
	//echo $last_update;
	?><body>
	<div>
	<table width="870"   cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
	 <thead>
	   <tr align="center">
	    <th colspan="12"><b>Comments</b></th>
	    </tr>

	    <tr>
	    <th width="30" rowspan="2">Sl</th>
	    <th width="120" rowspan="2">Po NO</th>
	    <th width="70" rowspan="2">Ship Date</th>
	    <th width="80" rowspan="2">As Merketing</th>
	    <th width="70" rowspan="2">As Budget</th>
	    <th width="70" rowspan="2">Mn.Book Qty</th>
	    <th width="70" rowspan="2">Sht.Book Qty</th>
	    <th width="70" rowspan="2">Smp.Book Qty</th>
	    <th  width="70" rowspan="2">Tot.Book Qty</th>
	    <th colspan="2">Balance</th>
	    <th width="" rowspan="2">Comments ON Budget</th>
	    </tr>
	    <tr>
	    <th width="70">As Mkt.</th>
	    <th width="70">As Budget</th>
	    </tr>
	     </thead>
	</table>
	<?

	$cbo_fabric_natu=str_replace("'","",$fab_nature);
	$cbo_fabric_source=str_replace("'","",$fab_source);
	if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and a.fab_nature_id='$cbo_fabric_natu'";
	if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and a.fabric_source='$cbo_fabric_source'";
	 $paln_cut_qnty_array=return_library_array( "select min(id) as id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown  where po_break_down_id in(".str_replace("'","",$order_id).") and is_deleted=0 and status_active=1 group by color_number_id,size_number_id,item_number_id,po_break_down_id", "id", "plan_cut_qnty");
	 $order_qnty_array=return_library_array( "select min(id) as id,sum(order_quantity) as order_quantity from wo_po_color_size_breakdown  where po_break_down_id in(".str_replace("'","",$order_id).") and is_deleted=0 and status_active=1 group by color_number_id,size_number_id,item_number_id,po_break_down_id", "id", "order_quantity");
	 $budget_on_sql=sql_select("select embellishment_id,embellishment_budget_id from variable_order_tracking where company_name='$company_name' and variable_list=75 and status_active=1 and is_deleted=0 and embellishment_id=3");
				if(count($budget_on_sql)>0){
					foreach($budget_on_sql as $row)
					{
						$budget_on=$row[csf('embellishment_budget_id')]?$row[csf('embellishment_budget_id')]:2;
					}
				}
				else{
					$budget_on=2;
				}
	//print_r( $paln_cut_qnty_array);
	//echo $job_no;

	$item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no in('".str_replace(',',"','",$job_no)."')", "gmts_item_id", "set_item_ratio");
	//$item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no ='$job_no'", "gmts_item_id", "set_item_ratio");
	//	echo "select quotation_id from wo_po_details_master where job_no='".$job_no."' ";
	$quotation_id = return_field_value(" quotation_id as quotation_id"," wo_po_details_master ","job_no in('".str_replace(',',"','",$job_no)."')","quotation_id");
	$tot_mkt_cost  = return_field_value(" sum(b.fab_knit_req_kg) as mkt_cost","wo_price_quotation a,wo_pri_quo_sum_dtls b"," a.id=b.quotation_id and a.id='".$quotation_id."'","mkt_cost");
	//	print_r( $item_ratio_array);
	$nameArray=sql_select("select a.id, a.item_number_id, a.costing_per, a.job_no, b.po_break_down_id, b.color_size_table_id, b.requirment, c.po_number
	FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_po_break_down c
	WHERE a.job_no=b.job_no and a.job_no=c.job_no_mst and a.id=b.pre_cost_fabric_cost_dtls_id and b.po_break_down_id=c.id and b.po_break_down_id in (".str_replace("'","",$order_id).")   and a.status_active=1 and a.is_deleted=0 order by id");
	$count=0;
	//$cbo_fabric_natu $cbo_fabric_source_cond
	$tot_grey_req_as_pre_cost_arr=array();$tot_grey_req_as_price_cost_arr=array();$tot_grey_req_as_price_cost=0;
	foreach ($nameArray as $result)
	{
		//echo "select quotation_id as quotation_id from wo_po_details_master where job_no='".$result[csf('job_no')]."'";
		// $quotation_id = return_field_value(" quotation_id as quotation_id"," wo_po_details_master ","job_no='".$result[csf('job_no')]."'","quotation_id");
		if (count($nameArray)>0 )
		{

			if($budget_on==1){
				$poqnty=$order_qnty_array[$result[csf("color_size_table_id")]];
			}else{
				$poqnty=$paln_cut_qnty_array[$result[csf("color_size_table_id")]];
			}

            if($result[csf("costing_per")]==1)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($poqnty/(12*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
				$dzn_qnty_p=12;
				//$tot_mkt_price=$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(12*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==2)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($poqnty/(1*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
				$dzn_qnty_p=1;
			}
			if($result[csf("costing_per")]==3)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($poqnty/(24*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
				$dzn_qnty_p=12*2;
			}
			if($result[csf("costing_per")]==4)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($poqnty/(36*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
				$dzn_qnty_p=12*3;
			}
			if($result[csf("costing_per")]==5)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($poqnty/(48*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
				$dzn_qnty_p=12*4;
			}
			$dzn_qnty_p=$dzn_qnty_p*$item_ratio_array[$result[csf("item_number_id")]];

			  $tot_grey_req_as_price_cost+=($tot_mkt_cost/$dzn_qnty_p)*$poqnty;
			//echo $paln_cut_qnty_array[$result[csf("color_size_table_id")]].'='.$tot_mkt_cost.'/'.$dzn_qnty_p.'<br>';
			//$tot_grey_req_as_price_cost_arr[$quotation_id]+=$tot_grey_req_as_price_cost;

			$tot_grey_req_as_pre_cost_arr[$result[csf("po_number")]]+=$tot_grey_req_as_pre_cost;
        }
    }
	       // $tot_grey_req_as_pre_cost_arr[$result[csf("po_number")]];
		  // echo $tot_grey_req_as_pre_cost;die;
		   //Price Quotation


		            $total_pre_cost=0;
					$total_booking_qnty_main=0;
					$total_booking_qnty_short=0;
					$total_booking_qnty_sample=0;
					$total_tot_bok_qty=0;
					$tot_balance=0;

					$booking_qnty_main=return_library_array( "select max(a.po_break_down_id) as po_break_down_id,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b, wo_booking_mst c where a.job_no =b.job_no_mst and    a.booking_no =c.booking_no  and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$order_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and a.is_short=2 and c.item_category=2 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty"); //a.job_no =c.job_no and

					$booking_qnty_main_wvn=return_library_array( "select max(a.po_break_down_id) as po_break_down_id,sum(a.fin_fab_qnty) as fin_fab_qnty  from wo_booking_dtls a, wo_po_break_down b, wo_booking_mst c where a.job_no =b.job_no_mst and    a.booking_no =c.booking_no  and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$order_id).") and a.booking_type =1  and a.is_short=2  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "fin_fab_qnty"); //a.job_no =c.job_no and


					$booking_qnty_short=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$order_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and c.item_category=2 and a.is_short=1 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
				

					$booking_qnty_short_wvn=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.fin_fab_qnty) as fin_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$order_id).") and a.booking_type =1  and a.is_short=1 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "fin_fab_qnty");


					$booking_qnty_sample=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c  where a.job_no =b.job_no_mst and  a.job_no=c.job_no and  a.booking_no=c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$order_id).") and a.booking_type =4 and c.fabric_source=$cbo_fabric_source and c.item_category=2  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");

					
					$booking_qnty_sample_wvn=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.fin_fab_qnty) as fin_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c  where a.job_no =b.job_no_mst and  a.job_no=c.job_no and  a.booking_no=c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$order_id).") and a.booking_type =4   and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "fin_fab_qnty");

					

				
					$sql_data=sql_select( "select max(a.id) as id,  a.po_number,max(a.pub_shipment_date) as pub_shipment_date,sum(a.plan_cut) as plan_cut  from wo_po_break_down a,wo_pre_cost_sum_dtls b,wo_pre_cost_mst c where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no and a.id in(".str_replace("'","",$order_id).") group by a.po_number order by id");

		

	?>
	<div style="width:890px; max-height:400px; overflow-y:scroll" id="scroll_body">
	<table width="870"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
	<?
	$k=0;$total_price_mkt_cost=0;
	foreach($sql_data  as $row)
	{
		if ($i%2==0)
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
		$k++;
		//tot_grey_req_as_price_cost_arr
		$quotation_id = return_field_value(" a.quotation_id as quotation_id"," wo_po_details_master a,wo_po_break_down b ","a.job_no=b.job_no_mst and b.po_number='".$row[csf('po_number')]."'","quotation_id");

		if($booking_qnty_main[$row[csf("id")]]>0){
			$booking_qnty_main=$booking_qnty_main[$row[csf("id")]];
		}else{
			$booking_qnty_main=$booking_qnty_main_wvn[$row[csf("id")]];
		}
		if($booking_qnty_short[$row[csf("id")]]>0){
			$booking_qnty_short=$booking_qnty_short[$row[csf("id")]];
		}else{
			$booking_qnty_short=$booking_qnty_short_wvn[$row[csf("id")]];
		}
		if($booking_qnty_sample[$row[csf("id")]]>0){
			$booking_qnty_sample=$booking_qnty_sample[$row[csf("id")]];
		}else{
			$booking_qnty_sample=$booking_qnty_sample_wvn[$row[csf("id")]];
		}

		?>
		<tr bgcolor="<? echo $bgcolor; ?>">
		    <td width="30"> <? echo $k; ?> </td>
		    <td width="120"><p><? echo $row[csf("po_number")]; ?></p> </td>
		    <td width="70" align="right"><? echo change_date_format($row[csf("pub_shipment_date")],"dd-mm-yyyy",'-'); ?> </td>
		    <td width="80" align="right"><? $total_price_mkt_cost+=$tot_grey_req_as_price_cost;echo number_format($tot_grey_req_as_price_cost,2);?> </td>
		    <td width="70" align="right"><?  echo number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]],2); $total_pre_cost+=$tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]];?> </td>
		    <td width="70" align="right"><? echo number_format($booking_qnty_main,2); $total_booking_qnty_main+=$booking_qnty_main;?> </td>
		    <td width="70" align="right"> <? echo number_format($booking_qnty_short,2); $total_booking_qnty_short+=$booking_qnty_short;?></td>
		    <td width="70" align="right"><? echo number_format($booking_qnty_sample,2); $total_booking_qnty_sample+=$booking_qnty_sample;?></td>
		    <td width="70" align="right">	<? $tot_bok_qty=$booking_qnty_main+$booking_qnty_short+$booking_qnty_sample; echo number_format($tot_bok_qty,2); $total_tot_bok_qty+=$tot_bok_qty;?> </td>
		    <td width="70" align="right"> <? $balance= def_number_format($total_price_mkt_cost-$tot_bok_qty,2,""); echo number_format($balance,2); $tot_balance+= $balance?></td>
		    <td width="70" align="right"> <?  $total_pre_cost_bal=$tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]-$tot_bok_qty;$tot_pre_cost_bal+=$total_pre_cost_bal;echo number_format($total_pre_cost_bal,2); ?></td>
		    <td width="">
		    <p>
		     <?
			$pre_cost= $tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]];

			if( $total_pre_cost_bal>0)
			{
				echo "Less Booking";
			}
			else if ($total_pre_cost_bal<0)
			{
				echo "Over Booking";
			}
			else if ($pre_cost==$tot_bok_qty)
			{
				echo "As Per";
			}
			else
			{
				echo "";
			}
			?>
		    </p>
			</td>
		</tr>
		<?
	}
	?>
	<tfoot>
	    <tr>
	    <td colspan="3">Total:</td>
	    <td align="right"><? echo number_format($total_price_mkt_cost,2); ?></td>
	     <td align="right"><? echo number_format($total_pre_cost,2); ?></td>
	    <td align="right"><? echo number_format($total_booking_qnty_main,2); ?></td>
	    <td align="right"><? echo number_format($total_booking_qnty_short,2); ?></td>
	    <td align="right"><? echo number_format($total_booking_qnty_sample,2); ?></td>
	     <td align="right"><? echo number_format($total_tot_bok_qty,2); ?></td>
	    <td align="right"><? echo number_format($tot_balance,2); ?></td>
	    <td align="right"><? echo number_format($tot_pre_cost_bal,2); ?></td>
	    </tr>
	    </tfoot>
	</table>
	</div>
	</div>
	 <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</body>
	<?

	exit();
}
//start here 

if($action=="show_fabric_booking_report_libas")
{
	
	extract($_REQUEST);
	$data = explode('**', $data);

	$txt_booking_no="'".str_replace("'","",$data[0])."'";
	$cbo_company_name=str_replace("'","",$data[1]);
	$txt_order_no_id=str_replace("'","",$data[2]);
	$cbo_fabric_natu=str_replace("'","",$data[3]);
	$cbo_fabric_source=str_replace("'","",$data[4]);
	$revised_no=str_replace("'","",$data[5]);

	$job_no=$data[6];

	$txt_job_no="'".implode(explode(",", $job_no), "','")."'";
	//echo $txt_job_no;	

	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1 and master_tble_id='$cbo_company_name'",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$company_library=return_library_array( "select id,company_name from   lib_company",'id','company_name');
	$size_library=return_library_array( "select id,size_name from   lib_size",'id','size_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$location_name_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$po_qnty_tot1=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	?>
	<div style="width:1330px" align="center">
    <?php
	$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7");
	list($nameArray_approved_row) = $nameArray_approved;
	$nameArray_approved_date = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
	list($nameArray_approved_date_row) = $nameArray_approved_date;
	$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
	list($nameArray_approved_comments_row) = $nameArray_approved_comments;
	$path = str_replace("'", "", $path);
	if ($path != "") {
		$path = $path;
	} else {
		$path = "../../";
	}

	?>										<!--    Header Company Information         -->
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black" >
           <tr>
               <td width="100">
               <img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1250">
                    <table width="100%" cellpadding="0" cellspacing="0"  >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php
								echo $company_library[$cbo_company_name];?>
                            </td>
                            <td rowspan="3" width="250">

                               <span style="font-size:18px"><b> Job No:&nbsp;&nbsp;<? echo trim($txt_job_no,"'"); ?></b></span><br/>

                               <span style="font-size:16px"><b> Repeat No:&nbsp;&nbsp;
	                               <?php $job_no_txt=trim($txt_job_no,"'"); $order_repeat_no=return_field_value( "order_repeat_no", "wo_po_details_master","job_no='$job_no_txt'");

	                                	if(!empty($order_repeat_no)) 
	                                	{
	                                		echo $order_repeat_no;
	                                	}
	                                	else
	                                	{
	                                		echo "NEW";
	                                	} 

	                                ?>
	                                </b>
	                            </span><br/>
                                <?
								 if($nameArray_approved_row[csf('approved_no')]>1)
								 {
								 ?>
								 <b> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></b>
                                  <br/>
								  Approved Date: <? echo $nameArray_approved_date_row[csf('approved_date')]; ?>
								  <?
								 }
							  	?>


                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
							if($txt_job_no!="")
							{
							 $location=return_field_value( "location_name", "wo_po_details_master","job_no='$txt_job_no'");
							}
							else
							{
							$location="";
							}

							foreach ($nameArray as $result)
                            {
							 	echo  $location_name_arr[$location];
                           		 ?><br>
                                Email Address: <? echo $result[csf('email')];?>
                                Website No: <? echo $result[csf('website')]; ?>

                                <?

                            }

                            ?>
                               </td>
                            </tr>
                            <tr>
	                            <td align="center" style="font-size:20px">
	                                <strong>Main Fabric Booking V2 &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
	                             </td>
                            </tr>
                      </table>
                </td>
            </tr>
       </table>
                <?
				$job_no='';
				$total_set_qnty=0;
				$colar_excess_percent=0;
				$cuff_excess_percent=0;
				$rmg_process_breakdown=0;
				$booking_po_id='';
				$po_quantity=0;

                $nameArray=sql_select( "select a.booking_no, a.booking_date,'' as remarks, a.supplier_id, '' as fabric_composition, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.po_break_down_id, a.colar_excess_percent, a.cuff_excess_percent, a.delivery_date, a.is_apply_last_update, a.fabric_source, a.rmg_process_breakdown, a.insert_date, a.pay_mode, a.update_date,'' as uom, b.job_no, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.total_set_qnty, b.style_description, b.season_buyer_wise AS season, b.product_dept, b.product_code, b.pro_sub_dep, b.dealing_marchant, a.booking_percent from wo_booking_mst_hstry a, wo_po_details_master b where a.job_no=b.job_no and a.booking_no=$txt_booking_no and a.approved_no=$revised_no" );
               
				foreach ($nameArray as $result)
				{
					$total_set_qnty=$result[csf('total_set_qnty')];
					$colar_excess_percent=$result[csf('colar_excess_percent')];
				    $cuff_excess_percent=$result[csf('cuff_excess_percent')];
					$rmg_process_breakdown=$result[csf('rmg_process_breakdown')];
					$booking_uom=$result[csf('uom')];
					$pay_modes=$result[csf('pay_mode')];
					$remarks=$result[csf('remarks')];
					$fabric_composition=$result[csf('fabric_composition')];
					$po_no="";
					$shipment_date="";
					$sql_po= "select grouping,file_no,po_number, MIN(pub_shipment_date) pub_shipment_date, sum(po_quantity) as po_quantity from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number,grouping,file_no";
					$data_array_po=sql_select($sql_po);
					foreach ($data_array_po as $row_po)
					{
						$po_no.=$row_po[csf('po_number')].", ";
						$shipment_date.=change_date_format($row_po[csf('pub_shipment_date')],'dd-mm-yyyy','-').", ";
						$po_quantity+=$row_po[csf('po_quantity')];
						$grouping[$row_po[csf('grouping')]]=$row_po[csf('grouping')];
						$file_no[$row_po[csf('file_no')]]=$row_po[csf('file_no')];
					}
					$lead_time="";
					if($db_type==0)
					{
						$sql_lead_time= "select DATEDIFF(pub_shipment_date,po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")";
					}
					if($db_type==2)
					{
						$sql_lead_time= "select (pub_shipment_date-po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")";
					}
					$data_array_lead_time=sql_select($sql_lead_time);
					foreach ($data_array_lead_time as $row_lead_time)
					{
						$lead_time.=$row_lead_time[csf('date_diff')].",";
					}
					$po_received_date="";
					$sql_po_received_date= "select MIN(po_received_date) as po_received_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")";
					$data_array_po_received_date=sql_select($sql_po_received_date);
					foreach ($data_array_po_received_date as $row_po_received_date)
					{
						$po_received_date=change_date_format($row_po_received_date[csf('po_received_date')],'dd-mm-yyyy','-');
					}


					$booking_po_id=$result[csf('po_break_down_id')];



					$sql_po= "select po_number,MIN(pub_shipment_date) pub_shipment_date, MIN(insert_date) as insert_date,shiping_status from wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number,shiping_status";
					$data_array_po=sql_select($sql_po);
					foreach ($data_array_po as $rows)
					{
						$daysInHand.=(datediff('d',date('d-m-Y',time()),$rows[csf('pub_shipment_date')])-1).",";
						$booking_date=$result[csf('update_date')];
						if($booking_date=="" || $booking_date=="0000-00-00 00:00:00")
						{
							$booking_date=$result[csf('insert_date')];
						}
						$WOPreparedAfter.=(datediff('d',$rows[csf('insert_date')],$booking_date)-1).",";

						if($rows[csf('shiping_status')]==1)
						{
						$shiping_status.= "FP".",";
						}
						else if($rows[csf('shiping_status')]==2)
						{
						$shiping_status.= "PS".",";
						}
						else if($rows[csf('shiping_status')]==3)
						{
						$shiping_status.= "FS".",";
						}

					}

					if($db_type==2)
					{
						$group_concat_all=" listagg(cast(b.grouping as varchar2(4000)),',') within group (order by b.grouping) as grouping,
		                                    listagg(cast(b.file_no as varchar2(4000)),',') within group (order by b.file_no) as file_no  ";
					}
					else { $group_concat_all="group_concat(b.grouping) as grouping, group_concat(b.file_no) as file_no";}

					$data_array3=sql_select("select a.job_no, a.company_name, a.buyer_name, $group_concat_all from wo_po_details_master a, wo_po_break_down b where b.id in (".$result[csf('po_break_down_id')].") and a.job_no=b.job_no_mst group by a.job_no,a.company_name,a.buyer_name");

					if($pay_modes==5 || $pay_modes==3 )
					{
						$supplier_name=$company_library[$result[csf('supplier_id')]];
					}
					else
					{
						$supplier_name=$supplier_name_arr[$result[csf('supplier_id')]];
					}
					?>
			       <table width="100%" style="border:1px solid black" >
			            <tr>
			                <td colspan="6" valign="top" style="font-size:18px; color:#F00"><? if($result[csf('is_apply_last_update')]==2 && str_replace("'","",$id_approved_id) !=1){echo "Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.  Contact to ".$marchentrArr[$result[csf('dealing_marchant')]]; } else{ echo "";} ?></td>
			            </tr>
			            <tr>
			                <td width="100"><span style="font-size:18px"><b>Buyer/Agent Name</b></span></td>
			                <td width="110">:&nbsp;<span style="font-size:18px"><b><? echo $buyer_name_arr[$result[csf('buyer_name')]]; ?></b></span></td>
			                <td width="100"><span style="font-size:12px"><b>Dept.</b></span></td>
			                <td width="110">:&nbsp;<? echo $product_dept[$result[csf('product_dept')]] ; if($result[csf('product_code')] !=""){ echo " (".$result[csf('product_code')].")";} if($result[csf('pro_sub_dep')] !=0){ echo " (".$pro_sub_dept_array[$result[csf('pro_sub_dep')]].")";}?></td>
			                <td width="100"><span style="font-size:12px"><b>Order Qnty</b></span></td>
			                <td width="110">:&nbsp;
							<?  echo $po_qnty_tot1." ".$unit_of_measurement[$result[csf('order_uom')]] ; ?>
			                </td>
			            </tr>
			            <tr>

			                <td style="font-size:12px"><b>Garments Item</b></td>
			                <td>:&nbsp;
							<?
							$gmts_item_name="";
							$gmts_item=explode(',',$result[csf('gmts_item_id')]);
							for($g=0;$g<=count($gmts_item); $g++)
							{
								$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
							}
							echo rtrim($gmts_item_name,',');
							?>
			                </td>
			                <td style="font-size:12px"><b>Booking Date</b></td>
			                <td>:&nbsp;
							<?
							$booking_date=$result[csf('booking_date')];
							echo change_date_format($booking_date,'dd-mm-yyyy','-','');
							?>&nbsp;&nbsp;&nbsp;</td>
			                <td style="font-size:18px"><b>Style Ref.</b>   </td>
			                <td style="font-size:18px">:&nbsp;<b><? echo $result[csf('style_ref_no')];?> </b>   </td>
			            </tr>
			             <tr>
			                <td  style="font-size:12px"><b>Style Des.</b></td>
			                <td  >:&nbsp;<? echo $result[csf('style_description')]; $job_no= $result[csf('job_no')];?></td>
			                <td style="font-size:12px"><b>Lead Time </b>   </td>
			                <td>:&nbsp;<?  echo rtrim($lead_time,",");?> </td>
			                <td style="font-size:12px"><b>Dealing Merchant</b></td>
			                <td>:&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
			            </tr>

			            <tr>
			                <td style="font-size:12px"><b>Supplier Name</b>   </td>
			                <td>:&nbsp;<? echo $supplier_name;?>    </td>
			                <td style="font-size:12px"><b>Fab. Delv. Date</b></td>
			               	<td>:&nbsp;<? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
			                <td style="font-size:18px"><b>Booking No </b>   </td>
			                <td style="font-size:18px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b><? echo "(".$fabric_source[$result[csf('fabric_source')]].")"?></td>



			            </tr>
			            <tr>
			                <td style="font-size:12px"><b>Season</b></td>
			                <td>:&nbsp;<? echo $season_name_arr[$result[csf('season')]]; ?></td>
			                <td  style="font-size:12px"><b>Attention</b></td>
			                <td  >:&nbsp;<? echo $result[csf('attention')]; ?></td>
			                <td  style="font-size:12px"><b>PO Received Date</b></td>
			                <td  >:&nbsp;<? echo $po_received_date; ?></td>



			            </tr>
			           <tr>
			               <td  style="font-size:18px"><b>Order No</b></td>
			               <td style="font-size:18px" colspan="3">:&nbsp;<b><? echo rtrim($po_no,", "); ?></b></td>
			               <td>Internal Ref</td>
			               <td>:&nbsp;<? echo implode($grouping, ",") ?></td>
			            </tr>
			            <tr>
			               <td  style="font-size:12px"><b>Shipment Date</b></td>
			               <td colspan="3"> :&nbsp;<? echo rtrim($shipment_date,", "); ?></td>               
			               <td>File NO.</td>
			               <td>:&nbsp;<? echo implode($file_no, ",") ?></td>

			            </tr>

			            </tr>
			            <tr>
			               <td style="font-size:12px"><b>WO Prepared After</b></td>
			               <td> :&nbsp;<? echo rtrim($WOPreparedAfter,',').' Days' ?></td>

			               <td style="font-size:12px"><b>Ship.days in Hand</b></td>
			               <td> :&nbsp;<? echo rtrim($daysInHand,',').' Days'?></td>

			               <td style="font-size:12px"><b>Ex-factory status</b></td>
			               <td> :&nbsp;<? echo rtrim($shiping_status,','); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Booking Percent :</b><? echo $booking_percent=$result[csf('booking_percent')]."%"; ?></td>

			            </tr>
			             <tr>
			            	<td style="font-size:12px"><b>Fabric Composition</b></td>
			            	  <td style="font-size:18px" colspan="5"> :&nbsp;<? echo $fabric_composition;?></td>
			            </tr>
			            <tr>
			            	<td style="font-size:12px"><b>Remarks</b></td>
			            	<td colspan="5"> :&nbsp;<? echo $remarks;?></td>
			            </tr>
			        </table>
           			<?
				}


			if($cbo_fabric_source==1 || $cbo_fabric_source==3)
			{
				$nameArray_size=sql_select( "select  size_number_id,min(id) as id, min(size_order) as size_order from wo_po_color_size_breakdown where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  job_no_mst=$txt_job_no and is_deleted=0 and status_active=1 group by size_number_id order by size_order");

		   ?>
            <table width="100%" >
		    <tr>
            <td width="800">
                <div id="div_size_color_matrix" style="float:left; max-width:1000;">
            	<fieldset id="div_size_color_matrix" style="max-width:1000;">
 				<legend>Size and Color Breakdown</legend>
 				<table  class="rpt_table"  border="1" align="left" cellpadding="0" width="750" cellspacing="0" rules="all" >
                    <tr>
                        <td style="border:1px solid black"><strong>Color/Size</strong></td>
                    <?
						foreach($nameArray_size  as $result_size)
                        {	     ?>
                        	<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
                    <?	}    ?>
                        <td style="border:1px solid black; width:130px" align="center"><strong> Total Order Qty(Pcs)</strong></td>
                        <td style="border:1px solid black; width:80px" align="center"><strong> Excess %</strong></td>
                        <td style="border:1px solid black; width:130px" align="center"><strong> Total Plan Cut Qty(Pcs)</strong></td>
                    </tr>
                    <?
					$color_size_order_qnty_array=array();
					$color_size_qnty_array=array();
					$size_tatal=array();
					$size_tatal_order=array();
					for($c=0;$c<count($gmts_item); $c++)
				    {
					$item_size_tatal=array();
					$item_size_tatal_order=array();
					$item_grand_total=0;
					$item_grand_total_order=0;
					$nameArray_color=sql_select( "select  color_number_id,min(id) as id,min(color_order) as color_order from wo_po_color_size_breakdown where  item_number_id=$gmts_item[$c] and po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by color_number_id  order by color_order");
					?>
                    <tr>
                    <td style="border:1px solid black" colspan="<? echo count($nameArray_size)+3;?>"><strong><? echo $garments_item[$gmts_item[$c]];?></strong></td>

                    </tr>
                    <?
					foreach($nameArray_color as $result_color)
                    {
                    ?>
                    <tr>
                        <td align="center" style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; // echo $row_num_tr; ?></td>
                        <?
						$color_total=0;
						$color_total_order=0;

						foreach($nameArray_size  as $result_size)
						{
						$nameArray_color_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as  order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$result_color[csf('color_number_id')]."  and item_number_id=$gmts_item[$c] and  status_active=1 and is_deleted =0");
						foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                        {
                        ?>
                            <td style="border:1px solid black; text-align:right">
							<?
								if($result_color_size_qnty[csf('plan_cut_qnty')]!= "")
								{
									 echo number_format($result_color_size_qnty[csf('order_quantity')],0);
									 $color_total += $result_color_size_qnty[csf('plan_cut_qnty')] ;
									 $color_total_order += $result_color_size_qnty[csf('order_quantity')] ;
									 $item_grand_total+=$result_color_size_qnty[csf('plan_cut_qnty')];
									 $item_grand_total_order+=$result_color_size_qnty[csf('order_quantity')];
								     $grand_total +=$result_color_size_qnty[csf('plan_cut_qnty')];
									 $grand_total_order +=$result_color_size_qnty[csf('order_quantity')];

									 $color_size_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
									 $color_size_order_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
									 if (array_key_exists($result_size[csf('size_number_id')], $size_tatal))
									 {
											$size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
											$size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
									 }
									 else
									 {
										$size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
										$size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
									 }
									 if (array_key_exists($result_size[csf('size_number_id')], $item_size_tatal))
									 {
											$item_size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
											$item_size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
									 }
									 else
									 {
										$item_size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
										$item_size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
									 }
								}
								else echo "0";
							 ?>
							</td>

                    <?
						}
                        }
                        ?>
                          <td style="border:1px solid black; text-align:right"><?  echo number_format(round($color_total_order),0); ?></td>

                         <td style="border:1px solid black; text-align:right"><? $excexss_per=($color_total-$color_total_order)/$color_total_order*100; echo number_format($excexss_per,2)." %"; ?>
                         </td>
                        <td style="border:1px solid black; text-align:right"><? echo number_format(round($color_total),0); ?></td>
                    </tr>
                    <?
                    }
					?>

                        <td align="center" style="border:1px solid black"><strong>Sub Total</strong></td>
                        <?
						foreach($nameArray_size  as $result_size)
                        {
                        ?>
                        <td style="border:1px solid black;  text-align:right"><? echo $item_size_tatal_order[$result_size[csf('size_number_id')]];  ?></td>
                        <?
                        }
                        ?>
                        <td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($item_grand_total_order),0); ?></td>
                        <td  style="border:1px solid black;  text-align:right"><? $excess_item_gra_tot=($item_grand_total-$item_grand_total_order)/$item_grand_total_order*100; echo number_format($excess_item_gra_tot,2)." %"; ?></td>
                        <td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($item_grand_total),0); ?></td>
                    </tr>
                    <?
					}
                    ?>
                     <tr>
                        <td style="border:1px solid black" align="center" colspan="<? echo count($nameArray_size)+3; ?>"><strong>&nbsp;</strong></td>
                        </tr>
                    <tr>
                    <tr>
                        <td align="center" style="border:1px solid black"><strong>Grand Total</strong></td>
                        <?
						foreach($nameArray_size  as $result_size)
                        {
                        ?>
                        <td style="border:1px solid black;  text-align:right"><? echo $size_tatal_order[$result_size[csf('size_number_id')]];  ?></td>
                        <?
                        }
                        ?>
                        <td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($grand_total_order),0); ?></td>
                        <td  style="border:1px solid black;  text-align:right"><? $excess_gra_tot= ($grand_total-$grand_total_order)/$grand_total_order*100; echo number_format($excess_gra_tot,2)." %"; ?></td>
                        <td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($grand_total),0); ?></td>
                    </tr>
                </table>
                </fieldset>
                </div>
                </td>
                <td width="200" valign="top" align="left">
                <div id="div_size_color_matrix" style="float:left;">
                <?
				$rmg_process_breakdown_arr=explode('_',$rmg_process_breakdown)
				?>
            	 	<fieldset id="" >
 				<legend>RMG Process Loss % </legend>
            	<table width="180" class="rpt_table" border="1" rules="all">
                <?
				if(number_format($rmg_process_breakdown_arr[8],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Cut Panel rejection <!-- Extra Cutting % breack Down 8-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[8],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[2],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Chest Printing <!-- Printing % breack Down 2-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[2],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[10],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Neck/Sleeve Printing <!-- New breack Down 10-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[10],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[1],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Embroidery   <!-- Embroidery  % breack Down 1-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[1],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[4],2)>0)
				{
				?>
                <tr>
                <td width="130">
                 Sewing /Input<!-- Sewing % breack Down 4-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[4],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[3],2)>0)
				{
				?>
                <tr>
                <td width="130">
                 Garments Wash <!-- Washing %breack Down 3-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[3],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[15],2)>0)
				{
				?>
                <tr>
                <td width="130">
                 Gmts Finishing <!-- Washing %breack Down 3-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[15],2);
				?>
                </td>
                </tr>
                <?
                }
				if(number_format($rmg_process_breakdown_arr[11],2)>0)
				{
				?>
                <tr>
                <td width="130">
                 Others <!-- New breack Down 11-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[11],2);
				?>
                </td>
                </tr>
                <?
                }
				$gmts_pro_sub_tot=$rmg_process_breakdown_arr[8]+$rmg_process_breakdown_arr[2]+$rmg_process_breakdown_arr[10]+$rmg_process_breakdown_arr[1]+$rmg_process_breakdown_arr[4]+$rmg_process_breakdown_arr[3]+$rmg_process_breakdown_arr[11]+$rmg_process_breakdown_arr[15];
				if($gmts_pro_sub_tot>0)
				{
				?>
                <tr>
                <td width="130">
                Sub Total <!-- New breack Down 11-->
                </td>
                <td align="right">
                <?

				echo number_format($gmts_pro_sub_tot,2);
				?>
                </td>
                </tr>
                <?
				}
				?>
                </table>
                </fieldset>


                <fieldset id="" >
 				<legend>Fabric Process Loss % </legend>
            	<table width="180" class="rpt_table" border="1" rules="all">
                 <?
				if(number_format($rmg_process_breakdown_arr[6],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Knitting  <!--  Knitting % breack Down 6-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[6],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[12],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Yarn Dyeing  <!--  New breack Down 12-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[12],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[5],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Dyeing & Finishing  <!-- Finishing % breack Down 5-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[5],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[13],2)>0)
				{
				?>
                <tr>
                <td width="130">
                All Over Print <!-- new  breack Down 13-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[13],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[14],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Lay Wash (Fabric) <!-- new  breack Down 14-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[14],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[7],2)>0)
				{
				?>
                 <tr>
                <td width="130">
                Dying   <!-- breack Down 7-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[7],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[0],2)>0)
				{
				?>
                <tr>
                <td width="130">
                Cutting (Fabric) <!-- Cutting % breack Down 0-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[0],2);
				?>
                </td>
                </tr>
                <?
				}
				if(number_format($rmg_process_breakdown_arr[9],2)>0)
				{
				?>
                <tr>
                <td width="130">
               Others  <!-- Others% breack Down 9-->
                </td>
                <td align="right">
                <?
				echo number_format($rmg_process_breakdown_arr[9],2);
				?>
                </td>
                </tr>
                <?
				}
				$fab_proce_sub_tot=$rmg_process_breakdown_arr[6]+$rmg_process_breakdown_arr[12]+$rmg_process_breakdown_arr[5]+$rmg_process_breakdown_arr[13]+$rmg_process_breakdown_arr[14]+$rmg_process_breakdown_arr[7]+$rmg_process_breakdown_arr[0]+$rmg_process_breakdown_arr[9];
				if(fab_proce_sub_tot>0)
				{
				?>
                <tr>
                <td width="130">
                Sub Total  <!-- Others% breack Down 9-->
                </td>
                <td align="right">
                <?

				echo number_format($fab_proce_sub_tot,2);
				?>
                </td>
                </tr>
                <?
				}
				if($gmts_pro_sub_tot+$fab_proce_sub_tot>0)
				{
				?>
                 <tr>
                <td width="130">
                Grand Total  <!-- Others% breack Down 9-->
                </td>
                <td align="right">
                <?
				echo number_format($gmts_pro_sub_tot+$fab_proce_sub_tot,2);
				?>
                </td>
                </tr>
                <?
				}
				?>
           </table>
           </fieldset>
           </div>
                </td>
            <td width="330" valign="top" align="left">
            <?
			$nameArray_imge =sql_select("SELECT image_location FROM common_photo_library where master_tble_id='$job_no' and file_type=1");
			?>
            <div id="div_size_color_matrix" style="float:left;">
            	<fieldset id="" >
 				<legend>Image </legend>
            	<table width="310">
                <tr>
                <?
				$img_counter = 0;
                foreach($nameArray_imge as $result_imge)
				{
				    if($path=="")
                    {
                    $path='../../';
                    }

					?>
					<td>
                        <img src="<? echo $path.$result_imge[csf('image_location')]; ?>" width="90" height="100" border="2" />
					</td>
					<?

					$img_counter++;
				}
				?>
                </tr>
           </table>
           </fieldset>
           </div>
          </td>
        </tr>
       </table>
        <?
			}// if($cbo_fabric_source==1) end

	  ?>
      <br/>

      <!--  Here will be the main portion  -->
     <?
	 $costing_per="";
	 $costing_per_qnty=0;
	 $costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='$job_no'");
	 		if($costing_per_id==1)
			{
				$costing_per="1 Dzn";
				$costing_per_qnty=12;
			}
			if($costing_per_id==2)
			{
				$costing_per="1 Pcs";
				$costing_per_qnty=1;
			}
			if($costing_per_id==3)
			{
				$costing_per="2 Dzn";
				$costing_per_qnty=24;

			}
			if($costing_per_id==4)
			{
				$costing_per="3 Dzn";
				$costing_per_qnty=36;

			}
			if($costing_per_id==5)
			{
				$costing_per="4 Dzn";
				$costing_per_qnty=48;

			}
			$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no='$job_no'");;

	 ?>

	     <?
	if($cbo_fabric_source==1  || $cbo_fabric_source==3)
	{
		$nameArray_gmts_sizes= sql_select("select a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight, b.dia_width, c.size_number_id,c.size_order FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls_hstry d
		WHERE a.job_no=b.job_no and
		a.id=b.pre_cost_fabric_cost_dtls_id and
		c.job_no_mst=a.job_no and
		c.id=b.color_size_table_id and
		b.po_break_down_id=d.po_break_down_id and
		b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
		d.booking_no =$txt_booking_no and
		d.status_active=1 and
		d.approved_no=$revised_no and 
		d.is_deleted=0
		group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,c.size_number_id,c.size_order order by c.size_order");
		$GmtsSizesArr=array();
		foreach($nameArray_gmts_sizes as $sizes_row){
			$GmtsSizesArr[$sizes_row[csf('body_part_id')]][$sizes_row[csf('color_type_id')]][$sizes_row[csf('construction')]][$sizes_row[csf('composition')]][$sizes_row[csf('gsm_weight')]][$sizes_row[csf('dia_width')]][$sizes_row[csf('size_number_id')]]=$size_library[$sizes_row[csf('size_number_id')]];
		}


		$nameArray_fabric_description= sql_select("select min(a.id) as fabric_cost_dtls_id, max(a.lib_yarn_count_deter_id) as determin_id,a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,min(a.width_dia_type) as width_dia_type, b.dia_width, avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent , avg(b.requirment) as requirment FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls_hstry d
		WHERE a.job_no=b.job_no and
		a.id=b.pre_cost_fabric_cost_dtls_id and
		c.job_no_mst=a.job_no and
		c.id=b.color_size_table_id and
		b.po_break_down_id=d.po_break_down_id and
		b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
		d.booking_no =$txt_booking_no and
		d.status_active=1 and
		d.approved_no=$revised_no and 
		d.is_deleted=0
		group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width order by fabric_cost_dtls_id,a.body_part_id,b.dia_width");
		 ?>

	     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
	     <tr align="center">
	     <th colspan="3" align="left">Body Part</th>
	        <?
			foreach($nameArray_fabric_description  as $result_fabric_description)
			{
				if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";
				else         		               echo "<td  colspan='2'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";
			}
			?>
	        <td  rowspan="10" width="50"><p>Total  Finish Fabric (<? echo $unit_of_measurement[$booking_uom];?>)</p></td>
	        <td  rowspan="10" width="50"><p>Total Grey Fabric (<? echo $unit_of_measurement[$booking_uom];?>)</p></td>
	        <td  rowspan="10" width="50"><p>Process Loss % </p></td>
	       </tr>
	     <tr align="center"><th colspan="3" align="left">Color Type</th>
	        <?
			foreach($nameArray_fabric_description  as $result_fabric_description)
			{
				if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";
				else         		               echo "<td  colspan='2'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";
			}
			?>
	        <!--<td  rowspan="8" width="50"><p>Total  Finish Fabric (KG)</p></td> <td  rowspan="8" width="50"><p>Total Grey Fabric (KG)</p></td>-->
	             <!--<td  rowspan="7" width="50"><p>Process Loss % </p></td>-->
	       </tr>
	        <tr align="center"><th colspan="3" align="left">Fabric Construction</th>
	        <?
			foreach($nameArray_fabric_description  as $result_fabric_description)
			{
				if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='2'>&nbsp</td>";
				else         		               echo "<td  colspan='2'>". $result_fabric_description[csf('construction')]."</td>";
			}
			?>


	       </tr>
	        <tr align="center"><th   colspan="3" align="left">Fabric Composition / Yarn Type</th>
	        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
			{
				if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='2' >&nbsp</td>";
				else         		               echo "<td colspan='2' >".$result_fabric_description[csf('composition')]."</td>";
			}
			?>

	       </tr>
	       <tr align="center"><th  colspan="3" align="left">GSM</th>
	        <?
			foreach($nameArray_fabric_description  as $result_fabric_description)
			{
				if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='2'>&nbsp</td>";
				else         		       echo "<td colspan='2' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";
			}
			?>

	       </tr>
	       <tr align="center"><th   colspan="3" align="left">Dia/Width (Inch)</th>
	        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
			{
				if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='2'>&nbsp</td>";
				else         		              echo "<td colspan='2' align='center'>".$result_fabric_description[csf('dia_width')].",".$fabric_typee[$result_fabric_description[csf('width_dia_type')]]."</td>";
			}
			?>

	       </tr>
	        <tr align="center"><th   colspan="3" align="left">Gmts Sizes</th>
	        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
			{
				$sizeArr=$GmtsSizesArr[$result_fabric_description[csf('body_part_id')]][$result_fabric_description[csf('color_type_id')]][$result_fabric_description[csf('construction')]][$result_fabric_description[csf('composition')]][$result_fabric_description[csf('gsm_weight')]][$result_fabric_description[csf('dia_width')]];
				$Gsize=implode(",",$sizeArr);


				if( $Gsize == "")   echo "<td colspan='2'>&nbsp</td>";
				else         		              echo "<td colspan='2' align='center'>".$Gsize."</td>";
			}
			?>

	       </tr>
	       <tr align="center"><th   colspan="3" align="left">Consumption For <? echo $costing_per; ?></th>
	        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
			{
				if( $result_fabric_description[csf('requirment')] == "")   echo "<td colspan='2'>&nbsp</td>";
				else         		              echo "<td colspan='2' align='center'>Fin: ".number_format($result_fabric_description[csf('cons')],4).", Grey: ".number_format($result_fabric_description[csf('requirment')],4)."</td>";
			}
			?>

	       </tr>
	       <tr>
	       <th  colspan="<? echo  count($nameArray_fabric_description)*2+3; ?>" align="left" style="height:30px">&nbsp;</th>
	       </tr>

	       <tr>
	            <!--<th  width="120" align="left">Gmts. Color</th>-->
	            <th  width="120" align="left">Fabric Color</th>
	            <th  width="120" align="left">Body Color</th>
	            <th  width="120" align="left">Lab Dip No</th>
	        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
			{
				  echo "<th width='50'>Finish</th><th width='50' >Grey</th>";
			}
			?>

	       </tr>
	       <?

			  $gmt_color_library=array();
			  $gmt_color_data=sql_select("select b.gmts_color_id, b.contrast_color_id
			  FROM
			  wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b
			  WHERE a.id=b.pre_cost_fabric_cost_dtls_id and a.fab_nature_id=$cbo_fabric_natu and a.fabric_source =$cbo_fabric_source and
			  a.job_no ='$job_no'");
			  foreach( $gmt_color_data as $gmt_color_row)
			  {
				$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]][$gmt_color_row[csf("gmts_color_id")]]=$color_library[$gmt_color_row[csf("gmts_color_id")]];

			  }

		        $grand_total_fin_fab_qnty=0;
				$grand_total_grey_fab_qnty=0;
				$grand_totalcons_per_finish=0;
				$grand_totalcons_per_grey=0;

				$color_wise_wo_sql=sql_select("select fabric_color_id
											  FROM
											  wo_booking_dtls
											  WHERE
											  booking_no =$txt_booking_no and
											  status_active=1 and
	                                          is_deleted=0
											  group by fabric_color_id");
			foreach($color_wise_wo_sql as $color_wise_wo_result)
		    {
			?>
				<tr>

	            <td  width="120" align="left">
				<?
				echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];


				?>
	            </td>
	            <td>
	            <?
				echo implode(",",$gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]]);
				?>
	            </td>
	            <td  width="120" align="left">
				<?
				$lapdip_no="";
				$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."");
				if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no;
				?>
	            </td>
	            <?
				$total_fin_fab_qnty=0;
				$total_grey_fab_qnty=0;

				foreach($nameArray_fabric_description as $result_fabric_description)
			    {
					if($db_type==0)
					{
						$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls_hstry d
						WHERE a.job_no=b.job_no and
						a.id=b.pre_cost_fabric_cost_dtls_id and
						c.job_no_mst=a.job_no and
						c.id=b.color_size_table_id and
						b.po_break_down_id=d.po_break_down_id and
						b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
						d.booking_no =$txt_booking_no and
						d.approved_no=$revised_no and 
						a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
						a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
						a.construction='".$result_fabric_description[csf('construction')]."' and
						a.composition='".$result_fabric_description[csf('composition')]."' and
						a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
						b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
						d.fabric_color_id='".$color_wise_wo_result[csf('fabric_color_id')]."' and
						d.status_active=1 and
						d.is_deleted=0 ");
					}
					if($db_type==2)
					{
						$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls_hstry d
						WHERE a.job_no=b.job_no and
						a.id=b.pre_cost_fabric_cost_dtls_id and
						c.job_no_mst=a.job_no and
						c.id=b.color_size_table_id and
						b.po_break_down_id=d.po_break_down_id and
						b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
						d.booking_no =$txt_booking_no and
						d.approved_no=$revised_no and 
						a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
						a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
						a.construction='".$result_fabric_description[csf('construction')]."' and
						a.composition='".$result_fabric_description[csf('composition')]."' and
						a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
						b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
						nvl(d.fabric_color_id,0)=nvl('".$color_wise_wo_result[csf('fabric_color_id')]."',0) and
						d.status_active=1 and
						d.is_deleted=0 ");
					}

					list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
				?>
				<td width='50' align='right'>
				<?
				if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
				{
					echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;
					$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
				}
				?>
	            </td>
	            <td width='50' align='right' >
				<?
				if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
				{
					if($process_loss_method==1)
					{
						$process_loss_percent=(($color_wise_wo_result_qnty[csf('grey_fab_qnty')]-$color_wise_wo_result_qnty[csf('fin_fab_qnty')])/$color_wise_wo_result_qnty[csf('fin_fab_qnty')])*100;
					}

					if($process_loss_method==2)
					{
						$process_loss_percent=(($color_wise_wo_result_qnty[csf('grey_fab_qnty')]-$color_wise_wo_result_qnty[csf('fin_fab_qnty')])/$color_wise_wo_result_qnty[csf('grey_fab_qnty')])*100;
					}
					echo number_format($process_loss_percent,2)."%<br>";
					echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2);
					$total_grey_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];


				}
				?>
	            </td>
	            <?
				}
				?>
	            <td align="right"><? echo number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
	            <td align="right"><? echo number_format($total_grey_fab_qnty,2); $grand_total_grey_fab_qnty+=$total_grey_fab_qnty;?></td>

	            <td align="right">
	            <?
				if($process_loss_method==1)
				{
					$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_fin_fab_qnty)*100;
				}

				if($process_loss_method==2)
				{
					$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_grey_fab_qnty)*100;
				}
				echo number_format($process_percent,2);

				?>
	            </td>
	            </tr>
	         <?
			}
			?>
	        <tr style=" font-weight:bold">
	        <th  width="120" align="left">&nbsp;</th>
	        <td  width="120" align="left">&nbsp;</td>
	        <td  width="120" align="left"><strong>Total</strong></td>
	        <?
				foreach($nameArray_fabric_description as $result_fabric_description)
			    {
					$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls_hstry d
													WHERE a.job_no=b.job_no and
													a.id=b.pre_cost_fabric_cost_dtls_id and
													c.job_no_mst=a.job_no and
													c.id=b.color_size_table_id and
													b.po_break_down_id=d.po_break_down_id and
													b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
													d.booking_no =$txt_booking_no and
													d.approved_no=$revised_no and
													a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
													a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
													a.construction='".$result_fabric_description[csf('construction')]."' and
													a.composition='".$result_fabric_description[csf('composition')]."' and
													a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
													b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
													d.status_active=1 and
													d.is_deleted=0
													");
					list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
				?>
				<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;?></td><td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2);?></td>
	            <?
				}
				?>
	            <td align="right"><? echo number_format($grand_total_fin_fab_qnty,2);?></td>
	            <td align="right"><? echo number_format($grand_total_grey_fab_qnty,2);?></td>
	            <td align="right">
	            <?
	            if($process_loss_method==1)// markup
				{
					$totalprocess_percent=(($grand_total_grey_fab_qnty-$grand_total_fin_fab_qnty)/$grand_total_fin_fab_qnty)*100;
				}

				if($process_loss_method==2) //margin
				{
					$totalprocess_percent=(($grand_total_grey_fab_qnty-$grand_total_fin_fab_qnty)/$grand_total_grey_fab_qnty)*100;
				}
				echo number_format($totalprocess_percent,2);
				?>
	            </td>
	            </tr>
	            <tr style="font-weight:bold">
	        <!--<td  width="120" align="left">&nbsp;</td>-->
	        <th  width="120" align="left">&nbsp;</th>
	        <td  width="120" align="left">&nbsp;</td>
	        <td  width="120" align="left"><strong>Consumption For <? echo $costing_per; ?></strong></td>
	        <?
				foreach($nameArray_fabric_description as $result_fabric_description)
			    {
					$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls_hstry d
													WHERE a.job_no=b.job_no and
													a.id=b.pre_cost_fabric_cost_dtls_id and
													c.job_no_mst=a.job_no and
													c.id=b.color_size_table_id and
													b.po_break_down_id=d.po_break_down_id and
													b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
													d.booking_no =$txt_booking_no and
													d.approved_no=$revised_no and
													a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
													a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
													a.construction='".$result_fabric_description[csf('construction')]."' and
													a.composition='".$result_fabric_description[csf('composition')]."' and
													a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
													b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
													d.status_active=1 and
													d.is_deleted=0
													");
					list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty;

				?>
				<td width='50' align='right'><?  //echo number_format(($color_wise_wo_result_qnty['fin_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4) ;?></td><td width='50' align='right' > <? //echo number_format(($color_wise_wo_result_qnty['grey_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4);?></td>
	            <?
				}
				?>
	            <td align="right">
				<?
				//echo $grand_total_fin_fab_qnty;
				echo number_format(($grand_total_fin_fab_qnty/$grand_total)*($total_set_qnty*$costing_per_qnty),4);
				$grand_total_fin_fab_qnty_dzn=number_format(($grand_total_fin_fab_qnty/$grand_total)*($total_set_qnty*$costing_per_qnty),4);
				?>
	            </td>
	            <td align="right"><? echo number_format(($grand_total_grey_fab_qnty/$grand_total)*($total_set_qnty*$costing_per_qnty),4);$grand_total_grey_fab_qnty_dzn=number_format(($grand_total_grey_fab_qnty/$grand_total)*($total_set_qnty*$costing_per_qnty),4)?></td>
	            <td align="right">
	            <?
	            if($process_loss_method==1)
				{
					$totalprocess_percent_dzn=(($grand_total_grey_fab_qnty_dzn-$grand_total_fin_fab_qnty_dzn)/$grand_total_fin_fab_qnty_dzn)*100;
				}

				if($process_loss_method==2)
				{
					$totalprocess_percent_dzn=(($grand_total_grey_fab_qnty_dzn-$grand_total_fin_fab_qnty_dzn)/$grand_total_grey_fab_qnty_dzn)*100;
				}
				echo number_format($totalprocess_percent_dzn,2);
				?>
	            </td>
	            </tr>
	    </table>
	    <?
	}

		if($cbo_fabric_source==2)
		{
				$nameArray_gmts_sizes= sql_select("select a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight, b.dia_width, c.size_number_id,c.size_order FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls_hstry d
			WHERE a.job_no=b.job_no and
			a.id=b.pre_cost_fabric_cost_dtls_id and
			c.job_no_mst=a.job_no and
			c.id=b.color_size_table_id and
			b.po_break_down_id=d.po_break_down_id and
			b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
			d.booking_no =$txt_booking_no and
			d.approved_no=$revised_no and
			d.status_active=1 and
			d.is_deleted=0
			group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width,c.size_number_id,c.size_order order by c.size_order");
			$GmtsSizesArr=array();
			foreach($nameArray_gmts_sizes as $sizes_row){
				$GmtsSizesArr[$sizes_row[csf('body_part_id')]][$sizes_row[csf('color_type_id')]][$sizes_row[csf('construction')]][$sizes_row[csf('composition')]][$sizes_row[csf('gsm_weight')]][$sizes_row[csf('dia_width')]][$sizes_row[csf('size_number_id')]]=$size_library[$sizes_row[csf('size_number_id')]];
			}

			$nameArray_fabric_description= sql_select("select min(a.id) as fabric_cost_dtls_id,  a.lib_yarn_count_deter_id as determin_id,a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,min(a.width_dia_type) as width_dia_type , b.dia_width, avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent, avg(b.requirment) as requirment  FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls_hstry d
			WHERE a.job_no=b.job_no and
			a.id=b.pre_cost_fabric_cost_dtls_id and
			c.job_no_mst=a.job_no and
			c.id=b.color_size_table_id and
			b.po_break_down_id=d.po_break_down_id and
			b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
			d.booking_no =$txt_booking_no and
			d.approved_no=$revised_no and
			d.status_active=1 and
			d.is_deleted=0
			group by a.body_part_id,a.lib_yarn_count_deter_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width order by fabric_cost_dtls_id,a.body_part_id,b.dia_width");
			 ?>

		     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
		     <tr align="center">
		     <th colspan="3" align="left">Body Part</th>
		        <?
				foreach($nameArray_fabric_description  as $result_fabric_description)
				{
					if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";
					else         		               echo "<td  colspan='3'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";
				}
				?>
		        <td  rowspan="10" width="50"><p>Total Fabric (<? echo $unit_of_measurement[$booking_uom];?>)</p></td>
		        <td  rowspan="10" width="50"><p>Avg Rate (<? echo $unit_of_measurement[$booking_uom];?>)</p></td>
		        <td  rowspan="10" width="50"><p>Amount </p></td>
		       </tr>
		     <tr align="center"><th colspan="3" align="left">Color Type</th>
		        <?
				foreach($nameArray_fabric_description  as $result_fabric_description)
				{
					if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";
					else         		               echo "<td  colspan='3'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";
				}
				?>
		       </tr>
		        <tr align="center"><th colspan="3" align="left">Fabric Construction</th>
		        <?
				foreach($nameArray_fabric_description  as $result_fabric_description)
				{
					if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='3'>&nbsp</td>";
					else         		               echo "<td  colspan='3'>". $result_fabric_description[csf('construction')]."</td>";
				}
				?>


		       </tr>
		        <tr align="center"><th   colspan="3" align="left">Fabric Composition</th>
		        <?
				foreach($nameArray_fabric_description as $result_fabric_description)
				{
					if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='3' >&nbsp</td>";
					else         		               echo "<td colspan='3' >".$result_fabric_description[csf('composition')]."</td>";
				}
				?>

		       </tr>
		       <tr align="center"><th  colspan="3" align="left">GSM</th>
		        <?
				foreach($nameArray_fabric_description  as $result_fabric_description)
				{
					if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='3'>&nbsp</td>";
					else         		       echo "<td colspan='3' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";
				}
				?>

		       </tr>
		       <tr align="center"><th   colspan="3" align="left">Dia/Width (Inch)</th>
		        <?
				foreach($nameArray_fabric_description as $result_fabric_description)
				{
					if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='3'>&nbsp</td>";
					else         		              echo "<td colspan='3' align='center'>".$result_fabric_description[csf('dia_width')].",".$fabric_typee[$result_fabric_description[csf('width_dia_type')]]."</td>";
				}
				?>

		       </tr>
		       <tr align="center"><th   colspan="3" align="left">Gmts Sizes</th>
		        <?
				foreach($nameArray_fabric_description as $result_fabric_description)
				{
					$sizeArr=$GmtsSizesArr[$result_fabric_description[csf('body_part_id')]][$result_fabric_description[csf('color_type_id')]][$result_fabric_description[csf('construction')]][$result_fabric_description[csf('composition')]][$result_fabric_description[csf('gsm_weight')]][$result_fabric_description[csf('dia_width')]];
					$Gsize=implode(",",$sizeArr);


					if( $Gsize == "")   echo "<td colspan='3'>&nbsp</td>";
					else         		              echo "<td colspan='3' align='center'>".$Gsize."</td>";
				}
				?>

		       </tr>
		       <tr align="center"><th   colspan="3" align="left">Consumption For <? echo $costing_per; ?></th>
		        <?
				foreach($nameArray_fabric_description as $result_fabric_description)
				{
					if( $result_fabric_description[csf('requirment')] == "")   echo "<td colspan='3'>&nbsp</td>";
					else         		              echo "<td colspan='3' align='center'>Fin: ".number_format($result_fabric_description[csf('cons')],2).", Grey: ".number_format($result_fabric_description[csf('requirment')],2)."</td>";
				}
				?>

		       </tr>
		       <tr>
		       <th  colspan="<? echo  count($nameArray_fabric_description)*3+3; ?>" align="left" style="height:30px">&nbsp;</th>
		       </tr>

		       <tr>
		            <!--<th  width="120" align="left">Gmts. Color</th>-->
		            <th  width="120" align="left">Fabric Color</th>
		            <th  width="120" align="left">Body Color</th>
		            <th  width="120" align="left">Lab Dip No</th>
		        <?
				foreach($nameArray_fabric_description as $result_fabric_description)
				{
					  echo "<th width='50'>Fab. Qty</th><th width='50' >Rate</th><th width='50' >Amount</th>";
				}
				?>

		       </tr>
		       <?
				  $gmt_color_library=array();
				  $gmt_color_data=sql_select("select b.gmts_color_id, b.contrast_color_id
				  FROM
				  wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b
				  WHERE a.id=b.pre_cost_fabric_cost_dtls_id and a.fab_nature_id=$cbo_fabric_natu and a.fabric_source =$cbo_fabric_source and
				  a.job_no ='$job_no'");
				  foreach( $gmt_color_data as $gmt_color_row)
				  {
					$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]][$gmt_color_row[csf("gmts_color_id")]]=$color_library[$gmt_color_row[csf("gmts_color_id")]];
				  }

			        $grand_total_fin_fab_qnty=0;
					$grand_total_amount=0;
					$color_wise_wo_sql=sql_select("select fabric_color_id
												  FROM
												  wo_booking_dtls
												  WHERE
												  booking_no =$txt_booking_no and
												  status_active=1 and
		                                          is_deleted=0
												  group by fabric_color_id");
				foreach($color_wise_wo_sql as $color_wise_wo_result)
			    {
				?>
					<tr>
		            <td  width="120" align="left">
					<?
					echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];


					?>
		            </td>
		            <td>
		            <?
					echo implode(",",$gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]]);
					?>
		            </td>
		            <td  width="120" align="left">
					<?
					$lapdip_no="";
					$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."");
					if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no;
					?>
		            </td>
		            <?
					$total_fin_fab_qnty=0;
					$total_amount=0;

					foreach($nameArray_fabric_description as $result_fabric_description)
				    {
						if($db_type==0)
						{
						$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty, avg(d.rate) as rate FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls_hstry d
						WHERE a.job_no=b.job_no and
						a.id=b.pre_cost_fabric_cost_dtls_id and
						c.job_no_mst=a.job_no and
						c.id=b.color_size_table_id and
						b.po_break_down_id=d.po_break_down_id and
						b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
						d.booking_no =$txt_booking_no and
						d.approved_no =$revised_no and
						a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
						a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
						a.construction='".$result_fabric_description[csf('construction')]."' and
						a.composition='".$result_fabric_description[csf('composition')]."' and
						a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
						b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
						d.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
						d.status_active=1 and
						d.is_deleted=0
						");
						}
						if($db_type==2)
						{

						$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls_hstry d
						WHERE a.job_no=b.job_no and
						a.id=b.pre_cost_fabric_cost_dtls_id and
						c.job_no_mst=a.job_no and
						c.id=b.color_size_table_id and
						b.po_break_down_id=d.po_break_down_id and
						b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
						d.booking_no =$txt_booking_no and
						d.approved_no =$revised_no and
						a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
						a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
						a.construction='".$result_fabric_description[csf('construction')]."' and
						a.composition='".$result_fabric_description[csf('composition')]."' and
						a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
						b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
						nvl(d.fabric_color_id,0)=nvl('".$color_wise_wo_result[csf('fabric_color_id')]."',0) and
						d.status_active=1 and
						d.is_deleted=0
						");
						}
						list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
					?>
					<td width='50' align='right'>
					<?
					if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
					{
					echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2) ;
					$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
					}
					?>
		            </td>
		            <td width='50' align='right' >
					<?
					if($color_wise_wo_result_qnty[csf('rate')]!="")
					{
					echo number_format($color_wise_wo_result_qnty[csf('rate')],2);
					//$total_grey_fab_qnty+=$color_wise_wo_result_qnty['grey_fab_qnty'];
					}
					?>
		            </td>
		            <td width='50' align='right' >
					<?
					$amount=$color_wise_wo_result_qnty[csf('grey_fab_qnty')]*$color_wise_wo_result_qnty[csf('rate')];
					if($amount!="")
					{
					echo number_format($amount,2);
					$total_amount+=$amount;
					}
					?>
		            </td>
		            <?
					}
					?>
		            <td align="right"><? echo number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
		            <td align="right"><? echo number_format($total_amount/$total_fin_fab_qnty,2); $grand_total_amount+=$total_amount;?></td>
		            <td align="right">
		            <?
					echo number_format($total_amount,2);

					?>
		            </td>
		            </tr>
		         <?
				}
				?>
		        <tr style=" font-weight:bold">
		        <!--<td  width="120" align="left">&nbsp;</td>-->
		        <th  width="120" align="left">&nbsp;</th>
		        <td  width="120" align="left">&nbsp;</td>
		        <td  width="120" align="left"><strong>Total</strong></td>
		        <?
					foreach($nameArray_fabric_description as $result_fabric_description)
				    {

						$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls_hstry d
														WHERE a.job_no=b.job_no and
														a.id=b.pre_cost_fabric_cost_dtls_id and
														c.job_no_mst=a.job_no and
														c.id=b.color_size_table_id and
														b.po_break_down_id=d.po_break_down_id and
														b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
														d.booking_no =$txt_booking_no and
														d.approved_no =$revised_no and
														a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
														a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
														a.construction='".$result_fabric_description[csf('construction')]."' and
														a.composition='".$result_fabric_description[csf('composition')]."' and
														a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
														b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
														d.status_active=1 and
														d.is_deleted=0
														");
						list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
					?>
					<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2) ;?></td>
		            <td width='50' align='right' > <? //echo number_format($color_wise_wo_result_qnty['grey_fab_qnty'],2);?></td>
		            <td width='50' align='right' > <? //echo number_format($color_wise_wo_result_qnty['grey_fab_qnty'],2);?></td>
		            <?
					}
					?>
		            <td align="right"><? echo number_format($grand_total_fin_fab_qnty,2);?></td>
		            <td align="right"><? echo number_format($grand_total_amount/$grand_total_fin_fab_qnty,2);?></td>
		            <td align="right">
		            <?
					echo number_format($grand_total_amount,2);
					?>
		            </td>
		            </tr>
		            <tr style="font-weight:bold">
		        <!--<td  width="120" align="left">&nbsp;</td>-->
		        <th  width="120" align="left">&nbsp;</th>
		        <td  width="120" align="left">&nbsp;</td>
		        <td  width="120" align="left"><strong>Consumption For <? echo $costing_per; ?></strong></td>
		        <?
					foreach($nameArray_fabric_description as $result_fabric_description)
				    {

						$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls_hstry d
														WHERE a.job_no=b.job_no and
														a.id=b.pre_cost_fabric_cost_dtls_id and
														c.job_no_mst=a.job_no and
														c.id=b.color_size_table_id and
														b.po_break_down_id=d.po_break_down_id and
														b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
														d.booking_no =$txt_booking_no and
														d.approved_no =$revised_no and
														a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
														a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
														a.construction='".$result_fabric_description[csf('construction')]."' and
														a.composition='".$result_fabric_description[csf('composition')]."' and
														a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
														b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
														d.status_active=1 and
														d.is_deleted=0
														");
						list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty

					?>
					<td width='50' align='right'><?  //echo number_format(($color_wise_wo_result_qnty['fin_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4) ;?></td>
		            <td width='50' align='right' > <? //echo number_format(($color_wise_wo_result_qnty['grey_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4);?></td>
		            <td width='50' align='right' > <? //echo number_format(($color_wise_wo_result_qnty['grey_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4);?></td>
		            <?
					}
					?>
		            <td align="right">
					<?
					$consumption_per_unit_fab=($grand_total_fin_fab_qnty/$po_qnty_tot)*($costing_per_qnty);

					echo number_format($consumption_per_unit_fab,4);
					?>
		            </td>
		            <td align="right">
					<?
					$consumption_per_unit_amuont=($grand_total_amount/$po_qnty_tot)*($costing_per_qnty);
					echo number_format(($consumption_per_unit_amuont/$consumption_per_unit_fab),2);
					?>
		            </td>
		            <td align="right">
		            <?
					echo number_format($consumption_per_unit_amuont,2);
					?>
		            </td>
		            </tr>
		    </table>
		    <?
		}
		?>
        <br/>
        <?
		if($cbo_fabric_source==1 || $cbo_fabric_source==3 || $cbo_fabric_source==2)
		{
			$lab_dip_color_arr=array();
			$lab_dip_color_sql=sql_select("select pre_cost_fabric_cost_dtls_id, gmts_color_id, contrast_color_id from wo_pre_cos_fab_co_color_dtls where job_no='$job_no'");
			foreach($lab_dip_color_sql as $row)
			{
				$lab_dip_color_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('gmts_color_id')]]=$row[csf('contrast_color_id')];
			}
			$order_plan_qty_arr=array();
			$color_wise_wo_sql_qnty=sql_select( "select color_number_id, size_number_id, sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in ($booking_po_id) and status_active=1 and is_deleted =0 group by color_number_id, size_number_id");
			foreach($color_wise_wo_sql_qnty as $row)
			{
				$order_plan_qty_arr[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['plan']=$row[csf('plan_cut_qnty')];
				$order_plan_qty_arr[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order']=$row[csf('order_quantity')];
			}

			$collar_cuff_percent_arr=array();
			$collar_cuff_body_arr=array();
			$collar_cuff_color_arr=array();
			$collar_cuff_size_arr=array();
			$collar_cuff_item_size_arr=array();
			$color_size_sensitive_arr=array();

			$collar_cuff_sql="select a.id, a.color_size_sensitive, a.color_break_down, b.color_number_id, b.gmts_sizes, b.item_size, c.size_number_id, '' as colar_cuff_per, e.body_part_full_name, e.body_part_type
			FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c, wo_booking_dtls_hstry d, lib_body_part e

			WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and d.approved_no =$revised_no and a.body_part_id=e.id and c.id=d.color_size_table_id and d.color_size_table_id=b.color_size_table_id  and d.po_break_down_id=c.po_break_down_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and e.body_part_type in (40,50) order by  c.size_order";
			$collar_cuff_sql_res=sql_select($collar_cuff_sql);

			foreach($collar_cuff_sql_res as $collar_cuff_row)
			{
				$collar_cuff_percent_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('gmts_sizes')]]=$collar_cuff_row[csf('colar_cuff_per')];
				$collar_cuff_body_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]]=$collar_cuff_row[csf('body_part_full_name')];
				$collar_cuff_size_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('size_number_id')]]=$collar_cuff_row[csf('size_number_id')];
				$collar_cuff_item_size_arr[$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('size_number_id')]][$collar_cuff_row[csf('item_size')]]=$collar_cuff_row[csf('item_size')];
				$color_size_sensitive_arr[$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('id')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('color_size_sensitive')]]=$collar_cuff_row[csf('color_break_down')];

			}
			//print_r($collar_cuff_percent_arr[40]) ;
			unset($collar_cuff_sql_res);
			//$count_collar_cuff=count($collar_cuff_size_arr);
			/*echo '10**<pre>';
			print_r($collar_cuff_sql_res); die;*/
			foreach($collar_cuff_body_arr as $body_type=>$body_name)
			{
				foreach($body_name as $body_val)
				{
					$count_collar_cuff=count($collar_cuff_size_arr[$body_type][$body_val]);
					$pre_grand_tot_collar=0; $pre_grand_tot_collar_order_qty=0;

					?>
                    <div style="max-height:1330px; overflow:auto; float:left; padding-top:5px; margin-left:5px; margin-bottom:5px; position:relative;">
					<table width="625" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                        <tr>
                        	<td colspan="<? echo $count_collar_cuff+3; ?>" align="center"><b><? echo $body_val; ?> - Color Size Brakedown in Pcs.</b></td>
                        </tr>
                        <tr>
                            <td width="100">Size</td>
								<?
                                foreach($collar_cuff_size_arr[$body_type][$body_val]  as $size_number_id)
                                {
									?>
									<td align="center" style="border:1px solid black"><strong><? echo $size_library[$size_number_id];?></strong></td>
									<?
                                }
                                ?>
                            <td width="60" rowspan="2" align="center"><strong>Total</strong></td>
                            <td rowspan="2" align="center"><strong>Extra %</strong></td>
                        </tr>
                        <tr>
                            <td style="font-size:12px"><? echo $body_val; ?> Size</td>
                            <?
                            foreach($collar_cuff_item_size_arr[$body_val]  as $size_number_id=>$size_number)
                            {
								if(count($size_number)>0)
								{
									foreach($size_number  as $item_size=>$val)
									{
									?>
									<td align="center" style="border:1px solid black"><strong><? echo $item_size;?></strong></td>
									<?
									}
								}
								else
								{
									?>
									<td align="center" style="border:1px solid black"><strong>&nbsp;</strong></td>
									<?
								}
                            }

                            $pre_size_total_arr=array();
                            foreach($color_size_sensitive_arr[$body_val] as $pre_cost_id=>$pre_cost_data)
                            {
								foreach($pre_cost_data as $color_number_id=>$color_number_data)
								{
									foreach($color_number_data as $color_size_sensitive=>$color_break_down)
									{
										$pre_color_total_collar=0;
										$pre_color_total_collar_order_qnty=0;
										$process_loss_method=$process_loss_method;
										$constrast_color_arr=array();
										if($color_size_sensitive==3)
										{
											$constrast_color=explode('__',$color_break_down);
											for($i=0;$i<count($constrast_color);$i++)
											{
												$constrast_color2=explode('_',$constrast_color[$i]);
												$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
											}
										}
										?>
										<tr>
											<td>
												<?
                                                if( $color_size_sensitive==3)
                                                {
                                                    echo strtoupper ($constrast_color_arr[$color_number_id]) ;
                                                    $lab_dip_color_id=$lab_dip_color_arr[$pre_cost_id][$color_number_id];
                                                }
                                                else
                                                {
                                                    echo $color_library[$color_number_id];
                                                    $lab_dip_color_id=$color_number_id;
                                                }
                                                ?>
											</td>
											<?
											foreach($collar_cuff_size_arr[$body_type][$body_val] as $size_number_id)
											{
												?>
												<td align="center" style="border:1px solid black">
													<? $plan_cut=0; $collerqty=0; $collar_ex_per=0;
													if($body_type==50) $plan_cut=($order_plan_qty_arr[$color_number_id][$size_number_id]['plan'])*2;
													else $plan_cut=$order_plan_qty_arr[$color_number_id][$size_number_id]['plan'];
                                                    $ord_qty=$order_plan_qty_arr[$color_number_id][$size_number_id]['order'];

                                                    $collar_ex_per=$collar_cuff_percent_arr[$body_type][$body_val][$color_number_id][$size_number_id];
                                                    // echo $collar_ex_per.'=';

												    if($body_type==50) { if($collar_ex_per==0 || $collar_ex_per=="") $collar_ex_per=$cuff_excess_percent; else $collar_ex_per=$collar_ex_per; }
                                                    else if($body_type==40) { if($collar_ex_per==0 || $collar_ex_per=="") $collar_ex_per=$colar_excess_percent; else $collar_ex_per=$collar_ex_per; }
                                                    $colar_excess_per=number_format(($plan_cut*$collar_ex_per)/100,6,".",",");
                                                    $collerqty=($plan_cut+$colar_excess_per);

                                                    //$collerqty=number_format(($requirment/$costing_per_qnty)*$plan_cut,2,'.','');

                                                    echo number_format($collerqty);
                                                    $pre_size_total_arr[$size_number_id]+=$collerqty;
                                                    $pre_color_total_collar+=$collerqty;
                                                    $pre_color_total_collar_order_qnty+=$plan_cut;

                                                    //$pre_grand_tot_collar_order_qty+=$plan_cut;
                                                    ?>
												</td>
												<?
											}
											?>

											<td align="center"><? echo number_format($pre_color_total_collar); ?></td>
											<td align="center"><? echo number_format((($pre_color_total_collar-$pre_color_total_collar_order_qnty)/$pre_color_total_collar_order_qnty)*100,2); ?></td>
										</tr>
										<?
										$pre_grand_collar_ex_per+=$collar_ex_per;
										$pre_grand_tot_collar+=$pre_color_total_collar;
										$pre_grand_tot_collar_order_qty+=$pre_color_total_collar_order_qnty;
									}
								}
							}
							?>
                        </tr>
                        <tr>
                            <td>Size Total</td>
								<?
                                foreach($pre_size_total_arr  as $size_qty)
                                {
									?>
									<td style="border:1px solid black;  text-align:center"><? echo number_format($size_qty); ?></td>
									<?
                                }
                                ?>
                            <td style="border:1px solid black; text-align:center"><? echo number_format($pre_grand_tot_collar); ?></td>
                            <td align="center" style="border:1px solid black"><? echo number_format((($pre_grand_tot_collar-$pre_grand_tot_collar_order_qty)/$pre_grand_tot_collar_order_qty)*100,2); ?></td>
                        </tr>
					</table>
                </div>
                <br/>
                <?
            	}
        	}

			$lib_item_group_arr=return_library_array( "select item_name, id from lib_item_group where item_category=4 and is_deleted=0 and status_active=1 order by item_name", "id", "item_name");
			$sql=sql_select("select id from wo_booking_mst where booking_no=$txt_booking_no");
			$bookingId=0;
			foreach($sql as $row){
			$bookingId= $row[csf('id')];
			}
			$co=0;
			$sql_data=sql_select("select a.fabric_color, a.item_color, a.precost_trim_cost_id, b.trim_group, b.cons_uom,sum(qty) as qty from wo_dye_to_match a, wo_pre_cost_trim_cost_dtls b where a.precost_trim_cost_id=b.id and a.booking_id=$bookingId and a.qty>0 group by a.fabric_color, a.item_color, a.precost_trim_cost_id, b.trim_group, b.cons_uom order by a.fabric_color");
			if(count($sql_data)>0)
			{
				?>
				<br/>
				<table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
					<tr align="center">
						<td colspan="10"><b>Dyes To Match</b></td>
					</tr>
					<tr align="center">
						<td>Sl</td>
						<td>Item</td>
						<td>Body Color</td>
						<td>Item Color</td>
						<td>Finish Qty.</td>
						<td>UOM</td>
					</tr>
					<?
					foreach($sql_data  as $row)
					{
						$co++;
						?>
						<tr>
							<td><? echo $co; ?></td>
							<td> <? echo $lib_item_group_arr[$row[csf('trim_group')]];?></td>
							<td><? echo $color_library[$row[csf('fabric_color')]];?></td>
							<td><? echo $color_library[$row[csf('item_color')]];?></td>
							<td align="right"><? echo $row[csf('qty')];?></td>
							<td><? echo $unit_of_measurement[$row[csf('cons_uom')]];?></td>
						</tr>
						<?
					}
					?>
				</table>
				<br/>
				<?
			}
			$condition= new condition();
			if($job_no!=''){
				$condition->job_no("='$job_no'");
			}
			$condition->init();
			$cos_per_arr=$condition->getCostingPerArr();
			$yarn= new yarn($condition);
			//echo $yarn->getQuery(); die;
			$yarn_data_array=$yarn->getOrderCountCompositionPercentTypeColorAndRateWiseYarnQtyAndAmountArray();
			//print_r($yarn_data_array);
			$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
			$po_number=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number");

			$yarn_sql_array=sql_select("SELECT min(a.id) as id ,a.count_id, a.copm_one_id, a.percent_one, a.copm_two_id, a.percent_two, a.color, a.type_id, (a.rate) as rate, b.po_break_down_id from wo_pre_cost_fab_yarn_cost_dtls a, wo_booking_dtls_hstry b where a.job_no=b.job_no and a.fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.job_no='$job_no' and b.booking_no=$txt_booking_no and b.approved_no =$revised_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.count_id, a.copm_one_id, a.percent_one, a.copm_two_id, a.percent_two, a.color, a.type_id, b.po_break_down_id, a.rate order by po_break_down_id");
			?>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" >
				<tr>
					<td width="49%" valign="top">
						<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
							<tr align="center">
								<td colspan="5"><b>Yarn Required Summary (Pre Cost)</b></td>
							</tr>
							<tr align="center">
								<td>Sl</td>
								<td>PO</td>
								<td>Yarn Description</td>
									<?
									if($show_yarn_rate==1)
									{
										?>
										<td>Rate</td>
										<?
									}
									?>
								<td>Cons for <? echo $costing_per; ?> Gmts</td>
								<td>Total (KG)</td>
							</tr>
							<?
							$i=0;
							$total_yarn=0;
							foreach($yarn_sql_array  as $row)
							{
								$i++;
								$rowcons_qnty = $yarn_data_array[$row[csf("po_break_down_id")]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];
								$rowcons_Amt = $yarn_data_array[$row[csf("po_break_down_id")]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['amount'];


								$rate=$rowcons_Amt/$rowcons_qnty;
								//$rowcons_qnty =($rowcons_qnty/100)*$booking_percent;
								?>
								<tr align="center">
									<td><? echo $i; ?></td>
									<td><? echo $po_number[$row[csf('po_break_down_id')]]; ?></td>
									<td>
										<?
										$yarn_des=$yarn_count_arr[$row[csf('count_id')]]." ".$composition[$row[csf('copm_one_id')]]." ".$row[csf('percent_one')]."%  ";
										$yarn_des.=$color_library[$row[csf('color')]]." ";
										$yarn_des.=$yarn_type[$row[csf('type_id')]];

										echo $yarn_des;
										?>
									</td>
									<?
									if($show_yarn_rate==1)
									{
										?>
										<td><? echo number_format($rate,4); ?></td>
										<?
									}
									?>
									<td><?  echo number_format(($rowcons_qnty/$po_quantity)*$cos_per_arr[$job_no],4); ?></td>
									<td align="right"><? echo number_format($rowcons_qnty,2); $total_yarn+=$rowcons_qnty; ?></td>
								</tr>
								<?
							}
							?>
							<tr align="center">
								<td></td>
								<td></td>
								<td></td>
								<?
								if($show_yarn_rate==1)
								{
									?>
									<td></td>
									<?
								}
								?>
								<td align="right">Total : </td>
								<td align="right"><? echo number_format($total_yarn,2); ?></td>
							</tr>
						</table>
					</td>
					<td width="2%">&nbsp;</td>
					<td width="49%" valign="top" align="center">
					<?
					$yarn_sql_array=sql_select("SELECT min(a.id) as id, a.item_id, sum(a.qnty) as qnty ,min(b.supplier_id) as supplier_id,min(b.lot) as lot from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and a.booking_no=$txt_booking_no and  a.status_active=1 and a.is_deleted=0 group by a.item_id order by a.id");
					if(count($yarn_sql_array)>0)
					{
					?>
						<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
							<tr align="center">
								<td colspan="7"><b>Allocated Yarn</b></td>
							</tr>
							<tr align="center">
								<td>Sl</td>
								<td>Yarn Description</td>
								<td>Brand</td>
								<td>Lot</td>
								<td>Allocated Qty (Kg)</td>
							</tr>
							<?
							$total_allo=0;
							$item=return_library_array( "select id, product_name_details from   product_details_master",'id','product_name_details');
							$supplier=return_library_array( "select id, short_name from   lib_supplier",'id','short_name');
							$i=0;
							$total_yarn=0;
							foreach($yarn_sql_array  as $row)
							{
								$i++;
								?>
								<tr align="center">
									<td><? echo $i; ?></td>
									<td><? echo $item[$row[csf('item_id')]]; ?></td>
									<td><? echo $supplier[$row[csf('supplier_id')]]; ?></td>
									<td><? echo $row[csf('lot')]; ?></td>
									<td align="right"><? echo number_format($row[csf('qnty')],4); $total_allo+= $row[csf('qnty')];?></td>
								</tr>
								<?
							}
							?>
							<tr align="center">
								<td>Total</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right"><? echo number_format($total_allo,4); ?></td>
							</tr>
						</table>
						<?
					}
					else
					{
						$is_yarn_allocated=return_field_value("allocation", "variable_settings_inventory", "company_name=$cbo_company_name and variable_list=18 and item_category_id=1");
						if($is_yarn_allocated==1)
						{
							?>
							<font style=" font-size:30px"><b> Draft</b></font>
							<?
						}
						else
						{
							echo "";
						}
					}
					?>
					</td>
				</tr>
			</table>
			<br/>
			<?
			$sql_embelishment=sql_select("select emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls where job_no='$job_no' and status_active=1 and 	is_deleted=0");
			?>
			<table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
				<tr>
					<td width="49%" valign="top">
					<?
					if(count($sql_embelishment)>0)
					{
						?>
						<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
							<tr align="center">
								<td colspan="7"><b>Embelishment (Pre Cost)</b></td>
							</tr>
							<tr align="center">
								<td>Sl</td>
								<td>Embelishment Name</td>
								<td>Embelishment Type</td>
								<td>Cons <? echo $costing_per; ?> Gmts</td>
								<td>Rate</td>
								<td>Amount</td>
							</tr>
							<?
							$sql_embelishment=sql_select("select emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls where job_no='$job_no' and status_active=1 and 	is_deleted=0");
							$i=0;
							foreach($sql_embelishment  as $row_embelishment)
							{
								$i++;
								?>
								<tr align="center">
									<td><? echo $i; ?></td>
									<td><? echo $emblishment_name_array[$row_embelishment[csf('emb_name')]]; ?></td>
									<td>
									<?
										if($row_embelishment[csf('emb_name')]==1) echo $emblishment_print_type[$row_embelishment[csf('emb_type')]];
										if($row_embelishment[csf('emb_name')]==2) echo $emblishment_embroy_type[$row_embelishment[csf('emb_type')]];
										if($row_embelishment[csf('emb_name')]==3)  echo $emblishment_wash_type[$row_embelishment[csf('emb_type')]];
										if($row_embelishment[csf('emb_name')]==4) echo $emblishment_spwork_type[$row_embelishment[csf('emb_type')]];
										if($row_embelishment[csf('emb_name')]==5) echo $emblishment_gmts_type[$row_embelishment[csf('emb_type')]];
									?>
									</td>
									<td><? echo $row_embelishment[csf('cons_dzn_gmts')]; ?></td>
									<td><? echo $row_embelishment[csf('rate')]; ?></td>
									<td><? echo $row_embelishment[csf('amount')]; ?></td>
								</tr>
								<?
							}
							?>
						</table>
						<?
					}
					?>
					</td>
					<td width="2%">&nbsp;</td>
					<td width="49%" valign="top" align="center">
						<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
							<tr align="center">
								<td><b>Approved Instructions</b></td>
							</tr>
							<tr>
								<td><? echo $nameArray_approved_comments_row[csf('comments')]; ?></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<br/>

        <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="49%" style="border:solid; border-color:#000; border-width:thin" valign="top">
                    <? echo get_spacial_instruction($txt_booking_no,"97%",118);?>
				</td>
				<td width="2%">&nbsp;</td>
				<td width="49%" valign="top">
					<table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
						<tr align="center">
							<td colspan="10"><b>Comments</b></td>
						</tr>
						<tr align="center">
							<td>Sl</td>
							<td>Po NO</td>
							<td>Ship Date</td>
							<td>Pre-Cost Qty</td>
							<td>Mn.Book Qty</td>
							<td>Sht.Book Qty</td>
							<td>Smp.Book Qty</td>
							<td>Tot.Book Qty</td>
							<td>Balance</td>
							<td>Comments</td>
						</tr>
						<?
						$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
						$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
						if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and a.fab_nature_id='$cbo_fabric_natu'";
						if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and a.fabric_source='$cbo_fabric_source'";
						$paln_cut_qnty_array=return_library_array( "select min(id) as id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown  where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by color_number_id,size_number_id,item_number_id,po_break_down_id", "id", "plan_cut_qnty");

						$item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no =$txt_job_no", "gmts_item_id", "set_item_ratio");



						$nameArray=sql_select(" select a.id, a.item_number_id, a.costing_per, b.po_break_down_id, b.color_size_table_id, b.requirment, c.po_number FROM	wo_pre_cost_fabric_cost_dtls a,	wo_pre_cos_fab_co_avg_con_dtls b, wo_po_break_down c WHERE a.job_no=b.job_no and a.job_no=c.job_no_mst and a.id=b.pre_cost_fabric_cost_dtls_id and b.po_break_down_id=c.id and b.po_break_down_id in (".str_replace("'","",$txt_order_no_id).")  $cbo_fabric_natu $cbo_fabric_source_cond and a.status_active=1 and a.is_deleted=0 order by id");

						$count=0;
						$tot_grey_req_as_pre_cost_arr=array();
						foreach ($nameArray as $result)
						{
							if (count($nameArray)>0 )
							{
								if($result[csf("costing_per")]==1)
								{
									$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(12*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
								}
								if($result[csf("costing_per")]==2)
								{
									$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(1*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
								}
								if($result[csf("costing_per")]==3)
								{
									$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(24*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
								}
								if($result[csf("costing_per")]==4)
								{
									$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(36*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
								}
								if($result[csf("costing_per")]==5)
								{
									$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(48*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
								}
								$tot_grey_req_as_pre_cost_arr[$result[csf("po_number")]]+=$tot_grey_req_as_pre_cost;
							}
						}
						$total_pre_cost=0;
						$total_booking_qnty_main=0;
						$total_booking_qnty_short=0;
						$total_booking_qnty_sample=0;
						$total_tot_bok_qty=0;
						$tot_balance=0;

						$booking_qnty_main=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls_hstry a, wo_po_break_down b, wo_booking_mst_hstry c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no  and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and a.is_short=2 and c.item_category=2 and a.status_active=1 and a.is_deleted=0 and a.approved_no=$revised_no and c.approved_no=$revised_no group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");

						$booking_qnty_short=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls_hstry a, wo_po_break_down b , wo_booking_mst_hstry c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and c.item_category=2 and a.is_short=1 and a.status_active=1 and a.is_deleted=0 and a.approved_no=$revised_no and c.approved_no=$revised_no group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");

						$booking_qnty_sample=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls_hstry a, wo_po_break_down b , wo_booking_mst_hstry c  where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =4 and c.fabric_source=$cbo_fabric_source and c.item_category=2  and a.status_active=1 and a.is_deleted=0 and a.approved_no=$revised_no and c.approved_no=$revised_no group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");

						$sql_data=sql_select( "select max(a.id) as id,  a.po_number,max(a.pub_shipment_date) as pub_shipment_date,sum(a.plan_cut) as plan_cut  from wo_po_break_down a,wo_pre_cost_sum_dtls b,wo_pre_cost_mst c where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no and a.id in(".str_replace("'","",$txt_order_no_id).") group by a.po_number order by id");
						foreach($sql_data  as $row)
						{
							$col++;
							?>
							<tr align="center">
								<td><? echo $col; ?></td>
								<td><? echo $row[csf("po_number")]; ?></td>
								<td><? echo change_date_format($row[csf("pub_shipment_date")],"dd-mm-yyyy",'-'); ?></td>
								<td align="right"><? echo number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]],2); $total_pre_cost+=$tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]; ?></td>
								<td align="right"><? echo number_format($booking_qnty_main[$row[csf("id")]],2); $total_booking_qnty_main+=$booking_qnty_main[$row[csf("id")]];?></td>
								<td align="right"><? echo number_format($booking_qnty_short[$row[csf("id")]],2); $total_booking_qnty_short+=$booking_qnty_short[$row[csf("id")]];?></td>
								<td align="right"><? echo number_format($booking_qnty_sample[$row[csf("id")]],2); $total_booking_qnty_sample+=$booking_qnty_sample[$row[csf("id")]];?></td>
								<td align="right"><? $tot_bok_qty=$booking_qnty_main[$row[csf("id")]]+$booking_qnty_short[$row[csf("id")]]+$booking_qnty_sample[$row[csf("id")]]; echo number_format($tot_bok_qty,2); $total_tot_bok_qty+=$tot_bok_qty;?></td>
								<td align="right">
								<? $balance= def_number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]-$tot_bok_qty,2,""); echo number_format($balance,2); $tot_balance+= $balance?>
								</td>
								<td>
									<?
									if( $balance>0)
									{
										echo "Less Booking";
									}
									else if ($balance<0)
									{
										echo "Over Booking";
									}
									else
									{
										echo "";
									}
									?>
								</td>
							</tr>
							<?
						}
						?>
						<tfoot>
							<tr>
								<td colspan="3">Total:</td>
								<td align="right"><? echo number_format($total_pre_cost,2); ?></td>
								<td align="right"><? echo number_format($total_booking_qnty_main,2); ?></td>
								<td align="right"><? echo number_format($total_booking_qnty_short,2); ?></td>
								<td align="right"><? echo number_format($total_booking_qnty_sample,2); ?></td>
								<td align="right"><? echo number_format($total_tot_bok_qty,2); ?></td>
								<td align="right"><? echo number_format($tot_balance,2); ?></td>
								<td></td>
							</tr>
						</tfoot>
					</table>
				</td>
            </tr>
        </table>
       <br>
         <?

		 	$lib_designation=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");

	 		//$data_array=sql_select("select b.approved_by,b.approved_no, b.approved_date,b.un_approved_reason, c.user_full_name,c.designation  from  wo_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.booking_no=$txt_booking_no and b.entry_form=7 order by b.id asc");
			$data_array=sql_select(" select b.id,b.approved_by,b.approved_no, b.approved_date,b.un_approved_reason, c.user_full_name,c.designation,d.approval_cause from wo_booking_mst a join approval_history b on a.id=b.mst_id join  user_passwd c on b.approved_by=c.id left join fabric_booking_approval_cause d on b.id =d.approval_history_id  where a.booking_no=$txt_booking_no  and b.entry_form=7 order by b.id asc");

 	?>
       <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="3" style="border:1px solid black;">Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="25%" style="border:1px solid black;">Name/Designation</th><th width="27%" style="border:1px solid black;">Approval Date</th><th width="15%" style="border:1px solid black;">Approval No</th><th width="30%" style="border:1px solid black;">Un Approval Cause</th>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($data_array as $row){
			?>
            <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
                <td width="25%" style="border:1px solid black;"><? echo $row[csf('user_full_name')]." / ". $lib_designation[$row[csf('designation')]];?></td>
                <td width="27%" style="border:1px solid black;"><? echo date("d-m-Y h:i:s",strtotime($row[csf('approved_date')]));?></td>
                <td width="15%" style="border:1px solid black;"><? echo $row[csf('approved_no')];?></td>
                <td width="15%" style="border:1px solid black;"><? echo $row[csf('approval_cause')];?></td>
                </tr>
                <?
				$i++;
			}
				?>
            </tbody>
        </table>
        <br/>
       <?
	   //------------------------------ Query for TNA start-----------------------------------
				$po_id_all=str_replace("'","",$txt_order_no_id);
				$po_num_arr=return_library_array("select id,po_number from wo_po_break_down where id in($po_id_all)",'id','po_number');
				
				$tna_start_sql=sql_select( "select id,po_number_id,
								(case when task_number=31 then task_start_date else null end) as fab_booking_start_date,
								(case when task_number=31 then task_finish_date else null end) as fab_booking_end_date,
								(case when task_number=60 then task_start_date else null end) as knitting_start_date,
								(case when task_number=60 then task_finish_date else null end) as knitting_end_date,
								(case when task_number=61 then task_start_date else null end) as dying_start_date,
								(case when task_number=61 then task_finish_date else null end) as dying_end_date,
								(case when task_number=73 then task_start_date else null end) as finishing_start_date,
								(case when task_number=73 then task_finish_date else null end) as finishing_end_date,
								(case when task_number in (84,186,187) then task_start_date else null end) as cutting_start_date,
								(case when task_number in (84,186,187) then task_finish_date else null end) as cutting_end_date,
								(case when task_number in (86,122,190,191) then task_start_date else null end) as sewing_start_date,
								(case when task_number in (86,122,190,191) then task_finish_date else null end) as sewing_end_date,
								(case when task_number=110 then task_start_date else null end) as exfact_start_date,
								(case when task_number=110 then task_finish_date else null end) as exfact_end_date,
								(case when task_number=47 then task_start_date else null end) as yarn_rec_start_date,
								(case when task_number=47 then task_finish_date else null end) as yarn_rec_end_date,

								(case when task_number=63 then task_start_date else null end) as aop_rec_start_date,
								(case when task_number=63 then task_finish_date else null end) as aop_rec_end_date,
								(case when task_number=178 then task_start_date else null end) as kniting_yd_start_date,
								(case when task_number=178 then task_finish_date else null end) as kniting_yd_rec_end_date
								from tna_process_mst
								where status_active=1 and po_number_id in($po_id_all)");
				$tna_fab_start=$tna_knit_start=$tna_dyeing_start=$tna_fin_start=$tna_cut_start=$tna_sewin_start=$tna_exfact_start="";
				$tna_date_task_arr=array();
				foreach($tna_start_sql as $row)
				{
					if($row[csf("fab_booking_start_date")]!="" && $row[csf("fab_booking_start_date")]!="0000-00-00")
					{
						if($tna_fab_start=="")
						{
							$tna_fab_start=$row[csf("fab_booking_start_date")];
						}

						$tna_date_task_arr[$row[csf("po_number_id")]]['fab_booking_start_date']=$row[csf("fab_booking_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['fab_booking_end_date']=$row[csf("fab_booking_end_date")];


					}


					if($row[csf("knitting_start_date")]!="" && $row[csf("knitting_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['knitting_start_date']=$row[csf("knitting_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['knitting_end_date']=$row[csf("knitting_end_date")];
					}
					if($row[csf("dying_start_date")]!="" && $row[csf("dying_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['dying_start_date']=$row[csf("dying_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['dying_end_date']=$row[csf("dying_end_date")];
					}
					if($row[csf("finishing_start_date")]!="" && $row[csf("finishing_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['finishing_start_date']=$row[csf("finishing_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['finishing_end_date']=$row[csf("finishing_end_date")];
					}
					if($row[csf("cutting_start_date")]!="" && $row[csf("cutting_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_start_date']=$row[csf("cutting_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_end_date']=$row[csf("cutting_end_date")];
					}
					if($row[csf("sewing_start_date")]!="" && $row[csf("sewing_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_start_date']=$row[csf("sewing_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_end_date']=$row[csf("sewing_end_date")];
					}
					if($row[csf("exfact_start_date")]!="" && $row[csf("exfact_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_start_date']=$row[csf("exfact_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_end_date']=$row[csf("exfact_end_date")];
					}
					if($row[csf("yarn_rec_start_date")]!="" && $row[csf("yarn_rec_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_start_date']=$row[csf("yarn_rec_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_end_date']=$row[csf("yarn_rec_end_date")];
					}
					if($row[csf("kniting_yd_start_date")]!="" && $row[csf("kniting_yd_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['kniting_yd_start_date']=$row[csf("kniting_yd_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['kniting_yd_rec_end_date']=$row[csf("kniting_yd_rec_end_date")];
					}
					if($row[csf("aop_rec_start_date")]!="" && $row[csf("aop_rec_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['aop_rec_start_date']=$row[csf("aop_rec_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['aop_rec_end_date']=$row[csf("aop_rec_end_date")];
					}
				}

	//------------------------------ Query for TNA end-----------------------------------

	   	$task_short_name_arr=return_library_array( "select task_name,task_short_name from lib_tna_task where is_deleted=0 and status_active=1 and task_name in(31,60,61,73,84,186,187,86,122,190,191,110,47,63,178)",'task_name','task_short_name');



	   ?>

		<fieldset id="div_size_color_matrix" style="max-width:1200;">
				<legend>TNA Information</legend>
				<!--<span style="font-size:180; font-weight:bold;"></span>-->
				<table width="100%" style="border:1px solid black;font-size:12px" border="1" cellpadding="2" cellspacing="0" rules="all">
					<tr>
						<td rowspan="2" align="center" valign="top">SL</td>
						<td width="180" rowspan="2"  align="center" valign="top"><b>Order No</b></td>
						<td colspan="2" align="center" valign="top"><b><? echo $task_short_name_arr[31];?></b></td>
						<td colspan="2" align="center" valign="top"><b><? echo $task_short_name_arr[47];?><!--Yarn Receive--></b></td>
						<td colspan="2" align="center" valign="top"><b><? echo $task_short_name_arr[60];?><!--Knitting--></b></td>
						<td colspan="2" align="center" valign="top"><b><? echo $task_short_name_arr[178];?><!--Knitting Y/D--></b></td>
						<td colspan="2" align="center" valign="top"><b><? echo $task_short_name_arr[61];?><!--Dyeing--></b></td>
						<td colspan="2" align="center" valign="top"><b><? echo $task_short_name_arr[63];?><!--AOP Recieve--></b></td>

						<td colspan="2" align="center" valign="top"><b><? echo $task_short_name_arr[73];?><!--Finishing Fabric--></b></td>

						<td colspan="2" align="center" valign="top"><b>Cutting </b></td>
						<td colspan="2" align="center" valign="top"><b>Sewing </b></td>
						<td colspan="2"  align="center" valign="top"><b><? echo $task_short_name_arr[110];?><!--Ex-factory --></b></td>
					</tr>
					<tr>
						<td width="85" align="center" valign="top"><b>Start Date</b></td>
						<td width="85" align="center" valign="top"><b>End Date</b></td>

						<td width="85" align="center" valign="top"><b>Start Date</b></td>
						<td width="85" align="center" valign="top"><b>End Date</b></td>

						<td width="85" align="center" valign="top"><b>Start Date</b></td>
						<td width="85" align="center" valign="top"><b>End Date</b></td>

						<td width="85" align="center" valign="top"><b>Start Date</b></td>
						<td width="85" align="center" valign="top"><b>End Date</b></td>

						<td width="85" align="center" valign="top"><b>Start Date</b></td>
						<td width="85" align="center" valign="top"><b>End Date</b></td>

						<td width="85" align="center" valign="top"><b>Start Date</b></td>
						<td width="85" align="center" valign="top"><b>End Date</b></td>

						<td width="85" align="center" valign="top"><b>Start Date</b></td>
						<td width="85" align="center" valign="top"><b>End Date</b></td>
						<td width="85" align="center" valign="top"><b>Start Date</b></td>
						<td width="85" align="center" valign="top"><b>End Date</b></td>
						<td width="85" align="center" valign="top"><b>Start Date</b></td>
						<td width="85" align="center" valign="top"><b>End Date</b></td>
						<td width="85" align="center" valign="top"><b>Start Date</b></td>
						<td width="85" align="center" valign="top"><b>End Date</b></td>

					</tr>
					<?
					$i=1;
					foreach($tna_date_task_arr as $order_id=>$row)
					{
						?>
						<tr>
							<td><? echo $i; ?></td>
							<td><? echo $po_num_arr[$order_id]; ?></td>
							<td align="center"><? echo change_date_format($row['fab_booking_start_date']); ?></td>
							<td  align="center"><? echo change_date_format($row['fab_booking_end_date']); ?></td>

							<td align="center"><? echo change_date_format($row['yarn_rec_start_date']); ?></td>
							<td  align="center"><? echo change_date_format($row['yarn_rec_end_date']); ?></td>
							<td align="center"><? echo change_date_format($row['knitting_start_date']); ?></td>
							<td  align="center"><? echo change_date_format($row['knitting_end_date']); ?></td>

							<td  align="center"><? echo change_date_format($row['kniting_yd_start_date']); ?></td>
							<td  align="center"><? echo change_date_format($row['kniting_yd_rec_end_date']); ?></td>

							<td align="center"><? echo change_date_format($row['dying_start_date']); ?></td>
							<td align="center"><? echo change_date_format($row['dying_end_date']); ?></td>

							<td align="center"><? echo change_date_format($row['aop_rec_start_date']); ?></td>
							<td align="center"><? echo change_date_format($row['aop_rec_end_date']); ?></td>


							<td align="center"><? echo change_date_format($row['finishing_start_date']); ?></td>
							<td align="center"><? echo change_date_format($row['finishing_end_date']); ?></td>
							<td align="center"><? echo change_date_format($row['cutting_start_date']); ?></td>
							<td align="center"><? echo change_date_format($row['cutting_end_date']); ?></td>
							<td align="center"><? echo change_date_format($row['sewing_start_date']); ?></td>
							<td align="center"><? echo change_date_format($row['sewing_end_date']); ?></td>
							<td align="center"><? echo change_date_format($row['exfact_start_date']); ?></td>
							<td align="center"><? echo change_date_format($row['exfact_end_date']); ?></td>
						</tr>
						<?
						$i++;
					}
					?>

				</table>
				</fieldset>
		<?







		$tna_history_start_sql=sql_select( "select id,po_number_id,
		(case when task_number=31 then task_start_date else null end) as fab_booking_start_date,
		(case when task_number=31 then task_finish_date else null end) as fab_booking_end_date,
		(case when task_number=60 then task_start_date else null end) as knitting_start_date,
		(case when task_number=60 then task_finish_date else null end) as knitting_end_date,
		(case when task_number=61 then task_start_date else null end) as dying_start_date,
		(case when task_number=61 then task_finish_date else null end) as dying_end_date,
		(case when task_number=73 then task_start_date else null end) as finishing_start_date,
		(case when task_number=73 then task_finish_date else null end) as finishing_end_date,
		(case when task_number in (84,186,187) then task_start_date else null end) as cutting_start_date,
		(case when task_number in (84,186,187) then task_finish_date else null end) as cutting_end_date,
		(case when task_number in (86,122,190,191) then task_start_date else null end) as sewing_start_date,
		(case when task_number in (86,122,190,191) then task_finish_date else null end) as sewing_end_date,
		(case when task_number=110 then task_start_date else null end) as exfact_start_date,
		(case when task_number=110 then task_finish_date else null end) as exfact_end_date,
		(case when task_number=47 then task_start_date else null end) as yarn_rec_start_date,
		(case when task_number=47 then task_finish_date else null end) as yarn_rec_end_date,

		(case when task_number=63 then task_start_date else null end) as aop_rec_start_date,
		(case when task_number=63 then task_finish_date else null end) as aop_rec_end_date,
		(case when task_number=178 then task_start_date else null end) as kniting_yd_start_date,
		(case when task_number=178 then task_finish_date else null end) as kniting_yd_rec_end_date
		from tna_plan_actual_history
		where status_active=0 and is_deleted=1 and po_number_id in($po_id_all) and task_number in(31,60,61,73,84,186,187,86,122,190,191,110,47,63,178)");
		$tna_fab_start=$tna_knit_start=$tna_dyeing_start=$tna_fin_start=$tna_cut_start=$tna_sewin_start=$tna_exfact_start="";
		$tna_date_task_arr=array();
		foreach($tna_history_start_sql as $row)
		{
			if($row[csf("fab_booking_start_date")]!="" && $row[csf("fab_booking_start_date")]!="0000-00-00")
			{
				if($tna_fab_start=="")
				{
					$tna_fab_start=$row[csf("fab_booking_start_date")];
				}

				$tna_date_task_arr[$row[csf("po_number_id")]]['fab_booking_start_date']=$row[csf("fab_booking_start_date")];
				$tna_date_task_arr[$row[csf("po_number_id")]]['fab_booking_end_date']=$row[csf("fab_booking_end_date")];
			}


			if($row[csf("knitting_start_date")]!="" && $row[csf("knitting_start_date")]!="0000-00-00")
			{
				$tna_date_task_arr[$row[csf("po_number_id")]]['knitting_start_date']=$row[csf("knitting_start_date")];
				$tna_date_task_arr[$row[csf("po_number_id")]]['knitting_end_date']=$row[csf("knitting_end_date")];
			}
			if($row[csf("dying_start_date")]!="" && $row[csf("dying_start_date")]!="0000-00-00")
			{
				$tna_date_task_arr[$row[csf("po_number_id")]]['dying_start_date']=$row[csf("dying_start_date")];
				$tna_date_task_arr[$row[csf("po_number_id")]]['dying_end_date']=$row[csf("dying_end_date")];
			}
			if($row[csf("finishing_start_date")]!="" && $row[csf("finishing_start_date")]!="0000-00-00")
			{
				$tna_date_task_arr[$row[csf("po_number_id")]]['finishing_start_date']=$row[csf("finishing_start_date")];
				$tna_date_task_arr[$row[csf("po_number_id")]]['finishing_end_date']=$row[csf("finishing_end_date")];
			}
			if($row[csf("cutting_start_date")]!="" && $row[csf("cutting_start_date")]!="0000-00-00")
			{
				$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_start_date']=$row[csf("cutting_start_date")];
				$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_end_date']=$row[csf("cutting_end_date")];
			}
			if($row[csf("sewing_start_date")]!="" && $row[csf("sewing_start_date")]!="0000-00-00")
			{
				$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_start_date']=$row[csf("sewing_start_date")];
				$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_end_date']=$row[csf("sewing_end_date")];
			}
			if($row[csf("exfact_start_date")]!="" && $row[csf("exfact_start_date")]!="0000-00-00")
			{
				$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_start_date']=$row[csf("exfact_start_date")];
				$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_end_date']=$row[csf("exfact_end_date")];
			}
			if($row[csf("yarn_rec_start_date")]!="" && $row[csf("yarn_rec_start_date")]!="0000-00-00")
			{
				$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_start_date']=$row[csf("yarn_rec_start_date")];
				$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_end_date']=$row[csf("yarn_rec_end_date")];
			}
			if($row[csf("kniting_yd_start_date")]!="" && $row[csf("kniting_yd_start_date")]!="0000-00-00")
			{
				$tna_date_task_arr[$row[csf("po_number_id")]]['kniting_yd_start_date']=$row[csf("kniting_yd_start_date")];
				$tna_date_task_arr[$row[csf("po_number_id")]]['kniting_yd_rec_end_date']=$row[csf("kniting_yd_rec_end_date")];
			}
			if($row[csf("aop_rec_start_date")]!="" && $row[csf("aop_rec_start_date")]!="0000-00-00")
			{
				$tna_date_task_arr[$row[csf("po_number_id")]]['aop_rec_start_date']=$row[csf("aop_rec_start_date")];
				$tna_date_task_arr[$row[csf("po_number_id")]]['aop_rec_end_date']=$row[csf("aop_rec_end_date")];
			}
		}

       if(count($tna_date_task_arr)>0){

	  ?>

		<fieldset id="div_size_color_matrix" style="max-width:1200;">
        <legend>TNA History Information</legend>
        <!--<span style="font-size:180; font-weight:bold;"></span>-->
        <table width="100%" style="border:1px solid black;font-size:12px" border="1" cellpadding="2" cellspacing="0" rules="all">
            <tr>
            	<td rowspan="2" align="center" valign="top">SL</td>
            	<td width="180" rowspan="2"  align="center" valign="top"><b>Order No</b></td>
                <td colspan="2" align="center" valign="top"><b><? echo $task_short_name_arr[31];?></b></td>
                <td colspan="2" align="center" valign="top"><b><? echo $task_short_name_arr[47];?><!--Yarn Receive--></b></td>
                <td colspan="2" align="center" valign="top"><b><? echo $task_short_name_arr[60];?><!--Knitting--></b></td>
				<td colspan="2" align="center" valign="top"><b><? echo $task_short_name_arr[178];?><!--Knitting Y/D--></b></td>
                <td colspan="2" align="center" valign="top"><b><? echo $task_short_name_arr[61];?><!--Dyeing--></b></td>
				<td colspan="2" align="center" valign="top"><b><? echo $task_short_name_arr[63];?><!--AOP Recieve--></b></td>

                <td colspan="2" align="center" valign="top"><b><? echo $task_short_name_arr[73];?><!--Finishing Fabric--></b></td>

                <td colspan="2" align="center" valign="top"><b>Cutting </b></td>
                <td colspan="2" align="center" valign="top"><b>Sewing </b></td>
                <td colspan="2"  align="center" valign="top"><b><? echo $task_short_name_arr[110];?><!--Ex-factory --></b></td>
            </tr>
            <tr>
            	<td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>

                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>

                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>

				<td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>

				<td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>

                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>

                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>

            </tr>
            <?
			$i=1;
			foreach($tna_date_task_arr as $order_id=>$row)
			{
				?>
                <tr>
                	<td><? echo $i; ?></td>
                    <td><? echo $po_num_arr[$order_id]; ?></td>
                    <td align="center"><? echo change_date_format($row['fab_booking_start_date']); ?></td>
                    <td  align="center"><? echo change_date_format($row['fab_booking_end_date']); ?></td>

                    <td align="center"><? echo change_date_format($row['yarn_rec_start_date']); ?></td>
                    <td  align="center"><? echo change_date_format($row['yarn_rec_end_date']); ?></td>
                	<td align="center"><? echo change_date_format($row['knitting_start_date']); ?></td>
                    <td  align="center"><? echo change_date_format($row['knitting_end_date']); ?></td>

					<td  align="center"><? echo change_date_format($row['kniting_yd_start_date']); ?></td>
					<td  align="center"><? echo change_date_format($row['kniting_yd_rec_end_date']); ?></td>

                    <td align="center"><? echo change_date_format($row['dying_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['dying_end_date']); ?></td>

					 <td align="center"><? echo change_date_format($row['aop_rec_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['aop_rec_end_date']); ?></td>

                    <td align="center"><? echo change_date_format($row['finishing_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['finishing_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['cutting_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['cutting_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['sewing_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['sewing_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['exfact_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['exfact_end_date']); ?></td>
                </tr>
                <?
				$i++;
			}
			?>
        </table>
        </fieldset>
		<?
	   		}
		}// fabric Source End

		if($cbo_fabric_source==2)
		{
           echo get_spacial_instruction($txt_booking_no,"97%",118);
		}
		echo signature_table(1, $cbo_company_name, "1330px");
		?>
       </div>
       <?

}

// start last version print report 
if($action=="refusing_cause_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Refusing Cause Info","../../", 1, 1, $unicode);
	$permission="1_1_1_1";
	?>
    <script>
 	var permission='<? echo $permission; ?>';

	function set_values( cause )
	{
		var refusing_cause = document.getElementById('txt_refusing_cause').value;
		if(refusing_cause == '')
		{
			document.getElementById('txt_refusing_cause').value =refusing_cause;
			parent.emailwindow.hide();
		}
		else
		{
			alert("Please save refusing cause first or empty");
			return;
		}

	}

	function fnc_cause_info( operation )
	{
		var refusing_cause=$("#txt_refusing_cause").val();
		var quo_id=$("#hidden_quo_id").val();
  		if (form_validation('txt_refusing_cause','Refusing Cause')==false)
		{
			return;
		}
		else
		{
			if(operation==2)
			{
				alert('Delete Not allow');
				return;
			}
			var data="action=save_update_delete_refusing_cause&operation="+operation+"&refusing_cause="+refusing_cause+"&quo_id="+quo_id;
			http.open("POST","fabric_booking_approval_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_cause_info_reponse;
		}
	}
	function fnc_cause_info_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split('**');
			if(response[0]==0 || response[0]==1)
			{
				if(response[0]==0) alert("Data saved successfully");
				if(response[0]==1) alert("Data Update successfully");
				document.getElementById('txt_refusing_cause').value =response[1];
				parent.emailwindow.hide();
			}
			else
			{
				alert("Data not saved");
				return;
			}
		}
	}

    </script>
    <body  onload="set_hotkey();">
    <div align="center" style="width:100%;">
	<fieldset style="width:470px;">
		<legend>Refusing Cause</legend>
		<form name="causeinfo_1" id="causeinfo_1"  autocomplete="off">
			<table cellpadding="0" cellspacing="2" width="470px">
			 	<tr>
					<td width="100" class="must_entry_caption">Refusing Cause</td>
					<td >
						<?
							$refusing_reason_arr =sql_select("SELECT id, refusing_reason from refusing_cause_history where entry_form=7 and  mst_id='$quo_id' order by id desc ");
						?>
						<input type="text" name="txt_refusing_cause" id="txt_refusing_cause" class="text_boxes" style="width:320px;height: 100px;" value="<?=$refusing_reason_arr[0][csf('refusing_reason')]?>" />
						<input type="hidden" name="hidden_quo_id" id="hidden_quo_id" value="<?=$quo_id;?>">
					</td>
                  </tr>
                  <tr>
					<td colspan="4" align="center" class="button_container">
						<?
						if(count($refusing_reason_arr)>0)
						{
					    	echo load_submit_buttons( $permission, "fnc_cause_info", 1,0 ,"reset_form('causeinfo_1','','')",1);
						}
						else{
							echo load_submit_buttons( $permission, "fnc_cause_info", 0,0 ,"reset_form('causeinfo_1','','')",1);
						}
				        ?> </br>
				        <input type="button" class="formbutton" value="Close" name="close_buttons" id="close_buttons" onClick="set_values();" style="width:50px;height: 35px;">
 					</td>
				</tr>
				<tr>
					<td colspan="4" align="center">&nbsp;</td>
				</tr>
		   </table>
			</form>
		</fieldset>
	</div>
    <?
			$sqlHis="select approval_cause from approval_cause_refusing_his where entry_form=7 and booking_id='$quo_id' and approval_type='1' order by id Desc";
			$sqlHisRes=sql_select($sqlHis);
		?>
		<table align="center" cellspacing="0" width="420" class="rpt_table" border="1" rules="all">
			<thead>
				<th width="30">SL</th>
				<th>Refusing History</th>
			</thead>
		</table>
		<div style="width:420px; overflow-y:scroll; max-height:260px;" align="center">
			<table align="center" cellspacing="0" width="403" class="rpt_table" border="1" rules="all">
			<?
			$i=1;
			foreach($sqlHisRes as $hrow)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');">
					<td width="30"><?=$i; ?></td>
					<td style="word-break:break-all"><?=$hrow[csf('approval_cause')]; ?></td>
				</tr>
				<?
				$i++;
			}
			?>
			</table>
		</div>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</body>
    <?
	exit();
}

if($action=="save_update_delete_refusing_cause")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $_REQUEST ));
	
	if(is_duplicate_field( "approval_cause", "approval_cause_refusing_his", "approval_cause='".str_replace("'", "", $refusing_cause)."' and entry_form=7 and booking_id='".str_replace("'", "", $quo_id)."' and approval_type=1 and status_active=1 and is_deleted=0" )==1)
	{
		//
	}
	else
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id_his=return_next_id( "id", "approval_cause_refusing_his", 1);
		$id=return_field_value("id", "refusing_cause_history", "mst_id=".$quo_id." and entry_form=7");
		$sqlHis="insert into approval_cause_refusing_his( id, cause_id, entry_form, booking_id, approval_type, approval_cause, inserted_by, insert_date, updated_by, update_date)
				select $id_his, id, entry_form, mst_id, 1, refusing_reason, inserted_by, insert_date, updated_by, update_date from refusing_cause_history where mst_id=".$quo_id." and entry_form=7 and id=$id"; //die;
		$flag=1;
		if(count($sqlHis)>0)
		{
			$rID3=execute_query($sqlHis,0);
			if($flag==1)
			{
				if($rID3==1) $flag=1; else $flag=0;
			}
		}
	}
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}		
		$id=return_next_id( "id", "refusing_cause_history", 1);
		$field_array = "id,entry_form,mst_id,refusing_reason,inserted_by,insert_date";
		$data_array = "(".$id.",7,".$quo_id.",'".$refusing_cause."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		$rID=sql_insert("refusing_cause_history",$field_array,$data_array,1);
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");
				echo "0**$refusing_cause";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
				echo "0**$refusing_cause";
			}
			else{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if($operation==1)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}		
		
		$id=return_field_value("id", "refusing_cause_history", "mst_id=".$quo_id." and entry_form=7");
		$field_array = "refusing_reason*updated_by*update_date";
		$data_array = "'".str_replace("'", "", $refusing_cause)."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				
		$flag=1;		
		$rID=sql_update("refusing_cause_history",$field_array,$data_array,"id",$id,0);
		if($rID==1) $flag=1; else $flag=0;
		
		//echo "10**".$rID.'='.$rID3.'='.$flag; die;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**$refusing_cause";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**$refusing_cause";
			}
			else{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if($operation==2)
	{
	}
}



// B16 START 
if($action=="show_fabric_booking_report16")//Print B16
{
	extract($_REQUEST);
	$data = explode('**', $data);

	$txt_booking_no="'".str_replace("'","",$data[0])."'";
	$cbo_company_name=str_replace("'","",$data[1]);
	$txt_order_no_id=str_replace("'","",$data[2]);
	$cbo_fabric_natu=str_replace("'","",$data[3]);
	$cbo_fabric_source=str_replace("'","",$data[4]);
	$revised_no=str_replace("'","",$data[5]);
	//$txt_job_no="'".implode(explode(",", $data[6]), "','")."'";
	$txt_job_no=str_replace("'","",$data[6]);

	$show_yarn_rate=str_replace("'","",$data[7]);
	$report_title=str_replace("'","",$data[8]);
	$path=str_replace("'","",$data[9]);
	$id_approved_id=str_replace("'","",$data[10]);

	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1 and master_tble_id='$cbo_company_name'",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_name_arr=return_library_array( "select id, brand_name from lib_buyer_brand ",'id','brand_name');
	$user_name_arr=return_library_array( "select id, user_full_name from user_passwd ",'id','user_full_name');
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );

	//$location_name_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	//$po_qnty_tot1=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	//wo_pre_cost_fabric_cost_dtls
	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	?>
	
	<style type="text/css">
		@media print {
		    .pagebreak { page-break-before: always; } /* page-break-after works, as well */
		}
	</style>
	<div style="width:1330px" align="center">
    <?php
    	$lip_yarn_count=return_library_array( "select id,fabric_composition_id from lib_yarn_count_determina_mst where  status_active=1", "id", "fabric_composition_id");
		$fabric_composition=return_library_array( "select id,fabric_composition_name from lib_fabric_composition where  status_active=1", "id", "fabric_composition_name");

		
		/*$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7");
		list($nameArray_approved_row) = $nameArray_approved;*/
		$nameArray_approved_row[csf('approved_no')]=$revised_no;
		$nameArray_approved_date = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
		list($nameArray_approved_date_row) = $nameArray_approved_date;
		$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
		list($nameArray_approved_comments_row) = $nameArray_approved_comments;

		$max_approve_date_data = sql_select("select min(b.approved_date) as approved_date,max(b.approved_date) as last_approve_date,max(b.un_approved_date) as un_approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7");
		//echo "select min(b.approved_date) as approved_date,max(b.approved_date) as last_approve_date,max(b.un_approved_date) from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7";
		$first_approve_date='';
		$last_approve_date='';
		$un_approved_date='';
		if(count($max_approve_date_data))
		{
			$last_approve_date=$max_approve_date_data[0][csf('last_approve_date')];
			$first_approve_date=$max_approve_date_data[0][csf('approved_date')];
			$un_approved_date=$max_approve_date_data[0][csf('un_approved_date')];
		}
		
		  if($txt_job_no!="") $location=return_field_value( "location_name", "wo_po_details_master","job_no='$txt_job_no'");
             else $location="";

		$sql_loc=sql_select("select id,location_name,address from lib_location where company_id=$cbo_company_name");
		//echo "select id,location_name,address from lib_location where company_id=$cbo_company_name";
		foreach($sql_loc as $row)
		{
			$location_name_arr[$row[csf('id')]]= $row[csf('location_name')];
			$location_address_arr[$row[csf('id')]]= $row[csf('address')];
		}
		// echo "<pre>";
		// print_r($emblishment_name_array);
		// echo "</pre>";

		//[140] => WASH ENZYME SILICON
    	//[142] => Wash Hydro Tumble Dry
    	//[193] => Washing
		
		$yes_no_sql=sql_select("select job_no,cons_process from  wo_pre_cost_fab_conv_cost_dtls where job_no='$txt_job_no'  and status_active=1 and is_deleted=0  order by id");
		
		$peach='';
		$brush='';
		$fab_wash='';
		foreach ($yes_no_sql as $row) {
			
			if (strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'peach') !== false)
        	{
			    if(!empty($peach))
			    {
			    	$peach.=",".$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			    else{
			    	$peach.=$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			}
			if (strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'brush') !== false)
        	{
			    if(!empty($brush))
			    {
			    	$brush.=",".$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			    else{
			    	$brush.=$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			}
			if (strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'wash') !== false)
        	{
			    if(!empty($fab_wash))
			    {
			    	$fab_wash.=",".$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			    else{
			    	$fab_wash.=$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			}
		}
		$emb_print=sql_select("select id, job_no, emb_name, emb_type from wo_pre_cost_embe_cost_dtls where  job_no='$txt_job_no' and status_active=1 and is_deleted=0 order by id");
		//echo "select id, job_no, emb_name, emb_type from wo_pre_cost_embe_cost_dtls where emb_name!=3 and job_no='$txt_job_no' and status_active=1 and is_deleted=0 order by id";


		$emb_print_data=array();
		$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type,99=>$blank_array);
		foreach ($emb_print as $row) 
		{
			$emb_print_data[$row[csf('job_no')]][$row[csf('emb_name')]].=$type_array[$row[csf("emb_name")]][$row[csf('emb_type')]].",";
		}
		// echo "<pre>";
		// print_r($emb_print_data);
		// echo "</pre>";
		//echo "select id, job_no, emb_name, emb_type from wo_pre_cost_embe_cost_dtls where emb_name!=3 and job_no='$txt_job_no' and status_active=1 and is_deleted=0 order by id";

		//requisition_no,fab_material,quality_level,sustainability_standard,remarks,fabric_composition, tagged_booking_no

		$nameArray=sql_select( "select a.booking_no, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.po_break_down_id, a.colar_excess_percent, a.cuff_excess_percent, a.delivery_date, a.is_apply_last_update, a.fabric_source, a.rmg_process_breakdown, a.insert_date, a.update_date, '' as tagged_booking_no, '' as uom, a.pay_mode, a.booking_percent, b.job_no, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.total_set_qnty, b.style_description, b.season_buyer_wise as season, b.product_dept, b.product_code, b.pro_sub_dep, b.dealing_marchant,b.factory_marchant, b.order_repeat_no, b.repeat_job_no, '' as fabric_composition, '' as remarks,'' as sustainability_standard,b.brand_id,'' as quality_level,'' as fab_material,'' as requisition_no,b.qlty_label,b.packing,b.job_no from wo_booking_mst_hstry a, wo_po_details_master b where a.job_no=b.job_no and a.booking_no=$txt_booking_no and a.approved_no=$revised_no");
		
		$po_id_all=$nameArray[0][csf('po_break_down_id')];
		$job_no_str=$nameArray[0][csf('job_no')];
		$booking_uom=$nameArray[0][csf('uom')];
		$bookingup_date=$nameArray[0][csf('update_date')];
		$bookingins_date=$nameArray[0][csf('insert_date')];
		$delivery_date=$nameArray[0][csf('delivery_date')];
		$requisition_no=$nameArray[0][csf('requisition_no')];
		
		$job_yes_no=sql_select("select id, job_id,job_no, gmts_item_id, set_item_ratio, smv_pcs, smv_set, smv_pcs_precost, smv_set_precost, complexity, embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, ws_id, aop, aopseq,bush,bushseq,peach,peachseq,yd,ydseq from wo_po_details_mas_set_details where job_no='$job_no_str'");

		$po_shipment_date=sql_select("select  MIN(pub_shipment_date) as min_shipment_date,max(pub_shipment_date) as max_shipment_date from wo_po_break_down where id in(".$po_id_all.") order by shipment_date asc ");
		//echo "select  MIN(pub_shipment_date) as min_shipment_date,max(pub_shipment_date) as max_shipment_date from wo_po_break_down where id in(".$po_id_all.") ";
         $min_shipment_date='';
         $max_shipment_date='';
         foreach ($po_shipment_date as $row) {
         	 $min_shipment_date=$row[csf('min_shipment_date')];
         	 $max_shipment_date=$row[csf('max_shipment_date')];
         	 break;
         }

        $po_running_cancel= sql_select("select case when status_active=1 then  PO_NUMBER end as running_po, case when status_active>1 then po_number end as cancel_po,po_quantity from wo_po_break_down  where id in(".$po_id_all.") order by shipment_date asc ");
        $running_po='';
        $cancel_po='';
        $running_po_qnty=0;
        foreach ($po_running_cancel as $row) {
        	if(!empty($row[csf('running_po')]))
        	{
        		if(!empty($running_po))
        		{
        			$running_po.=",".$row[csf('running_po')];
        		}
        		else{
        			$running_po.=$row[csf('running_po')];
        		}
        		 $running_po_qnty+=$row[csf('po_quantity')];
        	}
        	if(!empty($row[csf('cancel_po')]))
        	{
        		if(!empty($cancel_po))
        		{
        			$cancel_po.=",".$row[csf('cancel_po')];
        		}
        		else{
        			$cancel_po.=$row[csf('cancel_po')];
        		}
        	}
        }
        $stype_color_res=sql_select("select  stripe_type from wo_pre_stripe_color where job_no='$txt_job_no' and status_active=1 and is_deleted=0 group by stripe_type");
        $stype_color='';
        foreach ($stype_color_res as $val) {
        	if(!empty($stype_color))
        	{
        		$stype_color.=", ".$stripe_type_arr[$val[csf('stripe_type')]];
        	}
        	else
        	{
        		$stype_color=$stripe_type_arr[$val[csf('stripe_type')]];
        	}
        	
        }
        $yd_aop_sql=sql_select("select id, job_no,  color_type_id from wo_pre_cost_fabric_cost_dtls where job_no='$txt_job_no' and status_active=1 and is_deleted=0 order by id asc");
        //echo "select id, job_no,  color_type_id from wo_pre_cost_fabric_cost_dtls where job_no='$txt_job_no' and status_active=1 and is_deleted=0 order by id asc";

        $yd='';
        $aop='';
        foreach ($yd_aop_sql as $row) {
        	if (strpos(strtolower($color_type[$row[csf('color_type_id')]]), 'y/d') !== false)
        	{
			    if(!empty($yd))
			    {
			    	$yd.=",".$color_type[$row[csf('color_type_id')]];
			    }
			    else{
			    	$yd.=$color_type[$row[csf('color_type_id')]];
			    }
			   
			}
			if (strpos(strtolower($color_type[$row[csf('color_type_id')]]), 'aop') !== false)
        	{
			    if(!empty($aop))
			    {
			    	$aop.=",".$color_type[$row[csf('color_type_id')]];
			    }
			    else{
			    	$aop.=$color_type[$row[csf('color_type_id')]];
			    }

			}
			//echo $color_type[$row[csf('color_type_id')]]."<br>";
        }
       // echo $yd.'__'.$aop;
		?>	
											<!--    Header Company Information         -->
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black; font-family:Arial Narrow;" >
            <tr>
                <td width="200" style="font-size:28px"><img  src='../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' /></td>
                <td width="1250">
                    <table width="100%" cellpadding="0" cellspacing="0"  >
                        <tr>
                            <td align="center" style="font-size:28px;"> <?php echo $company_library[$cbo_company_name]; ?></td>
                            
                        </tr>
                        <tr>
                            <td align="center" style="font-size:18px">
                           <?
                                                      
                            echo $location_address_arr[$location];
                           ?> 
                            </td>
                            
                        </tr>
                        <tr>
                            <td align="center" style="font-size:24px">
                            <span style="float:center;"><b><strong> <font style="color:black">Main Fabric Booking </font></strong></b></span> 
                               
                            </td>
                            
                        </tr>
						
                        <tr>
                            <td align="center" style="font-size:20px">
							<?
							if(str_replace("'","",$id_approved_id) ==1){ ?>
                            <span style="font-size:20px; float:center;"><strong> <font style="color:green"> <? echo "[Approved]"; ?> </font></strong></span> 
                               <?}else{?>

								<span style="font-size:20px; float:center;"><strong> <font style="color:red"><? echo "[Not Approved]"; ?> </font></strong></span> 

							   <?}?>
                            </td>
                            
                        </tr>
                    </table>
                </td>
                <td width="200">
                	<table style="border:1px solid black; font-family:Arial Narrow;" width="100%">
                		<tr>
                			<td><b>Min. Ship Date:</b></td>
                			<td><b><?php echo  date('d-m-Y',strtotime($min_shipment_date));?></b></td>
                		</tr>
                		<tr>
                			<td><b>Max. Ship Date:</b></td>
                			<td><b><?php echo date('d-m-Y',strtotime($max_shipment_date));?></b></td>
                		</tr>
                	</table>
                	<br>
                	<table style="border:1px solid black; font-family:Arial Narrow;font-size: 10px;" width="100%">
                		<tr>
                			<td>Printing Date :</td>
                			<td><?php echo  date('d-m-Y');?></td>
                		</tr>
                		<tr>
                			<td>Printing Time:</td>
                			<td><?php echo  date('h:i:sa');?></td>
                		</tr>
                		<tr>
                			<td>User Name:</td>
                			<td><?php echo $user_name_arr[$user_id];?></td>
                		</tr>
                		<tr>
                			<?php 

                				function get_client_ip() {
								    $ipaddress = '';
								    if (getenv('HTTP_CLIENT_IP'))
								        $ipaddress = getenv('HTTP_CLIENT_IP');
								    else if(getenv('HTTP_X_FORWARDED_FOR'))
								        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
								    else if(getenv('HTTP_X_FORWARDED'))
								        $ipaddress = getenv('HTTP_X_FORWARDED');
								    else if(getenv('HTTP_FORWARDED_FOR'))
								        $ipaddress = getenv('HTTP_FORWARDED_FOR');
								    else if(getenv('HTTP_FORWARDED'))
								       $ipaddress = getenv('HTTP_FORWARDED');
								    else if(getenv('REMOTE_ADDR'))
								        $ipaddress = getenv('REMOTE_ADDR');
								    else
								        $ipaddress = 'UNKNOWN';
								    return $ipaddress;
								}

                			 ?>
                			<td>IP Address:</td>
                			<td><?php if(empty($user_ip)){echo get_client_ip();} echo $user_ip;?></td>
                		</tr>
                	</table>
                </td>
            </tr>
        </table>
		<?
		
       
		
        $job_no=trim($txt_job_no,"'"); $total_set_qnty=0; $colar_excess_percent=0; $cuff_excess_percent=0; $rmg_process_breakdown=0; $booking_percent=0; $booking_po_id='';
		
        
		
		if($db_type==0)
        {
            $date_dif_cond="DATEDIFF(pub_shipment_date,po_received_date)";
            $group_concat_all="group_concat(grouping) as grouping, group_concat(file_no) as file_no";
        }
        else
        {
            $date_dif_cond="(pub_shipment_date-po_received_date)";
            $group_concat_all=" listagg(cast(grouping as varchar2(4000)),',') within group (order by grouping) as grouping,
                                listagg(cast(file_no as varchar2(4000)),',') within group (order by file_no) as file_no  ";
        }
        $po_number_arr=array(); $po_ship_date_arr=array(); $shipment_date=""; $po_no=""; $po_received_date=""; $shiping_status="";
        $po_sql=sql_select("select id, po_number, pub_shipment_date, MIN(pub_shipment_date) as mpub_shipment_date, MIN(po_received_date) as po_received_date, MIN(insert_date) as insert_date, plan_cut, po_quantity, shiping_status, $date_dif_cond as date_diff,min(factory_received_date) as factory_received_date, $group_concat_all from wo_po_break_down where id in(".$po_id_all.") group by id, po_number, pub_shipment_date, plan_cut, po_quantity, shiping_status, po_received_date");
      
        $to_ship=0;
        $fp_ship=0;
        $f_ship=0;

        foreach($po_sql as $row)
        {
            $po_qnty_tot+=$row[csf('plan_cut')];
            $po_qnty_tot1+=$row[csf('po_quantity')];
            $po_number_arr[$row[csf('id')]]=$row[csf('po_number')];
            $po_ship_date_arr[$row[csf('id')]]=$row[csf('pub_shipment_date')];
            $po_num_arr[$row[csf('id')]]=$row[csf('po_number')];
            $po_no.=$row[csf('po_number')].", ";
            $shipment_date.=change_date_format($row[csf('mpub_shipment_date')],'dd-mm-yyyy','-').", ";
            $lead_time.=$row[csf('date_diff')].",";
            $po_received_date=change_date_format($row[csf('po_received_date')],'dd-mm-yyyy','-');
            $factory_received_date=change_date_format($row[csf('factory_received_date')],'dd-mm-yyyy','-');
            $grouping.=$row[csf('grouping')].",";
            $file_no.=$row[csf('file_no')].",";
			
			$daysInHand.=(datediff('d',date('d-m-Y',time()),$row[csf('mpub_shipment_date')])-1).",";
			
			if($bookingup_date=="" || $bookingup_date=="0000-00-00 00:00:00")
			{
				$booking_date=$bookingins_date;
			}
			$WOPreparedAfter.=(datediff('d',$row[csf('insert_date')],$booking_date)-1).",";

			if($row[csf('shiping_status')]==1) {
				$shiping_status.= "FP".",";
				$to_ship++;
				$fp_ship++;
			}
			else if($row[csf('shiping_status')]==2){
				$shiping_status.= "PD".",";
				$to_ship++;
			} 
			else if($row[csf('shiping_status')]==3){
				$shiping_status.= "FS".",";
				$to_ship++;
				$f_ship++;
			} 

			
        }

        if($to_ship==$f_ship)
        {
        	$shiping_status= "Full shipped";
        }
        else if($to_ship==$fp_ship)
        {
        	$shiping_status= "Full Pending";
        }
        else{
        	$shiping_status= "Partial Delivery";
        }
       
		
		$po_no=implode(",",array_filter(array_unique(explode(",",$po_no))));
		$shipment_date=implode(",",array_filter(array_unique(explode(",",$shipment_date))));
		$lead_time=implode(",",array_filter(array_unique(explode(",",$lead_time))));
		$po_received_date=implode(",",array_filter(array_unique(explode(",",$po_received_date))));
		$factory_received_date=implode(",",array_filter(array_unique(explode(",",$factory_received_date))));
		$grouping=implode(",",array_filter(array_unique(explode(",",$grouping))));
		$file_no=implode(",",array_filter(array_unique(explode(",",$file_no))));
		
		$daysInHand=implode(",",array_filter(array_unique(explode(",",$daysInHand))));
		$WOPreparedAfter=implode(",",array_filter(array_unique(explode(",",$WOPreparedAfter))));
		$shiping_status=implode(",",array_filter(array_unique(explode(",",$shiping_status))));
		
        foreach ($nameArray as $result)
        {
            $total_set_qnty=$result[csf('total_set_qnty')];
            $colar_excess_percent=$result[csf('colar_excess_percent')];
            $cuff_excess_percent=$result[csf('cuff_excess_percent')];
            $rmg_process_breakdown=$result[csf('rmg_process_breakdown')];
            
            $booking_percent=$result[csf('booking_percent')];
			$booking_po_id=$result[csf('po_break_down_id')];

			?>
			<table width="100%" class="rpt_table"  border="1" align="left" cellpadding="0"  cellspacing="0" rules="all"  style="font-size:18px; font-family:Arial Narrow;" >
				<tr>
					<td colspan="2" rowspan="6" width="210">
						<? $nameArray_imge =sql_select("SELECT image_location FROM common_photo_library where master_tble_id='$job_no' and file_type=1"); ?>
                        <div id="div_size_color_matrix" style="float:left;">
                            <fieldset id="" width="210">
                                <legend>Image </legend>
                                <table width="208">
                                    <tr>
										<?
                                        $img_counter = 0;
                                        foreach($nameArray_imge as $result_imge)
                                        {
											if($path=="") $path='../';
											?>
											<td><img src="<? echo $path.$result_imge[csf('image_location')]; ?>" width="200" height="200" border="2" /></td>
											<?
											$img_counter++;
                                        }
                                        ?>
                                    </tr>
                                </table>
                            </fieldset>
                        </div>
					</td>
					<td width="100"><b>Job No</b></td>
					<?php 
						
					 	$revised_no_txt=$nameArray_approved_row[csf('approved_no')]-1;
						if($revised_no_txt<0)
						{
							$revised_no_txt=0;
						}

					 ?>
					<td width="140"> <span style="font-size:18px"><b style="float:left;font-size:18px"><? echo trim($txt_job_no,"'");if(!empty($revised_no_txt)){ ?>&nbsp;<span style="color: red;">/&nbsp;<? echo $revised_no_txt; }?></span></b> </span> </td>
					<td colspan="7" width="760">
						<b><?php if(!empty($result[csf('remarks')])){ ?><span style="font-size: 25px;">&#8592;</span><? echo $result[csf('remarks')]; } ?></b>
					</td>
					
				</tr>
				<tr>
					<td width="100" style="font-size:16px;"><b>Style</b></td>
					<td width="110"style="font-size:16px;" >&nbsp;<? echo $result[csf('style_ref_no')]; ?></td>
					
					<td width="100" style="font-size:16px;"><b>Dept. (Prod Code)</b></td>
					<td width="140"style="font-size:16px;" >&nbsp;<? echo $product_dept[$result[csf('product_dept')]]; if($result[csf('product_code')] !=""){ echo " (".$result[csf('product_code')].")";} ?></td>
					
					<td width="100"><b>YD</b></td>
					<td width="110"><?php echo (!empty($yd) || $job_yes_no[0][csf('yd')]==1)  ? 'Yes' : 'No' ;?></td>
					<td width="100"><?php 
					
					echo !empty($yd) ? $stype_color : ''; ?></td>
					<td width="110"><b>Fac. Order Received Date</b></td>
					<td width="100"><?php echo $factory_received_date; ?></td>
				</tr>
				<tr>
					<td width="100"><span style="font-size:18px"><b>Buyer/Agent Name</b></span></td>
					<td width="110">&nbsp;<span style="font-size:18px"><? echo $buyer_name_arr[$result[csf('buyer_name')]]; ?></span></td>
					
					<td width="100"><b>Sub Dep</b></td>
					<td width="140"><? if($result[csf('pro_sub_dep')] !=0){ echo " (".$pro_sub_dept_array[$result[csf('pro_sub_dep')]].")";}?></td>
					<td width="100"><b>AOP</b></td>
					
					<td width="110"><?php echo (!empty($aop) ||  $job_yes_no[0][csf('aop')]==1) ? 'Yes' : 'No' ;?></td>
					<td width="100"></td>
					<td width="110"><b>Booking Start<br>Appoval Date</b></td>
					<td width="100">
						<?php if(!empty($first_approve_date)){ echo date('d-m-Y',strtotime($first_approve_date)); } ?><br>
						<?php if(!empty($last_approve_date)){ echo date('d-m-Y',strtotime($last_approve_date)); } ?>
							
						</td>
					
				</tr>
				<tr>
					<td width="100"><span style="font-size:18px"><b>Garments Item</b></span></td>
					<td width="110">&nbsp;<span style="font-size:18px"> <?
                        $gmts_item_name="";
                        $gmts_item=explode(',',$result[csf('gmts_item_id')]);
                        for($g=0;$g<=count($gmts_item); $g++)
                        {
                            $gmts_item_name.= $garments_item[$gmts_item[$g]].",";
                        }
                        echo rtrim($gmts_item_name,',');
                        ?></span></td>
					
					<td width="100"><b>Season</b></td>
					<td width="140"><? echo $season_name_arr[$result[csf('season')]]; ?></td>
					<td width="100"><b>Peach</b></td>
					
					<td width="110"><?php echo  (!empty($peach) || $job_yes_no[0][csf('peach')]==1) ? 'Yes' : 'No' ;?></td>
					<td width="100"></td>
					<td width="110"><b>Approved Status</b></td>
					<td width="100">
					<? if(str_replace("'","",$id_approved_id) ==1){ ?>
					<b style="color:green"><?	echo "Yes";?></b>
					<?}else{?><b style="color:red"><?	echo "No";?></b><?}; ?></td>
					
				</tr>
				<tr>
					<td width="100"><span style="font-size:18px"><b>Sample Req. No</b></span></td>
					<td width="110">&nbsp;<span style="font-size:18px"><?php echo $requisition_no; ?></span></td>
					
					<td width="100"><b>Brand</b></td>
					<td width="140"><?php echo $brand_name_arr[$result[csf('brand_id')]]; ?></td>
					<td width="100"><b>Brushing</b></td>
					
					<td width="110"><?php echo  (!empty($brush) || $job_yes_no[0][csf('bush')]==1) ? 'Yes' : 'No' ;?></td>
					<td width="100"></td>
					<td width="110"><b>Booking Date</b></td>
					<td width="100"> <?
                        if($booking_date=="" || $booking_date=="0000-00-00 00:00:00")
                        {
                        }
                        $booking_date=$result[csf('insert_date')];
                        echo change_date_format($booking_date,'dd-mm-yyyy','-','');
                        ?>
                        	
                    </td>
					
				</tr>
				<tr>
					<td width="100"><span style="font-size:18px"><b>Booking No</b></span></td>
					<td width="110"><span style="font-size:18px"><b><? echo $result[csf('booking_no')];?></b><? echo "<br>(".$fabric_source[$result[csf('fabric_source')]].")"?></span></td>
					
					<td width="100"><b>Order Repeat No</b></td>
					<td width="140"><? echo  $result[csf('order_repeat_no')];?></td>

					<td width="100"><b>Print / Type</b></td>
					<td width="110"><?php echo  (!empty($emb_print_data[$txt_job_no][1]) || $job_yes_no[0][csf('embelishment')]==1) ? 'Yes' : 'No' ;?></td>
					<td width="100"><?php echo  !empty($emb_print_data[$txt_job_no][1]) ? chop($emb_print_data[$txt_job_no][1],",") : '' ;?></td>
					
					<td width="110"><b>Delivery Date</b></td>
					<td width="100"> 
                        <? echo change_date_format($delivery_date); ?>
                        	
                    </td>
					
				</tr>
				<tr>
					<td width="100"><span style="font-size:18px"><b>Running PO No</b></span></td>
					<td width="350" colspan="3" style="word-break: break-all;">
						<p style="font-size:12px;width: 450px;word-break: break-all;" ><?
					 echo $running_po; ?></p>
					</td>
					
					<td width="100"><b>Order Repeat Job No</b></td>
					<td width="110"><? echo $result[csf('repeat_job_no')];?></td>

					<td width="100"><b>EMB / Type</b></td>
					
					<td width="110"><?php echo  (!empty($emb_print_data[$txt_job_no][2]) ||  $job_yes_no[0][csf('embro')]==1) ? 'Yes' : 'No' ;?></td>
					<td width="100"><?php echo  !empty($emb_print_data[$txt_job_no][2]) ? chop($emb_print_data[$txt_job_no][2],",") : '' ;?></td>

					<td width="110"><b>Amendment Date</b></td>
					<td width="100"> 
                        <? if(!empty($un_approved_date)){echo change_date_format($un_approved_date);} ?>
                        	
                    </td>
					
					
				</tr>
				<tr>
					<td width="100"><span style="font-size:18px"><b>Cancelled PO</b></span></td>
					<td width="350" colspan="3" ><p style="font-size:12px;width: 450px;word-break: break-all;"><?
					 echo $cancel_po; ?></p></td>
					
					

					<?php $fab_material=array(1=>"Organic",2=>"BCI"); ?>
					<td width="100"><b>Fab. Material</b> </td>
					<td width="110"><?php echo $fab_material[$result[csf('fab_material')]]; ?></td>


					<td width="100"><b>GMT Wash</b></td>
					
					<td width="110"><?php echo  (!empty($emb_print_data[$txt_job_no][3]) ||  $job_yes_no[0][csf('wash')]==1) ? 'Yes' : 'No' ;?></td>
					<td width="100"><?php echo  !empty($emb_print_data[$txt_job_no][3]) ? chop($emb_print_data[$txt_job_no][3],",") : '' ;?></td>

					<td width="110"><b>Dealing Merchandiser</b></td>
					<td width="100"> 
                        <? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?>
                        	
                    </td>
					
				</tr>
				<tr>
					<td width="100"><span style="font-size:18px"><b>GMT/ Style Description</b></span></td>
					<td width="350" colspan="3"><span style="font-size:18px"><? echo $result[csf('style_description')]; ?></span></td>
					
					
					<?php $sustainability_standard=array(1=>"GOTS",2=>"OCS",3=>"BCI",4=>"GRS",5=>"C2C",6=>"SUPIMA",7=>"Others",8=>"Conventional"); ?>
					<td width="100"><b>Sustainability Standard</b></td>
					<td width="110"><?php echo $sustainability_standard[$result[csf('sustainability_standard')]]; ?></td>
					

					<td width="100"><b>Fab Wash</b></td>
					<td width="110"><?php echo !empty($fab_wash)? 'Yes' : 'No'; ?></td>


					<td width="100"></td>
					
					<td width="110"><b>Factory Merchandiser</b></td>
					<td width="100"> 
                        
                         <? echo $marchentrArr[$result[csf('factory_marchant')]]; ?>
                        	
                    </td>
				</tr>
				<tr>
					<td width="100"><span style="font-size:18px"><b>Fabric Description</b></span></td>
					<td width="350" colspan="3"><span style="font-size:18px">

						<? 

							$sql_fab="
							  SELECT 
							         a.lib_yarn_count_deter_id AS determin_id,
							         a.construction
							        
							        
							    FROM wo_pre_cost_fabric_cost_dtls a,
							         wo_pre_cos_fab_co_avg_con_dtls b,
							         wo_booking_dtls_hstry d
							   WHERE     a.job_no = b.job_no
							         AND a.id = b.pre_cost_fabric_cost_dtls_id
							         AND a.id = d.pre_cost_fabric_cost_dtls_id
							         AND b.po_break_down_id = d.po_break_down_id
							         AND b.color_size_table_id = d.color_size_table_id
							         AND b.pre_cost_fabric_cost_dtls_id = d.pre_cost_fabric_cost_dtls_id
							         AND d.booking_no = $txt_booking_no
							         AND a.status_active = 1
							         AND d.status_active = 1
							         AND d.is_deleted = 0
							         AND a.body_part_id in (1,20)
							         AND d.approved_no =$revised_no
							    group by a.lib_yarn_count_deter_id , a.construction
							";
							//echo $sql_fab;
							$res_fab=sql_select($sql_fab);
							$des='';
							foreach ($res_fab as $row) 
							{

								if(!empty($des))
								{
									$des."***";
								}
								$des.=$row[csf('construction')] . " ". $fabric_composition[$lip_yarn_count[$row[csf('determin_id')]]];
							}

							echo implode(",", array_unique(explode("***", $des)));

						?>
							
						</span></td>
					
					
					<td width="100"><b>Order Nature</b></td>
					<td width="110"><?php echo $fbooking_order_nature[$result[csf('quality_level')]] ?></td>
					<td width="100"><b>Running Order Qty</b></td>
					<?php $order_uom_res=sql_select( "select a.order_uom from wo_po_details_master a where a.status_active=1 and a.is_deleted=0  and a.job_no='$txt_job_no' ");
						$order_uom='';
						if(count($order_uom_res))
						{
							$order_uom=$unit_of_measurement[$order_uom_res[0][csf('order_uom')]];
						}
					 ?>
					<td width="210" colspan="2" align="center"><b> <?php echo number_format($running_po_qnty,0); ?>&nbsp;(<?=$order_uom;?>)</b></td>
					
					
					<td width="110"><b>Shipment Status</b></td>
					<td width="100"> 
                        <? echo rtrim($shiping_status,','); ?>
                        	
                    </td>
				</tr>
				<tr>
					<td width="100"><span style="font-size:18px"><b>Attention</b></span></td>
					<td  width="350" colspan="3"><span style="font-size:18px"><? echo $result[csf('attention')]?></span></td>
					
					<td width="100"><b>Quality Label</b></td>
					<td width="110"><?php echo $quality_label[$result[csf('qlty_label')]] ?></td>
					<td width="100"><b>Packing</b></td>
					
					<td width="420" colspan="4" align="center"><b> <?php echo $packing[$result[csf('packing')]]; ?></b></td>
					
					
					
					
				</tr>
				
				
				
			</table>
			<br>
			<?
		}
		
		if($cbo_fabric_source==1)
		{
			$nameArray_size=sql_select( "select size_number_id,min(id) as id, min(size_order) as size_order from wo_po_color_size_breakdown where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by size_number_id order by size_order");
			?>
			<table width="100%" style="font-family:Arial Narrow;font-size:18px" >
                <tr>
                    <td width="900">
                        <div id="div_size_color_matrix" style="float:left; max-width:1000;">
                            <fieldset id="div_size_color_matrix" style="max-width:1000;">
                                <legend>Size and Color Breakdown</legend>
                                <table  class="rpt_table"  border="1" align="left" cellpadding="0" width="1000" cellspacing="0" rules="all" >
                                    <tr>
                                        <td style="border:1px solid black"><strong>Color/Size</strong></td>
                                        <?
                                        foreach($nameArray_size  as $result_size)
                                        {
											?>
                                        	<td align="center" style="border:1px solid black"><strong><?=$size_library[$result_size[csf('size_number_id')]];?></strong></td>
                                        <? } ?>
                                        <td style="border:1px solid black; width:130px" align="center"><strong> Total Order Qty(Pcs)</strong></td>
                                        <td style="border:1px solid black; width:80px" align="center"><strong> Excess %</strong></td>
                                        <td style="border:1px solid black; width:130px" align="center"><strong> Total Plan Cut Qty(Pcs)</strong></td>
                                    </tr>
                                    <?
                                    $color_size_order_qnty_array=array(); $color_size_qnty_array=array(); $size_tatal=array(); $size_tatal_order=array();
                                    for($c=0;$c<count($gmts_item); $c++)
                                    {
										$item_size_tatal=array(); $item_size_tatal_order=array(); $item_grand_total=0; $item_grand_total_order=0;
										$nameArray_color=sql_select( "select  color_number_id,min(id) as id,min(color_order) as color_order from wo_po_color_size_breakdown where  item_number_id=$gmts_item[$c] and po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by color_number_id  order by color_order");
										?>
										<tr>
											<td style="border:1px solid black; text-align:center;" colspan="<? echo count($nameArray_size)+3;?>"><strong><? echo $garments_item[$gmts_item[$c]];?></strong></td>
										</tr>
										<?
										foreach($nameArray_color as $result_color)
										{
											?>
											<tr>
                                                <td align="center" style="border:1px solid black"><?=$color_library[$result_color[csf('color_number_id')]]; ?></td>
                                                <?
                                                $color_total=0; $color_total_order=0;
                                                foreach($nameArray_size  as $result_size)
                                                {
													$nameArray_color_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as  order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$result_color[csf('color_number_id')]."  and item_number_id=$gmts_item[$c] and  status_active=1 and is_deleted =0");
													foreach($nameArray_color_size_qnty as $result_color_size_qnty)
													{
														?>
														<td style="border:1px solid black; text-align:center; font-size:18px;">
														<?
														if($result_color_size_qnty[csf('plan_cut_qnty')]!= "")
														{
															echo number_format($result_color_size_qnty[csf('order_quantity')],0);
															$color_total += $result_color_size_qnty[csf('plan_cut_qnty')] ;
															$color_total_order += $result_color_size_qnty[csf('order_quantity')] ;
															$item_grand_total+=$result_color_size_qnty[csf('plan_cut_qnty')];
															$item_grand_total_order+=$result_color_size_qnty[csf('order_quantity')];
															$grand_total +=$result_color_size_qnty[csf('plan_cut_qnty')];
															$grand_total_order +=$result_color_size_qnty[csf('order_quantity')];
															
															$color_size_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
															$color_size_order_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
															if (array_key_exists($result_size[csf('size_number_id')], $size_tatal))
															{
																$size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
																$size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
															}
															else
															{
																$size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
																$size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
															}
															if (array_key_exists($result_size[csf('size_number_id')], $item_size_tatal))
															{
																$item_size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
																$item_size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
															}
															else
															{
																$item_size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
																$item_size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
															}
														}
														else echo "0";
														?>
														</td>
														<?
													}
                                                }
                                                ?>
                                                <td style="border:1px solid black; text-align:center; font-size:18px;"><?=number_format(round($color_total_order),0); ?></td>
                                                <td style="border:1px solid black; text-align:center; font-size:18px;"><? $excexss_per=($color_total-$color_total_order)/$color_total_order*100; echo number_format($excexss_per,2)." %"; ?></td>
                                                <td style="border:1px solid black; text-align:center; font-size:18px;"><?=number_format(round($color_total),0); ?></td>
											</tr>
											<?
										}
										?>
										
										<td align="center" style="border:1px solid black"><strong>Sub Total</strong></td>
										<?
										foreach($nameArray_size  as $result_size)
										{
											?>
											<td style="border:1px solid black;  text-align:center; font-size:18px;"><? echo $item_size_tatal_order[$result_size[csf('size_number_id')]];  ?></td>
											<?
										}
										?>
										<td style="border:1px solid black;  text-align:center; font-size:18px;"><?  echo number_format(round($item_grand_total_order),0); ?></td>
										<td style="border:1px solid black;  text-align:center; font-size:18px;"><? $excess_item_gra_tot=($item_grand_total-$item_grand_total_order)/$item_grand_total_order*100; echo number_format($excess_item_gra_tot,2)." %"; ?></td>
										<td style="border:1px solid black;  text-align:center; font-size:18px;"><?  echo number_format(round($item_grand_total),0); ?></td>
										</tr>
										<?
                                    }
                                    ?>
                                    <tr>
                                    	<td style="border:1px solid black; font-size:18px;" align="center" colspan="<? echo count($nameArray_size)+3; ?>"><strong>&nbsp;</strong></td>
                                    </tr>
                                    <tr>
                                        <td align="center" style="border:1px solid black"><strong>Grand Total</strong></td>
                                        <?
                                        foreach($nameArray_size  as $result_size)
                                        {
											?>
											<td style="border:1px solid black; text-align:center; font-size:18px;"><? echo $size_tatal_order[$result_size[csf('size_number_id')]]; ?></td>
											<?
                                        }
                                        ?>
                                        <td style="border:1px solid black; text-align:center; font-size:18px;"><?=number_format(round($grand_total_order),0); ?></td>
                                        <td style="border:1px solid black; text-align:center; font-size:18px;"><? $excess_gra_tot=($grand_total-$grand_total_order)/$grand_total_order*100; echo number_format($excess_gra_tot,2)." %"; ?></td>
                                        <td style="border:1px solid black; text-align:center; font-size:18px;"><?  echo number_format(round($grand_total),0); ?></td>
                                    </tr>
                                </table>
                            </fieldset>
                        </div>
                    </td>
                    <td width="130" valign="top" align="left">
                        <div id="div_size_color_matrix" style="float:left;">
							
                        </div>
                    </td>
                    <td width="200" valign="top" align="right">
						
                        <div id="div_size_color_matrix" style="float:right;font-size:18px;font-family:Arial Narrow;">
                           <? $rmg_process_breakdown_arr=explode('_',$rmg_process_breakdown); ?>
                            <fieldset id="" >
                                <legend>RMG Process Loss % </legend>
                                <table width="180" class="rpt_table" border="1" rules="all">
									<?
                                    if(number_format($rmg_process_breakdown_arr[8],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Cut Panel rejection <!-- Extra Cutting % breack Down 8--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[8],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[2],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Chest Printing <!-- Printing % breack Down 2--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[2],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[10],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Neck/Sleeve Printing <!-- New breack Down 10--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[10],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[1],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Embroidery   <!-- Embroidery  % breack Down 1--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[1],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[4],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Sewing /Input<!-- Sewing % breack Down 4--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[4],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[3],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Garments Wash <!-- Washing %breack Down 3--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[3],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[15],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Gmts Finishing <!-- Washing %breack Down 3--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[15],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[11],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Others <!-- New breack Down 11--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[11],2); ?></td>
										</tr>
										<?
                                    }
                                    $gmts_pro_sub_tot=$rmg_process_breakdown_arr[8]+$rmg_process_breakdown_arr[2]+$rmg_process_breakdown_arr[10]+$rmg_process_breakdown_arr[1]+$rmg_process_breakdown_arr[4]+$rmg_process_breakdown_arr[3]+$rmg_process_breakdown_arr[11]+$rmg_process_breakdown_arr[15];
                                    if($gmts_pro_sub_tot>0)
                                    {
										?>
										<tr>
                                            <td width="130">Sub Total <!-- New breack Down 11--></td>
                                            <td align="right"><? echo number_format($gmts_pro_sub_tot,2); ?></td>
										</tr>
										<?
                                    }
                                    ?>
                                </table>
                            </fieldset>
                            <fieldset id="" >
                                <legend>Fabric Process Loss % </legend>
                                <table width="180" class="rpt_table" border="1" rules="all" style="font-family:Arial Narrow;">
                                    <?
                                    if(number_format($rmg_process_breakdown_arr[6],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Knitting  <!--  Knitting % breack Down 6--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[6],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[12],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Yarn Dyeing  <!--  New breack Down 12--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[12],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[5],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Dyeing & Finishing  <!-- Finishing % breack Down 5--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[5],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[13],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130"> All Over Print <!-- new  breack Down 13--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[13],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[14],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Lay Wash (Fabric) <!-- new  breack Down 14--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[14],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[7],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Dying   <!-- breack Down 7--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[7],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[0],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Cutting (Fabric) <!-- Cutting % breack Down 0--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[0],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[9],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Others  <!-- Others% breack Down 9--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[9],2); ?></td>
										</tr>
										<?
                                    }

                                    $fab_proce_sub_tot=$rmg_process_breakdown_arr[6]+$rmg_process_breakdown_arr[12]+$rmg_process_breakdown_arr[5]+$rmg_process_breakdown_arr[13]+$rmg_process_breakdown_arr[14]+$rmg_process_breakdown_arr[7]+$rmg_process_breakdown_arr[0]+$rmg_process_breakdown_arr[9];
                                    if(fab_proce_sub_tot>0)
                                    {
										?>
										<tr>
                                            <td width="130">Sub Total  <!-- Others% breack Down 9--></td>
                                            <td align="right"><? echo number_format($fab_proce_sub_tot,2); ?></td>
										</tr>
										<?
                                    }
                                    if($gmts_pro_sub_tot+$fab_proce_sub_tot>0)
                                    {
										?>
										<tr>
                                            <td width="130">Grand Total  <!-- Others% breack Down 9--></td>
                                            <td align="right"><? echo number_format($gmts_pro_sub_tot+$fab_proce_sub_tot,2); ?></td>
										</tr>
										<?
                                    }
                                    ?>
                                </table>
                            </fieldset>
                        </div>
                    </td>
                </tr>
			</table>
			<?
		}
		// if($cbo_fabric_source==1) end
		
	  	?>
		<br/>
		<br>
      	<!--  Here will be the main portion  -->
		<?
        $costing_per=""; $costing_per_qnty=0;
        $costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='$txt_job_no'");
        if($costing_per_id==1)
        {
			$costing_per="1 Dzn";
			$costing_per_qnty=12;
        }
        if($costing_per_id==2)
        {
			$costing_per="1 Pcs";
			$costing_per_qnty=1;
        }
        if($costing_per_id==3)
        {
			$costing_per="2 Dzn";
			$costing_per_qnty=24;
        }
        if($costing_per_id==4)
        {
			$costing_per="3 Dzn";
			$costing_per_qnty=36;
        }
        if($costing_per_id==5)
        {
			$costing_per="4 Dzn";
			$costing_per_qnty=48;
        }

        $process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no='$txt_job_no'");
		//$s_length=return_field_value( "stitch_length", "fabric_mapping","mst_id=$determin_id");;
		$s_lengthArr=return_library_array( "select mst_id, stitch_length from fabric_mapping",'mst_id','stitch_length');


		
		if($cbo_fabric_source==1)
		{
			$fb_desc_sq="SELECT min(a.id) as fabric_cost_dtls_id, a.lib_yarn_count_deter_id as determin_id, a.item_number_id, a.body_part_id, a.color_type_id, a.construction, a.composition, a.gsm_weight, min(a.width_dia_type) as width_dia_type,  b.dia_width, avg(b.cons) as cons, avg(b.process_loss_percent) as process_loss_percent, avg(b.requirment) as requirment FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls_hstry d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id  and a.id=d.pre_cost_fabric_cost_dtls_id  and b.po_break_down_id=d.po_break_down_id and b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and d.booking_no=$txt_booking_no and a.status_active=1 and d.status_active=1 and d.is_deleted=0 and d.approved_no=$revised_no group by a.body_part_id, a.lib_yarn_count_deter_id, a.color_type_id, a.item_number_id, a.construction, a.composition, a.gsm_weight,  b.dia_width order by fabric_cost_dtls_id, a.body_part_id, b.dia_width";
			//echo $fb_desc_sq;
			$nameArray_fabric_description= sql_select($fb_desc_sq);
			?>
			<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" style="font-family:Arial Narrow;font-size:18px;" >
                <tr align="center">
                    <th colspan="3" align="left">Body Part</th>
                    <?
                    foreach($nameArray_fabric_description  as $result_fabric_description)
                    {
						if( $result_fabric_description[csf('body_part_id')] == "") echo "<td  colspan='2'>&nbsp</td>";
						else echo "<td colspan='2'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";
                    }
                    ?>
                    <td rowspan="10" width="50"><p>Total  Finish Fabric (<? echo $unit_of_measurement[$booking_uom];?>)</p></td>
                    <td rowspan="10" width="50"><p>Total Grey Fabric (<? echo $unit_of_measurement[$booking_uom];?>)</p></td>
                    <td rowspan="10" width="50"><p>Process Loss % </p></td>
                </tr>
                <tr align="center">
                    <th colspan="3" align="left">Color Type</th>
                    <?
                    foreach($nameArray_fabric_description  as $result_fabric_description)
                    {
						if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";
						else echo "<td  colspan='2'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";
                    }
                    ?>
                </tr>
                <tr align="center">
                    <th colspan="3" align="left">Fabric Construction</th>
                    <?
                    foreach($nameArray_fabric_description  as $result_fabric_description)
                    {
						if($result_fabric_description[csf('construction')]== "") echo "<td  colspan='2'>&nbsp</td>";
						else echo "<td  colspan='2'>". $result_fabric_description[csf('construction')]."</td>";
                    }
                    ?>
                </tr>
                 <tr align="center">
                    <th colspan="3" align="left">Fabric Composition</th>
                    <?
                    foreach($nameArray_fabric_description  as $result_fabric_description)
                    {
						if($result_fabric_description[csf('determin_id')]== "") echo "<td  colspan='2'>&nbsp</td>";
						else echo "<td  colspan='2'>". $fabric_composition[$lip_yarn_count[$result_fabric_description[csf('determin_id')]]]."</td>";
                    }
                    ?>
                </tr>
                <tr align="center">
                    <th colspan="3" align="left">Yarn Composition</th>
                    <?
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
						if( $result_fabric_description[csf('composition')] == "") echo "<td colspan='2' >&nbsp</td>";
						else echo "<td colspan='2' >".$result_fabric_description[csf('composition')]."</td>";
                    }
                    ?>
                </tr>
                <tr align="center">
                	<th colspan="3" align="left">GSM</th>
					<?
                    foreach($nameArray_fabric_description  as $result_fabric_description)
                    {
						if( $result_fabric_description[csf('gsm_weight')] == "") echo "<td colspan='2'>&nbsp</td>";
						else echo "<td colspan='2' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";
                    }
                    ?>
                </tr>
               
                <tr align="center">
                    <th colspan="3" align="left">Dia/Width (Inch)</th>
                    <?
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
						if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='2'>&nbsp</td>";
						else echo "<td colspan='2' align='center'>".$result_fabric_description[csf('dia_width')].",".$fabric_typee[$result_fabric_description[csf('width_dia_type')]]."</td>";
                    }
                    ?>
                </tr>
                <tr align="center">
                    <th   colspan="3" align="left">Consumption For <? echo $costing_per; ?></th>
                    <?
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
						if( $result_fabric_description[csf('requirment')] == "")   echo "<td colspan='2'>&nbsp</td>";
						else echo "<td colspan='2' align='center'>A Fin: ".number_format($result_fabric_description[csf('cons')],4).", Grey: ".number_format($result_fabric_description[csf('requirment')],4)."</td>";
                    }
                    ?>
                </tr>
                <tr>
                	<th colspan="<? echo  count($nameArray_fabric_description)*2+3; ?>" align="left" style="height:30px">&nbsp;</th>
                </tr>
                <tr>
                    <th width="120" align="left">Fabric Color</th>
                    <th width="120" align="left">GMT Color</th>
                    <th width="120" align="left">Lab Dip No</th>
                    <?
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
                   		echo "<th width='50'>Finish</th><th width='50' >Grey</th>";
                    }
                    ?>
                </tr>
                <?
                $color_wise_wo_sql_s = "SELECT a.item_number_id, a.body_part_id, a.color_type_id, a.construction, a.composition, a.gsm_weight, a.lib_yarn_count_deter_id, b.dia_width, b.remarks, d.fabric_color_id, d.fin_fab_qnty as fin_fab_qnty, d.grey_fab_qnty as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a join wo_pre_cos_fab_co_avg_con_dtls b on  a.id=b.pre_cost_fabric_cost_dtls_id join wo_po_color_size_breakdown c on c.id=b.color_size_table_id join wo_booking_dtls_hstry d on b.po_break_down_id=d.po_break_down_id and b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id WHERE  d.booking_no =$txt_booking_no and a.uom=12 and d.status_active=1 and  d.is_deleted=0 and d.approved_no=$revised_no";
                //echo $color_wise_wo_sql_s;
                
                $color_wise_wo_sql_res=sql_select($color_wise_wo_sql_s); $fin_grey_qty_arr=array(); $fin_grey_color_qty_arr=array();
                foreach($color_wise_wo_sql_res as $row)
                {
					$fin_grey_key = $row[csf('item_number_id')].'**'.$row[csf('body_part_id')].'**'.$row[csf('color_type_id')].'**'.$row[csf('construction')].'**'.$row[csf('composition')].'**'.$row[csf('gsm_weight')].'**'.$row[csf('lib_yarn_count_deter_id')].'**'.$row[csf('dia_width')];
					$fin_grey_color_key = $row[csf('item_number_id')].'**'.$row[csf('body_part_id')].'**'.$row[csf('color_type_id')].'**'.$row[csf('construction')].'**'.$row[csf('composition')].'**'.$row[csf('gsm_weight')].'**'.$row[csf('lib_yarn_count_deter_id')].'**'.$row[csf('dia_width')].'**'.$row[csf('fabric_color_id')];
					$fin_grey_qty_arr[$fin_grey_key]['fin'] += $row[csf('fin_fab_qnty')];
					$fin_grey_qty_arr[$fin_grey_key]['grey'] += $row[csf('grey_fab_qnty')];
					$fin_grey_color_qty_arr[$fin_grey_color_key]['fin'] +=$row[csf('fin_fab_qnty')];
					$fin_grey_color_qty_arr[$fin_grey_color_key]['grey'] +=$row[csf('grey_fab_qnty')];
                }
                unset($color_wise_wo_sql_res);
                
                $gmt_color_library=array();

                $gmt_color_data=sql_select("select a.id,b.gmts_color_id, b.contrast_color_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b WHERE a.id=b.pre_cost_fabric_cost_dtls_id and a.fab_nature_id=$cbo_fabric_natu and a.fabric_source =$cbo_fabric_source and a.job_no ='$txt_job_no' and a.status_active=1 and b.status_active=1 order by a.id");
                foreach( $gmt_color_data as $gmt_color_row)
                {
                	$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]][$gmt_color_row[csf("gmts_color_id")]]=$color_library[$gmt_color_row[csf("gmts_color_id")]];
                }
                
                $lab_dip_no_arr=array();
                $lab_dip_no_sql=sql_select("select lapdip_no, color_name_id from wo_po_lapdip_approval_info where job_no_mst='$txt_job_no' and status_active=1 and is_deleted=0 and approval_status=3");
                foreach($lab_dip_no_sql as $row)
                {
                	$lab_dip_no_arr[$row[csf('color_name_id')]]=$row[csf('lapdip_no')];
                }
                unset($lab_dip_no_sql);
                
                $grand_total_fin_fab_qnty=0; $grand_total_grey_fab_qnty=0; $grand_totalcons_per_finish=0; $grand_totalcons_per_grey=0;
                $color_wise_wo_sql=sql_select("select fabric_color_id FROM wo_booking_dtls_hstry WHERE booking_no =$txt_booking_no and status_active=1 and is_deleted=0 and approved_no=$revised_no group by fabric_color_id");
                foreach($color_wise_wo_sql as $color_wise_wo_result)
                {
					?>
					<tr>
                        <td width="120" align="left"><? echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]]; ?></td>
                        <td><? echo implode(",",$gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]]); ?></td>
                        <td width="120" align="left">
							<?
                            $lapdip_no=""; $lapdip_no=$lab_dip_no_arr[$color_wise_wo_result[csf('fabric_color_id')]];
                            if($lapdip_no=="") echo "&nbsp;"; else echo $lapdip_no;
                            ?>
                        </td>
                        <?
                        $total_fin_fab_qnty=0; $total_grey_fab_qnty=0;
                        foreach($nameArray_fabric_description as $result_fabric_description)
                        {
							$color_wo_fin_qnty=0; $color_wo_grey_qnty=0;
							$fin_gray_color = $result_fabric_description[csf('item_number_id')].'**'.$result_fabric_description[csf('body_part_id')].'**'.$result_fabric_description[csf('color_type_id')].'**'.$result_fabric_description[csf('construction')].'**'.$result_fabric_description[csf('composition')].'**'.$result_fabric_description[csf('gsm_weight')].'**'.$result_fabric_description[csf('determin_id')].'**'.$result_fabric_description[csf('dia_width')].'**'.$color_wise_wo_result[csf('fabric_color_id')];
							$color_wo_fin_qnty=$fin_grey_color_qty_arr[$fin_gray_color]['fin'];
							
							$color_wo_grey_qnty=$fin_grey_color_qty_arr[$fin_gray_color]['grey'];
							?>
							<td width='50' align='center' style="font-size:18px;">
								<?
                                if($color_wo_fin_qnty!="")
                                {
                                    echo number_format($color_wo_fin_qnty,2) ;
                                    $total_fin_fab_qnty+=$color_wo_fin_qnty;
                                }
                                ?>
							</td>
							<td width='50' align='center' style="font-size:18px;">
								<?
                                if($color_wo_grey_qnty!="")
                                {
									echo number_format($color_wo_grey_qnty,2);
									$total_grey_fab_qnty+=$color_wo_grey_qnty;
                                }
                                ?>
							</td>
							<?
                        }
                        ?>
                        <td align="center" style="font-size:18px;"><? echo number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
                        <td align="center" style="font-size:18px;"><? echo number_format($total_grey_fab_qnty,2); $grand_total_grey_fab_qnty+=$total_grey_fab_qnty;?></td>
                        <td align="center" style="font-size:18px;">
                        <?
                        if($process_loss_method==1)
                        {
                        	$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_fin_fab_qnty)*100;
                        }
                        if($process_loss_method==2)
                        {
                        	$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_grey_fab_qnty)*100;
                        }
                        echo number_format($process_percent,2);
                        ?>
                        </td>
					</tr>
					<?
                }
                ?>
                <tr style=" font-weight:bold">
                    <th width="120" align="left">&nbsp;</th>
                    <td width="120" align="left">&nbsp;</td>
                    <td width="120" align="left"><strong>Total</strong></td>
                    <?
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
						$wo_fin_qnty=0; $wo_grey_qnty=0;
						$fin_key = $result_fabric_description[csf('item_number_id')].'**'.$result_fabric_description[csf('body_part_id')].'**'.$result_fabric_description[csf('color_type_id')].'**'.$result_fabric_description[csf('construction')].'**'.$result_fabric_description[csf('composition')].'**'.$result_fabric_description[csf('gsm_weight')].'**'.$result_fabric_description[csf('determin_id')].'**'.$result_fabric_description[csf('dia_width')];
						
						$wo_fin_qnty=$fin_grey_qty_arr[$fin_key]['fin'];
						$wo_grey_qnty=$fin_grey_qty_arr[$fin_key]['grey'];
						?>
						<td width='50' align='center' style="font-size:18px;"><?  echo number_format($wo_fin_qnty,2) ;?></td><td width='50' align='center' style="font-size:18px;" > <? echo number_format($wo_grey_qnty,2);?></td>
						<?
                    }
                    ?>
                    <td align="center" style="font-size:18px;"><? echo number_format($grand_total_fin_fab_qnty,2);?></td>
                    <td align="center" style="font-size:18px;"><? echo number_format($grand_total_grey_fab_qnty,2);?></td>
                    <td align="center" style="font-size:18px;">
						<?
                        if($process_loss_method==1)// markup
                        {
                            $totalprocess_percent=(($grand_total_grey_fab_qnty-$grand_total_fin_fab_qnty)/$grand_total_fin_fab_qnty)*100;
                        }
                        
                        if($process_loss_method==2) //margin
                        {
                            $totalprocess_percent=(($grand_total_grey_fab_qnty-$grand_total_fin_fab_qnty)/$grand_total_grey_fab_qnty)*100;
                        }
                        echo number_format($totalprocess_percent,2);
                        ?>
                    </td>
                </tr>
                <tr style="font-weight:bold">
                    <th width="120" align="left">&nbsp;</th>
                    <td width="120" align="left">&nbsp;</td>
                    <td width="120" align="left"><strong>Consumption For <? echo $costing_per; ?></strong></td>
						<?
                        foreach($nameArray_fabric_description as $result_fabric_description)
                        {
							?>
							<td width='50' align='center' style="font-size:18px;"><?  //echo number_format(($color_wise_wo_result_qnty['fin_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4) ;?></td>
                            <td width='50' align='right' > <? //echo number_format(($color_wise_wo_result_qnty['grey_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4);?></td>
							<?
                        }
                        ?>
                    <td align="center" style="font-size:18px;">
						<?
                        //echo $grand_total_fin_fab_qnty;
                        echo number_format(($grand_total_fin_fab_qnty/$grand_total)*($total_set_qnty*$costing_per_qnty),4);
                        $grand_total_fin_fab_qnty_dzn=number_format(($grand_total_fin_fab_qnty/$grand_total)*($total_set_qnty*$costing_per_qnty),4);
                        ?>
                    </td>
                    <td align="center" style="font-size:18px;"><? echo number_format(($grand_total_grey_fab_qnty/$grand_total)*($total_set_qnty*$costing_per_qnty),4);$grand_total_grey_fab_qnty_dzn=number_format(($grand_total_grey_fab_qnty/$grand_total)*($total_set_qnty*$costing_per_qnty),4)?></td>
                    <td align="center" style="font-size:18px;">
						<?
                        if($process_loss_method==1)
                        {
                        	$totalprocess_percent_dzn=(($grand_total_grey_fab_qnty_dzn-$grand_total_fin_fab_qnty_dzn)/$grand_total_fin_fab_qnty_dzn)*100;
                        }
                        
                        if($process_loss_method==2)
                        {
                        	$totalprocess_percent_dzn=(($grand_total_grey_fab_qnty_dzn-$grand_total_fin_fab_qnty_dzn)/$grand_total_grey_fab_qnty_dzn)*100;
                        }
                        echo number_format($totalprocess_percent_dzn,2);
                        ?>
                    </td>
                </tr>
			</table>
			<?
		}
		//echo "kausar"; die;

		if($cbo_fabric_source==2)
		{
			$nameArray_fabric_description= sql_select("select min(a.id) as fabric_cost_dtls_id, a.lib_yarn_count_deter_id as determin_id, a.body_part_id, a.color_type_id, a.construction, a.composition, a.gsm_weight, min(a.width_dia_type) as width_dia_type, b.dia_width, avg(a.avg_finish_cons) as cons, avg(b.process_loss_percent) as process_loss_percent, avg(a.avg_cons) as requirment FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls_hstry d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and b.po_break_down_id=d.po_break_down_id and b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and d.status_active=1 and d.is_deleted=0 and d.approved_no=$revised_no group by a.body_part_id, a.lib_yarn_count_deter_id, a.color_type_id, a.construction, a.composition, a.gsm_weight, b.dia_width order by fabric_cost_dtls_id, a.body_part_id, b.dia_width");


			?>
			<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" style="font-family:Arial Narrow;font-size:18px;" >
                <tr align="center">
                    <th colspan="3" align="left">Body Part</th>
                    <?
                    foreach($nameArray_fabric_description  as $result_fabric_description)
                    {
						if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";
						else echo "<td  colspan='3'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";
                    }
                    ?>
                    <td rowspan="10" width="50"><p>Total Fabric (<? echo $unit_of_measurement[$booking_uom];?>)</p></td>
                    <td rowspan="10" width="50"><p>Avg Rate (<? echo $unit_of_measurement[$booking_uom];?>)</p></td>
                    <td rowspan="10" width="50"><p>Amount </p></td>
                </tr>
                <tr align="center"><th colspan="3" align="left">Color Type</th>
					<?
                    foreach($nameArray_fabric_description  as $result_fabric_description)
                    {
                        if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";
                        else echo "<td  colspan='3'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";
                    }
                    ?>
                </tr>
                <tr align="center"><th colspan="3" align="left">Fabric Construction</th>
					<?
                    foreach($nameArray_fabric_description  as $result_fabric_description)
                    {
						if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='3'>&nbsp</td>";
						else  echo "<td  colspan='3'>". $result_fabric_description[csf('construction')]."</td>";
                    }
                    ?>
                </tr>
                <tr align="center"><th colspan="3" align="left">Fabric Composition</th>
					<?
                    foreach($nameArray_fabric_description  as $result_fabric_description)
                    {
						if( $result_fabric_description[csf('determin_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";
						else  echo "<td  colspan='3'>". $fabric_composition[$lip_yarn_count[$result_fabric_description[csf('determin_id')]]]."</td>";

						
                    }
                    ?>
                </tr>
                <tr align="center"><th colspan="3" align="left">Yarn Composition</th>
					<?
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
						if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='3' >&nbsp</td>";
						else echo "<td colspan='3' >".$result_fabric_description[csf('composition')]."</td>";
                    }
                    ?>
                </tr>
                <tr align="center"><th  colspan="3" align="left">GSM</th>
					<?
                    foreach($nameArray_fabric_description  as $result_fabric_description)
                    {
						if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='3'>&nbsp</td>";
						else  echo "<td colspan='3' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";
                    }
                    ?>
                </tr>
                
                <tr align="center"><th colspan="3" align="left">Dia/Width (Inch)</th>
					<?
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
						if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='3'>&nbsp</td>";
						else echo "<td colspan='3' align='center'>".$result_fabric_description[csf('dia_width')].",".$fabric_typee[$result_fabric_description[csf('width_dia_type')]]."</td>";
                    }
                    ?>
                </tr>
                <tr align="center"><th   colspan="3" align="left">Consumption For <? echo $costing_per; ?></th>
					<?
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
						if( $result_fabric_description[csf('requirment')] == "")   echo "<td colspan='3'>&nbsp</td>";
						else echo "<td colspan='3' align='center'>Fin: ".number_format($result_fabric_description[csf('cons')],2).", Grey: ".number_format($result_fabric_description[csf('requirment')],2)."</td>";
                    }
                    ?>
                </tr>
                <tr>
                	<th colspan="<? echo  count($nameArray_fabric_description)*3+3; ?>" align="left" style="height:30px">&nbsp;</th>
                </tr>
                <tr>
                    <th width="120" align="left">Fabric Color</th>
                    <th width="120" align="left">GMT Color</th>
                    <th width="120" align="left">Lab Dip No</th>
                    <?
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
                    	echo "<th width='50'>Fab. Qty</th><th width='50' >Rate</th><th width='50' >Amount</th>";
                    }
                    ?>
                </tr>
                <?
                $gmt_color_library=array();
                $gmt_color_data=sql_select("select a.id as fab_id,b.gmts_color_id, b.contrast_color_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b  WHERE a.id=b.pre_cost_fabric_cost_dtls_id and a.fab_nature_id=$cbo_fabric_natu and a.fabric_source =$cbo_fabric_source and a.job_no ='$txt_job_no' and a.status_active=1 and b.status_active=1 order by a.id");
                foreach( $gmt_color_data as $gmt_color_row)
                {
                	$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]][$gmt_color_row[csf("gmts_color_id")]]=$color_library[$gmt_color_row[csf("gmts_color_id")]];
                }
                
                $grand_total_fin_fab_qnty=0; $grand_total_amount=0;
                
                $color_wise_wo_sql=sql_select("select fabric_color_id FROM wo_booking_dtls WHERE booking_no =$txt_booking_no and status_active=1 and is_deleted=0 group by fabric_color_id");
                foreach($color_wise_wo_sql as $color_wise_wo_result)
                {
                ?>
                <tr>
                
                <td  width="120" align="left">
                <?
                echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
                
                
                ?>
                </td>
                <td>
                <?
                echo implode(",",$gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]]);
                ?>
                </td>
                <td  width="120" align="left">
                <?
                $lapdip_no="";
                $lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$txt_job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."");
                if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no;
                ?>
                </td>
                <?
                $total_fin_fab_qnty=0;
                $total_amount=0;
                
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
                if($db_type==0)
                {
                $color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty, avg(d.rate) as rate FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls_hstry d
                WHERE a.job_no=b.job_no and
                a.id=b.pre_cost_fabric_cost_dtls_id and
                c.job_no_mst=a.job_no and
                c.id=b.color_size_table_id and
                b.po_break_down_id=d.po_break_down_id and
                b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
                d.booking_no =$txt_booking_no and
                a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
                a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
                a.construction='".$result_fabric_description[csf('construction')]."' and
                a.composition='".$result_fabric_description[csf('composition')]."' and
                a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
                a.lib_yarn_count_deter_id='".$result_fabric_description[csf('determin_id')]."' and
                b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
                d.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
                d.status_active=1 and
                d.is_deleted=0
                and d.approved_no=$revised_no
                ");
                }
                if($db_type==2)
                {
                
                $color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls_hstry d
                WHERE a.job_no=b.job_no and
                a.id=b.pre_cost_fabric_cost_dtls_id and
                c.job_no_mst=a.job_no and
                c.id=b.color_size_table_id and
                b.po_break_down_id=d.po_break_down_id and
                b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
                d.booking_no =$txt_booking_no and
                a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
                a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
                a.construction='".$result_fabric_description[csf('construction')]."' and
                a.composition='".$result_fabric_description[csf('composition')]."' and
                a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
                a.lib_yarn_count_deter_id='".$result_fabric_description[csf('determin_id')]."' and
                b.dia_width='".$result_fabric_description[csf('dia_width')]."' and

                nvl(d.fabric_color_id,0)=nvl('".$color_wise_wo_result[csf('fabric_color_id')]."',0) and
                d.status_active=1 and
                d.is_deleted=0
                and d.approved_no=$revised_no
                ");
                }
                list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
                ?>
                <td width='50' align='right'>
                <?
                if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
                {
                echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2) ;
                $total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
                }
                ?>
                </td>
                <td width='50' align='right' >
                <?
                if($color_wise_wo_result_qnty[csf('rate')]!="")
                {
                echo number_format($color_wise_wo_result_qnty[csf('rate')],2);
                }
                ?>
                </td>
                <td width='50' align='right' >
                <?
                $amount=$color_wise_wo_result_qnty[csf('grey_fab_qnty')]*$color_wise_wo_result_qnty[csf('rate')];
                if($amount!="")
                {
                echo number_format($amount,2);
                $total_amount+=$amount;
                }
                ?>
                </td>
                <?
                }
                ?>
                <td align="right"><? echo number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
                <td align="right"><? echo number_format($total_amount/$total_fin_fab_qnty,2); $grand_total_amount+=$total_amount;?></td>
                <td align="right">
                <?
                echo number_format($total_amount,2);
                
                ?>
                </td>
                </tr>
                <?
                }
                ?>
                <tr style=" font-weight:bold">
                <!--<td  width="120" align="left">&nbsp;</td>-->
                <th  width="120" align="left">&nbsp;</th>
                <td  width="120" align="left">&nbsp;</td>
                <td  width="120" align="left"><strong>Total</strong></td>
                <?
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
                $color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls_hstry d
                WHERE a.job_no=b.job_no and
                a.id=b.pre_cost_fabric_cost_dtls_id and
                c.job_no_mst=a.job_no and
                c.id=b.color_size_table_id and
                b.po_break_down_id=d.po_break_down_id and
                b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
                d.booking_no =$txt_booking_no and
                a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
                a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
                a.construction='".$result_fabric_description[csf('construction')]."' and
                a.composition='".$result_fabric_description[csf('composition')]."' and
                a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
                a.lib_yarn_count_deter_id='".$result_fabric_description[csf('determin_id')]."' and
                b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
                d.status_active=1 and
                d.is_deleted=0
                and d.approved_no=$revised_no
                ");
                list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
                ?>
                <td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2) ;?></td>
                <td width='50' align='right' > <? //echo number_format($color_wise_wo_result_qnty['grey_fab_qnty'],2);?></td>
                <td width='50' align='right' > <? //echo number_format($color_wise_wo_result_qnty['grey_fab_qnty'],2);?></td>
                <?
                }
                ?>
                <td align="right"><? echo number_format($grand_total_fin_fab_qnty,2);?></td>
                <td align="right"><? echo number_format($grand_total_amount/$grand_total_fin_fab_qnty,2);?></td>
                <td align="right">
                <?
                echo number_format($grand_total_amount,2);
                ?>
                </td>
                </tr>
                <tr style="font-weight:bold">
                <!--<td  width="120" align="left">&nbsp;</td>-->
                <th  width="120" align="left">&nbsp;</th>
                <td  width="120" align="left">&nbsp;</td>
                <td  width="120" align="left"><strong>Consumption For <? echo $costing_per; ?></strong></td>
                <?
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
                $color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls_hstry d
                WHERE a.job_no=b.job_no and
                a.id=b.pre_cost_fabric_cost_dtls_id and
                c.job_no_mst=a.job_no and
                c.id=b.color_size_table_id and
                b.po_break_down_id=d.po_break_down_id and
                b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
                d.booking_no =$txt_booking_no and
                a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
                a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
                a.construction='".$result_fabric_description[csf('construction')]."' and
                a.composition='".$result_fabric_description[csf('composition')]."' and
                a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
                a.lib_yarn_count_deter_id='".$result_fabric_description[csf('determin_id')]."' and
                b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
                d.status_active=1 and
                d.is_deleted=0
                and d.approved_no=$revised_no
                ");
                list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
                
                ?>
                <td width='50' align='right'><?  //echo number_format(($color_wise_wo_result_qnty['fin_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4) ;?></td>
                <td width='50' align='right' > <? //echo number_format(($color_wise_wo_result_qnty['grey_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4);?></td>
                <td width='50' align='right' > <? //echo number_format(($color_wise_wo_result_qnty['grey_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4);?></td>
                <?
                }
                ?>
                <td align="right">
                <?
                $consumption_per_unit_fab=($grand_total_fin_fab_qnty/$po_qnty_tot)*($costing_per_qnty);
                echo number_format($consumption_per_unit_fab,4);
                ?>
                </td>
                <td align="right">
                <?
                $consumption_per_unit_amuont=($grand_total_amount/$po_qnty_tot)*($costing_per_qnty);
                echo number_format(($consumption_per_unit_amuont/$consumption_per_unit_fab),2);
                ?>
                </td>
                <td align="right">
                <?
                echo number_format($consumption_per_unit_amuont,2);
                ?>
                </td>
                </tr>
			</table>
			<?
		}
		?>
        <br/>
        <?
		if($cbo_fabric_source==1)
		{
			$lab_dip_color_arr=array();
			$lab_dip_color_sql=sql_select("select pre_cost_fabric_cost_dtls_id, gmts_color_id, contrast_color_id from wo_pre_cos_fab_co_color_dtls where job_no='$txt_job_no'");
			foreach($lab_dip_color_sql as $row)
			{
				$lab_dip_color_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('gmts_color_id')]]=$row[csf('contrast_color_id')];
			}
			
			

			$collar_cuff_percent_arr=array(); $collar_cuff_body_arr=array(); $collar_cuff_color_arr=array(); $collar_cuff_size_arr=array(); $collar_cuff_item_size_arr=array(); $color_size_sensitive_arr=array();

			$collar_cuff_sql="select a.id, a.item_number_id, a.color_size_sensitive, a.color_break_down, b.color_number_id, b.gmts_sizes, b.item_size, c.size_number_id, '' as colar_cuff_per, e.body_part_full_name, e.body_part_type
			FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c, wo_booking_dtls_hstry d, lib_body_part e

			WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.body_part_id=e.id and e.body_part_type in (40,50) and c.id=d.color_size_table_id and d.color_size_table_id=b.color_size_table_id  and d.po_break_down_id=c.po_break_down_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and d.approved_no=$revised_no order by  c.size_order";
			//echo $collar_cuff_sql;
			$collar_cuff_sql_res=sql_select($collar_cuff_sql);
			
			$itemIdArr="";

			foreach($collar_cuff_sql_res as $collar_cuff_row)
			{
				$collar_cuff_percent_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('gmts_sizes')]]=$collar_cuff_row[csf('colar_cuff_per')];
				$collar_cuff_body_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]]=$collar_cuff_row[csf('body_part_full_name')];
				$collar_cuff_size_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('size_number_id')]]=$collar_cuff_row[csf('size_number_id')];
				if(!empty($collar_cuff_row[csf('item_size')]))
				{
					$collar_cuff_item_size_arr[$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('size_number_id')]][$collar_cuff_row[csf('item_size')]]=$collar_cuff_row[csf('item_size')];
				}
				
				$color_size_sensitive_arr[$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('id')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('color_size_sensitive')]]=$collar_cuff_row[csf('color_break_down')];
				
				$itemIdArr[$collar_cuff_row[csf('body_part_type')]].=$collar_cuff_row[csf('item_number_id')].',';
			}
			//print_r($collar_cuff_percent_arr[40]) ;
			unset($collar_cuff_sql_res);
			//$count_collar_cuff=count($collar_cuff_size_arr);
			
			$order_plan_qty_arr=array();
			$color_wise_wo_sql_qnty=sql_select( "select item_number_id, color_number_id, size_number_id, sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in ($booking_po_id) and status_active=1 and is_deleted =0  group by item_number_id, color_number_id, size_number_id");//and item_number_id in (".implode(",",$itemIdArr).")
			foreach($color_wise_wo_sql_qnty as $row)
			{
				$order_plan_qty_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['plan']=$row[csf('plan_cut_qnty')];
				$order_plan_qty_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order']=$row[csf('order_quantity')];
			}
			unset($color_wise_wo_sql_qnty);

			
			foreach($collar_cuff_body_arr as $body_type=>$body_name)
			{
				$gmtsItemId=array_filter(array_unique(explode(",",$itemIdArr[$body_type])));
				foreach($body_name as $body_val)
				{
					$count_collar_cuff=count($collar_cuff_size_arr[$body_type][$body_val]);
					$pre_grand_tot_collar=0; $pre_grand_tot_collar_order_qty=0;

					?>
                    <div style="max-height:1330px; overflow:auto; float:left; padding-top:5px; margin-left:5px; margin-bottom:5px; position:relative;font-size:18px;">
					<table width="625" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                        <tr>
                        	<td colspan="<? echo $count_collar_cuff+3; ?>" align="center"><b><? echo $body_val; ?> - Color Size Brakedown in Pcs.</b></td>
                        </tr>
                        <tr>
                            <td width="100">Size</td>
								<?
                                foreach($collar_cuff_size_arr[$body_type][$body_val]  as $size_number_id)
                                {
									?>
									<td align="center" style="border:1px solid black"><strong><? echo $size_library[$size_number_id];?></strong></td>
									<?
                                }
                                ?>
                            <td width="60" rowspan="2" align="center"><strong>Total</strong></td>
                            <td rowspan="2" align="center"><strong>Extra %</strong></td>
                        </tr>
                        <tr>
                            <td style="font-size:12px"><? echo $body_val; ?> Size</td>
                            <?
                            foreach($collar_cuff_item_size_arr[$body_val]  as $size_number_id=>$size_number)
                            {
								if(count($size_number)>0)
								{
									 foreach($size_number  as $item_size=>$val)
									 {
										?>
										<td align="center" style="border:1px solid black"><strong><? echo $item_size;?></strong></td>
										<?
									 }
								}
								else
								{
									?>
									<td align="center" style="border:1px solid black"><strong>&nbsp;</strong></td>
									<?
								}
                            }
                            ?>
                        </tr>
                            <?

                            $pre_size_total_arr=array();
                            foreach($color_size_sensitive_arr[$body_val] as $pre_cost_id=>$pre_cost_data)
                            {
								foreach($pre_cost_data as $color_number_id=>$color_number_data)
								{
									foreach($color_number_data as $color_size_sensitive=>$color_break_down)
									{
										$pre_color_total_collar=0;
										$pre_color_total_collar_order_qnty=0;
										$process_loss_method=$process_loss_method;
										$constrast_color_arr=array();
										if($color_size_sensitive==3)
										{
											$constrast_color=explode('__',$color_break_down);
											for($i=0;$i<count($constrast_color);$i++)
											{
												$constrast_color2=explode('_',$constrast_color[$i]);
												$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
											}
										}
										?>
										<tr>
											<td>
												<?
                                                if( $color_size_sensitive==3)
                                                {
                                                    echo strtoupper ($constrast_color_arr[$color_number_id]) ;
                                                    $lab_dip_color_id=$lab_dip_color_arr[$pre_cost_id][$color_number_id];
                                                }
                                                else
                                                {
                                                    echo $color_library[$color_number_id];
                                                    $lab_dip_color_id=$color_number_id;
                                                }
                                                ?>
											</td>
											<?
											foreach($collar_cuff_size_arr[$body_type][$body_val] as $size_number_id)
											{
												?>
												<td align="center" style="border:1px solid black">
													<? $plan_cut=0; $collerqty=0; $collar_ex_per=0;
													$plan_cut=0;
													foreach($gmtsItemId as $giid)
													{
														if($body_type==50) $plan_cut+=($order_plan_qty_arr[$giid][$color_number_id][$size_number_id]['plan'])*2;
														else $plan_cut+=$order_plan_qty_arr[$giid][$color_number_id][$size_number_id]['plan'];
													}
													
                                                    //$ord_qty=$order_plan_qty_arr[$color_number_id][$size_number_id]['order'];

                                                    $collar_ex_per=$collar_cuff_percent_arr[$body_type][$body_val][$color_number_id][$size_number_id];
                                                    // echo $collar_ex_per.'=';

												    if($body_type==50) { if($collar_ex_per==0 || $collar_ex_per=="") $collar_ex_per=$cuff_excess_percent; else $collar_ex_per=$collar_ex_per; }
                                                    else if($body_type==40) { if($collar_ex_per==0 || $collar_ex_per=="") $collar_ex_per=$colar_excess_percent; else $collar_ex_per=$collar_ex_per; }
                                                    $colar_excess_per=number_format(($plan_cut*$collar_ex_per)/100,6,".",",");
                                                    $collerqty=($plan_cut+$colar_excess_per);

                                                    //$collerqty=number_format(($requirment/$costing_per_qnty)*$plan_cut,2,'.','');

                                                    echo number_format($collerqty);
                                                    $pre_size_total_arr[$size_number_id]+=$collerqty;
                                                    $pre_color_total_collar+=$collerqty;
                                                    $pre_color_total_collar_order_qnty+=$plan_cut;

                                                    //$pre_grand_tot_collar_order_qty+=$plan_cut;
                                                    ?>
												</td>
												<?
											}
											?>

											<td align="center"><? echo number_format($pre_color_total_collar); ?></td>
											<td align="center"><? echo number_format((($pre_color_total_collar-$pre_color_total_collar_order_qnty)/$pre_color_total_collar_order_qnty)*100,2); ?></td>
										</tr>
										<?
										$pre_grand_collar_ex_per+=$collar_ex_per;
										$pre_grand_tot_collar+=$pre_color_total_collar;
										$pre_grand_tot_collar_order_qty+=$pre_color_total_collar_order_qnty;
									}
								}
							}
							?>
                        
                        <tr>
                            <td>Size Total</td>
								<?
                               // foreach($pre_size_total_arr  as $size_qty)
                               // {
                                	foreach($collar_cuff_size_arr[$body_type][$body_val] as $size_number_id)
									{
										$size_qty=$pre_size_total_arr[$size_number_id];
										?>
										<td style="border:1px solid black;  text-align:center"><? echo number_format($size_qty); ?></td>
										<?
									}

                               // }
                                ?>
                            <td style="border:1px solid black; text-align:center"><? echo number_format($pre_grand_tot_collar); ?></td>
                            <td align="center" style="border:1px solid black"><? echo number_format((($pre_grand_tot_collar-$pre_grand_tot_collar_order_qty)/$pre_grand_tot_collar_order_qty)*100,2); ?></td>
                        </tr>
					</table>
                </div>
                <br/>
                <?
            }
        }

        ?>

       		 <table width="98%">
       		 	<tr>
       		 		<td width="45%" style="float: left;">
       		 			<?php 

       		 				$sql_purchase="SELECT a.booking_no, a.uom, sum(b.fin_fab_qnty) as qnty , b.construction, b.copmposition, b.gsm_weight as dia, b.dia_width as gsm from wo_booking_mst a, wo_booking_dtls b where     a.booking_no = b.booking_no and a.fabric_source = 2 and b.job_no = '$txt_job_no'and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 and  a.booking_type=1 group by a.booking_no, a.uom, b.construction, b.copmposition, b.dia, b.gsm_weight, b.dia_width"; 
       		 				//echo $sql_purchase;
							$purchase_res=sql_select($sql_purchase);
							?>
							<table  width="98%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">

 			                   <thead>
				                   <tr align="center">
				                    	<th colspan="5"><b>Purchased Booking Info</b></th>
				                    </tr>
				                    <tr align="center">
					                    <th>Sl</th>
					                    <th>Booking No</th>
					                    <th>Fabric Data</th>
					                    <th>UOM</th>
					                    <th>Qnty</th>
					                   
				                    </tr>
 			                   </thead>
 			                   <tbody>
 			                   		<?

 			                   			foreach ($purchase_res as  $row) 
 			                   			{
 			                   				?>
 			                   				<tr>
 			                   					<td><?=$p++;?></td>
 			                   					<td><p><?=$row[csf('booking_no')];?></p></td>
 			                   					<td><p><?=$row[csf('construction')] .", ".$row[csf('copmposition')].", ".$row[csf('gsm')].", ".$row[csf('dia')];?></p></td>
 			                   					<td><p><?=$unit_of_measurement[$row[csf('uom')]];?></p></td>
 			                   					<td><p><?=number_format($row[csf('qnty')],2);?></p></td>
 			                   				</tr>
 			                   				<?
 			                   			}


 			                   		?>
 			                   </tbody>
 			                   
 			            </table>

       		 			 
       		 		</td>
       		 		<td width="45%" style="float: right;">
 			       		 <table  width="98%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">

 			                   <tr align="center">
 			                    	<td colspan="10"><b>Dyes To Match</b></td>
 			                    </tr>
 			                    <tr align="center">
 				                    <td>Sl</td>
 				                    <td>Item</td>
 				                    <td>Item Description</td>
 				                    <td>Body Color</td>
 				                    <td>Item Color</td>
 				                    <td>Finish Qty.</td>
 				                    <td>UOM</td>
 			                    </tr>
 			                    <?
 								$lib_item_group_arr=return_library_array( "select item_name, id from lib_item_group where item_category=4 and is_deleted=0  and  status_active=1 order by item_name", "id", "item_name");
 								$sql=sql_select("select id from wo_booking_mst_hstry where booking_no=$txt_booking_no and approved_no=$revised_no");
 								$bookingId=0;
 								foreach($sql as $row){
 									$bookingId= $row[csf('id')];
 								}
 								$co=0;
 								$sql_data=sql_select("select a.fabric_color,a.item_color,a.precost_trim_cost_id,b.trim_group,b.cons_uom,sum(qty) as qty, b.description   from wo_dye_to_match a, wo_pre_cost_trim_cost_dtls b where a.precost_trim_cost_id=b.id and a.booking_id=$bookingId and a.qty>0 and a.status_active=1 and b.status_active=1  group by a.fabric_color,a.item_color,a.precost_trim_cost_id,b.trim_group,b.cons_uom, b.description order by a.fabric_color");

 								foreach($sql_data  as $row)
 			                    {
 									$co++;
 									?>
 				                    <tr>
 				                    <td><? echo $co; ?></td>
 				                    <td> <? echo $lib_item_group_arr[$row[csf('trim_group')]];?></td>
 				                    <td><p> <? echo $row[csf('description')];?></p></td>
 				                    <td><? echo $color_library[$row[csf('fabric_color')]];?></td>
 				                    <td><? echo $color_library[$row[csf('item_color')]];?></td>
 				                    <td align="right"><? echo $row[csf('qty')];?></td>
 				                    <td><? echo $unit_of_measurement[$row[csf('cons_uom')]];?></td>
 				                    </tr>
 				                    <?
 								}
 								?>
 			            </table>
       		 		</td>
       		 	</tr>
       		 </table>
            <br>

        <?

		 $condition= new condition();
		if(str_replace("'","",$txt_order_no_id) !=''){
			$condition->po_id("in(".str_replace("'","",$txt_order_no_id).")");
		}

		$condition->init();
		$cos_per_arr=$condition->getCostingPerArr();
		$yarn= new yarn($condition);
		$yarn_data_array=$yarn->getCountCompositionPercentTypeColorAndRateWiseYarnQtyAndAmountArray();
		$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');


		$yarn_sql_array=sql_select("SELECT min(a.id) as id ,a.count_id, a.copm_one_id, a.percent_one, a.color, a.type_id, sum(a.cons_qnty) as yarn_required, a.rate  from wo_pre_cost_fab_yarn_cost_dtls a, wo_booking_dtls_hstry b where a.job_no=b.job_no and a.fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.job_no='$txt_job_no' and b.booking_no=$txt_booking_no  and  a.status_active=1 and a.is_deleted=0 and b.approved_no=$revised_no group by a.count_id,a.copm_one_id,a.percent_one,a.color,a.type_id,a.rate order by id");
		?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" style="font-family:Arial Narrow;font-size:18px;" >
            <tr>
                <td width="49%" valign="top">
                    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td colspan="7"><b>Yarn Required Summary (Pre Cost)</b></td>

                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Yarn Description</td>
                    <td>Brand</td>
                    <td>Lot</td>
                    <?
					if($show_yarn_rate==1)
					{
					?>
                    <td>Rate</td>
                    <?
					}
					?>
                    <td>Cons for <? echo $costing_per; ?> Gmts</td>
                    <td>Total (KG)</td>
                    </tr>
                    <?
					$i=0;
					$total_yarn=0;
					foreach($yarn_sql_array  as $row)
                    {

						$i++;
						$rowcons_qnty = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];
						$rowcons_Amt = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['amount'];


						$rate=$rowcons_Amt/$rowcons_qnty;
						$rowcons_qnty =($rowcons_qnty/100)*$booking_percent;
					?>
                    <tr align="center">
                    <td><? echo $i; ?></td>
                    <td>
					<?
					$yarn_des=$yarn_count_arr[$row[csf('count_id')]]." ".$composition[$row[csf('copm_one_id')]]." ".$row[csf('percent_one')]."%  ";
					$yarn_des.=$color_library[$row[csf('color')]]." ";
					$yarn_des.=$yarn_type[$row[csf('type_id')]];
					echo $yarn_des;
					?>
                    </td>
                    <td></td>
                    <td></td>
                    <?
					if($show_yarn_rate==1)
					{
					?>
                     <td><? echo number_format($row[csf('rate')],4); ?></td>
                     <?
					}
					 ?>
                    <td><?  echo number_format(($rowcons_qnty/$po_qnty_tot)*$cos_per_arr[$job_no],4);//echo number_format($row[csf('yarn_required')],4); ?></td>

                    <td align="right">
					<? echo number_format($rowcons_qnty,2); $total_yarn+=$rowcons_qnty; ?></td>
                    </tr>
                    <?
					}
					?>
                    <tr align="center">
                    <td>Total</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <?
					if($show_yarn_rate==1)
					{
					?>
                    <td></td>
                    <?
                    }
					?>
                    <td></td>
                    <td align="right"><? echo number_format($total_yarn,2); ?></td>
                    </tr>
                    </table>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top" align="center">
                <?
				$yarn_sql_array=sql_select("SELECT min(a.id) as id, a.item_id, sum(a.qnty) as qnty ,min(b.supplier_id) as supplier_id,min(b.lot) as lot from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and a.booking_no=$txt_booking_no and  a.status_active=1 and a.is_deleted=0 group by a.item_id order by a.id");
				if(count($yarn_sql_array)>0)
				{
				?>
                   <table  width="100%"  style="font-size:18px;" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td colspan="7"><b>Allocated Yarn</b></td>

                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Yarn Description</td>
                    <td>Brand</td>
                    <td>Lot</td>


                    <td>Allocated Qty (Kg)</td>
                    </tr>
                    <?
					$total_allo=0;
					$item=return_library_array( "select id, product_name_details from   product_details_master",'id','product_name_details');
					$supplier=return_library_array( "select id, short_name from   lib_supplier",'id','short_name');
					$i=0;
					$total_yarn=0;
					foreach($yarn_sql_array  as $row)
                    {

						$i++;
					?>
                    <tr align="center">
                    <td><? echo $i; ?></td>
                    <td>
					<?

					echo $item[$row[csf('item_id')]];
					?>
                    </td>
                    <td>
                    <?

					echo $supplier[$row[csf('supplier_id')]];
					?>
                    </td>
                    <td>
					<?

					echo $row[csf('lot')];
					?>
                    </td>
                    <td align="right"><? echo number_format($row[csf('qnty')],4); $total_allo+= $row[csf('qnty')];?></td>
                    </tr>
                    <?
					}
					?>
                    <tr align="center">
                    <td>Total</td>
                    <td></td>


                    <td></td>
                    <td></td>
                    <td align="right"><? echo number_format($total_allo,4); ?></td>
                    </tr>
                    </table>
                    <?
				}
				else
				{
					$is_yarn_allocated=return_field_value("allocation", "variable_settings_inventory", "company_name=$cbo_company_name and variable_list=18 and item_category_id=1");
					if($is_yarn_allocated==1)
					{
					?>
					<font style=" font-size:30px"><b> </b></font>
                    <?
					}
					else
					{
						echo "";
					}
				}
					?>
                </td>
                 <td width="49%" valign="top" align="center">
                	
               	
                </td>
            </tr>
        </table>
        <br/>
        <?
		$sql_embelishment=sql_select("select emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls where job_no='$txt_job_no' and status_active=1 and 	is_deleted=0");
		?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" style="font-family:Arial Narrow;font-size:18px;">
            <tr>
                <td width="49%" valign="top">
                <?
				if(count($sql_embelishment)>0)
				{
				?>
                    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" style="font-family:Arial Narrow;font-size:18px;">
                    <tr align="center">
                    <td colspan="7"><b>Embelishment (Pre Cost)</b></td>

                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Embelishment Name</td>
                    <td>Embelishment Type</td>
                    <td>Cons <? echo $costing_per; ?> Gmts</td>
                    <td>Rate</td>
                    <td>Amount</td>

                    </tr>
                    <?
					$sql_embelishment=sql_select("select emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls where job_no='$txt_job_no' and status_active=1 and 	is_deleted=0");
					$i=0;
					//$total_yarn=0;
					foreach($sql_embelishment  as $row_embelishment)
                    {

						$i++;
					?>
                    <tr align="center">
                    <td><? echo $i; ?></td>
                    <td>
					<?
					echo $emblishment_name_array[$row_embelishment[csf('emb_name')]];
					?>
                    </td>
                    <td>
                    <?
					if($row_embelishment[csf('emb_name')]==1)
					{
					echo $emblishment_print_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==2)
					{
					echo $emblishment_embroy_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==3)
					{
					echo $emblishment_wash_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==4)
					{
					echo $emblishment_spwork_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==5)
					{
					echo $emblishment_gmts_type[$row_embelishment[csf('emb_type')]];
					}
					?>

                    </td>
                    <td>
                    <?
					echo $row_embelishment[csf('cons_dzn_gmts')];
					?>
                    </td>
                    <td>
					<?
					echo $row_embelishment[csf('rate')];
					?>
                    </td>

                    <td>
					<?
					echo $row_embelishment[csf('amount')];
					?>
                    </td>


                    </tr>
                    <?
					}
					?>

                    </table>
                    <?
				}
					?>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top" align="center">
                				<?
                				$lib_designation_arr=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");
									 $user_lib_designation_arr=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
									 $user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

								$mst_id=return_field_value("id as mst_id","wo_booking_mst","booking_no=$txt_booking_no","mst_id");
                				 $unapprove_data_array=sql_select("select b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date,b.approved_no from   approval_history b where b.mst_id=$mst_id and b.entry_form=7  order by b.approved_date,b.approved_by");

                				
                				
	                				$sql_unapproved=sql_select("select booking_id,approval_cause from fabric_booking_approval_cause where  entry_form=7 and approval_type=2 and is_deleted=0 and status_active=1 and booking_id=$mst_id");
	                					$unapproved_request_arr=array();
	                					foreach($sql_unapproved as $rowu)
	                					{
	                						$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
	                					}
	                		 		?>
	                		       <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
	                		            <thead>
	                		            <tr style="border:1px solid black;">
	                		                <th colspan="6" style="border:1px solid black;">Approval/Un Approval History</th>
	                		                </tr>
	                		                <tr style="border:1px solid black;">
	                		                <th width="3%" style="border:1px solid black;">Sl</th>
	                						<th width="30%" style="border:1px solid black;">Name</th>
	                						<th width="20%" style="border:1px solid black;">Designation</th>
	                						<th width="5%" style="border:1px solid black;">Approval Status</th>
	                						<th width="20%" style="border:1px solid black;">Reason For Un Approval</th>
	                						<th width="22%" style="border:1px solid black;"> Date</th>

	                		                </tr>
	                		            </thead>
	                		            <tbody>
	                		            <?
	                					$i=1;
	                					foreach($unapprove_data_array as $row)
	                					{

		                					?>
		                		            <tr style="border:1px solid black;">
		                		                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
		                						<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
		                						<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
		                						<td width="5%" style="border:1px solid black; text-align:center"><? echo 'Yes';?></td>
		                						<td width="20%" style="border:1px solid black;"><? echo '';?></td>
		                						<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); else echo "";?></td>
		                		            </tr>
		                		                <?
		                						$i++;
		                						$un_approved_date= explode(" ",$row[csf('un_approved_date')]);
		                						$un_approved_date=$un_approved_date[0];
		                						if($db_type==0) //Mysql
		                						{
		                							if($un_approved_date=="" || $un_approved_date=="0000-00-00") $un_approved_date="";else $un_approved_date=$un_approved_date;
		                						}
		                						else
		                						{
		                							if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;
		                						}

		                						if($un_approved_date!="")
		                						{
			                						?>
				                					<tr style="border:1px solid black;">
				                		                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				                						<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				                						<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				                						<td width="5%" style="border:1px solid black;text-align:center;"><? echo 'No';?></td>
				                						<td width="20%" style="border:1px solid black;text-align:center"><? echo $unapproved_request_arr[$mst_id];?></td>
				                						<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('un_approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('un_approved_date')])); else echo "";?></td>
				                		              </tr>

			                		                <?
			                						$i++;
		                						}

		                				}
	                						?>
	                		            </tbody>
	                		        </table>
	                				

                </td>
            </tr>
        </table>
        <br/>
        <table  width="100%" style="margin: 0px;padding: 0px;">
        <?php $stripe_color_wise=array(); ?>
       
        <tr>
        	<td width="70%" style="float: left;">
        		<table  class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%"   style="font-family:Arial Narrow;font-size:18px;margin: 0px;padding: 0px;" >
        		       
        		        <tr>
        		            <td align="center" colspan="9">  Stripe Details</td>
        		            
    		            </tr>
        		        <?
        				$color_name_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
        				$sql_stripe="select c.id,c.composition,c.construction,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,sum(b.grey_fab_qnty) as fab_qty,b.dia_width,d.color_number_id as color_number_id,d.id as did,d.stripe_color,d.fabreqtotkg as fabreqtotkg ,d.measurement as measurement ,d.yarn_dyed,d.uom  from wo_booking_dtls_hstry b, wo_pre_cost_fabric_cost_dtls c,wo_pre_stripe_color d where c.id=b.pre_cost_fabric_cost_dtls_id and c.job_no=b.job_no and d.pre_cost_fabric_cost_dtls_id=c.id and d.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and b.job_no=d.job_no and b.job_no='$txt_job_no'  and d.job_no='$txt_job_no' and b.booking_no=$txt_booking_no  and c.color_type_id in (2,6,33,34) and b.status_active=1  and c.is_deleted=0 and c.status_active=1  and d.is_deleted=0 and d.status_active=1  and 	b.is_deleted=0 and b.approved_no=$revised_no group by c.id,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,d.color_number_id,d.id,d.stripe_color,d.yarn_dyed,d.fabreqtotkg ,d.measurement,d.uom,c.composition,c.construction,b.dia_width order by d.id ";   
        				//echo $sql_stripe;     				
        				$result_data=sql_select($sql_stripe);
        				foreach($result_data as $row)
        				{
        					$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['stripe_color'][$row[csf('did')]]=$row[csf('stripe_color')];
        					$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['measurement'][$row[csf('did')]]=$row[csf('measurement')];
        					$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['uom'][$row[csf('did')]]=$row[csf('uom')];
        					$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fabreqtotkg'][$row[csf('did')]]=$row[csf('fabreqtotkg')];
        					$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['yarn_dyed'][$row[csf('did')]]=$row[csf('yarn_dyed')];

        					$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['composition']=$row[csf('composition')];
        					$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['construction']=$row[csf('construction')];
        					$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['gsm_weight']=$row[csf('gsm_weight')];
        					$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['color_type_id']=$row[csf('color_type_id')];
        					$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['dia_width']=$row[csf('dia_width')];
        				}
        				?>
        		            <tr>
	        		            <td align="center" width="30"> SL</td>
	        		            <td align="center" width="100"> Body Part</td>
	        		            <td align="center" width="80"> Fabric Color</td>
	        		            <td align="center" width="70"> Fabric Qty(KG)</td>
	        		            <td align="center" width="70"> Stripe Color</td>
	        		            <td align="center" width="70"> Stripe Measurement</td>
	        		            <td align="center" width="70"> Stripe Uom</td>
	        		            <td  align="center" width="70"> Qty.(KG)</td>
	        		            <td  align="center" width="70"> Y/D Req.</td>
        		            </tr>
        		            <?
        					//if($db_type==0) $color_cond="d.fabric_color_id='".$color_id."'";
        					//else if($db_type==2) $color_cond="nvl(d.fabric_color_id,0)=nvl('".$color_id."',0)";
        						//else if($db_type==2) $color_cond="nvl(d.fabric_color_id,0)=nvl('".$color_id."',0)";


        					$i=1;$total_fab_qty=0;$total_fabreqtotkg=0;$fab_data_array=array();
        		            foreach($stripe_arr as $body_id=>$body_data)
        		            {
        						foreach($body_data as $color_id=>$color_val)
        						{
        							$rowspan=count($color_val['stripe_color']);
        							$composition=$stripe_arr2[$body_id][$color_id]['composition'];
        							$construction=$stripe_arr2[$body_id][$color_id]['construction'];
        							$gsm_weight=$stripe_arr2[$body_id][$color_id]['gsm_weight'];
        							$color_type_id=$stripe_arr2[$body_id][$color_id]['color_type_id'];
        							$dia_width=$stripe_arr2[$body_id][$color_id]['dia_width'];

        							if($db_type==0) $color_cond="d.fabric_color_id='".$color_id."'";
        							else if($db_type==2) $color_cond="nvl(d.fabric_color_id,0)=nvl('".$color_id."',0)";

        							$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls_hstry d
        								WHERE a.job_no=b.job_no and
        								a.id=b.pre_cost_fabric_cost_dtls_id and
        								c.job_no_mst=a.job_no and
        								c.id=b.color_size_table_id and
        								b.po_break_down_id=d.po_break_down_id and
        								b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
        								d.booking_no =$txt_booking_no and
        								a.body_part_id='".$body_id."' and
        								a.color_type_id='".$color_type_id."' and
        								a.construction='".$construction."' and
        								a.composition='".$composition."' and
        								a.gsm_weight='".$gsm_weight."' and
        								$color_cond and
        								d.status_active=1 and
        								d.is_deleted=0
        								and d.approved_no=$revised_no
        								");
        						
        								list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty;
        							?>
        							<tr>
	        							<?
	        							$color_qty=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
	        							?>
	        							<td rowspan="<? echo $rowspan;?>"> <? echo $i; ?></td>
	        							<td rowspan="<? echo $rowspan;?>"> <? echo $body_part[$body_id]; ?></td>
	        							<td rowspan="<? echo $rowspan;?>"> <? echo $color_name_arr[$color_id]; ?></td>
	        							<td rowspan="<? echo $rowspan;?>" align="right"> <? echo number_format($color_qty,2); ?>&nbsp; </td>
	        							<?
	        							$total_fab_qty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
	        							$sk=0;
	        							foreach($color_val['stripe_color'] as $strip_color_id=>$s_color_val)
	        							{

	        								$measurement=$color_val['measurement'][$strip_color_id];
	        								$uom=$color_val['uom'][$strip_color_id];
	        								$fabreqtotkg=$color_val['fabreqtotkg'][$strip_color_id];
	        								$yarn_dyed=$color_val['yarn_dyed'][$strip_color_id];
	        								if($sk>0)
	        								{
	        									echo "<tr>";
	        								}
	        								?>
	        							
		        								<td><?  echo  $color_name_arr[$s_color_val]; ?></td>
		        								<td align="right"> <? echo  number_format($measurement,2); ?> &nbsp; </td>
		        		                        <td> <? echo  $unit_of_measurement[$uom]; ?></td>
		        								<td align="right"> <? echo  number_format($fabreqtotkg,2); ?> &nbsp;</td>
		        								<td> <? echo  $yes_no[$yarn_dyed]; ?></td>
	        								
	        								<?
	        								if($sk>0)
	        								{
	        									echo "</tr>";
	        								}
	        								$sk++;
	        								$total_fabreqtotkg+=$fabreqtotkg;
	        								$stripe_color_wise[$color_name_arr[$s_color_val]]+=$fabreqtotkg;
	        							}
	        							$i++;
	        							?>
        							</tr>
        							<?
        						}
        					}
        					?>
	        		            <tfoot>
		        		            <tr>
			        		            <td colspan="3">Total </td>
			        		            <td align="right">  <? echo  number_format($total_fab_qty,2); ?> &nbsp;</td>
			        		            <td></td>
			        		            <td></td>
			        		            <td>   </td>
			        		            <td align="right"><? echo  number_format($total_fabreqtotkg,2); ?> &nbsp;</td>
			        		        </tr>
	        		            </tfoot>
        		            </table>
        	</td>
        	
        	<td width="20%" style="float: right;">
        		        <table  class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%"   style="font-family:Arial Narrow;font-size:18px;margin: 0px;padding: 0px;"  >
        		       

        		        <tr>
        		            <td align="left" colspan="3"> Stripe  Color wise Summary</td>
        		            
        		           
        		           
    		            </tr>
        		        <?
        				
        				?>
        		            <tr>
	        		            <td width="30"> SL</td>
	        		            
	        		            <td width="70"> Stripe Color</td>
	        		           
	        		            <td  width="70"> Qty.(KG)</td>
	        		           
        		            </tr>
        		            <?
        					//if($db_type==0) $color_cond="d.fabric_color_id='".$color_id."'";
        					//else if($db_type==2) $color_cond="nvl(d.fabric_color_id,0)=nvl('".$color_id."',0)";
        						//else if($db_type==2) $color_cond="nvl(d.fabric_color_id,0)=nvl('".$color_id."',0)";


        					$i=1;$total_stripe_qnt=0;        		            
        						foreach($stripe_color_wise as $color=>$val)
        						{
        							
        							
        							?>
        							<tr>
	        							<td> <? echo $i; ?></td>
	        							
	        							<td > <? echo $color; ?></td>
	        							<td align="right"> <?php echo number_format($val,2); ?></td>
	        						</tr>
        							
        							<?
        							$total_stripe_qnt+=$val;
        							
        							$i++;
        						}
        					
        					?>
        		            <tfoot>
        		            <tr>
        		            
        		            <td></td>
        		            <td></td>
        		            
        		            <td align="right"><? echo  number_format($total_stripe_qnt,2); ?> </td>
        		            </tr>
        		            </tfoot>
        		            </table>
        	</td>
        </tr>
         </table >
      
        <br/>
        <table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" style="font-family:Arial Narrow;font-size:18px;">
        <tr align="center">
        <td colspan="10"><b> Comments(Production) </b></td>
        </tr>
        <tr align="center">
        <td>Sl</td>
        <td>Po NO</td>
        <td>Ship Date</td>
        <td>Pre-Cost Qty</td>
        <td>Mn.Book Qty</td>
        <td>Sht.Book Qty</td>
        <td>Smp.Book Qty</td>
        <td>Tot.Book Qty</td>
        <td>Balance</td>
        <td>Comments</td>
        </tr>
        <?
        $cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
        $cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
        if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and a.fab_nature_id='$cbo_fabric_natu'";
        if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and a.fabric_source='$cbo_fabric_source'";
        $paln_cut_qnty_array=return_library_array( "select min(id) as id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown  where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by color_number_id,size_number_id,item_number_id,po_break_down_id", "id", "plan_cut_qnty");
        $item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no ='$txt_job_no'", "gmts_item_id", "set_item_ratio");

        $nameArray=sql_select("
        select
        a.id,
        a.item_number_id,
        a.costing_per,
        b.po_break_down_id,
        b.color_size_table_id,
        b.requirment,
        c.po_number
        FROM
        wo_pre_cost_fabric_cost_dtls a,
        wo_pre_cos_fab_co_avg_con_dtls b,
        wo_po_break_down c
        WHERE
        a.job_no=b.job_no and
        a.job_no=c.job_no_mst and
        a.id=b.pre_cost_fabric_cost_dtls_id and
        b.po_break_down_id=c.id and
        b.po_break_down_id in (".str_replace("'","",$txt_order_no_id).")  $cbo_fabric_natu $cbo_fabric_source_cond and a.status_active=1 and a.is_deleted=0
        order by id");

        $count=0;
        $tot_grey_req_as_pre_cost_arr=array();
        foreach ($nameArray as $result)
        {
        if (count($nameArray)>0 )
        {
        if($result[csf("costing_per")]==1)
        {
        $tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(12*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
        }
        if($result[csf("costing_per")]==2)
        {
        $tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(1*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
        }
        if($result[csf("costing_per")]==3)
        {
        $tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(24*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
        }
        if($result[csf("costing_per")]==4)
        {
        $tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(36*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
        }
        if($result[csf("costing_per")]==5)
        {
        $tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(48*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
        }
        $tot_grey_req_as_pre_cost_arr[$result[csf("po_number")]]+=$tot_grey_req_as_pre_cost;
        }
        }

        $total_pre_cost=0;
        $total_booking_qnty_main=0;
        $total_booking_qnty_short=0;
        $total_booking_qnty_sample=0;
        $total_tot_bok_qty=0;
        $tot_balance=0;
        
        $booking_qnty_main=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b, wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no  and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and a.is_short=2 and c.item_category=2 and a.status_active=1 and a.is_deleted=0  group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");

       

       
        $booking_qnty_short=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and c.item_category=2 and a.is_short=1 and a.status_active=1 and a.is_deleted=0 and a.is_deleted=0  group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
      

        $booking_qnty_sample=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c  where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =4 and c.fabric_source=$cbo_fabric_source and c.item_category=2  and a.status_active=1 and a.is_deleted=0  group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
       
        $sql_data=sql_select( "select max(a.id) as id,  a.po_number,max(a.pub_shipment_date) as pub_shipment_date,sum(a.plan_cut) as plan_cut  from wo_po_break_down a,wo_pre_cost_sum_dtls b,wo_pre_cost_mst c where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no and a.id in(".str_replace("'","",$txt_order_no_id).") group by a.po_number order by pub_shipment_date asc");
        foreach($sql_data  as $row)
        {
        $col++;
        ?>
        <tr align="center">
        <td><? echo $col; ?></td>
        <td><? echo $row[csf("po_number")]; ?></td>
        <td><? echo change_date_format($row[csf("pub_shipment_date")],"dd-mm-yyyy",'-'); ?></td>
        <td align="right"><? echo number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]],2); $total_pre_cost+=$tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]; ?></td>
        <td align="right"><? echo number_format($booking_qnty_main[$row[csf("id")]],2); $total_booking_qnty_main+=$booking_qnty_main[$row[csf("id")]];?></td>
        <td align="right"><? echo number_format($booking_qnty_short[$row[csf("id")]],2); $total_booking_qnty_short+=$booking_qnty_short[$row[csf("id")]];?></td>
        <td align="right"><? echo number_format($booking_qnty_sample[$row[csf("id")]],2); $total_booking_qnty_sample+=$booking_qnty_sample[$row[csf("id")]];?></td>
        <td align="right"><? $tot_bok_qty=$booking_qnty_main[$row[csf("id")]]+$booking_qnty_short[$row[csf("id")]]+$booking_qnty_sample[$row[csf("id")]]; echo number_format($tot_bok_qty,2); $total_tot_bok_qty+=$tot_bok_qty;?></td>
        <td align="right">
        <? $balance= def_number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]-$tot_bok_qty,2,""); echo number_format($balance,2); $tot_balance+= $balance?>
        </td>
        <td>
        <?
        if( $balance>0)
        {
        echo "Less Booking";
        }
        else if ($balance<0)
        {
        echo "Extra Booking";
        }
        else
        {
        echo "";
        }
        ?>
        </td>
        </tr>
        <?
        }
        ?>
        <tfoot>
        <tr>
        <td colspan="3">Total:</td>
        <td align="right"><? echo number_format($total_pre_cost,2); ?></td>
        <td align="right"><? echo number_format($total_booking_qnty_main,2); ?></td>
        <td align="right"><? echo number_format($total_booking_qnty_short,2); ?></td>
        <td align="right"><? echo number_format($total_booking_qnty_sample,2); ?></td>
        <td align="right"><? echo number_format($total_tot_bok_qty,2); ?></td>
        <td align="right"><? echo number_format($tot_balance,2); ?></td>
        <td></td>
        </tr>
        </tfoot>
        </table>

        <?
         if(count($purchase_res))
        {
        	?>
         <br/>
        <table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" style="font-family:Arial Narrow;font-size:18px;">
        <tr align="center">
        <td colspan="10"><b> Comments(Purchase)</b></td>
        </tr>
        <tr align="center">
        <td>Sl</td>
        <td>Po NO</td>
        <td>Ship Date</td>
        <td>Pre-Cost Qty</td>
        <td>Purchase<br>Booking Qty</td>
        <td>Sht.Book Qty</td>
        <td>Smp.Book Qty</td>
        <td>Tot.Book Qty</td>
        <td>Balance</td>
        <td>Comments</td>
        </tr>
        <?

       



        $cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
        $cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
        if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and a.fab_nature_id='$cbo_fabric_natu'";
        if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and a.fabric_source='$cbo_fabric_source'";
        $paln_cut_qnty_array=return_library_array( "select min(id) as id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown  where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by color_number_id,size_number_id,item_number_id,po_break_down_id", "id", "plan_cut_qnty");
        $item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no ='$txt_job_no'", "gmts_item_id", "set_item_ratio");

        $nameArray=sql_select("
        select
        a.id,
        a.item_number_id,
        a.costing_per,
        b.po_break_down_id,
        b.color_size_table_id,
        b.requirment,
        c.po_number
        FROM
        wo_pre_cost_fabric_cost_dtls a,
        wo_pre_cos_fab_co_avg_con_dtls b,
        wo_po_break_down c
        WHERE
        a.job_no=b.job_no and
        a.job_no=c.job_no_mst and
        a.id=b.pre_cost_fabric_cost_dtls_id and
        b.po_break_down_id=c.id 
       
       and c.job_no_mst = '$txt_job_no'  and a.fab_nature_id=3 and   a.fabric_source = '2'  and a.status_active=1 and a.is_deleted=0
        order by id");
       

        $count=0;
        $tot_grey_req_as_pre_cost_arr=array();
        foreach ($nameArray as $result)
        {
        if (count($nameArray)>0 )
        {
        if($result[csf("costing_per")]==1)
        {
        $tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(12*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
        }
        if($result[csf("costing_per")]==2)
        {
        $tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(1*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
        }
        if($result[csf("costing_per")]==3)
        {
        $tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(24*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
        }
        if($result[csf("costing_per")]==4)
        {
        $tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(36*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
        }
        if($result[csf("costing_per")]==5)
        {
        $tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(48*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
        }
        $tot_grey_req_as_pre_cost_arr[$result[csf("po_number")]]+=$tot_grey_req_as_pre_cost;
        }
        }

        $total_pre_cost=0;
        $total_booking_qnty_main=0;
        $total_booking_qnty_short=0;
        $total_booking_qnty_sample=0;
        $total_tot_bok_qty=0;
        $tot_balance=0;
        $booking_qnty_main=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b, wo_booking_mst c where a.job_no =b.job_no_mst and   a.booking_no =c.booking_no  and a.po_break_down_id =b.id and a.job_no = '$txt_job_no' and a.booking_type =1  and c.fabric_source = 2  and a.is_short=2  and a.status_active=1 and a.is_deleted=0  group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");

       
        $booking_qnty_short=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.job_no = '$txt_job_no' and a.booking_type =1 and c.fabric_source=2 and c.item_category=2 and a.is_short=1 and a.status_active=1 and a.is_deleted=0  group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
       
        $booking_qnty_sample=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c  where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.job_no = '$txt_job_no' and a.booking_type =4 and c.fabric_source=2 and c.item_category=2  and a.status_active=1 and a.is_deleted=0  group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
     
        $sql_data=sql_select( "select max(a.id) as id,  a.po_number,max(a.pub_shipment_date) as pub_shipment_date,sum(a.plan_cut) as plan_cut  from wo_po_break_down a,wo_pre_cost_sum_dtls b,wo_pre_cost_mst c where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no and a.job_no_mst='$txt_job_no' group by a.po_number order by pub_shipment_date asc");
        foreach($sql_data  as $row)
        {
        $col++;
        ?>
        <tr align="center">
        <td><? echo $col; ?></td>
        <td><? echo $row[csf("po_number")]; ?></td>
        <td><? echo change_date_format($row[csf("pub_shipment_date")],"dd-mm-yyyy",'-'); ?></td>
        <td align="right"><? echo number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]],2); $total_pre_cost+=$tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]; ?></td>
        <td align="right"><? echo number_format($booking_qnty_main[$row[csf("id")]],2); $total_booking_qnty_main+=$booking_qnty_main[$row[csf("id")]];?></td>
        <td align="right"><? echo number_format($booking_qnty_short[$row[csf("id")]],2); $total_booking_qnty_short+=$booking_qnty_short[$row[csf("id")]];?></td>
        <td align="right"><? echo number_format($booking_qnty_sample[$row[csf("id")]],2); $total_booking_qnty_sample+=$booking_qnty_sample[$row[csf("id")]];?></td>
        <td align="right"><? $tot_bok_qty=$booking_qnty_main[$row[csf("id")]]+$booking_qnty_short[$row[csf("id")]]+$booking_qnty_sample[$row[csf("id")]]; echo number_format($tot_bok_qty,2); $total_tot_bok_qty+=$tot_bok_qty;?></td>
        <td align="right">
        <? $balance= def_number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]-$tot_bok_qty,2,""); echo number_format($balance,2); $tot_balance+= $balance?>
        </td>
        <td>
        <?
        if( $balance>0)
        {
        echo "Less Booking";
        }
        else if ($balance<0)
        {
        echo "Extra Booking";
        }
        else
        {
        echo "";
        }
        ?>
        </td>
        </tr>
        <?
        }
        ?>
        <tfoot>
        <tr>
        <td colspan="3">Total:</td>
        <td align="right"><? echo number_format($total_pre_cost,2); ?></td>
        <td align="right"><? echo number_format($total_booking_qnty_main,2); ?></td>
        <td align="right"><? echo number_format($total_booking_qnty_short,2); ?></td>
        <td align="right"><? echo number_format($total_booking_qnty_sample,2); ?></td>
        <td align="right"><? echo number_format($total_tot_bok_qty,2); ?></td>
        <td align="right"><? echo number_format($tot_balance,2); ?></td>
        <td></td>
        </tr>
       
        </tfoot>
        </table>
         <?
        	}
        ?>
       <br>

		<fieldset id="div_size_color_matrix" style="max-width:1000;">
		<?
		//------------------------------ Query for TNA start-----------------------------------
				$po_id_all=str_replace("'","",$txt_order_no_id);
				$po_num_arr=return_library_array("select id,po_number from wo_po_break_down where id in($po_id_all)",'id','po_number');
				$tna_start_sql=sql_select( "select id,po_number_id,
								(case when task_number=31 then task_start_date else null end) as fab_booking_start_date,
								(case when task_number=31 then task_finish_date else null end) as fab_booking_end_date,
								(case when task_number=60 then task_start_date else null end) as knitting_start_date,
								(case when task_number=60 then task_finish_date else null end) as knitting_end_date,
								(case when task_number=61 then task_start_date else null end) as dying_start_date,
								(case when task_number=61 then task_finish_date else null end) as dying_end_date,
								(case when task_number=64 then task_start_date else null end) as finishing_start_date,
								(case when task_number=64 then task_finish_date else null end) as finishing_end_date,
								(case when task_number=84 then task_start_date else null end) as cutting_start_date,
								(case when task_number=84 then task_finish_date else null end) as cutting_end_date,
								(case when task_number=86 then task_start_date else null end) as sewing_start_date,
								(case when task_number=86 then task_finish_date else null end) as sewing_end_date,
								(case when task_number=110 then task_start_date else null end) as exfact_start_date,
								(case when task_number=110 then task_finish_date else null end) as exfact_end_date,
								(case when task_number=47 then task_start_date else null end) as yarn_rec_start_date,
								(case when task_number=47 then task_finish_date else null end) as yarn_rec_end_date
								from tna_process_mst
								where status_active=1 and po_number_id in($po_id_all)");
				$tna_fab_start=$tna_knit_start=$tna_dyeing_start=$tna_fin_start=$tna_cut_start=$tna_sewin_start=$tna_exfact_start="";
				$tna_date_task_arr=array();
				foreach($tna_start_sql as $row)
				{
					if($row[csf("fab_booking_start_date")]!="" && $row[csf("fab_booking_start_date")]!="0000-00-00")
					{
						if($tna_fab_start=="")
						{
							$tna_fab_start=$row[csf("fab_booking_start_date")];
						}
					}


					if($row[csf("knitting_start_date")]!="" && $row[csf("knitting_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['knitting_start_date']=$row[csf("knitting_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['knitting_end_date']=$row[csf("knitting_end_date")];
					}
					if($row[csf("dying_start_date")]!="" && $row[csf("dying_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['dying_start_date']=$row[csf("dying_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['dying_end_date']=$row[csf("dying_end_date")];
					}
					if($row[csf("finishing_start_date")]!="" && $row[csf("finishing_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['finishing_start_date']=$row[csf("finishing_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['finishing_end_date']=$row[csf("finishing_end_date")];
					}
					if($row[csf("cutting_start_date")]!="" && $row[csf("cutting_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_start_date']=$row[csf("cutting_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_end_date']=$row[csf("cutting_end_date")];
					}

					if($row[csf("sewing_start_date")]!="" && $row[csf("sewing_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_start_date']=$row[csf("sewing_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_end_date']=$row[csf("sewing_end_date")];
					}
					if($row[csf("exfact_start_date")]!="" && $row[csf("exfact_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_start_date']=$row[csf("exfact_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_end_date']=$row[csf("exfact_end_date")];
					}
					if($row[csf("yarn_rec_start_date")]!="" && $row[csf("yarn_rec_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_start_date']=$row[csf("yarn_rec_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_end_date']=$row[csf("yarn_rec_end_date")];
					}
				}

			//------------------------------ Query for TNA end-----------------------------------
		?>
        <legend>TNA Information</legend>
        <!--<span style="font-size:180; font-weight:bold;"></span>-->
        <table width="100%" style="border:1px solid black;font-size:17px; font-family:Arial Narrow;" border="1" cellpadding="2" cellspacing="0" rules="all">
            <tr>
            	<td rowspan="2" align="center" valign="top">SL</td>
            	<td width="180" rowspan="2"  align="center" valign="top"><b>Order No</b></td>
                <td colspan="2" align="center" valign="top"><b>Yarn Receive</b></td>
                <td colspan="2" align="center" valign="top"><b>Knitting</b></td>
                <td colspan="2" align="center" valign="top"><b>Dyeing</b></td>
                <td colspan="2" align="center" valign="top"><b>Finish Fabric Prod.</b></td>
                <td colspan="2" align="center" valign="top"><b>Cutting </b></td>
                <td colspan="2" align="center" valign="top"><b>Sewing </b></td>
                <td colspan="2"  align="center" valign="top"><b>Ex-factory </b></td>
            </tr>
            <tr>
            	<td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>

            </tr>
            <?
			$i=1;
			foreach($tna_date_task_arr as $order_id=>$row)
			{
				 //$tna_date_task_arr//knitting_start_date dying_start_date finishing_start_date cutting_start_date sewing_start_date exfact_start_date
				?>
                <tr>
                	<td><? echo $i; ?></td>
                    <td><? echo $po_num_arr[$order_id]; ?></td>
                    <td align="center"><? echo change_date_format($row['yarn_rec_start_date']); ?></td>
                    <td  align="center"><? echo change_date_format($row['yarn_rec_end_date']); ?></td>
                	<td align="center"><? echo change_date_format($row['knitting_start_date']); ?></td>
                    <td  align="center"><? echo change_date_format($row['knitting_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['dying_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['dying_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['finishing_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['finishing_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['cutting_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['cutting_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['sewing_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['sewing_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['exfact_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['exfact_end_date']); ?></td>
                </tr>
                <?
				$i++;
			}
			?>

        </table>
        </fieldset>
        <?
		}// fabric Source End
		if($isYarnPurchseValidate==2)
		{
			?>
            <br>
			<table align="left" width="350px" style="border:1px solid black;font-size:12px; font-family:Arial Narrow;" border="1" cellspacing="0" rules="all">
				<tr>
					<th colspan="3">Yarn Purchase Requisition Info</th>
				</tr>
				<tr>
					<th width="120">Job No</th>
					<th width="130">Purchase Req. No</th>
					<th>Qty.</th>
				</tr>
                
                <?
				$sqlYarnReq="select a.requ_no, b.job_no, sum(b.quantity) as qty from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and b.job_no='$txt_job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.requ_no, b.job_no";
				$sqlYarnReqRes=sql_select($sqlYarnReq);
				foreach($sqlYarnReqRes as $row)
				{
				?>
                <tr>
					<td width="120"><?=$row[csf("job_no")]; ?></td>
					<td width="130"><?=$row[csf("requ_no")]; ?></td>
					<td align="right"><?=number_format($row[csf("qty")],2); ?></td>
				</tr>
                
                <?
				}
				?>
        	</table>
            <br><br><br>
		<? } echo get_spacial_instruction($txt_booking_no,"97%",118); ?>
        
        
        <br>
         <div style="font-family:Arial Narrow;">
         <?
		 	echo signature_table(1, $cbo_company_name, "1400px");
		 ?>
         </div>
        <br>

        <?
        	$grand_order_total=0;
        	$grand_plan_total=0;
        	$size_wise_total=array();
			$nameArray_size=sql_select( "select size_number_id from wo_po_color_size_breakdown where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by size_number_id ");
			?>
			
                <div id="div_size_color_matrix" class="pagebreak">
                    <fieldset id="div_size_color_matrix" >
                        <legend>Size and Color Breakdown</legend>
                        <table  class="rpt_table"  border="1" align="left" cellpadding="0"  cellspacing="0" rules="all" >
                            <tr>
                            	<td>PO Number</td>
                            	<td>PO Received Date</td>
                            	<td>Ship Date</td>
                            	<td>Lead Time</td>
                            	<td>Ship.days in Hand</td>
                            	<td>Gmts Item</td>
                                <td style="border:1px solid black"><strong>Color/Size</strong></td>
                                <?
                                foreach($nameArray_size  as $result_size)
                                {
									?>
                                	<td align="center" style="border:1px solid black"><?=$size_library[$result_size[csf('size_number_id')]];?></td>
                                <? } ?>
                                <td  align="center"> Total Order Qty(Pcs)</td>
                                <td  align="center"> Excess %</td>
                                <td  align="center"> Total Plan Cut Qty(Pcs)</td>
                            </tr>
                            <?
                            $color_size_order_qnty_array=array(); $color_size_qnty_array=array(); $size_tatal=array(); $size_tatal_order=array();
                           	$item_size_tatal=array(); $item_size_tatal_order=array(); $item_grand_total=0; $item_grand_total_order=0;
                           	$result_cs=sql_select( "select b.item_number_id,b.color_number_id,sum(b.order_quantity) as order_quantity,b.size_number_id,b.po_break_down_id from wo_po_break_down a, wo_po_color_size_breakdown b where a.id=b.po_break_down_id and a.job_no_mst ='$txt_job_no' and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 group by b.item_number_id,b.color_number_id,b.size_number_id,b.po_break_down_id");
                           	$color_size_data=array();
                           	foreach ($result_cs as $row) {
                           		$color_size_data[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
                           	}

                           	$sql_color="select a.po_number,a.id,a.po_received_date,a.shipment_date,b.item_number_id,b.color_number_id,sum(b.plan_cut_qnty) as plan_cut_qnty,a.shiping_status from wo_po_break_down a, wo_po_color_size_breakdown b where a.id=b.po_break_down_id and a.job_no_mst ='$txt_job_no' and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 group by a.po_number,a.id,a.po_received_date,a.shipment_date,b.item_number_id,b.color_number_id,a.shiping_status order by a.shipment_date asc ";
                           	//echo $sql_color;
							$result_color_size=sql_select( $sql_color);

							foreach ($result_color_size as $row) 
							{
								
								?>

								<tr>
									<td><?php echo $row[csf('po_number')] ?></td>
									<td><?php echo change_date_format($row[csf('po_received_date')]); ?></td>
									<td><?php echo change_date_format($row[csf('shipment_date')]); ?></td>
									<?php 

										$date1=date_create($row[csf('po_received_date')]);
										$date2=date_create($row[csf('shipment_date')]);
										$diff=date_diff($date1,$date2);
										$current_date=date_create(strval(date('Y-m-d')));
										$diff1=date_diff($current_date,$date2);
										
										$day_in_hand=$diff1->format("%R%a days");
										if($row[csf('shiping_status')]==3)
										{
											$day_in_hand='0 days';
										}
									 ?>
									<td><?php echo $diff->format("%a days"); ?></td>
									<td><?php echo str_replace("+", "", $day_in_hand); ?></td>
									<td><?php echo $garments_item[$row[csf('item_number_id')]]; ?></td>
									<td><?php echo $color_library[$row[csf('color_number_id')]]; ?></td>
									<?
									$total=0;

                                    foreach($nameArray_size  as $key)
                                    {
										
										?>
                                    	<td align="center" style="border:1px solid black">
                                    		<?
                                    				$qnty=0;
                                    				$qnty=$color_size_data[$row[csf('id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$key[csf('size_number_id')]];

                                    				echo number_format($qnty);
                                    				$size_wise_total[$key[csf('size_number_id')]]+=$qnty;
                                    				$total+=$qnty;
                                    				
                                    			?>
                                    		
                                    	
                                    	</td>
                                   	 	<? 
                                	}

                                	$grand_order_total+=$total;
        							$grand_plan_total+=$row[csf('plan_cut_qnty')];

        							$plan_cut_dif=$row[csf('plan_cut_qnty')]-$total;
        							$ex_cut_perc=($plan_cut_dif/$total)*100;

                                	?>
                                	<td align="center"><?php echo number_format($total); ?></td>
                                	<td align="center"><?php echo number_format($ex_cut_perc,2); ?>%</td>
                                	<td align="center"><?php echo number_format($row[csf('plan_cut_qnty')]); ?></td>

								</tr>
								<?
							}

								
                            ?>
                            <tr>
                            	<td align="right" colspan="7">Total</td>
                            	
                            	<?
                                    foreach($nameArray_size  as $key)
                                    {
										
										?>
                                    	<td align="center" style="border:1px solid black">
                                    		<strong><?
                                    				
                                    				
                                    				echo number_format($size_wise_total[$key[csf('size_number_id')]])
                                    			?>
                                    		
                                    	</strong>

                                    	</td>
                                   	 	<? 
                                	}

                                	?>
                                <td align="center"><strong><?=number_format($grand_order_total)?></strong></td>
                                <td></td>
                                <td align="center"><strong><?=number_format($grand_plan_total)?></strong></td>
                            </tr>
                           
                           
                        </table>
                    </fieldset>
                </div>
          
			<?
		
		?>

         <!--<br><br><br><br>-->
        
       </div>
       <?
	   exit();
}
// B16 END

if($action=="print_booking_10")
{
	extract($_REQUEST);
	$data=explode("**", $data);
	$txt_booking_no=$data[0];
	$cbo_company_name=$data[1];
	$txt_order_no_id=$data[2];
	$cbo_fabric_natu=$data[3];
	$cbo_fabric_source=$data[4];
	$revised_no=$data[5];
	$txt_job_no=$data[6];
	$show_yarn_rate=$data[7];
	$report_title=$data[8];
	$path=$data[9];
	$id_approved_id=$data[10];
	//$cbo_company_name=str_replace("'","",$cbo_company_name);
	//$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	//$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);

	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name");
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	//$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$location_name_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	//$po_qnty_tot1=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");

	$uom=0;
	$job_data_arr=array();
	$nameArray_buyer=sql_select( "select  a.style_ref_no,a.style_description, a.job_no, a.style_owner, a.buyer_name, a.client_id, a.dealing_marchant, a.season, a.season_matrix, a.total_set_qnty, a.product_dept, a.product_code, a.pro_sub_dep, a.gmts_item_id, a.order_repeat_no, a.qlty_label,a.season_buyer_wise, a.factory_marchant from wo_po_details_master a, wo_booking_dtls_hstry b where a.job_no=b.job_no and b.booking_no='$txt_booking_no' and b.status_active =1 and b.is_deleted=0 and b.approved_no=$revised_no");
	
	foreach ($nameArray_buyer as $result_buy)
	{
		$job_data_arr['job_no'][$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
		$job_data_arr['job_no_in'][$result_buy[csf('job_no')]]="'".$result_buy[csf('job_no')]."'";
		$job_data_arr['total_set_qnty'][$result_buy[csf('job_no')]]=$result_buy[csf('total_set_qnty')];
		$job_data_arr['product_dept'][$result_buy[csf('job_no')]]=$product_dept[$result_buy[csf('product_dept')]];
		$job_data_arr['product_code'][$result_buy[csf('job_no')]]=$result_buy[csf('product_code')];
		$job_data_arr['pro_sub_dep'][$result_buy[csf('job_no')]]=$pro_sub_dept_array[$result_buy[csf('pro_sub_dep')]];
		$job_data_arr['gmts_item_id'][$result_buy[csf('job_no')]]=$result_buy[csf('gmts_item_id')];
		$job_data_arr['style_ref_no'][$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
		$job_data_arr['style_description'][$result_buy[csf('job_no')]]=$result_buy[csf('style_description')];
		$job_data_arr['dealing_marchant'][$result_buy[csf('job_no')]]=$marchentrArr[$result_buy[csf('dealing_marchant')]];
		$job_data_arr['factory_marchant'][$result_buy[csf('job_no')]]=$marchentrArr[$result_buy[csf('factory_marchant')]];
		$job_data_arr['season_matrix'][$result_buy[csf('job_no')]]=$season_arr[$result_buy[csf('season_matrix')]];
		$job_data_arr['season_buyer_wise'][$result_buy[csf('job_no')]]=$season_arr[$result_buy[csf('season_buyer_wise')]];
		$job_data_arr['order_repeat_no'][$result_buy[csf('job_no')]]=$result_buy[csf('order_repeat_no')];
		$job_data_arr['qlty_label'][$result_buy[csf('job_no')]]=$quality_label[$result_buy[csf('qlty_label')]];
		$job_data_arr['client'][$result_buy[csf('job_no')]]=$result_buy[csf('client_id')];
	}

	$job_no= implode(",",array_unique($job_data_arr['job_no']));
	$job_no_in= implode(",",array_unique($job_data_arr['job_no_in']));
	$product_depertment=implode(",",array_unique($job_data_arr['product_dept']));
	$product_code=implode(",",array_unique($job_data_arr['product_code']));
	$pro_sub_dep=implode(",",array_unique($job_data_arr['pro_sub_dep']));
	$gmts_item_id=implode(",",array_unique($job_data_arr['gmts_item_id']));
	$style_sting=implode(",",array_unique($job_data_arr['style_ref_no']));
	$style_description=implode(",",array_unique($job_data_arr['style_description']));
	$dealing_marchant=implode(",",array_unique($job_data_arr['dealing_marchant']));
	$factory_marchant=implode(",",array_unique($job_data_arr['factory_marchant']));
	$season_matrix=implode(",",array_unique($job_data_arr['season_matrix']));
	$season_buyer_wise=implode(",",array_unique($job_data_arr['season_buyer_wise']));
	$order_repeat_no= implode(",",array_unique($job_data_arr['order_repeat_no']));
	$qlty_label= implode(",",array_unique($job_data_arr['qlty_label']));
	$client_id= implode(",",array_unique($job_data_arr['client']));
	?>
	<div style="width:1330px" align="center">
	<?php
	$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='$txt_booking_no' and b.entry_form=7 and a.status_active =1 and a.is_deleted=0 ");
	list($nameArray_approved_row) = $nameArray_approved;
	$nameArray_approved_date = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='$txt_booking_no' and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "' and a.status_active =1 and a.is_deleted=0 ");
	list($nameArray_approved_date_row) = $nameArray_approved_date;
	$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='$txt_booking_no' and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "' and a.status_active =1 and a.is_deleted=0 ");
	list($nameArray_approved_comments_row) = $nameArray_approved_comments;
	$path = str_replace("'", "", $path);
	if ($path != "") {
		$path = $path;
	} else {
		$path = "../../";
	}

	?>										<!--    Header Company Information         -->
	<style type="text/css">
		 .table_valign { vertical-align: top;word-break: break-all;word-wrap: break-word; }
	</style>
	<table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black"  >
		<tr>
			<td width="100">
				<img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
			</td>
			<td width="1250">
				<table width="100%" cellpadding="0" cellspacing="0"  >
					<tr>
						<td align="center" style="font-size:20px;">
							<?php
							echo $company_library[$cbo_company_name];
							?>
						</td>
						<td rowspan="3" width="250">

							<span style="font-size:18px"><b> Job No:&nbsp;&nbsp;<? echo trim($job_no,"'"); ?></b></span><br/>
							<?
							if($nameArray_approved_row[csf('approved_no')]>1)
							{
							?>
							<b> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></b>
							<br/>
							Approved Date: <? echo $nameArray_approved_date_row[csf('approved_date')]; ?>
							<?
							}
							?>


						</td>
					</tr>
					<tr>
						<td align="center" style="font-size:14px">
							<?
							$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
							if($txt_job_no!="")
							{
							$location=return_field_value( "location_name", "wo_po_details_master","job_no='$txt_job_no'");
							}
							else
							{
							$location="";
							}

							foreach ($nameArray as $result)
							{
							echo  $location_name_arr[$location];
							?>

							Email Address: <? echo $result[csf('email')];?>
							Website No: <? echo $result[csf('website')]; ?>

							<?

							}

							?>
						</td>
					</tr>
					<tr>
						<td align="center" style="font-size:20px">
							<strong><? if($report_title !=""){ echo $report_title;} else { echo "General Work Order";}?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<?
	$po_data=array();
	if($db_type==0){
		$sql_job= "SELECT b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,DATEDIFF(pub_shipment_date,po_received_date) date_diff,MIN(po_received_date) as po_received_date ,MIN(pub_shipment_date) as pub_shipment_date,MIN(b.insert_date) as insert_date,b.shiping_status  from wo_booking_dtls_hstry a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no='$txt_booking_no' and a.status_active =1 and a.is_deleted=0 and a.approved_no=$revised_no   group by b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,pub_shipment_date,po_received_date,b.insert_date,b.shiping_status ";
	}
	if($db_type==2){
		$sql_job= "SELECT b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no, b.po_quantity,(pub_shipment_date-po_received_date) date_diff,MIN(po_received_date) as po_received_date,MIN(pub_shipment_date) as pub_shipment_date,MIN(b.insert_date) as insert_date,b.shiping_status, c.order_uom, c.factory_marchant   from wo_booking_dtls_hstry a, wo_po_break_down b , wo_po_details_master c where a.po_break_down_id=b.id and b.job_id = c.id and b.job_id = c.id and a.booking_no='$txt_booking_no' and a.status_active =1 and a.is_deleted=0 and a.approved_no=$revised_no  group by b.job_no_mst,b.id, b.po_number,b.grouping, b.file_no,pub_shipment_date,po_received_date, b.po_quantity, b.insert_date,b.shiping_status, c.order_uom, c.factory_marchant";
	}
	//echo $sql_job;
	$nameArray_job=sql_select($sql_job);

	foreach ($nameArray_job as $result_job)
	{
		$po_data['po_id'][$result_job[csf('id')]]=$result_job[csf('id')];
		$po_data['po_number'][$result_job[csf('id')]]=$result_job[csf('po_number')];
		$po_data['leadtime'][$result_job[csf('id')]]=$result_job[csf('date_diff')];
		$po_data['po_quantity'][$result_job[csf('id')]]=$result_job[csf('po_quantity')];
		$po_data['po_received_date'][$result_job[csf('id')]]=change_date_format($result_job[csf('po_received_date')],'dd-mm-yyyy','-');
		$ddd=strtotime($result_job[csf('pub_shipment_date')]);
		$po_data['pub_shipment_date'][$ddd]=$ddd;
		$po_data['pub_shipment_date_po'][$result_job[csf('id')]]=$result_job[csf('pub_shipment_date')];
		$po_data['insert_date'][$result_job[csf('id')]]=$result_job[csf('insert_date')];

		if($result_job[csf('shiping_status')]==1){
		$shiping_status= "FP";
		}
		else if($result_job[csf('shiping_status')]==2){
		$shiping_status= "PS";
		}
		else if($result_job[csf('shiping_status')]==3){
		$shiping_status= "FS";
		}
		$po_data['shiping_status'][$result_job[csf('id')]]=$shiping_status;
		$po_data['file_no'][$result_job[csf('id')]]=$result_job[csf('file_no')];
		$po_data['grouping'][$result_job[csf('id')]]=$result_job[csf('grouping')];
		$uom_wise_qty[$result_job[csf('order_uom')]] += $result_job[csf('po_quantity')];
	}
	$txt_order_no_id=implode(",",array_unique($po_data['po_id']));
	$leadtime=implode(",",array_unique($po_data['leadtime']));
	$po_quantity=array_sum($po_data['po_quantity']);
	$po_received_date=implode(",",array_unique($po_data['po_received_date']));
	$po_number=implode(",",array_unique($po_data['po_number']));
	$shipment_date=date('d-m-Y',min($po_data['pub_shipment_date']));
	$maxshipment_date=date('d-m-Y',max($po_data['pub_shipment_date']));
	$shiping_status=implode(",",array_unique($po_data['shiping_status']));
	$file_no=implode(",",array_unique($po_data['file_no']));
	$grouping=implode(",",array_unique($po_data['grouping']));

	$colar_excess_percent=0;
	$cuff_excess_percent=0;
	$rmg_process_breakdown=0;
	$nameArray=sql_select( "select a.buyer_id,a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.delivery_date,a.is_apply_last_update,a.fabric_source,a.rmg_process_breakdown,a.insert_date,a.update_date, '' as uom,
      '' as remarks,a.pay_mode,'' as fabric_composition from wo_booking_mst_hstry a  where   a.booking_no='$txt_booking_no' and a.status_active =1 and a.is_deleted=0 and a.approved_no=$revised_no");

	foreach ($nameArray as $result)
	{
		$total_set_qnty=$result[csf('total_set_qnty')];
		$colar_excess_percent=$result[csf('colar_excess_percent')];
		$cuff_excess_percent=$result[csf('cuff_excess_percent')];
		$rmg_process_breakdown=$result[csf('rmg_process_breakdown')];
		foreach ($po_data['po_id'] as $po_id=>$po_val){
			$daysInHand.=(datediff('d',date('d-m-Y',time()),$po_data['pub_shipment_date_po'][$po_id])-1).",";
			$booking_date=$result[csf('update_date')];
			if($booking_date=="" || $booking_date=="0000-00-00 00:00:00"){
			$booking_date=$result[csf('insert_date')];
			}
			$WOPreparedAfter.=(datediff('d',$po_data['insert_date'][$po_id],$booking_date)-1).",";
		}
	?>
	<table width="100%" style="border:1px solid black;table-layout: fixed;" id="table_h" >
		<tr>
			<td colspan="6" valign="top" style="font-size:18px; color:#F00"><? if($result[csf('is_apply_last_update')]==2){echo "Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.  Contact to ".$marchentrArr[$result[csf('dealing_marchant')]]; } else{ echo "";} ?></td>
		</tr>
		<tr>
			<th>Buyer/Agent Name</th>	
			<td>:&nbsp;<? $buyer_name_str=""; if($client_id!=0) $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]]."-".$buyer_name_arr[$client_id]; else $buyer_name_str=$buyer_name_arr[$result[csf('buyer_id')]];  if($show_yarn_rate!=1) { echo $buyer_name_str; } ?></b></td>
			<th>Product Depertment</th>
			<td>:&nbsp;
			<?
				echo $product_depertment ;
				if($product_code !="")
				{
					echo " (".$product_code.")";
				}
				if($pro_sub_dep != "")
				{
					echo " (".$pro_sub_dep.")";
				}
			?>
			</td>
			<th>Order Qnty</th>
			<td>:&nbsp;<? 
				$l=1; 
				foreach ($uom_wise_qty as $uomid => $qty) {
					if($l==1){
						$poqtydata .= $qty."(".$unit_of_measurement[$uomid].")" ;	
					}
					else{
						$poqtydata .= $qty."(".$unit_of_measurement[$uomid]."), " ;
					}
					
				}
				echo $poqtydata; ?>
					
			</td>
		</tr>
		<tr>

			<td><b>Garments Item</b></td>
			<td>:&nbsp;
			<?
			$gmts_item_name="";
			$gmts_item=explode(',',$gmts_item_id);
			for($g=0;$g<=count($gmts_item); $g++)
			{
			$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
			}
			echo rtrim($gmts_item_name,',');
			?>
			</td>
			<td><b>Booking Release Date</b></td>
			<td>:&nbsp;
			<?
			$booking_date=$result[csf('update_date')];
			if($booking_date=="" || $booking_date=="0000-00-00 00:00:00")
			{
			$booking_date=$result[csf('insert_date')];
			}
			echo change_date_format($booking_date,'dd-mm-yyyy','-','');
			?>&nbsp;&nbsp;&nbsp;</td>
			<td><b>Style Ref.</b>   </td>
			<td style="font-size:18px">:&nbsp;<b>
			<?
			if($show_yarn_rate!=1) { echo $style_sting; }
			?>
			</b>
			</td>
		</tr>
		<tr>
			<td><b>Style Des.</b></td>
			<td>:&nbsp;<? echo $style_description;?></td>	
			<td><b>Dealing Merchandiser</b></td>
			<td>:&nbsp;<? echo $dealing_marchant; ?></td>
			<td><b>Factory Merchandiser</b></td>
			<td>:&nbsp;<? echo $factory_marchant; ?></td>
		</tr>

		<tr>
			<td><b>Supplier Name</b> </td>
			<td>:&nbsp;
			<?
			if($result[csf('pay_mode')]==5){
			echo $company_library[$result[csf('supplier_id')]];
			}
			else{
			echo $supplier_name_arr[$result[csf('supplier_id')]];
			}
			?>    </td>
			<td><b>Delivery Date</b></td>
			<td>:&nbsp;<? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
			<td><b>Booking No </b>   </td>
			<td>:&nbsp;<b><? echo $result[csf('booking_no')];?></b><? echo "(".$fabric_source[$result[csf('fabric_source')]].")"?> <? //echo "(".$unit_of_measurement[$result[csf('uom')]].")"; $uom=$result[csf('uom')];?></td>
		</tr>
		<tr>
			<td  class="table_valign"><p><b>Attention</b></p></td>
			<td class="table_valign" ><p>:&nbsp;<? echo $result[csf('attention')]; ?></p></td>
			<td class="table_valign"><p><b>Lead Time </b> </p>  </td>
			<td class="table_valign"><p>:&nbsp;
				<?
				echo $leadtime;
				?> </p>
			</td>
			<td class="table_valign" ><p><b>Po Received Date</b></p></td>
			<td class="table_valign"><p>:&nbsp;<? echo $po_received_date; ?></p></td>
		</tr>
		<tr>
			<td class="table_valign"><b>Order No</b></td>
			<td class="table_valign" colspan="3">:&nbsp;<? echo $po_number; ?></td>
			<td class="table_valign"><b>Repeat No</b></td>
			<td class="table_valign">:&nbsp;<? echo $order_repeat_no; ?></td>
		</tr>
		<tr>
			<td><b>Shipment Date</b></td>
			<td colspan="3" style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"> : First:&nbsp;<? echo rtrim($shipment_date,", "); //echo $max_pub_shipment_date; ?>, Last: <? echo $maxshipment_date; ?></td>
			<td><b>Quality Label</b></td>
			<td  >:&nbsp;<? echo $qlty_label; ?></td>
		</tr>
		</tr>
		<tr>
			<td><b>WO Prepared After</b></td>
			<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"> :&nbsp;
			<?
			$WOPreparedAfter=implode(",",array_unique(explode(",",chop($WOPreparedAfter,","))));
			echo $WOPreparedAfter.' Days' ;
			?></td>

			<td><b>Ship.days in Hand</b></td>
			<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"> :&nbsp;
			<?
			$daysInHand=implode(",",array_unique(explode(",",chop($daysInHand,","))));
			echo $daysInHand.' Days' ;
			?></td>

			<td><b>Ex-factory status</b></td>
			<td style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"> :&nbsp;
			<?
			echo $shiping_status;
			?></td>

		</tr>
		<tr>
			<td><b>Internal Ref No</b></td>
			<td> :&nbsp;<b><? echo $grouping; ?></b></td>
			<td><b>File no</b></td>
			<td> :&nbsp;<b><? echo  $file_no;?></b></td>
			<td><b>Currency</b></td>
			<td> :&nbsp;<b><? echo  $currency[$result[csf("currency_id")]];?></b></td>
		</tr>
		<tr>
			<td><b>Rmarks</b></td>
			<td colspan="3"> :<? echo $result[csf('remarks')]?></td>
			<td><b>Season</b></td>
			<td>:&nbsp;<?  if($season_matrix!="") echo $season_matrix; else echo $season_buyer_wise; ?></td>
		</tr>
		<tr>
			<td><b>Fabric Composition</b></td>
			<td colspan="5"> :<? echo $result[csf('fabric_composition')]?></td>
		</tr>

	</table>
	<?
	}

	if($cbo_fabric_source==1 || $cbo_fabric_source==2)
	{
	$nameArray_size=sql_select( "select  size_number_id,min(id) as id,	min(size_order) as size_order from wo_po_color_size_breakdown where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  	is_deleted=0 and status_active!=0 group by size_number_id order by size_order");

	?>
	<table width="100%" >
	<tr>
	<td width="800">
	<div id="div_size_color_matrix" style="float:left; max-width:1000;">
	<fieldset id="div_size_color_matrix" style="max-width:1000;">
	<legend>Size and Color Breakdown</legend>
	<table  class="rpt_table"  border="1" align="left" cellpadding="0" width="750" cellspacing="0" rules="all" >
	<tr>
		<td style="border:1px solid black"><strong>Color/Size</strong></td>
		<?
		foreach($nameArray_size  as $result_size)
		{	     ?>
		<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
		<?	}    ?>
		<td style="border:1px solid black; width:130px" align="center"><strong> Total Order Qty(Pcs)</strong></td>
		<td style="border:1px solid black; width:80px" align="center"><strong> Excess %</strong></td>
		<td style="border:1px solid black; width:130px" align="center"><strong> Total Plan Cut Qty(Pcs)</strong></td>
	</tr>
	<?
	$color_size_order_qnty_array=array();
	$color_size_qnty_array=array();
	$size_tatal=array();
	$size_tatal_order=array();
	for($c=0;$c<count($gmts_item); $c++)
	{
	$item_size_tatal=array();
	$item_size_tatal_order=array();
	$item_grand_total=0;
	$item_grand_total_order=0;
	$nameArray_color=sql_select( "select  color_number_id,min(id) as id,min(color_order) as color_order from wo_po_color_size_breakdown where  item_number_id=$gmts_item[$c] and po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active!=0 group by color_number_id  order by color_order");
	?>
	<tr>
		<td style="border:1px solid black" colspan="<? echo count($nameArray_size)+3;?>"><strong><? echo $garments_item[$gmts_item[$c]];?></strong></td>

	</tr>
	<?
	foreach($nameArray_color as $result_color)
	{
	?>
	<tr>
	<td align="center" style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; // echo $row_num_tr; ?></td>
	<?
	$color_total=0;
	$color_total_order=0;

	foreach($nameArray_size  as $result_size)
	{
	$nameArray_color_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as  order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$result_color[csf('color_number_id')]."  and item_number_id=$gmts_item[$c] and  status_active!=0 and is_deleted =0");
	foreach($nameArray_color_size_qnty as $result_color_size_qnty)
	{
	?>
	<td style="border:1px solid black; text-align:right">
	<?
	if($result_color_size_qnty[csf('plan_cut_qnty')]!= "")
	{
	echo number_format($result_color_size_qnty[csf('order_quantity')],0);
	$color_total += $result_color_size_qnty[csf('plan_cut_qnty')] ;
	$color_total_order += $result_color_size_qnty[csf('order_quantity')] ;
	$item_grand_total+=$result_color_size_qnty[csf('plan_cut_qnty')];
	$item_grand_total_order+=$result_color_size_qnty[csf('order_quantity')];
	$grand_total +=$result_color_size_qnty[csf('plan_cut_qnty')];
	$grand_total_order +=$result_color_size_qnty[csf('order_quantity')];


	$color_size_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
	$color_size_order_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
	if (array_key_exists($result_size[csf('size_number_id')], $size_tatal))
	{
	$size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
	$size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
	}
	else
	{
	$size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
	$size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
	}
	if (array_key_exists($result_size[csf('size_number_id')], $item_size_tatal))
	{
	$item_size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
	$item_size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
	}
	else
	{
	$item_size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
	$item_size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
	}
	}
	else echo "0";
	?>
	</td>

	<?
	}
	}
	?>
	<td style="border:1px solid black; text-align:right"><?  echo number_format(round($color_total_order),0); ?></td>

	<td style="border:1px solid black; text-align:right"><? $excexss_per=($color_total-$color_total_order)/$color_total_order*100; echo number_format($excexss_per,2)." %"; ?>
	</td>
	<td style="border:1px solid black; text-align:right"><? echo number_format(round($color_total),0); ?></td>
	</tr>
	<?
	}
	?>

	<td align="center" style="border:1px solid black"><strong>Sub Total</strong></td>
	<?
	foreach($nameArray_size  as $result_size)
	{
	?>
	<td style="border:1px solid black;  text-align:right"><? echo $item_size_tatal_order[$result_size[csf('size_number_id')]];  ?></td>
	<?
	}
	?>
	<td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($item_grand_total_order),0); ?></td>
	<td  style="border:1px solid black;  text-align:right"><? $excess_item_gra_tot=($item_grand_total-$item_grand_total_order)/$item_grand_total_order*100; echo number_format($excess_item_gra_tot,2)." %"; ?></td>
	<td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($item_grand_total),0); ?></td>
	</tr>
	<?
	}
	?>
	<tr>
	<td style="border:1px solid black" align="center" colspan="<? echo count($nameArray_size)+3; ?>"><strong>&nbsp;</strong></td>
	</tr>
	<tr>
	<tr>
	<td align="center" style="border:1px solid black"><strong>Grand Total</strong></td>
	<?
	foreach($nameArray_size  as $result_size)
	{
	?>
	<td style="border:1px solid black;  text-align:right"><? echo $size_tatal_order[$result_size[csf('size_number_id')]];  ?></td>
	<?
	}
	?>
	<td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($grand_total_order),0); ?></td>
	<td  style="border:1px solid black;  text-align:right"><? $excess_gra_tot= ($grand_total-$grand_total_order)/$grand_total_order*100; echo number_format($excess_gra_tot,2)." %"; ?></td>
	<td  style="border:1px solid black;  text-align:right"><?  echo number_format(round($grand_total),0); ?></td>
	</tr>
	</table>
	</fieldset>
	</div>
	</td>
	<td width="200" valign="top" align="left">
	<div id="div_size_color_matrix" style="float:left;">
	<?
	$rmg_process_breakdown_arr=explode('_',$rmg_process_breakdown)
	?>
	<fieldset>
	<legend>RMG Process Loss % </legend>
	<table width="180" class="rpt_table" border="1" rules="all">
	<?
	if(number_format($rmg_process_breakdown_arr[8],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Cut Panel rejection <!-- Extra Cutting % breack Down 8-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[8],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[2],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Chest Printing <!-- Printing % breack Down 2-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[2],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[10],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Neck/Sleeve Printing <!-- New breack Down 10-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[10],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[1],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Embroidery   <!-- Embroidery  % breack Down 1-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[1],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[4],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Sewing /Input<!-- Sewing % breack Down 4-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[4],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[3],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Garments Wash <!-- Washing %breack Down 3-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[3],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[15],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Gmts Finishing <!-- Washing %breack Down 3-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[15],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[11],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Others <!-- New breack Down 11-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[11],2);
	?>
	</td>
	</tr>
	<?
	}
	$gmts_pro_sub_tot=$rmg_process_breakdown_arr[8]+$rmg_process_breakdown_arr[2]+$rmg_process_breakdown_arr[10]+$rmg_process_breakdown_arr[1]+$rmg_process_breakdown_arr[4]+$rmg_process_breakdown_arr[3]+$rmg_process_breakdown_arr[11]+$rmg_process_breakdown_arr[15];
	if($gmts_pro_sub_tot>0)
	{
	?>
	<tr>
	<td width="130">
	Sub Total <!-- New breack Down 11-->
	</td>
	<td align="right">
	<?

	echo number_format($gmts_pro_sub_tot,2);
	?>
	</td>
	</tr>
	<?
	}
	?>
	</table>
	</fieldset>


	<fieldset>
	<legend>Fabric Process Loss % </legend>
	<table width="180" class="rpt_table" border="1" rules="all">
	<?
	if(number_format($rmg_process_breakdown_arr[6],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Knitting  <!--  Knitting % breack Down 6-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[6],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[12],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Yarn Dyeing  <!--  New breack Down 12-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[12],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[5],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Dyeing & Finishing  <!-- Finishing % breack Down 5-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[5],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[13],2)>0)
	{
	?>
	<tr>
	<td width="130">
	All Over Print <!-- new  breack Down 13-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[13],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[14],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Lay Wash (Fabric) <!-- new  breack Down 14-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[14],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[7],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Dying   <!-- breack Down 7-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[7],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[0],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Cutting (Fabric) <!-- Cutting % breack Down 0-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[0],2);
	?>
	</td>
	</tr>
	<?
	}
	if(number_format($rmg_process_breakdown_arr[9],2)>0)
	{
	?>
	<tr>
	<td width="130">
	Others  <!-- Others% breack Down 9-->
	</td>
	<td align="right">
	<?
	echo number_format($rmg_process_breakdown_arr[9],2);
	?>
	</td>
	</tr>
	<?
	}
	$fab_proce_sub_tot=$rmg_process_breakdown_arr[6]+$rmg_process_breakdown_arr[12]+$rmg_process_breakdown_arr[5]+$rmg_process_breakdown_arr[13]+$rmg_process_breakdown_arr[14]+$rmg_process_breakdown_arr[7]+$rmg_process_breakdown_arr[0]+$rmg_process_breakdown_arr[9];
	if(fab_proce_sub_tot>0)
	{
	?>
	<tr>
	<td width="130">
	Sub Total  <!-- Others% breack Down 9-->
	</td>
	<td align="right">
	<?

	echo number_format($fab_proce_sub_tot,2);
	?>
	</td>
	</tr>
	<?
	}
	if($gmts_pro_sub_tot+$fab_proce_sub_tot>0)
	{
	?>
	<tr>
	<td width="130">
	Grand Total  <!-- Others% breack Down 9-->
	</td>
	<td align="right">
	<?
	echo number_format($gmts_pro_sub_tot+$fab_proce_sub_tot,2);
	?>
	</td>
	</tr>
	<?
	}
	?>
	</table>
	</fieldset>
	</div>
	</td>
	<td width="330" valign="top" align="left">
	<?
	$nameArray_imge =sql_select("SELECT image_location FROM common_photo_library where master_tble_id in($job_no_in) and file_type=1");
	?>
	<div id="div_size_color_matrix" style="float:left;">
	<fieldset>
	<legend>Image</legend>
	<table width="310">
	<tr>
	<?
	$img_counter = 0;
	foreach($nameArray_imge as $result_imge)
	{
	if($path=="")
	{
	$path='../../';
	}
	?>
	<td>
	<img src="<? echo $path.$result_imge[csf('image_location')]; ?>" width="90" height="100" border="2" />
	</td>
	<?

	$img_counter++;
	}
	?>
	</tr>
	</table>
	</fieldset>
	</div>
	</td>
	</tr>
	</table>
	<?
	}// if($cbo_fabric_source==1) end

	?>
	<br/>
	<!--  Here will be the main portion  -->
	<?
	$costing_per="";
	$costing_per_qnty=0;
	$costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no in($job_no_in)");
	if($costing_per_id==1)
	{
	$costing_per="1 Dzn";
	$costing_per_qnty=12;

	}
	if($costing_per_id==2)
	{
	$costing_per="1 Pcs";
	$costing_per_qnty=1;

	}
	if($costing_per_id==3)
	{
	$costing_per="2 Dzn";
	$costing_per_qnty=24;

	}
	if($costing_per_id==4)
	{
	$costing_per="3 Dzn";
	$costing_per_qnty=36;

	}
	if($costing_per_id==5)
	{
	$costing_per="4 Dzn";
	$costing_per_qnty=48;
	}
	$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no in($job_no_in)");

	$uom_arr=array(1=>"Pcs",12=>"Kg",23=>"Mtr",27=>"Yds");

	foreach($uom_arr as $uom_id=>$uom_val){
	if($cbo_fabric_source==1)
	{

	$nameArray_fabric_description= sql_select("SELECT a.job_no, a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type as width_dia_type , b.dia_width,'' as pre_cost_remarks, avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent, avg(b.requirment) as requirment  FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls_hstry d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and b.po_break_down_id=d.po_break_down_id and b.color_number_id=d.gmts_color_id and b.dia_width=d.dia_width and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and d.booking_no ='$txt_booking_no' and a.uom=$uom_id and d.status_active=1 and d.is_deleted=0 and b.cons>0 and d.approved_no=$revised_no group by a.job_no, a.id,a.item_number_id,a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,a.width_dia_type,b.dia_width order by fabric_cost_dtls_id,a.body_part_id,b.dia_width"); 
   

	if(count($nameArray_fabric_description)>0){
	?>

	<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
	<caption style="font-weight: bold; font-size: 18px">Fabric Details in <? echo $uom_val ?></caption>
	<tr align="center">
	<th colspan="3" align="left">Job</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)
	{	if( $result_fabric_description[csf('job_no')] == "")
		echo "<td colspan='3'>&nbsp</td>";
		else
		echo "<td colspan='3'>". $result_fabric_description[csf('job_no')]."</td>";
	}
	?>	
	<td  rowspan="13" width="50"><p>Total Finish Fabric</p></td>

	<td  rowspan="13" width="50"><p>Avg Rate</p></td>
	<td  rowspan="13" width="50"><p>Amount </p></td>
	</tr>
	<tr align="center">
		<th colspan="3" align="left">Style</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)
	{   if( $result_fabric_description[csf('job_no')] == "")
		{
			echo "<td colspan='3'>&nbsp</td>";
		}		
		else
		{
			if($show_yarn_rate!=1) { 
			echo "<td colspan='3'>". $job_data_arr['style_ref_no'][$result_fabric_description[csf('job_no')]]."</td>";
			}
			else{
				echo "<td colspan='3'>&nbsp</td>";
			}
		}		
	}?>	
	</tr>
	<tr align="center">
	<th colspan="3" align="left">Item Name</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('body_part_id')] == "")
	echo "<td colspan='3'>&nbsp</td>";
	else
	echo "<td colspan='3'>". $garments_item[$result_fabric_description[csf('item_number_id')]]."</td>";
	}
	?>
	</tr>
	<tr align="center">
	<th colspan="3" align="left">Body Part</th>
	<?

	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('body_part_id')] == "")
	echo "<td colspan='3'>&nbsp</td>";
	else
	echo "<td colspan='3'>".$body_part[$result_fabric_description[csf('body_part_id')]]."</td>";
	}
	?>
	</tr>
	<tr align="center"><th colspan="3" align="left">Color Type</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";
	else         		               echo "<td  colspan='3'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";
	}
	?>
	</tr>
	<tr align="center"><th colspan="3" align="left">Fabric Construction</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='3'>&nbsp</td>";
	else         		               echo "<td  colspan='3'>". $result_fabric_description[csf('construction')]."</td>";
	}
	?>


	</tr>
	<tr align="center"><th   colspan="3" align="left">Yarn Composition</th>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='3' >&nbsp</td>";
	else echo "<td colspan='3' >".$result_fabric_description[csf('composition')]."</td>";
	}
	?>

	</tr>
	<tr align="center"><th  colspan="3" align="left">GSM</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='3'>&nbsp</td>";
	else echo "<td colspan='3' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";
	}
	?>

	</tr>
	<tr align="center"><th   colspan="3" align="left">Dia/Width (Inch)</th>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='3'>&nbsp</td>";
	else         		              echo "<td colspan='3' align='center'>".$result_fabric_description[csf('dia_width')].",".$fabric_typee[$result_fabric_description[csf('width_dia_type')]]."</td>";
	}
	?>

	</tr>
	<tr align="center"><th   colspan="3" align="left">Consumption For <? echo $costing_per; ?></th>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	if( $result_fabric_description[csf('cons')] == "")   echo "<td colspan='3'>&nbsp</td>";
	else         		              echo "<td colspan='3' align='center'>Fin: ".number_format($result_fabric_description[csf('cons')],2)."</td>";
	}
	?>
	</tr>
    <tr align="center"><th   colspan="3" align="left">Remarks</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('pre_cost_remarks')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else         		              echo "<td colspan='3' align='center'>".$result_fabric_description[csf('pre_cost_remarks')]."</td>";
		}
		?>

       </tr>
	<tr>
	<th  colspan="<? echo  count($nameArray_fabric_description)*3+3; ?>" align="left" style="height:30px">&nbsp;</th>
	</tr>
	<tr>
	<th  width="120" align="left">Fabric Color</th>
	<th  width="120" align="left">Body Color</th>
	<th  width="120" align="left">Lab Dip No</th>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	echo "<th width='50'>Fab. Qty</th><th width='50' >Rate</th><th width='50' >Amount</th>";
	}
	?>
	</tr>
	<?
	$gmt_color_library=array();
	$gmt_color_data=sql_select("select b.gmts_color_id, b.contrast_color_id
	FROM
	wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b
	WHERE a.id=b.pre_cost_fabric_cost_dtls_id and a.fab_nature_id=$cbo_fabric_natu and a.uom=$uom_id  and a.fabric_source =$cbo_fabric_source and
	a.job_no in ($job_no_in)");
	foreach( $gmt_color_data as $gmt_color_row){
	$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]][$gmt_color_row[csf("gmts_color_id")]]=$color_library[$gmt_color_row[csf("gmts_color_id")]];
	}
	$grand_total_fin_fab_qnty=0;
	$grand_total_amount=0;
	$color_wise_wo_sql=sql_select("select b.fabric_color_id
	FROM
	wo_pre_cost_fabric_cost_dtls a,
	wo_booking_dtls_hstry b
	WHERE
	a.id=b.pre_cost_fabric_cost_dtls_id and
	a.uom=$uom_id and
	b.booking_no ='$txt_booking_no' and
	b.status_active=1 and
	b.is_deleted=0
	and b.approved_no=$revised_no
	group by b.fabric_color_id");
	foreach($color_wise_wo_sql as $color_wise_wo_result)
	{
	?>
	<tr>
	<td  width="120" align="left">
	<?
	echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
	?>
	</td>
	<td>
	<?
	echo implode(",",$gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]]);
	?>
	</td>
	<td  width="120" align="left">
	<?
	$lapdip_no="";
	$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."");
	if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no;
	?>
	</td>
	<?
	$total_fin_fab_qnty=0;
	$total_amount=0;

	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	if($db_type==0)
	{
	$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.fin_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls_hstry d
	WHERE
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no ='$txt_booking_no' and
	a.uom=$uom_id and
	a.id='".$result_fabric_description[csf('fabric_cost_dtls_id')]."' and
	a.item_number_id='".$result_fabric_description[csf('item_number_id')]."' and
	a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
	a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
	a.construction='".$result_fabric_description[csf('construction')]."' and
	a.composition='".$result_fabric_description[csf('composition')]."' and
	a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
	d.dia_width='".$result_fabric_description[csf('dia_width')]."' and
	d.pre_cost_remarks='".$result_fabric_description[csf('pre_cost_remarks')]."' and
	d.fabric_color_id='".$color_wise_wo_result[csf('fabric_color_id')]."' and
	d.status_active=1 and
	d.is_deleted=0
	and d.approved_no=$revised_no
	");
	}
	if($db_type==2)
	{
	$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.fin_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls_hstry d
	WHERE
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no ='$txt_booking_no' and
	a.uom=$uom_id and
	a.id='".$result_fabric_description[csf('fabric_cost_dtls_id')]."' and
	a.item_number_id='".$result_fabric_description[csf('item_number_id')]."' and
	a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
	a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
	a.construction='".$result_fabric_description[csf('construction')]."' and
	a.composition='".$result_fabric_description[csf('composition')]."' and
	a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
	d.dia_width='".$result_fabric_description[csf('dia_width')]."' and
	d.pre_cost_remarks='".$result_fabric_description[csf('pre_cost_remarks')]."' and
	nvl(d.fabric_color_id,0)=nvl('".$color_wise_wo_result[csf('fabric_color_id')]."',0) and
	d.status_active=1 and
	d.is_deleted=0
	and d.approved_no=$revised_no
	");
	}
	list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
	?>
	<td width='50' align='right'>
	<?
	if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
	{
	echo def_number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;
	$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
	}
	?>
	</td>
	<td width='50' align='right' >
	<?
	if($color_wise_wo_result_qnty[csf('rate')]!="")
	{
	echo def_number_format($color_wise_wo_result_qnty[csf('rate')],5);
	}
	?>
	</td>
	<td width='50' align='right' >
	<?
	$amount=def_number_format($color_wise_wo_result_qnty[csf('amount')],2,'',0);
	if($amount!="")
	{
	echo $amount;
	$total_amount+=$amount;
	}
	?>
	</td>
	<?
	}
	?>
	<td align="right"><? echo def_number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
	<td align="right"><? echo def_number_format($total_amount/$total_fin_fab_qnty,2); $grand_total_amount+=$total_amount;?></td>
	<td align="right">
	<?
	echo def_number_format($total_amount,2);

	?>
	</td>
	</tr>
	<?
	}
	?>
	<tr style=" font-weight:bold">
	<th  width="120" align="left">&nbsp;</th>
	<td  width="120" align="left">&nbsp;</td>
	<td  width="120" align="left"><strong>Total</strong></td>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.fin_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls_hstry d
	WHERE
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no ='$txt_booking_no' and
	a.uom=$uom_id and
	a.item_number_id='".$result_fabric_description[csf('item_number_id')]."' and
	a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
	a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
	a.construction='".$result_fabric_description[csf('construction')]."' and
	a.composition='".$result_fabric_description[csf('composition')]."' and
	a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
	d.dia_width='".$result_fabric_description[csf('dia_width')]."' and
	d.pre_cost_remarks='".$result_fabric_description[csf('pre_cost_remarks')]."' and
	d.status_active=1 and
	d.is_deleted=0
	and d.approved_no=$revised_no
	");
	list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
	?>
	<td width='50' align='right'><?  echo def_number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;?></td>
	<td width='50' align='right'></td>
	<td width='50' align='right'></td>
	<?
	}
	?>
	<td align="right"><? echo def_number_format($grand_total_fin_fab_qnty,2);?></td>
	<td align="right"><? echo def_number_format($grand_total_amount/$grand_total_fin_fab_qnty,2);?></td>
	<td align="right">
	<?
	echo def_number_format($grand_total_amount,2);
	?>
	</td>
	</tr>	
	</table>
	<br/>
	<?
	}
	}
	}
	//===========================

	foreach($uom_arr as $uom_id=>$uom_val){
	if($cbo_fabric_source==2){
	$nameArray_fabric_description= sql_select("SELECT a.job_no, a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type as width_dia_type , b.dia_width,'' as pre_cost_remarks, avg(b.cons) as cons  , avg(b.process_loss_percent) as process_loss_percent, avg(b.requirment) as requirment  FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls_hstry d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and b.po_break_down_id=d.po_break_down_id and b.color_number_id=d.gmts_color_id and b.dia_width=d.dia_width and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and d.booking_no ='$txt_booking_no' and a.uom=$uom_id and d.status_active=1 and d.is_deleted=0  and b.cons>0 and d.approved_no=$revised_no group by a.id,a.item_number_id,a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,a.width_dia_type,b.dia_width,a.job_no order by fabric_cost_dtls_id,a.body_part_id,b.dia_width"); 
	if(count($nameArray_fabric_description)>0){
	?>
	<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
	<caption style="font-weight: bold; font-size: 18px">Fabric Details in <? echo $uom_val;?></caption>
	<tr align="center">
	<th colspan="3" align="left">Job</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)
	{	if( $result_fabric_description[csf('job_no')] == "")
		echo "<td colspan='3'>&nbsp</td>";
		else
		echo "<td colspan='3'>". $result_fabric_description[csf('job_no')]."</td>";
	}
	?>	
	<td  rowspan="13" width="50"><p>Total Finish Fabric</p></td>

	<td  rowspan="13" width="50"><p>Avg Rate</p></td>
	<td  rowspan="13" width="50"><p>Amount </p></td>
	</tr>
	<tr align="center">
		<th colspan="3" align="left">Style</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)
	{   
		if( $result_fabric_description[csf('job_no')] == "")
		{
			echo "<td colspan='3'>&nbsp</td>";
		}		
		else
		{
			if($show_yarn_rate!=1) { 
			echo "<td colspan='3'>". $job_data_arr['style_ref_no'][$result_fabric_description[csf('job_no')]]."</td>";
			}
			else{
				echo "<td colspan='3'>&nbsp</td>";
			}
		}
	}?>	
	</tr>
	<tr align="center">
	<th colspan="3" align="left">Item Name</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('body_part_id')] == "")
	echo "<td colspan='3'>&nbsp</td>";
	else
	echo "<td colspan='3'>". $garments_item[$result_fabric_description[csf('item_number_id')]]."</td>";
	}
	?>
	</tr>
	<tr align="center">
	<th colspan="3" align="left">Body Part</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('body_part_id')] == "")
	echo "<td colspan='3'>&nbsp</td>";
	else
	echo "<td colspan='3'>".$body_part[$result_fabric_description[csf('body_part_id')]]."</td>";
	}
	?>
	</tr>
	<tr align="center"><th colspan="3" align="left">Color Type</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";
	else         		               echo "<td  colspan='3'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";
	}
	?>
	</tr>
	<tr align="center"><th colspan="3" align="left">Fabric Construction</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='3'>&nbsp</td>";
	else         		               echo "<td  colspan='3'>". $result_fabric_description[csf('construction')]."</td>";
	}
	?>
	</tr>
	<tr align="center"><th   colspan="3" align="left">Yarn Composition</th>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='3' >&nbsp</td>";
	else         		               echo "<td colspan='3' >".$result_fabric_description[csf('composition')]."</td>";
	}
	?>
	</tr>
	<tr align="center"><th  colspan="3" align="left">GSM</th>
	<?
	foreach($nameArray_fabric_description  as $result_fabric_description)
	{
	if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='3'>&nbsp</td>";
	else         		       echo "<td colspan='3' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";
	}
	?>
	</tr>
	<tr align="center"><th   colspan="3" align="left">Dia/Width (Inch)</th>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='3'>&nbsp</td>";
	else         		              echo "<td colspan='3' align='center'>".$result_fabric_description[csf('dia_width')].",".$fabric_typee[$result_fabric_description[csf('width_dia_type')]]."</td>";
	}
	?>

	</tr>
	<tr align="center"><th   colspan="3" align="left">Consumption For <? echo $costing_per; ?></th>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	if( $result_fabric_description[csf('cons')] == "")   echo "<td colspan='3'>&nbsp</td>";
	else         		              echo "<td colspan='3' align='center'>Fin: ".number_format($result_fabric_description[csf('cons')],2)."</td>";
	}
	?>

	</tr>
    <tr align="center"><th   colspan="3" align="left">Remarks</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('pre_cost_remarks')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else         		              echo "<td colspan='3' align='center'>".$result_fabric_description[csf('pre_cost_remarks')]."</td>";
		}
		?>

       </tr>
	<tr>
	<th  colspan="<? echo  count($nameArray_fabric_description)*3+3; ?>" align="left" style="height:30px">&nbsp;</th>
	</tr>
	<tr>
	<th  width="120" align="left">Fabric Color</th>
	<th  width="120" align="left">Body Color</th>
	<th  width="120" align="left">Lab Dip No</th>
	<?
	if($cbo_fabric_source==2)
	{
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	echo "<th width='50'>Fab. Qty</th><th width='50' >Rate</th><th width='50' >Amount</th>";
	}
	}
	else
	{
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	echo "<th width='50'>Fab. Qty</th>";
	}
	}

	?>
	</tr>
	<?
	$gmt_color_library=array();

	$gmt_color_data=sql_select("select b.gmts_color_id, b.contrast_color_id
	FROM
	wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b
	WHERE a.id=b.pre_cost_fabric_cost_dtls_id and a.fab_nature_id=$cbo_fabric_natu and a.uom=$uom_id  and a.fabric_source =$cbo_fabric_source and
	a.job_no in ($job_no_in)");
	
	foreach( $gmt_color_data as $gmt_color_row){
	$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]][$gmt_color_row[csf("gmts_color_id")]]=$color_library[$gmt_color_row[csf("gmts_color_id")]];
	}
	$grand_total_fin_fab_qnty=0;
	$grand_total_amount=0;
	$color_wise_wo_sql=sql_select("select b.fabric_color_id
	FROM
	wo_pre_cost_fabric_cost_dtls a,
	wo_booking_dtls_hstry b
	WHERE
	a.id=b.pre_cost_fabric_cost_dtls_id and
	a.uom=$uom_id and
	b.booking_no ='$txt_booking_no' and
	b.status_active=1 and
	b.is_deleted=0
	and b.approved_no=$revised_no
	group by b.fabric_color_id");
	
	foreach($color_wise_wo_sql as $color_wise_wo_result)
	{
	?>
	<tr>
	<td  width="120" align="left">
	<?
	echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
	?>
	</td>
	<td>
	<?
	echo implode(",",$gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]]);
	?>
	</td>
	<td  width="120" align="left">
	<?
	$lapdip_no="";
	$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."");
	if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no;
	?>
	</td>
	<?
	$total_fin_fab_qnty=0;
	$total_amount=0;

	foreach($nameArray_fabric_description as $result_fabric_description)
	{
	if($db_type==0)
	{
	$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.fin_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls_hstry d
	WHERE
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no ='$txt_booking_no' and
	a.uom=$uom_id and
	a.id='".$result_fabric_description[csf('fabric_cost_dtls_id')]."' and
	a.item_number_id='".$result_fabric_description[csf('item_number_id')]."' and
	a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
	a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
	a.construction='".$result_fabric_description[csf('construction')]."' and
	a.composition='".$result_fabric_description[csf('composition')]."' and
	a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
	d.dia_width='".$result_fabric_description[csf('dia_width')]."' and
	
	d.fabric_color_id='".$color_wise_wo_result[csf('fabric_color_id')]."' and
	d.status_active=1 and
	d.is_deleted=0
	and d.approved_no=$revised_no
	");
	
	}
	if($db_type==2)
	{
	$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.fin_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls_hstry d
	WHERE
	a.job_no=d.job_no and
	a.id=d.pre_cost_fabric_cost_dtls_id and
	d.booking_no ='$txt_booking_no' and
	a.uom=$uom_id and
	a.id='".$result_fabric_description[csf('fabric_cost_dtls_id')]."' and
	a.item_number_id='".$result_fabric_description[csf('item_number_id')]."' and
	a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
	a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
	a.construction='".$result_fabric_description[csf('construction')]."' and
	a.composition='".$result_fabric_description[csf('composition')]."' and
	a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
	d.dia_width='".$result_fabric_description[csf('dia_width')]."' and
	
	nvl(d.fabric_color_id,0)=nvl('".$color_wise_wo_result[csf('fabric_color_id')]."',0) and
	d.status_active=1 and
	d.is_deleted=0
	and d.approved_no=$revised_no
	");

	}
	list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
	?>
	<td width='50' align='right'>
	<?
	if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
	{
	echo def_number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;
	$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
	}
	?>
	</td>
	<td width='50' align='right' >
	<?
	if($color_wise_wo_result_qnty[csf('rate')]!="")
	{
	echo def_number_format($color_wise_wo_result_qnty[csf('rate')],5);
	}
	?>
	</td>
	<td width='50' align='right' >
	<?
	$amount=def_number_format($color_wise_wo_result_qnty[csf('amount')],2,'',0);
	if($amount!="")
	{
	echo $amount;
	$total_amount+=$amount;
	}
	?>
	</td>
	<?
	}
	?>
	<td align="right"><? echo def_number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
	<td align="right"><? echo def_number_format($total_amount/$total_fin_fab_qnty,2); $grand_total_amount+=$total_amount;?></td>
	<td align="right">
	<?
	echo def_number_format($total_amount,2);

	?>
	</td>
	</tr>
	<?
	}
	?>
	<tr style=" font-weight:bold">
	<th  width="120" align="left">&nbsp;</th>
	<td  width="120" align="left">&nbsp;</td>
	<td  width="120" align="left"><strong>Total</strong></td>
	<?
	foreach($nameArray_fabric_description as $result_fabric_description)
	{
		$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate,sum(d.fin_fab_qnty*d.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls_hstry d
		WHERE
		a.job_no=d.job_no and
		a.id=d.pre_cost_fabric_cost_dtls_id and
		d.booking_no ='$txt_booking_no' and
		a.uom=$uom_id and
		a.id='".$result_fabric_description[csf('fabric_cost_dtls_id')]."' and
		a.item_number_id='".$result_fabric_description[csf('item_number_id')]."' and
		a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
		a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
		a.construction='".$result_fabric_description[csf('construction')]."' and
		a.composition='".$result_fabric_description[csf('composition')]."' and
		a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
		d.dia_width='".$result_fabric_description[csf('dia_width')]."' and
		
		d.status_active=1 and
		d.is_deleted=0
		and d.approved_no=$revised_no
		");
		list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
		?>
		<td width='50' align='right'><?  echo def_number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;?></td>
		<td width='50' align='right' ></td>
		<td width='50' align='right' ></td>
		<?
	}
	?>
	<td align="right"><? echo def_number_format($grand_total_fin_fab_qnty,2);?></td>
	<td align="right"><? echo number_format($grand_total_amount/$grand_total_fin_fab_qnty,2);?></td>
	<td align="right">
	<?
	echo def_number_format($grand_total_amount,2);
	?>
	</td>
	</tr>
	</table>
	<br/>
	<?
	}
	}
	}
	//===========================
	?>
    <?
	$sql_data=sql_select("select a.id as fabric_cost_dtls_id, a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type,a.uom,b.dia_width,b.pre_cost_remarks,b.fabric_color_id,b.remark, sum(b.grey_fab_qnty) as grey_fab_qnty,sum(b.adjust_qty)as adjust_qty,sum(b.fin_fab_qnty) as fin_fab_qnty,avg(b.rate) as rate,sum(b.grey_fab_qnty*b.rate) as amount FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls_hstry b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no ='$txt_booking_no' and b.adjust_qty>0 and b.status_active=1 and b.is_deleted=0 and b.approved_no=$revised_no group by a.id,a.item_number_id, a.body_part_id,a.color_type_id, a.construction, a.composition, a.gsm_weight,a.width_dia_type,a.uom,b.dia_width,b.pre_cost_remarks,b.fabric_color_id,b.remark order by a.id,a.body_part_id");

		?>
		<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">

        <tr>
        <td colspan="7">
        <strong>Fabric Stock Adjustment Details</strong>
        </td>

        </tr>

        <tr>
        <td>
        Fabrication
        </td>
        <td>
        Process
        </td>
        <td>
        Fabric Color
        </td>
        <td>
        Required
        </td>
        <td>
        Stock Used
        </td>
        <td>
        Booking Qty
        </td><td>
        Uom
        </td>
         <td>
        Remarks
        </td>
        </tr>
        <?
		foreach($sql_data as $row){
		?>
          <tr>
        <td>
        <? echo $body_part[$row[csf('body_part_id')]].",".$color_type[$row[csf('color_type_id')]].",".$row[csf('construction')].",".$row[csf('composition')].",".$row[csf('gsm_weight')].",".$fabric_typee[$row[csf('width_dia_type')]].",".$row[csf('dia_width')]  ?>
        </td>
        <td>
        <? echo $row[csf('pre_cost_remarks')];  ?>
        </td>
        <td>
        <? echo $color_library[$row[csf('fabric_color_id')]];  ?>
        </td>
        <td align="right">
        <? echo number_format($row[csf('grey_fab_qnty')],4);  ?>
        </td>
        <td align="right">
         <? echo number_format($row[csf('adjust_qty')],4) ; ?>
        </td>
        <td align="right">
       <? echo number_format($row[csf('fin_fab_qnty')],4);  ?>
        </td>
         <td>
        <? echo $unit_of_measurement[$row[csf('uom')]];  ?>
        </td>
         <td>
         <? echo $row[csf('remark')];  ?>
        </td>
        </tr>
        <?
		}
		?>
        </table>


	<?
	//echo $cbo_fabric_source;
	if($cbo_fabric_source==1 || $cbo_fabric_source==2){
	?>
	<table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
	<tr>
	<?

	$nameArray_item_size=sql_select( "select min(c.id) as id,b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls_hstry d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no ='$txt_booking_no'  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 and d.approved_no=$revised_no group by b.item_size,c.size_number_id order by id");
	if(count($nameArray_item_size)>0)
	{
	?>
	<td width="49%">
		<!-- here -->
		<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
			<tr>
				<td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Collar -  Colour Size Brakedown in Pcs</b></td>
			</tr>
			<tr>
				<td width="70">Size</td>
				<?
				foreach($nameArray_item_size  as $result_size)
				{
				?>
				<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
				<?
				}
				?>
				<td rowspan="2" align="center"><strong>Total</strong></td>
				<td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
			</tr>
			<tr>
			<td>Collar Size</td>

				<?
				foreach($nameArray_item_size  as $result_item_size)
				{
					?>
					<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
					<?
				}
				?>
			<?
			$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls_hstry d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no ='$txt_booking_no' and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and d.approved_no=$revised_no group by c.color_number_id,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id
			");
			//h
			foreach($color_wise_wo_sql as $color_wise_wo_result)
			{
				$color_total_collar=0;
				$color_total_collar_order_qnty=0;
				$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
				$constrast_color_arr=array();
				if($color_wise_wo_result[csf("color_size_sensitive")]==3)
				{
					$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
					for($i=0;$i<count($constrast_color);$i++)
					{
						$constrast_color2=explode('_',$constrast_color[$i]);
						$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
					}
				}
				?>
				<tr>
					<td>
						<?
						if($color_wise_wo_result[csf("color_size_sensitive")]==3)
						{
						echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]) ;
						$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
						}
						else
						{
						echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
						$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
						}
						?>
					</td>
					<?

					foreach($nameArray_item_size  as $result_size)
					{
								$sql_excess_per="select a.po_break_down_id,a.gmts_color_id,a.size_number_id,a.excess_per,b.body_part_id FROM wo_booking_colar_culff_dtls a,wo_pre_cost_fabric_cost_dtls b where
								 a.booking_no ='$txt_booking_no' and a.pre_cost_fabric_cost_dtls_id=b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  a.size_number_id =".$result_size[csf('size_number_id')]." and a.gmts_color_id =".$color_wise_wo_result[csf('color_number_id')]." and b.body_part_id=2 and a.status_active=1 ";
								$resultData=sql_select($sql_excess_per);
								list($excess_percent)=$resultData;
								$colar_excess_percent=$excess_percent[csf('excess_per')];

								?>
								<td align="center" style="border:1px solid black" title="<? echo $colar_excess_percentage;?>">
									<?


									$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active!=0 and is_deleted =0");
									list($plan_cut_qnty)=$color_wise_wo_sql_qnty;
									$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
									$colar_excess_per=($plan_cut*$colar_excess_percent)/100;
									echo number_format($plan_cut+$colar_excess_per,0);
									$color_total_collar+=$plan_cut+$colar_excess_per;
									$color_total_collar_order_qnty+=$plan_cut;
									$grand_total_collar+=$plan_cut+$colar_excess_per;
									$grand_total_collar_order_qnty+=$plan_cut;
									?>
								</td>
								<?
					}

					?>
					<td align="center"><? echo number_format($color_total_collar,0); ?></td>
					<td align="center"><? echo number_format((($color_total_collar-$color_total_collar_order_qnty)/$color_total_collar_order_qnty)*100,2); ?></td>
				</tr>
				<?
			}
			?>
			<tr>
			<td>Size Total</td>
			<?
			foreach($nameArray_item_size  as $result_size)
			{
			?>
			<td style="border:1px solid black;  text-align:center"><? $colar_excess_pers=($size_tatal[$result_size[csf('size_number_id')]]*$colar_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]+$colar_excess_pers,0); ?></td>
			<?
			}
			?>
			<td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_collar,0); ?></td>
			<td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_collar-$grand_total_collar_order_qnty)/$grand_total_collar_order_qnty)*100,2); ?></td>
			</tr>
		</table>
	</td>
	<td width="2%">
	</td>
	<?
	}
	?>
	<?
	$nameArray_item_size=sql_select( "select min(c.id) as id, b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no ='$txt_booking_no'  and a.body_part_id=3  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");

	if(count($nameArray_item_size)>0)
	{
		?>
		<td width="49%">
		<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
			<tr>
				<td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Cuff -  Colour Size Brakedown in Pcs</b></td>
			</tr>
			<tr>
				<td width="70">Size</td>

				<?
				foreach($nameArray_item_size  as $result_size)
				{
				?>
				<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
				<?
				}
				?>
				<td rowspan="2" align="center"><strong>Total</strong></td>
				<td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
			</tr>
			<tr>
			<td>Cuff Size</td>

			<?
			foreach($nameArray_item_size  as $result_item_size)
			{
				?>
				<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
				<?
			}
			?>
			<?
			$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no ='$txt_booking_no' and a.body_part_id=3  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and  d.status_active=1 and d.is_deleted=0 group by c.color_number_id ,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id
			");
			foreach($color_wise_wo_sql as $color_wise_wo_result)
			{
				$color_total_cuff=0;
				$color_total_cuff_order_qnty=0;
				$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
				$constrast_color_arr=array();
				if($color_wise_wo_result[csf("color_size_sensitive")]==3)
				{
					$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
					for($i=0;$i<count($constrast_color);$i++)
					{
						$constrast_color2=explode('_',$constrast_color[$i]);
						$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
					}
				}
				?>
				<tr>
					<td>
					<?
					if($color_wise_wo_result[csf("color_size_sensitive")]==3)
					{
					echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]);
					$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
					}
					else
					{
					echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
					$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
					}
					?>
					</td>
					<?
					foreach($nameArray_item_size  as $result_size)
					{

						 	$sql_excess_cuff="select a.po_break_down_id,a.gmts_color_id,a.size_number_id,a.excess_per,b.body_part_id FROM wo_booking_colar_culff_dtls a,wo_pre_cost_fabric_cost_dtls b where a.booking_no ='$txt_booking_no' and a.pre_cost_fabric_cost_dtls_id=b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  a.size_number_id =".$result_size[csf('size_number_id')]." and a.gmts_color_id =".$color_wise_wo_result[csf('color_number_id')]." and b.body_part_id=3 and a.status_active=1 ";
							$resultData_cuff=sql_select($sql_excess_cuff);
							list($cuff_excess_percent)=$resultData_cuff;
							$cuff_excess_percent=$cuff_excess_percent[csf('excess_per')];
							?>
							<td align="center" style="border:1px solid black">
								<?
								$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active!=0 and is_deleted =0");
								list($plan_cut_qnty)=$color_wise_wo_sql_qnty;
								$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
								$cuff_excess_per=(($plan_cut*2)*$cuff_excess_percent)/100;
								echo number_format($plan_cut*2+$cuff_excess_per,0);
								$color_total_cuff+=$plan_cut*2+$cuff_excess_per;
								$color_total_cuff_order_qnty+=$plan_cut*2;
								$grand_total_cuff+=$plan_cut*2+$cuff_excess_per;
								$grand_total_cuff_order_qnty+=$plan_cut*2;
								?>
							</td>
							<?
					}
					?>
					<td align="center"><? echo number_format($color_total_cuff,0); ?></td>
					<td align="center"><? echo number_format((($color_total_cuff-$color_total_cuff_order_qnty)/$color_total_cuff_order_qnty)*100,2); ?></td>
				</tr>
				<?
			}
			?>
			<tr>
				<td>Size Total</td>
				<?
				foreach($nameArray_item_size  as $result_size)
				{
					?>
					<td style="border:1px solid black;  text-align:center"><? $cuff_excess_pers=(($size_tatal[$result_size[csf('size_number_id')]]*2)*$cuff_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]*2+$cuff_excess_pers,0); ?></td>
					<?
				}
				?>
				<td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_cuff,0); ?></td>
				<td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_cuff-$grand_total_cuff_order_qnty)/$grand_total_cuff_order_qnty)*100,2); ?></td>
			</tr>
		</table>
		</td>
		<?
	}
	?>
	</tr>
	</table>
		<br/>
		<table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
			<tr>
				<?

				$nameArray_item_size=sql_select( "select min(c.id) as id,b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no ='$txt_booking_no'  and a.body_part_id=172  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");
				if(count($nameArray_item_size)>0)
				{
					?>
					<td width="49%">
						<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
							<tr>
								<td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Collar Tipping -  Colour Size Brakedown in Pcs</b></td>
							</tr>
							<tr>
								<td width="70">Size</td>
								<?
								foreach($nameArray_item_size  as $result_size)
								{
								?>
								<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
								<?
								}
								?>
								<td rowspan="2" align="center"><strong>Total</strong></td>
								<td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
							</tr>
							<tr>
							<td>Collar Size</td>
							<?
							foreach($nameArray_item_size  as $result_item_size)
							{
							?>
							<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
							<?
							}
							?>
							<?
							$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no ='$txt_booking_no' and a.body_part_id=172  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by c.color_number_id,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id
							");
							foreach($color_wise_wo_sql as $color_wise_wo_result)
							{
								$color_total_collar_tipping=0;
								$color_total_collar_tipping_order_qnty=0;
								$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
								$constrast_color_arr=array();
								if($color_wise_wo_result[csf("color_size_sensitive")]==3)
								{
									$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
									for($i=0;$i<count($constrast_color);$i++)
									{
										$constrast_color2=explode('_',$constrast_color[$i]);
										$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
									}
								}
								?>
								<tr>
									<td>
									<?
									if($color_wise_wo_result[csf("color_size_sensitive")]==3)
									{
										echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]) ;
										$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
									}
									else
									{
										echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
										$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
									}
									?>
									</td>
									<?
									foreach($nameArray_item_size  as $result_size)
									{
											$sql_excess_collarTip="select a.po_break_down_id,a.gmts_color_id,a.size_number_id,a.excess_per,b.body_part_id FROM wo_booking_colar_culff_dtls a,wo_pre_cost_fabric_cost_dtls b where
										 a.booking_no ='$txt_booking_no' and a.pre_cost_fabric_cost_dtls_id=b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  a.size_number_id =".$result_size[csf('size_number_id')]." and a.gmts_color_id =".$color_wise_wo_result[csf('color_number_id')]." and b.body_part_id=172 and a.status_active=1 ";
										$resultData_collar_tip=sql_select($sql_excess_collarTip);
										list($collarTip_excess_percent)=$resultData_collar_tip;
										$colar_excess_percent=$collarTip_excess_percent[csf('excess_per')];

										?>
										<td align="center" style="border:1px solid black">
											<?
											$color_tipping_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active!=0 and is_deleted =0");

											list($plan_cut_qnty)=$color_tipping_wise_wo_sql_qnty;
											$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
											$colar_excess_per=($plan_cut*$colar_excess_percent)/100;
											echo number_format($plan_cut+$colar_excess_per,0);
											$color_total_collar_tipping+=$plan_cut+$colar_excess_per;
											$color_total_collar_tipping_order_qnty+=$plan_cut;
											$grand_total_collar_tipping+=$plan_cut+$colar_excess_per;
											$grand_total_collar_tipping_order_qnty+=$plan_cut;
											?>
										</td>
										<?
									}
									?>
									<td align="center"><? echo number_format($color_total_collar_tipping,0); ?></td>
									<td align="center"><? echo number_format((($color_total_collar_tipping-$color_total_collar_tipping_order_qnty)/$color_total_collar_tipping_order_qnty)*100,2); ?></td>
								</tr>
								<?
							}
							?>
							<tr>
								<td>Size Total</td>
								<?
								foreach($nameArray_item_size  as $result_size)
								{
								?>
								<td style="border:1px solid black;  text-align:center"><? $colar_excess_pers=($size_tatal[$result_size[csf('size_number_id')]]*$colar_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]+$colar_excess_pers,0); ?></td>
								<?
								}
								?>
								<td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_collar_tipping,0); ?></td>
								<td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_collar_tipping-$grand_total_collar_tipping_order_qnty)/$grand_total_collar_tipping_order_qnty)*100,2); ?></td>
							</tr>
						</table>
					</td>
					<td width="2%">
					</td>
					<?
				}
				?>
				<?
				$nameArray_item_size=sql_select( "select min(c.id) as id, b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no ='$txt_booking_no'  and a.body_part_id=214  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by b.item_size,c.size_number_id order by id");
				if(count($nameArray_item_size)>0)
				{
					?>
					<td width="49%">
						<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
							<tr>
								<td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b>Cuff Tipping -  Colour Size Brakedown in Pcs</b></td>
							</tr>
							<tr>
								<td width="70">Size</td>
								<?
								foreach($nameArray_item_size  as $result_size)
								{
								?>
								<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
								<?
								}
								?>
								<td rowspan="2" align="center"><strong>Total</strong></td>
								<td width="60" rowspan="2" align="center"><strong>Extra %</strong></td>
							</tr>
							<tr>
							<td>Cuff Size</td>
							<?
							foreach($nameArray_item_size  as $result_item_size)
							{
							?>
							<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
							<?
							}
							?>
							<?
							$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no ='$txt_booking_no' and a.body_part_id=214  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and  d.status_active=1 and d.is_deleted=0 group by c.color_number_id ,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id
							");
							foreach($color_wise_wo_sql as $color_wise_wo_result)
							{
								$color_total_cuff_tipping=0;
								$color_total_cuff_tipping_order_qnty=0;
								$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
								$constrast_color_arr=array();
								if($color_wise_wo_result[csf("color_size_sensitive")]==3)
								{
									$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
									for($i=0;$i<count($constrast_color);$i++)
									{
										$constrast_color2=explode('_',$constrast_color[$i]);
										$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
									}
								}
								?>
								<tr>
									<td>
									<?
									if($color_wise_wo_result[csf("color_size_sensitive")]==3)
									{
									echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]);
									$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
									}
									else
									{
									echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
									$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
									}
									?>
									</td>
									<?
									foreach($nameArray_item_size  as $result_size)
									{
										$sql_excess_cuffTip="select a.po_break_down_id,a.gmts_color_id,a.size_number_id,a.excess_per,b.body_part_id FROM wo_booking_colar_culff_dtls a,wo_pre_cost_fabric_cost_dtls b where
									 a.booking_no ='$txt_booking_no' and a.pre_cost_fabric_cost_dtls_id=b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  a.size_number_id =".$result_size[csf('size_number_id')]." and a.gmts_color_id =".$color_wise_wo_result[csf('color_number_id')]." and b.body_part_id=214 and a.status_active=1 ";
									$resultData_cuff_tip=sql_select($sql_excess_cuffTip);
									list($cuffTip_excess_percent)=$resultData_cuff_tip;
									$cuff_excess_percent=$cuffTip_excess_percent[csf('excess_per')];

									?>
									<td align="center" style="border:1px solid black">
									<?
									$cuff_tipping_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active!=0 and is_deleted =0");
									list($plan_cut_qnty)=$cuff_tipping_wise_wo_sql_qnty;
									$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
									$cuff_tipping_excess_per=(($plan_cut*2)*$cuff_excess_percent)/100;
									echo number_format($plan_cut*2+$cuff_tipping_excess_per,0);
									$color_total_cuff_tipping+=$plan_cut*2+$cuff_tipping_excess_per;
									$color_total_cuff_tipping_order_qnty+=$plan_cut*2;
									$grand_total_cuff_tipping+=$plan_cut*2+$cuff_excess_per;
									$grand_total_cuff_tipping_order_qnty+=$plan_cut*2;
									?>
									</td>
									<?
									}
									?>
									<td align="center"><? echo number_format($color_total_cuff_tipping,0); ?></td>
									<td align="center" title="<? echo $color_total_cuff_tipping."**".$color_total_cuff_tipping_order_qnty; ?>"><? echo number_format((($color_total_cuff_tipping-$color_total_cuff_tipping_order_qnty)/$color_total_cuff_tipping_order_qnty)*100,2); ?></td>
								</tr>
								<?
							}
							?>
							<tr>
								<td>Size Total</td>
								<?
								foreach($nameArray_item_size  as $result_size)
								{
								?>
								<td style="border:1px solid black;  text-align:center"><? $cuff_excess_pers=(($size_tatal[$result_size[csf('size_number_id')]]*2)*$cuff_excess_percent)/100; echo number_format($size_tatal[$result_size[csf('size_number_id')]]*2+$cuff_excess_pers,0); ?></td>
								<?
								}
								?>
								<td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_cuff_tipping,0); ?></td>
								<td align="center" style="border:1px solid black"> <? echo number_format((($grand_total_cuff_tipping-$grand_total_cuff_tipping_order_qnty)/$grand_total_cuff_tipping_order_qnty)*100,2); ?></td>
							</tr>
						</table>
					</td>
					<?
				}
				?>
			</tr>
		</table>
		<br/>
		<table  width="100%"  border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width="49%" style="border:solid; border-color:#000; border-width:thin" valign="top">
					<table  width="100%"  border="0" cellpadding="0" cellspacing="0">
						<thead>
							<tr>
								<th width="3%"></th><th width="97%" align="left"><u>Special Instruction</u></th>
							</tr>
						</thead>
						<tbody>
							<?
							$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no='$txt_booking_no'");
							if ( count($data_array)>0)
							{
								$i=0;
								foreach( $data_array as $row )
								{
									$i++;
									?>
									<tr id="settr_1" valign="top">
										<td style="vertical-align:top">
											<? echo $i;?>
										</td>
										<td>
											<strong style="font-size:20px"> <? echo $row[csf('terms')]; ?></strong>
										</td>
									</tr>
									<?
								}
							}
							?>
						</tbody>
					</table>
				</td>
					<td width="2%">
					</td>
					<td width="49%" valign="top">
						<table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
							<tr align="center">
								<td><b>Approved Instructions</b></td>
							</tr>
							<tr>
								<td>
								<?  echo $nameArray_approved_comments_row[csf('comments')];  ?>
								</td>
							</tr>
						</table>
						<br />
					</td>
			</tr>
		</table>
		<br>
	<?
	//------------------------------ Query for TNA start-----------------------------------
	$po_id_all=str_replace("'","",$txt_order_no_id);
	$po_num_arr=return_library_array("select id,po_number from wo_po_break_down where id in($po_id_all)",'id','po_number');
	$tna_start_sql=sql_select( "select id,po_number_id,
	(case when task_number=31 then task_start_date else null end) as fab_booking_start_date,
	(case when task_number=31 then task_finish_date else null end) as fab_booking_end_date,
	(case when task_number=60 then task_start_date else null end) as knitting_start_date,
	(case when task_number=60 then task_finish_date else null end) as knitting_end_date,
	(case when task_number=61 then task_start_date else null end) as dying_start_date,
	(case when task_number=61 then task_finish_date else null end) as dying_end_date,
	(case when task_number=73 then task_start_date else null end) as finishing_start_date,
	(case when task_number=73 then task_finish_date else null end) as finishing_end_date,
	(case when task_number=84 then task_start_date else null end) as cutting_start_date,
	(case when task_number=84 then task_finish_date else null end) as cutting_end_date,
	(case when task_number=86 then task_start_date else null end) as sewing_start_date,
	(case when task_number=86 then task_finish_date else null end) as sewing_end_date,
	(case when task_number=110 then task_start_date else null end) as exfact_start_date,
	(case when task_number=110 then task_finish_date else null end) as exfact_end_date,
	(case when task_number=47 then task_start_date else null end) as yarn_rec_start_date,
	(case when task_number=47 then task_finish_date else null end) as yarn_rec_end_date
	from tna_process_mst
	where status_active=1 and po_number_id in($po_id_all)");
	$tna_fab_start=$tna_knit_start=$tna_dyeing_start=$tna_fin_start=$tna_cut_start=$tna_sewin_start=$tna_exfact_start="";
	$tna_date_task_arr=array();
	foreach($tna_start_sql as $row)
	{
		if($row[csf("fab_booking_start_date")]!="" && $row[csf("fab_booking_start_date")]!="0000-00-00")
		{
			if($tna_fab_start=="")
			{
				$tna_fab_start=$row[csf("fab_booking_start_date")];
			}
		}
		if($row[csf("knitting_start_date")]!="" && $row[csf("knitting_start_date")]!="0000-00-00")
		{
			$tna_date_task_arr[$row[csf("po_number_id")]]['knitting_start_date']=$row[csf("knitting_start_date")];
			$tna_date_task_arr[$row[csf("po_number_id")]]['knitting_end_date']=$row[csf("knitting_end_date")];
		}
		if($row[csf("dying_start_date")]!="" && $row[csf("dying_start_date")]!="0000-00-00")
		{
			$tna_date_task_arr[$row[csf("po_number_id")]]['dying_start_date']=$row[csf("dying_start_date")];
			$tna_date_task_arr[$row[csf("po_number_id")]]['dying_end_date']=$row[csf("dying_end_date")];
		}
		if($row[csf("finishing_start_date")]!="" && $row[csf("finishing_start_date")]!="0000-00-00")
		{
			$tna_date_task_arr[$row[csf("po_number_id")]]['finishing_start_date']=$row[csf("finishing_start_date")];
			$tna_date_task_arr[$row[csf("po_number_id")]]['finishing_end_date']=$row[csf("finishing_end_date")];
		}
		if($row[csf("cutting_start_date")]!="" && $row[csf("cutting_start_date")]!="0000-00-00")
		{
			$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_start_date']=$row[csf("cutting_start_date")];
			$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_end_date']=$row[csf("cutting_end_date")];
		}
		if($row[csf("sewing_start_date")]!="" && $row[csf("sewing_start_date")]!="0000-00-00")
		{
			$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_start_date']=$row[csf("sewing_start_date")];
			$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_end_date']=$row[csf("sewing_end_date")];
		}
		if($row[csf("exfact_start_date")]!="" && $row[csf("exfact_start_date")]!="0000-00-00")
		{
			$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_start_date']=$row[csf("exfact_start_date")];
			$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_end_date']=$row[csf("exfact_end_date")];
		}
		if($row[csf("yarn_rec_start_date")]!="" && $row[csf("yarn_rec_start_date")]!="0000-00-00")
		{
			$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_start_date']=$row[csf("yarn_rec_start_date")];
			$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_end_date']=$row[csf("yarn_rec_end_date")];
		}
	}
	//------------------------------ Query for TNA end-----------------------------------
	?>
	<fieldset id="div_size_color_matrix" style="max-width:1000; display:none">
		<legend>TNA Information</legend>
		<table width="100%" style="border:1px solid black;font-size:12px" border="1" cellpadding="2" cellspacing="0" rules="all">
			<tr>
				<td rowspan="2" align="center" valign="top">SL</td>
				<td width="180" rowspan="2"  align="center" valign="top"><b>Order No</b></td>
				<td colspan="2" align="center" valign="top"><b>Yarn Receive</b></td>
				<td colspan="2" align="center" valign="top"><b>Knitting</b></td>
				<td colspan="2" align="center" valign="top"><b>Dyeing</b></td>
				<td colspan="2" align="center" valign="top"><b>Finishing Fabric</b></td>
				<td colspan="2" align="center" valign="top"><b>Cutting </b></td>
				<td colspan="2" align="center" valign="top"><b>Sewing </b></td>
				<td colspan="2"  align="center" valign="top"><b>Ex-factory </b></td>
			</tr>
			<tr>
				<td width="85" align="center" valign="top"><b>Start Date</b></td>
				<td width="85" align="center" valign="top"><b>End Date</b></td>
				<td width="85" align="center" valign="top"><b>Start Date</b></td>
				<td width="85" align="center" valign="top"><b>End Date</b></td>
				<td width="85" align="center" valign="top"><b>Start Date</b></td>
				<td width="85" align="center" valign="top"><b>End Date</b></td>
				<td width="85" align="center" valign="top"><b>Start Date</b></td>
				<td width="85" align="center" valign="top"><b>End Date</b></td>
				<td width="85" align="center" valign="top"><b>Start Date</b></td>
				<td width="85" align="center" valign="top"><b>End Date</b></td>
				<td width="85" align="center" valign="top"><b>Start Date</b></td>
				<td width="85" align="center" valign="top"><b>End Date</b></td>
				<td width="85" align="center" valign="top"><b>Start Date</b></td>
				<td width="85" align="center" valign="top"><b>End Date</b></td>
			</tr>
				<?
				$i=1;
				foreach($tna_date_task_arr as $order_id=>$row)
				{
					?>
					<tr>
					<td><? echo $i; ?></td>
					<td><? echo $po_num_arr[$order_id]; ?></td>
					<td align="center"><? echo change_date_format($row['yarn_rec_start_date']); ?></td>
					<td  align="center"><? echo change_date_format($row['yarn_rec_end_date']); ?></td>
					<td align="center"><? echo change_date_format($row['knitting_start_date']); ?></td>
					<td  align="center"><? echo change_date_format($row['knitting_end_date']); ?></td>
					<td align="center"><? echo change_date_format($row['dying_start_date']); ?></td>
					<td align="center"><? echo change_date_format($row['dying_end_date']); ?></td>
					<td align="center"><? echo change_date_format($row['finishing_start_date']); ?></td>
					<td align="center"><? echo change_date_format($row['finishing_end_date']); ?></td>
					<td align="center"><? echo change_date_format($row['cutting_start_date']); ?></td>
					<td align="center"><? echo change_date_format($row['cutting_end_date']); ?></td>
					<td align="center"><? echo change_date_format($row['sewing_start_date']); ?></td>
					<td align="center"><? echo change_date_format($row['sewing_end_date']); ?></td>
					<td align="center"><? echo change_date_format($row['exfact_start_date']); ?></td>
					<td align="center"><? echo change_date_format($row['exfact_end_date']); ?></td>
					</tr>
					<?
					$i++;
				}
				?>
		</table>
	</fieldset>
	<?
	}// fabric Source End
	?>
	<?
	//echo signature_table(1, $cbo_company_name, "1330px");
	echo signature_table(121, $cbo_company_name, "1330px", 1);
	echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
	?>
	</div>
	<?
}

if($action=="show_fabric_booking_report_print23")//Print B23=>13-03-2022(md mamun ahmed sagor)
{
	extract($_REQUEST);
	$data = explode('**', $data);

	$txt_booking_no="'".str_replace("'","",$data[0])."'";
	$cbo_company_name=str_replace("'","",$data[1]);
	$txt_order_no_id=str_replace("'","",$data[2]);
	$cbo_fabric_natu=str_replace("'","",$data[3]);
	$cbo_fabric_source=str_replace("'","",$data[4]);
	$revised_no=str_replace("'","",$data[5]);
	//$txt_job_no="'".implode(explode(",", $data[6]), "','")."'";
	$txt_job_no=str_replace("'","",$data[6]);

	$show_yarn_rate=str_replace("'","",$data[7]);
	$report_title=str_replace("'","",$data[8]);
	$path=str_replace("'","",$data[9]);
	$id_approved_id=str_replace("'","",$data[10]);
	
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1 and master_tble_id='$cbo_company_name'",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_name_arr=return_library_array( "select id, brand_name from lib_buyer_brand ",'id','brand_name');
	//$user_name_arr=return_library_array( "select id, user_full_name from user_passwd ",'id','user_full_name');
	$user_name_arr=return_library_array( "select id, user_name from user_passwd ",'id','user_name');
	$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team",'id','team_leader_name');
 
	//$location_name_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	//$po_qnty_tot1=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	//wo_pre_cost_fabric_cost_dtls
	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	?>
	<style type="text/css">
		@media print {
		    .pagebreak { page-break-before: always; } /* page-break-after works, as well */
		}
	</style>
	<div style="width:1330px" align="center">
    <?php
    	$lip_yarn_count=return_library_array( "select id,fabric_composition_id from lib_yarn_count_determina_mst where  status_active=1", "id", "fabric_composition_id");
		$fabric_composition=return_library_array( "select id,fabric_composition_name from lib_fabric_composition where  status_active=1", "id", "fabric_composition_name");
		
		$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7");
		list($nameArray_approved_row) = $nameArray_approved;
		
		$nameArray_approved_date = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
		//echo "select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'";
		//list($nameArray_approved_date) = $nameArray_approved_date;
		foreach($nameArray_approved_date  as $row)
		{
		$nameArray_approved_date_chk=strtotime($row[csf('approved_date')]);
		}
		
		$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
		list($nameArray_approved_comments_row) = $nameArray_approved_comments;

		$max_approve_date_data = sql_select("select min(b.approved_date) as approved_date,max(b.approved_date) as last_approve_date,max(b.un_approved_date) as un_approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7");
		$first_approve_date='';
		$last_approve_date='';
		$un_approved_date='';
		if(count($max_approve_date_data))
		{
			$last_approve_date=$max_approve_date_data[0][csf('last_approve_date')];
			$first_approve_date=$max_approve_date_data[0][csf('approved_date')];
			$un_approved_date=$max_approve_date_data[0][csf('un_approved_date')];
		}
		
		if($txt_job_no!="") $location=return_field_value( "location_name", "wo_po_details_master","job_no='$txt_job_no'"); else $location="";
		$sql_loc=sql_select("select id,location_name,address from lib_location where company_id=$cbo_company_name");
		foreach($sql_loc as $row)
		{
			$location_name_arr[$row[csf('id')]]= $row[csf('location_name')];
			$location_address_arr[$row[csf('id')]]= $row[csf('address')];
		}		
		$yes_no_sql=sql_select("select job_no,cons_process from  wo_pre_cost_fab_conv_cost_dtls where job_no='$txt_job_no'  and status_active=1 and is_deleted=0  order by id");
		
		$peach=''; $brush=''; $fab_wash='';

		$emb_print=sql_select("select id, job_no, emb_name, emb_type from wo_pre_cost_embe_cost_dtls where  job_no='$txt_job_no' and status_active=1 and is_deleted=0 and cons_dzn_gmts>0 and emb_name in (1,2,3) order by id");
		
		$emb_print_data=array();
		$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type,99=>$blank_array);
		
		foreach ($emb_print as $row) 
		{
			$emb_print_data[$row[csf('job_no')]][$row[csf('emb_name')]].=$type_array[$row[csf("emb_name")]][$row[csf('emb_type')]].",";
		}
		
		// echo "<pre>";
		// print_r();

		$nameArray=sql_select( "select a.booking_no, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.po_break_down_id, a.colar_excess_percent, a.cuff_excess_percent, a.delivery_date, a.is_apply_last_update, a.fabric_source, a.inserted_by,a.rmg_process_breakdown, a.insert_date, a.update_date, a.tagged_booking_no, a.uom, a.pay_mode, a.booking_percent, b.job_no, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.total_set_qnty, (b.job_quantity*b.total_set_qnty) as jobqtypcs, b.style_description, b.season_buyer_wise as season, b.product_dept, b.product_code, b.pro_sub_dep, b.dealing_marchant,b.factory_marchant, b.order_repeat_no, b.repeat_job_no, a.fabric_composition, a.remarks, a.sustainability_standard, b.brand_id, a.quality_level, a.fab_material, a.requisition_no, b.qlty_label, b.packing, b.job_no, a.proceed_knitting, a.proceed_dyeing,b.team_leader from wo_booking_mst a, wo_po_details_master b where a.job_no=b.job_no and a.booking_no=$txt_booking_no");


		
		$po_id_all=$nameArray[0][csf('po_break_down_id')];
		$job_no_str=$nameArray[0][csf('job_no')];
		$booking_uom=$nameArray[0][csf('uom')];
		$bookingup_date=$nameArray[0][csf('update_date')];
		$bookingins_date=$nameArray[0][csf('insert_date')];
		$delivery_date=$nameArray[0][csf('delivery_date')];
		$requisition_no=$nameArray[0][csf('requisition_no')];
		$jobqtypcs=$nameArray[0][csf('jobqtypcs')];
		$inserted_by2=$user_name_arr[$nameArray[0][csf('inserted_by')]];

		$service_knitting_dyeing=sql_select("SELECT booking_no_prefix_num, supplier_id, entry_form from wo_booking_mst  where booking_type=3 and entry_form in (534, 535) and status_active=1 and is_deleted=0 and item_category=12 and tagged_booking_no=$txt_booking_no and process in (1,31)");
		foreach($service_knitting_dyeing as $row){
			$service_working_company[$row[csf('entry_form')]]['booking_no'][$row[csf('booking_no_prefix_num')]]=$row[csf('booking_no_prefix_num')];
			$service_working_company[$row[csf('entry_form')]]['working_company'][$row[csf('supplier_id')]]=$supplier_name_arr[$row[csf('supplier_id')]];
		}
		
		$job_yes_no=sql_select("select id, job_id,job_no, gmts_item_id, set_item_ratio, smv_pcs, smv_set, smv_pcs_precost, smv_set_precost, complexity, embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, ws_id, aop, aopseq,bush,bushseq,peach,peachseq,yd,ydseq from wo_po_details_mas_set_details where job_no='$job_no_str'");

	

		 $cancel_po_arr=return_library_array( "select po_number,po_number from wo_po_break_down where job_no_mst='$job_no_str' and status_active=3", "po_number", "po_number");
	

		$po_shipment_date=sql_select("select  MIN(pub_shipment_date) as min_shipment_date,max(pub_shipment_date) as max_shipment_date from wo_po_break_down where id in(".$po_id_all.") order by shipment_date asc ");
         $min_shipment_date='';
         $max_shipment_date='';
         foreach ($po_shipment_date as $row) {
         	 $min_shipment_date=$row[csf('min_shipment_date')];
         	 $max_shipment_date=$row[csf('max_shipment_date')];
         	 break;
         }

        $po_running_cancel= sql_select("select case when status_active=1 then  PO_NUMBER end as running_po, case when status_active>1 then po_number end as cancel_po,po_quantity from wo_po_break_down  where id in(".$po_id_all.") order by shipment_date asc ");
		
        $running_po='';
        $cancel_po='';
        $running_po_qnty=0;
        foreach ($po_running_cancel as $row) {
        	if(!empty($row[csf('running_po')]))
        	{
        		if(!empty($running_po))
        		{
        			$running_po.=",".$row[csf('running_po')];
        		}
        		else{
        			$running_po.=$row[csf('running_po')];
        		}
        		$running_po_qnty+=$row[csf('po_quantity')];
        	}
        	if(!empty($row[csf('cancel_po')]))
        	{
        		if(!empty($cancel_po))
        		{
        			$cancel_po.=",".$row[csf('cancel_po')];
        		}
        		else{
        			$cancel_po.=$row[csf('cancel_po')];
        		}
        	}
        }
        $stype_color_res=sql_select("select  stripe_type from wo_pre_stripe_color where job_no='$txt_job_no' and status_active=1 and is_deleted=0 group by stripe_type");
        $stype_color='';
        foreach ($stype_color_res as $val) {
        	if(!empty($stype_color))
        	{
        		$stype_color.=", ".$stripe_type_arr[$val[csf('stripe_type')]];
        	}
        	else
        	{
        		$stype_color=$stripe_type_arr[$val[csf('stripe_type')]];
        	}
        	
        }
        $yd_aop_sql=sql_select("select id, job_no,  color_type_id from wo_pre_cost_fabric_cost_dtls where job_no='$txt_job_no' and status_active=1 and is_deleted=0 order by id asc");

        $yd=''; $aop='';
		foreach ($yes_no_sql as $row) {
			
			if (strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'peach') !== false)
        	{
			    if(!empty($peach))
			    {
			    	$peach.=",".$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			    else{
			    	$peach.=$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			}
			if (strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'brush') !== false || strtolower($conversion_cost_head_array[$row[csf('cons_process')]])==strtolower('Brushing at Main Fabric Booking') || strtolower($conversion_cost_head_array[$row[csf('cons_process')]])==strtolower('Brushing at Main Fabric Booking') || strtolower($conversion_cost_head_array[$row[csf('cons_process')]])==strtolower('Brush [With Finish]') || strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'brushing') !== false)
        	{
			    if(!empty($brush))
			    {
			    	$brush.=",".$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			    else{
			    	$brush.=$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			}
			if (strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'wash') !== false || strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'washing') !== false)
        	{
			    if(!empty($fab_wash))
			    {
			    	$fab_wash.=",".$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			    else{
			    	$fab_wash.=$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			}
			if (strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'y/d') !== false || strtolower($conversion_cost_head_array[$row[csf('cons_process')]])==strtolower('YD at Main Fabric Booking') || strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'yarn dyeing') !== false)
        	{
			    if(!empty($yd))
			    {
			    	$yd.=",".$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			    else{
			    	$yd.=$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			}
			if (strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'aop') !== false || strtolower($conversion_cost_head_array[$row[csf('cons_process')]])==strtolower('All Over Printing'))
        	{
			    if(!empty($aop))
			    {
			    	$aop.=",".$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			    else{
			    	$aop.=$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			}
		}
  		ob_start();     
		?>	
											<!--    Header Company Information         -->
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black; font-family:Arial Narrow;" >
            <tr>
                <td width="200" style="font-size:28px"><img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' /></td>
                <td width="1250">
                    <table width="100%" cellpadding="0" cellspacing="0"  style="position: relative;">
                        <tr>
                            <td align="center" style="font-size:28px;"> <?php echo $company_library[$cbo_company_name]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:18px;position: relative;"><?=$location_address_arr[$location]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:24px">
                            	<span style="float:center;"><b><strong> <font style="color:black">Main Fabric Booking </font></strong></b></span> 
                            </td>
                        </tr>                        
						<tr>
                            <td align="center" style="font-size:20px">
							<?
							//$booking_no=str_replace("'",$txt_booking_no);
							if(str_replace("'","",$id_approved_id) ==1){ ?>
                            <span style="font-size:20px; float:center;"><strong> <font style="color:green"> <? echo "[Approved]"; ?> </font></strong></span> 
                               <? }else{ ?>
								<span style="font-size:20px; float:center;"><strong> <font style="color:red"><? echo "[Not Approved]"; ?> </font></strong></span> 
							   <? } ?>
							  
                            </td>
                            
							
                        </tr>
                        <tr>
                         <!-- <td align="center" style="font-size:24px">
                            	<span style="float:center;padding:2%;"><strong style="background-color:yellow;font-size: 30px;"><? echo str_replace("'","",$txt_booking_no);?></strong></span> 
                            </td> -->
							<td align="right"><strong style="background-color:yellow;font-size: 30px;margin-right:10%;"><?=str_replace("'","",$txt_booking_no);;?></strong></td>
                        </tr>
                        
						
                    </table>
					
                </td>
                <td width="200">
                	<table style="border:1px solid black; font-family:Arial Narrow;" width="100%">
                		<tr>
                			<td><b>Min. Ship Date:</b></td>
                			<td><b><?php echo  date('d-m-Y',strtotime($min_shipment_date));?></b></td>
                		</tr>
                		<tr>
                			<td><b>Max. Ship Date:</b></td>
                			<td><b><?php echo date('d-m-Y',strtotime($max_shipment_date));?></b></td>
                		</tr>
                	</table>
                	<br>
                	<table style="border:1px solid black; font-family:Arial Narrow;font-size: 10px;" width="100%">
                		<tr>
                			<td>Printing Date :</td>
                			<td><?php echo  date('d-m-Y');?></td>
                		</tr>
                		<tr>
                			<td>Printing Time:</td>
                			<td><?php echo  date('h:i:sa');?></td>
                		</tr>
                		<tr>
                			<td>User Name:</td>
                			<td><?php echo $user_name_arr[$user_id];?></td>
                		</tr>
                		<tr>
                			<?php 
                				function get_client_ip() {
								    $ipaddress = '';
								    if (getenv('HTTP_CLIENT_IP'))
								        $ipaddress = getenv('HTTP_CLIENT_IP');
								    else if(getenv('HTTP_X_FORWARDED_FOR'))
								        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
								    else if(getenv('HTTP_X_FORWARDED'))
								        $ipaddress = getenv('HTTP_X_FORWARDED');
								    else if(getenv('HTTP_FORWARDED_FOR'))
								        $ipaddress = getenv('HTTP_FORWARDED_FOR');
								    else if(getenv('HTTP_FORWARDED'))
								       $ipaddress = getenv('HTTP_FORWARDED');
								    else if(getenv('REMOTE_ADDR'))
								        $ipaddress = getenv('REMOTE_ADDR');
								    else
								        $ipaddress = 'UNKNOWN';
								    return $ipaddress;
								}

                			 ?>
                			<td>IP Address:</td>
                			<td><?php if(empty($user_ip)){echo get_client_ip();} echo $user_ip;?></td>
                		</tr>
                	</table>
                </td>
            </tr>
        </table>
		<?
        $job_no=trim($txt_job_no,"'"); $total_set_qnty=0; $colar_excess_percent=0; $cuff_excess_percent=0; $rmg_process_breakdown=0; $booking_percent=0; $booking_po_id='';
		if($db_type==0)
        {
            $date_dif_cond="DATEDIFF(pub_shipment_date,po_received_date)";
            $group_concat_all="group_concat(grouping) as grouping, group_concat(file_no) as file_no";
        }
        else
        {
            $date_dif_cond="(pub_shipment_date-po_received_date)";
            $group_concat_all=" listagg(cast(grouping as varchar2(4000)),',') within group (order by grouping) as grouping,
                                listagg(cast(file_no as varchar2(4000)),',') within group (order by file_no) as file_no  ";
        }
        $po_number_arr=array(); $po_ship_date_arr=array(); $shipment_date=""; $po_no=""; $po_received_date=""; $shiping_status="";
        $po_sql=sql_select("select id, po_number, pub_shipment_date, MIN(pub_shipment_date) as mpub_shipment_date, MIN(po_received_date) as po_received_date, MIN(insert_date) as insert_date, plan_cut, po_quantity, shiping_status, $date_dif_cond as date_diff,min(factory_received_date) as factory_received_date, $group_concat_all,status_active from wo_po_break_down where id in(".$po_id_all.") group by id, po_number, pub_shipment_date, plan_cut, po_quantity, shiping_status, po_received_date,status_active ");
      
		
        $to_ship=0; $fp_ship=0; $f_ship=0;

        foreach($po_sql as $row)
        {
            $po_qnty_tot+=$row[csf('plan_cut')];
            $po_qnty_tot1+=$row[csf('po_quantity')];
            $po_number_arr[$row[csf('id')]]=$row[csf('po_number')];
            $po_ship_date_arr[$row[csf('id')]]=$row[csf('pub_shipment_date')];
            $po_num_arr[$row[csf('id')]]=$row[csf('po_number')];
            $po_no.=$row[csf('po_number')].", ";
            $shipment_date.=change_date_format($row[csf('mpub_shipment_date')],'dd-mm-yyyy','-').", ";
            $lead_time.=$row[csf('date_diff')].",";
            $po_received_date=change_date_format($row[csf('po_received_date')],'dd-mm-yyyy','-');
            $factory_received_date=change_date_format($row[csf('factory_received_date')],'dd-mm-yyyy','-');
            $grouping.=$row[csf('grouping')].",";
            $file_no.=$row[csf('file_no')].",";
			if($row[csf('status_active')]==3){
				$cancel_po_no[$row[csf('po_number')]]=$row[csf('po_number')];
			}

			
			$daysInHand.=(datediff('d',date('d-m-Y',time()),$row[csf('mpub_shipment_date')])-1).",";
			
			if($bookingup_date=="" || $bookingup_date=="0000-00-00 00:00:00")
			{
				$booking_date=$bookingins_date;
			}
			$WOPreparedAfter.=(datediff('d',$row[csf('insert_date')],$booking_date)-1).",";

			if($row[csf('shiping_status')]==1) {
				$shiping_status.= "FP".",";
				$to_ship++;
				$fp_ship++;
			}
			else if($row[csf('shiping_status')]==2){
				$shiping_status.= "PD".",";
				$to_ship++;
			} 
			else if($row[csf('shiping_status')]==3){
				$shiping_status.= "FS".",";
				$to_ship++;
				$f_ship++;
			} 
        }

        if($to_ship==$f_ship) $shiping_status= "<b style='color:green'>Full shipped</b>";
        else if($to_ship==$fp_ship) $shiping_status= "<b style='color:red'>Full Pending</b>";
        else $shiping_status= "<b style='color:red'>Partial Delivery</b>";
		
		$po_no=implode(",",array_filter(array_unique(explode(",",$po_no))));
		$shipment_date=implode(",",array_filter(array_unique(explode(",",$shipment_date))));
		$lead_time=implode(",",array_filter(array_unique(explode(",",$lead_time))));
		$po_received_date=implode(",",array_filter(array_unique(explode(",",$po_received_date))));
		$factory_received_date=implode(",",array_filter(array_unique(explode(",",$factory_received_date))));
		$grouping=implode(",",array_filter(array_unique(explode(",",$grouping))));
		$file_no=implode(",",array_filter(array_unique(explode(",",$file_no))));
		
		$daysInHand=implode(",",array_filter(array_unique(explode(",",$daysInHand))));
		$WOPreparedAfter=implode(",",array_filter(array_unique(explode(",",$WOPreparedAfter))));
		$shiping_status=implode(",",array_filter(array_unique(explode(",",$shiping_status))));
		
        foreach ($nameArray as $result)
        {
            $total_set_qnty=$result[csf('total_set_qnty')];
            $colar_excess_percent=$result[csf('colar_excess_percent')];
            $cuff_excess_percent=$result[csf('cuff_excess_percent')];
            $rmg_process_breakdown=$result[csf('rmg_process_breakdown')];
            
            $booking_percent=$result[csf('booking_percent')];
			$booking_po_id=$result[csf('po_break_down_id')];
			?>
			<table width="100%" class="rpt_table"  border="1" align="left" cellpadding="0"  cellspacing="0" rules="all"  style="font-size:18px; font-family:Arial Narrow;" >
				<tr>
					<td colspan="2" rowspan="6" width="210">
						<? $nameArray_imge =sql_select("SELECT image_location FROM common_photo_library where master_tble_id='$job_no' and file_type=1"); ?>
                        <div id="div_size_color_matrix" style="float:left;">
                            <fieldset id="" width="210">
                                <legend>Image </legend>
                                <table width="208">
                                    <tr>
										<?
                                        $img_counter = 0;
                                        foreach($nameArray_imge as $result_imge)
                                        {
											if($path=="") $path='../../../';
											?>
											<td><img src="<? echo $path.$result_imge[csf('image_location')]; ?>" width="200" height="200" border="2" /></td>
											<?
											$img_counter++;
                                        }
                                        ?>
                                    </tr>
                                </table>
                            </fieldset>
                        </div>
					</td>
					<td align="center" width="100" style="background-color:#808080"><b>Job No</b></td>
					<?php 
					 	$revised_no=$nameArray_approved_row[csf('approved_no')]-1;
						if($revised_no<0) $revised_no=0;
					 ?>
					 
					<td align="center" width="140" colspan="3" style="background-color:#808080"> <span style="font-size:18px"><b style="float:left;font-size:18px"><? echo trim($txt_job_no,"'");if(!empty($revised_no)){ ?>&nbsp;<span style="color: red;">/&nbsp;<? echo $revised_no; }?></span></b> </span> </td>
					<td align="center" width="100" style="background-color:#808080"><span style="font-size:18px"><b>Booking No</b></span></td>
					<td align="center" width="110" style="background-color:#808080"><span style="font-size:18px"><b><?=$result[csf('booking_no')];?></b><?="<br>(".$fabric_source[$result[csf('fabric_source')]].")" ?> </span> </td>
					<td align="center" width="100" style="background-color:#808080"><span style="font-size:18px"><b>Sample Req. No</b></span></td>
					<td align="center" width="110" colspan="2" style="background-color:#808080">&nbsp;<span style="font-size:18px" ><?php echo $requisition_no; ?></span></td>
					
					<!-- <td align="center" colspan="2" width="340"></td> -->
				</tr>
				<?php
					$order_yes_no=sql_select("Select embelishment ,embro ,wash  ,spworks ,gmtsdying , ws_id , aop , aopseq , bush ,  peach, yd    from wo_po_details_mas_set_details where job_no='$txt_job_no' order by id");			
				?>
				<tr style="text-align:center">
					<td align="center"width="100" style="font-size:16px;"><b>Style</b></td>
					<td width="110"style="font-size:16px;" >&nbsp;<? echo $result[csf('style_ref_no')]; ?></td>
					
					<td width="100" style="font-size:16px;"><b>Dept. (Prod Code)</b></td>
					<td width="140"style="font-size:16px;" >&nbsp;<? echo $product_dept[$result[csf('product_dept')]]; if($result[csf('product_code')] !=""){ echo " (".$result[csf('product_code')].")";} ?></td>
					
					<td width="100"><b>Print / Type</b></td>
					<td width="110" align="center"><?=($order_yes_no[0][csf('embelishment')]==1 || (!empty($emb_print_data[$txt_job_no][1]))) ? 'Yes' : 'No' ;?></td>
					<td width="100"><?=!empty($emb_print_data[$txt_job_no][1]) ? chop($emb_print_data[$txt_job_no][1],",") : '' ;?></td>
				
					<td width="110"><b>Booking Date</b></td>
					<td width="100"> <?
                        if($booking_date=="" || $booking_date=="0000-00-00 00:00:00")
                        {
                        }
                        $booking_date=$result[csf('insert_date')];
                        echo change_date_format($booking_date,'dd-mm-yyyy','-','');
                        ?>
                    </td>
				</tr>
				<tr style="text-align:center">
					<td width="100"><span style="font-size:18px"><b>Buyer/Agent Name</b></span></td>
					<td width="110">&nbsp;<span style="font-size:18px"><? echo $buyer_name_arr[$result[csf('buyer_name')]]; ?></span></td>
					<td width="100"><b>Sub Dep</b></td>
					<td width="140"><? if($result[csf('pro_sub_dep')] !=0){ echo " (".$pro_sub_dept_array[$result[csf('pro_sub_dep')]].")";}?></td>
					<td width="100"><b>EMB / Type</b></td>
					<td width="110" align="center"><?php echo  ($order_yes_no[0][csf('embro')]==1 || (!empty($emb_print_data[$txt_job_no][2]))) ? 'Yes' : 'No' ;?></td>
					<td width="100"><?php echo  !empty($emb_print_data[$txt_job_no][2]) ? chop($emb_print_data[$txt_job_no][2],",") : '' ;?></td>

				
					<td width="110"><b>Delivery Date</b></td>
					<td width="100"><? echo change_date_format($delivery_date); ?></td>
				</tr>
				<tr style="text-align:center">
					<td width="100"><span style="font-size:18px"><b>Garments Item</b></span></td>
					<td width="110">&nbsp;<span style="font-size:18px"> <?
                        $gmts_item_name="";
                        $gmts_item=explode(',',$result[csf('gmts_item_id')]);
                        for($g=0;$g<=count($gmts_item); $g++)
                        {
                            $gmts_item_name.= $garments_item[$gmts_item[$g]].",";
                        }
                        echo rtrim($gmts_item_name,',');
                        ?></span></td>
					<td width="100"><b>Season</b></td>
					<td width="140"><? echo $season_name_arr[$result[csf('season')]]; ?></td>

							
					<td width="100"><b>AOP</b></td>
					<td width="110" align="center"><?php echo   ($order_yes_no[0][csf('aop')]==1 || (!empty($aop))) ? 'Yes' : 'No' ;?></td>
					<td width="100"><?=$aop;?></td>
				
					<td width="110"><b>Approved Date</b></td>
					<td width="100" align="center" title="Date=<?=$nameArray_approved_date_chk;?>">	<b style="color:green"><? //date('d-m-Y',strtotime($nameArray_approved_date_chk2)); 
					if($nameArray_approved_date_chk!="") echo date('d-m-Y',$nameArray_approved_date_chk);else echo ""; ?></b></td>
				</tr>
				<tr style="text-align:center">
					<td width="100"><span style="font-size:18px"><b>Order Repeat No</b></span></td>
					<td width="110">&nbsp;<span style="font-size:18px"><?=$result[csf('order_repeat_no')];?></span></td>
					<td width="100"><b>Brand</b></td>
					<td width="140"><?php echo $brand_name_arr[$result[csf('brand_id')]]; ?></td>
					<td width="100"><b>YD</b></td>
					<td width="110" align="center"><?php echo (!empty($yd) || $order_yes_no[0][csf('yd')]==1)  ? 'Yes' : 'No' ;?></td>
					<td width="100"><?php 	echo $yd; ?></td>
					
					<td width="110"><b>Approved Status</b></td>
					<td width="100" align="center">
					<? if(str_replace("'","",$id_approved_id) ==1){ ?>
					<b style="color:green"><?="Yes"; ?></b>
					<? }else{ ?><b style="color:red"><?	echo "No";?></b><? }; ?></td>
				</tr>
				<tr style="text-align:center">
					<td width="100"><span style="font-size:18px"><b>Order Repeat Job No</b></span></td>
					<td width="110"><span style="font-size:18px"><? echo $result[csf('repeat_job_no')];?><b> </span> </td>
					<td width="100"><b>Fab. Material</b></td>
					<td width="140"><?php echo $fab_material[$result[csf('fab_material')]]; ?></td>
					<td width="100"><b>Peach</b></td>
					
					<td width="110" align="center"><?php echo  ($job_yes_no[0][csf('peach')]==1 || (!empty($peach))) ? 'Yes' : 'No' ;?></td>
					<td width="100"><?=$peach;?></td>
					<td width="110"><b>Amendment Date</b></td>
					<td width="100"><? if(!empty($un_approved_date)){echo change_date_format($un_approved_date);} ?></td>
				</tr>
				<tr style="text-align:center">
					<td width="100"><span style="font-size:18px"><b>Running PO No</b></span></td>
					<td width="350" colspan="3" style="word-break: break-all;"><p style="font-size:12px;width: 450px;word-break: break-all;" ><? echo $running_po; ?></p></td>
					<td width="100"><b>Sustainability Standard</b></td>
					<td width="110"><?php echo $sustainability_standard[$result[csf('sustainability_standard')]]; ?></td>

					<td width="100"><b>Brushing</b></td>
					<td width="110" align="center"><?php echo  ($job_yes_no[0][csf('bush')]==1 ||  (!empty($brush))) ? 'Yes' : 'No' ;?></td>
					<td width="100"><?=$brush;?></td>
					<td width="110"><b>Team Leader</b></td>
					<td width="100"><?=$team_leader_arr[$result[csf('team_leader')]];?></td>
				</tr>
				<tr style="text-align:center">
					<td width="100"><span style="font-size:18px"><b>Cancelled PO</b></span></td>
					<td width="350" colspan="3" ><p style="font-size:12px;width: 450px;word-break: break-all;"><? 	echo implode(",",$cancel_po_arr);;//$cancel_po; ?></p></td>		
					<?php $fab_material=array(1=>"Organic",2=>"BCI"); ?>
					<td width="100"><b>Order Nature</b> </td>
					<td width="110"><?php echo $fbooking_order_nature[$result[csf('quality_level')]] ?></td>
					<td width="100"><b>Fab Wash</b></td>
					<td width="110" align="center"><?php echo !empty($fab_wash)? 'Yes' : 'No'; ?></td>
					<td width="100"><?=$fab_wash?></td>
				

					<td width="110"><b>Dealing Merchandiser</b></td>
					<td width="100"><? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
				</tr>
				<tr style="text-align:center">
					<td width="100"><span style="font-size:18px"><b>GMT/ Style Description</b></span></td>
					<td width="350" colspan="3"><span style="font-size:18px"><? echo $result[csf('style_description')]; ?></span></td>
					<td width="100"><b>Quality Label</b></td>
					<td width="110"><?php echo $quality_label[$result[csf('qlty_label')]] ?></td>

					<td width="100"><b>GMT Wash</b></td>
					
					<td width="110" align="center"><?php echo  ($order_yes_no[0][csf('wash')]==1 || (!empty($emb_print_data[$txt_job_no][3]))) ? 'Yes' : 'No' ;?></td>
					<td width="100"><?php echo  !empty($emb_print_data[$txt_job_no][3]) ? chop($emb_print_data[$txt_job_no][3],",") : '' ;?></td>

					<td width="110"><b>Factory Merchandiser</b></td>
					<td width="100"><? echo $marchentrArr[$result[csf('factory_marchant')]]; ?></td>
				</tr>
				<tr style="text-align:center">
					<td width="100"><span style="font-size:18px"><b>Fabric Description</b></span></td>
					<td width="350" colspan="3"><span style="font-size:18px">
						<? 
							$sql_fab="SELECT a.lib_yarn_count_deter_id AS determin_id, a.construction
							    FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
							   WHERE a.job_id = b.job_id AND a.id = b.pre_cost_fabric_cost_dtls_id AND a.id = d.pre_cost_fabric_cost_dtls_id AND b.po_break_down_id = d.po_break_down_id AND b.color_size_table_id = d.color_size_table_id AND b.pre_cost_fabric_cost_dtls_id = d.pre_cost_fabric_cost_dtls_id AND d.booking_no = $txt_booking_no AND a.status_active = 1 AND d.status_active = 1 AND d.is_deleted = 0 and a.body_part_id in (1,20) group by a.lib_yarn_count_deter_id , a.construction";
							//echo $sql_fab;
							$res_fab=sql_select($sql_fab);
							$des='';
							foreach ($res_fab as $row) 
							{
								if(!empty($des))
								{
									$des."***";
								}
								$des.=$row[csf('construction')] . " ". $fabric_composition[$lip_yarn_count[$row[csf('determin_id')]]].",";
							}
							echo implode(",", array_unique(explode("***", $des)));
						?>
						</span></td>
					<td width="100"><b>Packing</b></td>
					<td width="110"><?php echo $packing[$result[csf('packing')]]; ?></td>
					<td width="100" style="background-color:#808080"><b>Running Order Qty</b></td>
					<?php $order_uom_res=sql_select( "select a.order_uom from wo_po_details_master a where a.status_active=1 and a.is_deleted=0  and a.job_no='$txt_job_no' ");
						$order_uom='';
						if(count($order_uom_res))
						{
							$order_uom=$unit_of_measurement[$order_uom_res[0][csf('order_uom')]];
						}
					 ?>
					<td width="210" colspan="2" align="center" style="background-color:#808080"><b> <?php echo number_format($running_po_qnty,0); ?>&nbsp;(<?=$order_uom;?>)</b></td>
					<td width="110" style="background-color:#808080"><b>Shipment Status</b></td>
					<td width="100" style="background-color:#808080"><? echo rtrim($shiping_status,','); ?></td>
				</tr>
				<tr style="text-align:center">
					<td width="100"><span style="font-size:18px"><b>Attention</b></span></td>
					<td  width="350" colspan="3"><span style="font-size:18px"><? echo $result[csf('attention')]?></span></td>
					<td width="100"><b>Proceed for Knitting</b></td>
					<td width="110">	<?php 
					if($result[csf('proceed_knitting')]==1){?>
					<b style="color:green"><? echo $yes_no[$result[csf('proceed_knitting')]]; ?></b>
					<?}else{?>
						<b style="color:red"><? echo $yes_no[$result[csf('proceed_knitting')]]; ?></b>
						<?}?></td>
					<td width="100"><b>Working Company</b></td>
					<td width="210"  align="center" colspan="2"><? echo implode(",",$service_working_company[534]['working_company'])  ?></td>
					<td width="100"><b>Work Order No</b></td>
					<td width="110"  align="center"><? echo implode(",",$service_working_company[534]['booking_no'])  ?></td>					
				</tr>
                <tr style="text-align:center">
				<td width="100"><span style="font-size:18px"><b>Remarks</b></span></td>
				<td  width="350" colspan="3"><b><?php if(!empty($result[csf('remarks')])){ ?><span style="font-size: 25px;">&#8592;</span><? echo $result[csf('remarks')]; } ?></b></td>
				<td><b>Proceed for Dyeing</b></td>
					<td>
						
					<?php 
					if($result[csf('proceed_dyeing')]==1){?>
					<b style="color:green"><? echo $yes_no[$result[csf('proceed_dyeing')]]; ?></b>
					<?}else{?>
						<b style="color:red"><? echo $yes_no[$result[csf('proceed_dyeing')]]; ?></b>
						<?}?>
					</td>
					<td><b>Working Company</b></td>
					<td width="210"  align="center" colspan="2"><? echo implode(",",$service_working_company[535]['working_company'])  ?></td>
					<td width="100"><b>Work Order No</b></td>
					<td width="110" align="center"><? echo implode(",",$service_working_company[535]['booking_no'])  ?></td>
				</tr>				
			</table>
			
			<?
		}	
			
	  	?>
		<span style="color:red; font-size:14px">PLS NOTE: BEFORE START KNITTING MUST CHECK ALL THE BELLOW INFORMATIONS, SPECIALLY DIA, GREY GSM, S/L & COUNT ETC. ANTIQUE WHITE MUST BE TEFLON FINISH TREATMENT</span>
		

		<?php
	
		$nameArray_fabric_description= sql_select("select a.body_part_id, a.lib_yarn_count_deter_id as determin_id, a.color_type_id, a.construction, a.composition, a.gsm_weight, min(a.width_dia_type) as width_dia_type, b.dia_width, b.remarks, avg(b.cons) as cons, b.process_loss_percent, avg(b.requirment) as requirment,b.po_break_down_id,  d.fabric_color_id, d.gmts_color_id, d.id as dtls_id, sum(d.fin_fab_qnty) as fin_fab_qnty, sum(d.grey_fab_qnty) as grey_fab_qnty,a.id FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and b.po_break_down_id=d.po_break_down_id and b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and d.status_active=1 and d.is_deleted=0  AND a.status_active = 1 AND a.is_deleted = 0   AND c.status_active = 1  AND c.is_deleted = 0  AND b.status_active = 1  AND b.is_deleted = 0 group by a.body_part_id,a.id, a.lib_yarn_count_deter_id, a.color_type_id, a.construction, a.composition, a.gsm_weight, b.dia_width, b.remarks,d.fabric_color_id, d.gmts_color_id, d.id,b.po_break_down_id, b.process_loss_percent order by a.id, a.body_part_id, b.dia_width");
	
	
	
		foreach ($nameArray_fabric_description as $row) {	
			$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."'  and approval_status=3 and is_deleted=0  and color_name_id=".$row[csf('fabric_color_id')]." and  po_break_down_id=".$row[csf('po_break_down_id')]."  and status_active=1 and is_deleted=0 ");
	
			$grouping_item=$row[csf('fabric_color_id')].'*'.$row[csf('body_part_id')].'*'.$row[csf('construction')].'*'.$row[csf('composition')].'*'.$row[csf('gsm_weight')].'*'.$row[csf('dia_width')].'*'.$row[csf('color_type_id')];	
			$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['gmts_color_id'] = $row[csf('gmts_color_id')];
			$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['fabric_color_id'] = $row[csf('fabric_color_id')];
			$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['lapdip_no'] = $lapdip_no;
			$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['body_part_id'] = $row[csf('body_part_id')];
			$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['fabric_des'] = $row[csf('construction')].','.$row[csf('composition')];
			$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['gsm'] = $row[csf('gsm_weight')];
			$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['fabric_dia'] = $row[csf('dia_width')].",".$fabric_typee[$row[csf('width_dia_type')]];
			$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['color_type_id'] = $row[csf('color_type_id')];
			$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['finsh_cons'] = $row[csf('cons')];
			$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['gray_cons'] = $row[csf('requirment')];
			$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['fin_fab_qnty'] += $row[csf('fin_fab_qnty')];
			$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['grey_fab_qnty'] += $row[csf('grey_fab_qnty')];
			$fabric_data_arr[$row[csf('gmts_color_id')]][$grouping_item]['process_loss_percent'] = $row[csf('process_loss_percent')];
	
		}
	
		// /*echo '<pre>';
		// print_r($fabric_data_arr); die;*/
	
		?>
		 <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" style="font-size: 18px;">
			 <tr>
				 <th>Gmts Color</th>
				 <th>Fabric Color</th>
				 <th>Lab Dip No</th>
				 <th>Body Part</th>
				 <th>Fabrication</th>
				 <th>GSM</th>
				 <th>Dia Type with </br> Fabric Dia</th>			
				 <th>Color Type</th>
				 <th>Finsh Cons.</th>
				 <th>Finish  Qty</th>
				 <th>Grey Cons.</th>
				 <th>Grey Qty</th>
				 <th>Process Loss %</th>
			 </tr>
			 <? 
			 foreach ($fabric_data_arr as $gmts_id=>$fabric_data_arr) {  
			 $i=1;     		  		
				 foreach ($fabric_data_arr as $fabric_id => $value) {
						 $fin_fab_qnty+=$value['fin_fab_qnty'];   		 	
						 $grey_fab_qnty+=$value['grey_fab_qnty'];   		 	
						  if($i==1){
						   ?>
						  <tr>
							 <td rowspan="<? echo count($fabric_data_arr) ?>"><? echo $color_library[$gmts_id] ?></td>
							 <td><? echo $color_library[$value['fabric_color_id']] ?></td>
							<td><? echo $value['lapdip_no']; ?></td>
							 <td><? echo $body_part[$value['body_part_id']] ?></td>
							 <td><? echo $value['fabric_des'] ?></td>
							 <td><? echo $value['gsm'] ?></td>
							 <td><? echo $value['fabric_dia'] ?></td>
							 <td><? echo $color_type[$value['color_type_id']] ?></td>
							 <td align="right"><? echo fn_number_format($value['finsh_cons'],4) ; ?></td>
							 <td align="right"><? echo fn_number_format($value['fin_fab_qnty'],4) ; ?></td>
							 <td align="right"><? echo fn_number_format($value['gray_cons'],4) ; ?></td>		     			
							 <td align="right"><? echo fn_number_format($value['grey_fab_qnty'],4) ; ?></td>
							 <td align="center"><? echo $value['process_loss_percent'] ?></td>
						 </tr>
						  <? } 
						  else { ?>
							  <tr>
								 <td><? echo $color_library[$value['fabric_color_id']] ?></td>
								 <td><? echo $value['lapdip_no'];?></td>
								 <td><? echo $body_part[$value['body_part_id']] ?></td>
								 <td><? echo $value['fabric_des'] ?></td>
								 <td><? echo $value['gsm'] ?></td>
								 <td><? echo $value['fabric_dia'] ?></td>
								 <td><? echo $color_type[$value['color_type_id']] ?></td> 
								 <td align="right"><? echo fn_number_format($value['finsh_cons'],4) ; ?></td>
								 <td align="right"><? echo number_format($value['fin_fab_qnty'],2) ?></td>
								 <td align="right"><? echo fn_number_format($value['gray_cons'],4) ; ?></td>			     			
								 <td align="right"><? echo number_format($value['grey_fab_qnty'],2) ?></td>
								 <td align="center"><? echo $value['process_loss_percent'] ?></td>
							 </tr>
						  <? }
						  $i++;
				 }
			 } 
			 ?>
			 <tr>
				 <th colspan="9">Total</th>
				 <th><? echo number_format($fin_fab_qnty);  ?></th>
				 <th></th>
				 <th><? echo number_format($grey_fab_qnty);  ?></th>
				 <th></th>
			 </tr>
		 </table>
		  <br/>



      	<!--  Here will be the main portion  -->
		<?
        $costing_per=""; $costing_per_qnty=0;
        $costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='$job_no'");
        if($costing_per_id==1)
        {
			$costing_per="1 Dzn";
			$costing_per_qnty=12;
        }
        if($costing_per_id==2)
        {
			$costing_per="1 Pcs";
			$costing_per_qnty=1;
        }
        if($costing_per_id==3)
        {
			$costing_per="2 Dzn";
			$costing_per_qnty=24;
        }
        if($costing_per_id==4)
        {
			$costing_per="3 Dzn";
			$costing_per_qnty=36;
        }
        if($costing_per_id==5)
        {
			$costing_per="4 Dzn";
			$costing_per_qnty=48;
        }




        $process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no='$job_no'");
		//$s_length=return_field_value( "stitch_length", "fabric_mapping","mst_id=$determin_id");;
		$s_lengthArr=return_library_array( "select mst_id, stitch_length from fabric_mapping",'mst_id','stitch_length');		
		if($cbo_fabric_source==1)
		{
			$fb_desc_sq="SELECT min(a.id) as fabric_cost_dtls_id, a.lib_yarn_count_deter_id as determin_id, a.item_number_id, a.body_part_id, a.color_type_id, a.construction, a.composition, a.gsm_weight, min(a.width_dia_type) as width_dia_type,  b.dia_width, avg(b.cons) as cons, avg(b.process_loss_percent) as process_loss_percent, avg(b.requirment) as requirment FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d WHERE a.job_id=b.job_id and a.id=b.pre_cost_fabric_cost_dtls_id  and a.id=d.pre_cost_fabric_cost_dtls_id  and b.po_break_down_id=d.po_break_down_id and b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and d.booking_no=$txt_booking_no and a.status_active=1 and d.status_active=1 and d.is_deleted=0 group by a.body_part_id, a.lib_yarn_count_deter_id, a.color_type_id, a.item_number_id, a.construction, a.composition, a.gsm_weight,  b.dia_width order by fabric_cost_dtls_id, a.body_part_id, b.dia_width";
			$nameArray_fabric_description= sql_select($fb_desc_sq);
			?>
			<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" style="font-family:Arial Narrow;font-size:18px;" >
                <tr align="center">
                    <th colspan="2" align="left">Body Part</th>
                    <?
                    foreach($nameArray_fabric_description  as $result_fabric_description)
                    {
						if( $result_fabric_description[csf('body_part_id')] == "") echo "<td  colspan='2'>&nbsp</td>";
						else echo "<td colspan='2'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";
                    }
                    ?>
                   <th colspan="2" align="center">Total</th>
                </tr>
                <tr>
                    <th width="120" align="left">Fabric Color</th>
                    <th width="120" align="left">Body Color</th>
                    <?
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
                   		echo "<th width='50'>Finish</th><th width='50' >Grey</th>";
                    }
                    ?>
					<th width='50'>Finish</th>
					<th width='50' >Grey</th>
                </tr>
                <?
                $color_wise_wo_sql = "SELECT a.item_number_id, a.body_part_id, a.color_type_id, a.construction, a.composition, a.gsm_weight, a.lib_yarn_count_deter_id, b.dia_width, b.remarks, d.fabric_color_id, d.fin_fab_qnty as fin_fab_qnty, d.grey_fab_qnty as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a join wo_pre_cos_fab_co_avg_con_dtls b on  a.id=b.pre_cost_fabric_cost_dtls_id join wo_po_color_size_breakdown c on c.id=b.color_size_table_id join wo_booking_dtls d on b.po_break_down_id=d.po_break_down_id and b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id WHERE  d.booking_no =$txt_booking_no and a.uom=12 and d.status_active=1 and  d.is_deleted=0 ";
                
                $color_wise_wo_sql_res=sql_select($color_wise_wo_sql); $fin_grey_qty_arr=array(); $fin_grey_color_qty_arr=array();
                foreach($color_wise_wo_sql_res as $row)
                {
					$fin_grey_key = $row[csf('item_number_id')].'**'.$row[csf('body_part_id')].'**'.$row[csf('color_type_id')].'**'.$row[csf('construction')].'**'.$row[csf('composition')].'**'.$row[csf('gsm_weight')].'**'.$row[csf('lib_yarn_count_deter_id')].'**'.$row[csf('dia_width')];
					$fin_grey_color_key = $row[csf('item_number_id')].'**'.$row[csf('body_part_id')].'**'.$row[csf('color_type_id')].'**'.$row[csf('construction')].'**'.$row[csf('composition')].'**'.$row[csf('gsm_weight')].'**'.$row[csf('lib_yarn_count_deter_id')].'**'.$row[csf('dia_width')].'**'.$row[csf('fabric_color_id')];
					$fin_grey_qty_arr[$fin_grey_key]['fin'] += $row[csf('fin_fab_qnty')];
					$fin_grey_qty_arr[$fin_grey_key]['grey'] += $row[csf('grey_fab_qnty')];
					$fin_grey_color_qty_arr[$fin_grey_color_key]['fin'] +=$row[csf('fin_fab_qnty')];
					$fin_grey_color_qty_arr[$fin_grey_color_key]['grey'] +=$row[csf('grey_fab_qnty')];
                }
                unset($color_wise_wo_sql_res);
                
                $gmt_color_library=array();
                $gmt_color_data=sql_select("select a.id,b.gmts_color_id, b.contrast_color_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b WHERE a.id=b.pre_cost_fabric_cost_dtls_id and a.fab_nature_id=$cbo_fabric_natu and a.fabric_source =$cbo_fabric_source and a.job_no ='$job_no' and a.status_active=1 and b.status_active=1 order by a.id");
                foreach( $gmt_color_data as $gmt_color_row)
                {
                	$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]][$gmt_color_row[csf("gmts_color_id")]]=$color_library[$gmt_color_row[csf("gmts_color_id")]];
                }
                
                $lab_dip_no_arr=array();
                $lab_dip_no_sql=sql_select("select lapdip_no, color_name_id from wo_po_lapdip_approval_info where job_no_mst='$job_no' and status_active=1 and is_deleted=0 and approval_status=3");
                foreach($lab_dip_no_sql as $row)
                {
                	$lab_dip_no_arr[$row[csf('color_name_id')]]=$row[csf('lapdip_no')];
                }
                unset($lab_dip_no_sql);
                
                $grand_total_fin_fab_qnty=0; $grand_total_grey_fab_qnty=0; $grand_totalcons_per_finish=0; $grand_totalcons_per_grey=0;
                $color_wise_wo_sql=sql_select("select fabric_color_id FROM wo_booking_dtls WHERE booking_no =$txt_booking_no and status_active=1 and is_deleted=0 group by fabric_color_id");
                foreach($color_wise_wo_sql as $color_wise_wo_result)
                {
					?>
					<tr>
                        <td width="120" align="left"><? echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]]; ?></td>
                        <td width="120"><? echo implode(",",$gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]]); ?></td>
                       
                        <?
                        $total_fin_fab_qnty=0; $total_grey_fab_qnty=0;
                        foreach($nameArray_fabric_description as $result_fabric_description)
                        {
							$color_wo_fin_qnty=0; $color_wo_grey_qnty=0;
							$fin_gray_color = $result_fabric_description[csf('item_number_id')].'**'.$result_fabric_description[csf('body_part_id')].'**'.$result_fabric_description[csf('color_type_id')].'**'.$result_fabric_description[csf('construction')].'**'.$result_fabric_description[csf('composition')].'**'.$result_fabric_description[csf('gsm_weight')].'**'.$result_fabric_description[csf('determin_id')].'**'.$result_fabric_description[csf('dia_width')].'**'.$color_wise_wo_result[csf('fabric_color_id')];
							$color_wo_fin_qnty=$fin_grey_color_qty_arr[$fin_gray_color]['fin'];
							
							$color_wo_grey_qnty=$fin_grey_color_qty_arr[$fin_gray_color]['grey'];
							?>
							<td width='50' align='center' style="font-size:18px;">
								<?
                                if($color_wo_fin_qnty!="")
                                {
                                    echo number_format($color_wo_fin_qnty,2) ;
                                    $total_fin_fab_qnty+=$color_wo_fin_qnty;
                                }
                                ?>
							</td>
							<td width='50' align='center' style="font-size:18px;">
								<?
                                if($color_wo_grey_qnty!="")
                                {
									echo number_format($color_wo_grey_qnty,2);
									$total_grey_fab_qnty+=$color_wo_grey_qnty;
                                }
                                ?>
							</td>
							
							
							<?
                        }
                        ?>
						<td width='50' align='center' style="font-size:18px;">
								<?
                                
									echo number_format($total_fin_fab_qnty,2);
									 $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;
                                
                                ?>
							</td>
							
							<td width='50' align='center' style="font-size:18px;">
								<?
                               
									echo number_format($total_grey_fab_qnty,2);
									 $grand_total_grey_fab_qnty+=$total_grey_fab_qnty;
                                
                                ?>
							</td>
                        
					</tr>
					<?
                }
                ?>
                <tr style=" font-weight:bold">
                    <th width="120" align="left">&nbsp;</th>      
					                
                    <td width="120" align="left"><strong>Total</strong></td>
                    <?
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
						$wo_fin_qnty=0; $wo_grey_qnty=0;
						$fin_key = $result_fabric_description[csf('item_number_id')].'**'.$result_fabric_description[csf('body_part_id')].'**'.$result_fabric_description[csf('color_type_id')].'**'.$result_fabric_description[csf('construction')].'**'.$result_fabric_description[csf('composition')].'**'.$result_fabric_description[csf('gsm_weight')].'**'.$result_fabric_description[csf('determin_id')].'**'.$result_fabric_description[csf('dia_width')];
						
						$wo_fin_qnty=$fin_grey_qty_arr[$fin_key]['fin'];
						$wo_grey_qnty=$fin_grey_qty_arr[$fin_key]['grey'];
						?>
						<td width='50' align='center' style="font-size:18px;"><?  echo number_format($wo_fin_qnty,2) ;?></td><td width='50' align='center' style="font-size:18px;" > <? echo number_format($wo_grey_qnty,2);?></td>
						<?
                    }
                    ?>
					<td width='50' align='center' style="font-size:18px;"><?  echo number_format($grand_total_fin_fab_qnty,2) ;?></td><td width='50' align='center' style="font-size:18px;" > <? echo number_format($grand_total_grey_fab_qnty,2);?></td>
                   
                </tr>
               
			</table>
			<?
		}
		//echo "kausar"; die;
		if($cbo_fabric_source==2)
		{
			$nameArray_fabric_description= sql_select("select min(a.id) as fabric_cost_dtls_id, a.lib_yarn_count_deter_id as determin_id, a.body_part_id, a.color_type_id, a.construction, a.composition, a.gsm_weight, min(a.width_dia_type) as width_dia_type, b.dia_width, avg(a.avg_finish_cons) as cons, avg(b.process_loss_percent) as process_loss_percent, avg(a.avg_cons) as requirment FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d WHERE a.job_id=b.job_id and a.id=b.pre_cost_fabric_cost_dtls_id and c.job_id=a.job_id and c.id=b.color_size_table_id and b.po_break_down_id=d.po_break_down_id and b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and d.status_active=1 and d.is_deleted=0 group by a.body_part_id, a.lib_yarn_count_deter_id, a.color_type_id, a.construction, a.composition, a.gsm_weight, b.dia_width order by fabric_cost_dtls_id, a.body_part_id, b.dia_width");


			?>
			<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" style="font-family:Arial Narrow;font-size:18px;" >
                <tr align="center">
                    <th colspan="2" align="left">Body Part</th>
                    <?
                    foreach($nameArray_fabric_description  as $result_fabric_description)
                    {
						if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";
						else echo "<td  colspan='3'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";
                    }
                    ?>
                    <td rowspan="10" width="50"><p>Total Fabric (<? echo $unit_of_measurement[$booking_uom];?>)</p></td>
                    <td rowspan="10" width="50"><p>Avg Rate (<? echo $unit_of_measurement[$booking_uom];?>)</p></td>
                    <td rowspan="10" width="50"><p>Amount </p></td>
                </tr>
             
                <tr>
                	<th colspan="<? echo  count($nameArray_fabric_description)*3+3; ?>" align="left" style="height:30px">&nbsp;</th>
                </tr>
                <tr>
                    <th width="120" align="left">Fabric Color</th>
                    <th width="120" align="left">Body Color</th>
                   
                    <?
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
                    	echo "<th width='50'>Fab. Qty</th><th width='50' >Rate</th><th width='50' >Amount</th>";
                    }
                    ?>
                </tr>
                <?
                $gmt_color_library=array();
                $gmt_color_data=sql_select("select a.id as fab_id,b.gmts_color_id, b.contrast_color_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_color_dtls b  WHERE a.id=b.pre_cost_fabric_cost_dtls_id and a.fab_nature_id=$cbo_fabric_natu and a.fabric_source =$cbo_fabric_source and a.job_no ='$job_no' and a.status_active=1 and b.status_active=1 order by a.id");
                foreach( $gmt_color_data as $gmt_color_row)
                {
                	$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]][$gmt_color_row[csf("gmts_color_id")]]=$color_library[$gmt_color_row[csf("gmts_color_id")]];
                }
                
                $grand_total_fin_fab_qnty=0; $grand_total_amount=0;
                
                $color_wise_wo_sql=sql_select("select fabric_color_id FROM wo_booking_dtls WHERE booking_no =$txt_booking_no and status_active=1 and is_deleted=0 group by fabric_color_id");
                foreach($color_wise_wo_sql as $color_wise_wo_result)
                {
                ?>
                <tr>
                
                <td  width="120" align="left">
                <?
                echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
                
                
                ?>
                </td>
                <td>
                <?
                echo implode(",",$gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]]);
                ?>
                </td>
               
                <?
                $total_fin_fab_qnty=0;
                $total_amount=0;
                
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
                if($db_type==0)
                {
                $color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty, avg(d.rate) as rate FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
                WHERE a.job_id=b.job_id and
                a.id=b.pre_cost_fabric_cost_dtls_id and
                c.job_id=a.job_id and
                c.id=b.color_size_table_id and
                b.po_break_down_id=d.po_break_down_id and
                b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
                d.booking_no =$txt_booking_no and
                a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
                a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
                a.construction='".$result_fabric_description[csf('construction')]."' and
                a.composition='".$result_fabric_description[csf('composition')]."' and
                a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
                a.lib_yarn_count_deter_id='".$result_fabric_description[csf('determin_id')]."' and
                b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
                d.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
                d.status_active=1 and
                d.is_deleted=0
                ");
                }
                if($db_type==2)
                {
                
                $color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
                WHERE a.job_id=b.job_id and
                a.id=b.pre_cost_fabric_cost_dtls_id and
                c.job_id=a.job_id and
                c.id=b.color_size_table_id and
                b.po_break_down_id=d.po_break_down_id and
                b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
                d.booking_no =$txt_booking_no and
                a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
                a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
                a.construction='".$result_fabric_description[csf('construction')]."' and
                a.composition='".$result_fabric_description[csf('composition')]."' and
                a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
                a.lib_yarn_count_deter_id='".$result_fabric_description[csf('determin_id')]."' and
                b.dia_width='".$result_fabric_description[csf('dia_width')]."' and

                nvl(d.fabric_color_id,0)=nvl('".$color_wise_wo_result[csf('fabric_color_id')]."',0) and
                d.status_active=1 and
                d.is_deleted=0
                ");
                }
                list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
                ?>
                <td width='50' align='right'>
                <?
                if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
                {
                echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2) ;
                $total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
                }
                ?>
                </td>
                <td width='50' align='right' >
                <?
                if($color_wise_wo_result_qnty[csf('rate')]!="")
                {
                echo number_format($color_wise_wo_result_qnty[csf('rate')],2);
                }
                ?>
                </td>
                <td width='50' align='right' >
                <?
                $amount=$color_wise_wo_result_qnty[csf('grey_fab_qnty')]*$color_wise_wo_result_qnty[csf('rate')];
                if($amount!="")
                {
                echo number_format($amount,2);
                $total_amount+=$amount;
                }
                ?>
                </td>
                <?
                }
                ?>
                <td align="right"><? echo number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
                <td align="right"><? echo number_format($total_amount/$total_fin_fab_qnty,2); $grand_total_amount+=$total_amount;?></td>
                <td align="right">
                <?
                echo number_format($total_amount,2);
                
                ?>
                </td>
                </tr>
                <?
                }
                ?>
                <tr style=" font-weight:bold">
                <!--<td  width="120" align="left">&nbsp;</td>-->
                <th  width="120" align="left">&nbsp;</th>
              
                <td  width="120" align="left"><strong>Total</strong></td>
                <?
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
                $color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
                WHERE a.job_id=b.job_id and
                a.id=b.pre_cost_fabric_cost_dtls_id and
                c.job_id=a.job_id and
                c.id=b.color_size_table_id and
                b.po_break_down_id=d.po_break_down_id and
                b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
                d.booking_no =$txt_booking_no and
                a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
                a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
                a.construction='".$result_fabric_description[csf('construction')]."' and
                a.composition='".$result_fabric_description[csf('composition')]."' and
                a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
                a.lib_yarn_count_deter_id='".$result_fabric_description[csf('determin_id')]."' and
                b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
                d.status_active=1 and
                d.is_deleted=0
                ");
                list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
                ?>
                <td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2) ;?></td>
                <td width='50' align='right' > <? //echo number_format($color_wise_wo_result_qnty['grey_fab_qnty'],2);?></td>
                <td width='50' align='right' > <? //echo number_format($color_wise_wo_result_qnty['grey_fab_qnty'],2);?></td>
                <?
                }
                ?>
                <td align="right"><? echo number_format($grand_total_fin_fab_qnty,2);?></td>
                <td align="right"><? echo number_format($grand_total_amount/$grand_total_fin_fab_qnty,2);?></td>
                <td align="right">
                <?
                echo number_format($grand_total_amount,2);
                ?>
                </td>
                </tr>
                <tr style="font-weight:bold">
                <!--<td  width="120" align="left">&nbsp;</td>-->
                <th  width="120" align="left">&nbsp;</th>
           
                <td  width="120" align="left"><strong>Consumption For <? echo $costing_per; ?></strong></td>
                <?
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
                $color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
                WHERE a.job_id=b.job_id and
                a.id=b.pre_cost_fabric_cost_dtls_id and
                c.job_id=a.job_id and
                c.id=b.color_size_table_id and
                b.po_break_down_id=d.po_break_down_id and
                b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
                d.booking_no =$txt_booking_no and
                a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
                a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
                a.construction='".$result_fabric_description[csf('construction')]."' and
                a.composition='".$result_fabric_description[csf('composition')]."' and
                a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
                a.lib_yarn_count_deter_id='".$result_fabric_description[csf('determin_id')]."' and
                b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
                d.status_active=1 and
                d.is_deleted=0
                ");
                list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
                
                ?>
                <td width='50' align='right'><?  //echo number_format(($color_wise_wo_result_qnty['fin_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4) ;?></td>
                <td width='50' align='right' > <? //echo number_format(($color_wise_wo_result_qnty['grey_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4);?></td>
                <td width='50' align='right' > <? //echo number_format(($color_wise_wo_result_qnty['grey_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4);?></td>
                <?
                }
                ?>
                <td align="right">
                <?
                $consumption_per_unit_fab=($grand_total_fin_fab_qnty/$po_qnty_tot)*($costing_per_qnty);
                echo number_format($consumption_per_unit_fab,4);
                ?>
                </td>
                <td align="right">
                <?
                $consumption_per_unit_amuont=($grand_total_amount/$po_qnty_tot)*($costing_per_qnty);
                echo number_format(($consumption_per_unit_amuont/$consumption_per_unit_fab),2);
                ?>
                </td>
                <td align="right">
                <?
                echo number_format($consumption_per_unit_amuont,2);
                ?>
                </td>
                </tr>
			</table>
			<?
		}
		?>
        <?
		if($cbo_fabric_source==1)
		{
			$lab_dip_color_arr=array();
			$lab_dip_color_sql=sql_select("select pre_cost_fabric_cost_dtls_id, gmts_color_id, contrast_color_id from wo_pre_cos_fab_co_color_dtls where job_no='$job_no'");
			foreach($lab_dip_color_sql as $row)
			{
				$lab_dip_color_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('gmts_color_id')]]=$row[csf('contrast_color_id')];
			}
			
			

			$collar_cuff_percent_arr=array(); $collar_cuff_body_arr=array(); $collar_cuff_color_arr=array(); $collar_cuff_size_arr=array(); $collar_cuff_item_size_arr=array(); $color_size_sensitive_arr=array();

			$collar_cuff_sql="select a.id, a.item_number_id, a.color_size_sensitive, a.color_break_down, b.color_number_id, b.gmts_sizes, b.item_size, c.size_number_id, d.colar_cuff_per, e.body_part_full_name, e.body_part_type
			FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c, wo_booking_dtls d, lib_body_part e

			WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.body_part_id=e.id and e.body_part_type in (40,50) and c.id=d.color_size_table_id and d.color_size_table_id=b.color_size_table_id  and d.po_break_down_id=c.po_break_down_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 order by  c.size_order";
			//echo $collar_cuff_sql;
			$collar_cuff_sql_res=sql_select($collar_cuff_sql);
			
			$itemIdArr=array();

			foreach($collar_cuff_sql_res as $collar_cuff_row)
			{
				$collar_cuff_percent_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('gmts_sizes')]]=$collar_cuff_row[csf('colar_cuff_per')];
				$collar_cuff_body_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]]=$collar_cuff_row[csf('body_part_full_name')];
				$collar_cuff_size_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('size_number_id')]]=$collar_cuff_row[csf('size_number_id')];
				if(!empty($collar_cuff_row[csf('item_size')]))
				{
					$collar_cuff_item_size_arr[$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('size_number_id')]][$collar_cuff_row[csf('item_size')]]=$collar_cuff_row[csf('item_size')];
				}
				
				$color_size_sensitive_arr[$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('id')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('color_size_sensitive')]]=$collar_cuff_row[csf('color_break_down')];
				
				$itemIdArr[$collar_cuff_row[csf('body_part_type')]].=$collar_cuff_row[csf('item_number_id')].',';
			}
			//print_r($collar_cuff_percent_arr[40]) ;
			unset($collar_cuff_sql_res);
			//$count_collar_cuff=count($collar_cuff_size_arr);
			
			$order_plan_qty_arr=array();
			$color_wise_wo_sql_qnty=sql_select( "select item_number_id, color_number_id, size_number_id, sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in ($booking_po_id) and status_active=1 and is_deleted =0  group by item_number_id, color_number_id, size_number_id");//and item_number_id in (".implode(",",$itemIdArr).")
			foreach($color_wise_wo_sql_qnty as $row)
			{
				$order_plan_qty_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['plan']=$row[csf('plan_cut_qnty')];
				$order_plan_qty_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order']=$row[csf('order_quantity')];
			}
			unset($color_wise_wo_sql_qnty);

			
			foreach($collar_cuff_body_arr as $body_type=>$body_name)
			{
				$gmtsItemId=array_filter(array_unique(explode(",",$itemIdArr[$body_type])));
				foreach($body_name as $body_val)
				{
					$count_collar_cuff=count($collar_cuff_size_arr[$body_type][$body_val]);
					$pre_grand_tot_collar=0; $pre_grand_tot_collar_order_qty=0;

					?>
                    <div style="max-height:1330px; overflow:auto; float:left; padding-top:5px; margin-left:5px; margin-bottom:5px; position:relative;font-size:18px;">
					<table width="625" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                        <tr>
                        	<td colspan="<? echo $count_collar_cuff+3; ?>" align="center"><b><? echo $body_val; ?> - Color Size Brakedown in Pcs.</b></td>
                        </tr>
                        <tr>
                            <td width="100">Size</td>
								<?
                                foreach($collar_cuff_size_arr[$body_type][$body_val]  as $size_number_id)
                                {
									?>
									<td align="center" style="border:1px solid black"><strong><? echo $size_library[$size_number_id];?></strong></td>
									<?
                                }
                                ?>
                            <td width="60" rowspan="2" align="center"><strong>Total</strong></td>
                            <td rowspan="2" align="center"><strong>Extra %</strong></td>
                        </tr>
                        <tr>
                            <td style="font-size:12px"><? echo $body_val; ?> Size</td>
                            <?
                            foreach($collar_cuff_item_size_arr[$body_val]  as $size_number_id=>$size_number)
                            {
								if(count($size_number)>0)
								{
									 foreach($size_number  as $item_size=>$val)
									 {
										?>
										<td align="center" style="border:1px solid black"><strong><? echo $item_size;?></strong></td>
										<?
									 }
								}
								else
								{
									?>
									<td align="center" style="border:1px solid black"><strong>&nbsp;</strong></td>
									<?
								}
                            }
                            ?>
                        </tr>
                            <?

                            $pre_size_total_arr=array();
                            foreach($color_size_sensitive_arr[$body_val] as $pre_cost_id=>$pre_cost_data)
                            {
								foreach($pre_cost_data as $color_number_id=>$color_number_data)
								{
									foreach($color_number_data as $color_size_sensitive=>$color_break_down)
									{
										$pre_color_total_collar=0;
										$pre_color_total_collar_order_qnty=0;
										$process_loss_method=$process_loss_method;
										$constrast_color_arr=array();
										if($color_size_sensitive==3)
										{
											$constrast_color=explode('__',$color_break_down);
											for($i=0;$i<count($constrast_color);$i++)
											{
												$constrast_color2=explode('_',$constrast_color[$i]);
												$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
											}
										}
										?>
										<tr>
											<td>
												<?
                                                if( $color_size_sensitive==3)
                                                {
                                                    echo strtoupper ($constrast_color_arr[$color_number_id]) ;
                                                    $lab_dip_color_id=$lab_dip_color_arr[$pre_cost_id][$color_number_id];
                                                }
                                                else
                                                {
                                                    echo $color_library[$color_number_id];
                                                    $lab_dip_color_id=$color_number_id;
                                                }
                                                ?>
											</td>
											<?
											foreach($collar_cuff_size_arr[$body_type][$body_val] as $size_number_id)
											{
												?>
												<td align="center" style="border:1px solid black">
													<? $plan_cut=0; $collerqty=0; $collar_ex_per=0;
													$plan_cut=0;
													foreach($gmtsItemId as $giid)
													{
														// if($body_type==50) $plan_cut+=($order_plan_qty_arr[$giid][$color_number_id][$size_number_id]['plan'])*2;
														// else $plan_cut+=$order_plan_qty_arr[$giid][$color_number_id][$size_number_id]['plan'];

														$plan_cut+=$order_plan_qty_arr[$giid][$color_number_id][$size_number_id]['plan'];
													}
													
													
                                                    //$ord_qty=$order_plan_qty_arr[$color_number_id][$size_number_id]['order'];

                                                    $collar_ex_per=$collar_cuff_percent_arr[$body_type][$body_val][$color_number_id][$size_number_id];
                                                    // echo $collar_ex_per.'=';

												    if($body_type==50) { if($collar_ex_per==0 || $collar_ex_per=="") $collar_ex_per=$cuff_excess_percent; else $collar_ex_per=$collar_ex_per; }
                                                    else if($body_type==40) { if($collar_ex_per==0 || $collar_ex_per=="") $collar_ex_per=$colar_excess_percent; else $collar_ex_per=$collar_ex_per; }
                                                    $colar_excess_per=($plan_cut*$collar_ex_per)/100;

                                                	if($body_type==50){
														$collerqty=($plan_cut+$colar_excess_per)*2;
													}else{
														$collerqty=($plan_cut+$colar_excess_per);
													}

                                                    //$collerqty=number_format(($requirment/$costing_per_qnty)*$plan_cut,2,'.','');

                                                    echo number_format($collerqty);
                                                    $pre_size_total_arr[$size_number_id]+=$collerqty;
                                                    $pre_color_total_collar+=$collerqty;
                                                    $pre_color_total_collar_order_qnty+=$plan_cut;

                                                    //$pre_grand_tot_collar_order_qty+=$plan_cut;
                                                    ?>
												</td>
												<?
											}
											?>

											<td align="center"><? echo number_format($pre_color_total_collar); ?></td>
											<!-- <td align="center"><? echo number_format((($pre_color_total_collar-$pre_color_total_collar_order_qnty)/$pre_color_total_collar_order_qnty)*100,2); ?></td> -->
												<td align="center"><? echo $collar_ex_per; ?></td>
										</tr>
										<?
										$pre_grand_collar_ex_per+=$collar_ex_per;
										$pre_grand_tot_collar+=$pre_color_total_collar;
										$pre_grand_tot_collar_order_qty+=$pre_color_total_collar_order_qnty;
									}
								}
							}
							?>
                        
                        <tr>
                            <td>Size Total</td>
								<?
                               // foreach($pre_size_total_arr  as $size_qty)
                               // {
                                	foreach($collar_cuff_size_arr[$body_type][$body_val] as $size_number_id)
									{
										$size_qty=$pre_size_total_arr[$size_number_id];
										?>
										<td style="border:1px solid black;  text-align:center"><? echo number_format($size_qty); ?></td>
										<?
									}

                               // }
                                ?>
                            <td style="border:1px solid black; text-align:center"><? echo number_format($pre_grand_tot_collar); ?></td>
                            <!-- <td align="center" style="border:1px solid black"><? echo number_format((($pre_grand_tot_collar-$pre_grand_tot_collar_order_qty)/$pre_grand_tot_collar_order_qty)*100,2); ?></td> -->
							<td align="center" style="border:1px solid black"></td>
                        </tr>
					</table>
                </div>
                <?
            }
        }

        ?>

       		

        <?

	if($show_yarn_rate==1)
	{
		 $condition= new condition();
		if(str_replace("'","",$txt_order_no_id) !=''){
			$condition->po_id("in(".str_replace("'","",$txt_order_no_id).")");
		}

		$condition->init();
		$cos_per_arr=$condition->getCostingPerArr();
		$yarn= new yarn($condition);
		$yarn_data_array=$yarn->getCountCompositionPercentTypeColorAndRateWiseYarnQtyAndAmountArray();
		$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');

		$yarn_sql_array=sql_select("SELECT min(a.id) as id ,a.count_id, a.copm_one_id, a.percent_one, a.color, a.type_id, sum(a.cons_qnty) as yarn_required, a.rate  from wo_pre_cost_fab_yarn_cost_dtls a, wo_booking_dtls b where a.job_no=b.job_no and a.fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.job_no='$job_no' and b.booking_no=$txt_booking_no  and  a.status_active=1 and a.is_deleted=0 group by a.count_id,a.copm_one_id,a.percent_one,a.color,a.type_id,a.rate order by id");
	
		?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" style="font-family:Arial Narrow;font-size:18px;" >
            <tr>
                <td width="100%" valign="top">
                    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td colspan="7"><b>Yarn Required Summary (Pre Cost)</b></td>

                    </tr>
                    <tr align="center">
                    <td width="40">Sl</td>
                    <td width="40">Yarn Count</td>
                    <td width="40">Yarn Type</td>
                    <td width="120">Yarn Description</td>
                    <td width="40">Brand</td>
                    <td width="40">Lot</td>                   
                    <td width="40">Rate</td>                  
                    <td width="40">Cons for <? echo $costing_per; ?> Gmts</td>
                    <td width="40">Total (KG)</td>
                    </tr>
                    <?
					$i=0;
					$total_yarn=0;
					foreach($yarn_sql_array  as $row)
                    {

						$i++;
						$rowcons_qnty = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];
						$rowcons_Amt = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['amount'];


						$rate=$rowcons_Amt/$rowcons_qnty;
						$rowcons_qnty =($rowcons_qnty/100)*$booking_percent;
					?>
                    <tr align="center">
                    <td><? echo $i; ?></td>
                    <td><?	echo $yarn_count_arr[$row[csf('count_id')]];?>
                    </td>
					<td><? echo $yarn_type[$row[csf('type_id')]] ?></td>
                    <td>
						<? 
							$yarn_des=$composition[$row[csf('copm_one_id')]]." ".$row[csf('percent_one')]."%  ";
					$yarn_des.=$color_library[$row[csf('color')]]." ";
					echo $yarn_des;
						?>
					</td>
                    <td></td>                   
                    <td></td>                   
                    <td><? echo number_format($row[csf('rate')],4); ?></td>
                     
                    <td title="<?="(".$rowcons_qnty."/".$po_qnty_tot.")*".$cos_per_arr[$job_no];?>"><?  echo number_format(($rowcons_qnty/$po_qnty_tot)*$cos_per_arr[$job_no],4);//echo number_format($row[csf('yarn_required')],4); ?></td>

                    <td align="right">
					<? echo number_format($rowcons_qnty,2); $total_yarn+=$rowcons_qnty; ?></td>
                    </tr>
                    <?
					}
					?>
                    <tr align="center">
                    <td>Total</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>                  
                    <td></td>                   
                    <td></td>
                    <td align="right"><? echo number_format($total_yarn,2); ?></td>
                    </tr>
                    </table>
                </td>
               
                </td>
            </tr>
        </table>
        <?
	}
		$sql_embelishment=sql_select("select emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls where job_no='$job_no' and status_active=1 and 	is_deleted=0");
		?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" style="font-family:Arial Narrow;font-size:18px;">
            <tr>
                <td width="49%" valign="top">
                <?
				/* if(count($sql_embelishment)>0)
				{ */
				?>
                    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" style="font-family:Arial Narrow;font-size:18px;">
                    <tr align="center">
                    <td colspan="7"><b>Embelishment (Pre Cost)</b></td>

                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Embelishment Name</td>
                    <td>Embelishment Type</td>
                    <td>Cons <? echo $costing_per; ?> Gmts</td>
                    <td>Rate</td>
                    <td>Amount</td>

                    </tr>
                    <?
					$sql_embelishment=sql_select("select emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls where job_no='$job_no' and status_active=1 and 	is_deleted=0");
					$i=0;
					//$total_yarn=0;
					foreach($sql_embelishment  as $row_embelishment)
                    {

						$i++;
					?>
                    <tr align="center">
                    <td><? echo $i; ?></td>
                    <td>
					<?
					echo $emblishment_name_array[$row_embelishment[csf('emb_name')]];
					?>
                    </td>
                    <td>
                    <?
					if($row_embelishment[csf('emb_name')]==1)
					{
					echo $emblishment_print_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==2)
					{
					echo $emblishment_embroy_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==3)
					{
					echo $emblishment_wash_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==4)
					{
					echo $emblishment_spwork_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==5)
					{
					echo $emblishment_gmts_type[$row_embelishment[csf('emb_type')]];
					}
					?>

                    </td>
                    <td>
                    <?
					echo $row_embelishment[csf('cons_dzn_gmts')];
					?>
                    </td>
                    <td>
					<?
					echo $row_embelishment[csf('rate')];
					?>
                    </td>

                    <td>
					<?
					echo $row_embelishment[csf('amount')];
					?>
                    </td>


                    </tr>
                    <?
					}
					?>

                    </table>
                    <?
				//}
					?>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top" align="center">
                				<?
                				$lib_designation_arr=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");
									 $user_lib_designation_arr=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
									 $user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

								$mst_id=return_field_value("id as mst_id","wo_booking_mst","booking_no=$txt_booking_no","mst_id");
                				 $unapprove_data_array=sql_select("select b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date,b.approved_no from   approval_history b where b.mst_id=$mst_id and b.entry_form=7  order by b.approved_date,b.approved_by");


                				/* if(count($unapprove_data_array)>0)
                				{ */
                				$sql_unapproved=sql_select("select booking_id,approval_cause from fabric_booking_approval_cause where  entry_form=7 and approval_type=2 and is_deleted=0 and status_active=1 and booking_id=$mst_id");
                					$unapproved_request_arr=array();
                					foreach($sql_unapproved as $rowu)
                					{
                						$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
                					}
                		 		?>
                		       <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
                		            <thead>
                		            <tr style="border:1px solid black;">
                		                <th colspan="6" style="border:1px solid black;">Approval/Un Approval History</th>
                		                </tr>
                		                <tr style="border:1px solid black;">
                		                <th width="3%" style="border:1px solid black;">Sl</th>
                						<th width="30%" style="border:1px solid black;">Name</th>
                						<th width="20%" style="border:1px solid black;">Designation</th>
                						<th width="5%" style="border:1px solid black;">Approval Status</th>
                						<th width="20%" style="border:1px solid black;">Reason For Un Approval</th>
                						<th width="22%" style="border:1px solid black;"> Date</th>

                		                </tr>
                		            </thead>
                		            <tbody>
                		            <?
                					$i=1;
                					foreach($unapprove_data_array as $row){

                					?>
                		            <tr style="border:1px solid black;">
                		                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
                						<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
                						<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
                						<td width="5%" style="border:1px solid black; text-align:center"><? echo 'Yes';?></td>
                						<td width="20%" style="border:1px solid black;"><? echo '';?></td>
                						<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); else echo "";?></td>
                		            </tr>
                		                <?
                						$i++;
                						$un_approved_date= explode(" ",$row[csf('un_approved_date')]);
                						$un_approved_date=$un_approved_date[0];
                						if($db_type==0) //Mysql
                						{
                							if($un_approved_date=="" || $un_approved_date=="0000-00-00") $un_approved_date="";else $un_approved_date=$un_approved_date;
                						}
                						else
                						{
                							if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;
                						}

                						if($un_approved_date!="")
                						{
                						?>
                					<tr style="border:1px solid black;">
                		                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
                						<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
                						<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
                						<td width="5%" style="border:1px solid black;text-align:center;"><? echo 'No';?></td>
                						<td width="20%" style="border:1px solid black;text-align:center"><? echo $unapproved_request_arr[$mst_id];?></td>
                						<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('un_approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('un_approved_date')])); else echo "";?></td>
                		              </tr>

                		                <?
                						$i++;
                						}

                					}
                						?>
                		            </tbody>
                		        </table>
                				<?
                				//}
                				?>

                </td>
            </tr>
        </table>
        <table  width="100%" style="margin: 0px;padding: 0px;">
        <?php $stripe_color_wise=array(); ?>
       
        <tr>
        	<td width="70%">
        		<table  class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%"   style="font-family:Arial Narrow;font-size:18px;margin: 0px;padding: 0px;" >
        		       
        		        <tr>
        		            <td align="center" colspan="10">  Stripe Details</td>
        		            
    		            </tr>
        		        <?
        				$color_name_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
        				$sql_stripe=("select c.id,c.composition,c.construction,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,sum(b.grey_fab_qnty) as fab_qty,b.dia_width,d.color_number_id as color_number_id,d.totfidder,d.id as did,d.stripe_color,d.fabreqtotkg as fabreqtotkg ,d.measurement as measurement ,d.yarn_dyed,d.uom  from wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c,wo_pre_stripe_color d, wo_po_color_size_breakdown e where c.id=b.pre_cost_fabric_cost_dtls_id and c.job_no=b.job_no and d.pre_cost_fabric_cost_dtls_id=c.id and d.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and b.job_no=d.job_no and b.job_no='$job_no'  and d.job_no='$job_no' and b.booking_no=$txt_booking_no  and c.color_type_id in (2,6,33,34) and b.status_active=1  and c.is_deleted=0 and c.status_active=1  and d.is_deleted=0 and d.status_active=1 and b.is_deleted=0 and e.id=b.color_size_table_id and e.is_deleted=0 and e.status_active=1 and e.color_number_id=d.color_number_id  group by c.id,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,d.color_number_id,d.id,d.stripe_color,d.yarn_dyed,d.fabreqtotkg ,d.measurement,d.uom,c.composition,c.construction,b.dia_width,d.totfidder order by d.id ");        				
        				$result_data=sql_select($sql_stripe);
        				foreach($result_data as $row)
        				{
        					$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['stripe_color'][$row[csf('did')]]=$row[csf('stripe_color')];
        					$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['measurement'][$row[csf('did')]]=$row[csf('measurement')];
        					$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['uom'][$row[csf('did')]]=$row[csf('uom')];
        					$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fabreqtotkg'][$row[csf('did')]]=$row[csf('fabreqtotkg')];
        					$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['yarn_dyed'][$row[csf('did')]]=$row[csf('yarn_dyed')];	
							$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['totfidder'][$row[csf('did')]]=$row[csf('totfidder')];
							$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['subtotal_measurement'][$row[csf('did')]]=$row[csf('measurement')];;

        					$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['composition']=$row[csf('composition')];
        					$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['construction']=$row[csf('construction')];
        					$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['gsm_weight']=$row[csf('gsm_weight')];
        					$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['color_type_id']=$row[csf('color_type_id')];
        					$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['dia_width']=$row[csf('dia_width')];
					

        				}
						// echo "<pre>";
						// print_r($stripe_arr2);
        				?>
        		            <tr>
	        		            <td align="center" width="30"> SL</td>
	        		            <td align="center" width="100"> Body Part</td>
	        		            <td align="center" width="80"> Fabric Color</td>
	        		            <td align="center" width="70"> Fabric Qty(KG)</td>
	        		            <td align="center" width="70"> Stripe Color</td>
	        		            <td align="center" width="70"> Stripe Measurement</td>
	        		            <td align="center" width="70"> Stripe Uom</td>
                                  <td  align="center" width="70"> Total Feeder</td>
	        		            <td  align="center" width="70"> Qty.(KG)</td>
                               
	        		            <td  align="center" width="70"> Y/D Req.</td>
        		            </tr>
        		            <?
							$color_wise_qty=sql_select("select  a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,d.gmts_color_id,(d.fin_fab_qnty) as fin_fab_qnty,(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
        								WHERE a.job_id=b.job_id and
        								a.id=b.pre_cost_fabric_cost_dtls_id and
        								c.job_no_mst=a.job_no and
        								c.id=b.color_size_table_id and
        								b.po_break_down_id=d.po_break_down_id and
        								b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
        								d.booking_no =$txt_booking_no and
        								d.status_active=1 and
        								d.is_deleted=0
        								");
										$tot_color_qty=0;
										foreach($color_wise_qty as $row)
										{
											$data_str=$row[csf('body_part_id')].$row[csf('color_type_id')].$row[csf('gmts_color_id')];
											$tot_color_qtyArr[$data_str]+=$row[csf('fin_fab_qnty')];
										}
										//echo $tot_color_qty.'=A';
										unset($color_wise_qty);
										
        					$i=1;$total_fab_qty=0;$total_fabreqtotkg=$total_totfidder=0;$fab_data_array=array();$qnty=0;
        		            foreach($stripe_arr as $body_id=>$body_data)
        		            {
        						foreach($body_data as $color_id=>$color_val)
        						{
        							$rowspan=count($color_val['stripe_color']);
        							$composition=$stripe_arr2[$body_id][$color_id]['composition'];
        							$construction=$stripe_arr2[$body_id][$color_id]['construction'];
        							$gsm_weight=$stripe_arr2[$body_id][$color_id]['gsm_weight'];
        							$color_type_id=$stripe_arr2[$body_id][$color_id]['color_type_id'];
        							$dia_width=$stripe_arr2[$body_id][$color_id]['dia_width'];
									$subtotal_measurement=array_sum($color_val['subtotal_measurement']);
									

        							/*if($db_type==0) $color_cond="d.gmts_color_id='".$color_id."'";
        							else if($db_type==2) $color_cond="nvl(d.gmts_color_id,0)=nvl('".$color_id."',0)";

        							$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
        								WHERE a.job_id=b.job_id and
        								a.id=b.pre_cost_fabric_cost_dtls_id and
        								c.job_no_mst=a.job_no and
        								c.id=b.color_size_table_id and
        								b.po_break_down_id=d.po_break_down_id and
        								b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
        								d.booking_no =$txt_booking_no and
        								a.body_part_id='".$body_id."' and
        								a.color_type_id='".$color_type_id."' and
        								a.construction='".$construction."' and
        								a.composition='".$composition."' and
        								a.gsm_weight='".$gsm_weight."' and
        								$color_cond and
        								d.status_active=1 and
        								d.is_deleted=0
        								");
        						
        								list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty;*/
        							$sk=0;
    								foreach($color_val['stripe_color'] as $strip_color_id=>$s_color_val)
        							{
        								
        								?>
	        							<tr>
		        							<?
		        							if($sk==0)
		        							{

												$data_str=$body_id.$color_type_id.$color_id;
			        							$color_qty=$tot_color_qtyArr[$data_str];
			        							//$color_qty=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
												$qnty=$color_qty/$subtotal_measurement;
			        							?>
			        							<td rowspan="<? echo $rowspan;?>"> <? echo $i; ?></td>
			        							<td rowspan="<? echo $rowspan;?>"> <? echo $body_part[$body_id]; ?></td>
			        							<td rowspan="<? echo $rowspan;?>"> <? echo $color_name_arr[$color_id]; ?></td>
			        							<td rowspan="<? echo $rowspan;?>" align="right"> <? echo number_format($color_qty,2); ?>&nbsp; </td>
			        							<?
			        							$total_fab_qty+=$color_qty;
											
			        							$i++;
			        						}
		        							$sk=0;
		        							

		        								$measurement=$color_val['measurement'][$strip_color_id];
												$totfidder=$color_val['totfidder'][$strip_color_id];
		        								$uom=$color_val['uom'][$strip_color_id];
		        								$fabreqtotkg=$color_val['fabreqtotkg'][$strip_color_id];
		        								$yarn_dyed=$color_val['yarn_dyed'][$strip_color_id];
												$qnty_kg=$qnty*$measurement;
		        								
		        								?>
		        							
			        								<td><?  echo  $color_name_arr[$s_color_val]; ?></td>
			        								<td align="right"> <? echo  number_format($measurement,2); ?> &nbsp; </td>
			        		                        <td> <? echo  $unit_of_measurement[$uom]; ?></td>
                                                      <td align="right"> <? echo  number_format($totfidder,2); ?> &nbsp;</td>
			        								<td align="right"> <? echo  number_format($qnty_kg,2); ?> &nbsp;</td>
                                                  
			        								<td> <? echo  $yes_no[$yarn_dyed]; ?></td>
		        								
		        								<?
		        								
		        								$sk++;
		        								$total_fabreqtotkg+=$qnty_kg;
												$total_totfidder+=$totfidder;
		        								$stripe_color_wise[$color_name_arr[$s_color_val]]+=$qnty_kg;
		        								$color_sub_tot[$body_id][$color_id]['fab_qnty']+=$qnty_kg;
		        							
											
		        							?>
	        							</tr>
	        							<?
	        						}
									?>
								  <tr>
			        		            <td colspan="4">&nbsp; </td>
			        		            <td align="center" colspan="4"><b> Sub Total </b></td>
			        		            <td align="right"><b><? echo  number_format($color_sub_tot[$body_id][$color_id]['fab_qnty'],2); ?></b> &nbsp;</td>
			        		        </tr>

							<? }  
        						}?>
	        		            <tfoot>
		        		            <tr>
			        		            <td colspan="3">Total </td>
			        		            <td align="right">  <? echo  number_format($total_fab_qty,2); ?> &nbsp;</td>
			        		            <td></td>
			        		            <td></td>
                                       
			        		            <td>   </td>
                                          <td  align="right"><? echo  number_format($total_totfidder,2); ?></td>
			        		            <td align="right"><? echo  number_format($total_fabreqtotkg,2); ?> &nbsp;</td>
			        		        </tr>
	        		            </tfoot>
        		            </table>
        	</td>
        	
        	<td width="20%" >
        		        <table  class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%"   style="font-family:Arial Narrow;font-size:18px;margin: 0px;padding: 0px;"  >
        		       

        		        <tr>
        		            <td align="left" colspan="3"> Stripe  Color wise Summary</td>
        		            
        		           
        		           
    		            </tr>
        		        <?
        				
        				?>
        		            <tr>
	        		            <td width="30"> SL</td>
	        		            
	        		            <td width="70"> Stripe Color</td>
	        		           
	        		            <td  width="70"> Qty.(KG)</td>
	        		           
        		            </tr>
        		            <?

        					$i=1;$total_stripe_qnt=0;        		            
        						foreach($stripe_color_wise as $color=>$val)
        						{
        							
        							
        							?>
        							<tr>
	        							<td> <? echo $i; ?></td>
	        							
	        							<td > <? echo $color; ?></td>
	        							<td align="right"> <?php echo number_format($val,2); ?></td>
	        						</tr>
        							
        							<?
        							$total_stripe_qnt+=$val;
        							
        							$i++;
        						}
        					
        					?>
        		            <tfoot>
        		            <tr>
        		            
        		            <td></td>
        		            <td></td>
        		            
        		            <td align="right"><? echo  number_format($total_stripe_qnt,2); ?> </td>
        		            </tr>
        		            </tfoot>
        		            </table>
        	</td>
        </tr>
         </table >
			<table width="98%">
       		 	<tr>
       		 		<td width="45%" style="float: left;">
       		 			<?php 

       		 				$sql_purchase="SELECT a.booking_no, a.uom, sum(b.fin_fab_qnty) as qnty , b.construction, b.copmposition, b.gsm_weight as dia, b.dia_width as gsm from wo_booking_mst a, wo_booking_dtls b where     a.booking_no = b.booking_no and a.fabric_source = 2 and b.job_no = '$txt_job_no'and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 and  a.booking_type=1 group by a.booking_no, a.uom, b.construction, b.copmposition, b.dia, b.gsm_weight, b.dia_width"; 
       		 				//echo $sql_purchase;
							$purchase_res=sql_select($sql_purchase);
							?>
							<table  width="98%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">

 			                   <thead>
				                   <tr align="center">
				                    	<th colspan="5"><b>Purchased Booking Info</b></th>
				                    </tr>
				                    <tr align="center">
					                    <th>Sl</th>
					                    <th>Booking No</th>
					                    <th>Fabric Data</th>
					                    <th>UOM</th>
					                    <th>Qnty</th>
					                   
				                    </tr>
 			                   </thead>
 			                   <tbody>
 			                   		<?

 			                   			foreach ($purchase_res as  $row) 
 			                   			{
 			                   				?>
 			                   				<tr>
 			                   					<td><?=$p++;?></td>
 			                   					<td><p><?=$row[csf('booking_no')];?></p></td>
 			                   					<td><p><?=$row[csf('construction')] .", ".$row[csf('copmposition')].", ".$row[csf('gsm')].", ".$row[csf('dia')];?></p></td>
 			                   					<td><p><?=$unit_of_measurement[$row[csf('uom')]];?></p></td>
 			                   					<td><p><?=number_format($row[csf('qnty')],2);?></p></td>
 			                   				</tr>
 			                   				<?
 			                   			}


 			                   		?>
 			                   </tbody>
 			                   
 			            </table>

       		 			 
       		 		</td>
       		 		<td width="45%" style="float: right;">
 			       		 <table  width="98%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">

 			                   <tr align="center">
 			                    	<td colspan="10"><b>Dyes To Match</b></td>
 			                    </tr>
 			                    <tr align="center">
 				                    <td>Sl</td>
 				                    <td>Item</td>
 				                    <td>Item Description</td>
 				                    <td>Body Color</td>
 				                    <td>Item Color</td>
 				                    <td>Finish Qty.</td>
 				                    <td>UOM</td>
 			                    </tr>
 			                    <?
 								$lib_item_group_arr=return_library_array( "select item_name, id from lib_item_group where item_category=4 and is_deleted=0  and  status_active=1 order by item_name", "id", "item_name");
 								$sql=sql_select("select id from wo_booking_mst where booking_no=$txt_booking_no");
 								$bookingId=0;
 								foreach($sql as $row){
 									$bookingId= $row[csf('id')];
 								}
 								$co=0;
 								$sql_data=sql_select("select a.fabric_color,a.item_color,a.precost_trim_cost_id,b.trim_group,b.cons_uom,sum(qty) as qty, b.description   from wo_dye_to_match a, wo_pre_cost_trim_cost_dtls b where a.precost_trim_cost_id=b.id and a.booking_id=$bookingId and a.qty>0 and a.status_active=1 and b.status_active=1  group by a.fabric_color,a.item_color,a.precost_trim_cost_id,b.trim_group,b.cons_uom, b.description order by a.fabric_color");

 								foreach($sql_data  as $row)
 			                    {
 									$co++;
 									?>
 				                    <tr>
 				                    <td><? echo $co; ?></td>
 				                    <td> <? echo $lib_item_group_arr[$row[csf('trim_group')]];?></td>
 				                    <td><p> <? echo $row[csf('description')];?></p></td>
 				                    <td><? echo $color_library[$row[csf('fabric_color')]];?></td>
 				                    <td><? echo $color_library[$row[csf('item_color')]];?></td>
 				                    <td align="right"><? echo $row[csf('qty')];?></td>
 				                    <td><? echo $unit_of_measurement[$row[csf('cons_uom')]];?></td>
 				                    </tr>
 				                    <?
 								}
 								?>
 			            </table>
       		 		</td>
       		 	</tr>
       		 </table>

			<table width="98%">
       		 	<tr>
       		 		<td width="45%" style="float: left;">
       		 			<?php 
							$rmg_process_breakdown_arr=array();
							$rmg_process_breakdown_arr=explode('_',$rmg_process_breakdown);
							?>
							<table  width="98%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">

 			                   <thead>
				                   <tr align="center">
				                    	<th colspan="9"><b>Fabrics Process Loss %</b></th>
				                    </tr>
				                    <tr align="center">
					                    <th>Yarn Dyeing</th>
					                    <th>Knitting </th>
					                    <th>Fabric Dyeing </th>
					                    <th>AOP</th>									
					                    <th>Dyeing & Finishing  </th>					                    
					                    <th>Others</th>
					                    <th>Total</th>
					                   
				                    </tr>
 			                   </thead>
 			                   <tbody>
									<tr>
										<td><? echo number_format($rmg_process_breakdown_arr[12],2); ?></td>
										<td><? echo number_format($rmg_process_breakdown_arr[6],2); ?></td>
										<td><? echo number_format($rmg_process_breakdown_arr[7],2); ?></td>
										<td><? echo number_format($rmg_process_breakdown_arr[13],2); ?></td>									
										<td><? echo number_format($rmg_process_breakdown_arr[5],2); ?></td>										
										<td><? echo number_format($rmg_process_breakdown_arr[9],2); ?></td>
										<? $total_fab=$rmg_process_breakdown_arr[12]+$rmg_process_breakdown_arr[6]+$rmg_process_breakdown_arr[7]+$rmg_process_breakdown_arr[13]+$rmg_process_breakdown_arr[5]+$rmg_process_breakdown_arr[9];?>
										<td><p><?=number_format($total_fab,2);?></p></td>
									</tr>
 			                   		
 			                   </tbody>
 			                   
 			            </table>

       		 			 
       		 		</td>
       		 		<td width="45%" style="float: right;">
 			       		 <table  width="98%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">

 			                   <tr align="center">
 			                    	<td colspan="8"><b>RMG Process Loss %</b></td>
 			                    </tr>
 			                    <tr align="center">
 				                    <td>Cutting</td>
 				                    <td>Sewing</td>
 				                    <td>Chest Printing</td>
 				                    <td>Embroidery</td>
 				                    <td>Finishing</td>
									<td>Cut Panel rejection</td>
 				                    <td>Neck/Sleeve Printing</td>
									<td> Garments Wash</td>
									<td> Lay Wash (Fabric) </td>
 				                    <td>Total</td>
 				                   
 			                    </tr>
									<? if($rmg_process_breakdown_arr[0]==''){
										$rmg_process_breakdown_arr[0]=0;
									} ?>
 				                    <tr>
 				                    <td><? echo fn_number_format($rmg_process_breakdown_arr[0],2); ?></td>
 				                    <td><? echo number_format($rmg_process_breakdown_arr[4],2); ?></td>
 				                    <td><? echo number_format($rmg_process_breakdown_arr[2],2); ?></td>
 				                    <td><? echo number_format($rmg_process_breakdown_arr[1],2); ?></td>
 				                    <td><? echo number_format($rmg_process_breakdown_arr[15],2); ?></td>
									<td><? echo number_format($rmg_process_breakdown_arr[8],2); ?></td>
 				                    <td><? echo number_format($rmg_process_breakdown_arr[10],2); ?></td>
									 <td><? echo number_format($rmg_process_breakdown_arr[3],2); ?></td>
									 <td><? echo number_format($rmg_process_breakdown_arr[14],2); ?></td>
									 <?php
										$total_rmg+=$rmg_process_breakdown_arr[0]+$rmg_process_breakdown_arr[2]+$rmg_process_breakdown_arr[8]+$rmg_process_breakdown_arr[4]+$rmg_process_breakdown_arr[10]+$rmg_process_breakdown_arr[1]+$rmg_process_breakdown_arr[15]+$rmg_process_breakdown_arr[3]+$rmg_process_breakdown_arr[14];
									 ?>
 				                    <td align="right"><? echo number_format($total_rmg,2);?></td>
 				                    
 				                    </tr>
 				                    <?
 								
 								?>
 			            </table>
       		 		</td>
       		 	</tr>
       		 </table>
        <?
			if($cbo_fabric_source==1)
			{
				$po_data=sql_select("select a.po_break_down_id	from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id and  a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") 
				and a.booking_no=$txt_booking_no and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 group by a.po_break_down_id");

			
				foreach($po_data as $val){

					$po_ids[$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];
				}



        	$grand_order_total=0; $grand_plan_total=0; $size_wise_total=array();
			$nameArray_size=sql_select( "select size_number_id,size_order from wo_po_color_size_breakdown where  is_deleted=0 ".where_con_using_array($po_ids,1,'po_break_down_id')." and status_active=1 group by size_number_id,size_order  order by size_order");
			
			?>
                <div id="div_size_color_matrix" class="pagebreak">
                    <fieldset id="div_size_color_matrix" >
                        <legend>Size and Color Breakdown</legend>
                        <table  class="rpt_table"  border="1" align="left" cellpadding="0"  cellspacing="0" rules="all" >
                            <tr>
                            	<td>PO Number</td>
                            	<td>PO Received Date </td>
                                <td>Lab Dip No </td>
                            	<td>Ship Date</td>
                            	
                            	<td>Gmts Item</td>
                                <td style="border:1px solid black"><strong>Color/Size</strong></td>
                                <?
                                foreach($nameArray_size  as $result_size)
                                {
									?>
                                	<td align="center" style="border:1px solid black"><?=$size_library[$result_size[csf('size_number_id')]];?></td>
                                <? } ?>
                                <td  align="center"> Total Order Qty(Pcs)</td>
                                <td  align="center"> Excess %</td>
                                <td  align="center"> Total Plan Cut Qty(Pcs)</td>
                            </tr>
                            <?
                            $color_size_order_qnty_array=array(); $color_size_qnty_array=array(); $size_tatal=array(); $size_tatal_order=array();
                           	$item_size_tatal=array(); $item_size_tatal_order=array(); $item_grand_total=0; $item_grand_total_order=0;
                           	$result_cs=sql_select( "select b.item_number_id,b.color_number_id,sum(b.order_quantity) as order_quantity,b.size_number_id,b.po_break_down_id,b.size_order from wo_po_break_down a, wo_po_color_size_breakdown b where a.id=b.po_break_down_id and a.job_no_mst ='$job_no' ".where_con_using_array($po_ids,1,'b.po_break_down_id')." and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 group by b.item_number_id,b.color_number_id,b.size_number_id,b.po_break_down_id,b.size_order order by b.size_order ");
                           	$color_size_data=array();
                           	foreach ($result_cs as $row) {
                           		$color_size_data[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
                           	}

                           	$sql_color="select a.po_number,a.id,a.po_received_date,a.shipment_date,b.item_number_id,b.color_number_id,sum(b.plan_cut_qnty) as plan_cut_qnty,a.shiping_status from wo_po_break_down a, wo_po_color_size_breakdown b where a.id=b.po_break_down_id and a.job_no_mst ='$job_no' ".where_con_using_array($po_ids,1,'b.po_break_down_id')." and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 group by a.po_number,a.id,a.po_received_date,a.shipment_date,b.item_number_id,b.color_number_id,a.shiping_status order by a.po_number,a.shipment_date asc ";
                           	//echo $sql_color;
							$result_color_size=sql_select( $sql_color);

							foreach ($result_color_size as $row) 
							{
								$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."'  and approval_status=3   and color_name_id=".$row[csf('color_number_id')]."  and po_break_down_id=".$row[csf('id')]."  and status_active=1 and is_deleted=0");
								?>
								<tr>
									<td><?php echo $row[csf('po_number')] ?></td>
									<td><?php echo change_date_format($row[csf('po_received_date')]); ?></td>
                                     <td><?php  echo $lapdip_no; ?></td>
									<td><?php echo change_date_format($row[csf('shipment_date')]); ?></td>	
                                   								
									<td><?php echo $garments_item[$row[csf('item_number_id')]]; ?></td>
									<td><?php echo $color_library[$row[csf('color_number_id')]]; ?></td>
									<?
									$total=0;

                                    foreach($nameArray_size  as $key)
                                    {
										?>
                                    	<td align="center" style="border:1px solid black">
                                    		<?
                                    				$qnty=0;
                                    				$qnty=$color_size_data[$row[csf('id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$key[csf('size_number_id')]];

                                    				echo number_format($qnty);
                                    				$size_wise_total[$key[csf('size_number_id')]]+=$qnty;
                                    				$total+=$qnty;
                                    			?>
                                    	</td>
                                   	 	<? 
                                	}

                                	$grand_order_total+=$total;
        							$grand_plan_total+=$row[csf('plan_cut_qnty')];

        							$plan_cut_dif=$row[csf('plan_cut_qnty')]-$total;
        							$ex_cut_perc=($plan_cut_dif/$total)*100;

                                	?>
                                	<td align="center"><?php echo number_format($total); ?></td>
                                	<td align="center"><?php echo number_format($ex_cut_perc,2); ?>%</td>
                                	<td align="center"><?php echo number_format($row[csf('plan_cut_qnty')]); ?></td>
								</tr>
								<?
							}
                            ?>
                            <tr>
                            	<td align="right" colspan="6">Total</td>
                            	<?
                                    foreach($nameArray_size  as $key)
                                    {
										?>
                                    	<td align="center" style="border:1px solid black">
                                    		<strong><?
                                    				echo number_format($size_wise_total[$key[csf('size_number_id')]])
                                    			?>
                                    	</strong>
                                    	</td>
                                   	 	<? 
                                	}
                                	?>
                                <td align="center"><strong><?=number_format($grand_order_total)?></strong></td>
                                <td></td>
                                <td align="center"><strong><?=number_format($grand_plan_total)?></strong></td>
                            </tr>  
                        </table>
                    </fieldset>
                </div>

			<?php
				}
			?>
        <table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" style="font-family:Arial Narrow;font-size:18px;">
        <tr align="center">
        <td colspan="10"><b> Comments(Production) </b></td>
        </tr>
        <tr align="center">
        <td>Sl</td>
        <td>Po NO</td>
        <td>Ship Date</td>
        <td>Pre-Cost Qty</td>
        <td>Mn.Book Qty</td>
        <td>Sht.Book Qty</td>
        <td>Smp.Book Qty</td>
        <td>Tot.Book Qty</td>
        <td>Balance</td>
        <td>Comments</td>
        </tr>
        <?
        $cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
        $cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
        if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and a.fab_nature_id='$cbo_fabric_natu'";
        if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and a.fabric_source='$cbo_fabric_source'";
        $paln_cut_qnty_array=return_library_array( "select min(id) as id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown  where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by color_number_id,size_number_id,item_number_id,po_break_down_id", "id", "plan_cut_qnty");
        $item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no ='$txt_job_no'", "gmts_item_id", "set_item_ratio");

        $nameArray=sql_select("SELECT a.id, a.item_number_id, a.costing_per, b.po_break_down_id, b.color_size_table_id, b.requirment, c.po_number FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_po_break_down c WHERE a.job_id=b.job_id and a.job_no=c.job_no_mst and a.id=b.pre_cost_fabric_cost_dtls_id and b.po_break_down_id=c.id and b.po_break_down_id in (".str_replace("'","",$txt_order_no_id).")  $cbo_fabric_natu $cbo_fabric_source_cond and a.status_active=1 and a.is_deleted=0 order by id");
        $count=0;
        $tot_grey_req_as_pre_cost_arr=array();
        foreach ($nameArray as $result)
        {
        if (count($nameArray)>0 )
        {
        if($result[csf("costing_per")]==1)
        {
        $tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(12*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
        }
        if($result[csf("costing_per")]==2)
        {
        $tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(1*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
        }
        if($result[csf("costing_per")]==3)
        {
        $tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(24*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
        }
        if($result[csf("costing_per")]==4)
        {
        $tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(36*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
        }
        if($result[csf("costing_per")]==5)
        {
        $tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(48*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
        }
        $tot_grey_req_as_pre_cost_arr[$result[csf("po_number")]]+=$tot_grey_req_as_pre_cost;
        }
        }

        $total_pre_cost=0;
        $total_booking_qnty_main=0;
        $total_booking_qnty_short=0;
        $total_booking_qnty_sample=0;
        $total_tot_bok_qty=0;
        $tot_balance=0;
        $booking_qnty_main=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b, wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no  and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and a.is_short=2 and c.item_category=2 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");

        $booking_qnty_short=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and c.item_category=2 and a.is_short=1 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
        $booking_qnty_sample=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c  where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =4 and c.fabric_source=$cbo_fabric_source and c.item_category=2  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
        $sql_data=sql_select( "select max(a.id) as id,  a.po_number,max(a.pub_shipment_date) as pub_shipment_date,sum(a.plan_cut) as plan_cut  from wo_po_break_down a,wo_pre_cost_sum_dtls b,wo_pre_cost_mst c where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no and a.id in(".str_replace("'","",$txt_order_no_id).") group by a.po_number order by pub_shipment_date asc");
        foreach($sql_data  as $row)
        {
        $col++;
        ?>
			<tr align="center">
				<td><? echo $col; ?></td>
				<td><? echo $row[csf("po_number")]; ?></td>
				<td><? echo change_date_format($row[csf("pub_shipment_date")],"dd-mm-yyyy",'-'); ?></td>
				<td align="right"><? echo number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]],2); $total_pre_cost+=$tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]; ?></td>
				<td align="right"><? echo number_format($booking_qnty_main[$row[csf("id")]],2); $total_booking_qnty_main+=$booking_qnty_main[$row[csf("id")]];?></td>
				<td align="right"><? echo number_format($booking_qnty_short[$row[csf("id")]],2); $total_booking_qnty_short+=$booking_qnty_short[$row[csf("id")]];?></td>
				<td align="right"><? echo number_format($booking_qnty_sample[$row[csf("id")]],2); $total_booking_qnty_sample+=$booking_qnty_sample[$row[csf("id")]];?></td>
				<td align="right"><? $tot_bok_qty=$booking_qnty_main[$row[csf("id")]]+$booking_qnty_short[$row[csf("id")]]+$booking_qnty_sample[$row[csf("id")]]; echo number_format($tot_bok_qty,2); $total_tot_bok_qty+=$tot_bok_qty;?></td>
				<td align="right">
				<? $balance= def_number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]-$tot_bok_qty,2,""); echo number_format($balance,2); $tot_balance+= $balance?>
				</td>
				<td>
				<?
				if( $balance>0)
				{
				echo "Less Booking";
				}
				else if ($balance<0)
				{
				echo "Extra Booking";
				}
				else
				{
				echo "";
				}
				?>
				</td>
			</tr>
        <?
        }
        ?>
        <tfoot>
        <tr>
        <td colspan="3">Total:</td>
        <td align="right"><? echo number_format($total_pre_cost,2); ?></td>
        <td align="right"><? echo number_format($total_booking_qnty_main,2); ?></td>
        <td align="right"><? echo number_format($total_booking_qnty_short,2); ?></td>
        <td align="right"><? echo number_format($total_booking_qnty_sample,2); ?></td>
        <td align="right"><? echo number_format($total_tot_bok_qty,2); ?></td>
        <td align="right"><? echo number_format($tot_balance,2); ?></td>
        <td></td>
        </tr>
        </tfoot>
        </table>

        <?
       /*   if(count($purchase_res))
        { */
        	?>
        <table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" style="font-family:Arial Narrow;font-size:18px;">
        <tr align="center">
        <td colspan="10"><b> Comments(Purchase)</b></td>
        </tr>
        <tr align="center">
        <td>Sl</td>
        <td>Po NO</td>
        <td>Ship Date</td>
        <td>Pre-Cost Qty</td>
        <td>Purchase<br>Booking Qty</td>
        <td>Sht.Book Qty</td>
        <td>Smp.Book Qty</td>
        <td>Tot.Book Qty</td>
        <td>Balance</td>
        <td>Comments</td>
        </tr>
        <?
        $cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
        $cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
        if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and a.fab_nature_id='$cbo_fabric_natu'";
        if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and a.fabric_source='$cbo_fabric_source'";
        $paln_cut_qnty_array=return_library_array( "select min(id) as id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown  where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by color_number_id,size_number_id,item_number_id,po_break_down_id", "id", "plan_cut_qnty");
        $item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no ='$txt_job_no'", "gmts_item_id", "set_item_ratio");

        $nameArray=sql_select("select a.id, a.item_number_id, a.costing_per, b.po_break_down_id, b.color_size_table_id, b.requirment, c.po_number FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_po_break_down c WHERE a.job_no=b.job_no and a.job_no=c.job_no_mst and a.id=b.pre_cost_fabric_cost_dtls_id and b.po_break_down_id=c.id and c.job_no_mst = '$txt_job_no'   and   a.fabric_source = '2'  and a.status_active=1 and a.is_deleted=0 order by id");
		//echo "select a.id, a.item_number_id, a.costing_per, b.po_break_down_id, b.color_size_table_id, b.requirment, c.po_number FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_po_break_down c WHERE a.job_no=b.job_no and a.job_no=c.job_no_mst and a.id=b.pre_cost_fabric_cost_dtls_id and b.po_break_down_id=c.id and c.job_no_mst = '$txt_job_no'   and   a.fabric_source = '2'  and a.status_active=1 and a.is_deleted=0 order by id";
		 

        $count=0;
        $tot_grey_req_as_pre_cost_arr=array();
        foreach ($nameArray as $result)
        {
        if (count($nameArray)>0 )
        {
        if($result[csf("costing_per")]==1)
        {
        $tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(12*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
        }
        if($result[csf("costing_per")]==2)
        {
        $tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(1*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
        }
        if($result[csf("costing_per")]==3)
        {
        $tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(24*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
        }
        if($result[csf("costing_per")]==4)
        {
        $tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(36*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
        }
        if($result[csf("costing_per")]==5)
        {
        $tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(48*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
        }
        $tot_grey_req_as_pre_cost_arr[$result[csf("po_number")]]+=$tot_grey_req_as_pre_cost;
        }
        }

        $total_pre_cost=0;
        $total_booking_qnty_main=0;
        $total_booking_qnty_short=0;
        $total_booking_qnty_sample=0;
        $total_tot_bok_qty=0;
        $tot_balance=0;
        $booking_qnty_main=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b, wo_booking_mst c where a.job_no =b.job_no_mst and   a.booking_no =c.booking_no  and a.po_break_down_id =b.id and a.job_no = '$txt_job_no' and a.booking_type =1  and c.fabric_source = 2  and a.is_short=2  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");


        $booking_qnty_short=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.job_no = '$txt_job_no' and a.booking_type =1 and c.fabric_source=2 and c.item_category=2 and a.is_short=1 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
        $booking_qnty_sample=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c  where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.job_no = '$txt_job_no' and a.booking_type =4 and c.fabric_source=2 and c.item_category=2  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
        $sql_data=sql_select( "select max(a.id) as id,  a.po_number,max(a.pub_shipment_date) as pub_shipment_date,sum(a.plan_cut) as plan_cut  from wo_po_break_down a,wo_pre_cost_sum_dtls b,wo_pre_cost_mst c where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no and a.job_no_mst='$txt_job_no' group by a.po_number order by pub_shipment_date asc");
        foreach($sql_data  as $row)
        {
        $col++;
        ?>
        <tr align="center">
        <td><? echo $col; ?></td>
        <td><? echo $row[csf("po_number")]; ?></td>
        <td><? echo change_date_format($row[csf("pub_shipment_date")],"dd-mm-yyyy",'-'); ?></td>
        <td align="right"><? echo number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]],2); $total_pre_cost+=$tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]; ?></td>
        <td align="right"><? echo number_format($booking_qnty_main[$row[csf("id")]],2); $total_booking_qnty_main+=$booking_qnty_main[$row[csf("id")]];?></td>
        <td align="right"><? echo number_format($booking_qnty_short[$row[csf("id")]],2); $total_booking_qnty_short+=$booking_qnty_short[$row[csf("id")]];?></td>
        <td align="right"><? echo number_format($booking_qnty_sample[$row[csf("id")]],2); $total_booking_qnty_sample+=$booking_qnty_sample[$row[csf("id")]];?></td>
        <td align="right"><? $tot_bok_qty=$booking_qnty_main[$row[csf("id")]]+$booking_qnty_short[$row[csf("id")]]+$booking_qnty_sample[$row[csf("id")]]; echo number_format($tot_bok_qty,2); $total_tot_bok_qty+=$tot_bok_qty;?></td>
        <td align="right">
        <? $balance= def_number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]-$tot_bok_qty,2,""); echo number_format($balance,2); $tot_balance+= $balance?>
        </td>
        <td>
        <?
        if( $balance>0)
        {
        echo "Less Booking";
        }
        else if ($balance<0)
        {
        echo "Extra Booking";
        }
        else
        {
        echo "";
        }
        ?>
        </td>
        </tr>
        <?
        }
        ?>
        <tfoot>
        <tr>
        <td colspan="3">Total:</td>
        <td align="right"><? echo number_format($total_pre_cost,2); ?></td>
        <td align="right"><? echo number_format($total_booking_qnty_main,2); ?></td>
        <td align="right"><? echo number_format($total_booking_qnty_short,2); ?></td>
        <td align="right"><? echo number_format($total_booking_qnty_sample,2); ?></td>
        <td align="right"><? echo number_format($total_tot_bok_qty,2); ?></td>
        <td align="right"><? echo number_format($tot_balance,2); ?></td>
        <td></td>
        </tr>
       
        </tfoot>
        </table>
         <?
        	//}
        ?>

		<fieldset id="div_size_color_matrix" style="max-width:1000;">
		<?
		//------------------------------ Query for TNA start-----------------------------------
				$po_id_all=str_replace("'","",$txt_order_no_id);
				$po_num_arr=return_library_array("select id,po_number from wo_po_break_down where id in($po_id_all)",'id','po_number');
				$tna_start_sql=sql_select( "SELECT id,po_number_id, (case when task_number=31 then task_start_date else null end) as fab_booking_start_date, (case when task_number=31 then task_finish_date else null end) as fab_booking_end_date, (case when task_number=60 then task_start_date else null end) as knitting_start_date, (case when task_number=60 then task_finish_date else null end) as knitting_end_date, (case when task_number=61 then task_start_date else null end) as dying_start_date, (case when task_number=61 then task_finish_date else null end) as dying_end_date, (case when task_number=64 then task_start_date else null end) as finishing_start_date, (case when task_number=64 then task_finish_date else null end) as finishing_end_date, (case when task_number=84 then task_start_date else null end) as cutting_start_date, (case when task_number=84 then task_finish_date else null end) as cutting_end_date, (case when task_number=86 then task_start_date else null end) as sewing_start_date, (case when task_number=86 then task_finish_date else null end) as sewing_end_date, (case when task_number=110 then task_start_date else null end) as exfact_start_date, (case when task_number=110 then task_finish_date else null end) as exfact_end_date, (case when task_number=47 then task_start_date else null end) as yarn_rec_start_date, (case when task_number=47 then task_finish_date else null end) as yarn_rec_end_date from tna_process_mst where status_active=1 and po_number_id in($po_id_all)"); 
				$tna_fab_start=$tna_knit_start=$tna_dyeing_start=$tna_fin_start=$tna_cut_start=$tna_sewin_start=$tna_exfact_start="";
				$tna_date_task_arr=array();
				foreach($tna_start_sql as $row)
				{
					if($row[csf("fab_booking_start_date")]!="" && $row[csf("fab_booking_start_date")]!="0000-00-00")
					{
						if($tna_fab_start=="")
						{
							$tna_fab_start=$row[csf("fab_booking_start_date")];
						}
					}


					if($row[csf("knitting_start_date")]!="" && $row[csf("knitting_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['knitting_start_date']=$row[csf("knitting_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['knitting_end_date']=$row[csf("knitting_end_date")];
					}
					if($row[csf("dying_start_date")]!="" && $row[csf("dying_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['dying_start_date']=$row[csf("dying_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['dying_end_date']=$row[csf("dying_end_date")];
					}
					if($row[csf("finishing_start_date")]!="" && $row[csf("finishing_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['finishing_start_date']=$row[csf("finishing_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['finishing_end_date']=$row[csf("finishing_end_date")];
					}
					if($row[csf("cutting_start_date")]!="" && $row[csf("cutting_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_start_date']=$row[csf("cutting_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_end_date']=$row[csf("cutting_end_date")];
					}

					if($row[csf("sewing_start_date")]!="" && $row[csf("sewing_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_start_date']=$row[csf("sewing_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_end_date']=$row[csf("sewing_end_date")];
					}
					if($row[csf("exfact_start_date")]!="" && $row[csf("exfact_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_start_date']=$row[csf("exfact_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_end_date']=$row[csf("exfact_end_date")];
					}
					if($row[csf("yarn_rec_start_date")]!="" && $row[csf("yarn_rec_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_start_date']=$row[csf("yarn_rec_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_end_date']=$row[csf("yarn_rec_end_date")];
					}
				}

			//------------------------------ Query for TNA end-----------------------------------
		?>
        <legend>TNA Information</legend>
        <!--<span style="font-size:180; font-weight:bold;"></span>-->
        <table width="100%" style="border:1px solid black;font-size:17px; font-family:Arial Narrow;" border="1" cellpadding="2" cellspacing="0" rules="all">
            <tr>
            	<td rowspan="2" align="center" valign="top">SL</td>
            	<td width="180" rowspan="2"  align="center" valign="top"><b>Order No</b></td>
                <td colspan="2" align="center" valign="top"><b>Yarn Receive</b></td>
                <td colspan="2" align="center" valign="top"><b>Knitting</b></td>
                <td colspan="2" align="center" valign="top"><b>Dyeing</b></td>
                <td colspan="2" align="center" valign="top"><b>Finish Fabric Prod.</b></td>
                <td colspan="2" align="center" valign="top"><b>Cutting </b></td>
                <td colspan="2" align="center" valign="top"><b>Sewing </b></td>
                <td colspan="2"  align="center" valign="top"><b>Ex-factory </b></td>
            </tr>
            <tr>
            	<td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>

            </tr>
            <?
			$i=1;
			foreach($tna_date_task_arr as $order_id=>$row)
			{
				 //$tna_date_task_arr//knitting_start_date dying_start_date finishing_start_date cutting_start_date sewing_start_date exfact_start_date
				?>
                <tr>
                	<td><? echo $i; ?></td>
                    <td><? echo $po_num_arr[$order_id]; ?></td>
                    <td align="center"><? echo change_date_format($row['yarn_rec_start_date']); ?></td>
                    <td  align="center"><? echo change_date_format($row['yarn_rec_end_date']); ?></td>
                	<td align="center"><? echo change_date_format($row['knitting_start_date']); ?></td>
                    <td  align="center"><? echo change_date_format($row['knitting_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['dying_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['dying_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['finishing_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['finishing_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['cutting_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['cutting_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['sewing_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['sewing_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['exfact_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['exfact_end_date']); ?></td>
                </tr>
                <?
				$i++;
			}
			?>

        </table>
        </fieldset>
        <?
		}// fabric Source End
		if($isYarnPurchseValidate==2)
		{
			?>
			<table align="left" width="350px" style="border:1px solid black;font-size:12px; font-family:Arial Narrow;" border="1" cellspacing="0" rules="all">
				<tr>
					<th colspan="3">Yarn Purchase Requisition Info</th>
				</tr>
				<tr>
					<th width="120">Job No</th>
					<th width="130">Purchase Req. No</th>
					<th>Qty.</th>
				</tr>
                <?
				$sqlYarnReq="select a.requ_no, b.job_no, sum(b.quantity) as qty from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and b.job_no='$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.requ_no, b.job_no";
				$sqlYarnReqRes=sql_select($sqlYarnReq);
				foreach($sqlYarnReqRes as $row)
				{
				?>
                <tr>
					<td width="120"><?=$row[csf("job_no")]; ?></td>
					<td width="130"><?=$row[csf("requ_no")]; ?></td>
					<td align="right"><?=number_format($row[csf("qty")],2); ?></td>
				</tr>
                <?
				}
				?>
        	</table>
            <br>
		<? } echo get_spacial_instruction($txt_booking_no,"97%",118); ?>
        
        <div ><? echo signature_table(1, $cbo_company_name, "1400px",'',70,$inserted_by2);  //signature_table(1, $cbo_company_name, "1400px"); //$user_name_arr[$user_id] ?></div>
		<br>
       <table  class="rpt_table"  border="1" align="left" cellpadding="0" width="1300" cellspacing="0" rules="all" >
        	<thead>
            	<th colspan="16"><b>Job Progress Status: </b></th>
            </thead>
            <?
			$condition= new condition();
			if(str_replace("'","",$job_no) !=''){
				$condition->job_no("='$job_no'");
			}
			//Fabric Booking
			$condition->init();
			$fabric= new fabric($condition);  //echo $fabric->getQuery();
			$fabric_qty=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
			$fabreqQty=0;
			foreach($fabric_qty['knit']['grey'] as $fid=>$fdata)
			{
				foreach($fdata as $fuom=>$fuomdata)
				{
					$fabreqQty+=$fuomdata;
				}
			}
			
			$sqlJobPo=sql_select("select id from wo_po_break_down where job_no_mst='$job_no' and status_active=1 and is_deleted=0");
			$poIds="";
			foreach($sqlJobPo as $row)
			{
				if($poIds=="") $poIds=$row[csf('id')]; else $poIds.=','.$row[csf('id')];
			}
			unset($sqlJobPo);
			
			$sqlFabbooking=sql_select("select sum(grey_fab_qnty) as grey_fab_qnty from wo_booking_dtls where job_no='$job_no' and booking_type=1 and status_active=1 and is_deleted=0");
			$fabbookingQty=$sqlFabbooking[0][csf('grey_fab_qnty')];
			
			$fabbookingPer=($fabbookingQty/$fabreqQty)*100;
			
			//Yarn Allocation
			$sqlYarnAll=sql_select("select sum(qnty) as qnty from inv_material_allocation_dtls where job_no='$job_no' and item_category=1 and status_active=1 and is_deleted=0");
			$yarnAlloQty=$sqlYarnAll[0][csf('qnty')];
			$yarnAlloPer=($yarnAlloQty/$fabreqQty)*100;
			
			$sqlStore="select entry_form, trans_type, quantity from order_wise_pro_details where po_breakdown_id in(".$poIds.") and trans_type in (1,2) and entry_form in (2,3,22,23,58,82,7,66,37,225) and status_active=1";
			$sqlstoreRes=sql_select($sqlStore);
			
			$yarnIssueQty=$knittingFinishQty=$greyRecQty=$finishProdQty=$finishRecQty=0;
			foreach($sqlstoreRes as $row)
			{
				if($row[csf('trans_type')]==2 && $row[csf('entry_form')]==3)
				{
					$yarnIssueQty+=$row[csf('quantity')];
				}
				if($row[csf('trans_type')]==1)
				{
					if($row[csf('entry_form')]==2)
					{
						$knittingFinishQty+=$row[csf('quantity')];
					}
					else if($row[csf('entry_form')]==22 || $row[csf('entry_form')]==23 || $row[csf('entry_form')]==58 || $row[csf('entry_form')]==82)
					{
						$greyRecQty+=$row[csf('quantity')];
					}
					else if($row[csf('entry_form')]==7 || $row[csf('entry_form')]==66)
					{
						$finishProdQty+=$row[csf('quantity')];
					}
					else if($row[csf('entry_form')]==37 || $row[csf('entry_form')]==225)
					{
						$finishRecQty+=$row[csf('quantity')];
					}
				}
			}
			unset($sqlstoreRes);
			
			$yarnIssePer=($yarnIssueQty/$fabreqQty)*100;			//Yarn Issued
			$knittingFinishPer=($knittingFinishQty/$fabreqQty)*100;	//Knitting Finished
			$greyRecPer=($greyRecQty/$fabreqQty)*100;				//Greige Fabric Received
			$finishProdPer=($finishProdQty/$fabreqQty)*100;			//Finished Fabric Production
			$finishRecPer=($finishRecQty/$fabreqQty)*100;			//Finished Fabric Received
			
			
			$sqlGmtsProd="select production_type, embel_name, production_quantity from pro_garments_production_mst where po_break_down_id in(".$poIds.") and production_type in (1,3,5,7,15,11,8) and status_active=1";
			$sqlGmtsProdRes=sql_select($sqlGmtsProd);
			$cutQty=$printQty=$embRecQty=$sewOutQty=$ironQty=$hangTagQty=$polyQty=$packFinQty=0;
			foreach($sqlGmtsProdRes as $row)
			{
				if($row[csf('production_type')]==1)
				{
					$cutQty+=$row[csf('production_quantity')];
				}
				else if($row[csf('production_type')]==3 && $row[csf('embel_name')]==1)
				{
					$printQty+=$row[csf('production_quantity')];
				}
				else if($row[csf('production_type')]==3 && $row[csf('embel_name')]==1)
				{
					$embRecQty+=$row[csf('production_quantity')];
				}
				else if($row[csf('production_type')]==5)
				{
					$sewOutQty+=$row[csf('production_quantity')];
				}
				else if($row[csf('production_type')]==7)
				{
					$ironQty+=$row[csf('production_quantity')];
				}
				else if($row[csf('production_type')]==15)
				{
					$hangTagQty+=$row[csf('production_quantity')];
				}
				else if($row[csf('production_type')]==11)
				{
					$polyQty+=$row[csf('production_quantity')];
				}
				else if($row[csf('production_type')]==8)
				{
					$packFinQty+=$row[csf('production_quantity')];
				}
			}
			unset($sqlGmtsProdRes);
			$cutFinishPer=($cutQty/$jobqtypcs)*100;		//Cutting Finished
			$printRecPer=($printQty/$jobqtypcs)*100;	//Print Received
			$embRecPer=($embRecQty/$jobqtypcs)*100;		//Embroidery Received
			$sewOutPer=($sewOutQty/$jobqtypcs)*100;		//Sewing Completed
			$ironPer=($ironQty/$jobqtypcs)*100;			//Iron Completed
			$hangTagPer=($hangTagQty/$jobqtypcs)*100;	//Hang Tag Completed
			$polyPer=($polyQty/$jobqtypcs)*100;			//Poly Completed
			$packFinishPer=($packFinQty/$jobqtypcs)*100;//Packing Finishing Completed
			
			//Ex-factory Completed
			$sqlExFactory=return_field_value("sum(ex_factory_qnty) as exqty", "pro_ex_factory_mst", "po_break_down_id in(".$poIds.") and status_active=1", "exqty");
			$exFactoryPer=($sqlExFactory/$jobqtypcs)*100;
			
			?>
			<tr>
            	<td width="125"><b>Fabric Booking</b></td><td align="center" width="40"><?=number_format($fabbookingPer,2); ?></td>
                <td width="125"><b>Yarn Allocation</b></td><td align="center" width="40"><?=number_format($yarnAlloPer,2); ?></td>
                <td width="125"><b>Yarn Issued</b></td><td align="center" width="40"><?=number_format($yarnIssePer,2); ?></td>
                <td width="125"><b>Knitting Finished</b></td><td align="center" width="40"><?=number_format($knittingFinishPer,2); ?></td>
                <td width="125"><b>Greige Fabric Received</b></td><td align="center" width="40"><?=number_format($greyRecPer,2); ?></td>
                <td width="125"><b>Dye-Fin. Completed</b></td><td align="center" width="40"><?=number_format($finishProdPer,2); ?></td>
                <td width="125"><b>Finished Fabric Received</b></td><td align="center" width="40"><?=number_format($finishRecPer,2); ?></td>
                <td width="125"><b>Cutting Finished</b></td><td align="center"><?=number_format($cutFinishPer,2); ?></td>
            </tr>
            <tr>
            	<td><b>Print Received</b></td><td align="center"><?=number_format($printRecPer,2); ?></td>
            	<td><b>Embroidery Received</b></td><td align="center"><?=number_format($embRecPer,2); ?></td>
            	<td><b>Sewing Completed</b></td><td align="center"><?=number_format($sewOutPer,2); ?></td>
                <td><b>Iron Completed</b></td><td align="center"><?=number_format($ironPer,2); ?></td>
                <td><b>Hang Tag Completed</b></td><td align="center"><?=number_format($hangTagPer,2); ?></td>
                <td><b>Poly Completed</b></td><td align="center"><?=number_format($polyPer,2); ?></td>
                <td><b>Packing Finishing Completed</b></td><td align="center"><?=number_format($packFinishPer,2); ?></td>
                <td><b>Ex-factory Completed</b></td><td align="center"><?=number_format($exFactoryPer,2); ?></td>
            </tr>
        </table>

		
		<div >
                    <fieldset id="div_size_color_matrix" >
                        <legend>Actual PO Information</legend>
                        <table  class="rpt_table"  border="1" align="left" cellpadding="0"  cellspacing="0" rules="all" >
                            <tr>
                            	<th>PO Number</th>                            
                            	<th>Actual PO</th>
								<th>PO Recieve Date</th>
								<th>Ship Date</th>                           	
                                <th>GMTS Items</th>
                                <th >Gmts  Color</th>
								<th>Gmts Size</th>
                                <th>Total Order Qty(pcs)</th>
                            </tr>
			<?php
			$colorLibArr=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
			$sizeLibArr=return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");

					$ac_sql="select  b.po_number,a.acc_po_no, a.country_id, a.gmts_item, a.gmts_color_id,a.acc_po_qty as qnty, a.acc_ship_date,a.po_break_down_id,b.po_received_date,a.gmts_size_id
					from wo_po_acc_po_info a,wo_po_break_down b where  b.id=a.po_break_down_id and a.po_break_down_id=60252 and a.job_no='FAL-22-00707' and a.status_active=1 and a.is_deleted=0 ";
					$ac_po_data=sql_select($ac_sql);


					foreach($ac_po_data as $val){?>

						<tr>
							<td><?=$val[csf('po_number')];?></td>
							<td><?=$val[csf('acc_po_no')];?></td>
							<td><?=$val[csf('po_received_date')];?></td>
							<td><?=$val[csf('acc_ship_date')];?></td>
							<td><?=$garments_item[$val[csf('gmts_item')]];?></td>
							<td align="center"><?=$colorLibArr[$val[csf('gmts_color_id')]];?></td>
							<td align="center"><?=$sizeLibArr[$val[csf('gmts_size_id')]];?></td>
							<td align="center"><?=$val[csf('qnty')];?></td>
						</tr>


					<?}	?>


		                </table>
					
				</fieldset>


		</div >







        <?


					$item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no ='$job_no'", "gmts_item_id", "set_item_ratio");
					$po_number_arr=return_library_array( "select id,po_number from wo_po_break_down  where job_no_mst ='$job_no'", "id", "po_number");
					$shipment_date_arr=return_library_array( "select id,shipment_date from wo_po_break_down  where job_no_mst ='$job_no'", "id", "shipment_date");
        	$grand_order_total=0; $grand_plan_total=0; $size_wise_total=array();
			$nameArray_size=sql_select( "select size_number_id,size_order from wo_po_color_size_breakdown where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by size_number_id,size_order order by size_order ");



			
			$booking_dtls_sql="SELECT a.id as booking_dtls_id, b.id, a.fabric_color_id, a.fin_fab_qnty, a.grey_fab_qnty, a.amount, a.rate, a.colar_cuff_per  from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_no=$txt_booking_no and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			$booking_dtls_res=sql_select($booking_dtls_sql);
			
			$booking_dtls_id_array=array(); $fabric_color_array=array(); $finish_fabric_qnty_array=array(); $grey_fabric_qnty_array=array(); $grey_fabric_amount_array=array(); $grey_fabric_rate_array=array(); $colar_cuff_percent_array=array();
	
			foreach($booking_dtls_res as $row)
			{
				$booking_dtls_id_array[$row[csf("id")]]=$row[csf("booking_dtls_id")];
				//$job_no=$row[csf("job_no")];
				$fabric_color_array[$row[csf("id")]]=$row[csf("fabric_color_id")];
				$finish_fabric_qnty_array[$row[csf("id")]]+=$row[csf("fin_fab_qnty")];
				$grey_fabric_qnty_array[$row[csf("id")]]+=$row[csf("grey_fab_qnty")];
				$grey_fabric_amount_array[$row[csf("id")]]=$row[csf("amount")];
				$grey_fabric_rate_array[$row[csf("id")]]['rate']=$row[csf("rate")];
				$grey_fabric_rate_array[$row[csf("id")]]['colar_cuff_per']=$row[csf("colar_cuff_per")];
			}
			unset($booking_dtls_res);
		

			$name_sql="select a.id as pre_cost_fabric_cost_dtls_id, a.job_no, a.item_number_id, a.body_part_id, a.fab_nature_id, a.fabric_source, a.color_type_id, a.gsm_weight, a.construction, a.composition, a.color_size_sensitive, a.costing_per, a.color, a.color_break_down, a.rate as rate_mst, b.id, b.po_break_down_id, b.color_size_table_id, b.color_number_id, b.gmts_sizes as size_number_id, b.dia_width, b.item_size, b.cons, b.process_loss_percent, b.requirment, b.rate, b.pcs, b.remarks FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b
			WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and a.job_no='$job_no' and b.po_break_down_id in(".str_replace("'","",$txt_order_no_id).")   and a.status_active=1 and a.is_deleted=0 order by a.id,b.color_size_table_id";
			
	
			$nameArray=sql_select($name_sql);

			$po_fabric_wise_data=array();
			foreach ($nameArray as $result){

								if($finish_fabric_qnty_array[$result[csf("id")]]>0  || $grey_fabric_qnty_array[$result[csf("id")]]> 0  )
								{

									$ship_date=change_date_format($shipment_date_arr[$result[csf('po_break_down_id')]]);

									$po_fabric_wise_data[$result[csf('po_break_down_id')]][$ship_date][$result[csf('color_number_id')]]['finish_kg']+=$finish_fabric_qnty_array[$result[csf("id")]];
									$po_fabric_wise_data[$result[csf('po_break_down_id')]][$ship_date][$result[csf('color_number_id')]]['grey_kg']+=$grey_fabric_qnty_array[$result[csf("id")]];
									$po_fabric_wise_data[$result[csf('po_break_down_id')]][$ship_date][$result[csf('color_number_id')]]['process_loss']=$result[csf('process_loss_percent')];
									$po_fabric_wise_data[$result[csf('po_break_down_id')]][$ship_date][$result[csf('color_number_id')]]['color_size_sensitive']=$result[csf('color_size_sensitive')];
									$po_fabric_wise_data[$result[csf('po_break_down_id')]][$ship_date][$result[csf('color_number_id')]]['id']=$result[csf('id')];
							
								}
			  }

			?>
                <div id="div_size_color_matrix" class="pagebreak">
                    <fieldset id="div_size_color_matrix" >
                        <legend>Size and Color Breakdown</legend>
						<table  class="rpt_table"  border="1" align="left" style="float:left;" cellpadding="0"  cellspacing="0" rules="all" >
                            <tr>
                            	<td>PO Number</td>
                            
                            	<td>Ship Date</td>
								<td>fabric color</td>
								<td>Body Color</td>                           	
                                <td  align="center"> Total Finish Fabric(kg)</td>
                                <td  align="center"> Total Grey Fabric(kg)</td>
                                <td  align="center"> Process Loss</td>
                            </tr>
                            <?
                           
							foreach ($po_fabric_wise_data as $po_id=>$shipdate_data){
								foreach ($shipdate_data as $date_id=>$color_data) {
									foreach ($color_data as  $color_id=>$result){

								?>
								<tr>
									<td title="<?=$po_id;?>"><?php echo $po_number_arr[$po_id]; ?></td>
									
									<td><?php echo $date_id; ?></td>									
									<td><? if($result["color_size_sensitive"]!=0) echo $color_library[$color_id]; ?></td>									
									<td><?php
									
									$type=1;
									$color_id="";
									if($type==1)
									{
										echo $color_library[$fabric_color_array[$result["id"]]];
										$color_id=$fabric_color_array[$result["id"]];
									}
									else if($type==2)
									{
										if($result["color_size_sensitive"]==3)
										{
											echo $constrast_color_arr[$result["color_number_id"]]; $color_id=$contrastcolor_arr[$result["job_no"]][$result["pre_cost_fabric_cost_dtls_id"]][$result["color_number_id"]];
										}
										else if($result["color_size_sensitive"]==0)
										{
											echo $color_library[$result["color"]]; $color_id=$result["color"];
										}
										else
										{
											echo $color_library[$result["color_number_id"]]; $color_id=$result["color_number_id"];
										}
									}
									 ?></td>
									<?

                                	$grand_fabric_total+=$result["finish_kg"];
        							$grand_grey_total+=$result["grey_kg"];

                                    $po_fabric_tot[$po_id]+=$result["finish_kg"];
        							$po_grey_tot[$po_id]+=$result["grey_kg"];
                                    $color_wise_arr[$color_id]['finish_kg']+=$result["finish_kg"];
                                    $color_wise_arr[$color_id]['grey_kg']+=$result["grey_kg"];

        			

                                	?>
                                	<td align="center"><?php echo number_format($result["finish_kg"],2); ?></td>
                                	<td align="center"><?php echo number_format($result["grey_kg"],2); ?></td>
                                	<td align="center"><?php echo number_format($result['process_loss']); ?></td>
								</tr>
								<?
							     }
						      }?>

                            <tr>
                            	<td align="right" colspan="4"><b>Po Wise Total</b></td>                            	
                                <td align="center"><strong><?=number_format($po_fabric_tot[$po_id],2)?></strong></td>                                
                                <td align="center"><strong><?=number_format($po_grey_tot[$po_id],2)?></strong></td>
								<td></td>
                            </tr>
                            <?

							}
                            ?>
                            <tr>
                            	<td align="right" colspan="4"><b>Grand Total</b></td>                            	
                                <td align="center"><strong><?=number_format($grand_fabric_total,2)?></strong></td>                                
                                <td align="center"><strong><?=number_format($grand_grey_total,2)?></strong></td>
								<td></td>
                            </tr>
                        </table>
                        <table  class="rpt_table"  style="float:left;margin-left:5px;" border="1" align="left" cellpadding="0"  cellspacing="0" rules="all" >
                            <tr>
                                <td colspan="4" align="center">Color wise Summary</td>                               
                            </tr>
                            <tr>
                                <td>Sl</td>
                                <td>Color Name</td>
                                <td>Finish Fabric(kg)</td>
                                <td>Grey Fabric(kg)</td>
                            </tr>
                            <?php
                            $sl=1;
                                foreach($color_wise_arr as $cid=> $val){
                            ?>
                            <tr>
                                <td width="30"><?=$sl;?></td>
                                <td width="100"><?=$color_library[$cid];?></td>
                                <td width="100" align="right"><?=number_format($val['finish_kg'],2);?></td>
                                <td width="100" align="right"><?=number_format($val['grey_kg'],2);?></td>
                            </tr>
                            <?$sl++;
                        
                                    $grand_fabric_tot+=$val['finish_kg'];
        							$grand_grey_tot+=$val['grey_kg'];
                        }?>
                            <tr>
                              
                                <td colspan="2">Total</td>
                                <td align="right"><?=number_format($grand_fabric_tot,2);?></td>
                                <td align="right"><?=number_format($grand_grey_tot,2);?></td>
                            </tr>
                        </table>
                    </fieldset>
                </div>
			<?

			$actule_po_size=sql_select("select gmts_size_id from wo_po_acc_po_info where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by gmts_size_id ");
			$actule_po_data=sql_select( "SELECT a.id as po_id,a.po_number, b.acc_po_no, a.po_received_date, b.acc_ship_date, b.gmts_color_id, b.gmts_size_id, b.acc_po_qty, b.id as actule_po_id , b.gmts_item from wo_po_break_down a join wo_po_acc_po_info b on a.id=b.PO_BREAK_DOWN_ID where b.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and b.is_deleted=0 and b.status_active=1 and a.is_deleted=0 and a.status_active=1");
			$actule_po_arr=array();
			$attribute=array('po_number','acc_po_no','po_received_date','acc_ship_date','gmts_color_id','gmts_size_id','acc_po_qty','gmts_item');
			foreach ($actule_po_data as $row) {
				foreach ($attribute as $attr) {
					$actule_po_arr[$row[csf('po_id')]][$row[csf('actule_po_id')]][$attr]=$row[csf($attr)];
				}
				$actual_color_size[$row[csf('po_id')]][$row[csf('actule_po_id')]][$row[csf('gmts_item')]][$row[csf('gmts_color_id')]][$row[csf('gmts_size_id')]] =$row[csf('acc_po_qty')];				
			}

			$booking_dtls_data=sql_select(" SELECT a.po_number, a.pub_shipment_date,b.fabric_color_id,b.gmts_color_id, b.fin_fab_qnty, b.grey_fab_qnty, c.size_number_id,c.excess_cut_perc from wo_po_break_down a join wo_booking_dtls b on a.id=b.po_break_down_id join WO_PO_COLOR_SIZE_BREAKDOWN c on c.id=b.color_size_table_id where b.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
			$size_lib_arr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		
		?>
		    
       </div>
       <?
	$emailBody=ob_get_contents();
	//ob_clean();
	if($is_mail_send==1){
		/*$req_approved=return_field_value("id", "ELECTRONIC_APPROVAL_SETUP", "PAGE_ID = 336 and is_deleted=0");
		$is_approved=return_field_value("IS_APPROVED", "WO_BOOKING_MST", "STATUS_ACTIVE = 1 and is_deleted=0 and booking_no='$txt_booking_no'");
		if($req_approved && $is_approved==1){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Approved</h1>";
		}
		elseif($req_approved && $is_approved==0){
			$emailBody.="<h1 style='border:1px sloid #0ff;'>Draft</h1>";
		}
	*/		
		
		$sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=87 and b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name";
		$mail_sql_res=sql_select($sql);
		
		$mailArr=array();
		foreach($mail_sql_res as $row)
		{
			$mailArr[$row[EMAIL]]=$row[EMAIL]; 
		}
		
		$supplier_id=$nameArray[0][csf('supplier_id')];
		$supplier_mail=return_field_value("email", "lib_supplier", "status_active=1 and is_deleted=0 and id=$supplier_id ");

		
		$mailArr=array();
		if($mail_id!=''){$mailArr[]=$mail_id;}
		if($supplier_mail_arr[$supplier_id]!=''){$mailArr[]=$supplier_mail;}
		
		$to=implode(',',$mailArr);
		$subject="Fabric Booking Auto Mail";
		
		if($to!=""){
			require '../../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
			require_once('../../../auto_mail/setting/mail_setting.php');
			$header=mailHeader();
			sendMailMailer( $to, $subject, $emailBody );
		}
	}
	exit();
}



function send_final_app_notification($sys_id){

	require_once('../../mailer/class.phpmailer.php');
	require_once('../../auto_mail/setting/mail_setting.php');
	
	$bookingSql = "select ID,BOOKING_NO,COMPANY_ID,INSERTED_BY from WO_BOOKING_MST where id in($sys_id)";
	$bookingSqlRes=sql_select($bookingSql);
	$booking_data_arr=array();
	foreach($bookingSqlRes as $row)
	{
		$booking_data_arr['INSERTED_BY'][$row['INSERTED_BY']]=$row['INSERTED_BY'];

		$electronicSql = "select a.USER_EMAIL from USER_PASSWD a,ELECTRONIC_APPROVAL_SETUP b where a.id=b.USER_ID and b.ENTRY_FORM = 7 AND b.IS_DELETED = 0 AND a.IS_DELETED = 0 AND b.COMPANY_ID = {$row['COMPANY_ID']} and a.USER_EMAIL is not null
		UNION ALL
		select a.USER_EMAIL from USER_PASSWD a where a.IS_DELETED = 0 and a.USER_EMAIL is not null and a.id={$row['INSERTED_BY']} 
		";
		$electronicSqlRes=sql_select($electronicSql);
		$mail_arr=array();
		foreach($electronicSqlRes as $row)
		{
			$mail_arr[$row['USER_EMAIL']]=$row['USER_EMAIL'];
		}

		$message="Booking no: ".$row['BOOKING_NO']." is approverd.".$mail_body;
		$to = implode(',',$mail_arr);
		$subject = "Fabric booking full approved notification.";
		$header=mailHeader();
		if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail);

	}
}

?>