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

if($db_type==0) $year_cond="SUBSTRING_INDEX(a.recipe_date, '-', 1) as year";
else if($db_type==2) $year_cond="to_char(a.recipe_date,'YYYY') as year";

if($db_type==0) $year_cond_groupby="SUBSTRING_INDEX(a.recipe_date, '-', 1)";
else if($db_type==2) $year_cond_groupby="to_char(a.recipe_date,'YYYY')";

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );
	exit();
}

if($action=="load_drop_down_buyer_new_user")
{
	$data=explode("_",$data);
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


$company_arr=return_library_array( 'select id, company_short_name from lib_company','id','company_short_name');
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$sequence_no='';
	$company_name=str_replace("'","",$cbo_company_name);
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
	$receipe_batch_no=str_replace("'","",$txt_receipe_batch_no);
	$search_type=str_replace("'","",$cbo_search_type);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$receipe_batch_cond='';
	if($search_type==1 && $receipe_batch_no!='')
	{
		$receipe_batch_cond=" and a.recipe_id='".trim($receipe_batch_no)."'";
	}
	if($search_type==2 && $receipe_batch_no!='')
	{
		$receipe_batch_cond=" and b.batch_no='".trim($receipe_batch_no)."'";
	}
	if($search_type==3 && $receipe_batch_no!='')
	{
		$receipe_batch_cond=" and a.id=".trim($receipe_batch_no)."";
	}
	if ($txt_date_from != "" && $txt_date_to != "") {
		if($db_type==0)
		 {
			$date_cond="and a.recipe_date between '".change_date_format(trim($txt_date_from), "yyyy-mm-dd","-",1)."' and '".change_date_format(trim($txt_date_to), "yyyy-mm-dd", "-",1)."'";
		 }
		 else if($db_type==2)
		 {
			$date_cond="and a.recipe_date between '".change_date_format(trim($txt_date_from), "mm-dd-yyyy","-",1)."' and '".change_date_format(trim($txt_date_to), "mm-dd-yyyy", "-",1)."'";
		 }
	} else {
		$date_cond = "";
	}

	$approval_type=str_replace("'","",$cbo_approval_type);
	if($previous_approved==1 && $approval_type==1)
	{
		$previous_approved_type=1;
	}
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
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Topping Adding Stripping Recipe Approval.</font>";
		die;
	}

	if($approval_type==2)
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

			$sql="SELECT a.id, $year_cond, a.recipe_id, a.labdip_no, a.company_id, a.batch_id, a.method, a.recipe_date, a.order_source,  a.buyer_id, a.new_batch_weight, a.dyeing_re_process, b.batch_no, '0' as approval_id,  (select max(c.approved_no) from approval_history c where a.id=c.mst_id and c.entry_form=44 $alter_user_cond) as revised_no from pro_recipe_entry_mst a join pro_batch_create_mst b on a.batch_id=b.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and a.entry_form=60 and a.ready_to_approve=1 and a.approved in (0,2) $buyer_id_cond $receipe_batch_cond $date_cond";
			//echo $sql; die;

		}
		else if($sequence_no == "")
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in ($buyer_ids)";

			if($buyer_ids=="") $buyer_id_cond3=""; else $buyer_id_cond3=" and c.buyer_name in($buyer_ids)";

				//$seqSql="select sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no=$user_sequence_no and is_deleted=0 order by sequence_no desc";
				$seqSql="SELECT sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
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
				$sequence_no_by_no=chop($sequence_no_by_no,',');
				$sequence_no_by_yes=chop($sequence_no_by_yes,',');

				if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
				if($sequence_no_by_no=="") $sequence_no_by_no=0;

				//$pre_cost_id='';
				$recipe_id='';
				//$pre_cost_id_sql="select distinct (mst_id) as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_name and b.sequence_no in ($sequence_no_by_no) and b.entry_form=15 and b.current_approval_status=1 $buyer_id_cond3 $seqcond union select distinct (mst_id) as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_name and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=15 and b.current_approval_status=1 $buyer_id_cond3";

				$recipe_id_sql="SELECT distinct(a.id) as recipe_id from pro_recipe_entry_mst a join approval_history b join a.id=b.mst_id where a.company_id=$cbo_company_name and b.sequence_no in ($sequence_no_by_no) and b.entry_form=44 and b.current_approval_status=1 and a.status_active=1 and a.is_deleted=0 $buyer_id_cond $seqCond UNION SELECT distinct(a.id) as recipe_id from pro_recipe_entry_mst a join approval_history b join a.id=b.mst_id where a.company_id=$cbo_company_name and b.sequence_no in ($sequence_no_by_no) and b.entry_form=44 and b.current_approval_status=1 and a.entry_form=60 $buyer_id_cond";


				$bResult=sql_select($recipe_id_sql);
				foreach($bResult as $bRow)
				{
					$recipe_id[$bRow[csf('recipe_id')]]=$bRow[csf('recipe_id')];
				}

				$recipe_id=implode(",", $recipe_id);

				//$pre_cost_id_app_sql=sql_select("select b.mst_id as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_name and b.sequence_no=$user_sequence_no and b.entry_form=15 and a.ready_to_approved=1 and b.current_approval_status=1");

				$recipe_id_app_sql=sql_select("SELECT b.mst_id as recipe_id from pro_recipe_entry_mst a join approval_history b join a.id=b.mst_id where a.status_active=1 and a.is_deleted=0 and a.company_id=$cbo_company_name and b.sequence_no=$user_sequence_no and b.entry_form=44 and a.ready_to_approve=1 and b.current_approval_status=1 and a.entry_form=60");
				foreach($recipe_id_app_sql as $inf)
				{
					//if($pre_cost_id_app_byuser!="") $pre_cost_id_app_byuser.=",".$inf[csf('pre_cost_id')];
					//else $pre_cost_id_app_byuser.=$inf[csf('pre_cost_id')];
					$recipe_id_app_byuser[$inf[csf('pre_cost_id')]] = $inf[csf('pre_cost_id')];
				}

				//$pre_cost_id_app_byuser=implode(",",array_unique(explode(",",$pre_cost_id_app_byuser)));
			//}// 12-10-2018

			//$pre_cost_id_app_byuser=chop($pre_cost_id_app_byuser,',');
			//$result=array_diff(explode(',',$pre_cost_id),explode(',',$pre_cost_id_app_byuser));
			$result=array_diff($recipe_id,$recipe_id_app_byuser);
			//$pre_cost_id=implode(",",$result);
			$recipe_id=implode(",",$result);
			//echo $pre_cost_id;die;
			$pre_cost_id_cond="";

			if($recipe_id_app_byuser!="")
			{
				$recipe_id_app_byuser_arr=explode(",",$recipe_id_app_byuser);
				if(count($recipe_id_app_byuser_arr)>995)
				{
					$recipe_id_app_byuser_chunk_arr=array_chunk(explode(",",$recipe_id_app_byuser_arr),995) ;
					foreach($recipe_id_app_byuser_chunk_arr as $chunk_arr)
					{
						$chunk_arr_value=implode(",",$chunk_arr);
						$recipe_id_cond.=" and a.id not in($chunk_arr_value)";
					}
				}
				else
				{
					$recipe_id_cond=" and a.id not in($recipe_id_app_byuser)";
				}
			}
			else $recipe_id_cond="";

			$sql="SELECT a.id, $year_cond, a.recipe_id, a.labdip_no, a.company_id, a.batch_id, a.method, a.recipe_date, a.order_source,  a.buyer_id, a.new_batch_weight, a.dyeing_re_process, b.batch_no, 0 as approval_id, a.approved, (select max(c.approved_no) from approval_history c where a.id=c.mst_id and c.entry_form=44 $alter_user_cond) as revised_no from pro_recipe_entry_mst a join pro_batch_create_mst b on a.batch_id=b.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=60 and  a.company_id=$cbo_company_name and a.ready_to_approve=1 and a.approved in (0,2) $buyer_id_cond $receipe_batch_cond $date_cond group by a.id, $year_cond_groupby, a.recipe_id, a.labdip_no, a.company_id, a.batch_id, a.method, a.recipe_date, a.order_source,  a.buyer_id, a.new_batch_weight, a.dyeing_re_process, b.batch_no, a.approved";

			if($recipe_id!="")
			{
				$recipe_id_cond2="and ";
				//$pre_cost_id_arr=explode(",",$pre_cost_id);
				$recipe_id_arr=explode(",",$recipe_id);
				if(count($recipe_id_arr)>995)
				{
					$recipe_id_cond2.=" ( ";
					//$pre_cost_id_cond2.=" ( ";
					$recipe_id_arr_chunk_arr=array_chunk(explode(",",$recipe_id),995) ;
					$slcunk=0;
					foreach($recipe_id_arr_chunk_arr as $chunk_arr)
					{
						if($slcunk>0) $recipe_id_cond2.=" or";
						$chunk_arr_value=implode(",",$chunk_arr);	
						$recipe_id_cond2.="  a.id  in($chunk_arr_value)";
						$slcunk++;	
					}
					$recipe_id_cond2.=" )";
				}
				else
				{
					$recipe_id_cond2.="  a.id  in($pre_cost_id)";	 
				}
				$sql.=" UNION All SELECT a.id, $year_cond, a.recipe_id, a.labdip_no, a.company_id, a.batch_id, a.method, a.recipe_date, a.order_source,  a.buyer_id, a.new_batch_weight, a.dyeing_re_process, b.batch_no, 0 as approval_id, a.approved,   (select max(c.approved_no) from approval_history c where a.id=c.mst_id and c.entry_form=44 $alter_user_cond) as revised_no from pro_recipe_entry_mst a join pro_batch_create_mst b on a.batch_id=b.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=60 and  a.company_id=$cbo_company_name and a.ready_to_approve=1 and a.approved in (1,3) $buyer_id_cond $receipe_batch_cond $date_cond $recipe_id_cond2 group by a.id, $year_cond_groupby, a.recipe_id, a.labdip_no, a.company_id, a.batch_id, a.method, a.recipe_date, a.order_source,  a.buyer_id, a.new_batch_weight, a.dyeing_re_process, b.batch_no, a.approved";
			}
		}
		else
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";

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

			$sql="SELECT a.id , $year_cond, a.recipe_id, a.labdip_no, a.company_id, a.batch_id, a.method, a.recipe_date, a.order_source,  a.buyer_id, a.new_batch_weight, a.dyeing_re_process, b.batch_no, a.approved, (select max(c.approved_no) from approval_history c where a.id=c.mst_id and c.entry_form=44 $alter_user_cond) as revised_no,c.sequence_no from pro_recipe_entry_mst a join pro_batch_create_mst b on a.batch_id=b.id join approval_history c on a.id=c.mst_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=60 and  a.company_id=$cbo_company_name and a.ready_to_approve=1 and a.approved in (1,3) $buyer_id_cond $receipe_batch_cond $date_cond $sequence_no_cond group by a.id, $year_cond_groupby, a.recipe_id, a.labdip_no, a.company_id, a.batch_id, a.method, a.recipe_date, a.order_source,  a.buyer_id, a.new_batch_weight, a.dyeing_re_process, b.batch_no, a.approved,c.sequence_no";
		}
		
	}
	else
	{ 
		$buyer_ids=$buyer_ids_array[$user_id]['u'];
		$sequence_no_cond=" and c.sequence_no='$user_sequence_no'";
		$sql="SELECT a.id, $year_cond, a.recipe_id, a.labdip_no, a.company_id, a.batch_id, a.method, a.recipe_date, a.order_source,  a.buyer_id, a.new_batch_weight, a.dyeing_re_process, b.batch_no, a.approved, (select max(c.approved_no) from approval_history c where a.id=c.mst_id and c.entry_form=44 $alter_user_cond) as revised_no,c.sequence_no from pro_recipe_entry_mst a join pro_batch_create_mst b on a.batch_id=b.id join approval_history c on a.id=c.mst_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=60 and  a.company_id=$cbo_company_name and a.ready_to_approve=1 and a.approved in (1,3) and c.entry_form=44 $buyer_id_cond $receipe_batch_cond $date_cond  $sequence_no_cond group by a.id, $year_cond_groupby, a.recipe_id, a.labdip_no, a.company_id, a.batch_id, a.method, a.recipe_date, a.order_source,  a.buyer_id,  a.new_batch_weight, a.dyeing_re_process, b.batch_no, a.approved,c.sequence_no";
	}

	//echo $sql; die;
	


	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:600px; margin-top:10px">
        <legend>Topping Adding Stripping Recipe Entry Approval</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" >
                <thead>
                	<th width="30"></th>
                    <th width="40">SL</th>
                    <th width="50">Year</th>
                    <th width="50">System ID</th>
                    <th width="80">Recipe No</th>
                    <th width="80">Batch No</th>
                    <th width="80">Labdip No</th>
                    <th width="80">Recipe Date</th>
                    <th width="80">Category</th>
                   	<th>Order Source</th>
                </thead>
            </table>
            <div style="width:720px; overflow-y:scroll; max-height:460px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
                         $i=1;
                            $nameArray=sql_select( $sql );
                            foreach ($nameArray as $row)
                            {
								$id=$row[csf('id')];
								$sequence_no=$row[csf('sequence_no')];
								
								$approval_id=sql_select("select max(c.id) approval_id from approval_history c where c.entry_form=44 and c.sequence_no=$sequence_no and mst_id=$id");	
							
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$value=$row[csf('id')];								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" align="center">
                                	<td width="30" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="booking_id_<? echo $i;?>" name="booking_id[]" type="hidden" value="<? echo $value; ?>" />
                                        <input id="approval_id_<? echo $i;?>" name="approval_id[]" type="hidden" value="<? echo $approval_id[0][APPROVAL_ID]; ?>" />
                                    </td>
									<td width="40" align="center"><? echo  $i; ?></td>
									<td width="50"><? echo $row[csf('year')]; ?></td>
									<td width="50"><? echo $row[csf('id')]; ?></td>
									<td width="80"><? echo $row[csf('recipe_id')]; ?></td>
									<td width="80"><? echo $row[csf('batch_no')]; ?></td>
									<td width="80"><? echo $row[csf('labdip_no')]; ?></td>
									<td width="80"><? echo change_date_format($row[csf('recipe_date')]); ?></td>
									<td width="80"><? echo $dyeing_re_process[$row[csf('dyeing_re_process')]]; ?></td>
									<td><? echo $order_source[$row[csf('order_source')]]; ?></td>
                                    
								</tr>
								<?
								$i++;
							}
							
						$denyBtn="";
						if($approval_type==2) $denyBtn=""; else $denyBtn=" display:none";
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="700" class="rpt_table">
				<tfoot>
                    <!-- <td width="50" align="center" ><input type="checkbox" id="all_check" onClick="check_all('all_check')" /></td> -->
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

	$msg=''; $flag=''; $response='';
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($txt_alter_user_id!="") 	$user_id_approval=$txt_alter_user_id;
	else						$user_id_approval=$user_id;

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id_approval and is_deleted=0");

	//echo "10**SELECT sequence_no from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id_approval and is_deleted=0"; die;
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");

	//$buyer_arr=return_library_array( "select b.id, a.buyer_name   from wo_pre_cost_mst b,  wo_po_details_master a   where  a.job_no=b.job_no and a.is_deleted=0 and  a.status_active=1 and b.status_active=1 and  b.is_deleted=0 and b.id in ($booking_ids)", "id", "buyer_name"  );

	$buyer_arr=return_library_array( "SELECT id, buyer_id from pro_recipe_entry_mst  where  is_deleted=0 and  status_active=1 and id in ($booking_ids)", "id", "buyer_id"  );
	//echo "20**";
	//print_r($buyer_arr);die;
	// echo $user_sequence_no;die();
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

		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=44 group by mst_id","mst_id","approved_no");
		//echo "10**select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=15 group by mst_id"; die;

		$approved_status_arr = return_library_array("select id, approved from pro_recipe_entry_mst where id in($booking_ids)","id","approved");
		$approved_no_array=array();
		$booking_ids_all=explode(",",$booking_ids);
		//$booking_nos_all=explode(",",$booking_nos);
		$book_nos=''; //echo "10**";
		/*echo '<pre>';
		print_r($credentialUserBuyersArr);die;*/
		for($i=0; $i<count($booking_ids_all); $i++)
		{
			//$recipe_id=$booking_nos_all[$i];
			$recipe_id=$booking_ids_all[$i];
			$booking_id=$booking_ids_all[$i];

			//$approved_no=$max_approved_no_arr[$booking_id];
			$approved_no=($max_approved_no_arr[$booking_id] == '') ? 1 : $max_approved_no_arr[$booking_id];
			$approved_status=$approved_status_arr[$booking_id];
			$buyer_id=$buyer_arr[$booking_id];

			if($approved_status==2)
			{
				$approved_no=$approved_no+1;
				$approved_no_array[$recipe_id]=$approved_no;
				if($book_nos=="") $book_nos=$recipe_id; else $book_nos.=",".$recipe_id;
			}
			//echo "10**".$is_not_last_user; die;
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
				//echo "10**".$buyer_id; die;
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
			//echo "10**". $partial_approval;die;
			$booking_id_arr[]=$booking_id;
			$data_array_booking_update[$booking_id]=explode("*",($partial_approval));

			if($partial_approval==1)
			{
				$full_approve_booking_id_arr[]=$booking_id;
				$data_array_full_approve_booking_update[$booking_id]=explode("*",($user_id_approval."*'".$pc_date_time."'"));
			}
			
			if(($user_sequence_no*1)==0) { echo "seq**".$user_sequence_no; disconnect($con);die; }
			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",44,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')";
			$id=$id+1;
		}

		$flag=1;

		//$rID2=1;
		if(count($full_approve_booking_id_arr)>0)
		{

			$field_array_full_approved_booking_update = "approved_by*approved_date";
			$rID2=execute_query(bulk_update_sql_statement( "pro_recipe_entry_mst", "id", $field_array_full_approved_booking_update, $data_array_full_approve_booking_update, $full_approve_booking_id_arr));
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}
		}

		$field_array_booking_update = "approved";
		$rID=execute_query(bulk_update_sql_statement( "pro_recipe_entry_mst", "id", $field_array_booking_update, $data_array_booking_update, $booking_id_arr));

		if($flag==1)
		{
			if($rID) $flag=1; else $flag=0;
		}
		//echo "10**".bulk_update_sql_statement( "wo_pre_cost_mst", "id", $field_array_booking_update, $data_array_booking_update, $booking_id_arr); die;

		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=44 and mst_id in ($booking_ids)";
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
		//echo "10**".$flag.'-'.$rID12.'-'.$rID13.'-'.$rID14.'-'.$rID2.'-'.$rID3.'-'.$rID4.'-'.$rID5.'-'.$rID6.'-'.$rID7.'-'.$rID8.'-'.$rID9.'-'.$rID10.'-'.$rID11.'-'.$rID; die;
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


		$rID=sql_multirow_update("pro_recipe_entry_mst","approved*ready_to_approve",'2*0',"id",$booking_ids,0);
		if($rID) $flag=1; else $flag=0;
		//echo "22**".$rID;die;
		//$rID2=sql_multirow_update2("approval_history","current_approval_status",0,"entry_form*mst_id",15*$booking_ids,0);
		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=44 and mst_id in ($booking_ids)";
		$rID2=execute_query($query,1);
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

		$response=$booking_ids;

		if($flag==1) $msg='20'; else $msg='22';
	}
	else if($approval_type==5)
	{
		$sqlBookinghistory="select id, mst_id from approval_history where current_approval_status=1 and entry_form=44 and mst_id in ($booking_ids) ";
		//echo "10**".$sqlBookinghistory;
		$nameArray=sql_select($sqlBookinghistory); $bookidstr=""; $approval_id="";
		foreach ($nameArray as $row)
		{
			if($bookidstr=="") $bookidstr=$row[csf('mst_id')]; else $bookidstr.=','.$row[csf('mst_id')];
			if($approval_id=="") $approval_id=$row[csf('id')]; else $approval_id.=','.$row[csf('id')];
		}
		
		$appBookNoId=implode(",",array_filter(array_unique(explode(",",$bookidstr))));
		$approval_ids=implode(",",array_filter(array_unique(explode(",",$approval_id))));
		
		$rID=sql_multirow_update("pro_recipe_entry_mst","approved*ready_to_approve",'2*0',"id",$booking_ids,0);
		if($rID) $flag=1; else $flag=0;

		//$rID2=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
		if($approval_ids!="")
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=44 and current_approval_status=1 and id in ($approval_ids)";
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
	disconnect($con);
	die;
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




?>
