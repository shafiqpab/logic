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

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );

if ($action=="load_drop_down_buyer")
{
    if($data != 0)
    {
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
    }
    else{
        echo create_drop_down( "cbo_buyer_name", 152, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
        exit();
    }
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

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$sequence_no='';
	$company_name=str_replace("'","",$cbo_company_name);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$txt_quotation_no=str_replace("'","",$txt_quotation_no);
	$txt_mkt_no=str_replace("'","",$txt_mkt_no);
	if ($txt_quotation_no=="") $quotation_cond=""; else $quotation_cond =" and a.id =$txt_quotation_no";
	if ($txt_mkt_no=="") $mkt_no_cond=""; else $mkt_no_cond =" and a.mkt_no='$txt_mkt_no'";

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
		else
		{
			$buyer_id_cond=" and a.buyer_id=$cbo_buyer_name";
		}
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
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond=" and a.buyer_id=$cbo_buyer_name";
		}
	}
	//echo $buyer_id_cond."**";die;
	$date_cond='';
	if(str_replace("'","",$txt_date)!="")
	{
		if(str_replace("'","",$cbo_get_upto)==1) $date_cond=" and a.quot_date>$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==2) $date_cond=" and a.quot_date<=$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==3) $date_cond=" and a.quot_date=$txt_date";
		else $date_cond='';
	}

	$approval_type=str_replace("'","",$cbo_approval_type);
	if($previous_approved==1 && $approval_type==1)
	{
		$previous_approved_type=1;
	}

	
	//$user_id=3;
	//$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
	//$job_dealing_merchant_array = return_library_array("select job_no, dealing_marchant from wo_po_details_master","job_no","dealing_marchant");

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");
	$buyer_ids_array = array();
	$buyerData = sql_select("select user_id, sequence_no, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0 and bypass=2");//echo "select user_id, sequence_no, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0 and bypass=2";
	foreach($buyerData as $row)
	{
		$buyer_ids_array[$row[csf('user_id')]]['u']=$row[csf('buyer_id')];
		$buyer_ids_array[$row[csf('sequence_no')]]['s']=$row[csf('buyer_id')];
	}

	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Price Quotation.</font>";
		die;
	}
	if($previous_approved==1 && $approval_type==1)
	{
		$buyer_ids=$buyer_ids_array[$user_id]['u'];
		if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";

		$sequence_no_cond=" and b.sequence_no<'$user_sequence_no'";
		$sql="SELECT a.id,  a.company_id, d.margin_dzn_percent, d.costing_per_id, d.price_with_commn_dzn, d.commission, d.total_cost, a.buyer_id, a.style_ref, a.style_desc, a.quot_date, a.est_ship_date, a.approved, a.inserted_by, a.mkt_no, b.id as approval_id, a.garments_nature
		from wo_price_quotation a, approval_history b, wo_price_quotation_costing_mst d
		where a.id=b.mst_id and a.id=b.quotation_id and b.entry_form=10 and a.company_id=$company_name and a.status_active=1 and a.is_deleted=0 and a.ready_to_approved=1 and b.current_approval_status=1 and a.approved in(1,3) and b.approved_by!=$user_id $buyer_id_cond2 $sequence_no_cond $date_cond $quotation_cond $mkt_no_cond
		group by a.id,  a.company_id, d.margin_dzn_percent, d.costing_per_id, d.price_with_commn_dzn, d.commission, d.total_cost, a.buyer_id, a.style_ref, a.style_desc, a.quot_date, a.est_ship_date, a.approved, a.inserted_by, a.mkt_no, b.id, a.garments_nature
		order by a.id ASC";
		//$buyer_id_cond

	}
	else if($approval_type==0)
	{

		//$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted=0");

		if($db_type==0)
		{
			$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass = 2 and buyer_id='' and is_deleted=0","seq");
		}
		else
		{
			$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
		}

		if($user_sequence_no==$min_sequence_no)
		{

			$buyer_ids = $buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
			//$sql="select a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type $buyer_id_cond $date_cond group by a.id";

		$sql="SELECT a.id,  a.company_id, b.margin_dzn_percent, b.costing_per_id, b.price_with_commn_dzn, b.commission, b.total_cost, a.buyer_id, a.style_ref, a.style_desc, a.quot_date,a.est_ship_date, '0' as approval_id,  a.approved, a.inserted_by, a.garments_nature, a.mkt_no  from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.company_id=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved=$approval_type $buyer_id_cond $buyer_id_cond2 $date_cond $quotation_cond $mkt_no_cond group by a.id,  a.company_id, b.margin_dzn_percent, b.costing_per_id, b.price_with_commn_dzn, b.commission, b.total_cost, a.buyer_id , a.garments_nature, a.style_ref, a.style_desc, a.quot_date, a.est_ship_date, a.approved, a.inserted_by, a.mkt_no order by a.id ASC";
		//echo $sql;//die;
		}
		else if($sequence_no=="")
		{

			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";

			$seqSql="select sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
			//echo $seqSql;die;
			$seqData=sql_select($seqSql);

			//$sequence_no_by=$seqData[0][csf('sequence_no_by')];
			//$buyerIds=$seqData[0][csf('buyer_ids')];

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
				}
				else
				{
					$sequence_no_by_yes.=$sRow[csf('sequence_no')].",";
				}
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
			$sequence_no_by_no=chop($sequence_no_by_no,',');
			$sequence_no_by_yes=chop($sequence_no_by_yes,',');

			if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
			if($sequence_no_by_no=="") $sequence_no_by_no=0;

			$quotation_id='';
			$quotation_id_sql="select distinct (mst_id) as quotation_id from wo_price_quotation a, approval_history b where a.id=b.mst_id and a.company_id=$company_name  and b.sequence_no in ($sequence_no_by_no) and b.entry_form=10 and b.current_approval_status=1 $buyer_id_cond $date_cond $seqCond
			union
			select distinct (mst_id) as quotation_id from wo_price_quotation a, approval_history b where a.id=b.mst_id and a.company_id=$company_name and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=10 and b.current_approval_status=1 $buyer_id_cond $date_cond";
			$bResult=sql_select($quotation_id_sql);
			foreach($bResult as $bRow)
			{
				$quotation_id.=$bRow[csf('quotation_id')].",";
			}

			$quotation_id=chop($quotation_id,',');


			$quotation_id_app_sql=sql_select(" select mst_id as quotation_id from wo_price_quotation a, approval_history b where a.id=b.mst_id and a.company_id=$company_name  and b.sequence_no=$user_sequence_no and b.entry_form=10 and b.current_approval_status=1");

			foreach($quotation_id_app_sql as $inf)
			{
				if($quotation_id_app_byuser!="") $quotation_id_app_byuser.=",".$inf[csf('pre_cost_id')];
				else $quotation_id_app_byuser.=$inf[csf('quotation_id')];
			}

			$quotation_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
			$quotation_id_app_byuser=chop($quotation_id_app_byuser,',');
			$result=array_diff(explode(',',$quotation_id),explode(',',$quotation_id_app_byuser));
			$quotation_id=implode(",",$result);

			$quotation_id_cond="";
			if($quotation_id_app_byuser!="")
			{
				$quotation_id_app_byuser_arr=explode(",",$quotation_id_app_byuser);
				if( count($quotation_id_app_byuser_arr)>999)
				{
					$quotation_id_chunk_arr=array_chunk($quotation_id_app_byuser_arr,999) ;
					foreach($quotation_id_chunk_arr as $chunk_arr)
					{
						$chunk_arr_value=implode(",",$chunk_arr);
						$quotation_id_cond.=" and a.id not in($chunk_arr_value)";
					}

				}
				else
				{
					$quotation_id_cond=" and a.id not in($quotation_id_app_byuser)";
				}
			}
			else $quotation_id_cond="";

			if($quotation_id!="")
			{
				$sql="SELECT x.* from (select a.id, a.company_id, b.margin_dzn_percent, b.costing_per_id, b.price_with_commn_dzn, b.commission, b.total_cost, a.buyer_id, a.style_ref, a.style_desc, a.quot_date,a.est_ship_date, '0' as approval_id, a.approved, a.inserted_by, a.garments_nature, a.mkt_no from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.company_id=$company_name  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved=$approval_type $quotation_id_cond $buyerIds_cond $buyer_id_cond $buyer_id_cond2 $date_cond $quotation_cond $mkt_no_cond group by a.id, a.company_id, b.margin_dzn_percent, b.costing_per_id, b.price_with_commn_dzn, b.commission, b.total_cost, a.buyer_id, a.garments_nature, a.style_ref, a.style_desc, a.quot_date,a.est_ship_date, a.approved, a.inserted_by, a.mkt_no
				UNION ALL
				SELECT a.id, a.company_id, b.margin_dzn_percent, b.costing_per_id, b.price_with_commn_dzn, b.commission, b.total_cost, a.buyer_id, a.style_ref, a.style_desc, a.quot_date,a.est_ship_date, '0' as approval_id, a.approved, a.inserted_by, a.garments_nature, a.mkt_no from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.company_id=$company_name  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved in (1,3) and (a.id in($quotation_id))  $buyer_id_cond $buyer_id_cond2 $date_cond $quotation_cond $mkt_no_cond group by a.id, a.company_id, b.margin_dzn_percent, b.costing_per_id, b.price_with_commn_dzn, b.commission, b.total_cost,a.buyer_id,a.garments_nature, a.style_ref, a.style_desc, a.quot_date,a.est_ship_date, a.approved , a.inserted_by, a.mkt_no ) x order by x.id ";

			}
			else
			{
				$sql="SELECT a.id, a.company_id, b.margin_dzn_percent, b.costing_per_id, b.price_with_commn_dzn, b.commission, b.total_cost, a.buyer_id, a.style_ref, a.style_desc, a.quot_date,a.est_ship_date, '0' as approval_id, a.approved, a.inserted_by, a.garments_nature, a.mkt_no from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.company_id=$company_name  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved=$approval_type  $quotation_id_cond $buyerIds_cond $buyer_id_cond $buyer_id_cond2  $date_cond $quotation_cond $mkt_no_cond group by a.id, a.company_id, b.margin_dzn_percent, b.costing_per_id, b.price_with_commn_dzn, b.commission, b.total_cost, a.buyer_id, a.garments_nature, a.style_ref, a.style_desc, a.quot_date,a.est_ship_date, a.approved , a.inserted_by, a.mkt_no order by a.id ASC";
			}
			//echo $sql;
		}
		else
		{

			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
			$user_sequence_no=$user_sequence_no-1;
			//$sequence_no_min=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted=0");
			if($sequence_no==$user_sequence_no) $sequence_no_by_pass='';
			else
			{
				if($db_type==0)
				{
					$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");
				}
				else
				{
					$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");
				}
			}

			if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
			else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";

			$sql="SELECT a.id,  a.company_id,  a.buyer_id, d.margin_dzn_percent, d.costing_per_id, d.price_with_commn_dzn, d.commission, d.total_cost, a.style_ref, a.style_desc, a.quot_date,a.est_ship_date, a.approved,a.inserted_by,b.approved_date, b.id as approval_id, a.garments_nature, a.mkt_no from wo_price_quotation a, approval_history b, wo_price_quotation_costing_mst d where a.id=b.mst_id and a.id=d.quotation_id and b.entry_form=10 and a.company_id=$company_name  and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.ready_to_approved=1 and  a.approved in(1,3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $date_cond $quotation_cond $mkt_no_cond group by a.id, d.margin_dzn_percent, d.costing_per_id, d.price_with_commn_dzn, d.commission, d.total_cost, a.company_id,  a.buyer_id,  a.style_ref, a.style_desc, a.quot_date,a.est_ship_date, a.approved,a.inserted_by,b.approved_date, b.id, a.garments_nature, a.mkt_no order by a.id ASC";
		}
		//echo $sql;
	}
	else
	{
		$buyer_ids=$buyer_ids_array[$user_id]['u'];
		if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";

		$sequence_no_cond=" and b.sequence_no='$user_sequence_no'";
		if($unapproved_request == 1)
		{
			$sql="SELECT a.id, a.company_id,d.margin_dzn_percent, d.costing_per_id, d.price_with_commn_dzn, d.commission, d.total_cost, a.buyer_id, a.style_ref, a.style_desc, a.quot_date,a.est_ship_date, a.approved, a.inserted_by,b.approved_date, b.id as approval_id, a.garments_nature, a.mkt_no from wo_price_quotation a, approval_history b,wo_price_quotation_costing_mst d fabric_booking_approval_cause c where a.id=b.mst_id and a.id=d.quotation_id and a.id=c.booking_id and b.entry_form=10 and a.company_id=$company_name and a.status_active=1 and a.is_deleted=0 and a.ready_to_approved=1 and b.current_approval_status=1 and a.approved in(1,3) and c.entry_form=10 and c.approval_type=2 and c.is_deleted=0 and c.status_active=1 $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $date_cond $quotation_cond $mkt_no_cond order by a.id ASC";
		}
		else
		{
			$sql="SELECT a.id, a.company_id, c.margin_dzn_percent, c.costing_per_id, c.price_with_commn_dzn, c.commission, c.total_cost, a.buyer_id, a.style_ref, a.style_desc, a.quot_date, a.est_ship_date, a.approved, a.inserted_by,b.approved_date, b.id as approval_id, a.garments_nature, a.mkt_no from wo_price_quotation a, approval_history b,wo_price_quotation_costing_mst c where a.id=b.mst_id and a.id=c.quotation_id and b.entry_form=10 and a.company_id=$company_name and a.status_active=1 and a.is_deleted=0 and a.ready_to_approved=1 and b.current_approval_status=1 and a.approved in(1,3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $date_cond $quotation_cond $mkt_no_cond order by a.id ASC";
		}

	}
     //echo $sql;die();

	$sql_unapproved=sql_select("select * from fabric_booking_approval_cause where  entry_form=10 and approval_type=2 and is_deleted=0 and status_active=1");
	$unapproved_request_arr=array();
	foreach($sql_unapproved as $rowu)
	{
		$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
	}

	$tbl_width = ($approval_type==1) ? 1110 : 1000;
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:<? echo $tbl_width+20;?>px; margin-top:10px">
        <legend>Price Quotation Approval</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width;?>" class="rpt_table" >
                <thead>
                	<th width="30"></th>
                    <th width="40">SL</th>
                    <th width="70">Quotation No</th>
                    <th width="60">Mkt_no</th>
                    <th width="100">Buyer</th>
                    <th width="100">Style Ref.</th>
                    <th width="80">Quotation Date</th>
                    <th width="80">Est. Ship Date</th>
                    <th width="50">Image</th>
                    <th width="80">Margin%</th>
                    <th width="80">Approved Date</th>
                    <? if($approval_type==1){?>
                    <th width="100">Insert By</th>
                    <th width="150">Unapproved Request</th>
                    <th width=""><input type="checkbox" name="copy_basis" id="copy_basis"/>Un-approved Reason</th>
                    <?}else{?>
                    <th width="80">Refusing Cause</th>
                    <th width="">Insert By</th>
                    <? } ?>
                </thead>
            </table>
            <div style="width:<? echo $tbl_width;?>px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width+38;?>" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
							//echo $sql; die;
                            $i=1;
							$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$company_name." and module_id=2 and report_id=32 and is_deleted=0 and status_active=1");
							$report_id = explode(',', $print_report_format);
                            $nameArray=sql_select( $sql );
                            foreach ($nameArray as $row)
                            {
								if ($i%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";

								$value=$row[csf('id')];
								$gmt_nature=$row[csf('garments_nature')];
								$date = change_date_format($row[csf("approved_date")]);
								
								$quot_date=$row[csf("quot_date")];
								$txt_quotation_date=change_date_format($quot_date, "yyyy-mm-dd", "-",1)	;	
								$asking_profit=return_field_value("asking_profit", "lib_standard_cm_entry", "company_id=$company_name  and '$txt_quotation_date' between applying_period_date and applying_period_to_date  and status_active=1 and is_deleted=0");
								if($asking_profit=="") $asking_profit=0;
								$costing_per_id=$row[csf("costing_per_id")];
								$final_cost_dzn=$row[csf("total_cost")];
						
								if($costing_per_id==1) { $final_cost_psc=$final_cost_dzn/12; $order_price_per_dzn=12; }
								else if($costing_per_id==2) { $final_cost_psc=$final_cost_dzn/1; $order_price_per_dzn=1; }
								else if($costing_per_id==3) { $final_cost_psc=$final_cost_dzn/(2*12); $order_price_per_dzn=24; }
								else if($costing_per_id==4) { $final_cost_psc=$final_cost_dzn/(3*12); $order_price_per_dzn=36; }
								else if($costing_per_id==5) { $final_cost_psc=$final_cost_dzn/(4*12); $order_price_per_dzn=48; }
								$margin_method=1-($asking_profit/100);
								$net_asking_profit=($final_cost_psc/$margin_method)-$final_cost_psc;
								$net_asking_profit=$net_asking_profit*$order_price_per_dzn;
								$cost_dzn=$final_cost_dzn+$net_asking_profit+$row[csf("commission")];
								$margin_dzn=$row[csf("price_with_commn_dzn")]-$cost_dzn;
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" align="center">
                                	<td width="30" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="booking_id_<? echo $i;?>" name="booking_id[]" type="hidden" value="<? echo $value; ?>" />
                                        <input id="booking_no_<? echo $i;?>" name="booking_no]" type="hidden" value="<? echo $row[csf('id')]; ?>" />
                                        <input id="approval_id_<? echo $i;?>" name="approval_id[]" type="hidden" value="<? echo $row[csf('approval_id')]; ?>" />
                                        <input id="<? echo strtoupper($row[csf('id')]); ?>" name="no_quot[]" type="hidden" value="<? echo $i;?>" />
                                    </td>
									<td width="40" align="center"><? echo $i; ?></td>
									<?

										//$report_name=$report_format[$report_id['0']];
									
										if($report_id[0] == 90){
											$quotation_rep = 'preCostRpt';
										}
										elseif ($report_id[0] == 91) {
											$quotation_rep = 'preCostRpt2';
										}
										elseif ($report_id[0] == 92) {
											$quotation_rep = 'preCostRpt3';
										}
										elseif ($report_id[0] == 194) {
											$quotation_rep = 'preCostRpt4'; //Quotation Woven
										}
										elseif ($report_id[0] == 219) {
											$quotation_rep = 'preCostRpt4'; //  quotation summery
										}
										elseif ($report_id[0] == 239) {
											$quotation_rep = 'summary2'; //  quotation summery 2
										}
										elseif ($report_id[0] == 213) {
											$quotation_rep = 'preCostRpt5';  //Quotation Woven 2
										}
										elseif ($report_id[0] == 217) {
											$quotation_rep = 'lc_cost_details';  //Quotation Woven 2
										}
										elseif ($report_id[0] == 414) {
											$quotation_rep = 'preCostRpt6';  // Quo. Wov. EPM
										}
										else{
											$quotation_rep = 'preCostRpt2';
										}
									?>
										
									<td width="70"><p><a href='##' style='color:#000' onClick="generate_worder_report('<? echo $quotation_rep; ?>',<? echo $row[csf('id')]; ?>,<? echo $row[csf('company_id')]; ?>,<? echo $row[csf('buyer_id')]; ?>,'<? echo $row[csf('style_ref')];?>','<? echo $row[csf('quot_date')]; ?>','<? echo $gmt_nature; ?>')"><? echo $row[csf('id')]; ?></a></p></td>
                                    <td width="60"><p><? echo $row[csf('mkt_no')]; ?>&nbsp;</p></td>
                                    <td width="100"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
									<td width="100" align="left"><p><? echo $row[csf('style_ref')]; ?>&nbsp;</p></td>
                                    <td width="80" align="center"><? if($row[csf('quot_date')]!="0000-00-00") echo change_date_format($row[csf('quot_date')]); ?>&nbsp;</td>
									<td align="center" width="80"><? if($row[csf('est_ship_date')]!="0000-00-00") echo change_date_format($row[csf('est_ship_date')]); ?>&nbsp;</td>
                                    <td width="50" align="center"><a href="##" onClick="openImgFile('<? echo $row[csf('id')];?>','img');">View</a></td>

									<td width="80"><p><? echo number_format(($margin_dzn/$row[csf("price_with_commn_dzn")]*100),2); ?></p></td>
                                    <td align="center" width="80"><? if($row[csf('approved_date')]!="0000-00-00") echo change_date_format($row[csf('approved_date')]); ?>&nbsp;</td>
                                    <? if($approval_type==1){?>
                                    <td width="100"><p><? echo ucfirst ($user_arr[$row[csf('inserted_by')]]); ?>&nbsp;</p></td>
                                    <td align="center" width="150"><? echo $unapproved_request_arr[$value]; ?>&nbsp;</td>
                                    <td><input type="text" name="unapprove_reason[]" id="unapprove_reason_<?echo $i;?>"  onChange="copy_value(this.value,'unapprove_reason_',<?php echo $i; ?>); "  name="unapprove_reason_<?php echo $i; ?>" class="text_boxes"></td>
                                    <? }else{ ?>
                                    <td> <input style="width:80px;" type="text" class="text_boxes"  name="txtCause_<? echo $row[csf('id')];?>" id="txtCause_<? echo $row[csf('id')];?>" placeholder="browse" onClick="openmypage_refusing_cause('requires/price_quatation_approval_controller.php?action=refusing_cause_popup','Refusing Cause','<? echo $row[csf('id')];?>');" value="<? echo $row[csf('refusing_cause')];?>"/></td>
                                    <td><p><? echo ucfirst ($user_arr[$row[csf('inserted_by')]]); ?>&nbsp;</p></td>
									
                                    <? } ?>
								</tr>
								<?
								$i++;
							}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="<? echo $tbl_width;?>" class="rpt_table">
				<tfoot>
                    <td width="30" align="center" >
                    	 <input type="checkbox" style="cursor: pointer;" id="all_check" onClick="check_all('all_check')" />
                    </td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1 || $previous_approved_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/></td>
				</tfoot>
			</table>
        </fieldset>
    </form>
<?
	exit();
}

if($action=="refusing_cause_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Refusing Cause Info","../../", 1, 1, $unicode);
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
			var data="action=save_update_delete_refusing_cause&operation="+operation+"&refusing_cause="+refusing_cause+"&quo_id="+quo_id;
			http.open("POST","price_quatation_approval_controller.php",true);
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
			if(response[0]==0)
			{
				alert("data saved successfully");
				document.getElementById('txt_refusing_cause').value =response[1];
				parent.emailwindow.hide();
			}
			else
			{
				alert("data not saved");
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
						<input type="text" name="txt_refusing_cause" id="txt_refusing_cause" class="text_boxes" style="width:320px;height: 100px;" value="" />
						<input type="hidden" name="hidden_quo_id" id="hidden_quo_id" value="<? echo $quo_id;?>">
					</td>
                  </tr>
                  <tr>
					<td colspan="4" align="center" class="button_container">
						<?
					     echo load_submit_buttons( $permission, "fnc_cause_info", 0,0 ,"reset_form('causeinfo_1','','')",1);
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
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</body>
    <?
	exit();
}

if($action=="save_update_delete_refusing_cause")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $_REQUEST ));
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}		
		$get_history = sql_select("SELECT id from approval_history where mst_id='$quo_id' and entry_form =10 and current_approval_status=1");
		$id=return_next_id( "id", "refusing_cause_history", 1);
		$field_array = "id,entry_form,mst_id,refusing_reason,inserted_by,insert_date";
		$data_array = "(".$id.",10,".$quo_id.",'".$refusing_cause."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		//echo "10**insert into refusing_cause_history (".$field_array.") values ".$data_array; die;
		$field_array_update ="un_approved_by*un_approved_date*current_approval_status*un_approved_reason* updated_by*update_date";
		$data_array_update = "".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*".$refusing_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_insert("refusing_cause_history",$field_array,$data_array,1);
		$rID2=execute_query("update wo_price_quotation set ready_to_approved=0 ,approved=0, updated_by = ".$_SESSION['logic_erp']['user_id']." , update_date = '".$pc_date_time."' where id='$quo_id'");
		if(count($get_history)>0)
		{
			$rID3=execute_query("update approval_history set un_approved_by=".$_SESSION['logic_erp']['user_id']." ,un_approved_date='".$pc_date_time."', current_approval_status =0, un_approved_reason= '".$refusing_cause."', updated_by = ".$_SESSION['logic_erp']['user_id']." , update_date = '".$pc_date_time."' where mst_id='$quo_id' and entry_form =10 and current_approval_status=1");
		}
		
		if($db_type==0)
		{
			if($rID && $rID2)
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
			if($rID && $rID2)
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
	$user_id_approval=0;
	$msg=''; $flag=''; $response='';
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($txt_alter_user_id!="") 	$user_id_approval=$txt_alter_user_id;
	else						$user_id_approval=$user_id;

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id_approval and is_deleted=0");
	$user_bypass=return_field_value("bypass","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id_approval and is_deleted=0");

	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");

	
	//if pre cost approved then block price quatation approved......................
	$combination_source=return_field_value("PUBLISH_SHIPMENT_DATE","VARIABLE_ORDER_TRACKING","COMPANY_NAME = $cbo_company_name AND VARIABLE_LIST = 47");
	
	
	$mstSql = "select a.READY_TO_APPROVED from wo_price_quotation a where a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and a.id in($booking_ids)";
	$mstSqlRes = sql_select($mstSql);
	foreach($mstSqlRes as $rows){
		if($rows['READY_TO_APPROVED'] != 1){echo "21**".implode(',',$appQutationArr);die;}
	}

	$pre_costing_approved_arr = return_library_array("select b.QUOTATION_ID,a.APPROVED from WO_PRE_COST_MST a,WO_PO_DETAILS_MASTER b where a.JOB_ID = b.id and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and b.QUOTATION_ID in($booking_ids)","QUOTATION_ID","APPROVED");
	
	$appQutationArr=array();
	foreach(explode(',',str_replace("'","",$booking_ids)) as $booking_id){
		if($combination_source==4 && $pre_costing_approved_arr[$booking_id]>0){
			$appQutationArr[$booking_id]=$booking_id;
		}
	}
	
	if(count($appQutationArr)>0){echo "16**".implode(',',$appQutationArr);die;}
	//...........................................................................end;
	
	
	
	if($approval_type==0)
	{
		//echo "10**SELECT sequence_no from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=$user_bypass and is_deleted=0"; die;
		if($user_bypass ==2)
		{
			$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");
		}
		else
		{
			$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and is_deleted=0");
		}
		

		// if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;
		$partial_approval = "";
		if($is_not_last_user != "")
		{
			// getting login in user's buyer id
			$loginUserBuyersArr = array();
			$loginUserBuyersSQL=sql_select("select (b.buyer_id || ',' ||  a.buyer_id) as buyer_id from user_passwd a, electronic_approval_setup b where a.id = b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and b.user_id=$user_id_approval and b.is_deleted=0 group by b.buyer_id, a.buyer_id");
			foreach ($loginUserBuyersSQL as $key => $buyerID) {
				$loginUserBuyersArr[] = $buyerID[csf('buyer_id')];
			}

			$loginUserBuyersArr = implode(',',$loginUserBuyersArr);
			$loginUserBuyersArr = explode(',',$loginUserBuyersArr);
			$loginUserBuyersArr = array_filter($loginUserBuyersArr);
			$loginUserBuyersArr = array_unique($loginUserBuyersArr);
			// print_r($loginUserBuyersArr);die();

			// getting next level all user's buyer id
			$credentialUserBuyersArr = array();
			$sql = sql_select("select (b.buyer_id || ',' ||  a.buyer_id) as buyer_id from user_passwd a, electronic_approval_setup b where a.id = b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 group by b.buyer_id, a.buyer_id");
			foreach ($sql as $key => $buyerID) {
				$credentialUserBuyersArr[] = $buyerID[csf('buyer_id')];
			}
			$credentialUserBuyersArr = implode(',',$credentialUserBuyersArr);
			$credentialUserBuyersArr = explode(',',$credentialUserBuyersArr);
			$credentialUserBuyersArr = array_filter($credentialUserBuyersArr);
			$credentialUserBuyersArr = array_unique($credentialUserBuyersArr);
			// print_r($credentialUserBuyersArr);die();

			/*$isBuyerExist = array_intersect($loginUserBuyersArr,$credentialUserBuyersArr);
			echo '10**<pre>';print_r($isBuyerExist);die;
			if(count($isBuyerExist) > 0)
			{
				$partial_approval=3;
			}
			else
			{
				$partial_approval=1;
			}*/

			if(count($credentialUserBuyersArr)>0)
			{
				if(in_array($loginUserBuyersArr,$credentialUserBuyersArr))
				{
					$partial_approval=3;
				}
				else
				{
					$partial_approval=1;
				}
			}
			else
			{
				$partial_approval=3;
			}
			
		}
		else
		{
			$partial_approval=1;
		}
		//echo $partial_approval;die;
		$response=$booking_ids;
		$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date,inserted_by,insert_date";
		$id=return_next_id( "id","approval_history", 1 ) ;

		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=10 group by mst_id","mst_id","approved_no");

		$approved_status_arr = return_library_array("select id, approved from wo_price_quotation where id in($booking_ids)","id","approved");

		$approved_no_array=array();
		$booking_ids_all=explode(",",$booking_ids);
		$book_nos='';

		for($i=0;$i<count($booking_ids_all);$i++)
		{
			$booking_id=$booking_ids_all[$i];

			$approved_no=$max_approved_no_arr[$booking_id];
			$approved_status=$approved_status_arr[$booking_id];

			if($approved_status==0)
			{
				$approved_no=$approved_no+1;
				$approved_no_array[$booking_id]=$approved_no;
				if($book_nos=="") $book_nos=$booking_id; else $book_nos.=",".$booking_id;
			}

			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",10,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')";

			$id=$id+1;

		}
		//print_r($booking_ids_all);
		//echo "insert into approval_history (".$field_array.") Values ".$data_array."**".$book_nos."**".$booking_nos;die;

		if(count($approved_no_array)>0)
		{
			$approved_string="";

			foreach($approved_no_array as $key=>$value)
			{
				$approved_string.=" WHEN $key THEN $value";
			}

			$approved_string_mst="CASE id ".$approved_string." END";
			$approved_string_dtls="CASE quotation_id ".$approved_string." END";

			$sql_insert="insert into wo_price_quotation_his( id, approved_no, quotation_id, company_id, buyer_id, style_ref, revised_no, pord_dept, product_code, style_desc, currency, agent, offer_qnty, region, color_range, incoterm, incoterm_place, machine_line, prod_line_hr, fabric_source, costing_per, quot_date, est_ship_date, factory, remarks, garments_nature, order_uom, gmts_item_id, set_break_down, total_set_qnty, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, efficiency_wastage_percent, season, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, op_date, inquery_id, m_list_no, bh_marchant, ready_to_approved)
				select
				'', $approved_string_mst, id, company_id, buyer_id, style_ref, revised_no, pord_dept, product_code, style_desc, currency, agent, offer_qnty, region, color_range, incoterm, incoterm_place, machine_line, prod_line_hr, fabric_source, costing_per, quot_date, est_ship_date, factory, remarks, garments_nature, order_uom, gmts_item_id, set_break_down, total_set_qnty, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, efficiency_wastage_percent, season, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, op_date, inquery_id, m_list_no, bh_marchant, ready_to_approved
		from wo_price_quotation where id in ($book_nos)";
			//echo $sql_insert;die;

			$sql_insert2="insert into wo_price_quot_costing_mst_his(id, quot_mst_id, quotation_id, approved_no, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, commission, commission_percent, final_cost_dzn, final_cost_dzn_percent, final_cost_pcs, final_cost_set_pcs_rate, a1st_quoted_price, a1st_quoted_price_percent, a1st_quoted_price_date, revised_price, revised_price_date, confirm_price, confirm_price_set_pcs_rate, confirm_price_dzn, confirm_price_dzn_percent, margin_dzn, margin_dzn_percent, price_with_commn_dzn, price_with_commn_percent_dzn, price_with_commn_pcs, price_with_commn_percent_pcs, confirm_date, asking_quoted_price, asking_quoted_price_percent, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, terget_qty, depr_amor_pre_cost, depr_amor_po_price, interest_pre_cost, interest_po_price, income_tax_pre_cost, income_tax_po_price)
				select
				'', id, quotation_id, $approved_string_dtls, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, commission, commission_percent, final_cost_dzn, final_cost_dzn_percent, final_cost_pcs, final_cost_set_pcs_rate, a1st_quoted_price, a1st_quoted_price_percent, a1st_quoted_price_date, revised_price, revised_price_date, confirm_price, confirm_price_set_pcs_rate, confirm_price_dzn, confirm_price_dzn_percent, margin_dzn, margin_dzn_percent, price_with_commn_dzn, price_with_commn_percent_dzn, price_with_commn_pcs, price_with_commn_percent_pcs, confirm_date, asking_quoted_price, asking_quoted_price_percent, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, terget_qty, depr_amor_pre_cost, depr_amor_po_price, interest_pre_cost, interest_po_price, income_tax_pre_cost, income_tax_po_price from wo_price_quotation_costing_mst where quotation_id in ($book_nos)";
			//echo $sql_insert2;die;

			$sql_insert3="insert into wo_price_quot_set_details_his(id, approved_no, quot_set_dlts_id, quotation_id, gmts_item_id, set_item_ratio)
				select
				'', $approved_string_dtls, id, quotation_id, gmts_item_id, set_item_ratio from wo_price_quotation_set_details where quotation_id in ($book_nos)";
			//echo $sql_insert3;die;

			$sql_insert4="insert into wo_pri_quo_comm_cost_dtls_his(id, approved_no, quo_comm_dtls_id, quotation_id, item_id, base_id,  rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted)
				select
				'', $approved_string_dtls, id, quotation_id, item_id, base_id, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_pri_quo_comarcial_cost_dtls where quotation_id in ($book_nos)";
			//echo $sql_insert4;die;

			$sql_insert5="insert into wo_pri_quo_commiss_dtls_his(id, approved_no, quo_commiss_dtls_id, quotation_id, particulars_id, commission_base_id, commision_rate, commission_amount, inserted_by, insert_date, updated_by, update_date,  status_active, is_deleted)
				select
				'', $approved_string_dtls, id, quotation_id, particulars_id, commission_base_id, commision_rate, commission_amount, inserted_by, insert_date, updated_by,  update_date, status_active, is_deleted from wo_pri_quo_commiss_cost_dtls where quotation_id in ($book_nos)";
			//echo $sql_insert5;die;

			$sql_insert6="insert into wo_pri_quo_embe_cost_dtls_his(id, approved_no, quo_emb_dtls_id, quotation_id, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted)
				select
				'', $approved_string_dtls, id, quotation_id, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_pri_quo_embe_cost_dtls where quotation_id in ($book_nos)";
			//echo $sql_insert6;die;

			$sql_insert7="insert into wo_pri_quo_fab_cost_dtls_his(id, approved_no, quo_fab_dtls_id, quotation_id, item_number_id, body_part_id,  fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, company_id, costing_per, fab_cons_in_quotat_varia, process_loss_method, yarn_breack_down, width_dia_type, cons_breack_down, msmnt_break_down, marker_break_down)
				select
				'', $approved_string_dtls, id, quotation_id, item_number_id, body_part_id, fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, company_id, costing_per, fab_cons_in_quotat_varia, process_loss_method, yarn_breack_down, width_dia_type, cons_breack_down, msmnt_break_down, marker_break_down from wo_pri_quo_fabric_cost_dtls where quotation_id in ($book_nos)";
			//echo $sql_insert7;die;

			$sql_insert8="insert into wo_pri_quo_fab_conv_dtls_his (id, approved_no, quo_fab_conv_dtls_id, quotation_id, cost_head, cons_type, req_qnty, charge_unit, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, process_loss)
				select
				'', $approved_string_dtls, id, quotation_id, cost_head, cons_type, req_qnty, charge_unit, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, process_loss from wo_pri_quo_fab_conv_cost_dtls where quotation_id in ($book_nos)";
			//echo $sql_insert8;die;

			$sql_insert9="insert into wo_pri_quo_fab_co_avg_con_his (id, approved_no, quo_fab_avg_co_dtls_id, wo_pri_quo_fab_co_dtls_id, quotation_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs, body_length, body_sewing_margin, body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin, front_rise_length, front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin, total, marker_dia, marker_yds, marker_inch, gmts_pcs, marker_length, net_fab_cons)
				select
				'', $approved_string_dtls, id, wo_pri_quo_fab_co_dtls_id, quotation_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs, body_length, body_sewing_margin, body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin, front_rise_length, front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin, total, marker_dia, marker_yds, marker_inch, gmts_pcs, marker_length, net_fab_cons from wo_pri_quo_fab_co_avg_con_dtls where quotation_id in ($book_nos)";
			//echo $sql_insert9;die;

			$sql_insert10="insert into wo_pri_quo_fab_yarn_dtls_his(id, approved_no, quo_yarn_dtls_id, quotation_id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, supplier_id)
				select
				'', $approved_string_dtls, id, quotation_id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, supplier_id from wo_pri_quo_fab_yarn_cost_dtls where quotation_id in ($book_nos)";
			//echo $sql_insert10;die;

			$sql_insert11="insert into wo_pri_quo_sum_dtls_his( id, approved_no, quo_sum_dtls_id, quotation_id, fab_yarn_req_kg, fab_woven_req_yds, fab_knit_req_kg, fab_amount, yarn_cons_qnty, yarn_amount, conv_req_qnty, conv_charge_unit, conv_amount, trim_cons, trim_rate, trim_amount, emb_amount, wash_amount, comar_rate, comar_amount, commis_rate, commis_amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted)
				select
				'', $approved_string_dtls, id, quotation_id, fab_yarn_req_kg, fab_woven_req_yds, fab_knit_req_kg, fab_amount, yarn_cons_qnty, yarn_amount, conv_req_qnty, conv_charge_unit, conv_amount, trim_cons, trim_rate, trim_amount, emb_amount, wash_amount, comar_rate, comar_amount, commis_rate, commis_amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_pri_quo_sum_dtls where quotation_id in ($book_nos)";
			//echo $sql_insert11;die;

			$sql_insert12="insert into wo_pri_quo_trim_cost_dtls_his( id, approved_no, quo_trim_dtls_id, quotation_id, trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted)
				select
				'', $approved_string_dtls, id, quotation_id, trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_pri_quo_trim_cost_dtls where quotation_id in ($book_nos)";
			//echo $sql_insert12;die;
		}

		if($partial_approval == 1)
		{
			$updateData=$user_id_approval."*'".$pc_date_time."'";


			sql_multirow_update("wo_price_quotation","approved_by*approved_date",$updateData,"id",$booking_ids,1);
		}

		$rID=sql_multirow_update("wo_price_quotation","approved",$partial_approval,"id",$booking_ids,0);
		if($rID) $flag=1; else $flag=0;

		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=10 and mst_id in ($booking_ids)";
		$rIDapp=execute_query($query,1);
		if($flag==1)
		{
			if($rIDapp) $flag=1; else $flag=0;
		}

		$rID2=sql_insert("approval_history",$field_array,$data_array,0);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}

		if(count($approved_no_array)>0)
		{
			$rID3=execute_query($sql_insert,1);
			if($flag==1)
			{
				if($rID3) $flag=1; else $flag=0;
			}

			$rID4=execute_query($sql_insert2,1);
			if($flag==1)
			{
				if($rID4) $flag=1; else $flag=0;
			}

			$rID5=execute_query($sql_insert3,1);
			if($flag==1)
			{
				if($rID5) $flag=1; else $flag=0;
			}

			$rID6=execute_query($sql_insert4,1);
			if($flag==1)
			{
				if($rID6) $flag=1; else $flag=0;
			}

			$rID7=execute_query($sql_insert5,1);
			if($flag==1)
			{
				if($rID7) $flag=1; else $flag=0;
			}

			$rID8=execute_query($sql_insert6,1);
			if($flag==1)
			{
				if($rID8) $flag=1; else $flag=0;
			}

			$rID9=execute_query($sql_insert7,1);
			if($flag==1)
			{
				if($rID9) $flag=1; else $flag=0;
			}

			$rID10=execute_query($sql_insert8,1);
			if($flag==1)
			{
				if($rID10) $flag=1; else $flag=0;
			}

			$rID11=execute_query($sql_insert9,1);
			if($flag==1)
			{
				if($rID11) $flag=1; else $flag=0;
			}

			$rID12=execute_query($sql_insert10,1);
			if($flag==1)
			{
				if($rID12) $flag=1; else $flag=0;
			}

			$rID13=execute_query($sql_insert11,1);
			if($flag==1)
			{
				if($rID13) $flag=1; else $flag=0;
			}

			$rID14=execute_query($sql_insert12,1);
			if($flag==1)
			{
				if($rID14) $flag=1; else $flag=0;
			}
		}
		//echo "21**".$flag;die;
		if($flag==1) $msg='19'; else $msg='21';
	}
	else
	{
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

		//precost app job...............................
		 
		$quotation_sql ="select a.JOB_NO,a.QUOTATION_ID from WO_PO_DETAILS_MASTER a,WO_PRE_COST_MST b where a.id=b.JOB_ID and a.IS_DELETED =0 and b.IS_DELETED=0 and a.STATUS_ACTIVE =1 and b.STATUS_ACTIVE=1 and a.QUOTATION_ID<>0 and b.APPROVED in(1,3) and a.QUOTATION_ID in($booking_ids)";
		
		//echo "23**";echo $quotation_sql;oci_rollback($con);die;
		
		
		$pre_costing_approved_job_arr = return_library_array($quotation_sql,"JOB_NO","JOB_NO");
		if(count($pre_costing_approved_job_arr)>0){
			echo "23**".implode(',',$pre_costing_approved_job_arr);
			oci_rollback($con);
			disconnect($con);
			die;
		}
		//............................................end;


		$rID=sql_multirow_update("wo_price_quotation","approved*ready_to_approved",'0*2',"id",$booking_ids,0);
		if($rID) $flag=1; else $flag=0;
		$unapprove_reasons_arr = explode(',', $unapprove_reasons);
		$approval_ids_arr = explode(',', $approval_ids);
		$id_val = array_combine($approval_ids_arr, $unapprove_reasons_arr);
		// $id_val = array_combine($booking_ids_all, $unapprove_reasons_arr);
		// echo "<pre>";
		// print_r($id_val);
		// $size = count($booking_ids_all);
		foreach ($id_val as $key => $value) {
			$rID2 = execute_query("UPDATE approval_history SET current_approval_status=0, un_approved_reason=$value WHERE entry_form=10 and id=$key",1);
		}

		// die();
		// $query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=10 and mst_id in ($booking_ids)";
		$rID2 = true;
		// $rID2=execute_query($query,1);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}

		$data=$user_id_approval."*'".$pc_date_time."'*".$user_id."*'".$pc_date_time."'";
		$rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date*updated_by*update_date",$data,"id",$approval_ids,1);
		if($flag==1)
		{
			if($rID3) $flag=1; else $flag=0;
		}
		//echo $flag;die;
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

if($action=='user_popup'){
echo load_html_head_contents("Popup Info","../../",1, 1,'',1,'');
?>

<script>

// flowing script for multy select data------------------------------------------------------------------------------start;
  function js_set_value(id)
  {
 	//alert(id)
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
$sql = "select a.id, a.user_name, a.department_id, a.user_full_name, a.designation,b.sequence_no from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and valid=1 and a.id!=$user_id and b.is_deleted=0  and b.page_id=$menu_id order by sequence_no ASC";
//echo $sql;
$arr = array(2 => $custom_designation, 3 => $Department);
echo create_list_view("list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,", "630", "220", 0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr, "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);');
?>

</form>
<script language="javascript" type="text/javascript">
  setFilterGrid("tbl_style_ref");
</script>


<?
}// action SystemIdPopup end;

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
                    if($db_type==0)
	                 {
	                    $sql_img = "select id,master_tble_id,image_location
	                    from common_photo_library
	                    where master_tble_id='$id' and form_name='quotation_entry' limit 1";
	                 }
	              	if($db_type==2)
	                 {
	                $sql_img = "select id,master_tble_id,image_location from common_photo_library
	                    where master_tble_id='$id' and form_name='quotation_entry'  ";
	                 }
	                 //echo $sql_img; die;

	                $data_array_img=sql_select($sql_img);
	                if(count($data_array_img) > 0){
	                    foreach($data_array_img as $inf_img)
	                    {
							$i++;
	                    ?>
	                    	<td align="center"><img  src='../../<? echo $inf_img[csf("image_location")]; ?>' height='300' width='200'/></td>
	                    <?
							if($i%2==0) echo "</tr><tr>";
	                    }
                	}
                	else{ ?>
                		<td align="center"><img  src='../../images/no-image.jpg' height='300' width='200'/></td>
                	<? }
                    ?>
                </tr>
            </table>
        </div>
	</fieldset>
<?
exit();
}

?>