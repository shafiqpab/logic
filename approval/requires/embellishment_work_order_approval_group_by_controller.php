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

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
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

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$company_fullname_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

//Group app start..............................................................

function getSequence($parameterArr=array()){
	$lib_buyer_id_string=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));

	//Electronic app setup data.....................
	$electronic_app_sql="SELECT COMPANY_ID,PAGE_ID,ENTRY_FORM,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT,GROUP_NO,FABRIC_SOURCE FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['ENTRY_FORM']} AND IS_DELETED = 0 order by SEQUENCE_NO";
 	 //echo $electronic_app_sql;die;
	$electronic_app_sql_result=sql_select($electronic_app_sql);
	$dataArr=array();
	foreach($electronic_app_sql_result as $rows){

		if($rows['BUYER_ID']=='' || $rows['BUYER_ID'] == 0){$rows['BUYER_ID'] = $lib_buyer_id_string;}

		$dataArr['user_by'][$rows['USER_ID']]=$rows;
		$dataArr['sequ_by'][$rows['SEQUENCE_NO']]=$rows;
		$dataArr['group_user_by'][$rows['GROUP_NO']][$rows['USER_ID']]=$rows;
		$dataArr['group_arr'][$rows['GROUP_NO']]=$rows['GROUP_NO'];
		$dataArr['group_seq_arr'][$rows['GROUP_NO']][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
		$dataArr['group_by_seq_arr'][$rows['SEQUENCE_NO']]=$rows['GROUP_NO'];

		$dataArr['bypass_seq_arr'][$rows['BYPASS']][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
		$dataArr['group_bypass_arr'][$rows['GROUP_NO']][$rows['BYPASS']]=$rows['BYPASS'];
	}
	return $dataArr;
}


function getFinalUser($parameterArr=array()){
	$lib_buyer_id_string = implode(',',(array_keys($parameterArr['lib_buyer_arr'])));

	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT,GROUP_NO,FABRIC_SOURCE FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['ENTRY_FORM']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	  //echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){
        if($rows['BUYER_ID']=='' || $rows['BUYER_ID'] == 0){
            $rows['BUYER_ID'] = $lib_buyer_id_string;
        }
		$usersDataArr[$rows['USER_ID']]['BUYER_ID']=explode(',',$rows['BUYER_ID']);
		$usersDataArr[$rows['USER_ID']]['BRAND_ID']=explode(',',$rows['BRAND_ID']);
		$usersDataArr[$rows['USER_ID']]['FABRIC_SOURCE']=explode(',',$rows['FABRIC_SOURCE']);
		
		$userSeqDataArr[$rows['USER_ID']]=$rows['SEQUENCE_NO'];
		$userGroupDataArr[$rows['USER_ID']]=$rows['GROUP_NO'];
		$groupBypassNoDataArr[$rows['GROUP_NO']][$rows['BYPASS']][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
	}

	 //print_r($userDataArr[446]['BUYER_ID']);die;
	// print_r($buyer_wise_brand_id_arr[22]);die;
 
	$finalSeq=array();
	foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
		foreach($userSeqDataArr as $user_id=>$seq){
			if(
				(in_array($bbtsRows['buyer_id'],$usersDataArr[$user_id]['BUYER_ID']) || $bbtsRows['buyer_id'] == 0)
			){
				$finalSeq[$sys_id][$user_id]=$seq;
			}
		 
		}
	}

	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr,'user_group'=>$userGroupDataArr,'group_bypass_no_data_arr'=>$groupBypassNoDataArr);
}


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$sequence_no='';
	$company_name=str_replace("'","",$cbo_company_name);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$file_no=str_replace("'","",$txt_file_no);
	$internal_ref=str_replace("'","",$txt_internal_ref);
	$booking_no=str_replace("'","",$txt_booking_no);
	$booking_year=str_replace("'","",$cbo_booking_year);
    $app_user_id = ($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;

	if ($file_no)$where_con .=" and c.file_no='".trim($file_no)."'"; 
	if ($internal_ref) $where_con .=" and c.grouping like '%".trim($internal_ref)."%' "; 
	if ($booking_no) {$where_con .= " and a.booking_no like('%".trim($booking_no)."')"; }
	if ($booking_year) $where_con .=" and to_char(a.insert_date,'YYYY')='".trim($booking_year)."' ";
	
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
	$job_dealing_merchant_array = return_library_array("select job_no, dealing_marchant from wo_po_details_master","job_no","dealing_marchant");

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
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

    $electronicDataArr=getSequence(array('company_id'=>$cbo_company_name,'ENTRY_FORM'=>32,'user_id'=>$app_user_id,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'product_dept_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0));
	
	$my_seq = $electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO'];
    $my_group = $electronicDataArr['user_by'][$app_user_id]['GROUP_NO'];
	
    $my_group_seq_arr = $electronicDataArr['group_seq_arr'][$my_group];
    $electronicDataArr['group_seq_arr'][0] = [0] + $electronicDataArr['group_seq_arr'][1];

    $my_previous_bypass_no_seq = 0;
    rsort($electronicDataArr['bypass_seq_arr'][2]);
    foreach($electronicDataArr['bypass_seq_arr'][2] as $uid => $seq){
        if($seq<$my_seq){$my_previous_bypass_no_seq = $seq;break;}
    }

 

	if($approval_type==0) // Un-Approve
	{ 
		//Match data..................................
		
		if($electronicDataArr['user_by'][$app_user_id]['BUYER_ID']){
			$where_con .= " and a.BUYER_ID in(".$electronicDataArr['user_by'][$app_user_id]['BUYER_ID'].",0)";
			$electronicDataArr['sequ_by'][0]['BUYER_ID']=$electronicDataArr['user_by'][$app_user_id]['BUYER_ID'];
		}
 
		$data_mast_sql = "SELECT a.ID,a.APPROVED_SEQU_BY,a.APPROVED_GROUP_BY, a.ITEM_CATEGORY,a.FABRIC_SOURCE, a.BUYER_ID, a.IS_APPROVED from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no  and a.company_id=$company_name and a.booking_type=6 and a.item_category in(25) and a.is_short in(2,3) and a.STATUS_ACTIVE=1 and a.IS_DELETED=0  $where_con and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.IS_APPROVED<>1  and a.READY_TO_APPROVED=1 ";
		 //echo $data_mast_sql;die;
		//var_dump($electronicDataArr['group_seq_arr']);die;
		
		//echo $my_previous_bypass_no_seq;die;

		$tmp_sys_id_arr=array();$sys_data_arr=array();
		$data_mas_sql_res=sql_select( $data_mast_sql );
		foreach ($data_mas_sql_res as $row)
		{    
			$group_stage_arr = array();
			for($group = ($row['APPROVED_GROUP_BY'] == $my_group && $electronicDataArr['sequ_by'][$my_seq]['BYPASS'] == 2)?$my_group:$my_group-1;$group >= 0; $group-- ){
				
				krsort($electronicDataArr['group_seq_arr'][$group]);
				foreach($electronicDataArr['group_seq_arr'][$group] as $seq){
					//echo $row['APPROVED_SEQU_BY'];
					if($seq<$my_seq){ 
						if(  
							(in_array($row['BUYER_ID'],explode(',',$electronicDataArr['sequ_by'][$seq]['BUYER_ID'])) || $row['BUYER_ID']==0) && ($row['APPROVED_GROUP_BY'] <= $group)
							)
						{   
						
							if($electronicDataArr['sequ_by'][$seq]['BYPASS']==1){
								$tmp_sys_id_arr[$group][$seq][$row['ID']]=$row['ID'];
							}
							else{
								$tmp_sys_id_arr[$group][$seq][$row['ID']]=$row['ID'];
								if(in_array($my_previous_bypass_no_seq,$electronicDataArr['group_seq_arr'][$my_group]) && $row['APPROVED_SEQU_BY'] < $my_previous_bypass_no_seq){
									unset($tmp_sys_id_arr[$group][$seq][$row['ID']]);
								}
								break 2; break; 
							}
							
							if( (in_array($row['APPROVED_SEQU_BY'],$electronicDataArr['group_seq_arr'][$my_group]) && ($row['APPROVED_SEQU_BY'] != $my_previous_bypass_no_seq ) && $electronicDataArr['group_bypass_arr'][$my_group][2] !=2 ) || (count($group_stage_arr[$row['ID']]) > 1) || ($my_previous_bypass_no_seq < $my_seq) && ($row['APPROVED_SEQU_BY'] < $my_previous_bypass_no_seq )  ){ 
								unset($tmp_sys_id_arr[$group][$seq][$row['ID']]);
								break; 
							}
							$group_stage_arr[$row['ID']][$group] = $group;

						}
					}
				}
				 

				// echo $group_stage.',';
			}//group loof;

			foreach($group_stage_arr as $sys_id => $gidArr){ 
				foreach($gidArr as $gid => $gid){	
					if(count($group_stage_arr[$sys_id])>1 && array_key_first($group_stage_arr[$sys_id]) != $gid ){
						unset($tmp_sys_id_arr[$gid]);
					}
				}
			}

		}
	 	
		//print_r($tmp_sys_id_arr);die;
		// print_r($group_stage);die;
		
		$sql='';
		for($group=0;$group<=$my_group; $group++ ){
			foreach($electronicDataArr['group_seq_arr'][$group] as $seq){
		
				if(count($tmp_sys_id_arr[$group][$seq])){
					$sys_con = where_con_using_array($tmp_sys_id_arr[$group][$seq],0,'a.ID');
					if($sql!=''){$sql .=" UNION ALL ";}
					$approved_user_cond=" and c.approved_by='$user_id'";
					$sql .= "SELECT a.id,a.entry_form, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.is_apply_last_update from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no  and a.company_id=$company_name and a.is_short in(2,3) and a.booking_type=6 and a.item_category in(25) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.APPROVED_SEQU_BY=$seq  and a.APPROVED_GROUP_BY=$group $sys_con and a.is_approved<>1  group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,a.entry_form ";
				}
              
				
			}
		}

		$sql = "select * from ($sql) x  order by x.id DESC";
	}
	else
	{   
		if($electronicDataArr['user_by'][$app_user_id]['BUYER_ID']){
			$where_con .= " and a.BUYER_ID in(".$electronicDataArr['user_by'][$app_user_id]['BUYER_ID'].",0)";
			$electronicDataArr['sequ_by'][0]['BUYER_ID']=$electronicDataArr['user_by'][$app_user_id]['BUYER_ID'];
		}
		
		$sql =  "SELECT a.id,a.entry_form, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.is_apply_last_update from wo_booking_mst a, wo_booking_dtls b ,APPROVAL_MST c where a.booking_no=b.booking_no and a.id=c.mst_id  and a.company_id=$company_name and a.is_short in(2,3) and a.booking_type=6 and a.item_category in(25) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and a.APPROVED_GROUP_BY=c.GROUP_NO  and c.GROUP_NO={$electronicDataArr['user_by'][$app_user_id]['GROUP_NO']}  and b.is_deleted=0 and a.ready_to_approved=1  and a.is_approved<>0 $where_con group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,a.entry_form "; 


    }

 	//echo $sql;die;

	
	$sql_job=sql_select("SELECT a.pay_mode,a.booking_no,b.po_break_down_id ,b.job_no,c.grouping, c.file_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no and b.job_no=c.job_no_mst and b.po_break_down_id =c.id and a.company_id=$company_name and a.is_short in(2,3) and a.booking_type=6 and a.item_category in(25) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1  $booking_no_cond $date_cond $booking_year_cond group by a.pay_mode,a.booking_no,b.po_break_down_id ,b.job_no,c.grouping, c.file_no");
	
	$job_information_arr=array();
	foreach( $sql_job as $jval)
	{
		$job_information_arr[$jval[csf('booking_no')]]['jobno'][]=$jval[csf('job_no')];
		$job_information_arr[$jval[csf('booking_no')]]['po_break_down_id'][]=$jval[csf('po_break_down_id')];
		$job_information_arr[$jval[csf('booking_no')]]['grouping'][]=$jval[csf('grouping')];
		$job_information_arr[$jval[csf('booking_no')]]['file_no'][]=$jval[csf('file_no')];
		$job_information_arr[$jval[csf('booking_no')]]['file_no'][]=$jval[csf('file_no')];
		$job_paymode_arr[$jval[csf('booking_no')]]=$jval[csf('pay_mode')];
	}	

	$sql_style=sql_select("SELECT c.style_ref_no from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c where a.booking_no=b.booking_no and b.job_no=c.job_no  and a.company_id=$company_name and a.is_short in(2,3) and a.booking_type=6 and a.item_category in(25) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.ready_to_approved=1  $booking_no_cond $date_cond $booking_year_cond group by  c.style_ref_no");

	foreach($sql_style as $row)
	{
		$style_ref_no =$row[csf("style_ref_no")];
	}

	?>
    
    <script>
	
		function openmypage_app_cause(wo_id,app_type,i)
		{
			var txt_appv_cause = $("#txt_appv_cause_"+i).val();	
			var approval_id = $("#approval_id_"+i).val();
			
			var data=wo_id+"_"+app_type+"_"+txt_appv_cause+"_"+approval_id;
			
			var title = 'Approval Cause Info';	
			var page_link = 'requires/embellishment_work_order_approval_group_by_controller.php?data='+data+'&action=appcause_popup';
			  
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
			var page_link = 'requires/embellishment_work_order_approval_group_by_controller.php?data='+data+'&action=appinstra_popup';
			  
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
			var page_link = 'requires/embellishment_work_order_approval_group_by_controller.php?data='+data+'&action=unappcause_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=180px,center=1,resize=1,scrolling=0','');
			
			emailwindow.onclose=function()
			{
				var unappv_request=this.contentDoc.getElementById("hidden_unappv_request");
				
				$('#txt_unappv_req_'+i).val(unappv_request.value);
			}
		}
		
	</script>
    
    <?
		 if($approval_type==0)
		 {
			
			$fset=1530;
			$table1=1570; 
			$table2=1572; 
		 }
		 else if($approval_type==1)
		 {
			 $fset=1530;
			 $table1=1570; 
			 $table2=1572; 
		 }
		 
		
		$print_report_format_ids_partial = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=35 and is_deleted=0 and status_active=1");
		$format_ids_partial=explode(",",$print_report_format_ids_partial);
		//print_r($print_report_format_ids_partial);

		// $print_report_format_ids_emb= return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=89 and is_deleted=0 and status_active=1");
		// $format_ids_emb=explode(",",$print_report_format_ids_emb);
		// //print_r($format_ids_emb);
		
		$print_report_format_ids_short = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=2 and is_deleted=0 and status_active=1");
		$print_report_ids_short=explode(",",$print_report_format_ids_short);
		
		
		$print_report_format_ids = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id =122 and is_deleted=0 and status_active=1");
		$format_ids=explode(",",$print_report_format_ids);
		$row_id=$format_ids[0];

		
		
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:1570px; margin-top:10px">
        <legend>Embellishment Work Order Approval</legend>	
            <table align="left" cellspacing="0" cellpadding="0" border="1" rules="all" width="1550" class="rpt_table" >
                <thead>
                	<th width="35"></th>
                    <th width="40">SL</th>
                    <th width="70">Booking No</th>
                    <th width="70">Fabric Source</th>
                    <th width="60">Year</th>
                    <th width="80">Type</th>
                    <th width="100">Booking Date</th>
                    <th width="125">Buyer</th>
                    <th width="160">Supplier</th>
                    <th width="100">Job No</th>
                    <th width="70">Internal Ref</th>
                    <th width="70">File</th>
                    <th width="110">Dealing Merchant</th>
                    <th width="50">Image</th>
                    <th width="50">File</th>
                    <th width="90">Delivery Date</th>
                    <?
					if($approval_type==0) echo "<th width='80'>Appv Instra</th>";
					if($approval_type==1) echo "<th width='80'>Un-appv request</th>"; 
					?>
                    <th><? if($approval_type==0) echo "Not Appv. Cause" ; else echo "Not Un-Appv. Cause"; ?></th>
                </thead>
            </table>            
            <div style="width:1570px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table align="left" cellspacing="0" cellpadding="0" border="1" rules="all" width="1550" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
						//echo $sql;
						 
                            $i=1; $all_approval_id='';
                            $nameArray=sql_select( $sql );
                            foreach ($nameArray as $row)
                            {
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								
								$value=$row[csf('id')];
								
								if($row[csf('booking_type')]==4) 
								{
									$booking_type="Sample";
									$type=3;
								}
								else
								{
									if($row[csf('is_short')]==1) {
                                        $booking_type="Short";
                                    } 
                                    elseif($row[csf('is_short')]==3) {
                                        $booking_type="Dia Wise";
                                    } 
                                    else {
                                        $booking_type="Main";
                                    } 
									$type=$row[csf('is_short')];
								}
								
								//=========== for job file internal reff===========
								$dealing_merchant='';
								$dealing_merchant_arr=array();
								$job_no_arr=array();
								$all_job_no='';
								foreach( $job_information_arr[$row[csf('booking_no')]]['jobno'] as $key=>$job_data )
								{
									$job_no_arr[]=$job_data;
									$dealing_merchant_arr[]=$dealing_merchant_array[$job_dealing_merchant_array[$job_data]];
								}
								
								$job_no_arr=array_unique($job_no_arr);
								$all_job_no=implode(",",$job_no_arr);
								$dealing_merchant_arr=array_unique($dealing_merchant_arr);
								$dealing_merchant=implode(",",$dealing_merchant_arr);
								
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
								
								if($row[csf('approval_id')]==0)
								{
									$print_cond=1;
								}
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
								
								if($print_cond==1)
								{									
								if($row_id==51) $action='preCostRpt2';
								else if($row_id==307)$action='basic_cost';
								else if($row_id==311)$action='bom_epm_woven';
								else if($row_id==313)$action='mkt_source_cost';
								else if($row_id==158) $action='preCostRptWoven';
								else if($row_id==159)$action='bomRptWoven';
								else if($row_id==192)$action='checkListRpt';
								else if($row_id==761) $action='bom_pcs_woven';
								else if($row_id==770) $action='bom_pcs_woven2';
								else if($row_id==381) $action='mo_sheet_2';
								else if($row_id==403) $action='mo_sheet_3';
								else if($row_id==473) $action='slgCostRpt';
			                  
								// $function="generate_worder_report5('".$action."','".$all_job_no."','".$row[csf('company_id')]."','".$row[csf('buyer_id')]."','".$style_ref_no."','".$row[csf('costing_date')]."','','".$row[csf('costing_per')]."');"; 
								
								
								
									$embellishment_print_button=return_field_value("format_id","lib_report_template","template_name ='".$cbo_company_name."' and module_id=2 and report_id=31 and is_deleted=0 and status_active=1");
									$emb_button_id_arr=explode(',',$embellishment_print_button);
									
									if($emb_button_id_arr[0]==86){
										$ction_type="show_trim_booking_report";
									}
									else if($emb_button_id_arr[0]==87){
										$ction_type="show_trim_booking_report1";
									}
									else if($emb_button_id_arr[0]==88){
										$ction_type="show_trim_booking_report2";
									}
									else if($emb_button_id_arr[0]==89){
										$ction_type="show_trim_booking_report_urmi";
									}
									else{
										$emb_button_id_arr[0]=6;
										$ction_type="show_trim_booking_report";
									}
									$multi_embellishment_print_button= return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=89 and is_deleted=0 and status_active=1");
									$multi_emb_button_id_arr=explode(",",$multi_embellishment_print_button);
									//print_r($multi_emb_button_id_arr);
									
									// $multi_embellishment_print_button=return_field_value("format_id","lib_report_template","template_name ='".$cbo_company_name."' and module_id=2 and report_id=244 and is_deleted=0 and status_active=1");
									//print_r($multi_embellishment_print_button);
									$multi_emb_button_id_arr=explode(',',$multi_embellishment_print_button);
									if($multi_emb_button_id_arr[0]==13){
										$ction_type="show_trim_booking_report";
									}
									else if($multi_emb_button_id_arr[0]==14){
										$ction_type="show_trim_booking_report1";
									}
									
									if($row[csf('ENTRY_FORM')]==201){
										if($multi_emb_button_id_arr[0]==235){
											$ction_type="show_trim_booking_report9";
										}
										else if($multi_emb_button_id_arr[0]==13){
											$ction_type="show_trim_booking_report2";
										}
										else if($multi_emb_button_id_arr[0]==15){
											$ction_type="show_trim_booking_report3";
										}
										else if($multi_emb_button_id_arr[0]==16){
											$ction_type="show_trim_booking_report4";
										}
										else if($multi_emb_button_id_arr[0]==177){
											$ction_type="show_trim_booking_report5";
										}
									}

									// if($row[csf('ENTRY_FORM')]==403){
									// 	$multi_emb_button_id_arr[0]=235;
									// 	$ction_type="show_trim_booking_report9";
									// }

									if($row[csf('ENTRY_FORM')]==574){
										$multi_emb_button_id_arr[0]=574;
										$ction_type="show_trim_booking_report2";
									}


								
									$variable="<a href='#' onClick=\"generate_trim_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$all_po_id."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$all_job_no."','".$row[csf('is_approved')]."',".$multi_emb_button_id_arr[0].",'".$ction_type."','".$i."',".$fabric_nature.")\"> ".$row[csf('booking_no_prefix_num')]."<a/>";

									
									
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
										<td width="35" align="center" valign="middle">
											<input type="checkbox" id="tbl_<? echo $i;?>" name="tbl[]" onClick="check_last_update(<? echo $i;?>);" />
											<input id="booking_id_<? echo $i;?>" name="booking_id[]" type="hidden" value="<? echo $value; ?>" />
											<input id="booking_no_<? echo $i;?>" name="booking_no[]" type="hidden" value="<? echo $row[csf('booking_no')]; ?>" />
											<input id="approval_id_<? echo $i;?>" name="approval_id[]" type="hidden" value="<? echo $row[csf('approval_id')]; ?>" />
                                            <input id="last_update_<? echo $i;?>" name="last_update[]" type="hidden" value="<? echo $row[csf('is_apply_last_update')]; ?>" />
                                            <input id="<? echo strtoupper($row[csf('booking_no')]); ?>" name="no_booook[]" type="hidden" value="<? echo $i;?>" />
										</td> 
										<td width="40" id="td_<? echo $i; ?>" style="cursor:pointer" align="center" onClick="generate_worder_report2(<? echo $type; ?>,'<? echo $row[csf('booking_no')]; ?>',<? echo $row[csf('company_id')]; ?>,'<? echo $row[csf('po_break_down_id')]; ?>',<? echo $row[csf('item_category')].','.$row[csf('fabric_source')]; ?>,'<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('is_approved')]; ?>','show_fabric_booking_report3')"><? echo $i; ?></td>
                                         
										<td width="70"><p>&nbsp;&nbsp;<? echo $variable; ?></p></td>
                                        <td width="70"><p><? echo $print_button; ?></p></td>
                                        <td width="60" align="center"><? echo $row[csf('year')]; ?></td>
										<td width="80" align="center"><p><? echo $booking_type; ?></p></td>
										<td width="100" align="center"><? if($row[csf('booking_date')]!="0000-00-00") echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</td>
										<td width="125"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
										<td width="160"><p>
										<? 
											if($job_paymode_arr[$row[csf('booking_no')]]==3 || $job_paymode_arr[$row[csf('booking_no')]]==5)
											{
												echo $company_fullname_arr[$row[csf('supplier_id')]];
											}
											else
											{
												echo $supplier_arr[$row[csf('supplier_id')]]; 
											}
										?>

                                        &nbsp;</p></td>
										<td width="100" align="center"><p>
										<? 
										$job_sl=1;
										foreach($job_no_arr as $jobNo){
											$function="generate_worder_report5('".$action."','".$jobNo."','".$row[csf('company_id')]."','".$row[csf('buyer_id')]."','".$style_ref_no."','".$row[csf('costing_date')]."','','".$row[csf('costing_per')]."');"; 
											if($job_sl==1){

												echo "<a href='#' onClick=\"$function\"> ".$jobNo." <a/>";
											}
											else{
												
												echo ", <a href='#' onClick=\"$function\"> ".$jobNo." <a/>";
											}
											$job_sl++;
										
										}
										
										?>
                                        &nbsp;</p></td>
                                        
                                        
                                        <td width="70"><p><? echo $all_internal_ref; ?></p></td>
                    					<td width="70"><? echo $all_file_no; ?></td>
										<td width="110" id="dealing_merchant_<? echo $i;?>"><p><? echo $dealing_merchant; ?>&nbsp;</p></td>
										<td width="50" align="center"><a href="##" onClick="openImgFile('<? echo $all_job_no;?>','img');">View</a></td>
										<td width="50" align="center"><a href="##" onClick="openImgFile('<? echo $all_job_no;?>','file');">View</a></td>
										<td align="center" width="90"><? if($row[csf('delivery_date')]!="0000-00-00") echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</td>                                
                                        <?
										if($approval_type==0)echo "<td align='center' width='80'>
                                        		<Input name='txt_appv_instra[]' class='text_boxes' readonly placeholder='Please Browse' ID='txt_appv_instra_".$i."' style='width:70px' maxlength='50' onClick='openmypage_app_instrac(".$value.",".$approval_type.",".$i.")'></td>";
											if($approval_type==1)echo "<td align='center' width='80'>
                                        		<Input name='txt_unappv_req[]' class='text_boxes' readonly placeholder='Please Browse' ID='txt_unappv_req_".$i."' style='width:70px' maxlength='50' onClick='openmypage_unapp_request(".$value.",".$approval_type.",".$i.")'></td>"; 
                                        ?>
                                        <td align="center">
                                        	<Input name="txt_appv_cause[]" class="text_boxes" readonly placeholder="Please Browse" ID="txt_appv_cause_<? echo $i;?>" style="width:97px" maxlength="50" title="Maximum 50 Character" onClick="openmypage_app_cause(<? echo $value; ?>,<? echo $approval_type; ?>,<? echo $i;?>)">&nbsp;</td>
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
                        ?>
                    </tbody>
                </table>
            </div>
            <table align="left" cellspacing="0" cellpadding="0" border="0" rules="all" width="1550" class="rpt_table">
				<tfoot>
                    <td width="35" align="center" >
                    	<input type="checkbox" id="all_check" onClick="check_all('all_check')" />
                        <input type="hidden" name="hide_approval_type" id="hide_approval_type" value="<? echo $approval_type; ?>">
                        <font style="display:none"><? echo $all_approval_id; ?></font>
                    </td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/>

					<input type="button" value="Deny" class="formbutton" style="width:100px;<?= ($approval_type==1)?' display:none':''; ?>" onClick="submit_approved(<?=$i; ?>,5);"/>
				
				</td>
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
	$msg=''; $flag=''; $response='';

	$txt_alter_user_id = str_replace("'","",$txt_alter_user_id);
	$booking_ids = str_replace("'","",$booking_ids);
    $appv_causes = str_replace("'","",$appv_causes);
	$appv_causes_arr = explode('**',$appv_causes);
    $user_id_approval = ($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;
	$target_app_id_arr = explode(',',$booking_ids);	

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );


	$sql = "SELECT a.ID, a.READY_TO_APPROVED,a.IS_APPROVED,a.COMPANY_ID, a.SUPPLIER_ID,a.APPROVED_GROUP_BY,a.APPROVED_SEQU_BY,a.BUYER_ID from wo_booking_mst a where  a.COMPANY_ID=$cbo_company_name and a.STATUS_ACTIVE=1 and a.IS_DELETED=0  and a.IS_APPROVED<>1 and a.booking_type=6 and a.item_category in(25) and a.is_short in(2,3) and a.READY_TO_APPROVED=1 and a.id in($booking_ids) ";

	//echo $sql;die();
	$sqlResult=sql_select( $sql );
	foreach ($sqlResult as $row)
	{
		if($row['READY_TO_APPROVED'] != 1){echo '25**Please select ready to approved yes for approved this booking';exit();}
		$matchDataArr[$row['ID']]=array('buyer_id'=>$row['BUYER_ID'],'brand_id'=>0,'supplier_id'=>0,'fb_source'=>0);
		$last_app_status_arr[$row['ID']] = $row['IS_APPROVED'];
	}
	
	$finalDataArr=getFinalUser(array('company_id'=>$cbo_company_name,'ENTRY_FORM'=>32,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0,'product_dept_arr'=>0,'match_data'=>$matchDataArr));
 
	$sequ_no_arr_by_sys_id = $finalDataArr['final_seq'];

	$user_sequence_no = $finalDataArr['user_seq'][$user_id_approval];
   // print_r($user_sequence_no);die;
	$user_group_no = $finalDataArr['user_group'][$user_id_approval];
	$max_group_no = max($finalDataArr['user_group']);

	$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=32 group by mst_id","mst_id","approved_no");
 
	//print_r($target_app_id_arr);die;
	
 	if($approval_type==0)
	{ 

		$id=return_next_id( "id","approval_mst", 1 ) ;
		$ahid=return_next_id( "id","approval_history", 1 ) ;	
		

        foreach($target_app_id_arr as $key => $mst_id)
        {
		 
			$approved = ((max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no) || ( (max($finalDataArr['group_bypass_no_data_arr'][$max_group_no][2])==$user_sequence_no) && ($max_group_no == $user_group_no))  || ( (max($finalDataArr['group_bypass_no_data_arr'][$max_group_no][2]) == '') && ($max_group_no == $user_group_no) ) )?1:3;

			//print_r($finalDataArr['last_bypass_no_data_arr']);die;

			$approved_no = $max_approved_no_arr[$mst_id]*1;
			if($last_app_status_arr[$mst_id] == 0 || $last_app_status_arr[$mst_id] == 2){
				$approved_no = $max_approved_no_arr[$mst_id]+1;
				$approved_no_array[$mst_id] = $approved_no;
			}

			if($data_array!=''){$data_array.=",";}
			$data_array.="(".$id.",32,'".$mst_id."','".$user_sequence_no."','".$user_group_no."',".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
			$id=$id+1;
			
		
			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$ahid.",32,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$appv_causes_arr[$key]."','".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,".$approved.")";
			$ahid++;
			
			//mst data.......................
			$data_array_up[$mst_id] = explode(",",("".$approved.",".$user_sequence_no.",".$user_group_no.",'".$pc_date_time."',".$user_id_approval."")); 
        }
	 
 
        $flag=1;
		if($flag==1) 
		{  
			$field_array="id, entry_form, mst_id,  sequence_no,group_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
			$rID1=sql_insert("approval_mst",$field_array,$data_array,0);
			if($rID1) $flag=1; else $flag=0; 
		}
		   
		if($flag==1) 
		{
			$field_array_up="IS_APPROVED*APPROVED_SEQU_BY*APPROVED_GROUP_BY*APPROVED_DATE*APPROVED_BY"; 
			$usql = bulk_update_sql_statement( "wo_booking_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr );
			//echo $usql;oci_rollback($con);die;
			$rID2=execute_query($usql);
			if($rID2) $flag=1; else $flag=0; 
		}

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=32 and mst_id in ($booking_ids)";
			$rID3=execute_query($query,1);
			if($rID3) $flag=1; else $flag=0;
		}
		 
		if($flag==1)
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by,COMMENTS, approved_date, inserted_by, insert_date,IS_SIGNING,APPROVED";
			$rID4=sql_insert("approval_history",$field_array,$history_data_array,0);
			if($rID4) $flag=1; else $flag=0;
		}


		if(count($approved_no_array)>0 && $flag)
		{
			$approved_string="";

			foreach($approved_no_array as $key=>$value)
			{
				$approved_string.=" WHEN TO_NUMBER($key) THEN '".$value."'";
			}

			$approved_string_mst="CASE id ".$approved_string." END";
		    $approved_string_dtls="CASE mst_id ".$approved_string." END";

			$sql_insert="insert into wo_booking_mst_hstry(id, approved_no, booking_id, booking_type, is_short, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, source, booking_date, delivery_date, booking_month, booking_year, supplier_id, attention, booking_percent, colar_excess_percent, cuff_excess_percent, is_approved, is_deleted, status_active, inserted_by, insert_date, updated_by, update_date, ready_to_approved, is_apply_last_update, rmg_process_breakdown) 
            select	
            '', $approved_string_mst, id, booking_type, is_short, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, source, booking_date, delivery_date, booking_month, booking_year, supplier_id, attention, booking_percent, colar_excess_percent, cuff_excess_percent, is_approved, is_deleted, status_active, inserted_by, insert_date, updated_by, update_date, ready_to_approved, is_apply_last_update, rmg_process_breakdown from wo_booking_mst where booking_no in ($booking_ids)";

           //echo $sql_insert;die;

			//-----------------------------------------Booking dtls-------------------------------------	
            $sql_insert_dtls="insert into wo_booking_dtls_hstry(id, approved_no, booking_dtls_id, job_no, po_break_down_id, pre_cost_fabric_cost_dtls_id, color_size_table_id, booking_no, booking_type, is_short, fabric_color_id, item_size, fin_fab_qnty, grey_fab_qnty, rate, amount, color_type, construction, copmposition, gsm_weight, dia_width, process_loss_percent, trim_group, description, brand_supplier, uom, process, sensitivity, wo_qnty, delivery_date, cons_break_down, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, rmg_qty, gmt_item, responsible_dept, responsible_person, reason, gmts_size, gmts_color_id) 
            select	
            '', $approved_string_dtls, id, job_no, po_break_down_id, pre_cost_fabric_cost_dtls_id, color_size_table_id, booking_no, booking_type, is_short, fabric_color_id, item_size, fin_fab_qnty, grey_fab_qnty, rate, amount, color_type, construction, copmposition, gsm_weight, dia_width, process_loss_percent, trim_group, description, brand_supplier, uom, process, sensitivity, wo_qnty, delivery_date, cons_break_down, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, rmg_qty, gmt_item, responsible_dept, responsible_person, reason, gmts_size, gmts_color_id from wo_booking_dtls where booking_no in ($booking_ids)";


				// $rID5=execute_query($sql_insert,0);
				// if($flag==1)
				// {
				// 	if($rID5) $flag=1; else $flag=0;
				// }
	
				// $rID6=execute_query($sql_insert_dtls,1);
				// if($flag==1)
				// {
				// 	if($rID6) $flag=1; else $flag=0;
				// }
			
	
		}


		
		 //echo "21**".$rID1.",".$rID2.",".$rID3.",".$rID4.",".$rID5.",".$rID6;oci_rollback($con);die;
		
		if($flag==1) $msg='19'; else $msg='21';

        
	}
	else if($approval_type==5)
	{              
		
		$ahid=return_next_id( "id","approval_history", 1 ) ;	
		$target_app_id_arr = explode(',',$booking_ids);	
        foreach($target_app_id_arr as $key => $mst_id)
        {
			$approved_no = $max_approved_no_arr[$mst_id]*1;
			if($history_data_array!="") $history_data_array.=",";

			$history_data_array.="(".$ahid.",32,".$mst_id.",'".$approved_no."','".$user_sequence_no."',0,".$user_id_approval.",'".$appv_causes_arr[$key]."','".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,2)";
			$ahid++;
        }

		

		$rID1=sql_multirow_update("wo_booking_mst","IS_APPROVED*READY_TO_APPROVED*APPROVED_SEQU_BY*APPROVED_GROUP_BY",'2*0*0*0',"id",$booking_ids,0);
		if($rID1) $flag=1; else $flag=0;

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=32 and mst_id in ($booking_ids)";
			$rID2=execute_query($query,1);
			if($rID2) $flag=1; else $flag=0;
		}
		
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=32 and mst_id in ($booking_ids)";
			$rID3=execute_query($query,1); 
			if($rID3) $flag=1; else $flag=0; 
		}
		
		if($flag==1)
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, COMMENTS,approved_date, inserted_by, insert_date,IS_SIGNING,APPROVED";
			$rID4=sql_insert("approval_history",$field_array,$history_data_array,0);
			if($rID4) $flag=1; else $flag=0;
		}

		//echo "5**".$rID1.",".$rID2.",".$rID3.",".$rID4.",".$rID5;oci_rollback($con);die;
		
		$response=$booking_ids;
		if($flag==1) $msg='50'; else $msg='0';
		
	}
	else
	{              
		
		$ahid=return_next_id( "id","approval_history", 1 ) ;	
        foreach($target_app_id_arr as $key => $mst_id)
        {
			$approved_no = $max_approved_no_arr[$mst_id]*1;
			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$ahid.",32,".$mst_id.",".$approved_no.",'".$user_sequence_no."',0,".$user_id_approval.",'".$appv_causes_arr[$key]."','".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$ahid++;
        }

		//echo $booking_ids;die;

		$rID1=sql_multirow_update("wo_booking_mst","IS_APPROVED*READY_TO_APPROVED*APPROVED_SEQU_BY*APPROVED_GROUP_BY",'0*0*0*0',"id",$booking_ids,0);
		if($rID1) $flag=1; else $flag=0;

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=32 and mst_id in ($booking_ids)";
			$rID2=execute_query($query,1);
			if($rID2) $flag=1; else $flag=0;
		}
		
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=32 and mst_id in ($booking_ids)";
			$rID3=execute_query($query,1); 
			if($rID3) $flag=1; else $flag=0; 
		}
		
		if($flag==1)
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by,COMMENTS, approved_date, inserted_by, insert_date,IS_SIGNING,APPROVED";
			$rID4=sql_insert("approval_history",$field_array,$history_data_array,0);
			if($rID4) $flag=1; else $flag=0;
		}


		 // echo "22**".$rID1.",".$rID2.",".$rID3.",".$rID4.$query;oci_rollback($con);die;
		
		$response=$booking_ids;
		if($flag==1) $msg='20'; else $msg='22';
		
	}
	

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
		$sql_cause="select MAX(id) as id from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=32 and user_id='$user_id' and booking_id='$wo_id' and approval_type='$app_type' and status_active=1 and is_deleted=0";				
		$nameArray_cause=sql_select($sql_cause);
		foreach($nameArray_cause as $row)
		{
			$app_cause=return_field_value("approval_cause", "fabric_booking_approval_cause", "id='".$row[csf("id")]."' and status_active=1 and is_deleted=0");
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
				http.open("POST","embellishment_work_order_approval_group_by_controller.php",true);
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
		
		function generate_worder_mail(woid,mail,appvtype,user)
		{
			var data="action=app_cause_mail&woid="+woid+'&mail='+mail+'&appvtype='+appvtype+'&user='+user;
			//alert (data);return;
			freeze_window(6);
			http.open("POST","embellishment_work_order_approval_group_by_controller.php",true);
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
	$sql_req="select approval_cause,approval_no from fabric_booking_approval_cause where entry_form=32 and booking_id='$wo_id' and approval_type=2 and status_active=1 and is_deleted=0 order by approval_no ";
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
			$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation,b.SEQUENCE_NO,b.GROUP_NO from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name  and valid=1 and a.id!=$user_id  and a.is_deleted=0 and b.is_deleted=0 and b.entry_form=32 order by b.sequence_no";
		 	//echo $sql;die;
		 	$arr=array (2=>$custom_designation,3=>$Department);
		 	echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department,Seq. no,Group no", "100,120,150,180,50,50","730","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id,sequence_no,group_no", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,7,7' ) ;
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
			
			$approved_no_history=return_field_value("approved_no","approval_history","entry_form=32 and mst_id=$wo_id and approved_by=$user_id");
			$approved_no_cause=return_field_value("approval_no","fabric_booking_approval_cause","entry_form=32 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
			
			//echo "shajjad_".$approved_no_history.'_'.$approved_no_cause; die;
			
			if($approved_no_history=="" && $approved_no_cause=="")
			{
				//echo "insert"; die;
				
				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;
			
				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",32,".$user_id.",".$wo_id." ,".$appv_type.",0,".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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
				
				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=32 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
				
				$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
				$data_array="".$page_id."*32*".$user_id."*".$wo_id."*".$appv_type."*0*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
				
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
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=32 and mst_id=$wo_id and approved_by=$user_id");
				$max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=32 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
				
				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;
			
					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$page_id.",32,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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
					
					$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=32 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
					
					$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
					$data_array="".$page_id."*32*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
					
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
		
			$unapproved_cause_id=return_field_value("id","fabric_booking_approval_cause","entry_form=32 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");
			
			$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=32 and mst_id=$wo_id and approved_by=$user_id");
			
			if($unapproved_cause_id=="")
			{
			
				//echo "shajjad_".$unapproved_cause_id; die;
		
				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;
			
				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_history_id,approval_cause,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",32,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$approval_id.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				
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
				
				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=32 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");
				
				$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_history_id*approval_cause*updated_by*update_date*status_active*is_deleted";
				$data_array="".$page_id."*32*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$approval_id."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
				
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

if($action=="pre_cost_data")
{
	
	$jobData=sql_select("select a.job_no, a.company_name, a.buyer_name, a.style_ref_no,b.costing_date,b.costing_per from wo_po_details_master a,wo_pre_cost_mst b where a.job_no=b.job_no and b.job_no='".trim($data)."'");
	foreach($jobData as $row)
	{
		$last_job_data=implode('***',$row);;
	}
	echo $last_job_data;
	exit();	
}

// For Comments
if($action=="show_fabric_comment_report")
{
	echo load_html_head_contents("Approval Cause Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	//$last_update=return_field_value("is_apply_last_update","wo_booking_mst","booking_no='".trim($data)."'");
	//echo $last_update;
	?>
	<body>
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
		//print_r( $paln_cut_qnty_array);
		//echo $job_no;
		
		$item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no in('".str_replace(',',"','",$job_no)."')", "gmts_item_id", "set_item_ratio");
		//$item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no ='$job_no'", "gmts_item_id", "set_item_ratio");
		//	echo "select quotation_id from wo_po_details_master where job_no='".$job_no."' ";
		$quotation_id = return_field_value(" quotation_id as quotation_id"," wo_po_details_master ","job_no in('".str_replace(',',"','",$job_no)."')","quotation_id");  
		$tot_mkt_cost  = return_field_value(" sum(b.fab_knit_req_kg) as mkt_cost","wo_price_quotation a,wo_pri_quo_sum_dtls b"," a.id=b.quotation_id and a.id='".$quotation_id."'","mkt_cost");
		//	print_r( $item_ratio_array);
		$nameArray=sql_select("
		select
		a.id,
		a.item_number_id,
		a.costing_per,
		a.job_no,
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
		b.po_break_down_id in (".str_replace("'","",$order_id).")   and a.status_active=1 and a.is_deleted=0
		order by id");
		$count=0;
		//$cbo_fabric_natu $cbo_fabric_source_cond
		$tot_grey_req_as_pre_cost_arr=array();$tot_grey_req_as_price_cost_arr=array();$tot_grey_req_as_price_cost=0;
		foreach ($nameArray as $result)
		{
			//echo "select quotation_id as quotation_id from wo_po_details_master where job_no='".$result[csf('job_no')]."'";
			// $quotation_id = return_field_value(" quotation_id as quotation_id"," wo_po_details_master ","job_no='".$result[csf('job_no')]."'","quotation_id");  
			if (count($nameArray)>0 )
			{
	            if($result[csf("costing_per")]==1)
				{
					$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(12*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
					$dzn_qnty_p=12;
					//$tot_mkt_price=$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(12*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
				}
				if($result[csf("costing_per")]==2)
				{
					$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(1*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
					$dzn_qnty_p=1;
				}
				if($result[csf("costing_per")]==3)
				{
					$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(24*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
					$dzn_qnty_p=12*2;
				}
				if($result[csf("costing_per")]==4)
				{
					$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(36*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
					$dzn_qnty_p=12*3;
				}
				if($result[csf("costing_per")]==5)
				{
					$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(48*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
					$dzn_qnty_p=12*4;
				}
				$dzn_qnty_p=$dzn_qnty_p*$item_ratio_array[$result[csf("item_number_id")]];
				
				  $tot_grey_req_as_price_cost+=($tot_mkt_cost/$dzn_qnty_p)*$paln_cut_qnty_array[$result[csf("color_size_table_id")]];
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

						$booking_qnty_main=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b, wo_booking_mst c where a.job_no =b.job_no_mst and    a.booking_no =c.booking_no  and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$order_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and a.is_short=2 and c.item_category=2 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty"); //a.job_no =c.job_no and
						
						$booking_qnty_short=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$order_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and c.item_category=2 and a.is_short=1 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
			
			
						$booking_qnty_sample=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c  where a.job_no =b.job_no_mst and  a.job_no=c.job_no and  a.booking_no=c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$order_id).") and a.booking_type =4 and c.fabric_source=$cbo_fabric_source and c.item_category=2  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
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
		?>
	<tr bgcolor="<? echo $bgcolor; ?>">
	    <td width="30"> <? echo $k; ?> </td>
	    <td width="120"><p><? echo $row[csf("po_number")]; ?></p> </td>
	    <td width="70" align="right"><? echo change_date_format($row[csf("pub_shipment_date")],"dd-mm-yyyy",'-'); ?> </td>
	    <td width="80" align="right"><? $total_price_mkt_cost+=$tot_grey_req_as_price_cost;echo number_format($tot_grey_req_as_price_cost,2);?> </td>
	    <td width="70" align="right"><?  echo number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]],2); $total_pre_cost+=$tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]];?> </td>
	    <td width="70" align="right"><? echo number_format($booking_qnty_main[$row[csf("id")]],2); $total_booking_qnty_main+=$booking_qnty_main[$row[csf("id")]];?> </td>
	    <td width="70" align="right"> <? echo number_format($booking_qnty_short[$row[csf("id")]],2); $total_booking_qnty_short+=$booking_qnty_short[$row[csf("id")]];?></td>
	    <td width="70" align="right"><? echo number_format($booking_qnty_sample[$row[csf("id")]],2); $total_booking_qnty_sample+=$booking_qnty_sample[$row[csf("id")]];?></td>
	    <td width="70" align="right">	<? $tot_bok_qty=$booking_qnty_main[$row[csf("id")]]+$booking_qnty_short[$row[csf("id")]]+$booking_qnty_sample[$row[csf("id")]]; echo number_format($tot_bok_qty,2); $total_tot_bok_qty+=$tot_bok_qty;?> </td>
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


?>