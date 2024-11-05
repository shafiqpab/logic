<?php
/*--- ----------------------------------------- Comments
Purpose         :   User Management Report Page
Functionality   :   
JS Functions    :
Created by      :   Sapayth Hossain
Creation date   :   30-07-2020
Updated by      :
Update date     :
Oracle Convert  :       
Convert date    :      
QC Performed BY :       
QC Date         :   
Comments        :
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:../../login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents('User Management Report', '../', 1, 1, $unicode, 0, '');
// load_html_head_contents($title, $path, $filter, $popup, $unicode, $multi_select, $am_chart, $jqlatest)
?>

<script>
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = '../logout.php';
    var permission='<?php echo $permission; ?>';
    var usersListArr;
    var usersCount;

    window.onload = function() {
    	// freeze_window(5);
    	load_drop_down('requires/user_management_report_controller', '', 'load_drop_down_userid', 'userid_td' );
    	load_drop_down('requires/user_management_report_controller', '', 'load_drop_down_fullname', 'fullname_td' );
    	load_drop_down('requires/user_management_report_controller', '', 'load_drop_down_department', 'department_td' );
    	load_drop_down('requires/user_management_report_controller', '', 'load_drop_down_designation', 'designation_td' );
    	
    	/*var searchType = 1;
    	var data='action=generate_report&search_type='+searchType;
		http.open('POST', "requires/user_management_report_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = searchUserResponse;*/
    }

    function setValuesById(id) {
    	// console.log(id);
    	document.getElementById('cbo_id').value = id;
    	document.getElementById('cbo_name').value = id;
    	document.getElementById('cbo_designation').value = usersListArr[id].designation;
    	document.getElementById('cbo_department').value = usersListArr[id].department_id;
    	document.getElementById('cbo_status').value = usersListArr[id].valid;

    	// console.log(usersListArr);
    }

    function searchUser() {
    	var searchType = 2;
    	var id = document.getElementById('cbo_id').value;
    	var designationId = document.getElementById('cbo_designation').value;
    	var departmentId = document.getElementById('cbo_department').value;
    	var statusId = document.getElementById('cbo_status').value;

    	freeze_window(5);
		var data='action=generate_report&search_type='+searchType+"&userId="+id+'&designationId='+designationId+'&departmentId='+departmentId+'&statusId='+statusId;
		http.open('POST', "requires/user_management_report_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = searchUserResponse;
    }

    function searchUserResponse() {
    	if(http.readyState == 4) {
    		var response = trim(http.responseText);
	    	var result = response.split('####');
	    	document.getElementById('users-list-area').innerHTML = result[0];
	    	usersListArr = JSON.parse(result[2]);
	    	/*var tfConfig = {filters_row_index: 0,
						    // col_width: ['4%', '5%', '5%', '5%', '5%', '5%', '10%', '30%', '30%', null]
						   };*/
			/*var tf_tbl_users_list = {  
		        filters_row_index: 1,
		        // remember_grid_values: true
		    }; */
		    usersCount = document.getElementById('tbl_users_list').rows.length;
		    console.log(usersCount);
		    if(usersCount > 1) {
		    	setFilterGrid("tbl_users_list", -1);
		    }	    	

	    	if(result[1]) {
                document.getElementById('btnExcel').href = result[1];
            }
    	}
    	release_freezing();
    }

    function printPreview() {
        var w = window.open('', '_blank');
        var d = w.document.open();
        var styles = '<style> .heading-area {background:#E9F3FF; padding: 10px 0; border: 1px solid #E9F3EE; }';
        styles += ' #rpt_table thead { background: #8AABD7; } ';
        styles += ' tbody#report-container tr td { text-align: center; padding: 3px 1px; } ';
        styles += '</style>';
        var filterRow = document.getElementsByClassName('fltrow')[0];
        if(usersCount > 1) {
        	filterRow.style.display = 'none';
        }

        d.write ('<!DOCTYPE HTML>'+'<html><head><link rel="stylesheet" href="../../css/style_print.css" type="text/css" media="print" /><title>Print Preview</title>'+styles+'</head><body>'+document.getElementById('report-area').innerHTML+'</body</html>');
        d.close();
        if(usersCount > 1) {
        	filterRow.style.display = 'table-row';
        }
    }

</script>
</head>
<body>
    <div style="width:100%;" align="center">
        <?php echo load_freeze_divs ('../', $permission); ?>
        <form name="userSearch1" id="userSearch1" autocomplete="off"> 
            <fieldset style="width:65%;">
                <legend>Search Panel</legend>
                <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	                <table id="tbl_master" width="100%">
	                    <thead>
	                    	<tr>
	                    		<th width="15%">User ID</th>
			                    <th width="15%">Full Name</th>
			                    <th width="15%">Designation</th>
			                    <th width="15%">Department</th>
			                    <th width="15%">Status</th>
			                    <th>
			                    	<input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width: 90%;" />
			                    </th>
	                    	</tr>
	                    </thead>
	                    <tbody>
	                    	<tr>
		                    	<td id="userid_td">
		                    		<?php echo create_drop_down('cbo_id', 140, $usersIdArr, '', 1, '-- Select --', $selected, 'setValuesById(this.value);'); ?>
		                    	</td>
		                    	<td id="fullname_td">
		                    		<?php echo create_drop_down('cbo_name', 140, $usersNameArr, '', 1, '-- Select --', $selected, 'setValuesById(this.value);'); ?>
		                    	</td>
		                    	<td id="designation_td">
		                    		<?php echo create_drop_down('cbo_designation', 140, $customDesignationsArr, 'id,custom_designation', 1, '-- Select --', $selected, ''); ?>
		                    	</td>
		                    	<td id="department_td">
		                    		<?php echo create_drop_down('cbo_department', 140, $departmentsArr, 'id,department_name', 1, '-- Select --', $selected, ''); ?>
		                    	</td>
		                    	<td>
		                    		<?php echo create_drop_down('cbo_status', 140, $row_status, '', 1, '-- Select --', $selected, ''); ?>
		                    	</td>
		                    	<td style="text-align: center;">
		                    		<input type="button" name="btnSearchUser" class="formbutton" value="Show" onclick="searchUser();" style="width: 90%;" />
		                    	</td>
		                    </tr>
	                    </tbody>
	                </table>
                </form>
            </fieldset>
        </form>
    </div>
    <div id="users-list-area" style="margin-top: 20px;" align="center"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>