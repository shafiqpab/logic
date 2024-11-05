<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_ip=$_SESSION['logic_erp']['user_ip'];

extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');
require_once('../../mailer/class.phpmailer.php');
$from_mail="PLATFORM-ERP@fakir.app";
	
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$sequence_no='';
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_item_category=str_replace("'","",$cbo_item_category_id);
	$cbo_req_year=str_replace("'","",$cbo_req_year);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_requsition_no=str_replace("'","",$txt_requsition_no);
	$approval_type=str_replace("'","",$cbo_approval_type);
	$previous_approved=str_replace("'","",$previous_approved);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($txt_alter_user_id!="") $user_id=$txt_alter_user_id;

	$userCatCredFromIdArr = array();
	$categoryData = sql_select("select a.user_id, a.sequence_no, b.item_cate_id from electronic_approval_setup a, user_passwd b where a.user_id = b.id and a.company_id=$cbo_company_name and a.page_id=$menu_id and a.is_deleted=0");

	foreach($categoryData as $row)
	{
		$userCatCredFromIdArr[$row[csf('user_id')]]=$row[csf('item_cate_id')];
	}

	$item_cate_id = $userCatCredFromIdArr[$user_id];   //user category crediatial

	$user_cred_item_cat_cond = "";
	$all_category = array_keys($item_category); 

	if($cbo_item_category ==0)
	{
		if($item_cate_id != "")
		{
			$user_cred_item_cat_cond =  $item_cate_id;
			$category_array = explode(",",$item_cate_id);
		}else{
			$user_cred_item_cat_cond = implode(",",$all_category);
			$category_array = $all_category;
		}

		$cbo_item_category_cond=" and c.item_category_id in (".$user_cred_item_cat_cond.")";
	}
	else
	{
		$cbo_item_category_cond=" and c.item_category_id=$cbo_item_category";
		$category_array = array();
		$category_array[] =   $cbo_item_category;
	}

	$req_date_cond="";
	if ($txt_date_from != '' && $txt_date_to != '')
	{
		if($db_type==0)
		{
			$txt_date_from = date("Y-m-d", strtotime($txt_date_from));
			$txt_date_to = date("Y-m-d", strtotime($txt_date_to));
			$req_date_cond = " and a.indent_date between '$txt_date_from' and '$txt_date_to'";
		}
		else
		{
			$txt_date_from = date("d-M-Y", strtotime($txt_date_from));
			$txt_date_to = date("d-M-Y", strtotime($txt_date_to));
			$req_date_cond = " and a.indent_date between '$txt_date_from' and '$txt_date_to'";
		}	
	}

	if($db_type==0)
	{
		if ($cbo_req_year != 0) $req_year_cond= " and year(a.insert_date)=$cbo_req_year";
		$year_cond_prefix= "year(a.insert_date)";
	}
	else if($db_type==2)
	{
		if ($cbo_req_year != 0) $req_year_cond= " and TO_CHAR(a.insert_date,'YYYY')=$cbo_req_year";
		$year_cond_prefix= "TO_CHAR(a.insert_date,'YYYY')";
	}

	$requsition_cond="";
	if(trim($txt_requsition_no) != "")
	{
		$requsition_cond .= " and a.itemissue_req_prefix_num = '$txt_requsition_no' ";
	}

	if($previous_approved==1 && $approval_type==1) $previous_approved_type=1;
	
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0","seq");
		
	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Item Issue Requisition.</font>";
		die;
	}	  

	if($previous_approved==1 && $approval_type==1)
	{
	
		$sequence_no_cond=" and d.sequence_no<'$user_sequence_no'";
		
		$sql="SELECT a.id, a.itemissue_req_prefix_num, a.itemissue_req_sys_id ,a.company_id, a.is_approved, a.indent_date, a.required_date, a.inserted_by, a.store_id, d.id as approval_id
		from inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c, approval_history d
		where a.id=b.mst_id and b.product_id=c.id and a.id=d.mst_id and a.company_id=$cbo_company_name and a.is_approved in(1,3) and a.ready_to_approved=1 $cbo_item_category_cond  and d.entry_form=26  and d.current_approval_status=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sequence_no_cond $req_date_cond $req_year_cond 
		group by a.id,d.id, a.itemissue_req_prefix_num,a.itemissue_req_sys_id ,a.company_id,a.is_approved, a.indent_date, a.inserted_by, a.store_id, a.required_date
		order by a.id, a.itemissue_req_prefix_num";
	}
	
	else if($approval_type==0) // unapproval process start
	{

		if($user_sequence_no==$min_sequence_no)	// First user
		{
			$sql="SELECT a.id, a.itemissue_req_prefix_num, a.indent_date, a.required_date, a.itemissue_req_sys_id, a.company_id, a.inserted_by, a.store_id, a.is_approved 
			from inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c
			where a.id=b.mst_id and b.product_id=c.id and a.company_id='$company_name' $requsition_cond $cbo_item_category_cond $req_date_cond $req_year_cond and a.ready_to_approved=1 and a.is_approved=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
			group by a.id, a.itemissue_req_prefix_num,a.indent_date,a.required_date,a.itemissue_req_sys_id ,a.company_id,a.inserted_by,a.store_id,a.is_approved order by a.id, a.itemissue_req_prefix_num";
		}
		else // Next user
		{
			$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted = 0");
			if($sequence_no=="") // bypass if previous user Yes
            {
            	if($db_type==0)
				{
					
					$seqSql="select group_concat(sequence_no) as sequence_no_by  from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
					$seqData=sql_select($seqSql);
					
					$sequence_no_by=$seqData[0][csf('sequence_no_by')];
					$requsition_id=return_field_value("group_concat(distinct(app.mst_id)) as requsition_id","inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c, approval_history app"," app.mst_id=a.id and a.id=b.mst_id and b.product_id=c.id and a.id=b.mst_id and a.company_id=$cbo_company_name and  app.sequence_no in ($sequence_no_by) and app.entry_form=26 and app.current_approval_status=1 ","requsition_id");
					$requsition_id=implode(",",array_unique(explode(",",$requsition_id)));
					$requsition_id_app_byuser=return_field_value("group_concat(distinct(app.mst_id)) as requsition_id","inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c, approval_history app","app.mst_id=a.id and a.id=b.mst_id and b.product_id=c.id and a.id=b.mst_id and a.company_id=$cbo_company_name and app.entry_form=26 and app.current_approval_status=1 and app.sequence_no=$user_sequence_no","requsition_id");
				}
				else
				{

					$seqSql="select LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no_by from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
					$seqData=sql_select($seqSql);
					$sequence_no_by=$seqData[0][csf('sequence_no_by')];
					$requsition_id=return_field_value("LISTAGG(app.mst_id, ',') WITHIN GROUP (ORDER BY app.mst_id) as requsition_id","inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c, approval_history app"," app.mst_id=a.id and a.id=b.mst_id and b.product_id=c.id and a.id=b.mst_id and a.company_id=$cbo_company_name and  app.sequence_no in ($sequence_no_by) and app.entry_form=26 and app.current_approval_status=1 ","requsition_id");

					$requsition_id=implode(",",array_unique(explode(",",$requsition_id)));
					$requsition_id_app_byuser=return_field_value("LISTAGG(app.mst_id, ',') WITHIN GROUP (ORDER BY app.mst_id) as requsition_id","inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c, approval_history app","app.mst_id=a.id and a.id=b.mst_id and b.product_id=c.id and a.id=b.mst_id and a.company_id=$cbo_company_name and app.entry_form=26 and app.current_approval_status=1 and app.sequence_no=$user_sequence_no","requsition_id");
					$requsition_id_app_byuser=implode(",",array_unique(explode(",",$requsition_id_app_byuser)));
				}
 	
				$result=array_diff(explode(',',$requsition_id),explode(',',$requsition_id_app_byuser));
				$requsition_id=implode(",",$result);
								
            	if($requsition_id!="")
				{
					$sql = "select U.* from (SELECT a.id,a.itemissue_req_prefix_num, a.indent_date, a.required_date, a.itemissue_req_sys_id, a.company_id, a.inserted_by, a.store_id, a.is_approved
					FROM inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c
					WHERE a.id in ($requsition_id) and a.id=b.mst_id and b.product_id=c.id and a.company_id=$cbo_company_name $cbo_item_category_cond $requ_cond $requsition_cond $req_date_cond $req_year_cond and a.is_approved in(1,3) $approved_by and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
					GROUP by a.id,a.itemissue_req_prefix_num,a.indent_date,a.required_date,a.itemissue_req_sys_id ,a.company_id,a.inserted_by,a.store_id,a.is_approved
					
					union all 
					 
					SELECT a.id, a.itemissue_req_prefix_num, a.indent_date, a.required_date, a.itemissue_req_sys_id, a.company_id, a.inserted_by, a.store_id, a.is_approved
					FROM inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c
					WHERE a.id not in ($requsition_id) and a.id=b.mst_id and b.product_id=c.id and a.company_id=$cbo_company_name $cbo_item_category_cond $requ_cond $requsition_cond $req_date_cond $req_year_cond and a.is_approved in(0) $approved_by and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
					GROUP by a.id,a.itemissue_req_prefix_num,a.indent_date,a.required_date,a.itemissue_req_sys_id ,a.company_id,a.inserted_by,a.store_id,a.is_approved) U order by U.id";
				}
				else
				{
					$sql = "SELECT a.id,a.itemissue_req_prefix_num,a.indent_date,a.required_date,a.itemissue_req_sys_id ,a.company_id, a.inserted_by, a.store_id,a.is_approved
					FROM inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c
					WHERE a.id=b.mst_id and b.product_id=c.id and a.company_id=$cbo_company_name $cbo_item_category_cond $requ_cond $requsition_cond $req_date_cond $req_year_cond and a.is_approved in(0) $approved_by and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
					GROUP by a.id,a.itemissue_req_prefix_num,a.indent_date,a.required_date,a.itemissue_req_sys_id ,a.company_id,a.inserted_by,a.store_id,a.is_approved order by a.id,a.itemissue_req_prefix_num";
					
				}
            }
            else // bypass No
            {
            	$user_sequence_no=$user_sequence_no-1;
				if($sequence_no==$user_sequence_no) 
				{
					$sequence_no_by_pass=$sequence_no;
					$sequence_no_cond=" and d.sequence_no in ($sequence_no_by_pass)";
				}
				else
				{
					if($db_type==0) 
					{
						$sequence_no_by_pass=return_field_value("group_concat(sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
					}
					else if($db_type==2) 
					{
						$sequence_no_by_pass=return_field_value("listagg(sequence_no,',') within group (order by sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
					}
					
					if($sequence_no_by_pass=="") $sequence_no_cond=" and d.sequence_no='$sequence_no'";
					else $sequence_no_cond=" and d.sequence_no in ($sequence_no_by_pass)";
				}
				
				$sql="SELECT a.id,a.itemissue_req_prefix_num,a.indent_date,a.required_date,a.itemissue_req_sys_id ,a.company_id, a.inserted_by, a.store_id, a.is_approved
				FROM inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c, approval_history d
				WHERE a.id=b.mst_id and b.product_id=c.id and a.id=d.mst_id and a.company_id=$cbo_company_name $sequence_no_cond and a.is_approved in(1,3) and a.ready_to_approved=1 $cbo_item_category_cond $requsition_cond $req_date_cond $req_year_cond and d.entry_form=26 and d.current_approval_status=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				GROUP by a.id,a.itemissue_req_prefix_num,a.indent_date,a.required_date,a.itemissue_req_sys_id ,a.company_id, a.inserted_by, a.store_id, a.is_approved
				ORDER by a.id,a.itemissue_req_prefix_num";
            } 
		}
	}
	else // approval process start
	{
		$sequence_no_cond=" and d.sequence_no='$user_sequence_no'";
		$sql="SELECT a.id, a.itemissue_req_prefix_num, a.itemissue_req_sys_id ,a.company_id, a.is_approved, a.indent_date, a.required_date, a.inserted_by, a.store_id, d.id as approval_id
		from inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c, approval_history d
		where a.id=b.mst_id and b.product_id=c.id and a.id=d.mst_id and a.company_id=$cbo_company_name and a.is_approved in(1,3) and a.ready_to_approved=1 $cbo_item_category_cond $requsition_cond $req_date_cond $req_year_cond and d.entry_form=26  and d.current_approval_status=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sequence_no_cond
		group by a.id,d.id, a.itemissue_req_prefix_num,a.itemissue_req_sys_id ,a.company_id,a.is_approved, a.indent_date, a.required_date, a.store_id, a.inserted_by
		order by a.id,a.itemissue_req_prefix_num";
    }
	//echo $sql;
		if($db_type == 0)
		{
			$select_category = " group_concat(c.item_category_id)";
		}else{
			$select_category = " listagg(c.item_category_id,',') within group (order by c.item_category_id)";
		}

		$categoryFromReqId = sql_select("SELECT a.id, $select_category as item_category_ids
		from inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c
		where a.id = b.mst_id and b.product_id = c.id and a.company_id = $cbo_company_name and a.ready_to_approved = 1 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
		group by a.id
		order by a.id");
		$categoryFromReqIdArr = array();
		foreach ($categoryFromReqId as  $value) 
		{
			$itemcat_arr = array(); $category_name = "";
			$itemcat_arr = array_filter(array_unique(explode(",",$value[csf('item_category_ids')])));
			foreach ($itemcat_arr as $cat_id) {
				$category_name .=  $item_category[$cat_id].",";
			}
			$categoryFromReqIdArr[$value[csf('id')]] = chop($category_name,",");
		}

		$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=6 and report_id =156 and is_deleted=0 and status_active=1");
		//echo $print_report_format;
    	$format_ids=explode(",",$print_report_format);

    	$department_arr = return_library_array("select id, department_name from lib_department", "id", "department_name");
    	$sql_user_res=sql_select("select id, user_full_name, department_id from user_passwd");
    	foreach ($sql_user_res as $row) {
    		$user_arr[$row[csf('id')]]['user_full_name']=$row[csf('user_full_name')];
    		$user_arr[$row[csf('id')]]['department_id']=$department_arr[$row[csf('department_id')]];
    	}


		$sql_unapproved=sql_select("select * from fabric_booking_approval_cause where  entry_form=26  and is_deleted=0 and status_active=1");
	$unapproved_request_arr=array();
	$approval_case_arr=array();
	foreach($sql_unapproved as $rowu)
	{
		
		if($rowu[csf('approval_type')]==2)
		{
			$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
		}
		$approval_case_arr[$rowu[csf('booking_id')]][$rowu[csf('approval_type')]]=$rowu[csf('approval_cause')];
	}


	?>


<script type="text/javascript">
	function openmypage_app_cause(wo_id,app_type,i)
		{
			var txt_appv_cause = $("#txt_appv_cause_"+i).val();
			var approval_id = $("#approval_id_"+i).val();
			var data=wo_id+"_"+app_type+"_"+txt_appv_cause+"_"+approval_id;
			var title = 'Approval Cause Info';
			var page_link = 'requires/item_issue_requisition_approval_controller.php?data='+data+'&action=appcause_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=250px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
				$('#txt_appv_cause_'+i).val(appv_cause.value);
			}
		}


		
	</script>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:910px; margin-top:10px">
        <legend>Item Issue Requisition Approval</legend>	
        <div align="center" style="width:100%;">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table" align="left">
                <thead>
                	<th width="40"></th>
                    <th width="40">SL</th>
                    <th width="100">Requisition No</th>
                    <th width="160">Item Category</th>
                    <th width="120">Insert User</th>
                    <th width="120">Department</th>
                    <th width="100">Indent Date</th>
                    <th width="120">Req Date</th>
					<? 
					if($approval_type==0)
					{
						?>
					 	<th width="100">Not Appv. Cause</th>
						<? 
					}
					?>
                </thead>
            </table>  

            <div style="width:930px; overflow-y:scroll; overflow-x: hidden; max-height:330px;" id="buyer_list_view">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table" id="tbl_list_search" align="center">
                    <tbody>
                        <?
						 //echo $sql;
						 
                            $i=1; $j=0; $all_approval_id='';
                            $nameArray=sql_select( $sql );
                            foreach ($nameArray as $row)
                            {
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								
								
								$value='';
								$unapprove_value_id=$row[csf('id')];
								if($approval_type==0)
								{
									$value=$row[csf('id')];
								}
								else
								{									
									$value=$row[csf('id')]."**".$row[csf('approval_id')];
								}

								$variable='';
								if($format_ids[$j]==78) // Print 
                                {
                                	$type=1;
                                    $variable="<a href='#'  title='".$format_ids[$j]."'  onclick=\"print_report('".$row[csf('company_id')]."','".$row[csf('id')]."','".$row[csf('itemissue_req_sys_id')]."','".$row[csf('store_id')]."','".$row[csf('is_approved')]."','".$type."')\"> ".$row[csf('itemissue_req_prefix_num')]." <a/>";

                                }
                               	elseif($format_ids[$j]==66) // Print 2 
                                {
                                    $type=2;
                                    $variable="<a href='#'  title='".$format_ids[$j]."'  onclick=\"print_report('".$row[csf('company_id')]."','".$row[csf('id')]."','".$row[csf('itemissue_req_sys_id')]."','".$row[csf('store_id')]."','".$row[csf('is_approved')]."','".$type."')\"> ".$row[csf('itemissue_req_prefix_num')]." <a/>";
                                }
                                elseif($format_ids[$j]==85) // Print 3
                                {	
                                	$type=3;
                                    $variable="<a href='#'  title='".$format_ids[$j]."'  onclick=\"print_report('".$row[csf('company_id')]."','".$row[csf('id')]."','".$row[csf('itemissue_req_sys_id')]."','".$row[csf('store_id')]."','".$row[csf('is_approved')]."','".$type."')\"> ".$row[csf('itemissue_req_prefix_num')]." <a/>";
                                }
								else{
									$variable=$row[csf('itemissue_req_prefix_num')];
								}
								//echo $row[csf('approval_id')];
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
										<td width="40" align="center" valign="middle">
											<input type="checkbox" id="tbl_<? echo $i;?>" name="tbl[]"/>
											<input id="req_id_<? echo $i;?>" name="req_id[]" type="hidden" style="width:30px;" value="<? echo $value; ?>" />
                                        	<input id="requisition_id_<? echo $i;?>" name="requisition_id[]" type="hidden" value="<? echo $row[csf('id')]; ?>" />
											<input id="approval_id_<?=$i;?>" name="approval_id[]" type="hidden" value="<?=$row[csf('approval_id')]; ?>" />
										</td> 
                                         <td width="40" align="center"><? echo $i; ?></td>
                                        <td width="100" align="center"><? echo $variable; ?></td>
										<td width="160">
											<p>
												<?
													echo $categoryFromReqIdArr[$row[csf('id')]];
												?>
											</p>
										</td>
										<td width="120" align="center"><? echo $user_arr[$row[csf('inserted_by')]]['user_full_name']; ?>&nbsp;
										</td>
										<td width="120" align="center"><? echo $user_arr[$row[csf('inserted_by')]]['department_id']; ?>&nbsp;
										</td>
										<td width="100" align="center"><? if($row[csf('indent_date')]!="0000-00-00") echo change_date_format($row[csf('indent_date')]); ?>&nbsp;
										</td>
							
										<td width="125" align="center"><? if($row[csf('required_date')]!="0000-00-00") echo change_date_format($row[csf('required_date')]); ?>&nbsp;</td>
								<?	if($approval_type==0)
									{
										$casues=$approval_case_arr[$unapprove_value_id][$approval_type]
										?>
										 <td align="center" width="100" style="word-break:break-all">
	                                        	<input name="txt_appv_cause[]" class="text_boxes" readonly placeholder="Please Browse" ID="txt_appv_cause_<?=$i;?>" style="width:70px" value="<? echo $casues;?>" maxlength="50" title="Maximum 50 Character" onClick="openmypage_app_cause(<?=$unapprove_value_id; ?>,<?=$approval_type; ?>,<?=$i;?>)">&nbsp;
	                                    </td>
										<? 
									}?>

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
								$denyBtn="";
								if($approval_type==0) $denyBtn=""; else $denyBtn=" display:none";
                        ?>
                    </tbody>
                </table>
            </div>

            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="910" class="rpt_table" align="left">
				<tfoot>
                    <td width="50" align="center" >
                    	<input type="checkbox" id="all_check" onClick="check_all('all_check')" />
                        <input type="hidden" name="hide_approval_type" id="hide_approval_type" value="<? echo $approval_type; ?>">
                        <font style="display:none"><? echo $all_approval_id; ?></font>
                    </td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/>
					&nbsp;&nbsp;&nbsp;<input type="button" value="<? if($approval_type==0) echo "Deny"; ?>" class="formbutton" style="width:100px;<?=$denyBtn; ?>" onClick="submit_approved(<?=$i; ?>,5);"/></td>
				</tfoot>
			</table>
		</div>
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

	$wo_id=$data_all[0];
	$app_type=$data_all[1];
	$app_cause=$data_all[2];
	$approval_id=$data_all[3];

	if($app_cause=="")
	{
		$sql_cause="select MAX(id) as id from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=26 and user_id='$user_id' and booking_id='$wo_id' and approval_type=$app_type and status_active=1 and is_deleted=0";
		// echo $sql_cause; //die;
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
				http.open("POST","item_issue_requisition_approval_controller.php",true);
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

				
			}
		}

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

			$approved_no_history=return_field_value("approved_no","approval_history","entry_form=26 and mst_id=$wo_id and approved_by=$user_id");
			$approved_no_cause=return_field_value("approval_no","fabric_booking_approval_cause","entry_form=26 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

			//echo "shajjad_".$approved_no_history.'_'.$approved_no_cause; die;

			if($approved_no_history=="" && $approved_no_cause=="")
			{
				//echo "insert"; die;

				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",26,".$user_id.",".$wo_id." ,".$appv_type.",0,".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

				// echo "INSERT INTO fabric_booking_approval_cause (".$field_array.") VALUES ".$data_array; die;
				$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
			

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

				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=26 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
				$data_array="".$page_id."*1*".$user_id."*".$wo_id."*".$appv_type."*0*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

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
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=26 and mst_id=$wo_id and approved_by=$user_id");
				$max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=26 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$page_id.",1,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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

					$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=26 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

					$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
					$data_array="".$page_id."*1*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

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
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=26 and mst_id=$wo_id and approved_by=$user_id");
				$max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=26 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$page_id.",1,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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

					$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=26 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

					$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
					$data_array="".$page_id."*1*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

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
}
if($action=='user_popup')
{
	echo load_html_head_contents("Popup Info","../../",1, 1,'',1,'');
	?>	

	<script>
	  function js_set_value(id)
	  { 
		document.getElementById('selected_id').value=id;
		  parent.emailwindow.hide();
	  }

	</script>

	<form>
	        <input type="hidden" id="selected_id" name="selected_id" /> 
	       <?php
	        $custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');	
			$Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;

			$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.page_id=$menu_id order by sequence_no";
			 $arr=array (2=>$custom_designation,3=>$Department);
			 echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
			?>
	        
	</form>
	<?
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
	
	$msg=''; $flag=''; $response='';
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($txt_alter_user_id!="") 	$user_id_approval=$txt_alter_user_id;
	else						$user_id_approval=$user_id;
	//echo "0**";
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id_approval and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");
	
	if($approval_type==0)
	{
		$response=$req_nos;

		$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");

        if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;
		$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, user_ip,inserted_by,insert_date"; 

		$id=return_next_id( "id","approval_history", 1 ) ;

		$mst_id_approve_arr=array();
		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($req_nos) and entry_form=26 group by mst_id","mst_id","approved_no");

		$approved_status_arr = return_library_array("select id, is_approved from inv_item_issue_requisition_mst where id in($req_nos) ","id","is_approved");

		$approved_no_array=array();
		$reqs_ids=explode(",",$req_nos);
		$i=0;
		foreach($reqs_ids as $val)
		{
			$approved_no=$max_approved_no_arr[$val]['approved_no'];
            $approved_status=$approved_status_arr[$val];
            if($approved_status == 0)
            {
                $approved_no=$approved_no+1;
                $approved_no_array[$val]=$approved_no;
            }
			if($i!=0) $data_array.=",";
			
			$data_array.="(".$id.",26,".$val.",".$approved_no.",".$user_sequence_no.",1,".$user_id_approval.",'".$pc_date_time."','".$user_ip."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
			$id=$id+1;
			$i++;
		}	

		$rID=sql_multirow_update("inv_item_issue_requisition_mst","is_approved",$partial_approval,"id",$req_nos,0);    
        if($rID) $flag=1; else $flag=0;
		
		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=26 and mst_id in ($req_nos)";
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
		
		if($flag==1) $msg='19'; else $msg='21';
	}else if($approval_type==5)
	{



		//========================================================================================
		$sqlBookinghistory="select id, mst_id from approval_history where current_approval_status=1 and entry_form=26 and mst_id in ($req_nos) ";
	
		$nameArray=sql_select($sqlBookinghistory); $bookidstr=""; $approval_id="";
		foreach ($nameArray as $row)
		{
			if($bookidstr=="") $bookidstr=$row[csf('mst_id')]; else $bookidstr.=','.$row[csf('mst_id')];
			if($approval_id=="") $approval_id=$row[csf('id')]; else $approval_id.=','.$row[csf('id')];
		}
		
		$appBookNoId=implode(",",array_filter(array_unique(explode(",",$bookidstr))));
		$approval_ids=implode(",",array_filter(array_unique(explode(",",$approval_id))));
		
		$rID=sql_multirow_update("inv_item_issue_requisition_mst","ready_to_approved",'0',"id",$req_nos,0);
		

		if($rID) $flag=1; else $flag=0;


		if($approval_ids!="")
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=26 and current_approval_status=1 and id in ($approval_ids)";
			// echo "10**".$query;
			$rID2=execute_query($query,1);
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}
		}

		//echo "10**".$rID.'='.$rID2.'='.$rID4.'='.$flag; die;
		$response=$req_nos;
		if($flag==1) $msg='50'; else $msg='51';



	}
	else
	{
		$req_nos = explode(',',$req_nos); 
		
		$reqs_ids=''; $app_ids='';
		
		foreach($req_nos as $value)
		{
			$data = explode('**',$value);
			$reqs_id=$data[0];
			$app_id=$data[1];
			
			if($reqs_ids=='') $reqs_ids=$reqs_id; else $reqs_ids.=",".$reqs_id;
			if($app_ids=='') $app_ids=$app_id; else $app_ids.=",".$app_id;
		}

		$rID=sql_multirow_update("inv_item_issue_requisition_mst","is_approved*ready_to_approved","0*0","id",$reqs_ids,0);
		if($rID) $flag=1; else $flag=0;
		
		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=26 and mst_id in ($reqs_ids)";
		$rID2=execute_query($query,1);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
			
		$data=$user_id_approval."*'".$pc_date_time."'*".$user_id."*'".$pc_date_time."'";
		$rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date*updated_by*update_date",$data,"id",$app_ids,1);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		
		
		$response=$reqs_ids;
		
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


?>