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
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
//$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name");
if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_supplier", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(3) and c.tag_company =$data and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$sequence_no='';
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_supplier=str_replace("'","",$cbo_supplier);

	$cbo_buyer_name=0;
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
	
	
	$item_category_id=str_replace("'","",$cbo_item_category_id);
	$wo_year=str_replace("'","",$cbo_year);
	$wo_no=str_replace("'","",$txt_wo_no);

	if($item_category_id==0) $itemCategoryIdCond= "and b.item_category_id in (5,6,7,23)"; else $itemCategoryIdCond="and b.item_category_id in ($item_category_id)";
	if($cbo_supplier){ $supplierIdCond= " and a.supplier_id=$cbo_supplier "; }
	if ($wo_no=="") $woNoCond=""; else $woNoCond=" and a.wo_number_prefix_num='".trim($wo_no)."' ";
	if ($wo_year=="" || $wo_year==0) $woYearCond="";
	else
	{
		if($db_type==2) $woYearCond=" and to_char(a.insert_date,'YYYY')='".trim($wo_year)."' ";
		else $woYearCond=" and YEAR(a.insert_date)='".trim($wo_year)."' ";
	}

	$woDateCond='';
	if(str_replace("'","",$txt_date)!="")
	{
		if(str_replace("'","",$cbo_get_upto)==1) $woDateCond=" and a.wo_date>$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==2) $woDateCond=" and a.wo_date<=$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==3) $woDateCond=" and a.wo_date=$txt_date";
		else $woDateCond='';
	}

	$approval_type=str_replace("'","",$cbo_approval_type);
	if($previous_approved==1 && $approval_type==1) $previous_approved_type=1;
	//$user_id=133;
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");
	/*$buyer_ids_array = array();
	$buyerData = sql_select("select user_id, sequence_no, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0 ");
	foreach($buyerData as $row)
	{
		$buyer_ids_array[$row[csf('user_id')]]['u']=$row[csf('buyer_id')];
		$buyer_ids_array[$row[csf('sequence_no')]]['s']=$row[csf('buyer_id')];
	}*/
	//print_r($buyer_ids_array);
	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Dyes & Chemical WO Approval.</font>";
		die;
	}
	
	if($db_type==2) $itemCategoryList="rtrim(xmlagg(xmlelement(e,b.item_category_id,',').extract('//text()') order by b.item_category_id).GetClobVal(),',')"; 
	else $itemCategoryList="group_concat(b.item_category_id)";

	 //echo $previous_approved.'--'.$approval_type;die;
	if($previous_approved==1 && $approval_type==1)
	{
		$sequence_no_cond=" and c.sequence_no<'$user_sequence_no'";
		
		$sql= "select a.id, a.company_name, a.wo_number_prefix_num, a.wo_number, a.pay_mode, $itemCategoryList as item_category, a.supplier_id, a.wo_date, a.delivery_date, a.inserted_by, c.id as approval_id, c.sequence_no, c.approved_by
		from wo_non_order_info_mst a, wo_non_order_info_dtls b, approval_history c
		where a.id = b.mst_id and a.id=c.mst_id and c.entry_form=3 and c.current_approval_status=1 and a.ready_to_approved=1 and a.entry_form = 145 and a.company_name=$cbo_company_name and a.is_approved in (1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $itemCategoryIdCond $woNoCond $woYearCond $woDateCond $sequence_no_cond $supplierIdCond
		group by a.id, a.company_name, a.wo_number_prefix_num, a.wo_number, a.pay_mode, a.supplier_id, a.wo_date, a.delivery_date, a.inserted_by, c.id, c.sequence_no, c.approved_by
		order by a.id Desc"; 
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
			//$buyer_ids = $buyer_ids_array[$user_id]['u'];
			//if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";

			$sql= "select a.id, a.company_name, a.wo_number_prefix_num, a.wo_number, a.pay_mode, $itemCategoryList as item_category, a.supplier_id, a.wo_date, a.delivery_date, a.inserted_by, 0 as approval_id, 0 as sequence_no, 0 as approved_by
			from wo_non_order_info_mst a, wo_non_order_info_dtls b
			where a.id = b.mst_id and a.ready_to_approved=1 and a.company_name=$cbo_company_name and a.is_approved in (0,2) and a.entry_form=145 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $itemCategoryIdCond $woNoCond $woYearCond $woDateCond $supplierIdCond
			group by a.id, a.company_name, a.wo_number_prefix_num, a.wo_number, a.pay_mode, a.supplier_id, a.wo_date, a.delivery_date, a.inserted_by
			order by a.id Desc"; 
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
			$pre_cost_id_sql="select distinct (c.mst_id) as pre_cost_id from wo_non_order_info_mst a, wo_non_order_info_dtls b, approval_history c where a.id = b.mst_id and a.id=c.mst_id and c.entry_form=3 and c.current_approval_status=1 and a.ready_to_approved=1 and a.entry_form = 145 and a.company_name=$cbo_company_name and a.is_approved in (1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.sequence_no in ($sequence_no_by_no) $itemCategoryIdCond $woNoCond $woYearCond $woDateCond
			union
			select distinct (c.mst_id) as pre_cost_id from wo_non_order_info_mst a, wo_non_order_info_dtls b, approval_history c where a.id = b.mst_id and a.id=c.mst_id and c.entry_form=3 and c.current_approval_status=1 and a.ready_to_approved=1 and a.entry_form = 145 and a.company_name=$cbo_company_name and a.is_approved in (1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.sequence_no in ($sequence_no_by_yes) $itemCategoryIdCond $woNoCond $woYearCond $woDateCond";
			$bResult=sql_select($pre_cost_id_sql);
			foreach($bResult as $bRow)
			{
				$pre_cost_id.=$bRow[csf('pre_cost_id')].",";
			}
			$pre_cost_id=chop($pre_cost_id,',');
			
			$pre_cost_id_app_sql=sql_select("select c.mst_id as pre_cost_id from wo_non_order_info_mst a, wo_non_order_info_dtls b, approval_history c
			where a.id = b.mst_id and a.id=c.mst_id and c.entry_form=3 and c.current_approval_status=1 and a.ready_to_approved=1 and a.entry_form = 145 and a.company_name=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.sequence_no=$user_sequence_no");

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
						$pre_cost_id_cond.=" and a.id not in($chunk_arr_value)";
					}
				}
				else $pre_cost_id_cond=" and a.id not in($pre_cost_id_app_byuser)";
			}
			else $pre_cost_id_cond="";
			
			$sql= "select a.id, a.company_name, a.wo_number_prefix_num, a.wo_number, a.pay_mode, $itemCategoryList as item_category, a.supplier_id, a.wo_date, a.delivery_date, a.inserted_by, 0 as approval_id, 0 as sequence_no, 0 as approved_by
			from wo_non_order_info_mst a, wo_non_order_info_dtls b
			where a.id = b.mst_id and a.ready_to_approved=1 and a.company_name=$cbo_company_name and a.is_approved in (0,2) and a.entry_form=145 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $itemCategoryIdCond $woNoCond $woYearCond $woDateCond $pre_cost_id_cond $supplierIdCond
			group by a.id, a.company_name, a.wo_number_prefix_num, a.wo_number, a.pay_mode, a.supplier_id, a.wo_date, a.delivery_date, a.inserted_by";
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
						$pre_cost_id_cond2.=" a.id in($chunk_arr_value)";
						$slcunk++;	
					}
					$pre_cost_id_cond2.=" )";
				}
				else $pre_cost_id_cond2.=" a.id in($pre_cost_id)";	 
				
				$sql.=" union all
						select a.id, a.company_name, a.wo_number_prefix_num, a.wo_number, a.pay_mode, $itemCategoryList as item_category, a.supplier_id, a.wo_date, a.delivery_date, a.inserted_by, c.id as approval_id, c.sequence_no, c.approved_by
					from wo_non_order_info_mst a, wo_non_order_info_dtls b, approval_history c
					where a.id = b.mst_id and a.id=c.mst_id and c.entry_form=3 and c.current_approval_status=1 and a.ready_to_approved=1 and a.entry_form = 145 and a.company_name=$cbo_company_name and a.is_approved in (1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $itemCategoryIdCond $woNoCond $woYearCond $woDateCond $pre_cost_id_cond2 $supplierIdCond
					group by a.id, a.company_name, a.wo_number_prefix_num, a.wo_number, a.pay_mode, a.supplier_id, a.wo_date, a.delivery_date, a.inserted_by, c.id, c.sequence_no, c.approved_by";
			}
			$sql.=" order by a.id Desc";
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

			$sql="select a.id, a.company_name, a.wo_number_prefix_num, a.wo_number, a.pay_mode, $itemCategoryList as item_category, a.supplier_id, a.wo_date, a.delivery_date, a.inserted_by, c.id as approval_id, c.sequence_no, c.approved_by
			from wo_non_order_info_mst a, wo_non_order_info_dtls b, approval_history c
			where a.id = b.mst_id and a.id=c.mst_id and c.entry_form=3 and c.current_approval_status=1 and a.ready_to_approved=1 and a.entry_form = 145 and a.company_name=$cbo_company_name and a.is_approved in (1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $itemCategoryIdCond $woNoCond $woYearCond $woDateCond $sequence_no_cond $supplierIdCond
			group by a.id, a.company_name, a.wo_number_prefix_num, a.wo_number, a.pay_mode, a.supplier_id, a.wo_date, a.delivery_date, a.inserted_by, c.id, c.sequence_no, c.approved_by order by a.id Desc";
			//echo $sql; die;
		}
	}
	else
	{ 
		$buyer_ids=$buyer_ids_array[$user_id]['u'];
		if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";

		$sequence_no_cond=" and c.sequence_no='$user_sequence_no'";
		
		$sql="select a.id, a.company_name, a.wo_number_prefix_num, a.wo_number, a.pay_mode, $itemCategoryList as item_category, a.supplier_id, a.wo_date, a.delivery_date, a.inserted_by, c.id as approval_id, c.sequence_no, c.approved_by
		from wo_non_order_info_mst a, wo_non_order_info_dtls b, approval_history c
		where a.id = b.mst_id and a.id=c.mst_id and c.entry_form=3 and c.current_approval_status=1 and a.ready_to_approved=1 and a.entry_form = 145 and a.company_name=$cbo_company_name and a.is_approved in (1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $itemCategoryIdCond $woNoCond $woYearCond $woDateCond $sequence_no_cond $supplierIdCond
		group by a.id, a.company_name, a.wo_number_prefix_num, a.wo_number, a.pay_mode, a.supplier_id, a.wo_date, a.delivery_date, a.inserted_by, c.id, c.sequence_no, c.approved_by order by a.id Desc";
	}
	//echo $sql; //die;
	$nameArray=sql_select( $sql );

	$sql_unapproved=sql_select("select * from fabric_booking_approval_cause where entry_form=3 and approval_type=2 and is_deleted=0 and status_active=1");
	$unapproved_request_arr=array();
	foreach($sql_unapproved as $rowu)
	{
		$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
	}

	//WO button---------------------------------
	$print_report_format_ids = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=5 and report_id in(132) and is_deleted=0 and status_active=1");
	$format_ids=explode(",",$print_report_format_ids);
	$row_id=$format_ids[0];
	// echo $row_id.'d';
	?>
    
    <script>
		function openmypage_app_cause(wo_id,app_type,i)
		{
			var txt_appv_cause = $("#txt_appv_cause_"+i).val();
			var approval_id = $("#approval_id_"+i).val();
			var data=wo_id+"_"+app_type+"_"+txt_appv_cause+"_"+approval_id;
			
			var title = (app_type==0)?'Not Appv. Cause':'Not Un-Appv. Cause';
			var page_link = 'requires/dyes_chemical_wo_approval_controller.php?data='+data+'&action=appcause_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
				$('#txt_appv_cause_'+i).val(appv_cause.value);
			}
		}

	</script>
    
    
    <form name="dyeChemWoApproval_2" id="dyeChemWoApproval_2">
        <fieldset style="width:1025px; margin-top:10px">
        <legend>Dyes & Chemical WO Approval</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1020" class="rpt_table" >
                <thead>
                    <th width="40">&nbsp;</th>
                    <th width="30">SL</th>
                    <th width="110">WO NO</th>
                    <th width="120">Item Catagory</th>
                    <th width="120">Supplier</th>
                    <th width="70">Work Order Date</th>
                    <th width="70">Delivery Date</th>
                    <th width="140">Unapproved Request</th>
                    <th width="100">Insert By</th>
                    <th width="80">Approved Date</th>
                    <th><? if($approval_type==0) echo "Not Appv. Cause" ; else echo "Not Un-Appv. Cause"; ?></th>
                </thead>
            </table>
            <div style="width:1021px; overflow-y:scroll; float:left; max-height:330px;" id="buyer_list_view">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
                        $i=1; //die;
						foreach ($nameArray as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							$itemCategoryStr= "";
							$item_arr =  array_unique(explode(",",$row[csf('item_category')]));
							foreach($item_arr as $item_id)
							{
								if($itemCategoryStr=="") $itemCategoryStr=$item_category[$item_id]; else $itemCategoryStr.=', '.$item_category[$item_id];
							}
							
							if($row_id==78) $action_gen='dyes_chemical_work_print'; //Print;
							else if($row_id==84) $action_gen='dyes_chemical_work_print2'; //Print 2;
							else if($row_id==732) $action_gen='dyes_chemical_work_po_print'; //PO Print;
							else if($row_id==85) $action_gen='dyes_chemical_work_print3'; //Print 3;
							else if($row_id==430) $action_gen='dyes_chemical_work_po_print2'; //PO Print 2;
							else $action_gen='dyes_chemical_work_print';
							

							$function="generate_worder_report('".$action_gen."','".$row[csf('id')]."',".$cbo_company_name.");";
							
							$supplierStr="";
							if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5) $supplierStr=$company_arr[$row[csf('supplier_id')]]; else $supplierStr=$supplier_arr[$row[csf('supplier_id')]];
							?>
							<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>" align="center">
								<td width="40" align="center" valign="middle">
									<input type="checkbox" id="tbl_<?=$i;?>" />
									<input id="booking_id_<?=$i;?>" name="booking_id[]" type="hidden" value="<?=$row[csf('id')]; ?>" />
									<input id="booking_no_<?=$i;?>" name="booking_no[]" type="hidden" value="<?=$row[csf('wo_number')]; ?>" />
									<input id="approval_id_<?=$i;?>" name="approval_id[]" type="hidden" value="<?=$row[csf('approval_id')]; ?>" />
									<input id="<?=strtoupper($row[csf('wo_number')]); ?>" name="no_joooob[]" type="hidden" value="<?=$i;?>" />
								</td>
								<td width="30" align="center"><?=$i; ?></td>
								<td width="110"><a href='##' onClick="<?=$function; ?>"><?=$row[csf('wo_number')]; ?></a></td>
								<td width="120" style="word-break:break-all;"><?=$itemCategoryStr; ?></td>
								<td width="120" style="word-break:break-all;"><?=$supplierStr; ?></td>
								<td width="70" style="word-break:break-all;" align="center"><?=change_date_format($row[csf('wo_date')]); ?></td>
								<td width="70" style="word-break:break-all;" align="center"><?=change_date_format($row[csf('delivery_date')]); ?></td>
								
								<td width="140" style="word-break:break-all"><? if($approval_type==1) echo $unapproved_request_arr[$row[csf('id')]]; ?> </td>
								<td width="100" style="word-break:break-all;"><? echo ucfirst($user_arr[$row[csf('inserted_by')]]); ?>&nbsp;</td>
								<td width="80" align="center"><? if($row[csf('approved_date')]!="0000-00-00") echo change_date_format($row[csf('approved_date')]); ?>&nbsp;</td>
                                <td align="center">
                                   <input name="txt_appv_cause[]" class="text_boxes" readonly placeholder="Please Browse" ID="txt_appv_cause_<?=$i;?>" style="width:97px" maxlength="50" title="Maximum 50 Character" onClick="openmypage_app_cause(<?=$row[csf('id')]; ?>,<?=$approval_type; ?>,<?=$i;?>)"></td>
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
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="1000" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" ><input type="checkbox" id="all_check" onClick="check_all('all_check');" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<?=$i; ?>,<?=$approval_type; ?>);"/>&nbsp;&nbsp;&nbsp;<input type="button" value="<? if($approval_type==2) echo "Deny"; ?>" class="formbutton" style="width:100px;<?=$denyBtn; ?>" onClick="submit_approved(<?=$i; ?>,5);"/></td>
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
	list($wo_id,$app_type,$app_cause,$approval_id)=explode('_',$data);

	if($app_cause=="")
	{
		$sql_cause="select MAX(id) as id from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=3 and user_id='$user_id' and booking_id='$wo_id' and approval_type=$app_type and status_active=1 and is_deleted=0";
		
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
				http.open("POST","dyes_chemical_wo_approval_controller.php",true);
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
				 //alert(http.responseText);

				var reponse=trim(http.responseText).split('**');
				show_msg(reponse[0]);

				set_button_status(1, permission, 'fnc_appv_entry',1);
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
			http.open("POST","dyes_chemical_wo_approval_controller.php",true);
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
        <form name="cause_1" id="cause_1">
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
                            if($id_up!='')
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 1,0,"reset_form('cause_1','','','','','');",1);
                            }
                            else
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 0,0,"reset_form('cause_1','','','','','');",1);
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

		if ($operation==0)  // Insert Here
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}

			$approved_no_history=return_field_value("approved_no","approval_history","entry_form=3 and mst_id=$wo_id and approved_by=$user_id");
			$approved_no_cause=return_field_value("approval_no","fabric_booking_approval_cause","entry_form=3 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

			// echo "10**reza_".$approved_no_history.'_'.$approved_no_cause; die;

			if($approved_no_history=="" && $approved_no_cause=="")
			{
				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",3,".$user_id.",".$wo_id." ,".$appv_type.",0,".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
				 //echo "10**".$data_array; die;

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

				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=3 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");


				$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
				$data_array="".$page_id."*3*".$user_id."*".$wo_id."*".$appv_type."*0*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

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
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=3 and mst_id=$wo_id and approved_by=$user_id");
				$max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=3 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				
				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$page_id.",3,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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

					$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=3 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

					$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
					$data_array="".$page_id."*3*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

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
				
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=3 and mst_id=$wo_id and approved_by=$user_id");
				$max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=3 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$page_id.",3,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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

					$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=3 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

					$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
					$data_array="".$page_id."*3*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

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

			$unapproved_cause_id=return_field_value("id","fabric_booking_approval_cause","entry_form=3 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");


			$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=3 and mst_id=$wo_id and approved_by=$user_id");

			if($unapproved_cause_id=="")
			{

				//echo "shajjad_".$unapproved_cause_id; die;

				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_history_id,approval_cause,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",3,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$approval_id.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

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
				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=3 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");

				$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_history_id*approval_cause*updated_by*update_date*status_active*is_deleted";
				$data_array="".$page_id."*3*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$approval_id."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

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
	$msg=''; $flag=''; $response='';
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($txt_alter_user_id!="") 	$user_id_approval=$txt_alter_user_id;
	else						$user_id_approval=$user_id;

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id_approval and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");

	//$buyer_arr=return_library_array( "select b.id, a.buyer_name   from wo_pre_cost_mst b,  wo_po_details_master a where  a.job_no=b.job_no and a.is_deleted=0 and  a.status_active=1 and b.status_active=1 and  b.is_deleted=0 and b.id in ($booking_ids)", "id", "buyer_name"  );
	/*echo "10****";
	print_r($buyer_arr);die;*/
	// echo $user_sequence_no;die();
	//echo "10**".$approval_type; die;
	if($approval_type==2)//Un-Approved
	{
		$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id_approval and is_deleted=0 and bypass=2");
		$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0 and bypass=2");
		//echo "10**".$min_sequence_no.'--'.$user_sequence_no; die;
		if($min_sequence_no!=$user_sequence_no)
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

			/*$sql = sql_select("select b.buyer_id as buyer_id from electronic_approval_setup b where b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no = $user_sequence_no and b.is_deleted=0 group by b.buyer_id"); $userBuyer=0;
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
			}*/
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
				$previous_user_app = sql_select("select id from approval_history where mst_id in($booking_ids) and entry_form=3 and sequence_no <$user_sequence_no and current_approval_status=1 group by id");
				
				if(count($previous_user_app)==0)
				{
					$previous_user_app = sql_select("select id from approval_history where mst_id in($booking_ids) and entry_form=3 and sequence_no in ($previous_user_seq) and current_approval_status=1 group by id");
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

		//if($db_type==2) {$buyer_id_cond=" and a.buyer_id is null "; $buyer_id_cond2=" and b.buyer_id is not null ";}
		//else {$buyer_id_cond=" and a.buyer_id='' "; $buyer_id_cond2=" and b.buyer_id!='' ";}
		//echo "22**";
		$is_not_last_user=return_field_value("a.sequence_no","electronic_approval_setup a"," a.company_id=$cbo_company_name and a.page_id=$menu_id and a.sequence_no>$user_sequence_no and a.bypass=2 and a.is_deleted=0 $buyer_id_cond","sequence_no");

		//echo $is_not_last_user;die;

		$partial_approval = "";
		if($is_not_last_user == "")
		{
			//$credentialUserBuyersArr = [];
			$sql = sql_select("select (b.buyer_id) as buyer_id from electronic_approval_setup b where b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 group by b.buyer_id");
			foreach ($sql as $key => $buyerID) {
				if($buyerID[csf('buyer_id')]!=''){$credentialUserBuyersArr[$buyerID[csf('buyer_id')]] = $buyerID[csf('buyer_id')];}
			}

			//$credentialUserBuyersArr = implode(',',$credentialUserBuyersArr);
			//$credentialUserBuyersArr = explode(',',$credentialUserBuyersArr);
			//$credentialUserBuyersArr = array_unique($credentialUserBuyersArr);
		}
		else
		{
			$check_user_buyer = sql_select("select b.user_id as user_id from user_passwd a, electronic_approval_setup b where a.id = b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 $buyer_id_cond  group by b.user_id");
			//echo "21**".count($check_user_buyer);die;
			if(count($check_user_buyer)==0)
			{
				$sql = sql_select("select b.buyer_id as buyer_id from user_passwd b, electronic_approval_setup a where b.id = a.user_id and a.company_id=$cbo_company_name and a.page_id=$menu_id and a.sequence_no>$user_sequence_no and a.is_deleted=0 and a.bypass=2 $buyer_id_cond $buyer_id_cond2 group by b.buyer_id");
				foreach ($sql as $key => $buyerID) {
					if($buyerID[csf('buyer_id')]!=''){$credentialUserBuyersArr[$buyerID[csf('buyer_id')]] = $buyerID[csf('buyer_id')];}
				}

				$sql = sql_select("select (b.buyer_id) as buyer_id from electronic_approval_setup b where b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 $buyer_id_cond2  group by b.buyer_id");
				foreach ($sql as $key => $buyerID) {
					if($buyerID[csf('buyer_id')]!=''){$credentialUserBuyersArr[$buyerID[csf('buyer_id')]] = $buyerID[csf('buyer_id')];}
				}

				//$credentialUserBuyersArr = implode(',',$credentialUserBuyersArr);
				//$credentialUserBuyersArr = explode(',',$credentialUserBuyersArr);
				//$credentialUserBuyersArr = array_unique($credentialUserBuyersArr);
			}
			//print_r($credentialUserBuyersArr);die;
		}
		// if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;

		$response=$booking_ids;
		$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date,inserted_by,insert_date";
		$id=return_next_id( "id","approval_history", 1 ) ;

		//echo "10**select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=15 group by mst_id"; die;
		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=3 group by mst_id","mst_id","approved_no");

		$approved_status_arr = return_library_array("select id, is_approved from wo_non_order_info_mst where id in($booking_ids)","id","is_approved");
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
				$approved_no_array[$booking_id]=$approved_no;
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
			//echo "10**".$partial_approval;die;
			
			
			 //echo "10**$is_not_last_user";print_r($credentialUserBuyersArr);oci_rollback($con);die;
			
			
			$booking_id_arr[]=$booking_id;
			$data_array_booking_update[$booking_id]=explode("*",($partial_approval));

			if($partial_approval==1)
			{
				$full_approve_booking_id_arr[]=$booking_id;
				$data_array_full_approve_booking_update[$booking_id]=explode("*",($user_id_approval));
			}

			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",3,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')";
			$id=$id+1;
		}

		$flag=1;
		if(count($approved_no_array)>0)
		{
			$approved_string="";
		
			foreach($approved_no_array as $key=>$value)
			{
				$approved_string.=" WHEN $key THEN $value";
			}
			
			$approved_string_mst="CASE id ".$approved_string." END";
			$approved_string_dtls="CASE mst_id ".$approved_string." END";
			
			 $sql_insert="insert into wo_non_order_info_mst_history(id, mst_id, approved_no, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, buyer_po, requisition_no, delivery_place, wo_date, supplier_id, attention, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode, terms_and_condition, is_approved, approved_by, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date) 
				select	
				'', id, $approved_string_mst, garments_nature, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, buyer_po, requisition_no, delivery_place, wo_date, supplier_id, attention, wo_basis_id, item_category, currency_id, delivery_date, source, pay_mode, terms_and_condition, is_approved, approved_by, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date from  wo_non_order_info_mst where id in ($booking_ids)";
			//echo "10**".$sql_insert; die;
			$sql_insert_dtls="insert into wo_non_order_info_dtls_history(id, approved_no, mst_id, requisition_dtls_id, po_breakdown_id, requisition_no, item_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, req_quantity, supplier_order_quantity, uom,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted) 
				select	
				'', $approved_string_dtls, mst_id, requisition_dtls_id, po_breakdown_id, requisition_no, item_id, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color_name, req_quantity, supplier_order_quantity, uom,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_non_order_info_dtls where mst_id in ($booking_ids)";
			//echo "10**".$sql_insert_dtls; die;
		}
		
		$flag=1;
		
		$rID3=execute_query($sql_insert,0);
		if($rID3==1 && $flag==1) $flag=1; else $flag=40; 
			
		$rID4=execute_query($sql_insert_dtls,1);
		if($rID4==1 && $flag==1) $flag=1; else $flag=30; 

		$rID9=1;
		if(count($full_approve_booking_id_arr)>0)
		{
			$field_array_full_approved_booking_update = "approved_by";
			$rID9=execute_query(bulk_update_sql_statement( "wo_non_order_info_mst", "id", $field_array_full_approved_booking_update, $data_array_full_approve_booking_update, $full_approve_booking_id_arr));
			if($rID9==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		$field_array_booking_update = "is_approved";

		$rID=execute_query(bulk_update_sql_statement( "wo_non_order_info_mst", "id", $field_array_booking_update, $data_array_booking_update, $booking_id_arr));
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		//echo $flag; die;
		
		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=3 and mst_id in ($booking_ids)";
		$rIDapp=execute_query($query,1);
		if($rIDapp==1 && $flag==1) $flag=1; else $flag=0;
		
		//echo "10**Insert into approval_history ($field_array) values $data_array"; die;
		$rID2=sql_insert("approval_history",$field_array,$data_array,0);
		//echo $rID2; die;
		if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		//echo "10**".$rID3.'-'.$rID4.'-'.$rID9.'-'.$rID.'-'.$rIDapp.'-'.$rID2.'-'.$flag; die;
		if($flag==1) $msg='19'; else $msg='21';
		//echo $msg."**".$flag;die;
	}
	else if($approval_type==1)//Approved
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
		
		$next_user_app = sql_select("select id from approval_history where mst_id in($booking_ids) and entry_form=3 and sequence_no >$user_sequence_no and current_approval_status=1 group by id");
				
		if(count($next_user_app)>0)
		{
			echo "25**unapproved"; 
			disconnect($con);
			die;
		}
		$flag=1;
		$rID=sql_multirow_update("wo_non_order_info_mst","is_approved*ready_to_approved",'2*0',"id",$booking_ids,0);
		if($rID) $flag=1; else $flag=0;
		//echo "22**".$rID;die;
		//$rID2=sql_multirow_update2("approval_history","current_approval_status",0,"entry_form*mst_id",15*$booking_ids,0);
		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=3 and mst_id in ($booking_ids)";
		$rID2=execute_query($query,1);
		if($rID2==1 && $flag==1) $flag=1; else $flag=0;

		$unapproved_status="UPDATE fabric_booking_approval_cause SET status_active=0,is_deleted=1 WHERE entry_form=3 and approval_type=2 and is_deleted=0 and status_active=1 and booking_id in ($booking_ids)";
		//echo $unapproved_status;die;
		$rIDunapp=execute_query($unapproved_status,1);
		if($rIDunapp==1 && $flag==1) $flag=1; else $flag=0;

		$data=$user_id_approval."*'".$pc_date_time."'*".$user_id."*'".$pc_date_time."'";
		$rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date*updated_by*update_date",$data,"id",$approval_ids,1);
		if($rID3==1 && $flag==1) $flag=1; else $flag=0;
		$response=$booking_ids;
		if($flag==1) $msg='20'; else $msg='22';
	}
	else if($approval_type==5)//Deny
	{
		$sqlBookinghistory="select id, mst_id from approval_history where current_approval_status=1 and entry_form=3 and mst_id in ($booking_ids) ";
		//echo "10**".$sqlBookinghistory;
		$nameArray=sql_select($sqlBookinghistory); $bookidstr=""; $approval_id="";
		foreach ($nameArray as $row)
		{
			if($bookidstr=="") $bookidstr=$row[csf('mst_id')]; else $bookidstr.=','.$row[csf('mst_id')];
			if($approval_id=="") $approval_id=$row[csf('id')]; else $approval_id.=','.$row[csf('id')];
		}
		
		$appBookNoId=implode(",",array_filter(array_unique(explode(",",$bookidstr))));
		$approval_ids=implode(",",array_filter(array_unique(explode(",",$approval_id))));
		
		$flag=1;
		$rID=sql_multirow_update("wo_non_order_info_mst","is_approved*ready_to_approved","2*0","id",$booking_ids,0);
		if($rID) $flag=1; else $flag=0;

		//$rID2=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
		if($approval_ids!="")
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=3 and current_approval_status=1 and id in ($approval_ids)";
			//echo "10**".$query;
			$rID2=execute_query($query,1);
			if($rID2==1 && $flag==1) $flag=1; else $flag=0;
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
			$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.page_id=$menu_id order by b.sequence_no";
			//echo $sql;
			$arr=array (2=>$custom_designation,3=>$Department);
			echo create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
        ?>
    </form>
    <script language="javascript" type="text/javascript">
    setFilterGrid("tbl_style_ref");
    </script>
	<?
	exit();
}
?>
