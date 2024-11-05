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


$tna_process_start_date="2014-12-01";
if($db_type==0) $blank_date="0000-00-00"; else $blank_date=""; 

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
	die; 	 
}

if($action=="set_print_button")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=3 and report_id=24 and is_deleted=0 and status_active=1");
	echo "print_report_button_setting('$print_report_format');\n"; 
die;
}

$lead_time=return_library_array("select task_template_id,lead_time from tna_task_template_details where task_type=1 group by task_template_id,lead_time","task_template_id","lead_time");
$buyer_short_name_arr = return_library_array("SELECT short_name,id FROM  lib_buyer WHERE is_deleted = 0 and status_active=1 order by id asc","id","short_name");


	$tna_process_type=return_field_value("tna_process_type"," variable_order_tracking","variable_list=31"); 

if($action=="generate_tna_report")
{

?>
<style>
.group-1{background-image: linear-gradient(rgb(000, 222, 255) 10%, rgb(123, 170, 214) 96%)!important;}
.group-2{background-image: linear-gradient(rgb(999, 222, 255) 10%, rgb(123, 170, 214) 96%)!important;}
.group-3{background-image: linear-gradient(rgb(222, 500, 255) 10%, rgb(123, 170, 214) 96%)!important;}
.group-4{background-image: linear-gradient(rgb(852, 258, 255) 10%, rgb(123, 170, 214) 96%)!important;}
.group-5{background-image: linear-gradient(rgb(100, 200, 255) 10%, rgb(123, 170, 214) 96%)!important;}
.group-6{background-image: linear-gradient(rgb(170, 458, 255) 10%, rgb(123, 170, 214) 96%)!important;}
.group-7{background-image: linear-gradient(rgb(190, 112, 255) 10%, rgb(123, 170, 214) 96%)!important;}
.group-8{background-image: linear-gradient(rgb(183, 191, 255) 10%, rgb(123, 170, 214) 96%)!important;}
.group-9{background-image: linear-gradient(rgb(193, 354, 255) 10%, rgb(123, 170, 214) 96%)!important;}
.group-10{background-image: linear-gradient(rgb(121, 171, 255) 10%, rgb(123, 170, 214) 96%)!important;}
.group-11{background-image: linear-gradient(rgb(122, 258, 255) 10%, rgb(123, 170, 214) 96%)!important;}
.group-12{background-image: linear-gradient(rgb(023, 151, 255) 10%, rgb(123, 170, 214) 96%)!important;}
.group-13{background-image: linear-gradient(rgb(124, 141, 255) 10%, rgb(123, 170, 214) 96%)!important;}
.group-14{background-image: linear-gradient(rgb(819, 999, 255) 10%, rgb(123, 170, 214) 96%)!important;}
.group-15{background-image: linear-gradient(rgb(248, 121, 255) 10%, rgb(123, 170, 214) 96%)!important;}

</style>

<?
	
	$actual_manual_update_task_arr=return_library_array("select task_id,task_id from tna_manual_permission where is_actual_manual=1  and company_id=$cbo_company_name","task_id","task_id");
	$plan_manual_update_task_arr=return_library_array("select task_id,task_id from tna_manual_permission where is_plan_manual=1  and company_id=$cbo_company_name","task_id","task_id");
	
	$cbo_task_group=str_replace("'","",$cbo_task_group);	
	if($db_type==0){$task_group_con=" and task_group!=''";}else{$task_group_con=" and task_group is not null";}
	if($cbo_task_group){$task_group_con.=" and task_group like('%$cbo_task_group%')";}
	
	
	$mod_sql= sql_select("select id,task_catagory,task_name,task_short_name,task_type,completion_percent,task_group,task_group_sequence from lib_tna_task where is_deleted = 0 and status_active=1 $task_group_con order by task_group_sequence,task_sequence_no asc");
	
	
	
	
	$tna_task_group_array=array();
	$tna_task_array=array();
	$tna_task_id=array();
	$tna_task_cat=array();
	$tna_task_name_arr=array();
	//$tna_task_detls=array();
	foreach ($mod_sql as $row)
	{
		$tna_task_group_array[$row[csf("task_group")]][$row[csf("task_name")]] = $row[csf("task_group")];
		$tna_task_short_array[$row[csf("task_name")]] =$row[csf("task_short_name")];

		$tna_task_id[$row[csf("task_name")]]=$row[csf("task_name")];
		$tna_task_array[$row[csf("id")]] =$row[csf("task_short_name")];
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
			$date_range=" and b.insert_date between ".$txt_date_from." and '".str_replace("'","",$txt_date_to)." 23:59:59'";}
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
	
	if(str_replace("'","",$cbo_shipment_status)==3)$shipment_status_con=" and b.shiping_status=$cbo_shipment_status"; else $shipment_status_con=" and b.shiping_status !=3";
	
	
	
	
	
	$tna_all_task=implode(",",$tna_task_id);
	
	if(str_replace("'","",$cbo_search_type)==3)
	{
	$sql = "SELECT a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.set_smv,a.job_no_prefix_num,a.dealing_marchant,a.quotation_id,b.id,b.po_number FROM  wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no  and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 $order_status_cond";
	}
	else
	{
	$sql = "SELECT a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.set_smv,a.job_no_prefix_num,a.dealing_marchant,a.quotation_id,b.id,b.po_number FROM  wo_po_details_master a,  wo_po_break_down b WHERE a.job_no=b.job_no_mst $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no  and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 $order_status_cond"; 
	}
	


	$result = sql_select( $sql ) ;
	$wo_po_details_master = array();
	$po_no_arr=array();
	$job_no_arr=array();
	foreach( $result as  $row ) 
	{	
		$wo_po_details_master[$row[csf('job_no')]]['quotation_id']=$row[csf('quotation_id')];
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
	
	//print_r($buyer_name);die;
	
	$po_no_arr_all=implode(",",$po_no_arr); if($po_no_arr_all!="") $po_no_arr_all .=",0"; else $po_no_arr_all .="0"; 
	$job_no_all="'".implode("','",$job_no_arr)."'";
	$c=count($tna_task_id);
	
	if($db_type==0)
	{
		$sql ="select a.po_number_id,a.job_no,a.shipment_date,a.template_id,a.po_receive_date,b.insert_date,";
		$i=1;
	
		foreach( $tna_task_id as $dval=>$id)    	
		{
			if ($i!=$c) $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id, ";
			else $sql .="max(CASE WHEN CONCAT(a.task_number) = '".$id."' THEN concat(a.actual_start_date,'_',a.actual_finish_date,'_',a.task_start_date,'_',a.task_finish_date,'_',a.notice_date_start,'_',a.notice_date_end,'_',a.remarks,'_',a.id,'_',a.task_number,'_',a.plan_start_flag,'_',a.plan_finish_flag)  END ) as status$id ";
			$i++;
		}
		
		$sql .=" from tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id and a.po_number_id in( $po_no_arr_all ) and a.job_no in ($job_no_all) $shipment_status_con and b.status_active=1 and a.task_type=1  and b.po_quantity>0 $order_status_cond group by a.po_number_id,a.job_no order by a.shipment_date,a.po_number_id,a.job_no"; 
	}
	else
	{
		$sql ="select a.po_number_id,a.job_no,max(a.shipment_date) as shipment_date,a.template_id,max(a.po_receive_date) as po_receive_date,b.insert_date,";
		$i=1;
		
		//var_dump($tna_task_id);
		
		
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
		$sql .=" from  tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id $sql_order_con $sql_job_con $shipment_status_con and b.status_active=1 and b.po_quantity>0 and a.task_type=1 $order_status_cond  group by a.po_number_id,a.job_no,a.template_id,a.shipment_date,b.insert_date order by a.shipment_date,a.po_number_id,a.job_no"; 
	}
	
	  //echo $sql;die;
	
	$lead_time_array=return_library_array("select task_template_id,lead_time from tna_task_template_details where is_deleted=0 and status_active=1 and task_type=1  group by lead_time,task_template_id","task_template_id",'lead_time');
	if($db_type==0)
	{
		$tast_tmp_id_arr=return_library_array("select task_template_id, group_concat(tna_task_id) as tna_task_id  from tna_task_template_details where task_type=1 group by task_template_id","task_template_id",'tna_task_id');
	}
	else
	{
		$tast_tmp_id_arr=return_library_array("select task_template_id, listagg(cast(tna_task_id as varchar(4000)),',') within group(order by tna_task_id) as tna_task_id  from tna_task_template_details where task_type=1 group by task_template_id","task_template_id",'tna_task_id');
	}
	
	
$sqlQuotation="SELECT  a.id as quotation_id,a.quot_date,b.inquery_date FROM wo_price_quotation a LEFT JOIN wo_quotation_inquery b ON a.inquery_id = b.id where a.company_id=$cbo_company_id and  b.company_id=$cbo_company_id";
$sqlQuotationResult= sql_select($sqlQuotation);
foreach($sqlQuotationResult as $row){
	$quotation_arr[$row[csf('quotation_id')]]['quot_date']=$row[csf('quot_date')];
	$quotation_arr[$row[csf('quotation_id')]]['inquery_date']=$row[csf('inquery_date')];
}
	
	
	//echo $sql;
	
	$data_sql= sql_select($sql);
	$width=(count($tna_task_id)*160)+1140;
	
	
	ob_start();
	
	?>
    <fieldset>
    <div style="margin:0 1%;">
        <span style="background:#FF0000; padding:0 6px; border-radius:9px; cursor:pointer;" title="Red">&nbsp;</span>&nbsp; Target Date Over. &nbsp;&nbsp;
        <span style="background:#2A9FFF; padding:0 6px; border-radius:9px; cursor:pointer;" title="Blue">&nbsp;</span>&nbsp; Done in late. &nbsp;&nbsp;
        
        <span style="background:#009933; padding:0 6px; border-radius:9px; cursor:pointer;" title="Green">&nbsp;</span>&nbsp; Done in Due. &nbsp;&nbsp;
        
        <span style="background:#FFFF00; padding:0 6px; border-radius:9px; cursor:pointer;" title="Yellow">&nbsp;</span>&nbsp; Reminder.
        
        <span style="background:#FF66FF; padding:0 6px; border-radius:9px; cursor:pointer;" title="Pink">&nbsp;</span>&nbsp; Manual Update Plan.
        
        
    </div>
    
    <div style="width:<? echo $width+145; ?>px" align="left">
    <table width="<? echo $width+140; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
    	<thead>
        	<tr>
            	<th width="60" rowspan="3">SL</th><th width="120" rowspan="3">Merchant</th><th width="120" rowspan="3">Buyer Name</th><th width="120" rowspan="3">PO Number</th><th width="100" rowspan="3">PO Qty.</th><th width="50" rowspan="3">SMV</th><th width="120" rowspan="3">Style Ref.</th> <th width="120" rowspan="3">Job No.</th><th width="100" rowspan="3">Shipment Date</th><th width="80" rowspan="3">Buyer Inquiry</th><th width="80" rowspan="3">Quotation</th><th width="80" rowspan="3">OPD</th>
                
                <th width="90" rowspan="3">Status</th>
                <?
					
					$i=0;
					foreach($tna_task_group_array as $group=>$task_arr)
					{
						$i++;
						$colspan= count($task_arr)*2;
						$w=$colspan*160;
						echo '<th width="160" class="group-'.$i.'" colspan="'.$colspan.'">'.$group.'</th>';
					}
					echo '</tr><tr>';
					
					
					
					foreach($tna_task_group_array as $task_arr)
					{
						foreach($task_arr as $key=>$val)
						{
							echo '<th width="160" colspan="2" title="'.$key.'='.$tna_task_name[$key].'">'.$tna_task_short_array[$key].'</th>';
						}
					}
					echo '</tr><tr>';
					
					foreach($tna_task_group_array as $task_arr)
					{
						foreach($task_arr as $key=>$val)
						{
							echo '<th width="80"> Start</th><th width="80"> Finish</th>';
						}
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
							$tot_po_qty+=$po_qty;
						?>
                    </td>
                    <td width="50" rowspan="3" align="center"><? echo number_format($wo_po_details_master[$row[csf('job_no')]]['set_smv'],2); ?></td>
                    
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
                     
                     
                    <td width="100" rowspan="3" title="<? echo $lead_timee."; "." PO. Rec. Date: ".change_date_format($row[csf('po_receive_date')]); ?>"><? echo change_date_format($row[csf('shipment_date')])."<br>"." ".$lead_timee."<br>"." PO Lead Time:".$po_lead_time;  ?></td>
                    <td width="80" rowspan="3"><? 
						$quotation_id=$wo_po_details_master[$row[csf('job_no')]]['quotation_id'];
						echo $quotation_arr[$quotation_id]['inquery_date']
					?></td>
                    <td width="80" rowspan="3"><? echo $quotation_arr[$quotation_id]['quot_date'];?></td>
                    <td width="80" rowspan="3" align="center"><? echo change_date_format($row[csf('insert_date')]);?></td>
                    <td width="90">Plan</td>
                <?
 
			
					 $tast_id_arr=array_unique(explode(',',$tast_tmp_id_arr[$row[csf('template_id')]]));
					 $i=0;
					 foreach($tna_task_group_array as $task_arr){
					 foreach($task_arr as $vid=>$val)
					 {	$key=$vid;
						 $i++;
						
						if ( $new_approval_arr[$row[csf('job_no')]][$key]=="") $new_data=explode("_",$row[csf('status').$key]); 
						else $new_data=explode("_",$new_approval_arr[$row[csf('job_no')]][$key]);
						
						if($new_data[9]==1){$psc=' bgcolor="#FF66FF"';}else{$psc="";}
						if($new_data[10]==1){$pfc=' bgcolor="#FF66FF"';}else{$pfc="";}
						
						
						if($new_data[7]!="") $function="onclick='update_tna_process(1,$new_data[7],".$row[csf('po_number_id')].")'"; else $function="";
						
						if($plan_manual_update_task_arr[$vid]==''){$function="";}
						
						if(in_array($vid,$tast_id_arr))
						{
							if(count($tna_task_id)==$i)
							 
								echo '<td align="center" '.$psc.' width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? "<span style='color:#FF0000'> N/A </span>" : change_date_format($new_data[2])).'</td><td align="center" '.$pfc.' '.$function.'> '.($new_data[3]== "N/A"  || $new_data[3]=="0000-00-00"? "" : change_date_format($new_data[3])).'</td>';
								
							 else
								
								echo '<td align="center" '.$psc.' width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? "<span style='color:#FF0000'> N/A </span>" : change_date_format($new_data[2])).'</td><td align="center" '.$pfc.' width="80" '.$function.'> '.($new_data[3]== ""  || $new_data[3]=="0000-00-00"? "<span style='color:#FF0000'> N/A </span>" : change_date_format($new_data[3])).'</td>';
						}
						else
						{
							if(count($tna_task_id)==$i)
								
								echo '<td align="center" '.$psc.'   width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? "N/A" : change_date_format($new_data[2])).'</td><td align="center" '.$pfc.'  '.$function.'> '.($new_data[3]== ""  || $new_data[3]=="0000-00-00"? "" : change_date_format($new_data[3])).'</td>';
								
							 else
								
								echo '<td align="center" '.$psc.'  width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? "N/A" : change_date_format($new_data[2])).'</td><td align="center" '.$pfc.'  width="80" '.$function.'> '.($new_data[3]== ""  || $new_data[3]=="0000-00-00"? "N/A" : change_date_format($new_data[3])).'</td>';
						}
						
						
					 }
					 }
					echo '</tr>';
					
					echo '<tr><td width="90">Actual</td>';
					$i=0;
					 foreach($tna_task_group_array as $task_arr){
					 foreach($task_arr as $vid=>$val)
					 {	$key=$vid;
						  
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
						
						
						
						//echo strtotime($new_data[5])."_".strtotime(date("Y-m-d",time()));die;
						 
						if ($new_data[3]!= $blank_date) {
							if (strtotime($new_data[5])<=strtotime(date("Y-m-d",time())) && strtotime(date("Y-m-d",time()))<=strtotime($new_data[3]))  $bgcolor1="#FFFF00";
							else if (strtotime($new_data[3])<strtotime(date("Y-m-d",time())))  $bgcolor1="#FF0000"; else $bgcolor1="";
						}
						
						
						
						
						if ($new_data[0]!=$blank_date) $bgcolor="";
						if ($new_data[1]!=$blank_date) $bgcolor1="";
						
						$idd=$row[csf('job_no')]."".$row[csf('po_number_id')]."".$key;
						if(count($tna_task_id)==$i)
							echo '<td align="center" title="Click Here to Edit Date" id="'.$idd.'1" '.$function.' width="80" bgcolor="'.$bgcolor.'">'.($new_data[0]== "" || $new_data[0]=="0000-00-00" ? "" : change_date_format($new_data[0])).'</td><td align="center" id="'.$idd.'2" title="Click Here to Edit Date" '.$function.' bgcolor="'.$bgcolor1.'" title="'.$new_data[6].'">'.($new_data[1]== "" || $new_data[1]=="0000-00-00" ? "" : change_date_format($new_data[1])).'</td>';
							
						else
							echo '<td align="center" id="'.$idd.'1" title="Click Here to Edit Date"  '.$function.' width="80" bgcolor="'.$bgcolor.'">'.($new_data[0]== "" || $new_data[0]=="0000-00-00" ? "" : change_date_format($new_data[0])).'</td><td align="center" id="'.$idd.'2" title="Click Here to Edit Date" '.$function.' width="80" bgcolor="'.$bgcolor1.'" title="'.$new_data[6].'">'.($new_data[1]== "" || $new_data[1]=="0000-00-00" ? "" : change_date_format($new_data[1])).'</td>';
						
					 }
					 }
					echo '</tr>'; 
					
					echo '<tr><td width="90">Delay/Early By</td>';
					$j=0;
					 foreach($tna_task_group_array as $task_arr){
					 foreach($task_arr as $vid=>$val)
					 {	$key=$vid;
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
							if($start_diff>=0)
							{
								$bgcolor="#009933";
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
							if($finish_diff>=0)
							{	
								$bgcolor1="#009933";
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
					}
					 
					echo '</tr>';
					
					
					 
		 }
				 
	}
		?>
     
     
    </table>
    </div>
    <div style="width:<? echo $width+145; ?>px;" align="left">
         <table width="<? echo $width+140; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left">
            <tfoot>
                <th width="60"></th>
                <th width="120"></th>
                <th width="120"></th>
                <th width="120">Total</th>
                <th width="100" id="total_po_qty" align="right"><? echo number_format($tot_po_qty,2);?></th>
                <th width="50"></th>
                <th width="120"></th>
                <th width="120"></th>
                <th width="100"></th>
                <th width="80"></th>
                <th width="80"></th>
                <th width="80"></th>
                <th width="90"></th>
                
                	<?
					foreach($tna_task_group_array as $task_arr)
					{
						foreach($task_arr as $key=>$val)
						{
							echo '<th width="80"></th><th width="80"></th>';
						}
					}
					?>
                
                
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
		echo '</tr></table></fieldset>';



	
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
	
	
	
	$cbo_task_group=str_replace("'","",$cbo_task_group);	
	if($db_type==0){$task_group_con=" and task_group!=''";}else{$task_group_con=" and task_group is not null";}
	if($cbo_task_group){$task_group_con.=" and task_group like('%$cbo_task_group%')";}
	
	
	$sql =sql_select("select id,task_name,task_short_name from  lib_tna_task where status_active=1 and is_deleted=0 $task_group_con"); 
	
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
	
	$lead_time_array=return_library_array("select task_template_id,lead_time from tna_task_template_details where is_deleted=0 and status_active=1 and task_type=1 group by lead_time","task_template_id",'lead_time');
	$txt_job_no=str_replace("'","",$txt_job_no);
	if($txt_job_no=="") $txt_job_no=""; else $txt_job_no=" and a.job_no_prefix_num ='$txt_job_no'";
	$txt_order_no=str_replace("'","",$txt_order_no);
	if($txt_order_no=="") $txt_order_no=""; else $txt_order_no=" and b.po_number ='$txt_order_no'";
	$txt_style_ref_no=str_replace("'","",$txt_style_ref_no);
	if($txt_style_ref_no=="") $txt_style_ref_no=""; else $txt_style_ref_no=" and a.style_ref_no ='$txt_style_ref_no'";
	
	
	
	
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
		where p.po_number_id=b.id and b.job_no_mst=a.job_no and p.task_number in($tna_task_id) $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no  $order_status_cond
		group by a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_no_prefix_num,a.dealing_marchant,a.insert_date,b.po_number,b.shipment_date,b.po_quantity, p.po_number_id,po_received_date, p.task_number, p.template_id
		order by shipment_date,po_number_id,job_no";
	}
	else
	{
		$sql ="select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_no_prefix_num,a.dealing_marchant,to_char(a.insert_date,'YYYY') as job_year,b.po_number,b.shipment_date,b.po_quantity, p.po_number_id,p.po_receive_date, p.task_number, p.template_id, min(case when  p.task_start_date is not null  then p.task_start_date end) as task_start_date, max(p.task_finish_date) as task_finish_date, min(case when  p.task_start_date is not null then p.actual_start_date end) as actual_start_date, max(p.actual_finish_date) as actual_finish_date 
		from  tna_process_mst p,wo_po_details_master a,  wo_po_break_down b
		where p.po_number_id=b.id and b.job_no_mst=a.job_no and p.task_number in($tna_task_id) $date_range $cbo_company_name $cbo_buyer_name $cbo_team_member $txt_job_no $txt_order_no $txt_style_ref_no $order_status_cond 
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
		$task_short_name= return_library_array("select task_name,task_short_name from lib_tna_task where is_deleted = 0 and status_active=1 order by task_sequence_no asc","task_name","task_short_name");
	
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
	
	if(str_replace("'","",$cbo_shipment_status)==3)$shipment_status_con=" and b.shiping_status=$cbo_shipment_status"; else $shipment_status_con=" and b.shiping_status !=3";
	
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
		
		$task_short_name= return_library_array("select task_name,task_short_name from lib_tna_task where is_deleted = 0 and status_active=1 order by task_sequence_no asc","task_name","task_short_name");
		
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
	
	if(str_replace("'","",$cbo_shipment_status)==3)$shipment_status_con=" and b.shiping_status=$cbo_shipment_status"; else $shipment_status_con=" and b.shiping_status !=3";
	
	
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
	
	$lib_task_sql=sql_select("select task_name,task_short_name, penalty from lib_tna_task");
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
	
		
	
	$mod_sql= sql_select("select a.id,a.task_catagory,a.task_name,a.task_short_name,a.task_type,a.completion_percent from lib_tna_task a, tna_task_template_details b where b.for_specific=$cbo_buyer_name and a.task_name=b.tna_task_id and a.is_deleted = 0 and a.status_active=1  and b.task_type=1 and b.is_deleted = 0 and b.status_active=1 order by a.task_sequence_no asc");
	$tna_task_array=array();
	$tna_task_id=array();
	$tna_task_cat=array();
	$tna_task_name_arr=array();
	//$tna_task_detls=array();
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
	//**txt_date_from*txt_date_to*txt_job_no
	
	if(str_replace("'","",$cbo_shipment_status)==3)$shipment_status_con=" and b.shiping_status=$cbo_shipment_status"; else $shipment_status_con=" and b.shiping_status !=3";
	
	
	
	
	
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
	
	//print_r($buyer_name);die;
	
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
		
		$sql .=" from tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id and a.po_number_id in( $po_no_arr_all ) and a.job_no in ($job_no_all) $shipment_status_con and b.status_active=1  and b.po_quantity>0 $order_status_cond group by a.po_number_id,a.job_no order by a.shipment_date,a.po_number_id,a.job_no"; 
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
		$sql .=" from  tna_process_mst a, wo_po_break_down b where a.po_number_id=b.id $sql_order_con $sql_job_con $shipment_status_con and b.status_active=1 and b.po_quantity>0 $order_status_cond  group by a.po_number_id,a.job_no,a.template_id,a.shipment_date order by a.shipment_date,a.po_number_id,a.job_no"; 
	}
	
	//echo $sql;
	
	$lead_time_array=return_library_array("select task_template_id,lead_time from tna_task_template_details where is_deleted=0 and status_active=1 and task_type=1 group by lead_time,task_template_id","task_template_id",'lead_time');
	
	if($db_type==0)
	{
		$tast_tmp_id_arr=return_library_array("select task_template_id, group_concat(tna_task_id) as tna_task_id  from tna_task_template_details where task_type=1 group by task_template_id","task_template_id",'tna_task_id');
	}
	else
	{
		$tast_tmp_id_arr=return_library_array("select task_template_id, listagg(cast(tna_task_id as varchar(4000)),',') within group(order by tna_task_id) as tna_task_id  from tna_task_template_details where task_type=1 group by task_template_id","task_template_id",'tna_task_id');
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
                     
                     
                    <td width="100" rowspan="3" title="<? echo $lead_timee."; "." PO. Rec. Date: ".change_date_format($row[csf('po_receive_date')]); ?>"><? echo change_date_format($row[csf('shipment_date')])."<br>"." ".$lead_timee."<br>"." PO Lead Time:".$po_lead_time;  ?></td>
                    <td width="90">Plan</td>
                <?
 
	
					 $i=0;
					 //$tast_id_arr=array();
					 $tast_id_arr=array_unique(explode(',',$tast_tmp_id_arr[$row[csf('template_id')]]));
					 foreach($tna_task_id as $vid=>$key)
					 {
						 $i++;
						
						if ( $new_approval_arr[$row[csf('job_no')]][$key]=="") $new_data=explode("_",$row[csf('status').$key]); 
						else $new_data=explode("_",$new_approval_arr[$row[csf('job_no')]][$key]);
						
						
						
						
						if($new_data[7]!="") $function="onclick='update_tna_process(1,$new_data[7],".$row[csf('po_number_id')].")'"; else $function="";
						if(in_array($vid,$tast_id_arr))
						{
							if(count($tna_task_id)==$i)
								
								echo '<td  width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? " <span style='color:#FF0000'> N/A </span>" : change_date_format($new_data[2])).'</td><td '.$function.'> '.($new_data[3]== ""  || $new_data[3]=="0000-00-00"? "" : change_date_format($new_data[3])).'</td>';
								
							 else
								echo '<td width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? "<span style='color:#FF0000'> N/A </span>" : change_date_format($new_data[2])).'</td><td width="80" '.$function.'> '.($new_data[3]== ""  || $new_data[3]=="0000-00-00"? "<span style='color:#FF0000'> N/A </span>" : change_date_format($new_data[3])).'</td>';
						}
						else
						{
							if(count($tna_task_id)==$i)
								echo '<td  width="80" '.$function.'>'.($new_data[2]== "" || $new_data[2]=="0000-00-00" ? " N/A" : change_date_format($new_data[2])).'</td><td '.$function.'> '.($new_data[3]== ""  || $new_data[3]=="0000-00-00"? "" : change_date_format($new_data[3])).'</td>';
								
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
		
		$tna= "select template_id,task_number,task_start_date,task_finish_date,actual_start_date,actual_finish_date from  tna_process_mst where id=$mid  and task_type=1";
		$tna_result=sql_select($tna);
		
		$mod_sql= sql_select("select id,task_catagory,task_name,task_short_name,task_type,completion_percent from lib_tna_task where is_deleted = 0 and status_active=1 order by task_sequence_no asc");
		$tna_task_array=array();
		foreach ($mod_sql as $row)
		{	
			$tna_task_array[$row[csf("task_name")]] = $row[csf("task_short_name")];
		}
		
		//History data start------------------------
		$tna_history_sql= "select id, template_id,task_number, job_no, po_number_id, task_start_date, task_finish_date, actual_start_date, actual_finish_date,plan_start_flag,plan_finish_flag from  tna_plan_actual_history where   is_deleted = 0 and status_active=1 and  template_id=".$tna_result[0][csf('template_id')]." and task_number=".$tna_result[0][csf('task_number')]." and po_number_id=$po_id and job_no='".$result[0][csf('job_no')]."'";
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
	
	
	//var dataString = 'txt_actual_start_date*txt_actual_finish_date';
		 
		var data="action=save_update_delete&operation="+operation+'&start_flag='+start_flag+'&finish_flag='+finish_flag+get_submitted_data_string('txt_actual_start_date*txt_actual_finish_date*txt_update_tna_id*txt_plan_start_date*txt_plan_finish_date*txt_update_tna_type*txt_plan_actual_history',"../../../");
		//alert (data);return;
		freeze_window(operation);
		http.open("POST","fabric_tna_progress_report_controller.php",true);
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
		
		
		//make_history($db_type,1,$id);
		
		if($update_tna_type==1)
		{
			
			$field='';$data='';
			if($start_flag==1){$field="*plan_start_flag";$data="*1";}
			if($finish_flag==1){$field.="*plan_finish_flag";$data.="*1";}
			
			
			
			$field_array1="task_type*task_start_date*task_finish_date".$field;
			$data_array1="1*".$txt_plan_start_date."*".$txt_plan_finish_date.$data."";
			
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
				$field_array="id, template_id,task_number, job_no, po_number_id, task_start_date, task_finish_date, plan_start_flag,plan_finish_flag,status_active,is_deleted, task_type";
				$data_array="(".$hid.",".$htmp_id.",".$htask_id.",'".str_replace("'","",$hjob_id)."',".$hpo_id.",".$txt_plan_start_date.",".$txt_plan_finish_date.",".$start_flag.",".$finish_flag.",'1','0',1)";
				$rID=sql_insert("tna_plan_actual_history",$field_array,$data_array,1);
				
			}
			//history process end-----------------------------------------;
			
		}
		else
		{
			if($db_type==0)
			{
				$sql2 ="SELECT actual_start_date,actual_finish_date,actual_start_flag,actual_finish_flag FROM tna_process_mst where id=$id  and  task_type=1";
			}
			if($db_type==2 || $db_type==1)
			{	
				$sql2 ="SELECT actual_start_date,actual_finish_date,nvl(actual_start_flag,0) as actual_start_flag,nvl(actual_finish_flag,0) as actual_finish_flag FROM tna_process_mst where id=$id  and task_type=1";
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
				$hfield_array="task_type*actual_start_date*actual_finish_date";
				$hdata_array="1*".$txt_actual_start_date."*".$txt_actual_finish_date."";
				$rID=sql_update("tna_plan_actual_history",$hfield_array,$hdata_array,"id",str_replace("'","",$hmid),1);
			}
			else
			{
				$hid=return_next_id( "id", "tna_plan_actual_history", 1 ) ;
				$field_array="id, template_id,task_number,job_no, po_number_id, actual_start_date, actual_finish_date, status_active, is_deleted,task_type";
				$data_array="(".$hid.",".$htmp_id.",".$htask_id.",'".str_replace("'","",$hjob_id)."',".$hpo_id.",".$txt_actual_start_date.",".$txt_actual_finish_date.",'1','0',1)";
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
	
	//echo $job_no.'_'.$po_id.'_'.$template_id.$tna_process_type;die;
	
	echo load_html_head_contents("TNA","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$tna_task_arr=return_library_array( "select task_name, task_short_name from lib_tna_task",'task_name','task_short_name');
	$lead_time_array=return_library_array("select task_template_id,lead_time from tna_task_template_details where is_deleted=0 and status_active=1 and task_type=1 group by lead_time,task_template_id","task_template_id",'lead_time');

	
	$sql ="select b.company_name,b.buyer_name,a.po_number,b.job_no,b.style_ref_no,b.gmts_item_id,a.po_received_date,a.shipment_date from  wo_po_break_down a,wo_po_details_master b where a.id=$po_id and a.job_no_mst=b.job_no";
	$result=sql_select($sql);
	
	
	$tna_task_id=array();
	$plan_start_array=array();
	$plan_finish_array=array();
	
	$actual_start_array=array();
	$actual_finish_array=array();
	
	$notice_start_array=array();
	$notice_finish_array=array();
	
	//$task_sql= sql_select("select a.task_number,a.task_start_date,a.task_finish_date,a.actual_start_date,a.actual_finish_date,a.notice_date_start,a.notice_date_end from tna_process_mst a, lib_tna_task b where a.task_number=b.task_name and a.template_id='$template_id' and a.po_number_id='$po_id' order by b.task_sequence_no asc");
	
	 $task_sql=sql_select("select a.task_number,a.task_start_date,a.task_finish_date,a.actual_start_date,a.actual_finish_date,a.notice_date_start,a.notice_date_end from tna_process_mst a, lib_tna_task b where a.task_number=b.task_name and a.po_number_id='$po_id' and b.status_active=1 and b.is_deleted=0 and a.task_type=1 order by b.task_sequence_no asc");
	
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

	
	
	
	
	$comments_array=array();
	$responsible_array=array();
	
	//$res_comm_sql= sql_select("select task_id, comments, responsible from tna_progress_comments where tamplate_id='$template_id' and order_id='$po_id'");
	//echo "select task_id, comments, responsible from tna_progress_comments where order_id='$po_id'";
	$res_comm_sql=sql_select("select mer_comments,task_id, comments, responsible from tna_progress_comments where order_id='$po_id'");
	
	foreach ($res_comm_sql as $row_res_comm)
	{
		$comments_array[$row_res_comm[csf("task_id")]] =$row_res_comm[csf("comments")];
		$mer_comments_array[$row_res_comm[csf("task_id")]] =$row_res_comm[csf("mer_comments")];
		$responsible_array[$row_res_comm[csf("task_id")]]=$row_res_comm[csf("responsible")];
	}
	
	
	$execution_time_array=array();
	
	//$execution_time_sql= sql_select("select tna_task_id, execution_days from tna_task_template_details where task_template_id='$template_id'");
	
	$execution_time_sql= sql_select("select for_specific, tna_task_id, execution_days from tna_task_template_details where task_type=1");
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
	
	$lead_time=return_library_array("select task_template_id,lead_time from tna_task_template_details where task_type=1 group by task_template_id,lead_time","task_template_id","lead_time");
		
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
				if (responsible!="" || comments!="" || mrc_comments!="")
				{
					j++;
					data_all+=get_submitted_data_string('txtresponsible_'+i+'*txtcomments_'+i+'*txtmercomments_'+i+'*taskid_'+i,"../../../",i);
				}
			}
			
			  //alert(data_all);  
			
			if(data_all=='')
			{
				alert("No Comments Found");	
				return;
			}
			//alert(data_all);return;
			var data="action=save_update_delete_progress_comments&operation="+operation+get_submitted_data_string('jobno*orderid*tamplateid',"../../../")+data_all+'&tot_row='+tot_row;
			//alert (data);return;
			freeze_window(operation);
			http.open("POST","fabric_tna_progress_report_controller.php",true);
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
				$.post('fabric_tna_progress_report_controller.php?job_no='+'<? echo $job_no; ?>'+'&po_id='+<? echo $po_id; ?>+'&template_id='+<? echo $template_id; ?>+'&tna_process_type='+<? echo $tna_process_type; ?>,
				{ 
					path: '', action: "generate_report_file", filename: path_link },
					function(data)
					{
						//alert(data);
						$('#txt_file_link_ref').val(data);
						///window.open(rel_path+trim(data), "#");
					}
				);
				
					
					set_button_status(1, permission, 'fnc_progress_comments_entry',3);
				
				release_freezing();	
			}
		}
		function autoRefresh_div()
		 {
			  $("#auto_id").load("fabric_tna_progress_report_controller.php");// a function which will load data from other file after x seconds
		  }
		
		function openmypage(i,title)
		{	
			var txtcomments = document.getElementById(i).value;
			var page_link = 'fabric_tna_progress_report_controller.php?data='+txtcomments+'&action=comments_popup&title='+title;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=160px,center=1,resize=1,scrolling=0','../../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var additional_infos=this.contentDoc.getElementById("additional_infos").value;
				document.getElementById(i).value=additional_infos;
				document.getElementById("td"+i).innerHTML=additional_infos;
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
			
	
	//document.getElementById('report_container123').innerHTML=report_convert_button('../../../../'); 
	//alert(refresh_data);
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
            <td width="130">Order No</td>
           	<td width="176"><?php  echo $row[csf('po_number')];  //echo $result[0][csf('po_number')];  ?></td>
        </tr>
        <tr>
        	<td width="130">Style Ref.</td>
            <td width="176"><?php  echo $row[csf('style_ref_no')];  ?></td>
            <td width="130">RMG Item</td>
            <td width="176"><?php  echo $garments_item[$row[csf('gmts_item_id')]];  ?></td>
            <td width="130">Order Recv. Date</td>
           	<td width="176"><?php  echo change_date_format($row[csf('po_received_date')]);  ?></td>
        </tr>
        <tr>
        	<td width="130">Ship Date</td>
            <td width="176"><?php  echo change_date_format($row[csf('shipment_date')]);  ?></td>
            <td width="130">Lead Time</td>
            <td width="176">
				<? 
					if($tna_process_type==1)
					{
						$lead_timee=$lead_time_array[$template_id];
					}
					else
					{
						$lead_timee=$template_id;
					}
                    //echo $lead_time=return_field_value("lead_time","tna_task_template_details", "task_template_id='$template_id' and status_active=1 and is_deleted=0");
					echo $lead_timee;
                ?>
            </td>
            <td width="130">Job Number</td>
           	<td width="176">
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
                            <td width="150" id="tdtxtresponsible_<?php echo $i; ?>"  onClick="openmypage('txtresponsible_<?php echo $i; ?>','Responsible'); return false;" title="Click For Comments"><?php  echo $responsible_array[$key]; ?></td>
                            <td width="120" align="center" id="tdtxtcomments_<?php echo $i; ?>" onClick="openmypage('txtcomments_<?php echo $i; ?>','Comments'); return false;" title="Click For Responsible" ><?php  echo $comments_array[$key]; ?></td>
                            <td align="center" id="tdtxtmercomments_<?php echo $i; ?>"  onClick="openmypage('txtmercomments_<?php echo $i; ?>','Mer. Comments'); return false;"  title="Click For Comments"><?php  echo $mer_comments_array[$key];?></td>
                        </tr>
                        
                        <Input name="txtcomments[]" ID="txtcomments_<?php echo $i; ?>" value="<?php  echo $comments_array[$key]; ?>" type="hidden"  class="text_boxes" />
                        <Input name="txtmercomments[]" ID="txtmercomments_<?php echo $i; ?>" value="<?php  echo $mer_comments_array[$key]; ?>" type="hidden"  class="text_boxes" />
                        
                        <Input name="txtresponsible[]" class="text_boxes" ID="txtresponsible_<?php echo $i; ?>" value="<?php  echo $responsible_array[$key]; ?>" type="hidden"/>
                            	
                        <Input type="hidden" name="taskid[]" class="text_boxes" ID="taskid_<?php echo $i; ?>" value="<? echo $key; ?>">
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
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
    <?
	
		
		foreach (glob("*.xls") as $filename) {
		@unlink($filename);
		}
		
		$create_new_doc = fopen($filenames, 'w');	
		$is_created = fwrite($create_new_doc,ob_get_contents());
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
                <td width="120px" height="5" align="center" valign="middle"><?php echo $title;?></td>
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
			
			if(str_replace("'","",$$txtresponsible)!="" || str_replace("'","",$$txtcomments)!="" || str_replace("'","",$$txtmercomments)!="")
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
			
			if(str_replace("'","",$$txtresponsible)!="" ||str_replace("'","",$$txtcomments)!="" || str_replace("'","",$$txtmercomments)!="")
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
	
	 $task_sql=sql_select("select a.task_number,a.task_start_date,a.task_finish_date,a.actual_start_date,a.actual_finish_date,a.notice_date_start,a.notice_date_end from tna_process_mst a, lib_tna_task b where a.task_number=b.task_name and a.po_number_id='$po_id' and a.task_type=1 order by b.task_sequence_no asc");
	
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

	
	
	$lead_time_array=return_library_array("select task_template_id,lead_time from tna_task_template_details where is_deleted=0 and status_active=1 and task_type=1 group by lead_time","task_template_id",'lead_time');
	
	$comments_array=array();
	$responsible_array=array();
	
	//$res_comm_sql= sql_select("select task_id, comments, responsible from tna_progress_comments where tamplate_id='$template_id' and order_id='$po_id'");
	//echo "select task_id, comments, responsible from tna_progress_comments where order_id='$po_id'";
	$res_comm_sql=sql_select("select mer_comments,task_id, comments, responsible from tna_progress_comments where order_id='$po_id'");
	
	foreach ($res_comm_sql as $row_res_comm)
	{
		$comments_array[$row_res_comm[csf("task_id")]] =$row_res_comm[csf("comments")];
		$responsible_array[$row_res_comm[csf("task_id")]]=$row_res_comm[csf("responsible")];
		$mer_comments_array[$row_res_comm[csf("task_id")]] =$row_res_comm[csf("mer_comments")];
	}
	
	
	$execution_time_array=array();
	
	//$execution_time_sql= sql_select("select tna_task_id, execution_days from tna_task_template_details where task_template_id='$template_id'");
	
	$execution_time_sql= sql_select("select tna_task_id, execution_days from tna_task_template_details where task_type=1");
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
	
	$lead_time=return_library_array("select task_template_id,lead_time from tna_task_template_details where task_type=1 group by task_template_id,lead_time","task_template_id","lead_time");
	
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
                            <td width='150'>".$responsible_array[$key]."</td>
                            <td width='120' align='center'>".$comments_array[$key]."</td>
                            <td align='center'>  ".$mer_comments_array[$key]."</td>
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



function make_history($db_type,$operation,$update_id)
{
		
	if ($operation==1)  
	{		
		$con = connect();
		if($db_type==0)
		{
			  mysql_query("BEGIN");
		}
			
		//history process  start-----------------------------------------;
		if(str_replace("'","",$update_id))
		{
			$hid=return_next_id( "id", "tna_plan_actual_history", 1 ) ;
			$sql="select template_id,task_number, job_no, po_number_id, task_start_date, task_finish_date, plan_start_flag,plan_finish_flag,status_active,is_deleted, task_type from TNA_PROCESS_MST where status_active=1 and  is_deleted=0 and task_type=1 and id=$update_id";
			
			$result=sql_select($sql);
			foreach($result as $row){
				$data_array="(".$hid.",".$row[csf('template_id')].",".$row[csf('task_number')].",'".$row[csf('job_no')]."',".$row[csf('po_number_id')].",'".$row[csf('task_start_date')]."','".$row[csf('task_finish_date')]."',".$row[csf('plan_start_flag')].",".$row[csf('plan_finish_flag')].",'0','1',1)";
				execute_query("delete from tna_plan_actual_history where po_number_id=".$row[csf('po_number_id')]." and task_number= ".$row[csf('task_number')]." and is_deleted=1 and status_active=0",0);
			}
			
			$field_array="id, template_id,task_number, job_no, po_number_id, task_start_date, task_finish_date, plan_start_flag,plan_finish_flag,status_active,is_deleted, task_type";
		  	$rID=sql_insert("tna_plan_actual_history",$field_array,$data_array,1);
		}
		//history process end-----------------------------------------;
			
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				return "1**".str_replace("'", '', $id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				return "10**";
			}
		}
		if($db_type==1 || $db_type==2 )
		{
			if($rID)
			{
				oci_commit($con);
				return "1**".str_replace("'", '', $id);
			}
			else
			{
				oci_rollback($con);
				return "10**";
			}
		}
		
		disconnect($con);
	}
}


?>

