<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
extract($_REQUEST);
$user_id=$_SESSION['logic_erp']['user_id'];
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$tna_status_arr=array(1=>'Raised',2=>'Closed');
$closed_user_arr = array(1,610,805,836,552,630);
$raised_user_arr = array(1,610,805,836);


$tna_process_start_date="2014-12-01";
if($db_type==0) $blank_date="0000-00-00"; else $blank_date=""; 

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
	exit();	 
}

if ($action=="load_drop_down_marchant")
{
	echo create_drop_down( "cbo_team_member", 110, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Merchant --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_team_agent", 110, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21))  group by a.id,a.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" ); 
	exit();
}



if($action=="generate_report")
{
 
	$maxHight="360px";
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
		
		$maxHight="auto";
		$image_path="../";
		
		?>
        <style> table{font-size:12px!important;} </style>
        <?
	}

	
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_taks_name=str_replace("'","",$txt_taks_name);
	$tna_task_id=str_replace("'","",$tna_task_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_team_agent=str_replace("'","",$cbo_team_agent);
	$cbo_team_leader=str_replace("'","",$cbo_team_leader);
	$cbo_team_member=str_replace("'","",$cbo_team_member);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_style_ref_no=str_replace("'","",$txt_style_ref_no);
	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$cbo_shipment_status=str_replace("'","",$cbo_shipment_status);
	$cbo_order_status=str_replace("'","",$cbo_order_status);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$tna_status=str_replace("'","",$cbo_tna_status);
	$cbo_task_group=str_replace("'","",$cbo_task_group);
	$cbo_issue_status=str_replace("'","",$cbo_issue_status);
	
	if($cbo_task_group){$task_group_con=" and task_group ='$cbo_task_group'";}
	
	
	if($tna_task_id!="") $task_cond=" and task_name in ($tna_task_id)"; else $task_cond="";
	$mod_sql= sql_select("select id,task_catagory,task_name,task_short_name,task_type,completion_percent,task_group from lib_tna_task where is_deleted = 0 and status_active=1 and task_type=1 $task_cond $task_group_con order by task_sequence_no asc");
	
	$tna_task_array=array();
	$tna_task_id=array();
	$tna_task_cat=array();
	$tna_task_name_arr=array();
	//$tna_task_detls=array();
	foreach ($mod_sql as $row)
	{
		$tna_task_group_by_id[$row[csf("task_name")]]=$row[csf("task_group")];
		$tna_task_id[$row[csf("task_name")]]=$row[csf("task_name")];
		$tna_task_array[$row[csf("task_name")]] =$row[csf("task_short_name")];
		$tna_task_cat[$row[csf("id")]]=$row[csf("task_catagory")];
		$tna_task_name_arr[$row[csf("id")]]=$row[csf("task_name")];
	}
	$cbo_company_id=$cbo_company_name;
	
	$order_status_cond="";
	$sql_cond="";
	if($cbo_search_type==2)
	{
		if($cbo_company_name>0) $sql_cond=" and a.company_name = $cbo_company_name";
		if($cbo_buyer_name>0) $sql_cond.=" and a.buyer_name = $cbo_buyer_name";
		if($cbo_team_agent>0) $sql_cond.=" and a.agent_name = $cbo_team_agent";
		if($cbo_team_leader>0) $sql_cond.=" and a.team_leader = $cbo_team_leader";
		if($cbo_team_member>0) $sql_cond.=" and a.dealing_marchant = $cbo_team_member";
		if($cbo_order_status>0) $sql_cond.=" and b.is_confirmed=$cbo_order_status";
		
		if($cbo_shipment_status==3){$sql_cond.=" and b.shiping_status=$cbo_shipment_status";}
		elseif($cbo_shipment_status==4){$sql_cond.=" and b.shiping_status !=3";}
		else{$sql_cond.="";}
		
		
		if($txt_job_no!="") $sql_cond.=" and a.job_no_prefix_num ='$txt_job_no'";
		if($txt_order_no!="") $sql_cond.=" and b.po_number ='$txt_order_no'";
		if($txt_style_ref_no!="") $sql_cond.=" and a.style_ref_no ='$txt_style_ref_no'";
		
		$date_range="";
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$sql_cond.=" and c.country_ship_date between '$txt_date_from' and '$txt_date_to'";
		}
		
		$sql_country = "SELECT b.id
		FROM  wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c 
		WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.is_deleted = 0 and c.status_active=1 $sql_cond";
		
		$country_result=sql_select($sql_country);
		$country_po_id=array();
		foreach($country_result as $row)
		{
			$country_po_id[$row[csf("id")]]=$row[csf("id")];
		}
	}
	else
	{
		if($cbo_company_name>0) $sql_cond=" and a.company_name = $cbo_company_name";
		if($cbo_buyer_name>0) $sql_cond.=" and a.buyer_name = $cbo_buyer_name";
		if($cbo_team_agent>0) $sql_cond.=" and a.team_leader = $cbo_team_agent";
		if($cbo_team_leader>0) $sql_cond.=" and a.team_leader = $cbo_team_leader";
		if($cbo_team_member>0) $sql_cond.=" and a.dealing_marchant = $cbo_team_member";
		if($cbo_order_status>0) $sql_cond.=" and b.is_confirmed=$cbo_order_status";
		
/*		if($cbo_shipment_status==3) $sql_cond.=" and b.shiping_status=$cbo_shipment_status"; 
		else $sql_cond.=" and b.shiping_status !=3";
*/		
		if($cbo_shipment_status==3){$sql_cond.=" and b.shiping_status=$cbo_shipment_status";}
		elseif($cbo_shipment_status==4){$sql_cond.=" and b.shiping_status !=3";}
		else{$sql_cond.="";}
		
		
		
		
		if($txt_job_no!="") $sql_cond.=" and a.job_no_prefix_num ='$txt_job_no'";
		if($txt_order_no!="") $sql_cond.=" and b.po_number ='$txt_order_no'";
		if($txt_style_ref_no!="") $sql_cond.=" and a.style_ref_no ='$txt_style_ref_no'";
		
		$date_range="";
		if($txt_date_from!="" && $txt_date_to!="")
		{
			if($cbo_search_type==1)
			{
				$sql_cond.=" and b.pub_shipment_date between '$txt_date_from' and '$txt_date_to'";
			}
			else if($cbo_search_type==3)
			{
				$sql_cond.=" and to_char(b.insert_date,'DD-MM-YYYY') between '".change_date_format($txt_date_from)."' and '".change_date_format($txt_date_to)."'";
			}
			else if($cbo_search_type==4)
			{
				$sql_cond.=" and c.task_start_date between '$txt_date_from' and '$txt_date_to'";
			}
			else
			{
				$sql_cond.=" and c.task_finish_date between '$txt_date_from' and '$txt_date_to'";
			}
		}
	}
	
 
	
	$tna_all_task=implode(",",$tna_task_id);
	
	$po_no_arr_all=implode(",",$po_no_arr); if($po_no_arr_all!="") $po_no_arr_all .=",0"; else $po_no_arr_all .="0"; 
	$job_no_all="'".implode("','",$job_no_arr)."'";
	$c=count($tna_task_id);
	
	
	if($cbo_search_type!=2)
	{
		if($db_type==0)
		{
			$sql ="select a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, a.agent_name, a.team_leader,a.gmts_item_id, b.id, b.po_number,a.total_set_qnty,b.po_quantity, c.po_number_id, c.shipment_date, c.po_receive_date,";
			$i=1;
		
			foreach( $tna_task_id as $dval=>$id)    	
			{
				if ($i!=$c) $sql .="max(CASE WHEN CONCAT(c.task_number) = '".$id."' and c.task_type=1 THEN concat(c.actual_start_date,'_',c.actual_finish_date,'_',c.task_start_date,'_',c.task_finish_date,'_',c.id,'_',c.task_number)  END ) as status$id, ";
				else $sql .="max(CASE WHEN CONCAT(c.task_number) = '".$id."' and c.task_type=1 THEN concat(c.actual_start_date,'_',c.actual_finish_date,'_',c.task_start_date,'_',c.task_finish_date,c.id,'_',c.task_number)  END ) as status$id ";
				$i++;
			}
			
			$sql .=" from  wo_po_details_master a, wo_po_break_down b, tna_process_mst c
			where a.job_no=b.job_no_mst and b.id=c.po_number_id $sql_cond and a.status_active=1 and b.status_active=1 and c.status_active=1  and b.po_quantity>0 and c.task_type=1 
			group by a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, a.agent_name, a.team_leader, a.gmts_item_id, b.id, b.po_number, c.po_number_id, c.shipment_date, c.po_receive_date,b.po_quantity,a.total_set_qnty ,b.po_quantity
			order by c.shipment_date,b.id,a.job_no"; 
		}
		else
		{
			$sql ="select a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, a.agent_name, a.team_leader, a.gmts_item_id, b.id, b.po_number,b.EXTENDED_SHIP_DATE, a.total_set_qnty,b.po_quantity, c.po_number_id, c.shipment_date, c.po_receive_date,";
			$i=1;
			foreach( $tna_task_id as $dval=>$id)    	
			{
				if ($i!=$c) $sql .="max(CASE WHEN c.task_number = '".$id."' and c.task_type=1 THEN c.actual_start_date || '_' || c.actual_finish_date || '_' || c.task_start_date || '_' || c.task_finish_date || '_' || c.id  || '_' || c.task_number  END ) as status$id, ";
				
				else $sql .="max(CASE WHEN c.task_number = '".$id."' and c.task_type=1 THEN c.actual_start_date || '_' || c.actual_finish_date || '_' || c.task_start_date || '_' || c.task_finish_date || '_' || c.id  || '_' || c.task_number  END ) as status$id ";
				
				$i++;
			}
			$sql .=" from  wo_po_details_master a, wo_po_break_down b, tna_process_mst c
			where a.job_no=b.job_no_mst and b.id=c.po_number_id $sql_cond and a.status_active=1 and b.status_active=1 and c.status_active=1  and b.po_quantity>0  and c.task_type=1 
			group by a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, a.agent_name, a.team_leader, a.gmts_item_id, b.id, b.po_number,b.EXTENDED_SHIP_DATE, c.po_number_id, c.shipment_date, c.po_receive_date,b.po_quantity,a.total_set_qnty 
			order by c.shipment_date,b.id,a.job_no"; 
		}
		
		 //echo $sql;die;
	}
	else
	{
		if($db_type==0)
		{
			$sql ="select a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, a.agent_name, a.team_leader, a.gmts_item_id, b.id, b.po_number,a.total_set_qnty,b.po_quantity, c.po_number_id, c.shipment_date, c.po_receive_date,";
			$i=1;
		
			foreach( $tna_task_id as $dval=>$id)    	
			{
				if ($i!=$c) $sql .="max(CASE WHEN CONCAT(c.task_number) = '".$id."' and c.task_type=1  THEN concat(c.actual_start_date,'_',c.actual_finish_date,'_',c.task_start_date,'_',c.task_finish_date,'_',c.id,'_',c.task_number)  END ) as status$id, ";
				else $sql .="max(CASE WHEN CONCAT(c.task_number) = '".$id."' and c.task_type=1  THEN concat(c.actual_start_date,'_',c.actual_finish_date,'_',c.task_start_date,'_',c.task_finish_date,c.id,'_',c.task_number)  END ) as status$id ";
				$i++;
			}
			
			$sql .=" from  wo_po_details_master a, wo_po_break_down b, tna_process_mst c
			where a.job_no=b.job_no_mst and b.id=c.po_number_id and a.status_active=1 and b.status_active=1 and c.status_active=1  and b.po_quantity>0 and c.task_type=1  and b.id in(". implode(',',$country_po_id)."
			group by a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, a.agent_name, a.team_leader, a.gmts_item_id, b.id, b.po_number,  c.shipment_date, c.po_number_id, c.po_receive_date,a.total_set_qnty ,b.po_quantity
			order by c.shipment_date,b.id,a.job_no"; 
		}
		else
		{
			$sql ="select a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, a.agent_name, a.team_leader, a.gmts_item_id, b.id, b.po_number,b.EXTENDED_SHIP_DATE,a.total_set_qnty,b.po_quantity, c.po_number_id, c.shipment_date, c.po_receive_date,";
			$i=1;
			foreach( $tna_task_id as $dval=>$id)    	
			{
				if ($i!=$c) $sql .="max(CASE WHEN c.task_number = '".$id."' and c.task_type=1 THEN c.actual_start_date || '_' || c.actual_finish_date || '_' || c.task_start_date || '_' || c.task_finish_date || '_' || c.id  || '_' || c.task_number  END ) as status$id, ";
				
				else $sql .="max(CASE WHEN c.task_number = '".$id."' and c.task_type=1 THEN c.actual_start_date || '_' || c.actual_finish_date || '_' || c.task_start_date || '_' || c.task_finish_date || '_' || c.id  || '_' || c.task_number  END ) as status$id ";
				
				$i++;
			}
			$sql .=" from  wo_po_details_master a, wo_po_break_down b, tna_process_mst c
			where a.job_no=b.job_no_mst and b.id=c.po_number_id and a.status_active=1 and b.status_active=1 and c.status_active=1  and b.po_quantity>0 and c.task_type=1";
			
			$chunk_po_no_arr_all=array_chunk(array_unique($country_po_id),999);
			$p=1;
			foreach($chunk_po_no_arr_all as $tna_po_id)
			{
				if($p==1) $sql .=" and (c.po_number_id in(".implode(',',$tna_po_id).")"; else $sql .=" or c.po_number_id in(".implode(',',$tna_po_id).")";
				$p++;
			}
			$sql .=" )";
			
			$sql .="group by a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, a.agent_name, a.team_leader, a.gmts_item_id, b.id, b.po_number,b.EXTENDED_SHIP_DATE, c.po_number_id, c.shipment_date, c.po_receive_date ,a.total_set_qnty
			order by c.shipment_date,b.id,a.job_no"; 
		}
	}
	
	
	 // echo $sql;
	
	
	$job_image=return_library_array("select master_tble_id,image_location from common_photo_library","master_tble_id",'image_location');
	$brand_arr=return_library_array("select master_tble_id,brand_name from lib_buyer_brand","id",'brand_name');
	
	$sql_member = sql_select("SELECT team_member_name,id FROM lib_mkt_team_member_info WHERE is_deleted = 0 and status_active=1");
	foreach( $sql_member as  $row ) 
	{	
		$team_member_arr[$row[csf('id')]]=$row[csf('team_member_name')];
	}
	
	
	$result = sql_select("select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name" ) ;
	$team_leader_name = array();
	foreach( $result as  $row ) 
	{	
		$team_leader_name[$row[csf('id')]]=$row[csf('team_leader_name')];
	}
	
	
	$sql_buyer = sql_select("SELECT buyer_name,id FROM  lib_buyer WHERE is_deleted = 0 and status_active=1 order by id asc");
	foreach( $sql_buyer as  $row ) 
	{	
		$buyer_name_arr[$row[csf('id')]]=$row[csf('buyer_name')];
	}
	
	//echo $sql;die;
	
	$data_sql= sql_select($sql);
	
	$poArr=array();
	foreach ($data_sql as $row)
	{
		$poArr[$row[csf('po_number_id')]]=$row[csf('po_number_id')];
	}
	 
	$issue_sql="select ORDER_ID,ISSUE_STATUS from TNA_TASK_ISSUE_RAISED_CLOSED where TASK_TYPE=1 and STATUS_ACTIVE=1 and IS_DELETED=0 ".where_con_using_array($poArr,0,'ORDER_ID')."";
	$issue_sql_res = sql_select($issue_sql);
	$total_issue_raised_close_arr=array();
	foreach( $issue_sql_res as  $row ) 
	{	
		$total_issue_raised_close_arr[$row['ORDER_ID']][$row['ISSUE_STATUS']]+=1;
	}



	$sql_con="select a.job_no,a.booking_no_prefix_num,b.po_break_down_id from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.is_short in(1,2) and a.BOOKING_TYPE=1  and a.IS_DELETED=0 and b.IS_DELETED=0  and a.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 ".where_con_using_array($poArr,1,'b.po_break_down_id')." order by a.booking_no_prefix_num";// and a.entry_form=86
	// echo $sql_con;
	$sql_booking_sql = sql_select($sql_con);
	foreach( $sql_booking_sql as  $row ) 
	{	
		$booking_no_arr[$row[csf('po_break_down_id')]][$row[csf('booking_no_prefix_num')]]=$row[csf('booking_no_prefix_num')];
	}
	
	
	$width=(count($tna_task_id)*240)+1470;
	
	ob_start();
	
	?>
    <div style="width:<? echo $width+20; ?>px" align="left">
    <table width="<? echo $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
    	<thead>
        	<tr>
            	<th width="40" rowspan="2">SL</th>
                <th width="100" rowspan="2">Team Leader</th>
                <th width="80" rowspan="2">Merchant<br>Contact No</th>
                <th width="70" rowspan="2">Buyer Name</th>
                <th width="70" rowspan="2">Agent & Brand Name</th>
                <th width="60" rowspan="2">Job No.</th>
                <th width="100" rowspan="2">Fab.Booking</th>
                <th width="60" rowspan="2">Image</th>
                <th width="120" rowspan="2">Style Ref.</th>
                <th width="60" rowspan="2">Issue Raised</th>
                <th width="60" rowspan="2">Issue Closed</th>
                <th width="120" rowspan="2">Item</th>
                <th width="50" rowspan="2">SMV</th>  
                <th width="120" rowspan="2">PO Number</th>
                <th width="100" rowspan="2">PO Qty(PCS)</th>
                <th width="80" rowspan="2">PO Rcv. Date</th>
                <th width="80" rowspan="2">Shipment Date</th>
                <th width="80" rowspan="2">Extended Ship Date</th>
                <th width="60" rowspan="2">PO Lead Time</th>
                <?
					$i=0;
					foreach($tna_task_array as $id=>$key)
					{
						$i++;
						echo '<th width="240" colspan="3">'. $key."<br><small>".$tna_task_group_by_id[$id].'</small></th>'; 
					}
					echo '</tr><tr>';
					
					$i=0;
					
					foreach($tna_task_array as $key)
					{
						$i++;
						if($cbo_search_type==4 || $tna_status==1)
						{
							echo '<th width="80">Plan Start</th><th width="80"> Actual Start</th><th width="80"> Status</th>'; 
						}
						else
						{
							echo '<th width="80">Plan Finish</th><th width="80"> Actual Finish</th><th width="80"> Status</th>'; 
						}
						
					}
					echo '</tr>';
					 
				?>
                </thead>
         </table>
         </div>
         
         <?
		 //die;
		 //echo "saju1_".count($tna_task_array); die; ?>
         
        <div style="overflow-y:scroll; max-height:<? echo $maxHight;?>; width:<? echo $width+20; ?>px;" align="left" id="scroll_body">
        <table width="<? echo $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
        
        <?
        
        $tid=0;
        $i=1;
        $count=0;
        $kid=1;
        $new_job_no=array();
        $h=0;
        $tot_po_qty=0;
		//print_r($data_sql); die;
		
		
		foreach ($data_sql as $row)
        {
			foreach($tna_task_id as $vid=>$key)
			{
				if ( $new_approval_arr[$row[csf('job_no')]][$key]=="") $new_data=explode("_",$row[csf('status').$key]); 
				else $new_data=explode("_",$new_approval_arr[$row[csf('job_no')]][$key]);
				$start_date="";
				$end_data="";
				
				if($cbo_search_type==4 || $tna_status==1)
				{
					$start_date=$new_data[2];
					$end_data=$new_data[0];
					
				}
				else
				{
					$start_date=$new_data[3];
					$end_data=$new_data[1];
				}
				
				if($start_date!="" && $start_date!="0000-00-00")
				{
					$display_datails_data[$row[csf("id")]]=$row[csf("id")];
				}
			}
		}
		
		


		
		$summary_data=array();
        foreach ($data_sql as $row)
        {
			if($display_datails_data[$row[csf("id")]]!="")
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
					if ($h%2==0)  
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";	
					$row[csf('po_qty_pcs')]=$row[csf('total_set_qnty')]*$row[csf('po_quantity')];
					
					
					if($cbo_issue_status==1 && $total_issue_raised_close_arr[$row[csf('id')]][1]==0){continue;}
					else if($cbo_issue_status==2 && $total_issue_raised_close_arr[$row[csf('id')]][1]>0){continue;}
					if($cbo_issue_status==2 && $total_issue_raised_close_arr[$row[csf('id')]][2]<=0){continue;}
					
					?>
					<tr bgcolor="<? echo $bgcolor;?>" style="vertical-align:middle" height="25" onClick="change_color('tr_<? echo $h;?>','<? echo $bgcolor;?>')" id="tr_<? echo $h; ?>">
						<td width="40" align="center"><? echo $kid++;?></td>
						<td width="100" style="word-break:break-all;"><p><? echo $team_leader_name[$row[csf('team_leader')]]; ?></p></td>
                        
                        <td width="80" style="word-break:break-all;"><p><? echo $team_member_arr[$row[csf('dealing_marchant')]]; ?></p></td>
						<td width="70" style="word-break:break-all;"><p><? echo $buyer_name_arr[$row[csf('buyer_name')]]; ?></p></td>

						<td width="70" style="word-break:break-all;"><p><? echo  $buyer_name_arr[$row[csf('agent_name')]]. '&'. $brand_arr[$row[csf('brand_id')]]; ?></p></td>
						 
						<td width="60" align="center" title="<? echo $row[csf('job_no')]; ?>"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                        <td width="100" align="center"><? echo implode(',',$booking_no_arr[$row[csf('id')]]); ?></td>
						<td width="60" title="" style="cursor:pointer;" onClick="openmypage_image('requires/tna_progress_vs_actual_plan_controller.php?action=show_image&job_no=<? echo $row[csf('job_no')]; ?>','Image View')"><img src="../../<? echo $image_path.$job_image[$row[csf('job_no')]];?>" width="50" height="30"  /></td>
						<td width="120" title="<? echo $row[csf('job_no')]; ?>"><div style="width:115px; word-wrap:break-word;"><? echo $row[csf('style_ref_no')]; ?></div></td>
						
						
						<td style="font-weight:bold;cursor: pointer;background-color:<?=($total_issue_raised_close_arr[$row[csf('id')]][1]>0)?'red':'';?>;" width="60" align="center" onClick="set_tna_issue(1,<?=$row[csf('id')];?>,<?=$row[csf('job_no_prefix_num')];?>)"><u><?=$total_issue_raised_close_arr[$row[csf('id')]][1]??'0';?></u></td>

						<td style="font-weight:bold;cursor: pointer;" width="60" align="center" onClick="set_tna_issue(2,<?=$row[csf('id')];?>,<?=$row[csf('job_no_prefix_num')];?>)"><u><?=$total_issue_raised_close_arr[$row[csf('id')]][2]??'0';?></u></td>
						<td width="120" title="<? echo $row[csf('job_no')]; ?>" style="word-break:break-all;"><div style="width:115px; word-wrap:break-word;">
						<?
						$gmts_item_arr=array_unique(explode(",",$row[csf('gmts_item_id')]));
						$all_garments="";
						foreach($gmts_item_arr as $gmt_id)
						{
							$all_garments.=$garments_item[$gmt_id].",";
						}
						echo chop($all_garments,",");
						?></div></td>
						<td width="50" align="center"><? echo $row[csf('set_smv')]; ?></td>
						<td width="120" align="center"><div style="width:115px; word-wrap:break-word;"><? echo $row[csf('po_number')];?></div> </td>
						<td width="100" align="right"><? echo number_format($row[csf('po_qty_pcs')]); $tot_po_qty+=$row[csf('po_qty_pcs')]; ?> </td>
						<td width="80" align="center"><? if($row[csf('po_receive_date')]!="" && $row[csf('po_receive_date')]!="0000-00-00") echo change_date_format($row[csf('po_receive_date')]); ?></td>
						<td width="80" align="center"><? if($row[csf('shipment_date')]!="" && $row[csf('shipment_date')]!="0000-00-00") echo change_date_format($row[csf('shipment_date')]); ?></td>
						<? 
						$po_lead_time=datediff( "d", date("Y-m-d",strtotime(change_date_format($row[csf('po_receive_date')]))), date("Y-m-d",strtotime(change_date_format($row[csf('shipment_date')]))) );
						?>
						<td width="80" align="center"><? echo change_date_format($row['EXTENDED_SHIP_DATE']);  ?></td>
						<td width="60" align="center"><? echo $po_lead_time;  ?></td>
						<?
						
						$i=0;
						foreach($tna_task_id as $vid=>$key)
						{
							$i++;
							if ( $new_approval_arr[$row[csf('job_no')]][$key]=="") $new_data=explode("_",$row[csf('status').$key]); 
							else $new_data=explode("_",$new_approval_arr[$row[csf('job_no')]][$key]);
							$start_date="";
							$end_data="";
							
							if($cbo_search_type==4 || $tna_status==1)
							{
								$start_date=$new_data[2];
								$end_data=$new_data[0];
							}
							else
							{
								$start_date=$new_data[3];
								$end_data=$new_data[1];
							}
							
							if($start_date=="" || $start_date=="0000-00-00")
							{
								$end_data="N/A"; 
							}
							else
							{
								$summary_data[$vid]["task_total"]++;
								if($end_data=="" || $end_data=="0000-00-00")
								{
									$date_dif=datediff( "d", $pc_date, $start_date);
									$date_dif=$date_dif-1;
									if($date_dif<0)
									{
										$diff_text="Due";
										$bg_color=' bgcolor="#FF0000" ';
										$summary_data[$vid]["due"]++;
									}
									else if($date_dif>=0)
									{
										$diff_text="In-hand";
										$bg_color=' bgcolor="#FFCC33" ';
										$summary_data[$vid]["in_hand"]++;
									}
									else $diff_text="";
								}
								else
								{
									$date_dif=datediff( "d", $end_data, $start_date);
									$date_dif=$date_dif-1;
									
									if($date_dif<0)
									{
										$diff_text="Later";
										$bg_color=' bgcolor="#359AFF" ';
										$summary_data[$vid]["later"]++;
									}
									else if($date_dif>0)
									{
										$diff_text="Earlier";
										$bg_color='';
										$summary_data[$vid]["earlier"]++;
									}
									else
									{
										$diff_text="At Per";
										$bg_color=' bgcolor="#00BB00" ';
										$summary_data[$vid]["at_per"]++;
									}
								}
								
							}
							
							
							if($end_data=="N/A")
							{
								echo '<td width="80"  align="center">'."<span style='color:#FF0000'> N/A </span>".'</td><td width="80"  align="center">'."<span style='color:#FF0000'> N/A </span>".'</td><td  width="80"  align="center"> <span style="color:#FF0000"> N/A </span></td>';
							}
							else
							{
								echo '<td width="80" align="center" >'.($start_date== "" || $start_date=="0000-00-00"?"<span style='color:#FF0000'> N/A </span>":change_date_format($start_date)).'</td>
								<td width="80" align="center" >'.($end_data== ""  || $end_data=="0000-00-00"?"":change_date_format($end_data)).'</td><td  width="80" align="center" '.$bg_color .'>&nbsp;'.$date_dif." ".$diff_text.' </td>';
							}
							
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
                <th width="40">&nbsp;</th>
                <th width="97">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="58">&nbsp;</th>
                <th width="98">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="120">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="120">&nbsp;</th>
                <th width="50">&nbsp;</th>  
                <th width="120">Total</th>
                <th width="100" id="total_po_qty" align="right"><? echo number_format($tot_po_qty);?></th>
                <th width="80" >&nbsp;</th>
				<th width="80" >&nbsp;</th>
                <th width="80" >&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th colspan="<? echo (count($tna_task_id)*3);?>" ></th>
            </tfoot>
        </table>
        <br />
       <?
	   if(count($summary_data)>0)
	   {
		   ?> 
            <table width="1400" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="summery">
                <thead>
                    <tr>
                        <th width="40" rowspan="2">SL</th>
                        <th width="120" rowspan="2">Task Name</th>
                        <th width="80" rowspan="2">Total Number</th>
                        <th width="80" colspan="10">Completed</th>
                        <th width="80" rowspan="2">Due</th>
                        <th width="80" rowspan="2">Due (%)</th>
                        <th width="80" rowspan="2">In-hand</th>
                        <th rowspan="2">In-hand (%)</th>
                    </tr>
                    <tr>
                        <th width="80">Earlier</th>
                        <th width="80">Earlier (%)</th>
                        <th width="80">At Per</th>
                        <th width="80">At Per (%)</th>
                        <th width="80">On Time</th>
                        <th width="80">On Time (%)</th>
                        <th width="80">Later</th>
                        <th width="80">Later (%)</th>
                        <th width="80">Total</th>
                        <th width="80">Total (%)</th>
                    </tr>
                </thead>
                <tbody>
                <?
                $i=1;
				//$mod_sql= sql_select("select id,task_catagory,task_name,task_short_name,task_type,completion_percent from lib_tna_task where is_deleted = 0 and status_active=1 $task_cond order by task_sequence_no asc");
                //foreach($summary_data as $task_id=>$val)
                //{
				//}
				
                foreach($mod_sql as $row)
				{
					if($summary_data[$row[csf("task_name")]]["task_total"]>0)
					{
						if ($i%2==0)  
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						$earlier_percent=$summary_data[$row[csf("task_name")]]["earlier"]/$summary_data[$row[csf("task_name")]]["task_total"]*100;
						$at_per_percent=$summary_data[$row[csf("task_name")]]["at_per"]/$summary_data[$row[csf("task_name")]]["task_total"]*100;
						$ontime_total=$summary_data[$row[csf("task_name")]]["earlier"]+$summary_data[$row[csf("task_name")]]["at_per"];
						$ontime_total_percent=$ontime_total/$summary_data[$row[csf("task_name")]]["task_total"]*100;
						$letter_percent=$summary_data[$row[csf("task_name")]]["later"]/$summary_data[$row[csf("task_name")]]["task_total"]*100;
						$com_tatal=$summary_data[$row[csf("task_name")]]["earlier"]+$summary_data[$row[csf("task_name")]]["at_per"]+$summary_data[$row[csf("task_name")]]["later"];
						$com_tatal_percent=$com_tatal/$summary_data[$row[csf("task_name")]]["task_total"]*100;
						$due_percent=$summary_data[$row[csf("task_name")]]["due"]/$summary_data[$row[csf("task_name")]]["task_total"]*100;
						$in_hand_percent=$summary_data[$row[csf("task_name")]]["in_hand"]/$summary_data[$row[csf("task_name")]]["task_total"]*100;
						
						$gt_task_total+=$summary_data[$row[csf("task_name")]]["task_total"];
						$gt_Earlier+=$summary_data[$row[csf("task_name")]]["earlier"];
						$gt_at_per+=$summary_data[$row[csf("task_name")]]["at_per"];
						$gt_ontime_total+=$ontime_total;
						$gt_later+=$summary_data[$row[csf("task_name")]]["later"];
						$gt_com_tatal+=$com_tatal;
						$gt_due+=$summary_data[$row[csf("task_name")]]["due"];
						$gt_in_hand+=$summary_data[$row[csf("task_name")]]["in_hand"];
						
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $h;?>','<? echo $bgcolor;?>')" id="tr_<? echo $h; ?>">
						<td align="center"><? echo $i; ?></td>
						<td><? echo $tna_task_array[$row[csf("task_name")]]; ?></td>
						<td align="right"><? echo number_format($summary_data[$row[csf("task_name")]]["task_total"],2); ?></td>
						<td align="right"><? echo number_format($summary_data[$row[csf("task_name")]]["earlier"],2); ?></td>
						<td align="right"><? echo number_format($earlier_percent,2); ?></td>
						<td align="right"><? echo number_format($summary_data[$row[csf("task_name")]]["at_per"],2); ?></td>
						<td align="right"><? echo number_format($at_per_percent,2); ?></td>
						<td align="right"><? echo number_format($ontime_total,2); ?></td>
						<td align="right"><? echo number_format($ontime_total_percent,2); ?></td>
						<td align="right"><? echo number_format($summary_data[$row[csf("task_name")]]["later"],2); ?></td>
						<td align="right"><? echo number_format($letter_percent,2); ?></td>
						<td align="right"><? echo number_format($com_tatal,2); ?></td>
						<td align="right"><? echo number_format($com_tatal_percent,2); ?></td>
						<td align="right"><? echo number_format($summary_data[$row[csf("task_name")]]["due"],2); ?></td>
						<td align="right"><? echo number_format($due_percent,2); ?></td>
						<td align="right"><? echo number_format($summary_data[$row[csf("task_name")]]["in_hand"],2); ?></td>
						<td align="right"><? echo number_format($in_hand_percent,2); ?></td>
						</tr>
						<?
						$i++;$h++;
					}
				}
				
				$gt_Earlier_percent=$gt_Earlier/$gt_task_total*100;
				$gt_at_per_percent=$gt_at_per/$gt_task_total*100;
				$gt_ontime_total_percent=$gt_ontime_total/$gt_task_total*100;
				$gt_later_percent=$gt_later/$gt_task_total*100;
				$gt_com_tatal_percent=$gt_com_tatal/$gt_task_total*100;
				$gt_due_percent=$gt_due/$gt_task_total*100;
				$gt_in_hand_percent=$gt_in_hand/$gt_task_total*100;	
                ?>
                <tfoot>
                    <tr>
                        <th align="right" colspan="2">Total / Avg: </th>
                        <th align="right"><? echo number_format($gt_task_total,2); ?></th>
                        <th align="right"><? echo number_format($gt_Earlier,2); ?></th>
                        <th align="right"><? echo number_format($gt_Earlier_percent,2); ?></th>
                        <th align="right"><? echo number_format($gt_at_per,2); ?></th>
                        <th align="right"><? echo number_format($gt_at_per_percent,2); ?></th>
                        <th align="right"><? echo number_format($gt_ontime_total,2); ?></th>
                        <th align="right"><? echo number_format($gt_ontime_total_percent,2); ?></th>
                        <th align="right"><? echo number_format($gt_later,2); ?></th>
                        <th align="right"><? echo number_format($gt_later_percent,2); ?></th>
                        <th align="right"><? echo number_format($gt_com_tatal,2); ?></th>
                        <th align="right"><? echo number_format($gt_com_tatal_percent,2); ?></th>
                        <th align="right"><? echo number_format($gt_due,2); ?></th>
                        <th align="right"><? echo number_format($gt_due_percent,2); ?></th>
                        <th align="right"><? echo number_format($gt_in_hand,2); ?></th>
                        <th align="right"><? echo number_format($gt_in_hand_percent,2); ?></th>
                    </tr>
                </tfoot>
                </tbody>
            </table>
            <?
	   }
	   ?>
    </div>
    
    
          <?
		  
		  //var_dump($summary_data);
		  
		/* $sql = sql_select("select designation,name from variable_settings_signature where report_id=95 and company_id=$cbo_company_id order by sequence_no" );
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
		echo '</tr></table>';*/
	
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

if($action=="generate_report_tna_hit_rate")
{
	$maxHight="360px";
		
	
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_taks_name=str_replace("'","",$txt_taks_name);
	$tna_task_id=str_replace("'","",$tna_task_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_team_agent=str_replace("'","",$cbo_team_agent);
	$cbo_team_leader=str_replace("'","",$cbo_team_leader);
	$cbo_team_member=str_replace("'","",$cbo_team_member);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_style_ref_no=str_replace("'","",$txt_style_ref_no);
	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$cbo_shipment_status=str_replace("'","",$cbo_shipment_status);
	$cbo_order_status=str_replace("'","",$cbo_order_status);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$tna_status=str_replace("'","",$cbo_tna_status);
	
	
	//echo $cbo_shipment_status;die;
	
	
	if($tna_task_id!=""){
		$task_cond=" and task_name in ($tna_task_id)";
		//$task_cond2=" and a.TASK_NUMBER in ($tna_task_id)";
	} 
	else{
		$task_cond="";
	}
	
	$mod_sql= sql_select("select id,task_catagory,task_name,task_short_name,task_type,completion_percent from lib_tna_task where is_deleted = 0 and status_active=1 $task_cond order by task_sequence_no asc");
	$tna_task_array=array();
	$tna_task_id=array();
	$tna_task_cat=array();
	$tna_task_name_arr=array();
	//$tna_task_detls=array();
	foreach ($mod_sql as $row)
	{
		$tna_task_id[$row[csf("task_name")]]=$row[csf("task_name")];
		$tna_task_array[$row[csf("task_name")]] =$row[csf("task_short_name")];
		$tna_task_cat[$row[csf("id")]]=$row[csf("task_catagory")];
		$tna_task_name_arr[$row[csf("id")]]=$row[csf("task_name")];
	}
	$cbo_company_id=$cbo_company_name;
	
	$order_status_cond="";
	$sql_cond="";
	if($cbo_search_type==2)
	{
		if($cbo_company_name>0) $sql_cond=" and a.company_name = $cbo_company_name";
		if($cbo_buyer_name>0) $sql_cond.=" and a.buyer_name = $cbo_buyer_name";
		if($cbo_team_agent>0) $sql_cond.=" and a.agent_name = $cbo_team_agent";
		if($cbo_team_leader>0) $sql_cond.=" and a.team_leader = $cbo_team_leader";
		if($cbo_team_member>0) $sql_cond.=" and a.dealing_marchant = $cbo_team_member";
		if($cbo_order_status>0) $sql_cond.=" and b.is_confirmed=$cbo_order_status";
		
		if($cbo_shipment_status==3){$sql_cond.=" and b.shiping_status=$cbo_shipment_status";}
		elseif($cbo_shipment_status==4){$sql_cond.=" and b.shiping_status !=3";}
		else{$sql_cond.="";}
		
		
		if($txt_job_no!="") $sql_cond.=" and a.job_no_prefix_num ='$txt_job_no'";
		if($txt_order_no!="") $sql_cond.=" and b.po_number ='$txt_order_no'";
		if($txt_style_ref_no!="") $sql_cond.=" and a.style_ref_no ='$txt_style_ref_no'";
		
		$date_range="";
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$sql_cond.=" and c.country_ship_date between '$txt_date_from' and '$txt_date_to'";
		}
		
		$sql_country = "SELECT b.id
		FROM  wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c 
		WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.is_deleted = 0 and c.status_active=1 $sql_cond";
		
		$country_result=sql_select($sql_country);
		$country_po_id=array();
		foreach($country_result as $row)
		{
			$country_po_id[$row[csf("id")]]=$row[csf("id")];
		}
	}
	else
	{
		if($cbo_company_name>0) $sql_cond=" and a.company_name = $cbo_company_name";
		if($cbo_buyer_name>0) $sql_cond.=" and a.buyer_name = $cbo_buyer_name";
		if($cbo_team_agent>0) $sql_cond.=" and a.team_leader = $cbo_team_agent";
		if($cbo_team_leader>0) $sql_cond.=" and a.team_leader = $cbo_team_leader";
		if($cbo_team_member>0) $sql_cond.=" and a.dealing_marchant = $cbo_team_member";
		if($cbo_order_status>0) $sql_cond.=" and b.is_confirmed=$cbo_order_status";
	
		if($cbo_shipment_status==3){$sql_cond.=" and b.shiping_status=$cbo_shipment_status";}
		elseif($cbo_shipment_status==4){$sql_cond.=" and b.shiping_status !=3";}
		else{$sql_cond.="";}
		
		if($txt_job_no!="") $sql_cond.=" and a.job_no_prefix_num ='$txt_job_no'";
		if($txt_order_no!="") $sql_cond.=" and b.po_number ='$txt_order_no'";
		if($txt_style_ref_no!="") $sql_cond.=" and a.style_ref_no ='$txt_style_ref_no'";
		
		$date_range="";
		if($txt_date_from!="" && $txt_date_to!="")
		{
			if($cbo_search_type==1)
			{
				$sql_cond.=" and b.pub_shipment_date between '$txt_date_from' and '$txt_date_to'";
			}
			else if($cbo_search_type==3)
			{
				$sql_cond.=" and to_char(b.insert_date,'DD-MM-YYYY') between '".change_date_format($txt_date_from)."' and '".change_date_format($txt_date_to)."'";
			}
			else if($cbo_search_type==4)
			{
				$sql_cond.=" and c.task_start_date between '$txt_date_from' and '$txt_date_to'";
			}
			else
			{
				$sql_cond.=" and c.task_finish_date between '$txt_date_from' and '$txt_date_to'";
			}
		}
	}
	
	
	
	$tna_all_task=implode(",",$tna_task_id);
	
	$po_no_arr_all=implode(",",$po_no_arr); if($po_no_arr_all!="") $po_no_arr_all .=",0"; else $po_no_arr_all .="0"; 
	$job_no_all="'".implode("','",$job_no_arr)."'";
	$c=count($tna_task_id);
	
	
	if($cbo_search_type!=2)
	{
		if($db_type==0)
		{
			$sql ="select a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, a.team_leader,a.gmts_item_id, b.id, b.po_number,a.total_set_qnty,b.po_quantity, c.po_number_id, c.shipment_date, c.po_receive_date,";
			$i=1;
		
			foreach( $tna_task_id as $dval=>$id)    	
			{
				if ($i!=$c) $sql .="max(CASE WHEN CONCAT(c.task_number) = '".$id."' THEN concat(c.actual_start_date,'_',c.actual_finish_date,'_',c.task_start_date,'_',c.task_finish_date,'_',c.id,'_',c.task_number)  END ) as status$id, ";
				else $sql .="max(CASE WHEN CONCAT(c.task_number) = '".$id."' THEN concat(c.actual_start_date,'_',c.actual_finish_date,'_',c.task_start_date,'_',c.task_finish_date,c.id,'_',c.task_number)  END ) as status$id ";
				$i++;
			}
			
			$sql .=" from  wo_po_details_master a, wo_po_break_down b, tna_process_mst c
			where a.job_no=b.job_no_mst and b.id=c.po_number_id $sql_cond and a.status_active=1 and b.status_active=1 and c.status_active=1  and b.po_quantity>0 
			group by a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, a.team_leader, a.gmts_item_id, b.id, b.po_number, c.po_number_id, c.shipment_date, c.po_receive_date,b.po_quantity,a.total_set_qnty ,b.po_quantity
			order by c.shipment_date,b.id,a.job_no"; 
		}
		else
		{
			$sql ="select a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, a.team_leader, a.gmts_item_id, b.id, b.po_number, a.total_set_qnty,b.po_quantity, c.po_number_id, c.shipment_date, c.po_receive_date,";
			$i=1;
			foreach( $tna_task_id as $dval=>$id)    	
			{
				if ($i!=$c) $sql .="max(CASE WHEN c.task_number = '".$id."' THEN c.actual_start_date || '_' || c.actual_finish_date || '_' || c.task_start_date || '_' || c.task_finish_date || '_' || c.id  || '_' || c.task_number  END ) as status$id, ";
				
				else $sql .="max(CASE WHEN c.task_number = '".$id."' THEN c.actual_start_date || '_' || c.actual_finish_date || '_' || c.task_start_date || '_' || c.task_finish_date || '_' || c.id  || '_' || c.task_number  END ) as status$id ";
				
				$i++;
			}
			$sql .=" from  wo_po_details_master a, wo_po_break_down b, tna_process_mst c
			where a.job_no=b.job_no_mst and b.id=c.po_number_id $sql_cond and a.status_active=1 and b.status_active=1 and c.status_active=1  and b.po_quantity>0 
			group by a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, a.team_leader, a.gmts_item_id, b.id, b.po_number, c.po_number_id, c.shipment_date, c.po_receive_date,b.po_quantity,a.total_set_qnty 
			order by c.shipment_date,b.id,a.job_no"; 
		}
	}
	else
	{
		if($db_type==0)
		{
			$sql ="select a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, a.team_leader, a.gmts_item_id, b.id, b.po_number,a.total_set_qnty,b.po_quantity, c.po_number_id, c.shipment_date, c.po_receive_date,";
			$i=1;
		
			foreach( $tna_task_id as $dval=>$id)    	
			{
				if ($i!=$c) $sql .="max(CASE WHEN CONCAT(c.task_number) = '".$id."' THEN concat(c.actual_start_date,'_',c.actual_finish_date,'_',c.task_start_date,'_',c.task_finish_date,'_',c.id,'_',c.task_number)  END ) as status$id, ";
				else $sql .="max(CASE WHEN CONCAT(c.task_number) = '".$id."' THEN concat(c.actual_start_date,'_',c.actual_finish_date,'_',c.task_start_date,'_',c.task_finish_date,c.id,'_',c.task_number)  END ) as status$id ";
				$i++;
			}
			
			$sql .=" from  wo_po_details_master a, wo_po_break_down b, tna_process_mst c
			where a.job_no=b.job_no_mst and b.id=c.po_number_id and a.status_active=1 and b.status_active=1 and c.status_active=1  and b.po_quantity>0  and b.id in(". implode(',',$country_po_id)."
			group by a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, a.team_leader, a.gmts_item_id, b.id, b.po_number,  c.shipment_date, c.po_number_id, c.po_receive_date,a.total_set_qnty ,b.po_quantity
			order by c.shipment_date,b.id,a.job_no"; 
		}
		else
		{
			$sql ="select a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, a.team_leader, a.gmts_item_id, b.id, b.po_number,a.total_set_qnty,b.po_quantity, c.po_number_id, c.shipment_date, c.po_receive_date,";
			$i=1;
			foreach( $tna_task_id as $dval=>$id)    	
			{
				if ($i!=$c) $sql .="max(CASE WHEN c.task_number = '".$id."' THEN c.actual_start_date || '_' || c.actual_finish_date || '_' || c.task_start_date || '_' || c.task_finish_date || '_' || c.id  || '_' || c.task_number  END ) as status$id, ";
				
				else $sql .="max(CASE WHEN c.task_number = '".$id."' THEN c.actual_start_date || '_' || c.actual_finish_date || '_' || c.task_start_date || '_' || c.task_finish_date || '_' || c.id  || '_' || c.task_number  END ) as status$id ";
				
				$i++;
			}
			$sql .=" from  wo_po_details_master a, wo_po_break_down b, tna_process_mst c
			where a.job_no=b.job_no_mst and b.id=c.po_number_id and a.status_active=1 and b.status_active=1 and c.status_active=1  and b.po_quantity>0";
			
			$chunk_po_no_arr_all=array_chunk(array_unique($country_po_id),999);
			$p=1;
			foreach($chunk_po_no_arr_all as $tna_po_id)
			{
				if($p==1) $sql .=" and (c.po_number_id in(".implode(',',$tna_po_id).")"; else $sql .=" or c.po_number_id in(".implode(',',$tna_po_id).")";
				$p++;
			}
			$sql .=" )";
			
			$sql .="group by a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, a.team_leader, a.gmts_item_id, b.id, b.po_number,  c.po_number_id, c.shipment_date, c.po_receive_date ,a.total_set_qnty
			order by c.shipment_date,b.id,a.job_no"; 
		}
	}
	
	
	   //echo $sql;
	
	
	$lib_company_arr=return_library_array("select ID,COMPANY_NAME from LIB_COMPANY","ID",'COMPANY_NAME');
	
	
	$job_image=return_library_array("select master_tble_id,image_location from common_photo_library","master_tble_id",'image_location');
	
	$sql_member = sql_select("SELECT team_member_name,id FROM lib_mkt_team_member_info WHERE is_deleted = 0 and status_active=1");
	foreach( $sql_member as  $row ) 
	{	
		$team_member_arr[$row[csf('id')]]=$row[csf('team_member_name')];
	}
	
	
	$result = sql_select("select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name" ) ;
	$team_leader_name = array();
	foreach( $result as  $row ) 
	{	
		$team_leader_name[$row[csf('id')]]=$row[csf('team_leader_name')];
	}
	
	
	$sql_buyer = sql_select("SELECT buyer_name,id FROM  lib_buyer WHERE is_deleted = 0 and status_active=1 order by id asc");
	foreach( $sql_buyer as  $row ) 
	{	
		$buyer_name_arr[$row[csf('id')]]=$row[csf('buyer_name')];
	}
	
	  //echo $sql;die;
	
	$data_sql= sql_select($sql);
	
	$poArr=array();$poWiseDataArr=array();
	foreach ($data_sql as $row)
	{
		$poArr[$row[csf('po_number_id')]]=$row[csf('po_number_id')];
		$poArr[$row[csf('po_number_id')]]=$row[csf('po_number_id')];
		$poWiseDataArr[$row[csf('po_number_id')]]=array(
			team_leader=>$row[csf('team_leader')],
			buyer_name=>$row[csf('buyer_name')]
		);
		
		
		
	}
	
	
	//selected task id start--------------------------------------------
	
		if($txt_date_from!="" && $txt_date_to!="")
		{
			if($cbo_search_type==4)
			{
				$sql_cond=" and a.task_start_date between '$txt_date_from' and '$txt_date_to'";
			}
			else
			{
				$sql_cond=" and a.task_finish_date between '$txt_date_from' and '$txt_date_to'";
			}
		}
	
	
	
	$task_cond2=" and a.TASK_NUMBER in (".implode(',',$tna_task_id).")";
	//$tna_process_task_sql = "SELECT a.PO_NUMBER_ID,a.TASK_NUMBER,a.TASK_FINISH_DATE AS PLAN_DATE,a.ACTUAL_FINISH_DATE AS ACTUAL_DATE FROM tna_process_mst a WHERE a.task_type = 1 and A.IS_DELETED=0 and A.STATUS_ACTIVE=1  ".where_con_using_array($poArr,0,'a.po_number_id')." $task_cond2 GROUP BY a.PO_NUMBER_ID,a.TASK_NUMBER,a.TASK_FINISH_DATE,a.ACTUAL_FINISH_DATE";  
	
	//$tna_process_task_sql = "SELECT a.PO_NUMBER_ID,a.TASK_NUMBER,a.TASK_START_DATE AS PLAN_DATE,a.ACTUAL_START_DATE AS ACTUAL_DATE FROM tna_process_mst a,LIB_TNA_TASK b WHERE a.task_type = 1 and b.TASK_NAME=a.TASK_NUMBER and A.IS_DELETED=0 and A.STATUS_ACTIVE=1  ".where_con_using_array($poArr,0,'a.po_number_id')." $task_cond2 $sql_cond GROUP BY a.PO_NUMBER_ID,a.TASK_NUMBER,a.TASK_START_DATE,a.ACTUAL_START_DATE, b.TASK_SEQUENCE_NO order by b.TASK_SEQUENCE_NO";
	
	$tna_process_task_sql = "SELECT a.PO_NUMBER_ID,a.TASK_NUMBER,a.TASK_FINISH_DATE AS PLAN_DATE,a.ACTUAL_FINISH_DATE AS ACTUAL_DATE FROM tna_process_mst a,LIB_TNA_TASK b WHERE a.task_type = 1 and b.TASK_NAME=a.TASK_NUMBER and A.IS_DELETED=0 and A.STATUS_ACTIVE=1  ".where_con_using_array($poArr,0,'a.po_number_id')." $task_cond2 $sql_cond GROUP BY a.PO_NUMBER_ID,a.TASK_NUMBER,a.TASK_FINISH_DATE,a.ACTUAL_FINISH_DATE, b.TASK_SEQUENCE_NO order by b.TASK_SEQUENCE_NO";  
	
	   //echo $tna_process_task_sql;die;
	
	$tna_process_task_sql_result = sql_select($tna_process_task_sql);
	$tna_process_task_arr=array();
	foreach( $tna_process_task_sql_result as  $row ) 
	{	
		$tna_process_task_arr[$row['TASK_NUMBER']]=$tna_task_array[$row['TASK_NUMBER']];
		$tna_process_task_id_arr[$row['TASK_NUMBER']]=$row['TASK_NUMBER'];
		
		$team_leader=$poWiseDataArr[$row['PO_NUMBER_ID']]['team_leader'];
		$buyer_name=$poWiseDataArr[$row['PO_NUMBER_ID']]['buyer_name'];
		$dataArr[$team_leader][$buyer_name][$row['TASK_NUMBER']]=$row['TASK_NUMBER'];
		$teamBuyerTaskWisePoArr[$team_leader][$buyer_name][$row['TASK_NUMBER']][$row['PO_NUMBER_ID']]=$row['PO_NUMBER_ID'];
		$callSpanArr[$team_leader][$buyer_name.$row['TASK_NUMBER']]=1;
		
		$key=$team_leader.$buyer_name.$row['TASK_NUMBER'];
		if(($row[PLAN_DATE]=="0000-00-00" || $row[PLAN_DATE]=='') || ($row['ACTUAL_DATE']=="0000-00-00" || $row['ACTUAL_DATE']=='')){
			$tnaDataArr[fail][$key][$row['PO_NUMBER_ID']]=1;
		}
		else{
			$date_dif=(datediff( "d", $row['PLAN_DATE'],$row['ACTUAL_DATE'])-1);
			if($date_dif<0){$tnaDataArr['early'][$key][$row['PO_NUMBER_ID']]=1;}
			elseif($date_dif==0){$tnaDataArr['intime'][$key][$row['PO_NUMBER_ID']]=1;}
			elseif($date_dif>0){$tnaDataArr['late'][$key][$row['PO_NUMBER_ID']]=1;}
		}
		
	}	
	//$tna_task_array=array();
	//$tna_task_id=array();
	//$tna_task_array=$tna_process_task_arr;
	//$tna_task_id=$tna_process_task_id_arr;
	//--------------------------------------------selected task id end;
	
	 
	 
	 
	 
	 
	 
	
	$sql_con="select a.job_no,a.booking_no_prefix_num,b.po_break_down_id from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.is_short=2  and a.entry_form=86";
	
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
	
	
	$width=650;
	
	ob_start();
	
	?>
    
 <div style="width:<? echo $width+20; ?>px" align="left">
    <table width="<? echo $width; ?>">
        <tr><td align="center"><h2><?=$lib_company_arr[$cbo_company_name];?></h2></td></tr>
        <tr><td align="center"><b>TNA Hit Rate Summary</b></td></tr>
        <tr><td align="center"><b>Date: From <?= change_date_format($txt_date_from); ?> To <?= change_date_format($txt_date_to);?></b></td></tr>
    </table>
    
    
    <table width="<? echo $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
    	<thead>
        	<tr>
            	<th width="120">Team Leader</th>
                <th width="100">Buyer Name</th>
                <th width="100">Task Name</th>
                <th width="60">Total PO</th>
                <th width="60">On Time</th>
                <th width="60">On Time %</th>
                <th width="60">Failed</th>
                <th>Failed%</th>
               </tr>
           </thead>
       </table>
     </div>   
    <div style="overflow-y:scroll; max-height:<? echo $maxHight;?>; width:<? echo $width+20; ?>px;" align="left" id="scroll_body">
        <table width="<? echo $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body_">
            <tbody>
            	<? 
				$summaryDataArr=array();
				$grand_total_po=0;
				$grand_ontime=0;
				$grand_faield=0;

				foreach($dataArr as $team_leader_id=>$teamLeaderRow){
					
					?>
                    <tr>
                    	<td width="120" valign="top" rowspan="<?=count($callSpanArr[$team_leader_id])+count($teamLeaderRow);?>"><?= $team_leader_name[$team_leader_id];?></td>
                    <?
					
					$flag=1;
					foreach($teamLeaderRow as $buyer_id=>$buyerRow){
						if($flag==0){echo "<tr>";}
						
						?>
							<td width="100" valign="top" rowspan="<?=count($buyerRow)?>"><?= $buyer_name_arr[$buyer_id];?></td>
						<?
						
						$flag2=1;$i=1;
						foreach($buyerRow as $task_id=>$rows){
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
						$func = "onClick=change_color('tr_".$i."','".$bgcolor."')";
						if($flag2==0){echo "<tr bgcolor='".$bgcolor."' id='tr_".$i."' ".$func."  >";}
						$key=$team_leader_id.$buyer_id.$task_id;
						
						$total_po=count($teamBuyerTaskWisePoArr[$team_leader_id][$buyer_id][$task_id]);
						$ontime=count($tnaDataArr['early'][$key])+count($tnaDataArr['intime'][$key]);
						$faield=count($tnaDataArr['late'][$key])+count($tnaDataArr['fail'][$key]);
						
						$summaryDataArr[totalpo][$team_leader_id][$buyer_id]+=$total_po;
						$summaryDataArr[ontime][$team_leader_id][$buyer_id]+=$ontime;
						$summaryDataArr[faield][$team_leader_id][$buyer_id]+=$faield;
						
						
						
				?>
                            <td width="100" title="<?=$task_id;?>"><?= $tna_task_array[$task_id];?></td>
                			<td width="60" align="center"><?= $total_po;?></td>
                			<td width="60" align="center"><?= $ontime;?></td>
                			<td width="60" align="center"><?= number_format(($ontime/$total_po)*100,2); ?></td>
                			<td width="60" align="center"><?= $faield;?></td>
                			<td align="center"><?= number_format(($faield/$total_po)*100,2); ?></td>
                		</tr>
				
				<? 
						$flag=0;$flag2=0;$i++;		
						}
						?>
						<tr bgcolor="#FFCC66">
                        	<td colspan="2" align="right"><b><?= $buyer_name_arr[$buyer_id];?> Total</b></td>
                        	<td align="center"><?= $summaryDataArr[totalpo][$team_leader_id][$buyer_id];?></td>
                        	<td align="center"><?= $summaryDataArr[ontime][$team_leader_id][$buyer_id];?></td>
                            <td align="center"><? echo number_format(($summaryDataArr[ontime][$team_leader_id][$buyer_id]/$summaryDataArr[totalpo][$team_leader_id][$buyer_id])*100,2);?></td>
                            <td align="center"><?= $summaryDataArr[faield][$team_leader_id][$buyer_id];?></td>
                            <td align="center"><?= number_format(($summaryDataArr[faield][$team_leader_id][$buyer_id]/$summaryDataArr[totalpo][$team_leader_id][$buyer_id])*100,2);?></td>
                        </tr>
						<?
					}
						?>
						<tr bgcolor="#91FFFF">
                        	<td colspan="3" align="right"><b><?= $team_leader_name[$team_leader_id];?> Total</b></td>
                        	<td align="center"><?= array_sum($summaryDataArr['totalpo'][$team_leader_id]);?></td>
                        	<td align="center"><?= array_sum($summaryDataArr['ontime'][$team_leader_id]);?></td>
                            <td align="center"><?= number_format((array_sum($summaryDataArr['ontime'][$team_leader_id])/array_sum($summaryDataArr['totalpo'][$team_leader_id]))*100,2);?></td>
                        	<td align="center"><?= array_sum($summaryDataArr['faield'][$team_leader_id]);?></td>
                        	<td align="center"><?= number_format((array_sum($summaryDataArr['faield'][$team_leader_id])/array_sum($summaryDataArr[totalpo][$team_leader_id]))*100,2);?></td>
                        </tr>
						<?
						
						//Grand Total.............
						$grand_total_po+=array_sum($summaryDataArr['totalpo'][$team_leader_id]);
						$grand_ontime+=array_sum($summaryDataArr['ontime'][$team_leader_id]);
						$grand_faield+=array_sum($summaryDataArr['faield'][$team_leader_id]);
					
				} ?>
                
                    <tfoot>
                        <td colspan="3" align="right"><b>Grand Total</b></td>
                        <td align="center"><?= $grand_total_po;?></td>
                        <td align="center"><?= $grand_ontime;?></td>
                        <td align="center"><?= number_format(($grand_ontime/$grand_total_po)*100,2);?></td>
                        <td align="center"><?= $grand_faield;?></td>
                        <td align="center"><?= number_format(($grand_faield/$grand_total_po)*100,2);?></td>
                    </tfoot>
                
            </tbody>
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




if($action=="show_image")
{
	echo load_html_head_contents("Set Entry","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	//echo "select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1";
	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
	
	?>
    <table>
    <tr>
    <?
    foreach ($data_array as $row)
	{ 
	?>
    <td><img src='../../../<? echo $row[csf('image_location')]; ?>' height='250' width='300' /></td>
    <?
	}
	?>
    </tr>
    </table>
    
    <?
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
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
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
	
	$sql =sql_select("select id, task_name, task_short_name, task_sequence_no from  lib_tna_task where status_active=1 and is_deleted=0 order by task_sequence_no"); 
	//$arr=array(0=>$tna_task_name);
	//echo $sql; die;
	//echo create_list_view("list_view", "Task Name,Task Short Name","200","400","280",0, $sql , "js_set_value", "task_name,task_name", "", 1, "task_name,0", $arr, "task_name,task_short_name", "","setFilterGrid('list_view',-1)","0","",1) ;	
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

if($action == 'set_form_data'){
	list($update_id,$task_id,$issue_status)=explode(',',$data);
	$sql="select ID, JOB_ID, ORDER_ID, TASK_ID, ISSUE_RAISED,ISSUE_CLOSED,ISSUE_STATUS,TASK_TYPE, inserted_by, insert_date, status_active, is_deleted from TNA_TASK_ISSUE_RAISED_CLOSED where id=$update_id and task_id=$task_id and STATUS_ACTIVE=1 and IS_DELETED=0";
	$sql_res=sql_select($sql);
	$row = $sql_res[0];


	$task_arr=return_library_array("select a.TASK_NAME,a.TASK_SHORT_NAME from LIB_TNA_TASK a where a.STATUS_ACTIVE=1 and a.is_deleted=0 and task_type=1  and TASK_NAME=$task_id","TASK_NAME",'TASK_SHORT_NAME');


	$comments_field = ($issue_status==1)?"ISSUE_RAISED":"ISSUE_CLOSED";

	if($issue_status==2){
		$htmlBody = '<tr>\
			<td colspan="4"><b>'.$task_arr[$task_id]." :</b> ".$row['ISSUE_RAISED'].'</td>\
		</tr>\
		<tr>\
			<td>1</td>\
			<td>'.$task_arr[$task_id].'</td>\
			<td align="center">\
				<input type="hidden" id="update_id_'.$task_id.'" value="">\
				<input type="text" id="issue_raised_closed_'.$task_id.'" class="text_boxes" style="width:98%">\
			</td>\
			<td align="center">'.create_drop_down( "cbo_issue_status_".$task_id, '70', $tna_status_arr, "",0, "-- Select --", 2, "",1 ).'</td>\
		</tr>';
		echo "$('#issue_raised_close_list_view tbody').html('".$htmlBody."');\n";
		echo "$('#tna_task_list').val(".$task_id.");\n";
	}
	else{
		if($row['ISSUE_STATUS']==2){
			echo "alert('This issue already closed.');\n";
			exit();
		}
		echo "$('#cbo_issue_status_".$task_id."').val('".$row['ISSUE_STATUS']."');\n";
	}




	echo "$('#issue_raised_closed_".$task_id."').val('".$row[$comments_field]."');\n";

	echo "$('#update_id_".$task_id."').val('".$row['ID']."');\n";

	$permission = $_SESSION['page_permission']; 
	//if($issue_status == 2 && !in_array($user_id,$raised_user_arr)){$permission = '2_2_2_2';}

	echo "set_button_status(1, '".$permission."', 'fnc_tna_isse_rasied_close_save',1);\n";  
	exit();
}


// 




if($action == "save_update_delete_issue"){
	extract($_REQUEST);

	$data = str_replace(["(",")"]," ",$data);

	$dataArr = explode('~~',$data);
	$con = connect();


	if ($operation==0)  // Insert Here
	{
		
		
		$id=return_next_id( "id","TNA_TASK_ISSUE_RAISED_CLOSED", 1 ) ;
		$comments_field = ($issue_status==1)?"ISSUE_RAISED":"ISSUE_CLOSED";
		$field_array="id, job_id, order_id, task_id, $comments_field,ISSUE_STATUS,task_type, inserted_by, insert_date, status_active, is_deleted";

		$task_id_arr=array();
		foreach($dataArr as $dataStr){
			list($task_id,$comments,$status,$update_id)=explode('#**#',$dataStr);

			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",".$job_id.",".$po_id.",".$task_id.",'".$comments."',".$status.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$id++;

			$task_id_arr[$task_id]=$task_id;
		}


		if (is_duplicate_field( "id", "TNA_TASK_ISSUE_RAISED_CLOSED", "ORDER_ID=$po_id and TASK_TYPE=1 and ISSUE_STATUS=1 and STATUS_ACTIVE=1 and IS_DELETED=0 and TASK_ID in(".implode(',',$task_id_arr).")" ) == true)
		{
			echo "11**".$id;
			die;
		}


		//echo "insert into TNA_TASK_ISSUE_RAISED_CLOSED ($field_array) values  $data_array";die;

		$rIDs=sql_insert("TNA_TASK_ISSUE_RAISED_CLOSED",$field_array,$data_array,1);


		if($rIDs)
		{
			oci_commit($con); 
			echo "0**".str_replace("'","",$id);
		}
		else
		{
			oci_rollback($con); 
			echo "5**".str_replace("'","",$id);
		}

	}
	else if ($operation==1)  // Update Here
	{ 
 
		$comments_field = ($issue_status==1)?"ISSUE_RAISED":"ISSUE_CLOSED";

		if($issue_status==2){$status_file="*ISSUE_STATUS*update_date";}

		$field_array=$comments_field."*updated_by".$status_file;
		//echo $field_array;die;

		foreach($dataArr as $dataStr){
			list($task_id,$comments,$status,$update_id)=explode('#**#',$dataStr);
			if($issue_status==2){$status_data="*".$status."*'".$pc_date_time."'";}
			$data_array="'".$comments."'*'".$_SESSION['logic_erp']["user_id"]."'".$status_data;
			$rID=sql_update("TNA_TASK_ISSUE_RAISED_CLOSED",$field_array,$data_array,"id","".$update_id."",1);
		}	
		
		if($rID)
		{
			oci_commit($con); 
			echo "1**".str_replace("'","",$update_id);
		}
		else
		{
			oci_rollback($con); 
			echo "10**".str_replace("'","",$update_id);
		}
		
				
	}
	else if ($operation==2) { //Delete here
		$rID=0;
		if($issue_status==1){
			$field_array="UPDATED_BY*STATUS_ACTIVE*IS_DELETED*UPDATE_DATE";
			foreach($dataArr as $dataStr){
				list($task_id,$comments,$status,$update_id)=explode('#**#',$dataStr);

				if (is_duplicate_field( "id", "TNA_TASK_ISSUE_RAISED_CLOSED", "id=$update_id and STATUS_ACTIVE=1 and IS_DELETED=0 and UPDATE_DATE is not null" ) == true)
				{
					echo "12**".$id;
					die;
				}
	
				$data_array="'".$_SESSION['logic_erp']["user_id"]."'*0*1*'".$pc_date_time."'";;
				$rID=sql_update("TNA_TASK_ISSUE_RAISED_CLOSED",$field_array,$data_array,"id","".$update_id."",1);	
			}
		
		}
		else if($issue_status==2){
			$field_array="UPDATED_BY*ISSUE_CLOSED*ISSUE_STATUS*UPDATE_DATE";
			foreach($dataArr as $dataStr){
				list($task_id,$comments,$status,$update_id)=explode('#**#',$dataStr);

				$data_array="''*''*1*''";;
				$rID=sql_update("TNA_TASK_ISSUE_RAISED_CLOSED",$field_array,$data_array,"id","".$update_id."",1);	
			}
		
		}
 
		
		if($rID)
		{
			oci_commit($con); 
			echo "2**".str_replace("'","",$update_id);
		}
		else
		{
			oci_rollback($con); 
			echo "10**".str_replace("'","",$update_id);
		}
	}

	exit();
}


if($action=='tna_task_date_save_update_delete'){
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$txt_tna_date = date('d-M-y',strtotime($txt_tna_date));

	if ($operation==1)  
	{		
		$con = connect();

		$sql ="SELECT ID,TEMPLATE_ID,JOB_NO,TASK_NUMBER,PO_NUMBER_ID,ACTUAL_START_DATE,ACTUAL_FINISH_DATE,nvl(actual_start_flag,0) as ACTUAL_START_FLAG,nvl(actual_finish_flag,0) as ACTUAL_FINISH_FLAG, TASK_START_DATE,TASK_FINISH_DATE,nvl(PLAN_START_FLAG,0) as PLAN_START_FLAG,nvl(PLAN_FINISH_FLAG,0) as PLAN_FINISH_FLAG FROM tna_process_mst where PO_NUMBER_ID=$po_id and TASK_NUMBER=$task_id and TASK_TYPE=1";
		//echo $sql;die;
		$tna_proess_mst_data_arr=sql_select($sql);

		$id = $tna_proess_mst_data_arr[0]['ID'];
		$htmp_id = $tna_proess_mst_data_arr[0]['TEMPLATE_ID'];
		$htask_id = $tna_proess_mst_data_arr[0]['TASK_NUMBER'];
		$hpo_id = $tna_proess_mst_data_arr[0]['PO_NUMBER_ID'];
		$hjob_id = $tna_proess_mst_data_arr[0]['JOB_NO'];


		$sql_his ="SELECT ID FROM tna_plan_actual_history where PO_NUMBER_ID=$po_id and TASK_NUMBER=$task_id and TASK_TYPE=1";
		 //echo $sql_his;die;
		$tna_proess_his_data_arr=sql_select($sql_his);
		$hmid = $tna_proess_his_data_arr[0]['ID'];


	 
		if($date_type==1){$field_array="task_type*task_start_date*plan_start_flag";}
		else if($date_type==2){$field_array="task_type*task_finish_date*plan_finish_flag";}
		else if($date_type==3){$field_array="task_type*actual_start_date*actual_start_flag";}
		else if($date_type==4){$field_array="task_type*actual_finish_date*actual_finish_flag";}
	
		$data_array="1*'".$txt_tna_date."'*1";
		$rID=sql_update("tna_process_mst",$field_array,$data_array,"id",$id,1);
		if($rID){$flag=1;}else{$flag=0;}

		
		//history process  start-----------------------------------------;
		if($hmid)
		{
			$rID1=sql_update("tna_plan_actual_history",$field_array,$data_array,"id",$hmid,1);
			if($rID1){$flag=1;}else{$flag=0;}
		}
		else
		{
			if($date_type==1){$field=",task_start_date,plan_start_flag";}
			else if($date_type==2){$field=",task_finish_date,plan_finish_flag";}
			else if($date_type==3){$field=",actual_start_date,actual_start_flag";}
			else if($date_type==4){$field=",actual_finish_date,actual_finish_flag";}
			
			$field_array="id, template_id,task_number, job_no, po_number_id,status_active,is_deleted,task_type $field";
			$hid=return_next_id( "id", "tna_plan_actual_history", 1 ) ;

			$data_array="(".$hid.",".$htmp_id.",".$htask_id.",'".$hjob_id."',".$hpo_id.",1,0,1,'".$txt_tna_date."',1)";
			//echo $data_array;die;
			$rID2=sql_insert("tna_plan_actual_history",$field_array,$data_array,1);
			if($rID2){$flag=1;}else{$flag=0;}
			
		}
		//history process end-----------------------------------------;

		
		if($flag == 1)
		{
				oci_commit($con);
				echo "1**".$id."**".$date_type."**".$htask_id."**".$hpo_id."**".change_date_format($txt_tna_date);
				
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

if($action=='tna_task_date_update'){
	if(!in_array($user_id,$closed_user_arr)){exit('You have no permission to edit date.');}
	extract($_REQUEST);
	?>
	<script>
		let fn_tna_task_date_save_update_delete = (operation)=>{
			var data="action=tna_task_date_save_update_delete&txt_tna_date="+$('#txt_tna_date').val()+"&date_type=<?= $date_type;?>&po_id=<?= $po_id;?>&task_id=<?= $task_id;?>&operation="+operation;
			//alert(data)
			//freeze_window(operation);
			http.open("POST","tna_progress_vs_actual_plan_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = ()=>{
				if(http.readyState == 4) 
				{
					$('#update_response_data').val(http.responseText);
					parent.emailwindow.hide();
				}
			}

		}
	</script>
	
	<?

	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);

	$date_type_arr=[1=>"Plan Start Date",2=>"Plan Finish Date",3=>"Actual Start Date",4=>"Actual Finish Date"]
	?>
	<form action="#">
		<table>
			<tr>
				<th align="right" width="98"><b><?= $date_type_arr[$date_type];?></b></th>
				<th>
					<input type="hidden" id="update_response_data">
					<input type="text" name="txt_tna_date" id="txt_tna_date" class="datepicker" value="<?= change_date_format($tna_date);?>" style="width:95%;">
				</th>
			</tr>
			<tr>
				<td colspan="2">
					<?= load_submit_buttons($permission, "fn_tna_task_date_save_update_delete", 1,0,"refresh_data();",1); ?>
				</td>
			</tr>
		</table>
	</form>

	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

<?

exit();
}

if($action=='task_issue_raised_close_popup'){
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);


	$tna_data_res=sql_select("select a.TASK_NAME as ID,b.TASK_NUMBER,b.PO_NUMBER_ID,b.TASK_START_DATE,b.TASK_FINISH_DATE,b.ACTUAL_START_DATE,b.ACTUAL_FINISH_DATE,b.TEMPLATE_ID,a.TASK_SHORT_NAME from LIB_TNA_TASK a,TNA_PROCESS_MST b where a.task_name=b.TASK_NUMBER and b.task_type=1 AND a.task_type = 1 and a.STATUS_ACTIVE=1 and a.is_deleted=0   and b.STATUS_ACTIVE=1 and b.is_deleted=0 and PO_NUMBER_ID=$po_id $whereCon order by a.TASK_SEQUENCE_NO");
	$tna_task_id_arr=array();
	foreach($tna_data_res as $row){
		$task_arr[$row['ID']]=$row['TASK_SHORT_NAME'];
		$template_id=$row['TEMPLATE_ID'];

		$tna_data_arr[$row['TASK_NUMBER']]['TASK_START_DATE']=$row['TASK_START_DATE'];
		$tna_data_arr[$row['TASK_NUMBER']]['TASK_FINISH_DATE']=$row['TASK_FINISH_DATE'];
		$tna_data_arr[$row['TASK_NUMBER']]['ACTUAL_START_DATE']=$row['ACTUAL_START_DATE'];
		$tna_data_arr[$row['TASK_NUMBER']]['ACTUAL_FINISH_DATE']=$row['ACTUAL_FINISH_DATE'];
		$tna_task_id_arr[$row['TASK_NUMBER']]=$row['TASK_NUMBER'];

	}

	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	$company_library=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$booking_arr=return_library_array( "select BOOKING_NO from WO_BOOKING_DTLS where PO_BREAK_DOWN_ID=$po_id and BOOKING_TYPE=1 and STATUS_ACTIVE=1 and IS_DELETED=0", "BOOKING_NO", "BOOKING_NO");

	$booking_qty_arr=return_library_array( "select b.PO_BREAK_DOWN_ID,sum((b.requirment/b.PCS)*c.PLAN_CUT_QNTY) as REQUIRMENT from  wo_pre_cos_fab_co_avg_con_dtls b,WO_PO_COLOR_SIZE_BREAKDOWN c WHERE c.id=b.COLOR_SIZE_TABLE_ID   and b.po_break_down_id=$po_id and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 group by b.po_break_down_id", "PO_BREAK_DOWN_ID", "REQUIRMENT");

	?>
	<script>
		var issue_status=<?=$issue_status;?>;
		let fnc_tna_isse_rasied_close_save=(operation)=>{
			if(issue_status==2 && operation==0){
				alert("Please select raised issue to closed.");return;
			}

			let tna_task_list=$('#tna_task_list').val();
			let tna_task_id_arr=tna_task_list.split(',');
			var dataArr=Array();
			tna_task_id_arr.forEach(async (i) => {
				if($('#issue_raised_closed_' + i).val()){
					dataArr.push(i + '#**#' + $('#issue_raised_closed_' + i).val() + '#**#' + $('#cbo_issue_status_' + i).val() + '#**#' + $('#update_id_' + i).val());
				}
			}); 

			if(dataArr.length <= 0){alert("Please write comments.");return;}

			var dataStr = dataArr.join('~~') ;

			var data="action=save_update_delete_issue&operation="+operation+'&job_id='+<?=$job_id;?>+'&po_id='+<?=$po_id;?>+'&issue_status=<?=$issue_status;?>&data='+dataStr;
			//alert (operation);return;
			//freeze_window(operation);
			
			http.open("POST","tna_progress_vs_actual_plan_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = ()=>{
				
				if(http.readyState == 4) 
				{
					var reponse=http.responseText.split('**');
					
					show_list_view(<?=$po_id;?>,'task_issue_raised_closed_list_view','list_container','tna_progress_vs_actual_plan_controller','');
					set_button_status(0, '<?=$permission;?>', 'fnc_tna_isse_rasied_close_save',1);

					if(reponse[0]==0){alert('Save successfully');}
					else if(reponse[0]==1){alert('Update successfully');}
					else if(reponse[0]==2){alert('Delete successfully');}
					else if(reponse[0]==11){alert('This task issue already raised. You can update now.');}
					else if(reponse[0]==12){alert('This task issue already closed. Closed issue delete not allow.');}


					tna_task_id_arr.forEach(async (i) => {
						if($('#issue_raised_closed_' + i).val()){
							$('#issue_raised_closed_' + i).val('');
						}
					}); 



				}
			}	 
		}

		let get_closed_raised=(str)=>{
			get_php_form_data(str+','+issue_status, 'set_form_data', 'tna_progress_vs_actual_plan_controller' ); 
		}
	
		let fn_tna_date_update = (task_id,po_id,tna_date,date_type)=>{
			var title="TNA Date Update";
			var company = $("#cbo_company_name").val();	
			
			var page_link='tna_progress_vs_actual_plan_controller.php?action=tna_task_date_update&po_id='+po_id+'&task_id='+task_id+'&tna_date='+tna_date+'&date_type='+date_type+'&permission='+'<?=$permission;?>';  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=350px,height=250px,center=1,resize=1,scrolling=0','../../');
			emailwindow.onclose=function()
			{
				var response_data = this.contentDoc.getElementById("update_response_data").value;
				var [status,mst_id,date_type,task_id,po_id,tna_date] = response_data.split('**');
				$('#txt_' + date_type + '_' + po_id + '_' + task_id).html(tna_date).css("color", "red");
			}	
		}


	</script>


	<?
	$sql ="select b.company_name,b.buyer_name,b.job_no,b.style_ref_no,b.gmts_item_id, set_smv, 
	LISTAGG(cast(a.po_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_number,
	LISTAGG(cast(a.po_received_date as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_received_date,
	LISTAGG(cast(a.shipment_date as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as shipment_date,
	LISTAGG(cast(a.EXTENDED_SHIP_DATE as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as EXTENDED_SHIP_DATE,
	SUM(po_quantity*total_set_qnty) as po_qty_pcs from  wo_po_break_down a,wo_po_details_master b where a.id =$po_id and a.job_no_mst=b.job_no group by b.company_name,b.buyer_name,b.job_no,b.style_ref_no,b.gmts_item_id, set_smv";
	$result=sql_select($sql);

	$mod_sql= sql_select("select a.id,a.task_catagory,a.task_name,a.task_short_name,a.task_type,a.completion_percent,a.task_sequence_no,b.task_template_id,b.lead_time ,b.tna_task_id
	from lib_tna_task a,tna_task_template_details b where a.task_name=b.tna_task_id and b.task_type=1 and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 order by a.task_sequence_no asc");
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

	$color=return_library_array( "select a.po_break_down_id ,b.color_name, a.color_number_id from wo_po_color_size_breakdown a, lib_color b where a.color_number_id=b.id and a.po_break_down_id='".$po_id."' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.po_break_down_id ,b.color_name, a.color_number_id order by b.color_name", "color_number_id", "color_name" );

	$imbillishment_cost=return_field_value("rate","wo_pre_cost_embe_cost_dtls","job_no='".$result[0][csf('job_no')]."' and status_active=1 and is_deleted=0","rate");
		$is_imblishment=$imbillishment_cost?"Yes":"No";
	?>

	<table border="1" rules="all" class="rpt_table" width="85%">
    	<?php $buyer_id="";
		foreach($result as $row)
		{
			$buyer_id=$row[csf('buyer_name')];
		?>
    	<tr>
        	<td>Company</td>
            <td><?php  echo $company_library[$row[csf('company_name')]];  ?></td>
            <td>Buyer</td>
            <td><?php  echo $buyer_arr[$row[csf('buyer_name')]];  ?></td>
            <td>Job Number</td>
           	<td>
            	<? echo $row[csf('job_no')];   ?>
            	<input type="hidden" name="jobno" class="text_boxes" ID="jobno" value="<? echo $job_no; ?>" style="width:100px" />
            	<input type="hidden" name="orderid" class="text_boxes" ID="orderid" value="<? echo $po_id; ?>" style="width:100px" />
                <input type="hidden" name="tamplateid" class="text_boxes" ID="tamplateid" value="<? echo $template_id; ?>" style="width:100px" />
            </td>
        </tr>
        <tr>
            <td>Order No</td>
           	<td><b><?php  echo $row[csf('po_number')]; ?></b></td>
            <td>Style Ref.</td>
            <td><?php  echo $row[csf('style_ref_no')];  ?></td>
            <td>Booking Number</td>
            <td><?php echo implode(',',$booking_arr); ?></td>
        </tr>
        <tr>
            <td>Garments Item</td>
            <td><?php  
				//echo $garments_item[$row[csf('gmts_item_id')]]; 
				$gmts_item_arr=array_unique(explode(",",$row[csf('gmts_item_id')]));
				$all_garments="";
				foreach($gmts_item_arr as $gmt_id)
				{
					$all_garments.=$garments_item[$gmt_id].",";
				}
				echo chop($all_garments,","); 
			 ?></td>
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
            <td>Template Lead Time</td>
            <td><b><? echo $lead_time_array[$template_id]; ?></b></td>
        </tr>
        <tr>
            <td>Quantity (PCS)</td>
            <td><b><?php echo $po_qty_pcs=$row[csf('po_qty_pcs')];?></b></td>
            <td>Number of Color</td>
            <td><b><?php echo count($color);  ?></b></td>
			<td>Color Name</td>
            <td rowspan="2"><?php echo implode(', ',$color); ?></td>
        </tr>

        <tr>
            <td>Extended Shipment Date</td>
            <td><?php echo change_date_format($row['EXTENDED_SHIP_DATE']);?></td>
            <td>Fabric Qty</td>
            <td colspan="2"><?= number_format(array_sum($booking_qty_arr),2);?></td>
        </tr>
		
        <?php
		}
		?>
    </table><br>

	
	<table width="100%" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="issue_raised_close_list_view">
		<thead>
			<th width="25">SL</th>
			<th width="150">Task Name</th>
			<th><?=($issue_status==1)?"Issue Raised Comments":"Issue Closed Comments";?></th>
			<th width="80">Status</th>
		</thead>
		<tbody>
		<? 
		$i=1;
		if($issue_status==1){
			foreach($task_arr as $task_id=>$task_name){
				?>
				<tr>
					<td><?=$i;?></td>
					<td><?=$task_name;?></td>
					<td align="center">
						<input type="hidden" id="update_id_<?=$task_id;?>" value="">
						<input type="text" id="issue_raised_closed_<?=$task_id;?>" class="text_boxes" style="width:98%">
					</td>
					<td align="center">
						<? 
							echo create_drop_down( "cbo_issue_status_".$task_id, '70', $tna_status_arr, "",0, "-- Select --", $issue_status, "",1 );
						?>
					</td>
				</tr>
			<?
			$i++;
			}
		}
		?>
		</tbody>
		<tfoot>
			<td colspan="4" align="center" valign="middle" style="padding: 5px;">
				<input type="hidden" id="tna_task_list" value="<?=implode(',',array_keys($task_arr));?>">
				<? //if($issue_status == 2 && !in_array($user_id,$raised_user_arr)){$permission = '2_2_2_2';}?>
				<?= load_submit_buttons($permission, "fnc_tna_isse_rasied_close_save", 0,0,"refresh_data();",1); ?>
				
			</td>
		</tfoot>
	</table>


	<div id="list_container"></div>

	<?
	//$tna_task_id_arr=array(10,31,86,270,32,15,12,80,70,71,48,50,60,72,61,73,84,85,86,101,110);
	$task_short_name_arr=return_library_array( "select TASK_NAME,TASK_SHORT_NAME from LIB_TNA_TASK where  STATUS_ACTIVE=1 order by TASK_SEQUENCE_NO", "TASK_NAME", "TASK_SHORT_NAME" );

	// $tna_sql="select TASK_NUMBER,PO_NUMBER_ID,TASK_START_DATE,TASK_FINISH_DATE,ACTUAL_START_DATE,ACTUAL_FINISH_DATE from TNA_PROCESS_MST where PO_NUMBER_ID=$po_id";
		
	// //echo $tna_sql;die();
	// $tna_sql_result=sql_select($tna_sql);
	// $tna_task_id_arr=array();
	// foreach($tna_sql_result as $tnaRow){
	// 	$tna_data_arr[$tnaRow['TASK_NUMBER']]['TASK_START_DATE']=$tnaRow['TASK_START_DATE'];
	// 	$tna_data_arr[$tnaRow['TASK_NUMBER']]['TASK_FINISH_DATE']=$tnaRow['TASK_FINISH_DATE'];
	// 	$tna_data_arr[$tnaRow['TASK_NUMBER']]['ACTUAL_START_DATE']=$tnaRow['ACTUAL_START_DATE'];
	// 	$tna_data_arr[$tnaRow['TASK_NUMBER']]['ACTUAL_FINISH_DATE']=$tnaRow['ACTUAL_FINISH_DATE'];
	// 	$tna_task_id_arr[$tnaRow['TASK_NUMBER']]=$tnaRow['TASK_NUMBER'];
	// }

	?>

	<br>
	<table border="1" rules="all" class="rpt_table">
		<thead><strong>Show the TNA (Task Wise)</strong></thead>
			<thead>   
				<th>Task Name</th>
				<th>Plan Start Date</th>
				<th>Plan Finish Date</th>
				<th>Actual Start Date</th>
				<th>Actual Finish Date</th>
				<th>Start Delay/ Early By</th>
				<th>Finish Delay/ Early By</th>
			</thead>
		<tbody>
		
		<? 
			foreach($tna_task_id_arr as $task_id){
				
				if($tna_data_arr[$task_id]['ACTUAL_START_DATE']!=''){
					$startDelayEarly=datediff( "d", $tna_data_arr[$task_id]['ACTUAL_START_DATE'],$tna_data_arr[$task_id]['TASK_START_DATE'] );
				}else{
					$startDelayEarly=datediff( "d", date("Y-m-d") , $tna_data_arr[$task_id]['TASK_START_DATE'] );
				}
				
				if($tna_data_arr[$task_id]['ACTUAL_FINISH_DATE']!=''){
					$finishDelayEarly=datediff( "d", $tna_data_arr[$task_id]['ACTUAL_FINISH_DATE'], $tna_data_arr[$task_id]['TASK_FINISH_DATE'] );
				}else{
					$finishDelayEarly=datediff( "d", date("Y-m-d") , $tna_data_arr[$task_id]['TASK_FINISH_DATE'] );
				}
			?>
			<tr>   
				<td title="<?=$task_id;?>"><?=$task_short_name_arr[$task_id];?></td>
				<td id="txt_1_<?=$po_id.'_'.$task_id;?>" align="center" onclick="fn_tna_date_update('<?=$task_id;?>','<?=$po_id;?>','<?=$tna_data_arr[$task_id][TASK_START_DATE];?>',1);"><? if($tna_data_arr[$task_id]['TASK_START_DATE']){ echo change_date_format($tna_data_arr[$task_id][TASK_START_DATE]);}?></td>
				<td id="txt_2_<?=$po_id.'_'.$task_id;?>" align="center" onclick="fn_tna_date_update('<?=$task_id;?>','<?=$po_id;?>','<?=$tna_data_arr[$task_id][TASK_FINISH_DATE];?>',2);"><? if($tna_data_arr[$task_id]['TASK_FINISH_DATE']){ echo change_date_format($tna_data_arr[$task_id][TASK_FINISH_DATE]);}?></td>
				<td id="txt_3_<?=$po_id.'_'.$task_id;?>" align="center" onclick="fn_tna_date_update('<?=$task_id;?>','<?=$po_id;?>','<?=$tna_data_arr[$task_id][ACTUAL_START_DATE];?>',3);"><? if($tna_data_arr[$task_id][ACTUAL_START_DATE]){ echo change_date_format($tna_data_arr[$task_id][ACTUAL_START_DATE]);}?></td>
				<td id="txt_4_<?=$po_id.'_'.$task_id;?>" align="center" onclick="fn_tna_date_update('<?=$task_id;?>','<?=$po_id;?>','<?=$tna_data_arr[$task_id][ACTUAL_FINISH_DATE];?>',4);"><? if($tna_data_arr[$task_id][ACTUAL_FINISH_DATE]){ echo change_date_format($tna_data_arr[$task_id][ACTUAL_FINISH_DATE]);}?></td>
				<td align="center"><?=$startDelayEarly;?></td>
				<td align="center"><?=$finishDelayEarly;?></td>
			</tr>
			<?
				}
			?>
			
		</tbody>
		</table> 
		
		<br>

	<?
		$pro_sql="select a.PO_BREAK_DOWN_ID,a.PRODUCTION_TYPE,a.EMBEL_NAME,a.EMBEL_TYPE,sum(b.production_qnty) as QTY from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.po_break_down_id=$po_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.po_break_down_id,a.production_type,a.EMBEL_NAME,EMBEL_TYPE";
		//echo $pro_sql;
  
		$pro_sql_result=sql_select($pro_sql);
		$pro_data_arr=array();
		foreach($pro_sql_result as $preRow)
		{
			$pro_data_arr[$preRow['PRODUCTION_TYPE']]+=$preRow['QTY'];
			$pro_data_arr[$preRow['EMBEL_TYPE']]+=$preRow['EMBEL_TYPE'];
			$emb_pro_data_arr[$preRow['PRODUCTION_TYPE']]+=$preRow['QTY'];
		}


	  	$exfac_data_arr=return_library_array( "SELECT po_break_down_id, sum(ex_factory_qnty) as EXQTY FROM  pro_ex_factory_mst WHERE po_break_down_id =$po_id and status_active =1 and is_deleted = 0 group by po_break_down_id", "po_break_down_id", "EXQTY");



	?>
	<thead><strong>Show Garments Qty (Task Wise)</strong></thead>
	<table border="1" rules="all" class="rpt_table">
		<thead>   
			<th>Task Name</th>
			<th>Production Qty (PCS)</th>
			<th>Excess/ Balance Qty (PCS)</th>
			<th>Excess/ Balance Qty %</th>
		</thead>
		<tr>    
		         <?
		         $cutting_output=$po_qty_pcs-$pro_data_arr[1];
		        ?>
			<td> Cutting Output</td>
			<td align="right"><?=number_format($pro_data_arr[1],0,"",",");?></td> 
			<td align="right">
			<?=$cutting_output;?>
			</td> 
			<td align="right"><?=number_format(($cutting_output/$po_qty_pcs)*100 ,2);?></td> 
		</tr>
		<tr>   
		       <?
		         $embellishment=$po_qty_pcs-$emb_pro_data_arr[3];
		        ?>
			<td>Embellishment</td>
			<td align="right"><?=number_format($emb_pro_data_arr[3],0,"",",");?></td> 
			<td align="right">
			<?=$embellishment;?>
			</td> 
			<td align="right"><?=number_format(($embellishment/$po_qty_pcs)*100,2);?></td>  
		</tr>
		<tr> 
		      <?
		         $sewing_input=$po_qty_pcs-$pro_data_arr[4];
		        ?>      
			<td>Sewing Input</td>
			<td align="right"><?=number_format($pro_data_arr[4],0,"",",");?></td> 
			<td align="right">
			<?=$sewing_input;?>
			</td> 
			<td align="right"><?=number_format(($sewing_input/$po_qty_pcs)*100,2)?></td> 
		</tr>
		<tr>     
		        <?
		         $sewing_output=$po_qty_pcs-$pro_data_arr[5];
		        ?>  
			<td>Sewing Output</td>
			<td align="right"><?=number_format($pro_data_arr[5],0,"",",");?></td> 
			<td align="right">
			<?=$sewing_output;?>
			</td> 
			<td align="right"><?=number_format(($sewing_output/$po_qty_pcs)*100,2)?></td> 
		</tr>
		<tr>    <?
		        $total_fin_gmt=$po_qty_pcs-$pro_data_arr[8];
		        ?>   
			<td>Total Finished Gmts</td>
			<td align="right"><?=number_format($pro_data_arr[8],0,"",",");?></td> 
			<td align="right">
			<?=$total_fin_gmt;?>
			</td> 
			<td align="right"><?=number_format(($total_fin_gmt/$po_qty_pcs)*100,2)?></td> 
		</tr>  
		        <?
		        $total_ins_gmt=$po_qty_pcs-$insfection_qty;
		        ?>   
			<td>Total Inspection Gmts</td>
			<td align="right"><?=number_format($insfection_qty,0,"",",");?></td> 
			<td align="right">
			<?=$total_ins_gmt;?>
			</td> 
			<td align="right"><?=number_format(($total_ins_gmt/$po_qty_pcs)*100,2);?></td> 
		</tr>
		<tr>      
		    <?
		       $total_ship_out_gmt=$po_qty_pcs-$exfac_data_arr[$po_id];
		    ?>  
			<td>Total Ship Out Gmts</td>
			<td align="right"><?=number_format($exfac_data_arr[$po_id],0,"",",");?></td> 
			<td align="right">
			<?=$total_ship_out_gmt;?>
			</td> 
			<td align="right"><?=number_format(($total_ship_out_gmt/$po_qty_pcs)*100,2)?></td> 
		</tr>
	</table>

	<script> 
		show_list_view(<?=$po_id;?>,'task_issue_raised_closed_list_view','list_container','tna_progress_vs_actual_plan_controller','');
	</script>

	<?

	

	exit();
}

if($action == 'task_issue_raised_closed_list_view'){
	extract($_REQUEST);
	$po_id=$data;

	//$task_arr=return_library_array("select a.ID,a.TASK_SHORT_NAME from LIB_TNA_TASK a,TNA_PROCESS_MST b where a.task_name=b.TASK_NUMBER and b.task_type=1 and a.STATUS_ACTIVE=1 and a.is_deleted=0   and b.STATUS_ACTIVE=1 and b.is_deleted=0 and PO_NUMBER_ID=$po_id $whereCon ","ID",'TASK_SHORT_NAME');

	$task_arr=return_library_array("select a.TASK_NAME,a.TASK_SHORT_NAME from LIB_TNA_TASK a where   a.task_type=1 and a.STATUS_ACTIVE=1 and a.is_deleted=0","TASK_NAME",'TASK_SHORT_NAME');

	$user_arr=return_library_array("select a.ID,a.USER_FULL_NAME from user_passwd a","ID",'USER_FULL_NAME');
		
	$sql="SELECT ID, JOB_ID, ORDER_ID, TASK_ID, ISSUE_RAISED, ISSUE_CLOSED,TASK_TYPE,ISSUE_STATUS, INSERTED_BY, INSERT_DATE, UPDATE_DATE,UPDATED_BY,STATUS_ACTIVE, IS_DELETED FROM TNA_TASK_ISSUE_RAISED_CLOSED where order_id=$po_id and STATUS_ACTIVE=1 and IS_DELETED=0 order by ISSUE_STATUS,INSERT_DATE desc";
	$sqlRes = sql_select($sql);

	?>

		<table width="100%" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="list_view">
			<thead>
				<th width="25">SL</th>
				<th width="150">Task Name</th> 
				<th width="120">Raised By</th>
				<th width="120">Raised Date & Time</th>
				<th>Issue Raised Comments</th>
				<th width="120">Closed By</th>
				<th width="120">Closed Date & Time</th>
				<th>Issue Closed Comments</th>
				<th width="80">Status</th>
			</thead>
			<tbody>
				<?php
				$i=1;
				foreach($sqlRes as $row){
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
					if($row['ISSUE_STATUS']==2){$color="green";}
					else{$color="black";}
				?>
				<tr bgcolor="<?=$bgcolor;?>" onClick="get_closed_raised('<?=$row['ID'].','.$row['TASK_ID'];?>')" style="cursor: pointer;color:<?=$color;?>">
					<td width="25"><?=$i;?></td>
					<td width="150"><?=$task_arr[$row['TASK_ID']];?></td>
					<td><?=$user_arr[$row['INSERTED_BY']];?></td>
					<td><?=$row['INSERT_DATE'];?></td>
					<td><?=$row['ISSUE_RAISED'];?></td>
					<td><?=$user_arr[$row['UPDATED_BY']];?></td>
					<td><?=$row['UPDATE_DATE'];?></td>
					<td><?=$row['ISSUE_CLOSED'];?></td>
					<td width="80"><?=$tna_status_arr[$row['ISSUE_STATUS']];?></td>
				</tr>
				<?php
				$i++;
				}
				?>
			</tbody>
		</table>
		<?
	exit();
}

?>

