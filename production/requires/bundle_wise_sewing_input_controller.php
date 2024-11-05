<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "") {
    header("location:login.php");
    die;
}


$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

//------------------------------------------------------------------------------------------------------
$country_library = return_library_array("select id,country_name from lib_country", "id", "country_name");

if ($action == "load_drop_down_location") {
    echo create_drop_down("cbo_location", 180, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name", "id,location_name", 1, "-- Select Location --", $selected, "load_drop_down('requires/bundle_wise_sewing_input_controller', this.value, 'load_drop_down_floor', 'floor_td' );", 0);
    exit();
}

if ($action=="load_drop_down_working_location")
{
    echo create_drop_down( "cbo_working_location", 180, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",0 );
    exit();
}

if ($action == "load_drop_down_floor") {
    /*  echo create_drop_down( "cbo_floor", 180, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (9) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/bundle_wise_sewing_input_controller', $('#cbo_company_name').val()+'_'+$('#cbo_location').val()+'_'+this.value, 'load_drop_down_line', 'line_td' );",0 );
	*/
    echo create_drop_down("cbo_floor", 180, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process=5 order by floor_name", "id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/bundle_wise_sewing_input_controller', $('#cbo_emb_company').val()+'_'+$('#cbo_location').val()+'_'+this.value+'_'+$('#txt_issue_date').val(), 'load_drop_down_line', 'line_td' );", 0);

    exit();
}

if($action=="load_drop_down_location_floor_line_and_source_line")
{
    list($company_id, $location, $floor,$issue_date,$source) = explode("_", $data);

    echo create_drop_down("cbo_floor", 180, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$location' and production_process=5 order by floor_name", "id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/bundle_wise_sewing_input_controller', $('#cbo_emb_company').val()+'_'+$('#cbo_location').val()+'_'+this.value+'_'+$('#txt_issue_date').val(), 'load_drop_down_line', 'line_td' );", 0);
    echo "****";

    $nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='$company_id' and variable_list=23 and status_active=1 and is_deleted=0");

    $prod_reso_allocation = $nameArray[0][csf('auto_update')];
    $cond = "";

    if ($prod_reso_allocation == 1) {
        $line_library = return_library_array("select id,line_name,sewing_line_serial from lib_sewing_line where status_active=1 order by sewing_line_serial", "id", "line_name");
        $line_array = array();

        if ($floor == 0 && $location != 0) $cond = " and a.location_id= $location";
        if ($floor != 0) $cond = " and a.floor_id= $floor";



        if($db_type==0)
        {
            $issue_date = date("Y-m-d",strtotime($issue_date));
        }
        else
        {
            $issue_date = change_date_format(date("Y-m-d",strtotime($issue_date)),'','',1);
        }

        $cond.=" and b.pr_date='".$issue_date."'";


        if ($db_type == 0)
        {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number,a.prod_resource_num  order by a.line_number asc, a.prod_resource_num asc, a.id asc");
        }
        else if ($db_type == 2 || $db_type == 1)
        {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number,a.prod_resource_num  order by a.line_number asc, a.prod_resource_num asc, a.id asc");
        }


         $line_merge=9999;
        foreach($line_data as $row)
        {
            $line='';
            $line_number=explode(",",$row[csf('line_number')]);
            foreach($line_number as $val)
            {
                if(count($line_number)>1)
                {
                    $line_merge++;
                    $new_arr[$line_merge]=$row[csf('id')];
                }
                else
                {
                    if($new_arr[$line_library[$val]])
                    $new_arr[$line_library[$val]." "]=$row[csf('id')];
                    else
                        $new_arr[$line_library[$val]]=$row[csf('id')];
                }

                if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
            }
            $line_array[$row[csf('id')]]=$line;
        }
        //ksort($new_arr);
        foreach($new_arr as $key=>$v)
        {
            $line_array_new[$v]=$line_array[$v];
        }
        echo create_drop_down( "cbo_line_no", 180,$line_array_new,"", 1, "--- Select ---", $selected, "",0,0 );

    } else {
        if ($floor == 0 && $location != 0) $cond = " and location_name= $location";
        if ($floor != 0) $cond = " and floor_name= $floor"; else  $cond = " and floor_name like('%%')";

        echo create_drop_down("cbo_line_no", 160, "select id,line_name,sewing_line_serial from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 $cond order by sewing_line_serial", "id,line_name", 1, "--- Select ---", $selected, "", 0, 0);
    }

    echo "****";

    $user_id = $_SESSION['logic_erp']["user_id"];
    //========== user credential start ========
    $userCredential = sql_select("SELECT WORKING_UNIT_ID, unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
    $working_unit_id = $userCredential[0][csf('WORKING_UNIT_ID')];

    $working_credential_cond = "";

    if ($working_unit_id > 0)
    {
        $working_credential_cond = " and comp.id in($working_unit_id)";
    }


    $selected_company =0;// $explode_data[1];

    if ($source == 3)
    {
        if ($db_type == 0) {
             echo create_drop_down("cbo_emb_company", 180, "SELECT id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,23,party_type) order by supplier_name", "id,supplier_name", 1, "--- Select ---", $selected, "load_drop_down( 'requires/bundle_wise_sewing_input_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data(document.getElementById('cbo_company_name').value,'load_variable_settings','requires/bundle_wise_sewing_input_controller');load_html();");
        } else {
            echo create_drop_down("cbo_emb_company", 180, "SELECT a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(22,23) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select--", 0, "load_drop_down( 'requires/bundle_wise_sewing_input_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data(document.getElementById('cbo_company_name').value,'load_variable_settings','requires/bundle_wise_sewing_input_controller');load_html();");
        }
    }
    else if ($source == 1)
    {
        echo create_drop_down("cbo_emb_company", 180, "SELECT id,company_name from lib_company comp where is_deleted=0 and status_active=1 $working_credential_cond order by company_name", "id,company_name", 1, "--- Select ---", $selected_company, "load_drop_down( 'requires/bundle_wise_sewing_input_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data(this.value,'load_variable_settings','requires/bundle_wise_sewing_input_controller');load_html();fnc_company_check(document.getElementById('cbo_source').value);", 0, 0);

    }
    else
    {
        echo create_drop_down("cbo_emb_company", 180, $blank_array, "", 1, "--- Select ---", $selected, "load_drop_down( 'requires/bundle_wise_sewing_input_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data(this.value,'load_variable_settings','requires/bundle_wise_sewing_input_controller');load_html();", 0);
    }

    echo "****";

    echo create_drop_down("cbo_location", 180, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$company_id' order by location_name", "id,location_name", 1, "-- Select Location --", $selected, "load_drop_down('requires/bundle_wise_sewing_input_controller', this.value, 'load_drop_down_floor', 'floor_td' );", 0);
}


if($action=="company_wise_report_button_setting")
{
    extract($_REQUEST);

    $print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=7 and report_id=192 and is_deleted=0 and status_active=1");


    $level_access_sql = "select id, PAGE_ID, FIELD_ID,FIELD_NAME from field_level_access where COMPANY_ID='".$data."' and USER_ID='".$user_id."' and PAGE_ID=96 and FIELD_NAME='cbo_input_date' and STATUS_ACTIVE=1";
    $level_access=sql_select($level_access_sql);
    if($level_access){

        echo "$('#txt_issue_date').attr('disabled','true')" . ";\n";
    }

    //echo $print_report_format; disconnect($con); die;

    $print_report_format_arr=explode(",",$print_report_format);
    //print_r($print_report_format_arr);
    echo "$('#Print2').hide();\n";
    echo "$('#Print3').hide();\n";
    echo "$('#Print4').hide();\n";
    echo "$('#Print5').hide();\n";
    echo "$('#Print6').hide();\n";
    echo "$('#Print7').hide();\n";
    echo "$('#Print8').hide();\n";
    echo "$('#Print9').hide();\n";
    echo "$('#Print10').hide();\n";
    echo "$('#Print11').hide();\n";
    echo "$('#Print12').hide();\n";
    echo "$('#Print13').hide();\n";
    echo "$('#Print14').hide();\n";
    echo "$('#Print15').hide();\n";

    if($print_report_format != "")
    {
        foreach($print_report_format_arr as $id)
        {

            if($id==135){echo "$('#Print2').show();\n";}
            if($id==136){echo "$('#Print3').show();\n";}
            if($id==137){echo "$('#Print4').show();\n";}
            if($id==129){echo "$('#Print5').show();\n";}
            if($id==72){echo "$('#Print6').show();\n";}
            if($id==191){echo "$('#Print7').show();\n";}
            if($id==220){echo "$('#Print8').show();\n";}
            if($id==235){echo "$('#Print9').show();\n";}
            if($id==274){echo "$('#Print10').show();\n";}
            if($id==241){echo "$('#Print11').show();\n";}
            if($id==427){echo "$('#Print12').show();\n";}
            if($id==28){echo "$('#Print13').show();\n";}
            if($id==280){echo "$('#Print14').show();\n";}
            if($id==304){echo "$('#Print15').show();\n";}

        }
    }
    exit();
}


if ($action=="get_first_selected_print_report"){

	list($company_id,$mail_id,$mail_body)=explode('**',$data);

	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company_id."' and module_id=7 and report_id=192 and is_deleted=0 and status_active=1");
	$print_report_format_arr=explode(',',$print_report_format);
 ;


    if($print_report_format_arr[0] == 135){ echo "fnc_issue_print_embroidery_entry(5,1,'".$mail_id."','".$mail_body."')";}
    else if($print_report_format_arr[0] == 136){ echo "fnc_issue_print_embroidery_entry(6,1,'".$mail_id."','".$mail_body."')";}
    else if($print_report_format_arr[0] == 137){ echo "fnc_bundle_wise_input_print(0,1,'".$mail_id."','".$mail_body."')";}
    else if($print_report_format_arr[0] == 129){ echo "fnc_bundle_wise_input_print(7,1,'".$mail_id."','".$mail_body."')";}
    else if($print_report_format_arr[0] == 72){ echo "fnc_bundle_wise_input_print(8,1,'".$mail_id."','".$mail_body."')";}
    else if($print_report_format_arr[0] == 191){ echo "fnc_bundle_wise_input_print(9,1,'".$mail_id."','".$mail_body."')";}
    else if($print_report_format_arr[0] == 220){ echo "fnc_bundle_wise_input_print(10,1,'".$mail_id."','".$mail_body."')";}
    else if($print_report_format_arr[0] == 235){ echo "fnc_issue_print_embroidery_entry(7,1,'".$mail_id."','".$mail_body."')";}
    else if($print_report_format_arr[0] == 274){ echo "fnc_issue_print_embroidery_entry(8,1,'".$mail_id."','".$mail_body."')";}
    else if($print_report_format_arr[0] == 241){ echo "fnc_issue_print_embroidery_entry(11,1,'".$mail_id."','".$mail_body."')";}
    else if($print_report_format_arr[0] == 427){ echo "fnc_bundle_wise_input_print(12,1,'".$mail_id."','".$mail_body."')";}

	exit();
}


if ($action == "load_drop_down_line") {

    list($company_id, $location, $floor,$issue_date) = explode("_", $data);

    $nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='$company_id' and variable_list=23 and status_active=1 and is_deleted=0");

    $prod_reso_allocation = $nameArray[0][csf('auto_update')];
    $cond = "";

    if ($prod_reso_allocation == 1) {
        $line_library = return_library_array("select id,line_name,sewing_line_serial from lib_sewing_line where status_active=1 order by sewing_line_serial", "id", "line_name");
        $line_array = array();

        if ($floor == 0 && $location != 0) $cond = " and a.location_id= $location";
        if ($floor != 0) $cond = " and a.floor_id= $floor";



        if($db_type==0)
        {
            $issue_date = date("Y-m-d",strtotime($issue_date));
        }
        else
        {
            $issue_date = change_date_format(date("Y-m-d",strtotime($issue_date)),'','',1);
        }

        $cond.=" and b.pr_date='".$issue_date."'";


        if ($db_type == 0)
        {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number,a.prod_resource_num  order by a.line_number asc, a.prod_resource_num asc, a.id asc");
        }
        else if ($db_type == 2 || $db_type == 1)
        {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number,a.prod_resource_num  order by a.line_number asc, a.prod_resource_num asc, a.id asc");
        }


         $line_merge=9999;
        foreach($line_data as $row)
        {
            $line='';
            $line_number=explode(",",$row[csf('line_number')]);
            foreach($line_number as $val)
            {
                if(count($line_number)>1)
                {
                    $line_merge++;
                    $new_arr[$line_merge]=$row[csf('id')];
                }
                else
                {
                    if($new_arr[$line_library[$val]])
                    $new_arr[$line_library[$val]." "]=$row[csf('id')];
                    else
                        $new_arr[$line_library[$val]]=$row[csf('id')];
                }

                if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
            }
            $line_array[$row[csf('id')]]=$line;
        }
        //ksort($new_arr);
        foreach($new_arr as $key=>$v)
        {
            $line_array_new[$v]=$line_array[$v];
        }
        echo create_drop_down( "cbo_line_no", 180,$line_array_new,"", 1, "--- Select ---", $selected, "",0,0 );

    } else {
        if ($floor == 0 && $location != 0) $cond = " and location_name= $location";
        if ($floor != 0) $cond = " and floor_name= $floor"; else  $cond = " and floor_name like('%%')";

        echo create_drop_down("cbo_line_no", 160, "select id,line_name,sewing_line_serial from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 $cond order by sewing_line_serial", "id,line_name", 1, "--- Select ---", $selected, "", 0, 0);
    }
    exit();
}

if ($action=="load_drop_down_line_no")
{
	list($company_id,$location,$floor,$issue_date)=explode("_",$data);
	// echo $data; die;

	$nameArray= sql_select("select id, auto_update from variable_settings_production where company_name='$company_id' and variable_list=23 and status_active=1 and is_deleted=0");

	$prod_reso_allocation = $nameArray[0][csf('auto_update')];
	$cond="";
	if($prod_reso_allocation==1)
	{
		$line_library=return_library_array( "select id,line_name,sewing_line_serial from lib_sewing_line order by sewing_line_serial", "id", "line_name"  );
		$line_array=array();

		if( $floor==0 && $location!=0 ) $cond = " and a.location_id= $location";
		if( $floor!=0 ) $cond = " and a.floor_id= $floor";

		if($db_type==0) $issue_date = date("Y-m-d",strtotime($issue_date));
		else $issue_date = change_date_format(date("Y-m-d",strtotime($issue_date)),'','',1);

		$cond.=" and b.pr_date='".$issue_date."'";

		if($db_type==0)
		{
			$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number,a.prod_resource_num order by a.prod_resource_num asc, a.id asc");
		}
		else if($db_type==2 || $db_type==1)
		{
			$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number,a.prod_resource_num  order by  a.prod_resource_num,a.id asc");
		}
		 $line_merge=9999;
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if(count($line_number)>1)
				{
					$line_merge++;
					$new_arr[$line_merge]=$row[csf('id')];
				}
				else
					$new_arr[$line_library[$val]]=$row[csf('id')];

				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_array[$row[csf('id')]]=$line;
		}
		ksort($new_arr);
		foreach($new_arr as $key=>$v)
		{
			$line_array_new[$v]=$line_array[$v];
		}
		echo create_drop_down( "cbo_line_no", 100,$line_array_new,"", 1, "--- Select ---", $selected, "",0,0 );
	}
	else
	{
		if( $floor==0 && $location!=0 ) $cond = " and location_name= $location";
		if( $floor!=0 ) $cond = " and floor_name= $floor"; else  $cond = " and floor_name like('%%')";

		echo create_drop_down( "cbo_line_no", 100, "select id,line_name,sewing_line_serial from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 $cond order by sewing_line_serial","id,line_name", 1, "--- Select ---", $selected, "",0,0 );
	}
	exit();
}

if ($action == "load_variable_settings") {
    echo "$('#sewing_production_variable').val(0);\n";
    $sql_result = sql_select("select printing_emb_production,production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
    foreach ($sql_result as $result) {
        echo "$('#sewing_production_variable').val(" . $result[csf("printing_emb_production")] . ");\n";
        echo "$('#styleOrOrderWisw').val(" . $result[csf("production_entry")] . ");\n";
    }

    $delivery_basis = return_field_value("cut_panel_delevery", "variable_settings_production", "company_name=$data and variable_list=32 and status_active=1 and is_deleted=0");
    if ($delivery_basis == 3 || $delivery_basis == 2 || $delivery_basis == "") {$delivery_basis = 3;}else {$delivery_basis = 1;}
    // echo $delivery_basis;
    echo "$('#delivery_basis').val(" . $delivery_basis . ");\n";

    echo "$('#hidden_variable_cntl').val('0');\n";
    echo "$('#hidden_preceding_process').val('0');\n";
    $control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=28 and company_name='$data'");
    if(count($control_and_preceding)>0)
    {
      echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf("is_control")]."');\n";
      echo "$('#hidden_preceding_process').val('".$control_and_preceding[0][csf("preceding_page_id")]."');\n";
    }

	echo "$('#wip_valuation_for_accounts').val(0);\n";
	$wip_valuation_for_accounts = return_field_value("allow_fin_fab_rcv", "variable_settings_production", "company_name=$data and variable_list=76 and status_active=1 and is_deleted=0");
	echo "$('#wip_valuation_for_accounts').val($wip_valuation_for_accounts);\n";
	if($wip_valuation_for_accounts==1)
	{
		echo "$('#wip_valuation_for_accounts_button').show();\n";
	}
    exit();
}


if($action=="production_process_control")
{
    echo "$('#hidden_variable_cntl').val('0');\n";
    echo "$('#hidden_preceding_process').val('0');\n";
    $control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=28 and company_name='$data'");
    if(count($control_and_preceding)>0)
    {
      echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf("is_control")]."');\n";
      echo "$('#hidden_preceding_process').val('".$control_and_preceding[0][csf("preceding_page_id")]."');\n";
    }

    exit();
}
if($action=="show_cost_details")
{
	echo load_html_head_contents("Challan Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	$lib_color=return_library_array( "select id, color_name from lib_color",'id','color_name');

	$sqlResult =sql_select("SELECT b.id as po_id,b.po_number,c.item_number_id,c.color_number_id, a.cost_of_fab_per_pcs,a.cut_oh_per_pcs,a.cost_per_pcs,a.fab_rate_per_pcs from pro_garments_production_dtls a,WO_PO_COLOR_SIZE_BREAKDOWN c,wo_po_break_down b,lib_country d where a.COLOR_SIZE_BREAK_DOWN_ID=c.id and b.id=c.po_break_down_id  and c.country_id=d.id and a.delivery_mst_id='$sys_id' and a.status_active=1 and a.is_deleted=0 and a.production_type=4");// and a.embel_name=2
	if(count($sqlResult)==0)
	{
		?>
		<div class="alert alert-danger">Data not found!</div>
		<?
		die;
	}
	$data_array = array();
	foreach ($sqlResult as $v)
	{
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_of_fab_per_pcs'] = $v['COST_OF_FAB_PER_PCS'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cut_oh_per_pcs'] = $v['CUT_OH_PER_PCS'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_per_pcs'] = $v['COST_PER_PCS'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['fab_rate_per_pcs'] = $v['FAB_RATE_PER_PCS'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['po_number'] = $v['PO_NUMBER'];
	}
	?>
 		<table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
			<thead>
				<th width="150">PO</th>
				<th width="150">Item</th>
				<th width="150">Color</th>
				<th width="100">Cost Per Pcs</th>
			</thead>
			<tbody>
				<?
				$i=1;
				foreach ($data_array as $po_id=>$po_data)
				{
					foreach ($po_data as $itm_id=>$itm_data)
					{
						foreach ($itm_data as $color_id=>$v)
						{
							$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor;?>">
								<td><?=$v['po_number'];?></td>
								<td><?=$garments_item[$itm_id];?></td>
								<td><?=$lib_color[$color_id];?></td>
								<td align="right"><?=$v['cost_per_pcs'];?></td>
							</tr>
							<?
							$i++;
						}
					}
				}
				?>
			</tbody>
		</table>
	<?

	exit();
}

if($action=="show_cost_details__")
{
	echo load_html_head_contents("Challan Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	$lib_country=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$sqlResult =sql_select("SELECT b.po_number,a.country_id,a.item_number_id, a.cost_of_fab_per_pcs,a.cost_per_pcs,a.production_type,a.embel_name from pro_garments_production_mst a,wo_po_break_down b,lib_country c where b.id=a.po_break_down_id and a.country_id=c.id and a.delivery_mst_id='$sys_id' and a.status_active=1 and a.is_deleted=0 and a.production_type=4");
	if(count($sqlResult)==0)
	{
		?>
		<div class="alert alert-danger">Data not found!</div>
		<?
		die;
	}
	?>
 		<table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
			<thead>
				<th width="100">PO</th>
				<th width="100">Item</th>
				<th width="100">Counry</th>
				<th width="90">Tot Cost Per Pcs</th>
			</thead>
			<tbody>
				<?
				$i=1;
                foreach ($sqlResult as $v)
				{
                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                        <td><?=$v['PO_NUMBER'];?></td>
                        <td><?=$garments_item[$v['ITEM_NUMBER_ID']];?></td>
                        <td><?=$lib_country[$v['COUNTRY_ID']];?></td>
                        <td><?=$v['COST_PER_PCS'];?></td>
                    </tr>
                    <?
				}
				?>
			</tbody>
		</table>
	<?

	exit();
}

if ($action=="load_variable_settings_for_working_company")
{
    $sql_result = sql_select("select working_company_mandatory from variable_settings_production where company_name=$data and variable_list=41 and status_active=1");

    $working_company="";
    foreach($sql_result as $row)
    {
        $working_company=$row[csf("working_company_mandatory")];
    }
    echo $working_company;

    exit();
}


/*
if ($action == "load_drop_down_embro_issue_source") {
    $explode_data = explode("**", $data);
    $data = $explode_data[0];
    $selected_company = $explode_data[1];

    if ($data == 3) {
        if ($db_type == 0) {
            echo create_drop_down("cbo_emb_company", 180, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,23,party_type) order by supplier_name", "id,supplier_name", 1, "--- Select ---", $selected, "load_drop_down( 'requires/bundle_wise_sewing_input_controller', this.value, 'load_drop_down_location', 'location_td' );");
        } else {
            echo create_drop_down("cbo_emb_company", 180, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(22,23) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select--", 0, "load_drop_down( 'requires/bundle_wise_sewing_input_controller', this.value, 'load_drop_down_location', 'location_td' );");
        }
    } else if ($data == 1)
        echo create_drop_down("cbo_emb_company", 180, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name", "id,company_name", 1, "--- Select ---", $selected_company, "load_drop_down( 'requires/bundle_wise_sewing_input_controller', this.value, 'load_drop_down_location', 'location_td' );", 0, 0);
    else
        echo create_drop_down("cbo_emb_company", 180, $blank_array, "", 1, "--- Select ---", $selected, "load_drop_down( 'requires/bundle_wise_sewing_input_controller', this.value, 'load_drop_down_location', 'location_td' );", 0);

    exit();
}

*/
if ($action == "load_drop_down_embro_issue_source")
{
    $user_id = $_SESSION['logic_erp']["user_id"];
    //========== user credential start ========
    $userCredential = sql_select("SELECT WORKING_UNIT_ID, unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
    $working_unit_id = $userCredential[0][csf('WORKING_UNIT_ID')];

    $working_credential_cond = "";

    if ($working_unit_id > 0)
    {
        $working_credential_cond = " and comp.id in($working_unit_id)";
    }

    $explode_data = explode("**", $data);
    $data = $explode_data[0];
    $selected_company =0;// $explode_data[1];

    if ($data == 3)
    {
        if ($db_type == 0) {
             echo create_drop_down("cbo_emb_company", 180, "SELECT id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,23,party_type) order by supplier_name", "id,supplier_name", 1, "--- Select ---", $selected, "load_drop_down( 'requires/bundle_wise_sewing_input_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data(document.getElementById('cbo_company_name').value,'load_variable_settings','requires/bundle_wise_sewing_input_controller');load_html();");
        } else {
            echo create_drop_down("cbo_emb_company", 180, "SELECT a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(22) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select--", 0, "load_drop_down( 'requires/bundle_wise_sewing_input_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data(document.getElementById('cbo_company_name').value,'load_variable_settings','requires/bundle_wise_sewing_input_controller');load_html();");
        }
    }
    else if ($data == 1)
    {
        echo create_drop_down("cbo_emb_company", 180, "SELECT id,company_name from lib_company comp where is_deleted=0 and status_active=1 $working_credential_cond order by company_name", "id,company_name", 1, "--- Select ---", $selected_company, "load_drop_down( 'requires/bundle_wise_sewing_input_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data(this.value,'load_variable_settings','requires/bundle_wise_sewing_input_controller');load_html();fnc_company_check(document.getElementById('cbo_source').value);", 0, 0);

    }
    else
    {
        echo create_drop_down("cbo_emb_company", 180, $blank_array, "", 1, "--- Select ---", $selected, "load_drop_down( 'requires/bundle_wise_sewing_input_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data(this.value,'load_variable_settings','requires/bundle_wise_sewing_input_controller');load_html();", 0);
    }

    exit();
}


if ($action == "order_popup") {
    extract($_REQUEST);
    echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
    ?>
    <script>
        $(document).ready(function (e) {
            $("#txt_search_common").focus();
        });

        function search_populate(str) {
            //alert(str);
            if (str == 0) {
                document.getElementById('search_by_th_up').innerHTML = "Order No";
                document.getElementById('search_by_td').innerHTML = '<input type="text" name="txt_search_common" style="width:230px" class="text_boxes" id="txt_search_common"  value=""  />';
            }
            else if (str == 1) {
                document.getElementById('search_by_th_up').innerHTML = "Style Ref. Number";
                document.getElementById('search_by_td').innerHTML = '<input type="text" name="txt_search_common" style="width:230px" class="text_boxes" id="txt_search_common"  value="" />';
            }
            else //if(str==2)
            {
                var buyer_name = '<option value="0">--- Select Buyer ---</option>';
                <?php
                if ($db_type == 0) {
                    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer where find_in_set($company,tag_company) and status_active=1 and is_deleted=0 order by buyer_name", 'id', 'buyer_name');
                } else {
                    $buyer_arr = return_library_array("select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond order by buy.buyer_name", 'id', 'buyer_name');
                }
                foreach ($buyer_arr as $key => $val) {
                    echo "buyer_name += '<option value=\"$key\">" . ($val) . "</option>';";
                }
                ?>
                document.getElementById('search_by_th_up').innerHTML = "Select Buyer Name";
                document.getElementById('search_by_td').innerHTML = '<select name="txt_search_common" style="width:230px" class="combo_boxes" id="txt_search_common">' + buyer_name + '</select>';
            }
        }

        function js_set_value(id, item_id, po_qnty, plan_qnty, country_id) {
            $("#hidden_mst_id").val(id);
            $("#hidden_grmtItem_id").val(item_id);
            $("#hidden_po_qnty").val(po_qnty);
            $("#hidden_country_id").val(country_id);
            parent.emailwindow.hide();
        }
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;">
        <form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
            <table width="780" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
                <tr>
                    <td align="center" width="100%">
                        <table ellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                            <thead>
                            <th width="130">Search By</th>
                            <th width="180" align="center" id="search_by_th_up">Enter Order Number</th>
                            <th width="200">Date Range</th>
                            <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset"
                                                  style="width:100px;"/></th>
                            </thead>
                            <tr>
                                <td width="130">
                                    <?
                                    $searchby_arr = array(0 => "Order No", 1 => "Style Ref. Number", 2 => "Buyer Name");
                                    echo create_drop_down("txt_search_by", 130, $searchby_arr, "", 1, "-- Select Sample --", $selected, "search_populate(this.value)", 0);
                                    ?>
                                </td>
                                <td width="180" align="center" id="search_by_td">
                                    <input type="text" style="width:230px" class="text_boxes" name="txt_search_common"
                                           id="txt_search_common"
                                           onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()"/>
                                </td>
                                <td align="center">
                                    <input name="txt_date_from" id="txt_date_from" class="datepicker"
                                           style="width:70px"> To
                                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                                </td>
                                <td align="center">
                                    <input type="button" id="btn_show" class="formbutton" value="Show"
                                           onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $garments_nature; ?>, 'create_po_search_list_view', 'search_div', 'bundle_wise_sewing_input_controller', 'setFilterGrid(\'tbl_po_list\',-1)')"
                                           style="width:100px;"/>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" height="40" valign="middle">
                        <? echo load_month_buttons(1); ?>
                        <input type="hidden" id="hidden_mst_id">
                        <input type="hidden" id="hidden_grmtItem_id">
                        <input type="hidden" id="hidden_po_qnty">
                        <input type="hidden" id="hidden_country_id">
                    </td>
                </tr>
            </table>
            <div style="margin-top:10px" id="search_div"></div>
        </form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}

if ($action == "create_po_search_list_view") {
    $ex_data = explode("_", $data);
    $txt_search_by = $ex_data[0];
    $txt_search_common = $ex_data[1];
    $txt_date_from = $ex_data[2];
    $txt_date_to = $ex_data[3];
    $company = $ex_data[4];
    $garments_nature = $ex_data[5];

    $sql_cond = "";
    if (trim($txt_search_common) != "") {
        if (trim($txt_search_by) == 0)
            $sql_cond = " and b.po_number like '%" . trim($txt_search_common) . "%'";
        else if (trim($txt_search_by) == 1)
            $sql_cond = " and a.style_ref_no like '%" . trim($txt_search_common) . "%'";
        else if (trim($txt_search_by) == 2)
            $sql_cond = " and a.buyer_name=trim('$txt_search_common')";
    }
    if ($txt_date_from != "" || $txt_date_to != "") {
        if ($db_type == 0) {
            $sql_cond .= " and b.shipment_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
        }
        if ($db_type == 2 || $db_type == 1) {
            $sql_cond .= " and b.shipment_date between '" . date("j-M-Y", strtotime($txt_date_from)) . "' and '" . date("j-M-Y", strtotime($txt_date_to)) . "'";
        }
    }

    if (trim($company) != "") $sql_cond .= " and a.company_name='$company'";

    $sql = "SELECT b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number, b.po_quantity, b.plan_cut
    from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_embe_cost_dtls c
    where
    a.id = b.job_id and a.id = c.job_id and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.garments_nature=$garments_nature and c.emb_name=2
    $sql_cond group by b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number, b.po_quantity, b.plan_cut order by b.id DESC";
    //echo $sql;die;
    $result = sql_select($sql);
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
    $company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');

    //$po_country_arr=return_library_array( "select po_break_down_id, $select_field"."_concat(distinct(country_id)) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
    if ($db_type == 0) {
        $po_country_arr = return_library_array("select po_break_down_id, group_concat(distinct(country_id)) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id", 'po_break_down_id', 'country');
    } else {
        $po_country_arr = return_library_array("select po_break_down_id, listagg(CAST(country_id as VARCHAR(4000)),',') within group (order by country_id) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id", 'po_break_down_id', 'country');
    }

    $po_country_data_arr = array();
    $poCountryData = sql_select("select po_break_down_id, item_number_id, country_id, sum(order_quantity) as qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id");

    foreach ($poCountryData as $row) {
        $po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['po_qnty'] = $row[csf('qnty')];
        $po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['plan_cut_qnty'] = $row[csf('plan_cut_qnty')];
    }

    $total_issu_qty_data_arr = array();
    $total_issu_qty_arr = sql_select("select po_break_down_id, item_number_id, country_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=2 group by po_break_down_id, item_number_id, country_id");

    foreach ($total_issu_qty_arr as $row) {
        $total_issu_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]] = $row[csf('production_quantity')];
    }

    ?>
    <div style="width:1030px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
            <th width="30">SL</th>
            <th width="70">Shipment Date</th>
            <th width="100">Order No</th>
            <th width="100">Buyer</th>
            <th width="120">Style</th>
            <th width="140">Item</th>
            <th width="100">Country</th>
            <th width="80">Order Qty</th>
            <th width="80">Total Issue Qty</th>
            <th width="80">Balance</th>
            <th>Company Name</th>
            </thead>
        </table>
    </div>
    <div style="width:1030px; max-height:240px;overflow-y:scroll;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1012" class="rpt_table" id="tbl_po_list">
            <?
            $i = 1;
            foreach ($result as $row) {
                $exp_grmts_item = explode("__", $row[csf("set_break_down")]);
                $numOfItem = count($exp_grmts_item);
                $set_qty = "";
                $grmts_item = "";

                $country = array_unique(explode(",", $po_country_arr[$row[csf("id")]]));
                //$country=explode(",",$po_country_arr[$row[csf("id")]]);
                $numOfCountry = count($country);

                for ($k = 0; $k < $numOfItem; $k++) {
                    if ($row["total_set_qnty"] > 1) {
                        $grmts_item_qty = explode("_", $exp_grmts_item[$k]);
                        $grmts_item = $grmts_item_qty[0];
                        $set_qty = $grmts_item_qty[1];
                    } else {
                        $grmts_item_qty = explode("_", $exp_grmts_item[$k]);
                        $grmts_item = $grmts_item_qty[0];
                        $set_qty = $grmts_item_qty[1];
                    }

                    foreach ($country as $country_id) {
                        if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

                        //$po_qnty=$row[csf("po_quantity")]; $plan_cut_qnty=$row[csf("plan_cut")];
                        $po_qnty = $po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id]['po_qnty'];
                        $plan_cut_qnty = $po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id]['plan_cut_qnty'];

                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer"
                            onClick="js_set_value(<? echo $row[csf("id")]; ?>,'<? echo $grmts_item; ?>','<? echo $po_qnty; ?>','<? echo $plan_cut_qnty; ?>','<? echo $country_id; ?>');">
                            <td width="30" align="center"><?php echo $i; ?></td>
                            <td width="70"
                                align="center"><?php echo change_date_format($row[csf("shipment_date")]); ?></td>
                            <td width="100"><p><?php echo $row[csf("po_number")]; ?></p></td>
                            <td width="100"><p><?php echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
                            <td width="120"><p><?php echo $row[csf("style_ref_no")]; ?></p></td>
                            <td width="140"><p><?php echo $garments_item[$grmts_item]; ?></p></td>
                            <td width="100"><p><?php echo $country_library[$country_id]; ?>&nbsp;</p></td>
                            <td width="80" align="right"><?php echo $po_qnty; //$po_qnty*$set_qty;
                                ?>&nbsp;</td>
                            <td width="80" align="right">
                                <?php
                                echo $total_cut_qty = $total_issu_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id]
                                ?> &nbsp;
                            </td>
                            <td width="80" align="right">
                                <?php
                                $balance = $po_qnty - $total_cut_qty;
                                echo $balance;
                                ?>&nbsp;
                            </td>
                            <td><?php echo $company_arr[$row[csf("company_name")]]; ?> </td>
                        </tr>
                        <?
                        $i++;
                    }
                }
            }
            ?>
        </table>
    </div>
    <?
    exit();
}

if ($action == "populate_data_from_search_popup") {
    $dataArr = explode("**", $data);
    $po_id = $dataArr[0];
    $item_id = $dataArr[1];
    $embel_name = $dataArr[2];
    $country_id = $dataArr[3];

    $res = sql_select("SELECT a.id, a.po_quantity, a.plan_cut, a.po_number, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.job_no, b.location_name
        from wo_po_break_down a, wo_po_details_master b, wo_pre_cost_embe_cost_dtls c
        where a.job_id=b.id and b.id=c.job_id and a.id=$po_id group by a.id, a.po_quantity, a.plan_cut, a.po_number, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.job_no, b.location_name");

    foreach ($res as $result) {
        echo "$('#txt_order_no').val('" . $result[csf('po_number')] . "');\n";
        echo "$('#hidden_po_break_down_id').val('" . $result[csf('id')] . "');\n";
        echo "$('#cbo_buyer_name').val('" . $result[csf('buyer_name')] . "');\n";
        echo "$('#txt_style_no').val('" . $result[csf('style_ref_no')] . "');\n";

        $dataArray = sql_select("select SUM(CASE WHEN production_type=1 THEN production_quantity END) as totalcutting,SUM(CASE WHEN production_type=4 and embel_name='$embel_name' THEN production_quantity ELSE 0 END) as totalprinting from pro_garments_production_mst WHERE po_break_down_id=" . $result[csf('id')] . " and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0");
        foreach ($dataArray as $row) {
            echo "$('#txt_cutting_qty').val('" . $row[csf('totalcutting')] . "');\n";
            echo "$('#txt_cumul_issue_qty').attr('placeholder','" . $row[csf('totalprinting')] . "');\n";
            echo "$('#txt_cumul_issue_qty').val('" . $row[csf('totalprinting')] . "');\n";
            $yet_to_produced = $row[csf('totalcutting')] - $row[csf('totalprinting')];
            echo "$('#txt_yet_to_issue').attr('placeholder','" . $yet_to_produced . "');\n";
            echo "$('#txt_yet_to_issue').val('" . $yet_to_produced . "');\n";
        }
    }
    exit();
}

if($action=="bundle_popup_rescan")
{
    extract($_REQUEST);
    echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	?>
    <script>

        function check_all_data() {
            var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
            for( var i = 1; i <= tbl_row_count; i++ ) {
                if($("#search"+i).css("display") !='none'){
                 js_set_value( i );
                }
            }
        }


        var selected_id = new Array();

        function toggle( x, origColor ) {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }

        function js_set_value( str)
        {
            toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

            if( jQuery.inArray( $('#txt_individual' + str).val(), selected_id ) == -1 ) {
                selected_id.push( $('#txt_individual' + str).val() );

            }
            else {
                for( var i = 0; i < selected_id.length; i++ ) {
                    if( selected_id[i] == $('#txt_individual' + str).val() ) break;
                }
                selected_id.splice( i, 1 );
            }
            var id = '';
            for( var i = 0; i < selected_id.length; i++ ) {
                id += selected_id[i] + ',';
            }
            id = id.substr( 0, id.length - 1 );

            $('#hidden_bundle_nos').val( id );

        }

        function fnc_close()
        {
            document.getElementById('hidden_source_cond').value=document.getElementById('source_cond').value;
            //alert(document.getElementById('hidden_source_cond').value)
            parent.emailwindow.hide();
        }

        function reset_hide_field()
        {
            $('#hidden_bundle_nos').val( '' );
            $('#hidden_source_cond').val( '' );
            selected_id = new Array();
        }
    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	    <form name="searchwofrm"  id="searchwofrm">
	        <fieldset style="width:810px;">
	        <legend>Enter search words <input type="checkbox" value="1" name="is_exact" id="is_exact" checked> is exact</legend>
	            <table cellpadding="0" cellspacing="0" width="550" border="1" rules="all" class="rpt_table">
	                <thead>
	                    <th>Cut Year</th>
	                    <th>Job No</th>
	                    <th>Order No</th>
	                    <th class="must_entry_caption">Cut No</th>
	                    <th>Bundle No</th>
	                    <th>
	                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
	                        <input type="hidden" name="hidden_bundle_nos" id="hidden_bundle_nos">
	                        <input type="hidden" name="hidden_source_cond" id="hidden_source_cond">
	                    </th>
	                </thead>
	                <tr class="general">
	                    <td align="center">
	                    <?
	                        echo create_drop_down( "cbo_cut_year", 60, $year,'', "", '-- Select --',date("Y",time()), "",'','','','' );
	                    ?>
	                    </td>
	                    <td align="center">
	                        <input type="text" style="width:130px" class="text_boxes" name="txt_job_no" id="txt_job_no" />
	                    </td>
	                    <td align="center" id="search_by_td">
	                        <input type="text" style="width:130px" class="text_boxes" name="txt_order_no" id="txt_order_no" />
	                    </td>
	                    <td><input type="text" name="txt_cut_no" id="txt_cut_no" style="width:120px" class="text_boxes" /></td>
	                    <td><input type="text" name="bundle_no" id="bundle_no" style="width:120px" class="text_boxes" /></td>
	                    <td align="center">
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('txt_order_no').value+'_'+<? echo $company; ?>+'_'+document.getElementById('bundle_no').value+'_'+'<? echo trim($bundleNo,','); ?>'+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('cbo_cut_year').value+'_'+$('#is_exact').is(':checked'), 'create_bundle_rescan_search_list_view', 'search_div', 'bundle_wise_sewing_input_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
	                     </td>
	                </tr>
	           </table>
	           <div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
	        </fieldset>
	    </form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}



if($action=="create_bundle_rescan_search_list_view")
{
    $ex_data = explode("_",$data);
    $txt_order_no = "%".trim($ex_data[0])."%";
    $company = $ex_data[1];
    if(trim($ex_data[2])){$bundle_no = "".trim($ex_data[2])."";}
    else{ $bundle_no = "%".trim($ex_data[2])."%";}
    $selectedBuldle=$ex_data[3];
    $job_no=$ex_data[4];
    $cut_no=$ex_data[5];
    $order_no =str_replace("'","", $ex_data[0]);
    $bndl_no =str_replace("'","", $ex_data[2]);
    $syear = substr($ex_data[6],2);
    $is_exact=$ex_data[7];

    foreach(explode(",",$selectedBuldle) as $bn)
    {
        $curentscanned_bundle_arr[$bn]=$bn;
    }

    if($db_type==2) $not_null_bundle=" and bundle_no is not null";
    else $not_null_bundle=" and bundle_no!=''";






    /*if( trim($ex_data[5])=='' ){echo "<h2 style='color:#D00; text-align:center;'>Please Select  Cut No</h2>";exit();}*/

    //$company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');



    $company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
    $size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
    $color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
    $country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
    //  $cutting_no=return_field_value("cut_no", "pro_garments_production_dtls", "barcode_no in ($bundle_nos)");
    $cutting_no =trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT));
    $last_operation=gmt_production_validation_script( 4, 1,'',$cutting_no, $production_squence);

    if ($cut_no != '')
    {
        if($is_exact=='true')
        {
         $cutCon = " and c.cut_no = '".trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT))."'";
         $cutCon_a = " and b.cut_no = '".trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT))."'";
        }
        else
        {
            $cutCon = " and c.cut_no like '%".$cut_no."'";
            $cutCon_a = " and b.cut_no like '%".$cut_no."'";
        }
    }
    //if($job_no!='') $jobCon=" and f.job_no_prefix_num = $job_no"; else $jobCon="";
    //print_r($scanned_bundle_qty_arr);die;
    if($job_no!='')
    {
        if($is_exact=='true') $jobCon=" and f.job_no = '$job_no'";
        else  $jobCon=" and f.job_no like '%$job_no%'";

    }
    $orderCon="";
    if($order_no)
    {
        if($is_exact=='true') $orderCon=" and e.po_number = '$order_no'";
        else  $orderCon=" and e.po_number like '%$order_no%'";

    }

    $bndlCon="";
    if($bndl_no)
    {
        if($is_exact=='true') $bndlCon=" and c.bundle_no = '$bndl_no'";
        else  $bndlCon=" and c.bundle_no like '%$bndl_no%'";

    }


    $sql_scan_bundle=sql_select(" select b.barcode_no,sum(b.production_qnty) as production_qnty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=4 and b.status_active=1 and b.is_deleted=0 $not_null_bundle  $cutCon_a  group by b.barcode_no");
    foreach($sql_scan_bundle as $val)
    {
        $scanned_bundle_qty_arr[$val[csf('barcode_no')]]+=$val[csf('production_qnty')];
        $scanned_bundle_arr[]=$val[csf('barcode_no')];
    }



    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="50">Year</th>
            <th width="50">Job No</th>
            <th width="90">Order No</th>
            <th width="130">Gmts Item</th>
            <th width="110">Country</th>
            <th width="100">Color</th>
            <th width="50">Size</th>
            <th width="50">Cut No</th>
            <th width="80">Bundle No</th>
            <th>Bundle Qty.</th>
        </thead>
    </table>
    <div style="width:850px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table" id="tbl_list_search">
        <?
            $i=1;
            $last_operation_string='';
            foreach($last_operation as  $item_id=>$operation_cond)
            {
                if( $operation_cond!=0 ) $operation_conds=" and d.id in (".ltrim($operation_cond,",").") ";
                else
                {
                    $sqld = sql_select( "SELECT c.bundle_no, SUM(c.reject_qty) as reject_qty, SUM(c.alter_qty) as alter_qty, SUM(c.spot_qty) as spot_qty, SUM(c.replace_qty) as replace_qty  from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e,wo_po_details_master f where d.job_id=f.id and e.job_id=f.id and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company  and c.production_type in (3)  $orderCon $bndlCon     $jobCon $cutCon  and a.status_active=1 and a.is_deleted=0 group by  c.bundle_no order by  length(c.bundle_no) asc, c.bundle_no asc" );
                    foreach($sqld as $arows)
                    {
                        $reject_qty[$arows[csf('bundle_no')]]+=$arows[csf('reject_qty')];
                        $alter_qty[$arows[csf('bundle_no')]]+=$arows[csf('alter_qty')];
                        $spt_qty[$arows[csf('bundle_no')]]+=$arows[csf('spt_qty')];
                        $replace_qty[$arows[csf('bundle_no')]]+=$arows[csf('replace_qty')];
                    }
                }

                $sql="SELECT c.cut_no, c.bundle_no,c.barcode_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, sum(c.production_qnty) as qty, e.po_number from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e,wo_po_details_master f where d.job_id=f.id and e.job_id=f.id and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company  $orderCon $bndlCon   $operation_conds  $item_id $jobCon $cutCon  and a.status_active=1 and a.is_deleted=0 group by c.cut_no, c.bundle_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no,c.barcode_no, e.po_number order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
                 //echo $sql;
                $result = sql_select($sql);
                foreach ($result as $row)
                {

                     $bundle_qty = ($row[csf('qty')] + $replace_qty[$row[csf('bundle_no')]])  - ($reject_qty[$row[csf('bundle_no')]] + $alter_qty[$row[csf('bundle_no')]]+ $spt_qty[$row[csf('bundle_no')]])-$scanned_bundle_qty_arr[$row[csf('barcode_no')]];

                    //$bundle_qty=$row[csf('qty')]-$scanned_bundle_qty_arr[$row[csf('barcode_no')]]+$replace_qty[$row[csf('barcode_no')]];

                    //if($row[csf('barcode_no')]=='17990000005901')
                //  echo  $row[csf('qty')]."=".$reject_qty[$row[csf('bundle_no')]]."=".$scanned_bundle_qty_arr[$row[csf('barcode_no')]]."*";
                    if($bundle_qty>0 && in_array($row[csf('barcode_no')],$scanned_bundle_arr) && !in_array($row[csf('bundle_no')],$curentscanned_bundle_arr))
                    {
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        list($shortName,$year,$job)=explode('-',$row[csf('job_no_mst')]);
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)">
                            <td width="40"><? echo $i; ?>
                                 <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
                            </td>
                            <td width="50" align="center"><p><? echo $year; ?></p></td>
                            <td width="50" align="center"><p><? echo $job*1; ?></p></td>
                            <td width="90"><p><? echo $row[csf('po_number')]; ?></p></td>
                            <td width="130"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
                            <td width="110"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                            <td width="100"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
                            <td width="50"><p><? echo $size_arr[$row[csf('size_number_id')]]; ?></p></td>
                            <td width="50"><? echo $row[csf('cut_no')]; ?></td>
                            <td width="80"><? echo $row[csf('bundle_no')]; ?></td>
                            <td align="right"><? echo $bundle_qty; ?>&nbsp;&nbsp;</td>
                        </tr>
                    <?
                        $i++;
                    }
                }
            }

            if(empty($last_operation))
            {
                $sql="SELECT c.cut_no,d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, sum(c.production_qnty) as qty, e.po_number,c.barcode_no from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e,wo_po_details_master f where d.job_id=f.id e.job_id=f.id and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company and e.po_number like '$txt_order_no' and c.bundle_no like '$bundle_no' and  c.production_type=1 $jobCon $cutCon  and a.status_active=1 and a.is_deleted=0 group by c.cut_no, c.bundle_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, e.po_number,c.barcode_no order by  c.cut_no,length(c.bundle_no) asc, c.bundle_no asc";
                // order by c.cut_no, c.bundle_no DESC

                $last_operation_string='';
                $result = sql_select($sql);
                foreach ($result as $row)
                {
                    $bundle_qty=$row[csf('qty')]-$scanned_bundle_qty_arr[$row[csf('barcode_no')]];
                    if($bundle_qty>0 && in_array($row[csf('barcode_no')],$scanned_bundle_arr) && !in_array($row[csf('bundle_no')],$curentscanned_bundle_arr))
                    {
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        list($shortName,$year,$job)=explode('-',$row[csf('job_no_mst')]);
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)">
                            <td width="40"><? echo $i; ?>
                                 <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
                            </td>
                            <td width="50" align="center"><p><? echo $year; ?></p></td>
                            <td width="50" align="center"><p><? echo $job*1; ?></p></td>
                            <td width="90"><p><? echo $row[csf('po_number')]; ?></p></td>
                            <td width="130"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
                            <td width="110"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                            <td width="100"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
                            <td width="50"><p><? echo $size_arr[$row[csf('size_number_id')]]; ?></p></td>
                            <td width="70"><? echo $row[csf('cut_no')]; ?></td>
                            <td width="80"><? echo $row[csf('bundle_no')]; ?></td>
                            <td align="right"><? echo $row[csf('qty')]; ?>&nbsp;&nbsp;</td>
                        </tr>
                    <?
                        $i++;
                    }
                }
            }
            ?>
            <input type="hidden" name="source_cond"  value="<?php echo $last_operation_string; ?>" id="source_cond"  />
        </table>
    </div>
    <table width="830">
        <tr>
            <td align="center" >
               <span  style="float:left;"> <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All</span>
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
	<?
    exit();
}

if($action=="populate_bundle_data_rescan")
{

    $ex_data = explode("**",$data);
    $bundle=explode(",",$ex_data[0]);
    $mst_id=explode(",",$ex_data[2]);
    $bundle_nos="'".implode("','",$bundle)."'";
    $vscan=$ex_data[4];
    $source_cond=$ex_data[5];
    //echo $bundle_nos;die;
    //echo $vscan;die;

	$bundle_count=count(explode(",",$bundle_nos)); $bundle_nos_cond=""; $scnbundle_nos_cond=""; $cutbundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nos_cond=" and (";
		$scnbundle_nos_cond=" and (";
		$cutbundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_nos),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
			$scnbundle_nos_cond.=" b.barcode_no in($bundleNos) or ";
			$cutbundle_nos_cond.=" barcode_no in($bundleNos) or ";
		}
		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";

		$scnbundle_nos_cond=chop($scnbundle_nos_cond,'or ');
		$scnbundle_nos_cond.=")";

		$cutbundle_nos_cond=chop($cutbundle_nos_cond,'or ');
		$cutbundle_nos_cond.=")";
	}
	else
	{
		$bundle_nos_cond=" and c.barcode_no in ($bundle_nos)";
		$scnbundle_nos_cond=" and b.barcode_no in ($bundle_nos)";
		$cutbundle_nos_cond=" and barcode_no in ($bundle_nos)";
	}

    $scanned_bundle_arr=return_library_array( "SELECT b.bundle_no, sum(b.production_qnty) as production_qnty from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=4 and b.status_active=1 and b.is_deleted=0 $scnbundle_nos_cond group by b.bundle_no",'bundle_no','production_qnty');
    //print_r($scanned_bundle_arr);die;
    $size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
    $color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
    $country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

    $year_field="";
    if($db_type==0)
    {
        $year_field="YEAR(f.insert_date)";
    }
    else if($db_type==2)
    {
        $year_field="to_char(f.insert_date,'YYYY')";
    }
    $cutting_no=return_field_value("cut_no", "pro_garments_production_dtls", "1=1 $cutbundle_nos_cond");
    //$cutting_no =trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT));
    $last_operation=gmt_production_validation_script( 4, 1,'', $cutting_no, $production_squence);

    //$last_operation=gmt_production_validation_script( 4, 1);
     //print_r($last_operation);die;
    foreach($last_operation as  $item_id=>$operation_cond)
    {
        //echo $item_id; die;  17990000000898
        if( $operation_cond!=0 ) $operation_conds=" and d.id in (".ltrim($operation_cond,",").") ";
        else
        {

            $sqld = sql_select( "SELECT c.bundle_no, SUM(c.reject_qty) as reject_qty, SUM(c.alter_qty) as alter_qty, SUM(c.spot_qty) as spot_qty, SUM(c.replace_qty) as replace_qty  from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e,wo_po_details_master f where d.job_id=f.id e.job_id=f.id and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id='$ex_data[3]'  and c.production_type in (3) $bundle_nos_cond $jobCon $cutCon  and a.status_active=1 and a.is_deleted=0 group by  c.bundle_no order by  length(c.bundle_no) asc, c.bundle_no asc" );
            foreach($sqld as $arows)
            {
                $reject_qty[$arows[csf('bundle_no')]]+=$arows[csf('reject_qty')];
                $alter_qty[$arows[csf('bundle_no')]]+=$arows[csf('alter_qty')];
                $spt_qty[$arows[csf('bundle_no')]]+=$arows[csf('spt_qty')];
                $replace_qty[$arows[csf('bundle_no')]]+=$arows[csf('replace_qty')];
            }
        }

        $sql="SELECT max(c.id) as prdid, d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, MAX($year_field) as year, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no, sum(c.production_qnty) as production_qnty, e.po_number,c.barcode_no from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where f.company_name='$ex_data[3]' and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_id=f.id and d.job_id=f.id $bundle_nos_cond $operation_conds  and c.status_active=1 and c.is_deleted=0 $item_id group by d.id, e.id, f.job_no_prefix_num, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no, e.po_number,c.barcode_no order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
    //echo $sql;
    $result = sql_select($sql);
    $count=count($result);
    $i=$ex_data[1]+$count;
    foreach ($result as $row)
    {
    //echo $row[csf('production_qnty')]."=".$scanned_bundle_arr[$row[csf('bundle_no')]]; 5=15 15=15
        //$qty=$row[csf('production_qnty')]-$scanned_bundle_arr[$row[csf('bundle_no')]];
         $qty = ($row[csf('production_qnty')] + $replace_qty[$row[csf('bundle_no')]]) - ($reject_qty[$row[csf('bundle_no')]] + $alter_qty[$row[csf('bundle_no')]]+ $spt_qty[$row[csf('bundle_no')]])-$scanned_bundle_arr[$row[csf('bundle_no')]];
        if(($qty*1)>0 && $scanned_bundle_arr[$row[csf('bundle_no')]]!="")
        {
            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>">
                <td width="30"><? echo $i; ?></td>
                <td width="90" id="bundle_<? echo $i; ?>" title="<? echo $row[csf('barcode_no')]; ?>"><? echo $row[csf('bundle_no')]; ?></td>
                <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                <td width="65"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                <td width="90" style="word-break:break-all;" align="left"><? echo $row[csf('po_number')]; ?></td>
                <td width="120" style="word-break:break-all;" align="left"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
                <td width="100" style="word-break:break-all;" align="left"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
                <td width="80" style="word-break:break-all;" align="left"><? echo $color_arr[$row[csf('color_number_id')]]; ?></td>
                <td width="70" style="word-break:break-all;" align="left">&nbsp;<? echo $size_arr[$row[csf('size_number_id')]]; ?></td>
                <td width="80" id="prodQty_<? echo $i; ?>" align="right"><? echo $qty; ?>&nbsp;</td>
                <td id="button_1" align="center">
                    <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />

                    <input type="hidden" name="cutNo[]" id="cutNo_<? echo $i; ?>" value="<? echo $row[csf('cut_no')]; ?>"/>

                    <input type="hidden" name="colorSizeId[]" id="colorSizeId_<? echo $i; ?>" value="<? echo $row[csf('colorsizeid')]; ?>"/>
                    <input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $row[csf('po_id')]; ?>"/>
                    <input type="hidden" name="gmtsitemId[]" id="gmtsitemId_<? echo $i; ?>" value="<? echo $row[csf('item_number_id')]; ?>"/>
                    <input type="hidden" name="countryId[]" id="countryId_<? echo $i; ?>" value="<? echo $row[csf('country_id')]; ?>"/>
                    <input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_number_id')]; ?>"/>
                    <input type="hidden" name="sizeId[]" id="sizeId_<? echo $i; ?>" value="<? echo $row[csf('size_number_id')]; ?>"/>
                    <input type="hidden" name="qty[]" id="qty_<? echo $i; ?>" value="<? echo $qty; ?>"/>
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>"  value="<? echo $row[csf('prdid')]; ?>"/>
                    <input type="hidden" name="isRescan[]" id="isRescan_<? echo $i; ?>" value="1"/>
                </td>
            </tr>
            <?
            $i--;
            }
        }
    }

    if(empty($last_operation))
    {
          $sql="SELECT max(c.id)  as prdid, d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, MAX($year_field) as year, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no, sum(c.production_qnty) as production_qnty, e.po_number,c.barcode_no from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where f.company_name='$ex_data[3]' and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_id=f.id and d.job_id=f.id  and c.production_type=1 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $bundle_nos_cond group by d.id, e.id, f.job_no_prefix_num, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no,c.barcode_no, e.po_number order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";

    $result = sql_select($sql);
    $count=count($result);
    $i=$ex_data[1]+$count;
    foreach ($result as $row)
    {
        $qty=$row[csf('production_qnty')]-$scanned_bundle_arr[$row[csf('bundle_no')]];
        if(($qty*1)>0)
        {
            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>">
                <td width="30"><? echo $i; ?></td>
                <td width="90" id="bundle_<? echo $i; ?>" title="<? echo $row[csf('barcode_no')]; ?>"><? echo $row[csf('bundle_no')]; ?></td>
                <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                <td width="65"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                <td width="90" style="word-break:break-all;" align="left"><? echo $row[csf('po_number')]; ?></td>
                <td width="120" style="word-break:break-all;" align="left"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
                <td width="100" style="word-break:break-all;" align="left"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
                <td width="80" style="word-break:break-all;" align="left"><? echo $color_arr[$row[csf('color_number_id')]]; ?></td>
                <td width="70" style="word-break:break-all;" align="left">&nbsp;<? echo $size_arr[$row[csf('size_number_id')]]; ?></td>
                <td width="80" id="prodQty_<? echo $i; ?>" align="right"><? echo $qty; ?>&nbsp;</td>
                <td id="button_1" align="center">
                    <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />

                    <input type="hidden" name="cutNo[]" id="cutNo_<? echo $i; ?>" value="<? echo $row[csf('cut_no')]; ?>"/>

                    <input type="hidden" name="colorSizeId[]" id="colorSizeId_<? echo $i; ?>" value="<? echo $row[csf('colorsizeid')]; ?>"/>
                    <input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $row[csf('po_id')]; ?>"/>
                    <input type="hidden" name="gmtsitemId[]" id="gmtsitemId_<? echo $i; ?>" value="<? echo $row[csf('item_number_id')]; ?>"/>
                    <input type="hidden" name="countryId[]" id="countryId_<? echo $i; ?>" value="<? echo $row[csf('country_id')]; ?>"/>
                    <input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_number_id')]; ?>"/>
                    <input type="hidden" name="sizeId[]" id="sizeId_<? echo $i; ?>" value="<? echo $row[csf('size_number_id')]; ?>"/>
                    <input type="hidden" name="qty[]" id="qty_<? echo $i; ?>" value="<? echo $qty; ?>"/>
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>"value="<? echo $row[csf('prdid')]; ?>"/>
                    <input type="hidden" name="isRescan[]" id="isRescan_<? echo $i; ?>" value="1"/>
                </td>
            </tr>
            <?
            $i--;
            }
        }
    }
    exit();
}


if ($action == "bundle_popup") {
    extract($_REQUEST);
    echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);

    ?>
    <script>

        function check_all_data() {
            var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
            for (var i = 1; i <= tbl_row_count; i++) {
                if ($("#search" + i).css("display") != 'none') {
                    js_set_value(i);
                }
            }
        }
        var selected_id = new Array();
        var selected_qty = 0;


        function toggle(x, origColor) {
            var newColor = 'yellow';
            if (x.style) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
            }
        }

        function js_set_value(str) {


            toggle(document.getElementById('search' + str), '#FFFFCC');

            if (jQuery.inArray($('#txt_individual' + str).val(), selected_id) == -1) {
                selected_id.push($('#txt_individual' + str).val());
                selected_qty += $('#hidden_qty' + str).val()*1;

            }
            else {
                for (var i = 0; i < selected_id.length; i++) {
                    if (selected_id[i] == $('#txt_individual' + str).val()) break;
                }
                selected_id.splice(i, 1);
                selected_qty -= $('#hidden_qty' + str).val()*1;
            }

            var id = '';
            for (var i = 0; i < selected_id.length; i++) {
                id += selected_id[i] + ',';


            }
            id = id.substr(0, id.length - 1);

            $('#hidden_bundle_nos').val(id);
            $('#total_bndl_qty').text( selected_qty );


        }

        function fnc_close() {
            document.getElementById('hidden_source_cond').value=document.getElementById('source_cond').value;
            parent.emailwindow.hide();
        }

        function reset_hide_field() {
            $('#hidden_bundle_nos').val('');
            selected_id = new Array();
        }

    </script>
    </head>
    <body>
    <div align="center" style="width:100%;">
        <form name="searchwofrm" id="searchwofrm">
            <fieldset style="width:1060px;">
                <legend>Enter search words <input type="checkbox" value="1" name="is_exact" id="is_exact" > is exact</legend>
                <table cellpadding="0" cellspacing="0" width="800" border="1" rules="all" class="rpt_table">
                    <thead>
                    <th>Cut Year</th>
                    <th>Job Year</th>
                    <th>Job No</th>
                    <th>Style Ref</th>
                    <th>Internal Ref.</th>
                    <th>Order No</th>
                    <th class="must_entry_caption">Cut No</th>
                    <th>Order Cut No</th>
                    <th>Bundle No</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton"/>
                        <input type="hidden" name="hidden_bundle_nos" id="hidden_bundle_nos">
                        <input type="hidden" name="hidden_source_cond" id="hidden_source_cond">
                    </th>
                    </thead>
                <tr class="general">
                    <td align="center">
                    <?
                        echo create_drop_down( "cbo_cut_year", 60, $year,'', "", '-- Select --',date("Y",time()), "",'','','','' );
                    ?>
                    </td>
                    <td align="center">
                    <?
                        echo create_drop_down( "cbo_job_year", 60, $year,'', "", '-- Select --',date("Y",time()), "",'','','','' );
                    ?>
                    </td>
                        <td align="center">
                            <input type="text" style="width:120px" class="text_boxes" name="txt_job_no"
                                   id="txt_job_no"/>
                        </td>
                        <td align="center">
                            <input type="text" style="width:120px" class="text_boxes" name="txt_style_ref"
                                   id="txt_style_ref"/>
                        </td>
                        <td align="center">
                            <input type="text" style="width:132px" class="text_boxes" name="txt_internal_ref"
                                   id="txt_internal_ref"/>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:120px" class="text_boxes" name="txt_order_no"
                                   id="txt_order_no"/>
                        </td>
                        <td><input type="text" name="txt_cut_no" id="txt_cut_no" style="width:120px"
                                   class="text_boxes"/></td>
                        <td><input type="text" name="txt_order_cut_no" id="txt_order_cut_no" style="width:120px"
                                   class="text_boxes"/></td>
                        <td><input type="text" name="bundle_no" id="bundle_no" style="width:120px" class="text_boxes"/>
                        </td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show"
                                   onClick="show_list_view (document.getElementById('txt_order_no').value+'_'+<? echo $company; ?>+'_'+document.getElementById('bundle_no').value+'_'+'<? echo $bundleNo; ?>'+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('cbo_cut_year').value+'_'+$('#is_exact').is(':checked')+'_<? echo $company_id; ?>'+'_<? echo $is_controll; ?>'+'_<? echo $preceding_process; ?>'+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_order_cut_no').value+'_'+document.getElementById('cbo_job_year').value+'_'+document.getElementById('txt_style_ref').value, 'create_bundle_search_list_view', 'search_div', 'bundle_wise_sewing_input_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')"
                                   style="width:100px;"/>
                        </td>
                    </tr>
                </table>
                <div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
            </fieldset>
        </form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}

if ($action == "create_bundle_search_list_view")
{
    $ex_data = explode("_", $data);
    // echo "<pre>";
    // print_r($ex_data) ;//die;
    $txt_order_no = "%" . trim($ex_data[0]) . "%";
    // $style_ref = "%" . trim($ex_data[14]) . "%";
    $company = $ex_data[1];
    //$bundle_no = "%".trim($ex_data[2])."%";
    if (trim($ex_data[2])) {
        $bundle_no = "" . trim($ex_data[2]) . "";
    } else {
        $bundle_no = "%" . trim($ex_data[2]) . "%";
    }

    $selectedBuldle = $ex_data[3];
    $job_no = $ex_data[4];
    $order_no =str_replace("'","", $ex_data[0]);
    $bndl_no =str_replace("'","", $ex_data[2]);
    $cut_no = $ex_data[5];
    $syear = substr($ex_data[6],2);
    $is_exact=$ex_data[7];
    $is_controll=$ex_data[9];
    $preceding_process=$ex_data[10];
    $internal_ref=$ex_data[11];
    $order_cut_no=$ex_data[12];
    $job_year=$ex_data[13];
    $style_ref=str_replace("'","", $ex_data[14]);
    //echo  $style_ref;die;
    if (trim($job_no) == '' && trim($order_no) == '' && trim($cut_no) == '' && trim($bndl_no) == '' && trim($internal_ref) == '' && trim($order_cut_no) == '' && trim($style_ref) == '')
    {
        echo "<h2 style='color:#D00; text-align:center;font-size:20px;'>Please enter value of any one search field.</h2>";
        exit();
    }

    if (trim($job_no) == '' && trim($order_no) == '' && trim($cut_no) == '' && trim($bndl_no) == '' && trim($internal_ref) == '' && trim($order_cut_no) != '' && trim($style_ref) != '')
    {
        echo "<h2 style='color:#D00; text-align:center;font-size:20px;'>Please enter job no, Style Ref., int. ref. no or order no search field value.</h2>";
        exit();
    }

    $qty_source=1;
    if($preceding_process==117) $qty_source=1;//Cutting qc
    else if($preceding_process==123) $qty_source=9;//Cutting Delivery To Input Challan

    $company_short_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
    //list($short_name)=explode('-',$company_short_arr[$company]);
    $cutConvertToInt = convertToInt('c.cut_no', array($company_short_arr[$company], '-'), 'cut_no');
    $bundleConvertToInt = convertToInt('c.bundle_no', array($company_short_arr[$company], '-', "/"), 'order_bundle_no');

    $size_arr = return_library_array("select id, size_name from lib_size", 'id', 'size_name');
    $color_arr = return_library_array("select id, color_name from lib_color", "id", "color_name");
    $country_arr = return_library_array("select id, country_name from lib_country", 'id', 'country_name');


   // $scanned_bundle_arr = return_library_array("select b.bundle_no, b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=4 and  b.status_active=1 and b.is_deleted=0", 'bundle_no', 'bundle_no');
    $cutting_no =trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT));
    $where_con = ''; $style_cond ='';
    if ($ex_data[2])
    {
        $where_con .= " and c.bundle_no like '%" . trim($ex_data[2]) . "'";
    }

    if ($ex_data[0])
    {
        if($is_exact=='true') $where_con .= " and e.po_number='" . trim($ex_data[0]) . "'";
        else $where_con .= " and e.po_number like  '%" . trim($ex_data[0]) . "%'";


    }
    $tmp_cut=trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT));
    if ($cut_no != '')
     {
        if($is_exact=='true')
        {
            //$cutCon = " and c.cut_no = '".trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT))."'";
           // $cutCon_a = " and a.cut_no = '".trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT))."'";
            $cutCon = " and c.cut_no = '$cut_no'";
            $cutCon_a = " and b.cut_no = '$cut_no'";
        }
        else
        {
            $cutCon = " and c.cut_no = '$cutting_no'";
            $cutCon_a = " and b.cut_no = '$cutting_no'";
        }

        $cut_po_arr=return_library_array( "select order_id,order_id from  ppl_cut_lay_mst a, ppl_cut_lay_bundle b where a.id=b.mst_id and b.status_active=1 and a.cutting_no='$cutting_no'", "order_id", "order_id");
        // print_r($cut_po_arr);die;
        if(count($cut_po_arr)>0)
        {
            $cut_po_id_cond = where_con_using_array($cut_po_arr,0,"d.po_break_down_id");
        }
    }
    if($job_no!='')
    {
        if($is_exact=='true') $jobCon=" and f.job_no = '$job_no'";
        else  $jobCon=" and f.job_no like '%$job_no%'";

    }
    if($style_ref!='')
    {
        if($is_exact=='true') $style_cond=" and f.style_ref_no = '$style_ref'";
        else  $style_cond=" and f.style_ref_no like '%$style_ref%'";

    }
    $orderCon="";
    if($order_no)
    {
        if($is_exact=='true') $orderCon=" and e.po_number = '$order_no'";
        else  $orderCon=" and e.po_number like '%$order_no%'";

    }

    $bndlCon="";
    if($bndl_no)
    {
        if($is_exact=='true') $bndlCon=" and c.bundle_no = '$bndl_no'";
        else  $bndlCon=" and c.bundle_no like '%$bndl_no%'";

    }
    $year_cond="";
    if($syear)
    {
        $year_cond .= " and a.cutting_no like '%-$syear-%' ";
    }

    $job_year_cond="";
    if($job_year)
    {
        $job_year_cond .= " and to_char(a.insert_date,'YYYY')=$job_year";
    }

    if ($internal_ref != '') {
        $internal_ref_con = " and e.grouping ='$internal_ref'";
    }

    if ($order_cut_no != '')
    {
        $order_cut_no_con = " and h.order_cut_no ='$order_cut_no'";

    }

    if(trim($job_no)!='' || trim($ex_data[0])!="" || $internal_ref !="" || $style_ref !="")
    {
        if(trim($job_no)!='')
        {
            if($is_exact=='true')
            {
                $jobnoCond = " and a.job_no='$job_no'";
            }
            else
            {
                $jobnoCond = " and a.job_no_prefix_num=$job_no";
            }
        }
        if(trim($ex_data[0])!='')
        {
            $poCond = " and b.po_number='$ex_data[0]'";
        }
        if(trim($internal_ref)!='')
        {
            $refCond = " and b.grouping='$internal_ref'";
        }
        if(trim($ex_data[14])!='')
        {
            $styleCond = " and a.style_ref_no='$style_ref'";
        }
        $sql = "SELECT b.id from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and a.status_active=1 and b.status_active=1 $jobnoCond $poCond $refCond $styleCond $job_year_cond";
        // echo $sql;die;
        $res = sql_select($sql);
        $po_id_arr = array();
        foreach ($res as $val)
        {
            $po_id_arr[$val['ID']] = $val['ID'];
        }
        if(count($po_id_arr)>0)
        {
            $po_id_conds = where_con_using_array($po_id_arr,0,"a.po_break_down_id");
        }
    }


    // echo $tmp_cut;
   /*$scanned_bundle_arr = return_library_array("select bundle_no, bundle_no from pro_garments_production_dtls where production_type=4 and cut_no='".$tmp_cut."' and status_active=1 and is_deleted=0", 'bundle_no', 'bundle_no');
    foreach (explode(",", $selectedBuldle) as $bn) {
        $scanned_bundle_arr[$bn] = $bn;
    }*/

    $scanne=sql_select( "SELECT b.bundle_no, sum(b.production_qnty) as production_qnty,a.sewing_line from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=4  and b.status_active=1 and b.is_deleted=0 $cutCon_a $po_id_conds group by b.bundle_no,a.sewing_line");
    foreach($scanne as $row)
    {
        $duplicate_bundle[$row[csf("bundle_no")]] +=$row[csf("production_qnty")];
        $scanned_bundle_arr[$row[csf("bundle_no")]] = $row[csf("bundle_no")];

    }

    foreach (explode(",", $selectedBuldle) as $bn) {
        $scanned_bundle_arr[$bn] = $bn;
    }

    $challan=sql_select("SELECT d.id, d.sys_number_prefix_num, c.delivery_mst_id
    from pro_garments_production_mst a, pro_garments_production_dtls c, pro_gmts_delivery_mst d
    where a.id=c.mst_id and d.id=a.delivery_mst_id and c.delivery_mst_id=d.id $bndlCon $cutCon $po_id_conds and d.status_active=1 and d.is_deleted=0 and c.production_type=$qty_source group by d.id, d.sys_number_prefix_num, c.delivery_mst_id");
    $challan_arr=array();
    foreach($challan as $row)
    {
        $challan_arr[$row[csf("delivery_mst_id")]]["challan_num"] = $row[csf("sys_number_prefix_num")];
    }
    //print_r($challan_arr);



    //$cutting_no=return_field_value("cut_no", "pro_garments_production_dtls", "barcode_no in ($bundle_nos)");

    // echo $cutting_no;
    /* $sql_check = sql_select("SELECT preceding_op,succeding_op,embel_name,col_size_id from pro_production_sequence where current_operation=4 and cutting_no='$cutting_no'");
    // echo "select preceding_op,succeding_op,embel_name,col_size_id from pro_production_sequence where current_operation=4 and cutting_no='$cutting_no'";
    $qty_source_cond = "";
    $preceding_op = "";
    foreach ($sql_check as $chkrow)
    {
        if (($chkrow[csf("embel_name")] * 1) == 0)
        {
            $qty_source_cond = " and c.production_type=" . $chkrow[csf("preceding_op")];
            $preceding_op = $chkrow[csf("preceding_op")];
        }
        else
        {
            $qty_source_cond = " and c.production_type=" . $chkrow[csf("preceding_op")] . " and a.embel_name=" . $chkrow[csf("embel_name")];
            $preceding_op = $chkrow[csf("preceding_op")];
        }
	} */

    $last_operation=gmt_production_validation_script( 4, 1,'', $cutting_no, $production_squence);
    //$last_operation=gmt_production_validation_script( 4, 1 );
    // print_r($last_operation);

    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1150" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="50">Year</th>
            <th width="50">Job No</th>
            <th width="90">Order No</th>
            <th width="120">Style Ref.</th>
            <th width="130">Gmts Item</th>
            <th width="110">Country</th>
            <th width="100">Color</th>
            <th width="50">Size</th>
            <th width="120">Cut No</th>
            <th width="50">OCN</th>
            <th width="100">Challan No</th>
            <th width="80">Bundle No</th>
            <th>Bundle Qty.</th>
        </thead>
    </table>
    <div style="width:1170px; max-height:210px; overflow-y:auto" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1150" class="rpt_table" id="tbl_list_search">
        <?
            if($qty_source==9)
            {
                $prev_process = 9;
            }
            else
            {
                $prev_process = 3;
            }
            $i=1;
            $last_operation_string='';
            //foreach($last_operation as  $item_id=>$operation_cond)
            foreach($last_operation as  $item_id=>$operation_cond)
            {
                if( $operation_cond!=0 )
                {
                    $operation_conds=" and d.id in (".ltrim($operation_cond,",").") ";
                }
                else
                {
                    // echo "SELECT c.bundle_no, SUM(c.reject_qty) as reject_qty, SUM(c.alter_qty) as alter_qty, SUM(c.spot_qty) as spot_qty, SUM(c.replace_qty) as replace_qty  from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d where a.id=c.mst_id and c.color_size_break_down_id=d.id and c.production_type=3 and a.company_id=$company  $cut_po_id_cond $po_id_conds  $bndlCon  $cutCon and a.status_active=1 and a.is_deleted=0 group by  c.bundle_no order by  length(c.bundle_no) asc, c.bundle_no asc";die;
                    $job_year_cond = str_replace("a.insert_date","f.insert_date",$job_year_cond);
                    $sqld = sql_select( "SELECT c.bundle_no, SUM(c.reject_qty) as reject_qty, SUM(c.alter_qty) as alter_qty, SUM(c.spot_qty) as spot_qty, SUM(c.replace_qty) as replace_qty  from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d where a.id=c.mst_id and c.color_size_break_down_id=d.id and c.production_type=$prev_process and a.company_id=$company  $cut_po_id_cond $po_id_conds  $bndlCon  $cutCon and a.status_active=1 and a.is_deleted=0 group by  c.bundle_no order by  length(c.bundle_no) asc, c.bundle_no asc" );
                    foreach($sqld as $arows)
                    {
                        $reject_qty[$arows[csf('bundle_no')]]+=$arows[csf('reject_qty')];
                        $alter_qty[$arows[csf('bundle_no')]]+=$arows[csf('alter_qty')];
                        $spt_qty[$arows[csf('bundle_no')]]+=$arows[csf('spot_qty')];
                        $replace_qty[$arows[csf('bundle_no')]]+=$arows[csf('replace_qty')];
                    }
                }

                //echo $last_operation_string;
                // echo $is_controll;
                // if($is_controll==1)
                // {
                    // $sql="SELECT c.cut_no,d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, sum(c.production_qnty) as qty, sum(c.replace_qty) as replace_qty, e.po_number,c.barcode_no, c.delivery_mst_id
                    // from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e,wo_po_details_master f
                    // where d.job_id=f.id and e.job_id=f.id and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company $orderCon $bndlCon $year_cond  $jobCon $cutCon $internal_ref_con  and a.status_active=1 and a.is_deleted=0 AND c.production_type = $qty_source  and c.barcode_no is not null
                    // group by c.cut_no, c.bundle_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, e.po_number,c.barcode_no, c.delivery_mst_id
                    // order by  c.cut_no,length(c.bundle_no) asc, c.bundle_no asc";

                    // ============================== New SQL ======================================================

                    if($order_cut_no=="")
                    {
                        $sql="SELECT c.cut_no,d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, sum(c.production_qnty) as qty, sum(c.replace_qty) as replace_qty, c.barcode_no, c.delivery_mst_id
                        from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d
                        where a.id=c.mst_id and c.color_size_break_down_id=d.id  and a.company_id=$company $bndlCon   $po_id_conds $cutCon and a.status_active=1 and a.is_deleted=0 AND c.production_type = $qty_source  and c.barcode_no is not null
                        group by c.cut_no, c.bundle_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, c.barcode_no, c.delivery_mst_id
                        order by  c.cut_no,length(c.bundle_no) asc, c.bundle_no asc";
                    }
                    else
                    {
                        $sql = "SELECT a.cutting_no from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.id=b.mst_id and b.status_active=1 and b.order_cut_no=$order_cut_no $year_cond ";
                        // echo $sql;die;
                        $res = sql_select($sql);
                        $ocn_cut_arr = array();
                        foreach ($res as $v)
                        {
                            $ocn_cut_arr[$v['CUTTING_NO']]=$v['CUTTING_NO'];
                        }
                        $ocn_bndl_cond = where_con_using_array($ocn_cut_arr,1,"c.cut_no");

                        $sql="SELECT c.cut_no,d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, sum(c.production_qnty) as qty, sum(c.replace_qty) as replace_qty,c.barcode_no, c.delivery_mst_id
                        from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d
                        where a.id=c.mst_id and c.color_size_break_down_id=d.id and a.company_id=$company  $cut_po_id_cond $po_id_conds $bndlCon  $cutCon $ocn_bndl_cond  and a.status_active=1 and a.is_deleted=0 AND c.production_type = $qty_source  and c.barcode_no is not null
                        group by c.cut_no, c.bundle_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no,c.barcode_no, c.delivery_mst_id
                        order by  c.cut_no,length(c.bundle_no) asc, c.bundle_no asc";
                    }
                 // echo $sql;(die);
            //    /* }
            //     else
            //     {
            //         $sql="SELECT c.cut_no,d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, sum(c.production_qnty) as qty, sum(c.replace_qty) as replace_qty, e.po_number,c.barcode_no from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e,wo_po_details_master f where d.job_no_mst=f.job_no and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company $orderCon $bndlCon $year_cond  $item_id $jobCon $cutCon  and a.status_active=1 and a.is_deleted=0 $operation_conds group by c.cut_no, c.bundle_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, e.po_number,c.barcode_no order by  c.cut_no,length(c.bundle_no) asc, c.bundle_no asc";
            //     }*/

                //echo $sql;(die);
                $result = sql_select($sql);

                $cut_no_arr=array();
                $po_id_arr=array();
                foreach($result as $val)
                {
                    // if($scanned_bundle_arr[$val[csf("bundle_no")]]=="")
                    // {
                        $cut_no_arr[$val[csf("cut_no")]]=$val[csf("cut_no")];
                        $po_id_arr[$val[csf("po_break_down_id")]]=$val[csf("po_break_down_id")];
                    // }
                }
                $cutting_no_cond = where_con_using_array($cut_no_arr,1,"c.CUTTING_NO");
                $po_id_cond = where_con_using_array($po_id_arr,0,"a.order_id");
                $bundel_sql="SELECT a.bundle_no,b.order_cut_no from ppl_cut_lay_bundle a,ppl_cut_lay_dtls b,ppl_cut_lay_mst c where a.dtls_id=b.id and b.mst_id=c.id and a.status_active=1 and b.status_active=1 and c.status_active=1 $po_id_cond";
                // echo $bundel_sql;die();
                $bundle=sql_select($bundel_sql);
                $order_cut_no_arr=array();

                foreach($bundle as $vals )
                {
                        $order_cut_no_arr[$vals[csf("bundle_no")]]=$vals[csf("order_cut_no")];
                }
                // ====================== order data ========================
               //$po_id_cond = where_con_using_array($po_id_arr,0,"b.id");
                $po_sql = "SELECT a.style_ref_no, b.id,b.po_number from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id  and a.status_active=1 and b.status_active=1 $styleCond $poCond";
            //    echo $po_sql;die;
                $res = sql_select($po_sql);
                $po_data_arr = array();
                foreach ($res as $v)
                {
                    $po_data_arr[$v['ID']]['style'] = $v['STYLE_REF_NO'];
                    $po_data_arr[$v['ID']]['po'] = $v['PO_NUMBER'];
                }
            //    die('ok');

            //   echo "<pre>";
            //  print_r($po_data_arr) ;die;
                foreach ($result as $row)
                {
                    // echo $preceding_op."==".$row[csf('qty')]."=".$reject_qty[$row[csf('bundle_no')]]."*".$alter_qty[$row[csf('bundle_no')]]."*".$spt_qty[$row[csf('bundle_no')]]."<br>";

                    $rej_qty = 0;
                    $rej_qty = $reject_qty[$row[csf('bundle_no')]] + $alter_qty[$row[csf('bundle_no')]]+ $spt_qty[$row[csf('bundle_no')]];
                    if(($row[csf('qty')] - $rej_qty)>0) // when  qc pass qty more than reject qty
                    {
                        // if($preceding_op==3)
                        // {
                            $row[csf('qty')] = ($row[csf('qty')] + $replace_qty[$row[csf('bundle_no')]])- ($reject_qty[$row[csf('bundle_no')]] + $alter_qty[$row[csf('bundle_no')]]+ $spt_qty[$row[csf('bundle_no')]]);
                        // }

                    }
                    elseif (($rej_qty - $row[csf('qty')])>0) // when reject qty more than qc pass qty
                    {
                        $row[csf('qty')] = ($reject_qty[$row[csf('bundle_no')]] + $alter_qty[$row[csf('bundle_no')]]+ $spt_qty[$row[csf('bundle_no')]]) - $row[csf('qty')];
                    }

                    // $row[csf('qty')] = (($row[csf('qty')]) ) - ($reject_qty[$row[csf('bundle_no')]] + $alter_qty[$row[csf('bundle_no')]]+ $spt_qty[$row[csf('bundle_no')]]);
                    //+ $replace_qty[$row[csf('bundle_no')]]
                    $balance_qnty=$row[csf('qty')]-$duplicate_bundle[$row[csf("bundle_no")]];
                    if($scanned_bundle_arr[$row[csf('bundle_no')]]=="" && $row[csf('qty')]>0 && $balance_qnty>0)
                    {
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        list($shortName,$year,$job)=explode('-',$row[csf('job_no_mst')]);
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)">
                            <td width="40"><? echo $i; ?>
                                 <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
                                 <input type="hidden" name="hidden_qty" id="hidden_qty<?php echo $i; ?>" value="<?php echo $row[csf('qty')]; ?>"/>

                            </td>
                            <td width="50" align="center"><p><? echo $year; ?></p></td>
                            <td width="50" align="center"><p><? echo $job*1; ?></p></td>
                            <td width="90"><p><? echo $po_data_arr[$row[csf('po_break_down_id')]]['po']; ?></p></td>
                            <td width="120"><p><? echo $po_data_arr[$row[csf('po_break_down_id')]]['style']; ?></p></td>
                            <td width="130"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
                            <td width="110"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                            <td width="100"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
                            <td width="50"><p><? echo $size_arr[$row[csf('size_number_id')]]; ?></p></td>
                            <td width="120"><? echo $row[csf('cut_no')]; ?></td>
                            <td width="50"><? echo $order_cut_no_arr[$row[csf('bundle_no')]];?></td>
                            <td width="100" title="<? echo $row[csf('delivery_mst_id')]; ?>"><p><? echo $challan_arr[$row[csf('delivery_mst_id')]]['challan_num']; ?></p></td>
                            <td width="80"><? echo $row[csf('bundle_no')]; ?></td>
                            <td align="center" ><? echo $row[csf('qty')]; ?></td>
                        </tr>
                        <?
                        $i++;
                    }
                }
            }
            if(empty($last_operation))
            {
                //die;
                $sql="SELECT c.cut_no,d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, sum(c.production_qnty) as qty, e.po_number,c.barcode_no, f.style_ref_no from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e,wo_po_details_master f where d.job_id=f.id and f.id=e.job_id and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company and e.po_number like '$txt_order_no' and c.bundle_no like '$bundle_no' and  c.production_type=1 $jobCon $style_cond $cutCon  and a.status_active=1 and a.is_deleted=0 group by c.cut_no, c.bundle_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, e.po_number,c.barcode_no, f.style_ref_no order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";

                //order by c.cut_no, c.bundle_no DESC
                $last_operation_string='';
                $result = sql_select($sql);
                foreach ($result as $row)
                {
                    if($scanned_bundle_arr[$row[csf('bundle_no')]]=="")
                    {
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        list($shortName,$year,$job)=explode('-',$row[csf('job_no_mst')]);
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)">
                            <td width="40"><? echo $i; ?>
                                 <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>

                            </td>
                            <td width="50" align="center"><p><? echo $year; ?></p></td>
                            <td width="50" align="center"><p><? echo $job*1; ?></p></td>
                            <td width="90"><p><? echo $row[csf('po_number')]; ?></p></td>
                            <td width="120"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                            <td width="130"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
                            <td width="110"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                            <td width="100"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
                            <td width="50"><p><? echo $size_arr[$row[csf('size_number_id')]]; ?></p></td>
                            <td width="120"><? echo $row[csf('cut_no')]; ?></td>
                            <td width="80"><? echo $row[csf('bundle_no')]; ?></td>
                            <td align="right"><? echo $row[csf('qty')]; ?>&nbsp;&nbsp;</td>
                        </tr>
                    <?
                        $i++;
                    }
                }
            }
            ?>
            <input type="hidden" name="source_cond"  value="<?php echo $last_operation_string; ?>" id="source_cond"  />


        </table>
    </div>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1150" class="rpt_table">
        	<tfoot>
        		<tr>
            <th width="40"></th>
            <th width="50"></th>
            <th width="50"></th>
            <th width="90"></th>
            <th width="120"></th>
            <th width="130"></th>
            <th width="110"></th>
            <th width="100"></th>
            <th width="50"></th>
            <th width="120"></th>
            <th width="50"></th>
            <th width="100"></th>
            <th width="80">Total</th>
            <th align="center"  id="total_bndl_qty"></th>
        		</tr>
				<tr>
					<tr>
		</tr>
        	</tfoot>
        </table>
    <table width="980">
        <tr>
            <td align="center" >
               <span  style="float:left;"> <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All</span>
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
    <?
    exit();
}


if ($action == "challan_duplicate_check")
{
    $data=explode("__",$data);
    $result=sql_select("select a.sys_number,b.bundle_no from pro_gmts_delivery_mst a,pro_garments_production_dtls b where a.id=b.delivery_mst_id and b.barcode_no='$data[0]' and b.production_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.sys_number, b.bundle_no");
    foreach ($result as $row)
    {
       echo "Bundle No " . $row[csf('bundle_no')] . " Found in Challan No " . $row[csf('sys_number')] . ".";
      //echo "2_".$row[csf('bundle_no')]."**".$row[csf('sys_number')];
      // die;
    }

    exit();
}

if ($action == "populate_bundle_data")
{
    $ex_data = explode("**", $data);
    $bundle = explode(",", $ex_data[0]);
    $mst_id = explode(",", $ex_data[2]);
    $bundle_nos = "'".implode("','", $bundle)."'";
    $vscan=$ex_data[4];
    $source_cond=$ex_data[5];

	$bundle_count=count(explode(",",$bundle_nos)); $bundle_nos_cond=""; $scnbundle_nos_cond=""; $cutbundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nos_cond=" and (";
		$scnbundle_nos_cond=" and (";
		$cutbundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_nos),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
			$scnbundle_nos_cond.=" b.barcode_no in($bundleNos) or ";
			$cutbundle_nos_cond.=" barcode_no in($bundleNos) or ";
		}
		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";

		$scnbundle_nos_cond=chop($scnbundle_nos_cond,'or ');
		$scnbundle_nos_cond.=")";

		$cutbundle_nos_cond=chop($cutbundle_nos_cond,'or ');
		$cutbundle_nos_cond.=")";
	}
	else
	{
		$bundle_nos_cond=" and c.barcode_no in ($bundle_nos)";
		$scnbundle_nos_cond=" and b.barcode_no in ($bundle_nos)";
		$cutbundle_nos_cond=" and barcode_no in ($bundle_nos)";
	}

    $scanned_bundle_arr = return_library_array("SELECT b.bundle_no, b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=4 and b.status_active=1 and b.is_deleted=0 $scnbundle_nos_cond", 'bundle_no', 'bundle_no');
    $size_arr = return_library_array("select id, size_name from lib_size", 'id', 'size_name');
    $color_arr = return_library_array("select id, color_name from lib_color", "id", "color_name");
    $country_arr = return_library_array("select id, country_name from lib_country", 'id', 'country_name');
    $buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');

    $year_field = "";
    if ($db_type == 0) {
        $year_field = "YEAR(a.insert_date)";
    } else if ($db_type == 2) {
        $year_field = "to_char(a.insert_date,'YYYY')";
    }

    $preceding_process = return_field_value("preceding_page_id", "variable_settings_production", "company_name=$ex_data[3] and variable_list=33 and page_category_id=28 and status_active=1 and is_deleted=0");
    $qty_source=1;
    if($preceding_process==117) $qty_source=1;//Cutting qc
    else if($preceding_process==123) $qty_source=9;//Cutting Delivery To Input Challan

    $last_operation=array();
    //$last_operation=gmt_production_validation_script( 4, 1);

    $cutting_no=return_field_value("cut_no", "pro_garments_production_dtls", "1=1 $cutbundle_nos_cond");
   /*  $sql_check = sql_select("SELECT preceding_op,succeding_op,embel_name,col_size_id from pro_production_sequence where current_operation=4 and cutting_no='$cutting_no'");
    // echo "select preceding_op,succeding_op,embel_name,col_size_id from pro_production_sequence where current_operation=4 and cutting_no='$cutting_no'";
    $qty_source_cond = "";
    foreach ($sql_check as $chkrow)
    {
        if (($chkrow[csf("embel_name")] * 1) == 0)
        {
            $qty_source_cond = " and c.production_type=" . $chkrow[csf("preceding_op")];
        }
        else
        {
            $qty_source_cond = " and c.production_type=" . $chkrow[csf("preceding_op")] . " and a.embel_name=" . $chkrow[csf("embel_name")];
        }
	} */


    if($qty_source==9)
    {
        $prev_process = 9;
    }
    else
    {
        $prev_process = 3;
    }

    //$cutting_no =trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT));
    $last_operation=gmt_production_validation_script( 4, $qty_source,'', $cutting_no, $production_squence);
    // print_r($last_operation);die('kakku');
    foreach($last_operation as  $item_id=>$operation_cond)
    {
        //echo $item_id;die;
        if( $operation_cond!=0 ) $operation_conds=" and d.id in (".ltrim($operation_cond,",").") ";
        else
        {
            $sqld = sql_select( "SELECT  c.bundle_no, c.reject_qty,c.alter_qty, c.spot_qty, c.replace_qty  from pro_garments_production_mst a,pro_garments_production_dtls c where a.id=c.mst_id and a.company_id=$ex_data[3] and c.production_type=$prev_process and c.status_active=1 and c.is_deleted=0 $bundle_nos_cond " );
            foreach($sqld as $arows)
            {
                $reject_qty[$arows[csf('bundle_no')]]+=$arows[csf('reject_qty')];
                $alter_qty[$arows[csf('bundle_no')]]+=$arows[csf('alter_qty')];
                $spt_qty[$arows[csf('bundle_no')]]+=$arows[csf('spot_qty')];
                $replace_qty[$arows[csf('bundle_no')]]+=$arows[csf('replace_qty')];
            }
        }
        // echo $item_id;die('kakku');
        $sql="SELECT c.id as prdid, d.id as colorsizeid, d.po_break_down_id as po_id, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no, c.production_qnty as production_qnty,c.barcode_no from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d where a.company_id='$ex_data[3]' and a.id=c.mst_id and c.color_size_break_down_id=d.id and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $bundle_nos_cond $item_id $operation_conds";
        //c.delivery_mst_id =a.delivery_mst_id
        // echo $sql; die;
        $result = sql_select($sql);
        $po_id_arr = array();
        $data_array = array();
        foreach ($result as $v)
        {
            $po_id_arr[$v['PO_ID']] = $v['PO_ID'];
            $data_array[$v['BARCODE_NO']]['prdid'] = $v['PRDID'];
            $data_array[$v['BARCODE_NO']]['colorsizeid'] = $v['COLORSIZEID'];
            $data_array[$v['BARCODE_NO']]['po_id'] = $v['PO_ID'];
            $data_array[$v['BARCODE_NO']]['item_number_id'] = $v['ITEM_NUMBER_ID'];
            $data_array[$v['BARCODE_NO']]['country_id'] = $v['COUNTRY_ID'];
            $data_array[$v['BARCODE_NO']]['size_number_id'] = $v['SIZE_NUMBER_ID'];
            $data_array[$v['BARCODE_NO']]['color_number_id'] = $v['COLOR_NUMBER_ID'];
            $data_array[$v['BARCODE_NO']]['cut_no'] = $v['CUT_NO'];
            $data_array[$v['BARCODE_NO']]['bundle_no'] = $v['BUNDLE_NO'];
            $data_array[$v['BARCODE_NO']]['production_qnty'] += $v['PRODUCTION_QNTY'];
        }
        // print_r($data_array);die;
        $po_ids = implode(",",$po_id_arr);

        $order_sql = "SELECT a.job_no_prefix_num,a.style_ref_no, $year_field as year, a.buyer_name,b.po_number,b.id from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and b.status_active=1 and b.id in($po_ids)";
        // echo $order_sql;die;
        $res = sql_select($order_sql);
        $order_data_array = array();
        foreach ($res as $val)
        {
            $order_data_array[$val['ID']]['job_prefix'] = $val['JOB_NO_PREFIX_NUM'];
            $order_data_array[$val['ID']]['style'] = $val['STYLE_REF_NO'];
            $order_data_array[$val['ID']]['year'] = $val['YEAR'];
            $order_data_array[$val['ID']]['buyer_name'] = $val['BUYER_NAME'];
            $order_data_array[$val['ID']]['po_number'] = $val['PO_NUMBER'];
        }
        $count=count($data_array);
        $i=$ex_data[1]+$count;
        foreach ($data_array as $barcode => $row)
        {

            if(trim($scanned_bundle_arr[$row['bundle_no']])=="")
            {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                if($qty_source==9)
                {
                    $qty = $row['production_qnty'];
                }
                else
                {
                    $qty = ($row['production_qnty'] + $replace_qty[$row['bundle_no']]) - ($reject_qty[$row['bundle_no']] + $alter_qty[$row['bundle_no']]+ $spt_qty[$row['bundle_no']]);
                }

            //  $qty=$row[csf('production_qnty')];
                //+ $replace_qty[$row[csf('bundle_no')]]
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="90" id="bundle_<? echo $i; ?>" title="<? echo $barcode; ?>"><? echo $row['bundle_no']; ?></td>
                    <td width="50" align="center"><? echo $order_data_array[$row['po_id']]['year']; ?></td>
                    <td width="60" align="center"><? echo $order_data_array[$row['po_id']]['job_prefix']; ?></td>
                    <td width="70" align="center"><p><? echo $order_data_array[$row['po_id']]['style']; ?></p></td>
                    <td width="65"><? echo $buyer_arr[$order_data_array[$row['po_id']]['buyer_name']]; ?></td>
                    <td width="90" style="word-break:break-all;" align="left"><? echo $order_data_array[$row['po_id']]['po_number']; ?></td>
                    <td width="120" style="word-break:break-all;" align="left"><? echo $garments_item[$row['item_number_id']]; ?></td>
                    <td width="100" style="word-break:break-all;" align="left"><? echo $country_arr[$row['country_id']]; ?></td>
                    <td width="80" style="word-break:break-all;" align="left"><? echo $color_arr[$row['color_number_id']]; ?></td>
                    <td width="70" style="word-break:break-all;" align="left">&nbsp;<? echo $size_arr[$row['size_number_id']]; ?></td>
                    <td width="80" id="prodQty_<? echo $i; ?>" align="right"><? echo $qty; ?>&nbsp;</td>
                    <td id="button_1" align="center">
                        <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />

                        <input type="hidden" name="cutNo[]" id="cutNo_<? echo $i; ?>" value="<? echo $row['cut_no']; ?>"/>
                        <input type="hidden" name="colorSizeId[]" id="colorSizeId_<? echo $i; ?>" value="<? echo $row['colorsizeid']; ?>"/>
                        <input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $row['po_id']; ?>"/>
                        <input type="hidden" name="gmtsitemId[]" id="gmtsitemId_<? echo $i; ?>" value="<? echo $row['item_number_id']; ?>"/>
                        <input type="hidden" name="countryId[]" id="countryId_<? echo $i; ?>" value="<? echo $row['country_id']; ?>"/>
                        <input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row['color_number_id']; ?>"/>
                        <input type="hidden" name="sizeId[]" id="sizeId_<? echo $i; ?>" value="<? echo $row['size_number_id']; ?>"/>
                        <input type="hidden" name="qty[]" id="qty_<? echo $i; ?>" value="<? echo $qty; ?>"/>
                        <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value="<? echo $row['prdid']; ?>"/>
                        <input type="hidden" name="isRescan[]" id="isRescan_<? echo $i; ?>" value="0"/>
                    </td>
                </tr>
                <?
                $i--;
            }
        }
    }

    if(empty($last_operation))
    {
          $sql="SELECT d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, MAX($year_field) as year, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no, sum(c.production_qnty) as production_qnty, e.po_number,c.barcode_no from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where f.company_name='$ex_data[3]' and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_id=f.id and f.id=d.job_id  and c.production_type=1 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $bundle_nos_cond group by d.id, e.id, f.job_no_prefix_num, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no,c.barcode_no, e.po_number order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";

    }

    exit();
}

if ($action == "populate_bundle_data_bk") // 30-10-2022
{
    $ex_data = explode("**", $data);
    $bundle = explode(",", $ex_data[0]);
    $mst_id = explode(",", $ex_data[2]);
    $bundle_nos = "'" . implode("','", $bundle) . "'";
    $vscan=$ex_data[4];
    $source_cond=$ex_data[5];

	$bundle_count=count(explode(",",$bundle_nos)); $bundle_nos_cond=""; $scnbundle_nos_cond=""; $cutbundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nos_cond=" and (";
		$scnbundle_nos_cond=" and (";
		$cutbundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_nos),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
			$scnbundle_nos_cond.=" b.barcode_no in($bundleNos) or ";
			$cutbundle_nos_cond.=" barcode_no in($bundleNos) or ";
		}
		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";

		$scnbundle_nos_cond=chop($scnbundle_nos_cond,'or ');
		$scnbundle_nos_cond.=")";

		$cutbundle_nos_cond=chop($cutbundle_nos_cond,'or ');
		$cutbundle_nos_cond.=")";
	}
	else
	{
		$bundle_nos_cond=" and c.barcode_no in ($bundle_nos)";
		$scnbundle_nos_cond=" and b.barcode_no in ($bundle_nos)";
		$cutbundle_nos_cond=" and barcode_no in ($bundle_nos)";
	}

    $scanned_bundle_arr = return_library_array("SELECT b.bundle_no, b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=4 and b.status_active=1 and b.is_deleted=0 $scnbundle_nos_cond", 'bundle_no', 'bundle_no');
    $size_arr = return_library_array("select id, size_name from lib_size", 'id', 'size_name');
    $color_arr = return_library_array("select id, color_name from lib_color", "id", "color_name");
    $country_arr = return_library_array("select id, country_name from lib_country", 'id', 'country_name');
    $buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');

    $year_field = "";
    if ($db_type == 0) {
        $year_field = "YEAR(f.insert_date)";
    } else if ($db_type == 2) {
        $year_field = "to_char(f.insert_date,'YYYY')";
    }

    $preceding_process = return_field_value("preceding_page_id", "variable_settings_production", "company_name=$ex_data[3] and variable_list=33 and page_category_id=28 and status_active=1 and is_deleted=0");
    $qty_source=1;
    if($preceding_process==117) $qty_source=1;//Cutting qc
    else if($preceding_process==123) $qty_source=9;//Cutting Delivery To Input Challan

    $last_operation=array();
    //$last_operation=gmt_production_validation_script( 4, 1);

    $cutting_no=return_field_value("cut_no", "pro_garments_production_dtls", "1=1 $cutbundle_nos_cond");
   /*  $sql_check = sql_select("SELECT preceding_op,succeding_op,embel_name,col_size_id from pro_production_sequence where current_operation=4 and cutting_no='$cutting_no'");
    // echo "select preceding_op,succeding_op,embel_name,col_size_id from pro_production_sequence where current_operation=4 and cutting_no='$cutting_no'";
    $qty_source_cond = "";
    foreach ($sql_check as $chkrow)
    {
        if (($chkrow[csf("embel_name")] * 1) == 0)
        {
            $qty_source_cond = " and c.production_type=" . $chkrow[csf("preceding_op")];
        }
        else
        {
            $qty_source_cond = " and c.production_type=" . $chkrow[csf("preceding_op")] . " and a.embel_name=" . $chkrow[csf("embel_name")];
        }
	} */

    //$cutting_no =trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT));
    $last_operation=gmt_production_validation_script( 4, $qty_source,'', $cutting_no, $production_squence);
    // print_r($last_operation);die('kakku');
    foreach($last_operation as  $item_id=>$operation_cond)
    {
        //echo $item_id;die;
        if( $operation_cond!=0 ) $operation_conds=" and d.id in (".ltrim($operation_cond,",").") ";
        else
        {
            $sqld = sql_select( "SELECT  c.bundle_no, SUM(c.reject_qty) as reject_qty, SUM(c.alter_qty) as alter_qty, SUM(c.spot_qty) as spot_qty, SUM(c.replace_qty) as replace_qty  from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where a.id=c.mst_id and a.po_break_down_id=e.id and f.company_name=$ex_data[3] and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_id=f.id and f.id=d.job_id and c.production_type=3 and c.status_active=1 and c.is_deleted=0 $bundle_nos_cond group by  c.bundle_no order by  length(c.bundle_no) asc, c.bundle_no asc" );
            foreach($sqld as $arows)
            {
                $reject_qty[$arows[csf('bundle_no')]]+=$arows[csf('reject_qty')];
                $alter_qty[$arows[csf('bundle_no')]]+=$arows[csf('alter_qty')];
                $spt_qty[$arows[csf('bundle_no')]]+=$arows[csf('spot_qty')];
                $replace_qty[$arows[csf('bundle_no')]]+=$arows[csf('replace_qty')];
            }
        }
        // echo $item_id;die('kakku');
        $sql="SELECT max(c.id) as prdid, d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, MAX($year_field) as year, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no, sum(c.production_qnty) as production_qnty, e.po_number,c.barcode_no from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where f.company_name='$ex_data[3]' and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_id=f.id and f.id=d.job_id and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $bundle_nos_cond $item_id $operation_conds  group by d.id, e.id, f.job_no_prefix_num, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no,c.barcode_no, e.po_number order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
        //c.delivery_mst_id =a.delivery_mst_id
        // echo $sql; die;
        $result = sql_select($sql);
        $count=count($result);
        $i=$ex_data[1]+$count;
        foreach ($result as $row)
        {

            if(trim($scanned_bundle_arr[$row[csf('bundle_no')]])=="")
            {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                $qty = ($row[csf('production_qnty')] + $replace_qty[$row[csf('bundle_no')]]) - ($reject_qty[$row[csf('bundle_no')]] + $alter_qty[$row[csf('bundle_no')]]+ $spt_qty[$row[csf('bundle_no')]]);

            //  $qty=$row[csf('production_qnty')];
                //+ $replace_qty[$row[csf('bundle_no')]]
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i;?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="90" id="bundle_<? echo $i; ?>" title="<? echo $row[csf('barcode_no')]; ?>"><? echo $row[csf('bundle_no')]; ?></td>
                    <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                    <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="65"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                    <td width="90" style="word-break:break-all;" align="left"><? echo $row[csf('po_number')]; ?></td>
                    <td width="120" style="word-break:break-all;" align="left"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
                    <td width="100" style="word-break:break-all;" align="left"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
                    <td width="80" style="word-break:break-all;" align="left"><? echo $color_arr[$row[csf('color_number_id')]]; ?></td>
                    <td width="70" style="word-break:break-all;" align="left">&nbsp;<? echo $size_arr[$row[csf('size_number_id')]]; ?></td>
                    <td width="80" id="prodQty_<? echo $i; ?>" align="right"><? echo $qty; ?>&nbsp;</td>
                    <td id="button_1" align="center">
                        <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />

                        <input type="hidden" name="cutNo[]" id="cutNo_<? echo $i; ?>" value="<? echo $row[csf('cut_no')]; ?>"/>
                        <input type="hidden" name="colorSizeId[]" id="colorSizeId_<? echo $i; ?>" value="<? echo $row[csf('colorsizeid')]; ?>"/>
                        <input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $row[csf('po_id')]; ?>"/>
                        <input type="hidden" name="gmtsitemId[]" id="gmtsitemId_<? echo $i; ?>" value="<? echo $row[csf('item_number_id')]; ?>"/>
                        <input type="hidden" name="countryId[]" id="countryId_<? echo $i; ?>" value="<? echo $row[csf('country_id')]; ?>"/>
                        <input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_number_id')]; ?>"/>
                        <input type="hidden" name="sizeId[]" id="sizeId_<? echo $i; ?>" value="<? echo $row[csf('size_number_id')]; ?>"/>
                        <input type="hidden" name="qty[]" id="qty_<? echo $i; ?>" value="<? echo $qty; ?>"/>
                        <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value="<? echo $row[csf('prdid')]; ?>"/>
                        <input type="hidden" name="isRescan[]" id="isRescan_<? echo $i; ?>" value="0"/>
                    </td>
                </tr>
                <?
                $i--;
            }
        }
    }

    if(empty($last_operation))
    {
          $sql="SELECT d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, MAX($year_field) as year, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no, sum(c.production_qnty) as production_qnty, e.po_number,c.barcode_no from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where f.company_name='$ex_data[3]' and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_id=f.id and f.id=d.job_id  and c.production_type=1 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $bundle_nos_cond group by d.id, e.id, f.job_no_prefix_num, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no,c.barcode_no, e.po_number order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";

    }

    exit();
}

if ($action == "populate_bundle_data_check")
{

    $ex_data = explode("**", $data);
    $bundle = explode(",", $ex_data[0]);
    $mst_id = explode(",", $ex_data[2]);
    $bundle_nos = "'" . implode("','", $bundle) . "'";

	$bundle_count=count(explode(",",$bundle_nos)); $bundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_nos),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
		}
		$bundle_nos_cond=chop($bundle_nos_cond,'or ');
		$bundle_nos_cond.=")";
	}
	else
	{
		$bundle_nos_cond=" and c.barcode_no in ($bundle_nos)";
	}

    $year_field = "";
    if ($db_type == 0) {
        $year_field = "YEAR(f.insert_date)";
    } else if ($db_type == 2) {
        $year_field = "to_char(f.insert_date,'YYYY')";
    }

    if( $ex_data[4]!=0 )
        $str_cond=" and a.sewing_line= $ex_data[4]";
    else
        $str_cond="";

    $sql = "SELECT d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, f.company_name, MAX($year_field) as year, f.buyer_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no, SUM(c.reject_qty) as raj_qty, SUM(c.alter_qty) as alt_qty, SUM(c.spot_qty) as spt_qty, SUM(c.replace_qty) as replace_qty, e.po_number from pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_id=f.id and f.id=d.job_id and c.production_type=1 and c.status_active=1 and c.is_deleted=0 $bundle_nos_cond group by d.id, e.id, f.job_no_prefix_num, f.buyer_name, f.company_name, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id,c.cut_no, c.bundle_no, e.po_number order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
    $result = sql_select($sql);
    $msg_type=1;
    if(count($result)>0){
        foreach($result as $row)
        {
            if($row[csf('company_name')]!=$ex_data[3]){
                $msg_type=2;
            }
        }
    }
    else $msg_type=3;

    echo $msg_type;
    exit();
}

if ($action == "bundle_nos") {


    /*  if($db_type==0)
    {
        $bundle_nos=return_field_value("group_concat(b.bundle_no order by b.id desc) as bundle_no", "pro_garments_production_mst a, pro_garments_production_dtls b","a.id=b.mst_id and a.delivery_mst_id=$data and b.production_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0",'bundle_no');
    }
    else if($db_type==2)
    {
        $bundle_nos=return_field_value("LISTAGG(b.bundle_no, ',') WITHIN GROUP (ORDER BY b.id desc) as bundle_no", "pro_garments_production_mst a, pro_garments_production_dtls b","a.id=b.mst_id and a.delivery_mst_id=$data and b.production_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0",'bundle_no');
    }*/

    $bundle_nosww = return_library_array("SELECT b.barcode_no, b.bundle_no from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.delivery_mst_id=$data and b.production_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "bundle_no", "barcode_no");

    if(count($bundle_nosww)>0)
    {
        $bundle_nos_cond = where_con_using_array($bundle_nosww,1,"b.barcode_no");
    }
    $bundle_nos = implode(",", $bundle_nosww);

    $output_bundles=return_library_array( "select b.bundle_no, sum(b.production_qnty) as issue_qty from pro_gmts_delivery_mst a,pro_garments_production_dtls b where  a.id=b.delivery_mst_id $bundle_nos_cond and b.production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.bundle_no",'bundle_no','issue_qty');

    if(count($output_bundles)>0)
        echo $bundle_nos."**1";
    else
        echo $bundle_nos."**0";
    exit();
}

if ($action == "color_and_size_level") {
    $dataArr = explode("**", $data);
    $po_id = $dataArr[0];
    $item_id = $dataArr[1];
    $variableSettings = $dataArr[2];
    $styleOrOrderWisw = $dataArr[3];
    $embelName = $dataArr[4];
    $country_id = $dataArr[5];

    $color_library = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
    $size_library = return_library_array("select id, size_name from lib_size", 'id', 'size_name');

    //#############################################################################################//
    //order wise - color level, color and size level

    //$variableSettings=2;

    if ($variableSettings == 2) // color level
    {
        if ($db_type == 0) {
            $sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty,
            (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END)
            from pro_garments_production_dtls pdtls where pdtls.production_type=1 and pdtls.is_deleted=0 ) as production_qnty,
            (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END)
            from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name='$embelName'
            and cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty
            from wo_po_color_size_breakdown
            where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1
            group by color_number_id";
        } else {
            $sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
            sum(CASE WHEN c.production_type=1 then b.production_qnty ELSE 0 END) as production_qnty,
            sum(CASE WHEN c.production_type=2 and c.embel_name='$embelName' then b.production_qnty ELSE 0 END) as cur_production_qnty
            from wo_po_color_size_breakdown a
            left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
            left join pro_garments_production_mst c on c.id=b.mst_id
            where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and
            a.status_active=1 group by a.item_number_id, a.color_number_id";

        }

        $colorResult = sql_select($sql);
    } else if ($variableSettings == 3) //color and size level
    {


        $dtlsData = sql_select("select a.color_size_break_down_id,
            sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty,
            sum(CASE WHEN a.production_type=2 and b.embel_name='$embelName' then a.production_qnty ELSE 0 END) as cur_production_qnty
            from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and
            b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0
            and a.production_type in(1,2) group by a.color_size_break_down_id");

        foreach ($dtlsData as $row) {
            $color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss'] = $row[csf('production_qnty')];
            $color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv'] = $row[csf('cur_production_qnty')];
        }
        //print_r($color_size_qnty_array);

        $sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
        from wo_po_color_size_breakdown
        where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1
        order by color_number_id, id";

        $colorResult = sql_select($sql);
    }

    $colorHTML = "";
    $colorID = '';
    $chkColor = array();
    $i = 0;
    $totalQnty = 0;
    foreach ($colorResult as $color) {
        if ($variableSettings == 2) // color level
        {
            $colorHTML .= '<tr><td>' . $color_library[$color[csf("color_number_id")]] . '</td><td><input type="text" name="txt_color" id="colSize_' . ($i + 1) . '" style="width:80px"  class="text_boxes_numeric" placeholder="' . ($color[csf("production_qnty")] - $color[csf("cur_production_qnty")]) . '" onblur="fn_colorlevel_total(' . ($i + 1) . ')"></td></tr>';
            $totalQnty += $color[csf("production_qnty")] - $color[csf("cur_production_qnty")];
            $colorID .= $color[csf("color_number_id")] . ",";
        } else //color and size level
        {
            if (!in_array($color[csf("color_number_id")], $chkColor)) {
                if ($i != 0) $colorHTML .= "</table></div>";
                $i = 0;
                $colorHTML .= '<h3 align="left" id="accordion_h' . $color[csf("color_number_id")] . '" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_' . $color[csf("color_number_id")] . '\', \'\',1)"> <span id="accordion_h' . $color[csf("color_number_id")] . 'span">+</span>' . $color_library[$color[csf("color_number_id")]] . ' : <span id="total_' . $color[csf("color_number_id")] . '"></span> </h3>';
                $colorHTML .= '<div id="content_search_panel_' . $color[csf("color_number_id")] . '" style="display:none" class="accord_close"><table id="table_' . $color[csf("color_number_id")] . '">';
                $chkColor[] = $color[csf("color_number_id")];
            }
            //$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
            $colorID .= $color[csf("size_number_id")] . "*" . $color[csf("color_number_id")] . ",";

            $iss_qnty = $color_size_qnty_array[$color[csf('id')]]['iss'];
            $rcv_qnty = $color_size_qnty_array[$color[csf('id')]]['rcv'];


            $colorHTML .= '<tr><td>' . $size_library[$color[csf("size_number_id")]] . '</td><td><input type="text" name="colorSize" id="colSize_' . $color[csf("color_number_id")] . ($i + 1) . '"  class="text_boxes_numeric" style="width:100px" placeholder="' . ($iss_qnty - $rcv_qnty) . '" onblur="fn_total(' . $color[csf("color_number_id")] . ',' . ($i + 1) . ')"></td></tr>';
        }
        $i++;
    }
    //echo $colorHTML;die;
    if ($variableSettings == 2) {
        $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th></thead><tbody>' . $colorHTML . '<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="' . $totalQnty . '" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>';
    }
    echo "$('#breakdown_td_id').html('" . addslashes($colorHTML) . "');\n";
    $colorList = substr($colorID, 0, -1);
    echo "$('#hidden_colorSizeID').val('" . $colorList . "');\n";
    //#############################################################################################//
    exit();
}

if ($action == "show_dtls_listview") {
    $location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
    $company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
    $supplier_arr = return_library_array("select id, supplier_name from  lib_supplier", 'id', 'supplier_name');

    ?>
    <div style="width:100%;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
            <th width="50">SL</th>
            <th width="150" align="center">Item Name</th>
            <th width="120" align="center">Country</th>
            <th width="80" align="center">Production Date</th>
            <th width="80" align="center">Production Qnty</th>
            <th width="150" align="center">Serving Company</th>
            <th width="120" align="center">Location</th>
            <th align="center">Challan No</th>
            </thead>
        </table>
    </div>
    <div style="width:100%;max-height:180px; overflow-y:scroll" id="sewing_production_list_view" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <?php
            $i = 1;
            $total_production_qnty = 0;
            $sqlResult = sql_select("select id,po_break_down_id,item_number_id,country_id,production_date,production_quantity,production_source,
                serving_company,location,challan_no from pro_garments_production_mst where delivery_mst_id='$data' and status_active=1
                and is_deleted=0 order by id");
            foreach ($sqlResult as $selectResult) {
                if ($i % 2 == 0) $bgcolor = "#E9F3FF";
                else $bgcolor = "#FFFFFF";
                $total_production_qnty += $selectResult[csf('production_quantity')];
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                    onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_issue_form_data','requires/bundle_wise_sewing_input_controller');">
                    <td width="50" align="center"><? echo $i; ?></td>
                    <td width="150" align="center">
                        <p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p></td>
                    <td width="120" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p>
                    </td>
                    <td width="80"
                        align="center"><?php echo change_date_format($selectResult[csf('production_date')]); ?></td>
                    <td width="80" align="center"><?php echo $selectResult[csf('production_quantity')]; ?></td>
                    <?php
                    $source = $selectResult[csf('production_source')];
                    if ($source == 3) $serving_company = $supplier_arr[$selectResult[csf('serving_company')]];
                    else $serving_company = $company_arr[$selectResult[csf('serving_company')]];
                    ?>
                    <td width="150" align="center"><p><?php echo $serving_company; ?></p></td>
                    <td width="120" align="center"><p><? echo $location_arr[$selectResult[csf('location')]]; ?></p></td>
                    <td align="center"><p><?php echo $selectResult[csf('challan_no')]; ?></p></td>
                </tr>
                <?php
                $i++;
            }
            ?>
        </table>
    </div>
    <?
    exit();
}

if ($action == "show_country_listview") {
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="370" class="rpt_table">
        <thead>
        <th width="30">SL</th>
        <th width="110">Item Name</th>
        <th width="80">Country</th>
        <th width="75">Shipment Date</th>
        <th>Order Qty.</th>
        </thead>
        <?
        $i = 1;

        $sqlResult = sql_select("select po_break_down_id, item_number_id, country_id, max(country_ship_date) as country_ship_date, sum(order_quantity) as
            order_qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$data' and status_active=1 and is_deleted=0
            group by po_break_down_id, item_number_id, country_id");
        foreach ($sqlResult as $row) {
            if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                onClick="put_country_data(<? echo $row[csf('po_break_down_id')] . "," . $row[csf('item_number_id')] . "," . $row[csf('country_id')] . "," . $row[csf('order_qnty')] . "," . $row[csf('plan_cut_qnty')]; ?>);">
                <td width="30"><? echo $i; ?></td>
                <td width="110"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
                <td width="80"><p><? echo $country_library[$row[csf('country_id')]]; ?>&nbsp;</p></td>
                <td width="75"
                    align="center"><? if ($row[csf('country_ship_date')] != "0000-00-00") echo change_date_format($row[csf('country_ship_date')]); ?>
                    &nbsp;</td>
                <td align="right"><?php echo $row[csf('order_qnty')]; ?></td>
            </tr>
            <?
            $i++;
        }
        ?>
    </table>
    <?
    exit();
}

if($action=="populate_bundle_data_update")
{
    $ex_data = explode("**",$data);
    $bundle=explode(",",trim($ex_data[0]));
    $mst_id=$ex_data[2];
    $bundle_nos="'".implode("','",$bundle)."'";

    $bundle_count=count(explode(",",trim($ex_data[0]))); $bundle_nos_cond=""; $recbundle_nos_cond="";
    if($db_type==2 && $bundle_count>400)
    {
        $recbundle_nos_cond=" and (";
        $bundle_nos_cond=" and (";
        $bundleArr=array_chunk(explode(",",trim($ex_data[0])),399);
        foreach($bundleArr as $bundleNos)
        {
            $bundleNos=implode(",",$bundleNos);
            $recbundle_nos_cond.=" c.barcode_no in($bundleNos) or ";
            $bundle_nos_cond.=" b.barcode_no in($bundleNos) or ";
        }
        $recbundle_nos_cond=chop($recbundle_nos_cond,'or ');
        $recbundle_nos_cond.=")";

        $bundle_nos_cond=chop($bundle_nos_cond,'or ');
        $bundle_nos_cond.=")";
    }
    else
    {
        $recbundle_nos_cond=" and c.barcode_no in ($bundle_nos)";
        $bundle_nos_cond=" and b.barcode_no in ($bundle_nos)";
    }

    //$scanned_bundle_arr=return_library_array( "select b.bundle_no, b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=2 and a.embel_name=1 and b.status_active=1 and b.is_deleted=0",'bundle_no','bundle_no');
    $size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
    $color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
    $country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

    $output_bundles=return_library_array( "select b.bundle_no, sum(b.production_qnty) as issue_qty from pro_gmts_delivery_mst a,pro_garments_production_dtls b where  a.id=b.delivery_mst_id and b.production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bundle_nos_cond group by b.bundle_no",'bundle_no','issue_qty');
    //echo  "select b.bundle_no, sum(b.production_qnty) as issue_qty from pro_gmts_delivery_mst a,pro_garments_production_dtls b where and b.barcode_no in ($bundle_nos)  and b.production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.bundle_no";
    //$receive_qty=return_field_value("","","a.id=b.delivery_mst_id and b.barcode_no in ($bundle_no","issue_qty");

    //print_r($output_bundles);

    $year_field="";
    if($db_type==0)
    {
        $year_field="YEAR(f.insert_date)";
    }
    else if($db_type==2)
    {
        $year_field="to_char(f.insert_date,'YYYY')";
    }

    $sql="SELECT c.id as prdid,d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, $year_field as year, f.buyer_name, f.style_ref_no,d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no,c.bundle_no,c.barcode_no,c.production_qnty,c.is_rescan, e.po_number from pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where f.company_name='$ex_data[3]' and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_id=f.id and f.id=d.job_id  and c.production_type=4 and c.delivery_mst_id=".$mst_id." and c.status_active=1 and c.is_deleted=0 $recbundle_nos_cond order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
    //echo $sql;die;
    $result = sql_select($sql);
    $count=count($result);
    $i=$ex_data[1]+$count;
    foreach ($result as $row)
    {
        if($scanned_bundle_arr[$row[csf('bundle_no')]]=="" || $mst_id[0]!="")
        {
            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            // $qty=($bundle_qty_arr[$row[csf('bundle_no')]]+$row[csf('replace_qty')])-($row[csf('raj_qty')]+$row[csf('alt_qty')]+$row[csf('spt_qty')]);
            $qty=$row[csf('production_qnty')];
        ?>
            <tr bgcolor="<?= $bgcolor; ?>" id="tr_<?= $i;?>">
                <td width="30"><?= $i; ?></td>
                <td width="90" id="bundle_<?= $i; ?>" title="<?= $row[csf('barcode_no')]; ?>"><?= $row[csf('bundle_no')]; ?></td>
                <td width="50" align="center"><?= $row[csf('year')]; ?></td>
                <td width="60" align="center"><?= $row[csf('job_no_prefix_num')]; ?></td>
                <td width ="70"><p><?= $row[csf('style_ref_no')]; ?></p></td>
                <td width="65"><?= $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                <td width="90" style="word-break:break-all;" align="left"><?= $row[csf('po_number')]; ?></td>
                <td width="120" style="word-break:break-all;" align="left"><?= $garments_item[$row[csf('item_number_id')]]; ?></td>
                <td width="100" style="word-break:break-all;" align="left"><?= $country_arr[$row[csf('country_id')]]; ?></td>
                <td width="80" style="word-break:break-all;" align="left"><?= $color_arr[$row[csf('color_number_id')]]; ?></td>
                <td width="70" style="word-break:break-all;" align="left">&nbsp;<?= $size_arr[$row[csf('size_number_id')]]; ?></td>
                <td width="80" id="prodQty_<?= $i; ?>"align="right"><?= $qty.'=';?>&nbsp;</td>
                <?
                $str_col='';
                $onclick=' onClick="fn_deleteRow('.$i.');" ';
                if($output_bundles[$row[csf('bundle_no')]]!='')
                {
                    $str_col=' bgcolor="#FE6569" ';
                    $onclick='';
                }
                ?>
                <td id="button_1" align="center" <?= $str_col; ?>>
                    <input type="button" id="decrease_<?= $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" <? echo $onclick; ?>/>
                    <input type="hidden" name="cutNo[]" id="cutNo_<?= $i; ?>" value="<?= $row[csf('cut_no')]; ?>"/>
                    <input type="hidden" name="colorSizeId[]" id="colorSizeId_<?= $i; ?>" value="<?= $row[csf('colorsizeid')]; ?>"/>
                    <input type="hidden" name="orderId[]" id="orderId_<?= $i; ?>" value="<?= $row[csf('po_id')]; ?>"/>
                    <input type="hidden" name="gmtsitemId[]" id="gmtsitemId_<?= $i; ?>" value="<?= $row[csf('item_number_id')]; ?>"/>
                    <input type="hidden" name="countryId[]" id="countryId_<?= $i; ?>" value="<?= $row[csf('country_id')]; ?>"/>
                    <input type="hidden" name="colorId[]" id="colorId_<?= $i; ?>" value="<?= $row[csf('color_number_id')]; ?>"/>
                    <input type="hidden" name="sizeId[]" id="sizeId_<?= $i; ?>" value="<?= $row[csf('size_number_id')]; ?>"/>
                    <input type="hidden" name="qty[]" id="qty_<?= $i; ?>" value="<?= $qty; ?>"/>
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<?= $i; ?>"  value="<?= $row[csf('prdid')]; ?>"/>
                    <input type="hidden" name="isRescan[]" id="isRescan_<?= $i; ?>" value="<?= $row[csf('is_rescan')]; ?>"/>
                </td>
            </tr>
        <?
            $i--;
        }
    }
    exit();
}
if ($action == "populate_issue_form_data") {
    //production type=2 come from array
    $sqlResult = sql_select("select id,garments_nature,challan_no,po_break_down_id,item_number_id,country_id,production_source,serving_company,location,
        embel_name,embel_type,production_date,production_quantity,production_source,production_type,entry_break_down_type,production_hour,sewing_line,
        supervisor,carton_qty,remarks,floor_id,alter_qnty,reject_qnty,total_produced,yet_to_produced from pro_garments_production_mst where id='$data'
        and production_type='4' and status_active=1 and is_deleted=0 order by id");
    //echo "sdfds".$sqlResult;die;
    foreach ($sqlResult as $result) {
        //echo "$('#txt_issue_date').val('".change_date_format($result[csf('production_date')])."');\n";
        echo "$('#txt_issue_qty').val('" . $result[csf('production_quantity')] . "');\n";
        echo "$('#txt_challan').val('" . $result[csf('challan_no')] . "');\n";
        echo "$('#txt_iss_id').val('" . $result[csf('id')] . "');\n";
        echo "$('#txt_remark').val('" . $result[csf('remarks')] . "');\n";

        $dataArray = sql_select("select SUM(CASE WHEN production_type=1 THEN production_quantity END) as totalCutting,SUM(CASE WHEN production_type=4 and embel_name=" . $result[csf('embel_name')] . " THEN production_quantity ELSE 0 END) as totalPrinting from pro_garments_production_mst WHERE po_break_down_id=" . $result[csf('po_break_down_id')] . " and item_number_id=" . $result[csf('item_number_id')] . " and country_id=" . $result[csf('country_id')] . " and is_deleted=0");
        foreach ($dataArray as $row) {
            echo "$('#txt_cutting_qty').val('" . $row[csf('totalCutting')] . "');\n";
            echo "$('#txt_cumul_issue_qty').val('" . $row[csf('totalPrinting')] . "');\n";
            $yet_to_produced = $row[csf('totalCutting')] - $row[csf('totalPrinting')];
            echo "$('#txt_yet_to_issue').val('" . $yet_to_produced . "');\n";
        }

        echo "get_php_form_data(" . $result[csf('po_break_down_id')] . "+'**'+" . $result[csf("item_number_id")] . "+'**'+" . $result[csf("embel_name")] . "+'**'+" . $result[csf("country_id")] . ", 'populate_data_from_search_popup', 'requires/bundle_wise_sewing_input_controller' );\n";

        echo "$('#cbo_item_name').val('" . $result[csf('item_number_id')] . "');\n";
        echo "$('#cbo_country_name').val('" . $result[csf('country_id')] . "');\n";

        echo "show_list_view('" . $result[csf('po_break_down_id')] . "','show_country_listview','list_view_country','requires/bundle_wise_sewing_input_controller','');\n";

        echo "$('#txt_mst_id').val('" . $result[csf('id')] . "');\n";
        echo "set_button_status(1, permission, 'fnc_issue_print_embroidery_entry',1,1);\n";

        //break down of color and size------------------------------------------
        //#############################################################################################//
        // order wise - color level, color and size level
        $color_library = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
        $size_library = return_library_array("select id, size_name from lib_size", 'id', 'size_name');

        $variableSettings = $result[csf('entry_break_down_type')];
        if ($variableSettings != 1) // gross level
        {
            $po_id = $result[csf('po_break_down_id')];
            $item_id = $result[csf('item_number_id')];
            $country_id = $result[csf('country_id')];

            $sql_dtls = sql_select("select color_size_break_down_id,production_qnty,size_number_id, color_number_id
                from  pro_garments_production_dtls a,wo_po_color_size_breakdown b where a.mst_id=$data and a.status_active=1 and a.color_size_break_down_id=b.id
                and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id'");
            foreach ($sql_dtls as $row) {
                if ($variableSettings == 2) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')] . $row[csf('color_number_id')];
                $amountArr[$index] = $row[csf('production_qnty')];
            }

            //$variableSettings=2;


            if ($variableSettings == 2) // color level
            {
                if ($db_type == 0) {
                    $sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty,
                    (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END)
                    from pro_garments_production_dtls pdtls where pdtls.production_type=1 and pdtls.is_deleted=0 ) as production_qnty,
                    (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END)
                    from pro_garments_production_dtls cur where cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty
                    from wo_po_color_size_breakdown
                    where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1
                    group by color_number_id";
                } else {
                    $sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
                    sum(CASE WHEN c.production_type=1 then b.production_qnty ELSE 0 END) as production_qnty,
                    sum(CASE WHEN c.production_type=2 then b.production_qnty ELSE 0 END) as cur_production_qnty
                    from wo_po_color_size_breakdown a
                    left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
                    left join pro_garments_production_mst c on c.id=b.mst_id
                    where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.item_number_id, a.color_number_id";

                }
            } else if ($variableSettings == 3) //color and size level
            {
                $dtlsData = sql_select("select a.color_size_break_down_id,
                    sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty,
                    sum(CASE WHEN a.production_type=2 and b.embel_name='$embelName' then a.production_qnty ELSE 0 END) as cur_production_qnty
                    from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and  b.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id'
                    and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(1,2)
                    group by a.color_size_break_down_id");

                foreach ($dtlsData as $row) {
                    $color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss'] = $row[csf('production_qnty')];
                    $color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv'] = $row[csf('cur_production_qnty')];
                }
                //print_r($color_size_qnty_array);

                $sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
                from wo_po_color_size_breakdown
                where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1
                order by color_number_id";

            } else // by default color and size level
            {
                $dtlsData = sql_select("select a.color_size_break_down_id,
                    sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty,
                    sum(CASE WHEN a.production_type=2 and b.embel_name='$embelName' then a.production_qnty ELSE 0 END) as cur_production_qnty
                    from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and b.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id'
                    and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(1,2)
                    group by a.color_size_break_down_id");

                foreach ($dtlsData as $row) {
                    $color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss'] = $row[csf('production_qnty')];
                    $color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv'] = $row[csf('cur_production_qnty')];
                }
                //print_r($color_size_qnty_array);

                $sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
                from wo_po_color_size_breakdown
                where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1
                order by color_number_id";
            }

            $colorResult = sql_select($sql);
            //print_r($sql);die;
            $colorHTML = "";
            $colorID = '';
            $chkColor = array();
            $i = 0;
            $totalQnty = 0;
            $colorWiseTotal = 0;
            foreach ($colorResult as $color) {
                if ($variableSettings == 2) // color level
                {
                    $amount = $amountArr[$color[csf("color_number_id")]];
                    $colorHTML .= '<tr><td>' . $color_library[$color[csf("color_number_id")]] . '</td><td><input type="text" name="txt_color" id="colSize_' . ($i + 1) . '" style="width:80px"  class="text_boxes_numeric" placeholder="' . ($color[csf("production_qnty")] - $color[csf("cur_production_qnty")] + $amount) . '" value="' . $amount . '" onblur="fn_colorlevel_total(' . ($i + 1) . ')"></td></tr>';
                    $totalQnty += $amount;
                    $colorID .= $color[csf("color_number_id")] . ",";
                } else //color and size level
                {
                    $index = $color[csf("size_number_id")] . $color[csf("color_number_id")];
                    $amount = $amountArr[$index];
                    if (!in_array($color[csf("color_number_id")], $chkColor)) {
                        if ($i != 0) $colorHTML .= "</table></div>";
                        $i = 0;
                        $colorWiseTotal = 0;
                        $colorHTML .= '<h3 align="left" id="accordion_h' . $color[csf("color_number_id")] . '" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_' . $color[csf("color_number_id")] . '\', \'\',1)"> <span id="accordion_h' . $color[csf("color_number_id")] . 'span">+</span>' . $color_library[$color[csf("color_number_id")]] . ' : <span id="total_' . $color[csf("color_number_id")] . '"></span></h3>';
                        $colorHTML .= '<div id="content_search_panel_' . $color[csf("color_number_id")] . '" style="display:none" class="accord_close"><table id="table_' . $color[csf("color_number_id")] . '">';
                        $chkColor[] = $color[csf("color_number_id")];
                        $totalFn .= "fn_total(" . $color[csf("color_number_id")] . ");";
                    }
                    $colorID .= $color[csf("size_number_id")] . "*" . $color[csf("color_number_id")] . ",";

                    $iss_qnty = $color_size_qnty_array[$color[csf('id')]]['iss'];
                    $rcv_qnty = $color_size_qnty_array[$color[csf('id')]]['rcv'];


                    $colorHTML .= '<tr><td>' . $size_library[$color[csf("size_number_id")]] . '</td><td><input type="text" name="colorSize" id="colSize_' . $color[csf("color_number_id")] . ($i + 1) . '"  class="text_boxes_numeric" style="width:100px" placeholder="' . ($iss_qnty - $rcv_qnty + $amount) . '" onblur="fn_total(' . $color[csf("color_number_id")] . ',' . ($i + 1) . ')" value="' . $amount . '" ></td></tr>';
                    $colorWiseTotal += $amount;
                }
                $i++;
            }
            //echo $colorHTML;die;
            if ($variableSettings == 2) {
                $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th></thead><tbody>' . $colorHTML . '<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="' . $totalQnty . '" value="' . $totalQnty . '" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>';
            }
            echo "$('#breakdown_td_id').html('" . addslashes($colorHTML) . "');\n";
            if ($variableSettings == 3) echo "$totalFn;\n";
            $colorList = substr($colorID, 0, -1);
            echo "$('#hidden_colorSizeID').val('" . $colorList . "');\n";
        }//end if condtion
        //#############################################################################################//
    }
    exit();
}

//pro_garments_production_mst
if ($action == "save_update_delete") {
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));

    $prod_reso_allocation = return_field_value("auto_update", "variable_settings_production", "company_name=$cbo_emb_company and variable_list=23 and status_active=1 and is_deleted=0");
    if($prod_reso_allocation !=2 ){$prod_reso_allocation=1;}
    // echo "10**".$prod_reso_allocation;die;


    if ($operation == 0) // Insert Here----------------------------------------------------------
    {
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }
        //table lock here  entry form 160 =all production pages
      // if ( check_table_status( 160, 1 )==0 ) { echo "15**0"; die;}

        if (str_replace("'", "", $txt_system_id) == "")
        {
            // $mst_id = return_next_id("id", "pro_gmts_delivery_mst", 1);


            if ($db_type == 0) $year_cond = "YEAR(insert_date)";
            else if ($db_type == 2) $year_cond="to_char(insert_date,'YYYY')";
            else $year_cond = "";//defined Later

            $new_sys_number = explode("*", return_next_id_by_sequence("", "pro_gmts_delivery_mst",$con,1,$cbo_company_name,'SWI',0,date("Y",time()),0,0,4,0,0 ));
            $field_array_delivery = "id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, production_source, serving_company, floor_id,sewing_line, organic, delivery_date,entry_form,working_company_id,working_location_id, inserted_by, insert_date,remarks";
            $mst_id = return_next_id_by_sequence(  "pro_gmts_delivery_mst_seq", "pro_gmts_delivery_mst", $con );
            $data_array_delivery = "(" . $mst_id . ",'" . $new_sys_number[1] . "','" .(int) $new_sys_number[2] . "','" . $new_sys_number[0] . "', " . $cbo_company_name . ",4," . $cbo_location . "," . $delivery_basis . "," . $cbo_source . "," . $cbo_emb_company . "," . $cbo_floor . "," . $cbo_line_no . "," . $txt_organic . "," . $txt_issue_date . ",96,".$cbo_working_company_name.",".$cbo_working_location."," . $user_id . ",'" . $pc_date_time . "','".str_replace("'","",$txt_remarks)."')";
            $challan_no =(int) $new_sys_number[2];
            $txt_challan_no = $new_sys_number[0];
        }
        else
        {
            $mst_id = str_replace("'", "", $txt_system_id);
            $txt_chal_no = explode("-", str_replace("'", "", $txt_challan_no));
            $challan_no = (int)$txt_chal_no[3];

            $field_array_delivery = "company_id*location_id*delivery_basis*production_source*serving_company*floor_id*sewing_line*organic*delivery_date*working_company_id*working_location_id*updated_by*update_date*remarks";
            $data_array_delivery = "" . $cbo_company_name . "*" . $cbo_location . "*" . $delivery_basis . "*" . $cbo_source . "*" . $cbo_emb_company . "*" . $cbo_floor . "*" . $cbo_line_no . "*" . $txt_organic . "*" . $txt_issue_date . "*" . $user_id . "*'" . $pc_date_time . "'*'".str_replace("'","",$txt_remarks)."'";

        }
        for($j=1;$j<=$tot_row;$j++)
        {
            $bundleCheck="barcodeNo_".$j;
            $cutNo="cutNo_".$j;
            $is_rescan="isRescan_".$j;
            if($$is_rescan!=1)
            {
                $bundleCheckArr[trim($$bundleCheck)]=trim($$bundleCheck);
            }
            $bundleCheckArr2[trim($$bundleCheck)]=trim($$bundleCheck);
            $all_cut_no_arr[$$cutNo]=$$cutNo;
        }
        $cut_nums="'".implode("','", $all_cut_no_arr)."'";
        $bundle_wise_type_sql="SELECT b.bundle_no ,b.color_type_id from ppl_cut_lay_mst a,ppl_cut_lay_bundle b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.cutting_no in ($cut_nums) ";
        $bundle_wise_type_array=array();
        $bundle_wise_data=sql_select($bundle_wise_type_sql);
        foreach($bundle_wise_data as $vals)
        {
            $bundle_wise_type_array[$vals[csf("bundle_no")]]=$vals[csf("color_type_id")];
        }


        $bundle="'".implode("','",$bundleCheckArr)."'";
        $bundle2="'".implode("','",$bundleCheckArr2)."'";
        $receive_sql="SELECT c.barcode_no,c.bundle_no from pro_garments_production_dtls c where  c.barcode_no  in ($bundle)  and c.production_type=4 and c.status_active=1 and c.is_deleted=0 and (c.is_rescan=0 or c.is_rescan is null)"; ;
        $receive_result = sql_select($receive_sql);
        foreach ($receive_result as $row)
        {
            $duplicate_bundle[trim($row[csf('bundle_no')])]=trim($row[csf('bundle_no')]);
        }

        // ================== getting cutting and input qty =====================
        $sql="SELECT c.production_type,c.production_qnty,c.bundle_no from pro_garments_production_dtls c where  c.barcode_no  in ($bundle2)  and c.production_type in(1,4) and c.status_active=1 and c.is_deleted=0";
        // echo "10**$sql";die;
        $result = sql_select($sql);
        $prev_qty_arr = array();
        foreach ($result as $row)
        {
            $prev_qty_arr[trim($row[csf('production_type')])][trim($row[csf('bundle_no')])]+=trim($row[csf('production_qnty')]);
        }

        if (str_replace("'", "", $delivery_basis) == 3)
        {
            //$id = return_next_id("id", "pro_garments_production_mst", 1);

            $field_array_mst = "id, delivery_mst_id,cut_no, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, production_date, production_quantity, production_type, entry_break_down_type, remarks, floor_id,sewing_line,shift_name,prod_reso_allo,entry_form,cost_of_fab_per_pcs,cut_oh_per_pcs,cost_per_pcs, inserted_by, insert_date";
            //echo "10**";
            $mstArr = array();
            $dtlsArr = array();
            $colorSizeArr = array();
            $mstIdArr = array();
            $colorSizeIdArr = array();
            $poIdArr = array();
            $itemIdArr = array();
            $colorIdArr = array();
            $bundleNoArr = array();
            for ($j = 1; $j <= $tot_row; $j++)
            {
                $cutNo="cutNo_".$j;
                $bundleNo = "bundleNo_" . $j;
                $barcodeNo="barcodeNo_".$j;
                $orderId = "orderId_" . $j;
                $gmtsitemId = "gmtsitemId_" . $j;
                $countryId = "countryId_" . $j;
                $colorId = "colorId_" . $j;
                $sizeId = "sizeId_" . $j;
                $colorSizeId = "colorSizeId_" . $j;
                $qty = "qty_" . $j;
                $checkRescan="isRescan_".$j;
                if($duplicate_bundle[trim($$bundleNo)]=='')
                {
                    $bundleCutArr[$$bundleNo]=$$cutNo;
                    $cutArr[$$orderId][$$gmtsitemId][$$countryId]=$$cutNo;
                    $mstArr[$$orderId][$$gmtsitemId][$$countryId] += $$qty;
                    $colorSizeArr[$$bundleNo] = $$orderId . "**" . $$gmtsitemId . "**" . $$countryId . "**" . $$colorId;
                    $dtlsArr[$$bundleNo] += $$qty;
                    $dtlsArrColorSize[$$bundleNo] = $$colorSizeId;
                    $bundleRescanArr[$$bundleNo]=$$checkRescan;
                    $bundleBarcodeArr[$$bundleNo]=$$barcodeNo;
                }
                $poIdArr[$$orderId] = $$orderId;
                $itemIdArr[$$gmtsitemId] = $$gmtsitemId;
                $colorIdArr[$$colorId] = $$colorId;
                $bundleNoArr[$$barcodeNo] = $$barcodeNo;

            }

            /* ================================= calculate fabric cost =================================== */
            $poIds = implode(",",array_filter($poIdArr));
            $itemIds = implode(",",array_filter($itemIdArr));
            $colorIds = implode(",",array_filter($colorIdArr));
            $sew_bundle = "'".implode("','",array_filter($bundleNoArr))."'";

            // $sql = "SELECT c.po_break_down_id as po_id,c.item_number_id,c.color_number_id,b.production_type,a.embel_name,b.cost_of_fab_per_pcs,b.cut_oh_per_pcs,b.cost_per_pcs from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and b.production_type in(1,3)  and c.po_break_down_id in($poIds) and c.item_number_id in($itemIds) and c.color_number_id in($colorIds) order by a.production_type,a.embel_name asc";

            $sql = "SELECT c.po_break_down_id as po_id,c.item_number_id,c.color_number_id,b.production_type,a.embel_name,b.cost_of_fab_per_pcs,b.cut_oh_per_pcs,b.cost_per_pcs,b.bundle_no,b.production_qnty,(b.cost_per_pcs*b.production_qnty) as amount from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and b.production_type in(1,3)  and c.po_break_down_id in($poIds) and c.item_number_id in($itemIds) and c.color_number_id in($colorIds) and b.barcode_no in($sew_bundle) order by a.production_type,a.embel_name asc";// and b.cut_no in($cutNos)
            // echo "10**".$sql;die;
            $res = sql_select($sql);
            $fab_cost_array = array();
            $x=0;
            $y=0;
            foreach ($res as $v)
            {
                /* if($v['PRODUCTION_TYPE']==1)
                {
					$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['prod_qty'] += $v['PRODUCTION_QNTY'];
					$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['amount'] += $v['AMOUNT'];
                }

                if($v['PRODUCTION_TYPE']==3 && $v['EMBEL_NAME']==1)
                {
                    if($x==0)
                    {
                        $fab_cost_array = array();
                        $x++;
                    }
                    $fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['prod_qty'] += $v['PRODUCTION_QNTY'];
					$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['amount'] += $v['AMOUNT'];
                }

                if($v['PRODUCTION_TYPE']==3 && $v['EMBEL_NAME']==2)
                {
                    if($y==0)
                    {
                        $fab_cost_array = array();
                        $y++;
                    }
                    $fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['prod_qty'] += $v['PRODUCTION_QNTY'];
					$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['amount'] += $v['AMOUNT'];
                } */

                $fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']][$v['PRODUCTION_TYPE']][$v['EMBEL_NAME']]['cost_of_fab_per_pcs'] = $v['COST_OF_FAB_PER_PCS'];
                // $fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COUNTRY_ID']]['cut_oh_per_pcs'] = $v['CUT_OH_PER_PCS'];
                $fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']][$v['PRODUCTION_TYPE']][$v['EMBEL_NAME']]['cost_per_pcs'] = $v['COST_PER_PCS'];
            }
            /* ================================== end fabric cost ========================================= */

            foreach ($mstArr as $orderId => $orderData) {
                foreach ($orderData as $gmtsItemId => $gmtsItemIdData) {
                    foreach ($gmtsItemIdData as $countryId => $qty) {

                        // $cost_per_pcs = $fab_cost_array[$orderId][$gmtsItemId][$countryId][1][0]['cost_of_fab_per_pcs']+$fab_cost_array[$orderId][$gmtsItemId][$countryId][3][1]['cost_of_fab_per_pcs']+$fab_cost_array[$orderId][$gmtsItemId][$countryId][3][2]['cost_of_fab_per_pcs'];
                        $id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );
                        if ($data_array_mst != "") $data_array_mst .= ",";
                        $data_array_mst .= "(" . $id . "," . $mst_id . ",'" . $cutArr[$orderId][$gmtsItemId][$countryId] . "'," . $cbo_company_name . "," . $garments_nature . ",'" . $challan_no . "'," . $orderId . ", " . $gmtsItemId . "," . $countryId . ", " . $cbo_source . "," . $cbo_emb_company . "," . $cbo_location . "," . $txt_issue_date . "," . $qty . ",4," . $sewing_production_variable . ",'" . $txt_remark . "'," . $cbo_floor . "," . $cbo_line_no . "," . $cbo_shift_name . "," . $prod_reso_allocation . ",96,'" . $cost_per_pcs . "','" . $cost_per_pcs . "','" . $cost_per_pcs . "'," . $user_id . ",'" . $pc_date_time . "')";
                        $mstIdArr[$orderId][$gmtsItemId][$countryId] = $id;

                    }
                }
            }

          //  $dtls_id = return_next_id("id", "pro_garments_production_dtls", 1);
            $field_array_dtls ="id,delivery_mst_id,mst_id,production_type,color_size_break_down_id, production_qnty, cut_no, bundle_no,entry_form,barcode_no,is_rescan,color_type_id,cost_of_fab_per_pcs,cut_oh_per_pcs,cost_per_pcs";

            foreach ($dtlsArr as $bundle_no => $qty)
            {
                if($prev_qty_arr[1][$bundle_no] >= $qty+$prev_qty_arr[4][$bundle_no]) // here check input qty not over cutting qty
				{

                    $colorSizedData = explode("**", $colorSizeArr[$bundle_no]);
                    $gmtsMstId = $mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
                    //$cut_no=$cutArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
                    $cut_no=$bundleCutArr[$bundle_no];

                    $cost_per_pcs = $fab_cost_array[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[3]][1][0]['cost_per_pcs']+$fab_cost_array[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[3]][3][1]['cost_of_fab_per_pcs']+$fab_cost_array[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[3]][3][2]['cost_of_fab_per_pcs'];

                    $dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );

                    if ($data_array_dtls != "") $data_array_dtls .= ",";
                    $data_array_dtls .= "(" . $dtls_id . "," . $mst_id . "," . $gmtsMstId . ",4,'" . $dtlsArrColorSize[$bundle_no] . "','" . $qty . "','" . $cut_no . "','" . $bundle_no . "',96,'".$bundleBarcodeArr[$bundle_no]."','".$bundleRescanArr[$bundle_no]."','".$bundle_wise_type_array[$bundle_no]."','" . $cost_per_pcs . "','" . $cost_per_pcs . "','" . $cost_per_pcs . "')";
                    //$colorSizeIdArr[$colorSizeId]=$dtls_id;
                }
				else
				{
					echo "20**Sewing input qty is over than cutting qty.Bundle no=$bundle_no".$prev_qty_arr[1][$bundle_no] .">=". $qty."=".$prev_qty_arr[4][$bundle_no];die();
				}

            }


            if (str_replace("'", "", $txt_system_id) == "") {
                $challanrID = sql_insert("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, 1);
            } else {
                $challanrID = sql_update("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, "id", $txt_system_id, 1);
            }
            $rID = sql_insert("pro_garments_production_mst", $field_array_mst, $data_array_mst, 1);
            $dtlsrID = sql_insert("pro_garments_production_dtls", $field_array_dtls, $data_array_dtls, 1);

            // echo "10**insert into pro_garments_production_mst (".$field_array_mst.") values ".$data_array_mst;die;

            // echo "10**".$data_array_dtls;die;
           // echo $challanrID ."&&b". $rID ."&&a". $dtlsrID ;die;
            //release lock table

            // echo "10**".$challanrID.$rID.$dtlsrID;die;
            if ($db_type == 0) {
                if ($challanrID && $rID && $dtlsrID) {
                    mysql_query("COMMIT");
                    echo "0**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no);
                } else {
                    mysql_query("ROLLBACK");
                    echo "10**";
                }
            } else if ($db_type == 1 || $db_type == 2) {
                if ($challanrID && $rID && $dtlsrID) {
                    oci_commit($con);
                    echo "0**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no);
                } else {
                    oci_rollback($con);
                    echo "10**";
                }
            }
        }
        else
        {
            //$id = return_next_id("id", "pro_garments_production_mst", 1);
             $id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );

            $field_array1 = "id, delivery_mst_id, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, production_date, production_quantity, production_type, entry_break_down_type, remarks, floor_id,sewing_line,prod_reso_allo,entry_form, inserted_by, insert_date";

            $data_array1 = "(" . $id . "," . $mst_id . "," . $cbo_company_name . "," . $garments_nature . ",'" . $challan_no . "'," . $hidden_po_break_down_id . ", " . $cbo_item_name . "," . $cbo_country_name . ", " . $cbo_source . "," . $cbo_emb_company . "," . $cbo_location . "," . $txt_issue_date . "," . $txt_issue_qty . ",4," . $sewing_production_variable . "," . $txt_remark . "," . $cbo_floor . "," . $cbo_line_no . "," . $prod_reso_allocation . ",96," . $user_id . ",'" . $pc_date_time . "')";


            //echo "INSERT INTO pro_garments_production_mst (".$field_array1.") VALUES ".$data_array1;die;
            // pro_garments_production_dtls table entry here ----------------------------------///
            $field_array = "id,delivery_mst_id,mst_id,production_type,color_size_break_down_id,production_qnty,entry_form";
            $dtlsrID = true;
            if (str_replace("'", "", $sewing_production_variable) == 2)//color level wise
            {
                $color_sizeID_arr = sql_select("select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and color_mst_id!=0 order by id");
                $colSizeID_arr = array();
                foreach ($color_sizeID_arr as $val) {
                    $index = $val[csf("color_number_id")];
                    $colSizeID_arr[$index] = $val[csf("id")];
                }
                // $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
                $rowEx = explode("**", $colorIDvalue);
               // $dtls_id = return_next_id("id", "pro_garments_production_dtls", 1);
                $data_array = "";
                $j = 0;
                foreach ($rowEx as $rowE => $val) {
                    $colorSizeNumberIDArr = explode("*", $val);
                    //2 for Issue to Print / Emb Entry
                     $dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
                    if ($j == 0) $data_array = "(" . $dtls_id . "," . $mst_id . "," . $id . ",4,'" . $colSizeID_arr[$colorSizeNumberIDArr[0]] . "','" . $colorSizeNumberIDArr[1] . "',96)";
                    else $data_array .= ",(" . $dtls_id . "," . $mst_id . "," . $id . ",4,'" . $colSizeID_arr[$colorSizeNumberIDArr[0]] . "','" . $colorSizeNumberIDArr[1] . "',96)";

                    $j++;
                }
            }

            if (str_replace("'", "", $sewing_production_variable) == 3)//color and size wise
            {
                $color_sizeID_arr = sql_select("select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name order by size_number_id,color_number_id");
                $colSizeID_arr = array();
                foreach ($color_sizeID_arr as $val) {
                    $index = $val[csf("size_number_id")] . $val[csf("color_number_id")];
                    $colSizeID_arr[$index] = $val[csf('id')];
                }

                //  colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value --------------------------//
                $rowEx = explode("***", $colorIDvalue);
                //$dtls_id = return_next_id("id", "pro_garments_production_dtls", 1);
                $data_array = "";
                $j = 0;
                foreach ($rowEx as $rowE => $valE) {
                    $colorAndSizeAndValue_arr = explode("*", $valE);
                    $sizeID = $colorAndSizeAndValue_arr[0];
                    $colorID = $colorAndSizeAndValue_arr[1];
                    $colorSizeValue = $colorAndSizeAndValue_arr[2];
                    $index = $sizeID . $colorID;

                    //2 for Issue to Print / Emb Entry
                     $dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

                    if ($j == 0) $data_array = "(" . $dtls_id . "," . $mst_id . "," . $id . ",4,'" . $colSizeID_arr[$index] . "','" . $colorSizeValue . "')";
                    else $data_array .= ",(" . $dtls_id . "," . $mst_id . "," . $id . ",4,'" . $colSizeID_arr[$index] . "','" . $colorSizeValue . "')";

                    $j++;
                }
            }
            //echo "10** insert into pro_garments_production_mst($field_array1)values".$data_array1;die;
            $rID = sql_insert("pro_garments_production_mst", $field_array1, $data_array1, 1);
            if (str_replace("'", "", $txt_system_id) == "") {
                $challanrID = sql_insert("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, 1);
            } else {
                $challanrID = sql_update("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, "id", $txt_system_id, 1);
            }

            $dtlsrID = true;
            if (str_replace("'", "", $sewing_production_variable) == 2 || str_replace("'", "", $sewing_production_variable) == 3) {
                $dtlsrID = sql_insert("pro_garments_production_dtls", $field_array, $data_array, 1);
            }



            //echo "10**".$rID."**".$challanrID."**".$dtlsrID."**".$challanrID;die;
            if ($db_type == 0) {
                if ($rID && $challanrID && $dtlsrID) {
                    mysql_query("COMMIT");
                    echo "0**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no);
                } else {
                    mysql_query("ROLLBACK");
                    echo "10**";
                }
            } else if ($db_type == 1 || $db_type == 2) {
                if ($rID && $challanrID && $dtlsrID) {
                    oci_commit($con);
                    echo "0**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no);
                } else {
                    oci_rollback($con);
                    echo "10**";
                }
            }
        }
        //check_table_status(160, 0);
        disconnect($con);
        die;

    }
    else if ($operation == 1) // Update Here End------------------------------------------------------
    {
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }
        //table lock here
     //   if  ( check_table_status( 160, 1 )==0 ) { echo "15**1"; die;}
        $mst_id = str_replace("'", "", $txt_system_id);
        $txt_chal_no = explode("-", str_replace("'", "", $txt_challan_no));
        $challan_no = (int)$txt_chal_no[3];

        $field_array_delivery = "company_id*location_id*production_source*serving_company*floor_id*sewing_line*organic*delivery_date*working_company_id*working_location_id*updated_by*update_date*remarks";
        $data_array_delivery = "" . $cbo_company_name . "*" . $cbo_location . "*" . $cbo_source . "*" . $cbo_emb_company . "*" . $cbo_floor . "*" . $cbo_line_no . "*" . $txt_organic . "*" . $txt_issue_date . "*".$cbo_working_company_name."*".$cbo_working_location."*" . $user_id . "*'" . $pc_date_time . "'*'".str_replace("'","",$txt_remarks)."'";

        if (str_replace("'", "", $delivery_basis) == 3)
        {

            //$id = return_next_id("id", "pro_garments_production_mst", 1);
           // $dtls_id = return_next_id("id", "pro_garments_production_dtls", 1);
            $non_delete_arr=production_validation($mst_id,5);
            $issue_data_arr=production_data($mst_id,4);

            for($j=1;$j<=$tot_row;$j++)
            {
                $bundleCheck="barcodeNo_".$j;
                $is_rescanww="isRescan_".$j;
                if($$is_rescanww!=1)
                {
                    $bundleCheckArr[trim($$bundleCheck)]=trim($$bundleCheck);
                }
                $cutNo="cutNo_".$j;
                $all_cut_no_arr[$$cutNo]=$$cutNo;
            }
            $cut_nums="'".implode("','", $all_cut_no_arr)."'";
            $bundle_wise_type_sql="SELECT b.bundle_no ,b.color_type_id from ppl_cut_lay_mst a,ppl_cut_lay_bundle b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.cutting_no in ($cut_nums) ";
            $bundle_wise_type_array=array();
            $bundle_wise_data=sql_select($bundle_wise_type_sql);
            foreach($bundle_wise_data as $vals)
            {
                $bundle_wise_type_array[$vals[csf("bundle_no")]]=$vals[csf("color_type_id")];
            }

            $bundle="'".implode("','",$bundleCheckArr)."'";
            $receive_sql="select c.barcode_no,c.bundle_no from pro_garments_production_dtls c where  c.barcode_no  in ($bundle)  and c.production_type=4 and c.status_active=1 and c.is_deleted=0 and (c.is_rescan=0 or c.is_rescan is null) and delivery_mst_id<>$mst_id";
            // echo "10**".$receive_sql;die;
            $receive_result = sql_select($receive_sql);
            foreach ($receive_result as $row)
            {
                $duplicate_bundle[trim($row[csf('bundle_no')])]=trim($row[csf('bundle_no')]);
            }




            $delete = execute_query("UPDATE pro_garments_production_mst SET updated_by=$user_id,update_date='$pc_date_time',is_deleted=1, status_active=0  WHERE delivery_mst_id=$mst_id and production_type=4", 0);
            $delete_dtls = execute_query("UPDATE pro_garments_production_dtls SET is_deleted=1, status_active=0 WHERE delivery_mst_id=$mst_id and production_type=4", 0);



            $field_array_mst = "id, delivery_mst_id,cut_no, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, production_date, production_quantity, production_type, entry_break_down_type, remarks, floor_id,sewing_line,shift_name,prod_reso_allo,entry_form,cost_of_fab_per_pcs,cut_oh_per_pcs,cost_per_pcs, inserted_by, insert_date,updated_by,update_date";
            //echo "0**";
            $mstArr = array();
            $dtlsArr = array();
            $colorSizeArr = array();
            $mstIdArr = array();
            $colorSizeIdArr = array();
            $poIdArr = array();
            $itemIdArr = array();
            $colorIdArr = array();
            $bundleNoArr = array();
            $coloSizeIDArr = array();
            for ($j = 1; $j <= $tot_row; $j++) {
                $cutNo="cutNo_".$j;
                $bundleNo = "bundleNo_" . $j;
                $barcodeNo="barcodeNo_".$j;
                $orderId = "orderId_" . $j;
                $gmtsitemId = "gmtsitemId_" . $j;
                $countryId = "countryId_" . $j;
                $colorId = "colorId_" . $j;
                $sizeId = "sizeId_" . $j;
                $colorSizeId = "colorSizeId_" . $j;
                $qty = "qty_" . $j;
                $checkRescan="isRescan_".$j;

                if($non_delete_arr[$$bundleNo]=="" && $duplicate_bundle[trim($$bundleNo)]==''){
                    $bundleCutArr[$$bundleNo]=$$cutNo;
                    $cutArr[$$orderId][$$gmtsitemId][$$countryId]=$$cutNo;
                    $mstArr[$$orderId][$$gmtsitemId][$$countryId] += $$qty;
                    $colorSizeArr[$$bundleNo] = $$orderId . "**" . $$gmtsitemId . "**" . $$countryId . "**" . $$colorId;
                    $dtlsArr[$$bundleNo] += $$qty;
                    $dtlsArrColorSize[$$bundleNo] = $$colorSizeId;
                    $bundleRescanArr[$$bundleNo]=$$checkRescan;
                    $bundleBarcodeArr[$$bundleNo]=$$barcodeNo;
                }
                $poIdArr[$$orderId] = $$orderId;
                $itemIdArr[$$gmtsitemId] = $$gmtsitemId;
                $colorIdArr[$$colorId] = $$colorId;
                $bundleNoArr[$$barcodeNo] = $$barcodeNo;
                $coloSizeIDArr[$$colorSizeId] = $$colorId;
            }

            /* ================================= calculate fabric cost =================================== */
            $poIds = implode(",",array_filter($poIdArr));
            $itemIds = implode(",",array_filter($itemIdArr));
            $colorIds = implode(",",array_filter($colorIdArr));
            $sew_bundle = "'".implode("','",array_filter($bundleNoArr))."'";

            $sql = "SELECT c.po_break_down_id as po_id,c.item_number_id,c.color_number_id,b.production_type,a.embel_name,b.cost_of_fab_per_pcs,b.cut_oh_per_pcs,b.cost_per_pcs,b.bundle_no,b.production_qnty,(b.cost_per_pcs*b.production_qnty) as amount from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and b.production_type in(1,3)  and c.po_break_down_id in($poIds) and c.item_number_id in($itemIds) and c.color_number_id in($colorIds) and b.barcode_no in($sew_bundle) order by a.production_type,a.embel_name asc";
            // echo "10**".$sql;die;
            $res = sql_select($sql);
            $fab_cost_array = array();
            foreach ($res as $v)
            {
                $fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']][$v['PRODUCTION_TYPE']][$v['EMBEL_NAME']]['cost_of_fab_per_pcs'] = $v['COST_OF_FAB_PER_PCS'];
                // $fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COUNTRY_ID']]['cut_oh_per_pcs'] = $v['CUT_OH_PER_PCS'];
                $fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']][$v['PRODUCTION_TYPE']][$v['EMBEL_NAME']]['cost_per_pcs'] = $v['COST_PER_PCS'];
            }
            // echo "10**<pre>";print_r($fab_cost_array);die;
            /* ================================== end fabric cost ========================================= */

            // Not Delete Data...............................start;


            foreach($non_delete_arr as $bi)
            {
            	if($duplicate_bundle[trim($bi)]=='')
            	{

	                $bundleCutArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('cut_no')];
	                $bundleCutArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('cut_no')];
	                $cutArr[$issue_data_arr[trim($bi)][csf('po_break_down_id')]][$issue_data_arr[trim($bi)][csf('item_number_id')]][$issue_data_arr[trim($bi)][csf('country_id')]]=$issue_data_arr[trim($bi)][csf('cut_no')];
	                $mstArr[$issue_data_arr[trim($bi)][csf('po_break_down_id')]][$issue_data_arr[trim($bi)][csf('item_number_id')]][$issue_data_arr[trim($bi)][csf('country_id')]]+=$issue_data_arr[trim($bi)][csf('production_qnty')];
	                $colorSizeArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('po_break_down_id')]."**".$issue_data_arr[trim($bi)][csf('item_number_id')]."**".$issue_data_arr[trim($bi)][csf('country_id')]."**".$coloSizeIDArr[$issue_data_arr[trim($bi)][csf('color_size_break_down_id')]];

	                $dtlsArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]+=$issue_data_arr[trim($bi)][csf('production_qnty')];
	                //$issue_data_arr[trim($bi)][csf('bundle_no')]
	                $dtlsArrColorSize[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('color_size_break_down_id')];
	                $bundleBarcodeArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('barcode_no')];
	                $bundleBarcodeArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('barcode_no')];
	                $bundleRescanArr[$issue_data_arr[trim($bi)][csf('bundle_no')]]=$issue_data_arr[trim($bi)][csf('is_rescan')];
	            }


            }
            // Not Delete Data...............................end;
            //echo "10**";
            //print_r($bundleBarcodeArr);
            // die;
            foreach ($mstArr as $orderId => $orderData)
             {
                if($orderId)
                {
                    foreach ($orderData as $gmtsItemId => $gmtsItemIdData)
                     {
                        foreach ($gmtsItemIdData as $countryId => $qty)
                        {
                            $id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );
                            // $cost_per_pcs = $fab_cost_array[$orderId][$gmtsItemId][$countryId][1][0]['cost_of_fab_per_pcs']+$fab_cost_array[$orderId][$gmtsItemId][$countryId][3][1]['cost_of_fab_per_pcs']+$fab_cost_array[$orderId][$gmtsItemId][$countryId][3][2]['cost_of_fab_per_pcs'];
                            if ($data_array_mst != "") $data_array_mst .= ",";
                            $data_array_mst .= "(" . $id . "," . $mst_id . ",'" . $cutArr[$orderId][$gmtsItemId][$countryId] . "'," . $cbo_company_name . "," . $garments_nature . ",'" . $challan_no . "'," . $orderId . ", " . $gmtsItemId . "," . $countryId . ", " . $cbo_source . "," . $cbo_emb_company . "," . $cbo_location . "," . $txt_issue_date . "," . $qty . ",4," . $sewing_production_variable . ",'" . $txt_remark . "'," . $cbo_floor . "," . $cbo_line_no . "," . $cbo_shift_name . "," . $prod_reso_allocation . ",96,'" . $cost_per_pcs . "','" . $cost_per_pcs . "','" . $cost_per_pcs . "'," . $user_id . ",'" . $pc_date_time . "'," . $user_id . ",'" . $pc_date_time . "')";
                            $mstIdArr[$orderId][$gmtsItemId][$countryId] = $id;

                        }
                    }
                }
            }

            $field_array_dtls = "id,delivery_mst_id,mst_id,production_type,color_size_break_down_id,production_qnty,cut_no,bundle_no, entry_form,barcode_no,is_rescan,color_type_id,cost_of_fab_per_pcs,cut_oh_per_pcs,cost_per_pcs";
            // echo "10**SS"; print_r($dtlsArr);die;
            foreach ($dtlsArr as $bundle_no => $qty)
             {
                if($bundle_no)
                {
                    $colorSizedData = explode("**", $colorSizeArr[$bundle_no]);
                    $gmtsMstId = $mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
                    //$cut_no=$cutArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
                    $cost_per_pcs = $fab_cost_array[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[3]][1][0]['cost_per_pcs']+$fab_cost_array[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[3]][3][1]['cost_of_fab_per_pcs']+$fab_cost_array[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[3]][3][2]['cost_of_fab_per_pcs'];

                     $dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
                    $cut_no=$bundleCutArr[$bundle_no];
                    if ($data_array_dtls != "") $data_array_dtls .= ",";
                    $data_array_dtls .= "(" . $dtls_id . "," . $mst_id . "," . $gmtsMstId . ",4,'" . $dtlsArrColorSize[$bundle_no] . "','" . $qty . "','" . $cut_no . "','" . $bundle_no . "',96,'".$bundleBarcodeArr[$bundle_no]."','".$bundleRescanArr[$bundle_no]."','".$bundle_wise_type_array[$bundle_no]."','" . $cost_per_pcs . "','" . $cost_per_pcs . "','" . $cost_per_pcs . "')";
                    //$colorSizeIdArr[$colorSizeId]=$dtls_id;
                }

            }

            $challanrID = sql_update("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, "id", $txt_system_id, 1);
            $rID = sql_insert("pro_garments_production_mst", $field_array_mst, $data_array_mst, 1);
            $dtlsrID = sql_insert("pro_garments_production_dtls", $field_array_dtls, $data_array_dtls, 1);

            // echo "10**insert into pro_garments_production_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
            //echo $challanrID ."&&". $rID ."&&". $dtlsrID ."&&". $bundlerID ."&&". $delete ."&&". $delete_dtls ."&&". $delete_bundle;die;
            //release lock table
             if ($db_type == 0) {
                if ($challanrID && $rID && $dtlsrID && $delete && $delete_dtls) {
                    mysql_query("COMMIT");
                    echo "1**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no)."**".implode(',',$non_delete_arr);
                } else {
                    mysql_query("ROLLBACK");
                    echo "10**";
                }
            } else if ($db_type == 1 || $db_type == 2) {
                if ($challanrID && $rID && $dtlsrID && $delete && $delete_dtls) {
                    oci_commit($con);
                    echo "1**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no)."**".implode(',',$non_delete_arr);
                } else {
                    oci_rollback($con);
                    echo "10**";
                }
            }
        }
        else
        {
            // pro_garments_production_mst table data entry here
            $field_array1 = "production_source*serving_company*location*production_date*production_quantity*production_type*entry_break_down_type*challan_no*remarks*floor_id*sewing_line*total_produced*yet_to_produced*prod_reso_allo*updated_by*update_date";

            $data_array1 = "" . $cbo_source . "*" . $cbo_emb_company . "*" . $cbo_location . "*" . $txt_issue_date . "*" . $txt_issue_qty . "*4*" . $sewing_production_variable . "*'" . $challan_no . "'*" . $txt_remark . "*" . $cbo_floor . "*" . $cbo_line_no . "*" . $txt_cumul_issue_qty . "*" . $txt_yet_to_issue . "*" . $prod_reso_allocation . "*" . $user_id . "*'" . $pc_date_time . "'";
            // pro_garments_production_dtls table data entry here

            if (str_replace("'", "", $sewing_production_variable) != 1 && str_replace("'", "", $txt_mst_id) != '')// check is not gross level
            {
                //$dtlsrDelete = execute_query("delete from pro_garments_production_dtls where mst_id=$txt_mst_id",1);
                $dtlsrDelete = execute_query("UPDATE pro_garments_production_dtls SET is_deleted=1, status_active=0  where mst_id=$txt_mst_id", 1);
                $field_array = "id, mst_id, production_type, color_size_break_down_id, production_qnty";

                if (str_replace("'", "", $sewing_production_variable) == 2)//color level wise
                {
                    $color_sizeID_arr = sql_select("select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and color_mst_id!=0 order by id");
                    $colSizeID_arr = array();
                    foreach ($color_sizeID_arr as $val) {
                        $index = $val[csf("color_number_id")];
                        $colSizeID_arr[$index] = $val[csf("id")];
                    }

                    // $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
                    $rowEx = explode("**", $colorIDvalue);
                   // $dtls_id = return_next_id("id", "pro_garments_production_dtls", 1);
                    $data_array = "";
                    $j = 0;
                    foreach ($rowEx as $rowE => $val) {
                        $colorSizeNumberIDArr = explode("*", $val);
                        //2 for Issue to Print / Emb Entry
                         $dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
                        if ($j == 0) $data_array = "(" . $dtls_id . "," . $txt_mst_id . ",4,'" . $colSizeID_arr[$colorSizeNumberIDArr[0]] . "','" . $colorSizeNumberIDArr[1] . "')";
                        else $data_array .= ",(" . $dtls_id . "," . $txt_mst_id . ",4,'" . $colSizeID_arr[$colorSizeNumberIDArr[0]] . "','" . $colorSizeNumberIDArr[1] . "')";

                        $j++;
                    }
                }

                if (str_replace("'", "", $sewing_production_variable) == 3)//color and size wise
                {
                    $color_sizeID_arr = sql_select("select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name order by size_number_id,color_number_id");
                    $colSizeID_arr = array();
                    foreach ($color_sizeID_arr as $val) {
                        $index = $val[csf("size_number_id")] . $val[csf("color_number_id")];
                        $colSizeID_arr[$index] = $val[csf("id")];
                    }

                    //  colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value --------------------------//
                    $rowEx = explode("***", $colorIDvalue);
                   // $dtls_id = return_next_id("id", "pro_garments_production_dtls", 1);
                    $data_array = "";
                    $j = 0;
                    foreach ($rowEx as $rowE => $valE) {
                        $colorAndSizeAndValue_arr = explode("*", $valE);
                        $sizeID = $colorAndSizeAndValue_arr[0];
                        $colorID = $colorAndSizeAndValue_arr[1];
                        $colorSizeValue = $colorAndSizeAndValue_arr[2];
                        $index = $sizeID . $colorID;
                        //2 for Issue to Print / Emb Entry
                         $dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
                        if ($j == 0) $data_array = "(" . $dtls_id . "," . $txt_mst_id . ",4,'" . $colSizeID_arr[$index] . "','" . $colorSizeValue . "')";
                        else $data_array .= ",(" . $dtls_id . "," . $txt_mst_id . ",4,'" . $colSizeID_arr[$index] . "','" . $colorSizeValue . "')";

                        $j++;
                    }
                }

                //$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
            }//end cond


            $challanrID = sql_update("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, "id", $txt_system_id, 1);
            $rID = sql_update("pro_garments_production_mst", $field_array1, $data_array1, "id", "" . $txt_mst_id . "", 1);//echo $rID;die;
            $dtlsrID = true;
            if (str_replace("'", "", $sewing_production_variable) != 1 && str_replace("'", "", $txt_mst_id) != '')// check is not gross level
            {
                $dtlsrID = sql_insert("pro_garments_production_dtls", $field_array, $data_array, 1);
            }

            //release lock table
          //  check_table_status($_SESSION['menu_id'], 0);

            //echo "10**-".$field_array;die;


            //echo '10**'.$rID .'&&'. $challanrID .'&&'. $dtlsrID .'&&'. $dtlsrDelete;die;

            if ($db_type == 0) {
                if ($rID && $challanrID && $dtlsrID && $dtlsrDelete) {
                    mysql_query("COMMIT");
                    echo "1**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no);
                } else {
                    mysql_query("ROLLBACK");
                    echo "10**";
                }
            } else if ($db_type == 2 || $db_type == 1) {
                if ($rID && $challanrID && $dtlsrID && $dtlsrDelete) {
                    oci_commit($con);
                    echo "1**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no);
                } else {
                    oci_rollback($con);
                    echo "10**";
                }
            }
        }
        //check_table_status(160, 0);
        disconnect($con);
        die;


    }
    else if ($operation == 2) // Delete Here  ------------------------------------------------------
    {
        $con = connect();
        if ($db_type == 0)
         {
            mysql_query("BEGIN");
         }
        for($j=1;$j<=$tot_row;$j++)
        {
            $bundleCheck="barcodeNo_".$j;
            $bundleCheckArr[$$bundleCheck]=$$bundleCheck;
        }
        $bundle="'".implode("','",$bundleCheckArr)."'";
        $mst_id = str_replace("'", "", $txt_system_id);
        $txt_chal_no = explode("-", str_replace("'", "", $txt_challan_no));
        $challan_no = (int)$txt_chal_no[3];
        $is_output=sql_select("select bundle_no from pro_garments_production_dtls where status_active=1 and is_deleted=0 and production_type=5 and barcode_no in($bundle) order by bundle_no");
        foreach ($is_output as $key => $value)
         {
            $output_bundle[$key]=$value[csf("bundle_no")];
         }
        $all_output_bundle= "'".implode("','",$output_bundle)."'";
        if(count($is_output)<=0)
        {
             $delete_deliver_mst = execute_query("UPDATE pro_gmts_delivery_mst SET is_deleted=1, status_active=0 WHERE id=$mst_id", 0);
            $delete_mst = execute_query("UPDATE pro_garments_production_mst SET updated_by=$user_id,update_date='$pc_date_time',is_deleted=1, status_active=0  WHERE delivery_mst_id=$mst_id and production_type=4", 0);
            $delete_dtls = execute_query("UPDATE pro_garments_production_dtls SET is_deleted=1, status_active=0 WHERE delivery_mst_id=$mst_id and production_type=4", 0);

            if ($db_type == 0)
             {
                if ($delete_mst && $delete_dtls && $delete_deliver_mst)
                 {
                    mysql_query("COMMIT");
                    echo "2**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no);
                 }
                else
                {
                    mysql_query("ROLLBACK");
                    echo "10**";
                }
            }

            else if ($db_type == 1 || $db_type == 2)
            {
                if ($delete_mst && $delete_dtls && $delete_deliver_mst)
                 {
                    oci_commit($con);
                    echo "2**" . $mst_id . "**" . str_replace("'", "", $txt_challan_no);
                 }
                else
                 {
                    oci_rollback($con);
                    echo "10**";
                 }
            }

        }
        else
        {
            echo "141**$all_output_bundle" ;
        }

        disconnect($con);
        die;

    }
}

if ($action == "challan_no_popup") {
   echo load_html_head_contents("Challan Info", "../../", 1, 1, '', '', '');
    extract($_REQUEST);
    ?>

    <script>

        function js_set_value(id, challan) {
            $('#hidden_mst_id').val(id);
            $('#hidden_txt_challan_no').val(challan);
            parent.emailwindow.hide();
        }

    </script>

    </head>

    <body>
    <div align="center" style="width:1030px;">
        <form name="searchwofrm" id="searchwofrm">
            <fieldset style="width:1020px;">
                <legend>Enter search words</legend>
                <table cellpadding="0" cellspacing="0" width="900" border="1" rules="all" class="rpt_table">
                    <thead>
                    <th>Sewing Company</th>
                    <th>Order No</th>
                    <th>Internal Ref.</th>
                    <th>Challan No</th>
                    <th>Cutting No</th>
                    <th>Order Cut No</th>
                    <th>Line No</th>
                    <th>Input Date</th>

                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
                               class="formbutton"/>
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes"
                               value="<? echo $cbo_company_name; ?>">
                        <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="text_boxes" value="">
                        <input type="hidden" name="hidden_txt_challan_no" id="hidden_txt_challan_no" class="text_boxes" value="">
                    </th>
                    </thead>
                    <tr class="general">
                        <td align="center" style="width:100px"  id="emb_company_td">
                            <?
                            echo create_drop_down("cbo_emb_company", 180, $blank_array, "", 1, "--- Select ---", $selected, "");

                            ?>
                        </td>
                        <td><input type="text" style="width:100px" class="text_boxes" name="txt_order_no" id="txt_order_no"/>
                        </td>
                        <td><input type="text" style="width:100px" class="text_boxes" name="txt_internal_ref" id="txt_internal_ref"/>
                        </td>
                        <td><input type="text" style="width:100px" class="text_boxes" name="txt_search_common"
                                   id="txt_search_common"/></td>
                         <td><input type="text" style="width:100px" class="text_boxes"  name="txt_cut_no" id="txt_cut_no" /></td>
                         <td><input type="text" style="width:100px" class="text_boxes"  name="txt_order_cut_no" id="txt_order_cut_no" /></td>
                        <td id="line_no_id">
                            <?
                            // $line_library = return_library_array("select id,line_name from lib_sewing_line where company_name=$cbo_emb_company", "id", "line_name");
                            // echo create_drop_down("cbo_line_no", 100, $line_library, "", 1, "--- Select ---", $selected, "");
                            echo create_drop_down( "cbo_line_no", 100, $blank_array,"", 1, "-- Select --", $selected, "" );
                            ?>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_issue_date" id="txt_issue_date" value="" class="datepicker"
                                   style="width:107px;"/>
                        </td>

                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show"
                                   onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_line_no').value+'_'+document.getElementById('txt_issue_date').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_emb_company').value+'_'+document.getElementById('txt_order_no').value+'_<?php echo $cbo_source; ?>'+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_order_cut_no').value, 'create_challan_search_list_view', 'search_div', 'bundle_wise_sewing_input_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
                                   style="width:100px;"/>
                        </td>

                    </tr>
                </table>
                <div style="width:100%; margin-top:10px; margin-left:3px" id="search_div" align="left"></div>
            </fieldset>
        </form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
        load_drop_down( 'bundle_wise_sewing_input_controller', '<?php echo $cbo_emb_company; ?>_<?php echo $cbo_location; ?>_<?php echo $cbo_floor; ?>_<?php echo $txt_issue_date; ?>', 'load_drop_down_line_no', 'line_no_id' );

        load_drop_down('bundle_wise_sewing_input_controller','<?php echo $cbo_source; ?>_<?php echo $cbo_emb_company; ?>'  , 'load_drop_down_sewing_company', 'emb_company_td');
    </script>

    </html>

    <?
}

if ($action == "load_drop_down_sewing_company") {
    $explode_data = explode("_", $data);
    $data = $explode_data[0];
    $serving_company =$explode_data[1];// $explode_data[1];

    if ($data == 3)
    {
        if ($db_type == 0)
        {
            echo create_drop_down("cbo_emb_company", 180, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,23,party_type) order by supplier_name", "id,supplier_name", 1, "--- Select ---", $serving_company,"",1);
        }
        else
        {
            echo create_drop_down("cbo_emb_company", 180, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(22,23) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select--", $serving_company,"",1);
        }
    }
    else if ($data == 1)
    {
        echo create_drop_down("cbo_emb_company", 180, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name", "id,company_name", 1, "--- Select ---", $serving_company,"",1);

    }
    else
    {
        echo create_drop_down("cbo_emb_company", 180, $blank_array, "", 1, "--- Select ---", $selected, "", 0);
    }

    exit();
}

if ($action == "create_challan_search_list_view") {
    // echo $data;//die;
    //echo $data;die;
    list($challan, $line_no, $issue_date, $company_id, $sew_company, $order_no, $cbo_source,$cutting_no,$internal_ref,$order_cut_no) = explode("_", $data);

    if ($internal_ref != '') {
        $internal_ref_con = " and c.grouping ='$internal_ref'";
    }

    $search_string = "%" . trim($data[0]) . "%";
    if ($challan != '') {
        $challan_con = " and b.challan_no ='$challan'";
    }

	if($company_id==0) { echo "Please Select Company."; die; }

	if ($challan == '' && $order_no=="" && $cutting_no=="" && $issue_date=="" && $internal_ref=="" && $order_cut_no=="")
	{
		echo "Please Select Order No or Challan No or Cutting No or Input Date or Order Cut No
        or Internal Ref."; die;
	}
    //echo $company_id;die;

    if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year";
    else if ($db_type == 2) $year_field = "MAX(to_char(a.insert_date,'YYYY')) as year";
    else $year_field = "";//defined Later

    if ($order_no != '')
    {

		$sql_po="SELECT id, po_number from wo_po_break_down where status_active=1 and is_deleted=0 and po_number like('%$order_no%')";
        // echo $sql_po;die();

		$sql_res=sql_select($sql_po); $po_arr=array(); $tot_rows=0; $poIds='';
		foreach($sql_res as $row)
		{
			$tot_rows++;
			$poIds.=$row[csf("id")].",";
			$po_arr[$row[csf('id')]]=$row[csf('po_number')];
		}
		unset($sql_res);

		$poIds=chop($poIds,','); $order_con = "";
		if($db_type==2 && $tot_rows>1000)
		{
			$order_con=" and (";

			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$order_con.=" b.po_break_down_id in($ids) or ";
			}
			$order_con=chop($order_con,'or ');
			$order_con.=")";
		}
		else
		{
			$order_con=" and b.po_break_down_id in ($poIds)";
		}
    } else {
        $order_con = "";
    }

    if ($db_type == 0) {
        if ($issue_date != '') $issue_date_con = "and a.delivery_date = '" . change_date_format($issue_date, "yyyy-mm-dd", "-") . "'"; else $issue_date_con = "";
    }
    if ($db_type == 2) {
        if ($issue_date != '') $issue_date_con = "and a.delivery_date = '" . change_date_format($issue_date, '', '', 1) . "'"; else $issue_date_con = "";
    }

    if ($sew_company != 0) {
        $sew_company_con = " and b.serving_company=$sew_company";
    }
    $cutting_no_cond=($cutting_no)? " and d.cut_no like '%".$cutting_no ."%'" : " ";
    // $line_con=($line_no)? " and a.sewing_line like '%".$line_no ."%'" : " ";
    $line_con=($line_no)? " and a.sewing_line = '".$line_no ."'" : " ";

    if ($order_cut_no != '') {
        $order_cut_no_con = " and f.order_cut_no ='$order_cut_no'";
    }

    if ($order_cut_no_con == ""){

        $sql = "SELECT a.id, $year_field, a.sys_number_prefix_num, a.sys_number, a.delivery_date, a.production_source, a.serving_company, a.location_id, a.floor_id,a.sewing_line,c.po_number,d.cut_no, c.grouping from pro_gmts_delivery_mst a,pro_garments_production_mst b  ,wo_po_break_down c,pro_garments_production_dtls d where  b.po_break_down_id=c.id and a.id=b.delivery_mst_id and b.id=d.mst_id and a.production_type=4 and b.production_type=4 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and b.production_source=$cbo_source $order_con $challan_con $issue_date_con $sew_company_con $cutting_no_cond $line_con $internal_ref_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id , a.sys_number_prefix_num, a.sys_number, a.delivery_date, a.production_source, a.serving_company,a.location_id, a.floor_id,a.sewing_line,c.po_number,d.cut_no, c.grouping order by a.id DESC";

    } else{
        $sql = "SELECT a.id, $year_field, a.sys_number_prefix_num, a.sys_number, a.delivery_date, a.production_source, a.serving_company, a.location_id, a.floor_id,a.sewing_line,c.po_number,d.cut_no, c.grouping , f.order_cut_no
        from pro_gmts_delivery_mst a,pro_garments_production_mst b  ,wo_po_break_down c,pro_garments_production_dtls d, ppl_cut_lay_mst e,ppl_cut_lay_dtls f
        where  b.po_break_down_id=c.id and a.id=b.delivery_mst_id and b.id=d.mst_id and e.job_no = c.job_no_mst
         and e.id = f.mst_id and a.production_type=4 and b.production_type=4 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and b.production_source=$cbo_source $order_con $challan_con $issue_date_con $sew_company_con $cutting_no_cond $line_con $internal_ref_con $order_cut_no_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active = 1 and e.is_deleted = 0 and f.status_active = 1 and f.is_deleted = 0
        group by a.id , a.sys_number_prefix_num, a.sys_number, a.delivery_date, a.production_source, a.serving_company,a.location_id, a.floor_id,a.sewing_line,c.po_number,d.cut_no, c.grouping, f.order_cut_no order by a.id DESC";
    }
    // echo $sql;


    $nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='$sew_company' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];

    // echo $sql;//die;
    $result = sql_select($sql);
	if ($order_no == '') {

		$tot_rows=0; $poIds='';
		foreach ($result as $row) {
			$tot_rows++;
			$poIds.=$row[csf("po_break_down_id")].",";
		}

		$poIds=chop($poIds,','); $order_con = "";
		if($db_type==2 && $tot_rows>1000)
		{
			$order_con=" and (";

			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$order_con.=" id in($ids) or ";
			}
			$order_con=chop($order_con,'or ');
			$order_con.=")";
		}
		else
		{
			$order_con=" and id in ($poIds)";
		}

		$sql_po="select id, po_number from wo_po_break_down where status_active=1 and is_deleted=0 $order_con";

		$sql_res=sql_select($sql_po); $po_arr=array(); $tot_rows=0; $poIds='';
		foreach($sql_res as $row)
		{
			$po_arr[$row[csf('id')]]=$row[csf('po_number')];
		}
		unset($sql_res);
	}

    $floor_arr = return_library_array("select id,floor_name from lib_prod_floor", 'id', 'floor_name');
    $location_arr = return_library_array("select id,location_name from lib_location", 'id', 'location_name');
    $company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
    $supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
    $line_library = return_library_array("select id,line_name from lib_sewing_line", "id", "line_name");
    $resource_alocate_line = return_library_array("select id, line_number from prod_resource_mst", "id", "line_number");
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table">
        <thead>
        <th width="30">SL</th>
        <th width="45">Challan</th>
        <th width="80">Cut No</th>
        <th width="40">Year</th>
        <th width="60">Input Date</th>
        <th width="60">Source</th>
        <th width="200">Sewing Company</th>
        <th width="110">Location</th>
        <th width="100">Floor</th>
        <th width="45">Line</th>
        <th>Order No</th>
        </thead>
    </table>
    <div style="width:1000px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="980" class="rpt_table" id="tbl_list_search">
            <?
            $i = 1;
            foreach ($result as $row) {
                if ($i % 2 == 0)
                    $bgcolor = "#E9F3FF";
                else
                    $bgcolor = "#FFFFFF";

                if ($row[csf('production_source')] == 1)
                    $serv_comp = $company_arr[$row[csf('serving_company')]];
                else
                    $serv_comp = $supllier_arr[$row[csf('serving_company')]];
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                    onClick="js_set_value('<? echo $row[csf('id')]; ?>', '<? echo $row[csf('sys_number')]; ?>');">
                    <td width="30"><? echo $i; ?></td>
                    <td width="45"><p>&nbsp;<? echo $row[csf('sys_number_prefix_num')]; ?></p></td>
                    <td width="80"><p>&nbsp;<? echo $row[csf('cut_no')]; ?></p></td>
                    <td width="40" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="60"><p><? echo change_date_format($row[csf('delivery_date')]); ?></p></td>
                    <td width="60"><p><? echo $knitting_source[$row[csf('production_source')]]; ?></p></td>
                    <td width="200"><p><? echo $serv_comp; ?></p></td>
                    <td width="110"><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
                    <td width="100"><p><? echo $floor_arr[$row[csf('floor_id')]]; ?></p></td>
                    <td width="45"><p>
                            <?
                            if ($prod_reso_allocation == 1) {
                                $sewing_line = $resource_alocate_line[$row[csf('sewing_line')]];
                                $sewing_line_arr = explode(",", $sewing_line);
                                $sewing_line_name = "";
                                foreach ($sewing_line_arr as $line_id) {
                                    $sewing_line_name .= $line_library[$line_id] . ",";
                                }
                                $sewing_line_name = chop($sewing_line_name, ",");
                                echo $sewing_line_name;
                            } else {
                                echo $line_library[$row[csf('sewing_line')]];
                            }
                            ?></p>
                    </td>
                    <td><p><? echo $row[csf('po_number')]; ?></p></td>
                </tr>
                <?
                $i++;
            }
            ?>
        </table>
    </div>
    <?
    exit();
}

if ($action == 'populate_data_from_challan_popup') {



    $data_array = sql_select("SELECT a.id, a.company_id, a.sys_number, a.embel_type, a.embel_name, a.production_source, a.serving_company, a.location_id, a.floor_id, a.organic, a.delivery_date,a.sewing_line,b.shift_name,a.working_company_id,a.working_location_id,a.remarks from pro_gmts_delivery_mst a,pro_garments_production_mst b where a.id=b.delivery_mst_id and a.id='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
    // echo $data_array;die;
    foreach ($data_array as $row) {

        // echo "load_drop_down( 'requires/bundle_wise_sewing_input_controller', '" . $row[csf('location_id')] . "', 'load_drop_down_floor', 'floor_td' );\n";

        // echo "load_drop_down( 'requires/bundle_wise_sewing_input_controller',".$row[csf("serving_company")]."+'_'+" . $row[csf('location_id')] . "+'_'+" . $row[csf('floor_id')] . "+'_'+'" . change_date_format($row[csf('delivery_date')]) . "', 'load_drop_down_line', 'line_td' );\n";
        // echo "load_drop_down( 'requires/bundle_wise_sewing_input_controller', '" . $row[csf('production_source')] . "', 'load_drop_down_embro_issue_source', 'emb_company_td' );\n";
        // echo "load_drop_down( 'requires/bundle_wise_sewing_input_controller', '".$row[csf('serving_company')]."', 'load_drop_down_location', 'location_td' );\n";

        echo "load_drop_down_multiple( 'requires/bundle_wise_sewing_input_controller',".$row[csf("serving_company")]."+'_'+" . $row[csf('location_id')] . "+'_'+" . $row[csf('floor_id')] . "+'_'+'" . change_date_format($row[csf('delivery_date')]) . "'+'_'+'" . $row[csf('production_source')] . "', 'load_drop_down_location_floor_line_and_source_line', 'floor_td*line_td*emb_company_td*location_td' );\n";



        echo "document.getElementById('txt_challan_no').value               = '" . $row[csf("sys_number")] . "';\n";
        echo "document.getElementById('cbo_company_name').value             = '" . $row[csf("company_id")] . "';\n";
        echo "$('#cbo_company_name').attr('disabled','true')" . ";\n";
        echo "$('#cbo_line_no').attr('disabled','true')" . ";\n";
        echo "$('#cbo_source').val('" . $row[csf('production_source')] . "');\n";


        echo "$('#cbo_emb_company').val('" . $row[csf('serving_company')] . "');\n";
        echo "$('#cbo_location').val('" . $row[csf('location_id')] . "');\n";

        echo "$('#cbo_floor').val('" . $row[csf('floor_id')] . "');\n";
        //echo "$('#cbo_embel_name').val('".$row[csf('embel_name')]."');\n";
        //echo "$('#cbo_embel_type').val('".$row[csf('embel_type')]."');\n";

        echo "$('#cbo_line_no').val('" . $row[csf('sewing_line')] . "');\n";

        echo "$('#txt_organic').val('" . $row[csf('organic')] . "');\n";
        echo "$('#txt_remarks').val('" . $row[csf('remarks')] . "');\n";
        echo "$('#txt_system_id').val('" . $row[csf('id')] . "');\n";
        echo "$('#txt_issue_date').val('" . change_date_format($row[csf('delivery_date')]) . "');\n";
        echo "$('#cbo_working_company_name').val('".$row[csf('working_company_id')]."');\n";
        echo "$('#cbo_working_location').val('".$row[csf('working_location_id')]."');\n";
        echo "$('#cbo_shift_name').val('".$row[csf('shift_name')]."');\n";


        echo "set_button_status(0, '" . $_SESSION['page_permission'] . "', 'fnc_issue_print_embroidery_entry',1,1);\n";
        exit();
    }
}

if ($action == "emblishment_issue_print") {
    extract($_REQUEST);
    $data = explode('*', $data);
    $cbo_company_name = $data[0];  $is_mail_send = $data[4]; $mail_id = $data[5]; $mail_body = $data[6];
    if( $is_mail_send == 1){ob_start();}
    //print_r ($data);
    $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
    $supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
    $location_library = return_library_array("select id, location_name from  lib_location", "id", "location_name");
    $floor_library = return_library_array("select id, floor_name from  lib_prod_floor", "id", "floor_name");
    $color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
    $line_library = return_library_array("select id,line_name from lib_sewing_line", "id", "line_name");
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
    $order_array = array();
    $order_sql = "SELECT a.job_no, a.buyer_name,a.style_ref_no,a.style_description, b.id, b.po_number, b.po_quantity from  wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
    $order_sql_result = sql_select($order_sql);
    foreach ($order_sql_result as $row) {
        $order_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
        $order_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
        $order_array[$row[csf('id')]]['po_number'] = $row[csf('po_number')];
        $order_array[$row[csf('id')]]['po_quantity'] = $row[csf('po_quantity')];
        $order_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
        $order_array[$row[csf('id')]]['style_des'] = $row[csf('style_description')];
    }

    $sql = "SELECT id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
    production_source, serving_company, floor_id, sewing_line, organic, delivery_date,working_company_id,working_location_id from pro_gmts_delivery_mst where production_type=4 and id='$data[1]' and
    status_active=1 and is_deleted=0 ";
    $dataArray = sql_select($sql);

    $nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='" . $dataArray[0][csf('serving_company')] . "' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];

    if ($prod_reso_allocation == 1) {

        if ($dataArray[0][csf('floor_id')] == 0 && $dataArray[0][csf('location_id')] != 0) $cond = " and a.location_id= {$dataArray[0][csf('location_id')]}";
        if ($dataArray[0][csf('floor_id')] != 0) $cond = " and a.floor_id= {$dataArray[0][csf('floor_id')]}";

        if ($db_type == 0) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
        } else if ($db_type == 2 || $db_type == 1) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
        }

        foreach ($line_data as $row) {
            $line = '';
            $line_number = explode(",", $row[csf('line_number')]);
            foreach ($line_number as $val) {
                if ($line == '') $line = $line_library[$val]; else $line .= "," . $line_library[$val];
            }
            $line_array[$row[csf('id')]] = $line;
        }
        $line = $line_array[$dataArray[0][csf('sewing_line')]];
    } else {
        $line = $line_library[$dataArray[0][csf('sewing_line')]];
    }

    ?>
    <div style="width:930px;">
        <table cellspacing="0" style="font: 12px tahoma; width: 100%;">
            <tr>
                <td colspan="6" align="center" style="font-size:24px">
                    <strong><? echo $company_library[$data[0]]; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="6" align="center" style="font-size:14px">
                    <?

                    $nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
                    foreach ($nameArray as $result) {
                        ?>
                        Plot No: <? echo $result[csf('plot_no')]; ?>
                        Level No: <? echo $result[csf('level_no')] ?>
                        Road No: <? echo $result[csf('road_no')]; ?>
                        Block No: <? echo $result[csf('block_no')]; ?>
                        City No: <? echo $result[csf('city')]; ?>
                        Zip Code: <? echo $result[csf('zip_code')]; ?>
                        Province No: <?php echo $result[csf('province')]; ?>
                        Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                        Email Address: <? echo $result[csf('email')]; ?>
                        Website No: <? echo $result[csf('website')];

                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:20px"><u><strong>Bundle Wise Delivery to Sewing Input Challan/Gate
                            Pass</strong></u></td>
            </tr>

            <tr>
                <td width="95"><strong>Challan No</strong></td>
                <td width="150"><? echo ": ".$dataArray[0][csf('sys_number')]; ?></td>
                <td width="80"><strong>Source</strong></td>
                <td width="190"><? echo ": ".$knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
                <td width="120"><strong>Sew. Company</strong></td>
                <td>
                    <?
                    if ($dataArray[0][csf('production_source')] == 1) echo ": ".$company_library[$dataArray[0][csf('serving_company')]];
                    else echo ": ".$supplier_library[$dataArray[0][csf('serving_company')]];

                    ?>
                </td>
            </tr>
            <tr>
                <td><strong>Location </strong></td>
                <td><? echo ": ".$location_library[$dataArray[0][csf('location_id')]]; ?></td>
                <td><strong>Floor  </strong></td>
                <td><? echo ": ".$floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
                <td><strong>Line </strong></td>
                <td><? echo ": ".$line; ?></td>
            </tr>
            <tr>
            <td><strong>Working Company  </strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
            <td><strong>Working Location </strong></td><td>: <? echo $location_library[$dataArray[0][csf('working_location_id')]]; ?></td>
            <td><strong>Input Date  </strong></td>
             <td><? echo ": ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
            </tr>
            <tr>

                <td>Remarks</td>
                <td colspan="3"><? //echo $dataArray[0][csf('sewing_line')];
                    ?></td>
            </tr>

			<!--<tr>
                <td width="125"><strong>Challan No:</strong></td>
                <td width="175px"><? echo $dataArray[0][csf('sys_number')]; ?></td>
                <td width="125"><strong>Embel. Name :</strong></td>
                <td width="175px"><? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
                <td width="125"><strong>Emb. Type:</strong></td>
                <td width="175px">
                    <?
                    if ($dataArray[0][csf('embel_name')] == 1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]];
                    elseif ($dataArray[0][csf('embel_name')] == 2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]];
                    elseif ($dataArray[0][csf('embel_name')] == 3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]];
                    elseif ($dataArray[0][csf('embel_name')] == 4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]];
                    ?>
                </td>
            </tr>
            <tr>
                <td><strong>Emb. Source:</strong></td>
                <td><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
                <td><strong>Emb. Company:</strong></td>
                <td>
                    <?
                    if ($dataArray[0][csf('production_source')] == 1) echo $company_library[$dataArray[0][csf('serving_company')]];
                    else echo $supplier_library[$dataArray[0][csf('serving_company')]];

                    ?>

                </td>
                <td><strong>Location:</strong></td>
                <td><? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Floor :</strong></td>
                <td><? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
                <td><strong>Organic :</strong></td>
                <td><? echo $dataArray[0][csf('organic')]; ?></td>
                <td><strong>Delivery Date :</strong></td>
                <td><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
            </tr>
            <tr>
         <td><strong>Body Part  </strong></td><td>: <? echo $body_part_arr[$dataArray[0][csf('body_part')]]; ?></td>
            <td><strong>Working Company  </strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
            <td><strong>Working Location </strong></td><td>: <? echo $location_library[$dataArray[0][csf('working_location_id')]]; ?></td>
        </tr>
		-->
        <tr>
            <td  colspan="4" id="barcode_img_id"></td>

        </tr>

        </table>
        <?
        $delivery_mst_id = $dataArray[0][csf('id')];

        if ($data[2] == 3) {
            $sql = "SELECT sum(b.production_qnty) as production_qnty,a.po_break_down_id,a.item_number_id,a.country_id, d.color_number_id,
            count(b.id) as  num_of_bundle
            from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d
            where a.delivery_mst_id ='$data[1]'
            and a.id=b.mst_id and b.color_size_break_down_id=d.id and b.production_type=4 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0
            group by a.po_break_down_id,a.item_number_id,a.country_id,d.color_number_id
            order by a.po_break_down_id,d.color_number_id ";
        } else {
            $sql = "SELECT sum(a.production_qnty) as production_qnty,c.po_break_down_id,c.item_number_id,c.country_id, b.color_number_id
            from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c
            where c.delivery_mst_id ='$data[1]'
            and c.id=a.mst_id and a.color_size_break_down_id=b.id and b.production_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
            group by c.po_break_down_id,c.item_number_id,c.country_id,b.color_number_id ";
        }
        $result = sql_select($sql);
        ?>

        <div style="width:100%;">
            <table cellspacing="0" width="900" border="1" rules="all" class="rpt_table"
                   style=" margin-top:20px; font: 12px tahoma;">
                <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="80" align="center">Buyer</th>
                <th width="80" align="center">Job</th>
                <th width="80" align="center">Style Ref</th>
                <th width="100" align="center">Style Des</th>
                <th width="80" align="center">Order No.</th>
                <th width="80" align="center">Gmt. Item</th>
                <th width="80" align="center">Country</th>
                <th width="80" align="center">Color</th>
                <th width="80" align="center">Gmt. Qty</th>
                <? if ($data[2] == 3) { ?>
                    <th align="center">No of Bundle</th>
                <? } ?>
                </thead>
                <tbody>
                <?

                $i = 1;
                $tot_qnty = array();
                foreach ($result as $val) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";

                    $color_count = count($cid);
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i; ?></td>
                        <td align="center"><? echo $buyer_arr[$order_array[$val[csf('po_break_down_id')]]['buyer_name']]; ?></td>
                        <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['job_no']; ?></td>
                        <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['style_ref']; ?></td>
                        <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['style_des']; ?></td>
                        <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['po_number']; ?></td>
                        <td align="center"><? echo $garments_item[$val[csf('item_number_id')]]; ?></td>
                        <td align="center"><? echo $country_library[$val[csf('country_id')]]; ?></td>
                        <td align="center"><? echo $color_library[$val[csf('color_number_id')]]; ?></td>
                        <td align="right"><? echo $val[csf('production_qnty')]; ?></td>
                        <? if ($data[2] == 3) { ?>
                            <td align="center"> <? echo $val[csf('num_of_bundle')]; ?></td>
                            <?
                        }
                        $color_qty_arr[$val[csf('color_number_id')]] += $val[csf('production_qnty')];
                        $color_wise_bundle_no_arr[$val[csf('color_number_id')]] += $val[csf('num_of_bundle')];
                        ?>
                    </tr>
                    <?
                    $total_bundle += $val[csf('num_of_bundle')];
                    $production_quantity += $val[csf('production_qnty')];
                    $i++;
                }
                ?>
                </tbody>
                <tr>
                    <td colspan="8"></td>
                </tr>
                <tr>
                    <? if ($data[3] == 3) $colspan = 8; else $colspan = 7; ?>
                    <td colspan="9" align="right"><strong>Grand Total :</strong></td>
                    <td align="right"><? echo $production_quantity; ?></td>
                    <? if ($data[2] == 3) { ?>
                        <td align="center"> <? echo $total_bundle; ?></td>
                    <? } ?>
                </tr>
            </table>
            <br clear="all">
            <table cellspacing="0" border="1" rules="all" class="rpt_table"
                   style=" margin-top:20px; font: 12px tahoma;">
                <thead>
                <tr>
                    <td colspan="4"><strong>Color Wise Summary</strong></td>
                </tr>
                <tr bgcolor="#dddddd" align="center">
                    <td>SL</td>
                    <td>Color</td>
                    <td>No Of Bundle</td>
                    <td>Quantity (Pcs)</td>
                </tr>
                </thead>
                <tbody>
                <? $i = 1;
                foreach ($color_qty_arr as $color_id => $color_qty):
                    $bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td align="center"><? echo $color_library[$color_id]; ?></td>
                        <td align="center"><? echo $color_wise_bundle_no_arr[$color_id]; ?></td>
                        <td align="right"><? echo $color_qty; ?></td>
                    </tr>
                    <?
                    $i++;
                endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="4"></td>
                </tr>
                <tr>
                    <td colspan="2" align="right">Total =</td>
                    <td align="center"><? echo $total_bundle; ?></td>
                    <td align="right"><? echo $production_quantity; ?></td>
                </tr>
                </tfoot>
            </table>
            <br>
            <?
            echo signature_table(28, $data[0], "900px");
            ?>
        </div>
    </div>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
        function generateBarcode(valuess) {
            var value = valuess;//$("#barcodeValue").val();
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

            value = {code: value, rect: false};
            $("#barcode_img_id").show().barcode(value, btype, settings);
        }
        generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
    </script>
    <?

        if($is_mail_send==1){
            $emailBody=ob_get_contents();

            $sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=120 and  b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
            $mail_sql_res=sql_select($sql);

            $mailArr=array();
            foreach($mail_sql_res as $row)
            {
                $mailArr[$row['EMAIL']]=$row['EMAIL'];
            }

            if($mail_id!=''){$mailArr[]=$mail_id;}


            $to=implode(',',$mailArr);
            $subject="Bundle Wise Delivery to Sewing Input Challan/Gate Pass";

            if($to!=""){
                include('../../auto_mail/setting/mail_setting.php');
                $header=mailHeader();
                echo sendMailMailer( $to, $subject, $mail_body."<br><br>".$emailBody,$from_mail,'' );
            }
        }
    exit();
}

if ($action == "emblishment_issue_print_2")
{
    extract($_REQUEST);
    $data = explode('*', $data);

    $cbo_company_name = $data[0]; $is_mail_send = $data[4]; $mail_id = $data[5]; $mail_body = $data[6];if( $is_mail_send == 1){ob_start();}

    //print_r ($data);
    $company_library = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    $job_no = array();
    $job_id = array();
    $country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
    $supplier_library = return_library_array("select id,supplier_name from  lib_supplier where status_active=1 and is_deleted=0", "id", "supplier_name");
    $location_library = return_library_array("select id, location_name from  lib_location where status_active=1 and is_deleted=0", "id", "location_name");
    $floor_library = return_library_array("select id, floor_name from  lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
    $color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
    $size_library = return_library_array("select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0 ", 'id', 'buyer_name');
    $line_library = return_library_array("select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0", "id", "line_name");

    $sql = "SELECT id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
    production_source, serving_company, floor_id,sewing_line, organic, delivery_date,working_company_id,working_location_id from pro_gmts_delivery_mst where production_type=4 and id='$data[1]' and
    status_active=1 and is_deleted=0 ";

    $dataArray = sql_select($sql);

    $nameArray = sql_select("SELECT id, auto_update from variable_settings_production where company_name='" . $dataArray[0][csf('serving_company')] . "' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];

    if ($prod_reso_allocation == 1)
     {

        if ($dataArray[0][csf('floor_id')] == 0 && $dataArray[0][csf('location_id')] != 0) $cond = " and a.location_id= {$dataArray[0][csf('location_id')]}";
        if ($dataArray[0][csf('floor_id')] != 0) $cond = " and a.floor_id= {$dataArray[0][csf('floor_id')]}";

        if ($db_type == 0) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
        } else if ($db_type == 2 || $db_type == 1) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
        }

        foreach ($line_data as $row) {
            $line = '';
            $line_number = explode(",", $row[csf('line_number')]);
            foreach ($line_number as $val) {
                if ($line == '') $line = $line_library[$val]; else $line .= "," . $line_library[$val];
            }
            $line_array[$row[csf('id')]] = $line;
        }
        $line = $line_array[$dataArray[0][csf('sewing_line')]];
    }
    else
     {
        $line = $line_library[$dataArray[0][csf('sewing_line')]];
     }
    unset($line_data);
    //$page_id_arr=[1,2,3,4];
    $page_id_arr=[1,2,3];
    $copy_name=array(1=>'st',2=>'nd',3=>'rd',4=>'th');

    $kk=0;
    foreach($page_id_arr as $vals)
    {
        $production_quantity  =0;
        $total_bundle  = 0;
        $size_qty_arr=array();
        $size_wise_bundle_no_arr=array();

        if ($data[2] == 3) {

            $sql = "SELECT sum(b.production_qnty) as production_qnty,a.po_break_down_id,a.item_number_id,a.country_id,b.cut_no,b.bundle_no,d.size_number_id, d.color_number_id,
            count(b.id) as  num_of_bundle

            from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d
            where a.delivery_mst_id ='$data[1]'
            and a.id=b.mst_id and b.production_type=4 and b.color_size_break_down_id=d.id and  b.status_active=1 and b.is_deleted=0 and d.status_active=1
            and d.is_deleted=0
            group by a.po_break_down_id,a.item_number_id,a.country_id,b.cut_no,b.bundle_no,d.size_number_id,d.color_number_id
            order by  length(b.bundle_no) asc, b.bundle_no  asc";//a.po_break_down_id,d.color_number_id
        }
         else {
            $sql = "SELECT sum(a.production_qnty) as production_qnty,c.po_break_down_id,c.item_number_id,c.country_id,a.cut_no,a.bundle_no,b.size_number_id, b.color_number_id

            from pro_garments_production_mst c, pro_garments_production_dtls a, wo_po_color_size_breakdown b
            where c.delivery_mst_id ='$data[1]'
            and c.id=a.mst_id and a.color_size_break_down_id=b.id and a.production_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
            group by c.po_break_down_id,c.item_number_id,c.country_id,a.cut_no,a.bundle_no,b.size_number_id,b.color_number_id order by  length(a.bundle_no) asc, a.bundle_no asc";
        }

        $delivery_mst_id = $dataArray[0][csf('id')];

               // echo $sql;
                //die;

                $data_result = sql_select($sql);
                $all_cut_arr=array();
                $all_po_arr=array();

                foreach($data_result as $v)
                {
                    $all_cut_arr[$v[csf("cut_no")]]=$v[csf("cut_no")];
                    $all_po_arr[$v[csf("po_break_down_id")]]=$v[csf("po_break_down_id")];
                }
                $all_cut_nos="'".implode("','", $all_cut_arr)."'";
                $all_po_nos="'".implode("','", $all_po_arr)."'";
                $ppl_mst_sql="SELECT a.cutting_no, c.batch_no, b.order_cut_no from ppl_cut_lay_mst a,  ppl_cut_lay_dtls b, pro_batch_create_mst c where a.id=b.mst_id and b.batch_id=c.id and a.status_active=1 and a.cutting_no in($all_cut_nos)";
              //  echo  $ppl_mst_sql ;die;

                $ppl_mst_arr=array();

                $order_cut_nos = '';
                foreach($ppl_mst_sql as $p_val)
                {
                    $ppl_mst_arr[$p_val[csf("cutting_no")]]=$p_val[csf("batch_no")];

                    if($order_cut_nos!='')
                    {
                        $order_cut_nos .= ", ".$p_val[csf("order_cut_no")];
                    }
                    else
                    {
                        $order_cut_nos .= $p_val[csf("order_cut_no")];
                    }
                }

                $ppl_mst_sql=sql_select("SELECT cutting_no,batch_id from ppl_cut_lay_mst where status_active=1 and cutting_no in($all_cut_nos) ");
                $ppl_mst_arr2=array();
                foreach($ppl_mst_sql as $p_val)
                {
                    $ppl_mst_arr2[$p_val[csf("cutting_no")]]=$p_val[csf("batch_id")];
                }
                $order_array = array();
                $order_sql = "SELECT a.id as job_id, a.job_no, a.buyer_name,a.style_ref_no, a.style_description,b.id, b.po_number, b.po_quantity,b.file_no,b.grouping,c.country_id from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in($all_po_nos)";
                $order_sql_result = sql_select($order_sql);
                $po_no = array();
                foreach ($order_sql_result as $row)
                {
                    $job_id[$row[csf('id')]] = $row[csf('job_id')];
                    $job_no[$row[csf('id')]] = $row[csf('job_no')];
                    $order_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
                    $order_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
                    $order_array[$row[csf('id')]]['po_number'] = $row[csf('po_number')];
                    $order_array[$row[csf('id')]]['po_quantity'] = $row[csf('po_quantity')];
                    $order_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
                    $order_array[$row[csf('id')]]['style_des'] = $row[csf('style_description')];
                    $order_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
                    $order_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
                    $order_array[$row[csf('id')]]['country_id'] = $row[csf('country_id')];
                    $po_no[$row[csf('id')]] = $row[csf('id')];
                }
                unset($order_sql_result);
                $po_no_im = implode(',',$po_no);
                $batch_query = "SELECT  a.BUNDLE_NO , a.ROLL_ID , b.ID , b.BATCH_NO FROM PPL_CUT_LAY_BUNDLE a , PRO_ROLL_DETAILS b
                WHERE a.ROLL_ID = b.id AND a.ORDER_ID IN($po_no_im) AND b.STATUS_ACTIVE=1 AND b.IS_DELETED=0 AND a.STATUS_ACTIVE=1 AND a.IS_DELETED=0";
              //  echo $batch_query ;die;
                $batch_arr = array();
                 foreach(sql_select($batch_query) as $key=>$row)
                 {
                     $batch_arr[$row['BUNDLE_NO']]['BARTCH_NO_DATA'] = $row['BATCH_NO'] ;
                 }
                 // echo "<pre>";
                 // print_r($batch_arr);die;

                $sql_cut="SELECT bundle_no, number_start as number_start, number_end as number_end  from ppl_cut_lay_bundle where status_active=1 and is_deleted=0 and order_id in($all_po_nos)";
                $sql_cut_res = sql_select($sql_cut); $number_arr=array();
                foreach($sql_cut_res as $row)
                {
                    $number_arr[$row[csf('bundle_no')]]['number_start']=$row[csf('number_start')];
                    $number_arr[$row[csf('bundle_no')]]['number_end']=$row[csf('number_end')];
                }
                unset($sql_cut_res);

        ?>
        <style type="text/css">
            @media print
            {
                #footer_id {page-break-after: always;}
            }
        </style>


            <div style="width:930px;">
                <table cellspacing="0" style="font: 11px tahoma; width: 100%;">
                    <tr>
                        <td colspan="5" align="center" style="font-size:24px">
                            <strong style="margin-left: 300px !important;"><? echo $company_library[$data[0]]; ?></strong>

                            </td>
                            <td><span style="font-size:x-large;  "><? echo $vals;?><sup><? echo $copy_name[$vals]; ?></sup> Copy</span></td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="6" align="center" style="font-size:14px">
                            <?

                            $nameArray = sql_select("SELECT plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
                            foreach ($nameArray as $result) {

                                echo $result[csf('city')];

                            }
                            unset($nameArray);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" align="center" style="font-size:20px"><u><strong>Bundle Wise Delivery to Sewing Input Challan/Gate Pass</strong></u></td>
                    </tr>
                    <tr>
                        <td width="95"><strong>Challan No</strong></td>
                        <td width="150"><? echo ": ".$dataArray[0][csf('sys_number')]; ?></td>
                        <td width="80"><strong>Source</strong></td>
                        <td width="190"><? echo ": ".$knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
                        <td width="120"><strong>Sew. Company</strong></td>
                        <td>
                            <?
                            if ($dataArray[0][csf('production_source')] == 1) echo ": ".$company_library[$dataArray[0][csf('serving_company')]];
                            else echo ": ".$supplier_library[$dataArray[0][csf('serving_company')]];

                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Location </strong></td>
                        <td><? echo ": ".$location_library[$dataArray[0][csf('location_id')]]; ?></td>
                        <td><strong>Floor  </strong></td>
                        <td><? echo ": ".$floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
                        <td><strong>Line </strong></td>
                        <td><? echo ": ".$line; ?></td>
                    </tr>
                    <tr>
                    <td><strong>Working Company  </strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
                    <td><strong>Working Location </strong></td><td>: <? echo $location_library[$dataArray[0][csf('working_location_id')]]; ?></td>
                    <td><strong>Input Date  </strong></td>
                     <td><? echo ": ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
                    </tr>
                    <tr>
                        <td>Remarks</td>
                        <td colspan=""><? //echo $dataArray[0][csf('sewing_line')];?></td>
                        <td> Order Cut No</td>
                        <td colspan=""><? echo ": ".$order_cut_nos; ?></td>
                    </tr>


                    <tr>
                        <td colspan="6" id="barcode_img_id_<? echo $kk;?>"></td>
                    </tr>

                </table>
                <br>
                <div style="width:100%;">
                    <table cellspacing="0" width="1240" border="1" rules="all" class="rpt_table" style="font: 12px tahoma;">
                        <thead bgcolor="#dddddd" align="center">
                        <th width="30">SL</th>
                        <th width="60" align="center">Bundle No</th>
                        <th width="60" align="center">Job</th>
                        <th width="80" align="center">Buyer</th>
                        <th width="80" align="center">File No</th>
                        <th width="80" align="center">Int. Ref.</th>
                        <th width="80" align="center">Country</th>
                        <th width="80" align="center">Style Ref</th>
                        <th width="100" align="center">Style Des</th>
                        <th width="80" align="center">Order No.</th>
                        <th width="80" align="center">Gmt. Item</th>
                        <th width="80" align="center">Color</th>
                        <th width="100" align="center">Batch</th>
                        <th width="80" align="center">Size</th>
                        <th width="60" align="center">Reject Qty</th>
                        <th width="60" align="center">RMG Qty</th>
                        <th width="60" align="center">Gmt. Qty</th>
                        </thead>
                        <tbody>
                        <?
                        $i = 1;
                        $size_wise_bundle_no = 0;
                        $num_of_bundle = 0;
                        $tot_qnty = array();
                        foreach ($data_result as $val)
                        {


                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";
                            $color_count = count($cid);
                            $number_start=''; $number_end='';
                            $number_start=$number_arr[$val[csf('bundle_no')]]['number_start'];
                            $number_end=$number_arr[$val[csf('bundle_no')]]['number_end'];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>">
                                <td><? echo $i; ?></td>
                                <td align="center"><? echo $val[csf('bundle_no')]; ?></td>
                                <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['job_no']; ?></td>
                                <td align="center"><? echo $buyer_arr[$order_array[$val[csf('po_break_down_id')]]['buyer_name']]; ?></td>
                                <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['file_no']; ?></td>
                                <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['grouping']; ?></td>
                                <td align="center"><? echo $country_arr[$val[csf('country_id')]]; ?></td>
                                <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['style_ref']; ?></td>
                                <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['style_des']; ?></td>
                                <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['po_number']; ?></td>
                                <td align="center"><? echo $garments_item[$val[csf('item_number_id')]]; ?></td>
                                <td align="center"><? echo $color_library[$val[csf('color_number_id')]]; ?></td>
                                <td align="center"><? echo  $batch_arr[$val['BUNDLE_NO']]['BARTCH_NO_DATA'] ;?></td>
                                <td align="center"><? echo $size_library[$val[csf('size_number_id')]]; ?></td>
                                <td></td>
                                <td align="center"><? echo $number_start.'-'.$number_end; ?></td>
                                <td align="right"><? echo $val[csf('production_qnty')]; ?></td>

                            </tr>
                            <?
                            $production_quantity += $val[csf('production_qnty')];
                            $total_bundle += $val[csf('num_of_bundle')];
                            $size_qty_arr[$val[csf('size_number_id')]] += $val[csf('production_qnty')];
                            $size_wise_bundle_no_arr[$val[csf('size_number_id')]] += $val[csf('num_of_bundle')];
                            $i++;
                        }
                        ?>
                        </tbody>
                        <tr>
                            <td colspan="13"></td>
                        </tr>
                        <tr>
                            <td colspan="3"><strong>No. Of Bundle :<? echo $total_bundle; ?></strong></td>
                            <td colspan="13" align="right"><strong>Grand Total </strong></td>
                            <td align="right"><? echo $production_quantity; ?></td>
                        </tr>
                    </table>

                    <br>
                    <table cellspacing="0" border="1" rules="all" cellpadding="3" style="font: 12px tahoma;">
                        <thead>
                        <tr>
                            <td colspan="4"><strong>Size Wise Summary</strong></td>
                        </tr>
                        <tr bgcolor="#dddddd" align="center">
                            <th>SL</th>
                            <th>Size</th>
                            <th>No Of Bundle</th>
                            <th>Quantity (Pcs)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $i = 1;
                        foreach ($size_qty_arr as $size_id => $size_qty):
                            $bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>">
                                <td align="center"><? echo $i; ?></td>
                                <td align="center"><? echo $size_library[$size_id]; ?></td>
                                <td align="center"><? echo $size_wise_bundle_no_arr[$size_id]; ?></td>
                                <td align="right"><? echo $size_qty; ?></td>
                            </tr>
                            <?
                            $i++;
                        endforeach; ?>
                        </tbody>
                        <tr>
                            <td colspan="4" style="padding: 1px !important;"></td>
                        </tr>
                        <tr>
                            <td colspan="2" align="right"><strong>Total </strong></td>
                            <td align="center"><? echo $total_bundle; ?></td>
                            <td align="right"><? echo $production_quantity; ?></td>
                        </tr>
                    </table>
                    <br>
                    <?
                    $job_no_implode = "'".implode("','", $job_no)."'";
                    $job_id_implode = "'".implode(",", $job_id)."'";
                    $all_po_id = "'".implode(",", $all_po_arr)."'";
    //                    echo $all_po_id;
    //                    echo "select a.id, a.job_no, a.job_id, a.trim_group, b.item_name, c.po_number, c.po_quantity, a.description, a.cons_uom, a.cons_dzn_gmts, a.remark, c.total_set_qnty from wo_pre_cost_trim_cost_dtls a, lib_item_group b, wo_po_break_down c, wo_po_details_master d where b.id = a.trim_group and c.job_no_mst = a.job_no and d.id = c.job_id and b.trim_type = 1 and c.id in ($all_po_id) order  by a.id asc";
                    $sqlTrimData = sql_select("select a.id, a.job_no, a.job_id, a.trim_group, b.item_name, c.po_number, c.po_quantity, a.description, a.cons_uom, a.cons_dzn_gmts, a.remark, d.total_set_qnty, d.order_uom from wo_pre_cost_trim_cost_dtls a, lib_item_group b, wo_po_break_down c, wo_po_details_master d where b.id = a.trim_group and c.job_no_mst = a.job_no and d.id = c.job_id and b.trim_type = 1 and c.id in ($all_po_id) order  by a.id asc");
                    if(count($sqlTrimData) > 0){
                    ?>
                        <table cellspacing="0" border="1" rules="all" cellpadding="3" style="font: 12px tahoma;" width="1240">
                        <thead>
                        <tr>
                            <th colspan="9" align="left"><strong>Required Sewing Trims</strong></th>
                        </tr>
                        <tr bgcolor="#dddddd" align="center">
                            <th width="30">SL</th>
                            <th width="140">Item Name</th>
                            <th width="200">Description</th>
                            <th width="120">Order No.</th>
                            <th width="120">Wo No.</th>
                            <th width="100">Budget Cons/DZN</th>
                            <th width="70">UOM</th>
                            <th width="100">Required Qty.</th>
                            <th>Remarks</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?
                        $sqlGetBookingData = sql_select("select pre_cost_fabric_cost_dtls_id, booking_no, wo_qnty from wo_booking_dtls where po_break_down_id in ($all_po_id) and status_active = 1  and is_deleted = 0");
                        $bookingArr = array();
                        foreach ($sqlGetBookingData as $key => $booking){
                            $bookingArr[$booking[csf('pre_cost_fabric_cost_dtls_id')]]['booking_no'][$key] = $booking[csf('booking_no')];
                            $bookingArr[$booking[csf('pre_cost_fabric_cost_dtls_id')]]['booking_qty'][$key] = $booking[csf('wo_qnty')];
                        }
                        $mainDataArr = array();
                        $totalQty = 0;
                        foreach ($sqlTrimData as $key => $preTrim){
                            $keyMod = $preTrim[csf('item_name')]."*".$preTrim[csf('trim_group')]."*".$preTrim[csf('description')]."*".$preTrim[csf('cons_uom')];
                            $mainDataArr[$keyMod]['item_name'] = $preTrim[csf('item_name')];
                            $mainDataArr[$keyMod]['description'] = $preTrim[csf('description')];
                            $mainDataArr[$keyMod]['uom'] =  $unit_of_measurement[$preTrim[csf('cons_uom')]];
                            $mainDataArr[$keyMod]['cons_dzn'] += $preTrim[csf('cons_dzn_gmts')];
                            $mainDataArr[$keyMod]['remarks'] = $preTrim[csf('remark')];
                            $mainDataArr[$keyMod]['order_no'] = $preTrim[csf('po_number')];
                            $mainDataArr[$keyMod]['po_qty'] = $preTrim[csf('po_quantity')];
                            $mainDataArr[$keyMod]['set_qty'] = ($preTrim[csf('order_uom')] == 58 ? $preTrim[csf('total_set_qnty')] : 1);
                            $mainDataArr[$keyMod]['booking_no'] = implode(', ',array_unique($bookingArr[$preTrim[csf('id')]]['booking_no']));
                            $mainDataArr[$keyMod]['booking_qty'] = array_sum($bookingArr[$preTrim[csf('id')]]['booking_qty']);
                        }
                        $count = 1;
                        foreach ($mainDataArr as $val){
                            if ($count % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";

                            $reqQty = ($val['booking_qty'] / ($val['po_qty']*$val['set_qty'])) * $production_quantity;
                        ?>
                            <tr bgcolor="<?=$bgcolor?>">
                                <td align="center"><?=$count?></td>
                                <td><?=$val['item_name']?></td>
                                <td><?=$val['description']?></td>
                                <td align="center"><?=$val['order_no']?></td>
                                <td align="center" ><?=$val['booking_no']?></td>
                                <td align="right"><?=number_format($val['cons_dzn'], '2', '.', '')?></td>
                                <td align="center"><?=$val['uom']?></td>
                                <td align="right" title="Required Qty. = (Trims booking qty./Order qty.) x Sewing input qty."><?=number_format($reqQty, 2)?></td>
                                <td><?=$val['remarks']?></td>
                            </tr>
                        <?
                            $totalQty += $reqQty;
                            $count++;
                        }
                        ?>
                        </tbody>
                            <tr>
                                <td colspan="9" style="padding: 1px !important;"></td>
                            </tr>
                            <tr>
                                <td align="right" colspan="7"><strong>Total</strong></td>
                                <td align="right"><?=number_format($totalQty, 2)?></td>
                                <td></td>
                            </tr>

                    </table>
                        <br>
                    <?
                    }
                    echo signature_table(28, $data[0], "900px");
                    ?>
                </div>
            </div>
            <div id="footer_id"></div>

        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
            function generateBarcode(valuess) {
                var ids='<? echo $kk;?>';
                var value = valuess;//$("#barcodeValue").val();
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

                value = {code: value, rect: false};
                $("#barcode_img_id_"+ids).show().barcode(value, btype, settings);
            }
            generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
        </script>
        <?
        $kk++;
    }


    if($is_mail_send==1){
        $emailBody=ob_get_contents();

        $sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=120 and  b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
        $mail_sql_res=sql_select($sql);

        $mailArr=array();
        foreach($mail_sql_res as $row)
        {
            $mailArr[$row['EMAIL']]=$row['EMAIL'];
        }

        if($mail_id!=''){$mailArr[]=$mail_id;}


        $to=implode(',',$mailArr);
        $subject="Bundle Wise Delivery to Sewing Input Challan/Gate Pass";

        if($to!=""){
            include('../../auto_mail/setting/mail_setting.php');
            $header=mailHeader();
            echo sendMailMailer( $to, $subject, $mail_body."<br><br>".$emailBody,$from_mail,'' );
        }
    }



    exit();
}


if($action=="emblishment_issue_print_3")
{
    extract($_REQUEST);
    $data=explode('*',$data);
    $cbo_company_name = $data[0];  $is_mail_send = $data[4]; $mail_id = $data[5]; $mail_body = $data[6];if( $is_mail_send == 1){ob_start();}
    //print_r ($data);
    $company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
    $supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
    $location_library=return_library_array( "select id, location_name from  lib_location", "id", "location_name");
    $floor_library=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name");
    $color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
    $size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
    $line_library = return_library_array("select id,line_name from lib_sewing_line", "id", "line_name");
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $body_part_arr=return_library_array( "select id,bundle_use_for from ppl_bundle_title where company_id=$data[0]",'id','bundle_use_for');

    $sql="SELECT id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
    production_source, serving_company, floor_id, sewing_line, organic, delivery_date,working_company_id,working_location_id,inserted_by from pro_gmts_delivery_mst where production_type=4 and id='$data[1]' and
    status_active=1 and is_deleted=0 ";
    //  echo $sql;
    $dataArray=sql_select($sql);
    $inserted_by=$dataArray[0][csf("inserted_by")];
    $user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );

    $nameArray = sql_select("SELECT id, auto_update from variable_settings_production where company_name='" . $dataArray[0][csf('serving_company')] . "' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];

    if ($prod_reso_allocation == 1) {

        if ($dataArray[0][csf('floor_id')] == 0 && $dataArray[0][csf('location_id')] != 0) $cond = " and a.location_id= {$dataArray[0][csf('location_id')]}";
        if ($dataArray[0][csf('floor_id')] != 0) $cond = " and a.floor_id= {$dataArray[0][csf('floor_id')]}";

        if ($db_type == 0) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
        } else if ($db_type == 2 || $db_type == 1) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
        }

        foreach ($line_data as $row) {
            $line = '';
            $line_number = explode(",", $row[csf('line_number')]);
            foreach ($line_number as $val) {
                if ($line == '') $line = $line_library[$val]; else $line .= "," . $line_library[$val];
            }
            $line_array[$row[csf('id')]] = $line;
        }
        $line = $line_array[$dataArray[0][csf('sewing_line')]];
    } else {
        $line = $line_library[$dataArray[0][csf('sewing_line')]];
    }

    $cut_mst_sql = "SELECT B.ID,B.CUT_NO,A.SYS_NUMBER FROM PRO_GMTS_DELIVERY_MST A, PRO_GARMENTS_PRODUCTION_MST B
    WHERE A.ID = B.DELIVERY_MST_ID AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND  B.DELIVERY_MST_ID='$data[1]' AND B.PRODUCTION_TYPE=4 ORDER BY B.ID DESC";

    // $cut_mst_sql = "SELECT B.ID,B.CUT_NO,A.SYS_NUMBER ,C.BATCH_ID,C.OTHER_FABRIC_WEIGHT,D.FLOOR_ID FROM PRO_GMTS_DELIVERY_MST A, PRO_GARMENTS_PRODUCTION_DTLS B, PPL_CUT_LAY_MST C, LIB_CUTTING_TABLE D
    // WHERE A.ID = B.DELIVERY_MST_ID AND C.TABLE_NO=D.ID AND B.CUT_NO=C.CUTTING_NO AND  B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND  A.ID='$data[1]' ";
    //echo $cut_mst_sql;
	$cut_mst_sql_result = sql_select($cut_mst_sql);

	foreach($cut_mst_sql_result as $row_req)
	{
	$cut_mst .= "'".$row_req['CUT_NO']."'".",";
	}
	$all_cut_mst = ltrim(implode(",", array_unique(explode(",", chop($cut_mst, ",")))), ',');

    $ppl_mst_sql = "SELECT A.ID,A.BATCH_ID,A.OTHER_FABRIC_WEIGHT,B.FLOOR_ID FROM PPL_CUT_LAY_MST A, LIB_CUTTING_TABLE B
    WHERE A.TABLE_NO=B.ID AND  B.STATUS_ACTIVE=1 AND B.IS_DELETED=0  and A.CUTTING_NO IN ($all_cut_mst) ORDER BY A.ID DESC";
    $ppl_mst_sql_result = sql_select($ppl_mst_sql);
    // echo $ppl_mst_sql;

	?>
	<div style="width:900px;">
	    <table cellspacing="0" style="font: 11px tahoma; width: 100%;">
	        <tr>
	            <td colspan="6" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="6" align="center" style="font-size:14px">
	                <?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
					foreach ($nameArray as $result)
					{
						 echo $result[csf('city')];
					}
	                ?>
	            </td>
	        </tr>
	        <tr>
	            <td colspan="6" align="center" style="font-size:20px"><u><strong>Bundle Wise Delivery to Sewing Input Challan/Gate Pass</strong></u></td>
	        </tr>
            <tr>
                <td width="95"><strong>Challan No</strong></td>
                <td width="150"><? echo ": ".$dataArray[0][csf('sys_number')]; ?></td>
                <td width="80"><strong>Source</strong></td>
                <td width="190"><? echo ": ".$knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
                <td width="120"><strong>Sew. Company</strong></td>
                <td>
                    <?
                    if ($dataArray[0][csf('production_source')] == 1) echo ": ".$company_library[$dataArray[0][csf('serving_company')]];
                    else echo ": ".$supplier_library[$dataArray[0][csf('serving_company')]];

                    ?>
                </td>
            </tr>
            <tr>
                <td><strong>Location</strong></td>
                <td><? echo ": ".$location_library[$dataArray[0][csf('location_id')]]; ?></td>
                <td><strong>Floor </strong></td>
                <td><? echo ": ".$floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
                <td><strong>Line </strong></td>
                <td><? echo ": ".$line; ?></td>
            </tr>
            <tr>
            <td><strong>Working Company  </strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
            <td><strong>Working Location </strong></td><td>: <? echo $location_library[$dataArray[0][csf('working_location_id')]]; ?></td>
            <td><strong>Input Date </strong></td>
             <td><? echo ": ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
            </tr>
            <tr>
                <td><strong>Remarks</strong></td>
                <td><? //echo $dataArray[0][csf('sewing_line')]; ?></td>
                <td><strong>Cutting Floor </strong></td><td>: <? echo $floor_library[$ppl_mst_sql_result[0]['FLOOR_ID']];; ?></td>
                <td><strong>Batch No </strong></td><td>: <? echo $ppl_mst_sql_result[0]['BATCH_ID']; ?></td>
            </tr>
            <tr>
                <td></td><td></td>
                <td></td><td></td>
                <td><strong>Others Fabric Weight </strong></td><td>: <? echo $ppl_mst_sql_result[0]['OTHER_FABRIC_WEIGHT']; ?></td>
            </tr>
            <!--
            <tr>
            <td width="90"><strong>Challan No</strong></td> <td width="140px">: <? echo $dataArray[0][csf('sys_number')]; ?></td>
            <td width="110"><strong>Embel. Name</strong></td><td width="175px"> : <? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
            <td width="105"><strong>Emb. Type</strong></td><td width="155px">:
            <?
                if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]];
                elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]];
                elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]];
                elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]];
             ?>
            </td>
        </tr>
        <tr>
            <td><strong>Emb. Source</strong></td><td>: <? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
            <td><strong>Emb. Company</strong></td><td>:
                <?
                    if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]];
                    else echo $supplier_library[$dataArray[0][csf('serving_company')]];

                ?>
            </td>
            <td><strong>Location</strong></td><td>: <? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Floor </strong></td><td>: <? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
            <td><strong>Organic</strong></td><td> : <? echo $dataArray[0][csf('organic')]; ?></td>
            <td><strong>Delivery Date </strong></td><td>: <? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
        </tr>
        <tr>
         <td><strong>Body Part  </strong></td><td>: <? echo $body_part_arr[$dataArray[0][csf('body_part')]]; ?></td>
            <td><strong>Working Company  </strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
            <td><strong>Working Location </strong></td><td>: <? echo $location_library[$dataArray[0][csf('working_location_id')]]; ?></td>
        </tr>
        -->
        <tr>
            <td  colspan="4" id="barcode_img_id"></td>

        </tr>
	    </table><br />
	    <?
		if($db_type==2)
			$group_concat=" listagg(cast(b.cut_no AS VARCHAR2(4000)),',') within group (order by b.cut_no) as cut_no";
	    else if($db_type==0)
			$group_concat=" group_concat(b.cut_no) as cut_no" ;

	            $delivery_mst_id =$dataArray[0][csf('id')];
	            // base on Embel. Name
	            if($data[2]==3)
	            {
	                if($db_type==0)
	                {
	                    $sql="SELECT  sum(b.production_qnty) as production_qnty, a.po_break_down_id, a.item_number_id, a.country_id, b.bundle_no, d.color_number_id, d.size_number_id, sum(d.order_quantity) as order_quantity
	                    count(b.id) as  num_of_bundle,
	                    (select sum(c.number_start) from ppl_cut_lay_bundle c where  b.bundle_no = c.bundle_no) number_start,
	                    (select sum(e.number_end) from ppl_cut_lay_bundle e where  b.bundle_no = e.bundle_no) number_end
	                    from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d
	                    where a.delivery_mst_id ='$data[1]' and a.id=b.mst_id and b.color_size_break_down_id=d.id and b.status_active=1 and b.is_deleted=0 and d.status_active=1
	                    and d.is_deleted=0 and b.bundle_no <> ''
	                    group by a.po_break_down_id, a.item_number_id, a.country_id, b.bundle_no, d.color_number_id, d.size_number_id,  d.order_quantity order by b.bundle_no ";

						//for previous size input qty
	                    $sql_prev="SELECT  sum(b.production_qnty) as production_qnty, a.po_break_down_id, a.item_number_id, a.country_id, b.bundle_no, d.color_number_id, d.size_number_id, d.order_quantity
	                    count(b.id) as  num_of_bundle,
	                    (select sum(c.number_start) from ppl_cut_lay_bundle c where  b.bundle_no = c.bundle_no) number_start,
	                    (select sum(e.number_end) from ppl_cut_lay_bundle e where  b.bundle_no = e.bundle_no) number_end
	                    from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d";
	                    $sql_prev_where=" where a.delivery_mst_id <> '$data[1]' and a.id=b.mst_id and b.color_size_break_down_id=d.id and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.bundle_no <> '' and a.po_break_down_id in(";
						$size_condition = " and d.size_number_id in(";
	                    $sql_prev_group=" group by a.po_break_down_id, a.item_number_id, a.country_id, b.bundle_no, d.color_number_id, d.size_number_id, d.order_quantity order by b.bundle_no ";
	                }
	                else
	                {
	                    /*$sql="SELECT $group_concat,sum(b.production_qnty) as production_qnty,a.po_break_down_id,a.item_number_id,a.country_id,b.bundle_no, d.color_number_id,d.size_number_id,
	                    count(b.id) as  num_of_bundle, sum(C.NUMBER_START) number_start, sum(C.NUMBER_END) number_end
	                    from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d, ppl_cut_lay_bundle  c
	                    where a.delivery_mst_id ='$data[1]' and a.id=b.mst_id and b.bundle_no = c.bundle_no and b.color_size_break_down_id=d.id and b.status_active=1 and b.is_deleted=0 and d.status_active=1
	                    and d.is_deleted=0  and b.bundle_no is not null
	                    group by a.po_break_down_id,a.item_number_id,a.country_id,b.bundle_no,d.color_number_id,d.size_number_id
	                    order by a.po_break_down_id,d.color_number_id ";*/

	                 	$sql="SELECT $group_concat,sum(b.production_qnty) as production_qnty,a.po_break_down_id,a.item_number_id,a.country_id,b.bundle_no, d.color_number_id,d.size_number_id, d.order_quantity,
	                    count(b.id) as  num_of_bundle  ,d.color_order,d.size_order
	                    from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d
	                    where a.delivery_mst_id ='$data[1]' and a.id=b.mst_id  and b.color_size_break_down_id=d.id and b.status_active=1 and b.is_deleted=0 and d.status_active=1
	                    and d.is_deleted=0  and b.bundle_no is not null and a.production_type=4
	                    group by a.po_break_down_id,a.item_number_id,a.country_id,b.bundle_no,d.color_number_id,d.size_number_id, d.order_quantity ,d.color_order,d.size_order
	                    order by d.color_order,d.size_order ";

	                 	$sql_prev="SELECT $group_concat,sum(b.production_qnty) as production_qnty, a.po_break_down_id, a.item_number_id, a.country_id, b.bundle_no, d.color_number_id, d.size_number_id, sum(d.order_quantity) as order_quantity,
	                    count(b.id) as  num_of_bundle , d.color_order,d.size_order
	                    from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d";
	                    $sql_prev_where=" where a.delivery_mst_id <> '$data[1]' and a.id=b.mst_id  and b.color_size_break_down_id=d.id and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.bundle_no is not null and a.production_type=4 and a.po_break_down_id in(";
						$size_condition = " and d.size_number_id in(";
	                    $sql_prev_group=" group by a.po_break_down_id, a.item_number_id, a.country_id, b.bundle_no, d.color_number_id, d.size_number_id, d.order_quantity ,d.color_order,d.size_order order by d.color_order,d.size_order ";

	                }
	            }
	            else
	            {
	                if($db_type==0)
	                {
	                    $sql="SELECT sum(a.production_qnty) as production_qnty,c.po_break_down_id,c.item_number_id,c.country_id, b.color_number_id,b.size_number_id, b.order_quantity
	                    from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c where c.delivery_mst_id ='$data[1]'
	                    and c.id=a.mst_id  and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.bundle_no!=''
	                    group by c.po_break_down_id,c.item_number_id,c.country_id,b.color_number_id,b.size_number_id, b.order_quantity";

					    $sql_prev="SELECT sum(a.production_qnty) as production_qnty, c.po_break_down_id, c.item_number_id, c.country_id, b.color_number_id, b.size_number_id, sum(b.order_quantity) as order_quantity
	                    from pro_garments_production_dtls a, wo_po_color_size_breakdown b, pro_garments_production_mst c";
						$sql_prev_where=" where c.delivery_mst_id ='$data[1]' and c.id=a.mst_id  and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.bundle_no!='' and c.po_break_down_id in(";
						$size_condition = " and b.size_number_id in(";
	                    $sql_prev_group=" group by c.po_break_down_id,c.item_number_id,c.country_id,b.color_number_id,b.size_number_id, b.order_quantity";
	                }
	                else
	                {
	                    $sql="SELECT sum(a.production_qnty) as production_qnty,c.po_break_down_id,c.item_number_id,c.country_id,b.color_number_id ,b.size_number_id, b.order_quantity,d.color_order,d.size_order
	                    from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c where c.delivery_mst_id ='$data[1]' and c.id=a.mst_id
	                    and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.bundle_no is not null
	                    group by c.po_break_down_id,c.item_number_id,c.country_id,b.color_number_id,b.size_number_id, b.order_quantity ,d.color_order,d.size_order order by d.color_order,d.size_order";

					    $sql_prev="SELECT sum(a.production_qnty) as production_qnty, c.po_break_down_id, c.item_number_id, c.country_id, b.color_number_id, b.size_number_id, sum(b.order_quantity) as order_quantity
	                    from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c";
						$sql_prev_where=" where c.delivery_mst_id ='$data[1]' and c.id=a.mst_id and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.bundle_no is not null and c.po_break_down_id in(";
						$size_condition = " and b.size_number_id in(";
	                    $sql_prev_group=" group by c.po_break_down_id,c.item_number_id,c.country_id,b.color_number_id,b.size_number_id, b.order_quantity";
	                }
	            }

	            // echo $sql;
	            //echo $sql_prev;
	            $result=sql_select($sql);
	            $rows=array();
	            $po_cutlay_id_arr=array();
	            $size_no_id_arr=array();
	            $po_wise_ordcutno_arr=array();
	            foreach($result as $value)
	            {
	                $po_cutlay_id_arr[$value[csf('po_break_down_id')]]= $value[csf('po_break_down_id')];
	                $size_no_id_arr[$value[csf('size_number_id')]]= $value[csf('size_number_id')];
	            }
	            $order_id_cut=implode(",", $po_cutlay_id_arr);
	            if(!$order_id_cut)
					$order_id_cut=0;

	            $sql_order_cut="SELECT c.cutting_no, a.order_cut_no, b.order_id from ppl_cut_lay_mst c, ppl_cut_lay_dtls a, ppl_cut_lay_bundle b where c.id=b.mst_id  and c.status_active=1 and c.is_deleted=0 and  a.mst_id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_id in($order_id_cut) group by c.cutting_no, a.order_cut_no, b.order_id " ;
	            foreach(sql_select($sql_order_cut) as $vals)
	            {
	                $po_ids=explode(",", $vals[csf("order_id")]);
	                foreach($po_ids as $val)
	                {
	                    $po_wise_ordcutno_arr[$vals[csf("cutting_no")]][$val]=$vals[csf("order_cut_no")];
	                }
	            }
	            //print_r($po_wise_ordcutno_arr);die;

				//for previous size input qty
				//echo $sql_prev.$sql_prev_where.$order_id_cut.")".$size_condition.implode(',', $size_no_id_arr).")".$sql_prev_group;
				$sql_prev_rslt=sql_select($sql_prev.$sql_prev_where.$order_id_cut.")".$size_condition.implode(',', $size_no_id_arr).")".$sql_prev_group);
				$prev_qty_arr = array();
				foreach($sql_prev_rslt as $row)
				{
					$prev_qty_arr[$order_array[$row[csf('po_break_down_id')]]['po_number']][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty'] += $row[csf('production_qnty')];
				}
				//echo "<pre>";
				//print_r($prev_qty_arr);
                $order_array=array();
                //$order_sql="select a.job_no, a.buyer_name,a.style_ref_no, b.id, b.po_number, b.po_quantity,c.cutting_no  from  wo_po_details_master a, wo_po_break_down b,ppl_cut_lay_mst c,ppl_cut_lay_dtls d where  c.id=d.mst_id and d.order_id=b.id and a.job_no=b.job_no_mst and b.job_no_mst=c.job_no and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";//c.entry_form=77 and
                $order_sql="SELECT a.job_no, a.buyer_name,a.style_ref_no,a.style_description, b.id, b.po_number, b.po_quantity,b.grouping  from  wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$data[0] and b.id in($order_id_cut)";//c.entry_form=77 and

                $order_sql_result=sql_select($order_sql);
                foreach ($order_sql_result as $row)
                {
                    $order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
                    $order_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
                    $order_array[$row[csf('id')]]['grouping']=$row[csf('grouping')];
                    $order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
                    $order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
                    $order_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
                    $order_array[$row[csf('id')]]['cut_num'][$row[csf('cutting_no')]]=$row[csf('cutting_no')];
                    $order_array[$row[csf('id')]]['style_des']=$row[csf('style_description')];
                }

	            foreach($result as $val)
	            {
	                $rows[$buyer_arr[$order_array[$val[csf('po_break_down_id')]]['buyer_name']]] [$order_array[$val[csf('po_break_down_id')]]['job_no']][$order_array[$val[csf('po_break_down_id')]]['grouping']] [$order_array[$val[csf('po_break_down_id')]]['style_ref']] [$order_array[$val[csf('po_break_down_id')]]['style_des']] [$order_array[$val[csf('po_break_down_id')]]['po_number']] [implode(',',array_unique(explode(',',$val[csf('cut_no')])))] [$garments_item[$val[csf('item_number_id')]]][$country_library[$val[csf('country_id')]]] [$color_library[$val[csf('color_number_id')]]] ['val']+=$val[csf('production_qnty')];

	                $rows[$buyer_arr[$order_array[$val[csf('po_break_down_id')]]['buyer_name']]] [$order_array[$val[csf('po_break_down_id')]]['job_no']][$order_array[$val[csf('po_break_down_id')]]['grouping']] [$order_array[$val[csf('po_break_down_id')]]['style_ref']] [$order_array[$val[csf('po_break_down_id')]]['style_des']] [$order_array[$val[csf('po_break_down_id')]]['po_number']] [implode(',',array_unique(explode(',',$val[csf('cut_no')])))] [$garments_item[$val[csf('item_number_id')]]][$country_library[$val[csf('country_id')]]] [$color_library[$val[csf('color_number_id')]]] ['order_no']=$val[csf('po_break_down_id')];

	                $rows[$buyer_arr[$order_array[$val[csf('po_break_down_id')]]['buyer_name']]] [$order_array[$val[csf('po_break_down_id')]]['job_no']][$order_array[$val[csf('po_break_down_id')]]['grouping']] [$order_array[$val[csf('po_break_down_id')]]['style_ref']] [$order_array[$val[csf('po_break_down_id')]]['style_des']] [$order_array[$val[csf('po_break_down_id')]]['po_number']] [implode(',',array_unique(explode(',',$val[csf('cut_no')])))] [$garments_item[$val[csf('item_number_id')]]][$country_library[$val[csf('country_id')]]] [$color_library[$val[csf('color_number_id')]]] ['count']++;

	               //$size_qty_arr[$val[csf('size_number_id')]] += $val[csf('production_qnty')];
	               // $size_wise_bundle_no_arr[$val[csf('size_number_id')]] ++;
	                $po_size_qty_arr[$order_array[$val[csf('po_break_down_id')]]['po_number']][$val[csf('color_number_id')]][$val[csf('size_number_id')]]['qty'] += $val[csf('production_qnty')];
	                $po_size_qty_arr[$order_array[$val[csf('po_break_down_id')]]['po_number']][$val[csf('color_number_id')]][$val[csf('size_number_id')]]['order_quantity'] = $val[csf('order_quantity')];
                    $po_size_qty_arr[$order_array[$val[csf('po_break_down_id')]]['po_number']][$val[csf('color_number_id')]][$val[csf('size_number_id')]]['po_break_down_id'] = $val[csf('po_break_down_id')];
	                $po_size_qty_arr[$order_array[$val[csf('po_break_down_id')]]['po_number']][$val[csf('color_number_id')]][$val[csf('size_number_id')]]['no_of_bundle'] ++;
	            }

				// echo "<pre>"; print_r($po_size_qty_arr);
				//die;
				unset($result);

                // ======================== get order qty ========================
                $sql = "SELECT d.po_break_down_id,d.color_number_id,d.size_number_id, d.order_quantity  AS qty FROM wo_po_color_size_breakdown d WHERE d.po_break_down_id IN ($order_id_cut)  and status_active=1";
                // echo $sql;die;
                $res = sql_select($sql);
                $order_qty_array = array();
                foreach ($res as $v)
                {
                    $order_qty_array[$v[csf('po_break_down_id')]][$v[csf('color_number_id')]][$v[csf('size_number_id')]] += $v[csf('qty')];
                }
                // echo "<pre>"; print_r($order_qty_array);die;
	        ?>
	    <div style="width:100%;">
	    <table cellspacing="0" width="980" border="1" rules="all" class="rpt_table" style="font: 12px tahoma;">
	        <thead bgcolor="#dddddd" align="center">
	            <th width="30">SL</th>
	            <th width="80" align="center">Buyer</th>
                <th width="80" align="center">Job</th>
	            <th width="80" align="center">Int. Ref.</th>
	            <th width="80" align="center">Style Ref</th>
	            <th width="100" align="center">Style Des</th>
	            <th width="80" align="center">Order No.</th>
	            <th width="120" align="center">Order Cut No</th>
	            <th width="80" align="center">Cutting Number</th>
	            <th width="80" align="center">Gmt. Item</th>
	            <th width="80" align="center">Country</th>
	            <th width="80" align="center">Color</th>
	            <th width="80" align="center">Gmt. Qty</th>
	            <? if($data[2]==3)  {  ?>
	            <th align="center">No of Bundle</th>
	            <? }   ?>
	        </thead>
	        <tbody>
	            <?
	          	//$size_qty_arr=array();
	            $i=1;
	            $tot_qnty=array();
	             foreach($rows as $buyer=>$brows)
	             {
	                 foreach($brows as $job=>$jrows)
	                 {
                        foreach($jrows as $intRef=>$intRefs)
                        {
    	                     foreach($intRefs as $styleref=>$srrows)
    	                     {
    	                         foreach($srrows as $styledes=>$sdrows)
    	                         {
    	                             foreach($sdrows as $order=>$orows)
    	                             {
    	                                 foreach($orows as $cutn=>$ctrows)
    	                                 {
    	                                    foreach($ctrows as $gmtitm=>$girows)
    	                                     {
    	                                        foreach($girows as $Country=>$cntrows)
    	                                         {
    	                                             foreach($cntrows as $color=>$cdata)
    	                                             {
    	                                                if ($i%2==0)
    	                                                    $bgcolor="#E9F3FF";
    	                                                else
    	                                                    $bgcolor="#FFFFFF";
    	                                                $color_count=count($cid);
    	                                                ?>
    	                                                <tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
    	                                                    <td><? echo $i;  ?></td>
    	                                                    <td align="center"><? echo $buyer; ?></td>
                                                            <td align="center"><? echo $job; ?></td>
    	                                                    <td align="center"><? echo $intRef; ?></td>
    	                                                    <td align="center"><? echo $styleref; ?></td>
    	                                                    <td align="center"><? echo $styledes; ?></td>
    	                                                    <td align="center"><? echo $order; ?></td>
    	                                                    <td align="center"><? echo $po_wise_ordcutno_arr[$cutn][$cdata['order_no']]; ?></td>
    	                                                    <td align="center"><? echo $cutn; ?></td>
    	                                                    <td align="center"><? echo $gmtitm; ?></td>
    	                                                    <td align="center"><? echo $Country; ?></td>
    	                                                    <td align="center"><? echo $color;?></td>
    	                                                    <td align="right"><?  echo $cdata['val']; ?></td>
    	                                                    <? if($data[2]==3)
    	                                                     {  ?>
    	                                                    <td  align="center"> <?  echo $cdata['count']; ?></td>
    	                                                    <?
    	                                                    $color_qty_arr[$color] += $cdata['val'];
    	                                                    $color_wise_bundle_no_arr[$color] += $cdata['count'];
    	                                                    }
    	                                                    ?>

    	                                                </tr>
    	                                                <?
    	                                                $production_quantity += $cdata['val'];
    	                                                $total_bundle += $cdata['count'];
    	                                                $i++;
    	                                             }
    	                                         }
    	                                     }
    	                                 }
    	                             }
    	                         }
                             }
	                     }
	                 }
	             }
	             ?>
	        </tbody>
	        <tr>
	            <td colspan="12" align="right"><strong>Grand Total </strong></td>
	            <td align="right"><?  echo $production_quantity; ?></td>
	            <td align="center"><?  echo $total_bundle; ?></td>
	        </tr>
	    </table>
		<br>
		<table cellspacing="0" border="1" rules="all" cellpadding="3" style="font: 12px tahoma;">
			<thead>
                <tr>
                    <td colspan="7"><strong>PO & Size Wise Summary</strong></td>
                </tr>
                <tr bgcolor="#dddddd" align="center">
                    <td>SL</td>
                    <td>PO No.</td>
                    <td>Size Qty</td>
                    <td>Size</td>
                    <td>No Of Bundle</td>
                    <td>Quantity (Pcs)</td>
                    <td>Balance</td>
                </tr>
            </thead>
            <tbody>
			<?
			$rowSpanPo = array();
			foreach($po_size_qty_arr as $poId=>$poArr)
			{
                foreach($poArr as $colorId=>$colorArr)
                {
    				foreach($colorArr as $sizeId=>$row)
    				{
    					$rowSpanPo[$poId]++;
    				}
                }
			}
			//echo "<pre>";
			//print_r($rowSpanPo);

			$i = 0;
			foreach($po_size_qty_arr as $poId=>$poArr)
			{
				$z = 1;
				$totalNoOfbundle = 0;
				$totalQty = 0;
				$totalOrderQty = 0;
				$totalBalanceQty = 0;

                foreach($poArr as $colorId=>$colorArr)
                {
    				foreach($colorArr as $sizeId=>$row)
    				{
    					$i++;
    					$bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";

    					//balance qty calculating
                        // echo $poId."=".$colorId."=".$sizeId."<br>";
                        $order_qty = $order_qty_array[$row['po_break_down_id']][$colorId][$sizeId];
    					$balanceQty =$order_qty - ($row['qty'] + $prev_qty_arr[$poId][$colorId][$sizeId]['qty']);
                        // echo$order_qty ."- (".$row['qty'] ."+". $prev_qty_arr[$poId][$sizeId]['qty'].")<br>";

    					if($z == 1)
    					{
    						$z++;
    						?>
                            <tr bgcolor="<? echo $bgcolor; ?>">
                                <td align="center"><? echo $i; ?></td>
                                <td rowspan="<? echo $rowSpanPo[$poId]; ?>" align="center"><? echo $poId; ?></td>
                                <td align="center"><? echo$order_qty; ?></td>
                                <td align="center"><? echo $size_library[$sizeId]; ?></td>
                                <td align="center"><? echo $row['no_of_bundle']; ?></td>
                                <td align="right"><? echo $row['qty']; ?></td>
                                <td align="right"><? echo $balanceQty; ?></td>
                            </tr>
                            <?
    					}
    					else
    					{
                            ?>
    						<tr bgcolor="<? echo $bgcolor; ?>">
                                <td align="center"><? echo $i; ?></td>
                                <td align="center"><? echo$order_qty; ?></td>
                                <td align="center"><? echo $size_library[$sizeId]; ?></td>
                                <td align="center"><? echo $row['no_of_bundle']; ?></td>
                                <td align="right"><? echo $row['qty']; ?></td>
                                <td align="right"><? echo $balanceQty; ?></td>
                            </tr>
                            <?
    					}
    					$totalNoOfbundle += $row['no_of_bundle'];
    					$totalQty += $row['qty'];
    					$totalOrderQty +=$order_qty;
    					$totalBalanceQty += $balanceQty;

    					$grandTotalOrderQty +=$order_qty;
    					$grandTotalBalanceQty += $balanceQty;
    				}
                }
				?>
                <tr>
                    <td colspan="2" align="right"><strong>Total </strong></td>
                    <td align="center"><strong><? echo $totalOrderQty; ?></strong></td>
                    <td align="center"></td>
                    <td align="center"><strong><? echo $totalNoOfbundle; ?></strong></td>
                    <td align="right"><strong><? echo $totalQty; ?></strong></td>
                    <td align="right"><strong><? //echo $totalBalanceQty; ?></strong></td>
                </tr>
                <?
			}

            /*foreach ($size_qty_arr as $size_id => $size_qty):
                $bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td align="center"><? echo $i; ?></td>
                    <td align="center"><? //echo $i; ?></td>
                    <td align="center"><? //echo $i; ?></td>
                    <td align="center"><? echo $size_library[$size_id]; ?></td>
                    <td align="center"><? echo $size_wise_bundle_no_arr[$size_id]; ?></td>
                    <td align="right"><? echo $size_qty; ?></td>
                    <td align="right"><? //echo $size_qty; ?></td>
                </tr>
                <?
                $i++;
            endforeach;*/ ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="7"></td>
            </tr>
            <tr>
                <td colspan="2" align="right"><strong>Grand Total</strong></td>
                <td align="center"><strong><? echo $grandTotalOrderQty; ?></strong></td>
                <td align="center"></td>
                <td align="center"><strong><? echo $total_bundle; ?></strong></td>
                <td align="right"><strong><? echo $production_quantity; ?></strong></td>
                <td align="right"><strong><? //echo $grandTotalBalanceQty; ?></strong></td>
            </tr>
            </tfoot>
        </table>
		 <?
			echo signature_table(226, $data[0], "950px","","",$user_library[$inserted_by]);
		 ?>
	</div>
    </div>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
	    function generateBarcode( valuess ){
	            var value = valuess;//$("#barcodeValue").val();
	            var btype = 'code39';//$("input[name=btype]:checked").val();
	            var renderer ='bmp';// $("input[name=renderer]:checked").val();
	            var settings = {
	              output:renderer,
	              bgColor: '#FFFFFF',
	              color: '#000000',
	              barWidth: 1,
	              barHeight: 30,
	              moduleSize:5,
	              posX: 10,
	              posY: 20,
	              addQuietZone: 1
	            };

	             value = {code:value, rect: false};
	            $("#barcode_img_id").show().barcode(value, btype, settings);
	        }
	        generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
	     </script>
	<?


    if($is_mail_send==1){

        $emailBody=ob_get_contents();
        $sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=120 and  b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
        $mail_sql_res=sql_select($sql);

        $mailArr=array();
        foreach($mail_sql_res as $row)
        {
            $mailArr[$row['EMAIL']]=$row['EMAIL'];
        }

        if($mail_id!=''){$mailArr[]=$mail_id;}


        $to=implode(',',$mailArr);
        $subject="Bundle Wise Delivery to Sewing Input Challan/Gate Pass";

        if($to!=""){
            include('../../auto_mail/setting/mail_setting.php');
            $header=mailHeader();
            echo sendMailMailer( $to, $subject, $mail_body."<br><br>".$emailBody,$from_mail,'' );
        }
    }
	exit();
}
if ($action == "emblishment_issue_print_4")
{
    extract($_REQUEST);
    $data = explode('*', $data);
    $cbo_company_name = $data[0]; $is_mail_send = $data[5]; $mail_id = $data[6]; $mail_body = $data[7];if( $is_mail_send == 1){ob_start();}
    //print_r ($data);
    $company_library = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    $job_no = array();
    $job_id = array();
    $country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
    $supplier_library = return_library_array("select id,supplier_name from  lib_supplier where status_active=1 and is_deleted=0", "id", "supplier_name");
    $location_library = return_library_array("select id, location_name from  lib_location where status_active=1 and is_deleted=0", "id", "location_name");
    $floor_library = return_library_array("select id, floor_name from  lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
    $color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
    $size_library = return_library_array("select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0 ", 'id', 'buyer_name');
    $line_library = return_library_array("select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0", "id", "line_name");

    $sql = "SELECT id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
    production_source, serving_company, floor_id,sewing_line, organic, delivery_date,working_company_id,working_location_id from pro_gmts_delivery_mst where production_type=4 and id='$data[1]' and
    status_active=1 and is_deleted=0 ";

    //echo $sql;

    $dataArray = sql_select($sql);

    $nameArray = sql_select("SELECT id, auto_update from variable_settings_production where company_name='" . $dataArray[0][csf('serving_company')] . "' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];

    if ($prod_reso_allocation == 1)
     {

        if ($dataArray[0][csf('floor_id')] == 0 && $dataArray[0][csf('location_id')] != 0) $cond = " and a.location_id= {$dataArray[0][csf('location_id')]}";
        if ($dataArray[0][csf('floor_id')] != 0) $cond = " and a.floor_id= {$dataArray[0][csf('floor_id')]}";

        if ($db_type == 0) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
        } else if ($db_type == 2 || $db_type == 1) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
        }

        foreach ($line_data as $row) {
            $line = '';
            $line_number = explode(",", $row[csf('line_number')]);
            foreach ($line_number as $val) {
                if ($line == '') $line = $line_library[$val]; else $line .= "," . $line_library[$val];
            }
            $line_array[$row[csf('id')]] = $line;
        }
        $line = $line_array[$dataArray[0][csf('sewing_line')]];
    }
    else
     {
        $line = $line_library[$dataArray[0][csf('sewing_line')]];
     }
    unset($line_data);
    //$page_id_arr=[1,2,3,4];
    $page_id_arr=[1,2,3];
    $copy_name=array(1=>'st',2=>'nd',3=>'rd',4=>'th');

    $kk=0;
    foreach($page_id_arr as $vals)
    {
        $production_quantity  =0;
        $total_bundle  = 0;
        $size_qty_arr=array();
        $size_wise_bundle_no_arr=array();
        ?>
        <style type="text/css">
            @media print
            {
                #footer_id {page-break-after: always;}
            }
        </style>


            <div style="width:930px;">
                <table cellspacing="0" style="font: 11px tahoma; width: 100%;">
                    <tr>
                        <td colspan="5" align="center" style="font-size:24px">
                            <strong style="margin-left: 300px !important;"><? echo $company_library[$data[0]]; ?></strong>

                            </td>
                            <td><span style="font-size:x-large;  "><? echo $vals;?><sup><? echo $copy_name[$vals]; ?></sup> Copy</span></td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="6" align="center" style="font-size:14px">
                            <?

                            $nameArray = sql_select("SELECT plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
                            foreach ($nameArray as $result) {

                                echo $result[csf('city')];

                            }
                            unset($nameArray);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" align="center" style="font-size:20px"><u><strong>Bundle Wise Delivery to Sewing Input Challan/Gate Pass</strong></u></td>
                    </tr>
                    <tr>
                        <td width="95"><strong>Challan No</strong></td>
                        <td width="150"><? echo ": ".$dataArray[0][csf('sys_number')]; ?></td>
                        <td width="80"><strong>Source</strong></td>
                        <td width="190"><? echo ": ".$knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
                        <td width="120"><strong>Sew. Company</strong></td>
                        <td>
                            <?
                            if ($dataArray[0][csf('production_source')] == 1) echo ": ".$company_library[$dataArray[0][csf('serving_company')]];
                            else echo ": ".$supplier_library[$dataArray[0][csf('serving_company')]];

                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Location </strong></td>
                        <td><? echo ": ".$location_library[$dataArray[0][csf('location_id')]]; ?></td>
                        <td><strong>Floor  </strong></td>
                        <td><? echo ": ".$floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
                        <td><strong>Line </strong></td>
                        <td><? echo ": ".$line; ?></td>
                    </tr>
                    <tr>
                    <td><strong>Working Company  </strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
                    <td><strong>Working Location </strong></td><td>: <? echo $location_library[$dataArray[0][csf('working_location_id')]]; ?></td>
                    <td><strong>Input Date  </strong></td>
                     <td><? echo ": ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
                    </tr>
                    <tr>

                        <td>Remarks</td>
                        <td colspan="3"><? //echo $dataArray[0][csf('sewing_line')];
                            ?></td>
                    </tr>


                    <tr>
                        <td colspan="6" id="barcode_img_id_<? echo $kk;?>"></td>
                    </tr>

                </table>
                <br>
                <?

                $delivery_mst_id = $dataArray[0][csf('id')];

                if ($data[2] == 3) {

                    $sql = "SELECT sum(b.production_qnty) as production_qnty,a.po_break_down_id,a.item_number_id,a.country_id,b.cut_no,b.bundle_no,d.size_number_id, d.color_number_id,
                    count(b.id) as  num_of_bundle

                    from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d
                    where a.delivery_mst_id ='$data[1]'
                    and a.id=b.mst_id and b.production_type=4 and b.color_size_break_down_id=d.id and  b.status_active=1 and b.is_deleted=0 and d.status_active=1
                    and d.is_deleted=0
                    group by a.po_break_down_id,a.item_number_id,a.country_id,b.cut_no,b.bundle_no,d.size_number_id,d.color_number_id
                    order by  length(b.bundle_no) asc, b.bundle_no  asc";//a.po_break_down_id,d.color_number_id
                }
                 else {
                    $sql = "SELECT sum(a.production_qnty) as production_qnty,c.po_break_down_id,c.item_number_id,c.country_id,a.cut_no,a.bundle_no,b.size_number_id, b.color_number_id

                    from pro_garments_production_mst c, pro_garments_production_dtls a, wo_po_color_size_breakdown b
                    where c.delivery_mst_id ='$data[1]'
                    and c.id=a.mst_id and a.color_size_break_down_id=b.id and a.production_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
                    group by c.po_break_down_id,c.item_number_id,c.country_id,a.cut_no,a.bundle_no,b.size_number_id,b.color_number_id order by  length(a.bundle_no) asc, a.bundle_no asc";
                }


               // echo $sql;
                //die;

                $result = sql_select($sql);
                $all_cut_arr=array();
                $all_po_arr=array();

                foreach($result as $v)
                {
                    $all_cut_arr[$v[csf("cut_no")]]=$v[csf("cut_no")];
                    $all_po_arr[$v[csf("po_break_down_id")]]=$v[csf("po_break_down_id")];
                }
                $all_cut_nos="'".implode("','", $all_cut_arr)."'";
                $all_po_nos="'".implode("','", $all_po_arr)."'";
                $ppl_mst_sql=sql_select("SELECT cutting_no,batch_id from ppl_cut_lay_mst where status_active=1 and cutting_no in($all_cut_nos) ");
                $ppl_mst_arr=array();
                foreach($ppl_mst_sql as $p_val)
                {
                    $ppl_mst_arr[$p_val[csf("cutting_no")]]=$p_val[csf("batch_id")];
                }

                $order_array = array();
                $order_sql = "SELECT a.id as job_id, a.job_no, a.buyer_name,a.style_ref_no, a.style_description,b.id, b.po_number, b.po_quantity,b.file_no,b.grouping,c.country_id from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in($all_po_nos)";
                $order_sql_result = sql_select($order_sql);
                foreach ($order_sql_result as $row)
                {
                    $job_id[$row[csf('id')]] = $row[csf('job_id')];
                    $job_no[$row[csf('id')]] = $row[csf('job_no')];
                    $order_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
                    $order_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
                    $order_array[$row[csf('id')]]['po_number'] = $row[csf('po_number')];
                    $order_array[$row[csf('id')]]['po_quantity'] = $row[csf('po_quantity')];
                    $order_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
                    $order_array[$row[csf('id')]]['style_des'] = $row[csf('style_description')];
                    $order_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
                    $order_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
                    $order_array[$row[csf('id')]]['country_id'] = $row[csf('country_id')];
                }
                unset($order_sql_result);


                $sql_cut="SELECT bundle_no, number_start as number_start, number_end as number_end  from ppl_cut_lay_bundle where status_active=1 and is_deleted=0 and order_id in($all_po_nos)";
                $sql_cut_res = sql_select($sql_cut); $number_arr=array();
                foreach($sql_cut_res as $row)
                {
                    $number_arr[$row[csf('bundle_no')]]['number_start']=$row[csf('number_start')];
                    $number_arr[$row[csf('bundle_no')]]['number_end']=$row[csf('number_end')];
                }
                unset($sql_cut_res);


                ?>

                <div style="width:100%;">
                    <table cellspacing="0" width="1240" border="1" rules="all" class="rpt_table" style="font: 12px tahoma;">
                        <thead bgcolor="#dddddd" align="center">
                        <th width="30">SL</th>
                        <th width="60" align="center">Bundle No</th>
                        <th width="60" align="center">Job</th>
                        <th width="80" align="center">Buyer</th>
                        <th width="80" align="center">File No</th>
                        <th width="80" align="center">Int. Ref.</th>
                        <th width="80" align="center">Country</th>
                        <th width="80" align="center">Style Ref</th>
                        <th width="100" align="center">Style Des</th>
                        <th width="80" align="center">Order No.</th>
                        <th width="80" align="center">Gmt. Item</th>
                        <th width="80" align="center">Color</th>
                        <th width="100" align="center">Batch</th>
                        <th width="80" align="center">Size</th>
                        <th width="60" align="center">Reject Qty</th>
                        <th width="60" align="center">RMG Qty</th>
                        <th width="60" align="center">Gmt. Qty</th>
                        </thead>
                        <tbody>
                        <?
                        $i = 1;
                        $size_wise_bundle_no = 0;
                        $num_of_bundle = 0;
                        $tot_qnty = array();
                        foreach ($result as $val)
                        {


                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";
                            $color_count = count($cid);
                            $number_start=''; $number_end='';
                            $number_start=$number_arr[$val[csf('bundle_no')]]['number_start'];
                            $number_end=$number_arr[$val[csf('bundle_no')]]['number_end'];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>">
                                <td><? echo $i; ?></td>
                                <td align="center"><? echo $val[csf('bundle_no')]; ?></td>
                                <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['job_no']; ?></td>
                                <td align="center"><? echo $buyer_arr[$order_array[$val[csf('po_break_down_id')]]['buyer_name']]; ?></td>
                                <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['file_no']; ?></td>
                                <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['grouping']; ?></td>
                                <td align="center"><? echo $country_arr[$val[csf('country_id')]]; ?></td>
                                <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['style_ref']; ?></td>
                                <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['style_des']; ?></td>
                                <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['po_number']; ?></td>
                                <td align="center"><? echo $garments_item[$val[csf('item_number_id')]]; ?></td>
                                <td align="center"><? echo $color_library[$val[csf('color_number_id')]]; ?></td>
                                <td align="center"><? echo  $ppl_mst_arr[$val[csf("cut_no")]]; ?></td>
                                <td align="center"><? echo $size_library[$val[csf('size_number_id')]]; ?></td>
                                <td></td>
                                <td align="center"><? echo $number_start.'-'.$number_end; ?></td>
                                <td align="right"><? echo $val[csf('production_qnty')]; ?></td>



                            </tr>
                            <?
                            $production_quantity += $val[csf('production_qnty')];
                            $total_bundle += $val[csf('num_of_bundle')];
                            $size_qty_arr[$val[csf('size_number_id')]] += $val[csf('production_qnty')];
                            $size_wise_bundle_no_arr[$val[csf('size_number_id')]] += $val[csf('num_of_bundle')];
                            $i++;

                        }
                        ?>
                        </tbody>
                        <tr>
                            <td colspan="13"></td>
                        </tr>
                        <tr>
                            <td colspan="3"><strong>No. Of Bundle :<? echo $total_bundle; ?></strong></td>
                            <td colspan="13" align="right"><strong>Grand Total </strong></td>
                            <td align="right"><? echo $production_quantity; ?></td>
                        </tr>
                    </table>

                    <br>
                    <table cellspacing="0" border="1" rules="all" cellpadding="3" style="font: 12px tahoma;">
                        <thead>
                        <tr>
                            <td colspan="4"><strong>Size Wise Summary</strong></td>
                        </tr>
                        <tr bgcolor="#dddddd" align="center">
                            <th>SL</th>
                            <th>Size</th>
                            <th>No Of Bundle</th>
                            <th>Quantity (Pcs)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $i = 1;
                        foreach ($size_qty_arr as $size_id => $size_qty):
                            $bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>">
                                <td align="center"><? echo $i; ?></td>
                                <td align="center"><? echo $size_library[$size_id]; ?></td>
                                <td align="center"><? echo $size_wise_bundle_no_arr[$size_id]; ?></td>
                                <td align="right"><? echo $size_qty; ?></td>
                            </tr>
                            <?
                            $i++;
                        endforeach; ?>
                        </tbody>
                        <tr>
                            <td colspan="4" style="padding: 1px !important;"></td>
                        </tr>
                        <tr>
                            <td colspan="2" align="right"><strong>Total </strong></td>
                            <td align="center"><? echo $total_bundle; ?></td>
                            <td align="right"><? echo $production_quantity; ?></td>
                        </tr>
                    </table>
                    <br>
                    <?
                    $job_no_implode = "'".implode("','", $job_no)."'";
                    $job_id_implode = "'".implode("','", $job_id)."'";
                    $all_po_id = "'".implode("','", $all_po_arr)."'";
                     // echo $all_po_id;
                    if($data[4]==1)
                    {
                        $sql="SELECT A.ID,A.JOB_NO,A.JOB_ID,A.TRIM_GROUP,B.ITEM_NAME,C.PO_NUMBER,SUM(C.PO_QUANTITY) AS PO_QUANTITY,A.DESCRIPTION,A.CONS_UOM,A.CONS_DZN_GMTS,E.CONS_PCS,A.REMARK
                        FROM wo_pre_cost_trim_cost_dtls a,lib_item_group b,wo_po_break_down c,wo_po_details_master d,wo_pre_cost_trim_co_cons_dtls e
                        WHERE     b.id = a.trim_group
                        AND c.job_no_mst = a.job_no
                        AND d.id = c.job_id
                        AND d.id = e.job_id
                        And a.id=e. WO_PRE_COST_TRIM_COST_DTLS_ID
                        And c.id=e.PO_BREAK_DOWN_ID
                        AND b.trim_type = 1
                        AND c.id IN  ($all_po_id)
                        And a.STATUS_ACTIVE=1 and a.IS_DELETED=0
                        And b.STATUS_ACTIVE=1 and b.IS_DELETED=0
                        And c.STATUS_ACTIVE=1 and c.IS_DELETED=0
                        And d.STATUS_ACTIVE=1 and d.IS_DELETED=0
                        And e.STATUS_ACTIVE=1 and e.IS_DELETED=0
                        Group By A.ID,A.JOB_NO,A.JOB_ID,A.TRIM_GROUP,B.ITEM_NAME,C.PO_NUMBER,A.DESCRIPTION,A.CONS_UOM,A.CONS_DZN_GMTS,E.CONS_PCS,A.REMARK
                        ORDER BY a.id ASC";
                        // echo $sql;
                        //$sqlTrimData = sql_select("select a.id, a.job_no, a.job_id, a.trim_group, b.item_name, c.po_number, c.po_quantity, a.description, a.cons_uom, a.cons_dzn_gmts, a.remark, d.total_set_qnty, d.order_uom from wo_pre_cost_trim_cost_dtls a, lib_item_group b, wo_po_break_down c, wo_po_details_master d where b.id = a.trim_group and c.job_no_mst = a.job_no and d.id = c.job_id and b.trim_type = 1 and c.id in ($all_po_id) order  by a.id asc");
                        $sqlTrimData = sql_select($sql);
                        if(count($sqlTrimData) > 0){
                        ?>
                            <table cellspacing="0" border="1" rules="all" cellpadding="3" style="font: 12px tahoma;" width="1240">
                                <thead>
                                    <tr>
                                        <th colspan="9" align="left"><strong>Required Sewing Trims</strong></th>
                                    </tr>
                                    <tr bgcolor="#dddddd" align="center">
                                        <th width="30">SL</th>
                                        <th width="140">Item Name</th>
                                        <th width="200">Description</th>
                                        <th width="120">Order No.</th>
                                        <th width="120">Wo No.</th>
                                        <th width="100">Budget Cons/DZN</th>
                                        <th width="70">UOM</th>
                                        <th width="100">Required Qty.</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?
                                    $sqlGetBookingData = sql_select("select pre_cost_fabric_cost_dtls_id, booking_no, wo_qnty from wo_booking_dtls where po_break_down_id in ($all_po_id) and status_active = 1  and is_deleted = 0");
                                    $bookingArr = array();
                                    foreach ($sqlGetBookingData as $key => $booking){
                                        $bookingArr[$booking[csf('pre_cost_fabric_cost_dtls_id')]]['booking_no'][$key] = $booking[csf('booking_no')];
                                        $bookingArr[$booking[csf('pre_cost_fabric_cost_dtls_id')]]['booking_qty'][$key] = $booking[csf('wo_qnty')];
                                    }
                                    $countArr = array();
                                    foreach ($sqlTrimData as $key => $preTrim)
                                    {
                                        if($preTrim[csf('cons_pcs')]>0)
                                        {
                                            $keyMod = $preTrim[csf('item_name')]."*".$preTrim[csf('trim_group')]."*".$preTrim[csf('description')]."*".$preTrim[csf('cons_uom')];
                                            $countArr[$keyMod]++;
                                        }
                                    }
                                    // echo "<pre>"; print_r($countArr);
                                    $mainDataArr = array();
                                    $totalQty = 0;
                                    $totalcons_dzn = 0;
                                    foreach ($sqlTrimData as $key => $preTrim){
                                        $keyMod = $preTrim[csf('item_name')]."*".$preTrim[csf('trim_group')]."*".$preTrim[csf('description')]."*".$preTrim[csf('cons_uom')];
                                        $mainDataArr[$keyMod]['item_name'] = $preTrim[csf('item_name')];
                                        $mainDataArr[$keyMod]['description'] = $preTrim[csf('description')];
                                        $mainDataArr[$keyMod]['uom'] =  $unit_of_measurement[$preTrim[csf('cons_uom')]];
                                        $mainDataArr[$keyMod]['cons_dzn'] += $preTrim[csf('cons_pcs')];
                                        $mainDataArr[$keyMod]['remarks'] = $preTrim[csf('remark')];
                                        $mainDataArr[$keyMod]['order_no'] = $preTrim[csf('po_number')];
                                        $mainDataArr[$keyMod]['po_qty'] = $preTrim[csf('po_quantity')];
                                        $mainDataArr[$keyMod]['set_qty'] = ($preTrim[csf('order_uom')] == 58 ? $preTrim[csf('total_set_qnty')] : 1);
                                        $mainDataArr[$keyMod]['booking_no'] = implode(', ',array_unique($bookingArr[$preTrim[csf('id')]]['booking_no']));
                                        $mainDataArr[$keyMod]['booking_qty'] = array_sum($bookingArr[$preTrim[csf('id')]]['booking_qty']);
                                    }
                                    $count = 1;
                                    foreach ($mainDataArr as $key=>$val){
                                        if ($count % 2 == 0)
                                            $bgcolor = "#E9F3FF";
                                        else
                                            $bgcolor = "#FFFFFF";

                                        $cons_dzn=number_format(($val['cons_dzn']/$countArr[$key]), '2', '.', '');
                                        $reqQty =($cons_dzn / 12) * $production_quantity;
                                    ?>
                                        <tr bgcolor="<?=$bgcolor?>">
                                            <td align="center"><?=$count;?></td>
                                            <td><?=$val['item_name'];?></td>
                                            <td><?=$val['description'];?></td>
                                            <td align="center"><?=$val['order_no'];?></td>
                                            <td align="center" ><?=$val['booking_no'];?></td>
                                            <td align="right"><?=$cons_dzn;?></td>
                                            <td align="center"><?=$val['uom'];?></td>
                                            <td align="right"><?=number_format($reqQty, 2);?></td>
                                            <td><?=$val['remarks']?></td>
                                        </tr>
                                    <?
                                        $totalQty += $reqQty;
                                        $totalcons_dzn += $cons_dzn;
                                        $count++;
                                    }
                                    ?>
                                </tbody>
                                <tr>
                                    <td colspan="9" style="padding: 1px !important;"></td>
                                </tr>
                                <tr>
                                    <td align="right" colspan="7"><strong>Total</strong></td>
                                    <td align="right"><?=number_format($totalQty, 2)?></td>
                                    <td></td>
                                </tr>

                            </table>
                            <br>
                        <?
                        }
                    }
                    echo signature_table(28, $data[0], "900px");
                    ?>
                </div>
            </div>
            <div id="footer_id"></div>

        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
            function generateBarcode(valuess) {
                var ids='<? echo $kk;?>';
                var value = valuess;//$("#barcodeValue").val();
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

                value = {code: value, rect: false};
                $("#barcode_img_id_"+ids).show().barcode(value, btype, settings);
            }
            generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
        </script>
        <?
        $kk++;
    }


    if($is_mail_send==1){
        $emailBody=ob_get_contents();

        $sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=120 and  b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
        $mail_sql_res=sql_select($sql);

        $mailArr=array();
        foreach($mail_sql_res as $row)
        {
            $mailArr[$row['EMAIL']]=$row['EMAIL'];
        }

        if($mail_id!=''){$mailArr[]=$mail_id;}


        $to=implode(',',$mailArr);
        $subject="Bundle Wise Delivery to Sewing Input Challan/Gate Pass";

        if($to!=""){
            include('../../auto_mail/setting/mail_setting.php');
            $header=mailHeader();
            echo sendMailMailer( $to, $subject, $mail_body."<br><br>".$emailBody,$from_mail,'' );
        }
    }

    exit();
}
if ($action == "sewing_input_issue_print_5")
{
    extract($_REQUEST);
    $data = explode('*', $data);
    $cbo_company_name = $data[0];  $is_mail_send = $data[5]; $mail_id = $data[6]; $mail_body = $data[7];if( $is_mail_send == 1){ob_start();}
    //print_r ($data);
    $company_library = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    $job_no = array();
    $job_id = array();
    $country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
    $supplier_library = return_library_array("select id,supplier_name from  lib_supplier where status_active=1 and is_deleted=0", "id", "supplier_name");
    $location_library = return_library_array("select id, location_name from  lib_location where status_active=1 and is_deleted=0", "id", "location_name");
    $floor_library = return_library_array("select id, floor_name from  lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
    $color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
    $size_library = return_library_array("select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0 ", 'id', 'buyer_name');
    $line_library = return_library_array("select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0", "id", "line_name");

    $sql = "SELECT id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
    production_source, serving_company, floor_id,sewing_line, organic, delivery_date,working_company_id,working_location_id from pro_gmts_delivery_mst where production_type=4 and id='$data[1]' and
    status_active=1 and is_deleted=0 ";

    // echo $sql;
    $dataArray = sql_select($sql);

    $nameArray = sql_select("SELECT id, auto_update from variable_settings_production where company_name='" . $dataArray[0][csf('serving_company')] . "' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];

    if ($prod_reso_allocation == 1)
    {
        if ($dataArray[0][csf('floor_id')] == 0 && $dataArray[0][csf('location_id')] != 0) $cond = " and a.location_id= {$dataArray[0][csf('location_id')]}";
        if ($dataArray[0][csf('floor_id')] != 0) $cond = " and a.floor_id= {$dataArray[0][csf('floor_id')]}";

        if ($db_type == 0) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
        } else if ($db_type == 2 || $db_type == 1) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
        }

        foreach ($line_data as $row) {
            $line = '';
            $line_number = explode(",", $row[csf('line_number')]);
            foreach ($line_number as $val) {
                if ($line == '') $line = $line_library[$val]; else $line .= "," . $line_library[$val];
            }
            $line_array[$row[csf('id')]] = $line;
        }
        $line = $line_array[$dataArray[0][csf('sewing_line')]];
    }
    else
    {
        $line = $line_library[$dataArray[0][csf('sewing_line')]];
    }
    unset($line_data);
    //$page_id_arr=[1,2,3,4];
    // $page_id_arr=[1,2,3];
    // $copy_name=array(1=>'st',2=>'nd',3=>'rd',4=>'th');

    // $kk=0;
    // foreach($page_id_arr as $vals)
    // {
        $production_quantity  =0;
        $total_bundle  = 0;
        $size_qty_arr=array();
        $size_wise_bundle_no_arr=array();
        ?>
        <style type="text/css">
            @media print
            {
                #footer_id {page-break-after: always;}
            }
        </style>

        <div style="width:930px;">
            <table cellspacing="0" style="font: 11px tahoma; width: 100%;">
                <tr>
                    <td colspan="5" align="center" style="font-size:24px">
                        <strong style="margin-left: 300px !important;"><? echo $company_library[$data[0]]; ?></strong>
                    </td>
                    <!-- <td><span style="font-size:x-large;  "><? echo $vals;?><sup><? echo $copy_name[$vals]; ?></sup> Copy</span></td> -->
                </tr>
                <tr class="form_caption">
                    <td colspan="6" align="center" style="font-size:14px">
                        <?
                        $nameArray = sql_select("SELECT plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
                        foreach ($nameArray as $result) {
                            echo $result[csf('city')];
                        }
                        unset($nameArray);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" align="center" style="font-size:20px"><u><strong> Sewing Input Challan/Gate Pass</strong></u></td>
                </tr>
                <tr>
                    <td width="95"><strong>Challan No </strong></td>
                    <td width="150"><? echo ": ".$dataArray[0][csf('sys_number')]; ?></td>
                    <td  align="right"><strong>Input Date :  </strong></td>
                    <td><? echo ": ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
                </tr>
                <br>
                <tr>
                    <td ><strong>Sewing Source</strong></td>
                    <td ><? echo ": ".$knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
                    <td width="120"><strong>Sewing Company</strong></td>
                    <td>
                    <?
                        if ($dataArray[0][csf('production_source')] == 1) echo ": ".$company_library[$dataArray[0][csf('serving_company')]];
                        else echo ": ".$supplier_library[$dataArray[0][csf('serving_company')]];
                    ?>
                    </td>
                    <td width="80"><strong>Location </strong></td>
                    <td><? echo ": ".$location_library[$dataArray[0][csf('location_id')]]; ?></td>
                </tr>
                <tr>
                    <td colspan="6" id="barcode_img_id_<? echo $kk;?>"></td>
                </tr>
            </table>
            <br>
            <?
            $delivery_mst_id = $dataArray[0][csf('id')];

            $sql = "SELECT b.id as col_size_id, b.job_no_mst as Job_No,sum(a.production_qnty) as production_qnty,c.po_break_down_id,c.item_number_id,c.country_id,a.cut_no,a.bundle_no,b.size_number_id, b.color_number_id,e.buyer_name,e.style_ref_no,e.style_description,f.po_number,sum(b.order_quantity) as order_quantity,count(a.id) as  num_of_bundle, b.article_number as article_number

            from pro_garments_production_mst c, pro_garments_production_dtls a, wo_po_color_size_breakdown b,wo_po_details_master e,wo_po_break_down f
            where c.delivery_mst_id ='$data[1]'
            and c.id=a.mst_id and b.job_id=e.id and e.id=f.job_id and a.color_size_break_down_id=b.id and f.id=b.po_break_down_id and a.production_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
            group by a.id, b.id,c.po_break_down_id,c.item_number_id,c.country_id,a.cut_no,a.bundle_no,b.size_number_id,b.color_number_id,b.job_no_mst,e.buyer_name,e.style_ref_no,e.style_description,f.po_number, b.article_number order by  length(a.bundle_no) asc, a.bundle_no asc";

            // echo $sql;die;
            $result = sql_select($sql);
            $all_cut_arr=array();
            $all_po_arr=array();
            $bundle_cut_arr=array();
            $master_data_arr=array();
            $total_no_bundle_arr=array();
            $total_no_quantity_arr=array();

            foreach($result as $v)
            {
                $master_data_arr[$v[csf('job_no')]]['buyer_name']=$v[csf('buyer_name')];
                $master_data_arr[$v[csf('job_no')]]['item_number_id']=$v[csf('item_number_id')];
                $master_data_arr[$v[csf('job_no')]]['country_id']=$v[csf('country_id')];
                $master_data_arr[$v[csf('job_no')]]['color_number_id']=$v[csf('color_number_id')];
                $master_data_arr[$v[csf('job_no')]]['style_ref']=$v[csf('style_ref_no')];
                $master_data_arr[$v[csf('job_no')]]['style_des']=$v[csf('style_description')];
                $master_data_arr[$v[csf('job_no')]]['po_number'].=$v[csf('po_number')].",";
                // $master_data_arr[$v[csf('job_no')]]['article_number'].=$v[csf('article_number')];
                $master_data_arr[$v[csf('job_no')]]['cut_no'].=$v[csf('cut_no')].",";

                $total_no_bundle_arr[$v[csf("po_break_down_id")]][$v[csf('color_number_id')]][$v[csf('size_number_id')]]++;

                $total_no_quantity_arr[$v[csf("po_break_down_id")]][$v[csf('color_number_id')]][$v[csf('size_number_id')]]+=$v[csf('production_qnty')];

                $all_cut_arr[$v[csf("cut_no")]]=$v[csf("cut_no")];

                $all_po_arr[$v[csf("po_break_down_id")]]=$v[csf("po_break_down_id")];
                $bundle_cut_arr[$v[csf("bundle_no")]]=$v[csf("bundle_no")];
            }
            // echo "<pre>";print_r($master_data_arr);die;
            // print_r($total_no_quantity_arr);
            // echo "<pre>"; print_r($total_no_quantity_arr); die;

            $all_cut_nos="'".implode("','", $all_cut_arr)."'";
            $all_po_nos="'".implode("','", $all_po_arr)."'";
            $bundle_cut_nos="'".implode("','", $bundle_cut_arr)."'";
            $ppl_mst_sql=sql_select("SELECT cutting_no,batch_id from ppl_cut_lay_mst where status_active=1 and cutting_no in($all_cut_nos) ");
            $ppl_mst_arr=array();
            foreach($ppl_mst_sql as $p_val)
            {
                $ppl_mst_arr[$p_val[csf("cutting_no")]]=$p_val[csf("batch_id")];
            }

            $order_array = array();
            $order_sql = "SELECT a.id as job_id, a.job_no, a.buyer_name,a.style_ref_no, a.style_description,b.id, b.po_number, b.po_quantity,b.file_no,b.grouping,c.country_id from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in($all_po_nos)";
            $order_sql_result = sql_select($order_sql);
            foreach ($order_sql_result as $row)
            {
                $job_id[$row[csf('id')]] = $row[csf('job_id')];
                $job_no[$row[csf('id')]] = $row[csf('job_no')];
                $order_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
                $order_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
                $order_array[$row[csf('id')]]['po_number'] = $row[csf('po_number')];
                $order_array[$row[csf('id')]]['po_quantity'] = $row[csf('po_quantity')];
                $order_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
                $order_array[$row[csf('id')]]['style_des'] = $row[csf('style_description')];
                $order_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
                $order_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
                $order_array[$row[csf('id')]]['country_id'] = $row[csf('country_id')];
            }
            unset($order_sql_result);

            $sql_cut="SELECT bundle_no, a.country_id,a.size_id,a.order_id,b.color_id,b.gmt_item_id,b.order_cut_no,c.job_no,number_start as number_start, number_end as number_end  from ppl_cut_lay_bundle a,PPL_CUT_LAY_DTLS b,ppl_cut_lay_mst c where c.id=b.mst_id and a.dtls_id=b.id and a.status_active=1 and a.is_deleted=0 and a.order_id in($all_po_nos)";
            //   echo $sql_cut;
            $sql_cut_res = sql_select($sql_cut); $number_arr=array(); $order_cut_no_arr=array();
            foreach($sql_cut_res as $row)
            {
                //    $order_cut_no_arr[$row[csf('country_id')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('gmt_item_id')]]['order_cut_no']=$row[csf('order_cut_no')];
                $order_cut_no[$row[csf('job_no')]]['order_cut_no']=$row[csf('order_cut_no')];
                $number_arr[$row[csf('bundle_no')]]['number_start']=$row[csf('number_start')];
                $number_arr[$row[csf('bundle_no')]]['number_end']=$row[csf('number_end')];
            }

            // echo $sql_cut; die;
            // print_r($order_cut_no_arr);echo "sdfsgdg";
            unset($sql_cut_res);

            $sql = "SELECT b.job_no_mst as job_no,a.production_qnty,b.size_number_id, b.color_number_id,c.po_break_down_id

            from pro_garments_production_mst c, pro_garments_production_dtls a, wo_po_color_size_breakdown b
            where c.po_break_down_id in($all_po_nos)
            and c.id=a.mst_id and a.color_size_break_down_id=b.id and a.production_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
            // echo $sql;die;

            $sql_data_cut=sql_select($sql);
            $cutting_data_arr=array();
            foreach($sql_data_cut as $row)
            {
                $cutting_data_arr[$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['total_cut']+=$row['PRODUCTION_QNTY'];
            }
            ?>

            <div style="width:100%;">
                <table cellspacing="0" width="910" border="1" rules="all" class="rpt_table" style="font: 12px tahoma;">
                    <thead bgcolor="#dddddd" align="center">
                        <th width="30">SL</th>
                        <th width="60" align="center">Buyer</th>
                        <th width="60" align="center">Job No</th>
                        <th width="80" align="center">Style Ref</th>
                        <th width="80" align="center">Style Des</th>
                        <th width="80" align="center">PO Number</th>
                        <th width="80" align="center">Garments Item</th>
                        <th width="80" align="center">Country</th>
                        <th width="80" align="center">Color</th>
                        <th width="80" align="center">Cutting No</th>
                        <th width="100" align="center">Order Cut</th>
                    </thead>
                    <tbody>
                    <?
                    $i = 1;
                    $size_wise_bundle_no = 0;
                    $num_of_bundle = 0;
                    $tot_qnty = array();
                    $col_size_id_chk = array();
                    $color_size_qty_arr=array();

                    foreach ($master_data_arr as $job_no =>$v)
                    {
                        if ($i % 2 == 0) $bgcolor = "#E9F3FF";
                        else $bgcolor = "#FFFFFF";
                        $color_count = count($cid);
                        $number_start=''; $number_end='';

                        $number_start=$number_arr[$val[csf('bundle_no')]]['number_start'];
                        $number_end=$number_arr[$val[csf('bundle_no')]]['number_end'];
                        $po_no = implode(",",array_unique(array_filter(explode(",", $v['po_number']))));
                        $cut_no = implode(",",array_unique(array_filter(explode(",", $v['cut_no']))));
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>">
                            <td><? echo $i; ?></td>
                            <td align="center"><? echo $buyer_arr[$v['buyer_name']]; ?></td>
                            <td align="center"><? echo $job_no; ?></td>
                            <td align="center"><? echo $v['style_ref'] ; ?></td>
                            <td align="center"><? echo $v['style_des']; ?></td>
                            <td align="center"><? echo $po_no; ?></td>
                            <td align="center"><? echo $garments_item[$v['item_number_id']];?></td>
                            <td align="center"><? echo $country_arr[$v['country_id']]; ?></td>
                            <td align="center"><? echo $color_library[$v['color_number_id']]; ?></td>
                            <td align="center"><? echo $cut_no; ?> </td>
                            <td align="center"><? echo  $order_cut_no[$row[csf('job_no')]]['order_cut_no']; ?></td>
                        </tr>
                        <?
                        $i++;
                        //  print_r($size_wise_bundle_no_arr);
                        // print_r($color_size_qty_arr);
                        //  print_r($total_no_bundle_arr);
                    }
                    ?>
                    </tbody>
                    <!-- <tfoot>
                        <tr>
                            <th colspan="3"><strong>No. Of Bundle :<? //echo $total_bundle; ?></strong></th>
                            <th colspan="8" align="right"><strong>Grand Total </strong></th>
                            <th align="right"><? //echo $production_quantity; ?></th>
                        </tr>
                    </tfoot> -->
                </table>
                <?
                    foreach($result as $row)
                    {
                        $production_quantity += $row[csf('production_qnty')];
                        $total_bundle += $row[csf('num_of_bundle')];
                        $size_qty_arr[$row[csf('size_number_id')]] += $row[csf('production_qnty')];
                        $color_size_qty_arr[$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['production_qnty'] += $row[csf('production_qnty')];
                        $color_size_qty_arr[$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['po_number']=$row[csf('po_number')];
                        $color_size_qty_arr[$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['article_number']=$row[csf('article_number')];

                        if($col_size_id_chk[$row['COL_SIZE_ID']]=="")
                        {
                            $color_size_qty_arr[$row[csf('job_no')]][$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order_quantity'] += $row[csf('order_quantity')];

                            $col_size_id_chk[$row['COL_SIZE_ID']] = $row['COL_SIZE_ID'];
                        }
                        $size_wise_bundle_no_arr[$row[csf('size_number_id')]] += $row[csf('num_of_bundle')];
                    }
                    // echo "<pre>";print_r($color_size_qty_arr); die;
                ?>
                <br>
                <!-- =================Size Wise Summary -->
                <table cellspacing="0" border="1" rules="all" cellpadding="3" style="font: 12px tahoma;">
                    <thead>
                    <tr>
                        <td colspan="4"><strong>Size Wise Summary</strong></td>
                    </tr>
                    <tr bgcolor="#dddddd" align="center">
                        <th>SL</th>
                        <th>Job No</th>
                        <th>Order</th>
                        <th>Gmt Color</th>
                        <th>Size</th>
                        <th>Article No</th>
                        <th>Order Qty</th>
                        <th>Order To Cutting Ratio%</th>
                        <th>Variance</th>
                        <th>No Of Bundle</th>
                        <th>Quantity (Pcs)</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?
                    $i = 1;
                    $color_id_arr = array();
                    $size_id_arr = array();
                    // echo "<pre>";print_r($color_size_qty_arr); die;
                    foreach ($color_size_qty_arr as $job_no => $job_data)
                    {
                        foreach ($job_data as $po_id => $po_data)
                        {
                            foreach ($po_data as $color_id => $color_data)
                            {
                                foreach ($color_data as $size_id => $row)
                                {
                                    $color_id_arr[$color_id] = $color_id;
                                    $size_id_arr[$size_id] = $size_id;
                                    $po_number=implode(",",(array_filter(explode(",", $row['po_number']))));

                                    $bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
                                    // echo $cutting_data_arr[$job_no][$po_id][$color_id][$size_id]['total_cut']."-".$row['order_quantity']."<br>";
                                    ?>

                                    <tr bgcolor="<? echo $bgcolor; ?>">
                                        <td align="center"><?=$i; ?></td>
                                        <td align="center"><? echo  $job_no; ?></td>
                                        <td align="center"><? echo $row['po_number']; ?></td>
                                        <td align="center"><? echo $color_library[$color_id]; ?></td>
                                        <td align="center"><? echo $size_library[$size_id]; ?></td>
                                        <td align="right">
                                        <? echo $color_size_qty_arr[$job_no][$po_id][$color_id][$size_id]['article_number']; ?>
                                        </td>
                                        <td align="right">
                                        <? echo number_format($color_size_qty_arr[$job_no][$po_id][$color_id][$size_id]['order_quantity']); ?>
                                        </td>
                                        <td align="right">
                                        <? echo number_format($cutting_data_arr[$job_no][$po_id][$color_id][$size_id]['total_cut']/$row['order_quantity']*100,2); ?>
                                        </td>
                                        <td align="right"><? echo number_format($cutting_data_arr[$job_no][$po_id][$color_id][$size_id]['total_cut']-$row['order_quantity']);  ?></td>
                                        <td align="right"><? echo $total_no_bundle_arr[$po_id][$color_id][$size_id];?></td>
                                        <td align="right"><? echo $total_no_quantity_arr[$po_id][$color_id][$size_id];?></td>
                                    </tr>
                                    <?
                                    $i++;
                                }
                            }
                        }
                    }
                    ?>
                    </tbody>
                    <!-- <tr>
                        <td colspan="4" style="padding: 1px !important;"></td>
                    </tr>
                    <tr>
                        <td colspan="9" align="right"><strong>Total </strong></td>
                        <td align="center"><? echo $total_bundle; ?></td>
                        <td align="right"><? echo $production_quantity; ?></td>
                    </tr> -->
                </table>
                <br>
                <?
                $job_no_implode = "'".implode("','", $job_no)."'";
                $job_id_implode = "'".implode("','", $job_id)."'";
                $all_po_id = "'".implode("','", $all_po_arr)."'";
                $all_color_id = implode(",", $color_id_arr);
                $all_size_id = implode(",", $size_id_arr);
                    // echo $all_po_id;
                if($data[4]==1)
                {
                    /* $sql="SELECT A.ID,A.JOB_NO,A.JOB_ID,A.TRIM_GROUP,B.ITEM_NAME,C.PO_NUMBER,F.QUANTITY    AS ISSUE_QTY,G.RECEIVE_QNTY,SUM(C.PO_QUANTITY) AS PO_QUANTITY,A.DESCRIPTION,A.CONS_UOM,A.CONS_DZN_GMTS,E.CONS_PCS,A.REMARK,E.COLOR_NUMBER_ID AS GMTS_COLOR,E.ITEM_COLOR_NUMBER_ID AS ITEM_COLOR,E.SIZE_NUMBER_ID AS GMTS_SIZE,E.ITEM_SIZE
                    FROM wo_pre_cost_trim_cost_dtls a,lib_item_group b,wo_po_break_down c,wo_po_details_master d,wo_pre_cost_trim_co_cons_dtls e,order_wise_pro_details f,inv_trims_entry_dtls g
                    WHERE     b.id = a.trim_group
                    AND c.job_no_mst = a.job_no
                    AND d.id = c.job_id
                    AND d.id = e.job_id
                    And a.id=e.WO_PRE_COST_TRIM_COST_DTLS_ID
                    And c.id=e.PO_BREAK_DOWN_ID
                    AND  c.id=f.PO_BREAKDOWN_ID
                    AND  g.id=f.dtls_id
                    AND b.trim_type = 1
                    AND c.id IN  ($all_po_id)
                    AND e.COLOR_NUMBER_ID IN  ($all_color_id)
                    AND e.SIZE_NUMBER_ID IN  ($all_size_id)
                    And a.STATUS_ACTIVE=1 and a.IS_DELETED=0
                    And b.STATUS_ACTIVE=1 and b.IS_DELETED=0
                    And c.STATUS_ACTIVE=1 and c.IS_DELETED=0
                    And d.STATUS_ACTIVE=1 and d.IS_DELETED=0
                    And e.STATUS_ACTIVE=1 and e.IS_DELETED=0
                    Group By A.ID,A.JOB_NO,A.JOB_ID,A.TRIM_GROUP,B.ITEM_NAME,C.PO_NUMBER,A.DESCRIPTION,A.CONS_UOM,A.CONS_DZN_GMTS,E.CONS_PCS,A.REMARK,E.COLOR_NUMBER_ID,E.ITEM_COLOR_NUMBER_ID,E.SIZE_NUMBER_ID,E.ITEM_SIZE, F.QUANTITY,G.RECEIVE_QNTY
                    ORDER BY a.id ASC"; */
                    $sql="SELECT A.ID,A.JOB_NO,A.JOB_ID,A.TRIM_GROUP,B.ITEM_NAME,C.PO_NUMBER,SUM(C.PO_QUANTITY) AS PO_QUANTITY,A.DESCRIPTION,A.CONS_UOM,A.CONS_DZN_GMTS,E.CONS_PCS,A.REMARK,E.COLOR_NUMBER_ID AS GMTS_COLOR,E.ITEM_COLOR_NUMBER_ID AS ITEM_COLOR,E.SIZE_NUMBER_ID AS GMTS_SIZE,E.ITEM_SIZE,c.id as po_id
                    FROM wo_pre_cost_trim_cost_dtls a,lib_item_group b,wo_po_break_down c,wo_po_details_master d,wo_pre_cost_trim_co_cons_dtls e
                    WHERE     b.id = a.trim_group
                    AND c.job_no_mst = a.job_no
                    AND d.id = c.job_id
                    AND d.id = e.job_id
                    And a.id=e.WO_PRE_COST_TRIM_COST_DTLS_ID
                    And c.id=e.PO_BREAK_DOWN_ID
                    AND b.trim_type = 1
                    AND c.id IN  ($all_po_id)
                    AND e.COLOR_NUMBER_ID IN  ($all_color_id)
                    AND e.SIZE_NUMBER_ID IN  ($all_size_id)
                    And a.STATUS_ACTIVE=1 and a.IS_DELETED=0
                    And b.STATUS_ACTIVE=1 and b.IS_DELETED=0
                    And c.STATUS_ACTIVE=1 and c.IS_DELETED=0
                    And d.STATUS_ACTIVE=1 and d.IS_DELETED=0
                    And e.STATUS_ACTIVE=1 and e.IS_DELETED=0
                    and E.CONS_PCS>0
                    Group By A.ID,A.JOB_NO,A.JOB_ID,A.TRIM_GROUP,B.ITEM_NAME,C.PO_NUMBER,A.DESCRIPTION,A.CONS_UOM,A.CONS_DZN_GMTS,E.CONS_PCS,A.REMARK,E.COLOR_NUMBER_ID,E.ITEM_COLOR_NUMBER_ID,E.SIZE_NUMBER_ID,E.ITEM_SIZE,c.id
                    ORDER BY a.id ASC";
                    //  echo $sql; die;
                    //$sqlTrimData = sql_select("select a.id, a.job_no, a.job_id, a.trim_group, b.item_name, c.po_number, c.po_quantity, a.description, a.cons_uom, a.cons_dzn_gmts, a.remark, d.total_set_qnty, d.order_uom from wo_pre_cost_trim_cost_dtls a, lib_item_group b, wo_po_break_down c, wo_po_details_master d where b.id = a.trim_group and c.job_no_mst = a.job_no and d.id = c.job_id and b.trim_type = 1 and c.id in ($all_po_id) order  by a.id asc");

                    $sqlTrimData = sql_select($sql);
                    $conversion_fac_arr=return_library_array("select id, conversion_factor from LIB_ITEM_GROUP where ITEM_CATEGORY=4","id","conversion_factor");
                    // ================================ trims receive ===============================
                    $sql = "SELECT d.ITEM_GROUP_ID, c.QUANTITY from ORDER_WISE_PRO_DETAILS c, PRODUCT_DETAILS_MASTER d
                    where c.prod_id=d.id and c.trans_type in(1,4,5) and c.entry_form in(24,73,78,112) and d.entry_form=24  AND c.po_breakdown_id IN  ($all_po_id) and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0";
                    // echo $sql;
                    $res= sql_select($sql);
                    $trims_rcv_qty_array = array();
                    foreach ($res as$v)
                    {
                        // $trims_rcv_qty_array[$v['ITEM_GROUP_ID']][$v['PO_ID']][$v['COLOR_ID']][$v['SIZE_ID']] = $v['QUANTITY'];
                        $trims_rcv_qty_array[$v['ITEM_GROUP_ID']] += $v['QUANTITY']*$conversion_fac_arr[$v['ITEM_GROUP_ID']];
                    }

                    $sql = "SELECT d.ITEM_GROUP_ID, c.QUANTITY from ORDER_WISE_PRO_DETAILS c, PRODUCT_DETAILS_MASTER d
                    where c.prod_id=d.id and c.trans_type in(2,3,6) and c.entry_form in(25,49,78,112) and d.entry_form=24  AND c.po_breakdown_id IN  ($all_po_id)";
                    // echo $sql;
                    $res= sql_select($sql);
                    $trims_issue_qty_array = array();
                    foreach ($res as$v)
                    {
                        $trims_issue_qty_array[$v['ITEM_GROUP_ID']] += $v['QUANTITY']*$conversion_fac_arr[$v['ITEM_GROUP_ID']];
                    }

                    // if(count($sqlTrimData) > 0){
                    ?>
                        <!-- ==============Required Sewing Trims==================== -->
                        <table cellspacing="0" border="1" rules="all" cellpadding="3" style="font: 12px tahoma;" width="1520">
                            <thead>
                                <tr>
                                    <th colspan="15" align="center"><strong>Required Sewing Trims</strong></th>
                                </tr>
                                <tr bgcolor="#dddddd" align="center">
                                    <th width="30">S/L</th>
                                    <th width="140">Item Name</th>
                                    <th width="200">Description</th>
                                    <th width="120">Gmts.Color</th>
                                    <th width="120">Gmts Size</th>
                                    <th width="100">Item Color</th>
                                    <th width="100">Item Size</th>
                                    <th width="80">Job NO</th>
                                    <th width="80">Order No.</th>
                                    <th width="120">Wo No.</th>
                                    <th width="100">Budget Cons/DZN</th>
                                    <th width="70">UOM</th>
                                    <th width="100">Required Qty.</th>
                                    <th width="80">Perv.Issue Qty</th>
                                    <th width="80">Stock </th>
                                    <th width="80">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                $sqlGetBookingData = sql_select("select pre_cost_fabric_cost_dtls_id, booking_no, wo_qnty from wo_booking_dtls where po_break_down_id in ($all_po_id) and status_active = 1  and is_deleted = 0");
                                $bookingArr = array();
                                foreach ($sqlGetBookingData as $key => $booking)
                                {
                                    $bookingArr[$booking[csf('pre_cost_fabric_cost_dtls_id')]]['booking_no'][$key] = $booking[csf('booking_no')];
                                    $bookingArr[$booking[csf('pre_cost_fabric_cost_dtls_id')]]['booking_qty'][$key] = $booking[csf('wo_qnty')];
                                }
                                $mainDataArr = array();
                                $totalQty = 0;
                                $tot_issue = 0;
                                $tot_stock = 0;
                                $totalcons_dzn = 0;
                                foreach ($sqlTrimData as $preTrim)
                                {
                                    if($preTrim[csf('cons_pcs')]>0)
                                    {
                                        $production_quantity = $total_no_quantity_arr[$preTrim[csf('po_id')]][$preTrim[csf('gmts_color')]][$preTrim[csf('gmts_size')]];

                                        $reqQty =($preTrim[csf('cons_pcs')] / 12) * $production_quantity;
                                        if($reqQty>0)
                                        {

                                            $keyMod = $preTrim[csf('item_name')]."*".$preTrim[csf('gmts_color')]."*".$preTrim[csf('description')]."*".$preTrim[csf('gmts_size')]."*".$preTrim[csf('item_color')]."*".$preTrim[csf('item_size')]."*".$preTrim[csf('po_number')]."*".$preTrim[csf('po_id')]."*".$preTrim[csf('trim_group')];
                                            $mainDataArr[$preTrim[csf('TRIM_GROUP')]][$keyMod]['item_name'] = $preTrim[csf('item_name')];
                                            $mainDataArr[$preTrim[csf('TRIM_GROUP')]][$keyMod]['description'] = $preTrim[csf('description')];
                                            $mainDataArr[$preTrim[csf('TRIM_GROUP')]][$keyMod]['gmts_color'] = $preTrim[csf('gmts_color')];
                                            $mainDataArr[$preTrim[csf('TRIM_GROUP')]][$keyMod]['gmts_size'] = $preTrim[csf('gmts_size')];
                                            $mainDataArr[$preTrim[csf('TRIM_GROUP')]][$keyMod]['item_color'] = $preTrim[csf('item_color')];
                                            $mainDataArr[$preTrim[csf('TRIM_GROUP')]][$keyMod]['item_size'] = $preTrim[csf('item_size')];

                                            $mainDataArr[$preTrim[csf('TRIM_GROUP')]][$keyMod]['uom'] =  $unit_of_measurement[$preTrim[csf('cons_uom')]];
                                            $mainDataArr[$preTrim[csf('TRIM_GROUP')]][$keyMod]['cons_dzn'] = $preTrim[csf('cons_pcs')];
                                            $mainDataArr[$preTrim[csf('TRIM_GROUP')]][$keyMod]['remarks'] = $preTrim[csf('remark')];
                                            $mainDataArr[$preTrim[csf('TRIM_GROUP')]][$keyMod]['job_no'] = $preTrim[csf('job_no')];
                                            $mainDataArr[$preTrim[csf('TRIM_GROUP')]][$keyMod]['order_no'] = $preTrim[csf('po_number')];
                                            $mainDataArr[$preTrim[csf('TRIM_GROUP')]][$keyMod]['po_qty'] = $preTrim[csf('po_quantity')];
                                            $mainDataArr[$preTrim[csf('TRIM_GROUP')]][$keyMod]['set_qty'] = ($preTrim[csf('order_uom')] == 58 ? $preTrim[csf('total_set_qnty')] : 1);
                                            $mainDataArr[$preTrim[csf('TRIM_GROUP')]][$keyMod]['booking_no'] = implode(', ',array_unique($bookingArr[$preTrim[csf('id')]]['booking_no']));
                                            $mainDataArr[$preTrim[csf('TRIM_GROUP')]][$keyMod]['booking_qty'] = array_sum($bookingArr[$preTrim[csf('id')]]['booking_qty']);
                                            $mainDataArr[$preTrim[csf('TRIM_GROUP')]][$keyMod]['issue_qty'] = $preTrim[csf('issue_qty')];
                                            $mainDataArr[$preTrim[csf('TRIM_GROUP')]][$keyMod]['receive_qnty'] = $preTrim[csf('receive_qnty')];
                                        }
                                    }
                                }
                                // echo "<pre>";print_r($mainDataArr);
                                $count = 1;
                                foreach ($mainDataArr as $trim_group => $item_data)
                                {
                                    $r=0;
                                    foreach ($item_data as $key => $val)
                                    {
                                        if ($count % 2 == 0)
                                            $bgcolor = "#E9F3FF";
                                        else
                                            $bgcolor = "#FFFFFF";

                                        $ex = explode("*",$key);
                                        $production_quantity = $total_no_quantity_arr[$ex['7']][$ex['1']][$ex['3']];

                                        $reqQty =($val['cons_dzn'] / 12) * $production_quantity;
                                        $trims_rcve = $trims_rcv_qty_array[$trim_group];
                                        // echo "(".$val['cons_dzn'] ."/ 12) * ".$ex['3']."<br>";
                                        if ( $trims_rcve> 0)
                                        {
                                            ?>
                                            <tr bgcolor="<?=$bgcolor?>">
                                                <td align="center"><?=$count?></td>
                                                <td><?=$val['item_name'];?></td>
                                                <td><?=$val['description'];?></td>
                                                <td><?= $color_library[$color_id];?></td>
                                                <td><?=$size_library[$val['gmts_size']];?></td>
                                                <td><?= $color_library[$val['item_color']];?></td>
                                                <td><?=$val['item_size'];?></td>
                                                <td align="center"><?=$val['job_no']?></td>
                                                <td align="center"><?=$val['order_no']?></td>
                                                <td align="center" ><?=$val['booking_no']?></td>
                                                <td align="right"><?=$cons_dzn=number_format($val['cons_dzn'], '2', '.', '')?></td>
                                                <td align="center"><?=$val['uom']?></td>
                                                <td align="right"><?=number_format($reqQty, 2)?></td>
                                                <? if($r==0)
                                                {
                                                    ?>
                                                    <td rowspan="<?=count($item_data);?>" align="center"><?=$trims_issue_qty_array[$trim_group];?></td>
                                                    <td rowspan="<?=count($item_data);?>" align="center"><?=number_format(($trims_rcv_qty_array[$trim_group]-$trims_issue_qty_array[$trim_group]),2); ?></td>
                                                    <?
                                                    $r++;

                                                    $tot_issue += $trims_issue_qty_array[$trim_group];
                                                    $tot_stock += $trims_rcv_qty_array[$trim_group]-$trims_issue_qty_array[$trim_group];
                                                }
                                                ?>
                                                <td><?=$val['remarks']?></td>
                                            </tr>
                                            <?
                                        }
                                        $totalQty += $reqQty;
                                        // $tot_issue += $trims_issue_qty_array[$trim_group];
                                        // $tot_stock += $stock;
                                        $totalcons_dzn += $cons_dzn;
                                        $count++;
                                    }
                                }
                                ?>
                            </tbody>
                            <tr>
                                <td align="right" colspan="12"><strong>Total</strong></td>
                                <td align="right"><?=number_format($totalQty, 2)?></td>
                                <td align="right"><?=number_format($tot_issue, 2)?></td>
                                <td align="right"><?=number_format($tot_stock, 2)?></td>
                                <td></td>
                            </tr>

                        </table>
                        <br>
                    <?
                    // }
                }
                echo signature_table(28, $data[0], "900px","","10px");
                ?>
            </div>
        </div>
        <?
    // }
    ?>
    <div id="footer_id"></div>

    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
        function generateBarcode(valuess) {
            var ids='<? echo $kk;?>';
            var value = valuess;//$("#barcodeValue").val();
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

            value = {code: value, rect: false};
            $("#barcode_img_id_"+ids).show().barcode(value, btype, settings);
        }
        generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
    </script>
    <?
    // $kk++;


    if($is_mail_send==1){
        $emailBody=ob_get_contents();

        $sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=120 and  b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
        $mail_sql_res=sql_select($sql);

        $mailArr=array();
        foreach($mail_sql_res as $row)
        {
            $mailArr[$row['EMAIL']]=$row['EMAIL'];
        }

        if($mail_id!=''){$mailArr[]=$mail_id;}


        $to=implode(',',$mailArr);
        $subject="Bundle Wise Delivery to Sewing Input Challan/Gate Pass";

        if($to!=""){
            include('../../auto_mail/setting/mail_setting.php');
            $header=mailHeader();
            echo sendMailMailer( $to, $subject, $mail_body."<br><br>".$emailBody,$from_mail,'' );
        }
    }

    exit();
}

if($action=="emblishment_issue_print_3_18.02.2021")
{
    extract($_REQUEST);
    $data=explode('*',$data);
    //print_r ($data);
    $company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
    $supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
    $location_library=return_library_array( "select id, location_name from  lib_location", "id", "location_name");
    $floor_library=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name");
    $color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
    $size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
    $line_library = return_library_array("select id,line_name from lib_sewing_line", "id", "line_name");
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $body_part_arr=return_library_array( "select id,bundle_use_for from ppl_bundle_title where company_id=$data[0]",'id','bundle_use_for');
    $order_array=array();
    //$order_sql="select a.job_no, a.buyer_name,a.style_ref_no, b.id, b.po_number, b.po_quantity,c.cutting_no  from  wo_po_details_master a, wo_po_break_down b,ppl_cut_lay_mst c,ppl_cut_lay_dtls d where  c.id=d.mst_id and d.order_id=b.id and a.job_no=b.job_no_mst and b.job_no_mst=c.job_no and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";//c.entry_form=77 and
    $order_sql="SELECT a.job_no, a.buyer_name,a.style_ref_no,a.style_description, b.id, b.po_number, b.po_quantity,b.grouping  from  wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$data[0]";//c.entry_form=77 and

    $order_sql_result=sql_select($order_sql);
    foreach ($order_sql_result as $row)
    {
        $order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
        $order_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
        $order_array[$row[csf('id')]]['grouping']=$row[csf('grouping')];
        $order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
        $order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
        $order_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
        $order_array[$row[csf('id')]]['cut_num'][$row[csf('cutting_no')]]=$row[csf('cutting_no')];
        $order_array[$row[csf('id')]]['style_des']=$row[csf('style_description')];
    }

    $sql="select id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
    production_source, serving_company, floor_id, sewing_line, organic, delivery_date,working_company_id,working_location_id from pro_gmts_delivery_mst where production_type=4 and id='$data[1]' and
    status_active=1 and is_deleted=0 ";
    $dataArray=sql_select($sql);

    $nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='" . $dataArray[0][csf('serving_company')] . "' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];

    if ($prod_reso_allocation == 1) {

        if ($dataArray[0][csf('floor_id')] == 0 && $dataArray[0][csf('location_id')] != 0) $cond = " and a.location_id= {$dataArray[0][csf('location_id')]}";
        if ($dataArray[0][csf('floor_id')] != 0) $cond = " and a.floor_id= {$dataArray[0][csf('floor_id')]}";

        if ($db_type == 0) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
        } else if ($db_type == 2 || $db_type == 1) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
        }

        foreach ($line_data as $row) {
            $line = '';
            $line_number = explode(",", $row[csf('line_number')]);
            foreach ($line_number as $val) {
                if ($line == '') $line = $line_library[$val]; else $line .= "," . $line_library[$val];
            }
            $line_array[$row[csf('id')]] = $line;
        }
        $line = $line_array[$dataArray[0][csf('sewing_line')]];
    } else {
        $line = $line_library[$dataArray[0][csf('sewing_line')]];
    }

	?>
	<div style="width:900px;">
	    <table cellspacing="0" style="font: 11px tahoma; width: 100%;">
	        <tr>
	            <td colspan="6" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="6" align="center" style="font-size:14px">
	                <?

	                    $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");



	                    foreach ($nameArray as $result)
	                    {

	                         echo $result[csf('city')];

	                    }
	                ?>
	            </td>
	        </tr>
	        <tr>
	            <td colspan="6" align="center" style="font-size:20px"><u><strong>Bundle Wise Delivery to Sewing Input Challan/Gate Pass</strong></u></td>
	        </tr>

	            <tr>
	                <td width="95"><strong>Challan No</strong></td>
	                <td width="150"><? echo ": ".$dataArray[0][csf('sys_number')]; ?></td>
	                <td width="80"><strong>Source</strong></td>
	                <td width="190"><? echo ": ".$knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
	                <td width="120"><strong>Sew. Company</strong></td>
	                <td>
	                    <?
	                    if ($dataArray[0][csf('production_source')] == 1) echo ": ".$company_library[$dataArray[0][csf('serving_company')]];
	                    else echo ": ".$supplier_library[$dataArray[0][csf('serving_company')]];

	                    ?>
	                </td>
	            </tr>
	            <tr>
	                <td><strong>Location</strong></td>
	                <td><? echo ": ".$location_library[$dataArray[0][csf('location_id')]]; ?></td>
	                <td><strong>Floor </strong></td>
	                <td><? echo ": ".$floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
	                <td><strong>Line </strong></td>
	                <td><? echo ": ".$line; ?></td>
	            </tr>
	            <tr>
	            <td><strong>Working Company  </strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
	            <td><strong>Working Location </strong></td><td>: <? echo $location_library[$dataArray[0][csf('working_location_id')]]; ?></td>
	            <td><strong>Input Date </strong></td>
	             <td><? echo ": ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
	            </tr>
	            <tr>

	                <td>Remarks</td>
	                <td colspan="3"><? //echo $dataArray[0][csf('sewing_line')];
	                    ?></td>
	            </tr>


	<!--        <tr>
	            <td width="90"><strong>Challan No</strong></td> <td width="140px">: <? echo $dataArray[0][csf('sys_number')]; ?></td>
	            <td width="110"><strong>Embel. Name</strong></td><td width="175px"> : <? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
	            <td width="105"><strong>Emb. Type</strong></td><td width="155px">:
	            <?
	                if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]];
	                elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]];
	                elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]];
	                elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]];
	             ?>
	            </td>
	        </tr>
	        <tr>
	            <td><strong>Emb. Source</strong></td><td>: <? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
	            <td><strong>Emb. Company</strong></td><td>:
	                <?
	                    if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]];
	                    else echo $supplier_library[$dataArray[0][csf('serving_company')]];

	                ?>
	            </td>
	            <td><strong>Location</strong></td><td>: <? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
	        </tr>
	        <tr>
	            <td><strong>Floor </strong></td><td>: <? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
	            <td><strong>Organic</strong></td><td> : <? echo $dataArray[0][csf('organic')]; ?></td>
	            <td><strong>Delivery Date </strong></td><td>: <? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
	        </tr>
	        <tr>
	         <td><strong>Body Part  </strong></td><td>: <? echo $body_part_arr[$dataArray[0][csf('body_part')]]; ?></td>
	            <td><strong>Working Company  </strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
	            <td><strong>Working Location </strong></td><td>: <? echo $location_library[$dataArray[0][csf('working_location_id')]]; ?></td>
	        </tr>
	-->        <tr>
	            <td  colspan="4" id="barcode_img_id"></td>

	        </tr>

	    </table><br />
	        <?
	if($db_type==2) $group_concat="  listagg(cast(b.cut_no AS VARCHAR2(4000)),',') within group (order by b.cut_no) as cut_no" ;
	    else if($db_type==0) $group_concat=" group_concat(b.cut_no) as cut_no" ;

	            $delivery_mst_id =$dataArray[0][csf('id')];
	            // base on Embel. Name
	            if($data[2]==3)
	            {
	                if($db_type==0)
	                {
	                    $sql="SELECT  sum(b.production_qnty) as production_qnty,a.po_break_down_id,a.item_number_id,a.country_id,b.bundle_no, d.color_number_id,d.size_number_id,
	                    count(b.id) as  num_of_bundle,
	                    (select sum(c.number_start) from ppl_cut_lay_bundle c where  b.bundle_no = c.bundle_no) number_start,
	                    (select sum(e.number_end) from ppl_cut_lay_bundle e where  b.bundle_no = e.bundle_no) number_end
	                    from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d
	                    where a.delivery_mst_id ='$data[1]' and a.id=b.mst_id and b.color_size_break_down_id=d.id and b.status_active=1 and b.is_deleted=0 and d.status_active=1
	                    and d.is_deleted=0 and b.bundle_no <> ''
	                    group by a.po_break_down_id,a.item_number_id,a.country_id,b.bundle_no,d.color_number_id,d.size_number_id
	                    order by b.bundle_no ";
	                }
	                else
	                {
	                    /*$sql="SELECT $group_concat,sum(b.production_qnty) as production_qnty,a.po_break_down_id,a.item_number_id,a.country_id,b.bundle_no, d.color_number_id,d.size_number_id,
	                    count(b.id) as  num_of_bundle, sum(C.NUMBER_START) number_start, sum(C.NUMBER_END) number_end
	                    from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d, ppl_cut_lay_bundle  c
	                    where a.delivery_mst_id ='$data[1]' and a.id=b.mst_id and b.bundle_no = c.bundle_no and b.color_size_break_down_id=d.id and b.status_active=1 and b.is_deleted=0 and d.status_active=1
	                    and d.is_deleted=0  and b.bundle_no is not null
	                    group by a.po_break_down_id,a.item_number_id,a.country_id,b.bundle_no,d.color_number_id,d.size_number_id
	                    order by a.po_break_down_id,d.color_number_id ";*/

	                 $sql="SELECT $group_concat,sum(b.production_qnty) as production_qnty,a.po_break_down_id,a.item_number_id,a.country_id,b.bundle_no, d.color_number_id,d.size_number_id,
	                    count(b.id) as  num_of_bundle
	                    from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d
	                    where a.delivery_mst_id ='$data[1]' and a.id=b.mst_id  and b.color_size_break_down_id=d.id and b.status_active=1 and b.is_deleted=0 and d.status_active=1
	                    and d.is_deleted=0  and b.bundle_no is not null and a.production_type=4
	                    group by a.po_break_down_id,a.item_number_id,a.country_id,b.bundle_no,d.color_number_id,d.size_number_id
	                    order by  d.size_number_id ";

	                }
	            }
	            else
	            {
	                if($db_type==0)
	                {
	                    $sql="SELECT sum(a.production_qnty) as production_qnty,c.po_break_down_id,c.item_number_id,c.country_id, b.color_number_id,b.size_number_id
	                    from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c where c.delivery_mst_id ='$data[1]'
	                    and c.id=a.mst_id  and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.bundle_no!=''
	                    group by c.po_break_down_id,c.item_number_id,c.country_id,b.color_number_id,b.size_number_id ";
	                }
	                else
	                {
	                    $sql="SELECT sum(a.production_qnty) as production_qnty,c.po_break_down_id,c.item_number_id,c.country_id,b.color_number_id ,b.size_number_id
	                    from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c where c.delivery_mst_id ='$data[1]' and c.id=a.mst_id
	                    and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.bundle_no is not null
	                    group by c.po_break_down_id,c.item_number_id,c.country_id,b.color_number_id,b.size_number_id ";
	                }

	            }

	            //echo $sql;
	            $result=sql_select($sql);
	            $rows=array();
	            $po_cutlay_id_arr=array();
	            $po_wise_ordcutno_arr=array();

	            foreach($result as $value)
	            {
	                $po_cutlay_id_arr[$value[csf('po_break_down_id')]]= $value[csf('po_break_down_id')];
	            }
	            $order_id_cut=implode(",", $po_cutlay_id_arr);
	            if(!$order_id_cut)$order_id_cut=0;

	            $sql_order_cut="SELECT c.cutting_no, a.order_cut_no ,b.order_id from ppl_cut_lay_mst c, ppl_cut_lay_dtls a, ppl_cut_lay_bundle b where c.id=b.mst_id  and c.status_active=1 and c.is_deleted=0 and  a.mst_id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_id in($order_id_cut) group by c.cutting_no, a.order_cut_no ,b.order_id " ;
	            foreach(sql_select($sql_order_cut) as $vals)
	            {
	                $po_ids=explode(",", $vals[csf("order_id")]);
	                foreach($po_ids as $val)
	                {
	                    $po_wise_ordcutno_arr[$vals[csf("cutting_no")]][$val]=$vals[csf("order_cut_no")];
	                }

	            }
	            //print_r($po_wise_ordcutno_arr);die;

	            foreach($result as $val)
	            {
	                $rows[$buyer_arr[$order_array[$val[csf('po_break_down_id')]]['buyer_name']]] [$order_array[$val[csf('po_break_down_id')]]['job_no']][$order_array[$val[csf('po_break_down_id')]]['grouping']] [$order_array[$val[csf('po_break_down_id')]]['style_ref']] [$order_array[$val[csf('po_break_down_id')]]['style_des']] [$order_array[$val[csf('po_break_down_id')]]['po_number']] [implode(',',array_unique(explode(',',$val[csf('cut_no')])))] [$garments_item[$val[csf('item_number_id')]]][$country_library[$val[csf('country_id')]]] [$color_library[$val[csf('color_number_id')]]] ['val']+=$val[csf('production_qnty')];

	                $rows[$buyer_arr[$order_array[$val[csf('po_break_down_id')]]['buyer_name']]] [$order_array[$val[csf('po_break_down_id')]]['job_no']][$order_array[$val[csf('po_break_down_id')]]['grouping']] [$order_array[$val[csf('po_break_down_id')]]['style_ref']] [$order_array[$val[csf('po_break_down_id')]]['style_des']] [$order_array[$val[csf('po_break_down_id')]]['po_number']] [implode(',',array_unique(explode(',',$val[csf('cut_no')])))] [$garments_item[$val[csf('item_number_id')]]][$country_library[$val[csf('country_id')]]] [$color_library[$val[csf('color_number_id')]]] ['order_no']=$val[csf('po_break_down_id')];

	                $rows[$buyer_arr[$order_array[$val[csf('po_break_down_id')]]['buyer_name']]] [$order_array[$val[csf('po_break_down_id')]]['job_no']][$order_array[$val[csf('po_break_down_id')]]['grouping']] [$order_array[$val[csf('po_break_down_id')]]['style_ref']] [$order_array[$val[csf('po_break_down_id')]]['style_des']] [$order_array[$val[csf('po_break_down_id')]]['po_number']] [implode(',',array_unique(explode(',',$val[csf('cut_no')])))] [$garments_item[$val[csf('item_number_id')]]][$country_library[$val[csf('country_id')]]] [$color_library[$val[csf('color_number_id')]]] ['count']++;


	                $size_qty_arr[$val[csf('size_number_id')]] += $val[csf('production_qnty')];
	                $size_wise_bundle_no_arr[$val[csf('size_number_id')]] ++;
	            }

	           // print_r($po_wise_ordcutno_arr);
	            //die;
	            unset($result);
	        ?>


	    <div style="width:100%;">
	    <table cellspacing="0" width="980" border="1" rules="all" class="rpt_table" style="font: 12px tahoma;">
	        <thead bgcolor="#dddddd" align="center">
	            <th width="30">SL</th>
	            <th width="80" align="center">Buyer</th>
                <th width="80" align="center">Job</th>
	            <th width="80" align="center">Int. Ref.</th>
	            <th width="80" align="center">Style Ref</th>
	            <th width="100" align="center">Style Des</th>
	            <th width="80" align="center">Order No.</th>
	            <th width="120" align="center">Order Cut No</th>
	            <th width="80" align="center">Cutting Number</th>
	            <th width="80" align="center">Gmt. Item</th>
	            <th width="80" align="center">Country</th>
	            <th width="80" align="center">Color</th>
	            <th width="80" align="center">Gmt. Qty</th>
	            <? if($data[2]==3)  {  ?>
	            <th align="center">No of Bundle</th>
	            <? }   ?>
	        </thead>
	        <tbody>
	            <?
	          //  $size_qty_arr=array();
	            $i=1;
	            $tot_qnty=array();
	             foreach($rows as $buyer=>$brows)
	             {
	                 foreach($brows as $job=>$jrows)
	                 {
                        foreach($jrows as $intRef=>$intRefs)
                        {
    	                     foreach($intRefs as $styleref=>$srrows)
    	                     {
    	                         foreach($srrows as $styledes=>$sdrows)
    	                         {
    	                             foreach($sdrows as $order=>$orows)
    	                             {
    	                                 foreach($orows as $cutn=>$ctrows)
    	                                 {
    	                                    foreach($ctrows as $gmtitm=>$girows)
    	                                     {
    	                                        foreach($girows as $Country=>$cntrows)
    	                                         {
    	                                             foreach($cntrows as $color=>$cdata)
    	                                             {
    	                                                if ($i%2==0)
    	                                                    $bgcolor="#E9F3FF";
    	                                                else
    	                                                    $bgcolor="#FFFFFF";
    	                                                $color_count=count($cid);
    	                                                ?>
    	                                                <tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
    	                                                    <td><? echo $i;  ?></td>
    	                                                    <td align="center"><? echo $buyer; ?></td>
                                                            <td align="center"><? echo $job; ?></td>
    	                                                    <td align="center"><? echo $intRef; ?></td>
    	                                                    <td align="center"><? echo $styleref; ?></td>
    	                                                    <td align="center"><? echo $styledes; ?></td>
    	                                                    <td align="center"><? echo $order; ?></td>
    	                                                    <td align="center"><? echo $po_wise_ordcutno_arr[$cutn][$cdata['order_no']]; ?></td>
    	                                                    <td align="center"><? echo $cutn; ?></td>
    	                                                    <td align="center"><? echo $gmtitm; ?></td>
    	                                                    <td align="center"><? echo $Country; ?></td>
    	                                                    <td align="center"><? echo $color;?></td>
    	                                                    <td align="right"><?  echo $cdata['val']; ?></td>
    	                                                    <? if($data[2]==3)
    	                                                     {  ?>
    	                                                    <td  align="center"> <?  echo $cdata['count']; ?></td>
    	                                                    <?
    	                                                    $color_qty_arr[$color] += $cdata['val'];
    	                                                    $color_wise_bundle_no_arr[$color] += $cdata['count'];
    	                                                    }
    	                                                    ?>

    	                                                </tr>
    	                                                <?
    	                                                $production_quantity += $cdata['val'];
    	                                                $total_bundle += $cdata['count'];
    	                                                $i++;
    	                                             }
    	                                         }
    	                                     }
    	                                 }
    	                             }
    	                         }
                             }
	                     }
	                 }
	             }

	                ?>
	        </tbody>
	        <tr>
	            <td colspan="12" align="right"><strong>Grand Total </strong></td>
	            <td align="right"><?  echo $production_quantity; ?></td>
	            <td align="center"><?  echo $total_bundle; ?></td>
	        </tr>
	    </table>


	 <br>
	            <table cellspacing="0" border="1" rules="all" cellpadding="3" style="font: 12px tahoma;">
	                <thead>
	                <tr>
	                    <td colspan="4"><strong>Size Wise Summary</strong></td>
	                </tr>
	                <tr bgcolor="#dddddd" align="center">
	                    <td>SL</td>
	                    <td>Size</td>
	                    <td>No Of Bundle</td>
	                    <td>Quantity (Pcs)</td>
	                </tr>
	                </thead>
	                <tbody>
	                <? $i = 1;
	                foreach ($size_qty_arr as $size_id => $size_qty):
	                    $bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>">
	                        <td align="center"><? echo $i; ?></td>
	                        <td align="center"><? echo $size_library[$size_id]; ?></td>
	                        <td align="center"><? echo $size_wise_bundle_no_arr[$size_id]; ?></td>
	                        <td align="right"><? echo $size_qty; ?></td>
	                    </tr>
	                    <?
	                    $i++;
	                endforeach; ?>
	                </tbody>
	                <tfoot>
	                <tr>
	                    <td colspan="4"></td>
	                </tr>
	                <tr>
	                    <td colspan="2" align="right"><strong>Total </strong></td>
	                    <td align="center"><? echo $total_bundle; ?></td>
	                    <td align="right"><? echo $production_quantity; ?></td>
	                </tr>
	                </tfoot>
	            </table>
	            <br>
	         <?
	            echo signature_table(28, $data[0], "500px");
	         ?>
	    </div>
	    </div>
	    <script type="text/javascript" src="../../js/jquery.js"></script>
	    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
	    function generateBarcode( valuess ){
	            var value = valuess;//$("#barcodeValue").val();
	            var btype = 'code39';//$("input[name=btype]:checked").val();
	            var renderer ='bmp';// $("input[name=renderer]:checked").val();
	            var settings = {
	              output:renderer,
	              bgColor: '#FFFFFF',
	              color: '#000000',
	              barWidth: 1,
	              barHeight: 30,
	              moduleSize:5,
	              posX: 10,
	              posY: 20,
	              addQuietZone: 1
	            };

	             value = {code:value, rect: false};
	            $("#barcode_img_id").show().barcode(value, btype, settings);
	        }
	        generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
	     </script>
	<?




	exit();
}

if($action=="sewing_input_challan_print")
{

    extract($_REQUEST);
    $data=explode('*',$data);
    $cbo_company_name = $data[0]; $is_mail_send = $data[4]; $mail_id = $data[5]; $mail_body = $data[6];if( $is_mail_send == 1){ob_start();}
    //print_r ($data);
    $company_library=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    $supplier_library=return_library_array( "select id,supplier_name from  lib_supplier where status_active=1 and is_deleted=0", "id","supplier_name");
    $location_library=return_library_array( "select id, location_name from  lib_location where status_active=1 and is_deleted=0", "id", "location_name");
    $floor_library=return_library_array( "select id, floor_name from  lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
    $color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0 ", "id", "color_name" );
    $size_library=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
    $line_library = return_library_array("select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0", "id", "line_name");
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
    $body_part_arr=return_library_array( "select id,bundle_use_for from ppl_bundle_title where company_id=$data[0]",'id','bundle_use_for');



    $sql="select id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
    production_source, serving_company, floor_id, sewing_line, organic, delivery_date,working_company_id,working_location_id,remarks from pro_gmts_delivery_mst where production_type=4 and id='$data[1]' and
    status_active=1 and is_deleted=0 ";
    $dataArray=sql_select($sql);

    $nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='" . $dataArray[0][csf('company_id')] . "' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];

    if ($prod_reso_allocation == 1) {

        if ($dataArray[0][csf('floor_id')] == 0 && $dataArray[0][csf('location_id')] != 0) $cond = " and a.location_id= {$dataArray[0][csf('location_id')]}";
        if ($dataArray[0][csf('floor_id')] != 0) $cond = " and a.floor_id= {$dataArray[0][csf('floor_id')]}";

        if ($db_type == 0) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
        } else if ($db_type == 2 || $db_type == 1) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
        }

        foreach ($line_data as $row) {
            $line = '';
            $line_number = explode(",", $row[csf('line_number')]);
            foreach ($line_number as $val) {

                if ($line == '') $line = $line_library[$val]; else $line .= "," . $line_library[$val];
            }
            $line_array[$row[csf('id')]] = $line;
        }
        $line = $line_array[$dataArray[0][csf('sewing_line')]];
    } else {
        $line = $line_library[$dataArray[0][csf('sewing_line')]];
    }

	?>
	<div style="width:900px;">
	    <table cellspacing="0" style="font: 11px tahoma; width: 100%;">
	        <tr>
	            <td colspan="6" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="6" align="center" style="font-size:14px">
	                <?
	                    $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");


	                    foreach ($nameArray as $result)
	                    {

	                         echo $result[csf('city')];

	                    }
	                ?>
	            </td>
	        </tr>
	        <tr>
	            <td colspan="6" align="center" style="font-size:20px"><u><strong>Sewing Input Challan</strong></u></td>
	        </tr>
	     <tr>
	            <td width="90"><strong>Challan No</strong></td> <td width="140px">: <? echo $dataArray[0][csf('sys_number')]; ?></td>
	            <td width="110"><strong>Input Date</strong></td><td width="175px"> : <? echo ": ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
	            <td width="105"><strong>Barcode</strong></td><td  colspan="4" id="barcode_img_id"></td>
	        </tr>
	        <tr>

	            <td><strong>Sewing Source</strong></td><td>:
	                <?
	                    echo $knitting_source[$dataArray[0][csf('production_source')]];


	                ?>
	            </td>
	            <td><strong>Sewing Company</strong></td><td>: <? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]];
	                    else echo $supplier_library[$dataArray[0][csf('serving_company')]]; ?> </td>
	            <td><strong>Location</strong></td><td>: <? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
	        </tr>
	        <tr>
	            <td><strong>Floor </strong></td><td>: <? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
	            <td><strong>Line </strong></td><td><? echo ": ".$line; ?></td>
	            <td><strong>Organic</strong></td><td> : <? echo $dataArray[0][csf('organic')]; ?></td>
	        </tr>
	        <tr>
	         <td><strong>Body Part  </strong></td><td>: <? echo $body_part_arr[$dataArray[0][csf('body_part')]]; ?></td>

	        </tr>
	       <tr>


	        </tr>

	    </table>
	         <br>
	        <?

	        $delivery_mst_id =$dataArray[0][csf('id')];
	       //$cut_no_all=sql_select("select listagg(cast(a.cut_no as varchar2(4000)),',') within group (order by a.cut_no) as cut_no from pro_garments_production_dtls a  where a.status_active=1 and a.is_deleted=0 and  a.delivery_mst_id='$delivery_mst_id' ");
	        $cut_nos_all=sql_select("select cut_no from pro_garments_production_dtls a  where a.status_active=1 and a.is_deleted=0 and  a.delivery_mst_id='$delivery_mst_id' and production_type=4 group by cut_no");
	        $cut_number_string="";
	        foreach($cut_nos_all as $cut_val)
	        {
	            if($cut_number_string=="")
	            {
	                $val=$cut_val[csf('cut_no')];
	                 $cut_number_string.="'$val'";
	            }
	            else
	            {
	                $val=$cut_val[csf('cut_no')];
	                $cut_number_string.=','."'$val'";
	            }
	        }
	         //echo $cut_number_string;


	               /* $sqls="SELECT b.cut_no, b.production_qnty, a.country_id, d.color_number_id, d.size_number_id, e.style_ref_no, e.style_description, e.job_no, e.buyer_name,f.po_number,f.id as po_id, f.po_quantity,h.order_cut_no,b.bundle_no
	                from
	                    pro_garments_production_mst a,
	                    pro_garments_production_dtls b,
	                    wo_po_color_size_breakdown d,
	                    wo_po_details_master e,
	                    wo_po_break_down f,
	                    ppl_cut_lay_mst g,
	                    ppl_cut_lay_dtls h,
	                    ppl_cut_lay_bundle i
	                where
	                    a.delivery_mst_id ='$data[1]'

	                    and e.job_no=f.job_no_mst
	                    and f.id=a.po_break_down_id
	                    and a.id=b.mst_id
	                    and b.color_size_break_down_id=d.id
	                    and d.status_active=1
	                    and d.is_deleted=0
	                    and g.id=h.mst_id
	                    and i.mst_id=g.id
	                    and i.dtls_id=h.id
	                    and i.bundle_no=b.bundle_no
	                    and g.cutting_no=b.cut_no
	                    and h.color_id=d.color_number_id
	                    and g.status_active=1 and g.is_deleted=0 and h.status_active=1 and h.is_deleted=0
	                    and a.status_active=1 and a.is_deleted=0
	                    and b.status_active=1
	                    and b.is_deleted=0
	                    and f.status_active=1
	                    and f.is_deleted=0
	                     and d.status_active=1
	                    and d.is_deleted=0
	                    and e.status_active=1
	                    and e.is_deleted=0
	                    and i.status_active=1
	                    and i.is_deleted=0
	                order by e.job_no,d.size_order";*/
	                $sqls="SELECT a.item_number_id, b.cut_no, b.production_qnty, a.country_id, d.color_number_id, d.size_number_id, e.style_ref_no, e.style_description, e.job_no, e.buyer_name,f.po_number,f.id as po_id, f.po_quantity,b.bundle_no,f.file_no,f.grouping from  pro_garments_production_mst a, pro_garments_production_dtls b,wo_po_color_size_breakdown d, wo_po_details_master e,wo_po_break_down f  where
	                    a.delivery_mst_id ='$data[1]' and a.production_type=4 and b.production_type=4  and e.id=f.job_id  and f.id=a.po_break_down_id and e.id=d.job_id
	                    and a.id=b.mst_id  and b.color_size_break_down_id=d.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1
	                    and b.is_deleted=0  and f.status_active=1  and f.is_deleted=0 and d.status_active in(1,2,3)
	                    and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 order by e.job_no,d.size_order";

	               $result=sql_select($sqls);
	               //unset($sqls);
	              /*  $cut_nos=$cut_no_all[0][csf("cut_no")];
	               $cut_nos=explode(",", $cut_nos);
	               $cut_no_string="";
	               foreach($cut_nos as $val)
	               {
	                if($cut_no_string=="") {$cut_no_string.="'$val'";}
	                else {$cut_no_string.=','."'$val'";}

	               }*/

	            $order_cut_no_sql=sql_select("select b.order_ids as po,b.order_cut_no as ord_cut,cutting_no from ppl_cut_lay_mst a,ppl_cut_lay_dtls b where a.id=b.mst_id and a.cutting_no in ($cut_number_string)");
	            foreach($order_cut_no_sql as $rows)
	            {
	                 if(strpos($rows[csf("po")], ",")==false)
	                 {
	                     $order_cut_no_arr[$rows[csf("cutting_no")]][$rows[csf("po")]]=$rows[csf("ord_cut")];
	                 }
	                 else
	                 {
	                    $po_ids=$rows[csf("po")];
	                    $po_ids=explode(",", $po_ids);
	                    foreach($po_ids as $po_val)
	                    {
	                        $order_cut_no_arr[$rows[csf("cutting_no")]][$po_val]=$rows[csf("ord_cut")];
	                    }
	                 }

	            }

	             $batch_sql="select a.roll_data, b.bundle_no,b.roll_no from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b,ppl_cut_lay_mst c where c.id=a.mst_id and c.id= b.mst_id and a.id=b.dtls_id   and a.status_active=1 and a.is_deleted=0 and c.cutting_no in ($cut_number_string ) group by  a.roll_data, b.bundle_no,b.roll_no";


	                 //echo $batch_sql;die;
	                     $batcharr_sql=sql_select($batch_sql);
	                    $batch_array=array();
	                    foreach($batcharr_sql as $row )
	                    {
	                        $roll_data=explode("**",$row[csf("roll_data")]);
	                        foreach ($roll_data as $roll_data_value)
	                        {
	                            $roll_data_single_row=explode("=",$roll_data_value);
	                            if ($roll_data_single_row[1]==$row[csf("roll_no")]) {
	                              $batch_array[$row[csf("bundle_no")]] .=",".$roll_data_single_row[5];
	                          }
	                        }


	                    }
	                      /* echo "<pre>";
	            print_r($batch_array);
	            echo "</pre>";
	                      */

	                foreach($result as $rows)
	                {

	                    $key=$rows[csf('country_id')].$rows[csf('buyer_name')].$rows[csf('job_no')].$rows[csf('po_id')].$rows[csf('color_number_id')].$rows[csf('style_ref_no')].$rows[csf('style_description')].$rows[csf('cut_no')].$rows[csf('order_cut_no')];

	                    $bundle_size_arr[$rows[csf('size_number_id')]]=$rows[csf('size_number_id')];
	                    $dataArr[$key]=array(
	                        country_id=>$rows[csf('country_id')],
	                        buyer_name=>$rows[csf('buyer_name')],
	                        po_id=>$rows[csf('po_id')],
	                        po_number=>$rows[csf('po_number')],
	                        item_number_id=>$rows[csf('item_number_id')],
	                        color_number_id=>$rows[csf('color_number_id')],
	                        size_number_id=>$rows[csf('size_number_id')],
	                        style_ref_no=>$rows[csf('style_ref_no')],
	                        style_description=>$rows[csf('style_description')],
	                        job_no=>$rows[csf('job_no')],
	                        cut_no=>$rows[csf('cut_no')],
                            order_cut_no=>$rows[csf('order_cut_no')],
                            file_no=>$rows[csf('file_no')],
	                        grouping=>$rows[csf('grouping')]
	                    );
	                    $batch_wise_qty_array[$batch_array[$rows[csf("bundle_no")]]]+=$rows[csf('production_qnty')];
	                    $productionQtyArr[$key]+=$rows[csf('production_qnty')];
	                    $sizeQtyArr[$key][$rows[csf('size_number_id')]]+=$rows[csf('production_qnty')];
	                    $bundleArr[$key][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];

	                }


	            $table_width=1060+(count($bundle_size_arr)*50);
	        ?>
	    <div style="width:100%;">
	    <table align="left" cellspacing="0" width="<? echo $table_width; ?>"  border="1" rules="all" class="rpt_table" style=" margin-top:20px;" >
	        <thead bgcolor="#dddddd" align="center">
	            <tr>
	                <th width="30" rowspan="2">SL</th>
	                <th width="80" align="center" rowspan="2">Buyer</th>
	                <th width="80" align="center" rowspan="2">Job No</th>
                    <th width="80" align="center" rowspan="2">Style Ref</th>
                    <th width="80" align="center" rowspan="2">File No</th>
	                <th width="80" align="center" rowspan="2">Ref No</th>
	                <th width="80" align="center" rowspan="2">PO Number</th>
	                <th width="80" align="center" rowspan="2">Garments Item</th>
	                <th width="100" align="center" rowspan="2">Style Des</th>
	                <th width="80" align="center" rowspan="2">Country</th>
	                <th width="80" align="center" rowspan="2">Color</th>
	                <th width="80" align="center" rowspan="2">Cutting No</th>
	                <th width="80" align="center" rowspan="2">Order Cut</th>
	                <th align="center" width="50" colspan="<? echo count($bundle_size_arr); ?>">Size</th>
	                <th width="80" align="center" rowspan="2">Total Issue Qty</th>
	                <th width="80" align="center" rowspan="2">No of Bundle</th>
	                <th width= align="center" rowspan="2">Remarks</th>
	              </tr>
	              <tr>
	                <?
	                $i=0;
	                foreach($bundle_size_arr as $inf)
	                {
	                ?>
	                <th align="center" width="50" rowspan="2"><? echo $size_library[$inf]; ?></th>
	                <?
	                }
	                ?>
	             </tr>
	        </thead>
	        <tbody>
	            <?
	            $i=1;
	            $tot_qnty=array();
	            foreach($dataArr as $key=>$row)
	            {

	                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
	                        <td><? echo $i;  ?></td>
	                        <td align="center"><? echo $buyer_arr[$row['buyer_name']]; ?></td>
	                        <td align="center"><? echo $row['job_no']; ?></td>
                            <td align="center"><? echo $row['style_ref_no']; ?></td>
                            <td align="center"><? echo $row['file_no']; ?></td>
	                        <td align="center"><? echo $row['grouping']; ?></td>
	                        <td align="center"><? echo $row['po_number'];?></td>
	                        <td align="center"><? echo $garments_item[$row['item_number_id']];?></td>
	                        <td align="center"><? echo $row['style_description']; ?></td>
	                        <td align="center"><? echo $country_library[$row['country_id']]; ?></td>
	                        <td align="center"><? echo $color_library[$row['color_number_id']]; ?></td>
	                        <td align="center"><? echo $row['cut_no']; ?></td>
	                         <td align="center"><? echo $order_cut_no_arr[$row['cut_no']][$row['po_id']]; ?></td>

	                        <?
	                        foreach($bundle_size_arr as $size_id)
	                        {
	                            $size_qty=0;
	                            $size_qty=$sizeQtyArr[$key][$size_id];
	                            ?>
	                            <td align="center" width="50"><? echo $size_qty; ?></td>
	                            <?
	                            $grand_total_size_arr[$size_id]+=$size_qty;
	                        }
	                        ?>
	                        <td align="center"><? echo $productionQtyArr[$key]; ?></td>
	                        <td align="center"><? echo count($bundleArr[$key]); ?></td>
	                        <td align="center"> <?  echo $dataArray[0][csf('remarks')]; ?></td>
	                    </tr>
	                    <?
	                    $i++;
	                    $grand_total_qty+=$productionQtyArr[$key];
	                    $grand_total_bundle_num+=count($bundleArr[$key]);
	                    $grand_total_reject_qty+=$val['reject_qty'];

	           }
	           ?>
	        </tbody>
	        <tr bgcolor="#DDDDDD">

	            <td colspan="13" align="right"><strong>Grand Total :</strong></td>
	            <?
	            foreach($bundle_size_arr as $size_id)
	            {
	                ?>
	                <td align="center" width="50"><? echo $grand_total_size_arr[$size_id]; ?></td>
	                <?
	            }
	            ?>
	             <td align="center"><? echo $grand_total_qty; ?></td>
	            <td align="center"><? echo $grand_total_bundle_num; ?></td>
	            <td  align="center"><?  //echo $val[csf('num_of_bundle')]; ?></td>
	        </tr>
	    </table>
	 </div> &nbsp; <br>

	 <table align="left" cellspacing="0"  border="1" rules="all" class="rpt_table" style=" margin-top:50px; width: 400px; margin-left: 50px;"  >
	        <thead bgcolor="#dddddd" align="center">
	            <tr>
	                <th width="200">Lot/Batch Number</th>
	                <th width="100">Qty</th>
	                <th width="100">UOM</th>
	            </tr>
	        </thead>
	        <tbody>
	        <?

	            $j=1;
	           /* echo "<pre>";
	            print_r($batch_wise_qty_array);
	            echo "</pre>";*/
	            foreach($batch_wise_qty_array as $batch=>$batch_qty)
	            {
	                $bgcolor=($j%2==0)?"#E9F3FF":"#FFFFFF";
	                $batch=array_unique(explode(",",ltrim($batch,',')));

	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
	                        <td width="200" align="center"><? echo implode(",",$batch); ?></td>
	                        <td width="100" align="center"><? echo $batch_qty; ?></td>
	                        <td width="100" align="center">Pcs</td>
	                    </tr>
	                <?
	            $j++;
	            $total_batch_qty+=$batch_qty;
	            }
	            ?>
	        </tbody>
	            <tr bgcolor="#DDDDDD">
	                <td width="200" align="right"><strong>Total:</strong></td>
	                <td width="100" align="center"><? echo $total_batch_qty; ?></td>
	                <td width="100" align="center">Pcs</td>
	            </tr>
	    </table>

	</div>
	     <?
	        //echo signature_table(226, $data[0], "900px");
	     ?>
	     <br>
	</div>



	        <br>
	         <?
	            echo signature_table(226, $data[0], "1110px");
	         ?>
	    </div>
	    <script type="text/javascript" src="../../js/jquery.js"></script>
	    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
	    function generateBarcode( valuess ){
	            var value = valuess;//$("#barcodeValue").val();
	            var btype = 'code39';//$("input[name=btype]:checked").val();
	            var renderer ='bmp';// $("input[name=renderer]:checked").val();
	            var settings = {
	              output:renderer,
	              bgColor: '#FFFFFF',
	              color: '#000000',
	              barWidth: 1,
	              barHeight: 30,
	              moduleSize:5,
	              posX: 10,
	              posY: 20,
	              addQuietZone: 1
	            };

	             value = {code:value, rect: false};
	            $("#barcode_img_id").show().barcode(value, btype, settings);
	        }
	        generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
	     </script>
	<?


        if($is_mail_send==1){
            $emailBody=ob_get_contents();

            $sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=120 and  b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
            $mail_sql_res=sql_select($sql);

            $mailArr=array();
            foreach($mail_sql_res as $row)
            {
                $mailArr[$row['EMAIL']]=$row['EMAIL'];
            }

            if($mail_id!=''){$mailArr[]=$mail_id;}


            $to=implode(',',$mailArr);
            $subject="Bundle Wise Delivery to Sewing Input Challan/Gate Pass";

            if($to!=""){
                include('../../auto_mail/setting/mail_setting.php');
                $header=mailHeader();
                echo sendMailMailer( $to, $subject, $mail_body."<br><br>".$emailBody,$from_mail,'' );
            }
        }


	exit();

}

if($action=="sewing_input_challan_print7")
{

    extract($_REQUEST);
    $data=explode('*',$data);
    $cbo_company_name = $data[0]; $is_mail_send = $data[4]; $mail_id = $data[5]; $mail_body = $data[6];if( $is_mail_send == 1){ob_start();}
    // print_r ($data);
    $company_library=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    $supplier_library=return_library_array( "select id,supplier_name from  lib_supplier where status_active=1 and is_deleted=0", "id","supplier_name");
    $location_library=return_library_array( "select id, location_name from  lib_location where status_active=1 and is_deleted=0", "id", "location_name");
    $floor_library=return_library_array( "select id, floor_name from  lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
    $color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0 ", "id", "color_name" );
    $size_library=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
    $line_library = return_library_array("select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0", "id", "line_name");
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
    $body_part_arr=return_library_array( "select id,bundle_use_for from ppl_bundle_title where company_id=$data[0]",'id','bundle_use_for');

    $company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name, group_id, vat_number from lib_company where status_active=1 and  is_deleted=0");
	$group_com_arr_lib = return_library_array("select id, group_name from lib_group where is_deleted=0 and status_active=1 order by group_name", 'id', 'group_name');

	//select group_name,id from lib_group where is_deleted=0 and status_active=1 order by group_name", "id,group_name"
	foreach ($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
		$company_array[$row[csf('id')]]['group_id'] = $row[csf('group_id')];
		$company_array[$row[csf('id')]]['vat_number'] = $row[csf('vat_number')];
	}

    $sql="select id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
    production_source, serving_company, floor_id, sewing_line, organic, delivery_date,working_company_id,working_location_id,remarks from pro_gmts_delivery_mst where production_type=4 and id='$data[1]' and
    status_active=1 and is_deleted=0 ";
    $dataArray=sql_select($sql);

    $nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='" . $dataArray[0][csf('company_id')] . "' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];

    if ($prod_reso_allocation == 1) {

        if ($dataArray[0][csf('floor_id')] == 0 && $dataArray[0][csf('location_id')] != 0) $cond = " and a.location_id= {$dataArray[0][csf('location_id')]}";
        if ($dataArray[0][csf('floor_id')] != 0) $cond = " and a.floor_id= {$dataArray[0][csf('floor_id')]}";

        if ($db_type == 0) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
        } else if ($db_type == 2 || $db_type == 1) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
        }

        foreach ($line_data as $row) {
            $line = '';
            $line_number = explode(",", $row[csf('line_number')]);
            foreach ($line_number as $val) {

                if ($line == '') $line = $line_library[$val]; else $line .= "," . $line_library[$val];
            }
            $line_array[$row[csf('id')]] = $line;
        }
        $line = $line_array[$dataArray[0][csf('sewing_line')]];
    } else {
        $line = $line_library[$dataArray[0][csf('sewing_line')]];
    }

	?>
	<div style="width:900px;">
	    <table cellspacing="0" style="font: 11px tahoma; width: 100%;">
            <tr>
                <td colspan="6" align="center" style="font-size:22px">
                    <strong><? echo $group_com_arr_lib[$company_array[$data[0]]['group_id']]; ?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:14px">
                    <strong>Working Company:</strong>
                    <strong><? echo $company_library[$data[4]]; ?>
                    (Location:
                    <? echo $location_library[$dataArray[0][csf('location_id')]]; ?>
                    )
                    </strong>
                </td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:14px">
                    <strong>Working Company Add:</strong>
                    <strong>
                            <?
                                $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[4] and status_active=1 and is_deleted=0");
                                foreach ($nameArray as $result)
                                {
                                    echo $result[csf('city')];
                                }
                            ?>
                    </strong>
                </td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:14px">
                    <strong>Owner Company:</strong>
                    <strong><? echo $company_library[$data[0]]; ?></strong>
                </td>
            </tr>

	        <tr>
	            <td colspan="6" align="center" style="font-size:20px"><u><strong> Sewing Input Challan/Gate Pass</strong></u></td>
	        </tr>
	        <tr>
	            <td width="90"><strong>Challan No</strong></td> <td width="140px">: <? echo $dataArray[0][csf('sys_number')]; ?></td>
	            <td width="110"><strong>Input Date</strong></td><td width="175px"> : <? echo ": ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
	            <td width="105"><strong>Barcode</strong></td><td  colspan="4" id="barcode_img_id"></td>
	        </tr>
	        <tr>

	            <td><strong>Sewing Source</strong></td><td>:
	                <?
	                    echo $knitting_source[$dataArray[0][csf('production_source')]];


	                ?>
	            </td>
	            <td><strong>Sewing Company</strong></td><td>: <? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]];
	                    else echo $supplier_library[$dataArray[0][csf('serving_company')]]; ?> </td>
	            <td><strong>Location</strong></td><td>: <? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
	        </tr>
	        <tr>
	            <td><strong>Floor </strong></td><td>: <? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
	            <td><strong>Line </strong></td><td><? echo ": ".$line; ?></td>
	            <td><strong>Organic</strong></td><td> : <? echo $dataArray[0][csf('organic')]; ?></td>
	        </tr>
	        <tr>
	         <td><strong>Body Part  </strong></td><td>: <? echo $body_part_arr[$dataArray[0][csf('body_part')]]; ?></td>

	        </tr>
	       <tr>


	        </tr>

	    </table>
	         <br>
	        <?

	        $delivery_mst_id =$dataArray[0][csf('id')];
	       //$cut_no_all=sql_select("select listagg(cast(a.cut_no as varchar2(4000)),',') within group (order by a.cut_no) as cut_no from pro_garments_production_dtls a  where a.status_active=1 and a.is_deleted=0 and  a.delivery_mst_id='$delivery_mst_id' ");
	        $cut_nos_all=sql_select("select cut_no from pro_garments_production_dtls a  where a.status_active=1 and a.is_deleted=0 and  a.delivery_mst_id='$delivery_mst_id' and production_type=4 group by cut_no");
	        $cut_number_string="";
	        foreach($cut_nos_all as $cut_val)
	        {
	            if($cut_number_string=="")
	            {
	                $val=$cut_val[csf('cut_no')];
	                 $cut_number_string.="'$val'";
	            }
	            else
	            {
	                $val=$cut_val[csf('cut_no')];
	                $cut_number_string.=','."'$val'";
	            }
	        }
	         //echo $cut_number_string;


	               /* $sqls="SELECT b.cut_no, b.production_qnty, a.country_id, d.color_number_id, d.size_number_id, e.style_ref_no, e.style_description, e.job_no, e.buyer_name,f.po_number,f.id as po_id, f.po_quantity,h.order_cut_no,b.bundle_no
	                from
	                    pro_garments_production_mst a,
	                    pro_garments_production_dtls b,
	                    wo_po_color_size_breakdown d,
	                    wo_po_details_master e,
	                    wo_po_break_down f,
	                    ppl_cut_lay_mst g,
	                    ppl_cut_lay_dtls h,
	                    ppl_cut_lay_bundle i
	                where
	                    a.delivery_mst_id ='$data[1]'

	                    and e.job_no=f.job_no_mst
	                    and f.id=a.po_break_down_id
	                    and a.id=b.mst_id
	                    and b.color_size_break_down_id=d.id
	                    and d.status_active=1
	                    and d.is_deleted=0
	                    and g.id=h.mst_id
	                    and i.mst_id=g.id
	                    and i.dtls_id=h.id
	                    and i.bundle_no=b.bundle_no
	                    and g.cutting_no=b.cut_no
	                    and h.color_id=d.color_number_id
	                    and g.status_active=1 and g.is_deleted=0 and h.status_active=1 and h.is_deleted=0
	                    and a.status_active=1 and a.is_deleted=0
	                    and b.status_active=1
	                    and b.is_deleted=0
	                    and f.status_active=1
	                    and f.is_deleted=0
	                     and d.status_active=1
	                    and d.is_deleted=0
	                    and e.status_active=1
	                    and e.is_deleted=0
	                    and i.status_active=1
	                    and i.is_deleted=0
	                order by e.job_no,d.size_order";*/
	                $sqls="SELECT a.item_number_id, b.cut_no, b.production_qnty, a.country_id, d.color_number_id, d.size_number_id, e.style_ref_no, e.style_description, e.job_no, e.buyer_name,f.po_number,f.id as po_id, f.po_quantity,b.bundle_no,f.file_no,f.grouping from  pro_garments_production_mst a, pro_garments_production_dtls b,wo_po_color_size_breakdown d, wo_po_details_master e,wo_po_break_down f  where
	                    a.delivery_mst_id ='$data[1]' and a.production_type=4 and b.production_type=4  and e.id=f.job_id  and f.id=a.po_break_down_id and e.id=d.job_id
	                    and a.id=b.mst_id  and b.color_size_break_down_id=d.id  and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
	                    and b.is_deleted=0  and f.status_active=1  and f.is_deleted=0 and d.status_active=1
	                    and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 order by e.job_no,d.size_order";

	               $result=sql_select($sqls);
	               //unset($sqls);
	              /*  $cut_nos=$cut_no_all[0][csf("cut_no")];
	               $cut_nos=explode(",", $cut_nos);
	               $cut_no_string="";
	               foreach($cut_nos as $val)
	               {
	                if($cut_no_string=="") {$cut_no_string.="'$val'";}
	                else {$cut_no_string.=','."'$val'";}

	               }*/

	            $order_cut_no_sql=sql_select("select b.order_ids as po,b.order_cut_no as ord_cut,cutting_no from ppl_cut_lay_mst a,ppl_cut_lay_dtls b where a.id=b.mst_id and a.cutting_no in ($cut_number_string)");
	            foreach($order_cut_no_sql as $rows)
	            {
	                 if(strpos($rows[csf("po")], ",")==false)
	                 {
	                     $order_cut_no_arr[$rows[csf("cutting_no")]][$rows[csf("po")]]=$rows[csf("ord_cut")];
	                 }
	                 else
	                 {
	                    $po_ids=$rows[csf("po")];
	                    $po_ids=explode(",", $po_ids);
	                    foreach($po_ids as $po_val)
	                    {
	                        $order_cut_no_arr[$rows[csf("cutting_no")]][$po_val]=$rows[csf("ord_cut")];
	                    }
	                 }

	            }

	             $batch_sql="select a.roll_data, b.bundle_no,b.roll_no from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b,ppl_cut_lay_mst c where c.id=a.mst_id and c.id= b.mst_id and a.id=b.dtls_id   and a.status_active=1 and a.is_deleted=0 and c.cutting_no in ($cut_number_string ) group by  a.roll_data, b.bundle_no,b.roll_no";


	                 //echo $batch_sql;die;
	                     $batcharr_sql=sql_select($batch_sql);
	                    $batch_array=array();
	                    foreach($batcharr_sql as $row )
	                    {
	                        $roll_data=explode("**",$row[csf("roll_data")]);
	                        foreach ($roll_data as $roll_data_value)
	                        {
	                            $roll_data_single_row=explode("=",$roll_data_value);
	                            if ($roll_data_single_row[1]==$row[csf("roll_no")]) {
	                              $batch_array[$row[csf("bundle_no")]] .=",".$roll_data_single_row[5];
	                          }
	                        }


	                    }
	                      /* echo "<pre>";
	            print_r($batch_array);
	            echo "</pre>";
	                      */

	                foreach($result as $rows)
	                {

	                    $key=$rows[csf('country_id')].$rows[csf('buyer_name')].$rows[csf('job_no')].$rows[csf('po_id')].$rows[csf('color_number_id')].$rows[csf('style_ref_no')].$rows[csf('style_description')].$rows[csf('cut_no')].$rows[csf('order_cut_no')];

	                    $bundle_size_arr[$rows[csf('size_number_id')]]=$rows[csf('size_number_id')];
	                    $dataArr[$key]=array(
	                        country_id=>$rows[csf('country_id')],
	                        buyer_name=>$rows[csf('buyer_name')],
	                        po_id=>$rows[csf('po_id')],
	                        po_number=>$rows[csf('po_number')],
	                        item_number_id=>$rows[csf('item_number_id')],
	                        color_number_id=>$rows[csf('color_number_id')],
	                        size_number_id=>$rows[csf('size_number_id')],
	                        style_ref_no=>$rows[csf('style_ref_no')],
	                        style_description=>$rows[csf('style_description')],
	                        job_no=>$rows[csf('job_no')],
	                        cut_no=>$rows[csf('cut_no')],
                            order_cut_no=>$rows[csf('order_cut_no')],
                            file_no=>$rows[csf('file_no')],
	                        grouping=>$rows[csf('grouping')]
	                    );
	                    $batch_wise_qty_array[$batch_array[$rows[csf("bundle_no")]]]+=$rows[csf('production_qnty')];
	                    $productionQtyArr[$key]+=$rows[csf('production_qnty')];
	                    $sizeQtyArr[$key][$rows[csf('size_number_id')]]+=$rows[csf('production_qnty')];
	                    $bundleArr[$key][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];

	                }


	            $table_width=1060+(count($bundle_size_arr)*50);
	        ?>
	    <div style="width:100%;">
	    <table align="left" cellspacing="0" width="<? echo $table_width; ?>"  border="1" rules="all" class="rpt_table" style=" margin-top:20px;" >
	        <thead bgcolor="#dddddd" align="center">
	            <tr>
	                <th width="30" rowspan="2">SL</th>
	                <th width="80" align="center" rowspan="2">Buyer</th>
	                <th width="80" align="center" rowspan="2">Job No</th>
                    <th width="80" align="center" rowspan="2">Style Ref</th>
                    <th width="80" align="center" rowspan="2">File No</th>
	                <th width="80" align="center" rowspan="2">Ref No</th>
	                <th width="80" align="center" rowspan="2">PO Number</th>
	                <th width="80" align="center" rowspan="2">Garments Item</th>
	                <th width="100" align="center" rowspan="2">Style Des</th>
	                <th width="80" align="center" rowspan="2">Country</th>
	                <th width="80" align="center" rowspan="2">Color</th>
	                <th width="80" align="center" rowspan="2">Cutting No</th>
	                <th width="80" align="center" rowspan="2">Order Cut</th>
	                <th align="center" width="50" colspan="<? echo count($bundle_size_arr); ?>">Size</th>
	                <th width="80" align="center" rowspan="2">Total Issue Qty</th>
	                <th width="80" align="center" rowspan="2">No of Bundle</th>
	                <th width= align="center" rowspan="2">Remarks</th>
	              </tr>
	              <tr>
	                <?
	                $i=0;
	                foreach($bundle_size_arr as $inf)
	                {
	                ?>
	                <th align="center" width="50" rowspan="2"><? echo $size_library[$inf]; ?></th>
	                <?
	                }
	                ?>
	             </tr>
	        </thead>
	        <tbody>
	            <?
	            $i=1;
	            $tot_qnty=array();
	            foreach($dataArr as $key=>$row)
	            {

	                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
	                        <td><? echo $i;  ?></td>
	                        <td align="center"><? echo $buyer_arr[$row['buyer_name']]; ?></td>
	                        <td align="center"><? echo $row['job_no']; ?></td>
                            <td align="center"><? echo $row['style_ref_no']; ?></td>
                            <td align="center"><? echo $row['file_no']; ?></td>
	                        <td align="center"><? echo $row['grouping']; ?></td>
	                        <td align="center"><? echo $row['po_number'];?></td>
	                        <td align="center"><? echo $garments_item[$row['item_number_id']];?></td>
	                        <td align="center"><? echo $row['style_description']; ?></td>
	                        <td align="center"><? echo $country_library[$row['country_id']]; ?></td>
	                        <td align="center"><? echo $color_library[$row['color_number_id']]; ?></td>
	                        <td align="center"><? echo $row['cut_no']; ?></td>
	                         <td align="center"><? echo $order_cut_no_arr[$row['cut_no']][$row['po_id']]; ?></td>

	                        <?
	                        foreach($bundle_size_arr as $size_id)
	                        {
	                            $size_qty=0;
	                            $size_qty=$sizeQtyArr[$key][$size_id];
	                            ?>
	                            <td align="center" width="50"><? echo $size_qty; ?></td>
	                            <?
	                            $grand_total_size_arr[$size_id]+=$size_qty;
	                        }
	                        ?>
	                        <td align="center"><? echo $productionQtyArr[$key]; ?></td>
	                        <td align="center"><? echo count($bundleArr[$key]); ?></td>
	                        <td align="center"><?  echo $dataArray[0][csf('remarks')]; ?></td>
	                    </tr>
	                    <?
	                    $i++;
	                    $grand_total_qty+=$productionQtyArr[$key];
	                    $grand_total_bundle_num+=count($bundleArr[$key]);
	                    $grand_total_reject_qty+=$val['reject_qty'];

	           }
	           ?>
	        </tbody>
	        <tr bgcolor="#DDDDDD">

	            <td colspan="13" align="right"><strong>Grand Total :</strong></td>
	            <?
	            foreach($bundle_size_arr as $size_id)
	            {
	                ?>
	                <td align="center" width="50"><? echo $grand_total_size_arr[$size_id]; ?></td>
	                <?
	            }
	            ?>
	             <td align="center"><? echo $grand_total_qty; ?></td>
	            <td align="center"><? echo $grand_total_bundle_num; ?></td>
	            <td  align="center"><?  //echo $val[csf('num_of_bundle')]; ?></td>
	        </tr>
	    </table>
	 </div> &nbsp; <br>

	 <table align="left" cellspacing="0"  border="1" rules="all" class="rpt_table" style=" margin-top:50px; width: 400px; margin-left: 50px;"  >
	        <thead bgcolor="#dddddd" align="center">
	            <tr>
	                <th width="200">Lot/Batch Number</th>
	                <th width="100">Qty</th>
	                <th width="100">UOM</th>
	            </tr>
	        </thead>
	        <tbody>
	        <?

	            $j=1;
	           /* echo "<pre>";
	            print_r($batch_wise_qty_array);
	            echo "</pre>";*/
	            foreach($batch_wise_qty_array as $batch=>$batch_qty)
	            {
	                $bgcolor=($j%2==0)?"#E9F3FF":"#FFFFFF";
	                $batch=array_unique(explode(",",ltrim($batch,',')));

	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
	                        <td width="200" align="center"><? echo implode(",",$batch); ?></td>
	                        <td width="100" align="center"><? echo $batch_qty; ?></td>
	                        <td width="100" align="center">Pcs</td>
	                    </tr>
	                <?
	            $j++;
	            $total_batch_qty+=$batch_qty;
	            }
	            ?>
	        </tbody>
	            <tr bgcolor="#DDDDDD">
	                <td width="200" align="right"><strong>Total:</strong></td>
	                <td width="100" align="center"><? echo $total_batch_qty; ?></td>
	                <td width="100" align="center">Pcs</td>
	            </tr>
	    </table>

	</div>
	     <?
	        //echo signature_table(226, $data[0], "900px");
	     ?>
	     <br>
	</div>



	        <br>
	         <?
	            echo signature_table(226, $data[0], "1110px");
	         ?>
	    </div>
	    <script type="text/javascript" src="../../js/jquery.js"></script>
	    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
	    function generateBarcode( valuess ){
	            var value = valuess;//$("#barcodeValue").val();
	            var btype = 'code39';//$("input[name=btype]:checked").val();
	            var renderer ='bmp';// $("input[name=renderer]:checked").val();
	            var settings = {
	              output:renderer,
	              bgColor: '#FFFFFF',
	              color: '#000000',
	              barWidth: 1,
	              barHeight: 30,
	              moduleSize:5,
	              posX: 10,
	              posY: 20,
	              addQuietZone: 1
	            };

	             value = {code:value, rect: false};
	            $("#barcode_img_id").show().barcode(value, btype, settings);
	        }
	        generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
	     </script>
	<?

    if($is_mail_send==1){
        $emailBody=ob_get_contents();

        $sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=120 and  b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
        $mail_sql_res=sql_select($sql);

        $mailArr=array();
        foreach($mail_sql_res as $row)
        {
            $mailArr[$row['EMAIL']]=$row['EMAIL'];
        }

        if($mail_id!=''){$mailArr[]=$mail_id;}


        $to=implode(',',$mailArr);
        $subject="Bundle Wise Delivery to Sewing Input Challan/Gate Pass";

        if($to!=""){
            include('../../auto_mail/setting/mail_setting.php');
            $header=mailHeader();
            echo sendMailMailer( $to, $subject, $mail_body."<br><br>".$emailBody,$from_mail,'' );
        }
    }

	exit();

}


if($action=="sewing_input_challan_print_5")
{

    extract($_REQUEST);
    $data=explode('*',$data);
    $cbo_company_name = $data[0]; $is_mail_send = $data[4]; $mail_id = $data[5]; $mail_body = $data[6];if( $is_mail_send == 1){ob_start();}
    // print_r($data);
    $company_library=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    $supplier_library=return_library_array( "select id,supplier_name from  lib_supplier where status_active=1 and is_deleted=0", "id","supplier_name");
    $location_library=return_library_array( "select id, location_name from  lib_location where status_active=1 and is_deleted=0", "id", "location_name");
    $floor_library=return_library_array( "select id, floor_name from  lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
    $color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0 ", "id", "color_name" );
    $size_library=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
    $line_library = return_library_array("select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0", "id", "line_name");
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
    $body_part_arr=return_library_array( "select id,bundle_use_for from ppl_bundle_title where company_id=$data[0]",'id','bundle_use_for');



    $sql="SELECT id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
    production_source, serving_company, floor_id, sewing_line, organic, delivery_date,working_company_id,working_location_id from pro_gmts_delivery_mst where production_type=4 and id='$data[1]' and
    status_active=1 and is_deleted=0 ";
    $dataArray=sql_select($sql);

    $nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='" . $dataArray[0][csf('company_id')] . "' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];

    if ($prod_reso_allocation == 1) {

        if ($dataArray[0][csf('floor_id')] == 0 && $dataArray[0][csf('location_id')] != 0) $cond = " and a.location_id= {$dataArray[0][csf('location_id')]}";
        if ($dataArray[0][csf('floor_id')] != 0) $cond = " and a.floor_id= {$dataArray[0][csf('floor_id')]}";

        if ($db_type == 0) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
        } else if ($db_type == 2 || $db_type == 1) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
        }

        foreach ($line_data as $row) {
            $line = '';
            $line_number = explode(",", $row[csf('line_number')]);
            foreach ($line_number as $val) {

                if ($line == '') $line = $line_library[$val]; else $line .= "," . $line_library[$val];
            }
            $line_array[$row[csf('id')]] = $line;
        }
        $line = $line_array[$dataArray[0][csf('sewing_line')]];
    } else {
        $line = $line_library[$dataArray[0][csf('sewing_line')]];
    }

    $cut_floor_lay_arr=array();
	$cut_floor_sql="SELECT a.cutting_no,
	a.floor_id,
	b.delivery_mst_id,
	b.CUT_NO

    FROM ppl_cut_lay_mst a, pro_garments_production_mst b
    WHERE    a.cutting_no= b.CUT_NO
	AND b.delivery_mst_id='$data[1]'
	AND a.status_active = 1
	AND a.is_deleted = 0";
	$cut_floor_sql_data=sql_select($cut_floor_sql);
	foreach($cut_floor_sql_data as $row)
	{
		$cut_floor_lay_arr[$row[csf('cutting_no')]][$row[csf('floor_id')]]=$row[csf('floor_id')];
	}

	?>
	<div style="width:900px;">
	    <table cellspacing="0" style="font: 11px tahoma; width: 100%;">
	        <tr>
	            <td colspan="6" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="6" align="center" style="font-size:14px">
	                <?
	                    $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");


	                    foreach ($nameArray as $result)
	                    {

	                         echo $result[csf('city')];

	                    }
	                ?>
	            </td>
	        </tr>
	        <tr>
	            <td colspan="6" align="center" style="font-size:20px"><u><strong> Sewing Input Challan/Gate Pass</strong></u></td>
	        </tr>
	     <tr>
	            <td width="90"><strong>Challan No</strong></td> <td width="140px">: <? echo $dataArray[0][csf('sys_number')]; ?></td>
	            <td width="110"><strong>Input Date</strong></td><td width="175px"> : <? echo ": ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
	            <td width="105"><strong>Barcode</strong></td><td  colspan="4" id="barcode_img_id"></td>
	        </tr>
	        <tr>

	            <td><strong>Sewing Source</strong></td><td>:
	                <?
	                    echo $knitting_source[$dataArray[0][csf('production_source')]];
	                ?>
	            </td>
	            <td><strong>Sewing Company</strong></td><td>: <? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]];
	                    else echo $supplier_library[$dataArray[0][csf('serving_company')]]; ?> </td>
	            <td><strong>Location</strong></td><td>: <? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
	        </tr>
	        <tr>
	            <td><strong>Floor </strong></td><td>: <? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
	            <td><strong>Line </strong></td><td><? echo ": ".$line; ?></td>
	            <td><strong>Organic</strong></td><td> : <? echo $dataArray[0][csf('organic')]; ?></td>
	        </tr>
	        <tr>
	         <td><strong>Body Part  </strong></td><td>: <? echo $body_part_arr[$dataArray[0][csf('body_part')]]; ?></td>
	         <td><strong>Cutting Floor  </strong></td><td>: <? echo $floor_library[$cut_floor_lay_arr[$row[csf('cutting_no')]][$row[csf('floor_id')]]]; ?> </td>

	        </tr>
	       <tr>
	        </tr>
	    </table>
	         <br>
	        <?

	        $delivery_mst_id =$dataArray[0][csf('id')];
	        $cut_nos_all=sql_select("select cut_no from pro_garments_production_dtls a  where a.status_active=1 and a.is_deleted=0 and  a.delivery_mst_id='$delivery_mst_id' and production_type=4 group by cut_no");
	        $cut_number_string="";
	        foreach($cut_nos_all as $cut_val)
	        {
	            if($cut_number_string=="")
	            {
	                $val=$cut_val[csf('cut_no')];
	                 $cut_number_string.="'$val'";
	            }
	            else
	            {
	                $val=$cut_val[csf('cut_no')];
	                $cut_number_string.=','."'$val'";
	            }
	        }

            $sqls="SELECT b.cut_no, b.production_qnty, a.country_id, d.color_number_id, d.size_number_id, e.style_ref_no, e.style_description, e.job_no, e.buyer_name,f.po_number, f.grouping,f.id as po_id, f.po_quantity,b.bundle_no,d.article_number from  pro_garments_production_mst a, pro_garments_production_dtls b,wo_po_color_size_breakdown d, wo_po_details_master e,wo_po_break_down f  where
                a.delivery_mst_id ='$data[1]' and a.production_type=4 and b.production_type=4  and e.id=f.job_id and e.id=d.job_id  and f.id=a.po_break_down_id
                and a.id=b.mst_id  and b.color_size_break_down_id=d.id  and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
                and b.is_deleted=0  and f.status_active=1  and f.is_deleted=0 and d.status_active=1
                and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 order by e.job_no,d.size_order";
            // echo $sqls; die;
            $result=sql_select($sqls);

                // ==========================FOR CRM 13602  -> 2023 /Mendetory Batch ID=============================
	            $order_cut_no_sql=sql_select("SELECT a.batch_id,b.order_ids as po,b.order_cut_no as ord_cut, b.roll_data, a.cutting_no,c.batch_no from ppl_cut_lay_mst a,ppl_cut_lay_dtls b,pro_batch_create_mst c where a.id=b.mst_id and b.batch_id=c.id and a.cutting_no in ($cut_number_string)");
                $article_arr = array();
	            foreach($order_cut_no_sql as $rows)
	            {
	                 if(strpos($rows[csf("po")], ",")==false)
	                 {
	                     $order_cut_no_arr[$rows[csf("cutting_no")]][$rows[csf("po")]]=$rows[csf("ord_cut")];
	                 }
	                 else
	                 {
	                    $po_ids=$rows[csf("po")];
	                    $po_ids=explode(",", $po_ids);
	                    foreach($po_ids as $po_val)
	                    {
	                        $order_cut_no_arr[$rows[csf("cutting_no")]][$po_val]=$rows[csf("ord_cut")];
	                    }
	                 }

                      $batch_id_arr[$rows[csf("cutting_no")]]=$rows[csf("batch_no")];

	            }

                    // ==========================FOR CRM 26091-> 2023 /Batch ID FROM PLIES POPUP=============================
                   /*  $order_cut_no_sql=sql_select("SELECT a.batch_id,b.order_ids as po,b.order_cut_no as ord_cut, b.roll_data, a.cutting_no from ppl_cut_lay_mst a,ppl_cut_lay_dtls b where a.id=b.mst_id and a.cutting_no in ($cut_number_string)");
                    foreach($order_cut_no_sql as $rows)
                    {
                    if(strpos($rows[csf("po")], ",")==false)
                    {
                        $order_cut_no_arr[$rows[csf("cutting_no")]][$rows[csf("po")]]=$rows[csf("ord_cut")];
                    }
                    else
                    {
                        $po_ids=$rows[csf("po")];
                        $po_ids=explode(",", $po_ids);
                        foreach($po_ids as $po_val)
                        {
                            $order_cut_no_arr[$rows[csf("cutting_no")]][$po_val]=$rows[csf("ord_cut")];
                        }
                    }

                    // $batch_id_arr[$rows[csf("cutting_no")]]=$rows[csf("batch_id")];
                    $roll_data_arr=explode("**",$rows["ROLL_DATA"]);
                    $batch_id_arr2  = array();
                    foreach($roll_data_arr as $val)
                    {
                        $roll_data=explode("=",$val);
                        if (!$batch_id_arr2 [$rows[csf("cutting_no")]][$roll_data[5]]) {
                            $batch_id_arr[$rows[csf("cutting_no")]].=$roll_data[5].",";
                        }

                        $batch_id_arr2 [$rows[csf("cutting_no")]][$roll_data[5]] = $roll_data[5];

                    }



                    }
                    // echo "<pre>"; print_r( $batch_id_arr); die; */



	                foreach($result as $rows)
	                {

	                    $key=$rows[csf('country_id')].$rows[csf('buyer_name')].$rows[csf('job_no')].$rows[csf('po_id')].$rows[csf('color_number_id')].$rows[csf('style_ref_no')].$rows[csf('style_description')].$rows[csf('cut_no')].$rows[csf('order_cut_no')];

	                    $bundle_size_arr[$rows[csf('size_number_id')]]=$rows[csf('size_number_id')];
	                    $dataArr[$key]=array(
	                        'country_id'=>$rows[csf('country_id')],
	                        'buyer_name'=>$rows[csf('buyer_name')],
	                        'po_id'=>$rows[csf('po_id')],
	                        'po_number'=>$rows[csf('po_number')],
                            'grouping'=>$rows[csf('grouping')],
	                        'color_number_id'=>$rows[csf('color_number_id')],
	                        'size_number_id'=>$rows[csf('size_number_id')],
	                        'style_ref_no'=>$rows[csf('style_ref_no')],
	                        'style_description'=>$rows[csf('style_description')],
	                        'job_no'=>$rows[csf('job_no')],
	                        'cut_no'=>$rows[csf('cut_no')],
	                        'order_cut_no'=>$rows[csf('order_cut_no')]

	                    );
	                    // $batch_wise_bndl_array[$key] .= $batch_array[$rows[csf("bundle_no")]].",";

	                    $productionQtyArr[$key]+=$rows[csf('production_qnty')];
	                    $sizeQtyArr[$key][$rows[csf('size_number_id')]]+=$rows[csf('production_qnty')];
	                    $bundleArr[$key][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];

                        $article_arr[$rows['PO_ID']][$rows['COLOR_NUMBER_ID']][$rows['ARTICLE_NUMBER']] = $rows['ARTICLE_NUMBER'];

	                }


	            $table_width=980+(count($bundle_size_arr)*50);
	        ?>
	    <div style="width:100%;">
	    <table align="left" cellspacing="0" width="<? echo $table_width; ?>"  border="1" rules="all" class="rpt_table" style=" margin-top:20px;" >
	        <thead bgcolor="#dddddd" align="center">
	            <tr>
	                <th width="30" rowspan="2">SL</th>
	                <th width="80" align="center" rowspan="2">Buyer</th>
	                <th width="80" align="center" rowspan="2">Job No</th>
	                <th width="80" align="center" rowspan="2">Style Ref</th>
                    <th width="80" align="center" rowspan="2">Internal Ref</th>
	                <th width="80" align="center" rowspan="2">PO Number</th>
	                <th width="80" align="center" rowspan="2">Article No</th>
	                <th width="80" align="center" rowspan="2">Country</th>
	                <th width="80" align="center" rowspan="2">Cutting No</th>
	                <th width="80" align="center" rowspan="2">Order Cut</th>
	                <th width="100" align="center" rowspan="2">Batch No</th>
	                <th width="80" align="center" rowspan="2">Color</th>
	                <th align="center" width="50" colspan="<? echo count($bundle_size_arr); ?>">Size</th>
	                <th width="80" align="center" rowspan="2">Total Issue Qty</th>
	                <th width="80" align="center" rowspan="2">No of Bundle</th>
	                <th width= align="center" rowspan="2">Remarks</th>
	              </tr>
	              <tr>
	                <?
	                $i=0;
	                foreach($bundle_size_arr as $inf)
	                {
	                ?>
	                <th align="center" width="50" rowspan="2"><? echo $size_library[$inf]; ?></th>
	                <?
	                }
	                ?>
	             </tr>
	        </thead>
	        <tbody>
	            <?
	            $i=1;
	            $tot_qnty=array();
	            foreach($dataArr as $key=>$row)
	            {
                   $article_arr2 = $article_arr[$rows['PO_ID']][$rows['COLOR_NUMBER_ID']];
                   $article_no  = implode(',',$article_arr2);
                   $batch_no = implode(",",array_unique(array_filter(explode(",",$batch_wise_bndl_array[$key]))));
	                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" style="font-size:12px">
	                        <td><? echo $i; ?></td>
	                        <td align="center"><? echo $buyer_arr[$row['buyer_name']]; ?></td>
	                        <td align="center"><? echo $row['job_no']; ?></td>
	                        <td align="center"><? echo $row['style_ref_no']; ?></td>
                            <td align="center"><p><? echo $row['grouping']; ?></p></td>
	                        <td align="center"><? echo $row['po_number'];?></td>
	                        <td align="center"><? echo $article_no;?></td>
	                        <td align="center"><? echo $country_library[$row['country_id']]; ?></td>
	                        <td align="center"><? echo $row['cut_no']; ?></td>
	                        <td align="center"><? echo $order_cut_no_arr[$row['cut_no']][$row['po_id']]; ?></td>
	                        <td align="center"><? echo $batch_id_arr[$row['cut_no']]; ?></td>
	                        <td align="center"><? echo $color_library[$row['color_number_id']]; ?></td>
	                        <?
	                        foreach($bundle_size_arr as $size_id)
	                        {
	                            $size_qty=0;
	                            $size_qty=$sizeQtyArr[$key][$size_id];
	                            ?>
	                            <td align="center" width="50"><? echo $size_qty; ?></td>
	                            <?
	                            $grand_total_size_arr[$size_id]+=$size_qty;
	                        }
	                        ?>
	                        <td align="center"><? echo $productionQtyArr[$key]; ?></td>
	                        <td align="center"><? echo count($bundleArr[$key]); ?></td>
	                        <td align="center"></td>
	                    </tr>
	                    <?
	                    $i++;
	                    $grand_total_qty+=$productionQtyArr[$key];
	                    $grand_total_bundle_num+=count($bundleArr[$key]);
	                    $grand_total_reject_qty+=$val['reject_qty'];

	           }
	           ?>
	        </tbody>
	        <tr bgcolor="#DDDDDD">

	            <td colspan="12" align="right"><strong>Grand Total :</strong></td>
	            <?
	            foreach($bundle_size_arr as $size_id)
	            {
	                ?>
	                <td align="center" width="50"><? echo $grand_total_size_arr[$size_id]; ?></td>
	                <?
	            }
	            ?>
	             <td align="center"><? echo $grand_total_qty; ?></td>
	            <td align="center"><? echo $grand_total_bundle_num; ?></td>
	            <td  align="center"><?  //echo $val[csf('num_of_bundle')]; ?></td>
	        </tr>
	    </table>
	 </div> &nbsp; <br>



	</div>
	     <?
	        echo signature_table(226, $data[0], "900px");
	     ?>
	     <br>
	</div>

	    </div>
	    <script type="text/javascript" src="../../js/jquery.js"></script>
	    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
	    function generateBarcode( valuess ){
	            var value = valuess;//$("#barcodeValue").val();
	            var btype = 'code39';//$("input[name=btype]:checked").val();
	            var renderer ='bmp';// $("input[name=renderer]:checked").val();
	            var settings = {
	              output:renderer,
	              bgColor: '#FFFFFF',
	              color: '#000000',
	              barWidth: 1,
	              barHeight: 30,
	              moduleSize:5,
	              posX: 10,
	              posY: 20,
	              addQuietZone: 1
	            };

	             value = {code:value, rect: false};
	            $("#barcode_img_id").show().barcode(value, btype, settings);
	        }
	        generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
	     </script>
	<?


        if($is_mail_send==1){
            $emailBody=ob_get_contents();

            $sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=120 and  b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
            $mail_sql_res=sql_select($sql);

            $mailArr=array();
            foreach($mail_sql_res as $row)
            {
                $mailArr[$row['EMAIL']]=$row['EMAIL'];
            }

            if($mail_id!=''){$mailArr[]=$mail_id;}


            $to=implode(',',$mailArr);
            $subject="Bundle Wise Delivery to Sewing Input Challan/Gate Pass";

            if($to!=""){
                include('../../auto_mail/setting/mail_setting.php');
                $header=mailHeader();
                echo sendMailMailer( $to, $subject, $mail_body."<br><br>".$emailBody,$from_mail,'' );
            }
        }

	exit();

}

if($action=="sewing_input_challan_print_12")
{

    extract($_REQUEST);
    $data=explode('*',$data);
    $cbo_company_name = $data[0]; $is_mail_send = $data[4]; $mail_id = $data[5]; $mail_body = $data[6];if( $is_mail_send == 1){ob_start();}
    // print_r($data);
    $company_library=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    $supplier_library=return_library_array( "select id,supplier_name from  lib_supplier where status_active=1 and is_deleted=0", "id","supplier_name");
    $location_library=return_library_array( "select id, location_name from  lib_location where status_active=1 and is_deleted=0", "id", "location_name");
    $floor_library=return_library_array( "select id, floor_name from  lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
    $color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0 ", "id", "color_name" );
    $size_library=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
    $line_library = return_library_array("select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0", "id", "line_name");
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
    $body_part_arr=return_library_array( "select id,bundle_use_for from ppl_bundle_title where company_id=$data[0]",'id','bundle_use_for');




    $sql="SELECT a.id, a.sys_number_prefix, a.sys_number_prefix_num, a.sys_number, a.company_id, a.production_type, a.location_id, a.delivery_basis, a.embel_name, a.embel_type,
    a.production_source, a.serving_company, a.floor_id, a.sewing_line, a.organic, a.delivery_date,a.working_company_id,a.working_location_id,b.shift_name from pro_gmts_delivery_mst a,pro_garments_production_mst b where a.id=b.delivery_mst_id and a.production_type=4 and b.production_type=4 and a.id='$data[1]' and
    a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
    $dataArray=sql_select($sql);

    $nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='" . $dataArray[0][csf('company_id')] . "' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];

    if ($prod_reso_allocation == 1) {

        if ($dataArray[0][csf('floor_id')] == 0 && $dataArray[0][csf('location_id')] != 0) $cond = " and a.location_id= {$dataArray[0][csf('location_id')]}";
        if ($dataArray[0][csf('floor_id')] != 0) $cond = " and a.floor_id= {$dataArray[0][csf('floor_id')]}";

        if ($db_type == 0) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
        } else if ($db_type == 2 || $db_type == 1) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
        }

        foreach ($line_data as $row) {
            $line = '';
            $line_number = explode(",", $row[csf('line_number')]);
            foreach ($line_number as $val) {

                if ($line == '') $line = $line_library[$val]; else $line .= "," . $line_library[$val];
            }
            $line_array[$row[csf('id')]] = $line;
        }
        $line = $line_array[$dataArray[0][csf('sewing_line')]];
    } else {
        $line = $line_library[$dataArray[0][csf('sewing_line')]];
    }

    $cut_floor_lay_arr=array();
    $cut_floor_sql="SELECT a.cutting_no,
    a.floor_id,
    b.delivery_mst_id,
    b.CUT_NO

    FROM ppl_cut_lay_mst a, pro_garments_production_mst b
    WHERE    a.cutting_no= b.CUT_NO
    AND b.delivery_mst_id='$data[1]'
    AND a.status_active = 1
    AND a.is_deleted = 0";
    $cut_floor_sql_data=sql_select($cut_floor_sql);
    foreach($cut_floor_sql_data as $row)
    {
        $cut_floor_lay_arr[$row[csf('cutting_no')]][$row[csf('floor_id')]]=$row[csf('floor_id')];
    }

    $page_id_arr=[1,2,3];
    $copy_name=array(1=>'st',2=>'nd',3=>'rd',4=>'th');

    foreach($page_id_arr as $vals)
    {
    ?>
    <div style="width:900px;">
        <table cellspacing="0" style="font: 11px tahoma; width: 100%;">
            <tr>
                <td colspan="6" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="6" align="center" style="font-size:14px">
                    <?
                        $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");


                        foreach ($nameArray as $result)
                        {

                             echo $result[csf('city')];

                        }
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:20px"><u><strong> Sewing Input Challan/Gate Pass</strong></u></td>
            </tr>
             <tr>
                <td width="90"><strong>Challan No</strong></td> <td width="140px">: <? echo $dataArray[0][csf('sys_number')]; ?></td>
                <td width="110"><strong>Input Date</strong></td><td width="175px"> : <? echo ": ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
                <td width="105"><strong>Barcode</strong></td><td  colspan="4" id="barcode_img_id_<?php echo $vals;?>"></td>
            </tr>
            <tr>

                <td><strong>Sewing Source</strong></td><td>:
                    <?
                        echo $knitting_source[$dataArray[0][csf('production_source')]];
                    ?>
                </td>
                <td><strong>Sewing Company</strong></td><td>: <? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]];
                        else echo $supplier_library[$dataArray[0][csf('serving_company')]]; ?> </td>
                <td><strong>Location</strong></td><td>: <? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Floor </strong></td><td>: <? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
                <td><strong>Line </strong></td><td><? echo ": ".$line; ?></td>
                <td><strong>Organic</strong></td><td> : <? echo $dataArray[0][csf('organic')]; ?></td>
            </tr>
            <tr>
             <td><strong>Body Part  </strong></td><td>: <? echo $body_part_arr[$dataArray[0][csf('body_part')]]; ?></td>
             <td><strong>Cutting Floor  </strong></td><td>: <? echo $floor_library[$cut_floor_lay_arr[$row[csf('cutting_no')]][$row[csf('floor_id')]]]; ?> </td>
             <td><strong>Shift Name</strong></td><td> : <? echo $shift_name[$dataArray[0][csf('shift_name')]]; ?></td>

            </tr>
        </table>
          <?
            $delivery_mst_id =$dataArray[0][csf('id')];
            $cut_nos_all=sql_select("select cut_no from pro_garments_production_dtls a  where a.status_active=1 and a.is_deleted=0 and  a.delivery_mst_id='$delivery_mst_id' and production_type=4 group by cut_no");
            $cut_number_string="";
            foreach($cut_nos_all as $cut_val)
            {
                if($cut_number_string=="")
                {
                    $val=$cut_val[csf('cut_no')];
                     $cut_number_string.="'$val'";
                }
                else
                {
                    $val=$cut_val[csf('cut_no')];
                    $cut_number_string.=','."'$val'";
                }
            }

            $sqls="SELECT b.cut_no, b.production_qnty, a.country_id, d.color_number_id, d.size_number_id, e.style_ref_no, e.style_description, e.job_no, e.buyer_name,f.po_number, f.grouping,f.id as po_id, f.po_quantity,b.bundle_no,a.item_number_id from  pro_garments_production_mst a, pro_garments_production_dtls b,wo_po_color_size_breakdown d, wo_po_details_master e,wo_po_break_down f  where
                a.delivery_mst_id ='$data[1]' and a.production_type=4 and b.production_type=4  and e.id=f.job_id and e.id=d.job_id  and f.id=a.po_break_down_id
                and a.id=b.mst_id  and b.color_size_break_down_id=d.id  and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
                and b.is_deleted=0  and f.status_active=1  and f.is_deleted=0 and d.status_active=1
                and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 order by e.job_no,d.size_order";

            $result=sql_select($sqls);


                $order_cut_no_sql=sql_select("SELECT a.batch_id,b.order_ids as po,b.order_cut_no as ord_cut, b.roll_data, a.cutting_no from ppl_cut_lay_mst a,ppl_cut_lay_dtls b where a.id=b.mst_id and a.cutting_no in ($cut_number_string)");
                foreach($order_cut_no_sql as $rows)
                {
                     if(strpos($rows[csf("po")], ",")==false)
                     {
                         $order_cut_no_arr[$rows[csf("cutting_no")]][$rows[csf("po")]]=$rows[csf("ord_cut")];
                     }
                     else
                     {
                        $po_ids=$rows[csf("po")];
                        $po_ids=explode(",", $po_ids);
                        foreach($po_ids as $po_val)
                        {
                            $order_cut_no_arr[$rows[csf("cutting_no")]][$po_val]=$rows[csf("ord_cut")];
                        }
                     }

                    //  $batch_id_arr[$rows[csf("cutting_no")]]=$rows[csf("batch_id")];
                    $roll_data_arr=explode("**",$rows["ROLL_DATA"]);
                    foreach($roll_data_arr as $val)
                    {
                        $roll_data=explode("=",$val);
                        $batch_id_arr[$rows[csf("cutting_no")]].=$roll_data[5].",";
                    }



                }

                 $batch_sql="SELECT a.bundle_no,b.batch_no from ppl_cut_lay_bundle a,PRO_ROLL_DETAILS b,ppl_cut_lay_mst c where b.id=a.roll_id and a.mst_id=c.id and a.status_active=1 and a.is_deleted=0 and c.cutting_no in ($cut_number_string )";


                //  echo $batch_sql;die;
                $batcharr_sql=sql_select($batch_sql);
                $batch_array=array();
                $grand_total_size_arr=array();
                $productionQtyArr=array();
                $grand_total_bundle_num="";
                $sizeQtyArr=array();
                foreach($batcharr_sql as $row )
                {
                    $batch_array[$row[csf("bundle_no")]] = $row[csf("batch_no")];
                }

                    // print_r($batch_array);die;
                    foreach($result as $rows)
                    {

                        $key=$rows[csf('country_id')].$rows[csf('buyer_name')].$rows[csf('job_no')].$rows[csf('po_id')].$rows[csf('color_number_id')].$rows[csf('style_ref_no')].$rows[csf('style_description')].$rows[csf('cut_no')].$rows[csf('order_cut_no')];

                        $bundle_size_arr[$rows[csf('size_number_id')]]=$rows[csf('size_number_id')];
                        $dataArr[$key]=array(
                            'country_id'=>$rows[csf('country_id')],
                            'buyer_name'=>$rows[csf('buyer_name')],
                            'po_id'=>$rows[csf('po_id')],
                            'po_number'=>$rows[csf('po_number')],
                            'grouping'=>$rows[csf('grouping')],
                            'color_number_id'=>$rows[csf('color_number_id')],
                            'size_number_id'=>$rows[csf('size_number_id')],
                            'style_ref_no'=>$rows[csf('style_ref_no')],
                            'style_description'=>$rows[csf('style_description')],
                            'job_no'=>$rows[csf('job_no')],
                            'cut_no'=>$rows[csf('cut_no')],
                            'item_number_id'=>$rows[csf('item_number_id')],
                            'order_cut_no'=>$rows[csf('order_cut_no')]

                        );
                        $batch_wise_bndl_array[$key] .= $batch_array[$rows[csf("bundle_no")]].",";

                        $productionQtyArr[$key]+=$rows[csf('production_qnty')];
                        $sizeQtyArr[$key][$rows[csf('size_number_id')]]+=$rows[csf('production_qnty')];
                        $bundleArr[$key][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];

                    }


                $table_width=1080+(count($bundle_size_arr)*50);
            ?>
        <div style="width:100%;">
        <table align="left" cellspacing="0" width="<? echo $table_width; ?>"  border="1" rules="all" class="rpt_table" style=" margin-top:20px;" >
            <thead bgcolor="#dddddd" align="center">
                <tr>
                    <th width="30" rowspan="2">SL</th>
                    <th width="80" align="center" rowspan="2">Buyer</th>
                    <th width="80" align="center" rowspan="2">Job No</th>
                    <th width="80" align="center" rowspan="2">Style Ref</th>
                    <th width="80" align="center" rowspan="2">Internal Ref</th>
                    <th width="80" align="center" rowspan="2">PO Number</th>
                    <th width="100" align="center" rowspan="2">Gmt Item.</th>
                    <th width="80" align="center" rowspan="2">Country</th>
                    <th width="80" align="center" rowspan="2">Cutting No</th>
                    <th width="80" align="center" rowspan="2">Order Cut</th>
                    <th width="100" align="center" rowspan="2">Batch No</th>
                    <th width="80" align="center" rowspan="2">Color</th>
                    <th align="center" width="50" colspan="<? echo count($bundle_size_arr); ?>">Size</th>
                    <th width="80" align="center" rowspan="2">Total Issue Qty</th>
                    <th width="80" align="center" rowspan="2">No of Bundle</th>
                    <th align="center" rowspan="2">Remarks</th>
                  </tr>
                  <tr>
                    <?
                    $i=0;
                    foreach($bundle_size_arr as $inf)
                    {
                    ?>
                    <th align="center" width="50" rowspan="2"><? echo $size_library[$inf]; ?></th>
                    <?
                    }
                    ?>
                 </tr>
            </thead>
            <tbody>
                <?
                $i=1;
                $tot_qnty=array();
                $grand_total_qty="";
                foreach($dataArr as $key=>$row)
                {
                   $batch_no = implode(",",array_unique(array_filter(explode(",",$batch_wise_bndl_array[$key]))));
                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="font-size:12px">
                            <td><? echo $i; ?></td>
                            <td align="center"><? echo $buyer_arr[$row['buyer_name']]; ?></td>
                            <td align="center"><? echo $row['job_no']; ?></td>
                            <td align="center"><? echo $row['style_ref_no']; ?></td>
                            <td align="center"><p><? echo $row['grouping']; ?></p></td>
                            <td align="center"><? echo $row['po_number'];?></td>
                            <td align="center"><? echo $garments_item[$row['item_number_id']];?></td>
                            <td align="center"><? echo $country_library[$row['country_id']]; ?></td>
                            <td align="center"><? echo $row['cut_no']; ?></td>
                            <td align="center"><? echo $order_cut_no_arr[$row['cut_no']][$row['po_id']]; ?></td>
                            <td align="center"><?=$batch_no; ?></td>
                            <td align="center"><? echo $color_library[$row['color_number_id']]; ?></td>
                            <?
                            foreach($bundle_size_arr as $size_id)
                            {
                                // echo $size_id."**";
                                $size_qty=0;
                                $size_qty=$sizeQtyArr[$key][$size_id];
                                ?>
                                <td align="center" width="50"><? echo $size_qty; ?></td>
                                <?
                                $grand_total_size_arr[$size_id]+=$size_qty;
                            }
                            ?>
                            <td align="center"><? echo $productionQtyArr[$key]; ?></td>
                            <td align="center"><? echo count($bundleArr[$key]); ?></td>
                            <td align="center"></td>
                        </tr>
                        <?
                        $i++;
                        $grand_total_qty+=$productionQtyArr[$key];
                        $grand_total_bundle_num+=count($bundleArr[$key]);
                        $grand_total_reject_qty+=$val['reject_qty'];

               }
               ?>
            </tbody>
            <tr bgcolor="#DDDDDD">

                <td colspan="12" align="right"><strong>Grand Total :</strong></td>
                <?
                foreach($bundle_size_arr as $size_id)
                {
                    ?>
                    <td align="center" width="50"><? echo $grand_total_size_arr[$size_id]; ?></td>
                    <?
                }
                ?>
                 <td align="center"><? echo $grand_total_qty; ?></td>
                <td align="center"><? echo $grand_total_bundle_num; ?></td>
                <td  align="center"><?  //echo $val[csf('num_of_bundle')]; ?></td>
            </tr>
        </table>
     </div>
    </div>
         <?
            echo signature_table(226, $data[0], "900px",'',0);
         ?>
         <br>
    </div>

        </div>
        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
        function generateBarcode( valuess ){
                var value = valuess;//$("#barcodeValue").val();
                var btype = 'code39';//$("input[name=btype]:checked").val();
                var renderer ='bmp';// $("input[name=renderer]:checked").val();
                var settings = {
                  output:renderer,
                  bgColor: '#FFFFFF',
                  color: '#000000',
                  barWidth: 1,
                  barHeight: 30,
                  moduleSize:5,
                  posX: 10,
                  posY: 20,
                  addQuietZone: 1
                };

                 value = {code:value, rect: false};
                $("#barcode_img_id_<?php echo $vals;?>").show().barcode(value, btype, settings);
            }
            generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
         </script>
    <?
    }


    if($is_mail_send==1){
        $emailBody=ob_get_contents();

        $sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=120 and  b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
        $mail_sql_res=sql_select($sql);

        $mailArr=array();
        foreach($mail_sql_res as $row)
        {
            $mailArr[$row['EMAIL']]=$row['EMAIL'];
        }

        if($mail_id!=''){$mailArr[]=$mail_id;}


        $to=implode(',',$mailArr);
        $subject="Bundle Wise Delivery to Sewing Input Challan/Gate Pass";

        if($to!=""){
            include('../../auto_mail/setting/mail_setting.php');
            $header=mailHeader();
            echo sendMailMailer( $to, $subject, $mail_body."<br><br>".$emailBody,$from_mail,'' );
        }
    }
    exit();

}


if($action=="sewing_input_challan_print_8")//(Shafiq)
{

    extract($_REQUEST);
    $data=explode('*',$data);
    $cbo_company_name = $data[0]; $is_mail_send = $data[4]; $mail_id = $data[5]; $mail_body = $data[6];if( $is_mail_send == 1){ob_start();}


    //print_r ($data);
    $company_library=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    $supplier_library=return_library_array( "select id,supplier_name from  lib_supplier where status_active=1 and is_deleted=0", "id","supplier_name");
    $location_library=return_library_array( "select id, location_name from  lib_location where status_active=1 and is_deleted=0", "id", "location_name");
    $floor_library=return_library_array( "select id, floor_name from  lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
    $color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0 ", "id", "color_name" );
    $size_library=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
    $line_library = return_library_array("select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0", "id", "line_name");
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
    $body_part_arr=return_library_array( "select id,bundle_use_for from ppl_bundle_title where company_id=$data[0]",'id','bundle_use_for');



    $sql="SELECT id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
    production_source, serving_company, floor_id, sewing_line, organic, delivery_date,working_company_id,working_location_id
    from pro_gmts_delivery_mst
    where production_type=4 and id='$data[1]' and status_active=1 and is_deleted=0 ";
    $dataArray=sql_select($sql);

    $nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='" . $dataArray[0][csf('company_id')] . "' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];

    if ($prod_reso_allocation == 1)
    {
        if ($dataArray[0][csf('floor_id')] == 0 && $dataArray[0][csf('location_id')] != 0) $cond = " and a.location_id= {$dataArray[0][csf('location_id')]}";
        if ($dataArray[0][csf('floor_id')] != 0) $cond = " and a.floor_id= {$dataArray[0][csf('floor_id')]}";

        if ($db_type == 0) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
        } else if ($db_type == 2 || $db_type == 1) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
        }

        foreach ($line_data as $row)
        {
            $line = '';
            $line_number = explode(",", $row[csf('line_number')]);
            foreach ($line_number as $val) {

                if ($line == '') $line = $line_library[$val]; else $line .= "," . $line_library[$val];
            }
            $line_array[$row[csf('id')]] = $line;
        }
        $line = $line_array[$dataArray[0][csf('sewing_line')]];
    }
    else
    {
        $line = $line_library[$dataArray[0][csf('sewing_line')]];
    }

    // =============================== getting cut no ====================================

    $delivery_mst_id =$dataArray[0][csf('id')];
    $cut_nos_all=sql_select("select cut_no from pro_garments_production_dtls a  where a.status_active=1 and a.is_deleted=0 and  a.delivery_mst_id='$delivery_mst_id' and production_type=4 group by cut_no");
    $cut_number_string="";
    foreach($cut_nos_all as $cut_val)
    {
        if($cut_number_string=="")
        {
            $val=$cut_val[csf('cut_no')];
             $cut_number_string.="'$val'";
        }
        else
        {
            $val=$cut_val[csf('cut_no')];
            $cut_number_string.=','."'$val'";
        }
    }
     // echo $cut_number_string;
    // ==================================== order cut no ==================================

    $order_cut_no_sql=sql_select("SELECT b.order_ids as po,b.order_cut_no as ord_cut,cutting_no from ppl_cut_lay_mst a,ppl_cut_lay_dtls b where a.id=b.mst_id and a.cutting_no in($cut_number_string)");
    $ordcut_number_string="";
    foreach($order_cut_no_sql as $ordCut_val)
    {
        if($ordcut_number_string=="")
        {
            $val=$ordCut_val[csf('ord_cut')];
             $ordcut_number_string.="'$val'";
        }
        else
        {
            $val=$ordCut_val[csf('ord_cut')];
            $ordcut_number_string.=','."'$val'";
        }
    }
    ob_start();
	?>
	<style type="text/css">
  		table.details-view {
	    border-bottom:1px solid #000;
	    font-size: 12px;
	    }
	    th.outer,
	    td.outer {
	    border-left: 1px solid #000;
	    border-right: 1px solid #000;
	    border-top: 1px solid #000;
	    padding: 0.1em;
	    }

	    th.inner,
	    td.inner {
	    border-top: 1px solid #000;
	    padding: 1px;
	    }

	</style>
	<div style="width:900px;">
	    <table cellspacing="0" style="font: 11px tahoma; width: 100%;">
	        <tr>
	            <td colspan="6" align="center" style="font-size:20px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="6" align="center" style="font-size:14px">
	                <?
	                    $nameArray=sql_select( "SELECT plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");

	                    foreach ($nameArray as $result)
	                    {
	                         echo $result[csf('city')];
	                    }
	                ?>
	            </td>
	        </tr>
	        <tr>
	            <td colspan="6" align="center" style="font-size:18px"><u><strong> Bundle Wise Delivery to Sewing Input Challan/Gate Pass</strong></u></td>
	        </tr>
	     	<tr>
	            <td width="90"><strong>Challan No</strong></td> <td width="140px">: <? echo $dataArray[0][csf('sys_number')]; ?></td>
	            <td width="110"><strong>Source</strong></td><td width="175px"> : <? echo $knitting_source[$dataArray[0][csf('production_source')]];?></td>
	            <td width="105"><strong>Sewing Company</strong></td>
	            <td>:
	            	<? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]];
	                    else echo $supplier_library[$dataArray[0][csf('serving_company')]]; ?>

	            </td>
	        </tr>
	        <tr>

	            <td><strong>Location</strong></td><td>:
	                <? echo $location_library[$dataArray[0][csf('location_id')]]; ?>
	            </td>
	            <td><strong>Floor</strong></td><td> <? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?> </td>
	            <td><strong>Line</strong></td><td> <? echo ": ".$line; ?></td>
	        </tr>
	        <tr>
	            <td><strong>Company </strong></td><td>: <? echo $company_library[$dataArray[0][csf('company_id')]]; ?></td>
	            <td><strong>Input Date</strong></td><td> <? echo ": ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
	            <td><strong>Cutting No. </strong></td><td>: <? echo str_replace("'", "", $cut_number_string); ?></td>
	        </tr>
	        <tr>
	            <td><strong>Order Cut No. </strong></td><td>: <? echo str_replace("'", "", $ordcut_number_string); ?></td>
	            <td></td><td></td>
	            <td></td><td></td>

	        </tr>

	    </table>

	        <?
	        $sqls="SELECT a.item_number_id, b.cut_no, b.production_qnty, a.country_id, d.color_number_id, d.size_number_id, e.style_ref_no, e.style_description, e.job_no, e.buyer_name,f.po_number,f.id as po_id, f.po_quantity,b.bundle_no from  pro_garments_production_mst a, pro_garments_production_dtls b,wo_po_color_size_breakdown d, wo_po_details_master e,wo_po_break_down f  where
	                    a.delivery_mst_id ='$data[1]' and a.production_type=4 and b.production_type=4  and e.id=f.job_id and e.id=d.job_id   and f.id=a.po_break_down_id
	                    and a.id=b.mst_id  and b.color_size_break_down_id=d.id  and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
	                    and b.is_deleted=0  and f.status_active=1  and f.is_deleted=0 and d.status_active=1
	                    and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 order by e.job_no,d.size_order";

	        $result=sql_select($sqls);

            foreach($result as $rows)
            {

                $key=$rows[csf('country_id')].$rows[csf('buyer_name')].$rows[csf('job_no')].$rows[csf('po_id')].$rows[csf('color_number_id')].$rows[csf('style_ref_no')].$rows[csf('style_description')].$rows[csf('cut_no')].$rows[csf('order_cut_no')];

                $bundle_size_arr[$rows[csf('size_number_id')]]=$rows[csf('size_number_id')];
                $size_wise_bundle_num_arr[$rows[csf('size_number_id')]] ++;
                $dataArr[$key]=array(
                    country_id=>$rows[csf('country_id')],
                    buyer_name=>$rows[csf('buyer_name')],
                    po_id=>$rows[csf('po_id')],
                    po_number=>$rows[csf('po_number')],
                    item_number_id=>$rows[csf('item_number_id')],
                    color_number_id=>$rows[csf('color_number_id')],
                    size_number_id=>$rows[csf('size_number_id')],
                    style_ref_no=>$rows[csf('style_ref_no')],
                    style_description=>$rows[csf('style_description')],
                    job_no=>$rows[csf('job_no')],
                    cut_no=>$rows[csf('cut_no')],
                    order_cut_no=>$rows[csf('order_cut_no')]
                );
                $batch_wise_qty_array[$batch_array[$rows[csf("bundle_no")]]]+=$rows[csf('production_qnty')];
                $productionQtyArr[$key]+=$rows[csf('production_qnty')];
                $sizeQtyArr[$key][$rows[csf('size_number_id')]]+=$rows[csf('production_qnty')];
                $bundleArr[$key][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];

            }
            $width = 868;
            $others_col_width = 720;
            $dif = $width - $others_col_width;
	        $total_size = count($bundle_size_arr);
            $size_width = $dif / $total_size;
	        $table_width=720+($total_size*$size_width);
	        // ksort($bundle_size_arr);
	        ?>
	    <div style="width:100%;">
	    <table align="left" cellspacing="0" cellpadding="0" width="<? echo $table_width; ?>" border="1" class="details-view rpt_table" style=" margin-top:5px;" >
	        <thead bgcolor="#dddddd" align="center">
	            <tr>
	                <th class="outer" width="30" rowspan="2">SL</th>
	                <th class="inner" width="100" align="center" rowspan="2">Buyer</th>
	                <th class="inner" width="100" align="center" rowspan="2">PO Number</th>
	                <th class="inner" width="100" align="center" rowspan="2">Style Ref</th>
	                <th class="inner" width="100" align="center" rowspan="2">Style Des</th>
	                <th class="inner" width="100" align="center" rowspan="2">Color</th>
	                <th class="inner" align="center" width="<? echo $size_width;?>" colspan="<? echo count($bundle_size_arr); ?>">Size</th>
	                <th class="inner" width="80" align="center" rowspan="2">Total Qty</th>
	                <th class="outer" width= align="center" rowspan="2">Remarks</th>
	              </tr>
	              <tr>
	                <?
	                $i=0;
	                foreach($bundle_size_arr as $inf)
	                {
		                ?>
		                <th class="inner" align="center" width="<? echo $size_width;?>" rowspan="2"><? echo $size_library[$inf]; ?></th>
		                <?
	                }
	                ?>
	             </tr>
	        </thead>
	        <tbody>
	            <?
	            $i=1;
	            $tot_qnty=array();
	            foreach($dataArr as $key=>$row)
	            {

	                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
	                        <td class="outer"><? echo $i;  ?></td>
	                        <td style="word-break: break-all;" class="inner" align="center"><? echo $buyer_arr[$row['buyer_name']]; ?></td>
	                        <td style="word-break: break-all;" class="inner" align="center"><? echo $row['po_number'];?></td>
	                        <td style="word-break: break-all;" class="inner" align="center"><? echo $row['style_ref_no']; ?></td>
	                        <td style="word-break: break-all;" class="inner" align="center"><? echo $row['style_description']; ?></td>
	                        <td style="word-break: break-all;" class="inner" align="center"><? echo $color_library[$row['color_number_id']]; ?></td>

	                        <?
	                        foreach($bundle_size_arr as $size_id)
	                        {
	                            $size_qty=0;
	                            $size_qty=$sizeQtyArr[$key][$size_id];
	                            ?>
	                            <td class="inner" align="center" width="<? echo $size_width;?>"><? echo $size_qty; ?></td>
	                            <?
	                            $grand_total_size_arr[$size_id]+=$size_qty;
	                        }
	                        ?>
	                        <td class="inner" align="center"><? echo $productionQtyArr[$key]; ?></td>
	                        <td class="outer" align="center"> <?  //echo $val[csf('num_of_bundle')]; ?></td>
	                    </tr>
	                    <?
	                    $i++;
	                    $grand_total_qty+=$productionQtyArr[$key];
	                    $grand_total_bundle_num+=count($bundleArr[$key]);
	                    $grand_total_reject_qty+=$val['reject_qty'];

	           }
	           ?>
	        </tbody>
	        <tr bgcolor="#DDDDDD">

	            <td class="outer" colspan="6" align="right"><strong>Bundle No :</strong></td>
	            <?
	            foreach($bundle_size_arr as $size_id)
	            {
	                ?>
	                <td class="inner" align="center" width="<? echo $size_width;?>"><? echo $size_wise_bundle_num_arr[$size_id]; ?></td>
	                <?
	            }
	            ?>
	            <td class="inner" align="center"><? echo $grand_total_bundle_num; ?></td>
	            <td class="outer"  align="center"><?  //echo $val[csf('num_of_bundle')]; ?></td>
	        </tr>
	    </table>
	 </div>
	 <br clear="all">
	 <!-- ==================================== DETAILS PART START ====================================== -->
	<?
	if($db_type==0)
    {
        $sql="SELECT sum(a.production_qnty) as production_qnty,sum(a.reject_qty) as reject_qnty,a.bundle_no,c.po_break_down_id,b.size_number_id
        from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c where c.delivery_mst_id ='$data[1]' and c.id=a.mst_id
        and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.bundle_no is not null
        group by a.bundle_no,c.po_break_down_id,b.size_number_id order by a.bundle_no,b.size_number_id asc";
    }
    else
    {
        $sql="SELECT sum(a.production_qnty) as production_qnty,sum(a.reject_qty) as reject_qnty,a.bundle_no,a.barcode_no,a.id,b.size_number_id
        from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c where c.delivery_mst_id ='$data[1]' and c.id=a.mst_id
        and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.bundle_no is not null
        group by a.bundle_no,a.barcode_no,b.size_number_id,a.id order by a.id, b.size_number_id asc";
    }
    $sql_res = sql_select($sql);
    $bundleNoArr = "";
    $bundleNo = "";
    foreach($sql_res as $val)
    {
        $size_qty_arr[$val[csf('bundle_no')]][$val[csf('size_number_id')]] 		+= $val[csf('production_qnty')];
        $reject_qty_arr[$val[csf('bundle_no')]][$val[csf('size_number_id')]] 	+= $val[csf('reject_qnty')];
        $size_wise_bundle_no_arr[$val[csf('size_number_id')]] ++;
        $bundleNoArr = $val[csf('barcode_no')];
        $bundleNo .= "'$bundleNoArr',";
    }
    $bundleNo =  chop($bundleNo,",");
    // ===================================== CUTTING REJECT ==========================
    $cut_sql = "SELECT sum(case when a.production_type =1 then a.reject_qty else 0 end) as cut_reject_qnty,
        sum(case when a.production_type in(2,3) and embel_name in(1,2) then a.reject_qty else 0 end) as emb_reject_qnty,
        a.bundle_no,a.id,b.size_number_id
        from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c where a.barcode_no in($bundleNo) and c.id=a.mst_id
        and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.bundle_no is not null
        group by a.bundle_no,b.size_number_id,a.id order by a.id, b.size_number_id asc ";
    $rej_qty_arr = array();
    $cut_sql_res = sql_select($cut_sql);
    foreach ($cut_sql_res as $val)
    {
        $rej_qty_arr[$val[csf('bundle_no')]][$val[csf('size_number_id')]]['cut_reject_qnty'] += $val[csf('cut_reject_qnty')];
        $rej_qty_arr[$val[csf('bundle_no')]][$val[csf('size_number_id')]]['emb_reject_qnty'] += $val[csf('emb_reject_qnty')];

    }
    // ================================ CUTTING QC REJECT QNTY ==============================
    $cutting_qc_sql="SELECT a.bundle_no,a.size_id, sum(a.reject_qty) as reject_qty,sum(a.replace_qty) as replace_qty
    from pro_gmts_cutting_qc_dtls a
    where a.status_active=1 and a.barcode_no in($bundleNo)
    group by a.bundle_no,a.size_id";
   // echo $cutting_qc_sql;
    $cutting_qc_sql_res = sql_select($cutting_qc_sql);
    $cutting_rej_array = array();
    foreach ($cutting_qc_sql_res as $row)
    {
        $cutting_rej_array[$row[csf('bundle_no')]][$row[csf('size_id')]]['reject_qty'] += $row[csf('reject_qty')];
        $cutting_rej_array[$row[csf('bundle_no')]][$row[csf('size_id')]]['replace_qty'] += $row[csf('replace_qty')];

    }

    //    echo "<pre>";
    //     print_r($cutting_rej_array);
    //     echo "</pre>";
    //     die();
    ?>
    <div class="details">
    <?
    $num_item = 52; //we set number of item in each col
	$current_col = 0;
	$column_data = '';
    $i = 1;
    	foreach ($size_qty_arr as $bundle_no => $bundle_data)
	    {
	        foreach ($bundle_data as $size_id => $size_qty)
	        {
	            $bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
	        	if($current_col == 0)
	        	{

					$column_data .='<div style="margin-top: 10px; width: 31.6%; float: left;padding: 0 3px; text-align:center;">
						 <table width="100%" cellpadding="0" cellspacing="0" class="details-view" border="1" style="margin-left:auto;margin-right:auto;">
					        <thead>
						        <tr>
						            <td width="100%" colspan="5" align="center"><strong>Size Wise Summary</strong></td>
						        </tr>
						        <tr bgcolor="#dddddd" align="center">
						            <td width="30%" class="inner">Bundle No</td>
						            <td width="13%" class="inner">Size</td>
						            <td width="19%" class="inner">Bundle Qty</td>
						            <td width="19%" class="inner">Reject Qty</td>
                                    <td width="19%" class="inner">Replace Qty</td>
						            <td width="19%" class="outer">Actual Qty</td>
						        </tr>
					        </thead>
					        <tbody>';

				}

						$column_data .='<tr bgcolor='.$bgcolor.'>
						                <td width="30%" class="inner" align="center">'.substr($bundle_no, 7).'</td>
						                <td width="13%" class="inner" align="center">'.$size_library[$size_id].'</td>
						                <td width="19%" class="inner" align="right">'.$size_qty.'</td>
						                <td width="19%" class="inner" align="right">'.($cutting_rej_array[$bundle_no][$size_id]['reject_qty']).'</td>
                                        <td width="19%" class="inner" align="right">'.($cutting_rej_array[$bundle_no][$size_id]['replace_qty']
                                        ).'</td>

                                        <td width="19%" class="outer" align="right">'.($size_qty-($cutting_rej_array[$bundle_no][$size_id]['reject_qty'])+$cutting_rej_array[$bundle_no][$size_id]['replace_qty']).'</td>
						            </tr>';
					            if ($current_col == $num_item - 1)  // Close the row if $current_col equals to 2 in the ($num_cols -1)
					            {
							        $current_col = 0;
							        $column_data .= '</tbody></table></div>';
							    }
							    else
							    {
							        $current_col++;
							    }
					$i++;
				}
			}
			echo $column_data;
			?>
		</div>
		</div>
		     <?
		        // echo signature_table(26, $data[0], "730px");
		     ?>

		</div>
		    </div>
		    <script type="text/javascript" src="../../js/jquery.js"></script>
		    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>

		<?



        if($is_mail_send==1){
            $emailBody=ob_get_contents();


            $sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=120 and  b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
            $mail_sql_res=sql_select($sql);

            $mailArr=array();
            foreach($mail_sql_res as $row)
            {
                $mailArr[$row['EMAIL']]=$row['EMAIL'];
            }

            if($mail_id!=''){$mailArr[]=$mail_id;}


            $to=implode(',',$mailArr);
            $subject="Bundle Wise Delivery to Sewing Input Challan/Gate Pass";


            if($to!=""){
                include('../../auto_mail/setting/mail_setting.php');
                $header=mailHeader();
                echo sendMailMailer( $to, $subject, $mail_body."<br><br>".$emailBody,$from_mail,'' );
            }
        }
		exit();
}


if($action=="sewing_input_challan_print_10")// for FFL (Shafiq)
{

    extract($_REQUEST);
    $data=explode('*',$data);
    $cbo_company_name = $data[0]; $is_mail_send = $data[4]; $mail_id = $data[5]; $mail_body = $data[6];if( $is_mail_send == 1){ob_start();}
    //print_r ($data);
    $company_library=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    $supplier_library=return_library_array( "select id,supplier_name from  lib_supplier where status_active=1 and is_deleted=0", "id","supplier_name");
    $location_library=return_library_array( "select id, location_name from  lib_location where status_active=1 and is_deleted=0", "id", "location_name");
    $floor_library=return_library_array( "select id, floor_name from  lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
    $color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0 ", "id", "color_name" );
    $country_library=return_library_array( "select id,country_name from lib_country where status_active=1 and is_deleted=0 ", "id", "country_name" );
    $size_library=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
    $line_library = return_library_array("select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0", "id", "line_name");
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
    $body_part_arr=return_library_array( "select id,bundle_use_for from ppl_bundle_title where company_id=$data[0]",'id','bundle_use_for');



    $sql="SELECT id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
    production_source, serving_company, floor_id, sewing_line, organic, delivery_date,working_company_id,working_location_id,remarks
    from pro_gmts_delivery_mst
    where production_type=4 and id='$data[1]' and status_active=1 and is_deleted=0 ";
    $dataArray=sql_select($sql);

    $nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='" . $dataArray[0][csf('company_id')] . "' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];

    if ($prod_reso_allocation == 1)
    {
        if ($dataArray[0][csf('floor_id')] == 0 && $dataArray[0][csf('location_id')] != 0) $cond = " and a.location_id= {$dataArray[0][csf('location_id')]}";
        if ($dataArray[0][csf('floor_id')] != 0) $cond = " and a.floor_id= {$dataArray[0][csf('floor_id')]}";

        if ($db_type == 0) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
        } else if ($db_type == 2 || $db_type == 1) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
        }

        foreach ($line_data as $row)
        {
            $line = '';
            $line_number = explode(",", $row[csf('line_number')]);
            foreach ($line_number as $val) {

                if ($line == '') $line = $line_library[$val]; else $line .= "," . $line_library[$val];
            }
            $line_array[$row[csf('id')]] = $line;
        }
        $line = $line_array[$dataArray[0][csf('sewing_line')]];
    }
    else
    {
        $line = $line_library[$dataArray[0][csf('sewing_line')]];
    }

    // =============================== getting cut no ====================================

    $delivery_mst_id =$dataArray[0][csf('id')];
    $cut_nos_all=sql_select("select cut_no from pro_garments_production_dtls a  where a.status_active=1 and a.is_deleted=0 and  a.delivery_mst_id='$delivery_mst_id' and production_type=4 group by cut_no");
    $cut_number_string="";
    foreach($cut_nos_all as $cut_val)
    {
        if($cut_number_string=="")
        {
            $val=$cut_val[csf('cut_no')];
             $cut_number_string.="'$val'";
        }
        else
        {
            $val=$cut_val[csf('cut_no')];
            $cut_number_string.=','."'$val'";
        }
    }
     // echo $cut_number_string;
    // ==================================== order cut no ==================================

    $order_cut_no_sql=sql_select("SELECT b.order_ids as po,b.order_cut_no as ord_cut,cutting_no from ppl_cut_lay_mst a,ppl_cut_lay_dtls b where a.id=b.mst_id and a.cutting_no in($cut_number_string)");
    $ordcut_number_string="";
    foreach($order_cut_no_sql as $ordCut_val)
    {
        if($ordcut_number_string=="")
        {
            $val=$ordCut_val[csf('ord_cut')];
             $ordcut_number_string.="'$val'";
        }
        else
        {
            $val=$ordCut_val[csf('ord_cut')];
            $ordcut_number_string.=','."'$val'";
        }
    }
    ?>
    <style type="text/css">
        table.details-view {
        border-bottom:1px solid #000;
        font-size: 12px;
        }
        th.outer,
        td.outer {
        border-left: 1px solid #000;
        border-right: 1px solid #000;
        border-top: 1px solid #000;
        padding: 0.1em;
        }

        th.inner,
        td.inner {
        border-top: 1px solid #000;
        padding: 1px;
        }

    </style>
    <div style="width:900px;">
        <table cellspacing="0" style="font: 11px tahoma; width: 100%;">
            <tr>
                <td colspan="6" align="center" style="font-size:20px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="6" align="center" style="font-size:14px">
                    <?
                        $nameArray=sql_select( "SELECT plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");

                        foreach ($nameArray as $result)
                        {
                             echo $result[csf('city')];
                        }
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:18px"><u><strong> Bundle Wise Sewing Input Challan</strong></u></td>
            </tr>
            <tr>
                <td width="90"><strong>Challan No</strong></td> <td width="140px">: <? echo $dataArray[0][csf('sys_number')]; ?></td>
                <td width="110"><strong>Source</strong></td><td width="175px"> : <? echo $knitting_source[$dataArray[0][csf('production_source')]];?></td>
                <td width="105"><strong>Sewing Company</strong></td>
                <td>:
                    <? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]];
                        else echo $supplier_library[$dataArray[0][csf('serving_company')]]; ?>

                </td>
            </tr>
            <tr>

                <td><strong>Location</strong></td><td>:
                    <? echo $location_library[$dataArray[0][csf('location_id')]]; ?>
                </td>
                <td><strong>Floor</strong></td><td> <? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?> </td>
                <td><strong>Line</strong></td><td> <? echo ": ".$line; ?></td>
            </tr>
            <tr>
                <td><strong>Company </strong></td><td>: <? echo $company_library[$dataArray[0][csf('company_id')]]; ?></td>
                <td><strong>Input Date</strong></td><td> <? echo ": ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
                <td><strong>Cutting No. </strong></td><td>: <? echo str_replace("'", "", $cut_number_string); ?></td>
            </tr>
            <tr>
                <td><strong>Order Cut No. </strong></td><td>: <? echo str_replace("'", "", $ordcut_number_string); ?></td>
                <td><strong>Remarks </strong></td><td>: <? echo $dataArray[0][csf('remarks')];?></td>
                <td></td><td></td>

            </tr>

        </table>

            <?
            $sqls="SELECT a.item_number_id, b.cut_no, b.production_qnty, a.country_id, d.color_number_id, d.size_number_id, e.style_ref_no, e.style_description, e.job_no, e.buyer_name,f.po_number,f.id as po_id, f.po_quantity,b.bundle_no from  pro_garments_production_mst a, pro_garments_production_dtls b,wo_po_color_size_breakdown d, wo_po_details_master e,wo_po_break_down f  where
                        a.delivery_mst_id ='$data[1]' and a.production_type=4 and b.production_type=4  and e.id=f.job_id and e.id=d.job_id   and f.id=a.po_break_down_id
                        and a.id=b.mst_id  and b.color_size_break_down_id=d.id  and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
                        and b.is_deleted=0  and f.status_active=1  and f.is_deleted=0 and d.status_active=1
                        and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 order by e.job_no,d.size_order";

            $result=sql_select($sqls);

            foreach($result as $rows)
            {

                $key=$rows[csf('country_id')].$rows[csf('buyer_name')].$rows[csf('job_no')].$rows[csf('po_id')].$rows[csf('color_number_id')].$rows[csf('style_ref_no')].$rows[csf('style_description')].$rows[csf('cut_no')].$rows[csf('order_cut_no')];

                $bundle_size_arr[$rows[csf('size_number_id')]]=$rows[csf('size_number_id')];
                $size_wise_bundle_num_arr[$rows[csf('size_number_id')]] ++;
                $dataArr[$key]=array(
                    country_id=>$rows[csf('country_id')],
                    buyer_name=>$rows[csf('buyer_name')],
                    po_id=>$rows[csf('po_id')],
                    po_number=>$rows[csf('po_number')],
                    item_number_id=>$rows[csf('item_number_id')],
                    color_number_id=>$rows[csf('color_number_id')],
                    size_number_id=>$rows[csf('size_number_id')],
                    style_ref_no=>$rows[csf('style_ref_no')],
                    style_description=>$rows[csf('style_description')],
                    job_no=>$rows[csf('job_no')],
                    cut_no=>$rows[csf('cut_no')],
                    order_cut_no=>$rows[csf('order_cut_no')]
                );
                $batch_wise_qty_array[$batch_array[$rows[csf("bundle_no")]]]+=$rows[csf('production_qnty')];
                $productionQtyArr[$key]+=$rows[csf('production_qnty')];
                $sizeQtyArr[$key][$rows[csf('size_number_id')]]+=$rows[csf('production_qnty')];
                $bundleArr[$key][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];

            }
            $width = 868;
            $others_col_width = 720;
            $dif = $width - $others_col_width;
            $total_size = count($bundle_size_arr);
            $size_width = $dif / $total_size;
            $table_width=800+($total_size*$size_width);
            // ksort($bundle_size_arr);
            ?>
        <div style="width:100%;">
        <table align="left" cellspacing="0" cellpadding="0" width="<? echo $table_width; ?>" border="1" class="details-view rpt_table" style=" margin-top:5px;" >
            <thead bgcolor="#dddddd" align="center">
                <tr>
                    <th class="outer" width="30" rowspan="2">SL</th>
                    <th class="inner" width="100" align="center" rowspan="2">Buyer</th>
                    <th class="inner" width="80" align="center" rowspan="2">Job</th>
                    <th class="inner" width="100" align="center" rowspan="2">PO Number</th>
                    <th class="inner" width="100" align="center" rowspan="2">Style Ref</th>
                    <th class="inner" width="100" align="center" rowspan="2">Style Des</th>
                    <th class="inner" width="100" align="center" rowspan="2">Color</th>
                    <th class="inner" align="center" width="<? echo $size_width;?>" colspan="<? echo count($bundle_size_arr); ?>">Size</th>
                    <th class="inner" width="80" align="center" rowspan="2">Total Qty</th>
                    <th class="outer" width= align="center" rowspan="2">Remarks</th>
                  </tr>
                  <tr>
                    <?
                    $i=0;
                    foreach($bundle_size_arr as $inf)
                    {
                        ?>
                        <th class="inner" align="center" width="<? echo $size_width;?>" rowspan="2"><? echo $size_library[$inf]; ?></th>
                        <?
                    }
                    ?>
                 </tr>
            </thead>
            <tbody>
                <?
                $i=1;
                $tot_qnty=array();
                foreach($dataArr as $key=>$row)
                {

                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:12px">
                            <td class="outer"><? echo $i;  ?></td>
                            <td style="word-break: break-all;" class="inner" align="center"><? echo $buyer_arr[$row['buyer_name']]; ?></td>
                            <td style="word-break: break-all;" class="inner" align="center"><? echo $row['job_no'];?></td>
                            <td style="word-break: break-all;" class="inner" align="center"><? echo $row['po_number'];?></td>
                            <td style="word-break: break-all;" class="inner" align="center"><? echo $row['style_ref_no']; ?></td>
                            <td style="word-break: break-all;" class="inner" align="center"><? echo $row['style_description']; ?></td>
                            <td style="word-break: break-all;" class="inner" align="center"><? echo $color_library[$row['color_number_id']]; ?></td>

                            <?
                            foreach($bundle_size_arr as $size_id)
                            {
                                $size_qty=0;
                                $size_qty=$sizeQtyArr[$key][$size_id];
                                ?>
                                <td class="inner" align="center" width="<? echo $size_width;?>"><? echo $size_qty; ?></td>
                                <?
                                $grand_total_size_arr[$size_id]+=$size_qty;
                            }
                            ?>
                            <td class="inner" align="center"><? echo $productionQtyArr[$key]; ?></td>
                            <td class="outer" align="center"> <?  //echo $val[csf('num_of_bundle')]; ?></td>
                        </tr>
                        <?
                        $i++;
                        $grand_total_qty+=$productionQtyArr[$key];
                        $grand_total_bundle_num+=count($bundleArr[$key]);
                        $grand_total_reject_qty+=$val['reject_qty'];

               }
               ?>
            </tbody>
            <tr bgcolor="#DDDDDD">
                <td class="outer" colspan="7" align="right"><strong>Total Qty :</strong></td>
                <?
                    foreach($bundle_size_arr as $size_id)
                    {
                        ?>
                        <td class="inner" align="center" width="<? echo $size_width;?>">
                            <?
                            echo $grand_total_size_arr[$size_id];
                            ?>
                        </td>
                        <?
                    }
                 ?>
                <td class="inner" align="center"><? echo $grand_total_qty; ?></td>
                <td class="outer"  align="center"><? ?></td>
            </tr>
            <tr bgcolor="#DDDDDD">

                <td class="outer" colspan="7" align="right"><strong>Bundle No :</strong></td>
                <?
                foreach($bundle_size_arr as $size_id)
                {
                    ?>
                    <td class="inner" align="center" width="<? echo $size_width;?>"><? echo $size_wise_bundle_num_arr[$size_id]; ?></td>
                    <?
                }
                ?>
                <td class="inner" align="center"><? echo $grand_total_bundle_num; ?></td>
                <td class="outer"  align="center"><?  //echo $val[csf('num_of_bundle')]; ?></td>
            </tr>
        </table>
     </div>
     <br clear="all">
     <!-- ==================================== DETAILS PART START ====================================== -->
    <?
    if($db_type==0)
    {
        $sql="SELECT sum(a.production_qnty) as production_qnty,sum(a.reject_qty) as reject_qnty,a.bundle_no,c.po_break_down_id,b.size_number_id
        from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c where c.delivery_mst_id ='$data[1]' and c.id=a.mst_id
        and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.bundle_no is not null
        group by a.bundle_no,c.po_break_down_id,b.size_number_id order by b.size_number_id ,a.bundle_no asc";
    }
    else
    {
        $sql="SELECT sum(a.production_qnty) as production_qnty,sum(a.reject_qty) as reject_qnty,sum(d.size_qty) as bundle_qnty,a.bundle_no,a.id,b.size_number_id,c.country_id,a.barcode_no
        from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c, ppl_cut_lay_bundle d
        where c.delivery_mst_id ='$data[1]' and c.id=a.mst_id and d.bundle_no=a.bundle_no
        and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.bundle_no is not null
        group by a.bundle_no,b.size_number_id,a.id,c.country_id,a.barcode_no order by  b.size_number_id ,a.id asc";
    }
    // echo $sql;
    $sql_res = sql_select($sql);
    $bundleNoArr = "";
    $bundleNo = "";
    foreach($sql_res as $val)
    {
        // $size_qty_arr[$val[csf('bundle_no')]][$val[csf('size_number_id')]]      += $val[csf('production_qnty')];
        $size_qty_arr[$val[csf('bundle_no')]][$val[csf('size_number_id')]]      += $val[csf('bundle_qnty')];
        $reject_qty_arr[$val[csf('bundle_no')]][$val[csf('size_number_id')]]    += $val[csf('reject_qnty')];
        $country_arr[$val[csf('bundle_no')]][$val[csf('size_number_id')]]    = $val[csf('country_id')];
        $size_wise_bundle_no_arr[$val[csf('size_number_id')]] ++;
        $bundleNoArr = $val[csf('barcode_no')];
        $bundleNo .= "'$bundleNoArr',";
    }
    $bundleNo =  chop($bundleNo,",");
    // ===================================== CUTTING REJECT ==========================
    $cut_sql = "SELECT sum(case when a.production_type =1 then a.reject_qty else 0 end) as cut_reject_qnty,
        sum(case when a.production_type in(2,3) and embel_name in(1,2,4) then a.reject_qty else 0 end) as emb_reject_qnty,
        sum(case when a.production_type =1 then a.replace_qty else 0 end) as cut_replace_qnty,
        sum(case when a.production_type in(2,3) and embel_name in(1,2,4) then a.replace_qty else 0 end) as emb_replace_qnty,
        a.bundle_no,a.id,b.size_number_id
        from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c where a.barcode_no in($bundleNo) and c.id=a.mst_id
        and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.bundle_no is not null
        group by a.bundle_no,b.size_number_id,a.id order by a.id, b.size_number_id asc ";
    $rej_qty_arr = array();
    $cut_sql_res = sql_select($cut_sql);
    foreach ($cut_sql_res as $val)
    {
        $rej_qty_arr[$val[csf('bundle_no')]][$val[csf('size_number_id')]]['cut_reject_qnty'] += $val[csf('cut_reject_qnty')];
        $rej_qty_arr[$val[csf('bundle_no')]][$val[csf('size_number_id')]]['emb_reject_qnty'] += $val[csf('emb_reject_qnty')];
        $rej_qty_arr[$val[csf('bundle_no')]][$val[csf('size_number_id')]]['cut_replace_qnty'] += $val[csf('cut_replace_qnty')];
        $rej_qty_arr[$val[csf('bundle_no')]][$val[csf('size_number_id')]]['emb_replace_qnty'] += $val[csf('emb_replace_qnty')];
    }
    // echo "<pre>"; print_r($rej_qty_arr); die;
    // ================================ CUTTING QC REJECT QNTY ==============================
    $cutting_qc_sql="SELECT a.bundle_no,a.size_id, sum(d.reject_qty) as reject_qty,c.cutting_no
    from pro_gmts_cutting_qc_dtls a
    where a.status_active=1 and a.barcode_no in($bundleNo)
    group by a.bundle_no,a.size_id";
    // echo $cutting_qc_sql;
    $cutting_qc_sql_res = sql_select($cutting_qc_sql);
    $cutting_rej_array = array();
    foreach ($cutting_qc_sql_res as $row)
    {
        $cutting_rej_array[$row[csf('bundle_no')]][$row[csf('size_id')]] += $row[csf('reject_qty')];
    }

    // echo "<pre>";print_r($cutting_rej_array); die;
    ?>
    <div class="details">
    <?
    $num_item = 31; //we set number of item in each col
    $current_col_count= 0;
    $current_col_count_2= 0;
    $current_col = 1;
    $column_data = '';
    $i = 1;
    // echo "<pre>"; print_r($size_qty_arr); die;
        foreach ($size_qty_arr as $bundle_no => $bundle_data)
        {
            foreach ($bundle_data as $size_id => $size_qty)
            {
                $bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
                if ($current_col_count_2 == 0) {
                    $column_data .='<div style="margin-top: 0px; width: 100%; padding: 0 3px; text-align:center;">';
                }
                if($current_col == 1)
                {
                    $column_data .='<div style="margin-top: 10px; width: 33%; float: left;padding: 0 3px; text-align:center;">
                         <table width="100%" cellpadding="0" cellspacing="0" class="details-view" border="1" style="margin-left:auto;margin-right:auto;">
                            <thead>
                                <tr>
                                    <td width="100%" colspan="7" align="center"><strong>Size Wise Summary</strong></td>
                                </tr>
                                <tr bgcolor="#dddddd" align="center">
                                    <td width="4%" class="inner">Sl</td>
                                    <td width="30%" class="inner">Country</td>
                                    <td width="20%" class="inner">Bundle No</td>
                                    <td width="10%" class="inner">Size</td>
                                    <td width="12%" class="inner">Bundle Qty</td>
                                    <td width="12%" class="inner">Reject Qty</td>
                                    <td width="12%" class="outer">Actual Qty</td>
                                </tr>
                            </thead>
                            <tbody>';

                }
                $column_data .='<tr bgcolor='.$bgcolor.'>
                                    <td width="4%" class="inner" align="center">'.$i.'</td>
                                    <td width="30%" class="inner" align="center">'.$country_library[$country_arr[$bundle_no][$size_id]].'</td>
                                    <td width="20%" class="inner" align="center">'.substr($bundle_no, 7).'</td>
                                    <td width="10%" class="inner" align="center">'.$size_library[$size_id].'</td>
                                    <td width="12%" class="inner" align="right">'.$size_qty.'</td>
                                    <td width="12%" class="inner" align="right">'.($rej_qty_arr[$bundle_no][$size_id]['cut_reject_qnty']+$rej_qty_arr[$bundle_no][$size_id]['emb_reject_qnty']+$cutting_rej_array[$bundle_no][$size_id]-$rej_qty_arr[$bundle_no][$size_id]['cut_replace_qnty']-$rej_qty_arr[$bundle_no][$size_id]['emb_replace_qnty']).'</td>
                                    <td width="12%" class="outer" align="right">'.($size_qty-($rej_qty_arr[$bundle_no][$size_id]['cut_reject_qnty']+$rej_qty_arr[$bundle_no][$size_id]['emb_reject_qnty']+$cutting_rej_array[$bundle_no][$size_id]-$rej_qty_arr[$bundle_no][$size_id]['cut_replace_qnty']-$rej_qty_arr[$bundle_no][$size_id]['emb_replace_qnty'])).'</td>
                                </tr>';
                                $current_col_count_2++;
                                if ($current_col == $num_item -1)  // Close the row if $current_col equals to 2 in the ($num_cols -1)
                                {
                                    $current_col_count++;
                                    $current_col = 1;

                                    $column_data .= '</tbody></table></div>';
                                    if ($current_col_count==2)
                                    {
                                        $current_col_count = 0;
                                        $current_col_count_2=0;
                                        $column_data .= '<br clear="all">  </div>';
                                        //echo '<br clear="all"> Test';
                                    }
                                    // echo $current_col_count;
                                }
                                else
                                {
                                    $current_col++;
                                }

                    $i++;
                }
            }
            echo $column_data;
            ?>
        </div>
        <!-- <br clear="all"> -->
        </div>
             <?
                echo signature_table(226, $data[0], "730px","","10");
             ?>

        </div>
            </div>
            <script type="text/javascript" src="../../js/jquery.js"></script>
            <script type="text/javascript" src="../../js/jquerybarcode.js"></script>

        <?

            if($is_mail_send==1){
                $emailBody=ob_get_contents();

                $sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=120 and  b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
                $mail_sql_res=sql_select($sql);

                $mailArr=array();
                foreach($mail_sql_res as $row)
                {
                    $mailArr[$row['EMAIL']]=$row['EMAIL'];
                }

                if($mail_id!=''){$mailArr[]=$mail_id;}


                $to=implode(',',$mailArr);
                $subject="Bundle Wise Delivery to Sewing Input Challan/Gate Pass";

                if($to!=""){
                    include('../../auto_mail/setting/mail_setting.php');
                    $header=mailHeader();
                    echo sendMailMailer( $to, $subject, $mail_body."<br><br>".$emailBody,$from_mail,'' );
                }
            }

        exit();
}

if ($action == "emblishment_issue_print_11")
{
    extract($_REQUEST);
    $data = explode('*', $data);
    $cbo_company_name = $data[0]; $is_mail_send = $data[4];  $mail_id = $data[5]; $mail_body = $data[6];if( $is_mail_send == 1){ob_start();}
    //print_r ($data);
    $company_library = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    $job_no = array();
    $job_id = array();
    $country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
    $supplier_library = return_library_array("select id,supplier_name from  lib_supplier where status_active=1 and is_deleted=0", "id", "supplier_name");
    $location_library = return_library_array("select id, location_name from  lib_location where status_active=1 and is_deleted=0", "id", "location_name");
    $floor_library = return_library_array("select id, floor_name from  lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
    $color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
    $size_library = return_library_array("select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0 ", 'id', 'buyer_name');
    $line_library = return_library_array("select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0", "id", "line_name");
    $item_library = return_library_array("select id,item_name from LIB_GARMENT_ITEM where status_active=1 and is_deleted=0", "id", "item_name");

    $sql = "SELECT id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
    production_source, serving_company, floor_id,sewing_line, organic, delivery_date,working_company_id,working_location_id, remarks from pro_gmts_delivery_mst where production_type=4 and id='$data[1]' and
    status_active=1 and is_deleted=0 ";

    //echo $sql;

    $dataArray = sql_select($sql);
    $delivery_mst_id = $dataArray[0][csf('id')];

    $sql_order_mst = sql_select("select b.job_no, b.style_ref_no, b.buyer_name, a.country_id,c.po_number, b.gmts_item_id, f.color_number_id from pro_garments_production_mst a, pro_garments_production_dtls e, wo_po_color_size_breakdown f, wo_po_details_master b, wo_po_break_down c where a.po_break_down_id = c.id
                     and c.job_no_mst = b.job_no and a.id = e.mst_id and e.color_size_break_down_id = f.id and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0 and f.status_active = 1 and f.is_deleted = 0 and a.delivery_mst_id = $delivery_mst_id
                     group by b.job_no, b.style_ref_no, b.buyer_name, a.country_id,c.po_number, b.gmts_item_id, f.color_number_id");

    $nameArray = sql_select("SELECT id, auto_update from variable_settings_production where company_name='" . $dataArray[0][csf('serving_company')] . "' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];

    if ($prod_reso_allocation == 1)
    {
        if ($dataArray[0][csf('floor_id')] == 0 && $dataArray[0][csf('location_id')] != 0) $cond = " and a.location_id= {$dataArray[0][csf('location_id')]}";
        if ($dataArray[0][csf('floor_id')] != 0) $cond = " and a.floor_id= {$dataArray[0][csf('floor_id')]}";

        if ($db_type == 0) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
        } else if ($db_type == 2 || $db_type == 1) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
        }

        foreach ($line_data as $row) {
            $line = '';
            $line_number = explode(",", $row[csf('line_number')]);
            foreach ($line_number as $val) {
                if ($line == '') $line = $line_library[$val]; else $line .= "," . $line_library[$val];
            }
            $line_array[$row[csf('id')]] = $line;
        }
        $line = $line_array[$dataArray[0][csf('sewing_line')]];
    }
    else
    {
        $line = $line_library[$dataArray[0][csf('sewing_line')]];
    }
    unset($line_data);
    $production_quantity  =0;
    $total_bundle  = 0;
    $size_qty_arr=array();
    $size_wise_bundle_no_arr=array();
    ?>
    <style type="text/css">
        @media print
        {
            #footer_id {page-break-after: always;}
        }
    </style>


    <div style="width:1240px;">
        <table cellspacing="0" style="font: 13px tahoma;" width="1240">
            <tr>
                <td colspan="6" align="center" style="font-size:24px">
                    <strong><? echo $company_library[$data[0]]; ?></strong>
                </td>
            </tr>
            <tr class="form_caption">
                <td colspan="6" align="center" style="font-size:14px">
                    <?

                    $nameArray = sql_select("SELECT plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
                    foreach ($nameArray as $result) {

                        echo $result[csf('city')];

                    }
                    unset($nameArray);
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:20px"><u><strong>Sewing Input Challan</strong></u></td>
            </tr>
            <tr>
                <td width="140"><strong>Challan No.</strong></td>
                <td width="240"><? echo ": ".$dataArray[0][csf('sys_number')]; ?></td>
                <td width="130"><strong>Buyer</strong></td>
                <td width="240"><? echo ": ".$buyer_arr[$sql_order_mst[0][csf('buyer_name')]]; ?></td>
                <td width="130"><strong>Country</strong></td>
                <td>: <? echo $country_arr[$sql_order_mst[0][csf('country_id')]]?> </td>
            </tr>
            <tr>
                <td><strong>Source</strong></td>
                <td><? echo ": ".$knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
                <td><strong>Order No. </strong></td>
                <td><? echo ": ".$sql_order_mst[0][csf('po_number')]; ?></td>
                <td><strong>Floor  </strong></td>
                <td><? echo ": ".$floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Sewing Location </strong></td>
                <td><? echo ": ".$location_library[$dataArray[0][csf('location_id')]]; ?></td>
                <td><strong>Style Reference</strong></td><td>: <? echo $sql_order_mst[0][csf('style_ref_no')]; ?></td>
                <td><strong>Line </strong></td>
                <td><? echo ": ".$line; ?></td>
            </tr>
            <tr>
                <td><strong>Sewing Company</strong></td>
                <td>
                    <?
                    if ($dataArray[0][csf('production_source')] == 1) echo ": ".$company_library[$dataArray[0][csf('serving_company')]];
                    else echo ": ".$supplier_library[$dataArray[0][csf('serving_company')]];
                    ?>
                </td>
                <td><strong>Job</strong></td>
                <td>: <? echo $sql_order_mst[0][csf('job_no')]; ?></td>
                <td><strong>Input Date  </strong></td>
                <td><? echo ": ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
            </tr>
            <tr>
                <td><strong>Item Name</strong></td>
                <td>: <? echo $item_library[$sql_order_mst[0][csf('gmts_item_id')]]; ?></td>
                <td><strong>Color</strong></td>
                <td>: <? echo $color_library[$sql_order_mst[0][csf('color_number_id')]]?></td>
            </tr>
            <tr>
                <td><strong>Remarks</strong></td>
                <td colspan="5">: <? echo $dataArray[0][csf('remarks')]  ?></td>
            </tr>
        </table>
        <br>
        <?
        $sig_company_id = $data[0];

        if ($data[2] == 3) {

            $sql = "SELECT sum(b.production_qnty) as production_qnty,a.po_break_down_id,a.item_number_id,a.country_id,b.cut_no,b.bundle_no,d.size_number_id, d.color_number_id,
                count(b.id) as  num_of_bundle

                from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d
                where a.delivery_mst_id ='$data[1]'
                and a.id=b.mst_id and b.production_type=4 and b.color_size_break_down_id=d.id and  b.status_active=1 and b.is_deleted=0 and d.status_active=1
                and d.is_deleted=0
                group by a.po_break_down_id,a.item_number_id,a.country_id,b.cut_no,b.bundle_no,d.size_number_id,d.color_number_id
                order by  length(b.bundle_no) asc, b.bundle_no  asc";//a.po_break_down_id,d.color_number_id
        }
        else {
            $sql = "SELECT sum(a.production_qnty) as production_qnty,c.po_break_down_id,c.item_number_id,c.country_id,a.cut_no,a.bundle_no,b.size_number_id, b.color_number_id

                from pro_garments_production_mst c, pro_garments_production_dtls a, wo_po_color_size_breakdown b
                where c.delivery_mst_id ='$data[1]'
                and c.id=a.mst_id and a.color_size_break_down_id=b.id and a.production_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
                group by c.po_break_down_id,c.item_number_id,c.country_id,a.cut_no,a.bundle_no,b.size_number_id,b.color_number_id order by  length(a.bundle_no) asc, a.bundle_no asc";
        }

        $result = sql_select($sql);
        $all_cut_arr=array();
        $all_po_arr=array();

        foreach($result as $v)
        {
            $all_cut_arr[$v[csf("cut_no")]]=$v[csf("cut_no")];
            $all_po_arr[$v[csf("po_break_down_id")]]=$v[csf("po_break_down_id")];
        }
        $all_cut_nos="'".implode("','", $all_cut_arr)."'";
        $all_po_nos="'".implode("','", $all_po_arr)."'";
        $ppl_mst_sql=sql_select("SELECT cutting_no,batch_id from ppl_cut_lay_mst where status_active=1 and cutting_no in($all_cut_nos) ");
        $ppl_mst_arr=array();
        foreach($ppl_mst_sql as $p_val)
        {
            $ppl_mst_arr[$p_val[csf("cutting_no")]]=$p_val[csf("batch_id")];
        }

        $order_array = array();
        $order_sql = "SELECT a.id as job_id, a.job_no, a.buyer_name,a.style_ref_no, a.style_description,b.id, b.po_number, b.po_quantity,b.file_no,b.grouping,c.country_id from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in($all_po_nos)";
        $order_sql_result = sql_select($order_sql);
        foreach ($order_sql_result as $row)
        {
            $job_id[$row[csf('id')]] = $row[csf('job_id')];
            $job_no[$row[csf('id')]] = $row[csf('job_no')];
            $order_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
            $order_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
            $order_array[$row[csf('id')]]['po_number'] = $row[csf('po_number')];
            $order_array[$row[csf('id')]]['po_quantity'] = $row[csf('po_quantity')];
            $order_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
            $order_array[$row[csf('id')]]['style_des'] = $row[csf('style_description')];
            $order_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
            $order_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
            $order_array[$row[csf('id')]]['country_id'] = $row[csf('country_id')];
        }
        unset($order_sql_result);


        $sql_cut="SELECT bundle_no, number_start as number_start, number_end as number_end  from ppl_cut_lay_bundle where status_active=1 and is_deleted=0 and order_id in($all_po_nos)";
        $sql_cut_res = sql_select($sql_cut); $number_arr=array();
        foreach($sql_cut_res as $row)
        {
            $number_arr[$row[csf('bundle_no')]]['number_start']=$row[csf('number_start')];
            $number_arr[$row[csf('bundle_no')]]['number_end']=$row[csf('number_end')];
        }
        unset($sql_cut_res);


        ?>

        <div style="width:100%;">
            <table cellspacing="0" width="1240" border="1" rules="all" border="0" style="border:none;" >
                <tr style="border:none;">
                    <td style="border: none;" valign="top" width="300">
                        <table cellspacing="0" width="290" border="1" cellpadding="2" rules="all" class="rpt_table" style="font: 12px tahoma;">
                            <thead bgcolor="#dddddd" align="center">
                            <tr>
                                <th width="30">SL</th>
                                <th width="110" align="center">Bundle No</th>
                                <th width="70" align="center">Size</th>
                                <th  align="center">Qty.</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?
                            $i = 1;
                            $size_wise_bundle_no = 0;
                            $num_of_bundle = 0;
                            $tot_qnty = array();
                            foreach ($result as $val)
                            {


                                if ($i % 2 == 0)
                                    $bgcolor = "#E9F3FF";
                                else
                                    $bgcolor = "#FFFFFF";
                                $color_count = count($cid);
                                $number_start=''; $number_end='';
                                $number_start=$number_arr[$val[csf('bundle_no')]]['number_start'];
                                $number_end=$number_arr[$val[csf('bundle_no')]]['number_end'];
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>">
                                    <td align="center"><? echo $i; ?></td>
                                    <td align="center"><? echo $val[csf('bundle_no')]; ?></td>
                                    <td align="center"><? echo $size_library[$val[csf('size_number_id')]]; ?></td>
                                    <td align="right"><? echo $val[csf('production_qnty')]; ?></td>
                                </tr>
                                <?
                                $production_quantity += $val[csf('production_qnty')];
                                $size_qty_arr[$val[csf('size_number_id')]] += $val[csf('production_qnty')];
                                $size_wise_bundle_no_arr[$val[csf('size_number_id')]] += $val[csf('num_of_bundle')];
                                $total_bundle += $val[csf('num_of_bundle')];
                                $i++;
                            }
                            ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="4" style="padding: 1px !important;"></td>
                            </tr>
                            <tr>
                                <th colspan="3" align="right">Total</th>
                                <th align="right"><? echo $production_quantity; ?></th>
                            </tr>
                            </tfoot>
                        </table>
                    </td>
                    <td style="border: none;" valign="top">
                        <table cellspacing="0" border="1" rules="all" cellpadding="2" style="font: 12px tahoma;" width="320">
                            <thead>
                                <tr>
                                    <th colspan="4" align="left">Size Wise Summary</th>
                                </tr>
                                <tr bgcolor="#dddddd" align="center">
                                    <th width="30">SL</th>
                                    <th width="70">Size</th>
                                    <th>No Of Bundle</th>
                                    <th>Quantity (Pcs)</th>
                                </tr>
                            </thead>
                            <tbody>
                            <? $i = 1;
                            foreach ($size_qty_arr as $size_id => $size_qty):
                                $bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>">
                                    <td align="center"><? echo $i; ?></td>
                                    <td align="center"><? echo $size_library[$size_id]; ?></td>
                                    <td align="center"><? echo $size_wise_bundle_no_arr[$size_id]; ?></td>
                                    <td align="right"><? echo $size_qty; ?></td>
                                </tr>
                                <?
                                $i++;
                            endforeach; ?>
                            </tbody>
                            <tr>
                                <td colspan="4" style="padding: 1px !important;"></td>
                            </tr>
                            <tfoot>
                                <tr>
                                    <th colspan="2" align="right">Total</th>
                                    <th align="center"><? echo $total_bundle; ?></th>
                                    <th align="right"><? echo $production_quantity; ?></th>
                                </tr>
                            </tfoot>
                        </table>
                        <br>
                        <?
                        $job_no_implode = "'".implode("','", $job_no)."'";
                        $job_id_implode = "'".implode(",", $job_id)."'";
                        $all_po_id = "'".implode(",", $all_po_arr)."'";
                        $sqlTrimData = sql_select("select a.id, a.job_no, a.job_id, a.trim_group, b.item_name, c.po_number, c.po_quantity, a.description, a.cons_uom, a.cons_dzn_gmts, a.remark, d.total_set_qnty, d.order_uom from wo_pre_cost_trim_cost_dtls a, lib_item_group b, wo_po_break_down c, wo_po_details_master d where b.id = a.trim_group and c.job_no_mst = a.job_no and d.id = c.job_id and b.trim_type = 1 and c.id in ($all_po_id) order  by a.id asc");
                        if(count($sqlTrimData) > 0){
                        ?>
                            <table cellspacing="0" border="1" rules="all" cellpadding="3" style="font: 12px tahoma;" width="920">
                                <thead>
                                <tr>
                                    <th colspan="8" align="left"><strong>Required Sewing Trims</strong></th>
                                </tr>
                                <tr bgcolor="#dddddd" align="center">
                                    <th width="30">SL</th>
                                    <th width="120">Item Name</th>
                                    <th width="180">Description</th>
                                    <th width="110">Wo No.</th>
                                    <th width="90">Budget Cons/DZN</th>
                                    <th width="60">UOM</th>
                                    <th width="90">Required Qty.</th>
                                    <th>Remarks</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?
                                $sqlGetBookingData = sql_select("select pre_cost_fabric_cost_dtls_id, booking_no, wo_qnty from wo_booking_dtls where po_break_down_id in ($all_po_id) and status_active = 1  and is_deleted = 0");
                                $bookingArr = array();
                                foreach ($sqlGetBookingData as $key => $booking){
                                    $bookingArr[$booking[csf('pre_cost_fabric_cost_dtls_id')]]['booking_no'][$key] = $booking[csf('booking_no')];
                                    $bookingArr[$booking[csf('pre_cost_fabric_cost_dtls_id')]]['booking_qty'][$key] = $booking[csf('wo_qnty')];
                                }
                                $mainDataArr = array();
                                $totalQty = 0;
                                foreach ($sqlTrimData as $key => $preTrim){
                                    $keyMod = $preTrim[csf('item_name')]."*".$preTrim[csf('trim_group')]."*".$preTrim[csf('description')]."*".$preTrim[csf('cons_uom')];
                                    $mainDataArr[$keyMod]['item_name'] = $preTrim[csf('item_name')];
                                    $mainDataArr[$keyMod]['description'] = $preTrim[csf('description')];
                                    $mainDataArr[$keyMod]['uom'] =  $unit_of_measurement[$preTrim[csf('cons_uom')]];
                                    $mainDataArr[$keyMod]['cons_dzn'] += $preTrim[csf('cons_dzn_gmts')];
                                    $mainDataArr[$keyMod]['remarks'] = $preTrim[csf('remark')];
                                    $mainDataArr[$keyMod]['order_no'] = $preTrim[csf('po_number')];
                                    $mainDataArr[$keyMod]['po_qty'] = $preTrim[csf('po_quantity')];
                                    $mainDataArr[$keyMod]['set_qty'] = ($preTrim[csf('order_uom')] == 58 ? $preTrim[csf('total_set_qnty')] : 1);
                                    $mainDataArr[$keyMod]['booking_no'] = implode(', ',array_unique($bookingArr[$preTrim[csf('id')]]['booking_no']));
                                    $mainDataArr[$keyMod]['booking_qty'] = array_sum($bookingArr[$preTrim[csf('id')]]['booking_qty']);
                                }
                                $count = 1;
                                foreach ($mainDataArr as $val){
                                    if ($count % 2 == 0)
                                        $bgcolor = "#E9F3FF";
                                    else
                                        $bgcolor = "#FFFFFF";

                                    $reqQty = ($val['booking_qty'] / ($val['po_qty']*$val['set_qty'])) * $production_quantity;
                                    ?>
                                    <tr bgcolor="<?=$bgcolor?>">
                                        <td align="center"><?=$count?></td>
                                        <td><?=$val['item_name']?></td>
                                        <td><?=$val['description']?></td>
                                        <td align="center" ><?=$val['booking_no']?></td>
                                        <td align="right"><?=number_format($val['cons_dzn'], '2', '.', '')?></td>
                                        <td align="center"><?=$val['uom']?></td>
                                        <td align="right"><?=number_format($reqQty, 2)?></td>
                                        <td><?=$val['remarks']?></td>
                                    </tr>
                                    <?
                                    $totalQty += $reqQty;
                                    $count++;
                                }
                                ?>
                                </tbody>
                                <tr>
                                    <td colspan="8" style="padding: 1px !important;"></td>
                                </tr>
                                <tr>
                                    <th align="right" colspan="6"><strong>Total</strong></th>
                                    <th align="right"><?=number_format($totalQty, 2)?></th>
                                    <th></th>
                                </tr>

                            </table>
                            <?
                            }
                            ?>
                    </td>
                </tr>
            </table>
            <br>
            <?
            echo signature_table(28, $sig_company_id, "1240px");
            ?>
        </div>
    </div>
    <?

        if($is_mail_send==1){
            $emailBody=ob_get_contents();

            $sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=120 and  b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
            $mail_sql_res=sql_select($sql);

            $mailArr=array();
            foreach($mail_sql_res as $row)
            {
                $mailArr[$row['EMAIL']]=$row['EMAIL'];
            }

            if($mail_id!=''){$mailArr[]=$mail_id;}


            $to=implode(',',$mailArr);
            $subject="Bundle Wise Delivery to Sewing Input Challan/Gate Pass";

            if($to!=""){
                include('../../auto_mail/setting/mail_setting.php');
                $header=mailHeader();
                echo sendMailMailer( $to, $subject, $mail_body."<br><br>".$emailBody,$from_mail,'' );
            }
        }
    exit();
}

if ($action == "emblishment_issue_print_13") //Print 13 (Zayed)
{
    extract($_REQUEST);
    $data = explode('*', $data);
    $cbo_company_name = $data[0]; $is_mail_send = $data[5]; $mail_id = $data[6]; $mail_body = $data[7];if( $is_mail_send == 1){ob_start();}
    $company_library = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    $job_no = array();
    $job_id = array();
    $country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
    $supplier_library = return_library_array("select id,supplier_name from  lib_supplier where status_active=1 and is_deleted=0", "id", "supplier_name");
    $location_library = return_library_array("select id, location_name from  lib_location where status_active=1 and is_deleted=0", "id", "location_name");
    $floor_library = return_library_array("select id, floor_name from  lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
    $color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
    $size_library = return_library_array("select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0 ", 'id', 'buyer_name');
    $line_library = return_library_array("select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0", "id", "line_name");

    $sql = "SELECT id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
    production_source, serving_company, floor_id,sewing_line, organic, delivery_date,working_company_id,working_location_id from pro_gmts_delivery_mst where production_type=4 and id='$data[1]' and
    status_active=1 and is_deleted=0 ";
    //echo $sql;

    $dataArray = sql_select($sql);

    $nameArray = sql_select("SELECT id, auto_update from variable_settings_production where company_name='" . $dataArray[0][csf('serving_company')] . "' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];

    if ($prod_reso_allocation == 1)
    {
        if ($dataArray[0][csf('floor_id')] == 0 && $dataArray[0][csf('location_id')] != 0) $cond = " and a.location_id= {$dataArray[0][csf('location_id')]}";
        if ($dataArray[0][csf('floor_id')] != 0) $cond = " and a.floor_id= {$dataArray[0][csf('floor_id')]}";

        if ($db_type == 0) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
        } else if ($db_type == 2 || $db_type == 1) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
        }

        foreach ($line_data as $row) {
            $line = '';
            $line_number = explode(",", $row[csf('line_number')]);
            foreach ($line_number as $val) {
                if ($line == '') $line = $line_library[$val]; else $line .= "," . $line_library[$val];
            }
            $line_array[$row[csf('id')]] = $line;
        }
        $line = $line_array[$dataArray[0][csf('sewing_line')]];
    }
    else
    {
        $line = $line_library[$dataArray[0][csf('sewing_line')]];
    }
    unset($line_data);

    $production_quantity  =0;
    $total_bundle  = 0;
    $size_qty_arr=array();
    $size_wise_bundle_no_arr=array();
    ?>
    <style type="text/css">
        @media print
        {
            #footer_id {page-break-after: always;}
        }
    </style>
        <div style="width:930px;">
            <table cellspacing="0" style="font: 11px tahoma; width: 100%;">
                <tr>
                    <td colspan="5" align="center" style="font-size:24px">
                        <strong style="margin-left: 300px !important;"><? echo $company_library[$data[0]]; ?></strong>
                    </td>
                </tr>
                <tr class="form_caption">
                    <td colspan="6" align="center" style="font-size:14px">
                        <?
                        $nameArray = sql_select("SELECT plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
                        foreach ($nameArray as $result) {
                            echo $result[csf('city')];
                        }
                        unset($nameArray);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" align="center" style="font-size:20px"><u><strong>Bundle Wise Delivery to Sewing Input Challan/Gate Pass</strong></u></td>
                </tr>
                <tr>
                    <td width="95"><strong>Challan No</strong></td>
                    <td width="150"><? echo ": ".$dataArray[0][csf('sys_number')]; ?></td>
                    <td width="80"><strong>Source</strong></td>
                    <td width="190"><? echo ": ".$knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
                    <td width="120"><strong>Sew. Company</strong></td>
                    <td>
                        <?
                        if ($dataArray[0][csf('production_source')] == 1) echo ": ".$company_library[$dataArray[0][csf('serving_company')]];
                        else echo ": ".$supplier_library[$dataArray[0][csf('serving_company')]];
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><strong>Location </strong></td>
                    <td><? echo ": ".$location_library[$dataArray[0][csf('location_id')]]; ?></td>
                    <td><strong>Floor  </strong></td>
                    <td><? echo ": ".$floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
                    <td><strong>Line </strong></td>
                    <td><? echo ": ".$line; ?></td>
                </tr>
                <tr>
                    <td><strong>Working Company  </strong></td><td>: <? echo $company_library[$dataArray[0][csf('working_company_id')]]; ?></td>
                    <td><strong>Working Location </strong></td><td>: <? echo $location_library[$dataArray[0][csf('working_location_id')]]; ?></td>
                    <td><strong>Input Date  </strong></td>
                    <td><? echo ": ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
                </tr>
                <tr>
                    <td>Remarks</td>
                    <td colspan="3"><? //echo $dataArray[0][csf('sewing_line')];?></td>
                </tr>
                <tr>
                    <td colspan="6" id="barcode_img_id_<? echo $kk;?>"></td>
                </tr>
            </table>
            <br>
            <?

            $delivery_mst_id = $dataArray[0][csf('id')];

            if ($data[2] == 3) {
                $sql = "SELECT sum(b.production_qnty) as production_qnty,a.po_break_down_id,a.item_number_id,a.country_id,b.cut_no,b.bundle_no,b.barcode_no,d.size_number_id, d.color_number_id,
                count(b.id) as  num_of_bundle,d.article_number
                from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown d
                where a.delivery_mst_id ='$data[1]'
                and a.id=b.mst_id and b.production_type=4 and b.color_size_break_down_id=d.id and  b.status_active=1 and b.is_deleted=0 and d.status_active=1
                and d.is_deleted=0
                group by a.po_break_down_id,a.item_number_id,a.country_id,b.cut_no,b.bundle_no,b.barcode_no,d.size_number_id,d.color_number_id,d.article_number
                order by  length(b.bundle_no) asc, b.bundle_no  asc";//a.po_break_down_id,d.color_number_id
            }
            else {
                $sql = "SELECT sum(a.production_qnty) as production_qnty,c.po_break_down_id,c.item_number_id,c.country_id,a.cut_no,a.bundle_no,a.barcode_no,b.size_number_id, b.color_number_id,b.article_number
                from pro_garments_production_mst c, pro_garments_production_dtls a, wo_po_color_size_breakdown b
                where c.delivery_mst_id ='$data[1]'
                and c.id=a.mst_id and a.color_size_break_down_id=b.id and a.production_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
                group by c.po_break_down_id,c.item_number_id,c.country_id,a.cut_no,a.bundle_no,a.barcode_no,b.size_number_id,b.color_number_id,b.article_number order by  length(a.bundle_no) asc, a.bundle_no asc";
            }
            // echo $sql; die;

            $result = sql_select($sql);
            $all_cut_arr=array();
            $all_po_arr=array();
            $article_arr=array();

            foreach($result as $v)
            {
                $all_cut_arr[$v[csf("cut_no")]]=$v[csf("cut_no")];
                $all_po_arr[$v[csf("po_break_down_id")]]=$v[csf("po_break_down_id")];
                $bundle_no_arr[$v[csf("barcode_no")]]=$v[csf("barcode_no")];
                $article_arr[$v['PO_ID']][$v['COLOR_NUMBER_ID']][$v['SIZE_NUMBER_ID']][$v['ARTICLE_NUMBER']] = $v['ARTICLE_NUMBER'];
            }
            $all_cut_nos="'".implode("','", $all_cut_arr)."'";
            $all_po_nos="'".implode("','", $all_po_arr)."'";
            $bundle_nos="'".implode("','", $bundle_no_arr)."'";

            $batch_no_sql=sql_select("SELECT a.bundle_no, c.batch_no
            from ppl_cut_lay_bundle a, pro_roll_details c
            where a.roll_id=c.id
            and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.barcode_no in($bundle_nos)");
            $batch_no_arr=array();
            foreach($batch_no_sql as $val)
            {
                $batch_no_arr[$val[csf("bundle_no")]]=$val[csf("batch_no")];
            }
            // echo "<pre>"; print_r($batch_no_arr); die;

            $order_array = array();
            $order_sql = "SELECT a.id as job_id, a.job_no, a.buyer_name,a.style_ref_no, a.style_description,b.id, b.po_number, b.po_quantity,b.file_no,b.grouping,c.country_id from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in($all_po_nos)";
            $order_sql_result = sql_select($order_sql);
            foreach ($order_sql_result as $row)
            {
                $job_id[$row[csf('id')]] = $row[csf('job_id')];
                $job_no[$row[csf('id')]] = $row[csf('job_no')];
                $order_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
                $order_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
                $order_array[$row[csf('id')]]['po_number'] = $row[csf('po_number')];
                $order_array[$row[csf('id')]]['po_quantity'] = $row[csf('po_quantity')];
                $order_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
                $order_array[$row[csf('id')]]['style_des'] = $row[csf('style_description')];
                $order_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
                $order_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
                $order_array[$row[csf('id')]]['country_id'] = $row[csf('country_id')];
            }
            unset($order_sql_result);

            $sql_cut="SELECT bundle_no, number_start as number_start, number_end as number_end  from ppl_cut_lay_bundle where status_active=1 and is_deleted=0 and order_id in($all_po_nos)";
            $sql_cut_res = sql_select($sql_cut); $number_arr=array();
            foreach($sql_cut_res as $row)
            {
                $number_arr[$row[csf('bundle_no')]]['number_start']=$row[csf('number_start')];
                $number_arr[$row[csf('bundle_no')]]['number_end']=$row[csf('number_end')];
            }
            unset($sql_cut_res);
            ?>

            <div style="width:100%;">
                <table cellspacing="0" width="1240" border="1" rules="all" class="rpt_table" style="font: 12px tahoma;">
                    <thead bgcolor="#dddddd" align="center">
                    <th width="30">SL</th>
                    <th width="60" align="center">Bundle No</th>
                    <th width="60" align="center">Job</th>
                    <th width="80" align="center">Buyer</th>
                    <th width="80" align="center">File No</th>
                    <th width="80" align="center">Int. Ref.</th>
                    <th width="80" align="center">Article No</th>
                    <th width="80" align="center">Country</th>
                    <th width="80" align="center">Style Ref</th>
                    <th width="100" align="center">Style Des</th>
                    <th width="80" align="center">Order No.</th>
                    <th width="80" align="center">Gmt. Item</th>
                    <th width="80" align="center">Color</th>
                    <th width="100" align="center">Batch</th>
                    <th width="80" align="center">Size</th>
                    <th width="60" align="center">Reject Qty</th>
                    <th width="60" align="center">RMG Qty</th>
                    <th width="60" align="center">Gmt. Qty</th>
                    </thead>
                    <tbody>
                    <?
                    $i = 1;
                    $size_wise_bundle_no = 0;
                    $num_of_bundle = 0;
                    $tot_qnty = array();
                    // echo "<pre>"; print_r($result); die;
                    foreach ($result as $val)
                    {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";
                        $color_count = count($cid);
                        $number_start=''; $number_end='';
                        $number_start=$number_arr[$val[csf('bundle_no')]]['number_start'];
                        $number_end=$number_arr[$val[csf('bundle_no')]]['number_end'];

                        $article_no = $article_arr[$val['po_break_down_id']][$val['color_number_id']][$val['size_number_id']] = $val['ARTICLE_NUMBER'];
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>">
                            <td><? echo $i; ?></td>
                            <td align="center"><? echo $val[csf('bundle_no')]; ?></td>
                            <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['job_no']; ?></td>
                            <td align="center"><? echo $buyer_arr[$order_array[$val[csf('po_break_down_id')]]['buyer_name']]; ?></td>
                            <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['file_no']; ?></td>
                            <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['grouping']; ?></td>
                            <td align="center"><? echo $article_no ?></td>
                            <td align="center"><? echo $country_arr[$val[csf('country_id')]]; ?></td>
                            <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['style_ref']; ?></td>
                            <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['style_des']; ?></td>
                            <td align="center"><? echo $order_array[$val[csf('po_break_down_id')]]['po_number']; ?></td>
                            <td align="center"><? echo $garments_item[$val[csf('item_number_id')]]; ?></td>
                            <td align="center"><? echo $color_library[$val[csf('color_number_id')]]; ?></td>
                            <td align="center"><? echo $batch_no_arr[$val[csf("bundle_no")]]; ?></td>
                            <td align="center"><? echo $size_library[$val[csf('size_number_id')]]; ?></td>
                            <td></td>
                            <td align="center"><? echo $number_start.'-'.$number_end; ?></td>
                            <td align="right"><? echo $val[csf('production_qnty')]; ?></td>
                        </tr>
                        <?
                        $production_quantity += $val[csf('production_qnty')];
                        $total_bundle += $val[csf('num_of_bundle')];
                        $size_qty_arr[$val[csf('size_number_id')]] += $val[csf('production_qnty')];
                        $size_wise_bundle_no_arr[$val[csf('size_number_id')]] += $val[csf('num_of_bundle')];
                        $i++;
                    }
                    ?>
                    </tbody>
                    <tr>
                        <td colspan="13"></td>
                    </tr>
                    <tr>
                        <td colspan="3"><strong>No. Of Bundle :<? echo $total_bundle; ?></strong></td>
                        <td colspan="14" align="right"><strong>Grand Total </strong></td>
                        <td align="right"><? echo $production_quantity; ?></td>
                    </tr>
                </table>
                <br>
                <table cellspacing="0" border="1" rules="all" cellpadding="3" style="font: 12px tahoma;">
                    <thead>
                        <tr>
                            <td colspan="4"><strong>Size Wise Summary</strong></td>
                        </tr>
                        <tr bgcolor="#dddddd" align="center">
                            <th>SL</th>
                            <th>Size</th>
                            <th>No Of Bundle</th>
                            <th>Quantity (Pcs)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <? $i = 1;
                        foreach ($size_qty_arr as $size_id => $size_qty):
                            $bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>">
                                <td align="center"><? echo $i; ?></td>
                                <td align="center"><? echo $size_library[$size_id]; ?></td>
                                <td align="center"><? echo $size_wise_bundle_no_arr[$size_id]; ?></td>
                                <td align="right"><? echo $size_qty; ?></td>
                            </tr>
                            <?
                            $i++;
                        endforeach; ?>
                    </tbody>
                    <tr>
                        <td colspan="4" style="padding: 1px !important;"></td>
                    </tr>
                    <tr>
                        <td colspan="2" align="right"><strong>Total </strong></td>
                        <td align="center"><? echo $total_bundle; ?></td>
                        <td align="right"><? echo $production_quantity; ?></td>
                    </tr>
                </table>
                <br>
                <?
                $job_no_implode = "'".implode("','", $job_no)."'";
                $job_id_implode = "'".implode("','", $job_id)."'";
                $all_po_id = "'".implode("','", $all_po_arr)."'";
                    // echo $all_po_id;
                if($data[4]==1)
                {
                    $sql="SELECT A.ID,A.JOB_NO,A.JOB_ID,A.TRIM_GROUP,B.ITEM_NAME,C.PO_NUMBER,SUM(C.PO_QUANTITY) AS PO_QUANTITY,A.DESCRIPTION,A.CONS_UOM,A.CONS_DZN_GMTS,E.CONS_PCS,A.REMARK
                    FROM wo_pre_cost_trim_cost_dtls a,lib_item_group b,wo_po_break_down c,wo_po_details_master d,wo_pre_cost_trim_co_cons_dtls e
                    WHERE     b.id = a.trim_group
                    AND c.job_no_mst = a.job_no
                    AND d.id = c.job_id
                    AND d.id = e.job_id
                    And a.id=e. WO_PRE_COST_TRIM_COST_DTLS_ID
                    And c.id=e.PO_BREAK_DOWN_ID
                    AND b.trim_type = 1
                    AND c.id IN  ($all_po_id)
                    And a.STATUS_ACTIVE=1 and a.IS_DELETED=0
                    And b.STATUS_ACTIVE=1 and b.IS_DELETED=0
                    And c.STATUS_ACTIVE=1 and c.IS_DELETED=0
                    And d.STATUS_ACTIVE=1 and d.IS_DELETED=0
                    And e.STATUS_ACTIVE=1 and e.IS_DELETED=0
                    Group By A.ID,A.JOB_NO,A.JOB_ID,A.TRIM_GROUP,B.ITEM_NAME,C.PO_NUMBER,A.DESCRIPTION,A.CONS_UOM,A.CONS_DZN_GMTS,E.CONS_PCS,A.REMARK
                    ORDER BY a.id ASC";
                    // echo $sql;
                    //$sqlTrimData = sql_select("select a.id, a.job_no, a.job_id, a.trim_group, b.item_name, c.po_number, c.po_quantity, a.description, a.cons_uom, a.cons_dzn_gmts, a.remark, d.total_set_qnty, d.order_uom from wo_pre_cost_trim_cost_dtls a, lib_item_group b, wo_po_break_down c, wo_po_details_master d where b.id = a.trim_group and c.job_no_mst = a.job_no and d.id = c.job_id and b.trim_type = 1 and c.id in ($all_po_id) order  by a.id asc");
                    $sqlTrimData = sql_select($sql);
                    if(count($sqlTrimData) > 0){
                    ?>
                        <table cellspacing="0" border="1" rules="all" cellpadding="3" style="font: 12px tahoma;" width="1240">
                            <thead>
                                <tr>
                                    <th colspan="9" align="left"><strong>Required Sewing Trims</strong></th>
                                </tr>
                                <tr bgcolor="#dddddd" align="center">
                                    <th width="30">SL</th>
                                    <th width="140">Item Name</th>
                                    <th width="200">Description</th>
                                    <th width="120">Order No.</th>
                                    <th width="120">Wo No.</th>
                                    <th width="100">Budget Cons/DZN</th>
                                    <th width="70">UOM</th>
                                    <th width="100">Required Qty.</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                $sqlGetBookingData = sql_select("select pre_cost_fabric_cost_dtls_id, booking_no, wo_qnty from wo_booking_dtls where po_break_down_id in ($all_po_id) and status_active = 1  and is_deleted = 0");
                                $bookingArr = array();
                                foreach ($sqlGetBookingData as $key => $booking){
                                    $bookingArr[$booking[csf('pre_cost_fabric_cost_dtls_id')]]['booking_no'][$key] = $booking[csf('booking_no')];
                                    $bookingArr[$booking[csf('pre_cost_fabric_cost_dtls_id')]]['booking_qty'][$key] = $booking[csf('wo_qnty')];
                                }
                                $countArr = array();
                                foreach ($sqlTrimData as $key => $preTrim)
                                {
                                    if($preTrim[csf('cons_pcs')]>0)
                                    {
                                        $keyMod = $preTrim[csf('item_name')]."*".$preTrim[csf('trim_group')]."*".$preTrim[csf('description')]."*".$preTrim[csf('cons_uom')];
                                        $countArr[$keyMod]++;
                                    }
                                }
                                // echo "<pre>"; print_r($countArr);
                                $mainDataArr = array();
                                $totalQty = 0;
                                $totalcons_dzn = 0;
                                foreach ($sqlTrimData as $key => $preTrim){
                                    $keyMod = $preTrim[csf('item_name')]."*".$preTrim[csf('trim_group')]."*".$preTrim[csf('description')]."*".$preTrim[csf('cons_uom')];
                                    $mainDataArr[$keyMod]['item_name'] = $preTrim[csf('item_name')];
                                    $mainDataArr[$keyMod]['description'] = $preTrim[csf('description')];
                                    $mainDataArr[$keyMod]['uom'] =  $unit_of_measurement[$preTrim[csf('cons_uom')]];
                                    $mainDataArr[$keyMod]['cons_dzn'] += $preTrim[csf('cons_pcs')];
                                    $mainDataArr[$keyMod]['remarks'] = $preTrim[csf('remark')];
                                    $mainDataArr[$keyMod]['order_no'] = $preTrim[csf('po_number')];
                                    $mainDataArr[$keyMod]['po_qty'] = $preTrim[csf('po_quantity')];
                                    $mainDataArr[$keyMod]['set_qty'] = ($preTrim[csf('order_uom')] == 58 ? $preTrim[csf('total_set_qnty')] : 1);
                                    $mainDataArr[$keyMod]['booking_no'] = implode(', ',array_unique($bookingArr[$preTrim[csf('id')]]['booking_no']));
                                    $mainDataArr[$keyMod]['booking_qty'] = array_sum($bookingArr[$preTrim[csf('id')]]['booking_qty']);
                                }
                                $count = 1;
                                foreach ($mainDataArr as $key=>$val){
                                    if ($count % 2 == 0)
                                        $bgcolor = "#E9F3FF";
                                    else
                                        $bgcolor = "#FFFFFF";

                                    $cons_dzn=number_format(($val['cons_dzn']/$countArr[$key]), '2', '.', '');
                                    $reqQty =($cons_dzn / 12) * $production_quantity;
                                ?>
                                    <tr bgcolor="<?=$bgcolor?>">
                                        <td align="center"><?=$count;?></td>
                                        <td><?=$val['item_name'];?></td>
                                        <td><?=$val['description'];?></td>
                                        <td align="center"><?=$val['order_no'];?></td>
                                        <td align="center" ><?=$val['booking_no'];?></td>
                                        <td align="right"><?=$cons_dzn;?></td>
                                        <td align="center"><?=$val['uom'];?></td>
                                        <td align="right"><?=number_format($reqQty, 2);?></td>
                                        <td><?=$val['remarks']?></td>
                                    </tr>
                                <?
                                    $totalQty += $reqQty;
                                    $totalcons_dzn += $cons_dzn;
                                    $count++;
                                }
                                ?>
                            </tbody>
                            <tr>
                                <td colspan="9" style="padding: 1px !important;"></td>
                            </tr>
                            <tr>
                                <td align="right" colspan="7"><strong>Total</strong></td>
                                <td align="right"><?=number_format($totalQty, 2)?></td>
                                <td></td>
                            </tr>

                        </table>
                        <br>
                    <?
                    }
                }
                echo signature_table(28, $data[0], "900px");
                ?>
            </div>
        </div>
        <div id="footer_id"></div>

    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
        function generateBarcode(valuess) {
            var ids='<? echo $kk;?>';
            var value = valuess;//$("#barcodeValue").val();
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

            value = {code: value, rect: false};
            $("#barcode_img_id_"+ids).show().barcode(value, btype, settings);
        }
        generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
    </script>
    <?


    if($is_mail_send==1){
        $emailBody=ob_get_contents();

        $sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=120 and  b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
        $mail_sql_res=sql_select($sql);

        $mailArr=array();
        foreach($mail_sql_res as $row)
        {
            $mailArr[$row['EMAIL']]=$row['EMAIL'];
        }

        if($mail_id!=''){$mailArr[]=$mail_id;}


        $to=implode(',',$mailArr);
        $subject="Bundle Wise Delivery to Sewing Input Challan/Gate Pass";

        if($to!=""){
            include('../../auto_mail/setting/mail_setting.php');
            $header=mailHeader();
            echo sendMailMailer( $to, $subject, $mail_body."<br><br>".$emailBody,$from_mail,'' );
        }
    }

    exit();
}


if($action=="emblishment_issue_print_14") //Print 14 .
{
    //echo "Called Exit function in the beginning."; exit();
	extract($_REQUEST);
	$data=explode('*',$data);
	// print_r ($data); $data[5]=body part
	$cbo_template_id=$data[4];
	$sewingLineArray=return_library_array( "select id, line_name from lib_sewing_line", "id", "line_name");
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$floorArray=return_library_array( "select id, floor_name from lib_prod_floor", "id", "floor_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$location_library=return_library_array( "select id, location_name from  lib_location", "id", "location_name");
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	// $body_part_arr=return_library_array( "select id,bundle_use_for from ppl_bundle_title where company_id=$data[0]", "id", "bundle_use_for"  );
	$lib_country=return_library_array( "select id,country_name  from  lib_country", "id", "country_name"  );

	$sql_report_info="SELECT id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,inserted_by,
	production_source, serving_company, floor_id, organic, remarks, delivery_date,body_part,working_company_id,working_location_id from pro_gmts_delivery_mst where production_type=4 and id='$data[1]' and status_active=1 and is_deleted=0 ";
	// echo $sql_report_info; die;
	$info=sql_select($sql_report_info);
	$summarySQL = "SELECT a.sewing_line, a.item_number_id, b.cut_no, c.po_number, d.buyer_name, d.style_ref_no, e.color_number_id, e.size_number_id, a.country_id, b.production_qnty,b.reject_qty, b.bundle_qty, b.bundle_no, b.id, c.id as order_id, b.color_size_break_down_id, a.wo_order_id

	from
		pro_garments_production_mst   a,
		pro_garments_production_dtls  b,
		wo_po_color_size_breakdown    e,
		wo_po_break_down              c,
		wo_po_details_master          d,
		lib_size s
	where
		a.delivery_mst_id='$data[1]' and a.id = b.mst_id AND b.color_size_break_down_id = e.id AND e.po_break_down_id = c.id
		AND c.job_id = d.id and a.po_break_down_id=c.id and e.job_id=c.job_id and a.production_type = 4
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		and e.size_number_id = s.id
		order by s.sequence, length(b.bundle_no) asc, b.bundle_no asc
		";
	// echo $summarySQL; die;

	//data=1*13132*3*Bundle Issued to Print*1&action=emblishment_issue_print_9

	$summaryArray = sql_select($summarySQL);
	//echo "<pre>"; print_r($summaryArray);  die;
	$dataArray = array();
	$sizeArray = array();
	$bundleQty = array();
	$sizeBundleArray = array();
	$orderArray = array();
	$itemArray = array();
	$buyerArray = array();
	$woOrderArray = array();
	$cutNo = array();
	$podataArr=array();
    $size_wise_bundle_arr=array();
    foreach($summaryArray as $summery){
		$cutNo[$summery[csf('cut_no')]] = $summery[csf('cut_no')];
    }
	$orderArrayComma = "'" . implode ( "', '", $cutNo ) . "'";
	$rmgSQL = "SELECT b.bundle_no, concat(b.number_start, concat('-', b.number_end)) as rmg_qty, a.batch_id, a.table_no, b.order_id, c.order_cut_no,b.pattern_no,b.size_qty
	FROM ppl_cut_lay_mst a, ppl_cut_lay_bundle b, ppl_cut_lay_dtls c
	WHERE a.id = b.mst_id and a.id = c.mst_id and a.cutting_no IN ($orderArrayComma) and a.status_active=1 and b.status_active=1 and c.status_active=1";
	//echo $rmgSQL; die;
	$rmgQty = sql_select($rmgSQL);
	$bundleRMG = array();
	$batchArray = array();
	$tableArray = array();
	$orderCutArray = array();
	foreach($rmgQty as $rmg){
		$orderCutArray[$rmg[csf('order_id')]] = $rmg[csf('order_cut_no')];
		$bundleRMG[$rmg[csf('bundle_no')]] = $rmg[csf('rmg_qty')];
		$batchArray[$rmg[csf('batch_id')]] = $rmg[csf('batch_id')];
		$tableArray[$table_no_library[$rmg[csf('table_no')]]] = $table_no_library[$rmg[csf('table_no')]];
		$pattern_array[$rmg[csf('bundle_no')]] = $rmg[csf('pattern_no')];
		$lay_qty_array[$rmg[csf('bundle_no')]] += $rmg[csf('size_qty')];
	}

	foreach($summaryArray as $summery){
		// $cutNo[$summery[csf('cut_no')]] = $summery[csf('cut_no')];
		$orderArray[$summery[csf('order_id')]] = $summery[csf('order_id')];
		$woOrderArray[$summery[csf('wo_order_id')]] = $summery[csf('wo_order_id')];
		$sizeArray[$summery[csf('size_number_id')]] = $summery[csf('size_number_id')];
		$itemArray[$garments_item[$summery[csf('item_number_id')]]] = $garments_item[$summery[csf('item_number_id')]];
		$buyerArray[$buyer_arr[$summery[csf('buyer_name')]]] = $buyer_arr[$summery[csf('buyer_name')]];

		//$podataArr[$summery[csf('color_size_break_down_id')]]['po']=$summery[csf('po_number')];
		//$podataArr[$summery[csf('color_size_break_down_id')]]['color']=$summery[csf('color_number_id')];
		$podataArr[$summery[csf('color_size_break_down_id')]]['size']=$summery[csf('size_number_id')];

		$dataArray[$summery[csf('cut_no')]][$summery[csf('po_number')]][$summery[csf('country_id')]][$summery[csf('color_number_id')]]['cutting_no'] = $summery[csf('cut_no')];

		$dataArray[$summery[csf('cut_no')]][$summery[csf('po_number')]][$summery[csf('country_id')]][$summery[csf('color_number_id')]]['bundle_no'] = $summery[csf('bundle_no')];

		$dataArray[$summery[csf('cut_no')]][$summery[csf('po_number')]][$summery[csf('country_id')]][$summery[csf('color_number_id')]]['po_number'] = $summery[csf('po_number')];

		$dataArray[$summery[csf('cut_no')]][$summery[csf('po_number')]][$summery[csf('country_id')]][$summery[csf('color_number_id')]]['item_id'] = $summery[csf('item_number_id')];

		$dataArray[$summery[csf('cut_no')]][$summery[csf('po_number')]][$summery[csf('country_id')]][$summery[csf('color_number_id')]]['style_ref'] = $summery[csf('style_ref_no')];

		$dataArray[$summery[csf('cut_no')]][$summery[csf('po_number')]][$summery[csf('country_id')]][$summery[csf('color_number_id')]]['color'] = $summery[csf('color_number_id')];

		$dataArray[$summery[csf('cut_no')]][$summery[csf('po_number')]][$summery[csf('country_id')]][$summery[csf('color_number_id')]]['country'] = $summery[csf('country_id')];

		$dataArray[$summery[csf('cut_no')]][$summery[csf('po_number')]][$summery[csf('country_id')]][$summery[csf('color_number_id')]]['order_cut'] = $summery[csf('order_id')];

		$dataArray[$summery[csf('cut_no')]][$summery[csf('po_number')]][$summery[csf('country_id')]][$summery[csf('color_number_id')]]['in_qty'] += $summery[csf('production_qnty')];
		$dataArray[$summery[csf('cut_no')]][$summery[csf('po_number')]][$summery[csf('country_id')]][$summery[csf('color_number_id')]]['reject_qty'] += $summery[csf('reject_qty')];
		// $dataArray[$summery[csf('cut_no')]][$summery[csf('po_number')]][$summery[csf('country_id')]][$summery[csf('color_number_id')]]['size_array'][$summery[csf('size_number_id')]] += $summery[csf('production_qnty')];
		$dataArray[$summery[csf('cut_no')]][$summery[csf('po_number')]][$summery[csf('country_id')]][$summery[csf('color_number_id')]]['size_array'][$summery[csf('size_number_id')]] += $lay_qty_array[$summery[csf('bundle_no')]];

		$dataArray[$summery[csf('cut_no')]][$summery[csf('po_number')]][$summery[csf('country_id')]][$summery[csf('color_number_id')]]['bundle_array'][$summery[csf('size_number_id')]] += 1;

		$sizeBundleArray[$summery[csf('cut_no')]][$summery[csf('size_number_id')]][$pattern_array[$summery[csf('bundle_no')]]][$summery[csf('bundle_no')]]['gmts_qty'] =  $summery[csf('production_qnty')];
		$sizeBundleArray[$summery[csf('cut_no')]][$summery[csf('size_number_id')]][$pattern_array[$summery[csf('bundle_no')]]][$summery[csf('bundle_no')]]['lay_qty'] +=  $lay_qty_array[$summery[csf('bundle_no')]];

	}


	//$orderArrayComma = implode(',', $cutNo);
	//echo "<pre>"; print_r($orderCutArray); exit();
    // echo "<pre>"; print_r($size_wise_bundle_arr); exit();
	$woOrderArrayComma = implode(',', $woOrderArray);
	$bookingSQL = "SELECT booking_no FROM wo_booking_mst WHERE ID IN ($woOrderArrayComma)";
	$bookingQty = sql_select($bookingSQL);
	$bookingArray = array();
	foreach($bookingQty as $booking){
		$bookingArray[$booking[csf('booking_no')]] = $booking[csf('booking_no')];
	}
	//echo $orderArrayComma; die;
	$prevProdProcessSQL = "SELECT A.SEWING_LINE, B.CUT_NO, A.PO_BREAK_DOWN_ID, B.COLOR_SIZE_BREAK_DOWN_ID as COLORSIZEID, b.BUNDLE_NO, b.REJECT_QTY
	from pro_garments_production_mst a, pro_garments_production_dtls b
	where a.id = b.mst_id and a.production_type=4 and a.delivery_mst_id=$data[1] and b.cut_no in($orderArrayComma) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$prevProdProcessSQLArray = sql_select($prevProdProcessSQL);
    //echo "<pre>"; print_r($prevProdProcessSQLArray); exit();
    foreach($prevProdProcessSQLArray as $row)
    {
        $sizeBundleArray[$row['CUT_NO']][$podataArr[$row['COLORSIZEID']]['size']][$pattern_array[$row['BUNDLE_NO']]][$row['BUNDLE_NO']]['reject_qnty'] +=  $row['REJECT_QTY'];
    }

    $count_of_bundle_sql="SELECT e.size_number_id, count(b.bundle_no) as bundle_no FROM pro_garments_production_mst a,
    pro_garments_production_dtls b, wo_po_color_size_breakdown  e  WHERE  a.delivery_mst_id = '$data[1]'
    AND a.id = b.mst_id
    AND b.color_size_break_down_id = e.id
    AND a.production_type = 4
    AND a.status_active = 1
    AND a.is_deleted = 0
    AND b.status_active = 1
    AND b.is_deleted = 0
    GROUP BY
    e.size_number_id";

    $total_count_bundle_sql=sql_select($count_of_bundle_sql);
    $count_bundle_arr=array();

    foreach($total_count_bundle_sql as $row)
    {
        $count_bundle_arr[$row[csf('size_number_id')]]=$row[csf('bundle_no')];
    }
    // echo "<pre>"; print_r($count_bundle_arr); exit();




    $cutSizeQtyArray = array();
    $laySizeQtyArray = array();
    foreach($sizeBundleArray as $cutNo => $cutWise){
        foreach($cutWise as $sizeNumber => $sizeWise){
            foreach($sizeWise as $patternWise){
                foreach($patternWise as $bundleWise){
                    $cutSizeQtyArray[$cutNo][$sizeNumber] += $bundleWise['gmts_qty'];
                    $laySizeQtyArray[$cutNo][$sizeNumber] += $bundleWise['lay_qty'];
                }
            }
        }
    }

	//echo "<pre>"; print_r($cutSizeQtyArray);  die;

    $proResourceAllow = sql_select("select id, auto_update from variable_settings_production where company_name='$data[0]' and variable_list=23 and status_active=1 and is_deleted=0");

    $prod_reso_allocation = $proResourceAllow[0][csf('auto_update')];
    //echo $prod_reso_allocation; exit();
    $lineArray = array();
    if ($prod_reso_allocation == 1)
    {
        $lines = implode(',', $sewingLineArray);
        $mstSQL = sql_select("SELECT line_number from prod_resource_mst where id = '$data[8]' ");
        $line = '';
        foreach($mstSQL as $res)
        {
            $lineArray[] = $sewingLineArray[$res['LINE_NUMBER']];
        }
    }
    else
    {
        //library
        $lineArray[] = $sewingLineArray[$data[8]];
    }
	?>
	<style>
		tr.spaceUnder>td {
		padding-bottom: 1em;
	}
	.checkmark {
      display: inline-block;
      transform: rotate(45deg);
      height: 10px;
      width: 6px;
      margin-left: 60%;
      border-bottom: 5px solid #78b13f;
      border-right: 5px solid #78b13f;
	  margin-left: 0px;
    }
	@media print {
    .pagebreak {
        clear: both;
        page-break-after: always;
		page-break-inside:avoid;
    }
}

	</style>

		<div style="width: 1000px;">
			<table cellspacing="0" style="font: 11px tahoma; width: 100%;">
				<tr >
                    <td width="350"><strong id="barcode_img_id"></strong></td>
					<td colspan="5"  align="center" style="font-size:24px;"><strong><? echo $company_library[$info[0]['COMPANY_ID']]; ?></strong></td>


				</tr>
				<tr class="form_caption">
					<td colspan="100%" align="center" style="font-size:14px">
						<?
							$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
							foreach ($nameArray as $result)
							{
								echo ucfirst($result[csf('city')]);
							}
						?>
					</td>
				</tr>
				<tr class="spaceUnder">
					<td colspan="100%" align="center" style="font-size:20px"><u><strong>Bundle Wise Delivery to Sewing Input Challan/Gate Pass</strong></u></td>
				</tr>
				<tr>
					<td  width="90"><strong>Challan No</strong></td> <td width="140px">: <?= $info[0][csf('sys_number')]; ?></td>
					<td width="110"><strong>Source</strong></td><td width="175px"> : <?= $knitting_source[$data[4]]; ?></td>
					<td width="105"><strong>Sewing Company</strong></td><td width="155px">: <?=$company_library[$data[5]];?> </td>
					<td width="105"><strong>Batch</strong></td><td width="155px">: <?=implode(",", $batchArray);?> </td>
				</tr>
				<tr>
					<td><strong>Location</strong></td><td>: <?= $location_library[$info[0][csf('location_id')]]; ?></td>
					<td><strong>Floor</strong></td><td>: <?=$floorArray[$data[7]]; ?></td>
					<td><strong>Line</strong></td><td>: <?=implode(',', $lineArray); ?></td>
					<td><strong>Cut Table</strong></td><td>: <?=implode(",", $tableArray);?></td>
				</tr>
				<tr>
					<td><strong>Company</strong></td><td>: <?=$info[0][csf('production_source')]==1 ?$company_library[$info[0][csf('serving_company')]] : $supplier_library[$info[0][csf('serving_company')]];?></td>
					<td><strong>Input date</strong></td><td>: <?=$data[9]; ?></td>
					<td><strong>Buyer</strong></td><td>: <?=implode(',', $buyerArray); ?></td>
					<td><strong>Remarks</strong></td><td>:<?= $info[0][csf('remarks')];?></td>
				</tr>
			</table>


			<table class="details-view rpt_table" style="font-size:11px; margin-top:5px;" width="100%" cellspacing="0" cellpadding="0" border="1" align="left">
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th class="outer" rowspan="2" width="10">SL</th>
						<th class="inner" rowspan="2" width="50" align="center">Cutting No.</th>
						<th class="inner" rowspan="2" width="70" align="center">PO Number</th>
						<th class="inner" rowspan="2" width="50" align="center">Item</th>
						<th class="inner" rowspan="2" width="70" align="center">Style Ref</th>
						<th class="inner" rowspan="2" width="70" align="center">Color</th>
						<th class="inner" rowspan="2" width="50" align="center">Country</th>
						<th class="inner" rowspan="2" width="30" align="center">Cut</th>
						<th class="inner" colspan="<?=count($sizeArray);?>" width="50" align="center">Size</th>
						<th class="inner" colspan="3"  width="150" align="center"></th>

					</tr>
					<tr>
						<?php
						foreach($sizeArray as $size)
						{

							?>
							<th class="inner" rowspan="2" width="50" align="center"><?=$size_library[$size]; ?></th>
							<?php
						}
							?>
						<th class="inner" rowspan="2" width="50" align="center">Input Qty</th>
						<th class="inner" rowspan="2" width="50" align="center">Bundle Qty</th>
						<th class="inner" rowspan="2" width="50" align="center">Rej Qty</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$i = 1;
                        $tot_in_qty = 0;
                        $tot_rej_qty = 0;
						foreach($dataArray as $cutting_no => $cuttingWise)
						{
							$i % 2 == 0 ? $bgcolor = "#FFFFFF" : $bgcolor = "#E9F3FF";
							$cuttingArray = explode("-", $cutting_no);
							$newCutting = ltrim($cuttingArray[2], 0);
							foreach($cuttingWise as $po_number => $poWise)
							{
								foreach($poWise as $country => $countryWise)
								{
									foreach($countryWise as $color => $colorWise)
									{
										//echo "<pre>"; print_r($colorWise); exit();
										?>
										<tr style="font-size:12px" bgcolor="<?=$bgcolor;?>">
											<td class="outer"><?=$i; ?></td>
											<td style="word-break: break-all;" class="inner" align="center"><?php echo $newCutting; ?></td>
											<td style="word-break: break-all;" class="inner" align="center"><?php echo $po_number; ?></td>
											<td style="word-break: break-all;" class="inner" align="center"><?=$garments_item[$colorWise['item_id']];?></td>
											<td style="word-break: break-all;" class="inner" align="center"><?=$colorWise['style_ref'];?></td>
											<td style="word-break: break-all;" class="inner" align="center"><?php echo $color_library[$color] ; ?></td>
											<td style="word-break: break-all;" class="inner" align="center"><?php echo $lib_country[$country]; ?></td>
											<td style="word-break: break-all;" class="inner" align="center"><?=$orderCutArray[$colorWise['order_cut']]; ?></td>
											<?php


                                            //$summery[csf('production_qnty')];
											$total_qty = 0;
                                            $total_layQty = 0;
											foreach($sizeArray as $key => $value)
											{
                                               // $inputQty = $sizeBundleArray[$cutting_no][$key][$pattern_array[$colorWise['bundle_no']]][$colorWise['bundle_no']]['gmts_qty'];

												$total_qty += $cutSizeQtyArray[$cutting_no][$key];
												$bundleQty[$key] += $total_qty;

                                                $total_layQty += $laySizeQtyArray[$cutting_no][$key];
                                                $layQtyArr[$key] += $total_layQty;
												?>
													<td class="inner" width="24" align="center"><?=$cutSizeQtyArray[$cutting_no][$key]; ?></td>
												<?php
											}
                                            $rej_qty = $total_layQty - $total_qty;
												?>
												<td class="inner" align="center"><?=$total_qty;?></td>
												<td class="inner" align="center"><?=$total_layQty;?></td>
												<td class="inner" align="center"><?=$rej_qty;?></td>
										</tr>
										<?php
									}
								}
							}
							$i++;
                            $tot_in_qty += $colorWise['in_qty'];
                            $tot_rej_qty += $rej_qty;
                            $total_lay_qty += $total_layQty;
                            $total_bundle_qty += $total_qty;
						}
					?>
				</tbody>
				<tbody>
					<tr bgcolor="#cddcdc">

						<td class="outer" colspan="8" align="right"><strong>Total Qty :</strong></td>
						<?php

						//$total_bundle_qty = 0;
						//$total_lay_qty = 0;
						foreach($sizeArray as $key => $value)
                        {
							//$total_bundle_qty += $bundleQty[$key];
							//$total_lay_qty += $layQtyArr[$key];
						    ?>
						    <td class="inner" width="24" align="center"><? echo $bundleQty[$key]; ?></td>
						    <?php
                        }
                        ?>
						<td class="inner" align="center"><?=$total_bundle_qty; ?></td>
						<td class="inner" align="center"><?=$total_lay_qty; ?></td>
						<td class="inner" align="center"><?=$tot_rej_qty; ?></td>
					</tr>
					<tr bgcolor="#DDDDDD" height="24">

						<td class="outer" colspan="8" align="right"><strong>Bundle QTY :</strong></td>
						<?php
						/* $total_bundle_qty = 0;
						foreach($bundleQty as $qty){
							$total_bundle_qty += $qty;
						 */
						foreach($sizeArray as $key => $value){

						?>
						<td class="inner" width="24" align="center"><? echo $count_bundle_arr[$key]; ?></td>
						<?php } ?>
						<td class="inner" align="center"><?//=$total_bundle_qty; ?></td>
						<td class="inner" align="center"></td>
						<td class="inner" align="center"></td>
					</tr>
				</tbody>
			</table>
			<?php
				//$item_segment = 53;
				$item_segment = 47;
				$current_row = 1;
				$current_cols = 1;
				$table_data = '';
				$end_table = 0;
				$first = 1;

				$grand_total = 0;
                $grand_total_bundle_qty = 0;
                $grand_total_input_qty = 0;
                $grand_total_reject_qty = 0;
				$serial = 1;
				foreach($sizeBundleArray as $cut_no => $cutWise)
				{
					foreach($cutWise as $size => $sizeWise)
					{
                        $sub_bundle_qty = 0;
                        $sub_input_qty = 0;
                        $sub_reject_qty = 0;
                        foreach($sizeWise as $pattern => $pattern_data)
						{
                            $sl=1;
                            foreach($pattern_data as $bundle => $r)
                            {
                                $serial % 2 == 0 ? $bgcolor = "#FFFFFF" : $bgcolor = "#E9F3FF";
                                $bundleArray = explode('-', $bundle);
                                if(!empty($bundleArray[4])){
                                    $newBundle = $bundleArray[2]."-".$bundleArray[3]."-".$bundleArray[4];
                                }else{
                                    $newBundle = $bundleArray[2]."-".$bundleArray[3];
                                }

                                // $bundle_qty = $r['gmts_qty']+ $r['reject_qnty'];
                                $bundle_qty = $r['lay_qty'];
                                if($current_row ==1)
                                {
                                    $table_data .= '
                                    <table class="details-view rpt_table" style="font-size:15px; margin:5px;"  cellspacing="0" cellpadding="0" border="1" align="left" width="490">
                                    <thead bgcolor="#dddddd" align="center">
                                        <tr>
                                            <th class="outer" width="30" align="center">SL</th>
                                            <th class="inner" width="130" align="center">Size</th>
                                            <th class="inner" width="30" align="center">Ptn</th>
                                            <th class="inner" width="100" align="center">Bundle No.</th>
                                            <th class="inner" width="50" align="center">RMG Qty</th>
                                            <th class="inner" width="40" align="center">Bundle Qty </th>
                                            <th class="inner" width="40" align="center">Input Qty</th>
                                            <th class="inner" width="50" align="center">Reject</th>

                                        </tr>
                                    </thead>
                                    <tbody>';
                                    $first++;
                                }
                                $rej_qty = $bundle_qty - $r['gmts_qty'];
                                $table_data .= '<tr bgcolor='.$bgcolor.'>
                                                        <td class="outer"  width="30" align="center">'.$sl.'</td>
                                                        <td class="inner"  width="130" align="center">'.$size_library[$size].'</td>
                                                        <td class="inner"  width="30" align="center">'.$pattern.'</td>
                                                        <td class="inner"  width="100" align="center">'.$newBundle.'</td>
                                                        <td class="inner"  width="50" align="center">'.$bundleRMG[$bundle].'</td>
                                                        <td class="inner"  width="40" align="center">'.$bundle_qty.'</td>
                                                        <td class="inner"  width="40" align="center">'.$r['gmts_qty'].'</td>
                                                        <td class="inner"  width="50" align="center">'.$rej_qty.'</td>
                                                    </tr>';
                                $serial++;
                                $sl++;
                                $sub_bundle_qty += $bundle_qty;
                                $sub_input_qty += $r['gmts_qty'];
                                $sub_reject_qty += $rej_qty;
                                if ($current_row == $item_segment)
                                {
                                    // echo $current_row .'=='. $item_segment."<br>";
                                    $current_row = 1;
                                    $table_data .= '</tbody></table>';

                                    if($current_cols == 2)
                                    {
                                        $end_table = 1;
                                        // $table_data .= '<p style="page-break-after: always;"></p>';
                                        $table_data .= '<div class="pagebreak"></div><br clear="all">';
                                        $current_cols = 1;
                                        //$item_segment = 67;
                                        $item_segment = 60;

                                    }
                                    else
                                    {
                                        $current_cols++;
                                    }

                                }
                                else
                                {
                                    $current_row++;
                                }

                            }
                            $table_data .= '<tr bgcolor="#DDDDDD">

                                            <td class="outer" align="center"></td>
                                            <td class="inner" align="center">Bundle Qty</td>
                                            <td class="inner" align="center">'.($sl-1).'</td>
                                            <td class="inner" align="center"></td>
                                            <td class="inner" align="center"></td>
											<td class="inner" align="center"></td>
											<td class="inner" align="center"></td>
											<td class="inner" align="center"></td>
										</tr>';
                        }
						$grand_total_bundle_qty += $sub_bundle_qty;
						$grand_total_input_qty += $sub_input_qty;
						$grand_total_reject_qty += $sub_reject_qty;
						$table_data .= '<tr bgcolor="#DDDDDD">
											<td class="inner" colspan="5" align="right">Size Total</td>
											<td class="inner"  width="40" align="center">'.$sub_bundle_qty.'</td>
											<td class="inner"  width="60" align="center">'.$sub_input_qty.'</td>
											<td class="inner"  width="40" align="center">'.$sub_reject_qty.'</td>
										</tr>';
					}
				}
				$table_data .= '<tfoot>
									<tr bgcolor="#DDDDDD">
										<th class="inner" colspan="5" align="right">Grand Total</th>
										<th class="inner"  width="40" align="center">'.$grand_total_bundle_qty.'</th>
										<th class="inner"  width="60" align="center">'.$grand_total_input_qty.'</th>
										<th class="inner"  width="40" align="center">'.$grand_total_reject_qty.'</th>
									</tr>
								</tfoot></table>';
				echo $table_data;
				?>
			<br clear="all">
			<?php //echo signature_table(243, $data[0], "900px","","10"); ?>
		</div>
        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
        function generateBarcode( valuess ){
                var value = valuess;//$("#barcodeValue").val();
                var btype = 'code39';//$("input[name=btype]:checked").val();
                var renderer ='bmp';// $("input[name=renderer]:checked").val();
                var settings = {
                  output:renderer,
                  bgColor: '#FFFFFF',
                  color: '#000000',
                  barWidth: 1,
                  barHeight: 30,
                  moduleSize:5,
                  posX: 10,
                  posY: 20,
                  addQuietZone: 1
                };
                 value = {code:value, rect: false};
                $("#barcode_img_id").show().barcode(value, btype, settings);
            }
            generateBarcode('<? echo $info[0][csf('sys_number')]; ?>');
         </script>

	<?
	exit();
}

if($action=="sewing_input_challan_print_15") //Print 15
{

    extract($_REQUEST);
    $data=explode('*',$data);
    $cbo_company_name = $data[0]; $is_mail_send = $data[4]; $mail_id = $data[5]; $mail_body = $data[6];if( $is_mail_send == 1){ob_start();}
    // print_r($data);
    $company_library=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    $supplier_library=return_library_array( "select id,supplier_name from  lib_supplier where status_active=1 and is_deleted=0", "id","supplier_name");
    $location_library=return_library_array( "select id, location_name from  lib_location where status_active=1 and is_deleted=0", "id", "location_name");
    $floor_library=return_library_array( "select id, floor_name from  lib_prod_floor where status_active=1 and is_deleted=0", "id", "floor_name");
    $color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0 ", "id", "color_name" );
    $size_library=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
    $line_library = return_library_array("select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0", "id", "line_name");
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
    $body_part_arr=return_library_array( "select id,bundle_use_for from ppl_bundle_title where company_id=$data[0]",'id','bundle_use_for');



    $sql="SELECT id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, delivery_basis, embel_name, embel_type,
    production_source, serving_company, floor_id, sewing_line, organic, delivery_date,working_company_id,working_location_id from pro_gmts_delivery_mst where production_type=4 and id='$data[1]' and
    status_active=1 and is_deleted=0 ";
    $dataArray=sql_select($sql);

    $nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='" . $dataArray[0][csf('company_id')] . "' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];

    if ($prod_reso_allocation == 1) {

        if ($dataArray[0][csf('floor_id')] == 0 && $dataArray[0][csf('location_id')] != 0) $cond = " and a.location_id= {$dataArray[0][csf('location_id')]}";
        if ($dataArray[0][csf('floor_id')] != 0) $cond = " and a.floor_id= {$dataArray[0][csf('floor_id')]}";

        if ($db_type == 0) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
        } else if ($db_type == 2 || $db_type == 1) {
            $line_data = sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
        }

        foreach ($line_data as $row) {
            $line = '';
            $line_number = explode(",", $row[csf('line_number')]);
            foreach ($line_number as $val) {

                if ($line == '') $line = $line_library[$val]; else $line .= "," . $line_library[$val];
            }
            $line_array[$row[csf('id')]] = $line;
        }
        $line = $line_array[$dataArray[0][csf('sewing_line')]];
    } else {
        $line = $line_library[$dataArray[0][csf('sewing_line')]];
    }

    $cut_floor_lay_arr=array();
	$cut_floor_sql="SELECT a.cutting_no,
	a.floor_id,
	b.delivery_mst_id,
	b.CUT_NO

    FROM ppl_cut_lay_mst a, pro_garments_production_mst b
    WHERE    a.cutting_no= b.CUT_NO
	AND b.delivery_mst_id='$data[1]'
	AND a.status_active = 1
	AND a.is_deleted = 0";
	$cut_floor_sql_data=sql_select($cut_floor_sql);
	foreach($cut_floor_sql_data as $row)
	{
		$cut_floor_lay_arr[$row[csf('cutting_no')]][$row[csf('floor_id')]]=$row[csf('floor_id')];
	}

	?>
	<div style="width:900px;">
	    <table cellspacing="0" style="font: 11px tahoma; width: 100%;">
	        <tr>
	            <td colspan="6" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="6" align="center" style="font-size:14px">
	                <?
	                    $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");


	                    foreach ($nameArray as $result)
	                    {

	                         echo $result[csf('city')];

	                    }
	                ?>
	            </td>
	        </tr>
	        <tr>
	            <td colspan="6" align="center" style="font-size:20px"><u><strong> Sewing Input Challan/Gate Pass</strong></u></td>
	        </tr>
	        <tr>
	            <td width="90"><strong>Challan No</strong></td> <td width="140px">: <? echo $dataArray[0][csf('sys_number')]; ?></td>
	            <td width="110"><strong>Input Date</strong></td><td width="175px"> : <? echo ": ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
	            <td width="105"><strong>Barcode</strong></td><td  colspan="4" id="barcode_img_id"></td>
	        </tr>
	        <tr>

	            <td><strong>Sewing Source</strong></td><td>:
	                <?
	                    echo $knitting_source[$dataArray[0][csf('production_source')]];
	                ?>
	            </td>
	            <td><strong>Sewing Company</strong></td><td>: <? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]];
	                    else echo $supplier_library[$dataArray[0][csf('serving_company')]]; ?> </td>
	            <td><strong>Location</strong></td><td>: <? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
	        </tr>
	        <tr>
	            <td><strong>Floor </strong></td><td>: <? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
	            <td><strong>Line </strong></td><td><? echo ": ".$line; ?></td>
	            <td><strong>Organic</strong></td><td> : <? echo $dataArray[0][csf('organic')]; ?></td>
	        </tr>
	        <tr>
	            <td><strong>Body Part  </strong></td><td>: <? echo $body_part_arr[$dataArray[0][csf('body_part')]]; ?></td>
	            <td><strong>Cutting Floor  </strong></td><td>: <? echo $floor_library[$cut_floor_lay_arr[$row[csf('cutting_no')]][$row[csf('floor_id')]]]; ?> </td>
	        </tr>
	    </table>
	         <br>
	    <?

	        $delivery_mst_id =$dataArray[0][csf('id')];
	        $cut_nos_all=sql_select("select cut_no from pro_garments_production_dtls a  where a.status_active=1 and a.is_deleted=0 and  a.delivery_mst_id='$delivery_mst_id' and production_type=4 group by cut_no");
	        $cut_number_string="";
	        foreach($cut_nos_all as $cut_val)
	        {
	            if($cut_number_string=="")
	            {
	                $val=$cut_val[csf('cut_no')];
	                 $cut_number_string.="'$val'";
	            }
	            else
	            {
	                $val=$cut_val[csf('cut_no')];
	                $cut_number_string.=','."'$val'";
	            }
	        }

            $sqls="SELECT b.cut_no, b.production_qnty, a.country_id, d.color_number_id, d.size_number_id, e.style_ref_no, e.style_description, e.job_no, e.buyer_name,f.po_number, f.grouping,f.id as po_id, f.po_quantity,b.bundle_no,d.article_number from  pro_garments_production_mst a, pro_garments_production_dtls b,wo_po_color_size_breakdown d, wo_po_details_master e,wo_po_break_down f  where
                a.delivery_mst_id ='$data[1]' and a.production_type=4 and b.production_type=4  and e.id=f.job_id and e.id=d.job_id  and f.id=a.po_break_down_id
                and a.id=b.mst_id  and b.color_size_break_down_id=d.id  and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
                and b.is_deleted=0  and f.status_active=1  and f.is_deleted=0 and d.status_active=1
                and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 order by e.job_no,d.size_order";
            // echo $sqls; die;
            $result=sql_select($sqls);

            $order_cut_no_sql=sql_select("SELECT a.batch_id,b.order_ids as po,b.order_cut_no as ord_cut, b.roll_data, a.cutting_no from ppl_cut_lay_mst a,ppl_cut_lay_dtls b where a.id=b.mst_id and a.cutting_no in ($cut_number_string)");
            foreach($order_cut_no_sql as $rows)
            {
                if(strpos($rows[csf("po")], ",")==false)
                {
                    $order_cut_no_arr[$rows[csf("cutting_no")]][$rows[csf("po")]]=$rows[csf("ord_cut")];
                }
                else
                {
                    $po_ids=$rows[csf("po")];
                    $po_ids=explode(",", $po_ids);
                    foreach($po_ids as $po_val)
                    {
                        $order_cut_no_arr[$rows[csf("cutting_no")]][$po_val]=$rows[csf("ord_cut")];
                    }
                }

                // $batch_id_arr[$rows[csf("cutting_no")]]=$rows[csf("batch_id")];
                $roll_data_arr=explode("**",$rows["ROLL_DATA"]);
                $batch_id_arr2  = array();
                foreach($roll_data_arr as $val)
                {
                    $roll_data=explode("=",$val);
                    if (!$batch_id_arr2 [$rows[csf("cutting_no")]][$roll_data[5]]) {
                        $batch_id_arr[$rows[csf("cutting_no")]].=$roll_data[5].",";
                    }

                    $batch_id_arr2 [$rows[csf("cutting_no")]][$roll_data[5]] = $roll_data[5];

                }
            }
            // echo "<pre>"; print_r( $batch_id_arr); die;
            foreach($result as $rows)
            {

                $key=$rows[csf('country_id')].$rows[csf('buyer_name')].$rows[csf('job_no')].$rows[csf('po_id')].$rows[csf('color_number_id')].$rows[csf('style_ref_no')].$rows[csf('style_description')].$rows[csf('cut_no')].$rows[csf('order_cut_no')];

                $bundle_size_arr[$rows[csf('size_number_id')]]=$rows[csf('size_number_id')];
                $dataArr[$key]=array(
                    'country_id'=>$rows[csf('country_id')],
                    'buyer_name'=>$rows[csf('buyer_name')],
                    'po_id'=>$rows[csf('po_id')],
                    'po_number'=>$rows[csf('po_number')],
                    'grouping'=>$rows[csf('grouping')],
                    'color_number_id'=>$rows[csf('color_number_id')],
                    'size_number_id'=>$rows[csf('size_number_id')],
                    'style_ref_no'=>$rows[csf('style_ref_no')],
                    'style_description'=>$rows[csf('style_description')],
                    'job_no'=>$rows[csf('job_no')],
                    'cut_no'=>$rows[csf('cut_no')],
                    'order_cut_no'=>$rows[csf('order_cut_no')]

                );
                // $batch_wise_bndl_array[$key] .= $batch_array[$rows[csf("bundle_no")]].",";

                $productionQtyArr[$key]+=$rows[csf('production_qnty')];
                $sizeQtyArr[$key][$rows[csf('size_number_id')]]+=$rows[csf('production_qnty')];
                $bundleArr[$key][$rows[csf('bundle_no')]]=$rows[csf('bundle_no')];

                $article_arr[$rows['PO_ID']][$rows['COLOR_NUMBER_ID']][$rows['ARTICLE_NUMBER']] = $rows['ARTICLE_NUMBER'];

            }
	        $table_width=980+(count($bundle_size_arr)*50);
	    ?>
        <div style="width:100%;">
            <table align="left" cellspacing="0" width="<? echo $table_width; ?>"  border="1" rules="all" class="rpt_table" style=" margin-top:20px;" >
                <thead bgcolor="#dddddd" align="center">
                    <tr>
                        <th width="30" rowspan="2">SL</th>
                        <th width="80" align="center" rowspan="2">Buyer</th>
                        <th width="80" align="center" rowspan="2">Job No</th>
                        <th width="80" align="center" rowspan="2">Style Ref</th>
                        <th width="80" align="center" rowspan="2">Internal Ref</th>
                        <th width="80" align="center" rowspan="2">PO Number</th>
                        <th width="80" align="center" rowspan="2">Article No</th>
                        <th width="80" align="center" rowspan="2">Country</th>
                        <th width="80" align="center" rowspan="2">Cutting No</th>
                        <th width="80" align="center" rowspan="2">Order Cut</th>
                        <th width="100" align="center" rowspan="2">Batch No</th>
                        <th width="80" align="center" rowspan="2">Color</th>
                        <th align="center" width="50" colspan="<? echo count($bundle_size_arr); ?>">Size</th>
                        <th width="80" align="center" rowspan="2">Total Issue Qty</th>
                        <th width="80" align="center" rowspan="2">No of Bundle</th>
                        <th width= align="center" rowspan="2">Remarks</th>
                    </tr>
                    <tr>
                        <?
                        $i=0;
                        foreach($bundle_size_arr as $inf)
                        {
                        ?>
                        <th align="center" width="50" rowspan="2"><? echo $size_library[$inf]; ?></th>
                        <?
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?
                        $i=1;
                        $tot_qnty=array();
                        foreach($dataArr as $key=>$row)
                        {
                            $article_arr2 = $article_arr[$rows['PO_ID']][$rows['COLOR_NUMBER_ID']];
                            $article_no  = implode(',',$article_arr2);
                            $batch_no = implode(",",array_unique(array_filter(explode(",",$batch_wise_bndl_array[$key]))));
                            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                            ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" style="font-size:12px">
                                    <td><? echo $i; ?></td>
                                    <td align="center"><? echo $buyer_arr[$row['buyer_name']]; ?></td>
                                    <td align="center"><? echo $row['job_no']; ?></td>
                                    <td align="center"><? echo $row['style_ref_no']; ?></td>
                                    <td align="center"><p><? echo $row['grouping']; ?></p></td>
                                    <td align="center"><? echo $row['po_number'];?></td>
                                    <td align="center"><? echo trim($article_no,',');?></td>
                                    <td align="center"><? echo $country_library[$row['country_id']]; ?></td>
                                    <td align="center"><? echo $row['cut_no']; ?></td>
                                    <td align="center"><? echo $order_cut_no_arr[$row['cut_no']][$row['po_id']]; ?></td>
                                    <td align="center"><? echo trim($batch_id_arr[$row['cut_no']],','); ?></td>
                                    <td align="center"><? echo $color_library[$row['color_number_id']]; ?></td>
                                    <?
                                    foreach($bundle_size_arr as $size_id)
                                    {
                                        $size_qty=0;
                                        $size_qty=$sizeQtyArr[$key][$size_id];
                                        ?>
                                        <td align="center" width="50"><? echo $size_qty; ?></td>
                                        <?
                                        $grand_total_size_arr[$size_id]+=$size_qty;
                                    }
                                    ?>
                                    <td align="center"><? echo $productionQtyArr[$key]; ?></td>
                                    <td align="center"><? echo count($bundleArr[$key]); ?></td>
                                    <td align="center"></td>
                                </tr>
                            <?
                            $i++;
                            $grand_total_qty+=$productionQtyArr[$key];
                            $grand_total_bundle_num+=count($bundleArr[$key]);
                            $grand_total_reject_qty+=$val['reject_qty'];
                        }
                    ?>
                </tbody>
                <tr bgcolor="#DDDDDD">
                    <td colspan="12" align="right"><strong>Grand Total :</strong></td>
                    <?
                        foreach($bundle_size_arr as $size_id)
                        {
                            ?>
                            <td align="center" width="50"><? echo $grand_total_size_arr[$size_id]; ?></td>
                            <?
                        }
                    ?>
                    <td align="center"><? echo $grand_total_qty; ?></td>
                    <td align="center"><? echo $grand_total_bundle_num; ?></td>
                    <td  align="center"><?  //echo $val[csf('num_of_bundle')]; ?></td>
                </tr>
            </table>
        </div>

        </div>
            <?
                echo signature_table(226, $data[0], "900px");
            ?>
            <br>
        </div>

	</div>
	    <script type="text/javascript" src="../../js/jquery.js"></script>
	    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
	    function generateBarcode( valuess )
        {
            var value = valuess;//$("#barcodeValue").val();
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer ='bmp';// $("input[name=renderer]:checked").val();
            var settings = {
                output:renderer,
                bgColor: '#FFFFFF',
                color: '#000000',
                barWidth: 1,
                barHeight: 30,
                moduleSize:5,
                posX: 10,
                posY: 20,
                addQuietZone: 1
            };

                value = {code:value, rect: false};
            $("#barcode_img_id").show().barcode(value, btype, settings);
	    }
        generateBarcode('<? echo $dataArray[0][csf('sys_number')]; ?>');
	</script>
	<?
        if($is_mail_send==1)
        {
            $emailBody=ob_get_contents();

            $sql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=120 and  b.mail_user_setup_id=c.id and a.company_id =$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
            $mail_sql_res=sql_select($sql);

            $mailArr=array();
            foreach($mail_sql_res as $row)
            {
                $mailArr[$row['EMAIL']]=$row['EMAIL'];
            }

            if($mail_id!=''){$mailArr[]=$mail_id;}


            $to=implode(',',$mailArr);
            $subject="Bundle Wise Delivery to Sewing Input Challan/Gate Pass";

            if($to!=""){
                include('../../auto_mail/setting/mail_setting.php');
                $header=mailHeader();
                echo sendMailMailer( $to, $subject, $mail_body."<br><br>".$emailBody,$from_mail,'' );
            }
        }

        exit();

}
?>
