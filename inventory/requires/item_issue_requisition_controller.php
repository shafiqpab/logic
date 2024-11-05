<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
include('../../includes/common.php');
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
//====user credentials===
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id,company_location_id, item_cate_id, supplier_id FROM user_passwd where id=$user_id");
$cred_store_location_id = $userCredential[0][csf('store_location_id')];
$cred_location_id = $userCredential[0][csf('company_location_id')];
$cred_company_id = $userCredential[0][csf('company_id')];
$cred_item_cate_id = $userCredential[0][csf('item_cate_id')];

if ($cred_store_location_id != '') {$store_location_credential_cond = "and a.id in($cred_store_location_id)";} else { $store_location_credential_cond = "";}
if ($cred_company_id != '') {$scred_company_id_cond = "and comp.id in($cred_company_id)";} else { $scred_company_id_cond = "";}
if ($cred_item_cate_id != '') {$cred_item_cate_id_cond = "$cred_item_cate_id";} else { $cred_item_cate_id_cond = "";}
if ($cred_item_cate_id != '') {$cred_item_cate_id_cond_2 = "and a.item_category_id in($cred_item_cate_id)";} else { $cred_item_cate_id_cond_2 = "";}
if ($cred_location_id != '') {$location_credential_cond = "and id in($cred_location_id)";} else { $location_credential_cond = "";}


foreach($general_item_category as $item_id=>$item_name)
{
	$all_general_item_id.=$item_id.",";
}
$all_general_item_id=chop($all_general_item_id,",");
//echo $all_general_item_id;die;

$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
if ($action == "load_drop_down_location")
{
	echo create_drop_down("cbo_location_name", 145, "select id,location_name from lib_location where company_id=$data and is_deleted=0  and status_active=1 $location_credential_cond", "id,location_name", 1, "-- Select --", "", "load_drop_down( 'requires/item_issue_requisition_controller', this.value, 'load_drop_down_store','store_td');load_drop_down( 'requires/item_issue_requisition_controller', document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_floor','sewing_td');load_drop_down( 'requires/item_issue_requisition_controller',document.getElementById('cbo_company_id').value+'_'+this.value+'_'+document.getElementById('cbo_sewing_floor_name').value, 'load_drop_down_sewing_line','line_td');load_drop_down( 'requires/item_issue_requisition_controller',document.getElementById('cbo_company_id').value+'_'+this.value+'_'+document.getElementById('cbo_sewing_floor_name').value, 'load_drop_down_machine','machine_td');");
	exit();
}

if ($action=="load_drop_down_floor")
{
	$data_ref=explode("_",$data);
	$company_id=$data_ref[0];
	$location_id=$data_ref[1];

	echo create_drop_down( "cbo_sewing_floor_name", 145, "SELECT id,floor_name from lib_prod_floor where company_id=$company_id and production_process=5 and location_id=$location_id and status_active=1 and is_deleted=0 ","id,floor_name", 1, "-- Select Sewing Floor --", $selected, "load_drop_down( 'requires/item_issue_requisition_controller',document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_name').value+'_'+this.value, 'load_drop_down_sewing_line','line_td');load_drop_down( 'requires/item_issue_requisition_controller',document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_name').value+'_'+this.value, 'load_drop_down_machine','machine_td');" );

	exit();
}


if($action=="load_drop_down_sewing_line")
{
	$explode_data = explode("_",$data);
	$company = $explode_data[0];
	$location = $explode_data[1];
	$floor = $explode_data[2];

	$line_library=return_library_array( "select id,line_name from lib_sewing_line where company_name='$company' and floor_name='$floor' and location_name='$location' and status_active=1 and is_deleted=0 group by id, line_name", "id", "line_name"  );
	
	echo create_drop_down( "cbo_sewing_floor_line", 145,$line_library,"", 1, "--- Select Sewing Line ---", $selected, "",0,0 );
}

if ($action == "load_drop_down_machine")
{
	$data = explode("_", $data);
	$company_id = $data[0];
	$location_id = $data[1];
	$floor_id = $data[2];

	echo create_drop_down("cbo_machine_no", 145, "select id, machine_no as machine_name from lib_machine_name where category_id=8 and company_id=$company_id and location_id=$location_id and floor_id=$floor_id and status_active=1 and is_deleted=0 and is_locked=0 order by seq_no", "id,machine_name", 1, "-- Select Machine --", 0, "", "");
	exit();
}

if($action == "load_drop_down_machine_no")
{
    $data = explode("_", $data);
    $sql = "SELECT id,machine_no from lib_machine_name where category_id=$data[0] and is_deleted=0 and status_active=1 order by machine_no";
    $result = sql_select($sql);
    // echo count($result); die;
    $selected = 0;
    if (count($result)==1) {
        $selected = $result[0][csf('id')];
    }
    echo create_drop_down( "txt_machine_no_".$data[1], 90, $sql, "id,machine_no", 1, "-- Select --", $selected, "", 0, "", "", "", "", "", "", "txt_machine_no[]", "txt_machine_no_".$data[1] );
    die;
}


//load_drop_down( 'requires/item_issue_requisition_controller', this.value, 'load_drop_down_division','division_td');
if ($action == "load_drop_down_location_popup") 
{
	echo create_drop_down("cbo_location_name", 90, "select id,location_name from lib_location where company_id=$data and is_deleted=0  and status_active=1 $location_credential_cond", "id,location_name", 1, "-- Select --", $selected, "");
	exit();
}

if ($action == "load_drop_down_division") {

	$sql="select id,division_name from lib_division where company_id=$data and is_deleted=0  and status_active=1";
	echo create_drop_down("cbo_division_name", 145, $sql, "id,division_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/item_issue_requisition_controller', this.value, 'load_drop_down_department','department_td');");
	exit();
}

if ($action=="not_approve_cause_popup")
{
	$menu_id=$_SESSION['menu_id'];
	$user_id=$_SESSION['logic_erp']['user_id'];

	echo load_html_head_contents("Un Approval Request","../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
       
			
            <table align="center" cellspacing="0" width="380" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <thead>
                	<tr>
                		<th>Not Appv. Cause</th>
                	</tr>
                </thead>
                <tbody>
                	<tr>
                		<td><?php echo $data; ?></td>
                	</tr>
                </tbody>
            </table>

           
            
        </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}


if ($action == "load_drop_down_division_popup") 
{
	echo create_drop_down("cbo_division_name", 90, "select id,division_name from lib_division where company_id=$data and is_deleted=0  and status_active=1", "id,division_name", 1, "-- Select --", $selected, "load_drop_down( 'item_issue_requisition_controller', this.value, 'load_drop_down_department_popup','department_td');");
	exit();
}

if ($action=="load_drop_down_store_company")
{
 	echo create_drop_down( "cbo_store_name", 145, $blank_array,"", 1, "-- Select Store --", 0, "", 0);
	exit();
}

if ($action == "load_drop_down_store") 
{
	echo create_drop_down( "cbo_store_name", 145, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.location_id=$data and b.category_type not in(1,2,3,12,13,14,24,25,35,101) $store_location_credential_cond group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", "", "", "","");
	exit();
}


if ($action == "load_drop_down_department") {
	echo create_drop_down("cbo_department_name", 145, "select id,department_name from lib_department where division_id=$data and is_deleted=0 and status_active=1", "id,department_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/item_issue_requisition_controller', this.value, 'load_drop_down_section','section_td');");
	exit();
}


if ($action == "load_drop_down_department_popup") {
	echo create_drop_down("cbo_department_name", 90, "select id,department_name from lib_department where division_id=$data and is_deleted=0 and status_active=1", "id,department_name", 1, "-- Select --", $selected, "load_drop_down( 'item_issue_requisition_controller', this.value, 'load_drop_down_section_popup','section_td');");
	exit();
}


if ($action == "load_drop_down_section_popup") {
	echo create_drop_down("cbo_section_name", 90, "select id,section_name from lib_section where department_id=$data and is_deleted=0 and status_active=1", "id,section_name", 1, "-- Select --", $selected, "load_drop_down( 'item_issue_requisition_controller', this.value, 'load_drop_down_sub_section_popup','sub_section_td');");
	exit();
}

if ($action == "load_drop_down_sub_section_popup") {
	$array = array(0 => "None");
	echo create_drop_down("cbo_sub_section_name", 90, $array, "", 1, "-- Select --", 1);
	exit();
}


if ($action == "load_drop_down_section") {
	echo create_drop_down("cbo_section_name", 145, "select id,section_name from lib_section where department_id=$data and is_deleted=0 and status_active=1", "id,section_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/item_issue_requisition_controller', this.value, 'load_drop_down_sub_section','sub_section_td');");
	exit();
}

if ($action == "load_drop_down_sub_section") {
	//echo "jahid";die;
	//$array=array(1=>"None");
	echo create_drop_down("cbo_sub_section_name", 145, $array, "", 1, "-- Select --", 0);
	exit();
	//echo create_drop_down( "cbo_sub_section_name", 145,"select id,section_name from lib_section where department_id=$data and is_deleted=0 and status_active=1","id,section_name", 1, "-- Select --", $selected, "" );

}

if($action == "load_drop_down_group")
{
	echo create_drop_down( "txt_item_group", 130,"select a.item_name,a.id from lib_item_group a where a.item_category=$data and a.status_active=1 and a.is_deleted=0 group by a.item_name, a.id order by a.id","id,item_name", 1, "-- Select --", $selected, "" );
}

if ($action==='company_variable_setting_check')
{
	$variable_setting = return_field_value('user_given_code_status', 'variable_settings_inventory', "company_name=$data and variable_list=35", 'user_given_code_status');
	echo $variable_setting;
	exit();
}

if ($action == "save_update_delete_mst") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		$id = return_next_id("id", "inv_item_issue_requisition_mst", 1);

		if ($db_type == 0) $year_cond = "YEAR(insert_date)";
		else if ($db_type == 2) $year_cond = "to_char(insert_date,'YYYY')";
		else $year_cond = "";//defined Later
		// function return_mrr_number( $company, $location, $category, $year, $num_length, $main_query, $str_fld_name, $num_fld_name, $old_mrr_no )
		$new_item_req_system_id = explode("*", return_mrr_number(str_replace("'", "", $cbo_company_id), '', '', date("Y", time()), 5, "select itemissue_req_prefix, itemissue_req_prefix_num from inv_item_issue_requisition_mst where company_id=$cbo_company_id and $year_cond=" . date('Y', time()) . " order by id desc ", "itemissue_req_prefix", "itemissue_req_prefix_num"));

		$field_array = "id,itemissue_req_prefix,itemissue_req_prefix_num,itemissue_req_sys_id,company_id,indent_date,required_date,location_id,division_id,department_id,section_id,sub_section_id,delivery_point,remarks,manual_requisition_no,ready_to_approved,inserted_by,insert_date,store_id,sewing_floor,sewing_line,machine_no";
		$data_array = "(" . $id . ",'" . $new_item_req_system_id[1] . "'," . $new_item_req_system_id[2] . ",'" . $new_item_req_system_id[0] . "'," . $cbo_company_id . "," . $txt_indent_date . "," . $txt_required_date . "," . $cbo_location_name . "," . $cbo_division_name . "," . $cbo_department_name . "," . $cbo_section_name . "," . $cbo_sub_section_name . "," . $cbo_delivery_point . "," . $txt_remarks . "," . $txt_manual_requisition_no .",".$cbo_ready_to_approved. "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $cbo_store_name . "," .$cbo_sewing_floor_name.",".$cbo_sewing_floor_line.",".$cbo_machine_no.")";
		//echo "insert into com_item_issue_requisition_mst($field_array)values ".$data_array." ";die;
		$rID = sql_insert("inv_item_issue_requisition_mst", $field_array, $data_array, 1);

		if ($db_type == 0) 
		{
			if ($rID) 
			{
				mysql_query("COMMIT");
				echo "0**" . $id . "**" . $new_item_req_system_id[0];
			} 
			else 
			{
				mysql_query("ROLLBACK");
				echo "10**" . $id;
			}
		}

		if ($db_type == 2 || $db_type == 1) 
		{
			if ($rID) 
			{
				oci_commit($con);
				echo "0**" . $id . "**" . $new_item_req_system_id[0];
			} 
			else 
			{
				oci_rollback($con);
				echo "10**" . $id;
			}
		}
		disconnect($con);
		die;
	} 
	else if ($operation == 1)   // Update Here
	{

		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		/*#### Stop not eligible field from update operation start ####*/
		// company_id*location_id*
		// $cbo_company_id . "*" . $cbo_location_name . "*" . 
		/*#### Stop not eligible field from update operation end ####*/
				
		$field_array = "indent_date*store_id*location_id*required_date*division_id*department_id*section_id*sub_section_id*delivery_point*remarks*manual_requisition_no*ready_to_approved*sewing_floor*sewing_line*machine_no*updated_by*update_date*status_active*is_deleted";
		$data_array = "" . $txt_indent_date . "*" .$cbo_store_name. "*". $cbo_location_name ."*". $txt_required_date . "*" . $cbo_division_name . "*" . $cbo_department_name . "*" . $cbo_section_name . "*" . $cbo_sub_section_name . "*" . $cbo_delivery_point . "*" . $txt_remarks . "*" . $txt_manual_requisition_no . "*" . $cbo_ready_to_approved . "*" . $cbo_sewing_floor_name . "*" . $cbo_sewing_floor_line . "*" . $cbo_machine_no . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*1*0";

		$rID = sql_update("inv_item_issue_requisition_mst", $field_array, $data_array, "id", "" . $txt_system_id . "", 1);

		if ($db_type == 0) 
		{
			if ($rID) 
			{
				mysql_query("COMMIT");
				echo "1**" . $id . "**" . str_replace("'", '', $txt_indent_no);
			} 
			else 
			{
				mysql_query("ROLLBACK");
				echo "1**" . $id . "**" . str_replace("'", '', $txt_indent_no);
			}
		}
		if ($db_type == 2 || $db_type == 1) 
		{
			if ($rID) 
			{
				oci_commit($con);
				echo "1**" . $id . "**" . str_replace("'", '', $txt_indent_no);
			} 
			else 
			{
				oci_rollback($con);
				echo "1**" . $id . "**" . str_replace("'", '', $txt_indent_no);
			}
		}
		disconnect($con);
		die;
	} 
	else if ($operation == 2)   // Delete Here
	{

		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		$issue_check_sql=sql_select("select req_id from inv_issue_master where status_active=1 and is_deleted=0 and entry_form=21 and req_id=$txt_system_id");
		$issue_req_id=$issue_check_sql[0][csf("req_id")];
		if($issue_req_id)
		{
			echo "20**Issue Found, Delete Not Allow.";disconnect($con);die;
		}

		$field_array = "updated_by*update_date*status_active*is_deleted";
		$data_array = "" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*'0'*'1'";

		$rID = sql_delete("inv_item_issue_requisition_mst", $field_array, $data_array, "id", "" . $txt_system_id . "", 1);
		$rID_dtls = sql_delete("inv_itemissue_requisition_dtls", $field_array, $data_array, "mst_id", $txt_system_id, 1);
		if ($db_type == 0) 
		{
			if ($rID && $rID_dtls) 
			{
				mysql_query("COMMIT");
				echo "2**" . $id . "**" . str_replace("'", '', $txt_indent_no);
			} 
			else 
			{
				mysql_query("ROLLBACK");
				echo "6**" . $id . "**" . str_replace("'", '', $txt_indent_no);
			}
		}
		if ($db_type == 2 || $db_type == 1) 
		{
			if ($rID && $rID_dtls) 
			{
				oci_commit($con);
				echo "2**" . $id . "**" . str_replace("'", '', $txt_indent_no);
			} 
			else 
			{
				oci_rollback($con);
				echo "10**" . $rID;
			}
		}
		disconnect($con);
		die;
	}

}

if ($action == "save_update_delete_dtls") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$dtls_id = return_next_id("id", "inv_itemissue_requisition_dtls", 1);
		//echo "10**$dtls_id";die;
		//cbo_company_id*txt_indent_date*txt_required_date*cbo_delivery_point*txt_remarks 
		$field_array = "id,mst_id,item_account,item_group,item_sub_group,item_description,item_size,unit_of_measure,current_stock,req_for,req_qty,rtn_qnty,remarks,product_id,machine_category_id,machine_no,inserted_by,insert_date";
		for ($j = 1; $j <= $row_num; $j++) 
		{
			$txt_item_account = "txt_item_account_" . $j;
			$txt_item_group = "hiddenitemgroupid_" . $j;
			$txt_item_sub = "txt_item_sub_" . $j;
			$txt_item_description = "txt_item_description_" . $j;
			$txt_item_size = "txt_item_size_" . $j;
			$txt_required_for = "txt_required_for_" . $j;
			$cbo_uom = "hiddentxtuom_" . $j;
			$txt_req_qty = "txt_req_qty_" . $j;
			$txt_rtn_qty = "txt_rtn_qty_" . $j;
			$txt_stock = "txt_stock_" . $j;
			$txt_remarks = "txt_remarks_" . $j;
			$txt_product_id = "txt_product_id_" . $j;
			$machineCategory = "txt_machine_category_" . $j;
			$machineNo = "txt_machine_no_" . $j;

			$product_id = str_replace("'", '', $txt_product_id);
			//echo "10**".str_replace("'", '', $txt_product_id);die; ," . $$txt_rtn_qty . "
			if (str_replace("'", '', $$txt_req_qty) > 0) 
			{
				if ($data_array != "") $data_array .= ",";
				$data_array .= "(" . $dtls_id . "," . $txt_system_id . "," . $$txt_item_account . "," . $$txt_item_group . "," . $$txt_item_sub . "," . $$txt_item_description . "," . $$txt_item_size . "," . $$cbo_uom . "," . $$txt_stock . "," . $$txt_required_for . "," . $$txt_req_qty . "," . $$txt_rtn_qty . "," . $$txt_remarks . "," . $$product_id . "," . $$machineCategory . "," . $$machineNo . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				$dtls_id++;
			}
		}
		//echo "10**$row_num";die;
		//echo "10**".$con;
		// echo  $stid= "insert into inv_itemissue_requisition_dtls ($field_array) values ".$data_array; 
		// die;
		//$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
		//echo "10**".$exestd;die;
		//print_r($data_array); die();
		$rID = sql_insert("inv_itemissue_requisition_dtls", $field_array, $data_array, 1);
		//echo "10**".$rID;die;
		
		if ($db_type == 0) 
		{
			if ($rID) 
			{
				mysql_query("COMMIT");
				echo "0**" . str_replace("'", '', $txt_system_id);
			} 
			else 
			{
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", '', $txt_system_id);
			}
		}

		if ($db_type == 2 || $db_type == 1) 
		{
			if ($rID) 
			{
				oci_commit($con);
				echo "0**" . str_replace("'", '', $txt_system_id);
			} 
			else 
			{
				oci_rollback($con);
				echo "10**" . str_replace("'", '', $txt_system_id);
			}
		}
		disconnect($con);
		die;
	} 
	else if ($operation == 1)   // Update Here
	{

		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		
		$issue_check_sql=sql_select("select sum(b.cons_quantity) as cons_quantity from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.transaction_type=2 and a.entry_form=21 and a.req_id=$txt_system_id and b.prod_id=$txt_product_id_1");
		$issue_qnty=$issue_check_sql[0][csf("cons_quantity")]*1;
		$req_qnty=str_replace("'","",$txt_req_qty_1)*1;
		//echo "10**$issue_qnty=$req_qnty";die;
		if($req_qnty < $issue_qnty)
		{
			echo "20**Requisition Quantity Not Allow Less Then Issue Quantity.";disconnect($con);die;
		}

		$field_array = "req_for*req_qty*rtn_qnty*machine_category_id*machine_no*remarks*updated_by*update_date*status_active*is_deleted";
		$txt_required_for = "txt_required_for_1";
		$txt_req_qty = "txt_req_qty_1";
		$txt_rtn_qty = "txt_rtn_qty_1";
		$txt_remarks = "txt_remarks_1";
		$txt_machine_cat = "txt_machine_category_1";
		$txt_machine_no = "txt_machine_no_1";
		if (str_replace("'", '', $$txt_req_qty) > 0) {
			$data_array = "" . $$txt_required_for . "*" . $$txt_req_qty . "*" . $$txt_rtn_qty . "*" . $$txt_machine_cat . "*" . $$txt_machine_no . "*" . $$txt_remarks . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*1*0";
		}
		//echo "10**".$data_array;die;
		$rID = sql_update("inv_itemissue_requisition_dtls", $field_array, $data_array, "id", "" . $update_id_dtls . "", 1);
		//echo "10**".$rID;die;
		if ($db_type == 0) 
		{
			if ($rID) 
			{
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", '', $txt_system_id);
			} 
			else 
			{
				mysql_query("ROLLBACK");
				echo "1**" . "**" . str_replace("'", '', $txt_system_id);
			}
		}
		if ($db_type == 2 || $db_type == 1) 
		{
			if ($rID) 
			{
				oci_commit($con);
				echo "1**" . str_replace("'", '', $txt_system_id);
			} 
			else 
			{
				oci_rollback($con);
				echo "1**" . str_replace("'", '', $txt_system_id);
			}
		}
		disconnect($con);
		die;
	} 
	else if ($operation == 2)   // Delete Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		$issue_check_sql=sql_select("select b.id from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.transaction_type=2 and a.entry_form=21 and a.req_id=$txt_system_id and b.prod_id=$txt_product_id_1");
		$issue_req_id=$issue_check_sql[0][csf("id")];
		if($issue_req_id)
		{
			echo "20**Issue Found, Delete Not Allow.";disconnect($con);die;
		}
		$field_array = "updated_by*update_date*status_active*is_deleted";
		$data_array = "" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*'0'*'1'";

		$rID = sql_delete("inv_itemissue_requisition_dtls", $field_array, $data_array, "id", "" . $update_id_dtls . "", 1);

		if ($db_type == 0) 
		{
			if ($rID) 
			{
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", '', $txt_system_id);
			} 
			else 
			{
				mysql_query("ROLLBACK");
				echo "6**" . str_replace("'", '', $txt_system_id);
			}
		}
		if ($db_type == 2 || $db_type == 1) 
		{
			if ($rID) 
			{
				oci_commit($con);
				echo "2**" . str_replace("'", '', $txt_system_id);
			} 
			else 
			{
				oci_rollback($con);
				echo "10**" . str_replace("'", '', $txt_system_id);
			}
		}
		disconnect($con);
		die;
	}
}



if ($action == "item_issue_requisition_popup_search") 
{
	echo load_html_head_contents("Item Issue Requisition search From", "../../", 1, 1, '', '1', '');
	extract($_REQUEST);

	?>
    <script>

        function js_set_value(id) {
			// alert(id); return;
            $('#hidden_item_issue_id').val(id);
            parent.emailwindow.hide();
        }

        function item_issue_requisition_popup() {

            if (form_validation('cbo_company_id', 'Company') == false) {
                alert('Please, Select Company.');
                return;
            }

			var indent_no = document.getElementById('txt_system_id').value;
			var date_from = document.getElementById('txt_date_from').value;
			var date_to = document.getElementById('txt_date_to').value;
			
			if (indent_no == "" && date_from == "" && date_to == "") {
				alert('Please, Give Indent No or Indent Date Range.');
				return;
			}


            show_list_view(document.getElementById('cbo_company_id').value + '**' + document.getElementById('txt_date_from').value  + '**' + document.getElementById('txt_date_to').value + '**' + document.getElementById('txt_required_date').value + '**' + document.getElementById('cbo_location_name').value + '**' + document.getElementById('cbo_division_name').value + '**' + document.getElementById('cbo_department_name').value + '**' + document.getElementById('cbo_section_name').value + '**' + document.getElementById('cbo_sub_section_name').value + '**' + document.getElementById('cbo_delivery_point').value + '**' + document.getElementById('txt_system_id').value+ '**' + document.getElementById('cbo_year_selection').value, 'items_search_list_view', 'search_div', 'item_issue_requisition_controller', 'setFilterGrid(\'list_view\',-1)');
        }
        function fnc_sub_section() {
            $('#cbo_sub_section_name').css('display', 'none');
        }
    </script>
    </head>
    <body>
    <div align="center" style="width:1230px;">
        <form name="searchitemreqfrm" id="searchitemreqfrm">
            <fieldset style="width:1230px; margin-left:3px">
                <legend>Search</legend>
                <table cellpadding="0" cellspacing="0" width="1130" class="rpt_table" rules="all">
                    <thead>
                    <th class="must_entry_caption">Company</th>
                    <th>Indent No.</th>
                    <th width="160">Indent Date Range</th>
                    <th align="right">Required Date</th>
                    <th align="right">Location</th>
                    <th align="right">Division</th>
                    <th align="right">Department</th>
                    <th align="right">Section</th>
                    <th align="right" style="display:none;">Sub Section</th>
                    <th align="right">Delivery Point</th>
                    <th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton"/><input type="hidden" name="id_field" id="id_field" value=""/></th>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
							<?
							$company = "select comp.id,comp.company_name from lib_company comp where  comp.status_active=1 and comp.is_deleted=0 $scred_company_id_cond order by company_name";
							echo create_drop_down("cbo_company_id", 144, $company, "id,company_name", 1, "-- Select --",$cbo_company_name, "load_drop_down( 'item_issue_requisition_controller', this.value, 'load_drop_down_location_popup','location_td');load_drop_down( 'item_issue_requisition_controller', this.value, 'load_drop_down_division_popup','division_td');");

							?>
                        </td>
                        <td><input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes"  style="width:70px"></td>
                        <!-- <td><input type="text" name="txt_indent_date" id="txt_indent_date" class="datepicker" style="width:70px"></td> -->
						<td align="center" >
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px"> To
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px">
	                    </td> 
                        <td><input type="text" name="txt_required_date" id="txt_required_date" class="datepicker" style="width:70px" readonly></td>
                        <td id="location_td">
							<?php
							echo create_drop_down("cbo_location_name", 90, $blank_array, "", 1, "-- Select --", 0, "");
							?>
                        </td>
                        <td id="division_td" width="90">
							<?php
							echo create_drop_down("cbo_division_name", 90, $blank_array, "", 1, "-- Select --");
							?>
                        </td>
                        <td width="70" id="department_td">
							<?php
							echo create_drop_down("cbo_department_name", 90, $blank_array, "", 1, "-- Select --");
							?>
                        </td>
                        <td id="section_td" width="132">
							<?
							echo create_drop_down("cbo_section_name", 90, $blank_array, "", 1, "-- Select --", '');
							?>
                        </td>
                        <td id="sub_section_td" width="90" style="display:none;">
							<?php
							echo create_drop_down("cbo_sub_section_name", 90, $blank_array, "", 1, "-- Select --");
							?>
                        </td>
                        <td><input type="text" name="cbo_delivery_point" id="cbo_delivery_point" style="width:90px"
                                   class="text_boxes"></td>
                        <td><input type="hidden" id="hidden_item_issue_id"/>
                            <input type="button" id="search_button" class="formbutton" value="Show" onClick="item_issue_requisition_popup()" style="width:100px;"/>
                        </td>
                    </tr>
                    </tbody>
					<tfoot>
	                <tr>
	                    <td align="center" height="25" valign="middle" colspan="11" style="background-image: -moz-linear-gradient(bottom, rgb(136,170,283) 7%, rgb(194,220,255) 10%, rgb(136,170,283) 96%);">
	                    <? echo load_month_buttons(1);  ?>
	                    </td>
	                </tr>
	            </tfoot> 
                </table>
                <div style="width:100%; margin-top:10px;" id="search_div" align="center"></div>
            </fieldset>
        </form>
    </div>
    </body>
    <script type="text/javascript">
    	$("#cbo_company_id").val(0);
		$('#cbo_location_name').val(0);
		$('#cbo_division_name').val(0);
		$('#cbo_department_name').val(0);
		$('#cbo_section_name').val(0);
		$('#cbo_sub_section_name').val(0);
	</script>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>

	<?
	exit();

}

if ($action == "items_search_list_view") 
{
	$data = explode('**', $data);
	$delivery = $data[9];
	$indent_no = trim($data[10]);
	$cbo_year = trim($data[11]);
	//var_dump($data);die;
	if ($data[0] != 0) 
	{
		$company_id = " and company_id = $data[0]";
	} 
	else 
	{
		echo "Select Company";
		die;
	}
	
	$location_id=$division_id =$department_id =$section_id =$sub_section_id =$delivery_id =$ind_id ="";
	if ($data[4] != 0) $location_id = " and location_id = $data[4]"; 
	if ($data[5] != 0) $division_id = " and division_id = $data[5]";
	if ($data[6] != 0) $department_id = " and department_id = $data[6]";
	if ($data[7] != 0) $section_id = " and section_id = $data[7]";
	if ($data[8] != 0) $sub_section_id = " and sub_section_id = $data[8]";
	if ($data[9] != '') $delivery_id = " and delivery_point like '$delivery%'";
	if ($data[10] != '') $ind_id = " and itemissue_req_sys_id like '%$indent_no'";	
	
	//$date=change_date_format($data[1],'mm-dd-yyyy');
	//if($data[1]!=0){ $indent_date=" and indent_date = $data[1]";}else{ $indent_date=""; }
	$section_library = return_library_array("select id, section_name from lib_section", "id", "section_name");
	$department = return_library_array("select id, department_name from lib_department", "id", "department_name");
	$location = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$division = return_library_array("select id, division_name from lib_division", "id", "division_name");
	$section_library = return_library_array("select id, section_name from lib_section", "id", "section_name");
	$user_library = return_library_array("select id, user_name from user_passwd", "id", "user_name");

	// $date = $data[1];
	$re_date = $data[3];
	$indent_date = $require_date = "";
	if ($data[1] != "" && $data[2] != "")
	{
		if ($db_type == 0) 
		{
			$indent_date = "and indent_date between '" . change_date_format($data[1], 'yyyy-mm-dd') . "' and '" . change_date_format($data[2], 'yyyy-mm-dd') . "'";
		} 
		else if ($db_type == 2) 
		{
			$indent_date = "and indent_date between '" . change_date_format($data[1], '', '', 1) . "' and '" . change_date_format($data[2], '', '', 1) . "'";
		}
	}

	if ($data[3] != "") 
	{
		if ($db_type == 0) 
		{
			$require_date = "and required_date ='" . change_date_format($re_date, 'yyyy-mm-dd') . "'";
		} 
		else if ($db_type == 2) 
		{
			$require_date = "and required_date ='" . change_date_format($re_date, '', '', 1) . "'";
		}
	}

	$year_field=$year_cond="";
	if ($db_type == 0) 
	{
		$year_field = "YEAR(insert_date) as year";
		$year_cond=" and year(insert_date)=".trim($cbo_year);
	}
	else if ($db_type == 2)
	{
		$year_field = "to_char(insert_date,'YYYY') as year";
		$year_cond=" and to_char(insert_date,'YYYY')=".trim($cbo_year);
	}

	$user_cond="";
	if ($user_lavel!=2) $user_cond= " and inserted_by=$user_id";
	$is_approved_arr=array(0=>'No', 1=>'Yes', 2=>'No', 3=>'Partial Approved');

	$sql = "select id,$year_field,itemissue_req_prefix,itemissue_req_prefix_num,itemissue_req_sys_id,company_id,indent_date,required_date,location_id,division_id,department_id,section_id,sub_section_id,delivery_point,remarks,inserted_by,ready_to_approved,is_approved from inv_item_issue_requisition_mst where status_active=1 and is_deleted=0 $company_id $indent_date $require_date $location_id $division_id $department_id $section_id $sub_section_id $delivery_id $ind_id $user_cond $year_cond order by id desc";
	$sql_res=sql_select($sql);
	//echo  $sql; die;
	?>
	<div style="width: 1200px;">
	<table width="1200" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" align="left">
        <thead>
			<tr>
				<th colspan="14"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
			</tr>
			<tr>
				<th width="50">SL</th>
				<th width="150">Company</th>
				<th width="50">Year</th>
				<th width="80">Indent No.</th>
				<th width="80">Indent Date</th>
				<th width="80">Required Date</th>
				<th width="100">Location</th>
				<th width="80">Division</th>
				<th width="80">Department</th>
				<th width="80">Section</th>
				<th width="80">Insert By</th>
				<th width="80">Ready to approve</th>
				<th width="80">Approval Status</th>
				<th>Delivery Point</th>
			</tr>
        </thead>
     </table>
     <div style="width:1200px; overflow-y:scroll; max-height:250px">
     	<table width="1180" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="list_view">
			<?
			$i = 1;
            foreach($sql_res as $row)
            {
                if($i%2==0) $bgcolor="#E9F3FF"; 
                else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value(<? echo $row[csf('id')]; ?>);">
                	<td width="50"><? echo $i; ?></td>
		            <td width="150"><? echo $company_arr[$row[csf("company_id")]]; ?></td>
		            <td width="50"><? echo $row[csf("year")]; ?></td>
		            <td width="80"><? echo $row[csf("itemissue_req_prefix_num")]; ?></td>
		            <td width="80"><? echo change_date_format($row[csf("indent_date")]); ?></td>
		            <td width="80"><? echo change_date_format($row[csf("required_date")]); ?></td>
		            <td width="100"><? echo $location[$row[csf("location_id")]]; ?></td>
		            <td width="80"><? echo $division[$row[csf("division_id")]]; ?></td>
		            <td width="80"><? echo $department[$row[csf("department_id")]]; ?></td>
		            <td width="80"><? echo $section_library[$row[csf("section_id")]]; ?></td>
		            <td width="80"><? echo $user_library[$row[csf("inserted_by")]]; ?></td>
		            <td width="80"><? echo $yes_no[$row[csf("ready_to_approved")]]; ?></td>
		            <td width="80"><? echo $is_approved_arr[$row[csf("is_approved")]]; ?></td>
		            <td><? echo $row[csf("delivery_point")]; ?></td>
				</tr>
            	<?
				$i++;
            }
			?>
		</table>
    </div>
    </div>
    <?
    exit();	
}


if ($action == "populate_data_from_item_issue_requisition") 
{
//	echo "select id,itemissue_req_prefix,itemissue_req_prefix_num,itemissue_req_sys_id,company_id,indent_date,required_date,location_id,division_id,department_id,section_id,sub_section_id,delivery_point,remarks,manual_requisition_no,ready_to_approved,is_approved, store_id from inv_item_issue_requisition_mst where status_active=1 and is_deleted=0 and id='$data'";



	$data_array = sql_select("SELECT id,itemissue_req_prefix,itemissue_req_prefix_num,itemissue_req_sys_id,company_id,indent_date,required_date,location_id,division_id,department_id,section_id,sub_section_id,delivery_point,remarks,manual_requisition_no,ready_to_approved,is_approved,store_id,nvl(sewing_floor,0) as sewing_floor,nvl(sewing_line,0) as sewing_line,nvl(machine_no,0) as machine_no from inv_item_issue_requisition_mst where status_active=1 and is_deleted=0 and id='$data'");

	foreach ($data_array as $row) 
	{
		// $app_cause1=return_field_value("approval_cause", "fabric_booking_approval_cause", "booking_id='".$row[csf("id")]."' and status_active=1 and is_deleted=0 and entry_form=26");
		$app_cause1 = return_field_value("refusing_reason", "refusing_cause_history", "mst_id='".$row[csf("id")]."'");

		echo "load_drop_down( 'requires/item_issue_requisition_controller', '" . $row[csf("company_id")] . "', 'load_drop_down_location', 'location_td' );\n";
		echo "load_drop_down( 'requires/item_issue_requisition_controller', '" . $row[csf("company_id")] . "', 'load_drop_down_division', 'division_td' );\n";
		echo "load_drop_down( 'requires/item_issue_requisition_controller', '" . $row[csf("division_id")] . "', 'load_drop_down_department', 'department_td' );\n";
		echo "load_drop_down( 'requires/item_issue_requisition_controller', '" . $row[csf("department_id")] . "', 'load_drop_down_section', 'section_td' );\n";
		
		echo "load_drop_down( 'requires/item_issue_requisition_controller', '" . $row[csf("location_id")] . "', 'load_drop_down_store', 'store_td' );\n";

		echo "load_drop_down('requires/item_issue_requisition_controller','".$row[csf("company_id")]."'+'_'+'".$row[csf("location_id")]."','load_drop_down_floor','sewing_td');\n";

		echo "load_drop_down('requires/item_issue_requisition_controller','".$row[csf("company_id")]."'+'_'+'".$row[csf("location_id")]."'+'_'+'".$row[csf("sewing_floor")]."','load_drop_down_sewing_line','line_td');\n";

		echo "load_drop_down('requires/item_issue_requisition_controller','".$row[csf("company_id")]."'+'_'+'".$row[csf("location_id")]."'+'_'+'".$row[csf("sewing_floor")]."','load_drop_down_machine','machine_td');\n";
		
		//echo "load_drop_down( 'requires/item_issue_requisition_controller', this.value, 'load_drop_down_store','store_td');\n";
		//echo "load_drop_down( 'requires/item_issue_requisition_controller', '" . $row[csf("section_id")] . "', 'load_drop_down_sub_section', 'sub_section_td' );\n";
		echo "document.getElementById('txt_indent_no').value = '" . $row[csf("itemissue_req_sys_id")] . "';\n";
		echo "document.getElementById('txt_not_approve_cause').value = '" .$app_cause1 . "';\n";
		echo "document.getElementById('cbo_company_id').value = '" . $row[csf("company_id")] . "';\n";
		echo "$('#cbo_company_id').attr('disabled','true')" . ";\n";
		echo "document.getElementById('txt_indent_date').value = '" . change_date_format($row[csf("indent_date")]) . "';\n";
		echo "document.getElementById('txt_required_date').value = '" . change_date_format($row[csf("required_date")]) . "';\n";
		echo "document.getElementById('cbo_location_name').value = '" . $row[csf("location_id")] . "';\n";
		echo "document.getElementById('cbo_sewing_floor_name').value = '" . $row[csf("sewing_floor")] . "';\n";
		echo "document.getElementById('cbo_sewing_floor_line').value = '" . $row[csf("sewing_line")] . "';\n";
		echo "document.getElementById('cbo_machine_no').value = '" . $row[csf("machine_no")] . "';\n";
		echo "$('#cbo_location_name').attr('disabled','true')" . ";\n";
		echo "document.getElementById('cbo_division_name').value = '" . $row[csf("division_id")] . "';\n";
		echo "document.getElementById('cbo_department_name').value = '" . $row[csf("department_id")] . "';\n";
		echo "document.getElementById('cbo_section_name').value = '" . $row[csf("section_id")] . "';\n";
		echo "document.getElementById('cbo_sub_section_name').value = '" . $row[csf("sub_section_id")] . "';\n";
		echo "document.getElementById('cbo_delivery_point').value = '" . $row[csf("delivery_point")] . "';\n";
		echo "document.getElementById('txt_remarks').value = '" . $row[csf("remarks")] . "';\n";
		echo "document.getElementById('txt_manual_requisition_no').value = '" . $row[csf("manual_requisition_no")] . "';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '" . $row[csf("ready_to_approved")] . "';\n";
		echo "document.getElementById('txt_is_approved').value = '" . $row[csf("is_approved")] . "';\n";
		
		
		
		echo "document.getElementById('cbo_store_name').value = '" . $row[csf("store_id")] . "';\n";
		echo "$('#cbo_store_name').attr('disabled','true')" . ";\n";
		echo "$('#approval_status_tr').html('');\n";
		if($row[csf("is_approved")] == 1)
		{
		echo "$('#approval_status_tr').html('This Requisition is Approved by Authority.');\n";
		echo "$('#itemissuerequisition_1 input, #itemissuerequisition_1 select').prop('disabled','true')".";\n";
		echo "$('#itemissuerequisition_2 input, #itemissuerequisition_2 select').prop('disabled','true')".";\n";
		echo "$('#txt_indent_no').removeAttr('disabled')" . ";\n";
		echo "$('#Refresh1').removeAttr('disabled')" . ";\n";
		// echo "$('#Print1').removeAttr('disabled')" . ";\n";
		echo "$('#btn_Print').removeAttr('disabled')" . ";\n";
		echo "$('#btn_Print2').removeAttr('disabled')" . ";\n";
		echo "$('#btn_Print3').removeAttr('disabled')" . ";\n";
		echo "$('#btn_Print4').removeAttr('disabled')" . ";\n";
		echo "$('#update1').removeClass('formbutton').addClass('formbutton_disabled');\n";  
		echo "$('#Delete1').removeClass('formbutton').addClass('formbutton_disabled');\n"; 
		}
		if($row[csf("is_approved")] == 3)
		{
			echo "$('#approval_status_tr').html('Partial Approved.');\n";
		}
		echo "document.getElementById('txt_system_id').value = '" . $row[csf("id")] . "';\n";
		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_item_issue_requisition_mst',1);\n";
	}

	exit();
}


if ($action == "item_account_popup") 
{
	echo load_html_head_contents("Item Details Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
    <script>
        var selected_id = new Array, selected_name = new Array();
        selected_attach_id = new Array();

        function check_all_data() {
            var tbl_row_count = document.getElementById('tbl_list').rows.length;

            tbl_row_count = tbl_row_count - 1;
            //alert(tbl_row_count);
            for (var i = 1; i <= tbl_row_count; i++) {
                eval($('#tr_' + i).attr("onclick"));
            }
        }

        function toggle(x, origColor) {
            var newColor = 'yellow';
            if (x.style) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
            }
        }

        function js_set_value(id) {
        	// alert(id);
            var str = id.split("_");
            toggle(document.getElementById('tr_' + str[0]), '#FFFFFF');
            str = str[1];
            if (jQuery.inArray(str, selected_id) == -1) {
                selected_id.push(str);
            }
            else {
                for (var i = 0; i < selected_id.length; i++) {
                    if (selected_id[i] == str) break;
                }
                selected_id.splice(i, 1);
            }
            var id = '';
            for (var i = 0; i < selected_id.length; i++) {
                id += selected_id[i] + ',';
            }
            id = id.substr(0, id.length - 1);

            $('#txt_selected_id').val(id);
        }

        

        
    </script>
    </head>
    <body>
    <div align="center">
        <form name="item_detailsfrm" id="item_detailsfrm">
            <fieldset style="width:710px;">
                <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
                       class="rpt_table" id="tbl_list_search">
                    <thead>
                    <th>Item Category</th>
                    <th>Item Group</th>
					<th>Item Number</th>
                    <th>Item Code</th>
                    <th>Item Description</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"
                               onClick="reset_form('item_detailsfrm','search_div','','','','');"></th>
                    </thead>
                    <tbody>
                    <tr>

                        <td align="center">
                            <!--<input type="text" style="width:130px" class="text_boxes" name="txt_item_category" id="txt_item_category" />-->
							<?php
							echo create_drop_down("cbo_item_category_id", 160, $item_category, "", 1, "-- Select --", $selected, "load_drop_down( 'item_issue_requisition_controller', this.value, 'load_drop_down_group','group_td');", "", $cred_item_cate_id_cond, "", "", "1,2,3,12,13,14,24,25,101");
							//not seleted 1,2,3,12,13,14,24,25
							?>
                        </td>
                        <td align="center" id="group_td">
                            <?
                    			echo create_drop_down("txt_item_group",130,$blank_array,"",1,"-- Select --",$selected, "" );
                    		?> 
                    	</td>
						<td align="center"><input type="text" style="width:90px" class="text_boxes" name="txt_item_number" id="txt_item_number" /> </td>
						<td align="center"><input type="text" style="width:130px" class="text_boxes" name="txt_item_code" id="txt_item_code"/></td>
                        <td align="center"><input type="text" style="width:130px" class="text_boxes" name="txt_item_description" id="txt_item_description"/></td>
                        <td align="center"><input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_item_category_id').value+'**'+document.getElementById('txt_item_group').value+'**'+document.getElementById('txt_item_description').value+'**'+document.getElementById('txt_item_code').value+'**'+<? echo $cbo_company_name; ?>+'**'+<? echo $cbo_store_name; ?>+'**'+'<? echo $hidden_variable_setting; ?>'+'**'+'<? echo $prev_product_id; ?>'+'**'+document.getElementById('txt_item_number').value, 'item_account_popup_list_view', 'search_div', 'item_issue_requisition_controller', 'setFilterGrid(\'tbl_list\',-1,\'tableFilters\')');" style="width:100px;"/>
                            <input type="hidden" name="txt_tot_row" id="txt_tot_row"/>
                            <input type="hidden" name="txt_selected_id" id="txt_selected_id" value=""/></td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
            <div style="margin-top:15px" id="search_div"></div>
        </form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    
    </html>
	<?
	exit();
}

if ($action == "item_account_popup_list_view") 
{
	echo load_html_head_contents("Item Creation popup", "../../", 1, 1,'','1','');
	$data = explode('**', $data);
	$group = trim($data[1]);
	$description = trim($data[2]);
	$code = trim($data[3]);
	$item_number = trim($data[8]);
	$company = $data[4];
	$store_name = trim($data[5]);
	$variable_stock_val = str_replace("'","",$data[6]) ;
	$prev_product_id = str_replace("'","",$data[7]) ;
	if($store_name==0 || $store_name =="") $store_name=0;
	//echo $prev_product_id.'**';
	$item_category_id = $item_group = $item_description = "";
	if ($data[0] != 0) $item_category_id = " and a.item_category_id='$data[0]'";
	if ($data[1] != 0) $item_group = " and a.item_group_id ='$group'";
	//if ($data[2] != "") $item_description = " and a.item_description like '%$description%' ";
	if ($data[2] != "") $item_description = " and upper(a.item_description) LIKE upper('%$description%')";
	if ($data[3] != "") $item_code = " and upper(a.item_code) LIKE upper('%$code%')";
	if ($data[8]!=""){$item_number = " and a.item_number like('%$item_number%')";}
	if ($prev_product_id != "") $prev_product = " and a.id not in($prev_product_id)";
	
	if($variable_stock_val==1)
	{
		$sql = "select a.id, a.item_account, a.item_category_id, a.item_description, a.item_code, a.item_size, a.item_group_id, a.unit_of_measure, a.current_stock, a.re_order_label, a.status_active, b.item_name, sum((case when c.transaction_type in(1,4,5) then c.cons_quantity else 0 end)-(case when c.transaction_type in(2,3,6) then c.cons_quantity else 0 end)) as balance_qnty,a.item_number,a.SUB_GROUP_NAME
		from lib_item_group b, product_details_master a left join inv_transaction c on c.prod_id=a.id and c.store_id=$store_name and c.status_active=1 and c.is_deleted=0  
		where a.item_group_id=b.id and a.company_id=$company and a.status_active in(1,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form<>24 and a.item_category_id not in(1,2,3,12,13,14,24,25,101) $item_category_id $item_group $item_description $item_code $item_number $cred_item_cate_id_cond_2 $prev_product
		group by a.id, a.item_account, a.item_category_id, a.item_description,a.item_code, a.item_size, a.item_group_id, a.unit_of_measure, a.current_stock, a.re_order_label, a.status_active, b.item_name,a.item_number,a.SUB_GROUP_NAME";
	}
	else
	{
		$sql = "select a.id, a.item_account, a.item_category_id, a.item_description,a.item_code, a.item_size, a.item_group_id, a.unit_of_measure, a.current_stock, a.re_order_label, a.status_active, b.item_name, sum((case when c.transaction_type in(1,4,5) then c.cons_quantity else 0 end)-(case when c.transaction_type in(2,3,6) then c.cons_quantity else 0 end)) as balance_qnty,a.item_number,a.SUB_GROUP_NAME
		from lib_item_group b, product_details_master a, inv_transaction c 
		where a.item_group_id=b.id and c.prod_id=a.id and c.store_id=$store_name and c.status_active=1 and c.is_deleted=0 and a.company_id=$company and a.status_active in(1,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id not in(1,2,3,12,13,14,24,25,101) and a.entry_form<>24 $item_category_id $item_group $item_description $item_code $item_number $cred_item_cate_id_cond_2 $prev_product
		group by a.id, a.item_account, a.item_category_id, a.item_description,a.item_code, a.item_size, a.item_group_id, a.unit_of_measure, a.current_stock, a.re_order_label, a.status_active, b.item_name,a.item_number,a.SUB_GROUP_NAME
		having sum((case when c.transaction_type in(1,4,5) then c.cons_quantity else 0 end)-(case when c.transaction_type in(2,3,6) then c.cons_quantity else 0 end))>0";
	}
	
	//echo $sql;
	$sql_res=sql_select($sql);
	//print_r($sql_res);

	//$style="";
	$tableWith=0;
    if($user_lavel==2 || $user_lavel==1)
	{
		//$style="";
		$tableWith=1020;
		$colspan=13;
	}
	else{
		//$style="style='display:none;visibility: hidden;'";
		$tableWith=940;
		$colspan=12;
	}
				
	?>
    <div style="width: 1050px;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?echo $tableWith; ?>" class="rpt_table">
            <thead>
				<tr>
					<th colspan="<?=$colspan;?>"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
				</tr>
				<tr>
					<th width="30">SL</th>
					<th width="90">Item Account</th>
					<th width="90">Item Code</th>
					<th width="90">Item Category</th>
					<th width="60">Item Number</th>
					<th width="70">Sub Group Name</th>
					<th width="130">Item Description</th>
					<th width="80">Item Size</th>
					<th width="100">Item Group</th>
					<th width="60">UOM</th>
					<?
					$status = sql_select("select ready_to_approve,id from variable_settings_inventory where company_name = $company and variable_list =51 and is_deleted = 0 and status_active = 1");					 
					$select_status =  $status[0][csf('ready_to_approve')];
					if(($user_lavel==2 || $user_lavel==1) && $select_status==1 )
					{
					?>
					<th width="80">Stock</th>
					<?
					}
					?>                
					<th width="70">ReOrder Level</th>
					<th>Product ID</th>
				</tr>
                
            </thead>
     	</table>
    </div>
    <div style="width:1050px; max-height:270px;overflow-y:scroll;" >
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?echo $tableWith; ?>" class="rpt_table" id="tbl_list">
			<?
			$i=1;
		    foreach($sql_res as $prod_id=>$val )
		    {
		        if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $i.'_'.$val[csf("id")]; ?>');" >
                    <td width="30"><?php echo $i; ?></td>
                    <td width="90"><p><?php echo $val[csf("item_account")]; ?></p></td>
                    <td width="90"><p><?php echo $val[csf("item_code")]; ?></p></td>
                    <td width="90"><?php echo $item_category[$val[csf("item_category_id")]]; ?></td>
                    <td width="60"><?php echo $val[csf("item_number")]; ?></td> 
                    <td width="70"><?php echo $val[csf("SUB_GROUP_NAME")]; ?></td> 
                    <td width="130"><?php echo $val[csf("item_description")]; ?></td>
                    <td width="80"><?php echo $val[csf("item_size")]; ?></td>
                    <td width="100"><?php echo $val[csf("item_name")]; ?></td>
                    <td width="60"><?php echo $unit_of_measurement[$val[csf("unit_of_measure")]]; ?></td>
                    <?
					$status = sql_select("select ready_to_approve,id from variable_settings_inventory where company_name = $company and variable_list =51 and is_deleted = 0 and status_active = 1");					 
					$select_status =  $status[0][csf('ready_to_approve')];
                     if(($user_lavel==2 || $user_lavel==1) && $select_status==1)
					{
					?>
                    <td width="80" align="right"><?php echo number_format($val[csf("balance_qnty")],2); ?></td>
               		<? } ?>                    
                    <td width="70" align="right"><?php echo $val[csf("re_order_label")]; ?></td>
                    <td align="center"><?php echo $val[csf("id")]; ?></td>
                </tr>
                <?
                $i++;
		    }
			?>
		</table>
	</div>
	<table width="890" cellspacing="0" cellpadding="0" style="border:none" align="center">
        <tr>
            <td align="center" height="30" valign="bottom">
                <div style="width:100%">
                    <div style="width:50%; float:left" align="left">
                        <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                    </div>
                    <div style="width:50%; float:left" align="left">
                        <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
 
	<?
	exit(); 
}

if ($action == "stock_popup") 
{
	extract($_REQUEST);

	$sql = "select store_id, sum(case when transaction_type in (1,4,5)  then cons_quantity else 0 end) as total_receive,sum(case when transaction_type in (2,3,6)  then cons_quantity else 0 end) as total_issue  from inv_transaction where prod_id='$product_id' and company_id='$cbo_company_name' and status_active=1 and is_deleted=0 group by store_id";
	$store = return_library_array("select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type in ($all_general_item_id) and a.status_active=1 and a.is_deleted=0 group by a.id,a.store_name order by a.store_name", 'id', 'store_name');

	?>
    <table width="250" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
        <thead>
        <tr>
            <th width="150">Store Name</th>
            <th align="center">Stock</th>
        </tr>
        </thead>
        <tbody>
		<? $result = sql_select($sql);
		$i = 1;
		foreach ($result as $row) {
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
			$stock = $row[csf('total_receive')] - $row[csf('total_issue')];
			?>
            <tr bgcolor="<? echo $bgcolor; ?>" id="">
                <td align="right"><? echo $store[$row[csf('store_id')]]; ?></td>
                <td align="right"><? echo $stock; ?></td>
            </tr>

			<?
			$total_stock += $stock;
			$i++;
		}
		?>
        <tr bgcolor="#FFCC66">
            <td align="right">Total</td>
            <td align="right"><? echo $total_stock; ?></td>
        </tr>
        </tbody>
    </table>
	<? 
}

if ($action == "item_issue_requisition_list") {
	$explode_data = explode("**", $data);
	$data = $explode_data[0];
	$table_row = $explode_data[1];
	$store_name = $explode_data[2];
	$company = $explode_data[3];
	$item_group = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	$store_item_stock = return_library_array("select a.id, sum((case when p.transaction_type in(1,4,5) then p.cons_quantity else 0 end)-(case when p.transaction_type in(2,3,6) then p.cons_quantity else 0 end)) as bal_qnty
		from inv_transaction p, product_details_master a
		where p.prod_id=a.id and a.id in ($data) and p.store_id=$store_name and a.status_active in(1,3) and p.status_active=1 and p.is_deleted=0
		group by  a.id", 'id', 'bal_qnty');
		
		
	if ($data != "") {
		$nameArray = sql_select("select a.id, a.item_account, a.sub_group_name, a.item_category_id, a.item_description, a.item_size, a.item_group_id, a.unit_of_measure, a.current_stock, a.re_order_label, a.status_active, b.item_name
		from product_details_master a, lib_item_group b 
		where a.item_group_id=b.id and a.id in ($data) and a.status_active in(1,3)");
		//print_r($nameArray);
		foreach ($nameArray as $inf) {
			$table_row++;
			?>
            <tr class="general" id="tr_<? echo $table_row; ?>">
                <td>
                    <input type="text" name="txt_item_account_<? echo $table_row; ?>" id="txt_item_account_<? echo $table_row; ?>" placeholder="browse" class="text_boxes" onClick="fnc_item_account(<? echo $table_row; ?>)" value="<? echo $inf[csf("item_account")]; ?>" style="width:75px;" readonly>
                    <input type="hidden" name="txt_product_id_<? echo $table_row; ?>" id="txt_product_id_<? echo $table_row; ?>" placeholder="browse" class="text_boxes" value="<? echo $inf[csf("id")]; ?>" style="width:75px;" readonly>
                </td>
                <td>
                	<?
                    echo create_drop_down( "txt_item_category_1", 90,$item_category,"", 1, "-- Select --", $inf[csf("item_category_id")], "",1,"","","","1,2,3,12,13,14,24,25,101");
                    ?>
                </td>
                <td>
                    <input type="text" name="txt_item_group_<? echo $table_row; ?>" id="txt_item_group_<? echo $table_row; ?>" class="text_boxes" value="<? echo $inf[csf("item_name")]; ?>" style="width:75px;" disabled>
                    <input type="hidden" name="hiddenitemgroupid_<? echo $table_row; ?>" id="hiddenitemgroupid_<? echo $table_row; ?>" class="text_boxes" value="<? echo $inf[csf("item_group_id")]; ?>" style="width:75px;" maxlength="200"/>
                </td>
                <td>
                    <input type="text" name="txt_item_sub_<? echo $table_row; ?>" id="txt_item_sub_<? echo $table_row; ?>" class="text_boxes" value="<? echo $inf[csf("sub_group_name")]; ?>" style="width:75px;" disabled>
                </td>
                <td><input type="text" name="txt_item_description_<? echo $table_row; ?>" id="txt_item_description_<? echo $table_row; ?>" class="text_boxes" value="<? echo $inf[csf("item_description")]; ?>" style="width:190px" disabled>
                </td>

				 <td align="center">
                    <? echo create_drop_down( "txt_machine_category_".$table_row, 90, $machine_category,"", 1, "--Select--", $selected, "load_drop_down( 'requires/item_issue_requisition_controller', this.value+'_'+$table_row, 'load_drop_down_machine_no','machine_no_td_".$table_row."' );",0, "", "", "", "", "", "", "txt_machine_category[]", "txt_machine_category_".$table_row ); ?>
                </td>
                <td align="center" id="machine_no_td_<?= $table_row; ?>">
                    <?
                        echo create_drop_down( "txt_machine_no_".$table_row, 90, $blank_array, "", 1, "-- Select --", $selected, "", 0, "", "", "", "", "", "", "txt_machine_no[]", "txt_machine_no_".$table_row );
                    ?>
                </td>


                <td><input type="text" name="txt_item_size_<? echo $table_row; ?>" id="txt_item_size_<? echo $table_row; ?>" class="text_boxes" value="<? echo $inf[csf("item_size")]; ?>" style="width:75px" disabled></td>
                <td><input type="text" name="txt_required_for_<? echo $table_row; ?>" id="txt_required_for_<? echo $table_row; ?>" class="text_boxes" placeholder="Write" style="width:60px;"></td>
                <td align="right">
                	<input type="text" name="txt_uom_<? echo $table_row; ?>" id="txt_uom_<? echo $table_row; ?>" class="text_boxes_numeric" style=" width:40px" value="<? echo $unit_of_measurement[$inf[csf("unit_of_measure")]]; ?>" readonly>
                    <input type="hidden" name="hiddentxtuom_<? echo $table_row; ?>" id="hiddentxtuom_<? echo $table_row; ?>" class="text_boxes" value="<? echo $inf[csf("unit_of_measure")]; ?>" style="width:60px;" maxlength="200" readonly/>
                </td>
                <td><input type="text" name="txt_req_qty_<? echo $table_row; ?>" id="txt_req_qty_<? echo $table_row; ?>" class="text_boxes_numeric" placeholder="Write" style="width:60px;"></td>
                <td><input type="text" name="txt_rtn_qty_<? echo $table_row; ?>" id="txt_rtn_qty_<? echo $table_row; ?>" class="text_boxes_numeric" placeholder="Write" style="width:60px;"></td>
                <?
				$status = sql_select("select ready_to_approve,id from variable_settings_inventory where company_name = $company and variable_list =51 and is_deleted = 0 and status_active = 1");				 
				$select_status =  $status[0][csf('ready_to_approve')];
                if(($user_lavel==2 || $user_lavel==1))
				{
				?>
                <td title="<? if($select_status==1){echo $store_item_stock[$inf[csf("id")]];} else{echo "See Lib Setting Plz. ";} ?>"><input type="text" name="txt_stock_<? echo $table_row; ?>" id="txt_stock_<? echo $table_row; ?>" class="text_boxes_numeric" value="<? if($select_status==1){echo $store_item_stock[$inf[csf("id")]];}?>" style="width:40px;" onDblClick="trans_history_popup(<? if($select_status==1){echo $inf[csf("id")];} ?>)" readonly></td>
                <?php 
            	}
                else
				{
				?>
				<td><input type="hidden" name="txt_stock_<? echo $table_row; ?>" id="txt_stock_<? echo $table_row; ?>" class="text_boxes_numeric" value="<? echo $store_item_stock[$inf[csf("id")]]; ?>" onDblClick="trans_history_popup(<? echo $inf[csf("id")]; ?>)" readonly></td>
				<?php } ?>
                <td>
                	<input type="text" name="txt_remarks_<? echo $table_row; ?>" id="txt_remarks_<? echo $table_row; ?>" class="text_boxes" style="width:80px;" placeholder="Write">
                    <input type="hidden" id="hidden_selectedID"/>
                    <input type="hidden" name="txt_tot_row" id="txt_tot_row"/>
                    <input type="hidden" id="update_id_dtls" name="update_id_dtls"/>
                </td>
            </tr>
			<?

		}//end foreach

	}
}

if ($action == "show_item_issue_listview") {
	$sql = "select b.id, b.mst_id, b.item_account ,b.item_group, b.item_sub_group, b.item_description, b.item_size, b.unit_of_measure, b.current_stock, b.req_for, b.req_qty, b.rtn_qnty, b.remarks, b.product_id ,b.machine_category_id,b.machine_no, a.company_id
	from inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b 
	where a.id=b.mst_id and a.id='$data' and b.status_active=1 and b.is_deleted=0 order by id Asc";
	$nameArray = sql_select($sql);
	$company=$nameArray[0]['COMPANY_ID'];
	?>
    <div style="width:1300px;">
        <table width="1280" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table" align="left" id="item_issue_listview_dtls">
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="110">Item Account</th>
                     <th width="120">Item Category</th>
                    <th width="110">Item Group</th>
                    <th width="70">Item Sub. Group</th>
                    <th width="200">Item Description</th>
                    <th width="100">Machine Category</th>
                    <th width="90">Machine No</th>
                    <th width="60">Item Size</th>
                    <th width="60">Required For</th>
                    <th width="50">UOM</th>
                    <th width="50">Req. Qty.</th>
                    <th width="50">Rtn. Qty.</th>
                    <?		
					$status = sql_select("select ready_to_approve,id from variable_settings_inventory where company_name = $company and variable_list =51 and is_deleted = 0 and status_active = 1");
					 
					$select_status =  $status[0][csf('ready_to_approve')];
					$update_id =  $status[0][csf('id')];			 
	                if(($user_lavel==2 || $user_lavel==1) && $select_status==1)
					{
					?>
	                <th width="55">Stock</th>
	                <?
	            	}
	            	?>                    
                    <th>Remarksdd</th>
                </tr>
            </thead>
            <tbody>
			<?
			$con = connect();
			$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (54)");
			oci_commit($con);

			$prod_id_arr = array();
			foreach ($nameArray as $selectResult) 
			{
				$prod_id_arr[$selectResult[csf('product_id')]] = $selectResult[csf('product_id')];
			}
			if (count($prod_id_arr)>0)
			{
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 54, 1, $prod_id_arr, $empty_arr);
				$prodDataArr=sql_select("SELECT A.ID, A.ITEM_CATEGORY_ID
				FROM PRODUCT_DETAILS_MASTER A, GBL_TEMP_ENGINE B WHERE A.ID=B.REF_VAL AND B.USER_ID= $user_id AND B.ENTRY_FORM=54 AND B.REF_FROM=1");

				foreach($prodDataArr as $row)
				{
					$product_arr[$row['ID']]['category_name']=$row['ITEM_CATEGORY_ID'];
				}
				unset($prodDataArr);
			}

			execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (54)");
			oci_commit($con);
			disconnect($con);

			$item_group = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
			//$item_sub_group = return_library_array("select sub_group_code,sub_group_name from product_details_master", 'sub_group_code', 'sub_group_name');

			//$item_category_arr = return_library_array("select id,item_category_id from product_details_master", 'id', 'item_category_id');


			$machine_no_arr = return_library_array("select id,machine_no from lib_machine_name", 'id', 'machine_no');
			$i = 1;
			foreach ($nameArray as $selectResult) 
			{
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="get_php_form_data('<? echo $selectResult[csf('id')]; ?>','populate_item_details_form_data_dtls','requires/item_issue_requisition_controller');populate_row_dte()">
                    <td><? echo $i; ?><input type="hidden" name="view_product_id_<?=$i?>" id="view_product_id_<?=$i?>" value="<? echo $selectResult[csf("product_id")]; ?>" class="text_boxes" style="width:75px;" readonly ></td>
                    <td><? echo $selectResult[csf("item_account")]; ?></td>
                    <td><? 
					//echo $item_category[$item_category_arr[$selectResult[csf("product_id")]]];
					echo $item_category[$product_arr[$selectResult[csf("product_id")]]['category_name']]; ?></td>
                    <td><? echo $item_group[$selectResult[csf("item_group")]]; ?></td>
                    <td><? echo $selectResult[csf("item_sub_group")]; ?></td>
                    <td><? echo $selectResult[csf("item_description")]; ?></td>
                    <td><? echo $machine_category[$selectResult[csf("machine_category_id")]]; ?></td>
                    <td><? echo $machine_no_arr[$selectResult[csf("machine_no")]]; ?></td>
                    <td><? echo $selectResult[csf("item_size")]; ?></td>
                    <td align="right"><? echo $selectResult[csf("req_for")]; ?></td>
                    <td align="right"><? echo $unit_of_measurement[$selectResult[csf("unit_of_measure")]]; ?></td>
                    <td align="right"><? echo $selectResult[csf("req_qty")]; ?></td>
                    <td align="right"><? echo $selectResult[csf("rtn_qnty")]; ?></td>
                    <?
					 $status = sql_select("select ready_to_approve,id from variable_settings_inventory where company_name = $company and variable_list =51 and is_deleted = 0 and status_active = 1");
					 
					 $select_status =  $status[0][csf('ready_to_approve')];
	                if(($user_lavel==2 || $user_lavel==1) && $select_status==1)
					{
					?>
                    <td align="right"><? echo number_format($selectResult[csf("current_stock")],4); ?></td>
                    <?
	            	}
	            	?>
                    <td align="right"><? echo $selectResult[csf("remarks")]; ?></td>
                </tr>
				<? $i++;
			} 
			?>
            </tbody>
        </table>
    </div>
	<?
}


if ($action == "populate_item_details_form_data_dtls") {
	$item_group = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
	//$item_sub_group = return_library_array("select sub_group_code,sub_group_name from product_details_master", 'sub_group_code', 'sub_group_name');
	$item_category_arr = return_library_array("select id,item_category_id from product_details_master", 'id', 'item_category_id');
	$data_ar = sql_select(" select id, mst_id, item_account, item_group, item_sub_group, item_description, item_size, unit_of_measure, current_stock, req_for, req_qty, rtn_qnty, remarks, product_id,machine_category_id,machine_no  from inv_itemissue_requisition_dtls where id='$data'");
	$table_row =1;

	$mst_id=$data_ar[0]["MST_ID"];
	$com_sql=sql_select("select company_id from inv_item_issue_requisition_mst where id=$mst_id");
	$company_id=$com_sql[0]["COMPANY_ID"];
	$status = sql_select("select ready_to_approve,id from variable_settings_inventory where company_name = $company_id and variable_list =51 and is_deleted = 0 and status_active = 1");		 
	$select_status =  $status[0][csf('ready_to_approve')];

	foreach ($data_ar as $info) {

		echo "document.getElementById('txt_item_account_1').value 		= '" . $info[csf("item_account")] . "';\n";
		echo "$('#txt_item_account_1').attr('disabled',true);\n";
		echo "document.getElementById('txt_product_id_1').value 		= '" . $info[csf("product_id")] . "';\n";
		echo "document.getElementById('txt_item_category_1').value 		= '" . $item_category_arr[$info[csf("product_id")]] . "';\n";
		echo "document.getElementById('txt_item_group_1').value 		= '" . $item_group[$info[csf("item_group")]] . "';\n";
		echo "document.getElementById('hiddenitemgroupid_1').value 		= '" . $info[csf("item_group")] . "';\n";
		echo "document.getElementById('txt_item_sub_1').value 			= '" . $info[csf("item_sub_group")] . "';\n";
		echo "document.getElementById('txt_item_description_1').value 	= '" . $info[csf("item_description")] . "';\n";
		echo "document.getElementById('txt_item_size_1').value 			= '" . $info[csf("item_size")] . "';\n";
		echo "document.getElementById('txt_uom_1').value 				= '" . $unit_of_measurement[$info[csf("unit_of_measure")]] . "';\n";
		echo "document.getElementById('hiddentxtuom_1').value 			= '" . $info[csf("unit_of_measure")] . "';\n";
		if($select_status==1){
			echo "document.getElementById('txt_stock_1').value 				= '" . number_format($info[csf("current_stock")],4) . "';\n";
		}
		echo "document.getElementById('txt_required_for_1').value 		= '" . $info[csf("req_for")] . "';\n";
		echo "document.getElementById('txt_req_qty_1').value 			= '" . $info[csf("req_qty")] . "';\n";
		echo "document.getElementById('txt_rtn_qty_1').value 			= '" . $info[csf("rtn_qnty")] . "';\n";
		echo "document.getElementById('txt_remarks_1').value 			= '" . $info[csf("remarks")] . "';\n";
		echo "document.getElementById('txt_machine_category_1').value 			= '" . $info[csf("machine_category_id")] . "';\n";
	    echo "load_drop_down('requires/item_issue_requisition_controller', ". $info[csf("machine_category_id")] ." + '_' + ". $table_row .", 'load_drop_down_machine_no', 'machine_no_td_".$table_row."');\n";
		echo "document.getElementById('txt_machine_no_1').value 			= '" . $info[csf("machine_no")] . "';\n";
		echo "document.getElementById('update_id_dtls').value 			= '" . $info[csf("id")] . "';\n";
		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_item_issue_requisition_dtls',2);\n";
	}

	exit();
}

if ($action == "print_item_issue_requisition") 
{
	extract($_REQUEST);
	echo load_html_head_contents("Item Issue requisition Print", "../../", 1, 1, '', '', '');
	$data = explode('*', $data);
	// print_r($data);die;
	//$data[3]
	//select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and b.category_type not in(1,2,3,12,13,14,24,25,35) group by a.id,a.store_name order by a.store_name","id,store_name

	//select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and b.category_type not in(1,2,3,12,13,14,24,25,35) group by a.id,a.store_name order by a.store_name

	//page lib 
	$store = return_library_array("select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and b.category_type not in(1,2,3,12,13,14,24,25,35) group by a.id,a.store_name order by a.store_name", 'id', 'store_name');

	//print_r($store); die;

	//$store1 = return_library_array("select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type in ($all_general_item_id) and a.status_active=1 and a.is_deleted=0 group by a.id,a.store_name order by a.store_name", 'id', 'store_name');

	//print_r($store); 
	//print_r($store1); 
	//die;

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$section_library = return_library_array("select id, section_name from lib_section", "id", "section_name");
	$department = return_library_array("select id, department_name from lib_department", "id", "department_name");
	$location = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$division = return_library_array("select id, division_name from lib_division", "id", "division_name");
	$section_library = return_library_array("select id, section_name from lib_section", "id", "section_name");
	$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	$user_designation_arr=return_library_array( "select id,custom_designation from lib_designation",'id','custom_designation');



	$sql = "select a.id,a.itemissue_req_prefix,a.itemissue_req_prefix_num,a.itemissue_req_sys_id,a.company_id,a.indent_date,a.required_date,a.location_id,a.division_id,a.department_id,a.section_id,a.sub_section_id,a.delivery_point,a.remarks,a.inserted_by,b.designation, b.user_full_name from inv_item_issue_requisition_mst a , user_passwd b  where  a.inserted_by=b.id and a.status_active=1 and a.is_deleted=0 and a.id='$data[1]'";
	// echo $sql; die;
	$dataArray = sql_select($sql);
	$inserted_by=$dataArray[0][csf('inserted_by')];
	$user_designation=$user_designation_arr[$dataArray[0][csf("designation")]];
	$user_name=$dataArray[0][csf("user_full_name")];
	?>
    <div style="width:1000px;">
     <?php
		/*$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from inv_item_issue_requisition_mst a, approval_history b where a.id=b.mst_id  and b.entry_form=26 and b.mst_id='$data[1]'"); 
		list($nameArray_approved_row)=$nameArray_approved;
		$nameArray_approved=sql_select( "select b.approved_date as approved_date,b.approved_no,b.approved_by from inv_item_issue_requisition_mst a, approval_history b where a.id=b.mst_id  and b.entry_form=26 and b.mst_id='$data[1]' order by b.id");
		list($nameArray_approved_date_row)=$nameArray_approved_date;
		
		
		$nameArray_approved_comments=sql_select( "select b.comments as comments from inv_item_issue_requisition_mst a, approval_history b where a.id=b.mst_id  and b.entry_form=26 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
		list($nameArray_approved_comments_row)=$nameArray_approved_comments;
		$designation_arr = return_library_array("select id, custom_designation from lib_designation", "id", "custom_designation");
		$user_designation_arr = return_library_array("select id, designation from USER_PASSWD", "id", "designation");*/
		
		//echo "select max(b.approved_no) as approved_no from inv_item_issue_requisition_mst a, approval_history b where a.id=b.mst_id  and b.entry_form=26 and b.mst_id='$data[1]'"; die;
		//$nameArray_approved=sql_select( "select max(b.approved_by) as approved_by from inv_item_issue_requisition_mst a, approval_history b where a.id=b.mst_id  and b.entry_form=26 and b.mst_id='$data[1]'"); 
		

		
		
		//list($nameArray_approved_row)=$nameArray_approved;
		
		
	//$nameArray_approved_date=sql_select( "select b.approved_date as approved_date,b.approved_no,b.approved_by from inv_item_issue_requisition_mst a, approval_history b where a.id=b.mst_id  and b.entry_form=26 and b.mst_id='$data[1]' and b.id in(select max(b.id) from inv_item_issue_requisition_mst a, approval_history b where a.id=b.mst_id and b.entry_form=26 and b.mst_id='$data[1]' group by b.approved_by)  order by b.id");
		
		//$nameArray_approved=sql_select( "select b.approved_date as approved_date,b.approved_no,b.approved_by from inv_item_issue_requisition_mst a, approval_history b where a.id=b.mst_id  and b.entry_form=26 and b.mst_id='$data[1]' order by b.id");
		
		//echo "select max(b.APPROVED_DATE ) APPROVED_DATE ,b.approved_by,count(b.id) as approved_no from inv_item_issue_requisition_mst a, approval_history b where a.id=b.mst_id and b.entry_form=26 and b.mst_id='$data[1]' group by b.approved_by";
		
		
		
		/*
		list($nameArray_approved_date_row)=$nameArray_approved_date;
		$nameArray_approved_comments=sql_select( "select b.comments as comments from inv_item_issue_requisition_mst a, approval_history b where a.id=b.mst_id  and b.entry_form=26 and b.approved_no='".$nameArray_approved_row[csf('approved_no')]."'");
		list($nameArray_approved_comments_row)=$nameArray_approved_comments;
		*/
	
		
		
	/*	select 
   rowid, id, level_des, system_designation, 
   custom_designation, custom_designation_local, allowance_rate, 
   allowance_treatment, inserted_by, insert_date, 
   updated_by, update_date, status_active, 
   is_deleted, is_locked
	from logic3rdversion.lib_designation*/

		
    ?>	
	<table width="1000" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
		<tr class="form_caption" >
			<?
			$data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
			?>
			<td rowspan="2" align="left" width="110">
				<?
				foreach ($data_array as $img_row) {
					?>
					<img src='../<? echo $img_row[csf('image_location')]; ?>' height='80%' width='80%' align="middle"/>
					<?
				}
				?>
			</td>
			<td colspan="4" align="center" style="font-size:18px">
				<strong><? echo $company_library[$data[0]]; ?></strong></td>
			
			<td>&nbsp;</td>
		</tr>
            <tr>
            	<td colspan="4" align="center">
					<?
					$location_sql = "select COMPANY_ID,EMAIL,WEBSITE,ADDRESS from  lib_location where ID='$data[5]' and COMPANY_ID='$data[0]'";
					$addressRes=sql_select($location_sql);
                    ?>
					Address: <?=$addressRes[0]['ADDRESS']; ?><br>
					Email Address: <? echo $addressRes['EMAIL'];?>
					Website No: <? echo $addressRes['WEBSITE'];?>
                </td>
                <td>&nbsp;</td>
            </tr>
		</table>
	
        <table width="1000" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
            <!-- tr>
                <td colspan="6" align="center" style="font-size:18px">
                    <strong><? //echo $company_library[$data[0]]; ?></strong></td>
            </tr> -->
           
            <tr>
                <td colspan="6" align="center" style="font-size:16px"><strong><u>Item Issue
                            requisition</u></strong></center></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="160"><strong>Indent No:</strong></td>
                <td width="160px"><? echo $dataArray[0][csf('itemissue_req_sys_id')]; ?></td>
                <td width="160"><strong>Indent Date:</strong></td>
                <td width="160px"><? echo change_date_format($dataArray[0][csf('indent_date')]); ?></td>
                <td width="160"><strong>Required Date:</strong></td>
                <td width="160px"><? echo change_date_format($dataArray[0][csf('required_date')]); ?></td>
            </tr>
            <tr>
                <td width="180"><strong>Location:</strong></td>
                <td width="160px"><? echo $location[$dataArray[0][csf('location_id')]]; ?></td>
                <td width="180"><strong>Division:</strong></td>
                <td width="160px"><? echo $division[$dataArray[0][csf('division_id')]]; ?></td>
                <td width="180"><strong>Department:</strong></td>
                <td width="160px"><? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
            </tr>

            <tr>
                <td width="180"><strong>Section:</strong></td>
                <td width="160px"><? echo $section_library[$dataArray[0][csf('section_id')]]; ?></td>
                <td width="180"><strong>Sub Section:</strong></td>
                <td width="160px"><? echo $dataArray[0][csf('sub_section_id')]; ?></td>
                <td width="180"><strong>Delivery Point:</strong></td>
                <td width="160px"><? echo $dataArray[0][csf('delivery_point')]; ?></td>
            </tr>
            <tr>
                
                <td width="160"><strong>Store:</strong></td>
                <td width="160px"><? echo $store[$data[3]]; ?></td>

                <td width="160"><strong>Remarks: </strong></td>
                <td colspan="4" ><? echo $dataArray[0][csf('remarks')]; ?></td>

            </tr>
            <tr>
                <td><strong>Bar Code:</strong></td>
                <td colspan="4" id="barcode_img"></td>
                <td style="text-align:center; color:#FF0000; font-weight:bold; font-size:16px;"><? if($data[4]==1) echo "Approved"; else echo "&nbsp;"; ?></td>
            </tr>
        </table>
    </div>
				<!-- <td width="160"><strong>Indent Date:</strong></td>
                <td width="160px"><? echo change_date_format($dataArray[0][csf('indent_date')]); ?></td>
                <td width="160"><strong>Required Date:</strong></td>
                <td width="160px"><? echo change_date_format($dataArray[0][csf('required_date')]); ?></td> -->

    <div style="width:100%; margin-top:10px;">

		<?

		$sql_dtls = "select b.id, b.mst_id, b.item_account, b.item_group, b.item_sub_group, b.item_description, b.item_size, b.unit_of_measure, b.current_stock, b.req_for, b.req_qty, b.rtn_qnty, b.remarks, b.product_id from inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b where a.id=b.mst_id and a.id='$data[1]' and b.status_active=1 and b.is_deleted=0";

		$item_group = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
		$nameArray = sql_select($sql_dtls);


		?>
        <table cellspacing="0" width="1000" border="1" rules="all" class="rpt_table">
            <thead bgcolor="#dddddd" style="font-size:13px">
                <tr>
                    <th width="20">SL</th>
                    <th width="60">Product Id</th>
                    <th width="70">Item Account</th>
                    <th width="100">Item Category</th>
                    <th width="120">Item Group</th>
                    <th width="100">Item Sub Group</th>
                    <th width="120">Item Description</th>
                    <th width="60">Item Size</th>
                    <th width="80">Required For</th>
                    <th width="40">UOM</th>
                    <th width="50">Req. Qty.</th>
                    <th width="50">Replace Qty.</th>
                    <?
					$company=$data[0];
					$status = sql_select("select ready_to_approve,id from variable_settings_inventory where company_name = $company and variable_list =51 and is_deleted = 0 and status_active = 1");					 
					$select_status =  $status[0][csf('ready_to_approve')];
					 
	                if(($user_lavel==2 || $user_lavel==1) && $select_status==1)
					{
					?>
                    <th width="50">Stock</th>
                    <? 
					}
					?>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
			<?
			$con = connect();
			$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (54)");
			oci_commit($con);

			$prod_id_arr = array();
			foreach ($nameArray as $selectResult) 
			{
				$prod_id_arr[$selectResult[csf('product_id')]] = $selectResult[csf('product_id')];
			}
			if (count($prod_id_arr)>0)
			{
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 54, 2, $prod_id_arr, $empty_arr);
				$prodDataArr=sql_select("SELECT A.ID, A.ITEM_CATEGORY_ID
				FROM PRODUCT_DETAILS_MASTER A, GBL_TEMP_ENGINE B WHERE A.ID=B.REF_VAL AND B.USER_ID= $user_id AND B.ENTRY_FORM=54 AND B.REF_FROM=2");

				foreach($prodDataArr as $row)
				{
					$product_arr[$row['ID']]['category_name']=$row['ITEM_CATEGORY_ID'];
				}
				unset($prodDataArr);
			}

			execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (54)");
			oci_commit($con);
			disconnect($con);
			//$item_sub_group = return_library_array("select sub_group_code,sub_group_name from product_details_master", 'sub_group_code', 'sub_group_name');
			$user_arr = return_library_array("select user_name,id from user_passwd", 'id', 'user_name');
			$user = "select id,inserted_by,updated_by from inv_item_issue_requisition_mst where id='$data[1]'";
			//$item_category_arr = return_library_array("select id,item_category_id from product_details_master", 'id', 'item_category_id');
			$user_print = sql_select($user);
			$i = 1;
			foreach ($nameArray as $selectResult) 
			{
				?>
                <tr>
                    <td align="center"><? echo $i; ?></td>
                    <td align="center"><? echo $selectResult[csf("product_id")]; ?></td>
                    <td align="center"><? echo $selectResult[csf("item_account")]; ?></td>
                    <td style="word-break: break-all;"><?
					//echo $item_category[$item_category_arr[$selectResult[csf("product_id")]]]; 
					echo $item_category[$product_arr[$selectResult[csf("product_id")]]['category_name']]; 
					 ?></td>
                    <td style="word-break: break-all;"><? echo $item_group[$selectResult[csf("item_group")]]; ?></td>
                    <td style="word-break: break-all;"><? echo $selectResult[csf("item_sub_group")]; ?></td>
                    <td style="word-break: break-all;"><? echo $selectResult[csf("item_description")]; ?></td>
                    <td><? echo $selectResult[csf("item_size")]; ?></td>
                    <td><? echo $selectResult[csf("req_for")]; ?></td>
                    <td align="center"><? echo $unit_of_measurement[$selectResult[csf("unit_of_measure")]]; ?></td>
                    <td align="right"><? echo $selectResult[csf("req_qty")]; ?></td>
                    <td align="right"><? echo $selectResult[csf("rtn_qnty")]; ?></td>
                    <?
					$company=$data[0];
					$status = sql_select("select ready_to_approve,id from variable_settings_inventory where company_name = $company and variable_list =51 and is_deleted = 0 and status_active = 1");					 
					$select_status =  $status[0][csf('ready_to_approve')];
					 
	                if(($user_lavel==2 || $user_lavel==1) && $select_status==1)
					{
					?>
                    <td align="right"><? echo $selectResult[csf("current_stock")]; ?></td>
                    <? 
					}
					?>
                    <td align="right"><? echo $selectResult[csf("remarks")]; ?></td>
                </tr>
				<? 
				$i++;
			} 
			?>
            </tbody>
        </table>
       <!-- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
	   <div style="margin-top:15px">
                <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:1000px;text-align:center;" rules="all">
                    <label style="font-size:16px"><b> Raised By</b></label>
                    <thead bgcolor="#dddddd">
                    <tr style="font-weight:bold">
                        <th style="font-size:15px" width="20">SL</th>
                        <th style="font-size:15px" width="250">Name</th>
                        <th style="font-size:15px" width="200">Position</th>
                    </tr>
                    </thead>
                    <tr>
                        <td width="20"><? echo "1"; ?></td>
                        <td width="250"><? echo $user_name; ?></td>
                        <td width="200"><?echo $user_designation ?></td>
                    </tr>
                </table>
            </div>
			<?

//approved status
/*$data_array_approve=sql_select("SELECT b.approved_by,b.approved_no, b.approved_date, c.user_full_name, c.designation, b.un_approved_by from inv_purchase_requisition_mst a, approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and b.entry_form=1 and a.id='$data[1]' order by b.id asc");*/

$approved_sql=sql_select("SELECT  mst_id, approved_by ,max(approved_date) as approved_date  from approval_history where entry_form in (26,56) AND  mst_id = $data[1]   group by mst_id, approved_by order by  approved_by asc");

// echo "SELECT  mst_id, approved_by ,max(approved_date) as approved_date  from approval_history where entry_form in (26,56) AND  mst_id = $data[1]   group by mst_id, approved_by order by  approved_by asc";die;


$approved_his_sql=sql_select("SELECT  MST_ID, APPROVED_BY ,APPROVED_DATE,UN_APPROVED_REASON,UN_APPROVED_DATE,APPROVED_NO,APPROVED  from approval_history where entry_form in (26,56) AND  mst_id ='$data[1]' ORDER BY approved_by ASC");

// echo "SELECT  MST_ID, APPROVED_BY ,APPROVED_DATE,UN_APPROVED_REASON,UN_APPROVED_DATE,APPROVED_NO,APPROVED  from approval_history where entry_form in (26,56) AND  mst_id ='$data[1]' ORDER BY approved_by ASC";die;

// $approved_his_sql=sql_select("SELECT a.MST_ID, a.APPROVED_BY, a.APPROVED_DATE, b.REFUSING_REASON AS UN_APPROVED_REASON, a.UN_APPROVED_DATE, a.APPROVED_NO, a.APPROVED FROM approval_history a LEFT JOIN refusing_cause_history b ON a.APPROVED_BY = b.INSERTED_BY WHERE a.entry_form IN (26, 56) AND a.mst_id = '$data[1]' ORDER BY approved_by ASC");

//  echo "SELECT a.MST_ID, a.APPROVED_BY, a.APPROVED_DATE, b.REFUSING_REASON AS UN_APPROVED_REASON, a.UN_APPROVED_DATE, a.APPROVED_NO, a.APPROVED FROM approval_history a LEFT JOIN refusing_cause_history b ON a.APPROVED_BY = b.INSERTED_BY WHERE a.entry_form IN (26, 56) AND a.mst_id = '$data[1]' ORDER BY approved_by ASC";die;

/*$approved_sql=sql_select("SELECT  mst_id, approved_by,sequence_no ,min(approved_date) as approved_date from approval_history where entry_form=1 AND  mst_id ='$data[1]' group by mst_id, approved_by,sequence_no order by sequence_no");

$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date  from approval_history where entry_form=1 AND  mst_id ='$data[1]' ");*/

$sql_unapproved=sql_select("select * from fabric_booking_approval_cause where  entry_form=1  and is_deleted=0 and status_active=1");
$unapproved_request_arr=array();
foreach($sql_unapproved as $rowu)
{
	$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
}
/*echo "<pre>";
print_r($unapproved_request_arr);*/
foreach ($approved_his_sql as $key => $row)
{
	$array_data[$row[csf('approved_by')]][$row[csf('approved_date')]]['approved_date'] = $row[csf('approved_date')];
	if ($row[csf('un_approved_date')]!='')
	{
		$array_data[$row[csf('approved_by')]][$row[csf('un_approved_date')]]['un_approved_date'] = $row[csf('un_approved_date')];
		$array_data[$row[csf('approved_by')]][$row[csf('un_approved_date')]]['mst_id'] = $row[csf('mst_id')];
	}
}
/*echo "<pre>";
print_r($array_data);*/

$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");
?>
<? if(count($approved_sql) > 0)
{
	$sl=1;
	?>
	<div style="margin-top:15px">
		<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
			<label style="font-size:16px"> Approval Status </label>
			<thead>
				<tr style="font-weight:bold">
					<th style="font-size:16px" width="20">SL</th>
					<th style="font-size:16px" width="250">Name</th>
					<th style="font-size:16px" width="200">Designation</th>
					<th style="font-size:16px" width="100">Approval Date</th>
				</tr>
			</thead>
			<? foreach ($approved_sql as $key => $value)
			{
				?>
				<tr>
					<td width="20"><? echo $sl; ?></td>
					<td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
					<td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
					<td width="100"><? echo change_date_format($value[csf("approved_date")]); ?></td>
				</tr>
				<?
				$sl++;
			}
			?>
		</table>
	</div>
	<?
}


$app_type_arr = array(0=>'Un App',1=>'Full App',2=>'Deny',3=>'Partial App');

if(count($approved_his_sql) > 0)
{
	$sl=1;
	?>
	<div style="margin-top:15px">
		<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
			<label style="font-size:16px"> Approval / Un-Approval History </label>
			<thead>
				<tr style="font-weight:bold">
					<th style="font-size:16px" width="20">SL</th>
					<th style="font-size:16px" width="150">Approved / Un-Approved</th>
					<th style="font-size:16px" width="150">Designation</th>
					<th style="font-size:16px" width="50">Approval Status</th>
					<th style="font-size:16px" width="150">Reason for Un-Approval</th>
					<th style="font-size:16px" width="150">Date</th>
				</tr>
			</thead>
			<? 
			foreach ($approved_his_sql as $key => $value)
			{
				if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
					<td  width="20"><? echo $sl; ?></td>
					<td  width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
					<td  width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
					<td  width="50"><?= $app_type_arr[$value["APPROVED"]]; ?></td>
					<td  width="150"><?= $value["UN_APPROVED_REASON"]; ?></td>
					<td  width="150"><? $approved_date = explode(" ",$value[csf("approved_date")]);
					echo $value[csf("approved_date")]; ?></td>
				</tr>
				<?
				$sl++;
			}
			?>
		</table>
	</div>
	<?
}
?>
   <!-- <div style="height:100px;"></div>-->
    <table>
        <tr height="21">
			<?
			echo signature_table(143, $data[0], "1000px",$template_id,"0");
			?>
        </tr>
    </table>
    <script type="text/javascript" src="../js/jquery.js"></script>
    <script type="text/javascript" src="../js/jquerybarcode.js"></script>
    <script>

        function generateBarcode(valuess) {

            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();
            var settings = {
                output: renderer,
                bgColor: '#FFFFFF',
                color: '#000000',
                barWidth: 1,
                barHeight: 30,
                moduleSize: 5,
                posX: 10,
                posY: 20,
                addQuietZone: 1
            };
            $("#barcode_img").html('11');
            value = {code: value, rect: false};
            $("#barcode_img").show().barcode(value, btype, settings);

        }

        generateBarcode('<? echo $data[2]; ?>');

    </script>
	<?
	exit();
}

if ($action == "print_item_issue_requisition_print2") 
{
	extract($_REQUEST);
	echo load_html_head_contents("Item Issue requisition Print 2", "../../", 1, 1, '', '', '');
	$data = explode('*', $data);
	// echo "<pre>"; print_r($data); die;
	$store = return_library_array("select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and b.category_type not in(1,2,3,12,13,14,24,25,35) group by a.id,a.store_name order by a.store_name", 'id', 'store_name');

	//print_r($store); die;

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$section_library = return_library_array("select id, section_name from lib_section", "id", "section_name");
	$department = return_library_array("select id, department_name from lib_department", "id", "department_name");
	$location = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$division = return_library_array("select id, division_name from lib_division", "id", "division_name");
	$section_library = return_library_array("select id, section_name from lib_section", "id", "section_name");
	$sewing_floor_library = return_library_array("select id, floor_name from lib_prod_floor where production_process=5 and status_active=1 and is_deleted=0 ", "id", "floor_name");
	$line_library=return_library_array( "select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0", "id", "line_name"  );
	$machine_library=return_library_array( "select id,machine_no from lib_machine_name where category_id=8 and status_active=1 and is_deleted=0", "id", "machine_no"  );
	$sql = "select id,itemissue_req_prefix,itemissue_req_prefix_num,itemissue_req_sys_id,company_id,indent_date,required_date,location_id,division_id,department_id,section_id,sub_section_id,delivery_point,remarks,sewing_floor,sewing_line,machine_no from inv_item_issue_requisition_mst where status_active=1 and is_deleted=0 and id='$data[1]'";
	$dataArray = sql_select($sql);
	$name_iso_Array=sql_select("select iso_no from lib_iso where company_id=$data[0] and status_active=1 and module_id=6 and menu_id=1028");

	$status = sql_select("select ready_to_approve,id from variable_settings_inventory where company_name = $data[0] and variable_list =51 and is_deleted = 0 and status_active = 1");
	$select_status = $status[0][csf('ready_to_approve')];


	$con = connect();
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (54)");
	oci_commit($con);

	?>
    <div style="width:1000px;">
 
        <table width="1000" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
            <tr class="form_caption">
				<?
					$data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
				?>
                <td rowspan="3" align="left"  width="110">
					<?
					foreach ($data_array as $img_row) {
						?>
                        <img src='../<? echo $img_row[csf('image_location')]; ?>'height='60%' width='60%'
                             />
						<?
					}
					?>
                </td>
                <td colspan="4" align="center" style="font-size:18px">
                    <strong><? echo $company_library[$data[0]]; ?></strong></td>
                <td width="200"><b><?="ISO Number  :".$name_iso_Array[0]["ISO_NO"]?></b></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
            	<td colspan="4" align="center" style="font-size:16px">
					<?
						echo $location[$data[5]];
					?>
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr>
				<!-- <td>&nbsp;</td> -->
                <td colspan="4" align="center" style="font-size:16px"><strong><u>Floor/Store Requisition</u></strong></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="5" height="10"></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="160"><strong>Requisition No:</strong></td>
                <td width="160px"><? echo $dataArray[0][csf('itemissue_req_sys_id')]; ?></td>
                <td width="160"><strong>Requisition Date:</strong></td>
                <td width="160px"><? echo change_date_format($dataArray[0][csf('indent_date')]); ?></td>
                <td width="160"><strong>Required Date:</strong></td>
                <td width="160px"><? echo change_date_format($dataArray[0][csf('required_date')]); ?></td>
            </tr>
            <tr>
                <td width="180"><strong>Division:</strong></td>
                <td width="160px"><? echo $division[$dataArray[0][csf('division_id')]]; ?></td>
                <td width="180"><strong>Department:</strong></td>
                <td width="160px"><? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
				<td width="180"><strong>Section:</strong></td>
                <td width="160px"><? echo $section_library[$dataArray[0][csf('section_id')]]; ?></td>
            </tr>

            <tr>
            	<td width="160" valign="top"><strong>Store:</strong></td>
                <td width="160px" valign="top"><? echo $store[$data[3]]; ?></td>
            	<td width="160" valign="top"><strong>Sewing Floor:</strong></td>
                <td width="160px" valign="top"><? echo $sewing_floor_library[$dataArray[0][csf('sewing_floor')]]; ?></td>
                <td width="160" valign="top"><strong>Sewing Line:</strong></td>
                <td width="160px" valign="top"><? echo $line_library[$dataArray[0][csf('sewing_line')]]; ?></td>
            </tr>

            <tr>
                <td width="160" valign="top"><strong>Machine No:</strong></td>
                <td width="160px" valign="top"><? echo $machine_library[$dataArray[0][csf('machine_no')]]; ?></td>
                <td width="160" valign="top"><strong>Remarks: </strong></td>
                <td colspan="4" valign="top"><? echo $dataArray[0][csf('remarks')]; ?></td>
            </tr>
            <tr>
                <td colspan="6" height="10"></td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </div>
	
    <div style="width:100%; margin-top:10px;">

		<?
		$sql_dtls = "SELECT b.id, b.mst_id, b.item_account, b.item_group, b.item_sub_group, b.item_description, b.item_size, b.unit_of_measure, b.current_stock, b.req_for, b.req_qty, b.rtn_qnty, b.remarks, b.PRODUCT_ID, c.item_code from inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id='$data[1]' and b.status_active=1 and b.is_deleted=0";

		$item_group = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
		$nameArray = sql_select($sql_dtls);
		$all_prod_ids_arr = array();
		foreach ($nameArray as $selectResult) 
		{
			$all_prod_ids_arr[$selectResult["PRODUCT_ID"]]=$selectResult["PRODUCT_ID"];
			//$all_prod_ids.=$selectResult[csf("product_id")].",";
		}
		// $all_prod_ids=implode(",",array_unique(explode(",",chop($all_prod_ids,","))));
		// if($all_prod_ids=="") $all_prod_ids=0;
		// $issue_sql="select b.id, b.prod_id, b.transaction_date as transaction_date, b.cons_quantity as issue_qty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id in($all_prod_ids) and b.transaction_type in(2,3,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by  b.prod_id,b.id";
		//print_r($all_prod_ids_arr);
		if (count($all_prod_ids_arr)>0)
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 54, 5, $all_prod_ids_arr, $empty_arr);
			$issue_sql="SELECT B.ID, B.PROD_ID, B.TRANSACTION_DATE AS TRANSACTION_DATE, B.CONS_QUANTITY AS ISSUE_QTY FROM INV_TRANSACTION B , GBL_TEMP_ENGINE A
			WHERE A.REF_VAL = B.PROD_ID AND B.TRANSACTION_TYPE IN(2,3,6) AND  B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND A.USER_ID= $user_id AND A.ENTRY_FORM=54 AND A.REF_FROM=5 ORDER BY  B.PROD_ID,B.ID";
		}
		// echo  $issue_sql;
		$issue_sql_result= sql_select($issue_sql);
		foreach($issue_sql_result as $row)
		{
			$receive_array[$row['PROD_ID']]['transaction_date']=$row['TRANSACTION_DATE'];
			$receive_array[$row['PROD_ID']]['issue_qty']=$row['ISSUE_QTY'];
		}

		?>

        <table cellspacing="0" width="1000" border="1" rules="all" class="rpt_table">
            <thead bgcolor="#dddddd" >
                <tr>
                    <th width="20">SL</th>
                    <th width="60">Product Id</th>
                    <th width="100">Item Code</th>
                    <th width="100">Item Category</th>
                    <th width="120">Item Group</th>
                    <th width="120">Item Description</th>
                    <th width="60">Item Size</th>
                    <th width="80">Required For</th>
                    <th width="40">UOM</th>
                    <th width="50">Req. Qty.</th>
                    <th width="50">Replace Qty.</th>
                    <th width="70">Last Issue Date</th>
                    <th width="100">Last Issue Quantity</th>
                    <?
	                if(($user_lavel==2 || $user_lavel==1) && $select_status==1)
					{
					?>
                    <th width="50">Closing Stock</th>
                    <? 
					}
					?>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
			<?
			$prod_id_arr = array();
			foreach ($nameArray as $selectResult) 
			{
				$prod_id_arr[$selectResult[csf('product_id')]] = $selectResult[csf('product_id')];
			}
			if (count($prod_id_arr)>0)
			{
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 54, 3, $prod_id_arr, $empty_arr);
				$prodDataArr=sql_select("SELECT A.ID, A.ITEM_CATEGORY_ID
				FROM PRODUCT_DETAILS_MASTER A, GBL_TEMP_ENGINE B WHERE A.ID=B.REF_VAL AND B.USER_ID= $user_id AND B.ENTRY_FORM=54 AND B.REF_FROM=3");

				foreach($prodDataArr as $row)
				{
					$product_arr[$row['ID']]['category_name']=$row['ITEM_CATEGORY_ID'];
				}
				unset($prodDataArr);
			}


			$user_arr = return_library_array("select user_name,id from user_passwd", 'id', 'user_name');
			$user = "select id,inserted_by,updated_by from inv_item_issue_requisition_mst where id='$data[1]'";
			//$item_category_arr = return_library_array("select id,item_category_id from product_details_master", 'id', 'item_category_id');
			$user_print = sql_select($user);
			$i = 1;
			foreach ($nameArray as $selectResult) 
			{
				$last_issue_date=$receive_array[$selectResult[csf("product_id")]]['transaction_date'];
				$last_issue_qty=$receive_array[$selectResult[csf("product_id")]]['issue_qty'];
				?>
                <tr>
                    <td align="center"><? echo $i; ?></td>
                    <td align="center"><? echo $selectResult[csf("product_id")]; ?></td>
                    <td align="center"><? echo $selectResult[csf("item_code")];?></td>
                    <td style="word-break: break-all;"><?
					//echo $item_category[$item_category_arr[$selectResult[csf("product_id")]]]; 
					echo $item_category[$product_arr[$selectResult[csf("product_id")]]['category_name']];
					?></td>
                    <td style="word-break: break-all;"><? echo $item_group[$selectResult[csf("item_group")]]; ?></td>
                    <td style="word-break: break-all;"><? echo $selectResult[csf("item_description")]; ?></td>
                    <td><? echo $selectResult[csf("item_size")]; ?></td>
                    <td><? echo $selectResult[csf("req_for")]; ?></td>
                    <td align="center"><? echo $unit_of_measurement[$selectResult[csf("unit_of_measure")]]; ?></td>
                    <td align="right"><? echo number_format($selectResult[csf("req_qty")],2); ?></td>
                    <td align="right"><? echo number_format($selectResult[csf("rtn_qnty")],2); ?></td>
                    <td align="right"><? echo change_date_format($last_issue_date);?></td>
                    <td align="right"><? echo number_format($last_issue_qty,2); ?></td>
                    <?
	                if(($user_lavel==2 || $user_lavel==1) && $select_status==1)
					{
					?>
                    <td align="right"><? echo $selectResult[csf("current_stock")]; ?></td>
                    <? 
					}
					?>
                    <td align="right"><? echo $selectResult[csf("remarks")]; ?></td>
                </tr>
				<? 
				$i++;
			} 
			?>
            </tbody>
        </table>
        <?php  

        	echo signature_table(143, $data[0], "1000px",$template_id,"", $user_id);
        ?>
       &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

    <br/>
    </div>
  
    <script type="text/javascript" src="../js/jquery.js"></script>
	<?
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (54)");
	oci_commit($con);
	disconnect($con);

	exit();
}

if ($action == "print_item_issue_requisition_print3") 
{
	extract($_REQUEST);
	echo load_html_head_contents("Item Issue requisition Print 3", "../../", 1, 1, '', '', '');
	$data = explode('*', $data);

	$store = return_library_array("select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and b.category_type not in(1,2,3,12,13,14,24,25,35) group by a.id,a.store_name order by a.store_name", 'id', 'store_name');

	//print_r($store); die;

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$section_library = return_library_array("select id, section_name from lib_section", "id", "section_name");
	$department = return_library_array("select id, department_name from lib_department", "id", "department_name");
	$location = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$division = return_library_array("select id, division_name from lib_division", "id", "division_name");

	$sql = "SELECT itemissue_req_sys_id as ITEMISSUE_REQ_SYS_ID,company_id as COMPANY_ID,indent_date as INDENT_DATE,required_date as REQUIRED_DATE,location_id as LOCATION_ID,division_id as DIVISION_ID,department_id as DEPARTMENT_ID,section_id as SECTION_ID,sub_section_id as SUB_SECTION_ID,delivery_point as DELIVERY_POINT,remarks as REMARKS,inserted_by as INSERTED_BY from inv_item_issue_requisition_mst where status_active=1 and is_deleted=0 and id='$data[1]'";
	$dataArray = sql_select($sql);
	$inserted_by=$dataArray[0]['INSERTED_BY'];
	$location_id=$dataArray[0]['LOCATION_ID'];

	$status = sql_select("select ready_to_approve,id from variable_settings_inventory where company_name = $data[0] and variable_list =51 and is_deleted = 0 and status_active = 1");
	$select_status = $status[0][csf('ready_to_approve')];

	$con = connect();
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (54)");
	oci_commit($con);


	?>
    <div style="width:1000px;">
 
        <table width="1000" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
            <tr class="form_caption">
                <td colspan="6" align="center" style="font-size:18px">
                    <strong><? echo $company_library[$data[0]]; ?></strong>
				</td>
            </tr>
            <tr>
            	<td colspan="6" align="center">
					<?
					$location_dtils=return_library_array("select id, address from lib_location where company_id=$data[0] and id=$location_id", "id", "address");

					echo $location_dtils[$location_id];


					/*$nameArray = sql_select("SELECT plot_no as PLOT_NO,level_no as LEVEL_NO,road_no as ROAD_NO,block_no as BLOCK_NO,country_id as COUNTRY_ID,province as PROVINCE,city as CITY,zip_code as ZIP_CODE,email as EMAIL,website as WEBSITE from lib_company where id='$data[0]'");

					foreach ($nameArray as $result) 
					{
						?>
						<? echo $result['PLOT_NO']; ?>
						<? echo $result['LEVEL_NO'] ?>
						<? echo $result['ROAD_NO']; ?>
						<? echo $result['BLOCK_NO']; ?>
						<? echo $result['CITY']; ?>
						<? echo $result['ZIP_CODE']; ?>
						<? echo $result['PROVINCE']; ?>
						<? echo $country_arr[$result['COUNTRY_ID']]; ?>
						<? echo $result['EMAIL']; ?>
						<? echo $result['WEBSITE'];
					}*/
					?>
                </td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:16px"><strong><u>Item Issue Requisition</u></strong></td>
            </tr>
            <tr>
                <td colspan="6" height="10"></td>
            </tr>
            <tr>
                <td width="160"><strong>Requisition No:</strong></td>
                <td width="160px"><? echo $dataArray[0]['ITEMISSUE_REQ_SYS_ID']; ?></td>
                <td width="160"><strong>Requisition Date:</strong></td>
                <td width="160px"><? echo change_date_format($dataArray[0]['INDENT_DATE']); ?></td>
                <td width="160"><strong>Required Date:</strong></td>
                <td width="160px"><? echo change_date_format($dataArray[0]['REQUIRED_DATE']); ?></td>
            </tr>
            <tr>
                <td width="180"><strong>Location:</strong></td>
                <td width="160px"><? echo $location[$dataArray[0]['LOCATION_ID']]; ?></td>
                <td width="180"><strong>Division:</strong></td>
                <td width="160px"><? echo $division[$dataArray[0]['DIVISION_ID']]; ?></td>
                <td width="180"><strong>Department:</strong></td>
                <td width="160px"><? echo $department[$dataArray[0]['DEPARTMENT_ID']]; ?></td>
            </tr>
            <tr>
				<td width="180"><strong>Section:</strong></td>
                <td width="160px"><? echo $section_library[$dataArray[0]['SECTION_ID']]; ?></td>
                <td width="160" valign="top"><strong>Store:</strong></td>
                <td width="160px"><? echo $store[$data[3]]; ?></td>
                <td width="160" valign="top"><strong>Remarks: </strong></td>
                <td width="160px"><? echo $dataArray[0]['REMARKS']; ?></td>
            </tr>
            <tr>
                <td colspan="6" height="10"></td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </div>
	
    <div style="width:100%; margin-top:10px;">

		<?
		$sql_dtls = "SELECT b.item_account as ITEM_ACCOUNT, b.item_group as ITEM_GROUP, b.item_sub_group as ITEM_SUB_GROUP, b.item_description as ITEM_DESCRIPTION, b.item_size as ITEM_SIZE, b.unit_of_measure as UNIT_OF_MEASURE, b.current_stock as CURRENT_STOCK, b.req_for as REQ_FOR, b.req_qty as REQ_QTY, b.rtn_qnty as RTN_QNTY, b.remarks as REMARKS, b.product_id as PRODUCT_ID, c.item_code as ITEM_CODE from inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b, product_details_master c where a.id=b.mst_id and  b.product_id=c.id and a.id='$data[1]' and b.status_active=1 and b.is_deleted=0";
		// echo $sql_dtls;
		$item_group = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
		$nameArray = sql_select($sql_dtls);
		?>

        <table cellspacing="0" width="1000" border="1" rules="all" class="rpt_table">
            <thead bgcolor="#dddddd" >
                <tr>
                    <th width="20">SL</th>
                    <th width="60">Product Id</th>
                    <th width="70">Item Code</th>
                    <th width="100">Item Category</th>
                    <th width="120">Item Group</th>
                    <th width="100">Item Sub Group</th>
                    <th width="120">Item Description</th>
                    <th width="60">Item Size</th>
                    <th width="80">Required For</th>
                    <th width="40">UOM</th>
                    <th width="50">Req. Qty.</th>
                    <th width="50">Replace Qty.</th>
                    <?
	                if(($user_lavel==2 || $user_lavel==1) && $select_status==1)
					{
					?>
                    <th width="50">Closing Stock</th>
                    <? 
					}
					?>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
			<?
			$prod_id_arr = array();
			foreach ($nameArray as $selectResult) 
			{
				$prod_id_arr[$selectResult[csf('product_id')]] = $selectResult[csf('product_id')];
			}
			if (count($prod_id_arr)>0)
			{
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 54, 4, $prod_id_arr, $empty_arr);
				$prodDataArr=sql_select("SELECT A.ID, A.ITEM_CATEGORY_ID
				FROM PRODUCT_DETAILS_MASTER A, GBL_TEMP_ENGINE B WHERE A.ID=B.REF_VAL AND B.USER_ID= $user_id AND B.ENTRY_FORM=54 AND B.REF_FROM=4");

				foreach($prodDataArr as $row)
				{
					$product_arr[$row['ID']]['category_name']=$row['ITEM_CATEGORY_ID'];
				}
				unset($prodDataArr);
			}
			// $user_arr = return_library_array("select user_name,id from user_passwd", 'id', 'user_name');
			// $user = "select id,inserted_by,updated_by from inv_item_issue_requisition_mst where id='$data[1]'";
			//$item_category_arr = return_library_array("select id,item_category_id from product_details_master", 'id', 'item_category_id');
			$user_print = sql_select($user);
			$i = 1;
			foreach ($nameArray as $selectResult) 
			{
				?>
                <tr>
                    <td align="center"><? echo $i; ?></td>
                    <td align="center"><? echo $selectResult["PRODUCT_ID"]; ?></td>
                    <td style="word-break: break-all;"><? echo $selectResult["ITEM_CODE"]; ?></td>
                    <td style="word-break: break-all;"><?
					 //echo $item_category[$item_category_arr[$selectResult["PRODUCT_ID"]]]; 
					 echo $item_category[$product_arr[$selectResult[csf("product_id")]]['category_name']];
					 ?></td>
                    <td style="word-break: break-all;"><? echo $item_group[$selectResult["ITEM_GROUP"]]; ?></td>
                    <td style="word-break: break-all;"><? echo $selectResult["ITEM_SUB_GROUP"]; ?></td>
                    <td style="word-break: break-all;"><? echo $selectResult["ITEM_DESCRIPTION"]; ?></td>
                    <td><? echo $selectResult["ITEM_SIZE"]; ?></td>
                    <td><? echo $selectResult["REQ_FOR"]; ?></td>
                    <td align="center"><? echo $unit_of_measurement[$selectResult["UNIT_OF_MEASURE"]]; ?></td>
                    <td align="right"><? echo number_format($selectResult["REQ_QTY"],2); ?></td>
                    <td align="right"><? echo number_format($selectResult["RTN_QNTY"],2); ?></td>
                    <?
						if(($user_lavel==2 || $user_lavel==1) && $select_status==1)
						{
							?>
								<td align="right"><? echo $selectResult["CURRENT_STOCK"]; ?></td>
							<? 
						}
					?>
                    <td align="right"><? echo $selectResult["REMARKS"]; ?></td>
                </tr>
				<? 
				$i++;
			} 
			?>
            </tbody>
        </table>
    	<br/>
    </div>

	<?
	$user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	$nameArray_approved=sql_select("SELECT b.approved_by as APPROVED_BY,min(b.approved_date) as APPROVED_DATE from approval_history b where b.mst_id=$data[1] and b.entry_form in(26,56) group by mst_id, approved_by order by approved_date");

	if(count($nameArray_approved)>0)
	{
 		?>
        <table  width="1000" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            	<tr style="border:1px solid black;">
                	<th colspan="5" style="border:1px solid black;">Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="10%" style="border:1px solid black;">Sl</th>
				<th width="60%" style="border:1px solid black;">Name</th>
				<th width="30%" style="border:1px solid black;">Approval Date</th>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($nameArray_approved as $row)
			{
				?>
				<tr style="border:1px solid black;">
					<td width="10%" style="border:1px solid black;"><? echo $i;?></td>
					<td width="60%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('APPROVED_BY')]];?></td>
					<td width="30%" style="border:1px solid black;text-align:center"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('APPROVED_DATE')]));?></td>
				
				</tr>
                <?
				$i++;
			}
				?>
            </tbody>
        </table>
		<br/>
		<?
	}

	?>
    <br/>
	<table>
        <tr height="21">
			<?
			echo signature_table(143, $data[0], "1000px",$template_id,70,$inserted_by);
			?>
        </tr>
    </table>
    <script type="text/javascript" src="../js/jquery.js"></script>
	<?
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (54)");
	oci_commit($con);
	disconnect($con);
	exit();
}

if ($action == "print_item_issue_requisition_print4") 
{
	extract($_REQUEST);
	echo load_html_head_contents("Item Issue requisition Print 4", "../../", 1, 1, '', '', '');
	$data = explode('*', $data);

	$store = return_library_array("select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and b.category_type not in(1,2,3,12,13,14,24,25,35) group by a.id,a.store_name order by a.store_name", 'id', 'store_name');

	//print_r($store); die;

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$section_library = return_library_array("select id, section_name from lib_section", "id", "section_name");
	$department = return_library_array("select id, department_name from lib_department", "id", "department_name");
	$location = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$division = return_library_array("select id, division_name from lib_division", "id", "division_name");
	$section_library = return_library_array("select id, section_name from lib_section", "id", "section_name");
	$sewing_floor_library = return_library_array("select id, floor_name from lib_prod_floor where production_process=5 and status_active=1 and is_deleted=0 ", "id", "floor_name");
	$line_library=return_library_array( "select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0", "id", "line_name"  );
	$machine_library=return_library_array( "select id,machine_no from lib_machine_name where category_id=8 and status_active=1 and is_deleted=0", "id", "machine_no"  );
	$sql = "select id,itemissue_req_prefix,itemissue_req_prefix_num,itemissue_req_sys_id,company_id,indent_date,required_date,location_id,division_id,department_id,section_id,sub_section_id,delivery_point,remarks,sewing_floor,sewing_line,machine_no from inv_item_issue_requisition_mst where status_active=1 and is_deleted=0 and id='$data[1]'";
	$dataArray = sql_select($sql);

	?>
    <div style="width:1000px;">
 
        <table width="1000" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
            <tr class="form_caption">
				<?
					$data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
				?>
                <td rowspan="3" align="left"  width="110">
					<?
					foreach ($data_array as $img_row) {
						?>
                        <img src='../<? echo $img_row[csf('image_location')]; ?>'height='80%' width='80%'
                             />
						<?
					}
					?>
                </td>
                <td colspan="4" align="center" style="font-size:18px">
                    <strong><? echo $company_library[$data[0]]; ?></strong></td>
                
                <td>&nbsp;</td>
            </tr>
            <tr>
            	<td colspan="4" align="center">
					<?
					$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id='$data[0]'");

					foreach ($nameArray as $result) {
						?>
						<? echo $result[csf('plot_no')]; ?>
						<? echo $result[csf('level_no')] ?>
						<? echo $result[csf('road_no')]; ?>
						<? echo $result[csf('block_no')]; ?>
						<? echo $result[csf('city')]; ?>
						<? echo $result[csf('zip_code')]; ?>
						<? echo $result[csf('province')]; ?>
						<? echo $country_arr[$result[csf('country_id')]]; ?>
						<? echo $result[csf('email')]; ?>
						<? echo $result[csf('website')];
					}
					?>
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr>
				<!-- <td>&nbsp;</td> -->
                <td colspan="4" align="center" style="font-size:16px"><strong><u>Floor/Store Requisition</u></strong></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="5" height="10"></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="160"><strong>Requisition No:</strong></td>
                <td width="160px"><? echo $dataArray[0][csf('itemissue_req_sys_id')]; ?></td>
                <td width="160"><strong>Requisition Date:</strong></td>
                <td width="160px"><? echo change_date_format($dataArray[0][csf('indent_date')]); ?></td>
                <td width="160"><strong>Required Date:</strong></td>
                <td width="160px"><? echo change_date_format($dataArray[0][csf('required_date')]); ?></td>
            </tr>
            <tr>
                <td width="180"><strong>Division:</strong></td>
                <td width="160px"><? echo $division[$dataArray[0][csf('division_id')]]; ?></td>
                <td width="180"><strong>Department:</strong></td>
                <td width="160px"><? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
				<td width="180"><strong>Section:</strong></td>
                <td width="160px"><? echo $section_library[$dataArray[0][csf('section_id')]]; ?></td>
            </tr>

            <tr>
            	<td width="160" valign="top"><strong>Store:</strong></td>
                <td width="160px" valign="top"><? echo $store[$data[3]]; ?></td>
				<td width="160" valign="top"><strong>Remarks: </strong></td>
                <td width="160px" valign="top"><? echo $dataArray[0][csf('remarks')]; ?></td>
            	
            </tr>

           
            <tr>
                <td colspan="6" height="10"></td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </div>
	
    <div style="width:100%; margin-top:10px;">

		<?
		$sql_dtls = "select b.id, b.mst_id, b.item_account, b.item_group, b.item_sub_group, b.item_description, b.item_size, b.unit_of_measure, b.current_stock, b.req_for, b.req_qty, b.rtn_qnty, b.remarks, b.product_id, c.item_code from inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b,product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id='$data[1]' and b.status_active=1 and b.is_deleted=0";

		$item_group = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
		$nameArray = sql_select($sql_dtls);
		foreach ($nameArray as $selectResult) 
		{
			$all_prod_ids.=$selectResult[csf("product_id")].",";
		}
		$all_prod_ids=implode(",",array_unique(explode(",",chop($all_prod_ids,","))));
		if($all_prod_ids=="") $all_prod_ids=0;
		// $issue_sql="select b.id, b.prod_id, b.transaction_date as transaction_date, b.cons_quantity as issue_qty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id in($all_prod_ids) and b.transaction_type in(2,3,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by  b.prod_id,b.id";
		$issue_sql="select b.id, b.prod_id, b.transaction_date as transaction_date, b.cons_quantity as issue_qty from inv_transaction b where b.prod_id in($all_prod_ids) and b.transaction_type in(2,3,6) and  b.status_active=1 and b.is_deleted=0 order by  b.prod_id,b.id";
		// echo  $issue_sql;
		$issue_sql_result= sql_select($issue_sql);
		foreach($issue_sql_result as $row)
		{
			$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
			$receive_array[$row[csf('prod_id')]]['issue_qty']=$row[csf('issue_qty')];
		}

		?>

        <table cellspacing="0" width="1000" border="1" rules="all" class="rpt_table">
            <thead bgcolor="#dddddd" >
                <tr>
                    <th width="20">SL</th>
                    <th width="60">Product Id</th>
                    <th width="100">Item Code</th>
                    <th width="100">Item Category</th>
                    <th width="120">Item Group</th>
                    <th width="120">Item Description</th>
                    <th width="80">Required For</th>
                    <th width="40">UOM</th>
                    <th width="50">Req. Qty.</th>
                    
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
			<?
			$user_arr = return_library_array("select user_name,id from user_passwd", 'id', 'user_name');
			$user = "select id,inserted_by,updated_by from inv_item_issue_requisition_mst where id='$data[1]'";
			$item_category_arr = return_library_array("select id,item_category_id from product_details_master", 'id', 'item_category_id');
			$user_print = sql_select($user);
			$i = 1;
			foreach ($nameArray as $selectResult) 
			{ 
				 $last_issue_date=$receive_array[$selectResult[csf("product_id")]]['transaction_date'];

				 if($receive_array[$selectResult[csf("product_id")]]['issue_qty']>0){
					$last_issue_qty=$receive_array[$selectResult[csf("product_id")]]['issue_qty'];
					}else{
					   $last_issue_qty=0;
					}
				?>
                <tr>
                    <td align="center"><? echo $i; ?></td>
                    <td align="center"><? echo $selectResult[csf("product_id")]; ?></td>
                    <td align="center"><? echo $selectResult[csf("item_code")];?></td>
                    <td style="word-break: break-all;"><? echo $item_category[$item_category_arr[$selectResult[csf("product_id")]]]; ?></td>
                    <td style="word-break: break-all;"><? echo $item_group[$selectResult[csf("item_group")]]; ?></td>
                    <td style="word-break: break-all;"><? echo $selectResult[csf("item_description")]; ?></td>
                    <td><? echo $selectResult[csf("req_for")]; ?></td>
                    <td align="center"><? echo $unit_of_measurement[$selectResult[csf("unit_of_measure")]]; ?></td>
                    <td align="right"><? echo number_format($selectResult[csf("req_qty")],2); ?></td>
                    
                    <td align="right"><? echo $selectResult[csf("remarks")]; ?></td>
                </tr>
				<? 
				$i++;
			} 
			?>
            </tbody>
        </table>
        <?php  

        	echo signature_table(143, $data[0], "1000px",$template_id,"", $user_id);
        ?>
       &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

    <br/>
    </div>
  
    <script type="text/javascript" src="../js/jquery.js"></script>
	<?
	exit();
}

if ($action == "print_item_issue_requisition_print5") 
{
	extract($_REQUEST);
	echo load_html_head_contents("Item Issue requisition Print", "../../", 1, 1, '', '', '');
	$data = explode('*', $data);
	// print_r($data); die;
	
	$store = return_library_array("select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and b.category_type not in(1,2,3,12,13,14,24,25,35) group by a.id,a.store_name order by a.store_name", 'id', 'store_name');

	//print_r($store); die;

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$section_library = return_library_array("select id, section_name from lib_section", "id", "section_name");
	$department = return_library_array("select id, department_name from lib_department", "id", "department_name");
	$location = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$division = return_library_array("select id, division_name from lib_division", "id", "division_name");
	$section_library = return_library_array("select id, section_name from lib_section", "id", "section_name");
	$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	$user_designation_arr=return_library_array( "select id,custom_designation from lib_designation",'id','custom_designation');

	$status = sql_select("select ready_to_approve,id from variable_settings_inventory where company_name = $data[0] and variable_list =51 and is_deleted = 0 and status_active = 1");
	$select_status = $status[0][csf('ready_to_approve')];


	$sql = "select a.id,a.itemissue_req_prefix,a.itemissue_req_prefix_num,a.itemissue_req_sys_id,a.company_id,a.indent_date,a.required_date,a.location_id,a.division_id,a.department_id,a.section_id,a.sub_section_id,a.delivery_point,a.remarks,a.inserted_by,b.designation, b.user_full_name from inv_item_issue_requisition_mst a , user_passwd b  where  a.inserted_by=b.id and a.status_active=1 and a.is_deleted=0 and a.id='$data[1]'";
	// echo $sql; die;
	$dataArray = sql_select($sql);
	$inserted_by=$dataArray[0][csf('inserted_by')];
	$user_designation=$user_designation_arr[$dataArray[0][csf("designation")]];
	$user_name=$dataArray[0][csf("user_full_name")];
	?>
    <div style="width:1000px;">
 
        <table width="1000" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
         
            <tr class="form_caption">
				<?
				$data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
				?>

                <td rowspan="2" align="left" width="110">
					<?
					foreach ($data_array as $img_row) {

						?>
                        <img src='../<? echo $img_row[csf('image_location')]; ?>' height='80%' width='80%'
                             align="middle"/>
						<?

					}
					?>
                </td>
                <td colspan="4" align="center" style="font-size:18px">
                    <strong><? echo $company_library[$data[0]]; ?></strong></td>
                
                <td>&nbsp;</td>
            </tr>
            <tr>
            	<td colspan="4" align="center">
					<?
					$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id='$data[0]'");

					foreach ($nameArray as $result) {
						?>
						<? echo $result[csf('plot_no')]; ?>
						<? echo $result[csf('level_no')] ?>
						<? echo $result[csf('road_no')]; ?>
						<? echo $result[csf('block_no')]; ?>
						<? echo $result[csf('city')]; ?>
						<? echo $result[csf('zip_code')]; ?>
						<? echo $result[csf('province')]; ?>
						<? echo $country_arr[$result[csf('country_id')]]; ?>
						<? echo $result[csf('email')]; ?>
						<? echo $result[csf('website')];
					}
					?>
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:16px"><strong><u>Item Issue Requisition/Demand</u></strong></center></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td width="160"><strong>Indent No:</strong></td>
                <td width="160px"><? echo $dataArray[0][csf('itemissue_req_sys_id')]; ?></td>
                <td width="160"><strong>Indent Date:</strong></td>
                <td width="160px"><? echo change_date_format($dataArray[0][csf('indent_date')]); ?></td>
                <td width="160"><strong>Required Date:</strong></td>
                <td width="160px"><? echo change_date_format($dataArray[0][csf('required_date')]); ?></td>
            </tr>
            <tr>
                <td width="180"><strong>Location:</strong></td>
                <td width="160px"><? echo $location[$dataArray[0][csf('location_id')]]; ?></td>
                <td width="180"><strong>Division:</strong></td>
                <td width="160px"><? echo $division[$dataArray[0][csf('division_id')]]; ?></td>
                <td width="180"><strong>Department:</strong></td>
                <td width="160px"><? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
            </tr>

            <tr>
                <td width="180"><strong>Section:</strong></td>
                <td width="160px"><? echo $section_library[$dataArray[0][csf('section_id')]]; ?></td>
                <td width="180"><strong>Sub Section:</strong></td>
                <td width="160px"><? echo $dataArray[0][csf('sub_section_id')]; ?></td>
                <td width="180"><strong>Delivery Point:</strong></td>
                <td width="160px"><? echo $dataArray[0][csf('delivery_point')]; ?></td>
            </tr>
            <tr>
                
                <td width="160"><strong>Store:</strong></td>
                <td width="160px"><? echo $store[$data[3]]; ?></td>

                <td width="160"><strong>Remarks: </strong></td>
                <td colspan="4" ><? echo $dataArray[0][csf('remarks')]; ?></td>

            </tr>
            <tr>
                <td><strong>Bar Code:</strong></td>
                <td colspan="4" id="barcode_img"></td>
                <td style="text-align:center; color:#FF0000; font-weight:bold; font-size:16px;"><? if($data[4]==1) echo "Approved"; else echo "&nbsp;"; ?></td>
            </tr>
        </table>
    </div>
			
    <div style="width:100%; margin-top:10px;">
		<?

		// $sql_dtls = "SELECT B.ID, B.MST_ID, B.ITEM_ACCOUNT, B.ITEM_GROUP, B.ITEM_SUB_GROUP, B.ITEM_DESCRIPTION, B.ITEM_SIZE, B.UNIT_OF_MEASURE, B.CURRENT_STOCK, B.REQ_FOR, B.REQ_QTY, B.RTN_QNTY, B.REMARKS, B.PRODUCT_ID 
		// FROM INV_ITEM_ISSUE_REQUISITION_MST A, INV_ITEMISSUE_REQUISITION_DTLS B WHERE A.ID=B.MST_ID AND A.ID='$data[1]' AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0";

		$sql_dtls = " SELECT B.ID, B.MST_ID, B.ITEM_ACCOUNT, B.ITEM_GROUP, B.ITEM_SUB_GROUP, B.ITEM_DESCRIPTION, B.ITEM_SIZE, B.UNIT_OF_MEASURE, B.CURRENT_STOCK, B.REQ_FOR,
		B.REQ_QTY, B.RTN_QNTY, B.REMARKS, B.PRODUCT_ID ,c.ITEM_NUMBER,c.ITEM_CODE 
		FROM INV_ITEM_ISSUE_REQUISITION_MST A, INV_ITEMISSUE_REQUISITION_DTLS B , product_details_master c
		WHERE A.ID=B.MST_ID and B.PRODUCT_ID = c.id
		AND A.ID='$data[1]' AND a.STATUS_ACTIVE=1 AND a.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND c.STATUS_ACTIVE in(1,3) AND c.IS_DELETED=0 ";

		//echo $sql_dtls;
		$item_group = return_library_array("select id,item_name from lib_item_group", 'id', 'item_name');
		$nameArray = sql_select($sql_dtls);


		?>
        <table cellspacing="0" width="1040" border="1" rules="all" class="rpt_table">
            <thead bgcolor="#dddddd" style="font-size:13px">
                <tr>
                    <th width="20">SL</th>
					<th width="100">Item Category</th>
                    <th width="100">Item Number</th>
                    <th width="130">Item Code</th>
                    <th width="100">Item Sub Group</th>
                    <th width="160">Item Description</th>
                    <th width="90">Required For</th>
                    <th width="90">UOM</th>
                    <th width="100">Req. Qty.</th>
                    <?
	                if(($user_lavel==2 || $user_lavel==1) && $select_status==1)
					{
					?>
                    <th width="60">Stock</th>
                    <? 
					}
					?>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
			<?
			$con = connect();
			$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (54)");
			oci_commit($con);

			$prod_id_arr = array();
			foreach ($nameArray as $selectResult) 
			{
				$prod_id_arr[$selectResult[csf('product_id')]] = $selectResult[csf('product_id')];
			}
			if (count($prod_id_arr)>0)
			{
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 54, 2, $prod_id_arr, $empty_arr);
				$prodDataArr=sql_select("SELECT A.ID, A.ITEM_CATEGORY_ID
				FROM PRODUCT_DETAILS_MASTER A, GBL_TEMP_ENGINE B WHERE A.ID=B.REF_VAL AND B.USER_ID= $user_id AND B.ENTRY_FORM=54 AND B.REF_FROM=2");

				foreach($prodDataArr as $row)
				{
					$product_arr[$row['ID']]['category_name']=$row['ITEM_CATEGORY_ID'];
				}
				unset($prodDataArr);
			}

			execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (54)");
			oci_commit($con);
			disconnect($con);
			//$item_sub_group = return_library_array("select sub_group_code,sub_group_name from product_details_master", 'sub_group_code', 'sub_group_name');
			$user_arr = return_library_array("select user_name,id from user_passwd", 'id', 'user_name');
			$user = "select id,inserted_by,updated_by from inv_item_issue_requisition_mst where id='$data[1]'";
			//$item_category_arr = return_library_array("select id,item_category_id from product_details_master", 'id', 'item_category_id');
			$user_print = sql_select($user);
			$i = 1;
			foreach ($nameArray as $selectResult) 
			{
				?>
                <tr>
                    <td align="center"><? echo $i; ?></td>
					<td style="word-break: break-all;"><?
					echo $item_category[$product_arr[$selectResult["PRODUCT_ID"]]['category_name']]; 
					 ?></td>
                    <td align="center"><? echo  $selectResult["ITEM_NUMBER"];?></td> 
                    <td align="center"><? echo  $selectResult["ITEM_CODE"];?></td> 
                    <td style="word-break: break-all;"><? echo $selectResult["ITEM_SUB_GROUP"]; ?></td>
                    <td style="word-break: break-all;"><? echo $selectResult["ITEM_DESCRIPTION"]; ?></td>
                    <td><? echo $selectResult[csf("req_for")]; ?></td>
                    <td align="center"><? echo $unit_of_measurement[$selectResult["UNIT_OF_MEASURE"]]; ?></td>
                    <td align="right"><? echo $selectResult["REQ_QTY"]; ?></td>

                    <?
	                if(($user_lavel==2 || $user_lavel==1) && $select_status==1)
					{
					?>
                    <td align="right"><? echo $selectResult["CURRENT_STOCK"]; ?></td>
                    <? 
					}
					?>
                    <td align="right"><? echo $selectResult["REMARKS"]; ?></td>
                </tr>
				<? 
				$i++;
			} 
			?>
            </tbody>
        </table>
       <!-- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
	   <div style="margin-top:15px">
			<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:1000px;text-align:center;" rules="all">
				<label style="font-size:16px"><b> Raised By</b></label>
				<thead bgcolor="#dddddd">
				<tr style="font-weight:bold">
					<th style="font-size:15px" width="20">SL</th>
					<th style="font-size:15px" width="250">Name</th>
					<th style="font-size:15px" width="200">Position</th>
				</tr>
				</thead>
				<tr>
					<td width="20"><? echo "1"; ?></td>
					<td width="250"><? echo $user_name; ?></td>
					<td width="200"><?echo $user_designation ?></td>
				</tr>
			</table>
		</div>

        <?
		
		$lib_designation_arr=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");
		$user_lib_designation_arr=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
		$user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

		$nameArray_approved=sql_select("SELECT b.approved_by,min(b.approved_date) as approved_date from approval_history b where b.mst_id=$data[1] and b.entry_form=26 group by mst_id, approved_by order by approved_date");
		$unapprove_data_array=sql_select("SELECT b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date,b.approved_no from   approval_history b where b.mst_id=$data[1] and b.entry_form=26 order by approved_no,approved_date");

		if(count($nameArray_approved)>0)
		{
			?>
			<br>
				<table  width="1000" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
					<thead bgcolor="#dddddd">
						<tr style="border:1px solid black;">
							<th colspan="5" style="border:1px solid black;">Approval Status</th>
						</tr>
						<tr style="border:1px solid black;">
						<th width="3%" style="border:1px solid black;">Sl</th>
						<th width="40%" style="border:1px solid black;">Name</th>
						<th width="30%" style="border:1px solid black;">Designation</th>
						<th width="27%" style="border:1px solid black;">Approval Date</th>
						</tr>
					</thead>
					<tbody>
					<?
					$i=1;
					foreach($nameArray_approved as $row){
					?>
					<tr style="border:1px solid black;">
						<td width="3%" style="border:1px solid black;"><? echo $i;?></td>
						<td width="40%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
						<td width="30%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
						<td width="27%" style="border:1px solid black;text-align:center"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')]));?></td>
					
						</tr>
						<?
						$i++;
					}
						?>
					</tbody>
				</table>
			<br/>
			<?
			
		}
		?>
    </div>
  
    <script type="text/javascript" src="../js/jquery.js"></script>
    <script type="text/javascript" src="../js/jquerybarcode.js"></script>
    <script>

        function generateBarcode(valuess) {

            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();
            var settings = {
                output: renderer,
                bgColor: '#FFFFFF',
                color: '#000000',
                barWidth: 1,
                barHeight: 30,
                moduleSize: 5,
                posX: 10,
                posY: 20,
                addQuietZone: 1
            };
            $("#barcode_img").html('11');
            value = {code: value, rect: false};
            $("#barcode_img").show().barcode(value, btype, settings);

        }

        generateBarcode('<? echo $data[2]; ?>');

    </script>
	<?
	exit();
}

if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=6 and report_id=156 and is_deleted=0 and status_active=1");
   	$printButton=explode(',',$print_report_format);
    //print_r($printButton);
	foreach($printButton as $id){

		if($id==66)$buttonHtml.='<input type="button" style="width:80px;" id="btn_Print2"  onClick="fnc_print(2)" class="formbutton printReport" name="btn_Print2" value="Print 2" /> ';
		if($id==78)$buttonHtml.='<input type="button" style="width:80px;" id="btn_Print"  onClick="fnc_print(1)" class="formbutton printReport" name="btn_Print" value="Print" /> ';
		if($id==85)$buttonHtml.='<input type="button" style="width:80px;" id="btn_Print3"  onClick="fnc_print(3)" class="formbutton printReport" name="btn_Print3" value="Print 3" /> ';

		if($id==137)$buttonHtml.='<input type="button" style="width:80px;" id="btn_Print4"  onClick="fnc_print(4)" class="formbutton printReport" name="btn_Print4" value="Print 4" /> ';
		if($id==129)$buttonHtml.='<input type="button" style="width:80px;" id="btn_Print5"  onClick="fnc_print(5)" class="formbutton printReport" name="btn_Print5" value="Print 5" /> ';
		
	}

   echo "document.getElementById('button_data_panel').innerHTML = '".$buttonHtml."';\n";
    exit();
}
?>