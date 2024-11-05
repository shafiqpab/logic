<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//--------------------------------------------------------------------------------------------------------------------
$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$color_name_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
$team_name_library=return_library_array( "select id,team_name from lib_marketing_team", "id", "team_name"  );
$team_member_name_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );   	 
	exit();
}
if ($action=="load_drop_down_team_member")
{
if($data!=0)
	{
        echo create_drop_down( "cbo_team_member", 150, "select id,team_member_name 	 from lib_mkt_team_member_info  where team_id='$data' and status_active=1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Select Team Member-", $selected, "" ); 
	}
 else
   {
		 echo create_drop_down( "cbo_team_member", 150, $blank_array,"", 1, "-Select Team Member- ", $selected, "" );
   }
}
$tmplte=explode("**",$data);
if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;


if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name=str_replace("'","",$cbo_company_name);
	//$serch_by=str_replace("'","",$cbo_search_by);
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	
	
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no=trim($txt_job_no);
	if($txt_job_no !="" || $txt_job_no !=0)
	{
		$year = substr(str_replace("'","",$cbo_year_selection), -2); 
		$job_no=$company_library[$company_name]."-".$year."-".str_pad($txt_job_no, 5, 0, STR_PAD_LEFT);
		$jobcond="and a.job_no='".$job_no."'";
	}
	else
	{
		$jobcond="";	
	}
	
	
	$date_cond='';
	if(str_replace("'","",$cbo_category_by)==1)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			$date_cond="and c.country_ship_date between '$start_date' and '$end_date'";
		}
	}
	if(str_replace("'","",$cbo_category_by)==2)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			$date_cond="and b.po_received_date between '$start_date' and '$end_date'";
		}
	}
	if(str_replace("'","",$cbo_category_by)==3)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			$date_cond="and a.job_no=d.job_no and b.id=d.po_number_id and c.po_break_down_id=d.po_number_id and d.task_number =9 and d.task_start_date between '$start_date' and '$end_date'";
		}
	}
	if(str_replace("'","",$cbo_category_by)==4)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			$date_cond="and a.job_no=d.job_no and b.id=d.po_number_id and c.po_break_down_id=d.po_number_id and d.task_number =10 and d.task_finish_date between '$start_date' and '$end_date'";
		}
	}
	if(str_replace("'","",$cbo_category_by)==5)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			$date_cond="and a.job_no=d.job_no and b.id=d.po_number_id and c.po_break_down_id=d.po_number_id and d.task_number =8 and d.task_finish_date between '$start_date' and '$end_date'";
		}
	}
	if(str_replace("'","",$cbo_category_by)==6)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			$date_cond="and a.job_no=d.job_no and b.id=d.po_number_id and c.po_break_down_id=d.po_number_id and d.task_number =12 and d.task_finish_date between '$start_date' and '$end_date'";
		}
	}
	if(str_replace("'","",$cbo_category_by)==7)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			$date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
	}
	//if (str_replace("'","",$txt_job_no)=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in (".str_replace("'","",$txt_job_no).") ";
	
	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";
	if(str_replace("'","",$txt_order_no)!="")
	{
		$ordercond=" and b.po_number like '%".str_replace("'","",$txt_order_no)."%'"; 
	}
	else
	{
		$ordercond="";
	}
	
	$team_cond="";
	if(str_replace("'","",$cbo_team_name)!=0) $team_cond=" and a.team_leader=".str_replace("'","",$cbo_team_name)."  ";
	if(str_replace("'","",$cbo_team_member)!=0) $team_cond.=" and a.dealing_marchant=".str_replace("'","",$cbo_team_member)."  ";
	
		
	 /*$sql="select a.po_number_id, a.job_no,a.task_number, max(a.task_finish_date) as task_finish_date
	from tna_process_mst a, wo_po_break_down b 
	where a.po_number_id=b.id and b.shiping_status !=3 and b.status_active=1 and b.po_quantity>0 and b.is_confirmed='1' and a.task_number in(9,10) $ordercond $jobcond
	group by a.po_number_id,a.job_no,a.task_number order by a.task_number";*/
	
	
	 $sql="select a.job_no,a.task_number,
min(CASE WHEN a.task_number = 9 THEN  a.task_finish_date  END ) as lab_sub ,
max(CASE WHEN a.task_number = 10 THEN a.task_finish_date  END ) as lab_apvl
from tna_process_mst a, wo_po_break_down b 
where a.po_number_id=b.id and b.shiping_status !=3 and b.status_active=1 and b.po_quantity>0 and b.is_confirmed='1' and a.task_number in(9,10) $ordercond $jobcond   
group by a.job_no,a.task_number order by a.job_no";
	//echo $sql;
	
	$lab_sql_data=sql_select($sql);
	foreach($lab_sql_data as $row){
		if($row[csf('task_number')]==9){$lab_data[$row[csf('job_no')]][$row[csf('task_number')]]=$row[csf('lab_sub')];}
		else{$lab_data[$row[csf('job_no')]][$row[csf('task_number')]]=$row[csf('lab_apvl')];}	
	}


	
	if($template==1)
	{
		ob_start();
	?>
		<div style="width:3450px">
		<fieldset style="width:100%;">	
			<table width="3450">
				<tr class="form_caption">
					<td colspan="26" align="center">PP approval Status</td>
				</tr>
				<tr class="form_caption">
					<td colspan="26" align="center"><? echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
			<table class="rpt_table" width="3390" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="30">SL</th>
					<th width="50">Buyer</th>
					<th width="100">Job No</th>
					<th width="100">Style Ref</th>
                    <th width="100">Style Qty</th>
                    <th width="100">Team</th>
                    <th width="100">Team Member</th>
                    <th width="200">Item</th>
					<th width="90">Order No</th>
                    <th width="80">Shipment Date</th>
					<th width="80">Color Qnty (Pcs)</th>
					<th width="80"><!--Yarn rerqd (Kg)  -->Sample Booking Qty (Kg)</th>
					<th width="250">Fabrication</th>
					<th width="100">Gmts. Color</th>
					<th width="100">PO Rec. Date</th>
					<th width="60">Pantone/ Swatch Send Date TO Fab.dept</th>
					<th width="80">Pantone/ Swatch Receive Date from Fab.dept</th>
                    <th width="100">Days Taken For Lab Dip</th>
					<th width="100">Sample Fabric Booking Date</th>
					<th width="100">Lab Dip Buyer Submission Date</th>


					<th width="100">Lab submission to Buyer  End  Date ( TNA)</th>
                    
					<th width="90">Lab Dip Appv. Date</th>
                    
					<th width="100">Lab Approval  End  Date ( TNA)</th>
                    
                    
                    <th width="60">Days Taken For Lab Dip Appv.</th>
                    <th width="100">Finish Fab In-house</th>
                    <th width="150">Days Taken For Finish Fab In-house</th>
                    <th width="70">PP End Submit Date</th>
                    <th width="70">PP Submit Date</th>
					<th width="90">PP Appv. Date</th>
					<th width="90">Days Taken For PP Appv.</th>
					<th width="90">Days Taken PO Rev Date Vs PP Appv. Date</th>
                    <th width="70">TNA End Date for PP</th>
                    <th width="70">Deviation</th>
                    <th width="60">Already has gone in hand days</th>
                    <th width="60">Days in Hand</th>
					<th>Remarks /bulk in inhse date</th>
				</thead>
			</table>
			<div style="width:3410px; max-height:400px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="3390" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
	<?
	$job_array=array();
	$po_wise_data=array();
	$po_id_arr=array();
	$report_data_arr=array();
	if(str_replace("'","",$cbo_category_by)==1 || str_replace("'","",$cbo_category_by)==2 || str_replace("'","",$cbo_category_by)==7){
	$sql_data=sql_select("select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.team_leader,a.dealing_marchant, a.gmts_item_id,a.job_quantity, b.id,b.po_number, b.po_received_date, b.po_quantity,b.plan_cut, b.shipment_date, c.item_number_id,c.country_ship_date,c.color_number_id,c.order_quantity,c.plan_cut_qnty,c.shiping_status  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.is_confirmed=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond");
	}
	else{
	$sql_data=sql_select("select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.team_leader,a.dealing_marchant, a.gmts_item_id,a.job_quantity, b.id,b.po_number, b.po_received_date, b.po_quantity,b.plan_cut, b.shipment_date, c.item_number_id,c.country_ship_date,c.color_number_id,c.order_quantity,c.plan_cut_qnty,c.shiping_status  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,  tna_process_mst d where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.is_confirmed=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond");
	}
	
	
	
	
	
	foreach( $sql_data as $row_data)
	{
	$job_array[$row_data[csf('id')]]=$row_data[csf('job_no')];
	$po_id_arr[$row_data[csf('id')]]=$row_data[csf('id')];
	$po_wise_data[$row_data[csf('job_no')]][job_no]=$row_data[csf('job_no')];
	$po_wise_data[$row_data[csf('job_no')]][buyer_name]=$row_data[csf('buyer_name')];
	$po_wise_data[$row_data[csf('job_no')]][style_ref_no]=$row_data[csf('style_ref_no')];
	$po_wise_data[$row_data[csf('job_no')]][team_leader]=$row_data[csf('team_leader')];
	$po_wise_data[$row_data[csf('job_no')]][dealing_marchant]=$row_data[csf('dealing_marchant')];
	$po_wise_data[$row_data[csf('job_no')]][job_no_qty]=$row_data[csf('job_quantity')];
	
	$po_wise_data[$row_data[csf('job_no')]][gmts_item_id][$row_data[csf('item_number_id')]]=$garments_item[$row_data[csf('item_number_id')]];
	$po_wise_data[$row_data[csf('job_no')]][po_id][$row_data[csf('id')]]=$row_data[csf('id')];
	$po_wise_data[$row_data[csf('job_no')]][po_number][$row_data[csf('id')]]=$row_data[csf('po_number')];
	$po_wise_data[$row_data[csf('job_no')]][po_received_date][$row_data[csf('id')]]=$row_data[csf('po_received_date')];
	$po_wise_data[$row_data[csf('job_no')]][country_ship_date][$row_data[csf('id')]]=$row_data[csf('country_ship_date')];
	
	$po_wise_data[$row_data[csf('job_no')]][shiping_status][$row_data[csf('id')]]=$row_data[csf('shiping_status')];

	$report_data_arr[$row_data[csf('job_no')]][$row_data[csf('color_number_id')]][color_number_id]=$row_data[csf('color_number_id')];
	$report_data_arr[$row_data[csf('job_no')]][$row_data[csf('color_number_id')]][color_from]="Color Size Break Down";
	$report_data_arr[$row_data[csf('job_no')]][$row_data[csf('color_number_id')]][order_quantity]+=$row_data[csf('order_quantity')];
	$report_data_arr[$row_data[csf('job_no')]][$row_data[csf('color_number_id')]][plan_cut_qnty]+=$row_data[csf('plan_cut_qnty')];
	}
	if(count($po_id_arr)>0)
	{
	   $po_id=array_chunk($po_id_arr,1000, true);
	   $po_cond_in="";
	   $po_cond_in1="";
	   $po_cond_in2="";
	   $po_cond_in3="";
	   $po_cond_in4="";
	   $ji=0;
	   foreach($po_id as $key=> $value)
	   {
		   if($ji==0)
		   {
				$po_cond_in="and c.po_break_down_id in(".implode(",",$value).")"; 
				$po_cond_in1="and po_break_down_id in(".implode(",",$value).")";
				$po_cond_in2="and c.po_breakdown_id in(".implode(",",$value).")"; 
				$po_cond_in3="and b.po_break_down_id in(".implode(",",$value).")"; 
				$po_cond_in4="and po_number_id in(".implode(",",$value).")"; 
				
		   }
		   else
		   {
				$po_cond_in.=" or c.po_break_down_id in(".implode(",",$value).")";
				$po_cond_in1.=" or po_break_down_id in(".implode(",",$value).")";
				$po_cond_in2.=" or c.po_breakdown_id in(".implode(",",$value).")";
				$po_cond_in3.=" or b.po_breakdown_id in(".implode(",",$value).")";
				$po_cond_in4.=" or po_number_id in(".implode(",",$value).")";
		   }
		   $ji++;
	   }
	  
	  $booking_id_arr=array();
	  $sql_booking=sql_select("select a.job_no,a.fabric_description,a.gsm_weight,b.id as booking_id,b.booking_date,c.po_break_down_id,c.pre_cost_fabric_cost_dtls_id,c.fabric_color_id,c.grey_fab_qnty from wo_pre_cost_fabric_cost_dtls a,wo_booking_mst b, wo_booking_dtls c where a.job_no=b.job_no and a.job_no=c.job_no and a.id=c.pre_cost_fabric_cost_dtls_id and b.booking_no=c.booking_no and b.booking_type=4 and b.item_category=2 and b.fabric_source in(1,2,3)  $po_cond_in and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");//and a.body_part_id in(1,14,15,16,17,20) 
	  foreach($sql_booking as $row_booking)
	  {
		  		$booking_id_arr[$row_booking[csf('booking_id')]]=$row_booking[csf('booking_id')];
				$report_data_arr[$row_booking[csf('job_no')]][$row_booking[csf('fabric_color_id')]][color_number_id]=$row_booking[csf('fabric_color_id')];
				$report_data_arr[$row_booking[csf('job_no')]][$row_booking[csf('fabric_color_id')]][color_from]="Fabric Booking";
				
				$report_data_arr[$row_booking[csf('job_no')]][$row_booking[csf('fabric_color_id')]][fabric_description][$row_booking[csf('pre_cost_fabric_cost_dtls_id')]]=$row_booking[csf('fabric_description')].",".$row_booking[csf('gsm_weight')];
				$report_data_arr[$row_booking[csf('job_no')]][$row_booking[csf('fabric_color_id')]][grey_fab_qnty]+=$row_booking[csf('grey_fab_qnty')];
				$report_data_arr[$row_booking[csf('job_no')]][$row_booking[csf('fabric_color_id')]][booking_date][$row_booking[csf('booking_id')]]=$row_booking[csf('booking_date')];
				$report_data_arr[$row_booking[csf('job_no')]][$row_booking[csf('fabric_color_id')]][booking_id][$row_booking[csf('booking_id')]]=$row_booking[csf('booking_id')];

	  }
	  
	if(count($booking_id_arr)>0)
	{
	   $booking_id=array_chunk($booking_id_arr,1000, true);
	   $booking_cond_in="";
	  
	   $bi=0;
	   foreach($booking_id as $keyb=> $valueb)
	   {
		   if($bi==0)
		   {
				$booking_cond_in=" and a.booking_id in(".implode(",",$valueb).")"; 
				
		   }
		   else
		   {
				$booking_cond_in.=" or a.booking_id in(".implode(",",$valueb).")";
				
		   }
		   $bi++;
	   }
	}
	   
	   $sql_lab_dip=sql_select("select job_no_mst,color_name_id,min(lapdip_target_approval_date) as lapdip_target_approval_date, min(send_to_factory_date) as send_to_factory_date, max(recv_from_factory_date) as recv_from_factory_date, min(submitted_to_buyer) as submitted_to_buyer, max(approval_status_date) as  approval_status_date, max(approval_status) as approval_status from wo_po_lapdip_approval_info where  is_deleted=0 and status_active=1  $po_cond_in1  group by job_no_mst,color_name_id");
	   foreach($sql_lab_dip as $row_lab_dip)
	   {
		   		$report_data_arr[$row_lab_dip[csf('job_no_mst')]][$row_lab_dip[csf('color_name_id')]][color_number_id]=$row_lab_dip[csf('color_name_id')];
				$report_data_arr[$row_lab_dip[csf('job_no_mst')]][$row_lab_dip[csf('color_name_id')]][color_from]="Labdip";
				$report_data_arr[$row_lab_dip[csf('job_no_mst')]][$row_lab_dip[csf('color_name_id')]][send_to_factory_date]=$row_lab_dip[csf('send_to_factory_date')];
				$report_data_arr[$row_lab_dip[csf('job_no_mst')]][$row_lab_dip[csf('color_name_id')]][recv_from_factory_date]=$row_lab_dip[csf('recv_from_factory_date')];
				$report_data_arr[$row_lab_dip[csf('job_no_mst')]][$row_lab_dip[csf('color_name_id')]][submitted_to_buyer]=$row_lab_dip[csf('submitted_to_buyer')];
				$report_data_arr[$row_lab_dip[csf('job_no_mst')]][$row_lab_dip[csf('color_name_id')]][approval_status_date]=$row_lab_dip[csf('approval_status_date')];
				//$report_data_arr[$row_lab_dip[csf('job_no_mst')]][$row_lab_dip[csf('color_name_id')]][approval_status]=$row_lab_dip[csf('approval_status')];
	   }
	   //$lab_dip_app_status_arr=array();
	    $sql_lab_dip_app_status=sql_select("select job_no_mst,color_name_id, approval_status as approval_status from wo_po_lapdip_approval_info where   approval_status=3 $po_cond_in1 and is_deleted=0 and status_active=1 group by job_no_mst,color_name_id,approval_status");//is_master_color=1 and
	   foreach($sql_lab_dip_app_status as $rowl_lab_dip_app_status)
	   {
		   		
				$report_data_arr[$rowl_lab_dip_app_status[csf('job_no_mst')]][$rowl_lab_dip_app_status[csf('color_name_id')]][approval_status]=$rowl_lab_dip_app_status[csf('approval_status')];
	   }
	   
	   $sql_fin_fab_receive_qty=sql_select("select  c.po_breakdown_id,c.color_id , min(a.receive_date) as receive_date, SUM(c.quantity) as quantity, sum(c.reject_qty) as reject_qty
					from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c 
					where a.id=b.mst_id  and a.entry_form=37 and  a.item_category=2  and b.id=c.dtls_id and b.trans_id=c.trans_id and c.trans_type=1 $po_cond_in2 $booking_cond_in   and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by  c.po_breakdown_id,c.color_id");
	   foreach($sql_fin_fab_receive_qty as $row_fin_fab_receive_qty)
	   {
		$report_data_arr[$job_array[$row_fin_fab_receive_qty[csf('po_breakdown_id')]]][$row_fin_fab_receive_qty[csf('color_id')]][color_number_id]=$row_fin_fab_receive_qty[csf('color_id')];
		$report_data_arr[$job_array[$row_fin_fab_receive_qty[csf('po_breakdown_id')]]][$row_fin_fab_receive_qty[csf('color_id')]][color_from]="Finish Fabric";
		$report_data_arr[$job_array[$row_fin_fab_receive_qty[csf('po_breakdown_id')]]][$row_fin_fab_receive_qty[csf('color_id')]][receive_date]=$row_fin_fab_receive_qty[csf('receive_date')];
		//$report_data_arr[$row_fin_fab_receive_qty[csf('po_breakdown_id')]][$row_fin_fab_receive_qty[csf('color_id')]][quantity]=$row_fin_fab_receive_qty[csf('quantity')];
	   }
	  // echo "select a.job_no_mst,a.color_number_id, min(b.target_approval_date) as target_approval_date, min(b.send_to_factory_date) as send_to_factory_date,  min(b.submitted_to_buyer) as submitted_to_buyer, max(b.approval_status_date) as  approval_status_date from wo_po_color_size_breakdown a, wo_po_sample_approval_info b, lib_sample c where a.po_break_down_id=b.po_break_down_id and b.sample_type_id=c.id and a.id=b.color_number_id $po_cond_in3 and c.sample_type=2  and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 group by a.job_no_mst,a.color_number_id";
	   $sql_sample=sql_select("select a.job_no_mst,a.color_number_id, min(b.target_approval_date) as target_approval_date, min(b.send_to_factory_date) as send_to_factory_date,  min(b.submitted_to_buyer) as submitted_to_buyer, max(b.approval_status_date) as  approval_status_date from wo_po_color_size_breakdown a, wo_po_sample_approval_info b, lib_sample c where a.po_break_down_id=b.po_break_down_id and b.sample_type_id=c.id and a.id=b.color_number_id $po_cond_in3 and c.sample_type=2  and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and (b.entry_form_id is null or b.entry_form_id=0) group by a.job_no_mst,a.color_number_id");
	   foreach($sql_sample as $row_sample)
	   {
			$report_data_arr[$row_sample[csf('job_no_mst')]][$row_sample[csf('color_number_id')]][color_number_id]=$row_sample[csf('color_number_id')];
			$report_data_arr[$row_sample[csf('job_no_mst')]][$row_sample[csf('color_number_id')]][color_from]="Sample Approval";
			$report_data_arr[$row_sample[csf('job_no_mst')]][$row_sample[csf('color_number_id')]][pp_send_to_factory_date]=$row_sample[csf('send_to_factory_date')];
			$report_data_arr[$row_sample[csf('job_no_mst')]][$row_sample[csf('color_number_id')]][pp_submitted_to_buyer]=$row_sample[csf('submitted_to_buyer')];
			$report_data_arr[$row_sample[csf('job_no_mst')]][$row_sample[csf('color_number_id')]][pp_approval_status_date]=$row_sample[csf('approval_status_date')];
	   }
	   
		$sql_sample_app_status=sql_select("select a.job_no_mst,a.color_number_id, b.approval_status from wo_po_color_size_breakdown a, wo_po_sample_approval_info b, lib_sample c where a.po_break_down_id=b.po_break_down_id and b.sample_type_id=c.id and a.id=b.color_number_id $po_cond_in3 and c.sample_type=2 and b.approval_status=3  and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 group by a.job_no_mst,a.color_number_id,b.approval_status");
		foreach($sql_sample_app_status as $row_sample_app_status)
		{
			$report_data_arr[$row_sample_app_status[csf('job_no_mst')]][$row_sample_app_status[csf('color_number_id')]][pp_approval_status]=$row_sample_app_status[csf('approval_status')];
		}
		$tna_date_arr=array();
		$sql_tan=sql_select("select job_no,min(task_finish_date) as task_finish_date from tna_process_mst where  is_deleted=0 and status_active=1 and task_number=12 $po_cond_in4 group by job_no");
		foreach($sql_tan as $sql_row)
		{
			$tna_date_arr[$sql_row[csf('job_no')]]=$sql_row[csf('task_finish_date')];
		}
		
		$ppendsubmit_arr=array();
		$sql_ppendsubmit=sql_select("select job_no,min(task_finish_date) as task_finish_date from tna_process_mst where  is_deleted=0 and status_active=1 and task_number=8 $po_cond_in4 group by job_no");
		foreach($sql_ppendsubmit as $ppendsubmit_row)
		{
			$ppendsubmit_arr[$ppendsubmit_row[csf('job_no')]]=$ppendsubmit_row[csf('task_finish_date')];
		}
		
		$exfactory_data_array=array();
		$exfactory_data=sql_select("select c.po_break_down_id, MAX(c.ex_factory_date) as ex_factory_date from pro_ex_factory_mst c  where c.status_active=1 and c.is_deleted=0 $po_cond_in group by c.po_break_down_id ");
		foreach($exfactory_data as $exfatory_row)
		{
			$exfactory_data_array[$job_array[$exfatory_row[csf('po_breakdown_id')]]][ex_factory_date]=$exfatory_row[csf('ex_factory_date')];
		}
	}// end if(count($po_id_arr)>0)
    
		$style_qty_Print_array=array();

				$i=1;
				$tot_job_qnty=0;
				$tot_order_quantity=0;
				$tot_grey_fab_qnty=0;
				foreach($report_data_arr as $color_number_id=>$color_number_value)
				{
				foreach($report_data_arr[$color_number_id] as $key=>$value)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";     
				


				?>
                <tr align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
					<td width="30"><? echo $i; ?></td>
					<td width="50" style="word-wrap:break-word; word-break: break-all;"><? echo $buyer_short_name_library[$po_wise_data[$color_number_id][buyer_name]]; ?></td>
					<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $po_wise_data[$color_number_id][job_no]; ?></td>
					<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $po_wise_data[$color_number_id][style_ref_no]; ?></td>
                    <td width="100" style="word-wrap:break-word; word-break: break-all;">
					<? 
					if(!in_array($po_wise_data[$color_number_id][job_no],$style_qty_Print_array))
					{
					echo $po_wise_data[$color_number_id][job_no_qty]; 
					$tot_job_qnty+=$po_wise_data[$color_number_id][job_no_qty]; 
					$style_qty_Print_array[]=$po_wise_data[$color_number_id][job_no];
					}
					?>
                    </td>
                    
                    <td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $team_name_library[$po_wise_data[$color_number_id][team_leader]]; ?></td>
                    <td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $team_member_name_library[$po_wise_data[$color_number_id][dealing_marchant]]; ?></td>
                    
                    
                    <td width="200" style="word-wrap:break-word; word-break: break-all;"><? echo implode(",",$po_wise_data[$color_number_id][gmts_item_id]); ?></td>
					<td width="90" style="word-wrap:break-word; word-break: break-all;"><? echo implode(",",$po_wise_data[$color_number_id][po_number]); ?></td>
                    <td width="80" style="word-wrap:break-word; word-break: break-all;">
					<?
					$date=date('d-m-Y');
					$country_ship_date= min($po_wise_data[$color_number_id][country_ship_date]);
					$date_diff_3=datediff( "d", $exfactory_data_array[$po_wise_data[$color_number_id][job_no]][ex_factory_date] , $country_ship_date);
					$date_diff_1=datediff("d",$date,$country_ship_date);

					if($country_ship_date !="" && $country_ship_date !="0000-00-00" && $country_ship_date !="0")
					{
					echo change_date_format($country_ship_date,'dd-mm-yyyy','-'); 
					}
					?>
                    </td>
					<td width="80" style="word-wrap:break-word; word-break: break-all; text-align:right"><? echo $value[order_quantity]; $tot_order_quantity+=$value[order_quantity];?></td>
					<td width="80" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? 
					if($value[grey_fab_qnty]>0)
					{
						echo number_format($value[grey_fab_qnty],2);
						$tot_grey_fab_qnty+=$value[grey_fab_qnty];
					} 
					?>
                    </td>
					<td width="250" style="word-wrap:break-word; word-break: break-all;"><? echo implode(",",$value[fabric_description]); ?></td>
					<td width="100" style="word-wrap:break-word; word-break: break-all;" title="<? echo $value[color_from]; ?>"><? echo $color_name_library[$value[color_number_id]]; ?></td>
					<td width="100" style="word-wrap:break-word; word-break: break-all;">
					<?
					$po_received_date= min($po_wise_data[$color_number_id][po_received_date]);
					if($po_received_date !="" && $po_received_date !="0000-00-00" && $po_received_date !="0")
					{
					echo change_date_format($po_received_date,'dd-mm-yyyy','-'); 
					}
					?>
                    </td>
					<td width="60" style="word-wrap:break-word; word-break: break-all;">
					<? 
					if($value[send_to_factory_date] !="" && $value[send_to_factory_date] !="0000-00-00" && $value[send_to_factory_date] !="0")
					{
						echo change_date_format($value[send_to_factory_date],'dd-mm-yyyy','-');
					} 
					?>
                    </td>
					<td width="80" style="word-wrap:break-word; word-break: break-all;">
					<? 
					if($value[recv_from_factory_date] !="" && $value[recv_from_factory_date] !="0000-00-00" && $value[recv_from_factory_date] !="0")
					{
					echo change_date_format($value[recv_from_factory_date],'dd-mm-yyyy','-'); 
					}
					?>
                    </td>
                    <td width="100" style="word-wrap:break-word; word-break: break-all;">
					<? 
					if($value[recv_from_factory_date] !="" && $value[recv_from_factory_date] !="0000-00-00" && $value[recv_from_factory_date] !="0")
					{
					echo datediff("d",$value[send_to_factory_date] , $value[recv_from_factory_date]);
					}
					?>
                    </td>
					<td width="100" style="word-wrap:break-word; word-break: break-all;">
                    <a href="##" onclick="booking_date_popup('<? echo implode(",",$po_wise_data[$color_number_id][po_id]);?>',<? echo $value[color_number_id]?>)">
					<? 
					
                    $booking_id_for_date = min($value[booking_id]);
					if($value[booking_date][$booking_id_for_date] !="" && $value[booking_date][$booking_id_for_date] !="0000-00-00" && $value[booking_date][$booking_id_for_date] !="0")
					{
					echo change_date_format($value[booking_date][$booking_id_for_date],'dd-mm-yyyy','-'); 
					}
					
					/*if(min($value[booking_date]) !="" && min($value[booking_date]) !="0000-00-00" && min($value[booking_date]) !="0")
					{
					echo change_date_format(min($value[booking_date]),'dd-mm-yyyy','-'); 
					}*/
					?>
                    </a>
                    </td>
					<td width="100" style="word-wrap:break-word; word-break: break-all;">
					<? 
					if($value[submitted_to_buyer] !="" && $value[submitted_to_buyer] !="0000-00-00" && $value[submitted_to_buyer] !="0")
					{
					echo change_date_format($value[submitted_to_buyer],'dd-mm-yyyy','-'); 
					}
					?>
                    </td>
                    
                    <td width="100"><? echo change_date_format($lab_data[$po_wise_data[$color_number_id][job_no]][9]);?></td>
                    
                    
					<td width="90" style="word-wrap:break-word; word-break: break-all;" onclick="labdip_popup('<? echo implode(",",$po_wise_data[$color_number_id][po_id]);?>',<? echo $value[color_number_id]?>,'<? echo $value[send_to_factory_date];?>','<? echo $value[recv_from_factory_date];?>','<? echo $value[submitted_to_buyer];?>','<? echo $value[approval_status_date];?>','<? echo $value[pp_submitted_to_buyer];?>','<? echo $value[pp_approval_status_date];?>')">
                    <a href="##">
                    
					<?
					if($value[approval_status_date] !="" && $value[approval_status_date] !="0000-00-00" && $value[approval_status_date] !="0" && $value[approval_status]==3)
					{
					   echo change_date_format($value[approval_status_date],'dd-mm-yyyy','-'); 
					}
					?>
                    </a>
                    </td>
                    
                    <td width="100"><? echo change_date_format($lab_data[$po_wise_data[$color_number_id][job_no]][10]);?></td>
                    
                    <td width="60" style="word-wrap:break-word; word-break: break-all;">
					<? 
					if($value[approval_status_date] !="" && $value[approval_status_date] !="0000-00-00" && $value[approval_status_date] !="0" && $value[approval_status]==3)
					{
					echo datediff( "d", $value[submitted_to_buyer] , $value[approval_status_date]);
					}
					?>
                    </td>
                    
                    <td width="100" style="word-wrap:break-word; word-break: break-all;">
                     <a href="##" onclick="fin_receive_date_popup('<? echo implode(",",$po_wise_data[$color_number_id][po_id]);?>',<? echo $value[color_number_id]?>,'<? echo $booking_cond_in ?>')">
					<? 
					if($value[receive_date] !="" && $value[receive_date] !="0000-00-00" && $value[receive_date] !="0")
					{
					echo change_date_format($value[receive_date],'dd-mm-yyyy','-');
					}
					
					?>
                    </a>
                    </td>
                    
                    
                    <td width="150" style="word-wrap:break-word; word-break: break-all;">
					<? 
					if($value[receive_date] !="" && $value[receive_date] !="0000-00-00" && $value[receive_date] !="0" && $value[booking_date] !="" && $value[booking_date] !="0000-00-00" && min($value[booking_date]) !="0")
					{
					echo datediff( "d", min($value[booking_date]) , $value[receive_date]);
					}
					?>
                    </td>
                     <td width="70" style="word-wrap:break-word; word-break: break-all;">
                    
					<? 
					if($ppendsubmit_arr[$po_wise_data[$color_number_id][job_no]] !="" && $ppendsubmit_arr[$po_wise_data[$color_number_id][job_no]] !="0000-00-00" && $ppendsubmit_arr[$po_wise_data[$color_number_id][job_no]] !="0")
					{
					echo change_date_format($ppendsubmit_arr[$po_wise_data[$color_number_id][job_no]],'dd-mm-yyyy','-');
					}
					?>
                    </td>
                    <td width="70" style="word-wrap:break-word; word-break: break-all;">
					<? 
					if($value[pp_submitted_to_buyer] !="" && $value[pp_submitted_to_buyer] !="0000-00-00" && $value[pp_submitted_to_buyer] !="0")
					{
					echo change_date_format($value[pp_submitted_to_buyer],'dd-mm-yyyy','-');
					}
					?>
                    </td>
					<td width="90" style="word-wrap:break-word; word-break: break-all;" onclick="ppsample_popup('<? echo implode(",",$po_wise_data[$color_number_id][po_id]);?>',<? echo $value[color_number_id]?>,'<? echo $value[send_to_factory_date];?>','<? echo $value[recv_from_factory_date];?>','<? echo $value[submitted_to_buyer];?>','<? echo $value[approval_status_date];?>','<? echo $value[pp_submitted_to_buyer];?>','<? echo $value[pp_approval_status_date];?>')">
                    <a href="##">
					<? 
					if($value[pp_approval_status_date] !="" && $value[pp_approval_status_date] !="0000-00-00" && $value[pp_approval_status_date] !="0" && $value[pp_approval_status] ==3) 
					{
					echo change_date_format($value[pp_approval_status_date],'dd-mm-yyyy','-'); 
					}
					?>
                    </a>
                    </td>
					<td width="90" style="word-wrap:break-word; word-break: break-all;">
					<? 
					if($value[pp_submitted_to_buyer] !="" && $value[pp_submitted_to_buyer] !="0000-00-00" && $value[pp_submitted_to_buyer] !="0" && $value[pp_approval_status_date] !="" && $value[pp_approval_status_date] !="0000-00-00" && $value[pp_approval_status_date] !="0" && $value[pp_approval_status] ==3)
					{
					echo datediff( "d", $value[pp_submitted_to_buyer] , $value[pp_approval_status_date]);
					}
					?>
                    </td>
					<td width="90" style="word-wrap:break-word; word-break: break-all;">
					<? 
					if($value[pp_approval_status_date] !="" && $value[pp_approval_status_date] !="0000-00-00" && $value[pp_approval_status_date] !="0" && $value[pp_approval_status] ==3)
					{
					echo datediff( "d", $po_received_date , $value[pp_approval_status_date]);
					}
					?>
                    </td>
                    <td width="70">
					<? 
					if($tna_date_arr[$po_wise_data[$color_number_id][job_no]] !="" && $tna_date_arr[$po_wise_data[$color_number_id][job_no]] !="0000-00-00" && $tna_date_arr[$po_wise_data[$color_number_id][job_no]] !="0")
					{
					echo change_date_format($tna_date_arr[$po_wise_data[$color_number_id][job_no]],'dd-mm-yyyy','-');
					}
					?>
                    </td>
                    <td width="70">
					<?
					if($value[pp_approval_status_date] !="" && $value[pp_approval_status_date] !="0000-00-00" && $value[pp_approval_status_date] !="0" && $tna_date_arr[$po_wise_data[$color_number_id][job_no]] !="" && $tna_date_arr[$po_wise_data[$color_number_id][job_no]] !="0000-00-00" && $tna_date_arr[$po_wise_data[$color_number_id][job_no]] !="0")
					{
					echo datediff( "d", $value[pp_approval_status_date] , $tna_date_arr[$po_wise_data[$color_number_id][job_no]]);
					}
					?>
                    </td>
                    <td width="60">
                    <?
					echo datediff( "d", $po_received_date , $date)-1;
					?>
                    </td>
                   
                    <?
                     $shiping_status=max($po_wise_data[$color_number_id][shiping_status]);
					 
					 if($shiping_status==1 && $date_diff_1>10 )
						{
						$color="";	
						}
						if($shiping_status==1 && ($date_diff_1<=10 && $date_diff_1>=0))
						{
						$color="orange";
						}
						if($shiping_status==1 &&  $date_diff_1<0)
						{
						$color="red";	
						}
						//=====================================
						if($shiping_status==2 && $date_diff_1>10 )
						{
						$color="";	
						}
						if($shiping_status==2 && ($date_diff_1<=10 && $date_diff_1>=0))
						{
						$color="orange";	
						}
						if($shiping_status==2 &&  $date_diff_1<0)
						{
						$color="red";	
						}
						
						//========================================
						if($shiping_status==3 && $date_diff_3 >=0 )
						{
						$color="green";	
						}
						if($shiping_status==3 &&  $date_diff_3<0)
						{
						$color="#2A9FFF";	
						}
						?>
                         <td width="60" bgcolor="<? echo $color;  ?>" title="<? echo "if shiping status is partial ship or full pending then\n (current date - min shipment date) otherwise (max exfactory date - min shipment date)" ?>">
                        <?
						
					if($shiping_status==1 || $shiping_status==2)
					{
					echo $date_diff_1;
					}
					if($shiping_status==3)
					{
					echo $date_diff_3;
					}
					?>
                    </td>
					<td style="word-wrap:break-word; word-break: break-all;" onclick="remarks_popup('<? echo implode(",",$po_wise_data[$color_number_id][po_id]);?>',<? echo $value[color_number_id]?>,'<? echo $value[send_to_factory_date];?>','<? echo $value[recv_from_factory_date];?>','<? echo $value[submitted_to_buyer];?>','<? echo $value[approval_status_date];?>','<? echo $value[pp_submitted_to_buyer];?>','<? echo $value[pp_approval_status_date];?>')"><a href="##">View</a></td>
                    </tr>
                <?
				$i++;
				}
				}
				?>
                 
				</table>
				<table class="rpt_table" width="3390" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tfoot>
					<th width="30"><!--SL--></th>
					<th width="50"><!--Buyer--></th>
					<th width="100"><!--Job No--></th>
					<th width="100"><!--Style Ref--></th>
                    <th width="100" id=""><? //echo number_format($tot_job_qnty,2); ?></th>
                    <th width="100"><!--Team--></th>
                    <th width="100"><!--Team Member--></th>
                    <th width="200"><!--Item--></th>
					<th width="90"><!--Order No--></th>
                    <th width="80"><!--Shipment Date--></th>
					<th width="80" id="ord_qty"><!--Order Qnty (Pcs)--><? echo $tot_order_quantity; ?></th>
					<th width="80" id="value_yarn_amount"><!--Yarn rerqd (Kg)--><? echo number_format($tot_grey_fab_qnty,2); ?></th>
					<th width="250"><!--Fabrication--></th>
					<th width="100"><!--Gmts. Color--></th>
					<th width="100"><!--PO Rec. Date--></th>
					<th width="60"><!--Fab Swatch Send Date--></th>
					<th width="80"><!--Fab Swatch Receive Date--></th>
                    <th width="100"><!--Days Taken For Lab Dip--></th>
					<th width="100"><!--Sample Fabric Booking Date--></th>
					<th width="100"><!--Lab Dip Submission Date--></th>
                    <th width="100"><!--Lab submission to Buyer End Date (TNA)--></th>
                    <th width="90"><!--Lab Dip Appv. Date--></th>
                    <th width="100"><!--Lab Approval End Date (TNA)--></th>
                    <th width="60"><!--Days Taken For Lab Dip Appv.--></th>
                    <th width="100"><!--Finish Fab In-house--></th>
                    <th width="150"><!--Days Taken For Finish Fab In-house--></th>
                    <th width="70"><!--PP End Submit Date--></th>
                    <th width="70"><!--PP Submit Date--></th>
					<th width="90"><!--PP Appv. Date--></th>
					<th width="90"><!--Days Taken For PP Appv.--></th>
					<th width="90"><!--Days Taken PO Rev Date Vs PP Appv. Date--></th>
                    <th width="70"><!--TNA End Date--></th>
                    <th width="70"><!--Deviation--></th>
                    <th width="60"><!--Days in Hand--></th>
                    <th width="60"><!--Days in Hand--></th>
					<th><!--Remarks /bulk in inhse date--></th>
					</tfoot>
				</table>
				</div>
			</fieldset>
		</div>
	<?
	}
//===========================================================================================================================================================
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows";
	exit();	
}

if($action=="labdip_veiw")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo "select color_name_id, submitted_to_buyer,case when approval_status= 3  then approval_status_date  end  as app_date, case when approval_status= 5  then approval_status_date  end  as resubmit_date from wo_po_lapdip_approval_info where po_break_down_id in($po_id) and color_name_id=$color_id and approval_status in(3,5) and is_deleted=0 and status_active=1 order by id";
	
	$sql_lapdib_comments=sql_select( "select color_name_id,submitted_to_buyer, approval_status, approval_status_date from wo_po_lapdip_approval_info where po_break_down_id in($po_id) and color_name_id=$color_id  and is_deleted=0 and status_active=1 order by id");
	
	
	
	//$sql_lapdib_comments=sql_select("select color_name_id, submitted_to_buyer,case when approval_status= 3  then approval_status_date  end  as app_date, case when approval_status= 5  then approval_status_date  end  as resubmit_date from wo_po_lapdip_approval_info where po_break_down_id in($po_id) and color_name_id=$color_id and approval_status in(3,5) and is_deleted=0 and status_active=1 order by id");
	?>
    
    
    <table class="rpt_table" width="300" cellpadding="0" cellspacing="0" border="1" rules="all">
	<thead>
    <th width="30">SL</th> 
    <th>Color</th>
    <th>Submit Date</th>
    <th>Status</th>
    <th>Status Date</th>
    </thead>
    <tbody>
    <?
	$i=1;
	foreach($sql_lapdib_comments as $row_lapdib_comments)
	{
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";   
	?>
    <tr bgcolor="<? echo $bgcolor; ?>">
    <td><? echo $i;?></td>
    <td><? echo  $color_name_library[$row_lapdib_comments[csf('color_name_id')]];?></td>
    <td>
	<? 
	if($row_lapdib_comments[csf('submitted_to_buyer')] !="" && $row_lapdib_comments[csf('submitted_to_buyer')] !="0000-00-00" && $row_lapdib_comments[csf('submitted_to_buyer')] !="0")
	{
	echo  change_date_format($row_lapdib_comments[csf('submitted_to_buyer')],'dd-mm-yyyy','-');
	}
	?>
    </td>
    <td>
	<? 
	
	echo  $approval_status[$row_lapdib_comments[csf('approval_status')]];
	
	?>
    </td>
    <td>
	<? 
	if($row_lapdib_comments[csf('approval_status_date')] !="" && $row_lapdib_comments[csf('approval_status_date')] !="0000-00-00" && $row_lapdib_comments[csf('approval_status_date')] !="0")
	{
	echo  change_date_format($row_lapdib_comments[csf('approval_status_date')],'dd-mm-yyyy','-');
	}
	?>
    </td>
    </tr>
    <?
	$i++;
	}
	?>
    <tr>
    </tbody>
    </table>
    
    
    
    
    <?
}

if($action=="ppsample_veiw")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo "select a.color_number_id,b.approval_status, approval_status_date from  wo_po_color_size_breakdown a, wo_po_sample_approval_info b , lib_sample c where a.po_break_down_id=b.po_break_down_id and a.id=b.color_number_id and  b.sample_type_id=c.id	and b.po_break_down_id in($po_id)  and c.sample_type=2 and a.color_number_id=$color_id     and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";
		$sql_sample_comments=sql_select("select a.color_number_id,b.submitted_to_buyer,b.approval_status, approval_status_date from  wo_po_color_size_breakdown a, wo_po_sample_approval_info b , lib_sample c where a.po_break_down_id=b.po_break_down_id and a.id=b.color_number_id and  b.sample_type_id=c.id	and b.po_break_down_id in($po_id)  and c.sample_type=2 and a.color_number_id=$color_id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and (b.entry_form_id is null or b.entry_form_id=0)");

	
	//$sql_sample_comments=sql_select("select a.color_number_id,b.submitted_to_buyer, case when b.approval_status=3 then 	b.approval_status_date end as app_date,case when b.approval_status=5 then 	b.approval_status_date end as resubmit_date from  wo_po_color_size_breakdown a, wo_po_sample_approval_info b , lib_sample c where a.po_break_down_id=b.po_break_down_id and a.id=b.color_number_id and and b.sample_type_id=c.id	b.po_break_down_id in($po_id) and approval_status in(3,5) and c.sample_type=2 and a.color_number_id=$color_id     and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1");
	?>
   <table class="rpt_table" width="300" cellpadding="0" cellspacing="0" border="1" rules="all">
	<thead>
    <th width="30">SL</th> 
    <th>Color</th>
    <th>Submit Date</th>
    <th>Status</th>
    <th>Status Date</th>
    </thead>
    <tbody>
    <?
	$i=1;
	foreach($sql_sample_comments as $sql_sample_comments)
	{
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";   
	?>
    <tr bgcolor="<? echo $bgcolor; ?>">
    <td><? echo $i;?></td>
    <td><? echo  $color_name_library[$sql_sample_comments[csf('color_number_id')]];?></td>
    <td>
	<? 
	if($sql_sample_comments[csf('submitted_to_buyer')] !="" && $sql_sample_comments[csf('submitted_to_buyer')] !="0000-00-00" && $sql_sample_comments[csf('submitted_to_buyer')] !="0")
	{
	echo  change_date_format($sql_sample_comments[csf('submitted_to_buyer')],'dd-mm-yyyy','-');
	}
	?>
    </td>
    <td>
	<? 
	
	echo  $approval_status[$sql_sample_comments[csf('approval_status')]];
	
	?>
    </td>
    <td>
	<? 
	if($sql_sample_comments[csf('approval_status_date')] !="" && $sql_sample_comments[csf('approval_status_date')] !="0000-00-00" && $sql_sample_comments[csf('approval_status_date')] !="0")
	{
	echo  change_date_format($sql_sample_comments[csf('approval_status_date')],'dd-mm-yyyy','-');
	}
	?>
    </td>
    </tr>
    <?
	$i++;
	}
	?>
    <tr>
    </tbody>
    </table>
    
    <?
}

	

if($action=="remarks_veiw")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$sql_lapdib_comments=sql_select("select color_name_id,lapdip_comments from wo_po_lapdip_approval_info where po_break_down_id in($po_id) and color_name_id=$color_id  and is_deleted=0 and status_active=1");//and  (send_to_factory_date='$send_to_factory_date' or recv_from_factory_date='$recv_from_factory_date' or submitted_to_buyer='$submitted_to_buyer' or approval_status_date='$approval_status_date') 
	
	$sql_sample_comments=sql_select("select a.color_number_id,b.sample_comments from  wo_po_color_size_breakdown a, wo_po_sample_approval_info b , lib_sample c where a.po_break_down_id=b.po_break_down_id and a.id=b.color_number_id  and b.sample_type_id=c.id	and b.po_break_down_id in($po_id) and c.sample_type=2 and a.color_number_id=$color_id    and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and (b.entry_form_id is null or b.entry_form_id=0)");//and  (b.submitted_to_buyer='$pp_submitted_to_buyer' or b.approval_status_date='$pp_approval_status_date') 
	?>
    <table class="rpt_table" width="530" cellpadding="0" cellspacing="0" border="1" rules="all">
	<thead>
    <th width="30">SL</th> 
    <th>Comments</th>
    </thead>
    <tbody>
    <tr>
    <td colspan="3"><strong>Lapdib Comments</strong></td>
    </tr>
    
    <?
	$i=1;
	foreach($sql_lapdib_comments as $row_lapdib_comments)
	{
	?>
    <tr>
    <td><? echo $i;?></td>
    <td><? echo  $row_lapdib_comments[csf('lapdip_comments')];?></td>
    </tr>
    <?
	$i++;
	}
	?>
    <tr>
    <td colspan="3"><strong>Sample Comments</strong></td>
    </tr>
    
    <?
	$i=1;
	foreach($sql_sample_comments as $row_sample_comments)
	{
	?>
    <tr>
    <td><? echo $i;?></td>
    <td><? echo  $row_sample_comments[csf('sample_comments')];?></td>
    </tr>
    <?
	$i++;
	}
	?>
    </tbody>
    </table>
    <?
}

if($action=="booking_date_view")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$sql_booking=sql_select("select b.booking_no, b.booking_date from wo_pre_cost_fabric_cost_dtls a,wo_booking_mst b, wo_booking_dtls c where a.job_no=b.job_no and a.job_no=c.job_no and a.id=c.pre_cost_fabric_cost_dtls_id and b.booking_no=c.booking_no and b.booking_type=4 and b.item_category=2 and b.fabric_source in(1,2,3)  and c.po_break_down_id in($po_id) and c.fabric_color_id=$color_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.booking_no,b.booking_date");//and a.body_part_id in(1,14,15,16,17,20)
	?>
    <table class="rpt_table" width="310" cellpadding="0" cellspacing="0" border="1" rules="all">
	<thead>
    <th width="30">SL</th> 
    <th width="100">Booking No</th>
    <th>Booking Date</th>
    </thead>
    <tbody>
    <?
	$i=1;
	foreach($sql_booking as $row_booking)
	{
	?>
    <tr>
    <td><? echo $i;?></td>
    <td><? echo  $row_booking[csf('booking_no')];?></td>
    <td>
	<? 
	if($row_booking[csf('booking_date')] !="" && $row_booking[csf('booking_date')] !="0000-00-00" && $row_booking[csf('booking_date')] !="0")
	{
	echo  change_date_format($row_booking[csf('booking_date')],'dd-mm-yyyy','-');
	}
	?>
    </td>
    </tr>
    <?
	$i++;
	}
	?>
    </tbody>
    </table>
    <?
}

if($action=="fin_receive_date_view")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

					
	$sql_fin_fab_receive_qty=sql_select("select  a.receive_date,a.booking_id,a.booking_no
					from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c 
					where a.id=b.mst_id  and a.entry_form=37 and  a.item_category=2  and b.id=c.dtls_id and b.trans_id=c.trans_id and c.trans_type=1 and c.po_breakdown_id in($po_id) and c.color_id=$color_id $booking_id   and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by  a.receive_date,a.booking_id,a.booking_no");
	?>
    <table class="rpt_table" width="310" cellpadding="0" cellspacing="0" border="1" rules="all">
	<thead>
    <th width="30">SL</th> 
    <th width="100">Booking No</th>
    <th>Receive Date</th>
    </thead>
    <tbody>
    <?
	$i=1;
	foreach($sql_fin_fab_receive_qty as $row_fin_fab_receive_qty)
	{
	?>
    <tr>
    <td><? echo $i;?></td>
    <td><? echo  $row_fin_fab_receive_qty[csf('booking_no')];?></td>
    <td>
	<? 
	if($row_fin_fab_receive_qty[csf('receive_date')] !="" && $row_fin_fab_receive_qty[csf('receive_date')] !="0000-00-00" && $row_fin_fab_receive_qty[csf('receive_date')] !="0")
	{
	echo  change_date_format($row_fin_fab_receive_qty[csf('receive_date')],'dd-mm-yyyy','-');
	}
	?>
    </td>
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