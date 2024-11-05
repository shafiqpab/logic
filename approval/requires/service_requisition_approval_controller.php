<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php'); 

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

if($action=='user_popup')
{
	echo load_html_head_contents("Popup Info","../../",1, 1,'',1,'');
	?>	
	<script>
	  function js_set_value(id)
	  { 
		document.getElementById('selected_id').value=id;
		parent.emailwindow.hide();
	  }
	</script>
	<form>
	        <input type="hidden" id="selected_id" name="selected_id" /> 
	       <?php
	        $custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');	
			$Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;

			$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation,b.sequence_no,b.GROUP_NO from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.entry_form=61 order by sequence_no";
			//echo $sql;
			$arr=array (2=>$custom_designation,3=>$Department);
			echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department,Seq,Group", "100,120,150,150,60,60","750","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id,sequence_no,GROUP_NO", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,7,7' ) ;
			?>
	</form>
	<script language="javascript" type="text/javascript">
	  setFilterGrid("tbl_style_ref");
	</script>
	<?
}


function getSequence($parameterArr=array()){
	$lib_department_str=implode(',',(array_keys($parameterArr['lib_department_arr'])));
	$lib_location_str=implode(',',(array_keys($parameterArr['lib_location_arr'])));
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,ENTRY_FORM,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT,LOCATION FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['ENTRY_FORM']} AND IS_DELETED = 0 order by SEQUENCE_NO";
	//  echo $sql;die;
	$sql_result=sql_select($sql);
	$dataArr=array();
	foreach($sql_result as $rows){		
		if($rows['DEPARTMENT']==''){$rows['DEPARTMENT']=$lib_department_str;}
		if($rows['LOCATION']==''){$rows['LOCATION']=$lib_location_str;}
		$dataArr['sequ_by'][$rows['SEQUENCE_NO']]=$rows;
		$dataArr['user_by'][$rows['USER_ID']]=$rows;
		$dataArr['sequ_arr'][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
	}
 
	return $dataArr;
}


function getFinalUser($parameterArr=array()){
	$lib_department_str = implode(',',(array_keys($parameterArr['lib_department_arr'])));
	$lib_location_str = implode(',',(array_keys($parameterArr['lib_location_arr'])));

	//echo $lib_location_str;die;

	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,ENTRY_FORM,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT,LOCATION FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['ENTRY_FORM']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	//echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){
		if($rows['DEPARTMENT']==''){$rows['DEPARTMENT']=$lib_department_str;}
		if($rows['LOCATION']==''){$rows['LOCATION']=$lib_location_str;}

		$usersDataArr[$rows['USER_ID']]['LOCATION']=explode(',',$rows['LOCATION']);
		$usersDataArr[$rows['USER_ID']]['DEPARTMENT']=explode(',',$rows['DEPARTMENT']);
		$userSeqDataArr[$rows['USER_ID']]=$rows['SEQUENCE_NO'];
	
	}

	$finalSeq=array();
	foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
		foreach($userSeqDataArr as $user_id=>$seq){
			if( (in_array($bbtsRows['location'],$usersDataArr[$user_id]['LOCATION']) ||  $bbtsRows['location'] == 0)
			&& (in_array($bbtsRows['department'],$usersDataArr[$user_id]['DEPARTMENT']) ||  $bbtsRows['department'] == 0)
			){
				$finalSeq[$sys_id][$user_id]=$seq;
			}
		}
	}

	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr);
}


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$company_name = str_replace("'","",$cbo_company_name);
	$cbo_req_year = str_replace("'","",$cbo_req_year);
	$txt_req_no = str_replace("'","",$txt_req_no);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$approval_type = str_replace("'","",$cbo_approval_type);
	$txt_alter_user_id = str_replace("'","",$txt_alter_user_id);
	$app_user_id = ($txt_alter_user_id) ? $txt_alter_user_id : $user_id;

	$department_arr = return_library_array( "select id, department_name from lib_department", 'id', 'department_name' );
	$location_arr = return_library_array( "select id,LOCATION_NAME from LIB_LOCATION where COMPANY_ID=$company_name", 'id', 'LOCATION_NAME' );

 
	if ($txt_req_no != '') $where_con = " and a.requ_prefix_num=$txt_req_no";
	if ($txt_date_from != '' && $txt_date_to != '')
	{
		$txt_date_from = date("d-M-Y", strtotime($txt_date_from));
		$txt_date_to = date("d-M-Y", strtotime($txt_date_to));
		$where_con .= " and a.requisition_date between '$txt_date_from' and '$txt_date_to'";
	}
	if ($cbo_req_year != 0) $where_con .= " and TO_CHAR(a.insert_date,'YYYY')=$cbo_req_year";
	
	
	$electronicDataArr=getSequence(array('company_id'=>$company_name,'ENTRY_FORM'=>61,'user_id'=>$app_user_id,'lib_buyer_arr'=>0,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_location_arr'=>$location_arr,'lib_department_arr'=>$department_arr));

	 //echo $electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO'];die;
	 
	?>

	<script type="text/javascript">
		function openmypage_app_cause(wo_id,app_type,i)
		{
			var txt_appv_cause = $("#txt_appv_cause_"+i).val();
			var approval_id = $("#approval_id_"+i).val();
			var data=wo_id+"_"+app_type+"_"+txt_appv_cause+"_"+approval_id;
			var title = 'Approval Cause Info';
			var page_link = 'requires/service_requisition_approval_controller.php?data='+data+'&action=appcause_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
				$('#txt_appv_cause_'+i).val(appv_cause.value);
			}
		}

		function openmypage_reqdetails(requ_id,requ_no)
		{
			var data=requ_id+"**"+requ_no;
			var title = 'Requisition Details';
			var page_link = 'requires/service_requisition_approval_controller.php?data='+data+'&action=reqdetails_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=250px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				/*var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
				$('#txt_appv_cause_'+i).val(appv_cause.value);*/
			}
		}
	</script>
	<?
 
 
	if($approval_type==0) // Un-Approve
	{  
		
		//Match data..................................
		if($electronicDataArr['user_by'][$app_user_id]['DEPARTMENT']){
			$where_con .= " and a.DEPARTMENT_ID in(".$electronicDataArr['user_by'][$app_user_id]['DEPARTMENT'].",0)";
			$electronicDataArr['sequ_by'][0]['DEPARTMENT']=$electronicDataArr['user_by'][$app_user_id]['DEPARTMENT'];
		}

		if($electronicDataArr['user_by'][$app_user_id]['LOCATION']){
			$where_con .= " and a.LOCATION_ID in(".$electronicDataArr['user_by'][$app_user_id]['LOCATION'].",0)";
			$electronicDataArr['sequ_by'][0]['LOCATION']=$electronicDataArr['user_by'][$app_user_id]['LOCATION'];
		}
 
		$data_mast_sql = "select A.ID,a.LOCATION_ID,a.DEPARTMENT_ID from inv_purchase_requisition_mst a where a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and a.is_approved<>1 AND a.ENTRY_FORM = 526 and a.READY_TO_APPROVE=1 and A.COMPANY_ID=$company_name $where_con";
		 //echo $data_mast_sql;die;

		$tmp_sys_id_arr=array();
		$data_mas_sql_res=sql_select( $data_mast_sql );
         //print_r( $data_mas_sql_res);
		 foreach ($data_mas_sql_res as $row)
		 { 
			 for($seq=($electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO']-1);$seq>=0; $seq-- ){
				 				 
				 if((in_array($row['DEPARTMENT_ID'],explode(',',$electronicDataArr['sequ_by'][$seq]['DEPARTMENT'])) || $row['DEPARTMENT_ID']==0)
				 && (in_array($row['LOCATION_ID'],explode(',',$electronicDataArr['sequ_by'][$seq]['LOCATION'])) || $row['LOCATION_ID']==0)
				 )
				 {
					 if($electronicDataArr['sequ_by'][$seq]['BYPASS']==1){
						 $tmp_sys_id_arr[$seq][$row['ID']]=$row['ID'];	
					 }
					 else{
						 $tmp_sys_id_arr[$seq][$row['ID']]=$row['ID'];
						 break;
					 }

				 }
			 }
		 }
		 //..........................................Match data;
	
		//print_r($tmp_sys_id_arr);die;
		
		$sql='';
		for($seq=0;$seq<=count($electronicDataArr['sequ_arr']); $seq++ ){
			$sys_con = where_con_using_array($tmp_sys_id_arr[$seq],0,'a.ID');

			if($tmp_sys_id_arr[$seq]){
				if($sql!=''){$sql .=" UNION ALL ";}
				$sql .= "select A.ID, A.REQU_NO, A.REQU_PREFIX_NUM ,A.REMARKS, 
				A.COMPANY_ID, TO_CHAR(a.insert_date,'YYYY') AS YEAR, listagg(b.item_category, ',') within group (order by b.item_category) as ITEM_CATEGORY_ID , A.REQUISITION_DATE, A.DELIVERY_DATE, 0 AS APPROVAL_ID, A.IS_APPROVED, 
				A.DEPARTMENT_ID, SUM(B.AMOUNT) AS REQ_VALUE from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b where a.id=b.mst_id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.is_approved<>1 and a.READY_TO_APPROVE=1 and A.COMPANY_ID=$company_name  and a.ENTRY_FORM=526 and a.APPROVED_SEQU_BY=$seq $sys_con group by a.id,a.remarks, a.company_id, a.requ_no, a.requ_prefix_num ,TO_CHAR(a.insert_date,'YYYY'), a.requisition_date, a.delivery_date, a.is_approved, a.department_id";
			}
		}
	}
	else // approval process start
	{

		$sql = " select A.ID, A.REQU_NO, A.REQU_PREFIX_NUM ,A.REMARKS, A.COMPANY_ID, TO_CHAR(a.insert_date,'YYYY') AS YEAR, listagg(b.item_category, ',') within group (order by b.item_category) as ITEM_CATEGORY_ID , A.REQUISITION_DATE, A.DELIVERY_DATE, 0 AS APPROVAL_ID, A.IS_APPROVED, A.DEPARTMENT_ID, SUM(B.AMOUNT) AS REQ_VALUE from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b,APPROVAL_MST c where a.id=b.mst_id and c.mst_id=a.id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.READY_TO_APPROVE=1 and A.COMPANY_ID=$company_name and c.SEQUENCE_NO={$electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO']} and a.APPROVED_SEQU_BY=c.SEQUENCE_NO and a.ENTRY_FORM=526 and c.ENTRY_FORM=61  $where_con group by a.id,a.remarks, a.company_id, a.requ_no, a.requ_prefix_num ,TO_CHAR(a.insert_date,'YYYY'), a.requisition_date, a.delivery_date, a.is_approved, a.department_id order by a.id";		

	}
	
 	// echo $sql;
	$nameArray=sql_select($sql);
	// foreach ($nameArray as $row){
	// 	$mst_id_arr[$row['ID']]=$row['ID'];
	// }


	// $sql_unapproved=sql_select("select * from fabric_booking_approval_cause where  entry_form=61 and is_deleted=0 and status_active=1 and BOOKING_ID in(".implode(',',$mst_id_arr).")");
	// $unapproved_request_arr=array();
	// $approval_case_arr=array();
	// foreach($sql_unapproved as $rowu)
	// {
	// 	if($rowu[csf('approval_type')]==2)
	// 	{
	// 		$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
	// 	}
	// 	$approval_case_arr[$rowu[csf('booking_id')]][$rowu[csf('approval_type')]]=$rowu[csf('approval_cause')];
	// }

	$user_arr=return_library_array( "select id, user_name from user_passwd", 'id', 'user_name' );	
	$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=19 and report_id =238 and is_deleted=0 and status_active=1");
 
    $format_ids=explode(",",$print_report_format);
    // print_r($format_ids);
    if($approval_type==0) $width=1200;
	else $width=1100;
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:<?= $width+20; ?>px; margin-top:10px">
        <legend>Purchase Requisition Approval</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width; ?>" class="rpt_table" align="left" >
                <thead>
                	<th width="50"></th>
                    <th width="50">SL</th>
                    <th width="60">Req. No</th>
                    <th width="60">Year</th>
                    <th width="150">Item Category</th>
                    <th width="100">Depatment</th>
                    <th width="100">Requisition Value</th>                    
                    <th width="70">Requisition Date</th>
                    <th width="70" title="Delivery Date">In-House Demand date</th>
                    <th width="130">Last Approval Date and Person</th>
					<th width="100">Un-approve request</th>
					<? if ($approval_type==0) { ?>
						<th width="100">Not Appv. Cause</th>
					<? } ?>
					<th>&nbsp;</th>
                </thead>
            </table>
            <div style="width:<?= $width+20; ?>px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width; ?>" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <? 
						$i=1; $j=0;						
						foreach ($nameArray as $row)
						{
							$bgcolor = ($i%2==0)?"#E9F3FF":"#FFFFFF";
					 					
							$variable='';
							if($format_ids[$j]==109) // Print Report 2
							{
								$type=1;
							}
							elseif($format_ids[$j]==110) // Print Report 2
							{
								$type=2;
							}							
							else
							{
								$type=0;
							}
							$variable="<a href='#'  title='".$format_ids[$j]."'  onclick=\"print_report('".$row[csf('company_id')]."','".$row[csf('id')]."','Purchase Requisition',1,'".$type."')\"> ".$row[csf('requ_prefix_num')]." <a/>";


							?>
							<tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" id="tr_<?= $i; ?>"> 
								<td width="50" align="center" valign="middle">
									<input type="checkbox" id="tbl_<?= $i; ?>" />
									<input id="req_id_<?= $i;?>" name="req_id[]" type="hidden" value="<?= $row[csf('id')]; ?>" /> 
									<input id="requisition_id_<?= $i;?>" name="requisition_id[]" type="hidden" value="<?= $row[csf('id')]; ?>" /><!--this is uesd for delete row-->
									<input id="approval_id_<?=$i;?>" name="approval_id[]" type="hidden" value="<?=$row[csf('approval_id')]; ?>" />
								</td>   
								<td width="50" align="center"><?= $i; ?></td>
								<td width="60" align="center"><p><?= $variable; ?></p></td>
								<td width="60" align="center"><p><?= $row[csf('year')]; ?></p></td>
								<td width="150">
									<p>
										<?
										$item_category_names = ""; $item_id_arr = array();
										$item_id_arr= array_unique(explode(",", $row[csf("item_category_id")]));
										foreach($item_id_arr as $item_id)
										{
											$item_category_names .= $item_category[$item_id].",";
										}
										echo chop($item_category_names, ",");
										?>
									</p>
								</td>
								<td width="100" align="center"><p><? echo $department_arr[$row[csf('department_id')]]; ?></p></td>
								<td width="100" align="right"><p><? echo number_format($row[csf('req_value')],2); ?></p></td>
								<td width="70" align="center"><? if($row[csf('requisition_date')]!="0000-00-00") echo change_date_format($row[csf('requisition_date')]); ?>&nbsp;</td>
								<td width="70" align="center"><? if($row[csf('delivery_date')]!="0000-00-00") echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</td>
								<td width="130" align="center"><p><? echo $row[csf('approved_date')].'<br>'.$user_arr[$row[csf('approved_by')]]; ?></p></td>
								<td width="100" align="center"> 
									<p>
										<? 
										if($approval_type==1)
										{
											$unapproved_request=$unapproved_request_arr[$row[csf('id')]]; 
											if($unapproved_request!='')
											{
												$view_request='View';
											}
										}
										else
										{
											$unapproved_request=''; 
											$view_request='';
										}									
										?>
										<a href="#report_details" onClick="openmypage('<? echo $unapproved_request; ?>','unapprove_request_action','Unapprove Request Details')"><? echo $view_request; ?></a>
									</p>
								</td>
								<?
								if($approval_type==0)
								{
									$casues=$approval_case_arr[$unapprove_value_id][$approval_type]
									?>
									<td align="center" style="word-break:break-all">
										<input name="txt_appv_cause[]" class="text_boxes" readonly placeholder="Please Browse" ID="txt_appv_cause_<?=$i;?>" style="width:70px" value="<? echo $casues;?>" maxlength="50" title="Maximum 50 Character" onClick="openmypage_app_cause(<?=$unapprove_value_id; ?>,<?=$approval_type; ?>,<?=$i;?>)">&nbsp;
									</td>
									<? 
								}
								?>
								<td align="center"><input type="button" class="formbutton" id="reqdtls_<? echo $i;?>" style="width:100px" value="Req. Details" onClick="openmypage_reqdetails(<? echo $row["ID"]; ?>, '<? echo $row['REQU_NO']; ?>')"/></td>																	
							</tr>
							<?
							$i++;						
						}
                        ?>
                    </tbody>
                </table>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width; ?>" class="rpt_table">
					<tfoot>
						<td width="50" align="center" ><input type="checkbox" id="all_check" onClick="check_all('all_check')" /></td>
						<td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/><input type="button" value="Deny" class="formbutton" style="width:100px;display:<?=($approval_type==1)?'none':'';?><?=$denyBtn; ?>" onClick="submit_approved(<?=$i; ?>,5);"/></td>
					</tfoot>
				</table>
            </div>
            
        </fieldset>
    </form>         
	<?
	exit();	
}

if ($action=="approve")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$con = connect();

	$msg=''; $flag=''; $response='';

	$company_name = str_replace("'","",$cbo_company_name);
	$approval_type = str_replace("'","",$approval_type);
	$target_ids = str_replace("'","",$req_nos);
	$appv_causes = str_replace("'","",$appv_causes);
	$txt_alter_user_id = str_replace("'","",$txt_alter_user_id);
	$app_user_id = ($txt_alter_user_id) ? $txt_alter_user_id : $user_id;
	$target_id_arr = explode(',',$target_ids);
	$appv_causes_arr = explode(',',$appv_causes);



	$department_arr = return_library_array( "select id, department_name from lib_department", 'id', 'department_name' );
	$location_arr = return_library_array( "select id,LOCATION_NAME from LIB_LOCATION where COMPANY_ID=$company_name", 'id', 'LOCATION_NAME' );

	$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($target_ids) and entry_form=61 group by mst_id","mst_id","approved_no");
 
 	
	
	if($approval_type==0)
	{
		//------------------
		$sql="select A.ID,a.IS_APPROVED,a.DEPARTMENT_ID, b.LOCATION_ID from inv_purchase_requisition_mst a where a.id in($target_ids) and a.entry_form=526 and a.READY_TO_APPROVE=1 and a.status_active=1 and a.is_deleted=0";
		
		$sqlResult=sql_select( $sql );
		foreach ($sqlResult as $row)
		{
			$matchDataArr[$row['ID']]=array('location'=>$row['LOCATION_ID'],'department'=>$row['DEPARTMENT_ID']);
			$last_app_status_arr[$row['ID']] = $row['IS_APPROVED'];
		}
		
		$finalDataArr = getFinalUser(array('company_id'=>$company_name,'ENTRY_FORM'=>61,'lib_buyer_arr'=>0,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_location_arr'=>$location_arr,'lib_department_arr'=>$department_arr,'match_data'=>$matchDataArr));
 
		$sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
		$user_sequence_no = $finalDataArr['user_seq'][$app_user_id];

		//echo $user_sequence_no;die;

		
		
		$id = return_next_id( "id","approval_mst", 1 ) ;
		$ahid=return_next_id( "id","approval_history", 1 ) ;	

		foreach($target_id_arr as $key => $mst_id)
		{		
			$approved = (max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no)?1:3;

			$approved_no = $max_approved_no_arr[$mst_id]*1;
			if($last_app_status_arr[$mst_id] == 0 || $last_app_status_arr[$mst_id] == 2)
			{
				$approved_no = $approved_no+1;
				$approved_no_array[$mst_id] = $approved_no;
			}

			
			if($data_array!=''){$data_array.=",";}
			$data_array.="(".$id.",61,".$mst_id.",".$user_sequence_no.",".$app_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."',".$approved.")"; 
			$id=$id+1;

			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$ahid.",61,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$app_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,".$approved.",'".$appv_causes_arr[$key]."')";
			$ahid++;
			
			//mst data.......................
			$data_array_up[$mst_id] = explode(",",("".$approved.",".$user_sequence_no.",".$app_user_id.",'".$pc_date_time."'"));
		}
		
		 //print_r($data_array_up);die;
		
		
		$flag=1;
		
		if($flag==1) 
		{
			$field_array="id, entry_form, mst_id, sequence_no, approved_by, approved_date, inserted_by, insert_date, user_ip,APPROVED";
			//echo $data_array;die;
			$rID1 = sql_insert("approval_mst",$field_array,$data_array,0);
			if($rID1) $flag=1; else $flag=0; 
		}
		
		
		if($flag==1) 
		{
			$field_array_up="is_approved*approved_sequ_by*APPROVED_BY*APPROVED_DATE";
			$sql = bulk_update_sql_statement( "inv_purchase_requisition_mst", "id", $field_array_up, $data_array_up, $target_id_arr );
			//echo $sql;
			$rID2 = execute_query($sql);
			if($rID2) $flag=1; else $flag=0; 
		}

		if($flag==1)
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING,APPROVED,COMMENTS";
			$rID3 = sql_insert("approval_history",$field_array,$history_data_array,0);
			if($rID3) $flag=1; else $flag=0;
		}
		

		
		if(count($approved_no_array)>0){
			$approved_string="";
			foreach($approved_no_array as $key=>$value)
			{
				$approved_string.=" WHEN $key THEN $value";
			}
			
			$approved_string_mst="CASE id ".$approved_string." END";
			$approved_string_dtls="CASE mst_id ".$approved_string." END";
			
			$sql_insert="INSERT into inv_pur_requisition_mst_hist(id, hist_mst_id, approved_no, entry_form, requ_no, requ_no_prefix, requ_prefix_num, company_id, item_category_id, supplier_id, location_id, division_id, department_id, section_id, requisition_date, store_name, pay_mode, source, cbo_currency, delivery_date, do_no, attention, remarks, terms_and_condition, manual_req, ready_to_approve, is_approved, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date) 
			select	
			'', id, $approved_string_mst, entry_form, requ_no, requ_no_prefix, requ_prefix_num, company_id, item_category_id, supplier_id, location_id, division_id, department_id, section_id, requisition_date, store_name, pay_mode, source, cbo_currency, delivery_date, do_no, attention, remarks, terms_and_condition, manual_req, ready_to_approve, is_approved, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date from  inv_purchase_requisition_mst where id in ($target_ids)";
			
			//echo $sql_insert;
			$sql_insert_dtls="INSERT into  inv_pur_requisition_dtls_hist(id, approved_no, mst_id, product_id, user_code_maintain, required_for, job_id, job_no, buyer_id, style_ref_no, color_id, count_id, composition_id, com_percent, yarn_type_id, yarn_inhouse_date, cons_uom, quantity, rate, amount, stock, remarks, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted) 
			select	
			'', $approved_string_dtls, mst_id, product_id, user_code_maintain, required_for, job_id, job_no, buyer_id, style_ref_no, color_id, count_id, composition_id, com_percent, yarn_type_id, yarn_inhouse_date, cons_uom, quantity, rate, amount, stock, remarks, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from  inv_purchase_requisition_dtls where mst_id in ($target_ids)";

			
			if($flag==1) 
			{
				$rID4 = execute_query($sql_insert,0);
				if($rID4) $flag=1; else $flag=0; 
			}       
			
			if($flag==1) 
			{
				$rID5 = execute_query($sql_insert_dtls,1);
				if($rID5) $flag=1; else $flag=0; 
			} 
		}
		
		//echo '21**'.$rID1.'**'.$rID2.'**'.$rID3.'**'.$rID4.'**'.$rID5;oci_rollback($con);die;

		 if($flag==1){$msg=19;}
		
	
	}
	else if($approval_type==5)
	{
		
		$ahid=return_next_id( "id","approval_history", 1 ) ;	
		foreach($target_id_arr as $key => $mst_id)
		{		
			$approved_no = $max_approved_no_arr[$mst_id]*1;
			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$ahid.",61,".$mst_id.",".$approved_no.",'".$user_sequence_no."',0,".$app_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,2,'".$appv_causes_arr[$key]."')";
			$ahid++;
		}
		
		
		
		$flag=1;

		if($flag==1) 
		{
			$rID1 = sql_multirow_update("inv_purchase_requisition_mst","is_approved*ready_to_approve*approved_sequ_by","2*0*0","id",$target_ids,0); 
			if($rID1) $flag=1; else $flag=0; 
		}
 
		if($flag==1) 
		{
			$query="delete from approval_mst WHERE entry_form=61 and mst_id in ($target_ids)";
			$rID2 = execute_query($query,1); 
			if($rID2) $flag=1; else $flag=0; 
		}

		if($flag==1)
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING,APPROVED,COMMENTS";
			$rID3 = sql_insert("approval_history",$field_array,$history_data_array,0);
			if($rID3) $flag=1; else $flag=0;
		}


		if($flag==1){$msg=50;}
		//echo "0**$rID**$rID2**$rID3**$rID4**$rID5";oci_rollback($con);die;	 
	}
	else
	{
		$ahid=return_next_id( "id","approval_history", 1 ) ;	
		foreach($target_id_arr as $key => $mst_id)
		{		
			$approved_no = $max_approved_no_arr[$mst_id]*1;
			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$ahid.",61,".$mst_id.",".$approved_no.",'".$user_sequence_no."',0,".$app_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,'".$appv_causes_arr[$key]."')";
			$ahid++;
		}
		
		
		$flag=1;

		if($flag==1) 
		{
			$rID1 = sql_multirow_update("inv_purchase_requisition_mst","is_approved*ready_to_approve*approved_sequ_by","0*0*0","id",$target_ids,0); 
			if($rID1) $flag=1; else $flag=0; 
		}
 
		if($flag==1) 
		{
			$query="delete from approval_mst WHERE entry_form=61 and mst_id in ($target_ids)";
			$rID2 = execute_query($query,1); 
			if($rID2) $flag=1; else $flag=0; 
		}

		if($flag==1)
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING,APPROVED,COMMENTS";
			$rID3 = sql_insert("approval_history",$field_array,$history_data_array,0);
			if($rID3) $flag=1; else $flag=0;
		} 
		
		
		if($flag==1){$msg=20;}
		
		
	}
	
	
		
	if($flag==1)
	{
		oci_commit($con);
		echo $msg."**".$response;
	}
	else
	{
		oci_rollback($con);
		echo $msg."**".$response;
	}

	disconnect($con);
	die;
	
}


if ($action=="save_update_delete_requ_qty")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	for ($i=1; $i<=$tot_row; $i++)
    {
		$req_dtls_id = "req_dtls_id_".$i;
		if(str_replace("'","",$$req_dtls_id)) $all_req_dtls_ids[str_replace("'","",$$req_dtls_id)]=str_replace("'","",$$req_dtls_id);
	}
	
	if(count($all_req_dtls_ids)>0)
	{
		$requisition_arr=return_library_array( "select id, rate from inv_purchase_requisition_dtls where id in(".implode(",",$all_req_dtls_ids).") ", 'id', 'rate' );
	}
	//echo "10**<pre>";print_r($requisition_arr);oci_rollback($con);disconnect($con);die;
	$field_array_up = "quantity*amount*updated_by*update_date";
	for ($i=1; $i<=$tot_row; $i++)
    {
		$txtqty = "txtqty_".$i;
		$req_dtls_id = "req_dtls_id_".$i;
		$amount=str_replace("'",'',$$txtqty)*$requisition_arr[str_replace("'",'',$$req_dtls_id)];
		$updateID_array[] = str_replace("'",'',$$req_dtls_id);
		$data_array_up[str_replace("'",'',$$req_dtls_id)] = explode("*",("".$$txtqty."*'".$amount."'*".$user_id."*'".$pc_date_time."'"));	
	}

	//echo bulk_update_sql_statement("inv_purchase_requisition_dtls","id",$field_array_up,$data_array_up,$updateID_array);
	$dtlsrID=execute_query(bulk_update_sql_statement("inv_purchase_requisition_dtls","id",$field_array_up,$data_array_up,$updateID_array),1);

	if($db_type==0)
	{
		if($dtlsrID)
		{
			mysql_query("COMMIT");
			echo "1**";
		}
		else
		{
			mysql_query("ROLLBACK");
			echo "10**";
		}
	}
	if($db_type==2 || $db_type==1)
	{
	    if($dtlsrID)
		{
			oci_commit($con);
			echo "1**";
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

if($action=="reqdetails_popup")
{ 
	echo load_html_head_contents("Requ. Details","../../", 1, 1, $unicode,1);
	extract($_REQUEST);
	$ex_data=explode("**",$data);

	$sql="SELECT a.id, b.id as dtls_id, b.service_for , b.service_details, b.quantity, b.product_id, c.item_category_id, c.item_account, c.item_description, c.item_size, c.item_group_id, c.sub_group_name, c.order_uom as unit_of_measure, d.item_name,b.rate,  b.amount
	from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b 
	left join product_details_master c on  b.product_id=c.id and c.status_active=1 and c.is_deleted=0
    left join lib_item_group d on c.item_group_id=d.id and d.status_active=1 and d.is_deleted=0
	where a.id=b.mst_id  and a.id=$ex_data[0] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	// echo $sql;
	$sql_res=sql_select($sql);
	?>
	<script>
		var permission='<? echo $permission; ?>';
		function js_set_value()
		{
			parent.emailwindow.hide(); 
		}

		function fn_requisition_details_qtyupdate(operation)
		{
			var tot_row=$('#tbl_details tbody tr').length;
			var data_all="";
			for(var i=1; i<=tot_row; i++)
			{
				if (form_validation('txtqty_'+i,'Quantity')==false)
				{
					return;
				}
				data_all = data_all+get_submitted_data_string('txtqty_'+i+'*req_dtls_id_'+i,"../");				
			}

			var data="action=save_update_delete_requ_qty&tot_row="+tot_row+data_all;
			// alert (data);//return;
			freeze_window(operation);
			http.open("POST","service_requisition_approval_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fn_requisition_details_qtyupdate_reponse;					
		}

		function fn_requisition_details_qtyupdate_reponse()
		{
			if(http.readyState == 4)
			{				
				var reponse=http.responseText.split('**');
				if (reponse[0]==1) alert("Data is updated Successfully");
				else alert("Data is not updated Successfully");
				release_freezing();
			}
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
        <? echo load_freeze_divs ("../../",$permission,1); ?>
    	<fieldset style="width:970px; margin-top:10px;">
        <legend>Purchase Requisition Details</legend>
        <form name="purchaserequisition_2" id="purchaserequisition_2" autocomplete="off">
        	<table class="rpt_table" width="950" cellspacing="0" cellpadding="0" align="center" id="tbl_details">
                <thead>
                    <tr>
                    	<th width="100">Service For</th>
	                    <th width="100">Service Details</th>
	                    <th width="150">Item Description</th>
	                    <th width="100">Item Category</th>
	                    <th width="100">Item Group</th>
	                    <th width="100">Item Sub. Group</th>
	                    <th width="100">Item Size</th>
	                    <th width="60">Service UOM</th>
	                    <th class="must_entry_caption" title="Must Entry Field." width="80"> <font color="blue">Quantity</font></th>
	                    <th width="50">Rate</th>
	                    <th width="50">Amount</th>
                	</tr>
            	</thead>               
                <tbody>
                	<?
                	$i=1;
                	$bgcolor="#E9F3FF";
                	foreach ($sql_res as $row) 
                	{                		
	                	?>
	                    <tr bgcolor="<?= $bgcolor; ?>">
	                    	 <input type="hidden" name="req_dtls_id[]" id="req_dtls_id_<?= $i; ?>" value="<?= $row[csf('dtls_id')]; ?>">
	                        <td><?= $service_for_arr[$row[csf('service_for')]]; ?></td>
	                        <td><?= $row[csf('service_details')]; ?></td>
	                        <td><?= $row[csf('item_description')]; ?></td>
	                        <td><?= $item_category[$row[csf('item_category_id')]]; ?></td>
	                        <td><?= $row[csf('item_name')]; ?></td>
	                        <td><?= $row[csf('sub_group_name')]; ?></td>
	                        <td><?= $row[csf('item_size')]; ?></td>
	                        <td><?= $unit_of_measurement[$row[csf('unit_of_measure')]]; ?></td>
	                        <td align="right"><input type="text" name="txtqty[]" id="txtqty_<?= $i; ?>" style="width:80px" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" /></td>
							<td align="right"><?= number_format($row[csf('rate')],2); ?></td>
							<td align="right"><?= number_format($row[csf('amount')],2); ?></td>
	                    </tr>
	                    <?
	                    $i++;
	                }
	                ?>    
                </tbody>
            </table>

                <table width="100%">
                	<tr>
                        <td colspan="22" height="20" valign="middle" align="center" class="button_container"> 
                            <input type="button" class="formbutton" id="updateqtyid" name="updateqtyid" value="Update" onClick="fn_requisition_details_qtyupdate(1)" style="width:80px" />                           
                        </td>    
                    </tr>
                </table>
        </form>
	    </fieldset>
        </div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?	
}

if ($action=="unapprove_request_action")
{
	echo load_html_head_contents("Un Approval Request","../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data_all=explode('_',$data);
	$requ_unapprove=$data_all[1];
	//$unapp_request=$data_all[1];
	?>
	<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                    	<textarea name="unappv_request" id="unappv_request" readonly class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"><? echo $requ_unapprove;?> </textarea>
                        <Input type="hidden" name="wo_id" class="text_boxes" ID="wo_id" value="<? echo $wo_id; ?>" style="width:30px" />
                       
                    </td>
                </tr>
            </table> 
              
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >
                
               
                <tr>
                    <td align="center">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px" />
                    </td>	  
                </tr>
            </table>
            </fieldset>
            </form>
			  <script src="../includes/functions_bottom.js" type="text/javascript"></script>
        </div>

	<?
}


if ($action=="load_drop_down_store")
{
	$permitted_store_id=return_field_value("STORE_LOCATION_ID","user_passwd","id='".$user_id."'");
	if($permitted_store_id){$storCon=" and id in($permitted_store_id)";}
	echo create_drop_down( "cbo_store_id", 130, "select id,store_name from lib_store_location where status_active=1 and is_deleted=0 and company_id=$data $storCon order by store_name","id,store_name", 1, "-- All --","","",0,"","","","");
	exit();
}



if ($action=="save_update_delete_appv_cause")
{
	//$approval_id
	$approval_type=str_replace("'","",$appv_type);


	if($approval_type==0)
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

			$approved_no_history=return_field_value("approved_no","approval_history","entry_form=61 and mst_id=$wo_id and approved_by=$user_id");
			$approved_no_cause=return_field_value("approval_no","fabric_booking_approval_cause","entry_form=61 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

			//echo "shajjad_".$approved_no_history.'_'.$approved_no_cause; die;

			if($approved_no_history=="" && $approved_no_cause=="")
			{
				//echo "insert"; die;

				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",61,".$user_id.",".$wo_id." ,".$appv_type.",0,".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
				//echo $rID; die;

				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT");
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						mysql_query("ROLLBACK");
						echo "10**".$rID;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID )
					{
						oci_commit($con);
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						oci_rollback($con);
						echo "10**".$rID;
					}
				}
				disconnect($con);
				die;
			}
			else if($approved_no_history=="" && $approved_no_cause!="")
			{
				$con = connect();
				if($db_type==0)
				{
					mysql_query("BEGIN");
				}

				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=61 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
				$data_array="".$page_id."*61*".$user_id."*".$wo_id."*".$appv_type."*0*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

				 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);

				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT");
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						mysql_query("ROLLBACK");
						echo "10**".$rID;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID )
					{
						oci_commit($con);
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						oci_rollback($con);
						echo "10**".$rID;
					}
				}
				disconnect($con);
				die;
			}
			else if($approved_no_history!="" && $approved_no_cause!="")
			{
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=61 and mst_id=$wo_id and approved_by=$user_id");
				$max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=61 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$page_id.",61,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
					//echo $rID; die;

					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT");
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							mysql_query("ROLLBACK");
							echo "10**".$rID;
						}
					}
					else if($db_type==2 || $db_type==1 )
					{
						if($rID )
						{
							oci_commit($con);
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							oci_rollback($con);
							echo "10**".$rID;
						}
					}
					disconnect($con);
					die;
				}
				else if($max_appv_no_his==$max_appv_no_cause)
				{
					$con = connect();
					if($db_type==0)
					{
						mysql_query("BEGIN");
					}

					$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=61 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

					$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
					$data_array="".$page_id."*61*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

					 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);

					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT");
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							mysql_query("ROLLBACK");
							echo "10**".$rID;
						}
					}
					else if($db_type==2 || $db_type==1 )
					{
						if($rID )
						{
							oci_commit($con);
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							oci_rollback($con);
							echo "10**".$rID;
						}
					}
					disconnect($con);
					die;
				}
			}
			else if($approved_no_history!="" && $approved_no_cause=="")
			{
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=61 and mst_id=$wo_id and approved_by=$user_id");
				$max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=61 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$page_id.",61,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
					//echo $rID; die;

					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT");
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							mysql_query("ROLLBACK");
							echo "10**".$rID;
						}
					}
					else if($db_type==2 || $db_type==1 )
					{
						if($rID )
						{
							oci_commit($con);
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							oci_rollback($con);
							echo "10**".$rID;
						}
					}
					disconnect($con);
					die;
				}
				else if($max_appv_no_his==$max_appv_no_cause)
				{
					$con = connect();
					if($db_type==0)
					{
						mysql_query("BEGIN");
					}

					$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=61 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

					$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
					$data_array="".$page_id."*61*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

					 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);

					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT");
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							mysql_query("ROLLBACK");
							echo "10**".$rID;
						}
					}
					else if($db_type==2 || $db_type==1 )
					{
						if($rID )
						{
							oci_commit($con);
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							oci_rollback($con);
							echo "10**".$rID;
						}
					}
					disconnect($con);
					die;
				}
			}

		}

		if ($operation==1)  // Update Here
		{

		}

	}//type=0
	
}

if ($action=="appcause_popup")
{
	echo load_html_head_contents("Approval Cause Info", "../../", 1, 1,'','','');
	extract($_REQUEST);

	$data_all=explode('_',$data);

	$wo_id=$data_all[0];
	$app_type=$data_all[1];
	$app_cause=$data_all[2];
	$approval_id=$data_all[3];

	if($app_cause=="")
	{
		$sql_cause="select MAX(id) as id from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=61 and user_id='$user_id' and booking_id='$wo_id' and approval_type=$app_type and status_active=1 and is_deleted=0";
		//echo $sql_cause; //die;
		$nameArray_cause=sql_select($sql_cause);
		if(count($nameArray_cause)>0){
			foreach($nameArray_cause as $row)
			{
				$app_cause1=return_field_value("approval_cause", "fabric_booking_approval_cause", "id='".$row[csf("id")]."' and status_active=1 and is_deleted=0");
				$app_cause = str_replace(array("\r", "\n"), ' ', $app_cause1);
			}
		}
		else
		{
			$app_cause = '';
		}
	}

	?>
    <script>
		$( document ).ready(function() {
			document.getElementById("appv_cause").value='<? echo $app_cause; ?>';
		});

		var permission='<? echo $permission; ?>';
		function fnc_appv_entry(operation)
		{
			var appv_cause = $('#appv_cause').val();

			if (form_validation('appv_cause','Approval Cause')==false)
			{
				if (appv_cause=='')
				{
					alert("Please write cause.");
				}
				return;
			}
			else
			{

				var data="action=save_update_delete_appv_cause&operation="+operation+get_submitted_data_string('appv_cause*wo_id*appv_type*page_id*user_id*approval_id',"../../");
				//alert (data);return;
				freeze_window(operation);
				http.open("POST","service_requisition_approval_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange=fnc_appv_entry_Reply_info;
			}
		}

		function fnc_appv_entry_Reply_info()
		{
			if(http.readyState == 4)
			{
				//release_freezing();
				//alert(http.responseText);return;

				var reponse=trim(http.responseText).split('**');
				show_msg(reponse[0]);

				set_button_status(1, permission, 'fnc_appv_entry',1);
				release_freezing();

				
			}
		}

		function fnc_close()
		{
			appv_cause= $("#appv_cause").val();

			document.getElementById('hidden_appv_cause').value=appv_cause;

			parent.emailwindow.hide();
		}

		
    </script>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                    	<textarea name="appv_cause" id="appv_cause" class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"></textarea>
                        <input type="hidden" name="wo_id" class="text_boxes" ID="wo_id" value="<? echo $wo_id; ?>" style="width:30px" />
                        <input type="hidden" name="appv_type" class="text_boxes" ID="appv_type" value="<? echo $app_type; ?>" style="width:30px" />
                        <input type="hidden" name="page_id" class="text_boxes" ID="page_id" value="<? echo $menu_id; ?>" style="width:30px" />
                        <input type="hidden" name="user_id" class="text_boxes" ID="user_id" value="<? echo $user_id; ?>" style="width:30px" />
                        <input type="hidden" name="approval_id" class="text_boxes" ID="approval_id" value="<? echo $approval_id; ?>" style="width:30px" />
                    </td>
                </tr>
            </table>

            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >

                <tr>
                    <td align="center" class="button_container">
                        <?
						//print_r ($id_up_all);
                            if($id_up!='')
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 1,0,"reset_form('size_1','','','','','');",1);
                            }
                            else
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 0,0,"reset_form('size_1','','','','','');",1);
                            }
                        ?>
                        <input type="hidden" name="hidden_appv_cause" id="hidden_appv_cause" class="text_boxes /">

                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>
                </tr>
            </table>
            </fieldset>
            </form>
        </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}




?>