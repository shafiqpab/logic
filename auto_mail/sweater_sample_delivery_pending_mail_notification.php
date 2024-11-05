<?php
date_default_timezone_set("Asia/Dhaka");
// require_once('../mailer/class.phpmailer.php');
require_once('../includes/common.php');
require_once('setting/mail_setting.php');
extract($_REQUEST);
// var returnValue=return_global_ajax_value(reponse[2], 'sweater_sample_requisition_mail_notification', '', '../../../auto_mail/sweater_sample_requisition_mail_notification');
//echo load_html_head_contents("Mail Notification", "../", 1, 1,'','','');

$action='sweater_sample_delivery_pending_mail_notification';	
if($action=='sweater_sample_delivery_pending_mail_notification'){

	$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
	$current_date = change_date_format(date("Y-m-d", $strtotime),'','',1);
	$previous_date = change_date_format(date('Y-m-d', strtotime('-180 day', strtotime($current_date))),'','',1);
 
	
	$company_arr=return_library_array( "select id, company_name from lib_company ",'id','company_name');	
	$dealing_mar_arr=return_library_array( "select id,team_member_name from lib_mkt_team_member_info where  status_active =1 and is_deleted=0 order by team_member_name", "id", "team_member_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$team_name_arr=return_library_array( "select id, team_name from lib_sample_production_team", "id", "team_name"  );

	function add_date_without_offday($date,$addDay){
		
		for($i=1;$i<=$addDay;$i++){
			if(date('l', strtotime($date. " + $i day"))=='Friday'){
				$addDay+=1;
			}
		}
		
		return date('d-m-Y', strtotime($date. " + $addDay day"));
	}
	
foreach($company_arr as $company_id=>$company_name){	
	
	$cbo_company_name=$company_id;
	$cbo_location='0';
	$cbo_sample_team=0;
	$cbo_buyer_name='0';
	$cbo_comp_status='1';
	$cbo_type='2';
	$txt_date_from	=$previous_date;
	$txt_date_to=$current_date;

 
	$txt_sample_name=str_replace("'","",$txt_sample_name);
	if($txt_sample_name!=""){$sample_name_cond =" and a.sample_name like('%$txt_sample_name%')";}
	
	
	$sql="select a.id,a.sample_name,b.sequ from lib_sample a,lib_buyer_tag_sample b where a.id=b.tag_sample  and b.sequ is not null and a.status_active=1 and a.is_deleted=0 $sample_name_cond  group by  a.id,a.sample_name,b.sequ order by b.sequ";
	$sample_name_arr=return_library_array($sql,'id','sample_name');
	
	
	$txt_garments_item=trim(str_replace("'","",$txt_garments_item));
	if($txt_garments_item!=""){
		$garments_item_cond =" and ITEM_NAME like('%$txt_garments_item%')";
		$sql="select id,ITEM_NAME from LIB_GARMENT_ITEM where 1=1 $garments_item_cond";
		$garments_item_arr=return_library_array($sql,'id','ITEM_NAME');
	}
	 
	
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_sample_team=str_replace("'","",$cbo_sample_team);
	$cbo_location=str_replace("'","",$cbo_location);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_garments_item=str_replace("'","",$txt_garments_item);
	$txt_req_no=str_replace("'","",$txt_req_no);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$cbo_comp_status=str_replace("'","",$cbo_comp_status);
	$cbo_type=str_replace("'","",$cbo_type);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);

	if($cbo_buyer_name>0){$where_cond .=" and a.buyer_name=$cbo_buyer_name";}
	if($txt_style_ref!=""){$where_cond .=" and a.style_ref_no like('%$txt_style_ref%')";}
	if($txt_req_no!=""){$where_cond .=" and a.requisition_number like('%$txt_req_no')";}
	if($cbo_sample_team>0){$where_cond .=" and a.TEAM_LEADER=$cbo_sample_team";}
	if($cbo_location>0){$where_cond .=" and a.LOCATION_ID=$cbo_location";}

	if($txt_sample_name!=""){$where_cond .=" and b.SAMPLE_NAME in(".implode(',',array_flip($sample_name_arr)).")";}
	if($txt_garments_item!=""){$where_cond .=" and b.GMTS_ITEM_ID in(".implode(',',array_flip($garments_item_arr)).")";}


	if($txt_date_from!="" && $txt_date_to!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
		}
		else
		{
			$start_date=date("j-M-Y",strtotime(str_replace("'","",$txt_date_from)));
			$end_date=date("j-M-Y",strtotime(str_replace("'","",$txt_date_to)));
		}
		
		if($cbo_type==2){
			$where_cond .=" and a.REQUISITION_DATE between '$start_date' and '$end_date'";
		}
		else if($cbo_type==1){
			$where_cond .=" and b.DELV_END_DATE between '$start_date' and '$end_date'";
		}
	}

		if($cbo_comp_status==2){
			$joinTable=" tna_process_mst d,";
			$joinWhere=" and a.id=d.po_number_id and d.task_type=5 and d.task_number = 8 and d.actual_start_date is not null";
		}
		
		
		
		$sql="SELECT 
		a.ID AS SAMPLE_MST_ID,a.REQUISITION_NUMBER,
		LISTAGG(CAST(b.SAMPLE_NAME AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.SAMPLE_NAME) as SAMPLE_NAME,
		a.BUYER_NAME,a.STYLE_REF_NO,max(b.DELV_START_DATE) as DELV_START_DATE, min(b.DELV_END_DATE) as DELV_END_DATE,
		a.DEALING_MARCHANT,a.TEAM_LEADER,a.REMARKS,
		LISTAGG(CAST(c.GAUGE AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.GAUGE) as GAUGE,
		LISTAGG(CAST(c.FABRIC_DESCRIPTION AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.FABRIC_DESCRIPTION) as FABRIC_DESCRIPTION,
		LISTAGG(CAST(c.GMTS_ITEM_ID AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.GMTS_ITEM_ID) as GMTS_ITEM_ID,
		LISTAGG(CAST(c.COLOR_DATA AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.COLOR_DATA) as COLOR_DATA,
		a.REQUISITION_DATE,
		LISTAGG(CAST(b.ID AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.ID) as SAMPLE_DTLS_ID,
		LISTAGG(CAST(b.SIZE_DATA AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.SIZE_DATA) as SIZE_DATA,
		
		sum(b.SAMPLE_PROD_QTY) as SAMPLE_QTY,max(d.CONFIRM_DEL_END_DATE) as CONFIRM_DEL_END_DATE,
		LISTAGG(CAST(b.EMBELLISHMENT_STATUS_ID AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.EMBELLISHMENT_STATUS_ID) as EMBELLISHMENT_STATUS_ID,
		a.COMPANY_ID,a.SEASON,a.REFUSING_CAUSE,
		sum(c.REQUIRED_QTY) AS REQUIRED_QTY
		 FROM $joinTable SAMPLE_DEVELOPMENT_DTLS b,SAMPLE_DEVELOPMENT_FABRIC_ACC c,SAMPLE_DEVELOPMENT_MST a 
		 LEFT JOIN SAMPLE_REQUISITION_ACKNOWLEDGE d ON a.id=d.SAMPLE_MST_ID
		 
		 WHERE a.id=b.SAMPLE_MST_ID and b.SAMPLE_MST_ID=c.SAMPLE_MST_ID and a.entry_form_id=341 and c.FORM_TYPE=1  and a.company_id=$cbo_company_name $where_cond $joinWhere
		group by a.ID,a.COMPANY_ID,a.REQUISITION_NUMBER,a.REQUISITION_DATE,a.DEALING_MARCHANT,a.BUYER_NAME,a.SEASON,a.STYLE_REF_NO,a.TEAM_LEADER,a.REFUSING_CAUSE,a.REMARKS
		";
        $dataArray=sql_select( $sql );
		foreach ($dataArray as $row){
			$sample_id_arr[$row[SAMPLE_MST_ID]]=$row[SAMPLE_MST_ID];
		}
		
	
	
	$taskArr=array(
		/*1=> "Yarn In Hand Date",
		2=> "Accessories In Hand Date",
		3=> "Knitting Complete Date",
		4=> "Linking Complete Date",
		5=> "Embelishment",
		6=> "Washing Completion Date",
		7=> "Sample Completion Date",*/
		8=> "Sample Delivery Date"
	);	
	
	$comp_status_arr=array(1=>"Pending",2=>"Complete");
	
	$width=(count($taskArr)*250)+1500;	
		
		
		$tna_sql="select ID,PO_NUMBER_ID,TASK_NUMBER,ACTUAL_START_DATE from tna_process_mst where task_type=5 and is_deleted=0 and status_active=1";	
        
		$sample_id_list_arr=array_chunk($sample_id_arr,999);
		$p=1;
		foreach($sample_id_list_arr as $sample_id_process)
		{
			if($p==1) $tna_sql .="  and ( po_number_id in(".implode(',',$sample_id_process).")"; 
			else  $tna_sql .=" or po_number_id in(".implode(',',$sample_id_process).")";
			
			$p++;
		}
		$tna_sql .=")";
		
		$actual_start_date_arr=array();
		$tnaDataArray=sql_select( $tna_sql );
		foreach ($tnaDataArray as $row){
			$actual_start_date_arr[$row[PO_NUMBER_ID]][$row[TASK_NUMBER]]=$row[ACTUAL_START_DATE];
		}
	
	ob_start();
	?>
    <form name="sample_acknowledgement_2" id="sample_acknowledgement_2">
        <div style="width:<? echo $width+30;?>px; float:left;">
            <table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption">
                <tr>
                   <td align="center" width="100%" colspan="25" class="form_caption" ><strong style="font-size:18px"><? echo $company_arr[$company_id]; ?></strong></td>
                </tr> 
                <tr>  
                   <td align="center" width="100%" colspan="25">Sample Delivery Pending</td>
                </tr>  
            </table>
            
            <table cellspacing="0" align="left" cellpadding="0" border="1" rules="all" width="<? echo $width;?>" class="rpt_table" >
                <thead>
                     <tr>                   
                        <th rowspan="2" width="35">SL</th>
                        <th rowspan="2" width="80">Req. No</th>
                        <th rowspan="2" width="120">Sample Name</th>
                        <th rowspan="2" width="70">Sample Requisition Date</th>
                        <th rowspan="2" width="70">Requisition Month</th>
                        <th rowspan="2" width="120">Buyer</th>
                        <th rowspan="2" width="80">Style Ref</th>
                        <th rowspan="2" width="70">Sample Start Date</th>
                        <th rowspan="2" width="70">Sample Del. Date</th>
                        <th rowspan="2" width="70">Confirm Del. Date</th>
                        <th rowspan="2" width="80">Dealing Merchandiser</th>
                        <th rowspan="2" width="80">Sample Team</th>
                        <th rowspan="2" width="80">Gauge</th>
                        <th rowspan="2" width="120">Yarn Composition</th>
                        <th rowspan="2" width="80">Yarn Count</th>
                        <th rowspan="2" width="80">Garment Item</th>
                        <th rowspan="2" width="80">Size</th>
                        <th rowspan="2" width="80">Qty</th>
                        <th rowspan="2" width="80">Trims/Embl Req.</th>
                        <? foreach($taskArr as $task_id=>$task_name){?>            
                        <th colspan="3"><? echo $task_name;?></th>
                      	<? } ?>
                        <th rowspan="2" >COMP STATUS</th>
                        <!--<th rowspan="2">REMARKS</th>-->
                    </tr>
                    <tr> 
                        <? foreach($taskArr as $task_id=>$task_name){?>
                        <th width="70">Plan Date</th>
                        <th width="70">Actual Date</th>
                        <th width="65">Delay/Early By</th>
                        <? } ?>
                    </tr>
                
                </thead>
         
                        <?
                         $i=1;
						 $plan_start_date=array();
                            foreach ($dataArray as $row){
								
								list($size)=explode('_',$row[SIZE_DATA]);
								
								$countStrArr=array();
								foreach(explode('-----',$row[COLOR_DATA]) as $cdr){
									$colorPopupDataArr=explode('__',$cdr);
									$countStrArr[$colorPopupDataArr[4]]=$colorPopupDataArr[4];
								}
								
								$plan_start_date[1]=add_date_without_offday($row[DELV_START_DATE],1);
								
								/*if($actual_start_date_arr[$row[SAMPLE_MST_ID]][1]){
									$plan_start_date[2]=$actual_start_date_arr[$row[SAMPLE_MST_ID]][1];
								}
								else
								{
									$plan_start_date[2]=add_date_without_offday($row[DELV_START_DATE],3);
								}*/
								$plan_start_date[2]=add_date_without_offday($plan_start_date[1],1);

								$plan_start_date[3]=add_date_without_offday($plan_start_date[1],3);
								$plan_start_date[4]=add_date_without_offday($plan_start_date[3],1);
								
								if($row[EMBELLISHMENT_STATUS_ID]){
									$plan_start_date[5]=add_date_without_offday($plan_start_date[4],3);
									$plan_start_date[6]=add_date_without_offday($plan_start_date[5],1);
								}
								else
								{
									$plan_start_date[5]='';
									$plan_start_date[6]=add_date_without_offday($plan_start_date[4],1);
								}
								
								//$plan_start_date[7]=add_date_without_offday($plan_start_date[6],1);
								$plan_start_date[7]=$row[DELV_END_DATE];
								//$plan_start_date[8]=add_date_without_offday($plan_start_date[7],1);
								$plan_start_date[8]=$row[CONFIRM_DEL_END_DATE];
								
								
								if(datediff( 'd',$actual_start_date_arr[$row[SAMPLE_MST_ID]][8], $plan_start_date[8]) && $cbo_comp_status==1){continue;}
								$bgcolor=($i%2==0)?$bgcolor="#E9F3FF":"#FFFFFF";

								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" align="center">
									<td align="center"><? echo  $i; ?></td>
                                    <td ><? echo $row[REQUISITION_NUMBER]; ?></td>
                                    <td align="center"><p><? echo $sample_name_arr[$row[SAMPLE_NAME]]; ?></p></td>
                                    <td><? echo change_date_format($row[REQUISITION_DATE]); ?></td>
                                    <td><p><? echo date("M-Y",strtotime($row[REQUISITION_DATE])); ?></p></td>
                                    <td><? echo $buyer_arr[$row[BUYER_NAME]]; ?></td>
                                    <td><? echo $row[STYLE_REF_NO]; ?></td>
                                    <td><? echo change_date_format($row[DELV_START_DATE]); ?></td>
                                    <td><? echo change_date_format($row[DELV_END_DATE]); ?></td>
                                    <td><? echo change_date_format($row[CONFIRM_DEL_END_DATE]); ?></td>
                                    <td><? echo $dealing_mar_arr[$row[DEALING_MARCHANT]]; ?></td>
                                    <td><? echo $team_name_arr[$row[TEAM_LEADER]]; ?></td>
                                    <td><? echo $row[GAUGE];?></td>
                                    <td><? echo $row[FABRIC_DESCRIPTION]; ?></td>
                                    <td>&nbsp;<p><? echo implode(', ',$countStrArr); ?></p></td>
                                    <td><? echo $garments_item[$row[GMTS_ITEM_ID]]; ?></td>
                                    <td><? echo $size; ?></td>
                                    <td><? echo $row[SAMPLE_QTY]; ?></td>
                                    <td><? echo $row[EMBELLISHMENT_STATUS_ID]?"Yes":"No"; ?></td>
                                   
                                    <? foreach($taskArr as $task_id=>$task_name){
										
										if($actual_start_date_arr[$row[SAMPLE_MST_ID]][$task_id]!='' && $plan_start_date[$task_id]!=''){
										$day_diff = datediff( 'd',$actual_start_date_arr[$row[SAMPLE_MST_ID]][$task_id], $plan_start_date[$task_id])-1;	
										}
										else
										{
											$day_diff ='';
										}
									
									if($day_diff===''){$bg='#FF0';}
									elseif($day_diff<0){$bg='#F00';}
									else{$bg='#5ED05A';}
									?> 
                                    
                                    <td><? echo change_date_format($plan_start_date[$task_id]); ?></td>
                                    <td><? echo change_date_format($actual_start_date_arr[$row[SAMPLE_MST_ID]][$task_id]); ?></td>
                                    <td align="center"bgcolor="<? echo $bg;?>"><? echo $day_diff; ?></td>
                                    <? } ?>
                                    
                                    
                                    <td width="80" align="center"><strong><? echo ($actual_start_date_arr[$row[SAMPLE_MST_ID]][8])?$comp_status_arr[2]:$comp_status_arr[1]; ?></strong></td>
                                    <!--<td>< ? echo $row[REMARKS];?></td>-->
								</tr>
								<?
								$i++;
							}
                        ?>
                    </tbody>
                </table>

        </div>
    </form>
	<?
	$emailBody = ob_get_contents();
    ob_clean();


	$to="";
	$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=29 AND a.MAIL_TYPE=1 and b.mail_user_setup_id=c.id and a.company_id=$company_id  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	$mail_sql2=sql_select($sql2);
	foreach($mail_sql2 as $row)
	{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}

	$subject="Sample Delivery Pending";
	//if($to!=""){echo send_mail_mailer( $to, $subject, $emailBody );}else{ echo 'Sorry. Email not found';}
	if($_REQUEST['isview']==1){
		echo $emailBody;
	}
	else{
	  if($to!=""){echo send_mail_mailer( $to, $subject, $emailBody );}else{ echo 'Sorry. Email not found';}
}

}




}

?>
