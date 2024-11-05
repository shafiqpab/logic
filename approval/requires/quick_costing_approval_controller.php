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

$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$sql_user=sql_select("select id, user_name, user_full_name from user_passwd");
$user_arr=array();
foreach($sql_user as $row)
{
	$user_arr[$row[csf('id')]]['user_name']=$row[csf('user_name')];
	$user_arr[$row[csf('id')]]['user_full_name']=$row[csf('user_full_name')];
}
//$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );

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
			else $buyer_id_cond="";
		}
		else $buyer_id_cond=" and a.buyer_id=$cbo_buyer_name";
	}

	if ($job_year=="" || $job_year==0) $job_year_cond="";
	else
	{
		if($db_type==2) $job_year_cond=" and to_char(a.insert_date,'YYYY')='".trim($job_year)."' ";
		else $job_year_cond=" and YEAR(a.insert_date)='".trim($job_year)."' ";
	}

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

	$buyer_ids_array = array();
	//$brand_ids_array = array();
	$buyerData = sql_select("select user_id, sequence_no, buyer_id,BRAND_ID from electronic_approval_setup where page_id=$menu_id and is_deleted=0 ");//and bypass=2//echo "select user_id, sequence_no, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0 and bypass=2";
	foreach($buyerData as $row)
	{
		$buyer_ids_array[$row[csf('user_id')]]['u']=$row[csf('buyer_id')];
		$buyer_ids_array[$row[csf('sequence_no')]]['s']=$row[csf('buyer_id')];
		//$brand_ids_array[$row[csf('user_id')]]['u']=$row[csf('BRAND_ID')];
		//$brand_ids_array[$row[csf('sequence_no')]]['s']=$row[csf('BRAND_ID')];
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

		$sql="select a.id,a.qc_no, b.tot_fob_cost,a.cost_sheet_id, $year_cond, a.cost_sheet_no, a.style_ref, a.buyer_id, a.delivery_date, a.exchange_rate, a.offer_qty, a.costing_date,a.revise_no, a.option_id, c.id as approval_id, c.sequence_no, c.approved_by,
			a.approved,a.inserted_by,a.revise_no, a.option_id,d.job_id, d.id as confirm_id,a.exchange_rate from qc_mst a, qc_tot_cost_summary b,approval_history c,qc_confirm_mst d where c.mst_id=a.id and c.entry_form=36 and  a.qc_no=b.mst_id and b.mst_id=d.cost_sheet_id and a.status_active=1 and a.is_deleted=0 and a.approved in (1,3) and d.ready_to_approve=1 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.current_approval_status=1 $sequence_no_cond $buyer_id_cond $buyer_id_cond2 $date_cond  $job_year_cond $style_cond $txt_costshit_no ";
		//$buyer_id_cond //and d.job_id>0 
	//echo $sql;
	}
	else if($approval_type==2)
	{
		if($db_type==0)
		{
			$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup"," page_id=$menu_id and sequence_no<$user_sequence_no and bypass = 2 and buyer_id='' and is_deleted=0","seq");
		}
		else
		{
			$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup"," page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
		}

		if($user_sequence_no==$min_sequence_no)
		{
			$buyer_ids = $buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
			//$brand_ids = $brand_ids_array[$user_id]['u'];
			//if($brand_ids=="") $brand_id_cond2=""; else $brand_id_cond2=" and a.BRAND_ID in($brand_ids)";
			
			
			
			
			//$buyer_id_cond=""; $buyer_id_cond2=""; 
			$sql="select a.id,a.qc_no, b.tot_fob_cost,a.cost_sheet_id,$year_cond, a.cost_sheet_no, a.style_ref, a.buyer_id, a.delivery_date, a.exchange_rate, a.offer_qty, a.costing_date,a.revise_no, a.option_id,c.job_id, c.id as confirm_id,a.exchange_rate from qc_mst a, qc_tot_cost_summary b, qc_confirm_mst c where a.qc_no=b.mst_id and b.mst_id=c.cost_sheet_id and a.approved in (0,2) and c.ready_to_approve=1 and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyer_id_cond $buyer_id_cond2 $brand_id_cond2 $date_cond  $job_year_cond $style_cond $txt_costshit_no ";
	//and c.job_id>0
	
		}
		else if($sequence_no == "")
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
			if($buyer_ids=="") $buyer_id_cond3=""; else $buyer_id_cond3=" and c.buyer_id in($buyer_ids)";
			
			//$brand_ids=$brand_ids_array[$user_id]['u'];
			//if($brand_ids=="") $brand_id_cond2=""; else $brand_id_cond2=" and a.brand_id in($brand_ids)";
			//if($brand_ids=="") $brand_id_cond3=""; else $brand_id_cond3=" and a.brand_id in($brand_ids)";


			//$buyer_id_cond=""; $buyer_id_cond2=""; $buyer_id_cond3=""; 

			$seqSql="select sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=0 and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
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
				$buyerIds_cond=""; $seqCond="";
			}
			else
			{
				$buyerIds_cond=" and a.buyer_id not in($buyerIds)"; $seqCond=" and (".chop($query_string,'or ').")";
			}
			$sequence_no_by_no=chop($sequence_no_by_no,',');
			$sequence_no_by_yes=chop($sequence_no_by_yes,',');

			if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
			if($sequence_no_by_no=="") $sequence_no_by_no=0;

			$qc_id='';
			$qc_id_sql="select distinct (b.mst_id) as qc_id from qc_mst a, approval_history b where a.id=b.mst_id and b.sequence_no in ($sequence_no_by_no) and b.entry_form=36 and b.current_approval_status=1 $buyer_id_cond3 $brand_id_cond2 $seqCond
			union
			select distinct (b.mst_id) as qc_id from qc_mst a, approval_history b where a.id=b.mst_id  and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=36 and b.current_approval_status=1 $buyer_id_cond3 $brand_id_cond2";
			$bResult=sql_select($qc_id_sql);
			foreach($bResult as $bRow)
			{
				$qc_id.=$bRow[csf('qc_id')].",";
			}

			$qc_id=chop($qc_id,',');

			$qc_id_app_sql=sql_select("select b.mst_id as qc_id from qc_mst a, approval_history b
			where a.id=b.mst_id  and b.sequence_no=$user_sequence_no and b.entry_form=36  and b.current_approval_status=1");

			foreach($qc_id_app_sql as $inf)
			{
				if($qc_id__app_byuser!="") $qc_id_app_byuser.=",".$inf[csf('pre_cost_id')];
				else $qc_id_app_byuser.=$inf[csf('pre_cost_id')];
			}

			$qc_id_app_byuser=implode(",",array_unique(explode(",",$qc_id_app_byuser)));
			//}// 12-10-2018

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

			$sql="select a.id, a.qc_no, b.tot_fob_cost,a.cost_sheet_id,$year_cond, a.cost_sheet_no, a.style_ref, a.buyer_id, a.delivery_date, a.exchange_rate, a.offer_qty, a.costing_date,a.revise_no, a.option_id,0 as approval_id,
			a.approved,a.inserted_by,a.revise_no, a.option_id,c.job_id, c.id as confirm_id,a.exchange_rate from qc_mst a, qc_tot_cost_summary b,qc_confirm_mst c where a.qc_no=b.mst_id and b.mst_id=c.cost_sheet_id   and a.status_active=1 and a.is_deleted=0  and a.approved in (0,2) and c.ready_to_approve=1 and b.status_active=1 and b.is_deleted=0 and   c.status_active=1 and c.is_deleted=0 $qc_id_cond $buyer_id_cond $buyer_id_cond2 $brand_id_cond2 $date_cond  $job_year_cond $style_cond $txt_costshit_no ";
			//echo $sql;die;//and c.job_id>0
			if($qc_id!="")
			{
				$sql.=" union all
				select a.id,a.qc_no, b.tot_fob_cost,a.cost_sheet_id,$year_cond, a.cost_sheet_no, a.style_ref, a.buyer_id, a.delivery_date, a.exchange_rate, a.offer_qty, a.costing_date,a.revise_no, a.option_id,0 as approval_id,
			a.approved,a.inserted_by,a.revise_no, a.option_id,c.job_id, c.id as confirm_id,a.exchange_rate from qc_mst a, qc_tot_cost_summary b, qc_confirm_mst c
				where  a.qc_no=b.mst_id and b.mst_id=c.cost_sheet_id  and a.status_active=1 and a.is_deleted=0  and a.approved in (1,3) and c.ready_to_approve=1 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
				  and (a.id in($qc_id)) $buyer_id_cond $buyer_id_cond2 $brand_id_cond2 $date_cond  $job_year_cond $style_cond $txt_costshit_no";
				  //and c.job_id>0 
			}
		}
		else
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
			//$brand_ids=$brand_ids_array[$user_id]['u'];
			//if($brand_ids=="") $brand_id_cond2=""; else $brand_id_cond2=" and a.brand_id in($brand_ids)";

			$user_sequence_no=$user_sequence_no-1;
			if($sequence_no==$user_sequence_no) $sequence_no_by_pass='';
			else
			{
				if($db_type==0)
				{
					$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup"," page_id=$menu_id and
					 sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");// and bypass=1
				}
				else
				{
					$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no)
					 as sequence_no","electronic_approval_setup"," page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");//bypass=1 and
				}
			}

			if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$sequence_no'";
			else $sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";
			$sql="select a.id, a.qc_no, b.tot_fob_cost,a.cost_sheet_id,$year_cond, a.cost_sheet_no, a.style_ref, a.buyer_id, a.delivery_date, a.exchange_rate, a.offer_qty, a.costing_date,a.revise_no, a.option_id, c.id as approval_id, c.sequence_no, c.approved_by,a.approved,a.inserted_by,a.revise_no, a.option_id,d.job_id, d.id as confirm_id,a.exchange_rate from qc_mst a, qc_tot_cost_summary b,approval_history c ,qc_confirm_mst d where c.mst_id=a.id and c.entry_form=36 and  a.qc_no=b.mst_id and b.mst_id=d.cost_sheet_id  and a.status_active=1 and a.is_deleted=0  and a.approved in (1,3) and d.ready_to_approve=1 and  b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.current_approval_status=1 $sequence_no_cond $buyer_id_cond $buyer_id_cond2 $brand_id_cond2 $date_cond  $job_year_cond $style_cond $txt_costshit_no ";

			//and d.job_id>0
		}
	}
	else
	{ 
		$buyer_ids=$buyer_ids_array[$user_id]['u'];
		//$brand_ids=$brand_ids_array[$user_id]['u'];
		if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
		//if($brand_ids=="") $brand_id_cond2=""; else $brand_id_cond2=" and a.brand_id in($brand_ids)";
		
		
		//$buyer_id_cond=""; $buyer_id_cond2=""; $buyer_id_cond3=""; 
		$sequence_no_cond=" and c.sequence_no='$user_sequence_no'";
		$sql="select a.id, a.qc_no, b.tot_fob_cost, a.cost_sheet_id, $year_cond, a.cost_sheet_no, a.style_ref, a.buyer_id, a.delivery_date, a.exchange_rate, a.offer_qty, a.costing_date, a.revise_no, a.option_id, c.id as approval_id, c.sequence_no, c.approved_by,
			a.approved,a.inserted_by,a.revise_no, a.option_id,d.job_id, d.id as confirm_id,a.exchange_rate from qc_mst a, qc_tot_cost_summary b, approval_history c,qc_confirm_mst d where c.mst_id=a.id and c.entry_form=36 and  a.qc_no=b.mst_id and b.mst_id=d.cost_sheet_id  and a.status_active=1 and a.is_deleted=0  and a.approved in (1,3) and d.ready_to_approve=1 and  b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.current_approval_status=1 $sequence_no_cond $buyer_id_cond $buyer_id_cond2 $brand_id_cond2 $date_cond  $job_year_cond $style_cond $txt_costshit_no ";
			//and d.job_id>0
			 //echo $sql;
	}
	
	//$isApproved=return_library_array( "select qc_no, approved from lib_supplier", "qc_no", "approved");
	
	$qcmargin="Select a.qc_no, a.buyer_id, a.approved, a.delivery_date, b.smv, b.efficency, b.available_min from qc_mst a, qc_margin_mst b where a.qc_no=b.qc_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	
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
	unset($salesforcustData);
	//print_r($salesforArr[65]['7,2020']); die;
	
	
	?>
    <script>

		function openmypage_app_cause(wo_id,app_type,i)
		{
			var txt_appv_cause = $("#txt_appv_cause_"+i).val();
			var approval_id = $("#approval_id_"+i).val();
			var data=wo_id+"_"+app_type+"_"+txt_appv_cause+"_"+approval_id;
			var title = 'Approval Cause Info';
			var page_link = 'requires/quick_costing_approval_controller.php?data='+data+'&action=appcause_popup';
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
			var page_link = 'requires/quick_costing_approval_controller.php?data='+data+'&action=appinstra_popup';
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
			var page_link = 'requires/quick_costing_approval_controller.php?data='+data+'&action=unappcause_popup';
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
	if($row_id=84)  $report_action="quick_costing_print2";
	if($approval_type==0)
	{
		$fset=1380;
		$table1=1570;
		$table2=1572;
	}
	else if($approval_type==1)
	{
		$fset=1380;
		$table1=1570;
		$table2=1572;
	}
	
	$sql_request="select booking_id, approval_cause from fabric_booking_approval_cause where entry_form=28 and approval_type=2 and status_active=1 and is_deleted=0 order by id Desc";
	$unappRequest_arr=array();

	$nameArray_request=sql_select($sql_request);
	foreach($nameArray_request as $approw)
	{
		$unappRequest_arr[$approw[csf("booking_id")]]=$approw[csf("approval_cause")];
	}
	//echo $report_action;die;
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:1780px; margin-top:10px">
        <legend>Quick Costing Approval</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1780" class="rpt_table" >
                <thead>
                	<th width="30"></th>
                    <th width="30">SL</th>
                    <th width="70">Offer Qty.</th>
                    <th width="70">FOB Cost</th>
                    <th width="100">Cost Sheet No</th>
                    <th width="120">Style Ref.</th>
                   	<th width="70">Revise No</th>
                   	<th width="70">Option ID</th>
                   	<th width="70">Exchange Rate</th>
                    <th width="50">Year</th>
                    <th width="120">Buyer</th>
                    <th width="65">Costing Date</th>
                    <th width="65">Delivery Date</th>
                    
                    <th width="70">Style Avl Min</th>
                    <th width="70">Buyer Forecast Min</th>
                    <th width="70">Booked Min</th>
                    <th width="70">Balance Min</th>
                    
                    <th width="100">Insert By</th>
					<th width="100">User Full Name</th>
                    <th width="70">Approved Date</th>
                    <?
					if($approval_type==0) echo "<th width='80'>Appv Instra</th>";
					if($approval_type==1) echo "<th width='80'>Un-Appv Request</th>";
					?>
                    <th><? if($approval_type==0) echo "Not Appv. Cause" ; else echo "Not Un-Appv. Cause"; ?></th>
                </thead>
            </table>
            <div style="width:1780px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1762" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
                        //echo $sql; die;
                         $i=1;
                            $nameArray=sql_select( $sql );

							// print ($sql);die;
							$ref_no = ""; $file_numbers = "";
                            foreach ($nameArray as $row)
                            {
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								if($approval_type==2) $value=$row[csf('id')]; else $value=$row[csf('id')]."**".$row[csf('approval_id')]."**".$row[csf('confirm_id')];;
								
								$fob_cost=$row[csf('tot_fob_cost')];
								if($fob_cost=='' || $fob_cost==0) $fob_cost=0;else $fob_cost=$fob_cost;
								if($fob_cost<0 || $fob_cost==0) $td_color="#F00"; else $td_color="";
								
								$exdeldate=explode("-",change_date_format($row[csf('delivery_date')]));
								//print_r($exdeldate);
								
								$m=ltrim($exdeldate[1], '0'); $y=$exdeldate[2];
								$mmyy=$m.','.$y;
								
								$styleAvlMin=$buyerForcutMin=$balanceMin=0;
								$bookedMin=0;
								$styleAvlMin=$marginArr[$row[csf('qc_no')]]['avlmin'];//$marginArr[$row[csf('qc_no')]]['smv']*$row[csf('offer_qty')]*($marginArr[$row[csf('qc_no')]]['eff']/100);
								$buyerForcutMin=$salesforArr[$row[csf('buyer_id')]][$mmyy];
								$bookedMin=$bookMinArr[$row[csf('buyer_id')]][$mmyy];
								/*if($approval_type==1)
								{
									$bookedMin+=$styleAvlMin;
								}*/
								
								$balanceMin=$buyerForcutMin-$bookedMin;
								
								//$bookMinArr[$row[csf('buyer_id')]][$mmyy]-=$buyerForcutMin;
								$qc_no=$row[csf('qc_no')];
								$costing_date=$row[csf('costing_date')];
								$buyer_id=$row[csf('buyer_id')];
								$offer_qty=$row[csf('offer_qty')];
								$ex_rate=$row[csf('exchange_rate')];
								?>
								<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>" align="center">
                                	<td width="27" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="booking_id_<? echo $i;?>" name="booking_id[]" type="hidden" value="<? echo $value; ?>" />
                                        <input id="confirm_id_<? echo $i;?>" name="confirm_id[]" type="hidden" value="<? echo $row[csf('confirm_id')]; ?>" />
                                        <input id="booking_no_<? echo $i;?>" styleavlmin="<?=$styleAvlMin; ?>" name="booking_no[]" type="hidden" value="<? echo $row[csf('qc_no')]; ?>" />
                                        <input id="approval_id_<? echo $i;?>" name="approval_id[]" type="hidden" value="<? echo $row[csf('approval_id')]; ?>" />
                                        <input id="<?=strtoupper($row[csf('cost_sheet_no')]); ?>" name="no_joooob[]" type="hidden" value="<? echo $i;?>" />
                                        <input id="cm_cost_id_<?=$i; ?>" name="cm_cost_id[]" style="width:20px;" type="hidden" value="<?=$fob_cost; ?>" />
                                    </td>
									<td width="30" align="center"><? echo  $i; ?></td>
									<td width="70" style="word-break:break-all"><? echo $row[csf('offer_qty')]; ?></td>
                                    <td width="70" align="right"><p style="color:<? echo $td_color; ?>"><? echo number_format($fob_cost,2); ?>&nbsp;</p></td>
                                    <td width="100" style=" word-break:break-all;">
                                    	<a href="##" onClick="fnc_costing_details('<? echo $qc_no;?>','<? echo $buyer_arr[$buyer_id]."_".$buyer_id;?>','<? echo $costing_date;?>','<? echo $ex_rate;?>','<? echo $offer_qty; ?>','costing_popup')"><p><?=$row[csf('cost_sheet_no')]; ?></p></a>
                                    	
                                    </td>
                                    <td width="120" align="center" style="word-break:break-all"><a href='##'  onclick="fnc_print_report(<?php echo $row[csf('qc_no')];?>,<?php echo $row[csf('cost_sheet_no')];?>,'<?php echo $report_action;?>' )"><? echo $row[csf('style_ref')]; ?></a></td>
                                    <td width="70" style="word-break:break-all"><? echo $row[csf('revise_no')]; ?></td>
                                    <td width="70" style="word-break:break-all"><? echo $row[csf('option_id')]; ?></td>
                                    <td width="70" style="word-break:break-all"><? echo $row[csf('exchange_rate')]; ?></td>
                                    <td width="50" style="word-break:break-all"><? echo $row[csf('year')]; ?>&nbsp;</td>
                                    <td width="120" style="word-break:break-all"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</td>
									
                                    <td width="65" align="center"><? if($row[csf('costing_date')]!="0000-00-00") echo change_date_format($row[csf('costing_date')]); ?>&nbsp;</td>
									<td align="center" width="65"><? if($row[csf('delivery_date')]!="0000-00-00") echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</td>
                                    
                                    <td width="70" style="word-break:break-all" align="right" id="tdstyleavlmin_<?=$i; ?>"><?=number_format($styleAvlMin,2); ?></td>
                                    <td width="70" style="word-break:break-all" align="right"><?=$buyerForcutMin; ?></td>
                                    <td width="70" style="word-break:break-all" align="right"><?=$bookedMin; ?></td>
                                    <td width="70" style="word-break:break-all" align="right" id="tdbalanmin_<?=$i; ?>"><?=$balanceMin; ?></td>

                                    <td width="100" style="word-break:break-all"><? echo ucfirst($user_arr[$row[csf('inserted_by')]]['user_name']); ?>&nbsp;</td>
									<td width="100" style="word-break:break-all"><? echo ucfirst($user_arr[$row[csf('inserted_by')]]['user_full_name']); ?>&nbsp;</td>
                                    <td width="70" align="center"><? if($row[csf('approved_date')]!="0000-00-00") echo change_date_format($row[csf('approved_date')]); ?>&nbsp;</td>
                                    <?
										if($approval_type==0)echo "<td align='center' width='80'>
                                        		<Input name='txt_appv_instra[]' class='text_boxes' readonly placeholder='Please Browse' ID='txt_appv_instra_".$i."' style='width:70px' maxlength='50' onClick='openmypage_app_instrac(".$row[csf('qc_no')].",".$approval_type.",".$i.")' ></td>";
											if($approval_type==1)echo "<td align='center' width='80'>
                                        		<Input name='txt_unappv_req[]' class='text_boxes' readonly placeholder='Please Browse' ID='txt_unappv_req_".$i."' style='width:70px' maxlength='50' onClick='openmypage_unapp_request(".$row[csf('qc_no')].",".$approval_type.",".$i.")' value='".$unappRequest_arr[$row[csf('qc_no')]]."'></td>";
                                        ?>
                                        <td align="center">
                                        	<Input name="txt_appv_cause[]" class="text_boxes" readonly placeholder="Please Browse" ID="txt_appv_cause_<? echo $i;?>" style="width:90px" maxlength="50" title="Maximum 50 Character" onClick="openmypage_app_cause(<? echo $row[csf('qc_no')]; ?>,<? echo $approval_type; ?>,<? echo $i;?>)">&nbsp;</td>
								</tr>
								<?
								$i++;
						}

                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="1562" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" ><input type="checkbox" id="all_check" onClick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/></td>
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

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup"," page_id=$menu_id and user_id=$user_id_approval and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup"," page_id=$menu_id and is_deleted=0");

	$buyer_arr=return_library_array( "select a.id, a.buyer_id   from   qc_mst a   where   a.is_deleted=0 and  a.status_active=1  and a.id in ($booking_ids)", "id", "buyer_id"  );
	//echo "20**";
	//print_r($buyer_arr);die;
	// echo $user_sequence_no;die();
	if($approval_type==2)
	{
		if($db_type==2) {$buyer_id_cond=" and a.buyer_id is null "; $buyer_id_cond2=" and b.buyer_id is not null ";}
		else 			{$buyer_id_cond=" and a.buyer_id='' "; $buyer_id_cond2=" and b.buyer_id!='' ";}



		//echo "22**";
		$is_not_last_user=return_field_value("a.sequence_no","electronic_approval_setup a"," a.page_id=$menu_id and a.sequence_no>$user_sequence_no and a.bypass=2 and a.is_deleted=0 $buyer_id_cond","sequence_no");

		//echo $is_not_last_user;die;

		$partial_approval = "";
		if($is_not_last_user == "")
		{
			//$credentialUserBuyersArr = [];
			$sql = sql_select("select (b.buyer_id) as buyer_id from electronic_approval_setup b where b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2  group by b.buyer_id");
			foreach ($sql as $key => $buyerID) {
				$credentialUserBuyersArr[] = $buyerID[csf('buyer_id')];
			}

			$credentialUserBuyersArr = implode(',',$credentialUserBuyersArr);
			$credentialUserBuyersArr = explode(',',$credentialUserBuyersArr);
			$credentialUserBuyersArr = array_unique($credentialUserBuyersArr);
		}
		else
		{

			$check_user_buyer = sql_select("select b.user_id as user_id from user_passwd a, electronic_approval_setup b where a.id = b.user_id and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 $buyer_id_cond  group by b.user_id");
			//echo "21**".count($check_user_buyer);die;
			if(count($check_user_buyer)==0)
			{

				$sql = sql_select("select b.buyer_id as buyer_id from user_passwd b, electronic_approval_setup a where b.id = a.user_id and  a.page_id=$menu_id and a.sequence_no>$user_sequence_no and a.is_deleted=0 and a.bypass=2 $buyer_id_cond $buyer_id_cond2 group by b.buyer_id");
				foreach ($sql as $key => $buyerID) {
					$credentialUserBuyersArr[] = $buyerID[csf('buyer_id')];
				}

				$sql = sql_select("select (b.buyer_id) as buyer_id from electronic_approval_setup b where  b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 $buyer_id_cond2  group by b.buyer_id");
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

		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=36 group by mst_id","mst_id","approved_no");

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


			if($approved_status==0 || $approved_status==2)
			{
				$approved_no=$approved_no+1;
				$approved_no_array[$val]=$approved_no;
				if($book_nos=="") $book_nos=$val; else $book_nos.=",".$val;
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
			$confirm_id_arr[]=$confirm_id;
			$data_array_confirm_update[$confirm_id]=explode("*",($partial_approval));

			if($partial_approval==1)
			{
				$full_approve_booking_id_arr[]=$booking_id;
				$full_approve_confirm_id_arr[]=$confirm_id;
				$data_array_full_approve_booking_update[$booking_id]=explode("*",($user_id_approval."*'".$pc_date_time."'"));
			}


			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",36,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')";
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

			$confirm_mst_sql="insert into qc_confirm_mst_history(id, approved_no, confirm_mst_id,cost_sheet_id, lib_item_id, confirm_style,confirm_order_qty, confirm_fob, deal_merchant,  ship_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, job_id, approved,  approved_by, approved_date) 

				select 
				  '',$approved_string_confirm,id, cost_sheet_id, lib_item_id,confirm_style, confirm_order_qty, confirm_fob,  deal_merchant, ship_date, inserted_by, insert_date, updated_by, update_date,  status_active, is_deleted, job_id,  approved, approved_by, approved_date from qc_confirm_mst where cost_sheet_id in ($book_nos)";

			$confirm_dtls_sql="insert into qc_confirm_dtls_history( id, approved_no, confirm_dtls_id, mst_id, cost_sheet_id, item_id, 
			   fab_cons_kg, fab_cons_yds, fab_amount, sp_oparation_amount, acc_amount, fright_amount,  lab_amount, misce_amount, other_amount,  comm_amount, fob_amount, cm_amount,  rmg_ratio, inserted_by, insert_date,  updated_by, update_date, status_active,  is_deleted, fab_cons_mtr, cppm_amount,  smv_amount) 

			select 
			  '',$approved_string_confirm,id, mst_id, cost_sheet_id, item_id, fab_cons_kg, fab_cons_yds, fab_amount, sp_oparation_amount, acc_amount,  fright_amount, lab_amount, misce_amount,  other_amount, comm_amount, fob_amount,   cm_amount, rmg_ratio, inserted_by,  insert_date, updated_by, update_date,  status_active, is_deleted, fab_cons_mtr,   cppm_amount, smv_amount
			from qc_confirm_dtls where cost_sheet_id in ($book_nos)";



			$sql_insert_cons_rate="insert into  qc_cons_rate_dtls_histroy( id,approved_no, cons_rate_dtls_id, mst_id, item_id, type, 	particular_type_id, formula, consumption, unit,  is_calculation, rate, rate_data, value, inserted_by, insert_date, 
  				updated_by, update_date, status_active,  is_deleted, ex_percent)
			   select 
			   '',$approved_string_dtls, id, mst_id, item_id, type, particular_type_id, formula,  consumption, unit, is_calculation, rate, rate_data, value, 
			   inserted_by, insert_date, updated_by,update_date, status_active, is_deleted, ex_percent
			from qc_cons_rate_dtls where mst_id in ($book_nos) ";
			//echo $sql_insert;die;


			$sql_fabric_dtls="insert into  qc_fabric_dtls_history(id,approved_no, fabric_dtls_id, mst_id,  item_id, body_part, 
			des,value, alw, inserted_by,insert_date, updated_by, update_date,  status_active, is_deleted, uniq_id)
			select 
    			'',$approved_string_dtls,id, mst_id, item_id, body_part, des, value, alw, inserted_by, insert_date, updated_by, update_date,
    			 status_active,  is_deleted, uniq_id from qc_fabric_dtls where mst_id in ($book_nos)";
			//echo $sql_precost_dtls;die;


			//--------------------------------------wo_pre_cost_fabric_cost_dtls_h---------------------------------------------------------------------------

			$sql_item_cost_dtls="insert into qc_item_cost_summary_his(id, approved_no ,item_sum_id, mst_id, item_id, fabric_cost, sp_operation_cost,accessories_cost, smv, efficiency, cm_cost, frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost,fob_pcs, inserted_by, insert_date, updated_by, update_date, status_active,is_deleted, rmg_ratio, cpm)
				select
				'', $approved_string_dtls,  id, mst_id, item_id, fabric_cost, sp_operation_cost, accessories_cost,  smv, efficiency, cm_cost,frieght_cost, lab_test_cost, miscellaneous_cost, other_cost, commission_cost, fob_pcs, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, rmg_ratio, cpm from qc_item_cost_summary where  mst_id in ($book_nos)";
			//echo $sql_precost_fabric_cost_dtls;die;

			//--------------------------------------wo_pre_cost_fab_yarn_cst_dtl_h---------------------------------------------------------------------------
			$sql_meeting_mst="insert into qc_meeting_mst_history(id, approved_no, metting_mst_id, mst_id, meeting_no, buyer_agent_id, location_id, meeting_date, meeting_time, remarks,  inserted_by, insert_date, updated_by,  update_date, status_active, is_deleted)
				select
				'', $approved_string_dtls, id, mst_id, meeting_no, buyer_agent_id, location_id, meeting_date, meeting_time, remarks, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from qc_meeting_mst  where  mst_id in ($book_nos)";
				//echo $sql_precost_fab_yarn_cst;die;

			//----------------------------------wo_pre_cost_comarc_cost_dtls_h----------------------------------------
			$sql_qc_mst="insert into qc_mst_history( id , approved_no, qc_mst_id, cost_sheet_id, cost_sheet_no, temp_id, style_des, buyer_id, cons_basis, season_id, style_ref, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, lib_item_id, pre_cost_sheet_id, revise_no,  option_id, buyer_remarks, option_remarks,  meeting_no, qc_no, uom, 
 				approved, approved_by, approved_date, from_client)
				select
				'', $approved_string_mst, id, cost_sheet_id, cost_sheet_no, temp_id, style_des, buyer_id, cons_basis, season_id, style_ref, department_id, delivery_date, exchange_rate,  offer_qty, quoted_price, tgt_price,  stage_id, costing_date, inserted_by,  insert_date, updated_by, update_date,  status_active, is_deleted, lib_item_id,  pre_cost_sheet_id, revise_no, option_id,  buyer_remarks, option_remarks, meeting_no,  qc_no, uom, approved, approved_by, approved_date, from_client from qc_mst where  qc_no in ($book_nos)";
				//echo $sql_precost_fcomarc_cost_dtls;die;


			//-------------------------------------pre_cost_commis_cost_dtls_h-------------------------------------------
			$sql_tot_cost="insert into qc_tot_cost_summary_history( id,approved_no, tot_sum_id, mst_id,  buyer_agent_id, location_id, no_of_pack,  is_confirm, is_cm_calculative, mis_lumsum_cost,  commision_per, tot_fab_cost, tot_sp_operation_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost,  tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost,  tot_commission_cost, tot_cost, tot_fob_cost,  inserted_by, insert_date, updated_by,  update_date, status_active, is_deleted,  tot_rmg_ratio)
				select
				'', $approved_string_dtls,  id, mst_id, buyer_agent_id,location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_accessories_cost,  tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost,  tot_cost, tot_fob_cost, inserted_by,   insert_date, updated_by, update_date,  status_active, is_deleted, tot_rmg_ratio from qc_tot_cost_summary where  mst_id in ($book_nos)";
			//	echo $sql_precost_commis_cost_dtls;die;

			


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
			//sql_multirow_update("wo_pre_cost_mst","approved_by*approved_date",$updateData,"id",$booking_ids,1);
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

		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=36 and mst_id in ($booking_ids)";
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

		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=36 and mst_id in ($confirm_ids)";
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

	if($db_type==2 || $db_type==1 )
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
                    <td>&nbsp;&nbsp;<strong>Style Ref.</strong></td>
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
		 	//$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
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
		$sql_cause="select MAX(id) as id from fabric_booking_approval_cause where entry_form=28 and user_id='$user_id' and booking_id='$wo_id' and status_active=1 and is_deleted=0";
		//echo $sql_cause; die;page_id='$menu_id' and 
		$nameArray_cause=sql_select($sql_cause);
		if(count($nameArray_cause)>0){
			foreach($nameArray_cause as $row)
			{
				$app_cause1=return_field_value("approval_cause", "fabric_booking_approval_cause", "id='".$row[csf("id")]."' and status_active=1 and is_deleted=0");
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
				http.open("POST","quick_costing_approval_controller.php",true);
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
			http.open("POST","quick_costing_approval_controller.php",true);
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

			$approved_no_history=return_field_value("approved_no","approval_history","entry_form=28 and mst_id=$wo_id and approved_by=$user_id");
			$approved_no_cause=return_field_value("approval_no","fabric_booking_approval_cause","entry_form=28 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
			//echo "10**".$approved_no_history.'='.$approved_no_cause; die;

			if($approved_no_history=="" && $approved_no_cause=="")
			{
				//echo "insert"; die;

				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
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

				$field_array="user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
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

					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
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
			$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=36 and mst_id=$mst_id and approved_by=$user_id");
			if($unapproved_cause_id=="")
			{
				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1);

				$field_array="id, page_id, entry_form, user_id, booking_id, approval_type, approval_no, approval_history_id, approval_cause, inserted_by, insert_date, status_active, is_deleted";
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

				$field_array="user_id*booking_id*approval_type*approval_no*approval_history_id*approval_cause*updated_by*update_date*status_active*is_deleted";
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

if($action=="costing_popup")
{
    echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
    extract($_REQUEST);
    ?>
    <script>
        //if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";
        var permission='<?=$permission; ?>';
      
        
        function frm_close()
        {
            parent.emailwindow.hide();
        }

       
    </script>
    <body >
    <div align="center" style="width:100%;">
        <?=load_freeze_divs ("../../",'',1); 

        $sql_cost_summary=sql_select("select  id, mst_id, buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost, tot_rmg_ratio, commercial_cost from qc_tot_cost_summary where mst_id=$qc_no and status_active=1 and is_deleted=0");
        if(count($sql_cost_summary)>0){
            foreach($sql_cost_summary as $row)
            {
                $fabric_cost_qc     =$row[csf('tot_fab_cost')];
                $sp_operation_cost_qc =$row[csf('tot_sp_operation_cost')];
                $accessories_cost_qc=$row[csf('tot_accessories_cost')];
                //$avl_min_qc       =$row[csf('tot_fab_cost')];
                $cm_cost_qc         =$row[csf('tot_cm_cost')];
                $frieght_cost_qc    =$row[csf('tot_fright_cost')];
                $lab_test_cost_qc   =$row[csf('tot_lab_test_cost')];
                $mis_offer_qty_qc   =$row[csf('tot_miscellaneous_cost')];
                $other_cost_qc      =$row[csf('tot_other_cost')];
                $com_cost_qc        =$row[csf('tot_commission_cost')];
				$commercial_cost_qc =$row[csf('commercial_cost')];

                $fob_qc             =$row[csf('tot_cost')];
                $fob_pcs_qc         =$row[csf('tot_fob_cost')];
            }
        }

        $rate_data=''; $tot_cons='';
        $total_qc_yd_cost=$total_qc_yd_cost=$total_qc_knit_cost=$total_qc_df_cost=$total_qc_aop_cost=0;
        
        $sql_cons_rate=sql_select("select id, item_id, particular_type_id, is_calculation, rate_data, tot_cons, rate, ex_percent, value from qc_cons_rate_dtls where mst_id=$qc_no and type=1 and status_active=1 and is_deleted=0 order by id ");
		//echo "select id, particular_type_id, is_calculation, rate_data, tot_cons, ex_percent, value from qc_cons_rate_dtls where mst_id=$qc_no and type=1 and status_active=1 and is_deleted=0 order by id";
       
	   $yarnIdArr=array(); $yarnQcRate=array(); $knittingIdArr=array(); $knitQcRate=array(); $dyeingIdArr=array(); $dyeingQcRate=array(); $aopIdArr=array(); $aopQcRate=array(); $mainfabricBodyQty=$ribBodyQty=$hoodBodyQty=$othersBodyQty=$totBodyconsQty=$ydsBodyQty=0; $withOutConsRateCost=0; $ydsAmount=0; $itemCountArr=array();
	   $mainfabricBodyid=$ribBodyid=$hoodBodyid=$othersBodyid=0;
        foreach($sql_cons_rate as $row){
			if($row[csf('is_calculation')]==1 && $row[csf('rate_data')]!="")
			{
				$tot_cons =$row[csf('tot_cons')];
				$rate_data          =explode('~~',$row[csf('rate_data')]);
				
				if($rate_data[23]!="") 
				{
					$actualCons=0;
					$actualCons=$tot_cons;//$tot_cons*($rate_data[2]/100);
					$yarnIdArr[$row[csf('id')]][$rate_data[23]]=$rate_data[23];
					$yarnQcRate[$row[csf('id')]][$rate_data[23]]=$rate_data[3].'_'.$actualCons.'_'.$rate_data[2];
				}
				if($rate_data[24]!="") 
				{
					$actualCons=0;
					$actualCons=$tot_cons;//$tot_cons*($rate_data[6]/100);
					$yarnIdArr[$row[csf('id')]][$rate_data[24]]=$rate_data[24];
					$yarnQcRate[$row[csf('id')]][$rate_data[24]]=$rate_data[7].'_'.$actualCons.'_'.$rate_data[6];
				}
				if($rate_data[25]!="") 
				{
					$actualCons=0;
					$actualCons=$tot_cons;//$tot_cons*($rate_data[10]/100);
					$yarnIdArr[$row[csf('id')]][$rate_data[25]]=$rate_data[25];
					$yarnQcRate[$row[csf('id')]][$rate_data[25]]=$rate_data[11].'_'.$actualCons.'_'.$rate_data[10];
				}
				
				if($rate_data[27]!="") 
				{
					$knittingIdArr[$row[csf('id')]][$rate_data[27]]=$rate_data[27];
					$knitQcRate[$row[csf('id')]][$rate_data[27]]=$rate_data[28].'_'.$tot_cons;
				}
				if($rate_data[30]!="") 
				{
					$knittingIdArr[$row[csf('id')]][$rate_data[30]]=$rate_data[30];
					$knitQcRate[$row[csf('id')]][$rate_data[30]]=$rate_data[31].'_'.$tot_cons;
				}
				if($rate_data[33]!="") 
				{
					$knittingIdArr[$row[csf('id')]][$rate_data[33]]=$rate_data[33];
					$knitQcRate[$row[csf('id')]][$rate_data[33]]=$rate_data[34].'_'.$tot_cons;
				}
				
				if($rate_data[36]!="") 
				{
					$dyeingIdArr[$row[csf('id')]][$rate_data[36]]=$rate_data[36];
					$dyeingQcRate[$row[csf('id')]][$rate_data[36]]=$rate_data[37].'_'.$tot_cons;
				}
				if($rate_data[39]!="") 
				{
					$dyeingIdArr[$row[csf('id')]][$rate_data[39]]=$rate_data[39];
					$dyeingQcRate[$row[csf('id')]][$rate_data[39]]=$rate_data[40].'_'.$tot_cons;
				}
				if($rate_data[42]!="") 
				{
					$dyeingIdArr[$row[csf('id')]][$rate_data[42]]=$rate_data[42];
					$dyeingQcRate[$row[csf('id')]][$rate_data[42]]=$rate_data[43].'_'.$tot_cons;
				}
				
				if($rate_data[45]!="") 
				{
					$aopIdArr[$row[csf('id')]][$rate_data[45]]=$rate_data[45];
					$aopQcRate[$row[csf('id')]][$rate_data[45]]=$rate_data[46].'_'.$tot_cons;
				}
				if($rate_data[48]!="") 
				{
					$aopIdArr[$row[csf('id')]][$rate_data[48]]=$rate_data[48];
					$aopQcRate[$row[csf('id')]][$rate_data[48]]=$rate_data[49].'_'.$tot_cons;
				}
				if($rate_data[51]!="") 
				{
					$aopIdArr[$row[csf('id')]][$rate_data[51]]=$rate_data[51];
					$aopQcRate[$row[csf('id')]][$rate_data[51]]=$rate_data[52].'_'.$tot_cons;
				}
				
				//echo $rate_data[15].'='.$rate_data[17].'='.$tot_cons.'+++++++++'; 
				$total_qc_yd_cost+= $rate_data[18]*$tot_cons;
				
				if($row[csf('particular_type_id')]==1 || $row[csf('particular_type_id')]==20) 
				{
					$mainfabricBodyQty+=$row[csf('tot_cons')];
					$mainfabricBodyid=$row[csf('id')];
				}
				if($row[csf('particular_type_id')]==4)
				{
					$ribBodyQty+=$row[csf('tot_cons')];
					$ribBodyid=$row[csf('id')];
				}
				if($row[csf('particular_type_id')]==6 || $row[csf('particular_type_id')]==7) 
				{
					$hoodBodyQty+=$row[csf('tot_cons')];
					$hoodBodyid=$row[csf('id')];
				}
				if($row[csf('particular_type_id')]==998) 
				{
					$othersBodyQty+=$row[csf('tot_cons')];
					$othersBodyid=$row[csf('id')];
				}
				$totBodyconsQty+=$row[csf('tot_cons')];
				//if($row[csf('particular_type_id')]==999) $ydsBodyQty+=$row[csf('tot_cons')];
			}
			
			if($row[csf('is_calculation')]!=1 && $row[csf('particular_type_id')]!=999 ) $withOutConsRateCost+=$row[csf('tot_cons')]*$row[csf('rate')];
			if($row[csf('particular_type_id')]==999) { $ydsAmount+=$row[csf('tot_cons')]*$row[csf('rate')]; $ydsBodyQty+=$row[csf('tot_cons')]; }
			$itemCountArr[$row[csf('item_id')]]=$row[csf('item_id')];
        }
		$countItemId=count($itemCountArr);
		if($countItemId==1) $fabconsdisabled=""; else $fabconsdisabled="disabled";
		//echo $mainfabricBodyid.'='.$ribBodyid.'='.$hoodBodyid.'='.$othersBodyid;
		//print_r($knittingIdArr);
		$companyArr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
		$buyerArr = return_library_array("select id,short_name from lib_buyer ","id","short_name");
		$color_library_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
		$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
        //echo $tot_cons; die;
        $sql_mst="select a.id, a.qc_no, a.company_id, a.location_id, a.cost_sheet_no, a.lib_item_id, a.buyer_id, a.style_des, a.style_ref, a.season_id, a.department_id, a.offer_qty, a.uom, a.delivery_date, a.costing_date, a.inserted_by, a.approved, a.approved_by, a.approved_date, a.revise_no, a.option_id, a.exchange_rate, a.costing_per from qc_mst a, qc_confirm_mst b where a.qc_no=$qc_no and a.qc_no=b.cost_sheet_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";// and (b.job_id is null or b.job_id =0) and a.approved not in(1,3)
        $sql_mst_res=sql_select($sql_mst); $costingper=0;
        foreach($sql_mst_res as $row){ 
            $cost_sheet_no = $row[csf('cost_sheet_no')];
            $style_des  = $row[csf('style_des')];
            $offer_qty  = $row[csf('offer_qty')];
            $style_ref  = $row[csf('style_ref')];
            $revise_no  = $row[csf('revise_no')];
            $option_id  = $row[csf('option_id')];
            $buyer_id   = $row[csf('buyer_id')];
            $buyer_name = $buyerArr[$row[csf('buyer_id')]];
			$company_name =  $companyArr[$row[csf('company_id')]];
			$company_id = $row[csf('company_id')];
			$location_id = $row[csf('location_id')];
			$costing_date = $row[csf('costing_date')];
			$exchange_rate = $row[csf('exchange_rate')];
			$costingper= $row[csf('costing_per')];
        }

        $sql_qc=sql_select("select id, qc_no, fabric_cost, accessories_cost, avl_min, cm_cost, frieght_cost, lab_test_cost, mis_offer_qty, other_cost, commercial_cost, com_cost, fob, fob_pcs, margin, margin_percent, total_yarn_cost, yarn_dyeing_cost, knitting_cost, df_cost, aop_cost, total_cost, buyer, cpm, smv, efficency, cm, available_min, special_operation, main_fabric_top, rib, hood, others, totbodycons, yds, fabricpurchasekg, fabricpurchaseyds from qc_margin_mst where qc_no=$qc_no and status_active=1 and is_deleted=0");
        $actualMainfabricBodyQty=$actualRibBodyQty=$actualHoodBodyQty=$actualOthersBodyQty=$actualTotBodyconsQty=$actualYdsBodyQty=0;
        if(count($sql_qc)>0){
            foreach($sql_qc as $row)
            {
                $update_id              =$row[csf('id')];
                $fabric_cost            =$row[csf('fabric_cost')];
                $accessories_cost       =$row[csf('accessories_cost')];
                $avl_min                =$row[csf('avl_min')];
                $cm_cost                =$row[csf('cm_cost')];
                $frieght_cost           =$row[csf('frieght_cost')];
                $lab_test_cost          =$row[csf('lab_test_cost')];
                $mis_offer_qty          =$row[csf('mis_offer_qty')];
                $other_cost             =$row[csf('other_cost')];
				$commercial_cost        =$row[csf('commercial_cost')];
                $com_cost               =$row[csf('com_cost')];
                $fob                    =$row[csf('fob')];
                $fob_pcs                =$row[csf('fob_pcs')];
                $margin                 =$row[csf('margin')];
                $margin_percent         =$row[csf('margin_percent')];
                $total_yarn_cost        =$row[csf('total_yarn_cost')];
                $yarn_dyeing_cost       =$row[csf('yarn_dyeing_cost')];
                $knitting_cost          =$row[csf('knitting_cost')];
                $df_cost                =$row[csf('df_cost')];
                $aop_cost               =$row[csf('aop_cost')];
                $buyer                  =$row[csf('buyer')];
                $cpm                    =$row[csf('cpm')];
                $smv                    =$row[csf('smv')];
                $efficency              =$row[csf('efficency')];
                $cm                     =$row[csf('cm')];
                $available_min          =$row[csf('available_min')];
                $sp_operation_cost      =$row[csf('special_operation')];
				
				$actualMainfabricBodyQty=$row[csf('main_fabric_top')];
				$actualRibBodyQty=$row[csf('rib')];
				$actualHoodBodyQty=$row[csf('hood')];
				$actualOthersBodyQty=$row[csf('others')];
				$actualTotBodyconsQty=$row[csf('totbodycons')];
				$actualYdsBodyQty=$row[csf('yds')];
				
				$withOutConsRateCost=$row[csf('fabricpurchasekg')];
				$ydsAmount=$row[csf('fabricpurchaseyds')];
				
                $update_button_active   =1;
            }
            //$buyer_name = return_field_value("short_name", "lib_buyer", "id='$buyer' and status_active = 1", "short_name");
        } 
        else 
        {
			if($actualMainfabricBodyQty==0) $actualMainfabricBodyQty=$mainfabricBodyQty;
			if($actualRibBodyQty==0) $actualRibBodyQty=$ribBodyQty;
			if($actualHoodBodyQty==0) $actualHoodBodyQty=$hoodBodyQty;
			if($actualOthersBodyQty==0) $actualOthersBodyQty=$othersBodyQty;
			if($actualTotBodyconsQty==0) $actualTotBodyconsQty=$totBodyconsQty;
			if($actualYdsBodyQty==0) $actualYdsBodyQty=$ydsBodyQty;
			
            $update_id=$fabric_cost=$accessories_cost=$avl_min=$cm_cost=$frieght_cost=$lab_test_cost=$mis_offer_qty=$other_cost=$com_cost=$fob=$margin=$margin_percent=$total_yarn_cost=$yarn_dyeing_cost=$knitting_cost=$df_cost=$aop_cost=$total_cost=$cpm=$smv=$efficency=$cm=$available_min='';
            $update_button_active   =0;

            $fabric_cost=$fabric_cost_qc;
            $accessories_cost=$accessories_cost_qc;
            $cm_cost=$cm_cost_qc;
            $frieght_cost=$frieght_cost_qc;
            $lab_test_cost=$lab_test_cost_qc;
            $mis_offer_qty=$mis_offer_qty_qc;
            $other_cost=$other_cost_qc;
			$commercial_cost=$commercial_cost_qc;
            $com_cost=$com_cost_qc;
            $sp_operation_cost=$sp_operation_cost_qc;
            
            $fob=$fabric_cost+$sp_operation_cost+$accessories_cost+$cm_cost+$frieght_cost+$lab_test_cost+$mis_offer_qty+$other_cost+$commercial_cost+$com_cost;
            $fob_pcs=$fob_pcs_qc;
            //$fob_pcs=$fob/12;
            $margin=$fob_qc-$fob;
            $margin_percent=($margin/$fob)*100;

            $applyingDate=date('d-M-Y');
			$cost_per_minute_variable=return_field_value("yarn_iss_with_serv_app as cost_per_minute","variable_order_tracking","company_name ='$company_id' and variable_list=67 and is_deleted=0 and status_active=1","cost_per_minute");
			if($cost_per_minute_variable=="" || $cost_per_minute_variable==0) $cost_per_minute_variable=0; else $cost_per_minute_variable=$cost_per_minute_variable;
			//echo $cost_per_minute_variable;
			// class="form_caption" style="font-size:large"
			if($db_type==0) $limit_cond="LIMIT 1"; else if($db_type==2) $limit_cond="";
			if($location_id>0 && $cost_per_minute_variable==1)
			{
				$sql="select b.cost_per_minute from lib_standard_cm_entry a, lib_standard_cm_entry_dtls b where a.id=b.mst_id and a.company_id='$company_id' and b.location_id='$location_id' and '$applyingDate' between b.applying_period_date and b.applying_period_to_date and b.status_active=1 and b.is_deleted=0 $limit_cond";
			}
			else 
			{
				$sql="select cost_per_minute from lib_standard_cm_entry where company_id='$company_id' and '$applyingDate' between applying_period_date and applying_period_to_date and status_active=1 and is_deleted=0";
			}
			//echo $sql;
			$dataSql=sql_select($sql); $cpm=0;
			foreach ($dataSql as $row)
			{
				if($row[csf("cost_per_minute")]!="") $cpm=$row[csf("cost_per_minute")];
			}
			unset($dataSql);
			
			$effPerSql="select sum(available_min) as available_min, sum(produce_min) as produce_min from production_logicsoft where buyer = '$buyer_name' and production_date<'$costing_date'";
			$effPerSqlData=sql_select($effPerSql); $efficency=0;
			foreach ($effPerSqlData as $row)
			{
				$efficency=($row[csf("produce_min")]/$row[csf("available_min")])*100;
			}
			unset($effPerSqlData);
			if(($efficency*1)!=0) $efficency=number_format($efficency,4,'.','');
			
			$yarnLibIdArr=array(); $knitLibIdArr=array(); $dyeLibIdArr=array(); $aopLibIdArr=array();
			foreach($yarnIdArr as $yrid=>$yiddata)
			{
				foreach($yiddata as $ylid)
				{
					$yarnLibIdArr[$ylid]=$ylid;
				}
			}
			//print_r($yarnLibIdArr);
			
			foreach($knittingIdArr as $krid=>$kiddata)
			{
				foreach($kiddata as $klid)
				{
					$knitLibIdArr[$klid]=$klid;
				}
			}
			//print_r($knitLibIdArr);
			
			foreach($dyeingIdArr as $drid=>$diddata)
			{
				foreach($diddata as $dlid)
				{
					$dyeLibIdArr[$dlid]=$dlid;
				}
			}
			//print_r($dyeLibIdArr);
			
			foreach($aopIdArr as $arid=>$aiddata)
			{
				foreach($aiddata as $alid)
				{
					$aopLibIdArr[$alid]=$alid;
				}
			}
			//print_r($aopLibIdArr);
			
			$yarnDataArr=array();
			if(implode(",",$yarnIdArr)!="")
			{
				$sql="select id, supplier_id, yarn_count, composition, percent, yarn_type, rate from lib_yarn_rate where status_active=1 and is_deleted=0 and id in (".implode(",",$yarnLibIdArr).") order by id desc";
				$data_array=sql_select($sql);
				foreach($data_array as $row)
				{
					$yarnDataArr[$row[csf('id')]]=$lib_yarn_count[$row[csf('yarn_count')]].'_'.$yarn_type[$row[csf('yarn_type')]].'_'.$composition[$row[csf('composition')]].'_'.$row[csf('rate')];
				}
				unset($data_array);
			}
			
			$knittingDataArr=array();
			if(implode(",",$knittingIdArr)!="")
			{
				$sql="select id, body_part, const_comp, gsm, gauge, yarn_description, uom_id, in_house_rate, buyer_id from lib_subcon_charge where is_deleted=0 and rate_type_id=2 and status_active=1 and id in (".implode(",",$knitLibIdArr).") order by id desc";
				$data_array=sql_select($sql);
				foreach($data_array as $row)
				{
					$knittingDataArr[$row[csf('id')]]=$body_part[$row[csf('body_part')]].'_'.$row[csf('const_comp')].'_'.$row[csf('yarn_description')].'_'.($row[csf('in_house_rate')]/$exchange_rate);
				}
				unset($data_array);
			}
			
			$dyeingDataArr=array();
			if(implode(",",$dyeingIdArr)!="")
			{
				$sql="select id, comapny_id, const_comp, process_type_id, process_id, color_id, width_dia_id, uom_id, buyer_id, in_house_rate, color_range_id from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id in (3,4,7,6) and id in (".implode(",",$dyeLibIdArr).")";
				$data_array=sql_select($sql);
				foreach($data_array as $row)
				{
					$dyeingDataArr[$row[csf('id')]]=$color_library_arr[$row[csf('color_id')]].'_'.($row[csf('in_house_rate')]/$exchange_rate).'_'.$process_type[$row[csf('process_type_id')]].'_'.$color_range[$row[csf('color_range_id')]];
				}
				unset($data_array);
			}
			
			$aopDataArr=array();
			if(implode(",",$aopIdArr)!="")
			{
				$sql="select id, comapny_id, const_comp, process_type_id, process_id, color_id, width_dia_id, in_house_rate, uom_id, rate_type_id ,customer_rate, buyer_id, status_active,color_range_id from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id in (3,4,7,6) and process_id=35 and id in (".implode(",",$aopLibIdArr).")";
				$data_array=sql_select($sql);
				foreach($data_array as $row)
				{
					$aopDataArr[$row[csf('id')]]=$color_library_arr[$row[csf('color_id')]].'_'.($row[csf('in_house_rate')]/$exchange_rate).'_'.$process_type[$row[csf('process_type_id')]].'_'.$color_range[$row[csf('color_range_id')]];
				}
				unset($data_array);
			}
        }
		if($costingper==2) $costingcap="$/PCS"; else if($costingper==1) $costingcap="$/DZN"; else $costingcap="";
        ?>
        <fieldset style="width:835px ">
            <legend><?="Company:$company_name; Cost Sheet No : $cost_sheet_no;  Option: $option_id; Revise No: $revise_no; Style Desc.:$style_des; Style Ref.: $style_ref; Costing Per: $qccosting_per[$costingper]"; ?><input type="hidden" class="text_boxes" name="cbo_costingper_id" id="cbo_costingper_id" value="<?=$costingper; ?>" ></legend>
        
        <form name="quick_cosing_entry" id="quick_cosing_entry" enctype="multipart/form-data" method="post">
            <div style="float: left">
                <div style="width: 250px; float: left;">
                    <table width="250" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left">
                        <thead>
                            <tr>
                                <th colspan="3">Marketing Actual Cost</th>
                            </tr>       
                            <tr>
                                <th width="120">Description</th>
                                <th width="65">QC Cost</th>
                                <th width="65">Actual Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td width="120">Fabric</td> 
                                <td width="65"><p><? echo $fabric_cost_qc; ?></p></td>
                                <td width="65"><p><? echo $fabric_cost; ?></p></td>
                            </tr>
                            <tr>
                                <td width="120">Special Operation</td>
                                <td width="65"><p><? echo $sp_operation_cost_qc; ?></p></td>
                                <td width="65"><p><? echo $sp_operation_cost; ?></p></td>
                            </tr>
                            <tr>
                                <td width="120">Accessories</td>
                                <td width="65"><p><? echo $accessories_cost_qc; ?></p></td>
                                <td width="65"><p><? echo $accessories_cost; ?></p></td>
                            </tr>
                            <tr>
                                <td width="120">CM (<?=$costingcap; ?>)</td>
                                <td width="65"><p><?=$cm_cost_qc; ?></p></td>
                                <td width="65" title="(((CPM*100)/Efficiency)*SMV)*<?=$costingcap; ?>"><p><?=$cm_cost; ?></p></td>
                            </tr>
                            <tr>
                                <td width="120">Frieght Cost(<?=$costingcap; ?>)</td>
                                <td width="65"><p><? echo $frieght_cost_qc; ?></p></td>
                                <td width="65"><p><? echo $frieght_cost; ?></p></td>
                            </tr>
                            <tr>
                                <td width="120">Lab - Test(<?=$costingcap; ?>)</td>
                                <td width="65"><p><? echo $lab_test_cost_qc; ?></p></td>
                                <td width="65"><p><? echo $lab_test_cost; ?></p></td>
                            </tr>
                            <tr>
                                <td width="120">Mis/Offer Qty.</td>
                                <td width="65"><p><? echo $mis_offer_qty_qc; ?></p></td>
                                <td width="65"><p><? echo $mis_offer_qty; ?></p></td>
                            </tr>
                            <tr>
                                <td width="120">Other Cost(<?=$costingcap; ?>)</td>
                                <td width="65"><p><? echo $other_cost_qc; ?></p></td>
                                <td width="65"><p><? echo $other_cost; ?></p></td>
                            </tr>
                            <tr>
                                <td width="120">Commercial Cost</td>
                                <td width="65"><p><?=$commercial_cost_qc; ?></p></td>
                                <td width="65"><p><?=$commercial_cost; ?></p></td>
                            </tr>
                            <tr>
                                <td width="120">Com.(%)(<?=$costingcap; ?>)</td>
                                <td width="65"><p><? echo $com_cost_qc; ?></p></td>
                                <td width="65"><p><? echo $com_cost; ?></p></td>
                            </tr>
                            <tr>
                                <td width="120"><strong>F.O.B(<?=$costingcap; ?>)</strong></td>
                                <td width="65"><p><?=$fob_qc; ?></p></td>
                                <td width="65" title="fabric_cost+special_operation+accessories_cost+cm_dzn+frieght_dzn+lab_dzn+mis_offer_qty+other_cost_dzn+com_dzn"><p><? echo $fob; ?></p></td>
                            </tr>
                            <tr>
                                <td width="120">F.O.B($/PCS)</td>
                                <td width="65"><p><?=$fob_pcs_qc; ?></p></td>
                                <td width="65"><p><? echo $fob_pcs; ?></p></td>
                            </tr>
                            <tr>
                                <td width="120" >Margin Per/<?=$costingcap; ?></td>
                                <td colspan="2" title="F.O.B($/PCS)-Cost - F.O.B(<?=$costingcap; ?>)" ><p><? echo number_format($margin,4); ?></p>
                               
                                </td>
                            </tr>
                            <tr>
                                <td width="120" >Margin %</td>
                                <td colspan="2" title="Margin Per/DZN * 100"><p><?=number_format($margin_percent,4); ?></p></td>
                            </tr>
                            <tr>
                                <td colspan="3">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="120">AVL Min.</td>
                                <td width="65"><p><? echo $avl_min_qc; ?></p></td>
                                <td width="65"><p><? echo $avl_min; ?></p></td>
                            </tr>
                        </tbody>
                    </table>
                    <table width="250" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left">
                        <thead>
                            <tr>
                                <th colspan="2">CM Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td width="150">Buyer Name</td>
                                <td width="100" align="center"><strong><? echo $buyer_name; ?></strong>
                                    
                                </td>
                            </tr>
                            <tr>
                                <td width="150">CPM</td>
                                <td width="100"><p><?=$cpm; ?></p></td>
                            </tr>
                            <tr>
                                <td width="150">SMV</td>
                                <td width="100"><p><?=$smv; ?></p></td>
                            </tr>
                            <tr>
                                <td width="150">Efficency %</td>
                                <td width="100"><p><?=$efficency; ?></p></td>
                            </tr>
                            <tr>
                                <td width="150">CM</td>
                                <td width="100" title="((((cpm*100)/efficency)*smv)*<?=$costingcap; ?>)/ex_rate"><p><?=$cm; ?></p></td>
                            </tr>
                            <tr>
                                <td width="150">Available Minutes</td>
                                <td width="100" title="(smv*offer_qty)/(efficency/100)"><p><? echo $available_min; ?></p></td>
                            </tr>
                        </tbody>
                        <tfoot>
                        	<tr>
                                <td colspan="2">
								<?
								$data_array2=sql_select("select image_location from common_photo_library where master_tble_id='$qc_no' and form_name='qcv2img' and is_deleted=0 and file_type=1");
								foreach($data_array2 as $img_row)
								{
									?>
									<img src='../../<? echo $img_row[csf('image_location')]; ?>' height='70' width='50' align="middle" />	
									<? 
								}
								?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div style="width: 570px; float: right;">
                	<table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left">
                        <thead>
                            <tr>
                                <th colspan="7">Fabric Consuption </th>
                            </tr>       
                            <tr>
                                <th width="100">Details</th>
                                <th width="75">Main Fabric Top</th>
                                <th width="75">Rib</th>
                                <th width="75">Hood</th>
                                <th width="70">Other</th>
                                <th width="80">Total Cons.</th>
                                <th>Yds Cons.</th>
                            </tr>
                        </thead>
                    </table>
                    <div style="width:570px; max-height:85px; float: left; overflow-y:scroll" id="scroll_body">
                        <table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left" id="tbl_bodyPart">
                            <tbody>
                            	<tr>
                                    <td width="100"><strong>QC Cons.</strong></td>
                                    <td width="75"><p><?=$mainfabricBodyQty; ?></p></td>
                                    <td width="75"><p><?=$ribBodyQty; ?></p></td>
                                    <td width="75"><p><?=$hoodBodyQty; ?></p></td>
                                    <td width="70"><p><?=$othersBodyQty; ?></p></td>
                                    <td width="80"><p><?=$totBodyconsQty; ?></p></td>
                                    <td><p><?=$ydsBodyQty; ?></p></td>
                                </tr>
                                <tr>
                                    <td width="100"><strong>Actual Cons.</strong></td>
                                    <td width="75"><p><?=$actualMainfabricBodyQty; ?></p></td>
                                    <td width="75"><p><?=$actualRibBodyQty; ?></p></td>
                                    <td width="75"><p><?=$actualHoodBodyQty; ?></p></td>
                                    <td width="70"><p><?=$actualOthersBodyQty; ?></p></td>
                                    <td width="80"><p><?=$actualTotBodyconsQty; ?></p></td>
                                    <td><p><?=$actualYdsBodyQty; ?></p></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left">
                        <thead>
                            <tr>
                                <th colspan="7">Yarn details</th>
                            </tr>       
                            <tr>
                                <th width="100">Yarn Count</th>
                                <th width="100">Yarn Type</th>
                                <th width="100">Composition Name</th>
                                <th width="100">Yarn details</th>
                                <th width="50">%</th>
                                <th width="50">QC Rate</th>
                                <th>Actual Rate</th>
                            </tr>
                        </thead>
                    </table>
                    <div style="width:570px; max-height:85px; float: left; overflow-y:scroll" id="scroll_body">
                        <table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left" id="tbl_yarn_cost">
                            <tbody>
                                <?
                                if($update_id!='')
                                {
                                    $yarn_dtls_update=sql_select("select  id, qc_no, mst_id, type, lib_table_id, rate_data_id, yarn_count, yarn_type, yarn_details, qc_rate, actual_rate, febric_type, color_type, color, composition, body_part, feb_desc, yarn_desc, process, tot_cons, actual_cost, ex_percent from qc_margin_dtls where mst_id=$update_id and qc_no=$qc_no and type=1 and status_active=1 and is_deleted=0");
                                    $i=1;
                                    foreach($yarn_dtls_update as $row)
									{
                                        ?>
                                        <tr> 
                                            <td width="100"><p><?=$row[csf('yarn_count')]; ?></p>
                                            </td>
                                            <td width="100"><p><?=$row[csf('yarn_type')]; ?></p></td>
                                            <td width="100"><p><?=$row[csf('composition')]; ?></p></td>
                                            <td width="100"><p><?=$row[csf('yarn_details')]; ?></p></td>
                                            <td width="50"><p><?=$row[csf('ex_percent')]; ?></p></td>
                                            <td width="50" titel="<?=$row[csf('tot_cons')]; ?>"><p><?=$row[csf('qc_rate')]; $total_qc_rate +=$row[csf('qc_rate')]*$row[csf('tot_cons')]; ?></p></td>
                                            <td><p><?=$row[csf('actual_rate')]; ?></p></td>
                                        </tr>
                                        <? $i++;
                                    }
                                }
                                else
                                {
                                    if(count($yarnDataArr)>0)
									{
                                        $i=1; $tot_cons_yarn=''; $rate_data=''; $rate_data_id=''; $ex_percent_yarn=''; $qcData="";
										foreach($yarnIdArr as $rid=>$iddata)
                                        //foreach($yarnDataArr as $yid=>$ydata)
										{
											foreach($iddata as $yid=>$ydata)
											{
												$rate_data=explode('_',$yarnDataArr[$yid]);
												$yCount=$yType=$compo=$yRate=$yPer="";
												
												$yCount=$rate_data[0];
												$yType=$rate_data[1];
												$compo=$rate_data[2];
												$yRate=number_format($rate_data[3],4);
												
												$rate_data_id=$rid; 
												$qcData=explode('_',$yarnQcRate[$rid][$yid]);
												$qcRate=$qcData[0];
												$tot_cons_yarn =$qcData[1];
												$yPer =$qcData[2];
												$actualcostyarn=($tot_cons_yarn*$yRate*($yPer/100));
												$total_yarn_cost+=$actualcostyarn;
												//echo $tot_cons_yarn*(($yPer*1)/100)*$yRate.'<br>';
												//$actual_cons_yarn=$tot_cons_yarn*($yPer/100);
												$yarnDtls="";
												
												if($yCount!="") $yarnDtls.=$yCount;
												if($yType!="") $yarnDtls.=', '.$yType;
												if($compo!="") $yarnDtls.=', '.$compo;
												
												?>
												<tr> 
													<td width="100" title="<?=$tot_cons_yarn.'='.$yPer.'='.$yRate; ?>">
														<p><?=$yCount; ?></p>
													</td>
													<td width="100"><p><?=$yType; ?></p></td>
													<td width="100"><p><?=$compo; ?></p></td>
													<td width="100"><p><?=$yarnDtls; ?></p></td>
                                                    <td width="50"><p><?=$yPer; ?></p></td>
													<td width="50" titel="<?=$tot_cons_yarn; ?>"><p><?=$qcRate; $total_qc_rate+=$qcRate*$tot_cons_yarn; ?></p> </td>
													<td><p><?=$yRate; ?></p></td>
												</tr>
												<? 
												$i++;
											}
										}
                                    }
									else
									{
										?>
										<tr> 
											<td width="100">
												
											</td>
											<td width="100"></td>
											<td width="100"></td>
											<td width="100"></td>
                                            <td width="50"></td>
											<td width="50"> </td>
											<td></td>
										</tr>
                                        <?
									}
                                }
                                ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="5" align="right"><strong>Total Yarn Cost</strong></td>
                                <td width="50"><p><?=$total_qc_rate; ?></p></td>
                                <td width="50" ><p><?=$total_yarn_cost; ?></p></td>
                            </tr>
                            <tr>
                                <td colspan="5" align="right">Yarn Dyeing Cost</td>
                                <td width="50"><p><? echo $total_qc_yd_cost; ?></p> </td>
                                <td width="50"><p><? echo $yarn_dyeing_cost; ?></p> </td>
                            </tr>
                        </tfoot>
                        </table>
                    </div>
                    
                    <table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left">
                        <thead>
                            <tr>    

                                <th colspan="5">Knitting Details</th>
                            </tr>       
                            <tr>
                                <th width="150">Body Part</th>
                                <th width="150">Fabric Construction</th>
                                <th width="100">Yarn Description</th>
                                <th width="75">QC Rate</th>
                                <th>Actual Rate</th>
                            </tr>
                        </thead>
                    </table>
                    <div style="width:570px; max-height:65px; float: left; overflow-y:scroll" id="scroll_body">
                        <table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left" id="tbl_kniting_cost">
                            <tbody>
                                <? $total_qc_knit_cost=0;
                                if($update_id!='')
                                {
                                    $knit_dtls_update=sql_select("select  id, qc_no, mst_id, type, lib_table_id, rate_data_id, yarn_count, yarn_type, yarn_details, qc_rate, actual_rate, febric_type, color_type, color, composition, body_part, feb_desc, yarn_desc, process, tot_cons from qc_margin_dtls where mst_id=$update_id and qc_no=$qc_no and type=2 and status_active=1 and is_deleted=0");
                                    $j=1;
                                    foreach($knit_dtls_update as $row){ 
                                        ?>
                                        <tr> 
                                            <td width="150"><p><? echo $row[csf('body_part')]; ?></p></td>
                                            <td width="150"><p><? echo $row[csf('feb_desc')]; ?></p></td>
                                            <td width="100"><p><? echo $row[csf('yarn_desc')]; ?></p></td>
                                            <td ><p><? echo $row[csf('qc_rate')]; $total_qc_knit_cost+=$row[csf('tot_cons')]*$row[csf('qc_rate')]; ?></p></td>
                                            <td ><p><? echo $row[csf('actual_rate')]; ?></p></td>
                                        </tr>
                                        <? $j++;
                                    }
                                }
                                else
                                {
                                    if(count($knittingDataArr)>0)
									{
                                        $j=1; $tot_cons_knit=''; $rate_data=''; $rate_data_id=''; $qcData="";
                                        foreach($knittingIdArr as $kid=>$kiddata)
										//foreach($knittingIdArr as $krid=>$kiddata)
										{
											foreach($kiddata as $krid=>$knitdata)
											{
                                            $rate_data=explode('_',$knittingDataArr[$krid]);
											$rate_data_id=$krid;
											
											$qcData=explode('_',$knitQcRate[$kid][$krid]);
											$qcRate =$qcData[0];
											$tot_cons_knit =$qcData[1];
											$bodyPart=$const_comp=$yarn_description=""; $in_house_rate=0;
											
											$bodyPart=$rate_data[0];
											$const_comp=$rate_data[1];
											$yarn_description=$rate_data[2]; 
											$in_house_rate=number_format($rate_data[3],4);
											
											?>
												<tr> 
													<td width="150"><p><?=$bodyPart; ?></p></td>
													<td width="150"><p><?=$const_comp; ?></p></td>
													<td width="100"><p><?=$yarn_description; ?></p></td>
													<td ><p><?=$qcRate; $total_qc_knit_cost+=$tot_cons_knit*$qcRate; ?></p></td>
													<td><p><?=$in_house_rate; ?></p></td>
												</tr>
											<? $j++;
											}
                                        }
                                    }
									else
									{
										?>
                                        <tr> 
                                            <td width="150"></td>
                                            <td width="150"></td>
                                            <td width="100"></td>
                                            <td ></td>
                                            <td></td>
                                        </tr>
                                        <?	
									}
                                }
                                ?>
                            </tbody>
                            <tfoot>
	                            <tr>
	                                <td colspan="3" align="right"><strong>Knitting Cost</strong></td>
	                                <td width="74"><p><? echo $total_qc_knit_cost; ?></p></td>
	                                <td width="74"><p><? echo $knitting_cost; ?></p></td>
	                            </tr>
	                        </tfoot>
                        </table>
                    </div>
                   
                    
                    <table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left">
                        <thead>
                            <tr>
                                <th colspan="5">Dyeing Finishing Details</th>
                            </tr>       
                            <tr>
                                <th width="150">Color Range</th>
                                <th width="150">Color Name</th>
                                <th width="100">Process Name</th>
                                <th width="75">QC Rate</th>
                                <th>Actual Rate</th>
                            </tr>
                        </thead>
                    </table>
                    <div style="width:570px; max-height:55px; float: left; overflow-y:scroll" id="scroll_body">
                        <table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left" id="tbl_df">
                            <tbody>
                                <?
                                if($update_id!='')
                                {
                                    $df_dtls_update=sql_select("select  id, qc_no, mst_id, type, lib_table_id, rate_data_id, yarn_count, yarn_type, yarn_details, qc_rate, actual_rate, febric_type, color_type, color, composition, body_part, feb_desc, yarn_desc, process, tot_cons from qc_margin_dtls where mst_id=$update_id and qc_no=$qc_no and type=3 and status_active=1 and is_deleted=0");
                                    $k=1;
                                    foreach($df_dtls_update as $row){ 
                                        ?>
                                        <tr>
                                            <td width="150"><p><? echo $row[csf('color_type')]; ?></p></td>
                                            <td width="150"><p><? echo $row[csf('color')]; ?></p></td>
                                            <td width="100"><p><? echo $row[csf('process')]; ?></p></td>
                                            <td width="75" ><p><? echo $row[csf('qc_rate')]; $total_qc_df_cost+=$row[csf('qc_rate')]*$row[csf('tot_cons')];  ?></p></td>
                                            <td ><p><? echo $row[csf('actual_rate')]; ?></p></td>
                                        </tr>
                                        <? $k++;
                                    }
                                }
                                else
                                {
                                    if(count($dyeingDataArr)>0)
									{
                                        $k=1; $tot_cons_df=$qcData=''; $rate_data=''; $rate_data_id=''; $qcData="";
										foreach($dyeingIdArr as $drid=>$diddata)
                                       //foreach($dyeingDataArr as $did=>$ddata)
										{
											foreach($diddata as $did=>$ddata)
											{
											//echo $ddata;
                                            $rate_data=explode('_',$dyeingDataArr[$did]);
                                            $rate_data_id=$did;
											$qcData=explode('_',$dyeingQcRate[$drid][$did]);
											$qcRate=$qcData[0];
                                            $tot_cons_df =$qcData[1];
											
											$colorName=$in_house_rate=$process_type_id=$color_range_id="";
											
											$colorName=$rate_data[0];
											$in_house_rate=number_format($rate_data[1],4);
											$process_type_id=$rate_data[2];
											$color_range_id=$rate_data[3];
											
                                            ?>
                                            <tr>
                                                <td width="150"><p><?=$color_range_id; ?></p></td>
                                                <td width="150"><p><?=$colorName; ?></p></td>
                                                <td width="100"><p><?=$process_type_id; ?></p></td>
                                                <td width="75"><p><?=$qcRate; $total_qc_df_cost+=$qcRate*$tot_cons_df; ?></p></td>
                                                <td ><p><?=$in_house_rate; ?></p></td>
                                            </tr>
                                            <? $k++;
											}
                                        }
                                    }
									else
									{
										?>
                                        <tr>
                                            <td width="150"></td>
                                            <td width="150"></td>
                                            <td width="100"></td>
                                            <td width="75" ></td>
                                            <td ></td>
                                        </tr>
                                        <?
									}
                                }
                                ?>
                            </tbody>
                            <tfoot>
	                            <tr>
	                                <td colspan="3" align="right"><strong>Dyeing Finishing Cost</strong></td>
	                                <td width="74" ><p><?=$total_qc_df_cost; ?></p></td>
	                                <td width="74"  ><p><?=$df_cost; ?></p></td>
	                            </tr>
	                        </tfoot>
                        </table>
                    </div>
                   
                    
                    <table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left">
                        <thead>
                            <tr>
                                <th colspan="5">AOP Cost Details</th>
                            </tr>       
                            <tr>
                                <th width="150">Color Range</th>
                                <th width="150">Color Name</th>
                                <th width="100">Process Name</th>
                                <th width="75">QC Rate</th>
                                <th>Actual Rate</th>
                            </tr>
                        </thead>
                    </table>
                    <div style="width:570px; max-height:55px; float: left; overflow-y:scroll" id="scroll_body">
                        <table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left" id="tbl_aop">
                            <tbody>
                                <?
                                if($update_id!='')
                                {
                                    $aop_dtls_update=sql_select("select  id, qc_no, mst_id, type, lib_table_id, rate_data_id, yarn_count, yarn_type, yarn_details, qc_rate, actual_rate, febric_type, color_type, color, composition, body_part, feb_desc, yarn_desc, process, tot_cons,actual_cost from qc_margin_dtls where mst_id=$update_id and qc_no=$qc_no and type=4 and status_active=1 and is_deleted=0");
									//echo $update_id;
                                    $m=1;
                                    foreach($aop_dtls_update as $row){ 
                                        ?>
                                        <tr>
                                            <td width="150"><p><? echo $row[csf('color_type')]; ?></p></td>
                                            <td width="150"><p><? echo $row[csf('color')]; ?></p></td>
                                            <td width="100"><p><? echo $row[csf('process')]; ?></p></td>
                                            <td width="75" ><p><? echo $row[csf('qc_rate')]; $total_qc_aop_cost+=$row[csf('qc_rate')]*$row[csf('tot_cons')]; ?></p></td>
                                            <td ><p><? echo $row[csf('actual_rate')]; ?></p></td>
                                               
                                        </tr>
                                        <? $m++;
                                    }
                                }
                                else
                                {
                                    if(count($aopDataArr)>0)
									{
                                        $m=1; $tot_cons_aop=''; $rate_data=$qcData=''; $rate_data_id=''; $qcData="";
										foreach($aopIdArr as $arid=>$aiddata)
                                        //foreach($aopDataArr as $aid=>$aopData)
										{
											foreach($aiddata as $aid=>$aopData)
											{
                                            $rate_data=explode('_',$aopDataArr[$aid]);
											
											$qcData=explode('_',$aopQcRate[$arid][$aid]);
											$qcRate=$qcData[0];
                                            $tot_cons_aop =$qcData[1];
											
											$colorName=$in_house_rate=$process_type_id=$color_range_id="";
											
											$colorName=$rate_data[0];
											$in_house_rate=number_format($rate_data[1],4);
											$process_type_id=$rate_data[2];
											$color_range_id=$rate_data[3];
											
                                            ?>
                                            <tr>
                                                <td width="150"><?=$color_range_id; ?></td>
                                                <td width="150"><?=$colorName; ?></td>
                                                <td width="100"><p><?=$process_type_id; ?></p></td>
                                                <td width="75" ><p><?=$qcRate; $total_qc_aop_cost+=$qcRate*$tot_cons_aop; ?></p></td>
                                                <td ><p><?=$in_house_rate; ?></p></td>
                                            </tr>
                                            <? $m++;
											}
                                        }
                                    }
									else
									{
										?>
                                        <tr>
                                            <td width="150"></td>
                                            <td width="150"></td>
                                            <td width="100"></td>
                                            <td width="75" ></td>
                                            <td > </td>
                                        </tr>
                                        <?	
									}
                                }
                                ?>
                            </tbody>
                            <tfoot>
	                            <tr>
	                                <td colspan="3" align="right"><strong>AOP Cost</strong></td>
	                                <td width="74"><p><?=$total_qc_aop_cost; ?></p></td>
	                                <td width="74"><p><?=$aop_cost; ?></p></td>
	                            </tr>
	                            <tr>
	                                <td colspan="4" align="right"><strong>Fabric Purchase [Kg]</strong></td>
	                                <td width="74"><p><?=$withOutConsRateCost; ?></p></td>
	                            </tr>
	                            <tr>
	                                <td colspan="4" align="right"><strong>Fabric Purchase Cost[Yds]</strong></td>
	                                <td width="74"><p><?=$ydsAmount; ?></p></td>
	                            </tr>
	                            <tr>
	                                <?
	                                    $qc_total_cost=$total_qc_aop_cost+$total_qc_df_cost+$total_qc_knit_cost+$total_qc_yd_cost+$total_qc_rate; 
	                                    $total_cost=$total_yarn_cost+$yarn_dyeing_cost+$knitting_cost+$df_cost+$aop_cost+$withOutConsRateCost+$ydsAmount; 
	                                    $total_fab_cost=$total_cost*$tot_cons;
										$fabric_cost_qc=$total_qc_rate+$total_qc_yd_cost+$total_qc_knit_cost+$total_qc_df_cost+$total_qc_aop_cost+$withOutConsRateCost+$ydsAmount;
	                                ?>
	                                <td colspan="3" align="right"><strong>Fabric Total Cost</strong></td>
	                                <td width="74"><p><? echo $fabric_cost_qc; //$qc_total_cost; ?></p></td>
	                                <td width="74"><p><? echo $total_cost; ?></p></td>
	                            </tr>
	                        </tfoot>
                        </table>
                    </div>
                    
                </div>
            </div>
            <div style="width: 100%">
                <table  style="width: 100%">
                    <tr>
                        <td height="50" valign="middle" align="center" class="button_container">
                           
                      

                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:100%"><p align="center"><input type="button" id="btn_close" class="formbutton" style="width:100px;" value="Close" onClick="frm_close();" ></p></div>
        </form>
    </div>
    </fieldset>
    <script type="text/javascript">
        $('#txt_fabric').val(<?=$total_cost; ?>);
    </script>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
    <?
	exit();
}
?>
