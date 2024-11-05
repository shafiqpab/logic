<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');


include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.fabrics.php');
include('../../../includes/class4/class.yarns.php');
include('../../../includes/class4/class.trims.php');


extract($_REQUEST);
$user_id=$_SESSION['logic_erp']['user_id'];
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];



if($db_type==0) $blank_date="0000-00-00"; else $blank_date=""; 

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Customer --", $selected, "" );  
	die; 	 
}

if($action=="load_drop_down_cust_buyer")
{
	echo create_drop_down( "cbo_customer_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Cust. Buyer --", $selected, "" );  
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



if($action=="generate_tna_report_job_wise_v2")
{

	//if(str_replace("'","",$cbo_company_id)!=0){$where_con=" and a.company_name = $cbo_company_id";}
	 if(str_replace("'","",$cbo_buyer_name)!=0){$where_con.=" and b.buyer_id = $cbo_buyer_name";}
	if(str_replace("'","",$cbo_customer_buyer_name)!=0){$where_con.=" and b.CUSTOMER_BUYER = $cbo_customer_buyer_name";}
	
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
		$tna_task_cat[$row[csf("task_name")]]=$row[csf("task_catagory")];
		$tna_task_name_arr[$row[csf("task_name")]]=$row[csf("task_name")];
		
		$tna_task_array[$row[csf("task_name")]] =$row[csf("task_short_name")];
		$tna_task_name_array[$row[csf("task_name")]] =$tna_task_name[$row[csf("task_name")]];
		
		$lead_time_array[$row[csf("task_template_id")]]=$row[csf("lead_time")];
		$tast_tmp_id_arr[$row[csf("task_template_id")]][$row[csf("tna_task_id")]]=$row[csf("tna_task_id")];
	}
	
	$c=count($tna_task_id);
	if($db_type==0)
	{
		$sql ="select b.style_ref_no,b.season,b.sales_booking_no,b.buyer_id,b.CUSTOMER_BUYER,b.within_group,a.po_number_id, a.job_no, a.shipment_date,max(b.delivery_date) as pub_shipment_date, a.template_id, a.po_receive_date,b.insert_date,";
		$i=1;
	
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c) $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id, ";
			else $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id ";
			$i++;
		}
		
		$sql .=" from  tna_process_mst a, fabric_sales_order_mst b where a.po_number_id=b.id  and b.status_active=1  and a.task_type=2 and b.company_id=$cbo_company_id $txt_job_no $txt_order_no $date_range  group by b.style_ref_no,b.season,b.sales_booking_no,b.buyer_id,b.CUSTOMER_BUYER,b.within_group,a.po_number_id,a.job_no,a.template_id,a.shipment_date,b.insert_date order by a.shipment_date,a.po_number_id,a.job_no"; 
	}
	else
	{
		$sql ="select b.id, b.style_ref_no,b.season,b.sales_booking_no,b.buyer_id,b.CUSTOMER_BUYER,b.within_group,
		
		LISTAGG(cast(a.po_number_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as po_number_id, 
		LISTAGG(cast(a.template_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY a.id) as template_id, 		
		
		a.job_no, max(a.shipment_date) as shipment_date,max(b.delivery_date) as pub_shipment_date, max(a.po_receive_date) as po_receive_date,max(b.BOOKING_DATE) as BOOKING_DATE,b.insert_date,";
		$i=1; 
		
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c){$sql .="max(CASE WHEN a.task_number = '".$id."' THEN a.actual_start_date || '_' || a.actual_finish_date || '_' || a.task_start_date || '_' || a.task_finish_date ||'_' || a.notice_date_start || '_' || a.notice_date_end || '_' || a.remarks || '_' || a.id || '_' || a.task_number || '_' || a.plan_start_flag || '_' || a.plan_finish_flag  || '_' || a.ACTUAL_START_FLAG || '_' || a.ACTUAL_FINISH_FLAG   END ) as status$id, min(CASE WHEN a.task_number = '".$id."' THEN a.actual_start_date || '_' || a.actual_finish_date || '_' || a.task_start_date || '_' || a.task_finish_date ||'_' || a.notice_date_start || '_' || a.notice_date_end || '_' || a.remarks || '_' || a.id || '_' || a.task_number || '_' || a.plan_start_flag || '_' || a.plan_finish_flag  || '_' || a.ACTUAL_START_FLAG || '_' || a.ACTUAL_FINISH_FLAG   END ) as min_status$id, ";
			}
			else{$sql .="max(CASE WHEN a.task_number = '".$id."'  THEN a.actual_start_date || '_' || a.actual_finish_date || '_' || a.task_start_date || '_' || a.task_finish_date || '_' || a.notice_date_start || '_' || a.notice_date_end || '_' || a.remarks || '_' || a.id || '_' || a.task_number || '_' || a.plan_start_flag || '_' || a.plan_finish_flag  || '_' || a.ACTUAL_START_FLAG || '_' || a.ACTUAL_FINISH_FLAG   END ) as status$id, min(CASE WHEN a.task_number = '".$id."'  THEN a.actual_start_date || '_' || a.actual_finish_date || '_' || a.task_start_date || '_' || a.task_finish_date || '_' || a.notice_date_start || '_' || a.notice_date_end || '_' || a.remarks || '_' || a.id || '_' || a.task_number || '_' || a.plan_start_flag || '_' || a.plan_finish_flag  || '_' || a.ACTUAL_START_FLAG || '_' || a.ACTUAL_FINISH_FLAG   END ) as min_status$id  ";
			}
			
			$i++;
		}

		$sql .=" from  tna_process_mst a, fabric_sales_order_mst b,FABRIC_SALES_ORDER_DTLS c where a.job_no= b.JOB_NO and b.id=c.mst_id and c.id=a.po_number_id  and b.status_active=1 and c.status_active=1  and a.task_type=2 and b.company_id=$cbo_company_id $txt_job_no $txt_order_no $date_range $where_con  group by b.id, b.style_ref_no,b.season,b.sales_booking_no,b.buyer_id,b.CUSTOMER_BUYER,b.within_group,a.job_no,a.shipment_date,b.insert_date order by a.shipment_date,a.job_no"; 
	}
	    
	//  echo $sql;die;
		
	$data_sql= sql_select($sql);
	
	foreach($data_sql as $row){
		$jobIdArr[$row[csf('po_number_id')]]=$row[csf('po_number_id')];
		$bookingArr[$row[csf('sales_booking_no')]]=$row[csf('sales_booking_no')];
	}
	
	$booking_sql = "select a.JOB_NO,b.buyer_id po_buyer,b.APPROVED_DATE  from fabric_sales_order_mst a,wo_booking_mst b where a.sales_booking_no=b.booking_no ".where_con_using_array(explode(',',implode(',',$bookingArr)),1,'b.booking_no')." ";
	
	$booking_sql_res= sql_select($booking_sql);
	foreach($booking_sql_res as $row){
		$po_buyer_id_arr[$row['JOB_NO']]=$row['PO_BUYER'];
		$booking_app_date_arr[$row['JOB_NO']]=$row['APPROVED_DATE'];
	}
	
	
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
		$template_id_arr[$row['PO_NUMBER_ID']]=$row['TEMPLATE_ID'];
		$template_lead_time_arr[$row['PO_NUMBER_ID']]=$row['TEMPLATE_ID'];
	}
	 
	//print_r($tna_task_array);die;
	
	$width=(count($tna_task_id)*161)+920;

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
                <th rowspan="2" width="80">Customer</th>
                <th rowspan="2" width="80">Cust. Buyer</th>
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
         
         
        	<div style="overflow-y:scroll; max-height:360px; width:<? echo $width+20; ?>px;" align="left" id="scroll_body">
          	<table width="<? echo $width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
       
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
				if ($row[csf('min_status').$key]!="") $min_new_approval_arr[$row[csf('job_no')]][$key]=$row[csf('min_status').$key];
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
                    <td rowspan="3" width="80"><?=$buyer_name[$row[csf('buyer_id')]]; ?></td>
                    <td rowspan="3" width="80"><?=$buyer_name[$row['CUSTOMER_BUYER']]; ?></td>
                    <td rowspan="3" width="100"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                    <td rowspan="3" width="80"><p><? echo $row[csf('season')]; ?></p></td>
                    <td rowspan="3" width="120" align="center">
						<a href="javascript:void(0)" onClick="generate_report_tna_textail('<? echo $cbo_company_id; ?>','<? echo $row[csf('sales_booking_no')]; ?>','<? echo $row[csf('job_no')]; ?>');"><? echo $row[csf('sales_booking_no')]; ?></a><br>
						<? echo ($booking_app_date_arr[$row['JOB_NO']])?$booking_app_date_arr[$row['JOB_NO']]:'-- -- --'; ?>
					</td>
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
						$po_lead_time=datediff( "d", date("Y-m-d",strtotime(change_date_format($row[csf('BOOKING_DATE')]))), date("Y-m-d",strtotime(change_date_format($row[csf('shipment_date')]))) );

					 ?>
                    <td width="100" rowspan="3" title="<? echo " Booking Date: ".change_date_format($row['BOOKING_DATE']);  echo ",\n Receive Date: ".change_date_format($row['PO_RECEIVE_DATE']); echo ",\n Delivery Date: ".$row[csf('pub_shipment_date')] .",\n Insert Date: ".$row[csf('insert_date')]; echo " ,\n Template ID:".$row[csf('template_id')];?>"><? echo change_date_format($row[csf('shipment_date')])."<br>"." ".$lead_time."<br>"." FSO L/T:".$po_lead_time;  ?>
                    <a href="javascript:openTemplate(<? echo $row[csf('template_id')];?>)">View</a>
                    </td>
                    <td width="60" rowspan="3" align="center"><? echo date("d-m-Y",strtotime($row[csf('po_receive_date')]));?></td>
                    <td width="90">Plan</td>
                <?
					 $tast_id_arr=$tast_tmp_id_arr[$row[csf('template_id')]];
				 
					 $i=0;
					 foreach($tna_task_array as $key=>$vid)
					 {
						 $i++;
						if ( $new_approval_arr[$row[csf('job_no')]][$key]==""){ $new_data=explode("_",$row[csf('status').$key]); }
						else $new_data=explode("_",$new_approval_arr[$row[csf('job_no')]][$key]);
						if($new_data[7]!=""){$function="onclick=update_tna_process(1,$new_data[7],'{$row[csf(job_no)]}',1)";} else{ $function="";}
						
						
						if($plan_manual_update_task_arr[$key]==''){$function="";}
						
						if($new_data[9]==1){$psc=" style='color:#0000FF'";}else{$psc="";}
						if($new_data[10]==1){$pfc=" style='color:#0000FF'";}else{$pfc="";}
						
						
						if(in_array($key,$tast_id_arr))
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
					 foreach($tna_task_array as $key=>$vid)
					 {
						  
						 $i++;
						if ( $new_approval_arr[$row[csf('job_no')]][$key]==""){$new_data=explode("_",$row[csf('status').$key]);}
						else{$new_data=explode("_",$new_approval_arr[$row[csf('job_no')]][$key]);}
								
						if ( $min_new_approval_arr[$row[csf('job_no')]][$key]==""){$min_new_data=explode("_",$row[csf('min_status').$key]);}
						else{$min_new_data=explode("_",$min_new_approval_arr[$row[csf('job_no')]][$key]);}
								
						
						if( $new_data[7]!="") $function="onclick=update_tna_process(2,$new_data[7],'{$row[csf(job_no)]}',1)";  else $function="";
						$bgcolor1=""; $bgcolor="";
						
						if($actual_manual_update_task_arr[$key]==''){$function="";}
						
						
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
						
						if ($min_new_data[0]!=$blank_date) $bgcolor="";
						if ($new_data[1]!=$blank_date) $bgcolor1="";
						
						
						if($new_data[11]==1){$asc=" style='color:#0000FF'";}else{$asc="";}
						if($new_data[12]==1){$afc=" style='color:#0000FF'";}else{$afc="";}
						
						
						$idd=$row[csf('job_no')]."".$row[csf('po_number_id')]."".$key;
						if(count($tna_task_id)==$i)
							echo '<td align="center"'.$asc.' title="Click Here to Edit Date" id="'.$idd.'1" '.$function.' width="80" bgcolor="'.$bgcolor.'">'.($min_new_data[0]== "" || $min_new_data[0]=="0000-00-00" ? "" : change_date_format($min_new_data[0])).'</td><td align="center"'.$afc.' id="'.$idd.'2" title="Click Here to Edit Date" '.$function.' bgcolor="'.$bgcolor1.'" title="'.$new_data[6].'">'.($new_data[1]== "" || $new_data[1]=="0000-00-00" ? "" : change_date_format($new_data[1])).'</td>';
							
						else
							echo '<td align="center"'.$asc.' id="'.$idd.'1" title="Click Here to Edit Date"  '.$function.' width="80" bgcolor="'.$bgcolor.'">'.($min_new_data[0]== "" || $min_new_data[0]=="0000-00-00" ? "" : change_date_format($min_new_data[0])).'</td><td align="center"'.$afc.' id="'.$idd.'2" title="Click Here to Edit Date" '.$function.' width="80" bgcolor="'.$bgcolor1.'" title="'.$new_data[6].'">'.($new_data[1]== "" || $new_data[1]=="0000-00-00" ? "" : change_date_format($new_data[1])).'</td>';
						
					 }
					echo '</tr>'; 
					
					echo '<tr><td width="90">Delay/Early By</td>';
					$j=0;
					foreach($tna_task_array as $key=>$vid)
					{
						 $j++;
						if ( $new_approval_arr[$row[csf('job_no')]][$key]==""){ $new_data=explode("_",$row[csf('status').$key]); }
						else{ $new_data=explode("_",$new_approval_arr[$row[csf('job_no')]][$key]);}
								
						if ( $min_new_approval_arr[$row[csf('job_no')]][$key]==""){ $min_new_data=explode("_",$row[csf('min_status').$key]); }
						else{ $min_new_data=explode("_",$min_new_approval_arr[$row[csf('job_no')]][$key]);}
						
						$bgcolor1=""; $bgcolor="";
						
						
						if($min_new_data[0]!=$blank_date)
						{
							$start_diff1 = datediff( "d", $min_new_data[0], $new_data[2]);
							if($min_new_data[0]== "")
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
								if($min_new_data[0]== "")
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
							if($min_new_data[0]== "")
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
    <div style="width:<? echo $width+20; ?>px;" align="left">
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
		http.open("POST","textile_tna_report_controller_v2.php",true);
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
            	<input type="text" <? if($type==2 ||  $type==3) echo "disabled='disabled'";  ?> name="txt_plan_start_date" id="txt_plan_start_date" class="datepicker" style="width:100px" value="<? if($ts_history_con==1){echo change_date_format($tna_history[0][csf('task_start_date')]);} else if($ts_result_con==0){echo "";}else{ echo change_date_format($tna_result[0][csf('task_start_date')]);} ?>" />
            </td>
            
            <td align="right">Plan Finish Date</td>
            <td>
            	<input type="text" <? if($type==2 ||  $type==3) echo "disabled='disabled'";  ?> name="txt_plan_finish_date" id="txt_plan_finish_date" class="datepicker" style="width:100px"  value="<? if($tf_history_con==1){echo change_date_format($tna_history[0][csf('task_finish_date')]);} else if($tf_result_con==0){echo "";}else echo change_date_format($tna_result[0][csf('task_finish_date')]); ?>"/>
            </td>
        </tr>
        
         <tr>
        	<td align="right">Actual Start Date</td>
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
			http.open("POST","textile_tna_report_controller_v2.php",true);
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
				$.post('textile_tna_report_controller_v2.php?job_no='+'<? echo $job_no; ?>'+'&po_id='+<? echo $po_id; ?>+'&template_id='+<? echo $template_id; ?>+'&tna_process_type='+<? echo $tna_process_type; ?>,
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
			  $("#auto_id").load("textile_tna_report_controller_v2.php");// a function which will load data from other file after x seconds
		  }
		
		function openmypage(i)
		{	
			var title = 'TNA Progress Comment';
			
			var txtcomments = document.getElementById(i).value;
			//var data='additional_info='+additional_info;
			//alert(txtcomments);return;
			
			var page_link = 'textile_tna_report_controller_v2.php?data='+txtcomments+'&action=comments_popup';
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




if($action=="print_booking_15") //ISD-22-8874 by Md Mamun-13-05-2022
{
	extract($_REQUEST);

	$data = explode("*", $data);
	//print_r($data);die;

	//$cbo_company_name=str_replace("'","",$cbo_company_name);
	//$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	//$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);

	$path="../../";

	//echo $cbo_company_name;die;

	$cbo_company_name = $data[0];
	$txt_booking_no = $data[1];
	$cbo_fabric_natu = $data[2];
	$cbo_fabric_source = $data[3];
	 
	//echo $txt_booking_no;die;

	$imge_arr=return_library_array( "SELECT master_tble_id,image_location from common_photo_library where form_name='company_details' or form_name='knit_order_entry' and file_type=1",'master_tble_id','image_location');
	$company_library=return_library_array( "SELECT id,company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
	$color_library=return_library_array( "SELECT id,color_name from lib_color", "id", "color_name");
	$supplier_name_arr=return_library_array( "SELECT id,supplier_name from lib_supplier  where status_active=1 and is_deleted=0",'id','supplier_name');
	$supplier_address_arr=return_library_array( "SELECT id,address_1 from lib_supplier  where status_active=1 and is_deleted=0",'id','address_1');
	$marchentrArr = return_library_array("SELECT id,team_member_name from lib_mkt_team_member_info  where status_active=1 and is_deleted=0","id","team_member_name");
	$buyer_name_arr=return_library_array("SELECT id,buyer_name from lib_buyer  where status_active=1 and is_deleted=0",'id','buyer_name');

	$nameArray_approved=sql_select("SELECT max(b.approved_no) as approved_no,a.is_approved, count(b.id) as revised_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='$txt_booking_no' and b.entry_form=7 group by a.is_approved");
	//print_r($nameArray_approved);die;

	$nameArray_approved=sql_select("select max(b.approved_no) as approved_no,a.is_approved, count(b.id) as revised_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='$txt_booking_no' and b.entry_form=7 group by a.is_approved");
	list($nameArray_approved_row)=$nameArray_approved;

	$nameArray_approved_date=sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='$txt_booking_no' and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
	

	list($nameArray_approved_date_row)=$nameArray_approved_date;
	$nameArray_approved_comments=sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='$txt_booking_no' and b.entry_form=7 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
	list($nameArray_approved_comments_row)=$nameArray_approved_comments;

	$job_po_arr=array();
	$ref_no='';$job_no_aarr='';
	$nameArray_per_job=sql_select("SELECT  a.job_no, a.style_ref_no, b.po_break_down_id, c.po_number, c.grouping from wo_po_details_master a, wo_booking_dtls b, wo_po_break_down c where c.id=b.po_break_down_id and a.job_no=b.job_no and  a.job_no=c.job_no_mst and b.booking_no='$txt_booking_no' and b.status_active =1 and b.is_deleted=0  group by a.job_no,a.style_ref_no,b.po_break_down_id,c.grouping,c.po_number");

	

	foreach ($nameArray_per_job as $row_per_job)
	{
		$job_no_aarr.="'".$row_per_job[csf('job_no')]."'".',';
		$all_po_id_arr[$row_per_job[csf('po_break_down_id')]]=$row_per_job[csf('po_break_down_id')];
		$job_po_arr[$row_per_job[csf('job_no')]].=$row_per_job[csf('po_number')].',';
		if($ref_no=='') $ref_no=$row_per_job[csf('grouping')]; else $ref_no.=",".$row_per_job[csf('grouping')];
		
		$job_no_allArr.=$row_per_job[csf('job_no')].',';
	}
	
	$job_no_all=rtrim($job_no_allArr,',');
	$job_no_Arr=array_unique(explode(",",$job_no_all));

	$job_nos=rtrim($job_no_aarr,',');
	$txt_order_no_id=implode(",",$all_po_id_arr);
	$job_nos=implode(",",array_unique(explode(",",$job_nos)));

	$ref_nos=implode(",",array_unique(explode(",",$ref_no)));

	$job_data_arr=array(); 
	$nameArray_buyer=sql_select( "SELECT a.style_ref_no, a.style_description, a.job_no, a.style_owner, a.buyer_name, a.dealing_marchant, a.season, a.season_matrix, a.total_set_qnty, a.product_dept, a.product_code, a.pro_sub_dep, a.gmts_item_id, a.order_repeat_no, a.qlty_label from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no='$txt_booking_no' and b.status_active =1 and b.is_deleted=0 and a.job_no in(".$job_nos.") order by a.job_no ");

	


	foreach ($nameArray_buyer as $result_buy)
	{
		$job_data_arr['job_no'][$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
		$job_data_arr['job_no_in'][$result_buy[csf('job_no')]]="'".$result_buy[csf('job_no')]."'";
		$dealing_marchant.=$marchentrArr[$result_buy[csf('dealing_marchant')]].',';
	}
	
	$sqlColorSizeBreak="select job_no_mst, color_number_id, article_number from wo_po_color_size_breakdown where job_no_mst in(".$job_nos.") and status_active =1 and is_deleted=0";
	$sqlColorSizeBreakArr=sql_select($sqlColorSizeBreak); $artNoArr=array();
	
	foreach ($sqlColorSizeBreakArr as $czrow)
	{
		if($artNoArr[$czrow[csf('job_no_mst')]][$czrow[csf('color_number_id')]]['artno']=="")
			$artNoArr[$czrow[csf('job_no_mst')]][$czrow[csf('color_number_id')]]['artno']=$czrow[csf('article_number')];
		else
			$artNoArr[$czrow[csf('job_no_mst')]][$czrow[csf('color_number_id')]]['artno'].=','.$czrow[csf('article_number')];
	}
	unset($sqlColorSizeBreakArr);

	$dealing_marchant=rtrim($dealing_marchant,',');
	$dealing_marchants=implode(",",array_unique(explode(",",$dealing_marchant)));
 	$nameArray=sql_select( "SELECT a.buyer_id, a.booking_no, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.po_break_down_id, a.fabric_source, a.remarks, a.pay_mode, a.fabric_composition, a.booking_percent, a.is_approved,a.item_category,a.uom from wo_booking_mst a where  a.booking_no='$txt_booking_no' and a.status_active =1 and a.is_deleted=0");

	 

 	foreach ($nameArray as $result_job)
 	{
 		$booking_date=$result_job[csf('booking_date')];
 		$buyer_id=$result_job[csf('buyer_id')];
 		$currency_id=$result_job[csf('currency_id')];
 		$attention=$result_job[csf('attention')];
 		$delivery_date=$result_job[csf('delivery_date')];
 		$supplier_id=$result_job[csf('supplier_id')];
 		$remarks=$result_job[csf('remarks')];
 		$payMode=$result_job[csf('pay_mode')];
 		$booking_percent=$result_job[csf('booking_percent')];
		$is_approved=$result_job[csf('is_approved')];
		$item=$result_job[csf('item_category')];
		$uom=$result_job[csf('uom')];
 	}

 	$lapdip_no_sql=sql_select("SELECT job_no_mst, lapdip_no,color_name_id from wo_po_lapdip_approval_info where  job_no_mst in ($job_nos) and status_active = 1 and approval_status = 3 ");

	

 	foreach($lapdip_no_sql as $key=>$vals)
 	{
 		$lapdip_no_arr[$vals[csf("job_no_mst")]][$vals[csf("color_name_id")]]=$vals[csf("lapdip_no")];
 	}

 	$condition= new condition();
	if(str_replace("'","",$txt_order_no_id) !='')
	{
		$condition->po_id("in($txt_order_no_id)");
	}

	$condition->init();
	$fabric= new fabric($condition);
	$fabric_costing_arr2=$fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
	$fabric_qty_arr=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
	$fabric_amount_arr=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
	$fab_qnty_arr=$fabric->getQtyArray_by_JobFabricIdItemBobyPartGmtsColorGsmDia_greyAndfinish();
	ob_start();
	?>
	<style>

		@media print
		{
			.page-break { height:0; page-break-before:always; margin:0; border-top:none; }
		}
		body, p, span, td, a {font-size:10pt;font-family: Arial;}
		body{margin-left:2em; margin-right:2em; font-family: "Arial Narrow", Arial, sans-serif;}
	</style>
  <div style="width:1310px" align="center">
		<table width="100%" cellpadding="0" cellspacing="0" style="border:0px solid black" >
			<tr>
				<td width="100">
					<img  src='<?=$path.$imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
				</td>
				<td width="1250">
					<table width="100%" cellpadding="0" cellspacing="0"  border="0" >
						<tr>
							<td align="center"><?=$company_library[$cbo_company_name]; ?></td>
							<td rowspan="3" width="250">
								<span><b> Booking No:&nbsp;&nbsp;<?=trim($txt_booking_no,"'"); ?></b></span><br/>
								<span><b> Booking Date :&nbsp;&nbsp;<?=change_date_format($booking_date); ?></b></span><br/>
								 <?
								if($nameArray_approved_row[csf('approved_no')]==1 && $nameArray_approved_row[csf('is_approved')]==0){
									?>
                                    <b> Revised No :  <?=$nameArray_approved_row[csf('revised_no')]; ?></b>
                                      <br/>
                                      Approved Date: <?=$nameArray_approved_date_row[csf('approved_date')]; ?>
                                    <?

								}
								 if($nameArray_approved_row[csf('approved_no')]>1 && $nameArray_approved_row[csf('is_approved')]==0)
								 {
								 ?>
								 <b> Revised No: <?=$nameArray_approved_row[csf('revised_no')];?></b>
                                  <br/>
								  Approved Date: <?=$nameArray_approved_date_row[csf('approved_date')]; ?>
								  <?
								 }
							  	?>
                                <?
                                if($nameArray_approved_row[csf('approved_no')]>1 && ($nameArray_approved_row[csf('is_approved')]==1 || $nameArray_approved_row[csf('is_approved')]==3))
								 {
								 ?>
								 <b> Revised No: <?=$nameArray_approved_row[csf('revised_no')]-1;?></b>
                                  <br/>
								  Approved Date: <?=$nameArray_approved_date_row[csf('approved_date')]; ?>
								  <?
								 }
							  	?>
							</td>
						</tr>
						<tr>
							<td align="center">
								<?
								$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");

								if($txt_job_no!="")
								{
									$location=return_field_value( "location_name", "wo_po_details_master","job_no='$txt_job_no'");
								}
								else
								{
									$location="";
								}
								foreach ($nameArray as $result)
								{
									$email=$result[csf('email')];
									$city=$result[csf('city')];

									?>
									Email Address: <?=$email;?>
									Website: <? echo $result[csf('website')]; ?>
									<?
								}
								?>
							</td>
						</tr>
						<tr>
							<td align="center">
								<strong><? if($report_title !=""){ echo $report_title.'-'.$fabric_source[$cbo_fabric_source];}?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if($is_approved==1){ echo "(Approved)";} else if($is_approved==3){ echo "(Partial Approved)";} else{echo "";}; ?> </font></strong><!--ISD-22-05697 by Kausar-->
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table width="100%" style="border:0px solid black;table-layout: fixed;" >
			<tr>
				<td width="200"><span><b>To </b></span></td>
				<td width="280">&nbsp;<span></span></td>
				<td width="200"><span><b>Buyer</b></span></td>
				<td width="230"><span> :&nbsp;<b><? echo $buyer_name_arr[$buyer_id]; ?></b></span></td>
			</tr>
			<tr>
				<td width="200"><b>Supplier Name</b>   </td>
				<td width="280">:&nbsp;
					<?
					if($payMode==5 || $payMode==3){
						echo $company_library[$supplier_id];
						$suplier_address=$city.','.$email;
					}
					else{
						echo $supplier_name_arr[$supplier_id];
						$suplier_address=$supplier_address_arr[$supplier_id];
					}
					?>    </td>
					<td width="200"><b>Dealing Marchant</b></td>
					<td width="" colspan="2">:&nbsp;<?=$dealing_marchants;?></td>
			</tr>
			<tr>
				<td width="200"><b>Address</b></td>
				<td width="280">:&nbsp;<?=$suplier_address; ?></td>
				<td width="200"><b>Currency </b>   </td>
				<td width="230">:&nbsp;
					<?=$currency[$currency_id]; ?>
				</td>
			</tr>
			<tr>
				<td width="200"><b>Attention</b></td>
				<td  width="280">:&nbsp;<?=$attention; ?></td>
				<td width="200"><b>Fabric Nature</b></td>
				<td  width="280">:&nbsp;<? echo $item_category[$item];?>(<? echo $unit_of_measurement[$uom]?>)</td>
				
			</tr>
			<tr>
				<td width="200"><b>Delivery Date</b></td>
				<td width="280">:&nbsp;<?=change_date_format($delivery_date); ?></td>
				<td width="200"><b>Internal Ref. No </b>   </td>
				<td colspan="2">:&nbsp;
					<?=$ref_nos; ?>
				</td>
			</tr>
			<tr>
				<td width="200"><b>Pay Mode</b></td>
				<td width="280">:&nbsp;<?php echo $pay_mode[$payMode]; ?>
				<td width="200"><b>Remark</b></td>
				<td colspan="2">:&nbsp;<?=$remarks;?></td>
			</tr>
	    </table>
	    <?
		// and a.job_no='CTL-22-02031'
		$costing_per_arr=return_library_array( "select job_no, costing_per from wo_pre_cost_mst where job_no in(".$job_nos.") ", "job_no","costing_per");
		$set_item_ratio_data=sql_select("SELECT id,set_item_ratio,gmts_item_id,job_no from wo_po_details_mas_set_details  where job_no in (".$job_nos.") ");

		foreach($set_item_ratio_data as $row){
			$set_item_ratio_arr[$row[csf("job_no")]][$row[csf("gmts_item_id")]]=$row[csf("set_item_ratio")];
		}

		$plan_cut_data =sql_select("select plan_cut_qnty,job_no_mst,po_break_down_id,item_number_id,color_number_id,size_number_id from wo_po_color_size_breakdown where job_no_mst in(".$job_nos.")   and status_active=1 ");

		foreach($plan_cut_data as $val){
			$plan_cut_arr[$val[csf("job_no_mst")]][$val[csf("po_break_down_id")]][$val[csf("item_number_id")]][$val[csf("color_number_id")]][$val[csf("size_number_id")]]+=$val[csf("plan_cut_qnty")];
		}

	    $color_wise_process_loss=sql_select("SELECT a.id,a.job_no, a.body_part_id, b.color_number_id, a.process_loss_method, b.process_loss_percent as loss,a.item_number_id,b.cons_pcs AS consdzn,b.requirment,b.po_break_down_id, a.gsm_weight as gsm,b.dia_width as dia,b.gmts_sizes FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b WHERE a.job_id=b.job_id and a.id=b.pre_cost_fabric_cost_dtls_id and  a.job_no in(".$job_nos.")  and a.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1  group by a.id,a.job_no, a.body_part_id, a.process_loss_method, b.color_number_id, b.process_loss_percent,a.item_number_id,b.po_break_down_id, a.gsm_weight,b.dia_width,b.gmts_sizes,b.cons_pcs,b.requirment");
		//echo "SELECT a.job_no, a.body_part_id, b.color_number_id, a.process_loss_method, b.process_loss_percent as loss, avg(b.cons_pcs) as consdzn FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and  a.job_no in(".$job_nos.") and b.process_loss_percent>0 group by a.job_no, a.body_part_id, a.process_loss_method, b.color_number_id, b.process_loss_percent";
		foreach($color_wise_process_loss as $val)
	    {
			$set_item_ratio=$set_item_ratio_arr[$val[csf("job_no")]][$val[csf("item_number_id")]];
			$costing_per=$costing_per_arr[$val[csf("job_no")]];			
			$plan_cut_qnty=$plan_cut_arr[$val[csf("job_no")]][$val[csf("po_break_down_id")]][$val[csf("item_number_id")]][$val[csf("color_number_id")]][$val[csf("gmts_sizes")]];
			
			// echo $plan_cut_qnty.'<br>';
			if($set_item_ratio==0 || $set_item_ratio=="") $set_item_ratio=1;
			$pcs_value=0;

			if($costing_per==1) $pcs_value=1*12*$set_item_ratio;
			else if($costing_per==2) $pcs_value=1*1*$set_item_ratio;
			else if($costing_per==3) $pcs_value=2*12*$set_item_ratio;
			else if($costing_per==4) $pcs_value=3*12*$set_item_ratio;
			else if($costing_per==5) $pcs_value=4*12*$set_item_ratio;
			if($val[csf("loss")]>0)
			{ 
				$loss_arr[$val[csf("job_no")]][$val[csf("id")]][$val[csf("item_number_id")]][$val[csf("body_part_id")]][$val[csf("color_number_id")]]['loss']=$val[csf("loss")];
			}			
				$loss_arr[$val[csf("job_no")]][$val[csf("id")]][$val[csf("item_number_id")]][$val[csf("body_part_id")]][$val[csf("color_number_id")]]['consdzn']=$val[csf("consdzn")];
	    }

	    $sql_booking="SELECT a.job_no, a.id as fabric_cost_dtls_id, a.body_part_id, a.color_type_id as c_type, a.construction, a.composition, a.gsm_weight as gsm, a.avg_finish_cons, d.process_loss_percent, d.dia_width as dia, a.uom, (d.fin_fab_qnty) as fin_fab_qntys,(d.adjust_qty) as adjust_qty, (d.grey_fab_qnty) as grey_fab_qntys, (d.rate) as rates, (d.amount) as amounts, c.style_ref_no, c.job_no_prefix_num, d.fabric_color_id as fab_color, d.gmts_color_id as gmt_color,a.item_number_id
	    FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d, wo_po_details_master c
	    WHERE a.job_no=d.job_no and a.id=d.pre_cost_fabric_cost_dtls_id  and a.job_id=c.id  and d.job_no=c.job_no
	    and d.booking_no ='$txt_booking_no' and d.job_no in(".$job_nos.") and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.is_deleted=0
	    order by a.job_no,d.fabric_color_id,a.item_number_id ";
		//echo $sql_booking;die;
		
		$result_set=sql_select($sql_booking);
		foreach( $result_set as $row)
		{
			$fab_dtls_idArr[$row[csf("fabric_cost_dtls_id")]]=$row[csf("fabric_cost_dtls_id")];
		}
		
		$color_wise_avg=sql_select("SELECT a.id as fab_dtls_id,a.job_no, a.body_part_id, b.color_number_id, (b.cons_pcs) as consdzn FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b WHERE a.job_id=b.job_id and a.id=b.pre_cost_fabric_cost_dtls_id  and b.cons_pcs>0 and  a.job_no in(".$job_nos.") and a.id in(".implode(",",$fab_dtls_idArr).")  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
		 
		
	    $color_chkArr=array();
	    // $k=1;
	    foreach($color_wise_avg as $val)
	    {
			$avg_cons_arr[$val[csf("job_no")]][$val[csf("fab_dtls_id")]][$val[csf("color_number_id")]]['consdzn']+=$val[csf("consdzn")];
			$colorstring=$val[csf("job_no")].$val[csf("fab_dtls_id")].$val[csf("color_number_id")];
			$avg_cons_arr[$val[csf("job_no")]][$val[csf("fab_dtls_id")]][$val[csf("color_number_id")]]['color_count']+=1;
		    // $color_chkArr[$colorstring]=$colorstring;
		    // $k++;
	    }
		unset($color_wise_avg);
	 	//print_r($avg_cons_arr);
 
	    foreach( $result_set as $row)
	    {
	    	$body_part_id=$body_part[$row[csf("body_part_id")]];
	    	$uom_data_arr[$row[csf("uom")]]=$unit_of_measurement[$row[csf("uom")]];
	    	$construction=$row[csf("construction")];
	    	$compositions= $row[csf("composition")];
	    	$item_desc=$row[csf("construction")].','.$compositions;

	    	$process_loss=$loss_arr[$row[csf("job_no")]][$row[csf("fabric_cost_dtls_id")]][$row[csf("item_number_id")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]]['loss'];
	    	$process_loss_method=$loss_arr[$row[csf("job_no")]][$row[csf("fabric_cost_dtls_id")]][$row[csf("item_number_id")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]]['loss_method'];
			$fabQnty=$fab_qnty_arr['knit']['finish'][$row[csf("job_no")]][$row[csf("fabric_cost_dtls_id")]][$row[csf("item_number_id")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]][$row[csf("gsm")]][$row[csf("dia")]];
			
			if($process_loss=='') $process_loss=0;else $process_loss=$process_loss;
			if($process_loss_method=='') $process_loss_method=0;else $process_loss_method=$process_loss_method;
			//$avgfincons=$loss_arr[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("gmt_color")]]['consdzn'];
			$avgfincons=$avg_cons_arr[$row[csf("job_no")]][$row[csf("fabric_cost_dtls_id")]][$row[csf("gmt_color")]]['consdzn']/$avg_cons_arr[$row[csf("job_no")]][$row[csf("fabric_cost_dtls_id")]][$row[csf("gmt_color")]]['color_count'];
			//echo $avg_cons_arr[$row[csf("job_no")]][$row[csf("fabric_cost_dtls_id")]][$row[csf("gmt_color")]]['consdzn'].'='.$avg_cons_arr[$row[csf("job_no")]][$row[csf("fabric_cost_dtls_id")]][$row[csf("gmt_color")]]['color_count'].'<br>';

			//$fabric_detail_arr[$row[csf("job_no")]][$row[csf("gmt_color")]][$row[csf("fab_color")]][$body_part_id][$item_desc]['gsm']=$row[csf("gsm")];
			//$fabric_detail_arr[$row[csf("job_no")]][$row[csf("gmt_color")]][$row[csf("fab_color")]][$body_part_id][$item_desc]['dia']=$row[csf("dia")];
			$fabric_detail_arr[$row[csf("job_no")]][$row[csf("gmt_color")]][$row[csf("fab_color")]][$row[csf("item_number_id")]][$row[csf("body_part_id")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]]['c_type']=$row[csf("c_type")];
	    	$fabric_detail_arr[$row[csf("job_no")]][$row[csf("gmt_color")]][$row[csf("fab_color")]][$row[csf("item_number_id")]][$row[csf("body_part_id")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]]['uom']=$row[csf("uom")];
	    	$fabric_detail_arr[$row[csf("job_no")]][$row[csf("gmt_color")]][$row[csf("fab_color")]][$row[csf("item_number_id")]][$row[csf("body_part_id")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]]['style_ref_no']=$row[csf("style_ref_no")];
	    	$fabric_detail_arr[$row[csf("job_no")]][$row[csf("gmt_color")]][$row[csf("fab_color")]][$row[csf("item_number_id")]][$row[csf("body_part_id")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]]['job_prefix']=$row[csf("job_no_prefix_num")];
	    	$fabric_detail_arr[$row[csf("job_no")]][$row[csf("gmt_color")]][$row[csf("fab_color")]][$row[csf("item_number_id")]][$row[csf("body_part_id")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]]['full_job']=$row[csf("job_no")];
	    	$fabric_detail_arr[$row[csf("job_no")]][$row[csf("gmt_color")]][$row[csf("fab_color")]][$row[csf("item_number_id")]][$row[csf("body_part_id")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]]['style_ref_no']=$row[csf("style_ref_no")];

	    	// $fabric_detail_arr[$row[csf("job_no")]][$row[csf("gmt_color")]][$row[csf("fab_color")]][$row[csf("body_part_id")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]]['fin_qty']+=$row[csf("fin_fab_qntys")]+$row[csf("adjust_qty")];
			 $fabric_detail_arr[$row[csf("job_no")]][$row[csf("gmt_color")]][$row[csf("fab_color")]][$row[csf("item_number_id")]][$row[csf("body_part_id")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]]['fin_qty']=$fabQnty;

	    	$fabric_detail_arr[$row[csf("job_no")]][$row[csf("gmt_color")]][$row[csf("fab_color")]][$row[csf("item_number_id")]][$row[csf("body_part_id")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]]['grey_qty']+=$row[csf("grey_fab_qntys")];
			$fabric_detail_arr[$row[csf("job_no")]][$row[csf("gmt_color")]][$row[csf("fab_color")]][$row[csf("item_number_id")]][$row[csf("body_part_id")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]]['fin_fab_qntys']+=$row[csf("fin_fab_qntys")];
	    	$fabric_detail_arr[$row[csf("job_no")]][$row[csf("gmt_color")]][$row[csf("fab_color")]][$row[csf("item_number_id")]][$row[csf("body_part_id")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]]['amounts']+=$row[csf("amounts")];
	    	$fabric_detail_arr[$row[csf("job_no")]][$row[csf("gmt_color")]][$row[csf("fab_color")]][$row[csf("item_number_id")]][$row[csf("body_part_id")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]]['rates']=$row[csf("amounts")]/$row[csf("grey_fab_qntys")];
	    	$fabric_detail_arr[$row[csf("job_no")]][$row[csf("gmt_color")]][$row[csf("fab_color")]][$row[csf("item_number_id")]][$row[csf("body_part_id")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]]['p_loss']=$process_loss;
	    	$fabric_detail_arr[$row[csf("job_no")]][$row[csf("gmt_color")]][$row[csf("fab_color")]][$row[csf("item_number_id")]][$row[csf("body_part_id")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]]['p_loss_method']=$process_loss_method;
	    	$fabric_detail_arr[$row[csf("job_no")]][$row[csf("gmt_color")]][$row[csf("fab_color")]][$row[csf("item_number_id")]][$row[csf("body_part_id")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]]['fabric_cost_dtls_id']=$row[csf("fabric_cost_dtls_id")];
			$fabric_detail_arr[$row[csf("job_no")]][$row[csf("gmt_color")]][$row[csf("fab_color")]][$row[csf("item_number_id")]][$row[csf("body_part_id")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]]['process_losss']+=$row[csf("process_loss_percent")];
			$fabric_detail_arr[$row[csf("job_no")]][$row[csf("gmt_color")]][$row[csf("fab_color")]][$row[csf("item_number_id")]][$row[csf("body_part_id")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]]['adjust_qty']+=$row[csf("adjust_qty")];
			$fabric_detail_arr[$row[csf("job_no")]][$row[csf("gmt_color")]][$row[csf("fab_color")]][$row[csf("item_number_id")]][$row[csf("body_part_id")]][$item_desc][$row[csf("gsm")]][$row[csf("dia")]]['avgfincons']=$avgfincons;

	    }
	    $fab_row_span_arr=array();
	    foreach($fabric_detail_arr as $job_key=>$job_data)
	    {
	    	$desc_rowspan=0;
	    	foreach($job_data as $gmt_color_key=>$gmt_color_data)
	    	{
	    		foreach($gmt_color_data as $fab_color_key=>$fab_color_data)
	    		{
					foreach($fab_color_data as $item_key=>$item_data)
					{
						foreach($item_data as $bodypartid=>$bodypart_data)
						{
							foreach($bodypart_data as $desc_key=>$desData)
							{
								foreach($desData as $gsm=>$gsmdata)
								{
									foreach($gsmdata as $dia=>$val)
									{
										$desc_rowspan++;
									}
								}
							}
						}
					}
	    		}
	    	}
	    	$fab_row_span_arr[$job_key]=$desc_rowspan;
	    }
		?>
		<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >

			<tr>
				<th width="20" align="center" style="word-break: break-all;word-wrap: break-word;">SL</th>
				<th width="50" align="center" style="word-break: break-all;word-wrap: break-word;">Job No</th>
				<th width="80" align="center" style="word-break: break-all;word-wrap: break-word;">Style Ref</th>
                <th width="70" align="center" style="word-break: break-all;word-wrap: break-word;">Body Part</th>
				<th width="130" align="center" style="word-break: break-all;word-wrap: break-word;">Item Description</th>
				<th width="40" align="center" style="word-break: break-all;word-wrap: break-word;">GSM</th>
				<th width="40" align="center" style="word-break: break-all;word-wrap: break-word;">Fabric Dia</th>
				<th width="70" align="center" style="word-break: break-all;word-wrap: break-word;">Color Type</th>
                <th width="60" align="center" style="word-break: break-all;word-wrap: break-word;">Art. No</th>
				<th width="90" align="center" style="word-break: break-all;word-wrap: break-word;">Gmts Color</th>
				<th width="90" align="center" style="word-break: break-all;word-wrap: break-word;">Fabric Color</th>
                <th width="50" align="center" style="word-break: break-all;word-wrap: break-word;">Avg. Fin. Cons[DZN]</th>
				<th width="50" align="center" style="word-break: break-all;word-wrap: break-word;">Lab Dip No</th>
				<th width="40" align="center" style="word-break: break-all;word-wrap: break-word;">UOM</th>
				<th width='60' align="center" style="word-break: break-all;word-wrap: break-word;">Finish Fab. Qty (Budget)</th>
				<th width='60' align="center" style="word-break: break-all;word-wrap: break-word;">Finish Fab. Qty (Booking)</th>
				<th width='50' align="center" style="word-break: break-all;word-wrap: break-word;">Process Loss</th>
				<th width='60' align="center" style="word-break: break-all;word-wrap: break-word;">Grey Fab. Qty[W/O]</th>
				<th width='60' align="center" style="word-break: break-all;word-wrap: break-word;">Adj. Qty</th>
				<th width='60' align="center" style="word-break: break-all;word-wrap: break-word;">Grey Balance Qty</th>
				<th width='50' align="center" style="word-break: break-all;word-wrap: break-word;">Avg Rate</th>
				<th align="center" style="word-break: break-all;word-wrap: break-word;">Amount</th>
			</tr>
			<?
 			$k=$p=1;$total_fin_qty=$total_grey_qty=$total_adjust_qty=$total_grey_adjust_balance=$total_amount=0;

			foreach($fabric_detail_arr as $job_key=>$job_data)
			{
				$y=1;
				foreach($job_data as $gmt_color_key=>$gmt_color_data)
				{
					foreach($gmt_color_data as $fab_color_key=>$fab_color_data)
					{
						foreach($fab_color_data as $item_key=>$item_data)
					{
						
						foreach($item_data as $bodypart_key=>$bodypart_data)
						{
							foreach($bodypart_data as $desc_key=>$desData)
							{
								foreach($desData as $gsm=>$gsmdata)
								{
									foreach($gsmdata as $dia=>$val)
									{
										$po_nos=rtrim($job_po_arr[$job_key],',');
										$po_nos=implode(",",array_unique(explode(",",$po_nos)));
										$fab_row_span=$fab_row_span_arr[$job_key];
										$p_loss_method=$val['p_loss_method'];
										$process_loss=$val['p_loss'];
										$labtest_no=$lab_dip_arr[$job_key]['labtest_no'];
										if($process_loss) $process_loss=$process_loss;else $process_loss=0;
										//  $fin_qty=$val['fin_qty'];
										$diaWidth=$dia;
										if($diaWidth!='') $diaWidth=$diaWidth;
										
										$artnoStr="";
										$artnoStr=implode(",",array_filter(array_unique(explode(",",$artNoArr[$job_key][$gmt_color_key]['artno']))));
										
										$fabric_cost_dtls_id=$val['fabric_cost_dtls_id'];
												/*if($p_loss_method==1) //markup
												{
				
													$fin_qty=$val['fin_qty']-(($val['fin_qty']*$process_loss)/(100+$process_loss));
												}
												else if($p_loss_method==2) //margin
												{
													$fin_qty=$val['fin_qty']-(($val['fin_qty']*$process_loss)/100);
												}
												else $fin_qty=$val['fin_qty'];*/ //Charka
												$fin_qty=$val['fin_qty'];
										?>
										<tr>
											<?
											if($y==1)
											{
												?>
												<td style="word-break: break-all;word-wrap: break-word;" rowspan="<?=$fab_row_span; ?>"><?=$p; ?></td>
												<td style="word-break: break-all;word-wrap: break-word;" align="center" rowspan="<?=$fab_row_span;?>"><?=$val['job_prefix']; ?></td>
												<td style="word-break: break-all;word-wrap: break-word;" rowspan="<?=$fab_row_span;?>"><?=$val['style_ref_no']; ?>&nbsp;</td>
												<?
											}
											$labdipno="";
											if($lapdip_no_arr[$val['full_job']][$fab_color_key]!="") $labdipno=$lapdip_no_arr[$val['full_job']][$fab_color_key]; //ISD-23-14129
											//else $labdipno=$lapdip_no_arr[$val['full_job']][$gmt_color_key];
											?>
											<td style="word-break: break-all;word-wrap: break-word;"><?=$body_part[$bodypart_key]; ?>&nbsp;</td>
											<td style="word-break: break-all;word-wrap: break-word;"><?=$desc_key; ?>&nbsp;</td>
											<td style="word-break: break-all;word-wrap: break-word;"><?=$gsm; ?>&nbsp;</td>
											<td style="word-break: break-all;word-wrap: break-word;"><?=$diaWidth.$fabric_typee[$dia]; ?>&nbsp;</td>
											<td style="word-break: break-all;word-wrap: break-word;"><?=$color_type[$val[('c_type')]]; ?>&nbsp;</td>
											<td style="word-break: break-all;"><?=$artnoStr; ?>&nbsp;</td>
											<td style="word-break: break-all;word-wrap: break-word;"><?=$color_library[$gmt_color_key]; ?>&nbsp;</td>
											<td style="word-break: break-all;word-wrap: break-word;"><?=$color_library[$fab_color_key]; ?>&nbsp;</td>
											<td style="word-break: break-all;" align="right"><?=number_format($val['avgfincons'],2); ?>&nbsp;</td>
											<td style="word-break: break-all;word-wrap: break-word;" title="<?=$lapdip_no_arr[$val['full_job']][$gmt_color_key].'=='; ?>"><?=$labdipno; ?>&nbsp;</td>
											<td  style="word-break: break-all;word-wrap: break-word;"><?=$unit_of_measurement[$val['uom']]; ?>&nbsp;</td>
											<td style="word-break: break-all;word-wrap: break-word;" align="right" title="(Markup/Margin Method) Process Loss=<?=$process_loss;?>,Fin Qty=<?=$fin_qty;?>"><?=number_format($fin_qty,2); //number_format($fin_qty,2); ?>&nbsp;</td>
											<td style="word-break: break-all;word-wrap: break-word;" align="right"  ><?=number_format($val['fin_fab_qntys'],2);  //number_format($fin_qty,2); ?>&nbsp;</td>
											<td style="word-break: break-all;word-wrap: break-word;" align="right"><?=number_format($process_loss,2); ?>&nbsp;</td>
											<td style="word-break: break-all;word-wrap: break-word;" align="right"><?=number_format($val['grey_qty'],2); ?>&nbsp;</td>
											<td style="word-break: break-all;word-wrap: break-word;" align="right"><?=number_format($val['adjust_qty'],2); ?>&nbsp;</td>
											<td style="word-break: break-all;word-wrap: break-word;" align="right"><?=number_format($val['grey_qty']-$val['adjust_qty'],2); ?>&nbsp;</td>
											<td style="word-break: break-all;word-wrap: break-word;" align="right"><?=number_format($val['rates'],2); ?>&nbsp;</td>
											<td style="word-break: break-all;word-wrap: break-word;" align="right"><?=number_format($val['amounts'],2); ?>&nbsp;</td>
										</tr>
										<?
										$k++;$y++;
										$total_fin_qty+=$fin_qty;
										$total_grey_qty+=$val['grey_qty'];
										$total_fin_fab_qntys+=$val['fin_fab_qntys'];
										$total_adjust_qty+=$val['adjust_qty'];
										$total_grey_adjust_balance+=$val['grey_qty']-$val['adjust_qty'];
										$total_amount+=$val['amounts'];
									}
								}
							}}
						}
			        }
		        }
				$p++;
	        }
			?>
			<tfoot>
				<tr>
					<th colspan="14" align="right"> Total </th>
					<th align="right"> <?=number_format($total_fin_qty,2); ?> </th>
					<th align="right"> <?=number_format($total_fin_fab_qntys,2); ?> </th>
					<th></th>
					<th align="right"> <?=number_format($total_grey_qty,2); ?> </th>
					<th align="right"> <?=number_format($total_adjust_qty,2); ?> </th>
					<th align="right"> <?=number_format($total_grey_adjust_balance,2); ?> </th>
					<th align="right"> <? //=number_format($total_fin_qty,2); ?> </th>
					<th align="right"> <?=number_format($total_amount,2); ?> </th>
				</tr>
			</tfoot>
		</table>
		<?
    

	?>
	<br>




	<?
        $size_lib_arr = return_library_array("select id,size_name from  lib_size ","id","size_name");
        $color_lib_arr = return_library_array("select id,color_name from  lib_color ","id","color_name");
        $sql_collar_cuff= "SELECT a.body_part_type, a.body_part_id, b.job_no, b.gmts_color_id, b.size_number_id, b.item_size, b.gmts_qty, b.excess_per, b.qty, b.po_break_down_id as po_id FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_colar_culff_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and b.booking_no ='$txt_booking_no' and b.job_no in (".$job_nos.") and b.status_active=1 and b.is_deleted=0 order by b.id ASC" ;
       // echo $sql_collar_cuff;
        $sql_data_collar_cuff=sql_select($sql_collar_cuff);
        $body_part_color_arr=array(); $body_part_size_arr=array(); $body_part_color_size_arr=array(); $body_part_body_size_arr=array();
        foreach($sql_data_collar_cuff as $row)
        {
            $body_part_color_arr[$row[csf("body_part_type")]][$row[csf("body_part_id")]][$row[csf("job_no")]]['color'][$row[csf("gmts_color_id")]]=$row[csf("gmts_color_id")];
            $body_part_size_arr[$row[csf("body_part_type")]][$row[csf("body_part_id")]][$row[csf("job_no")]]['size'][$row[csf("size_number_id")]][$row[csf("item_size")]]=$row[csf("item_size")];
			 $body_part_gmts_size_arr[$row[csf("body_part_type")]][$row[csf("body_part_id")]][$row[csf("job_no")]]['size'][$row[csf("size_number_id")]]=$row[csf("size_number_id")];
            $body_part_body_size_arr[$row[csf("body_part_type")]][$row[csf("body_part_id")]][$row[csf("job_no")]]['item_size'][$row[csf("size_number_id")]]=$row[csf("item_size")];
            $body_part_color_size_arr[$row[csf("body_part_type")]][$row[csf("body_part_id")]][$row[csf("job_no")]][$row[csf("gmts_color_id")]][$row[csf("size_number_id")]][$row[csf("item_size")]]['qty']+=$row[csf("qty")];
			
			$body_part_color_size_arr[$row[csf("body_part_type")]][$row[csf("body_part_id")]][$row[csf("job_no")]][$row[csf("gmts_color_id")]][$row[csf("size_number_id")]]['gmtsqty']+=$row[csf("gmts_qty")];
			 $body_part_item_size_arr[$row[csf("body_part_type")]][$row[csf("body_part_id")]][$row[csf("job_no")]][$row[csf("size_number_id")]][$row[csf("item_size")]]=$row[csf("item_size")];
			   $body_part_excess_per_arr[$row[csf("body_part_type")]][$row[csf("body_part_id")]][$row[csf("job_no")]][$row[csf("gmts_color_id")]]['excess_per']=$row[csf("excess_per")];
        }

		foreach($body_part_color_arr as $body_type=>$body_data)
		{
			foreach($body_data as $body_id=>$jobdata)
			{
				foreach($jobdata as $job_no=>$data)
				{
					$count_collar_cuff=count($body_part_size_arr[$body_type][$body_id][$job_no]['size']);
					$pre_grand_tot_collar=0; $pre_grand_tot_collar_order_qty=0;

					?>
					<div style="max-height:1330px; overflow:auto; float:left; padding-top:5px; margin-left:5px; margin-bottom:5px; position:relative;">
					<table width="625" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
						<tr>
							<td colspan="<? echo $count_collar_cuff+3; ?>" align="center"><b><? echo "Job No:".$job_no.'; '.$body_part[$body_id]; ?> - Color Size Brakedown in Pcs.</b></td>
						</tr>
						<tr>
							<td width="100">Size</td>
								<?
								foreach($body_part_gmts_size_arr[$body_type][$body_id][$job_no]['size']  as $size_number_id)
								{
									?>
								<td align="center" style="border:1px solid black" colspan="<?=count($body_part_item_size_arr[$body_type][$body_id][$job_no][$size_number_id]);?>"><strong><? echo $size_lib_arr[$size_number_id];?></strong></td>
									<?
								}
								?>
							<td width="60" rowspan="2" align="center"><strong>Total</strong></td>
							<td rowspan="2" align="center"><strong>Extra %</strong></td>
						</tr>
                        <tr>
                            <td style="font-size:12px"><? echo $body_part[$body_id]; ?> Size</td>
                            <?
                            foreach($body_part_item_size_arr[$body_type][$body_id][$job_no]  as $size_number_id=>$size_data)
                            {
								 foreach($size_data  as $size_id=>$size_number)
                            {
								?>
                                <td align="center" style="border:1px solid black"><strong><? echo $size_number;?></strong></td>
                                <?
                            }}

                            $pre_size_total_arr=array();
                            foreach($data['color'] as $color_id=>$color_data)
                            {
								?>
								<tr>
									<td><? echo $color_library[$color_id]; ?></td>
									<? $pre_color_total_collar=0;  $pre_color_total_gmtsqty=0;
										foreach($body_part_size_arr[$body_type][$body_id][$job_no]['size']  as $size_number_id=>$size_data)
										{
											foreach($size_data  as $size_id)
										{
											$size_qty=0; $gmtssize_qty=0;
											$size_qty=$body_part_color_size_arr[$body_type][$body_id][$job_no][$color_id][$size_number_id][$size_id]['qty'];
											
											$gmtssize_qty=$body_part_color_size_arr[$body_type][$body_id][$job_no][$color_id][$size_number_id]['gmtsqty'];
											$pre_size_total_arr[$size_number_id][$size_id]+=$size_qty;
											$pre_color_total_collar+=$size_qty;
											$pre_color_total_gmtsqty+=$gmtssize_qty;
											?>
											<td align="center" style="border:1px solid black"><? echo number_format($size_qty); ?></td>
											<?
										}}
										$excess_per=$body_part_excess_per_arr[$body_type][$body_id][$job_no][$color_id]['excess_per'];
										$color_gmts_pcs=0;
										if($body_type==50) $color_gmts_pcs=(($pre_color_total_collar/2)-$pre_color_total_gmtsqty); else $color_gmts_pcs=$pre_color_total_collar-$pre_color_total_gmtsqty;
									?>
                                    <td align="center"><? echo number_format($pre_color_total_collar); ?></td>
                                    <td align="center"><? echo number_format($excess_per); ;//number_format((($color_gmts_pcs)/$pre_color_total_gmtsqty)*100,2); ?></td>
                                </tr>
                                <?
                                $pre_grand_collar_ex_per+=$collar_ex_per;
                                $pre_grand_tot_collar+=$pre_color_total_collar;
                                $pre_grand_tot_collar_order_qty+=$pre_color_total_collar_order_qnty;
							}
							?>
                        </tr>
                        <tr>
                            <td>Size Total</td>
								<?
                                foreach($pre_size_total_arr  as $gmtsSize=>$size_data)
                                {
									 foreach($size_data  as $size_qty)
                                {
									?>
									<td style="border:1px solid black;  text-align:center"><? echo number_format($size_qty); ?></td>
									<?
                                }}
                                ?>
                            <td style="border:1px solid black; text-align:center"><? echo number_format($pre_grand_tot_collar); ?></td>
                            <td align="center" style="border:1px solid black"><? echo fn_number_format((($pre_grand_tot_collar-$pre_grand_tot_collar_order_qty)/$pre_grand_tot_collar_order_qty)*100,2); ?></td>
                        </tr>
					</table>
                </div>
                <br/>
                <?
				}
            }
        }
		//////////////////



	
	$cos_per_arr=$condition->getCostingPerArr();
	$yarn= new yarn($condition);
	$yarn_data_array=$yarn->getCountCompositionPercentTypeColorAndRateWiseYarnQtyAndAmountArray();
 	$yarn_count_arr=return_library_array( "SELECT id,yarn_count from lib_yarn_count",'id','yarn_count');
 	$po_qnty_tot=return_field_value("sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");

 	if($db_type==0)
 	{
 		$job_listagg=" group_concat(a.job_no) as job_no";
 	}
 	else
 	{

 		$job_listagg=" listagg(cast(a.job_no as varchar2(4000)),',') within group (order by a.job_no) as job_no";
 	}
	 
		// $yarn_sql_array=sql_select("SELECT min(a.id) as id ,a.count_id, a.copm_one_id, a.percent_one, a.color, a.type_id, sum(a.cons_qnty) as yarn_required, a.rate     from wo_pre_cost_fab_yarn_cost_dtls a, wo_booking_dtls b where a.job_no=b.job_no and a.fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.job_no in ($job_nos) and b.booking_no=$txt_booking_no  and  a.status_active=1 and a.is_deleted=0 group by a.count_id,a.copm_one_id,a.percent_one,a.color,a.type_id,a.rate ");

		$yarn_sql_array=sql_select("SELECT min(a.id) as id ,a.count_id, a.copm_one_id, a.percent_one, a.color, a.type_id,sum(a.cons_qnty) as yarn_required,a.rate,sum(b.grey_fab_qnty*a.cons_ratio/100) as booking_qty	from wo_pre_cost_fab_yarn_cost_dtls a, wo_booking_dtls b
		where a.job_no=b.job_no and a.fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and b.booking_type=1 and a.job_no in ($job_nos) and b.booking_no='$txt_booking_no'
		and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0  group by a.count_id,a.copm_one_id,a.percent_one,a.color,a.type_id,a.rate");
	?>
	<br/>
	<br/>
	<br/>
	<table style="margin-top: 10px;" class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >

		<tr align="center">
             <td colspan="8"><b>Yarn Required Summary (Pre Cost) </b></td>
        </tr>

        <tr align="center">
        	<td width="25" style="word-wrap: break-word;word-break: break-all;">Sl</td>
        	<td width="400" style="word-wrap: break-word;word-break: break-all;">Yarn Description</td>
        	<td width="60" style="word-wrap: break-word;word-break: break-all;">Brand</td>
        	<td width="60" style="word-wrap: break-word;word-break: break-all;">Lot</td>
        	<td width="50" style="word-wrap: break-word;word-break: break-all;">Rate</td>
        	<td width="120" style="word-wrap: break-word;word-break: break-all;">Cons for Dzn Gmts</td>
        	<td width="110" style="word-wrap: break-word;word-break: break-all;">Total (KG)</td>
			<td width="110" style="word-wrap: break-word;word-break: break-all;">Booking Qty</td>
        </tr>

        <?
		$i=0;
		$total_yarn=0;$total_booking_qty=0;
		foreach($yarn_sql_array  as $row)
        {

			$i++;
			$rowcons_qnty = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];
			$rowcons_Amt = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['amount'];
			$booing_qnty=$row[csf("booking_qty")];

			$rate=$rowcons_Amt/$rowcons_qnty;
			$rowcons_qnty =($rowcons_qnty/100)*$booking_percent;
			$job_no=$row[csf("job_no")];
			$cos_per_value=0;
			//foreach(explode(",", $job_no) as $keys=>$vals)
			foreach($job_no_Arr as $keys=>$vals)
			{
				$cos_per_value=$cos_per_arr[$vals];
			}
			?>
            <tr align="center">
            	<td width="25" style="word-wrap: break-word;word-break: break-all;"><? echo $i; ?></td>

            	<td width="400" style="word-wrap: break-word;word-break: break-all;" align="left">
            		<?
            		$yarn_des=$yarn_count_arr[$row[csf('count_id')]]." ".$composition[$row[csf('copm_one_id')]]." ".$row[csf('percent_one')]."%  ";
            		$yarn_des.=$color_library[$row[csf('color')]]." ";
            		$yarn_des.=$yarn_type[$row[csf('type_id')]];
            		echo $yarn_des;
            		?>
            	</td>
            	<td width="60" style="word-wrap: break-word;word-break: break-all;"></td>
            	<td width="60" style="word-wrap: break-word;word-break: break-all;"></td>
            	<td width="50" style="word-wrap: break-word;word-break: break-all;"><? echo number_format($row[csf('rate')],4);  $cos_per_arr[$job_no].' c'; ?></td>
            	<td width="120" style="word-wrap: break-word;word-break: break-all;"><? echo number_format(($rowcons_qnty/$po_qnty_tot)*$cos_per_value,4);?></td>
            	<td align="right" width="110" style="word-wrap: break-word;word-break: break-all;"><? echo number_format($rowcons_qnty,2); $total_yarn+=$rowcons_qnty; ?></td>
				<td align="right" width="110" style="word-wrap: break-word;word-break: break-all;"><? echo number_format($booing_qnty,2); $total_booking_qty+=$booing_qnty; ?></td>
            </tr>
            <?
		}
		?>
       <tr align="center">
	       	<td colspan="6" align="right">Total</td>
	       	<td align="right"><? echo number_format($total_yarn,2); ?></td>
			<td align="right"><? echo number_format($total_booking_qty,2); ?></td>
        </tr>
	</table>

	<br>
	<?
	$color_name_arr=return_library_array( "SELECT id,color_name from lib_color",'id','color_name');
	
	$all_jobs_id=$job_nos;
	$sql_stripe="SELECT c.id,c.job_no,c.composition,c.construction,c.body_part_id,c.gsm_weight,c.color_type_id,d.color_number_id as color_number_id,d.id as did,d.uom,d.stripe_color,d.fabreqtotkg as fabreqtotkg ,d.measurement as measurement ,d.yarn_dyed,c.uom as type_uom from wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c,wo_pre_stripe_color d where c.id=b.pre_cost_fabric_cost_dtls_id and c.job_no=b.job_no and d.pre_cost_fabric_cost_dtls_id=c.id and d.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and b.job_no=d.job_no and d.job_no in($all_jobs_id)  and b.booking_no='$txt_booking_no'  and c.color_type_id in (2,3,6) and b.status_active=1  and c.is_deleted=0 and c.status_active=1  and d.is_deleted=0 and d.status_active=1  and b.is_deleted=0  
	group by c.id,c.job_no,c.body_part_id,c.gsm_weight,c.color_type_id,d.color_number_id,d.uom,d.id,d.stripe_color,d.yarn_dyed,d.fabreqtotkg ,d.measurement,c.composition,c.construction,c.uom order by c.job_no,c.id,d.id ";
	//echo $sql_stripe;
		$result_data=sql_select($sql_stripe);
		foreach($result_data as $row)
		{
			$style_ref_no=$job_data_arr['style_ref_no'][$row[csf('job_no')]];
			if($row[csf('type_uom')]==12){
				$type_uom_arr[$row[csf('type_uom')]]='kg';
			}elseif($row[csf('type_uom')]==1){
				$type_uom_arr[$row[csf('type_uom')]]='pcs';
			}
			$stripe_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('stripe_color')].'*'.$row[csf('did')]]['type_uom']=$row[csf('type_uom')];
			$stripe_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('stripe_color')].'*'.$row[csf('did')]]['stripe_color']=$row[csf('stripe_color')];
			$stripe_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('stripe_color')].'*'.$row[csf('did')]]['measurement']=$row[csf('measurement')];
			$stripe_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('stripe_color')].'*'.$row[csf('did')]]['fabreqtotkg']+=$row[csf('fabreqtotkg')];
			$stripe_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('stripe_color')].'*'.$row[csf('did')]]['yarn_dyed']=$row[csf('yarn_dyed')];
			$stripe_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('stripe_color')].'*'.$row[csf('did')]]['fabric_description']=$row[csf('fabric_description')];
			$stripe_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('stripe_color')].'*'.$row[csf('did')]]['uom']=$row[csf('uom')];
		
			$stripe_arr[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('stripe_color')].'*'.$row[csf('did')]]['style_ref_no']=$style_ref_no;

			$stripe_arr2[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['composition']=$row[csf('composition')];
			$stripe_arr2[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fabric_description']=$row[csf('fabric_description')];
			$stripe_arr2[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['style_ref_no']=$style_ref_no;
			$stripe_arr2[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['construction']=$row[csf('construction')];
			$stripe_arr2[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['gsm_weight']=$row[csf('gsm_weight')];
			$stripe_arr2[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['uom']=$row[csf('uom')];
			$stripe_arr2[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['color_type_id']=$row[csf('color_type_id')];
			//$stripe_arr2[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['dia_width']=$row[csf('dia_width')];
			$stripe_arr2[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fabreqtotkg']+=$row[csf('fabreqtotkg')];
			$stripe_arr2[$row[csf('job_no')]][$row[csf('body_part_id')]][$row[csf('color_number_id')]]['type_uom']=$row[csf('type_uom')];
		}
	
 // print_r($type_uom_arr);

		foreach($type_uom_arr as $uom_id=>$uom_arr){
		?>
			

			<table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
       		 <caption> <strong style="float:left"> Stripe Details (<?=$uom_arr;?>)</strong></caption>
      

            <tr>
                <th width="30"> SL</th>
                <th width="100">Job No</th>
				<th width="100">Body Part</th>
				<!-- <th width="100">Fab. Desc.</th> -->
                <th width="80">Fabric Color</th>
                <th width="70">Fabric Qty(<?=$uom_arr;?>)</th>
                <th width="70">Stripe Color</th>
                <th width="70">Stripe Measurement</th>
				<th  width="70">Stripe Uom</th>
                <th  width="70">Qty.(<?=$uom_arr;?>)</th>

				<th  width="70">Y/D Req.</th>
            </tr>
            <?
				$job_strip_color_rowspan=array();$fab_strip_color_rowspan=array();$color_strip_color_rowspan=array();
			  foreach($stripe_arr as $job_id=>$job_data)
			  {	 $job_row_span=0;
					 foreach($job_data as $body_id=>$body_data)
					 {
						   $fab_row_span=0;
						   foreach($body_data as $color_id=>$color_data)
						   {
								$color_row_span=0;
								foreach($color_data as $strip_color_id=>$color_val)
								{
									$job_row_span++;$fab_row_span++;$color_row_span++;

								}
								$job_strip_color_rowspan[$job_id]=$job_row_span;
								$fab_strip_color_rowspan[$job_id][$body_id]=$fab_row_span;
								$color_strip_color_rowspan[$job_id][$body_id][$color_id]=$color_row_span;
								$color_strip_color_qty_arr[$job_id][$body_id][$color_id]=$stripe_arr2[$job_id][$body_id][$color_id]['fabreqtotkg'];


						   }
					 }
			  }
			 // print_r($job_strip_color_rowspan);


			$i=1;$total_fab_qty=0;$total_fabreqtotkg=$total_color_qty=0;$fab_data_array=array();
            foreach($stripe_arr as $job_id=>$job_data)	{
			 $job_span=1;
			 foreach($job_data as $body_id=>$body_data)    {
			  $fab_span=1;
			   foreach($body_data as $color_id=>$color_data)  {
			     $color_span=1;
				foreach($color_data as $strip_color_key=>$color_val)   {
					//$rowspan=count($color_val['stripe_color']);
					$strip_color_str_arr=explode("*",$strip_color_key);
					$strip_color_id=$strip_color_str_arr[0];

					if($uom_id==$color_val['type_uom'])
					{
					?>
					<tr>
					<?

					if($job_span==1)
					{
					?>
                        <td align="center" rowspan="<? echo $job_strip_color_rowspan[$job_id];?>"> <? echo $i; ?></td>
                        <td align="center" title="<? echo $job_id;?>" rowspan="<? echo $job_strip_color_rowspan[$job_id];?>"> <? echo $job_id; ?></td>
						<?
					}
					if($fab_span==1)
					{
						?>
						<td align="center" rowspan="<? echo $fab_strip_color_rowspan[$job_id][$body_id];?>"> <? echo $body_part[$body_id]; ?></td>
						<!-- <td align="center" rowspan="<? echo $fab_strip_color_rowspan[$job_id][$body_id];?>"> <? //echo $color_val['fabric_description']; ?></td> -->
						<?
					}
					if($color_span==1)
					{
					$color_qty= $color_strip_color_qty_arr[$job_id][$body_id][$color_id];//$color_val['fabreqtotkg'];
					$total_color_qty+=$color_qty;
					?>
                        <td rowspan="<? echo $color_strip_color_rowspan[$job_id][$body_id][$color_id];?>" align="center"> <? echo $color_name_arr[$color_id]; ?></td>
                        <td rowspan="<? echo $color_strip_color_rowspan[$job_id][$body_id][$color_id];?>" align="center"> <? echo number_format($color_qty,2); ?></td>
					<?
					}

						?>
						<td align="center"><?  echo  $color_name_arr[$strip_color_id]; ?></td>
						<td align="center"> <? echo  number_format($color_val['measurement'],2); ?></td>
						<td align="center"> <? echo  $unit_of_measurement[$color_val['uom']]; ?></td>
						<td align="center" title="Stripe Measurement/Tot Stripe Measurement*Fabric Qty(KG)"> <? echo  number_format($color_val['fabreqtotkg'],2); ?></td>

						<td align="center"> <? echo  $yes_no[$color_val['yarn_dyed']]; ?></td>
					</tr>
						<?
					
						$total_fabreqtotkg+=$color_val['fabreqtotkg'];

					$job_span++;$fab_span++;$color_span++;
					}
				  }
				}
				$i++;
			}
			}
			?>
        <tfoot>
        <tr>
            <td align="right" colspan="4">Total </td>
            <td align="center"><? echo  number_format($total_color_qty,2); ?> </td>
            <td></td>
            <td></td>

            <td> </td>
			 <td align="center"><? echo  number_format($total_fabreqtotkg,2); ?> </td>
			<td> </td>
        </tr>
        </tfoot>
   </table>
   <?}?>
   <br/>
    

    <table  style="margin-top: 5px;float: left;"    width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">

		<tr>
			<td colspan="10" align="center">
				<strong>Comments</strong>
			</td>
		</tr>
		<tr>
			<td align="center"> SL </td>
			<td align="center" width="200"  style="word-wrap: break-word;word-break: break-all;"> PO NO </td>
			<td align="center"> Ship Date </td>
			<td align="center">BOM Qty</td>
			<td align="center"> Booking Qty </td>
			<td align="center"> Short Booking Qty </td>
			<td align="center"> Total Booking Qty </td>
			<td align="center"> Balance </td>
			<td align="center"> Comments </td>
		</tr>
		<?
		$po_num_arr=return_library_array("select id,po_number from wo_po_break_down where status_active=1 and is_deleted=0 and id in(select po_break_down_id from wo_booking_dtls where status_active=1 and is_deleted=0 and booking_no='$txt_booking_no')",'id','po_number');
		$is_short_data=sql_select("SELECT a.id, sum(b.grey_fab_qnty) as booking_qty from wo_po_break_down a,wo_booking_dtls b  where a.job_no_mst =b.job_no and  a.id=b.po_break_down_id   and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and b.is_short =1 group by  a.id ");
		foreach($is_short_data as $vals)
		{
			$short_qty_arr[$vals[csf("id")]]=$vals[csf("booking_qty")];
		}

		$booking_data=sql_select("SELECT a.id, sum(b.grey_fab_qnty) as booking_qty from wo_po_break_down a,wo_booking_dtls b  where a.job_no_mst =b.job_no and  a.id=b.po_break_down_id   and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and b.is_short !=1 and b.booking_type=1 group by  a.id  order by a.id");
		foreach($booking_data as $vals)
		{
			$booking_arr[$vals[csf("id")]]=$vals[csf("booking_qty")];
		}

		$po_date=return_library_array("select id,shipment_date from wo_po_break_down where status_active=1 and is_deleted=0",'id','shipment_date');

		$comments_data=sql_select("SELECT min(a.id) as ids,b.po_break_down_id as po_number,sum(d.fin_fab_qnty) as fin_fab_qntys,sum(d.grey_fab_qnty) as grey_fab_qntys,SUM(b.requirment) as precost_grey_qty FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
			WHERE a.job_no=b.job_no and
			a.id=b.pre_cost_fabric_cost_dtls_id
			and
			b.po_break_down_id=d.po_break_down_id and
			b.color_number_id=d.gmts_color_id and
			b.dia_width=d.dia_width and
			b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
			a.job_no=d.job_no and
			a.id=d.pre_cost_fabric_cost_dtls_id
			and
			d.booking_no ='$txt_booking_no' and
			d.job_no in($all_jobs_id) and

			d.status_active=1 and
			d.is_deleted=0 and
			b.cons>0
			group by b.po_break_down_id order by b.po_break_down_id");



		$job_no=$all_jobs_id;
		$condition= new condition();
		if(str_replace("'","",$job_no) !='')
		{
			$condition->job_no("in ($job_no)");
		}
		$condition->init();
		$fabric= new fabric($condition);
		$fabric_costing_qty_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();

		$j=1;
		$total_bom=0;
		$total_book=0;
		$total_short=0;
		$total_short_full=0;
		$total_balance=0;
		foreach($comments_data as $val)
		{
			$po_id=$val[csf('po_number')];
			$woven_qty=array_sum($fabric_costing_qty_arr['woven']['grey'][$po_id]);
			$knit_qty=array_sum($fabric_costing_qty_arr['knit']['grey'][$po_id]);
			$sum_woven_knit=$woven_qty + $knit_qty;


			?>
			<tr>
				<td align="center"><? echo $j;?></td>

				<td align="center"  style="word-wrap: break-word;word-break: break-all;"> <? echo $po_num_arr[$val[csf("po_number")]] ;?> </td>
				<td align="center"> <? echo change_date_format($po_date[$val[csf("po_number")]], "yyyy-mm-dd", "-");?> </td>
				<td align="center"><?  echo $pre= def_number_format($sum_woven_knit,2);  ?> </td>
				<td align="center"><?  echo $bookings= def_number_format($booking_arr[$val[csf("po_number")]],2);  ?> </td>
				<td align="center"> <?echo $short=def_number_format($short_qty_arr[$val[csf("po_number")]],2); ?> </td>
				<td align="center"> <?   $tot_short_book= str_replace(',','',$bookings) +  str_replace(',','',$short) ; echo def_number_format($tot_short_book,2); ?>  </td>
				<td align="center"> <?  $bal =str_replace(',','',$pre)-str_replace(',','',$tot_short_book) ; echo def_number_format($bal,2);  ?> </td>
				<td align="center"> <? if($bal!=0){ if($pre>$tot_short_book){echo "Less ";} else{ echo "Over";} }?> </td>


			</tr>
			<?
			$total_bom +=str_replace(',','',$pre);
			$total_book +=str_replace(',','',$bookings);
			$total_short +=str_replace(',','',$short);
			$total_short_full += str_replace(',','',$tot_short_book);
			$total_balance += str_replace(',','',$bal);

			$j++;
		}
		?>
		<tr>
			<td colspan="3" align="right"> <b> Total </b></td>
			<td align="center"><strong><? echo def_number_format($total_bom,2);?> </strong> </td>
			<td align="center"><strong><? echo def_number_format($total_book,2);?> </strong> </td>
			<td align="center"><strong><? echo def_number_format($total_short,2);?> </strong> </td>
			<td align="center"><strong><? echo def_number_format($total_short_full,2);?> </strong> </td>
			<td align="center"><strong><? echo def_number_format($total_balance,2);?> </strong> </td>
			<td>&nbsp;</td>
		</tr>
	</table>


    <fieldset id="div_size_color_matrix" style="max-width:1000;">
		<?
	    //Query for TNA start-
		$po_id_all=str_replace("'","",$txt_order_no_id);
		$po_num_arr=return_library_array("select id,po_number from wo_po_break_down where id in($po_id_all)",'id','po_number');
		$tna_start_sql=sql_select( "select id,po_number_id,
						(case when task_number=31 then task_start_date else null end) as fab_booking_start_date,
						(case when task_number=31 then task_finish_date else null end) as fab_booking_end_date,
						(case when task_number=60 then task_start_date else null end) as knitting_start_date,
						(case when task_number=60 then task_finish_date else null end) as knitting_end_date,
						(case when task_number=61 then task_start_date else null end) as dying_start_date,
						(case when task_number=61 then task_finish_date else null end) as dying_end_date,
						(case when task_number=64 then task_start_date else null end) as finishing_start_date,
						(case when task_number=64 then task_finish_date else null end) as finishing_end_date,
						(case when task_number=84 then task_start_date else null end) as cutting_start_date,
						(case when task_number=84 then task_finish_date else null end) as cutting_end_date,
						(case when task_number=86 then task_start_date else null end) as sewing_start_date,
						(case when task_number=86 then task_finish_date else null end) as sewing_end_date,
						(case when task_number=110 then task_start_date else null end) as exfact_start_date,
						(case when task_number=110 then task_finish_date else null end) as exfact_end_date,
						(case when task_number=47 then task_start_date else null end) as yarn_rec_start_date,
						(case when task_number=47 then task_finish_date else null end) as yarn_rec_end_date
						from tna_process_mst
						where status_active=1 and po_number_id in($po_id_all)");
		$tna_fab_start=$tna_knit_start=$tna_dyeing_start=$tna_fin_start=$tna_cut_start=$tna_sewin_start=$tna_exfact_start="";
		$tna_date_task_arr=array();
		foreach($tna_start_sql as $row)
		{
			if($row[csf("fab_booking_start_date")]!="" && $row[csf("fab_booking_start_date")]!="0000-00-00")
			{
				if($tna_fab_start=="")
				{
					$tna_fab_start=$row[csf("fab_booking_start_date")];
				}
			}


			if($row[csf("knitting_start_date")]!="" && $row[csf("knitting_start_date")]!="0000-00-00")
			{
				$tna_date_task_arr[$row[csf("po_number_id")]]['knitting_start_date']=$row[csf("knitting_start_date")];
				$tna_date_task_arr[$row[csf("po_number_id")]]['knitting_end_date']=$row[csf("knitting_end_date")];
			}
			if($row[csf("dying_start_date")]!="" && $row[csf("dying_start_date")]!="0000-00-00")
			{
				$tna_date_task_arr[$row[csf("po_number_id")]]['dying_start_date']=$row[csf("dying_start_date")];
				$tna_date_task_arr[$row[csf("po_number_id")]]['dying_end_date']=$row[csf("dying_end_date")];
			}
			if($row[csf("finishing_start_date")]!="" && $row[csf("finishing_start_date")]!="0000-00-00")
			{
				$tna_date_task_arr[$row[csf("po_number_id")]]['finishing_start_date']=$row[csf("finishing_start_date")];
				$tna_date_task_arr[$row[csf("po_number_id")]]['finishing_end_date']=$row[csf("finishing_end_date")];
			}
			if($row[csf("cutting_start_date")]!="" && $row[csf("cutting_start_date")]!="0000-00-00")
			{
				$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_start_date']=$row[csf("cutting_start_date")];
				$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_end_date']=$row[csf("cutting_end_date")];
			}

			if($row[csf("sewing_start_date")]!="" && $row[csf("sewing_start_date")]!="0000-00-00")
			{
				$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_start_date']=$row[csf("sewing_start_date")];
				$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_end_date']=$row[csf("sewing_end_date")];
			}
			if($row[csf("exfact_start_date")]!="" && $row[csf("exfact_start_date")]!="0000-00-00")
			{
				$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_start_date']=$row[csf("exfact_start_date")];
				$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_end_date']=$row[csf("exfact_end_date")];
			}
			if($row[csf("yarn_rec_start_date")]!="" && $row[csf("yarn_rec_start_date")]!="0000-00-00")
			{
				$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_start_date']=$row[csf("yarn_rec_start_date")];
				$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_end_date']=$row[csf("yarn_rec_end_date")];
			}
		}


		?>
        <legend>TNA Information</legend>

        <table width="100%" style="border:1px solid black;font-size:12px; font-family:Arial Narrow;" border="1" cellpadding="2" cellspacing="0" rules="all">
        	<tr>
        		<td rowspan="2" align="center" valign="top">SL</td>
        		<td width="180" rowspan="2"  align="center" valign="top" style="word-wrap: break-word;word-break: break-all;"><b>Order No</b></td>
        		<td colspan="2" align="center" valign="top"><b>Yarn Receive</b></td>
        		<td colspan="2" align="center" valign="top"><b>Knitting</b></td>
        		<td colspan="2" align="center" valign="top"><b>Dyeing</b></td>
        		<td colspan="2" align="center" valign="top"><b>Finish Fabric Prod.</b></td>
        		<td colspan="2" align="center" valign="top"><b>Cutting </b></td>
        		<td colspan="2" align="center" valign="top"><b>Sewing </b></td>
        		<td colspan="2"  align="center" valign="top"><b>Ex-factory </b></td>
        	</tr>
        	<tr>
        		<td width="85" align="center" valign="top"><b>Start Date</b></td>
        		<td width="85" align="center" valign="top"><b>End Date</b></td>
        		<td width="85" align="center" valign="top"><b>Start Date</b></td>
        		<td width="85" align="center" valign="top"><b>End Date</b></td>
        		<td width="85" align="center" valign="top"><b>Start Date</b></td>
        		<td width="85" align="center" valign="top"><b>End Date</b></td>
        		<td width="85" align="center" valign="top"><b>Start Date</b></td>
        		<td width="85" align="center" valign="top"><b>End Date</b></td>
        		<td width="85" align="center" valign="top"><b>Start Date</b></td>
        		<td width="85" align="center" valign="top"><b>End Date</b></td>
        		<td width="85" align="center" valign="top"><b>Start Date</b></td>
        		<td width="85" align="center" valign="top"><b>End Date</b></td>
        		<td width="85" align="center" valign="top"><b>Start Date</b></td>
        		<td width="85" align="center" valign="top"><b>End Date</b></td>

        	</tr>
            <?
			$i=1;
			foreach($tna_date_task_arr as $order_id=>$row)
			{

				?>
				<tr>
					<td><? echo $i; ?></td>
					<td style="word-wrap: break-word;word-break: break-all;"><? echo $po_num_arr[$order_id]; ?></td>
					<td align="center"><? echo change_date_format($row['yarn_rec_start_date']); ?></td>
					<td  align="center"><? echo change_date_format($row['yarn_rec_end_date']); ?></td>
					<td align="center"><? echo change_date_format($row['knitting_start_date']); ?></td>
					<td  align="center"><? echo change_date_format($row['knitting_end_date']); ?></td>
					<td align="center"><? echo change_date_format($row['dying_start_date']); ?></td>
					<td align="center"><? echo change_date_format($row['dying_end_date']); ?></td>
					<td align="center"><? echo change_date_format($row['finishing_start_date']); ?></td>
					<td align="center"><? echo change_date_format($row['finishing_end_date']); ?></td>
					<td align="center"><? echo change_date_format($row['cutting_start_date']); ?></td>
					<td align="center"><? echo change_date_format($row['cutting_end_date']); ?></td>
					<td align="center"><? echo change_date_format($row['sewing_start_date']); ?></td>
					<td align="center"><? echo change_date_format($row['sewing_end_date']); ?></td>
					<td align="center"><? echo change_date_format($row['exfact_start_date']); ?></td>
					<td align="center"><? echo change_date_format($row['exfact_end_date']); ?></td>
				</tr>
                <?
				$i++;
			}
			?>

        </table>
    </fieldset>

    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" style="font-family:Arial Narrow;">
            <thead>
                <tr>
                    <th width="3%"></th><th width="97%" align="left"><u>Special Instruction</u></th>
                </tr>
            </thead>
            <tbody>
            <?
            $data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no='$txt_booking_no'");// quotation_id='$data'
            if ( count($data_array)>0)
            {
                $i=0;
                foreach( $data_array as $row )
                {
                    $i++;
                    ?>
                        <tr id="settr_1" valign="top">
                            <td style="vertical-align:top">
                            <? echo $i;?>
                            </td>
                            <td>
                           <strong style="font-size:14px"> <? echo $row[csf('terms')]; ?></strong>
                            </td>
                        </tr>
                    <?
                }
            }
            ?>
        </tbody>
    </table>


	<br>
	<?
		echo signature_table(121, $cbo_company_name, "1000px");
	?>

  </div>
	<?  
} 

?>

