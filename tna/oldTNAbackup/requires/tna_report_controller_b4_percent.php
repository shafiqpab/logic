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

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
	die; 	 
}

if($action=="generate_tna_report")
{
	
	if($db_type==0) $blank_date="0000-00-00"; else $blank_date=""; 
	
	 //echo "dds****";
	$mod_sql= sql_select("select id,task_catagory,task_name,task_short_name,task_type,completion_percent from lib_tna_task order by task_sequence_no asc");
	$tna_task_array=array();
	$tna_task_id=array();
	$tna_task_cat=array();
	$tna_task_name=array();
	//$tna_task_detls=array();
	foreach ($mod_sql as $row)
	{
		$tna_task_id[]=$row[csf("id")];
		$tna_task_array[$row[csf("id")]] =$row[csf("task_short_name")];
		$tna_task_cat[$row[csf("id")]]=$row[csf("task_catagory")];
		//$tna_task_detls[$row[csf("id")]]=$row[csf("task_category")];
		$tna_task_name[$row[csf("id")]]=$row[csf("task_name")];
	}
	if(str_replace("'","",$cbo_company_name)==0) $cbo_company_name=""; else $cbo_company_name=" and a.company_name = $cbo_company_name";
	if(str_replace("'","",$cbo_buyer_name)==0) $cbo_buyer_name=""; else $cbo_buyer_name=" and a.buyer_name = $cbo_buyer_name";
	if(str_replace("'","",$cbo_team_member)==0) $cbo_team_member=""; else $cbo_team_member=" and a.dealing_marchant = $cbo_team_member";
	if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") $date_range=""; else $date_range=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
	
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if($txt_job_no=="") $txt_job_no=""; else $txt_job_no=" and a.job_no_prefix_num ='$txt_job_no'";
	$txt_order_no=str_replace("'","",$txt_order_no);
	if($txt_order_no=="") $txt_order_no=""; else $txt_order_no=" and b.po_number ='$txt_order_no'";
	$txt_style_ref_no=str_replace("'","",$txt_style_ref_no);
	if($txt_style_ref_no=="") $txt_style_ref_no=""; else $txt_style_ref_no=" and a.style_ref_no ='$txt_style_ref_no'";
	//**txt_date_from*txt_date_to*txt_job_no
	
	
	$tna_all_task=implode(",",$tna_task_id);
	$sql = "SELECT a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_no_prefix_num,a.dealing_marchant,b.id,b.po_number FROM  wo_po_details_master a,  wo_po_break_down b WHERE a.job_no=b.job_no_mst $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no";  //$cbo_company_name $cbo_buyer_name $txt_job_no $cbo_team_name  and a.job_no='ASL-13-00173'
 //echo $sql; die;
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
		$wo_po_details_master[$row[csf('job_no')]][$row[csf('id')]]=$row[csf('po_number')];
		$po_no_arr[]=$row[csf('id')];
		$job_no_arr[]=$row[csf('job_no')];
		
	}
	
	/*
UPDATE  lib_tna_task AS t1, tna_task_template_details AS t2 
  SET
t1.sequence_no=t2.sequence_no
WHERE t1.id= t2.tna_task_id  
 */
 
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
	
	//print_r($buyer_name);die;
	
	$po_no_arr_all=implode(",",$po_no_arr); if($po_no_arr_all!="") $po_no_arr_all .=",0"; else $po_no_arr_all .="0"; 
	$job_no_all="'".implode("','",$job_no_arr)."'";
	$lead_time=return_library_array("select task_template_id,lead_time from tna_task_template_details group by task_template_id,lead_time","task_template_id","lead_time");
	$c=count($tna_task_id);
	
	if($db_type==0)
	{
		$sql ="select po_number_id,job_no,shipment_date,template_id,po_receive_date,";
		$i=1;
	
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c) $sql .="max(CASE WHEN CONCAT(task_number) = '".$id."' THEN concat(actual_start_date,'_',actual_finish_date,'_',task_start_date,'_',task_finish_date,'_',notice_date_start,'_',notice_date_end,'_',remarks,'_',id)  END ) as status$id, ";
			else $sql .="max(CASE WHEN CONCAT(task_number) = '".$id."' THEN concat(actual_start_date,'_',actual_finish_date,'_',task_start_date,'_',task_finish_date,'_',notice_date_start,'_',notice_date_end,'_',remarks,'_',id)  END ) as status$id ";
			$i++;
		}
		
		$sql .=" from tna_process_mst where po_number_id in( $po_no_arr_all ) and job_no in ($job_no_all) group by po_number_id,job_no order by shipment_date,po_number_id,job_no"; 
	}
	else
	{
		$sql ="select po_number_id,job_no,max(shipment_date) as shipment_date,template_id,max(po_receive_date) as po_receive_date,";
		$i=1;
		
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c) $sql .="max(CASE WHEN task_number = '".$id."' THEN actual_start_date || '_' || actual_finish_date || '_' || task_start_date || '_' || task_finish_date ||'_' || notice_date_start || '_' || notice_date_end || '_' || remarks || '_' || id  END ) as status$id, ";
			
			else $sql .="max(CASE WHEN task_number = '".$id."' THEN actual_start_date || '_' || actual_finish_date || '_' || task_start_date || '_' || task_finish_date || '_' || notice_date_start || '_' || notice_date_end || '_' || remarks || '_' || id  END ) as status$id ";
			
			$i++;
		}
		
		$sql .=" from  tna_process_mst where po_number_id in( $po_no_arr_all ) and job_no in ($job_no_all)  group by po_number_id,job_no,template_id order by shipment_date,po_number_id,job_no"; 
	}
	
	
	//job_no in ('13-00052','13-00051','13-00049') 
	 //echo 	 ($sql);
	$data_sql= sql_select($sql);
	
	$width=(count($tna_task_id)*160)+850;
	//print_r($data_sql); die;
	//echo "saju1_".$width; die;
	
	//$sql ="select a.po_number,b.job_no,b.buyer_name,b.style_ref_no,b.job_no_prefix from  wo_po_break_down a,wo_po_details_master b where a.id=$po_no_arr_all and a.job_no_mst=b.job_no";
	//echo $sql;
	
	?>
    <div style="width:<? echo $width+200; ?>px" align="left">
    <table width="<? echo $width+140; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
    	<thead>
        	<tr>
            	<th width="60" rowspan="2">SL</th><th width="120" rowspan="2">Merchant</th><th width="120" rowspan="2">Buyer Name</th><th width="120" rowspan="2">PO Number</th><th width="100" rowspan="2">PO Qty.</th><th width="120" rowspan="2">Style Ref.</th> <th width="120" rowspan="2">Job No.</th><th width="100" rowspan="2">Shipment Date</th>
                
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
         
         <? //echo "saju1_".count($tna_task_array); die; ?>
         
        	<div style="overflow-y:scroll; max-height:400px; width:<? echo $width+170; ?>px;" align="left" id="scroll_body">
          	<table width="<? echo $width+140; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
       
    <?
	
	$tid=0;
	$i=1;
	$count=0;
	$kid=1;
	$new_job_no=array();
	$h=0;
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
			 			if ($h%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							
							//echo $wo_po_details_master[$row[csf('job_no')]][csf('dealing_marchant')]."**";
		?>
        		<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onClick="change_color('tr_<? echo $h;?>','<? echo $bgcolor;?>')" id="tr_<? echo $h; ?>">
                    <td width="60" rowspan="3"><? echo $kid++;?></td>
                    <td width="120" rowspan="3"><? echo $row[csf('po_number_id')]."=".$team_member_name[$wo_po_details_master[$row[csf('job_no')]][csf('dealing_marchant')]]; ?></td>
                    <td width="120" rowspan="3"><? echo $buyer_name[$wo_po_details_master[$row[csf('job_no')]][csf('buyer_name')]]; ?></td>
                    <td width="120" rowspan="3" align="center">
						<? 
                            //echo $wo_po_details_master[$row[csf('job_no')]][$row[csf('po_number_id')]]; 
							echo "<a href='#report_details' style='color:#990000' onclick= \"progress_comment_popup('".$row[csf('job_no')]."','".$row[csf('po_number_id')]."','".$row[csf('template_id')]."');\">".$wo_po_details_master[$row[csf('job_no')]][$row[csf('po_number_id')]]."</a>";
							
                        ?>
                    </td>
                    
                    <td width="100" rowspan="3" align="right">
						<?
							$po_qty=return_field_value("po_quantity", "wo_po_break_down", "id='".$row[csf('po_number_id')]."' and status_active=1 and is_deleted=0"); 
							echo number_format($po_qty,2); 
						?>
                    </td>
                    
                    <td width="120"  rowspan="3" title="<? echo $row[csf('job_no')]; ?>"><p><? echo $wo_po_details_master[$row[csf('job_no')]][csf('style_ref_no')]; ?></p></td>
                     <td width="120" rowspan="3" title=""><? echo $wo_po_details_master[$row[csf('job_no')]][csf('job_no_prefix_num')]; ?></td>
                    <td width="100" rowspan="3" title="<? echo "Lead Time: ".$lead_time[$row[csf('template_id')]]."; "." Rec. Date: ".change_date_format($row[csf('po_receive_date')]); ?>"><? echo change_date_format($row[csf('shipment_date')]);  ?></td>
                    <td width="90">Plan</td>
                <?
 
		//(actual_start_date,'_',actual_finish_date,'_',task_start_date,'_',task_finish_date,'_',notice_date_start,'_',notice_date_end,'_',remarks
		//$new_data : 0->actual_start_date, 1->actual_finish_date, 2->task_start_date, 3->task_finish_date, 4->notice_date_start, 5->notice_date_end
				//job_no 	 	po_receive_date 	
					$i=0;
					 foreach($tna_task_id as $vid=>$key)
					 {
						 $i++;
						
						if ( $new_approval_arr[$row[csf('job_no')]][$key]=="") $new_data=explode("_",$row[csf('status').$key]); 
						else $new_data=explode("_",$new_approval_arr[$row[csf('job_no')]][$key]);
						if($new_data[7]!="") $function="onclick='update_tna_process(1,$new_data[7],".$row[csf('po_number_id')].")'"; else $function="";
						if(count($tna_task_id)==$i)
						 //echo $i."-".count($tna_task_id)."=";
							/*echo '<td  width="80" '.$function.'>'.($new_data[2]== "" ? "" : change_date_format($new_data[2])).'</td><td width="80" '.$function.'> '.($new_data[3]== "" ? "" : change_date_format($new_data[3])).'</td>';*/
							
							echo '<td  width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? "" : change_date_format($new_data[2])).'</td><td '.$function.'> '.($new_data[3]== ""  || $new_data[3]=="0000-00-00"? "" : change_date_format($new_data[3])).'</td>';
							
						 else
							/*echo '<td width="80" '.$function.'>'.($new_data[2]== "" ? "" : change_date_format($new_data[2])).'</td><td width="80" '.$function.'> '.($new_data[3]== ""  ? "" : change_date_format($new_data[3])).'</td>';*/
							
							echo '<td width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? "" : change_date_format($new_data[2])).'</td><td width="80" '.$function.'> '.($new_data[3]== ""  || $new_data[3]=="0000-00-00"? "" : change_date_format($new_data[3])).'</td>';
					 }
					echo '</tr>';
					
					echo '<tr><td width="90">Actual</td>';
					$i=0;
					 foreach($tna_task_id as $vid=>$key)
					 {
						  
						 $i++;
						 
						  //echo $row[csf('status').$key]."***";
						
						if ( $new_approval_arr[$row[csf('job_no')]][$key]=="") $new_data=explode("_",$row[csf('status').$key]);
						else $new_data=explode("_",$new_approval_arr[$row[csf('job_no')]][$key]);
						
						//echo "<pre>";
						//print_r($new_data);
						//$new_data : 0->actual_start_date, 1->actual_finish_date, 2->task_start_date, 3->task_finish_date, 4->notice_date_start, 5->notice_date_end
						
						if( $new_data[7]!="") $function="onclick='update_tna_process(2,$new_data[7],".$row[csf('po_number_id')].")'";  else $function="";
						$bgcolor1=""; $bgcolor="";
						
						if (trim($new_data[2])!= $blank_date) 
						{
							
							
							if (strtotime($new_data[4])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($new_data[2]))  $bgcolor="#FFFF00";//Yellow
							else if (strtotime($new_data[2])<strtotime(date("Y-m-d",time())))  $bgcolor="#FF0000";//Red
							else $bgcolor="";
							
						}
						
						
						
						//echo strtotime($new_data[5])."_".strtotime(date("Y-m-d",time()));die;
						 
						if ($new_data[3]!= $blank_date) {
							if (strtotime($new_data[5])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($new_data[3]))  $bgcolor1="#FFFF00";
							else if (strtotime($new_data[3])<strtotime(date("Y-m-d",time())))  $bgcolor1="#FF0000"; else $bgcolor1="";
						}
						
						
						
						/*if ($new_data[0]!="" ) $bgcolor="";
						if ($new_data[1]!="") $bgcolor1="";*/
						
						if ($new_data[0]!=$blank_date) $bgcolor="";
						if ($new_data[1]!=$blank_date) $bgcolor1="";
						
					///if($key==8) { echo $row[csf('status').$key]; print_r($new_approval_arr)."==";  }
						
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
						
						if($new_data[0]!=$blank_date)
						{
							$start_diff1 = datediff( "d", $new_data[0], $new_data[2]);
							$finish_diff1 = datediff( "d", $new_data[1], $new_data[3]);
							
							$start_diff=$start_diff1-2;
							$finish_diff=$finish_diff1-2;
							
							if($start_diff<0)
							{
								$bgcolor="#2A9FFF"; //Blue
							}
							if($start_diff>0)
							{
								$bgcolor="";
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
							$start_diff="";
							$bgcolor="";
							$finish_diff="";
							$bgcolor1="";
							
							if(date("Y-m-d")>$new_data[2] && $new_data[2]!=$blank_date)
							{
								$start_diff1 = datediff( "d", $new_data[2], date("Y-m-d"));
								$start_diff=$start_diff1-2;
								$bgcolor="#FF0000";		//Red
							}
							if(date("Y-m-d")>$new_data[3] && $new_data[3]!=$blank_date)
							{
								$finish_diff1 = datediff( "d", $new_data[3], date("Y-m-d"));
								$finish_diff=$finish_diff1-2;
								$bgcolor1="#FF0000";
							}
							if(date("Y-m-d")<=$new_data[2])
							{	
								$start_diff = "";
								$bgcolor="";
							}
							if(date("Y-m-d")<=$new_data[3])
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
?>
 
<? 


if($action=="edit_update_tna")
{
	
	//echo "$mid";
	echo load_html_head_contents("TNA","../../", 1, 1, $unicode);
	extract($_REQUEST);
	
	$plnread=""; $actread="";
	if( $type==1 )
		$plnread="disabled='disabled'";
	else
		$actread="disabled='disabled'";
		
		if( $type==2 )
		$plnread="disabled='disabled'";
	else
		$actread="disabled='disabled'";
		
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$sql ="select a.po_number,b.job_no,b.buyer_name,b.style_ref_no from  wo_po_break_down a,wo_po_details_master b where a.id=$po_id and a.job_no_mst=b.job_no";
		//echo $sql;	
		$result=sql_select($sql);
		$tna= "select task_start_date,task_finish_date,actual_start_date,actual_finish_date from  tna_process_mst where id=$mid ";
		$tna_result=sql_select($tna);
				
		
	?> 
   
     <script>
	 
	 var permission='<? echo $permission; ?>';
function fnc_tna_actual_date_update( operation )
{
	//alert(operation);
	
	//var dataString = 'txt_actual_start_date*txt_actual_finish_date';
		 
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_actual_start_date*txt_actual_finish_date*txt_update_tna_id',"../../");
		//alert (data);
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
		/*if(show_msg(reponse[0])==1)
		{
		alert(Update Successfully)	
		}
		else
		{
			alert(Invalid Operation);
		}
		*/
		//alert (reponse[0]);
		
		
		//document.getElementById('report_container').innerHTML  = reponse[1];
		set_button_status(1, permission, 'fnc_tna_actual_date_update',1);
		release_freezing();
		
	}
}
	</script>
    
     </head>
    <body onLoad="set_hotkey()">
    <div id="messagebox_main"></div>
    <div align="center" style="width:100%">
      <? echo load_freeze_divs ("../../");  ?>
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
        	<td align="right">Plan Start Date</td><td><input type="text" <? //echo $plnread; ?> disabled="disabled" name="txt_plan_start_date" id="txt_plan_start_date" class="datepicker" style="width:100px" value="<? echo change_date_format($tna_result[0][csf('task_start_date')]); ?>" /></td>
            <td align="right">Plan Finish Date</td><td><input type="text" <? //echo $plnread; ?> disabled="disabled" name="txt_plan_finish_date" id="txt_plan_finish_date" class="datepicker" style="width:100px"  value="<? echo change_date_format($tna_result[0][csf('task_finish_date')]) ?>"/></td>
        </tr>
         <tr>
        	<td align="right">Actual Start Date</td><td><input type="text" <? //echo $actread; ?> name="txt_actual_start_date" id="txt_actual_start_date" class="datepicker" style="width:100px" value="<? echo change_date_format($tna_result[0][csf('actual_start_date')]) ?>" /></td>
            <td align="right">Actual Finish Date</td><td><input type="text" <? //echo $actread; ?> name="txt_actual_finish_date" id="txt_actual_finish_date" class="datepicker" style="width:100px" value="<? echo change_date_format($tna_result[0][csf('actual_finish_date')]); ?>" /></td>
        </tr>
        
        <tr>
        	<td colspan="4" height="50" valign="middle" align="center" class="button_container">
            <input type="hidden" id="txt_update_tna_id" name="txt_update_tna_id"  value="<? echo $mid; ?>" />
            <? echo load_submit_buttons( $permission, "fnc_tna_actual_date_update", 1,0 ,"",2) ; ?> 
            </td>
        </tr>
        
    </table>
    </div>
 </body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
    <?
	die;

}

if($action=="save_update_delete")
{
		
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//echo "sdsdsds";
	
	if ($operation==1)  
	{		
		$con = connect();
		if($db_type==0)
		{
			  mysql_query("BEGIN");
		}
		 
		$id=str_replace("'",'',$txt_update_tna_id);
		$field_array="actual_start_date*actual_finish_date";
		$data_array="".$txt_actual_start_date."*".$txt_actual_finish_date."";
		//echo $id;die;
		//echo "INSERT INTO tna_process_mst (".$field_array.") VALUES ".$data_array;die;
			
		$rID=sql_update("tna_process_mst",$field_array,$data_array,"id",$id,1);
			
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
	
	//echo $job_no.'_'.$po_id.'_'.$template_id;die;
	
	echo load_html_head_contents("TNA","../../", 1, 1, $unicode);
	extract($_REQUEST);
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$tna_task_arr=return_library_array( "select id, task_short_name from lib_tna_task",'id','task_short_name');
	
	
	$sql ="select b.company_name,b.buyer_name,a.po_number,b.job_no,b.style_ref_no,b.gmts_item_id,a.po_received_date,a.shipment_date from  wo_po_break_down a,wo_po_details_master b where a.id=$po_id and a.job_no_mst=b.job_no";
	$result=sql_select($sql);
	
	
	$tna_task_id=array();
	$plan_start_array=array();
	$plan_finish_array=array();
	
	$actual_start_array=array();
	$actual_finish_array=array();
	
	$notice_start_array=array();
	$notice_finish_array=array();
	
	//echo "select task_number,task_start_date,task_finish_date,actual_start_date,actual_finish_date,notice_date_start,notice_date_end from tna_process_mst where template_id='$template_id' and po_number_id='$po_id' order by sequence_no asc";
	$task_sql= sql_select("select task_number,task_start_date,task_finish_date,actual_start_date,actual_finish_date,notice_date_start,notice_date_end from tna_process_mst where template_id='$template_id' and po_number_id='$po_id' order by sequence_no asc");
	foreach ($task_sql as $row_task)
	{
		$tna_task_id[]=$row_task[csf("task_number")];
		
		$plan_start_array[$row_task[csf("task_number")]] =$row_task[csf("task_start_date")];
		$plan_finish_array[$row_task[csf("task_number")]]=$row_task[csf("task_finish_date")];
		
		$actual_start_array[$row_task[csf("task_number")]] =$row_task[csf("actual_start_date")];
		$actual_finish_array[$row_task[csf("task_number")]]=$row_task[csf("actual_finish_date")];
		
		$notice_start_array[$row_task[csf("task_number")]] =$row_task[csf("notice_date_start")];
		$notice_finish_array[$row_task[csf("task_number")]]=$row_task[csf("notice_date_end")];
	}
	
	//echo "<pre>";
	//print_r($actual_finish_array);
	
	$comments_array=array();
	$responsible_array=array();
	
	$res_comm_sql= sql_select("select task_id, comments, responsible from tna_progress_comments where tamplate_id='$template_id' and order_id='$po_id'");
	foreach ($res_comm_sql as $row_res_comm)
	{
		$comments_array[$row_res_comm[csf("task_id")]] =$row_res_comm[csf("comments")];
		$responsible_array[$row_res_comm[csf("task_id")]]=$row_res_comm[csf("responsible")];
	}
	
	
	$execution_time_array=array();
	
	$execution_time_sql= sql_select("select tna_task_id, execution_days from tna_task_template_details where task_template_id='$template_id'");
	foreach ($execution_time_sql as $row_execution_time)
	{
		$execution_time_array[$row_execution_time[csf("tna_task_id")]] =$row_execution_time[csf("execution_days")];
	}
	
	
	$upid_sql= sql_select("select min(id) as id from tna_progress_comments where tamplate_id='$template_id' and order_id='$po_id'");
	foreach ($upid_sql as $row_upid)
	{
		$id_up=$row_upid[csf("id")];
	}
	
		
?> 
   
    <script>
	 
		var permission='<? echo $permission; ?>';
	
		function fnc_progress_comments_entry(operation)
		{
			//alert (operation);return;
			
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
				
				if (comments!="")
				{
					j++;
					data_all+=get_submitted_data_string('txtresponsible_'+i+'*txtcomments_'+i+'*taskid_'+i,"../../",i);
				}
			}
			
			//alert(data_all);return;
			
			if(data_all=='')
			{
				alert("No Comments Found");	
				return;
			}
			//alert(data_all);return;
			var data="action=save_update_delete_progress_comments&operation="+operation+get_submitted_data_string('jobno*orderid*tamplateid',"../../")+data_all+'&tot_row='+tot_row;
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
				//var save_id_return= return_global_ajax_value( reponse[2], 'load_php_dtls_form_return_id_date', '', 'sample_development_controller');
				/*var reponse_return_id=save_id_return.split('*');
				
				var tot_row=$('#size_tbl tbody tr').length;
				var k=1;
				for(i=0; i<=reponse_return_id.length; i++)
				{
					$('#sizeupid_'+k).val(reponse_return_id[i]);
					var id=$('#sizeupid_'+k).val();
					k++;
				}*/
				//if(id!='')
				//{
					set_button_status(1, permission, 'fnc_progress_comments_entry',3);
				//}
				release_freezing();	
			}
		}
		
		function openmypage(i)
		{	
			var title = 'TNA Progress Comment';
			
			var txtcomments = document.getElementById("txtcomments_"+i).value;
			//var data='additional_info='+additional_info;
			//alert(txtcomments);return;
			
			var page_link = 'tna_report_controller.php?data='+txtcomments+'&action=comments_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=160px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				
				var additional_infos=this.contentDoc.getElementById("additional_infos").value;
				
				document.getElementById("txtcomments_"+i).value=additional_infos;
			}
		}
		
		function new_window()
		{
			document.getElementById('scroll_body2').style.overflow="auto";
			document.getElementById('scroll_body2').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('details_reports').innerHTML+'</body</html>');
			d.close();
			
			document.getElementById('scroll_body2').style.overflowY="scroll";
			document.getElementById('scroll_body2').style.maxHeight="180px";
		}
		
		function new_excel()
		{
			window.open($('#txt_file_link_ref').val(), "#");
			 
		}
		
		 
		
		/*$(document).ready(function() 
		{
			$('#report_container123').html(report_convert_button('../../../'));	
		});*/
	
	//document.getElementById('report_container123').innerHTML=report_convert_button('../../../'); 
	
	</script>
    
</head>
<body onLoad="set_hotkey()">
	<div id="messagebox_main"></div>
	<div align="center" style="width:100%;">
    <? 
		echo load_freeze_divs ("../../",'',1);  
		ob_start();
	?>
    
    <form name="tnaprocesscomments_3" id="tnaprocesscomments_3" autocomplete="off" >
    
    <div align="center" style="width:100%" id="details_reports">
    
     <table width="1060" border="1" rules="all" class="rpt_table">
    	<tr><td colspan="6" align="center"><b><font size="+1">TNA Progress Comment</font></b></td></tr>
    </table>
    
    <table width="1060" border="1" rules="all" class="rpt_table">
    	<?php
		foreach($result as $row)
		{
		?>
    	<tr>
        	<td width="130">Company</td>
            <td width="196"><?php  echo $company_library[$row[csf('company_name')]];  ?></td>
            <td width="130">Buyer</td>
            <td width="186"><?php  echo $buyer_arr[$row[csf('buyer_name')]];  ?></td>
            <td width="130">Order No</td>
           	<td width="186"><?php  echo $row[csf('po_number')];  //echo $result[0][csf('po_number')];  ?></td>
        </tr>
        <tr>
        	<td width="130">Style Ref.</td>
            <td width="196"><?php  echo $row[csf('style_ref_no')];  ?></td>
            <td width="130">RMG Item</td>
            <td width="186"><?php  echo $garments_item[$row[csf('gmts_item_id')]];  ?></td>
            <td width="130">Order Recv. Date</td>
           	<td width="186"><?php  echo change_date_format($row[csf('po_received_date')]);  ?></td>
        </tr>
        <tr>
        	<td width="130">Ship Date</td>
            <td width="196"><?php  echo change_date_format($row[csf('shipment_date')]);  ?></td>
            <td width="130">Lead Time</td>
            <td width="186">
				<?
                    echo $lead_time=return_field_value("lead_time","tna_task_template_details", "task_template_id='$template_id' and status_active=1 and is_deleted=0");
                ?>
            </td>
            <td width="130">Job Number</td>
           	<td width="186">
            	<? echo $row[csf('job_no')];   ?>
            	<Input type="hidden" name="jobno" class="text_boxes" ID="jobno" value="<? echo $job_no; ?>" style="width:100px" />
            	<Input type="hidden" name="orderid" class="text_boxes" ID="orderid" value="<? echo $po_id; ?>" style="width:100px" />
                <Input type="hidden" name="tamplateid" class="text_boxes" ID="tamplateid" value="<? echo $template_id; ?>" style="width:100px" />
            </td>
            
        </tr>
        <?php
		}
		?>
    </table>
    
    <table><tr height="10"><td colspan="6">&nbsp;</td></tr></table>
    
    <table style="width: 1070px;">
        <tr>
            <td>
                <div style="width: 1070px;font-size:12px;">
                <table width="1060" border="1" rules="all" class="rpt_table">
                    <thead>
                    	<tr align="center">
                            <th width="50">Task No</th>
                            <th width="150">Task Name</th>
                            <th width="60">Allowed Days</th>
                            <th width="80">Plan Satart Date</th>
                            <th width="80">Plan Finish Date</th>
                            <th width="80">Actual Satart Date</th>
                            <th width="80">Actual Finish Date</th>
                            <th width="80">Start Delay/ Early By</th>
                            <th width="80">Finish Delay/ Early By</th>
                            <th width="150">Responsible</th>
                            <th width="">Comments</th>
                        </tr>
                    </thead>
                </table>
                </div>
            </td>
        </tr>
    </table> 
    <table style="width:1070px;">
        <tr>
            <td>    
                <div style="width: 1070px;overflow-y: scroll; max-height:180px;font-size:12px;" id="scroll_body2">
                <table width="1050px" border="1" rules="all" class="rpt_table" id="comments_tbl">
                	<tbody>
						<?php
                        $i=0;
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
								$finish_diff1 = datediff( "d", $actual_finish_array[$key], $plan_finish_array[$key]);
								
								$start_diff=$start_diff1-2;
								$finish_diff=$finish_diff1-2;
								
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
									$bgcolor6="#2A9FFF";
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
								if(date("Y-m-d")>$plan_start_array[$key])
								{
									$start_diff1 = datediff( "d", $plan_start_array[$key], date("Y-m-d"));
									$start_diff=$start_diff1-2;
									$bgcolor5="#FF0000";		//Red
									$start="(Delay)";
								}
								if(date("Y-m-d")>$plan_finish_array[$key])
								{
									$finish_diff1 = datediff( "d", $plan_finish_array[$key], date("Y-m-d"));
									$finish_diff=$finish_diff1-2;
									$bgcolor6="#FF0000";
									$finish="(Delay)";
								}
								if(date("Y-m-d")<=$plan_start_array[$key])
								{
									$start_diff = "";
									$bgcolor5="";
									$start="(Ac. Start Dt. Not Found)";
								}
								if(date("Y-m-d")<=$plan_finish_array[$key])
								{
									$finish_diff = "";
									$bgcolor6="";
									$finish="(Ac. Finish Dt. Not Found)";
									
								}
							}
                        ?>
                        <tr bgcolor="<? echo $trcolor; ?>">
                            <td align="center" width="50"><? echo $i; ?></td>
                            <td width="150"> <? echo $tna_task_arr[$key]; ?></td>
                            <td align="center" width="60"><? echo $execution_time_array[$key]; ?></td>
                            <td align="center" width="80"><? echo  change_date_format($plan_start_array[$key]); ?></td>
                            <td align="center" width="80"><? echo  change_date_format($plan_finish_array[$key]); ?></td>
                            <td align="center" width="80" bgcolor="<? echo $bgcolor;  ?>">
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
                            <td align="center" width="80" bgcolor="<? echo $bgcolor1;  ?>">
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
                            <td align="center" width="80" bgcolor="<? echo $bgcolor5;  ?>">
								<?  
                                    echo $start_diff." ".$start;
                                ?>
                            </td>
                            <td align="center" width="80" bgcolor="<? echo $bgcolor6;  ?>">
                                <?  
                                    echo $finish_diff." ".$finish;
                                ?>
                            </td>
                            
                            <td width="150">
                            	<Input name="txtresponsible[]" class="text_boxes" ID="txtresponsible_<?php echo $i; ?>" value="<?php  echo $responsible_array[$key]; ?>" style="width:137px" />
                            	<Input type="hidden" name="taskid[]" class="text_boxes" ID="taskid_<?php echo $i; ?>" value="<? echo $key; ?>" style="width:50px">
                            </td>
                            <td><Input name="txtcomments[]" placeholder="Double Click to Search" class="text_boxes" ID="txtcomments_<?php echo $i; ?>" value="<?php  echo $comments_array[$key]; ?>" onDblClick="openmypage(<?php echo $i; ?>); return false" style="width:135px;" autocomplete="off" readonly /></td>
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
					echo load_submit_buttons($permission, "fnc_progress_comments_entry", 1,0,"reset_form('tnaprocesscomments_3','','','','','');",3);
				}
				else
				{
					echo load_submit_buttons($permission, "fnc_progress_comments_entry", 0,0,"reset_form('tnaprocesscomments_3','','','','','');",3);
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
	?>

    <input type="hidden" id="txt_file_link_ref" value="<? echo $filenames; ?>">
    </div>
    <div id="report_container123" align="center"></div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
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
		
		//echo "$html";
		
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

if($action=="comments_popup")
{
	echo load_html_head_contents("TNA Progress Comment", "../../", 1, 1,'','','');
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
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
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
		
		$field_array_comments="id, job_id, order_id, tamplate_id, task_id, responsible, comments, inserted_by, insert_date, status_active, is_deleted";
		
		//$data_array_comments='';
		
		
		 
		for($i=1;$i<=$tot_row; $i++)
		{
			$txtresponsible='txtresponsible_'.$i;
			$txtcomments='txtcomments_'.$i;
			$taskid='taskid_'.$i;
			//if($id=="") $sizeid=return_next_id( "id", "sample_development_size", 1 ); //else $sizeid=$sizeid+1;
			//$size_id=return_id( $$txtsizename, $size_arr, "lib_size", "id,size_name");
			
			if(str_replace("'","",$$txtcomments)!="")
			{
				
				if($data_array_comments!="") $data_array_comments.=",";
	
				$data_array_comments.="(".$id.",".$jobno.",".$orderid.",".$tamplateid.",".$$taskid.",".$$txtresponsible.",".$$txtcomments.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				
				
				
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
			
			//echo 	"shajjad_".$rIDs;die;
			
			if($rIDs)
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
	else if ($operation==1)   // Update Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$id=return_next_id( "id","tna_progress_comments", 1 ) ;
		
		$field_array_comments="id, job_id, order_id, tamplate_id, task_id, responsible, comments, inserted_by, insert_date, status_active, is_deleted";
		
		$data_array_comments='';
		
		for($i=1;$i<=$tot_row; $i++)
		{
			$txtresponsible='txtresponsible_'.$i;
			$txtcomments='txtcomments_'.$i;
			$taskid='taskid_'.$i;
			
			if(str_replace("'","",$$txtcomments)!="")
			{
				if($data_array_comments!="") $data_array_comments.=",";
	
				$data_array_comments.="(".$id.",".$jobno.",".$orderid.",".$tamplateid.",".$$taskid.",".$$txtresponsible.",".$$txtcomments.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				
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



function sql_insert2( $strTable, $arrNames, $arrValues, $commit, $contain_lob )
{
	global $con ;
	if($contain_lob=="") $contain_lob=0;
	if( $contain_lob==0)
	{
		$tmpv=explode(")",$arrValues);
		if(count($tmpv)>2)
			$strQuery= "INSERT ALL \n";
		else
			$strQuery= "INSERT  \n";
			
		for($i=0; $i<count($tmpv)-1; $i++)
		{
			if( strpos(trim($tmpv[$i]), ",")==0)
				$tmpv[$i]=substr_replace($tmpv[$i], " ", 0, 1); 
			$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$tmpv[$i].") \n";
		}
		
	   if(count($tmpv)>2) $strQuery .= "SELECT * FROM dual";
	   return $strQuery ;
	}
	else
	{
		$tmpv=explode(")",$arrValues);
		
		for($i=0; $i<count($tmpv)-1; $i++)
		{
			$strQuery="";
			$strQuery= "INSERT  \n";
			if( strpos(trim($tmpv[$i]), ",")==0)
				$tmpv[$i]=substr_replace($tmpv[$i], " ", 0, 1); 
			$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$tmpv[$i].") \n";
			//return $strQuery ;
			$stid =  oci_parse($con, $strQuery);
			$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
			if (!$exestd) return "0"; 
		}
		return "1";
	    
	}
    //return  $strQuery; die;
	//echo $strQuery;die;
	//$_SESSION['last_query']=$_SESSION['last_query'].";;".$strQuery;

	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	if ($exestd) 
		return "1";
	else 
		return "0";
	die;
	
	if ( $commit==1 )
	{
		if (!oci_error($exestd))
		{
			$pc_time= add_time(date("H:i:s",time()),360); 
			$pc_date_time = date("d-M-Y h:i:s",strtotime(add_time(date("H:i:s",time()),360)));
	        $pc_date = date("d-M-Y",strtotime(add_time(date("H:i:s",time()),360)));
			
			$strQuery= "INSERT INTO activities_history ( session_id,user_id,ip_address,entry_time,entry_date,module_name,form_name,query_details,query_type) VALUES ('".$_SESSION['logic_erp']["history_id"]."','".$_SESSION['logic_erp']["user_id"]."','".$_SESSION['logic_erp']["pc_local_ip"]."','".$pc_date_time."','".$pc_date."','".$_SESSION["module_id"]."','".$_SESSION['menu_id']."','".encrypt($_SESSION['last_query'])."','0')"; 
			$resultss=oci_parse($con, $strQuery);
			oci_execute($resultss);
			$_SESSION['last_query']="";
			//oci_commit($con); 
			return "0";
		}
		else
		{
			//oci_rollback($con);
			return "10";
		}
	}
	else return 1;
	//else
		//return 0;
		
	die;
}
?>