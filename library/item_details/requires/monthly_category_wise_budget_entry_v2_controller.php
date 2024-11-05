<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];



$lib_department_name=return_library_array("select id, department_name  from lib_department where status_active = 1 and is_deleted = 0","id","department_name");
$lib_section_name=return_library_array("select id, section_name  from lib_section where status_active = 1 and is_deleted = 0","id","section_name");


if ($action=="load_drop_down_com_profit")
{
	echo create_drop_down( "cbo_profit_center_1", 100, "select a.id, a.profit_center_name from  lib_profit_center a where a.status_active =1 and a.is_deleted=0 and a.company_id='$data' order by a.profit_center_name","id,profit_center_name", 1, "-- Select --", $selected, "",0 );
	exit();
}

if ($action == "load_drop_down_division_popup") 
{
	echo create_drop_down("cbo_division_1", 90, "select id,division_name from lib_division where company_id=$data and is_deleted=0  and status_active=1", "id,division_name", 1, "-- Select --", $selected, "fn_load_department(this.value,1)");
	//load_drop_down( 'requires/monthly_category_wise_budget_entry_v2_controller', this.value, 'load_drop_down_department_popup','department_td_popup');
	exit();
}

if ($action == "department_list") 
{
	$department_sql="select id,department_name from lib_department where division_id=$data and is_deleted=0 and status_active=1";
	$department_sql_result=sql_select($department_sql);
	$department_arr=array();
	foreach($department_sql_result as $row)
	{
		$department_arr[$row[csf('id')]]=$row[csf('department_name')];
	}
	$jsDepartment_arr= json_encode($department_arr);
	echo $jsDepartment_arr;
	die();
}

if ($action == "section_list") 
{
	$section_sql="select id,section_name from lib_section where department_id=$data and is_deleted=0 and status_active=1";
	$section_sql_result=sql_select($section_sql);
	$section_arr=array();
	foreach($section_sql_result as $row)
	{
		$section_arr[$row[csf('id')]]=$row[csf('section_name')];
	}
	$jsSection_arr= json_encode($section_arr);
	echo $jsSection_arr;
	die();
}

/*if ($action == "load_drop_down_department_popup") {
	echo create_drop_down("cbo_department_1", 100, "select id,department_name from lib_department where division_id=$data and is_deleted=0 and status_active=1", "id,department_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/monthly_category_wise_budget_entry_v2_controller', this.value, 'load_drop_down_section_popup','section_td_popup');");
	exit();
}

if ($action == "load_drop_down_section_popup") {
	echo create_drop_down("cbo_section_1", 100, "select id,section_name from lib_section where department_id=$data and is_deleted=0 and status_active=1", "id,section_name", 1, "-- Select --", $selected, "");
	exit();
}*/

if ($action=="save_update_delete")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
	
	$update_id = str_replace("'", "", $txt_system_id);
	
	$dtlsRow_array = explode(',', $dtlrow);
	$all_profit_center_arr=array();
	foreach ($dtlsRow_array as $i) {
		$cbo_profit_center = "cbo_profit_center_" . $i;
		$all_profit_center_arr[str_replace("'", "", $$cbo_profit_center)]=str_replace("'", "", $$cbo_profit_center);          
	} 
	$apply_month_arr=explode('-', str_replace("'", "",$txt_from_date));
	$apply_month=$apply_month_arr[1]."-".$apply_month_arr[2];
	$update_cond="";
	if($update_id>0) $update_cond=" and a.id <> $update_id";
	$sql_duplicate="select a.id from LIB_CATEGORY_BUDGET_ENTRY_MST a, LIB_CATEGORY_BUDGET_ENTRY_DTLS b where a.ID=b.MST_ID and a.status_active=1 and b.status_active=1 and a.status_id=1 and company_id=$cbo_company_name and b.PROFIT_CENTER in(".implode(",",$all_profit_center_arr).") and to_char(a.applying_date_from, 'Mon-YYYY')='$apply_month' $update_cond";
	$sql_duplicate_result=sql_select($sql_duplicate);
	if(count($sql_duplicate_result)>0)
	{
		echo "20** Duplicate Profit Center Not Allowed In Same Applying Period";die;
	}
	
	
	
    if ($operation==0)  // Insert Here
    {        
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }
		
		
        $id = return_next_id("id", "LIB_CATEGORY_BUDGET_ENTRY_MST", 1);
        $field_array_mst = "id, company_id, applying_date_from, applying_date_to, currency_id, status_id, remarks, inserted_user, insert_date, status_active, is_deleted";
        $data_array_mst = "(" . $id . "," . $cbo_company_name . "," . $txt_from_date . "," . $txt_to_date . "," . $cbo_currency_name . "," . $cbo_status_name . "," . $txt_remarks . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";

        $field_array_dtls = "id, mst_id, profit_center,division, department, section, budget_amount, category_mix_id, inserted_user, insert_date, status_active, is_deleted";
        $dtlsRow_array = explode(',', $dtlrow);
        $data_array_dtls = "";
        $dtls_id = return_next_id("id", "LIB_CATEGORY_BUDGET_ENTRY_DTLS", 1);
        foreach ($dtlsRow_array as $i) {
            $cbo_profit_center = "cbo_profit_center_" . $i;
            $cbo_division = "cbo_division_" . $i;
            $cbo_department = "cbo_department_" . $i;
            $cbo_section = "cbo_section_" . $i;
            $amount = "txt_amount_" . $i;
            $txt_amount_string = "txt_amount_string_" . $i;
            if (str_replace("'", "", $$cbo_profit_center) > 0 && str_replace("'", "", $$amount) > 0) {
                if ($data_array_dtls != "") $data_array_dtls .= ",";
                $data_array_dtls .= "(" . $dtls_id . "," . $id . "," . $$cbo_profit_center . "," . $$cbo_division . "," . $$cbo_department . "," . $$cbo_section . "," . $$amount . "," . $$txt_amount_string . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";
                $dtls_id += 1;
            }           
        }            
        // echo "10**insert into LIB_CATEGORY_BUDGET_ENTRY_DTLS ($field_array_dtls) values $data_array_dtls";   disconnect($con);die;
        // echo "10**insert into LIB_CATEGORY_BUDGET_ENTRY_MST ($field_array_mst) values $data_array_mst";   disconnect($con);die;

        $rID = sql_insert("LIB_CATEGORY_BUDGET_ENTRY_MST", $field_array_mst, $data_array_mst, 1);
        $rID1 = sql_insert("LIB_CATEGORY_BUDGET_ENTRY_DTLS", $field_array_dtls, $data_array_dtls, 1);

        // echo "10**$rID=$rID1";disconnect($con);  disconnect($con);die;

        if ($db_type == 0) {
            if ($rID && $rID1) {
                mysql_query("COMMIT");
                echo "0**" . $id;
            } else {
                mysql_query("ROLLBACK");
                echo "10**0";
            }
        } else if ($db_type == 2 || $db_type == 1) {
            if ($rID && $rID1) {
                oci_commit($con);
                echo "0**" . $id;
            } else {
                oci_rollback($con);
                echo "10**0";
            }
        }
        
        disconnect($con);
        die;
    }
    elseif ($operation == 1)   // Update Here
    {
            $con = connect();
            if ($db_type == 0) {
                mysql_query("BEGIN");
            }         
            
            $field_array_mst_update = "remarks*status_id*updated_user*updated_date";
            $data_array_mst_update = "" . $txt_remarks . "*" . $cbo_status_name . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

            $field_array_dtls_insert = "id, mst_id, profit_center,division, department, section, budget_amount, category_mix_id, inserted_user, insert_date, status_active, is_deleted";
            $field_array_dtls_update = "profit_center*division*department*section*budget_amount*category_mix_id*updated_user*updated_date";
            $field_array_dtls_delete = "status_active*is_deleted*updated_user*updated_date";
            $dtlsRow_array = explode(',', $dtlrow);
            $data_array_dtls_insert = "";
            $get_all_id = return_library_array("select id, id as dtls_id from lib_category_budget_entry_dtls where mst_id = $update_id and is_deleted = 0 and status_Active = 1", "id", "dtls_id");

            $dtls_id_insert = return_next_id("id", "lib_category_budget_entry_dtls", 1);
            $deleted_id = []; $data_array_dtls_update = []; $data_array_dtls_delete = []; $errorrRept56 = "";
            foreach ($dtlsRow_array as $i){
                $cbo_profit_center = "cbo_profit_center_" . $i;
                $cbo_division = "cbo_division_" . $i;
                $cbo_department = "cbo_department_" . $i;
                $cbo_section = "cbo_section_" . $i;
                $amount = "txt_amount_".$i;
                $dtls_update_id = "dtls_id_".$i;
                $txt_amount_string = "txt_amount_string_" . $i;
                // echo  "10**".$$dtls_update_id ;disconnect($con);die;

                if(str_replace("'", "", $$dtls_update_id) != ""){
                     if(in_array(str_replace("'", "", $$dtls_update_id), $get_all_id)){
                         $update_id_arr[]=str_replace("'", "", $$dtls_update_id);
                         $deleted_id[str_replace("'", "", $$dtls_update_id)] = str_replace("'", "", $$dtls_update_id);
                         $data_array_dtls_update[str_replace("'", "", $$dtls_update_id)]=explode("*",("".$$cbo_profit_center."*".$$cbo_division."*".$$cbo_department."*".$$cbo_section."*".$$amount."*".$$txt_amount_string."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
                     }
                }else{
                    if(str_replace("'", "", $$cbo_profit_center) > 0 && str_replace("'", "", $$amount) > 0 ){
                        if ($data_array_dtls_insert != "") $data_array_dtls_insert .=",";
                        $data_array_dtls_insert .= "(". $dtls_id_insert .",". $update_id .",". $$cbo_profit_center .",". $$cbo_division .",". $$cbo_department .",". $$cbo_section .",". $$amount .",". $$txt_amount_string ."," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";
                        $dtls_id_insert += 1;
                    }
                }
               
            }
           
            $rID3 = 1;
            $deleted_id_com = array_diff($get_all_id, $deleted_id);
            if(count($deleted_id_com) > 0){
               foreach ($deleted_id_com as $id){
                   $delete_id_arr[]=$id;
                   $data_array_dtls_delete[$id]=explode("*",("0*1*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
               }
            }

            $rID = sql_update("LIB_CATEGORY_BUDGET_ENTRY_MST", $field_array_mst_update, $data_array_mst_update, "id", "" .$update_id."");
            $rID1 = 1;

            // echo "10**".count($data_array_dtls_update);disconnect($con);die;
            if(count($data_array_dtls_update) > 0){
                $rID1=execute_query(bulk_update_sql_statement("LIB_CATEGORY_BUDGET_ENTRY_DTLS","id",$field_array_dtls_update,$data_array_dtls_update,$update_id_arr),0);
            }
            // echo "10**".bulk_update_sql_statement("LIB_CATEGORY_BUDGET_ENTRY_DTLS","id",$field_array_dtls_update,$data_array_dtls_update,$update_id_arr);disconnect($con);die;
            // echo "10**".$data_array_dtls_update;disconnect($con);die;
            $rID2 = 1;
            if($data_array_dtls_insert != ""){
                $rID2 = sql_insert("LIB_CATEGORY_BUDGET_ENTRY_DTLS", $field_array_dtls_insert, $data_array_dtls_insert, 1);
            }
            if(count($data_array_dtls_delete) > 0){
                $rID3=execute_query(bulk_update_sql_statement("LIB_CATEGORY_BUDGET_ENTRY_DTLS","id",$field_array_dtls_delete,$data_array_dtls_delete,$delete_id_arr),0);
            }

            // echo "10**".$rID1."_".$rID2."_".$rID3; disconnect($con);die;

            if ($db_type == 0) {
                if ($rID && $rID1 && $rID2 && $rID3) {
                    mysql_query("COMMIT");
                    echo "1**".$update_id;
                } else {
                    mysql_query("ROLLBACK");
                    echo "10**0";
                }
            }
            if ($db_type == 2 || $db_type == 1) {
                if ($rID && $rID1 && $rID2 && $rID3) {
                    oci_commit($con);
                    echo "1**".$update_id;
                } else {
                    oci_rollback($con);
                    echo "10**0";
                }
            }
            disconnect($con);
            die;
    }
    elseif ($operation == 2)   // Delete Here
    {
        $update_id = str_replace("'", "", $txt_system_id);
        $field_array_mst_update = 'status_active*is_deleted';
        $data_array_mst_update = '0*1';
        $rID = sql_update("LIB_CATEGORY_BUDGET_ENTRY_MST", $field_array_mst_update, $data_array_mst_update, "id", $update_id,1);
        $rID1 = sql_update("LIB_CATEGORY_BUDGET_ENTRY_DTLS", $field_array_mst_update, $data_array_mst_update, "mst_id", $update_id,1);
        if ($db_type == 0) {
            if ($rID && $rID1) {
                mysql_query("COMMIT");
                echo "2**1";
            } else {
                mysql_query("ROLLBACK");
                echo "10**0";
            }
        }
        if ($db_type == 2 || $db_type == 1) {
            if ($rID && $rID1) {
                oci_commit($con);
                echo "2**1";
            } else {
                oci_rollback($con);
                echo "10**0";
            }
        }
        disconnect($con);
        die;
    }
}

if($action == "load_php_data_to_form"){
    $sql_data = sql_select("SELECT id, company_id, to_char(applying_date_from, 'dd-mm-YYYY') as applying_date_from, to_char(applying_date_to, 'dd-mm-YYYY') as applying_date_to, currency_id, remarks, status_id from LIB_CATEGORY_BUDGET_ENTRY_MST where id = $data and is_deleted=0 and status_active = 1  order by id desc");
    if(count($sql_data) > 0){
        echo "$('#txt_system_id').val(".$sql_data[0][csf('id')].");\n";
        echo "$('#cbo_company_name').val(".$sql_data[0][csf('company_id')].");\n";
        echo "$('#cbo_currency_name').val(".$sql_data[0][csf('currency_id')].");\n";
        echo "$('#txt_from_date').val('".$sql_data[0][csf('applying_date_from')]."');\n";
        echo "$('#txt_to_date').val('".$sql_data[0][csf('applying_date_to')]."');\n";
        echo "$('#txt_remarks').val('".$sql_data[0][csf('remarks')]."');\n";
        echo "$('#cbo_status_name').val('".$sql_data[0][csf('status_id')]."');\n";
        echo "disable_fields('cbo_company_name*txt_from_date*txt_to_date');\n";
        echo "set_button_status(1, permission, 'fnc_category_wise_budget',1);\n";
    }
    $firstday = change_date_format($sql_data[0][csf('applying_date_from')]);
    $lastday = change_date_format($sql_data[0][csf('applying_date_to')]);
    $com_id=$sql_data[0][csf('company_id')];
    $sql_data_dtls = sql_select("select id, profit_center, division, department, section, budget_amount, category_mix_id from lib_category_budget_entry_dtls where status_active = 1 and is_deleted = 0 and mst_id = $data order by id");
    $lib_profit_center=return_library_array("select id, profit_center_name from lib_profit_center where status_active = 1 and is_deleted = 0 and COMPANY_ID=$com_id","id","profit_center_name");
    $lib_division_name=return_library_array("select id, division_name  from lib_division where status_active = 1 and is_deleted = 0 and COMPANY_ID=$com_id","id","division_name");
    $data_appender = "";
    if(count($sql_data_dtls) > 0)
    {
        $i = 1;
        foreach ($sql_data_dtls as $k => $row){
            $data_appender .= '<tr id="row_'.$i.'" class="row"><td align="center" class="sl_col">'.$i.'</td>';
           
            $data_appender .= '<td align="center" id="com_profit_td">' . create_drop_down("cbo_profit_center_$i", 100, $lib_profit_center, "", 1, "-- Select Category --", $row[csf('profit_center')], "") . '</td>';
             $data_appender .= '<td align="center" id="com_division_td">' . create_drop_down("cbo_division_$i", 100, $lib_division_name, "", 1, "-- Select Category --", $row[csf('division')], "fn_load_department(this.value,".$i.")") . '</td>';
             $data_appender .= '<td align="center" id="department_td_popup">' . create_drop_down("cbo_department_$i", 100, $lib_department_name, "", 1, "-- Select Category --", $row[csf('department')], "fn_load_section(this.value,".$i.")") . '</td>';
             $data_appender .= '<td align="center" id="section_td_popup">' . create_drop_down("cbo_section_$i", 100, $lib_section_name, "", 1, "-- Select Category --", $row[csf('section')], "") . '</td>';
            $data_appender .= '<td align="center"><input type="text" onClick="fn_budge_brk_amt('.$i.')" name="txt_amount_'.$i.'" id="txt_amount_'.$i.'" style="width:100px" class="text_boxes_numeric"  value="'.$row[csf('budget_amount')].'" placeholder="Write"/><input type="hidden" name="txt_amount_string_'.$i.'" id="txt_amount_string_'.$i.'" value="'.$row[csf('category_mix_id')].'" /></td>';
            $data_appender .= '<td align="center"><input type="hidden" name="dtls_id_'.$i.'" id="dtls_id_'.$i.'" value="'.$row[csf('id')].'"> <input type="hidden" name="txt_amount_string_'.$i.'" id="txt_amount_string_'.$i.'" value="'.$row[csf('category_mix_id')].'">';          
            if (count($sql_data_dtls) == 1) {
                $data_appender .= '<input type="button" id="increase_' . $i . '" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onclick="fnc_addRow(' . $i . ')">';
                $data_appender .= '<input type="button" id="decrease_' . $i . '" name="decrease[]" style="width:30px; cursor: not-allowed; opacity: .4;" class="formbuttonplasminus" value="-" onclick="fnc_removeRow(' . $i . ')">';
            } else if (count($sql_data_dtls) == $i) {
                $data_appender .= '<input type="button" id="increase_' . $i . '" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onclick="fnc_addRow(' . $i . ')">';
                $data_appender .= '<input type="button" id="decrease_' . $i . '" name="decrease[]" style="width:30px;" class="formbuttonplasminus" value="-" onclick="fnc_removeRow(' . $i . ')">';
            } else {
                $data_appender .= '<input type="button" id="increase_' . $i . '" name="increase[]" style="width:30px; cursor: not-allowed; opacity: .4;" class="formbuttonplasminus" value="+" onclick="fnc_addRow(' . $i . ')">';
                $data_appender .= '<input type="button" id="decrease_' . $i . '" name="decrease[]" style="width:30px;" class="formbuttonplasminus" value="-" onclick="fnc_removeRow(' . $i . ')">';
            }
                       
            $data_appender .='</td></tr>';
            $i++;
        }
    }
    else{
        $i = 1;
        $data_appender .= '<tr id="row_'.$i.'" class="row"><td align="center" class="sl_col">'.$i.'</td>';
        $data_appender .= '<td align="center">'.create_drop_down( "cbo_category_name_$i", 190, $general_item_category, "", 1, "-- Select Category", "", "").'</td>';
        $data_appender .= '<td align="center"><input type="text" name="txt_amount_'.$i.'" id="txt_amount_'.$i.'" style="width:180px" class="text_boxes_numeric"  value="" placeholder="Write"/></td>';
        $data_appender .= '<td align="center"><input type="hidden" name="dtls_id_'.$i.'" id="dtls_id_'.$i.'" value="">';
        $data_appender .= '<input type="button" id="increase_'.$i.'" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onclick="fnc_addRow('.$i.')">';
        $data_appender .= '<input type="button" id="decrease_'.$i.'" name="decrease[]" style="width:30px; cursor: not-allowed; opacity: .4;" class="formbuttonplasminus" value="-" onclick="fnc_removeRow('.$i.')">';
        $data_appender .='</td></tr>';
    }
    echo  "$('#table_body_1').html('".$data_appender."');\n";
    exit();
}

if ($action == "budget_list_view"){
    $company_name=return_library_array("select id, company_name from lib_company where status_active = 1 and is_deleted = 0","id","company_name");
    $lib_profit_center=return_library_array("select id, profit_center_name from lib_profit_center where status_active = 1 and is_deleted = 0 and COMPANY_ID=$data","id","profit_center_name");
	$lib_division_name=return_library_array("select id, division_name  from lib_division where status_active = 1 and is_deleted = 0 and COMPANY_ID=$data","id","division_name");
    $arr = array(0=>$company_name,2=>$lib_profit_center,3=>$lib_division_name, 4=>$lib_department_name, 5=>$lib_section_name, 6=>$row_status);
    echo  create_list_view ( "list_view", "Company, Month, Profit Center,Division, Department,Section,Status", "140,80,100,100,100,100,100","790","220",0, "SELECT a.id, a.company_id, to_char(a.applying_date_from, 'Mon-YYYY') as applying_year, a.status_id, b.department, b.profit_center, b.division, b.section from LIB_CATEGORY_BUDGET_ENTRY_MST a, LIB_CATEGORY_BUDGET_ENTRY_DTLS b where a.id=b.mst_id and a.is_deleted = 0 and a.status_active = 1 and b.is_deleted = 0 and b.status_active = 1 and COMPANY_ID=$data order by a.id desc", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "company_id,0,profit_center,division,department,section,status_id", $arr , "company_id,applying_year,profit_center,division,department,section,status_id", "requires/monthly_category_wise_budget_entry_v2_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0' ) ;
    exit();
}


if($action=="budge_brk_amt")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", '', '', $unicode);
	$budge_brk_amt_arr=explode(",",$amt_string);
	$category_wise_prev_data=array();
	foreach($budge_brk_amt_arr as $break_data)
	{
		$break_data_ref=explode("_",$break_data);
		$category_wise_prev_data[$break_data_ref[0]]=$break_data_ref[1];
	}
	//echo "rrr<pre>"; print_r($category_wise_prev_data);
	?>
    <script>
		function frm_close()
		{
			var row_num=$('#tbl_amt_breakdown tbody tr').length;
			var cat_amt_data="";var tot_amount=0;
			for (var j=1; j<=row_num; j++)
			{
				var cat_id=$("#cat_id_"+j).val();
				var cat_amount=$("#txt_discount_"+j).val()*1;
				if(cat_amount>0)
				{
					if(cat_amt_data=="") cat_amt_data+=cat_id+"_"+cat_amount;
					else cat_amt_data+=","+cat_id+"_"+cat_amount;
					tot_amount+=cat_amount;
				}
			}
			//alert(cat_amt_data);
			$('#hdn_brk_amt_data').val(cat_amt_data);
			$('#hdn_tot_amt').val(tot_amount);
			
			parent.emailwindow.hide();
		}
    </script>
    <div style="width:100%">
    <input type="hidden" id="hdn_brk_amt_data" name="hdn_brk_amt_data" value="<?= $budge_brk_amt;?>" />
    <input type="hidden" id="hdn_tot_amt" name="hdn_tot_amt" value="<?= $tot_amt;?>" />
    <table width="480" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="tbl_amt_breakdown" align="left">
        <thead>
            <tr>
                <th width="50">Sl</th>
                <th width="250">Category</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
        <?
			$i=1;
			foreach($general_item_category as $cat_id=>$cat_val)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
                <tr bgcolor="<?=$bgcolor;?>">
                    <td align="center"><?= $i;?></td>
                    <td><?= $cat_val;?></td>
                    <td align="center">
                    <input type="text" id="txt_discount_<?=$i;?>" name="txt_discount_<?=$i;?>" class="text_boxes_numeric" style="width:130px;" value="<?= $category_wise_prev_data[$cat_id];?>" />
                    <input type="hidden" id="cat_id_<?=$i;?>" name="cat_id_<?=$i;?>" value="<?= $cat_id;?>" />
                    </td>
                </tr>
                <?
				$i++;
			}
		?>	
        </tbody>
    </table>
    <br>
    <div style="width:100%"><p align="center"><input type="button" id="btn_close" class="formbutton" style="width:100px;" value="Close" onClick="frm_close();" ></p></div>
    </div>
    <?
	exit();
}

