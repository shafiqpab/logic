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
include('../../includes/class4/class.yarns.php');
include('../../includes/class4/class.conversions.php');
include('../../includes/class4/class.trims.php');
include('../../includes/class4/class.emblishments.php');
include('../../includes/class4/class.washes.php');
include('../../includes/class4/class.commercials.php');
include('../../includes/class4/class.commisions.php');
include('../../includes/class4/class.others.php');


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
if($action=="report_formate_setting")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=12 and report_id=178 and is_deleted=0 and status_active=1");
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
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );
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
$sub_dep_arr=return_library_array( "select id, sub_department_name from lib_pro_sub_deparatment", "id", "sub_department_name");

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$sequence_no='';
	$company_name=str_replace("'","",$cbo_company_name);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);

	$working_company=str_replace("'","",$cbo_working_company);
	$working_company_cond='';
	if(!empty($working_company))
	{
		$working_company_cond=" and a.working_company_id=$working_company";
	}
	$lib_company_name=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$user_id=($txt_alter_user_id !='')?$txt_alter_user_id:$user_id;

	if($company_name>0){$all_company_arr[$company_name]=$company_name;}
	else{
		$all_company_arr=return_library_array( "select company_id, company_id from electronic_approval_setup where page_id=$menu_id and user_id=$user_id and is_deleted=0",'company_id','company_id');
	}
?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:2102px; margin-top:10px">
        <legend>Pre-Costing Approval</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2122" class="rpt_table" >
                <thead>
                	<th width="30"></th>
                    <th width="40">SL</th>
                    <th width="50">Job No</th>
                    <th width="100" style="word-wrap: break-word;">Working Company</th>
                    <th width="70">Last Version</th>
                    <th width="70">Margin / PCS</th>
                    <th width="60">BOM Margin / PCS</th>
                    <th width="60">CM Cost</th>
                    <th width="60">EPM </th>
                    <th width="60">SMV</th>
                    <th width="60">Efficiency </th>
                    <th width="70">Internal Ref.</th>
                   	<th width="70">File No</th>
                    <th width="40">Year</th>
                    <th width="125">Buyer</th>
                    <th width="160">Style Ref.</th>
					<th width="100">Brand</th>
					<th width="100">Sub Dept</th>
                    <th width="65">Costing Date</th>
                    <th width="65">Est. Ship Date</th>
                    <th width="45">Image</th>
                    <th width="150">Unapproved Request</th>
                    <th width="65">Insert By</th>
                    <th width="80">Approved Date</th>
                    <th width="100">Refusing Cause</th>
					<th >Remarks</th>

                </thead>
            </table>
            <div style="width:2122px; overflow-y:scroll; max-height:460px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2102" class="rpt_table" id="tbl_list_search">
                    <tbody>
<?	
	$i=1;
	foreach($all_company_arr as $company_name=>$company){

	
	//echo $txt_internal_ref; echo $txt_file_no;
	$alter_user_cond='';
	$alter_user_cond_2='';
	if(!empty($txt_alter_user_id))
	{
		$alter_user_cond=" and c.approved_by=$txt_alter_user_id ";
		$alter_user_cond_2=" and h.approved_by=$txt_alter_user_id ";
	}
	if($txt_alter_user_id!="")
	{
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			$log_sql = sql_select("SELECT user_level,buyer_id,unit_id,is_data_level_secured FROM user_passwd WHERE id = '$txt_alter_user_id' AND valid = 1");
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
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	}
	$job_no=str_replace("'","",$txt_job_no);
	$file_no=str_replace("'","",$txt_file_no);
	$internal_ref=str_replace("'","",$txt_internal_ref);
	$job_year=str_replace("'","",$cbo_year);

	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and d.file_no='".trim($file_no)."' ";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and d.grouping='".trim($internal_ref)."' ";
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num='".trim($job_no)."' ";
	if ($job_no=="") $job_no_cond2=""; else $job_no_cond2=" and c.job_no_prefix_num='".trim($job_no)."' ";
	if ($job_year=="" || $job_year==0) $job_year_cond="";
	else
	{
		if($db_type==2) $job_year_cond=" and to_char(a.insert_date,'YYYY')='".trim($job_year)."' ";
		else $job_year_cond=" and YEAR(a.insert_date)='".trim($job_year)."' ";
	}

	$date_cond='';
	if(str_replace("'","",$txt_date)!="")
	{
		if($db_type==0) $txt_date="'".change_date_format(str_replace("'","",$txt_date), "yyyy-mm-dd", "-")."'";
		else if($db_type==2) $txt_date="'".change_date_format(str_replace("'","",$txt_date), "yyyy-mm-dd", "-",1)."'";
		
		if(str_replace("'","",$cbo_get_upto)==1) $date_cond=" and b.costing_date>$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==2) $date_cond=" and b.costing_date<=$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==3) $date_cond=" and b.costing_date=$txt_date";
		else $date_cond='';
	}

	$approval_type=str_replace("'","",$cbo_approval_type);
	if($previous_approved==1 && $approval_type==1)
	{
		$previous_approved_type=1;
	}
	//echo $menu_id;die;
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");

	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and is_deleted=0");
	
	$sql_vari_seting_copy_quotation=sql_select("select embellishment_budget_id,copy_quotation,variable_list from variable_order_tracking where company_name=$company_name and variable_list in(76,20) and status_active=1 and is_deleted=0");
	foreach($sql_vari_seting_copy_quotation as $row)
	{
		if($row[csf('variable_list')]==20)
		{
			$copy_quotation_id=$row[csf('copy_quotation')];
		}
		else{
			$budget_up_app_validation=$row[csf('embellishment_budget_id')];
		}
	}
	
	$buyer_ids_array = array();
	$buyerData = sql_select("select user_id, sequence_no, buyer_id from electronic_approval_setup where company_id=$company_name and page_id=$menu_id and is_deleted=0 ");//and bypass=2//echo "select user_id, sequence_no, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0 and bypass=2";
	foreach($buyerData as $row)
	{
		$buyer_ids_array[$row[csf('user_id')]]['u']=$row[csf('buyer_id')];
		$buyer_ids_array[$row[csf('sequence_no')]]['s']=$row[csf('buyer_id')];
	}
	//print_r($buyer_ids_array);
	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Pre-Costing.</font>";
		die;
	}

	 //echo $previous_approved;die;
	if($previous_approved==1 && $approval_type==1)
	{
		$sequence_no_cond=" and c.sequence_no<'$user_sequence_no'";

		$sql="select a.garments_nature, b.id, b.job_id,b.sourcing_approved, a.quotation_id, a.job_no_prefix_num, $year_cond, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, a.set_smv as sew_smv, b.approved, b.inserted_by, c.id as approval_id, c.sequence_no, c.approved_by, c.id as approval_id, (select max(h.approved_no) from approval_history h where b.id=h.mst_id and h.entry_form=15 $alter_user_cond_2) as revised_no,a.brand_id,a.pro_sub_dep,a.working_company_id
		  from wo_pre_cost_mst b, wo_po_details_master a, approval_history c, wo_po_break_down d
		  where b.id=c.mst_id and c.entry_form=15 and a.job_no=b.job_no and a.company_name=$company_name and a.job_no=d.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.current_approval_status=1 and b.ready_to_approved=1 and b.is_deleted=0 and b.approved in (1,3) $buyer_id_cond $date_cond $job_no_cond $file_no_cond $internal_ref_cond $sequence_no_cond $job_year_cond $alter_user_cond $working_company_cond group by a.garments_nature, b.id, a.quotation_id, b.job_id, a.job_no_prefix_num, $year_cond_groupby, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, a.set_smv, b.approved,b.sourcing_approved, b.inserted_by, c.id, c.sequence_no, c.approved_by, c.id,a.brand_id,a.pro_sub_dep,a.working_company_id";
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

		if($user_sequence_no==$min_sequence_no)
		{
			$buyer_ids = $buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";

			$sql="select b.CONFIRM_APPROVAL,b.sew_effi_percent,a.garments_nature, b.id, b.job_id,b.sourcing_approved, a.quotation_id, a.job_no_prefix_num, $year_cond, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, '0' as approval_id, b.approved, a.set_smv as sew_smv, b.inserted_by, b.entry_from, (select max(c.approved_no) from approval_history c where b.id=c.mst_id and c.entry_form=15 $alter_user_cond) as revised_no ,a.brand_id,a.pro_sub_dep,a.working_company_id from wo_pre_cost_mst b, wo_po_details_master a, wo_po_break_down d where a.job_no=b.job_no and a.job_no=d.job_no_mst and a.company_name=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 and b.approved in (0,2) $buyer_id_cond $buyer_id_cond2 $date_cond $job_no_cond $job_year_cond $internal_ref_cond $file_no_cond $working_company_cond group by b.CONFIRM_APPROVAL,b.sew_effi_percent,a.garments_nature, b.id, a.quotation_id, b.job_id, a.job_no_prefix_num, $year_cond_groupby, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date,b.sourcing_approved, '0', b.approved, a.set_smv, b.inserted_by, b.entry_from,a.brand_id,a.pro_sub_dep,a.working_company_id";
			//echo $sql;die;
		}
		else if($sequence_no == "")
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in ($buyer_ids)";

			if($buyer_ids=="") $buyer_id_cond3=""; else $buyer_id_cond3=" and c.buyer_name in($buyer_ids)";

				$seqSql="select sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=$company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";

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
					else
					{
						$sequence_no_by_yes.=$sRow[csf('sequence_no')].",";
					}
				}

				$buyerIds=chop($buyerIds,',');
				if($buyerIds=="")
				{
					$buyerIds_cond="";
					$seqCond="";
				}
				else
				{
					$buyerIds_cond=" and a.buyer_name not in($buyerIds)";
					$seqCond=" and (".chop($query_string,'or ').")";
				}
				$sequence_no_by_no=chop($sequence_no_by_no,',');
				$sequence_no_by_yes=chop($sequence_no_by_yes,',');

				if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
				if($sequence_no_by_no=="") $sequence_no_by_no=0;

				$pre_cost_id='';
				$pre_cost_id_sql="select distinct (mst_id) as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_name and b.sequence_no in ($sequence_no_by_no) and b.entry_form=15 and b.current_approval_status=1 $buyer_id_cond3 $job_no_cond2 $seqCond
				union
				select distinct (mst_id) as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_name and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=15 and b.current_approval_status=1 $buyer_id_cond3";
				$bResult=sql_select($pre_cost_id_sql);
				foreach($bResult as $bRow)
				{
					$pre_cost_id.=$bRow[csf('pre_cost_id')].",";
				}

				$pre_cost_id=chop($pre_cost_id,',');

				$pre_cost_id_app_sql=sql_select("select b.mst_id as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c
				where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_name and b.sequence_no=$user_sequence_no and b.entry_form=15 and a.ready_to_approved=1 and b.current_approval_status=1  $job_no_cond2");

				foreach($pre_cost_id_app_sql as $inf)
				{
					if($pre_cost_id_app_byuser!="") $pre_cost_id_app_byuser.=",".$inf[csf('pre_cost_id')];
					else $pre_cost_id_app_byuser.=$inf[csf('pre_cost_id')];
				}

				$pre_cost_id_app_byuser=implode(",",array_unique(explode(",",$pre_cost_id_app_byuser)));
			//}// 12-10-2018

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
				else
				{
					$pre_cost_id_cond=" and b.id not in($pre_cost_id_app_byuser)";
				}
			}
			else $pre_cost_id_cond="";

			$sql="select b.CONFIRM_APPROVAL, b.sew_effi_percent, b.remarks,a.garments_nature, b.id, b.job_id,b.sourcing_approved, a.quotation_id, a.job_no_prefix_num, $year_cond, a.job_no, a.buyer_name, a.style_ref_no, a.set_smv as sew_smv, b.costing_date, 0 as approval_id, b.approved, b.inserted_by, b.entry_from, (select max(c.approved_no) from approval_history c where b.id=c.mst_id and c.entry_form=15 $alter_user_cond) as revised_no,a.brand_id,a.pro_sub_dep,a.working_company_id
			from wo_pre_cost_mst b ,wo_po_details_master a, wo_po_break_down d
			where a.job_no=b.job_no and a.job_no=d.job_no_mst and a.company_name=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 and b.approved in (0,2) $buyer_id_cond $date_cond $pre_cost_id_cond $buyerIds_cond $buyer_id_cond2 $job_no_cond $file_no_cond $internal_ref_cond $job_year_cond $working_company_cond group by b.CONFIRM_APPROVAL,b.sew_effi_percent, b.remarks,a.garments_nature, b.id, b.job_id,b.sourcing_approved, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby, a.job_no, a.buyer_name, a.style_ref_no, a.set_smv, b.costing_date, b.approved, b.inserted_by, b.entry_from ,a.brand_id,a.pro_sub_dep,a.working_company_id";
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
				else
				{
					$pre_cost_id_cond2.="  b.id  in($pre_cost_id)";	 
				}

				$sql.=" union all
				select b.CONFIRM_APPROVAL,b.sew_effi_percent, b.remarks,a.garments_nature, b.id, b.job_id, b.sourcing_approved, a.quotation_id, a.job_no_prefix_num, $year_cond, a.job_no, a.buyer_name, a.style_ref_no, a.set_smv as sew_smv, b.costing_date, 0 as approval_id, b.approved, b.inserted_by, b.entry_from, (select max(c.approved_no) from approval_history c where b.id=c.mst_id and c.entry_form=15 $alter_user_cond) as revised_no,a.brand_id,a.pro_sub_dep,a.working_company_id
				from wo_pre_cost_mst b, wo_po_details_master a, wo_po_break_down d
				where a.job_no=b.job_no and a.job_no=d.job_no_mst and a.company_name=$company_name  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 and b.approved in(3) $pre_cost_id_cond2 $buyer_id_cond $buyer_id_cond2 $date_cond $job_no_cond $file_no_cond $internal_ref_cond $job_year_cond $working_company_cond group by b.CONFIRM_APPROVAL,b.sew_effi_percent, b.remarks,a.garments_nature, b.id, b.job_id, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby, a.job_no, a.buyer_name, a.style_ref_no, a.set_smv, b.costing_date, b.approved, b.inserted_by, b.entry_from,b.sourcing_approved,a.brand_id,a.pro_sub_dep,a.working_company_id";//b.approved in(1,3)
			}
			//echo $sql; die;
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
					$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and
					 sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");// and bypass=1
				}
				else
				{
					$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no)
					 as sequence_no","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");//bypass=1 and
				}
			}

			if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$sequence_no'";
			else $sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";

			  $sql="select b.CONFIRM_APPROVAL,a.garments_nature, b.id, b.job_id,b.sourcing_approved, a.quotation_id, a.job_no_prefix_num, $year_cond, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, b.approved, b.inserted_by, c.id as approval_id, c.sequence_no, c.approved_by, c.id as approval_id, b.entry_from, a.set_smv as sew_smv, (select max(h.approved_no) from approval_history h where b.id=h.mst_id and h.entry_form=15 $alter_user_cond_2) as revised_no,a.brand_id,a.pro_sub_dep,a.working_company_id
			      from wo_pre_cost_mst b, wo_po_details_master a, approval_history c, wo_po_break_down d
				  where b.id=c.mst_id and c.entry_form=15 and a.job_no=b.job_no and a.company_name=$company_name and a.job_no=d.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.current_approval_status=1 and b.is_deleted=0 and b.approved in (1,3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $date_cond $job_no_cond $file_no_cond $internal_ref_cond $job_year_cond $working_company_cond group by b.CONFIRM_APPROVAL,a.garments_nature, b.id, b.job_id,b.sourcing_approved, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby, a.job_no, a.buyer_name, a.set_smv, a.style_ref_no, b.costing_date, b.approved, b.inserted_by, c.id, c.sequence_no, c.approved_by, b.entry_from ,a.brand_id,a.pro_sub_dep,a.working_company_id"; //and b.ready_to_approved=1
		}
	}
	else
	{ 
		$buyer_ids=$buyer_ids_array[$user_id]['u'];
		if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";

		$sequence_no_cond=" and c.sequence_no='$user_sequence_no'";

		$sql="select b.CONFIRM_APPROVAL, b.sew_effi_percent, b.remarks,a.garments_nature, b.id, b.job_id,b.sourcing_approved, a.quotation_id, a.job_no_prefix_num, $year_cond, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, a.set_smv as sew_smv, b.approved, b.inserted_by, c.id as approval_id, c.sequence_no, c.approved_date, c.approved_by, c.id as approval_id, b.entry_from,(select max(h.approved_no) from approval_history h where b.id=h.mst_id and h.entry_form=15 $alter_user_cond_2) as revised_no,a.brand_id,a.pro_sub_dep,a.working_company_id
			from wo_pre_cost_mst b,  wo_po_details_master a,approval_history c,wo_po_break_down d
			where b.id=c.mst_id and c.entry_form=15 and a.job_no=b.job_no and a.company_name=$company_name and a.job_no=d.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.current_approval_status=1 and b.is_deleted=0 and b.approved in (1,3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $date_cond $job_no_cond $file_no_cond $internal_ref_cond $job_year_cond $working_company_cond  group by b.CONFIRM_APPROVAL, b.sew_effi_percent, b.remarks,a.garments_nature, b.id, b.job_id,b.sourcing_approved, a.quotation_id, a.quotation_id, a.set_smv, a.job_no_prefix_num, $year_cond_groupby, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, b.approved, b.inserted_by, c.id, c.sequence_no, c.approved_by, c.id, c.approved_date, b.entry_from,a.brand_id,a.pro_sub_dep,a.working_company_id"; //and b.ready_to_approved=1
	}

	 //echo $sql; die;

	 $nameArray = sql_select( $sql );
	 foreach ($nameArray as $row) {
		 $pi_ids.=$row[csf('id')].',';
	 }
	 $pi_Ids = implode(",", array_unique(explode(",", rtrim($pi_ids,','))));
	
	//print_r($pi_Ids);
	
	$sql_unapproved=sql_select("select * from fabric_booking_approval_cause where  entry_form=15 and approval_type=2 and is_deleted=0 and status_active=1");
	$unapproved_request_arr=array();
	foreach($sql_unapproved as $rowu)
	{
		$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
	}
// 	echo "<pre>";
// print_r($unapproved_request_arr); 
//   echo "</pre>";die();
	//variable Settings Check 
	$sqlVariableCheck=sql_select("select id, is_required, cm_std_per, cm_std_value, margin_std_per, margin_std_value from variable_approval_settings where company_name=$company_name and is_required=1 and variable_list=1 and status_active=1 and is_deleted=0");
	$isReq=2; $cm_std_per=$cm_std_value=$margin_std_per=$margin_std_value=0;
	foreach($sqlVariableCheck as $vrow)
	{
		$isReq=$vrow[csf('is_required')];
		$cm_std_per=$vrow[csf('cm_std_per')]*1;
		$cm_std_value=$vrow[csf('cm_std_value')]*1;
		$margin_std_per=$vrow[csf('margin_std_per')]*1;
		$margin_std_value=$vrow[csf('margin_std_value')]*1;
	}

	//Pre cost button---------------------------------
	$print_report_format_ids = return_field_value("format_id","lib_report_template","template_name=".$company_name." and module_id=2 and report_id in (43) and is_deleted=0 and status_active=1");
	$format_ids=explode(",",$print_report_format_ids);
	$row_id=$format_ids[0];
	$print_report_format_ids_wvn = return_field_value("format_id","lib_report_template","template_name=".$company_name." and module_id=2 and report_id in (122) and is_deleted=0 and status_active=1");
	$format_ids_wvn=explode(",",$print_report_format_ids_wvn);
	$row_id_wvn=$format_ids_wvn[0];
	// echo $row_id.'d';

	//Order Wise Budget Report button---------------------------------
	$print_report_format_ids2 = return_field_value("format_id","lib_report_template","template_name=".$company_name." and module_id=11 and report_id=18 and is_deleted=0 and status_active=1");
	$format_ids2=explode(",",$print_report_format_ids2);
	$row_id2=$format_ids2[0];
	$job_no_arrs=array();
	$nameArray2=sql_select( $sql );
	foreach( $nameArray2 as $row)
	{
		$job_id_arr[$row[csf('job_id')]]=$row[csf('job_id')];
		array_push($job_no_arrs, $row[csf('job_no')]);
	}
	
/*	$cofirmApprovalArr=array();
	if($isReq==1)
	{
		if($cm_std_per==0 && $cm_std_value==0 && $margin_std_per==0 && $margin_std_value==0)
		{
			echo "<font style='color:#F00; font-size:14px; font-weight:bold'>No Value Set In Library For Confirmation Before Approval.</font>";
			die;
		}
		else
		{
			$sqlConfirm="select id, company_name, mst_id, cm_std_per, cmper, cm_std_value, cm_cost, margin_std_per, marginper, margin_std_value, marginval, is_confirm from approval_confirm where is_confirm=1 and status_active=1 and is_deleted=0 ".where_con_using_array($job_id_arr,1,'mst_id')."";
			$sqlAppCon=sql_select($sqlConfirm);
			
			foreach($sqlAppCon as $row)
			{
				$cofirmApprovalArr[$row[csf('mst_id')]]=$row[csf('job_no_mst')];
			}
			unset($sqlAppCon);
		}
	}
	
*/	
	   
	$sql_intRef=sql_select("select job_no_mst,grouping,file_no from wo_po_break_down where is_deleted=0 and status_active=1 ".where_con_using_array($job_id_arr,1,'job_id')."");
	$intRef=array();
	foreach($sql_intRef as $rowData)
	{
		$intRef[$rowData[csf('job_no_mst')]]["internalRef"].=$rowData[csf('grouping')].",";
		$intRef[$rowData[csf('job_no_mst')]]["fileNo"]=$rowData[csf('file_no')];
	}
	$sql_pre_sourcing=sql_select("select job_no, sourcing_approved from wo_pre_cost_mst where is_deleted=0 and status_active=1 ".where_con_using_array($job_id_arr,0,'job_id')."");
	
	$jobSourcing_arr=array();
	foreach($sql_pre_sourcing as $row)
	{
		$jobSourcing_arr[$row[csf('job_no')]]["sourcing_approved"]=$row[csf('sourcing_approved')];
	}
	unset($sql_pre_sourcing);
	
	$pre_data_array = sql_select("select job_no, costing_per_id as costing_per, job_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, commission, commission_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, total_cost, deffdlc_cost, deffdlc_percent, interest_cost, interest_percent, incometax_cost, incometax_percent, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, price_pcs_or_set, depr_amor_pre_cost, depr_amor_po_price, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche, margin_pcs_bom from wo_pre_cost_dtls where status_active=1 and is_deleted=0 ".where_con_using_array($job_id_arr,1,'job_id')."");
		 
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
		$cm_cost=$cm_cost;
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

	

	$sql_remarks=sql_select("select * from fabric_booking_approval_cause where  entry_form=15  and is_deleted=0 and status_active=1 and user_id=$user_id and BOOKING_ID in(".$pi_Ids.")");

    // $sql="select * from fabric_booking_approval_cause where  entry_form=15  and is_deleted=0 and status_active=1 and user_id=$user_id and BOOKING_ID in(".$pi_Ids.")";
    // echo $sql;die();
	//$unapproved_request_arr=array();
	$remarks_case_arr=array();
	foreach($sql_remarks as $rowu)
	{
        $remarks_case_arr[$rowu[csf('booking_id')]][$rowu[csf('approval_type')]]=$rowu['REMARKS'];
	}

    //     echo "<pre>";
    // print_r($remarks_case_arr); 
    //   echo "</pre>";//die();
	$fab_sql=sql_select("select job_no, avg_finish_cons, amount from wo_pre_cost_fabric_cost_dtls where is_deleted=0 and status_active=1 ".where_con_using_array($job_id_arr,1,'job_id')."");
	
	foreach($fab_sql as $row)
	{
		$job_cost_arr[$row[csf('job_no')]]['fabric_cost']+=$row[csf('amount')];
	}
	$trim_sql=sql_select("select job_no,amount from wo_pre_cost_trim_cost_dtls where is_deleted=0 and status_active=1 ".where_con_using_array($job_id_arr,0,'job_id')."");
	
	foreach($trim_sql as $row)
	{
		$job_cost_arr[$row[csf('job_no')]]['trims_cost']+=$row[csf('amount')];
	}

	$job_cond=where_con_using_array(array_unique($job_no_arrs),1,"job_no");

	$nameArray=sql_select( $sql );
	$ref_no = "";
	$file_numbers = "";
	foreach ($nameArray as $row)
	{
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

		$value=$row[csf('id')];

		if($row[csf('approval_id')]==0)
		{
			$print_cond=1;
		}
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
		$cm_cost=$cm_cost_arr[$row[csf('job_no')]]['cm_cost'];
		if($cm_cost=='' || $cm_cost==0) $cm_cost=0;else $cm_cost=$cm_cost;
		if($cm_cost<0 || $cm_cost==0) $td_color="#F00"; else $td_color="";

		if($row_id2==23){$type=1;/*Summary;*/}
		else if($row_id2==24){$type=2;}
		else if($row_id2==25){$type=3;/*Budget Report2;*/}
		else if($row_id2==26){$type=4;/*Quote Vs Budget;*/}
		else if($row_id2==27){$type=5;/*Budget On Shipout;*/}
		else if($row_id2==29){$type=6;/*C.Date Budget On Shipout;*/}
		else if($row_id2==182){$type=7;/*Budget Report 3;*/}

		$function2="generat_print_report($type,$company_name,0,'','',{$row[csf('job_no_prefix_num')]},'','','',".$row[csf('year')].",0,1,'','','','')";
		if($print_cond==1)
		{
			// echo $row_id_wvn.'W'.$row_id;
			if($row_id_wvn==311 && $row[csf('garments_nature')]==3){$action='bom_epm_woven';}
			else if($row_id_wvn==313 && $row[csf('garments_nature')]==3){$action='mkt_source_cost';}
			else if($row_id_wvn==761 && $row[csf('entry_from')]==158 && $row[csf('garments_nature')]==3){$action='bom_pcs_woven';}
			else if($row_id_wvn==159 && $row[csf('entry_from')]==158 && $row[csf('garments_nature')]==3) {$action='bomRptWoven';} //report_btn_8;
			else if($row_id==50){$action='preCostRpt'; } //report_btn_1;
			else if($row_id==51){$action='preCostRpt2';} //report_btn_2;
			else if($row_id==52){$action='bomRpt';} //report_btn_3;
			else if($row_id==63){$action='bomRpt2';} //report_btn_4;
			else if($row_id==142){$action='preCostRptBpkW';}
			else if($row_id==156){$action='accessories_details';} //report_btn_5;
			else if($row_id==157){$action='accessories_details2';} //report_btn_6;
			else if($row_id==158){$action='preCostRptWoven';} //report_btn_7;
			else if($row_id==159 && $row[csf('entry_from')]==111){$action='bomRptWoven';} //report_btn_8;
			else if($row_id==170){$action='preCostRpt3';} //report_btn_9;
			else if($row_id==171){$action='preCostRpt4';} //report_btn_10;
			else if($row_id==173){$action='preCostRpt5';} //report_btn_10;
			else if($row_id==192){$action='checkListRpt';}
			else if($row_id==197){$action='bomRpt3';}
			else if($row_id==211){$action='mo_sheet';}
			else if($row_id==215){$action='budget3_details';}
			else if($row_id==221){$action='fabric_cost_detail';}											
			else if($row_id==238){$action='summary';}
			else if($row_id==270){$action='preCostRpt6';}
			else if($row_id==581){$action='costsheet';}
			else if($row_id==730){$action='budgetsheet';}
			else if($row_id==268){$action='budget_4';}
			//else if($row_id==761){$action='bom_pcs_woven';}
			 	//echo $row_id.'DSDS';

			$function="generate_worder_report_pre_cost('".$action."','".$row[csf('job_no')]."',".$company_name.",".$row[csf('buyer_name')].",'".$row[csf('style_ref_no')]."','".$row[csf('costing_date')]."',".$row[csf('entry_from')].",'".$row[csf('job_id')]."',".$row[GARMENTS_NATURE].");"; 
			$function2="history_print_job('".$row[csf('job_no')]."',".$company_name.",".$row[csf('buyer_name')].",'".$row[csf('style_ref_no')]."','".$row[csf('costing_date')]."',".$row[csf('entry_from')].",'".$row[csf('quotation_id')]."',".$row[csf('revised_no')].");"; 
			// $function2 -Remove- issue-5777
			$garments_nature=$row[csf('garments_nature')];
			$trims_cost=$job_cost_arr[$row[csf('job_no')]]['trims_cost'];
			if($trims_cost=='') $trims_cost=0;else $trims_cost=$trims_cost;
			$fabric_cost=$job_cost_arr[$row[csf('job_no')]]['fabric_cost'];
			if($fabric_cost=='') $fabric_cost=0;else $fabric_cost=$fabric_cost;
			//echo $trims_cost.'fd'. $fabric_cost;
			if($copy_quotation_id==0 || $copy_quotation_id==2) $copy_quotation_id=2;else $copy_quotation_id=$copy_quotation_id;
			
			$sourcing_approved=0;
			if($budget_up_app_validation==1) //Budget Sourcing
			{
				 $sourcing_approved=$jobSourcing_arr[$row[csf('job_no')]]["sourcing_approved"];
				//  echo  $budget_up_app_validation.'DD';
				if($sourcing_approved=="")  $sourcing_approved=0;
			}
			
			
			//-------------------------------------------------
			if($row['CONFIRM_APPROVAL']!=1 && $isReq==1 && (($pre_cost_dat_by_job[$row[csf('job_no')]]['cm_val']<$cm_std_value) || ($pre_cost_dat_by_job[$row[csf('job_no')]]['cm_per']<$cm_std_per) || ($pre_cost_dat_by_job[$row[csf('job_no')]]['marg_val']<$margin_std_value) || ($pre_cost_dat_by_job[$row[csf('job_no')]]['marg_per']<$margin_std_per)) ){
				continue;
			}
			//-------------------------------------------------
			
			
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" align="center">
				<td width="27" align="center" valign="middle">
					<input type="checkbox" id="tbl_<? echo $i;?>" />
					<input id="booking_id_<? echo $i;?>" name="booking_id[]" type="hidden" value="<? echo $value; ?>" />
					<input id="booking_no_<? echo $i;?>" name="booking_no[]" type="hidden" value="<? echo $row[csf('job_no')]; ?>" />
					<input id="approval_id_<? echo $i;?>" name="approval_id[]" type="hidden" value="<? echo $row[csf('approval_id')]; ?>" />
					<input id="<? echo strtoupper($row[csf('job_no')]); ?>" name="no_joooob[]" type="hidden" value="<? echo $i;?>" />
					<input id="cm_cost_id_<? echo $i;?>" name="cm_cost_id[]" style="width:20px;" type="hidden" value="<? echo $cm_cost; ?>" />
					<input id="fabric_id_cost_<? echo $i;?>" name="fabric_id_cost[]" style="width:20px;" type="hidden" value="<? echo $fabric_cost; ?>" />
					<input id="trims_id_cost_<? echo $i;?>" name="trims_id_cost[]" style="width:20px;" type="hidden" value="<? echo $trims_cost; ?>" />
					<input id="garments_nature_<? echo $i;?>" name="garments_nature[]" style="width:20px;" type="hidden" value="<? echo $garments_nature; ?>" />
					<input id="copy_quot_<? echo $i;?>" name="copy_quot[]" style="width:20px;" type="hidden" value="<? echo $copy_quotation_id; ?>" />
					<input id="sourcing_approved_id_<? echo $i;?>" name="sourcing_approved_id[]" style="width:20px;" type="hidden" value="<? echo $sourcing_approved; ?>" />
					
					<input id="mst_id_company_id_<?=$i;?>" name="mst_id_company_id[]" type="hidden" value="<?=$row[csf('job_no')].'*'.$value.'*'.$company_name; ?>" />
				</td>
				<td width="40" align="center"><? echo  $i; ?></td>
				<td width="50">
					<p><a href='##'  onclick="<? echo $function; ?>"><? echo $row[csf('job_no_prefix_num')]; ?></a></p>
				</td>
				<td width="100" style="word-wrap: break-word;"><? echo $lib_company_name[$row[csf('working_company_id')]]; ?></td>
				<td width="70">
					<?php if($row[csf('revised_no')]>1){ ?>
					<p><a href='##'  onclick="<? echo $function2; ?>"><? echo $row[csf('revised_no')]-1; ?></a></p>
				 <?php } ?>
				</td>
				<td width="70" align="center"><?=$margin_pcs_arr[$row[csf('job_no')]]['marginpcs']; ?></td>
				<td width="60" align="center"><?=$margin_pcs_arr[$row[csf('job_no')]]['marginpcsbom']; ?></td>
				 <td width="60" align="right" style="word-break:break-all"><p style="color:<?=$td_color; ?>"><? echo number_format($cm_cost,4); ?>&nbsp;</p></td>
				 <td width="60"  title="Contribution Margin/Costing Per/SMV(<? echo $job_epm_contribute_margin_arr[$row[csf('job_no')]];?>)" align="right"><p style="color:<? //echo $td_color; ?>"><? echo number_format($job_epm_arr[$row[csf('job_no')]]/$row[csf('sew_smv')],4); ?>&nbsp;</p></td>
				 <td width="60" align="right" style="word-break:break-all"><? echo $row[csf('sew_smv')]; ?>&nbsp;</td>
				 <td width="60" align="right" style="word-break:break-all"><? echo $row[csf('sew_effi_percent')]; ?>&nbsp;</td>
				<td width="72" style=" word-break:break-all;"><p>
				<?
					$int_ref=implode(",",array_unique(explode(",",chop($intRef[$row[csf('job_no')]]["internalRef"],","))));
					echo $int_ref;
				//echo $intRef[$row[csf('job_no')]]["internalRef"]; ?>
				</p>
				</td>
				<td width="70"><p><? echo $intRef[$row[csf('job_no')]]["fileNo"]; ?></p></td>
				<td width="40"><p><? echo $row[csf('year')]; ?>&nbsp;</p></td>
				<td width="125"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
				<td width="160" align="center" style="word-break:break-all"><a href='##'  onclick="<? echo $function2; ?>"><? echo $row[csf('style_ref_no')]; ?></a></td>
				<td width="100" align="center"><p><? echo $brand_arr[$row[csf('brand_id')]]; ?></p></td>
				<td width="100" align="center"><p><? echo $sub_dep_arr[$row[csf('pro_sub_dep')]]; ?></p></td>
				<td width="65" align="center"><? if($row[csf('costing_date')]!="0000-00-00") echo change_date_format($row[csf('costing_date')]); ?>&nbsp;</td>
				<td align="center" width="65"><? if($row[csf('est_ship_date')]!="0000-00-00") echo change_date_format($row[csf('est_ship_date')]); ?>&nbsp;</td>
				<td width="45" align="center"><a href="##" onClick="openImgFile('<? echo $row[csf('job_no')];?>','img');">View</a></td>
				<td width="150" style="word-break:break-all">
				<?
					if($approval_type==1)
					{
						echo $unapproved_request_arr[$value];
					}
				?>
				</td>
				<td width="65"><p><? echo ucfirst ($user_arr[$row[csf('inserted_by')]]); ?>&nbsp;</p></td>
				<td width="80" align="center"><? if($row[csf('approved_date')]!="0000-00-00") echo change_date_format($row[csf('approved_date')]); ?>&nbsp;</td>
				<?
				$mst_id=$row[csf('id')];
				$refusing_reason_arr =sql_select("SELECT id,refusing_reason from refusing_cause_history where  mst_id='$mst_id' order by id desc ");							  
			//	 print_r($refusing_reason_arr);
				 ?>

				<td width="100" > <input style="width:90px;" type="text" class="text_boxes"  name="txtCause_<? echo $row[csf('id')];?>" id="txtCause_<? echo $row[csf('id')];?>" placeholder="browse" onClick="openmypage_refusing_cause('requires/pre_costing_approval_controller.php?action=refusing_cause_popup','Refusing Cause','<? echo $row[csf('id')];?>');" value="<?  echo $refusing_reason_arr[0][csf('refusing_reason')]; //$row[csf('refusing_cause')];?>"/></td>

			
				    <? 
                                    $casues1=$remarks_case_arr[$value][2];
									//print_r($casues1);
									
                                    
				     ?>
				
				<td  align="center"><input name="txt_appv_cause[]" class="text_boxes" readonly placeholder="Please Browse" ID="txt_appv_cause_<?=$i;?>" style="width:90px" title="<?=$value."--".$approval_type?>" value="<? echo $casues1;?>"  maxlength="50" title="Maximum 50 Character" onClick="openmypage_app_cause(<?=$value; ?>,<?=$approval_type; ?>,<?=$i; ?>,'1',<?=$user_id;?>)">&nbsp;</td>
				
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
			if($db_type==2 || $db_type==1 )
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
	$denyBtn=""; $denyBtnMsg=""; $btnmsg=""; $isApp="";
	if($approval_type==2) 
	{
		$denyBtn=""; 
		$btnmsg="Approve";
		$denyBtnMsg="Deny";
		$isApp="";
	}
	else 
	{
		$denyBtn=" display:none";
		$btnmsg="Un-Approve";
		$denyBtnMsg="";
		$isApp=" display:none";
	}
}//end company loof
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="1842" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" style=" <?=$isApp; ?> "><input type="checkbox" id="all_check" onClick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<?=$btnmsg; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<?=$i; ?>,<?=$approval_type; ?>);"/>&nbsp;&nbsp;&nbsp;<input type="button" value="<?=$denyBtnMsg; ?>" class="formbutton" style="width:100px;<?=$denyBtn; ?>" onClick="submit_approved(<?=$i; ?>,5);"/></td>
				</tfoot>
			</table>
        </fieldset>
    </form>
	<?
	exit();
}

if ($action=="appcause_popup")
{
    echo load_html_head_contents("Approval Cause Info", "../../", 1, 1,'','','');
    extract($_REQUEST);

    $data_all=explode('_',$data);
	//print_r( $data_all);

    $wo_id=$data_all[0];
    $app_type=$data_all[1];
    $app_cause=$data_all[2];
    $approval_id=$data_all[3];
	$app_from=$data_all[4];
	$user_id=$data_all[5];

    if($app_cause=="")
    {
        $sql_cause="select MAX(id) as id from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=15 and user_id='$user_id' and booking_id='$wo_id' and approval_type=$app_type and status_active=1 and is_deleted=0";
        //echo $sql_cause; die;
        $nameArray_cause=sql_select($sql_cause);
        if(count($nameArray_cause)>0)
		{
			if($app_from==1)
			{
				foreach($nameArray_cause as $row)
				{
					$app_cause1=return_field_value("remarks", "fabric_booking_approval_cause", "id='".$row[csf("id")]."' and status_active=1 and is_deleted=0");
					$app_cause = str_replace(array("\r", "\n"), ' ', $app_cause1);
				}
			}
			else
			{
				foreach($nameArray_cause as $row)
				{
					$app_cause1=return_field_value("not_approval_cause", "fabric_booking_approval_cause", "id='".$row[csf("id")]."' and status_active=1 and is_deleted=0");
					$app_cause = str_replace(array("\r", "\n"), ' ', $app_cause1);
				}
			}
        }
        else
        {
            $app_cause = '';
        }
    }
	//echo $app_cause.test;die;
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

                var data="action=save_update_delete_appv_cause&operation="+operation+"&user_id="+<?=$user_id;?>+get_submitted_data_string('appv_cause*wo_id*appv_type*page_id*user_id*approval_id*app_from',"../../");
                //alert (data);return;
                freeze_window(operation);
                http.open("POST","pre_costing_approval_controller.php",true);
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
            }
        }

        function fnc_close()
        {
            appv_cause= $("#appv_cause").val();

            document.getElementById('hidden_appv_cause').value=appv_cause;

            parent.emailwindow.hide();
        }

       /* function generate_worder_mail(woid,mail,appvtype,user)
        {
            var data="action=app_cause_mail&woid="+woid+'&mail='+mail+'&appvtype='+appvtype+'&user='+user;
            //alert (data);return;
            freeze_window(6);
            http.open("POST","pi_approval_new_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange=fnc_appv_mail_Reply_info;

        }*/

        /*function fnc_appv_mail_Reply_info()
        {
            if(http.readyState == 4)
            {
                var response=trim(http.responseText).split('**');
                /*if(response[0]==222)
                {
                    show_msg(reponse[0]);
                }*/
                //release_freezing();
            //}
        //}

    </script>
    <body>
        <div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
        <form name="size_1" id="size_1">
            <fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                        <textarea name="appv_cause" id="appv_cause" class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"><? echo $app_cause; ?></textarea>
                        <input type="hidden" name="wo_id" class="text_boxes" ID="wo_id" value="<? echo $wo_id; ?>" style="width:30px" />
                        <input type="hidden" name="appv_type" class="text_boxes" ID="appv_type" value="<? echo $app_type; ?>" style="width:30px" />
                        <input type="hidden" name="page_id" class="text_boxes" ID="page_id" value="<? echo $menu_id; ?>" style="width:30px" />
                        <input type="hidden" name="user_id" class="text_boxes" ID="user_id" value="<? echo $user_id; ?>" style="width:30px" />
                        <input type="hidden" name="approval_id" class="text_boxes" ID="approval_id" value="<? echo $approval_id; ?>" style="width:30px" />
                        <input type="hidden" name="app_from" class="text_boxes" ID="app_from" value="<? echo $app_from; ?>" style="width:30px" />
                    </td>
                </tr>
            </table>

            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >

                <tr>
                    <td align="center" class="button_container">
                        <?
                        //print_r ($id_up_all);
                            if($app_cause!='')
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


if ($action=="save_update_delete_appv_cause")
{  
    //$approval_id
    $approval_type=str_replace("'","",$appv_type);
  
    if($approval_type==2)
    { 
        $process = array( &$_POST );
        extract(check_magic_quote_gpc( $process ));
		$app_from=str_replace("'","",$app_from);
		
		
		if($app_from==1)
		{
			$approval_cause_field="remarks";
		}
		else
		{
			$approval_cause_field="not_approval_cause";
		}
		
		//echo "10**".$appv_cause."==".$not_appv_cause;die;
		
        if ($operation==0 || $operation==1)  // Insert Here
        { 
            $con = connect();
            if($db_type==0)
            {
                mysql_query("BEGIN");
            }
			
            $approved_no_history=return_field_value("approved_no","approval_history","entry_form=15 and mst_id=$wo_id and approved_by=$user_id");
            //echo "10**$approved_no_history";die;
            $approved_no_cause=return_field_value("approval_no","fabric_booking_approval_cause","entry_form=15 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
            //echo "10**$approved_no_cause";die;

            if($approved_no_history=="" && $approved_no_cause=="")
            {
                //echo "insert"; die;
				
                $id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;
				//if($app_from==1)
                $field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,$approval_cause_field,inserted_by,insert_date,status_active,is_deleted";
                $data_array="(".$id_mst.",".$page_id.",15,".$user_id.",".$wo_id." ,".$appv_type.",0,".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				//echo "10**insert into fabric_booking_approval_cause ($field_array) values $data_array";die;
                //$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
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

                $id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=15 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

                $field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*$approval_cause_field*updated_by*update_date*status_active*is_deleted";
                $data_array="".$page_id."*15*".$user_id."*".$wo_id."*".$appv_type."*0*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
                 //echo "10**insert into fabric_booking_approval_cause ($field_array) values $data_array";die;
                 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);
                //  echo "10**1803==>".$rID;die;
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
                $max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=15 and mst_id=$wo_id and approved_by=$user_id");
                $max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=15 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

                if($max_appv_no_his!=$max_appv_no_cause)
                {
                    $id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

                    $field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,$approval_cause_field,inserted_by,insert_date,status_active,is_deleted";
                    $data_array="(".$id_mst.",".$page_id.",15,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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

                    $id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=15 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

                    $field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*$approval_cause_field*updated_by*update_date*status_active*is_deleted";
                    $data_array="".$page_id."*15*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

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
                $max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=15 and mst_id=$wo_id and approved_by=$user_id");
                $max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=15 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

                if($max_appv_no_his!=$max_appv_no_cause)
                {
                    $id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

                    $field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,$approval_cause_field,inserted_by,insert_date,status_active,is_deleted";
                    $data_array="(".$id_mst.",".$page_id.",15,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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

                    $id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=15 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

                    $field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*$approval_cause_field*updated_by*update_date*status_active*is_deleted";
                    $data_array="".$page_id."*15*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

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
		if($app_from==1)
		{
			$approval_cause_field="approval_cause";
		}

        if ($operation==0 || $operation==1)  // Insert Here
        {  
            $con = connect();
            if($db_type==0)
            {
                mysql_query("BEGIN");
            }

            $unapproved_cause_id=return_field_value("id","fabric_booking_approval_cause","entry_form=15 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");

            //echo "10**$unapproved_cause_id";die;
            $max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=15 and mst_id=$wo_id and approved_by=$user_id");
           
            if($unapproved_cause_id=="")
            {  
                $id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;
                $field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_history_id,$approval_cause_field,inserted_by,insert_date,status_active,is_deleted";
                $data_array="(".$id_mst.",".$page_id.",15,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$approval_id.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

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
                //echo "10**entry_form=15 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id";die;
                $id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=15 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");

                $field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_history_id*$approval_cause_field*updated_by*update_date*status_active*is_deleted";
                $data_array="".$page_id."*15*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$approval_id."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

                 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);
                // echo $rID; die;


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

if($action=="report_generate_2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$sequence_no='';
	$company_name=str_replace("'","",$cbo_company_name);

	$working_company=str_replace("'","",$cbo_working_company);
	$working_company_cond='';
	if(!empty($working_company))
	{
		$working_company_cond=" and a.working_company_id=$working_company";
	}
	//$lib_company_name=return_library_array( "select id, company_name from lib_company",'id','company_name');

	//echo $txt_internal_ref; echo $txt_file_no;
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$alter_user_cond='';
	$alter_user_cond_2='';
	if(!empty($txt_alter_user_id))
	{
		$alter_user_cond=" and c.approved_by=$txt_alter_user_id ";
		$alter_user_cond_2=" and h.approved_by=$txt_alter_user_id ";
	}
	if($txt_alter_user_id!="")
	{
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			$log_sql = sql_select("SELECT user_level,buyer_id,unit_id,is_data_level_secured FROM user_passwd WHERE id = '$txt_alter_user_id' AND valid = 1");
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
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	}
	$job_no=str_replace("'","",$txt_job_no);
	$file_no=str_replace("'","",$txt_file_no);
	$internal_ref=str_replace("'","",$txt_internal_ref);
	$job_year=str_replace("'","",$cbo_year);

	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and d.file_no='".trim($file_no)."' ";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and d.grouping='".trim($internal_ref)."' ";
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num='".trim($job_no)."' ";
	if ($job_no=="") $job_no_cond2=""; else $job_no_cond2=" and c.job_no_prefix_num='".trim($job_no)."' ";
	if ($job_year=="" || $job_year==0) $job_year_cond="";
	else
	{
		if($db_type==2) $job_year_cond=" and to_char(a.insert_date,'YYYY')='".trim($job_year)."' ";
		else $job_year_cond=" and YEAR(a.insert_date)='".trim($job_year)."' ";
	}

	$date_cond='';
	if(str_replace("'","",$txt_date)!="")
	{
		if($db_type==0) $txt_date="'".change_date_format(str_replace("'","",$txt_date), "yyyy-mm-dd", "-")."'";
		else if($db_type==2) $txt_date="'".change_date_format(str_replace("'","",$txt_date), "yyyy-mm-dd", "-",1)."'";
		
		if(str_replace("'","",$cbo_get_upto)==1) $date_cond=" and b.costing_date>$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==2) $date_cond=" and b.costing_date<=$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==3) $date_cond=" and b.costing_date=$txt_date";
		else $date_cond='';
	}

	$approval_type=str_replace("'","",$cbo_approval_type);
	if($previous_approved==1 && $approval_type==1)
	{
		$previous_approved_type=1;
	}
	//echo $menu_id;die;
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");

	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");
	//$cm_cost_arr=return_library_array( "select job_no, cm_cost from wo_pre_cost_dtls where is_deleted=0 and status_active=1", "job_no", "cm_cost"  );
	/*$pre_sql=sql_select("select job_no, cm_cost,fabric_cost,trims_cost from wo_pre_cost_dtls where is_deleted=0 and status_active=1");
	foreach($pre_sql as $row)
	{
		$cm_cost_arr[$row[csf('job_no')]]=$row[csf('cm_cost')];
		$job_cost_arr[$row[csf('job_no')]]['trims_cost']=$row[csf('trims_cost')];
		$job_cost_arr[$row[csf('job_no')]]['fabric_cost']=$row[csf('fabric_cost')];
	}*/
	$sql_vari_seting_copy_quotation=sql_select("select embellishment_budget_id,copy_quotation,variable_list from variable_order_tracking where company_name=$cbo_company_name and variable_list in(76,20) and status_active=1 and is_deleted=0");
	foreach($sql_vari_seting_copy_quotation as $row)
	{
		if($row[csf('variable_list')]==20)
		{
			$copy_quotation_id=$row[csf('copy_quotation')];
		}
		else{
			$budget_up_app_validation=$row[csf('embellishment_budget_id')];
		}
		
	}
	
	//echo $budget_up_app_validation.'DD'.$copy_quotation_id;
	//	..list($copy_quotation)= $sql_vari_seting_copy_quotation;
	//$copy_quotation_id=$copy_quotation[csf('copy_quotation')];
	
	//echo $copy_quotation_id."=select copy_quotation from variable_order_tracking where company_name=$cbo_company_name and variable_list=20 and status_active=1 and is_deleted=0";die;
	
	$buyer_ids_array = array();
	$buyerData = sql_select("select user_id, sequence_no, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0 ");//and bypass=2//echo "select user_id, sequence_no, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0 and bypass=2";
	foreach($buyerData as $row)
	{
		$buyer_ids_array[$row[csf('user_id')]]['u']=$row[csf('buyer_id')];
		$buyer_ids_array[$row[csf('sequence_no')]]['s']=$row[csf('buyer_id')];
	}
	//print_r($buyer_ids_array);
	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Pre-Costing.</font>";
		die;
	}

	 //echo $previous_approved;die;
	if($previous_approved==1 && $approval_type==1)
	{
		$sequence_no_cond=" and c.sequence_no<'$user_sequence_no'";

		$sql="select a.garments_nature, b.id, b.job_id,b.sourcing_approved, a.quotation_id, a.job_no_prefix_num, $year_cond, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, b.sew_smv, b.approved, b.inserted_by, c.id as approval_id, c.sequence_no, c.approved_by, c.id as approval_id, (select max(h.approved_no) from approval_history h where b.id=h.mst_id and h.entry_form=15 $alter_user_cond_2) as revised_no,a.brand_id,a.pro_sub_dep,min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date,a.product_dept,b.remarks
		  from wo_pre_cost_mst b,  wo_po_details_master a,approval_history c,wo_po_break_down d
		  where b.id=c.mst_id and c.entry_form=15 and a.job_no=b.job_no and a.company_name=$company_name and a.job_no=d.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.current_approval_status=1 and b.ready_to_approved=1 and b.is_deleted=0 and b.approved in (1,3) $buyer_id_cond $date_cond $job_no_cond $file_no_cond $internal_ref_cond $sequence_no_cond $job_year_cond $alter_user_cond $working_company_cond group by a.garments_nature, b.id, a.quotation_id, b.job_id, a.job_no_prefix_num, $year_cond_groupby, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, b.sew_smv, b.approved,b.sourcing_approved, b.inserted_by, c.id, c.sequence_no, c.approved_by, c.id,a.brand_id,a.pro_sub_dep,a.product_dept,b.remarks";
		//$buyer_id_cond
	}
	else if($approval_type==2)
	{
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
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";

			$sql="select a.garments_nature, b.id, b.job_id,b.sourcing_approved, a.quotation_id, a.job_no_prefix_num, $year_cond, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, '0' as approval_id, b.approved, b.sew_smv, b.inserted_by, b.entry_from, (select max(c.approved_no) from approval_history c where b.id=c.mst_id and c.entry_form=15 $alter_user_cond) as revised_no ,a.brand_id,a.pro_sub_dep,min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date,a.product_dept,b.remarks from wo_pre_cost_mst b, wo_po_details_master a, wo_po_break_down d where a.job_no=b.job_no and a.job_no=d.job_no_mst and a.company_name=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 and b.approved in (0,2) $buyer_id_cond $buyer_id_cond2 $date_cond $job_no_cond $job_year_cond $internal_ref_cond $file_no_cond $working_company_cond group by a.garments_nature, b.id, a.quotation_id, b.job_id, a.job_no_prefix_num, $year_cond_groupby, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date,b.sourcing_approved, '0', b.approved, b.sew_smv, b.inserted_by, b.entry_from,a.brand_id,a.pro_sub_dep,a.product_dept,b.remarks";
			//echo $sql;die;
		}
		else if($sequence_no == "")
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in ($buyer_ids)";

			if($buyer_ids=="") $buyer_id_cond3=""; else $buyer_id_cond3=" and c.buyer_name in($buyer_ids)";

				//$seqSql="select sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no=$user_sequence_no and is_deleted=0 order by sequence_no desc";
				$seqSql="select sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
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
					else
					{
						$sequence_no_by_yes.=$sRow[csf('sequence_no')].",";
					}
				}

				$buyerIds=chop($buyerIds,',');
				if($buyerIds=="")
				{
					$buyerIds_cond="";
					$seqCond="";
				}
				else
				{
					$buyerIds_cond=" and a.buyer_name not in($buyerIds)";
					$seqCond=" and (".chop($query_string,'or ').")";
				}
				$sequence_no_by_no=chop($sequence_no_by_no,',');
				$sequence_no_by_yes=chop($sequence_no_by_yes,',');

				if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
				if($sequence_no_by_no=="") $sequence_no_by_no=0;

				$pre_cost_id='';
				$pre_cost_id_sql="select distinct (mst_id) as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_name and b.sequence_no in ($sequence_no_by_no) and b.entry_form=15 and b.current_approval_status=1 $buyer_id_cond3 $job_no_cond2 $seqCond
				union
				select distinct (mst_id) as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_name and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=15 and b.current_approval_status=1 $buyer_id_cond3";
				$bResult=sql_select($pre_cost_id_sql);
				foreach($bResult as $bRow)
				{
					$pre_cost_id.=$bRow[csf('pre_cost_id')].",";
				}

				$pre_cost_id=chop($pre_cost_id,',');

				$pre_cost_id_app_sql=sql_select("select b.mst_id as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c
				where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_name and b.sequence_no=$user_sequence_no and b.entry_form=15 and a.ready_to_approved=1 and b.current_approval_status=1  $job_no_cond2");

				foreach($pre_cost_id_app_sql as $inf)
				{
					if($pre_cost_id_app_byuser!="") $pre_cost_id_app_byuser.=",".$inf[csf('pre_cost_id')];
					else $pre_cost_id_app_byuser.=$inf[csf('pre_cost_id')];
				}

				$pre_cost_id_app_byuser=implode(",",array_unique(explode(",",$pre_cost_id_app_byuser)));
			//}// 12-10-2018

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
				else
				{
					$pre_cost_id_cond=" and b.id not in($pre_cost_id_app_byuser)";
				}
			}
			else $pre_cost_id_cond="";

			$sql="select a.garments_nature, b.id, b.job_id,b.sourcing_approved, a.quotation_id, a.job_no_prefix_num, $year_cond, a.job_no, a.buyer_name, a.style_ref_no, b.sew_smv, b.costing_date, 0 as approval_id, b.approved, b.inserted_by, b.entry_from, (select max(c.approved_no) from approval_history c where b.id=c.mst_id and c.entry_form=15 $alter_user_cond) as revised_no,a.brand_id,a.pro_sub_dep,min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date,a.product_dept,b.remarks
			from wo_pre_cost_mst b ,wo_po_details_master a, wo_po_break_down d
			where a.job_no=b.job_no and a.job_no=d.job_no_mst and a.company_name=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 and b.approved in (0,2) $buyer_id_cond $date_cond $pre_cost_id_cond $buyerIds_cond $buyer_id_cond2 $job_no_cond $file_no_cond $internal_ref_cond $job_year_cond $working_company_cond group by a.garments_nature, b.id, b.job_id,b.sourcing_approved, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby, a.job_no, a.buyer_name, a.style_ref_no, b.sew_smv, b.costing_date, b.approved, b.inserted_by, b.entry_from ,a.brand_id,a.pro_sub_dep,a.product_dept,b.remarks";
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
				else
				{
					$pre_cost_id_cond2.="  b.id  in($pre_cost_id)";	 
				}

				$sql.=" union all
				select a.garments_nature, b.id, b.job_id, b.sourcing_approved, a.quotation_id, a.job_no_prefix_num, $year_cond, a.job_no, a.buyer_name, a.style_ref_no, b.sew_smv, b.costing_date, 0 as approval_id, b.approved, b.inserted_by, b.entry_from, (select max(c.approved_no) from approval_history c where b.id=c.mst_id and c.entry_form=15 $alter_user_cond) as revised_no,a.brand_id,a.pro_sub_dep,min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date,a.product_dept,b.remarks
				from wo_pre_cost_mst b, wo_po_details_master a, wo_po_break_down d
				where a.job_no=b.job_no and a.job_no=d.job_no_mst and a.company_name=$company_name  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 and b.approved in(3) $pre_cost_id_cond2 $buyer_id_cond $buyer_id_cond2 $date_cond $job_no_cond $file_no_cond $internal_ref_cond $job_year_cond $working_company_cond group by a.garments_nature, b.id, b.job_id, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby, a.job_no, a.buyer_name, a.style_ref_no, b.sew_smv, b.costing_date, b.approved, b.inserted_by, b.entry_from,b.sourcing_approved,a.brand_id,a.pro_sub_dep,a.product_dept,b.remarks";//b.approved in(1,3)
			}
			//echo $sql; die;
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
					$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and
					 sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");// and bypass=1
				}
				else
				{
					$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no)
					 as sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");//bypass=1 and
				}
			}

			if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$sequence_no'";
			else $sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";

			  $sql="select a.garments_nature, b.id, b.job_id,b.sourcing_approved, a.quotation_id, a.job_no_prefix_num, $year_cond, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, b.approved, b.inserted_by, c.id as approval_id, c.sequence_no, c.approved_by, c.id as approval_id, b.entry_from, b.sew_smv, (select max(h.approved_no) from approval_history h where b.id=h.mst_id and h.entry_form=15 $alter_user_cond_2) as revised_no,a.brand_id,a.pro_sub_dep,min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date,a.product_dept,b.remarks
			      from wo_pre_cost_mst b, wo_po_details_master a, approval_history c, wo_po_break_down d
				  where b.id=c.mst_id and c.entry_form=15 and a.job_no=b.job_no and a.company_name=$company_name and a.job_no=d.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.current_approval_status=1 and b.is_deleted=0 and b.approved in (1,3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $date_cond $job_no_cond $file_no_cond $internal_ref_cond $job_year_cond $working_company_cond group by a.garments_nature, b.id, b.job_id,b.sourcing_approved, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby, a.job_no, a.buyer_name, b.sew_smv, a.style_ref_no, b.costing_date, b.approved, b.inserted_by, c.id, c.sequence_no, c.approved_by, b.entry_from ,a.brand_id,a.pro_sub_dep,a.product_dept,b.remarks"; //and b.ready_to_approved=1
		}
	}
	else
	{ 
		$buyer_ids=$buyer_ids_array[$user_id]['u'];
		if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";

		$sequence_no_cond=" and c.sequence_no='$user_sequence_no'";

		$sql="select a.garments_nature, b.id, b.job_id,b.sourcing_approved, a.quotation_id, a.job_no_prefix_num, $year_cond, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, b.sew_smv, b.approved, b.inserted_by, c.id as approval_id, c.sequence_no, c.approved_date, c.approved_by, c.id as approval_id, b.entry_from,(select max(h.approved_no) from approval_history h where b.id=h.mst_id and h.entry_form=15 $alter_user_cond_2) as revised_no,a.brand_id,a.pro_sub_dep,min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date,a.product_dept,b.remarks
			from wo_pre_cost_mst b, wo_po_details_master a,approval_history c,wo_po_break_down d
			where b.id=c.mst_id and c.entry_form=15 and a.job_no=b.job_no and a.company_name=$company_name and a.job_no=d.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.current_approval_status=1 and b.is_deleted=0 and b.approved in (1,3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $date_cond $job_no_cond $file_no_cond $internal_ref_cond $job_year_cond $working_company_cond  group by a.garments_nature, b.id, b.job_id,b.sourcing_approved, a.quotation_id, a.quotation_id, b.sew_smv, a.job_no_prefix_num, $year_cond_groupby, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, b.approved, b.inserted_by, c.id, c.sequence_no, c.approved_by, c.id, c.approved_date, b.entry_from,a.brand_id,a.pro_sub_dep,a.product_dept,b.remarks"; //and b.ready_to_approved=1
	}

	 //echo $sql; //die;
	

	$sql_unapproved=sql_select("select booking_id,approval_cause from fabric_booking_approval_cause where  entry_form=15 and approval_type=2 and is_deleted=0 and status_active=1");
	$unapproved_request_arr=array();
	foreach($sql_unapproved as $rowu)
	{
		$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
	}

	//Pre cost button---------------------------------
	$print_report_format_ids = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id in (43) and is_deleted=0 and status_active=1");
	$format_ids=explode(",",$print_report_format_ids);
	$row_id=$format_ids[0];
	$print_report_format_ids_wvn = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id in (122) and is_deleted=0 and status_active=1");
	$format_ids_wvn=explode(",",$print_report_format_ids_wvn);
	$row_id_wvn=$format_ids_wvn[0];
	// echo $row_id.'d';

	//Order Wise Budget Report button---------------------------------
	$print_report_format_ids2 = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=11 and report_id=18 and is_deleted=0 and status_active=1");
	$format_ids2=explode(",",$print_report_format_ids2);
	$row_id2=$format_ids2[0];
	$job_no_arrs=array();
	$jobIds='';
   $nameArray2=sql_select( $sql );
   foreach( $nameArray2 as $row)
   {
	  $job_id_arr[$row[csf('job_id')]]=$row[csf('job_id')];
	  array_push($job_no_arrs, $row[csf('job_no')]);
	  if($jobIds=='') $jobIds=$row[csf('job_id')]; else $jobIds.=','.$row[csf('job_id')];
	 // echo $row[csf('job_id')].',ddd';;
   }
   if($db_type==0) $jobYearCond="and YEAR(a.insert_date)=$cbo_year"; else if($db_type==2) $jobYearCond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
   //echo $jobIds;
	$misc_cost=0;
  	if(!empty($jobIds))
	{
		$condition= new condition();
		$condition->company_name("=$cbo_company_name");
		if(str_replace("'","",$cbo_buyer_name)>0){
			$condition->buyer_name("=$cbo_buyer_name");
		}
		if($jobIds!=''){
			$condition->jobid_in("$jobIds");
		}
		if(str_replace("'","",$cbo_year)!=0){
			  $condition->job_year("$jobYearCond");
		 }
		if(str_replace("'","",$file_no)!='')
		{
			$condition->file_no("=$file_no"); 
		}
		if(str_replace("'","",$internal_ref)!='')
		{
			$condition->grouping("=$internal_ref"); 
		}
		
		$condition->init();
		$yarn= new yarn($condition);
		//echo $yarn->getQuery(); 
		$yarn_data_array=$yarn->getJobWiseYarnAmountArray();
		//echo "<pre>";
		//print_r($yarn_data_array);
		//echo "</pre>";
		$fabric= new fabric($condition);
		//echo $fabric->getQuery(); 
		$fabric_amount=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
		$conversion= new conversion($condition);
		$conv_amount_arr=$conversion->getAmountArray_by_jobAndProcess();

		$commercial= new commercial($condition);
		$commercial_costing_arr=$commercial->getAmountArray_by_job();
		$commission= new commision($condition);
		$commission_costing_arr=$commission->getAmountArray_by_job();
		$other= new other($condition);
		$other_costing_arr=$other->getAmountArray_by_job();
	}
	
	$imge_arr=return_library_array( "select master_tble_id, image_location from common_photo_library where file_type=1 ".where_con_using_array($job_no_arrs,1,'master_tble_id')."",'master_tble_id','image_location');
	
	 $sql_intRef=sql_select("select job_no_mst,grouping,file_no from wo_po_break_down where is_deleted=0 and status_active=1  ".where_con_using_array($job_id_arr,1,'job_id')." ");
	$intRef=array();
	foreach($sql_intRef as $rowData)
	{
		//$intRef[$rowData[csf('job_no_mst')]]["internalRef"].=$rowData[csf('grouping')].",";
		$intRef[$rowData[csf('job_no_mst')]]["internalRef"].=$rowData[csf('grouping')].",";
		$intRef[$rowData[csf('job_no_mst')]]["fileNo"]=$rowData[csf('file_no')];
	}
	
	$sql_pre_sourcing=sql_select("select job_no,sourcing_approved from wo_pre_cost_mst where is_deleted=0 and status_active=1  ".where_con_using_array($job_id_arr,0,'job_id')." ");
	//echo "select job_no,sourcing_approved from wo_pre_cost_mst where is_deleted=0 and status_active=1  ".where_con_using_array($job_id_arr,0,'job_id')." ";
	$jobSourcing_arr=array();
	foreach($sql_pre_sourcing as $row)
	{
		$jobSourcing_arr[$row[csf('job_no')]]["sourcing_approved"]=$row[csf('sourcing_approved')];
	}
	unset($sql_pre_sourcing);
	
		 $pre_data_array = sql_select("select job_no,costing_per_id as costing_per,job_id, fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent,certificate_pre_cost,certificate_percent,common_oh,common_oh_percent,total_cost,deffdlc_cost,deffdlc_percent,interest_cost,interest_percent,incometax_cost,incometax_percent,total_cost_percent,price_dzn,price_dzn_percent,margin_dzn,margin_dzn_percent,price_pcs_or_set,depr_amor_pre_cost,depr_amor_po_price,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche
		from wo_pre_cost_dtls
		where   status_active=1 and is_deleted=0  ".where_con_using_array($job_id_arr,1,'job_id')."");
		 
			//$pre_data_array=sql_select($sql_pre);
			foreach( $pre_data_array as $row )
            { 
				// $job_epm_arr=
				$cm_cost_arr[$row[csf('job_no')]]['cm_cost']+=$row[csf('cm_cost')];
				 if($row[csf("costing_per")]==1){$order_price_per_dzn=12;}
				else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;}
				else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;}
				else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;}
				else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;}
				else {$order_price_per_dzn=0;}
				//echo $order_price_per_dzn.', ';
				
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
				//deffdlc_cost,deffdlc_percent,interest_cost,interest_percent,incometax_cost,incometax_percent
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
				//interest_cost,interest_percent,incometax_cost
				//$tot_commission=$commission_costing_arr[$job_no];
				$lab_test=$lab_test_cost;
				$inspection=$inspection_cost;
				$cm_cost=$cm_cost;
				$freight=$freight_cost;
				$currier_pre_cost=$currier_cost;
				$certificate_pre_cost=$certificate_cost;
				
				$material_service_cost_dzn=$fabric_cost_dzn+$trims_cost_dzn+$embel_cost_dzn+$wash_cost_dzn+$deffdlc_cost_dzn+$inspection_dzn+$lab_test_dzn+$freight_dzn+$currier_pre_cost_dzn+$certificate_pre_cost_dzn;
				
				$net_fob_value_dzn=$price_dzn-$commission_dzn;
				$contributions_value_dzn=$net_fob_value_dzn-$material_service_cost_dzn-$comm_cost_dzn;
				//$material_service_contribute=$net_fob_value_dzn-$total_fab_cost_dzn;
				//echo $contributions_value_dzn.'='.$net_fob_value_dzn.', '; 
				 $job_epm_arr[$row[csf('job_no')]]=$contributions_value_dzn/$order_price_per_dzn;
				 $job_epm_contribute_margin_arr[$row[csf('job_no')]]=$contributions_value_dzn.', CostPer='.$order_price_per_dzn;
			}
			$fab_sql=sql_select("select job_no,avg_finish_cons,amount from wo_pre_cost_fabric_cost_dtls where is_deleted=0 and status_active=1  ".where_con_using_array($job_id_arr,1,'job_id')."");
			//echo "select job_no,avg_finish_cons,amount from wo_pre_cost_fabric_cost_dtls where is_deleted=0 and status_active=1  ".where_con_using_array($job_id_arr,0,'job_id')."";
			
			foreach($fab_sql as $row)
			{
				//$cm_cost_arr[$row[csf('job_no')]]=$row[csf('cm_cost')];
				//$job_cost_arr[$row[csf('job_no')]]['trims_cost']=$row[csf('trims_cost')];
				$job_cost_arr[$row[csf('job_no')]]['fabric_cost']+=$row[csf('amount')];
			}
			$trim_sql=sql_select("select job_no,amount from wo_pre_cost_trim_cost_dtls where is_deleted=0 and status_active=1   ".where_con_using_array($job_id_arr,0,'job_id')."");
			
			//echo "select job_no,amount from wo_pre_cost_trim_cost_dtls where is_deleted=0 and status_active=1   ".where_con_using_array($job_id_arr,0,'job_id')."";
			
			foreach($trim_sql as $row)
			{
				//$cm_cost_arr[$row[csf('job_no')]]=$row[csf('cm_cost')];
				//$job_cost_arr[$row[csf('job_no')]]['trims_cost']=$row[csf('trims_cost')];
				$job_cost_arr[$row[csf('job_no')]]['trims_cost']+=$row[csf('amount')];
			}
	
			$job_cond=where_con_using_array(array_unique($job_no_arrs),1,"job_no");

			$sql_job_data=sql_select("SELECT job_quantity,total_set_qnty ,total_price,job_quantity,job_no from wo_po_details_master where status_active=1 $job_cond");
			
			$job_wise_data=array();
			foreach ($sql_job_data as $row) 
			{
				$job_wise_data[$row[csf('job_no')]]['job_qty_pcs']=$row[csf('job_quantity')]*$row[csf('total_set_qnty')];
				$job_wise_data[$row[csf('job_no')]]['jobavgRate']=fn_number_format($row[csf('total_price')]/$row[csf('job_quantity')],4,".","");
				$job_wise_data[$row[csf('job_no')]]['total_price']=$row[csf('total_price')];
				
			}
			$image_arr=array();
			$master_tble_id=where_con_using_array(array_unique($job_no_arrs),1,"master_tble_id");
			$sql_img=sql_select("select image_location,master_tble_id from common_photo_library where   form_name='knit_order_entry' and file_type=1 $master_tble_id group by image_location,master_tble_id");


			foreach ($sql_img as $row) {
				$image_arr[$row[csf('master_tble_id')]].=$row[csf('image_location')]."***";
			}
			//print_r($image_arr);

			$bomDtls_arr=array(); $margin_pcs_arr=array();
			$bomDtlssql=sql_select( "select job_no, fabric_cost_percent, trims_cost_percent, embel_cost_percent, wash_cost_percent, cm_cost_percent, margin_pcs_set_percent, cm_cost, margin_pcs_set from wo_pre_cost_dtls where status_active=1 and is_deleted=0 $job_cond");
			
			foreach ($bomDtlssql as $row)
			{
				$bomDtls_arr[$row[csf('job_no')]]['trimper']=$row[csf('trims_cost_percent')];
				$bomDtls_arr[$row[csf('job_no')]]['cm']=$row[csf('cm_cost_percent')];
				$bomDtls_arr[$row[csf('job_no')]]['ms']=$row[csf('fabric_cost_percent')]+$row[csf('trims_cost_percent')]+$row[csf('embel_cost_percent')]+$row[csf('wash_cost_percent')];
				$bomDtls_arr[$row[csf('job_no')]]['margin']=$row[csf('margin_pcs_set_percent')];
				$bomDtls_arr[$row[csf('job_no')]]['fcp']=$row[csf('fabric_cost_percent')];
				$bomDtls_arr[$row[csf('job_no')]]['ecp']=$row[csf('embel_cost_percent')];
				$bomDtls_arr[$row[csf('job_no')]]['cm_cost']=$row[csf('cm_cost')];
				$margin_pcs_arr[$row[csf('job_no')]]=$row[csf('margin_pcs_set')];
			}
			unset($bomDtlssql);

			//$margin_pcs_arr=return_library_array( "select job_no,margin_pcs_set from wo_pre_cost_dtls where status_active=1 and is_deleted=0 $job_cond",'job_no','margin_pcs_set');
			
			// echo  $sourcing_approved.'DD'; 
	?>
	<script type="text/javascript">
		function openmypage_unapp_request(wo_id,app_type,i)
		{
			var data=wo_id;
			var title = 'Un Approval Request';
			var page_link = 'requires/pre_costing_approval_controller.php?data='+data+'&action=unappcause_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=180px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var unappv_request=this.contentDoc.getElementById("hidden_unappv_request");
				$('#txt_unappv_req_'+i).val(unappv_request.value);
			}
		}
	</script>
	
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:2642px; margin-top:10px">
        <legend>Pre-Costing Approval</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2662" class="rpt_table" >
                <thead>
                	<th width="30">&nbsp;</th>
                    <th width="40" style="word-break: break-all;">SL</th>
                    <th width="65" style="word-break: break-all;">Insert By/Merchandiser</th>
                    <th width="50" style="word-break: break-all;">Image</th>
                    <th width="65" style="word-break: break-all;">Budget/ Costing Date</th>
                    <th width="80" style="word-break: break-all;">Approved<br>Date</th>
                    <th width="40" style="word-break: break-all;">Year</th>
                    <th width="50" style="word-break: break-all;">Job No</th>
                    <th width="100" style="word-break: break-all;">Last Version</th>
                    <th width="160" style="word-break: break-all;">Style Ref.</th>
                    <th width="125" style="word-break: break-all;">Buyer</th>
                    <th width="100" style="word-break: break-all;">Product Dept</th>
                    <th width="100" style="word-break: break-all;">Sub Dept</th>
					<th width="100" style="word-break: break-all;">Brand</th>
					<th width="70" style="word-break: break-all;">Job Qty(Pcs)</th>
					<th width="60" style="word-break: break-all;">Avg. Rate</th>
                    <th width="80" style="word-break: break-all;">Total Value [$]</th>
					<th width="70" style="word-break: break-all;">Ship Start</th>
                    <th width="70" style="word-break: break-all;">Ship End</th>
                    <th width="60" style="word-break: break-all;">SMV</th>
                    <th width="60" style="word-break: break-all;">CM %</th>
                    <th width="60" style="word-break: break-all;">FCM %</th>
                    <th width="60" style="word-break: break-all;">EPM </th>
                    <th width="70" style="word-break: break-all;">Yarn Cost %</th>
                   	<th width="70" style="word-break: break-all;">Trims Cost %</th>
                    <th width="65" style="word-break: break-all;">Fabric Cost %</th>
                    <th width="65" style="word-break: break-all;">Emblishment Cost %</th>
                    <th width="65" style="word-break: break-all;">MISC Cost %</th>
                    <th width="65" style="word-break: break-all;">Mergin %</th>
                    <th width="150" style="word-break: break-all;">Unapproved Request</th>
                    <th width="150" style="word-break: break-all;">Refusing Cause</th>
                    <th style="word-break: break-all;">Remarks</th>
                </thead>
            </table>
            <div style="width:2662px; overflow-y:scroll; max-height:460px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2642" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
                        // echo $sql; //die;
						//echo $print_cond."kk";
                         $i=1;
                            $nameArray=sql_select( $sql );
							// print ($sql);die;
							$ref_no = "";
							$file_numbers = "";
                            foreach ($nameArray as $row)
                            {
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$value=$row[csf('id')];

								if($row[csf('approval_id')]==0)
								{
									$print_cond=1;
								}
								else
								{
									//$row[csf('approval_id')];
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
								$cm_cost=$cm_cost_arr[$row[csf('job_no')]]['cm_cost'];
								if($cm_cost=='' || $cm_cost==0) $cm_cost=0;else $cm_cost=$cm_cost;
								if($cm_cost<0 || $cm_cost==0)
								{
									$td_color="#F00";
								}
								else
								{
									$td_color="";
								}

								/*24,Budget,
								27,Budget On Shipout,
								182,Budget Report 3,
								25,Budget Report2,
								29,C.Date Budget On Shipout,
								26Quote Vs Budget,
								,23 Summary*/


							if($row_id2==23){$type=1;/*Summary;*/}
							else if($row_id2==24){$type=2;}
							else if($row_id2==25){$type=3;/*Budget Report2;*/}
							else if($row_id2==26){$type=4;/*Quote Vs Budget;*/}
							else if($row_id2==27){$type=5;/*Budget On Shipout;*/}
							else if($row_id2==29){$type=6;/*C.Date Budget On Shipout;*/}
							else if($row_id2==182){$type=7;/*Budget Report 3;*/}

							$function2="generat_print_report($type,$cbo_company_name,0,'','',{$row[csf('job_no_prefix_num')]},'','','',".$row[csf('year')].",0,1,'','','','')";
							//{$row[csf('buyer_name')]}
							
							//echo $print_cond;die;
							
							if($print_cond==1)
							{
								//pre cost v2 entry form 158;
								//pre cost entry form 111;
							
									if($row_id_wvn==311 && $row[csf('garments_nature')]==3){$action='bom_epm_woven';}
									else if($row_id_wvn==313 && $row[csf('garments_nature')]==3){$action='mkt_source_cost';}
									else if($row_id==50){$action='preCostRpt'; } //report_btn_1;
									else if($row_id==51){$action='preCostRpt2';} //report_btn_2;
									else if($row_id==52){$action='bomRpt';} //report_btn_3;
									else if($row_id==63){$action='bomRpt2';} //report_btn_4;
									else if($row_id==142){$action='preCostRptBpkW';}
									else if($row_id==156){$action='accessories_details';} //report_btn_5;
									else if($row_id==157){$action='accessories_details2';} //report_btn_6;
									else if($row_id==158){$action='preCostRptWoven';} //report_btn_7;
									else if($row_id==159){$action='bomRptWoven';} //report_btn_8;
									else if($row_id==170){$action='preCostRpt3';} //report_btn_9;
									else if($row_id==171){$action='preCostRpt4';} //report_btn_10;
									else if($row_id==173){$action='preCostRpt5';} //report_btn_10;
									else if($row_id==192){$action='checkListRpt';}
									else if($row_id==197){$action='bomRpt3';}
									else if($row_id==211){$action='mo_sheet';}
									else if($row_id==215){$action='budget3_details';}
									else if($row_id==221){$action='fabric_cost_detail';}											
									else if($row_id==238){$action='summary';}
									else if($row_id==270){$action='preCostRpt6';}
									else if($row_id==581){$action='costsheet';}
									else if($row_id==730){$action='budgetsheet';}
									else if($row_id==268){$action='budget_4';}
									else if($row_id==129){$action='budget5';}
									else if($row_id==120){$action='budgetsheet3';}
								

								$function="generate_worder_report_pre_cost('".$action."','".$row[csf('job_no')]."',".$cbo_company_name.",".$row[csf('buyer_name')].",'".$row[csf('style_ref_no')]."','".$row[csf('costing_date')]."',".$row[csf('entry_from')].",'".$row[csf('job_id')]."',".$row['GARMENTS_NATURE'].");"; 
								
								$function2="";
								if($row[csf('revised_no')]>0)
								{
									for($q=1; $q<=$row[csf('revised_no')]; $q++)
									{
										if($function2=="") $function2="<a href='#' onClick=\"history_budget_sheet('".$row[csf('job_no')]."',".$cbo_company_name.",".$row[csf('buyer_name')].",'".$row[csf('style_ref_no')]."','".$row[csf('costing_date')]."',".$row[csf('entry_from')].",'".$row[csf('quotation_id')]."','".$q."'".")\"> ".$q."<a/>";
										else $function2.=", "."<a href='#' onClick=\"history_budget_sheet('".$row[csf('job_no')]."',".$cbo_company_name.",".$row[csf('buyer_name')].",'".$row[csf('style_ref_no')]."','".$row[csf('costing_date')]."',".$row[csf('entry_from')].",'".$row[csf('quotation_id')]."','".$q."'".")\"> ".$q."<a/>";
										
										
										/*if($variable1=="")
											$variable1="<a href='#' onClick=\"generate_worder_report_history('".$type."','".$report_type."','".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','show_fabric_booking_report3','".$i."','".$q."'".")\"> ".$q."<a/>";
										else
											$variable1.=", "."<a href='#' onClick=\"generate_worder_report_history('".$type."','".$report_type."','".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','show_fabric_booking_report3','".$i."','".$q."'".")\"> ".$q."<a/>";*/
									}
								}
								
								//$function2="history_budget_sheet('".$row[csf('job_no')]."',".$cbo_company_name.",".$row[csf('buyer_name')].",'".$row[csf('style_ref_no')]."','".$row[csf('costing_date')]."',".$row[csf('entry_from')].",'".$row[csf('quotation_id')]."',".$row[csf('revised_no')].");"; 
								// $function2 -Remove- issue-5777
								$garments_nature=$row[csf('garments_nature')];
								$trims_cost=$job_cost_arr[$row[csf('job_no')]]['trims_cost'];
								if($trims_cost=='') $trims_cost=0;else $trims_cost=$trims_cost;
								$fabric_cost=$job_cost_arr[$row[csf('job_no')]]['fabric_cost'];
								if($fabric_cost=='') $fabric_cost=0;else $fabric_cost=$fabric_cost;
								//echo $trims_cost.'fd'. $fabric_cost;
								if($copy_quotation_id==0 || $copy_quotation_id==2) $copy_quotation_id=2;else $copy_quotation_id=$copy_quotation_id;
								
								

								
								$sourcing_approved=0;
								if($budget_up_app_validation==1) //Budget Sourcing
								{
									 $sourcing_approved=$jobSourcing_arr[$row[csf('job_no')]]["sourcing_approved"];
									//  echo  $budget_up_app_validation.'DD';
									if($sourcing_approved=="")  $sourcing_approved=0;
								}
								$yarnPercent=0;
								$yarnPercent=($yarn_data_array[$row[csf('job_no')]]/$job_wise_data[$row[csf('job_no')]]['total_price'])*100;
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" align="center">
                                	<td width="27" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="booking_id_<? echo $i;?>" name="booking_id[]" type="hidden" value="<? echo $value; ?>" />
                                        <input id="booking_no_<? echo $i;?>" name="booking_no[]" type="hidden" value="<? echo $row[csf('job_no')]; ?>" />
                                        <input id="approval_id_<? echo $i;?>" name="approval_id[]" type="hidden" value="<? echo $row[csf('approval_id')]; ?>" />
                                        <input id="<? echo strtoupper($row[csf('job_no')]); ?>" name="no_joooob[]" type="hidden" value="<? echo $i;?>" />
                                        <input id="cm_cost_id_<? echo $i;?>" name="cm_cost_id[]" style="width:20px;" type="hidden" value="<? echo $cm_cost; ?>" />
                                        <input id="fabric_id_cost_<? echo $i;?>" name="fabric_id_cost[]" style="width:20px;" type="hidden" value="<? echo $fabric_cost; ?>" />
                                        <input id="trims_id_cost_<? echo $i;?>" name="trims_id_cost[]" style="width:20px;" type="hidden" value="<? echo $trims_cost; ?>" />
                                        <input id="garments_nature_<? echo $i;?>" name="garments_nature[]" style="width:20px;" type="hidden" value="<? echo $garments_nature; ?>" />
                                        <input id="copy_quot_<? echo $i;?>" name="copy_quot[]" style="width:20px;" type="hidden" value="<? echo $copy_quotation_id; ?>" />
                                        <input id="sourcing_approved_id_<? echo $i;?>" name="sourcing_approved_id[]" style="width:20px;" type="hidden" value="<? echo $sourcing_approved; ?>" />
                                        
                                        <input id="mst_id_company_id_<?=$i;?>" name="mst_id_company_id[]" type="hidden" value="<?=$row[csf('job_no')].'*'.$value.'*'.$company_name; ?>" />
                                        
                                    </td>
									<td width="40" align="center"><? echo  $i; ?></td>
									<td width="65"><p><? echo ucfirst ($user_arr[$row[csf('inserted_by')]]); ?>&nbsp;</p></td>
                                    <td width="50" onClick="openmypage_image('requires/pre_costing_approval_controller.php?action=show_image&job_no=<?=$row[csf('job_no')]; ?>','Image View')"><img src='../<?=$imge_arr[$row[csf('job_no')]]; ?>' height='25' width='30' /></td>
									
									<td width="65" align="center"><? if($row[csf('costing_date')]!="0000-00-00") echo change_date_format($row[csf('costing_date')]); ?>&nbsp;</td>
									<td width="80" align="center"><? if($row[csf('approved_date')]!="0000-00-00") echo change_date_format($row[csf('approved_date')]); ?>&nbsp;</td>
									<td width="40"><p><? echo $row[csf('year')]; ?>&nbsp;</p></td>
									<td width="50">
                                    	<p><a href='##'  onclick="<? echo $function; ?>"><? echo $row[csf('job_no_prefix_num')]; ?></a></p>
                                    </td>
                                    <td width="100" align="center" style="word-break:break-all" title="<?=$row[csf('revised_no')]; ?>">&nbsp;&nbsp;<?=$function2; ?></td>
                                    <td width="160" align="center"><p><?=$row[csf('style_ref_no')]; ?></p></td>
                                    <td width="125"><p><?=$buyer_arr[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
                                    <td width="100"><?=$product_dept[$row[csf('product_dept')]];?></td>
                                    <td width="100" align="center"><p><? echo $sub_dep_arr[$row[csf('pro_sub_dep')]]; ?></p></td>
									<td width="100" align="center"><p><? echo $brand_arr[$row[csf('brand_id')]]; ?></p></td>
									<td width="70" align="right" style="word-break:break-all;"><?=number_format($job_wise_data[$row[csf('job_no')]]['job_qty_pcs']); ?></td>
									<td width="60" align="right" style="word-break:break-all;"><?=number_format($job_wise_data[$row[csf('job_no')]]['jobavgRate'],4); ?></td>
                                    <td width="80" align="right" style="word-break:break-all;"><?='$'.number_format($job_wise_data[$row[csf('job_no')]]['total_price'],2); ?></td>
									<td align="center" width="70"><? if($row[csf('minship_date')]!="0000-00-00") echo change_date_format($row[csf('minship_date')]); ?>&nbsp;</td>
     								<td align="center" width="70"><? if($row[csf('maxship_date')]!="0000-00-00") echo change_date_format($row[csf('maxship_date')]); ?>&nbsp;</td>
     								<td width="60" align="right"><p style="color:<? //echo $td_color; ?>"><? echo $row[csf('sew_smv')];; ?>&nbsp;</p></td>

                                     <td width="60" align="right"><p style="color:<? echo $td_color; ?>"><? echo number_format($bomDtls_arr[$row[csf('job_no')]]['cm_cost'],4).'%'; ?>&nbsp;</p></td>

                                     <td width="60" align="right" style="word-break:break-all;" ><? echo number_format($bomDtls_arr[$row[csf('job_no')]]['cm'],4).'%'; ?></td>

                                     <td width="60"  title="Contribution Margin/Costing Per/SMV(<? echo $job_epm_contribute_margin_arr[$row[csf('job_no')]];?>)" align="right"><p style="color:<? //echo $td_color; ?>"><? echo number_format($job_epm_arr[$row[csf('job_no')]]/$row[csf('sew_smv')],4); ?>&nbsp;</p></td>
                                    
                                    <td align="center" width="70" style="word-break:break-all;"><?=fn_number_format($yarnPercent,2).'%'; ?></td>
                                    <td align="center" width="70" style="word-break:break-all;"><?=fn_number_format($bomDtls_arr[$row[csf('job_no')]]['trimper'],2).'%'; ?></td>
									<td align="center" width="65" style="word-break:break-all;"><?=fn_number_format($bomDtls_arr[$row[csf('job_no')]]['fcp'],2).'%';?>&nbsp;</td>

									<td align="center" width="65" style="word-break:break-all;"><?=fn_number_format($bomDtls_arr[$row[csf('job_no')]]['ecp'],2).'%';?>&nbsp;</td>
									<?php 
											$misc=0;
											$other_cost_attr = array('inspection','freight','certificate_pre_cost','deffdlc_cost','design_cost','studio_cost','common_oh','interest_cost','incometax_cost','depr_amor_pre_cost');
											$total_other_cost = 0;
											foreach ($other_cost_attr as $attr) {
												$total_other_cost+=$other_costing_arr[$row[csf('job_no')]][$attr];
											}
											$misc_cost=$other_costing_arr[$row[csf('job_no')]]['lab_test']+$commercial_costing_arr[$row[csf('job_no')]]+$commission_costing_arr[$row[csf('job_no')]]+$total_other_cost;

											$misc=($misc_cost/$job_wise_data[$row[csf('job_no')]]['total_price'])*100;
									 ?>
									<td align="center" width="65" style="word-break:break-all;"><?=fn_number_format($misc,2).'%';?>&nbsp;</td>
									<td align="center" width="65" style="word-break:break-all;"><?=fn_number_format($bomDtls_arr[$row[csf('job_no')]]['margin'],2).'%';?>&nbsp;</td>
                                    <td width="150" style="word-break:break-all">
									<?
										
										$unp_cause=$unapproved_request_arr[$value];
											if($approval_type==1)echo "<input name='txt_unappv_req[]' value='".$unp_cause."' class='text_boxes' readonly placeholder='Please Browse' ID='txt_unappv_req_".$i."' style='width:100px' maxlength='50' onClick='openmypage_unapp_request(".$value.",".$approval_type.",".$i.")'>";
									?>
                                    </td>
                                    <?
									$mst_id=$row[csf('id')];
									$refusing_reason_arr =sql_select("SELECT id, refusing_reason from   refusing_cause_history where  mst_id='$mst_id'  order by id desc ");							  
								//	 print_r($refusing_reason_arr);
									 ?>
                                    <td width="150"> <input style="width:120px;" type="text" class="text_boxes"  name="txtCause_<? echo $row[csf('id')];?>" id="txtCause_<?=$row[csf('id')];?>" placeholder="browse" onClick="openmypage_refusing_cause('requires/pre_costing_approval_controller.php?action=refusing_cause_popup','Refusing Cause','<? echo $row[csf('id')];?>');" value="<?  echo $refusing_reason_arr[0][csf('refusing_reason')]; //$row[csf('refusing_cause')];?>"/></td>
                                    <td><?=$row[csf('remarks')]?></td>
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
								if($db_type==2 || $db_type==1 )
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
						$denyBtn=""; $denyBtnMsg=""; $btnmsg=""; $isApp="";
						if($approval_type==2) 
						{
							$denyBtn=""; 
							$btnmsg="Approve";
							$denyBtnMsg="Deny";
							$isApp="";
						}
						else 
						{
							$denyBtn=" display:none";
							$btnmsg="Un-Approve";
							$denyBtnMsg="";
							$isApp=" display:none";
						}
					
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="2542" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" style=" <?=$isApp; ?> "><input type="checkbox" id="all_check" onClick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<?=$btnmsg; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<?=$i; ?>,<?=$approval_type; ?>);"/>&nbsp;&nbsp;&nbsp;<input type="button" value="<?=$denyBtnMsg; ?>" class="formbutton" style="width:100px;<?=$denyBtn; ?>" onClick="submit_approved(<?=$i; ?>,5);"/></td>
				</tfoot>
			</table>
        </fieldset>
    </form>
	<?
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
	$txt_color_id=str_replace("'",'',$txt_color_id);
	$txt_po_breack_down_id = str_replace("'",'',$txt_po_breack_down_id);
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
		$txt_po_breack_down_id_cond4='';
	}
	else
	{
		$selected_po_breack_down_id = str_replace("'",'',$txt_po_breack_down_id);
		$txt_po_breack_down_id_cond=" and b.id in(".$selected_po_breack_down_id.")";
		$txt_po_breack_down_id_cond1=" and c.po_break_down_id in(".$selected_po_breack_down_id.")";
		$txt_po_breack_down_id_cond4=" and  po_break_down_id in(".$selected_po_breack_down_id.")";
	}
	if(str_replace("'",'',$txt_color_id)=="")
	{
		$txt_color_id_cond='';
		$color_id_cond="";
	}
	else
	{
		$selected_txt_color_id = str_replace("'",'',$txt_color_id);
		$txt_color_id_cond=" and c.color_number_id in(".$selected_txt_color_id.")";
		$color_id_cond=" and  color_number_id in(".$selected_txt_color_id.")";
	}
	
	$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=".$cbo_company_name."  and variable_list=22 and status_active=1 and is_deleted=0");
	if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;

	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

	//array for display name
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$body_part_type_arr=return_library_array("select id, body_part_full_name from lib_body_part","id","body_part_full_name");



	 $po_qty=0; $po_plun_cut_qty=0; $total_set_qnty=0;$total_fob_value=0;$job_in_orders=''; $pulich_ship_date=''; $job_in_file=''; $job_in_ref='';
	 $sql_po="SELECT a.job_no,a.location_name, a.total_set_qnty, b.id, b.po_number, b.grouping, b.file_no, b.pub_shipment_date, c.order_total,c.item_number_id, c.country_id, c.color_number_id, c.size_number_id, c.order_quantity, c.plan_cut_qnty,b.unit_price  from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.job_no =".$txt_job_no." $txt_po_breack_down_id_cond $txt_color_id_cond  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id";
	 
	$sql_po_data=sql_select($sql_po);
	foreach($sql_po_data as $sql_po_row)
	{
		$po_qty+=$sql_po_row[csf('order_quantity')];
		$po_plun_cut_qty+=$sql_po_row[csf('plan_cut_qnty')];
		$total_set_qnty=$sql_po_row[csf('total_set_qnty')];
		$location_name_id=$sql_po_row[csf('location_name')];
		$po_unit_price_arr[$sql_po_row[csf('unit_price')]]=$sql_po_row[csf('unit_price')];
		
		$pulich_ship_date = $sql_po_row[csf('pub_shipment_date')];
		$job_in_file .= $sql_po_row[csf('file_no')].",";
		$job_in_ref .= $sql_po_row[csf('grouping')].",";
		$total_fob_value+=$sql_po_row[csf('order_total')];
		$po_id_arr[$sql_po_row[csf('id')]]=$sql_po_row[csf('id')];
		$po_no_arr[$sql_po_row[csf('po_number')]]=$sql_po_row[csf('po_number')];
		$gmts_item_arr[$sql_po_row[csf('id')]][$sql_po_row[csf('color_number_id')]]['gmts_item']=$sql_po_row[csf('item_number_id')];
	}
	$po_id_str= implode( ",",$po_id_arr);
	$poNoArr= implode( ",",$po_no_arr);
	
	$min_max_ship_date=sql_select("select b.job_no_mst, min(b.pub_shipment_date) as min_pub_shipment_date, max(b.pub_shipment_date) as max_pub_shipment_date from wo_po_break_down  b where b.job_no_mst=".$txt_job_no." $txt_po_breack_down_id_cond and b.status_active=1 and b.is_deleted=0 group by b.job_no_mst");
	foreach($min_max_ship_date as $row){
		$min_pub_ship_date=$row[csf('min_pub_shipment_date')];
		$max_pub_ship_date=$row[csf('max_pub_shipment_date')];
	}
	$shipment_country=sql_select("SELECT country_id from wo_po_color_size_breakdown where job_no_mst=".$txt_job_no." and status_active=1 and is_deleted=0 group by country_id");
	foreach($shipment_country as $row){
		$all_country_arr[$row[csf('country_id')]]=$country_library[$row[csf('country_id')]];
	}

	$approv_data_array=sql_select(" select a.job_no,b.id,b.approved_by,b.approved_no, b.current_approval_status,b.un_approved_reason, c.user_full_name,c.designation,d.approval_cause from wo_pre_cost_mst a join approval_history b on a.id=b.mst_id join  user_passwd c on b.approved_by=c.id left join fabric_booking_approval_cause d on a.id =d.booking_id  where   b.entry_form=15 $job_no order by b.id asc");
			
			foreach($approv_data_array as $row)
			{			
				$current_approval_status=$row[csf('current_approval_status')];
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
		$job_no=$row[csf("job_no")];
		$style_ref_no=$row[csf("style_ref_no")];
		$style_desc=$row[csf("style_description")];
		$uom_id=$row[csf("order_uom")];
		$approved=$row[csf("approved")];
		$budget_minute=$row[csf("budget_minute")];
		$costing_per_id=$row[csf("costing_per")];
		$job_quantity=$row[csf("job_quantity")];
		$quotation_id= $row[csf("quotation_id")];
		
		if($txt_color_id !=="" || $txt_po_breack_down_id !==""){
			$avg_unit_price=array_sum($po_unit_price_arr)/count($po_unit_price_arr);
		}else{
			$avg_unit_price=$row[csf("avg_unit_price")];
			}

		
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
				<td align="left"><div style="font-size:18px; width:220px; font-weight:bold; text-align:right; padding:10px;  color:#00FF00;"> <? if( $row[csf("approved")]==1 || $row[csf("approved")]==3){echo "Approved ";} else {echo "";}
	                // if($current_approval_status==0) $ap_msg="Approved";
					// else $ap_msg="";
					// echo $ap_msg;
                 ?></div>
				</td>
			</tr>
		</table>
        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px" rules="all">
			<tr>
				<td width="140">Buyer</td>
                <td width="180" style="word-break:break-all"><? echo $buyer_arr[$buyer_name]; ?></td>
				<td rowspan="4" style="text-align: center; background-color:yellow;"><b>Style Ref. <? echo $style_ref_no; ?></b></td>
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
				<td><? echo $po_qty." Pcs";//$po_qty." ". $unit_of_measurement[$uom_id]; ?></td>
                <td rowspan="2" style="text-align: center; background-color:yellow;"><b>Job No-&nbsp;<? echo $job_no; ?></b></td>
                <td>Country</td>
                <td><?echo implode(",", $all_country_arr)?></td>
			</tr>
			<tr>
				<td>Del</td>
				<td><?= change_date_format($min_pub_ship_date)?>  to <?= change_date_format($max_pub_ship_date)?></td>
				<td>Plan Cut Qty</td>
                <td><? echo $po_plun_cut_qty." Pcs";// $po_plun_cut_qty." ". $unit_of_measurement[$uom_id]; ?></td>
			</tr>
			<tr>
				<td>PO</td>
				<td colspan="5"><div style="width: 650px;word-wrap: break-word;"><?=$poNoArr?></div></td>
			</tr>
            
        </table>
    	<?
		$pcs_value=0;
	
		$item_ratio_arr=array();$item_ratio= "";
		$gmts_item_sql=sql_select("select  count(set_item_ratio) set_item_ratio from  wo_po_details_mas_set_details  where job_no=$txt_job_no");
		 
		foreach($gmts_item_sql as $gmts_item_row)
		{
			$item_ratio_arr[$gmts_item_row[csf('gmts_item_id')]] = $gmts_item_row[csf('set_item_ratio')];
			$itemRatio=$gmts_item_row[csf('set_item_ratio')];
		}
		// echo $itemRatio;die;
		unset($gmts_item_sql);

		if($set_item_ratio==0 || $set_item_ratio=="") $set_item_ratio=1;


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
	
	$pcs_value=$order_price_per_dzn*$itemRatio;
	// echo $pcs_value;
	//start	Trims Cost part report here -------------------------------------------
	
				
	$wo_pre_cos_fab_co_avg_con_dtls_data=sql_select("SELECT id, wo_pre_cost_trim_cost_dtls_id as pre_cost_trim_cost_dtls, job_no, po_break_down_id, item_number_id, color_number_id, size_number_id, item_size, item_color_number_id, country_id, place, cons, excess_per, tot_cons, ex_cons, rate, amount, pcs, gmts_pcs, color_size_table_id from wo_pre_cost_trim_co_cons_dtls where job_no=$txt_job_no  $color_id_cond $txt_po_breack_down_id_cond4  and status_active=1 and is_deleted=0");

	foreach($wo_pre_cos_fab_co_avg_con_dtls_data as $val){
		$gmts_item=$gmts_item_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['gmts_item'];
			// $item_ratio=$item_ratio_arr[$gmts_item];
			
			// $po_order_val=$po_qty*$pcs_value;
		// $RowtotReq=($row[csf('order_quantity')]/$item_ratio)*($val[csf('cons')]/$pcs_value);
		$RowtotReq=($po_qty/$itemRatio)*($val[csf('cons')]/$pcs_value);
		$trim_pre_cost_cons_arr[$val[csf('pre_cost_trim_cost_dtls')]]['cons']=($RowtotReq/$po_qty);
	}

	 
 




	$sql_trim = "select id, job_no, trim_group,description,brand_sup_ref,remark, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active,seq
	from wo_pre_cost_trim_cost_dtls
	where job_no=".$txt_job_no." and status_active=1 and is_deleted=0 order by seq";
	$data_array_trim=sql_select($sql_trim);
	$TrimData=array();
	foreach( $data_array_trim as $row_trim )
	{
		if($txt_color_id !=="" || $txt_po_breack_down_id !==""){
			$amount=$trim_pre_cost_cons_arr[$row_trim[csf('id')]]['cons']*$pcs_value*$row_trim[csf('rate')];
		}else{
			$amount=$row_trim[csf("amount")];
		}
 
		$TrimData[$row_trim[csf('id')]]['trim_group']=$row_trim[csf('trim_group')];
		$TrimData[$row_trim[csf('id')]]['rate']=$row_trim[csf('rate')];
		$TrimData[$row_trim[csf('id')]]['amount']+=$amount;

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
		$commission_cost=$row_new[csf('commission')];
		$commission_rate=$row_new[csf('commission_percent')];
	}

	//Emb cost Cost part report here -------------------------------------------
	$wo_pre_cos_fab_co_avg_con_dtls_data=sql_select("select id, pre_cost_emb_cost_dtls_id, job_no, po_break_down_id, item_number_id, color_number_id, size_number_id, requirment, rate, amount, color_size_table_id, country_id from wo_pre_cos_emb_co_avg_con_dtls where job_no=$txt_job_no $color_id_cond $txt_po_breack_down_id_cond4  order by id");

	foreach($wo_pre_cos_fab_co_avg_con_dtls_data as $emb_con_row){

		$gmts_item=$gmts_item_arr[$emb_con_row[csf('po_break_down_id')]][$emb_con_row[csf('color_number_id')]]['gmts_item'];
		$item_ratio=$item_ratio_arr[$gmts_item];
		// $pcs_value=$order_price_per_dzn*$item_ratio;
			// $po_order_val=$po_qty*$pcs_value;
		// $RowtotReq=($row[csf('order_quantity')]/$item_ratio)*($val[csf('cons')]/$pcs_value);	 
		 
		$RowtotReq=($po_qty/$itemRatio)*($emb_con_row[csf('requirment')]/$pcs_value);
		$embl_pre_cost_cons_arr[$emb_con_row[csf('pre_cost_emb_cost_dtls_id')]]['cons']=($RowtotReq/$po_qty);

	}

	$sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pre_cost_embe_cost_dtls where job_no=".$txt_job_no." and emb_name in(1,2,4,6,99) and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	foreach($data_array as $row){
		if($row[csf("emb_name")]==1) $emb_typearr=$emblishment_print_type;
		else if($row[csf("emb_name")]==2) $emb_typearr=$emblishment_embroy_type;
		else if($row[csf("emb_name")]==4) $emb_typearr=$emblishment_spwork_type;
		else if($row[csf("emb_name")]==99) $emb_typearr=$emblishment_other_type_arr;
		if($row[csf("emb_name")]==1){
			$print_emb_type[$row[csf("emb_type")]]=$emb_typearr[$row[csf("emb_type")]].'-'.$row[csf("rate")];
		}
		else{
			$other_emb_type[$row[csf("emb_type")]]=$emb_typearr[$row[csf("emb_type")]].'-'.$row[csf("rate")];
		}
		
	}
	
	$EmbData=array();
	$EmbData['Print']['cons_dzn_gmts']=0;
	$EmbData['Embroidery / Special Works']['cons_dzn_gmts']=0;
	foreach( $data_array as $row )
	{
		// $row[csf("amount")]
		 
		if($txt_color_id !=="" || $txt_po_breack_down_id !==""){
			$amount=$embl_pre_cost_cons_arr[$row[csf('id')]]['cons']*$pcs_value*$row[csf("rate")];
		}else{
			$amount=$row[csf("amount")];
		}

		if($row[csf("emb_name")]==1){
			$emb_type=implode(",",$print_emb_type);
			$EmbData['Print']['amount']+=$amount;
			$EmbData['Print']['emb_type']=$emb_type;
		}
		else{
			$emb_type=implode(",",$other_emb_type);
			$EmbData['Embroidery / Special Works']['amount']+=$amount;
			$EmbData['Embroidery / Special Works']['emb_type']=$emb_type;

			//$EmbData['Embroidery / Special Works']['emb_type']=$row[csf("emb_type")]
		}
		
	}
	
	//End Emb cost Cost part report here 
	//Wash cost Cost part report here 
	$sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pre_cost_embe_cost_dtls where job_no=".$txt_job_no." and emb_name in(3,5) and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	foreach( $data_array as $row ){
		// $row[csf("amount")]
		if($txt_color_id !=="" || $txt_po_breack_down_id !==""){
			$amount=$embl_pre_cost_cons_arr[$row[csf('id')]]['cons']*$pcs_value*$row[csf("rate")];
		}else{
			$amount=$row[csf("amount")];
		}
 
		$washData['Wash']['amount']+=$amount;
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
		$color_wise_cons_data=sql_select("select  id, po_break_down_id, color_number_id, gmts_sizes, dia_width, item_size, cons,process_loss_percent, requirment, pcs, color_size_table_id, rate, amount, pre_cost_fabric_cost_dtls_id as pre_cost_dtls_id 
		from wo_pre_cos_fab_co_avg_con_dtls where job_no=$txt_job_no $color_id_cond $txt_po_breack_down_id_cond4 and cons>0 order by id");
		 

		foreach($color_wise_cons_data as $val){
			$gmts_item=$gmts_item_arr[$val[csf('po_break_down_id')]][$val[csf('color_number_id')]]['gmts_item'];
			$item_ratio=$item_ratio_arr[$gmts_item];
			// $pcs_value=$order_price_per_dzn*$item_ratio;
			// $po_plun_cut_val=$po_plun_cut_qty*$pcs_value;
			$fab_cons_qnty=(($val[csf('requirment')]/$pcs_value)*$po_plun_cut_qty);
			//  echo $pcs_value ."==>". $po_plun_cut_val ."==>".$fab_cons_qnty ."<br>";
			$color_wise_cons_arr[$val[csf('pre_cost_dtls_id')]]['cons']=($fab_cons_qnty/($po_plun_cut_qty));

		}

			// echo "<pre>";
			// print_r($color_wise_cons_arr);

	//  $sql = "select a.id, a.seq, a.job_no, a.body_part_id, a.fab_nature_id, a.color_type_id, a.fabric_description, a.uom, a.avg_cons, a.avg_cons_yarn, a.fabric_source, a.gsm_weight, a.rate, a.amount, a.avg_finish_cons, a.status_active, a.construction, a.composition from wo_pre_cost_fabric_cost_dtls a, wo_po_break_down b where b.job_no_mst=a.job_no and a.job_no=".$txt_job_no." $txt_po_breack_down_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 order by seq";

	 $sql = "select a.id, a.seq, a.job_no, a.body_part_id, a.fab_nature_id, a.color_type_id, a.fabric_description, a.uom, a.avg_cons, a.avg_cons_yarn, a.fabric_source, a.gsm_weight, a.rate, a.amount, a.avg_finish_cons, a.status_active, a.construction, a.composition from wo_pre_cost_fabric_cost_dtls a where  a.job_no=".$txt_job_no."  and a.status_active=1 and a.is_deleted=0  order by seq";
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
		$item_descrition = $row[csf("construction")].", ".$row[csf("composition")].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("gsm_weight")].", ".$body_part_type_arr[$row[csf('body_part_id')]].", ".$sustainability_standard.", ".$row[csf("seq")] ;

		if($txt_color_id !=="" || $txt_po_breack_down_id !==""){
			$avgCons=$color_wise_cons_arr[$row[csf('id')]]['cons']*$pcs_value;
		}else{
			$avgCons=$row[csf("avg_cons")];
		}



		if($row[csf("fab_nature_id")]==2){//knit fabrics
			if($row[csf('color_type_id')]==5){
				$rate=$row[csf("rate")]+$con_charg_rate[$row[csf('id')]];
				$amount=$avgCons*$rate;
			}
			else{
				$rate=$row[csf("rate")];
				$amount=$avgCons*$rate;
			}			
		}
		if($row[csf("fab_nature_id")]==3){//woven fabrics
			if($row[csf('color_type_id')]==5){
				$rate=$row[csf("rate")]+$con_charg_rate[$row[csf('id')]];
				$amount=$avgCons*$rate;
			}
			else{
				$rate=$row[csf("rate")];
				$amount=$avgCons*$rate;
			}
		}
		
		
	
		
		 
		// echo   $avgCons ."<br>";
		$fabric_dtls_data_arr[$item_descrition]['description']=$row[csf("construction")].", ".$row[csf("composition")].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("gsm_weight")].", ".$body_part_type_arr[$row[csf('body_part_id')]].", ".$sustainability_standard;
	    // $fabric_dtls_data_arr[$item_descrition]['avg_cons']+=$row[csf("avg_cons")];
		 $fabric_dtls_data_arr[$item_descrition]['avg_cons']+=$avgCons;
		$fabric_dtls_data_arr[$item_descrition]['rate']+=$rate;
		$fabric_dtls_data_arr[$item_descrition]['amount']+=$amount;
	}
	foreach( $fabric_dtls_data_arr as $fabdata )
	{
		$knit_fab .= '<tr>
			<td align="left">'.$i.'</td>
			<td align="left">'.$fabdata['description'].'</td>
			<td align="right">'. number_format($fabdata['avg_cons'], 8, '.', '').'</td>
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
					// $row["amount"] old
					

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
						<td align="left" width="400"><? echo $index; ?>[<? echo chop($row["emb_type"],'-'); ?>]</td>
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
				<td align="right" colspan="2" style="font-weight:bold;">&dollar;<? $grand_total+=$cm_cost; echo fn_number_format($cm_cost,4);  ?></td>
			</tr>
			<tr>
				<td align="left"><?= $sl++ ?></td>
				<td align="left">Wash / Gmts Dyeing</td>
				<td align="right" colspan="2" style="font-weight:bold;">&dollar;<? $grand_total+=$washData['Wash']['amount']; echo fn_number_format($washData['Wash']['amount'],4);  ?></td>
			</tr>
			<tr>
				<td align="left"><?= $sl++ ?></td>
				<td align="left" title="lab Test,Inspection,Freight,Courier Cost,Certificate Cost,Deffd. LC Cost,Design Cost,Studio Cost,Opert. Exp.,Interest,Income Tax,Depc. & Amort.">Inspection / Lab test / Others</td>
				<td align="right" colspan="2" style="font-weight:bold;">&dollar;<? $grand_total+=$labtest_others_total; echo fn_number_format($labtest_others_total,4) ?></td>
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
				<td align="left"><?= $sl++ ?></td>
				<td align="left">Commission / Rebate </td>
				<td align="right" style="font-weight:bold;">Commission / Rebate <?= fn_number_format($commission_rate,2) ?>&percnt;</td>
				<td align="right" colspan="2" style="font-weight:bold;">&dollar;<? $total_commission_cost+=$commission_cost; echo fn_number_format($commission_cost,2);  ?></td>
			</tr>
			<tr>
				<td align="right" colspan="3" style="font-weight:bold;">Grand Total price/dzn</td>
				<td align="right" title="Total+Commercial Charge+Commision/Rebet" style="font-weight:bold;">&dollar;<?= fn_number_format($commercial_charge+$total_commission_cost+$grand_total,2) ?></td>
			</tr>
			<tr>
				<td align="right" colspan="3" style="font-weight:bold;">Unit Price</td>
				<td align="right" title="Grand Total price/dzn/12" style="font-weight:bold;">&dollar;<?
					$factory_unit_price=($commercial_charge+$total_commission_cost+$grand_total)/12;
				 	echo fn_number_format($factory_unit_price,2) 
				 ?></td>
			</tr>
			<tr>
				<?
					$factory_profit=$avg_unit_price-$factory_unit_price
				 ?>
				<td align="right" colspan="3" style="font-weight:bold;">Factory Profit/Unit <?= fn_number_format(($factory_profit/$avg_unit_price)*100,2)  ?>&percnt;</td>
				<td align="right" title="Agreed Price-Unit Price" style="font-weight:bold;">&dollar;<?= fn_number_format($factory_profit,2) ?></td>
			</tr>
			<tr>
				<td align="right" style="color:blue; font-weight:bold;" colspan="3">Final Unit Price</td>
				<td align="right" style="color:blue; font-weight:bold;" title="Factory Profit+Unit Price">&dollar;<?= fn_number_format($factory_profit+$factory_unit_price,2) ?></td>
			</tr>
			<tr>
				<td align="right" style="color:blue ; font-weight:bold;" colspan="3">Quoted Price/Unit</td>
				<td align="right" style="color:blue ; font-weight:bold;" title="Factory Profit+Unit Price">&dollar;<?= fn_number_format($factory_profit+$factory_unit_price,2) ?></td>
			</tr>
			<tr>
				<td align="right" style="color:blue ; font-weight:bold;" colspan="3">Target Price/Unit</td>
				<td align="right"></td>
			</tr>
			<tr>
				<td align="right" style="color:blue ; font-weight:bold;" colspan="3">Agreed Price/Unit</td>
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

if($action=="preCostRpt2")
{
	$process = array( &$_POST );
	//print_r($process);
	extract(check_magic_quote_gpc( $process ));
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no="'".$txt_job_no."'";
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_style_ref="'".$txt_style_ref."'";
	if($txt_job_no=="") $job_no=''; else $job_no=" and a.job_no=".$txt_job_no."";
	$costing_date=str_replace("'","",$txt_costing_date);

	$txt_costing_date=change_date_format(str_replace("'","",$txt_costing_date),'yyyy-mm-dd','-');
	if($cbo_company_name=="") $company_name=''; else $company_name=" and a.company_name=".$cbo_company_name."";
	if($cbo_buyer_name=="") $cbo_buyer_name=''; else $cbo_buyer_name=" and a.buyer_name=".$cbo_buyer_name."";
	if($txt_style_ref=="") $txt_style_ref=''; else $txt_style_ref=" and a.style_ref_no=".$txt_style_ref."";
	if($txt_costing_date=="") $txt_costing_date=''; else $txt_costing_date=" and b.costing_date='".$txt_costing_date."'";
	$revised_no=$revised_no-1;

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
	/*
	$sql_data=sql_select("select yarn_iss_with_serv_app from variable_order_tracking where company_name=".$cbo_company_name." and variable_list=67 and status_active=1 and is_deleted=0");
	foreach($sql_data as $sql_row)
	{
		$location_cpm_cost=$sql_row[csf('yarn_iss_with_serv_app')];
	}*/
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$location_cpm_cost=0;
	$cm_min_variable=return_field_value("yarn_iss_with_serv_app as cost_per_minute","variable_order_tracking","company_name =".$cbo_company_name." and variable_list=67 and is_deleted=0 and status_active=1","cost_per_minute");
	if($cm_min_variable=="" || $cm_min_variable==0) $location_cpm_cost=0; else $location_cpm_cost=$cm_min_variable;

	//array for display name
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	//$gsm_weight_top=return_field_value("gsm_weight", "wo_pre_cost_fabric_cost_dtls", "job_no=$txt_job_no and body_part_id=1");
	//$gsm_weight_bottom=return_field_value("gsm_weight", "wo_pre_cost_fabric_cost_dtls", "job_no=$txt_job_no and body_part_id=20");
	if($db_type==0) $group_gsm="group_concat( distinct b.gsm_weight) AS gsm_weight";
	if($db_type==2) $group_gsm="listagg(b.gsm_weight ,',') within group (order by b.gsm_weight) AS gsm_weight";
	

	$gsm_weight_top=return_field_value("$group_gsm", "lib_body_part a,wo_pre_cost_fabric_cost_dtls_h b", "a.id=b.body_part_id and b.job_no=$txt_job_no and b.approved_no=$revised_no and a.body_part_type=1 and b.status_active=1 and b.is_deleted=0","gsm_weight");
	$gsm_weight_bottom=return_field_value("$group_gsm", "lib_body_part a,wo_pre_cost_fabric_cost_dtls_h b", "a.id=b.body_part_id and b.approved_no=$revised_no and b.job_no=$txt_job_no and a.body_part_type=20 and b.status_active=1 and b.is_deleted=0","gsm_weight");


	 $po_qty=0; $po_plun_cut_qty=0; $total_set_qnty=0;$total_fob_value=0;$job_in_orders=''; $pulich_ship_date=''; $job_in_file=''; $job_in_ref='';
	 $sql_po="select a.job_no,a.location_name, a.total_set_qnty, b.id, b.po_number, b.grouping, b.file_no, b.pub_shipment_date, c.order_total,c.item_number_id, c.country_id, c.color_number_id, c.size_number_id, c.order_quantity, c.plan_cut_qnty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.job_no =".$txt_job_no." $txt_po_breack_down_id_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id";
	//echo $sql_po; die;
	$sql_po_data=sql_select($sql_po);
	foreach($sql_po_data as $sql_po_row)
	{
		$po_qty+=$sql_po_row[csf('order_quantity')];
		$po_plun_cut_qty+=$sql_po_row[csf('plan_cut_qnty')];
		$total_set_qnty=$sql_po_row[csf('total_set_qnty')];
		$location_name_id=$sql_po_row[csf('location_name')];
		
		$job_in_orders .=$sql_po_row[csf('po_number')].",";
		$pulich_ship_date = $sql_po_row[csf('pub_shipment_date')];
		$job_in_file .= $sql_po_row[csf('file_no')].",";
		$job_in_ref .= $sql_po_row[csf('grouping')].",";
		$total_fob_value+=$sql_po_row[csf('order_total')];
	}
	$job_in_orders=implode(", ",array_unique(explode(",",substr(trim($job_in_orders),0,-1))));

	//echo $location_name_id.'DDD';

	$gmtsitem_ratio_array=array();
	$gmtsitem_ratio_sql=sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where job_no =".$txt_job_no."");// where job_no ='FAL-14-01157'
	foreach($gmtsitem_ratio_sql as $gmtsitem_ratio_sql_row)
	{
		$gmtsitem_ratio_array[$gmtsitem_ratio_sql_row[csf('job_no')]][$gmtsitem_ratio_sql_row[csf('gmts_item_id')]]=$gmtsitem_ratio_sql_row[csf('set_item_ratio')];
	}
	$cm_cost_based_on_date="";
	if($cm_cost_method_based_on==1){
			if($db_type==0) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-");
			if($db_type==2) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
	}
	else if($cm_cost_method_based_on==2){
		$min_shipment_sql=sql_select("select job_no_mst, min(shipment_date) as min_shipment_date from wo_po_break_down where job_no_mst=".$txt_job_no." $txt_po_breack_down_id_cond1 and status_active=1 and is_deleted=0 group by job_no_mst");
		$min_shipment_date="";
		foreach($min_shipment_sql as $row){
			$min_shipment_date=$row[csf('min_shipment_date')];
		}

		if($db_type==0) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");
		if($db_type==2) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
	}
	else if($cm_cost_method_based_on==3){
		$max_shipment_sql=sql_select("select job_no_mst, max(shipment_date) as max_shipment_date from wo_po_break_down where job_no_mst=".$txt_job_no." and status_active=1 and is_deleted=0 $txt_po_breack_down_id_cond1 group by job_no_mst");
		$max_shipment_date="";
		foreach($max_shipment_sql as $row){
			$max_shipment_date=$row[csf('max_shipment_date')];
		}

		if($db_type==0) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");
		if($db_type==2) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
	}
	else if($cm_cost_method_based_on==4){

		$max_shipment_sql=sql_select("select job_no_mst, min(pub_shipment_date) as min_pub_shipment_date from wo_po_break_down where job_no_mst=".$txt_job_no." and status_active=1 and is_deleted=0 $txt_po_breack_down_id_cond1 group by job_no_mst");
		$min_pub_shipment_date="";
		foreach($max_shipment_sql as $row){
			$min_pub_shipment_date=$row[csf('min_pub_shipment_date')];
		}

		if($db_type==0) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");
		if($db_type==2) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
	}
	else if($cm_cost_method_based_on==5){
		$max_shipment_sql=sql_select("select job_no_mst, max(pub_shipment_date) as max_pub_shipment_date from wo_po_break_down where job_no_mst=".$txt_job_no." and status_active=1 and is_deleted=0 $txt_po_breack_down_id_cond1 group by job_no_mst");
		$max_pub_shipment_date="";
		foreach($max_shipment_sql as $row){
			$max_pub_shipment_date=$row[csf('max_pub_shipment_date')];
		}

		if($db_type==0) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");
		if($db_type==2) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
	}
	$financial_para=array();
	
	if($location_cpm_cost!=1)
	{
		$sql_std_para=sql_select("select interest_expense, income_tax, cost_per_minute, applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_name and status_active=1 and is_deleted=0 order by id");
		
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				//$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para[$newdate]['interest_expense']=$row[csf('interest_expense')];
				$financial_para[$newdate]['income_tax']=$row[csf('income_tax')];
				$financial_para[$newdate]['cost_per_minute']=$row[csf('cost_per_minute')];
			}
		}
	}
	else
	{
		$sql_std_para=sql_select( "select a.id, b.id as dtls_id, b.location_id, b.applying_period_date, b.applying_period_to_date, b.monthly_cm_expense, b.no_factory_machine, b.working_hour, b.cost_per_minute from lib_standard_cm_entry a, lib_standard_cm_entry_dtls b where a.id=b.mst_id and b.location_id=$location_name_id and a.company_id=$cbo_company_name" );
		foreach($sql_std_para as $row)
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para[$newdate]['interest_expense']=$row[csf('interest_expense')];
				$financial_para[$newdate]['income_tax']=$row[csf('income_tax')];
				$financial_para[$newdate]['cost_per_minute']=$row[csf('cost_per_minute')];
			}
		}
	}
	$fab_knit_req_kg_avg=0; $fab_woven_req_yds_avg=0;
	$sql = "select a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.quotation_id, a.gmts_item_id, a.order_uom, a.job_quantity, a.avg_unit_price, b.costing_per, b.budget_minute,b.costing_date, b.sew_smv, b.cut_smv, b.sew_effi_percent, b.cut_effi_percent, b.approved, c.fab_knit_req_kg, c.fab_knit_fin_req_kg, c.fab_woven_req_yds, c.fab_woven_fin_req_yds, c.fab_yarn_req_kg from wo_po_details_master a, wo_pre_cost_mst_histry b left join wo_pre_cost_sum_dtls_histroy c on b.job_no=c.job_no and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 where a.job_no=b.job_no and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.approved_no=$revised_no and c.approved_no=$revised_no $job_no $company_name $cbo_buyer_name $txt_style_ref order by a.job_no";
	 //echo $sql;
	
	$data_array=sql_select($sql);
	$po_plan_quantity=0;
	$result_sql =sql_select("select po_number,grouping,file_no,plan_cut,pub_shipment_date from wo_po_break_down where job_no_mst=$txt_job_no and status_active=1 and is_deleted=0 $txt_po_breack_down_id_cond1 order by pub_shipment_date DESC");
		
		foreach ($result_sql as $val){
			
			$po_plan_quantity+=$val[csf('plan_cut')];
		}
		
	$uom=""; $sew_smv=0; $cut_smv=0; $sew_effi_percent=0; $cut_effi_percent=0;
	foreach ($data_array as $row)
	{
		$order_price_per_dzn=0; $order_job_qnty=0; $avg_unit_price=0;
		$sew_smv=$row[csf("sew_smv")];
	    $cut_smv=$row[csf("cut_smv")]; 
		$costing_date=$row[csf("costing_date")];
	    $sew_effi_percent=$row[csf("sew_effi_percent")];
	    $cut_effi_percent=$row[csf("cut_effi_percent")];
		$order_values = $row[csf("job_quantity")]*$row[csf("avg_unit_price")];
		
		//$job_in_orders = substr(trim($job_in_orders),0,-1);
		$job_ref=array_unique(explode(",",rtrim($job_in_ref,", ")));
		$job_file=array_unique(explode(",",rtrim($job_in_file,", ")));

		foreach ($job_ref as $ref){
			$ref_cond.=", ".$ref;
		}
		$file_con='';
		foreach ($job_file as $file){
			if($file_con=='') $file_cond=$file; else $file_cond.=", ".$file;
		}
		?>
        <table style="width:850px" ><tr><td colspan="6" align="center">
           <?
            $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id=$cbo_company_name","image_location");
            ?>
            <img  src='../../<? echo $image_location; ?>' height='70' align="left" />
            <b style="font-size:25px;"><? echo $comp[str_replace("'","",$cbo_company_name)]; ?></b><br>
            <b style="font-size:14px;">Pre- Costing</b>
        </td></tr></table>
        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px" rules="all">
            <tr>
                <td width="80">Job Number</td>
                <td width="80"><b><? echo $row[csf("job_no")]; ?></b></td>
                <td width="90">Buyer</td>
                <td width="100"><b><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></b></td>
                <td width="80">Garments Item</td>
                <?
                    $grmnt_items = "";
                    if($garments_item[$row[csf("gmts_item_id")]]=="")
                    {
                        $grmts_sql = sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where job_no=$txt_job_no");
                        foreach($grmts_sql as $key=>$val){
                            $grmnt_items .=$garments_item[$val[csf("gmts_item_id")]].", ";
                        }
                        $grmnt_items = substr_replace($grmnt_items,"",-1,1);
                    }else{
                        $grmnt_items = $garments_item[$row[csf("gmts_item_id")]];
                    }
                ?>
                <td width="100"><b><? echo $grmnt_items; ?></b></td>
            </tr>
            <tr>
                <td>Style Ref. No </td>
                <td colspan=""><b><? echo $row[csf("style_ref_no")]; ?></b></td>
                <td>Job Qty</td>
                <td><b><? $uom=$row[csf("order_uom")]; echo $row[csf("job_quantity")]." ". $unit_of_measurement[$row[csf("order_uom")]]; ?></b></td>
                 <td>Plan Cut Qty</td>
               <td><b><? echo $po_plan_quantity." ". $unit_of_measurement[$row[csf("order_uom")]];?></b></td>
            </tr>
            <tr>
                <td>Order Numbers</td>
                <td colspan="5"><p><? echo $job_in_orders; ?></p></td>
            </tr>
            <tr>
                <td>Knit Fabric Cons</td>
                <td><b><? echo $row[csf("fab_knit_req_kg")];$fab_knit_req_kg_avg+=$row[csf("fab_knit_req_kg")]; ?> (Kg)</b></td>
                <td>Woven Fabric Cons</td>
                <td><b><? echo $row[csf("fab_woven_req_yds")];$fab_woven_req_yds_avg+= $row[csf("fab_woven_req_yds")];?> (Yds)</b></td>
                <td>Price Per Unit</td>
                <td><b><? echo $row[csf("avg_unit_price")]; ?> USD</b></td>
            </tr>
            <tr>
                <td>Avg Yarn Req</td>
                <td><b><? echo $row[csf("fab_yarn_req_kg")] ?> (Kg)</b></td>
                <td>Costing Per</td>
                <td><b><? echo $costing_per[$row[csf("costing_per")]];?></b></td>
                <td>Shipment Date </td>
                <td><b><? echo change_date_format($pulich_ship_date); ?></b></td>
            </tr>
            <tr>
            <td>Knit Fin Fabric Cons</td>
                <td><b><? echo $row[csf("fab_knit_fin_req_kg")] ?> (Kg)</b></td>
                <td>Woven Fin Fabric Cons</td>
                <td><b><? echo $row[csf("fab_woven_fin_req_yds")]; ?>(Yds)</b></td>
                <td>GSM</td>
                <td><b><? 
                $gsm_weights_top=implode(",",array_unique(explode(",",$gsm_weight_top)));
                $gsm_weight_bottom=implode(",",array_unique(explode(",",$gsm_weight_bottom)));
                if($gsm_weights_top!='') $gsm_weightTop=$gsm_weights_top;else $gsm_weightTop='';
                if($gsm_weight_bottom!='' && $gsm_weights_top!='') $gsm_weightBottom=" ,".$gsm_weight_bottom;
                else if($gsm_weight_bottom!='' && $gsm_weights_top=='') $gsm_weightBottom=$gsm_weight_bottom;
                else $gsm_weightBottom='';
                echo $gsm_weightTop .$gsm_weightBottom;
                ?></b></td>
            </tr>
            <tr>
                <td>Budget Minuite</td>
                <td><b><? echo $row[csf("budget_minute")] ?> </b></td>
                <td align="center" height="10" colspan="2" valign="top" id="app_sms" style="font-size:18px;"><font color="#FF0000"><? if( $row[csf("approved")]==1 || $row[csf("approved")]==3){echo "This Job is Approved ";} else {echo "";} ?> </font> </td>
                <td>Quotation ID</td>
                <td><b><? echo $row[csf("quotation_id")]; $quotation_id= $row[csf("quotation_id")]; ?> </b></td>
            </tr>
            <tr>
             	<td>Internal Ref</td>
                <td colspan="2"><b><? echo ltrim($ref_cond,", "); ?></b></td>
                <td>File No</td>
                <td colspan="2"><b><? echo $file_cond; ?></b></td>
            </tr>
        </table>
    	<?
		if($row[csf("costing_per")]==1){
			$order_price_per_dzn=12;
			$costing_for=" DZN";
		}
		else if($row[csf("costing_per")]==2){
			$order_price_per_dzn=1;
			$costing_for=" PCS";
		}
		else if($row[csf("costing_per")]==3){
			$order_price_per_dzn=24;
			$costing_for=" 2 DZN";
		}
		else if($row[csf("costing_per")]==4){
			$order_price_per_dzn=36;
			$costing_for=" 3 DZN";
		}
		else if($row[csf("costing_per")]==5){
			$order_price_per_dzn=48;
			$costing_for=" 4 DZN";
		}
		$order_job_qnty=$row[csf("job_quantity")];
		$avg_unit_price=$row[csf("avg_unit_price")];
	}//end first foearch
 	
	//start	all summary report here -------------------------------------------
	$condition= new condition();
	if(str_replace("'","",$txt_job_no) !=''){
		$condition->job_no("=$txt_job_no");
	}
	if(str_replace("'",'',$txt_po_breack_down_id) !=""){
		$condition->po_id("in($selected_po_breack_down_id)");
	}
	$condition->init();
	$fabric= new fabric($condition);
	$yarn= new yarn($condition);
	$conversion= new conversion($condition);
	$trim= new trims($condition);
	$emblishment= new emblishment($condition);
	$wash= new wash($condition);
	$other= new other($condition);
	$other_cost=$other->getAmountArray_by_job();
	//$trims_cost_arr=$trim->getQtyArray_by_orderAndItemidTypeid();
	//print_r($trims_cost_arr);
	$commercial= new commercial($condition);
	$commision= new commision($condition);

	$sql_new = "select job_no, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, commission, commission_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, depr_amor_pre_cost, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche, design_cost, design_percent, studio_cost, studio_percent from wo_pre_cost_dtls_histry where job_no=".$txt_job_no." and status_active=1 and is_deleted=0 and approved_no=$revised_no";
	//echo $sql_new;
	$data_array_new=sql_select($sql_new);
	$summary_data=array();

	foreach( $data_array_new as $row_new )
	{
		$summary_data['price_dzn']=$row_new[csf("price_dzn")];
		$summary_data['price_dzn_job']=$total_fob_value;//($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("price_dzn")];
		$summary_data['commission']=$row_new[csf("commission")];
		$summary_data['trims_cost']=$row_new[csf("trims_cost")];
		$summary_data['emb_cost']=$row_new[csf("embel_cost")];
		$summary_data['lab_test']=$row_new[csf("lab_test")];
		$summary_data['lab_test_job']=$other_cost[$row_new[csf("job_no")]]['lab_test'];
		$summary_data['inspection']=$row_new[csf("inspection")];
		$summary_data['inspection_job']=$other_cost[$row_new[csf("job_no")]]['inspection'];
		$summary_data['freight']=$row_new[csf("freight")];
		$summary_data['freight_job']=$other_cost[$row_new[csf("job_no")]]['freight'];
		$summary_data['design']=$row_new[csf("design_cost")];
		$summary_data['design_job']=$row_new[csf("design_cost")]/$order_price_per_dzn*$po_qty;
		$summary_data['studio']=$row_new[csf("studio_cost")];
		$summary_data['studio_job']=$row_new[csf("studio_cost")]/$order_price_per_dzn*$po_qty;
		$summary_data['currier_pre_cost']=$row_new[csf("currier_pre_cost")];
		$summary_data['currier_pre_cost_job']=$other_cost[$row_new[csf("job_no")]]['currier_pre_cost'];
		$summary_data['certificate_pre_cost']=$row_new[csf("certificate_pre_cost")];
		$summary_data['certificate_pre_cost_job']=$other_cost[$row_new[csf("job_no")]]['certificate_pre_cost'];
		$summary_data['wash_cost']=$row_new[csf("wash_cost")];
		$summary_data['OtherDirectExpenses']=$row_new[csf("lab_test")]+$row_new[csf("inspection")]+$row_new[csf("design_cost")]+$row_new[csf("studio_cost")]+$row_new[csf("freight")]+$row_new[csf("currier_pre_cost")]+$row_new[csf("certificate_pre_cost")]+$row_new[csf("wash_cost")];
		$summary_data['OtherDirectExpenses_job']=$summary_data['lab_test_job']+$summary_data['inspection_job']+$summary_data['design_job']+$summary_data['studio_job']+$summary_data['freight_job']+$summary_data['currier_pre_cost_job']+$summary_data['certificate_pre_cost_job'];
		$summary_data['cm_cost']=$row_new[csf("cm_cost")];
		$summary_data['cm_cost_job']=$other_cost[$row_new[csf("job_no")]]['cm_cost'];
		$summary_data['comm_cost']=$row_new[csf("comm_cost")];
		$summary_data['common_oh']=$row_new[csf("common_oh")];
		$summary_data['common_oh_job']=$other_cost[$row_new[csf("job_no")]]['common_oh'];
		$summary_data['depr_amor_pre_cost']=$row_new[csf("depr_amor_pre_cost")];
		$summary_data['depr_amor_pre_cost_job']=$other_cost[$row_new[csf("job_no")]]['depr_amor_pre_cost'];
	}

	//Fabric =====================
	$fabric_qty=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
	$fabric_amount=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
	$sql_fabric = "select id, job_no, body_part_id, fab_nature_id, color_type_id, fabric_description,uom, avg_cons,avg_cons_yarn, fabric_source,gsm_weight, rate, amount, avg_finish_cons, status_active from wo_pre_cost_fabric_cost_dtls_h where job_no=".$txt_job_no." and status_active=1 and is_deleted=0 and approved_no=$revised_no";
	//echo $sql_fabric;
	$data_arr_fabric=sql_select($sql_fabric);
	foreach($data_arr_fabric as $fab_row)
	{
		$summary_data['fabric_cost'][$fab_row[csf("id")]]=$fab_row[csf("amount")];
		$summary_data['fabric_cost_job']+=$fabric_amount['knit']['grey'][$fab_row[csf("id")]][$fab_row[csf("uom")]];
		$summary_data['fabric_cost_job']+=$fabric_amount['woven']['grey'][$fab_row[csf("id")]][$fab_row[csf("uom")]];
	}
	//Fabric End======================
	//Yarn===========================
	$totYarn=0;
	$YarnData=array();
	$yarn_data_array=$yarn->get_By_Precostdtlsid_YarnQtyAmountArray();
	$sql_yarn="select f.id as yarn_id, f.cons_ratio, f.cons_qnty, f.avg_cons_qnty, f.rate, f.amount, count_id, copm_one_id, percent_one, color, type_id from wo_pre_cost_fab_yarn_cost_dtls f where f.job_no =".$txt_job_no." and f.is_deleted=0 and f.status_active=1  order by f.id";
	$data_arr_yarn=sql_select($sql_yarn);
	foreach($data_arr_yarn as $yarn_row)
	{
		$yarnrate=$yarn_row[csf("rate")];
		$summary_data[yarn_cost][$yarn_row[csf("yarn_id")]]=$yarn_row[csf("amount")];
		$summary_data[yarn_cost_job]+=$yarn_data_array[$yarn_row[csf("yarn_id")]]['amount'];
		$index="'".$yarn_row[csf("count_id")]."_".$yarn_row[csf("copm_one_id")]."_".$yarn_row[csf("percent_one")]."_".$yarn_row[csf("type_id")]."_".$yarn_row[csf("color")]."_".$yarnrate."'";
		$YarnData[$index]['qty']+=$yarn_data_array[$yarn_row[csf("yarn_id")]]['qty'];
		$YarnData[$index]['amount']+=$yarn_data_array[$yarn_row[csf("yarn_id")]]['amount'];
		$YarnData[$index]['dznqty']+=$yarn_row[csf("cons_qnty")];
		$YarnData[$index]['dznamount']+=$yarn_row[csf("amount")];
		$totYarn+=$yarn_data_array[$yarn_row[csf("yarn_id")]]['qty'];
	}
	//Yarn End============================
	//Conversion
	$totConv=0;
	$ConvData=array();
	$conv_data=array();
	$conv_amount_arr=$conversion->getAmountArray_by_conversionid();
	$conv_qty_arr=$conversion->getQtyArray_by_conversionid();
	$sql_conv = "select a.id as con_id, a.fabric_description as fabric_description_id, a.job_no, a.cons_process, a.req_qnty,a.avg_req_qnty, a.charge_unit, a.amount, a.status_active,b.body_part_id,b.fab_nature_id,b.color_type_id,b.fabric_description,b.uom  from wo_pre_cost_fab_conv_cost_dtls a left join wo_pre_cost_fabric_cost_dtls_h b on a.job_no=b.job_no  and b.status_active=1 and b.is_deleted=0 where a.job_no=".$txt_job_no." and a.is_deleted =0 and a.status_active=1 and b.approved_no=$revised_no";
	//echo $sql_conv;

	$data_arr_conv=sql_select($sql_conv);
	foreach($data_arr_conv as $conv_row)
	{
		$convamount=$conv_amount_arr[$conv_row[csf('con_id')]][$conv_row[csf('uom')]];
		$convQty=$conv_qty_arr[$conv_row[csf('con_id')]][$conv_row[csf('uom')]];
		$conv_data[cons_process][$conv_row[csf('con_id')]]=$conv_row[csf('cons_process')];
		$conv_data[amount][$conv_row[csf('con_id')]]=$conv_row[csf('amount')];
		$conv_data[amount_job][$conv_row[csf('con_id')]]+=$convamount;
		$summary_data[conver_cost_job]+=$convamount;
	
		$index=$conv_row[csf('con_id')];
		$ConvData[$index]['item_descrition']=$body_part[$conv_row[csf("body_part_id")]].", ".$color_type[$conv_row[csf("color_type_id")]].", ".$conv_row[csf("fabric_description")];
		$ConvData[$index]['cons_process']=$conv_row[csf("cons_process")];
		$ConvData[$index]['req_qnty']=$conv_row[csf("req_qnty")];
		$ConvData[$index]['uom']=$conv_row[csf("uom")];
		$ConvData[$index]['charge_unit']=$conv_row[csf("charge_unit")];
		$ConvData[$index]['amount']=$conv_row[csf("amount")];
		$ConvData[$index]['tot_req_qnty']=$convQty;
		$ConvData[$index]['tot_amount']=$convamount;
		$totConv+=$conv_row[csf("req_qnty")];
	}
	//Conversion End
	//start	Trims Cost part report here -------------------------------------------
	$sql_trim = "select id, job_no, trim_group,description,brand_sup_ref,remark, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active,seq
	from wo_pre_cost_trim_cost_dtls_his
	where job_no=".$txt_job_no." and status_active=1 and is_deleted=0 and approved_no=$revised_no order by seq";
	//echo $sql_trim;
	$data_array_trim=sql_select($sql_trim);
	$trim_amount_arr=$trim->getAmountArray_precostdtlsid();
	$trim_qty_arr=$trim->getQtyArray_by_precostdtlsid();
	//$trim_type_qty_arr=$trim->getQtyArray_by_orderAndTrimsTypeid();
	//print_r($trim_type_qty_arr);
	$totTrim=0;
	$TrimData=array();
	foreach( $data_array_trim as $row_trim )
	{
		$trim_qty=$trim_qty_arr[$row_trim[csf("id")]];
		$trim_amount=$trim_amount_arr[$row_trim[csf("id")]];
		$summary_data[trims_cost_job]+=$trim_amount;
		$TrimData[$row_trim[csf('id')]]['trim_group']=$row_trim[csf('trim_group')];
		$TrimData[$row_trim[csf('id')]]['description']=$row_trim[csf('description')];
		$TrimData[$row_trim[csf('id')]]['brand_sup_ref']=$row_trim[csf('brand_sup_ref')];
		$TrimData[$row_trim[csf('id')]]['remark']=$row_trim[csf('remark')];
		$TrimData[$row_trim[csf('id')]]['cons_uom']=$row_trim[csf('cons_uom')];
		$TrimData[$row_trim[csf('id')]]['cons_dzn_gmts']=$row_trim[csf('cons_dzn_gmts')];
		$TrimData[$row_trim[csf('id')]]['rate']=$row_trim[csf('rate')];
		$TrimData[$row_trim[csf('id')]]['amount']=$row_trim[csf('amount')];
		$TrimData[$row_trim[csf('id')]]['apvl_req']=$row_trim[csf('apvl_req')];
		$TrimData[$row_trim[csf('id')]]['nominated_supp']=$row_trim[csf('nominated_supp')];
		$TrimData[$row_trim[csf('id')]]['tot_cons']=$trim_qty;
		$TrimData[$row_trim[csf('id')]]['tot_amount']=$trim_amount;
		$totTrim+=$row_trim[csf('cons_dzn_gmts')];
	}
	//End	Trims Cost part report here -------------------------------------------
	//Emb cost Cost part report here -------------------------------------------

	$sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pre_cost_embe_cost_dtls_his where job_no=".$txt_job_no." and emb_name in(1,2,4,5) and status_active=1 and is_deleted=0 and approved_no=$revised_no";
	$data_array=sql_select($sql);
	$emblishment_qty=$emblishment->getQtyArray_by_jobAndEmblishmentid();
	$emblishment_amount=$emblishment->getAmountArray_by_jobAndEmblishmentid();
	$totEmb=0;
	$EmbData=array();
	
	foreach( $data_array as $row )
	{
		$embqty=$emblishment_qty[$row[csf("job_no")]][$row[csf("id")]];
		$embamount=$emblishment_amount[$row[csf("job_no")]][$row[csf("id")]];
		$summary_data[emb_cost_job]+=$embamount;
		$EmbData[$row[csf("id")]]['emb_name']=$row[csf("emb_name")];
		$EmbData[$row[csf("id")]]['emb_type']=$row[csf("emb_type")];
		$EmbData[$row[csf("id")]]['cons_dzn_gmts']=$row[csf("cons_dzn_gmts")];
		$EmbData[$row[csf("id")]]['rate']=$row[csf("rate")];
		$EmbData[$row[csf("id")]]['amount']=$row[csf("amount")];
		$EmbData[$row[csf("id")]]['tot_cons']=$embqty;
		$EmbData[$row[csf("id")]]['tot_amount']=$embamount;
		$totEmb+=$row[csf("cons_dzn_gmts")];
	}
	//End Emb cost Cost part report here -------------------------------------------
	//Wash cost Cost part report here -------------------------------------------
	$sql = "select id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount,status_active from wo_pre_cost_embe_cost_dtls_his where job_no=".$txt_job_no." and emb_name =3 and status_active=1 and is_deleted=0 and approved_no=$revised_no";
	$data_array=sql_select($sql);
	$wash_qty=$wash->getQtyArray_by_jobAndEmblishmentid();
	$wash_amount=$wash->getAmountArray_by_jobAndEmblishmentid();
	foreach( $data_array as $row ){
		$washqty=$wash_qty[$row[csf("job_no")]][$row[csf("id")]];
		$washamount=$wash_amount[$row[csf("job_no")]][$row[csf("id")]];
		$summary_data[wash_cost_job]+=$washamount;
		$summary_data[OtherDirectExpenses_job]+=$washamount;
		$EmbData[$row[csf("id")]]['emb_name']=$row[csf("emb_name")];
		$EmbData[$row[csf("id")]]['emb_type']=$row[csf("emb_type")];
		$EmbData[$row[csf("id")]]['cons_dzn_gmts']=$row[csf("cons_dzn_gmts")];
		$EmbData[$row[csf("id")]]['rate']=$row[csf("rate")];
		$EmbData[$row[csf("id")]]['amount']=$row[csf("amount")];
		$EmbData[$row[csf("id")]]['tot_cons']=$washqty;
		$EmbData[$row[csf("id")]]['tot_amount']=$washamount;
		$totEmb+=$row[csf("cons_dzn_gmts")];
	}
	//End Wash cost Cost part report here -------------------------------------------
	//Commision cost Cost part report here -------------------------------------------
	$sql = "select id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount, status_active from wo_pre_cost_commiss_cost_dtls where job_no=".$txt_job_no." and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	$commision_amount=$commision->getAmountArray_by_jobAndPrecostdtlsid();
	$totCommi=0;
	$CommiData=array();
	foreach( $data_array as $row ){
		$commisionamount=$commision_amount[$row[csf("job_no")]][$row[csf("id")]];
		$summary_data[commission_job]+=$commisionamount;
		$CommiData[$row[csf("id")]]['particulars_id']=$row[csf("particulars_id")];
		$CommiData[$row[csf("id")]]['commission_base_id']=$row[csf("commission_base_id")];
		$CommiData[$row[csf("id")]]['commision_rate']=$row[csf("commision_rate")];
		$CommiData[$row[csf("id")]]['commission_amount']=$row[csf("commission_amount")];
		$CommiData[$row[csf("id")]]['tot_commission_amount']=$commisionamount;
		$totCommi+=$row[csf("commission_amount")];
	}
	//End Commision cost Cost part report here -------------------------------------------
	//Commarcial cost Cost part report here -------------------------------------------
	$sql = "select id, job_no, item_id, rate, amount, status_active from  wo_pre_cost_comarci_cost_dtls  where job_no=".$txt_job_no." and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	$commarcial_amount=$commercial->getAmountArray_by_jobAndPrecostdtlsid();
	
	$totCommar=0;
	$CommarData=array();
	foreach( $data_array as $row ){
		$commarcialamount=$commarcial_amount[$row[csf("job_no")]][$row[csf("id")]];
		$summary_data[comm_cost_job]+=$commarcialamount;
		$CommarData[$row[csf("id")]]['item_id']=$row[csf("item_id")];
		$CommarData[$row[csf("id")]]['rate']=$row[csf("rate")];
		$CommarData[$row[csf("id")]]['amount']=$row[csf("amount")];
		$CommarData[$row[csf("id")]]['tot_amount']=$commarcialamount;
		$totCommar+=$row[csf("amount")];
	}
	//End Commarcial cost Cost part report here -------------------------------------------
	?>
    <div style="margin-top:15px">
        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;" rules="all">
            <tr style="font-weight:bold">
            	<td align="center" width="850" colspan="5">Order Profitability </td>
            </tr>
            <tr style="font-weight:bold">
                <td align="center" width="80">Line Items</td>
                <td width="400">Particulars</td>
                <td width="120">Amount (USD)/<? echo $costing_for; ?></td>
                <td width="120">Total Value</td>
                <td>%</td>
            </tr>
            <tr>
                <td align="center">1</td>
                <td style="font-weight:bold">Gross FOB Value</td>
                <td align="right" style="font-weight:bold"><? echo fn_number_format($summary_data[price_dzn],4); ?></td>
                <td align="right" style="font-weight:bold"><? echo fn_number_format($summary_data[price_dzn_job],4); ?></td>
                <td align="right" style="font-weight:bold"><? echo fn_number_format(($summary_data[price_dzn_job]/$summary_data[price_dzn_job])*100,4);?></td>
            </tr>
            <tr>
                <td align="center">2</td>
                <td style=" padding-left:15px">Less: commission</td>
                <td align="right"><? echo fn_number_format($summary_data[commission],4); ?></td>
                <td align="right"><? echo fn_number_format($summary_data[commission_job],4); ?></td>
                <td align="right"><? echo fn_number_format(($summary_data[commission_job]/$summary_data[price_dzn_job])*100,4);?></td>
            </tr>
            <tr>
                <td align="center">3</td>
                <?
                $NetFOBValue=$summary_data[price_dzn]-$summary_data[commission];
                $NetFOBValue_job=$summary_data[price_dzn_job]-$summary_data[commission_job];
                ?>
                <td style="font-weight:bold"><b>Net FOB Value (1-2)</b></td>
                <td align="right" style="font-weight:bold"><? echo fn_number_format($NetFOBValue,4); ?></td>
                <td align="right" style="font-weight:bold"><? echo fn_number_format($NetFOBValue_job,4); ?></td>
                <td align="right" style="font-weight:bold"><? echo fn_number_format(($NetFOBValue_job/$summary_data[price_dzn_job])*100,4);?></td>
            </tr>
            <tr>
                <td align="center">4</td>
                <td style="font-weight:bold"><b>Less: Cost of Material & Services (5+6+7+8+9) </b></td>
                <?
                $Less_Cost_Material_Services=array_sum($summary_data[yarn_cost])+array_sum($summary_data[fabric_cost])+array_sum($conv_data[amount])+$summary_data[trims_cost]+$summary_data[emb_cost]+$summary_data[lab_test]+$summary_data[inspection]+$summary_data[design]+$summary_data[studio]+$summary_data[freight]+$summary_data[currier_pre_cost]+$summary_data[certificate_pre_cost]+$summary_data[wash_cost];
                
                $Less_Cost_Material_Services_job=$summary_data[yarn_cost_job]+$summary_data[fabric_cost_job]+$summary_data[conver_cost_job]+$summary_data[trims_cost_job]+$summary_data[emb_cost_job]+$summary_data[OtherDirectExpenses_job];
                ?>
                <td align="right" style="font-weight:bold"> <? echo fn_number_format($Less_Cost_Material_Services,4); ?></td>
                <td align="right" style="font-weight:bold"><? echo fn_number_format($Less_Cost_Material_Services_job,4); ?></td>
                <td align="right" style="font-weight:bold"><? echo fn_number_format(($Less_Cost_Material_Services_job/$summary_data[price_dzn_job])*100,4);?></td>
            </tr>
            <tr>
                <td align="center" rowspan="2">5</td>
                <td style=" padding-left:100px;font-weight:bold">Fabric Purchase Cost</td>
                <td align="right" style="font-weight:bold"> <? echo fn_number_format(array_sum($summary_data[fabric_cost]),4); ?></td>
                <td align="right" style="font-weight:bold"> <? echo fn_number_format($summary_data[fabric_cost_job],4); ?></td>
                <td align="right" style="font-weight:bold"><? echo fn_number_format(($summary_data[fabric_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>
            </tr>
            <tr>
                <td style=" padding-left:100px;font-weight:bold">Yarn Cost</td>
                <td align="right" style="font-weight:bold"> <? echo fn_number_format(array_sum($summary_data[yarn_cost]),4); ?></td>
                <td align="right" style="font-weight:bold"> <? echo fn_number_format($summary_data[yarn_cost_job],4); ?></td>
                <td align="right" style="font-weight:bold"><? echo fn_number_format(($summary_data[yarn_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>
            </tr>
            <tr>
                <td align="center" valign="top">6</td>
                <td width="400" style=" padding-left:100px">
                    <table>
                        <tr>
                        	<td style="font-weight:bold">Conversion Cost</td>
                        </tr>
                    </table>
                    <table border="1" class="rpt_table" rules="all">
						<? foreach($conv_data[cons_process] as $key => $value){ ?>
                            <tr>
                                <td align="left"><? echo $conversion_cost_head_array[$conv_data[cons_process][$key]]; ?></td>
                            </tr>
                        <? }?>
                    </table>
                </td>
                <td align="right" valign="top">
                    <table>
                        <tr>
                        	<td align="right" style="font-weight:bold"><? echo fn_number_format(array_sum($conv_data[amount]),4); ?></td>
                        </tr>
                    </table>
                    <table border="1" class="rpt_table" rules="all">
						<? foreach($conv_data[cons_process] as $key => $value){ ?>
                            <tr>
                                <td align="right"><? echo fn_number_format($conv_data[amount][$key],4);?></td>
                            </tr>
                        <? } ?>
                    </table>
                </td>
                <td align="right" valign="top">
                    <table>
                        <tr>
                        	<td align="right" style="font-weight:bold"><? echo fn_number_format($summary_data[conver_cost_job],4); ?></td>
                        </tr>
                    </table>
                    <table border="1" class="rpt_table" rules="all">
                        <? foreach($conv_data[cons_process] as $key => $value){ ?>
                            <tr>
                                <td align="right"><? echo fn_number_format($conv_data[amount_job][$key],4);?></td>
                            </tr>
                        <? }?>
                    </table>
                </td>
                <td align="right" valign="top">
                    <table>
                        <tr>
                        	<td align="right" style="font-weight:bold"><? echo fn_number_format(($summary_data[conver_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>
                        </tr>
                    </table>
                    <table border="1" class="rpt_table" rules="all">
						<? foreach($conv_data[cons_process] as $key => $value){ ?>
                            <tr>
                            	<td align="right"><? echo fn_number_format(($conv_data[amount_job][$key]/$summary_data[price_dzn_job])*100,4);?></td>
                            </tr>
                        <? }?>
                    </table>
                </td>
            </tr>
            <tr>
                <td align="center">7</td>
                <td style="padding-left:100px;font-weight:bold" ><b>Trim Cost </b></td>
                <td align="right" style="font-weight:bold"> <? echo fn_number_format($summary_data[trims_cost],4); ?></td>
                <td align="right" style="font-weight:bold"><? echo fn_number_format($summary_data[trims_cost_job],4)?></td>
                <td align="right" style="font-weight:bold"><? echo fn_number_format(($summary_data[trims_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>
            </tr>
            <tr>
                <td align="center">8</td>
                <td style=" padding-left:100px;font-weight:bold"><b>Embelishment Cost </b></td>
                <td align="right" style="font-weight:bold"> <? echo fn_number_format($summary_data[emb_cost],4); ?></td>
                <td align="right" style="font-weight:bold"><? echo fn_number_format($summary_data[emb_cost_job],4); ?></td>
                <td align="right" style="font-weight:bold"><? echo fn_number_format(($summary_data[emb_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>
            </tr>
            <tr>
                <td align="center" valign="top">9</td>
                <td style="padding-left:100px">
                    <table>
                        <tr>
                        	<td style="font-weight:bold">Other Direct Expenses</td>
                        </tr>
                    </table>
                    <table border="1" class="rpt_table" rules="all">
                        <tr>
                        	<td>Lab Test</td>
                        </tr>
                        <tr>
                        	<td>Inspection</td>
                        </tr>
                        <tr>
                        	<td>Design Cost</td>
                        </tr>
                        <tr>
                        	<td>Studio Cost</td>
                        </tr>
                        <tr>
                        	<td>Freight Cost</td>
                        </tr>
                        <tr>
                        	<td>Courier Cost</td>
                        </tr>
                        <tr>
                        	<td>Certificate Cost</td>
                        </tr>
                        <tr>
                        	<td>Garments Wash Cost</td>
                        </tr>
                    </table>
                </td>
                <td align="right" valign="top">
                    <table>
                        <tr>
                        	<td align="right" style="font-weight:bold"><? echo fn_number_format($summary_data[OtherDirectExpenses],4); ?></td>
                        </tr>
                    </table>
                    <table border="1" class="rpt_table" rules="all">
                        <tr>
                        	<td align="right"><? echo fn_number_format($summary_data[lab_test],4);?></td>
                        </tr>
                        <tr>
                        	<td align="right"><? echo fn_number_format($summary_data[inspection],4);?></td>
                        </tr>
                        <tr>
                        	<td align="right"><? echo fn_number_format($summary_data[design],4);?></td>
                        </tr>
                        <tr>
                        	<td align="right"><? echo fn_number_format($summary_data[studio],4);?></td>
                        </tr>
                        <tr>
                        	<td align="right"><? echo fn_number_format($summary_data[freight],4);?></td>
                        </tr>
                        <tr>
                        	<td align="right"><? echo fn_number_format($summary_data[currier_pre_cost],4);?></td>
                        </tr>
                        <tr>
                        	<td align="right"><? echo fn_number_format($summary_data[certificate_pre_cost],4);?></td>
                        </tr>
                        <tr>
                        	<td align="right"><? echo fn_number_format($summary_data[wash_cost],4);?></td>
                        </tr>
                    </table>
                </td>
                <td align="right" valign="top">
                    <table>
                        <tr>
                        <td align="right" style="font-weight:bold"><? echo fn_number_format($summary_data[OtherDirectExpenses_job],4); ?></td>
                        </tr>
                    </table>
                    <table border="1" class="rpt_table" rules="all">
                        <tr>
                        	<td align="right"><? echo fn_number_format($summary_data[lab_test_job],4);?></td>
                        </tr>
                        <tr>
                        	<td align="right"><? echo fn_number_format($summary_data[inspection_job],4);?></td>
                        </tr>
                        <tr>
                        	<td align="right"><? echo fn_number_format($summary_data[design_job],4);?></td>
                        </tr>
                        <tr>
                        	<td align="right"><? echo fn_number_format($summary_data[studio_job],4);?></td>
                        </tr>
                        <tr>
                        	<td align="right"><? echo fn_number_format($summary_data[freight_job],4);?></td>
                        </tr>
                        <tr>
                       		<td align="right"><? echo fn_number_format($summary_data[currier_pre_cost_job],4);?></td>
                        </tr>
                        <tr>
                        	<td align="right"><? echo fn_number_format($summary_data[certificate_pre_cost_job],4);?></td>
                        </tr>
                        <tr>
                        	<td align="right"><? echo fn_number_format($summary_data[wash_cost_job],4);?></td>
                        </tr>
                    </table>
                </td>
                <td align="right" valign="top">
                    <table>
                        <tr>
                        	<td align="right" style="font-weight:bold"><? echo fn_number_format(($summary_data[OtherDirectExpenses_job]/$summary_data[price_dzn_job])*100,4);?></td>
                        </tr>
                    </table>
                    <table border="1" class="rpt_table" rules="all">
                        <tr>
                        	<td align="right"><? echo fn_number_format(($summary_data[lab_test_job]/$summary_data[price_dzn_job])*100,4);?></td>
                        </tr>
                        <tr>
                        	<td align="right"><? echo fn_number_format(($summary_data[inspection_job]/$summary_data[price_dzn_job])*100,4);?></td>
                        </tr>
                        <tr>
                        	<td align="right"><? echo fn_number_format(($summary_data[design_job]/$summary_data[price_dzn_job])*100,4);?></td>
                        </tr>
                        <tr>
                       		<td align="right"><? echo fn_number_format(($summary_data[studio_job]/$summary_data[price_dzn_job])*100,4);?></td>
                        </tr>
                        <tr>
                        	<td align="right"><? echo fn_number_format(($summary_data[freight_job]/$summary_data[price_dzn_job])*100,4);?></td>
                        </tr>
                        <tr>
                        	<td align="right"><? echo fn_number_format(($summary_data[currier_pre_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>
                        </tr>
                        <tr>
                        	<td align="right"><? echo fn_number_format(($summary_data[certificate_pre_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>
                        </tr>
                        <tr>
                        	<td align="right"><? echo fn_number_format(($summary_data[wash_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td align="center">10</td>
                <td style="font-weight:bold">Contributions/Value Additions (3-4)</td>
                <?
                $Contribution_Margin=$NetFOBValue-$Less_Cost_Material_Services;
                $Contribution_Margin_job=$NetFOBValue_job-$Less_Cost_Material_Services_job;
                ?>
                <td align="right" style="font-weight:bold"> <? echo fn_number_format($Contribution_Margin,4); ?></td>
                <td align="right" style="font-weight:bold"><? echo fn_number_format($Contribution_Margin_job,4); ?></td>
                <td align="right" style="font-weight:bold"><? echo fn_number_format(($Contribution_Margin_job/$summary_data[price_dzn_job])*100,4);?></td>
            </tr>
            <tr>
                <td align="center">11</td>
                <td style=" padding-left:15px">Less: CM Cost </td>
                <td align="right"><? echo fn_number_format($summary_data[cm_cost],4); ?> </td>
                <td align="right"><? echo fn_number_format($summary_data[cm_cost_job],4); ?></td>
                <td align="right"><? echo fn_number_format(($summary_data[cm_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>
            </tr>
            <tr>
                <td align="center">12</td>
                <td style="font-weight:bold">Gross Profit (10-11)</td>
                <?
                $Gross_Profit=$Contribution_Margin-$summary_data[cm_cost];
                $Gross_Profit_job=$Contribution_Margin_job-$summary_data[cm_cost_job];
                ?>
                <td align="right" style="font-weight:bold"> <? echo fn_number_format($Gross_Profit,4); ?></td>
                <td align="right" style="font-weight:bold"><? echo fn_number_format($Gross_Profit_job,4); ?></td>
                <td align="right" style="font-weight:bold"><? echo fn_number_format(($Gross_Profit_job/$summary_data[price_dzn_job])*100,4);?></td>
            </tr>
            <tr>
                <td align="center">13</td>
                <td style=" padding-left:15px">Less: Commercial Cost</td>
                <td align="right"> <? echo fn_number_format( $summary_data[comm_cost],4); ?></td>
                <td align="right"><? echo fn_number_format( $summary_data[comm_cost_job],4); ?></td>
                <td align="right"><? echo fn_number_format(($summary_data[comm_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>
            </tr>
            <tr>
                <td align="center">14</td>
                <td style=" padding-left:15px">Less: Operating Expensees</td>
                <td align="right"><? echo fn_number_format( $summary_data[common_oh],4); ?> </td>
                <td align="right"><? echo fn_number_format( $summary_data[common_oh_job],4); ?> </td>
                <td align="right"><? echo fn_number_format(($summary_data[common_oh_job]/$summary_data[price_dzn_job])*100,4);?></td>
            </tr>
            <tr >
                <td align="center">15</td>
                <td style="font-weight:bold">Operating Profit/ Loss (12-(13+14))</td>
                <?
                $OperatingProfitLoss=$Gross_Profit-($summary_data[comm_cost]+$summary_data[common_oh]);
                $OperatingProfitLoss_job=$Gross_Profit_job-($summary_data[comm_cost_job]+$summary_data[common_oh_job]);
                ?>
                <td align="right" style="font-weight:bold"> <? echo fn_number_format($OperatingProfitLoss,4); ?></td>
                <td align="right" style="font-weight:bold"><? echo fn_number_format($OperatingProfitLoss_job,4); ?></td>
                <td align="right" style="font-weight:bold"><? echo fn_number_format(($OperatingProfitLoss_job/$summary_data[price_dzn_job])*100,4);?></td>
            </tr>
            <tr>
                <td align="center">16</td>
                <td style=" padding-left:15px">Less: Depreciation & Amortization </td>
                
                <td align="right"> <? echo fn_number_format( $summary_data[depr_amor_pre_cost],4); ?></td>
                <td align="right"><? echo fn_number_format( $summary_data[depr_amor_pre_cost_job],4); ?></td>
                <td align="right"><? echo fn_number_format(($summary_data[depr_amor_pre_cost_job]/$summary_data[price_dzn_job])*100,4);?></td>
            </tr>
            <tr>
				<?
                $pre_costing_date=change_date_format($costing_date,'','',1);
                $interest_expense=$NetFOBValue*$financial_para[$pre_costing_date][interest_expense]/100;
                $income_tax=$NetFOBValue*$financial_para[$pre_costing_date][income_tax]/100;
                $interest_expense_job=$NetFOBValue_job*$financial_para[$pre_costing_date][interest_expense]/100;
                $income_tax_job=$NetFOBValue_job*$financial_para[$pre_costing_date][income_tax]/100;
                ?>
                <td align="center">17</td>
                <td style=" padding-left:15px">Less: Interest </td>
                
                <td align="right"> <? echo fn_number_format( $interest_expense,4); ?></td>
                <td align="right"><? echo fn_number_format( $interest_expense_job,4); ?></td>
                <td align="right"><? echo fn_number_format(($interest_expense_job/$summary_data[price_dzn_job])*100,4);?></td>
            </tr>
            <tr>
                <td align="center">18</td>
                <td style=" padding-left:15px">Less: Income Tax</td>
                
                <td align="right"> <? echo fn_number_format( $income_tax,4); ?></td>
                <td align="right"><? echo fn_number_format( $income_tax_job,4); ?></td>
                <td align="right"><? echo fn_number_format(($income_tax_job/$summary_data[price_dzn_job])*100,4);?></td>
            </tr>
            <tr>
				<?
                $Netprofit=$OperatingProfitLoss-($summary_data[depr_amor_pre_cost]+$interest_expense+$income_tax);
                $Netprofit_job=$OperatingProfitLoss_job-($summary_data[depr_amor_pre_cost_job]+$interest_expense_job+$income_tax_job);
                ?>
                <td align="center">19</td>
                <td style="font-weight:bold">Net Profit/Loss (15-(16+17+18))</td>
                
                <td align="right" style="font-weight:bold"><? echo fn_number_format( $Netprofit,4); ?> </td>
                <td align="right" style="font-weight:bold"><? echo fn_number_format( $Netprofit_job,4); ?></td>
                <td align="right" style="font-weight:bold"><? echo fn_number_format(($Netprofit_job/$summary_data[price_dzn_job])*100,4);?></td>
            </tr>
        </table>
    </div>
    <?
	//End all summary report here -------------------------------------------
	//2	All Fabric Cost part here-------------------------------------------
	$sql = "select id, job_no, body_part_id, fab_nature_id, color_type_id, fabric_description, uom, avg_cons, avg_cons_yarn, fabric_source, gsm_weight, rate, amount, avg_finish_cons, status_active from wo_pre_cost_fabric_cost_dtls_h where job_no=".$txt_job_no."  and status_active=1 and is_deleted=0 and approved_no=$revised_no";
	$data_array=sql_select($sql);

	$knit_fab=""; $woven_fab="";
	$knit_subtotal_avg_cons=0; $knit_subtotal_amount=0; $woven_subtotal_avg_cons=0; $woven_subtotal_amount=0;
	$grand_total_amount=0;

	$i=1; $j=1;
	foreach( $data_array as $row )
	{
		$uom=$unit_of_measurement[$row[csf("uom")]];
		$item_descrition = $body_part[$row[csf("body_part_id")]].", ".$color_type[$row[csf("color_type_id")]].", ".$row[csf("fabric_description")].", ".$row[csf("gsm_weight")];

		if($row[csf("fab_nature_id")]==2){//knit fabrics
			$i++;
			$totalConsKnit=$fabric_qty['knit']['grey'][$row[csf("id")]][$row[csf("uom")]];
			$totalAmountKnit=$fabric_amount['knit']['grey'][$row[csf("id")]][$row[csf("uom")]];
			$amount=$row[csf("avg_cons")]*$row[csf("rate")];

			$knit_fab .= '<tr>
				<td align="left">'.$item_descrition.'</td>
				<td align="left">'.$fabric_source[$row[csf("fabric_source")]].'</td>
				<td align="right">'.fn_number_format($row[csf("avg_cons")],4).'</td>
				<td align="right">'.fn_number_format($totalConsKnit,4).'</td>
				<td align="left">'.$uom.'</td>
				<td align="right">'.fn_number_format($row[csf("rate")],4).'</td>
				<td align="right">'.fn_number_format($amount,4).'</td>
				<td align="right">'.fn_number_format($totalAmountKnit,4).'</td>
			</tr>';
			if($row[csf("uom")]==1){
				$DznConsKnitSubTotalPcs += $row[csf("avg_cons")];
				$TotalConsKnitSubTotalPcs += $totalConsKnit;
				$DznAmountKnitSubTotalPcs+=$amount;
				$TotalAmountKnitSubTotalPcs+=$totalAmountKnit;
			 }
			 if($row[csf("uom")]==12){
				$DznConsKnitSubTotalKg += $row[csf("avg_cons")];
				$TotalConsKnitSubTotalKg += $totalConsKnit;
				$DznAmountKnitSubTotalKg+=$amount;
				$TotalAmountKnitSubTotalKg+=$totalAmountKnit;
			 }
			 if($row[csf("uom")]==23){
				$DznConsKnitSubTotalMtr += $row[csf("avg_cons")];
				$TotalConsKnitSubTotalMtr += $totalConsKnit;
				$DznAmountKnitSubTotalMtr+=$amount;
				$TotalAmountKnitSubTotalMtr+=$totalAmountKnit;
			 }
			 if($row[csf("uom")]==27){
				$DznConsKnitSubTotalYds += $row[csf("avg_cons")];
				$TotalConsKnitSubTotalYds += $totalConsKnit;
				$DznAmountKnitSubTotalYds+=$amount;
				$TotalAmountKnitSubTotalYds+=$totalAmountKnit;
			 }
			 $GrandtotalDznAmount+=$amount;
			 $GrandtotalAmount+=$totalAmountKnit;

			 $GrandTotalFabricDznAmount+=$amount;
			 $GrandTotalFabricAmount+=$totalAmountKnit;
		}

		if($row[csf("fab_nature_id")]==3){//woven fabrics
			$j++;
			$totalConsWoven=$fabric_qty['woven']['grey'][$row[csf("id")]][$row[csf("uom")]];
			$totalAmountWoven=$fabric_amount['woven']['grey'][$row[csf("id")]][$row[csf("uom")]];
			$amount=$row[csf("avg_cons")]*$row[csf("rate")];
			$woven_fab .= '<tr>
				<td align="left">'.$item_descrition.'</td>
				<td align="left">'.$fabric_source[$row[csf("fabric_source")]].'</td>
				<td align="right">'.fn_number_format($row[csf("avg_cons")],4).'</td>
				<td align="right">'.fn_number_format($totalConsWoven,4).'</td>
				<td align="left">'.$uom.'</td>
				<td align="right">'.fn_number_format($row[csf("rate")],4).'</td>
				<td align="right">'.fn_number_format($amount,4).'</td>
				<td align="right">'.fn_number_format($totalAmountWoven,4).'</td>
			</tr>';

			if($row[csf("uom")]==1){
				$DznConsWovenSubTotalPcs += $row[csf("avg_cons")];
				$TotalConsWovenSubTotalPcs += $totalConsWoven;
				$DznAmountWovenSubTotalPcs+=$amount;
				$TotalAmountWovenSubTotalPcs+=$totalAmountWoven;
			 }
			 if($row[csf("uom")]==12){
				$DznConsWovenSubTotalKg += $row[csf("avg_cons")];
				$TotalConsWovenSubTotalKg += $totalConsWoven;
				$DznAmountWovenSubTotalKg+=$amount;
				$TotalAmountWovenSubTotalKg+=$totalAmountWoven;
			 }
			 if($row[csf("uom")]==23){
				$DznConsWovenSubTotalMtr += $row[csf("avg_cons")];
				$TotalConsWovenSubTotalMtr += $totalConsWoven;
				$DznAmountWovenSubTotalMtr+=$amount;
				$TotalAmountWovenSubTotalMtr+=$totalAmountWoven;
			 }
			 if($row[csf("uom")]==27){
				$DznConsWovenSubTotalYds += $row[csf("avg_cons")];
				$TotalConsWovenSubTotalYds += $totalConsWoven;
				$DznAmountWovenSubTotalYds+=$amount;
				$TotalAmountWovenSubTotalYds+=$totalAmountWoven;
			 }
			 $GrandtotalDznAmount+=$amount;
			 $GrandtotalAmount+=$totalAmountWoven;

			 $GrandTotalFabricDznAmount+=$amount;
			 $GrandTotalFabricAmount+=$totalAmountWoven;
		}
	}
		
	if($DznConsKnitSubTotalPcs>0) $i++;
	if($DznConsKnitSubTotalKg>0) $i++;
	if($DznConsKnitSubTotalMtr>0) $i++;
	if($DznConsKnitSubTotalYds>0) $i++;
	if($DznConsWovenSubTotalPcs>0) $j++;
	if($DznConsWovenSubTotalKg>0) $j++;
	if($DznConsWovenSubTotalMtr>0) $j++;
	if($DznConsWovenSubTotalYds>0) $j++;
		
	$knit_fab= '<div style="margin-top:15px">
			<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
				<label><b>All Fabric Cost  </b></label>
					<tr style="font-weight:bold"  align="center">
						<td width="80" rowspan="'.$i.'" ><div class="verticalText"><b>Knit Fabric</b></div></td>
						<td width="350">Description</td>
						<td width="100">Source</td>
						<td width="100">Fab. Cons/'.$costing_for.'</td>
						<td width="100">Total Cons</td>
						<td width="100">UOM</td>
						<td width="100">Rate (USD)</td>
						<td width="100">Amount (USD)/'.$costing_for.'</td>
						<td width="100">Tot.Amount (USD)</td>
					</tr>'.$knit_fab;
	if($DznConsWovenSubTotalPcs>0 || $DznConsWovenSubTotalKg>0 || $DznConsWovenSubTotalMtr>0 || $DznConsWovenSubTotalYds>0){
		$woven_fab= '<tr><td colspan="9">&nbsp;</td></tr><tr>
					<td width="80" rowspan="'.$j.'"><div class="verticalText"><b>Woven Fabric</b></div></td></tr>'.$woven_fab;
	}

	//knit fabrics table here
	if($DznConsKnitSubTotalPcs>0){
		$knit_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2" align="left">Sub Total(Pcs)</td>
						<td align="right">'.fn_number_format($DznConsKnitSubTotalPcs,4).'</td>
						<td align="right">'.fn_number_format($TotalConsKnitSubTotalPcs,4).'</td>
						<td></td>
						<td></td>
						<td align="right">'.fn_number_format($DznAmountKnitSubTotalPcs,4).'</td>
						<td align="right">'.fn_number_format($TotalAmountKnitSubTotalPcs,4).'</td>
					</tr>';
	}
	if($DznConsKnitSubTotalKg>0){
		$knit_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2" align="left">Sub Total(Kg)</td>
						<td align="right">'.fn_number_format($DznConsKnitSubTotalKg,4).'</td>
						<td align="right">'.fn_number_format($TotalConsKnitSubTotalKg,4).'</td>
						<td></td>
						<td></td>
						<td align="right">'.fn_number_format($DznAmountKnitSubTotalKg,4).'</td>
						<td align="right">'.fn_number_format($TotalAmountKnitSubTotalKg,4).'</td>
					</tr>';
	}
	if($DznConsKnitSubTotalMtr>0){
		$knit_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2" align="left">Sub Total(Mtr)</td>
						<td align="right">'.fn_number_format($DznConsKnitSubTotalMtr,4).'</td>
						<td align="right">'.fn_number_format($TotalConsKnitSubTotalMtr,4).'</td>
						<td></td>
						<td></td>
						<td align="right">'.fn_number_format($DznAmountKnitSubTotalMtr,4).'</td>
						<td align="right">'.fn_number_format($TotalAmountKnitSubTotalMtr,4).'</td>
					</tr>';
	}
	if($DznConsKnitSubTotalYds>0){
		$knit_fab .='<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2" align="left">Sub Total(Yds)</td>
						<td align="right">'.fn_number_format($DznConsKnitSubTotalYds,4).'</td>
						<td align="right">'.fn_number_format($TotalConsKnitSubTotalYds,4).'</td>
						<td></td>
						<td></td>
						<td align="right">'.fn_number_format($DznAmountKnitSubTotalYds,4).'</td>
						<td align="right">'.fn_number_format($TotalAmountKnitSubTotalYds,4).'</td>
					</tr>';
	}
	
	if($zero_value==1) echo $knit_fab;
	else
	{
		if($row[csf("avg_cons")]>0) echo $knit_fab; else echo "";
	}

	//woven fabrics table here
	if($DznConsWovenSubTotalPcs>0){
		$woven_fab .='<tr class="rpt_bottom" style="font-weight:bold">
					<td colspan="2" align="left">Sub Total(Pcs)</td>
					<td align="right">'.fn_number_format($DznConsWovenSubTotalPcs,4).'</td>
					<td align="right">'.fn_number_format($TotalConsWovenSubTotalPcs,4).'</td>
					<td></td>
					<td></td>
					<td align="right">'.fn_number_format($DznAmountWovenSubTotalPcs,4).'</td>
					<td align="right">'.fn_number_format($TotalAmountWovenSubTotalPcs,4).'</td>
				</tr>';
	}
	if($DznConsWovenSubTotalKg>0){
		$woven_fab .='<tr class="rpt_bottom" style="font-weight:bold">
					<td colspan="2" align="left">Sub Total(Kg)</td>
					<td align="right">'.fn_number_format($DznConsWovenSubTotalKg,4).'</td>
					<td align="right">'.fn_number_format($TotalConsWovenSubTotalKg,4).'</td>
					<td></td>
					<td></td>
					<td align="right">'.fn_number_format($DznAmountWovenSubTotalKg,4).'</td>
					<td align="right">'.fn_number_format($TotalAmountWovenSubTotalKg,4).'</td>
				</tr>';
	}
	if($DznConsWovenSubTotalMtr>0){
		$woven_fab .='<tr class="rpt_bottom" style="font-weight:bold">
					<td colspan="2" align="left">Sub Total(Mtr)</td>
					<td align="right">'.fn_number_format($DznConsWovenSubTotalMtr,4).'</td>
					<td align="right">'.fn_number_format($TotalConsWovenSubTotalMtr,4).'</td>
					<td></td>
					<td></td>
					<td align="right">'.fn_number_format($DznAmountWovenSubTotalMtr,4).'</td>
					<td align="right">'.fn_number_format($TotalAmountWovenSubTotalMtr,4).'</td>
				</tr>';
	}
	if($DznConsWovenSubTotalYds>0){
		$woven_fab .='<tr class="rpt_bottom" style="font-weight:bold">
					<td colspan="2" align="left">Sub Total(Yds)</td>
					<td align="right">'.fn_number_format($DznConsWovenSubTotalYds,4).'</td>
					<td align="right">'.fn_number_format($TotalAmountWovenSubTotalYds,4).'</td>
					<td></td>
					<td></td>
					<td align="right">'.fn_number_format($DznAmountWovenSubTotalYds,4).'</td>
					<td align="right">'.fn_number_format($DznAmountWovenSubTotalYds,4).'</td>
				</tr>';
	}

		$woven_fab .='<tr class="rpt_bottom" style="font-weight:bold">
					<td colspan="4" align="left">Total</td>
					<td align="right"></td>
					<td></td>
					<td></td>
					<td align="right">'.fn_number_format(($GrandtotalDznAmount),4).'</td>
					<td align="right">'.fn_number_format(($GrandtotalAmount),4).'</td>
				</tr></table></div>';
	if($zero_value==1) echo $woven_fab;
	else{
			if($row[csf("avg_cons")]>0) echo $woven_fab; else echo "";
	}
	//end 	All Fabric Cost part report-------------------------------------------

	//Start	Yarn Cost part report here -------------------------------------------
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	if($zero_value==1)
	{
		?>
		<div style="margin-top:15px">
			<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
				<tr style="font-weight:bold">
					<td width="70" rowspan="<? echo count($YarnData)+2; ?>"><div class="verticalText"><b>Yarn Cost</b></div></td>
					<td width="350">Yarn Desc</td>
					<td width="100">Yarn Qty</td>
					<td width="100">Total Qty</td>
					<td width="100">Rate (USD)</td>
					<td width="100">Amount (USD)</td>
					<td width="100">Tot.Amount (USD)</td>
				</tr>
			<?
			$TotalDznQty = 0; $TotalQty = 0; $TotalDznAmount = 0; $TotalAmount = 0;
			foreach( $YarnData as $index=>$row )
			{
				$des=explode("_",str_replace("'","",$index));
				$item_descrition = $lib_yarn_count[$des[0]]." ".$composition[$des[1]]." ".$des[2]."% ".$color_library[$des[4]]." ".$yarn_type[$des[3]];
				?>
				<tr>
					<td align="left"><? echo $item_descrition; ?></td>
					<td align="right"><? echo fn_number_format($row["dznqty"],4); ?></td>
					<td align="right"><? echo fn_number_format($row["qty"],4); ?></td>
					<td align="right"><? echo fn_number_format($des[5],4); ?></td>
					<td align="right"><? echo fn_number_format($row["dznamount"],4); ?></td>
					<td align="right"><? echo fn_number_format($row["amount"],4); ?></td>
				</tr>
				<?
				 $TotalDznQty += $row["dznqty"];
				 $TotalQty += $row["qty"];
				 $TotalDznAmount += $row["dznamount"];
				 $TotalAmount += $row["amount"];

				 $GrandTotalFabricDznAmount+=$row["dznamount"];
				 $GrandTotalFabricAmount+=$row["amount"];
			}
		  ?>
				<tr class="rpt_bottom" style="font-weight:bold">
					<td align="left">Total</td>
					<td align="right"><? echo fn_number_format($TotalDznQty,4); ?></td>
					<td align="right"><? echo fn_number_format($TotalQty,4); ?></td>
					<td></td>
					<td align="right"><? echo fn_number_format($TotalDznAmount,4); ?></td>
					<td align="right"><? echo fn_number_format($TotalAmount,4); ?></td>
				</tr>
			</table>
	  </div>
	  <?
  }
	else
	{
	  if($totYarn>0)
	  {
		  ?>
		   <div style="margin-top:15px">
			<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
				<tr style="font-weight:bold">
					<td width="70" rowspan="<? echo count($YarnData)+2; ?>"><div class="verticalText"><b>Yarn Cost</b></div></td>
					<td width="350">Yarn Desc</td>
					<td width="100">Yarn Qty</td>
					<td width="100">Avg.Yarn Qty</td>
					<td width="100">Rate (USD)</td>
					<td width="100">Amount (USD)</td>
					<td width="100">Tot.Amount (USD)</td>
				</tr>
			<?
			$TotalDznQty = 0; $TotalQty = 0; $TotalDznAmount = 0; $TotalAmount = 0;
			foreach( $YarnData as $index=>$row )
			{
				$des=explode("_",str_replace("'","",$index));
				$item_descrition = $lib_yarn_count[$des[0]]." ".$composition[$des[1]]." ".$des[2]."% ".$color_library[$des[4]]." ".$yarn_type[$des[3]];
				?>
				<tr>
					<td align="left"><? echo $item_descrition; ?></td>
					<td align="right"><? echo fn_number_format($row["dznqty"],4); ?></td>
					<td align="right"><? echo fn_number_format($row["qty"],4); ?></td>
					<td align="right"><? echo fn_number_format($des[5],4); ?></td>
					<td align="right"><? echo fn_number_format($row["dznamount"],4); ?></td>
					<td align="right"><? echo fn_number_format($row["amount"],4); ?></td>
				</tr>
				<?
				 $TotalDznQty += $row["dznqty"];
				 $TotalQty += $row["qty"];
				 $TotalDznAmount += $row["dznamount"];
				 $TotalAmount += $row["amount"];
	
				 $GrandTotalFabricDznAmount+=$row["dznamount"];
				 $GrandTotalFabricAmount+=$row["amount"];
			}
			?>
				<tr class="rpt_bottom" style="font-weight:bold">
					<td align="left">Total</td>
					<td align="right"><? echo fn_number_format($TotalDznQty,4); ?></td>
					 <td align="right"><? echo fn_number_format($TotalQty,4); ?></td>
					<td></td>
					<td align="right"><? echo fn_number_format($TotalDznAmount,4); ?></td>
					<td align="right"><? echo fn_number_format($TotalAmount,4); ?></td>
				</tr>
			</table>
		</div>
		<?
	  }
	  else echo "";
	}
	//End Yarn Cost part report here -------------------------------------------

  	//start	Conversion Cost to Fabric report here -------------------------------------------
	if($zero_value==1)
	{
		?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <tr style="font-weight:bold">
                    <td width="80" rowspan="<? echo count($ConvData)+3; ?>"><div class="verticalText"><b>Conversion Cost to Fabric</b></div></td>
                    <td width="350">Particulars</td>
                    <td width="100">Process</td>
                    <td width="100">Cons/<? echo $costing_for; ?></td>
                    <td width="100">Total Cons/<? echo $costing_for; ?></td>
                    <td width="100">Uom</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                    <td width="100">Total Amount (USD)</td>
                </tr>
				<?
                $TotalDznAmount =0; $TotalAmount =0;
                foreach( $ConvData as $index=>$row )
                {
                    ?>
                    <tr>
                        <td align="left"><? echo $row['item_descrition']; ?></td>
                        <td align="left"><? echo $conversion_cost_head_array[$row["cons_process"]]; ?></td>
                        <td align="right"><? echo fn_number_format($row["req_qnty"],4); ?></td>
                        <td align="right"><? echo fn_number_format($row["tot_req_qnty"],4); ?></td>
                        <td align=""><? echo $unit_of_measurement[$row["uom"]]; ?></td>
                        <td align="right"><? echo fn_number_format($row["charge_unit"],4); ?></td>
                        <td align="right"><? echo fn_number_format($row["amount"],4); ?></td>
                        <td align="right"><? echo fn_number_format($row["tot_amount"],4); ?></td>
                    </tr>
                    <?
                     $TotalDznAmount += $row["amount"];
                     $TotalAmount += $row["tot_amount"];
    
                     $GrandTotalFabricDznAmount+=$row["amount"];
                     $GrandTotalFabricAmount+=$row["tot_amount"];
                }
                ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td  align="left" colspan="6">Total</td>
                    <td align="right"><? echo fn_number_format($TotalDznAmount,4); ?></td>
                    <td align="right"><? echo fn_number_format($TotalAmount,4); ?></td>
                </tr>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td align="left" colspan="6">Total Fabric Cost</td>
                    <td align="right"><? echo fn_number_format($GrandTotalFabricDznAmount,4); ?></td>
                    <td align="right"><? echo fn_number_format($GrandTotalFabricAmount,4); ?></td>
                </tr>
            </table>
        </div>
        <?
	}
	else
	{
		if($totConv>0)
		{
			?>
			 <div style="margin-top:15px">
                <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                    <tr style="font-weight:bold">
                        <td width="80" rowspan="<? echo count($ConvData)+3; ?>"><div class="verticalText"><b>Conversion Cost to Fabric</b></div></td>
                        <td width="350">Particulars</td>
                        <td width="100">Process</td>
                        <td width="100">Cons/<? echo $costing_for; ?></td>
                        <td width="100">Total Cons/<? echo $costing_for; ?></td>
                         <td width="100">Uom</td>
                        <td width="100">Rate (USD)</td>
                        <td width="100">Amount (USD)</td>
                         <td width="100">Total Amount (USD)</td>
                    </tr>
					<?
				    $TotalDznAmount =0; $TotalAmount =0;
					foreach( $ConvData as $index=>$row)
					{
						?>
						<tr>
							<td align="left"><? echo $row['item_descrition']; ?></td>
							<td align="left"><? echo $conversion_cost_head_array[$row["cons_process"]]; ?></td>
							<td align="right"><? echo fn_number_format($row["req_qnty"],4); ?></td>
							<td align="right"><? echo fn_number_format($row["tot_req_qnty"],4); ?></td>
							<td align=""><? echo $unit_of_measurement[$row["uom"]]; ?></td>
							<td align="right"><? echo fn_number_format($row["charge_unit"],4); ?></td>
							<td align="right"><? echo fn_number_format($row["amount"],4); ?></td>
							<td align="right"><? echo fn_number_format($row["tot_amount"],4); ?></td>
						</tr>
						<?
						 $TotalDznAmount += $row["amount"];
						 $TotalAmount += $row["tot_amount"];
		
						 $GrandTotalFabricDznAmount+=$row["amount"];
						 $GrandTotalFabricAmount+=$row["tot_amount"];
					}
					?>
                    <tr class="rpt_bottom" style="font-weight:bold">
                        <td align="left" colspan="6">Total</td>
                        <td align="right"><? echo $TotalDznAmount; ?></td>
                        <td align="right"><? echo $TotalAmount; ?></td>
                    </tr>
                    <tr class="rpt_bottom" style="font-weight:bold">
                        <td align="left" colspan="6">Total Fabric Cost</td>
                        <td align="right"><? echo fn_number_format($GrandTotalFabricDznAmount,4); ?></td>
                        <td align="right"><? echo fn_number_format($GrandTotalFabricAmount,4); ?></td>
                    </tr>
                </table>
			  </div>
			  <?
		}
		else echo "";
	}
	//End Conversion Cost to Fabric report here -------------------------------------------
  	//start	Trims Cost part report here -------------------------------------------
	$trim_group=return_library_array( "select item_name,id from  lib_item_group", "id", "item_name"  );
	if($zero_value==1)
	{
		?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Trims Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Item Group</td>
                    <td width="150">Description</td>
                    <td width="150">Brand/Supp Ref</td>
                    <td width="150">Remarks</td>
                    <td width="100">UOM</td>
                    <td width="100">Cons/<? echo $costing_for; ?></td>
                    <td width="100">Tot. Cons/<? echo $costing_for; ?></td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                    <td width="100">Tot. Amount</td>
                </tr>
				<?
                $TotalDznAmount=0; $TotalAmount=0;
                foreach( $TrimData as $index=>$row )
                {
                    ?>
                        <tr>
                            <td align="left"><? echo $trim_group[$row["trim_group"]]; ?></td>
                            <td align="left"><? echo $row["description"]; ?></td>
                            <td align="left"><? echo $row["brand_sup_ref"]; ?></td>
                            <td align="left"><? echo $row["remark"]; ?></td>
                            <td align="left"><? echo $unit_of_measurement[$row["cons_uom"]]; ?></td>
                            <td align="right"><? echo fn_number_format($row["cons_dzn_gmts"],4); ?></td>
                            <td align="right"><? echo fn_number_format($row["tot_cons"],4); ?></td>
                            <td align="right"><? echo fn_number_format($row["rate"],4); ?></td>
                            <td align="right"><? echo fn_number_format($row["amount"],4); ?></td>
                            <td align="right"><? echo fn_number_format($row["tot_amount"],4); ?></td>
                        </tr>
                    <?
                     $TotalDznAmount += $row["amount"];
                     $TotalAmount += $row["tot_amount"];
                }
                ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="8" align="left">Total</td>
                    <td align="right"><? echo fn_number_format($TotalDznAmount,4); ?></td>
                    <td align="right"><? echo fn_number_format($TotalAmount,4); ?></td>
                </tr>
            </table>
        </div>
        <?
	}
	else
	{
		if($totTrim>0)
		{
			?>
			<div style="margin-top:15px">
                <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                <label><b>Trims Cost</b></label>
                    <tr style="font-weight:bold">
                        <td width="150">Item Group</td>
                        <td width="150">Description</td>
                        <td width="150">Brand/Supp Ref</td>
                        <td width="150">Remarks</td>
                        <td width="100">UOM</td>
                        <td width="100">Cons/<? echo $costing_for; ?></td>
                        <td width="100">Tot. Cons/<? echo $costing_for; ?></td>
                        <td width="100">Rate (USD)</td>
                        <td width="100">Amount (USD)</td>
                        <td width="100">Tot. Amount</td>
                    </tr>
					<?
					$TotalDznAmount=0; $TotalAmount=0;
					foreach( $TrimData as $index=>$row)
					{
						?>
							<tr>
								<td align="left"><? echo $trim_group[$row["trim_group"]]; ?></td>
								<td align="left"><? echo $row["description"]; ?></td>
								<td align="left"><? echo $row["brand_sup_ref"]; ?></td>
								<td align="left"><? echo $row["remark"]; ?></td>
								<td align="left"><? echo $unit_of_measurement[$row["cons_uom"]]; ?></td>
								<td align="right"><? echo fn_number_format($row["cons_dzn_gmts"],4); ?></td>
								<td align="right"><? echo fn_number_format($row["tot_cons"],4); ?></td>
								<td align="right"><? echo fn_number_format($row["rate"],4); ?></td>
								<td align="right"><? echo fn_number_format($row["amount"],4); ?></td>
								<td align="right"><? echo fn_number_format($row["tot_amount"],4); ?></td>
							</tr>
						<?
						 $TotalDznAmount += $row["amount"];
						 $TotalAmount += $row["tot_amount"];
					}
					?>
                    <tr class="rpt_bottom" style="font-weight:bold">
                        <td colspan="8" align="left">Total</td>
                        <td align="right"><? echo fn_number_format($TotalDznAmount,4); ?></td>
                        <td align="right"><? echo fn_number_format($TotalAmount,4); ?></td>
                    </tr>
                </table>
            </div>
            <?
		}
		else echo "";
	}
	//End Trims Cost Part report here -------------------------------------------
	//start	Embellishment Details part report here -------------------------------------------
	if($zero_value==1)
	{
		?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Embellishment Details</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Type</td>
                    <td width="150">Cons/<? echo $costing_for; ?></td>
                    <td width="150">Tot.Cons/<? echo $costing_for; ?></td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                    <td width="100">Tot. Amount</td>
                 </tr>
				<?
                $TotalDznAmount=0; $TotalAmount =0;
                foreach( $EmbData as $index=>$row )
                {
                    $em_type ="";
                    //$emblishment_name_array=array(1=>"Printing",2=>"Embroidery",3=>"Wash",4=>"Special Works",5=>"Others");
                    if($row["emb_name"]==1)$em_type = $emblishment_print_type[$row["emb_type"]];
                    else if($row["emb_name"]==2)$em_type = $emblishment_embroy_type[$row["emb_type"]];
                    else if($row["emb_name"]==3)$em_type = $emblishment_wash_type[$row["emb_type"]];
                    else if($row["emb_name"]==4)$em_type = $emblishment_spwork_type[$row["emb_type"]];
                    else if($row["emb_name"]==5)$em_type = $emblishment_gmts_type[$row["emb_type"]];
					?>
						<tr>
							<td align="left"><? echo $emblishment_name_array[$row["emb_name"]]; ?></td>
							<td align="left"><? echo $em_type; ?></td>
							<td align="right"><? echo fn_number_format($row["cons_dzn_gmts"],4); ?></td>
							<td align="right"><? echo fn_number_format($row["tot_cons"],4); ?></td>
							<td align="right"><? echo fn_number_format($row["rate"],4); ?></td>
							<td align="right"><? echo fn_number_format($row["amount"],4); ?></td>
							<td align="right"><? echo fn_number_format($row["tot_amount"],4); ?></td>
						</tr>
					<?
                     $TotalDznAmount += $row["amount"];
                     $TotalAmount += $row["tot_amount"];
                }
                ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="5" align="left">Total</td>
                    <td align="right"><? echo fn_number_format($TotalDznAmount,4); ?></td>
                    <td align="right"><? echo fn_number_format($TotalAmount,4); ?></td>
                </tr>
            </table>
        </div>
        <?
	}
	else
	{
		if($totEmb>0)
		{
			?>
			<div style="margin-top:15px">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
				<label><b>Embellishment Details</b></label>
					<tr style="font-weight:bold">
						<td width="150">Particulars</td>
						<td width="150">Type</td>
						<td width="150">Cons/<? echo $costing_for; ?></td>
						<td width="150">Tot.Cons/<? echo $costing_for; ?></td>
						<td width="100">Rate (USD)</td>
						<td width="100">Amount (USD)</td>
						<td width="100">Tot. Amount</td>
					 </tr>
					<?
                    $TotalDznAmount=0; $TotalAmount =0;
                    foreach( $EmbData as $index=>$row  )
                    {
                        $em_type ="";
                        //$emblishment_name_array=array(1=>"Printing",2=>"Embroidery",3=>"Wash",4=>"Special Works",5=>"Others");
                        if($row["emb_name"]==1)$em_type = $emblishment_print_type[$row["emb_type"]];
                        else if($row["emb_name"]==2)$em_type = $emblishment_embroy_type[$row["emb_type"]];
                        else if($row["emb_name"]==3)$em_type = $emblishment_wash_type[$row["emb_type"]];
                        else if($row["emb_name"]==4)$em_type = $emblishment_spwork_type[$row["emb_type"]];
                        else if($row["emb_name"]==5)$em_type = $emblishment_gmts_type[$row["emb_type"]];
                        ?>
                            <tr>
                                <td align="left"><? echo $emblishment_name_array[$row["emb_name"]]; ?></td>
                                <td align="left"><? echo $em_type; ?></td>
                                <td align="right"><? echo fn_number_format($row["cons_dzn_gmts"],4); ?></td>
                                <td align="right"><? echo fn_number_format($row["tot_cons"],4); ?></td>
                                <td align="right"><? echo fn_number_format($row["rate"],4); ?></td>
                                <td align="right"><? echo fn_number_format($row["amount"],4); ?></td>
                                <td align="right"><? echo fn_number_format($row["tot_amount"],4); ?></td>
                            </tr>
                        <?
                         $TotalDznAmount += $row["amount"];
                         $TotalAmount += $row["tot_amount"];
                    }
                    ?>
					<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="5" align="left">Total</td>
						<td align="right"><? echo fn_number_format($TotalDznAmount,4); ?></td>
						<td align="right"><? echo fn_number_format($TotalAmount,4); ?></td>
					</tr>
				</table>
			</div>
			<?
		}
		else echo "";
	}
	//End Embellishment Details Part report here -------------------------------------------

  	//start	Commercial Cost part report here -------------------------------------------
	if($zero_value==1)
	{
		?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commercial Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="100">Rate In %</td>
                    <td width="100">Amount (USD)</td>
                    <td width="100">Tot.Amount</td>
                 </tr>
				<?
                $TotalDznAount=0; $TotalAount =0;
                foreach( $CommarData as $index=>$row )
                {
					?>
						<tr>
							<td align="left"><? echo $camarcial_items[$row["item_id"]]; ?></td>
							<td align="right"><? echo fn_number_format($row["rate"],4); ?></td>
							<td align="right"><? echo fn_number_format($row["amount"],4); ?></td>
							<td align="right"><? echo fn_number_format($row["tot_amount"],4); ?></td>
						</tr>
					<?
                     $TotalDznAount += $row["amount"];
                     $TotalAount += $row["tot_amount"];
                }
                ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="2" align="left">Total</td>
                    <td align="right"><? echo fn_number_format($TotalDznAount,4); ?></td>
                    <td align="right"><? echo fn_number_format($TotalAount,4); ?></td>
                </tr>
            </table>
        </div>
        <?
	}
	else
	{
		if($totCommar>0)
		{
			?>
			<div style="margin-top:15px">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
				<label><b>Commercial Cost</b></label>
					<tr style="font-weight:bold">
						<td width="150">Particulars</td>
						<td width="100">Rate (USD)</td>
						<td width="100">Amount (USD)</td>
						<td width="100">Tot.Amount</td>
					 </tr>
					<?
                    $TotalDznAount=0; $TotalAount =0;
                    foreach( $CommarData as $index=>$row  )
                    {
						?>
							<tr>
								<td align="left"><? echo $camarcial_items[$row["item_id"]]; ?></td>
								<td align="right"><? echo fn_number_format($row["rate"],4); ?></td>
								<td align="right"><? echo fn_number_format($row["amount"],4); ?></td>
								<td align="right"><? echo fn_number_format($row["tot_amount"],4); ?></td>
							</tr>
						<?
                         $TotalDznAount += $row["amount"];
                         $TotalAount += $row["tot_amount"];
                    }
                    ?>
					<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="2" align="left">Total</td>
						<td align="right"><? echo fn_number_format($TotalDznAount,4); ?></td>
						<td align="right"><? echo fn_number_format($TotalAount,4); ?></td>
					</tr>
				</table>
            </div>
            <?
		}
		else echo "";
	}
	//End Commercial Cost Part report here -------------------------------------------
  	//start	Commission Cost part report here -------------------------------------------
	if($zero_value==1)
	{
		?>
        <div style="margin-top:15px">
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
            <label><b>Commission Cost</b></label>
                <tr style="font-weight:bold">
                    <td width="150">Particulars</td>
                    <td width="150">Commission Basis</td>
                    <td width="100">Rate (USD)</td>
                    <td width="100">Amount (USD)</td>
                    <td width="100">Tot. Amount</td>
                 </tr>
				<?
                $TotalDznAount = 0; $TotalAount = 0;
                foreach( $CommiData as $index=>$row )
                {
					?>
						<tr>
							<td align="left"><? echo $commission_particulars[$row["particulars_id"]]; ?></td>
							<td align="left"><? echo $commission_base_array[$row["commission_base_id"]]; ?></td>
							<td align="right"><? echo fn_number_format($row["commision_rate"],4); ?></td>
							<td align="right"><? echo fn_number_format($row["commission_amount"],4); ?></td>
							<td align="right"><? echo fn_number_format($row["tot_commission_amount"],4); ?></td>
						</tr>
					<?
                     $TotalDznAount += $row["commission_amount"];
                     $TotalAount += $row["tot_commission_amount"];
                }
                ?>
                <tr class="rpt_bottom" style="font-weight:bold">
                    <td colspan="3" align="left">Total</td>
                    <td align="right"><? echo fn_number_format($TotalDznAount,4); ?></td>
                    <td align="right"><? echo fn_number_format($TotalAount,4); ?></td>
                </tr>
            </table>
        </div>
        <?
	}
	else
	{
		if($totCommi>0)
		{
			$TotalDznAount = 0; $TotalAount = 0;
			?>
			<div style="margin-top:15px">
				<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
				<label><b>Commission Cost</b></label>
					<tr style="font-weight:bold">
						<td width="150">Particulars</td>
						<td width="150">Commission Basis</td>
						<td width="100">Rate (USD)</td>
						<td width="100">Amount (USD)</td>
						<td width="100">Tot. Amount</td>
					 </tr>
				<?
				$total_amount=0;
				foreach( $CommiData as $index=>$row )
				{
					?>
						<tr>
						   <td align="left"><? echo $commission_particulars[$row["particulars_id"]]; ?></td>
							<td align="left"><? echo $commission_base_array[$row["commission_base_id"]]; ?></td>
							<td align="right"><? echo fn_number_format($row["commision_rate"],4); ?></td>
							<td align="right"><? echo fn_number_format($row["commission_amount"],4); ?></td>
							<td align="right"><? echo fn_number_format($row["tot_commission_amount"],4); ?></td>
						</tr>
					<?
					 $TotalDznAount += $row["commission_amount"];
					 $TotalAount += $row["tot_commission_amount"];
				}
				?>
					<tr class="rpt_bottom" style="font-weight:bold">
						<td colspan="3" align="left">Total</td>
						<td align="right"><? echo fn_number_format($TotalDznAount,4); ?></td>
						<td align="right"><? echo fn_number_format($TotalAount,4); ?></td>
					</tr>
				</table>
            </div>
            <?
		}
		else echo "";
	}
	//End Commission Cost Part report here -------------------------------------------
	?>
    <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:300px;" rules="all">
        <label><b>CM Details</b></label>
        <tr>
            <td width="150">CPM (TK)</td>
            <td width="150"><? echo fn_number_format($financial_para[$cm_cost_based_on_date][cost_per_minute],4); ?></td>
        </tr>
        <tr>
            <td>SMV</td>
             <td title="Sew and Cut Smv"><? 
			 if($sew_smv>0 && $cut_smv>0) echo $sew_smv.','.$cut_smv;
			 else if($sew_smv>0 && $cut_smv==0) echo   $sew_smv;
			 else if($sew_smv==0 && $cut_smv>0) echo   $cut_smv;
			 else echo ""; ?></td>
        </tr>
        <tr>
            <td>EFF %</td>
            <td title="Sew and Cut Smv %">
			<?  if($sew_effi_percent>0 && $cut_effi_percent>0) echo $sew_effi_percent.','.$cut_effi_percent;
			 else if($sew_effi_percent>0 && $cut_effi_percent==0) echo   $sew_effi_percent;
			 else if($sew_effi_percent==0 && $cut_effi_percent>0) echo   $cut_effi_percent;
			 else echo ""; ?>
            </td>
        </tr>
    </table>
    <br/>
	<?
    // image show here  -------------------------------------------
    $sqlData = "select id, master_tble_id, image_location from common_photo_library where master_tble_id=".$txt_job_no."";
    $data_array_img=sql_select($sqlData);
    ?>
    <div style=" margin-left:310px;margin-top:-70px" >
		<? foreach($data_array_img AS $inf){ ?>
        	<img  src='../../<? echo $inf[csf("image_location")]; ?>' border="1" height='97' width='89' />
        <?  } ?>
    </div>
    <br/><br/><br/> <br/>
    <?
	if($quotation_id!=0 && $quotation_id!="")
	{
		$cost_control_source=return_field_value("cost_control_source","variable_order_tracking","company_name=$cbo_company_name and variable_list=53","cost_control_source");
		if($cost_control_source==1)
		{
			$lib_temp_arr=return_library_array("select id, item_name from lib_qc_template","id","item_name");
			?>
			<table width="850" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead style="font-size:11px">
					<tr>
						<th colspan="17" align="center">Budget as Per Quick Costing: </th>
					</tr>
					<tr>
						<th width="60">Item</th>
						<th width="45">Fab. Cons. Kg</th>
						<th width="45">Fab. Cons. Mtr</th>
						<th width="45">Fab. Cons. Yds</th>
						<th width="50">Fab. Amount</th>
						<th width="50">Special Opera.</th>
						<th width="50">Access.</th>
						<th width="45">Frieght Cost</th>
						<th width="45">Lab - Test</th>
						<th width="45">Misce.</th>
						<th width="45">Other Cost</th>
						<th width="45">Commis.</th>
						<th width="50">FOB ($/DZN)</th>
						<th width="45" title="((CPM*100)/Efficiency)">CPPM</th>
						<th width="45">SMV</th>
						<th width="45">CM</th>
						<th>RMG Qty(Pcs)</th>
					</tr>
				</thead>
			<?
			$sql_confirm_dtls=sql_select("select id, mst_id, cost_sheet_id, item_id, fab_cons_kg, fab_cons_mtr, fab_cons_yds, fab_amount, sp_oparation_amount, acc_amount, fright_amount, lab_amount, misce_amount, other_amount, comm_amount, fob_amount, cppm_amount, smv_amount, cm_amount, rmg_ratio from qc_confirm_dtls_history where cost_sheet_id='$quotation_id' and approved_no=$revised_no");
			$i=1;
			foreach($sql_confirm_dtls as $row_dtls)
			{
				?>
				<tr style="font-size:11px">
					<td width="60"><? echo $lib_temp_arr[$row_dtls[csf("item_id")]]; ?></td>
					<td width="45" align="right"><? echo $row_dtls[csf("fab_cons_kg")]; ?></td>
					<td width="45" align="right"><? echo $row_dtls[csf("fab_cons_mtr")]; ?></td>
					<td width="45" align="right"><? echo $row_dtls[csf("fab_cons_yds")]; ?></td>
					<td width="50" align="right"><? echo $row_dtls[csf("fab_amount")]; ?></td>
					<td width="50" align="right"><? echo $row_dtls[csf("sp_oparation_amount")]; ?></td>
					<td width="50" align="right"><? echo $row_dtls[csf("acc_amount")]; ?></td>
					<td width="45" align="right"><? echo $row_dtls[csf("fright_amount")]; ?></td>
					<td width="45" align="right"><? echo $row_dtls[csf("lab_amount")]; ?></td>
					<td width="45" align="right"><? echo $row_dtls[csf("misce_amount")]; ?></td>
					<td width="45" align="right"><? echo $row_dtls[csf("other_amount")]; ?></td>
					<td width="45" align="right"><? echo $row_dtls[csf("comm_amount")]; ?></td>
					<td width="50" align="right"><? echo $row_dtls[csf("fob_amount")]; ?></td>
					<td width="45"  align="right"><? echo $row_dtls[csf("cppm_amount")]; ?></td>
					<td width="45" align="right"><? echo $row_dtls[csf("smv_amount")]; ?></td>
					<td width="45" align="right"><? echo $row_dtls[csf("cm_amount")]; ?></td>
					<td align="right"><? echo $row_dtls[csf("rmg_ratio")]; ?></td>
				</tr>
				<?
			}
			?>
			</table>
			<?
		}
	}
	$lib_designation_arr=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");
	$user_lib_designation_arr=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
	$user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	
	$mst_id=return_field_value("id as mst_id","wo_pre_cost_mst","job_no=$txt_job_no","mst_id");
	//and b.un_approved_date is null
	$approve_data_array=sql_select("select b.approved_by, min(b.approved_date) as approved_date from  approval_history b where b.mst_id=$mst_id and b.entry_form=15 group by  b.approved_by order by b.sequence_no asc");
	$unapprove_data_array=sql_select("select b.approved_by, b.un_approved_by, b.approved_date, b.un_approved_reason, b.un_approved_date, b.approved_no from approval_history b where b.mst_id=$mst_id and b.entry_form=15 order by b.approved_date, b.approved_by");
	// echo "select b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date,b.approved_no from   approval_history b where b.mst_id=$mst_id and b.entry_form=15  order by b.approved_no";
	if(count($approve_data_array)>0)
	{
		?>
		<table  width="850" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
                <tr style="border:1px solid black;">
                	<th colspan="5" style="border:1px solid black;">Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                    <th width="3%" style="border:1px solid black;">Sl</th>
                    <th width="40%" style="border:1px solid black;">Name</th>
                    <th width="30%" style="border:1px solid black;">Designation</th>
                    <th width="27%" style="border:1px solid black;">Approval Date</th>
                </tr>
            </thead>
            <tbody>
            <?
            $i=1;
            foreach($approve_data_array as $row)
			{
				?>
				<tr style="border:1px solid black;">
				<td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				<td width="40%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				<td width="30%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				<td width="27%" style="border:1px solid black;text-align:center"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')]));?></td>
				</tr>
				<?
				$i++;
            }
            ?>
            </tbody>
		</table>
		<?
	}
	?>
    <br/>
	<?
	$sql_unapproved=sql_select("select booking_id,approval_cause from fabric_booking_approval_cause where entry_form=15 and approval_type=2 and is_deleted=0 and status_active=1 and booking_id=$mst_id");
	//echo "select booking_id,approval_cause from fabric_booking_approval_cause where  entry_form=15 and approval_type=2 and is_deleted=0 and status_active=1 and booking_id=$mst_id";
	$unapproved_request_arr=array();
	foreach($sql_unapproved as $rowu)
	{
		$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
	}
	
	if(count($unapprove_data_array)>0)
	{
		?>
		<table  width="850" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
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
						if($row[csf('un_approved_by')]!=0) $row[csf('approved_by')]=$row[csf('un_approved_by')];
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
	}
	echo signature_table(109, $cbo_company_name, "860px");
	exit();
}

if($action=="refusing_cause_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Refusing Cause Info","../../", 1, 1, $unicode);
	$permission="1_1_1_1";
	
	$sql_cause="select refusing_reason from refusing_cause_history where entry_form=15 and mst_id='$quo_id'";	
		
	$nameArray_cause=sql_select($sql_cause);
	$app_cause='';
	foreach($nameArray_cause as $row)
	{
		$app_cause.=$row[csf("refusing_reason")].",";
	}
	$app_cause=chop($app_cause,",");
	//print_r($app_cause);
	?>
    <script>
 	var permission='<?=$permission; ?>';

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
			http.open("POST","pre_costing_approval_controller.php",true);
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
				//document.getElementById('txt_refusing_cause').value =response[1];
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
						<input type="text" name="txt_refusing_cause" id="txt_refusing_cause" class="text_boxes" style="width:320px;height: 100px;" value="<?=$cause;?>" />
						<input type="hidden" name="hidden_quo_id" id="hidden_quo_id" value="<? echo $quo_id;?>">
					</td>
                  </tr>
                  <tr>
					<td colspan="4" align="center" class="button_container">
						<?
						if(!empty($app_cause))
						{
							echo load_submit_buttons( $permission, "fnc_cause_info", 1,0 ,"reset_form('causeinfo_1','','')",1);
						}
						else
						{
							echo load_submit_buttons($permission, "fnc_cause_info", 0,0,"reset_form('causeinfo_1','','','','','');",1);
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
         <?
		$sqlHis="select approval_cause from approval_cause_refusing_his where entry_form=15 and approval_type=1 and booking_id='$quo_id' and status_active=1 and is_deleted=0 order by id Desc";
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
	$flag=1;
	if(is_duplicate_field( "approval_cause", "approval_cause_refusing_his", "approval_cause='".$refusing_cause."' and entry_form=15 and booking_id='".str_replace("'", "", $quo_id)."' and approval_type=1 and status_active=1 and is_deleted=0" )==1)
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
		//$id_his=return_next_id( "id", "approval_cause_refusing_his", 1);
		$idpre=return_field_value("max(id) as id", "refusing_cause_history", "mst_id=".$quo_id." and entry_form=15 group by mst_id","id");
		$sqlHis="insert into approval_cause_refusing_his( id, cause_id, entry_form, booking_id, approval_type, approval_cause, inserted_by, insert_date, updated_by, update_date)
				select '', id, entry_form, mst_id, 1, refusing_reason, inserted_by, insert_date, updated_by, update_date from refusing_cause_history where mst_id=".$quo_id." and entry_form=15 and id=$idpre"; //die;
		
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
		// $get_history = sql_select("SELECT id from approval_history where mst_id='$quo_id' and entry_form =15 and current_approval_status=1");
		$id=return_next_id( "id", "refusing_cause_history", 1);
		$field_array = "id,entry_form,mst_id,refusing_reason,inserted_by,insert_date";
		$data_array = "(".$id.",15,".$quo_id.",'".$refusing_cause."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
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
	else if ($operation==1)  // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}		
		//$id=return_next_id( "id", "refusing_cause_history", 1);
		
		$idpre=return_field_value("max(id) as id", "refusing_cause_history", "mst_id=".$quo_id." and entry_form=15 group by mst_id","id");
		$field_array="refusing_reason*updated_by*update_date";
		$data_array="'".$refusing_cause."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=sql_update("refusing_cause_history",$field_array,$data_array,"id",$idpre,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		//echo "10**".$rID.'='.$rID3.'='.$flag; die;
		
		if($db_type==0)
		{
			if( $flag==1)
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
			if($flag==1)
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

	function auto_approved(
		$dataArr=array()){
		global $pc_date_time;
		global $user_id;
		$sys_id_arr=explode(',',$dataArr[sys_id]);
		
		$queryText = "select a.id,a.SETUP_DATE,b.APPROVAL_NEED,b.ALLOW_PARTIAL,b.PAGE_ID from APPROVAL_SETUP_MST a,APPROVAL_SETUP_DTLS b where a.id=b.MST_ID and a.COMPANY_ID=$dataArr[company_id] and b.PAGE_ID=$dataArr[app_necessity_page_id] and a.STATUS_ACTIVE =1 and a.IS_DELETED=0  and b.STATUS_ACTIVE =1 and b.IS_DELETED=0 order by a.SETUP_DATE desc";
		$queryTextRes = sql_select($queryText);
		
		
		if($queryTextRes[0][ALLOW_PARTIAL]==1){
			$con = connect();
			
 			/*$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($dataArr[sys_id]) and entry_form=$dataArr[entry_form] group by mst_id","mst_id","approved_no");
			
			
			$id=return_next_id( "id","approval_history", 1 ) ;
			$seq_wise_user_arr=return_library_array( "select  USER_ID,SEQUENCE_NO  from electronic_approval_setup where PAGE_ID=$dataArr[page_id] and COMPANY_ID = $dataArr[company_id] AND SEQUENCE_NO > $dataArr[user_sequence] AND IS_DELETED = 0",'SEQUENCE_NO','USER_ID');
			
			$max_seq=max(array_keys($seq_wise_user_arr));
			
			foreach($seq_wise_user_arr as $user_sequence_no=>$user_id_approval){
				$current_approval_status=($max_seq==$user_sequence_no)?1:0;
				
				foreach($sys_id_arr as $booking_id){
					$approved_no=($max_approved_no_arr[$booking_id] == 1) ? 1 : $max_approved_no_arr[$booking_id]+1;
					
					$data_array.="(".$id.",$dataArr[entry_form],".$booking_id.",".$approved_no.",'".$user_sequence_no."',$current_approval_status,".$user_id_approval.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')";
					$id=$id+1;
				}
			
			} 
			
			 $query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=$dataArr[entry_form] and mst_id in ($dataArr[sys_id])";
			$rIDapp=execute_query($query,1);
			
			
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date,inserted_by,insert_date";
			$rID=sql_insert("approval_history",$field_array,$data_array,0);*/
			
		
			$query="UPDATE $dataArr[mst_table] SET approved=1,approved_by=$dataArr[approval_by],approved_date='$pc_date_time' WHERE id in ($dataArr[sys_id])";
			$rID1=execute_query($query,1);
			
			
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
	if($txt_alter_user_id!="") 	$user_id_approval=$txt_alter_user_id;
	else						$user_id_approval=$user_id;

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id_approval and is_deleted=0");
	//echo "select sequence_no from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id_approval and is_deleted=0";die;
	
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");

	$buyer_arr=return_library_array( "select b.id, a.buyer_name   from wo_pre_cost_mst b,  wo_po_details_master a   where  a.job_no=b.job_no and a.is_deleted=0 and  a.status_active=1 and b.status_active=1 and  b.is_deleted=0 and b.id in ($booking_ids)", "id", "buyer_name"  );
		
	if($approval_type==2)
	{
		if($db_type==2) {$buyer_id_cond=" and a.buyer_id is null "; $buyer_id_cond2=" and b.buyer_id is not null ";}
		else 			{$buyer_id_cond=" and a.buyer_id='' "; $buyer_id_cond2=" and b.buyer_id!='' ";}

		//echo "22**";
		$is_not_last_user=return_field_value("a.sequence_no","electronic_approval_setup a"," a.company_id=$cbo_company_name and a.page_id=$menu_id and a.sequence_no>$user_sequence_no and a.bypass=2 and a.is_deleted=0 $buyer_id_cond","sequence_no");

		//echo $is_not_last_user;die;

		$partial_approval = "";
		if($is_not_last_user == "")
		{
			//$credentialUserBuyersArr = [];
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
		// if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;

		$response=$booking_ids;
		$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date,inserted_by,insert_date";
		$id=return_next_id( "id","approval_history", 1 ) ;

		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=15 group by mst_id","mst_id","approved_no");
		//echo "10**select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=15 group by mst_id"; die;

		$approved_status_arr = return_library_array("select id, approved from wo_pre_cost_mst where id in($booking_ids)","id","approved");
		$approved_no_array=array();
		$booking_ids_all=explode(",",$booking_ids);
		$booking_nos_all=explode(",",$booking_nos);
		$book_nos=''; //echo "10**";
		//print_r($credentialUserBuyersArr);die;
		for($i=0; $i<count($booking_nos_all); $i++)
		{
			$val=$booking_nos_all[$i];
			$booking_id=$booking_ids_all[$i];

			//$approved_no=$max_approved_no_arr[$booking_id];
			$approved_no=($max_approved_no_arr[$booking_id] == '') ? 1 : $max_approved_no_arr[$booking_id];
			//$approved_no=$user_sequence_no;
			$approved_status=$approved_status_arr[$booking_id]*1;
			$buyer_id=$buyer_arr[$booking_id];

			if($approved_status==2 || $approved_status==0)
			{
				if($max_approved_no_arr[$booking_id]){$approved_no=$approved_no+1;}
				$approved_no_array[$val]=$approved_no;
				if($book_nos=="") $book_nos=$val; else $book_nos.=",".$val;
			}

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
				//echo "10**".count($credentialUserBuyersArr); die;
				if(count($credentialUserBuyersArr)>0)
				{
					if(in_array($buyer_id,$credentialUserBuyersArr))
					{
						$partial_approval=3;
					}
					else $partial_approval=1;
				}
				else $partial_approval=3;
				//$partial_approval=3;
			}
			//echo $partial_approval;die;
			$booking_id_arr[]=$booking_id;
			$data_array_booking_update[$booking_id]=explode("*",($partial_approval));

			if($partial_approval==1)
			{
				$full_approve_booking_id_arr[]=$booking_id;
				$data_array_full_approve_booking_update[$booking_id]=explode("*",($user_id_approval."*'".$pc_date_time."'"));
			}
			
			if(($user_sequence_no*1)==0) { echo "seq**".$user_sequence_no; disconnect($con);die; }
			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",15,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')";
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
					$approved_string.=" WHEN TO_CHAR($key) THEN '".$value."'";
					$approved_string2.=" WHEN TO_NCHAR($key) THEN '".$value."'";
				}
			}

			$approved_string_mst="CASE job_no ".$approved_string2." END";
			$approved_string_dtls="CASE job_no ".$approved_string2." END";
			//$approved_string_dtls2="CASE job_no ".$approved_string2." END";
			
			$approved_string_mst2="CASE job_no_mst ".$approved_string2." END";
			$approved_string_dtls2="CASE job_no_mst ".$approved_string2." END";
			
			//------------wo_po_dtls_mst_his----------------------------------
			$sqljob="insert into wo_po_dtls_mst_his (id, job_id, approved_no, approval_page, garments_nature, job_no_prefix, job_no_prefix_num, job_no, quotation_id, order_repeat_no, company_name, buyer_name, style_ref_no, product_dept, product_code, location_name, style_description, ship_mode, region, team_leader, dealing_marchant, remarks, job_quantity, avg_unit_price, currency_id, total_price, packing, agent_name, product_category, order_uom, gmts_item_id, set_break_down, total_set_qnty, set_smv, is_deleted, status_active, inserted_by, insert_date, updated_by, update_date, pro_sub_dep, client_id, item_number_id, factory_marchant, qlty_label, is_excel, style_owner, booking_meeting_date, bh_merchant, copy_from, season_buyer_wise, is_repeat, repeat_job_no, ready_for_budget, working_location_id, gauge, fabric_composition, design_source_id, yarn_quality, season_year, brand_id, inquiry_id, body_wash_color, sustainability_standard, fab_material, quality_level, requisition_no, working_company_id, fit_id)
				select '', id, $approved_string_mst, 15, garments_nature, job_no_prefix, job_no_prefix_num, job_no, quotation_id, order_repeat_no, company_name, buyer_name, style_ref_no, product_dept, product_code, location_name, style_description, ship_mode, region, team_leader, dealing_marchant, remarks, job_quantity, avg_unit_price, currency_id, total_price, packing, agent_name, product_category, order_uom, gmts_item_id, set_break_down, total_set_qnty, set_smv, is_deleted, status_active, inserted_by, insert_date, updated_by, update_date, pro_sub_dep, client_id, item_number_id, factory_marchant, qlty_label, is_excel, style_owner, booking_meeting_date, bh_merchant, copy_from, season_buyer_wise, is_repeat, repeat_job_no, ready_for_budget, working_location_id, gauge, fabric_composition, design_source_id, yarn_quality, season_year, brand_id, inquiry_id, body_wash_color, sustainability_standard, fab_material, quality_level, requisition_no, working_company_id, fit_id
			from wo_po_details_master where job_no in ($book_nos)";
			// echo "10**".$sqljob;die;
			
			//------------wo_po_dtls_item_set_his----------------------------------
			$sqlsetitem="insert into wo_po_dtls_item_set_his (id, approval_page, set_dtls_id, approved_no, job_no, gmts_item_id, set_item_ratio, smv_pcs, smv_set, smv_pcs_precost, smv_set_precost, complexity, embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, quot_id, aop, aopseq, ws_id, job_id, bush, bushseq, peach, peachseq, yd, ydseq, printdiff, embrodiff, washdiff, spwdiff)
				select '', 15, id, $approved_string_mst, job_no, gmts_item_id, set_item_ratio, smv_pcs, smv_set, smv_pcs_precost, smv_set_precost, complexity, embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, quot_id, aop, aopseq, ws_id, job_id, bush, bushseq, peach, peachseq, yd, ydseq, printdiff, embrodiff, washdiff, spwdiff from wo_po_details_mas_set_details where job_no in ($book_nos)";
			//echo "10**".$sqlsetitem;die;
			
			//------------wo_po_break_down_his----------------------------------
			$sqlpo="insert into wo_po_break_down_his (id, approval_page, po_id, approved_no, job_no_mst, po_number, pub_shipment_date, excess_cut, po_received_date, po_quantity, unit_price, plan_cut, country_name, po_total_price, shipment_date, is_deleted, is_confirmed, details_remarks, delay_for, packing, grouping, projected_po_id, tna_task_from_upto, inserted_by, insert_date, updated_by, update_date, status_active, shiping_status, original_po_qty, factory_received_date, original_avg_price, pp_meeting_date, matrix_type, no_of_carton, actual_po_no, round_type, doc_sheet_qty, up_charge, pack_price, sc_lc, with_qty, extended_ship_date, sewing_company_id, sewing_location_id, extend_ship_mode, sea_discount, air_discount, job_id, pack_handover_date, etd_ldd, file_year, file_no, rfi_date)
				select '', 15, id, $approved_string_mst2, job_no_mst, po_number, pub_shipment_date, excess_cut, po_received_date, po_quantity, unit_price, plan_cut, country_name, po_total_price, shipment_date, is_deleted, is_confirmed, details_remarks, delay_for, packing, grouping, projected_po_id, tna_task_from_upto, inserted_by, insert_date, updated_by, update_date, status_active, shiping_status, original_po_qty, factory_received_date, original_avg_price, pp_meeting_date, matrix_type, no_of_carton, actual_po_no, round_type, doc_sheet_qty, up_charge, pack_price, sc_lc, with_qty, extended_ship_date, sewing_company_id, sewing_location_id, extend_ship_mode, sea_discount, air_discount, job_id, pack_handover_date, txt_etd_ldd, file_year, file_no, rfi_date from wo_po_break_down where job_no_mst in ($book_nos) and is_deleted=0";
			//echo "10**".$sqlpo;die;
			
			//------------wo_po_color_size_his----------------------------------
			$sqlcolorsize="insert into wo_po_color_size_his (id, approval_page, color_size_id, approved_no, po_break_down_id, job_no_mst, color_mst_id, size_mst_id, item_mst_id, country_mst_id, article_number, item_number_id, country_id, size_number_id, color_number_id, order_quantity, order_rate, order_total, excess_cut_perc, plan_cut_qnty, is_deleted, is_used, inserted_by, insert_date, updated_by, update_date, status_active, is_locked, cutup_date, cutup, country_ship_date, shiping_status, country_remarks, country_type, packing, color_order, size_order, ultimate_country_id, code_id, ul_country_code, pack_qty, pcs_per_pack, pack_type, pcs_pack, assort_qty, solid_qty, barcode_suffix_no, barcode_year, barcode_no, job_id, extended_ship_date, proj_qty, proj_amt, country_avg_rate)
				Select '', 15, id, $approved_string_mst2, po_break_down_id, job_no_mst, color_mst_id, size_mst_id, item_mst_id, country_mst_id, article_number, item_number_id, country_id, size_number_id, color_number_id, order_quantity, order_rate, order_total, excess_cut_perc, plan_cut_qnty, is_deleted, is_used, inserted_by, insert_date, updated_by, update_date, status_active, is_locked, cutup_date, cutup, country_ship_date, shiping_status, country_remarks, country_type, packing, color_order, size_order, ultimate_country_id, code_id, ul_country_code, pack_qty, pcs_per_pack, pack_type, pcs_pack, assort_qty, solid_qty, barcode_suffix_no, barcode_year, barcode_no, job_id, extended_ship_date, proj_qty, proj_amt, country_avg_rate from wo_po_color_size_breakdown where job_no_mst in ($book_nos) and is_deleted=0 ";	
			//echo "10**".$sqlcolorsize;die;
			
			//------------wo_pre_cost_mst_histry----------------------------------
			$sqlBom="insert into wo_pre_cost_mst_histry(id, approved_no, pre_cost_mst_id, garments_nature, job_no, costing_date, incoterm, incoterm_place, machine_line, prod_line_hr, costing_per, remarks, copy_quatation, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, efficiency_wastage_percent, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, ready_to_approved, budget_minute, sew_efficiency_source, entry_from, job_id, refusing_cause, sourcing_date, sourcing_inserted_date, sourcing_inserted_by, sourcing_update_date, sourcing_updated_by, sourcing_ready_to_approved, sourcing_approved, sourcing_remark, main_fabric_co, sourcinng_refusing_cause, approved_sequ_by, isorder_change, ready_to_source, approval_page)
				select '', $approved_string_mst, id, garments_nature, job_no, costing_date, incoterm, incoterm_place, machine_line, prod_line_hr, costing_per, remarks, copy_quatation, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, efficiency_wastage_percent, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, ready_to_approved, budget_minute, sew_efficiency_source, entry_from, job_id, refusing_cause, sourcing_date, sourcing_inserted_date, sourcing_inserted_by, sourcing_update_date, sourcing_updated_by, sourcing_ready_to_approved, sourcing_approved, sourcing_remark, main_fabric_co, sourcinng_refusing_cause, approved_sequ_by, isorder_change, ready_to_source, 15
			from wo_pre_cost_mst where job_no in ($book_nos)";
			//echo "10**".$sqlBom;die;
			
			//------------wo_pre_cost_dtls_histry----------------------------------
			$sql_bom_dtls="insert into wo_pre_cost_dtls_histry(id, approved_no, pre_cost_dtls_id, job_no, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, commission, commission_percent,	lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, cost_pcs_set, cost_pcs_set_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche, margin_pcs_bom, margin_bom_per, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, depr_amor_pre_cost, depr_amor_po_price, interest_cost, interest_percent, incometax_cost, incometax_percent, deffdlc_cost, deffdlc_percent, design_cost, design_percent, studio_cost, studio_percent, job_id, sourcing_inserted_date, sourcing_inserted_by, sourcing_update_date, sourcing_updated_by, sourcing_fabric_cost, sourcing_trims_cost, sourcing_embel_cost, sourcing_wash_cost, approval_page)
					select '', $approved_string_dtls, id, job_no, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, commission, commission_percent,	lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, cost_pcs_set, cost_pcs_set_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche, margin_pcs_bom, margin_bom_per, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, depr_amor_pre_cost, depr_amor_po_price, interest_cost, interest_percent, incometax_cost, incometax_percent, deffdlc_cost, deffdlc_percent, design_cost, design_percent, studio_cost, studio_percent, job_id, sourcing_inserted_date, sourcing_inserted_by, sourcing_update_date, sourcing_updated_by, sourcing_fabric_cost, sourcing_trims_cost, sourcing_embel_cost, sourcing_wash_cost, 15 from wo_pre_cost_dtls where job_no in ($book_nos)";
			//echo "10**".$sql_bom_dtls;die;

			//------------wo_pre_cost_fabric_cost_dtls_h----------------------------------
			$sql_fabric_cost_dtls="insert into wo_pre_cost_fabric_cost_dtls_h(id, approved_no, pre_cost_fabric_cost_dtls_id, job_no, item_number_id, body_part_id, fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, color_size_sensitive, color, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, company_id, costing_per, consumption_basis, process_loss_method, cons_breack_down, msmnt_break_down, color_break_down, yarn_breack_down, marker_break_down, width_dia_type, avg_cons_yarn, gsm_weight_yarn, uom, body_part_type, sample_id, job_id, gsm_weight_type, nominated_supp_multi, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, sourcing_nominated_supp, quotdtlsid, budget_on, source_id, is_synchronized, approval_page)
				select '', $approved_string_dtls, id, job_no, item_number_id, body_part_id, fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, color_size_sensitive, color, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, company_id, costing_per, consumption_basis, process_loss_method, cons_breack_down, msmnt_break_down, color_break_down, yarn_breack_down, marker_break_down, width_dia_type, avg_cons_yarn, gsm_weight_yarn, uom, body_part_type, sample_id, job_id, gsm_weight_type, nominated_supp_multi, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, sourcing_nominated_supp, quotdtlsid, budget_on, source_id, is_synchronized, 15 from wo_pre_cost_fabric_cost_dtls where job_no in ($book_nos)";
			//echo "10**".$sql_fabric_cost_dtls;die;
			
			//------------WO_PRE_FAB_AVG_CON_DTLS_H----------------------------------
			$sql_fabric_cons_dtls="insert into wo_pre_fab_avg_con_dtls_h(id, approved_no, fab_con_id, pre_cost_fabric_cost_dtls_id, job_no, po_break_down_id, color_number_id, gmts_sizes, dia_width, item_size, cons, process_loss_percent, requirment, pcs, color_size_table_id, body_length, body_sewing_margin, body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin, front_rise_length, front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin, total, marker_dia, marker_yds, marker_inch, gmts_pcs, marker_length, net_fab_cons, rate, amount, remarks, length, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, length_sleeve, width_sleeve, job_id, fina_char, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, cons_pcs, item_color, approval_page)
				select '', $approved_string_dtls, id,  pre_cost_fabric_cost_dtls_id, job_no, po_break_down_id, color_number_id, gmts_sizes, dia_width, item_size, cons, process_loss_percent, requirment, pcs, color_size_table_id, body_length, body_sewing_margin, body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin, front_rise_length, front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin, total, marker_dia, marker_yds, marker_inch, gmts_pcs, marker_length, net_fab_cons, rate, amount, remarks, length, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, length_sleeve, width_sleeve, job_id, fina_char, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, cons_pcs, item_color, 15 from wo_pre_cos_fab_co_avg_con_dtls where job_no in ($book_nos)";
			//echo "10**"$sql_fabric_cons_dtls;die;
			
			//-------------wo_pre_fab_concolor_dtls_h-----------------------------------------------
			$sql_concolor_cst="insert into wo_pre_fab_concolor_dtls_h (id, approved_no, contrast_id, pre_cost_fabric_cost_dtls_id, job_no, gmts_color_id, gmts_color, contrast_color_id, job_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, approval_page)
				select
				'', $approved_string_dtls, id, pre_cost_fabric_cost_dtls_id, job_no, gmts_color_id, gmts_color, contrast_color_id, job_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, 15 from wo_pre_cos_fab_co_color_dtls where job_no in ($book_nos)";
			//echo "10**".$sql_concolor_cst;die;
			
			//-------------wo_pre_stripe_color_h-----------------------------------------------
			$sql_stripecolor_cst="insert into wo_pre_stripe_color_h (id, approved_no, stripe_id, job_no, item_number_id, pre_cost_fabric_cost_dtls_id, color_number_id, stripe_color, measurement, uom, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, totfidder, fabreq, fabreqtotkg, yarn_dyed, sales_dtls_id, size_number_id, po_break_down_id, lenth, width, sample_color, sample_per, cons, excess_per, job_id, stripe_type, approval_page)
				select
				'', $approved_string_dtls, id, job_no, item_number_id, pre_cost_fabric_cost_dtls_id, color_number_id, stripe_color, measurement, uom, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, totfidder, fabreq, fabreqtotkg, yarn_dyed, sales_dtls_id, size_number_id, po_break_down_id, lenth, width, sample_color, sample_per, cons, excess_per, job_id, stripe_type, 15 from wo_pre_stripe_color where job_no in ($book_nos)";
			//echo "10**".$sql_stripecolor_cst;die;

			//-------------wo_pre_cost_fab_yarn_cst_dtl_h-----------------------------------------------
			$sql_precost_fab_yarn_cst="insert into wo_pre_cost_fab_yarn_cst_dtl_h (id, approved_no, pre_cost_fab_yarn_cost_dtls_id, fabric_cost_dtls_id, job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, avg_cons_qnty, supplier_id, color, consdznlbs, rate_dzn, job_id, yarn_finish, yarn_spinning_system, certification, approval_page)
				select
				'', $approved_string_dtls, id, fabric_cost_dtls_id, job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, avg_cons_qnty, supplier_id, color, consdznlbs, rate_dzn, job_id, yarn_finish, yarn_spinning_system, certification, 15 from wo_pre_cost_fab_yarn_cost_dtls where job_no in ($book_nos)";
				//echo "10**".$sql_precost_fab_yarn_cst;die;
				
			//-----------------------------------------wo_pre_cost_fab_con_cst_dtls_h-----------------------------------------
			$sql_precost_fab_con_cst_dtls="insert into  wo_pre_cost_fab_con_cst_dtls_h(id, approved_no, pre_cost_fab_conv_cst_dtls_id, job_no, fabric_description, cons_process, req_qnty, charge_unit, amount, color_break_down, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, avg_req_qnty, process_loss, job_id, approval_page)
				select '', $approved_string_dtls, id, job_no, fabric_description, cons_process, req_qnty, charge_unit, amount, color_break_down, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, avg_req_qnty, process_loss, job_id, 15 from wo_pre_cost_fab_conv_cost_dtls where job_no in ($book_nos)";
			//echo "10**".$sql_precost_fab_con_cst_dtls;die;
				
			//-------------------  WO_PRE_CONV_COLOR_DTLS_H------------------------------------------------------------
			$sql_conv_color_dtls="insert into wo_pre_conv_color_dtls_h(id, approved_no, conv_color_id, fabric_cost_dtls_id, conv_cost_dtls_id, job_no, job_id, convchargelibraryid, gmts_color_id, fabric_color_id, cons, unit_charge, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, approval_page)
				select '', $approved_string_dtls, id, fabric_cost_dtls_id, conv_cost_dtls_id, job_no, job_id, convchargelibraryid, gmts_color_id, fabric_color_id, cons, unit_charge, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, 15 from wo_pre_cos_conv_color_dtls where job_no in ($book_nos)";
			//echo "10**".$sql_conv_color_dtls;die;
				
			//------------wo_pre_cost_trim_cost_dtls_his------------------------------	----------------------
			$sql_precost_trim_cost_dtls="insert into  wo_pre_cost_trim_cost_dtls_his(id, approved_no, pre_cost_trim_cost_dtls_id, job_no, trim_group, description, brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, cons_breack_down, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, remark, country, calculatorstring, unit_price, inco_term, add_price, seq, job_id, nominated_supp_multi, material_source, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, sourcing_nominated_supp, tot_cons, ex_per, quotdtlsid, source_id, item_print, is_synchronized, approval_page)
				select '', $approved_string_dtls, id, job_no, trim_group, description, brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, cons_breack_down, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, remark, country, calculatorstring, unit_price, inco_term, add_price, seq, job_id, nominated_supp_multi, material_source, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, sourcing_nominated_supp, tot_cons, ex_per, quotdtlsid, source_id, item_print, is_synchronized, 15 from wo_pre_cost_trim_cost_dtls  where job_no in ($book_nos)";
			//echo "10**".$sql_precost_trim_cost_dtls;die;

			//---------------------------wo_pre_cost_trim_co_cons_dtl_h--------------------------------------------------
			$sql_precost_trim_co_cons_dtl="insert into wo_pre_cost_trim_co_cons_dtl_h( id, approved_no, pre_cost_trim_co_cons_dtls_id, wo_pre_cost_trim_cost_dtls_id, job_no, po_break_down_id, item_size, cons, place, pcs, country_id, excess_per, tot_cons, ex_cons, item_number_id, color_number_id, item_color_number_id, size_number_id, rate, amount, gmts_pcs, color_size_table_id, job_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, rate_cal_data, sourcing_rate, sourcing_amount, sourcing_update_date, sourcing_updated_by, sourcing_inserted_date, sourcing_inserted_by, cons_pcs, approval_page)
				select '', $approved_string_dtls, id, wo_pre_cost_trim_cost_dtls_id, job_no, po_break_down_id, item_size, cons, place, pcs, country_id, excess_per, tot_cons, ex_cons, item_number_id, color_number_id, item_color_number_id, size_number_id, rate, amount, gmts_pcs, color_size_table_id, job_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, rate_cal_data, sourcing_rate, sourcing_amount, sourcing_update_date, sourcing_updated_by, sourcing_inserted_date, sourcing_inserted_by, cons_pcs, 15 from wo_pre_cost_trim_co_cons_dtls where job_no in ($book_nos)";
			//echo "10**".$sql_precost_trim_co_cons_dtl;die;

			//-------------------  wo_pre_cost_embe_cost_dtls_his------------------------------------------------------------
			$sql_precost_embe_cost_dtls="insert into wo_pre_cost_embe_cost_dtls_his(id, approved_no, pre_cost_embe_cost_dtls_id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, budget_on, country, body_part_id, job_id, nominated_supp_multi, sourcing_nominated_supp, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, quotdtlsid, is_synchronized, approval_page)
				select '', $approved_string_dtls, id, job_no, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, budget_on, country, body_part_id, job_id, nominated_supp_multi, sourcing_nominated_supp, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, quotdtlsid, is_synchronized, 15 from wo_pre_cost_embe_cost_dtls where job_no in ($book_nos)";
			//echo "10**".$sql_precost_embe_cost_dtls;die;
				
			//-------------------  WO_PRE_EMB_AVG_CON_DTLS_H------------------------------------------------------------
			$sql_embe_cons_dtls="insert into wo_pre_emb_avg_con_dtls_h(id, approved_no, emb_cons_id, pre_cost_emb_cost_dtls_id, job_no, po_break_down_id, item_number_id, color_number_id, size_number_id, requirment, rate, amount, gmts_pcs, color_size_table_id, rate_lib_id, country_id, job_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, approval_page)
				select '', $approved_string_dtls, id, pre_cost_emb_cost_dtls_id, job_no, po_break_down_id, item_number_id, color_number_id, size_number_id, requirment, rate, amount, gmts_pcs, color_size_table_id, rate_lib_id, country_id, job_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, sourcing_rate, sourcing_amount, sourcing_inserted_by, sourcing_inserted_date, sourcing_updated_by, sourcing_update_date, 15 from wo_pre_cos_emb_co_avg_con_dtls where job_no in ($book_nos)";
			//echo "10**".$sql_embe_cons_dtls;die;
			
			//----------------------------------wo_pre_cost_comarc_cost_dtls_h----------------------------------------
			$sql_comarc_cost_dtls="insert into wo_pre_cost_comarc_cost_dtls_h( id, approved_no, pre_cost_comarci_cost_dtls_id, job_no, item_id, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, job_id, approval_page)
				select '', $approved_string_dtls, id, job_no, item_id, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, job_id, 15 from wo_pre_cost_comarci_cost_dtls where job_no in ($book_nos)";
			//echo "10**".$sql_comarc_cost_dtls;die;

			//-------------------------------------wo_pre_cost_commis_cost_dtls_h-------------------------------------------
			$sql_commis_cost_dtls="insert into wo_pre_cost_commis_cost_dtls_h (id, approved_no, pre_cost_commiss_cost_dtls_id, job_no, particulars_id, commission_base_id, commision_rate, commission_amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, job_id, approval_page)
				select '', $approved_string_dtls, id, job_no, particulars_id, commission_base_id, commision_rate, commission_amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, job_id, 15 from wo_pre_cost_commiss_cost_dtls where job_no in ($book_nos)";
			//echo "10**".$sql_commis_cost_dtls;die;
			
			//-------------------------------------wo_pre_cost_sum_dtls_histroy-------------------------------------------
			$sql_sum_dtls="insert into wo_pre_cost_sum_dtls_histroy (id, approved_no, pre_cost_sum_dtls_id, job_no, fab_yarn_req_kg, fab_woven_req_yds, fab_knit_req_kg, fab_amount, avg, yarn_cons_qnty, yarn_amount, conv_req_qnty, conv_charge_unit, conv_amount, trim_cons, trim_rate, trim_amount, emb_amount, comar_rate, comar_amount, commis_rate, commis_amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, fab_woven_fin_req_yds, fab_knit_fin_req_kg, job_id, approval_page)
				select '', $approved_string_dtls, id, job_no, fab_yarn_req_kg, fab_woven_req_yds, fab_knit_req_kg, fab_amount, avg, yarn_cons_qnty, yarn_amount, conv_req_qnty, conv_charge_unit, conv_amount, trim_cons, trim_rate, trim_amount, emb_amount, comar_rate, comar_amount, commis_rate, commis_amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, fab_woven_fin_req_yds, fab_knit_fin_req_kg, job_id, 15 from wo_pre_cost_sum_dtls where job_no in ($book_nos)";
			//echo "10**".$sql_sum_dtls;die;
			
			if(count($sqljob)>0)//JOB
			{
				$rID=execute_query($sqljob,1);
				// echo "1111**".$rID;die;
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

		//$rID2=1;
		if(count($full_approve_booking_id_arr)>0)
		{

			$field_array_full_approved_booking_update = "approved_by*approved_date";
			$rID19=execute_query(bulk_update_sql_statement( "wo_pre_cost_mst", "id", $field_array_full_approved_booking_update, $data_array_full_approve_booking_update, $full_approve_booking_id_arr));
			if($flag==1)
			{
				if($rID19) $flag=1; else $flag=0;
			}
			//sql_multirow_update("wo_pre_cost_mst","approved_by*approved_date",$updateData,"id",$booking_ids,1);
		}

		$field_array_booking_update = "approved";
		$rID20=execute_query(bulk_update_sql_statement( "wo_pre_cost_mst", "id", $field_array_booking_update, $data_array_booking_update, $booking_id_arr));

		if($flag==1)
		{
			if($rID20) $flag=1; else $flag=0;
		}


		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=15 and mst_id in ($booking_ids)";
		$rIDapp=execute_query($query,0);
		if($flag==1)
		{
			if($rIDapp) $flag=1; else $flag=0;
		}

		$rID21=sql_insert("approval_history",$field_array,$data_array,0);
		if($flag==1)
		{
			if($rID21) $flag=1; else $flag=0;
		}
		
		if($flag==1) $msg='19'; else $msg='21';
		//-------------auto approve if partial allow is yes----------------
		if($flag==1)
		{
			auto_approved(array(company_id=>$cbo_company_name,app_necessity_page_id=>25,mst_table=>'wo_pre_cost_mst',sys_id=>$booking_ids,approval_by=>$user_id_approval));//,user_sequence=>$user_sequence_no,entry_form=>15,page_id=>$menu_id
		}
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


		$rID=sql_multirow_update("wo_pre_cost_mst","approved*ready_to_approved",'2*0',"id",$booking_ids,0);
		if($rID) $flag=1; else $flag=0;
		//echo "22**".$rID;die;
		//$rID2=sql_multirow_update2("approval_history","current_approval_status",0,"entry_form*mst_id",15*$booking_ids,0);
		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=15 and mst_id in ($booking_ids)";
		$rID2=execute_query($query,0);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}

		$unapproved_status="UPDATE fabric_booking_approval_cause SET status_active=0,is_deleted=1 WHERE entry_form=15 and approval_type=2 and is_deleted=0 and status_active=1 and booking_id in ($booking_ids)";
		//echo $unapproved_status;die;
		$rIDunapp=execute_query($unapproved_status,0);
		if($flag==1)
		{
			if($rIDunapp) $flag=1; else $flag=0;
		}

		$data=$user_id_approval."*'".$pc_date_time."'*".$user_id."*'".$pc_date_time."'";
		$rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date*updated_by*update_date",$data,"id",$approval_ids,0);
		if($flag==1)
		{
			if($rID3) $flag=1; else $flag=0;
		}

		$response=$booking_ids;

		if($flag==1) $msg='20'; else $msg='22';
	}
	else if($approval_type==5)
	{
		$sqlBookinghistory="select id, mst_id from approval_history where current_approval_status=1 and entry_form=15 and mst_id in ($booking_ids) ";
		
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
		
		$rID=sql_multirow_update("wo_pre_cost_mst","approved*ready_to_approved",'2*0',"id",$booking_ids,0);
		if($rID) $flag=1; else $flag=0;

		//$rID2=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
		if($approval_ids!="")
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=15 and current_approval_status=1 and id in ($approval_ids)";
			//echo "10**".$query;
			$rID2=execute_query($query,0);
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

if ($action=="unappcause_popup")
{
	echo load_html_head_contents("Un Approval Request", "../../", 1, 1,'','','');
	extract($_REQUEST);
	//print_r($_REQUEST);
	$wo_id=$data;
	$sql_req="select approval_cause,approval_no from fabric_booking_approval_cause where entry_form=15 and booking_id='$wo_id' and approval_type=2 and status_active=1 and is_deleted=0 order by approval_no ";
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
			document.getElementById('hidden_unappv_request').value=unappv_request.trim();
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
                    	<textarea name="unappv_req" id="unappv_req" class="text_area" style="width:430px; height:100px;" readonly>
                    		<?=$unappv_req;?>
                    	</textarea>
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
			$sqlHis="select approval_cause from approval_cause_refusing_his where entry_form=15 and booking_id='$wo_id' and approval_type=2 and status_active=1 and is_deleted=0 order by id Desc";
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

if($action=="budgetsheet")
{
	///extract($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$txt_costing_date=change_date_format(str_replace("'","",$txt_costing_date),'yyyy-mm-dd','-');
	if($txt_job_no=="") $job_no=''; else $job_no=" and a.job_no='".$txt_job_no."'";
	if($cbo_company_name=="") $company_name=''; else $company_name=" and a.company_name=".$cbo_company_name."";
	if($cbo_buyer_name=="") $cbo_buyer_name=''; else $cbo_buyer_name=" and a.buyer_name=".$cbo_buyer_name."";
	if($txt_style_ref=="") $txt_style_ref=''; else $txt_style_ref=" and a.style_ref_no='".$txt_style_ref."'";
	if($txt_costing_date=="") $txt_costing_date=''; else $txt_costing_date=" and c.costing_date='".$txt_costing_date."'";
	$txt_po_breack_down_id=str_replace("'",'',$txt_po_breack_down_id);
	if(str_replace("'",'',$txt_po_breack_down_id)=="") 
	{
		$txt_po_breack_down_id_cond='';  $txt_po_breack_down_id_cond1='';  $txt_po_breack_down_id_cond2='';  $txt_po_breack_down_id_cond3=''; 
	}
	else
	{
		$txt_po_breack_down_id_cond=" and b.id in(".$txt_po_breack_down_id.")";
		$txt_po_breack_down_id_cond1=" and po_id in(".$txt_po_breack_down_id.")";
		$txt_po_breack_down_id_cond2=" and po_break_down_id in(".$txt_po_breack_down_id.")";
		$txt_po_breack_down_id_cond3=" and b.id in(".$txt_po_breack_down_id.")";
	}
  
	//array for display name
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$sesson_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$fabric_composition_arr=return_library_array( "select id, fabric_composition_name from lib_fabric_composition",'id','fabric_composition_name');
	//$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	
	$photo_data_array = sql_select("SELECT id,master_tble_id,image_location from common_photo_library where master_tble_id='$txt_job_no' and file_type=1  and rownum=1");
	
	if($db_type==0) $group_gsm="group_concat( distinct b.gsm_weight) AS gsm_weight";
	if($db_type==2) $group_gsm="listagg(b.gsm_weight ,',') within group (order by b.gsm_weight) AS gsm_weight";
  
	$gsm_weight_top=return_field_value("$group_gsm", "lib_body_part a,wo_pre_cost_fabric_cost_dtls_h b", "a.id=b.body_part_id and b.job_no='$txt_job_no' and b.status_active=1 and b.is_deleted=0 and a.body_part_type in(1,20) and b.approved_no=$revised_no and b.approval_page=15","gsm_weight");
	//$gsm_weight_bottom=return_field_value("$group_gsm", "lib_body_part a,wo_pre_cost_fabric_cost_dtls b", "a.id=b.body_part_id and b.job_no=$txt_job_no and a.body_part_type=20 ","gsm_weight");
	//echo $gsm_weight_bottom.'DD';
	$gmtsitem_ratio_array=array(); $grmnt_items = "";
	$grmts_sql = sql_select("select job_no, gmts_item_id, set_item_ratio from wo_po_dtls_item_set_his where job_no='$txt_job_no' and approved_no=$revised_no and approval_page=15");
	//echo "select job_no, gmts_item_id, set_item_ratio from wo_po_dtls_item_set_his where job_no='$txt_job_no' and approved_no=$revised_no and approval_page=15";
	foreach($grmts_sql as $key=>$val)
	{
		$grmnt_items .=$garments_item[$val[csf("gmts_item_id")]].",";
		$gmtsitem_ratio_array[$val[csf('job_no')]][$val[csf('gmts_item_id')]]=$val[csf('set_item_ratio')];
		$set_item_ratio += $val[csf('set_item_ratio')]; 
	}
	$grmnt_items = rtrim($grmnt_items,","); 
  
  $sql = "SELECT a.job_id, a.job_no, a.company_name, a.buyer_name, a.ship_mode, a.total_set_qnty, a.style_ref_no, a.gmts_item_id, a.order_uom, a.avg_unit_price, a.product_dept, a.season_buyer_wise, a.brand_id, a.style_description, a.job_quantity as job_qty, sum(b.plan_cut) as job_quantity, sum(b.po_quantity) as ord_qty, listagg(cast(b.sc_lc as varchar2(4000)),',') within group (order by b.sc_lc) as sc_lc, c.costing_per, c.costing_date, c.budget_minute, c.approved, a.quotation_id, c.exchange_rate, c.incoterm, c.sew_effi_percent, c.remarks, c.sew_smv, '' as refusing_cause, d.fab_knit_fin_req_kg, d.fab_knit_req_kg, d.fab_woven_req_yds, d.fab_woven_fin_req_yds, d.fab_yarn_req_kg
    from wo_po_dtls_mst_his a, wo_po_break_down_his b, wo_pre_cost_mst_histry c left join wo_pre_cost_sum_dtls_histroy d on  c.job_no=d.job_no and d.status_active=1 and d.is_deleted=0 and d.approved_no=$revised_no and d.approved_no=$revised_no and d.approval_page=15
    where a.job_no=b.job_no_mst and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.approved_no =$revised_no and c.approved_no = $revised_no 
	and a.approved_no=b.approved_no and b.approved_no=c.approved_no and a.approval_page=15
	and a.approval_page=b.approval_page and b.approval_page=c.approval_page
	
	
	$job_no $txt_po_breack_down_id_cond $company_name $cbo_buyer_name 
	group by a.job_id, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.gmts_item_id, a.order_uom, a.ship_mode, a.avg_unit_price, a.product_dept, c.incoterm, c.costing_date, c.exchange_rate, a.quotation_id, c.costing_per, c.sew_effi_percent, c.approved, c.budget_minute, d.fab_knit_req_kg, d.fab_woven_req_yds, d.fab_knit_fin_req_kg, d.fab_woven_fin_req_yds, d.fab_yarn_req_kg, a.job_quantity, a.season_buyer_wise, a.brand_id, a.total_set_qnty, a.style_description, c.remarks, c.sew_smv  order by a.job_no"; //a.job_quantity as job_quantity,
	
 //echo $sql;die;
  $data_array=sql_select($sql);
  $plan_cut_qty=$data_array[0][csf('job_quantity')];
  $total_set_qnty=$data_array[0][csf('total_set_qnty')];
  $exchange_rate=$data_array[0][csf('exchange_rate')];
  $sew_effi_percent=$data_array[0][csf('sew_effi_percent')];
  $sew_smv=$preCost_histry_row[csf('sew_smv')];
  
  $is_approved=return_field_value("approved", "wo_pre_cost_mst", "job_no='$txt_job_no' and  status_active=1 and is_deleted=0"); 
  
	$preCost_histry=sql_select( "SELECT b.margin_dzn_percent as MARGIN_DZN_PERCENT, b.fabric_cost_percent as FABRIC_COST_PERCENT, b.trims_cost_percent as TRIMS_COST_PERCENT, b.embel_cost_percent as EMBEL_COST_PERCENT, b.wash_cost_percent as WASH_COST_PERCENT, b.comm_cost_percent as COMM_COST_PERCENT, b.commission_percent as COMMISSION_PERCENT, b.lab_test_percent as LAB_TEST_PERCENT, b.inspection_percent as INSPECTION_PERCENT, b.cm_cost_percent as CM_COST_PERCENT, b.freight_percent as FREIGHT_PERCENT, b.currier_percent as CURRIER_PERCENT, b.certificate_percent as CERTIFICATE_PERCENT, b.common_oh_percent as COMMON_OH_PERCENT from wo_pre_cost_dtls_histry b where b.job_no='$txt_job_no' and b.approved_no=$revised_no"); 
	
	list($preCost_histry_row)=$preCost_histry;
	$opert_profitloss_percent=$preCost_histry_row[csf('margin_dzn_percent')];
	$fabric_cost_percent=$preCost_histry_row[csf('fabric_cost_percent')];
	$trims_cost_percent=$preCost_histry_row[csf('trims_cost_percent')];
	$embel_cost_percent=$preCost_histry_row[csf('embel_cost_percent')];
	$wash_cost_percent=$preCost_histry_row[csf('wash_cost_percent')];
	$comm_cost_percent=$preCost_histry_row[csf('comm_cost_percent')];
	$commission_percent=$preCost_histry_row[csf('commission_percent')];
	$common_oh_percent=$preCost_histry_row[csf('common_oh_percent')];
	
	$lab_test_percent=$preCost_histry_row[csf('lab_test_percent')];
	$inspection_percent=$preCost_histry_row[csf('inspection_percent')];
	$cm_cost_percent=$preCost_histry_row[csf('cm_cost_percent')];
	$freight_percent=$preCost_histry_row[csf('freight_percent')];
	$currier_percent=$preCost_histry_row[csf('currier_percent')];
	$certificate_percent=$preCost_histry_row[csf('certificate_percent')];
	//$currier_percent=$preCost_histry_row[csf('currier_percent')];
	
	$hissew_effi_percent=$preCost_histry_row[csf('sew_effi_percent')];
	//
	$first_app_date=""; $last_app_date="";
	$preCost_approved=sql_select( "select max(b.approved_no) as approved_no, min(b.approved_date) as first_app_date, max(b.approved_date) as last_app_date,a.id from wo_pre_cost_mst a, approval_history b where a.id=b.mst_id and a.job_no='$txt_job_no' and b.entry_form=15 group by a.id"); 
	//echo  "select max(b.approved_no) as approved_no, min(b.approved_date) as first_app_date, max(b.approved_date) as last_app_date,a.id from wo_pre_cost_mst a, approval_history b where   a.id=b.mst_id and a.job_no=$txt_job_no and b.entry_form=15 group by a.id";
	//echo  "select max(b.approved_no) as approved_no, min(b.approved_date) as first_app_date, max(b.approved_date) as last_app_date,a.id from wo_pre_cost_mst a, approval_history b where b.un_approved_by>0 and  a.id=b.mst_id and a.job_no=$txt_job_no and b.entry_form=15 group by a.id";
	if(count($preCost_approved)>0)
	{
		foreach($preCost_approved as $preCost_approved_row)
		{
			$approved_no_row=$preCost_approved_row[csf('approved_no')];
			$fst_date=$preCost_approved_row[csf('first_app_date')];
			$fstapp_date=$fst_date[0];
			
			$last_date=$preCost_approved_row[csf('last_app_date')];
			$lstapp_date=$last_date[0];
			$precost_id=$preCost_approved_row[csf('id')];
		}
	}
  
	$img_path = (str_replace("'", "", $img_path))? str_replace("'", "", $img_path):'../../';
	//echo $img_path;
	$costing_date=$data_array[0][csf('costing_date')];
	if(is_infinite($costing_date) || is_nan($costing_date)){$costing_date=0;}
	
	$approval_allow=sql_select("select b.id, b.page_id, b.approval_need, b.allow_partial, b.validate_page,a.setup_date from approval_setup_mst a, approval_setup_dtls b 
	where a.id=b.mst_id and a.company_id=$cbo_company_name and a.status_active=1 and b.page_id=15 and b.status_active=1 and b.is_deleted=0 order by b.id desc ");
	$appMsg="";
	if( $is_approved==1) 
	{
		$appMsg="This Budget is Approved.";
		$appcolor="color: green;";
	}
	else if( $is_approved==3)
	{
		if($approval_allow[0][csf("approval_need")]==1 && $approval_allow[0][csf("allow_partial")]==1){
			$appMsg="This Budget is Approved.";
			$appcolor="color: green;";
		}
		else{
			$appMsg="This Budget is Partially Approved.";
			$appcolor="color: green;";
		}
	}
	else
	{
		$appMsg="This Budget is Not Approved.";
		$appcolor="color: red;";
	}
	
	?>
	<div style="width:972px; margin:0 auto; font-family: 'Arial Narrow', Arial, sans-serif;">
        <div style="width:970px; font-size:20px; font-weight:bold">
            <b style="float: left"><?=$comp[str_replace("'","",$cbo_company_name)]; ?><br>Budget Sheet</b>
				<b style="left: 50%; margin-left: 240px; <?=$appcolor; ?>"><?=$appMsg; ?></b>
            <b style="float:right;"><?='Budget Date: ';?><?=date('d-M-y',strtotime($costing_date)); ?> <br><?='Revised No:'.$revised_no; ?>  </b>
        </div>
	
        <div style="width:970px; font-size:18px; font-weight:bold">
            <b style="float: left"></b>
            <b style="float:right; font-size:18px; font-weight:bold">   &nbsp;  </b>
        </div>
        <?
		
		$sqlpo="select a.job_id as JOB_ID, a.approved_no AS APPROVEDNO, a.job_no AS JOB_NO, b.po_id AS POID, b.po_number as PO_NUMBER, b.po_received_date as PO_RECEIVED_DATE, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, c.country_ship_date AS COUNTRY_SHIP_DATE, c.article_number AS ARTICLE_NUMBER, d.costing_per_id AS COSTING_PER from wo_po_dtls_mst_his a, wo_po_break_down_his b, wo_po_color_size_his c, wo_pre_cost_dtls_histry d where a.job_id=b.job_id and b.po_id=c.po_break_down_id and a.job_id=d.job_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and a.approved_no=$revised_no and b.approved_no=$revised_no and c.approved_no=$revised_no and d.approved_no=$revised_no and a.job_no='".$txt_job_no."' order by b.po_received_date DESC";
		//echo $sqlpo; die; //and a.job_no='$job_no'
		$sqlpoRes = sql_select($sqlpo);
		//print_r($sqlpoRes); die;
		$po_arr=array(); $poCountryArr=array(); $reqQtyAmtArr=array(); $costingPerArr=array(); $jobid=""; $jobQtyArr=array();
		foreach($sqlpoRes as $row)
		{
			$costingPerQty=0;
			if($row['COSTING_PER']==1) $costingPerQty=12;
			elseif($row['COSTING_PER']==2) $costingPerQty=1;	
			elseif($row['COSTING_PER']==3) $costingPerQty=24;
			elseif($row['COSTING_PER']==4) $costingPerQty=36;
			elseif($row['COSTING_PER']==5) $costingPerQty=48;
			else $costingPerQty=0;
			
			$costingPerArr[$row['JOB_ID']]=$costingPerQty;
			$jobDataArr[$row['JOB_ID']]['plan']+=$row['PLAN_CUT_QNTY'];
			$jobDataArr[$row['JOB_ID']]['poqty']+=$row['ORDER_QUANTITY'];
			$poArr['pono'][$row['POID']]=$row['PO_NUMBER'];
			$poArr['porecdate'][$row['POID']]=$row['PO_RECEIVED_DATE'];
			$poArr['poshipdate'][$row['POID']]=$row['PO_RECEIVED_DATE'];
			
			
			$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
			$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
			
			$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'].=$row['COUNTRY_ID'].',';
			
			$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty']+=$row['ORDER_QUANTITY'];
			$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty']+=$row['PLAN_CUT_QNTY'];
			
			$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['poqty']+=$row['ORDER_QUANTITY'];
			$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['planqty']+=$row['PLAN_CUT_QNTY'];
			if($jobid=="") $jobid=$row['JOB_ID']; else $jobid.=','.$row['JOB_ID'];
		}
		unset($sqlpoRes);
		$ujobid=array_unique(explode(",",$jobid));
		$cjobid=count($ujobid);
		$jobIds=implode(",",$ujobid);
		$jobidCond=''; $jobidCondition='';
		if($db_type==2 && $cjobid>1000)
		{
			$jobidCond=" and (";
			$jobidCondition=" and (";
			$jobIdsArr=array_chunk(explode(",",$jobIds),999);
			foreach($jobIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$jobidCond.=" a.job_id in($ids) or"; 
				$jobidCondition.=" job_id in($ids) or"; 
			}
			$jobidCond=chop($jobidCond,'or ');
			$jobidCond.=")";
			
			$jobidCondition=chop($jobidCondition,'or ');
			$jobidCondition.=")";
		}
		else
		{
			if($jobIds==""){ $jobidCond=""; } else { $jobidCond=" and a.job_id in($jobIds)"; }
			if($jobIds==""){ $jobidCondition=""; } else { $jobidCondition=" and job_id in($jobIds)"; }
		}
		
		$pre_cost_dtls = "SELECT pre_cost_dtls_id as dtls_id, job_id as job_id, job_no, costing_per_id as costing_per, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, currier_pre_cost, certificate_pre_cost, design_cost, studio_cost, depr_amor_pre_cost, interest_cost, incometax_cost, deffdlc_cost, comm_cost, comm_cost_percent, commission, commission_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche from wo_pre_cost_dtls_histry where job_no='".$txt_job_no."' and status_active=1 and is_deleted=0 and approved_no=$revised_no"; 
		$pre_cost_dtls_arr=sql_select($pre_cost_dtls);
		foreach ($pre_cost_dtls_arr as $row) {
			if($row[csf("costing_per")]==1){$order_price_per_dzn=12;$costing_for="1 DZN";}
			else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;$costing_for="1 PCS";}
			else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;$costing_for="2 DZN";}
			else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;$costing_for="3 DZN";}
			else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;$costing_for="4 DZN";}
			else {$order_price_per_dzn=0; $costing_for="DZN";}
			$job_id=$row[csf("job_id")];
			$planqty=$jobDataArr[$job_id]['plan'];
			$poQty=$jobDataArr[$job_id]['poqty'];
			
			if( ($row[csf("lab_test")]*1)!=0) $labAmt=($row[csf("lab_test")]/$order_price_per_dzn)*$poQty;
			if( ($row[csf("currier_pre_cost")]*1)!=0) $currierAmt=($row[csf("currier_pre_cost")]/$order_price_per_dzn)*$poQty;
			if( ($row[csf("inspection")]*1)!=0) $inspectionAmt=($row[csf("inspection")]/$order_price_per_dzn)*$poQty;
			if( ($row[csf("commission")]*1)!=0) $commissionAmt=($row[csf("commission")]/$order_price_per_dzn)*$poQty;
			if( ($row[csf("comm_cost")]*1)!=0) $commlAmt=($row[csf("comm_cost")]/$order_price_per_dzn)*$poQty;
			if( ($row[csf("freight")]*1)!=0) $freightAmt=($row[csf("freight")]/$order_price_per_dzn)*$poQty;
			if( ($row[csf("certificate_pre_cost")]*1)!=0) $certificateAmt=($row[csf("certificate_pre_cost")]/$order_price_per_dzn)*$poQty;
			if( ($row[csf("deffdlc_cost")]*1)!=0) $deffdlcAmt=($row[csf("deffdlc_cost")]/$order_price_per_dzn)*$poQty;
			if( ($row[csf("design_cost")]*1)!=0) $designAmt=($row[csf("design_cost")]/$order_price_per_dzn)*$poQty;
			if( ($row[csf("studio_cost")]*1)!=0) $studioAmt=($row[csf("studio_cost")]/$order_price_per_dzn)*$poQty;
			if( ($row[csf("depr_amor_pre_cost")]*1)!=0) $deprAmt=($row[csf("depr_amor_pre_cost")]/$order_price_per_dzn)*$poQty;
			if( ($row[csf("common_oh")]*1)!=0) $commonOhAmt=($row[csf("common_oh")]/$order_price_per_dzn)*$poQty;
			if( ($row[csf("interest_cost")]*1)!=0) $interestAmt=($row[csf("interest_cost")]/$order_price_per_dzn)*$poQty;
			if( ($row[csf("incometax_cost")]*1)!=0) $incometaxAmt=($row[csf("incometax_cost")]/$order_price_per_dzn)*$poQty;
			
			if( ($row[csf("cm_cost")]*1)!=0) $cmAmt=($row[csf("cm_cost")]/$order_price_per_dzn)*$poQty;
			
			$other_costing_arr[$job_id]['comm_cost']=$commlAmt;
			$other_costing_arr[$job_id]['commission']=$commissionAmt;
			$other_costing_arr[$job_id]['inspection']=$inspectionAmt;
			$other_costing_arr[$job_id]['freight']=$freightAmt;
			$other_costing_arr[$job_id]['certificate_pre_cost']=$certificateAmt;
			$other_costing_arr[$job_id]['deffdlc_cost']=$deffdlcAmt;
			$other_costing_arr[$job_id]['design_cost']=$designAmt;
			$other_costing_arr[$job_id]['studio_cost']=$studioAmt;
			$other_costing_arr[$job_id]['common_oh']=$commonOhAmt;
			$other_costing_arr[$job_id]['interest_cost']=$interestAmt;
			$other_costing_arr[$job_id]['incometax_cost']=$incometaxAmt;
			$other_costing_arr[$job_id]['depr_amor_pre_cost']=$deprAmt;
			$other_costing_arr[$job_id]['cm_cost']=$cmAmt;
			$other_costing_arr[$job_id]['lab_test']=$labAmt;
			
			
			$total_cost = $row[csf("total_cost")];
			$price_dzn = $row[csf("price_dzn")];
		}
		
		$gmtsitemRatioSql="select approved_no as APPROVENO, job_id AS JOB_ID, gmts_item_id AS GMTS_ITEM_ID, set_item_ratio AS SET_ITEM_RATIO, smv_pcs as SMV_PCS from wo_po_dtls_item_set_his where 1=1 and approved_no=$revised_no $jobCondS $jobidCondition";
		//echo $gmtsitemRatioSql; die;
		$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
		$jobItemRatioArr=array();
		foreach($gmtsitemRatioSqlRes as $row)
		{
			$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
			$jobDataArr[$row['JOB_ID']]['smv']=$row['SMV_PCS'];
		}
		unset($gmtsitemRatioSqlRes);
		
		//Contrast Details
		$sqlContrast="select a.approved_no as APPROVENO, a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.gmts_color_id as COLOR_NUMBER_ID, a.contrast_color_id AS CONTRAST_COLOR_ID from wo_pre_fab_concolor_dtls_h a where 1=1 and a.approved_no=$revised_no and a.status_active=1 and a.is_deleted=0 $jobCond $jobidCond";
		//echo $sqlContrast; die;
		$sqlContrastRes = sql_select($sqlContrast);
		$sqlContrastArr=array();
		foreach($sqlContrastRes as $row)
		{
			$sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]=$row['CONTRAST_COLOR_ID'];
		}
		unset($sqlContrastRes);
		
		//Stripe Details
		$sqlStripe="select a.approved_no as APPROVENO, a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.item_number_id AS ITEM_NUMBER_ID, a.color_number_id as COLOR_NUMBER_ID, a.stripe_color as STRIPE_COLOR, a.size_number_id as SIZE_NUMBER_ID, a.fabreq as FABREQ, a.yarn_dyed as YARN_DYED from wo_pre_stripe_color_h a where 1=1 and a.status_active=1 and a.is_deleted=0 and a.approved_no=$revised_no $jobCond $jobidCond";
		//echo $sqlStripe; die;
		$sqlStripeRes = sql_select($sqlStripe);
		$sqlStripeArr=array();
		foreach($sqlStripeRes as $row)
		{
			$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'][$row['STRIPE_COLOR']]=$row['STRIPE_COLOR'];
			$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['fabreq'][$row['STRIPE_COLOR']]=$row['FABREQ'];
		}
		unset($sqlStripeRes);
		
		
		//Fabric Details
		$sqlfab="select a.approved_no as APPROVENO, a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id AS FABID, a.item_number_id AS ITEM_NUMBER_ID, a.fab_nature_id AS FAB_NATURE_ID, a.color_type_id AS COLOR_TYPE_ID, a.fabric_source as FABRIC_SOURCE, a.color_size_sensitive AS COLOR_SIZE_SENSITIVE, a.construction AS CONSTRUCTION, a.fabric_description as FABRIC_DESCRIPTION, a.gsm_weight AS GSM_WEIGHT, a.uom AS UOM, a.budget_on as BUDGET_ON, b.po_break_down_id AS POID, b.color_number_id AS COLOR_NUMBER_ID, b.gmts_sizes AS SIZE_NUMBER_ID, b.cons AS CONS, b.requirment AS REQUIRMENT, b.rate as RATE, b.amount AS AMOUNT
		from wo_pre_cost_fabric_cost_dtls_h a, wo_pre_fab_avg_con_dtls_h b
		where 1=1 and a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and b.cons!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.approved_no=b.approved_no and a.approved_no=$revised_no $jobCond $jobidCond";
		//echo $sqlfab; die;
		$sqlfabRes = sql_select($sqlfab);
		$fabIdWiseGmtsDataArr=array();
		foreach($sqlfabRes as $row)
		{
			$poQty=$planQty=$costingPer=$itemRatio=$finReq=$greyReq=$finAmt=$greyAmt=0;
			
			$fabIdWiseGmtsDataArr[$row['FABID']]['item']=$row['ITEM_NUMBER_ID'];
			$fabIdWiseGmtsDataArr[$row['FABID']]['fnature']=$row['FAB_NATURE_ID'];
			$fabIdWiseGmtsDataArr[$row['FABID']]['sensitive']=$row['COLOR_SIZE_SENSITIVE'];
			$fabIdWiseGmtsDataArr[$row['FABID']]['color_type']=$row['COLOR_TYPE_ID'];
			$fabIdWiseGmtsDataArr[$row['FABID']]['uom']=$row['UOM'];
			$fabIdWiseGmtsDataArr[$row['FABID']]['budget_on']=$row['BUDGET_ON'];
			
			$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
			$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
			$costingPer=$costingPerArr[$row['JOB_ID']];
			$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
			if($row['BUDGET_ON']==1) $poPlanQty=$poQty; else $poPlanQty=$planQty;
			
			$finReq=($poPlanQty/$itemRatio)*($row['CONS']/$costingPer);
			$greyReq=($poPlanQty/$itemRatio)*($row['REQUIRMENT']/$costingPer);
			
			$finAmt=$finReq*$row['RATE'];
			$greyAmt=$greyReq*$row['RATE'];
			
			//echo $planQty.'='.$itemRatio.'='.$row['CONS'].'='.$row['REQUIRMENT'].'='.$costingPer.'='.$finReq.'='.$greyReq.'<br>';
			$fabQtyAmtArr[$row['JOB_ID']]['fabric']=$row['FABRIC_DESCRIPTION'];
			$fabQtyAmtArr[$row['JOB_ID']]['uom']=$row['UOM'];
			
			$fabQtyAmtArr[$row['JOB_ID']]['qty']+=$greyReq;
			$fabQtyAmtArr[$row['JOB_ID']]['amt']+=$greyAmt;
			$fabQtyAmtArr[$row['JOB_ID']]['dzn']=$row['AMOUNT'];
			$fabQtyAmtArr[$row['JOB_ID']]['rate']=$row['RATE'];
			
			if($row['FABRIC_SOURCE']==2)
			{
				$fabQtyAmtArr[$row['JOB_ID']]['purqty']+=$greyReq;
				$fabQtyAmtArr[$row['JOB_ID']]['puramt']+=$greyAmt;	
			}
			else
			{
				$fabQtyAmtArr[$row['JOB_ID']]['prodqty']+=$greyReq;
				$fabQtyAmtArr[$row['JOB_ID']]['prodamt']+=$greyAmt;
			}
			
			if($row['FAB_NATURE_ID']==2)
			{
				$fabric_qty_arr['knit']['finish'][$row['FABID']][$row['UOM']]+=$finReq;
				$fabric_qty_arr['knit']['grey'][$row['FABID']][$row['UOM']]+=$greyReq;
				$fabric_amount_arr['knit']['grey'][$row['FABID']][$row['UOM']]+=$greyAmt;
			}
			if($row['FAB_NATURE_ID']==3)
			{
				$fabric_qty_arr['woven']['finish'][$row['FABID']][$row['UOM']]+=$finReq;
				$fabric_qty_arr['woven']['grey'][$row['FABID']][$row['UOM']]+=$greyReq;
				$fabric_amount_arr['woven']['grey'][$row['FABID']][$row['UOM']]+=$greyAmt;
			}
		}
		unset($sqlfabRes);
		//print_r($fabQtyAmtArr[27617]['puramt']); die; 
		
		//Yarn Details
		$sqlYarn="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.cons AS CONS, a.requirment AS REQUIRMENT, b.pre_cost_fab_yarn_cost_dtls_id AS YARN_ID, b.count_id AS COUNT_ID, b.copm_one_id AS COPM_ONE_ID, b.percent_one AS PERCENT_ONE, b.type_id AS TYPE_ID, b.color AS COLOR, b.cons_ratio AS CONS_RATIO, b.cons_qnty AS CONS_QNTY, b.avg_cons_qnty AS AVG_CONS_QNTY, b.rate AS RATE, b.amount AS AMOUNT 
		
		from wo_pre_fab_avg_con_dtls_h a, wo_pre_cost_fab_yarn_cst_dtl_h b where 1=1 and a.job_id=b.job_id and a.pre_cost_fabric_cost_dtls_id=b.fabric_cost_dtls_id and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.approved_no=b.approved_no and a.approved_no=$revised_no $jobCond $jobidCond";
		//echo $sqlYarn;
		$sqlYarnRes = sql_select($sqlYarn);
		foreach($sqlYarnRes as $row)
		{
			$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$yarnReq=$yarnAmt=0;
			
			$gmtsItem=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['item'];
			
			$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
			$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
			$costingPer=$costingPerArr[$row['JOB_ID']];
			$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$gmtsItem];
			
			$consQnty=($row['REQUIRMENT']*$row['CONS_RATIO'])/100;
			
			$yarnReq=($planQty/$itemRatio)*($consQnty/$costingPer);
			
			$yarnAmt=$yarnReq*$row['RATE'];
			
			//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
			$yarnQtyAmtArr[$row['JOB_ID']]['yarn_qty']+=$yarnReq;
			$yarnQtyAmtArr[$row['JOB_ID']]['yarn_amt']+=$yarnAmt;
			$yarnDataWithFabricidArr[$row['PRECOSTID']]['amount']+=$yarnAmt;
			$yarnDataWithFabricidArr[$row['PRECOSTID']]['qty']+=$yarnReq;
			
			$yarn_data_array[$row['COUNT_ID']][$row['COPM_ONE_ID']][$row['PERCENT_ONE']][$row['TYPE_ID']][$row['COLOR']][$row['RATE']]['qty']+=$yarnReq;
        	$yarn_data_array[$row['COUNT_ID']][$row['COPM_ONE_ID']][$row['PERCENT_ONE']][$row['TYPE_ID']][$row['COLOR']][$row['RATE']]['amount']+=$yarnAmt;
		}
		unset($sqlYarnRes); 
		//print_r($reqQtyAmtArr); die;
		
		//Convaersion Details
		$sqlConv="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id AS PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.dia_width AS DIA_WIDTH, a.cons AS CONS, a.requirment AS REQUIRMENT, b.pre_cost_fab_conv_cst_dtls_id AS CONVERTION_ID, b.cons_process AS CONS_PROCESS, b.req_qnty AS REQ_QNTY, b.process_loss AS PROCESS_LOSS, b.avg_req_qnty AS AVG_REQ_QNTY, b.charge_unit AS CHARGE_UNIT, b.amount as AMOUNT, b.color_break_down AS COLOR_BREAK_DOWN
		from wo_pre_fab_avg_con_dtls_h a, wo_pre_cost_fab_con_cst_dtls_h b where 1=1 and a.pre_cost_fabric_cost_dtls_id=b.fabric_description and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.approved_no=b.approved_no and a.approved_no=$revised_no $jobCond $jobidCond";
		//echo $sqlConv; die;
		$sqlConvRes = sql_select($sqlConv);
		$convConsRateArr=array();
		foreach($sqlConvRes as $row)
		{
			$id=$row['CONVERTION_ID'];
			$colorBreakDown=$row['COLOR_BREAK_DOWN'];
			if($colorBreakDown !="")
			{
				$arr_1=explode("__",$colorBreakDown);
				for($ci=0;$ci<count($arr_1);$ci++)
				{
					$arr_2=explode("_",$arr_1[$ci]);
					$convConsRateArr[$id][$arr_2[0]][$arr_2[3]]['rate']=$arr_2[1];
					$convConsRateArr[$id][$arr_2[0]][$arr_2[3]]['cons']=$arr_2[4];
				}
			}
		}
		//echo "ff"; die;
		foreach($sqlConvRes as $row)
		{
			$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$reqqnty=$convAmt=0;
			$gmtsItem=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['item'];
			
			$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
			$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
			
			$costingPer=$costingPerArr[$row['JOB_ID']];
			$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$gmtsItem];
			
			$colorTypeId=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['color_type']; 
			$colorSizeSensitive=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['sensitive'];
			$budget_on=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['budget_on'];
			if($budget_on==1) $poPlanQty=$poQty; else $poPlanQty=$planQty;
			
			$consProcessId=$row['CONS_PROCESS'];
			$stripe_color=$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'];
			
			if(($colorTypeId==2 || $colorTypeId==3 || $colorTypeId==4 || $colorTypeId==6 || $colorTypeId==31 || $colorTypeId==32 || $colorTypeId==33 || $colorTypeId==34) && $consProcessId==30 && count($stripe_color)>0)
			{
				$qnty=0; $convrate=0;
				foreach($stripe_color as $stripe_color_id)
				{
					$stripe_color_cons_dzn=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$stripe_color_id]['cons'];
					$convrate=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$stripe_color_id]['rate'];
					
					$requirment=$stripe_color_cons_dzn-($stripe_color_cons_dzn*$row['PROCESS_LOSS'])/100;
					$qnty=($poPlanQty/$itemRatio)*($requirment/$costingPer);
					//echo $convrate.'=';
					if($convrate>0){
						$reqqnty+=$qnty;
						$convAmt+=$qnty*$convrate;
					}
				}
			}
			else
			{
				$convrate=$requirment=$reqqnty=0;
				$rateColorId=$row['COLOR_NUMBER_ID'];
				if($colorSizeSensitive==3) $rateColorId=$sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]; else $rateColorId=$row['COLOR_NUMBER_ID'];
		
				if($row['COLOR_BREAK_DOWN']!="")
				{
					$convDtlsRate=$convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$rateColorId]['rate']; 
					if($convDtlsRate>0) $convrate=$convDtlsRate; else $convrate=$row['CHARGE_UNIT']; 
				}else $convrate=$row['CHARGE_UNIT']; 
				
				//echo $row['CHARGE_UNIT'].'='.$row['CONVERTION_ID'].'=';
				if($convrate>0){
					$requirment=$row['REQUIRMENT']-($row['REQUIRMENT']*($row['PROCESS_LOSS']*1))/100;
					$qnty=($poPlanQty/$itemRatio)*($row['REQUIRMENT']/$costingPer);
					$reqqnty+=$qnty;
					$convAmt+=$qnty*$convrate;
				}
			}
			
			//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
			$convQtyAmtArr[$row['JOB_ID']]['conv_qty'][$consProcessId]+=$reqqnty;
			$convQtyAmtArr[$row['JOB_ID']]['conv_amt'][$consProcessId]+=$convAmt;
			
			$con_amount_fabric_process[$row['PRECOSTID']][$consProcessId]['conv_amt']+=$convAmt;
        	$con_qty_fabric_process[$row['PRECOSTID']][$consProcessId]['conv_qty']+=$reqqnty;
		}
		unset($sqlConvRes);
		//echo "kauar"; 
		//print_r($convQtyAmtArr); die;
		
		//Trims Details
		$sqlTrim="select a.job_id AS JOB_ID, a.pre_cost_trim_cost_dtls_id AS TRIMID, a.trim_group AS TRIM_GROUP, a.description AS DESCRIPTION, a.cons_uom AS CONS_UOM, a.cons_dzn_gmts CONS_DZN_GMTS, a.rate AS RATEMST, a.amount AS AMOUNT, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.cons AS CONS, b.tot_cons AS TOT_CONS, b.rate AS RATE, b.country_id AS COUNTRY_ID_TRIMS, b.color_size_table_id as COLOR_SIZE_ID
		from wo_pre_cost_trim_cost_dtls_his a, wo_pre_cost_trim_co_cons_dtl_h b
		where 1=1 and a.pre_cost_trim_cost_dtls_id=b.wo_pre_cost_trim_cost_dtls_id and b.cons>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.approved_no=b.approved_no and a.approved_no=$revised_no and b.approved_no=$revised_no $jobCond $jobidCond";
		//echo $sqlTrim; die;
		$sqlTrimRes = sql_select($sqlTrim);
		
		foreach($sqlTrimRes as $row)
		{
			$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
			
			$costingPer=$costingPerArr[$row['JOB_ID']];
			$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
			
			$poCountryId=array_filter(array_unique(explode(",",$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'])));
			//print_r($poCountryId);
			
			if($row['COUNTRY_ID_TRIMS']=="" || $row['COUNTRY_ID_TRIMS']==0)
			{
				$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
				$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
				
				$consQnty=($poQty/$itemRatio)*($row['CONS']/$costingPer);
				$consTotQnty=($poQty/$itemRatio)*($row['TOT_CONS']/$costingPer);
				
				$consAmt=$consQnty*$row['RATE'];
				$consTotAmt=$consTotQnty*$row['RATE'];
			}
			else
			{
				$countryIdArr=explode(",",$row['COUNTRY_ID_TRIMS']);
				$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
				foreach($poCountryId as $countryId)
				{
					if(in_array($countryId, $countryIdArr))
					{
						$poQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
						$planQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
						$consQty=$consTotQty=0;
						
						$consQty=($poQty/$itemRatio)*($row['CONS']/$costingPer);
						$consTotQty=($poQty/$itemRatio)*($row['TOT_CONS']/$costingPer);
						
						$consQnty+=$consQty;
						$consTotQnty+=$consTotQty;
						//echo $poQty.'-'.$itemRatio.'-'.$row['CONS'].'-'.$costingPer.'<br>';
						$consAmt+=$consQty*$row['RATE'];
						$consTotAmt+=$consTotQty*$row['RATE'];
					}
				}
			}
			
			//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
			//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['trimqty']+=$consQnty;
			$trimQtyAmtArr[$row['JOB_ID']]['trimtotqty']+=$consQnty;
			
			//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['trimamt']+=$consAmt;
			$trimQtyAmtArr[$row['JOB_ID']]['trimtotamt']+=$consAmt;
			$trim_qty_arr[$row['TRIMID']]+=$consQnty;
			$trim_amount_arr[$row['TRIMID']]+=$consAmt;
		}
		unset($sqlTrimRes); 
		//print_r($reqQtyAmtArr); die;
		
		$sqlEmb="select a.job_id AS JOB_ID, a.pre_cost_embe_cost_dtls_id AS EMB_ID, a.emb_name AS EMB_NAME, a.emb_type AS EMB_TYPE, a.cons_dzn_gmts AS CONS_DZN_GMTS_MST, a.rate AS RATE_MST, a.amount AS AMOUNT_MST, a.budget_on AS BUDGET_ON, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.requirment AS CONS_DZN_GMTS, b.rate AS RATE, b.amount AS AMOUNT, b.country_id AS COUNTRY_ID_EMB 
	from wo_pre_cost_embe_cost_dtls_his a, wo_pre_emb_avg_con_dtls_h b 
	where 1=1 and a.cons_dzn_gmts>0 and b.requirment>0 and
	a.job_id=b.job_id and a.pre_cost_embe_cost_dtls_id=b.pre_cost_emb_cost_dtls_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.approved_no=b.approved_no and a.approved_no=$revised_no and a.approval_page=15 $jobCond $jobidCond";
		//echo $sqlEmb; die;
		$sqlEmbRes = sql_select($sqlEmb);
		
		foreach($sqlEmbRes as $row)
		{
			$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
			
			$costingPer=$costingPerArr[$row['JOB_ID']];
			$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
			$budget_on=$row['BUDGET_ON'];
			
			$poCountryId=array_filter(array_unique(explode(",",$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'])));
			//print_r($poCountryId);
			$calPoPlanQty=0;
			
			if($row['COUNTRY_ID_EMB']=="" || $row['COUNTRY_ID_EMB']==0)
			{
				$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
				$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
				
				if($budget_on==1) $calPoPlanQty=$poQty; else $calPoPlanQty=$planQty;
				$consQty=0;
				$consQty=($calPoPlanQty/$itemRatio)*($row['CONS_DZN_GMTS']/$costingPer);
				$consQnty+=$consQty;
				
				$consAmt=$consQty*$row['RATE'];
			}
			else
			{
				$countryIdArr=explode(",",$row['COUNTRY_ID_EMB']);
				$consQnty=$consAmt=0;
				foreach($poCountryId as $countryId)
				{
					if(in_array($countryId, $countryIdArr))
					{
						$poQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
						$planQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
						
						if($budget_on==1) $calPoPlanQty=$poQty; else $calPoPlanQty=$planQty;
						$consQty=0;
						$consQty=($calPoPlanQty/$itemRatio)*($row['CONS_DZN_GMTS']/$costingPer);
						$consQnty+=$consQty;
						//echo $poQty.'-'.$itemRatio.'-'.$row['CONS_DZN_GMTS'].'-'.$costingPer.'<br>';
						$consAmt+=$consQty*$row['RATE'];
					}
				}
			}
			
			//echo $planQty.'='.$itemRatio.'='.$row['CONS_DZN_GMTS'].'='.$costingPer.'='.$consQty.'='.$consAmt.'<br>';
			$embQtyAmtArr[$row['JOB_ID']][$row['EMB_NAME']]['qty']+=$consQnty;
			$embQtyAmtArr[$row['JOB_ID']][$row['EMB_NAME']]['amt']+=$consAmt;
			/*if($row['EMB_NAME']==1)
			{
				$reqQtyAmtArr[$row['JOB_ID']]['print_qty']+=$consQnty;
				$reqQtyAmtArr[$row['JOB_ID']]['print_amt']+=$consAmt;
			}
			else if($row['EMB_NAME']==2)
			{
				$reqQtyAmtArr[$row['JOB_ID']]['embqty']+=$consQnty;
				$reqQtyAmtArr[$row['JOB_ID']]['embamt']+=$consAmt;
			}
			else if($row['EMB_NAME']==3)
			{
				$reqQtyAmtArr[$row['JOB_ID']]['washqty']+=$consQnty;
				$reqQtyAmtArr[$row['JOB_ID']]['washamt']+=$consAmt;
			}
			else if($row['EMB_NAME']==4)
			{
				$reqQtyAmtArr[$row['JOB_ID']]['special_works_qty']+=$consQnty;
				$reqQtyAmtArr[$row['JOB_ID']]['special_works_amt']+=$consAmt;
			}
			else if($row['EMB_NAME']==5)
			{
				$reqQtyAmtArr[$row['JOB_ID']]['gmts_dyeing_qty']+=$consQnty;
				$reqQtyAmtArr[$row['JOB_ID']]['gmts_dyeing_amt']+=$consAmt;
			}
			else
			{
				//$row['EMB_NAME']==99;
				$reqQtyAmtArr[$row['JOB_ID']]['others_qty']+=$consQnty;
				$reqQtyAmtArr[$row['JOB_ID']]['others_amt']+=$consAmt;
			}*/
		}
		unset($sqlEmbRes); 
		//echo "<pre>";
		//print_r($reqQtyAmtArr); die;
		
		$result =sql_select("select po_id as id, po_number, pub_shipment_date, file_no, excess_cut, grouping, po_received_date, plan_cut from wo_po_break_down_his where job_no_mst='$txt_job_no' $txt_po_breack_down_id_cond1 and status_active=1 and is_deleted=0 and approved_no=$revised_no and approval_page=15 order by po_received_date DESC");
		
		$job_in_orders = ''; $public_ship_date=''; $job_in_ref = ''; $job_in_file = '';
		$tot_excess_cut=0;$tot_row=0;
		foreach ($result as $val)
		{
			$job_in_orders .= $val[csf('po_number')].", ";
			$public_ship_date = $val[csf('pub_shipment_date')];
			$po_received_date = $val[csf('po_received_date')];
			$txt_order_no_arr[$val[csf('id')]] = $val[csf('id')];
			if($val[csf('excess_cut')]>0)
			{
				$tot_row++; 
			}
			$tot_excess_cut+= $val[csf('excess_cut')];
			$plancutqty +=$val[csf('plan_cut')];
		}
		$txt_order_no_id=implode(",", $txt_order_no_arr);
  $total_other_cost = 0;
  foreach ($data_array as $row)
  { 
    $order_price_per_dzn=0;
    $order_job_qnty=0;
    $ord_qty=0;
    $avg_unit_price=0;
    $uom=$row[csf("order_uom")]; 
    $sew_smv=$row[csf("sew_smv")]; 
    $order_values = $row[csf("job_qty")]*$row[csf("avg_unit_price")];   
  
    $job_in_orders = substr(trim($job_in_orders),0,-1);
    if($row[csf("costing_per")]==1){$order_price_per_dzn=12;$costing_for="1 DZN";}
    else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;$costing_for="1 PCS";}
    else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;$costing_for="2 DZN";}
    else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;$costing_for="3 DZN";}
    else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;$costing_for="4 DZN";}
    else {$order_price_per_dzn=0; $costing_for="DZN";}
    $order_job_qnty=$row[csf("job_qty")];
    //$order_qty = $row[csf("job_qty")]*$set_item_ratio;
    $po_no=str_replace("'","",$txt_po_breack_down_id);
    /*$condition= new condition();
    if(str_replace("'","",$txt_job_no) !=''){
        $condition->job_no("='$txt_job_no'");
     }
     
      if(str_replace("'","",$txt_po_breack_down_id)!='')
     {
      $condition->po_id("in($po_no)"); 
     }
    $condition->init();   
    $fabric= new fabric($condition);
    $yarn= new yarn($condition);
    $yarn_costing_arr=$yarn->getJobWiseYarnAmountArray();
    $yarn_qty_amount_arr=$yarn->getJobWiseYarnQtyAndAmountArray();

    $yarnDataWithFabricidArr=$yarn->get_By_Precostfabricdtlsid_YarnQtyAmountArray();

    $fabric= new fabric($condition);
    $fabricAmoutByFabricSource= $fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
    $fabricQtyByFabricSource= $fabric->getQtyArray_by_job_knitAndwoven_greyAndfinish_purchase();
    
    $fabric_qty_arr=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
    $fabric_amount_arr=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
    $conversion= new conversion($condition);
    $conversion_costing_arr_process=$conversion->getAmountArray_by_job();
    $conv_qty_job_process= $conversion->getQtyArray_by_jobAndProcess();
    $conv_amount_job_process= $conversion->getAmountArray_by_jobAndProcess();
    $con_qty_fabric_process = $conversion->getQtyArray_by_fabricAndProcess();
    $con_amount_fabric_process = $conversion->getAmountArray_by_fabricAndProcess();

    $trims= new trims($condition);
    $trims_costing_arr=$trims->getAmountArray_by_job();
    $trims_qty_arr=$trims->getQtyArray_by_job();

    $emblishment= new emblishment($condition);
    $emblishment_costing_arr=$emblishment->getAmountArray_by_job();
    $emb_qty_job_name_arr = $emblishment->getQtyArray_by_jobAndEmbname();
    $emb_amount_job_name_arr = $emblishment->getAmountArray_by_jobAndEmbname();

    $wash= new wash($condition);
    $emblishment_costing_arr_wash=$wash->getAmountArray_by_job();
    $wash_qty_job_name_arr =$wash->getQtyArray_by_jobAndEmbname();
    $wash_amount_job_name_arr =$wash->getAmountArray_by_jobAndEmbname();


    $commercial= new commercial($condition);
    $commercial_costing_arr=$commercial->getAmountArray_by_job();
    $commission= new commision($condition);
    $commission_costing_arr=$commission->getAmountArray_by_job();
    $other= new other($condition);
    $other_costing_arr=$other->getAmountArray_by_job();*/
    /*echo '<pre>';
    print_r($fabric_amount_arr); die;*/
	
    $job_id= $row[csf("job_id")];
    $finishing_arr = array('209','165','33','94','63','171','65','170','156','179','200','208','127','125','84','68','128','190','242','240','192','172','90','218','67','197','73','66','185','142','193');
	
    $total_finishing_amount=0; $total_finishing_qty=0;
	
    $other_cost_attr = array('inspection','freight','certificate_pre_cost','deffdlc_cost','design_cost','studio_cost','common_oh','interest_cost','incometax_cost','depr_amor_pre_cost');
	
    foreach ($other_cost_attr as $attr) {
      $total_other_cost+=$other_costing_arr[$job_id][$attr];
    }
    $misc_cost=$other_costing_arr[$job_id]['lab_test']+$other_costing_arr[$job_id]['comm_cost']+$other_costing_arr[$job_id]['commission']+$total_other_cost;

    foreach ($finishing_arr as $fid) {
      $total_finishing_amount +=$convQtyAmtArr[$job_id]['conv_amt'][$fid];
      $total_finishing_qty += $convQtyAmtArr[$job_id]['conv_qty'][$fid];
	  //echo $convQtyAmtArr[$job_id]['conv_amt'][$fid].'='.$fid.'<br>';
    }

    $total_fabic_cost=0;
    if(count($convQtyAmtArr[$job_id]['conv_qty'][31])>0){
      $total_fabic_cost+=$convQtyAmtArr[$job_id]['conv_amt'][31]/$convQtyAmtArr[$job_id]['conv_qty'][31];
    }
    $total_fabric_amount +=$convQtyAmtArr[$job_id]['conv_amt'][31];
    $total_fabric_per +=$convQtyAmtArr[$job_id]['conv_amt'][31]/$order_values*100;
    if(count($convQtyAmtArr[$job_id]['conv_amt'][30])>0){
      $total_fabic_cost+=$convQtyAmtArr[$job_id]['conv_amt'][30]/$convQtyAmtArr[$job_id]['conv_qty'][30];
    }
    if($yarnQtyAmtArr[$job_id]['yarn_amt']!=''){
      $total_fabic_cost+=$yarnQtyAmtArr[$job_id]['yarn_amt']/$yarnQtyAmtArr[$job_id]['yarn_qty'];
    }
    $total_fabric_amount +=$yarnQtyAmtArr[$job_id]['yarn_amt']; 
    $total_fabric_per +=$yarnQtyAmtArr[$job_id]['yarn_amt']/$order_values*100;
    if($total_finishing_amount!=0){
      $total_fabic_cost+=$total_finishing_amount/$total_finishing_qty;
    } 
    $total_fabric_amount +=$total_finishing_amount;
    $total_fabric_per +=$total_finishing_amount/$order_values*100;
    $total_fabric_amount +=$convQtyAmtArr[$job_id]['conv_amt'][30];
    $total_fabric_per +=$convQtyAmtArr[$job_id]['conv_amt'][30]/$order_values*100;
    if($convQtyAmtArr[$job_id]['conv_amt'][35]>0){
      $total_fabic_cost+=$convQtyAmtArr[$job_id]['conv_amt'][35]/$convQtyAmtArr[$job_id]['conv_qty'][30];
    }
    $total_fabric_amount +=$convQtyAmtArr[$job_id]['conv_amt'][35]; 
    $total_fabric_per +=$convQtyAmtArr[$job_id]['conv_amt'][35]/$order_values*100;
    if($convQtyAmtArr[$job_id]['conv_amt'][1]>0){
      $total_fabic_cost+=$convQtyAmtArr[$job_id]['conv_amt'][1]/$convQtyAmtArr[$job_id]['conv_qty'][1];
    }
    $total_fabric_amount +=$convQtyAmtArr[$job_id]['conv_amt'][1];
    $total_fabric_per +=$convQtyAmtArr[$job_id]['conv_amt'][1]/$order_values*100; 

    $purchase_amount = $fabQtyAmtArr[$job_id]['puramt'];
    $purchase_qty = $fabQtyAmtArr[$job_id]['purqty'];

    $ather_emb_attr = array(4,5,6,99);
    foreach ($ather_emb_attr as $att) {
      $others_emb_amount += $embQtyAmtArr[$job_id][$att]['amt'];
      $others_emb_qty += $embQtyAmtArr[$job_id][$att]['qty'];
    }
    $knitting_amount_summ=''; $dyeing_amount_summ=''; $yds_amount_summ=''; $aop_amount_summ='';
    if($convQtyAmtArr[$job_id]['conv_amt'][1]>0) {
      $knitting_amount_summ = fn_number_format($convQtyAmtArr[$job_id]['conv_amt'][1],2);
    }
    $yarn_amount_summ = $yarnQtyAmtArr[$job_id]['yarn_amt'];
    $print_amount_summ =$embQtyAmtArr[$job_id][1]['amt'];    
    $emb_amount_summ= $embQtyAmtArr[$job_id][2]['amt'];
    $wash_amount_summ = $embQtyAmtArr[$job_id][3]['amt'];
    if(count($convQtyAmtArr[$job_id]['conv_amt'][31])>0) {
      $dyeing_amount_summ=  $convQtyAmtArr[$job_id]['conv_amt'][31];
    }
    if(count($convQtyAmtArr[$job_id]['conv_amt'][30])>0) {
      $yds_amount_summ =$convQtyAmtArr[$job_id]['conv_amt'][30];
    }
    if(count($convQtyAmtArr[$job_id]['conv_amt'][35])>0) {
      $aop_amount_summ = $convQtyAmtArr[$job_id]['conv_amt'][35];
    }
    
    $total_budget_value = $yarn_amount_summ+$total_finishing_amount+$print_amount_summ+$trimQtyAmtArr[$job_id]['trimtotamt']+$yds_amount_summ+$aop_amount_summ+$emb_amount_summ+$knitting_amount_summ+$purchase_amount+$wash_amount_summ+$other_costing_arr[$job_id]['cm_cost']+$dyeing_amount_summ+$others_emb_amount+$misc_cost;
	//echo $total_budget_value; die;
    ?>
      <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px; font-family: 'Arial Narrow', Arial, sans-serif;" rules="all">
          <tr>
              <th rowspan="7">
              <? foreach($photo_data_array as $inf){ ?>
              <img  src='<?=$img_path; ?><? echo $inf[csf("image_location")]; ?>' height='100px' width='100px' />
              <? } ?>
              </th>
              <th style="background: #D7ECD9">Job No</th>
                <th><?=$row[csf("job_no")]; ?></th>
                <th style="background: #D7ECD9">OR. Rcv Date</th>
                <th><?=date('d-M-y',strtotime($po_received_date)); ?></th>
                <th style="background: #D7ECD9">Order Quantity</th>
                <th style="background: yellow; color: #8B0000;">Price/Pcs</th>
                <th align="right" style="background: yellow; color: #8B0000;">&#36; <?=$row[csf("avg_unit_price")]; ?> </th>
            </tr>
            <tr>                      
                <th style="background: #D7ECD9">Buyer</th>
                <th><?=$buyer_arr[$row[csf("buyer_name")]]; ?></th>
                <th style="background: #D7ECD9">Ship. Date</th>
                <th><?=date('d-M-y',strtotime($public_ship_date)); ?></th>
              	<th align="center" style="color: #8B0000"><?=$row[csf("job_qty")];?> <?=$unit_of_measurement[$row[csf("order_uom")]]; ?></th>
                <th style="background: yellow; color: #8B0000;">Order Value</th>                      
                <th align="right" style="background: yellow; color: #8B0000;">&#36; <?=number_format($order_values,2);  ?></th>
            </tr>
            <tr>
              <th style="background: #D7ECD9">Prod. Dept</th>
                <th><?=$product_dept[$row[csf("product_dept")]]; ?></th>
                <th style="background: #D7ECD9">Garments Item</th>
              <th> 
				<?
                $grmnt_items = "";
                if($garments_item[$row[csf("gmts_item_id")]]=="")
                {
					$grmts_sql = sql_select("select job_no, gmts_item_id, set_item_ratio from wo_po_dtls_item_set_his where job_no='$txt_job_no'");
						foreach($grmts_sql as $key=>$val){
							$grmnt_items .=$garments_item[$val[csf("gmts_item_id")]].", ";
							$gmts_item[]=$val[csf("gmts_item_id")];
						}
						$grmnt_items = substr_replace($grmnt_items,"",-1,1);
					}else{
						$gmts_item=explode(',',$row[csf("gmts_item_id")]);
						$grmnt_items = $garments_item[$row[csf("gmts_item_id")]];
                }
                echo $grmnt_items;
                ?>
        	</th>
              <th align="center" style="color: #8B0000"><?= $row[csf("job_qty")]*$set_item_ratio.' Pcs' ?></th>
                <th style="background: yellow; color: #8B0000;"> <? if($zero_value==0) echo "Budget Value"; ?></th>                      
                <th align="right" style="background: yellow; color: #8B0000;"><? if($zero_value==0){ ?>
                <? if($total_budget_value>0){ echo '&dollar;'.fn_number_format($total_budget_value,2); } ?><br/>
                <? if($total_budget_value>0){ echo fn_number_format($total_budget_value/$order_values*100,2).'%'; } ?>
                <? } ?>
                </th>
            </tr>
            <tr>
              <th style="background: #D7ECD9">Season / Brand</th>
                <th><?=$sesson_arr[$row[csf("season_buyer_wise")]].'&nbsp'.$brand_arr[$row[csf("brand_id")]]; ?></th>
                <th>Costing Per: <br><?= $costing_for;  ?></th>
                <th style="background: #D7ECD9">Plan Cut Quantity (<?=$tot_excess_cut.'%' ?>) </td>
              <th align="center" style="color: #8B0000"><?= $row[csf("job_quantity")]*$total_set_qnty.' Pcs';//." ". $unit_of_measurement[$row[csf("order_uom")]]; ?></th>
                <th rowspan="2" style="background: yellow; color: #8B0000;"><? if($zero_value==0) echo "Open Value %"; ?></th>                      
                <th rowspan="2" align="right" style="background: yellow; color: #8B0000;"><? if($zero_value==0) { ?> &#36;<? 
                  $margin_val = $order_values-$total_budget_value; 
                  echo fn_number_format($margin_val,2).'<br>'.fn_number_format($margin_val/$order_values*100,2).'%';
                  }
                 ?></th>
            </tr>
            <tr>
              <th style="background: #D7ECD9">Style No</th>
                <th><? $style_no= $row[csf("style_ref_no")]; echo $row[csf("style_ref_no")]; ?></th>
                <th style="background: #D7ECD9">App. Status</th>
                <th colspan="2"><?=$appMsg; ?></th>
            </tr>
            <tr>
              <th rowspan="2" style="background: #D7ECD9">Style Description</th>
                <th rowspan="2" colspan="2"><? echo $row[csf("style_description")]; ?></th>
                <th style="background: #D7ECD9">Remarks</th>
                <th colspan="3"><? echo $row[csf("remarks")]; ?></th>
            </tr>
            <tr>
              <th style="background: #D7ECD9">Refusing Cause</th>
                <th colspan="3"><? echo $row[csf("refusing_cause")]; ?></th>
            </tr>
        </table>

            <?        
      $avg_unit_price=$row[csf("avg_unit_price")];
      $ord_qty=$row[csf("ord_qty")];
  }//end first foearch
  /*echo '<pre>';
  print_r($conv_amount_job_process); die;*/
  
  $yarnPer=$yarnQtyAmtArr[$job_id]['yarn_amt']/$yarnQtyAmtArr[$job_id]['yarn_qty'];
  $finishPer=$total_finishing_amount/$total_finishing_qty;
  $ydsPer=$convQtyAmtArr[$job_id]['conv_amt'][30]/$convQtyAmtArr[$job_id]['conv_qty'][30];
  $aopPer=$convQtyAmtArr[$job_id]['conv_amt'][35]/$convQtyAmtArr[$job_id]['conv_qty'][35];
  $knitPer=$convQtyAmtArr[$job_id]['conv_amt'][1]/$convQtyAmtArr[$job_id]['conv_qty'][1];
  $purchPer=$purchase_qty/$purchase_amount;
  $dyePer=$convQtyAmtArr[$job_id]['conv_amt'][31]/$convQtyAmtArr[$job_id]['conv_qty'][31];
  
  $totFabPer=$yarnPer+$finishPer+$ydsPer+$aopPer+$knitPer+$purchPer+$dyePer;
  
  //echo $yarnPer.'='.$finishPer.'='.$ydsPer.'='.$aopPer.'='.$knitPer.'='.$purchPer.'='.$dyePer.'='.$totFabPer;
  //echo $other_costing_arr[$job_id]['cm_cost'].'='.$plancutqty.'='.$set_item_ratio;
  
    ?>
    <br>
    <label  style="float:left;background:#CCCCCC; font-size:larger;"><b>Summary </b> </label> 
    <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px; margin-top: 10px; font-family: 'Arial Narrow', Arial, sans-serif;" rules="all">
    
      <tr style="background: #D7ECD9">
        <th colspan="8" width="320">Fabric </th>
        <th colspan="4" width="160">Embellishment</th>
        <th colspan="4" width="160">Trims + CM + Misc</th>
        <th style="background: yellow">TTL COST &dollar;</th>
      </tr>
      <tr style="background: #D7ECD9">
        <th align="center">Item</th>
        <th align="center">Cost/Uom</th>
        <th align="center">Amount</th>
        <th align="center">&percnt;</th>
        <th align="center">Item</th>
        <th align="center">Cost/Uom</th>
        <th align="center">Amount</th>
        <th align="center">&percnt;</th>
        <th align="center">Item</th>
        <th align="center">Cost/Dz</th>
        <th align="center">Amount</th>
        <th align="center">&percnt;</th>
        <th align="center">Item</th>
        <th align="center">Cost/Dz</th>
        <th align="center">Amount</th>
        <th align="center">&percnt;</th>
        <th rowspan="5" align="right" style="background: yellow; color: #8B0000"><b>
          <? if($total_budget_value>0) { echo fn_number_format($total_budget_value,2,'',''); } ?><br/><br/>
                <? if($total_budget_value>0){ echo fn_number_format($total_budget_value/$order_values*100,2).'%'; } ?></b>
        </th>
      </tr>
      <tr>
        <th align="center">Yarn</th>
        <td align="center"><? if($yarn_amount_summ>0) { echo fn_number_format($yarnPer,2); } ?></td>
        <td align="right" style="color: #8B0000"><? if($yarn_amount_summ>0) { echo '&dollar;'.fn_number_format($yarn_amount_summ,2); } ?></td>
        <td align="right"><? if($yarn_amount_summ>0) { echo fn_number_format($yarnQtyAmtArr[$job_id]['yarn_amt']/$order_values*100,2).'%';}; ?></td>

        <th align="center">Finishing</th>
        <td align="center"><? if($total_finishing_amount>0) { echo fn_number_format($total_finishing_amount/$total_finishing_qty,2); } ?></td>
        <td align="right" style="color: #8B0000"><? if($total_finishing_amount>0) { echo '&dollar;'.fn_number_format($total_finishing_amount,2); } ?></td>
        <td align="right"><? if($total_finishing_amount>0) { echo fn_number_format($total_finishing_amount/$order_values*100,2).'%';} ?></td>

        <th align="center">Print</th>
        <td align="center"><? if($print_amount_summ>0) { echo fn_number_format($embQtyAmtArr[$job_id][1]['amt']/$embQtyAmtArr[$job_id][1]['qty'],2);}  ?></td>
        <td align="right" style="color: #8B0000"><? if($print_amount_summ>0) { echo '&dollar;'.fn_number_format($print_amount_summ,2);}  ?></td>
        <td align="right"><? if($print_amount_summ>0) { echo fn_number_format($embQtyAmtArr[$job_id][1]['amt']/$order_values*100,2).'%';} ?></td>

        <th align="center">Trim</th>
        <td align="center"><? if($trimQtyAmtArr[$job_id]['trimtotamt']>0) { echo fn_number_format($trimQtyAmtArr[$job_id]['trimtotamt']/$order_job_qnty,2); } ?></td>
        <td align="right" style="color: #8B0000"><? if($trimQtyAmtArr[$job_id]['trimtotamt']>0) { echo '&dollar;'.fn_number_format($trimQtyAmtArr[$job_id]['trimtotamt'],2);} ?></td>
        <td align="right"><? if($trimQtyAmtArr[$job_id]['trimtotamt']>0) { echo fn_number_format($trimQtyAmtArr[$job_id]['trimtotamt']/$order_values*100,2).'%';} ?></td>
      </tr>
      <tr>
        <th align="center">Yds</th>
        <td align="center"><? if($yds_amount_summ>0) { echo fn_number_format($convQtyAmtArr[$job_id]['conv_amt'][30]/$convQtyAmtArr[$job_id]['conv_qty'][30],2); } ?></td>
        <td align="right" style="color: #8B0000"><? if($yds_amount_summ>0) { echo '&dollar;'.fn_number_format($yds_amount_summ,2);} ?></td>
        <td align="right"><? if($yds_amount_summ>0) { echo fn_number_format($convQtyAmtArr[$job_id]['conv_amt'][30]/$order_values*100,2).'%';} ?></td>

        <th align="center">AOP</th>
        <td align="center"><? if($aop_amount_summ>0) { echo fn_number_format($convQtyAmtArr[$job_id]['conv_amt'][35]/$convQtyAmtArr[$job_id]['conv_qty'][35],2); } ?></td>
        <td align="right" style="color: #8B0000"><? if($aop_amount_summ>0) { echo '&dollar;'.fn_number_format($aop_amount_summ,2);} ?></td>
        <td align="right"><? if($aop_amount_summ>0) { echo fn_number_format($convQtyAmtArr[$job_id]['conv_amt'][35]/$order_values*100,2).'%';} ?></td>

        <th align="center">EMB</th>
        <td align="center"><? if($emb_amount_summ>0) { echo fn_number_format($embQtyAmtArr[$job_id][2]['amt']/$embQtyAmtArr[$job_id][2]['qty'],2);}  ?></td>
        <td align="right" style="color: #8B0000"><? if($emb_amount_summ>0) { echo '&dollar;'.fn_number_format($emb_amount_summ,2);}  ?></td>
        <td align="right"><? if($emb_amount_summ>0) { echo fn_number_format($embQtyAmtArr[$job_id][2]['amt']/$order_values*100,2).'%';} ?></td>
        <th align="center">MISC</th>
        <td align="center"><?  if($misc_cost>0) { echo fn_number_format($misc_cost/$order_job_qnty*12,2); } ?></td>
        <td align="right" style="color: #8B0000"><? if($misc_cost>0) { echo '&dollar;'.fn_number_format($misc_cost,2);}  ?></td>
        <td align="right"><? if($misc_cost>0) { echo fn_number_format($misc_cost/$order_values*100,2).'%';} ?></td>
      </tr>
      <tr>
        <th align="center">Knitting</th>
        <td align="center"><? if($knitting_amount_summ !='') { echo fn_number_format($convQtyAmtArr[$job_id]['conv_amt'][1]/$convQtyAmtArr[$job_id]['conv_qty'][1],2); } ?></td>
        <td align="right" style="color: #8B0000"><? if($knitting_amount_summ !='') { echo  '&dollar;'.$knitting_amount_summ;}   ?></td>
        <td align="right"><? if($knitting_amount_summ !=''){echo fn_number_format($convQtyAmtArr[$job_id]['conv_amt'][1]/$order_values*100,2).'%'; } ?></td>

        <th align="center">P. Fabric</th>
        <td align="center"><? $total_fabic_cost+=$purchase_qty/$purchase_amount; if($purchase_qty>0 && $purchase_amount>0){ echo fn_number_format($purchase_qty/$purchase_amount,2);} ?></td>
        <td align="right"><? $total_fabric_amount+=$purchase_amount; if($purchase_amount){echo '&dollar;'.fn_number_format($purchase_amount,2); } ?></td>
        <td align="right"><? $total_fabric_per+=$purchase_amount/$order_values*100; if($purchase_amount>0){ echo fn_number_format($purchase_amount/$order_values*100,2).'%'; }  ?></td>

        <th align="center">Wash</th>
        <td align="center"><? if($wash_amount_summ>0) {echo fn_number_format($embQtyAmtArr[$job_id][3]['amt']/$embQtyAmtArr[$job_id][3]['qty'],2); };  ?></td>
        <td align="right" style="color: #8B0000"><? if($wash_amount_summ>0) { echo '&dollar;'.fn_number_format($wash_amount_summ,2);}  ?></td>
        <td align="right"><? if($wash_amount_summ>0) { echo fn_number_format($embQtyAmtArr[$job_id][3]['amt']/$order_values*100,2).'%';} ?></td>

        <th align="center" style="color: #8B0000">F.CM</th>
        <td align="center" style="color: #8B0000" title="(CM Cost/Order Qty Pcs)x12"><? if($other_costing_arr[$job_id]['cm_cost']>0){echo fn_number_format(($other_costing_arr[$job_id]['cm_cost']/($plancutqty*$set_item_ratio))*12,2); } ?></td>
        <td align="right" style="color: #8B0000"><? if($other_costing_arr[$job_id]['cm_cost']>0){ echo fn_number_format($other_costing_arr[$job_id]['cm_cost'],2); } ?></td>
        <td align="right"><? if($other_costing_arr[$job_id]['cm_cost']>0){ echo fn_number_format($other_costing_arr[$job_id]['cm_cost']/$order_values*100,2).'%'; } ?></td>
      </tr>
      <tr>
        <th align="center">Dyeing</th>
        <td align="center"><? if($dyeing_amount_summ>0) {echo fn_number_format($convQtyAmtArr[$job_id]['conv_amt'][31]/$convQtyAmtArr[$job_id]['conv_qty'][31],2);} ?></td>
        <td align="right" style="color: #8B0000"><? if($dyeing_amount_summ>0) { echo '&dollar;'.fn_number_format($dyeing_amount_summ,2);} ?></td>
        <td align="right"><? if($dyeing_amount_summ>0) { echo fn_number_format($convQtyAmtArr[$job_id]['conv_amt'][31]/$order_values*100,2).'%';} ?></td>

        <th align="center" style="color: #8B0000">TOTAL</th>
        <th align="center" style="color: #8B0000"><? if($total_fabic_cost>0){ echo fn_number_format($totFabPer,2); } ?></th>
        <th align="right" style="color: #8B0000"><? if($total_fabric_amount>0){ echo '&dollar;'.fn_number_format($total_fabric_amount,2); }  ?></th>
        <th align="right" style="color: #8B0000"><? if($total_fabric_per>0){ echo fn_number_format($total_fabric_per,2); }  ?></th>

        <th align="center" title="Special works, Garments dyeing, UV print and others.">Others</th>
        <td align="center"><? if($others_emb_amount>0) {echo fn_number_format($others_emb_amount/$others_emb_qty,2); } ?></td>
        <td align="right"><? if($others_emb_amount>0) { echo '&dollar;'.fn_number_format($others_emb_amount,2); }  ?></td>
        <td align="right"><? if($others_emb_amount>0) { echo fn_number_format($others_emb_amount/$order_values*100,2);}  ?></td>
        <th></th>
        <td></td>
        <td></td>
        <td></td>
      </tr>
    </table>    
    <?
	$location_cpm_cost=0;
	$cm_min_variable=return_field_value("yarn_iss_with_serv_app as cost_per_minute","variable_order_tracking","company_name =".$cbo_company_name." and variable_list=67 and is_deleted=0 and status_active=1","cost_per_minute");
	if($cm_min_variable=="" || $cm_min_variable==0) $location_cpm_cost=0; else $location_cpm_cost=$cm_min_variable;
	if($location_cpm_cost!=1)
	{
		$sql_std_para=sql_select("select interest_expense, income_tax, cost_per_minute, applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_name and status_active=1 and is_deleted=0 order by id");
		
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				//$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para[$newdate][interest_expense]=$row[csf('interest_expense')];
				$financial_para[$newdate][income_tax]=$row[csf('income_tax')];
				$financial_para[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
			}
		}
	}
	else
	{
		$sql_std_para=sql_select( "select a.id, b.id as dtls_id, b.location_id, b.applying_period_date, b.applying_period_to_date, b.monthly_cm_expense, b.no_factory_machine, b.working_hour, b.cost_per_minute from lib_standard_cm_entry a, lib_standard_cm_entry_dtls b where a.id=b.mst_id and b.location_id=$location_name_id and a.company_id=$cbo_company_name" );
		foreach($sql_std_para as $row)
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para[$newdate][interest_expense]=$row[csf('interest_expense')];
				$financial_para[$newdate][income_tax]=$row[csf('income_tax')];
				$financial_para[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
			}
		}
	}

    $pre_costing_date=change_date_format($costing_date,'','',1);
    ?>
    <? if($zero_value==0){ ?>
    <br/>
    <label  style="text-align:left; background:#CCCCCC; font-size:larger;"><b>CM Details </b> </label>
    <div style="width:970px; margin-top: 10px; font-family: 'Arial Narrow', Arial, sans-serif;">
    <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:720px;float: left;" rules="all">
      <tr>
        <th colspan="13">&nbsp;</th>
      </tr>
      <tr style="background: #D7ECD9">
        <th>Style NO.</th>
        <th>MC</th>
        <th>Prd/Hr</th>
        <th>SMV</th>
        <th>BCM</th>
        <th>F.CM</th>
        <th>TTL Min</th>
        <th align="center">CPM</th>
        <th>RL</th>
        <th>RD</th>
        <th>A Eff%</th>
        <th>Layout No</th>
        <th>Alloc Qty</th>
      </tr>
      <tr align="center">
        <td><?= $style_no  ?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><?= $sew_smv ?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&#36; <? echo fn_number_format($financial_para[$pre_costing_date][cost_per_minute],4); ?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <th>Grand Total</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th><?= $sew_smv ?></th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&#36; <? echo fn_number_format($financial_para[$pre_costing_date][cost_per_minute],4); ?></th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
      </tr>
    </table>
    <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:248px; margin-left: 2px; float: right;" rules="all">
      <tr>
        <th colspan="3" bgcolor="yellow">Embellishment[DZN]</th>
      </tr>
      <tr>
        <th>Print Qty</th>
        <th>Emb Qty</th>
        <th>Wash Qty</th>
      </tr>
      <tr align="center">
        <td><? if($embQtyAmtArr[$job_id][1]['qty']>0){echo fn_number_format($embQtyAmtArr[$job_id][1]['qty'],2); } else { echo '&nbsp;'; } ?></td>
        <td><? if($embQtyAmtArr[$job_id][2]['qty']>0){echo fn_number_format($embQtyAmtArr[$job_id][1]['qty'],2); } else { echo '&nbsp;'; } ?></td>
        <td><? if($embQtyAmtArr[$job_id][3]['qty']>0){echo fn_number_format($embQtyAmtArr[$job_id][3]['qty'],2); } else { echo '&nbsp;'; } ?></td>
      </tr>
      <tr>
        <th><? if($embQtyAmtArr[$job_id][1]['qty']>0){echo fn_number_format($embQtyAmtArr[$job_id][1]['qty'],2); } else { echo '&nbsp;'; } ?></th>
        <th><? if($embQtyAmtArr[$job_id][2]['qty']>0){echo fn_number_format($embQtyAmtArr[$job_id][2]['qty'],2); } else { echo '&nbsp;'; } ?></th>
        <th><? if($embQtyAmtArr[$job_id][3]['qty']>0){echo fn_number_format($embQtyAmtArr[$job_id][3]['qty'],2); } else { echo '&nbsp;'; } ?></th>
      </tr>
    </table>    
    </div>
    <br>
    <? } ?>
    <?
      $nameArray_fabric_description= sql_select("SELECT (a.pre_cost_fabric_cost_dtls_id) as fabric_cost_dtls_id, a.item_number_id, max(a.lib_yarn_count_deter_id) as determin_id, a.body_part_id, a.uom, a.color_type_id, a.fabric_source, a.construction, a.composition, a.gsm_weight, min(a.width_dia_type) as width_dia_type, b.dia_width,avg(b.cons) as cons, avg(b.process_loss_percent) as process_loss_percent, a.fab_nature_id, avg(b.requirment) as requirment, d.fabric_composition_id FROM wo_pre_cost_fabric_cost_dtls_h a, wo_po_color_size_his c, wo_pre_fab_avg_con_dtls_h b, lib_yarn_count_determina_mst d WHERE a.job_no=b.job_no and a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and c.job_no_mst=a.job_no and  c.color_size_id=b.color_size_table_id and a.lib_yarn_count_deter_id=d.id and c.status_active=1 and c.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.job_no ='$txt_job_no' and a.approved_no=$revised_no and b.cons>0 group by a.body_part_id, a.uom, a.pre_cost_fabric_cost_dtls_id, a.item_number_id, a.color_type_id, a.fabric_source, a.construction, a.composition, a.gsm_weight, b.dia_width, a.fab_nature_id, d.fabric_composition_id order by fabric_cost_dtls_id, a.body_part_id, b.dia_width");
		
      //a.fabric_source=1 and
      foreach ($nameArray_fabric_description as $row) {
        $fabric_id=$row[csf('fabric_cost_dtls_id')];
        $yarn_amount= $yarnDataWithFabricidArr[$fabric_id]['amount'];
        $yarn_qty= $yarnDataWithFabricidArr[$fabric_id]['qty'];

        $yds_amount = array_sum($con_amount_fabric_process[$fabric_id][30]);
        $yds_qty = array_sum($con_qty_fabric_process[$fabric_id][30]);

        $knitting_amount = array_sum($con_amount_fabric_process[$fabric_id][1]);
        $knitting_qty = array_sum($con_qty_fabric_process[$fabric_id][1]);
        $dyeing_amount = array_sum($con_amount_fabric_process[$fabric_id][31]);
        $dyeing_qty = array_sum($con_qty_fabric_process[$fabric_id][31]);
        $aop_amount = array_sum($con_amount_fabric_process[$fabric_id][35]);
        $aop_qty = array_sum($con_qty_fabric_process[$fabric_id][35]);

        $total_finishing_amount=0;
        $total_finishing_qty=0;
        foreach ($finishing_arr as $fid) {
          $total_finishing_amount += array_sum($con_amount_fabric_process[$fabric_id][$fid]);
          $total_finishing_qty += array_sum($con_qty_fabric_process[$fabric_id][$fid]);
        }
        
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['body_part_id'] = $row[csf('body_part_id')];
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['description'] = $row[csf('construction')].', '.$fabric_composition_arr[$row[csf('fabric_composition_id')]];
        if($row[csf('fab_nature_id')]==2)
        {
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['fqty'] = array_sum($fabric_qty_arr['knit']['finish'][$row[csf('fabric_cost_dtls_id')]]);
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['gqty'] = array_sum($fabric_qty_arr['knit']['grey'][$row[csf('fabric_cost_dtls_id')]]);
        }
        if($row[csf('fab_nature_id')]==3)
        {
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['fqty'] = array_sum($fabric_qty_arr['woven']['finish'][$row[csf('fabric_cost_dtls_id')]]);
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['gqty'] = array_sum($fabric_qty_arr['woven']['grey'][$row[csf('fabric_cost_dtls_id')]]);
        }
        
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['funit'] = $row[csf('uom')];
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['cons'] = $row[csf('cons')];
        
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['process_loss'] = $row[csf('process_loss_percent')];
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['yarn_amount'] = $yarn_amount;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['yarn_per'] = $yarn_amount/$yarn_qty;

        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['yds_amount'] = $yds_amount;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['yds_per'] = $yds_amount/$yds_qty;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['knitting_amount'] = $knitting_amount;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['knitting_per'] = $knitting_amount/$knitting_qty;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['dyeing_amount'] = $dyeing_amount;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['dyeing_per'] = $dyeing_amount/$dyeing_qty;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['aop_amount'] = $aop_amount;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['aop_per'] = $aop_amount/$aop_qty;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['finishing_amount'] = $total_finishing_amount;
        $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['finishing_per'] = $total_finishing_amount/$total_finishing_qty;
        if($row[csf('fabric_source')]==1)
        {
          $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['ttl_cost'] = $yarn_amount+$yds_amount+$knitting_amount+$dyeing_amount+$aop_amount+$total_finishing_amount;
        }
        if($row[csf('fabric_source')]==2)
        {
          if($row[csf('fab_nature_id')]==2)
          {
            $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['ttl_cost']=array_sum($fabric_amount_arr['knit']['grey'][$row[csf('fabric_cost_dtls_id')]]);
          }
          if($row[csf('fab_nature_id')]==3)
          {
            $fabric_data_arr[$row[csf('fabric_cost_dtls_id')]]['ttl_cost']=array_sum($fabric_amount_arr['woven']['grey'][$row[csf('fabric_cost_dtls_id')]]);
          }
        }
      }
	  //echo "kkkk1";
      if($zero_value==0){ ?>
      <br>
      <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px; margin-top: 10px; font-family: 'Arial Narrow', Arial, sans-serif;" rules="all">
      <label  style="float:left;background:#CCCCCC; font-size:larger;"><b>Fabric Details </b> </label>  
        <tr style="background: #D7ECD9">
          <th rowspan="2">Garments Part Name</th>
          <th rowspan="2">Fabric Details</th>
          <th rowspan="2">Con</th>
          <th>F. QTY</th>
          <th rowspan="2">Process Loss</th>
          <th>G. QTY</th>
          <th colspan="7">Cost/Uom (Fabric)</th>
          <th rowspan="2">Cost/Dz</th>
          <th rowspan="2" style="background: yellow;">TTL Cost $</th>
        </tr>
        <tr style="background: #D7ECD9">
          <th>Unit</th>
          <th>Unit</th>
          <th>Yarn</th>
          <th>Yds</th>
          <th>Knitting</th>
          <th>Dyeing</th>
          <th>AOP</th>
          <th>Finishing</th>
          <th>Cost/Uom</th>
        </tr>
        <?
          foreach ($fabric_data_arr as $value) {?>
            <tr>
              <td rowspan="2"><?= $body_part[$value['body_part_id']] ?></td>
              <td rowspan="2"><?= $value['description'] ?></td>
              <td rowspan="2" align="center"><?= fn_number_format($value['cons'],2); ?></td>
              <td align="center"><? $total_fqty+=$value['fqty']; echo fn_number_format($value['fqty'],2); ?></td>
              <td rowspan="2" align="center"><? if($value['process_loss']>0){ echo fn_number_format($value['process_loss'],2);} ?></td>
              <td align="center"><? $total_gqty+=$value['gqty']; echo fn_number_format($value['gqty'],2) ?></td>
              <td rowspan="2" align="right"><? $total_yarn_amount += $value['yarn_amount']; if($value['yarn_amount']>0){echo fn_number_format($value['yarn_per'],2); }?><br><? if($value['yarn_amount']>0){ echo fn_number_format($value['yarn_amount'],2);} ?></td>
              <td rowspan="2" align="right"><? $total_yds_amount += $value['yds_amount']; if($value['yds_per']>0){ echo fn_number_format($value['yds_per'],2);}?><br><? if($value['yds_amount']>0){ echo fn_number_format($value['yds_amount'],2);} ?></td>
              <td rowspan="2" align="right"><? $total_knitting_amount += $value['knitting_amount']; if($value['knitting_per']>0){ echo fn_number_format($value['knitting_per'],2);}?><br><? if($value['knitting_amount']>0){ echo fn_number_format($value['knitting_amount'],2);} ?></td>
              <td rowspan="2" align="right"><? $total_dyeing_amount += $value['dyeing_amount']; if($value['dyeing_per']>0){ echo fn_number_format($value['dyeing_per'],2);} ?><br><? if($value['dyeing_amount']>0){echo fn_number_format($value['dyeing_amount'],2);} ?></td>
              <td rowspan="2" align="right"><? $total_aop_amount += $value['aop_amount']; if($value['aop_per']>0){ echo fn_number_format($value['aop_per'],2);} ?><br><? if($value['aop_amount']>0){ echo fn_number_format($value['aop_amount'],2);} ?></td>
              <td rowspan="2" align="right"><? $total_finishing_amount += $value['finishing_amount']; if($value['finishing_per']>0){ echo fn_number_format($value['finishing_per'],2);}?><br><? if($value['finishing_amount']>0){fn_number_format($value['finishing_amount'],2);} ?></td>
              <td rowspan="2" align="right" title="TTL Cost/Finish Quantity"><?= fn_number_format($value['ttl_cost']/$value['fqty'],2) ?></td>
              <td rowspan="2" align="right"><?= fn_number_format($value['ttl_cost']/$order_job_qnty*12,2) ?></td>
              <th rowspan="2" style="background: yellow;" align="right"><? $total_ttl_cost += $value['ttl_cost'];  echo fn_number_format($value['ttl_cost'],2) ?></th>
            </tr>
            <tr>
              <td align="center"><?= $unit_of_measurement[$value['funit']] ?></td>
              <td align="center"><?= $unit_of_measurement[$value['funit']] ?></td>              
            </tr>
          <? }
        ?>
        <tr>
          <th colspan="2">Fabric  Total</th>
          <td></td>
          <th align="center"><? if($total_fqty>0){echo fn_number_format($total_fqty,2);} ?></th>
          <td></td>
          <th align="right"><? if($total_gqty){ echo fn_number_format($total_gqty,2); } ?></th>
          <th align="right"><? if($total_yarn_amount){ echo fn_number_format($total_yarn_amount,2); } ?></th>
          <th align="right"><? if($total_yds_amount){ echo fn_number_format($total_yds_amount,2); } ?></th>
          <th align="right"><? if($total_knitting_amount){ echo fn_number_format($total_knitting_amount,2); } ?></th>
          <th align="right"><? if($total_dyeing_amount){ echo fn_number_format($total_dyeing_amount,2); } ?></th>
          <th align="right"><? if($total_aop_amount){ echo fn_number_format($total_aop_amount,2); } ?></th>
          <th align="right"><? if($total_finishing_amount){ echo fn_number_format($total_finishing_amount,2); } ?></th>
          <th></th>
          <th></th>
          <th style="background: yellow;" align="right"><? if($total_ttl_cost){ echo '&dollar;'.fn_number_format($total_ttl_cost,2); } ?> <br><? if($total_ttl_cost){ echo fn_number_format($total_ttl_cost/$order_values*100,2).'%'; } ?></th>
        </tr>
      </table>
      <? } ?>
      <?
      //end   All Fabric Cost part report-------------------------------------------
      $lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
      $sql = "select min(pre_cost_fab_yarn_cost_dtls_id) as id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, color,type_id, min(cons_ratio) as cons_ratio, sum(cons_qnty) as cons_qnty, rate, sum(amount) as amount from wo_pre_cost_fab_yarn_cst_dtl_h where job_no='".$txt_job_no."' and approved_no=$revised_no and status_active=1 and is_deleted=0 group by count_id, copm_one_id, percent_one, copm_two_id, percent_two, color,type_id, rate";
       //echo $sql;
      $data_array=sql_select($sql); 
      //$yarn_data_array=$yarn->getCountCompositionPercentTypeColorAndRateWiseYarnQtyAndAmountArray();
      //print_r($yarn_data_array);
    ?>
    <br>
    <div style="margin-top:15px; font-family: 'Arial Narrow', Arial, sans-serif;">
        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px;text-align:center;" rules="all">
            <label style="float:left;background:#CCCCCC; font-size:larger;"><b>Yarn Details </b> </label>  
            <tr style="font-weight:bold;">
                <td width="540" style="background: #D7ECD9">Yarn Description</td>
                <td width="80" style="background: #D7ECD9">Yarn Qty/<?=$costing_for; ?></td> 
                <td width="80" style="background: #D7ECD9">TTL Yarn Qty</td>                 
                <td width="80" style="background: #D7ECD9">Rate &#36;</td>
                <td width="80" style="background: yellow">Amount &#36;</td>
                <td width="80" style="background: #D7ECD9">% to Ord. Value</td>
            </tr>
            <?
            $total_yarn_qty = 0; $total_yarn_amount = 0; $total_yarn_cost_dzn=$total_yarn_qty_dzn=0; $total_yarn_cost_kg=0; $total_yarn_avg_cons_qty=0;
            foreach( $data_array as $row )
            { 
				if($row[csf("percent_one")]==100)
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$color_library[$row[csf("color")]]." ".$yarn_type[$row[csf("type_id")]];
				else
					$item_descrition = $lib_yarn_count[$row[csf("count_id")]]." ".$composition[$row[csf("copm_one_id")]]." ".$row[csf("percent_one")]."% ".$composition[$row[csf("copm_two_id")]]." ".$row[csf("percent_two")]."% ".$color_library[$row[csf("color")]]." ".$yarn_type[$row[csf("type_id")]];
				$rowcons_qnty = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];
				$rowavgcons_qnty = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];
				$rowamount = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['amount'];
				if(is_infinite($rowamount) || is_nan($rowamount)){$rowamount=0;}
				?>   
				<tr>
                    <td align="left"><? echo $item_descrition; ?></td>
                    <td align="right"><? echo fn_number_format($row[csf("cons_qnty")],3); ?></td>
                    <td align="right"><? echo fn_number_format($rowcons_qnty,2); ?></td>
                    
                    <td align="right"><? if($row[csf("rate")]>0){ echo fn_number_format($row[csf("rate")],3);} ?></td>
                    <td align="right" style="background: yellow"><? if($rowamount>0){ echo fn_number_format($rowamount,2);} ?></td>
                    <td align="right"><? 
                    $cv=($row[csf("amount")]/$price_dzn)*100;
                    if(is_infinite($cv) || is_nan($cv)){$cv=0;}
                    if($cv>0){echo fn_number_format($cv,2); }
                    ?></td>
				</tr>
				<?  
				$total_yarn_qty+=$rowcons_qnty;
				$total_yarn_qty_dzn+=$row[csf("cons_qnty")];
				$total_avg_yarn_qty+=$rowavgcons_qnty;
				$total_yarn_amount +=$rowamount;
				$total_yarn_cost_dzn+=$row[csf("amount")];
				$total_yarn_avg_cons_qty+=$rowavgcons_qnty;
				$total_yarn_cost_kg=$total_yarn_amount/$total_yarn_qty;
				if(is_infinite($total_yarn_cost_kg) || is_nan($total_yarn_cost_kg)){$total_yarn_cost_kg=0;}
            }
            ?>
            <tr class="rpt_bottom" style="font-weight:bold">
                <td>Yarn Total</td>
                <td align="right"><? if($total_yarn_qty_dzn>0){ echo fn_number_format($total_yarn_qty_dzn,4); } ?></td>
                <td align="right"><? if($total_yarn_qty>0){ echo fn_number_format($total_yarn_qty,2); } ?></td>                    
                <td></td>
                <td align="right" bgcolor="yellow"><? if($total_yarn_amount>0){ echo '&dollar;'.fn_number_format($total_yarn_amount,2); } ?></td>
                <td align="right"><? 
                $cv=($total_yarn_cost_dzn/$price_dzn)*100;
                if(is_infinite($cv) || is_nan($cv)){$cv=0;}
                if($cv>0){ echo fn_number_format($cv,2).' %';  }
                ?></td>
            </tr>
        </table>
    </div>
    <?
    //End Yarn Cost part report here -------------------------------------------

	//start Trims Cost part report here -------------------------------------------
	$supplier_library_fabric=return_library_array( "select a.id, a.supplier_name from lib_supplier a where a.is_deleted=0  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");
  
    $sql = "select pre_cost_trim_cost_dtls_id as id, job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp_multi, status_active from wo_pre_cost_trim_cost_dtls_his  where job_no='".$txt_job_no."' and approved_no=$revised_no and status_active=1 and is_deleted=0";
    $data_array=sql_select($sql);
  ?>
    <div style="margin-top:15px">
        <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:970px;text-align:center;font-family: 'Arial Narrow', Arial, sans-serif;" rules="all">
            <label  style="float:left;background:#CCCCCC; font-size:larger"><b>Trims Details</b> </label> 
            <tr style="font-weight:bold; background: #D7ECD9" >
                <td width="110" style="background: #D7ECD9">Item Group</td>
                <td width="110" style="background: #D7ECD9">Item Description</td>
                <td width="100" style="background: #D7ECD9">Supplier</td>
                <td width="60" style="background: #D7ECD9">UOM</td>
                <td width="80" style="background: #D7ECD9">Cons/<?=$costing_for; ?>[Qnty]</td>
                <td width="100" style="background: #D7ECD9">TTL Required[Qnty]</td>
                <td width="80" style="background: #D7ECD9">Rate &#36;</td>
                <td width="80" style="background: #D7ECD9">Amount/<?=$costing_for; ?>&#36;</td>
                <td width="80" style="background: yellow">Amount &#36;</td>
                <td width="60" style="background: #D7ECD9">% to Ord. Value</td>
            </tr>
            <?
           // $trim_qty_arr=$trims->getQtyArray_by_precostdtlsid();
            //print_r($trim_qty);
            //$trim_amount_arr=$trims->getAmountArray_precostdtlsid();
            $total_trims_cost=0;  $total_trims_qty=$total_trims_cost_dzn=0;$total_trims_cost_dzn=0;$total_trims_cost_kg=0;
            foreach( $data_array as $row ){ 
				$trim_group=return_library_array( "select item_name,id from  lib_item_group where id=".$row[csf("trim_group")], "id", "item_name" ); 
				$cons_dzn_gmts= $row[csf("cons_dzn_gmts")];
				$amount_dzn= $row[csf("amount")];
				$pre_trims_qty=$trim_qty_arr[$row[csf("id")]];
				$pre_trims_amount=$trim_amount_arr[$row[csf("id")]];  
				
				$nominated_supp_str="";
				$exsupp=explode(",",$row[csf("nominated_supp_multi")]);
				foreach($exsupp as $sid)
				{
					if($nominated_supp_str=="") $nominated_supp_str=$supplier_library_fabric[$sid]; else $nominated_supp_str.=','.$supplier_library_fabric[$sid];
				}            
				?>   
				<tr>
                    <td align="left"><? echo $trim_group[$row[csf("trim_group")]]; ?></td>
                    <td align="left"><? echo $row[csf("description")]; ?></td>
                    <td align="left"><?=$nominated_supp_str; ?></td>
                    <td align="center"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
                    <td align="right"><? echo fn_number_format($cons_dzn_gmts,3); ?></td>
                    <td align="right"><? echo fn_number_format($pre_trims_qty,4); ?></td>
                    <td align="right"><? echo fn_number_format($row[csf("rate")],3); ?></td>
                    <td align="right"><? echo fn_number_format($amount_dzn,4); ?></td>
                    <td align="right" style="background: yellow"><? echo fn_number_format($pre_trims_amount,2); ?></td>
                    <td align="right"  title="<? echo $amount_dzn.'='.$price_dzn;?>">
                    <? 
                    $cv=($amount_dzn/$price_dzn)*100;
                    if(is_infinite($cv) || is_nan($cv)){$cv=0;}
                    echo fn_number_format($cv,2); 
                    //echo fn_number_format(($amount_dzn/$price_dzn)*100,2); 
                    ?></td>
				</tr>
				<?
				$total_trims_cost += $pre_trims_amount;
				$total_trims_cost_dzn += $amount_dzn;
				$total_trims_qty += $pre_trims_qty;
            }
            ?>
            <tr class="rpt_bottom" style="font-weight:bold" >
                <td>Trims Total</td>
                <td colspan="4"></td>
                <td align="right"><? if($total_trims_qty>0){ echo fn_number_format($total_trims_qty,4); } ?></td>
                <td align="right"><? //echo fn_number_format($total_trims_cost_dzn,4); ?></td>                   
                
                <td align="right"><? if($total_trims_cost_dzn>0){ echo '&dollar;'.fn_number_format($total_trims_cost_dzn,4); } ?></td>
                <td align="right" style="background: yellow"><? if($total_trims_cost>0){ echo '&dollar;'.fn_number_format($total_trims_cost,2); } ?></td>
                <td align="right" title="<? echo $total_trims_cost_dzn.'='.$price_dzn;?>">
                <? 
                $cv=($total_trims_cost_dzn/$price_dzn)*100;
                if(is_infinite($cv) || is_nan($cv)){$cv=0;}
                if($cv){ echo fn_number_format($cv,2).' %'; }
                ?>
                </td>
            </tr>                
        </table>
    </div>
	<?
    $pre_cost_dtls_arr = sql_select("SELECT pre_cost_dtls_id as id, job_no, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, depr_amor_pre_cost, deffdlc_cost, studio_cost, design_cost, trims_cost_percent, embel_cost, embel_cost_percent, comm_cost, comm_cost_percent, commission, incometax_cost, interest_cost, interest_percent, commission_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, common_oh, common_oh_percent, design_percent, studio_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche from wo_pre_cost_dtls_histry where job_no='".$txt_job_no."' and approved_no=$revised_no and status_active=1 and is_deleted=0");
    foreach ($pre_cost_dtls_arr as $row) {
		$price_dzn=$row[csf("price_dzn")];
		$lab_test_dzn=$row[csf("lab_test")];
		$commission_cost_dzn=$row[csf("commission")];
		$commercial_cost_dzn = $row[csf("comm_cost")];
		
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
		$studio_cost_percent=$row[csf("studio_percent")];
		$design_cost_percent=$row[csf("design_percent")]; 
		
		$other_cost_per = $inspection_dzn+$freight_dzn+$certificate_pre_cost_dzn+$deffdlc_cost_dzn+$design_cost_dzn+$studio_cost_dzn+$common_oh_dzn+$interest_cost_dzn+$incometax_cost_dzn+$depr_amor_pre_cost_dzn;
    }      
     ?>
    <table  class="rpt_table"  border="1" align="left" cellpadding="0" width="350" cellspacing="0" rules="all" style="margin-top: 10px;font-family: 'Arial Narrow', Arial, sans-serif;">
        <tr style="background: #D7ECD9">
            <th>MISC/Others Cost</th>
            <th>%</th>
            <th>TTL Cost $</th>
        </tr>
        <tr>
            <td>Test cost</td>
            <td align="right"><?
            $lab_test_per=($other_costing_arr[$job_id]['lab_test']/$order_values)*100;
            if(is_infinite($lab_test_per) || is_nan($lab_test_per)) $lab_test_per=0;
            
            if($lab_test_per>0){echo fn_number_format($lab_test_per,2);}
            $total_misc_per += $lab_test_per;
            ?></td>
            <th align="right"><? if($other_costing_arr[$job_id]['lab_test']>0){ echo fn_number_format($other_costing_arr[$job_id]['lab_test'],2);} ?></th>
        </tr>
        <tr>
            <td>Buying commission</td>
            <td align="right"><?
            $commission_cost_per=($other_costing_arr[$job_id]['commission']/$order_values)*100;
            if(is_infinite($commission_cost_per) || is_nan($commission_cost_per)) $commission_cost_per=0;
            
            if($commission_cost_per>0){ echo fn_number_format($commission_cost_per,2);}
            $total_misc_per +=$commission_cost_per;
            ?></td>
            <th align="right"><? if($other_costing_arr[$job_id]['commission']>0){ echo fn_number_format($other_costing_arr[$job_id]['commission'],2);} ?></th>
        </tr>
        <tr>
            <td>Commercial cost</td>
            <td align="right"><?
            $commercial_cost_per=($other_costing_arr[$job_id]['comm_cost']/$order_values)*100;
            if(is_infinite($commercial_cost_per) || is_nan($commercial_cost_per)) $commercial_cost_per=0;
            
            if($commercial_cost_per>0){ echo fn_number_format($commercial_cost_per,2); }
            $total_misc_per +=$commercial_cost_per;
            ?>            
            </td>
            <th align="right"><? if($other_costing_arr[$job_id]['comm_cost']>0) { echo fn_number_format($other_costing_arr[$job_id]['comm_cost'],2);} ?></th>
        </tr>
        <tr>
            <td>Other costs</td>
            <td align="right"><?
            $other_cost_per=($total_other_cost/$order_values)*100;
            if(is_infinite($other_cost_per) || is_nan($other_cost_per)) $other_cost_per=0;
            
            if($other_cost_per>0){ echo fn_number_format($other_cost_per,2);}
            $total_misc_per +=$other_cost_per;
            ?>            
            </td>
            <th align="right"><? if($total_other_cost>0){ echo fn_number_format($total_other_cost,2);} ?></th>
        </tr>
        <tr>
            <th>MISC/Others Cost Sub Total</th>
            <th align="right"><? if($total_misc_per>0){ echo fn_number_format($total_misc_per,2).'%'; }  ?></th>
            <th align="right"><? if($misc_cost>0){ echo '&dollar;'.fn_number_format($misc_cost,2); } ?></th>
        </tr>
    </table>
    <div id="div_size_color_matrix" style="float:left; max-width:1000; font-family: 'Arial Narrow', Arial, sans-serif;">
        <fieldset id="div_size_color_matrix" style="max-width:1000;">
			<?
            $color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
            $size_library=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
            $nameArray_size=sql_select( "select  size_number_id, min(color_size_id) as id,  min(size_order) as size_order from wo_po_color_size_his where po_break_down_id in(".$txt_order_no_id.") and  job_no_mst='$txt_job_no' and approved_no=$revised_no and is_deleted=0 and status_active=1 group by size_number_id order by size_order");
            //echo "select  size_number_id,min(id) as id, min(size_order) as size_order from wo_po_color_size_breakdown where po_break_down_id in(".$txt_order_no_id.") and  job_no_mst=$txt_job_no and is_deleted=0 and status_active=1 group by size_number_id order by size_order"; die;
            ?>
            <legend>Size and Color Breakdown</legend>
                <table class="rpt_table"  border="1" align="left" cellpadding="0" width="750" cellspacing="0" rules="all" >
                    <tr>
                        <td style="border:1px solid black"><strong>Color/Size</strong></td>
                        <?          
                        foreach($nameArray_size  as $result_size)
                        { ?>
                        <td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
                        <? } ?>       
                        <td style="border:1px solid black; width:130px" align="center"><strong> Total Order Qty(Pcs)</strong></td>
                        <td style="border:1px solid black; width:80px" align="center"><strong> Excess %</strong></td>
                        <td style="border:1px solid black; width:130px" align="center"><strong> Total Plan Cut Qty(Pcs)</strong></td>
                    </tr>
                    <?
                    $color_size_order_qnty_array=array(); $color_size_qnty_array=array();  $size_tatal=array(); $size_tatal_order=array();
                    for($c=0;$c<count($gmts_item); $c++)
                    {
						$item_size_tatal=array(); $item_size_tatal_order=array(); $item_grand_total=0; $item_grand_total_order=0;
						$nameArray_color=sql_select( "select color_number_id, min(color_size_id) as id,min(color_order) as color_order from wo_po_color_size_his where item_number_id=$gmts_item[$c] and po_break_down_id in(".$txt_order_no_id.")  and approved_no=$revised_no and is_deleted=0 and status_active=1 group by color_number_id order by color_order");
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
                                $color_total=0; $color_total_order=0;
                                foreach($nameArray_size  as $result_size)
                                {
									$nameArray_color_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as  order_quantity from wo_po_color_size_his where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$result_color[csf('color_number_id')]." and approved_no=$revised_no and item_number_id=$gmts_item[$c] and  status_active=1 and is_deleted =0");                          
									foreach($nameArray_color_size_qnty as $result_color_size_qnty)
									{
										?>
										<td style="border:1px solid black; text-align:right">
										<? 
										if($result_color_size_qnty[csf('plan_cut_qnty')]!= "")
										{
											echo fn_number_format($result_color_size_qnty[csf('order_quantity')],0);
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
										else echo " ";
										?>
										</td>
										<?   
									}
                                }
                                ?>
                                <td style="border:1px solid black; text-align:right"><? if(round($color_total_order)>0){ echo fn_number_format(round($color_total_order),0);} ?></td>
                                <td style="border:1px solid black; text-align:right"><? $excexss_per=($color_total-$color_total_order)/$color_total_order*100; if(round($excexss_per)>0){ echo fn_number_format($excexss_per,2)." %";} ?></td>
                                <td style="border:1px solid black; text-align:right"><? if(round($color_total)>0){ echo fn_number_format(round($color_total),0);} ?></td>
                            </tr>
                            <?
						}
						?>
                        <tr>
                            <td align="center" style="border:1px solid black"><strong>Sub Total</strong></td>
                            <?
                            foreach($nameArray_size  as $result_size)
                            {
								?><td style="border:1px solid black;  text-align:right"><? echo $item_size_tatal_order[$result_size[csf('size_number_id')]];  ?></td><?
                            }
                            ?>
                            <td  style="border:1px solid black;  text-align:right"><? if(round($item_grand_total_order)>0){ echo fn_number_format(round($item_grand_total_order),0); } ?></td>
                            <td  style="border:1px solid black;  text-align:right"><? $excess_item_gra_tot=($item_grand_total-$item_grand_total_order)/$item_grand_total_order*100; if($excess_item_gra_tot>0){echo fn_number_format($excess_item_gra_tot,2)." %"; } ?></td>
                            <td  style="border:1px solid black;  text-align:right"><?  if(round($item_grand_total)>0){echo fn_number_format(round($item_grand_total),0); } ?></td>
						</tr>
						<?
                    }
                    ?>
                    <tr>
                    	<td style="border:1px solid black" align="center" colspan="<? echo count($nameArray_size)+3; ?>"><strong>&nbsp;</strong></td>
                    </tr>
                    <tr>
                        <td align="center" style="border:1px solid black"><strong>Grand Total</strong></td>
                        <?
                        foreach($nameArray_size  as $result_size)
                        {
                        	?><td style="border:1px solid black;  text-align:right"><? echo $size_tatal_order[$result_size[csf('size_number_id')]]; ?></td><?
                        }
                        ?>
                        <td style="border:1px solid black;  text-align:right"><? if(round($grand_total_order)>0){ echo fn_number_format(round($grand_total_order),0); } ?></td>
                        <td style="border:1px solid black;  text-align:right"><? $excess_gra_tot= ($grand_total-$grand_total_order)/$grand_total_order*100; if($excess_gra_tot>0) { echo fn_number_format($excess_gra_tot,2)." %"; } ?></td>
                        <td style="border:1px solid black;  text-align:right"><?  if(round($grand_total)>0) { echo fn_number_format(round($grand_total),0); } ?></td>
                    </tr>
            </table>
        </fieldset>
    </div>
    <br/><br/>
    <div>
    <br/>
	<?
    $width=990; $padding_top = 70; $prepared_by='';
    $sql = sql_select("select designation,name,activities,prepared_by from variable_settings_signature where report_id=109 and company_id=$cbo_company_name order by sequence_no");
    
    if($sql[0][csf("prepared_by")]==1){
		list($prepared_by,$activities)=explode('**',$prepared_by);
		$sql_2[100] = array ( DESIGNATION => 'Prepared By' ,NAME => $prepared_by, ACTIVITIES =>$activities, PREPARED_BY => 0 );
		$sql=$sql_2+$sql;
    }
    
    $count = count($sql);
    $td_width = floor($width / $count);
    $standard_width = $count * 120;
    if ($standard_width > $width) $td_width = 120;
    
	$no_coloumn_per_tr = floor($width / $td_width);
	$i = 1;
	if ($count == 0) {$message = "<b>Note: This is Software Generated Copy , Signature is not Required.</b>";}
	echo '<table id="signatureTblId" width="' . $width . '" style="padding-top:' . $padding_top . 'px;"><tr><td width="100%" height="' . $padding_top . '" colspan="' . $count . '">' . $message . '</td></tr><tr>';
	foreach ($sql as $row) {
		echo '<td width="' . $td_width . '" align="center" valign="top">
		<strong>' . $row[csf("activities")] . '</strong><br>
		<strong style="text-decoration:overline">' . $row[csf("designation")] . "</strong><br>" . $row[csf("name")] . '</td>';
		if ($i % $no_coloumn_per_tr == 0) {
			echo '</tr><tr><td width="100%" height="70" colspan="' . $no_coloumn_per_tr . '"></td></tr>';
		}
		$i++;
	}
	echo '</tr></table>';
	?>
	</div>
	<?
	disconnect($con);
	exit();
}
?>
