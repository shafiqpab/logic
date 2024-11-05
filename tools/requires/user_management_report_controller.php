<?php
//--------------------------------------------------------------------------------------------------------------------
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header('location:login.php');
include('../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_level=$_SESSION['logic_erp']["user_level"];

$usersListArr = array();
$usersIdArr = array();
$usersNameArr = array();
$usersStatusArr = array();

if (!count($usersResult)) {
	$customDesignationsArr=return_library_array('select id, custom_designation from lib_designation where is_deleted=0 and status_active=1 order by custom_designation', 'id', 'custom_designation');	
	$departmentsArr=return_library_array('select id, department_name from lib_department where is_deleted=0 and status_active=1 order by department_name', 'id', 'department_name');
	$companyArr=return_library_array('select id, company_name from lib_company where is_deleted=0 and status_active=1', 'id', 'company_name');
	$buyerArr=return_library_array('select a.id, a.buyer_name from lib_buyer a where a.is_deleted=0 and a.status_active=1', 'id', 'buyer_name');
	// $statusArr = array(1=>'Active',2=>'Inactive');

	$usersResult = sql_select('select id, user_name, department_id, user_full_name, designation, valid, user_email, user_level, unit_id, buyer_id from user_passwd order by user_name');

	foreach($usersResult as $user) {
		$usersListArr[$user[csf('id')]]['id'] = $user[csf('id')];
		$usersListArr[$user[csf('id')]]['user_name'] = $user[csf('user_name')];
		$usersListArr[$user[csf('id')]]['department_id'] = $user[csf('department_id')];
		$usersListArr[$user[csf('id')]]['user_full_name'] = $user[csf('user_full_name')];
		$usersListArr[$user[csf('id')]]['designation'] = $user[csf('designation')];
		$usersListArr[$user[csf('id')]]['valid'] = $user[csf('valid')];
		$usersListArr[$user[csf('id')]]['user_email'] = $user[csf('user_email')];
		$usersListArr[$user[csf('id')]]['user_level'] = $user[csf('user_level')];
		$usersListArr[$user[csf('id')]]['unit_id'] = $user[csf('unit_id')];
		$usersListArr[$user[csf('id')]]['buyer_id'] = $user[csf('buyer_id')];

		$usersIdArr[$user[csf('id')]] = $user[csf('user_name')];
		$usersNameArr[$user[csf('id')]] = $user[csf('user_full_name')];
		$usersStatusArr[$user[csf('id')]] = $user[csf('valid')];
	}
}

//--------------------------------------------------------------------------------------------------------------------

if($action == 'load_drop_down_userid') {
	echo create_drop_down('cbo_id', 150, $usersIdArr, '', 1, '-- Select --', $selected, 'setValuesById(this.value);');
	exit();
}

if($action == 'load_drop_down_fullname') {
	echo create_drop_down('cbo_name', 150, $usersNameArr, '', 1, '-- Select --', $selected, 'setValuesById(this.value);');
	exit();
}

if($action == 'load_drop_down_department') {
	echo create_drop_down('cbo_department', 150, $departmentsArr, 'id,department_name', 1, '-- Select --', $selected, '');
	exit();
}

if($action == 'load_drop_down_designation') {
	echo create_drop_down('cbo_designation', 150, $customDesignationsArr, 'id,custom_designation', 1, '-- Select --', $selected, '');
	exit();
}

/*if($action == 'load_drop_down_status') {
	echo create_drop_down('cbo_id', 150, $usersIdArr, '', 1, '-- Select --', $selected, 'setValuesById(this.value);');
	exit();
}*/

if($action == 'generate_report') {
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$searchedUserArr = array();
	$result = array();

	if ($search_type == 2) {
		$userCond = '';
		$desigCond = '';
		$deptCond = '';
		$statusCond = '';
		if (str_replace("'", '', $userId)!=0) $userCond=" and id=$userId";
		if (str_replace("'", '', $designationId)!=0) $desigCond=" and designation=$designationId";
		if (str_replace("'", '', $departmentId)!=0) $deptCond=" and department_id=$departmentId";
		if (str_replace("'", '', $statusId)!=0) $statusCond=" and valid=$statusId";

		/*echo "select id, user_name, department_id, user_full_name, designation, valid, user_email, user_level, unit_id, buyer_id
			from user_passwd
			where 1=1 $userCond $desigCond $deptCond $statusCond
			order by valid";*/

		$usersResult = sql_select("select id, user_name, department_id, user_full_name, designation, valid, user_email, user_level, unit_id, buyer_id
			from user_passwd
			where 1=1 $userCond $desigCond $deptCond $statusCond
			order by user_name");

		/*select a.id, a.buyer_name, a.contact_person, a.buyer_email from lib_buyer a, lib_buyer_tag_company b where a.is_deleted=0 and a.status_active=1 and a.id=b.buyer_id and b.tag_company in($unit_name) group by a.id, a.buyer_name, a.contact_person, a.buyer_email  order by a.buyer_name*/

		foreach($usersResult as $user) {
			$searchedUserArr[$user[csf('id')]]['id'] = $user[csf('id')];
			$searchedUserArr[$user[csf('id')]]['user_name'] = $user[csf('user_name')];
			$searchedUserArr[$user[csf('id')]]['department_id'] = $user[csf('department_id')];
			$searchedUserArr[$user[csf('id')]]['user_full_name'] = $user[csf('user_full_name')];
			$searchedUserArr[$user[csf('id')]]['designation'] = $user[csf('designation')];
			$searchedUserArr[$user[csf('id')]]['valid'] = $user[csf('valid')];
			$searchedUserArr[$user[csf('id')]]['user_email'] = $user[csf('user_email')];
			$searchedUserArr[$user[csf('id')]]['user_level'] = $user[csf('user_level')];
			$searchedUserArr[$user[csf('id')]]['unit_id'] = $user[csf('unit_id')];
			$searchedUserArr[$user[csf('id')]]['buyer_id'] = $user[csf('buyer_id')];
/*
			$usersIdArr[$user[csf('id')]] = $user[csf('user_name')];
			$usersNameArr[$user[csf('id')]] = $user[csf('user_full_name')];
			$usersStatusArr[$user[csf('id')]] = $user[csf('valid')];*/
		}

		$result = $searchedUserArr;
	} else {
		$result = $usersListArr;
	}

	ob_start();
?>
<style>
	table tr td {
	    padding: 5px;
	    text-align: center;
	}
</style>
	<div class="btn-container" style="text-align: center; margin-bottom: 15px;">
		<a href="#" id="btnExcel" download><input type="button" value="Excel Preview" name="excel" class="formbutton" style="width:100px;"/></a>
		<a href="#" id="btnPrint" onclick="printPreview();"><input type="button" value="Print Preview" name="print" class="formbutton" style="width:100px"/></a>
		<h2 style="margin-top: 10px;">User List</h2>
	</div>
	<div id="report-area">
		<fieldset style="width: 95%;">
	        <!-- <legend>User List</legend> -->
	        <!-- <table class="rpt_table" id="list_view" rules="all" width="648" height="" cellspacing="0" > -->
	        <table border="0" class="rpt_table" rules="all" style="width: 100%;">
	        	<thead>
	        		<tr>
	        			<th width="4%">SL No</th>
	        			<th width="5%">User ID</th>
	        			<th width="10%">Full Name</th>
	        			<th width="10%">Email Id</th>
	        			<th width="8%">Designation</th>
	        			<th width="8%">Department</th>
	        			<th width="10%">User Level</th>
	        			<th width="17%">Unit Name</th>
	        			<th width="17%">Buyer Name</th>
	        			<th width="10%">Status</th>
	        		</tr>
	        	</thead>
	        </table>	        
	        <div id="userlist-area">
	        	<table border="0" class="rpt_table" id="tbl_users_list" rules="all" style="width: 100%;">
		        	<tbody id="report-container">
		        		<?php
		        			$sl=1;
		        			foreach ($result as $user) {
		        				if ($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		        				$companyStr = '';
		        				$buyerStr = '';
		        				$userCompArr = explode(',', $user['unit_id']);
		        				$userBuyerArr = explode(',', $user['buyer_id']);

		        				if (count($companyArr) == count($userCompArr)) {
		        					$companyStr = 'All';
		        				} else {
		        					foreach ($userCompArr as $compId) {
		        						$company = $companyArr[$compId] == '' ? '' : $companyArr[$compId].', ';
		            					$companyStr .= $company;
		            				}
		        				}

		        				if ($user['buyer_id'] == '') {
		        					$buyerStr = 'All';
		        				} else {
		            				foreach ($userBuyerArr as $buyerId) {
		            					$buyer = $buyerArr[$buyerId] == '' ? '' : $buyerArr[$buyerId].', ';
		            					$buyerStr .= $buyer;
		            				}
		            			}

		        				$companyStr = rtrim($companyStr, ' ,');
		        				$buyerStr = rtrim($buyerStr, ' ,');
		        		?>
		        			<tr bgcolor="<?php echo $bgcolor; ?>">
		        				<td width="4%" style="word-break: break-all;"><p><?php echo $sl; ?></p></td>
		        				<td width="5%" style="word-break: break-all;"><p><?php echo $user['user_name']; ?></p></td>
		        				<td width="10%" style="word-break: break-all;"><p><?php echo $user['user_full_name']; ?></p></td>
		        				<td width="10%" style="word-break: break-all;"><p><?php echo $user['user_email']; ?></p></td>
		        				<td width="8%" style="word-break: break-all;"><p><?php echo $customDesignationsArr[$user['designation']]; ?></p></td>
		        				<td width="8%" style="word-break: break-all;"><p><?php echo $departmentsArr[$user['department_id']]; ?></p></td>
		        				<td width="10%" style="word-break: break-all;"><p><?php echo $user_type[$user['user_level']]; ?></p></td>
		        				<td width="17%" style="word-break: break-all;"><p><?php echo $companyStr; ?></p></td>
		        				<td width="17%" style="word-break: break-all;"><p><?php echo $buyerStr; ?></p></td>
		        				<td width="10%" style="word-break: break-all;"><?php echo $row_status[$user['valid']]; ?></td>
		        			</tr>
		        		<?php
		        			$sl++;
		        			}
		        		?>
		        	</tbody>
		        </table>
		    </div>
		    </table>
	    </fieldset>
    </div>
<?php

$user_id = $_SESSION['logic_erp']['user_id'];

// first delete all the previous .xls
foreach (glob($user_id."_*.xls") as $file) {       
    @unlink($file);
}

$fileName=$user_id.'_'.time().'.xls';
$create_new_excel = fopen($fileName, 'w');
$is_created = fwrite($create_new_excel, ob_get_contents());

echo '####requires/'.$fileName;

echo '####'.json_encode($usersListArr);
ob_end_flush();	// exit and flush output buffer
exit();
}