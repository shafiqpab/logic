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
$cbo_company_name = str_replace("'","",$cbo_company_name);

//$lead_time=return_library_array("select task_template_id,lead_time from tna_task_template_details group by task_template_id,lead_time","task_template_id","lead_time");
$tna_process_type=return_field_value("tna_process_type"," variable_order_tracking","variable_list=31 and company_name='".$cbo_company_name."'"); 


if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
	die; 	 
}

if($action=="generate_tna_report")
{
	$selected_task_id=str_replace("'","",$tna_task_id);
	$mod_sql= sql_select("select id,task_catagory,task_name,task_short_name,task_type,completion_percent from lib_tna_task where is_deleted = 0 and status_active=1 order by task_sequence_no asc");
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
	
	if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.task_start_date between $txt_date_from and $txt_date_to";
	
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if($txt_job_no=="") $txt_job_no=""; else $txt_job_no=" and a.job_no_prefix_num ='$txt_job_no'";
	$txt_order_no=str_replace("'","",$txt_order_no);
	if($txt_order_no=="") $txt_order_no=""; else $txt_order_no=" and c.po_number ='$txt_order_no'";
	
	$txt_style_ref_no=str_replace("'","",$txt_style_ref_no);
	if($txt_style_ref_no=="") $txt_style_ref_no=""; else $txt_style_ref_no=" and a.style_ref_no ='$txt_style_ref_no'";
	
	if(str_replace("'","",$cbo_shipment_status)==4){
			$shipment_status_con=" and b.shiping_status=3";
		} 
	elseif(str_replace("'","",$cbo_shipment_status)==1) {
			$shipment_status_con=" and b.shiping_status !=3";
		}
	else{
			$shipment_status_con="";
		}
	if($selected_task_id)$task_con=" and b.task_number in($selected_task_id)";else $task_con="";
	
	
	$tna_all_task=implode(",",$tna_task_id);
	
		$sql = "SELECT a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.set_smv, a.job_no_prefix_num, a.dealing_marchant, b.po_number_id
		FROM  wo_po_details_master a,tna_process_mst b,wo_po_break_down c 
		WHERE a.job_no=b.job_no and c.id=b.po_number_id and a.job_no=c.job_no_mst and b.job_no=c.job_no_mst $task_con $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_style_ref_no $txt_order_no and a.is_deleted = 0 and a.status_active=1"; 
	//echo $sql;

	$result = sql_select( $sql ) ;
	$wo_po_details_master = array();
	$po_no_arr=array();
	$job_no_arr=array();
	foreach( $result as  $row ) 
	{	
		$wo_po_details_master[$row[csf('po_number_id')]]['company_name']=$row[csf('company_name')];
		$wo_po_details_master[$row[csf('po_number_id')]]['buyer_name']=$row[csf('buyer_name')];
		$wo_po_details_master[$row[csf('po_number_id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$wo_po_details_master[$row[csf('po_number_id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
		$wo_po_details_master[$row[csf('po_number_id')]]['dealing_marchant']=$row[csf('dealing_marchant')];
		$wo_po_details_master[$row[csf('po_number_id')]]['set_smv']= $row[csf('set_smv')];
		$po_no_arr[$row[csf('po_number_id')]]=$row[csf('po_number_id')];
		$job_no_arr[$row[csf('job_no')]]=$row[csf('job_no')];
		
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
		$sql ="select a.po_number_id, a.job_no, a.shipment_date, a.template_id, a.po_receive_date,b.insert_date,b.po_number, b.file_no, b.grouping,b.po_quantity,";
		$i=1;
	
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c) $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id, ";
			else $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id ";
			$i++;
		}
		
		$sql .=" from tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id and a.po_number_id in( $po_no_arr_all ) and a.job_no in ($job_no_all) $shipment_status_con  and b.status_active=1  and b.po_quantity>0 $order_status_cond group by a.po_number_id,a.job_no,b.insert_date,b.po_number, b.file_no, b.grouping order by a.shipment_date,a.po_number_id,a.job_no"; 
	}
	else
	{
		$sql ="select a.po_number_id, a.job_no, max(a.shipment_date) as shipment_date, a.template_id, max(a.po_receive_date) as po_receive_date,b.insert_date,b.po_number, b.file_no, b.grouping,b.po_quantity,";
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
		$sql .=" from  tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id $sql_order_con $sql_job_con $shipment_status_con and b.status_active=1 and b.po_quantity>0 $order_status_cond group by a.po_number_id,a.job_no,a.template_id,a.shipment_date,b.insert_date,b.po_number, b.file_no, b.grouping,b.po_quantity order by a.shipment_date,a.po_number_id,a.job_no"; 
	}
	
	    //echo $sql;
	
	$lead_time_array=return_library_array("select task_template_id,lead_time from tna_task_template_details group by lead_time,task_template_id","task_template_id",'lead_time');
	if($db_type==0)
	{
		$tast_tmp_id_arr=return_library_array("select task_template_id, group_concat(tna_task_id) as tna_task_id  from tna_task_template_details group by task_template_id","task_template_id",'tna_task_id');
	}
	else
	{
		$tast_tmp_id_arr=return_library_array("select task_template_id, listagg(cast(tna_task_id as varchar(4000)),',') within group(order by tna_task_id) as tna_task_id  from tna_task_template_details group by task_template_id","task_template_id",'tna_task_id');
	}
	
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
						if(count($tna_task_array)==$i) echo '<th width="80">Start</th><th width="80"> Finish</th>'; else echo '<th width="80">Start</th><th width="80"> Finish</th>';
					}
					echo '</tr>';
					 
				?>
                </thead>
                </table>
         </div>
        	<div style="overflow-y:scroll; max-height:360px; width:<? echo $width+170; ?>px;" align="left" id="scroll_body">
          	<table width="<? echo $width+140; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
       
    <?
	
	$tid=0;$i=1;$count=0;$kid=1;$h=0;$tot_po_qty=0;
	//$new_job_no=array();
	foreach ($data_sql as $row)
	{
		/*if (!in_array($row[csf('job_no')],$new_job_no))
		{
			$new_job_no[]=$row[csf('job_no')];
		}*/
		
		
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
                    <td width="80" rowspan="3"><? echo $team_member_name[$wo_po_details_master[$row[csf('po_number_id')]]['dealing_marchant']]; ?></td>
                    <td width="70" rowspan="3"><? echo $buyer_name[$wo_po_details_master[$row[csf('po_number_id')]]['buyer_name']]; ?></td>
                    <td width="110" rowspan="3" align="center"><p>
						<? 
							echo "<a href='#report_details' style='color:#990000' onclick= \"progress_comment_popup('".$row[csf('job_no')]."','".$row[csf('po_number_id')]."','".$row[csf('template_id')]."','".$tna_process_type."');\">".$row[csf('po_number')]."</a>";
						
                        ?>
                   </p> </td>
                    
                    <td width="90" rowspan="3" align="right"><p>
						<?
							//$po_qty=return_field_value("po_quantity", "wo_po_break_down", "id='".$row[csf('po_number_id')]."' and status_active=1 and is_deleted=0"); 
							$po_qty=$row[csf('po_quantity')];
							echo number_format($po_qty);
							$tot_po_qty+=$po_qty;
						?>
                        </p>
                    </td>
                    <td width="30" rowspan="3" align="center"><? echo number_format($wo_po_details_master[$row[csf('po_number_id')]]['set_smv'],2); ?></td>
                    
                    <td width="120"  rowspan="3" title="<? echo $row[csf('job_no')]; ?>"><p><? echo $wo_po_details_master[$row[csf('po_number_id')]]['style_ref_no']; ?></p></td>
                     <td width="40" rowspan="3" title=""><? echo $wo_po_details_master[$row[csf('po_number_id')]]['job_no_prefix_num']; ?></td>
                     
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
                    <td width="100" rowspan="3" title="<? echo $lead_timee."; "." PO. Rec. Date: ".change_date_format($row[csf('po_receive_date')]); ?>"><? echo change_date_format($row[csf('shipment_date')])."<br>"." ".$lead_timee."<br>"." PO Lead Time:".$po_lead_time;  ?></td>
                    <td width="60" rowspan="3" align="center"><? echo date("d-m-Y",strtotime($row[csf('insert_date')]));?></td>
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
							
						else
							echo '<td id="'.$idd.'1" title="Click Here to Edit Date"  '.$function.' width="80" bgcolor="'.$bgcolor.'">'.($new_data[0]== "" || $new_data[0]=="0000-00-00" ? "" : change_date_format($new_data[0])).'</td><td id="'.$idd.'2" title="Click Here to Edit Date" '.$function.' width="80" bgcolor="'.$bgcolor1.'" title="'.$new_data[6].'">'.($new_data[1]== "" || $new_data[1]=="0000-00-00" ? "" : change_date_format($new_data[1])).'</td>';
						
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
    <div style="width:<? echo $width+140; ?>px;" align="left">
         <table width="100%" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <tfoot>
                <th width="40"></th>
                <th width="80"></th>
                <th width="70"></th>
                <th width="110">Total</th>
                <th width="89" id="total_po_qty" align="right"><? echo number_format($tot_po_qty,2);?></th>
                <th width="30"></th>
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


//---------------------------------------------------------------------------------------------------------




if($action=='task_surch')
{
	
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	?>
    <script>
		
        function setCookie(key, value) {
            var expires = new Date();
            expires.setTime(expires.getTime() + (1 * 24 * 60 * 60 * 1000));
            document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
        }

        function getCookie(key) {
            var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
            return keyValue ? keyValue[2] : '';
        }		
		
		//alert(getCookie('task_id_list'));
		
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	
		function check_all_data() {
			var tbl_row_count = document.getElementById('list_view').rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('.tr_' + i).attr('onclick');
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
		var cookieID='';
		function js_set_value( strCon ) 
		{
			var splitSTR = strCon.split("_");
			var str_or = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			
			toggle( document.getElementById( 'tr_' + selectID ), '#FFFFCC' );
			
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

			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name ); 
			$('#txt_selected_no').val( num );
			cookieID=id;
			
		}
		
		function window_close()
		{  	var cookieHistory='';
			if(getCookie('task_id_list').length<1000 && getCookie('task_id_list')!=''){ cookieHistory=getCookie('task_id_list')+',';}
			if(cookieID){setCookie('task_id_list',cookieHistory+cookieID);}
			parent.emailwindow.hide();
		}
    </script>
    <?
	$task_id_list_arr=explode(',',$_COOKIE['task_id_list']);
	foreach($task_id_list_arr as $v){
		if($v>0)$taskArr[$v]++;	
	}
	arsort($taskArr);
	
	//var_dump($taskArr);
	
	
	$company=str_replace("'","",$company);
	
	$sql =sql_select("select id,task_name,task_short_name from lib_tna_task where status_active=1 and is_deleted=0 order by task_sequence_no");
		
		$dataArr=array();
		foreach($sql as $row)
		{
			if($taskArr[$row[csf("task_name")]]!=''){$dataArrangeArr[$row[csf("task_name")]]=$row;}
		}
		
		foreach($taskArr as $key=>$v)
		{
			$dataArr[]=$dataArrangeArr[$key];
		}
		
		foreach($sql as $row)
		{
			if($taskArr[$row[csf("task_name")]]==''){$dataArr[]=$row;}
		}
		
		
		
		//ksort($dataArr);
	
	 
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
		foreach($dataArr as $row)
		{
			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			
			?>
        	<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $i; ?>_<? echo $row[csf("task_name")]; ?>_<? echo $tna_task_name[$row[csf("task_name")]]; ?>')" id="tr_<? echo $row[csf("task_name")]; ?>" class="tr_<? echo $i; ?>" style="cursor:pointer;">
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
	setFilterGrid("list_view",-1);
	
	var style_no='<? echo $tna_task_id_no;?>';
	var style_id='<? echo $tna_task_id;?>';
	var style_des='<? echo $tna_task;?>';
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




if($action=="edit_update_tna")
{
	
	
	//echo "$mid";
	echo load_html_head_contents("TNA","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	
	/*$plnread=""; $actread="";
	if( $type==1 )
		$actread="disabled='disabled'";
	else
		$actread="disabled='disabled'";
		
		if( $type==2 )
		$plnread="disabled='disabled'";
	else
		$actread="disabled='disabled'";*/
		
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		
		$sql ="select a.po_number,b.job_no,b.buyer_name,b.style_ref_no from  wo_po_break_down a,wo_po_details_master b where a.id=$po_id and a.job_no_mst=b.job_no";
		$result=sql_select($sql);
		
		$tna= "select template_id,task_number,task_start_date,task_finish_date,actual_start_date,actual_finish_date,plan_start_flag,plan_finish_flag from  tna_process_mst where id=$mid ";
		$tna_result=sql_select($tna);
		
		$mod_sql= sql_select("select id,task_catagory,task_name,task_short_name,task_type,completion_percent from lib_tna_task where is_deleted = 0 and status_active=1 order by task_sequence_no asc");
		$tna_task_array=array();
		foreach ($mod_sql as $row)
		{	
			$tna_task_array[$row[csf("task_name")]] = $row[csf("task_short_name")];
		}
		
		//History data start------------------------
		$tna_history_sql= "select id, template_id,task_number, job_no, po_number_id, task_start_date, task_finish_date, actual_start_date, actual_finish_date,plan_start_flag,plan_finish_flag from  tna_plan_actual_history where template_id=".$tna_result[0][csf('template_id')]." and task_number=".$tna_result[0][csf('task_number')]." and po_number_id=$po_id and job_no='".$result[0][csf('job_no')]."'";
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
		 
		var data="action=save_update_delete&operation="+operation+'&start_flag='+start_flag+'&finish_flag='+finish_flag+get_submitted_data_string('txt_actual_start_date*txt_actual_finish_date*txt_update_tna_id*txt_plan_start_date*txt_plan_finish_date*txt_update_tna_type*txt_plan_actual_history',"../../../");
		//alert (data);return;
		freeze_window(operation);
		http.open("POST","task_wise_tna_report_controller.php",true);
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
            	<input type="text" <? if($type==2) echo "disabled='disabled'";  ?> name="txt_plan_start_date" id="txt_plan_start_date" class="datepicker" style="width:100px" value="<? if($ts_history_con==1){echo change_date_format($tna_history[0][csf('task_start_date')]);} else if($ts_result_con==0){echo "";}else{ echo change_date_format($tna_result[0][csf('task_start_date')]);} ?>" />
            </td>
            
            <td align="right">Plan Finish Date</td>
            <td>
            	<input type="text" <? if($type==2) echo "disabled='disabled'";  ?> name="txt_plan_finish_date" id="txt_plan_finish_date" class="datepicker" style="width:100px"  value="<? if($tf_history_con==1){echo change_date_format($tna_history[0][csf('task_finish_date')]);} else if($tf_result_con==0){echo "";}else echo change_date_format($tna_result[0][csf('task_finish_date')]); ?>"/>
            </td>
        </tr>
        
         <tr>
        	<td align="right">Actual Start Date</td>
            <td>
            	<input type="text" <? if($type==1) echo "disabled='disabled'";  ?> name="txt_actual_start_date" id="txt_actual_start_date" class="datepicker" style="width:100px" value="<?  if($as_history_con==1){echo change_date_format($tna_history[0][csf('actual_start_date')]);} else if($as_result_con==0){echo "";}else echo change_date_format($tna_result[0][csf('actual_start_date')]); ?>" />
            </td>
            <td align="right">Actual Finish Date</td>
            <td>
            	<input type="text" <? if($type==1) echo "disabled='disabled'";  ?> name="txt_actual_finish_date" id="txt_actual_finish_date" class="datepicker" style="width:100px" value="<?   if($af_history_con==1){echo change_date_format($tna_history[0][csf('actual_finish_date')]);} else if($af_result_con==0){echo "";}else echo change_date_format($tna_result[0][csf('actual_finish_date')]); ?>" />
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

if($action=="save_update_delete")
{
		
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//echo "shajjad_".$txt_actual_start_date."**".$txt_actual_finish_date."**".$txt_plan_start_date."**".$txt_plan_finish_date;
	$update_tna_type=str_replace("'",'',$txt_update_tna_type);
	
	if ($operation==1)  
	{		
		$con = connect();
		if($db_type==0)
		{
			  mysql_query("BEGIN");
		}
		 
		$id=str_replace("'",'',$txt_update_tna_id);
		
		//echo $id;die;
		//echo "INSERT INTO tna_process_mst (".$field_array.") VALUES ".$data_array;die;

		if($update_tna_type==1)
		{
			
			$field='';$data='';
			if($start_flag==1){$field="*plan_start_flag";$data="*1";}
			if($finish_flag==1){$field.="*plan_finish_flag";$data.="*1";}
			
			
			$field_array1="task_start_date*task_finish_date".$field;
			$data_array1="".$txt_plan_start_date."*".$txt_plan_finish_date.$data."";
			
			$rID=sql_update("tna_process_mst",$field_array1,$data_array1,"id",$id,1);
			
			//history process  start-----------------------------------------;
			list($hmid,$htmp_id,$htask_id,$hpo_id,$hjob_id)=explode("_",$txt_plan_actual_history);
			if(str_replace("'","",$hmid))
			{
				$rID=sql_update("tna_plan_actual_history",$field_array1,$data_array1,"id",str_replace("'","",$hmid),1);
			}
			else
			{
				$hid=return_next_id( "id", "tna_plan_actual_history", 1 ) ;
				$field_array="id, template_id,task_number, job_no, po_number_id, task_start_date, task_finish_date, plan_start_flag,plan_finish_flag,status_active,is_deleted";
				$data_array="(".$hid.",".$htmp_id.",".$htask_id.",'".str_replace("'","",$hjob_id)."',".$hpo_id.",".$txt_plan_start_date.",".$txt_plan_finish_date.",".$start_flag.",".$finish_flag.",'1','0')";
				$rID=sql_insert("tna_plan_actual_history",$field_array,$data_array,1);
				
			}
			//history process end-----------------------------------------;
			
		}
		else
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
				$field_array="id, template_id,task_number,job_no, po_number_id, actual_start_date, actual_finish_date, status_active, is_deleted";
				$data_array="(".$hid.",".$htmp_id.",".$htask_id.",'".str_replace("'","",$hjob_id)."',".$hpo_id.",".$txt_actual_start_date.",".$txt_actual_finish_date.",'1','0')";
				$rID=sql_insert("tna_plan_actual_history",$field_array,$data_array,1);
			
			}
			//history process end-----------------------------------------;

			
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
	$lead_time_array=return_library_array("select task_template_id,lead_time from tna_task_template_details group by lead_time,task_template_id","task_template_id",'lead_time');

	$sql ="select b.company_name,b.buyer_name,a.po_number,b.job_no,b.style_ref_no,b.gmts_item_id,a.po_received_date,a.shipment_date,(po_quantity*total_set_qnty) as po_qty_pcs, set_smv from  wo_po_break_down a,wo_po_details_master b where a.id=$po_id and a.job_no_mst=b.job_no";
	$result=sql_select($sql);
	
	
	$tna_task_id=array();
	$plan_start_array=array();
	$plan_finish_array=array();
	$actual_start_array=array();
	$actual_finish_array=array();
	$notice_start_array=array();
	$notice_finish_array=array();
	
	 $task_sql=sql_select("select a.task_number,a.task_start_date,a.task_finish_date,a.actual_start_date,a.actual_finish_date,a.notice_date_start,a.notice_date_end from tna_process_mst a, lib_tna_task b where a.task_number=b.task_name and a.po_number_id='$po_id' and b.status_active=1 and b.is_deleted=0 order by b.task_sequence_no asc");
	
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
	
	$execution_time_sql= sql_select("select for_specific, tna_task_id, execution_days from tna_task_template_details");
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
	
	$lead_time=return_library_array("select task_template_id,lead_time from tna_task_template_details group by task_template_id,lead_time","task_template_id","lead_time");
		


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
				
				if (comments!="" || mrc_comments!="")
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
			http.open("POST","task_wise_tna_report_controller.php",true);
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
				$.post('task_wise_tna_report_controller.php?job_no='+'<? echo $job_no; ?>'+'&po_id='+<? echo $po_id; ?>+'&template_id='+<? echo $template_id; ?>+'&tna_process_type='+<? echo $tna_process_type; ?>,
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
			  $("#auto_id").load("task_wise_tna_report_controller.php");// a function which will load data from other file after x seconds
		  }
		
		function openmypage(i)
		{	
			var title = 'TNA Progress Comment';
			
			var txtcomments = document.getElementById(i).value;
			//var data='additional_info='+additional_info;
			//alert(txtcomments);return;
			
			var page_link = 'task_wise_tna_report_controller.php?data='+txtcomments+'&action=comments_popup';
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
                            <td align="center"><Input name="txtmercomments[]" class="text_boxes" ID="txtmercomments_<?php echo $i; ?>" value="<?php  echo $mer_comments_array[$key];//substr($smp_data[$key],0,-1); ?>" onDblClick="openmypage('txtmercomments_<?php echo $i; ?>'); return false" style="width:90%;" autocomplete="off" readonly placeholder="Double Click" /></td>
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
    <? 
	
	/*$html="<table width='1000' border='1' rules='all' class='rpt_table'>
    	<tr><td colspan='6' align='center'><b><font size='+1'>TNA Progress Comment</font></b></td></tr>
    </table>";
		$html.="<table width='1000' border='1' rules='all' class='rpt_table'>";
    	
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
            	".$job_no." 
            	".$po_id." 
               ".$template_id."
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
                                        if($actual_start_array[$key]=="0000-00-00") echo "";
                                        else   change_date_format($actual_start_array[$key]);
                                    }
                                    else
                                    {
                                        if($actual_start_array[$key]=="") echo "";
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
								  
                                     $start_diff  $start;
                                
                            </td>
                            <td align='center' width='70' bgcolor=".$bgcolor6.">
                                 
                                     $finish_diff  $finish;
                               
                            </td>
                            <td width='150'>
                            	".$responsible_array[$key]."
                            	
                            </td>
                            <td width='120' align='center'>". $comments_array[$key]."</td>
                            <td align='center'>".$smp_data[$key].",0,-1.'</td>
                        </tr>";
                       
                        }
                        
                    $html.="</tbody>
                </table>
                </div>
    		</td>
        </tr>
    </table>";*/
				//echo $html;
				?>
    
    
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
			
			if(str_replace("'","",$$txtcomments)!="" || str_replace("'","",$$txtmercomments)!="")
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
			
			if(str_replace("'","",$$txtcomments)!="" || str_replace("'","",$$txtmercomments)!="")
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
	
	 $task_sql=sql_select("select a.task_number,a.task_start_date,a.task_finish_date,a.actual_start_date,a.actual_finish_date,a.notice_date_start,a.notice_date_end from tna_process_mst a, lib_tna_task b where a.task_number=b.task_name and a.po_number_id='$po_id' order by b.task_sequence_no asc");
	
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

	
	
	$lead_time_array=return_library_array("select task_template_id,lead_time from tna_task_template_details group by lead_time","task_template_id",'lead_time');
	
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
	
	$execution_time_sql= sql_select("select tna_task_id, execution_days from tna_task_template_details");
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
	
	$lead_time=return_library_array("select task_template_id,lead_time from tna_task_template_details group by task_template_id,lead_time","task_template_id","lead_time");
	
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


?>

