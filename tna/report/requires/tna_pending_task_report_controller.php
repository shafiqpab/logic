<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
require_once('../../../includes/class3/class.conditions.php');
require_once('../../../includes/class3/class.reports.php');
require_once('../../../includes/class3/class.yarns.php');
require_once('../../../includes/class3/class.fabrics.php');


extract($_REQUEST);
$user_id=$_SESSION['logic_erp']['user_id'];
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
	exit();	 
}




if($action=="generate_report")
{
	
	$compid=str_replace("'","",$cbo_company_name);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$cbo_team_member=str_replace("'","",$cbo_team_member);

	
	
	$user_company_arr = return_library_array("select id,unit_id from user_passwd where valid=1","id","unit_id");
	$user_buyer_arr = return_library_array("select id,buyer_id from user_passwd where valid=1","id","buyer_id");
	$dealing_marchant = return_library_array("select id,team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$task_short_name = return_library_array("select task_name,task_short_name from lib_tna_task where status_active=1 and is_deleted=0","task_name","task_short_name");


	if($db_type==0)
	{
		$current_date = date("Y-m-d H:i:s",strtotime($txt_date_from));
		//$prev_date = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))); 
		$prev_date = $current_date; 
		$actual_date="='0000-00-00'";
	}
	else
	{
		$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($txt_date_from)),'','',1);
		//$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1); 
		$prev_date = $current_date; 
		$actual_date="is null";
	}
	
	
	
	//foreach($mail_type_wise_data[4] as $user_id => $task_id){
	$user_company_con=" and b.company_name in(".$user_company.")";
	if(str_replace("'","",$cbo_task_name)){$task_id_string = " and a.task_number=".$cbo_task_name;}
	if(str_replace("'","",$cbo_buyer_name)){$user_buyer_con=" and b.buyer_name in(".$cbo_buyer_name.")";}
	if(str_replace("'","",$txt_job_no)){$job_con=" and a.job_no =".$txt_job_no."";}
	if(str_replace("'","",$txt_order_no)){$order_con=" and d.po_number =".$txt_order_no."";}
	if(str_replace("'","",$cbo_team_member)){$team_member_con=" and b.dealing_marchant =".$cbo_team_member."";}

	$tna_process_start_date=return_field_value("tna_process_start_date"," variable_order_tracking"," company_name=".$compid." and variable_list=43"); 
	$sql_dtls="select a.task_finish_date,b.dealing_marchant,a.task_number,b.buyer_name,a.job_no,a.po_number_id,a.shipment_date,b.style_ref_no,d.po_number from tna_process_mst a, wo_po_details_master b, lib_tna_task c, wo_po_break_down d where a.job_no=b.job_no and a.po_number_id=d.id  and c.task_name=a.task_number and b.job_no=d.job_no_mst 
	 and a.task_finish_date between '$tna_process_start_date' and '$prev_date'
	  and a.actual_finish_date $actual_date $task_id_string $job_con $order_con $team_member_con and b.company_name=$compid  $user_buyer_con and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and d.status_active !=3 and d.shiping_status !=3 and d.is_confirmed=1 and a.task_number <> 110 group by b.dealing_marchant,a.task_number,b.buyer_name,a.job_no,a.po_number_id,a.task_finish_date,a.shipment_date,b.style_ref_no,d.po_number order by a.shipment_date";//and a.task_finish_date <= '".$prev_date."'
	  
	   //echo $sql_dtls;die;
	$nameArray_dtls=sql_select($sql_dtls);
	foreach($nameArray_dtls as $rows)
	{
		$dealing_marchant_data_arr[$rows[csf('dealing_marchant')]][]=$rows;
		$jobArr[$rows[csf('job_no')]]=$rows[csf('job_no')];
		
	}

 // var_dump($dealing_marchant_data_arr); 
 
	
		$sql ="select JOB_ID,ORDER_ID,TASK_ID,COMMENTS,RESPONSIBLE,MER_COMMENTS";
			
			$chunk_job_no_all=array_chunk($jobArr,999);
			$q=1;
			foreach($chunk_job_no_all as $job_no)
			{
				if($q==1) $sql_job_con .=" and (job_id in('".implode("','",$job_no)."')"; else $sql_sub_lc .=" or job_no in('".implode("','",$job_no)."')";
				$p++;
			}
			$sql_job_con .=" )";
			if(count($jobArr)==0){$sql_job_con=' and 1>1';}
		
		$sql .=" from  tna_progress_comments where 1=1 $sql_job_con "; 

		$comments_result=sql_select($sql);
		foreach($comments_result as $rows)
		{
			$commentsArr[COMMENTS][$rows[JOB_ID]][$rows[TASK_ID]]=$rows[COMMENTS];
			$commentsArr[RESPONSIBLE][$rows[JOB_ID]][$rows[TASK_ID]]=$rows[RESPONSIBLE];
			$commentsArr[MER_COMMENTS][$rows[JOB_ID]][$rows[TASK_ID]]=$rows[MER_COMMENTS];
			
		}
 
 ob_start();
 
 ?> 
<fieldset style="width:1230px;">
<table width="1220">
    <tr>
        <th><strong>Completion Pending From <? echo change_date_format($tna_process_start_date);?> To <? echo change_date_format($prev_date);?> </strong></th>
    </tr>
    
    <tr>
    	<td>
			<?
            /*$sql_mst="select b.dealing_marchant from tna_process_mst a, wo_po_details_master b where a.job_no=b.job_no   and a.task_finish_date between '$tna_process_start_date' and '$prev_date' and a.actual_finish_date $actual_date  $task_id_string $job_con $team_member_con and b.company_name=$compid  $user_buyer_con and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.dealing_marchant";*/
            foreach($dealing_marchant_data_arr as $dealing_marchant_id=>$nameArray_dtls)
            {
            $j++;
            ?>
    		<table width="100%" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                <tr>
                    <td colspan="11"><strong>Dealing Merchant : <? echo $dealing_marchant[$dealing_marchant_id]; ?></strong>, <strong> Mobile :<? echo $marchant_contact_no[$dealing_marchant_id]; ?></strong></td>
                </tr>
                <tr>
                    <th width="34"><strong>SL</strong></th>
                    <th width="146"><strong>Task Name</strong></th>
                    <th width="122"><strong>Buyer</strong></th>
                    <th width="80"><strong>Job No</strong></th>
                    <th width="145"><strong>Style Ref.</strong></th>
                    <th width="125"><strong>Order</strong></th>
                    <th width="80"><strong>Ship Date</strong></th>
                    <th width="80"><strong>TNA End Date</strong></th>
                    <th><strong>Responsible</strong></th>
                    <th><strong>Comments</strong></th>
                    <th><strong>Mer. Comments</strong></th>
                </tr>
                </thead>
				<?
                	$i=0;
				foreach($nameArray_dtls as $row_dtls)
				{
					$i++;
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
                ?>
        		<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $dealing_marchant_id.$i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $dealing_marchant_id.$i; ?>">
                    <td align="center"><? echo $i; ?></td>
                    <td><? echo $task_short_name[$row_dtls[csf('task_number')]]; ?></td>
                    <td><? echo $buyer_library[$row_dtls[csf('buyer_name')]]; ?></td>
                    <td><? echo $row_dtls[csf('job_no')]; ?></td>
                    <td><? echo $row_dtls[csf('style_ref_no')]; ?></td>
                    <td><p><? echo $row_dtls[csf('po_number')]; ?></p></td> 
                    <td align="center"><?  echo change_date_format($row_dtls[csf('shipment_date')]); ?></td>
                    <td align="center"><?  echo change_date_format($row_dtls[csf('task_finish_date')]); ?></td>
                    <td><? echo $commentsArr[RESPONSIBLE][$row_dtls[csf('job_no')]][$row_dtls[csf('task_number')]]; ?></td>
                    <td><? echo $commentsArr[COMMENTS][$row_dtls[csf('job_no')]][$row_dtls[csf('task_number')]]; ?></td>
                    <td><? echo $commentsArr[MER_COMMENTS][$row_dtls[csf('job_no')]][$row_dtls[csf('task_number')]]; ?></td>
                </tr>
			<? 
			}
            ?>
              </table><br />
			<?
		}

	echo '</td></tr></table></fieldset>';

	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//

	$contents=ob_get_contents();
	ob_clean();
	//echo $contents;



	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$contents);
	$filename=$user_id."_".$name.".xls";
 	echo "$contents****$filename";




exit();
}

