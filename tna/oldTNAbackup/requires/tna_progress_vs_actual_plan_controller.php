<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
extract($_REQUEST);
$user_id=$_SESSION['logic_erp']['user_id'];
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


$tna_process_start_date="2014-12-01";
if($db_type==0) $blank_date="0000-00-00"; else $blank_date=""; 

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
	exit();	 
}

if ($action=="load_drop_down_marchant")
{
	echo create_drop_down( "cbo_team_member", 110, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Merchant --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_team_agent", 110, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21))  order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" ); 
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
	
	
	
	
	
	if($tna_task_id!="") $task_cond=" and task_name in ($tna_task_id)"; else $task_cond="";
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
		if($cbo_shipment_status==3) $sql_cond.=" and b.shiping_status=$cbo_shipment_status"; else $sql_cond.=" and b.shiping_status !=3";
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
		if($cbo_team_member>0) $sql_cond.=" and a.dealing_marchant = $cbo_team_member";
		if($cbo_order_status>0) $sql_cond.=" and b.is_confirmed=$cbo_order_status";
		if($cbo_shipment_status==3) $sql_cond.=" and b.shiping_status=$cbo_shipment_status"; else $sql_cond.=" and b.shiping_status !=3";
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
			$sql ="select a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, a.gmts_item_id, b.id, b.po_number, b.po_quantity, c.po_number_id, c.shipment_date, c.po_receive_date,";
			$i=1;
		
			foreach( $tna_task_id as $dval=>$id)    	
			{
				if ($i!=$c) $sql .="max(CASE WHEN CONCAT(c.task_number) = '".$id."' THEN concat(c.actual_start_date,'_',c.actual_finish_date,'_',c.task_start_date,'_',c.task_finish_date,'_',c.id,'_',c.task_number)  END ) as status$id, ";
				else $sql .="max(CASE WHEN CONCAT(c.task_number) = '".$id."' THEN concat(c.actual_start_date,'_',c.actual_finish_date,'_',c.task_start_date,'_',c.task_finish_date,c.id,'_',c.task_number)  END ) as status$id ";
				$i++;
			}
			
			$sql .=" from  wo_po_details_master a, wo_po_break_down b, tna_process_mst c
			where a.job_no=b.job_no_mst and b.id=c.po_number_id $sql_cond and a.status_active=1 and b.status_active=1 and c.status_active=1  and b.po_quantity>0 
			group by a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, a.gmts_item_id, b.id, b.po_number, b.po_quantity, c.po_number_id, c.shipment_date, c.po_receive_date 
			order by c.shipment_date,b.id,a.job_no"; 
		}
		else
		{
			$sql ="select a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, a.gmts_item_id, b.id, b.po_number, b.po_quantity, c.po_number_id, c.shipment_date, c.po_receive_date,";
			$i=1;
			foreach( $tna_task_id as $dval=>$id)    	
			{
				if ($i!=$c) $sql .="max(CASE WHEN c.task_number = '".$id."' THEN c.actual_start_date || '_' || c.actual_finish_date || '_' || c.task_start_date || '_' || c.task_finish_date || '_' || c.id  || '_' || c.task_number  END ) as status$id, ";
				
				else $sql .="max(CASE WHEN c.task_number = '".$id."' THEN c.actual_start_date || '_' || c.actual_finish_date || '_' || c.task_start_date || '_' || c.task_finish_date || '_' || c.id  || '_' || c.task_number  END ) as status$id ";
				
				$i++;
			}
			$sql .=" from  wo_po_details_master a, wo_po_break_down b, tna_process_mst c
			where a.job_no=b.job_no_mst and b.id=c.po_number_id $sql_cond and a.status_active=1 and b.status_active=1 and c.status_active=1  and b.po_quantity>0 
			group by a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, a.gmts_item_id, b.id, b.po_number, b.po_quantity, c.po_number_id, c.shipment_date, c.po_receive_date 
			order by c.shipment_date,b.id,a.job_no"; 
		}
	}
	else
	{
		if($db_type==0)
		{
			$sql ="select a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, a.gmts_item_id, b.id, b.po_number, b.po_quantity, c.po_number_id, c.shipment_date, c.po_receive_date,";
			$i=1;
		
			foreach( $tna_task_id as $dval=>$id)    	
			{
				if ($i!=$c) $sql .="max(CASE WHEN CONCAT(c.task_number) = '".$id."' THEN concat(c.actual_start_date,'_',c.actual_finish_date,'_',c.task_start_date,'_',c.task_finish_date,'_',c.id,'_',c.task_number)  END ) as status$id, ";
				else $sql .="max(CASE WHEN CONCAT(c.task_number) = '".$id."' THEN concat(c.actual_start_date,'_',c.actual_finish_date,'_',c.task_start_date,'_',c.task_finish_date,c.id,'_',c.task_number)  END ) as status$id ";
				$i++;
			}
			
			$sql .=" from  wo_po_details_master a, wo_po_break_down b, tna_process_mst c
			where a.job_no=b.job_no_mst and b.id=c.po_number_id and a.status_active=1 and b.status_active=1 and c.status_active=1  and b.po_quantity>0  and b.id in(". implode(',',$country_po_id)."
			group by a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, a.gmts_item_id, b.id, b.po_number, b.po_quantity, c.shipment_date, c.po_number_id, c.po_receive_date 
			order by c.shipment_date,b.id,a.job_no"; 
		}
		else
		{
			$sql ="select a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, a.gmts_item_id, b.id, b.po_number, b.po_quantity, c.po_number_id, c.shipment_date, c.po_receive_date,";
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
			
			$sql .="group by a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, a.gmts_item_id, b.id, b.po_number, b.po_quantity, c.po_number_id, c.shipment_date, c.po_receive_date 
			order by c.shipment_date,b.id,a.job_no"; 
		}
	}
	
	
	  //echo $sql;
	
	
	$job_image=return_library_array("select master_tble_id,image_location from common_photo_library","master_tble_id",'image_location');
	
	$sql_member = sql_select("SELECT team_member_name,id FROM lib_mkt_team_member_info WHERE is_deleted = 0 and status_active=1");
	foreach( $sql_member as  $row ) 
	{	
		$team_member_arr[$row[csf('id')]]=$row[csf('team_member_name')];
	}
	
	$sql_buyer = sql_select("SELECT buyer_name,id FROM  lib_buyer WHERE is_deleted = 0 and status_active=1 order by id asc");
	foreach( $sql_buyer as  $row ) 
	{	
		$buyer_name_arr[$row[csf('id')]]=$row[csf('buyer_name')];
	}
	
	$data_sql= sql_select($sql);
	
	$width=(count($tna_task_id)*240)+1050;
	
	ob_start();
	
	?>
    <div style="width:<? echo $width+20; ?>px" align="left">
    <table width="<? echo $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
    	<thead>
        	<tr>
            	<th width="40" rowspan="2">SL</th>
                <th width="80" rowspan="2">Merchant<br>Contact No</th>
                <th width="70" rowspan="2">Buyer Name</th>
                <th width="60" rowspan="2">Job No.</th>
                <th width="60" rowspan="2">Image</th>
                <th width="120" rowspan="2">Style Ref.</th>
                <th width="120" rowspan="2">Item</th>
                <th width="50" rowspan="2">SMV</th>  
                <th width="120" rowspan="2">PO Number</th>
                <th width="100" rowspan="2">PO Qty.</th>
                <th width="80" rowspan="2">PO Rcv. Date</th>
                <th width="80" rowspan="2">Shipment Date</th>
                <th width="60" rowspan="2">PO Lead Time</th>
                <?
					$i=0;
					
					foreach($tna_task_array as $key)
					{
						$i++;
						//if(count($tna_task_array)==$i) echo '<th width="160" colspan="2">'. $key.'</th>'; else echo '<th width="160" colspan="2">'.$key.'</th>';
						echo '<th width="240" colspan="3">'. $key.'</th>'; 
					}
					echo '</tr><tr>';
					
					$i=0;
					
					foreach($tna_task_array as $key)
					{
						$i++;
						//if(count($tna_task_array)==$i) echo '<th width="80">Plan Finish</th><th width="80"> Actual Finish</th>'; else echo '<th width="80">Actual Finish </th><th width="80"> Finish</th>';
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
					
					?>
					<tr bgcolor="<? echo $bgcolor;?>" style="vertical-align:middle" height="25" onClick="change_color('tr_<? echo $h;?>','<? echo $bgcolor;?>')" id="tr_<? echo $h; ?>">
						<td width="40" align="center"><? echo $kid++;?></td>
						<td width="80" style="word-break:break-all;"><p><? echo $team_member_arr[$row[csf('dealing_marchant')]]; ?></p></td>
						<td width="70" style="word-break:break-all;"><p><? echo $buyer_name_arr[$row[csf('buyer_name')]]; ?></p></td>
						<td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
						<td width="60" title="" style="cursor:pointer;" onClick="openmypage_image('requires/tna_progress_vs_actual_plan_controller.php?action=show_image&job_no=<? echo $row[csf('job_no')]; ?>','Image View')"><img src="../<? echo $image_path.$job_image[$row[csf('job_no')]];?>" width="50" height="30"  /></td>
						<td width="120" title="<? echo $row[csf('job_no')]; ?>"><div style="width:115px; word-wrap:break-word;"><? echo $row[csf('style_ref_no')]; ?></div></td>
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
						<td width="100" align="right"><? echo number_format($row[csf('po_quantity')],2); $tot_po_qty+=$row[csf('po_quantity')]; ?> </td>
						<td width="80" align="center"><? if($row[csf('po_receive_date')]!="" && $row[csf('po_receive_date')]!="0000-00-00") echo change_date_format($row[csf('po_receive_date')]); ?></td>
						<td width="80" align="center"><? if($row[csf('shipment_date')]!="" && $row[csf('shipment_date')]!="0000-00-00") echo change_date_format($row[csf('shipment_date')]); ?></td>
						<? 
						$po_lead_time=datediff( "d", date("Y-m-d",strtotime(change_date_format($row[csf('po_receive_date')]))), date("Y-m-d",strtotime(change_date_format($row[csf('shipment_date')]))) );
						?>
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
										$bg_color='bgcolor="#FF0000"';
										$summary_data[$vid]["due"]++;
									}
									else if($date_dif>=0)
									{
										$diff_text="In-hand";
										$bg_color='bgcolor="#FFCC33"';
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
										$bg_color='bgcolor="#359AFF"';
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
										$bg_color='bgcolor="#00BB00"';
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
								echo '<td width="80"  align="center">'.($start_date== "" || $start_date=="0000-00-00" ? "<span style='color:#FF0000'> N/A </span>" : change_date_format($start_date)).'</td><td width="80"  align="center">'.($end_data== ""  || $end_data=="0000-00-00"? "" : change_date_format($end_data)).' </td><td  width="80" align="center" '.$bg_color .' >'.$date_dif." ".$diff_text . '</td>';
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
                <th width="80">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="118">&nbsp;</th>
                <th width="118">&nbsp;</th>
                <th width="50">&nbsp;</th>  
                <th width="120">Total</th>
                <th width="100" id="total_po_qty" align="right"><? echo number_format($tot_po_qty,2);?></th>
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


if($action=="show_image")
{
	echo load_html_head_contents("Set Entry","../../", 1, 1, $unicode);
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
    <td><img src='../../<? echo $row[csf('image_location')]; ?>' height='250' width='300' /></td>
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
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
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


?>

