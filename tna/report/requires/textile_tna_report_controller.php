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



if($db_type==0) $blank_date="0000-00-00"; else $blank_date=""; 

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
	die; 	 
}
if($action=="set_print_button")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=3 and report_id=180 and is_deleted=0 and status_active=1");
	echo "print_report_button_setting('$print_report_format');\n"; 
die;
}

$lead_time=return_library_array("select task_template_id,lead_time from tna_task_template_details where task_type=2 group by task_template_id,lead_time","task_template_id","lead_time");
$buyer_short_name_arr = return_library_array("SELECT short_name,id FROM  lib_buyer WHERE is_deleted = 0 and status_active=1 order by id asc","id","short_name");
$buyer_name = return_library_array("SELECT id,buyer_name FROM  lib_buyer WHERE is_deleted = 0 and status_active=1 order by id asc","id","buyer_name");
 

$cbo_company_id = str_replace("'","",$cbo_company_name);
$tna_process_type=return_field_value("tna_process_type"," variable_order_tracking","variable_list=31 and company_name='".$cbo_company_id."'"); 

if($action=="generate_tna_report")
{
	//if(str_replace("'","",$cbo_company_id)!=0){$where_con=" and a.company_name = $cbo_company_id";}
	if(str_replace("'","",$cbo_buyer_name)!=0){$where_con.=" and b.customer_buyer = $cbo_buyer_name";}
	//if(str_replace("'","",$cbo_team_member)!=0){$where_con.=" and a.dealing_marchant = $cbo_team_member";}

	//echo $where_con;die;
	
	if(str_replace("'","",$cbo_search_type)==1){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.delivery_date between $txt_date_from and $txt_date_to";
	}
	else if(str_replace("'","",$cbo_search_type)==3){
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
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.booking_date between $txt_date_from and $txt_date_to";
	}
	
	
	$txt_job_no=str_replace("'","",$txt_booking_no);
	if($txt_job_no=="") $txt_job_no=""; else $txt_job_no=" and b.job_no like('%$txt_job_no')";
	
	$txt_booking_id=str_replace("'","",$txt_booking_id);
	if($txt_booking_id=="") $txt_order_no=""; else $txt_order_no=" and b.sales_booking_no like('%$txt_booking_id')";
	
	
	$txt_file_no=str_replace("'","",$txt_file_no);
	if($txt_file_no=="") $file_cond=""; else $file_cond=" and b.file_no ='$txt_file_no'";
	$txt_int_ref_no=str_replace("'","",$txt_int_ref_no);
	if($txt_int_ref_no=="") $ref_cond=""; else $ref_cond=" and b.grouping ='$txt_int_ref_no'";
	
	$txt_style_ref_no=str_replace("'","",$txt_style_ref_no);
	if($txt_style_ref_no=="") $txt_style_ref_no=""; else $txt_style_ref_no=" and a.style_ref_no ='$txt_style_ref_no'";
	//**txt_date_from*txt_date_to*txt_job_no cbo_buyer_name
	

	$actual_manual_update_task_arr=return_library_array("select task_id,task_id from tna_manual_permission where is_actual_manual=1  and company_id=$cbo_company_id","task_id","task_id");
	$plan_manual_update_task_arr=return_library_array("select task_id,task_id from tna_manual_permission where is_plan_manual=1  and company_id=$cbo_company_id","task_id","task_id");

	$lib_color_arr=return_library_array("select ID,COLOR_NAME from lib_color where STATUS_ACTIVE=1 AND IS_DELETED=0","ID","COLOR_NAME");

	
	
	
	//------------------------------------------------------------- fabric_sales_order_mst
	
	$mod_sql= sql_select("select a.id,a.task_catagory,a.task_name,a.task_short_name,a.task_type,a.completion_percent,b.task_template_id,b.lead_time 
	from lib_tna_task a,tna_task_template_details b where a.task_name=b.tna_task_id and b.task_type=2 and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 order by a.task_sequence_no asc");
	
	$tna_task_array=array();$tna_task_id=array();$tna_task_cat=array();
	$tna_task_name_arr=array();$tast_tmp_id_arr=array();$lead_time_array=array();
	foreach ($mod_sql as $row)
	{
		
		
		$tna_task_name_array[$row[csf("id")]] =$tna_task_name[$row[csf("task_name")]];
		$tna_task_cat[$row[csf("id")]]=$row[csf("task_catagory")];
		$tna_task_name_arr[$row[csf("id")]]=$row[csf("task_name")];
		$lead_time_array[$row[csf("task_template_id")]]=$row[csf("lead_time")];
		$tast_tmp_id_arr[$row[csf("task_template_id")]][$row[csf("tna_task_id")]]=$row[csf("tna_task_id")];


		$tna_task_id[$row[csf("task_name")]]=$row[csf("task_name")];
		$tna_task_array[$row[csf("task_name")]] =$row[csf("task_short_name")];


	}
	 
	$c=count($tna_task_id);
	if($db_type==0)
	{
		$sql ="select b.style_ref_no,b.season,b.sales_booking_no,b.buyer_id,b.within_group,a.po_number_id, a.job_no, a.shipment_date,max(b.delivery_date) as pub_shipment_date, a.template_id, a.po_receive_date,b.insert_date,";
		$i=1;
	
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c) $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id, ";
			else $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id ";
			$i++;
		}
		
		$sql .=" from  tna_process_mst a, fabric_sales_order_mst b where a.po_number_id=b.id  and b.status_active=1  and a.task_type=2 and b.company_id=$cbo_company_id $txt_job_no $txt_order_no $date_range  group by b.style_ref_no,b.season,b.sales_booking_no,b.buyer_id,b.within_group,a.po_number_id,a.job_no,a.template_id,a.shipment_date,b.insert_date order by a.shipment_date,a.po_number_id,a.job_no"; 
	}
	else
	{
		$sql ="select c.FABRIC_DESC,c.COLOR_TYPE_ID,c.COLOR_ID,b.style_ref_no,b.season,b.sales_booking_no,b.customer_buyer as buyer_id,b.within_group,a.po_number_id, a.job_no, max(a.shipment_date) as shipment_date,max(b.delivery_date) as pub_shipment_date,a.template_id, max(a.po_receive_date) as po_receive_date,b.insert_date,";
		$i=1; 
		
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c) $sql .="max(CASE WHEN a.task_number = '".$id."' THEN a.actual_start_date || '_' || a.actual_finish_date || '_' || a.task_start_date || '_' || a.task_finish_date ||'_' || a.notice_date_start || '_' || a.notice_date_end || '_' || a.remarks || '_' || a.id || '_' || a.task_number || '_' || a.plan_start_flag || '_' || a.plan_finish_flag  || '_' || a.ACTUAL_START_FLAG || '_' || a.ACTUAL_FINISH_FLAG   END ) as status$id, ";
			
			else $sql .="max(CASE WHEN a.task_number = '".$id."' THEN a.actual_start_date || '_' || a.actual_finish_date || '_' || a.task_start_date || '_' || a.task_finish_date || '_' || a.notice_date_start || '_' || a.notice_date_end || '_' || a.remarks || '_' || a.id || '_' || a.task_number || '_' || a.plan_start_flag || '_' || a.plan_finish_flag  || '_' || a.ACTUAL_START_FLAG || '_' || a.ACTUAL_FINISH_FLAG   END ) as status$id ";
			
			$i++;
		}
		// fabric_sales_order_mst
		$sql .=" from  tna_process_mst a, fabric_sales_order_mst b,FABRIC_SALES_ORDER_DTLS c where a.job_no= b.JOB_NO and b.id=c.mst_id and c.id=a.po_number_id  and b.status_active=1 and c.status_active=1  and a.task_type=2 and b.company_id=$cbo_company_id $where_con $txt_job_no $txt_order_no $date_range  group by c.FABRIC_DESC,c.COLOR_TYPE_ID,c.COLOR_ID,b.style_ref_no,b.season,b.sales_booking_no,b.customer_buyer,b.within_group,a.po_number_id,a.job_no,a.template_id,a.shipment_date,b.insert_date order by a.shipment_date,a.po_number_id,a.job_no"; 
	}

	// echo $sql;die;
	    
		
	$data_sql= sql_select($sql);
	
	foreach($data_sql as $row){
		$jobIdArr[$row[csf('po_number_id')]]=$row[csf('po_number_id')];
		$bookingArr[$row[csf('sales_booking_no')]]=$row[csf('sales_booking_no')];
	}
	
	$po_buyer_id_arr=return_library_array("select a.job_no,b.buyer_id po_buyer  from fabric_sales_order_mst a,wo_booking_mst b where a.sales_booking_no=b.booking_no and a.id in(".implode(',',$jobIdArr).")","job_no","po_buyer");

	//print_r($po_buyer_id_arr);die;

	
	
	if($db_type==0)
	{	
		$date_diff="(DATEDIFF(b.SHIPMENT_DATE, b.PO_RECEIVE_DATE))";
	}
	else
	{
		$date_diff="(to_date(b.SHIPMENT_DATE, 'dd-MM-yy')- to_date(b.PO_RECEIVE_DATE, 'dd-MM-yy'))";
	}	
	
	
	
	$textile_tna_process_base=return_field_value("textile_tna_process_base"," variable_order_tracking"," company_name=".$cbo_company_id." and variable_list=62"); 
	
	
	if($textile_tna_process_base==1){
	//no process
	}
	else{
		$booking_sql= "select  B.PO_NUMBER_ID,B.TEMPLATE_ID, min$date_diff date_diff from tna_process_mst b where b.task_type=2 and b.job_no like('%".str_replace("'","",$txt_booking_no)."') group by b.po_number_id,b.template_id ";
	}
	  //echo $booking_sql;die;
	$booking_sql_result= sql_select($booking_sql);
	foreach($booking_sql_result as $row){
		$template_id_arr[$row[PO_NUMBER_ID]]=$row[TEMPLATE_ID];
		$template_lead_time_arr[$row[PO_NUMBER_ID]]=$row[TEMPLATE_ID];
	}
	 
	
	
	$width=(count($tna_task_id)*161)+590;

	ob_start();
	?>
   <div style="margin:0 1%;">
        <span style="background:#FF0000; padding:0 6px; border-radius:9px; cursor:pointer;" title="Red">&nbsp;</span>&nbsp; Target Date Over. &nbsp;&nbsp;
        <span style="background:#2A9FFF; padding:0 6px; border-radius:9px; cursor:pointer;" title="Blue">&nbsp;</span>&nbsp; Done in late. &nbsp;&nbsp;
        <span style="background:#FFFF00; padding:0 6px; border-radius:9px; cursor:pointer;" title="Yellow">&nbsp;</span>&nbsp; Reminder.
        
        <span style="background:#0000FF; padding:0 6px; border-radius:9px; cursor:pointer;" title="Royal Blue">&nbsp;</span>&nbsp; Manual Update Plan.
        
    </div>    
    <div style="width:<? echo $width+480; ?>px" align="left">
    <table width="<? echo $width+420; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
    	<thead>
        	<tr>
            	<th width="40" rowspan="2">SL</th>
                <th rowspan="2" width="80">PO Buyer</th>
                <th rowspan="2" width="100">Style</th>
                <th rowspan="2" width="80">Season</th>
                <th rowspan="2" width="120">Booking No</th>
                <th rowspan="2">Sales Order</th>
				<th width="100" rowspan="2">Fabric Color</th>
				<th width="100" rowspan="2">Color Type</th>
                <th width="100" rowspan="2">Delivery Date</th>
                <th width="60" rowspan="2">Receive Date</th>
                <th width="90" rowspan="2">Status</th>
                <?
					$i=0;
					foreach($tna_task_id as $task_name=>$task_name)
					{
						$i++;
						if(count($tna_task_id)==$i) echo '<th colspan="2" title="'.$task_name.'='.$tna_task_name[$task_name].'">'. $tna_task_array[$task_name].'</th>'; else echo '<th colspan="2" title="'.$task_name.'='.$tna_task_name[$task_name].'">'.$tna_task_array[$task_name].'</th>';
					}
					echo '</tr><tr>';
					
					$i=0;
					
					foreach($tna_task_id as $task_name=>$task_name)
					{
						$i++;
						if(count($tna_task_id)==$i) echo '<th width="80" title="plan_start_date=(delivery_date-deadline)-(execution_days-1)">Start</th><th width="80"> Finish</th>'; else echo '<th width="80" title="plan_start_date=(delivery_date-deadline)-(execution_days-1)">Start</th><th width="80"> Finish</th>';
					}
					echo '</tr>';
					 
				?>
                </thead>
                </table>
        </div>
         
    <div style="overflow-y:scroll; max-height:360px; width:<? echo $width+450; ?>px;" align="left" id="scroll_body">
        <table width="<? echo $width+420; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
       
    <?
	$tid=0;
	$i=1;
	$count=0;
	$kid=1;
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
			$bgcolor=($h%2==0)?"#E9F3FF":"#FFFFFF";	
							
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onClick="change_color('tr_<? echo $h;?>','<? echo $bgcolor;?>')" id="tr_<? echo $h; ?>">
				<td width="40" rowspan="3" align="center"><? echo $kid++;?></td>
				<td rowspan="3" width="80"><?
					echo $buyer_name[$row[csf('buyer_id')]]; 
					/*if($row[csf('within_group')]==1){
						echo $buyer_name[$po_buyer_id_arr[$row[csf('job_no')]]]; 
					}
					else
					{
						echo $buyer_name[$row[csf('buyer_id')]]; 
					}*/
					?>
					</td>
					<td rowspan="3" width="100"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					<td rowspan="3" width="80"><p><? echo $row[csf('season')]; ?></p></td>
					<td rowspan="3" width="120" align="center"><? echo $row[csf('sales_booking_no')]; ?></td>

					<td rowspan="3" align="center" title="<? echo $row[csf('FABRIC_DESC')]; ?>"><? echo $row[csf('job_no')]; ?></td>

					<td rowspan="3" width="100" align="center"><?= $lib_color_arr[$row['COLOR_ID']];?></td>
					<td rowspan="3" width="100" align="center"><?= $color_type[$row['COLOR_TYPE_ID']];?></td>

					<? 
					if($tna_process_type==1)
					{
						$lead_time="Template L/T: ".$lead_time_array[$row[csf('template_id')]];
					}
					else
					{
						$lead_time="Lead Time: ".($row[csf('template_id')]+1);
					}
					$po_lead_time=datediff( "d", date("Y-m-d",strtotime(change_date_format($row[csf('po_receive_date')]))), date("Y-m-d",strtotime(change_date_format($row[csf('shipment_date')]))) );

					?>
					
					
				<td width="100" rowspan="3" title="<? echo " Receive Date: ".change_date_format($row[csf('po_receive_date')]); echo ",\n Delivery Date: ".$row[csf('pub_shipment_date')] .",\n Insert Date: ".$row[csf('insert_date')]; echo " ,\n PO Template ID:".$template_id_arr[$row[csf('po_number_id')]];?>"><? echo change_date_format($row[csf('shipment_date')])."<br>"." ".$lead_time."<br>"." FSO L/T:".$po_lead_time;  ?>
				<br>PO  L/T:<? echo $template_lead_time_arr[$row[csf('sales_booking_no')]]+1;?> 
				<a href="javascript:openTemplate(<? echo $template_id_arr[$row[csf('po_number_id')]]//$row[csf('template_id')];?>)">View</a>
				
				
				</td>
				<td width="60" rowspan="3" align="center"><? echo date("d-m-Y",strtotime($row[csf('po_receive_date')]));?></td>
				<td width="90">Plan</td>
			<?


					$tast_id_arr=$tast_tmp_id_arr[$row[csf('template_id')]];
					$i=0;
					foreach($tna_task_id as $vid=>$key)
					{
						$i++;
					
					if ( $new_approval_arr[$row[csf('job_no')]][$key]=="") $new_data=explode("_",$row[csf('status').$key]); 
					else $new_data=explode("_",$new_approval_arr[$row[csf('job_no')]][$key]);
					if($new_data[7]!="") $function="onclick='update_tna_process(1,$new_data[7],".$row[csf('po_number_id')].")'"; else $function="";
					
					
					if($plan_manual_update_task_arr[$vid]==''){$function="";}
					
					if($new_data[9]==1){$psc=" style='color:#0000FF'";}else{$psc="";}
					if($new_data[10]==1){$pfc=" style='color:#0000FF'";}else{$pfc="";}
					
					
					if(in_array($vid,$tast_id_arr))
					{
						if(count($tna_task_id)==$i)
							echo '<td align="center" '.$psc.' width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? "<span style='color:#FF0000'> N/A </span>" : change_date_format($new_data[2])).'</td><td align="center" '.$pfc.' '.$function.'> '.($new_data[3]== "N/A"  || $new_data[3]=="0000-00-00"? "" : change_date_format($new_data[3])).'</td>';
							
							else
							echo '<td align="center" '.$psc.' width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? "<span style='color:#FF0000'> N/A </span>" : change_date_format($new_data[2])).'</td><td  align="center" '.$pfc.'width="80" '.$function.'> '.($new_data[3]== ""  || $new_data[3]=="0000-00-00"? "<span style='color:#FF0000'> N/A </span>" : change_date_format($new_data[3])).'</td>';
					}
					else
					{
						if(count($tna_task_id)==$i)
							echo '<td align="center" '.$psc.' width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? "N/A" : change_date_format($new_data[2])).'</td><td align="center" '.$pfc.' '.$function.'> '.($new_data[3]== "N/A"  || $new_data[3]=="0000-00-00"? "" : change_date_format($new_data[3])).'</td>';
							
							else
							echo '<td align="center" '.$psc.' width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? "N/A" : change_date_format($new_data[2])).'</td><td align="center" '.$pfc.' width="80" '.$function.'> '.($new_data[3]== ""  || $new_data[3]=="0000-00-00"? "N/A" : change_date_format($new_data[3])).'</td>';
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
					
					if($actual_manual_update_task_arr[$vid]==''){$function="";}
					
					
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
					
					
					if($new_data[11]==1){$asc=" style='color:#0000FF'";}else{$asc="";}
					if($new_data[12]==1){$afc=" style='color:#0000FF'";}else{$afc="";}
					
					
					$idd=$row[csf('job_no')]."".$row[csf('po_number_id')]."".$key;
					if(count($tna_task_id)==$i)
						echo '<td align="center" '.$asc.' title="Click Here to Edit Date" id="'.$idd.'1" '.$function.' width="80" bgcolor="'.$bgcolor.'">'.($new_data[0]== "" || $new_data[0]=="0000-00-00" ? "" : change_date_format($new_data[0])).'</td><td align="center"'.$afc.' id="'.$idd.'2" title="Click Here to Edit Date" '.$function.' bgcolor="'.$bgcolor1.'" title="'.$new_data[6].'">'.($new_data[1]== "" || $new_data[1]=="0000-00-00" ? "" : change_date_format($new_data[1])).'</td>';
						
					else
						echo '<td align="center" '.$asc.' id="'.$idd.'1" title="Click Here to Edit Date"  '.$function.' width="80" bgcolor="'.$bgcolor.'">'.($new_data[0]== "" || $new_data[0]=="0000-00-00" ? "" : change_date_format($new_data[0])).'</td><td align="center"'.$afc.' id="'.$idd.'2" title="Click Here to Edit Date" '.$function.' width="80" bgcolor="'.$bgcolor1.'" title="'.$new_data[6].'">'.($new_data[1]== "" || $new_data[1]=="0000-00-00" ? "" : change_date_format($new_data[1])).'</td>';
					
					}
				echo '</tr>'; 
				
				echo '<tr><td width="90">Delay/Early By</td>';
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
								$start_diff=-abs($start_diff1-1);
							}
							else
							{
								$start_diff=-abs($start_diff1-1);
							}
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
								$finish_diff=-abs($finish_diff1-1);
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
    <div style="width:<? echo $width+420; ?>px;" align="left">
         <table width="100%" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <tfoot>
                <th colspan="<? echo (count($tna_task_id)*2)+13;?>">&nbsp;</th>
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
 	echo "****$filename";
	exit();
}


if($action=="generate_tna_report_color_size_wise")
{

	//if(str_replace("'","",$cbo_company_id)!=0){$where_con=" and a.company_name = $cbo_company_id";}
	//if(str_replace("'","",$cbo_buyer_name)!=0){$where_con.=" and a.buyer_name = $cbo_buyer_name";}
	//if(str_replace("'","",$cbo_team_member)!=0){$where_con.=" and a.dealing_marchant = $cbo_team_member";}
	
	if(str_replace("'","",$cbo_search_type)==1){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.delivery_date between $txt_date_from and $txt_date_to";
	}
	else if(str_replace("'","",$cbo_search_type)==3){
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
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.booking_date between $txt_date_from and $txt_date_to";
	}
	
	
	$txt_job_no=str_replace("'","",$txt_booking_no);
	if($txt_job_no=="") $txt_job_no=""; else $txt_job_no=" and b.job_no like('%$txt_job_no')";
	
	$txt_booking_id=str_replace("'","",$txt_booking_id);
	if($txt_booking_id=="") $txt_order_no=""; else $txt_order_no=" and b.sales_booking_no like('%$txt_booking_id')";
	
	
	$txt_file_no=str_replace("'","",$txt_file_no);
	if($txt_file_no=="") $file_cond=""; else $file_cond=" and b.file_no ='$txt_file_no'";
	$txt_int_ref_no=str_replace("'","",$txt_int_ref_no);
	if($txt_int_ref_no=="") $ref_cond=""; else $ref_cond=" and b.grouping ='$txt_int_ref_no'";
	
	$txt_style_ref_no=str_replace("'","",$txt_style_ref_no);
	if($txt_style_ref_no=="") $txt_style_ref_no=""; else $txt_style_ref_no=" and a.style_ref_no ='$txt_style_ref_no'";
	//**txt_date_from*txt_date_to*txt_job_no
	

	$actual_manual_update_task_arr=return_library_array("select task_id,task_id from tna_manual_permission where is_actual_manual=1  and company_id=$cbo_company_id","task_id","task_id");
	$plan_manual_update_task_arr=return_library_array("select task_id,task_id from tna_manual_permission where is_plan_manual=1  and company_id=$cbo_company_id","task_id","task_id");
	
	
	
	//-------------------------------------------------------------
	
	$mod_sql= sql_select("select a.id,a.task_catagory,a.task_name,a.task_short_name,a.task_type,a.completion_percent,b.task_template_id,b.lead_time 
	from lib_tna_task a,tna_task_template_details b where a.task_name=b.tna_task_id and A.task_type=2 and b.task_type=2 and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 order by a.task_sequence_no asc");
	
	$tna_task_array=array();$tna_task_id=array();$tna_task_cat=array();
	$tna_task_name_arr=array();$tast_tmp_id_arr=array();$lead_time_array=array();
	foreach ($mod_sql as $row)
	{
		$tna_task_id[$row[csf("task_name")]]=$row[csf("task_name")];
		$tna_task_array[$row[csf("task_name")]] =$row[csf("task_short_name")];
		$tna_task_name_array[$row[csf("task_name")]] =$tna_task_name[$row[csf("task_name")]];
		$tna_task_cat[$row[csf("task_name")]]=$row[csf("task_catagory")];
		//$tna_task_name_arr[$row[csf("id")]]=$row[csf("task_name")];
		
		$lead_time_array[$row[csf("task_template_id")]]=$row[csf("lead_time")];
		$tast_tmp_id_arr[$row[csf("task_template_id")]][$row[csf("tna_task_id")]]=$row[csf("tna_task_id")];
	}
	
	
	
	
	
	
	
	$c=count($tna_task_id);
	if($db_type==0)
	{
		$sql ="select b.style_ref_no,b.season,b.sales_booking_no,b.buyer_id,b.within_group,a.po_number_id, a.job_no, a.shipment_date,max(b.delivery_date) as pub_shipment_date, a.template_id, a.po_receive_date,b.insert_date,";
		$i=1;
	
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c) $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id, ";
			else $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id ";
			$i++;
		}
		
		$sql .=" from  tna_process_mst a, fabric_sales_order_mst b where a.po_number_id=b.id  and b.status_active=1  and a.task_type=2 and b.company_id=$cbo_company_id $txt_job_no $txt_order_no $date_range  group by b.style_ref_no,b.season,b.sales_booking_no,b.buyer_id,b.within_group,a.po_number_id,a.job_no,a.template_id,a.shipment_date,b.insert_date order by a.shipment_date,a.po_number_id,a.job_no"; 
	}
	else
	{
		$sql ="select a.PO_NUMBER_ID, a.JOB_NO,a.template_id,b.SALES_BOOKING_NO, b.insert_date,b.SHIP_MODE,c.COLOR_ID,b.STYLE_REF_NO,b.season,b.BUYER_ID,b.WITHIN_GROUP, b.PO_BUYER,c.BODY_PART_ID,c.COLOR_TYPE_ID,c.FABRIC_DESC,c.GSM_WEIGHT,c.DIA,c.FINISH_QTY,b.PO_COMPANY_ID,b.PO_JOB_NO,b.ID AS FSO_ID,c.ID AS FSO_DTLS_ID,c.GREY_QTY,
		
		max(a.shipment_date) as shipment_date,
		max(a.po_receive_date) as po_receive_date,
		max(b.delivery_date) as pub_shipment_date,
		max(b.BOOKING_DATE) as BOOKING_DATE,
		max(b.DELIVERY_DATE) as DELIVERY_DATE,
		max(b.BOOKING_APPROVAL_DATE) as FSO_RECEIVE_DATE,
		";
		$i=1; 
		
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c) $sql .="max(CASE WHEN a.task_number = '".$id."' THEN a.actual_start_date || '_' || a.actual_finish_date || '_' || a.task_start_date || '_' || a.task_finish_date ||'_' || a.notice_date_start || '_' || a.notice_date_end || '_' || a.remarks || '_' || a.id || '_' || a.task_number || '_' || a.plan_start_flag || '_' || a.plan_finish_flag  || '_' || a.ACTUAL_START_FLAG || '_' || a.ACTUAL_FINISH_FLAG   END ) as status$id, ";
			
			else $sql .="max(CASE WHEN a.task_number = '".$id."' THEN a.actual_start_date || '_' || a.actual_finish_date || '_' || a.task_start_date || '_' || a.task_finish_date || '_' || a.notice_date_start || '_' || a.notice_date_end || '_' || a.remarks || '_' || a.id || '_' || a.task_number || '_' || a.plan_start_flag || '_' || a.plan_finish_flag  || '_' || a.ACTUAL_START_FLAG || '_' || a.ACTUAL_FINISH_FLAG   END ) as status$id ";
			
			$i++;
		}

		$sql .=" from  tna_process_mst a, fabric_sales_order_mst b,FABRIC_SALES_ORDER_DTLS c where a.job_no= b.JOB_NO and b.id=c.mst_id and c.id=a.po_number_id  and b.status_active=1 and c.status_active=1  and a.task_type=2 and b.company_id=$cbo_company_id $txt_job_no $txt_order_no $date_range  group by b.style_ref_no,b.season,b.sales_booking_no,b.buyer_id,b.PO_BUYER,b.within_group,b.SHIP_MODE,a.po_number_id,a.job_no,a.template_id,a.shipment_date,b.insert_date,c.COLOR_ID,c.BODY_PART_ID,c.COLOR_TYPE_ID,c.FABRIC_DESC,c.GSM_WEIGHT,c.DIA,c.FINISH_QTY,c.GREY_QTY,b.PO_COMPANY_ID,b.PO_JOB_NO,b.id,c.id order by a.job_no,c.COLOR_ID"; 
	}
	    
	//echo $sql;	
	$data_sql= sql_select($sql);
	$po_job_arr=array();
	foreach($data_sql as $row){
		//$jobIdArr[$row[csf('po_number_id')]]=$row[csf('po_number_id')];
		//$bookingArr[$row[csf('sales_booking_no')]]=$row[csf('sales_booking_no')];
		$po_job_arr[$row[PO_JOB_NO]]=$row[PO_JOB_NO];
		$rowspan[$row[JOB_NO]][$row[FSO_DTLS_ID]]=1;
	}
	

	$job_sql="select a.STYLE_REF_NO,a.DEALING_MARCHANT,a.TEAM_LEADER,a.FACTORY_MARCHANT,a.JOB_NO,b.PO_NUMBER,B.ID as PO_ID from WO_PO_DETAILS_MASTER a,WO_PO_BREAK_DOWN b where a.JOB_NO=b.JOB_NO_MST and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 ".where_con_using_array($po_job_arr,1,'a.JOB_NO')."";
	//echo $job_sql;die;
	$job_sql_result= sql_select($job_sql);
	foreach($job_sql_result as $row){
		$job_data[PO_ID][$row[JOB_NO]][]=$row[PO_ID];
		$job_data[PO_NUMBER][$row[JOB_NO]][]=$row[PO_NUMBER];
		$job_data[DEALING_MARCHANT][$row[JOB_NO]]=$row[DEALING_MARCHANT];
		$job_data[TEAM_LEADER][$row[JOB_NO]]=$row[TEAM_LEADER];
		$job_data[FACTORY_MARCHANT][$row[JOB_NO]]=$row[FACTORY_MARCHANT];
	}

	
	
	if($db_type==0)
	{	
		$date_diff="(DATEDIFF(b.SHIPMENT_DATE, b.PO_RECEIVE_DATE))";
	}
	else
	{
		$date_diff="(to_date(b.SHIPMENT_DATE, 'dd-MM-yy')- to_date(b.PO_RECEIVE_DATE, 'dd-MM-yy'))";
	}	
	
	
	
	$booking_sql= "select  B.PO_NUMBER_ID,B.TEMPLATE_ID, min$date_diff date_diff from tna_process_mst b where b.task_type=2 and b.job_no like('%".str_replace("'","",$txt_booking_no)."') group by b.po_number_id,b.template_id ";
	  //echo $booking_sql;die;
	$booking_sql_result= sql_select($booking_sql);
	foreach($booking_sql_result as $row){
		$template_id_arr[$row[PO_NUMBER_ID]]=$row[TEMPLATE_ID];
		$template_lead_time_arr[$row[PO_NUMBER_ID]]=$row[TEMPLATE_ID];
	}
	 

	$lib_color = return_library_array("SELECT id,COLOR_NAME FROM  LIB_COLOR WHERE is_deleted = 0 and status_active=1 order by id asc","id","COLOR_NAME");
	
	$lib_company = return_library_array("SELECT id,COMPANY_SHORT_NAME FROM  LIB_COMPANY WHERE is_deleted = 0 and status_active=1 order by id asc","id","COMPANY_SHORT_NAME");
	
	
	$team_leader_name = return_library_array("select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id","team_leader_name");
	
	$team_member_name = return_library_array("SELECT team_member_name,id FROM lib_mkt_team_member_info WHERE is_deleted = 0 and status_active=1 order by id asc","id","team_member_name");
 
 
	
	$width=(count($tna_task_id)*161)+1600;

	ob_start();
	?>
   <div style="margin:0 1%;">
        <span style="background:#FF0000; padding:0 6px; border-radius:9px; cursor:pointer;" title="Red">&nbsp;</span>&nbsp; Target Date Over. &nbsp;&nbsp;
        <span style="background:#2A9FFF; padding:0 6px; border-radius:9px; cursor:pointer;" title="Blue">&nbsp;</span>&nbsp; Done in late. &nbsp;&nbsp;
        <span style="background:#FFFF00; padding:0 6px; border-radius:9px; cursor:pointer;" title="Yellow">&nbsp;</span>&nbsp; Reminder.
        
        <span style="background:#0000FF; padding:0 6px; border-radius:9px; cursor:pointer;" title="Royal Blue">&nbsp;</span>&nbsp; Manual Update Plan.
        
    </div>    
    <div style="width:<? echo $width+20; ?>px" align="left">
    <table width="<? echo $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
    	<thead>
        	<tr>
            	<th width="40" rowspan="2">SL</th>
                <th rowspan="2" width="80">GMT - Order Owner</th>
                <th rowspan="2" width="100">#Team Leader #Dealing Merchant #Factory Merchant</th>
                <th rowspan="2" width="100">Style Ref</th>
                <th rowspan="2" width="100">PO Buyer Name</th>
                <th rowspan="2" width="100">Buyer PO Number</th>
                <th rowspan="2" width="100">Fabric Booking No</th>
                <th rowspan="2" width="70">Fabric Booking Date</th>
               	<th width="100" rowspan="2">FSO No</th>
               	<th width="70" rowspan="2">FSO Received Date</th>
                <th width="70" rowspan="2">Delivery Date</th>
                <th width="60" rowspan="2">Ship Mode</th>
                <th width="100" rowspan="2">Fabric Colour</th>
                <th rowspan="2">Fabric Description ( Body Part ,Color Type, Fabric Description,GSM,Dia)</th>
                <th width="60" rowspan="2">Finsh & Gray Req.</th>
                <th width="90" rowspan="2">Status</th>
                <?
					$i=0;
					foreach($tna_task_id as $task_name=>$key)
					{
						$i++;
						if(count($tna_task_id)==$i) echo '<th colspan="2" title="'.$task_name.'='.$tna_task_name_array[$task_name].'">'. $tna_task_array[$task_name].'</th>'; else echo '<th colspan="2" title="'.$task_name.'='.$tna_task_name_array[$task_name].'">'.$tna_task_array[$task_name].'</th>';
					}
					echo '</tr><tr>';
					
					$i=0;
					
					foreach($tna_task_id as $key)
					{
						$i++;
						if(count($tna_task_id)==$i) echo '<th width="80" title="plan_start_date=(delivery_date-deadline)-(execution_days-1)">Start</th><th width="80"> Finish</th>'; else echo '<th width="80" title="plan_start_date=(delivery_date-deadline)-(execution_days-1)">Start</th><th width="80"> Finish</th>';
					}
					echo '</tr>';
					 
				?>
                </thead>
                </table>
         </div>
         
         
    <div style="overflow-y:scroll; max-height:360px; width:<?=$width+20; ?>px;" align="left" id="scroll_body">
    <table width="<? echo $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
       
    <?
	
	$tid=0;
	$i=1;
	$count=0;
	$kid=1;
	$h=0;
	$tot_po_qty=0;
	$flag=0;
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
			$bgcolor=($h%2==0)?"#E9F3FF":"#FFFFFF";	
							
		
				$row[PO_BUYER]=($row[WITHIN_GROUP]==1)?$row[PO_BUYER]:$row[BUYER_ID];
				
                     
				if($tna_process_type==1)
				{
					$lead_time="Template L/T: ".$lead_time_array[$row[csf('template_id')]];
				}
				else
				{
					$lead_time="Lead Time: ".($row[csf('template_id')]+1);
				}
				$po_lead_time=datediff( "d", date("Y-m-d",strtotime(change_date_format($row[csf('po_receive_date')]))), date("Y-m-d",strtotime(change_date_format($row[csf('shipment_date')]))) );

		//Total Color Size count for rowspan			
		 $tcs=(count($rowspan[$row[JOB_NO]])*3);
		?>
        		
         <? if($flag==0){ ?>
                
                <tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onClick="change_color('tr_<? echo $h;?>','<? echo $bgcolor;?>')" id="tr_<? echo $h; ?>">
                     
                    <td width="40" rowspan="<?=$tcs; ?>" align="center"><? echo $kid++;?></td>
                    <td rowspan="<?=$tcs; ?>" width="80"><p><?=$lib_company[$row[PO_COMPANY_ID]];?></p></td>
                    <td rowspan="<?=$tcs; ?>" width="100" valign="middle"><p>
                    	<?=$team_member_name[$job_data[DEALING_MARCHANT][$row[PO_JOB_NO]]];?>,</br>
                    	<?=$team_leader_name[$job_data[TEAM_LEADER][$row[PO_JOB_NO]]];?>,</br>
                    	<?=$team_member_name[$job_data[FACTORY_MARCHANT][$row[PO_JOB_NO]]];?></p>
                    </td>
                    <td rowspan="<?=$tcs; ?>" width="100"><p><?=$row[STYLE_REF_NO];?></p></td>
                    <td rowspan="<?=$tcs; ?>" width="100"><p><?=$buyer_name[$row[PO_BUYER]];?></p></td>
                    <td rowspan="<?=$tcs; ?>" width="100" title="<? echo " Receive Date: ".change_date_format($row[csf('po_receive_date')]); echo ",\n Delivery Date: ".$row[csf('pub_shipment_date')] .",\n Insert Date: ".$row[csf('insert_date')]; echo " ,\n PO Template ID:".$template_id_arr[$row[csf('po_number_id')]];?>" valign="middle"><p>
						<?= implode(', ',$job_data[PO_NUMBER][$row[PO_JOB_NO]]);?><br>
                        <a href="javascript:order_dtls('<?= implode(', ',$job_data[PO_ID][$row[PO_JOB_NO]]);?>')">View</a></p>
                    </td>                     
                    <td rowspan="<?=$tcs; ?>" width="100"><a href="javascript:generate_booking_report('<?=$row[SALES_BOOKING_NO];?>')"><?=$row[SALES_BOOKING_NO];?></a></td>
                    
                    <td rowspan="<?=$tcs; ?>" width="70" align="center"><?=change_date_format($row[FSO_RECEIVE_DATE]);?></td>
                    <td rowspan="<?=$tcs; ?>" width="100" align="center"><p><a href="javascript:generate_fso_print('<?=$cbo_company_id.'**'.$row[SALES_BOOKING_NO].'*'.$row[csf(job_no)].'*Fabric Sales Order Entry*'.$row[FSO_ID];?>')"><?=$row[csf(job_no)];?></a></p></td>
                    <td rowspan="<?=$tcs; ?>" width="70" align="center"><?=change_date_format($row[BOOKING_DATE]);?></td>
                    <td rowspan="<?=$tcs;?>" width="70" align="center"><p>
						<?=change_date_format($row[DELIVERY_DATE]);?><br>
                        <?= $lead_time."<br>"." FSO L/T:".$po_lead_time; ?><br>
                        PO  L/T:<?= $template_lead_time_arr[$row[csf('sales_booking_no')]]+1;?> 
                    	<a href="javascript:openTemplate(<?= $row[csf('template_id')];?>)">View</a>
                        </p>
                    </td>
                    <td rowspan="<?=$tcs; ?>" width="60" align="center"><p><?=$shipment_mode[$row[SHIP_MODE]];?> </p></td>
                    
                     <?
					}
					else{
						?>
                       <tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onClick="change_color('tr_<? echo $h;?>','<? echo $bgcolor;?>')" id="tr_<? echo $h; ?>">

					<? 
					}
					$flag++;
					if(count($rowspan[$row[JOB_NO]])==$flag){$flag=0;}
					 ?> 
                     
                     
                    <td rowspan="3" width="100" align="center" title="<?=$row[PO_NUMBER_ID];?>"><p><?=$lib_color[$row[COLOR_ID]];?></p></td>
                    
                    <td rowspan="3" align="center" ><p><?=$body_part[$row[BODY_PART_ID]].','.$color_type[$row[COLOR_TYPE_ID]].','.$row[FABRIC_DESC].','.$row[GSM_WEIGHT].','.$row[DIA];?></p></td>
                    
                    <td width="60" rowspan="3" align="right"><p>
					Finish:<?=number_format($row[FINISH_QTY],2);?><br>
					Gray:<?=number_format($row[GREY_QTY],2);?></p></td>
                    
                    <td width="90">Plan</td>
                <?
					 $tast_id_arr=$tast_tmp_id_arr[$row[csf('template_id')]];
					 $i=0;
					 foreach($tna_task_id as $vid=>$key)
					 {
						 $i++;
						
						if ( $new_approval_arr[$row[csf('job_no')]][$key]=="") $new_data=explode("_",$row[csf('status').$key]); 
						else $new_data=explode("_",$new_approval_arr[$row[csf('job_no')]][$key]);
						if($new_data[7]!="") $function="onclick='update_tna_process(1,$new_data[7],".$row[csf('po_number_id')].")'"; else $function="";
						
						
						if($plan_manual_update_task_arr[$vid]==''){$function="";}
						
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
						
						if($actual_manual_update_task_arr[$vid]==''){$function="";}
						
						
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
						
						
						if($new_data[11]==1){$asc=" style='color:#0000FF'";}else{$asc="";}
						if($new_data[12]==1){$afc=" style='color:#0000FF'";}else{$afc="";}
						
						
						$idd=$row[csf('job_no')]."".$row[csf('po_number_id')]."".$key;
						if(count($tna_task_id)==$i)
							echo '<td align="center"'.$asc.' title="Click Here to Edit Date" id="'.$idd.'1" '.$function.' width="80" bgcolor="'.$bgcolor.'">'.($new_data[0]== "" || $new_data[0]=="0000-00-00" ? "" : change_date_format($new_data[0])).'</td><td align="center"'.$afc.' id="'.$idd.'2" title="Click Here to Edit Date" '.$function.' bgcolor="'.$bgcolor1.'" title="'.$new_data[6].'">'.($new_data[1]== "" || $new_data[1]=="0000-00-00" ? "" : change_date_format($new_data[1])).'</td>';
							
						else
							echo '<td align="center"'.$asc.' id="'.$idd.'1" title="Click Here to Edit Date"  '.$function.' width="80" bgcolor="'.$bgcolor.'">'.($new_data[0]== "" || $new_data[0]=="0000-00-00" ? "" : change_date_format($new_data[0])).'</td><td align="center"'.$afc.' id="'.$idd.'2" title="Click Here to Edit Date" '.$function.' width="80" bgcolor="'.$bgcolor1.'" title="'.$new_data[6].'">'.($new_data[1]== "" || $new_data[1]=="0000-00-00" ? "" : change_date_format($new_data[1])).'</td>';
						
					 }
					echo '</tr>'; 
					
					echo '<tr><td width="90">Delay/Early By</td>';
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
									$start_diff=-abs($start_diff1-1);
								}
								else
								{
									$start_diff=-abs($start_diff1-1);
								}
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
									$finish_diff=-abs($finish_diff1-1);
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
    <div style="width:<?=$width+20; ?>px;" align="left">
         <table width="100%" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <tfoot>
                <th colspan="<? echo (count($tna_task_id)*2)+13;?>">&nbsp;</th>
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
 	echo "****$filename";
	exit();
}



if($action=="generate_tna_report_job_wise")
{

	//if(str_replace("'","",$cbo_company_id)!=0){$where_con=" and a.company_name = $cbo_company_id";}
	//if(str_replace("'","",$cbo_buyer_name)!=0){$where_con.=" and a.buyer_name = $cbo_buyer_name";}
	//if(str_replace("'","",$cbo_team_member)!=0){$where_con.=" and a.dealing_marchant = $cbo_team_member";}
	
	if(str_replace("'","",$cbo_search_type)==1){
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.delivery_date between $txt_date_from and $txt_date_to";
	}
	else if(str_replace("'","",$cbo_search_type)==3){
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
		if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.booking_date between $txt_date_from and $txt_date_to";
	}
	
	
	$txt_job_no=str_replace("'","",$txt_booking_no);
	if($txt_job_no=="") $txt_job_no=""; else $txt_job_no=" and b.job_no like('%$txt_job_no')";
	
	$txt_booking_id=str_replace("'","",$txt_booking_id);
	if($txt_booking_id=="") $txt_order_no=""; else $txt_order_no=" and b.sales_booking_no like('%$txt_booking_id')";
	
	
	$txt_file_no=str_replace("'","",$txt_file_no);
	if($txt_file_no=="") $file_cond=""; else $file_cond=" and b.file_no ='$txt_file_no'";
	$txt_int_ref_no=str_replace("'","",$txt_int_ref_no);
	if($txt_int_ref_no=="") $ref_cond=""; else $ref_cond=" and b.grouping ='$txt_int_ref_no'";
	
	$txt_style_ref_no=str_replace("'","",$txt_style_ref_no);
	if($txt_style_ref_no=="") $txt_style_ref_no=""; else $txt_style_ref_no=" and a.style_ref_no ='$txt_style_ref_no'";
	//**txt_date_from*txt_date_to*txt_job_no
	

	$actual_manual_update_task_arr=return_library_array("select task_id,task_id from tna_manual_permission where is_actual_manual=1  and company_id=$cbo_company_id","task_id","task_id");
	$plan_manual_update_task_arr=return_library_array("select task_id,task_id from tna_manual_permission where is_plan_manual=1  and company_id=$cbo_company_id","task_id","task_id");
	
	
	
	//-------------------------------------------------------------
	
	$mod_sql= sql_select("select a.id,a.task_catagory,a.task_name,a.task_short_name,a.task_type,a.completion_percent,b.task_template_id,b.lead_time 
	from lib_tna_task a,tna_task_template_details b where a.task_name=b.tna_task_id and b.task_type=2 and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 order by a.task_sequence_no asc");
	
	$tna_task_array=array();$tna_task_id=array();$tna_task_cat=array();
	$tna_task_name_arr=array();$tast_tmp_id_arr=array();$lead_time_array=array();
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
	
	
	
	
	
	
	
	$c=count($tna_task_id);
	if($db_type==0)
	{
		$sql ="select b.style_ref_no,b.season,b.sales_booking_no,b.buyer_id,b.within_group,a.po_number_id, a.job_no, a.shipment_date,max(b.delivery_date) as pub_shipment_date, a.template_id, a.po_receive_date,b.insert_date,";
		$i=1;
	
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c) $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id, ";
			else $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id ";
			$i++;
		}
		
		$sql .=" from  tna_process_mst a, fabric_sales_order_mst b where a.po_number_id=b.id  and b.status_active=1  and a.task_type=2 and b.company_id=$cbo_company_id $txt_job_no $txt_order_no $date_range  group by b.style_ref_no,b.season,b.sales_booking_no,b.buyer_id,b.within_group,a.po_number_id,a.job_no,a.template_id,a.shipment_date,b.insert_date order by a.shipment_date,a.po_number_id,a.job_no"; 
	}
	else
	{
		$sql ="select b.style_ref_no,b.season,b.sales_booking_no,b.buyer_id,b.within_group,
		
		LISTAGG(cast(a.po_number_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as po_number_id, 
		LISTAGG(cast(a.template_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as template_id, 		
		
		a.job_no, max(a.shipment_date) as shipment_date,max(b.delivery_date) as pub_shipment_date, max(a.po_receive_date) as po_receive_date,b.insert_date,";
		$i=1; 
		
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c) $sql .="max(CASE WHEN a.task_number = '".$id."' THEN a.actual_start_date || '_' || a.actual_finish_date || '_' || a.task_start_date || '_' || a.task_finish_date ||'_' || a.notice_date_start || '_' || a.notice_date_end || '_' || a.remarks || '_' || a.id || '_' || a.task_number || '_' || a.plan_start_flag || '_' || a.plan_finish_flag  || '_' || a.ACTUAL_START_FLAG || '_' || a.ACTUAL_FINISH_FLAG   END ) as status$id, ";
			
			else $sql .="max(CASE WHEN a.task_number = '".$id."' THEN a.actual_start_date || '_' || a.actual_finish_date || '_' || a.task_start_date || '_' || a.task_finish_date || '_' || a.notice_date_start || '_' || a.notice_date_end || '_' || a.remarks || '_' || a.id || '_' || a.task_number || '_' || a.plan_start_flag || '_' || a.plan_finish_flag  || '_' || a.ACTUAL_START_FLAG || '_' || a.ACTUAL_FINISH_FLAG   END ) as status$id ";
			
			$i++;
		}

		$sql .=" from  tna_process_mst a, fabric_sales_order_mst b,FABRIC_SALES_ORDER_DTLS c where a.job_no= b.JOB_NO and b.id=c.mst_id and c.id=a.po_number_id  and b.status_active=1 and c.status_active=1  and a.task_type=2 and b.company_id=$cbo_company_id $txt_job_no $txt_order_no $date_range  group by b.style_ref_no,b.season,b.sales_booking_no,b.buyer_id,b.within_group,a.job_no,a.shipment_date,b.insert_date order by a.shipment_date,a.job_no"; 
	}
	    
	// echo $sql;die;
		
	$data_sql= sql_select($sql);
	
	foreach($data_sql as $row){
		$jobIdArr[$row[csf('po_number_id')]]=$row[csf('po_number_id')];
		$bookingArr[$row[csf('sales_booking_no')]]=$row[csf('sales_booking_no')];
	}
	
	$po_buyer_id_arr=return_library_array("select a.job_no,b.buyer_id po_buyer  from fabric_sales_order_mst a,wo_booking_mst b where a.sales_booking_no=b.booking_no and a.id in(".implode(',',$jobIdArr).")","job_no","po_buyer");

	
	
	if($db_type==0)
	{	
		$date_diff="(DATEDIFF(b.SHIPMENT_DATE, b.PO_RECEIVE_DATE))";
	}
	else
	{
		$date_diff="(to_date(b.SHIPMENT_DATE, 'dd-MM-yy')- to_date(b.PO_RECEIVE_DATE, 'dd-MM-yy'))";
	}	
	
	
	
	$textile_tna_process_base=return_field_value("textile_tna_process_base"," variable_order_tracking"," company_name=".$cbo_company_id." and variable_list=62"); 
	
	
	if($textile_tna_process_base==1){
	//no process
	}
	else{
		$booking_sql= "select  B.PO_NUMBER_ID,B.TEMPLATE_ID, min$date_diff date_diff from tna_process_mst b where b.task_type=2 and b.job_no like('%".str_replace("'","",$txt_booking_no)."') group by b.po_number_id,b.template_id ";
	}
	  //echo $booking_sql;die;
	$booking_sql_result= sql_select($booking_sql);
	foreach($booking_sql_result as $row){
		$template_id_arr[$row[PO_NUMBER_ID]]=$row[TEMPLATE_ID];
		$template_lead_time_arr[$row[PO_NUMBER_ID]]=$row[TEMPLATE_ID];
	}
	 
	
	
	$width=(count($tna_task_id)*161)+390;

	ob_start();
	?>
   <div style="margin:0 1%;">
        <span style="background:#FF0000; padding:0 6px; border-radius:9px; cursor:pointer;" title="Red">&nbsp;</span>&nbsp; Target Date Over. &nbsp;&nbsp;
        <span style="background:#2A9FFF; padding:0 6px; border-radius:9px; cursor:pointer;" title="Blue">&nbsp;</span>&nbsp; Done in late. &nbsp;&nbsp;
        <span style="background:#FFFF00; padding:0 6px; border-radius:9px; cursor:pointer;" title="Yellow">&nbsp;</span>&nbsp; Reminder.
        
        <span style="background:#0000FF; padding:0 6px; border-radius:9px; cursor:pointer;" title="Royal Blue">&nbsp;</span>&nbsp; Manual Update Plan.
        
    </div>    
    <div style="width:<? echo $width+480; ?>px" align="left">
    <table width="<? echo $width+420; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
    	<thead>
        	<tr>
            	<th width="40" rowspan="2">SL</th>
                <th rowspan="2" width="80">PO Buyer</th>
                <th rowspan="2" width="100">Style</th>
                <th rowspan="2" width="80">Season</th>
                <th rowspan="2" width="120">Booking No</th>
                <th rowspan="2">Sales Order</th>
                <th width="100" rowspan="2">Delivery Date</th>
                <th width="60" rowspan="2">Receive Date</th>
                <th width="90" rowspan="2">Status</th>
                <?
					$i=0;
					foreach($tna_task_array as $task_name=>$key)
					{
						$i++;
						if(count($tna_task_array)==$i) echo '<th colspan="2" title="'.$tna_task_name_arr[$task_name].'='.$tna_task_name_array[$task_name].'">'. $key.'</th>'; else echo '<th colspan="2" title="'.$tna_task_name_arr[$task_name].'='.$tna_task_name_array[$task_name].'">'.$key.'</th>';
					}
					echo '</tr><tr>';
					
					$i=0;
					
					foreach($tna_task_array as $key)
					{
						$i++;
						if(count($tna_task_array)==$i) echo '<th width="80" title="plan_start_date=(delivery_date-deadline)-(execution_days-1)">Start</th><th width="80"> Finish</th>'; else echo '<th width="80" title="plan_start_date=(delivery_date-deadline)-(execution_days-1)">Start</th><th width="80"> Finish</th>';
					}
					echo '</tr>';
					 
				?>
                </thead>
                </table>
         </div>
         
         
        	<div style="overflow-y:scroll; max-height:360px; width:<? echo $width+450; ?>px;" align="left" id="scroll_body">
          	<table width="<? echo $width+420; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
       
    <?
	
	$tid=0;
	$i=1;
	$count=0;
	$kid=1;
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
			$bgcolor=($h%2==0)?"#E9F3FF":"#FFFFFF";
			$row[csf('template_id')]=implode(',',array_unique(explode(',',$row[csf('template_id')])));
							
		?>
        		<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onClick="change_color('tr_<? echo $h;?>','<? echo $bgcolor;?>')" id="tr_<? echo $h; ?>">
                    <td width="40" rowspan="3" align="center"><? echo $kid++;?></td>
                    <td rowspan="3" width="80"><?
						echo $buyer_name[$row[csf('buyer_id')]]; 
					 	?>
                     </td>
                     <td rowspan="3" width="100"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                     <td rowspan="3" width="80"><p><? echo $row[csf('season')]; ?></p></td>
                     <td rowspan="3" width="120" align="center"><? echo $row[csf('sales_booking_no')]; ?></td>
                     <td rowspan="3" align="center"><? echo $row[csf('job_no')]; ?></td>
                     <? 
					 	if($tna_process_type==1)
						{
							$lead_time="Template L/T: ".$lead_time_array[$row[csf('template_id')]];
						}
						else
						{
							$lead_time="Lead Time: ".($row[csf('template_id')]+1);
						}
						$po_lead_time=datediff( "d", date("Y-m-d",strtotime(change_date_format($row[csf('po_receive_date')]))), date("Y-m-d",strtotime(change_date_format($row[csf('shipment_date')]))) );

					//print_r($lead_time_array);die;
							
					
					 ?>
                     
                     
                    <td width="100" rowspan="3" title="<? echo " Receive Date: ".change_date_format($row[csf('po_receive_date')]); echo ",\n Delivery Date: ".$row[csf('pub_shipment_date')] .",\n Insert Date: ".$row[csf('insert_date')]; echo " ,\n PO Template ID:".$row[csf('template_id')];?>"><? echo change_date_format($row[csf('shipment_date')])."<br>"." ".$lead_time."<br>"." FSO L/T:".$po_lead_time;  ?>
                    <br>PO  L/T:<? echo $template_lead_time_arr[$row[csf('sales_booking_no')]]+1;?> 
                    <a href="javascript:openTemplate(<? echo $row[csf('template_id')];?>)">View</a>
                    </td>
                    <td width="60" rowspan="3" align="center"><? echo date("d-m-Y",strtotime($row[csf('po_receive_date')]));?></td>
                    <td width="90">Plan</td>
                <?
					 $tast_id_arr=$tast_tmp_id_arr[$row[csf('template_id')]];
					 $i=0;
					 foreach($tna_task_id as $vid=>$key)
					 {
						 $i++;
						if ( $new_approval_arr[$row[csf('job_no')]][$key]=="") $new_data=explode("_",$row[csf('status').$key]); 
						else $new_data=explode("_",$new_approval_arr[$row[csf('job_no')]][$key]);
						if($new_data[7]!=""){$function="onclick=update_tna_process(1,$new_data[7],'{$row[csf(job_no)]}',1)";} else{ $function="";}
						
						
						if($plan_manual_update_task_arr[$vid]==''){$function="";}
						
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
						
						if( $new_data[7]!="") $function="onclick=update_tna_process(2,$new_data[7],'{$row[csf(job_no)]}',1)";  else $function="";
						$bgcolor1=""; $bgcolor="";
						
						if($actual_manual_update_task_arr[$vid]==''){$function="";}
						
						
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
						
						
						if($new_data[11]==1){$asc=" style='color:#0000FF'";}else{$asc="";}
						if($new_data[12]==1){$afc=" style='color:#0000FF'";}else{$afc="";}
						
						
						$idd=$row[csf('job_no')]."".$row[csf('po_number_id')]."".$key;
						if(count($tna_task_id)==$i)
							echo '<td align="center"'.$asc.' title="Click Here to Edit Date" id="'.$idd.'1" '.$function.' width="80" bgcolor="'.$bgcolor.'">'.($new_data[0]== "" || $new_data[0]=="0000-00-00" ? "" : change_date_format($new_data[0])).'</td><td align="center"'.$afc.' id="'.$idd.'2" title="Click Here to Edit Date" '.$function.' bgcolor="'.$bgcolor1.'" title="'.$new_data[6].'">'.($new_data[1]== "" || $new_data[1]=="0000-00-00" ? "" : change_date_format($new_data[1])).'</td>';
							
						else
							echo '<td align="center"'.$asc.' id="'.$idd.'1" title="Click Here to Edit Date"  '.$function.' width="80" bgcolor="'.$bgcolor.'">'.($new_data[0]== "" || $new_data[0]=="0000-00-00" ? "" : change_date_format($new_data[0])).'</td><td align="center"'.$afc.' id="'.$idd.'2" title="Click Here to Edit Date" '.$function.' width="80" bgcolor="'.$bgcolor1.'" title="'.$new_data[6].'">'.($new_data[1]== "" || $new_data[1]=="0000-00-00" ? "" : change_date_format($new_data[1])).'</td>';
						
					 }
					echo '</tr>'; 
					
					echo '<tr><td width="90">Delay/Early By</td>';
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
									$start_diff=-abs($start_diff1-1);
								}
								else
								{
									$start_diff=-abs($start_diff1-1);
								}
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
									$finish_diff=-abs($finish_diff1-1);
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
    <div style="width:<? echo $width+420; ?>px;" align="left">
         <table width="100%" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <tfoot>
                <th colspan="<? echo (count($tna_task_id)*2)+13;?>">&nbsp;</th>
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
 	echo "****$filename";
	exit();
}

 



if($action=="order_dtls")
{
	echo load_html_head_contents("Order Dtls","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	 
	$job_sql="select a.JOB_NO,b.PO_NUMBER,b.PUB_SHIPMENT_DATE from WO_PO_DETAILS_MASTER a,WO_PO_BREAK_DOWN b where a.JOB_NO=b.JOB_NO_MST and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and B.ID in($po_id)";
	$job_sql_result= sql_select($job_sql);
	?>
        <table id="tbl_task_template" class="rpt_table" rules="all" border="1" width="100%">
            <thead>
                <th width="30">SL</th>
                <th>Job Number</th>
                <th>PO Number</th>
                <th>Ship Date</th>
            </thead>
            <tbody>
			<?
		 	$i=0;
			foreach ( $job_sql_result as $row )
			{
				$i++;
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
			?>
				<tr bgcolor="<? echo $bgcolor; ?>">
                    <td align="center"><?= $i; ?></td>
                    <td align="center"><?=$row[JOB_NO];?></td>
                    <td><?=$row[PO_NUMBER];?></td>
                    <td align="center"><?=change_date_format($row[PUB_SHIPMENT_DATE]);?></td>
                </tr>
				<?
			}
		  ?>
        </tbody>
    </table>
    <? 
	exit();
}


if($action=="template_detiles")
{
	echo load_html_head_contents("TNA","../../../", 1, 1, $unicode);
	extract($_REQUEST);
 $task_short_arr = return_library_array("select task_name,task_short_name from  lib_tna_task ","task_name","task_short_name"); //where sample_type='$key'
	 
	$data_array= sql_select("select id,task_template_id,lead_time,material_source,total_task,tna_task_id,deadline,execution_days,notice_before,sequence_no,for_specific,dependant_task,status_active from  tna_task_template_details where task_template_id in (".$templat_id.") and status_active > 0 and is_deleted=0 order by sequence_no"); 
	//,group_concat(tna_task_id) as task_group 
	
	?>
        <table id="tbl_task_template" class="rpt_table" rules="all" border="1" width="628">
            <thead>
            <tr>
                <th width="35">SL</th>
                <th width="150">Task Short Name </th>
                <th width="60">Deadline</th>
                <th width="60">Execution Days</th>
                <th width="60">Notice Before </th>	
                <th width="60">Sequence No</th>
                <th width="150">Dependant Task</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
		<?
		 $i=0;
			foreach ( $data_array as $row )
			{
				$i++;
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
		?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                    <td><? echo $i; ?></td>
                    <td><? echo $task_short_arr[$row[csf("tna_task_id")]]; ?></td>
                    <td align="center"><? echo $row[csf("deadline")]; ?></td>
                    <td align="center"><? echo $row[csf("execution_days")]; ?></td>
                    <td align="center"><? echo $row[csf("notice_before")]; ?></td>
                    <td align="center"><? echo $row[csf("sequence_no")]; ?></td>
                    <td><? echo $task_short_arr[$row[csf("dependant_task")]]; ?></td>
                    <td align="center"><? echo $row_status[$row[csf("status_active")]]; ?></td>
                </tr>
				<?
			}
		  ?>
        </tbody>
    </table>
    <? 
	
	
	exit();
}




//==================================================================================


if($action=="edit_update_tna")
{
	
	echo load_html_head_contents("TNA","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	 
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		
		
		$sql="SELECT a.within_group,  a.style_ref_no,  a.sales_booking_no,  a.job_no, a.buyer_id AS buyer_name, a.PO_BUYER AS po_buyer FROM fabric_sales_order_mst a WHERE a.JOB_NO ='$booking_id'";
		 //echo $sql;
		$result=sql_select($sql);
		
		$tna= "select template_id,task_number,task_start_date,task_finish_date,actual_start_date,actual_finish_date,plan_start_flag,plan_finish_flag,commit_start_date,commit_end_date,PO_NUMBER_ID from  tna_process_mst where id=$mid  and task_type=2";
		$tna_result=sql_select($tna);
		
		$mod_sql= sql_select("select id,task_catagory,task_name,task_short_name,task_type,completion_percent from lib_tna_task where is_deleted = 0 and status_active=1 order by task_sequence_no asc");
		$tna_task_array=array();
		foreach ($mod_sql as $row)
		{	
			$tna_task_array[$row[csf("task_name")]] = $row[csf("task_short_name")];
		}
		
		//History data start------------------------
		$tna_history_sql= "select id, template_id,task_number, job_no, po_number_id, task_start_date, task_finish_date, actual_start_date, actual_finish_date,plan_start_flag,plan_finish_flag,commit_start_date,commit_end_date from  tna_plan_actual_history where task_type=2 and template_id=".$tna_result[0][csf('template_id')]." and task_number=".$tna_result[0][csf('task_number')]." and po_number_id=".$tna_result[0][PO_NUMBER_ID]." and job_no='".$result[0][csf('job_no')]."'";
		
		//echo $tna_history_sql;  
		$tna_history=sql_select($tna_history_sql);
		//History data end------------------------

		$permission_sql_res=sql_select("select PLAN_USER_ID,ACTCUAL_USER_ID from TNA_MANUAL_PERMISSION where COMPANY_ID=$cbo_company_name and TASK_ID={$tna_result[0][csf('task_number')]}");
		$plan_status = (in_array($user_id, explode(',', $permission_sql_res[0]['PLAN_USER_ID'])))?1:0;
		$actcual_status = (in_array($user_id, explode(',', $permission_sql_res[0]['ACTCUAL_USER_ID'])))?1:0;
		// echo($tna_history_sql);
	
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
	if($tna_result[0][csf('commit_start_date')]==""){$cs_result_con=0;}
	else if($tna_result[0][csf('commit_start_date')]=="0000-00-00"){$cs_result_con=0;}else{$cs_result_con=1;}
	
	if($tna_history[0][csf('commit_end_date')]==""){$ce_history_con=0;}
	else if($tna_history[0][csf('commit_end_date')]=="0000-00-00"){$ce_history_con=0;}else{$ce_history_con=1;}
	if($tna_result[0][csf('commit_end_date')]==""){$ce_result_con=0;}
	else if($tna_result[0][csf('commit_end_date')]=="0000-00-00"){$ce_result_con=0;}else{$ce_result_con=1;}
	
	
	
	if($result[0][csf('within_group')]==1){
		$buyer_name = $buyer_name[$result[0][csf('po_buyer')]]; 
	}
	else
	{
		$buyer_name = $buyer_name[$result[0][csf('buyer_name')]]; 
	}
					 
	

	
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
		 
		var data="action=save_update_delete&operation="+operation+'&is_job_wise='+<?=$is_job_wise;?>+'&start_flag='+start_flag+'&finish_flag='+finish_flag+'&task_number=<?=$tna_result[0][csf('task_number')];?>'+get_submitted_data_string('txt_actual_start_date*txt_actual_finish_date*txt_update_tna_id*txt_plan_start_date*txt_plan_finish_date*txt_update_tna_type*txt_plan_actual_history*txt_commitment_start_date*txt_commitment_end_date*txt_update_job',"../../../");
		//alert (data);return;
		freeze_window(operation);
		http.open("POST","textile_tna_report_controller.php",true);
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
    <table><tr><td><font size="+1"><b><? echo $tna_task_array[$tna_result[0][csf('task_number')]]; ?></b></font></td></tr></table>  
    <table width="600" cellspacing="0" cellpadding="0" class="rpt_table">
    	<thead>
        	<th width="100">Buyer Name</th>
            <th width="100">Sales Order</th>
            <th width="120">Style Ref No</th>
            <th width="120">Booking no</th>
        </thead>
        <tr>
        	<td><? echo $buyer_name; ?></td>
            <td><? echo $result[0][csf('job_no')]; ?></td>
            <td><? echo $result[0][csf('style_ref_no')]; ?></td>
            <td><? echo $result[0][csf('sales_booking_no')]; ?></td>
            
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
        	<td align="right">Actual Start Date</td>
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
        	<td colspan="4" height="50" valign="middle" align="center" class="button_container">
            <input type="hidden" id="txt_plan_actual_history" name="txt_plan_actual_history"  value="<? echo $tna_history[0][csf('id')].'_'.$tna_result[0][csf('template_id')].'_'.$tna_result[0][csf('task_number')].'_'.$tna_result[0][PO_NUMBER_ID].'_'.$result[0][csf('job_no')]; ?>" />
            <input type="hidden" id="txt_update_job" name="txt_update_job"  value="<?=$result[0][csf('job_no')]; ?>" />
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

if($action=="save_update_delete")
{
		
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 


	$update_tna_type=str_replace("'",'',$txt_update_tna_type);
	//echo $txt_plan_actual_history;die;
	
	
	if ($operation==1)  
	{		
		$con = connect();
		if($db_type==0)
		{
			  mysql_query("BEGIN");
		}
		 
		$id=str_replace("'",'',$txt_update_tna_id);
		

		
		if($update_tna_type==1)
		{
			$field='';$data='';
			if($start_flag==1){$field="*plan_start_flag";$data="*1";}
			if($finish_flag==1){$field.="*plan_finish_flag";$data.="*1";}
			$field_array1="task_start_date*task_finish_date".$field;
			$data_array1="".$txt_plan_start_date."*".$txt_plan_finish_date.$data."";

			if($is_job_wise==1){
				
				$fieldSet='';
				if($start_flag==1){$fieldSet=",plan_start_flag=1";}
				if($finish_flag==1){$fieldSet.=",plan_finish_flag=1";}
				
				$updateSql="update tna_process_mst set task_start_date=$txt_plan_start_date,task_finish_date=$txt_plan_finish_date $fieldSet where JOB_NO=$txt_update_job and TASK_NUMBER=$task_number and TASK_TYPE=2";
				$rID=execute_query($updateSql);
				
				//$rID=sql_update("tna_process_mst",$field_array1,$data_array1,"JOB_NO",$txt_update_job,1);
			}
			else{
				$rID=sql_update("tna_process_mst",$field_array1,$data_array1,"id",$id,1);
			}
			
				
			
			//history process  start-----------------------------------------;
			list($hmid,$htmp_id,$htask_id,$hpo_id,$hjob_id)=explode("_",$txt_plan_actual_history);
			if(str_replace("'","",$hmid))
			{
				$rID=sql_update("tna_plan_actual_history",$field_array1,$data_array1,"id",str_replace("'","",$hmid),1);
			}
			else
			{
				$hid=return_next_id( "id", "tna_plan_actual_history", 1 ) ;
				$field_array="id, template_id,task_number, job_no, po_number_id, task_start_date, task_finish_date, plan_start_flag,plan_finish_flag,status_active,is_deleted,task_type";
				$data_array="(".$hid.",".$htmp_id.",".$htask_id.",'".str_replace("'","",$hjob_id)."',".$hpo_id.",".$txt_plan_start_date.",".$txt_plan_finish_date.",".$start_flag.",".$finish_flag.",'1','0',2)";
				$rID=sql_insert("tna_plan_actual_history",$field_array,$data_array,1);
				
			}
			//history process end-----------------------------------------;
			//echo "11**$data_array1";die;
			//echo $data_array;die;
			
		}
		else if($update_tna_type==3)
		{
			
			$field_array1="commit_start_date*commit_end_date";
			$data_array1="".$txt_commitment_start_date."*".$txt_commitment_end_date."";

			if($is_job_wise==1){
				$updateSql="update tna_process_mst set commit_start_date=$txt_commitment_start_date,commit_end_date=$txt_commitment_end_date where JOB_NO=$txt_update_job and TASK_NUMBER=$task_number and TASK_TYPE=2";
				$rID=execute_query($updateSql);
				//$rID=sql_update("tna_process_mst",$field_array1,$data_array1,"JOB_NO",$txt_update_job,1);
			}
			else{
				$rID=sql_update("tna_process_mst",$field_array1,$data_array1,"id",$id,1);
			}
			
			
			//history process  start-----------------------------------------;
			list($hmid,$htmp_id,$htask_id,$hpo_id,$hjob_id)=explode("_",$txt_plan_actual_history);
			if(str_replace("'","",$hmid))
			{
				$rID=sql_update("tna_plan_actual_history",$field_array1,$data_array1,"id",str_replace("'","",$hmid),1);
			}
			else
			{
				$hid=return_next_id( "id", "tna_plan_actual_history", 1 ) ;
				$field_array="id, template_id,task_number, job_no, po_number_id, task_start_date, task_finish_date,commit_start_date,commit_end_date, plan_start_flag,plan_finish_flag,status_active,is_deleted,task_type";
				$data_array="(".$hid.",".$htmp_id.",".$htask_id.",'".str_replace("'","",$hjob_id)."',".$hpo_id.",".$txt_plan_start_date.",".$txt_plan_finish_date.",".$txt_commitment_start_date.",".$txt_commitment_end_date.",".$start_flag.",".$finish_flag.",'1','0',2)";
				$rID=sql_insert("tna_plan_actual_history",$field_array,$data_array,1);
				 //echo '10**insert into tna_plan_actual_history '.$data_array.'values'.$data_array;die;;
				
			}
			
			
			//history process end-----------------------------------------;
			
		}
		else
		{
			if($db_type==0)
			{
				$sql2 ="SELECT actual_start_date,actual_finish_date,actual_start_flag,actual_finish_flag FROM tna_process_mst where id=$id and task_type=2";
			}
			if($db_type==2 || $db_type==1)
			{	
				$sql2 ="SELECT actual_start_date,actual_finish_date,nvl(actual_start_flag,0) as actual_start_flag,nvl(actual_finish_flag,0) as actual_finish_flag FROM tna_process_mst where id=$id and task_type=2";
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
			
			if($is_job_wise==1){
				
				$updateSql="update tna_process_mst set actual_start_date=$txt_actual_start_date,actual_finish_date=$txt_actual_finish_date,actual_start_flag=$start,actual_finish_flag=$finish  where JOB_NO=$txt_update_job and TASK_NUMBER=$task_number and TASK_TYPE=2";
				$rID=execute_query($updateSql);
				//$rID=sql_update("tna_process_mst",$field_array,$data_array,"JOB_NO",$txt_update_job,1);
			}
			else{
				$rID=sql_update("tna_process_mst",$field_array,$data_array,"id",$id,1);
			}
			
			
			
			
			//history process  start-----------------------------------------;
			list($hmid,$htmp_id,$htask_id,$hpo_id,$hjob_id)=explode("_",$txt_plan_actual_history);
			if(str_replace("'","",$hmid))
			{
				$hfield_array="actual_start_date*actual_finish_date";
				$hdata_array="".$txt_actual_start_date."*".$txt_actual_finish_date."";
				$rID=sql_update("tna_plan_actual_history",$hfield_array,$hdata_array,"id",str_replace("'","",$hmid),1);
				
			}
			else
			{
				$hid=return_next_id( "id", "tna_plan_actual_history", 1 ) ;
				$field_array="id, template_id,task_number,job_no, po_number_id, actual_start_date, actual_finish_date, status_active, is_deleted,task_type";
				$data_array="(".$hid.",".$htmp_id.",".$htask_id.",'".str_replace("'","",$hjob_id)."','".$hpo_id."',".$txt_actual_start_date.",".$txt_actual_finish_date.",'1','0',2)";
				$rID=sql_insert("tna_plan_actual_history",$field_array,$data_array,1);
			
			
			//echo $data_array;
			
			
			}
			//history process end-----------------------------------------;
			
			  //echo "11**".$rID;
			
		}
			
		if($db_type==0)
		{
			  if($rID)
			  {
				  mysql_query("COMMIT");  
				  echo "1**".str_replace("'", '', $id);
			  }
			  else
			  {
				  mysql_query("ROLLBACK"); 
				  echo "10**";
			  }
		}
		if($db_type==1 || $db_type==2 )
		{
			if($rID)
			{
				  oci_commit($con);
				  echo "1**".str_replace("'", '', $id);
			}
			else
			{
				  oci_rollback($con);
				  echo "10**";
			}
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
	$tna_task_arr=return_library_array( "select task_name, task_short_name from lib_tna_task",'task_name','task_short_name');
	$lead_time_array=return_library_array("select task_template_id,lead_time from tna_task_template_details where task_type=2 group by lead_time,task_template_id","task_template_id",'lead_time');

	$sql ="select b.company_name,b.buyer_name,a.po_number,b.job_no,b.style_ref_no,b.gmts_item_id,a.po_received_date,a.shipment_date,(po_quantity*total_set_qnty) as po_qty_pcs, set_smv from  wo_po_break_down a,wo_po_details_master b where a.id=$po_id and a.job_no_mst=b.job_no";
	$result=sql_select($sql);
	
	
	$tna_task_id=array();
	$plan_start_array=array();
	$plan_finish_array=array();
	$actual_start_array=array();
	$actual_finish_array=array();
	$notice_start_array=array();
	$notice_finish_array=array();
	
	 $task_sql=sql_select("select a.task_number,a.task_start_date,a.task_finish_date,a.actual_start_date,a.actual_finish_date,a.notice_date_start,a.notice_date_end from tna_process_mst a, lib_tna_task b where a.task_number=b.task_name and a.po_number_id='$po_id' and b.status_active=1 and b.is_deleted=0 and a.task_type=2 order by b.task_sequence_no asc");
	
	foreach ($task_sql as $row_task)
	{
		$tna_task_id[]=$row_task[csf("task_number")];
		
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


	
	
	
	$comments_array=array();
	$responsible_array=array();
	
	//$res_comm_sql= sql_select("select task_id, comments, responsible from tna_progress_comments where tamplate_id='$template_id' and order_id='$po_id'");
	//echo "select task_id, comments, responsible from tna_progress_comments where order_id='$po_id'";
	$res_comm_sql=sql_select("select task_id, comments, responsible,mer_comments from tna_progress_comments where order_id='$po_id'");
	
	foreach ($res_comm_sql as $row_res_comm)
	{
		$comments_array[$row_res_comm[csf("task_id")]] =$row_res_comm[csf("comments")];
		$mer_comments_array[$row_res_comm[csf("task_id")]] =$row_res_comm[csf("mer_comments")];
		$responsible_array[$row_res_comm[csf("task_id")]]=$row_res_comm[csf("responsible")];
	}
	
	
	$execution_time_array=array();
	
	//$execution_time_sql= sql_select("select tna_task_id, execution_days from tna_task_template_details where task_template_id='$template_id'");
	
	$execution_time_sql= sql_select("select for_specific, tna_task_id, execution_days from tna_task_template_details where task_type=2 ");
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
	
	$lead_time=return_library_array("select task_template_id,lead_time from tna_task_template_details where task_type=2  group by task_template_id,lead_time","task_template_id","lead_time");
		


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
			var data="action=save_update_delete_progress_comments&operation="+operation+get_submitted_data_string('jobno*orderid*tamplateid',"../../../")+data_all+'&tot_row='+tot_row;
			//alert (data);return;
			freeze_window(operation);
			http.open("POST","textile_tna_report_controller.php",true);
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
				$.post('textile_tna_report_controller.php?job_no='+'<? echo $job_no; ?>'+'&po_id='+<? echo $po_id; ?>+'&template_id='+<? echo $template_id; ?>+'&tna_process_type='+<? echo $tna_process_type; ?>,
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
			  $("#auto_id").load("textile_tna_report_controller.php");// a function which will load data from other file after x seconds
		  }
		
		function openmypage(i)
		{	
			var title = 'TNA Progress Comment';
			
			var txtcomments = document.getElementById(i).value;
			//var data='additional_info='+additional_info;
			//alert(txtcomments);return;
			
			var page_link = 'textile_tna_report_controller.php?data='+txtcomments+'&action=comments_popup';
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
			document.getElementById('scroll_body2').style.overflow="auto";
			document.getElementById('scroll_body2').style.maxHeight="none";
			
			//$('#comments_tbl tr:first').remove(); 
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('details_reports').innerHTML+'</body</html>');
			d.close();
			
			document.getElementById('scroll_body2').style.overflowY="scroll";
			document.getElementById('scroll_body2').style.maxHeight="180px";
			
			//$('#comments_tbl tr:first').show();
		}
		
		function new_excel()
		{
			window.open($('#txt_file_link_ref').val(), "#");
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
            <td><b><?php echo $row[csf('set_smv')]; ?></b></td>
        </tr>
        <tr>
            <td>Order Recv. Date</td>
           	<td><?php  echo change_date_format($row[csf('po_received_date')]); ?></td>
        	<td>Ship Date</td>
            <td><b><?php  echo change_date_format($row[csf('shipment_date')]); ?></b></td>
            <td>Lead Time</td>
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
					echo $lead_timee+1;
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
						
						//echo $c=count($tna_task_id);die;
						
                        foreach($tna_task_id as $key)
                        {
                            $i++;
                            
                            if ($i%2==0)  
                                $trcolor="#E9F3FF";
                            else
                                $trcolor="#FFFFFF";	
								
								
						//$new_data : 0->actual_start_date, 1->actual_finish_date, 2->task_start_date, 3->task_finish_date, 4->notice_date_start, 5->notice_date_end
						
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
                        <tr bgcolor="<? echo $trcolor; ?>">
                            <td align="center" width="30"><? echo $i; ?></td>
                            <td width="150"> <? echo $tna_task_arr[$key]; ?></td>
                            <td align="center" width="60"><? echo datediff( "d", $plan_start_array[$key],$plan_finish_array[$key]);//$execution_time_array[$buyer_id][$key]; ?></td>
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
	var tableFilters = 
	 {
		//col_33: "none",
		//col_operation: {
		//id: ["total_order_qnty","total_order_qnty_in_pcs","value_tot_cm_cost","value_tot_cost","value_order","value_margin","value_tot_trims_cost","value_tot_embell_cost"],
		//col: [9,11,25,26,29,30,31,32],
		//operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
		//write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		//}
	 }
	 
	// setFilterGrid("comments_tbl",-1,tableFilters);
	 //setFilterGrid("comments_tbl");
	</script>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
    <?
	
		/*$html=ob_get_contents();		
		ob_clean(); 
		//for report temp file delete 
		foreach (glob( "tmp_report_file/"."*.xls") as $filename) {			
				@unlink($filename);
		}	
		//html to xls convert
		$name=time();
		$name="$name".".xls";	
		$create_new_excel = fopen('tmp_report_file/'.$name, 'w');	
		$is_created = fwrite($create_new_excel,$html);
		
		echo "$html"."####"."$name"."####".$small_print;	*/
		
		//echo $html;
		
		foreach (glob("*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
		}
		//---------end------------//
		
		$create_new_doc = fopen($filenames, 'w');	
		$is_created = fwrite($create_new_doc,ob_get_contents());
		//echo "$total_data****$filenames****$tot_rows";
		
		
		
		
		/* $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename"; */
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

if ($action=="save_update_delete_progress_comments")
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
		
		$id=return_next_id( "id","tna_progress_comments", 1 ) ;
		
		$field_array_comments="id, job_id, order_id, tamplate_id, task_id, responsible, comments,mer_comments, inserted_by, insert_date, status_active, is_deleted";
		
		//$data_array_comments='';
		
		
		 
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
		
		
		$rID=execute_query("delete from tna_progress_comments where tamplate_id=$tamplateid and order_id=$orderid");
		
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
	$tna_task_arr=return_library_array( "select task_name, task_short_name from lib_tna_task",'task_name','task_short_name');
	
	$tna_task_id=array();
	$plan_start_array=array();
	$plan_finish_array=array();
	
	$actual_start_array=array();
	$actual_finish_array=array();
	
	$notice_start_array=array();
	$notice_finish_array=array();
	
	//$task_sql= sql_select("select a.task_number,a.task_start_date,a.task_finish_date,a.actual_start_date,a.actual_finish_date,a.notice_date_start,a.notice_date_end from tna_process_mst a, lib_tna_task b where a.task_number=b.task_name and a.template_id='$template_id' and a.po_number_id='$po_id' order by b.task_sequence_no asc");
	
	 $task_sql=sql_select("select a.task_number,a.task_start_date,a.task_finish_date,a.actual_start_date,a.actual_finish_date,a.notice_date_start,a.notice_date_end from tna_process_mst a, lib_tna_task b where a.task_number=b.task_name and a.po_number_id='$po_id' and a.task_type=2 order by b.task_sequence_no asc");
	
	foreach ($task_sql as $row_task)
	{
		$tna_task_id[]=$row_task[csf("task_number")];
		
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

	
	
	$lead_time_array=return_library_array("select task_template_id,lead_time from tna_task_template_details where task_type=2 group by lead_time","task_template_id",'lead_time');
	
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
	
	$execution_time_sql= sql_select("select tna_task_id, execution_days from tna_task_template_details where task_type=2");
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
	
	$lead_time=return_library_array("select task_template_id,lead_time from tna_task_template_details where task_type=2 group by task_template_id,lead_time","task_template_id","lead_time");
	
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



if($action=="get_booking_dtls"){
 
   
   
   
   $booking_sql="SELECT BOOKING_NO, COMPANY_ID, PO_BREAK_DOWN_ID, ITEM_CATEGORY, FABRIC_SOURCE, JOB_NO ,ENTRY_FORM, IS_APPROVED from wo_booking_mst where entry_form in(86,88,118) and booking_no='$data' and booking_type=1 and is_short in(1,2) and status_active=1 and is_deleted=0";
	$booking_sql_result= sql_select($booking_sql);
	$dataArr=array();
	foreach($booking_sql_result as $row){
		$dataArr[]=$row[BOOKING_NO];
		$dataArr[]=$row[COMPANY_ID];
		$dataArr[]=$row[FABRIC_SOURCE];
		$dataArr[]=$row[ENTRY_FORM];
		$dataArr[]=$row[JOB_NO];
		$dataArr[]=$row[PO_BREAK_DOWN_ID];
		$dataArr[]=$row[IS_APPROVED];
		$dataArr[]=$row[ITEM_CATEGORY];
	}
	
	
	
	
		$print_report_format2=return_field_value("format_id"," lib_report_template","template_name ={$dataArr[1]} and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
		$fReportId2=explode(",",$print_report_format2);
		$fReportId2=$fReportId2[0];

		// Short Fabric Booking
		$print_report_format3=return_field_value("format_id"," lib_report_template","template_name ={$dataArr[1]}  and module_id=2 and report_id=2 and is_deleted=0 and status_active=1");
		$fReportId3=explode(",",$print_report_format3);
		$fReportId3=$fReportId3[0];

		if ($dataArr[3]==86 || $dataArr[3]==118) 
		{// Budget Wise Fabric Booking and Main Fabric Booking V2
			$dataArr[]=$fReportId2;
		}
		else if($dataArr[3]==88)
		{
			$dataArr[]=$fReportId3;// Short Fabric Booking
		}
	
	
		echo implode('**',$dataArr);

	
}

?>

