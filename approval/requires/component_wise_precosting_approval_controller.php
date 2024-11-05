<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
extract($_REQUEST);
$permission = $_SESSION['page_permission'];

include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']['user_id'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];


$permissionSql = "SELECT approve_priv FROM user_priv_mst where user_id = $user_id AND main_menu_id = $menu_id";
$permissionCheck = sql_select($permissionSql);
$approvePermission = $permissionCheck[0][csf('approve_priv')];

 

if ($action == "load_drop_down_buyer") {
	echo create_drop_down("cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "");
	exit();
}


if ($action == 'user_popup') {
	echo load_html_head_contents("Popup Info", "../../", 1, 1, '', 1, '');
	?>

	<script>
		function js_set_value(id) {
			document.getElementById('selected_id').value = id;
			parent.emailwindow.hide();
		}
	</script>

	<form>
		<input type="hidden" id="selected_id" name="selected_id" />
		<?php
		$custom_designation = return_library_array("select id,custom_designation from lib_designation ", 'id', 'custom_designation');
		$Department = return_library_array("select id,department_name from  lib_department ", 'id', 'department_name');
		$sql = "select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.entry_form=11 and valid=1 and a.id!=$user_id  and b.is_deleted=0 order by sequence_no";
		$arr = array(2 => $custom_designation, 3 => $Department);
		echo create_list_view("list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,", "630", "220", 0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr, "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);');
		?>
	</form>
	<script language="javascript" type="text/javascript">
		setFilterGrid("tbl_style_ref");
	</script>
	<?
	exit();
}



function getSequence($parameterArr = array())
{
	$lib_buyer_arr = implode(',', (array_keys($parameterArr['lib_buyer_arr'])));
	//$lib_brand_arr=implode(',',(array_keys($parameterArr['lib_brand_arr'])));
	$buyer_brand_arr = $parameterArr['lib_brand_arr'];


	//Electronic app setup data.....................
	$sql = "SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND PAGE_ID = {$parameterArr['page_id']} AND IS_DELETED = 0 order by SEQUENCE_NO";
	// echo $sql;die;
	$sql_result = sql_select($sql);
	$dataArr = array(); 
	foreach ($sql_result as $rows) {


		if ($rows['BUYER_ID'] == '') {
			$rows['BUYER_ID'] = $lib_buyer_arr;
		}
		$temp_brand_arr = array(0 => 0);
		foreach (explode(',', $rows['BUYER_ID']) as $buyer_id) {
			if (count($parameterArr['lib_brand_arr'][$buyer_id])) {
				$temp_brand_arr[] = implode(',', (array_keys($parameterArr['lib_brand_arr'][$buyer_id])));
			}
		}
		if ($rows['BRAND_ID'] == '') {
			$rows['BRAND_ID'] = implode(',', explode(',', implode(',', $temp_brand_arr)));
		}


		$dataArr['sequ_by'][$rows['SEQUENCE_NO']] = $rows;
		$dataArr['user_by'][$rows['USER_ID']] = $rows;
		$dataArr['sequ_arr'][$rows['SEQUENCE_NO']] = $rows['SEQUENCE_NO'];
	}

	return $dataArr;
}

function getFinalUser($parameterArr = array())
{
	$lib_buyer_arr = implode(',', (array_keys($parameterArr['lib_buyer_arr'])));

	//Electronic app setup data.....................
	$sql = "SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND PAGE_ID = {$parameterArr['page_id']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	//echo $sql;die;
	$sql_result = sql_select($sql);
	foreach ($sql_result as $rows) {

		if ($rows['BUYER_ID'] == '') {
			$rows['BUYER_ID'] = $lib_buyer_arr;
		}
	
		$temp_brand_arr = array(0 => 0);
		foreach (explode(',', $rows['BUYER_ID']) as $buyer_id) {
			if (count($parameterArr['lib_brand_arr'][$buyer_id])) {
				$temp_brand_arr[] = implode(',', (array_keys($parameterArr['lib_brand_arr'][$buyer_id])));
			}
		}
		if ($rows['BRAND_ID'] == '') {
			$rows['BRAND_ID'] = implode(',', explode(',', implode(',', $temp_brand_arr)));
		}


		$usersDataArr[$rows['USER_ID']]['BUYER_ID'] = explode(',', $rows['BUYER_ID']);
		$usersDataArr[$rows['USER_ID']]['BRAND_ID'] = explode(',', $rows['BRAND_ID']);
		$userSeqDataArr[$rows['USER_ID']] = $rows['SEQUENCE_NO'];
	}



	$finalSeq = array();
	foreach ($parameterArr['match_data'] as $sys_id => $bbtsRows) {

		foreach ($userSeqDataArr as $user_id => $seq) {
			if (
				in_array($bbtsRows['buyer_id'], $usersDataArr[$user_id]['BUYER_ID'])
				//&& in_array($bbtsRows['brand_id'],$usersDataArr[$user_id]['BRAND_ID'])
				&&  $bbtsRows['buyer_id'] > 0
			) {
				$finalSeq[$sys_id][$user_id] = $seq;
			}
		}
	}



	return array('final_seq' => $finalSeq, 'user_seq' => $userSeqDataArr);
}



if ($action == "report_generate") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$company_name = str_replace("'", "", $cbo_company_name);
	$txt_job_no = str_replace("'", "", $txt_job_no);
	$approval_type = str_replace("'", "", $cbo_approval_type);
	$cbo_buyer_name = str_replace("'", "", $cbo_buyer_name);
	
	$txt_alter_user_id = str_replace("'", "", $txt_alter_user_id);
	$approved_by = ($txt_alter_user_id) ? $txt_alter_user_id : $user_id;

	$company_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$user_arr = return_library_array("SELECT ID,USER_NAME  FROM USER_PASSWD WHERE VALID=1", "ID", "USER_NAME");


	$txt_date = str_replace("'", "", $txt_date);
	$date_cond = '';
	if ($txt_date != "") {
		if ($db_type == 0)  $txt_date = change_date_format($txt_date, "yyyy-mm-dd");
		else   			 $txt_date = change_date_format($txt_date, "yyyy-mm-dd", "-", 1);

		if (str_replace("'", "", $cbo_get_upto) == 1) $date_cond = " and a.costing_date>'" . $txt_date . "'";
		else if (str_replace("'", "", $cbo_get_upto) == 2) $date_cond = " and a.costing_date<='" . $txt_date . "'";
		else if (str_replace("'", "", $cbo_get_upto) == 3) $date_cond = " and a.costing_date='" . $txt_date . "'";
		else $date_cond = '';
	}

	if($txt_job_no){$where_con=" and a.JOB_NO like('%".$txt_job_no."')";}
	if($cbo_buyer_name){$where_con .=" and b.BUYER_NAME=$cbo_buyer_name";}

	$electronicSetupSql = "select DEPARTMENT as COMPONENT_ID,BUYER_ID,USER_ID,BYPASS,SEQUENCE_NO,APPROVED_BY from electronic_approval_setup where company_id = $cbo_company_name and entry_form=11 and is_deleted=0";
	//echo $electronicSetupSql;die;
	$electronicSetupSqlResult = sql_select($electronicSetupSql);
	$electronicDataArr = array();
	foreach ($electronicSetupSqlResult as $row) {
		$electronicDataArr['USER_SEQ'][$row['USER_ID']] = $row['SEQUENCE_NO'];
		$electronicDataArr['SEQ_DATA'][$row['SEQUENCE_NO']] = $row;
	}
		 // print_r($electronicDataArr['SEQ_DATA'][4]['BUYER_ID']);die;
		//echo $electronicDataArr['SEQ_DATA'][$electronicDataArr['USER_SEQ'][1]]['COMPONENT_ID'];die;
	if ($approval_type == 2) {
		$find_my_buyer_data_arr = array();
		$my_buyer_id_arr = explode(',', $electronicDataArr['SEQ_DATA'][$electronicDataArr['USER_SEQ'][$approved_by]]['BUYER_ID']);
		$myLastSelectBuyer = end($my_buyer_id_arr);

		$my_component_id_arr = explode(',', $electronicDataArr['SEQ_DATA'][$electronicDataArr['USER_SEQ'][$approved_by]]['COMPONENT_ID']);
		//print_r($my_component_id_arr);die;
		$myLastSelectComponent = end($my_component_id_arr);

		//echo implode(',',$my_buyer_id_arr);die;
		for ($seq = $electronicDataArr['USER_SEQ'][$approved_by] - 1; $seq >= 1; $seq--) {
			$dataArr = $electronicDataArr['SEQ_DATA'][$seq];
			$buyer_id_arr = explode(',', $dataArr['BUYER_ID']);
			$component_id_arr = explode(',', $dataArr['COMPONENT_ID']);

			$myRestCompoArr = array_diff($my_component_id_arr,$component_id_arr);
			$myRestBuyerArr = array_diff($my_buyer_id_arr,$buyer_id_arr);
		 
			
			foreach ($my_buyer_id_arr as $key => $my_buyer_id) {
				foreach ($my_component_id_arr as $key2=>$my_component_id) {
					if (in_array($my_buyer_id, $buyer_id_arr) && in_array($my_component_id, $component_id_arr) ) {
						$find_my_buyer_data_arr[$seq][$my_buyer_id] = $my_buyer_id;
						$find_my_component_data_arr[$seq][$my_component_id] = $my_component_id;
						if($electronicDataArr['SEQ_DATA'][$seq]['BYPASS']==2){ 
							if($myLastSelectComponent == $my_component_id && count($myRestCompoArr)<1){
								unset($my_buyer_id_arr[$key]);
							}
							if($myLastSelectBuyer == $my_buyer_id && count($myRestBuyerArr)<1){
								unset($my_component_id_arr[$key2]);
							} 
						}
					}
				}
			}
		}

		if (count($my_buyer_id_arr)) {
			$find_my_buyer_data_arr[0] = $my_buyer_id_arr;
		}
		if (count($my_component_id_arr)) {
			$find_my_component_data_arr[0] = $my_component_id_arr;
		}

		 
		$my_buyer_id_str = $electronicDataArr['SEQ_DATA'][$electronicDataArr['USER_SEQ'][$approved_by]]['BUYER_ID'];
		$precostJobSql = "select a.ID,a.JOB_ID,a.JOB_NO,b.BUYER_NAME,B.STYLE_REF_NO,a.INSERT_DATE,a.UPDATED_BY,a.UPDATE_DATE,A.COSTING_DATE,to_char(a.INSERT_DATE,'YYYY') as YEAR,a.INSERTED_BY from WO_PRE_COST_MST a,WO_PO_DETAILS_MASTER b where b.id=a.job_id and a.READY_TO_APPROVED=1 and a.APPROVED<>1 and b.COMPANY_NAME=$cbo_company_name and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.BUYER_NAME in($my_buyer_id_str) $where_con $date_cond ";
		  // echo $precostJobSql;die;
		$precostJobSqlResult = sql_select($precostJobSql);
		$job_id_arr = array();$job_data_arr=array();$precost_id_arr = array();
		foreach ($precostJobSqlResult as $row) {
			$mySeq = $electronicDataArr['USER_SEQ'][$approved_by] - 1;

			for ($seq = $mySeq; $seq >= 0; $seq--) { 
				foreach($find_my_component_data_arr[$seq] as $component_id){
					if (in_array($row['BUYER_NAME'], $find_my_buyer_data_arr[$seq]) && $component_id) { 
						$job_id_arr[$seq][$row['JOB_ID']] = $row['JOB_ID'];
					}
				}
			}
			$job_data_arr[$row['JOB_ID']]=$row;
			$precost_id_arr[$row['ID']] = $row['ID'];
		}

	 //var_dump($find_my_buyer_data_arr[0]);die;
		
		foreach ($job_id_arr as $seq => $jobValArr) {
			$mySeq = $electronicDataArr['USER_SEQ'][$approved_by] - 1;
			$component_id_arr=$find_my_component_data_arr[$seq];
			if (count($job_id_arr[$seq]) && count($component_id_arr)) {
				$sqlArr[$seq] = "select c.ID,c.JOB_ID,c.COST_COMPONENT_ID from PRECOST_COMPONENT_APP_MST c where c.COST_COMPONENT_ID in(" . implode(',', $component_id_arr) . ") and c.ENTRY_FORM=11  and c.READY_TO_APPROVED=1 ".where_con_using_array($job_id_arr[$seq],0,'c.JOB_ID')." and c.APPROVED_SEQ=$seq";
			}
		}
		$sql = implode(' union ', $sqlArr);
 	  	  //echo $sql;die;
		$sqlResult = sql_select($sql);
		$dataArr = array();
		foreach ($sqlResult as $row) {
			$dataArr[$row['JOB_ID']][$row['ID']] = $row['COST_COMPONENT_ID'];
		}

	}
	else{
			$my_sql=$electronicDataArr['USER_SEQ'][$approved_by];

			$sql= "select c.ID,c.JOB_ID,c.COST_COMPONENT_ID from PRECOST_COMPONENT_APP_MST  c where  c.ENTRY_FORM=11  and  c.APPROVED_SEQ=$my_sql and c.READY_TO_APPROVED=1";
			// echo $sql;die;
			$sqlResult = sql_select($sql);
			$dataTmpArr = array();$jobArr = array();
			foreach ($sqlResult as $row) {
				$dataTmpArr[$row['JOB_ID']][$row['ID']] = $row['COST_COMPONENT_ID'];
				$jobArr[$row['JOB_ID']]= $row['JOB_ID'];
			}

			$precostJobSql = "select a.ID,a.JOB_ID,a.JOB_NO,b.BUYER_NAME,B.STYLE_REF_NO,a.INSERT_DATE,a.UPDATED_BY,a.UPDATE_DATE,A.COSTING_DATE,to_char(a.INSERT_DATE,'YYYY') as YEAR,a.INSERTED_BY from WO_PRE_COST_MST a,WO_PO_DETAILS_MASTER b where b.id=a.job_id and b.COMPANY_NAME=$cbo_company_name and a.READY_TO_APPROVED=1 and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 $where_con $date_cond ".where_con_using_array($jobArr,0,'b.id')."";
			//  echo $precostJobSql;die;
			$precostJobSqlResult = sql_select($precostJobSql);
			$job_data_arr=array();$precost_id_arr=array();$dataArr = array();
			foreach ($precostJobSqlResult as $row) {
				$job_data_arr[$row['JOB_ID']]=$row;
				$precost_id_arr[$row['ID']] = $row['ID'];

				$dataArr[$row['JOB_ID']]=$dataTmpArr[$row['JOB_ID']];

			}
	}


	$notAppReqSql = "select ID,BOOKING_ID,APPROVAL_CAUSE from FABRIC_BOOKING_APPROVAL_CAUSE where entry_form=15 ".where_con_using_array($precost_id_arr,1,'booking_id')."  and approval_type=2 and status_active=1 and is_deleted=0 ORDER BY ID"; 
	//echo $notAppReqSql;
	$notAppReqSqlRes = sql_select($notAppReqSql);
	$not_app_req_data_arr=array();
	foreach ($notAppReqSqlRes as $not_row) {
		$not_app_req_data_arr[$not_row['BOOKING_ID']] = $not_row['APPROVAL_CAUSE'];
	}

	$notAppCausSql = "select ID,ENTRY_FORM,MST_ID,COMPONENT_ID,REFUSING_REASON from PRECOST_COMPONENT_NOT_APP_CA where current_status=1 AND entry_form=11 ".where_con_using_array($precost_id_arr,0,'mst_id')." and inserted_by=$approved_by "; 
	//echo $notAppCausSql;
	$notAppCausSqlRes = sql_select($notAppCausSql);
	$not_app_cause_data_arr=array();
	foreach ($notAppCausSqlRes as $not_row) {
		$not_app_cause_data_arr[$not_row['MST_ID']][$not_row['COMPONENT_ID']] = $not_row['REFUSING_REASON'];
	}

	$width=1300;

	?>
		<table cellspacing="0" cellpadding="0" border="0" rules="all" width="<?=$width;?>" class="rpt_table">
			<thead>
				<th width="35">SL</th>
				<th width="80">Job No</th>
				<th width="170">Cost Components</th>
				<th width="50">Year</th>
				<th width="100">Buyer</th>
				<th width="100">Style Ref.</th>
				<th width="100">Costing Date</th>
				<th width="100">Est. Ship Date</th>
				<th width="50">Image</th>
				<th width="50">file</th>
				<th width="100">Insert By</th>
				<th width="80">Insert Date</th>
				<th width="100">Un-Approved Request</th>
				<th colspan="2">Deny causes</th>
			</thead>
		</table>
		<table cellspacing="0" cellpadding="0" border="0" rules="all" width="<?=$width;?>" class="rpt_table" id="tbl_list_search">
			<tbody>
				<?
				$sl = 1;$csl = 1;
				foreach ($dataArr as $job_id => $component_id_arr) {
					// print_r($component_id_arr) ;
					$rowspan = count($component_id_arr);
				?>
				<tr>
					<td width="35" rowspan="<?= $rowspan; ?>" valign="middle" align="center"><?= $sl; ?></td>
					<td width="80" rowspan="<?= $rowspan; ?>" valign="middle" align="center">
						<input type="checkbox" onclick="toggle_check(<?= $job_id;?>,'<?= implode(',',$component_id_arr);?>')" value="<?= $job_id;?>" id="job_<?= $job_id;?>"><br>
						<? if(in_array(1,$component_id_arr)){ ?>
							<a href="javascript:fn_po_wise_comments(<?= $job_id; ?>)"><?= $job_data_arr[$job_id]['JOB_NO']; ?></a>
						<? 
						} 
						else{
							echo $job_data_arr[$job_id]['JOB_NO']; 
						}
						?>
						<br/>
						<a href='##' id="bomRpt3<?= $job_id;?>" onclick="generate_report('bomRpt3','<?= $job_data_arr[$job_id]['JOB_NO']; ?>',<? echo $company_name; ?>,<?= $job_data_arr[$job_id]['BUYER_NAME']; ?>,'<?=$job_data_arr[$job_id]['STYLE_REF_NO'];?>','<?= $job_data_arr[$job_id]['COSTING_DATE']; ?>','')">BOM 3</a><br/>
						<a href='##' id="fabric_cost_detail<?= $job_id;?>" onclick="generate_report('fabric_cost_detail','<?=$job_data_arr[$job_id]['JOB_NO']; ?>',<? echo $company_name; ?>,<?=$job_data_arr[$job_id]['BUYER_NAME']; ?>,'<?=$job_data_arr[$job_id]['STYLE_REF_NO'];?>','<?=$job_data_arr[$job_id]['COSTING_DATE']; ?>','')">Fab Pre Cost</a>

					</td>
					<?
					$flag = 0; 
					asort($component_id_arr);
					foreach ($component_id_arr as $component_mst_id=>$component_id) {
						if ($flag == 1) {
							echo "<tr>";
						}
						?>

						<td valign="middle" width="170" id="td_<?=$job_id.$component_id;?>" onclick="check_bg_color('compo_<?=$job_id.$component_id;?>',this.id)" >
							<input type="checkbox" value="<?=$component_mst_id.','.$job_data_arr[$job_id]['ID'].','.$job_id.','.$component_id;?>" id="compo_<?=$job_id.$component_id;?>"  class="compo_<?=$csl;?>">
							<?=$cost_components[$component_id];?>
						</td>
						<?
						if($flag == 0){
						?>
							<td rowspan="<?= $rowspan; ?>" valign="middle" width="50" align="center"><?=$job_data_arr[$job_id]['YEAR'];?></td>
							<td rowspan="<?= $rowspan; ?>" valign="middle" width="100"><?= $buyer_arr[$job_data_arr[$job_id]['BUYER_NAME']]; ?></td>
							<td rowspan="<?= $rowspan; ?>" valign="middle" width="100"><?= $job_data_arr[$job_id]['STYLE_REF_NO']; ?></td>
							<td rowspan="<?= $rowspan; ?>" valign="middle" width="100" align="center"><?= change_date_format($job_data_arr[$job_id]['COSTING_DATE']); ?></td>
							<td rowspan="<?= $rowspan; ?>" valign="middle" width="100"></td>
							<td rowspan="<?= $rowspan; ?>" valign="middle" align="center" width="50"><a href="##" onClick="openImgFile('<?=$job_data_arr[$job_id]['JOB_NO'];?>','img');">View</a></td>
							<td rowspan="<?= $rowspan; ?>" valign="middle" align="center" width="50"><a href="javascript:void()" onClick="openPopup('<?=$job_data_arr[$job_id]['ID'];?>','Job File Pop up','job_file_popup')">File</a></td>
							<td rowspan="<?= $rowspan; ?>" valign="middle" width="100"><?= $user_arr[$job_data_arr[$job_id]['INSERTED_BY']]; ?></td>
							
							
							<td rowspan="<?= $rowspan; ?>" valign="middle" align="center" width="80"><?= change_date_format($job_data_arr[$job_id]['INSERT_DATE']);?></td>
							
				

							<td rowspan="<?= $rowspan; ?>" valign="middle"  width="100"><?=$not_app_req_data_arr[$job_data_arr[$job_id]['ID']];?></td>

							<td rowspan="<?= $rowspan; ?>" width="50" valign="middle" align="center"><a href="javascript:get_deny_cause_his('<?=$job_data_arr[$job_id]['ID'];?>')">History</a></td>
						
						<? 
						}
						?>
							<td style="cursor:pointer;" id="tdCause_<?=$job_data_arr[$job_id]['ID'].$component_id;?>" onClick="open_not_app_cause('refusing_cause_popup','Refusing Cause','<?=$job_data_arr[$job_id]['ID'].'**'.$component_id;?>');"><?=$not_app_cause_data_arr[$job_data_arr[$job_id]['ID']][$component_id];?></td>
						</tr>

					<?
						$flag = 1;
						$csl++;
					}
					$sl++;
				}
				?>
			</tbody>
		</table>
		<table cellspacing="0" cellpadding="0" border="0" rules="all" width="<?=$width;?>" class="rpt_table" id="tbl_list_search">
			<tfoot>
				<th width="35"></th>
				<th style="text-align:center;" width="80">
					<input type="checkbox" id="all_check" onclick="all_check()"> 
				</th>
				<th style="text-align:left;">
					<input type="button" value="<?=($approval_type == 1)?"UnApprove":"Approve"; ?>" class="formbutton" style="width:100px" onclick="submit_approved(<?=$csl;?>,<?=$approval_type;?>)" />

					<input type="button" value="Deny" class="formbutton" style="width:100px; display:<?=($approval_type==1)?'none;':'';?> " onClick="submit_approved(<?=$csl;?>,5);"/>

				</th>
			</tfoot>
		</table>



	<?

	

	exit();
}




if ($action == "approve") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$con = connect();

	$company_name = str_replace("'", "", $cbo_company_name);
	$txt_alter_user_id = str_replace("'", "", $txt_alter_user_id);
	$approved_by = ($txt_alter_user_id) ? $txt_alter_user_id : $user_id;

	$app_compo_data_arr=array();$app_job_arr=array();$app_cost_arr=array();
	foreach(explode('__',$app_data_str) as $precostid_jobid_componentid){
		list($componentmstid,$precostid,$jobid,$componentid)=explode(',',$precostid_jobid_componentid);
		$app_compo_data_arr[$jobid][$componentmstid]=$componentid;
		$app_job_arr[$jobid]=$jobid;
		$app_cost_arr[$jobid]=$precostid;
		 
	}

	$max_approved_sql = sql_select("select MST_ID, max(approved_no) as APPROVED_NO from approval_history where mst_id in(".implode(",",$app_cost_arr).") and entry_form=11 group by mst_id");
	$approvedNoArr= array();
	foreach ($max_approved_sql as $approvedRow) {          
		$approvedNoArr[$approvedRow['MST_ID']] = $approvedRow['APPROVED_NO'];           
	}	

	// print_r($app_job_arr);die;

	$electronicSetupSql = "select DEPARTMENT as COMPONENT_ID,BUYER_ID,USER_ID,BYPASS,SEQUENCE_NO,APPROVED_BY from electronic_approval_setup where company_id = $cbo_company_name and entry_form=11 and is_deleted=0";
	$electronicSetupSqlResult = sql_select($electronicSetupSql);
	$electronicDataArr = array();
	foreach ($electronicSetupSqlResult as $row) {
		$electronicDataArr['USER_SEQ'][$row['USER_ID']] = $row['SEQUENCE_NO'];
		$electronicDataArr['SEQ_DATA'][$row['SEQUENCE_NO']] = $row;
	}

	if($electronicDataArr['USER_SEQ'][$approved_by]<1){echo "21**";exit();}
	else if ($approval_type == 2) {

		 $totalSeq = count($electronicDataArr['SEQ_DATA']);
		 $my_seq=$electronicDataArr['USER_SEQ'][$approved_by];
		 $my_component_data_arr=explode(',',$electronicDataArr['SEQ_DATA'][$my_seq]['COMPONENT_ID']);


		$my_buyer_id_str = $electronicDataArr['SEQ_DATA'][$electronicDataArr['USER_SEQ'][$approved_by]]['BUYER_ID'];
		$precostJobSql = "select a.ID,a.JOB_ID,a.JOB_NO,a.APPROVED,b.BUYER_NAME,B.STYLE_REF_NO,B.INSERT_DATE,a.UPDATED_BY,a.UPDATE_DATE,A.COSTING_DATE,to_char(a.INSERT_DATE,'YYYY') as YEAR,a.INSERTED_BY from WO_PRE_COST_MST a,WO_PO_DETAILS_MASTER b where b.id=a.job_id and a.READY_TO_APPROVED=1 and a.APPROVED<>1 and b.COMPANY_NAME=$cbo_company_name and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.BUYER_NAME in($my_buyer_id_str) and b.id in(".implode(',',$app_job_arr).")";
		//echo $precostJobSql;die;
		$precostJobSqlResult = sql_select($precostJobSql);
		$job_component_wise_seq = array();
		$pre_cost_data_by_job_arr = array();
		foreach ($precostJobSqlResult as $row) {
			$curr_app_status_arr[$row['JOB_ID']] = $row['APPROVED']; 
			for($seq=$my_seq;$seq<=$totalSeq;$seq++){
				$seq_component_data_arr=explode(',',$electronicDataArr['SEQ_DATA'][$seq]['COMPONENT_ID']);
				
				foreach($my_component_data_arr as $com_id){
					if ( in_array($row['BUYER_NAME'], explode(',',$electronicDataArr['SEQ_DATA'][$seq]['BUYER_ID'])) && in_array($com_id,$seq_component_data_arr) ) {
						$job_component_wise_seq[$row['JOB_ID']][$com_id][$seq] = $seq;
					}
				}
			}

			$pre_cost_data_by_job_arr[$row['JOB_ID']]=$row;

		}

		
		$component_app_status_sql = "SELECT JOB_ID,COST_COMPONENT_ID,APPROVED FROM PRECOST_COMPONENT_APP_MST WHERE JOB_ID IN(".implode(',',$app_job_arr).") AND ENTRY_FORM=11 and APPROVED<>1";
		$component_app_status_sql_res = sql_select($component_app_status_sql);
		$job_wise_status_arr=array();
		foreach($component_app_status_sql_res as $rows){
			$rows['APPROVED']=($rows['APPROVED']==0)?3:$rows['APPROVED'];
			$job_wise_status_arr[$rows['JOB_ID']][$rows['COST_COMPONENT_ID']]=$rows['APPROVED'];
		}








		//----------------------------------------------------------------------------------------------------------------
		$data_array_cost = "";$data_array_cost_his = "";
		$id = return_next_id("id", "co_com_pre_costing_approval", 1);
		$his_id = return_next_id("id", "co_com_pre_costing_app_his", 1);
		$app_his_id = return_next_id( "id","approval_history", 1 ) ;
		$delet_job_id_arr=array(); 
		foreach($app_compo_data_arr as $jobid=>$component_id_arr){
			$mst_id = $app_cost_arr[$jobid];
			$precost_id_up[]=$mst_id;
			
			$job_no = $pre_cost_data_by_job_arr[$jobid]['JOB_NO'];
			$component_status_arr=array();
			$component_status_arr=$job_wise_status_arr[$jobid];

			foreach($component_id_arr as $component_mst_id=>$component_id){

				$curr_status=($my_seq==max($job_component_wise_seq[$jobid][$component_id]))?1:3;
				//......................................
				if ($data_array_cost != "") $data_array_cost .= ",";
				$data_array_cost .= "(" . $id . ",11," . $mst_id . ",'" . $job_no . "'," .$jobid . "," . $component_id . ",1,1," . $approved_by . ",'" . $pc_date_time . "')";
				$id++;
				$delet_job_id_arr[$jobid]=$jobid;
				//his.....................................
				if ($data_array_cost_his != "") $data_array_cost_his .= ",";
				$data_array_cost_his .= "(" . $his_id . ",11," . $mst_id . ",'" . $job_no . "'," . $component_id . ",1,1," . $approved_by . ",'" . $pc_date_time . "')";
				$his_id++;
				//.....................................end his;
				$component_data_up_arr[$jobid][$component_mst_id] = explode(",",("".$curr_status.",".$my_seq.",".$approved_by.",'".$pc_date_time."'")); 
				$component_id_up_arr[$jobid][]=$component_mst_id;
				$component_status_arr[$component_id]=$curr_status;
				
			}

			$precost_data_up[$mst_id] = explode(",",("0,".max($component_status_arr).",".max($component_status_arr).",0"));
			
			//History------------
			$approved_no = $approvedNoArr[$mst_id]*1; 
			if($curr_app_status_arr[$mst_id] ==0 || $curr_app_status_arr[$mst_id] ==2){
				$approved_no = $approved_no+1; 
				$approved_no_array[$jobid] = $approved_no;
				$target_job_no_arr[$jobid] = $jobid;
			}
			
			if($data_history_array_approved!="") $data_history_array_approved.=",";
			$data_history_array_approved.="(".$app_his_id.",11,".$mst_id.",".$approved_no.",'".$my_seq."',1,".$curr_status.",".$approved_by.",'".$pc_date_time."')"; 
			$app_his_id++;
		}

	

		$flag=1;
		//.........................................
		if($flag==1){
			$component_field_up="APPROVED*APPROVED_SEQ*APPROVED_BY*APPROVED_DATE";	
			foreach($component_data_up_arr as $key => $component_data_up){
				$updateSql = bulk_update_sql_statement( "PRECOST_COMPONENT_APP_MST", "id", $component_field_up, $component_data_up, $component_id_up_arr[$key] );
				$updateSql .= " and JOB_ID=$key";
			   $rID=execute_query($updateSql);
			   if($rID && $flag==1){$flag=1;}else{$flag=0;}
			}

		}

		//echo $flag;oci_rollback($con);die;
		//...............................................
		if($flag==1 && count($delet_job_id_arr)>0){
            $deleteSuccess = execute_query("DELETE FROM co_com_pre_costing_approval WHERE cost_component_id in(".implode(",",$app_cost_arr).") and job_id in(".implode(",",$delet_job_id_arr).")",1);
			if($deleteSuccess && $flag==1){$flag=1;}else{$flag=0;}
		}
		
		if($flag==1){
			$field_array_cost = "id, entry_form, mst_id, job_no,job_id,cost_component_id, current_approval_status,higher_othorized_approved, approved_by, approved_date";
			$rID1 = sql_insert("co_com_pre_costing_approval", $field_array_cost, $data_array_cost, 0);	
			if($rID1 && $flag==1){$flag=1;}else{$flag=0;}
		}

		//...............................................
		if($flag==1){
			$field_array_cost_his="id, entry_form, mst_id, job_no,cost_component_id, current_approval_status,higher_othorized_approved, approved_by, approved_date"; 
			$rID2 = sql_insert("co_com_pre_costing_app_his", $field_array_cost_his, $data_array_cost_his, 0);	
			if($rID2 && $flag==1){$flag=1;}else{$flag=0;}
		}


		//...............................................
		if($flag==1){
			$precost_field_up="higher_othorized_approved*approved*partial_approved";	
			$updatePreSql = bulk_update_sql_statement( "WO_PRE_COST_MST", "id", $precost_field_up, $precost_data_up, $precost_id_up );
			//echo $updateSql;die;
			$rID1_up=execute_query($updatePreSql);
			if($rID1_up && $flag==1){$flag=1;}else{$flag=0;}
		}

		//...............................................
		if($flag==1){
			$query = "UPDATE approval_history SET current_approval_status=0,un_approved_by=$approved_by,un_approved_date='{$pc_date_time}' WHERE entry_form=11 and mst_id in(".implode(",",$app_cost_arr).") and current_approval_status=1 and approved_by=$approved_by"; 
			$HisDel = execute_query($query,1); 
			if($HisDel && $flag==1){$flag=1;}else{$flag=0;}
			
			$approve_history_field_array = "id, entry_form, mst_id,approved_no,sequence_no,current_approval_status,full_approved,approved_by, approved_date"; 
			$rID3 = sql_insert("approval_history",$approve_history_field_array,$data_history_array_approved,0); 
			if($rID3 && $flag==1){$flag=1;}else{$flag=0;}
		}

		
		if(count($approved_no_array)>0)
		{
			$job_ids  = implode(",",$target_job_no_arr);
			
			$approved_string="";
			foreach($approved_no_array as $key=>$value)
			{
				$approved_string.=" WHEN $key THEN ".$value."";
			}
			$approved_string_mst="CASE job_id ".$approved_string." END";
			//$approved_string_dtls="CASE job_id ".$approved_string." END";

			$sql_insert="insert into wo_pre_cost_mst_histry(id, approved_no, pre_cost_mst_id, garments_nature, job_no, costing_date, incoterm, incoterm_place,
			machine_line, prod_line_hr, costing_per, remarks, copy_quatation, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent,
			cut_effi_percent, efficiency_wastage_percent, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active,
			is_deleted)
					select
					'', $approved_string_mst, id, garments_nature, job_no, costing_date, incoterm, incoterm_place, machine_line, prod_line_hr, costing_per,
			remarks, copy_quatation, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent,
			efficiency_wastage_percent, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted
			from wo_pre_cost_mst where job_id in ($job_ids)";
			 // echo $sql_insert;die;


			$sql_precost_dtls="insert into wo_pre_cost_dtls_histry(id,approved_no,pre_cost_dtls_id,job_no,costing_per_id,order_uom_id,fabric_cost,  fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,
			commission_percent,	lab_test,lab_test_percent,inspection, inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,
			currier_percent, certificate_pre_cost,certificate_percent,common_oh,common_oh_percent,total_cost,total_cost_percent,price_dzn,price_dzn_percent,
			margin_dzn,margin_dzn_percent,cost_pcs_set,cost_pcs_set_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,
			cm_for_sipment_sche,margin_pcs_bom, margin_bom_per,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
					select 
					'', $approved_string_mst, id,job_no,costing_per_id,order_uom_id,fabric_cost,  fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,
			commission_percent,	lab_test,lab_test_percent,inspection, inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,
			currier_percent, certificate_pre_cost,certificate_percent,common_oh,common_oh_percent,total_cost,total_cost_percent,price_dzn,price_dzn_percent,
			margin_dzn,margin_dzn_percent,cost_pcs_set,cost_pcs_set_percent,price_pcs_or_set,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,
			cm_for_sipment_sche,margin_pcs_bom, margin_bom_per,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_dtls  where  job_id in ($job_ids)";
			//echo $sql_precost_dtls;die;


			//------------------wo_pre_cost_fabric_cost_dtls_h-------------------------------------------------
			$sql_precost_fabric_cost_dtls="insert into wo_pre_cost_fabric_cost_dtls_h(id,approved_no,pre_cost_fabric_cost_dtls_id, job_no, item_number_id, body_part_id, fab_nature_id, color_type_id,lib_yarn_count_deter_id,construction,composition, fabric_description, gsm_weight,color_size_sensitive,	color, avg_cons, fabric_source, rate, amount,avg_finish_cons,avg_process_loss, inserted_by, insert_date,updated_by,update_date, status_active, is_deleted, company_id, costing_per,consumption_basis,process_loss_method,cons_breack_down,msmnt_break_down,color_break_down,yarn_breack_down,marker_break_down,width_dia_type)
				select 
				'', $approved_string_mst, id,job_no, item_number_id, body_part_id, fab_nature_id, color_type_id,lib_yarn_count_deter_id,construction,composition, fabric_description, gsm_weight,color_size_sensitive,	color, avg_cons, fabric_source, rate,amount,avg_finish_cons,avg_process_loss, inserted_by, insert_date,updated_by,update_date, status_active, is_deleted, company_id, costing_per,consumption_basis,process_loss_method,cons_breack_down,msmnt_break_down,color_break_down,yarn_breack_down,marker_break_down,width_dia_type from wo_pre_cost_fabric_cost_dtls where  job_id in ($job_ids)";
			//echo $sql_precost_fabric_cost_dtls;die;

			//--------------------wo_pre_cost_fab_yarn_cst_dtl_h--------------------------------------------------------
			$sql_precost_fab_yarn_cst="insert into wo_pre_cost_fab_yarn_cst_dtl_h(id,approved_no,pre_cost_fab_yarn_cost_dtls_id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
				select
				'', $approved_string_mst, id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,
			inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_fab_yarn_cost_dtls  where  job_id in ($job_ids)";
				//echo $sql_precost_fab_yarn_cst;die;

			//----------------------wo_pre_cost_comarc_cost_dtls_h----------------------------------------
			$sql_precost_fcomarc_cost_dtls="insert into wo_pre_cost_comarc_cost_dtls_h(id,approved_no,pre_cost_comarci_cost_dtls_id,job_no,item_id,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
				select
				'', $approved_string_mst,id,job_no,item_id,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,
			is_deleted from wo_pre_cost_comarci_cost_dtls where  job_id in ($job_ids)";
				//echo $sql_precost_fcomarc_cost_dtls;die;


			//-------------------------------------pre_cost_commis_cost_dtls_h-------------------------------------------
			$sql_precost_commis_cost_dtls="insert into wo_pre_cost_commis_cost_dtls_h(id,approved_no,pre_cost_commiss_cost_dtls_id,job_no,particulars_id,
			commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
				select
				'', $approved_string_mst, id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_commiss_cost_dtls where  job_id in ($job_ids)";
			//	echo $sql_precost_commis_cost_dtls;die;

			//--------------------------------------   wo_pre_cost_embe_cost_dtls_his---------------------------------------------------------------------------
			$sql_precost_embe_cost_dtls="insert into  wo_pre_cost_embe_cost_dtls_his(id,approved_no,pre_cost_embe_cost_dtls_id,job_no,emb_name,
			emb_type,cons_dzn_gmts,rate,amount,charge_lib_id,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
				select
				'', $approved_string_mst, id,job_no,emb_name,
			emb_type,cons_dzn_gmts,rate,amount,charge_lib_id,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_embe_cost_dtls  where  job_id in ($job_ids)";
				//echo $sql_precost_commis_cost_dtls;die;

			//---------------------------------wo_pre_cost_fab_yarnbkdown_his------------------------------------------------

			$sql_precost_fab_yarnbkdown_his="insert into  wo_pre_cost_fab_yarnbkdown_his(id,approved_no,pre_cost_fab_yarnbreakdown_id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
				select
				'', $approved_string_mst, id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_fab_yarnbreakdown  where  job_id in ($job_ids)";
				//echo $sql_precost_fab_yarnbkdown_his;die;

			//------------------------------wo_pre_cost_sum_dtls_histroy-----------------------------------------------

			$sql_precost_fab_sum_dtls="insert into  wo_pre_cost_sum_dtls_histroy(id,approved_no,pre_cost_sum_dtls_id,job_no,fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_woven_fin_req_yds,fab_knit_fin_req_kg,fab_amount,avg,yarn_cons_qnty,yarn_amount,conv_req_qnty,conv_charge_unit,conv_amount,trim_cons,trim_rate,trim_amount,emb_amount,comar_rate,
			comar_amount,commis_rate,commis_amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
				select
				'', $approved_string_mst, id,job_no,fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_woven_fin_req_yds,fab_knit_fin_req_kg,fab_amount,avg,yarn_cons_qnty,yarn_amount,conv_req_qnty,conv_charge_unit,conv_amount,trim_cons,trim_rate,trim_amount,emb_amount,comar_rate,
			comar_amount,commis_rate,commis_amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_sum_dtls  where  job_id in ($job_ids)";
				//echo $sql_precost_fab_sum_dtls;die;
				//-----------------------------wo_pre_cost_trim_cost_dtls_his------------------------------	-------------

			$sql_precost_trim_cost_dtls="insert into  wo_pre_cost_trim_cost_dtls_his(id,approved_no,pre_cost_trim_cost_dtls_id,job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,cons_breack_down,inserted_by, insert_date,updated_by,update_date, status_active,is_deleted)
				select
				'', $approved_string_mst, id,job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,cons_breack_down,inserted_by, insert_date,updated_by,update_date, status_active,is_deleted from wo_pre_cost_trim_cost_dtls  where  job_id in ($job_ids)";
				//echo $sql_precost_trim_cost_dtls;die;


			//---------------------------wo_pre_cost_trim_co_cons_dtl_h--------------------------------------------------

			$sql_precost_trim_co_cons_dtl="insert into   wo_pre_cost_trim_co_cons_dtl_h(id,approved_no,pre_cost_trim_co_cons_dtls_id,wo_pre_cost_trim_cost_dtls_id,job_no, po_break_down_id,item_size, cons, place, pcs,country_id)
				select
				'', $approved_string_mst, id,wo_pre_cost_trim_cost_dtls_id,job_no,po_break_down_id,item_size, cons,place, pcs,country_id from wo_pre_cost_trim_co_cons_dtls  where  job_id in ($job_ids)";
			//---------------------------wo_pre_cost_fab_con_cst_dtls_h-----------------------------------------

			$sql_precost_fab_con_cst_dtls="insert into   wo_pre_cost_fab_con_cst_dtls_h(id,approved_no,pre_cost_fab_conv_cst_dtls_id,job_no, fabric_description,cons_process,req_qnty,charge_unit,amount,color_break_down,charge_lib_id,inserted_by,insert_date,updated_by,update_date, status_active,is_deleted)
				select
				'', $approved_string_mst, id,job_no, fabric_description,cons_process,req_qnty,charge_unit,amount,color_break_down,charge_lib_id,inserted_by,insert_date,updated_by,update_date, status_active,is_deleted from wo_pre_cost_fab_conv_cost_dtls  where  job_id in ($job_ids)";


			if($flag==1)
			{
				$rID4=execute_query($sql_precost_trim_cost_dtls,1);
				if($rID4) $flag=1; else $flag=0;
			}


			if($flag==1)
			{
				$rID5=execute_query($sql_precost_trim_co_cons_dtl,1);
				if($rID5) $flag=1; else $flag=0;
			}

			if($flag==1)
			{
				$rID6=execute_query($sql_precost_fab_con_cst_dtls,1);
				if($rID6) $flag=1; else $flag=0;
			}

			if($flag==1)
			{
				$rID7=execute_query($sql_insert,0);
				if($rID7) $flag=1; else $flag=0;
			}


			if($flag==1)
			{
				$rID8=execute_query($sql_precost_dtls,1);
				if($rID8) $flag=1; else $flag=0;
			}


			if($flag==1)
			{
				$rID9=execute_query($sql_precost_fabric_cost_dtls,1);
				if($rID9) $flag=1; else $flag=0;
			}


			if($flag==1)
			{
				$rID10=execute_query($sql_precost_fab_yarn_cst,1);
				if($rID10) $flag=1; else $flag=0;
			}


			if($flag==1)
			{
				$rID11=execute_query($sql_precost_fcomarc_cost_dtls,1);
				if($rID11) $flag=1; else $flag=0;
			}


			if($flag==1)
			{
				$rID12=execute_query($sql_precost_commis_cost_dtls,1);
				if($rID12) $flag=1; else $flag=0;
			}

			if($flag==1)
			{
				$rID13=execute_query($sql_precost_embe_cost_dtls,1);
				if($rID13) $flag=1; else $flag=0;
			}

			if($flag==1)
			{
				$rID14=execute_query($sql_precost_fab_yarnbkdown_his,1);
				if($rID14) $flag=1; else $flag=0;
			}

			if($flag==1)
			{
				$rID15=execute_query($sql_precost_fab_sum_dtls,1);
				if($rID14) $flag=1; else $flag=0;
			}
		}

		
		//echo "21**$rID1**$rID2**$rID1_up**$HisDel**$rID3**$rID4**$rID5**$rID6**$rID7**$rID8**$rID9**$rID10**$rID11**$rID12**$rID13**$rID14";oci_rollback($con);die;

		if($flag==1){$msg=19;}else{$msg=21;}
				
	}
	else if($approval_type == 1){

		//echo $jobid;$componentid;die;
		$app_his_id = return_next_id( "id","approval_history", 1 ) ;
		$my_seq = $electronicDataArr['USER_SEQ'][$approved_by];
		
		$dtlete_component_id_arr=array();
		foreach($app_compo_data_arr as $jobid=>$component_id_arr){
			$mst_id = $app_cost_arr[$jobid];
			$precost_id_up[]=$mst_id;
			foreach($component_id_arr as $component_mst_id=>$component_id){

				$component_data_up_arr[$jobid][$component_mst_id] =explode(",",("0,0,0,".$approved_by.",'".$pc_date_time."'")); 
				$component_id_up_arr[$jobid][]=$component_mst_id;

				$precost_data_up[$mst_id] =explode(",",("0,0,0,0")); 
				$dtlete_component_id_arr[$component_id]=$component_id;


			}

			//History..........
			$approved_no = $approvedNoArr[$mst_id]*1; 
			if($data_history_array_approved!="") $data_history_array_approved.=",";
			$data_history_array_approved.="(".$app_his_id.",11,".$mst_id.",".$approved_no.",'".$my_seq."',0,0,".$approved_by.",'".$pc_date_time."')"; 
			$app_his_id++;
			
		}

		 //print_r($precost_id_up);die;
		//echo $data_array_cost;die;

		$flag=1;
		//.........................................
		if($flag==1){
			$component_field_up="READY_TO_APPROVED*APPROVED*APPROVED_SEQ*UNAPPROVED_BY*UNAPPROVED_DATE";
			foreach($component_data_up_arr as $key => $component_data_up){	
				$updateSql = bulk_update_sql_statement( "PRECOST_COMPONENT_APP_MST", "id", $component_field_up, $component_data_up, $component_id_up_arr[$key] );
				$updateSql .= " and JOB_ID=$key";
				$rID_up=execute_query($updateSql);
				if($rID_up && $flag==1){$flag=1;}else{$flag=0;}
			}
		}



		//...............................................
		if($flag==1){
			$precost_field_up="higher_othorized_approved*approved*partial_approved*ready_to_approved";	
			$updatePreSql = bulk_update_sql_statement( "WO_PRE_COST_MST", "id", $precost_field_up, $precost_data_up, $precost_id_up );
			//echo $updateSql;die;
			$rID1_up=execute_query($updatePreSql);
			if($rID1_up && $flag==1){$flag=1;}else{$flag=0;}
		}

		//...............................................
		if($flag==1){
			$rID2_DEL = execute_query("DELETE FROM co_com_pre_costing_approval  WHERE job_id in(".implode(",",$app_job_arr).") AND cost_component_id IN(".implode(",",$dtlete_component_id_arr).") and current_approval_status =1",1);
			if($rID2_DEL && $flag==1){$flag=1;}else{$flag=0;}
		}

		//...............................................
		if($flag==1){
			$query = "UPDATE approval_history SET current_approval_status=0,un_approved_by=$approved_by,un_approved_date='{$pc_date_time}' WHERE entry_form=11 and mst_id in(".implode(",",$app_cost_arr).") and current_approval_status=1 and approved_by=$approved_by"; 
			$HisDel = execute_query($query,1); 
			if($HisDel && $flag==1){$flag=1;}else{$flag=0;}
		}

		//...............................................
		if($flag==1){
			$approve_history_field_array = "id, entry_form, mst_id,approved_no,sequence_no,current_approval_status,full_approved,approved_by, approved_date"; 
			$rIDIn = sql_insert("approval_history",$approve_history_field_array,$data_history_array_approved,0); 
			if($rIDIn && $flag==1){$flag=1;}else{$flag=0;}
		}

		 //echo $query;oci_rollback($con);die;
		// echo "21**$rID1_up**$rID2_DEL**$HisDel**";oci_rollback($con);die;


		if($flag==1){$msg=20;}else{$msg=21;}
	}
	else if($approval_type == 5){

		$app_his_id = return_next_id( "id","approval_history", 1 ) ;
		$my_seq = $electronicDataArr['USER_SEQ'][$approved_by];
		
		$dtlete_component_id_arr=array();
		foreach($app_compo_data_arr as $jobid=>$component_id_arr){
			$mst_id = $app_cost_arr[$jobid];
			$precost_id_up[]=$mst_id;
			foreach($component_id_arr as $component_mst_id=>$component_id){

				$component_data_up[$component_mst_id] =explode(",",("0,2,0,".$approved_by.",'".$pc_date_time."'")); 
				$component_id_up[]=$component_mst_id;

				$precost_data_up[$mst_id] =explode(",",("0,2,0,0")); 
				$dtlete_component_id_arr[$component_id]=$component_id;


			}

				//History..........
				$approved_no = $approvedNoArr[$mst_id]*1; 
				if($data_history_array_approved!="") $data_history_array_approved.=",";
				$data_history_array_approved.="(".$app_his_id.",11,".$mst_id.",".$approved_no.",'".$my_seq."',0,2,".$approved_by.",'".$pc_date_time."')"; 
				$app_his_id++;
			
		}

		 //print_r($precost_id_up);die;
		//echo $data_array_cost;die;

		$flag=1;
		//.........................................
		if($flag==1){
			$component_field_up="READY_TO_APPROVED*APPROVED*APPROVED_SEQ*UNAPPROVED_BY*UNAPPROVED_DATE";	
			$updateSql = bulk_update_sql_statement( "PRECOST_COMPONENT_APP_MST", "id", $component_field_up, $component_data_up, $component_id_up );
			//echo $updateSql;die;
			$rID_up=execute_query($updateSql);
			if($rID_up && $flag==1){$flag=1;}else{$flag=0;}
		}

		//...............................................
		if($flag==1){
			$precost_field_up="higher_othorized_approved*approved*partial_approved*ready_to_approved";	
			$updatePreSql = bulk_update_sql_statement( "WO_PRE_COST_MST", "id", $precost_field_up, $precost_data_up, $precost_id_up );
			//echo $updateSql;die;
			$rID1_up=execute_query($updatePreSql);
			if($rID1_up && $flag==1){$flag=1;}else{$flag=0;}
		}

		//...............................................
		if($flag==1){
			$rID2_DEL = execute_query("DELETE FROM co_com_pre_costing_approval  WHERE job_id in(".implode(",",$app_job_arr).") AND cost_component_id IN(".implode(",",$dtlete_component_id_arr).") and current_approval_status =1",1);
			if($rID2_DEL && $flag==1){$flag=1;}else{$flag=0;}
		}

		//...............................................
		if($flag==1){
			$DENY = execute_query("UPDATE PRECOST_COMPONENT_NOT_APP_CA SET current_status=0  WHERE mst_id in(".implode(",",$precost_id_up).") AND entry_form=11",1);
			if($DENY && $flag==1){$flag=1;}else{$flag=0;}
		}

		//...............................................
		if($flag==1){
			$query = "UPDATE approval_history SET current_approval_status=0,un_approved_by=$approved_by,un_approved_date='{$pc_date_time}' WHERE entry_form=11 and mst_id in(".implode(",",$precost_id_up).") and current_approval_status=1 and approved_by=$approved_by"; 
			$HisDel = execute_query($query,1); 
			if($HisDel && $flag==1){$flag=1;}else{$flag=0;}
		}

		//...............................................
		if($flag==1){
			$approve_history_field_array = "id, entry_form, mst_id,approved_no,sequence_no,current_approval_status,full_approved,approved_by, approved_date"; 
			$rIDIn = sql_insert("approval_history",$approve_history_field_array,$data_history_array_approved,0); 
			if($rIDIn && $flag==1){$flag=1;}else{$flag=0;}
		}


		//echo "21**$rID_up**$rID1_up**$rID2_DEL**$DENY**$HisDel**";oci_rollback($con);die;

		if($flag==1){$msg=37;}else{$msg=21;}
	}




 
	if ($flag == 1) {
		oci_commit($con);
		echo $msg . "**" . $response;
	} else {
		oci_rollback($con);
		echo $msg . "**" . $response;
	}



 
	disconnect($con);
	die;

}//approved;



if ($action == "job_file_popup") {
	echo load_html_head_contents("Job Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);

	$job_file = sql_select("SELECT id, master_tble_id, image_location, real_file_name from common_photo_library where is_deleted=0 and form_name = 'pre_cost_v2'	and file_type = 2 and master_tble_id='$data'");

	//$sql = "select image_location from common_photo_library where master_tble_id='$data' and form_name='knit_order_entry' and file_type=2";
 

	?>
	<fieldset style="width:670px; margin-left:3px">
		<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
			<thead>
				<tr>
					<th>SL</th>
					<th>File Name</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?
				$i = 1;
				foreach ($job_file as $row) {
					$filename_arr = explode(".", $row[csf('real_file_name')]);
				?>
					<tr>
						<td><?= $i ?></td>
						<td><?= $filename_arr[0]; ?></td>
						<td><a href="../../<?= $row[csf('image_location')];  ?>" download>download</a></td>
					</tr>
				<?
					$i++;
				}
				?>
			</tbody>
		</table>
	</fieldset>
	<?
	exit();
}



if ($action == "img") {
	echo load_html_head_contents("Image View", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<fieldset style="width:600px; margin-left:5px">
		<div style="width:100%; word-wrap:break-word" id="scroll_body">
			<table border="0" rules="all" width="100%" cellpadding="2" cellspacing="2">
				<tr>
					<?
					$i = 0;
					$sql = "select image_location from common_photo_library where master_tble_id='$id' and form_name='knit_order_entry' and file_type=1";
					$result = sql_select($sql);
					foreach ($result as $row) {
						$i++;
					?>
						<!--<td align="center"><? echo $row[csf('image_location')]; ?></td>-->
						<td align="center"><img width="300px" height="180px" src="../../<? echo $row[csf('image_location')]; ?>" /></td>
					<?
						if ($i % 2 == 0) echo "</tr><tr>";
					}
					?>
				</tr>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}



if($action=="refusing_cause_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Refusing Cause Info","../../", 1, 1, $unicode);
	list($pre_cost_id,$component_id,$alter_user_id)=explode('**',$data);
	$alter_user_id=(str_replace("'","",$alter_user_id)!='')?$alter_user_id:$_SESSION['logic_erp']['user_id'];
	
	$notAppCausSql = "select ID,ENTRY_FORM,MST_ID,COMPONENT_ID,REFUSING_REASON from PRECOST_COMPONENT_NOT_APP_CA where current_status=1 and entry_form=11 and mst_id=$pre_cost_id and inserted_by=$alter_user_id and COMPONENT_ID=$component_id "; 
	$notAppCausSqlRes = sql_select($notAppCausSql);

	
	
	?>
    <script>
 	var permission='<? echo $permission; ?>';
	
    function fnc_cause_info( operation )
	{
		var refusing_cause=$("#txt_refusing_cause").val();
		var hidden_component_id=$("#hidden_component_id").val();
		var hidden_pre_cost_id=$("#hidden_pre_cost_id").val();
		var hidden_alter_user_id=$("#hidden_alter_user_id").val();
  		if (form_validation('txt_refusing_cause','Not Appv. Cause')==false)
		{
			return;
		}
		else
		{
			var data="action=save_update_delete_refusing_cause&operation="+operation+"&refusing_cause="+refusing_cause+"&hidden_pre_cost_id="+hidden_pre_cost_id+"&hidden_component_id="+hidden_component_id+"&hidden_alter_user_id="+hidden_alter_user_id;
			http.open("POST","component_wise_precosting_approval_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange =()=>{
				if(http.readyState == 4)
				{
					var response=trim(http.responseText).split('**');
					if(response[0]==0)
					{
						alert("Data saved successfully");
						parent.emailwindow.hide();
					}
					else if(response[0]==2)
					{
						alert("Data delete successfully");
						$('#txt_refusing_cause').val('');
						parent.emailwindow.hide();
					}
					else
					{
						alert("Data not saved");
						return;
					}
				}
			}


		}
	}
	

    </script>
    <body  onload="set_hotkey();">
    <div align="center" style="width:100%;">
	<fieldset style="width:95%;">
		<legend>Refusing Cause</legend>
		<form name="causeinfo_1" id="causeinfo_1"  autocomplete="off">
			<table cellpadding="0" cellspacing="2" width="95%">
			 	<tr>
					<td width="100" class="must_entry_caption">Deny Cause</td>
					<td >
						<textarea name="txt_refusing_cause" id="txt_refusing_cause" style="width:95%; height:100px;" class="text_boxes"><?=$notAppCausSqlRes[0]['REFUSING_REASON'];?></textarea>
						<input type="hidden" name="hidden_pre_cost_id" id="hidden_pre_cost_id" value="<? echo $pre_cost_id;?>">
						<input type="hidden" name="hidden_component_id" id="hidden_component_id" value="<? echo $component_id;?>">
						<input type="hidden" name="hidden_alter_user_id" id="hidden_alter_user_id" value="<? echo $alter_user_id;?>">
					</td>
                  </tr>
                  <tr>
					<td colspan="4" align="center" class="button_container">
						<?
							$su_status=($notAppCausSqlRes[0]['ID'])?1:0;
					     	echo load_submit_buttons( $permission, "fnc_cause_info", $su_status,0 ,"reset_form('causeinfo_1','','')",1);
				        ?>
					</td>
				</tr>
				<tr>
					<td colspan="4" align="center">&nbsp;</td>
				</tr>
		   </table>
			</form>
		</fieldset>
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
	$refusing_cause = str_replace(["(",")","[","]"]," ",$refusing_cause);

	if ($operation==0 || $operation==1)  // Insert Here
	{
		$con = connect();
		
		$app_user_id = ($hidden_alter_user_id)?$hidden_alter_user_id:$_SESSION['logic_erp']['user_id'];
		$id=return_next_id( "id", "PRECOST_COMPONENT_NOT_APP_CA", 1);
		$field_array = "id,entry_form,mst_id,component_id,refusing_reason,current_status,inserted_by,insert_date";
		$data_array = "(".$id.",11,".$hidden_pre_cost_id.",".$hidden_component_id.",'".$refusing_cause."',1,".$app_user_id.",'".$pc_date_time."')";

		$delete = execute_query("DELETE FROM PRECOST_COMPONENT_NOT_APP_CA WHERE  current_status=1 AND entry_form=11 and inserted_by=$app_user_id and mst_id=$hidden_pre_cost_id and component_id=$hidden_component_id" ,1);
		$rID=sql_insert("PRECOST_COMPONENT_NOT_APP_CA",$field_array,$data_array,1);

		if($rID)
		{
			oci_commit($con);
			echo "0**$refusing_cause";
		}
		else{
			oci_rollback($con);
			echo "10**";
		}
	
		disconnect($con);
		die;
	}
	else if($operation==2){
		$con = connect();
			$app_user_id = ($hidden_alter_user_id)?$hidden_alter_user_id:$_SESSION['logic_erp']['user_id'];
			$delete = execute_query("DELETE FROM PRECOST_COMPONENT_NOT_APP_CA WHERE  current_status=1 AND entry_form=11 and inserted_by=$app_user_id and mst_id=$hidden_pre_cost_id and component_id=$hidden_component_id" ,1);

			if($delete)
			{
				oci_commit($con);
				echo "2**$refusing_cause";
			}
			else{
				oci_rollback($con);
				echo "10**";
			}

		disconnect($con);
		die;
	}






}



if($action == 'deny_cause_his_dtls'){
	extract($_REQUEST);
	echo load_html_head_contents("Refusing Cause Info","../../", 1, 1, $unicode);

	$designation_arr = return_library_array("select id,CUSTOM_DESIGNATION from  LIB_DESIGNATION ", 'id', 'CUSTOM_DESIGNATION');
	$notAppCausSql = "select a.INSERT_DATE, a.COMPONENT_ID,a.REFUSING_REASON,b.USER_NAME,b.DESIGNATION from PRECOST_COMPONENT_NOT_APP_CA a,USER_PASSWD b where a.INSERTED_BY=b.id and a.current_status=0 and a.entry_form=11 and a.mst_id=$pre_cost_id order by a.INSERT_DATE asc";
	//echo $notAppCausSql; 
	$notAppCausSqlRes = sql_select($notAppCausSql);
	$dataArr=array();
	foreach($notAppCausSqlRes as $row){
		$key=$row['USER_NAME'].'**'.$row['DESIGNATION'].'**'.$row['INSERT_DATE'];
		$dataArr[$key][$row['COMPONENT_ID']]=$row;
	}

	?>
		
		<table border="1" class="rpt_table" rules="all" width="99%" cellpadding="0" cellspacing="0" align="center">
			<tr>
				<thead>
					<th>Name</th>
					<th>Designation</th>
					<th>Date & Time</th>
					<th>Cost Component</th>
					<th>Comments</th>
				</thead>
			</tr>
			<?php
			$i=1;
			foreach($dataArr as $key=>$dataRows){
				list($row['USER_NAME'],$row['DESIGNATION'],$row['INSERT_DATE'])=explode('**',$key);
				$rowspan=count($dataRows);
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
			<tr bgcolor="<?=$bgcolor; ?>">
				<td rowspan="<?=$rowspan;?>"><?=$row['USER_NAME'];?></td>
				<td rowspan="<?=$rowspan;?>"><?=$designation_arr[$row['DESIGNATION']];?></td>
				<td rowspan="<?=$rowspan;?>"><?=$row['INSERT_DATE'];?></td>
				<? 
				$f=0;
				foreach($dataRows as $component_id=>$row){ 
				if($f==1){echo "<tr>";}
				?>
				<td><?=$cost_components[$component_id];?></td>
				<td><?=$row['REFUSING_REASON'];?></td>
			</tr>
			<?
				$f=1;
				$i++;
				}
			}
			?>
		</table>
	<?
	exit();
}


if($action=='deny_mail'){

	//include('../../includes/common.php');
	include('../../mailer/class.phpmailer.php');
	include('../../auto_mail/setting/mail_setting.php');


	$sql_user = "SELECT a.ID,a.USER_NAME,a.USER_EMAIL,b.CUSTOM_DESIGNATION FROM USER_PASSWD a,LIB_DESIGNATION b WHERE a.DESIGNATION=b.id and a.VALID=1";
	$sql_user_result = sql_select($sql_user);
	$user_data_arr = array();
	foreach ($sql_user_result as $rows) {
		$user_data_arr['NAME'][$rows['ID']]= $rows['USER_NAME'];
		$user_data_arr['DEG'][$rows['ID']]= $rows['CUSTOM_DESIGNATION'];
		$user_data_arr['MAIL'][$rows['ID']]= $rows['USER_EMAIL'];
	}


	$sql="select a.JOB_NO,a.ID AS PRECOST_ID,a.INSERTED_BY,b.UNAPPROVED_BY,b.UNAPPROVED_DATE,b.COST_COMPONENT_ID from WO_PRE_COST_MST a,PRECOST_COMPONENT_APP_MST b where a.job_id=b.job_id and b.APPROVED=5 and a.id in(".$data.")";
	$sqlRes = sql_select($sql);
	$deny_component_pre_cost_id_arr=array();
	$user_wise_data_arr=array();
	foreach($sqlRes as $row){
		$deny_component_pre_cost_id_arr[$row['PRECOST_ID']]=$row['PRECOST_ID'];
		$user_wise_data_arr[$row['INSERTED_BY']][]=$row;
	}


	$notAppCausSql = "select ID,ENTRY_FORM,MST_ID,COMPONENT_ID,REFUSING_REASON from PRECOST_COMPONENT_NOT_APP_CA where  MST_ID in(".implode(',',$deny_component_pre_cost_id_arr).") order by id asc";
	 //echo $notAppCausSql;die; 
	$notAppCausSqlRes = sql_select($notAppCausSql);
	$last_component_denay_message_arr=array();
	foreach($notAppCausSqlRes as $rows){
		$last_component_denay_message_arr[$rows['MST_ID']][$rows['COMPONENT_ID']]=$rows['REFUSING_REASON'];
	}

		//print_r($last_component_denay_message_arr);

	foreach($user_wise_data_arr as $insert_user=>$dataArr){
		ob_start();
		?>
		<table border="1" rules="all">
			<thead bgcolor="#ddd">
				<th>Job No.</th>	
				<th>Name</th>	
				<th>Designation</th>	
				<th>Date & Time</th>	
				<th>Cost Component</th>	
				<th>Comments</th>	
			</thead>
			<tbody>
			<?
			$i=1;
			foreach($dataArr as $row){
				$bgcolor=($i%2==0)?"#E9F3FF" :"#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" >
					<td><?=$row['JOB_NO'];?></td>
					<td><?=$user_data_arr['NAME'][$row['UNAPPROVED_BY']];?></td>
					<td><?=$user_data_arr['DEG'][$row['UNAPPROVED_BY']];?></td>
					<td><?=date('d-m-Y h:i:s a',strtotime($row['UNAPPROVED_DATE']));?></td>
					<td><?=$cost_components[$row['COST_COMPONENT_ID']];?></td>
					<td><?=$last_component_denay_message_arr[$row['PRECOST_ID']][$row['COST_COMPONENT_ID']];?></td>
				</tr>
				<?
				$i++;
			}
			?>
			</tbody>
		</table>

		<?
		$mail_body_html[$insert_user]=ob_get_contents();
		ob_clean();
		//echo $user_data_arr['MAIL'][$insert_user]."<br>".$mail_body_html[$insert_user];
		$to = $user_data_arr['MAIL'][$insert_user];
		$htmlBody = $mail_body_html[$insert_user];
		
		
		$subject="Component wise precost approval deny mail";
		$header=mailHeader();
		if($to!=""){echo sendMailMailer( $to, $subject, $htmlBody, $from_mail );}

	}

	
	exit();


}




if($action=="po_wise_comments")
{
	extract($_REQUEST);
	echo load_html_head_contents("Refusing Cause Info","../../", 1, 1, $unicode);
	$alter_user_id = str_replace("'","",$alter_user_id);
	$job_id = str_replace("'","",$job_id);
	 
	$app_user_id=(str_replace("'","",$alter_user_id)!='')?$alter_user_id:$_SESSION['logic_erp']['user_id'];
	
	$poSql = "select ID,PO_NUMBER,JOB_ID from WO_PO_BREAK_DOWN where job_id=$job_id and is_deleted=0 and status_active=1"; 
	$poSqlRes = sql_select($poSql);


	$commentsSql = "select ID ,MST_ID ,MST_DTLS_ID ,FORM_NAME,TYPE,COMMENTS,INSERTED_BY,INSERT_DATE  FROM  COMMON_COMMENTS_LIBRARY where MST_ID=$job_id and TYPE=1 and FORM_NAME='component_wise_precost_app' order by id desc";
	//echo $commentsSql;
	// and INSERTED_BY=$app_user_id
	$commentsSqlRes = sql_select($commentsSql);
	$commentsDataArr=array();
	foreach($commentsSqlRes as $commentsRow){
		$commentsDataArr[$commentsRow['INSERTED_BY']][$commentsRow['MST_DTLS_ID']] = $commentsRow['COMMENTS'];
	}
 
	
	?>
    <script>
 	var permission='<?= $permission; ?>';
 	//var total_comments='<?= count($commentsDataArr[$app_user_id]); ?>';

    function fnc_cause_info( operation )
	{
		var poCommentsArr = Array();var poCommentsIdArr = Array();var poIdArr = Array();

		if(operation == 4){
			var arr = $('input[name="txtcommentscheckbox[]"]').map(function () {
					if(this.checked == true){poIdArr.push(this.value);}
				}).get();
				var poIdList = poIdArr.join(',');
				$('#selected_po_id').val(poIdList);
				parent.emailwindow.hide();
			return;		
		}
		
		
		
		var arr = $('input[name="txtcomments[]"]').map(function () {
			let [idTxt,poId]=(this.id).split('_');
			poCommentsArr.push(this.id+'='+this.value);
			poIdArr.push(poId);
			poCommentsIdArr.push('txtcomments_mst_id_'+poId+'='+$('#txtcomments_mst_id_'+poId).val());
		}).get();

		var poCommentsStr = poCommentsArr.join('&');
		var poCommentsIdStr = poCommentsIdArr.join('&');
		var poIdList = poIdArr.join(',');

		// alert(poCommentsIdArrList);return;
		
		
  		if (poIdArr.length>0)
		{
			var data="action=save_update_delete_comments&operation="+operation+"&"+poCommentsStr+"&po_id_list="+poIdList+"&"+poCommentsIdStr+"&job_id="+<?= $job_id;?>+"&app_user_id="+<?= $app_user_id;?>;
			http.open("POST","component_wise_precosting_approval_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange =()=>{
				if(http.readyState == 4)
				{
					var response=trim(http.responseText).split('**');
					//alert(response[1]);
					//if(response[0]==2){parent.emailwindow.hide();}
					//set_button_status(1, permission, 'fnc_cause_info',1,0);
					reset_form('comments_1','','');
					var commentsHtml =return_global_ajax_value('<?= $job_id.'_'.$app_user_id;?>', 'user_comments_list', '', 'component_wise_precosting_approval_controller');
					$('#comments_list_container').html(commentsHtml);
				}
			}


		}
	}

	function fn_set_comments(str){
		let [ID ,MST_ID ,MST_DTLS_ID ,FORM_NAME,TYPE,COMMENTS,INSERTED_BY,INSERT_DATE]=str.split('###');
		$('#txtcomments_mst_id_'+MST_DTLS_ID).val(ID);
		$('#txtcomments_'+MST_DTLS_ID).val(COMMENTS);
		set_button_status(1, permission, 'fnc_cause_info',1,0);
	}

 

	
	

    </script>
    <body  onload="set_hotkey();">
    <form name="comments_1" id="comments_1"  autocomplete="off">
		<input type="hidden" id="selected_po_id" value="">
		<div align="center" style="width:100%;max-height:260px; overflow-y:scroll;">
		
			<table border="1" rules="all" cellpadding="0" cellspacing="0" width="100%" class="rpt_table">
				<thead>
					<th colspan="2">SL</th>
					<th>Po Number</th>
					<th>Comments</th>
				</thead>
				<tbody>
					<?php
					$i=1;
					foreach($poSqlRes as $rows){
						
						?>
						<tr>
							<td align="center" width="20"><?= $i;?></td>
							<td align="center" width="20"><input type="checkbox" name="txtcommentscheckbox[]" id="txtcommentscheckbox_<?= $rows['ID'];?>" value="<?= $rows['ID'];?>"></td>
							<td width="100"><?= $rows['PO_NUMBER'];?></td>
							<td align="center">
								<input type="hidden" id="txtcomments_mst_id_<?= $rows['ID'];?>" value="">
								<input type="text" name="txtcomments[]" id="txtcomments_<?= $rows['ID'];?>" style="width:95%;" class="text_boxes" value="" />
							</td>
						</tr>
						<?php
						$i++;
					}
					?>
				</tbody>
		   </table>
	
		</div>


		<table width="100%">
			<tr>
				<td colspan="5" align="center" class="button_container">
					
					<?
						$su_status=($notAppCausSqlRes[0]['ID'])?1:0;
						echo load_submit_buttons( $permission, "fnc_cause_info", $su_status,1 ,"reset_form('comments_1','','')",1);
					?>
				</td>
			</tr>
		</table>
	</form>

	<div id="comments_list_container"></div>
	

	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		// if(total_comments>0){
		// 	set_button_status(1, permission, 'fnc_cause_info',1,0);
		// }
		var commentsHtml =return_global_ajax_value('<?= $job_id.'_'.$app_user_id;?>', 'user_comments_list', '', 'component_wise_precosting_approval_controller');
		$('#comments_list_container').html(commentsHtml);
		$("#Print1").removeClass("formbutton_disabled").addClass("formbutton").attr('onclick',"fnc_cause_info(4)");

	</script>
	</body>
    <?
	exit();
}





if($action=="save_update_delete_comments")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $_REQUEST ));
	$po_id_list = str_replace("'","",$po_id_list);
	$po_comments_id_list = str_replace("'","",$po_comments_id_list);
	$app_user_id = str_replace("'","",$app_user_id);


	if ($operation==0)  // Insert Here
	{
		
		$id=return_next_id( "id", "COMMON_COMMENTS_LIBRARY", 1);
		$field_array = "ID ,MST_ID ,MST_DTLS_ID ,FORM_NAME,TYPE,COMMENTS,INSERTED_BY,INSERT_DATE";

		foreach(explode(',',$po_id_list) as $po_id){
			if(${"txtcomments_".$po_id}){
				if ($data_array != "") $data_array .= ",";
				$data_array .= "(".$id.",".$job_id.",".$po_id.",'component_wise_precost_app','1','".${"txtcomments_".$po_id}."',".$app_user_id.",'".$pc_date_time."')";
				$id++;	
			}   
		}
		///echo "insert into COMMON_COMMENTS_LIBRARY ($field_array) values $data_array";die;
		
		$con = connect();
		//$delete = execute_query("DELETE FROM  COMMON_COMMENTS_LIBRARY where MST_ID=$job_id and MST_DTLS_ID in($po_id_list) and TYPE=1 and INSERTED_BY=$app_user_id and FORM_NAME='component_wise_precost_app'" ,1);
		$rID=sql_insert("COMMON_COMMENTS_LIBRARY",$field_array,$data_array,1);
		//echo $rID;die;

		if($rID)
		{
			oci_commit($con);
			echo "0**Save Successfull";
		}
		else{
			oci_rollback($con);
			echo "10**Save Fail";
		}
	
		disconnect($con);
	}
	else if($operation==1){

		foreach(explode(',',$po_id_list) as $po_id){
			$component_mst_id = ${"txtcomments_mst_id_".$po_id};
			if(${"txtcomments_".$po_id} && $component_mst_id){
				$component_data_up[$component_mst_id] =explode(",",("'".${"txtcomments_".$po_id}."',".$approved_by.",'".$pc_date_time."'")); 
				$component_id_up[]=$component_mst_id;
			}
		}
		
		$con = connect();
		$component_field_up="COMMENTS*UPDATED_BY*UPDATE_DATE";	
		$updateSql = bulk_update_sql_statement( "COMMON_COMMENTS_LIBRARY", "id", $component_field_up, $component_data_up, $component_id_up );
		//echo $updateSql;die;
		$rID=execute_query($updateSql);

		if($rID)
		{
			oci_commit($con);
			echo "1**Update Success";
		}
		else{
			oci_rollback($con);
			echo "10**Update Fail";
		}
	
		disconnect($con);
	}
	else if($operation==2){
		

		$component_mst_id_arr = array();
		foreach(explode(',',$po_id_list) as $po_id){
			if(${"txtcomments_mst_id_".$po_id}){
				$component_mst_id_arr[] = ${"txtcomments_mst_id_".$po_id};
			}
		}
		$component_mst_id_str = implode(',',$component_mst_id_arr);
		$con = connect();
		$delete = execute_query("DELETE FROM  COMMON_COMMENTS_LIBRARY where MST_ID=$job_id and MST_DTLS_ID in($po_id_list) and id in($component_mst_id_str) and TYPE=1 and INSERTED_BY=$app_user_id and FORM_NAME='component_wise_precost_app'" ,1);

		if($delete)
		{
			oci_commit($con);
			echo "2**Delete Success";
		}
		else{
			oci_rollback($con);
			echo "10**Delete Fail";
		}

		disconnect($con);
		die;
	}

	exit();

}



if($action = "user_comments_list"){
	list($job_id,$app_user_id) = explode('_',$data);

	$poSql = "select ID,PO_NUMBER,JOB_ID from WO_PO_BREAK_DOWN where job_id=$job_id and is_deleted=0 and status_active=1"; 
	$poSqlRes = sql_select($poSql);
	$po_number_arr = array(); 
	foreach($poSqlRes as $rows){
		$po_number_arr[$rows['ID']] = $rows['PO_NUMBER'];
	}

	$user_lib = return_library_array("select ID,USER_FULL_NAME from USER_PASSWD", 'ID', 'USER_FULL_NAME');
	$commentsSql = "select ID ,MST_ID ,MST_DTLS_ID ,FORM_NAME,TYPE,COMMENTS,INSERTED_BY,INSERT_DATE  FROM  COMMON_COMMENTS_LIBRARY where MST_ID=$job_id and TYPE=1 and FORM_NAME='component_wise_precost_app' order by id desc,INSERTED_BY";
	//echo $commentsSql;die;
	$commentsSqlRes = sql_select($commentsSql);
	?>

	<table border="1" rules="all" cellpadding="0" cellspacing="0" width="100%" class="rpt_table">
		<thead>
			<th>User</th>
			<th>Date</th>
			<th>Po Number</th>
			<th>Comments</th>
		</thead>
		<?
		$i=1;
		foreach($commentsSqlRes as $commentsRow){ 
			if($app_user_id == $commentsRow['INSERTED_BY']){$fn = "fn_set_comments('". implode('###',$commentsRow)."')";}
			else{$fn = "alert('Please update your comments')";}
			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
		?>
		<tr onclick="<?= $fn;?>" style="cursor:pointer" bgcolor="<?=$bgcolor; ?>">
			<td><?= $user_lib[$commentsRow['INSERTED_BY']];?></td>
			<td><?= $commentsRow['INSERT_DATE'];?></td>
			<td><?= $po_number_arr[$commentsRow['MST_DTLS_ID']];?></td>
			<td><?= $commentsRow['COMMENTS'];?></td>
		</tr>
		<? 
		$i++;
		} 
		?>
	</table>

<?

	exit();
}


?>