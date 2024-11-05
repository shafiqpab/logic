<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');
/*include('../../includes/class4/class.conditions.php');
include('../../includes/class4/class.reports.php');
include('../../includes/class4/class.fabrics.php');
include('../../includes/class4/class.yarns.php');
include('../../includes/class4/class.conversions.php');*/

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];

if($db_type==0) $year_cond="SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
else if($db_type==2) $year_cond="to_char(a.insert_date,'YYYY') as year";

if($db_type==0) $year_cond_groupby="SUBSTRING_INDEX(a.insert_date, '-', 1)";
else if($db_type==2) $year_cond_groupby="to_char(a.insert_date,'YYYY')";

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );
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

/*if ($action=="populate_cm_compulsory")
{
	$cm_cost_compulsory=return_field_value("cm_cost_compulsory","variable_order_tracking","company_name ='".$data."' and variable_list=22 and is_deleted=0 and status_active=1");
	echo $cm_cost_compulsory;
	exit();
}*/

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$company_name=str_replace("'","",$cbo_company_name);
	$user_id=($txt_alter_user_id=='')?$user_id:$txt_alter_user_id;

	if($company_name>0){
		$app_company_arr[$company_name]=$company_name;
	}
	else{
		$app_company_arr=return_library_array( "select company_id, company_id from electronic_approval_setup where page_id=$menu_id and user_id=$user_id and is_deleted=0",'company_id','company_id');
	}

	?>
<form name="sourcingApproval_2" id="sourcingApproval_2">
    <fieldset style="width:1230px; margin-top:10px">
    <legend>Sourcing Post Cost Approval</legend>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1220" class="rpt_table" align="left" >
            <thead>
                <th width="40">&nbsp;</th>
                <th width="30">SL</th>
                <th width="50">Company</th>
                <th width="50">Job No</th>
                <th width="60">CM Cost</th>
                <th width="60">EPM</th>
                <th width="60">SMV</th>
                <th width="40">Year</th>
                <th width="110">Buyer</th>
                <th width="130">Style Ref.</th>
                <th width="70">Sourcing Date</th>
                <th width="70">IMG</th>
                <th width="140">Unapproved Request</th>
                <th width="65">Insert By</th>
                <th width="80">Approved Date</th>
                <th>Refusing Cause</th>
            </thead>
        </table>
        <div style="width:1240px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" >
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1220" class="rpt_table" id="tbl_list_search" align="left">
              <tbody>
         <?
$i=1;
foreach($app_company_arr as $company_name){
	
	$sequence_no='';
	if($txt_alter_user_id!="")
	{
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			$log_sql = sql_select("SELECT user_level, buyer_id, unit_id, is_data_level_secured FROM user_passwd WHERE id = '$txt_alter_user_id' AND valid = 1");
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
		$user_id=$txt_alter_user_id;
	}
	else
	{
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!=""){$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";} else{$buyer_id_cond="";}
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	}
	
	
	$job_no=str_replace("'","",$txt_job_no);
	$style_ref=str_replace("'","",$txt_style_ref);
	$job_year=str_replace("'","",$cbo_year);

	if ($style_ref=="") $styleref_cond=""; else $styleref_cond=" and a.style_ref_no='".trim($style_ref)."' ";
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
		if(str_replace("'","",$cbo_get_upto)==1) $date_cond=" and b.sourcing_date>$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==2) $date_cond=" and b.sourcing_date<=$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==3) $date_cond=" and b.sourcing_date=$txt_date";
		else $date_cond='';
	}

	$approval_type=str_replace("'","",$cbo_approval_type);
	if($previous_approved==1 && $approval_type==1) $previous_approved_type=1;
	//$user_id=133;
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and is_deleted=0");
	$buyer_ids_array = array();
	$buyerData = sql_select("select user_id, sequence_no, buyer_id from electronic_approval_setup where company_id=$company_name and page_id=$menu_id and is_deleted=0 ");
	foreach($buyerData as $row)
	{
		$buyer_ids_array[$row[csf('user_id')]]['u']=$row[csf('buyer_id')];
		$buyer_ids_array[$row[csf('sequence_no')]]['s']=$row[csf('buyer_id')];
	}
	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Sourcing Post Cost Approval.</font>";
		die;
	}
	

	 //echo $previous_approved.'--'.$approval_type;die;
	if($previous_approved==1 && $approval_type==1)
	{
		$sequence_no_cond=" and c.sequence_no<'$user_sequence_no'";
		$sql="select b.id,a.set_smv as sew_smv, a.quotation_id, a.job_no_prefix_num, $year_cond, a.id as job_id, a.job_no, a.buyer_name, a.style_ref_no, b.sourcing_date as costing_date, b.approved, b.inserted_by, b.sourcinng_refusing_cause, c.id as approval_id, c.sequence_no, c.approved_by, c.id as approval_id, min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date, a.job_quantity, (a.job_quantity*a.total_set_qnty) as job_qty_pcs, a.total_price
			      from wo_pre_cost_mst b, wo_po_details_master a, approval_history c, wo_po_break_down d
				  where b.id=c.mst_id and c.entry_form=47 and a.job_no=b.job_no and a.COMPANY_NAME=$company_name and a.job_no=d.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.current_approval_status=1 and b.sourcing_ready_to_approved=1 and b.is_deleted=0 and b.sourcing_approved in (1,3) $buyer_id_cond $date_cond $job_no_cond $styleref_cond $sequence_no_cond group by b.id, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby, a.id, a.job_no, a.buyer_name, a.style_ref_no, b.sourcing_date, b.approved, b.inserted_by, b.sourcinng_refusing_cause, c.id, c.sequence_no, c.approved_by, c.id, a.job_quantity, a.total_set_qnty, a.total_price,a.set_smv";
		//$buyer_id_cond
	}
	else if($approval_type==2)
	{
		if($db_type==0)
		{
			$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass = 2 and buyer_id='' and is_deleted=0","seq");
		}
		else
		{
			$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
		}
		//echo $user_sequence_no.'--'.$min_sequence_no.'--'.$sequence_no; die;
		if($user_sequence_no==$min_sequence_no)
		{
			$buyer_ids = $buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";

			$sql="select b.id,a.set_smv as sew_smv, a.quotation_id, a.job_no_prefix_num, $year_cond, a.id as job_id, a.job_no, a.buyer_name, a.style_ref_no, b.sourcing_date as costing_date, '0' as approval_id, b.approved, b.inserted_by, b.sourcinng_refusing_cause, b.entry_from, min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date, a.job_quantity, (a.job_quantity*a.total_set_qnty) as job_qty_pcs, a.total_price from wo_pre_cost_mst b, wo_po_details_master a,wo_po_break_down d  where a.job_no=b.job_no and a.job_no=d.job_no_mst and a.COMPANY_NAME=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.sourcing_ready_to_approved=1 and b.sourcing_approved in (0,2) $buyer_id_cond $buyer_id_cond2 $date_cond $job_no_cond $job_year_cond $styleref_cond group by b.id, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby, a.id, a.job_no, a.buyer_name, a.style_ref_no, b.sourcing_date, '0', b.approved, b.inserted_by, b.sourcinng_refusing_cause, b.entry_from, a.job_quantity, a.total_set_qnty, a.total_price, a.set_smv";//, $internalRefCond as internalRef, $fileNoCond as fileNo
			 //echo $sql;die;
		}
		else if($sequence_no == "")
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";

			if($buyer_ids=="") $buyer_id_cond3=""; else $buyer_id_cond3=" and c.buyer_name in($buyer_ids)";

			$seqSql="select sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=$company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
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
				$buyerIds_cond=" and a.buyer_name not in($buyerIds)"; $seqCond=" and (".chop($query_string,'or ').")";
			}
			$sequence_no_by_no=chop($sequence_no_by_no,',');
			$sequence_no_by_yes=chop($sequence_no_by_yes,',');

			if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
			if($sequence_no_by_no=="") $sequence_no_by_no=0;

			$pre_cost_id='';
			$pre_cost_id_sql="select distinct (mst_id) as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c where a.id=b.mst_id and a.job_no=c.job_no and c.COMPANY_NAME=$company_name and b.sequence_no in ($sequence_no_by_no) and b.entry_form=47 and b.current_approval_status=1 $buyer_id_cond3 $seqCond
			union
			select distinct (mst_id) as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c where a.id=b.mst_id and a.job_no=c.job_no and c.COMPANY_NAME=$company_name and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=47 and b.current_approval_status=1 $buyer_id_cond3";
			$bResult=sql_select($pre_cost_id_sql);
			foreach($bResult as $bRow)
			{
				$pre_cost_id.=$bRow[csf('pre_cost_id')].",";
			}

			$pre_cost_id=chop($pre_cost_id,',');

			$pre_cost_id_app_sql=sql_select("select b.mst_id as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c
			where a.id=b.mst_id and a.job_no=c.job_no and c.COMPANY_NAME=$company_name and b.sequence_no=$user_sequence_no and b.entry_form=47 and a.sourcing_ready_to_approved=1 and b.current_approval_status=1");

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

			$sql="select b.id, a.set_smv as sew_smv,a.quotation_id, a.job_no_prefix_num, $year_cond, a.id as job_id, a.job_no, a.buyer_name, a.style_ref_no, b.sourcing_date as costing_date, 0 as approval_id, b.approved, b.inserted_by, b.sourcinng_refusing_cause, b.entry_from, min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date, a.job_quantity, (a.job_quantity*a.total_set_qnty) as job_qty_pcs, a.total_price
			from wo_pre_cost_mst b, wo_po_details_master a, wo_po_break_down d
			where a.job_no=b.job_no and a.job_no=d.job_no_mst and a.COMPANY_NAME=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.sourcing_ready_to_approved=1 and b.sourcing_approved in (0,2) $buyer_id_cond $date_cond $pre_cost_id_cond $buyerIds_cond $buyer_id_cond2 $job_no_cond $styleref_cond group by b.id, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby, a.id, a.job_no, a.buyer_name, a.style_ref_no, b.sourcing_date, b.approved, b.inserted_by, b.sourcinng_refusing_cause, b.entry_from, a.job_quantity, a.total_set_qnty, a.total_price, a.set_smv";//, $internalRefCond as internalRef, $fileNoCond as fileNo
			//echo $sql;die;
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
				select b.id, a.set_smv as sew_smv,a.quotation_id, a.job_no_prefix_num, $year_cond, a.id as job_id, a.job_no, a.buyer_name, a.style_ref_no, b.sourcing_date as costing_date, 0 as approval_id, b.approved, b.inserted_by, b.sourcinng_refusing_cause, b.entry_from, min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date, a.job_quantity, (a.job_quantity*a.total_set_qnty) as job_qty_pcs, a.total_price
				from wo_pre_cost_mst b, wo_po_details_master a, wo_po_break_down d
				where a.job_no=b.job_no and a.job_no=d.job_no_mst and a.COMPANY_NAME=$company_name  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.sourcing_ready_to_approved=1 and b.sourcing_approved in(1,3) $pre_cost_id_cond2 $buyer_id_cond $buyer_id_cond2 $date_cond $job_no_cond $styleref_cond group by b.id, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby, a.id, a.job_no, a.buyer_name, a.style_ref_no, b.sourcing_date, b.approved, b.inserted_by, b.sourcinng_refusing_cause, b.entry_from, a.job_quantity, a.total_set_qnty, a.total_price, a.set_smv";//, $internalRefCond as internalRef, $fileNoCond as fileNo
			}
		}
		else
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";

			$user_sequence_no=$user_sequence_no-1;
			if($sequence_no==$user_sequence_no) $sequence_no_by_pass='';
			else
			{
				if($db_type==0)
				{
					$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");// and bypass=1
				}
				else
				{
					$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");//bypass=1 and
				}
			}

			if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$sequence_no'";
			else $sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";

			$sql="select b.id, a.set_smv as sew_smv,a.quotation_id, a.job_no_prefix_num, $year_cond, a.id as job_id, a.job_no, a.buyer_name, a.style_ref_no, b.sourcing_date as costing_date, b.approved, b.inserted_by, b.sourcinng_refusing_cause, c.id as approval_id, c.sequence_no, c.approved_by, b.entry_from, min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date, a.job_quantity, (a.job_quantity*a.total_set_qnty) as job_qty_pcs, a.total_price
			from wo_pre_cost_mst b, wo_po_details_master a, approval_history c, wo_po_break_down d
			where b.id=c.mst_id and c.entry_form=47 and a.job_no=b.job_no and a.COMPANY_NAME=$company_name and a.job_no=d.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.current_approval_status=1 and b.sourcing_ready_to_approved=1 and b.is_deleted=0 and b.sourcing_approved in(1,3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $date_cond $job_no_cond $styleref_cond group by  b.id, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby, a.id, a.job_no, a.buyer_name, a.style_ref_no, b.sourcing_date, b.approved, b.inserted_by, b.sourcinng_refusing_cause, c.id, c.sequence_no, c.approved_by, b.entry_from, a.job_quantity, a.total_set_qnty, a.total_price, a.set_smv ";//, $internalRefCond as internalRef, $fileNoCond as fileNo
			//echo $sql; die;
		}
	}
	else
	{ 
		$buyer_ids=$buyer_ids_array[$user_id]['u'];
		if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";

		$sequence_no_cond=" and c.sequence_no='$user_sequence_no'";

		$sql="select b.id, a.set_smv as sew_smv,a.quotation_id, a.job_no_prefix_num, $year_cond, a.id as job_id, a.job_no, a.buyer_name, a.style_ref_no, b.sourcing_date as costing_date, b.approved, b.inserted_by, b.sourcinng_refusing_cause, c.id as approval_id, c.sequence_no, c.approved_date, c.approved_by, c.id as approval_id, b.entry_from, min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date, a.job_quantity, (a.job_quantity*a.total_set_qnty) as job_qty_pcs, a.total_price
			      from wo_pre_cost_mst b, wo_po_details_master a, approval_history c, wo_po_break_down d
				  where b.id=c.mst_id and c.entry_form=47 and a.job_no=b.job_no and a.COMPANY_NAME=$company_name and a.job_no=d.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.current_approval_status=1 and b.sourcing_ready_to_approved=1 and b.is_deleted=0 and b.sourcing_approved in (1,3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $date_cond $job_no_cond $styleref_cond group by b.id, a.quotation_id, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby, a.id, a.job_no, a.buyer_name, a.style_ref_no, b.sourcing_date, b.approved, b.inserted_by, b.sourcinng_refusing_cause, c.id, c.sequence_no, c.approved_by, c.id, c.approved_date, b.entry_from, a.job_quantity, a.total_set_qnty, a.total_price, a.set_smv ";//, $internalRefCond as internalRef, $fileNoCond as fileNo
	}
	// echo $sql; die;
	$nameArray=sql_select( $sql );
	$jobFobValue_arr=array(); $jobIds="";
	foreach ($nameArray as $row)
	{
		$jobFobValue_arr[$row[csf('job_no')]]=$row[csf('total_price')];
		$job_id_arr[$row[csf('job_id')]]=$row[csf('job_id')];
		//if($jobIds=='') $jobIds=$row[csf('job_id')]; else $jobIds.=','.$row[csf('job_id')];
	}





	
	// $jobIds=implode(",",array_filter(array_unique(explode(",",$jobIds))));
	// $job_ids=count(explode(",",$jobIds)); $jobId_cond="";
	// if($db_type==2 && $job_ids>1000)
	// {
	// 	$jobId_cond=" and (";
	// 	$jobIdsArr=array_chunk(explode(",",$jobIds),999);
	// 	foreach($jobIdsArr as $ids)
	// 	{
	// 		$ids=implode(",",$ids);
	// 		$jobId_cond.=" job_id in($ids) or"; 
	// 	}
	// 	$jobId_cond=chop($jobId_cond,'or ');
	// 	$jobId_cond.=")";
	// }
	// else $jobId_cond=" and job_id in($jobIds)";
	
	$preSql ="select job_no, costing_per_id as costing_per, job_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, commission, commission_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, total_cost, deffdlc_cost, deffdlc_percent, interest_cost, interest_percent, incometax_cost, incometax_percent, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, price_pcs_or_set, depr_amor_pre_cost, depr_amor_po_price, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche, margin_pcs_bom from wo_pre_cost_dtls where status_active=1 and is_deleted=0 ".where_con_using_array($job_id_arr,0,'job_id')."";
	//echo $preSql;die;
	$pre_data_array = sql_select($preSql);
		 
	$margin_pcs_arr=array();$pre_cost_dat_by_job=array();
	foreach( $pre_data_array as $row )
	{ 
		$cm_cost_arr[$row[csf('job_no')]]['cm_cost']+=$row[csf('cm_cost')];
		if($row[csf("costing_per")]==1){$order_price_per_dzn=12;}
		else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;}
		else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;}
		else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;}
		else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;}
		else {$order_price_per_dzn=0;}
		
		$fabric_cost_dzn=$row[csf("fabric_cost")];
		$trims_cost_dzn=$row[csf("trims_cost")];
		$embel_cost_dzn=$row[csf("embel_cost")];
		$wash_cost_dzn=$row[csf("wash_cost")];
		$comm_cost_dzn=$row[csf("comm_cost")];
		
		$comm_cost_dzn=$row[csf("comm_cost")];
		$cm_cost_dzn=$row[csf("cm_cost")];
		$price_dzn=$row[csf("price_dzn")];
		$total_cost_dzn=$row[csf("total_cost")];
		$deffdlc_cost_dzn=$row[csf("deffdlc_cost")];
		$interest_cost_dzn=$row[csf("interest_cost")];
		$incometax_cost_dzn=$row[csf("incometax_cost")];
		$commission_dzn=$row[csf("commission")];
		$operatin_expense_dzn=$row[csf("common_oh")];
		$lab_test_dzn=$row[csf("lab_test")];
		$inspection_dzn=$row[csf("inspection")];
		$cm_cost_dzn =$row[csf("cm_cost")];
		$common_oh_dzn =$row[csf("common_oh")];
		$freight_dzn =$row[csf("freight")];
		$currier_pre_cost_dzn = $row[csf("currier_pre_cost")];
		$certificate_pre_cost_dzn = $row[csf("certificate_pre_cost")];
		$deffdlc_cost_dzn = $row[csf("deffdlc_cost")];
		$depr_amor_pre_cost_dzn = $row[csf("depr_amor_pre_cost")];
		$interest_cost_dzn=$row[csf("interest_cost")];
		$interest_cost_percent=$row[csf("interest_percent")];
		$incometax_cost_dzn=$row[csf("incometax_cost")];
		$studio_cost_dzn=$row[csf("studio_cost")];
		$design_cost_dzn=$row[csf("design_cost")];
			
		$depr_amor_po_price=$row[csf("depr_amor_po_price")];
		$depr_amor_pre_cost_dzn=$row[csf("depr_amor_pre_cost")];
		$margin_pcs_bom=$commission_costing_arr[$job_no];
		$lab_test=$lab_test_cost;
		$inspection=$inspection_cost;
		$cm_cost=$cm_cost_dzn;
		$freight=$freight_cost;
		$currier_pre_cost=$currier_cost;
		$certificate_pre_cost=$certificate_cost;
		
		$material_service_cost_dzn=$fabric_cost_dzn+$trims_cost_dzn+$embel_cost_dzn+$wash_cost_dzn+$deffdlc_cost_dzn+$inspection_dzn+$lab_test_dzn+$freight_dzn+$currier_pre_cost_dzn+$certificate_pre_cost_dzn;
		
		$net_fob_value_dzn=$price_dzn-$commission_dzn;
		$contributions_value_dzn=$net_fob_value_dzn-$material_service_cost_dzn-$comm_cost_dzn;
		$job_epm_arr[$row[csf('job_no')]]=$contributions_value_dzn/$order_price_per_dzn;
		$job_epm_contribute_margin_arr[$row[csf('job_no')]]=$contributions_value_dzn.', CostPer='.$order_price_per_dzn;
		$margin_pcs_arr[$row[csf('job_no')]]['marginpcs']=$row[csf('margin_pcs_set')];
		$margin_pcs_arr[$row[csf('job_no')]]['marginpcsbom']=$row[csf('margin_pcs_bom')];
		
		$pre_cost_dat_by_job[$row[csf('job_no')]]['cm_val']=$row[csf("cm_cost")];
		$pre_cost_dat_by_job[$row[csf('job_no')]]['cm_per']=$row[csf("cm_cost_percent")];
		$pre_cost_dat_by_job[$row[csf('job_no')]]['marg_val']=$row[csf("margin_pcs_set")];
		$pre_cost_dat_by_job[$row[csf('job_no')]]['marg_per']=$row[csf("margin_pcs_set_percent")];
		
	}



	$sql_unapproved=sql_select("select * from fabric_booking_app_cause_source where  entry_form=47 and approval_type=2 and is_deleted=0 and status_active=1");
	$unapproved_request_arr=array();
	foreach($sql_unapproved as $rowu)
	{
		$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
	}

	//Pre cost button---------------------------------
	$print_report_format_ids = return_field_value("format_id","lib_report_template","template_name=".$company_name." and module_id=2 and report_id in (141) and is_deleted=0 and status_active=1");
	$format_ids=explode(",",$print_report_format_ids);
	$row_id=$format_ids[0];
	// echo $row_id.'d';

	//Order Wise Budget Report button---------------------------------
	$print_report_format_ids2 = return_field_value("format_id","lib_report_template","template_name=".$company_name." and module_id=11 and report_id=18 and is_deleted=0 and status_active=1");
	$format_ids2=explode(",",$print_report_format_ids2);
	$row_id2=$format_ids2[0];

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

							
							//{$row[csf('buyer_name')]}
							if($print_cond==1)
							{
								if($row_id==313){$action='mkt_source_cost'; } //MKT Vs Source
								else if($row_id==323){$action='app_final_cost';} //Final App
								

								$function="generate_worder_report('".$action."','".$row[csf('job_no')]."',".$company_name.",".$row[csf('buyer_name')].",'".$row[csf('style_ref_no')]."','".$row[csf('costing_date')]."',".$row[csf('entry_from')].",'".$row[csf('quotation_id')]."');"; 
								$function2="generate_worder_report('mkt_source_cost','".$row[csf('job_no')]."',".$company_name.",".$row[csf('buyer_name')].",'".$row[csf('style_ref_no')]."','".$row[csf('costing_date')]."',".$row[csf('entry_from')].",'".$row[csf('quotation_id')]."');";
								
								
								?>
								<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>" align="center">
                                	<td width="40" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<?=$i;?>" />
                                        <input id="booking_id_<?=$i;?>" name="booking_id[]" type="hidden" value="<?=$value; ?>" />
                                        <input id="booking_no_<?=$i;?>" name="booking_no[]" type="hidden" value="<?=$row[csf('job_no')]; ?>" />
                                        <input id="approval_id_<?=$i;?>" name="approval_id[]" type="hidden" value="<?=$row[csf('approval_id')]; ?>" />
                                        <input id="unapprov_msg_<?=$i;?>" name="unapprov_msg[]" type="hidden" value="<?=$unapproved_request_arr[$value]; ?>" />
                                        <input id="<?=strtoupper($row[csf('job_no')]); ?>" name="no_joooob[]" type="hidden" value="<?=$i;?>" />
                                        <input id="mst_id_company_id_<?=$i;?>" name="mst_id_company_id[]" type="hidden" value="<?=$row[csf('job_no')].'*'.$value.'*'.$company_name; ?>" />
                                   
                                    </td>
									<td width="30" align="center"><?=$i; ?></td>
									<td width="50" align="center"><?=$company_arr[$company_name];?></td>
									<td width="50"><a href='##' onClick="<?=$function; ?>"><?=$row[csf('job_no_prefix_num')]; ?></a></td>
                                    
									
									
									
									
									<td width="60" align="right"><p style="color:<?=$td_color; ?>"><?= number_format($cm_cost,4); ?></p></td>
									<td width="60"  title="Contribution Margin/Costing Per/SMV(<?= $job_epm_contribute_margin_arr[$row[csf('job_no')]];?>)" align="right"><p><?= number_format($job_epm_arr[$row[csf('job_no')]]/$row[csf('sew_smv')],4); ?></p></td>
									<td width="60" align="right"><?= $row[csf('sew_smv')]; ?></td>							
									
									
									
									
									
									
									
									
									<td width="40" style="word-break:break-all;"><?=$row[csf('year')]; ?></td>
                                    <td width="110" style="word-break:break-all;"><?=$buyer_arr[$row[csf('buyer_name')]]; ?></td>
                                    <td width="130" align="center" style="word-break:break-all;"><a href='##' onClick="<?=$function2; ?>"><?=$row[csf('style_ref_no')]; ?></a></td>
                                    <td width="70" align="center"><? if($row[csf('costing_date')]!="0000-00-00") echo change_date_format($row[csf('costing_date')]); ?>&nbsp;</td>
                                    <td align="center" width="70"><a href="##" onClick="openImgFile('<?=$row[csf('job_no')]; ?>','img');">View</a></td>
                                    <td width="140" style="word-break:break-all"><? if($approval_type==1) echo $unapproved_request_arr[$value]; ?> </td>
                                    <td width="65" style="word-break:break-all;"><? echo ucfirst ($user_arr[$row[csf('inserted_by')]]); ?>&nbsp;</td>
                                    <td width="80" align="center"><? if($row[csf('approved_date')]!="0000-00-00") echo change_date_format($row[csf('approved_date')]); ?>&nbsp;</td>
                                    <td> <input style="width:150px;" type="text" class="text_boxes"  name="txtCause_<?=$i; ?>" id="txtCause_<?=$i; ?>" placeholder="Browse" onClick="openmypage_refusing_cause('requires/sourcing_approval_controller.php?action=refusing_cause_popup','Refusing Cause','<?=$row[csf('id')]; ?>','<?=$row[csf('sourcinng_refusing_cause')]; ?>');" value="<?=$row[csf('sourcinng_refusing_cause')]; ?>"/></td>
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
						$denyBtn="";
						if($approval_type==2) $denyBtn=""; else $denyBtn=" display:none";
			}//end company loof;
	
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="1220" class="rpt_table" align="left">
				<tfoot>
                    <td width="40" align="center"><input type="checkbox" id="all_check" onClick="check_all('all_check');" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<?=$i; ?>,<?=$approval_type; ?>);"/>&nbsp;&nbsp;&nbsp;<input type="button" value="<? if($approval_type==2) echo "Deny"; ?>" class="formbutton" style="width:100px;<?=$denyBtn; ?>" onClick="submit_approved(<?=$i; ?>,5);"/></td>
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





	
	$appCompanyArr=array();$appIdArr=array();$appNoArr=array();
	foreach(explode(',',$mst_id_company_ids) as $ic){
		list($bno,$bid,$company)=explode('*',$ic);
		$appCompanyArr[$company]=$company;
		$appIdArr[$company][$bid]=$bid;
		$appNoArr[$company][$bno]=$bno;
	}
	
	foreach($appCompanyArr as $cbo_company_name){
		$booking_nos="'".implode("','",$appNoArr[$cbo_company_name])."'";
		$booking_ids=implode(',',$appIdArr[$cbo_company_name]);
	
		$msg=''; $flag=''; $response='';
		$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
		$user_id_approval=($txt_alter_user_id=='')?$user_id:$txt_alter_user_id;


		$sql="select a.SOURCING_READY_TO_APPROVED from WO_PRE_COST_MST a where a.id in($booking_ids)";
		$sqlResult=sql_select( $sql );
		foreach ($sqlResult as $row)
		{	
			if($row['SOURCING_READY_TO_APPROVED'] != 1 ){echo "Ready to approved NO is not allow";die;}
		}



		$userSequenceNo=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id_approval and is_deleted=0");
		$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");

		$buyer_arr=return_library_array( "select b.id, a.buyer_name from wo_pre_cost_mst b, wo_po_details_master a where a.job_no=b.job_no and a.is_deleted=0 and  a.status_active=1 and b.status_active=1 and  b.is_deleted=0 and b.id in ($booking_ids)", "id", "buyer_name"  );
	
		if($approval_type==2)
		{
			$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id_approval and is_deleted=0");// and bypass=2
			$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0"); //and bypass=2

			//echo $min_sequence_no .'!='. $user_sequence_no;die;
			if($min_sequence_no != $user_sequence_no)
			{
				$sql = sql_select("select b.buyer_id as buyer_id,b.sequence_no from electronic_approval_setup b where b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no < $user_sequence_no and b.is_deleted=0 and bypass=2 group by b.buyer_id,b.sequence_no order by b.sequence_no ASC");
				
				foreach ($sql as $key => $buyerID) {
					$allUserBuyersArr[$buyerID[csf('sequence_no')]] = $buyerID[csf('buyer_id')];
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
	
				$sql = sql_select("select b.buyer_id as buyer_id from electronic_approval_setup b where b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no = $user_sequence_no and b.is_deleted=0 group by b.buyer_id"); $userBuyer=0;
				foreach ($sql as $key => $buyerID) {
					if($buyerID[csf('buyer_id')]!='')
					{
						$currUserBuyersArr[$user_sequence_no] = $buyerID[csf('buyer_id')];
					}
					else
					{
						$currUserBuyersArr[$user_sequence_no] = chop($buyerIds,',');;
					}
				}
				
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
				
				//echo count($userBuyer);die;
				
				if(count($match_seq)>0 || $userBuyer==1)
				{
					$previous_user_seq = implode(',', $match_seq);
					$previous_user_app = sql_select("select id from approval_history where mst_id in($booking_ids) and entry_form=47 and sequence_no <$user_sequence_no and current_approval_status=1 group by id");
					
					if(count($previous_user_app)==0)
					{
						$previous_user_app = sql_select("select id from approval_history where mst_id in($booking_ids) and entry_form=47 and sequence_no in ($previous_user_seq) and current_approval_status=1 group by id");
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
			$is_not_last_user=return_field_value("a.sequence_no","electronic_approval_setup a"," a.company_id=$cbo_company_name and a.page_id=$menu_id and a.sequence_no>$user_sequence_no and a.bypass=2 and a.is_deleted=0 $buyer_id_cond","sequence_no");
	
			$partial_approval = "";
			if($is_not_last_user == "")
			{
				$sql = sql_select("select (b.buyer_id) as buyer_id from electronic_approval_setup b where b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2  group by b.buyer_id");
				foreach ($sql as $key => $buyerID) {
					$credentialUserBuyersArr[] = $buyerID[csf('buyer_id')];
				}
				$credentialUserBuyersArr = implode(',',$credentialUserBuyersArr);
				$credentialUserBuyersArr = explode(',',$credentialUserBuyersArr);
				$credentialUserBuyersArr = array_unique($credentialUserBuyersArr);
			}
			else
			{
				$check_user_buyer = sql_select("select b.user_id as user_id from user_passwd a, electronic_approval_setup b where a.id = b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 $buyer_id_cond  group by b.user_id");
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
	
			$response=$booking_ids;
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date,inserted_by,insert_date";
			$id=return_next_id( "id","approval_history", 1 ) ;
			
			$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=47 group by mst_id","mst_id","approved_no");
	
			$approved_status_arr = return_library_array("select id, sourcing_approved from wo_pre_cost_mst where id in($booking_ids)","id","sourcing_approved");
			$approved_no_array=array();
			$booking_ids_all=explode(",",$booking_ids);
			$booking_nos_all=explode(",",$booking_nos);
			$book_nos=''; //echo "10**";

			for($i=0;$i<count($booking_nos_all);$i++)
			{
				$val=$booking_nos_all[$i];
				$booking_id=$booking_ids_all[$i];
				$approved_no=0;
				$approved_no=$max_approved_no_arr[$booking_id]*1;
				//echo $max_approved_no_arr[$booking_id].'='.$approved_no.'='.$approved_status.'<br>';
				$approved_status=$approved_status_arr[$booking_id]*1;
				$buyer_id=$buyer_arr[$booking_id];
				if($approved_status==2 || $approved_status==0)
				{
					$approved_no=$approved_no+1;
					$approved_no_array[$val]=$approved_no;
					if($book_nos=="") $book_nos=$val; else $book_nos.=",".$val;
				}
				if($approved_status==0 && $approved_no=='') $approved_no=1;
	
				if($is_not_last_user == "")
				{
					if(in_array($buyer_id,$credentialUserBuyersArr))
					{
						$partial_approval=3;
					}
					else $partial_approval=1;
				}
				else
				{
					if(count($credentialUserBuyersArr)>0)
					{
						if(in_array($buyer_id,$credentialUserBuyersArr))
						{
							$partial_approval=3;
						}
						else $partial_approval=1;
					}
					else $partial_approval=3;
				}

				$booking_id_arr[]=$booking_id;
				$data_array_booking_update[$booking_id]=explode("*",($partial_approval));
	
				if($partial_approval==1)
				{
					$full_approve_booking_id_arr[]=$booking_id;
					$data_array_full_approve_booking_update[$booking_id]=explode("*",($user_id_approval."*'".$pc_date_time."'"));
				}
	
				if($data_array!="") $data_array.=",";
				$data_array.="(".$id.",47,".$booking_id.",".$approved_no.",'".$userSequenceNo."',1,".$user_id_approval.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')";
				$id=$id+1;
			}
	
			$flag=1; //echo "10**"; print_r($approved_no_array); die;
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
				
				$approved_string_mst2="CASE job_no_mst ".$approved_string." END";
				$approved_string_dtls2="CASE job_no_mst ".$approved_string." END";
				
				//------------wo_po_dtls_mst_his----------------------------------
				$sqljob="insert into wo_po_dtls_mst_his (id, job_id, approved_no, approval_page, garments_nature, job_no_prefix, job_no_prefix_num, job_no, quotation_id, order_repeat_no, company_name, buyer_name, style_ref_no, product_dept, product_code, location_name, style_description, ship_mode, region, team_leader, dealing_marchant, remarks, job_quantity, avg_unit_price, currency_id, total_price, packing, agent_name, product_category, order_uom, gmts_item_id, set_break_down, total_set_qnty, set_smv, is_deleted, status_active, inserted_by, insert_date, updated_by, update_date, pro_sub_dep, client_id, item_number_id, factory_marchant, qlty_label, is_excel, style_owner, booking_meeting_date, bh_merchant, copy_from, season_buyer_wise, is_repeat, repeat_job_no, ready_for_budget, working_location_id, gauge, fabric_composition, design_source_id, yarn_quality, season_year, brand_id, inquiry_id, body_wash_color, sustainability_standard, fab_material, quality_level, requisition_no, working_company_id, fit_id)
					select '', id, $approved_string_mst, 47, garments_nature, job_no_prefix, job_no_prefix_num, job_no, quotation_id, order_repeat_no, company_name, buyer_name, style_ref_no, product_dept, product_code, location_name, style_description, ship_mode, region, team_leader, dealing_marchant, remarks, job_quantity, avg_unit_price, currency_id, total_price, packing, agent_name, product_category, order_uom, gmts_item_id, set_break_down, total_set_qnty, set_smv, is_deleted, status_active, inserted_by, insert_date, updated_by, update_date, pro_sub_dep, client_id, item_number_id, factory_marchant, qlty_label, is_excel, style_owner, booking_meeting_date, bh_merchant, copy_from, season_buyer_wise, is_repeat, repeat_job_no, ready_for_budget, working_location_id, gauge, fabric_composition, design_source_id, yarn_quality, season_year, brand_id, inquiry_id, body_wash_color, sustainability_standard, fab_material, quality_level, requisition_no, working_company_id, fit_id
				from wo_po_details_master where job_no in ($book_nos)";
				//echo "10**".$sqljob;die;
				
				//------------wo_po_dtls_item_set_his----------------------------------
				$sqlsetitem="insert into wo_po_dtls_item_set_his (id, approval_page, set_dtls_id, approved_no, job_no, gmts_item_id, set_item_ratio, smv_pcs, smv_set, smv_pcs_precost, smv_set_precost, complexity, embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, quot_id, aop, aopseq, ws_id, job_id, bush, bushseq, peach, peachseq, yd, ydseq, printdiff, embrodiff, washdiff, spwdiff)
					select '', 47, id, $approved_string_mst, job_no, gmts_item_id, set_item_ratio, smv_pcs, smv_set, smv_pcs_precost, smv_set_precost, complexity, embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, quot_id, aop, aopseq, ws_id, job_id, bush, bushseq, peach, peachseq, yd, ydseq, printdiff, embrodiff, washdiff, spwdiff from wo_po_details_mas_set_details where job_no in ($book_nos)";
				//echo "10**".$sqlsetitem;die;
				
				//------------wo_po_break_down_his----------------------------------
				$sqlpo="insert into wo_po_break_down_his (id, approval_page, po_id, approved_no, job_no_mst, po_number, pub_shipment_date, excess_cut, po_received_date, po_quantity, unit_price, plan_cut, country_name, po_total_price, shipment_date, is_deleted, is_confirmed, details_remarks, delay_for, packing, grouping, projected_po_id, tna_task_from_upto, inserted_by, insert_date, updated_by, update_date, status_active, shiping_status, original_po_qty, factory_received_date, original_avg_price, pp_meeting_date, matrix_type, no_of_carton, actual_po_no, round_type, doc_sheet_qty, up_charge, pack_price, sc_lc, with_qty, extended_ship_date, sewing_company_id, sewing_location_id, extend_ship_mode, sea_discount, air_discount, job_id, pack_handover_date, etd_ldd, file_year, file_no, rfi_date)
					select '', 47, id, $approved_string_mst2, job_no_mst, po_number, pub_shipment_date, excess_cut, po_received_date, po_quantity, unit_price, plan_cut, country_name, po_total_price, shipment_date, is_deleted, is_confirmed, details_remarks, delay_for, packing, grouping, projected_po_id, tna_task_from_upto, inserted_by, insert_date, updated_by, update_date, status_active, shiping_status, original_po_qty, factory_received_date, original_avg_price, pp_meeting_date, matrix_type, no_of_carton, actual_po_no, round_type, doc_sheet_qty, up_charge, pack_price, sc_lc, with_qty, extended_ship_date, sewing_company_id, sewing_location_id, extend_ship_mode, sea_discount, air_discount, job_id, pack_handover_date, txt_etd_ldd, file_year, file_no, rfi_date from wo_po_break_down where job_no_mst in ($book_nos) and is_deleted=0";
				//echo "10**".$sqlpo;die;
				
				//------------wo_po_color_size_his----------------------------------
				$sqlcolorsize="insert into wo_po_color_size_his (id, approval_page, color_size_id, approved_no, po_break_down_id, job_no_mst, color_mst_id, size_mst_id, item_mst_id, country_mst_id, article_number, item_number_id, country_id, size_number_id, color_number_id, order_quantity, order_rate, order_total, excess_cut_perc, plan_cut_qnty, is_deleted, is_used, inserted_by, insert_date, updated_by, update_date, status_active, is_locked, cutup_date, cutup, country_ship_date, shiping_status, country_remarks, country_type, packing, color_order, size_order, ultimate_country_id, code_id, ul_country_code, pack_qty, pcs_per_pack, pack_type, pcs_pack, assort_qty, solid_qty, barcode_suffix_no, barcode_year, barcode_no, job_id, extended_ship_date, proj_qty, proj_amt, country_avg_rate)
					Select '', 47, id, $approved_string_mst2, po_break_down_id, job_no_mst, color_mst_id, size_mst_id, item_mst_id, country_mst_id, article_number, item_number_id, country_id, size_number_id, color_number_id, order_quantity, order_rate, order_total, excess_cut_perc, plan_cut_qnty, is_deleted, is_used, inserted_by, insert_date, updated_by, update_date, status_active, is_locked, cutup_date, cutup, country_ship_date, shiping_status, country_remarks, country_type, packing, color_order, size_order, ultimate_country_id, code_id, ul_country_code, pack_qty, pcs_per_pack, pack_type, pcs_pack, assort_qty, solid_qty, barcode_suffix_no, barcode_year, barcode_no, job_id, extended_ship_date, proj_qty, proj_amt, country_avg_rate from wo_po_color_size_breakdown where job_no_mst in ($book_nos) and is_deleted=0 ";	
				//echo "10**".$sqlcolorsize;die;
				
				//------------wo_pre_cost_mst_histry----------------------------------
				$sqlBom="insert into wo_pre_cost_mst_histry(id, approved_no, pre_cost_mst_id, garments_nature, job_no, costing_date, incoterm, incoterm_place, machine_line, prod_line_hr, costing_per, remarks, copy_quatation, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, efficiency_wastage_percent, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, ready_to_approved, budget_minute, sew_efficiency_source, entry_from, job_id, refusing_cause, sourcing_date, sourcing_inserted_date, sourcing_inserted_by, sourcing_update_date, sourcing_updated_by, sourcing_ready_to_approved, sourcing_approved, sourcing_remark, main_fabric_co, sourcinng_refusing_cause, approved_sequ_by, isorder_change, ready_to_source, approval_page)
					select '', $approved_string_mst, id, garments_nature, job_no, costing_date, incoterm, incoterm_place, machine_line, prod_line_hr, costing_per, remarks, copy_quatation, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, efficiency_wastage_percent, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, ready_to_approved, budget_minute, sew_efficiency_source, entry_from, job_id, refusing_cause, sourcing_date, sourcing_inserted_date, sourcing_inserted_by, sourcing_update_date, sourcing_updated_by, sourcing_ready_to_approved, sourcing_approved, sourcing_remark, main_fabric_co, sourcinng_refusing_cause, approved_sequ_by, isorder_change, ready_to_source, 47
				from wo_pre_cost_mst where job_no in ($book_nos)";
				//echo "10**".$sqlBom;die;
				
				//------------wo_pre_cost_dtls_histry----------------------------------
				$sql_bom_dtls="insert into wo_pre_cost_dtls_histry(id, approved_no, pre_cost_dtls_id, job_no, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, commission, commission_percent,	lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, cost_pcs_set, cost_pcs_set_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche, margin_pcs_bom, margin_bom_per, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, depr_amor_pre_cost, depr_amor_po_price, interest_cost, interest_percent, incometax_cost, incometax_percent, deffdlc_cost, deffdlc_percent, design_cost, design_percent, studio_cost, studio_percent, job_id, sourcing_inserted_date, sourcing_inserted_by, sourcing_update_date, sourcing_updated_by, sourcing_fabric_cost, sourcing_trims_cost, sourcing_embel_cost, sourcing_wash_cost, approval_page)
						select '', $approved_string_dtls, id, job_no, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, commission, commission_percent,	lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, cost_pcs_set, cost_pcs_set_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche, margin_pcs_bom, margin_bom_per, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, depr_amor_pre_cost, depr_amor_po_price, interest_cost, interest_percent, incometax_cost, incometax_percent, deffdlc_cost, deffdlc_percent, design_cost, design_percent, studio_cost, studio_percent, job_id, sourcing_inserted_date, sourcing_inserted_by, sourcing_update_date, sourcing_updated_by, sourcing_fabric_cost, sourcing_trims_cost, sourcing_embel_cost, sourcing_wash_cost, 47 from wo_pre_cost_dtls where job_no in ($book_nos)";
				//echo "10**".$sql_bom_dtls;die;
	
				//------------wo_pre_cost_fabric_cost_dtls_h----------------------------------
				$sql_fabric_cost_dtls="insert into wo_pre_cost_fabric_cost_dtls_h(id, approved_no, pre_cost_fabric_cost_dtls_id, job_no, item_number_id, body_part_id, fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, color_size_sensitive, color, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, company_id, costing_per, consumption_basis, process_loss_method, cons_breack_down, msmnt_break_down, color_break_down, yarn_breack_down, marker_break_down, width_dia_type, avg_cons_yarn, gsm_weight_yarn, uom, body_part_type, sample_id, job_id, gsm_weight_type, nominated_supp_multi, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, sourcing_nominated_supp, quotdtlsid, budget_on, source_id, is_synchronized, approval_page)
					select '', $approved_string_dtls, id, job_no, item_number_id, body_part_id, fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, color_size_sensitive, color, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, company_id, costing_per, consumption_basis, process_loss_method, cons_breack_down, msmnt_break_down, color_break_down, yarn_breack_down, marker_break_down, width_dia_type, avg_cons_yarn, gsm_weight_yarn, uom, body_part_type, sample_id, job_id, gsm_weight_type, nominated_supp_multi, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, sourcing_nominated_supp, quotdtlsid, budget_on, source_id, is_synchronized, 47 from wo_pre_cost_fabric_cost_dtls where job_no in ($book_nos)";
				//echo "10**".$sql_fabric_cost_dtls;die;
				
				//------------WO_PRE_FAB_AVG_CON_DTLS_H----------------------------------
				$sql_fabric_cons_dtls="insert into wo_pre_fab_avg_con_dtls_h(id, approved_no, fab_con_id, pre_cost_fabric_cost_dtls_id, job_no, po_break_down_id, color_number_id, gmts_sizes, dia_width, item_size, cons, process_loss_percent, requirment, pcs, color_size_table_id, body_length, body_sewing_margin, body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin, front_rise_length, front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin, total, marker_dia, marker_yds, marker_inch, gmts_pcs, marker_length, net_fab_cons, rate, amount, remarks, length, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, length_sleeve, width_sleeve, job_id, fina_char, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, cons_pcs, item_color, approval_page)
					select '', $approved_string_dtls, id,  pre_cost_fabric_cost_dtls_id, job_no, po_break_down_id, color_number_id, gmts_sizes, dia_width, item_size, cons, process_loss_percent, requirment, pcs, color_size_table_id, body_length, body_sewing_margin, body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin, front_rise_length, front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin, total, marker_dia, marker_yds, marker_inch, gmts_pcs, marker_length, net_fab_cons, rate, amount, remarks, length, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, length_sleeve, width_sleeve, job_id, fina_char, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, cons_pcs, item_color, 47 from wo_pre_cos_fab_co_avg_con_dtls where job_no in ($book_nos)";
				//echo "10**"$sql_fabric_cons_dtls;die;
				
				//-------------wo_pre_fab_concolor_dtls_h-----------------------------------------------
				$sql_concolor_cst="insert into wo_pre_fab_concolor_dtls_h (id, approved_no, contrast_id, pre_cost_fabric_cost_dtls_id, job_no, gmts_color_id, gmts_color, contrast_color_id, job_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, approval_page)
					select
					'', $approved_string_dtls, id, pre_cost_fabric_cost_dtls_id, job_no, gmts_color_id, gmts_color, contrast_color_id, job_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, 47 from wo_pre_cos_fab_co_color_dtls where job_no in ($book_nos)";
				//echo "10**".$sql_concolor_cst;die;
				
				//-------------wo_pre_stripe_color_h-----------------------------------------------
				$sql_stripecolor_cst="insert into wo_pre_stripe_color_h (id, approved_no, stripe_id, job_no, item_number_id, pre_cost_fabric_cost_dtls_id, color_number_id, stripe_color, measurement, uom, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, totfidder, fabreq, fabreqtotkg, yarn_dyed, sales_dtls_id, size_number_id, po_break_down_id, lenth, width, sample_color, sample_per, cons, excess_per, job_id, stripe_type, approval_page)
					select
					'', $approved_string_dtls, id, job_no, item_number_id, pre_cost_fabric_cost_dtls_id, color_number_id, stripe_color, measurement, uom, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, totfidder, fabreq, fabreqtotkg, yarn_dyed, sales_dtls_id, size_number_id, po_break_down_id, lenth, width, sample_color, sample_per, cons, excess_per, job_id, stripe_type, 47 from wo_pre_stripe_color where job_no in ($book_nos)";
				//echo "10**".$sql_stripecolor_cst;die;
	
				//-------------wo_pre_cost_fab_yarn_cst_dtl_h-----------------------------------------------
				$sql_precost_fab_yarn_cst="insert into wo_pre_cost_fab_yarn_cst_dtl_h (id, approved_no, pre_cost_fab_yarn_cost_dtls_id, fabric_cost_dtls_id, job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, avg_cons_qnty, supplier_id, color, consdznlbs, rate_dzn, job_id, yarn_finish, yarn_spinning_system, certification, approval_page)
					select
					'', $approved_string_dtls, id, fabric_cost_dtls_id, job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, avg_cons_qnty, supplier_id, color, consdznlbs, rate_dzn, job_id, yarn_finish, yarn_spinning_system, certification, 47 from wo_pre_cost_fab_yarn_cost_dtls where job_no in ($book_nos)";
					//echo "10**".$sql_precost_fab_yarn_cst;die;
					
				//-----------------------------------------wo_pre_cost_fab_con_cst_dtls_h-----------------------------------------
				$sql_precost_fab_con_cst_dtls="insert into  wo_pre_cost_fab_con_cst_dtls_h(id, approved_no, pre_cost_fab_conv_cst_dtls_id, job_no, fabric_description, cons_process, req_qnty, charge_unit, amount, color_break_down, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, avg_req_qnty, process_loss, job_id, approval_page)
					select '', $approved_string_dtls, id, job_no, fabric_description, cons_process, req_qnty, charge_unit, amount, color_break_down, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, avg_req_qnty, process_loss, job_id, 47 from wo_pre_cost_fab_conv_cost_dtls where job_no in ($book_nos)";
				//echo "10**".$sql_precost_fab_con_cst_dtls;die;
					
				//-------------------  WO_PRE_CONV_COLOR_DTLS_H------------------------------------------------------------
				$sql_conv_color_dtls="insert into wo_pre_conv_color_dtls_h(id, approved_no, conv_color_id, fabric_cost_dtls_id, conv_cost_dtls_id, job_no, job_id, convchargelibraryid, gmts_color_id, fabric_color_id, cons, unit_charge, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, approval_page)
					select '', $approved_string_dtls, id, fabric_cost_dtls_id, conv_cost_dtls_id, job_no, job_id, convchargelibraryid, gmts_color_id, fabric_color_id, cons, unit_charge, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, 47 from wo_pre_cos_conv_color_dtls where job_no in ($book_nos)";
				//echo "10**".$sql_conv_color_dtls;die;
					
				//------------wo_pre_cost_trim_cost_dtls_his------------------------------	----------------------
				$sql_precost_trim_cost_dtls="insert into  wo_pre_cost_trim_cost_dtls_his(id, approved_no, pre_cost_trim_cost_dtls_id, job_no, trim_group, description, brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, cons_breack_down, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, remark, country, calculatorstring, unit_price, inco_term, add_price, seq, job_id, nominated_supp_multi, material_source, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, sourcing_nominated_supp, tot_cons, ex_per, quotdtlsid, source_id, item_print, is_synchronized, approval_page)
					select '', $approved_string_dtls, id, job_no, trim_group, description, brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, cons_breack_down, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, remark, country, calculatorstring, unit_price, inco_term, add_price, seq, job_id, nominated_supp_multi, material_source, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, sourcing_nominated_supp, tot_cons, ex_per, quotdtlsid, source_id, item_print, is_synchronized, 47 from wo_pre_cost_trim_cost_dtls  where job_no in ($book_nos)";
				//echo "10**".$sql_precost_trim_cost_dtls;die;
	
				//---------------------------wo_pre_cost_trim_co_cons_dtl_h--------------------------------------------------
				$sql_precost_trim_co_cons_dtl="insert into wo_pre_cost_trim_co_cons_dtl_h( id, approved_no, pre_cost_trim_co_cons_dtls_id, wo_pre_cost_trim_cost_dtls_id, job_no, po_break_down_id, item_size, cons, place, pcs, country_id, excess_per, tot_cons, ex_cons, item_number_id, color_number_id, item_color_number_id, size_number_id, rate, amount, gmts_pcs, color_size_table_id, job_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, rate_cal_data, sourcing_rate, sourcing_amount, sourcing_update_date, sourcing_updated_by, sourcing_inserted_date, sourcing_inserted_by, cons_pcs, approval_page)
					select '', $approved_string_dtls, id, wo_pre_cost_trim_cost_dtls_id, job_no, po_break_down_id, item_size, cons, place, pcs, country_id, excess_per, tot_cons, ex_cons, item_number_id, color_number_id, item_color_number_id, size_number_id, rate, amount, gmts_pcs, color_size_table_id, job_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, rate_cal_data, sourcing_rate, sourcing_amount, sourcing_update_date, sourcing_updated_by, sourcing_inserted_date, sourcing_inserted_by, cons_pcs, 47 from wo_pre_cost_trim_co_cons_dtls where job_no in ($book_nos)";
				//echo "10**".$sql_precost_trim_co_cons_dtl;die;
	
				//-------------------  wo_pre_cost_embe_cost_dtls_his------------------------------------------------------------
				$sql_precost_embe_cost_dtls="insert into wo_pre_cost_embe_cost_dtls_his(id, approved_no, pre_cost_embe_cost_dtls_id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, budget_on, country, body_part_id, job_id, nominated_supp_multi, sourcing_nominated_supp, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, quotdtlsid, is_synchronized, approval_page)
					select '', $approved_string_dtls, id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, budget_on, country, body_part_id, job_id, nominated_supp_multi, sourcing_nominated_supp, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, quotdtlsid, is_synchronized, 47 from wo_pre_cost_embe_cost_dtls where job_no in ($book_nos)";
				//echo "10**".$sql_precost_embe_cost_dtls;die;
					
				//-------------------  WO_PRE_EMB_AVG_CON_DTLS_H------------------------------------------------------------
				$sql_embe_cons_dtls="insert into wo_pre_emb_avg_con_dtls_h(id, approved_no, emb_cons_id, pre_cost_emb_cost_dtls_id, job_no, po_break_down_id, item_number_id, color_number_id, size_number_id, requirment, rate, amount, gmts_pcs, color_size_table_id, rate_lib_id, country_id, job_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, approval_page)
					select '', $approved_string_dtls, id, pre_cost_emb_cost_dtls_id, job_no, po_break_down_id, item_number_id, color_number_id, size_number_id, requirment, rate, amount, gmts_pcs, color_size_table_id, rate_lib_id, country_id, job_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, 47 from wo_pre_cos_emb_co_avg_con_dtls where job_no in ($book_nos)";
				//echo "10**".$sql_embe_cons_dtls;die;
				
				//----------------------------------wo_pre_cost_comarc_cost_dtls_h----------------------------------------
				$sql_comarc_cost_dtls="insert into wo_pre_cost_comarc_cost_dtls_h( id, approved_no, pre_cost_comarci_cost_dtls_id, job_no, item_id, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, job_id, approval_page)
					select '', $approved_string_dtls, id, job_no, item_id, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, job_id, 47 from wo_pre_cost_comarci_cost_dtls where job_no in ($book_nos)";
				//echo "10**".$sql_comarc_cost_dtls;die;
	
				//-------------------------------------wo_pre_cost_commis_cost_dtls_h-------------------------------------------
				$sql_commis_cost_dtls="insert into wo_pre_cost_commis_cost_dtls_h (id, approved_no, pre_cost_commiss_cost_dtls_id, job_no, particulars_id, commission_base_id, commision_rate, commission_amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, job_id, approval_page)
					select '', $approved_string_dtls, id, job_no, particulars_id, commission_base_id, commision_rate, commission_amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, job_id, 47 from wo_pre_cost_commiss_cost_dtls where job_no in ($book_nos)";
				//echo "10**".$sql_commis_cost_dtls;die;
				
				//-------------------------------------wo_pre_cost_sum_dtls_histroy-------------------------------------------
				$sql_sum_dtls="insert into wo_pre_cost_sum_dtls_histroy (id, approved_no, pre_cost_sum_dtls_id, job_no, fab_yarn_req_kg, fab_woven_req_yds, fab_knit_req_kg, fab_amount, avg, yarn_cons_qnty, yarn_amount, conv_req_qnty, conv_charge_unit, conv_amount, trim_cons, trim_rate, trim_amount, emb_amount, comar_rate, comar_amount, commis_rate, commis_amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, fab_woven_fin_req_yds, fab_knit_fin_req_kg, job_id, approval_page)
				select '', $approved_string_dtls, id, job_no, fab_yarn_req_kg, fab_woven_req_yds, fab_knit_req_kg, fab_amount, avg, yarn_cons_qnty, yarn_amount, conv_req_qnty, conv_charge_unit, conv_amount, trim_cons, trim_rate, trim_amount, emb_amount, comar_rate, comar_amount, commis_rate, commis_amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, fab_woven_fin_req_yds, fab_knit_fin_req_kg, job_id, 47 from wo_pre_cost_sum_dtls where job_no in ($book_nos)";
				//echo "10**".$sql_sum_dtls;die;
				
				if(count($sqljob)>0)//JOB
				{
					$rID=execute_query($sqljob,1);
					if($rID==1 && $flag==1) $flag=1; else $flag=0;
				}
				
				if(count($sqlsetitem)>0)//JOB SET ITEM
				{
					$rID0=execute_query($sqlsetitem,1);
					if($rID0==1 && $flag==1) $flag=1; else $flag=0;
				}
				
				if(count($sqlpo)>0)//JOB PO
				{
					$rID1=execute_query($sqlpo,1);
					if($rID1==1 && $flag==1) $flag=1; else $flag=0;
				}
				
				if(count($sqlcolorsize)>0)//JOB PO COLOR SIZE
				{
					$rID2=execute_query($sqlcolorsize,1);
					if($rID2==1 && $flag==1) $flag=1; else $flag=0;
				}
				
				if(count($sqlBom)>0)//BOM MST
				{
					$rID3=execute_query($sqlBom,1);
					if($rID3==1 && $flag==1) $flag=1; else $flag=0;
				}
				
				if(count($sql_bom_dtls)>0)//BOM DTLS
				{
					$rID4=execute_query($sql_bom_dtls,1);
					if($rID4==1 && $flag==1) $flag=1; else $flag=0;
				}
				
				if(count($sql_fabric_cost_dtls)>0)//BOM FABRIC DTLS
				{
					$rID5=execute_query($sql_fabric_cost_dtls,1);
					if($rID5==1 && $flag==1) $flag=1; else $flag=0;
				}
				
				if(count($sql_fabric_cons_dtls)>0)//BOM FABRIC CONS
				{
					$rID6=execute_query($sql_fabric_cons_dtls,1);
					if($rID6==1 && $flag==1) $flag=1; else $flag=0;
				}
				
				if(count($sql_concolor_cst)>0)//BOM FABRIC CONTRAST COLOR
				{
					$rID7=execute_query($sql_concolor_cst,1);
					if($rID7==1 && $flag==1) $flag=1; else $flag=0;
				}
				
				if(count($sql_stripecolor_cst)>0)//BOM FABRIC STRIPE COLOR
				{
					$rID8=execute_query($sql_stripecolor_cst,1);
					if($rID8==1 && $flag==1) $flag=1; else $flag=0;
				}
				
				if(count($sql_precost_fab_yarn_cst)>0)//BOM YARN
				{
					$rID9=execute_query($sql_precost_fab_yarn_cst,1);
					if($rID9==1 && $flag==1) $flag=1; else $flag=0;
				}
				
				if(count($sql_precost_fab_con_cst_dtls)>0)//BOM CONV COST
				{
					$rID10=execute_query($sql_precost_fab_con_cst_dtls,1);
					if($rID10==1 && $flag==1) $flag=1; else $flag=0;
				}
				
				if(count($sql_conv_color_dtls)>0)//BOM CONV COLOR
				{
					$rID11=execute_query($sql_conv_color_dtls,1);
					if($rID11==1 && $flag==1) $flag=1; else $flag=0;
				}
				
				if(count($sql_precost_trim_cost_dtls)>0)//BOM TRIM
				{
					$rID12=execute_query($sql_precost_trim_cost_dtls,1);
					if($rID12==1 && $flag==1) $flag=1; else $flag=0;
				}
				
				if(count($sql_precost_trim_co_cons_dtl)>0)//BOM TRIM CONS
				{
					$rID13=execute_query($sql_precost_trim_co_cons_dtl,1);
					if($rID13==1 && $flag==1) $flag=1; else $flag=0;
				}
				
				if(count($sql_precost_embe_cost_dtls)>0)//BOM EMB
				{
					$rID14=execute_query($sql_precost_embe_cost_dtls,1);
					if($rID14==1 && $flag==1) $flag=1; else $flag=0;
				}
				
				if(count($sql_embe_cons_dtls)>0)//BOM EMB CONS
				{
					$rID15=execute_query($sql_embe_cons_dtls,1);
					if($rID15==1 && $flag==1) $flag=1; else $flag=0;
				}
				
				if(count($sql_comarc_cost_dtls)>0)//BOM COMMARCIAL
				{
					$rID16=execute_query($sql_comarc_cost_dtls,1);
					if($rID16==1 && $flag==1) $flag=1; else $flag=0;
				}
				
				if(count($sql_commis_cost_dtls)>0)//BOM COMMISION
				{
					$rID17=execute_query($sql_commis_cost_dtls,1);
					if($rID17==1 && $flag==1) $flag=1; else $flag=0;
				}
				if(count($sql_sum_dtls)>0)//BOM SUM DTLS
				{
					$rID18=execute_query($sql_sum_dtls,1);
					if($rID18==1 && $flag==1) $flag=1; else $flag=0;
				}
			}
			
			//echo "10**".$rID.'J-'.$rID0.'I-'.$rID1.'P-'.$rID2.'CZ-'.$rID3.'BM-'.$rID4.'BD-'.$rID5.'F-'.$rID6.'FC-'.$rID7.'FCC-'.$rID8.'FSC-'.$rID9.'Y-'.$rID10.'CV-'.$rID11.'CC-'.$rID12.'T-'.$rID13.'TC-'.$rID14.'E-'.$rID15.'EC-'.$rID16.'CL-'.$rID17.'CO-'.$rID18.'SUM-'.$flag; die;
			$field_array_booking_update = "sourcing_approved";
			$rID19=execute_query(bulk_update_sql_statement( "wo_pre_cost_mst", "id", $field_array_booking_update, $data_array_booking_update, $booking_id_arr));
	
			if($flag==1)
			{
				if($rID19) $flag=1; else $flag=0;
			}
			//echo $flag; die;
			
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=47 and mst_id in ($booking_ids)";
			$rIDapp=execute_query($query,1);
			
			if($flag==1)
			{
				if($rIDapp) $flag=1; else $flag=0;
			}
			
			$rID20=sql_insert("approval_history",$field_array,$data_array,0);
			if($flag==1)
			{
				if($rID20) $flag=1; else $flag=0;
			}
			if($flag==1) $msg='19'; else $msg='21';
		}
		else if($approval_type==1)
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
			
			$next_user_app = sql_select("select id from approval_history where mst_id in($booking_ids) and entry_form=47 and sequence_no >$userSequenceNo and current_approval_status=1 group by id");
					
			if(count($next_user_app)>0)
			{
				echo "25**unapproved"; 
				disconnect($con);
				die;
			}
	
			$rID=sql_multirow_update("wo_pre_cost_mst","sourcing_approved*sourcing_ready_to_approved",'2*0',"id",$booking_ids,0);
			if($rID) $flag=1; else $flag=0;
		
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=47 and mst_id in ($booking_ids)";
			$rID2=execute_query($query,1);
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}
	
			$unapproved_status="UPDATE fabric_booking_approval_cause SET status_active=0,is_deleted=1 WHERE entry_form=47 and approval_type=2 and is_deleted=0 and status_active=1 and booking_id in ($booking_ids)";
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
		else if($approval_type==5)
		{
			$sqlBookinghistory="select id, mst_id from approval_history where current_approval_status=1 and entry_form=47 and mst_id in ($booking_ids) ";
			
			$nameArray=sql_select($sqlBookinghistory); $bookidstr=""; $approval_id="";
			foreach ($nameArray as $row)
			{
				if($bookidstr=="") $bookidstr=$row[csf('mst_id')]; else $bookidstr.=','.$row[csf('mst_id')];
				if($approval_id=="") $approval_id=$row[csf('id')]; else $approval_id.=','.$row[csf('id')];
			}
			$appBookNoId=implode(",",array_filter(array_unique(explode(",",$bookidstr))));
			$approval_ids=implode(",",array_filter(array_unique(explode(",",$approval_id))));
		
		
			$rID=sql_multirow_update("wo_pre_cost_mst","sourcing_approved*sourcing_ready_to_approved",'2*0',"id",$booking_ids,0);
			if($rID) $flag=1; else $flag=0;
	
			if($approval_ids!="")
			{
				$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=47 and current_approval_status=1 and id in ($approval_ids)";
				//echo "10**".$query;
				$rID2=execute_query($query,1);
				if($flag==1)
				{
					if($rID2) $flag=1; else $flag=0;
				}
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
	
	}//end company loof;
	
	
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
                    	<!--<td align="center"><?//=$row[csf('image_location')];?></td>-->
                    	<td align="center"><img width="300px" height="180px" src="../../<?=$row[csf('image_location')]; ?>" /></td>
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
		echo create_list_view("list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr, "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
		?>
	</form>
	<script language="javascript" type="text/javascript">
	  setFilterGrid("tbl_style_ref");
	</script>
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
			http.open("POST","sourcing_approval_controller.php",true);
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
				alert("Data saved successfully");
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
						<input type="text" name="txt_refusing_cause" id="txt_refusing_cause" class="text_boxes" style="width:320px;height: 100px;" value="<?=$sourcinng_refusing_cause; ?>" />
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
		$get_history = sql_select("SELECT id from approval_history where mst_id='$quo_id' and entry_form =47 and current_approval_status=1");
		$id=return_next_id( "id", "refusing_cause_history", 1);
		$field_array = "id, entry_form, mst_id, refusing_reason,inserted_by, insert_date";
		$data_array = "(".$id.",47,".$quo_id.",'".$refusing_cause."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		//echo "10**insert into refusing_cause_history (".$field_array.") values ".$data_array; die;
		$field_array_update ="un_approved_by*un_approved_date*current_approval_status*un_approved_reason* updated_by*update_date";
		$data_array_update = "".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*".$refusing_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_insert("refusing_cause_history",$field_array,$data_array,1);
		$rID2=execute_query("update wo_pre_cost_mst set sourcing_ready_to_approved=0, sourcing_approved=0, sourcinng_refusing_cause='".$refusing_cause."', sourcing_updated_by=".$_SESSION['logic_erp']['user_id'].", sourcing_update_date = '".$pc_date_time."' where id='$quo_id'");
		//echo "10**update wo_pre_cost_mst set sourcing_ready_to_approved=0, sourcing_approved=0, sourcinng_refusing_cause='".$refusing_cause."', sourcing_updated_by=".$_SESSION['logic_erp']['user_id'].", sourcing_update_date = '".$pc_date_time."' where id='$quo_id'"; die;
		$rID3=1;
		if(count($get_history)>0)
		{
			$rID3=execute_query("update approval_history set un_approved_by=".$_SESSION['logic_erp']['user_id'].", un_approved_date='".$pc_date_time."', current_approval_status =0, un_approved_reason= '".$refusing_cause."', updated_by = ".$_SESSION['logic_erp']['user_id'].", update_date = '".$pc_date_time."' where mst_id='$quo_id' and entry_form =47 and current_approval_status=1");
		}
		//echo "10**".$rID.'='.$rID2.'='.$rID3; die;
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
?>
