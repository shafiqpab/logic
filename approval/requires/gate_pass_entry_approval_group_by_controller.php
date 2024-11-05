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

if($db_type==0)
{
	$select_year="year";
	$year_format="";
	$group_concat="group_concat";
}
else if ($db_type==2)
{
	$select_year="to_char";
	$year_format=",'YYYY'";
	$group_concat="wm_concat";
}

function getSequence($parameterArr=array()){
	$lib_department_id_string=implode(',',(array_keys($parameterArr['lib_department_id_arr']))); 
	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT,GROUP_NO FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND entry_form = {$parameterArr['entry_form']} AND IS_DELETED = 0 order by SEQUENCE_NO";
	 //echo $sql;die;
	$sql_result=sql_select($sql);
	$dataArr=array();
	foreach($sql_result as $rows){

        $rows['DEPARTMENT']=($rows['DEPARTMENT']!='')?$rows['DEPARTMENT']:$lib_department_id_string;

		$dataArr['user_by'][$rows['USER_ID']]=$rows;
		$dataArr['sequ_by'][$rows['SEQUENCE_NO']]=$rows;
		$dataArr['group_user_by'][$rows['GROUP_NO']][$rows['USER_ID']]=$rows;
		$dataArr['group_arr'][$rows['GROUP_NO']]=$rows['GROUP_NO'];
		$dataArr['group_seq_arr'][$rows['GROUP_NO']][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
		$dataArr['group_by_seq_arr'][$rows['SEQUENCE_NO']]=$rows['GROUP_NO'];
		$dataArr['bypass_seq_arr'][$rows['BYPASS']][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
		$dataArr['group_bypass_arr'][$rows['GROUP_NO']][$rows['BYPASS']]=$rows['BYPASS'];
	}
	return $dataArr;
}

function getFinalUser($parameterArr=array()){
	$lib_department_id_string=implode(',',(array_keys($parameterArr['lib_department_id_arr'])));
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT,GROUP_NO FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['entry_form']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	   // echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){
        $rows['DEPARTMENT']=($rows['DEPARTMENT']!='')?$rows['DEPARTMENT']:$lib_department_id_string; 
		$usersDataArr[$rows['USER_ID']]['DEPARTMENT']=explode(',',$rows['DEPARTMENT']);

		$userSeqDataArr[$rows['USER_ID']]=$rows['SEQUENCE_NO'];
		$userGroupDataArr[$rows['USER_ID']]=$rows['GROUP_NO'];
		$groupBypassNoDataArr[$rows['GROUP_NO']][$rows['BYPASS']][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
	}

	//print_r($parameterArr['match_data']);die;
 
	$finalSeq=array();
	foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
		foreach($userSeqDataArr as $user_id=>$seq){
			if( in_array($bbtsRows['department'],$usersDataArr[$user_id]['DEPARTMENT']) || $bbtsRows['department']==0 ){
				$finalSeq[$sys_id][$user_id]=$seq;
			}
		}
	}

	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr,'user_group'=>$userGroupDataArr,'group_bypass_no_data_arr'=>$groupBypassNoDataArr);
}


$department_arr = return_library_array( "select ID, DEPARTMENT_NAME from LIB_DEPARTMENT where STATUS_ACTIVE=1 and IS_DELETED=0", "ID", "DEPARTMENT_NAME"  );

$user_arr = return_library_array("select id, user_name from user_passwd", "id", "user_name");




if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$sequence_no='';
	$cbo_company_name=str_replace("'","",$cbo_company_name);
    $cbo_basis=str_replace("'","",$cbo_basis);
    $txt_system_id=str_replace("'","",$txt_system_id);
    $txt_date_from=str_replace("'","",trim($txt_date_from));
	$txt_date_to=str_replace("'","",trim($txt_date_to));
    $txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
    $approval_type=str_replace("'","",$cbo_approval_type);
    $cbo_department_id=str_replace("'","",$cbo_department_id);
 

    $app_user_id=($txt_alter_user_id)?$txt_alter_user_id:$user_id;


	$gate_pass_print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$cbo_company_name."' and module_id=6 and report_id=38 and is_deleted=0 and status_active=1");	
    $gate_format_ids=explode(",",$gate_pass_print_report_format);
    $print_btn=$gate_format_ids[0];


	
 
  

	if($cbo_department_id != 0){$where_con .= " and a.DEPARTMENT_ID=$cbo_department_id";}
	if($txt_system_id != ""){$where_con .= " and a.SYS_NUMBER like('%$txt_system_id')";}
	if($cbo_basis > 0){$where_con .= " and a.basis=$cbo_basis";}
	if($txt_date_from !="" && $txt_date_to!=""){$where_con .= " and a.out_date between '".$txt_date_from."' and '".$txt_date_to."'";} 
	

    $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$sample_supplier=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");

    
    $electronicDataArr=getSequence(array('company_id'=>$cbo_company_name,'page_id'=>$menu_id,'entry_form'=>59,'user_id'=>$app_user_id, 'lib_department_id_arr'=>$department_arr));

    $my_seq = $electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO'];
    $my_group = $electronicDataArr['user_by'][$app_user_id]['GROUP_NO'];
    $my_group_seq_arr = $electronicDataArr['group_seq_arr'][$my_group];
    $electronicDataArr['group_seq_arr'][0] = [0] + $electronicDataArr['group_seq_arr'][1];

	$my_previous_bypass_no_seq = 0;
    rsort($electronicDataArr['bypass_seq_arr'][2]);
    foreach($electronicDataArr['bypass_seq_arr'][2] as $uid => $seq){
        if($seq<$my_seq){$my_previous_bypass_no_seq = $seq;break;}
    }

	if($my_seq == ''){echo "<u><h2 style='color:red;'>You have no approval permission.</h2></u>";die();}
    if($approval_type==0){

        //Match data..................................
		if($electronicDataArr['user_by'][$app_user_id]['DEPARTMENT']){
			$where_con .= " and a.DEPARTMENT_ID in(".$electronicDataArr['user_by'][$app_user_id]['DEPARTMENT'].",0)";
			$electronicDataArr['sequ_by'][0]['DEPARTMENT']=$electronicDataArr['user_by'][$app_user_id]['DEPARTMENT'];
		}

			
        $data_mas_sql="SELECT a.ID, a.DEPARTMENT_ID,a.APPROVED_SEQU_BY,a.APPROVED_GROUP_BY from INV_GATE_PASS_MST a where  a.COMPANY_ID=$cbo_company_name and a.is_deleted=0 and a.status_active=1  and a.ready_to_approved=1 and a.approved<>1 $where_con";   
		 //echo $data_mas_sql; die; 


		
		$tmp_sys_id_arr=array();
		$data_mas_sql_res=sql_select( $data_mas_sql );
			
		foreach ($data_mas_sql_res as $row)
		{ 
			
			$group_stage_arr = array();
			for($group = ($row['APPROVED_GROUP_BY'] == $my_group && $electronicDataArr['sequ_by'][$my_seq]['BYPASS'] == 2)?$my_group:$my_group-1;$group >= 0; $group-- ){
				
				krsort($electronicDataArr['group_seq_arr'][$group]);
				foreach($electronicDataArr['group_seq_arr'][$group] as $seq){
					
					if($seq<$my_seq){ 
						if(
							(in_array($row['DEPARTMENT_ID'],explode(',',$electronicDataArr['sequ_by'][$seq]['DEPARTMENT'])) || $row['DEPARTMENT_ID']==0) && ($row['APPROVED_GROUP_BY'] <= $group)
							)
						{ 
							
							if($electronicDataArr['sequ_by'][$seq]['BYPASS']==1){
								$tmp_sys_id_arr[$group][$seq][$row['ID']]=$row['ID'];
							}
							else{
								$tmp_sys_id_arr[$group][$seq][$row['ID']]=$row['ID'];
								if(in_array($my_previous_bypass_no_seq,$electronicDataArr['group_seq_arr'][$my_group]) && $row['APPROVED_SEQU_BY'] < $my_previous_bypass_no_seq){
									unset($tmp_sys_id_arr[$group][$seq][$row['ID']]);
								}
								break 2; break; 
							}


							//This condition user for only 1 parson approve from group if all user can pass yes. but those are mendatory if found can pass no. 

							if( (in_array($row['APPROVED_SEQU_BY'],$electronicDataArr['group_seq_arr'][$my_group]) && ($row['APPROVED_SEQU_BY'] != $my_previous_bypass_no_seq ) && $electronicDataArr['group_bypass_arr'][$my_group][2] !=2 ) || (count($group_stage_arr[$row['ID']]) > 1) || ($my_previous_bypass_no_seq < $my_seq) && ($row['APPROVED_SEQU_BY'] < $my_previous_bypass_no_seq )   ){ 
								unset($tmp_sys_id_arr[$group][$seq][$row['ID']]);
								break; 
							}

							//Do not delete comment code ..................................
							// if( ($group_stage > 2) || $electronicDataArr['sequ_by'][$seq]['BYPASS']==2){
							// 	break 2; break; 
							// }
							//........................................end;
							$group_stage_arr[$row['ID']][$group] = $group;
							
						}

					}
					
				}

			 

			}//group loof;
			foreach($group_stage_arr as $sys_id => $gidArr){ 
				foreach($gidArr as $gid => $gid){	
					if(count($group_stage_arr[$sys_id])>1 && array_key_first($group_stage_arr[$sys_id]) != $gid ){
						unset($tmp_sys_id_arr[$gid]);
					}
				}
			}
			
			
			
		}
		//..........................................Match data;
		
		 // print_r($tmp_sys_id_arr);die;
		
		$sql='';
		for($group=0;$group<=$my_group; $group++ ){
			foreach($electronicDataArr['group_seq_arr'][$group] as $seq){
		
				if(count($tmp_sys_id_arr[$group][$seq])){
					$sys_con = where_con_using_array($tmp_sys_id_arr[$group][$seq],0,'a.ID');
				
					if($sql!=''){$sql .=" UNION ALL ";}

					$sql .= "SELECT A.ID,a.INSERTED_BY,a.WITHIN_GROUP,a.DEPARTMENT_ID,a.SENT_TO, A.SYS_NUMBER_PREFIX_NUM, A.SYS_NUMBER, A.BASIS, A.COMPANY_ID, $SELECT_YEAR(A.INSERT_DATE $YEAR_FORMAT) AS YEAR, A.OUT_DATE, 0 AS APPROVAL_ID, A.APPROVED , A.COM_LOCATION_ID ,A.CHALLAN_NO,A.RETURNABLE
					from inv_gate_pass_mst a
					where a.company_id=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and a.approved<>1 and a.APPROVED_SEQU_BY=$seq and a.APPROVED_GROUP_BY=$group  $sys_con and a.ready_to_approved=1 $basis_cond $system_id_cond $date_cond 
					group by a.id,a.INSERTED_BY,a.WITHIN_GROUP,a.DEPARTMENT_ID,a.SENT_TO,a.sys_number_prefix_num, a.sys_number, a.basis, a.company_id, a.insert_date, a.out_date,  a.approved,a.com_location_id ,a.challan_no,a.returnable";
				}
			}
		}
    }
    else {

		if($electronicDataArr['user_by'][$app_user_id]['DEPARTMENT']){
			$where_con .= " and a.DEPARTMENT_ID in(".$electronicDataArr['user_by'][$app_user_id]['DEPARTMENT'].",0)";
			$electronicDataArr['sequ_by'][0]['DEPARTMENT']=$electronicDataArr['user_by'][$app_user_id]['DEPARTMENT'];
		}
        
		$sql=" SELECT A.ID,a.INSERTED_BY,a.WITHIN_GROUP,a.DEPARTMENT_ID,a.SENT_TO, A.SYS_NUMBER_PREFIX_NUM, A.SYS_NUMBER, A.BASIS, A.COMPANY_ID, $SELECT_YEAR(A.INSERT_DATE $YEAR_FORMAT) AS YEAR,  A.OUT_DATE, A.APPROVED, 0 AS APPROVAL_ID, A.COM_LOCATION_ID,A.CHALLAN_NO,A.RETURNABLE
		from inv_gate_pass_mst a, APPROVAL_MST c 
		where a.id=c.mst_id and c.entry_form=59 and a.company_id=$cbo_company_name and a.is_deleted=0 and a.status_active=1
		and a.ready_to_approved=1 and a.approved in (1,3) and a.APPROVED_GROUP_BY=c.GROUP_NO  and c.GROUP_NO={$electronicDataArr['user_by'][$app_user_id]['GROUP_NO']}  $where_con
		GROUP by a.id,a.INSERTED_BY,a.WITHIN_GROUP, a.DEPARTMENT_ID,a.SENT_TO,a.sys_number_prefix_num, a.sys_number, a.basis, a.company_id, a.insert_date,  a.out_date, a.approved,  a.com_location_id, a.challan_no,a.returnable
		order by a.id";
    }

	 // echo $sql;die;
	
	?>

<script>
        function open_print_btn_popup(data){
            var title = 'Show Print Options';
            var page_link = 'requires/gate_pass_entry_approval_group_by_controller.php?action=print_button_variable&print_data='+data;
            emailwindow=dhtmlmodal.open('ShowPrint', 'iframe', page_link, title, 'width=650px,height=100px,center=1,resize=1,scrolling=0','');
            emailwindow.onclose=function()
            {
                
            }
        }
    </script>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:1270px; margin-top:10px">
        <legend>Gate Pass List View</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" class="rpt_table" align="left" >
                <thead>
                	<th width="30"></th>
                    <th width="30">SL</th>
                    <th width="100">Company</th>
                    <th width="120">Gate Pass Id</th>
                    <th width="120">System Challan No</th>
                    <th width="180">Department</th>
                    <th width="150">Basis</th>
                    <th  width="150">Gate Pass Date</th>
                    <th width="150">Supplier Name</th>
                    <th width="100">Inserted By</th>
					<th >Refusing Cause</th>
                </thead>
            </table>
            <div style="width:1270px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="left">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?						
                            $i=1;
                            $nameArray=sql_select( $sql );
                           
                            foreach ($nameArray as $row)
                            {
								//$approval_id=implode(",",array_unique(explode(",",$row[csf('approval_id')])));
								$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
								
								?>
								<tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" id="tr_<?= $i; ?>" align="center"> 
                                	<td width="30" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<?= $i;?>" />
                                        <input id="gate_id_<?= $i;?>" name="gate_id[]" type="hidden" value="<?= $row['ID']; ?>" />
                                        <input id="gatePass_id_<?= $i;?>" name="gatePass_id[]" type="hidden" value="<?= $row['ID']; ?>" />
                                        <input id="approval_id_<?= $i;?>" name="approval_id[]" type="hidden" value="<?= $approval_id; ?>" />
                                        <input id="<?= strtoupper($row['SYS_NUMBER_PREFIX_NUM']); ?>" name="no_gate_pass[]" type="hidden" value="<?= $i;?>" />
                                    </td>   
									<td width="30" align="center"><?= $i; ?></td>
									<td width="100"><?= $company_arr[$row['COMPANY_ID']]; ?></td>
                                    <td width="120" align="center"> <a href="javascript:open_print_btn_popup('<?= $row['COMPANY_ID']."*".$row['SYS_NUMBER']."*". $row['COM_LOCATION_ID']."*". $row['CHALLAN_NO']."*".$row['BASIS']."*".$row['RETURNABLE'];?>')"><?= $row['SYS_NUMBER']; ?></a></td>

									<td width="120"><?=$row['CHALLAN_NO']; ?></td>

									<td width="180" align="center"><?= $department_arr[$row['DEPARTMENT_ID']]; ?></td>                             
                                    <td width="150" align="center"><?= $get_pass_basis[$row['BASIS']]; ?></td>
                                    <td width="150" align="center"><?= change_date_format($row['OUT_DATE']); ?></td>
                                     <td  width="150"><? if($row[csf('WITHIN_GROUP')]==1){
									?>
										<?= $company_arr[$row['SENT_TO']]; ?>
									<?
									}
									
									else {echo $row['SENT_TO'];}
									?></td>	

                               <td width="100" align="center"><? echo ucfirst($user_arr[$row[csf('inserted_by')]]); ?></td>
							   <?
									$mst_id = $row[csf('id')];
									$refusing_reason_arr = sql_select("SELECT id,refusing_reason from refusing_cause_history where  mst_id='$mst_id' order by id desc ");
									//	 print_r($refusing_reason_arr);
									?>

									<td > <input style="width:100px;" type="text" class="text_boxes" name="txtCause_<? echo $row[csf('id')]; ?>" id="txtCause_<? echo $row[csf('id')]; ?>" placeholder="browse" onClick="openmypage_refusing_cause('requires/gate_pass_entry_approval_group_by_controller.php?action=refusing_cause_popup','Refusing Cause','<? echo $row[csf('id')]; ?>');" value="<? echo $refusing_reason_arr[0][csf('refusing_reason')];?>" /></td>    

								</tr>
								<?
								$i++;
							}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table" align="left">
				<tfoot>
                    <td width="30" align="center" >
						<input type="checkbox" id="all_check" onclick="check_all('all_check')" />
					</td>
                    <td colspan="2" align="left">
						<input type="button" value="<? if($approval_type==1) echo "Un-Approved"; else echo "Approved"; ?>" class="formbutton" style="width:100px" onclick="submit_approved(<?= $i; ?>,<?= $approval_type; ?>)"/>
						<? if($approval_type==0){ ?>
						<input type="button" value="Deny" class="formbutton" style="width:100px;" onClick="submit_approved(<?=$i; ?>,5);"/>
						<? } ?>
				</td>
				</tfoot>
			</table>
        </fieldset>
    </form>         
	<?
	exit();	
}

if ($action == "refusing_cause_popup") {
	extract($_REQUEST);
	echo load_html_head_contents("Refusing Cause Info", "../../", 1, 1, $unicode);
	$permission = "1_1_1_1";

	$sql_cause = "select refusing_reason from refusing_cause_history where entry_form=59 and mst_id='$quo_id'";

	$nameArray_cause = sql_select($sql_cause);
	$app_cause = '';
	foreach ($nameArray_cause as $row) {
		$app_cause .= $row[csf("refusing_reason")] . ",";
	}
	$app_cause = chop($app_cause, ",");
	//print_r($app_cause);
 ?>
	<script>
		var permission = '<?= $permission; ?>';

		function set_values(cause) {
			var refusing_cause = document.getElementById('txt_refusing_cause').value;
			if (refusing_cause == '') {
				document.getElementById('txt_refusing_cause').value = refusing_cause;
				parent.emailwindow.hide();
			} else {
				alert("Please save refusing cause first or empty");
				return;
			}
		}

		function fnc_cause_info(operation) {
			var refusing_cause = $("#txt_refusing_cause").val();
			var quo_id = $("#hidden_quo_id").val();
			if (form_validation('txt_refusing_cause', 'Refusing Cause') == false) {
				return;
			} else {
				var data = "action=save_update_delete_refusing_cause&operation=" + operation + "&refusing_cause=" + refusing_cause + "&quo_id=" + quo_id;
				http.open("POST", "gate_pass_entry_approval_group_by_controller.php", true);
				http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_cause_info_reponse;
			}
		}

		function fnc_cause_info_reponse() {
			if (http.readyState == 4) {
				var response = trim(http.responseText).split('**');
				if (response[0] == 0) {
					alert("Data saved successfully");
					//document.getElementById('txt_refusing_cause').value =response[1];
					parent.emailwindow.hide();
				} else {
					alert("Data not saved");
					return;
				}
			}
		}
	</script>

	<body onload="set_hotkey();">
		<div align="center" style="width:100%;">
			<fieldset style="width:470px;">
				<legend>Refusing Cause</legend>
				<form name="causeinfo_1" id="causeinfo_1" autocomplete="off">
					<table cellpadding="0" cellspacing="2" width="470px">
						<tr>
							<td width="100" class="must_entry_caption">Refusing Cause</td>
							<td>
								<input type="text" name="txt_refusing_cause" id="txt_refusing_cause" class="text_boxes" style="width:320px;height: 100px;" value="<?= $cause; ?>" />
								<input type="hidden" name="hidden_quo_id" id="hidden_quo_id" value="<? echo $quo_id; ?>">
							</td>
						</tr>
						<tr>
							<td colspan="4" align="center" class="button_container">
								<?
								if (!empty($app_cause)) {
									echo load_submit_buttons($permission, "fnc_cause_info", 1, 0, "reset_form('causeinfo_1','','')", 1);
								} else {
									echo load_submit_buttons($permission, "fnc_cause_info", 0, 0, "reset_form('causeinfo_1','','','','','');", 1);
								}
								?> </br>
								<input type="button" class="formbutton" value="Close" name="close_buttons" id="close_buttons" onClick="set_values();" style="width:50px;height: 35px;">
							</td>
						</tr>
						<tr>
							<td colspan="4" align="center">&nbsp;</td>
						</tr>
					</table>
				</form>
			</fieldset>
			<?
			$sqlHis = "select approval_cause from approval_cause_refusing_his where entry_form=59 and approval_type=1 and booking_id='$quo_id' and status_active=1 and is_deleted=0 order by id Desc";
			$sqlHisRes = sql_select($sqlHis);
			?>
			<table align="center" cellspacing="0" width="420" class="rpt_table" border="1" rules="all">
				<thead>
					<th width="30">SL</th>
					<th>Refusing History</th>
				</thead>
			</table>
			<div style="width:420px; overflow-y:scroll; max-height:260px;" align="center">
				<table align="center" cellspacing="0" width="403" class="rpt_table" border="1" rules="all">
					<?
					$i = 1;
					foreach ($sqlHisRes as $hrow) {
						if ($i % 2 == 0) $bgcolor = "#E9F3FF";
						else $bgcolor = "#FFFFFF";
					?>
						<tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>');">
							<td width="30"><?= $i; ?></td>
							<td style="word-break:break-all"><?= $hrow[csf('approval_cause')]; ?></td>
						</tr>
					<?
						$i++;
					}
					?>
				</table>
			</div>
		</div>
		<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</body>
 <?
	exit();
}

if($action=="save_update_delete_refusing_cause")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $_REQUEST ));
	$flag=1;
	if(is_duplicate_field( "approval_cause", "approval_cause_refusing_his", "approval_cause='".$refusing_cause."' and entry_form=59 and booking_id='".str_replace("'", "", $quo_id)."' and approval_type=1 and status_active=1 and is_deleted=0" )==1)
	{
		//
	}
	else
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//$id_his=return_next_id( "id", "approval_cause_refusing_his", 1);
		$idpre=return_field_value("max(id) as id", "refusing_cause_history", "mst_id=".$quo_id." and entry_form=59 group by mst_id","id");
		$sqlHis="insert into approval_cause_refusing_his( id, cause_id, entry_form, booking_id, approval_type, approval_cause, inserted_by, insert_date, updated_by, update_date)
				select '', id, entry_form, mst_id, 1, refusing_reason, inserted_by, insert_date, updated_by, update_date from refusing_cause_history where mst_id=".$quo_id." and entry_form=59 and id=$idpre"; //die;
		
		if(count($sqlHis)>0)
		{
			$rID3=execute_query($sqlHis,0);
			if($flag==1)
			{
				if($rID3==1) $flag=1; else $flag=0;
			}
		}
	}
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}		
		// $get_history = sql_select("SELECT id from approval_history where mst_id='$quo_id' and entry_form =15 and current_approval_status=1");
		$id=return_next_id( "id", "refusing_cause_history", 1);
		$field_array = "id,entry_form,mst_id,refusing_reason,inserted_by,insert_date";
		$data_array = "(".$id.",59,".$quo_id.",'".$refusing_cause."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		$rID=sql_insert("refusing_cause_history",$field_array,$data_array,1);
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");
				echo "0**$refusing_cause";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
				echo "0**$refusing_cause";
			}
			else{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}		
		//$id=return_next_id( "id", "refusing_cause_history", 1);
		
		$idpre=return_field_value("max(id) as id", "refusing_cause_history", "mst_id=".$quo_id." and entry_form=59 group by mst_id","id");
		$field_array="refusing_reason*updated_by*update_date";
		$data_array="'".$refusing_cause."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=sql_update("refusing_cause_history",$field_array,$data_array,"id",$idpre,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		//echo "10**".$rID.'='.$rID3.'='.$flag; die;
		
		if($db_type==0)
		{
			if( $flag==1)
			{
				mysql_query("COMMIT");
				echo "0**$refusing_cause";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**$refusing_cause";
			}
			else{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
}


if ($action=="approve")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$con = connect();

	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
    $approval_type=str_replace("'","",$approval_type);
    $cbo_company_name=str_replace("'","",$cbo_company_name);
    $gatePass_ids=str_replace("'","",$gatePass_ids);
    $app_user_id=($txt_alter_user_id)?$txt_alter_user_id:$user_id;
	$gatePass_ids_all = explode(",",$gatePass_ids);
	$causes = str_replace("'","",$causes);
	$cause_arr = explode(",",$causes);

	$sql="SELECT a.ID, a.DEPARTMENT_ID,a.READY_TO_APPROVED from inv_gate_pass_mst a  where a.COMPANY_ID=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and a.READY_TO_APPROVED=1  and a.id in($gatePass_ids)";
	//echo $sql;die; 

    $sqlResult=sql_select( $sql );
    foreach ($sqlResult as $row)
    {
        if($row['READY_TO_APPROVED'] != 1 ){echo "21**Ready to approved NO is not allow";die;}
		$matchDataArr[$row['ID']]=array('buyer'=>0,'brand'=>0,'item'=>0,'store'=>0,'department'=>$row['DEPARTMENT_ID']);
    } 
   
    $finalDataArr=getFinalUser(array('company_id'=>$cbo_company_name,'page_id'=>$menu_id,'entry_form'=>59,'lib_buyer_arr'=>0,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0,'lib_department_id_arr'=>$department_arr,'match_data'=>$matchDataArr));
     //print_r($finalDataArr);die;
		
 
	$sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
	$user_sequence_no = $finalDataArr['user_seq'][$app_user_id];
	$user_group_no = $finalDataArr['user_group'][$app_user_id];
	$max_group_no = max($finalDataArr['user_group']);

	 //echo $user_group_no;die;

	$max_approved_no_arr = return_library_array("SELECT mst_id, max(approved_no) as approved_no from approval_history where mst_id in($gatePass_ids) and entry_form=59 group by mst_id","mst_id","approved_no");


	$msg=''; $flag=''; $response='';	
	if($approval_type==0)
	{
		$id = return_next_id( "id","approval_history", 1 ) ;
		$appid = return_next_id( "id","approval_mst", 1 ) ;

		
		
		// ======================================================================== New
		foreach($gatePass_ids_all as $key => $gatePass_id)
		{
		
			$approved = ((max($finalDataArr['final_seq'][$gatePass_id])==$user_sequence_no) || ( (max($finalDataArr['group_bypass_no_data_arr'][$max_group_no][2])==$user_sequence_no) && ($max_group_no == $user_group_no))  || ( (max($finalDataArr['group_bypass_no_data_arr'][$max_group_no][2]) == '') && ($max_group_no == $user_group_no) ) )?1:3;
			
			$approved_no = $max_approved_no_arr[$gatePass_id]*1;
			if($last_app_status_arr[$gatePass_id] == 0 || $last_app_status_arr[$gatePass_id] == 2){
				$approved_no = $approved_no+1;
			}

			

 			//History data.......................
			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",59,".$gatePass_id.",".$approved_no.",'".$user_sequence_no."',1,".$app_user_id.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."',".$approved.",'".$cause_arr[$key]."')"; 
			$id=$id+1;

			//App mst data.......................
			if($app_data_array!=''){$app_data_array.=",";}
			$app_data_array.="(".$appid.",59,".$gatePass_id.",".$user_sequence_no.",".$app_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."',".$approved.",".$user_group_no.")"; 
			$appid++;

			//Update mst data...........................
			$mst_data_array_up[$gatePass_id] = explode(",",("".$approved.",".$user_sequence_no.",".$app_user_id.",'".$pc_date_time."',".$user_group_no."")); 

		}
		 //echo $gatePass_ids;die;
		$flag=1;

		if($flag==1) 
		{
			$mst_field_array_up="APPROVED*APPROVED_SEQU_BY*APPROVED_BY*APPROVED_DATE*APPROVED_GROUP_BY"; 
            $rID=execute_query(bulk_update_sql_statement( "inv_gate_pass_mst", "id", $mst_field_array_up, $mst_data_array_up, $gatePass_ids_all ));
            if($rID) $flag=1; else $flag=0; 
		}

 	 
		if($flag==1) 
		{
			$query="update approval_history set current_approval_status=0  WHERE entry_form=59 and mst_id in ($gatePass_ids)";
			$rIDapp=execute_query($query,1);
			if($rIDapp) $flag=1; else $flag=0; 
		} 

		if($flag==1) 
		{	
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,APPROVED,COMMENTS"; 
			$rID2=sql_insert("approval_history",$field_array,$data_array,1);
			if($rID2) $flag=1; else $flag=0; 
		}
	

		if($flag==1) 
		{
			$field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip,APPROVED,GROUP_NO";
			$rID3=sql_insert("approval_mst",$field_array,$app_data_array,0);
			if($rID3) $flag=1; else $flag=0; 
		}


		// echo '21**'.$rID.'**'.$rIDapp.'**'.$rID2.'**'.$app_data_array;oci_rollback($con); die;


		if($flag==1) $msg='19'; else $msg='21';

	}
	else if($approval_type==5){
		
		//$gatePass_ids_all=explode(",",$gatePass_ids);
		$id=return_next_id( "id","approval_history", 1 ) ;
		$data_array = '';
		foreach($gatePass_ids_all as $key => $gatePass_id)
		{
			$approved_no = $max_approved_no_arr[$gatePass_id];
			if($last_app_status_arr[$gatePass_id] == 0 || $last_app_status_arr[$gatePass_id] == 2){
				$approved_no=$max_approved_no_arr[$gatePass_id]+1;
			}

 			//History data.......................
			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",59,".$gatePass_id.",".$approved_no.",'".$user_sequence_no."',0,".$app_user_id.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."',2,'".$cause_arr[$key]."')"; 
			$id=$id+1;
		}
		
		
		$rID=sql_multirow_update("inv_gate_pass_mst","APPROVED*READY_TO_APPROVED*APPROVED_SEQU_BY*APPROVED_GROUP_BY","2*0*0*0","id",$gatePass_ids,1);
		if($rID) $flag=1; else $flag=0;

		if($flag==1) 
		{	
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,APPROVED,COMMENTS"; 
			$rID2=sql_insert("approval_history",$field_array,$data_array,1);
			if($rID2) $flag=1; else $flag=0; 
		}

		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=59 and mst_id in ($gatePass_ids)";
			$rID3=execute_query($query,1); 
			if($rID3) $flag=1; else $flag=0; 
		}


		if($flag==1) $msg='37'; else $msg='5';
	}
	else
	{

		//$gatePass_ids_all=explode(",",$gatePass_ids);
		$id=return_next_id( "id","approval_history", 1 ) ;
		$data_array = '';
		foreach($gatePass_ids_all as $key => $gatePass_id)
		{
			$approved_no = $max_approved_no_arr[$gatePass_id];
			if($last_app_status_arr[$gatePass_id] == 0 || $last_app_status_arr[$gatePass_id] == 2){
				$approved_no=$max_approved_no_arr[$gatePass_id]+1;
			}

 			//History data.......................
			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",59,".$gatePass_id.",".$approved_no.",'".$user_sequence_no."',0,".$app_user_id.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."',0,'".$cause_arr[$key]."')"; 
			$id=$id+1;
		}
		
		$rID=sql_multirow_update("inv_gate_pass_mst","APPROVED*READY_TO_APPROVED*APPROVED_SEQU_BY*APPROVED_GROUP_BY","0*0*0*0","id",$gatePass_ids,1);
		if($rID) $flag=1; else $flag=0;

		if($flag==1) 
		{	
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,APPROVED,COMMENTS"; 
			$rID2=sql_insert("approval_history",$field_array,$data_array,1);
			if($rID2) $flag=1; else $flag=0; 
		}

		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=59 and mst_id in ($gatePass_ids)";
			$rID3=execute_query($query,1); 
			if($rID3) $flag=1; else $flag=0; 
		}

		//echo $flag;oci_rollback($con); ;die;	
	 
		if($flag==1) $msg='20'; else $msg='22';
	}
	

	
	if($flag==1)
	{
		oci_commit($con);  
		echo $msg."**".$gatePass_ids;
	}
	else
	{
		oci_rollback($con); 
		echo $msg."**".$gatePass_ids;
	}
	
	disconnect($con);
	die;
}


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
			$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation,b.SEQUENCE_NO,b.GROUP_NO from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name  and valid=1 and a.id!=$user_id  and a.is_deleted=0 and b.is_deleted=0 and b.entry_form=59 order by b.sequence_no";
		 	 //echo $sql;die;
		 	$arr=array (2=>$custom_designation,3=>$Department);
		 	echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department,Seq. no,Group no", "100,120,150,180,50,50","730","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id,sequence_no,group_no", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,7,7' ) ;
		?>
	</form>
	<script language="javascript" type="text/javascript">
	  setFilterGrid("tbl_style_ref");
	</script>
	<?
}

if($action=="print_button_variable")
{ 
    echo load_html_head_contents("Print Button Options", "../../", 1, 1,'','','');
    extract($_REQUEST);
    list($company_id,$sys_number,$location_id,$emb_issue_ids,$basis,$returnable) = explode('*', $print_data);
	//3*OG-GPE-23-00177*1*567890*1*2 

       // print_r($print_data);
    ?>

    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
        function fnc_pi_approval_mst( operation )

        {
            var pi_approval_mst_values = $("#pi_approval_mst_values").val();
            var approval_mst_value = pi_approval_mst_values.split("*");
            var company_id =  approval_mst_value[0];
            var sys_number = approval_mst_value[1];
            var location_id = approval_mst_value[2];
			//alert(location_id);
            var emb_issue_ids = approval_mst_value[3];
            var basis = approval_mst_value[4];
            var returnable = approval_mst_value[5];
            // var cbo_pi_basis_id = 1;
            // var is_approved = '';
			var report_title="Gate Pass Entry";

            // if(sys_id=="")
            // {
            //     alert("Something went wrong");
            //     return;
            // }
            // print 5
            if(operation==1)
            {
				var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}

					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+report_title+'*'+show_item+'*'+basis+'*'+location_id+'&action=get_out_entry_print12&template_id=1', true );	
              
            }
            // print
             if(operation==2){
				var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+report_title+'*'+show_item+'*'+basis+'*'+location_id+'&action=get_out_entry_print&template_id=1', true );
            }
            //print-4
            if(operation==3)
            {
                var show_item=0;	
                   window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+show_item+'&action=print_to_html_report5&template_id=1', true );	
            }
            //print 2
            if(operation==4){
				var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else  
					{
						show_item="0";
					}
					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+show_item+'&action=print_to_html_report&template_id=1', true );
            }
            // Print 6

			if(operation==5){
				var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&action=get_out_entry_print6&template_id=1', true );
            }
			 // Print 7

			if(operation==6){
				var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+show_item+'&action=print_to_html_report_13&template_id=1', true );	
            }

            // Print 9
			if(operation==9){
				var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&action=get_out_entry_print9&template_id=1', true );	
					return;
				}

				 // Print 10
			if(operation==10){
				var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+report_title+'*'+show_item+'*'+basis+'*'+location_id+'*'+1+'&action=get_out_entry_print10&template_id=1', true );	
				}

			// Print 11
			if(operation==11){
				var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&action=get_pass_entry_print11&template_id=1', true );
					return;
				}
                 // Print 12
				if(operation==12){
					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&action=get_out_entry_print20&template_id=1', true );
					return;
				}

				 // Print 13
				 if(operation==13){
					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&action=get_out_entry_print21&template_id=1', true );
					return;
				}

				// Print 27
				if(operation==27){
					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&action=get_out_entry_print22&template_id=1', true );
					return;
				}

				// Print 14
				if(operation==14){
					var show_item='';
				var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
				if (r==true)
				{
					show_item="1";
				}
				else
				{
					show_item="0";
				}
					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&action=print_to_html_report_scandex&template_id=1', true );
					return;
				}

				// Print 15
				if(operation==15){
					var show_item='';
			     var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
			      if (r==true)
			     {
				show_item="1";
			     }
			   else
			    {
				show_item="0";
			    }
					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&action=get_out_entry_print28&template_id=1', true );
					return;
				}

				// Print 16
				if(operation==16){
					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}		
					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&action=get_out_entry_print16&template_id=1', true );
					return;
				}
        } 
    </script>

    <?php
    $buttonHtml='';

	// $gate_pass_print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$cbo_company_name."' and module_id=6 and report_id=38 and is_deleted=0 and status_active=1");	
    // $gate_format_ids=explode(",",$gate_pass_print_report_format);
    // $print_btn=$gate_format_ids[0];


    $gate_pass_print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company_id."' and module_id=6 and report_id=38 and is_deleted=0 and status_active=1");
   	$printButton=explode(',',$gate_pass_print_report_format);

	//print_r($printButton);
    $buttonHtml.='<div align="center">';
        foreach($printButton as $id){
            if($id==129)$buttonHtml.='
            <input type="hidden" name="printBtn4" id="pi_approval_mst_values" value="'.$print_data.'"/>
            <input type="button" name="printBtn4" id="printBtn4" value="Print 5" onClick="fnc_pi_approval_mst(1)" style="width:100px" class="formbutton"/>';

            if($id==115)$buttonHtml.='
			<input type="hidden" name="printBtn" id="pi_approval_mst_values" value="'.$print_data.'"/>
			<input type="button" name="printBtn" id="printBtn" value="Print " onClick="fnc_pi_approval_mst(2)" style="width:100px" class="formbutton">';

			if($id==137)$buttonHtml.='
			<input type="hidden" name="printBtn2" id="pi_approval_mst_values" value="'.$print_data.'"/>
			<input type="button" name="printBtn2" id="printBtn2" value="Print 4 " onClick="fnc_pi_approval_mst(3)" style="width:100px" class="formbutton">';

			if($id==116)$buttonHtml.='
			<input type="hidden" name="printBtn3" id="pi_approval_mst_values" value="'.$print_data.'"/>
			<input type="button" name="printBtn3" id="printBtn3" value="Print 2 " onClick="fnc_pi_approval_mst(4)" style="width:100px" class="formbutton">';

			if($id==161)$buttonHtml.='
			<input type="hidden" name="printBtn6" id="pi_approval_mst_values" value="'.$print_data.'"/>
			<input type="button" name="printBtn6" id="printBtn6" value="Print 6 " onClick="fnc_pi_approval_mst(5)" style="width:100px" class="formbutton">';

			if($id==191)$buttonHtml.='
			<input type="hidden" name="printBtn7" id="pi_approval_mst_values" value="'.$print_data.'"/>
			<input type="button" name="printBtn7" id="printBtn7" value="Print 7 " onClick="fnc_pi_approval_mst(6)" style="width:100px" class="formbutton">';
           
            if($id==235)$buttonHtml.='
			<input type="hidden" name="printBtn9" id="pi_approval_mst_values" value="'.$print_data.'"/>
			<input type="button" name="printBtn9" id="printBtn9" value="Print 9 " onClick="fnc_pi_approval_mst(9)" style="width:100px" class="formbutton">';

			if($id==274)$buttonHtml.='
			<input type="hidden" name="printBtn10" id="pi_approval_mst_values" value="'.$print_data.'"/>
			<input type="button" name="printBtn10" id="printBtn10" value="Print 10 " onClick="fnc_pi_approval_mst(10)" style="width:100px" class="formbutton">';

			if($id==241)$buttonHtml.='
			<input type="hidden" name="printBtn11" id="pi_approval_mst_values" value="'.$print_data.'"/>
			<input type="button" name="printBtn11" id="printBtn11" value="Print 11 " onClick="fnc_pi_approval_mst(11)" style="width:100px" class="formbutton">';

			if($id==427)$buttonHtml.='
			<input type="hidden" name="printBtn12" id="pi_approval_mst_values" value="'.$print_data.'"/>
			<input type="button" name="printBtn12" id="printBtn12" value="Print 12 " onClick="fnc_pi_approval_mst(12)" style="width:100px" class="formbutton">';

			if($id==28)$buttonHtml.='
			<input type="hidden" name="printBtn13" id="pi_approval_mst_values" value="'.$print_data.'"/>
			<input type="button" name="printBtn13" id="printBtn13" value="Print 13 " onClick="fnc_pi_approval_mst(13)" style="width:100px" class="formbutton">';

			if($id==437)$buttonHtml.='
			<input type="hidden" name="printBtn27" id="pi_approval_mst_values" value="'.$print_data.'"/>
			<input type="button" name="printBtn27" id="printBtn27" value="Print 27 " onClick="fnc_pi_approval_mst(27)" style="width:100px" class="formbutton">';

			if($id==280)$buttonHtml.='
			<input type="hidden" name="printBtn14" id="pi_approval_mst_values" value="'.$print_data.'"/>
			<input type="button" name="printBtn14" id="printBtn14" value="Print B14 " onClick="fnc_pi_approval_mst(14)" style="width:100px" class="formbutton">';

			if($id==304)$buttonHtml.='
			<input type="hidden" name="printBtn15" id="pi_approval_mst_values" value="'.$print_data.'"/>
			<input type="button" name="printBtn15" id="printBtn15" value="Print B15 " onClick="fnc_pi_approval_mst(15)" style="width:100px" class="formbutton">';

			if($id==719)$buttonHtml.='
			<input type="hidden" name="printBtn16" id="pi_approval_mst_values" value="'.$print_data.'"/>
			<input type="button" name="printBtn16" id="printBtn16" value="Print 16 " onClick="fnc_pi_approval_mst(16)" style="width:100px" class="formbutton">';
        }
    $buttonHtml.='</div>';
    echo $buttonHtml;
    exit();
} 

?>