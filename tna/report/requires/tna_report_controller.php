<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
extract($_REQUEST);
$user_id=$_SESSION['logic_erp']['user_id'];
$menu_id = $_SESSION['menu_id'];
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$tna_process_start_date="2014-12-01";
if($db_type==0) $blank_date="0000-00-00"; else $blank_date=""; 

function load_class($back_path,$classArr=array()){
	include($back_path.'includes/class4/class.conditions.php');
	include($back_path.'includes/class4/class.reports.php');
	foreach($classArr as $class_name){
		include($back_path."includes/class4/class.".$class_name.".php");
	}
}


if($action=="load_drop_down_buyer")
{
	if($_SESSION['logic_erp']['buyer_id'] !=''){$buyer_con = " and buy.id in(".$_SESSION['logic_erp']['buyer_id'].")";}
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_con group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
	die; 	 
}
if($action=="set_print_button")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=3 and report_id=23 and is_deleted=0 and status_active=1");
	echo "print_report_button_setting('$print_report_format');\n"; 
	die;
}

$lead_time=return_library_array("select task_template_id,lead_time from tna_task_template_details where task_type=1 and is_deleted=0 and status_active=1 group by task_template_id,lead_time","task_template_id","lead_time");
$buyer_short_name_arr = return_library_array("SELECT short_name,id FROM  lib_buyer WHERE is_deleted = 0 and status_active=1 order by id asc","id","short_name");


$cbo_company_name = str_replace("'","",$cbo_company_name);
$tna_process_type=return_field_value("tna_process_type"," variable_order_tracking","variable_list=31 and company_name='".$cbo_company_name."'"); 
$tna_process_based_on=return_field_value("TEXTILE_TNA_PROCESS_BASE"," variable_order_tracking","variable_list=31 and company_name='".$cbo_company_name."'"); 
// print_r($textile_tna_process_base);die;

if($action=="generate_tna_report")
{
	// cbo_shipment_status
	$user_app_prev=return_field_value("APPROVE_PRIV","USER_PRIV_MST","user_id=$user_id and MAIN_MENU_ID=$menu_id");
	if($_SESSION['logic_erp']['buyer_id'] !=''){$buyer_con = " and a.buyer_name in(".$_SESSION['logic_erp']['buyer_id'].")";}

	if($graph==1){
		if($db_type==0)
		{
			$txt_date_from = date("'Y-m-d'",strtotime($txt_date_from));
			$txt_date_to = date("'Y-m-d'",strtotime($txt_date_to));
		}
		else
		{
			$txt_date_from = date("'d-M-Y'",strtotime($txt_date_from));
			$txt_date_to = date("'d-M-Y'",strtotime($txt_date_to));
		}
	}

	$actual_manual_update_task_arr=return_library_array("select task_id,task_id from tna_manual_permission where is_actual_manual=1  and company_id=$cbo_company_name","task_id","task_id");
	$plan_manual_update_task_arr=return_library_array("select task_id,task_id from tna_manual_permission where is_plan_manual=1  and company_id=$cbo_company_name","task_id","task_id");

	$plan_manual_update_task_arr[0]=0;
	$actual_manual_update_task_arr[0]=0;
	$plan_actual_menual_task_arr = $actual_manual_update_task_arr + $plan_manual_update_task_arr;


	$cbo_task_group=str_replace("'","",$cbo_task_group);	
	if($db_type==0){$task_group_con=" and a.task_group!=''";}else{$task_group_con=" and a.task_group is not null";}
	if($cbo_task_group){$task_group_con.=" and a.task_group like('%$cbo_task_group%')";}

	$actual_manual_update_task_arr=return_library_array("select task_id,task_id from tna_manual_permission where is_actual_manual=1  and company_id=$cbo_company_name","task_id","task_id");
	$plan_manual_update_task_arr=return_library_array("select task_id,task_id from tna_manual_permission where is_plan_manual=1  and company_id=$cbo_company_name","task_id","task_id");

	$tna_approval_necessity_setup_arr=return_library_array("select b.PAGE_ID,b.APPROVAL_NEED  from APPROVAL_SETUP_MST a, APPROVAL_SETUP_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and b.PAGE_ID=32 order by a.SETUP_DATE","PAGE_ID","APPROVAL_NEED");
	
	if($_SESSION['logic_erp']['tna_task_id']){$whereCon="and a.task_name in(".$_SESSION['logic_erp']['tna_task_id'].")";}

	if( $tna_process_type==2 ){//Parcent base;
		$modSql= "select a.id,a.task_catagory,a.task_name,a.task_short_name,a.task_type,a.completion_percent,a.task_sequence_no,a.task_group
		from lib_tna_task a where a.is_deleted = 0 and a.status_active=1  AND a.task_type = 1 $task_group_con $whereCon order by a.task_sequence_no asc";
	}
	else
	{
		$modSql="select a.id,a.task_catagory,a.task_name,a.task_short_name,a.task_type,a.completion_percent,a.task_sequence_no,a.task_group,b.task_template_id,b.lead_time 
		from lib_tna_task a,tna_task_template_details b where a.task_name=b.tna_task_id  AND a.task_type = 1 and b.task_type=1 $whereCon $task_group_con and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 order by a.task_sequence_no asc";
	}
	
	//echo $modSql;die;
	
	$mod_sql= sql_select($modSql);
	$tna_task_array=array();
	$tna_task_id=array();
	$tna_task_cat=array();
	$tna_task_name_arr=array();
	foreach ($mod_sql as $row)
	{	
		$tna_task_group_by_id[$row[csf("task_name")]] = $row[csf("task_group")];
		$tna_task_id[$row[csf("task_name")]] = $row[csf("task_name")];
		$tna_task_array[$row[csf("task_name")]] = $row[csf("task_short_name")];
		$tna_task_name_array[$row[csf("id")]] = $tna_task_name[$row[csf("task_name")]];
		$tna_task_cat[$row[csf("id")]] = $row[csf("task_catagory")];
		$tna_task_name_arr[$row[csf("id")]] = $row[csf("task_name")];
		$lead_time_array[$row[csf("task_template_id")]] = $row[csf("lead_time")];
		$tast_tmp_id_arr[$row[csf("task_template_id")]][$row[csf("tna_task_id")]] = $row[csf("tna_task_id")];
	}
	
	
	
	$cbo_company_id=$cbo_company_name;
	
	$order_status_cond="";
	if(str_replace("'","",$cbo_order_status)>0) $order_status_cond=" and b.is_confirmed=$cbo_order_status";
	
	if(str_replace("'","",$cbo_company_name)==0) $cbo_company_name=""; else $cbo_company_name=" and a.company_name = $cbo_company_name";
	if(str_replace("'","",$cbo_buyer_name)==0) $cbo_buyer_name=""; else $cbo_buyer_name=" and a.buyer_name = $cbo_buyer_name";
	if(str_replace("'","",$cbo_team_member)==0) $cbo_team_member=""; else $cbo_team_member=" and a.dealing_marchant = $cbo_team_member";
	
	if(str_replace("'","",$cbo_search_type)==1){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
	}
	else if(str_replace("'","",$cbo_search_type)==3){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and c.country_ship_date between $txt_date_from and $txt_date_to";
	}
	else if(str_replace("'","",$cbo_search_type)==4){
		if($db_type==0)
		{
			if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)==""){$date_range="";}else{ 
			$date_range=" and b.insert_date between ".$txt_date_from." and ".$txt_date_to."";}
		}
		else
		{
			
			if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)==""){$date_range="";}else{ 
			$date_range=" and b.insert_date between ".$txt_date_from." and '".str_replace("'","",$txt_date_to)." 11:59:59 PM'";}
		}
	}
	else if(str_replace("'","",$cbo_search_type)==5){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.shipment_date between $txt_date_from and $txt_date_to";
	}
	else
	{
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.po_received_date between $txt_date_from and $txt_date_to";
	}
	
	

	
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if($txt_job_no=="") $txt_job_no=""; else $txt_job_no=" and a.job_no like('%$txt_job_no')";
	$txt_order_no=str_replace("'","",$txt_order_no);
	if($txt_order_no=="") $txt_order_no=""; else $txt_order_no=" and b.po_number ='$txt_order_no'";
	$txt_file_no=str_replace("'","",$txt_file_no);
	if($txt_file_no=="") $file_cond=""; else $file_cond=" and b.file_no ='$txt_file_no'";
	$txt_int_ref_no=str_replace("'","",$txt_int_ref_no);
	if($txt_int_ref_no=="") $ref_cond=""; else $ref_cond=" and b.grouping ='$txt_int_ref_no'";
	
	$txt_style_ref_no=str_replace("'","",$txt_style_ref_no);
	if($txt_style_ref_no=="") $txt_style_ref_no=""; else $txt_style_ref_no=" and a.style_ref_no ='$txt_style_ref_no'";
	//**txt_date_from*txt_date_to*txt_job_no
	
	if(str_replace("'","",$cbo_shipment_status)==4){$shipment_status_con=" and b.shiping_status=3";}
	else if(str_replace("'","",$cbo_shipment_status)==1){$shipment_status_con=" and b.shiping_status !=3";}
	
	
	
	
	
	$tna_all_task=implode(",",$tna_task_id);
	
	if(str_replace("'","",$cbo_search_type)==3)
	{
		$sql = "SELECT a.team_leader,a.factory_marchant,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, b.id ,b.po_number, b.file_no, b.grouping as in_ref_no,(a.TOTAL_SET_QNTY*b.PO_QUANTITY) as po_qty_pcs,b.PO_QUANTITY
		FROM  wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c 
		WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no  and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 $order_status_cond $file_cond $ref_cond $buyer_con";
	}
	else
	{
		$sql = "SELECT a.team_leader,a.factory_marchant,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, b.id, b.po_number, b.file_no, b.grouping as in_ref_no ,(a.TOTAL_SET_QNTY*b.PO_QUANTITY) as po_qty_pcs,b.PO_QUANTITY
		FROM  wo_po_details_master a,  wo_po_break_down b 
		WHERE a.job_no=b.job_no_mst $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no  and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 $order_status_cond  $file_cond $ref_cond $buyer_con"; 
	}
	
   //echo $sql; 
	$result = sql_select( $sql ) ;
	$wo_po_details_master = array();
	$po_no_arr=array();
	$job_no_arr=array();
	foreach( $result as  $row ) 
	{	
		$wo_po_details_master[$row[csf('id')]]['company_name']=$row[csf('company_name')];
		$wo_po_details_master[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$wo_po_details_master[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$wo_po_details_master[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
		$wo_po_details_master[$row[csf('id')]]['po_number']= $row[csf('po_number')];
		$wo_po_details_master[$row[csf('id')]]['set_smv']= $row[csf('set_smv')];
		$wo_po_details_master[$row[csf('id')]]['file_no']= $row[csf('file_no')];
		$wo_po_details_master[$row[csf('id')]]['in_ref_no']= $row[csf('in_ref_no')];
		
		$wo_po_details_master[$row[csf('id')]]['po_qty']= $row[csf('PO_QUANTITY')];
		$wo_po_details_master[$row[csf('id')]]['po_qty_pcs']= $row[csf('po_qty_pcs')];
		$po_no_arr[]=$row[csf('id')];
		$job_no_arr[$row[csf('job_no')]]=$row[csf('job_no')];
		
		//$wo_po_details_master[$row[csf('id')]]['dealing_marchant']=$row[csf('dealing_marchant')];
		//$wo_po_details_master[$row[csf('id')]]['factory_marchant']=$row[csf('factory_marchant')];
		//$wo_po_details_master[$row[csf('id')]]['team_leader']=$row[csf('team_leader')];


		$wo_po_details_master[$row[csf('job_no')]]['dealing_marchant']=$row[csf('dealing_marchant')];
		$wo_po_details_master[$row[csf('job_no')]]['factory_marchant']=$row[csf('factory_marchant')];
		$wo_po_details_master[$row[csf('job_no')]]['team_leader']=$row[csf('team_leader')];
		
		
	}
	
 
	$result = sql_select("select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name" ) ;
	$team_leader_name = array();
	foreach( $result as  $row ) 
	{	
		$team_leader_name[$row[csf('id')]]=$row[csf('team_leader_name')];
	}

 
	$sql = "SELECT team_member_name,id FROM lib_mkt_team_member_info WHERE is_deleted = 0 and status_active=1 order by id asc";
	$result = sql_select( $sql ) ;
	$team_member_name = array();
	foreach( $result as  $row ) 
	{	
		$team_member_name[$row[csf('id')]]=$row[csf('team_member_name')];
	}
	
	$sql = "SELECT buyer_name,id FROM  lib_buyer WHERE is_deleted = 0 and status_active=1 order by id asc";
	$result = sql_select( $sql ) ;
	$buyer_name = array();
	foreach( $result as  $row ) 
	{	
		$buyer_name[$row[csf('id')]]=$row[csf('buyer_name')];
	}
	
	
	$po_no_arr_all=implode(",",$po_no_arr); if($po_no_arr_all!="") $po_no_arr_all .=",0"; else $po_no_arr_all .="0"; 
 	
	$c=count($tna_task_id);
		
	if($db_type==0)
	{
		$sql ="select a.APPROVED,a.READY_TO_APPROVED,a.po_number_id, a.job_no, a.shipment_date,max(b.pub_shipment_date) as pub_shipment_date,max(b.shipment_date) as po_shipment_date,max(b.pub_shipment_date_prev) as pub_shipment_date_prev, a.template_id, a.po_receive_date,b.insert_date,";
		$i=1;
	
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c) $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id, ";
			else $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id ";
			$i++;
		}
		
		$sql .=" from tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id and a.po_number_id in( $po_no_arr_all ) ".where_con_using_array($job_no_arr,1,'a.job_no')." $shipment_status_con and b.status_active=1  and b.po_quantity>0 $order_status_cond and a.task_type=1 group by a.APPROVED,a.READY_TO_APPROVED,a.po_number_id,a.job_no,b.insert_date order by a.shipment_date,a.po_number_id,a.job_no"; 
	}
	else
	{
		$sql ="select a.APPROVED,a.READY_TO_APPROVED,a.po_number_id, a.job_no, max(b.shipment_date) as shipment_date,max(b.pub_shipment_date) as pub_shipment_date,max(b.shipment_date) as po_shipment_date,max(b.pub_shipment_date_prev) as pub_shipment_date_prev,a.template_id, max(a.po_receive_date) as po_receive_date,b.insert_date,";
		$i=1;
		
		foreach( $tna_task_id as $dval=>$id)    	
		{
			
			if ($i!=$c) $sql .="max(CASE WHEN a.task_number = '".$id."' THEN a.actual_start_date || '_' || a.actual_finish_date || '_' || a.task_start_date || '_' || a.task_finish_date ||'_' || a.notice_date_start || '_' || a.notice_date_end || '_' || a.remarks || '_' || a.id || '_' || a.task_number || '_' || a.plan_start_flag || '_' || a.plan_finish_flag  || '_' || a.ACTUAL_START_FLAG || '_' || a.ACTUAL_FINISH_FLAG  END ) as status$id, ";
			
			else $sql .="max(CASE WHEN a.task_number = '".$id."' THEN a.actual_start_date || '_' || a.actual_finish_date || '_' || a.task_start_date || '_' || a.task_finish_date || '_' || a.notice_date_start || '_' || a.notice_date_end || '_' || a.remarks || '_' || a.id || '_' || a.task_number || '_' || a.plan_start_flag || '_' || a.plan_finish_flag  || '_' || a.ACTUAL_START_FLAG || '_' || a.ACTUAL_FINISH_FLAG END ) as status$id ";
			$i++;
		}
			
		$po_no_arr_all=array_unique(explode(',',$po_no_arr_all));
		$sql_order_con=where_con_using_array($po_no_arr_all,0,'a.po_number_id');
		
		$sql_job_con=where_con_using_array($job_no_arr,1,'a.job_no'); 
			
			 
			
			
		//-------------------------------
		$sql .=" from  tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id $sql_order_con $sql_job_con $shipment_status_con and b.status_active=1 and b.po_quantity>0 $order_status_cond  and a.task_type=1  group by a.APPROVED,a.READY_TO_APPROVED,a.po_number_id,a.job_no,a.template_id,b.shipment_date,b.insert_date order by b.shipment_date,a.po_number_id,a.job_no"; 
	}
	
	// echo $sql;die;
	$data_sql= sql_select($sql);
	
	
	$poArr=array();$templateArr=array();
	foreach ($data_sql as $row)
	{
		$poArr[$row[csf('po_number_id')]]=$row[csf('po_number_id')];
		$templateArr[$row[csf('template_id')]]=$row[csf('template_id')];
	}
	 
	
	//selected task id start--------------------------------------------
	
	
	$tna_process_task_sql = "SELECT a.TNA_TASK_ID as TASK_NUMBER FROM TNA_TASK_TEMPLATE_DETAILS a WHERE a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and a.task_type = 1 ".where_con_using_array($templateArr,0,'a.TASK_TEMPLATE_ID')." GROUP BY a.TNA_TASK_ID";
	//echo $tna_process_task_sql;//die;
	$tna_process_task_sql_result = sql_select($tna_process_task_sql);
	$tna_process_task_arr=array();
	foreach( $tna_process_task_sql_result as  $row ) 
	{	
		$tna_process_task_arr[$row['TASK_NUMBER']]=$tna_task_array[$row['TASK_NUMBER']];
		$tna_process_task_id_arr[$row['TASK_NUMBER']]=$row['TASK_NUMBER'];

	}	
	
	//for sequence................
	$tempTaskNameArr=array();$tempTaskIdArr=array();
	foreach($tna_task_array as $tid=>$tn){
		if($tna_process_task_arr[$tid]){
			$tempTaskNameArr[$tid]=$tna_process_task_arr[$tid];
			$tempTaskIdArr[$tid]=$tid;
			
		}
	}
	
	
	
	$tna_task_array=array();
	$tna_task_id=array();
	$tna_task_array=$tempTaskNameArr;
	$tna_task_id=$tempTaskIdArr;
	//............................end
	
	/*$tna_task_array=array();
	$tna_task_id=array();
	$tna_task_array=$tna_process_task_arr;
	$tna_task_id=$tna_process_task_id_arr;*/
	
	
	
	
	//print_r($tna_task_array);die;
	//--------------------------------------------selected task id end;
	
	$sql_con="select a.job_no,a.booking_no_prefix_num,b.po_break_down_id from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.is_short=2  and a.entry_form in(86,118) and a.booking_type=1 and a.IS_DELETED=0 and b.IS_DELETED=0  and a.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1";//a.entry_form=86
	
	$po_id_list_arr=array_chunk($poArr,999);
	$p=1;
	foreach($po_id_list_arr as $po_id)
	{
		if($p==1){$sql_con .=" and (b.po_break_down_id in(".implode(',',$po_id).")";} 
		else{$sql_con .=" or b.po_break_down_id in(".implode(',',$po_id).")";}
		$p++;
	}
	$sql_con .=") order by a.booking_no_prefix_num";
	
	$sql_booking_sql = sql_select($sql_con);
	foreach( $sql_booking_sql as  $row ) 
	{	
		$booking_no_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no_prefix_num')]]=$row[csf('booking_no_prefix_num')];
	}	
	
	
	$orderHisSql = "select PO_ID,SHIPMENT_DATE from WO_PO_UPDATE_LOG where 1 = 1 ".where_con_using_array($poArr,0,'PO_ID')." group by PO_ID,SHIPMENT_DATE";
	$orderHisSqlRes = sql_select($orderHisSql);
	$changeShipDateArr=array();
	foreach( $orderHisSqlRes as  $row ) 
	{
		$row['SHIPMENT_DATE']=change_date_format($row['SHIPMENT_DATE']);
		$changeShipDateArr[$row['PO_ID']][$row['SHIPMENT_DATE']]=1;
	}
	
	$width=(count($tna_task_id)*162)+1260;
	if($tna_approval_necessity_setup_arr[32]==1){$width=$width+180;}
	
	ob_start();
	
	?>
    
   <style>small{font-size:10px; color:#006;}</style>
   <div style="margin:0 0%;">
        <span style="background:#FF0000; padding:0 6px; border-radius:9px; cursor:pointer;" title="Red">&nbsp;</span>&nbsp; Target Date Over. &nbsp;&nbsp;
        <span style="background:#2A9FFF; padding:0 6px; border-radius:9px; cursor:pointer;" title="Blue">&nbsp;</span>&nbsp; Done In Late. &nbsp;&nbsp;
        <span style="background:#FFFF00; padding:0 6px; border-radius:9px; cursor:pointer;" title="Yellow">&nbsp;</span>&nbsp; Reminder.
        <span style="background:#0000FF; padding:0 6px; border-radius:9px; cursor:pointer;" title="Royal Blue">&nbsp;</span>&nbsp; Manual Update Plan.
    </div>    
    <div style="width:<? echo $width+20; ?>px" align="left">
    <table width="<? echo $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
    	<thead>
        	<tr>
            	<th width="40" rowspan="2">SL</th>
                <th width="120" rowspan="2" ># Team Leader &nbsp; &nbsp; &nbsp; &nbsp;<br> # Dealing Merchant<br> # Factory Merchant</th>
                <th width="70" rowspan="2">Buyer Name</th>
                <th width="110" rowspan="2">PO Number</th>
                <th width="90" rowspan="2">PO Qty.</th>
                <th width="90" rowspan="2">PO Qty(PCS)</th>
                <th width="70" rowspan="2">File No.</th>
                <th width="70" rowspan="2">Int. Ref. No</th>
                <th width="40" rowspan="2">SMV</th>
                <th rowspan="2">Style Ref.</th> 
                <th width="40" rowspan="2">Job No.</th>
                <th width="60" rowspan="2">Fab. Booking</th>
				<th width="100" rowspan="2">Shipment Date</th>
                <th width="80" rowspan="2">PO Insert Date</th>
                <?php if($tna_approval_necessity_setup_arr[32]==1){?>
                <th width="90" rowspan="2">Ready To Approve</th>
                <th width="90" rowspan="2">TNA Approve Status</th>
                <?php } ?>
                <th width="90" rowspan="2">Status</th>
                    <?
					$i=0;
					foreach($tna_task_array as $task_name=>$key)
					{
						$td_bgcolor = ($plan_actual_menual_task_arr[$task_name]) ? "orange" : "";
						$i++;
						if(count($tna_task_array)==$i) 
						echo '<th style="background:'.$td_bgcolor.';" width="160" colspan="2" title="'.$task_name.'='.$tna_task_name[$task_name].'">'. $key." <br><small>".$tna_task_group_by_id[$task_name]."</small> </th>"; 
					    else echo '<th style="background:'.$td_bgcolor.';" width="160" colspan="2" title="'.$task_name.'='.$tna_task_name[$task_name].'">'.$key." <br><small>".$tna_task_group_by_id[$task_name]."</small> </th>";
					}
					echo '</tr><tr>';

					// Delay/Early By
					
					$i=0;
					foreach($tna_task_array as $key)
					{
						$i++;
						if(count($tna_task_array)==$i){
						echo '<th width="80" title="Plan Start Date=Ship Date-(Deadline+Execution Days+1)">Start</th><th width="80" title="Plan Finish Date=(Ship Date-Deadline)"> Finish</th>';}else{
						echo '<th width="80" title="Plan Start Date=Ship Date-(Deadline+Execution Days+1)">Start</th><th width="80" title="Plan Finish Date=(Ship Date-Deadline)"> Finish</th>';}
					}
					echo '</tr>';
				    ?>
                </thead>
                </table>
         </div>
         
         <? //echo "saju1_".count($tna_task_array); die; ?>
         
        	<div style="overflow-y:scroll; max-height:360px; width:<? echo $width+20; ?>px;" align="left" id="scroll_body">
          	<table width="<? echo $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
       
    <?
	
	$tid=0;
	$i=1;
	$count=0;
	$kid=1;
	$new_job_no=array();
	$h=0;
	$tot_po_qty=0;
	foreach ($data_sql as $row)
	{
		 
		if (!in_array($row[csf('job_no')],$new_job_no))
		{
			//$new_approval_arr=array(); 
			$new_job_no[]=$row[csf('job_no')];
		}
		 if($row[csf('po_number_id')]==0)
		 {
			 foreach($tna_task_id as $vid=>$key)
			 {
				if ($row[csf('status').$key]!="") $new_approval_arr[$row[csf('job_no')]][$key]=$row[csf('status').$key];
			 }
		 }
		 else
		 {
			 $h++;
			 if ($h%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
							
		?>
        		<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle; cursor:pointer;" height="25" onClick="change_color('tr_<? echo $h;?>','<? echo $bgcolor;?>')" id="tr_<? echo $h; ?>">
                    <td width="40" rowspan="3" align="center"><? echo $kid++;?></td>
                    <td width="120" rowspan="3" title="<?
					echo " Team Leader: ".$team_leader_name[$wo_po_details_master[$row[csf('job_no')]]['team_leader']];
					echo "; "; 
					echo " Dealing Merchant: ".$team_member_name[$wo_po_details_master[$row[csf('job_no')]]['dealing_marchant']];
					echo "; "; 
					echo " Factory Merchant: ".$team_member_name[$wo_po_details_master[$row[csf('job_no')]]['factory_marchant']]; ?>">
					<? 
					echo "# ".$team_leader_name[$wo_po_details_master[$row[csf('job_no')]]['team_leader']];
					echo "<br># ".$team_member_name[$wo_po_details_master[$row[csf('job_no')]]['dealing_marchant']];
					echo "<br># ".$team_member_name[$wo_po_details_master[$row[csf('job_no')]]['factory_marchant']]; 
					?>
                    </td>
                    <td width="70" rowspan="3"><p><? echo $buyer_name[$wo_po_details_master[$row[csf('po_number_id')]]['buyer_name']]; ?></p></td>
                    <td width="110" rowspan="3" align="center">
						<? 
                           // echo $wo_po_details_master[$row[csf('job_no')]][$row[csf('po_number_id')]]; 
							echo "<a href='#report_details' style='color:#990000' onclick= \"progress_comment_popup('".$row[csf('job_no')]."','".$row[csf('po_number_id')]."','".$row[csf('template_id')]."','".$tna_process_type."');\"><p>".$wo_po_details_master[$row[csf('po_number_id')]]['po_number']."</p></a>";
						
                        ?>
						<hr style="border:1px dotted #DDD;">
						<?
							echo "<a href='#report_details' style='color:#990000' onclick= \"task_his_popup('".$row[csf('job_no')]."','".$row[csf('po_number_id')]."','".$row[csf('template_id')]."','".$tna_process_type."');\"><p>Task His</p></a>";
						?>
							<hr style="border:1px dotted #DDD ;">
						<?
							echo "<a href='#report_details' style='color:#990000' onclick= \"report_generate_popup('".$row[csf('job_no')]."','".$row[csf('po_number_id')]."','".$row[csf('template_id')]."','".$tna_process_type."');\"><p>Report</p></a>";
						
                        ?>
                    </td>
                    
                    <td width="90" rowspan="3" align="right">
						<?
							$po_qty=$wo_po_details_master[$row[csf('po_number_id')]]['po_qty'];
							echo number_format($po_qty);
							$tot_po_qty+=$po_qty;
						?>
                    </td>
                    <td width="90" rowspan="3" align="right">
						<?
							$po_qty_pcs=$wo_po_details_master[$row[csf('po_number_id')]]['po_qty_pcs'];
							echo number_format($po_qty_pcs);
							$total_po_qty_pcs+=$po_qty_pcs;
						?>
                    </td>
                    <td width="70" rowspan="3"><p><? echo $wo_po_details_master[$row[csf('po_number_id')]]['file_no']; ?></p></td>
                    <td width="70" rowspan="3"><p><? echo $wo_po_details_master[$row[csf('po_number_id')]]['in_ref_no']; ?></p></td>
                    <td width="40" rowspan="3" align="center"><? echo number_format($wo_po_details_master[$row[csf('po_number_id')]]['set_smv'],2); ?></td>
                    <td rowspan="3" title="<? echo $row[csf('job_no')]; ?>"><p><? echo $wo_po_details_master[$row[csf('po_number_id')]]['style_ref_no']; ?></p></td>
                    <td width="40" rowspan="3" align="center"><? echo $wo_po_details_master[$row[csf('po_number_id')]]['job_no_prefix_num']; ?></td>
                    <td rowspan="3" width="60" align="center"><? echo implode(',',$booking_no_arr[$row[csf('po_number_id')]]); ?></td>
                     
                     <? 
					if($tna_process_type==1)
					{
						$lead_timee="Template Lead Time: ".$lead_time_array[$row[csf('template_id')]];
					}
					else
					{
						$lead_timee="Lead Time: ".($row[csf('template_id')]+1);
					}
					$po_lead_time=datediff( "d", date("Y-m-d",strtotime(change_date_format($row[csf('po_receive_date')]))), date("Y-m-d",strtotime(change_date_format($row[csf('shipment_date')]))) );


					unset($changeShipDateArr[$row[csf('po_number_id')]][change_date_format($row[csf('shipment_date')])]);
					$totalShipDateChange = count($changeShipDateArr[$row[csf('po_number_id')]]);

					  
					if($tna_process_based_on==1 ) { 
						$process_based_on_date = $row[csf('pub_shipment_date')];
					}
					else{
						$process_based_on_date = $row[csf('shipment_date')];
					}
					?>

                    <td width="100" rowspan="3" title="<? echo " PO. Rec. Date: ".change_date_format($row[csf('po_receive_date')]); echo ",\n Shipment Date: ".$process_based_on_date; echo ",\n Prev. Pub Ship Date: ".$row[csf('pub_shipment_date_prev')];echo ",\n Insert Date: ".$row[csf('insert_date')].",\n Process Date: ".$process_based_on_date.", \n Shipment Date Total Change: ".$totalShipDateChange;?>"><div style="width:98px; word-break:break-all">
					<? echo "<a href='#report_details' style='color:#990000' onclick= \"order_update_log_popup('".$row[csf('job_no')]."','".$row[csf('po_number_id')]."');\">".change_date_format($process_based_on_date)."</a> SDC:".$totalShipDateChange."<br>"." ".$lead_timee."<br>"." PO Lead Time:".($po_lead_time-1);  ?></div>
                    </td>

 
                    
                    <td width="80" rowspan="3" align="center"><? echo date("d-m-Y",strtotime($row[csf('insert_date')])); ?></td>
                    
                    <?php if($tna_approval_necessity_setup_arr[32]==1){?>
                    <td width="90" rowspan="3" align="center">
					<? 
						if($user_app_prev == 1){
							echo create_drop_down( "cbo_ready_to_approve", 80, $yes_no,"", 1, "-- Select --",$row['READY_TO_APPROVED'], "setReadyToApp(".$row[csf('po_number_id')].",this.value,".$row['APPROVED'].")" );
						}
						else{
							echo "Not Permitted";
						}
						
					?>
                    </td>                   
                    <td width="90" rowspan="3" align="center" id="approval_status_<?= $row[csf('po_number_id')];?>">
					<? 
					
					if($row['APPROVED']==1){echo "<span style='color:#1F7044;font-weight:bold;'>Approved</span>";}
					else if($row['APPROVED']==3){echo "<span style='color:#FF813A;font-weight:bold;'>Partial Approved</span>";}
					else if($row['APPROVED']==2){echo "<span style='color:#f00;font-weight:bold;'>Deny</span>";}
					else{ echo "<span style='color:#f00;font-weight:bold;'>Un Approve</span>";} 
					?>
                    </td>                   
                    <?php } ?>
                    <td width="90">Plan</td>
                	<?
					 $tast_id_arr=array_unique(explode(',',$tast_tmp_id_arr[$row[csf('template_id')]]));
					 $i=0;
					 foreach($tna_task_id as $vid=>$key)
					 {
						$i++;
						//print_r($row[csf('status').$key].'||');
						
						if ( $new_approval_arr[$row[csf('job_no')]][$key]=="") $new_data=explode("_",$row[csf('status').$key]); 
						else $new_data=explode("_",$new_approval_arr[$row[csf('job_no')]][$key]);
						if($new_data[7]!="") $function="onclick='update_tna_process(1,$new_data[7],".$row[csf('po_number_id')].")'"; else $function="";
						
						//if(!in_array($vid,$manual_update_task_arr)){$function="";}
 
						if($plan_manual_update_task_arr[$vid]==''){$function="";}
						if($row['APPROVED']==1){$function="onclick='approvedAlertMessage()'";}

						//echo $new_data[9];

						if($new_data[9]==1){$psc=" style='color:#0000FF'";}else{$psc="";}
						if($new_data[10]==1){$pfc=" style='color:#0000FF'";}else{$pfc="";}
						
						
						if(in_array($vid,$tast_id_arr))
						{
							if(count($tna_task_id)==$i)
								echo '<td id="plan_1'.$vid.$row[csf('po_number_id')].'" align="center" '.$psc.' width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? "<span style='color:#FF0000'> N/A </span>" : change_date_format($new_data[2])).'</td><td id="plan_2'.$vid.$row[csf('po_number_id')].'" align="center" '.$pfc.' '.$function.'> '.($new_data[3]== "N/A" || $new_data[3]=="0000-00-00"? "" : change_date_format($new_data[3])).'</td>';
							 else
								echo '<td id="plan_1'.$vid.$row[csf('po_number_id')].'" align="center" '.$psc.' width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? "<span style='color:#FF0000'> N/A </span>" : change_date_format($new_data[2])).'</td><td id="plan_2'.$vid.$row[csf('po_number_id')].'"  align="center" '.$pfc.' width="80" '.$function.'> '.($new_data[3]== "" || $new_data[3]=="0000-00-00"? "<span style='color:#FF0000'> N/A </span>" : change_date_format($new_data[3])).'</td>';
						}
						else
						{
							if(count($tna_task_id)==$i)
								echo '<td id="plan_1'.$vid.$row[csf('po_number_id')].'" align="center" '.$psc.' width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? "N/A" : change_date_format($new_data[2])).'</td><td id="plan_2'.$vid.$row[csf('po_number_id')].'" align="center" '.$pfc.' '.$function.'> '.($new_data[3]== "" || $new_data[3]=="0000-00-00"? "N/A" : change_date_format($new_data[3])).'</td>';
							 else
								echo '<td id="plan_1'.$vid.$row[csf('po_number_id')].'" align="center" '.$psc.' width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? "N/A" : change_date_format($new_data[2])).'</td><td id="plan_2'.$vid.$row[csf('po_number_id')].'" align="center" '.$pfc.' width="80" '.$function.'> '.($new_data[3]== ""  || $new_data[3]=="0000-00-00"? "N/A" : change_date_format($new_data[3])).'</td>';
						}
						
						
					 }
					echo '</tr>';
					
					
					 
					
					echo '<tr style="cursor:pointer" onClick="change_color(\'actula_'.$h.'\',\''.$bgcolor.'\')" id="actula_'.$h.'"><td width="90">Actual</td>';
					$i=0;
					 foreach($tna_task_id as $vid=>$key)
					 {
						  
						 $i++;
						if ( $new_approval_arr[$row[csf('job_no')]][$key]==""){$new_data=explode("_",$row[csf('status').$key]);}
						else{$new_data=explode("_",$new_approval_arr[$row[csf('job_no')]][$key]);}
						
						if( $new_data[7]!="") $function="onclick='update_tna_process(2,$new_data[7],".$row[csf('po_number_id')].")'";  else $function="";
						$bgcolor1=""; $bgcolor="";
						
						if($actual_manual_update_task_arr[$vid]==''){$function="";}
						if($row['APPROVED']==1){$function="onclick='approvedAlertMessage()'";}
						
						
						if (trim($new_data[2])!= $blank_date) 
						{
							if (strtotime($new_data[4])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($new_data[2])){$bgcolor="#FFFF00";}//Yellow
							else if (strtotime($new_data[2])<strtotime(date("Y-m-d",time()))){$bgcolor="#FF0000";}//Red
							else {$bgcolor="";}
							
						}
						 
						if ($new_data[3]!= $blank_date) {
							if (strtotime($new_data[5])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($new_data[3])){$bgcolor1="#FFFF00";}//Yellow
							else if (strtotime($new_data[3])<strtotime(date("Y-m-d",time()))){$bgcolor1="#FF0000";}//Red ;
							else{$bgcolor1="";}
						}
						
						if ($new_data[0]!=$blank_date) $bgcolor="";
						if ($new_data[1]!=$blank_date) $bgcolor1="";

						if($new_data[11]==1){$asc=" style='color:#0000FF'";}else{$asc="";}
						if($new_data[12]==1){$afc=" style='color:#0000FF'";}else{$afc="";}

						// if($new_data[9]==1){$psc2=" style='color:#0000FF'";}else{$psc2="";}
						// if($new_data[10]==1){$pfc2=" style='color:#0000FF'";}else{$pfc2="";}
						
						
						$idd=$row[csf('job_no')]."".$row[csf('po_number_id')]."".$key;
						if(count($tna_task_id)==$i)
							echo '<td id="actual_1'.$vid.$row[csf('po_number_id')].'" align="center" '.$asc.' title="Click Here to Edit Date" id="'.$idd.'1" '.$function.' width="80" bgcolor="'.$bgcolor.'">'.($new_data[0]== "" || $new_data[0]=="0000-00-00" ? "" : change_date_format($new_data[0])).'</td><td id="actual_2'.$vid.$row[csf('po_number_id')].'" align="center" '.$afc.' id="'.$idd.'2" title="Click Here to Edit Date" '.$function.' bgcolor="'.$bgcolor1.'" title="'.$new_data[6].'">'.($new_data[1]== "" || $new_data[1]=="0000-00-00" ? "" : change_date_format($new_data[1])).'</td>';
							
						else
							echo '<td id="actual_1'.$vid.$row[csf('po_number_id')].'" align="center" '.$asc.' id="'.$idd.'1" title="Click Here to Edit Date"  '.$function.' width="80" bgcolor="'.$bgcolor.'">'.($new_data[0]== "" || $new_data[0]=="0000-00-00" ? "" : change_date_format($new_data[0])).'</td><td id="actual_2'.$vid.$row[csf('po_number_id')].'" align="center" '.$afc.' id="'.$idd.'2" title="Click Here to Edit Date" '.$function.' width="80" bgcolor="'.$bgcolor1.'" title="'.$new_data[6].'">'.($new_data[1]== "" || $new_data[1]=="0000-00-00" ? "" : change_date_format($new_data[1])).'</td>';
						
					 }
					echo '</tr>'; 
					
					echo '<tr style="cursor:pointer" onClick="change_color(\'delay_'.$h.'\',\''.$bgcolor.'\')" id="delay_'.$h.'"><td width="90">Delay/Early By</td>';
					$j=0;
					foreach($tna_task_id as $vid=>$key)
					{
						 $j++;
						if ( $new_approval_arr[$row[csf('job_no')]][$key]=="") $new_data=explode("_",$row[csf('status').$key]); 
						else $new_data=explode("_",$new_approval_arr[$row[csf('job_no')]][$key]);
						
						$bgcolor1=""; $bgcolor="";
						
						
						if($new_data[0]!=$blank_date)
						{
							$start_diff1 = datediff( "d", $new_data[0], $new_data[2]);
							if($new_data[0]== "")
							{
								$start_diff=$start_diff1;
							}
							else
							{
								$start_diff=$start_diff1-1;
							}
							if($start_diff<0)
							{
								$bgcolor="#2A9FFF"; //Blue
							}
							if($start_diff>0)
							{
								$bgcolor="";
							}
						}
						else
						{
							if(strtotime(date("Y-m-d"))>strtotime($new_data[2]))
							{
								$start_diff1 = datediff( "d", $new_data[2], date("Y-m-d"));
								if($new_data[0]== "")
								{
									//$start_diff=-abs($start_diff1);
									$start_diff=-abs($start_diff1-1);
								}
								else
								{
									$start_diff=-abs($start_diff1-1);
								}
								//$bgcolor="#FF0000";		//Red
								$bgcolor=($new_data[2]== "" || $new_data[2]=="0000-00-00")?'':'#FF0000';
							}
							if(strtotime(date("Y-m-d"))<=strtotime($new_data[2]))
							{
								$start_diff = "";
								$bgcolor="";
							}
						}
						if($new_data[1]!=$blank_date)
						{
							$finish_diff1 = datediff( "d", $new_data[1], $new_data[3]);
							if($new_data[0]== "")
							{
								$finish_diff=$finish_diff1;
							}
							else
							{	
								$finish_diff=$finish_diff1-1;
							}
							if($finish_diff<0)
							{
								$bgcolor1="#2A9FFF";
							}
							if($finish_diff>0)
							{	
								$bgcolor1="";
							}
						}
						else
						{
							if(strtotime(date("Y-m-d"))>strtotime($new_data[3]))
							{
								
								$finish_diff1 = datediff( "d", $new_data[3], date("Y-m-d"));
								if($new_data[1]== "")
								{
									//$finish_diff=-abs($finish_diff1);
									$finish_diff=-abs($finish_diff1-1);
								}
								else
								{
									$finish_diff=-abs($finish_diff1-1);
								}
								//$bgcolor1="#FF0000";
								$bgcolor1=($new_data[3]== "" || $new_data[3]=="0000-00-00")?'':'#FF0000';
							}
							if(strtotime(date("Y-m-d"))<=strtotime($new_data[3]))
							{
								
								$finish_diff = "";
								$bgcolor1="";
							}
						}
						
						
						
						if(count($tna_task_id)==$j)
							
							echo '<td width="80" align="center" bgcolor="'.$bgcolor.'">'.($start_diff).'</td><td width="80" bgcolor="'.$bgcolor1.'" align="center">'.($finish_diff).'</td>';
						else
							echo '<td width="80" align="center" bgcolor="'.$bgcolor.'">'.($start_diff).'</td><td width="80" bgcolor="'.$bgcolor1.'" align="center">'.($finish_diff).'</td>';
					}
					 
					echo '</tr>';
					
					
					 
		 }
				 
	}
		?>
     
     
    </table>
    </div>
    <div style="width:<? echo $width+20; ?>px;" align="left">
         <table width="<? echo $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <tfoot>
                <th width="40"></th>
                <th width="120"></th>
                <th width="70"></th>
                <th width="110">Total</th>
                <th width="90" id="total_po_qty" align="right"><p><? echo number_format($tot_po_qty,2);?></p></th>
                <th width="90" id="total_po_qty_pcs" align="right"><p><? echo number_format($total_po_qty_pcs,2);?></p></th>
                <th width="70"></th>
                <th width="70"></th>
                <th width="40"></th>
                <th colspan="<? echo (count($tna_task_id)*2)+6;?>"></th>
            </tfoot>
        </table>
    </div>
    
    
          <?
		  
		 $sql = sql_select("select designation,name from variable_settings_signature where report_id=95 and company_id=$cbo_company_id order by sequence_no" );

	     $count=count($sql);

		$width=$width+170;
		$td_width=floor($width/$count);
		
		$standard_width=$count*150;
		
		if($standard_width>$width) $td_width=150;
		
		$no_coloumn_per_tr=floor($width/$td_width);
		$col=$count-2;
		$i=1;
		echo '<table width="'.$width.'"><tr><td width="'.$td_width.'" align="center" valign="bottom">'.$user_arr[$inserted_by].'</td><td height="70" colspan="'.$col.'"></td><td width="'.$td_width.'" align="center" valign="bottom">'.$user_arr[$nameArray_approved_date_row[csf('approved_by')]].'</td></tr><tr>';
		foreach($sql as $row)	
		{
			echo '<td width="'.$td_width.'" align="center" valign="top"><strong style="text-decoration:overline">'.$row[csf("designation")]."</strong><br>".$row[csf("name")].'</td>';
			
			if($i%$no_coloumn_per_tr==0) echo '</tr><tr><td height="70" colspan="'.$no_coloumn_per_tr.'"></td><tr>';
			$i++;
		} 
		echo '</tr></table>';



	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
 	echo "$total_datass****$filename";
	exit();
}


if($action=="generate_style_wise_report")
{
	if($graph==1){
		if($db_type==0)
		{
			$txt_date_from = date("'Y-m-d'",strtotime($txt_date_from));
			$txt_date_to = date("'Y-m-d'",strtotime($txt_date_to));
		}
		else
		{
			$txt_date_from = date("'d-M-Y'",strtotime($txt_date_from));
			$txt_date_to = date("'d-M-Y'",strtotime($txt_date_to));
		}
	}

	$actual_manual_update_task_arr=return_library_array("select task_id,task_id from tna_manual_permission where is_actual_manual=1  and company_id=$cbo_company_name","task_id","task_id");
	$plan_manual_update_task_arr=return_library_array("select task_id,task_id from tna_manual_permission where is_plan_manual=1  and company_id=$cbo_company_name","task_id","task_id");


	$plan_manual_update_task_arr[0]=0;
	$actual_manual_update_task_arr[0]=0;
	$plan_actual_menual_task_arr = $actual_manual_update_task_arr + $plan_manual_update_task_arr;


	$mod_sql= sql_select("select a.id,a.task_catagory,a.task_name,a.task_short_name,a.task_type,a.completion_percent, a.task_group, a.task_sequence_no,b.task_template_id,b.lead_time ,b.tna_task_id
	from lib_tna_task a,tna_task_template_details b where a.task_name=b.tna_task_id AND a.task_type = 1 and b.task_type=1 and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 order by a.task_sequence_no asc");
	
	$tna_task_array=array();
	$tna_task_id=array();
	$tna_task_cat=array();
	$tna_task_name_arr=array();
	foreach ($mod_sql as $row)
	{
		$tna_task_group_by_id[$row[csf("task_name")]] = $row[csf("task_group")];

		$tna_task_id[$row[csf("task_name")]]=$row[csf("task_name")];
		$tna_task_array[$row[csf("task_name")]] =$row[csf("task_short_name")];
		$tna_task_name_array[$row[csf("id")]] =$tna_task_name[$row[csf("task_name")]];
		$tna_task_cat[$row[csf("id")]]=$row[csf("task_catagory")];
		$tna_task_name_arr[$row[csf("id")]]=$row[csf("task_name")];
		$lead_time_array[$row[csf("task_template_id")]]=$row[csf("lead_time")];
		$tast_tmp_id_arr[$row[csf("task_template_id")]][$row[csf("tna_task_id")]]=$row[csf("tna_task_id")];
	}
	//print_r($lead_time_array);die;
	
	$cbo_company_id=$cbo_company_name;
	
	$order_status_cond="";
	if(str_replace("'","",$cbo_order_status)>0) $order_status_cond=" and b.is_confirmed=$cbo_order_status";
	
	if(str_replace("'","",$cbo_company_name)==0) $cbo_company_name=""; else $cbo_company_name=" and a.company_name = $cbo_company_name";
	if(str_replace("'","",$cbo_buyer_name)==0) $cbo_buyer_name=""; else $cbo_buyer_name=" and a.buyer_name = $cbo_buyer_name";
	if(str_replace("'","",$cbo_team_member)==0) $cbo_team_member=""; else $cbo_team_member=" and a.dealing_marchant = $cbo_team_member";
	
	if(str_replace("'","",$cbo_search_type)==1){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
	}
	else if(str_replace("'","",$cbo_search_type)==3){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and c.country_ship_date between $txt_date_from and $txt_date_to";
	}
	else if(str_replace("'","",$cbo_search_type)==4){
		if($db_type==0)
		{
			if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)==""){$date_range="";}else{ 
			$date_range=" and b.insert_date between ".$txt_date_from." and ".$txt_date_to."";}
		}
		else
		{
			
			if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)==""){$date_range="";}else{ 
			$date_range=" and b.insert_date between ".$txt_date_from." and '".str_replace("'","",$txt_date_to)." 11:59:59 PM'";}
		}
	}
	else
	{
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.po_received_date between $txt_date_from and $txt_date_to";
	}
	
	

	
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if($txt_job_no=="") $txt_job_no=""; else $txt_job_no=" and a.job_no like('%$txt_job_no')";
	$txt_order_no=str_replace("'","",$txt_order_no);
	if($txt_order_no=="") $txt_order_no=""; else $txt_order_no=" and b.po_number ='$txt_order_no'";
	$txt_file_no=str_replace("'","",$txt_file_no);
	if($txt_file_no=="") $file_cond=""; else $file_cond=" and b.file_no ='$txt_file_no'";
	$txt_int_ref_no=str_replace("'","",$txt_int_ref_no);
	if($txt_int_ref_no=="") $ref_cond=""; else $ref_cond=" and b.grouping ='$txt_int_ref_no'";
	
	$txt_style_ref_no=str_replace("'","",$txt_style_ref_no);
	if($txt_style_ref_no=="") $txt_style_ref_no=""; else $txt_style_ref_no=" and a.style_ref_no ='$txt_style_ref_no'";
	//**txt_date_from*txt_date_to*txt_job_no
	
	//if(str_replace("'","",$cbo_shipment_status)==3)$shipment_status_con=" and b.shiping_status=$cbo_shipment_status"; else $shipment_status_con=" and b.shiping_status !=3";
	
	if(str_replace("'","",$cbo_shipment_status)==4){$shipment_status_con=" and b.shiping_status=3";}
	else if(str_replace("'","",$cbo_shipment_status)==1){$shipment_status_con=" and b.shiping_status !=3";}
	
	
	//echo $cbo_shipment_status;die;
	
	
	$tna_all_task=implode(",",$tna_task_id);
	
	if(str_replace("'","",$cbo_search_type)==3)
	{
		$sql = "SELECT a.team_leader,a.factory_marchant,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, b.id ,b.po_number, b.file_no, b.grouping as in_ref_no,b.po_quantity,b.PUB_SHIPMENT_DATE
		FROM  wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c 
		WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no  and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 $order_status_cond $file_cond $ref_cond";
	}
	else
	{
		$sql = "SELECT a.team_leader,a.factory_marchant,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, b.id, b.po_number, b.file_no, b.grouping as in_ref_no ,b.po_quantity,b.PUB_SHIPMENT_DATE
		FROM  wo_po_details_master a,  wo_po_break_down b 
		WHERE a.job_no=b.job_no_mst $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no  and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 $order_status_cond  $file_cond $ref_cond"; 
	}
	
    //echo $sql; 
	$result = sql_select( $sql ) ;
	$wo_po_details_master = array();
	$po_no_arr=array();
	$job_no_arr=array();
	foreach( $result as  $row ) 
	{	
		$wo_po_details_master[$row[csf('id')]]['company_name']=$row[csf('company_name')];
		$po_no_arr[]=$row[csf('id')];
		$job_no_arr[]=$row[csf('job_no')];
		
		//$wo_po_details_master[$row[csf('id')]]['dealing_marchant']=$row[csf('dealing_marchant')];
		//$wo_po_details_master[$row[csf('id')]]['factory_marchant']=$row[csf('factory_marchant')];
		//$wo_po_details_master[$row[csf('id')]]['team_leader']=$row[csf('team_leader')];

		$wo_po_details_master[$row[csf('job_no')]][$row["PUB_SHIPMENT_DATE"]]['po_id'][$row[csf('id')]]= $row[csf('id')];

		
		$wo_po_details_master[$row[csf('job_no')]][$row["PUB_SHIPMENT_DATE"]]['po_number'][$row[csf('po_number')]]= $row[csf('po_number')];
		$wo_po_details_master[$row[csf('job_no')]][$row["PUB_SHIPMENT_DATE"]]['set_smv']= $row[csf('set_smv')];
		$wo_po_details_master[$row[csf('job_no')]][$row["PUB_SHIPMENT_DATE"]]['file_no']= $row[csf('file_no')];

		$wo_po_details_master[$row[csf('job_no')]][$row["PUB_SHIPMENT_DATE"]]['dealing_marchant']=$row[csf('dealing_marchant')];
		$wo_po_details_master[$row[csf('job_no')]][$row["PUB_SHIPMENT_DATE"]]['factory_marchant']=$row[csf('factory_marchant')];
		$wo_po_details_master[$row[csf('job_no')]][$row["PUB_SHIPMENT_DATE"]]['team_leader']=$row[csf('team_leader')];
		$wo_po_details_master[$row[csf('job_no')]][$row["PUB_SHIPMENT_DATE"]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
		$wo_po_details_master[$row[csf('job_no')]][$row["PUB_SHIPMENT_DATE"]]['in_ref_no']= $row[csf('in_ref_no')];
		$wo_po_details_master[$row[csf('job_no')]][$row["PUB_SHIPMENT_DATE"]]['buyer_name']=$row[csf('buyer_name')];
		$wo_po_details_master[$row[csf('job_no')]][$row["PUB_SHIPMENT_DATE"]]['style_ref_no']=$row[csf('style_ref_no')];
		$wo_po_details_master[$row[csf('job_no')]][$row["PUB_SHIPMENT_DATE"]]['po_quantity']+=$row[csf('po_quantity')];
	}
	
	
 
	$result = sql_select("select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name" ) ;
	$team_leader_name = array();
	foreach( $result as  $row ) 
	{	
		$team_leader_name[$row[csf('id')]]=$row[csf('team_leader_name')];
	}

 
	$sql = "SELECT team_member_name,id FROM lib_mkt_team_member_info WHERE is_deleted = 0 and status_active=1 order by id asc";
	$result = sql_select( $sql ) ;
	$team_member_name = array();
	foreach( $result as  $row ) 
	{	
		$team_member_name[$row[csf('id')]]=$row[csf('team_member_name')];
	}
	
	$sql = "SELECT buyer_name,id FROM  lib_buyer WHERE is_deleted = 0 and status_active=1 order by id asc";
	$result = sql_select( $sql ) ;
	$buyer_name = array();
	foreach( $result as  $row ) 
	{	
		$buyer_name[$row[csf('id')]]=$row[csf('buyer_name')];
	}
	
	
	$po_no_arr_all=implode(",",$po_no_arr); if($po_no_arr_all!="") $po_no_arr_all .=",0"; else $po_no_arr_all .="0"; 
	$job_no_all="'".implode("','",$job_no_arr)."'";
	$c=count($tna_task_id);
	
	if($db_type==0)
	{
		$sql ="select a.id,a.task_number,a.po_number_id, a.job_no, a.shipment_date,min(b.pub_shipment_date) as pub_shipment_date, a.template_id, a.po_receive_date,b.insert_date,";
		$i=1;
	
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c) $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id, ";
			else $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id ";
			$i++;
		}
		
		$sql .=" from tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id and a.po_number_id in( $po_no_arr_all ) and a.job_no in ($job_no_all) $shipment_status_con and b.status_active=1  and b.po_quantity>0 $order_status_cond and a.task_type=1 group by a.po_number_id,a.job_no,b.insert_date order by a.shipment_date,a.po_number_id,a.job_no"; 
	}
	else
	{
		$sql ="select a.job_no,a.task_number,  
		LISTAGG(cast(a.id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as id, 
		LISTAGG(cast(a.po_number_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as po_number_id, 
		LISTAGG(cast(a.template_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as template_id, 
		min(a.shipment_date) as shipment_date,
		(b.pub_shipment_date) as pub_shipment_date,
		min(a.po_receive_date) as po_receive_date,
		min(b.insert_date) as insert_date,
		
	    (min(a.actual_start_date) || '_' || min(a.task_start_date) || '_' || MAX(a.actual_finish_date) || '_' || MAX(a.task_finish_date) || '_' || min(a.plan_start_flag) || '_' || MAX(a.plan_finish_flag) || '_' || min(a.notice_date_start) || '_' || min(a.notice_date_end) || '_' || LISTAGG(cast(a.id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) ) as status ";
			
			
		//------------------
			$sql_order_con='';
			$po_no_arr_all=explode(',',$po_no_arr_all);
			$chunk_po_no_arr_all=array_chunk(array_unique($po_no_arr_all),999);
			$p=1;
			foreach($chunk_po_no_arr_all as $rlz_sub_id)
			{
				if($p==1) $sql_order_con .=" and (a.po_number_id in(".implode(',',$rlz_sub_id).")"; 
				else $sql_sub_lc .=" or a.po_number_id in(".implode(',',$rlz_sub_id).")";
				$p++;
			}
			$sql_order_con .=" )";
			
			$sql_job_con='';
			$job_no_all=explode(',',$job_no_all);
			$chunk_job_no_all=array_chunk(array_unique($job_no_all),999);
			$q=1;
			foreach($chunk_job_no_all as $rlz_sub_id)
			{
				if($q==1) $sql_job_con .=" and (a.job_no in(".implode(',',$rlz_sub_id).")"; 
				else $sql_sub_lc .=" or a.job_no in(".implode(',',$rlz_sub_id).")";
				$p++;
			}
			$sql_job_con .=" )";
			
			
		//-------------------------------
		$sql .=" from  tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id $sql_order_con $sql_job_con $shipment_status_con and b.status_active=1 and b.po_quantity>0 $order_status_cond  and a.task_type=1  group by a.job_no,a.task_number,b.pub_shipment_date order by a.job_no"; 
	}
	$data_sql_2= sql_select($sql);
	      //echo $sql;die;
		
		foreach($data_sql_2 as $row){
			$dataArr[$row[csf('job_no')]][$row[csf('pub_shipment_date')]][$row[csf('task_number')]]=$row;
			$data_sql[$row[csf('job_no')]][$row[csf('pub_shipment_date')]]=$row;
		}
		
		
	//booking...........................................start;	// 
		$sql_con="select a.JOB_NO,a.BOOKING_NO,b.PO_BREAK_DOWN_ID from wo_booking_mst a,WO_BOOKING_DTLS b where a.BOOKING_NO=b.BOOKING_NO  and a.is_short=2  and a.booking_type=1 and a.STATUS_ACTIVE=1  and a.IS_DELETED=0 and b.STATUS_ACTIVE=1  and b.IS_DELETED=0 and  b.PO_BREAK_DOWN_ID>0 and a.JOB_NO is not null";
		$q=1;
		foreach($chunk_po_no_arr_all as $rlz_sub_id)
		{
			if($q==1) $sql_con .=" and (b.PO_BREAK_DOWN_ID in(".implode(',',$rlz_sub_id).")"; 
			else $sql_con .=" or b.PO_BREAK_DOWN_ID in(".implode(',',$rlz_sub_id).")";
			$p++;
		}
		$sql_con .=") group by a.JOB_NO, a.BOOKING_NO,b.PO_BREAK_DOWN_ID order by a.BOOKING_NO";

	
		
		$sql_booking_sql = sql_select($sql_con);
		$booking_no_arr=array();
		foreach( $sql_booking_sql as  $row ) 
		{	
			$booking_no_arr[$row['JOB_NO']][$row['PO_BREAK_DOWN_ID']][$row['BOOKING_NO']]=$row['BOOKING_NO'];
		}	
	//booking...........................................end;	
		
		//echo $sql_con;die;
		
		
		
		
	//$data_sql= sql_select($sql);
	$width=(count($tna_task_id)*161)+1280;
	
	
	ob_start();
	
	?>
    
    
   <div style="margin:0 0%;">
        <span style="background:#FF0000; padding:0 6px; border-radius:9px; cursor:pointer;" title="Red">&nbsp;</span>&nbsp; Target Date Over. &nbsp;&nbsp;
        <span style="background:#2A9FFF; padding:0 6px; border-radius:9px; cursor:pointer;" title="Blue">&nbsp;</span>&nbsp; Done In Late. &nbsp;&nbsp;
        <span style="background:#FFFF00; padding:0 6px; border-radius:9px; cursor:pointer;" title="Yellow">&nbsp;</span>&nbsp; Reminder.
        
        <span style="background:#0000FF; padding:0 6px; border-radius:9px; cursor:pointer;" title="Royal Blue">&nbsp;</span>&nbsp; Manual Update Plan.
        
    </div>    
    <div style="width:<? echo $width+60; ?>px" align="left">
    <table width="<? echo $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
    	<thead>
        	<tr>
            	<th width="40" rowspan="2">SL</th>
                <th width="120" rowspan="2">Team Leader</th>
                <th width="120" rowspan="2">Dealing Merchant</th>
                <th width="120" rowspan="2">Factory Merchant</th>
                <th width="70" rowspan="2">Buyer Name</th>
                <th width="120" rowspan="2">Style Ref.</th> 
                <th width="40" rowspan="2">Job No.</th>
                <th width="110" rowspan="2">PO Number</th>
                <th width="90" rowspan="2">PO Qty.</th>
                <th width="40" rowspan="2">SMV</th>
                <th rowspan="2">Fab. Booking</th>
                <th width="100" rowspan="2">Shipment Date</th>
                <th width="80" rowspan="2">PO Insert Date</th>
                <th width="90" rowspan="2">Status</th>
                <?
					$i=0;
					foreach($tna_task_id as $task_name=>$key)
					{
					 
						$i++;
						$td_bgcolor = ($plan_actual_menual_task_arr[$task_name]) ? "orange" : "";
						
						if(count($tna_task_id)==$i) 
						echo '<th style="background:'.$td_bgcolor.';" width="160" colspan="2" title="'.$task_name.'='.$tna_task_name[$task_name].'">'. $tna_task_array[$key]." <br><small>".$tna_task_group_by_id[$task_name]."</small> </th>"; 

					    else echo '<th style="background:'.$td_bgcolor.';" width="160" colspan="2" title="'.$task_name.'='.$tna_task_name[$task_name].'">'.$tna_task_array[$key]." <br><small>".$tna_task_group_by_id[$task_name]."</small> </th>";


					}
					echo '</tr><tr>';
					$i=0;
					foreach($tna_task_id as $key)
					{
						$i++;
						if(count($tna_task_id)==$i){ 
						echo '<th width="80" title="Plan Start Date=Ship Date-(Deadline+Execution Days+1)">Start</th><th width="80" title="Plan Finish Date=(Ship Date-Deadline)"> Finish</th>';}else{
						echo '<th width="80" title="Plan Start Date=Ship Date-(Deadline+Execution Days+1)">Start</th><th width="80" title="Plan Finish Date=(Ship Date-Deadline)"> Finish</th>';}
					}
					echo '</tr>';
					 
				?>
                </thead>
                </table>
         </div>
         
        	<div style="overflow-y:scroll; max-height:360px; width:<? echo $width+20; ?>px;" align="left" id="scroll_body">
          	<table width="<? echo $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
       
    <?
	
	$tid=0;
	$i=1;
	$count=0;
	$kid=1;
	$new_job_no=array();
	$h=0;
	$tot_po_qty=0;
	
	foreach ($data_sql as $row2)
	{
	
	foreach ($row2 as $pub_ship_date=>$row)
	{
		
		 if($row[csf('po_number_id')]==0)
		 {
			 foreach($tna_task_id as $vid=>$key)
			 {
				if ($row[csf('status').$key]!="") $new_approval_arr[$row[csf('job_no')]][$key]=$row[csf('status').$key];
			 }
		 }
		 else
		 {
			 $h++;
			 if ($h%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
							
		?>
        		<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle; cursor:pointer;" height="25" onClick="change_color('tr_<? echo $h;?>','<? echo $bgcolor;?>')" id="tr_<? echo $h; ?>">
                    <td width="40" rowspan="3" align="center"><? echo $kid++;?></td>
                    <td width="120" rowspan="3">
						<? 
							echo $team_leader_name[$wo_po_details_master[$row[csf('job_no')]][$pub_ship_date]['team_leader']];
                        ?>
                    </td>
                    <td width="120" rowspan="3">
						<? 
							echo $team_member_name[$wo_po_details_master[$row[csf('job_no')]][$pub_ship_date]['dealing_marchant']];
                        ?>
                    </td>
                    <td width="120" rowspan="3">
						<? 
							echo $team_member_name[$wo_po_details_master[$row[csf('job_no')]][$pub_ship_date]['factory_marchant']]; 
                        ?>
                    </td>
                    <td width="70" rowspan="3"><p><? echo $buyer_name[$wo_po_details_master[$row[csf('job_no')]][$pub_ship_date]['buyer_name']]; ?></p></td>
                    <td width="120" rowspan="3" title="<? echo $row[csf('job_no')]; ?>"><p><? echo $wo_po_details_master[$row[csf('job_no')]][$pub_ship_date]['style_ref_no']; ?></p></td>
                    
                    <td width="40" rowspan="3" align="center"><? echo $wo_po_details_master[$row[csf('job_no')]][$pub_ship_date]['job_no_prefix_num']; ?></td>
                    
					
					
					 <td width="110" rowspan="3" align="center"><p>
						<div style="width:107px; word-break:break-all">
							<? 
							echo "<a href='#report_details' style='color:#990000' onclick= \"progress_comment_popup_style('".$row[csf('job_no')]."','".$row[csf('po_number_id')]."','".$row[csf('template_id')]."','".$tna_process_type."');\">".implode(',',$wo_po_details_master[$row[csf('job_no')]][$pub_ship_date]['po_number'])."</a>";
							?>
						</div>
					</td>
                    
                    <td width="90" rowspan="3" align="right"><p>
						<?
							$po_qty=$wo_po_details_master[$row[csf('job_no')]][$pub_ship_date]['po_quantity']; 
							echo number_format($po_qty);
							$tot_po_qty+=$po_qty;
						?>
                        </p>
                    </td>
                    <td width="40" rowspan="3" align="center"><? echo number_format($wo_po_details_master[$row[csf('job_no')]][$pub_ship_date]['set_smv'],2); ?></td>
                    
                     <td rowspan="3"><?
					 $bookingArr=array();
					 foreach($wo_po_details_master[$row[csf('job_no')]][$pub_ship_date]['po_id'] as $poId){ 
					 	$bookingArr[$poId]= implode(",",$booking_no_arr[$row[csf('job_no')]][$poId]);
					 }
					 echo implode(", ",array_unique(explode(',',implode(",",$bookingArr))));
					 ?></td>
                     <? 
					 	if($tna_process_type==1)
						{
							$lead_time_arr=array();
							foreach(explode(',',$row[csf('template_id')]) as $tmi){
								$lead_time_arr[$tmi]=$lead_time_array[$tmi];
							}
							$lead_time="Template Lead Time: ".implode(',',$lead_time_arr);
						}
						else
						{
							$lead_time="Lead Time: ".($row[csf('template_id')]+1);
						}
						$po_lead_time=datediff( "d", date("Y-m-d",strtotime(change_date_format($row[csf('po_receive_date')]))), date("Y-m-d",strtotime(change_date_format($row[csf('shipment_date')]))) );

					 ?>
                    <td width="100" rowspan="3" title="<? echo " PO. Rec. Date: ".change_date_format($row[csf('po_receive_date')]); echo ",\n Ship Date: ".$row[csf('shipment_date')] .",\n Insert Date: ".$row[csf('insert_date')];?>"><div style="width:98px; word-break:break-all">
					<? echo change_date_format($row[csf('pub_shipment_date')])."<br>"." ".$lead_time."<br>"." PO Lead Time:".($po_lead_time-1);  ?></div>
                    </td>
                    <td width="80" rowspan="3" align="center"><? echo date("d-m-Y",strtotime($row[csf('insert_date')]));?></td>
                    <td width="90">Plan</td>
                <?
					 $tast_id_arr=array_unique(explode(',',$tast_tmp_id_arr[$row[csf('template_id')]]));
					 $i=0;
					 foreach($tna_task_id as $vid=>$key)
					 {
						 $i++;
						list($actual_start_date,$task_start_date,$actual_finish_date,$task_finish_date,$plan_start_flag,$plan_finish_flag,$notice_date_start,$notice_date_end,$mst_id)=explode("_",$dataArr[$row[csf('job_no')]][$pub_ship_date][$key]['STATUS']);
						if($mst_id!="") $function="onclick=update_tna_process_style(1,'$mst_id','".$row[csf('po_number_id')]."')"; else $function="";
						if($plan_manual_update_task_arr[$vid]==''){$function="";}
						
						if($plan_start_flag==1){$psc=" style='color:#0000FF'";}else{$psc="";}
						if($plan_finish_flag==1){$pfc=" style='color:#0000FF'";}else{$pfc="";}
						
						
						if(count($tna_task_id)==$i)
							echo '<td align="center" '.$psc.'   width="80" '.$function.'>'.($task_start_date== "" || $task_start_date=="0000-00-00" ? "N/A" : change_date_format($task_start_date)).'</td><td align="center" '.$pfc.' '.$function.'> '.($task_finish_date== "N/A"  || $task_finish_date=="0000-00-00"? "" : change_date_format($task_finish_date)).'</td>';
						 else
							echo '<td align="center" '.$psc.'  width="80" '.$function.'>'.($task_start_date== "" || $task_start_date=="0000-00-00" ? "N/A" : change_date_format($task_start_date)).'</td><td align="center" '.$pfc.' width="80" '.$function.'> '.($task_finish_date== ""  || $task_finish_date=="0000-00-00"? "N/A" : change_date_format($task_finish_date)).'</td>';
					}
					echo '</tr>';
					
					echo '<tr style="cursor:pointer" onClick="change_color(\'actula_'.$h.'\',\''.$bgcolor.'\')" id="actula_'.$h.'"><td width="90">Actual</td>';
					$i=0;
					 foreach($tna_task_id as $vid=>$key)
					 {
						$i++;
						list($actual_start_date,$task_start_date,$actual_finish_date,$task_finish_date,$plan_start_flag,$plan_finish_flag,$notice_date_start,$notice_date_end,$mst_id)=explode("_",$dataArr[$row[csf('job_no')]][$pub_ship_date][$key]['STATUS']);
						$bgcolor1=""; $bgcolor="";
						if($actual_manual_update_task_arr[$vid] != ''){
							$function="onclick=update_tna_process_style(2,'".$mst_id."','".$row[csf('po_number_id')]."',$vid)";
						}
						else{ $function="alert('No Permision')";}
						
						if (trim($task_start_date)!= $blank_date) 
						{
							if (strtotime($notice_date_start)<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($task_start_date)){$bgcolor="#FFFF00";}//Yellow
							else if (strtotime($task_start_date)<strtotime(date("Y-m-d",time()))){$bgcolor="#FF0000";}//Red
							else {$bgcolor="";}
							
						}
						 
						if ($task_finish_date!= $blank_date) {
							if (strtotime($notice_date_end)<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($task_finish_date)){$bgcolor1="#FFFF00";}//Yellow
							else if (strtotime($task_finish_date)<strtotime(date("Y-m-d",time()))){$bgcolor1="#FF0000";}//Red ;
							else{$bgcolor1="";}
						}
						
						if ($actual_start_date!=$blank_date) $bgcolor="";
						if ($actual_finish_date!=$blank_date) $bgcolor1="";
						$idd=$row[csf('job_no')]."".$row[csf('po_number_id')]."".$key;
						
						
						
						if(count($tna_task_id)==$i)
							echo '<td align="center" title="Click Here to Edit Date" id="'.$idd.'1" '.$function.' width="80" bgcolor="'.$bgcolor.'">'.($actual_start_date== "" || $actual_start_date=="0000-00-00" ? "" : change_date_format($actual_start_date)).'</td><td align="center" id="'.$idd.'2" title="Click Here to Edit Date" '.$function.' bgcolor="'.$bgcolor1.'" title="'.$actual_finish_date.'">'.($actual_finish_date== "" || $actual_finish_date=="0000-00-00" ? "" : change_date_format($actual_finish_date)).'</td>';
							
						else
							echo '<td align="center" id="'.$idd.'1" title="Click Here to Edit Date"  '.$function.' width="80" bgcolor="'.$bgcolor.'">'.($actual_start_date== "" || $actual_start_date=="0000-00-00" ? "" : change_date_format($actual_start_date)).'</td><td align="center" id="'.$idd.'2" title="Click Here to Edit Date" '.$function.' width="80" bgcolor="'.$bgcolor1.'" title="">'.($actual_finish_date== "" || $actual_finish_date=="0000-00-00" ? "" : change_date_format($actual_finish_date)).'</td>';
					 }
					echo '</tr>'; 
					
					echo '<tr style="cursor:pointer" onClick="change_color(\'delay_'.$h.'\',\''.$bgcolor.'\')" id="delay_'.$h.'"><td width="90">Delay/Early By</td>';
					$j=0;
					foreach($tna_task_id as $vid=>$key)
					{
						
						list($actual_start_date,$task_start_date,$actual_finish_date,$task_finish_date)=explode("_",$dataArr[$row[csf('job_no')]][$pub_ship_date][$key]['STATUS']);
						
						
						$j++;
						$bgcolor1=""; $bgcolor="";
						if($actual_start_date!=$blank_date)
						{
							$start_diff1 = datediff( "d", $actual_start_date, $task_start_date);
							if($actual_start_date== ""){ $start_diff=$start_diff1;}
							else{$start_diff=$start_diff1-1;}
							
							if($start_diff<0){$bgcolor="#2A9FFF";} //Blue
							if($start_diff>0){$bgcolor="";}
						}
						else
						{
							if(strtotime(date("Y-m-d"))>strtotime($actual_start_date))
							{
								$start_diff1 = datediff( "d", $actual_start_date, date("Y-m-d"));
								if( $actual_start_date== "")
								{
									$start_diff=-abs($start_diff1-1);
								}
								else
								{
									$start_diff=-abs($start_diff1-1);
								}
								$bgcolor=($actual_start_date== "" || $actual_start_date=="0000-00-00")?'':'#FF0000';
							}
							if(strtotime(date("Y-m-d"))<=strtotime($actual_start_date))
							{
								$start_diff = "";
								$bgcolor="";
							}
						}
						if($actual_finish_date!=$blank_date)
						{
							$finish_diff1 = datediff( "d", $actual_finish_date, $task_finish_date);
							if($actual_start_date== "")
							{
								$finish_diff=$finish_diff1;
							}
							else
							{	
								$finish_diff=$finish_diff1-1;
							}
							if($finish_diff<0)
							{
								$bgcolor1="#2A9FFF";
							}
							if($finish_diff>0)
							{	
								$bgcolor1="";
							}
						}
						else
						{
							if(strtotime(date("Y-m-d"))>strtotime($task_finish_date))
							{
								
								$finish_diff1 = datediff( "d", $task_finish_date, date("Y-m-d"));
								if($actual_finish_date== "")
								{
									$finish_diff=-abs($finish_diff1-1);
								}
								else
								{
									$finish_diff=-abs($finish_diff1-1);
								}
								//$bgcolor1="#FF0000";
								$bgcolor1=($task_finish_date== "" || $task_finish_date=="0000-00-00")?'':'#FF0000';
							}
							if(strtotime(date("Y-m-d"))<=strtotime($task_finish_date))
							{
								
								$finish_diff = "";
								$bgcolor1="";
							}
						}
						
						
						
						if(count($tna_task_id)==$j)
							
							echo '<td width="80" align="center" bgcolor="'.$bgcolor.'">'.($start_diff).'</td><td width="80" bgcolor="'.$bgcolor1.'" align="center">'.($finish_diff).'</td>';
						else
							echo '<td width="80" align="center" bgcolor="'.$bgcolor.'">'.($start_diff).'</td><td width="80" bgcolor="'.$bgcolor1.'" align="center">'.($finish_diff).'</td>';
					}
					 
					echo '</tr>';
					
					
					 
		 }
				 
	
	
	}
	
	}
		?>
     
     
    </table>
    </div>
    <div style="width:<? echo $width+20; ?>px;" align="left">
         <table width="<? echo $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <tfoot>
                <th width="40"></th>
                <th width="120"></th>
                <th width="120"></th>
                <th width="120"></th>
                <th width="70"></th>
                <th width="120"></th>
                <th width="40"></th>
                <th width="110">Total</th>
                <th width="90" id="total_po_qty_" align="right"><p><? echo number_format($tot_po_qty,2);?></p></th>
                <th width="40"></th>
                <th colspan="<? echo (count($tna_task_id)*2)+4;?>"></th>
            </tfoot>
        </table>
    </div>
    
    
          <?
		  
		 $sql = sql_select("select designation,name from variable_settings_signature where report_id=95 and company_id=$cbo_company_id order by sequence_no" );
	     $count=count($sql);

		$width=$width+170;
		$td_width=floor($width/$count);
		
		$standard_width=$count*150;
		
		if($standard_width>$width) $td_width=150;
		
		$no_coloumn_per_tr=floor($width/$td_width);
		$col=$count-2;
		$i=1;
		echo '<table width="'.$width.'"><tr><td width="'.$td_width.'" align="center" valign="bottom">'.$user_arr[$inserted_by].'</td><td height="70" colspan="'.$col.'"></td><td width="'.$td_width.'" align="center" valign="bottom">'.$user_arr[$nameArray_approved_date_row[csf('approved_by')]].'</td></tr><tr>';
		foreach($sql as $row)	
		{
			echo '<td width="'.$td_width.'" align="center" valign="top"><strong style="text-decoration:overline">'.$row[csf("designation")]."</strong><br>".$row[csf("name")].'</td>';
			
			if($i%$no_coloumn_per_tr==0) echo '</tr><tr><td height="70" colspan="'.$no_coloumn_per_tr.'"></td><tr>';
			$i++;
		} 
		echo '</tr></table>';



	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
 	echo "$total_datass****$filename";
	exit();
}



if($action=="generate_style_wise_report_by_first_po_wise")
{


	if($graph==1){
		if($db_type==0)
		{
			$txt_date_from = date("'Y-m-d'",strtotime($txt_date_from));
			$txt_date_to = date("'Y-m-d'",strtotime($txt_date_to));
		}
		else
		{
			$txt_date_from = date("'d-M-Y'",strtotime($txt_date_from));
			$txt_date_to = date("'d-M-Y'",strtotime($txt_date_to));
		}
	}

	if(str_replace(',','',$cbo_buyer_name)){$buyer_con = "and b.FOR_SPECIFIC =$cbo_buyer_name";}


	$actual_manual_update_task_arr=return_library_array("select task_id from tna_manual_permission where is_actual_manual=1  and company_id=$cbo_company_name","task_id","task_id");
	$plan_manual_update_task_arr=return_library_array("select task_id,task_id from tna_manual_permission where is_plan_manual=1  and company_id=$cbo_company_name","task_id","task_id");

	//print_r($actual_manual_update_task_arr);die;

	if($_SESSION['logic_erp']['tna_task_id']){$whereCon="and a.task_name in(".$_SESSION['logic_erp']['tna_task_id'].")";}

	$mod_sql= sql_select("select a.id,a.task_catagory,a.task_name,a.task_short_name,a.task_type,a.completion_percent,a.task_sequence_no,b.task_template_id,b.lead_time ,b.tna_task_id
	from lib_tna_task a,tna_task_template_details b where a.task_name=b.tna_task_id AND a.task_type = 1 and b.task_type=1 and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 $buyer_con $whereCon order by a.task_sequence_no asc");
	
	
	$tna_task_array=array();
	$tna_task_id=array();
	$tna_task_cat=array();
	$tna_task_name_arr=array();
	foreach ($mod_sql as $row)
	{
		$tna_task_id[$row[csf("task_name")]]=$row[csf("task_name")];
		$tna_task_array[$row[csf("task_name")]] =$row[csf("task_short_name")];
		$tna_task_name_array[$row[csf("id")]] =$tna_task_name[$row[csf("task_name")]];
		$tna_task_cat[$row[csf("id")]]=$row[csf("task_catagory")];
		$tna_task_name_arr[$row[csf("id")]]=$row[csf("task_name")];
		$lead_time_array[$row[csf("task_template_id")]]=$row[csf("lead_time")];
		$tast_tmp_id_arr[$row[csf("task_template_id")]][$row[csf("tna_task_id")]]=$row[csf("tna_task_id")];
	}
	// print_r($lead_time_array);die;
	
	$cbo_company_id=$cbo_company_name;
	
	$order_status_cond="";
	if(str_replace("'","",$cbo_order_status)>0) $order_status_cond=" and b.is_confirmed=$cbo_order_status";
	
	if(str_replace("'","",$cbo_company_name)==0) $cbo_company_name=""; else $cbo_company_name=" and a.company_name = $cbo_company_name";
	if(str_replace("'","",$cbo_buyer_name)==0) $cbo_buyer_name=""; else $cbo_buyer_name=" and a.buyer_name = $cbo_buyer_name";
	if(str_replace("'","",$cbo_team_member)==0) $cbo_team_member=""; else $cbo_team_member=" and a.dealing_marchant = $cbo_team_member";
	
	if(str_replace("'","",$cbo_search_type)==1){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
	}
	else if(str_replace("'","",$cbo_search_type)==3){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and c.country_ship_date between $txt_date_from and $txt_date_to";
	}
	else if(str_replace("'","",$cbo_search_type)==4){
		if($db_type==0)
		{
			if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)==""){$date_range="";}else{ 
			$date_range=" and b.insert_date between ".$txt_date_from." and ".$txt_date_to."";}
		}
		else
		{
			
			if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)==""){$date_range="";}else{ 
			$date_range=" and b.insert_date between ".$txt_date_from." and '".str_replace("'","",$txt_date_to)." 11:59:59 PM'";}
		}
	}
	else
	{
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.po_received_date between $txt_date_from and $txt_date_to";
	}
	
	

	
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if($txt_job_no=="") $txt_job_no=""; else $txt_job_no=" and a.job_no like('%$txt_job_no')";
	$txt_order_no=str_replace("'","",$txt_order_no);
	if($txt_order_no=="") $txt_order_no=""; else $txt_order_no=" and b.po_number ='$txt_order_no'";
	$txt_file_no=str_replace("'","",$txt_file_no);
	if($txt_file_no=="") $file_cond=""; else $file_cond=" and b.file_no ='$txt_file_no'";
	$txt_int_ref_no=str_replace("'","",$txt_int_ref_no);
	if($txt_int_ref_no=="") $ref_cond=""; else $ref_cond=" and b.grouping ='$txt_int_ref_no'";
	
	$txt_style_ref_no=str_replace("'","",$txt_style_ref_no);
	if($txt_style_ref_no=="") $txt_style_ref_no=""; else $txt_style_ref_no=" and a.style_ref_no ='$txt_style_ref_no'";
	//**txt_date_from*txt_date_to*txt_job_no
	
	//if(str_replace("'","",$cbo_shipment_status)==3)$shipment_status_con=" and b.shiping_status=$cbo_shipment_status"; else $shipment_status_con=" and b.shiping_status !=3";
	
	if(str_replace("'","",$cbo_shipment_status)==4){$shipment_status_con=" and b.shiping_status=3";}
	else if(str_replace("'","",$cbo_shipment_status)==1){$shipment_status_con=" and b.shiping_status !=3";}
	
	
	//echo $cbo_shipment_status;die;
	
	
	$tna_all_task=implode(",",$tna_task_id);
	
	if(str_replace("'","",$cbo_search_type)==3)
	{
		$sql = "SELECT a.team_leader,a.factory_marchant,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, b.id ,b.po_number, b.file_no, b.grouping as in_ref_no,b.po_quantity,b.PUB_SHIPMENT_DATE
		FROM  wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c 
		WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no  and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 $order_status_cond $file_cond $ref_cond";
	}
	else
	{
		$sql = "SELECT a.team_leader,a.factory_marchant,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, b.id, b.po_number, b.file_no, b.grouping as in_ref_no ,b.po_quantity,b.PUB_SHIPMENT_DATE
		FROM  wo_po_details_master a,  wo_po_break_down b 
		WHERE a.job_no=b.job_no_mst $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no  and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 $order_status_cond  $file_cond $ref_cond"; 
	}
	
    //echo $sql; 
	$result = sql_select( $sql ) ;
	$wo_po_details_master = array();
	$po_no_arr=array();
	$job_no_arr=array();
	foreach( $result as  $row ) 
	{	
		$wo_po_details_master[$row[csf('id')]]['company_name']=$row[csf('company_name')];
		$po_no_arr[$row[csf('id')]]=$row[csf('id')];
		$job_no_arr[$row[csf('job_no')]]=$row[csf('job_no')];
		
		//$wo_po_details_master[$row[csf('id')]]['dealing_marchant']=$row[csf('dealing_marchant')];
		//$wo_po_details_master[$row[csf('id')]]['factory_marchant']=$row[csf('factory_marchant')];
		//$wo_po_details_master[$row[csf('id')]]['team_leader']=$row[csf('team_leader')];

		$wo_po_details_master[$row[csf('job_no')]]['po_id'][$row[csf('id')]]= $row[csf('id')];

		
		$wo_po_details_master[$row[csf('job_no')]]['po_number'][$row[csf('id')]]= $row[csf('po_number')];
		$wo_po_details_master[$row[csf('job_no')]]['set_smv']= $row[csf('set_smv')];
		$wo_po_details_master[$row[csf('job_no')]]['file_no']= $row[csf('file_no')];

		$wo_po_details_master[$row[csf('job_no')]]['dealing_marchant']=$row[csf('dealing_marchant')];
		$wo_po_details_master[$row[csf('job_no')]]['factory_marchant']=$row[csf('factory_marchant')];
		$wo_po_details_master[$row[csf('job_no')]]['team_leader']=$row[csf('team_leader')];
		$wo_po_details_master[$row[csf('job_no')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
		$wo_po_details_master[$row[csf('job_no')]]['in_ref_no']= $row[csf('in_ref_no')];
		$wo_po_details_master[$row[csf('job_no')]]['buyer_name']=$row[csf('buyer_name')];
		$wo_po_details_master[$row[csf('job_no')]]['style_ref_no']=$row[csf('style_ref_no')];
		$wo_po_details_master[$row[csf('job_no')]]['po_quantity']+=$row[csf('po_quantity')];
	}
	
	
 
	$result = sql_select("select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name" ) ;
	$team_leader_name = array();
	foreach( $result as  $row ) 
	{	
		$team_leader_name[$row[csf('id')]]=$row[csf('team_leader_name')];
	}

 
	$sql = "SELECT team_member_name,id FROM lib_mkt_team_member_info WHERE is_deleted = 0 and status_active=1 order by id asc";
	$result = sql_select( $sql ) ;
	$team_member_name = array();
	foreach( $result as  $row ) 
	{	
		$team_member_name[$row[csf('id')]]=$row[csf('team_member_name')];
	}
	
	$sql = "SELECT buyer_name,id FROM  lib_buyer WHERE is_deleted = 0 and status_active=1 order by id asc";
	$result = sql_select( $sql ) ;
	$buyer_name = array();
	foreach( $result as  $row ) 
	{	
		$buyer_name[$row[csf('id')]]=$row[csf('buyer_name')];
	}
	
	
	//$po_no_arr_all=implode(",",$po_no_arr); if($po_no_arr_all!="") $po_no_arr_all .=",0"; else $po_no_arr_all .="0"; 
	//$job_no_all="'".implode("','",$job_no_arr)."'";
	$c=count($tna_task_id);
		$sql_order_con = where_con_using_array($po_no_arr,0,'a.po_number_id');
		$sql_job_con = where_con_using_array($job_no_arr,1,'a.job_no');
	
		$sql ="select a.job_no,a.task_number,  
		LISTAGG(cast(a.id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as id, 
		LISTAGG(cast(a.po_number_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as po_number_id, 
		LISTAGG(cast(a.template_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as template_id, 
		min(a.shipment_date) as shipment_date,
		min(b.pub_shipment_date) as pub_shipment_date,
		min(a.po_receive_date) as po_receive_date,
		min(b.insert_date) as insert_date,
	    (min(a.actual_start_date) || '_' || min(a.task_start_date) || '_' || min(a.actual_finish_date) || '_' || min(a.task_finish_date) || '_' || min(a.plan_start_flag) || '_' || min(a.plan_finish_flag) || '_' || min(a.notice_date_start) || '_' || min(a.notice_date_end) || '_' || LISTAGG(cast(a.id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) ) as status  
		
		from  tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id $sql_order_con $sql_job_con $shipment_status_con and b.status_active=1 and b.po_quantity>0 $order_status_cond  and a.task_type=1  group by a.job_no,a.task_number order by a.job_no"; 

		$data_sql_2= sql_select($sql);
	      // echo $sql;die;
		foreach($data_sql_2 as $row){
			$dataArr[$row[csf('job_no')]][$row[csf('task_number')]]=$row;
			$data_sql[$row[csf('job_no')]]=$row;
		}
		
 
		
	//booking...........................................start;	// 
		$sql_con_con = where_con_using_array($po_no_arr,0,'b.PO_BREAK_DOWN_ID');
		$sql_con="select a.JOB_NO,a.BOOKING_NO,b.PO_BREAK_DOWN_ID from wo_booking_mst a,WO_BOOKING_DTLS b where a.BOOKING_NO=b.BOOKING_NO  and a.is_short=2  and a.booking_type=1 and a.STATUS_ACTIVE=1  and a.IS_DELETED=0 and b.STATUS_ACTIVE=1  and b.IS_DELETED=0 and  b.PO_BREAK_DOWN_ID>0 and a.JOB_NO is not null  $sql_con_con
		group by a.JOB_NO, a.BOOKING_NO,b.PO_BREAK_DOWN_ID order by a.BOOKING_NO";

		$sql_booking_sql = sql_select($sql_con);
		$booking_no_arr=array();
		foreach( $sql_booking_sql as  $row ) 
		{	
			$booking_no_arr[$row['JOB_NO']][$row['PO_BREAK_DOWN_ID']][$row['BOOKING_NO']]=$row['BOOKING_NO'];
		}	
	//booking...........................................end;	
 
		
	//$data_sql= sql_select($sql);
	$width=(count($tna_task_id)*161)+1280;
	
	ob_start();
	
	?>
    
    
   <div style="margin:0 0%;">
        <span style="background:#FF0000; padding:0 6px; border-radius:9px; cursor:pointer;" title="Red">&nbsp;</span>&nbsp; Target Date Over. &nbsp;&nbsp;
        <span style="background:#2A9FFF; padding:0 6px; border-radius:9px; cursor:pointer;" title="Blue">&nbsp;</span>&nbsp; Done In Late. &nbsp;&nbsp;
        <span style="background:#FFFF00; padding:0 6px; border-radius:9px; cursor:pointer;" title="Yellow">&nbsp;</span>&nbsp; Reminder.
        
        <span style="background:#0000FF; padding:0 6px; border-radius:9px; cursor:pointer;" title="Royal Blue">&nbsp;</span>&nbsp; Manual Update Plan.
        
    </div>    
    <div style="width:<? echo $width+60; ?>px" align="left">
    <table width="<? echo $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
    	<thead>
        	<tr>
            	<th width="40" rowspan="2">SL</th>
                <th width="120" rowspan="2">Team Leader</th>
                <th width="120" rowspan="2">Dealing Merchant</th>
                <th width="120" rowspan="2">Factory Merchant</th>
                <th width="70" rowspan="2">Buyer Name</th>
                <th width="120" rowspan="2">Style Ref.</th> 
                <th width="40" rowspan="2">Job No.</th>
                <th width="110" rowspan="2">PO Number</th>
                <th width="90" rowspan="2">PO Qty.</th>
                <th width="40" rowspan="2">SMV</th>
                <th rowspan="2">Fab. Booking</th>
                <th width="100" rowspan="2">Shipment Date</th>
                <th width="80" rowspan="2">PO Insert Date</th>
                
                <th width="90" rowspan="2">Status</th>
                <?
					$i=0;
					foreach($tna_task_id as $task_name=>$key)
					{
						$i++;
						if(count($tna_task_id)==$i) echo '<th width="160" colspan="2" title="'.$task_name.'='.$tna_task_name[$task_name].'">'. $tna_task_array[$key].'</th>'; else echo '<th width="160" colspan="2" title="'.$task_name.'='.$tna_task_name[$task_name].'">'.$tna_task_array[$key].'</th>';
					}
					echo '</tr><tr>';
					$i=0;
					foreach($tna_task_id as $key)
					{
						$i++;
						if(count($tna_task_id)==$i){ 
						echo '<th width="80" title="Plan Start Date=Ship Date-(Deadline+Execution Days+1)">Start</th><th width="80" title="Plan Finish Date=(Ship Date-Deadline)"> Finish</th>';}else{
						echo '<th width="80" title="Plan Start Date=Ship Date-(Deadline+Execution Days+1)">Start</th><th width="80" title="Plan Finish Date=(Ship Date-Deadline)"> Finish</th>';}
					}
					echo '</tr>';
					 
				?>
                </thead>
                </table>
         </div>
         
        	<div style="overflow-y:scroll; max-height:360px; width:<? echo $width+20; ?>px;" align="left" id="scroll_body">
          	<table width="<? echo $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
       
    <?
	
	$tid=0;
	$i=1;
	$count=0;
	$kid=1;
	$new_job_no=array();
	$h=0;
	$tot_po_qty=0;
	
	foreach ($data_sql as $row)
	{


		 if($row[csf('po_number_id')]==0)
		 {
			 foreach($tna_task_id as $vid=>$key)
			 {
				if ($row[csf('status').$key]!="") $new_approval_arr[$row[csf('job_no')]][$key]=$row[csf('status').$key];
			 }
		 }
		 else
		 {
			 $h++;
			 if ($h%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
							
		?>
        		<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle; cursor:pointer;" height="25" onClick="change_color('tr_<? echo $h;?>','<? echo $bgcolor;?>')" id="tr_<? echo $h; ?>">
                    <td width="40" rowspan="3" align="center"><? echo $kid++;?></td>
                    <td width="120" rowspan="3">
						<? 
							echo $team_leader_name[$wo_po_details_master[$row[csf('job_no')]]['team_leader']];
                        ?>
                    </td>
                    <td width="120" rowspan="3">
						<? 
							echo $team_member_name[$wo_po_details_master[$row[csf('job_no')]]['dealing_marchant']];
                        ?>
                    </td>
                    <td width="120" rowspan="3">
						<? 
							echo $team_member_name[$wo_po_details_master[$row[csf('job_no')]]['factory_marchant']]; 
                        ?>
                    </td>
                    <td width="70" rowspan="3"><p><? echo $buyer_name[$wo_po_details_master[$row[csf('job_no')]]['buyer_name']]; ?></p></td>
                    <td width="120" rowspan="3" title="<? echo $row[csf('job_no')]; ?>"><p><? echo $wo_po_details_master[$row[csf('job_no')]]['style_ref_no']; ?></p></td>
                    
                     <td width="40" rowspan="3" align="center"><? echo $wo_po_details_master[$row[csf('job_no')]]['job_no_prefix_num']; ?></td>
                    <td width="110" rowspan="3" align="center"><p>
                    <div style="width:107px; word-break:break-all">
						<? 
							$tempArr=array();
							foreach(explode(',',$row[csf('po_number_id')]) as $poid){
								$tempArr[]= "<a href='#report_details' style='color:#990000' onclick= \"progress_comment_popup_style_first_ship('".$row[csf('job_no')]."','".$poid."','".$row[csf('template_id')]."','".$tna_process_type."');\">".$wo_po_details_master[$row[csf('job_no')]]['po_number'][$poid]."</a>";
							}
							echo implode(', ',$tempArr)
                        ?>
                   </div>
                   </p> </td>
                    
                    <td width="90" rowspan="3" align="right"><p>
						<?
							$po_qty=$wo_po_details_master[$row[csf('job_no')]]['po_quantity']; 
							echo number_format($po_qty);
							$tot_po_qty+=$po_qty;
						?>
                        </p>
                    </td>
                    <td width="40" rowspan="3" align="center"><? echo number_format($wo_po_details_master[$row[csf('job_no')]]['set_smv'],2); ?></td>
                    
                     <td rowspan="3"><?
					 $bookingArr=array();
					 foreach($wo_po_details_master[$row[csf('job_no')]]['po_id'] as $poId){ 
					 	$bookingArr[$poId]= implode(",",$booking_no_arr[$row[csf('job_no')]][$poId]);
					 }
					 echo implode(", ",array_unique(explode(',',implode(",",$bookingArr))));
					 ?></td>
                     <? 
					 	if($tna_process_type==1)
						{
							$lead_time_arr=array();
							foreach(explode(',',$row[csf('template_id')]) as $tmi){
								$lead_time_arr[$tmi]=$lead_time_array[$tmi];
							}
							$lead_time="Template Lead Time: ".implode(',',$lead_time_arr);
						}
						else
						{
							$lead_time="Lead Time: ".($row[csf('template_id')]+1);
						}
						$po_lead_time=datediff( "d", date("Y-m-d",strtotime(change_date_format($row[csf('po_receive_date')]))), date("Y-m-d",strtotime(change_date_format($row[csf('shipment_date')]))) );

					 ?>
                    <td width="100" rowspan="3" title="<? echo " PO. Rec. Date: ".change_date_format($row[csf('po_receive_date')]); echo ",\n Ship Date: ".$row[csf('shipment_date')] .",\n Insert Date: ".$row[csf('insert_date')];?>"><div style="width:98px; word-break:break-all">
					<? echo change_date_format($row[csf('pub_shipment_date')])."<br>"." ".$lead_time."<br>"." PO Lead Time:".($po_lead_time-1);  ?></div>
                    </td>
                    <td width="80" rowspan="3" align="center"><? echo date("d-m-Y",strtotime($row[csf('insert_date')]));?></td>
                    <td width="90">Plan</td>
                <?
					 $tast_id_arr=array_unique(explode(',',$tast_tmp_id_arr[$row[csf('template_id')]]));
					 $i=0;
					 foreach($tna_task_id as $vid=>$key)
					 { 
						 $i++;
						list($actual_start_date,$task_start_date,$actual_finish_date,$task_finish_date,$plan_start_flag,$plan_finish_flag,$notice_date_start,$notice_date_end,$mst_id)=explode("_",$dataArr[$row[csf('job_no')]][$key]['STATUS']);
						if($mst_id!="") $function="onclick='update_tna_process___(1,$mst_id,".$row[csf('po_number_id')].")'"; else $function="";
						if($plan_manual_update_task_arr[$vid]==''){$function="";}
						
						if($plan_start_flag==1){$psc=" style='color:#0000FF'";}else{$psc="";}
						if($plan_finish_flag==1){$pfc=" style='color:#0000FF'";}else{$pfc="";}
						
						
						if(count($tna_task_id)==$i)
							echo '<td align="center" '.$psc.'   width="80" '.$function.'>'.($task_start_date== "" || $task_start_date=="0000-00-00" ? "N/A" : change_date_format($task_start_date)).'</td><td align="center" '.$pfc.' '.$function.'> '.($task_finish_date== "N/A"  || $task_finish_date=="0000-00-00"? "" : change_date_format($task_finish_date)).'</td>';
						 else
							echo '<td align="center" '.$psc.'  width="80" '.$function.'>'.($task_start_date== "" || $task_start_date=="0000-00-00" ? "N/A" : change_date_format($task_start_date)).'</td><td align="center" '.$pfc.' width="80" '.$function.'> '.($task_finish_date== ""  || $task_finish_date=="0000-00-00"? "N/A" : change_date_format($task_finish_date)).'</td>';
					}
					echo '</tr>';
					
					echo '<tr style="cursor:pointer" onClick="change_color(\'actula_'.$h.'\',\''.$bgcolor.'\')" id="actula_'.$h.'"><td width="90">Actual</td>';
					$i=0;
					 foreach($tna_task_id as $vid=>$key)
					 { 
						$i++;
						list($actual_start_date,$task_start_date,$actual_finish_date,$task_finish_date,$plan_start_flag,$plan_finish_flag,$notice_date_start,$notice_date_end,$mst_id)=explode("_",$dataArr[$row[csf('job_no')]][$key]['STATUS']);
						$bgcolor1=""; $bgcolor="";
						if($actual_manual_update_task_arr[$vid]){
							$function="onclick=update_tna_process_style_first_ship(2,'".$mst_id."','".$row[csf('po_number_id')]."',$vid)";
						}
						else{
							 //$function="onclick=alert('No Permision')";
							 $function="onclick=alert('NoPermision')";
						}
						
						if (trim($task_start_date)!= $blank_date) 
						{
							if (strtotime($notice_date_start)<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($task_start_date)){$bgcolor="#FFFF00";}//Yellow
							else if (strtotime($task_start_date)<strtotime(date("Y-m-d",time()))){$bgcolor="#FF0000";}//Red
							else {$bgcolor="";}
							
						}
						 
						if ($task_finish_date!= $blank_date) {
							if (strtotime($notice_date_end)<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($task_finish_date)){$bgcolor1="#FFFF00";}//Yellow
							else if (strtotime($task_finish_date)<strtotime(date("Y-m-d",time()))){$bgcolor1="#FF0000";}//Red ;
							else{$bgcolor1="";}
						}
						
						if ($actual_start_date!=$blank_date) $bgcolor="";
						if ($actual_finish_date!=$blank_date) $bgcolor1="";
						$idd=$row[csf('job_no')]."".$row[csf('po_number_id')]."".$key;
						
						
						
						if(count($tna_task_id)==$i)
							echo '<td align="center" title="Click Here to Edit Date" id="'.$idd.'1" '.$function.' width="80" bgcolor="'.$bgcolor.'">'.($actual_start_date== "" || $actual_start_date=="0000-00-00" ? "" : change_date_format($actual_start_date)).'</td><td align="center" id="'.$idd.'2" title="Click Here to Edit Date" '.$function.' bgcolor="'.$bgcolor1.'" title="'.$actual_finish_date.'">'.($actual_finish_date== "" || $actual_finish_date=="0000-00-00" ? "" : change_date_format($actual_finish_date)).'</td>';
							
						else
							echo '<td align="center" id="'.$idd.'1" title="Click Here to Edit Date"  '.$function.' width="80" bgcolor="'.$bgcolor.'">'.($actual_start_date== "" || $actual_start_date=="0000-00-00" ? "" : change_date_format($actual_start_date)).'</td><td align="center" id="'.$idd.'2" title="Click Here to Edit Date" '.$function.' width="80" bgcolor="'.$bgcolor1.'" title="">'.($actual_finish_date== "" || $actual_finish_date=="0000-00-00" ? "" : change_date_format($actual_finish_date)).'</td>';
					 }
					echo '</tr>'; 
					
					echo '<tr style="cursor:pointer" onClick="change_color(\'delay_'.$h.'\',\''.$bgcolor.'\')" id="delay_'.$h.'"><td width="90">Delay/Early By</td>';
					$j=0;
					foreach($tna_task_id as $vid=>$key)
					{
						
						list($actual_start_date,$task_start_date,$actual_finish_date,$task_finish_date)=explode("_",$dataArr[$row[csf('job_no')]][$key]['STATUS']);
						
						
						$j++;
						$bgcolor1=""; $bgcolor="";
						if($actual_start_date!=$blank_date)
						{
							$start_diff1 = datediff( "d", $actual_start_date, $task_start_date);
							if($actual_start_date== ""){ $start_diff=$start_diff1;}
							else{$start_diff=$start_diff1-1;}
							
							if($start_diff<0){$bgcolor="#2A9FFF";} //Blue
							if($start_diff>0){$bgcolor="";}
						}
						else
						{
							if(strtotime(date("Y-m-d"))>strtotime($actual_start_date))
							{
								$start_diff1 = datediff( "d", $actual_start_date, date("Y-m-d"));
								if( $actual_start_date== "")
								{
									$start_diff=-abs($start_diff1-1);
								}
								else
								{
									$start_diff=-abs($start_diff1-1);
								}
								$bgcolor=($actual_start_date== "" || $actual_start_date=="0000-00-00")?'':'#FF0000';
							}
							if(strtotime(date("Y-m-d"))<=strtotime($actual_start_date))
							{
								$start_diff = "";
								$bgcolor="";
							}
						}
						if($actual_finish_date!=$blank_date)
						{
							$finish_diff1 = datediff( "d", $actual_finish_date, $task_finish_date);
							if($actual_start_date== "")
							{
								$finish_diff=$finish_diff1;
							}
							else
							{	
								$finish_diff=$finish_diff1-1;
							}
							if($finish_diff<0)
							{
								$bgcolor1="#2A9FFF";
							}
							if($finish_diff>0)
							{	
								$bgcolor1="";
							}
						}
						else
						{
							if(strtotime(date("Y-m-d"))>strtotime($task_finish_date))
							{
								
								$finish_diff1 = datediff( "d", $task_finish_date, date("Y-m-d"));
								if($actual_finish_date== "")
								{
									$finish_diff=-abs($finish_diff1-1);
								}
								else
								{
									$finish_diff=-abs($finish_diff1-1);
								}
								//$bgcolor1="#FF0000";
								$bgcolor1=($task_finish_date== "" || $task_finish_date=="0000-00-00")?'':'#FF0000';
							}
							if(strtotime(date("Y-m-d"))<=strtotime($task_finish_date))
							{
								
								$finish_diff = "";
								$bgcolor1="";
							}
						}
						
						
						
						if(count($tna_task_id)==$j)
							
							echo '<td width="80" align="center" bgcolor="'.$bgcolor.'">'.($start_diff).'</td><td width="80" bgcolor="'.$bgcolor1.'" align="center">'.($finish_diff).'</td>';
						else
							echo '<td width="80" align="center" bgcolor="'.$bgcolor.'">'.($start_diff).'</td><td width="80" bgcolor="'.$bgcolor1.'" align="center">'.($finish_diff).'</td>';
					}
					 
					echo '</tr>';
					
					
					 
		 }
				 
	
	
	
	
	}
		?>
     
     
    </table>
    </div>
    <div style="width:<? echo $width+20; ?>px;" align="left">
         <table width="<? echo $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <tfoot>
                <th width="40"></th>
                <th width="120"></th>
                <th width="120"></th>
                <th width="120"></th>
                <th width="70"></th>
                <th width="120"></th>
                <th width="40"></th>
                <th width="110">Total</th>
                <th width="90" id="total_po_qty_" align="right"><p><? echo number_format($tot_po_qty,2);?></p></th>
                <th width="40"></th>
                <th colspan="<? echo (count($tna_task_id)*2)+4;?>"></th>
            </tfoot>
        </table>
    </div>
    
    
          <?
		  
		 $sql = sql_select("select designation,name from variable_settings_signature where report_id=95 and company_id=$cbo_company_id order by sequence_no" );
	     $count=count($sql);

		$width=$width+170;
		$td_width=floor($width/$count);
		
		$standard_width=$count*150;
		
		if($standard_width>$width) $td_width=150;
		
		$no_coloumn_per_tr=floor($width/$td_width);
		$col=$count-2;
		$i=1;
		echo '<table width="'.$width.'"><tr><td width="'.$td_width.'" align="center" valign="bottom">'.$user_arr[$inserted_by].'</td><td height="70" colspan="'.$col.'"></td><td width="'.$td_width.'" align="center" valign="bottom">'.$user_arr[$nameArray_approved_date_row[csf('approved_by')]].'</td></tr><tr>';
		foreach($sql as $row)	
		{
			echo '<td width="'.$td_width.'" align="center" valign="top"><strong style="text-decoration:overline">'.$row[csf("designation")]."</strong><br>".$row[csf("name")].'</td>';
			
			if($i%$no_coloumn_per_tr==0) echo '</tr><tr><td height="70" colspan="'.$no_coloumn_per_tr.'"></td><tr>';
			$i++;
		} 
		echo '</tr></table>';



	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
 	echo "$total_datass****$filename";
	exit();
}



if($action=="po_update_history")
{
	echo load_html_head_contents("TNA","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );	
	$user_arr=return_library_array( "select id, USER_NAME from USER_PASSWD",'id','USER_NAME');

	$sql_data=sql_select("select b.PUB_SHIPMENT_DATE from wo_po_break_down b where b.po_id in($po_id)");
	foreach( $sql_data as $row)
	{
		$poDataArr[$row[csf('PUB_SHIPMENT_DATE')]]=$row[csf('PUB_SHIPMENT_DATE')];
	}


	$sql_log=sql_select( "select id,job_no,po_id,order_status,po_no,po_received_date,shipment_date,org_ship_date,fac_receive_date,previous_po_qty,avg_price,excess_cut_parcent,plan_cut,status,projected_po,packing,remarks,file_no,update_date,update_by from wo_po_update_log where po_id in($po_id) order by id DESC");
	
	foreach($sql_log as $rows)
	{
		if($poDataArr[$rows[csf('shipment_date')]]=='' ){
			$key=$rows[csf('po_id')].$rows[csf('id')];
			$log_array[$key]['job_no']=$rows[csf('job_no')];
			$log_array[$key]['po_no']=$rows[csf('po_no')];
			$log_array[$key]['shipment_date']=$rows[csf('shipment_date')];
			$log_array[$key]['update_date']=$rows[csf('update_date')];
			$log_array[$key]['update_by']=$rows[csf('update_by')];
		}
	}
	   


	?> 
   
      
	</head>
	<body>
		<div style="width: 635px;font-size:12px;">
		
		<table width="630" border="1" rules="all" class="rpt_table">
			<thead>
				<th>Job NO</th>
				<th>Po Number</th>
				<th>Prev. Pub Ship Date</th>
				<th>Changed By</th>
				<th>Changed Date</th>
			</thead>
			<tbody>
				<?php
				$i=0;
				foreach($log_array as $row)
				{
				$i++;
				$trcolor=($i%2==0)?"#E9F3FF":"#FFFFFF"; 	
					
				?>
				<tr bgcolor="<? echo $trcolor; ?>">
					<td><? echo $row[job_no]; ?></td>
					<td><? echo $row[po_no]; ?></td>
					<td align="center"><? echo change_date_format($row[shipment_date]); ?></td>
					<td><? echo $user_arr[$row[update_by]]; ?></td>
					<td><? echo change_date_format($row[update_date]); ?></td>
				</tr>
				<?
				}
				?>
			</tbody>
		</table>
		</div>
		
	</body>           
	</html>
  
  
  <?
			
}

if($action=="update_tna_progress_comment_style_first_ship")
{
	if($db_type==0) $blank_date="0000-00-00"; else $blank_date=""; 	
	
	echo load_html_head_contents("TNA","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	
	$po_id=implode(',',array_unique(explode(',',$po_id)));
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$tna_task_arr=return_library_array( "select task_name, task_short_name from lib_tna_task where task_type = 1",'task_name','task_short_name');
	$lead_time_array=return_library_array("select task_template_id,lead_time from tna_task_template_details where task_type=1 and is_deleted=0 and status_active=1 group by lead_time,task_template_id","task_template_id",'lead_time');

	$sql ="select b.company_name,b.buyer_name,b.job_no,b.style_ref_no,b.gmts_item_id, set_smv, 
	LISTAGG(cast(a.po_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_number,
	LISTAGG(cast(a.po_received_date as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_received_date,
	LISTAGG(cast(a.shipment_date as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as shipment_date,
	SUM(po_quantity*total_set_qnty) as po_qty_pcs from  wo_po_break_down a,wo_po_details_master b where a.id in($po_id) and a.job_no_mst=b.job_no group by b.company_name,b.buyer_name,b.job_no,b.style_ref_no,b.gmts_item_id, set_smv";
	$result=sql_select($sql);
	//echo $sql;die;
	
	$tna_task_id=array();
	$plan_start_array=array();
	$plan_finish_array=array();
	$actual_start_array=array();
	$actual_finish_array=array();
	$notice_start_array=array();
	$notice_finish_array=array();
	
	/*	 $task_sql=sql_select("select a.task_number,a.task_start_date,a.task_finish_date,a.actual_start_date,a.actual_finish_date,a.notice_date_start,a.notice_date_end from tna_process_mst a, lib_tna_task b where a.task_type=1 and a.task_number=b.task_name and a.po_number_id in($po_id) and b.status_active=1 and b.is_deleted=0 order by b.task_sequence_no asc");*/
	
	 
	 
	 $task_sql=sql_select("select b.TASK_SEQUENCE_NO,a.task_number,min(a.task_start_date) as  task_start_date ,min(a.task_finish_date) as task_finish_date,min(a.actual_start_date) as actual_start_date,min(a.actual_finish_date) as actual_finish_date,min(a.notice_date_start) as notice_date_start,min(a.notice_date_end) as notice_date_end from tna_process_mst a, lib_tna_task b where a.task_type=1  AND a.task_type = 1 and a.task_number=b.task_name and a.po_number_id in($po_id)  and job_no='$job_no' and b.status_active=1 and b.is_deleted=0 group by b.TASK_SEQUENCE_NO,a.task_number order by b.TASK_SEQUENCE_NO asc");

	
	
	foreach ($task_sql as $row_task)
	{
		$tna_task_id[$row_task[csf("task_number")]]=$row_task[csf("task_number")];
		
		$plan_start_array[$row_task[csf("task_number")]] =$row_task[csf("task_start_date")];
		$plan_finish_array[$row_task[csf("task_number")]]=$row_task[csf("task_finish_date")];
		
		$actual_start_array[$row_task[csf("task_number")]] =$row_task[csf("actual_start_date")];
		$actual_finish_array[$row_task[csf("task_number")]]=$row_task[csf("actual_finish_date")];
		
		$notice_start_array[$row_task[csf("task_number")]] =$row_task[csf("notice_date_start")];
		$notice_finish_array[$row_task[csf("task_number")]]=$row_task[csf("notice_date_end")];
	} //var_dump($tna_task_id);die;
	
	//-----------------------------------------------------------------------------------------------
	
	//$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$color=return_library_array( "select a.po_break_down_id ,b.color_name, a.color_number_id from wo_po_color_size_breakdown a, lib_color b where a.color_number_id=b.id and a.po_break_down_id in($po_id) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.po_break_down_id ,b.color_name, a.color_number_id order by b.color_name", "color_number_id", "color_name" );
		
	
	
	$mer_comments_array=array();
			
			$data_array1=sql_select("select a.job_no_mst,b.color_mst_id, b.color_number_id from  wo_po_break_down a, wo_po_color_size_breakdown b, wo_po_sample_approval_info c where a.job_no_mst=b.job_no_mst and b.job_no_mst=c.job_no_mst and a.id=b.po_break_down_id and b.po_break_down_id=c.po_break_down_id and  b.id=c.color_number_id  and a.id in($po_id) and b.color_mst_id !=0  and c.sample_type_id =7  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by a.id,b.id,c.current_status");   //group by c.id 
			
			
			
			if (count($data_array1)<=0)
			{
			$data_array1=sql_select("select b.color_mst_id, b.color_number_id from  wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.id in($po_id) and b.color_mst_id !=0 and a.id=b.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,a.po_number,b.color_mst_id, b.color_number_id order by a.id");
			}
			
			foreach ( $data_array1 as $row1)
			{
				//$total_color[$row1[csf('color_number_id')]]=$row1[csf('color_number_id')];
			
			//sample app.................................................................start
			$data_array_sample_table=sql_select("Select a.color_number_id,a.approval_status,a.sample_comments,b.sample_type from wo_po_sample_approval_info a,lib_sample b where a.sample_type_id=b.id and a.po_break_down_id in($po_id) and a.color_number_id ='".$row1[csf('color_mst_id')]."'");
				foreach ( $data_array_sample_table as $smp_row)
				{ 
					if ($smp_row[csf("sample_type")]==2) {
							if($smp_row[csf('approval_status')]==1){$smp_data[8].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
							if($smp_row[csf('approval_status')]==3){$smp_data[12].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
						 }
					else if ($smp_row[csf("sample_type")]==3) {
							if($smp_row[csf('approval_status')]==1){$smp_data[7].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
							if($smp_row[csf('approval_status')]==3){$smp_data[13].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
					}
					else if ($smp_row[csf("sample_type")]==4) {
							if($smp_row[csf('approval_status')]==1){$smp_data[14].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
							if($smp_row[csf('approval_status')]==3){$smp_data[15].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
					}
					else if ($smp_row[csf("sample_type")]==7) {
							if($smp_row[csf('approval_status')]==1){$smp_data[16].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
							if($smp_row[csf('approval_status')]==3){$smp_data[17].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
					}
					else if ($smp_row[csf("sample_type")]==8) { 
							if($smp_row[csf('approval_status')]==1){$smp_data[21].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
							if($smp_row[csf('approval_status')]==3){$smp_data[22].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
					}
					else if ($smp_row[csf("sample_type")]==9) {
							if($smp_row[csf('approval_status')]==1){$smp_data[23].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
							if($smp_row[csf('approval_status')]==3){$smp_data[24].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
					}

				}
			//sample app.................................................................end


			//lapdip app..................................................................start	
			$data_array_sample_table=sql_select("Select color_name_id,approval_status,lapdip_comments from wo_po_lapdip_approval_info where  po_break_down_id in($po_id)");
				foreach ( $data_array_sample_table as $smp_row)
				{ 
					if($smp_row[csf('approval_status')]==1){$smp_data[9].=$color[$smp_row[csf('color_name_id')]].': '.$smp_row[csf('lapdip_comments')].',';}
					if($smp_row[csf('approval_status')]==3){$smp_data[10].=$color[$smp_row[csf('color_name_id')]].': '.$smp_row[csf('lapdip_comments')].',';}
				
				}
		//lapdip app.........................................................end	
		
		
		//embell app..........................................................start	
			$data_array_sample_table=sql_select("Select color_name_id,approval_status,embellishment_comments from wo_po_embell_approval where po_break_down_id in($po_id)");
				foreach ( $data_array_sample_table as $smp_row)
				{ 
					if($smp_row[csf('approval_status')]==1){$smp_data[19].=$color[$smp_row[csf('color_name_id')]].': '.$smp_row[csf('embellishment_comments')].',';}
					if($smp_row[csf('approval_status')]==3){$smp_data[20].=$color[$smp_row[csf('color_name_id')]].': '.$smp_row[csf('embellishment_comments')].',';}
				
				}
		//embell app..........................................................end	


		//Trims app..........................................................start	

			$data_array_sample_table=sql_select("Select approval_status,accessories_comments from wo_po_trims_approval_info where po_break_down_id='".$po_id."'");
				foreach ( $data_array_sample_table as $smp_row)
				{ 
					if($smp_row[csf('approval_status')]==1){$smp_data[25].=$smp_row[csf('accessories_comments')].',';}
					if($smp_row[csf('approval_status')]==3){$smp_data[11].=$smp_row[csf('accessories_comments')].',';}
				}


					
				
			}
	//----------------------------------------------------------------------------------------


	
	
	
	$comments_array=array();
	$responsible_array=array();
	
	//$res_comm_sql= sql_select("select task_id, comments, responsible from tna_progress_comments where tamplate_id='$template_id' and order_id='$po_id'");
	//echo "select task_id, comments, responsible from tna_progress_comments where order_id='$po_id'";
	$res_comm_sql=sql_select("select task_id, comments, responsible,mer_comments from tna_progress_comments where order_id in($po_id)");
	
	foreach ($res_comm_sql as $row_res_comm)
	{
		$comments_array[$row_res_comm[csf("task_id")]] =$row_res_comm[csf("comments")];
		$mer_comments_array[$row_res_comm[csf("task_id")]] =$row_res_comm[csf("mer_comments")];
		$responsible_array[$row_res_comm[csf("task_id")]]=$row_res_comm[csf("responsible")];
	}
	
	
	$execution_time_array=array();
	
	//$execution_time_sql= sql_select("select tna_task_id, execution_days from tna_task_template_details where task_template_id='$template_id'");
	
	$execution_time_sql= sql_select("select for_specific, tna_task_id, execution_days from tna_task_template_details where task_type=1 and is_deleted=0 and status_active=1");
	foreach ($execution_time_sql as $row_execution_time)
	{
		$execution_time_array[$row_execution_time[csf("for_specific")]][$row_execution_time[csf("tna_task_id")]]=$row_execution_time[csf("execution_days")];
	}
	
	//$upid_sql= sql_select("select min(id) as id from tna_progress_comments where tamplate_id='$template_id' and order_id='$po_id'");
	
	$upid_sql= sql_select("select min(id) as id from tna_progress_comments where order_id in($po_id)");
	foreach ($upid_sql as $row_upid)
	{
		$id_up=$row_upid[csf("id")];
	}
	
	$lead_time=return_library_array("select task_template_id,lead_time from tna_task_template_details where task_type=1 and is_deleted=0 and status_active=1 group by task_template_id,lead_time","task_template_id","lead_time");
		


	$booking_no=return_field_value("booking_no","wo_booking_dtls","po_break_down_id in(".$po_id.") and BOOKING_TYPE=1 and status_active=1 and is_deleted=0","booking_no");

		$imbillishment_cost=return_field_value("rate","wo_pre_cost_embe_cost_dtls","job_no='".$result[0][csf('job_no')]."' and status_active=1 and is_deleted=0","rate");
		$is_imblishment=$imbillishment_cost?"Yes":"No";

		
		$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst where job_no='".$result[0][csf('job_no')]."'","job_no","costing_per"); 
		$set_item_ratio_arr = return_library_array("select gmts_item_id, set_item_ratio from wo_po_details_mas_set_details where job_no='".$result[0][csf('job_no')]."'","gmts_item_id","set_item_ratio"); 
		
	 
	 $sql_po_qty_fab_data=sql_select("select sum(c.plan_cut_qnty) as order_quantity,c.item_number_id,c.size_number_id,c.color_number_id  from  wo_po_color_size_breakdown c where  c.po_break_down_id in(".$po_id.") and c.status_active=1  group by c.item_number_id,c.size_number_id,c.color_number_id");
	 foreach($sql_po_qty_fab_data as $row){
		$key=$row[csf(item_number_id)].$row[csf(size_number_id)].$row[csf(color_number_id)];
		$sql_po_qty_fab_arr[$key]+=$row[csf(order_quantity)]; 
	 }
	
	$sql = "select id, job_no,item_number_id, body_part_id, fab_nature_id, color_type_id, fabric_description, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active from wo_pre_cost_fabric_cost_dtls where job_no='".$result[0][csf('job_no')]."' and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
		
	$req_qty=0;
	foreach( $data_array as $row )
    {
		
		$set_item_ratio=$set_item_ratio_arr[$row[csf('item_number_id')]];
		
		$fab_dtls_data=sql_select("select po_break_down_id,color_number_id,gmts_sizes,cons,requirment from wo_pre_cos_fab_co_avg_con_dtls where pre_cost_fabric_cost_dtls_id=".$row[csf("id")]." and po_break_down_id in(".$po_id.") and cons !=0 ");
	   
		foreach($fab_dtls_data as $fab_dtls_data_row )
		{
			$dzn_qnty=0;
			if($costing_per_arr[$result[0][csf('job_no')]]==1) $dzn_qnty=12;
			else if($costing_per_arr[$result[0][csf('job_no')]]==3) $dzn_qnty=12*2;
			else if($costing_per_arr[$result[0][csf('job_no')]]==4) $dzn_qnty=12*3;
			else if($costing_per_arr[$result[0][csf('job_no')]]==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
			
			$key=$result[0][csf('gmts_item_id')].$fab_dtls_data_row[csf('gmts_sizes')].$fab_dtls_data_row[csf('color_number_id')];
			$po_qty_fab=$sql_po_qty_fab_arr[$key]; 
			$req_qty+=($po_qty_fab/($dzn_qnty*$set_item_ratio))*$fab_dtls_data_row[csf("cons")];
		}
	}
	///////////////////////////////////////////////////////////////////// 
	   
	   
	   

	?> 
   
    <script>
	 
		var permission='<? echo $permission; ?>';
		//var refresh_data="";
	
		function fnc_progress_comments_entry(operation)
		{
			var tot_row=$('#comments_tbl tbody tr').length;
			var data_all=''; var j=0;
			for(i=1; i<=tot_row; i++)
			{
				if (form_validation('taskid_'+i,'Task Number')==false )
				{
					alert("Task Number Not Found, Please Click On PO Number");
					return;
				}
				
				var responsible=$("#txtresponsible_"+i).val();
				var comments=$("#txtcomments_"+i).val();
				var mrc_comments=$("#txtmercomments_"+i).val();
				var taskid=$("#taskid_"+i).val();
				if (comments!="" || mrc_comments!="" || responsible!="")
				{
					j++;
					data_all+=get_submitted_data_string('txtresponsible_'+i+'*txtcomments_'+i+'*txtmercomments_'+i+'*taskid_'+i,"../../../",i);
				}
			}
			if(data_all=='')
			{
				alert("No Comments Found");	
				return;
			}
			var data="action=save_update_delete_progress_comments_style&operation="+operation+get_submitted_data_string('jobno*orderid*tamplateid',"../../../")+data_all+'&tot_row='+tot_row;
			freeze_window(operation);
			http.open("POST","tna_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fnc_progress_comments_Reply_info;
		}
		
		function fnc_progress_comments_Reply_info()
		{
			if(http.readyState == 4) 
			{
				var reponse=trim(http.responseText).split('**');	
				show_msg(reponse[0]);
				var path_link=$('#txt_file_link_ref').val();
				$.post('tna_report_controller.php?job_no='+'<? echo $job_no; ?>'+'&po_id='+<? echo $po_id; ?>+'&template_id='+<? echo $template_id; ?>+'&tna_process_type='+<? echo $tna_process_type; ?>,
				{ 
					path: '', action: "generate_report_file", filename: path_link },
					function(data)
					{
						$('#txt_file_link_ref').val(data);
					}
				);
					set_button_status(1, permission, 'fnc_progress_comments_entry',3);
				release_freezing();	
			}
		}
		function autoRefresh_div()
		 {
			  $("#auto_id").load("tna_report_controller.php");
		  }
		
		function openmypage(i)
		{	
			var title = 'TNA Progress Comment';
			var txtcomments = document.getElementById(i).value;
			var page_link = 'tna_report_controller.php?data='+txtcomments+'&action=comments_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=160px,center=1,resize=1,scrolling=0','../../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var additional_infos=this.contentDoc.getElementById("additional_infos").value;
				document.getElementById(i).value=additional_infos;
			}
		}
		
		function new_window()
		{
			var url = 'tna_report_controller.php?job_no=<? echo $job_no;?>&po_id=<? echo $po_id;?>&template_id=<? echo $template_id;?>&tna_process_type=<? echo $tna_process_type;?>&action=tna_progress_comment_print&permission=<? echo $permission;?>';
			window.open(url, "MY PAGE");
		}
		
		function new_excel()
		{
			var url = 'tna_report_controller.php?job_no=<? echo $job_no;?>&po_id=<? echo $po_id;?>&template_id=<? echo $template_id;?>&tna_process_type=<? echo $tna_process_type;?>&action=tna_progress_comment_print&permission=<? echo $permission;?>&isExcel=1';
			window.open(url, "MY PAGE");
		}
			
	
	</script>
   
  
		
   
	</head>
	<body onLoad="set_hotkey()">
	<div id="messagebox_main"></div>
	<div align="center" style="width:100%;">
    <? 
		echo load_freeze_divs ("../../../",'',1); 
		ob_start();
	?>
    
    <form name="tnaprocesscomments_3" id="tnaprocesscomments_3" autocomplete="off" >
    
    <div align="center" style="width:100%" id="details_reports">
    
     <table width="1000" border="1" rules="all" class="rpt_table">
    	<tr><td colspan="6" align="center"><b><font size="+1">TNA Progress Comment</font></b></td></tr>
    </table>
    
    <table width="1000" border="1" rules="all" class="rpt_table">
    	<?php $buyer_id="";
		foreach($result as $row)
		{
			$buyer_id=$row[csf('buyer_name')];
		?>
    	<tr>
        	<td width="130">Company</td>
            <td width="176"><?php  echo $company_library[$row[csf('company_name')]];  ?></td>
            <td width="130">Buyer</td>
            <td width="176"><?php  echo $buyer_arr[$row[csf('buyer_name')]];  ?></td>
            <td width="130">Job Number</td>
           	<td width="176">
            	<? echo $row[csf('job_no')];   ?>
            	<Input type="hidden" name="jobno" class="text_boxes" ID="jobno" value="<? echo $job_no; ?>" style="width:100px" />
            	<Input type="hidden" name="orderid" class="text_boxes" ID="orderid" value="<? echo $po_id; ?>" style="width:100px" />
                <Input type="hidden" name="tamplateid" class="text_boxes" ID="tamplateid" value="<? echo $template_id; ?>" style="width:100px" />
            </td>
        </tr>
        <tr>
            <td>Order No</td>
           	<td><b><?php  echo $row[csf('po_number')]; ?></b></td>
            <td>Style Ref.</td>
            <td><?php  echo $row[csf('style_ref_no')];  ?></td>
            <td>Booking Number</td>
            <td><?php echo $booking_no; ?></td>
        </tr>
        <tr>
            <td>Garments Item</td>
            <td><?php  echo $garments_item[$row[csf('gmts_item_id')]];  ?></td>
            <td>Embellishment</td>
            <td><b><?php echo $is_imblishment;  ?></b></td>
            <td>SMV</td>
            <td><b><?php echo number_format($row[csf('set_smv')],2); ?></b></td>
        </tr>
        <tr>
            <td>Order Recv. Date</td>
           	<td><?php  
			foreach(explode(',',$row[csf('po_received_date')]) as $po_received_date){
				$po_received_date_arr[$po_received_date]=change_date_format($po_received_date);
			}
				echo implode(',',$po_received_date_arr);
			?></td>
        	<td>Ship Date</td>
            <td><b><?php  
			foreach(explode(',',$row[csf('shipment_date')]) as $shipment_date_date){
				$shipment_date_date_arr[$shipment_date_date]=change_date_format($shipment_date_date);
			}
				echo implode(',',$shipment_date_date_arr);
			?></b></td>
            <td>Template/Style Lead Time</td>
            <td><b>
				<? 
					$lead_time_arr=array();
					if($tna_process_type==1)
					{
						foreach(array_unique(explode(',',$template_id)) as $tplid){
							$lead_time_arr[$tplid]=$lead_time_array[$tplid];
						}
					}
					else
					{
						$lead_time_arr[$template_id]=$template_id+1;
					}

					echo implode(', ',$lead_time_arr);
					
					echo " / ".datediff('d',min($po_received_date_arr),min($shipment_date_date_arr));
                ?>
            </b></td>
        </tr>
        <tr>
            <td>Quantity (PCS)</td>
            <td><b><?php echo $row[csf('po_qty_pcs')];?></b></td>
            <td>Finish Req. (KG)</td>
            <td><b><?php echo number_format($req_qty,2); ?></b></td>
            <td>Number of Color</td>
            <td><b><?php echo count($color);  ?></b></td>
        </tr>
        <?php
		}
		?>
    </table>
    
    <table><tr height="10"><td colspan="6">&nbsp;</td></tr></table>
    
    <table style="width: 1130px;">
        <tr>
            <td>
                <div style="width: 1120px;font-size:12px;">
                <table width="1100" border="1" rules="all" class="rpt_table">
                    <thead>
                    	<tr align="center">
                            <th width="30">Task No</th>
                            <th width="150">Task Name</th>
                            <th width="60">Allowed Days</th>
                            <th width="70">Plan Start Date</th>
                            <th width="70">Plan Finish Date</th>
                            <th width="70">Actual Start Date</th>
                            <th width="70">Actual Finish Date</th>
                            <th width="70">Start Delay/ Early By</th>
                            <th width="70">Finish Delay/ Early By</th>
                            <th width="150">Responsible</th>
                            <th width="120">Comments</th>
                            <th width="">Mer. Comments</th>
                        </tr>
                    </thead>
                </table>
                </div>
            </td>
        </tr>
    </table> 
    <table style="width:1130px;">
        <tr>
            <td>    
                <div style="width: 1120px;overflow-y: scroll; max-height:180px;font-size:12px;" id="scroll_body2">
                <table width="1100px" border="1" rules="all" class="rpt_table" id="comments_tbl">
                	<tbody>
						<?php
                        $i=0;
                        foreach($tna_task_id as $key)
                        {
                            $i++;
                           $trcolor=($i%2==0)?"#E9F3FF":"#FFFFFF"; 	
						
							$bgcolor1=""; $bgcolor="";
									
							if ($plan_start_array[$key]!=$blank_date) 
							{
								if (strtotime($notice_start_array[$key])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($plan_start_array[$key]))  $bgcolor="#FFFF00";
								else if (strtotime($plan_start_array[$key])<strtotime(date("Y-m-d",time())))  $bgcolor="#FF0000";
								else $bgcolor="";
								
							}
							 
							if ($plan_finish_array[$key]!=$blank_date) {
								if (strtotime($notice_finish_array[$key])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($plan_finish_array[$key]))  $bgcolor1="#FFFF00";
								else if (strtotime($plan_finish_array[$key])<strtotime(date("Y-m-d",time())))  $bgcolor1="#FF0000"; else $bgcolor1="";
							}
							
							if ($actual_start_array[$key]!=$blank_date) $bgcolor="";
							if ($actual_finish_array[$key]!=$blank_date) $bgcolor1="";
							// Delay / Early............
							$bgcolor5=""; $bgcolor6="";
							$delay=""; $early="";
							
							if($actual_start_array[$key]!=$blank_date)
							{
								$start_diff1 = datediff( "d", $actual_start_array[$key], $plan_start_array[$key]);
								if($actual_finish_array[$key]=="" || $actual_finish_array[$key]=="0000-00-00"){
									
									$finish_diff1 = datediff( "d",date("Y-m-d"), $plan_finish_array[$key]);
								}
								else
								{
									$finish_diff1 = datediff( "d", $actual_finish_array[$key], $plan_finish_array[$key]);	
								}
								$start_diff=$start_diff1-1;
								$finish_diff=$finish_diff1-1;
								
								if($start_diff<0)
								{
									$bgcolor5="#2A9FFF";	//Blue	
									$start="(Delay)";
								}
								if($start_diff>0)
								{
									$bgcolor5="";
									$start="(Early)";
								}
								if($finish_diff<0)
								{
									if($actual_finish_array[$key]=="" || $actual_finish_array[$key]=="0000-00-00"){
										$bgcolor6="#FF0000";//Blue
									}
									else
									{
										$bgcolor6="#2A9FFF";//Blue
									}
									$finish="(Delay)";
								}
								if($finish_diff>0)
								{	
									$bgcolor6="";
									$finish="(Early)";
								}
							}
							else
							{
								if(date("Y-m-d")> date("Y-m-d",strtotime($plan_start_array[$key])))
								{
									$start_diff1 = datediff( "d", $plan_start_array[$key], date("Y-m-d"));
									$start_diff=$start_diff1-1;
									$bgcolor5="#FF0000";		//Red
									$start="(Delay)";
								}
								if(date("Y-m-d")> date("Y-m-d",strtotime($plan_finish_array[$key])))
								{
									$finish_diff1 = datediff( "d", $plan_finish_array[$key], date("Y-m-d"));
									$finish_diff=$finish_diff1-1;
									$bgcolor6="#FF0000";
									$finish="(Delay)";
								}
								if(date("Y-m-d")<= date("Y-m-d",strtotime($plan_start_array[$key])))
								{
									$start_diff = "";
									$bgcolor5="";
									$start="";
								}
								if(date("Y-m-d")<= date("Y-m-d",strtotime($plan_finish_array[$key])))
								{
									$finish_diff = "";
									$bgcolor6="";
									$finish="";
								}
							}
                        ?>
                        <tr bgcolor="<? echo $trcolor; ?>">
                            <td align="center" width="30"><? echo $i; ?></td>
                            <td width="150"> <? echo $tna_task_arr[$key]; ?></td>
                            <td align="center" width="60"><? echo datediff( "d", $plan_start_array[$key],$plan_finish_array[$key]); ?></td>
                            <td align="center" width="70"><? echo  change_date_format($plan_start_array[$key]); ?></td>
                            <td align="center" width="70"><? echo  change_date_format($plan_finish_array[$key]); ?></td>
                            <td align="center" width="70" bgcolor="<? echo $bgcolor;  ?>">
								<?
                                    if($db_type==0)
                                    {
                                        if($actual_start_array[$key]=="0000-00-00") echo "";
                                        else echo  change_date_format($actual_start_array[$key]);
                                    }
                                    else
                                    {
                                        if($actual_start_array[$key]=="") echo "";
                                        else echo  change_date_format($actual_start_array[$key]);
                                    }
                                ?>
                            </td>
                            <td align="center" width="70" bgcolor="<? echo $bgcolor1;  ?>">
								<?  
                                    if($db_type==0)
                                    {
                                        if($actual_finish_array[$key]=="0000-00-00") echo "";
                                        else echo  change_date_format($actual_finish_array[$key]);
                                    }
                                    else
                                    {
                                        if($actual_finish_array[$key]=="") echo "";
                                        else echo  change_date_format($actual_finish_array[$key]);
                                    } 
                                ?>
                            </td>
                            <td align="center" width="70" bgcolor="<? echo $bgcolor5;  ?>">
								<?  
                                    echo abs($start_diff)." ".$start;
                                ?>
                            </td>
                            <td align="center" width="70" bgcolor="<? echo $bgcolor6;  ?>">
                                <?  
                                    echo abs($finish_diff)." ".$finish;
                                ?>
                            </td>
                            <td width="150">
                            	<Input name="txtresponsible[]" class="text_boxes" ID="txtresponsible_<?php echo $i; ?>" value="<?php  echo $responsible_array[$key]; ?>" style="width:138px" />
                            	<Input type="hidden" name="taskid[]" class="text_boxes" ID="taskid_<?php echo $i; ?>" value="<? echo $key; ?>" style="width:50px">
                            </td>
                            <td width="120" align="center"><Input name="txtcomments[]" class="text_boxes" ID="txtcomments_<?php echo $i; ?>" value="<?php  echo $comments_array[$key]; ?>" onDblClick="openmypage('txtcomments_<?php echo $i; ?>'); return false" style="width:100px;" autocomplete="off" readonly placeholder="Double Click"  /></td>
                            <?
							$mer_comments=$mer_comments_array[$key];
							if($mer_comments==""){
								$mer_comments=substr($smp_data[$key],0,-1);
							}
							?>
                            <td align="center"><Input name="txtmercomments[]" class="text_boxes" ID="txtmercomments_<?php echo $i; ?>" value="<?php  echo $mer_comments; ?>" onDblClick="openmypage('txtmercomments_<?php echo $i; ?>'); return false" style="width:90%;" autocomplete="off" readonly placeholder="Double Click" /></td>
                        </tr>
                        <?
                        }
                        ?>
                    </tbody>
                </table>
                </div>
    		</td>
        </tr>
    </table>
    
    </div>
     
    <table style="width:580px;">
    	<tr>
        	<td colspan="4" height="50" align="right" class="button_container">
            <input type="hidden" id="txt_update_tna_id" name="txt_update_tna_id"  value="<? echo $mid; ?>" />
            <?
					
				if($id_up!='')
				{
					echo load_submit_buttons('1_1_1_1', "fnc_progress_comments_entry", 1,0,"reset_form('tnaprocesscomments_3','','','','','');",3);
				}
				else
				{
					echo load_submit_buttons('1_1_1_1', "fnc_progress_comments_entry", 0,0,"reset_form('tnaprocesscomments_3','','','','','');",3);
				}
			?>
            </td>
            <td valign="top" class="button_container"><input type="button" class="formbutton" value="Print Preview" name="print" id="print" style="width:100px;" onClick="new_window()" /></td>
            <td valign="top" class="button_container"><input type="button" class="formbutton" value="Excel Preview" name="print" id="print" style="width:100px;" onClick="new_excel()" /></td>
        </tr>
    </table>
    </form>
    <?
		$name=time();
		$filenames=$name.".xls";
		//echo $html;
	?>

    <input type="hidden" id="txt_file_link_ref" value="<? echo $filenames; ?>">
    
    
    
    </div>
    <div id="report_container123" align="center"></div>
    
    <script>
		var tableFilters = {}
	</script>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
    <?
		foreach (glob("*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
		}
		//---------end------------//
		$create_new_doc = fopen($filenames, 'w');	
		$is_created = fwrite($create_new_doc,ob_get_contents());
		//echo "$total_data****$filenames****$tot_rows";
		exit();	
}


if($action=="update_tna_progress_comment_style")
{
	if($db_type==0) $blank_date="0000-00-00"; else $blank_date=""; 	

	echo load_html_head_contents("TNA","../../../", 1, 1, $unicode);


	extract($_REQUEST);
	
	$po_id=implode(',',array_unique(explode(',',$po_id)));
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$tna_task_arr=return_library_array( "select task_name, task_short_name from lib_tna_task where  task_type = 1",'task_name','task_short_name');
	$lead_time_array=return_library_array("select task_template_id,lead_time from tna_task_template_details where task_type=1 and is_deleted=0 and status_active=1 group by lead_time,task_template_id","task_template_id",'lead_time');

	$sql ="select b.company_name,b.buyer_name,b.job_no,b.style_ref_no,b.gmts_item_id, set_smv, 
	LISTAGG(cast(a.po_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_number,
	LISTAGG(cast(a.po_received_date as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_received_date,
	LISTAGG(cast(a.shipment_date as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as shipment_date,
	SUM(po_quantity*total_set_qnty) as po_qty_pcs from  wo_po_break_down a,wo_po_details_master b where a.id in($po_id) and a.job_no_mst=b.job_no group by b.company_name,b.buyer_name,b.job_no,b.style_ref_no,b.gmts_item_id, set_smv";
	$result=sql_select($sql);
	//echo $sql;die;
	
	$tna_task_id=array();
	$plan_start_array=array();
	$plan_finish_array=array();
	$actual_start_array=array();
	$actual_finish_array=array();
	$notice_start_array=array();
	$notice_finish_array=array();
	
	/*	 $task_sql=sql_select("select a.task_number,a.task_start_date,a.task_finish_date,a.actual_start_date,a.actual_finish_date,a.notice_date_start,a.notice_date_end from tna_process_mst a, lib_tna_task b where a.task_type=1 and a.task_number=b.task_name and a.po_number_id in($po_id) and b.status_active=1 and b.is_deleted=0 order by b.task_sequence_no asc");*/
	

	 $task_sql=sql_select("select a.task_number,min(a.task_start_date) as  task_start_date ,min(a.task_finish_date) as task_finish_date,min(a.actual_start_date) as actual_start_date,min(a.actual_finish_date) as actual_finish_date,min(a.notice_date_start) as notice_date_start,min(a.notice_date_end) as notice_date_end from tna_process_mst a, lib_tna_task b where a.task_type=1 AND b.task_type = 1 and a.task_number=b.task_name and a.po_number_id in($po_id)  and job_no='$job_no' and b.status_active=1 and b.is_deleted=0 group by a.task_number order by a.task_number asc");

	
	
	foreach ($task_sql as $row_task)
	{
		$tna_task_id[$row_task[csf("task_number")]]=$row_task[csf("task_number")];
		
		$plan_start_array[$row_task[csf("task_number")]] =$row_task[csf("task_start_date")];
		$plan_finish_array[$row_task[csf("task_number")]]=$row_task[csf("task_finish_date")];
		
		$actual_start_array[$row_task[csf("task_number")]] =$row_task[csf("actual_start_date")];
		$actual_finish_array[$row_task[csf("task_number")]]=$row_task[csf("actual_finish_date")];
		
		$notice_start_array[$row_task[csf("task_number")]] =$row_task[csf("notice_date_start")];
		$notice_finish_array[$row_task[csf("task_number")]]=$row_task[csf("notice_date_end")];
	} //var_dump($tna_task_id);die;
	
	//-----------------------------------------------------------------------------------------------
	
	//$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$color=return_library_array( "select a.po_break_down_id ,b.color_name, a.color_number_id from wo_po_color_size_breakdown a, lib_color b where a.color_number_id=b.id and a.po_break_down_id in($po_id) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.po_break_down_id ,b.color_name, a.color_number_id order by b.color_name", "color_number_id", "color_name" );
 
	
	$mer_comments_array=array();
			
			$data_array1=sql_select("select a.job_no_mst,b.color_mst_id, b.color_number_id from  wo_po_break_down a, wo_po_color_size_breakdown b, wo_po_sample_approval_info c where a.job_no_mst=b.job_no_mst and b.job_no_mst=c.job_no_mst and a.id=b.po_break_down_id and b.po_break_down_id=c.po_break_down_id and  b.id=c.color_number_id  and a.id in($po_id) and b.color_mst_id !=0  and c.sample_type_id =7  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by a.id,b.id,c.current_status");   //group by c.id 
 
			
			
			if (count($data_array1)<=0)
			{
				$data_array1=sql_select("select b.color_mst_id, b.color_number_id from  wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.id in($po_id) and b.color_mst_id !=0 and a.id=b.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,a.po_number,b.color_mst_id, b.color_number_id order by a.id");
			}
			
			foreach ( $data_array1 as $row1)
			{
				//$total_color[$row1[csf('color_number_id')]]=$row1[csf('color_number_id')];
			
			//sample app.................................................................start
			$data_array_sample_table=sql_select("Select a.color_number_id,a.approval_status,a.sample_comments,b.sample_type from wo_po_sample_approval_info a,lib_sample b where a.sample_type_id=b.id and a.po_break_down_id in($po_id) and a.color_number_id ='".$row1[csf('color_mst_id')]."'");
				foreach ( $data_array_sample_table as $smp_row)
				{ 
					if ($smp_row[csf("sample_type")]==2) {
							if($smp_row[csf('approval_status')]==1){$smp_data[8].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
							if($smp_row[csf('approval_status')]==3){$smp_data[12].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
						 }
					else if ($smp_row[csf("sample_type")]==3) {
							if($smp_row[csf('approval_status')]==1){$smp_data[7].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
							if($smp_row[csf('approval_status')]==3){$smp_data[13].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
					}
					else if ($smp_row[csf("sample_type")]==4) {
							if($smp_row[csf('approval_status')]==1){$smp_data[14].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
							if($smp_row[csf('approval_status')]==3){$smp_data[15].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
					}
					else if ($smp_row[csf("sample_type")]==7) {
							if($smp_row[csf('approval_status')]==1){$smp_data[16].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
							if($smp_row[csf('approval_status')]==3){$smp_data[17].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
					}
					else if ($smp_row[csf("sample_type")]==8) { 
							if($smp_row[csf('approval_status')]==1){$smp_data[21].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
							if($smp_row[csf('approval_status')]==3){$smp_data[22].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
					}
					else if ($smp_row[csf("sample_type")]==9) {
							if($smp_row[csf('approval_status')]==1){$smp_data[23].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
							if($smp_row[csf('approval_status')]==3){$smp_data[24].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
					}

				}
			//sample app.................................................................end


			//lapdip app..................................................................start	
			$data_array_sample_table=sql_select("Select color_name_id,approval_status,lapdip_comments from wo_po_lapdip_approval_info where  po_break_down_id in($po_id)");
				foreach ( $data_array_sample_table as $smp_row)
				{ 
					if($smp_row[csf('approval_status')]==1){$smp_data[9].=$color[$smp_row[csf('color_name_id')]].': '.$smp_row[csf('lapdip_comments')].',';}
					if($smp_row[csf('approval_status')]==3){$smp_data[10].=$color[$smp_row[csf('color_name_id')]].': '.$smp_row[csf('lapdip_comments')].',';}
				
				}
		//lapdip app.........................................................end	
		
		
		//embell app..........................................................start	
			$data_array_sample_table=sql_select("Select color_name_id,approval_status,embellishment_comments from wo_po_embell_approval where po_break_down_id in($po_id)");
				foreach ( $data_array_sample_table as $smp_row)
				{ 
					if($smp_row[csf('approval_status')]==1){$smp_data[19].=$color[$smp_row[csf('color_name_id')]].': '.$smp_row[csf('embellishment_comments')].',';}
					if($smp_row[csf('approval_status')]==3){$smp_data[20].=$color[$smp_row[csf('color_name_id')]].': '.$smp_row[csf('embellishment_comments')].',';}
				
				}
		//embell app..........................................................end	


		//Trims app..........................................................start	
		$data_array_sample_table=sql_select("Select approval_status,accessories_comments from wo_po_trims_approval_info where po_break_down_id='".$po_id."'");
			foreach ( $data_array_sample_table as $smp_row)
			{ 
				if($smp_row[csf('approval_status')]==1){$smp_data[25].=$smp_row[csf('accessories_comments')].',';}
				if($smp_row[csf('approval_status')]==3){$smp_data[11].=$smp_row[csf('accessories_comments')].',';}
			}
		}
	    //--------------  
	$comments_array=array();
	$responsible_array=array();
	
	//$res_comm_sql= sql_select("select task_id, comments, responsible from tna_progress_comments where tamplate_id='$template_id' and order_id='$po_id'");
	//echo "select task_id, comments, responsible from tna_progress_comments where order_id='$po_id'";
	$res_comm_sql=sql_select("select task_id, comments, responsible,mer_comments from tna_progress_comments where order_id in($po_id)");
	
	foreach ($res_comm_sql as $row_res_comm)
	{
		$comments_array[$row_res_comm[csf("task_id")]] =$row_res_comm[csf("comments")];
		$mer_comments_array[$row_res_comm[csf("task_id")]] =$row_res_comm[csf("mer_comments")];
		$responsible_array[$row_res_comm[csf("task_id")]]=$row_res_comm[csf("responsible")];
	}
	
	
	$execution_time_array=array();
	
	//$execution_time_sql= sql_select("select tna_task_id, execution_days from tna_task_template_details where task_template_id='$template_id'");
	
	$execution_time_sql= sql_select("select for_specific, tna_task_id, execution_days from tna_task_template_details where task_type=1 and is_deleted=0 and status_active=1");
	foreach ($execution_time_sql as $row_execution_time)
	{
		$execution_time_array[$row_execution_time[csf("for_specific")]][$row_execution_time[csf("tna_task_id")]]=$row_execution_time[csf("execution_days")];
	}
	
	//$upid_sql= sql_select("select min(id) as id from tna_progress_comments where tamplate_id='$template_id' and order_id='$po_id'");
	
	$upid_sql= sql_select("select min(id) as id from tna_progress_comments where order_id in($po_id)");
	foreach ($upid_sql as $row_upid)
	{
		$id_up=$row_upid[csf("id")];
	}
	
	$lead_time=return_library_array("select task_template_id,lead_time from tna_task_template_details where task_type=1 and is_deleted=0 and status_active=1 group by task_template_id,lead_time","task_template_id","lead_time");
		


	$booking_no=return_field_value("booking_no","wo_booking_dtls","po_break_down_id in(".$po_id.") and BOOKING_TYPE=1 and status_active=1 and is_deleted=0","booking_no");

	$imbillishment_cost=return_field_value("rate","wo_pre_cost_embe_cost_dtls","job_no='".$result[0][csf('job_no')]."' and status_active=1 and is_deleted=0","rate");
	$is_imblishment=$imbillishment_cost?"Yes":"No";

		
	$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst where job_no='".$result[0][csf('job_no')]."'","job_no","costing_per"); 
		$set_item_ratio_arr = return_library_array("select gmts_item_id, set_item_ratio from wo_po_details_mas_set_details where job_no='".$result[0][csf('job_no')]."'","gmts_item_id","set_item_ratio"); 
		
	 
	 $sql_po_qty_fab_data=sql_select("select sum(c.plan_cut_qnty) as order_quantity,c.item_number_id,c.size_number_id,c.color_number_id  from  wo_po_color_size_breakdown c where  c.po_break_down_id in(".$po_id.") and c.status_active=1  group by c.item_number_id,c.size_number_id,c.color_number_id");
	 foreach($sql_po_qty_fab_data as $row){
		$key=$row[csf(item_number_id)].$row[csf(size_number_id)].$row[csf(color_number_id)];
		$sql_po_qty_fab_arr[$key]+=$row[csf(order_quantity)]; 
	 }
	
	$sql = "select id, job_no,item_number_id, body_part_id, fab_nature_id, color_type_id, fabric_description, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active from wo_pre_cost_fabric_cost_dtls where job_no='".$result[0][csf('job_no')]."' and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);

	//echo $sql;die;
		
	$req_qty=0;
	foreach( $data_array as $row )
    {
		
		$set_item_ratio=$set_item_ratio_arr[$row[csf('item_number_id')]];
		
		$fab_dtls_data=sql_select("select po_break_down_id,color_number_id,gmts_sizes,cons,requirment from wo_pre_cos_fab_co_avg_con_dtls where pre_cost_fabric_cost_dtls_id=".$row[csf("id")]." and po_break_down_id in(".$po_id.") and cons !=0 ");
	   
		foreach($fab_dtls_data as $fab_dtls_data_row )
		{
			$dzn_qnty=0;
			if($costing_per_arr[$result[0][csf('job_no')]]==1) $dzn_qnty=12;
			else if($costing_per_arr[$result[0][csf('job_no')]]==3) $dzn_qnty=12*2;
			else if($costing_per_arr[$result[0][csf('job_no')]]==4) $dzn_qnty=12*3;
			else if($costing_per_arr[$result[0][csf('job_no')]]==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
			
			$key=$result[0][csf('gmts_item_id')].$fab_dtls_data_row[csf('gmts_sizes')].$fab_dtls_data_row[csf('color_number_id')];
			$po_qty_fab=$sql_po_qty_fab_arr[$key]; 
			$req_qty+=($po_qty_fab/($dzn_qnty*$set_item_ratio))*$fab_dtls_data_row[csf("cons")];
		}
	}
	  
	?> 
   
    <script>
	 
		var permission='<? echo $permission; ?>';
		//var refresh_data="";
	
		function fnc_progress_comments_entry(operation)
		{
			var tot_row=$('#comments_tbl tbody tr').length;
			var data_all=''; var j=0;
			for(i=1; i<=tot_row; i++)
			{
				if (form_validation('taskid_'+i,'Task Number')==false )
				{
					alert("Task Number Not Found, Please Click On PO Number");
					return;
				}
				
				var responsible=$("#txtresponsible_"+i).val();
				var comments=$("#txtcomments_"+i).val();
				var mrc_comments=$("#txtmercomments_"+i).val();
				var taskid=$("#taskid_"+i).val();
				if (comments!="" || mrc_comments!="" || responsible!="")
				{
					j++;
					data_all+=get_submitted_data_string('txtresponsible_'+i+'*txtcomments_'+i+'*txtmercomments_'+i+'*taskid_'+i,"../../../",i);
				}
			}
			if(data_all=='')
			{
				alert("No Comments Found");	
				return;
			}
			var data="action=save_update_delete_progress_comments_style&operation="+operation+get_submitted_data_string('jobno*orderid*tamplateid',"../../../")+data_all+'&tot_row='+tot_row;
			freeze_window(operation);
			http.open("POST","tna_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fnc_progress_comments_Reply_info;
		}
		
		function fnc_progress_comments_Reply_info()
		{
			if(http.readyState == 4) 
			{
				var reponse=trim(http.responseText).split('**');	
				show_msg(reponse[0]);
				var path_link=$('#txt_file_link_ref').val();
				$.post('tna_report_controller.php?job_no='+'<? echo $job_no; ?>'+'&po_id='+<? echo $po_id; ?>+'&template_id='+<? echo $template_id; ?>+'&tna_process_type='+<? echo $tna_process_type; ?>,
				{ 
					path: '', action: "generate_report_file", filename: path_link },
					function(data)
					{
						$('#txt_file_link_ref').val(data);
					}
				);
					set_button_status(1, permission, 'fnc_progress_comments_entry',3);
				release_freezing();	
			}
		}
		function autoRefresh_div()
		 {
			  $("#auto_id").load("tna_report_controller.php");
		  }
		
		function openmypage(i)
		{	
			var title = 'TNA Progress Comment';
			var txtcomments = document.getElementById(i).value;
			var page_link = 'tna_report_controller.php?data='+txtcomments+'&action=comments_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=160px,center=1,resize=1,scrolling=0','../../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var additional_infos=this.contentDoc.getElementById("additional_infos").value;
				document.getElementById(i).value=additional_infos;
			}
		}
		
		function new_window()
		{
			var url = 'tna_report_controller.php?job_no=<? echo $job_no;?>&po_id=<? echo $po_id;?>&template_id=<? echo $template_id;?>&tna_process_type=<? echo $tna_process_type;?>&action=tna_progress_comment_print&permission=<? echo $permission;?>';
			window.open(url, "MY PAGE");
		}
		
		function new_excel()
		{
			var url = 'tna_report_controller.php?job_no=<? echo $job_no;?>&po_id=<? echo $po_id;?>&template_id=<? echo $template_id;?>&tna_process_type=<? echo $tna_process_type;?>&action=tna_progress_comment_print&permission=<? echo $permission;?>&isExcel=1';
			window.open(url, "MY PAGE");
		} 
	</script>
    
	</head>
	<body onLoad="set_hotkey()">
	<div id="messagebox_main"></div>
	<div align="center" style="width:100%;">
    <? 
	echo load_freeze_divs ("../../../",'',1); 
	ob_start();
	?> 
    <form name="tnaprocesscomments_3" id="tnaprocesscomments_3" autocomplete="off">
    
		<div align="center" style="width:100%" id="details_reports">
		
		<table width="1000" border="1" rules="all" class="rpt_table">
			<tr><td colspan="6" align="center"><b><font size="+1">TNA Progress Comment</font></b></td></tr>
		</table>
    
		<table width="1000" border="1" rules="all" class="rpt_table">
			<?php $buyer_id="";
			foreach($result as $row)
			{
				$buyer_id=$row[csf('buyer_name')];
			?>
			<tr>
				<td width="130">Company</td>
				<td width="176"><?php  echo $company_library[$row[csf('company_name')]];  ?></td>
				<td width="130">Buyer</td>
				<td width="176"><?php  echo $buyer_arr[$row[csf('buyer_name')]];  ?></td>
				<td width="130">Job Number</td>
				<td width="176">
					<? echo $row[csf('job_no')];   ?>
					<Input type="hidden" name="jobno" class="text_boxes" ID="jobno" value="<? echo $job_no; ?>" style="width:100px" />
					<Input type="hidden" name="orderid" class="text_boxes" ID="orderid" value="<? echo $po_id; ?>" style="width:100px" />
					<Input type="hidden" name="tamplateid" class="text_boxes" ID="tamplateid" value="<? echo $template_id; ?>" style="width:100px" />
				</td>
			</tr>
			<tr>
				<td>Order No</td>
				<td><b><?php  echo $row[csf('po_number')]; ?></b></td>
				<td>Style Ref.</td>
				<td><?php  echo $row[csf('style_ref_no')];  ?></td>
				<td>Booking Number</td>
				<td><?php echo $booking_no; ?></td>
			</tr>
			<tr>
				<td>Garments Item</td>
				<td><?php  echo $garments_item[$row[csf('gmts_item_id')]];  ?></td>
				<td>Embellishment</td>
				<td><b><?php echo $is_imblishment;  ?></b></td>
				<td>SMV</td>
				<td><b><?php echo $row[csf('set_smv')]; ?></b></td>
			</tr>
			<tr>
				<td>Order Recv. Date</td>
				<td><?php  
				foreach(explode(',',$row[csf('po_received_date')]) as $po_received_date){
					$po_received_date_arr[$po_received_date]=change_date_format($po_received_date);
				}
					echo implode(',',$po_received_date_arr);
				?></td>
				<td>Ship Date</td>
				<td><b><?php   
				foreach(explode(',',$row[csf('shipment_date')]) as $shipment_date_date){
					$shipment_date_date_arr[$shipment_date_date]=change_date_format($shipment_date_date);
				}
					echo implode(',',$shipment_date_date_arr);
				?></b></td>
				<td>Lead Time</td>
				<td><b>
					<? 
						$lead_time_arr=array();
						if($tna_process_type==1)
						{
							foreach(array_unique(explode(',',$template_id)) as $tplid){
								$lead_time_arr[$tplid]=$lead_time_array[$tplid]+1;
							}
						}
						else
						{
							$lead_time_arr[$template_id]=$template_id+1;
						}
						echo implode(', ',$lead_time_arr);
					?>
				</b></td>
			</tr>
			<tr>
				<td>Quantity (PCS)</td>
				<td><b><?php echo $row[csf('po_qty_pcs')];?></b></td>
				<td>Finish Req. (KG)</td>
				<td><b><?php echo number_format($req_qty,2); ?></b></td>
				<td>Number of Color</td>
				<td><b><?php echo count($color);  ?></b></td>
			</tr>
			<?php
			}
			?>
		</table>
    
        <table><tr height="10"><td colspan="6">&nbsp;</td></tr></table>
    
		<table style="width: 1130px;">
			<tr>
				<td>
					<div style="width: 1120px;font-size:12px;">
					<table width="1100" border="1" rules="all" class="rpt_table">
						<thead>
							<tr align="center">
								<th width="30">Task No</th>
								<th width="150">Task Name</th>
								<th width="60">Allowed Days</th>
								<th width="70">Plan Start Date</th>
								<th width="70">Plan Finish Date</th>
								<th width="70">Actual Start Date</th>
								<th width="70">Actual Finish Date</th>
								<th width="70">Start Delay/ Early By</th>
								<th width="70">Finish Delay/ Early By</th>
								<th width="150">Responsible</th>
								<th width="120">Comments</th>
								<th width="">Mer. Comments</th>
							</tr>
						</thead>
					</table>
					</div>
				</td>
			</tr>
		</table> 
		<table style="width:1130px;">
			<tr>
				<td>    
					<div style="width: 1120px;overflow-y: scroll; max-height:180px;font-size:12px;" id="scroll_body2">
					<table width="1100px" border="1" rules="all" class="rpt_table" id="comments_tbl">
						<tbody>
							<?php
							$i=0; 
							foreach($tna_task_id as $key)
							{
								$i++;
							$trcolor=($i%2==0)?"#E9F3FF":"#FFFFFF"; 	
							
								$bgcolor1=""; $bgcolor="";
										
								if ($plan_start_array[$key]!=$blank_date) 
								{
									if (strtotime($notice_start_array[$key])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($plan_start_array[$key]))  $bgcolor="#FFFF00";
									else if (strtotime($plan_start_array[$key])<strtotime(date("Y-m-d",time())))  $bgcolor="#FF0000";
									else $bgcolor="";
									
								}
								
								if ($plan_finish_array[$key]!=$blank_date) {
									if (strtotime($notice_finish_array[$key])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($plan_finish_array[$key]))  $bgcolor1="#FFFF00";
									else if (strtotime($plan_finish_array[$key])<strtotime(date("Y-m-d",time())))  $bgcolor1="#FF0000"; else $bgcolor1="";
								}
								
								if ($actual_start_array[$key]!=$blank_date) $bgcolor="";
								if ($actual_finish_array[$key]!=$blank_date) $bgcolor1="";
								// Delay / Early............
								$bgcolor5=""; $bgcolor6="";
								$delay=""; $early="";
								
								if($actual_start_array[$key]!=$blank_date)
								{
									$start_diff1 = datediff( "d", $actual_start_array[$key], $plan_start_array[$key]);
									if($actual_finish_array[$key]=="" || $actual_finish_array[$key]=="0000-00-00"){
										
										$finish_diff1 = datediff( "d",date("Y-m-d"), $plan_finish_array[$key]);
									}
									else
									{
										$finish_diff1 = datediff( "d", $actual_finish_array[$key], $plan_finish_array[$key]);	
									}
									$start_diff=$start_diff1-1;
									$finish_diff=$finish_diff1-1;
									
									if($start_diff<0)
									{
										$bgcolor5="#2A9FFF";	//Blue	
										$start="(Delay)";
									}
									if($start_diff>0)
									{
										$bgcolor5="";
										$start="(Early)";
									}
									if($finish_diff<0)
									{
										if($actual_finish_array[$key]=="" || $actual_finish_array[$key]=="0000-00-00"){
											$bgcolor6="#FF0000";//Blue
										}
										else
										{
											$bgcolor6="#2A9FFF";//Blue
										}
										$finish="(Delay)";
									}
									if($finish_diff>0)
									{	
										$bgcolor6="";
										$finish="(Early)";
									}
								}
								else
								{
									if(date("Y-m-d")> date("Y-m-d",strtotime($plan_start_array[$key])))
									{
										$start_diff1 = datediff( "d", $plan_start_array[$key], date("Y-m-d"));
										$start_diff=$start_diff1-1;
										$bgcolor5="#FF0000";		//Red
										$start="(Delay)";
									}
									if(date("Y-m-d")> date("Y-m-d",strtotime($plan_finish_array[$key])))
									{
										$finish_diff1 = datediff( "d", $plan_finish_array[$key], date("Y-m-d"));
										$finish_diff=$finish_diff1-1;
										$bgcolor6="#FF0000";
										$finish="(Delay)";
									}
									if(date("Y-m-d")<= date("Y-m-d",strtotime($plan_start_array[$key])))
									{
										$start_diff = "";
										$bgcolor5="";
										$start="";
									}
									if(date("Y-m-d")<= date("Y-m-d",strtotime($plan_finish_array[$key])))
									{
										$finish_diff = "";
										$bgcolor6="";
										$finish="";
									}
								}
							?>
							<tr bgcolor="<? echo $trcolor; ?>">
								<td align="center" width="30"><? echo $i; ?></td>
								<td width="150"> <? echo $tna_task_arr[$key]; ?></td>
								<td align="center" width="60"><? echo datediff( "d", $plan_start_array[$key],$plan_finish_array[$key]); ?></td>
								<td align="center" width="70"><? echo  change_date_format($plan_start_array[$key]); ?></td>
								<td align="center" width="70"><? echo  change_date_format($plan_finish_array[$key]); ?></td>
								<td align="center" width="70" bgcolor="<? echo $bgcolor;  ?>">
									<?
										if($db_type==0)
										{
											if($actual_start_array[$key]=="0000-00-00") echo "";
											else echo  change_date_format($actual_start_array[$key]);
										}
										else
										{
											if($actual_start_array[$key]=="") echo "";
											else echo  change_date_format($actual_start_array[$key]);
										}
									?>
								</td>
								<td align="center" width="70" bgcolor="<? echo $bgcolor1;  ?>">
									<?  
										if($db_type==0)
										{
											if($actual_finish_array[$key]=="0000-00-00") echo "";
											else echo  change_date_format($actual_finish_array[$key]);
										}
										else
										{
											if($actual_finish_array[$key]=="") echo "";
											else echo  change_date_format($actual_finish_array[$key]);
										} 
									?>
								</td>
								<td align="center" width="70" bgcolor="<? echo $bgcolor5;  ?>">
									<?  
										echo abs($start_diff)." ".$start;
									?>
								</td>
								<td align="center" width="70" bgcolor="<? echo $bgcolor6;  ?>">
									<?  
										echo abs($finish_diff)." ".$finish;
									?>
								</td>
								<td width="150">
									<Input name="txtresponsible[]" class="text_boxes" ID="txtresponsible_<?php echo $i; ?>" value="<?php  echo $responsible_array[$key]; ?>" style="width:138px" />
									<Input type="hidden" name="taskid[]" class="text_boxes" ID="taskid_<?php echo $i; ?>" value="<? echo $key; ?>" style="width:50px">
								</td>
								<td width="120" align="center"><Input name="txtcomments[]" class="text_boxes" ID="txtcomments_<?php echo $i; ?>" value="<?php  echo $comments_array[$key]; ?>" onDblClick="openmypage('txtcomments_<?php echo $i; ?>'); return false" style="width:100px;" autocomplete="off" readonly placeholder="Double Click"  /></td>
								<?
								$mer_comments=$mer_comments_array[$key];
								if($mer_comments==""){
									$mer_comments=substr($smp_data[$key],0,-1);
								}
								?>
								<td align="center"><Input name="txtmercomments[]" class="text_boxes" ID="txtmercomments_<?php echo $i; ?>" value="<?php  echo $mer_comments; ?>" onDblClick="openmypage('txtmercomments_<?php echo $i; ?>'); return false" style="width:90%;" autocomplete="off" readonly placeholder="Double Click" /></td>
							</tr>
							<?
							}
							?>
						</tbody>
					</table>
					</div>
				</td>
			</tr>
		</table>
    </div>
     
    <table style="width:580px;">
    	<tr>
        	<td colspan="4" height="50" align="right" class="button_container">
            <input type="hidden" id="txt_update_tna_id" name="txt_update_tna_id"  value="<? echo $mid; ?>" />
            <?
					
				if($id_up!='')
				{
					echo load_submit_buttons('1_1_1_1', "fnc_progress_comments_entry", 1,0,"reset_form('tnaprocesscomments_3','','','','','');",3);
				}
				else
				{
					echo load_submit_buttons('1_1_1_1', "fnc_progress_comments_entry", 0,0,"reset_form('tnaprocesscomments_3','','','','','');",3);
				}
			?>
            </td>
            <td valign="top" class="button_container"><input type="button" class="formbutton" value="Print Preview" name="print" id="print" style="width:100px;" onClick="new_window()" /></td>
            <td valign="top" class="button_container"><input type="button" class="formbutton" value="Excel Preview" name="print" id="print" style="width:100px;" onClick="new_excel()" /></td>
        </tr>
    </table>
    </form>
    <?
		$name=time();
		$filenames=$name.".xls";
		//echo $html;
	?>

    <input type="hidden" id="txt_file_link_ref" value="<? echo $filenames; ?>">
    
    
    
    </div>
    <div id="report_container123" align="center"></div>
    
    <script>
		var tableFilters = {}
	</script>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
    <?
		foreach (glob("*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
		}
		//---------end------------//
		$create_new_doc = fopen($filenames, 'w');	
		$is_created = fwrite($create_new_doc,ob_get_contents());
		//echo "$total_data****$filenames****$tot_rows";
		exit();	
}


if ($action=="save_update_delete_progress_comments_style")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$tamplateid=str_replace("'","",$tamplateid);
	$orderid=str_replace("'","",$orderid);
	
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$id=return_next_id( "id","tna_progress_comments", 1 ) ;
		$field_array_comments="id, job_id, order_id, tamplate_id, task_id, responsible, comments,mer_comments,task_type, inserted_by, insert_date, status_active, is_deleted";
		 
		
		$tamplateid_end=end(explode(',',str_replace("'","",$tamplateid)));
		foreach(array_unique(explode(',',$orderid)) as $pid){
			for($i=1;$i<=$tot_row; $i++)
			{
				$txtresponsible='txtresponsible_'.$i;
				$txtcomments='txtcomments_'.$i;
				$txtmercomments='txtmercomments_'.$i;
				$taskid='taskid_'.$i;
				if(str_replace("'","",$$txtcomments)!="" || str_replace("'","",$$txtmercomments)!="" || str_replace("'","",$$txtresponsible)!="")
				{
					if($data_array_comments!="") $data_array_comments.=",";
		
					$data_array_comments.="(".$id.",".$jobno.",".$pid.",".$tamplateid_end.",".$$taskid.",".$$txtresponsible.",".$$txtcomments.",".$$txtmercomments.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$id=$id+1;
				}
			}
		}
		
		//echo "10**insert into tna_progress_comments (".$field_array_comments.") Values ".$data_array_comments."";die;
		
		//echo $rIDs=sql_insert2("tna_progress_comments",$field_array_comments,$data_array_comments,1);
		
		$rIDs=sql_insert("tna_progress_comments",$field_array_comments,$data_array_comments,1);
		
		if($db_type==0)
		{
			if($rIDs)
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'","",$id)."**".str_replace("'","",$id)."**1";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**".str_replace("'","",$id)."**".str_replace("'","",$id)."**0";
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rIDs)
			{
				oci_commit($con); 
				echo "0**".str_replace("'","",$id)."**".str_replace("'","",$id)."**"."**1";
			}
			else
			{
				oci_rollback($con); 
				echo "5**".str_replace("'","",$id)."**".str_replace("'","",$id)."**0";
			}
		}
		
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$id=return_next_id( "id","tna_progress_comments", 1 ) ;
		$field_array_comments="id, job_id, order_id, tamplate_id, task_id, responsible, comments,mer_comments,task_type, inserted_by, insert_date, status_active, is_deleted";
		$data_array_comments='';
		
		$tamplateid_end=end(explode(',',$tamplateid));
		foreach(array_unique(explode(',',$orderid)) as $pid){
			for($i=1;$i<=$tot_row; $i++)
			{
				$txtresponsible='txtresponsible_'.$i;
				$txtcomments='txtcomments_'.$i;
				$txtmercomments='txtmercomments_'.$i;
				$taskid='taskid_'.$i;
				
				if(str_replace("'","",$$txtcomments)!="" || str_replace("'","",$$txtmercomments)!="" || str_replace("'","",$$txtresponsible)!="")
				{
					if($data_array_comments!="") $data_array_comments.=",";
		
					$data_array_comments.="(".$id.",".$jobno.",".$pid.",".$tamplateid_end.",".$$taskid.",".$$txtresponsible.",".$$txtcomments.",".$$txtmercomments.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$id=$id+1;
				}
			}
		}
		
		//echo "10**insert into tna_progress_comments (".$field_array_comments.") Values ".$data_array_comments."";die;
		
		$rID=execute_query("delete from tna_progress_comments where tamplate_id in($tamplateid) and order_id in($orderid) and task_type=1");
		$rIDs=sql_insert("tna_progress_comments",$field_array_comments,$data_array_comments,1);
		
		//echo "0**".$rIDs;mysql_query("COMMIT");die;
		
		if($db_type==0)
		{
			if( $rID && $rIDs )
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'","",$id)."**".str_replace("'","",$id)."**1";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**".str_replace("'","",$id)."**".str_replace("'","",$id)."**0";
			}
		}
		if($db_type==2 || $db_type==1)
		{
			if( $rID && $rIDs )
			{
				oci_commit($con); 
				echo "0**".str_replace("'","",$id)."**".str_replace("'","",$id)."**1";
			}
			
			else
			{
				oci_rollback($con); 
				echo "5**".str_replace("'","",$id)."**".str_replace("'","",$id)."**0";
			}
		}
		disconnect($con);
		die;
	}
}



if($action=="generate_tna_with_commitment_report")
{

	$manual_update_task_arr=array(133,125,18,165,166,167,130,176,177,131,181,134,71,132);
	
	$mod_sql= sql_select("select id,task_catagory,task_name,task_short_name,task_type,completion_percent from lib_tna_task where is_deleted = 0 and status_active=1  AND task_type = 1 order by task_sequence_no asc");
	$tna_task_array=array();
	$tna_task_id=array();
	$tna_task_cat=array();
	$tna_task_name_arr=array();
	foreach ($mod_sql as $row)
	{
		$tna_task_id[$row[csf("task_name")]]=$row[csf("task_name")];
		$tna_task_array[$row[csf("id")]] =$row[csf("task_short_name")];
		$tna_task_name_array[$row[csf("id")]] =$tna_task_name[$row[csf("task_name")]];
		$tna_task_cat[$row[csf("id")]]=$row[csf("task_catagory")];
		$tna_task_name_arr[$row[csf("id")]]=$row[csf("task_name")];
	}
	$cbo_company_id=$cbo_company_name;
	
	$order_status_cond="";
	if(str_replace("'","",$cbo_order_status)>0) $order_status_cond=" and b.is_confirmed=$cbo_order_status";
	
	if(str_replace("'","",$cbo_company_name)==0) $cbo_company_name=""; else $cbo_company_name=" and a.company_name = $cbo_company_name";
	if(str_replace("'","",$cbo_buyer_name)==0) $cbo_buyer_name=""; else $cbo_buyer_name=" and a.buyer_name = $cbo_buyer_name";
	if(str_replace("'","",$cbo_team_member)==0) $cbo_team_member=""; else $cbo_team_member=" and a.dealing_marchant = $cbo_team_member";
	
	if(str_replace("'","",$cbo_search_type)==1){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
	}
	else if(str_replace("'","",$cbo_search_type)==3){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and c.country_ship_date between $txt_date_from and $txt_date_to";
	}
	else if(str_replace("'","",$cbo_search_type)==4){
		if($db_type==0)
		{
			if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)==""){$date_range="";}else{ 
			$date_range=" and b.insert_date between ".$txt_date_from." and ".$txt_date_to."";}
		}
		else
		{
			
			if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)==""){$date_range="";}else{ 
			$date_range=" and b.insert_date between ".$txt_date_from." and '".str_replace("'","",$txt_date_to)." 11:59:59 PM'";}
		}
	}
	else
	{
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.po_received_date between $txt_date_from and $txt_date_to";
	}
	
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if($txt_job_no=="") $txt_job_no=""; else $txt_job_no=" and a.job_no_prefix_num ='$txt_job_no'";
	$txt_order_no=str_replace("'","",$txt_order_no);
	if($txt_order_no=="") $txt_order_no=""; else $txt_order_no=" and b.po_number ='$txt_order_no'";
	$txt_file_no=str_replace("'","",$txt_file_no);
	if($txt_file_no=="") $file_cond=""; else $file_cond=" and b.file_no ='$txt_file_no'";
	$txt_int_ref_no=str_replace("'","",$txt_int_ref_no);
	if($txt_int_ref_no=="") $ref_cond=""; else $ref_cond=" and b.grouping ='$txt_int_ref_no'";
	
	$txt_style_ref_no=str_replace("'","",$txt_style_ref_no);
	if($txt_style_ref_no=="") $txt_style_ref_no=""; else $txt_style_ref_no=" and a.style_ref_no ='$txt_style_ref_no'";
	//**txt_date_from*txt_date_to*txt_job_no
	
	if(str_replace("'","",$cbo_shipment_status)==4){$shipment_status_con=" and b.shiping_status=3";}
	else if(str_replace("'","",$cbo_shipment_status)==1){$shipment_status_con=" and b.shiping_status !=3";}
	
	
	
	
	
	$tna_all_task=implode(",",$tna_task_id);
	
	if(str_replace("'","",$cbo_search_type)==3)
	{
		$sql = "SELECT a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, b.id ,b.po_number, b.file_no, b.grouping as in_ref_no
		FROM  wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c 
		WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no  and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 $order_status_cond $file_cond $ref_cond";
	}
	else
	{
		$sql = "SELECT a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, b.id, b.po_number, b.file_no, b.grouping as in_ref_no 
		FROM  wo_po_details_master a,  wo_po_break_down b 
		WHERE a.job_no=b.job_no_mst $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no  and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 $order_status_cond  $file_cond $ref_cond"; 
	}
	
 //echo $sql; 
	$result = sql_select( $sql ) ;
	$wo_po_details_master = array();
	$po_no_arr=array();
	$job_no_arr=array();
	foreach( $result as  $row ) 
	{	
		$wo_po_details_master[$row[csf('id')]]['company_name']=$row[csf('company_name')];
		$wo_po_details_master[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$wo_po_details_master[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$wo_po_details_master[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
		$wo_po_details_master[$row[csf('id')]]['dealing_marchant']=$row[csf('dealing_marchant')];
		$wo_po_details_master[$row[csf('id')]]['po_number']= $row[csf('po_number')];
		$wo_po_details_master[$row[csf('id')]]['set_smv']= $row[csf('set_smv')];
		$wo_po_details_master[$row[csf('id')]]['file_no']= $row[csf('file_no')];
		$wo_po_details_master[$row[csf('id')]]['in_ref_no']= $row[csf('in_ref_no')];
		$po_no_arr[]=$row[csf('id')];
		$job_no_arr[]=$row[csf('job_no')];
		
	}
	

 
	$sql = "SELECT team_member_name,id FROM lib_mkt_team_member_info WHERE is_deleted = 0 and status_active=1 order by id asc";
	$result = sql_select( $sql ) ;
	$team_member_name = array();
	foreach( $result as  $row ) 
	{	
		$team_member_name[$row[csf('id')]]=$row[csf('team_member_name')];
	}
	
	$sql = "SELECT buyer_name,id FROM  lib_buyer WHERE is_deleted = 0 and status_active=1 order by id asc";
	$result = sql_select( $sql ) ;
	$buyer_name = array();
	foreach( $result as  $row ) 
	{	
		$buyer_name[$row[csf('id')]]=$row[csf('buyer_name')];
	}
	
	
	$po_no_arr_all=implode(",",$po_no_arr); if($po_no_arr_all!="") $po_no_arr_all .=",0"; else $po_no_arr_all .="0"; 
	$job_no_all="'".implode("','",$job_no_arr)."'";
	$c=count($tna_task_id);
	
	if($db_type==0)
	{
		$sql ="select a.po_number_id, a.job_no, a.shipment_date, a.template_id, a.po_receive_date,b.insert_date,";
		$i=1;
	
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c) $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id, ";
			else $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id ";
			$i++;
		}
		
		$sql .=" from tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id and a.po_number_id in( $po_no_arr_all ) and a.job_no in ($job_no_all) $shipment_status_con and b.status_active=1  and b.po_quantity>0 $order_status_cond group by a.po_number_id,a.job_no,b.insert_date order by a.shipment_date,a.po_number_id,a.job_no"; 
	}
	else
	{
		$sql ="select a.po_number_id, a.job_no, max(a.shipment_date) as shipment_date, a.template_id, max(a.po_receive_date) as po_receive_date,b.insert_date,";
		$i=1;
		
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c) $sql .="max(CASE WHEN a.task_number = '".$id."' THEN a.actual_start_date || '_' || a.actual_finish_date || '_' || a.task_start_date || '_' || a.task_finish_date ||'_' || a.notice_date_start || '_' || a.notice_date_end || '_' || a.remarks || '_' || a.id || '_' || a.task_number || '_' || a.plan_start_flag || '_' || a.plan_finish_flag || '_' || a.commit_start_date || '_' || a.commit_end_date  END ) as status$id, ";
			
			else $sql .="max(CASE WHEN a.task_number = '".$id."' THEN a.actual_start_date || '_' || a.actual_finish_date || '_' || a.task_start_date || '_' || a.task_finish_date || '_' || a.notice_date_start || '_' || a.notice_date_end || '_' || a.remarks || '_' || a.id || '_' || a.task_number || '_' || a.plan_start_flag || '_' || a.plan_finish_flag || '_' || a.commit_start_date || '_' || a.commit_end_date  END ) as status$id ";
			
			$i++;
		}
		//------------------
			$sql_order_con='';
			$po_no_arr_all=explode(',',$po_no_arr_all);
			$chunk_po_no_arr_all=array_chunk(array_unique($po_no_arr_all),999);
			$p=1;
			foreach($chunk_po_no_arr_all as $rlz_sub_id)
			{
				if($p==1) $sql_order_con .=" and (a.po_number_id in(".implode(',',$rlz_sub_id).")"; else $sql_sub_lc .=" or a.po_number_id in(".implode(',',$rlz_sub_id).")";
				$p++;
			}
			$sql_order_con .=" )";
			
			$sql_job_con='';
			$job_no_all=explode(',',$job_no_all);
			$chunk_job_no_all=array_chunk(array_unique($job_no_all),999);
			$q=1;
			foreach($chunk_job_no_all as $rlz_sub_id)
			{
				if($q==1) $sql_job_con .=" and (a.job_no in(".implode(',',$rlz_sub_id).")"; else $sql_sub_lc .=" or a.job_no in(".implode(',',$rlz_sub_id).")";
				$p++;
			}
			$sql_job_con .=" )";
			
			
		//-------------------------------
		$sql .=" from  tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id $sql_order_con $sql_job_con $shipment_status_con and b.status_active=1 and b.po_quantity>0 $order_status_cond  group by a.po_number_id,a.job_no,a.template_id,a.shipment_date,b.insert_date order by a.shipment_date,a.po_number_id,a.job_no"; 
	}
	
	  //echo $sql;
	
	$lead_time_array=return_library_array("select task_template_id,lead_time from tna_task_template_details where task_type=1 and is_deleted=0 and status_active=1 group by lead_time,task_template_id","task_template_id",'lead_time');
	if($db_type==0)
	{
		$tast_tmp_id_arr=return_library_array("select task_template_id, group_concat(tna_task_id) as tna_task_id  from tna_task_template_details where task_type=1 and is_deleted=0 and status_active=1 group by task_template_id","task_template_id",'tna_task_id');
	}
	else
	{
		$tast_tmp_id_arr=return_library_array("select task_template_id, listagg(cast(tna_task_id as varchar(4000)),',') within group(order by tna_task_id) as tna_task_id  from tna_task_template_details where task_type=1 and is_deleted=0 and status_active=1 group by task_template_id","task_template_id",'tna_task_id');
	}
	
	     //echo 	 $sql;
	$data_sql= sql_select($sql);
	
	$width=(count($tna_task_id)*160)+900;
	
	
	ob_start();
	
	?>
   <div style="margin:0 1%;">
        <span style="background:#FF0000; padding:0 6px; border-radius:9px; cursor:pointer;" title="Red">&nbsp;</span>&nbsp; Target Date Over. &nbsp;&nbsp;
        <span style="background:#2A9FFF; padding:0 6px; border-radius:9px; cursor:pointer;" title="Blue">&nbsp;</span>&nbsp; Done in late. &nbsp;&nbsp;
        <span style="background:#FFFF00; padding:0 6px; border-radius:9px; cursor:pointer;" title="Yellow">&nbsp;</span>&nbsp; Reminder.
        
        <span style="background:#0000FF; padding:0 6px; border-radius:9px; cursor:pointer;" title="Royal Blue">&nbsp;</span>&nbsp; Manual Update Plan.
        
        
    </div>    
    <div style="width:<? echo $width+200; ?>px" align="left">
    <table width="<? echo $width+140; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
    	<thead>
        	<tr>
            	<th width="40" rowspan="2">SL</th>
                <th width="80" rowspan="2">Merchant</th>
                <th width="70" rowspan="2">Buyer Name</th>
                <th width="110" rowspan="2">PO Number</th>
                <th width="90" rowspan="2">PO Qty.</th>
                <th width="70" rowspan="2">File No.</th>
                <th width="70" rowspan="2">Int. Ref. No</th>
                <th width="30" rowspan="2">SMV</th>
                <th width="120" rowspan="2">Style Ref.</th> 
                <th width="40" rowspan="2">Job No.</th>
                <th width="100" rowspan="2">Shipment Date</th>
                <th width="60" rowspan="2">PO Insert Date</th>
                
                <th width="90" rowspan="2">Status</th>
                <?
					$i=0;
					foreach($tna_task_array as $task_name=>$key)
					{
						$i++;
						if(count($tna_task_array)==$i) echo '<th width="160" colspan="2" title="'.$tna_task_name_arr[$task_name].'='.$tna_task_name_array[$task_name].'">'. $key.'</th>'; else echo '<th width="160" colspan="2" title="'.$tna_task_name_arr[$task_name].'='.$tna_task_name_array[$task_name].'">'.$key.'</th>';
					}
					echo '</tr><tr>';
					
					$i=0;
					
					foreach($tna_task_array as $key)
					{
						$i++;
						if(count($tna_task_array)==$i) echo '<th width="80" title="plan_start_date=(ship date-deadline)-(execution_days-1)">Start</th><th width="80" title="plan_start_date=(ship date-deadline)-(execution_days-1)"> Finish</th>'; else echo '<th width="80">Start</th><th width="80"> Finish</th>';
					}
					echo '</tr>';
					 
				?>
                </thead>
                </table>
         </div>
         
         <? //echo "saju1_".count($tna_task_array); die; ?>
         
        	<div style="overflow-y:scroll; max-height:360px; width:<? echo $width+170; ?>px;" align="left" id="scroll_body">
          	<table width="<? echo $width+140; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
       
    <?
	
	$tid=0;
	$i=1;
	$count=0;
	$kid=1;
	$new_job_no=array();
	$h=0;
	$tot_po_qty=0;
	foreach ($data_sql as $row)
	{
		 
		if (!in_array($row[csf('job_no')],$new_job_no))
		{
			$new_job_no[]=$row[csf('job_no')];
		}
		 if($row[csf('po_number_id')]==0)
		 {
			 foreach($tna_task_id as $vid=>$key)
			 {
				if ($row[csf('status').$key]!="") $new_approval_arr[$row[csf('job_no')]][$key]=$row[csf('status').$key];
			 }
		 }
		 else
		 {
			 $h++;
			 if ($h%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
							
		?>
        		<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onClick="change_color('tr_<? echo $h;?>','<? echo $bgcolor;?>')" id="tr_<? echo $h; ?>">
                    <td width="40" rowspan="4" align="center"><? echo $kid++;?></td>
                    <td width="80" rowspan="4"><? 
					echo $team_member_name[$wo_po_details_master[$row[csf('po_number_id')]]['dealing_marchant']]; 
					
					?></td>
                    <td width="70" rowspan="4"><? echo $buyer_name[$wo_po_details_master[$row[csf('po_number_id')]]['buyer_name']]; ?></td>
                    <td width="110" rowspan="4" align="center"><p>
						<? 
                            //echo $wo_po_details_master[$row[csf('job_no')]][$row[csf('po_number_id')]]; 
							echo "<a href='#report_details' style='color:#990000' onclick= \"progress_comment_popup('".$row[csf('job_no')]."','".$row[csf('po_number_id')]."','".$row[csf('template_id')]."','".$tna_process_type."');\">".$wo_po_details_master[$row[csf('po_number_id')]]['po_number']."</a>";
						
						
                        ?>
                   </p> </td>
                    
                    <td width="90" rowspan="4" align="right"><p>
						<?
							$po_qty=return_field_value("po_quantity", "wo_po_break_down", "id='".$row[csf('po_number_id')]."' and status_active=1 and is_deleted=0"); 
							echo number_format($po_qty);
							$tot_po_qty+=$po_qty;
						?>
                        </p>
                    </td>
                    <td width="70" rowspan="4"><p><? echo $wo_po_details_master[$row[csf('po_number_id')]]['file_no']; ?></p></td>
                    <td width="70" rowspan="4"><p><? echo $wo_po_details_master[$row[csf('po_number_id')]]['in_ref_no']; ?></p></td>
                    <td width="30" rowspan="4" align="center"><? echo number_format($wo_po_details_master[$row[csf('po_number_id')]]['set_smv'],2); ?></td>
                    
                    <td width="120"  rowspan="4" title="<? echo $row[csf('job_no')]; ?>"><p><? echo $wo_po_details_master[$row[csf('po_number_id')]]['style_ref_no']; ?></p></td>
                     <td width="40" rowspan="4" title=""><? echo $wo_po_details_master[$row[csf('po_number_id')]]['job_no_prefix_num']; ?></td>
                     
                     
                     <? 
					 	if($tna_process_type==1)
						{
							$lead_timee="Template Lead Time: ".$lead_time_array[$row[csf('template_id')]];
						}
						else
						{
							$lead_timee="Lead Time: ".($row[csf('template_id')]+1);
						}
						$po_lead_time=datediff( "d", date("Y-m-d",strtotime(change_date_format($row[csf('po_receive_date')]))), date("Y-m-d",strtotime(change_date_format($row[csf('shipment_date')]))) );

					 ?>
                     
                     
                    <td width="100" rowspan="4" title="<? echo $lead_timee."; "." PO. Rec. Date: ".change_date_format($row[csf('po_receive_date')]); ?>"><? echo change_date_format($row[csf('shipment_date')])."<br>"." ".$lead_timee."<br>"." PO Lead Time:".($po_lead_time-1);  ?></td>
                    <td width="60" rowspan="4" align="center"><? echo date("d-m-Y",strtotime($row[csf('insert_date')]));?></td>
                    <td width="90">Plan</td>
                <?
 
	
					 $tast_id_arr=array_unique(explode(',',$tast_tmp_id_arr[$row[csf('template_id')]]));
					 $i=0;
					 foreach($tna_task_id as $vid=>$key)
					 {
						 $i++;
						
						if ( $new_approval_arr[$row[csf('job_no')]][$key]=="") $new_data=explode("_",$row[csf('status').$key]); 
						else $new_data=explode("_",$new_approval_arr[$row[csf('job_no')]][$key]);
						if($new_data[7]!="") $function="onclick='update_tna_process(1,$new_data[7],".$row[csf('po_number_id')].")'"; else $function="";
						
						//if(!in_array($vid,$manual_update_task_arr)){$function="";}

						
						if($new_data[9]==1){$psc=" style='color:#0000FF'";}else{$psc="";}
						if($new_data[10]==1){$pfc=" style='color:#0000FF'";}else{$pfc="";}
						
						
						if(in_array($vid,$tast_id_arr))
						{
							if(count($tna_task_id)==$i)
								echo '<td align="center" '.$psc.'   width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? "<span style='color:#FF0000'> N/A </span>" : change_date_format($new_data[2])).'</td><td align="center" '.$pfc.' '.$function.'> '.($new_data[3]== "N/A"  || $new_data[3]=="0000-00-00"? "" : change_date_format($new_data[3])).'</td>';
								
							 else
								echo '<td align="center" '.$psc.'  width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? "<span style='color:#FF0000'> N/A </span>" : change_date_format($new_data[2])).'</td><td  align="center" '.$pfc.'width="80" '.$function.'> '.($new_data[3]== ""  || $new_data[3]=="0000-00-00"? "<span style='color:#FF0000'> N/A </span>" : change_date_format($new_data[3])).'</td>';
						}
						else
						{
							if(count($tna_task_id)==$i)
								echo '<td align="center" '.$psc.'   width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? "N/A" : change_date_format($new_data[2])).'</td><td align="center" '.$pfc.' '.$function.'> '.($new_data[3]== "N/A"  || $new_data[3]=="0000-00-00"? "" : change_date_format($new_data[3])).'</td>';
								
							 else
								echo '<td align="center" '.$psc.'  width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? "N/A" : change_date_format($new_data[2])).'</td><td align="center" '.$pfc.' width="80" '.$function.'> '.($new_data[3]== ""  || $new_data[3]=="0000-00-00"? "N/A" : change_date_format($new_data[3])).'</td>';
						}
						
						
					 }
					echo '</tr>';
					
					echo '<tr><td width="90">Actual</td>';
					$i=0;
					 foreach($tna_task_id as $vid=>$key)
					 {
						  
						 $i++;
						if ( $new_approval_arr[$row[csf('job_no')]][$key]=="") $new_data=explode("_",$row[csf('status').$key]);
						else $new_data=explode("_",$new_approval_arr[$row[csf('job_no')]][$key]);
						
						if( $new_data[7]!="") $function="onclick='update_tna_process(2,$new_data[7],".$row[csf('po_number_id')].")'";  else $function="";
						$bgcolor1=""; $bgcolor="";
						
						if(!in_array($vid,$manual_update_task_arr)){$function="";}
						
						
						if (trim($new_data[2])!= $blank_date) 
						{
							
							
							if (strtotime($new_data[4])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($new_data[2]))  $bgcolor="#FFFF00";//Yellow
							else if (strtotime($new_data[2])<strtotime(date("Y-m-d",time())))  $bgcolor="#FF0000";//Red
							else $bgcolor="";
							
						}
						 
						if ($new_data[3]!= $blank_date) {
							if (strtotime($new_data[5])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($new_data[3]))  $bgcolor1="#FFFF00";
							else if (strtotime($new_data[3])<strtotime(date("Y-m-d",time())))  $bgcolor1="#FF0000"; else $bgcolor1="";
						}
						
						if ($new_data[0]!=$blank_date) $bgcolor="";
						if ($new_data[1]!=$blank_date) $bgcolor1="";
						
						
						$idd=$row[csf('job_no')]."".$row[csf('po_number_id')]."".$key;
						if(count($tna_task_id)==$i)
							echo '<td align="center" title="Click Here to Edit Date" id="'.$idd.'1" '.$function.' width="80" bgcolor="'.$bgcolor.'">'.($new_data[0]== "" || $new_data[0]=="0000-00-00" ? "" : change_date_format($new_data[0])).'</td><td id="'.$idd.'2" title="Click Here to Edit Date" '.$function.' bgcolor="'.$bgcolor1.'" title="'.$new_data[6].'">'.($new_data[1]== "" || $new_data[1]=="0000-00-00" ? "" : change_date_format($new_data[1])).'</td>';
							
						else
							echo '<td align="center" id="'.$idd.'1" title="Click Here to Edit Date"  '.$function.' width="80" bgcolor="'.$bgcolor.'">'.($new_data[0]== "" || $new_data[0]=="0000-00-00" ? "" : change_date_format($new_data[0])).'</td><td id="'.$idd.'2" title="Click Here to Edit Date" '.$function.' width="80" bgcolor="'.$bgcolor1.'" title="'.$new_data[6].'">'.($new_data[1]== "" || $new_data[1]=="0000-00-00" ? "" : change_date_format($new_data[1])).'</td>';
						
					 }
					echo '</tr>'; 
					
					
					echo '<tr style="background:#CCC"><td width="90" valign="middle">Commitment</td>';
					 foreach($tna_task_id as $vid=>$key)
					 {
						$new_data=explode("_",$row[csf('status').$key]);
						$function="onclick='update_tna_process(3,".$new_data[7].",".$row[csf('po_number_id')].")'";
							
							echo '<td align="center" width="80" '.$function.'>'.($new_data[11]== "" || $new_data[11]=="0000-00-00" ? "" : change_date_format($new_data[11])).'</td><td align="center" '.$pfc.' width="80" '.$function.'> '.($new_data[12]== ""  || $new_data[12]=="0000-00-00"? "" : change_date_format($new_data[12])).'</td>';

						
					 }
					echo '</tr>'; 
					
					echo '<tr><td width="90">Variance</td>';
					$j=0;
					foreach($tna_task_id as $vid=>$key)
					{
						 $j++;
						if ( $new_approval_arr[$row[csf('job_no')]][$key]=="") $new_data=explode("_",$row[csf('status').$key]); 
						else $new_data=explode("_",$new_approval_arr[$row[csf('job_no')]][$key]);
						
						$bgcolor1=""; $bgcolor="";
						
						
						if($new_data[0]!=$blank_date)
						{
							$start_diff1 = datediff( "d", $new_data[0], $new_data[2]);
							if($new_data[0]== "")
							{
								$start_diff=$start_diff1;
							}
							else
							{
								$start_diff=$start_diff1-1;
							}
							if($start_diff<0)
							{
								$bgcolor="#2A9FFF"; //Blue
							}
							if($start_diff>0)
							{
								$bgcolor="";
							}
						}
						else
						{
							if(strtotime(date("Y-m-d"))>strtotime($new_data[2]))
							{
								$start_diff1 = datediff( "d", $new_data[2], date("Y-m-d"));
								if($new_data[0]== "")
								{
									//$start_diff=-abs($start_diff1);
									$start_diff=-abs($start_diff1-1);
								}
								else
								{
									$start_diff=-abs($start_diff1-1);
								}
								//$bgcolor="#FF0000";		//Red
								$bgcolor=($new_data[2]== "" || $new_data[2]=="0000-00-00")?'':'#FF0000';
							}
							if(strtotime(date("Y-m-d"))<=strtotime($new_data[2]))
							{
								$start_diff = "";
								$bgcolor="";
							}
						}
						if($new_data[1]!=$blank_date)
						{
							$finish_diff1 = datediff( "d", $new_data[1], $new_data[3]);
							if($new_data[0]== "")
							{
								$finish_diff=$finish_diff1;
							}
							else
							{	
								$finish_diff=$finish_diff1-1;
							}
							if($finish_diff<0)
							{
								$bgcolor1="#2A9FFF";
							}
							if($finish_diff>0)
							{	
								$bgcolor1="";
							}
						}
						else
						{
							if(strtotime(date("Y-m-d"))>strtotime($new_data[3]))
							{
								
								$finish_diff1 = datediff( "d", $new_data[3], date("Y-m-d"));
								if($new_data[1]== "")
								{
									//$finish_diff=-abs($finish_diff1);
									$finish_diff=-abs($finish_diff1-1);
								}
								else
								{
									$finish_diff=-abs($finish_diff1-1);
								}
								//$bgcolor1="#FF0000";
								$bgcolor1=($new_data[3]== "" || $new_data[3]=="0000-00-00")?'':'#FF0000';
							}
							if(strtotime(date("Y-m-d"))<=strtotime($new_data[3]))
							{
								
								$finish_diff = "";
								$bgcolor1="";
							}
						}
						
						
						
						if(count($tna_task_id)==$j)
							
							echo '<td width="80" align="center" bgcolor="'.$bgcolor.'">'.($start_diff).'</td><td width="80" bgcolor="'.$bgcolor1.'" align="center">'.($finish_diff).'</td>';
						else
							echo '<td width="80" align="center" bgcolor="'.$bgcolor.'">'.($start_diff).'</td><td width="80" bgcolor="'.$bgcolor1.'" align="center">'.($finish_diff).'</td>';
					}
					 
					echo '</tr>';
					
					
					 
		 }
				 
	}
		?>
     
     
    </table>
    </div>
    <div style="width:<? echo $width+140; ?>px;" align="left">
         <table width="100%" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <tfoot>
                <th width="40"></th>
                <th width="80"></th>
                <th width="69"></th>
                <th width="109">Total</th>
                <th width="90" id="total_po_qty" align="right"><p><? echo number_format($tot_po_qty,2);?></p></th>
                <th width="69"></th>
                <th width="70"></th>
                <th width="29"></th>
                <th colspan="<? echo (count($tna_task_id)*2)+4;?>"></th>
            </tfoot>
        </table>
    </div>
    
    
          <?
		  
		 $sql = sql_select("select designation,name from variable_settings_signature where report_id=95 and company_id=$cbo_company_id order by sequence_no" );
	     $count=count($sql);

		$width=$width+170;
		$td_width=floor($width/$count);
		
		$standard_width=$count*150;
		
		if($standard_width>$width) $td_width=150;
		
		$no_coloumn_per_tr=floor($width/$td_width);
		$col=$count-2;
		$i=1;
		echo '<table width="'.$width.'"><tr><td width="'.$td_width.'" align="center" valign="bottom">'.$user_arr[$inserted_by].'</td><td height="70" colspan="'.$col.'"></td><td width="'.$td_width.'" align="center" valign="bottom">'.$user_arr[$nameArray_approved_date_row[csf('approved_by')]].'</td></tr><tr>';
		foreach($sql as $row)	
		{
			echo '<td width="'.$td_width.'" align="center" valign="top"><strong style="text-decoration:overline">'.$row[csf("designation")]."</strong><br>".$row[csf("name")].'</td>';
			
			if($i%$no_coloumn_per_tr==0) echo '</tr><tr><td height="70" colspan="'.$no_coloumn_per_tr.'"></td><tr>';
			$i++;
		} 
		echo '</tr></table>';



	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
 	echo "$total_datass****$filename";
	exit();
}

if($action=='task_surch')
{
	
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
    <script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() {
			var tbl_row_count = document.getElementById('list_view').rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
			//alert(strCon);
				var splitSTR = strCon.split("_");
				var str_or = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
				
				toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );
				
				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );
					selected_no.push( str_or );				
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
					selected_no.splice( i, 1 ); 
				}
				var id = ''; var name = ''; var job = ''; var num='';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
					num += selected_no[i] + ','; 
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 ); 
				num 	= num.substr( 0, num.length - 1 );
				//alert(num);
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name ); 
				$('#txt_selected_no').val( num );
		}
		
		function window_close()
		{
			parent.emailwindow.hide();
		}
    </script>
    <?
	$company=str_replace("'","",$company);
	$cbo_task_group=str_replace("'","",$cbo_task_group);
	
	
		
	if($db_type==0){$task_group_con=" and task_group!=''";}else{$task_group_con=" and task_group is not null";}
	if($cbo_task_group){$task_group_con.=" and task_group in('$cbo_task_group')";}


	$sql =sql_select("select id,task_name,task_short_name from lib_tna_task where status_active=1 and is_deleted=0  AND task_type = 1 $task_group_con order by task_sequence_no"); 
	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	?>
    <div style="width:400px" align="left"> 
    <table width="382" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
    	<thead>
        	<th width="50">SL</th>
            <th width="200">Task Name</th>
            <th>Short Name</th>
        </thead>
    </table>
    </div>
    <div style="width:400px; overflow-y: scroll; max-height:300px;" id="scroll_body" align="left">
    <table width="382" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="list_view">
    	<tbody>
        <?
		$i=1;
		foreach($sql as $row)
		{
			if ($i%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			?>
        	<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $i; ?>_<? echo $row[csf("task_name")]; ?>_<? echo $tna_task_name[$row[csf("task_name")]]; ?>')" id="tr_<? echo $i; ?>" style="cursor:pointer;">
                <td width="50" align="center"><? echo $i; ?></td>
                <td width="200"><p><? echo $tna_task_name[$row[csf("task_name")]]; ?>&nbsp;</p></td>
                <td><p><? echo $row[csf("task_short_name")]; ?>&nbsp;</p></td>
            </tr>
            <?
			$i++;
		}
		?>
        </tbody>
    </table>
    </div>
    <div style="width:400px" align="left"> 
    <table width="382" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
    	<tbody>
        	<td width="50" align="center"><input type="checkbox" id="chk_all" onClick="check_all_data()" ></th>
            <td align="center"><input type="button" id="btn_close" value="Close" class="formbutton" style="width:100px;" onClick="window_close()" align="middle">
	</th>
        
	</tbody>
    </table>
    </div>
    <?
	
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $tna_task_id_no;?>';
	var style_id='<? echo $tna_task_id;?>';
	var style_des='<? echo $tna_task;?>';
	//alert(style_id);
	if(style_no!="")
	{
		style_no_arr=style_no.split(",");
		style_id_arr=style_id.split(",");
		style_des_arr=style_des.split(",");
		var str_ref="";
		for(var k=0;k<style_no_arr.length; k++)
		{
			str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k];
			js_set_value(str_ref);
		}
	}
	</script>
    
    <?
	
	exit();
}

if($action=="generate_task_wise_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$tna_task_id=str_replace("'","",$tna_task_id);
	
	if(str_replace("'","",$cbo_company_name)==0) $cbo_company_name=""; else $cbo_company_name=" and a.company_name = $cbo_company_name";
	if(str_replace("'","",$cbo_buyer_name)==0) $cbo_buyer_name=""; else $cbo_buyer_name=" and a.buyer_name = $cbo_buyer_name";
	if(str_replace("'","",$cbo_team_member)==0) $cbo_team_member=""; else $cbo_team_member=" and a.dealing_marchant = $cbo_team_member";
	if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and p.task_start_date between $txt_date_from and $txt_date_to";
	$order_status_cond="";
	if(str_replace("'","",$cbo_order_status)>0) $order_status_cond=" and b.is_confirmed=$cbo_order_status";
	
	$lead_time_array=return_library_array("select task_template_id,lead_time from tna_task_template_details where task_type=1 and is_deleted=0 and status_active=1 group by lead_time","task_template_id",'lead_time');
	$txt_job_no=str_replace("'","",$txt_job_no);
	if($txt_job_no=="") $txt_job_no=""; else $txt_job_no=" and a.job_no_prefix_num ='$txt_job_no'";
	$txt_order_no=str_replace("'","",$txt_order_no);
	if($txt_order_no=="") $txt_order_no=""; else $txt_order_no=" and b.po_number ='$txt_order_no'";
	$txt_style_ref_no=str_replace("'","",$txt_style_ref_no);
	if($txt_style_ref_no=="") $txt_style_ref_no=""; else $txt_style_ref_no=" and a.style_ref_no ='$txt_style_ref_no'";
	
	
	//echo $date_range;
	
	//**txt_date_from*txt_date_to*txt_job_no
	
	
	/*	$tna_all_task=implode(",",$tna_task_id);
		$sql = "SELECT a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_no_prefix_num,a.dealing_marchant,b.id,b.po_number FROM  wo_po_details_master a,  wo_po_break_down b WHERE a.job_no=b.job_no_mst $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no";  //$cbo_company_name $cbo_buyer_name $txt_job_no $cbo_team_name  and a.job_no='ASL-13-00173'
	// echo $sql; die;
		$result = sql_select( $sql ) ;
		$wo_po_details_master = array();
		$po_no_arr=array();
		foreach( $result as  $row ) 
		{	
			$wo_po_details_master[$row[csf('id')]][('company_name')]=$row[csf('company_name')];
			$wo_po_details_master[$row[csf('id')]][('buyer_name')]=$row[csf('buyer_name')];
			$wo_po_details_master[$row[csf('id')]][('style_ref_no')]=$row[csf('style_ref_no')];
			$wo_po_details_master[$row[csf('id')]][('job_no_prefix_num')]=$row[csf('job_no_prefix_num')];
			$wo_po_details_master[$row[csf('id')]][('dealing_marchant')]=$row[csf('dealing_marchant')];
			$wo_po_details_master[$row[csf('id')]]['po_number']= $row[csf('po_number')];
		}
	*/	
	
	if($db_type==0)
	{
		$sql ="select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_no_prefix_num,a.dealing_marchant,year(a.insert_date) as job_year,b.po_number,b.shipment_date,b.po_quantity, p.po_number_id,p.po_receive_date, p.task_number, p.template_id, min(case when task_start_date!='0000-00-00' then task_start_date end) as task_start_date, max(task_finish_date) as task_finish_date, min(case when actual_start_date!='0000-00-00' then actual_start_date end) as actual_start_date, max(actual_finish_date) as actual_finish_date 
		from  tna_process_mst p,wo_po_details_master a,  wo_po_break_down b
		where p.po_number_id=b.id and b.job_no_mst=a.job_no and b.status_active!=3 and b.shiping_status !=3  and p.task_number in($tna_task_id) $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no  $order_status_cond
		group by a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_no_prefix_num,a.dealing_marchant,a.insert_date,b.po_number,b.shipment_date,b.po_quantity, p.po_number_id,po_received_date, p.task_number, p.template_id
		order by task_number,shipment_date,po_number_id,job_no";
	}
	else
	{
		$sql ="select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_no_prefix_num,a.dealing_marchant,to_char(a.insert_date,'YYYY') as job_year,b.po_number,b.shipment_date,b.po_quantity, p.po_number_id,p.po_receive_date, p.task_number, p.template_id, min(case when  p.task_start_date is not null  then p.task_start_date end) as task_start_date, max(p.task_finish_date) as task_finish_date, min(case when  p.task_start_date is not null then p.actual_start_date end) as actual_start_date, max(p.actual_finish_date) as actual_finish_date 
		from  tna_process_mst p,wo_po_details_master a,  wo_po_break_down b
		where p.po_number_id=b.id and b.job_no_mst=a.job_no and b.status_active!=3 and b.shiping_status !=3 and p.task_number in($tna_task_id) $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no $order_status_cond 
		group by a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_no_prefix_num,a.dealing_marchant,a.insert_date,b.po_number,b.po_quantity,b.shipment_date,p.po_receive_date, p.po_number_id, p.task_number, p.template_id
		order by p.task_number,b.shipment_date,p.po_number_id,a.job_no";
	}
	 //echo $sql;
	$sql_result=sql_select($sql);
	
	ob_start();
	
	?>
    <div style="width:9300px" align="left">
        <table width="910" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <thead>
                <tr>
                    <th width="40" >SL</th>
                    <th width="80" >Buyer</th>
                    <th width="40" >Job Year.</th>
                    <th width="60" >Job No.</th>
                    <th width="100" >Style Ref.</th> 
                    <th width="100" >PO Number</th>
                    <th width="70" >Shipment Date</th>
                    <th width="70" >PO Recv Date</th>
                    <th width="60">Lead Time</th>
                    <th width="70">Gmts.Qty.</th>
                    <th width="80">Status</th>
                    <th width="70">Start Date</th>
                    <th width="70">End Date</th>
                </tr>
            </thead>
        </table>
    </div>
    
    <div style="overflow-y:scroll; max-height:330px; width:930px;" align="left" id="scroll_body">
    	<table width="910" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="">
		<?
		$task_short_name= return_library_array("select task_name,task_short_name from lib_tna_task where is_deleted = 0 and status_active=1  AND task_type = 1 order by task_sequence_no asc","task_name","task_short_name");
	
		$i=1;$temp_arr=array();
        foreach ($sql_result as $row)
        {
			if ($i%2==0)  
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";	
			if(!in_array($row[csf("task_number")],$temp_arr))
			{
				$temp_arr[]=$row[csf("task_number")];
				?>
                <tr bgcolor="#FFFFCC">
                	<td colspan="5" style="font-size:20px; font-weight:bold;" align="left"><? echo $task_short_name[$row[csf("task_number")]]; ?></td><td colspan="8" style="font-size:20px; font-weight:bold;" align="right"><? echo $tna_task_name[$row[csf("task_number")]]; ?></td>
                </tr>
                <?
			}
			//echo $wo_po_details_master[$row[csf('job_no')]][csf('dealing_marchant')]."**";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                <td width="40" rowspan="3"><? echo $i++;?></td>
                <td width="80" rowspan="3"><p><? echo $buyer_short_name_arr[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
                <td width="40" rowspan="3" align="center"><p><? echo $row[csf('job_year')];?>&nbsp;</p></td>
                <td width="60" rowspan="3"  align="center"><p><? echo $row[csf('job_no_prefix_num')];?>&nbsp;</p></td>
                <td width="100"  rowspan="3" ><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
                <td width="100" rowspan="3" ><p><? echo $row[csf('po_number')]; ?>&nbsp;</p></td>
                <td width="70" rowspan="3" align="center"><p><? if($row[csf('shipment_date')]!='' && $row[csf('shipment_date')]!='0000-00-00') echo change_date_format($row[csf('shipment_date')]); else echo '&nbsp;'; ?>&nbsp;</p></td>
                <td width="70" rowspan="3" align="center"><p><? if($row[csf('po_receive_date')]!='' && $row[csf('po_receive_date')]!='0000-00-00') echo change_date_format($row[csf('po_receive_date')]);  else echo '&nbsp;'; ?></p></td>
                <td width="60" rowspan="3" align="center"><p>
				<?
				if($tna_process_type==1)
				{
					$lead_timee=$lead_time_array[$row[csf('template_id')]];
				}
				else
				{
					$lead_timee=$row[csf('template_id')];
				}
				echo $lead_timee; 
				//echo $lead_time[$row[csf('template_id')]]; 
				?>
                &nbsp;</p></td>
                <td width="70" rowspan="3" align="right" style="padding-right:5px;"><p><? echo number_format($row[csf('po_quantity')],0);  ?></p></td>
                <td width="80">As Per TNA</td>
                <td width="70" align="center"><p><? if($row[csf('task_start_date')]!='' && $row[csf('task_start_date')]!='0000-00-00') echo change_date_format(trim($row[csf('task_start_date')]));  else echo '&nbsp;'; ?>&nbsp;</p></td>
                <td width="70" align="center"><p><? if($row[csf('task_finish_date')]!='' && $row[csf('task_finish_date')]!='0000-00-00') echo change_date_format(trim($row[csf('task_finish_date')]));  else echo '&nbsp;'; ?>&nbsp;</p></td>
            </tr>
            
            <?
			$start_diff1=$start_diff=$end_diff1=$end_diff=$bgcolor=$bgcolor_end="";
			if ($row[csf('actual_start_date')]!=$blank_date)
			{
				$bgcolor="";
			} 
			else
			{
				if(date("Y-m-d")>date("Y-m-d",strtotime($row[csf('task_start_date')])))
				{
					$bgcolor="#FF0000";
				}
			} 
			if ($row[csf('actual_finish_date')]!=$blank_date)
			{
				$bgcolor_end="";
			} 
			else
			{
				if(date("Y-m-d")>date("Y-m-d",strtotime($row[csf('task_finish_date')])))
				{
					$bgcolor_end="#FF0000";
				}
			} 
			
			
			?>
            <tr>
            	<td width="80">Actual</td>
                <td width="70" align="center" bgcolor="<? echo $bgcolor; ?>"><p><? if($row[csf('actual_start_date')]!='' && $row[csf('actual_start_date')]!='0000-00-00') echo change_date_format(trim($row[csf('actual_start_date')])); else echo '&nbsp;';  ?>&nbsp;</p></td>
                <td width="70" align="center" bgcolor="<? echo $bgcolor_end; ?>"><p><? if($row[csf('actual_finish_date')]!='' && $row[csf('actual_finish_date')]!='0000-00-00') echo change_date_format(trim($row[csf('actual_finish_date')])); else echo '&nbsp;';  ?>&nbsp;</p></td>
            </tr>
            <?
			$start_diff=$end_diff="";
			if(trim($row[csf('actual_start_date')])!='' && trim($row[csf('actual_start_date')])!='0000-00-00')
			{ 
			
				$start_diff1 = datediff( "d", $row[csf('actual_start_date')], $row[csf('task_start_date')]);
				$start_diff=$start_diff1-1;
				if($start_diff<0)
				{
					$bgcolor="#2A9FFF"; //Blue
				}
				if($start_diff>0)
				{
					$bgcolor="";
				}
			}
			else
			{
				
				if(date("Y-m-d")>date("Y-m-d",strtotime($row[csf('task_start_date')])))
				{
					$start_diff1 = datediff( "d",  $row[csf('task_start_date')], date("Y-m-d"));
					$start_diff=-abs($start_diff1-1);
					$bgcolor="#FF0000";		//Red
				}
				if(date("Y-m-d")<=date("Y-m-d",strtotime($row[csf('task_start_date')])))
				{
					$start_diff = "";
					$bgcolor="";
				}
			}
			
			
			if(trim($row[csf('actual_finish_date')])!='' && trim($row[csf('actual_finish_date')])!='0000-00-00')
			{
				$end_diff1 = datediff( "d", $row[csf('actual_finish_date')], $row[csf('task_finish_date')]);
				$end_diff=$end_diff1-1;
				if($end_diff<0)
				{
					$bgcolor_end="#2A9FFF"; //Blue
				}
				if($end_diff>0)
				{
					$bgcolor_end="";
				}
			}
			else
			{
				if(date("Y-m-d")>date("Y-m-d",strtotime($row[csf('task_finish_date')])))
				{
					$end_diff1 = datediff( "d",  $row[csf('task_finish_date')], date("Y-m-d"));
					$end_diff=-abs($end_diff1-1);
					$bgcolor_end="#FF0000";		//Red
				}
				if(date("Y-m-d")<=date("Y-m-d",strtotime($row[csf('task_finish_date')])))
				{
					$end_diff1 = "";
					$bgcolor_end="";
				}
			}
			?>
            <tr>
            	<td width="80">Deviation</td>
                
                <td width="70" bgcolor="<? echo $bgcolor; ?>" align="center"><p><? echo $start_diff; ?>&nbsp;</p></td>
                <td width="70" bgcolor="<? echo $bgcolor_end; ?>" align="center"><p><? echo $end_diff; ?>&nbsp;</p></td>
            </tr>
			<?
        }
        ?>
    </table>
    </div>
    <?
	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
 	echo "$total_datass****$filename";
	exit();
    
	
}


if($action=="generate_overdew_task_wise_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$tna_task_id=str_replace("'","",$tna_task_id);
	//echo $pc_date;die;
	
	if(str_replace("'","",$cbo_company_name)==0) $cbo_company_name=""; else $cbo_company_name=" and a.company_name = $cbo_company_name";
	if(str_replace("'","",$cbo_buyer_name)==0) $cbo_buyer_name=""; else $cbo_buyer_name=" and a.buyer_name = $cbo_buyer_name";
	if(str_replace("'","",$cbo_team_member)==0) $cbo_team_member=""; else $cbo_team_member=" and a.dealing_marchant = $cbo_team_member";
	
	if(str_replace("'","",$cbo_search_type)==1){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
	}
	else if(str_replace("'","",$cbo_search_type)==3){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and c.country_ship_date between $txt_date_from and $txt_date_to";
	}
	else if(str_replace("'","",$cbo_search_type)==4){
		if($db_type==0)
		{
			if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)==""){$date_range="";}else{ 
			$date_range=" and b.insert_date between ".$txt_date_from." and ".$txt_date_to."";}
		}
		else
		{
			
			if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)==""){$date_range="";}else{ 
			$date_range=" and b.insert_date between ".$txt_date_from." and '".str_replace("'","",$txt_date_to)." 11:59:59 PM'";}
		}
	}
	else
	{
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.po_received_date between $txt_date_from and $txt_date_to";
	}
	
	//if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.shipment_date between $txt_date_from and $txt_date_to";
	
	$order_status_cond="";

	if(str_replace("'","",$cbo_order_status)>0) $order_status_cond=" and b.is_confirmed=$cbo_order_status";
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if($txt_job_no=="") $txt_job_no=""; else $txt_job_no=" and a.job_no_prefix_num ='$txt_job_no'";
	$txt_order_no=str_replace("'","",$txt_order_no);
	if($txt_order_no=="") $txt_order_no=""; else $txt_order_no=" and b.po_number ='$txt_order_no'";
	$txt_style_ref_no=str_replace("'","",$txt_style_ref_no);
	if($txt_style_ref_no=="") $txt_style_ref_no=""; else $txt_style_ref_no=" and a.style_ref_no ='$txt_style_ref_no'";
	
	if(str_replace("'","",$cbo_shipment_status)==4){$shipment_status_con=" and b.shiping_status=3";}
	else if(str_replace("'","",$cbo_shipment_status)==1){$shipment_status_con=" and b.shiping_status !=3";}
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	
	
	if(str_replace("'","",$cbo_search_type)==3)
	{
	$sql_total_task=sql_select("select p.task_number, count(p.id) as total_task
			from  tna_process_mst p, wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
			where p.po_number_id=b.id and b.job_no_mst=a.job_no and b.id=c.po_break_down_id and a.status_active=1 and b.status_active=1 and p.task_number in(10,29,31,47,60,84,86,88,110) $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no $order_status_cond  $shipment_status_con
			group by p.task_number");
	}
	else
	{
		$sql_total_task=sql_select("select p.task_number, count(p.id) as total_task
			from  tna_process_mst p, wo_po_details_master a, wo_po_break_down b
			where p.po_number_id=b.id and b.job_no_mst=a.job_no and a.status_active=1 and b.status_active=1 and p.task_number in(10,29,31,47,60,84,86,88,110) $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no $order_status_cond  $shipment_status_con
			group by p.task_number");
	}
	$total_task_data=array();
	foreach($sql_total_task as $row)
	{
		$total_task_data[$row[csf("task_number")]]=$row[csf("total_task")];
	}
	unset($sql_total_task);
	if(str_replace("'","",$cbo_search_type)==3)
	{
		if($db_type==0)
		{
			$sql ="select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_no_prefix_num,a.dealing_marchant,year(a.insert_date) as job_year,b.po_number,c.country_ship_date as shipment_date,b.po_quantity, p.po_number_id,p.po_receive_date, p.task_number, p.template_id, min(case when task_start_date!='0000-00-00' then task_start_date end) as task_start_date, max(task_finish_date) as task_finish_date 
			from  tna_process_mst p,wo_po_details_master a,  wo_po_break_down b, wo_po_color_size_breakdown c
			where p.po_number_id=b.id and b.job_no_mst=a.job_no and b.id=c.po_break_down_id and a.status_active=1 and b.status_active=1 and p.task_number in(10,29,31,47,60,84,86,88,110) and (p.task_start_date<'$pc_date' or p.task_finish_date<'$pc_date') and (p.actual_start_date='0000-00-00' or p.actual_finish_date='0000-00-00')  $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no  $order_status_cond $shipment_status_con
			group by a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_no_prefix_num,a.dealing_marchant,a.insert_date,b.po_number,b.po_quantity, p.po_number_id,po_received_date, p.task_number, p.template_id,c.country_ship_date
			order by cast(p.task_number AS UNSIGNED)";
		}
		else
		{
			$sql ="select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_no_prefix_num,a.dealing_marchant,to_char(a.insert_date,'YYYY') as job_year,b.po_number,c.country_ship_date as shipment_date,b.po_quantity, p.po_number_id,p.po_receive_date, p.task_number, p.template_id, min(case when  p.task_start_date is not null  then p.task_start_date end) as task_start_date, max(p.task_finish_date) as task_finish_date
			from  tna_process_mst p,wo_po_details_master a,  wo_po_break_down b, wo_po_color_size_breakdown c
			where p.po_number_id=b.id and b.job_no_mst=a.job_no and b.id=c.po_break_down_id and a.status_active=1 and b.status_active=1 and p.task_number in(10,29,31,47,60,84,86,88,110) and (p.task_start_date<'$pc_date' or p.task_finish_date<'$pc_date')  and (p.actual_start_date is null or p.actual_finish_date is null) $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no $order_status_cond  $shipment_status_con
			group by a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_no_prefix_num,a.dealing_marchant,a.insert_date,b.po_number,b.po_quantity,p.po_receive_date, p.po_number_id, p.task_number, p.template_id,c.country_ship_date
			order by TO_NUMBER(p.task_number, '999')";
		}
	}
	else
	{
		if($db_type==0)
		{
			$sql ="select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_no_prefix_num,a.dealing_marchant,year(a.insert_date) as job_year,b.po_number,b.shipment_date,b.po_quantity, p.po_number_id,p.po_receive_date, p.task_number, p.template_id, min(case when task_start_date!='0000-00-00' then task_start_date end) as task_start_date, max(task_finish_date) as task_finish_date 
			from  tna_process_mst p, wo_po_details_master a, wo_po_break_down b
			where p.po_number_id=b.id and b.job_no_mst=a.job_no and a.status_active=1 and b.status_active=1 and p.task_number in(10,29,31,47,60,84,86,88,110) and (p.task_start_date<'$pc_date' or p.task_finish_date<'$pc_date') and (p.actual_start_date='0000-00-00' or p.actual_finish_date='0000-00-00')  $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no  $order_status_cond $shipment_status_con
			group by a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_no_prefix_num,a.dealing_marchant,a.insert_date,b.po_number,b.shipment_date,b.po_quantity, p.po_number_id,po_received_date, p.task_number, p.template_id
			order by cast(p.task_number AS UNSIGNED)";
		}
		else
		{
			$sql ="select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_no_prefix_num,a.dealing_marchant,to_char(a.insert_date,'YYYY') as job_year,b.po_number,b.shipment_date,b.po_quantity, p.po_number_id,p.po_receive_date, p.task_number, p.template_id, min(case when  p.task_start_date is not null  then p.task_start_date end) as task_start_date, max(p.task_finish_date) as task_finish_date
			from  tna_process_mst p, wo_po_details_master a, wo_po_break_down b
			where p.po_number_id=b.id and b.job_no_mst=a.job_no and a.status_active=1 and b.status_active=1 and p.task_number in(10,29,31,47,60,84,86,88,110) and (p.task_start_date<'$pc_date' or p.task_finish_date<'$pc_date')  and (p.actual_start_date is null or p.actual_finish_date is null) $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no $order_status_cond  $shipment_status_con
			group by a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_no_prefix_num,a.dealing_marchant,a.insert_date,b.po_number,b.po_quantity,b.shipment_date,p.po_receive_date, p.po_number_id, p.task_number, p.template_id
			order by TO_NUMBER(p.task_number, '999')";
		}
	}
	
	
	//echo $sql;
	$sql_result=sql_select($sql);
	
	ob_start();
	
	?>
    <div style="width:1100px" align="left">
        <table width="1080" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <thead>
                <tr>
                    <th width="40" >SL</th>
                    <th width="120" >Buyer</th>
                    <th width="40" >Job Year.</th>
                    <th width="60" >Job No.</th>
                    <th width="120" >Style Ref.</th> 
                    <th width="120" >PO Number</th>
                    <? if(str_replace("'","",$cbo_search_type)==3)
					{
						?>
                        <th width="70" >Country Ship Date</th>
                        <?
					}
					else
					{
						?>
                        <th width="70" >Shipment Date</th>
                        <?
					}
					?>
                    
                    <th width="70">Plan Start Date</th>
                    <th width="70">Start Due Day</th>
                    <th width="70" >Plan Finish Date</th>
                    <th width="60">Finish Due Day</th>
                    <th width="120">Dealing Merchant</th>
                    <th>Contact No</th>
                </tr>
            </thead>
        </table>
    </div>
    
    <div style="overflow-y:scroll; max-height:330px; width:1100px;" align="left" id="scroll_body">
    	<table width="1080" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="">
		<?
		$marcent_sql = sql_select("SELECT team_member_name, id, member_contact_no FROM lib_mkt_team_member_info");
		$merchen_data_arr=array();
		foreach($marcent_sql as $row)
		{
			$merchen_data_arr[$row[csf("id")]]["team_member_name"]=$row[csf("team_member_name")];
			$merchen_data_arr[$row[csf("id")]]["member_contact_no"]=$row[csf("member_contact_no")];
		}
		
		$task_short_name= return_library_array("select task_name,task_short_name from lib_tna_task where is_deleted = 0 and status_active=1 AND task_type = 1 order by task_sequence_no asc","task_name","task_short_name");
		
		$i=1;$temp_arr=array();$k=0;
        foreach ($sql_result as $row)
        {
			if ($i%2==0)  
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";	
			if(!in_array($row[csf("task_number")],$temp_arr))
			{
				$temp_arr[]=$row[csf("task_number")];
				if($i!=1)
				{
					?>
                    <tr bgcolor="#E2E2E2">
                        <td colspan="13" style="font-size:20px; font-weight:bold;">Total Number of Events : <? echo $total_task_data[$task_num]; ?></td>
                    </tr>
                    <tr bgcolor="#D3D3D3">
                        <td colspan="13" style="font-size:20px; font-weight:bold;">Due Events : <? echo $k; ?></td>
                    </tr>
                    <tr bgcolor="#FFFFCC">
                        <td colspan="13" style="font-size:20px; font-weight:bold;"><? echo $task_short_name[$row[csf("task_number")]]; ?></td>
                    </tr>
                    <?
				}
				else
				{
					?>
                    <tr bgcolor="#FFFFCC">
                        <td colspan="13" style="font-size:20px; font-weight:bold;"><? echo $task_short_name[$row[csf("task_number")]]; ?></td>
                    </tr>
                    <?
				}
				$k=0;
			}
			$task_num=$row[csf("task_number")];
			$k++;
			//echo $wo_po_details_master[$row[csf('job_no')]][csf('dealing_marchant')]."**";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                <td width="40" align="center"><? echo $k;?></td>
                <td width="120" ><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
                <td width="40"  align="center"><p><? echo $row[csf('job_year')];?>&nbsp;</p></td>
                <td width="60"   align="center"><p><? echo $row[csf('job_no_prefix_num')];?>&nbsp;</p></td>
                <td width="120"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
                <td width="120"><p><? echo $row[csf('po_number')]; ?>&nbsp;</p></td>
                <td width="70" align="center"><p><? if($row[csf('shipment_date')]!='' && $row[csf('shipment_date')]!='0000-00-00') echo change_date_format($row[csf('shipment_date')]); else echo '&nbsp;'; ?>&nbsp;</p></td>
                <td width="70"  align="center"><p><? if($row[csf('task_start_date')]!='' && $row[csf('task_start_date')]!='0000-00-00') echo change_date_format(trim($row[csf('task_start_date')]));  else echo '&nbsp;'; ?></p></td>
                <td width="70" align="center"><p>
				<?
				$start_due_date=datediff( "d", $row[csf('task_start_date')], $pc_date);
				if($start_due_date>0) echo $start_due_date." Days";
				?>
                &nbsp;</p></td>
                <td width="70" align="center"><p><? if($row[csf('task_finish_date')]!='' && $row[csf('task_finish_date')]!='0000-00-00') echo change_date_format(trim($row[csf('task_finish_date')]));  else echo '&nbsp;'; ?></p></td>
                <td width="60" align="center"><p>
                <?
				$fin_due_date=datediff( "d", $row[csf('task_finish_date')], $pc_date);
				if($fin_due_date>0) echo $fin_due_date." Days";
				?>
                &nbsp;</p></td>
                <td width="120"><p><? echo $merchen_data_arr[$row[csf('dealing_marchant')]]["team_member_name"]; ?>&nbsp;</p></td>
                <td><p><? echo $merchen_data_arr[$row[csf('dealing_marchant')]]["member_contact_no"]; ?>&nbsp;</p></td>
            </tr>
			<?
			$i++;
        }
        ?>
        <tr bgcolor="#E2E2E2">
            <td colspan="13" style="font-size:20px; font-weight:bold;">Total Number of Events : <? echo $total_task_data[$task_num]; ?></td>
        </tr>
        <tr bgcolor="#D3D3D3">
            <td colspan="13" style="font-size:20px; font-weight:bold;">Due Events : <? echo $k; ?></td>
        </tr>
    </table>
    </div>
    <?
	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
 	echo "$total_datass****$filename";
	exit();
    
	
}

if($action=="generate_penalty_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$tna_task_id=str_replace("'","",$tna_task_id);
	$com_id=str_replace("'","",$cbo_company_name);
	//echo $pc_date;die;
	
	if(str_replace("'","",$cbo_company_name)==0) $cbo_company_name=""; else $cbo_company_name=" and a.company_name = $cbo_company_name";
	if(str_replace("'","",$cbo_buyer_name)==0) $cbo_buyer_name=""; else $cbo_buyer_name=" and a.buyer_name = $cbo_buyer_name";
	if(str_replace("'","",$cbo_team_member)==0) $cbo_team_member=""; else $cbo_team_member=" and a.dealing_marchant = $cbo_team_member";
	
	if(str_replace("'","",$cbo_search_type)==1){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
	}
	else if(str_replace("'","",$cbo_search_type)==3){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and c.country_ship_date between $txt_date_from and $txt_date_to";
	}
	else if(str_replace("'","",$cbo_search_type)==4){
		if($db_type==0)
		{
			if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)==""){$date_range="";}else{ 
			$date_range=" and b.insert_date between ".$txt_date_from." and ".$txt_date_to."";}
		}
		else
		{
			
			if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)==""){$date_range="";}else{ 
			$date_range=" and b.insert_date between ".$txt_date_from." and '".str_replace("'","",$txt_date_to)." 11:59:59 PM'";}
		}
	}
	else
	{
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.po_received_date between $txt_date_from and $txt_date_to";
	}
	
	//if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.shipment_date between $txt_date_from and $txt_date_to";
	
	$order_status_cond="";
	if(str_replace("'","",$cbo_order_status)>0) $order_status_cond=" and b.is_confirmed=$cbo_order_status";
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if($txt_job_no=="") $txt_job_no=""; else $txt_job_no=" and a.job_no_prefix_num ='$txt_job_no'";
	$txt_order_no=str_replace("'","",$txt_order_no);
	if($txt_order_no=="") $txt_order_no=""; else $txt_order_no=" and b.po_number ='$txt_order_no'";
	$txt_style_ref_no=str_replace("'","",$txt_style_ref_no);
	if($txt_style_ref_no=="") $txt_style_ref_no=""; else $txt_style_ref_no=" and a.style_ref_no ='$txt_style_ref_no'";
	
	if(str_replace("'","",$cbo_shipment_status)==4){$shipment_status_con=" and b.shiping_status=3";}
	else if(str_replace("'","",$cbo_shipment_status)==1){$shipment_status_con=" and b.shiping_status !=3";}
	
	
	$com_sql=sql_select("select id, company_name, plot_no, level_no, road_no, city from  lib_company where id=$com_id");
	foreach($com_sql as $row)
	{
		$com_name=$row[csf("company_name")];
		$com_plot_no=$row[csf("plot_no")];
		$com_level_no=$row[csf("level_no")];
		$com_road_no=$row[csf("road_no")];
		$com_city=$row[csf("city")];
	}
	if($com_plot_no!="")$com_add=$com_plot_no; if($com_level_no!="")$com_add.=" ".$com_level_no; if($com_road_no!="")$com_add.=" ".$com_road_no; 
	if($com_city!="")$com_add.=" ".$com_city;
	unset($com_sql);
	
	$lib_task_sql=sql_select("select task_name,task_short_name, penalty from lib_tna_task where task_type = 1");
	$task_data=array();
	foreach($lib_task_sql as $row)
	{
		$task_data[$row[csf("task_name")]]["task_short_name"]=$row[csf("task_short_name")];
		$task_data[$row[csf("task_name")]]["penalty"]=$row[csf("penalty")];
	}
	
	unset($lib_task_sql);
	
	if(str_replace("'","",$cbo_search_type)==3)
	{
		if($db_type==0)
		{
			$sql_total_task=sql_select("select p.task_number, count(case when p.actual_start_date='0000-00-00' then p.id end) as due_start, count(case when p.actual_finish_date='0000-00-00' then p.id end) as due_end
			from  tna_process_mst p, wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
			where p.po_number_id=b.id and b.job_no_mst=a.job_no and b.id=c.po_break_down_id and a.status_active=1 and b.status_active=1 and (p.task_start_date<'$pc_date' or p.task_finish_date<'$pc_date') $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no $order_status_cond  $shipment_status_con
			group by p.task_number
			order by cast(p.task_number AS UNSIGNED)");
		}
		else
		{
			$sql_total_task=sql_select("select p.task_number, count(case when p.actual_start_date is null then p.id end) as due_start, count(case when p.actual_finish_date is null then p.id end) as due_end
			from  tna_process_mst p, wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
			where p.po_number_id=b.id and b.job_no_mst=a.job_no and b.id=c.po_break_down_id and a.status_active=1 and b.status_active=1 and (p.task_start_date<'$pc_date' or p.task_finish_date<'$pc_date') $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no $order_status_cond  $shipment_status_con
			group by p.task_number
			order by TO_NUMBER(p.task_number, '999')");
		}
	
			//and (p.actual_start_date is null or p.actual_finish_date is null)
	}
	else
	{
		if($db_type==0)
		{
			$sql_total_task=sql_select("select p.task_number, count(case when p.actual_start_date='0000-00-00' then p.id end) as due_start, count(case when p.actual_finish_date='0000-00-00' then p.id end) as due_end
			from  tna_process_mst p, wo_po_details_master a, wo_po_break_down b
			where p.po_number_id=b.id and b.job_no_mst=a.job_no and a.status_active=1 and b.status_active=1  and (p.task_start_date<'$pc_date' or p.task_finish_date<'$pc_date') $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no $order_status_cond  $shipment_status_con
			group by p.task_number
			order by cast(p.task_number AS UNSIGNED)");
		}
		else
		{
			$sql_total_task=sql_select("select p.task_number, count(case when p.actual_start_date is null then p.id end) as due_start, count(case when p.actual_finish_date is null then p.id end) as due_end
			from  tna_process_mst p, wo_po_details_master a, wo_po_break_down b
			where p.po_number_id=b.id and b.job_no_mst=a.job_no and a.status_active=1 and b.status_active=1 and (p.task_start_date<'$pc_date' or p.task_finish_date<'$pc_date') $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no $order_status_cond  $shipment_status_con
			group by p.task_number
			order by TO_NUMBER(p.task_number, '999')");
		}
	}
	ob_start();
	
	?>
    <div style="width:820px" align="left">
    <table width="800" border="0">
    	<tr>
        	<td align="center" colspan="8" class="form_caption"><? echo $com_name; ?></td>
        </tr>
        <tr>
        	<td align="center" colspan="8" class="form_caption"><? echo $com_add; ?></td>
        </tr>
        <tr>
        	<td align="center" colspan="8" style="font-weight:bold; font-size:16px;">Penalty Payment Sheet for TNA Overdue Task (From  <? echo change_date_format(str_replace("'","",$txt_date_from)); ?> To <? echo change_date_format(str_replace("'","",$txt_date_to)); ?>)</td>
        </tr>
    </table>
    <table width="800" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <thead>
            <tr>
                <th width="40" rowspan="2">SL</th>
                <th width="150" rowspan="2">Task Name</th>
                <th colspan="2" width="200">Overdue Events</th>
                <th width="100" rowspan="2">Total Overdue</th>
                <th width="100" rowspan="2">Penalty / Event</th> 
                <th width="100" rowspan="2">Total Amount</th>
                <th rowspan="2">Remarks</th>
            </tr>
            <tr>
                <th width="100">Start</th>
                <th width="100">Finish</th>
            </tr>
        </thead>
    </table>
    </div>
    
    <div style="overflow-y:scroll; max-height:330px; width:820px;" align="left" id="scroll_body">
    <table width="800" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
		<?
        $i=1;
        foreach ($sql_total_task as $row)
        {
            if ($i%2==0)  
            $bgcolor="#E9F3FF";
            else
            $bgcolor="#FFFFFF";	
			
			$task_tot_due=$row[csf('due_start')]+$row[csf('due_end')];
			$task_tot_amt=$task_tot_due*$task_data[$row[csf('task_number')]]["penalty"];
			if($row[csf('due_start')]>0 || $row[csf('due_end')]>0)
			{
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
					<td width="40" align="center"><? echo $i;?></td>
					<td width="150"><? echo $task_data[$row[csf('task_number')]]["task_short_name"]; ?></td>
					<td width="100" align="right"><? echo $row[csf('due_start')];?></td>
					<td width="100" align="right"><? echo $row[csf('due_end')];?></td>
					<td width="100" align="right"><? echo $task_tot_due; $gt_task_tot_due+=$task_tot_due; ?></td>
					<td width="100" align="right"><? echo number_format($task_data[$row[csf('task_number')]]["penalty"],2); ?></td>
					<td width="100" align="right"><? echo number_format($task_tot_amt,2); $gt_task_tot_amt+=$task_tot_amt;  ?></td>
					<td  align="center"><p>&nbsp;</p></td>
				</tr>
				<?
				$i++;
			}
            
        }
        ?>
    </table>
    </div>
    <table width="800" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <tfoot>
            <tr>
                <th width="40" >&nbsp;</th>
                <th width="150" >&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100" align="right">Total</th>
                <th width="100" align="right"><? echo number_format($gt_task_tot_due,0); ?></th>
                <th width="100" align="right">&nbsp;</th> 
                <th width="100" align="right"><? echo number_format($gt_task_tot_amt,0); ?></th>
                <th>&nbsp;</th>
            </tr>
        </tfoot>
    </table>
    <?
	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
 	echo "$total_datass****$filename";
	exit();
    
	
}

if($action=="generate_buyer_task_wise_report")
{
	
	
	$actual_manual_update_task_arr=return_library_array("select task_id,task_id from tna_manual_permission where is_actual_manual=1  and company_id=$cbo_company_name","task_id","task_id");
	$plan_manual_update_task_arr=return_library_array("select task_id,task_id from tna_manual_permission where is_plan_manual=1  and company_id=$cbo_company_name","task_id","task_id");
	
	
	$mod_sql= sql_select("select a.id,a.task_catagory,a.task_name,a.task_short_name,a.task_type,a.completion_percent from lib_tna_task a, tna_task_template_details b where b.for_specific=$cbo_buyer_name and a.task_name=b.tna_task_id  AND a.task_type = 1 AND b.task_type = 1 and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 order by a.task_sequence_no asc");
	$tna_task_array=array();
	$tna_task_id=array();
	$tna_task_cat=array();
	$tna_task_name_arr=array();
	foreach ($mod_sql as $row)
	{
		$tna_task_id[$row[csf("task_name")]]=$row[csf("task_name")];
		$tna_task_array[$row[csf("id")]] =$row[csf("task_short_name")];
		$tna_task_cat[$row[csf("id")]]=$row[csf("task_catagory")];
		$tna_task_name_arr[$row[csf("id")]]=$row[csf("task_name")];
	}
	$order_status_cond="";
	if(str_replace("'","",$cbo_order_status)>0) $order_status_cond=" and b.is_confirmed=$cbo_order_status";
 
	if(str_replace("'","",$cbo_company_name)==0) $cbo_company_name=""; else $cbo_company_name=" and a.company_name = $cbo_company_name";
	if(str_replace("'","",$cbo_buyer_name)==0) $cbo_buyer_name=""; else $cbo_buyer_name=" and a.buyer_name = $cbo_buyer_name";
	if(str_replace("'","",$cbo_team_member)==0) $cbo_team_member=""; else $cbo_team_member=" and a.dealing_marchant = $cbo_team_member";
	
	if(str_replace("'","",$cbo_search_type)==1){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
	}
	else if(str_replace("'","",$cbo_search_type)==3){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and c.country_ship_date between $txt_date_from and $txt_date_to";
	}
	else if(str_replace("'","",$cbo_search_type)==4){
		if($db_type==0)
		{
			if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)==""){$date_range="";}else{ 
			$date_range=" and b.insert_date between ".$txt_date_from." and ".$txt_date_to."";}
		}
		else
		{
			
			if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)==""){$date_range="";}else{ 
			$date_range=" and b.insert_date between ".$txt_date_from." and '".str_replace("'","",$txt_date_to)." 11:59:59 PM'";}
		}
	}
	else
	{
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.po_received_date between $txt_date_from and $txt_date_to";
	}
	
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if($txt_job_no=="") $txt_job_no=""; else $txt_job_no=" and a.job_no_prefix_num ='$txt_job_no'";
	$txt_order_no=str_replace("'","",$txt_order_no);
	if($txt_order_no=="") $txt_order_no=""; else $txt_order_no=" and b.po_number ='$txt_order_no'";
	$txt_style_ref_no=str_replace("'","",$txt_style_ref_no);
	if($txt_style_ref_no=="") $txt_style_ref_no=""; else $txt_style_ref_no=" and a.style_ref_no ='$txt_style_ref_no'";
	
	if(str_replace("'","",$cbo_shipment_status)==4){$shipment_status_con=" and b.shiping_status=3";}
	else if(str_replace("'","",$cbo_shipment_status)==1){$shipment_status_con=" and b.shiping_status !=3";}
	
	
	
	
	
	$tna_all_task=implode(",",$tna_task_id);
	
	if(str_replace("'","",$cbo_search_type)==3)
	{
		$sql = "SELECT a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.set_smv,a.job_no_prefix_num,a.dealing_marchant,b.id,b.po_number FROM  wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no  and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 $order_status_cond";
	}
	else
	{
		$sql = "SELECT a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.set_smv,a.job_no_prefix_num,a.dealing_marchant,b.id,b.po_number FROM  wo_po_details_master a,  wo_po_break_down b WHERE a.job_no=b.job_no_mst $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no  and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 $order_status_cond"; 
	}
	

	$result = sql_select( $sql ) ;
	$wo_po_details_master = array();
	$po_no_arr=array();
	$job_no_arr=array();
	foreach( $result as  $row ) 
	{	
		$wo_po_details_master[$row[csf('job_no')]][csf('company_name')]=$row[csf('company_name')];
		$wo_po_details_master[$row[csf('job_no')]][csf('buyer_name')]=$row[csf('buyer_name')];
		$wo_po_details_master[$row[csf('job_no')]][csf('style_ref_no')]=$row[csf('style_ref_no')];
		$wo_po_details_master[$row[csf('job_no')]][csf('job_no_prefix_num')]=$row[csf('job_no_prefix_num')];
		$wo_po_details_master[$row[csf('job_no')]][csf('dealing_marchant')]=$row[csf('dealing_marchant')];
		$wo_po_details_master[$row[csf('job_no')]][$row[csf('id')]]= $row[csf('po_number')];
		$wo_po_details_master[$row[csf('job_no')]]['set_smv']= $row[csf('set_smv')];
		$po_no_arr[]=$row[csf('id')];
		$job_no_arr[]=$row[csf('job_no')];
		
	}
	
 
	$sql = "SELECT team_member_name,id FROM lib_mkt_team_member_info WHERE is_deleted = 0 and status_active=1 order by id asc";
	$result = sql_select( $sql ) ;
	$team_member_name = array();
	foreach( $result as  $row ) 
	{	
		$team_member_name[$row[csf('id')]]=$row[csf('team_member_name')];
	}
	
	$sql = "SELECT buyer_name,id FROM  lib_buyer WHERE is_deleted = 0 and status_active=1 order by id asc";
	$result = sql_select( $sql ) ;
	$buyer_name = array();
	foreach( $result as  $row ) 
	{	
		$buyer_name[$row[csf('id')]]=$row[csf('buyer_name')];
	}
	
	
	$po_no_arr_all=implode(",",$po_no_arr); if($po_no_arr_all!="") $po_no_arr_all .=",0"; else $po_no_arr_all .="0"; 
	$job_no_all="'".implode("','",$job_no_arr)."'";
	$c=count($tna_task_id);
	
	if($db_type==0)
	{
		$sql ="select a.po_number_id,a.job_no,a.shipment_date,a.template_id,a.po_receive_date,";
		$i=1;
	
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c) $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number)  END ) as status$id, ";
			else $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number)  END ) as status$id ";
			$i++;
		}
		
		$sql .=" from tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id and a.po_number_id in( $po_no_arr_all ) and a.job_no in ($job_no_all) $shipment_status_con and b.status_active=1 and a.task_type=1  and b.po_quantity>0 $order_status_cond group by a.po_number_id,a.job_no order by a.shipment_date,a.po_number_id,a.job_no"; 
	}
	else
	{
		$sql ="select a.po_number_id,a.job_no,max(a.shipment_date) as shipment_date,a.template_id,max(a.po_receive_date) as po_receive_date,";
		$i=1;
		
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c) $sql .="max(CASE WHEN a.task_number = '".$id."' THEN a.actual_start_date || '_' || a.actual_finish_date || '_' || a.task_start_date || '_' || a.task_finish_date ||'_' || a.notice_date_start || '_' || a.notice_date_end || '_' || a.remarks || '_' || a.id  END ) as status$id, ";
			
			else $sql .="max(CASE WHEN a.task_number = '".$id."' THEN a.actual_start_date || '_' || a.actual_finish_date || '_' || a.task_start_date || '_' || a.task_finish_date || '_' || a.notice_date_start || '_' || a.notice_date_end || '_' || a.remarks || '_' || a.id  END ) as status$id ";
			
			$i++;
		}
		//------------------
			$sql_order_con='';
			$po_no_arr_all=explode(',',$po_no_arr_all);
			$chunk_po_no_arr_all=array_chunk(array_unique($po_no_arr_all),999);
			$p=1;
			foreach($chunk_po_no_arr_all as $rlz_sub_id)
			{
				if($p==1) $sql_order_con .=" and (a.po_number_id in(".implode(',',$rlz_sub_id).")"; else $sql_sub_lc .=" or a.po_number_id in(".implode(',',$rlz_sub_id).")";
				$p++;
			}
			$sql_order_con .=" )";
			
			$sql_job_con='';
			$job_no_all=explode(',',$job_no_all);
			$chunk_job_no_all=array_chunk(array_unique($job_no_all),999);
			$q=1;
			foreach($chunk_job_no_all as $rlz_sub_id)
			{
				if($q==1) $sql_job_con .=" and (a.job_no in(".implode(',',$rlz_sub_id).")"; else $sql_sub_lc .=" or a.job_no in(".implode(',',$rlz_sub_id).")";
				$p++;
			}
			$sql_job_con .=" )";
			
			
		//-------------------------------
		$sql .=" from  tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id $sql_order_con $sql_job_con $shipment_status_con and b.status_active=1 and a.task_type=1 and b.po_quantity>0 $order_status_cond  group by a.po_number_id,a.job_no,a.template_id,a.shipment_date order by a.shipment_date,a.po_number_id,a.job_no"; 
	}
	
	
	$lead_time_array=return_library_array("select task_template_id,lead_time from tna_task_template_details where task_type=1 and is_deleted=0 and status_active=1 group by lead_time,task_template_id","task_template_id",'lead_time');
	
	if($db_type==0)
	{
		$tast_tmp_id_arr=return_library_array("select task_template_id, group_concat(tna_task_id) as tna_task_id  from tna_task_template_details where task_type=1 and is_deleted=0 and status_active=1 group by task_template_id","task_template_id",'tna_task_id');
	}
	else
	{
		$tast_tmp_id_arr=return_library_array("select task_template_id, listagg(cast(tna_task_id as varchar(4000)),',') within group(order by tna_task_id) as tna_task_id  from tna_task_template_details where task_type=1 and is_deleted=0 and status_active=1 group by task_template_id","task_template_id",'tna_task_id');
	}
	
	
	
	$data_sql= sql_select($sql);
	$width=(count($tna_task_id)*160)+900;
	
	ob_start();
	
	?>
    <div style="width:<? echo $width+200; ?>px" align="left">
    <table width="<? echo $width+140; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
    	<thead>
        	<tr>
            	<th width="60" rowspan="2">SL</th><th width="120" rowspan="2">Merchant</th><th width="120" rowspan="2">Buyer Name</th><th width="120" rowspan="2">PO Number</th><th width="100" rowspan="2">PO Qty.</th><th width="50" rowspan="2">SMV</th><th width="120" rowspan="2">Style Ref.</th> <th width="120" rowspan="2">Job No.</th><th width="100" rowspan="2">Shipment Date</th>
                
                <th width="90" rowspan="2">Status</th>
                <?
					$i=0;
					
					foreach($tna_task_array as $key)
					{
						$i++;
						if(count($tna_task_array)==$i) echo '<th width="160" colspan="2">'. $key.'</th>'; else echo '<th width="160" colspan="2">'.$key.'</th>';
					}
					echo '</tr><tr>';
					
					$i=0;
					
					foreach($tna_task_array as $key)
					{
						$i++;
						if(count($tna_task_array)==$i) echo '<th width="80"> Start</th><th width="80"> Finish</th>'; else echo '<th width="80"> Start</th><th width="80"> Finish</th>';
					}
					echo '</tr>';
					 
				?>
                </thead>
                </table>
         </div>
         
        	<div style="overflow-y:scroll; max-height:360px; width:<? echo $width+170; ?>px;" align="left" id="scroll_body">
          	<table width="<? echo $width+140; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
       
    <?
	
	$tid=0;
	$i=1;
	$count=0;
	$kid=1;
	$new_job_no=array();
	$h=0;
	$tot_po_qty=0;
	foreach ($data_sql as $row)
	{
		 
		if (!in_array($row[csf('job_no')],$new_job_no))
		{
			$new_job_no[]=$row[csf('job_no')];
		}
		 if($row[csf('po_number_id')]==0)
		 {
			 foreach($tna_task_id as $vid=>$key)
			 {
				if ($row[csf('status').$key]!="") $new_approval_arr[$row[csf('job_no')]][$key]=$row[csf('status').$key];
			 }
		 }
		 else
		 {
			 $h++;
			 	if ($h%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
							
		?>
        		<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onClick="change_color('tr_<? echo $h;?>','<? echo $bgcolor;?>')" id="tr_<? echo $h; ?>">
                    <td width="60" rowspan="3"><? echo $kid++;?></td>
                    <td width="120" rowspan="3"><? echo $team_member_name[$wo_po_details_master[$row[csf('job_no')]][csf('dealing_marchant')]]; ?></td>
                    <td width="120" rowspan="3"><? echo $buyer_name[$wo_po_details_master[$row[csf('job_no')]][csf('buyer_name')]]; ?></td>
                    <td width="120" rowspan="3" align="center"><p>
						<? 
							echo "<a href='#report_details' style='color:#990000' onclick= \"progress_comment_popup('".$row[csf('job_no')]."','".$row[csf('po_number_id')]."','".$row[csf('template_id')]."','".$tna_process_type."');\">".$wo_po_details_master[$row[csf('job_no')]][$row[csf('po_number_id')]]."</a>";
						
                        ?>
                   </p> </td>
                    
                    <td width="100" rowspan="3" align="right">
						<?
							$po_qty=return_field_value("po_quantity", "wo_po_break_down", "id='".$row[csf('po_number_id')]."' and status_active=1 and is_deleted=0"); 
							echo number_format($po_qty,2);
							$tot_po_qty	+=$po_qty; 
						?>
                    </td>
                    <td width="50" rowspan="3" align="center"><? echo $wo_po_details_master[$row[csf('job_no')]]['set_smv']; ?></td>
                    <td width="120"  rowspan="3" title="<? echo $row[csf('job_no')]; ?>"><p><? echo $wo_po_details_master[$row[csf('job_no')]][csf('style_ref_no')]; ?></p></td>
                     <td width="120" rowspan="3" title=""><? echo $wo_po_details_master[$row[csf('job_no')]][csf('job_no_prefix_num')]; ?></td>
                     
                     <? 
					 	if($tna_process_type==1)
						{
							$lead_timee="Template Lead Time: ".$lead_time_array[$row[csf('template_id')]];
						}
						else
						{
							$lead_timee="Lead Time: ".$row[csf('template_id')];
						}
						$po_lead_time=datediff( "d", date("Y-m-d",strtotime(change_date_format($row[csf('po_receive_date')]))), date("Y-m-d",strtotime(change_date_format($row[csf('shipment_date')]))) );

					 ?>
                     
                     
                    <td width="100" rowspan="3" title="<? echo $lead_timee."; "." PO. Rec. Date: ".change_date_format($row[csf('po_receive_date')]); ?>"><? echo change_date_format($row[csf('shipment_date')])."<br>"." ".$lead_timee."<br>"." PO Lead Time:".($po_lead_time-1);  ?></td>
                    <td width="90">Plan</td>
                <?
 
					 $i=0;
					 $tast_id_arr=array_unique(explode(',',$tast_tmp_id_arr[$row[csf('template_id')]]));
					 foreach($tna_task_id as $vid=>$key)
					 {
						 $i++;
						
						if ( $new_approval_arr[$row[csf('job_no')]][$key]=="") $new_data=explode("_",$row[csf('status').$key]); 
						else $new_data=explode("_",$new_approval_arr[$row[csf('job_no')]][$key]);
						if($new_data[7]!="") $function="onclick='update_tna_process(1,$new_data[7],".$row[csf('po_number_id')].")'"; else $function="";
						
						if($plan_manual_update_task_arr[$vid]==''){$function="";}
						
						if(in_array($vid,$tast_id_arr))
						{
							if(count($tna_task_id)==$i)
								
								echo '<td  width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? " <span style='color:#FF0000'> N/A </span>" : change_date_format($new_data[2])).'</td><td '.$function.'> '.($new_data[3]== "N/A"  || $new_data[3]=="0000-00-00"? "" : change_date_format($new_data[3])).'</td>';
								
							 else
								
								echo '<td width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? "<span style='color:#FF0000'> N/A </span>" : change_date_format($new_data[2])).'</td><td width="80" '.$function.'> '.($new_data[3]== ""  || $new_data[3]=="0000-00-00"? "<span style='color:#FF0000'> N/A </span>" : change_date_format($new_data[3])).'</td>';
						}
						else
						{
							if(count($tna_task_id)==$i)
								
								echo '<td  width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? " N/A" : change_date_format($new_data[2])).'</td><td '.$function.'> '.($new_data[3]== "N/A"  || $new_data[3]=="0000-00-00"? "" : change_date_format($new_data[3])).'</td>';
								
							 else
								
								echo '<td width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? " N/A" : change_date_format($new_data[2])).'</td><td width="80" '.$function.'> '.($new_data[3]== ""  || $new_data[3]=="0000-00-00"? " N/A" : change_date_format($new_data[3])).'</td>';
						}
						
					 }
					echo '</tr>';
					unset($tast_id_arr);
					echo '<tr><td width="90">Actual</td>';
					$i=0;
					 foreach($tna_task_id as $vid=>$key)
					 {
						  
						 $i++;
						 
						
						if ( $new_approval_arr[$row[csf('job_no')]][$key]=="") $new_data=explode("_",$row[csf('status').$key]);
						else $new_data=explode("_",$new_approval_arr[$row[csf('job_no')]][$key]);
						
						
						if( $new_data[7]!="") $function="onclick='update_tna_process(2,$new_data[7],".$row[csf('po_number_id')].")'";  else $function="";
						
						if($actual_manual_update_task_arr[$vid]==''){$function="";}
						
						$bgcolor1=""; $bgcolor="";
						
						if (trim($new_data[2])!= $blank_date) 
						{
							
							
							if (strtotime($new_data[4])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($new_data[2]))  $bgcolor="#FFFF00";//Yellow
							else if (strtotime($new_data[2])<strtotime(date("Y-m-d",time())))  $bgcolor="#FF0000";//Red
							else $bgcolor="";
							
						}
						
						
						 
						if ($new_data[3]!= $blank_date) {
							if (strtotime($new_data[5])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($new_data[3]))  $bgcolor1="#FFFF00";
							else if (strtotime($new_data[3])<strtotime(date("Y-m-d",time())))  $bgcolor1="#FF0000"; else $bgcolor1="";
						}
						
						
						
						
						if ($new_data[0]!=$blank_date) $bgcolor="";
						if ($new_data[1]!=$blank_date) $bgcolor1="";
						
						
						$idd=$row[csf('job_no')]."".$row[csf('po_number_id')]."".$key;
						if(count($tna_task_id)==$i)
							echo '<td title="Click Here to Edit Date" id="'.$idd.'1" '.$function.' width="80" bgcolor="'.$bgcolor.'">'.($new_data[0]== "" || $new_data[0]=="0000-00-00" ? "" : change_date_format($new_data[0])).'</td><td id="'.$idd.'2" title="Click Here to Edit Date" '.$function.' bgcolor="'.$bgcolor1.'" title="'.$new_data[6].'">'.($new_data[1]== "" || $new_data[1]=="0000-00-00" ? "" : change_date_format($new_data[1])).'</td>';
							
							//echo '<td title="Click Here to Edit Date" id="'.$idd.'1" '.$function.' width="80" bgcolor="'.$bgcolor.'">'.($new_data[0]== ""  ? "" : change_date_format($new_data[0])).'</td><td id="'.$idd.'2" title="Click Here to Edit Date" '.$function.' bgcolor="'.$bgcolor1.'" title="'.$new_data[6].'">'.($new_data[1]== ""  ? "" : change_date_format($new_data[1])).'</td>';
						else
							echo '<td id="'.$idd.'1" title="Click Here to Edit Date"  '.$function.' width="80" bgcolor="'.$bgcolor.'">'.($new_data[0]== "" || $new_data[0]=="0000-00-00" ? "" : change_date_format($new_data[0])).'</td><td id="'.$idd.'2" title="Click Here to Edit Date" '.$function.' width="80" bgcolor="'.$bgcolor1.'" title="'.$new_data[6].'">'.($new_data[1]== "" || $new_data[1]=="0000-00-00" ? "" : change_date_format($new_data[1])).'</td>';
						
							//echo '<td id="'.$idd.'1" title="Click Here to Edit Date"  '.$function.' width="80" bgcolor="'.$bgcolor.'">'.($new_data[0]== "" ? "" : change_date_format($new_data[0])).'</td><td id="'.$idd.'2" title="Click Here to Edit Date" '.$function.' width="80" bgcolor="'.$bgcolor1.'" title="'.$new_data[6].'">'.($new_data[1]== ""  ? "" : change_date_format($new_data[1])).'</td>';
					 }
					echo '</tr>'; 
					
					echo '<tr><td width="90">Delay/Early By</td>';
					$j=0;
					foreach($tna_task_id as $vid=>$key)
					{
						 $j++;
						if ( $new_approval_arr[$row[csf('job_no')]][$key]=="") $new_data=explode("_",$row[csf('status').$key]); 
						else $new_data=explode("_",$new_approval_arr[$row[csf('job_no')]][$key]);
						
						//echo "<pre>";
						//print_r($new_data);
						
						$bgcolor1=""; $bgcolor="";
						
						
						
						//$new_data : 0->actual_start_date, 1->actual_finish_date, 2->task_start_date, 3->task_finish_date, 4->notice_date_start, 5->notice_date_end
						//echo $new_data[3]."saju*";
						//new start
						
						if($new_data[0]!=$blank_date)
						{
							$start_diff1 = datediff( "d", $new_data[0], $new_data[2]);
							if($new_data[0]== "")
							{
								$start_diff=$start_diff1;
							}
							else
							{
								$start_diff=$start_diff1-1;
							}
							if($start_diff<0)
							{
								$bgcolor="#2A9FFF"; //Blue
							}
							if($start_diff>0)
							{
								$bgcolor="";
							}
						}
						else
						{
							if(strtotime(date("Y-m-d"))>strtotime($new_data[2]))
							{
								$start_diff1 = datediff( "d", $new_data[2], date("Y-m-d"));
								if($new_data[0]== "")
								{
									$start_diff=-abs($start_diff1);
								}
								else
								{
									$start_diff=-abs($start_diff1-1);
								}
								//$bgcolor="#FF0000";		//Red
								$bgcolor=($new_data[2]== "" || $new_data[2]=="0000-00-00")?'':'#FF0000';
							
							
							
							}
							if(strtotime(date("Y-m-d"))<=strtotime($new_data[2]))
							{
								$start_diff = "";
								$bgcolor="";
							}
						}
						if($new_data[1]!=$blank_date)
						{
							$finish_diff1 = datediff( "d", $new_data[1], $new_data[3]);
							if($new_data[0]== "")
							{
								$finish_diff=$finish_diff1;
							}
							else
							{	
								$finish_diff=$finish_diff1-1;
							}
							if($finish_diff<0)
							{
								$bgcolor1="#2A9FFF";
							}
							if($finish_diff>0)
							{	
								$bgcolor1="";
							}
						}
						else
						{
							if(strtotime(date("Y-m-d"))>strtotime($new_data[3]))
							{
								
								$finish_diff1 = datediff( "d", $new_data[3], date("Y-m-d"));
								if($new_data[1]== "")
								{
									$finish_diff=-abs($finish_diff1);
								}
								else
								{
									$finish_diff=-abs($finish_diff1-1);
								}
								$bgcolor1=($new_data[3]== "" || $new_data[3]=="0000-00-00")?'':'#FF0000';
							}
							if(strtotime(date("Y-m-d"))<=strtotime($new_data[3]))
							{
								
								$finish_diff = "";
								$bgcolor1="";
							}
						}
						
						
						
						if(count($tna_task_id)==$j)
							
							echo '<td width="80" align="center" bgcolor="'.$bgcolor.'">'.($start_diff).'</td><td width="80" bgcolor="'.$bgcolor1.'" align="center">'.($finish_diff).'</td>';
						else
							echo '<td width="80" align="center" bgcolor="'.$bgcolor.'">'.($start_diff).'</td><td width="80" bgcolor="'.$bgcolor1.'" align="center">'.($finish_diff).'</td>';
					}
					 
					echo '</tr>';
					
					
					 
		 }
				 
	}
		?>
     
     
    </table>
    </div>
    <div style="width:<? echo $width+140; ?>px;" align="left">
         <table width="100%" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <tfoot>
                <th width="59"></th>
                <th width="120"></th>
                <th width="119"></th>
                <th width="119">Total</th>
                <th width="99"><? echo number_format($tot_po_qty,2);?></th>
                <th width="50"></th>
                <th colspan="<? echo (count($tna_task_id)*2)+4;?>"></th>
            </tfoot>
        </table>
    </div>
    
    
    
    <?
	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
 echo "$total_datass****$filename";
	exit();
}


if($action=="edit_update_tna")
{
	echo load_html_head_contents("TNA","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	 
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		
		$sql ="select a.po_number,b.job_no,b.buyer_name,b.style_ref_no from  wo_po_break_down a,wo_po_details_master b where a.id=$po_id and a.job_no_mst=b.job_no";
		//echo $sql;die;
		$result=sql_select($sql);
		
		$tna= "select APPROVED,READY_TO_APPROVED,template_id,task_number,task_start_date,task_finish_date,actual_start_date,actual_finish_date,plan_start_flag,plan_finish_flag,commit_start_date,commit_end_date from  tna_process_mst where id in($mid) ";
		//echo $tna;die;
		$tna_result=sql_select($tna);
		
		$mod_sql= sql_select("select id,task_catagory,task_name,task_short_name,task_type,completion_percent from lib_tna_task where is_deleted = 0 and status_active=1  AND task_type = 1 order by task_sequence_no asc");
		$tna_task_array=array();
		foreach ($mod_sql as $row)
		{
			$tna_task_array[$row[csf("task_name")]] = $row[csf("task_short_name")];
		}

		$permission_sql_res=sql_select("select PLAN_USER_ID,ACTCUAL_USER_ID from TNA_MANUAL_PERMISSION where COMPANY_ID=$cbo_company_name and TASK_ID={$tna_result[0][csf('task_number')]}");
		$plan_status = (in_array($user_id,explode(',',$permission_sql_res[0]['PLAN_USER_ID'])))?1:0;
		$actcual_status = (in_array($user_id,explode(',',$permission_sql_res[0]['ACTCUAL_USER_ID'])))?1:0;
	
	
		
		//History data start------------------------
		$tna_history_sql= "select id, template_id,task_number, job_no, po_number_id, task_start_date, task_finish_date, actual_start_date, actual_finish_date,plan_start_flag,plan_finish_flag,commit_start_date,commit_end_date,update_by,update_date from  tna_plan_actual_history where  is_deleted = 0 and status_active=1 and template_id=".$tna_result[0][csf('template_id')]." and task_type = 1 and task_number=".$tna_result[0][csf('task_number')]." and po_number_id=$po_id and job_no='".$result[0][csf('job_no')]."' order by id desc";
		$tna_history=sql_select($tna_history_sql);
		 //echo $tna_history_sql;die;
		//History data end------------------------
		
		//var_dump($tna_history);
	
		if($tna_history[0][csf('task_start_date')]==""){$ts_history_con=0;}
		else if($tna_history[0][csf('task_start_date')]=="0000-00-00"){$ts_history_con=0;}else{$ts_history_con=1;}
		if($tna_result[0][csf('task_start_date')]==""){$ts_result_con=0;}
		else if($tna_result[0][csf('task_start_date')]=="0000-00-00"){$ts_result_con=0;}else{$ts_result_con=1;}


		if($tna_history[0][csf('task_finish_date')]==""){$tf_history_con=0;}
		else if($tna_history[0][csf('task_finish_date')]=="0000-00-00"){$tf_history_con=0;}else{$tf_history_con=1;}
		if($tna_result[0][csf('task_finish_date')]==""){$tf_result_con=0;}
		else if($tna_result[0][csf('task_finish_date')]=="0000-00-00"){$tf_result_con=0;}else{$tf_result_con=1;}


		if($tna_history[0][csf('actual_start_date')]==""){$as_history_con=0;}
		else if($tna_history[0][csf('actual_start_date')]=="0000-00-00"){$as_history_con=0;}else{$as_history_con=1;}
		if($tna_result[0][csf('actual_start_date')]==""){$as_result_con=0;}
		else if($tna_result[0][csf('actual_start_date')]=="0000-00-00"){$as_result_con=0;}else{$as_result_con=1;}

		if($tna_history[0][csf('actual_finish_date')]==""){$af_history_con=0;}
		else if($tna_history[0][csf('actual_finish_date')]=="0000-00-00"){$af_history_con=0;}else{$af_history_con=1;}
		if($tna_result[0][csf('actual_finish_date')]==""){$af_result_con=0;}
		else if($tna_result[0][csf('actual_finish_date')]=="0000-00-00"){$af_result_con=0;}else{$af_result_con=1;}

		
		if($tna_history[0][csf('commit_start_date')]==""){$cs_history_con=0;}
		else if($tna_history[0][csf('commit_start_date')]=="0000-00-00"){$cs_history_con=0;}else{$cs_history_con=1;}
		if($tna_result[0][csf('commit_start_date')]==""){$af_result_con=0;}
		else if($tna_result[0][csf('commit_start_date')]=="0000-00-00"){$af_result_con=0;}else{$af_result_con=1;}
		
		if($tna_history[0][csf('commit_end_date')]==""){$ce_history_con=0;}
		else if($tna_history[0][csf('commit_end_date')]=="0000-00-00"){$ce_history_con=0;}else{$ce_history_con=1;}
		if($tna_result[0][csf('commit_end_date')]==""){$ce_result_con=0;}
		else if($tna_result[0][csf('commit_end_date')]=="0000-00-00"){$ce_result_con=0;}else{$ce_result_con=1;}

	?> 
    
    
     <script>
	 
	/* $(document).ready(function(e) {
		 get_submitted_data_string('',"../../../"); 
        
    });*/
	
	
	 
	 
	 var permission='<? echo $permission; ?>';
	function fnc_tna_actual_date_update( operation )
	{
		var start_date='<? echo change_date_format($tna_result[0][csf('task_start_date')]);?>';
		var curr_start_date=$('#txt_plan_start_date').val();
		var history_start_date='<? echo change_date_format($tna_history[0][csf('task_start_date')]);?>';
		
		var finish_date='<? echo change_date_format($tna_result[0][csf('task_finish_date')]);?>';
		var curr_finish_date=$('#txt_plan_finish_date').val();
		var history_finish_date='<? echo change_date_format($tna_history[0][csf('task_finish_date')]);?>';

		var start_flag=0;var finish_flag=0;
		if(start_date!=curr_start_date){start_flag=1;}
		if((history_start_date!=curr_start_date) && history_start_date){ start_flag=1;}
		
		if(finish_date!=curr_finish_date){finish_flag=1;}
		if((history_finish_date!=curr_finish_date) && history_finish_date){ finish_flag=1;}
		 
		var data="action=save_update_delete&operation="+operation+'&start_flag='+start_flag+'&finish_flag='+finish_flag+get_submitted_data_string('txt_actual_start_date*txt_actual_finish_date*txt_update_tna_id*txt_plan_start_date*txt_plan_finish_date*txt_update_tna_type*txt_plan_actual_history*txt_commitment_start_date*txt_commitment_end_date',"../../../");
		//alert (data);return;
		freeze_window(operation);
		http.open("POST","tna_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_tna_actual_date_update_reponse;
	}


	function fnc_tna_actual_date_update_reponse()
	{
		if(http.readyState == 4) 
		{	
			 //alert(http.responseText);return;
			var reponse=trim(http.responseText).split('**');
			show_msg(trim(reponse[0]));
 
			
			if(reponse[0]==1)
			{
				$('#auto_field_data_str').val(http.responseText);
				parent.emailwindow.hide();
			}
			else
			{
				alert('Invalid Operation');
			}
			
			//alert (reponse[0]);
			
			
			//document.getElementById('report_container').innerHTML  = reponse[1];
			set_button_status(1, permission, 'fnc_tna_actual_date_update',1);
			release_freezing();
			
		}
	}
	</script>
			
			</head>
			<body onLoad="set_hotkey()">
			<div align="center" style="width:100%">
			<? 
				echo load_freeze_divs ("../../../",$permission,1);
			?>
			<form>
				<input type="hidden" id="auto_field_data_str" name="auto_field_data_str"  value="" />
			</form>
			
			<table><tr><td><font size="+1"><b><? echo $tna_task_array[$tna_result[0][csf('task_number')]]; ?></b></font></td></tr></table>  
			<table width="600" cellspacing="0" cellpadding="0" class="rpt_table">
				<thead>
					<th width="100">Buyer Name</th>
					<th width="100">Job No</th>
					<th width="120">Style Ref No</th>
					<th width="120">PO Number</th>
				</thead>
				<tr>
					<td><? echo $buyer_arr[$result[0][csf('buyer_name')]]; ?></td>
					<td> <? echo $result[0][csf('job_no')]; ?></td>
					<td><? echo $result[0][csf('style_ref_no')]; ?></td>
					<td><? echo  $result[0][csf('po_number')]; ?></td>
				</tr>
				<tr>
					<td colspan="4" height="15"></td>
				</tr>
				<tr>
					<td align="right">Plan Start Date</td>
					<td>
						<input type="text" <? if($type==2 ||  $type==3 || $plan_status==0) echo "disabled='disabled'";  ?> name="txt_plan_start_date" id="txt_plan_start_date" class="datepicker" style="width:100px" value="<? if($ts_history_con==1){echo change_date_format($tna_history[0][csf('task_start_date')]);} else if($ts_result_con==0){echo "";}else{ echo change_date_format($tna_result[0][csf('task_start_date')]);} ?>" />
					</td>
					
					<td align="right">Plan Finish Date</td>
					<td>
						<input type="text" <? if($type==2 ||  $type==3 || $plan_status==0) echo "disabled='disabled'";  ?> name="txt_plan_finish_date" id="txt_plan_finish_date" class="datepicker" style="width:100px"  value="<? if($tf_history_con==1){echo change_date_format($tna_history[0][csf('task_finish_date')]);} else if($tf_result_con==0){echo "";}else echo change_date_format($tna_result[0][csf('task_finish_date')]); ?>"/>
					</td>
				</tr>
				<tr>
					<td align="right">Actual Start Date </td>
					<td>
						<input type="text" <? if($type==1 ||  $type==3 || $actcual_status==0) echo "disabled='disabled'";  ?> name="txt_actual_start_date" id="txt_actual_start_date" class="datepicker" style="width:100px" value="<?  if($as_history_con==1){echo change_date_format($tna_history[0][csf('actual_start_date')]);} else if($as_result_con==0){echo "";}else echo change_date_format($tna_result[0][csf('actual_start_date')]); ?>" />
					</td>
					<td align="right">Actual Finish Date</td>
					<td>
						<input type="text" <? if($type==1 ||  $type==3 || $actcual_status==0) echo "disabled='disabled'";  ?> name="txt_actual_finish_date" id="txt_actual_finish_date" class="datepicker" style="width:100px" value="<?   if($af_history_con==1){echo change_date_format($tna_history[0][csf('actual_finish_date')]);} else if($af_result_con==0){echo "";}else echo change_date_format($tna_result[0][csf('actual_finish_date')]); ?>" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Commitment Start Date</td>
					<td>
						<input type="text" name="txt_commitment_start_date" id="txt_commitment_start_date" class="datepicker" style="width:100px" value="<?  if($cs_history_con==1){echo change_date_format($tna_history[0][csf('commit_start_date')]);} else if($cs_history_con==0){echo "";}else echo change_date_format($tna_result[0][csf('commit_start_date')]); ?>" />
					</td>
					<td align="right">Commitment End Date</td>
					<td>
						<input type="text" name="txt_commitment_end_date" id="txt_commitment_end_date" class="datepicker" style="width:100px" value="<? if($ce_history_con==1){echo change_date_format($tna_history[0][csf('commit_end_date')]);} else if($ce_history_con==0){echo "";}else{echo change_date_format($tna_result[0][csf('commit_end_date')]);} ?>" />
					</td>
				</tr>

				<tr>
					<td colspan="4">
					<?
						$user_arr=return_library_array( "select id, USER_NAME from USER_PASSWD where id={$tna_history[0][csf('update_by')]}",'id','USER_NAME');
						?>
						<b>Update By:</b> <?= $user_arr[$tna_history[0][csf('update_by')]];?>
						<b>Update Date:</b> <?= $tna_history[0][csf('update_date')];?>
					</td>
				</tr>
				
				<tr>
					<td colspan="4" height="50" valign="middle" align="center" class="button_container">
					<input type="hidden" id="txt_plan_actual_history" name="txt_plan_actual_history"  value="<? echo $tna_history[0][csf('id')].'_'.$tna_result[0][csf('template_id')].'_'.$tna_result[0][csf('task_number')].'_'.$po_id.'_'.$result[0][csf('job_no')]; ?>" />
					<input type="hidden" id="txt_update_tna_id" name="txt_update_tna_id"  value="<? echo $mid; ?>" />
					<input type="hidden" id="txt_update_tna_type" name="txt_update_tna_type"  value="<? echo $type; ?>" />
					<? echo load_submit_buttons( $permission, "fnc_tna_actual_date_update", 1,0 ,"",2) ; ?> 
					</td>
				</tr>
			</table>

			</div>
		</body>           
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
		</html>
			<?
		die;

}

if($action=="tna_target_save_update_delete_popup")
{
	
	//echo load_html_head_contents("TNA","../../../", 1, 1, $unicode);
	echo load_html_head_contents("TNA", "../../../", 1, 1,'','','',1);
	extract($_REQUEST);


		
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		
		$sql ="select a.po_number,b.job_no,b.buyer_name,b.style_ref_no,a.GROUPING from  wo_po_break_down a,wo_po_details_master b where a.id=$po_id and a.job_no_mst=b.job_no";
		//echo $sql;
		$result=sql_select($sql);
		
		$tna= "select APPROVED,READY_TO_APPROVED,template_id,task_number,task_start_date,task_finish_date,actual_start_date,actual_finish_date,plan_start_flag,plan_finish_flag,commit_start_date,commit_end_date from  tna_process_mst where id=$tna_mst_id ";
		$tna_result=sql_select($tna);
		//echo $tna;
		
		$mod_sql= sql_select("select id,task_catagory,task_name,task_short_name,task_type,completion_percent from lib_tna_task where is_deleted = 0 and status_active=1  AND  task_type = 1 order by task_sequence_no asc");
		$tna_task_array=array();
		foreach ($mod_sql as $row)
		{	
			$tna_task_array[$row[csf("task_name")]] = $row[csf("task_short_name")];
		}
		
		//History data start------------------------
		$tna_history_sql= "select id, template_id,task_number, job_no, po_number_id, task_start_date, task_finish_date, actual_start_date, actual_finish_date,plan_start_flag,plan_finish_flag,commit_start_date,commit_end_date from  tna_plan_actual_history where  is_deleted = 0 and status_active=1 and template_id=".$tna_result[0][csf('template_id')]." and task_number=".$tna_result[0][csf('task_number')]." and po_number_id=$po_id and job_no='".$result[0][csf('job_no')]."' order by id desc";
		$tna_history=sql_select($tna_history_sql);
		//History data end------------------------
		
		//var_dump($tna_history);
	
		if($tna_history[0][csf('task_start_date')]==""){$ts_history_con=0;}
		else if($tna_history[0][csf('task_start_date')]=="0000-00-00"){$ts_history_con=0;}else{$ts_history_con=1;}
		if($tna_result[0][csf('task_start_date')]==""){$ts_result_con=0;}
		else if($tna_result[0][csf('task_start_date')]=="0000-00-00"){$ts_result_con=0;}else{$ts_result_con=1;}


		if($tna_history[0][csf('task_finish_date')]==""){$tf_history_con=0;}
		else if($tna_history[0][csf('task_finish_date')]=="0000-00-00"){$tf_history_con=0;}else{$tf_history_con=1;}
		if($tna_result[0][csf('task_finish_date')]==""){$tf_result_con=0;}
		else if($tna_result[0][csf('task_finish_date')]=="0000-00-00"){$tf_result_con=0;}else{$tf_result_con=1;}


		if($tna_history[0][csf('actual_start_date')]==""){$as_history_con=0;}
		else if($tna_history[0][csf('actual_start_date')]=="0000-00-00"){$as_history_con=0;}else{$as_history_con=1;}
		if($tna_result[0][csf('actual_start_date')]==""){$as_result_con=0;}
		else if($tna_result[0][csf('actual_start_date')]=="0000-00-00"){$as_result_con=0;}else{$as_result_con=1;}

		if($tna_history[0][csf('actual_finish_date')]==""){$af_history_con=0;}
		else if($tna_history[0][csf('actual_finish_date')]=="0000-00-00"){$af_history_con=0;}else{$af_history_con=1;}
		if($tna_result[0][csf('actual_finish_date')]==""){$af_result_con=0;}
		else if($tna_result[0][csf('actual_finish_date')]=="0000-00-00"){$af_result_con=0;}else{$af_result_con=1;}

		
		if($tna_history[0][csf('commit_start_date')]==""){$cs_history_con=0;}
		else if($tna_history[0][csf('commit_start_date')]=="0000-00-00"){$cs_history_con=0;}else{$cs_history_con=1;}
		if($tna_result[0][csf('commit_start_date')]==""){$af_result_con=0;}
		else if($tna_result[0][csf('commit_start_date')]=="0000-00-00"){$af_result_con=0;}else{$af_result_con=1;}
		
		if($tna_history[0][csf('commit_end_date')]==""){$ce_history_con=0;}
		else if($tna_history[0][csf('commit_end_date')]=="0000-00-00"){$ce_history_con=0;}else{$ce_history_con=1;}
		if($tna_result[0][csf('commit_end_date')]==""){$ce_result_con=0;}
		else if($tna_result[0][csf('commit_end_date')]=="0000-00-00"){$ce_result_con=0;}else{$ce_result_con=1;}
	
	
	?> 
    
    
     <script>
	 
	/* $(document).ready(function(e) {
		 get_submitted_data_string('',"../../../"); 
        
    });*/
	
	
	 
	 
	 var permission='<? echo $permission; ?>';
	function fnc_tna_actual_date_update( operation )
	{
		var start_date='<? echo change_date_format($tna_result[0][csf('task_start_date')]);?>';
		var curr_start_date=$('#txt_plan_start_date').val();
		var history_start_date='<? echo change_date_format($tna_history[0][csf('task_start_date')]);?>';
		
		var finish_date='<? echo change_date_format($tna_result[0][csf('task_finish_date')]);?>';
		var curr_finish_date=$('#txt_plan_finish_date').val();
		var history_finish_date='<? echo change_date_format($tna_history[0][csf('task_finish_date')]);?>';

		var start_flag=0;var finish_flag=0;
		if(start_date!=curr_start_date){start_flag=1;}
		if((history_start_date!=curr_start_date) && history_start_date){ start_flag=1;}
		
		if(finish_date!=curr_finish_date){finish_flag=1;}
		if((history_finish_date!=curr_finish_date) && history_finish_date){ finish_flag=1;}
		 
		var data="action=save_update_delete&operation="+operation+'&start_flag='+start_flag+'&finish_flag='+finish_flag+get_submitted_data_string('txt_actual_start_date*txt_actual_finish_date*txt_update_tna_id*txt_plan_start_date*txt_plan_finish_date*txt_update_tna_type*txt_plan_actual_history*txt_commitment_start_date*txt_commitment_end_date',"../../../");
		//alert (data);return;
		freeze_window(operation);
		http.open("POST","tna_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_tna_actual_date_update_reponse;
	}


	function fnc_tna_actual_date_update_reponse()
	{
		if(http.readyState == 4) 
		{	
			 //alert(http.responseText);return;
			var reponse=trim(http.responseText).split('**');
			show_msg(trim(reponse[0]));
			
			if(reponse[0]==1)
			{
				$('#auto_field_data_str').val(http.responseText);
				//parent.emailwindow.hide();
			}
			else
			{
				alert('Invalid Operation');
			}
			
			//alert (reponse[0]);
			
			
			//document.getElementById('report_container').innerHTML  = reponse[1];
			set_button_status(1, permission, 'fnc_tna_actual_date_update',1);
			release_freezing();
			
		}
	}


	let set_target_entry_form = ()=>{
		let txt_plan_start_date = document.querySelector('#txt_plan_start_date').value;
		let txt_plan_finish_date = document.querySelector('#txt_plan_finish_date').value;
		let task_id = '<?=$task_id;?>';
		let po_id = '<?=$po_id;?>';
		let tna_mst_id = '<?=$tna_mst_id;?>';
		let permission = '<?=$permission;?>';

		var data="action=target_entry_form&txt_plan_start_date="+txt_plan_start_date+"&txt_plan_finish_date="+txt_plan_finish_date+"&task_id="+task_id+"&po_id="+po_id+"&tna_mst_id="+tna_mst_id+"&permission="+permission;
		//alert (data);return;
		//freeze_window(operation);
		http.open("POST","tna_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = ()=>{
			if(http.readyState == 4) 
			{ 
				
				
				document.querySelector('#target_entry_form_container').innerHTML = http.responseText;
				if(task_id == 48){
					fnGridView('gvMain',7);
				}
				else if(task_id == 52){
					fnGridView('gvMain',6);
				}
				else if(task_id == 60){
					fnGridView('gvMain',7);
				}
				else if(task_id == 63 || task_id == 61){
					fnGridView('gvMain',5);
				}
				else if(task_id == 73){
					fnGridView('gvMain',7);
				}
				else if(task_id == 70 || task_id == 71){ 
					fnGridView('gvMain',8);
				}
				else{
					fnGridView('gvMain',3);
				}
				
				$('#gvMain').freezeTable('update');
				
			}
		}
	}

	

	let calculate_day_val = (rowSelectorKey,colSelectorKey,all_date_key,task_id,other_selector)=>{

		var currVal = document.querySelector('#targetQty_'+rowSelectorKey+'_'+colSelectorKey).value*1;
		if( Number.isInteger(currVal) == false ){ 
			document.querySelector('#targetQty_'+rowSelectorKey+'_'+colSelectorKey).style.color = 'red';
			document.querySelector('#targetQty_'+rowSelectorKey+'_'+colSelectorKey).value=0;
			return;			
		}
		else{
			document.querySelector('#targetQty_'+rowSelectorKey+'_'+colSelectorKey).style.color = 'black';
		}
		

		//'plan_qty_td*plan_bal_td'
		var all_date_key_arr = all_date_key.split('*');
		var targetQtyVal=0;
		all_date_key_arr.forEach(function(item, index){
			targetQtyVal += document.querySelector('#targetQty_'+rowSelectorKey+'_'+item).value * 1;
		});
		
		var req_qty_td = document.querySelector('#req_qty_td_'+rowSelectorKey).innerHTML*1;
		let bal = req_qty_td - targetQtyVal;

		if(bal<0){ //alert('#targetQty_'+rowSelectorKey+'_'+colSelectorKey);
			document.querySelector('#targetQty_'+rowSelectorKey+'_'+colSelectorKey).style.color = 'red';
			document.querySelector('#targetQty_'+rowSelectorKey+'_'+colSelectorKey).value=0;
			return;			
		}
		$('#plan_qty_td_'+rowSelectorKey).text(targetQtyVal.toFixed(0));
		$('#plan_bal_td_'+rowSelectorKey).text(bal.toFixed(0));
		$('#gvMain').freezeTable('update');

	}


	let fn_tna_target_save_update_delete = (operation)=>{

		let txt_plan_start_date = document.querySelector('#txt_plan_start_date').value;
		let txt_plan_finish_date = document.querySelector('#txt_plan_finish_date').value;
		let task_id = '<?=$task_id;?>';
		let po_id = '<?=$po_id;?>';
		let tna_mst_id = '<?=$tna_mst_id;?>';
		let all_date_key = document.querySelector('#all_date_key').value;
		let all_date_key_arr = all_date_key.split('*');
		
		let all_row_key = document.querySelector('#all_row_key').value;
		let all_row_key_arr = all_row_key.split('**');
		var row_filed_data_str = '';

		var dateQtyFiledArr=Array();

		all_row_key_arr.forEach(function(val, key){
			all_date_key_arr.forEach(function(item, index){
				dateQtyFiledArr.push('targetQty_'+val+'_'+item);
			});
		});


		if(task_id == 48){
			var rowFiledDataArr=Array();
			all_row_key_arr.forEach(function(val, key){
				rowFiledDataArr.push(document.querySelector('#count_id'+val).title + '**' + document.querySelector('#component_id'+val).title);
			});
			row_filed_data_str = rowFiledDataArr.join('__');
		}

		else if(task_id == 60){
			var rowFiledDataArr=Array();
			all_row_key_arr.forEach(function(val, key){
				rowFiledDataArr.push(document.querySelector('#yarn_count_deter_id'+val).title );
			});
			row_filed_data_str = rowFiledDataArr.join('__');
		}	
		else if(task_id == 52){
			var rowFiledDataArr=Array();
			all_row_key_arr.forEach(function(val, key){
				rowFiledDataArr.push(document.querySelector('#yarn_color_id'+val).title );
			});
			row_filed_data_str = rowFiledDataArr.join('__');
		}
		else if(task_id == 73){
			var rowFiledDataArr=Array();
			all_row_key_arr.forEach(function(val, key){
				rowFiledDataArr.push(document.querySelector('#source_id'+val).title + '**' + document.querySelector('#uom_id'+val).title);
			});
			row_filed_data_str = rowFiledDataArr.join('__');
		}
		else if(task_id == 70 || task_id == 71){
			var rowFiledDataArr=Array();
			all_row_key_arr.forEach(function(val, key){
				rowFiledDataArr.push(document.querySelector('#type_id'+val).title + '**' + document.querySelector('#item_id'+val).title);
			});
			row_filed_data_str = rowFiledDataArr.join('__');
		}
	 
		
		var data='action=tna_target_save_update_delete&txt_plan_start_date='+txt_plan_start_date+'&txt_plan_finish_date='+txt_plan_finish_date+'&task_id='+task_id+'&po_id='+po_id+'&tna_mst_id='+tna_mst_id+'&all_row_key='+all_row_key+'&row_filed_data_str='+row_filed_data_str +'&operation='+operation  + get_submitted_data_string(dateQtyFiledArr.join('*'),"../../../");	
		 


		freeze_window(operation);
		http.open("POST","tna_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = ()=>{
			if(http.readyState == 4) 
			{ 
				var reponse=trim(http.responseText).split('**');
				if(reponse[0] == 1){
					set_button_status(1, permission, 'fn_tna_target_save_update_delete',4);
					alert("Save Success");
				}
				release_freezing();
				//alert(http.responseText);
			}
		}


	}



	</script>
			
		</head>
			<body onLoad="set_hotkey()">
			<? 
				echo load_freeze_divs ("../../../",$permission,1);
			?>
		<form id="targetForm">
		<input type="hidden" id="auto_field_data_str" name="auto_field_data_str"  value="" />
		<div align="center">
		<fieldset style="width:620px;">
			<table><tr><td><font size="+1"><b><? echo $tna_task_array[$tna_result[0][csf('task_number')]]; ?></b></font></td></tr></table>  
			<table width="600" cellspacing="0" cellpadding="0" class="rpt_table">
				<thead>
					<th width="100">Buyer Name</th>
					<th width="100">Job No</th>
					<th width="120">Style Ref No</th>
					<th width="120">PO Number</th>
					<th width="120">IR No</th>
				</thead>
				<tr>
					<td><? echo $buyer_arr[$result[0][csf('buyer_name')]]; ?></td>
					<td> <? echo $result[0][csf('job_no')]; ?></td>
					<td><? echo $result[0][csf('style_ref_no')]; ?></td>
					<td><? echo  $result[0][csf('po_number')]; ?></td>
					<td><? echo  $result[0][csf('GROUPING')]; ?></td>
				</tr>
				</table>
				<table cellspacing="3" cellpadding="4">
				<tr>
					<td align="right">Plan Start Date</td>
					<td>
						<input type="text" <? if($type==2 ||  $type==3) echo "disabled='disabled'";  ?> name="txt_plan_start_date" id="txt_plan_start_date" class="datepicker" style="width:100px" value="<? if($ts_history_con==1){echo change_date_format($tna_history[0][csf('task_start_date')]);} else if($ts_result_con==0){echo "";}else{ echo change_date_format($tna_result[0][csf('task_start_date')]);} ?>" onchange="set_target_entry_form()" />
					</td>
					<td align="right">Plan Finish Date</td>
					<td>
						<input type="text" <? if($type==2 ||  $type==3) echo "disabled='disabled'";  ?> name="txt_plan_finish_date" id="txt_plan_finish_date" class="datepicker" style="width:100px"  value="<? if($tf_history_con==1){echo change_date_format($tna_history[0][csf('task_finish_date')]);} else if($tf_result_con==0){echo "";}else echo change_date_format($tna_result[0][csf('task_finish_date')]); ?>" onchange="set_target_entry_form()" />
					</td>
				</tr>
				<tr>
					<td align="right">Actual Start Date </td>
					<td>
						<input type="text" <? if($type==1 ||  $type==3) echo "disabled='disabled'";  ?> name="txt_actual_start_date" id="txt_actual_start_date" class="datepicker" style="width:100px" value="<?  if($as_history_con==1){echo change_date_format($tna_history[0][csf('actual_start_date')]);} else if($as_result_con==0){echo "";}else echo change_date_format($tna_result[0][csf('actual_start_date')]); ?>" />
					</td>
					<td align="right">Actual Finish Date</td>
					<td>
						<input type="text" <? if($type==1 ||  $type==3) echo "disabled='disabled'";  ?> name="txt_actual_finish_date" id="txt_actual_finish_date" class="datepicker" style="width:100px" value="<?   if($af_history_con==1){echo change_date_format($tna_history[0][csf('actual_finish_date')]);} else if($af_result_con==0){echo "";}else echo change_date_format($tna_result[0][csf('actual_finish_date')]); ?>" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Commitment Start Date</td>
					<td>
						<input type="text" name="txt_commitment_start_date" id="txt_commitment_start_date" class="datepicker" style="width:100px" value="<?  if($cs_history_con==1){echo change_date_format($tna_history[0][csf('commit_start_date')]);} else if($cs_history_con==0){echo "";}else echo change_date_format($tna_result[0][csf('commit_start_date')]); ?>" />
					</td>
					<td align="right">Commitment End Date</td>
					<td>
						<input type="text" name="txt_commitment_end_date" id="txt_commitment_end_date" class="datepicker" style="width:100px" value="<? if($ce_history_con==1){echo change_date_format($tna_history[0][csf('commit_end_date')]);} else if($ce_history_con==0){echo "";}else{echo change_date_format($tna_result[0][csf('commit_end_date')]);} ?>" />
					</td>
				</tr>
				
				<tr>
					<td colspan="4" valign="middle" align="center" class="button_container">
					<input type="hidden" id="txt_plan_actual_history" name="txt_plan_actual_history"  value="<? echo $tna_history[0][csf('id')].'_'.$tna_result[0][csf('template_id')].'_'.$tna_result[0][csf('task_number')].'_'.$po_id.'_'.$result[0][csf('job_no')]; ?>" />
					<input type="hidden" id="txt_update_tna_id" name="txt_update_tna_id"  value="<? echo $tna_mst_id; ?>" />
					<input type="hidden" id="txt_update_tna_type" name="txt_update_tna_type"  value="<? echo $type; ?>" />
					<? echo load_submit_buttons( $permission, "fnc_tna_actual_date_update", 1,0 ,"",2) ; ?> 
					</td>
				</tr>
			</table>
		</fieldset>
		</div>
		<div id="target_entry_form_container"></div>
		<div align="center" ><button onclick="parent.emailwindow.hide();" class="formbutton" style="width:100px;"> Close </button></div>
		</form>


		
		</body>           
			<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
			<script>
				set_target_entry_form();
			</script>
			<script src="../../../js/freeze-table.js"></script>

	<script>

		let fnGridView = (freeze_table_select='table-multi-columns',columnNum=6) =>{
			$("#"+freeze_table_select).freezeTable({
				'columnNum' : columnNum,
				'scrollable':true,
				'freezeColumnHead':true,
				'columnKeep': true,
			});	
		}

		 
	</script>

	
	
	<?  
		die;

}


if($action=="target_entry_form")
{
	 
	//echo load_freeze_divs ("../../../",$permission,1);
 

	extract($_REQUEST);

	$plan_start_date = date('d-M-Y', strtotime($txt_plan_start_date));
	$plan_finish_date = date('d-M-Y', strtotime($txt_plan_finish_date));

	//......................
	$total_day = datediff('d', $txt_plan_start_date,$txt_plan_finish_date);
	$dayArr=array();
	$dformat = "d-M";
	for($i=0; $i < $total_day; $i++){
		$day = date($dformat, strtotime($txt_plan_start_date. " + $i day"));
		$dmyKey = date('dmY', strtotime($txt_plan_start_date. " + $i day"));
		$dayArr[$dmyKey] = $day;
	}
	$colspan = count($dayArr) + 3;
	$width = (count($dayArr)*55) + 350;
	//..........................
 
	if($task_id == 86 || $task_id == 90 || $task_id == 84 || $task_id == 267 || $task_id == 268 || $task_id == 88 || $task_id == 122)
	{

		if($task_id == 267 || $task_id == 268)
		{
			load_class('../../../',['emblishments']);

			$condition= new condition();
			if(trim($po_id) !=''){
				$condition->po_id(" =$po_id");
			}
			$condition->init();
			$costPerArr=$condition->getCostingPerArr();
			$emblishment= new emblishment($condition);
            $emblishment_costing_arr=$emblishment->getQtyArray_by_orderAndEmbname();
		}
		if($task_id == 90)
		{
			load_class('../../../',['washes']);
			$condition= new condition();
			if(trim($po_id) !=''){
				$condition->po_id(" =$po_id");
			}
			$condition->init();
			$costPerArr=$condition->getCostingPerArr();
			$wash= new wash($condition);
            $emblishment_costing_arr_name=$wash->getQtyArray_by_orderAndEmbname();
		}

		$tna_plan_target_sql = "select TNA_MST_ID,TASK_ID,TASK_TYPE, PO_ID,PLAN_QTY,PLAN_DATE, STATUS_ACTIVE,IS_DELETED,INSERTED_BY,INSERT_DATE  from TNA_PLAN_TARGET where TNA_MST_ID=$tna_mst_id and PO_ID=$po_id and TASK_ID=$task_id and task_type=1 and PLAN_DATE between '$plan_start_date' and '$plan_finish_date'";
		  //echo $tna_plan_target_sql;
		$tna_plan_target_sql_res = sql_select( $tna_plan_target_sql );
		$tna_plan_target_arr = array();
		foreach( $tna_plan_target_sql_res as $row ) 
		{
			$dmyKey = date('dmY', strtotime($row['PLAN_DATE']));
			$tna_plan_target_arr[$row['PO_ID'].$dmyKey] = $row['PLAN_QTY']; 
		}
 
		//$sql = "select d.BUYER_NAME,e.GROUPING,b.PO_BREAK_DOWN_ID,sum((b.requirment/b.PCS)*c.PLAN_CUT_QNTY) as REQUIRMENT from wo_pre_cos_fab_co_avg_con_dtls b,WO_PO_COLOR_SIZE_BREAKDOWN c,WO_PO_DETAILS_MASTER d,WO_PO_BREAK_DOWN e  WHERE b.po_break_down_id=e.id and e.job_id=d.id and  c.id=b.COLOR_SIZE_TABLE_ID  and b.po_break_down_id =$po_id  and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 group by d.BUYER_NAME,e.GROUPING,b.PO_BREAK_DOWN_ID";

		$sql = "SELECT a.job_no,a.BUYER_NAME,b.GROUPING,b.id as PO_BREAK_DOWN_ID,sum(c.ORDER_QUANTITY) as REQUIRMENT from WO_PO_DETAILS_MASTER a,WO_PO_BREAK_DOWN b,WO_PO_COLOR_SIZE_BREAKDOWN c WHERE a.id=b.job_id and b.job_id=c.job_id and b.id=c.PO_BREAK_DOWN_ID and b.id =$po_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 group by a.job_no,a.BUYER_NAME,b.GROUPING,b.id ";
		// echo $sql;

		$result = sql_select( $sql );
		$po_data_arr = array();
		foreach( $result as $row ) 
		{
			$po_data_arr[$row['PO_BREAK_DOWN_ID']]=[
				'REQUIRMENT' => $row['REQUIRMENT'],
				'BUYER_NAME' => $row['BUYER_NAME'],
				'GROUPING' => $row['GROUPING'],
				'job_no' => $row['JOB_NO'],
			];
		}

			

		?>
		 

		 <div id="gvMain">
		 <table border="1" rules="all"  cellspacing="0" cellpadding="0" class="rpt_table">
				 	<thead>
					<tr>
						<th width="150">Target&nbsp;Qty</th>
						<th width="100">Plan&nbsp;Qty</th>
						<th width="100">Plan&nbsp;Bal</th>
						<?php
						foreach ($dayArr as $key => $value) {
							echo "<th width='55'>$value</th>";
						}
						?>
					</tr>
					</thead>
			 
				<?php
				$rowKeyArr=array();
				foreach($po_data_arr as $po_id => $rows){
				$rowKeyArr[] = $po_id;
				$costingPer     =  $costPerArr[$rows['job_no']];
				if($task_id==267)//print
				{
					$req_qty = ($emblishment_costing_arr[$po_id][1]*$costingPer);
					$rows['REQUIRMENT'] = $req_qty;
				}
				if($task_id==268)//emb 
				{
					$req_qty = ($emblishment_costing_arr[$po_id][2]*$costingPer);
					$rows['REQUIRMENT'] = $req_qty;
				}
				if($task_id==90)//wash 
				{
					$req_qty = ($emblishment_costing_arr_name[$po_id][3]*$costingPer);
					$rows['REQUIRMENT'] = $req_qty;
				}
				?>
				<tr>
					<td align="right" id="req_qty_td_<?=$po_id;?>"><?= round($rows['REQUIRMENT']);?></td>
					<td align="right" id="plan_qty_td_<?=$po_id;?>"><?= round(array_sum($tna_plan_target_arr));?></td>
					<td  align="right" id="plan_bal_td_<?=$po_id;?>"><?= round($rows['REQUIRMENT'] - array_sum($tna_plan_target_arr));?></td>
					<?php
						$allDayKey = implode('*',array_keys($dayArr));
						foreach ($dayArr as $key => $value) {
							?>
							<td><input type="text" id="targetQty_<?=$po_id.'_'.$key;?>" onkeyup="calculate_day_val('<?=$po_id;?>','<?=$key;?>','<?=$allDayKey;?>',<?=$task_id;?>)" value="<?= $tna_plan_target_arr[$po_id.$key];?>" class="text_boxes_numeric" style="width:40px;"></td>
							<?
						}
					?>
				</tr>

				<?php
					}
				?>
			 
			</table>
			</div>


			<br>
			<input type="hidden" id="all_date_key" value="<?= $allDayKey;?>">
			<input type="hidden" id="all_row_key" value="<?= implode('**',$rowKeyArr);?>">
			<table width="100%">
				<tr><td align="center">
					<?php
						$su_status = (count($tna_plan_target_arr))?1:0;
						echo load_submit_buttons( $permission, "fn_tna_target_save_update_delete", $su_status,0 ,"",4) ; 
						?>
				</td></tr>
			</table>
			<? 

			
	}

	else if($task_id == 48){

		//'fabrics','yarns','washes','emblishments','trims'
		load_class('../../../',['yarns']);
		
		//---class
		$condition= new condition();
		if(trim($po_id) !=''){
			$condition->po_id(" =$po_id");
		}
		$condition->init();
		$yarn= new yarn($condition);
		$yarn_data_arr = $yarn->getOrderCountAndCompositionWiseYarnQtyArray();



			$yarn_sql_array=sql_select("SELECT  a.COUNT_ID, a.COPM_ONE_ID, sum (b.grey_fab_qnty*a.cons_ratio/100) AS GREY_REQ FROM wo_pre_cost_fab_yarn_cost_dtls a, wo_booking_dtls b WHERE a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.job_no=b.job_no and b.PO_BREAK_DOWN_ID=$po_id  and b.IS_SHORT=1 GROUP BY a.count_id, a.copm_one_id");
			$short_req_data_arr=array();
			foreach($yarn_sql_array as $row){
				$key = $row['COUNT_ID'].'_'.$row['COPM_ONE_ID'];
				$short_req_data_arr[$key] += $row['GREY_REQ'];
		}


		$req_data_arr=array();$count_id_arr=array();$composition_id_arr=array();
		foreach($yarn_data_arr[$po_id] as $count_id => $compositionArr){
			foreach($compositionArr as $composition_id => $reqQty){
				$key = $count_id.'_'.$composition_id;
				$req_data_arr[$key] = [
					'COUNT_ID' => $count_id,
					'COMPOSITION_ID' => $composition_id,
					'REQUIRMENT' => $reqQty
				];

				$count_id_arr[$count_id] = $count_id;
				$composition_id_arr[$composition_id] = $composition_id;
			}
		}


		$lib_count_arr=return_library_array( "select ID,YARN_COUNT from LIB_YARN_COUNT where is_deleted = 0 and status_active = 1 and id in(".implode(',',$count_id_arr).")",'ID','YARN_COUNT');

		$lib_comp_arr=return_library_array( "select ID,COMPOSITION_NAME from LIB_COMPOSITION_ARRAY where  is_deleted = 0 and status_active = 1 and id in(".implode(',',$composition_id_arr).")",'ID','COMPOSITION_NAME');



		$tna_plan_target_sql = "select COUNT_ID,COMPOSITION_ID,TNA_MST_ID,TASK_ID,TASK_TYPE, PO_ID,PLAN_QTY,PLAN_DATE, STATUS_ACTIVE,IS_DELETED,INSERTED_BY,INSERT_DATE  from TNA_PLAN_TARGET where TNA_MST_ID=$tna_mst_id and PO_ID=$po_id and TASK_ID=$task_id and task_type=1 and PLAN_DATE between '$plan_start_date' and '$plan_finish_date'";
		//echo $tna_plan_target_sql;
		$tna_plan_target_sql_res = sql_select( $tna_plan_target_sql );
		$tna_plan_target_arr = array();
		foreach( $tna_plan_target_sql_res as $row ) 
		{
			$dmyKey = date('dmY', strtotime($row['PLAN_DATE']));
			$tna_plan_target_arr[$row['COUNT_ID'].'_'.$row['COMPOSITION_ID']][$dmyKey] = $row['PLAN_QTY']; 
		}

	

		?>
		<div id="gvMain">
		<table border="1" rules="all" cellspacing="0" cellpadding="0" class="rpt_table">
			<thead>
				<tr>
					<th>Composition&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
					<th width="100">Count</th>
					<th width="100">Main&nbsp;Req</th>
					<th width="60">Exc&nbsp;Req</th>
					<th width="60">Total&nbsp;Req</th>
					<th width="60">Plan&nbsp;Qty </th>
					<th width="60">Plan&nbsp;Bal </th>
				<?php
					foreach ($dayArr as $key => $value) {
						echo "<th width='55'>$value</th>";
					}
				?>
				</tr>
			</thead>
		<?php
		$allDayKey = implode('*',array_keys($dayArr));
		$rowKeyArr = array();
		foreach($req_data_arr as $count_composition_id_key => $rows){
			$rowKeyArr[] = $count_composition_id_key;
		?>
			<tr>
				<td align="left" id="component_id<?= $count_composition_id_key;?>" title="<?= $rows['COMPOSITION_ID'];?>">
					<?= $lib_comp_arr[$rows['COMPOSITION_ID']];?>
				</td>
				<td align="right" id="count_id<?= $count_composition_id_key;?>" title="<?= $rows['COUNT_ID'];?>">
					<?= $lib_count_arr[$rows['COUNT_ID']];?>
				</td>
				<td align="right"><?= round($rows['REQUIRMENT']);?></td>
				<td align="right"><?= round($short_req_data_arr[$count_composition_id_key]);?></td>
				<td align="right" id="req_qty_td_<?=$count_composition_id_key;?>"><?= $total_req = round($rows['REQUIRMENT'] + $short_req_data_arr[$count_composition_id_key]);?></td>
				<td align="right" id="plan_qty_td_<?=$count_composition_id_key;?>"><?= round(array_sum($tna_plan_target_arr[$count_composition_id_key]));?></td>
				<td align="right" id="plan_bal_td_<?=$count_composition_id_key;?>"><?= round($total_req - array_sum($tna_plan_target_arr[$count_composition_id_key]));?></td>
				<?php
				foreach ($dayArr as $key => $value) {
				?>
					<td>
						<input type="text" id="targetQty_<?=$count_composition_id_key.'_'.$key;?>" onkeyup="calculate_day_val('<?=$count_composition_id_key;?>','<?=$key;?>','<?=$allDayKey;?>',<?=$task_id;?>)" value="<?= $tna_plan_target_arr[$count_composition_id_key][$key];?>" class="text_boxes_numeric" style="width:50px;">
					</td>
				<?php
				}
				?>
			</tr>
		<?php
		}
		?>
		</table>
		</div>
		
		<br>
			<input type="hidden" id="all_date_key" value="<?= $allDayKey;?>">
			<input type="hidden" id="all_row_key" value="<?= implode('**',$rowKeyArr);?>">
	

			<table width="100%">
				<tr><td align="center">
					<? 
					$su_status = (count($tna_plan_target_arr))?1:0;
					echo load_submit_buttons( $permission, "fn_tna_target_save_update_delete", $su_status,0 ,"",4) ; 
					?>
				</td></tr>
			</table>

		<?
		exit();

	}
	else if($task_id == 70 || $task_id == 71){
	

			//'fabrics','yarns','washes','emblishments','trims'
			load_class('../../../',['trims']);
			
			//---class
			$condition= new condition();
			if(trim($po_id) !=''){
				$condition->po_id(" =$po_id");
			}	
			$condition->init();
			$trims = new trims($condition);
			$trims_data_arr = $trims->getQtyArray_by_orderAndItemidTypeid();

			//print_r($trims_data_arr[$po_id]);die;

			$trims_type = ($task_id == 70)?1:2;

			$req_data_arr=array();$type_id_arr=array();$item_id_arr=array();
			foreach($trims_data_arr[$po_id] as $item_id => $typeDataArr){
				foreach($typeDataArr as $type_id => $reqQty){
					if($trims_type === $type_id){
						$key = $type_id.'_'.$item_id;
						$req_data_arr[$key] = [
							'TYPE_ID' => $type_id,
							'ITEM_ID' => $item_id,
							'REQUIRMENT' => $reqQty
						];

						$type_id_arr[$type_id] = $type_id;
						$item_id_arr[$item_id] = $item_id;
						$type_id_by_trims_group_id_arr[$item_id] = $type_id;
					}

				}
			}


					
			 $yarn_sql_array = sql_select("select a.TRIM_GROUP,sum(b.requirment) as CONS from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no   and a.PO_BREAK_DOWN_ID =$po_id   and a.sensitivity=0 and a.status_active=1 and a.is_deleted=0 o and a.is_short=1 and b.status_active=1 and b.is_deleted=0  group by a.trim_group ");

 
			 
			//echo $yarn_sql_array;die;
			$short_req_data_arr=array();
			foreach($yarn_sql_array as $row){
				$row['TYPE_ID'] = $type_id_by_trims_group_id_arr[$row['TRIM_GROUP']];
				$key = $row['TYPE_ID'].'_'.$row['TRIM_GROUP'];
				$short_req_data_arr[$key] += $row['CONS'];
		   }

		  // print_r($short_req_data_arr);




			$lib_trims_group_arr=return_library_array( "select ID,ITEM_NAME from LIB_ITEM_GROUP where is_deleted = 0 and status_active = 1 and id in(".implode(',',$item_id_arr).")",'ID','ITEM_NAME');
			$lib_trims_uom_arr=return_library_array( "select ID,TRIM_UOM from LIB_ITEM_GROUP where is_deleted = 0 and status_active = 1 and id in(".implode(',',$item_id_arr).")",'ID','TRIM_UOM');


			$tna_plan_target_sql = "select TYPE_ID,ITEM_ID,TNA_MST_ID,TASK_ID,TASK_TYPE, PO_ID,PLAN_QTY,PLAN_DATE, STATUS_ACTIVE,IS_DELETED,INSERTED_BY,INSERT_DATE  from TNA_PLAN_TARGET where TNA_MST_ID=$tna_mst_id and PO_ID=$po_id and TASK_ID=$task_id and task_type=1 and PLAN_DATE between '$plan_start_date' and '$plan_finish_date'";
			//echo $tna_plan_target_sql;
			$tna_plan_target_sql_res = sql_select( $tna_plan_target_sql );
			$tna_plan_target_arr = array();
			foreach( $tna_plan_target_sql_res as $row ) 
			{
				$dmyKey = date('dmY', strtotime($row['PLAN_DATE']));
				$tna_plan_target_arr[$row['TYPE_ID'].'_'.$row['ITEM_ID']][$dmyKey] = $row['PLAN_QTY']; 
			}

			//......................
			// $total_day = datediff('d', $txt_plan_start_date,$txt_plan_finish_date);
			// $dayArr=array();
			// $dformat = "d-M";
			// for($i=0; $i < $total_day; $i++){
			// 	$day = date($dformat, strtotime($txt_plan_start_date. " + $i day"));
			// 	$dmyKey = date('dmY', strtotime($txt_plan_start_date. " + $i day"));
			// 	$dayArr[$dmyKey] = $day;
			// }
			// $colspan = count($dayArr) + 8;
			//..........................


		?>
		<div id="gvMain">
		<table border="1" rules="all" cellspacing="0" cellpadding="0" class="rpt_table">
			<thead>
				<tr>
					<th width="100">Acc&nbsp;Type</th>
					<th width="150">Item&nbsp;Name</th>
					<th width="100">Unit</th>
					<th width="60">Main&nbsp;Req</th>
					<th width="60">Exc&nbsp;Req</th>
					<th width="60">Total&nbsp;Req</th>
					<th width="60">Plan&nbsp;Qty </th>
					<th width="60">Plan&nbsp;Bal</th>
				<?php
					foreach ($dayArr as $key => $value) {
						echo "<th width='45'>$value</th>";
					}
				?>
				</tr>
			</thead>
			<tbody>
		<?php
		$allDayKey = implode('*',array_keys($dayArr));
		$rowKeyArr = array();
		foreach($req_data_arr as $type_item_id_key => $rows){
			$rowKeyArr[] = $type_item_id_key;
		?>
			<tr>
				<td id="type_id<?= $type_item_id_key;?>" title="<?= $rows['TYPE_ID'];?>">
					<?= $trim_type[$rows['TYPE_ID']];?>
				</td>
				<td id="item_id<?= $type_item_id_key;?>" title="<?= $rows['ITEM_ID'];?>">
					<?= $lib_trims_group_arr[$rows['ITEM_ID']];?>
				</td>
				<td align="center"><?= $unit_of_measurement[$lib_trims_uom_arr[$rows['ITEM_ID']]];?></td>
				<td align="right"><?= round($rows['REQUIRMENT']);?></td>
				<td align="right"><?= round($short_req_data_arr[$type_item_id_key]);?></td>
				<td align="right" id="req_qty_td_<?=$type_item_id_key;?>"><?= $total_req = round($rows['REQUIRMENT'] + $short_req_data_arr[$type_item_id_key]);?></td>
				<td align="right" id="plan_qty_td_<?=$type_item_id_key;?>"><?= round(array_sum($tna_plan_target_arr[$type_item_id_key]));?></td>
				<td align="right" id="plan_bal_td_<?=$type_item_id_key;?>"><?= round($total_req - array_sum($tna_plan_target_arr[$type_item_id_key]));?></td>
				<?php
				foreach ($dayArr as $key => $value) {
				?>
					<td>
						<input type="text" id="targetQty_<?=$type_item_id_key.'_'.$key;?>" onkeyup="calculate_day_val('<?=$type_item_id_key;?>','<?=$key;?>','<?=$allDayKey;?>',<?=$task_id;?>)" value="<?= $tna_plan_target_arr[$type_item_id_key][$key];?>" class="text_boxes_numeric" style="width:50px;">
					</td>
				<?php
				}
				?>
			</tr>
			</tbody>
		<?php
		}
		?>
		</table><br>


			<input type="hidden" id="all_date_key" value="<?= $allDayKey;?>">
			<input type="hidden" id="all_row_key" value="<?= implode('**',$rowKeyArr);?>">

			<table width="100%">
				<tr><td align="center">
					<? 
					$su_status = (count($tna_plan_target_arr))?1:0;
					echo load_submit_buttons( $permission, "fn_tna_target_save_update_delete", $su_status,0 ,"",4) ; 
					?>
				</td></tr>
			</table>

		<?

	}
	else if($task_id == 73){

		//'fabrics','yarns','washes','emblishments','trims'
		load_class('../../../',['fabrics']);
		
		//---class
		$condition= new condition();
		if(trim($po_id) !=''){
			$condition->po_id(" =$po_id");
		}
		$condition->init();
		$fabric = new fabric($condition);
		$fabric_data_arr = $fabric->getQtyArray_by_orderAndFabricSource_knitAndwoven_greyAndfinish();


		 $yarn_sql_array=sql_select("SELECT a.FABRIC_SOURCE,a.UOM,  b.FIN_FAB_QNTY FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b WHERE  a.job_no=b.job_no and a.id=b.PRE_COST_FABRIC_COST_DTLS_ID and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and b.PO_BREAK_DOWN_ID=$po_id and b.IS_SHORT=1 ");
		 $short_req_data_arr=array();
		 foreach($yarn_sql_array as $row){
				$key = $row['FABRIC_SOURCE'].'_'.$row['UOM'];
				$short_req_data_arr[$key] += $row['FIN_FAB_QNTY'];
		}
		//print_r($short_req_data_arr);

		
		$req_data_arr=array();//$source_id_arr=array();$uom_id_arr=array();
		foreach($fabric_data_arr['knit']['finish'][$po_id] as $source_id => $uomArr){
			foreach($uomArr as $uom_id => $reqQty){
				$key = $source_id.'_'.$uom_id;
				$req_data_arr[$key] = [
					'SOURCE_ID' => $source_id,
					'UOM_ID' => $uom_id,
					'REQUIRMENT' => $reqQty
				];

				//$source_id_arr[$source_id] = $source_id;
				//$uom_id_arr[$uom_id] = $uom_id;
			}
		}



		$tna_plan_target_sql = "select SOURCE_ID,UOM_ID,TNA_MST_ID,TASK_ID,TASK_TYPE, PO_ID,PLAN_QTY,PLAN_DATE, STATUS_ACTIVE,IS_DELETED,INSERTED_BY,INSERT_DATE  from TNA_PLAN_TARGET where TNA_MST_ID=$tna_mst_id and PO_ID=$po_id and TASK_ID=$task_id and task_type=1 and PLAN_DATE between '$plan_start_date' and '$plan_finish_date'";
		//echo $tna_plan_target_sql;
		$tna_plan_target_sql_res = sql_select( $tna_plan_target_sql );
		$tna_plan_target_arr = array();
		foreach( $tna_plan_target_sql_res as $row ) 
		{
			$dmyKey = date('dmY', strtotime($row['PLAN_DATE']));
			$tna_plan_target_arr[$row['SOURCE_ID'].'_'.$row['UOM_ID']][$dmyKey] = $row['PLAN_QTY']; 
		}

		//......................
		// $total_day = datediff('d', $txt_plan_start_date,$txt_plan_finish_date);
		// $dayArr=array();
		// $dformat = "d-M";
		// for($i=0; $i < $total_day; $i++){
		// 	$day = date($dformat, strtotime($txt_plan_start_date. " + $i day"));
		// 	$dmyKey = date('dmY', strtotime($txt_plan_start_date. " + $i day"));
		// 	$dayArr[$dmyKey] = $day;
		// }
		// $colspan = count($dayArr) + 7;
		//..........................
			?>
			<div id="gvMain">
			<table border="1" rules="all" cellspacing="0" cellpadding="0" class="rpt_table">
				<thead>
					<tr>
						<th width="80">Fab.&nbsp;Source</th>
						<th width="60">Unit</th>
						<th width="100">Main&nbsp;Req</th>
						<th width="60">Exc&nbsp;Req</th>
						<th width="60">Total&nbsp;Req</th>
						<th width="60">Plan&nbsp;Qty </th>
						<th width="60">Plan&nbsp;Bal </th>
					<?php
						foreach ($dayArr as $key => $value) {
							echo "<th width='55'>$value</th>";
						}
					?>
					</tr>
				</thead>
			<?php
			$allDayKey = implode('*',array_keys($dayArr));
			$rowKeyArr = array();
			foreach($req_data_arr as $source_uom_id_key => $rows){
				$rowKeyArr[] = $source_uom_id_key;
			?>
				<tr>
					<td align="left" id="source_id<?= $source_uom_id_key;?>" title="<?= $rows['SOURCE_ID'];?>">
						<?= $fabric_source[$rows['SOURCE_ID']];?>
					</td>
					<td align="center" id="uom_id<?= $source_uom_id_key;?>" title="<?= $rows['UOM_ID'];?>">
						<?= $unit_of_measurement[$rows['UOM_ID']];?>
					</td>
					<td align="right"><?= round($rows['REQUIRMENT']);?></td>
					<td align="right"><?= round($short_req_data_arr[$source_uom_id_key]);?></td>
					<td align="right" id="req_qty_td_<?=$source_uom_id_key;?>"><?= $total_req = round($rows['REQUIRMENT'] + $short_req_data_arr[$source_uom_id_key]);?></td>
					<td align="right" id="plan_qty_td_<?=$source_uom_id_key;?>"><?= round(array_sum($tna_plan_target_arr[$source_uom_id_key]));?></td>
					<td align="right" id="plan_bal_td_<?=$source_uom_id_key;?>"><?= round($total_req - array_sum($tna_plan_target_arr[$source_uom_id_key]));?></td>
					<?php
					foreach ($dayArr as $key => $value) {
					?>
						<td>
							<input type="text" id="targetQty_<?=$source_uom_id_key.'_'.$key;?>" onkeyup="calculate_day_val('<?=$source_uom_id_key;?>','<?=$key;?>','<?=$allDayKey;?>',<?=$task_id;?>)" value="<?= $tna_plan_target_arr[$source_uom_id_key][$key];?>" class="text_boxes_numeric" style="width:50px;">
						</td>
					<?php
					}
					?>
				</tr>
			<?php
			}
			?>
			</table>
			</div>
		<br>
				<input type="hidden" id="all_date_key" value="<?= $allDayKey;?>">
				<input type="hidden" id="all_row_key" value="<?= implode('**',$rowKeyArr);?>">


				<table width="100%">
				<tr><td align="center">
						<? 
						$su_status = (count($tna_plan_target_arr))?1:0;
						echo load_submit_buttons( $permission, "fn_tna_target_save_update_delete", $su_status,0 ,"",4) ; 
						?>
					</td></tr>
				</table>
				

			<?
			exit();
	}

	else if($task_id == 63)
	{

		
		//'fabrics','yarns','washes','emblishments','trims'
		load_class('../../../',['fabrics']);
	
		//---class
		$condition= new condition();
		if(trim($po_id) !=''){
			$condition->po_id(" =$po_id");
		}
		$condition->init();
		$fabric = new fabric($condition);
		//$fabric_data_arr = $fabric->getQtyArray_by_orderColorType_knitAndwoven_greyAndfinish();
		$fabric_data_arr = $fabric->getQtyArray_by_orderAndColorTypeFabricSource_knitAndwoven_greyAndfinish();
		
		$yarn_sql_array=sql_select("SELECT b.FIN_FAB_QNTY FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b WHERE  a.job_no=b.job_no and a.id=b.PRE_COST_FABRIC_COST_DTLS_ID and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and b.PO_BREAK_DOWN_ID=$po_id and b.IS_SHORT=1 and a.color_type_id in (2,3,4,6,5) ");
		$short_req_data_arr=0;
		foreach($yarn_sql_array as $row){
			   $short_req_data_arr += $row['FIN_FAB_QNTY'];
	   }


		$tna_plan_target_sql = "select TNA_MST_ID,TASK_ID,TASK_TYPE, PO_ID,PLAN_QTY,PLAN_DATE, STATUS_ACTIVE,IS_DELETED,INSERTED_BY,INSERT_DATE  from TNA_PLAN_TARGET where TNA_MST_ID=$tna_mst_id and PO_ID=$po_id and TASK_ID=$task_id and task_type=1 and PLAN_DATE between '$plan_start_date' and '$plan_finish_date'";
		 //echo $tna_plan_target_sql;
		$tna_plan_target_sql_res = sql_select( $tna_plan_target_sql );
		$tna_plan_target_arr = array();
		foreach( $tna_plan_target_sql_res as $row ) 
		{
			$dmyKey = date('dmY', strtotime($row['PLAN_DATE']));
			$tna_plan_target_arr[$row['PO_ID']][$dmyKey] = $row['PLAN_QTY']; 
		}

		// echo "<pre>";
		// print_r($fabric_data_arr['knit']['finish'][$po_id][5][1]);
		// echo "</pre>";

		//$po_data_arr[$po_id]['REQUIRMENT']=array_sum($fabric_data_arr['knit']['finish'][$po_id][5]);
		 $po_data_arr[$po_id]['REQUIRMENT']=array_sum($fabric_data_arr['knit']['finish'][$po_id][5][1]);


		?>
		<div id="gvMain">
		 <table border="1" rules="all"  cellspacing="0" cellpadding="0" class="rpt_table">
				<thead>
				<tr>
					<th width="80">Main&nbsp;Req</th>
					<th width="80">Exc&nbsp;Req</th>
					<th width="80">Target&nbsp;Qty</th>
					<th width="80">Plan&nbsp;Qty</th>
					<th width="80">Plan&nbsp;Bal</th>
					<?php
					foreach ($dayArr as $key => $value) {
						echo "<th width='55'>$value</th>";
					}
					?>
				</tr>
				</thead>
			 
				<?php
				$rowKeyArr=array();
				foreach($po_data_arr as $po_id => $rows){
				$rowKeyArr[] = $po_id;
				$rows['EXC_REQUIRMENT'] = $short_req_data_arr;
				?>
				<tr>
					<td align="right"><?= round($rows['REQUIRMENT']);?></td>
					<td align="right"><?= round($rows['EXC_REQUIRMENT']);?></td>


					<td align="right" id="req_qty_td_<?=$po_id;?>"><?= round($rows['REQUIRMENT']+$rows['EXC_REQUIRMENT']);?></td>
					<td align="right" id="plan_qty_td_<?=$po_id;?>"><?= round(array_sum($tna_plan_target_arr[$po_id]));?></td>
					<td align="right" id="plan_bal_td_<?=$po_id;?>"><?= round($rows['REQUIRMENT'] - array_sum($tna_plan_target_arr[$po_id]));?></td>
					<?php
						$allDayKey = implode('*',array_keys($dayArr));
						foreach ($dayArr as $key => $value) {
							?>
							<td width='55'><input type="text" id="targetQty_<?=$po_id.'_'.$key;?>" onkeyup="calculate_day_val('<?=$po_id;?>','<?=$key;?>','<?=$allDayKey;?>',<?=$task_id;?>)" value="<?= $tna_plan_target_arr[$po_id][$key];?>" class="text_boxes_numeric" style="width:40px;"></td>
							<?
						}
					?>
				</tr>

				<?php
					}
				?>
			 
			</table>
			</div>


			<br>
			<input type="hidden" id="all_date_key" value="<?= $allDayKey;?>">
			<input type="hidden" id="all_row_key" value="<?= implode('**',$rowKeyArr);?>">
			<table width="100%">
				<tr><td align="center">
					<?php
						$su_status = (count($tna_plan_target_arr))?1:0;
						echo load_submit_buttons( $permission, "fn_tna_target_save_update_delete", $su_status,0 ,"",4) ; 
						?>
				</td></tr>
			</table>
			<? 

		exit();
	}


	else if($task_id == 61)
	{

		//'fabrics','yarns','washes','emblishments','trims'
		load_class('../../../',['fabrics']);
		//---class
		$condition= new condition();
		if(trim($po_id) !=''){
			$condition->po_id(" =$po_id");
		}
		$condition->init();
		$fabric = new fabric($condition);

		$fabric_data_arr = $fabric->getQtyArray_by_orderAndFabricSource_knitAndwoven_greyAndfinish();


		$yarn_sql_array=sql_select("SELECT b.GREY_FAB_QNTY FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b WHERE  a.job_no=b.job_no and a.id=b.PRE_COST_FABRIC_COST_DTLS_ID and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and b.PO_BREAK_DOWN_ID=$po_id and b.IS_SHORT=1 and a.FABRIC_SOURCE =1 ");
		$short_req_data_arr=0;
		foreach($yarn_sql_array as $row){
			   $short_req_data_arr += $row['GREY_FAB_QNTY'];
	   }



		$tna_plan_target_sql = "select TNA_MST_ID,TASK_ID,TASK_TYPE, PO_ID,PLAN_QTY,PLAN_DATE, STATUS_ACTIVE,IS_DELETED,INSERTED_BY,INSERT_DATE  from TNA_PLAN_TARGET where TNA_MST_ID=$tna_mst_id and PO_ID=$po_id and TASK_ID=$task_id and task_type=1 and PLAN_DATE between '$plan_start_date' and '$plan_finish_date'";
		 //echo $tna_plan_target_sql;
		$tna_plan_target_sql_res = sql_select( $tna_plan_target_sql );
		$tna_plan_target_arr = array();
		foreach( $tna_plan_target_sql_res as $row ) 
		{
			$dmyKey = date('dmY', strtotime($row['PLAN_DATE']));
			$tna_plan_target_arr[$row['PO_ID']][$dmyKey] = $row['PLAN_QTY']; 
		}


		$po_data_arr[$po_id]['REQUIRMENT'] = array_sum($fabric_data_arr['knit']['grey'][$po_id][1]);


		?>
		<div id="gvMain">
		 <table border="1" rules="all"  cellspacing="0" cellpadding="0" class="rpt_table">
				<thead>
				<tr>
					<th width="80">Main&nbsp;Req</th>
					<th width="80">Exc&nbsp;Req</th>
					<th width="80">Target&nbsp;Qty</th>
					<th width="80">Plan&nbsp;Qty</th>
					<th width="80">Plan&nbsp;Bal</th>
					<?php
					foreach ($dayArr as $key => $value) {
						echo "<th width='55'>$value</th>";
					}
					?>
				</tr>
				</thead>
			 
				<?php
				$rowKeyArr=array();
				foreach($po_data_arr as $po_id => $rows){
				$rowKeyArr[] = $po_id;
				$rows['EXC_REQUIRMENT'] = $short_req_data_arr;
				?>
				<tr>
					<td align="right"><?= round($rows['REQUIRMENT']);?></td>
					<td align="right"><?= round($rows['EXC_REQUIRMENT']);?></td>
					<td align="right" id="req_qty_td_<?=$po_id;?>"><?= round($rows['REQUIRMENT']+$rows['EXC_REQUIRMENT']);?></td>
					<td align="right" id="plan_qty_td_<?=$po_id;?>"><?= round(array_sum($tna_plan_target_arr[$po_id]));?></td>
					<td align="right" id="plan_bal_td_<?=$po_id;?>"><?= round($rows['REQUIRMENT'] - array_sum($tna_plan_target_arr[$po_id]));?></td>
					<?php
						$allDayKey = implode('*',array_keys($dayArr));
						foreach ($dayArr as $key => $value) {
							?>
							<td><input type="text" id="targetQty_<?=$po_id.'_'.$key;?>" onkeyup="calculate_day_val('<?=$po_id;?>','<?=$key;?>','<?=$allDayKey;?>',<?=$task_id;?>)" value="<?= $tna_plan_target_arr[$po_id][$key];?>" class="text_boxes_numeric" style="width:40px;"></td>
							<?
						}
					?>
				</tr>

				<?php
					}
				?>
			 
			</table>
			</div>


			<br>
			<input type="hidden" id="all_date_key" value="<?= $allDayKey;?>">
			<input type="hidden" id="all_row_key" value="<?= implode('**',$rowKeyArr);?>">
			<table width="100%">
				<tr><td align="center">
					<?php
						$su_status = (count($tna_plan_target_arr))?1:0;
						echo load_submit_buttons( $permission, "fn_tna_target_save_update_delete", $su_status,0 ,"",4) ; 
						?>
				</td></tr>
			</table>
			<? 

		exit();
	}

	else if($task_id == 52){



		$sql_array=sql_select("SELECT c.id as PO_ID,b.STRIPE_COLOR,sum(b.FABREQTOTKG) as QTY from WO_PRE_COST_FABRIC_COST_DTLS a,WO_PRE_STRIPE_COLOR b,WO_PO_BREAK_DOWN c where a.id=b.PRE_COST_FABRIC_COST_DTLS_ID and a.JOB_NO=c.job_no_mst and c.id=$po_id and a.COLOR_TYPE_ID in(2,3,4,6,5) and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and a.FABRIC_SOURCE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.yarn_dyed=1 group by c.id,b.STRIPE_COLOR");
		$req_data_arr=array();$strip_color_id_arr=array();
		foreach($sql_array as  $row){
				$req_data_arr[$row['STRIPE_COLOR']] = [
					'STRIPE_COLOR' => $row['STRIPE_COLOR'],
					'REQUIRMENT' => $row['QTY'],
					'PO_ID' => $row['PO_ID']
				];
				$strip_color_id_arr[$row['STRIPE_COLOR']] = $row['STRIPE_COLOR'];
		}


		$color_library=return_library_array( "select id,color_name from lib_color where id in(".implode(',',$strip_color_id_arr).")", "id", "color_name" );


		$tna_plan_target_sql = "select COLOUR_ID,TNA_MST_ID,TASK_ID,TASK_TYPE, PO_ID,PLAN_QTY,PLAN_DATE, STATUS_ACTIVE,IS_DELETED,INSERTED_BY,INSERT_DATE  from TNA_PLAN_TARGET where TNA_MST_ID=$tna_mst_id and PO_ID=$po_id and TASK_ID=$task_id and task_type=1 and PLAN_DATE between '$plan_start_date' and '$plan_finish_date'";
		//echo $tna_plan_target_sql;
		$tna_plan_target_sql_res = sql_select( $tna_plan_target_sql );
		$tna_plan_target_arr = array();
		foreach( $tna_plan_target_sql_res as $row ) 
		{
			$dmyKey = date('dmY', strtotime($row['PLAN_DATE']));
			$tna_plan_target_arr[$row['COLOUR_ID']][$dmyKey] = $row['PLAN_QTY']; 
		}

		load_class('../../../',['conversions']);
		$condition= new condition();
		if(trim($po_id) !=''){
			$condition->po_id(" =$po_id");
		}
		$condition->init();
		$conversion= new conversion($condition);
        $conversion_costing_arr=$conversion->getQtyArray_by_orderAndProcess();	

		?>
		<div id="gvMain">
		<table border="1" rules="all" cellspacing="0" cellpadding="0" class="rpt_table">
			<thead>
				<tr>
					<th width="100">Yarn&nbsp;Color&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
					<th width="100">Main&nbsp;Req</th>
					<th width="60">Exc&nbsp;Req</th>
					<th width="60">Total&nbsp;Req</th>
					<th width="60">Plan&nbsp;Qty </th>
					<th width="60">Plan&nbsp;Bal </th>
				<?php
					foreach ($dayArr as $key => $value) {
						echo "<th width='55'>$value</th>";
					}
				?>
				</tr>
			</thead>
		<?php
		$allDayKey = implode('*',array_keys($dayArr));
		$rowKeyArr = array();
		foreach($req_data_arr as $strip_color_id_key => $rows){
			$rowKeyArr[] = $strip_color_id_key;

			$req_qty = array_sum($conversion_costing_arr[$rows['PO_ID']][30]);
			$rows['REQUIRMENT'] = $req_qty;
		?>
			<tr>
				<td align="left" id="yarn_color_id<?= $strip_color_id_key;?>" title="<?= $rows['STRIPE_COLOR'];?>">
					<?= $color_library[$rows['STRIPE_COLOR']];?>
				</td>
				<td align="right"><?= round($rows['REQUIRMENT']);?></td>
				<td align="right"><?= round($short_req_data_arr[$strip_color_id_key]);?></td>
				<td align="right" id="req_qty_td_<?=$strip_color_id_key;?>"><?= $total_req = round($rows['REQUIRMENT'] + $short_req_data_arr[$strip_color_id_key]);?></td>
				<td align="right" id="plan_qty_td_<?=$strip_color_id_key;?>"><?= round(array_sum($tna_plan_target_arr[$strip_color_id_key]));?></td>
				<td align="right" id="plan_bal_td_<?=$strip_color_id_key;?>"><?= round($total_req - array_sum($tna_plan_target_arr[$strip_color_id_key]));?></td>
				<?php
				foreach ($dayArr as $key => $value) {
				?>
					<td>
						<input type="text" id="targetQty_<?=$strip_color_id_key.'_'.$key;?>" onkeyup="calculate_day_val('<?=$strip_color_id_key;?>','<?=$key;?>','<?=$allDayKey;?>',<?=$task_id;?>)" value="<?= $tna_plan_target_arr[$strip_color_id_key][$key];?>" class="text_boxes_numeric" style="width:50px;">
					</td>
				<?php
				}
				?>
			</tr>
		<?php
		}
		?>
		</table>
		</div>
		<br>
			<input type="hidden" id="all_date_key" value="<?= $allDayKey;?>">
			<input type="hidden" id="all_row_key" value="<?= implode('**',$rowKeyArr);?>">
			<table width="100%">
				<tr><td align="center">
					<? 
					$su_status = (count($tna_plan_target_arr))?1:0;
					echo load_submit_buttons( $permission, "fn_tna_target_save_update_delete", $su_status,0 ,"",4) ; 
					?>
				</td></tr>
			</table>

		<?
		exit();

	}

	else if($task_id == 60){

		$yarn_sql_array=sql_select("SELECT a.LIB_YARN_COUNT_DETER_ID,b.GREY_FAB_QNTY FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b WHERE  a.job_no=b.job_no and a.id=b.PRE_COST_FABRIC_COST_DTLS_ID and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and b.PO_BREAK_DOWN_ID=$po_id and b.IS_SHORT=1 and a.FABRIC_SOURCE=1 and a.FABRIC_SOURCE =1 ");
		$short_req_data_arr=array();
		foreach($yarn_sql_array as $row){
			   $short_req_data_arr[$row['LIB_YARN_COUNT_DETER_ID']] += $row['GREY_FAB_QNTY'];
	   }

		
		$yarn_sql_array=sql_select("select a.LIB_YARN_COUNT_DETER_ID as YARN_COUNT_DETER_ID,a.CONSTRUCTION,a.COMPOSITION,sum((b.requirment/b.PCS)*c.PLAN_CUT_QNTY) as REQUIRMENT from wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b,WO_PO_COLOR_SIZE_BREAKDOWN c WHERE c.id=b.COLOR_SIZE_TABLE_ID and a.id=b.PRE_COST_FABRIC_COST_DTLS_ID AND b.po_break_down_id=$po_id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 and a.FABRIC_SOURCE=1 group by a.LIB_YARN_COUNT_DETER_ID,a.CONSTRUCTION,a.COMPOSITION");


		$req_data_arr=array();
		foreach($yarn_sql_array as $row){
				$req_data_arr[$row['YARN_COUNT_DETER_ID']] = [
					'CONSTRUCTION' => $row['CONSTRUCTION'],
					'COMPOSITION' => $row['COMPOSITION'],
					'REQUIRMENT' => $row['REQUIRMENT'],
					'YARN_COUNT_DETER_ID' => $row['YARN_COUNT_DETER_ID']
				];
		}


		$tna_plan_target_sql = "select YARN_COUNT_DETER_ID,TNA_MST_ID,TASK_ID,TASK_TYPE, PO_ID,PLAN_QTY,PLAN_DATE, STATUS_ACTIVE,IS_DELETED,INSERTED_BY,INSERT_DATE  from TNA_PLAN_TARGET where TNA_MST_ID=$tna_mst_id and PO_ID=$po_id and TASK_ID=$task_id and task_type=1 and PLAN_DATE between '$plan_start_date' and '$plan_finish_date'";
		//echo $tna_plan_target_sql;
		$tna_plan_target_sql_res = sql_select( $tna_plan_target_sql );
		$tna_plan_target_arr = array();
		foreach( $tna_plan_target_sql_res as $row ) 
		{
			$dmyKey = date('dmY', strtotime($row['PLAN_DATE']));
			$tna_plan_target_arr[$row['YARN_COUNT_DETER_ID']][$dmyKey] = $row['PLAN_QTY']; 
		}

		//WO_PRE_COST_FABRIC_COST_DTLS

	

		?>
		<div id="gvMain">
		<table border="1" rules="all" cellspacing="0" cellpadding="0" class="rpt_table">
			<thead>
				<tr>
					<th width="150">Fabric&nbsp;Structure&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
					<th width="250">Composition&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
					<th width="60">Main&nbsp;Req</th>
					<th width="60">Exc&nbsp;Req</th>
					<th width="60">Total&nbsp;Req</th>
					<th width="60">Plan&nbsp;Qty </th>
					<th width="60">Plan&nbsp;Bal </th>
				<?php
					foreach ($dayArr as $key => $value) {
						echo "<th width='55'>$value</th>";
					}
				?>
				</tr>
			</thead>
		<?php
		$allDayKey = implode('*',array_keys($dayArr));
		$rowKeyArr = array();
		foreach($req_data_arr as $yarn_count_deter_id_key => $rows){
			$rowKeyArr[] = $yarn_count_deter_id_key;
		?>
			<tr>
				<td align="left" id="yarn_count_deter_id<?= $yarn_count_deter_id_key;?>" title="<?= $rows['YARN_COUNT_DETER_ID'];?>">
					<?= $rows['CONSTRUCTION'];?>
				</td>
				<td align="left"><p style="word-wrap: break-word;width:240px;"><?= wordwrap($rows['COMPOSITION'],45,"<br>\n");;?></p></td>
				<td align="right"><?= round($rows['REQUIRMENT']);?></td>
				<td align="right"><?= round($short_req_data_arr[$yarn_count_deter_id_key]);?></td>
				<td align="right" id="req_qty_td_<?=$yarn_count_deter_id_key;?>"><?= $total_req = round($rows['REQUIRMENT'] + $short_req_data_arr[$yarn_count_deter_id_key]);?></td>
				<td align="right" id="plan_qty_td_<?=$yarn_count_deter_id_key;?>"><?= round(array_sum($tna_plan_target_arr[$yarn_count_deter_id_key]));?></td>
				<td align="right" id="plan_bal_td_<?=$yarn_count_deter_id_key;?>"><?= round($total_req - array_sum($tna_plan_target_arr[$yarn_count_deter_id_key]));?></td>
				<?php
				foreach ($dayArr as $key => $value) {
				?>
					<td>
						<input type="text" id="targetQty_<?=$yarn_count_deter_id_key.'_'.$key;?>" onkeyup="calculate_day_val('<?=$yarn_count_deter_id_key;?>','<?=$key;?>','<?=$allDayKey;?>',<?=$task_id;?>)" value="<?= $tna_plan_target_arr[$yarn_count_deter_id_key][$key];?>" class="text_boxes_numeric" style="width:50px;">
					</td>
				<?php
				}
				?>
			</tr>
		<?php
		}
		?>
		</table>
		</div>
		<br>
			<input type="hidden" id="all_date_key" value="<?= $allDayKey;?>">
			<input type="hidden" id="all_row_key" value="<?= implode('**',$rowKeyArr);?>">
			<table width="100%">
				<tr><td align="center">
					<? 
					$su_status = (count($tna_plan_target_arr))?1:0;
					echo load_submit_buttons( $permission, "fn_tna_target_save_update_delete", $su_status,0 ,"",4) ; 
					?>
				</td></tr>
			</table>


		<?
		exit();

	}

}

if($action=="tna_target_save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$txt_plan_start_date = str_replace("'","",$txt_plan_start_date);
	$txt_plan_finish_date = str_replace("'","",$txt_plan_finish_date);

 
	//......................
	$total_day = datediff('d', $txt_plan_start_date,$txt_plan_finish_date);
	$dayArr=array();
	$dformat = "d-M-y";
	for($i=0; $i < $total_day; $i++){
		$day = date($dformat, strtotime($txt_plan_start_date. " + $i day"));
		$dmyKey = date('dmY', strtotime($txt_plan_start_date. " + $i day"));
		$dayArr[$dmyKey] = $day;
	}

	//..........................

	
	
	if ($operation==0)  
	{		
		$con = connect();
			$rID=execute_query("delete from TNA_PLAN_TARGET where TNA_MST_ID=$tna_mst_id and PO_ID=$po_id and TASK_ID=$task_id and task_type=1");
			$id=return_next_id( "id", "TNA_PLAN_TARGET", 1 ) ;

			
			if($task_id == 48){
				$field_array="ID, TNA_MST_ID,TASK_ID,TASK_TYPE, PO_ID, COUNT_ID, COMPOSITION_ID,PLAN_QTY,PLAN_DATE, STATUS_ACTIVE,IS_DELETED,INSERTED_BY,INSERT_DATE";
				$all_row_key_arr = explode('**',$all_row_key);
				$row_filed_data_arr = explode('__',$row_filed_data_str);

				$seq=0;
				foreach($all_row_key_arr as $row_key){
					list($count_id,$compostion_id) = explode('**',$row_filed_data_arr[$seq]);


					foreach($dayArr as $key => $plan_date){
						$targetQty = ${'targetQty_'.$row_key.'_'.$key};
						//echo $targetQty.'==';
						if ($data_array != "") $data_array .= ",";
						$data_array .= "(".$id.",".$tna_mst_id.",".$task_id.",'1',".$po_id.",".$count_id.",".$compostion_id.",'".str_replace("'","",$targetQty)."','".$plan_date."','1','0',".$_SESSION['logic_erp']['user_id'].",'" . $pc_date_time . "')";
						$id++;
					}
					$seq++;
				}
			}
			else if($task_id == 52){
				$field_array="ID, TNA_MST_ID,TASK_ID,TASK_TYPE, PO_ID, COLOUR_ID,PLAN_QTY,PLAN_DATE, STATUS_ACTIVE,IS_DELETED,INSERTED_BY,INSERT_DATE";
				$all_row_key_arr = explode('**',$all_row_key);
				$row_filed_data_arr = explode('__',$row_filed_data_str);

				$seq=0;
				foreach($all_row_key_arr as $row_key){
					list($colour_id) = explode('**',$row_filed_data_arr[$seq]);


					foreach($dayArr as $key => $plan_date){
						$targetQty = ${'targetQty_'.$row_key.'_'.$key};
						//echo $targetQty.'==';
						if ($data_array != "") $data_array .= ",";
						$data_array .= "(".$id.",".$tna_mst_id.",".$task_id.",'1',".$po_id.",".$colour_id.",'".str_replace("'","",$targetQty)."','".$plan_date."','1','0',".$_SESSION['logic_erp']['user_id'].",'" . $pc_date_time . "')";
						$id++;
					}
					$seq++;
				}
			}

			else if($task_id == 60){
				$field_array="ID, TNA_MST_ID,TASK_ID,TASK_TYPE, PO_ID, YARN_COUNT_DETER_ID,PLAN_QTY,PLAN_DATE, STATUS_ACTIVE,IS_DELETED,INSERTED_BY,INSERT_DATE";
				$all_row_key_arr = explode('**',$all_row_key);
				$row_filed_data_arr = explode('__',$row_filed_data_str);
				$seq=0;
				foreach($all_row_key_arr as $row_key){
					list($yarn_count_deter_id) = explode('**',$row_filed_data_arr[$seq]);


					foreach($dayArr as $key => $plan_date){
						$targetQty = ${'targetQty_'.$row_key.'_'.$key};
						//echo $targetQty.'==';
						if ($data_array != "") $data_array .= ",";
						$data_array .= "(".$id.",".$tna_mst_id.",".$task_id.",'1',".$po_id.",".$yarn_count_deter_id.",'".str_replace("'","",$targetQty)."','".$plan_date."','1','0',".$_SESSION['logic_erp']['user_id'].",'" . $pc_date_time . "')";
						$id++;
					}
					$seq++;
				}
			}
			
			else if($task_id == 73){
				$field_array="ID, TNA_MST_ID,TASK_ID,TASK_TYPE, PO_ID, SOURCE_ID, UOM_ID,PLAN_QTY,PLAN_DATE, STATUS_ACTIVE,IS_DELETED,INSERTED_BY,INSERT_DATE";
				$all_row_key_arr = explode('**',$all_row_key);
				$row_filed_data_arr = explode('__',$row_filed_data_str);

				$seq=0;
				foreach($all_row_key_arr as $row_key){
					list($source_id,$uom_id) = explode('**',$row_filed_data_arr[$seq]);


					foreach($dayArr as $key => $plan_date){
						$targetQty = ${'targetQty_'.$row_key.'_'.$key};
						//echo $targetQty.'==';
						if ($data_array != "") $data_array .= ",";
						$data_array .= "(".$id.",".$tna_mst_id.",".$task_id.",'1',".$po_id.",".$source_id.",".$uom_id.",'".str_replace("'","",$targetQty)."','".$plan_date."','1','0',".$_SESSION['logic_erp']['user_id'].",'" . $pc_date_time . "')";
						$id++;
					}
					$seq++;
				}
			}

			else if($task_id == 70 || $task_id == 71){
				$field_array="ID, TNA_MST_ID,TASK_ID,TASK_TYPE, PO_ID, TYPE_ID, ITEM_ID,PLAN_QTY,PLAN_DATE, STATUS_ACTIVE,IS_DELETED,INSERTED_BY,INSERT_DATE";
				$all_row_key_arr = explode('**',$all_row_key);
				$row_filed_data_arr = explode('__',$row_filed_data_str);

				$seq=0;
				foreach($all_row_key_arr as $row_key){
					list($type_id,$item_id) = explode('**',$row_filed_data_arr[$seq]);


					foreach($dayArr as $key => $plan_date){
						$targetQty = ${'targetQty_'.$row_key.'_'.$key};
						//echo $targetQty.'==';
						if ($data_array != "") $data_array .= ",";
						$data_array .= "(".$id.",".$tna_mst_id.",".$task_id.",'1',".$po_id.",".$type_id.",".$item_id.",'".str_replace("'","",$targetQty)."','".$plan_date."','1','0',".$_SESSION['logic_erp']['user_id'].",'" . $pc_date_time . "')";
						$id++;
					}
					$seq++;
				}
			}

			else{
				$field_array="ID, TNA_MST_ID,TASK_ID,TASK_TYPE, PO_ID,PLAN_QTY,PLAN_DATE, STATUS_ACTIVE,IS_DELETED,INSERTED_BY,INSERT_DATE";
				$all_row_key_arr = explode('**',$all_row_key);
				foreach($all_row_key_arr as $row_key){
					foreach($dayArr as $key => $plan_date){
						$targetQty = ${'targetQty_'.$row_key.'_'.$key};
						//echo $targetQty.'==';
						if ($data_array != "") $data_array .= ",";
						$data_array .= "(".$id.",".$tna_mst_id.",".$task_id.",'1',".$po_id.",'".str_replace("'","",$targetQty)."','".$plan_date."','1','0',".$_SESSION['logic_erp']['user_id'].",'" . $pc_date_time . "')";
						$id++;
					}
				}
			}
			


		 // echo "insert into TNA_PLAN_TARGET ($field_array) values $data_array ";die;

			$rID=sql_insert("TNA_PLAN_TARGET",$field_array,$data_array,1);
				
		}
		else if ($operation==1)  
		{		
			$con = connect();

				$rID=execute_query("delete from TNA_PLAN_TARGET where TNA_MST_ID=$tna_mst_id and PO_ID=$po_id and TASK_ID=$task_id and task_type=1");

				$id=return_next_id( "id", "TNA_PLAN_TARGET", 1 ) ;

				

			if($task_id == 48){
				$field_array="ID, TNA_MST_ID,TASK_ID,TASK_TYPE, PO_ID, COUNT_ID, COMPOSITION_ID,PLAN_QTY,PLAN_DATE, STATUS_ACTIVE,IS_DELETED,INSERTED_BY,INSERT_DATE";
				$all_row_key_arr = explode('**',$all_row_key);
				$row_filed_data_arr = explode('__',$row_filed_data_str);

				$seq=0;
				foreach($all_row_key_arr as $row_key){
				list($count_id,$compostion_id) = explode('**',$row_filed_data_arr[$seq]);

					foreach($dayArr as $key => $plan_date){
						$targetQty = ${'targetQty_'.$row_key.'_'.$key};
						//echo $targetQty.'==';
						if ($data_array != "") $data_array .= ",";
						$data_array .= "(".$id.",".$tna_mst_id.",".$task_id.",'1',".$po_id.",".$count_id.",".$compostion_id.",'".str_replace("'","",$targetQty)."','".$plan_date."','1','0',".$_SESSION['logic_erp']['user_id'].",'" . $pc_date_time . "')";
						$id++;
					}
					$seq++;
				}
			}
			else if($task_id == 52){
				$field_array="ID, TNA_MST_ID,TASK_ID,TASK_TYPE, PO_ID, COLOUR_ID,PLAN_QTY,PLAN_DATE, STATUS_ACTIVE,IS_DELETED,INSERTED_BY,INSERT_DATE";
				$all_row_key_arr = explode('**',$all_row_key);
				$row_filed_data_arr = explode('__',$row_filed_data_str);

				$seq=0;
				foreach($all_row_key_arr as $row_key){
				list($colour_id) = explode('**',$row_filed_data_arr[$seq]);

					foreach($dayArr as $key => $plan_date){
						$targetQty = ${'targetQty_'.$row_key.'_'.$key};
						//echo $targetQty.'==';
						if ($data_array != "") $data_array .= ",";
						$data_array .= "(".$id.",".$tna_mst_id.",".$task_id.",'1',".$po_id.",".$colour_id.",'".str_replace("'","",$targetQty)."','".$plan_date."','1','0',".$_SESSION['logic_erp']['user_id'].",'" . $pc_date_time . "')";
						$id++;
					}
					$seq++;
				}
			}
			else if($task_id == 60){
				$field_array="ID, TNA_MST_ID,TASK_ID,TASK_TYPE, PO_ID, YARN_COUNT_DETER_ID,PLAN_QTY,PLAN_DATE, STATUS_ACTIVE,IS_DELETED,INSERTED_BY,INSERT_DATE";
				$all_row_key_arr = explode('**',$all_row_key);
				$row_filed_data_arr = explode('__',$row_filed_data_str);

				$seq=0;
				foreach($all_row_key_arr as $row_key){
				list($yarn_count_deter_id) = explode('**',$row_filed_data_arr[$seq]);

					foreach($dayArr as $key => $plan_date){
						$targetQty = ${'targetQty_'.$row_key.'_'.$key};
						//echo $targetQty.'==';
						if ($data_array != "") $data_array .= ",";
						$data_array .= "(".$id.",".$tna_mst_id.",".$task_id.",'1',".$po_id.",".$yarn_count_deter_id.",'".str_replace("'","",$targetQty)."','".$plan_date."','1','0',".$_SESSION['logic_erp']['user_id'].",'" . $pc_date_time . "')";
						$id++;
					}
					$seq++;
				}
			}
			
			else if($task_id == 73){
				$field_array="ID, TNA_MST_ID,TASK_ID,TASK_TYPE, PO_ID, SOURCE_ID, UOM_ID,PLAN_QTY,PLAN_DATE, STATUS_ACTIVE,IS_DELETED,INSERTED_BY,INSERT_DATE";
				$all_row_key_arr = explode('**',$all_row_key);
				$row_filed_data_arr = explode('__',$row_filed_data_str);

				$seq=0;
				foreach($all_row_key_arr as $row_key){
				list($source_id,$uom_id) = explode('**',$row_filed_data_arr[$seq]);

					foreach($dayArr as $key => $plan_date){
						$targetQty = ${'targetQty_'.$row_key.'_'.$key};
						//echo $targetQty.'==';
						if ($data_array != "") $data_array .= ",";
						$data_array .= "(".$id.",".$tna_mst_id.",".$task_id.",'1',".$po_id.",".$source_id.",".$uom_id.",'".str_replace("'","",$targetQty)."','".$plan_date."','1','0',".$_SESSION['logic_erp']['user_id'].",'" . $pc_date_time . "')";
						$id++;
					}
					$seq++;
				}
			}
			else if($task_id == 70 || $task_id == 71){
				$field_array="ID, TNA_MST_ID,TASK_ID,TASK_TYPE, PO_ID, TYPE_ID, ITEM_ID,PLAN_QTY,PLAN_DATE, STATUS_ACTIVE,IS_DELETED,INSERTED_BY,INSERT_DATE";
				$all_row_key_arr = explode('**',$all_row_key);
				$row_filed_data_arr = explode('__',$row_filed_data_str);

				$seq=0;
				foreach($all_row_key_arr as $row_key){
				list($type_id,$item_id) = explode('**',$row_filed_data_arr[$seq]);

					foreach($dayArr as $key => $plan_date){
						$targetQty = ${'targetQty_'.$row_key.'_'.$key};
						//echo $targetQty.'==';
						if ($data_array != "") $data_array .= ",";
						$data_array .= "(".$id.",".$tna_mst_id.",".$task_id.",'1',".$po_id.",".$type_id.",".$item_id.",'".str_replace("'","",$targetQty)."','".$plan_date."','1','0',".$_SESSION['logic_erp']['user_id'].",'" . $pc_date_time . "')";
						$id++;
					}
					$seq++;
				}
			}
			else{
				$field_array="ID, TNA_MST_ID,TASK_ID,TASK_TYPE, PO_ID,PLAN_QTY,PLAN_DATE, STATUS_ACTIVE,IS_DELETED,INSERTED_BY,INSERT_DATE";
				$all_row_key_arr = explode('**',$all_row_key);
				foreach($all_row_key_arr as $row_key){
					foreach($dayArr as $key => $plan_date){
						$targetQty = ${'targetQty_'.$row_key.'_'.$key};
						if ($data_array != "") $data_array .= ",";
						$data_array .= "(".$id.",".$tna_mst_id.",".$task_id.",'1',".$po_id.",'".str_replace("'","",$targetQty)."','".$plan_date."','1','0',".$_SESSION['logic_erp']['user_id'].",'" . $pc_date_time . "')";
						$id++;
					}
				}
			}

				//echo "insert into TNA_PLAN_TARGET ($field_array) values $data_array ";die;

				$rID=sql_insert("TNA_PLAN_TARGET",$field_array,$data_array,1);
					
				
			}
			
		
			if($rID)
			{
				  oci_commit($con);
				  echo "1**";
				  
			}
			else
			{
				  oci_rollback($con);
				  echo "10**";
			}
		
		
		disconnect($con);
		die;
	
	 
}

if($action=="edit_update_tna_first_ship")
{
	
	echo load_html_head_contents("TNA","../../../", 1, 1, $unicode);
	extract($_REQUEST);
		
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		
		$sql ="select a.po_number,b.job_no,b.buyer_name,b.style_ref_no from  wo_po_break_down a,wo_po_details_master b where a.id in($po_id) and a.job_no_mst=b.job_no";
		$result=sql_select($sql);
		
		$tna= "select APPROVED,READY_TO_APPROVED,template_id,task_number,task_start_date,task_finish_date,actual_start_date,actual_finish_date,plan_start_flag,plan_finish_flag,commit_start_date,commit_end_date from  tna_process_mst where id in($mid)";
		//echo $tna;die;
		$tna_result=sql_select($tna);
		
		$mod_sql= sql_select("select id,task_catagory,task_name,task_short_name,task_type,completion_percent from lib_tna_task where is_deleted = 0 and status_active=1  AND  task_type = 1 order by task_sequence_no asc");
		$tna_task_array=array();
		foreach ($mod_sql as $row)
		{	
			$tna_task_array[$row[csf("task_name")]] = $row[csf("task_short_name")];
		}
		
		//History data start------------------------
		$tna_history_sql= "select id, template_id,task_number, job_no, po_number_id, task_start_date, task_finish_date, actual_start_date, actual_finish_date,plan_start_flag,plan_finish_flag,commit_start_date,commit_end_date from  tna_plan_actual_history where  is_deleted = 0 and status_active=1 and template_id=".$tna_result[0][csf('template_id')]." and task_number=".$tna_result[0][csf('task_number')]." and po_number_id in($po_id) and job_no='".$result[0][csf('job_no')]."' order by id asc";
		 //echo $tna_history_sql;die;
		$tna_history=sql_select($tna_history_sql);
		//History data end------------------------
		
		//var_dump($tna_history);
	
		if($tna_history[0][csf('task_start_date')]==""){$ts_history_con=0;}
		else if($tna_history[0][csf('task_start_date')]=="0000-00-00"){$ts_history_con=0;}else{$ts_history_con=1;}
		if($tna_result[0][csf('task_start_date')]==""){$ts_result_con=0;}
		else if($tna_result[0][csf('task_start_date')]=="0000-00-00"){$ts_result_con=0;}else{$ts_result_con=1;}


		if($tna_history[0][csf('task_finish_date')]==""){$tf_history_con=0;}
		else if($tna_history[0][csf('task_finish_date')]=="0000-00-00"){$tf_history_con=0;}else{$tf_history_con=1;}
		if($tna_result[0][csf('task_finish_date')]==""){$tf_result_con=0;}
		else if($tna_result[0][csf('task_finish_date')]=="0000-00-00"){$tf_result_con=0;}else{$tf_result_con=1;}


		if($tna_history[0][csf('actual_start_date')]==""){$as_history_con=0;}
		else if($tna_history[0][csf('actual_start_date')]=="0000-00-00"){$as_history_con=0;}else{$as_history_con=1;}
		if($tna_result[0][csf('actual_start_date')]==""){$as_result_con=0;}
		else if($tna_result[0][csf('actual_start_date')]=="0000-00-00"){$as_result_con=0;}else{$as_result_con=1;}

		if($tna_history[0][csf('actual_finish_date')]==""){$af_history_con=0;}
		else if($tna_history[0][csf('actual_finish_date')]=="0000-00-00"){$af_history_con=0;}else{$af_history_con=1;}
		if($tna_result[0][csf('actual_finish_date')]==""){$af_result_con=0;}
		else if($tna_result[0][csf('actual_finish_date')]=="0000-00-00"){$af_result_con=0;}else{$af_result_con=1;}

		
		if($tna_history[0][csf('commit_start_date')]==""){$cs_history_con=0;}
		else if($tna_history[0][csf('commit_start_date')]=="0000-00-00"){$cs_history_con=0;}else{$cs_history_con=1;}
		if($tna_result[0][csf('commit_start_date')]==""){$af_result_con=0;}
		else if($tna_result[0][csf('commit_start_date')]=="0000-00-00"){$af_result_con=0;}else{$af_result_con=1;}
		
		if($tna_history[0][csf('commit_end_date')]==""){$ce_history_con=0;}
		else if($tna_history[0][csf('commit_end_date')]=="0000-00-00"){$ce_history_con=0;}else{$ce_history_con=1;}
		if($tna_result[0][csf('commit_end_date')]==""){$ce_result_con=0;}
		else if($tna_result[0][csf('commit_end_date')]=="0000-00-00"){$ce_result_con=0;}else{$ce_result_con=1;}
	
	
	?> 
    
    
     <script>
	 
	 
	 var permission='<? echo $permission; ?>';
	function fnc_tna_actual_date_update_first_ship( operation )
	{
		var start_date='<? echo change_date_format($tna_result[0][csf('task_start_date')]);?>';
		var curr_start_date=$('#txt_plan_start_date').val();
		var history_start_date='<? echo change_date_format($tna_history[0][csf('task_start_date')]);?>';
		
		var finish_date='<? echo change_date_format($tna_result[0][csf('task_finish_date')]);?>';
		var curr_finish_date=$('#txt_plan_finish_date').val();
		var history_finish_date='<? echo change_date_format($tna_history[0][csf('task_finish_date')]);?>';

		var start_flag=0;var finish_flag=0;
		if(start_date!=curr_start_date){start_flag=1;}
		if((history_start_date!=curr_start_date) && history_start_date){ start_flag=1;}
		
		if(finish_date!=curr_finish_date){finish_flag=1;}
		if((history_finish_date!=curr_finish_date) && history_finish_date){ finish_flag=1;}
		 
		var data="action=save_update_delete_first_ship&operation="+operation+'&start_flag='+start_flag+'&finish_flag='+finish_flag+get_submitted_data_string('txt_actual_start_date*txt_actual_finish_date*txt_update_tna_id*txt_plan_start_date*txt_plan_finish_date*txt_update_tna_type*txt_plan_actual_history*txt_commitment_start_date*txt_commitment_end_date',"../../../");
		 //alert (data);return;
		freeze_window(operation);
		http.open("POST","tna_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = save_update_delete_first_ship_response;
	}


		function save_update_delete_first_ship_response()
		{
			if(http.readyState == 4) 
			{	
				//alert(http.responseText);return;
				var reponse=trim(http.responseText).split('**');
				show_msg(trim(reponse[0]));
				
				if(reponse[0]==1)
				{
					$('#auto_field_data_str').val(http.responseText);
					parent.emailwindow.hide();
				}
				else
				{
					alert('Invalid Operation');
				}
				
				//alert (reponse[0]);
				
				
				//document.getElementById('report_container').innerHTML  = reponse[1];
				set_button_status(1, permission, 'fnc_tna_actual_date_update_first_ship',1);
				release_freezing();
				
			}
		}
			</script>
			
			</head>
			<body onLoad="set_hotkey()">
		
			<div align="center" style="width:100%">
			<? 
				echo load_freeze_divs ("../../../",$permission,1);
			?>
			<form>
				<input type="hidden" id="auto_field_data_str" name="auto_field_data_str"  value="" />
			</form>
			
			<table><tr><td><font size="+1"><b><? echo $tna_task_array[$tna_result[0][csf('task_number')]]; ?></b></font></td></tr></table>  
			<table width="600" cellspacing="0" cellpadding="0" class="rpt_table">
				<thead>
					<th width="100">Buyer Name</th>
					<th width="100">Job No</th>
					<th width="120">Style Ref No</th>
					<th width="120">PO Number</th>
				</thead>
				<tr>
					<td><? echo $buyer_arr[$result[0][csf('buyer_name')]]; ?></td>
					<td> <? echo $result[0][csf('job_no')]; ?></td>
					<td><? echo $result[0][csf('style_ref_no')]; ?></td>
					<td><? echo  $result[0][csf('po_number')]; ?></td>
					
				</tr>
				<tr>
					<td colspan="4" height="15"></td>
				</tr>
				<tr>
					<td align="right">Plan Start Date</td>
					<td>
						<input type="text" <? if($type==2 ||  $type==3) echo "disabled='disabled'";  ?> name="txt_plan_start_date" id="txt_plan_start_date" class="datepicker" style="width:100px" value="<? if($ts_history_con==1){echo change_date_format($tna_history[0][csf('task_start_date')]);} else if($ts_result_con==0){echo "";}else{ echo change_date_format($tna_result[0][csf('task_start_date')]);} ?>" />
					</td>
					
					<td align="right">Plan Finish Date</td>
					<td>
						<input type="text" <? if($type==2 ||  $type==3) echo "disabled='disabled'";  ?> name="txt_plan_finish_date" id="txt_plan_finish_date" class="datepicker" style="width:100px"  value="<? if($tf_history_con==1){echo change_date_format($tna_history[0][csf('task_finish_date')]);} else if($tf_result_con==0){echo "";}else echo change_date_format($tna_result[0][csf('task_finish_date')]); ?>"/>
					</td>
				</tr>
				
				<tr>
					<td align="right">Actual Start Date </td>
					<td>
						<input type="text" <? if($type==1 ||  $type==3) echo "disabled='disabled'";  ?> name="txt_actual_start_date" id="txt_actual_start_date" class="datepicker" style="width:100px" value="<?  if($as_history_con==1){echo change_date_format($tna_history[0][csf('actual_start_date')]);} else if($as_result_con==0){echo "";}else echo change_date_format($tna_result[0][csf('actual_start_date')]); ?>" />
					</td>
					<td align="right">Actual Finish Date</td>
					<td>
						<input type="text" <? if($type==1 ||  $type==3) echo "disabled='disabled'";  ?> name="txt_actual_finish_date" id="txt_actual_finish_date" class="datepicker" style="width:100px" value="<?   if($af_history_con==1){echo change_date_format($tna_history[0][csf('actual_finish_date')]);} else if($af_result_con==0){echo "";}else echo change_date_format($tna_result[0][csf('actual_finish_date')]); ?>" />
					</td>
				</tr>
				
				<tr>
					<td align="right">Commitment Start Date</td>
					<td>
						<input type="text" name="txt_commitment_start_date" id="txt_commitment_start_date" class="datepicker" style="width:100px" value="<?  if($cs_history_con==1){echo change_date_format($tna_history[0][csf('commit_start_date')]);} else if($cs_history_con==0){echo "";}else echo change_date_format($tna_result[0][csf('commit_start_date')]); ?>" />
					</td>
					<td align="right">Commitment End Date</td>
					<td>
						<input type="text" name="txt_commitment_end_date" id="txt_commitment_end_date" class="datepicker" style="width:100px" value="<? if($ce_history_con==1){echo change_date_format($tna_history[0][csf('commit_end_date')]);} else if($ce_history_con==0){echo "";}else{echo change_date_format($tna_result[0][csf('commit_end_date')]);} ?>" />
					</td>
				</tr>
				
				<tr>
					<td colspan="4" height="50" valign="middle" align="center" class="button_container">
					<input type="hidden" id="txt_plan_actual_history" name="txt_plan_actual_history"  value="<? echo $tna_history[0][csf('id')].'_'.$tna_result[0][csf('template_id')].'_'.$tna_result[0][csf('task_number')].'_'.$po_id.'_'.$result[0][csf('job_no')]; ?>" />
					<input type="hidden" id="txt_update_tna_id" name="txt_update_tna_id"  value="<? echo $mid; ?>" />
					<input type="hidden" id="txt_update_tna_type" name="txt_update_tna_type"  value="<? echo $type; ?>" />
					<? echo load_submit_buttons( $permission, "fnc_tna_actual_date_update_first_ship", 1,0 ,"",2) ; ?> 
					</td>
				</tr>
				
			</table>
			</div>
		</body>           
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
		</html>
			<?
		die;

}

if($action=="save_update_delete")
{ 
    
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$txt_plan_actual_history = str_replace("'","",$txt_plan_actual_history);
	$update_tna_type=str_replace("'",'',$txt_update_tna_type);
	
	if ($operation==1)  
	{		
		$con = connect();

		$id=str_replace("'",'',$txt_update_tna_id);

		//echo $id;die;
		if($update_tna_type==1)
		{
			$field='';$data='';
			if($start_flag==1){$field="*plan_start_flag";$data="*1";}
			if($finish_flag==1){$field.="*plan_finish_flag";$data.="*1";}
			
			
			$field_array1="task_type*task_start_date*task_finish_date".$field;
			$data_array1="1*".$txt_plan_start_date."*".$txt_plan_finish_date.$data."";
			
			foreach(explode(',',$id) as $nid){
				$rID=sql_update("tna_process_mst",$field_array1,$data_array1,"id",$nid,1);
			}
			
			//history process  start-----------------------------------------;
			list($hmid,$htmp_id,$htask_id,$hpo_id,$hjob_id)=explode("_",$txt_plan_actual_history);
			if(str_replace("'","",$hmid))
			{
				$rID=sql_update("tna_plan_actual_history",$field_array1."*update_by*update_date",$data_array1."*".$user_id."*'".$pc_date_time."'","id",str_replace("'","",$hmid),1);
				//echo $rID;die;
			}
			else
			{
				$hid=return_next_id( "id", "tna_plan_actual_history", 1 ) ;
				$field_array="id, template_id,task_number, job_no, po_number_id, task_start_date, task_finish_date, plan_start_flag,plan_finish_flag,status_active,is_deleted,task_type,update_by,update_date";
				$data_array="(".$hid.",".$htmp_id.",".$htask_id.",'".str_replace("'","",$hjob_id)."',".$hpo_id.",".$txt_plan_start_date.",".$txt_plan_finish_date.",".$start_flag.",".$finish_flag.",'1','0',1,".$user_id.",'".$pc_date_time."')";
				
				$rID2=sql_insert("tna_plan_actual_history",$field_array,$data_array,1);
				
			}
			
			//echo $rID.'='.$rID2;die;
			
			// history process end-----------------------------------------;
		
			
		}
		else if($update_tna_type==3)
		{
			
			$field_array1="commit_start_date*commit_end_date";
			$data_array1="".$txt_commitment_start_date."*".$txt_commitment_end_date."";
			
			foreach(explode(',',$id) as $nid){
				$rID=sql_update("tna_process_mst",$field_array1,$data_array1,"id",$nid,1);
			}
			
			// history process start-----------------------------------------;
			list($hmid,$htmp_id,$htask_id,$hpo_id,$hjob_id)=explode("_",$txt_plan_actual_history);
			if(str_replace("'","",$hmid))
			{
				$rID=sql_update("tna_plan_actual_history",$field_array1."*update_by*update_date",$data_array1."*".$user_id."*'".$pc_date_time."'","id",str_replace("'","",$hmid),1);
			}
			else
			{
				$hid=return_next_id( "id", "tna_plan_actual_history", 1 ) ;
				$field_array="id, template_id,task_number, job_no, po_number_id, task_start_date, task_finish_date,commit_start_date,commit_end_date, plan_start_flag,plan_finish_flag,status_active,is_deleted,task_type,update_by,update_date";
				$data_array="(".$hid.",".$htmp_id.",".$htask_id.",'".str_replace("'","",$hjob_id)."',".$hpo_id.",".$txt_plan_start_date.",".$txt_plan_finish_date.",".$txt_commitment_start_date.",".$txt_commitment_end_date.",".$start_flag.",".$finish_flag.",'1','0',1,".$user_id.",'".$pc_date_time."')";
				$rID=sql_insert("tna_plan_actual_history",$field_array,$data_array,1);
			}
			//history process end-----------------------------------------;
		}
		else
		{
				
			$sql2 ="SELECT actual_start_date,actual_finish_date,nvl(actual_start_flag,0) as actual_start_flag,nvl(actual_finish_flag,0) as actual_finish_flag FROM tna_process_mst where id in($id)";
			
			
			$result2=sql_select($sql2);
			foreach($result2 as $row2)
			{
				$actual_start=$row2[csf("actual_start_date")];
				$actual_finish=$row2[csf("actual_finish_date")];
				$actual_start_flag=$row2[csf("actual_start_flag")];
				$actual_finish_flag=$row2[csf("actual_finish_flag")];
			}
			
			
			if(change_date_format($actual_start)!=change_date_format(str_replace("'",'',$txt_actual_start_date))){ $start=1; } else { $start=$actual_start_flag; } 
			if(change_date_format($actual_finish)!=change_date_format(str_replace("'",'',$txt_actual_finish_date))){ $finish=1; } else { $finish=$actual_finish_flag; }
			
			$field_array="actual_start_date*actual_finish_date*actual_start_flag*actual_finish_flag";
			$data_array="".$txt_actual_start_date."*".$txt_actual_finish_date."*".$start."*".$finish."";
			
			foreach(explode(',',$id) as $nid){
				$rID=sql_update("tna_process_mst",$field_array,$data_array,"id",$nid,1);
			}
			
			//history process  start-----------------------------------------;
			list($hmid,$htmp_id,$htask_id,$hpo_id,$hjob_id)=explode("_",$txt_plan_actual_history);
			if(str_replace("'","",$hmid))
			{
				$hfield_array="task_type*actual_start_date*actual_finish_date*update_by*update_date";
				$hdata_array="1*".$txt_actual_start_date."*".$txt_actual_finish_date."*".$user_id."*'".$pc_date_time."'";
				$rID=sql_update("tna_plan_actual_history",$hfield_array,$hdata_array,"id",str_replace("'","",$hmid),1);
			}
			else
			{
				$hid=return_next_id( "id", "tna_plan_actual_history", 1 ) ;
				$field_array="id, template_id,task_number,job_no, po_number_id, actual_start_date, actual_finish_date, status_active, is_deleted,task_type,update_by,update_date";
				$data_array="(".$hid.",".$htmp_id.",".$htask_id.",'".str_replace("'","",$hjob_id)."',".$hpo_id.",".$txt_actual_start_date.",".$txt_actual_finish_date.",'1','0',1,".$user_id.",'".$pc_date_time."')";
				$rID=sql_insert("tna_plan_actual_history",$field_array,$data_array,1);
			
			}
		}

		if($rID)
		{
			oci_commit($con);
			echo "1**".str_replace("'", '', $id)."**".str_replace("'",'',$txt_update_tna_type)."**".$htask_id."**".$hpo_id."**".change_date_format(str_replace("'",'',$txt_actual_start_date))."**".change_date_format(str_replace("'",'',$txt_actual_finish_date))."**".change_date_format(str_replace("'",'',$txt_plan_start_date))."**".change_date_format(str_replace("'",'',$txt_plan_finish_date));	
		}
		else
		{
			oci_rollback($con);
			echo "10**";
		}
		disconnect($con);
		die;
	}
}


if($action=="save_update_delete_first_ship")
{
		
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$update_tna_type=str_replace("'",'',$txt_update_tna_type);
	$txt_update_tna_id=str_replace("'",'',$txt_update_tna_id);
	$txt_plan_actual_history = str_replace("'","",$txt_plan_actual_history);


	
	if ($operation==1)  
	{		
		$con = connect();
		 
		$id=str_replace("'",'',$txt_update_tna_id);
		
		if($update_tna_type==1)
		{
			list($hmid,$htmp_id,$htask_id,$hpo_id_str,$hjob_id)=explode("_",$txt_plan_actual_history);
			foreach(explode(',',$hpo_id_str) as $hpo_id){
				$txt_plan_actual_history_arr[] = $hmid."_".$htmp_id."_".$htask_id."_".$hpo_id."_".$hjob_id;
			}
			$flag=1;
			$index=0;
			foreach(explode(',',$txt_update_tna_id) as $id)
			{
				$field='';$data='';
				if($start_flag==1){$field="*plan_start_flag";$data="*1";}
				if($finish_flag==1){$field.="*plan_finish_flag";$data.="*1";}
				
				
				$field_array1="task_type*task_start_date*task_finish_date".$field;
				$data_array1="1*".$txt_plan_start_date."*".$txt_plan_finish_date.$data."";
				
				$rID=sql_update("tna_process_mst",$field_array1,$data_array1,"id",$id,1);
				if($flag==1 && $rID){$flag=1;}else{$flag=0;}
				
				//history process  start-----------------------------------------;
				list($hmid,$htmp_id,$htask_id,$hpo_id,$hjob_id)=explode("_",$txt_plan_actual_history_arr[$index]);
				if(str_replace("'","",$hmid))
				{
					$rID=sql_update("tna_plan_actual_history",$field_array1,$data_array1,"id",str_replace("'","",$hmid),1);
					if($flag==1 && $rID){$flag=1;}else{$flag=0;}
				}
				else
				{
					$hid=return_next_id( "id", "tna_plan_actual_history", 1 ) ;
					$field_array="id, template_id,task_number, job_no, po_number_id, task_start_date, task_finish_date, plan_start_flag,plan_finish_flag,status_active,is_deleted,task_type";
					$data_array="(".$hid.",".$htmp_id.",".$htask_id.",'".str_replace("'","",$hjob_id)."',".$hpo_id.",".$txt_plan_start_date.",".$txt_plan_finish_date.",".$start_flag.",".$finish_flag.",'1','0',1)";
					$rID=sql_insert("tna_plan_actual_history",$field_array,$data_array,1);
					if($flag==1 && $rID){$flag=1;}else{$flag=0;}
					
				}
				//history process end-----------------------------------------;
				$index++;
			}//end foreach;
			
		}
		else if($update_tna_type==3)
		{
			
			list($hmid,$htmp_id,$htask_id,$hpo_id_str,$hjob_id)=explode("_",$txt_plan_actual_history);
			foreach(explode(',',$hpo_id_str) as $hpo_id){
				$txt_plan_actual_history_arr[] = $hmid."_".$htmp_id."_".$htask_id."_".$hpo_id."_".$hjob_id;
			}			
			$flag=1;
			$index=0;
			foreach(explode(',',$txt_update_tna_id) as $id)
			{
			
				$field_array1="commit_start_date*commit_end_date";
				$data_array1="".$txt_commitment_start_date."*".$txt_commitment_end_date."";
				
				$rID=sql_update("tna_process_mst",$field_array1,$data_array1,"id",$id,1);
				if($flag==1 && $rID){$flag=1;}else{$flag=0;}
				
				//history process  start-----------------------------------------;
				list($hmid,$htmp_id,$htask_id,$hpo_id,$hjob_id)=explode("_",$txt_plan_actual_history_arr[$index]);
				if(str_replace("'","",$hmid))
				{
					$rID=sql_update("tna_plan_actual_history",$field_array1,$data_array1,"id",str_replace("'","",$hmid),1);
					if($flag==1 && $rID){$flag=1;}else{$flag=0;}
				}
				else
				{
					$hid=return_next_id( "id", "tna_plan_actual_history", 1 ) ;
					$field_array="id, template_id,task_number, job_no, po_number_id, task_start_date, task_finish_date,commit_start_date,commit_end_date, plan_start_flag,plan_finish_flag,status_active,is_deleted,task_type";
					$data_array="(".$hid.",".$htmp_id.",".$htask_id.",'".str_replace("'","",$hjob_id)."',".$hpo_id.",".$txt_plan_start_date.",".$txt_plan_finish_date.",".$txt_commitment_start_date.",".$txt_commitment_end_date.",".$start_flag.",".$finish_flag.",'1','0',1)";
					$rID=sql_insert("tna_plan_actual_history",$field_array,$data_array,1);
					if($flag==1 && $rID){$flag=1;}else{$flag=0;}
					
				}
			
				$index++;
			//history process end-----------------------------------------;
			}//end foreach;
			
		}
		else   
		{	
			
			list($hmid,$htmp_id,$htask_id,$hpo_id_str,$hjob_id)=explode("_",$txt_plan_actual_history);
			foreach(explode(',',$hpo_id_str) as $hpo_id){
				$txt_plan_actual_history_arr[] = $hmid."_".$htmp_id."_".$htask_id."_".$hpo_id."_".$hjob_id;
			}
			
			$flag=1;
			$index=0;
			foreach(explode(',',$txt_update_tna_id) as $id)
			{
			
				if($db_type==0)
				{
					$sql2 ="SELECT actual_start_date,actual_finish_date,actual_start_flag,actual_finish_flag FROM tna_process_mst where id=$id";
				}
				if($db_type==2 || $db_type==1)
				{	
					$sql2 ="SELECT actual_start_date,actual_finish_date,nvl(actual_start_flag,0) as actual_start_flag,nvl(actual_finish_flag,0) as actual_finish_flag FROM tna_process_mst where id=$id";
				}
				
				$result2=sql_select($sql2);
				foreach($result2 as $row2)
				{
					$actual_start=$row2[csf("actual_start_date")];
					$actual_finish=$row2[csf("actual_finish_date")];
					$actual_start_flag=$row2[csf("actual_start_flag")];
					$actual_finish_flag=$row2[csf("actual_finish_flag")];
				}
				
				
				if(change_date_format($actual_start)!=change_date_format(str_replace("'",'',$txt_actual_start_date))){ $start=1; } else { $start=$actual_start_flag; } 
				if(change_date_format($actual_finish)!=change_date_format(str_replace("'",'',$txt_actual_finish_date))){ $finish=1; } else { $finish=$actual_finish_flag; }	
				
				$field_array="actual_start_date*actual_finish_date*actual_start_flag*actual_finish_flag";
				$data_array="".$txt_actual_start_date."*".$txt_actual_finish_date."*".$start."*".$finish."";
				
				$rID=sql_update("tna_process_mst",$field_array,$data_array,"id",$id,1);
				if($flag==1 && $rID){$flag=1;}else{$flag=0;}

 
				//history process  start-----------------------------------------;
				
				list($hmid,$htmp_id,$htask_id,$hpo_id,$hjob_id)=explode("_",$txt_plan_actual_history_arr[$index]);
				if(str_replace("'","",$hmid))
				{
					$hfield_array="task_type*actual_start_date*actual_finish_date";
					$hdata_array="1*".$txt_actual_start_date."*".$txt_actual_finish_date."";
					$rID=sql_update("tna_plan_actual_history",$hfield_array,$hdata_array,"id",str_replace("'","",$hmid),1);
					if($flag==1 && $rID){$flag=1;}else{$flag=0;}
				}
				else
				{
					$hid=return_next_id( "id", "tna_plan_actual_history", 1 ) ;
					$field_array="id, template_id,task_number,job_no, po_number_id, actual_start_date, actual_finish_date, status_active, is_deleted,task_type";
					$data_array="(".$hid.",".$htmp_id.",".$htask_id.",'".str_replace("'","",$hjob_id)."',".$hpo_id.",".$txt_actual_start_date.",".$txt_actual_finish_date.",'1','0',1)";
					$rID=sql_insert("tna_plan_actual_history",$field_array,$data_array,1);
					if($flag==1 && $rID){$flag=1;}else{$flag=0;}
					//echo "10**$hjob_id";oci_rollback($con);die;

				}
				//history process end-----------------------------------------;
				
				$index++;
				
			
			}//end foreach;

			
		}
		
		//

		
		if($flag==1)
		{
				oci_commit($con);
				echo "1**".str_replace("'", '', $id)."**".str_replace("'",'',$txt_update_tna_type)."**".$htask_id."**".$hpo_id."**".str_replace("'",'',$txt_actual_start_date)."**".str_replace("'",'',$txt_actual_finish_date)."**".str_replace("'",'',$txt_plan_start_date)."**".str_replace("'",'',$txt_plan_finish_date);
				
		}
		else
		{
				oci_rollback($con);
				echo "10**";
		}
		
		
		disconnect($con);
		die;
	}
}


if($action=="update_tna_progress_comment")
{
	 
	if($db_type==0) $blank_date="0000-00-00"; else $blank_date=""; 	
	
	echo load_html_head_contents("TNA","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$tna_task_arr=return_library_array( "select task_name, task_short_name from lib_tna_task where task_type = 1",'task_name','task_short_name');
	$lead_time_array=return_library_array("select task_template_id,lead_time from tna_task_template_details where task_type=1 and is_deleted=0 and status_active=1 group by lead_time,task_template_id","task_template_id",'lead_time');

	$sql ="select b.company_name,b.buyer_name,a.po_number,b.job_no,b.style_ref_no,b.gmts_item_id,a.po_received_date,a.shipment_date,(po_quantity*total_set_qnty) as po_qty_pcs, set_smv from  wo_po_break_down a,wo_po_details_master b where a.id=$po_id and a.job_no_mst=b.job_no";
	$result=sql_select($sql);
	
	
	$tna_task_id=array();
	$plan_start_array=array();
	$plan_finish_array=array();
	$actual_start_array=array();
	$actual_finish_array=array();
	$notice_start_array=array();
	$notice_finish_array=array();
	
	$task_sql=sql_select("SELECT a.task_number, a.task_start_date, a.task_finish_date, a.actual_start_date, a.actual_finish_date, a.notice_date_start, a.notice_date_end, b.task_group FROM tna_process_mst a, lib_tna_task b WHERE a.task_type = 1 AND b.task_type = 1 AND a.task_number = b.task_name AND a.po_number_id = $po_id AND b.status_active = 1 AND b.is_deleted = 0 ORDER BY b.task_sequence_no ASC");
    
	 
	foreach ($task_sql as $row_task)
	{
		$tna_task_id[$row_task[csf("task_number")]]=$row_task[csf("task_number")];
		
		$plan_start_array[$row_task[csf("task_number")]] = $row_task[csf("task_start_date")];
		$plan_finish_array[$row_task[csf("task_number")]] = $row_task[csf("task_finish_date")];
		
		$actual_start_array[$row_task[csf("task_number")]] = $row_task[csf("actual_start_date")];
		$actual_finish_array[$row_task[csf("task_number")]]= $row_task[csf("actual_finish_date")];
		
		$notice_start_array[$row_task[csf("task_number")]] = $row_task[csf("notice_date_start")];
		$notice_finish_array[$row_task[csf("task_number")]] = $row_task[csf("notice_date_end")];
	} //var_dump($tna_task_id);die;
 
	//-----------------------------------------------------------------------------------------------
	
	$color=return_library_array( "select a.po_break_down_id ,b.color_name, a.color_number_id from wo_po_color_size_breakdown a, lib_color b where a.color_number_id=b.id and a.po_break_down_id='".$po_id."' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.po_break_down_id ,b.color_name, a.color_number_id order by b.color_name", "color_number_id", "color_name" );
	
	
	
	$mer_comments_array=array();
			
			$data_array1=sql_select("select a.job_no_mst,b.color_mst_id, b.color_number_id from  wo_po_break_down a, wo_po_color_size_breakdown b, wo_po_sample_approval_info c where a.job_no_mst=b.job_no_mst and b.job_no_mst=c.job_no_mst and a.id=b.po_break_down_id and b.po_break_down_id=c.po_break_down_id and  b.id=c.color_number_id  and a.id='$po_id' and b.color_mst_id !=0  and c.sample_type_id =7  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by a.id,b.id,c.current_status");   //group by c.id 
			
			
			
			if (count($data_array1)<=0)
			{
			$data_array1=sql_select("select b.color_mst_id, b.color_number_id from  wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.id='$po_id' and b.color_mst_id !=0 and a.id=b.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,a.po_number,b.color_mst_id, b.color_number_id order by a.id");
			}
			
			foreach ( $data_array1 as $row1)
			{
			
			//sample app.................................................................start
			$data_array_sample_table=sql_select("Select a.color_number_id,a.approval_status,a.sample_comments,b.sample_type from wo_po_sample_approval_info a,lib_sample b where a.sample_type_id=b.id and a.po_break_down_id='".$po_id."' and a.color_number_id ='".$row1[csf('color_mst_id')]."'");
				foreach ( $data_array_sample_table as $smp_row)
				{ 
					if($smp_row[csf('sample_comments')]!=''){
						if ($smp_row[csf("sample_type")]==2) {
								if($smp_row[csf('approval_status')]==1){$smp_data[8].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
								if($smp_row[csf('approval_status')]==3){$smp_data[12].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
							 }
						else if ($smp_row[csf("sample_type")]==3) {
								if($smp_row[csf('approval_status')]==1){$smp_data[7].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
								if($smp_row[csf('approval_status')]==3){$smp_data[13].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
						}
						else if ($smp_row[csf("sample_type")]==4) {
								if($smp_row[csf('approval_status')]==1){$smp_data[14].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
								if($smp_row[csf('approval_status')]==3){$smp_data[15].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
						}
						else if ($smp_row[csf("sample_type")]==7) {
								if($smp_row[csf('approval_status')]==1){$smp_data[16].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
								if($smp_row[csf('approval_status')]==3){$smp_data[17].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
						}
						else if ($smp_row[csf("sample_type")]==8) { 
								if($smp_row[csf('approval_status')]==1){$smp_data[21].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
								if($smp_row[csf('approval_status')]==3){$smp_data[22].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
						}
						else if ($smp_row[csf("sample_type")]==9) {
								if($smp_row[csf('approval_status')]==1){$smp_data[23].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
								if($smp_row[csf('approval_status')]==3){$smp_data[24].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
						}
					}

				}
			//sample app.................................................................end


			//lapdip app..................................................................start	
			$data_array_sample_table=sql_select("Select color_name_id,approval_status,lapdip_comments from wo_po_lapdip_approval_info where  po_break_down_id='".$po_id."' and status_active=1");
				foreach ( $data_array_sample_table as $smp_row)
				{ 
					if($smp_row[csf('lapdip_comments')]!=''){
						if($smp_row[csf('approval_status')]==1){$smp_data[9].=$color[$smp_row[csf('color_name_id')]].': '.$smp_row[csf('lapdip_comments')].',';}
						if($smp_row[csf('approval_status')]==3){$smp_data[10].=$color[$smp_row[csf('color_name_id')]].': '.$smp_row[csf('lapdip_comments')].',';}
					}
				
				}
		//lapdip app.........................................................end	
		
		
		//embell app..........................................................start	
			$data_array_sample_table=sql_select("Select color_name_id,approval_status,embellishment_comments from wo_po_embell_approval where po_break_down_id='".$po_id."'");
				foreach ( $data_array_sample_table as $smp_row)
				{ 
					if($smp_row[csf('embellishment_comments')]!=''){
						if($smp_row[csf('approval_status')]==1){$smp_data[19].=$color[$smp_row[csf('color_name_id')]].': '.$smp_row[csf('embellishment_comments')].',';}
						if($smp_row[csf('approval_status')]==3){$smp_data[20].=$color[$smp_row[csf('color_name_id')]].': '.$smp_row[csf('embellishment_comments')].',';}
					}
				
				}
		//embell app..........................................................end	


		//Trims app..........................................................start	
			$data_array_sample_table=sql_select("Select approval_status,accessories_comments from wo_po_trims_approval_info where po_break_down_id='".$po_id."'");
				foreach ( $data_array_sample_table as $smp_row)
				{ 
					if($smp_row[csf('accessories_comments')]){
						if($smp_row[csf('approval_status')]==1){$smp_data[25].=$smp_row[csf('accessories_comments')].',';}
						if($smp_row[csf('approval_status')]==3){$smp_data[11].=$smp_row[csf('accessories_comments')].',';}
					}
				}
			}
	//----------------------------------------------------------------------------------------

	$comments_array=array();
	$responsible_array=array();
	
	$res_comm_sql=sql_select("select task_id, comments, responsible,mer_comments from tna_progress_comments where order_id='$po_id'");
	
	foreach ($res_comm_sql as $row_res_comm)
	{
		if($row_res_comm[csf("comments")]!=''){
			$comments_array[$row_res_comm[csf("task_id")]] =$row_res_comm[csf("comments")];
		}
		if($row_res_comm[csf("mer_comments")]!=''){
			$mer_comments_array[$row_res_comm[csf("task_id")]] =$row_res_comm[csf("mer_comments")];
		}
		if($row_res_comm[csf("responsible")]!=''){
			$responsible_array[$row_res_comm[csf("task_id")]]=$row_res_comm[csf("responsible")];
		}
		
	}
	
	
	$execution_time_array=array();
	
	//$execution_time_sql= sql_select("select tna_task_id, execution_days from tna_task_template_details where task_template_id='$template_id'");
	
	$execution_time_sql= sql_select("select for_specific, tna_task_id, execution_days from tna_task_template_details where task_type=1 and is_deleted=0 and status_active=1");
	foreach ($execution_time_sql as $row_execution_time)
	{
		$execution_time_array[$row_execution_time[csf("for_specific")]][$row_execution_time[csf("tna_task_id")]]=$row_execution_time[csf("execution_days")];
	}
	
	//$upid_sql= sql_select("select min(id) as id from tna_progress_comments where tamplate_id='$template_id' and order_id='$po_id'");
	
	$upid_sql= sql_select("select min(id) as id from tna_progress_comments where order_id='$po_id'");
	foreach ($upid_sql as $row_upid)
	{
		$id_up=$row_upid[csf("id")];
	}
	
	$lead_time=return_library_array("select task_template_id,lead_time from tna_task_template_details where task_type=1 and is_deleted=0 and status_active=1 group by task_template_id,lead_time","task_template_id","lead_time");
		


	$booking_no=return_field_value("booking_no","wo_booking_dtls","po_break_down_id='".$po_id."' and status_active=1 and is_deleted=0","booking_no");

		/////////////////////////////////////////////
		$imbillishment_cost=return_field_value("rate","wo_pre_cost_embe_cost_dtls","job_no='".$result[0][csf('job_no')]."' and status_active=1 and is_deleted=0","rate");
		$is_imblishment=$imbillishment_cost?"Yes":"No";

		
		$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst where job_no='".$result[0][csf('job_no')]."'","job_no","costing_per"); 
		$set_item_ratio_arr = return_library_array("select gmts_item_id, set_item_ratio from wo_po_details_mas_set_details where job_no='".$result[0][csf('job_no')]."'","gmts_item_id","set_item_ratio"); 
		
	 
	 $sql_po_qty_fab_data=sql_select("select sum(c.plan_cut_qnty) as order_quantity,c.item_number_id,c.size_number_id,c.color_number_id  from  wo_po_color_size_breakdown c where  c.po_break_down_id=".$po_id." and c.status_active=1  group by c.item_number_id,c.size_number_id,c.color_number_id");
	 foreach($sql_po_qty_fab_data as $row){
		$key=$row[csf(item_number_id)].$row[csf(size_number_id)].$row[csf(color_number_id)];
		$sql_po_qty_fab_arr[$key]+=$row[csf(order_quantity)]; 
	 }
	
	$sql = "select id, job_no,item_number_id, body_part_id, fab_nature_id, color_type_id, fabric_description, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active from wo_pre_cost_fabric_cost_dtls where job_no='".$result[0][csf('job_no')]."' and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
		
	$req_qty=0;
	foreach( $data_array as $row )
    {
		
		$set_item_ratio=$set_item_ratio_arr[$row[csf('item_number_id')]];
		
		$fab_dtls_data=sql_select("select po_break_down_id,color_number_id,gmts_sizes,cons,requirment from wo_pre_cos_fab_co_avg_con_dtls where pre_cost_fabric_cost_dtls_id=".$row[csf("id")]." and po_break_down_id=".$po_id." and cons !=0 ");
	   
		foreach($fab_dtls_data as $fab_dtls_data_row )
		{
			$dzn_qnty=0;
			if($costing_per_arr[$result[0][csf('job_no')]]==1) $dzn_qnty=12;
			else if($costing_per_arr[$result[0][csf('job_no')]]==3) $dzn_qnty=12*2;
			else if($costing_per_arr[$result[0][csf('job_no')]]==4) $dzn_qnty=12*3;
			else if($costing_per_arr[$result[0][csf('job_no')]]==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
			
			$key=$result[0][csf('gmts_item_id')].$fab_dtls_data_row[csf('gmts_sizes')].$fab_dtls_data_row[csf('color_number_id')];
			$po_qty_fab=$sql_po_qty_fab_arr[$key]; 
			$req_qty+=($po_qty_fab/($dzn_qnty*$set_item_ratio))*$fab_dtls_data_row[csf("cons")];
		}
	}
	///////////////////////////////////////////////////////////////////// 
	   
	   
	   

	?> 
   
    <script>
	 
		var permission='<? echo $permission; ?>';
		//var refresh_data="";
	
		function fnc_progress_comments_entry(operation)
		{
			 //alert (operation);return;
	
			if ($('#is_responsible_all_order').is(':checked')) {
				
				var responsible_all_order=1;
			}
			else
			{
				var responsible_all_order=0;
			}
			
			
			var tot_row=$('#comments_tbl tbody tr').length;
			
			 //alert(tot_row);
			
			var data_all=''; var j=0;
			
			for(i=1; i<=tot_row; i++)
			{
				if (form_validation('taskid_'+i,'Task Number')==false )
				{
					alert("Task Number Not Found, Please Click On PO Number");
					return;
				}
				
				var responsible=$("#txtresponsible_"+i).val();
				var comments=$("#txtcomments_"+i).val();
				var mrc_comments=$("#txtmercomments_"+i).val();
				
				var taskid=$("#taskid_"+i).val();
				
				//alert(responsible);return;
				
				if (comments!="" || mrc_comments!="" || responsible!="")
				{
					
					j++;
					data_all+=get_submitted_data_string('txtresponsible_'+i+'*txtcomments_'+i+'*txtmercomments_'+i+'*taskid_'+i,"../../../",i);
				}
			}
			
			 //alert(data_all); return;
			
			if(data_all=='')
			{
				alert("No Comments Found");	
				return;
			}
			//alert(data_all);return;
			var data="action=save_update_delete_progress_comments&operation="+operation+"&responsible_all_order="+responsible_all_order+get_submitted_data_string('jobno*orderid*tamplateid',"../../../")+data_all+'&tot_row='+tot_row;
			//alert (data);return;
			freeze_window(operation);
			http.open("POST","tna_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fnc_progress_comments_Reply_info;
		}
		
		function fnc_progress_comments_Reply_info()
		{
			if(http.readyState == 4) 
			{
				// alert(http.responseText);//return;
				var reponse=trim(http.responseText).split('**');	
				show_msg(reponse[0]);
				var path_link=$('#txt_file_link_ref').val();
				$.post('tna_report_controller.php?job_no='+'<? echo $job_no; ?>'+'&po_id='+<? echo $po_id; ?>+'&template_id='+<? echo $template_id; ?>+'&tna_process_type='+<? echo $tna_process_type; ?>,
				{ 
					path: '', action: "generate_report_file", filename: path_link },
					function(data)
					{
						$('#txt_file_link_ref').val(data);
					}
				);
					set_button_status(1, permission, 'fnc_progress_comments_entry',3);
				release_freezing();	
			}
		}
		function autoRefresh_div()
		 {
			  $("#auto_id").load("tna_report_controller.php");// a function which will load data from other file after x seconds
		  }
		
		function openmypage(i)
		{	
			var title = 'TNA Progress Comment';
			
			var txtcomments = document.getElementById(i).value;
			//var data='additional_info='+additional_info;
			//alert(txtcomments);return;
			
			var page_link = 'tna_report_controller.php?data='+txtcomments+'&action=comments_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=160px,center=1,resize=1,scrolling=0','../../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				
				var additional_infos=this.contentDoc.getElementById("additional_infos").value;
				
				document.getElementById(i).value=additional_infos;
			}
		}
		
		function new_window()
		{
			var url = 'tna_report_controller.php?job_no=<? echo $job_no;?>&po_id=<? echo $po_id;?>&template_id=<? echo $template_id;?>&tna_process_type=<? echo $tna_process_type;?>&action=tna_progress_comment_print&permission=<? echo $permission;?>';
			 
			// 'tna_report_controller.php?job_no=OG-23-00432&po_id=74845&template_id=227&tna_process_type=1&action=tna_progress_comment_print'
			// tna_report_controller.php?job_no=OG-23-00726&po_id=76921,76919,76918,76917,76916,76915,76920&template_id=280,280,280,280,280,280,280&tna_process_type=1&action=tna_progress_comment_print&permission=1_1_1_1

			window.open(url, "MY PAGE");
		}
		
		function new_excel()
		{
			//window.open($('#txt_file_link_ref').val(), "#");
			var url = 'tna_report_controller.php?job_no=<? echo $job_no;?>&po_id=<? echo $po_id;?>&template_id=<? echo $template_id;?>&tna_process_type=<? echo $tna_process_type;?>&action=tna_progress_comment_print&permission=<? echo $permission;?>&isExcel=1';
			window.open(url, "MY PAGE");
		}
			
	
	</script>
   
  
		
   
</head>
<body onLoad="set_hotkey()">
	<div id="messagebox_main"></div>
	<div align="center" style="width:100%;">
    <? 
		echo load_freeze_divs ("../../../",'',1); 
	
		ob_start();
	?>
    
    <form name="tnaprocesscomments_3" id="tnaprocesscomments_3" autocomplete="off" >
    
    <div align="center" style="width:100%" id="details_reports">
    
     <table width="1000" border="1" rules="all" class="rpt_table">
    	<tr><td colspan="6" align="center"><b><font size="+1">TNA Progress Comment000</font></b></td></tr>
    </table>
    
    <table width="1000" border="1" rules="all" class="rpt_table">
    	<?php $buyer_id="";
		foreach($result as $row)
		{
			$buyer_id=$row[csf('buyer_name')];
		?>
    	<tr>
        	<td width="130">Company</td>
            <td width="176"><?php  echo $company_library[$row[csf('company_name')]];  ?></td>
            <td width="130">Buyer</td>
            <td width="176"><?php  echo $buyer_arr[$row[csf('buyer_name')]];  ?></td>
            <td width="130">Job Number</td>
           	<td width="176">
            	<? echo $row[csf('job_no')];   ?>
            	<Input type="hidden" name="jobno" class="text_boxes" ID="jobno" value="<? echo $job_no; ?>" style="width:100px" />
            	<Input type="hidden" name="orderid" class="text_boxes" ID="orderid" value="<? echo $po_id; ?>" style="width:100px" />
                <Input type="hidden" name="tamplateid" class="text_boxes" ID="tamplateid" value="<? echo $template_id; ?>" style="width:100px" />
            </td>
        </tr>
        <tr>
            <td>Order No</td>
           	<td><b><?php  echo $row[csf('po_number')]; ?></b></td>
            <td>Style Ref.</td>
            <td><?php  echo $row[csf('style_ref_no')];  ?></td>
            <td>Booking Number</td>
            <td><?php echo $booking_no; ?></td>
        </tr>
        <tr>
            <td>Garments Item</td>
            <td><?php  echo $garments_item[$row[csf('gmts_item_id')]];  ?></td>
            <td>Embellishment</td>
            <td><b><?php echo $is_imblishment;  ?></b></td>
            <td>SMV</td>
            <td><b><?php echo $row[csf('set_smv')]; ?></b></td>
        </tr>
        <tr>
            <td>Order Recv. Date</td>
           	<td><?php  echo change_date_format($row[csf('po_received_date')]); ?></td>
        	<td>Ship Date</td>
            <td><b><?php  echo change_date_format($row[csf('shipment_date')]); ?></b></td>
            <td>Template Lead Time</td>
            <td><b>
				<?
					if($tna_process_type==1)
					{
						$lead_timee=$lead_time_array[$template_id];
					}
					else
					{
						$lead_timee=$template_id;
					}
					echo $lead_timee;
                ?>
            </b></td>
        </tr>
        <tr>
            <td>Quantity (PCS)</td>
            <td><b><?php echo $row[csf('po_qty_pcs')];?></b></td>
            <td>Finish Req. (KG)</td>
            <td><b><?php echo number_format($req_qty,2); ?></b></td>
            <td>Number of Color</td>
            <td><b><?php echo count($color);  ?></b></td>
        </tr>
        <?php
		}
		?>
    </table>
    
    <table><tr height="10"><td colspan="6">&nbsp;</td></tr></table>
    
    <table style="width: 1130px;">
        <tr>
            <td>
                <div style="width: 1120px;font-size:12px;">
                <table width="1100" border="1" rules="all" class="rpt_table">
                    <thead>
                    	<tr align="center">
                            <th width="30">Task No</th>
                            <th width="150">Task Name</th>
                            <th width="60">Allowed Days</th>
                            <th width="70">Plan Start Date</th>
                            <th width="70">Plan Finish Date</th>
                            <th width="70">Actual Start Date</th>
                            <th width="70">Actual Finish Date</th>
                            <th width="70">Start Delay/ Early By</th>
                            <th width="70">Finish Delay/ Early By</th>
                            <th width="150">
                            	Responsible
                                <input type="checkbox" checked id="is_responsible_all_order" value=""> All Order
                            </th>
                            <th width="120">Comments</th>
                            <th width="">Mer. Comments</th>
                        </tr>
                    </thead>
                </table>
                </div>
            </td>
        </tr>
    </table> 
    <table style="width:1130px;">
        <tr>
            <td>    
                <div style="width: 1120px;overflow-y: scroll; max-height:180px;font-size:12px;" id="scroll_body2">
                <table width="1100px" border="1" rules="all" class="rpt_table" id="comments_tbl">
                	<tbody>
						<?php
                        $i=0;
						
                        foreach($tna_task_id as $key)
                        {
                            $i++;
                            $trcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
							$bgcolor1=""; $bgcolor="";
									
							if ($plan_start_array[$key]!=$blank_date) 
							{
								if (strtotime($notice_start_array[$key])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($plan_start_array[$key]))  $bgcolor="#FFFF00";
								else if (strtotime($plan_start_array[$key])<strtotime(date("Y-m-d",time())))  $bgcolor="#FF0000";
								else $bgcolor="";
								
							}
							 
							if ($plan_finish_array[$key]!=$blank_date) {
								if (strtotime($notice_finish_array[$key])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($plan_finish_array[$key]))  $bgcolor1="#FFFF00";
								else if (strtotime($plan_finish_array[$key])<strtotime(date("Y-m-d",time())))  $bgcolor1="#FF0000"; else $bgcolor1="";
							}
							
							if ($actual_start_array[$key]!=$blank_date) $bgcolor="";
							if ($actual_finish_array[$key]!=$blank_date) $bgcolor1="";
							
							// Delay / Early............
									
							$bgcolor5=""; $bgcolor6="";
							$delay=""; $early="";
							
							if($actual_start_array[$key]!=$blank_date)
							{
								$start_diff1 = datediff( "d", $actual_start_array[$key], $plan_start_array[$key]);
								if($actual_finish_array[$key]=="" || $actual_finish_array[$key]=="0000-00-00"){
									
									$finish_diff1 = datediff( "d",date("Y-m-d"), $plan_finish_array[$key]);
								}
								else
								{
									$finish_diff1 = datediff( "d", $actual_finish_array[$key], $plan_finish_array[$key]);	
								}
								$start_diff=$start_diff1-1;
								$finish_diff=$finish_diff1-1;
								
								if($start_diff<0)
								{
									$bgcolor5="#2A9FFF";	//Blue	
									$start="(Delay)";
								}
								if($start_diff>0)
								{
									$bgcolor5="";
									$start="(Early)";
								}
								if($finish_diff<0)
								{
									if($actual_finish_array[$key]=="" || $actual_finish_array[$key]=="0000-00-00"){
										$bgcolor6="#FF0000";//Blue
									}
									else
									{
										$bgcolor6="#2A9FFF";//Blue
									}
									$finish="(Delay)";
								}
								if($finish_diff>0)
								{	
									$bgcolor6="";
									$finish="(Early)";
								}
							}
							else
							{
								if(date("Y-m-d")> date("Y-m-d",strtotime($plan_start_array[$key])))
								{
									$start_diff1 = datediff( "d", $plan_start_array[$key], date("Y-m-d"));
									$start_diff=$start_diff1-1;
									$bgcolor5="#FF0000";		//Red
									$start="(Delay)";
								}
								if(date("Y-m-d")> date("Y-m-d",strtotime($plan_finish_array[$key])))
								{
									$finish_diff1 = datediff( "d", $plan_finish_array[$key], date("Y-m-d"));
									$finish_diff=$finish_diff1-1;
									$bgcolor6="#FF0000";
									$finish="(Delay)";
								}
								if(date("Y-m-d")<= date("Y-m-d",strtotime($plan_start_array[$key])))
								{
									$start_diff = "";
									$bgcolor5="";
									$start="";
									//$start="(Ac. Start Dt. Not Found)";
								}
								if(date("Y-m-d")<= date("Y-m-d",strtotime($plan_finish_array[$key])))
								{
									$finish_diff = "";
									$bgcolor6="";
									$finish="";
									//$finish="(Ac. Finish Dt. Not Found)";
									//echo date("Y-m-d").'<='. change_date_format($plan_finish_array[$key]).'<br>';
								}
							}
                        ?>
                        <tr bgcolor="<?= $trcolor; ?>">
                            <td align="center" width="30"><?= $i; ?></td>
                            <td width="150"> <?= $tna_task_arr[$key]; ?></td>
                            <td align="center" width="60"><?= datediff( "d", $plan_start_array[$key],$plan_finish_array[$key]);//$execution_time_array[$buyer_id][$key]; ?></td>
                            <td align="center" width="70"><?= change_date_format($plan_start_array[$key]); ?></td>
                            <td align="center" width="70"><?= change_date_format($plan_finish_array[$key]); ?></td>
                            <td align="center" width="70" bgcolor="<? echo $bgcolor;  ?>">
								<?
								if($db_type==0)
								{
									if($actual_start_array[$key]=="0000-00-00") echo "";
									else echo  change_date_format($actual_start_array[$key]);
								}
								else
								{
									if($actual_start_array[$key]=="") echo "";
									else echo  change_date_format($actual_start_array[$key]);
								}
                                ?>
                            </td>
                            <td align="center" width="70" bgcolor="<? echo $bgcolor1;  ?>">
								<?  
                                    if($db_type==0)
                                    {
                                        if($actual_finish_array[$key]=="0000-00-00") echo "";
                                        else echo  change_date_format($actual_finish_array[$key]);
                                    }
                                    else
                                    {
                                        if($actual_finish_array[$key]=="") echo "";
                                        else echo  change_date_format($actual_finish_array[$key]);
                                    } 
                                ?>
                            </td>
                            <td align="center" width="70" bgcolor="<? echo $bgcolor5;  ?>">
								<?  
                                    echo abs($start_diff)." ".$start;
                                ?>
                            </td>
                            <td align="center" width="70" bgcolor="<? echo $bgcolor6;  ?>">
                                <?  
                                    echo abs($finish_diff)." ".$finish;
                                ?>
                            </td>
                            <td width="150">
                            	<Input name="txtresponsible[]" class="text_boxes" ID="txtresponsible_<?= $i; ?>" value="<?= $responsible_array[$key]; ?>" style="width:138px" />
                            	<Input type="hidden" name="taskid[]" class="text_boxes" ID="taskid_<?= $i; ?>" value="<?= $key; ?>" style="width:50px">
                            </td>
                            <td width="120" align="center"><Input name="txtcomments[]" class="text_boxes" ID="txtcomments_<?= $i; ?>" value="<?= $comments_array[$key]; ?>" onDblClick="openmypage('txtcomments_<?= $i; ?>'); return false" style="width:100px;" autocomplete="off" readonly placeholder="Double Click"  /></td>
                            <?
							$mer_comments=$mer_comments_array[$key];
							if($mer_comments==""){
								$mer_comments=substr($smp_data[$key],0,-1);
							}
							?>
                            <td align="center"><Input name="txtmercomments[]" class="text_boxes" ID="txtmercomments_<?= $i; ?>" value="<?= $mer_comments; ?>" onDblClick="openmypage('txtmercomments_<?= $i; ?>'); return false" style="width:90%;" autocomplete="off" readonly placeholder="Double Click" /></td>
                        </tr>
                        <?
                        }
                        ?>
                    </tbody>
                </table>
                </div>
    		</td>
        </tr>
    </table>

    
    
    </div>
     
    <table style="width:580px;">
    	<tr>
        	<td colspan="4" height="50" align="right" class="button_container">
            <input type="hidden" id="txt_update_tna_id" name="txt_update_tna_id"  value="<? echo $mid; ?>" />
            <?
					
				if($id_up!='')
				{
					echo load_submit_buttons('1_1_1_1', "fnc_progress_comments_entry", 1,0,"reset_form('tnaprocesscomments_3','','','','','');",3);
				}
				else
				{
					echo load_submit_buttons('1_1_1_1', "fnc_progress_comments_entry", 0,0,"reset_form('tnaprocesscomments_3','','','','','');",3);
				}
			?>
            </td>
            <td valign="top" class="button_container"><input type="button" class="formbutton" value="Print Preview" name="print" id="print" style="width:100px;" onClick="new_window()" /></td>
            <td valign="top" class="button_container"><input type="button" class="formbutton" value="Excel Preview" name="print" id="print" style="width:100px;" onClick="new_excel()" /></td>
        </tr>
    </table>
    </form>
    <?
		$name=time();
		$filenames=$name.".xls";
		//echo $html;
	?>

    <input type="hidden" id="txt_file_link_ref" value="<? echo $filenames; ?>">
    
    

    
    </div>
    <div id="report_container123" align="center"></div>
    
    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
    <?
	
		exit();	
}

if($action=="tna_progress_comment_print")
{ 

	//print_r($po_id);die;

	if($db_type==0) $blank_date="0000-00-00"; else $blank_date=""; 	
	
	echo load_html_head_contents("TNA","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	$tna_task_arr=return_library_array( "select task_name, task_short_name from lib_tna_task where task_type = 1",'task_name','task_short_name');
	$tna_task_grup_arr=return_library_array( "select task_name, task_group from lib_tna_task where task_type = 1",'task_name','task_group');
	
	$lead_time_array=return_library_array("select task_template_id,lead_time from tna_task_template_details where task_type=1 and is_deleted=0 and status_active=1 group by lead_time,task_template_id","task_template_id",'lead_time');

	$sql ="select b.company_name,b.buyer_name,a.po_number,b.job_no,b.style_ref_no,b.gmts_item_id,a.po_received_date,a.shipment_date,(po_quantity*total_set_qnty) as po_qty_pcs, set_smv from  wo_po_break_down a,wo_po_details_master b where a.id IN ($po_id) and a.job_no_mst=b.job_no";
 
	// $sql ="select b.company_name,b.buyer_name,a.po_number,b.job_no,b.style_ref_no,b.gmts_item_id,a.po_received_date,a.shipment_date,(po_quantity*total_set_qnty) as po_qty_pcs, set_smv from  wo_po_break_down a,wo_po_details_master b where b.job_no='$job_no' and a.job_no_mst=b.job_no";
 

	//$sql ="select b.company_name,b.buyer_name,a.po_number,b.job_no,b.style_ref_no,b.gmts_item_id,a.po_received_date,a.shipment_date,(po_quantity*total_set_qnty) as po_qty_pcs, set_smv from  wo_po_break_down a,wo_po_details_master b where a.id=$po_id and a.job_no_mst=b.job_no";
	//$result=sql_select($sql);

  // echo $sql;die;

	$result = sql_select($sql);
	$jobArr = array();
	foreach($result as $data){
		$jobArr[$data['JOB_NO']]['JOB_NO'] = $data['JOB_NO'];
		$jobArr[$data['JOB_NO']]['COMPANY_NAME'] = $data['COMPANY_NAME'];
		$jobArr[$data['JOB_NO']]['BUYER_NAME'] = $data['BUYER_NAME'];
		$jobArr[$data['JOB_NO']]['PO_NUMBER'] .= ','.$data['PO_NUMBER'];
		$jobArr[$data['JOB_NO']]['STYLE_REF_NO'] = $data['STYLE_REF_NO'];
		$jobArr[$data['JOB_NO']]['GMTS_ITEM_ID'] = $data['GMTS_ITEM_ID'];
		$jobArr[$data['JOB_NO']]['PO_RECEIVED_DATE'] = $data['PO_RECEIVED_DATE'];
		$jobArr[$data['JOB_NO']]['SHIPMENT_DATE'] = $data['SHIPMENT_DATE'];
		$jobArr[$data['JOB_NO']]['SET_SMV'] = $data['SET_SMV'];
		//$jobArr[$data['JOB_NO']]['PO_QTY_PCS'] += $data['PO_QUANTITY']*$data['TOTAL_SET_QNTY'];
		$jobArr[$data['JOB_NO']]['PO_QTY_PCS'] += $data['PO_QTY_PCS'];
	}

	// echo "<pre>";
	// print_r($jobArr);
	// echo "</pre>";
	// die;
	 
	$tna_task_id=array();
	$plan_start_array=array();
	$plan_finish_array=array();
	$actual_start_array=array();
	$actual_finish_array=array();
	$notice_start_array=array();
	$notice_finish_array=array();
	
	// $task_sql=sql_select("select a.task_number,a.task_start_date,a.task_finish_date,a.actual_start_date,a.actual_finish_date,a.notice_date_start,a.notice_date_end from tna_process_mst a, lib_tna_task b where a.task_type=1 AND b.task_type = 1 and a.task_number=b.task_name and a.po_number_id='$po_id' and b.status_active=1 and b.is_deleted=0 order by b.task_sequence_no asc");

	// $task_sql = sql_select("SELECT a.task_number, a.task_start_date, a.task_finish_date, a.actual_start_date, a.actual_finish_date, a.notice_date_start, a.notice_date_end, b.task_group FROM tna_process_mst a, lib_tna_task b WHERE a.task_type = 1 AND b.task_type = 1 AND a.task_number = b.task_name AND a.po_number_id =$po_id AND b.status_active = 1 AND b.is_deleted = 0 ORDER BY b.task_sequence_no ASC");

	$task_sql = sql_select("SELECT a.task_number, a.task_start_date, a.task_finish_date, a.actual_start_date, a.actual_finish_date, a.notice_date_start, a.notice_date_end, b.task_group FROM tna_process_mst a, lib_tna_task b WHERE a.task_type = 1 AND b.task_type = 1 AND a.task_number = b.task_name AND a.po_number_id in($po_id) AND b.status_active = 1 AND b.is_deleted = 0 ORDER BY b.task_sequence_no ASC");

	foreach ($task_sql as $row_task)
	{
		$tna_task_id[$row_task[csf("task_number")]]=$row_task[csf("task_number")];
		
		$plan_start_array[$row_task[csf("task_number")]] =$row_task[csf("task_start_date")];
		$plan_finish_array[$row_task[csf("task_number")]]=$row_task[csf("task_finish_date")];
		
		$actual_start_array[$row_task[csf("task_number")]] =$row_task[csf("actual_start_date")];
		$actual_finish_array[$row_task[csf("task_number")]]=$row_task[csf("actual_finish_date")];
		
		$notice_start_array[$row_task[csf("task_number")]] =$row_task[csf("notice_date_start")];
		$notice_finish_array[$row_task[csf("task_number")]]=$row_task[csf("notice_date_end")];
	} //var_dump($tna_task_id);die;
	
	//-----------------------------------------------------------------------------------------------
	
	//$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$color=return_library_array( "select a.po_break_down_id ,b.color_name, a.color_number_id from wo_po_color_size_breakdown a, lib_color b where a.color_number_id=b.id and a.po_break_down_id='".$po_id."' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.po_break_down_id ,b.color_name, a.color_number_id order by b.color_name", "color_number_id", "color_name" );
		
	
	
	$mer_comments_array=array();
			
			$data_array1=sql_select("select a.job_no_mst,b.color_mst_id, b.color_number_id from  wo_po_break_down a, wo_po_color_size_breakdown b, wo_po_sample_approval_info c where a.job_no_mst=b.job_no_mst and b.job_no_mst=c.job_no_mst and a.id=b.po_break_down_id and b.po_break_down_id=c.po_break_down_id and  b.id=c.color_number_id  and a.id='$po_id' and b.color_mst_id !=0  and c.sample_type_id =7  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by a.id,b.id,c.current_status");   //group by c.id 
			
			
			
			if (count($data_array1)<=0)
			{
			$data_array1=sql_select("select b.color_mst_id, b.color_number_id from  wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.id='$po_id' and b.color_mst_id !=0 and a.id=b.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,a.po_number,b.color_mst_id, b.color_number_id order by a.id");
			}
			
			foreach ( $data_array1 as $row1)
			{
				//$total_color[$row1[csf('color_number_id')]]=$row1[csf('color_number_id')];
			
			//sample app.................................................................start
			$data_array_sample_table=sql_select("Select a.color_number_id,a.approval_status,a.sample_comments,b.sample_type from wo_po_sample_approval_info a,lib_sample b where a.sample_type_id=b.id and a.po_break_down_id='".$po_id."' and a.color_number_id ='".$row1[csf('color_mst_id')]."'");
				foreach ( $data_array_sample_table as $smp_row)
				{ 
					if ($smp_row[csf("sample_type")]==2) {
							if($smp_row[csf('approval_status')]==1){$smp_data[8].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
							if($smp_row[csf('approval_status')]==3){$smp_data[12].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
						 }
					else if ($smp_row[csf("sample_type")]==3) {
							if($smp_row[csf('approval_status')]==1){$smp_data[7].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
							if($smp_row[csf('approval_status')]==3){$smp_data[13].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
					}
					else if ($smp_row[csf("sample_type")]==4) {
							if($smp_row[csf('approval_status')]==1){$smp_data[14].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
							if($smp_row[csf('approval_status')]==3){$smp_data[15].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
					}
					else if ($smp_row[csf("sample_type")]==7) {
							if($smp_row[csf('approval_status')]==1){$smp_data[16].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
							if($smp_row[csf('approval_status')]==3){$smp_data[17].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
					}
					else if ($smp_row[csf("sample_type")]==8) { 
							if($smp_row[csf('approval_status')]==1){$smp_data[21].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
							if($smp_row[csf('approval_status')]==3){$smp_data[22].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
					}
					else if ($smp_row[csf("sample_type")]==9) {
							if($smp_row[csf('approval_status')]==1){$smp_data[23].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
							if($smp_row[csf('approval_status')]==3){$smp_data[24].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
					}

				}
			//sample app.................................................................end


			//lapdip app..................................................................start	
			$data_array_sample_table=sql_select("Select color_name_id,approval_status,lapdip_comments from wo_po_lapdip_approval_info where  po_break_down_id='".$po_id."' and status_active=1");
				foreach ( $data_array_sample_table as $smp_row)
				{ 
					if($smp_row[csf('approval_status')]==1){$smp_data[9].=$color[$smp_row[csf('color_name_id')]].': '.$smp_row[csf('lapdip_comments')].',';}
					if($smp_row[csf('approval_status')]==3){$smp_data[10].=$color[$smp_row[csf('color_name_id')]].': '.$smp_row[csf('lapdip_comments')].',';}
				
				}
		//lapdip app.........................................................end	
		
		
		//embell app..........................................................start	
		$data_array_sample_table=sql_select("Select color_name_id,approval_status,embellishment_comments from wo_po_embell_approval where po_break_down_id='".$po_id."'");
			foreach ( $data_array_sample_table as $smp_row)
			{ 
				if($smp_row[csf('approval_status')]==1){$smp_data[19].=$color[$smp_row[csf('color_name_id')]].': '.$smp_row[csf('embellishment_comments')].',';}
				if($smp_row[csf('approval_status')]==3){$smp_data[20].=$color[$smp_row[csf('color_name_id')]].': '.$smp_row[csf('embellishment_comments')].',';}
			
			}
		//embell app..........................................................end	


		//Trims app..........................................................start	
		$data_array_sample_table=sql_select("Select approval_status,accessories_comments from wo_po_trims_approval_info where po_break_down_id='".$po_id."'");
			foreach ( $data_array_sample_table as $smp_row)
			{ 
				if($smp_row[csf('approval_status')]==1){$smp_data[25].=$smp_row[csf('accessories_comments')].',';}
				if($smp_row[csf('approval_status')]==3){$smp_data[11].=$smp_row[csf('accessories_comments')].',';}
			}

		}
		//----------------------------------------------------------------------------------------

	
	$comments_array=array();
	$responsible_array=array();
	
	$res_comm_sql=sql_select("select task_id, comments, responsible,mer_comments from tna_progress_comments where order_id='$po_id'");
	
	foreach ($res_comm_sql as $row_res_comm)
	{
		$comments_array[$row_res_comm[csf("task_id")]] =$row_res_comm[csf("comments")];
		$mer_comments_array[$row_res_comm[csf("task_id")]] =$row_res_comm[csf("mer_comments")];
		$responsible_array[$row_res_comm[csf("task_id")]]=$row_res_comm[csf("responsible")];
	}
	
	
	$execution_time_array=array();
	
	
	$execution_time_sql= sql_select("select for_specific, tna_task_id, execution_days from tna_task_template_details  where task_type=1 and is_deleted=0 and status_active=1");
	foreach ($execution_time_sql as $row_execution_time)
	{
		$execution_time_array[$row_execution_time[csf("for_specific")]][$row_execution_time[csf("tna_task_id")]]=$row_execution_time[csf("execution_days")];
	}
	
	
	$upid_sql= sql_select("select min(id) as id from tna_progress_comments where order_id='$po_id'");
	foreach ($upid_sql as $row_upid)
	{
		$id_up=$row_upid[csf("id")];
	}
	
	$lead_time=return_library_array("select task_template_id,lead_time from tna_task_template_details  where task_type=1 and is_deleted=0 and status_active=1 group by task_template_id,lead_time","task_template_id","lead_time");
		


	$booking_no=return_field_value("booking_no","wo_booking_dtls","po_break_down_id='".$po_id."' and status_active=1 and is_deleted=0","booking_no");

		/////////////////////////////////////////////
		$imbillishment_cost=return_field_value("rate","wo_pre_cost_embe_cost_dtls","job_no='".$result[0][csf('job_no')]."' and status_active=1 and is_deleted=0","rate");
		$is_imblishment=$imbillishment_cost?"Yes":"No";

		
		$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst where job_no='".$result[0][csf('job_no')]."'","job_no","costing_per"); 
		$set_item_ratio_arr = return_library_array("select gmts_item_id, set_item_ratio from wo_po_details_mas_set_details where job_no='".$result[0][csf('job_no')]."'","gmts_item_id","set_item_ratio"); 
		
	 
	 $sql_po_qty_fab_data=sql_select("select sum(c.plan_cut_qnty) as order_quantity,c.item_number_id,c.size_number_id,c.color_number_id  from  wo_po_color_size_breakdown c where  c.po_break_down_id=".$po_id." and c.status_active=1  group by c.item_number_id,c.size_number_id,c.color_number_id");
	 foreach($sql_po_qty_fab_data as $row){
		$key=$row[csf(item_number_id)].$row[csf(size_number_id)].$row[csf(color_number_id)];
		$sql_po_qty_fab_arr[$key]+=$row[csf(order_quantity)]; 
	 }
	
	$sql = "select id, job_no,item_number_id, body_part_id, fab_nature_id, color_type_id, fabric_description, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active from wo_pre_cost_fabric_cost_dtls where job_no='".$result[0][csf('job_no')]."' and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
		
	$req_qty=0;
	foreach( $data_array as $row )
    {
		
		$set_item_ratio=$set_item_ratio_arr[$row[csf('item_number_id')]];
		
		$fab_dtls_data=sql_select("select po_break_down_id,color_number_id,gmts_sizes,cons,requirment from wo_pre_cos_fab_co_avg_con_dtls where pre_cost_fabric_cost_dtls_id=".$row[csf("id")]." and po_break_down_id=".$po_id." and cons !=0 ");
	   
		foreach($fab_dtls_data as $fab_dtls_data_row )
		{
			$dzn_qnty=0;
			if($costing_per_arr[$result[0][csf('job_no')]]==1) $dzn_qnty=12;
			else if($costing_per_arr[$result[0][csf('job_no')]]==3) $dzn_qnty=12*2;
			else if($costing_per_arr[$result[0][csf('job_no')]]==4) $dzn_qnty=12*3;
			else if($costing_per_arr[$result[0][csf('job_no')]]==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
			
			$key=$result[0][csf('gmts_item_id')].$fab_dtls_data_row[csf('gmts_sizes')].$fab_dtls_data_row[csf('color_number_id')];
			$po_qty_fab=$sql_po_qty_fab_arr[$key]; 
			$req_qty+=($po_qty_fab/($dzn_qnty*$set_item_ratio))*$fab_dtls_data_row[csf("cons")];
		}
	}
?> 

</head>
<body>
	<? ob_start();?> 
	<div style="width:100%;"> 
	<table> 
		<tr>
            <td colspan="7" align="center" style="font-size:x-large"><strong><u>TNA Progress Comment</u></strong></center></td>
        </tr>
			<?php $buyer_id="";
			foreach($jobArr as $row)
			{
				$buyer_id=$row[csf('buyer_name')];
			?>
			<div style="width:100%" id="details_reports">
				<table>
					<tr>
						<td width="110">Company</td>
						<td width="180" colspan="2">: <?= $company_library[$row[csf('company_name')]];  ?></td>
						<td width="110">Buyer</td>
						<td width="180" colspan="2">: <?= $buyer_arr[$row[csf('buyer_name')]];  ?></td>
						<td width="110">Job Number</td>
						<td width="180" colspan="2">: <?= $row[csf('job_no')];?></td>
					</tr>
					<tr>
						<td>Order No</td>
						<td colspan="2">: <b><?= trim($row[csf('po_number')], ","); ?></b></td>
						<td>Style Ref.</td>
						<td colspan="2">: <?= $row[csf('style_ref_no')];  ?></td>
						<td>Booking Number</td>
						<td colspan="2">: <?= $booking_no; ?></td>
					</tr>
					<tr>
						<td>Garments Item</td>
						<td colspan="2">: <?= $garments_item[$row[csf('gmts_item_id')]];  ?></td>
						<td>Embellishment</td>
						<td colspan="2">: <b><?= $is_imblishment;  ?></b></td>
						<td>SMV</td>
						<td colspan="2">: <b><?= $row[csf('set_smv')]; ?></b></td>
					</tr>
					<tr>
						<td>Order Recv. Date</td>
						<td colspan="2">: <?= change_date_format($row[csf('po_received_date')]); ?></td>
						<td>Ship Date</td>
						<td colspan="2">: <b><?= change_date_format($row[csf('shipment_date')]); ?></b></td>
						<td>Lead Time</td>
						<td colspan="2">: <b>
							<? 
								if($tna_process_type==1)
								{
									$lead_timee=$lead_time_array[$template_id];
								}
								else
								{
									$lead_timee=$template_id;
								}
								echo $lead_timee+1;
							?>
						</b></td>
					</tr>
					<tr>
						<td>Quantity (PCS)</td>
						<td colspan="2">: <b><?= $row[csf('po_qty_pcs')];?></b></td>
						<td>Finish Req. (KG)</td>
						<td colspan="2">: <b><?= number_format($req_qty,2); ?></b></td>
						<td>Number of Color</td>
						<td colspan="2">: <b><?= count($color);  ?></b></td>
					</tr>
					
				</table>
			
			
				<table border="1" rules="all" class="rpt_table">
					<thead>
						<tr align="center">
							<th width="30">Task No</th>
							<th width="150">Task Name</th>
							<th width="50">Allowed Days</th>
							<th width="70">Plan Start Date</th>
							<th width="70">Plan Finish Date</th>
							<th width="70">Actual Start Date</th>
							<th width="70">Actual Finish Date</th>
							<th width="70">Start Delay/ Early By</th>
							<th width="70">Finish Delay/ Early By</th>
							<th width="120">Responsible</th>
							<th width="120">Responsible Dept.</th>
							<th width="120">Comments</th>
							<th width="120">Mer. Comments</th>
						</tr>
					</thead>
					<tbody>
						<?php
						// print_r($tna_task_id);exit;
						$i=0;
						foreach($tna_task_id as $key)
						{
							$i++;
							
							if ($i%2==0)  
								$trcolor="#E9F3FF";
							else
								$trcolor="#FFFFFF";	
						
							$bgcolor1=""; $bgcolor="";
									
							if ($plan_start_array[$key]!=$blank_date) 
							{
								if (strtotime($notice_start_array[$key])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($plan_start_array[$key]))  $bgcolor="#FFFF00";
								else if (strtotime($plan_start_array[$key])<strtotime(date("Y-m-d",time())))  $bgcolor="#FF0000";
								else $bgcolor="";
								
							}
							
							if ($plan_finish_array[$key]!=$blank_date) {
								if (strtotime($notice_finish_array[$key])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($plan_finish_array[$key]))  $bgcolor1="#FFFF00";
								else if (strtotime($plan_finish_array[$key])<strtotime(date("Y-m-d",time())))  $bgcolor1="#FF0000"; else $bgcolor1="";
							}
							
							if ($actual_start_array[$key]!=$blank_date) $bgcolor="";
							if ($actual_finish_array[$key]!=$blank_date) $bgcolor1="";
							
							// Delay / Early............
									
							$bgcolor5=""; $bgcolor6="";
							$delay=""; $early="";
							
							if($actual_start_array[$key]!=$blank_date)
							{
								$start_diff1 = datediff( "d", $actual_start_array[$key], $plan_start_array[$key]);
								if($actual_finish_array[$key]=="" || $actual_finish_array[$key]=="0000-00-00"){
									
									$finish_diff1 = datediff( "d",date("Y-m-d"), $plan_finish_array[$key]);
								}
								else
								{
									$finish_diff1 = datediff( "d", $actual_finish_array[$key], $plan_finish_array[$key]);	
								}
								$start_diff=$start_diff1-1;
								$finish_diff=$finish_diff1-1;
								
								if($start_diff<0)
								{
									$bgcolor5="#2A9FFF";	//Blue	
									$start="(Delay)";
								}
								if($start_diff>0)
								{
									$bgcolor5="";
									$start="(Early)";
								}
								if($finish_diff<0)
								{
									if($actual_finish_array[$key]=="" || $actual_finish_array[$key]=="0000-00-00"){
										$bgcolor6="#FF0000";//Blue
									}
									else
									{
										$bgcolor6="#2A9FFF";//Blue
									}
									$finish="(Delay)";
								}
								if($finish_diff>0)
								{	
									$bgcolor6="";
									$finish="(Early)";
								}
							}
							else
							{
								if(date("Y-m-d")> date("Y-m-d",strtotime($plan_start_array[$key])))
								{
									$start_diff1 = datediff( "d", $plan_start_array[$key], date("Y-m-d"));
									$start_diff=$start_diff1-1;
									$bgcolor5="#FF0000";		//Red
									$start="(Delay)";
								}
								if(date("Y-m-d")> date("Y-m-d",strtotime($plan_finish_array[$key])))
								{
									$finish_diff1 = datediff( "d", $plan_finish_array[$key], date("Y-m-d"));
									$finish_diff=$finish_diff1-1;
									$bgcolor6="#FF0000";
									$finish="(Delay)";
								}
								if(date("Y-m-d")<= date("Y-m-d",strtotime($plan_start_array[$key])))
								{
									$start_diff = "";
									$bgcolor5="";
									$start="";
								}
								if(date("Y-m-d")<= date("Y-m-d",strtotime($plan_finish_array[$key])))
								{
									$finish_diff = "";
									$bgcolor6="";
									$finish="";
								}
							}
						?>
						<tr bgcolor="<?= $trcolor; ?>">
							<td align="center" ><?= $i; ?></td>
							<td><?= $tna_task_arr[$key]; ?></td>
							<td align="center"><?= datediff( "d", $plan_start_array[$key],$plan_finish_array[$key]); ?></td>
							<td align="center"><?= change_date_format($plan_start_array[$key]); ?></td>
							<td align="center"><?= change_date_format($plan_finish_array[$key]); ?></td>
							<td align="center" bgcolor="<? echo $bgcolor;  ?>">
								<?
									if($db_type==0)
									{
										if($actual_start_array[$key]=="0000-00-00") echo "";
										else echo  change_date_format($actual_start_array[$key]);
									}
									else
									{
										if($actual_start_array[$key]=="") echo "";
										else echo  change_date_format($actual_start_array[$key]);
									}
								?>
							</td>
							<td align="center" bgcolor="<? echo $bgcolor1;  ?>">
								<?  
									if($db_type==0)
									{
										if($actual_finish_array[$key]=="0000-00-00") echo "";
										else echo  change_date_format($actual_finish_array[$key]);
									}
									else
									{
										if($actual_finish_array[$key]=="") echo "";
										else echo  change_date_format($actual_finish_array[$key]);
									} 
								?>
							</td>
							<td align="center" bgcolor="<? echo $bgcolor5;  ?>">
								<?= abs($start_diff)." ".$start;?>
							</td>
							<td bgcolor="<? echo $bgcolor6;  ?>">
								<?= abs($finish_diff)." ".$finish;?>
							</td>
							<td><?= $responsible_array[$key]; ?></td>
							<td><?= $tna_task_grup_arr[$key]; ?> </td>
							<td><?= $comments_array[$key]; ?></td>
							<?
							$mer_comments=$mer_comments_array[$key];
							if($mer_comments==""){
								$mer_comments=substr($smp_data[$key],0,-1);
							}
							?>
							
							<td><?= $mer_comments; ?></td>
						</tr>
						<?
						}
						?>
					</tbody>
				</table>
			</div>
			<?php
			}
			?>
	    </table>
    </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
    <?
    if($isExcel==1){
		$html=ob_get_contents();
		$filenames=time().".xls";
		foreach (glob("*.xls") as $filename) {
		@unlink($filename);
		}
		$create_new_doc = fopen($filenames, 'w');	
		$is_created = fwrite($create_new_doc,$html);
	
	
	?>
    <script> window.open('<? echo $filenames;?>'); </script>
    <?
	}
	
	
		exit();
		
	
}



if($action=="comments_popup")
{
	echo load_html_head_contents("TNA Progress Comment", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//$data=explode('*',$data);
?>
	<script>
	
		var additional_info='<?  echo $data; ?>';
	
		if(additional_info != "")
		{ 
			$(document).ready(function(e) {
				$('#comments').val( additional_info);
			}); 
		}
	
	
		function submit_comments()
		{
			var additional_infos =   $('#comments').val();
			
			$('#additional_infos').val( additional_infos );
			
			parent.emailwindow.hide();	
			   
		}
    </script>
</head>
<body>
	<div align="center" style="width:100%;" >
	<form name="comments_1"  id="comments_1" autocomplete="off">
		<table width="700" cellspacing="0" cellpadding="0" class="rpt_table" align="center">
    		<input type="hidden" name="additional_infos" id="additional_infos" value="">
            <tr>
                <td width="120px" height="5" align="center" valign="middle">Comments</td>
                <td width="570px">
                    <textarea rows="4" cols="115" style="white-space: pre-line;" wrap="hard" name="comments" id="comments"></textarea>
                </td>			
            </tr>
            <tr height="20">&nbsp;</tr>
            <tr>
                <td align="center" colspan="2">
                    <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="submit_comments();" style="width:100px" />
                </td>	  
            </tr>
    	</table>
    </form>
	</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}


if ($action=="set_ready_to_app")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}


	$app_status=str_replace("'","",$app_status);
 	
	$query="UPDATE TNA_PROCESS_MST SET ready_to_approved=$app_status,approved=0 WHERE PO_NUMBER_ID=$po_id";
	$rID=execute_query($query,1);
	
	if($db_type==0)
	{
		if($rID)
		{
			mysql_query("COMMIT");
			echo "0**".str_replace("'","",$po_id);
		}
		else
		{
			mysql_query("ROLLBACK"); 
			echo "5**".str_replace("'","",$po_id);
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		if($rID)
		{
			oci_commit($con); 
			echo "0**".str_replace("'","",$po_id);
		}
		else
		{
			oci_rollback($con); 
			echo "5**".str_replace("'","",$po_id);
		}
	}
	
	disconnect($con);
	die;



}




if ($action=="save_update_delete_progress_comments")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$responsible_all_order=str_replace("'","",$responsible_all_order);
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$id=return_next_id( "id","tna_progress_comments", 1 ) ;
		
		$field_array_comments="id, job_id, order_id, tamplate_id, task_id, responsible, comments,mer_comments, inserted_by, insert_date, status_active, is_deleted";
		
		//$data_array_comments='';
		
		$orderid=str_replace("'","",$orderid);
		$job_all_po_arr=array();
		$job_all_po_arr[$orderid]=$orderid;
		if($responsible_all_order==1){
			$sql="select PO_NUMBER_ID from  TNA_PROCESS_MST WHERE JOB_NO=$jobno group by PO_NUMBER_ID";
			$sql_result=sql_select($sql);
			foreach($sql_result as $rows){
				$job_all_po_arr[$rows[PO_NUMBER_ID]]=$rows[PO_NUMBER_ID];
			}
		}
		
		 
		foreach($job_all_po_arr as $orderid){
			for($i=1;$i<=$tot_row; $i++)
			{
				$txtresponsible='txtresponsible_'.$i;
				$txtcomments='txtcomments_'.$i;
				$txtmercomments='txtmercomments_'.$i;
				$taskid='taskid_'.$i;
				//if($id=="") $sizeid=return_next_id( "id", "sample_development_size", 1 ); //else $sizeid=$sizeid+1;
				//$size_id=return_id( $$txtsizename, $size_arr, "lib_size", "id,size_name");
				
				if(str_replace("'","",$$txtcomments)!="" || str_replace("'","",$$txtmercomments)!="" || str_replace("'","",$$txtresponsible)!="")
				{
					
					if($data_array_comments!="") $data_array_comments.=",";
		
					$data_array_comments.="(".$id.",".$jobno.",".$orderid.",".$tamplateid.",".$$taskid.",".$$txtresponsible.",".$$txtcomments.",".$$txtmercomments.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					
					
					
					$id=$id+1;
				}
			}
		}
		
		//echo "insert into tna_progress_comments (".$field_array_comments.") Values ".$data_array_comments."";die;
		
		//echo $rIDs=sql_insert2("tna_progress_comments",$field_array_comments,$data_array_comments,1);
		
		$rIDs=sql_insert("tna_progress_comments",$field_array_comments,$data_array_comments,1);
		
		if($db_type==0)
		{
			if($rIDs)
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'","",$id)."**".str_replace("'","",$id)."**1";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**".str_replace("'","",$id)."**".str_replace("'","",$id)."**0";
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			
			//echo 	"shajjad_".$rIDs;die; job_no,po_id,template_id,tna_process_type
			//echo "0**".str_replace("'","",$id)."**".str_replace("'","",$job_no)."**".str_replace("'","",$po_id)."**".str_replace("'","",$template_id)."**".str_replace("'","",$tna_process_type)."**"."**1";
			
			if($rIDs)
			{
				oci_commit($con); 
				echo "0**".str_replace("'","",$id)."**".str_replace("'","",$id)."**"."**1";
			}
			else
			{
				oci_rollback($con); 
				echo "5**".str_replace("'","",$id)."**".str_replace("'","",$id)."**0";
			}
		}
		
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$id=return_next_id( "id","tna_progress_comments", 1 ) ;
		
		$field_array_comments="id, job_id, order_id, tamplate_id, task_id, responsible, comments,mer_comments, inserted_by, insert_date, status_active, is_deleted";
		
		$data_array_comments='';
		
		
		$orderid=str_replace("'","",$orderid);
		$job_all_po_arr=array();
		$job_all_po_arr[$orderid]=$orderid;
		if($responsible_all_order==1){
			$sql="select PO_NUMBER_ID from  TNA_PROCESS_MST WHERE JOB_NO=$jobno group by PO_NUMBER_ID";
			$sql_result=sql_select($sql);
			foreach($sql_result as $rows){
				$job_all_po_arr[$rows[PO_NUMBER_ID]]=$rows[PO_NUMBER_ID];
			}
		}
		
		//print_r($job_all_po_arr);die;
		
		foreach($job_all_po_arr as $orderid){
			for($i=1;$i<=$tot_row; $i++)
			{
				$txtresponsible='txtresponsible_'.$i;
				$txtcomments='txtcomments_'.$i;
				$txtmercomments='txtmercomments_'.$i;
				$taskid='taskid_'.$i;
				
				if(str_replace("'","",$$txtcomments)!="" || str_replace("'","",$$txtmercomments)!="" || str_replace("'","",$$txtresponsible)!="")
				{
					if($data_array_comments!="") $data_array_comments.=",";
		
					$data_array_comments.="(".$id.",".$jobno.",".$orderid.",".$tamplateid.",".$$taskid.",".$$txtresponsible.",".$$txtcomments.",".$$txtmercomments.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					
					$id=$id+1;
				}
			}
		}
		
		
		//$rID=execute_query("delete from tna_progress_comments where tamplate_id=$tamplateid and order_id=$orderid");
		$rID=execute_query("delete from tna_progress_comments where order_id in(".implode($job_all_po_arr).")");
		
		$rIDs=sql_insert("tna_progress_comments",$field_array_comments,$data_array_comments,1);
		
		
		if($db_type==0)
		{
			if( $rID && $rIDs )
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'","",$id)."**".str_replace("'","",$id)."**1";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**".str_replace("'","",$id)."**".str_replace("'","",$id)."**0";
			}
		}
		if($db_type==2 || $db_type==1)
		{
			if( $rID && $rIDs )
			{
				oci_commit($con); 
				echo "0**".str_replace("'","",$id)."**".str_replace("'","",$id)."**1";
			}
			
			else
			{
				oci_rollback($con); 
				echo "5**".str_replace("'","",$id)."**".str_replace("'","",$id)."**0";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="generate_report_file")
{
	foreach (glob("*.xls") as $filename) 
	{
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$tna_task_arr=return_library_array( "select task_name, task_short_name from lib_tna_task where task_type = 1",'task_name','task_short_name');
	
	$tna_task_id=array();
	$plan_start_array=array();
	$plan_finish_array=array();
	
	$actual_start_array=array();
	$actual_finish_array=array();
	
	$notice_start_array=array();
	$notice_finish_array=array();
	
	//$task_sql= sql_select("select a.task_number,a.task_start_date,a.task_finish_date,a.actual_start_date,a.actual_finish_date,a.notice_date_start,a.notice_date_end from tna_process_mst a, lib_tna_task b where a.task_number=b.task_name and a.template_id='$template_id' and a.po_number_id='$po_id' order by b.task_sequence_no asc");
	
	 $task_sql=sql_select("select a.task_number,a.task_start_date,a.task_finish_date,a.actual_start_date,a.actual_finish_date,a.notice_date_start,a.notice_date_end from tna_process_mst a, lib_tna_task b where a.task_type=1  AND b.task_type = 1 and a.task_number=b.task_name and a.po_number_id='$po_id' order by b.task_sequence_no asc");
	
	foreach ($task_sql as $row_task)
	{
		$tna_task_id[$row_task[csf("task_number")]]=$row_task[csf("task_number")];
		
		$plan_start_array[$row_task[csf("task_number")]] =$row_task[csf("task_start_date")];
		$plan_finish_array[$row_task[csf("task_number")]]=$row_task[csf("task_finish_date")];
		
		$actual_start_array[$row_task[csf("task_number")]] =$row_task[csf("actual_start_date")];
		$actual_finish_array[$row_task[csf("task_number")]]=$row_task[csf("actual_finish_date")];
		
		$notice_start_array[$row_task[csf("task_number")]] =$row_task[csf("notice_date_start")];
		$notice_finish_array[$row_task[csf("task_number")]]=$row_task[csf("notice_date_end")];
	} //var_dump($tna_task_id);die;
	
	//print_r($task_sql);
	//echo "<pre>";
	//print_r($actual_finish_array);
	
	//-----------------------------------------------------------------------------------------------
	
//$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
$color=return_library_array( "select a.po_break_down_id ,b.color_name, a.color_number_id from wo_po_color_size_breakdown a, lib_color b where a.color_number_id=b.id and a.po_break_down_id='".$po_id."' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.po_break_down_id ,b.color_name, a.color_number_id order by b.color_name", "color_number_id", "color_name" );
	
	
	$mer_comments_array=array();
			
			$data_array1=sql_select("select b.color_mst_id, b.color_number_id from  wo_po_break_down a, wo_po_color_size_breakdown b, wo_po_sample_approval_info c where a.job_no_mst=b.job_no_mst and b.job_no_mst=c.job_no_mst and a.id=b.po_break_down_id and b.po_break_down_id=c.po_break_down_id and  b.id=c.color_number_id  and a.id='$po_id' and b.color_mst_id !=0  and c.sample_type_id =7  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by a.id,b.id,c.current_status");   //group by c.id 
			if (count($data_array1)<=0)
			{
			$data_array1=sql_select("select b.color_mst_id, b.color_number_id from  wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.id='$po_id' and b.color_mst_id !=0 and a.id=b.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,a.po_number,b.color_mst_id, b.color_number_id order by a.id");
			}
			


			foreach ( $data_array1 as $row1)
			{
			
			//sample app.................................................................start
			$data_array_sample_table=sql_select("Select a.color_number_id,a.approval_status,a.sample_comments,b.sample_type from wo_po_sample_approval_info a,lib_sample b where a.sample_type_id=b.id and a.po_break_down_id='".$po_id."' and a.color_number_id ='".$row1[csf('color_mst_id')]."'");
				foreach ( $data_array_sample_table as $smp_row)
				{ 
					if ($smp_row[csf("sample_type")]==2) {
							if($smp_row[csf('approval_status')]==1){$smp_data[8].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
							if($smp_row[csf('approval_status')]==3){$smp_data[12].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
						 }
					else if ($smp_row[csf("sample_type")]==3) {
							if($smp_row[csf('approval_status')]==1){$smp_data[7].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
							if($smp_row[csf('approval_status')]==3){$smp_data[13].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
					}
					else if ($smp_row[csf("sample_type")]==4) {
							if($smp_row[csf('approval_status')]==1){$smp_data[14].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
							if($smp_row[csf('approval_status')]==3){$smp_data[15].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
					}
					else if ($smp_row[csf("sample_type")]==7) {
							if($smp_row[csf('approval_status')]==1){$smp_data[16].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
							if($smp_row[csf('approval_status')]==3){$smp_data[17].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
					}
					else if ($smp_row[csf("sample_type")]==8) { 
							if($smp_row[csf('approval_status')]==1){$smp_data[21].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
							if($smp_row[csf('approval_status')]==3){$smp_data[22].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
					}
					else if ($smp_row[csf("sample_type")]==9) {
							if($smp_row[csf('approval_status')]==1){$smp_data[23].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
							if($smp_row[csf('approval_status')]==3){$smp_data[24].=$color[$row1[csf('color_number_id')]].': '.$smp_row[csf('sample_comments')].',';}
					}

				}
			//sample app.................................................................end


			//lapdip app..................................................................start	
			$data_array_sample_table=sql_select("Select color_name_id,approval_status,lapdip_comments from wo_po_lapdip_approval_info where  po_break_down_id='".$po_id."'");
				foreach ( $data_array_sample_table as $smp_row)
				{ 
					if($smp_row[csf('approval_status')]==1){$smp_data[9].=$color[$smp_row[csf('color_name_id')]].': '.$smp_row[csf('lapdip_comments')].',';}
					if($smp_row[csf('approval_status')]==3){$smp_data[10].=$color[$smp_row[csf('color_name_id')]].': '.$smp_row[csf('lapdip_comments')].',';}
				
				}
		//lapdip app.........................................................end	
		
		
		//embell app..........................................................start	
			$data_array_sample_table=sql_select("Select color_name_id,approval_status,embellishment_comments from wo_po_embell_approval where po_break_down_id='".$po_id."'");
				foreach ( $data_array_sample_table as $smp_row)
				{ 
					if($smp_row[csf('approval_status')]==1){$smp_data[19].=$color[$smp_row[csf('color_name_id')]].': '.$smp_row[csf('embellishment_comments')].',';}
					if($smp_row[csf('approval_status')]==3){$smp_data[20].=$color[$smp_row[csf('color_name_id')]].': '.$smp_row[csf('embellishment_comments')].',';}
				
				}
		//embell app..........................................................end	


		//Trims app..........................................................start	
			$data_array_sample_table=sql_select("Select approval_status,accessories_comments from wo_po_trims_approval_info where po_break_down_id='".$po_id."'");
				foreach ( $data_array_sample_table as $smp_row)
				{ 
					if($smp_row[csf('approval_status')]==1){$smp_data[25].=$smp_row[csf('accessories_comments')].',';}
					if($smp_row[csf('approval_status')]==3){$smp_data[11].=$smp_row[csf('accessories_comments')].',';}
				}


					
				
			}
//----------------------------------------------------------------------------------------

	
	
	$lead_time_array=return_library_array("select task_template_id,lead_time from tna_task_template_details  where task_type=1 and is_deleted=0 and status_active=1 group by lead_time","task_template_id",'lead_time');
	
	$comments_array=array();
	$responsible_array=array();
	
	//$res_comm_sql= sql_select("select task_id, comments, responsible from tna_progress_comments where tamplate_id='$template_id' and order_id='$po_id'");
	//echo "select task_id, comments, responsible from tna_progress_comments where order_id='$po_id'";
	$res_comm_sql=sql_select("select task_id, comments, responsible from tna_progress_comments where order_id='$po_id'");
	
	foreach ($res_comm_sql as $row_res_comm)
	{
		$comments_array[$row_res_comm[csf("task_id")]] =$row_res_comm[csf("comments")];
		$responsible_array[$row_res_comm[csf("task_id")]]=$row_res_comm[csf("responsible")];
	}
	
	
	$execution_time_array=array();
	
	//$execution_time_sql= sql_select("select tna_task_id, execution_days from tna_task_template_details where task_template_id='$template_id'");
	
	$execution_time_sql= sql_select("select tna_task_id, execution_days from tna_task_template_details  where task_type=1 and is_deleted=0 and status_active=1");
	foreach ($execution_time_sql as $row_execution_time)
	{
		$execution_time_array[$row_execution_time[csf("tna_task_id")]] =$row_execution_time[csf("execution_days")];
	}
	
	//$upid_sql= sql_select("select min(id) as id from tna_progress_comments where tamplate_id='$template_id' and order_id='$po_id'");
	
	$upid_sql= sql_select("select min(id) as id from tna_progress_comments where order_id='$po_id'");
	foreach ($upid_sql as $row_upid)
	{
		$id_up=$row_upid[csf("id")];
	}
	
	$lead_time=return_library_array("select task_template_id,lead_time from tna_task_template_details  where task_type=1 and is_deleted=0 and status_active=1 group by task_template_id,lead_time","task_template_id","lead_time");
	
	$html="<table width='1000' border='1' rules='all' class='rpt_table'>
    	<tr><td colspan='6' align='center'><b><font size='+1'>TNA Progress Comment</font></b></td></tr>
    </table>";
	$html.="<table width='1000' border='1' rules='all' class='rpt_table'>";
	$sql ="select b.company_name,b.buyer_name,a.po_number,b.job_no,b.style_ref_no,b.gmts_item_id,a.po_received_date,a.shipment_date from  wo_po_break_down a,wo_po_details_master b where a.id=$po_id and a.job_no_mst=b.job_no";
	$result=sql_select($sql);
	foreach($result as $row)
	{
		
    	$html.="<tr>
        	<td width='130'>Company</td>
            <td width='176'>".$company_library[$row[csf('company_name')]]."  </td>
            <td width='130'>Buyer</td>
            <td width='176'> ".$buyer_arr[$row[csf('buyer_name')]]."</td>
            <td width='130'>Order No</td>
           	<td width='176'>  ".$row[csf('po_number')]." </td>
        </tr>
        <tr>
        	<td width='130'>Style Ref.</td>
            <td width='176'> ".$row[csf('style_ref_no')]."</td>
            <td width='130'>RMG Item</td>
            <td width='176'>".$garments_item[$row[csf('gmts_item_id')]]."  </td>
            <td width='130'>Order Recv. Date</td>
           	<td width='176'> ".change_date_format($row[csf('po_received_date')])."</td>
        </tr>
        <tr>
        	<td width='130'>Ship Date</td>
            <td width='176'> ".change_date_format($row[csf('shipment_date')])."</td>
            <td width='130'>Lead Time</td>
            <td width='176'>";
				
					if($tna_process_type==1)
					{
						$lead_timee=$lead_time_array[$template_id];
					}
					else
					{
						$lead_timee=$template_id;
					}
                   
					 $lead_timee;
               
           $html.="</td>
            <td width='130'>Job Number</td>
           	<td width='176'>
            	". $row[csf('job_no')]."
            </td>
        </tr>";
       
		}
	 $html.="</table>";	
	 
	 	 $html.="<table><tr height='10'><td colspan='6'>&nbsp;</td></tr></table>";
     $html.="<table style='width: 1130px;'>
        <tr>
            <td>
                <div style='width: 1120px;font-size:12px;'>
                <table width='1100' border='1' rules='all' class='rpt_table'>
                    <thead>
                    	<tr align='center'>
                            <th width='30'>Task No</th>
                            <th width='150'>Task Name</th>
                            <th width='60'>Allowed Days</th>
                            <th width='70'>Plan Start Date</th>
                            <th width='70'>Plan Finish Date</th>
                            <th width='70'>Actual Start Date</th>
                            <th width='70'>Actual Finish Date</th>
                            <th width='70'>Start Delay/ Early By</th>
                            <th width='70'>Finish Delay/ Early By</th>
                            <th width='150'>Responsible</th>
                            <th width='120'>Comments</th>
                            <th width=''>Mer. Comments</th>
                        </tr>
                    </thead>
                </table>
                </div>
            </td>
        </tr>
    </table> ";
	$html.="
    <table style='width:1130px;'>
        <tr>
            <td>    
                <div style='width: 1120px;overflow-y: scroll; max-height:180px;font-size:12px;' id='scroll_body2'>
                <table width='1100px' border='1' rules='all' class='rpt_table' id='comments_tbl'>
                	<tbody>";
					
                        $i=0;
						
						
						
                        foreach($tna_task_id as $key)
                        {
                            $i++;
                            
                            if ($i%2==0)  
                                $trcolor='#E9F3FF';
                            else
                                $trcolor='#FFFFFF';	
								
								
						//$new_data : 0->actual_start_date, 1->actual_finish_date, 2->task_start_date, 3->task_finish_date, 4->notice_date_start, 5->notice_date_end
						
							$bgcolor1=''; $bgcolor='';
									
							if ($plan_start_array[$key]!=$blank_date) 
							{
								if (strtotime($notice_start_array[$key])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($plan_start_array[$key]))  $bgcolor="#FFFF00";
								else if (strtotime($plan_start_array[$key])<strtotime(date("Y-m-d",time())))  $bgcolor='#FF0000';
								else $bgcolor='';
								
							}
							 
							if ($plan_finish_array[$key]!=$blank_date) {
								if (strtotime($notice_finish_array[$key])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($plan_finish_array[$key]))  $bgcolor1="#FFFF00";
								else if (strtotime($plan_finish_array[$key])<strtotime(date("Y-m-d",time())))  $bgcolor1="#FF0000"; else $bgcolor1="";
							}
							
							if ($actual_start_array[$key]!=$blank_date) $bgcolor="";
							if ($actual_finish_array[$key]!=$blank_date) $bgcolor1="";
							
							// Delay / Early............
									
							$bgcolor5=""; $bgcolor6="";
							$delay=""; $early="";
							
							if($actual_start_array[$key]!=$blank_date)
							{
								$start_diff1 = datediff( "d", $actual_start_array[$key], $plan_start_array[$key]);
								$finish_diff1 = datediff( "d", $actual_finish_array[$key], $plan_finish_array[$key]);
								
								$start_diff=$start_diff1-1;
								$finish_diff=$finish_diff1-1;
								
								if($start_diff<0)
								{
									$bgcolor5='#2A9FFF';	//Blue
									$start='(Delay)';
								}
								if($start_diff>0)
								{
									$bgcolor5='';
									$start='(Early)';
									
								}
								if($finish_diff<0)
								{
									$bgcolor6='#2A9FFF';
									$finish='(Delay)';
								}
								if($finish_diff>0)
								{	
									$bgcolor6='';
									$finish='(Early)';
								}
								
								
							}
							else
							{
								if(date('Y-m-d')>$plan_start_array[$key])
								{
									$start_diff1 = datediff( "d", $plan_start_array[$key], date("Y-m-d"));
									$start_diff=$start_diff1-1;
									$bgcolor5="#FF0000";		//Red
									$start='(Delay)';
								}
								if(date("Y-m-d")>$plan_finish_array[$key])
								{
									$finish_diff1 = datediff( "d", $plan_finish_array[$key], date("Y-m-d"));
									$finish_diff=$finish_diff1-1;
									$bgcolor6="#FF0000";
									$finish='(Delay)';
								}
								if(date("Y-m-d")<=$plan_start_array[$key])
								{
									$start_diff = "";
									$bgcolor5="";
									$start="";
									//$start="(Ac. Start Dt. Not Found)";
								}
								if(date("Y-m-d")<=$plan_finish_array[$key])
								{
									$finish_diff = "";
									$bgcolor6="";
									$finish="";
									//$finish="(Ac. Finish Dt. Not Found)";
									
								}
							}
							$html.="<tr bgcolor='$trcolor'>
                            <td align='center' width='30'> $i</td>
                            <td width='150'> ".$tna_task_arr[$key]."</td>
                            <td align='center' width='60'>".$execution_time_array[$key]."</td>
                            <td align='center' width='70'>".change_date_format($plan_start_array[$key])."</td>
                            <td align='center' width='70'>".change_date_format($plan_finish_array[$key])."</td>
                            <td align='center' width='70' bgcolor=".$bgcolor.">";
								
                                    if($db_type==0)
                                    {
                                        if($actual_start_array[$key]=="0000-00-00")  '';
                                        else   change_date_format($actual_start_array[$key]);
                                    }
                                    else
                                    {
                                        if($actual_start_array[$key]=="")  '';
                                        else   change_date_format($actual_start_array[$key]);
                                    }
                               
                          $html.="</td>
                            <td align='center' width='70' bgcolor=".$bgcolor1.">";
								  
                                    if($db_type==0)
                                    {
                                        if($actual_finish_array[$key]=="0000-00-00")  '';
                                        else   change_date_format($actual_finish_array[$key]);
                                    }
                                    else
                                    {
                                        if($actual_finish_array[$key]=="")  '';
                                        else   change_date_format($actual_finish_array[$key]);
                                    } 
                               
                           $html.="</td>
                            <td align='center' width='70' bgcolor=".$bgcolor5.">
								  
                                     $start_diff  $start
                                
                            </td>
                            <td align='center' width='70' bgcolor=".$bgcolor6.">
                                 
                                     $finish_diff  $finish
                               
                            </td>
                            <td width='150'>
                            	".$responsible_array[$key]."
                            	
                            </td>
                            <td width='120' align='center'>".$comments_array[$key]."</td>
                            <td align='center'>  ".substr($smp_data[$key],0,-1)."</td>
                        </tr>";
                       
                        }
                        
                    $html.="</tbody>
                </table>
                </div>
    		</td>
        </tr>
    </table>";
	
	$filename=time().".xls";
	$create_new_doc = fopen($filename, 'w');	
	if(fwrite($create_new_doc,$html))
		echo $filename;
	else
		echo 0;
}



if($action=="generate_style_report_with_graph")
{

	if($graph==1){
		if($db_type==0)
		{
			$txt_date_from = date("'Y-m-d'",strtotime($txt_date_from));
			$txt_date_to = date("'Y-m-d'",strtotime($txt_date_to));
		}
		else
		{
			$txt_date_from = date("'d-M-Y'",strtotime($txt_date_from));
			$txt_date_to = date("'d-M-Y'",strtotime($txt_date_to));
		}
	}
$buyer_short_name_arr = return_library_array("SELECT short_name,id FROM  lib_buyer WHERE is_deleted = 0 and status_active=1 order by id asc","id","short_name");

$actual_manual_update_task_arr=return_library_array("select task_id,task_id from tna_manual_permission where is_actual_manual=1  and company_id=$cbo_company_name","task_id","task_id");
$plan_manual_update_task_arr=return_library_array("select task_id,task_id from tna_manual_permission where is_plan_manual=1  and company_id=$cbo_company_name","task_id","task_id");

	$mod_sql= sql_select("select a.id,a.task_catagory,a.task_name,a.task_short_name,a.task_type,a.completion_percent,a.task_sequence_no,b.task_template_id,b.lead_time ,b.tna_task_id
	from lib_tna_task a,tna_task_template_details b where a.task_name=b.tna_task_id and b.task_type=1  AND b.task_type = 1 and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 order by a.task_sequence_no asc");
	
	
	
	$taskIdArr=array();
	$tna_task_array=array();
	$tna_task_id=array();
	$tna_task_cat=array();
	$tna_task_name_arr=array();
	foreach ($mod_sql as $row)
	{
		$taskIdArr[$row[csf("task_name")]]=$row[csf("task_name")]*1;
		
		$tna_task_id[$row[csf("task_name")]]=$row[csf("task_name")];
		$tna_task_array[$row[csf("id")]] =$row[csf("task_short_name")];
		$tna_task_name_array[$row[csf("id")]] =$tna_task_name[$row[csf("task_name")]];
		$tna_task_cat[$row[csf("id")]]=$row[csf("task_catagory")];
		$tna_task_name_arr[$row[csf("id")]]=$row[csf("task_name")];
		$lead_time_array[$row[csf("task_template_id")]]=$row[csf("lead_time")];
		$tast_tmp_id_arr[$row[csf("task_template_id")]][$row[csf("tna_task_id")]]=$row[csf("tna_task_id")];
	
		$task_short_name_array[$row[csf("task_name")]] =$row[csf("task_short_name")];
	
	
	}
	//print_r($lead_time_array);die;
	
	$cbo_company_id=$cbo_company_name;
	
	$order_status_cond="";
	if(str_replace("'","",$cbo_order_status)>0) $order_status_cond=" and b.is_confirmed=$cbo_order_status";
	
	if(str_replace("'","",$cbo_company_name)==0) $cbo_company_name=""; else $cbo_company_name=" and a.company_name = $cbo_company_name";
	if(str_replace("'","",$cbo_buyer_name)==0) $cbo_buyer_name=""; else $cbo_buyer_name=" and a.buyer_name = $cbo_buyer_name";
	if(str_replace("'","",$cbo_team_member)==0) $cbo_team_member=""; else $cbo_team_member=" and a.dealing_marchant = $cbo_team_member";
	
	if(str_replace("'","",$cbo_search_type)==1){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
	}
	else if(str_replace("'","",$cbo_search_type)==3){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and c.country_ship_date between $txt_date_from and $txt_date_to";
	}
	else if(str_replace("'","",$cbo_search_type)==4){
		if($db_type==0)
		{
			if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)==""){$date_range="";}else{ 
			$date_range=" and b.insert_date between ".$txt_date_from." and ".$txt_date_to."";}
		}
		else
		{
			
			if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)==""){$date_range="";}else{ 
			$date_range=" and b.insert_date between ".$txt_date_from." and '".str_replace("'","",$txt_date_to)." 11:59:59 PM'";}
		}
	}
	else
	{
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.po_received_date between $txt_date_from and $txt_date_to";
	}
	
	

	
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if($txt_job_no=="") $txt_job_no=""; else $txt_job_no=" and a.job_no_prefix_num ='$txt_job_no'";
	$txt_order_no=str_replace("'","",$txt_order_no);
	if($txt_order_no=="") $txt_order_no=""; else $txt_order_no=" and b.po_number ='$txt_order_no'";
	$txt_file_no=str_replace("'","",$txt_file_no);
	if($txt_file_no=="") $file_cond=""; else $file_cond=" and b.file_no ='$txt_file_no'";
	$txt_int_ref_no=str_replace("'","",$txt_int_ref_no);
	if($txt_int_ref_no=="") $ref_cond=""; else $ref_cond=" and b.grouping ='$txt_int_ref_no'";
	
	$txt_style_ref_no=str_replace("'","",$txt_style_ref_no);
	if($txt_style_ref_no=="") $txt_style_ref_no=""; else $txt_style_ref_no=" and a.style_ref_no ='$txt_style_ref_no'";
	//**txt_date_from*txt_date_to*txt_job_no
	$shipment_status_con="";
	if(str_replace("'","",$cbo_shipment_status)==3) $shipment_status_con=" and b.shiping_status=$cbo_shipment_status";
	// else {$shipment_status_con=" and b.shiping_status !=3";} //as per Saeed
	
	
	
	
	
	$tna_all_task=implode(",",$tna_task_id);
	
	if(str_replace("'","",$cbo_search_type)==3)
	{
		$sql = "SELECT a.team_leader,a.factory_marchant,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, b.id ,b.po_number, b.file_no, b.grouping as in_ref_no,b.po_quantity
		FROM  wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c 
		WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no  and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 $order_status_cond $file_cond $ref_cond";
	}
	else
	{
		$sql = "SELECT a.team_leader,a.factory_marchant,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, b.id, b.po_number, b.file_no, b.grouping as in_ref_no ,b.po_quantity
		FROM  wo_po_details_master a,  wo_po_break_down b 
		WHERE a.job_no=b.job_no_mst $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no  and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 $order_status_cond  $file_cond $ref_cond"; 
	}
	
  //echo $sql; 
	$result = sql_select( $sql ) ;
	$wo_po_details_master = array();
	$po_no_arr=array();
	$job_no_arr=array();
	foreach( $result as  $row ) 
	{	
		$wo_po_details_master[$row[csf('id')]]['company_name']=$row[csf('company_name')];
		$po_no_arr[]=$row[csf('id')];
		$job_no_arr[]=$row[csf('job_no')];
		

		$wo_po_details_master[$row[csf('job_no')]]['po_number'][$row[csf('po_number')]]= $row[csf('po_number')];
		$wo_po_details_master[$row[csf('job_no')]]['set_smv']= $row[csf('set_smv')];
		$wo_po_details_master[$row[csf('job_no')]]['file_no']= $row[csf('file_no')];

		$wo_po_details_master[$row[csf('job_no')]]['dealing_marchant']=$row[csf('dealing_marchant')];
		$wo_po_details_master[$row[csf('job_no')]]['factory_marchant']=$row[csf('factory_marchant')];
		$wo_po_details_master[$row[csf('job_no')]]['team_leader']=$row[csf('team_leader')];
		$wo_po_details_master[$row[csf('job_no')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
		$wo_po_details_master[$row[csf('job_no')]]['in_ref_no']= $row[csf('in_ref_no')];
		$wo_po_details_master[$row[csf('job_no')]]['buyer_name']=$row[csf('buyer_name')];
		$wo_po_details_master[$row[csf('job_no')]]['style_ref_no']=$row[csf('style_ref_no')];
		$wo_po_details_master[$row[csf('job_no')]]['po_quantity']+=$row[csf('po_quantity')];
	}
	
 
	$result = sql_select("select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name" ) ;
	$team_leader_name = array();
	foreach( $result as  $row ) 
	{	
		$team_leader_name[$row[csf('id')]]=$row[csf('team_leader_name')];
	}

 
	$sql = "SELECT team_member_name,id FROM lib_mkt_team_member_info WHERE is_deleted = 0 and status_active=1 order by id asc";
	$result = sql_select( $sql ) ;
	$team_member_name = array();
	foreach( $result as  $row ) 
	{	
		$team_member_name[$row[csf('id')]]=$row[csf('team_member_name')];
	}
	
	$sql = "SELECT buyer_name,id FROM  lib_buyer WHERE is_deleted = 0 and status_active=1 order by id asc";
	$result = sql_select( $sql ) ;
	$buyer_name = array();
	foreach( $result as  $row ) 
	{	
		$buyer_name[$row[csf('id')]]=$row[csf('buyer_name')];
	}
	
	
	$po_no_arr_all=implode(",",$po_no_arr); if($po_no_arr_all!="") $po_no_arr_all .=",0"; else $po_no_arr_all .="0"; 
	$job_no_all="'".implode("','",$job_no_arr)."'";
	$c=count($tna_task_id);
	
	if($db_type==0)
	{
		$sql ="select a.id,a.task_number,a.po_number_id, a.job_no, a.shipment_date,min(b.pub_shipment_date) as pub_shipment_date, a.template_id, a.po_receive_date,b.insert_date,";
		$i=1;
	
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c) $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id, ";
			else $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id ";
			$i++;
		}
		
		$sql .=" from tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id and a.po_number_id in( $po_no_arr_all ) and a.job_no in ($job_no_all) $shipment_status_con and b.status_active=1  and b.po_quantity>0 $order_status_cond and a.task_type=1 group by a.po_number_id,a.job_no,b.insert_date order by a.shipment_date,a.po_number_id,a.job_no"; 
	}
	else
	{
		$sql ="select a.task_number,  
		LISTAGG(cast(a.id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as id, 
		LISTAGG(cast(a.po_number_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as po_number_id, 
		LISTAGG(cast(a.template_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as template_id, 
		a.job_no, min(a.shipment_date) as shipment_date,min(b.pub_shipment_date) as pub_shipment_date,min(a.po_receive_date) as po_receive_date,min(b.insert_date) as insert_date,";
		
		$sql .="(min(a.actual_start_date) || '_' || min(a.task_start_date) || '_' || min(a.actual_finish_date) || '_' || min(a.task_finish_date) || '_' || min(a.plan_start_flag) || '_' || min(a.plan_finish_flag) || '_' || min(a.notice_date_start) || '_' || min(a.notice_date_end) || '_' || LISTAGG(cast(a.id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) ) as status ";
			
			
		//------------------
			$sql_order_con='';
			$po_no_arr_all=explode(',',$po_no_arr_all);
			$chunk_po_no_arr_all=array_chunk(array_unique($po_no_arr_all),999);
			$p=1;
			foreach($chunk_po_no_arr_all as $rlz_sub_id)
			{
				if($p==1) $sql_order_con .=" and (a.po_number_id in(".implode(',',$rlz_sub_id).")"; else $sql_sub_lc .=" or a.po_number_id in(".implode(',',$rlz_sub_id).")";
				$p++;
			}
			$sql_order_con .=" )";
			
			$sql_job_con='';
			$job_no_all=explode(',',$job_no_all);
			$chunk_job_no_all=array_chunk(array_unique($job_no_all),999);
			$q=1;
			foreach($chunk_job_no_all as $rlz_sub_id)
			{
				if($q==1) $sql_job_con .=" and (a.job_no in(".implode(',',$rlz_sub_id).")"; else $sql_sub_lc .=" or a.job_no in(".implode(',',$rlz_sub_id).")";
				$p++;
			}
			$sql_job_con .=" )";
			
			
		//-------------------------------
		$sql .=" from  tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id $sql_order_con $sql_job_con $shipment_status_con and b.status_active=1 and b.po_quantity>0 $order_status_cond  and a.task_type=1  group by a.job_no,a.task_number order by a.job_no"; 
	}
	$data_sql_2= sql_select($sql);
	    //echo $sql;die;
		
		foreach($data_sql_2 as $row){
			$dataArr[$row[csf('job_no')]][$row[csf('task_number')]]=$row;
			$data_sql[$row[csf('job_no')]]=$row;
			$job_no=$row[csf('job_no')];
			$shipDate=$row[csf('pub_shipment_date')];
		}

		$image_location_arr=return_library_array("select id,image_location from common_photo_library   where form_name='knit_order_entry' and master_tble_id='$job_no'","id","image_location");
	
	if(count($data_sql_2)==0){?>
			<h3 style="text-align:center; color:#F00">Data Not Found</h3>
		<?
			exit();
		}
	
	
	
	ob_start();
	
	?>
  <div style="margin-left:10px;">
  <table width="900" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
  	<thead>
    <tr>
        <th>Buyer Name</th>
        <th>Job No.</th>
        <th>Style Ref.</th>
        <th>PO Number</th>
        <th width="80">PO Qty.</th>
        <th>Shipment Date</th>
        <th width="<?= count($image_location_arr)*46;?>">Image</th>
        <td onMouseOver="blanking('complete')" align="center"><span style="background:#00FF00; padding:0 6px; border-radius:9px; cursor:pointer;" title="green">&nbsp;</span></td>
        <td width="100">Task Complete</td>
  </tr>
  </thead>
  <tr>
        <td rowspan="3" align="center" valign="middle"><?= $buyer_short_name_arr[$wo_po_details_master[$job_no]['buyer_name']];?></td>
        <td rowspan="3" align="center" valign="middle"><a href="javascript:generate_bom('preCostRpt2','<?= $job_no;?>',<?= $cbo_company_id;?>,'<?= $wo_po_details_master[$job_no]['buyer_name'];?>','<?= $wo_po_details_master[$job_no]['style_ref_no'];?>','')"><?= $job_no;?></a></td>
        <td rowspan="3" align="center" valign="middle"><?= $wo_po_details_master[$job_no]['style_ref_no'];?></td>
        <td rowspan="3" align="center" valign="middle"><p><?= implode(',',$wo_po_details_master[$job_no]['po_number']);?></p></td>
        <td rowspan="3" align="center" valign="middle"><?= $wo_po_details_master[$job_no]['po_quantity'];?></td>
        <td rowspan="3" align="center" valign="middle"><?= change_date_format($shipDate);?></td>
        <td rowspan="3" align="center" valign="middle">
        	<? foreach($image_location_arr as $img_path){ ?>
            <img class="zoom" src="../../<?= $img_path;?>" width="40" style="float:left; border:1px solid #999; margin:1px;" />
            <? } ?>
        </td>
        <td onMouseOver="blanking('partially')" align="center" valign="middle"><span style="background:yellow; padding:0 6px; border-radius:9px; cursor:pointer;" title="yellow">&nbsp;</span></td>
        <td valign="middle">Partially Complete</td>
  </tr>
   <tr>
        <td onMouseOver="blanking('notStarted')" align="center"><span style="background:#FF0000; padding:0 6px; border-radius:9px; cursor:pointer;" title="Red">&nbsp;</span></td>
        <td>Task Not Started</td>
  </tr>
   <tr>
        <td onMouseOver="blanking('done_in_late')" align="center"><span style="background:#2A9FFF; padding:0 6px; border-radius:9px; cursor:pointer;" title="Blue">&nbsp;</span></td>
        <td>Done In Late</td>
  </tr>
  </table>
  </div>  
<?
  
error_reporting(0);
$dataShowParLine=9;
$taskChunkArr=array_chunk($taskIdArr,$dataShowParLine);
$taskNameArr=$task_short_name_array;



$width=130;	
$i=0;$s=0;
$html='';

echo '<div style="width:'.(($width*$dataShowParLine)+125).'px; border:1px solid #CCC;padding:5px 15px; margin:5px 10px;">';
foreach($taskChunkArr as $row){
	
	$actual_start_date=array();
	$task_start_date=array();
	$actual_finish_date=array();
	$task_finish_date=array();
	$plan_start_flag=array();
	$plan_finish_flag=array();
	$notice_date_start=array();
	$notice_date_end=array();
	$mst_id=array();
	
	foreach($row as $key=>$task_Id){
	list($actual_start_date[$key],$task_start_date[$key],$actual_finish_date[$key],$task_finish_date[$key],$plan_start_flag[$key],$plan_finish_flag[$key],$notice_date_start[$key],$notice_date_end[$key],$mst_id[$key])=explode("_",$dataArr[$job_no][$task_Id]['STATUS']);
	
}
	
	$activeIncativeClass=array();
	$containClass=array();
	$titleClass=array();
	
	for($i8=0;$i8<$dataShowParLine;$i8++){
		if($actual_start_date[$i8] && empty($actual_finish_date[$i8]) && $row[$i8]){$activeIncativeClass[$i8]="s_active partially";}
		if($actual_finish_date[$i8] && $row[$i8]){$activeIncativeClass[$i8]="s_active complete";}
		if(empty($actual_start_date[$i8]) && $row[$i8]){$activeIncativeClass[$i8]="s_active notStarted";}
		
		if($actual_finish_date[$i8] && $actual_start_date[$i8]){
			$done_day = datediff( "d", $task_finish_date[$i8], $actual_finish_date[$i8]);
			if($done_day>1){$activeIncativeClass[$i8]="s_active done_in_late";}
		}

		if(empty($row[$i8])){$containClass[$i8]="displayNone";}
		if(empty($row[$i8])){$titleClass[$i8]="displayNone";}
		
	}

	
	
	
	$i++;$s++;
	if($i==4){$i=2;}
	
	if($i==1){
		//$html.=$htmlArr['head'];
		 echo '
				<table id="s_graph" border="1" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<th width="60"><div class="g_contain"><div class="g_title bg-0">&nbsp; </div><div class="g_bar">&nbsp;</div><div class="g_box pointer">&nbsp; <b>&nbsp;</b></div></div></th>';
					
					for($i1=0;$i1<$dataShowParLine;$i1++){
					$bgcolor=($i1%2==0)?"bg-0":"bg-1";
					$opupFunction='<a href="javascript:fnTaskPopup(\''.$row[$i1].'*'.$job_no.'\')">'.$taskNameArr[$row[$i1]].'</a>';
					echo '<th width="'.$width.'"><div class="g_contain"><div class="g_title '.$bgcolor.'">'.$opupFunction.'</div><div class="g_bar">|</div><div class="g_box"><i class="'.$activeIncativeClass[$i1].'"></i></div></div></th>';
					}
					echo'<th rowspan="2" align="left" valign="bottom" width="60"><div class="right">&nbsp;</div></th>
			  </tr>';
	
	}	
	else if($i==2 && count($taskChunkArr)!=$s){
		//$html.=$htmlArr['left'];
		 
		echo '<tr>
				<th valign="bottom" rowspan="2" align="right" valign="middle"><div class="left">&nbsp;</div></th>';
				
				for($i5=($dataShowParLine-1);$i5>=0;$i5--){
				$opupFunction='<a href="javascript:fnTaskPopup(\''.$row[$i5].'*'.$job_no.'\')">'.$taskNameArr[$row[$i5]].'</a>';
				$bgcolor=($i5%2==0)?"bg-0":"bg-1";
				echo '<th><div class="g_contain"><div class="g_title  '.$bgcolor.'">'.$opupFunction.'</div> <div class="g_bar">|</div><div class="g_box"><i class="'.$activeIncativeClass[$i5].'"></i></div></div></th>';
				}
				
				
			echo '</tr>';
	}	
	else if($i==3 && count($taskChunkArr)!=$s){
		//$html.=$htmlArr['right'];
		 echo '<tr>';
				for($i3=0;$i3<$dataShowParLine;$i3++){
				$bgcolor=($i3%2==0)?"bg-0":"bg-1";
				$opupFunction='<a href="javascript:fnTaskPopup(\''.$row[$i3].'*'.$job_no.'\')">'.$taskNameArr[$row[$i3]].'</a>';
				
				echo '<th><div class="g_contain"><div class="g_title  '.$bgcolor.'">'.$opupFunction.'</div> <div class="g_bar">|</div><div class="g_box"><i class="'.$activeIncativeClass[$i3].'"></i></div></div></th>';
				}
				echo '<th valign="bottom" rowspan="2" align="left" valign="middle"><div class="right">&nbsp;</div></th>
			</tr>';
	
	}	
	else if(count($taskChunkArr)==$s){
		//$html.=$htmlArr['footer'];
		 if($i==3){
		 echo '<tr>';
				for($i4=0;$i4<$dataShowParLine;$i4++){
				$bgcolor=($i4%2==0)?"bg-0":"bg-1";
				$opupFunction='<a href="javascript:fnTaskPopup(\''.$row[$i4].'*'.$job_no.'\')">'.$taskNameArr[$row[$i4]].'</a>';
				echo '<th><div class="g_contain"><div class="g_title '.$bgcolor.' '.$titleClass[$i4].'">'.$opupFunction.'</div> <div class="g_bar '.$titleClass[$i4].'">|</div><div class="g_box"><i class="'.$activeIncativeClass[$i4].'"></i></div></div></th>';
				}
				echo '<th valign="bottom"><div class="g_contain"> <div class="g_bar">&nbsp;</div><div class="g_box right_end">&nbsp; <b>&nbsp;</b></div></div></th>
			</tr>
			</table>
			';
		 }
		 else{
		 echo '<tr>
				<th valign="bottom"><div class="g_contain">&nbsp; <div class="g_box left_end">&nbsp; <b>&nbsp;</b></div></div></th>';
				for($i6=($dataShowParLine-1);$i6>=0;$i6--){
					$opupFunction='<a href="javascript:fnTaskPopup(\''.$row[$i6].'*'.$job_no.'\')">'.$taskNameArr[$row[$i6]].'</a>';
					$bgcolor=($i6%2==0)?"bg-0":"bg-1";
				echo '<th><div class="g_contain"><div class="g_title '.$bgcolor.' '.$titleClass[$i6].'">'.$opupFunction.'</div><div class="g_bar '.$titleClass[$i6].'">|</div><div class="g_box"><i class="'.$activeIncativeClass[$i6].'"></i></div></div></th>';
				}
				
				
			echo '</tr>
			</table>
			';
		 }
		
	}
}
echo "</div>";
?>

<style>
#s_graph td,#s_graph th{border:none; text-align:center;}

:root{
	--blue:#2962FF;
}

.g_contain{
	margin: 0 0 15px;
	
}


.g_title{
	height:25px;
	text-align:center;
	vertical-align:middle;
	margin:0;
	padding:3px 0 0 0;
	line-height:12px;
}

.bg-1{background:#C5D9F1;}
.bg-0{background:#FCD5B4;}

.g_bar{
	color:#013E73;
	font-size:36px;
	text-align:center; 
	height:10px;
	margin:0;
	line-height:8px;
	overflow:hidden;
	}




.g_box{
	background:#013E73;
	padding:5px;
	height:18px;
}

.g_box b{
	color:#FFF;
	font-size:26px;
	line-height:12px;
}

.s_active{
	border-radius:50%;padding:1px 8px;
	box-shadow: 0 0 3px #FFF;
}

.complete{ 
	background:#00FF00; border:1px solid #00FF00;
}

.done_in_late{ 
	background:#2A9FFF; border:1px solid #2A9FFF;
}


.notStarted{ 
	background:#FF0000; border:1px solid #FF0000;
}
.partially{background:#FF0; border:1px solid #FF0;
}


.right{
	border-top:28px solid #013E73;
	border-right:30px solid #013E73;
	border-bottom:28px solid #013E73;
	border-radius:0 50px 50px 0;
	height:53px;
	margin: 0 0 15px;
	
}


.left{
	border-top:28px solid #013E73;
	border-left:30px solid #013E73;
	border-bottom:28px solid #013E73;
	border-radius:50px 0 0 50px;
	height:53px;
	margin: 0 0 15px;
}


.displayNone{background:none;color: transparent; font-size: 0;}





.pointer {
      position: relative;
		background:#013E73;
		padding:5px;
		height:18px;
    }
    .pointer:after {
      content: "";
      position: absolute;
      left: 0;
      bottom: 0;
      width: 0;
      height: 0;
      border-left: 9px solid #DBEAFF;
      border-top: 14px solid transparent;
      border-bottom: 14px solid transparent;
    }
    .pointer:before {
      content: "";
      position: absolute;
      right: -9px;
      bottom: 0;
      width: 0;
      height: 0;
      border-left: 9px solid #013E73;
      border-top: 14px solid transparent;
      border-bottom: 14px solid transparent;
    }
	
	
.right_end {
		position: relative;
		background:#013E73;
		padding:5px;
		height:18px;
		width:50%;
    }
    .right_end:after {
      content: "";
      position: absolute;
      left: 0;
      bottom: 0;
      width: 0;
      height: 0;
      border-left: 9px solid #013E73;
      border-top: 14px solid transparent;
      border-bottom: 14px solid transparent;
    }
    .right_end:before {
      content: "";
      position: absolute;
      right: -15px;
      bottom: 0;
      width: 0;
      height: 0;
      border-left: 15px solid #013E73;
      border-top: 14px solid transparent;
      border-bottom: 14px solid transparent;
    }
	
	
	
	
	.left_end {
		position: relative;
		background:#013E73;
		padding:5px;
		height:18px;
    }

    .left_end:before {
      content: "";
      position: absolute;
      right: 100%;
      width: 0;
      height: 0;
	  top: 0;
      border-top: 14px solid transparent;
      border-right: 15px solid #013E73;
      border-bottom: 14px solid transparent;
    }
	
    .left_end:after {
      content: "";
      position: absolute;
      right: 0;
      bottom: 0;
      width: 0;
      height: 0;
      border-right: 9px solid #013E73;
      border-top: 15px solid transparent;
      border-bottom: 14px solid transparent;
    }
	

.zoom {
  transition: transform .2s; /* Animation */
}

.zoom:hover {
  transform: scale(4.9); /* (150% zoom - Note: if the zoom is too large, it will go outside of the viewport) */
}
	
</style> 
 
 <script>
	function blanking(classId) {
		$('.'+classId).fadeOut(500);
		$('.'+classId).fadeIn(500);
	}
	//setInterval(blanking, 1000);
</script>  
   
 <?

	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$total_datass=ob_get_contents();
	ob_clean();
 	echo "$total_datass****1";
	exit();
}
// woven
if($action=="generate_style_report_with_graph_wvn")
{

	if($graph==1){
		if($db_type==0)
		{
			$txt_date_from = date("'Y-m-d'",strtotime($txt_date_from));
			$txt_date_to = date("'Y-m-d'",strtotime($txt_date_to));
		}
		else
		{
			$txt_date_from = date("'d-M-Y'",strtotime($txt_date_from));
			$txt_date_to = date("'d-M-Y'",strtotime($txt_date_to));
		}
	}
$buyer_short_name_arr = return_library_array("SELECT short_name,id FROM  lib_buyer WHERE is_deleted = 0 and status_active=1 order by id asc","id","short_name");

$actual_manual_update_task_arr=return_library_array("select task_id,task_id from tna_manual_permission where is_actual_manual=1  and company_id=$cbo_company_name","task_id","task_id");
$plan_manual_update_task_arr=return_library_array("select task_id,task_id from tna_manual_permission where is_plan_manual=1  and company_id=$cbo_company_name","task_id","task_id");

	$mod_sql= sql_select("select a.id,a.task_catagory,a.task_name,a.task_short_name,a.task_type,a.completion_percent,a.task_sequence_no,b.task_template_id,b.lead_time ,b.tna_task_id
	from lib_tna_task a,tna_task_template_details b where a.task_name=b.tna_task_id and a.task_type=1  AND b.task_type = 1 and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 order by a.task_sequence_no asc");
	
	
	
	$taskIdArr=array();
	$tna_task_array=array();
	$tna_task_id=array();
	$tna_task_cat=array();
	$tna_task_name_arr=array();
	foreach ($mod_sql as $row)
	{
		$taskIdArr[$row[csf("task_name")]]=$row[csf("task_name")]*1;
		
		$tna_task_id[$row[csf("task_name")]]=$row[csf("task_name")];
		$tna_task_array[$row[csf("id")]] =$row[csf("task_short_name")];
		$tna_task_name_array[$row[csf("id")]] =$tna_task_name[$row[csf("task_name")]];
		$tna_task_cat[$row[csf("id")]]=$row[csf("task_catagory")];
		$tna_task_name_arr[$row[csf("id")]]=$row[csf("task_name")];
		$lead_time_array[$row[csf("task_template_id")]]=$row[csf("lead_time")];
		$tast_tmp_id_arr[$row[csf("task_template_id")]][$row[csf("tna_task_id")]]=$row[csf("tna_task_id")];
	
		$task_short_name_array[$row[csf("task_name")]] =$row[csf("task_short_name")];
	
	
	}
	//print_r($lead_time_array);die;
	
	$cbo_company_id=$cbo_company_name;
	
	$rpt_type=str_replace("'","",$rpt_type);
	
	$order_status_cond="";
	if(str_replace("'","",$cbo_order_status)>0) $order_status_cond=" and b.is_confirmed=$cbo_order_status";
	
	if(str_replace("'","",$cbo_company_name)==0) $cbo_company_name=""; else $cbo_company_name=" and a.company_name = $cbo_company_name";
	if(str_replace("'","",$cbo_buyer_name)==0) $cbo_buyer_name=""; else $cbo_buyer_name=" and a.buyer_name = $cbo_buyer_name";
	if(str_replace("'","",$cbo_team_member)==0) $cbo_team_member=""; else $cbo_team_member=" and a.dealing_marchant = $cbo_team_member";
	
	if(str_replace("'","",$cbo_search_type)==1){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
	}
	else if(str_replace("'","",$cbo_search_type)==3){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and c.country_ship_date between $txt_date_from and $txt_date_to";
	}
	else if(str_replace("'","",$cbo_search_type)==4){
		if($db_type==0)
		{
			if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)==""){$date_range="";}else{ 
			$date_range=" and b.insert_date between ".$txt_date_from." and ".$txt_date_to."";}
		}
		else
		{
			
			if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)==""){$date_range="";}else{ 
			$date_range=" and b.insert_date between ".$txt_date_from." and '".str_replace("'","",$txt_date_to)." 11:59:59 PM'";}
		}
	}
	else
	{
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.po_received_date between $txt_date_from and $txt_date_to";
	}
	
	

	
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if($txt_job_no=="") $txt_job_no=""; else $txt_job_no=" and a.job_no_prefix_num ='$txt_job_no'";
	$txt_order_no=str_replace("'","",$txt_order_no);
	if($txt_order_no=="") $txt_order_no=""; else $txt_order_no=" and b.po_number ='$txt_order_no'";
	$txt_file_no=str_replace("'","",$txt_file_no);
	if($txt_file_no=="") $file_cond=""; else $file_cond=" and b.file_no ='$txt_file_no'";
	$txt_int_ref_no=str_replace("'","",$txt_int_ref_no);
	if($txt_int_ref_no=="") $ref_cond=""; else $ref_cond=" and b.grouping ='$txt_int_ref_no'";
	
	$txt_style_ref_no=str_replace("'","",$txt_style_ref_no);
	if($txt_style_ref_no=="") $txt_style_ref_no=""; else $txt_style_ref_no=" and a.style_ref_no ='$txt_style_ref_no'";
	//**txt_date_from*txt_date_to*txt_job_no
	$shipment_status_con="";
	if(str_replace("'","",$cbo_shipment_status)==3) $shipment_status_con=" and b.shiping_status=$cbo_shipment_status";
	// else {$shipment_status_con=" and b.shiping_status !=3";} //as per Saeed
	
	
	
	
	
	$tna_all_task=implode(",",$tna_task_id);
	
	if(str_replace("'","",$cbo_search_type)==3)
	{
		$sql = "SELECT a.id as job_id,a.team_leader,a.factory_marchant,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, b.id ,b.po_number, b.file_no, b.grouping as in_ref_no,b.po_quantity
		FROM  wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c 
		WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no  and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 $order_status_cond $file_cond $ref_cond";
	}
	else
	{
		$sql = "SELECT a.id as job_id,a.team_leader,a.factory_marchant,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, b.id, b.po_number, b.file_no, b.grouping as in_ref_no ,b.po_quantity
		FROM  wo_po_details_master a,  wo_po_break_down b 
		WHERE a.job_no=b.job_no_mst $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no  and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 $order_status_cond  $file_cond $ref_cond"; 
	}
	
  //echo $sql; 
	$result = sql_select( $sql ) ;
	$wo_po_details_master = array();
	$po_no_arr=array();
	$job_no_arr=array();
	foreach( $result as  $row ) 
	{	
		$wo_po_details_master[$row[csf('id')]]['company_name']=$row[csf('company_name')];
		$po_no_arr[]=$row[csf('id')];
		$job_no_arr[]=$row[csf('job_no')];
		

		$wo_po_details_master[$row[csf('job_no')]]['po_number'][$row[csf('po_number')]]= $row[csf('po_number')];
		$wo_po_details_master[$row[csf('job_no')]]['set_smv']= $row[csf('set_smv')];
		$wo_po_details_master[$row[csf('job_no')]]['file_no']= $row[csf('file_no')];

		$wo_po_details_master[$row[csf('job_no')]]['dealing_marchant']=$row[csf('dealing_marchant')];
		$wo_po_details_master[$row[csf('job_no')]]['factory_marchant']=$row[csf('factory_marchant')];
		$wo_po_details_master[$row[csf('job_no')]]['team_leader']=$row[csf('team_leader')];
		$wo_po_details_master[$row[csf('job_no')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
		$wo_po_details_master[$row[csf('job_no')]]['in_ref_no']= $row[csf('in_ref_no')];
		$wo_po_details_master[$row[csf('job_no')]]['buyer_name']=$row[csf('buyer_name')];
		$wo_po_details_master[$row[csf('job_no')]]['style_ref_no']=$row[csf('style_ref_no')];
		$wo_po_details_master[$row[csf('job_no')]]['po_quantity']+=$row[csf('po_quantity')];
		
		$jobId_arr[$row[csf('job_id')]]=$row[csf('job_id')];
		$poId_arr[$row[csf('id')]]=$row[csf('id')];
	}
	
 
	$result = sql_select("select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name" ) ;
	$team_leader_name = array();
	foreach( $result as  $row ) 
	{	
		$team_leader_name[$row[csf('id')]]=$row[csf('team_leader_name')];
	}

 
	$sql = "SELECT team_member_name,id FROM lib_mkt_team_member_info WHERE is_deleted = 0 and status_active=1 order by id asc";
	$result = sql_select( $sql ) ;
	$team_member_name = array();
	foreach( $result as  $row ) 
	{	
		$team_member_name[$row[csf('id')]]=$row[csf('team_member_name')];
	}
	
	$sql = "SELECT buyer_name,id FROM  lib_buyer WHERE is_deleted = 0 and status_active=1 order by id asc";
	$result = sql_select( $sql ) ;
	$buyer_name = array();
	foreach( $result as  $row ) 
	{	
		$buyer_name[$row[csf('id')]]=$row[csf('buyer_name')];
	}
	
	
	$po_no_arr_all=implode(",",$po_no_arr); if($po_no_arr_all!="") $po_no_arr_all .=",0"; else $po_no_arr_all .="0"; 
	$job_no_all="'".implode("','",$job_no_arr)."'";
	$c=count($tna_task_id);
	
	if($db_type==0)
	{
		$sql ="select a.id,a.task_number,a.po_number_id, a.job_no, a.shipment_date,min(b.pub_shipment_date) as pub_shipment_date, a.template_id, a.po_receive_date,b.insert_date,";
		$i=1;
	
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c) $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id, ";
			else $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id ";
			$i++;
		}
		
		$sql .=" from tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id and a.po_number_id in( $po_no_arr_all ) and a.job_no in ($job_no_all) $shipment_status_con and b.status_active=1  and b.po_quantity>0 $order_status_cond and a.task_type=1 group by a.po_number_id,a.job_no,b.insert_date order by a.shipment_date,a.po_number_id,a.job_no"; 
	}
	else
	{
		$sql ="select a.task_number,  
		LISTAGG(cast(a.id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as id, 
		LISTAGG(cast(a.po_number_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as po_number_id, 
		LISTAGG(cast(a.template_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as template_id, 
		a.job_no, min(a.shipment_date) as shipment_date,min(b.pub_shipment_date) as pub_shipment_date,min(a.po_receive_date) as po_receive_date,min(b.insert_date) as insert_date,";
		
		$sql .="(min(a.actual_start_date) || '_' || min(a.task_start_date) || '_' || min(a.actual_finish_date) || '_' || min(a.task_finish_date) || '_' || min(a.plan_start_flag) || '_' || min(a.plan_finish_flag) || '_' || min(a.notice_date_start) || '_' || min(a.notice_date_end) || '_' || LISTAGG(cast(a.id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) ) as status ";
			
			
		//------------------
			$sql_order_con='';
			$po_no_arr_all=explode(',',$po_no_arr_all);
			$chunk_po_no_arr_all=array_chunk(array_unique($po_no_arr_all),999);
			$p=1;
			foreach($chunk_po_no_arr_all as $rlz_sub_id)
			{
				if($p==1) $sql_order_con .=" and (a.po_number_id in(".implode(',',$rlz_sub_id).")"; else $sql_sub_lc .=" or a.po_number_id in(".implode(',',$rlz_sub_id).")";
				$p++;
			}
			$sql_order_con .=" )";
			
			$sql_job_con='';
			$job_no_all=explode(',',$job_no_all);
			$chunk_job_no_all=array_chunk(array_unique($job_no_all),999);
			$q=1;
			foreach($chunk_job_no_all as $rlz_sub_id)
			{
				if($q==1) $sql_job_con .=" and (a.job_no in(".implode(',',$rlz_sub_id).")"; else $sql_sub_lc .=" or a.job_no in(".implode(',',$rlz_sub_id).")";
				$p++;
			}
			$sql_job_con .=" )";
			
			
		//-------------------------------
		$sql .=" from  tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id $sql_order_con $sql_job_con $shipment_status_con and b.status_active=1 and b.po_quantity>0 $order_status_cond  and a.task_type=1  group by a.job_no,a.task_number order by a.job_no"; 
	}
	$data_sql_2= sql_select($sql);
	    //echo $sql;die;
		
		foreach($data_sql_2 as $row){
			$dataArr[$row[csf('job_no')]][$row[csf('task_number')]]=$row;
			$data_sql[$row[csf('job_no')]]=$row;
			$job_no=$row[csf('job_no')];
			$shipDate=$row[csf('pub_shipment_date')];
		}

		$image_location_arr=return_library_array("select id,image_location from common_photo_library   where form_name='knit_order_entry' and master_tble_id='$job_no'","id","image_location");
	
	if(count($data_sql_2)==0){?>
			<h3 style="text-align:center; color:#F00">Data Not Found</h3>
		<?
			exit();
		}
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	  $sql_sew_prod="SELECT a.floor_id,a.production_type,a.sewing_line,a.production_quantity,a.reject_qnty from pro_garments_production_mst a, wo_po_break_down e, wo_po_details_master f where  a.po_break_down_id=e.id and e.job_no_mst=f.job_no and a.production_type in(5,80) and a.status_active=1 and a.is_deleted=0   and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0  ".where_con_using_array($jobId_arr,0,'f.id')."   ";
	$sewing_out_prod_qty=$fin_prod_qty=$fin_defect_qty=$sew_reject_qty=0;
	$sew_prod_result=sql_select($sql_sew_prod);
	foreach($sew_prod_result as $row)
	{
		if($row[csf('production_type')]==5)
		{
		$sewing_out_prod_qty+=$row[csf('production_quantity')];
		
		$sew_reject_qty+=$row[csf('reject_qnty')];
		$sewing_out_prod_QtyArr[$row[csf('sewing_line')]]+=$row[csf('production_quantity')];
		}
		if($row[csf('production_type')]==80) //Fin
		{
		$fin_prod_qty+=$row[csf('production_quantity')];
		$fin_reject_qty+=$row[csf('reject_qnty')];
		}
	}
	//print_r($sewing_out_prod_QtyArr);
	//echo $fin_reject_qty.'='.$fin_prod_qty;
	unset($sew_prod_result);
	
	 $sew_defect_sql=sql_select("select b.po_break_down_id as po_id,
	  (CASE WHEN  b.production_type in(5) THEN b.defect_qty else 0 END)  as sew_defect_qty,
	  (CASE WHEN  b.production_type in(80) THEN b.defect_qty else 0 END)  as fin_defect_qty
	  from pro_gmts_prod_dft b
	where  b.production_type in(5,80) and  b.status_active=1 and b.is_deleted=0  ".where_con_using_array($poId_arr,0,'b.po_break_down_id')."");

	foreach($sew_defect_sql as $row)
	{
		//$sewing_defect_arr[$row[csf("po_id")]]+=$row[csf("defect_qty")];
		$fin_defect_qty+=$row[csf("fin_defect_qty")];
	}
	 unset($sew_defect_sql);
	 
	
	
	  $sql_sew="SELECT a.floor_id,a.prod_reso_allo,a.sewing_line,a.production_quantity, e.id as po_id,f.style_ref_no,f.buyer_name, b.bundle_no,b.defect_type_id, b.defect_point_id, b.defect_qty from pro_garments_production_mst a, wo_po_break_down e, wo_po_details_master f,pro_gmts_prod_dft b where a.id=b.mst_id and a.po_break_down_id=e.id and e.job_no_mst=f.job_no and e.id=b.po_break_down_id  and a.production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0    and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0    ".where_con_using_array($jobId_arr,0,'f.id')."   ";
	$sewing_defect_qty=0;
	$sew_sql_result=sql_select($sql_sew);
	foreach($sew_sql_result as $row)
	{
		if($row[csf('prod_reso_allo')]==1)
		{
		$line_resource_mst_arr=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
			$line_name="";
			foreach($line_resource_mst_arr as $resource_id)
			{
				$line_name .= ($line_name == "") ? $resource_id : ",".$resource_id;
			}
			$sewing_line=$line_name;
		}
		else $sewing_line=$row[csf('sewing_line')];
		
		 $floor_name=$floor_nameArr[$row[csf('floor_id')]];
		
		$sew_line_arr[$sewing_line][$row[csf('style_ref_no')]]['buyer_name']=$row[csf('buyer_name')];
		$sew_line_arr[$sewing_line][$row[csf('style_ref_no')]]['production_quantity']=$row[csf('production_quantity')];
		
		$sew_defect_qty_arr[$row[csf('defect_type_id')]][$row[csf('defect_point_id')]]+=$row[csf('defect_qty')];
		
		$sewing_defect_prod_QtyArr[$row[csf('sewing_line')]]+=$row[csf('defect_qty')];
		
		$sewing_defect_qty+=$row[csf('defect_qty')];
	}
	$alter_defect_point=0;
	foreach($sew_defect_qty_arr as $typeId=>$typeData)
	{
		foreach($typeData as $pointId=>$val_alter)
		{
			if($typeId==1) //Alter check
			{
				$defect_point=$sew_fin_alter_defect_type[$pointId];
				$alter_defect_point+=$val_alter;
			}
			elseif($typeId==2) //Spot check
			{
				$defect_point=$sew_fin_spot_defect_type[$pointId];
			}
			elseif($typeId==4 || $typeId==5) //Front check
			{
				$defect_point=$sew_fin_woven_defect_array[$pointId];
			}
			elseif($typeId==6) //West check
			{
				$defect_point=$sew_fin_woven_defect_array[$pointId];//sew_fin_measurment_check_array
			}
			elseif($typeId==7) //Mewasure check
			{
				$defect_point=$sew_fin_measurment_check_array[$pointId];//sew_fin_measurment_check_array
			}
			$defect_check_arr[$defect_point]+=$val_alter;
		}
	}
	   asort($defect_check_arr);
	   $defect_check_arrNew = array_slice($defect_check_arr, -10);
	   $t=1;$top_ten_percent_graph=array();$sewing_defect_top_qty=0;
	 foreach($defect_check_arrNew as $key_type=>$top_defect_qty)
	 {
		if($t<=10) //Top Ten
		{
			//$top_ten_percent_graph_arr[$key_type]=$top_defect_qty;//($top_defect_qty/$total_adult_qty)*100;
			$top_ten_percent_graph_arr[$top_defect_qty]='y';
			$top_ten_percent_graph_arr[$key_type]='label';
			//$top_ten_percent_graph[] = array('y'=>$top_defect_qty,'label'=>$key_type);
			$top_ten_percent_graph[] = array('y'=>$top_defect_qty,'label'=>$key_type);
			//array_push($top_ten_percent_graph, array("x"=> $top_defect_qty->x, "y"=> $key_type->y));
			$sewing_defect_top_qty+=$top_defect_qty;
		}
	 }
	// print_r($top_ten_percent_graph);
	$top_ten_percent_graphArr[]= array_slice($top_ten_percent_graph, 0, 10);
	$top_ten_percent_graphArr_chk = array_shift($top_ten_percent_graphArr);
	//Sewing Graph End
	
		$cut_qc_arr=sql_select("SELECT a.po_break_down_id as po_id,(b.production_qnty) as qnty,b.reject_qty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=1 and b.production_type=1 and b.cut_no is not null and b.color_size_break_down_id is not null and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($poId_arr,0,'a.po_break_down_id')." ");
		//echo "SELECT a.po_break_down_id as po_id,(b.production_qnty) as qnty,b.reject_qty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=1 and b.production_type=1 and b.cut_no is not null and b.color_size_break_down_id is not null and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($poId_arr,0,'a.po_break_down_id')." ";
		$cuting_reject_qty=$cuting_prod_qty=0;
		foreach( $cut_qc_arr as $row )
		{
			$cuting_prod_qty+=$row[csf("qnty")];
			 $cuting_reject_qty+=$row[csf("reject_qty")];
		}
		unset($cut_qc_arr);
		$act_ahip_arr=sql_select("select po_break_down_id,
		sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as shipout_qty,
		sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as return_qnty
		from  pro_ex_factory_mst where status_active=1 and is_deleted=0 ".where_con_using_array($poId_arr,0,'po_break_down_id')." group by po_break_down_id");// 
		$actual_shipout_qty=0;
		foreach($act_ahip_arr as $row)
		{
			$actual_shipout_qty+=$row[csf('shipout_qty')]-$row[csf('return_qnty')];
		}
	 unset($act_ahip_arr);
	 $sql_cut=sql_select("select d.id,a.job_no,d.cutting_no as cutting_no,d.marker_length,d.cad_marker_cons,c.marker_qty from wo_po_details_master a, ppl_cut_lay_mst d,ppl_cut_lay_dtls c where a.job_no=d.job_no   and d.id=c.mst_id and d.entry_form=289 and c.status_active=1 and c.is_deleted=0    $style_owner_cond  $comp_cond   $buyer_id_cond $year_cond $job_no_cond    $style_ref_cond $job_id_cond  $brand_name_cond  ".where_con_using_array($jobId_arr,0,'a.id')."  order by d.id asc");
		
		//echo "select d.id,a.job_no,d.cutting_no as cutting_no,d.marker_length,d.cad_marker_cons,c.marker_qty from wo_po_details_master a, ppl_cut_lay_mst d,ppl_cut_lay_dtls c where a.job_no=d.job_no   and d.id=c.mst_id and d.entry_form=289 and c.status_active=1 and c.is_deleted=0    $style_owner_cond  $comp_cond   $buyer_id_cond $year_cond $job_no_cond    $style_ref_cond $job_id_cond  $brand_name_cond  ".where_con_using_array($job_id_arr,1,'a.id')."  order by d.id asc";
		 $cut_lay_qty=0;
		foreach($sql_cut as $row)
		{
			//$master_ref=$master_ref_arr[$row[csf('job_no')]];
			//$cut_no_lay_arr[$master_ref]['cutting_no'].=$row[csf('cutting_no')].',';
			$cut_lay_qty+=$row[csf('marker_qty')];
			 
		}
		unset($sql_cut);
		
	$cut_dhu_per=($cuting_reject_qty/$cuting_prod_qty*100);
	$tot_sew_dhu_per=$sewing_defect_qty/$sewing_out_prod_qty*100;
	$fin_dhu_per=$fin_defect_qty/$fin_prod_qty*100;
	
	 $cut_to_shipQty_ratio_per=$actual_shipout_qty/$cut_lay_qty*100;
	 $fin_to_shipQty_ratio_per=$actual_shipout_qty/$fin_prod_qty*100;
	 
	 $rft_defect=$sewing_out_prod_qty-($alter_defect_point+$sew_reject_qty);
	 
	 $rft_per=($rft_defect/$sewing_out_prod_qty)*100;
	 $reject_rate_per=($sew_reject_qty/$sewing_out_prod_qty)*100;
	 $defect_rate_per=($sewing_defect_qty/$sewing_out_prod_qty)*100;
	 //sewing_out_prod_QtyArr //sewing_defect_prod_QtyArr
	 foreach($sewing_out_prod_QtyArr as $line=>$sew_qty)
	 {
		 $sewingDefectQty=$sewing_defect_prod_QtyArr[$line];
		 $lineName=$lineArr[$line];
		 $sew_dhu_per=$sewingDefectQty/$sew_qty*100;
		 $sewing_dhu_arr[$lineName]=$sew_dhu_per;
		 
	 }
	 asort($sewing_dhu_arr);
	 foreach($sewing_dhu_arr as $lineName=>$dhu_per)
	 {
		 $sewing_dhu_percent_graph[] = array('y'=>$dhu_per,'label'=>$lineName);
	 }
	//echo  $sewing_defect_qty.'=='.$sewing_out_prod_qty;
	
	
	 // print_r($sewing_dhu_arr);
	  //echo $top_ten_percent_graph;
	 
	
	
	// echo "<pre>"; print_r($top_ten_percent_graph);
	
	ob_start();
	
	?>
  <div style="margin-left:10px;">
  <table width="900" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
  	<thead>
    <tr>
        <th>Buyer Name</th>
        <th>Job No.</th>
        <th>Style Ref.</th>
        <th>PO Number</th>
        <th width="80">PO Qty.</th>
        <th>Shipment Date</th>
        <th width="<?= count($image_location_arr)*46;?>">Image</th>
        <td onMouseOver="blanking('complete')" align="center"><span style="background:#00FF00; padding:0 6px; border-radius:9px; cursor:pointer;" title="green">&nbsp;</span></td>
        <td width="100">Task Complete</td>
  </tr>
  </thead>
  <tr>
        <td rowspan="3" align="center" valign="middle"><?= $buyer_short_name_arr[$wo_po_details_master[$job_no]['buyer_name']];?></td>
        <td rowspan="3" align="center" valign="middle"><a href="javascript:generate_bom('preCostRpt2','<?= $job_no;?>',<?= $cbo_company_id;?>,'<?= $wo_po_details_master[$job_no]['buyer_name'];?>','<?= $wo_po_details_master[$job_no]['style_ref_no'];?>','')"><?= $job_no;?></a></td>
        <td rowspan="3" align="center" valign="middle"><?= $wo_po_details_master[$job_no]['style_ref_no'];?></td>
        <td rowspan="3" align="center" valign="middle"><p><?= implode(',',$wo_po_details_master[$job_no]['po_number']);?></p></td>
        <td rowspan="3" align="center" valign="middle"><?= $wo_po_details_master[$job_no]['po_quantity'];?></td>
        <td rowspan="3" align="center" valign="middle"><?= change_date_format($shipDate);?></td>
        <td rowspan="3" align="center" valign="middle">
        	<? foreach($image_location_arr as $img_path){ ?>
            <img class="zoom" src="../../<?= $img_path;?>" width="40" style="float:left; border:1px solid #999; margin:1px;" />
            <? } ?>
        </td>
        <td onMouseOver="blanking('partially')" align="center" valign="middle"><span style="background:yellow; padding:0 6px; border-radius:9px; cursor:pointer;" title="yellow">&nbsp;</span></td>
        <td valign="middle">Partially Complete</td>
  </tr>
   <tr>
        <td onMouseOver="blanking('notStarted')" align="center"><span style="background:#FF0000; padding:0 6px; border-radius:9px; cursor:pointer;" title="Red">&nbsp;</span></td>
        <td>Task Not Started</td>
  </tr>
   <tr>
        <td onMouseOver="blanking('done_in_late')" align="center"><span style="background:#2A9FFF; padding:0 6px; border-radius:9px; cursor:pointer;" title="Blue">&nbsp;</span></td>
        <td>Done In Late</td>
  </tr>
  </table>
  </div>  
<?
  
error_reporting(0);
$dataShowParLine=9;
$taskChunkArr=array_chunk($taskIdArr,$dataShowParLine);
$taskNameArr=$task_short_name_array;



$width=130;	
$i=0;$s=0;
$html='';

echo '<div style="width:'.(($width*$dataShowParLine)+125).'px; border:1px solid #CCC;padding:5px 15px; margin:5px 10px;">';
foreach($taskChunkArr as $row){
	
	$actual_start_date=array();
	$task_start_date=array();
	$actual_finish_date=array();
	$task_finish_date=array();
	$plan_start_flag=array();
	$plan_finish_flag=array();
	$notice_date_start=array();
	$notice_date_end=array();
	$mst_id=array();
	
	foreach($row as $key=>$task_Id){
	list($actual_start_date[$key],$task_start_date[$key],$actual_finish_date[$key],$task_finish_date[$key],$plan_start_flag[$key],$plan_finish_flag[$key],$notice_date_start[$key],$notice_date_end[$key],$mst_id[$key])=explode("_",$dataArr[$job_no][$task_Id]['STATUS']);
	
}
	
	$activeIncativeClass=array();
	$containClass=array();
	$titleClass=array();
	
	for($i8=0;$i8<$dataShowParLine;$i8++){
		if($actual_start_date[$i8] && empty($actual_finish_date[$i8]) && $row[$i8]){$activeIncativeClass[$i8]="s_active partially";}
		if($actual_finish_date[$i8] && $row[$i8]){$activeIncativeClass[$i8]="s_active complete";}
		if(empty($actual_start_date[$i8]) && $row[$i8]){$activeIncativeClass[$i8]="s_active notStarted";}
		
		if($actual_finish_date[$i8] && $actual_start_date[$i8]){
			$done_day = datediff( "d", $task_finish_date[$i8], $actual_finish_date[$i8]);
			if($done_day>1){$activeIncativeClass[$i8]="s_active done_in_late";}
		}

		if(empty($row[$i8])){$containClass[$i8]="displayNone";}
		if(empty($row[$i8])){$titleClass[$i8]="displayNone";}
		
	}

	
	
	
	$i++;$s++;
	if($i==4){$i=2;}
	
	if($i==1){
		//$html.=$htmlArr['head'];
		 echo '
				<table id="s_graph" border="1" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<th width="60"><div class="g_contain"><div class="g_title bg-0">&nbsp; </div><div class="g_bar">&nbsp;</div><div class="g_box pointer">&nbsp; <b>&nbsp;</b></div></div></th>';
					
					for($i1=0;$i1<$dataShowParLine;$i1++){
					$bgcolor=($i1%2==0)?"bg-0":"bg-1";
					$opupFunction='<a href="javascript:fnTaskPopupWvn(\''.$row[$i1].'*'.$job_no.'\')">'.$taskNameArr[$row[$i1]].'</a>';
					echo '<th width="'.$width.'"><div class="g_contain"><div class="g_title '.$bgcolor.'">'.$opupFunction.'</div><div class="g_bar">|</div><div class="g_box"><i class="'.$activeIncativeClass[$i1].'"></i></div></div></th>';
					}
					echo'<th rowspan="2" align="left" valign="bottom" width="60"><div class="right">&nbsp;</div></th>
			  </tr>';
	
	}	
	else if($i==2 && count($taskChunkArr)!=$s){
		//$html.=$htmlArr['left'];
		 
		echo '<tr>
				<th valign="bottom" rowspan="2" align="right" valign="middle"><div class="left">&nbsp;</div></th>';
				
				for($i5=($dataShowParLine-1);$i5>=0;$i5--){
				$opupFunction='<a href="javascript:fnTaskPopupWvn(\''.$row[$i5].'*'.$job_no.'\')">'.$taskNameArr[$row[$i5]].'</a>';
				$bgcolor=($i5%2==0)?"bg-0":"bg-1";
				echo '<th><div class="g_contain"><div class="g_title  '.$bgcolor.'">'.$opupFunction.'</div> <div class="g_bar">|</div><div class="g_box"><i class="'.$activeIncativeClass[$i5].'"></i></div></div></th>';
				}
				
				
			echo '</tr>';
	}	
	else if($i==3 && count($taskChunkArr)!=$s){
		//$html.=$htmlArr['right'];
		 echo '<tr>';
				for($i3=0;$i3<$dataShowParLine;$i3++){
				$bgcolor=($i3%2==0)?"bg-0":"bg-1";
				$opupFunction='<a href="javascript:fnTaskPopupWvn(\''.$row[$i3].'*'.$job_no.'\')">'.$taskNameArr[$row[$i3]].'</a>';
				
				echo '<th><div class="g_contain"><div class="g_title  '.$bgcolor.'">'.$opupFunction.'</div> <div class="g_bar">|</div><div class="g_box"><i class="'.$activeIncativeClass[$i3].'"></i></div></div></th>';
				}
				echo '<th valign="bottom" rowspan="2" align="left" valign="middle"><div class="right">&nbsp;</div></th>
			</tr>';
	
	}	
	else if(count($taskChunkArr)==$s){
		//$html.=$htmlArr['footer'];
		 if($i==3){
		 echo '<tr>';
				for($i4=0;$i4<$dataShowParLine;$i4++){
				$bgcolor=($i4%2==0)?"bg-0":"bg-1";
				$opupFunction='<a href="javascript:fnTaskPopupWvn(\''.$row[$i4].'*'.$job_no.'\')">'.$taskNameArr[$row[$i4]].'</a>';
				echo '<th><div class="g_contain"><div class="g_title '.$bgcolor.' '.$titleClass[$i4].'">'.$opupFunction.'</div> <div class="g_bar '.$titleClass[$i4].'">|</div><div class="g_box"><i class="'.$activeIncativeClass[$i4].'"></i></div></div></th>';
				}
				echo '<th valign="bottom"><div class="g_contain"> <div class="g_bar">&nbsp;</div><div class="g_box right_end">&nbsp; <b>&nbsp;</b></div></div></th>
			</tr>
			</table>
			';
		 }
		 else{
		 echo '<tr>
				<th valign="bottom"><div class="g_contain">&nbsp; <div class="g_box left_end">&nbsp; <b>&nbsp;</b></div></div></th>';
				for($i6=($dataShowParLine-1);$i6>=0;$i6--){
					$opupFunction='<a href="javascript:fnTaskPopupWvn(\''.$row[$i6].'*'.$job_no.'\')">'.$taskNameArr[$row[$i6]].'</a>';
					$bgcolor=($i6%2==0)?"bg-0":"bg-1";
				echo '<th><div class="g_contain"><div class="g_title '.$bgcolor.' '.$titleClass[$i6].'">'.$opupFunction.'</div><div class="g_bar '.$titleClass[$i6].'">|</div><div class="g_box"><i class="'.$activeIncativeClass[$i6].'"></i></div></div></th>';
				}
				
				
			echo '</tr>
			</table>
			';
		 }
		
	}
}
echo "</div>";
if(count($top_ten_percent_graphArr_chk)>0)
{
	$cut_dhu_per= json_encode($cut_dhu_per); 
	$tot_sew_dhu_per= json_encode($tot_sew_dhu_per); 
	$fin_dhu_per= json_encode($fin_dhu_per);
	
	 $cut_to_shipQty_ratio_per=json_encode($cut_to_shipQty_ratio_per);
	 $fin_to_shipQty_ratio_per=json_encode($fin_to_shipQty_ratio_per);
	// $fin_to_shipQty_ratio_per=$actual_shipout_qty/$fin_prod_qty*100;
	
	
	 
?>
 <div style="width:<? echo '.(($width*$dataShowParLine)).'?>px;border:solid 1px">
   
       <script> hs_chart_mm('<? echo json_encode($top_ten_percent_graphArr_chk,JSON_NUMERIC_CHECK);?>');</script>
       
        <script> canvas_chart_mm('<? echo $cut_dhu_per;?>','<? echo $tot_sew_dhu_per;?>','<? echo $fin_dhu_per;?>');</script>
        
       <script> canvas_ratio_chart_mm('<? echo $cut_to_shipQty_ratio_per;?>','<? echo $fin_to_shipQty_ratio_per;?>');</script>
       <script> //canvas_dhu_line_chart_mm('<? echo $tot_sew_dhu_per;?>','<? echo $reject_rate_per;?>','<? echo $defect_rate_per;?>','<? echo $rft_defect;?>','<? echo $sew_reject_qty;?>','<? echo $sewing_defect_qty;?>','<? echo $rft_per;?>','<? echo $sewing_out_prod_qty;?>');</script>
       
        <script> canvas_dhu_line_chart('<? echo json_encode($sewing_dhu_percent_graph,JSON_NUMERIC_CHECK);?>');</script>
                    
</div>
<?
}
?>



    



<!--<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>-->


<style>
#s_graph td,#s_graph th{border:none; text-align:center;}

:root{
	--blue:#2962FF;
}

.g_contain{
	margin: 0 0 15px;
	
}


.g_title{
	height:25px;
	text-align:center;
	vertical-align:middle;
	margin:0;
	padding:3px 0 0 0;
	line-height:12px;
}

.bg-1{background:#C5D9F1;}
.bg-0{background:#FCD5B4;}

.g_bar{
	color:#013E73;
	font-size:36px;
	text-align:center; 
	height:10px;
	margin:0;
	line-height:8px;
	overflow:hidden;
	}




.g_box{
	background:#013E73;
	padding:5px;
	height:18px;
}

.g_box b{
	color:#FFF;
	font-size:26px;
	line-height:12px;
}

.s_active{
	border-radius:50%;padding:1px 8px;
	box-shadow: 0 0 3px #FFF;
}

.complete{ 
	background:#00FF00; border:1px solid #00FF00;
}

.done_in_late{ 
	background:#2A9FFF; border:1px solid #2A9FFF;
}


.notStarted{ 
	background:#FF0000; border:1px solid #FF0000;
}
.partially{background:#FF0; border:1px solid #FF0;
}


.right{
	border-top:28px solid #013E73;
	border-right:30px solid #013E73;
	border-bottom:28px solid #013E73;
	border-radius:0 50px 50px 0;
	height:53px;
	margin: 0 0 15px;
	
}


.left{
	border-top:28px solid #013E73;
	border-left:30px solid #013E73;
	border-bottom:28px solid #013E73;
	border-radius:50px 0 0 50px;
	height:53px;
	margin: 0 0 15px;
}


.displayNone{background:none;color: transparent; font-size: 0;}





.pointer {
      position: relative;
		background:#013E73;
		padding:5px;
		height:18px;
    }
    .pointer:after {
      content: "";
      position: absolute;
      left: 0;
      bottom: 0;
      width: 0;
      height: 0;
      border-left: 9px solid #DBEAFF;
      border-top: 14px solid transparent;
      border-bottom: 14px solid transparent;
    }
    .pointer:before {
      content: "";
      position: absolute;
      right: -9px;
      bottom: 0;
      width: 0;
      height: 0;
      border-left: 9px solid #013E73;
      border-top: 14px solid transparent;
      border-bottom: 14px solid transparent;
    }
	
	
.right_end {
		position: relative;
		background:#013E73;
		padding:5px;
		height:18px;
		width:50%;
    }
    .right_end:after {
      content: "";
      position: absolute;
      left: 0;
      bottom: 0;
      width: 0;
      height: 0;
      border-left: 9px solid #013E73;
      border-top: 14px solid transparent;
      border-bottom: 14px solid transparent;
    }
    .right_end:before {
      content: "";
      position: absolute;
      right: -15px;
      bottom: 0;
      width: 0;
      height: 0;
      border-left: 15px solid #013E73;
      border-top: 14px solid transparent;
      border-bottom: 14px solid transparent;
    }
	
	
	
	
	.left_end {
		position: relative;
		background:#013E73;
		padding:5px;
		height:18px;
    }

    .left_end:before {
      content: "";
      position: absolute;
      right: 100%;
      width: 0;
      height: 0;
	  top: 0;
      border-top: 14px solid transparent;
      border-right: 15px solid #013E73;
      border-bottom: 14px solid transparent;
    }
	
    .left_end:after {
      content: "";
      position: absolute;
      right: 0;
      bottom: 0;
      width: 0;
      height: 0;
      border-right: 9px solid #013E73;
      border-top: 15px solid transparent;
      border-bottom: 14px solid transparent;
    }
	

.zoom {
  transition: transform .2s; /* Animation */
}

.zoom:hover {
  transform: scale(4.9); /* (150% zoom - Note: if the zoom is too large, it will go outside of the viewport) */
}
	
</style> 
 
 <script>
	function blanking(classId) {
		$('.'+classId).fadeOut(500);
		$('.'+classId).fadeIn(500);
	}
	//setInterval(blanking, 1000);
</script>  
 

   
 <?

	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$total_datass=ob_get_contents();
	ob_clean();
 	echo "$total_datass****1****".json_encode($top_ten_percent_graph,JSON_NUMERIC_CHECK).'****'.$rpt_type;
	exit();
}


if($action=='graph_task_poup_wvn') //For Wvn
{
	
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);

	list($task_id,$job_no)=explode('*',$task_data_str);
	
$dealing_library=return_library_array( "select id, team_member_name from lib_mkt_team_member_info", "id", "team_member_name");
$leader_library=return_library_array( "select id, team_leader_name from lib_marketing_team", "id", "team_leader_name");
	//TEMPLATE_ID
	$taskSql =sql_select("select MAX(TEMPLATE_ID)  AS TEMPLATE_ID,JOB_NO,MIN(TASK_START_DATE) AS TASK_START_DATE,MIN(TASK_FINISH_DATE) AS TASK_FINISH_DATE,MIN(ACTUAL_START_DATE) AS ACTUAL_START_DATE,MIN(ACTUAL_FINISH_DATE) AS ACTUAL_FINISH_DATE,MIN(COMMIT_START_DATE) AS COMMIT_START_DATE,MIN(COMMIT_END_DATE) AS COMMIT_END_DATE from tna_process_mst where JOB_NO='$job_no' and TASK_NUMBER='$task_id' and TASK_TYPE=1 GROUP BY JOB_NO"); 
	//echo "select max(template_id)  as template_id,JOB_NO,MIN(TASK_START_DATE) AS TASK_START_DATE,MIN(TASK_FINISH_DATE) AS TASK_FINISH_DATE,MIN(ACTUAL_START_DATE) AS ACTUAL_START_DATE,MIN(ACTUAL_FINISH_DATE) AS ACTUAL_FINISH_DATE,MIN(COMMIT_START_DATE) AS COMMIT_START_DATE,MIN(COMMIT_END_DATE) AS COMMIT_END_DATE from tna_process_mst where JOB_NO='$job_no' and TASK_NUMBER='$task_id' and TASK_TYPE=1 GROUP BY JOB_NO";
	$template_id=$taskSql[0][TEMPLATE_ID];
	//echo $template_id.'SS';
	//echo "select JOB_NO,MIN(TASK_START_DATE) AS TASK_START_DATE,MIN(TASK_FINISH_DATE) AS TASK_FINISH_DATE,MIN(ACTUAL_START_DATE) AS ACTUAL_START_DATE,MIN(ACTUAL_FINISH_DATE) AS ACTUAL_FINISH_DATE,MIN(COMMIT_START_DATE) AS COMMIT_START_DATE,MIN(COMMIT_END_DATE) AS COMMIT_END_DATE from tna_process_mst where JOB_NO='$job_no' and TASK_NUMBER='$task_id' and TASK_TYPE=1 GROUP BY JOB_NO";
	
	$task_short_arr = return_library_array("select task_name,task_short_name from  lib_tna_task where STATUS_ACTIVE=1 and IS_DELETED=0  AND task_type = 1","task_name","task_short_name");
	$sql_tna_comment=sql_select("select max(a.id) as id,a.mer_comments  from tna_progress_comments a,wo_po_break_down b where  b.id=a.order_id and a.task_id='$task_id' and b.job_no_mst='$job_no'  and a.task_type=1 group by a.mer_comments");
	//echo "select max(a.id) as id,a.mer_comments  from tna_progress_comments a,wo_po_break_down b where  b.id=a.order_id and a.task_id='$task_id' and b.job_no_mst='$job_no'  and a.task_type=1 group by a.mer_comments";
		foreach($sql_tna_comment as $row)
		{
			if($row[csf('mer_comments')])
			{
			$mer_comments=$row[csf('mer_comments')];
			}
		}
	?>
    <style>
	hr {
  border-top: 1px solid red; 
}
	</style>
    <div style="width:1150px" align="left"> 
    <table width="100%" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
    	<thead>
        	<!--<tr><th width="150">Plan Start Date</th><td align="center"><?=change_date_format($taskSql[0][TASK_START_DATE]);?></td></tr>
            <tr><th>Plan Finish Date</th><td align="center"><?=change_date_format($taskSql[0][TASK_FINISH_DATE]);?></td></tr>
            <tr><th>Actual Start Date</th><td align="center"><?=change_date_format($taskSql[0][ACTUAL_START_DATE]);?></td></tr>
            <tr><th>Actual Finish Date</th><td align="center"><?=change_date_format($taskSql[0][ACTUAL_FINISH_DATE]);?></td></tr>
            
            <tr><th>Commitment Start Date</th><td align="center"><?=change_date_format($taskSql[0][COMMIT_START_DATE]);?></td></tr>
            <tr><th>Commitment Finish Date</th><td align="center"><?=change_date_format($taskSql[0][COMMIT_END_DATE]);?></td></tr>-->
            
            <tr>
                <th colspan="11"> </th>
                <th colspan="2"><? echo $task_short_arr[$task_id];?> </th>
            </tr>
            <tr>
                <th>Team Leader</th>
                <th>Dealing Merchant</th>
                <th>Buyer Name</th>
                <th>Style Ref.</th>
                <th>Job No</th>
                <th>PO Number</th>
                <th>Job Qty.</th>
                <th>Style SMV</th>
                <th>Shipment Date</th>
                <th>PO Insert Date</th>
                <th>Status</th>
                <th>Start</th>
                <th>Finish</th>
            </tr>
            
        </thead>
        <?
	
		 
		
        $sql_po="select a.id as job_id,a.job_no_prefix_num, a.company_name,a.job_no,a.set_smv,a.style_ref_no,a.team_leader,a.dealing_marchant, a.buyer_name,a.inquiry_id, a.style_ref_no, a.avg_unit_price, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.unit_price, b.shiping_status,b.is_confirmed, b.pub_shipment_date,(b.pub_shipment_date - b.po_received_date) as  date_diff,b.po_received_date,b.insert_date,b.grouping as ref_no, b.shiping_status, (c.order_quantity) as po_quantity, (c.plan_cut_qnty) as plan_cut, (c.order_total) as po_total_price from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.job_no='$job_no'   order by b.id ";
		 
		//echo $sql_po;//die;
		$result_po=sql_select($sql_po); $i=1;
		$tot_rows=count($result_po);$po_qty_pcs=0;
		foreach($result_po as $row)
		{
			$is_confirmed=$row[csf('is_confirmed')];
			$company_name=$row[csf('company_name')];
			
			//$inquiry_wise_jobQty_arr[$row[csf('inquiry_id')]]+=$row[csf('po_quantity')]*$row[csf('ratio')];
			$master_stlye_arr[$row[csf('job_no')]]['team_leader']=$leader_library[$row[csf('team_leader')]];
			$master_stlye_arr[$row[csf('job_no')]]['dealing_marchant']=$dealing_library[$row[csf('dealing_marchant')]];
			$master_stlye_arr[$row[csf('job_no')]]['job_no'].=$row[csf('job_no')].',';
			$master_stlye_arr[$row[csf('job_no')]]['po_insert_date'].=$row[csf('insert_date')].',';
			$master_stlye_arr[$row[csf('job_no')]]['po_received_date'].=$row[csf('po_received_date')].',';
			$master_stlye_arr[$row[csf('job_no')]]['pub_ship_date'].=$row[csf('pub_shipment_date')].',';
			$master_stlye_arr[$row[csf('job_no')]]['po_lead_time'].=$row[csf('date_diff')].',';
			$master_stlye_arr[$row[csf('job_no')]]['buyer_name']=$row[csf('buyer_name')];
			$master_stlye_arr[$row[csf('job_no')]]['style_ref_no']=$row[csf('style_ref_no')];
			$master_stlye_arr[$row[csf('job_no')]]['po_number'].=$row[csf('po_number')].',';
			$master_stlye_arr[$row[csf('job_no')]]['po_id'].=$row[csf('po_id')].',';
			$master_stlye_arr[$row[csf('job_no')]]['sew_smv']=$row[csf('set_smv')];
			$master_stlye_arr[$row[csf('job_no')]]['po_qty_pcs']+=$row[csf('po_quantity')]*$row[csf('ratio')];
			$master_stlye_arr[$row[csf('job_no')]]['po_total_price']+=$row[csf('po_total_price')];
			$master_stlye_arr[$row[csf('job_no')]]['plan_cut']+=$row[csf('plan_cut')];
			$master_stlye_arr[$row[csf('job_no')]]['shiping_status']=$row[csf('shiping_status')];
			$po_qty_pcs+=$row[csf('po_quantity')]*$row[csf('ratio')];
			 
			$po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
			$inquiry_id_arr[$row[csf('inquiry_id')]]=$row[csf('inquiry_id')];
		}
		
		$tna_data_array= sql_select("select task_template_id,lead_time,tna_task_id,deadline,dependant_task from  tna_task_template_details where tna_task_id in (".$task_id.") and status_active=1 and  task_template_id=$template_id and is_deleted=0 order by sequence_no");
		//echo "select task_template_id,lead_time,tna_task_id,deadline,dependant_task from  tna_task_template_details where tna_task_id in (".$task_id.") and status_active=1 and  task_template_id=$template_id and is_deleted=0 order by sequence_no"; 
		//echo "select task_template_id,lead_time,tna_task_id,deadline,dependant_task from  tna_task_template_details where tna_task_id in (".$task_id.") and status_active=1  and company_id=$company_name and is_deleted=0 order by sequence_no";
		foreach($tna_data_array as $row)
		{
			$tna_lead_arr[$row[csf('tna_task_id')]]['lead_time']=$row[csf('lead_time')];
			$tna_lead_arr[$row[csf('tna_task_id')]]['dependant_task']=$row[csf('dependant_task')];
		}
		$deadline=$tna_lead_arr[$task_id]['lead_time']; 
		$dependant_task=$task_short_arr[$tna_lead_arr[$task_id]['dependant_task']];
		
		
		$i=1;
		foreach($master_stlye_arr as $job_no=>$row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$po_number=rtrim($row[('po_number')],',');
			$po_numbers=implode(",",array_unique(explode(",",$po_number)));
			
			$po_insert_date=rtrim($row[('po_insert_date')],',');
			$po_insert_dateArr=array_unique(explode(",",$po_insert_date));
			$po_insert_dateMax=min($po_insert_dateArr);
			
			$pub_shipment_date=rtrim($row[('pub_ship_date')],',');
			$pub_shipment_dateArr=array_unique(explode(",",$pub_shipment_date));
			$pub_shipment_dateMax=max($pub_shipment_dateArr);
			
			$po_lead_time=rtrim($row[('po_lead_time')],',');
			$po_lead_timeArr=array_unique(explode(",",$po_lead_time));
			$po_lead_timeMax=min($po_lead_timeArr);
		//	echo $pub_shipment_dateMax.'='.$po_insert_date;
			
		?>
       <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
       
				<td width="100"><p><? echo $row[('team_leader')]; ?></p></td>
                <td width="100"><p><? echo $row[('dealing_marchant')]; ?></p></td>
                <td width="100"><p><? echo $buyer_short_name_arr[$row[('buyer_name')]]; ?></p></td>
                <td width="100"><p><? echo $row[('style_ref_no')]; ?></p></td>
				<td width="100"><p><? echo  $job_no; ?></p></td>
                <td width="100"><p><? echo  $po_numbers; ?></p></td>
				<td width="100" align="right"><p><? echo number_format($row[('po_qty_pcs')],0); ?></p></td>
				<td width="100" align="right"><p><? echo number_format($row[('sew_smv')],0); ?></p></td>
			 
				<td width="100" align="center"><p><? echo change_date_format($pub_shipment_dateMax).'<hr>Template Lead Time:'.$deadline.'<hr> Po Lead Time:'.($po_lead_timeMax-1); ?></p></td>
                <td width="80" align="center"><p><? echo change_date_format($po_insert_dateMax);; ?></p></td>
				<td width="50" align="center"><p><? echo 'Plan<br><hr><br>Actual<hr><br>Commitment'; ?></p></td>
                <td width="70" align="center"><p>
				<? echo change_date_format($taskSql[0][TASK_START_DATE]).'<br><hr><br>'.change_date_format($taskSql[0][ACTUAL_START_DATE]).'<hr><br>'.change_date_format($taskSql[0][COMMIT_START_DATE]); ?></p> </td>
				<td width="70" align="center"><p><? //echo 'change_date_format($taskSql[0][TASK_FINISH_DATE])<br><hr><br>$change_date_format($taskSql[0][ACTUAL_FINISH_DATE]);<hr><br>change_date_format($taskSql[0][COMMIT_END_DATE])';
				echo change_date_format($taskSql[0][TASK_FINISH_DATE]).'<br><hr><br>'.change_date_format($taskSql[0][ACTUAL_FINISH_DATE]).'<hr><br>'.change_date_format($taskSql[0][COMMIT_END_DATE]);
				 ?></p></td>
        </tr>
        <?
		
		}
		?>
         <tr>
        <td colspan="13">&nbsp;
         
        </td>
        </tr>
        <tr>
        <td  align="center"> <b>Comment:</b> </td>
        <td align="left" colspan="10"> 
        <? echo $mer_comments;?>
        </td>
        <td colspan="2"  align="center">
       <b> <?
        echo 'Dependent Task<hr>'.$dependant_task;
		?>
        </b>
        </td>
        </tr>
    </table>
    <br>
    <?
    if($task_id==31 || $task_id==272 || $task_id==73 || $task_id==84 || $task_id==86 || $task_id==88 || $task_id==110) //Fab Booking,Fab. ETA/Fab Inhouse/Cuting Prod Done-84/Sewing Prod 86/ packing Fin
	{
		if(!empty($inquiry_id_arr))
		{
			$quaOfferQnty=0; $quaConfirmPrice=0; $quaConfirmPriceDzn=0; $quaPriceWithCommnPcs=0; $quaCostingPer=0; $quaCostingPerQty=0;
				
			  $sqlQc="select a.inquery_id,a.qc_no,a.offer_qty, 1 as costing_per, b.confirm_fob from qc_mst a, qc_confirm_mst b where a.qc_no=b.cost_sheet_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ".where_con_using_array($inquiry_id_arr,0,'a.inquery_id')."";
			$dataQc=sql_select($sqlQc);
			//print_r($dataQc);
			foreach($dataQc as $qcrow)
			{
				//echo $qcrow[csf('offer_qty')].'=';
				$inquiry_wise_jobQty=$inquiry_wise_jobQty_arr[$qcrow[csf('inquery_id')]];
				$quaOfferQnty=$jobQty;//$qcrow[csf('offer_qty')];
				$quaConfirmPrice=$qcrow[csf('confirm_fob')];
				$quaConfirmPriceDzn=$qcrow[csf('confirm_fob')];
				$quaPriceWithCommnPcs=$qcrow[csf('confirm_fob')];
				$quaCostingPerArr[$qcrow[csf('inquery_id')]]=$qcrow[csf('costing_per')];
				$qc_no=$qcrow[csf('qc_no')];
				$quaCostingPerQty=0; 
				//if($quaCostingPer==1) 
				$quaCostingPerQty=1;
			}
			unset($dataQc);
			
			$sql_cons_rate="select a.inquery_id,b.id, b.mst_id, b.item_id, b.type, b.particular_type_id, b.consumption, b.ex_percent, b.tot_cons, b.unit, b.is_calculation, b.rate, b.rate_data, b.value from qc_cons_rate_dtls b,qc_mst a where  b.mst_id=a.qc_no and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($inquiry_id_arr,0,'a.inquery_id')." order by b.id asc";
			$sql_result_cons_rate=sql_select($sql_cons_rate);
			$buyer_fabPurQty=$buyer_fabPurAmt=0;
			foreach ($sql_result_cons_rate as $rowConsRate)
			{
				if($rowConsRate[csf("type")]==1) //Fabric
				{
					if(($rowConsRate[csf("rate")]*1)>0 && $rowConsRate[csf('tot_cons')]>0)
					{
						//$edata=explode("~~",$rowConsRate[csf("rate_data")]);
						$quaOfferQnty=$inquiry_wise_jobQty_arr[$rowConsRate[csf('inquery_id')]];
						$quaCostingPerQty=$quaCostingPerArr[$rowConsRate[csf('inquery_id')]];
						$index=""; $mktcons=$mktamt=0;
						$mktcons=$mktamt=0;
						//$mktcons=($rowConsRate[csf('tot_cons')]/$quaCostingPerQty)*($quaOfferQnty);
						$mktcons=$rowConsRate[csf('tot_cons')];
						$mktamt=$mktcons*$rowConsRate[csf('rate')];
						$ConsRate=$rowConsRate[csf('value')]/$rowConsRate[csf('tot_cons')];
						$buyer_fabPurQty+=$mktcons;
						$buyer_fabPurAmt+=$ConsRate*$mktcons;
					}
					
				}
			}
		}
		$sql_fab_budget = "select id, job_no,item_number_id,uom, body_part_id, fab_nature_id, color_type_id, fabric_description, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active from wo_pre_cost_fabric_cost_dtls where fabric_source=2 and status_active=1 and is_deleted=0  and job_no='$job_no'";
		$data_fabPur=sql_select($sql_fab_budget);
		$fabric_booking_cons=0;
		foreach($data_fabPur as $row)
		{
			$fabric_booking_cons+=$row[csf('avg_cons')];
		}
		unset($data_fabPur);
		
			$wv_recv_qnty_arr=array();$wv_issue_qnty_arr=array();
			$wv_fin_sql=sql_select("SELECT po_breakdown_id as po_id, entry_form,(quantity) as qnty from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(18,17,19)  ".where_con_using_array($po_id_arr,0,'po_breakdown_id')." ");
		//echo "SELECT po_breakdown_id as po_id, entry_form,(quantity) as qnty from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(18,17,19)  ".where_con_using_array($po_id_arr,0,'po_breakdown_id')." ";
			$wv_issue_fin=$wv_recv_qnty=0;
			foreach($wv_fin_sql as $row)
			{		 	
			 	$entry_form_id=$row[csf("entry_form")];
				if($entry_form_id==17)
				{
				$wv_recv_qnty+=$row[csf("qnty")];
				}
				if($entry_form_id==18 || $entry_form_id==19)  //Issue
				{
				$wv_issue_fin+=$row[csf("qnty")];
				}
			}
			unset($wv_fin_sql);
			
		$sql_cut=sql_select("select d.id,a.job_no,d.cutting_no as cutting_no,d.marker_length,d.cad_marker_cons,c.marker_qty from wo_po_details_master a, ppl_cut_lay_mst d,ppl_cut_lay_dtls c where a.job_no=d.job_no   and d.id=c.mst_id and d.entry_form=289 and c.status_active=1 and c.is_deleted=0  and a.job_no='$job_no' order by d.id asc");
	
		foreach($sql_cut as $row)
		{
			//$master_ref=$master_ref_arr[$row[csf('job_no')]];
			$cut_no_lay_arr[$row[csf('job_no')]]['cutting_no'].=$row[csf('cutting_no')].',';
			$cut_lay_arr[$row[csf('job_no')]]['lay_cut']+=$row[csf('marker_qty')];
			if($row[csf('marker_length')])
			{
			$cut_lay_first_arr[$row[csf('cutting_no')]]['first_length']=$row[csf('marker_length')];
			}
			if($row[csf('cad_marker_cons')])
			{
			$cut_lay_first_arr[$row[csf('cutting_no')]]['cad_marker_cons']=$row[csf('cad_marker_cons')];
			}
		}
		unset($sql_cut);
		$sew_defect_sql=sql_select("select b.po_break_down_id as po_id,
	  (CASE WHEN  b.production_type in(5) THEN b.defect_qty else 0 END)  as defect_qty,
	  (CASE WHEN  b.production_type in(1) THEN b.defect_qty else 0 END)  as cut_defect_qty,
	   (CASE WHEN  b.production_type in(80) THEN b.defect_qty else 0 END)  as fin_defect_qty
	  from pro_gmts_prod_dft b
	where  b.production_type in(1,5,80) and  b.status_active=1 and b.is_deleted=0  ".where_con_using_array($po_id_arr,0,'b.po_break_down_id')."");
	$cuting_defect_qty=$sewing_defect_qty=0;
	foreach($sew_defect_sql as $row)
	{
		$sewing_defect_qty+=$row[csf("defect_qty")];
		$fin_defect_arr[$row[csf("po_id")]]+=$row[csf("fin_defect_qty")];
		$cuting_defect_qty+=$row[csf("cut_defect_qty")];
	}
	//echo $sewing_defect_qty;
	//print_r($cuting_defect_arr);
		$cut_qc_arr=sql_select("SELECT a.po_break_down_id as po_id,(b.production_qnty) as qnty,b.reject_qty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=1 and b.production_type=1 and b.cut_no is not null and b.color_size_break_down_id is not null and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($po_id_arr,0,'a.po_break_down_id')." ");
		//echo "SELECT a.po_break_down_id as po_id,(b.production_qnty) as qnty,b.reject_qty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=1 and b.production_type=1 and b.cut_no is not null and b.color_size_break_down_id is not null and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($po_id_arr,0,'a.po_break_down_id')." ";
	 $cuting_prod_qty=$cuting_reject_qty=0;
		foreach( $cut_qc_arr as $row )
		{
			$cuting_prod_qty+=$row[csf("qnty")];
			$cuting_reject_qty+=$row[csf("reject_qty")];
		}
		$cuting_reject_qty=0;
		$cuting_reject_qty=$cuting_defect_qty;
		unset($cut_qc_arr);
		//reject_qnty,production_quantity
		 $sew_out_sql=sql_select("select a.po_break_down_id as po_id,
		 a.reject_qnty as all_reject,
		(CASE WHEN  a.production_type=4 THEN a.production_quantity else 0 END)  as sewing_in,
	 	(CASE WHEN  a.production_type=4 THEN a.reject_qnty else 0 END)  as rej_sewing_in,
	 	(CASE WHEN  a.production_type=5 THEN a.production_quantity else 0 END)  as sewing_out,
		(CASE WHEN  a.production_type=5 THEN a.reject_qnty else 0 END)  as rej_sewing_out,
		(CASE WHEN  a.production_type=2 THEN a.production_quantity else 0 END)  as send_wash,
		(CASE WHEN  a.production_type=9 THEN a.reject_qnty else 0 END)  as cutting_del_input_qty,
		(CASE WHEN  a.production_type=80 THEN a.production_quantity else 0 END)  as fin_qty,
		(CASE WHEN  a.production_type=80 THEN a.reject_qnty else 0 END)  as rej_fin_qty
		from pro_garments_production_mst a
		where a.production_type in(2,3,4,5,9,80) and a.status_active=1 and a.is_deleted=0  ".where_con_using_array($po_id_arr,0,'po_break_down_id')."");
		 
		 
		$cuting_del_input_qty=$sewing_in_qty=$sewing_out_qty=$rej_sewing_qty=$send_wash_qty=$fin_qty_qty=$rej_fin_qty=$all_rej_qty=0;
		foreach($sew_out_sql as $row)
		{
		$cuting_del_input_qty+=$row[csf("cutting_del_input_qty")];
		$sewing_in_qty+=$row[csf("sewing_in")];
		$sewing_out_qty+=$row[csf("sewing_out")];
		$send_wash_qty+=$row[csf("send_wash")];
		$fin_qty_qty+=$row[csf("fin_qty")];
		$rej_fin_qty+=$row[csf("rej_fin_qty")];
		//echo $row[csf("rej_sewing_out")]+$row[csf("rej_sewing_in")].'DDD';
		$rej_sewing_qty+=$row[csf("rej_sewing_out")]+$row[csf("rej_sewing_in")];
		$all_rej_qty+=$row[csf("all_reject")]+$row[csf("all_reject")];
		}
		unset($sew_out_sql);
		
		$act_ahip_arr=sql_select("select po_break_down_id,
		sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as shipout_qty,
		sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as return_qnty
		from  pro_ex_factory_mst where status_active=1 and is_deleted=0 ".where_con_using_array($po_id_arr,0,'po_break_down_id')." group by po_break_down_id");// 
		$actual_shipout_qty=0;
		foreach($act_ahip_arr as $row)
		{
			$actual_shipout_qty+=$row[csf('shipout_qty')]-$row[csf('return_qnty')];
		}
		 unset($act_ahip_arr);
		
			$cutting_no=$cut_no_lay_arr[$job_no]['cutting_no'];
			$cut_lay_qty=$cut_lay_arr[$job_no]['lay_cut'];
			$cutting_noS=rtrim($cutting_no,',');$cutting_noArr=array_unique(explode(",",$cutting_noS));
			$min_cutting_no=min($cutting_noArr);
			$cad_marker_cons=$cut_lay_first_arr[$min_cutting_no]['cad_marker_cons']/12;
		    $used_yds=$cut_lay_qty*$cad_marker_cons;
			$leftOver_fabYds=$wv_issue_fin-$used_yds;
			$actual_cost_vari_pcs=($wv_issue_fin-$leftOver_fabYds)/$cut_lay_qty;
			$costing_vs_actual_pcs=($booking_fabric_Qty-$cad_marker_cons);
			$plan_cut=$master_stlye_arr[$job_no]['plan_cut'];
			
			$booking_cost_cons_yds=$fabric_booking_cons*$plan_cut;
			
			$tot_vari_actual_cons_yds=$actual_cost_vari_pcs*$cut_lay_qty;
			
			
	  //echo $task_id.'DD';		
	?>
      <table width="1100" border="0" cellpadding="2" cellspacing="0" >
      <?
       if($task_id==31 || $task_id==271 || $task_id==272 || $task_id==73)
		 {
	  ?>
      <tr>
      <td>
         <table width="550"  style="margin:5px;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
            <thead>
                  <tr>
                    <th colspan="5">Total Consumption Varriance Yds/Pcs </th>
                  </tr>
                <tr>
                    <th>Buyer Cons<br>Yds</th>
                    <th>Booking Cons<br>Yds</th>
                    <th>Actual Cons</th>
                    <th>Budget VS Actual</th>
                    <th>Marker VS Actual</th>
                    
                </tr>
            </thead>
            <tr>
            <td align="right"> <? echo number_format($buyer_fabPurQty,4);?></td>
            <td align="right"><? echo number_format($fabric_booking_cons,4);?> </td>
            <td align="right" title="Issue-LeftOver/Cut Lay"><? echo number_format($actual_cost_vari_pcs,4);?> </td>
            <td align="right" title="Booking Cons-Marker Cons(<? echo $cad_marker_cons;?>)"><? $budget_vs_actual=$fabric_booking_cons-$cad_marker_cons;echo number_format($budget_vs_actual,4);?> </td>
            <td align="right" title="Actual Cons-Marker Cons"><? 
			$marker_cons=$actual_cost_vari_pcs-$cad_marker_cons;
			echo number_format($marker_cons,4);?> </td>
            </tr>
            </table>
        </td>
         <td>
         <table width="550" border="1" style="margin:5px;" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
            <thead>
                  <tr>
                    <th colspan="5">Total Consumption Varriance Yds </th>
                  </tr>
                <tr>
                    <th>Buyer Cons<br>Yds</th>
                    <th>Booking Cons<br>Yds</th>
                    <th>Actual Cons</th>
                    <th>Budget VS Actual</th>
                    <th>Marker VS Actual</th>
                    
                </tr>
            </thead>
            <tr>
              <td align="right" title="BuyerCons*Plan Cut"> <? $tot_buyer_cons=$buyer_fabPurQty*$plan_cut;echo number_format($tot_buyer_cons,2);?></td>
            <td align="right" title="BookingCons*Plan Cut"><? echo number_format($booking_cost_cons_yds,2);?> </td>
             <td align="right" title="ActualCons*Lay Cut"> <? echo number_format($tot_vari_actual_cons_yds,2);?></td>
            <td align="right" title="Booking Cons-Actual"><? echo number_format($booking_cost_cons_yds-$tot_vari_actual_cons_yds,2);?> </td>
          <td align="right" title="Marker Cons(<? echo $cad_marker_cons;?>)*Lay Cut(<? echo $cut_lay_qty;?>)-Actual Cons"><? $vari_marker_cons=($cad_marker_cons*$cut_lay_qty)-$tot_vari_actual_cons_yds;
			echo number_format($vari_marker_cons,2);
			//echo number_format($fabric_booking_cons,4);?> </td>
            </tr>
            </table>
        </td>
        </tr>
        <?
	}
		?>
         <tr>
            <td>&nbsp;
            
            </td>
            </tr>
            <tr>
            <td>
            <?
			if($task_id==73) //Fab Inhouse
			{
				$fin_fab_trans_array=array();
			  $sql_fin_trans="select b.trans_type, b.po_breakdown_id,b.prod_id, sum(b.quantity) as qnty,sum(CASE WHEN b.trans_type=5 THEN b.quantity END) AS in_qty,
				sum(CASE WHEN b.trans_type=6 THEN b.quantity END) AS out_qty, c.from_order_id
			  from inv_transaction a, order_wise_pro_details b,inv_item_transfer_mst c where a.id=b.trans_id and c.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and c.transfer_criteria!=2 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(258,15) and a.item_category=3 and a.transaction_type in(5,6) and b.trans_type in(5,6)  ".where_con_using_array($po_id_arr,0,'b.po_breakdown_id')." group by b.trans_type, b.po_breakdown_id,c.from_order_id,b.prod_id";
			$result_fin_trans=sql_select( $sql_fin_trans );
			 $fin_in_qty=$fin_out_qty=$fin_in_qnty=$fin_out_qnty=$fin_in_amt=$fin_out_amt=0;
			foreach ($result_fin_trans as $row)
			{
				$fin_in_qty+=$row[csf('in_qty')];
				$fin_out_qty+=$row[csf('out_qty')];
			}
			$tot_transfer_qty=$fin_in_qty-$fin_out_qty;
			
			$balance_in_storeQty=($wv_recv_qnty+$fin_in_qty)-($wv_issue_fin+$fin_out_qty);
				?>
                <table width="550" border="1" style="margin:5px;" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
            <thead>
                  <tr>
                    <th colspan="8">Fabric Receive Status </th>
                  </tr>
                <tr>
                    <th>Required(Yds)</th>
                    <th>Receive (Yds)</th>
                    <th>Transfer In (Yds)</th>
                    <th>Transfer Out (Yds)</th>
                    <th>Issue (Yds)</th>
                    <th>Used (Yds)</th>
                    <th>Laftover Fab.(Yds)</th>
                    <th>Balance In Store</th>
                    
                </tr>
            </thead>
            <tr>
              <td align="right" title="Booking Cons*Plan Cut"> <? $fab_red_qty=$fabric_booking_cons*$plan_cut;echo number_format($fab_red_qty,2);?></td>
          	  <td align="right" title=""><? echo number_format($wv_recv_qnty,2);?> </td>
             <td align="right" title=""> <? echo number_format($fin_in_qty,2);?></td>
            <td align="right" title=""><? echo number_format($fin_out_qty,2);?> </td>
          <td align="right" title=""><? 
			echo number_format($wv_issue_fin,2);
			//echo number_format($fabric_booking_cons,4);?> </td>
             <td align="right" title=""> <? echo number_format($used_yds,2);?></td>
              <td align="right" title=""> <? echo number_format($leftOver_fabYds,2);?></td>
               <td align="right" title="wvn Recv+Transfer In-Issue-Out"> <? echo number_format($balance_in_storeQty,2);?></td>
            </tr>
            </table>
                
			<?	
			}
			?>  
             <?
			if($task_id==84) //Cutting Prod
			{
				$fin_fab_trans_array=array();
				  $sql_fin_trans="select b.trans_type, b.po_breakdown_id,b.prod_id, sum(b.quantity) as qnty,sum(CASE WHEN b.trans_type=5 THEN b.quantity END) AS in_qty,
					sum(CASE WHEN b.trans_type=6 THEN b.quantity END) AS out_qty, c.from_order_id
				  from inv_transaction a, order_wise_pro_details b,inv_item_transfer_mst c where a.id=b.trans_id and c.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and c.transfer_criteria!=2 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(258,15) and a.item_category=3 and a.transaction_type in(5,6) and b.trans_type in(5,6)  ".where_con_using_array($po_id_arr,0,'b.po_breakdown_id')." group by b.trans_type, b.po_breakdown_id,c.from_order_id,b.prod_id";
				$result_fin_trans=sql_select( $sql_fin_trans );
				 $fin_in_qty=$fin_out_qty=$fin_in_qnty=$fin_out_qnty=$fin_in_amt=$fin_out_amt=0;
				foreach ($result_fin_trans as $row)
				{
					$fin_in_qty+=$row[csf('in_qty')];
					$fin_out_qty+=$row[csf('out_qty')];
				}
				$tot_transfer_qty=$fin_in_qty-$fin_out_qty;
				$balance_in_storeQty=($wv_recv_qnty+$fin_in_qty)-($wv_issue_fin+$fin_out_qty);
				?>
                <table width="550" border="1" style="margin:5px;" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
            	<thead>
                  <tr>
                    <th colspan="6">Cutting Production Status(Pcs)</th>
                  </tr>
                <tr>
                    <th>Cut & Lay Qty</th>
                    <th>QC Pass Qty.</th>
                    <th>Defect Qty.</th>
                    <th>DHU %</th>
                    <th>Excess Cut%</th>
                    <th>Delivery To Input</th>
                </tr>
            </thead>
            <tr>
            <td align="right" title=""> <?  echo number_format($cut_lay_qty,2);?></td>
            <td align="right" title=""><? echo number_format($cuting_prod_qty,2);?> </td>
            <td align="right" title=""> <? echo number_format($cuting_reject_qty,2);?></td>
            <td  align="right" title="Count Of Defect Qty/Audit Qty*100"><p><?  $dhu_per=($cuting_reject_qty/$cuting_prod_qty*100);echo number_format($dhu_per,2); ?></p></td>
            <td align="right" title="(Lay Cut-Po Qty)/(Po Qty*100)"><p><? $excess_cut_per=(($cut_lay_qty-$po_qty_pcs))/$po_qty_pcs*100; echo $excess_cut_per; ?></p></td>
            
            <td align="right" title=""> <?  $cuting_del_input_qty=$sewing_in_qty; echo number_format($cuting_del_input_qty,2);?></td>
            
            </tr>
            </table>
                
			<?	
			}
			?>  
            <?
			if($task_id==86) //Sewing Prod
			{
				$fin_fab_trans_array=array();
				  $sql_fin_trans="select b.trans_type, b.po_breakdown_id,b.prod_id, sum(b.quantity) as qnty,sum(CASE WHEN b.trans_type=5 THEN b.quantity END) AS in_qty,
					sum(CASE WHEN b.trans_type=6 THEN b.quantity END) AS out_qty, c.from_order_id
				  from inv_transaction a, order_wise_pro_details b,inv_item_transfer_mst c where a.id=b.trans_id and c.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and c.transfer_criteria!=2 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(258,15) and a.item_category=3 and a.transaction_type in(5,6) and b.trans_type in(5,6)  ".where_con_using_array($po_id_arr,0,'b.po_breakdown_id')." group by b.trans_type, b.po_breakdown_id,c.from_order_id,b.prod_id";
				$result_fin_trans=sql_select( $sql_fin_trans );
				 $fin_in_qty=$fin_out_qty=$fin_in_qnty=$fin_out_qnty=$fin_in_amt=$fin_out_amt=0;
				foreach ($result_fin_trans as $row)
				{
					$fin_in_qty+=$row[csf('in_qty')];
					$fin_out_qty+=$row[csf('out_qty')];
				}
				$tot_transfer_qty=$fin_in_qty-$fin_out_qty;
				$balance_in_storeQty=($wv_recv_qnty+$fin_in_qty)-($wv_issue_fin+$fin_out_qty);
				?>
                <table width="550" border="1" style="margin:5px;" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
            	<thead>
                  <tr>
                    <th colspan="5">Sewing Production Status (Pcs)</th>
                  </tr>
                <tr>
                    <th>Input Qty.</th>
                    <th>QC Pass Qty.</th>
                    <th>Reject Qty.</th>
                    <th>DHU %</th>
                    <th>Send Wash</th>
                    
                </tr>
            </thead>
            <tr>
            <td align="right" title=""> <?  echo number_format($sewing_in_qty,2);?></td>
            <td align="right" title=""><? echo number_format($sewing_out_qty,2);?> </td>
            <td align="right" title=""> <? echo number_format($rej_sewing_qty,2);?></td>
            <td  align="right" title="Count Of Defect Qty(<? echo $sewing_defect_qty;?>)/Audit Qty*100"><p><?  $dhu_per=($sewing_defect_qty/$sewing_out_qty*100);echo number_format($dhu_per,2); ?></p></td>
            <td align="right" title=""> <?  echo number_format($send_wash_qty,2);?></td>
            
            </tr>
            </table>
                
			<?	
			}
			?>  
             <?
			if($task_id==88) //Packing Finishing Prod
			{
				$wash_recv_sql=sql_select("select b.buyer_po_id as po_id, b.quantity  as wash_qty
				from sub_material_mst a,sub_material_dtls b
				where  a.id=b.mst_id and  a.entry_form=296  and  b.status_active=1 and b.is_deleted=0  ".where_con_using_array($po_id_arr,0,'b.buyer_po_id')."");
				
				foreach($wash_recv_sql as $row)
				{
				$wash_recv_arr[$row[csf("po_id")]]+=$row[csf("wash_qty")];
				}
				unset($wash_recv_sql);
				$wash_recv_from_qty=$wash_wet_prod_qty=$wash_recv_from_reject=0;
				$wash_prod_sql=sql_select("select a.entry_form,b.buyer_po_id as po_id, b.qcpass_qty  as wash_qty,b.reje_qty
				from subcon_embel_production_mst a,subcon_embel_production_dtls b
				where  a.id=b.mst_id and  a.entry_form in(301,302)  and  b.status_active=1 and b.is_deleted=0  ".where_con_using_array($po_id_arr,0,'b.buyer_po_id')."");
				foreach($wash_prod_sql as $row)
				{
				if($row[csf("entry_form")]==301)
				{
				$wash_wet_prod_qty+=$row[csf("wash_qty")];
				}
				else{
				$wash_recv_from_qty+=$row[csf("wash_qty")]; 
				$wash_recv_from_reject+=$row[csf("reje_qty")];
				}
				} //wash_recv_from_qty
				unset($wash_prod_sql); 
				//print_r($cuting_prod_arr);subcon_delivery_mst
				$wash_del_sql=sql_select("select b.buyer_po_id as po_id, b.delivery_qty  as wash_qty
				from subcon_delivery_mst a,subcon_delivery_dtls b
				where  a.id=b.mst_id and  a.entry_form=303  and  b.status_active=1 and b.is_deleted=0  ".where_con_using_array($po_id_arr,0,'b.buyer_po_id')."");
				$wash_del_qty=0;
				foreach($wash_del_sql as $row)
				{
				$wash_del_qty+=$row[csf("wash_qty")];
				}
				unset($wash_del_sql);
				?>
                <table width="550" border="1" style="margin:5px;" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
            	<thead>
                  <tr>
                    <th colspan="3">Wash Status</th>
                     <th colspan="5">Finishing Status</th>
                  </tr>
                <tr>
                    <th>Wash Receive</th>
                    <th>Wash Prod.</th>
                    <th>Issue To Finishing</th>
                    
                    <th>From Wash</th>
                    <th>QC Pass Qty.</th>
                     <th>Wash Reject</th>
                    <th>Finishing Reject</th>
                    <th>DHU %</th>
                    
                </tr>
            </thead>
            <tr>
            <td align="right" title=""> <?  echo number_format($wash_recv_qc_qty,2);?></td>
            <td align="right" title=""><? echo number_format($wash_wet_prod_qty,2);?> </td>
            <td align="right" title=""> <? echo number_format($wash_del_qty,2);?></td>
            <td  align="right" title="Wash From Recv"><p><? echo number_format($wash_recv_from_qty,2); ?></p></td>
            <td align="right" title=""> <?  echo number_format($fin_qty_qty,2);?></td>
            
            <td align="right" title=""> <?  echo number_format($wash_recv_from_reject,2);?></td>
            <td align="right" title=""> <?  echo number_format($rej_fin_qty,2);?></td>
            <td align="right" title=""> <? $fin_defect_per=$rej_fin_qty/$fin_qty_qty*100;  echo number_format($fin_defect_per,2);?></td>
            
            
            </tr>
            </table>
                
			<?	
			}
			?>  
              <?
			
			if($task_id==110) //Ex-Factory/Hand Over
			{
				/*$wash_recv_sql=sql_select("select b.buyer_po_id as po_id, b.quantity  as wash_qty
				from sub_material_mst a,sub_material_dtls b
				where  a.id=b.mst_id and  a.entry_form=296  and  b.status_active=1 and b.is_deleted=0  ".where_con_using_array($po_id_arr,0,'b.buyer_po_id')."");
				
				foreach($wash_recv_sql as $row)
				{
				$wash_recv_arr[$row[csf("po_id")]]+=$row[csf("wash_qty")];
				}
				unset($wash_recv_sql);
				$wash_recv_from_qty=$wash_wet_prod_qty=$wash_recv_from_reject=0;
				$wash_prod_sql=sql_select("select a.entry_form,b.buyer_po_id as po_id, b.qcpass_qty  as wash_qty,b.reje_qty
				from subcon_embel_production_mst a,subcon_embel_production_dtls b
				where  a.id=b.mst_id and  a.entry_form in(301,302)  and  b.status_active=1 and b.is_deleted=0  ".where_con_using_array($po_id_arr,0,'b.buyer_po_id')."");
				foreach($wash_prod_sql as $row)
				{
				if($row[csf("entry_form")]==301)
				{
				$wash_wet_prod_qty+=$row[csf("wash_qty")];
				}
				else{
				$wash_recv_from_qty+=$row[csf("wash_qty")]; 
				$wash_recv_from_reject+=$row[csf("reje_qty")];
				}
				} //wash_recv_from_qty
				unset($wash_prod_sql); 
				//print_r($cuting_prod_arr);subcon_delivery_mst
				$wash_del_sql=sql_select("select b.buyer_po_id as po_id, b.delivery_qty  as wash_qty
				from subcon_delivery_mst a,subcon_delivery_dtls b
				where  a.id=b.mst_id and  a.entry_form=303  and  b.status_active=1 and b.is_deleted=0  ".where_con_using_array($po_id_arr,0,'b.buyer_po_id')."");
				$wash_del_qty=0;
				foreach($wash_del_sql as $row)
				{
				$wash_del_qty+=$row[csf("wash_qty")];
				}
				unset($wash_del_sql);*/
				$short_excess_qty_per=($actual_shipout_qty/$po_qty_pcs*100)-100; 
				$sample_pcs=16;
				 $cut_to_shipQty=($actual_shipout_qty-$cut_lay_qty)+$sample_pcs;
				 
				  $cut_to_shipRatio_per=($actual_shipout_qty/$cut_lay_qty)*100;
				?>
                <table width="550" border="1" style="margin:5px;" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
            	<thead>
                  <tr>
                    <th colspan="7">Shipment Status / Pcs</th>
                    
                  </tr>
                <tr>
                    <th>Ex-Factory Qty.</th>
                    <th>Short / Excess Qty.</th>
                    <th>Short/ Excess %</th>
                    
                    <th>Cut To Ship Qty.(Short/Excess)</th>
                    <th>Cut To Ship Ratio %</th>
                    <th>Total Leftover Qty.</th>
                    <th>Total Reject Qty.</th>
                </tr>
            </thead>
            <tr>
            <td align="right" title=""> <?  echo number_format($actual_shipout_qty,2);?></td>
            <td align="right" title="Ex-Fact Qty-PO Qty Pcs"><? echo number_format($actual_shipout_qty-$po_qty_pcs,2);?> </td>
            <td align="right" title="(Ex-Fact/Po Qty Pcs*100)-100"> <? echo number_format($short_excess_qty_per,2);?></td>
            <td  align="right" title="Ex-Fact-Lay Cut+Sample"><p><? echo number_format($cut_to_shipQty,2); ?></p></td>
            <td align="right" title="Ex-Fact/Lay Cut*100"> <?  echo number_format($cut_to_shipRatio_per,2);?></td>
            <td align="right" title="Sewing Out-ShipOut"> <?  echo number_format($sewing_out_qty-$actual_shipout_qty,2);?></td>
            <td align="right" title="All Reject"> <?  echo number_format($all_rej_qty,2);?></td>
            </tr>
            </table>
            
                
			<?	
			}
			?>  
             </td>
            </tr>
            
        </table>
        <?
	}
		?>
    </div>
    <?
	exit();
}

if($action=='graph_task_poup')
{
	
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);

	list($task_id,$job_no)=explode('*',$task_data_str);
	
	$taskSql =sql_select("select JOB_NO,MIN(TASK_START_DATE) AS TASK_START_DATE,MIN(TASK_FINISH_DATE) AS TASK_FINISH_DATE,MIN(ACTUAL_START_DATE) AS ACTUAL_START_DATE,MIN(ACTUAL_FINISH_DATE) AS ACTUAL_FINISH_DATE,MIN(COMMIT_START_DATE) AS COMMIT_START_DATE,MIN(COMMIT_END_DATE) AS COMMIT_END_DATE from tna_process_mst where JOB_NO='$job_no' and TASK_NUMBER='$task_id' and TASK_TYPE=1 GROUP BY JOB_NO"); 
	
	//echo "select JOB_NO,MIN(TASK_START_DATE) AS TASK_START_DATE,MIN(TASK_FINISH_DATE) AS TASK_FINISH_DATE,MIN(ACTUAL_START_DATE) AS ACTUAL_START_DATE,MIN(ACTUAL_FINISH_DATE) AS ACTUAL_FINISH_DATE,MIN(COMMIT_START_DATE) AS COMMIT_START_DATE,MIN(COMMIT_END_DATE) AS COMMIT_END_DATE from tna_process_mst where JOB_NO='$job_no' and TASK_NUMBER='$task_id' and TASK_TYPE=1 GROUP BY JOB_NO";
	
	
	?>
    <div style="width:300px" align="left"> 
    <table width="100%" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
    	<thead>
        	<tr><th width="150">Plan Start Date</th><td align="center"><?= change_date_format($taskSql[0][TASK_START_DATE]);?></td></tr>
            <tr><th>Plan Finish Date</th><td align="center"><?= change_date_format($taskSql[0][TASK_FINISH_DATE]);?></td></tr>
            <tr><th>Actual Start Date</th><td align="center"><?= change_date_format($taskSql[0][ACTUAL_START_DATE]);?></td></tr>
            <tr><th>Actual Finish Date</th><td align="center"><?= change_date_format($taskSql[0][ACTUAL_FINISH_DATE]);?></td></tr>
            
            <tr><th>Commitment Start Date</th><td align="center"><?= change_date_format($taskSql[0][COMMIT_START_DATE]);?></td></tr>
            <tr><th>Commitment Finish Date</th><td align="center"><?= change_date_format($taskSql[0][COMMIT_END_DATE]);?></td></tr>
            
        </thead>
    </table>
    </div>
    <?
	exit();
}

if($action=='graph_task_poup_yarn_in_house')
{
	
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);

	list($task_id,$job_no)=explode('*',$task_data_str);
	
	$taskSql =sql_select("select JOB_NO,MIN(TASK_START_DATE) AS TASK_START_DATE,MIN(TASK_FINISH_DATE) AS TASK_FINISH_DATE,MIN(ACTUAL_START_DATE) AS ACTUAL_START_DATE,MIN(ACTUAL_FINISH_DATE) AS ACTUAL_FINISH_DATE,MIN(COMMIT_START_DATE) AS COMMIT_START_DATE,MIN(COMMIT_END_DATE) AS COMMIT_END_DATE from tna_process_mst where JOB_NO='$job_no' and TASK_NUMBER='$task_id' and TASK_TYPE=1 GROUP BY JOB_NO"); 
	
	
	?>
    <div style="width:300px" align="left"> 
    <table width="100%" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
    	<thead>
        	<tr><th width="150">Plan Start Date</th><td align="center"><?= change_date_format($taskSql[0][TASK_START_DATE]);?></td></tr>
            <tr><th>Plan Finish Date</th><td align="center"><?= change_date_format($taskSql[0][TASK_FINISH_DATE]);?></td></tr>
            <tr><th>Actual Start Date</th><td align="center"><?= change_date_format($taskSql[0][ACTUAL_START_DATE]);?></td></tr>
            <tr><th>Actual Finish Date</th><td align="center"><?= change_date_format($taskSql[0][ACTUAL_FINISH_DATE]);?></td></tr>
            
            <tr><th>Commitment Start Date</th><td align="center"><?= change_date_format($taskSql[0][COMMIT_START_DATE]);?></td></tr>
            <tr><th>Commitment Finish Date</th><td align="center"><?= change_date_format($taskSql[0][COMMIT_END_DATE]);?></td></tr>
            
        </thead>
    </table>
    </div> 
    <div style="width:600px" align="left"> 
    <?
	$sql="SELECT a.WO_NUMBER,
       c.PI_ID,
       c.UOM AS PI_UOM,
       c.QUANTITY AS PI_QUANTITY,
       c.AMOUNT AS PI_AMOUNT,
       d.PI_NUMBER,
       e.RECV_NUMBER_PREFIX_NUM AS MRR,
       e.ID AS REC_ID,
       e.CURRENCY_ID,
       f.CONS_QUANTITY AS REC_QUANTITY,
       f.CONS_AMOUNT AS REC_AMOUNT,
       f.CONS_UOM AS REC_UOM,
	   f.JOB_NO
  FROM WO_NON_ORDER_INFO_MST a,
       WO_NON_ORDER_INFO_DTLS b,
       COM_PI_ITEM_DETAILS c,
       com_pi_master_details d,
       INV_RECEIVE_MASTER e,
       inv_transaction f
 WHERE     a.id = b.mst_id
       AND a.id = c.WORK_ORDER_ID
       AND c.pi_id = d.id
       AND d.id = e.booking_id
       AND e.id = f.mst_id
       AND e.RECEIVE_BASIS = 1
       AND e.ITEM_CATEGORY = 1
       AND f.JOB_NO = b.JOB_NO
       AND b.JOB_NO = '$job_no'";
	//echo $sql;
	
	$yarnInhouseSql =sql_select($sql);
	foreach($yarnInhouseSql as $rows){
		$yarnInhouseDataArr[PI_NO][$rows[PI_NUMBER]]=$rows[PI_NUMBER];
		//$yarnInhouseDataArr[PI_UOM][$rows[PI_ID]]=$unit_of_measurement[$rows[PI_UOM]];
		$yarnInhouseDataArr[PI_UOM][$rows[PI_UOM]]=$unit_of_measurement[$rows[PI_UOM]];
		$yarnInhouseDataArr[PI_QTY][$rows[PI_ID]]=$rows[PI_QUANTITY];
		$yarnInhouseDataArr[PI_AMOUNT][$rows[PI_ID]]=$rows[PI_AMOUNT];
		
		$yarnInhouseDataArr[REC_ID][$rows[MRR]]=$rows[MRR];
		//$yarnInhouseDataArr[REC_UOM][$rows[REC_ID]]=$unit_of_measurement[$rows[REC_UOM]];
		$yarnInhouseDataArr[REC_UOM][$rows[REC_UOM]]=$unit_of_measurement[$rows[REC_UOM]];
		//$yarnInhouseDataArr[REC_CURRENCY][$rows[REC_ID]]=$currency[$rows[CURRENCY_ID]];
		$yarnInhouseDataArr[REC_CURRENCY][$rows[CURRENCY_ID]]=$currency[$rows[CURRENCY_ID]];
		$yarnInhouseDataArr[REC_QTY][$rows[REC_ID]]=$rows[REC_QUANTITY];
		$yarnInhouseDataArr[REC_AMOUNT][$rows[REC_ID]]=$rows[REC_AMOUNT];
		
	}
	
	?>
    
    <br>
    <table width="100%" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
    	<thead>
        	<tr>
                 <th>PI No</th>	
                 <th>UOM</th>	
                 <th>PI.Qnty</th>	
                 <th>PI. Amount</th>	
                 <th>MRR No</th>	
                 <th>UOM</th>	
                 <th>Receive Qnty</th>	
                 <th>Receive Amount</th>	
                 <th>Currency</th>
            </tr>
        	<tr>
                 <td><?= implode(', ',$yarnInhouseDataArr[PI_NO]);?></td>	
                 <td align="center"><?= implode(', ',$yarnInhouseDataArr[PI_UOM]);?></td>	
                 <td align="center"><?= array_sum($yarnInhouseDataArr[PI_QTY]);?></td>	
                 <td align="center"><?= array_sum($yarnInhouseDataArr[PI_AMOUNT]);?></td>	
                 <td><?= implode(', ',$yarnInhouseDataArr[REC_ID]);?></td>	
                 <td align="center"><?= implode(', ',$yarnInhouseDataArr[REC_UOM]);?></td>	
                 <td align="center"><?= array_sum($yarnInhouseDataArr[REC_QTY]);?></td>	
                 <td align="center"><?= array_sum($yarnInhouseDataArr[REC_AMOUNT]);?></td>	
                 <td align="center"><?= implode(', ',$yarnInhouseDataArr[REC_CURRENCY]);?></td>
            </tr>
        </thead>
    </table>
    
    
    
    
    
    </div>
    <?
	exit();
}

if($action=='graph_task_poup_yarn_work_order')
{
	
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);

	list($task_id,$job_no)=explode('*',$task_data_str);
	
	$taskSql =sql_select("select JOB_NO,MIN(TASK_START_DATE) AS TASK_START_DATE,MIN(TASK_FINISH_DATE) AS TASK_FINISH_DATE,MIN(ACTUAL_START_DATE) AS ACTUAL_START_DATE,MIN(ACTUAL_FINISH_DATE) AS ACTUAL_FINISH_DATE,MIN(COMMIT_START_DATE) AS COMMIT_START_DATE,MIN(COMMIT_END_DATE) AS COMMIT_END_DATE from tna_process_mst where JOB_NO='$job_no' and TASK_NUMBER='$task_id' and TASK_TYPE=1 GROUP BY JOB_NO"); 
	
	
	?>
    <div style="width:300px" align="left"> 
    <table width="100%" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
    	<thead>
        	<tr><th width="150">Plan Start Date</th><td align="center"><?= change_date_format($taskSql[0][TASK_START_DATE]);?></td></tr>
            <tr><th>Plan Finish Date</th><td align="center"><?= change_date_format($taskSql[0][TASK_FINISH_DATE]);?></td></tr>
            <tr><th>Actual Start Date</th><td align="center"><?= change_date_format($taskSql[0][ACTUAL_START_DATE]);?></td></tr>
            <tr><th>Actual Finish Date</th><td align="center"><?= change_date_format($taskSql[0][ACTUAL_FINISH_DATE]);?></td></tr>
            
            <tr><th>Commitment Start Date</th><td align="center"><?= change_date_format($taskSql[0][COMMIT_START_DATE]);?></td></tr>
            <tr><th>Commitment Finish Date</th><td align="center"><?= change_date_format($taskSql[0][COMMIT_END_DATE]);?></td></tr>
            
        </thead>
    </table>
    </div> 
    <div style="width:600px" align="left"> 
    <?
	$sql="SELECT a.CURRENCY_ID,a.WO_NUMBER,
       b.REQUISITION_NO,
       b.REQ_QUANTITY,
       b.UOM AS REQ_UOM,
       b.SUPPLIER_ORDER_QUANTITY as WO_QUANTITY,
       b.AMOUNT as WO_AMOUNT,
       c.PI_ID,
       c.UOM AS PI_UOM,
       c.QUANTITY AS PI_QUANTITY,
       c.AMOUNT AS PI_AMOUNT,
       d.PI_NUMBER,
       e.LC_NUMBER
  FROM WO_NON_ORDER_INFO_MST a,
       WO_NON_ORDER_INFO_DTLS b,
       COM_PI_ITEM_DETAILS c,
       com_pi_master_details d
      LEFT JOIN COM_BTB_LC_PI  f  on d.id=f.pi_id 
      LEFT JOIN  COM_BTB_LC_MASTER_DETAILS e on  e.id=f.COM_BTB_LC_MASTER_DETAILS_ID
       
 WHERE     a.id = b.mst_id
       AND a.id = c.WORK_ORDER_ID
       AND b.mst_id = c.WORK_ORDER_ID
        AND c.pi_id = d.id
       AND b.JOB_NO = '$job_no'";
	   //echo $sql;
	
	$YarnWorkOrderSql =sql_select($sql);
	foreach($YarnWorkOrderSql as $rows){
		
		$YarnWorkOrderDataArr[REQ_NO][$rows[REQUISITION_NO]]=$rows[REQUISITION_NO];
		$YarnWorkOrderDataArr[REQ_UOM][$rows[REQ_UOM]]=$unit_of_measurement[$rows[REQ_UOM]];
		$YarnWorkOrderDataArr[REQ_QUANTITY][$rows[REQUISITION_NO]]=$rows[REQ_QUANTITY];

		$YarnWorkOrderDataArr[WO_NUMBER][$rows[WO_NUMBER]]=$rows[WO_NUMBER];
		$YarnWorkOrderDataArr[WO_QTY][$rows[WO_QUANTITY]]=$rows[WO_QUANTITY];
		$YarnWorkOrderDataArr[WO_AMOUNT][$rows[WO_NUMBER]]=$rows[WO_AMOUNT];
		//$YarnWorkOrderDataArr[WO_CURRENCY][$rows[WO_NUMBER]]=$currency[$rows[CURRENCY_ID]];
		$YarnWorkOrderDataArr[WO_CURRENCY][$rows[CURRENCY_ID]]=$currency[$rows[CURRENCY_ID]];
		
		$YarnWorkOrderDataArr[PI_NO][$rows[PI_NUMBER]]=$rows[PI_NUMBER];
		//$YarnWorkOrderDataArr[PI_UOM][$rows[PI_ID]]=$unit_of_measurement[$rows[PI_UOM]];
		$YarnWorkOrderDataArr[PI_UOM][$rows[PI_UOM]]=$unit_of_measurement[$rows[PI_UOM]];
		$YarnWorkOrderDataArr[PI_QTY][$rows[PI_ID]]=$rows[PI_QUANTITY];
		$YarnWorkOrderDataArr[PI_AMOUNT][$rows[PI_ID]]=$rows[PI_AMOUNT];
		
		
		//$YarnWorkOrderDataArr[LC_NUMBER][$rows[PI_ID]]=$rows[LC_NUMBER];
		$YarnWorkOrderDataArr[LC_NUMBER][$rows[LC_NUMBER]]=$rows[LC_NUMBER];
	
		
	}
	
	?>
    <br>
    <table width="100%" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
    	<thead>
        	<tr>
                <th>Requisition No	</th>
                <th>UOM</th>	
                <th>Req. Qnty</th>	
                <th>WO No</th>	
                <th>WO Qnty</th>	
                <th>WO Value</th>	
                <th>Currency</th>	
                <th>PI No</th>	
                <th>PI Qnty</th>	
                <th>PI Value</th>	
                <th>BTB No</th>
            </tr>
        	<tr>
                 <td><?= implode(', ',$YarnWorkOrderDataArr[REQ_NO]);?></td>	
                 <td align="center"><?= implode(', ',$YarnWorkOrderDataArr[REQ_UOM]);?></td>	
                 <td align="center"><?= array_sum($YarnWorkOrderDataArr[REQ_QUANTITY]);?></td>
                 <td><?= implode(', ',$YarnWorkOrderDataArr[WO_NUMBER]);?></td>	
                 <td align="center"><?= array_sum($YarnWorkOrderDataArr[WO_QTY]);?></td>	
                 <td align="center"><?= array_sum($YarnWorkOrderDataArr[WO_AMOUNT]);?></td>	
                 <td align="center"><?= implode(', ',$YarnWorkOrderDataArr[WO_CURRENCY]);?></td>
                 
                 	
                 <td><?= implode(', ',$YarnWorkOrderDataArr[PI_NO]);?></td>	
                 <td align="center"><?= array_sum($YarnWorkOrderDataArr[PI_QTY]);?></td>	
                 <td align="center"><?= array_sum($YarnWorkOrderDataArr[PI_AMOUNT]);?></td>	
                 <td align="center"><?= implode(', ',$YarnWorkOrderDataArr[LC_NUMBER]);?></td>	
            </tr>
        </thead>
    </table>
    
    
    
    
    
    </div>
    <?
	exit();
}






if($action=="generate_menual_date_change_report")
{

	if($graph==1){
		if($db_type==0)
		{
			$txt_date_from = date("'Y-m-d'",strtotime($txt_date_from));
			$txt_date_to = date("'Y-m-d'",strtotime($txt_date_to));
		}
		else
		{
			$txt_date_from = date("'d-M-Y'",strtotime($txt_date_from));
			$txt_date_to = date("'d-M-Y'",strtotime($txt_date_to));
		}
	}


$actual_manual_update_task_arr=return_library_array("select task_id,task_id from tna_manual_permission where is_actual_manual=1  and company_id=$cbo_company_name","task_id","task_id");
$plan_manual_update_task_arr=return_library_array("select task_id,task_id from tna_manual_permission where is_plan_manual=1  and company_id=$cbo_company_name","task_id","task_id");


	
	if( $tna_process_type==2 ){//Parcent base;
		$modSql= "select a.id,a.task_catagory,a.task_name,a.task_short_name,a.task_type,a.completion_percent,a.task_sequence_no
		from lib_tna_task a where a.is_deleted = 0 and a.status_active=1 AND task_type = 1 order by a.task_sequence_no asc";
	}
	else
	{
		$modSql="select a.id,a.task_catagory,a.task_name,a.task_short_name,a.task_type,a.completion_percent,a.task_sequence_no,b.task_template_id,b.lead_time 
		from lib_tna_task a,tna_task_template_details b where a.task_name=b.tna_task_id and b.task_type=1  AND a.task_type = 1 and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 order by a.task_sequence_no asc";
	}
	//echo $modSql;die;
	
	$mod_sql= sql_select($modSql);
	
	
	$tna_task_array=array();
	$tna_task_id=array();
	$tna_task_cat=array();
	$tna_task_name_arr=array();
	foreach ($mod_sql as $row)
	{
		$tna_task_id[$row[csf("task_name")]]=$row[csf("task_name")];
		$tna_task_array[$row[csf("id")]] =$row[csf("task_short_name")];
		$tna_task_name_array[$row[csf("id")]] =$tna_task_name[$row[csf("task_name")]];
		$tna_task_cat[$row[csf("id")]]=$row[csf("task_catagory")];
		$tna_task_name_arr[$row[csf("id")]]=$row[csf("task_name")];
		
		
		$lead_time_array[$row[csf("task_template_id")]]=$row[csf("lead_time")];
		$tast_tmp_id_arr[$row[csf("task_template_id")]][$row[csf("tna_task_id")]]=$row[csf("tna_task_id")];
		
		
	}
	$cbo_company_id=$cbo_company_name;
	
	$order_status_cond="";
	if(str_replace("'","",$cbo_order_status)>0) $order_status_cond=" and b.is_confirmed=$cbo_order_status";
	
	if(str_replace("'","",$cbo_company_name)==0) $cbo_company_name=""; else $cbo_company_name=" and a.company_name = $cbo_company_name";
	if(str_replace("'","",$cbo_buyer_name)==0) $cbo_buyer_name=""; else $cbo_buyer_name=" and a.buyer_name = $cbo_buyer_name";
	if(str_replace("'","",$cbo_team_member)==0) $cbo_team_member=""; else $cbo_team_member=" and a.dealing_marchant = $cbo_team_member";
	
	if(str_replace("'","",$cbo_search_type)==1){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
	}
	else if(str_replace("'","",$cbo_search_type)==3){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and c.country_ship_date between $txt_date_from and $txt_date_to";
	}
	else if(str_replace("'","",$cbo_search_type)==4){
		if($db_type==0)
		{
			if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)==""){$date_range="";}else{ 
			$date_range=" and b.insert_date between ".$txt_date_from." and ".$txt_date_to."";}
		}
		else
		{
			
			if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)==""){$date_range="";}else{ 
			$date_range=" and b.insert_date between ".$txt_date_from." and '".str_replace("'","",$txt_date_to)." 11:59:59 PM'";}
		}
	}
	else if(str_replace("'","",$cbo_search_type)==5){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.shipment_date between $txt_date_from and $txt_date_to";
	}
	else
	{
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.po_received_date between $txt_date_from and $txt_date_to";
	}
	
	

	
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if($txt_job_no=="") $txt_job_no=""; else $txt_job_no=" and a.job_no like('%$txt_job_no')";
	$txt_order_no=str_replace("'","",$txt_order_no);
	if($txt_order_no=="") $txt_order_no=""; else $txt_order_no=" and b.po_number ='$txt_order_no'";
	$txt_file_no=str_replace("'","",$txt_file_no);
	if($txt_file_no=="") $file_cond=""; else $file_cond=" and b.file_no ='$txt_file_no'";
	$txt_int_ref_no=str_replace("'","",$txt_int_ref_no);
	if($txt_int_ref_no=="") $ref_cond=""; else $ref_cond=" and b.grouping ='$txt_int_ref_no'";
	
	$txt_style_ref_no=str_replace("'","",$txt_style_ref_no);
	if($txt_style_ref_no=="") $txt_style_ref_no=""; else $txt_style_ref_no=" and a.style_ref_no ='$txt_style_ref_no'";
	//**txt_date_from*txt_date_to*txt_job_no
	
	if(str_replace("'","",$cbo_shipment_status)==4){$shipment_status_con=" and b.shiping_status=3";}
	else if(str_replace("'","",$cbo_shipment_status)==1){$shipment_status_con=" and b.shiping_status !=3";}
	
	
	
	
	
	$tna_all_task=implode(",",$tna_task_id);
	
	if(str_replace("'","",$cbo_search_type)==3)
	{
		$sql = "SELECT a.team_leader,a.factory_marchant,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, b.id ,b.po_number, b.file_no, b.grouping as in_ref_no,(a.TOTAL_SET_QNTY*b.PO_QUANTITY) as po_qty_pcs,b.PO_QUANTITY
		FROM  wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c 
		WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no  and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 $order_status_cond $file_cond $ref_cond";
	}
	else
	{
		$sql = "SELECT a.team_leader,a.factory_marchant,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, b.id, b.po_number, b.file_no, b.grouping as in_ref_no ,(a.TOTAL_SET_QNTY*b.PO_QUANTITY) as po_qty_pcs,b.PO_QUANTITY
		FROM  wo_po_details_master a,  wo_po_break_down b 
		WHERE a.job_no=b.job_no_mst $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no  and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 $order_status_cond  $file_cond $ref_cond"; 
	}
	
   //echo $sql; 
	$result = sql_select( $sql ) ;
	$wo_po_details_master = array();
	$po_no_arr=array();
	$job_no_arr=array();
	foreach( $result as  $row ) 
	{	
		$wo_po_details_master[$row[csf('id')]]['company_name']=$row[csf('company_name')];
		$wo_po_details_master[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$wo_po_details_master[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$wo_po_details_master[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
		$wo_po_details_master[$row[csf('id')]]['po_number']= $row[csf('po_number')];
		$wo_po_details_master[$row[csf('id')]]['set_smv']= $row[csf('set_smv')];
		$wo_po_details_master[$row[csf('id')]]['file_no']= $row[csf('file_no')];
		$wo_po_details_master[$row[csf('id')]]['in_ref_no']= $row[csf('in_ref_no')];
		
		$wo_po_details_master[$row[csf('id')]]['po_qty']= $row[csf('PO_QUANTITY')];
		$wo_po_details_master[$row[csf('id')]]['po_qty_pcs']= $row[csf('po_qty_pcs')];
		$po_no_arr[]=$row[csf('id')];
		$job_no_arr[]=$row[csf('job_no')];

		$wo_po_details_master[$row[csf('job_no')]]['dealing_marchant']=$row[csf('dealing_marchant')];
		$wo_po_details_master[$row[csf('job_no')]]['factory_marchant']=$row[csf('factory_marchant')];
		$wo_po_details_master[$row[csf('job_no')]]['team_leader']=$row[csf('team_leader')];
		
		
	}
	
 
	$result = sql_select("select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name" ) ;
	$team_leader_name = array();
	foreach( $result as  $row ) 
	{	
		$team_leader_name[$row[csf('id')]]=$row[csf('team_leader_name')];
	}

 
	$sql = "SELECT team_member_name,id FROM lib_mkt_team_member_info WHERE is_deleted = 0 and status_active=1 order by id asc";
	$result = sql_select( $sql ) ;
	$team_member_name = array();
	foreach( $result as  $row ) 
	{	
		$team_member_name[$row[csf('id')]]=$row[csf('team_member_name')];
	}
	
	$sql = "SELECT buyer_name,id FROM  lib_buyer WHERE is_deleted = 0 and status_active=1 order by id asc";
	$result = sql_select( $sql ) ;
	$buyer_name = array();
	foreach( $result as  $row ) 
	{	
		$buyer_name[$row[csf('id')]]=$row[csf('buyer_name')];
	}
	
	
	$po_no_arr_all=implode(",",$po_no_arr); if($po_no_arr_all!="") $po_no_arr_all .=",0"; else $po_no_arr_all .="0"; 
	$job_no_all="'".implode("','",$job_no_arr)."'";
	$c=count($tna_task_id);
	
	if($db_type==0)
	{
		$sql ="select a.APPROVED,a.READY_TO_APPROVED,a.po_number_id, a.job_no, a.shipment_date,max(b.pub_shipment_date) as pub_shipment_date, a.template_id, a.po_receive_date,b.insert_date,";
		$i=1;
	
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c) $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id, ";
			else $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id ";
			$i++;
		}
		
		$sql .=" from tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id and a.po_number_id in( $po_no_arr_all ) and a.job_no in ($job_no_all) $shipment_status_con and b.status_active=1  and b.po_quantity>0 $order_status_cond and a.task_type=1 group by a.APPROVED,a.READY_TO_APPROVED,a.po_number_id,a.job_no,b.insert_date order by a.shipment_date,a.po_number_id,a.job_no"; 
	}
	else
	{
		$sql ="select a.APPROVED,a.READY_TO_APPROVED,a.po_number_id, a.job_no, max(a.shipment_date) as shipment_date,max(b.pub_shipment_date) as pub_shipment_date,a.template_id, max(a.po_receive_date) as po_receive_date,b.insert_date,";
		$i=1;
		
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c) $sql .="max(CASE WHEN a.task_number = '".$id."' THEN a.actual_start_date || '_' || a.actual_finish_date || '_' || a.task_start_date || '_' || a.task_finish_date ||'_' || a.notice_date_start || '_' || a.notice_date_end || '_' || a.remarks || '_' || a.id || '_' || a.task_number || '_' || a.plan_start_flag || '_' || a.plan_finish_flag  END ) as status$id, ";
			
			else $sql .="max(CASE WHEN a.task_number = '".$id."' THEN a.actual_start_date || '_' || a.actual_finish_date || '_' || a.task_start_date || '_' || a.task_finish_date || '_' || a.notice_date_start || '_' || a.notice_date_end || '_' || a.remarks || '_' || a.id || '_' || a.task_number || '_' || a.plan_start_flag || '_' || a.plan_finish_flag  END ) as status$id ";
			
			$i++;
		}
		//------------------
			$sql_order_con='';
			$po_no_arr_all=explode(',',$po_no_arr_all);
			$chunk_po_no_arr_all=array_chunk(array_unique($po_no_arr_all),999);
			$p=1;
			foreach($chunk_po_no_arr_all as $rlz_sub_id)
			{
				if($p==1) $sql_order_con .=" and (a.po_number_id in(".implode(',',$rlz_sub_id).")"; else $sql_sub_lc .=" or a.po_number_id in(".implode(',',$rlz_sub_id).")";
				$p++;
			}
			$sql_order_con .=" )";
			
			$sql_job_con='';
			$job_no_all=explode(',',$job_no_all);
			$chunk_job_no_all=array_chunk(array_unique($job_no_all),999);
			$q=1;
			foreach($chunk_job_no_all as $rlz_sub_id)
			{
				if($q==1) $sql_job_con .=" and (a.job_no in(".implode(',',$rlz_sub_id).")"; else $sql_sub_lc .=" or a.job_no in(".implode(',',$rlz_sub_id).")";
				$p++;
			}
			$sql_job_con .=" )";
			
			
		//-------------------------------
		$sql .=" from  tna_process_mst a, wo_po_break_down b,TNA_PLAN_ACTUAL_HISTORY c where a.po_number_id=b.id  and b.id=C.PO_NUMBER_ID $sql_order_con $sql_job_con $shipment_status_con and b.status_active=1 and b.po_quantity>0 $order_status_cond  and a.task_type=1  group by a.APPROVED,a.READY_TO_APPROVED,a.po_number_id,a.job_no,a.template_id,a.shipment_date,b.insert_date order by a.shipment_date,a.po_number_id,a.job_no"; 
	}
	
	    //echo $sql;die;
	$data_sql= sql_select($sql);
	
	
	$poArr=array();
	foreach ($data_sql as $row)
	{
		$poArr[$row[csf('po_number_id')]]=$row[csf('po_number_id')];
	}
	 
	
	$sql_con="select a.job_no,a.booking_no_prefix_num,b.po_break_down_id from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.is_short=2  and a.booking_type=1";
	
	$po_id_list_arr=array_chunk($poArr,999);
	$p=1;
	foreach($po_id_list_arr as $po_id)
	{
		if($p==1){$sql_con .=" and (b.po_break_down_id in(".implode(',',$po_id).")";} 
		else{$sql_con .=" or b.po_break_down_id in(".implode(',',$po_id).")";}
		$p++;
	}
	$sql_con .=") order by a.booking_no_prefix_num";
	
	$sql_booking_sql = sql_select($sql_con);
	foreach( $sql_booking_sql as  $row ) 
	{	
		$booking_no_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no_prefix_num')]]=$row[csf('booking_no_prefix_num')];
	}	
	
	
	
	$width=(count($tna_task_id)*162)+1440;
	
	
	ob_start();
	
	?>
    
    
   <div style="margin:0 0%;">
        <span style="background:#FF0000; padding:0 6px; border-radius:9px; cursor:pointer;" title="Red">&nbsp;</span>&nbsp; Target Date Over. &nbsp;&nbsp;
        <span style="background:#2A9FFF; padding:0 6px; border-radius:9px; cursor:pointer;" title="Blue">&nbsp;</span>&nbsp; Done In Late. &nbsp;&nbsp;
        <span style="background:#FFFF00; padding:0 6px; border-radius:9px; cursor:pointer;" title="Yellow">&nbsp;</span>&nbsp; Reminder.
        
        <span style="background:#0000FF; padding:0 6px; border-radius:9px; cursor:pointer;" title="Royal Blue">&nbsp;</span>&nbsp; Manual Update Plan.
        
    </div>    
    <div style="width:<? echo $width+20; ?>px" align="left">
    <table width="<? echo $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
    	<thead>
        	<tr>
            	<th width="40" rowspan="2">SL</th>
                <th width="120" rowspan="2" ># Team Leader &nbsp; &nbsp; &nbsp; &nbsp;<br> # Dealing Merchant<br> # Factory Merchant</th>
                <th width="70" rowspan="2">Buyer Name</th>
                <th width="110" rowspan="2">PO Number</th>
                <th width="90" rowspan="2">PO Qty.</th>
                <th width="90" rowspan="2">PO Qty(PCS)</th>
                <th width="70" rowspan="2">File No.</th>
                <th width="70" rowspan="2">Int. Ref. No</th>
                <th width="40" rowspan="2">SMV</th>
                <th rowspan="2">Style Ref.</th> 
                <th width="40" rowspan="2">Job No.</th>
                <th width="60" rowspan="2">Fab. Booking</th>
                <th width="100" rowspan="2">Shipment Date</th>
                <th width="80" rowspan="2">PO Insert Date</th>
                
                <th width="90" rowspan="2">Ready To Approve</th>
                <th width="90" rowspan="2">TNA Approve Status</th>
                
                
                <th width="90" rowspan="2">Status</th>
                <?
				
					$i=0;
					foreach($tna_task_array as $task_name=>$key)
					{

						$i++;
						if(count($tna_task_array)==$i) echo '<th width="160" colspan="2" title="'.$tna_task_name_arr[$task_name].'='.$tna_task_name_array[$task_name].'">'. $key.'</th>'; else echo '<th width="160" colspan="2" title="'.$tna_task_name_arr[$task_name].'='.$tna_task_name_array[$task_name].'">'.$key.'</th>';
					}
					echo '</tr><tr>';
					
					$i=0;
					
					foreach($tna_task_array as $key)
					{
						$i++;
						if(count($tna_task_array)==$i){ 
						echo '<th width="80" title="Plan Start Date=Ship Date-(Deadline+Execution Days+1)">Start</th><th width="80" title="Plan Finish Date=(Ship Date-Deadline)"> Finish</th>';}else{
						echo '<th width="80" title="Plan Start Date=Ship Date-(Deadline+Execution Days+1)">Start</th><th width="80" title="Plan Finish Date=(Ship Date-Deadline)"> Finish</th>';}
					}
					echo '</tr>';
					 
				?>
                </thead>
                </table>
         </div>
         
         <? //echo "saju1_".count($tna_task_array); die; ?>
         
        	<div style="overflow-y:scroll; max-height:360px; width:<? echo $width+20; ?>px;" align="left" id="scroll_body">
          	<table width="<? echo $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
       
    <?
	
	$tid=0;
	$i=1;
	$count=0;
	$kid=1;
	$new_job_no=array();
	$h=0;
	$tot_po_qty=0;
	foreach ($data_sql as $row)
	{
		 
		if (!in_array($row[csf('job_no')],$new_job_no))
		{
			//$new_approval_arr=array(); 
			$new_job_no[]=$row[csf('job_no')];
		}
		 if($row[csf('po_number_id')]==0)
		 {
			 foreach($tna_task_id as $vid=>$key)
			 {
				if ($row[csf('status').$key]!="") $new_approval_arr[$row[csf('job_no')]][$key]=$row[csf('status').$key];
			 }
		 }
		 else
		 {
			 $h++;
			 if ($h%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
							
		?>
        		<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle; cursor:pointer;" height="25" onClick="change_color('tr_<? echo $h;?>','<? echo $bgcolor;?>')" id="tr_<? echo $h; ?>">
                    <td width="40" rowspan="3" align="center"><? echo $kid++;?></td>
                    <td width="120" rowspan="3" title="<?
					echo " Team Leader: ".$team_leader_name[$wo_po_details_master[$row[csf('job_no')]]['team_leader']];
					echo "; "; 
					echo " Dealing Merchant: ".$team_member_name[$wo_po_details_master[$row[csf('job_no')]]['dealing_marchant']];
					echo "; "; 
					echo " Factory Merchant: ".$team_member_name[$wo_po_details_master[$row[csf('job_no')]]['factory_marchant']]; ?>">
					<? 
					echo "# ".$team_leader_name[$wo_po_details_master[$row[csf('job_no')]]['team_leader']];
					echo "<br># ".$team_member_name[$wo_po_details_master[$row[csf('job_no')]]['dealing_marchant']];
					echo "<br># ".$team_member_name[$wo_po_details_master[$row[csf('job_no')]]['factory_marchant']]; 
					?>
                    </td>
                    <td width="70" rowspan="3"><p><? echo $buyer_name[$wo_po_details_master[$row[csf('po_number_id')]]['buyer_name']]; ?></p></td>
                    <td width="110" rowspan="3" align="center"><p><div style="width:107px; word-break:break-all">
						<? 
                            //echo $wo_po_details_master[$row[csf('job_no')]][$row[csf('po_number_id')]]; 
							echo "<a href='#report_details' style='color:#990000' onclick= \"progress_comment_popup('".$row[csf('job_no')]."','".$row[csf('po_number_id')]."','".$row[csf('template_id')]."','".$tna_process_type."');\">".$wo_po_details_master[$row[csf('po_number_id')]]['po_number']."</a>";
						
						
                        ?></div>
                   </p> </td>
                    
                    <td width="90" rowspan="3" align="right">
						<?
							$po_qty=$wo_po_details_master[$row[csf('po_number_id')]]['po_qty'];
							echo number_format($po_qty);
							$tot_po_qty+=$po_qty;
						?>
                    </td>
                    <td width="90" rowspan="3" align="right">
						<?
							$po_qty_pcs=$wo_po_details_master[$row[csf('po_number_id')]]['po_qty_pcs'];
							echo number_format($po_qty_pcs);
							$total_po_qty_pcs+=$po_qty_pcs;
						?>
                    </td>
                    <td width="70" rowspan="3"><p><? echo $wo_po_details_master[$row[csf('po_number_id')]]['file_no']; ?></p></td>
                    <td width="70" rowspan="3"><p><? echo $wo_po_details_master[$row[csf('po_number_id')]]['in_ref_no']; ?></p></td>
                    <td width="40" rowspan="3" align="center"><? echo number_format($wo_po_details_master[$row[csf('po_number_id')]]['set_smv'],2); ?></td>
                    
                    <td rowspan="3" title="<? echo $row[csf('job_no')]; ?>"><p><? echo $wo_po_details_master[$row[csf('po_number_id')]]['style_ref_no']; ?></p></td>
                    
                     <td width="40" rowspan="3" align="center"><? echo $wo_po_details_master[$row[csf('po_number_id')]]['job_no_prefix_num']; ?></td>
                     <td rowspan="3" width="60" align="center"><? echo implode(',',$booking_no_arr[$row[csf('po_number_id')]]); ?></td>
                     
                     <? 
					 	if($tna_process_type==1)
						{
							$lead_timee="Template Lead Time: ".$lead_time_array[$row[csf('template_id')]];
						}
						else
						{
							$lead_timee="Lead Time: ".($row[csf('template_id')]+1);
						}
						$po_lead_time=datediff( "d", date("Y-m-d",strtotime(change_date_format($row[csf('po_receive_date')]))), date("Y-m-d",strtotime(change_date_format($row[csf('shipment_date')]))) );

					 ?>
                     
                     
                    <td width="100" rowspan="3" title="<? echo " PO. Rec. Date: ".change_date_format($row[csf('po_receive_date')]); echo ",\n Pub Ship Date: ".$row[csf('pub_shipment_date')] .",\n Insert Date: ".$row[csf('insert_date')].",\n Shipment Date: ".$row[csf('shipment_date')];?>"><div style="width:98px; word-break:break-all">
					<? echo change_date_format($row[csf('shipment_date')])."<br>"." ".$lead_timee."<br>"." PO Lead Time:".($po_lead_time-1);  ?></div>
                    </td>
                    <td width="80" rowspan="3" align="center"><? echo date("d-m-Y",strtotime($row[csf('insert_date')]));?></td>
                    
                    
 					<td width="90" rowspan="3" align="center">
					<? 
						echo create_drop_down( "cbo_ready_to_approve", 80, $yes_no,"", 1, "-- Select --",$row['READY_TO_APPROVED'], "setReadyToApp(".$row[csf('po_number_id')].",this.value,".$row['APPROVED'].")" );
					?>
                    </td>                   
                    <td width="90" rowspan="3" align="center" id="approval_status_<?= $row[csf('po_number_id')];?>">
					<? 
					
					if($row['APPROVED']==1){echo "<span style='color:#1F7044;font-weight:bold;'>Approved</span>";}
					else if($row['APPROVED']==3){echo "<span style='color:#FF813A;font-weight:bold;'>Partial Approved</span>";}
					else if($row['APPROVED']==2){echo "<span style='color:#FF813A;font-weight:bold;'>Deny</span>";}
					else{ echo "<span style='color:#f00;font-weight:bold;'>Un Approve</span>";} 
					?>
                    </td>                   
                    
                    <td width="90">Plan</td>
                <?
 
	
					 $tast_id_arr=array_unique(explode(',',$tast_tmp_id_arr[$row[csf('template_id')]]));
					 $i=0;
					 foreach($tna_task_id as $vid=>$key)
					 {
						 $i++;
						
						if ( $new_approval_arr[$row[csf('job_no')]][$key]=="") $new_data=explode("_",$row[csf('status').$key]); 
						else $new_data=explode("_",$new_approval_arr[$row[csf('job_no')]][$key]);
						if($new_data[7]!="") $function="onclick='update_tna_process(1,$new_data[7],".$row[csf('po_number_id')].")'"; else $function="";
						
						//if(!in_array($vid,$manual_update_task_arr)){$function="";}
 
						if($plan_manual_update_task_arr[$vid]==''){$function="";}
						if($row[APPROVED]==1){$function="onclick='approvedAlertMessage()'";}
						
						if($new_data[9]==1){$psc=" style='color:#0000FF'";}else{$psc="";}
						if($new_data[10]==1){$pfc=" style='color:#0000FF'";}else{$pfc="";}
						
						
						if(in_array($vid,$tast_id_arr))
						{
							if(count($tna_task_id)==$i)
								echo '<td id="plan_1'.$vid.$row[csf('po_number_id')].'" align="center" '.$psc.'   width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? "<span style='color:#FF0000'> N/A </span>" : change_date_format($new_data[2])).'</td><td id="plan_2'.$vid.$row[csf('po_number_id')].'" align="center" '.$pfc.' '.$function.'> '.($new_data[3]== "N/A"  || $new_data[3]=="0000-00-00"? "" : change_date_format($new_data[3])).'</td>';
							 else
								echo '<td id="plan_1'.$vid.$row[csf('po_number_id')].'" align="center" '.$psc.'  width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? "<span style='color:#FF0000'> N/A </span>" : change_date_format($new_data[2])).'</td><td id="plan_2'.$vid.$row[csf('po_number_id')].'"  align="center" '.$pfc.'width="80" '.$function.'> '.($new_data[3]== ""  || $new_data[3]=="0000-00-00"? "<span style='color:#FF0000'> N/A </span>" : change_date_format($new_data[3])).'</td>';
						}
						else
						{
							if(count($tna_task_id)==$i)
								echo '<td id="plan_1'.$vid.$row[csf('po_number_id')].'" align="center" '.$psc.'   width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? "N/A" : change_date_format($new_data[2])).'</td><td id="plan_2'.$vid.$row[csf('po_number_id')].'" align="center" '.$pfc.' '.$function.'> '.($new_data[3]== ""  || $new_data[3]=="0000-00-00"? "N/A" : change_date_format($new_data[3])).'</td>';
								
							 else
								echo '<td id="plan_1'.$vid.$row[csf('po_number_id')].'" align="center" '.$psc.'  width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? "N/A" : change_date_format($new_data[2])).'</td><td id="plan_2'.$vid.$row[csf('po_number_id')].'" align="center" '.$pfc.' width="80" '.$function.'> '.($new_data[3]== ""  || $new_data[3]=="0000-00-00"? "N/A" : change_date_format($new_data[3])).'</td>';
						}
						
						
					 }
					echo '</tr>';
					
					
					 
					
					echo '<tr style="cursor:pointer" onClick="change_color(\'actula_'.$h.'\',\''.$bgcolor.'\')" id="actula_'.$h.'"><td width="90">Actual</td>';
					$i=0;
					 foreach($tna_task_id as $vid=>$key)
					 {
						  
						 $i++;
						if ( $new_approval_arr[$row[csf('job_no')]][$key]==""){$new_data=explode("_",$row[csf('status').$key]);}
						else{$new_data=explode("_",$new_approval_arr[$row[csf('job_no')]][$key]);}
						
						if( $new_data[7]!="") $function="onclick='update_tna_process(2,$new_data[7],".$row[csf('po_number_id')].")'";  else $function="";
						$bgcolor1=""; $bgcolor="";
						
						if($actual_manual_update_task_arr[$vid]==''){$function="";}
						if($row[APPROVED]==1){$function="onclick='approvedAlertMessage()'";}
						
						
						if (trim($new_data[2])!= $blank_date) 
						{
							if (strtotime($new_data[4])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($new_data[2])){$bgcolor="#FFFF00";}//Yellow
							else if (strtotime($new_data[2])<strtotime(date("Y-m-d",time()))){$bgcolor="#FF0000";}//Red
							else {$bgcolor="";}
							
						}
						 
						if ($new_data[3]!= $blank_date) {
							if (strtotime($new_data[5])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($new_data[3])){$bgcolor1="#FFFF00";}//Yellow
							else if (strtotime($new_data[3])<strtotime(date("Y-m-d",time()))){$bgcolor1="#FF0000";}//Red ;
							else{$bgcolor1="";}
						}
						
						if ($new_data[0]!=$blank_date) $bgcolor="";
						if ($new_data[1]!=$blank_date) $bgcolor1="";
						
						
						$idd=$row[csf('job_no')]."".$row[csf('po_number_id')]."".$key;
						if(count($tna_task_id)==$i)
							echo '<td id="actual_1'.$vid.$row[csf('po_number_id')].'" align="center" title="Click Here to Edit Date" id="'.$idd.'1" '.$function.' width="80" bgcolor="'.$bgcolor.'">'.($new_data[0]== "" || $new_data[0]=="0000-00-00" ? "" : change_date_format($new_data[0])).'</td><td id="actual_2'.$vid.$row[csf('po_number_id')].'" align="center" id="'.$idd.'2" title="Click Here to Edit Date" '.$function.' bgcolor="'.$bgcolor1.'" title="'.$new_data[6].'">'.($new_data[1]== "" || $new_data[1]=="0000-00-00" ? "" : change_date_format($new_data[1])).'</td>';
							
						else
							echo '<td id="actual_1'.$vid.$row[csf('po_number_id')].'" align="center" id="'.$idd.'1" title="Click Here to Edit Date"  '.$function.' width="80" bgcolor="'.$bgcolor.'">'.($new_data[0]== "" || $new_data[0]=="0000-00-00" ? "" : change_date_format($new_data[0])).'</td><td id="actual_2'.$vid.$row[csf('po_number_id')].'" align="center" id="'.$idd.'2" title="Click Here to Edit Date" '.$function.' width="80" bgcolor="'.$bgcolor1.'" title="'.$new_data[6].'">'.($new_data[1]== "" || $new_data[1]=="0000-00-00" ? "" : change_date_format($new_data[1])).'</td>';
						
					 }
					echo '</tr>'; 
					
					echo '<tr style="cursor:pointer" onClick="change_color(\'delay_'.$h.'\',\''.$bgcolor.'\')" id="delay_'.$h.'"><td width="90">Delay/Early By</td>';
					$j=0;
					foreach($tna_task_id as $vid=>$key)
					{
						 $j++;
						if ( $new_approval_arr[$row[csf('job_no')]][$key]=="") $new_data=explode("_",$row[csf('status').$key]); 
						else $new_data=explode("_",$new_approval_arr[$row[csf('job_no')]][$key]);
						
						$bgcolor1=""; $bgcolor="";
						
						
						if($new_data[0]!=$blank_date)
						{
							$start_diff1 = datediff( "d", $new_data[0], $new_data[2]);
							if($new_data[0]== "")
							{
								$start_diff=$start_diff1;
							}
							else
							{
								$start_diff=$start_diff1-1;
							}
							if($start_diff<0)
							{
								$bgcolor="#2A9FFF"; //Blue
							}
							if($start_diff>0)
							{
								$bgcolor="";
							}
						}
						else
						{
							if(strtotime(date("Y-m-d"))>strtotime($new_data[2]))
							{
								$start_diff1 = datediff( "d", $new_data[2], date("Y-m-d"));
								if($new_data[0]== "")
								{
									//$start_diff=-abs($start_diff1);
									$start_diff=-abs($start_diff1-1);
								}
								else
								{
									$start_diff=-abs($start_diff1-1);
								}
								//$bgcolor="#FF0000";		//Red
								$bgcolor=($new_data[2]== "" || $new_data[2]=="0000-00-00")?'':'#FF0000';
							}
							if(strtotime(date("Y-m-d"))<=strtotime($new_data[2]))
							{
								$start_diff = "";
								$bgcolor="";
							}
						}
						if($new_data[1]!=$blank_date)
						{
							$finish_diff1 = datediff( "d", $new_data[1], $new_data[3]);
							if($new_data[0]== "")
							{
								$finish_diff=$finish_diff1;
							}
							else
							{	
								$finish_diff=$finish_diff1-1;
							}
							if($finish_diff<0)
							{
								$bgcolor1="#2A9FFF";
							}
							if($finish_diff>0)
							{	
								$bgcolor1="";
							}
						}
						else
						{
							if(strtotime(date("Y-m-d"))>strtotime($new_data[3]))
							{
								
								$finish_diff1 = datediff( "d", $new_data[3], date("Y-m-d"));
								if($new_data[1]== "")
								{
									//$finish_diff=-abs($finish_diff1);
									$finish_diff=-abs($finish_diff1-1);
								}
								else
								{
									$finish_diff=-abs($finish_diff1-1);
								}
								//$bgcolor1="#FF0000";
								$bgcolor1=($new_data[3]== "" || $new_data[3]=="0000-00-00")?'':'#FF0000';
							}
							if(strtotime(date("Y-m-d"))<=strtotime($new_data[3]))
							{
								
								$finish_diff = "";
								$bgcolor1="";
							}
						}
						
						
						
						if(count($tna_task_id)==$j)
							
							echo '<td width="80" align="center" bgcolor="'.$bgcolor.'">'.($start_diff).'</td><td width="80" bgcolor="'.$bgcolor1.'" align="center">'.($finish_diff).'</td>';
						else
							echo '<td width="80" align="center" bgcolor="'.$bgcolor.'">'.($start_diff).'</td><td width="80" bgcolor="'.$bgcolor1.'" align="center">'.($finish_diff).'</td>';
					}
					 
					echo '</tr>';
					
					
					 
		 }
				 
	}
		?>
     
     
    </table>
    </div>
    <div style="width:<? echo $width+20; ?>px;" align="left">
         <table width="<? echo $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <tfoot>
                <th width="40"></th>
                <th width="120"></th>
                <th width="70"></th>
                <th width="110">Total</th>
                <th width="90" id="total_po_qty" align="right"><p><? echo number_format($tot_po_qty,2);?></p></th>
                <th width="90" id="total_po_qty_pcs" align="right"><p><? echo number_format($total_po_qty_pcs,2);?></p></th>
                <th width="70"></th>
                <th width="70"></th>
                <th width="40"></th>
                <th colspan="<? echo (count($tna_task_id)*2)+6;?>"></th>
            </tfoot>
        </table>
    </div>
    
    
          <?
		  
		 $sql = sql_select("select designation,name from variable_settings_signature where report_id=95 and company_id=$cbo_company_id order by sequence_no" );
	     $count=count($sql);

		$width=$width+170;
		$td_width=floor($width/$count);
		
		$standard_width=$count*150;
		
		if($standard_width>$width) $td_width=150;
		
		$no_coloumn_per_tr=floor($width/$td_width);
		$col=$count-2;
		$i=1;
		echo '<table width="'.$width.'"><tr><td width="'.$td_width.'" align="center" valign="bottom">'.$user_arr[$inserted_by].'</td><td height="70" colspan="'.$col.'"></td><td width="'.$td_width.'" align="center" valign="bottom">'.$user_arr[$nameArray_approved_date_row[csf('approved_by')]].'</td></tr><tr>';
		foreach($sql as $row)	
		{
			echo '<td width="'.$td_width.'" align="center" valign="top"><strong style="text-decoration:overline">'.$row[csf("designation")]."</strong><br>".$row[csf("name")].'</td>';
			
			if($i%$no_coloumn_per_tr==0) echo '</tr><tr><td height="70" colspan="'.$no_coloumn_per_tr.'"></td><tr>';
			$i++;
		} 
		echo '</tr></table>';



	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
 	echo "$total_datass****$filename";
	exit();
}


//this report for only knit asia
if($action=="generate_tna_report_v2")
{

	if($graph==1){
		if($db_type==0)
		{
			$txt_date_from = date("'Y-m-d'",strtotime($txt_date_from));
			$txt_date_to = date("'Y-m-d'",strtotime($txt_date_to));
		}
		else
		{
			$txt_date_from = date("'d-M-Y'",strtotime($txt_date_from));
			$txt_date_to = date("'d-M-Y'",strtotime($txt_date_to));
		}
	}


	$actual_manual_update_task_arr=return_library_array("select task_id,task_id from tna_manual_permission where is_actual_manual=1  and company_id=$cbo_company_name","task_id","task_id");
	$plan_manual_update_task_arr=return_library_array("select task_id,task_id from tna_manual_permission where is_plan_manual=1  and company_id=$cbo_company_name","task_id","task_id");

	if($_SESSION['logic_erp']['tna_task_id']){$whereCon="and a.task_name in(".$_SESSION['logic_erp']['tna_task_id'].")";}

	$mod_sql= sql_select("select a.id,a.task_catagory,a.task_name,a.task_short_name,a.task_type,a.completion_percent,a.task_sequence_no,b.task_template_id,b.lead_time 
	from lib_tna_task a,tna_task_template_details b where a.task_name=b.tna_task_id and b.task_type=1  AND a.task_type = 1 and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 $whereCon order by a.task_sequence_no asc");
	
	
	
	$tna_task_array=array();
	$tna_task_id=array();
	$tna_task_cat=array();
	$tna_task_name_arr=array();
	foreach ($mod_sql as $row)
	{
		$tna_task_id[$row[csf("task_name")]]=$row[csf("task_name")];
		$tna_task_array[$row[csf("id")]] =$row[csf("task_short_name")];
		$tna_task_name_array[$row[csf("id")]] =$tna_task_name[$row[csf("task_name")]];
		$tna_task_cat[$row[csf("id")]]=$row[csf("task_catagory")];
		$tna_task_name_arr[$row[csf("id")]]=$row[csf("task_name")];
		
		
		$lead_time_array[$row[csf("task_template_id")]]=$row[csf("lead_time")];
		$tast_tmp_id_arr[$row[csf("task_template_id")]][$row[csf("tna_task_id")]]=$row[csf("tna_task_id")];
		
		
	}
	$cbo_company_id=$cbo_company_name;
	
	$order_status_cond="";
	if(str_replace("'","",$cbo_order_status)>0) $order_status_cond=" and b.is_confirmed=$cbo_order_status";
	
	if(str_replace("'","",$cbo_company_name)==0) $cbo_company_name=""; else $cbo_company_name=" and a.company_name = $cbo_company_name";
	if(str_replace("'","",$cbo_buyer_name)==0) $cbo_buyer_name=""; else $cbo_buyer_name=" and a.buyer_name = $cbo_buyer_name";
	if(str_replace("'","",$cbo_team_member)==0) $cbo_team_member=""; else $cbo_team_member=" and a.dealing_marchant = $cbo_team_member";
	
	if(str_replace("'","",$cbo_search_type)==1){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
	}
	else if(str_replace("'","",$cbo_search_type)==3){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and c.country_ship_date between $txt_date_from and $txt_date_to";
	}
	else if(str_replace("'","",$cbo_search_type)==4){
		if($db_type==0)
		{
			if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)==""){$date_range="";}else{ 
			$date_range=" and b.insert_date between ".$txt_date_from." and ".$txt_date_to."";}
		}
		else
		{
			
			if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)==""){$date_range="";}else{ 
			$date_range=" and b.insert_date between ".$txt_date_from." and '".str_replace("'","",$txt_date_to)." 11:59:59 PM'";}
		}
	}
	else
	{
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.po_received_date between $txt_date_from and $txt_date_to";
	}
	
	

	
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if($txt_job_no=="") $txt_job_no=""; else $txt_job_no=" and a.job_no_prefix_num ='$txt_job_no'";
	$txt_order_no=str_replace("'","",$txt_order_no);
	if($txt_order_no=="") $txt_order_no=""; else $txt_order_no=" and b.po_number ='$txt_order_no'";
	$txt_file_no=str_replace("'","",$txt_file_no);
	if($txt_file_no=="") $file_cond=""; else $file_cond=" and b.file_no ='$txt_file_no'";
	$txt_int_ref_no=str_replace("'","",$txt_int_ref_no);
	if($txt_int_ref_no=="") $ref_cond=""; else $ref_cond=" and b.grouping ='$txt_int_ref_no'";
	
	$txt_style_ref_no=str_replace("'","",$txt_style_ref_no);
	if($txt_style_ref_no=="") $txt_style_ref_no=""; else $txt_style_ref_no=" and a.style_ref_no ='$txt_style_ref_no'";
	//**txt_date_from*txt_date_to*txt_job_no
	
	if(str_replace("'","",$cbo_shipment_status)==3)$shipment_status_con=" and b.shiping_status=$cbo_shipment_status"; else $shipment_status_con=" and b.shiping_status !=3";
	
	
	
	
	
	$tna_all_task=implode(",",$tna_task_id);
	
	if(str_replace("'","",$cbo_search_type)==3)
	{
		$sql = "SELECT a.team_leader,a.factory_marchant,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, b.id ,b.po_number, b.file_no, b.grouping as in_ref_no
		FROM  wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c 
		WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no  and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 $order_status_cond $file_cond $ref_cond";
	}
	else
	{
		$sql = "SELECT a.team_leader,a.factory_marchant,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, b.id, b.po_number, b.file_no, b.grouping as in_ref_no 
		FROM  wo_po_details_master a,  wo_po_break_down b 
		WHERE a.job_no=b.job_no_mst $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no  and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 $order_status_cond  $file_cond $ref_cond"; 
	}
	
   //echo $sql; 
	$result = sql_select( $sql ) ;
	$wo_po_details_master = array();
	$po_no_arr=array();
	$job_no_arr=array();
	foreach( $result as  $row ) 
	{	
		$wo_po_details_master[$row[csf('id')]]['company_name']=$row[csf('company_name')];
		$wo_po_details_master[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$wo_po_details_master[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$wo_po_details_master[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
		$wo_po_details_master[$row[csf('id')]]['po_number']= $row[csf('po_number')];
		$wo_po_details_master[$row[csf('id')]]['set_smv']= $row[csf('set_smv')];
		$wo_po_details_master[$row[csf('id')]]['file_no']= $row[csf('file_no')];
		$wo_po_details_master[$row[csf('id')]]['in_ref_no']= $row[csf('in_ref_no')];
		$po_no_arr[]=$row[csf('id')];
		$job_no_arr[]=$row[csf('job_no')];
		
		//$wo_po_details_master[$row[csf('id')]]['dealing_marchant']=$row[csf('dealing_marchant')];
		//$wo_po_details_master[$row[csf('id')]]['factory_marchant']=$row[csf('factory_marchant')];
		//$wo_po_details_master[$row[csf('id')]]['team_leader']=$row[csf('team_leader')];


		$wo_po_details_master[$row[csf('job_no')]]['dealing_marchant']=$row[csf('dealing_marchant')];
		$wo_po_details_master[$row[csf('job_no')]]['factory_marchant']=$row[csf('factory_marchant')];
		$wo_po_details_master[$row[csf('job_no')]]['team_leader']=$row[csf('team_leader')];
		
		
	}
	
 
	$result = sql_select("select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name" ) ;
	$team_leader_name = array();
	foreach( $result as  $row ) 
	{	
		$team_leader_name[$row[csf('id')]]=$row[csf('team_leader_name')];
	}

 
	$sql = "SELECT team_member_name,id FROM lib_mkt_team_member_info WHERE is_deleted = 0 and status_active=1 order by id asc";
	$result = sql_select( $sql ) ;
	$team_member_name = array();
	foreach( $result as  $row ) 
	{	
		$team_member_name[$row[csf('id')]]=$row[csf('team_member_name')];
	}
	
	$sql = "SELECT buyer_name,id FROM  lib_buyer WHERE is_deleted = 0 and status_active=1 order by id asc";
	$result = sql_select( $sql ) ;
	$buyer_name = array();
	foreach( $result as  $row ) 
	{	
		$buyer_name[$row[csf('id')]]=$row[csf('buyer_name')];
	}
	
	
	$po_no_arr_all=implode(",",$po_no_arr); if($po_no_arr_all!="") $po_no_arr_all .=",0"; else $po_no_arr_all .="0"; 
	$job_no_all="'".implode("','",$job_no_arr)."'";
	$c=count($tna_task_id);
	
	if($db_type==0)
	{
		$sql ="select a.po_number_id, a.job_no, a.shipment_date,max(b.pub_shipment_date) as pub_shipment_date, a.template_id, a.po_receive_date,b.insert_date,";
		$i=1;
	
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c) $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id, ";
			else $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id ";
			$i++;
		}
		
		$sql .=" from tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id and a.po_number_id in( $po_no_arr_all ) and a.job_no in ($job_no_all) $shipment_status_con and b.status_active=1  and b.po_quantity>0 $order_status_cond and a.task_type=1 group by a.po_number_id,a.job_no,b.insert_date order by a.shipment_date,a.po_number_id,a.job_no"; 
	}
	else
	{
		$sql ="select a.po_number_id, a.job_no, max(b.shipment_date) as shipment_date,max(b.pub_shipment_date) as pub_shipment_date,a.template_id, max(a.po_receive_date) as po_receive_date,b.insert_date,";
		$i=1;
		
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c) $sql .="max(CASE WHEN a.task_number = '".$id."' THEN a.actual_start_date || '_' || a.actual_finish_date || '_' || a.task_start_date || '_' || a.task_finish_date ||'_' || a.notice_date_start || '_' || a.notice_date_end || '_' || a.remarks || '_' || a.id || '_' || a.task_number || '_' || a.plan_start_flag || '_' || a.plan_finish_flag  || '_' || a.ACTUAL_START_FLAG || '_' || a.ACTUAL_FINISH_FLAG   END ) as status$id, ";
			
			else $sql .="max(CASE WHEN a.task_number = '".$id."' THEN a.actual_start_date || '_' || a.actual_finish_date || '_' || a.task_start_date || '_' || a.task_finish_date || '_' || a.notice_date_start || '_' || a.notice_date_end || '_' || a.remarks || '_' || a.id || '_' || a.task_number || '_' || a.plan_start_flag || '_' || a.plan_finish_flag  || '_' || a.ACTUAL_START_FLAG || '_' || a.ACTUAL_FINISH_FLAG  END ) as status$id ";
			
			$i++;
		}
		//------------------
			$sql_order_con='';
			$po_no_arr_all=explode(',',$po_no_arr_all);
			$chunk_po_no_arr_all=array_chunk(array_unique($po_no_arr_all),999);
			$p=1;
			foreach($chunk_po_no_arr_all as $rlz_sub_id)
			{
				if($p==1) $sql_order_con .=" and (a.po_number_id in(".implode(',',$rlz_sub_id).")"; else $sql_sub_lc .=" or a.po_number_id in(".implode(',',$rlz_sub_id).")";
				$p++;
			}
			$sql_order_con .=" )";
			
			$sql_job_con='';
			$job_no_all=explode(',',$job_no_all);
			$chunk_job_no_all=array_chunk(array_unique($job_no_all),999);
			$q=1;
			foreach($chunk_job_no_all as $rlz_sub_id)
			{
				if($q==1) $sql_job_con .=" and (a.job_no in(".implode(',',$rlz_sub_id).")"; else $sql_sub_lc .=" or a.job_no in(".implode(',',$rlz_sub_id).")";
				$p++;
			}
			$sql_job_con .=" )";
			
			
		//-------------------------------
		$sql .=" from  tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id $sql_order_con $sql_job_con $shipment_status_con and b.status_active=1 and b.po_quantity>0 $order_status_cond  and a.task_type=1  group by a.po_number_id,a.job_no,a.template_id,b.shipment_date,b.insert_date order by b.shipment_date,a.po_number_id,a.job_no"; 
	}
	
	 //echo $sql;
	$data_sql= sql_select($sql);
	
	
	$poArr=array();
	foreach ($data_sql as $row)
	{
		$poArr[$row[csf('po_number_id')]]=$row[csf('po_number_id')];
	}
	 
	
	$sql_con="select a.job_no,a.booking_no_prefix_num,b.po_break_down_id from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.is_short=2  and a.booking_type=1";
	
	$po_id_list_arr=array_chunk($poArr,999);
	$p=1;
	foreach($po_id_list_arr as $po_id)
	{
		if($p==1){$sql_con .=" and (b.po_break_down_id in(".implode(',',$po_id).")";} 
		else{$sql_con .=" or b.po_break_down_id in(".implode(',',$po_id).")";}
		$p++;
	}
	$sql_con .=") order by a.booking_no_prefix_num";
	
	$sql_booking_sql = sql_select($sql_con);
	foreach( $sql_booking_sql as  $row ) 
	{	
		$booking_no_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no_prefix_num')]]=$row[csf('booking_no_prefix_num')];
	}	
	
	
	
	$width=(count($tna_task_id)*161)+1350;
	
	
	ob_start();
	
	?>
    
    
   <div style="margin:0 0%;">
        <span style="background:#FF0000; padding:0 6px; border-radius:9px; cursor:pointer;" title="Red">&nbsp;</span>&nbsp; Target Date Over. &nbsp;&nbsp;
        <span style="background:#2A9FFF; padding:0 6px; border-radius:9px; cursor:pointer;" title="Blue">&nbsp;</span>&nbsp; Done In Late. &nbsp;&nbsp;
        <span style="background:#FFFF00; padding:0 6px; border-radius:9px; cursor:pointer;" title="Yellow">&nbsp;</span>&nbsp; Reminder.
        
        <span style="background:#0000FF; padding:0 6px; border-radius:9px; cursor:pointer;" title="Royal Blue">&nbsp;</span>&nbsp; Manual Update Plan.
        
    </div>    
    <div style="width:<? echo $width+60; ?>px" align="left">
    <table width="<? echo $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
    	<thead>
        	<tr>
            	<th width="40" rowspan="2">SL</th>
                <th width="120" rowspan="2" ># Team Leader &nbsp; &nbsp; &nbsp; &nbsp;<br> # Dealing Merchant<br> # Factory Merchant</th>
                <th width="70" rowspan="2">Buyer Name</th>
                <th width="110" rowspan="2">PO Number</th>
                <th width="90" rowspan="2">PO Qty.</th>
                <th width="70" rowspan="2">File No.</th>
                <th width="70" rowspan="2">Int. Ref. No</th>
                <th width="40" rowspan="2">SMV</th>
                <th rowspan="2">Style Ref.</th> 
                <th width="40" rowspan="2">Job No.</th>
                <th width="60" rowspan="2">Fab. Booking</th>
                <th width="100" rowspan="2">Shipment Date</th>
                <th width="80" rowspan="2">PO Insert Date</th>
                
                <th width="90" rowspan="2">Status</th>
                <?
					$i=0;
					foreach($tna_task_array as $task_name=>$key)
					{
						$i++;
						if(count($tna_task_array)==$i) echo '<th width="160" colspan="2" title="'.$tna_task_name_arr[$task_name].'='.$tna_task_name_array[$task_name].'">'. $key.'</th>'; else echo '<th width="160" colspan="2" title="'.$tna_task_name_arr[$task_name].'='.$tna_task_name_array[$task_name].'">'.$key.'</th>';
					}
					echo '</tr><tr>';
					
					$i=0;
					
					foreach($tna_task_array as $key)
					{
						$i++;
						if(count($tna_task_array)==$i){ 
						echo '<th width="80" title="Plan Start Date=Ship Date-(Deadline+Execution Days+1)">Start</th><th width="80" title="Plan Finish Date=(Ship Date-Deadline)"> Finish</th>';}else{
						echo '<th width="80" title="Plan Start Date=Ship Date-(Deadline+Execution Days+1)">Start</th><th width="80" title="Plan Finish Date=(Ship Date-Deadline)"> Finish</th>';}
					}
					echo '</tr>';
					 
				?>
                </thead>
                </table>
         </div>
         
         <? //echo "saju1_".count($tna_task_array); die; ?>
         
        	<div style="overflow-y:scroll; max-height:360px; width:<? echo $width+20; ?>px;" align="left" id="scroll_body">
          	<table width="<? echo $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
       
    <?
	
	$tid=0;
	$i=1;
	$count=0;
	$kid=1;
	$new_job_no=array();
	$h=0;
	$tot_po_qty=0;
	foreach ($data_sql as $row)
	{
		 
		if (!in_array($row[csf('job_no')],$new_job_no))
		{
			//$new_approval_arr=array(); 
			$new_job_no[]=$row[csf('job_no')];
		}
		 if($row[csf('po_number_id')]==0)
		 {
			 foreach($tna_task_id as $vid=>$key)
			 {
				if ($row[csf('status').$key]!="") $new_approval_arr[$row[csf('job_no')]][$key]=$row[csf('status').$key];
			 }
		 }
		 else
		 {
			 $h++;
			 if ($h%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
							
		?>
        		<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle; cursor:pointer;" height="25" onClick="change_color('tr_<? echo $h;?>','<? echo $bgcolor;?>')" id="tr_<? echo $h; ?>">
                    <td width="40" rowspan="3" align="center"><? echo $kid++;?></td>
                    <td width="120" rowspan="3" title="<?
					echo " Team Leader: ".$team_leader_name[$wo_po_details_master[$row[csf('job_no')]]['team_leader']];
					echo "; "; 
					echo " Dealing Merchant: ".$team_member_name[$wo_po_details_master[$row[csf('job_no')]]['dealing_marchant']];
					echo "; "; 
					echo " Factory Merchant: ".$team_member_name[$wo_po_details_master[$row[csf('job_no')]]['factory_marchant']]; ?>">
					<? 
					echo "# ".$team_leader_name[$wo_po_details_master[$row[csf('job_no')]]['team_leader']];
					echo "<br># ".$team_member_name[$wo_po_details_master[$row[csf('job_no')]]['dealing_marchant']];
					echo "<br># ".$team_member_name[$wo_po_details_master[$row[csf('job_no')]]['factory_marchant']]; 
					?>
                    </td>
                    <td width="70" rowspan="3"><p><? echo $buyer_name[$wo_po_details_master[$row[csf('po_number_id')]]['buyer_name']]; ?></p></td>
                    <td width="110" rowspan="3" align="center"><p><div style="width:107px; word-break:break-all">
						<? 
                            //echo $wo_po_details_master[$row[csf('job_no')]][$row[csf('po_number_id')]]; 
							echo "<a href='#report_details' style='color:#990000' onclick= \"progress_comment_popup('".$row[csf('job_no')]."','".$row[csf('po_number_id')]."','".$row[csf('template_id')]."','".$tna_process_type."');\">".$wo_po_details_master[$row[csf('po_number_id')]]['po_number']."</a>";
						
						
                        ?></div>
                   </p> </td>
                    
                    <td width="90" rowspan="3" align="right"><p>
						<?
							$po_qty=return_field_value("po_quantity", "wo_po_break_down", "id='".$row[csf('po_number_id')]."' and status_active=1 and is_deleted=0"); 
							echo number_format($po_qty);
							$tot_po_qty+=$po_qty;
						?>
                        </p>
                    </td>
                    <td width="70" rowspan="3"><p><? echo $wo_po_details_master[$row[csf('po_number_id')]]['file_no']; ?></p></td>
                    <td width="70" rowspan="3"><p><? echo $wo_po_details_master[$row[csf('po_number_id')]]['in_ref_no']; ?></p></td>
                    <td width="40" rowspan="3" align="center"><? echo number_format($wo_po_details_master[$row[csf('po_number_id')]]['set_smv'],2); ?></td>
                    
                    <td rowspan="3" title="<? echo $row[csf('job_no')]; ?>"><p><? echo $wo_po_details_master[$row[csf('po_number_id')]]['style_ref_no']; ?></p></td>
                    
                     <td width="40" rowspan="3" title=""><? echo $wo_po_details_master[$row[csf('po_number_id')]]['job_no_prefix_num']; ?></td>
                     <td rowspan="3" width="60" align="center"><? echo implode(',',$booking_no_arr[$row[csf('po_number_id')]]); ?></td>
                     
                     <? 
					 	if($tna_process_type==1)
						{
							$lead_timee="Template Lead Time: ".$lead_time_array[$row[csf('template_id')]];
						}
						else
						{
							$lead_timee="Lead Time: ".($row[csf('template_id')]+1);
						}
						$po_lead_time=datediff( "d", date("Y-m-d",strtotime(change_date_format($row[csf('po_receive_date')]))), date("Y-m-d",strtotime(change_date_format($row[csf('shipment_date')]))) );

					 ?>
                     
                     
                    <td width="100" rowspan="3" title="<? echo " PO. Rec. Date: ".change_date_format($row[csf('po_receive_date')]); echo ",\n Pub Ship Date: ".$row[csf('pub_shipment_date')] .",\n Insert Date: ".$row[csf('insert_date')];?>"><div style="width:98px; word-break:break-all">
					<? echo change_date_format($row[csf('shipment_date')])."<br>"." ".$lead_timee."<br>"." PO Lead Time:".($po_lead_time-1);  ?></div>
                    </td>
                    <td width="80" rowspan="3" align="center"><? echo date("d-m-Y",strtotime($row[csf('insert_date')]));?></td>
                    <td width="90">Plan</td>
                <?
 
					$target_tna_entry_task_arr=array(48=>48,52=>52,60=>60,61=>61,63=>63,73=>73,84=>84,267=>267,268=>268,70=>70,71=>71,86=>86,90=>90,88=>88,122=>122); 

					// $tast_id_arr=array_unique(explode(',',$tast_tmp_id_arr[$row[csf('template_id')]]));
					 $tast_id_arr=$tast_tmp_id_arr[$row[csf('template_id')]];
					 $i=0;
					 foreach($tna_task_id as $vid=>$key)
					 {
						 $i++;

						

						
						if ( $new_approval_arr[$row[csf('job_no')]][$key]=="") $new_data=explode("_",$row[csf('status').$key]); 
						else $new_data=explode("_",$new_approval_arr[$row[csf('job_no')]][$key]);
						if($new_data[7]!=""){
							$function="onclick='update_tna_process(1,$new_data[7],".$row[csf('po_number_id')].")'";
							$notApplicable="-- -- ----";
						} 
						else {$function="";$notApplicable="N/A";}
						
						
						if($target_tna_entry_task_arr[$key]){
							$function="onclick='fn_tna_target_entry(1,$new_data[7],".$row[csf('po_number_id')].",".$key.")'";	
						}
						
						
						if($plan_manual_update_task_arr[$vid]==''){$function="";}
						
						
						if($new_data[9]==1){$psc=" style='color:#0000FF'";}else{$psc="";}
						if($new_data[10]==1){$pfc=" style='color:#0000FF'";}else{$pfc="";}
						
						
					
						
						if(in_array($vid,$tast_id_arr))
						{
							if(count($tna_task_id)==$i)
								echo '<td id="plan_1'.$vid.$row[csf('po_number_id')].'" align="center" '.$psc.'   width="80" '.$function.'>'.(($new_data[2]== "" || $new_data[2]=="0000-00-00") ? "<span style='color:#FF0000'> $notApplicable </span>" : change_date_format($new_data[2])).'</td><td id="plan_2'.$vid.$row[csf('po_number_id')].'" align="center" '.$pfc.' '.$function.'> '.(($new_data[3]== ""  || $new_data[3]=="0000-00-00")? "" : change_date_format($new_data[3])).'</td>';
							 else
								echo '<td id="plan_1'.$vid.$row[csf('po_number_id')].'" align="center" '.$psc.'  width="80" '.$function.'>'.(($new_data[2]== "" || $new_data[2]=="0000-00-00") ? "<span style='color:#FF0000'> $notApplicable</span>" : change_date_format($new_data[2])).'</td><td id="plan_2'.$vid.$row[csf('po_number_id')].'" align="center" '.$pfc.'width="80" '.$function.'> '.(($new_data[3]== ""  || $new_data[3]=="0000-00-00")? "<span style='color:#FF0000'> $notApplicable </span>" : change_date_format($new_data[3])).'</td>';
						}
						else
						{
							if(count($tna_task_id)==$i)
								echo '<td id="plan_1'.$vid.$row[csf('po_number_id')].'" align="center" '.$psc.'   width="80" '.$function.'>'.(($new_data[2]== "" || $new_data[2]=="0000-00-00") ? $notApplicable : change_date_format($new_data[2])).'</td><td id="plan_2'.$vid.$row[csf('po_number_id')].'" align="center" '.$pfc.' '.$function.'> '.(($new_data[3]== ""  || $new_data[3]=="0000-00-00")? "" : change_date_format($new_data[3])).'</td>';
								
							 else
								echo '<td id="plan_1'.$vid.$row[csf('po_number_id')].'" align="center" '.$psc.'  width="80" '.$function.'>'.(($new_data[2]== "" || $new_data[2]=="0000-00-00") ? $notApplicable : change_date_format($new_data[2])).'</td><td id="plan_2'.$vid.$row[csf('po_number_id')].'" align="center" '.$pfc.' width="80" '.$function.'> '.(($new_data[3]== ""  || $new_data[3]=="0000-00-00")? $notApplicable : change_date_format($new_data[3])).'</td>';
						}
						
						
					 }
					echo '</tr>';
					
					
					 
					
					echo '<tr style="cursor:pointer" onClick="change_color(\'actula_'.$h.'\',\''.$bgcolor.'\')" id="actula_'.$h.'"><td width="90">Actual</td>';
					$i=0;
					 foreach($tna_task_id as $vid=>$key)
					 {
						  
						 $i++;
						if ( $new_approval_arr[$row[csf('job_no')]][$key]==""){$new_data=explode("_",$row[csf('status').$key]);}
						else{$new_data=explode("_",$new_approval_arr[$row[csf('job_no')]][$key]);}
						
						if( $new_data[7]!="") $function="onclick='update_tna_process(2,$new_data[7],".$row[csf('po_number_id')].")'";  else $function="";
						$bgcolor1=""; $bgcolor="";
						
						if($actual_manual_update_task_arr[$vid]==''){$function="";}
						
						
						if (trim($new_data[2])!= $blank_date) 
						{
							if (strtotime($new_data[4])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($new_data[2])){$bgcolor="#FFFF00";}//Yellow
							else if (strtotime($new_data[2])<strtotime(date("Y-m-d",time()))){$bgcolor="#FF0000";}//Red
							else {$bgcolor="";}
							
						}
						 
						if ($new_data[3]!= $blank_date) {
							if (strtotime($new_data[5])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($new_data[3])){$bgcolor1="#FFFF00";}//Yellow
							else if (strtotime($new_data[3])<strtotime(date("Y-m-d",time()))){$bgcolor1="#FF0000";}//Red ;
							else{$bgcolor1="";}
						}
						
						if ($new_data[0]!=$blank_date) $bgcolor="";
						if ($new_data[1]!=$blank_date) $bgcolor1="";
						
						
						$idd=$row[csf('job_no')]."".$row[csf('po_number_id')]."".$key;
						if(count($tna_task_id)==$i)
							echo '<td id="actual_1'.$vid.$row[csf('po_number_id')].'" align="center" title="Click Here to Edit Date" id="'.$idd.'1" '.$function.' width="80" bgcolor="'.$bgcolor.'">'.($new_data[0]== "" || $new_data[0]=="0000-00-00" ? "" : change_date_format($new_data[0])).'</td><td id="actual_2'.$vid.$row[csf('po_number_id')].'" align="center" id="'.$idd.'2" title="Click Here to Edit Date" '.$function.' bgcolor="'.$bgcolor1.'" title="'.$new_data[6].'">'.($new_data[1]== "" || $new_data[1]=="0000-00-00" ? "" : change_date_format($new_data[1])).'</td>';
							
						else
							echo '<td id="actual_1'.$vid.$row[csf('po_number_id')].'" align="center" id="'.$idd.'1" title="Click Here to Edit Date"  '.$function.' width="80" bgcolor="'.$bgcolor.'">'.($new_data[0]== "" || $new_data[0]=="0000-00-00" ? "" : change_date_format($new_data[0])).'</td><td id="actual_2'.$vid.$row[csf('po_number_id')].'" align="center" id="'.$idd.'2" title="Click Here to Edit Date" '.$function.' width="80" bgcolor="'.$bgcolor1.'" title="'.$new_data[6].'">'.($new_data[1]== "" || $new_data[1]=="0000-00-00" ? "" : change_date_format($new_data[1])).'</td>';
						
					 }
					echo '</tr>'; 
					
					echo '<tr style="cursor:pointer" onClick="change_color(\'delay_'.$h.'\',\''.$bgcolor.'\')" id="delay_'.$h.'"><td width="90">Delay/Early By</td>';
					$j=0;
					foreach($tna_task_id as $vid=>$key)
					{
						 $j++;
						if ( $new_approval_arr[$row[csf('job_no')]][$key]=="") $new_data=explode("_",$row[csf('status').$key]); 
						else $new_data=explode("_",$new_approval_arr[$row[csf('job_no')]][$key]);
						
						$bgcolor1=""; $bgcolor="";
						
						
						if($new_data[0]!=$blank_date)
						{
							$start_diff1 = datediff( "d", $new_data[0], $new_data[2]);
							if($new_data[0]== "")
							{
								$start_diff=$start_diff1;
							}
							else
							{
								$start_diff=$start_diff1-1;
							}
							if($start_diff<0)
							{
								$bgcolor="#2A9FFF"; //Blue
							}
							if($start_diff>0)
							{
								$bgcolor="";
							}
						}
						else
						{
							if(strtotime(date("Y-m-d"))>strtotime($new_data[2]))
							{
								$start_diff1 = datediff( "d", $new_data[2], date("Y-m-d"));
								if($new_data[0]== "")
								{
									//$start_diff=-abs($start_diff1);
									$start_diff=-abs($start_diff1-1);
								}
								else
								{
									$start_diff=-abs($start_diff1-1);
								}
								//$bgcolor="#FF0000";		//Red
								$bgcolor=($new_data[2]== "" || $new_data[2]=="0000-00-00")?'':'#FF0000';
							}
							if(strtotime(date("Y-m-d"))<=strtotime($new_data[2]))
							{
								$start_diff = "";
								$bgcolor="";
							}
						}
						if($new_data[1]!=$blank_date)
						{
							$finish_diff1 = datediff( "d", $new_data[1], $new_data[3]);
							if($new_data[0]== "")
							{
								$finish_diff=$finish_diff1;
							}
							else
							{	
								$finish_diff=$finish_diff1-1;
							}
							if($finish_diff<0)
							{
								$bgcolor1="#2A9FFF";
							}
							if($finish_diff>0)
							{	
								$bgcolor1="";
							}
						}
						else
						{
							if(strtotime(date("Y-m-d"))>strtotime($new_data[3]))
							{
								
								$finish_diff1 = datediff( "d", $new_data[3], date("Y-m-d"));
								if($new_data[1]== "")
								{
									//$finish_diff=-abs($finish_diff1);
									$finish_diff=-abs($finish_diff1-1);
								}
								else
								{
									$finish_diff=-abs($finish_diff1-1);
								}
								//$bgcolor1="#FF0000";
								$bgcolor1=($new_data[3]== "" || $new_data[3]=="0000-00-00")?'':'#FF0000';
							}
							if(strtotime(date("Y-m-d"))<=strtotime($new_data[3]))
							{
								
								$finish_diff = "";
								$bgcolor1="";
							}
						}
						
						
						
						if(count($tna_task_id)==$j)
							
							echo '<td width="80" align="center" bgcolor="'.$bgcolor.'">'.($start_diff).'</td><td width="80" bgcolor="'.$bgcolor1.'" align="center">'.($finish_diff).'</td>';
						else
							echo '<td width="80" align="center" bgcolor="'.$bgcolor.'">'.($start_diff).'</td><td width="80" bgcolor="'.$bgcolor1.'" align="center">'.($finish_diff).'</td>';
					}
					 
					echo '</tr>';
					
					
					 
		 }
				 
	}
		?>
     
     
    </table>
    </div>
    <div style="width:<? echo $width+20; ?>px;" align="left">
         <table width="<? echo $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <tfoot>
                <th width="40"></th>
                <th width="120"></th>
                <th width="70"></th>
                <th width="110">Total</th>
                <th width="90" id="total_po_qty" align="right"><p><? echo number_format($tot_po_qty,2);?></p></th>
                <th width="70"></th>
                <th width="70"></th>
                <th width="40"></th>
                <th colspan="<? echo (count($tna_task_id)*2)+6;?>"></th>
            </tfoot>
        </table>
    </div>
    
    
          <?
		  
		 $sql = sql_select("select designation,name from variable_settings_signature where report_id=95 and company_id=$cbo_company_id order by sequence_no" );
	     $count=count($sql);

		$width=$width+170;
		$td_width=floor($width/$count);
		
		$standard_width=$count*150;
		
		if($standard_width>$width) $td_width=150;
		
		$no_coloumn_per_tr=floor($width/$td_width);
		$col=$count-2;
		$i=1;
		echo '<table width="'.$width.'"><tr><td width="'.$td_width.'" align="center" valign="bottom">'.$user_arr[$inserted_by].'</td><td height="70" colspan="'.$col.'"></td><td width="'.$td_width.'" align="center" valign="bottom">'.$user_arr[$nameArray_approved_date_row[csf('approved_by')]].'</td></tr><tr>';
		foreach($sql as $row)	
		{
			echo '<td width="'.$td_width.'" align="center" valign="top"><strong style="text-decoration:overline">'.$row[csf("designation")]."</strong><br>".$row[csf("name")].'</td>';
			
			if($i%$no_coloumn_per_tr==0) echo '</tr><tr><td height="70" colspan="'.$no_coloumn_per_tr.'"></td><tr>';
			$i++;
		} 
		echo '</tr></table>';



	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
 	echo "$total_datass****$filename";
	exit();
}



// woven

if($action=="generate_tna_style_follow_up_woven_short")
{

	if($graph==1){
		if($db_type==0)
		{
			$txt_date_from = date("'Y-m-d'",strtotime($txt_date_from));
			$txt_date_to = date("'Y-m-d'",strtotime($txt_date_to));
		}
		else
		{
			$txt_date_from = date("'d-M-Y'",strtotime($txt_date_from));
			$txt_date_to = date("'d-M-Y'",strtotime($txt_date_to));
		}
	}

	//$actual_manual_update_task_arr=return_library_array("select task_id,task_id from tna_manual_permission where is_actual_manual=1  and company_id=$cbo_company_name","task_id","task_id");
	//$plan_manual_update_task_arr=return_library_array("select task_id,task_id from tna_manual_permission where is_plan_manual=1  and company_id=$cbo_company_name","task_id","task_id");

	$mod_sql= sql_select("select a.id,a.task_catagory,a.task_name,a.task_short_name,a.task_type,a.completion_percent,a.task_sequence_no,b.task_template_id,b.lead_time ,b.tna_task_id
	from lib_tna_task a,tna_task_template_details b where a.task_name=b.tna_task_id and b.task_type=1 AND a.task_type = 1 and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 order by a.task_sequence_no asc");
	
	$tna_task_array=array();
	$tna_task_id=array();
	$tna_task_cat=array();
	$tna_task_name_arr=array();
	foreach ($mod_sql as $row)
	{
		$tna_task_id[$row[csf("task_name")]]=$row[csf("task_name")];
		$tna_task_array[$row[csf("task_name")]] =$row[csf("task_short_name")];
		$tna_task_name_array[$row[csf("id")]] =$tna_task_name[$row[csf("task_name")]];
		$tna_task_cat[$row[csf("id")]]=$row[csf("task_catagory")];
		$tna_task_name_arr[$row[csf("id")]]=$row[csf("task_name")];
		$lead_time_array[$row[csf("task_template_id")]]=$row[csf("lead_time")];
		$tast_tmp_id_arr[$row[csf("task_template_id")]][$row[csf("tna_task_id")]]=$row[csf("tna_task_id")];
	}
	//print_r($lead_time_array);die;
	
	$cbo_company_id=$cbo_company_name;
	
	$order_status_cond="";
	if(str_replace("'","",$cbo_order_status)>0) $order_status_cond=" and b.is_confirmed=$cbo_order_status";
	
	if(str_replace("'","",$cbo_company_name)==0) $cbo_company_name=""; else $cbo_company_name=" and a.company_name = $cbo_company_name";
	if(str_replace("'","",$cbo_buyer_name)==0) $cbo_buyer_name=""; else $cbo_buyer_name=" and a.buyer_name = $cbo_buyer_name";
	if(str_replace("'","",$cbo_team_member)==0) $cbo_team_member=""; else $cbo_team_member=" and a.dealing_marchant = $cbo_team_member";
	
	if(str_replace("'","",$cbo_search_type)==1){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
	}
	else if(str_replace("'","",$cbo_search_type)==3){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and c.country_ship_date between $txt_date_from and $txt_date_to";
	}
	else if(str_replace("'","",$cbo_search_type)==4){
		if($db_type==0)
		{
			if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)==""){$date_range="";}else{ 
			$date_range=" and b.insert_date between ".$txt_date_from." and ".$txt_date_to."";}
		}
		else
		{
			
			if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)==""){$date_range="";}else{ 
			$date_range=" and b.insert_date between ".$txt_date_from." and '".str_replace("'","",$txt_date_to)." 11:59:59 PM'";}
		}
	}
	else
	{
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.po_received_date between $txt_date_from and $txt_date_to";
	}
	
	

	
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if($txt_job_no=="") $txt_job_no=""; else $txt_job_no=" and a.job_no like('%$txt_job_no')";
	$txt_order_no=str_replace("'","",$txt_order_no);
	if($txt_order_no=="") $txt_order_no=""; else $txt_order_no=" and b.po_number ='$txt_order_no'";
	$txt_file_no=str_replace("'","",$txt_file_no);
	if($txt_file_no=="") $file_cond=""; else $file_cond=" and b.file_no ='$txt_file_no'";
	$txt_int_ref_no=str_replace("'","",$txt_int_ref_no);
	if($txt_int_ref_no=="") $ref_cond=""; else $ref_cond=" and b.grouping ='$txt_int_ref_no'";
	
	$txt_style_ref_no=str_replace("'","",$txt_style_ref_no);
	if($txt_style_ref_no=="") $txt_style_ref_no=""; else $txt_style_ref_no=" and a.style_ref_no ='$txt_style_ref_no'";
	
	if(str_replace("'","",$cbo_shipment_status)==4){$shipment_status_con=" and b.shiping_status=3";}
	else if(str_replace("'","",$cbo_shipment_status)==1){$shipment_status_con=" and b.shiping_status !=3";}
	
	
	//echo $cbo_shipment_status;die;
	
	
	$tna_all_task=implode(",",$tna_task_id);
	
	if(str_replace("'","",$cbo_search_type)==3)
	{
		$sql = "SELECT a.team_leader,a.factory_marchant,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, b.id ,b.po_number, b.file_no, b.grouping as in_ref_no,b.po_quantity,b.PUB_SHIPMENT_DATE,a.GMTS_ITEM_ID
		FROM  wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c 
		WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no  and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 $order_status_cond $file_cond $ref_cond";
	}
	else
	{
		$sql = "SELECT a.team_leader,a.factory_marchant,a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, b.id, b.po_number, b.file_no, b.grouping as in_ref_no ,b.po_quantity,b.PUB_SHIPMENT_DATE,a.GMTS_ITEM_ID
		FROM  wo_po_details_master a,  wo_po_break_down b 
		WHERE a.job_no=b.job_no_mst $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no  and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 $order_status_cond  $file_cond $ref_cond"; 
	}
	
     //echo $sql; 
	$result = sql_select( $sql ) ;
	$wo_po_details_master = array();
	$po_no_arr=array();
	$job_no_arr=array();
	foreach( $result as  $row ) 
	{	
		$wo_po_details_master[$row[csf('id')]]['company_name']=$row[csf('company_name')];
		$po_no_arr[]=$row[csf('id')];
		$job_no_arr[]=$row[csf('job_no')];
		
		
		/*$wo_po_details_master[$row[csf('job_no')]][$row[PUB_SHIPMENT_DATE]]['po_id'][$row[csf('id')]]= $row[csf('id')];
		$wo_po_details_master[$row[csf('job_no')]][$row[PUB_SHIPMENT_DATE]]['po_number'][$row[csf('po_number')]]= $row[csf('po_number')];
		$wo_po_details_master[$row[csf('job_no')]][$row[PUB_SHIPMENT_DATE]]['set_smv']= $row[csf('set_smv')];
		$wo_po_details_master[$row[csf('job_no')]][$row[PUB_SHIPMENT_DATE]]['file_no']= $row[csf('file_no')];

		$wo_po_details_master[$row[csf('job_no')]][$row[PUB_SHIPMENT_DATE]]['dealing_marchant']=$row[csf('dealing_marchant')];
		$wo_po_details_master[$row[csf('job_no')]][$row[PUB_SHIPMENT_DATE]]['factory_marchant']=$row[csf('factory_marchant')];
		$wo_po_details_master[$row[csf('job_no')]][$row[PUB_SHIPMENT_DATE]]['team_leader']=$row[csf('team_leader')];
		$wo_po_details_master[$row[csf('job_no')]][$row[PUB_SHIPMENT_DATE]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
		$wo_po_details_master[$row[csf('job_no')]][$row[PUB_SHIPMENT_DATE]]['in_ref_no']= $row[csf('in_ref_no')];
		$wo_po_details_master[$row[csf('job_no')]][$row[PUB_SHIPMENT_DATE]]['buyer_name']=$row[csf('buyer_name')];
		$wo_po_details_master[$row[csf('job_no')]][$row[PUB_SHIPMENT_DATE]]['style_ref_no']=$row[csf('style_ref_no')];
		$wo_po_details_master[$row[csf('job_no')]][$row[PUB_SHIPMENT_DATE]]['po_quantity']+=$row[csf('po_quantity')];*/
	
	
	
		$wo_po_details_master[$row[csf('job_no')]]['po_quantity']+=$row[csf('po_quantity')];
		$wo_po_details_master[$row[csf('job_no')]]['po_number'][$row[csf('po_number')]]= $row[csf('po_number')];
		$wo_po_details_master[$row[csf('job_no')]]['style_ref_no']=$row[csf('style_ref_no')];
		$wo_po_details_master[$row[csf('job_no')]]['buyer_name']=$row[csf('buyer_name')];
		$wo_po_details_master[$row[csf('job_no')]]['dealing_marchant']=$row[csf('dealing_marchant')];
		$wo_po_details_master[$row[csf('job_no')]]['GMTS_ITEM_ID']=$row[csf('GMTS_ITEM_ID')];
		
		
	}
	
	
	
	
	
	
	
 
 
	
	$sql = "SELECT buyer_name,id FROM  lib_buyer WHERE is_deleted = 0 and status_active=1 order by id asc";
	$result = sql_select( $sql ) ;
	$buyer_name = array();
	foreach( $result as  $row ) 
	{	
		$buyer_name[$row[csf('id')]]=$row[csf('buyer_name')];
	}
	
	
	$po_no_arr_all=implode(",",$po_no_arr); if($po_no_arr_all!="") $po_no_arr_all .=",0"; else $po_no_arr_all .="0"; 
	$job_no_all="'".implode("','",$job_no_arr)."'";
	$c=count($tna_task_id);
	
	if($db_type==0)
	{
		$sql ="select a.id,a.task_number,a.po_number_id, a.job_no, a.shipment_date,min(b.pub_shipment_date) as pub_shipment_date, a.template_id, a.po_receive_date,b.insert_date,";
		$i=1;
	
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c) $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id, ";
			else $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id ";
			$i++;
		}
		
		$sql .=" from tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id and a.po_number_id in( $po_no_arr_all ) and a.job_no in ($job_no_all) $shipment_status_con and b.status_active=1  and b.po_quantity>0 $order_status_cond and a.task_type=1 group by a.po_number_id,a.job_no,b.insert_date order by a.shipment_date,a.po_number_id,a.job_no"; 
	}
	else
	{
		$sql ="select a.JOB_NO,a.TASK_NUMBER,  
		LISTAGG(cast(a.id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as id, 
		LISTAGG(cast(a.po_number_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as po_number_id, 
		LISTAGG(cast(a.template_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as template_id, 
		min(a.shipment_date) as SHIPMENT_DATE,
		min(b.pub_shipment_date) as PUB_SHIPMENT_DATE,
		min(a.po_receive_date) as PO_RECEIVE_DATE,
		min(b.insert_date) as INSERT_DATE,
		
	    (min(a.actual_start_date) || '_' || min(a.task_start_date) || '_' || MAX(a.actual_finish_date) || '_' || MAX(a.task_finish_date) || '_' || min(a.plan_start_flag) || '_' || MAX(a.plan_finish_flag) || '_' || min(a.notice_date_start) || '_' || min(a.notice_date_end) || '_' || LISTAGG(cast(a.id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) ) as STATUS ";
			
			
		//------------------
			$sql_order_con='';
			$po_no_arr_all=explode(',',$po_no_arr_all);
			$chunk_po_no_arr_all=array_chunk(array_unique($po_no_arr_all),999);
			$p=1;
			foreach($chunk_po_no_arr_all as $rlz_sub_id)
			{
				if($p==1) $sql_order_con .=" and (a.po_number_id in(".implode(',',$rlz_sub_id).")"; 
				else $sql_sub_lc .=" or a.po_number_id in(".implode(',',$rlz_sub_id).")";
				$p++;
			}
			$sql_order_con .=" )";
			
			$sql_job_con='';
			$job_no_all=explode(',',$job_no_all);
			$chunk_job_no_all=array_chunk(array_unique($job_no_all),999);
			$q=1;
			foreach($chunk_job_no_all as $rlz_sub_id)
			{
				if($q==1) $sql_job_con .=" and (a.job_no in(".implode(',',$rlz_sub_id).")"; 
				else $sql_sub_lc .=" or a.job_no in(".implode(',',$rlz_sub_id).")";
				$p++;
			}
			$sql_job_con .=" )";
			
			
		//-------------------------------
		$sql .=" from  tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id $sql_order_con $sql_job_con $shipment_status_con and b.status_active=1 and b.po_quantity>0 $order_status_cond  and a.task_type=1  group by a.job_no,a.task_number order by a.job_no"; 
	}
	$data_sql_res= sql_select($sql);
	      //echo $sql;die;
		
		$dataArr=array();
		foreach($data_sql_res as $row){
			
			list($actualStart,$planStart,$actualFinish,$planFinish)=explode('_',$row[STATUS]);
			
			$start_delay=datediff( "d", date("Y-m-d",strtotime($planStart)), date("Y-m-d",strtotime($actualStart)));
			$finish_delay=datediff( "d", date("Y-m-d",strtotime($planFinish)), date("Y-m-d",strtotime($actualFinish)));

			if($actualStart==''){$start_delay='';}
			if($actualFinish==''){$finish_delay='';}
			
			$dataArr[$row[TASK_NUMBER]]=array(
				PLAN_START=>$planStart,
				PLAN_FINISH=>$planFinish,
				ACTUAL_START=>$actualStart,
				ACTUAL_FINISH=>$actualFinish,
				START_DELAY=>$start_delay,
				FINISH_DELAY=>$finish_delay,
			);
			
			
			$JOB_NO = $row[JOB_NO];
			$SHIPMENT_DATE = $row[PUB_SHIPMENT_DATE];
			$PUB_SHIPMENT_DATE = $row[PUB_SHIPMENT_DATE];
			$SHIPMENT_DATE = $row[SHIPMENT_DATE];
			$PO_RECEIVE_DATE = $row[PO_RECEIVE_DATE];


		}
		
		
		
		
		//echo $sql_con;die;
		
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );			
		
	$sql = "SELECT team_member_name,id FROM lib_mkt_team_member_info WHERE is_deleted = 0 and status_active=1 order by id asc";
	$result = sql_select( $sql ) ;
	$team_member_name = array();
	foreach( $result as  $row ) 
	{	
		$team_member_name[$row[csf('id')]]=$row[csf('team_member_name')];
	}

	
	
	
	
	ob_start();
	
	?>
    
    
    
    <div id="scroll_body">
    
     <table width="500" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <tr>
                <td>Comapny:</td>
                <td><?=$company_library[$cbo_company_id];?></td>
                <td>Buyer:</td>
                <td><?=$buyer_name[$wo_po_details_master[$JOB_NO]['buyer_name']];?></td>
                <td>Job No:</td>
                <td><?=$JOB_NO;?></td>
            </tr>
            <tr>
                <td>Style:</td>
                <td><?=$wo_po_details_master[$JOB_NO]['style_ref_no'];?></td>
                <td>PO No:</td>
                <td colspan="2"><p><?=implode(', ',$wo_po_details_master[$JOB_NO]['po_number']);?></p></td>
            </tr>
            <tr>
                <td>Garments Item:</td>
                <td><?=$garments_item[$wo_po_details_master[$JOB_NO]['GMTS_ITEM_ID']];?></td>
                <td>Ship Date:</td>
                <td><?=change_date_format($SHIPMENT_DATE);?></td>
                <td>Quantity :</td>
                <td><?=$wo_po_details_master[$JOB_NO]['po_quantity'];?></td>
            </tr>
            
            <tr>
                <td>Dealing Merchant:</td>
                <td colspan="3"><?=$team_member_name[$wo_po_details_master[$JOB_NO]['dealing_marchant']];?></td>
                <td>Lead Time:</td>
                <td title="Ship Date:<?=$SHIPMENT_DATE;?>,Receive Date:<?=$PO_RECEIVE_DATE;?>"><?=datediff( "d", date("Y-m-d",strtotime($PO_RECEIVE_DATE)), date("Y-m-d",strtotime($SHIPMENT_DATE)));?></td>
            </tr>
            
            
            
   </table>
   
    
    <table width="500" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th>Event/Task Name</th>
                <th width="60">Status</th>
                <th width="70">Start</th>
                <th width="70">Finish</th>
            </tr>
        </thead>

       
    <?
		
		$i=1;					
		foreach($tna_task_id as $vid=>$key)
		{
			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
			?>
            <tr bgcolor="<?=$bgcolor;?>">
            	<td rowspan="3"><?=$i;?></td>
            	<td rowspan="3" title="<?=$key.'='.$tna_task_name[$key];?>"><?=$tna_task_name[$key];?></td>
                <td>Plan</td>
                <td align="center"><?=change_date_format($dataArr[$key][PLAN_START]);?></td>
                <td align="center"><?=change_date_format($dataArr[$key][PLAN_FINISH]);?></td>
            </tr>
            <tr>
                <td>Actual</td>
                <td align="center"><?=change_date_format($dataArr[$key][ACTUAL_START]);?></td>
                <td align="center"><?=change_date_format($dataArr[$key][ACTUAL_FINISH]);?></td>
            </tr>
            <tr>
                <td>Delay/Early</td>
                <td align="center"><?=$dataArr[$key][START_DELAY];?></td>
                <td align="center"><?=$dataArr[$key][FINISH_DELAY];?></td>
            </tr>
            <?
		$i++; 
		}
		
		?>
        
        </table>
					
<?
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
 	echo "$total_datass****$filename";
	exit();
}

if($action=="task_his_dtls")
{
	

	require_once('../../../includes/class4/class.conditions.php');
	require_once('../../../includes/class4/class.reports.php');
	require_once('../../../includes/class4/class.fabrics.php');
	require_once('../../../includes/class4/class.yarns.php');
	require_once('../../../includes/class4/class.washes.php');
	require_once('../../../includes/class4/class.emblishments.php');
	require_once('../../../includes/class4/class.trims.php');
 


	
	if($db_type==0) $blank_date="0000-00-00"; else $blank_date=""; 	
	
	echo load_html_head_contents("TNA","../../../", 1, 1, $unicode);
	extract($_REQUEST);

	
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	//$tna_task_arr=return_library_array( "select task_name, task_short_name from lib_tna_task",'task_name','task_short_name');
	$lead_time_array=return_library_array("select task_template_id,lead_time from tna_task_template_details where task_type=6 group by lead_time,task_template_id","task_template_id",'lead_time');

	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier where status_active =1 and is_deleted=0",'id','supplier_name');
	$trim_group_library= return_library_array("select id, item_name from lib_item_group where status_active =1 and is_deleted=0",'id','item_name');
	$dealing_mar_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0",'id','team_member_name');
	$designation_arr=return_library_array( "select id, CUSTOM_DESIGNATION from LIB_DESIGNATION where status_active =1 and is_deleted=0",'id','CUSTOM_DESIGNATION');
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');
 

	$fabric_description_array=array();
	$sql="select b.LIB_YARN_COUNT_DETER_ID,a.BOOKING_NO,A.JOB_NO AS JOB_NO, A.PO_BREAK_DOWN_ID AS PO_BREAK_DOWN_ID,A.GREY_FAB_QNTY AS GREY_FAB_QNTY,A.FIN_FAB_QNTY AS FIN_FAB_QNTY,A.ADJUST_QTY ADJUST_QTY,A.RATE AS RATE,A.AMOUNT AS AMOUNT,A.GMTS_COLOR_ID AS GMTS_COLOR_ID,A.DIA_WIDTH AS DIA_WIDTH, B.ID AS PRE_COST_FABRIC_COST_DTLS_ID, B.BODY_PART_ID AS BODY_PART_ID,B.COLOR_TYPE_ID AS COLOR_TYPE_ID,B.FABRIC_DESCRIPTION AS FABRIC_DESCRIPTION, B.GSM_WEIGHT AS GSM_WEIGHT,B.UOM AS UOM from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b  where a.pre_cost_fabric_cost_dtls_id=b.id and a.status_active =1 and a.is_deleted=0  and A.PO_BREAK_DOWN_ID=".$po_id."";
   // echo $sql;
   $dataArray=sql_select($sql);
   foreach($dataArray as $sql_row)
   {
	   $fabric_description_array[$sql_row['BOOKING_NO']]=$body_part[$sql_row['BODY_PART_ID']].', '.$color_type[$sql_row['COLOR_TYPE_ID']].', '.$sql_row['FABRIC_DESCRIPTION'].', '.$sql_row['GSM_WEIGHT'].', '.$unit_of_measurement[$sql_row['UOM']];

	   $fabric_description_dtls_array[$sql_row['PRE_COST_FABRIC_COST_DTLS_ID']]=$body_part[$sql_row['BODY_PART_ID']].', '.$color_type[$sql_row['COLOR_TYPE_ID']].', '.$sql_row['FABRIC_DESCRIPTION'].', '.$sql_row['GSM_WEIGHT'].', '.$unit_of_measurement[$sql_row['UOM']];

	   $fabric_description_count_deter_array[$sql_row['LIB_YARN_COUNT_DETER_ID']]=$body_part[$sql_row['BODY_PART_ID']].', '.$color_type[$sql_row['COLOR_TYPE_ID']].', '.$sql_row['FABRIC_DESCRIPTION'].', '.$sql_row['GSM_WEIGHT'].', '.$unit_of_measurement[$sql_row['UOM']];
   }
   
   
   

	$order_sql ="select b.COMPANY_NAME,b.BUYER_NAME,b.JOB_NO,b.STYLE_REF_NO,b.GMTS_ITEM_ID, SET_SMV,DEALING_MARCHANT, 
	LISTAGG(cast(a.po_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as PO_NUMBER,
	LISTAGG(cast(a.po_received_date as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as PO_RECEIVED_DATE,
	LISTAGG(cast(a.shipment_date as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as SHIPMENT_DATE,
	SUM(po_quantity*total_set_qnty) as PO_QTY_PCS from  wo_po_break_down a,wo_po_details_master b where a.id in($po_id) and a.job_no_mst=b.job_no group by b.company_name,b.buyer_name,DEALING_MARCHANT,b.job_no,b.style_ref_no,b.gmts_item_id, set_smv";
	$order_sql_res=sql_select($order_sql);
	//echo $sql;die;

	//---class
	$condition= new condition();
	$cbo_company = $order_sql_res[0]['COMPANY_NAME'];
	$txt_job_no = $order_sql_res[0]['JOB_NO'];
	if($cbo_company>0){
		$condition->company_name("=$cbo_company");
	}
	if(str_replace("'","",$txt_job_no) !=''){
		$condition->job_no(" = '".str_replace("'","",$txt_job_no)."'");
	}
	if(trim($po_id) !=''){
		$condition->po_id(" =$po_id");
	}
	$condition->init();
	 //echo $fabric->getQuery();die;
	$trims= new trims($condition);
	$trim_group_qty_arr=$trims->getQtyArray_by_orderCountryAndPrecostdtlsid();
	foreach($trim_group_qty_arr[$po_id] as $country=>$rowArr){
		foreach($rowArr as $pre_dtl_id=>$val){
			$trimsReqArr[$pre_dtl_id]+=$val;
		}	
	}
	//print_r($trimsReqArr);
	//class....
 


	//budget app..........................................
	$preCostSql = "select d.BYPASS,b.COSTING_DATE,a.MST_ID,a.APPROVED_NO,a.APPROVED_DATE,a.APPROVED_BY,c.USER_FULL_NAME,c.DESIGNATION from APPROVAL_HISTORY a,WO_PRE_COST_MST b,USER_PASSWD c,ELECTRONIC_APPROVAL_SETUP d where d.USER_ID=c.id and c.id=a.APPROVED_BY and  a.ENTRY_FORM in(15,46) and d.ENTRY_FORM in(15,46) and a.mst_id=b.id and d.IS_DELETED=0 and COMPANY_ID='".$order_sql_res[0]['COMPANY_NAME']."' and b.JOB_NO = '".$order_sql_res[0]['JOB_NO']."'";
	//echo $preCostSql;
	$preCostSqlRes=sql_select($preCostSql);
	$budget_data_arr=array();
	foreach($preCostSqlRes as $row){
		$budget_data_arr[$row['APPROVED_BY']]=$row;
	}


	//-------------------------------------------------Trims Booking
	$bookingSql ="select b.PRE_COST_FABRIC_COST_DTLS_ID,b.PO_BREAK_DOWN_ID,a.CURRENCY_ID,a.BOOKING_NO,a.BOOKING_DATE,a.PAY_MODE,a.SOURCE,a.SUPPLIER_ID,b.TRIM_GROUP,b.DESCRIPTION,c.TRIM_TYPE,b.WO_QNTY,c.ITEM_CATEGORY,b.AMOUNT from WO_BOOKING_MST a, WO_BOOKING_dtls b, LIB_ITEM_GROUP c where a.booking_no=b.BOOKING_NO and a.item_category=4 and c.ITEM_CATEGORY=4 and b.TRIM_GROUP=c.id and b.JOB_NO='".$order_sql_res[0]['JOB_NO']."' and COMPANY_ID='".$order_sql_res[0]['COMPANY_NAME']."' and b.PO_BREAK_DOWN_ID=$po_id";
	//echo $bookingSql;

	$bookingSqlRes=sql_select($bookingSql);
	$booking_data_arr=array();
	foreach($bookingSqlRes as $row){
		$row['SOURCE']=($row['SOURCE']==2)?3:$row['SOURCE'];
		$key=$row['BOOKING_NO'].$row['ITEM_CATEGORY'].$row['TRIM_TYPE'].$row['SOURCE'].$row['TRIM_GROUP'].$row['DESCRIPTION'];
		
		$booking_data_arr['DATA'][$row['ITEM_CATEGORY']][$row['TRIM_TYPE']][$row['SOURCE']][$key]=$row;
		$booking_data_arr['AMOUNT'][$key]+=$row['AMOUNT'];
		$booking_data_arr['WO_QNTY'][$key]+=$row['WO_QNTY'];
		$booking_data_arr['TRIMS_ITEM'][$row['PO_BREAK_DOWN_ID']][$row['TRIM_GROUP']]=$row['DESCRIPTION'];

		$booking_data_arr['BOOKING_QTY'][$row['BOOKING_NO']][$row['TRIM_GROUP']][$row['TRIM_TYPE']][$row['SOURCE']]+=$row['WO_QNTY'];
		$booking_data_arr['BOOKING_NO'][$row['PO_BREAK_DOWN_ID']][$row['TRIM_GROUP']][$row['BOOKING_NO']]=$row['BOOKING_NO'];

	}

	//var_dump($booking_data_arr['BOOKING_QTY']['RpC-TB-22-00261']); 


	//-------------------------------------------------Fabric Booking
	$bookingFbSql ="select b.PRE_COST_FABRIC_COST_DTLS_ID,a.CURRENCY_ID,a.BOOKING_NO,a.BOOKING_DATE,a.PAY_MODE,a.SOURCE,a.SUPPLIER_ID,a.ITEM_CATEGORY,b.CONSTRUCTION,b.COPMPOSITION,b.GSM_WEIGHT,b.DIA_WIDTH, b.GREY_FAB_QNTY,b.FIN_FAB_QNTY, b.AMOUNT,B.PO_BREAK_DOWN_ID from WO_BOOKING_MST a, WO_BOOKING_dtls b where a.booking_no=b.BOOKING_NO and a.item_category=2   and b.JOB_NO='".$order_sql_res[0]['JOB_NO']."' and COMPANY_ID='".$order_sql_res[0]['COMPANY_NAME']."'  and B.PO_BREAK_DOWN_ID=$po_id AND a.IS_DELETED =0 and a.STATUS_ACTIVE=1 AND b.IS_DELETED =0 and b.STATUS_ACTIVE=1";
	 //echo $bookingFbSql;
	$bookingFbSqlRes=sql_select($bookingFbSql);
	$booking_fb_data_arr=array();
	foreach($bookingFbSqlRes as $row){
		$row['SOURCE']=($row['SOURCE']==2)?3:$row['SOURCE'];
		$key=$row['BOOKING_NO'].$row['ITEM_CATEGORY'].$row['SOURCE'].$row['PRE_COST_FABRIC_COST_DTLS_ID'];
		$booking_fb_data_arr['DATA'][$row['ITEM_CATEGORY']][$row['SOURCE']][$key]=$row;
		$booking_fb_data_arr['AMOUNT'][$key]+=$row['AMOUNT'];
		$booking_fb_data_arr['WO_QNTY'][$key]+=$row['FIN_FAB_QNTY'];
		$booking_fb_data_arr['REQ_WO_QNTY'][$key]+=$row['GREY_FAB_QNTY'];
		
		$booking_fb_data_arr['BOOKING_QTY'][$fabric_description_dtls_array[$row['PRE_COST_FABRIC_COST_DTLS_ID']]]+=$row['GREY_FAB_QNTY'];
		$booking_fb_data_arr['BOOKING_NO'][$row['PO_BREAK_DOWN_ID']][$row['ITEM_CATEGORY']][$row['SOURCE']]=$row['BOOKING_NO'];
	}
 

	//var_dump($booking_fb_data_arr['BOOKING_QTY']);die;
	//
	

	//.............................................labdip
	$labdipSql="select a.id, a.JOB_NO_MST,a.COLOR_NAME_ID,a.LAPDIP_TARGET_APPROVAL_DATE,a.SEND_TO_FACTORY_DATE,a.APPROVAL_STATUS_DATE,a.APPROVAL_STATUS,a.STATUS_ACTIVE  from WO_PO_LAPDIP_APPROVAL_INFO a where a.JOB_NO_MST='".$order_sql_res[0]['JOB_NO']."' and a.PO_BREAK_DOWN_ID= $po_id order by id";
	$labdipSqlRes=sql_select($labdipSql);
	$labdip_data_arr=array();
	foreach($labdipSqlRes as $row)
	{
		$labdip_data_arr['DATA'][$row['COLOR_NAME_ID']]=$row;
	}


	//.............................................trims App
	$trimsAppSql="select a.JOB_NO_MST,a.PO_BREAK_DOWN_ID,a.ACCESSORIES_TYPE_ID,a.TARGET_APPROVAL_DATE,a.SENT_TO_SUPPLIER,a.SUBMITTED_TO_BUYER,a.APPROVAL_STATUS_DATE,a.APPROVAL_STATUS,a.STATUS_ACTIVE  from wo_po_trims_approval_info a where  a.JOB_NO_MST='".$order_sql_res[0]['JOB_NO']."' and a.PO_BREAK_DOWN_ID= $po_id";
	$trimsAppSqlRes=sql_select($trimsAppSql);
	$trims_data_arr=array();
	foreach($trimsAppSqlRes as $row)
	{
		$trims_data_arr['DATA'][]=$row;
	}	

	//finish fabric rec--------------------------------------------------------
	$finishFabRecSql = "select d.FABRIC_DESCRIPTION_ID,c.PO_BREAKDOWN_ID,a.RECEIVE_BASIS,a.SOURCE,a.RECV_NUMBER,a.RECEIVE_DATE,a.BOOKING_NO,a.CHALLAN_NO,a.SUPPLIER_ID,SUM(c.QUANTITY) AS QUANTITY, SUM(b.ORDER_RATE*c.QUANTITY) as VALUE from inv_receive_master a,PRO_FINISH_FABRIC_RCV_DTLS d,INV_TRANSACTION b,ORDER_WISE_PRO_DETAILS c where a.id=d.mst_id and d.TRANS_ID=b.id and a.id=b.mst_id and b.id=c.TRANS_ID and a.ENTRY_FORM=37 and a.ITEM_CATEGORY=2 and c.TRANS_TYPE=1 and c.ENTRY_FORM=37 and b.COMPANY_ID=".$order_sql_res[0]['COMPANY_NAME']." and b.ITEM_CATEGORY=2 and c.PO_BREAKDOWN_ID=$po_id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and d.STATUS_ACTIVE=1 and d.IS_DELETED=0  and b.STATUS_ACTIVE=1 and b.IS_DELETED=0  and c.STATUS_ACTIVE=1 and c.IS_DELETED=0
	GROUP BY d.FABRIC_DESCRIPTION_ID,c.PO_BREAKDOWN_ID,a.RECEIVE_BASIS,a.SOURCE,a.RECV_NUMBER,a.RECEIVE_DATE,a.BOOKING_NO,a.CHALLAN_NO,a.SUPPLIER_ID";
	// echo $finishFabRecSql;
	$finishFabRecSqlRes=sql_select($finishFabRecSql);
	$finish_fab_rec_data_arr=array();
	foreach($finishFabRecSqlRes as $row)
	{
		$row['SOURCE']=($row['SOURCE']==2)?3:$row['SOURCE'];
		
		$finish_fab_rec_data_arr['DATA'][$row['SOURCE']][]=$row;
	}	

	


	//finish Trims rec--------------------------------------------------------
	$finishTrimsRecSql = "select c.PO_BREAKDOWN_ID,a.RECEIVE_BASIS,a.SOURCE,a.FABRIC_SOURCE,a.RECV_NUMBER,a.RECEIVE_DATE,a.BOOKING_NO,a.CHALLAN_NO,a.SUPPLIER_ID,d.ITEM_GROUP_ID,e.TRIM_TYPE,SUM(c.QUANTITY) AS QUANTITY, SUM(d.RATE*c.QUANTITY) as VALUE from inv_receive_master a,INV_TRANSACTION b,ORDER_WISE_PRO_DETAILS c,inv_trims_entry_dtls d, LIB_ITEM_GROUP e where e.ITEM_CATEGORY=4 and d.ITEM_GROUP_ID=e.id and a.id=d.mst_id  and b.id=d.TRANS_ID  and  a.id=b.mst_id and b.id=c.TRANS_ID and a.ENTRY_FORM=24 and a.ITEM_CATEGORY=4 and c.TRANS_TYPE=1 and c.ENTRY_FORM=24  and b.ITEM_CATEGORY=4 and b.COMPANY_ID=".$order_sql_res[0]['COMPANY_NAME']." and b.ITEM_CATEGORY=4 and c.PO_BREAKDOWN_ID=$po_id  and a.IS_DELETED=0 and a.STATUS_ACTIVE=1  and b.IS_DELETED=0 and b.STATUS_ACTIVE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1
	GROUP BY c.PO_BREAKDOWN_ID,a.RECEIVE_BASIS,a.SOURCE,a.FABRIC_SOURCE,a.RECV_NUMBER,a.RECEIVE_DATE,a.BOOKING_NO,a.CHALLAN_NO,a.SUPPLIER_ID,d.ITEM_GROUP_ID,e.TRIM_TYPE";
 	// echo $finishTrimsRecSql;

	$finishTrimsRecSqlRes=sql_select($finishTrimsRecSql);
	$finish_trims_rec_data_arr=array();
	foreach($finishTrimsRecSqlRes as $row)
	{
		$row['SOURCE']=($row['SOURCE']==2)?3:$row['SOURCE'];
		$finish_trims_rec_data_arr['DATA'][$row['SOURCE']][$row['TRIM_TYPE']][]=$row;
	}	


	?> 
	

	</head>
	<body>


    <table border="1" rules="all" class="rpt_table">
    	<?php
		foreach($order_sql_res as $row)
		{
			
		?>
    	<tr>
        	<td width="100"><b>Company</b></td>
            <td><?= $company_library[$row['COMPANY_NAME']]; ?></td>
            <td width="100"><b>Buyer</b></td>
            <td><?= $buyer_arr[$row['BUYER_NAME']];  ?></td>
            <td width="100"><b>Job Number</b></td>
           	<td><?= $row['JOB_NO']; ?></td>
        </tr>
        <tr>
            <td><b>Order No</b></td>
           	<td><?= $row['PO_NUMBER']; ?></td>
            <td><b>Style Ref.</b></td>
            <td><?= $row['STYLE_REF_NO'];  ?></td>
            <td><b>Dealing Merchant</b></td>
            <td><?= $dealing_mar_arr[$row['DEALING_MARCHANT']];?></td>
        </tr>
        <tr>
            <td><b>Order Recv. Date</b></td>
           	<td>
				<?php  
					foreach(explode(',',$row['PO_RECEIVED_DATE']) as $po_received_date){
						$po_received_date_arr[$po_received_date]=change_date_format($po_received_date);
					}
					echo implode(',',$po_received_date_arr);
				?>
			</td>
        	<td><b>Ship Date</b></td>
            <td>
				<?php  
					foreach(explode(',',$row['SHIPMENT_DATE']) as $shipment_date_date){
						$shipment_date_date_arr[$shipment_date_date]=change_date_format($shipment_date_date);
					}
						echo implode(',',$shipment_date_date_arr);
					?>
				
			</td>
            <td><b>Quantity (PCS)</b></td>
            <td><?php echo $row['PO_QTY_PCS'];?></td>
        </tr>

        <?php
		}
		?>
    </table>
	<br>
    <h3>Budget Approval</h3>
	<table border="1" rules="all" class="rpt_table">
		<thead>
			<th>Costing Date</th>
			<th>Signatory</th>
			<th>Designation</th>
			<th>Can Bypass</th>
			<th>Approval Date & Time</th>
			<th>Approve No</th>
		</thead>
		<tbody>
			<?
			$rowSpan = count($budget_data_arr);
			$flag=0;
			foreach($budget_data_arr as $row)
			{
			if($flag==0){	
			?>
			<tr>
				<td align="center" valign="middle" rowspan="<?=$rowSpan;?>"><?=change_date_format($row['COSTING_DATE']);?></td>
			<?
			}
			else{
				echo "</tr>";
			}
			?>
				<td><?=$row['USER_FULL_NAME'];?></td>
				<td><?=$designation_arr[$row['DESIGNATION']];?></td>
				<td align="center"><?=$yes_no[$row['BYPASS']];?></td>
				<td align="center"><?=date('d-m-y h:i:s a',strtotime($row['APPROVED_DATE']));?></td>
				<td align="center"><?=$row['APPROVED_NO'];?></td>
			</tr>
			<?
			$flag=1;
			}
			?>
		</tbody>
	</table>
	<br>

	<h3>Finish Fabric Booking-Local</h3>
	<table border="1" rules="all" class="rpt_table">
		<thead>
			<th>SL</th>	
			<th>Wo No</th>	
			<th>Wo Date	</th>
			<th>Pay Mode</th>	 
			<th>Source</th>	
			<th>Supplier</th>	
			<th width="200">Fabric Description</th>	
			<th>Require </th>	
			<th>Wo Qty</th>	
			<th>Wo value(USD)</th>
		</thead>
		<tbody>
			<?
			$i=1;
			foreach($booking_fb_data_arr['DATA'][2][3] as $key=>$row){
				$row['AMOUNT']=$booking_fb_data_arr['AMOUNT'][$key];
				$row['WO_QNTY']=$booking_fb_data_arr['WO_QNTY'][$key];
				$row['REQ_WO_QNTY']=$booking_fb_data_arr['REQ_WO_QNTY'][$key];
				$row['DESCRIPTION']=$fabric_description_dtls_array[$row['PRE_COST_FABRIC_COST_DTLS_ID']];
				$new_supplier_arr = ($row['SOURCE'] == 2)?$supplier_arr:$company_library;
			?>
			<tr>
				<td><?=$i;?></td>	
				<td><?=$row['BOOKING_NO'];?></td>	
				<td><?=change_date_format($row['BOOKING_DATE']);?></td>
				<td><?=$pay_mode[$row['PAY_MODE']];?></td>	 
				<td><?=$source[$row['SOURCE']];?></td>	
				<td><?=$new_supplier_arr[$row['SUPPLIER_ID']];?></td>	
				<td><?=$row['DESCRIPTION'];?></td>	
				<td align="right"><?=number_format($row['REQ_WO_QNTY'],2);?></td>
				<td align="right"><?=number_format($row['WO_QNTY'],2);?></td>	
				<td align="right"><?=number_format($row['AMOUNT'],2);?></td>
			</tr>
			<?
			}
			?>
		</tbody>
	</table>

			
	<h3>Finish Fabric Booking-Foreign</h3>
	<table border="1" rules="all" class="rpt_table">
		<thead>
			<th>SL</th>	
			<th>Wo No</th>	
			<th>Wo Date	</th>
			<th>Pay Mode</th>	 
			<th>Source</th>	
			<th>Supplier</th>	
			<th width="200">Fabric Description</th>	
			<th>Require </th>	
			<th>Wo Qty</th>	
			<th>Wo value(USD)</th>
		</thead>
		<tbody>
			<?
			$i=1;
			foreach($booking_fb_data_arr['DATA'][2][1] as $key=>$row){
				$row['AMOUNT']=$booking_fb_data_arr['AMOUNT'][$key];
				$row['WO_QNTY']=$booking_fb_data_arr['WO_QNTY'][$key];
				$row['REQ_WO_QNTY']=$booking_fb_data_arr['REQ_WO_QNTY'][$key];
				$row['DESCRIPTION']=$fabric_description_dtls_array[$row['PRE_COST_FABRIC_COST_DTLS_ID']];
			?>
			<tr>
				<td><?=$i;?></td>	
				<td><?=$row['BOOKING_NO'];?></td>	
				<td><?=change_date_format($row['BOOKING_DATE']);?></td>
				<td><?=$pay_mode[$row['PAY_MODE']];?></td>	 
				<td><?=$source[$row['SOURCE']];?></td>	
				<td><?=$supplier_arr[$row['SUPPLIER_ID']];?></td>	
				<td><?=$row['DESCRIPTION'];?></td>	
				<td align="right"><?=number_format($row['REQ_WO_QNTY'],2);?></td>
				<td align="right"><?=number_format($row['WO_QNTY'],2);?></td>	
				<td align="right"><?=number_format($row['AMOUNT'],2);?></td>
			</tr>
			<?
			}
			?>
		</tbody>
	</table>

	<h3>Lab Approval</h3>
	<table border="1" rules="all" class="rpt_table">
		<thead>
			<th>SL</th>	
			<th>Gmts Color</th>	
			<th>Target app. date</th>	 
			<th>Send Date</th>	
			<th>Submission Date</th>	
			<th>Status</th>	
			<th>Approval/ Reject Date</th>	
		</thead>
		<tbody>
			<?
			$i=1;
			foreach($labdip_data_arr['DATA'] as $row){
			?>
			<tr>
				<td><?=$i;?></td>	
				<td><?=$color_arr[$row['COLOR_NAME_ID']];?></td>	
				<td align="center"><?=change_date_format($row['LAPDIP_TARGET_APPROVAL_DATE']);?></td>	 
				<td align="center"><?=change_date_format($row['SEND_TO_FACTORY_DATE']);?></td>
				<td align="center"><?=change_date_format($row['APPROVAL_STATUS_DATE']);?></td>
				<td><?=$approval_status[$row['APPROVAL_STATUS']];?></td>	
				<td align="center"><?=change_date_format($row['APPROVAL_STATUS_DATE']);?></td>
			</tr>
			<?
			}
			?>
		</tbody>
	</table>


	<h3>Finished Fabric Rcvd.-Local</h3>
	<table border="1" rules="all" class="rpt_table">
		<thead>
			<th>SL</th>	
			<th>Booking/PI no</th>	
			<th>MRR No</th>	 
			<th>MRR Date</th>	
			<th>Challan No</th>	
			<th>Supplier</th>	
			<th width="200">Fabric Description</th>	
			<th>Booking Qty</th>
			<th>Rcv Qty</th>
			<th>Receive value(USD)</th>
		</thead>
		<tbody>
			<?
			$i=1;
			foreach($finish_fab_rec_data_arr['DATA'][0] as $row){
			?>
			<tr>
				<td><?=$i;?></td>	
				<td><?=$row['BOOKING_NO'];?></td>	
				<td align="center"><?=$row['RECV_NUMBER'];?></td>	 
				<td align="center"><?=change_date_format($row['RECEIVE_DATE']);?></td>
				<td align="center"><?=$row['CHALLAN_NO'];?></td>
				<td><?=$supplier_arr[$row['SUPPLIER_ID']];?></td>	
				<td align="right"><?=$fabric_description_count_deter_array[$row['FABRIC_DESCRIPTION_ID']];?></td>
				<td align="right"><?=number_format($booking_fb_data_arr['BOOKING_QTY'][$fabric_description_count_deter_array[$row['FABRIC_DESCRIPTION_ID']]],2);?></td>
				<td align="right"><?=number_format($row['QUANTITY'],2);?></td>
				<td align="right"><?=number_format($row['VALUE'],2);?></td>
			</tr>
			<?
			}
			?>
		</tbody>
	</table>



	<h3>Finished Fabric Rcvd.-Foreign</h3>
	<table border="1" rules="all" class="rpt_table">
		<thead>
			<th>SL</th>	
			<th>Booking/PI no</th>	
			<th>MRR No</th>	 
			<th>MRR Date</th>	
			<th>Challan No</th>	
			<th>Supplier</th>	
			<th width="200">Fabric Description</th>	
			<th>Booking Qty</th>
			<th>Rcv Qty</th>
			<th>Receive value(USD)</th>
		</thead>
		<tbody>
			<?
			$i=1;
			foreach($finish_fab_rec_data_arr['DATA'][3] as $row){
			?>
			<tr>
				<td><?=$i;?></td>	
				<td><?=$row['BOOKING_NO'];?></td>	
				<td align="center"><?=$row['RECV_NUMBER'];?></td>	 
				<td align="center"><?=change_date_format($row['RECEIVE_DATE']);?></td>
				<td align="center"><?=$row['CHALLAN_NO'];?></td>
				<td><?=$supplier_arr[$row['SUPPLIER_ID']];?></td>	
				<td align="right"><?=$fabric_description_count_deter_array[$row['FABRIC_DESCRIPTION_ID']];?></td>
				<td align="right"><?=number_format($booking_fb_data_arr['BOOKING_QTY'][$fabric_description_count_deter_array[$row['FABRIC_DESCRIPTION_ID']]],2);?></td>
				<td align="right"><?=number_format($row['QUANTITY'],2);?></td>
				<td align="right"><?=number_format($row['VALUE'],2);?></td>
			</tr>
			<?
			}
			?>
		</tbody>
	</table>


	<h3>Sew Trims Booking-Local</h3>
	<table border="1" rules="all" class="rpt_table">
		<thead>
			<th>SL</th>	
			<th>Wo No</th>	
			<th>Wo Date	</th>
			<th>Pay Mode</th>	 
			<th>Source</th>	
			<th>Supplier</th>	
			<th>Item Group</th>	
			<th width="200">Item Description</th>	
			<th>Require </th>	
			<th>Wo Qty</th>	
			<th>Wo value(USD)</th>
		</thead>
		<tbody>
			<?
			$i=1;
			foreach($booking_data_arr['DATA'][4][1][3] as $key=>$row){
				$row['AMOUNT']=$booking_data_arr['AMOUNT'][$key];
				$row['WO_QNTY']=$booking_data_arr['WO_QNTY'][$key];
			?>
			<tr>
				<td><?=$i;?></td>	
				<td><?=$row['BOOKING_NO'];?></td>	
				<td><?=change_date_format($row['BOOKING_DATE']);?></td>
				<td><?=$pay_mode[$row['PAY_MODE']];?></td>	 
				<td><?=$source[$row['SOURCE']];?></td>	
				<td><?=$supplier_arr[$row['SUPPLIER_ID']];?></td>	
				<td><?=$trim_group_library[$row['TRIM_GROUP']];?></td>	
				<td><?=$row['DESCRIPTION'];?></td>	
				<td align="right"><?=number_format($trimsReqArr[$row['PRE_COST_FABRIC_COST_DTLS_ID']],2);?></td>	
				<td align="right"><?=number_format($row['WO_QNTY'],2);?></td>	
				<td align="right"><?=number_format($row['AMOUNT'],2);?></td>
			</tr>
			<?
			}
			?>
		</tbody>
	</table>

			
	<h3>Sew Trims Booking-Foreign</h3>
	<table border="1" rules="all" class="rpt_table">
		<thead>
			<th>SL</th>	
			<th>Wo No</th>	
			<th>Wo Date	</th>
			<th>Pay Mode</th>	 
			<th>Source</th>	
			<th>Supplier</th>	
			<th>Item Group</th>	
			<th width="200">Item Description</th>	
			<th>Require </th>	
			<th>Wo Qty</th>	
			<th>Wo value(USD)</th>
		</thead>
		<tbody>
			<?
			$i=1;
			foreach($booking_data_arr['DATA'][4][1][1] as $key=>$row){
				$row['AMOUNT']=$booking_data_arr['AMOUNT'][$key];
				$row['WO_QNTY']=$booking_data_arr['WO_QNTY'][$key];
			?>
			<tr>
				<td><?=$i;?></td>	
				<td><?=$row['BOOKING_NO'];?></td>	
				<td><?=change_date_format($row['BOOKING_DATE']);?></td>
				<td><?=$pay_mode[$row['PAY_MODE']];?></td>	 
				<td><?=$source[$row['SOURCE']];?></td>	
				<td><?=$supplier_arr[$row['SUPPLIER_ID']];?></td>	
				<td><?=$trim_group_library[$row['TRIM_GROUP']];?></td>	
				<td><?=$row['DESCRIPTION'];?></td>	
				<td align="right"><?=number_format($trimsReqArr[$row['PRE_COST_FABRIC_COST_DTLS_ID']],2);?></td>	
				<td align="right"><?=number_format($row['WO_QNTY'],2)?></td>	
				<td align="right"><?=number_format($row['AMOUNT'],2);?></td>
			</tr>
			<?
			}
			?>
		</tbody>
	</table>




	<h3>Sew Trims Rcvd.-Local</h3>
	<table border="1" rules="all" class="rpt_table">
		<thead>
			<th>SL</th>	
			<th>Booking/PI no</th>	
			<th>MRR No</th>	 
			<th>MRR Date</th>	
			<th>Challan No</th>	
			<th>Supplier</th>	
			<th>Item Group</th>	
			<th>Booking Qty</th>
			<th>Rcv Qty</th>
			<th>Receive value(USD)</th>
		</thead>
		<tbody>
			<?
			$i=1;
			foreach($finish_trims_rec_data_arr['DATA'][3][1] as $row){
			?>
			<tr>
				<td><?=$i;?></td>	
				<td><?=$row['BOOKING_NO'];?></td>	
				<td align="center"><?=$row['RECV_NUMBER'];?></td>	 
				<td align="center"><?=change_date_format($row['RECEIVE_DATE']);?></td>
				<td align="center"><?=$row['CHALLAN_NO'];?></td>
				<td><?=$supplier_arr[$row['SUPPLIER_ID']];?></td>	
				<td align="right"><?=$trim_group_library[$row['ITEM_GROUP_ID']];?></td>
				<td align="right"><?=number_format($booking_data_arr['BOOKING_QTY'][$row['BOOKING_NO']][$row['ITEM_GROUP_ID']][1][3],2);?></td>
				<td align="right"><?=number_format($row['QUANTITY'],2);?></td>
				<td align="right"><?=number_format($row['VALUE'],2);?></td>
			</tr>
			<?
			}
			?>
		</tbody>
	</table>


	<h3>Sew Trims Rcvd.-Foreign</h3>
	<table border="1" rules="all" class="rpt_table">
		<thead>
			<th>SL</th>	
			<th>Booking/PI no</th>	
			<th>MRR No</th>	 
			<th>MRR Date</th>	
			<th>Challan No</th>	
			<th>Supplier</th>	
			<th>Item Group</th>	
			<th>Booking Qty</th>
			<th>Rcv Qty</th>
			<th>Receive value(USD)</th>
		</thead>
		<tbody>
			<?
			$i=1;
			foreach($finish_trims_rec_data_arr['DATA'][1][1] as $row){
				//$BOOKING_NO=$booking_data_arr['BOOKING_NO'][$row['PO_BREAKDOWN_ID']][$row['ITEM_GROUP_ID']];
				$bookingQty=0;
				foreach($booking_data_arr['BOOKING_NO'][$row['PO_BREAKDOWN_ID']][$row['ITEM_GROUP_ID']] as $BOOKING_NO){
					$bookingQty+=$booking_data_arr['BOOKING_QTY'][$BOOKING_NO][$row['ITEM_GROUP_ID']][1][1];
				}


			?>
			<tr>
				<td><?=$i;?></td>	
				<td><?=$row['BOOKING_NO'] ;?></td>	
				<td align="center"><?=$row['RECV_NUMBER'];?></td>	 
				<td align="center"><?=change_date_format($row['RECEIVE_DATE']);?></td>
				<td align="center"><?=$row['CHALLAN_NO'];?></td>
				<td><?=$supplier_arr[$row['SUPPLIER_ID']];?></td>	
				<td align="right"><?=$trim_group_library[$row['ITEM_GROUP_ID']];?></td>
				<td align="right"><?=number_format($bookingQty,2);?></td>
				<td align="right"><?=number_format($row['QUANTITY'],2);?></td>
				<td align="right"><?=number_format($row['VALUE'],2);?></td>
			</tr>
			<?
			}
			?>
		</tbody>
	</table>



	<h3>Finish Trims Booking-Local</h3>
	<table border="1" rules="all" class="rpt_table">
		<thead>
			<th>SL</th>	
			<th>Wo No</th>	
			<th>Wo Date	</th>
			<th>Pay Mode</th>	 
			<th>Source</th>	
			<th>Supplier</th>	
			<th>Item Group</th>	
			<th width="200">Item Description</th>	
			<th>Require </th>	
			<th>Wo Qty</th>	
			<th>Wo value(USD)</th>
		</thead>
		<tbody>
			<?
			$i=1;
			foreach($booking_data_arr['DATA'][4][2][3] as $key=>$row){
				$row['AMOUNT']=$booking_data_arr['AMOUNT'][$key];
				$row['WO_QNTY']=$booking_data_arr['WO_QNTY'][$key];
			?>
			<tr>
				<td><?=$i;?></td>	
				<td><?=$row['BOOKING_NO'];?></td>	
				<td><?=change_date_format($row['BOOKING_DATE']);?></td>
				<td><?=$pay_mode[$row['PAY_MODE']];?></td>	 
				<td><?=$source[$row['SOURCE']];?></td>	
				<td><?=$supplier_arr[$row['SUPPLIER_ID']];?></td>	
				<td><?=$trim_group_library[$row['TRIM_GROUP']];?></td>	
				<td><?=$row['DESCRIPTION'];?></td>	
				<td align="right"><?=number_format($trimsReqArr[$row['PRE_COST_FABRIC_COST_DTLS_ID']],2);?></td>	
				<td align="right"><?=number_format($row['WO_QNTY'],2);?></td>	
				<td align="right"><?=number_format($row['AMOUNT'],2);?></td>
			</tr>
			<?
			}
			?>
		</tbody>
	</table>

			
	<h3>Finish Trims Booking-Foreign</h3>
	<table border="1" rules="all" class="rpt_table">
		<thead>
			<th>SL</th>	
			<th>Wo No</th>	
			<th>Wo Date	</th>
			<th>Pay Mode</th>	 
			<th>Source</th>	
			<th>Supplier</th>	
			<th>Item Group</th>	
			<th width="200">Item Description</th>	
			<th>Require </th>	
			<th>Wo Qty</th>	
			<th>Wo value(USD)</th>
		</thead>
		<tbody>
			<?
			$i=1;
			foreach($booking_data_arr['DATA'][4][2][1] as $key=>$row){
				$row['AMOUNT']=$booking_data_arr['AMOUNT'][$key];
				$row['WO_QNTY']=$booking_data_arr['WO_QNTY'][$key];
			?>
			<tr>
				<td><?=$i;?></td>	
				<td><?=$row['BOOKING_NO'];?></td>	
				<td><?=change_date_format($row['BOOKING_DATE']);?></td>
				<td><?=$pay_mode[$row['PAY_MODE']];?></td>	 
				<td><?=$source[$row['SOURCE']];?></td>	
				<td><?=$supplier_arr[$row['SUPPLIER_ID']];?></td>	
				<td><?=$trim_group_library[$row['TRIM_GROUP']];?></td>	
				<td><?=$row['DESCRIPTION'];?></td>	
				<td align="right"><?=number_format($trimsReqArr[$row['PRE_COST_FABRIC_COST_DTLS_ID']],2);?></td>	
				<td align="right"><?=number_format($row['WO_QNTY'],2);?></td>	
				<td align="right"><?=number_format($row['AMOUNT'],2);?></td>
			</tr>
			<?
			}
			?>
		</tbody>
	</table>


	<h3>Finishing Trims Rcvd.-Local</h3>
	<table border="1" rules="all" class="rpt_table">
		<thead>
			<th>SL</th>	
			<th>Booking/PI no</th>	
			<th>MRR No</th>	 
			<th>MRR Date</th>	
			<th>Challan No</th>	
			<th>Supplier</th>	
			<th>Item Group</th>	
			<th>Booking Qty</th>
			<th>Rcv Qty</th>
			<th>Receive value(USD)</th>
		</thead>
		<tbody>
			<?
			$i=1;
			foreach($finish_trims_rec_data_arr['DATA'][3][2] as $row){
			?>
			<tr>
				<td><?=$i;?></td>	
				<td><?=$row['BOOKING_NO'];?></td>	
				<td align="center"><?=$row['RECV_NUMBER'];?></td>	 
				<td align="center"><?=change_date_format($row['RECEIVE_DATE']);?></td>
				<td align="center"><?=$row['CHALLAN_NO'];?></td>
				<td><?=$supplier_arr[$row['SUPPLIER_ID']];?></td>	
				<td align="right"><?=$trim_group_library[$row['ITEM_GROUP_ID']];?></td>
				<td align="right"><?=number_format($booking_data_arr['BOOKING_QTY'][$row['BOOKING_NO']][$row['ITEM_GROUP_ID']][2][3],2);?></td>
				<td align="right"><?=number_format($row['QUANTITY'],2);?></td>
				<td align="right"><?=number_format($row['VALUE'],2);?></td>
			</tr>
			<?
			}
			?>
		</tbody>
	</table>


	<h3>Finishing Trims Rcvd.-Foreign</h3>
	<table border="1" rules="all" class="rpt_table">
		<thead>
			<th>SL</th>	
			<th>Booking/PI no</th>	
			<th>MRR No</th>	 
			<th>MRR Date</th>	
			<th>Challan No</th>	
			<th>Supplier</th>	
			<th>Item Group</th>	
			<th>Booking Qty</th>
			<th>Rcv Qty</th>
			<th>Receive value(USD)</th>
		</thead>
		<tbody>
			<?
			$i=1;
			foreach($finish_trims_rec_data_arr['DATA'][1][2] as $row){
				//$BOOKING_NO=$booking_data_arr['BOOKING_NO'][$row['PO_BREAKDOWN_ID']][$row['ITEM_GROUP_ID']];
				$bookingQty=0;
				foreach($booking_data_arr['BOOKING_NO'][$row['PO_BREAKDOWN_ID']][$row['ITEM_GROUP_ID']] as $BOOKING_NO){
					$bookingQty+=$booking_data_arr['BOOKING_QTY'][$BOOKING_NO][$row['ITEM_GROUP_ID']][2][1];
				}
			?>
			<tr>
				<td><?=$i;?></td>	
				<td><?=$row['BOOKING_NO'];?></td>	
				<td align="center"><?=$row['RECV_NUMBER'];?></td>	 
				<td align="center"><?=change_date_format($row['RECEIVE_DATE']);?></td>
				<td align="center"><?=$row['CHALLAN_NO'];?></td>
				<td><?=$supplier_arr[$row['SUPPLIER_ID']];?></td>	
				<td align="right"><?=$trim_group_library[$row['ITEM_GROUP_ID']];?></td>
				<td align="right"><?=number_format($bookingQty,2);?></td>
				<td align="right"><?=number_format($row['QUANTITY'],2);?></td>
				<td align="right"><?=number_format($row['VALUE'],2);?></td>
			</tr>
			<?
			}
			?>
		</tbody>
	</table>



	<h3>Trims Approval</h3>
	<table border="1" rules="all" class="rpt_table">
		<thead>
			<th>SL</th>	
			<th>Item Group</th>	
			<th>Item Description</th>	
			<th>Target app. date</th>	 
			<th>Send Date</th>	
			<th>Submission Date</th>	
			<th>Status</th>	
			<th>Approval/ Reject Date</th>	
		</thead>
		<tbody>
			<?
			$i=1;
			foreach($trims_data_arr['DATA'] as $row){
			?>
			<tr>
				<td><?=$i;?></td>	
				<td><?=$trim_group_library[$row['ACCESSORIES_TYPE_ID']];?></td>	
				<td><?=$booking_data_arr['TRIMS_ITEM'][$row['PO_BREAK_DOWN_ID']][$row['ACCESSORIES_TYPE_ID']];?></td>	
				<td align="center"><?=change_date_format($row['TARGET_APPROVAL_DATE']);?></td>	 
				<td align="center"><?=change_date_format($row['SENT_TO_SUPPLIER']);?></td>
				<td align="center"><?=change_date_format($row['SUBMITTED_TO_BUYER']);?></td>
				<td><?=$approval_status[$row['APPROVAL_STATUS']];?></td>	
				<td align="center"><?=change_date_format($row['APPROVAL_STATUS_DATE']);?></td>
			</tr>
			<?
			}
			?>
		</tbody>
	</table>

 

	 



	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}



?>

