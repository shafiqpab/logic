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



	function sortByMarginPercent($a, $b) {
		return $a['marginper'] > $b['marginper'];
	}





if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$sequence_no='';
	
	$company_name=str_replace("'","",$cbo_company_name);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$user_id=($txt_alter_user_id !='')?$txt_alter_user_id:$user_id;
	
	if($company_name>0){$all_company_arr[$company_name]=$company_name;}
	else{
		$all_company_arr=return_library_array( "select company_id, company_id from electronic_approval_setup where page_id=$menu_id and user_id=$user_id and is_deleted=0",'company_id','company_id');
	}
	
	
		//print_r($all_company_arr); 
	
		//unset($all_company_arr[18]);
		//unset($all_company_arr[1]);
		//unset($all_company_arr[3]);

	
?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:1850px; margin-top:10px">
        <legend>Pre-Costing Approval V2</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1850" class="rpt_table" >
                <thead>
                    <th width="40">&nbsp;</th>
                    <th width="30">SL</th>
                    <th width="50">Company</th>
                    <th width="50">Job No</th>
                    <th width="70">Internal Ref.</th>
                    <th width="110">Buyer</th>
                    <th width="40">Year</th>
                    <th width="130">Style Ref.</th>
                    <th width="60">G.Margin %</th>
                    <th width="70">Net Margin %</th>
                    <th width="70">Costing Date</th>
                    <th width="70">Ship Start</th>
                    <th width="70">Ship End</th>
                    <th width="70">Job Qty(Pcs)</th>
                    <th width="60">Avg . Rate</th>
                    <th width="80">Total Value</th>
                    <th width="60">Yarn %</th>
                    <th width="60">Trims %</th>
                    <th width="60">B2B %</th>
                    <th width="60">MS Cost %</th>
                    <th width="60">CM %</th>
					<th width="100">Refusing Cause</th>
                    <th width="140">Unapproved Request</th>
                    <th width="65">Insert By</th>
                    <th>Approved Date</th>
                </thead>
            </table>
            <div style="width:1850px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1832" class="rpt_table" id="tbl_list_search">
             <tbody>
<?

	$i=1;
	foreach($all_company_arr as $company_name=>$company){
	
 
	//echo $txt_internal_ref; echo $txt_file_no;
	
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
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and is_deleted=0");
	$buyer_ids_array = array();
	$buyerData = sql_select("select user_id, sequence_no, buyer_id from electronic_approval_setup where company_id=$company_name and page_id=$menu_id and is_deleted=0 ");
	foreach($buyerData as $row)
	{
		$buyer_ids_array[$row[csf('user_id')]]['u']=$row[csf('buyer_id')];
		$buyer_ids_array[$row[csf('sequence_no')]]['s']=$row[csf('buyer_id')];
	}
	//print_r($buyer_ids_array);
	// if($user_sequence_no=="")
	// {
	// 	echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Pre-Costing.</font>";
	// 	die;
	// }
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
		$sql="select b.id,b.garments_nature, a.quotation_id, a.job_no_prefix_num, $year_cond, a.id as job_id, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, b.approved, b.inserted_by, c.id as approval_id, c.sequence_no, c.approved_by, c.id as approval_id, min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date, a.job_quantity, (a.job_quantity*a.total_set_qnty) as job_qty_pcs, a.total_price, $internalRefCond as internalRef, $fileNoCond as fileNo
			      from wo_pre_cost_mst b, wo_po_details_master a, approval_history c, wo_po_break_down d
				  where b.id=c.mst_id and c.entry_form=15 and a.job_no=b.job_no and a.company_name=$company_name and a.job_no=d.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.current_approval_status=1 and b.ready_to_approved=1 and b.is_deleted=0 and b.approved in (1,3) $buyer_id_cond $date_cond $job_no_cond $file_no_cond $internal_ref_cond $sequence_no_cond group by b.id, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby, a.id, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, b.approved,b.garments_nature, b.inserted_by, c.id, c.sequence_no, c.approved_by, c.id, a.job_quantity, a.total_set_qnty, a.total_price";
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

			 //$sql="select b.id,a.quotation_id,a.job_no_prefix_num,$year_cond,a.job_no, a.buyer_name, a.style_ref_no,b.costing_date,'0' as approval_id, b.approved,b.inserted_by from wo_pre_cost_mst b,  wo_po_details_master a where a.job_no=b.job_no and a.company_name=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 and b.approved=2 $buyer_id_cond $buyer_id_cond2 $date_cond $job_no_cond $job_year_cond";

			$sql="select b.id,b.garments_nature, a.quotation_id, a.job_no_prefix_num, $year_cond, a.id as job_id, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, '0' as approval_id, b.approved, b.inserted_by, b.entry_from, min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date, a.job_quantity, (a.job_quantity*a.total_set_qnty) as job_qty_pcs, a.total_price, $internalRefCond as internalRef, $fileNoCond as fileNo from wo_pre_cost_mst b,  wo_po_details_master a ,wo_po_break_down d  where a.job_no=b.job_no and a.job_no=d.job_no_mst and a.company_name=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 and b.approved=2 $buyer_id_cond $buyer_id_cond2 $date_cond $job_no_cond $job_year_cond $internal_ref_cond $file_no_cond group by b.id, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby,  a.id, a.job_no, a.buyer_name, a.style_ref_no,b.garments_nature, b.costing_date, '0', b.approved, b.inserted_by, b.entry_from, a.job_quantity, a.total_set_qnty, a.total_price";
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
			$pre_cost_id_sql="select distinct (mst_id) as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_name and b.sequence_no in ($sequence_no_by_no) and b.entry_form=15 and b.current_approval_status=1 $buyer_id_cond3 $seqCond
			union
			select distinct (mst_id) as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_name and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=15 and b.current_approval_status=1 $buyer_id_cond3";
			
			$bResult=sql_select($pre_cost_id_sql);
			foreach($bResult as $bRow)
			{
				$pre_cost_id.=$bRow[csf('pre_cost_id')].",";
			}

			$pre_cost_id=chop($pre_cost_id,',');

			$pre_cost_id_app_sql=sql_select("select b.mst_id as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c
			where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_name and b.sequence_no=$user_sequence_no and b.entry_form=15 and a.ready_to_approved=1 and b.current_approval_status=1");

            //   echo $pre_cost_id_app_sql; die();
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

			$sql="select b.id,b.garments_nature, a.quotation_id, a.job_no_prefix_num, $year_cond, a.id as job_id, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, 0 as approval_id, b.approved, b.inserted_by, b.entry_from, min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date, a.job_quantity, (a.job_quantity*a.total_set_qnty) as job_qty_pcs, a.total_price, $internalRefCond as internalRef, $fileNoCond as fileNo
			from wo_pre_cost_mst b, wo_po_details_master a, wo_po_break_down d
			where a.job_no=b.job_no and a.job_no=d.job_no_mst and a.company_name=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 and b.approved in (0,2) $buyer_id_cond $date_cond $pre_cost_id_cond $buyerIds_cond $buyer_id_cond2 $job_no_cond $file_no_cond $internal_ref_cond group by b.id,b.garments_nature, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby, a.id, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, b.approved, b.inserted_by, b.entry_from, a.job_quantity, a.total_set_qnty, a.total_price";
			//   echo $sql;die;
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
				select b.id,b.garments_nature, a.quotation_id, a.job_no_prefix_num, $year_cond, a.id as job_id, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, 0 as approval_id, b.approved, b.inserted_by, b.entry_from, min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date, a.job_quantity, (a.job_quantity*a.total_set_qnty) as job_qty_pcs, a.total_price, $internalRefCond as internalRef, $fileNoCond as fileNo
				from wo_pre_cost_mst b, wo_po_details_master a, wo_po_break_down d
				where a.job_no=b.job_no and a.job_no=d.job_no_mst and a.company_name=$company_name  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 and b.approved in(1,3) $pre_cost_id_cond2 $buyer_id_cond $buyer_id_cond2 $date_cond $job_no_cond $file_no_cond $internal_ref_cond group by b.id, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby, a.id, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, b.approved, b.inserted_by,b.garments_nature, b.entry_from, a.job_quantity, a.total_set_qnty, a.total_price";

				// echo $sql;die();
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

			$sql="select b.id,b.garments_nature, a.quotation_id, a.job_no_prefix_num, $year_cond, a.id as job_id, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, b.approved, b.inserted_by, c.id as approval_id, c.sequence_no, c.approved_by, c.id as approval_id, b.entry_from, min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date, a.job_quantity, (a.job_quantity*a.total_set_qnty) as job_qty_pcs, a.total_price, $internalRefCond as internalRef, $fileNoCond as fileNo
			from wo_pre_cost_mst b, wo_po_details_master a, approval_history c, wo_po_break_down d
			where b.id=c.mst_id and c.entry_form=15 and a.job_no=b.job_no and a.company_name=$company_name and a.job_no=d.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.current_approval_status=1 and b.ready_to_approved=1 and b.is_deleted=0 and b.approved in(1,3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $date_cond $job_no_cond $file_no_cond $internal_ref_cond group by  b.id, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby, a.id, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date,b.garments_nature, b.approved, b.inserted_by, c.id, c.sequence_no, c.approved_by, b.entry_from, a.job_quantity, a.total_set_qnty, a.total_price ";
			//echo $sql; die;
		}
		 //echo $sql; die;
	}
	else
	{ 
		$buyer_ids=$buyer_ids_array[$user_id]['u'];
		if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";

		$sequence_no_cond=" and c.sequence_no='$user_sequence_no'";

		$sql="select b.id,b.garments_nature, a.quotation_id, a.job_no_prefix_num, $year_cond, a.id as job_id, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, b.approved, b.inserted_by, c.id as approval_id, c.sequence_no, c.approved_date, c.approved_by, c.id as approval_id, b.entry_from, min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date, a.job_quantity, (a.job_quantity*a.total_set_qnty) as job_qty_pcs, a.total_price, $internalRefCond as internalRef, $fileNoCond as fileNo
			      from wo_pre_cost_mst b, wo_po_details_master a, approval_history c, wo_po_break_down d
				  where b.id=c.mst_id and c.entry_form=15 and a.job_no=b.job_no and a.company_name=$company_name and a.job_no=d.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.current_approval_status=1 and b.ready_to_approved=1 and b.is_deleted=0 and b.approved in (1,3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $date_cond $job_no_cond $file_no_cond $internal_ref_cond group by b.id, a.quotation_id,b.garments_nature, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby, a.id, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, b.approved, b.inserted_by, c.id, c.sequence_no, c.approved_by, c.id, c.approved_date, b.entry_from, a.job_quantity, a.total_set_qnty, a.total_price ";
	}
	//  echo $sql; die;
	
	
	$nameArray=sql_select( $sql );
	$jobFobValue_arr=array(); $jobIds="";
	foreach ($nameArray as $row)
	{
		$jobFobValue_arr[$row[csf('job_no')]]=$row[csf('total_price')];
		if($jobIds=='') $jobIds=$row[csf('job_id')]; else $jobIds.=','.$row[csf('job_id')];
	}
	//echo $jobIds;die;
	$jobIds=implode(",",array_filter(array_unique(explode(",",$jobIds))));
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
	$bomDtlssql=sql_select( "select job_no, fabric_cost_percent, trims_cost_percent, embel_cost_percent, wash_cost_percent, cm_cost_percent, margin_pcs_set_percent,margin_bom_per from wo_pre_cost_dtls where status_active=1 and is_deleted=0 $jobId_cond");

	$sql="select job_no, fabric_cost_percent, trims_cost_percent, embel_cost_percent, wash_cost_percent, cm_cost_percent, margin_pcs_set_percent,margin_bom_per from wo_pre_cost_dtls where status_active=1 and is_deleted=0 $jobId_cond";

	//echo $sql;die();
	foreach ($bomDtlssql as $row)
	{
		$bomDtls_arr[$row[csf('job_no')]]['trimper']=$row[csf('trims_cost_percent')];
		$bomDtls_arr[$row[csf('job_no')]]['cm']=$row[csf('cm_cost_percent')];
		$bomDtls_arr[$row[csf('job_no')]]['ms']=$row[csf('fabric_cost_percent')]+$row[csf('trims_cost_percent')]+$row[csf('embel_cost_percent')]+$row[csf('wash_cost_percent')];
		$bomDtls_arr[$row[csf('job_no')]]['margin']=$row[csf('margin_pcs_set_percent')];
		$bomDtls_arr[$row[csf('job_no')]]['net_margin']=$row[csf('margin_bom_per')];
	}
// 	echo "<pre>";
// 	print_r($bomDtls_arr); 
//    echo "</pre>";die();

	unset($bomDtlssql);
	
	if($jobIds!="")
	{
		$condition= new condition();
		$condition->company_name("=$company_name");
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
		$yarn= new yarn($condition);
		//echo $yarn->getQuery();die;
		$yarn_data_array=$yarn->getJobWiseYarnAmountArray();
		$fabric= new fabric($condition);
		$fabric_amount=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
		$conversion= new conversion($condition);
		$conv_amount_arr=$conversion->getAmountArray_by_jobAndProcess();
	
	}



 
	
	$sql_fabric = "select id, job_no, uom, fabric_source from wo_pre_cost_fabric_cost_dtls where status_active=1 and is_deleted=0 $jobId_cond";
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
	unset($data_arr_fabric);

	$sql_unapproved=sql_select("select * from fabric_booking_approval_cause where  entry_form=15 and approval_type=2 and is_deleted=0 and status_active=1");
	$unapproved_request_arr=array();
	foreach($sql_unapproved as $rowu)
	{
		$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
	}

	//Pre cost button---------------------------------
	$print_report_format_ids = return_field_value("format_id","lib_report_template","template_name=".$company_name." and module_id=2 and report_id in (43) and is_deleted=0 and status_active=1");
	$format_ids=explode(",",$print_report_format_ids);
	$row_id=$format_ids[0];
	
	

	//Order Wise Budget Report button---------------------------------
	$print_report_format_ids2 = return_field_value("format_id","lib_report_template","template_name=".$company_name." and module_id=11 and report_id=18 and is_deleted=0 and status_active=1");
	$format_ids2=explode(",",$print_report_format_ids2);
	$row_id2=$format_ids2[0];
	$print_report_format_ids_wvn = return_field_value("format_id","lib_report_template","template_name=".$company_name." and module_id=2 and report_id in (122) and is_deleted=0 and status_active=1");
	$format_ids_wvn=explode(",",$print_report_format_ids_wvn);
	$row_id_wvn=$format_ids_wvn[0];
	
 	
 
                        $ii=1;
						$aop_cost_arr=array(35,36,37,40);
						$app_data_arr=array();
						//echo count($nameArray);die;
						foreach ($nameArray as $row)
						{
							if ($ii%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
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
 
							$function2="generat_print_report($type,$company_name,0,'','',{$row[csf('job_no_prefix_num')]},'','','',".$row[csf('year')].",0,1,'','','','')";
							//{$row[csf('buyer_name')]}
							//
							//echo $row_id.'DS';
							if($print_cond==1)
							{
								if($row_id==50 && $row[csf('garments_nature')]==2){$action='preCostRpt'; } //report_btn_1;
								else if($row_id==51 && $row[csf('garments_nature')]==2){$action='preCostRpt2';} //report_btn_2;
								else if($row_id==52 && $row[csf('garments_nature')]==2){$action='bomRpt';} //report_btn_3;
								else if($row_id==63 && $row[csf('garments_nature')]==2){$action='bomRpt2';} //report_btn_4;
								else if($row_id==156 && $row[csf('garments_nature')]==2){$action='accessories_details';} //report_btn_5;
								else if($row_id==157 && $row[csf('garments_nature')]==2){$action='accessories_details2';} //report_btn_6;
								else if($row_id==158 && $row[csf('garments_nature')]==2){$action='preCostRptWoven';} //report_btn_7;
								else if($row_id==159 && $row[csf('garments_nature')]==2){$action='bomRptWoven';} //report_btn_8;
								else if($row_id==170 && $row[csf('garments_nature')]==2){$action='preCostRpt3';} //report_btn_9;
								else if($row_id==171 && $row[csf('garments_nature')]==2){$action='preCostRpt4';} //report_btn_10;
								else if($row_id==173 && $row[csf('garments_nature')]==2){$action='preCostRpt5';} //report_btn_10;
								else if($row_id==211 && $row[csf('garments_nature')]==2){$action='mo_sheet';}
								else if($row_id==142 && $row[csf('garments_nature')]==2){$action='preCostRptBpkW';}
								else if($row_id==197 && $row[csf('garments_nature')]==2){$action='bomRpt3';}
								else if($row_id==192 && $row[csf('garments_nature')]==2){$action='checkListRpt';}
								else if($row_id==221 && $row[csf('garments_nature')]==2){$action='fabric_cost_detail';}
								if($row_id_wvn==311 && $row[csf('garments_nature')]==3){$action='bom_epm_woven';}
								else if($row_id_wvn==313 && $row[csf('garments_nature')]==3){$action='mkt_source_cost';}
								else if($row_id_wvn==761 && $row[csf('entry_from')]==158 && $row[csf('garments_nature')]==3){$action='bom_pcs_woven';}
								else if($row_id_wvn==159 && $row[csf('entry_from')]==158 && $row[csf('garments_nature')]==3) {$action='bomRptWoven';}			
								else if($row_id==238){$action='summary';}
								else if($row_id==215){$action='budget3_details';}
								else if($row_id==730){$action='budgetsheet';}
								else if($row_id==769){$action='preCostRpt7';}
								else if($row_id==63){$action='bomRpt2';}
								else if($row_id==498){$action='preCostRpt10';}
								else if($row_id==235){$action='preCostRpt2';}
								else if($row_id==800){$action='preCostRpt11';}
								else {$action='';}

								$function="generate_worder_report('".$action."','".$row[csf('job_no')]."',".$company_name.",".$row[csf('buyer_name')].",'".$row[csf('style_ref_no')]."','".$row[csf('costing_date')]."',".$row[csf('entry_from')].",'".$row[csf('quotation_id')]."','".$row[csf('garments_nature')]."');"; 
								
								$jobavgRate=0; $int_ref = ""; $file_numbers = "";
								$jobavgRate=$row[csf('total_price')]/$row[csf('job_quantity')];
								if($db_type==2) $row[csf('internalRef')]= $row[csf('internalRef')]->load();
								//if($db_type==2) $row[csf('fileNo')]= $row[csf('fileNo')]->load();
								
								$int_ref=implode(",",array_unique(explode(",",chop($row[csf('internalRef')],","))));
								//$file_numbers=implode(",",array_unique(explode(",",chop($row[csf('fileNo')],",")))); 
								$yarnPercent=$trimPercent=$fabpurchase_per=$aopamt=$yarn_dyeingAmt=$yarn_dyeingPer=$msper=$aopPer=$cmper=$marginper=0;
								$yarnPercent=($yarn_data_array[$row[csf('job_no')]]/$row[csf('total_price')])*100;
								$trimPercent=$bomDtls_arr[$row[csf('job_no')]]['trimper'];
								
								$fabpurchase_per=($fabricPurchesamt_arr[$row[csf('job_no')]]['fabpur']/$row[csf('total_price')])*100;
								
								$yarn_dyeingAmt=array_sum($conv_amount_arr[$row[csf('job_no')]][30]);
								$yarn_dyeingPer=($yarn_dyeingAmt/$row[csf('total_price')])*100;
								
								foreach($aop_cost_arr as $aop_process_id)
								{
									$aopamt+=array_sum($conv_amount_arr[$row[csf('job_no')]][$aop_process_id]);
								}
								$aopPer=($aopamt/$row[csf('total_price')])*100;
								
								$btwob_per=$yarnPercent+$fabpurchase_per+$trimPercent+$yarn_dyeingPer+$aopPer;
								
								$msper=$bomDtls_arr[$row[csf('job_no')]]['ms'];
								$cmper=$bomDtls_arr[$row[csf('job_no')]]['cm'];
								$marginper=$bomDtls_arr[$row[csf('job_no')]]['margin'];
								$net_margin=$bomDtls_arr[$row[csf('job_no')]]['net_margin'];
								if(empty($marginper) || $marginper=='')
								{
									$marginper=0;
								}

								$app_data_arr[$ii]['booking_id']=$value;
								$app_data_arr[$ii]['booking_no']=$row[csf('job_no')];
								$app_data_arr[$ii]['booking_no']=$row[csf('id')];
								$app_data_arr[$ii]['approval_id']=$row[csf('approval_id')];
								$app_data_arr[$ii]['no_joooob']=$ii;
								$app_data_arr[$ii]['no_joooob_id']=strtoupper($row[csf('job_no')]);
								$app_data_arr[$ii]['cm_cost_id']=$cm_cost;
								$app_data_arr[$ii]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
								$app_data_arr[$ii]['function']=$function;
								$app_data_arr[$ii]['int_ref']=$int_ref;
								$app_data_arr[$ii]['buyer_id']=$row[csf('buyer_name')];
								$app_data_arr[$ii]['buyer_name']=$buyer_arr[$row[csf('buyer_name')]];
								$app_data_arr[$ii]['year']=$row[csf('year')];
								$app_data_arr[$ii]['style_ref_no']=$row[csf('style_ref_no')];
								$app_data_arr[$ii]['function2']=$function2;
								$app_data_arr[$ii]['costing_date']=$row[csf('costing_date')];
								$app_data_arr[$ii]['minship_date']=$row[csf('minship_date')];
								$app_data_arr[$ii]['maxship_date']=$row[csf('maxship_date')];
								$app_data_arr[$ii]['job_qty_pcs']=$row[csf('job_qty_pcs')];
								$app_data_arr[$ii]['jobavgRate']=$jobavgRate;
								$app_data_arr[$ii]['total_price']=$row[csf('total_price')];
								$app_data_arr[$ii]['yarnPercent']=$yarnPercent;
								$app_data_arr[$ii]['trimPercent']=$trimPercent;
								$app_data_arr[$ii]['btwob_per']=$btwob_per;
								$app_data_arr[$ii]['msper']=$msper;
								$app_data_arr[$ii]['cmper']=$cmper;
								$app_data_arr[$ii]['marginper']=$marginper;
								$app_data_arr[$ii]['net_margin']=$net_margin;
								$app_data_arr[$ii]['approval_type']=$approval_type;
								$app_data_arr[$ii]['unapproved_request']=$unapproved_request_arr[$value];
								$app_data_arr[$ii]['inserted_by']=ucfirst($user_arr[$row[csf('inserted_by')]]);
								$app_data_arr[$ii]['approved_date']=$row[csf('approved_date')];
								$app_data_arr[$ii]['all_approval_id']=$all_approval_id;
								$app_data_arr[$ii]['print_cond']=$print_cond;
								
								$ii++;
							}
						}
						unset($nameArray);





						usort($app_data_arr, 'sortByMarginPercent');
						
						foreach ($app_data_arr as $index=>$row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
								$value=$row['booking_id'];
								$booking_no=$row['booking_no'];
								$approval_id=$row['approval_id'];
								$no_joooob=$row['no_joooob'];
								$no_joooob_id=$row['no_joooob_id'];
								$cm_cost_id=$row['cm_cost_id'];
								$function=$row['function'];
								$job_no_prefix_num=$row['job_no_prefix_num'];
								$int_ref=$row['int_ref'];
								$buyer_name=$row['buyer_name'];
								$year=$row['year'];
								$function2=$row['function2'];
								$style_ref_no=$row['style_ref_no'];
								$costing_date=$row['costing_date'];
								$minship_date=$row['minship_date'];
								$maxship_date=$row['maxship_date'];
								$job_qty_pcs=$row['job_qty_pcs'];
								$jobavgRate=$row['jobavgRate'];
								$total_price=$row['total_price'];
								$yarnPercent=$row['yarnPercent'];
								$trimPercent=$row['trimPercent'];
								$btwob_per=$row['btwob_per'];
								$msper=$row['msper'];
								$cmper=$row['cmper'];
								$marginper=$row['marginper'];
								$net_margin=$row['net_margin'];
								$approval_type=$row['approval_type'];
								$unapproved_request=$row['unapproved_request'];
								$inserted_by=$row['inserted_by'];
								$approved_date=$row['approved_date'];
								$all_approval_id=$row['all_approval_id'];
								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>" align="center">
                                	<td width="40" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<?=$i;?>" />
                                        <input id="booking_id_<?=$i;?>" name="booking_id[]" type="hidden" value="<?=$value; ?>" />
                                        <input id="booking_no_<?=$i;?>" name="booking_no[]" type="hidden" value="<?=$booking_no; ?>" />
                                        <input id="approval_id_<?=$i;?>" name="approval_id[]" type="hidden" value="<?=$approval_id; ?>" />
                                        <input id="<?=$no_joooob_id; ?>" name="no_joooob[]" type="hidden" value="<?=$i;?>" />
                                        <input id="cm_cost_id_<?=$i;?>" name="cm_cost_id[]" style="width:20px;" type="hidden" value="<?=$cm_cost_id; ?>" />
                                        
                                        <input id="mst_id_company_id_<?=$i;?>" name="mst_id_company_id[]" type="hidden" value="<?=$booking_no.'*'.$value.'*'.$company_name; ?>" />
                                        
                                        
                                    </td>
									<td width="30" align="center"><?=$i; ?></td>
                                    <td width="50" align="center"><?=$company_arr[$company_name];?></td>
									<td width="50"><a href='##' onclick="<?=$function; ?>"><?=$job_no_prefix_num; ?></a></td>
                                    <td width="70" style="word-break:break-all;"><?=$int_ref; ?></td>
                                    <td width="110" style="word-break:break-all;"><?=$buyer_name; ?></td>
                                    <td width="40" style="word-break:break-all;"><?=$year; ?></td>
                                    <td width="130" align="center" style="word-break:break-all;"><a href='##' onclick="<?=$function2; ?>"><?=$style_ref_no; ?></a></td>
                                     <td width="60" align="right" style="word-break:break-all;"><?=number_format($marginper,2); ?></td>
									 <td width="70" align="right" style="word-break:break-all;"><?=number_format($net_margin,2); ?></td>
                                    <td width="70" align="center"><? if($costing_date!="0000-00-00") echo change_date_format($costing_date); ?>&nbsp;</td>
                                    <td align="center" width="70"><? if($minship_date!="0000-00-00") echo change_date_format($minship_date); ?>&nbsp;</td>
                                    <td align="center" width="70"><? if($maxship_date!="0000-00-00") echo change_date_format($maxship_date); ?>&nbsp;</td>
                                    <td width="70" align="right" style="word-break:break-all;"><?=number_format($job_qty_pcs); ?></td>
                                    <td width="60" align="right" style="word-break:break-all;"><?=number_format($jobavgRate,4); ?></td>
                                    <td width="80" align="right" style="word-break:break-all;"><?=number_format($total_price,2); ?></td>
                                    
                                    <td width="60" align="right" style="word-break:break-all;"><?=number_format($yarnPercent,2); ?></td>
                                    <td width="60" align="right" style="word-break:break-all;"><?=number_format($trimPercent,2); ?></td>
                                    <td width="60" align="right" style="word-break:break-all;"><?=number_format($btwob_per,2); ?></td>
                                    <td width="60" align="right" style="word-break:break-all;"><?=number_format($msper,2); ?></td>
                                    <td width="60" align="right" style="word-break:break-all;" id="tdCm_<?=$i;?>"><?=number_format($cmper,2); ?></td>
									

								<td width="120"> <input style="width:100px;" type="text" class="text_boxes"  name="txtCause_<? echo $value;?>" id="txtCause_<? echo $value;?>" placeholder="browse" onClick="openmypage_refusing_cause('requires/pre_costing_approval_v2_controller.php?action=refusing_cause_popup','Refusing Cause','<? echo $value;?>');" value="<? echo $row[csf('refusing_cause')];?>"/></td>
                                    
                                    <td width="140" style="word-break:break-all"><? if($approval_type==1) echo $unapproved_request; ?> </td>
                                    <td width="65" style="word-break:break-all;"><? echo $inserted_by;?>&nbsp;</td>
                                    <td align="center"><? if($approved_date!="0000-00-00") echo change_date_format($approved_date); ?>&nbsp;</td>
								</tr>
								<?
								$i++;
							

							if($all_approval_id!="")
							{
								$con = connect();
								$rID=sql_multirow_update("approval_history","current_approval_status",0,"id",$all_approval_id,1);
								//echo $rID."**";
							
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
								
								disconnect($con);
							}
						}
						
	
	}//end company loof;
	

     ?>
                    </tbody>
                </table>
            </div>
            <table align="left" cellspacing="0" cellpadding="0" border="0" rules="all" width="1732" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" ><input type="checkbox" id="all_check" onclick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onclick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/></td>
				</tfoot>
			</table>
        </fieldset>
    </form>
	<?
	
	
	exit();
}

if($action=="report_generate_previous_10698")
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
	$buyerData = sql_select("select user_id, sequence_no, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0 ");
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
		$sql="select b.id, a.quotation_id, a.job_no_prefix_num, $year_cond, a.id as job_id, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, b.approved, b.inserted_by, c.id as approval_id, c.sequence_no, c.approved_by, c.id as approval_id, min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date, a.job_quantity, (a.job_quantity*a.total_set_qnty) as job_qty_pcs, a.total_price, $internalRefCond as internalRef, $fileNoCond as fileNo
			      from wo_pre_cost_mst b, wo_po_details_master a, approval_history c, wo_po_break_down d
				  where b.id=c.mst_id and c.entry_form=15 and a.job_no=b.job_no and a.company_name=$company_name and a.job_no=d.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.current_approval_status=1 and b.ready_to_approved=1 and b.is_deleted=0 and b.approved in (1,3) $buyer_id_cond $date_cond $job_no_cond $file_no_cond $internal_ref_cond $sequence_no_cond group by b.id, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby, a.id, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, b.approved, b.inserted_by, c.id, c.sequence_no, c.approved_by, c.id, a.job_quantity, a.total_set_qnty, a.total_price";
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
		//echo $user_sequence_no.'--'.$min_sequence_no.'--'.$sequence_no; die;
		if($user_sequence_no==$min_sequence_no)
		{
			$buyer_ids = $buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";

			 //$sql="select b.id,a.quotation_id,a.job_no_prefix_num,$year_cond,a.job_no, a.buyer_name, a.style_ref_no,b.costing_date,'0' as approval_id, b.approved,b.inserted_by from wo_pre_cost_mst b,  wo_po_details_master a where a.job_no=b.job_no and a.company_name=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 and b.approved=2 $buyer_id_cond $buyer_id_cond2 $date_cond $job_no_cond $job_year_cond";

			$sql="select b.id, a.quotation_id, a.job_no_prefix_num, $year_cond, a.id as job_id, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, '0' as approval_id, b.approved, b.inserted_by, b.entry_from, min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date, a.job_quantity, (a.job_quantity*a.total_set_qnty) as job_qty_pcs, a.total_price, $internalRefCond as internalRef, $fileNoCond as fileNo from wo_pre_cost_mst b,  wo_po_details_master a ,wo_po_break_down d  where a.job_no=b.job_no and a.job_no=d.job_no_mst and a.company_name=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 and b.approved=2 $buyer_id_cond $buyer_id_cond2 $date_cond $job_no_cond $job_year_cond $internal_ref_cond $file_no_cond group by b.id, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby,  a.id, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, '0', b.approved, b.inserted_by, b.entry_from, a.job_quantity, a.total_set_qnty, a.total_price";
			 //echo $sql;die;
		}
		else if($sequence_no == "")
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";

			if($buyer_ids=="") $buyer_id_cond3=""; else $buyer_id_cond3=" and c.buyer_name in($buyer_ids)";

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
			$pre_cost_id_sql="select distinct (mst_id) as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_name and b.sequence_no in ($sequence_no_by_no) and b.entry_form=15 and b.current_approval_status=1 $buyer_id_cond3 $seqCond
			union
			select distinct (mst_id) as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_name and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=15 and b.current_approval_status=1 $buyer_id_cond3";
			$bResult=sql_select($pre_cost_id_sql);
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

			$sql="select b.id, a.quotation_id, a.job_no_prefix_num, $year_cond, a.id as job_id, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, 0 as approval_id, b.approved, b.inserted_by, b.entry_from, min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date, a.job_quantity, (a.job_quantity*a.total_set_qnty) as job_qty_pcs, a.total_price, $internalRefCond as internalRef, $fileNoCond as fileNo
			from wo_pre_cost_mst b, wo_po_details_master a, wo_po_break_down d
			where a.job_no=b.job_no and a.job_no=d.job_no_mst and a.company_name=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 and b.approved in (0,2) $buyer_id_cond $date_cond $pre_cost_id_cond $buyerIds_cond $buyer_id_cond2 $job_no_cond $file_no_cond $internal_ref_cond group by b.id, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby, a.id, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, b.approved, b.inserted_by, b.entry_from, a.job_quantity, a.total_set_qnty, a.total_price";
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
				select b.id, a.quotation_id, a.job_no_prefix_num, $year_cond, a.id as job_id, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, 0 as approval_id, b.approved, b.inserted_by, b.entry_from, min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date, a.job_quantity, (a.job_quantity*a.total_set_qnty) as job_qty_pcs, a.total_price, $internalRefCond as internalRef, $fileNoCond as fileNo
				from wo_pre_cost_mst b, wo_po_details_master a, wo_po_break_down d
				where a.job_no=b.job_no and a.job_no=d.job_no_mst and a.company_name=$company_name  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 and b.approved in(1,3) $pre_cost_id_cond2 $buyer_id_cond $buyer_id_cond2 $date_cond $job_no_cond $file_no_cond $internal_ref_cond group by b.id, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby, a.id, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, b.approved, b.inserted_by, b.entry_from, a.job_quantity, a.total_set_qnty, a.total_price";
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
					$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");// and bypass=1
				}
				else
				{
					$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");//bypass=1 and
				}
			}

			if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$sequence_no'";
			else $sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";

			$sql="select b.id, a.quotation_id, a.job_no_prefix_num, $year_cond, a.id as job_id, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, b.approved, b.inserted_by, c.id as approval_id, c.sequence_no, c.approved_by, c.id as approval_id, b.entry_from, min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date, a.job_quantity, (a.job_quantity*a.total_set_qnty) as job_qty_pcs, a.total_price, $internalRefCond as internalRef, $fileNoCond as fileNo
			from wo_pre_cost_mst b, wo_po_details_master a, approval_history c, wo_po_break_down d
			where b.id=c.mst_id and c.entry_form=15 and a.job_no=b.job_no and a.company_name=$company_name and a.job_no=d.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.current_approval_status=1 and b.ready_to_approved=1 and b.is_deleted=0 and b.approved in(1,3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $date_cond $job_no_cond $file_no_cond $internal_ref_cond group by  b.id, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby, a.id, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, b.approved, b.inserted_by, c.id, c.sequence_no, c.approved_by, b.entry_from, a.job_quantity, a.total_set_qnty, a.total_price ";
			//echo $sql; die;
		}
	}
	else
	{ 
		$buyer_ids=$buyer_ids_array[$user_id]['u'];
		if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";

		$sequence_no_cond=" and c.sequence_no='$user_sequence_no'";

		$sql="select b.id, a.quotation_id, a.job_no_prefix_num, $year_cond, a.id as job_id, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, b.approved, b.inserted_by, c.id as approval_id, c.sequence_no, c.approved_date, c.approved_by, c.id as approval_id, b.entry_from, min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date, a.job_quantity, (a.job_quantity*a.total_set_qnty) as job_qty_pcs, a.total_price, $internalRefCond as internalRef, $fileNoCond as fileNo
			      from wo_pre_cost_mst b, wo_po_details_master a, approval_history c, wo_po_break_down d
				  where b.id=c.mst_id and c.entry_form=15 and a.job_no=b.job_no and a.company_name=$company_name and a.job_no=d.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.current_approval_status=1 and b.ready_to_approved=1 and b.is_deleted=0 and b.approved in (1,3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $date_cond $job_no_cond $file_no_cond $internal_ref_cond group by b.id, a.quotation_id, a.quotation_id, a.job_no_prefix_num, $year_cond_groupby, a.id, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, b.approved, b.inserted_by, c.id, c.sequence_no, c.approved_by, c.id, c.approved_date, b.entry_from, a.job_quantity, a.total_set_qnty, a.total_price ";
	}
	//echo $sql; die;
	$nameArray=sql_select( $sql );
	$jobFobValue_arr=array(); $jobIds="";
	foreach ($nameArray as $row)
	{
		$jobFobValue_arr[$row[csf('job_no')]]=$row[csf('total_price')];
		if($jobIds=='') $jobIds=$row[csf('job_id')]; else $jobIds.=','.$row[csf('job_id')];
	}
	//echo $jobIds;die;
	$jobIds=implode(",",array_filter(array_unique(explode(",",$jobIds))));
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
	if($jobIds!="")
	{
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
	$yarn= new yarn($condition);
	//echo $yarn->getQuery(); die;
	$yarn_data_array=$yarn->getJobWiseYarnAmountArray();
	
	$fabric= new fabric($condition);
	$fabric_amount=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
	$conversion= new conversion($condition);
	$conv_amount_arr=$conversion->getAmountArray_by_jobAndProcess();
	}
	//echo $conversion->getQuery(); die;
	//print_r($conv_amount_arr);
	
	$sql_fabric = "select id, job_no, uom, fabric_source from wo_pre_cost_fabric_cost_dtls where status_active=1 and is_deleted=0 $jobId_cond";
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
	unset($data_arr_fabric);

	$sql_unapproved=sql_select("select * from fabric_booking_approval_cause where  entry_form=15 and approval_type=2 and is_deleted=0 and status_active=1");
	$unapproved_request_arr=array();
	foreach($sql_unapproved as $rowu)
	{
		$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
	}

	//Pre cost button---------------------------------
	$print_report_format_ids = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id in (43) and is_deleted=0 and status_active=1");
	$format_ids=explode(",",$print_report_format_ids);
	$row_id=$format_ids[0];
	

	//Order Wise Budget Report button---------------------------------
	$print_report_format_ids2 = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=11 and report_id=18 and is_deleted=0 and status_active=1");
	$format_ids2=explode(",",$print_report_format_ids2);
	$row_id2=$format_ids2[0];
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:1580px; margin-top:10px">
        <legend>Pre-Costing Approval V2</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1580" class="rpt_table" >
                <thead>
                    <th width="40">&nbsp;</th>
                    <th width="30">SL</th>
                    <th width="50">Job No</th>
                    <th width="70">Internal Ref.</th>
                    <th width="110">Buyer</th>
                    <th width="40">Year</th>
                    <th width="130">Style Ref.</th>
                    <th width="70">Costing Date</th>
                    <th width="70">Ship Start</th>
                    <th width="70">Ship End</th>
                    <th width="70">Job Qty(Pcs)</th>
                    <th width="60">Avg. Rate</th>
                    <th width="80">Total Value</th>
                    
                    <th width="60">Yarn %</th>
                    <th width="60">Trims %</th>
                    <th width="60">B2B %</th>
                    <th width="60">MS Cost %</th>
                    <th width="60">CM %</th>
                    <th width="60">Margin %</th>
                    
                    <th width="140">Unapproved Request</th>
                    <th width="65">Insert By</th>
                    <th>Approved Date</th>
                </thead>
            </table>
            <div style="width:1580px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1562" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
                        $i=1; //die;
						$aop_cost_arr=array(35,36,37,40);
						foreach ($nameArray as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$id=$row[csf('id')];
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
								if($row_id==50){$action='preCostRpt'; } //report_btn_1;
								else if($row_id==51){$action='preCostRpt2';} //report_btn_2;
								else if($row_id==52){$action='bomRpt';} //report_btn_3;
								else if($row_id==63){$action='bomRpt2';} //report_btn_4;
								else if($row_id==156){$action='accessories_details';} //report_btn_5;
								else if($row_id==157){$action='accessories_details2';} //report_btn_6;
								else if($row_id==158){$action='preCostRptWoven';} //report_btn_7;
								else if($row_id==159){$action='bomRptWoven';} //report_btn_8;
								else if($row_id==170){$action='preCostRpt3';} //report_btn_9;
								else if($row_id==171){$action='preCostRpt4';} //report_btn_10;
								else if($row_id==173){$action='preCostRpt5';} //report_btn_10;
								else if($row_id==211){$action='mo_sheet';}
								else if($row_id==142){$action='preCostRptBpkW';}
								else if($row_id==197){$action='bomRpt3';}
								else if($row_id==192){$action='checkListRpt';}
								else if($row_id==221){$action='fabric_cost_detail';}
								else if($row_id==238){$action='summary';}
								else if($row_id==215){$action='budget3_details';}
								else if($row_id==730){$action='budgetsheet';}
								else if($row_id==800){$action='preCostRpt11';}

								$function="generate_worder_report('".$action."','".$row[csf('job_no')]."',".$cbo_company_name.",".$row[csf('buyer_name')].",'".$row[csf('style_ref_no')]."','".$row[csf('costing_date')]."',".$row[csf('entry_from')].",'".$row[csf('quotation_id')]."');"; 
								
								$jobavgRate=0; $int_ref = ""; $file_numbers = "";
								$jobavgRate=$row[csf('total_price')]/$row[csf('job_quantity')];
								if($db_type==2) $row[csf('internalRef')]= $row[csf('internalRef')]->load();
								//if($db_type==2) $row[csf('fileNo')]= $row[csf('fileNo')]->load();
								
								$int_ref=implode(",",array_unique(explode(",",chop($row[csf('internalRef')],","))));
								//$file_numbers=implode(",",array_unique(explode(",",chop($row[csf('fileNo')],",")))); 
								$yarnPercent=$trimPercent=$fabpurchase_per=$aopamt=$yarn_dyeingAmt=$yarn_dyeingPer=$msper=$aopPer=$cmper=$marginper=0;
								$yarnPercent=($yarn_data_array[$row[csf('job_no')]]/$row[csf('total_price')])*100;
								$trimPercent=$bomDtls_arr[$row[csf('job_no')]]['trimper'];
								
								$fabpurchase_per=($fabricPurchesamt_arr[$row[csf('job_no')]]['fabpur']/$row[csf('total_price')])*100;
								
								$yarn_dyeingAmt=array_sum($conv_amount_arr[$row[csf('job_no')]][30]);
								$yarn_dyeingPer=($yarn_dyeingAmt/$row[csf('total_price')])*100;
								
								foreach($aop_cost_arr as $aop_process_id)
								{
									$aopamt+=array_sum($conv_amount_arr[$row[csf('job_no')]][$aop_process_id]);
								}
								$aopPer=($aopamt/$row[csf('total_price')])*100;
								
								$btwob_per=$yarnPercent+$fabpurchase_per+$trimPercent+$yarn_dyeingPer+$aopPer;
								
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
                                    <td width="130" align="center" style="word-break:break-all;"><a href='##' onclick="<?=$function2; ?>"><?=$row[csf('style_ref_no')]; ?></a></td>
                                    <td width="70" align="center"><? if($row[csf('costing_date')]!="0000-00-00") echo change_date_format($row[csf('costing_date')]); ?>&nbsp;</td>
                                    <td align="center" width="70"><? if($row[csf('minship_date')]!="0000-00-00") echo change_date_format($row[csf('minship_date')]); ?>&nbsp;</td>
                                    <td align="center" width="70"><? if($row[csf('maxship_date')]!="0000-00-00") echo change_date_format($row[csf('maxship_date')]); ?>&nbsp;</td>
                                    <td width="70" align="right" style="word-break:break-all;"><?=number_format($row[csf('job_qty_pcs')]); ?></td>
                                    <td width="60" align="right" style="word-break:break-all;"><?=number_format($jobavgRate,4); ?></td>
                                    <td width="80" align="right" style="word-break:break-all;"><?=number_format($row[csf('total_price')],2); ?></td>
                                    
                                    <td width="60" align="right" style="word-break:break-all;"><?=number_format($yarnPercent,2); ?></td>
                                    <td width="60" align="right" style="word-break:break-all;"><?=number_format($trimPercent,2); ?></td>
                                    <td width="60" align="right" style="word-break:break-all;"><?=number_format($btwob_per,2); ?></td>
                                    <td width="60" align="right" style="word-break:break-all;"><?=number_format($msper,2); ?></td>
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
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="1562" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" ><input type="checkbox" id="all_check" onclick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onclick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/></td>
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
			http.open("POST","pre_costing_approval_v2_controller.php",true);
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
		$get_history = sql_select("SELECT id from approval_history where mst_id='$quo_id' and entry_form =15 and current_approval_status=1");
		$id=return_next_id( "id", "refusing_cause_history", 1);
		$field_array = "id,entry_form,mst_id,refusing_reason,inserted_by,insert_date";
		$data_array = "(".$id.",15,".$quo_id.",'".$refusing_cause."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		//echo "10**insert into refusing_cause_history (".$field_array.") values ".$data_array; die;
		$field_array_update ="un_approved_by*un_approved_date*current_approval_status*un_approved_reason* updated_by*update_date";
		$data_array_update = "".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*".$refusing_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_insert("refusing_cause_history",$field_array,$data_array,1);
		$rID2=execute_query("update wo_pre_cost_mst set ready_to_approved=0 ,approved=0, updated_by = ".$_SESSION['logic_erp']['user_id']." , update_date = '".$pc_date_time."' where id='$quo_id'");
		if(count($get_history)>0)
		{
			$rID3=execute_query("update approval_history set un_approved_by=".$_SESSION['logic_erp']['user_id']." ,un_approved_date='".$pc_date_time."', current_approval_status =0, un_approved_reason= '".$refusing_cause."', updated_by = ".$_SESSION['logic_erp']['user_id']." , update_date = '".$pc_date_time."' where mst_id='$quo_id' and entry_form =15 and current_approval_status=1");
		}
		//echo "24444**".$rID.",".$rID2.",".$rID3;oci_rollback($con);
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
	//echo "10**". __LINE__; die;
	
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
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");

	$buyer_arr=return_library_array( "select b.id, a.buyer_name   from wo_pre_cost_mst b,  wo_po_details_master a where  a.job_no=b.job_no and a.is_deleted=0 and  a.status_active=1 and b.status_active=1 and  b.is_deleted=0 and b.id in ($booking_ids)", "id", "buyer_name"  );
	

	$mstSql = "select READY_TO_APPROVED from wo_pre_cost_mst where id in($booking_ids)";
	$mstSqlRes = sql_select($mstSql);
	foreach($mstSqlRes as $rows){
		if($rows['READY_TO_APPROVED'] != 1){echo "21**".implode(',',$appQutationArr);die;}
	}
	
	
	if($approval_type==2)
	{
		$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id_approval and is_deleted=0 and bypass=2");
		$min_sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0 and bypass=2");
		//echo "10**".$min_sequence_no.'--'.$user_sequence_no; die;
		if($min_sequence_no == $user_sequence_no)
		{
			$sql = sql_select("select b.buyer_id as buyer_id,b.sequence_no from electronic_approval_setup b where b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no < $user_sequence_no and b.is_deleted=0 and bypass=2 group by b.buyer_id,b.sequence_no order by b.sequence_no ASC");
			//echo "10**select b.buyer_id as buyer_id,b.sequence_no from electronic_approval_setup b where b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no < $user_sequence_no and b.is_deleted=0 and bypass=2 group by b.buyer_id,b.sequence_no order by b.sequence_no ASC"; die;
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
			/*echo "10**<pre>";
			print_r($match_seq); die;*/
			/*echo "10**"; echo implode(',', $match_seq); die;
			$seqsql = sql_select("SELECT sequence_no from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0 and bypass=2 and sequence_no<$user_sequence_no");
			foreach ($seqsql as $row)
			{
				$previous_user_seq=$row[csf('sequence_no')];
			}*/
			if(count($match_seq)>0 || $userBuyer==1)
			{
				$previous_user_seq = implode(',', $match_seq);
				$previous_user_app = sql_select("select id from approval_history where mst_id in($booking_ids) and entry_form=15 and sequence_no <$user_sequence_no and current_approval_status=1 group by id");
				
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
		$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id_approval and is_deleted=0");

		if($db_type==2) {$buyer_id_cond=" and a.buyer_id is null "; $buyer_id_cond2=" and b.buyer_id is not null ";}
		else {$buyer_id_cond=" and a.buyer_id='' "; $buyer_id_cond2=" and b.buyer_id!='' ";}
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

		//echo "10**select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=15 group by mst_id"; die;
		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=15 group by mst_id","mst_id","approved_no");

		$approved_status_arr = return_library_array("select id, approved from wo_pre_cost_mst where id in($booking_ids)","id","approved");
		$approved_no_array=array();
		$booking_ids_all=explode(",",$booking_ids);
		$booking_nos_all=explode(",",$booking_nos);
		$book_nos='';
		//print_r($credentialUserBuyersArr);die;
		for($i=0;$i<count($booking_nos_all);$i++)
		{
			$val=$booking_nos_all[$i];
			$booking_id=$booking_ids_all[$i];

			$approved_no=$max_approved_no_arr[$booking_id];
			$approved_status=$approved_status_arr[$booking_id];
			$buyer_id=$buyer_arr[$booking_id];
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
				if(in_array($buyer_id,$credentialUserBuyersArr))
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
				if(count($credentialUserBuyersArr)>0)
				{
					if(in_array($buyer_id,$credentialUserBuyersArr))
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
		//echo "10**".$rID4.'-'.$rID5.'-'.$rID6.'-'.$rID7.'-'.$rID8.'-'.$rID9.'-'.$rID10.'-'.$rID11.'-'.$rID12.'-'.$rID13.'-'.$rID14.'-'.$rID15.'-'.$rID16.'-'.$flag; die;
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
		/*if($approval_ids!="")
		{
			$rIDapp=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
			if($flag==1)
			{
				if($rIDapp) $flag=1; else $flag=0;
			}
		}*/
		//echo "10**Insert into approval_history ($field_array) values $data_array"; die;
		$rID2=sql_insert("approval_history",$field_array,$data_array,0);
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
?>
