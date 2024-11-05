<?
header('Content-type:text/html; charset=utf-8');
session_start();
require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.conversions.php');

if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
$user_id = $_SESSION['logic_erp']["user_id"];
$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

$subprocessforwashin=implode(",",$subprocessForWashArr);
//--------------------------------------------------------------------------------------------

//$color_arr = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

if ($action == "load_drop_down_location") {
	echo create_drop_down("cbo_location_name", 170, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- Select Location --", 0, "");
	exit();
}

if ($action == "print_report_button_setting") {
	$print_report_format=return_field_value("format_id","lib_report_template","template_name =$data and module_id=7 and report_id=58 and is_deleted=0 and status_active=1");
	echo "print_report_button_setting('$print_report_format');\n";
	exit();
}



if ($action == "machineNo_popup") {
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$cbo_company_id = str_replace("'", "", $cbo_company_id);
	$recipe_id = str_replace("'", "", $txt_recipe_id);
	?>
	<script>
		function js_set_value(data) {
			var data = data.split("_");
			$("#hidden_machine_id").val(data[0]);
			$("#hidden_machine_name").val(data[1]);
			parent.emailwindow.hide();
		}
	</script>

	<input type="hidden" id="hidden_machine_id" name="hidden_machine_id">
	<input type="hidden" id="hidden_machine_name" name="hidden_machine_name">

	<?
	  $sql_fin_recipe=sql_select("select entry_form as ENTRYFORM from pro_recipe_entry_mst where id in($recipe_id) and status_active=1 and entry_form in(445,468)");
	//  echo "select entry_form as ENTRYFORM from pro_recipe_entry_mst where id in($recipe_id) and status_active=1";
	  $fin_from_page=$sql_fin_recipe[0]['ENTRYFORM'];
	 // echo  $fin_from_page.'S';445,468
	 if($fin_from_page==445 || $fin_from_page==468) //for Fin recipe --M/C
	 {
		$fin_categoryCond=" and CATEGORY_ID=4";
	 }
	 else $fin_categoryCond="and CATEGORY_ID=2";
	$location_name = return_library_array("select location_name,id from  lib_location where is_deleted=0", "id", "location_name");
	$floor = return_library_array("select floor_name,id from lib_prod_floor where is_deleted=0", "id", "floor_name");
	$arr = array(0 => $location_name, 1 => $floor);
	//and company_id='$cbo_company_id'
	   $sql = "select location_id,floor_id,machine_no,machine_group,dia_width,gauge,id from lib_machine_name where company_id=$cbo_company_id and is_deleted=0 and status_active=1    $fin_categoryCond order by seq_no";
	echo create_list_view("list_view", "Location Name,Floor Name,Machine No,Machine Group,Dia Width,Gauge", "150,140,100,120,80", "740", "250", 1, $sql, "js_set_value", "id,machine_no", "", 1, "location_id,floor_id,0,0,0,0", $arr, "location_id,floor_id,machine_no,machine_group,dia_width,gauge", "", 'setFilterGrid("list_view",-1);', '');

	exit();
}

if ($action == "mrr_popup") {
	echo load_html_head_contents("Requisition Popup Info", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(data) {
			$("#hidden_sys_id").val(data);
			parent.emailwindow.hide();
		}

        /*$(document).ready(function(e) {
         setFilterGrid('tbl_list_search',-1);
     });*/
 	</script>

	</head>
	<body>
		<div align="center" style="width:860px;">
			<form name="searchfrm" id="searchfrm">
				<fieldset style="width:855px;">
					<table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table">
						<thead>
                            <tr>
                                <th colspan="4"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
                            </tr>
                            <tr>
                                <th>Requisition Date Range</th>
                                <th>Search By</th>
                                <th width="250" id="search_by_td_up">Enter Requisition No</th>
                                <th>
                                    <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;"
                                    class="formbutton"/>
                                    <input type="hidden" name="hidden_sys_id" id="hidden_sys_id" class="text_boxes" value="">
                                </th>
                            </tr>
						</thead>
						<tr class="general">
							<td>
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
								style="width:70px;">To<input type="text" name="txt_date_to" id="txt_date_to"
								class="datepicker" style="width:70px;">
							</td>
							<td>
								<?
								$search_by_arr = array(1 => "Requisition No", 2 => "Recipe No", 3 => "Batch No");
								$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td id="search_by_td">
								<input type="text" style="width:130px;" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							<td>
								<input type="button" name="button2" class="formbutton" value="Show"
								onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $company; ?>'+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_year_selection').value, 'create_requisition_search_list_view', 'search_div', 'chemical_dyes_issue_requisition_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
								style="width:100px;"/>
							</td>
						</tr>
						<tr>
							<td colspan="5" align="center" height="40"
							valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</table>
					<div style="width:100%; margin-top:5px; margin-left:3px;" id="search_div" align="left"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "create_requisition_search_list_view")
{
	$data = explode("_", $data);
	$search_string = trim($data[0]);
	$search_by = $data[1];
	$start_date = trim($data[2]);
	$end_date = trim($data[3]);
	$company = $data[4];
	$search_type= $data[5];
	$year_id= $data[6];
	//echo $year_id;

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and a.requisition_date between '" . change_date_format($start_date, "yyyy-mm-dd", "-") . "' and '" . change_date_format($end_date, "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and a.requisition_date between '" . change_date_format($start_date, "yyyy-mm-dd", "-", 1) . "' and '" . change_date_format($end_date, "yyyy-mm-dd", "-", 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	if ($search_string != "")
	{
        if($search_type == 1)
		{
            if ($search_by == 1) {
                $search_field_cond = "and a.requ_prefix_num='$search_string'";
            }elseif ($search_by == 2) {
                if ($db_type == 0) $search_field_cond = " and FIND_IN_SET($search_string,recipe_id)";
                else if ($db_type == 2) $search_field_cond = " and ',' || a.recipe_id || ',' LIKE '%$search_string%'";
            }elseif ($search_by == 3) {
                $sql_batch=sql_select(" select id from pro_batch_create_mst where batch_no='$search_string' and (working_company_id=$company or company_id=$company) and status_active=1 and is_deleted=0");
                if (count($sql_batch) < 1)
                {echo '<span style="color:red; font-weight:bold">No Batch Found</span>';die;}
                foreach ($sql_batch as  $value) {
                    $batch_ids_arr[$value[csf('id')]]=$value[csf('id')];
                }
                if(!empty($batch_ids_arr))
                {
                    $batch_sl=0;//$db_type=0; I would like to know, who wrote this hard code 'db_type=0' ? //Aziz
                    if ($db_type == 0)
                    {
                        foreach ($batch_ids_arr as  $s_batch_id) {

                            $batch_sl++;
                            if($batch_sl==1) $search_field_cond = " and ( FIND_IN_SET($s_batch_id,batch_id) ";
                            else $search_field_cond .= " or FIND_IN_SET($s_batch_id,batch_id) ";
                        }
                    }
                    // $search_field_cond = " and FIND_IN_SET($batch_ids,batch_id)";
                    else if ($db_type == 2)
                    {
                        foreach ($batch_ids_arr as  $s_batch_id) {
                            $batch_sl++;
                            if($batch_sl==1) $search_field_cond = " and ( ',' || a.batch_id || ',' LIKE '%$s_batch_id%'";
                            else $search_field_cond .= " or ',' || a.batch_id || ',' LIKE '%$s_batch_id%'";
                        }
                    }
                    $search_field_cond.=")";
                }
            }
        }
		elseif($search_type == 2)
		{
            if ($search_by == 1) {
                $search_field_cond = "and requ_prefix_num LIKE '$search_string%'";
            }elseif ($search_by == 2) {
                if ($db_type == 0) $search_field_cond = " and FIND_IN_SET($search_string,recipe_id)";
                else if ($db_type == 2) $search_field_cond = " and ',' || a.recipe_id || ',' LIKE '%$search_string%'";
            }elseif ($search_by == 3) {
                $sql_batch=sql_select(" select id from pro_batch_create_mst where batch_no like '$search_string%' and (working_company_id=$company or company_id=$company) and status_active=1 and is_deleted=0");
                if (count($sql_batch) < 1)
                {echo '<span style="color:red; font-weight:bold">No Batch Found</span>';die;}
                foreach ($sql_batch as  $value) {
                    $batch_ids_arr[$value[csf('id')]]=$value[csf('id')];
                }
                if(!empty($batch_ids_arr))
                {
                    $batch_sl=0;//$db_type=0; I would like to know, who wrote this hard code 'db_type=0' ? //Aziz
                    if ($db_type == 0)
                    {
                        foreach ($batch_ids_arr as  $s_batch_id) {
                            $batch_sl++;
                            if($batch_sl==1) $search_field_cond = " and ( FIND_IN_SET($s_batch_id,batch_id) ";
                            else $search_field_cond .= " or FIND_IN_SET($s_batch_id,batch_id) ";
                        }
                    }
                    // $search_field_cond = " and FIND_IN_SET($batch_ids,batch_id)";
                    else if ($db_type == 2)
                    {

                        foreach ($batch_ids_arr as  $s_batch_id) {
                            $batch_sl++;
                            if($batch_sl==1) $search_field_cond = " and ( ',' || a.batch_id || ',' LIKE '%$s_batch_id%'";
                            else $search_field_cond .= " or ',' || a.batch_id || ',' LIKE '%$s_batch_id%'";
                        }
                    }
                    $search_field_cond.=")";
                }
            }
        }
		elseif($search_type == 3)
		{
            if ($search_by == 1) {
                $search_field_cond = "and requ_prefix_num LIKE '%$search_string'";
            }elseif ($search_by == 2) {
                if ($db_type == 0) $search_field_cond = " and FIND_IN_SET($search_string,recipe_id)";
                else if ($db_type == 2) $search_field_cond = " and ',' || a.recipe_id || ',' LIKE '%$search_string%'";
            }elseif ($search_by == 3) {
                $sql_batch=sql_select(" select id from pro_batch_create_mst where batch_no like '%$search_string' and (working_company_id=$company or company_id=$company) and status_active=1 and is_deleted=0");
                if (count($sql_batch) < 1)
                {echo '<span style="color:red; font-weight:bold">No Batch Found</span>';die;}
                foreach ($sql_batch as  $value) {
                    $batch_ids_arr[$value[csf('id')]]=$value[csf('id')];
                }
                if(!empty($batch_ids_arr))
                {
                    $batch_sl=0;//$db_type=0; I would like to know, who wrote this hard code 'db_type=0' ? //Aziz
                    if ($db_type == 0)
                    {
                        foreach ($batch_ids_arr as  $s_batch_id) {
                            $batch_sl++;
                            if($batch_sl==1) $search_field_cond = " and ( FIND_IN_SET($s_batch_id,batch_id) ";
                            else $search_field_cond .= " or FIND_IN_SET($s_batch_id,batch_id) ";
                        }
                    }
                    // $search_field_cond = " and FIND_IN_SET($batch_ids,batch_id)";
                    else if ($db_type == 2)
                    {

                        foreach ($batch_ids_arr as  $s_batch_id) {
                            $batch_sl++;
                            if($batch_sl==1) $search_field_cond = " and ( ',' || a.batch_id || ',' LIKE '%$s_batch_id%'";
                            else $search_field_cond .= " or ',' || a.batch_id || ',' LIKE '%$s_batch_id%'";
                        }
                    }
                    $search_field_cond.=")";
                }
            }
        }
		else
		{
            if ($search_by == 1) {
                $search_field_cond = "and requ_prefix_num LIKE '%$search_string%'";
            }elseif ($search_by == 2) {
                if ($db_type == 0) $search_field_cond = " and FIND_IN_SET($search_string,recipe_id)";
                else if ($db_type == 2) $search_field_cond = " and ',' || a.recipe_id || ',' LIKE '%$search_string%'";
            }elseif ($search_by == 3) {
                $sql_batch=sql_select(" select id from pro_batch_create_mst where batch_no like '%$search_string%' and (working_company_id=$company or company_id=$company) and status_active=1 and is_deleted=0");
                if (count($sql_batch) < 1)
                {echo '<span style="color:red; font-weight:bold">No Batch Found</span>';die;}
                foreach ($sql_batch as  $value) {
                    $batch_ids_arr[$value[csf('id')]]=$value[csf('id')];
                }
                if(!empty($batch_ids_arr))
                {
                    $batch_sl=0;//$db_type=0; I would like to know, who wrote this hard code 'db_type=0' ? //Aziz
                    if ($db_type == 0)
                    {
                        foreach ($batch_ids_arr as  $s_batch_id) {
                            $batch_sl++;
                            if($batch_sl==1) $search_field_cond = " and ( FIND_IN_SET($s_batch_id,batch_id) ";
                            else $search_field_cond .= " or FIND_IN_SET($s_batch_id,batch_id) ";
                        }
                    }
                    // $search_field_cond = " and FIND_IN_SET($batch_ids,batch_id)";
                    else if ($db_type == 2)
                    {

                        foreach ($batch_ids_arr as  $s_batch_id) {
                            $batch_sl++;
                            if($batch_sl==1) $search_field_cond = " and ( ',' || a.batch_id || ',' LIKE '%$s_batch_id%'";
                            else $search_field_cond .= " or ',' || a.batch_id || ',' LIKE '%$s_batch_id%'";
                        }
                    }
                    $search_field_cond.=")";
                }
            }
        }
	}else{
		$search_field_cond = "";
	}


	$company_arr = return_library_array("select id,company_name from lib_company", "id", "company_name");

	if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year";
	else $year_field = "";//defined Later
	if($year_id!="") $year_cond=" and to_char(a.insert_date,'YYYY')=$year_id"; else $year_cond="";
	//echo $year_id.'='.$year_cond.'DDDD';

	//$sql = "select id, requ_no, requ_prefix_num, $year_field, company_id, requisition_date, batch_id, recipe_id, method from dyes_chem_issue_requ_mst where company_id=$company and requisition_basis=8 and entry_form=156 and status_active=1 $date_cond $search_field_cond order by id DESC";
    
    $sql = "SELECT a.id, a.requ_no, a.requ_prefix_num, $year_field, a.company_id, a.requisition_date, a.batch_id, a.recipe_id, a.method, c.entry_form, c.dyeing_re_process
    from dyes_chem_issue_requ_mst a, DYES_CHEM_ISSUE_REQU_DTLS_CHILD b, pro_recipe_entry_mst c
    where a.ID=b.MST_ID and b.RECIPE_ID=c.ID and a.company_id=$company and a.requisition_basis=8 and a.entry_form=156 and a.status_active=1 and b.status_active=1 $date_cond $search_field_cond $year_cond
	group by a.id, a.requ_no, a.requ_prefix_num, a.insert_date, a.company_id, a.requisition_date, a.batch_id, a.recipe_id, a.method, c.entry_form, c.dyeing_re_process
	order by a.id DESC";
	// echo $sql;die;
	$result = sql_select($sql);
	$all_batch_id_arr=array();
	foreach ($result as $row)
	{
		$batch_id_arr=explode(",",$row[csf("batch_id")]);
		foreach($batch_id_arr as $b_id)
		{
			$all_batch_id_arr[$b_id]=$b_id;
		}
	}
	//echo "<pre>";print_r($all_batch_id_arr);die;
	$con = connect();
	$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (156)");
	oci_commit($con);
	if(count($all_batch_id_arr)>0) fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 156, 1, $all_batch_id_arr, $empty_arr);
	//echo "test2";die;
	$batch_arr = return_library_array("select a.id, a.batch_no from pro_batch_create_mst a, GBL_TEMP_ENGINE b where a.id=b.REF_VAL and b.ENTRY_FORM=156 and b.USER_ID=$user_id and b.REF_FROM=1 and a.batch_against<>0 ", "id", "batch_no");
	$batch_ar = array();
	$batchData = sql_select("select A.ID, A.BATCH_NO, A.BOOKING_WITHOUT_ORDER, A.EXTENTION_NO, A.ENTRY_FORM from pro_batch_create_mst a, GBL_TEMP_ENGINE b where a.id=b.REF_VAL and b.ENTRY_FORM=156 and b.USER_ID=$user_id and b.REF_FROM=1");
	foreach ($batchData as $batchRow) {
		$batch_ar[$batchRow['ID']]['ex'] = $batchRow['EXTENTION_NO'];
	}
	$po_arr = sql_select("select b.id, c.mst_id as batch_id, b.file_no, b.grouping
	from wo_po_break_down b, wo_po_details_master a, pro_batch_create_dtls c, GBL_TEMP_ENGINE d
	where a.id=b.job_id and b.id=c.po_id and c.mst_id=d.REF_VAL and d.ENTRY_FORM=156 and d.USER_ID=$user_id and d.REF_FROM=1 and a.company_name=$company and b.status_active=1 and b.is_deleted=0");
	$file_ref_array = array();
	$po_array = array();
	foreach ($po_arr as $row) {
		$file_ref_array[$row[csf('id')]]['ref'] = $row[csf('grouping')];
		$file_ref_array[$row[csf('id')]]['file'] = $row[csf('file_no')];
		$po_array[$row[csf('batch_id')]]['po'] = $row[csf('id')];
	}
	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (156)");
	oci_commit($con);
	disconnect($con);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="980" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="120">Company</th>
			<th width="100">Batch No</th>
			<th width="40">Ext No</th>
            <th width="100">Recipe No</th>
			<th width="80">Recipe Type</th>
			<th width="90">Requisition No</th>
			<th width="40">Year</th>
			<th width="90">Requisition Date</th>
			<th width="130">Method</th>
			<th width="70">File No</th>
			<th width="80">Ref. No</th>
		</thead>
	</table>
	<div style="width:1000px; max-height:250px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="980" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		foreach ($result as $row)
		{
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

			$batch_no = '';
			$po_id_data = '';
			$extention_no = '';
			$batch_id = explode(",", $row[csf('batch_id')]);
			foreach ($batch_id as $val) {
				if ($batch_no == "") $batch_no = $batch_arr[$val]; else $batch_no .= ", " . $batch_arr[$val];
				if ($po_id_data == "") $po_id_data = $po_array[$val]['po']; else $po_id_data .= "," . $po_array[$val]['po'];
				if ($extention_no == "") $extention_no = $batch_ar[$val]['ex']; else $extention_no .= "," . $batch_ar[$val]['ex'];
			}
			$po_ids = array_unique(explode(",", $po_id_data));
			$file_no = '';
			$ref_no = '';
			foreach ($po_ids as $pid) {
				//echo $pid;
				//if($file_no=="") $file_no=$file_ref_array[$pid]['file']; else $file_no.=",".$file_ref_array[$pid]['file'];
				//if($ref_no=="") $ref_no=$file_ref_array[$pid]['ref']; else $ref_no.=",".$file_ref_array[$pid]['ref'];

				if ($file_no == "") $file_no = $file_ref_array[$pid]['file']; else $file_no .= "," . $file_ref_array[$pid]['file'];
				if ($ref_no == "") $ref_no = $file_ref_array[$pid]['ref']; else $ref_no .= "," . $file_ref_array[$pid]['ref'];
			}
			//echo $po_id_data.'dddddd';

            if ($row[csf('entry_form')]==59) {
                $recipe_type='Dyeing Recipe';
            }
            elseif ($row[csf('entry_form')]==445) {
                $recipe_type='Finishing Recipe';
            }
            else {
                if ($row[csf('dyeing_re_process')]==1) {
                    $recipe_type='Topping';
                }
                elseif ($row[csf('dyeing_re_process')]==2) {
                    $recipe_type='Adding';
                }
                else{
                    $recipe_type='Stripping';
                }                
            }
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="js_set_value(<? echo $row[csf('id')]; ?>);">
				<td width="40"><? echo $i; ?></td>
				<td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
				<td width="100"><p><? echo $batch_no; ?></p></td>
				<td width="40"><p><? echo $extention_no; ?></p></td>
				<td width="100"><p><? echo $row[csf('recipe_id')]; ?></p></td>
				<td width="80"><p><? echo $recipe_type; ?></p></td>
                <td width="90"><p><? echo $row[csf('requ_prefix_num')]; ?></p></td>
				<td width="40" align="center"><p><? echo $row[csf('year')]; ?></p></td>
				<td width="90" align="center"><p><? echo change_date_format($row[csf('requisition_date')]); ?></p>
				</td>
				<td width="130"><p><? echo $dyeing_method[$row[csf('method')]]; ?>&nbsp;</p></td>
				<td width="70"><p><? echo $file_no; ?></p></td>
				<td width="80"><p><? echo $ref_no; ?></p></td>

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

if ($action == "populate_data_from_data") {
	$user_arr = return_library_array("select id, user_full_name from user_passwd", 'id', 'user_full_name');
	$sql = sql_select("select id, requ_no, company_id, location_id, requisition_date, requisition_basis, recipe_id, method, machine_id, is_apply_last_update, store_id from dyes_chem_issue_requ_mst where id=$data");
	foreach ($sql as $row) {
		echo "document.getElementById('txt_mrr_no').value = '" . $row[csf("requ_no")] . "';\n";
		echo "document.getElementById('update_id').value = '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('cbo_company_name').value = '" . $row[csf("company_id")] . "';\n";
		echo "document.getElementById('cbo_location_name').value = '" . $row[csf("location_id")] . "';\n";
		echo "document.getElementById('txt_requisition_date').value = '" . change_date_format($row[csf("requisition_date")]) . "';\n";
		echo "document.getElementById('cbo_receive_basis').value = '" . $row[csf("requisition_basis")] . "';\n";
		echo "document.getElementById('cbo_method').value = '" . $row[csf("method")] . "';\n";
		echo "document.getElementById('machine_id').value = '" . $row[csf("machine_id")] . "';\n";
		echo "document.getElementById('cbo_store_name').value = '" . $row[csf("store_id")] . "';\n";

		$machine_name = "";
		if ($row[csf("machine_id")] > 0) {
			$machine_name = return_field_value("machine_no", "lib_machine_name", "id=" . $row[csf('machine_id')]);
		}
		echo "document.getElementById('txt_machine_no').value = '" . $machine_name . "';\n";

		if ($row[csf("is_apply_last_update")] == 2) {
			$s = 0;
			$msg = "";
			$recipe_data = sql_select("select a.is_apply_last_update, b.id, b.updated_by, b.update_date from dyes_chem_requ_recipe_att a, pro_recipe_entry_mst b where a.recipe_id=b.id and a.mst_id=" . $row[csf("id")] . "");
			foreach ($recipe_data as $recpRow) {
				if ($recpRow[csf("is_apply_last_update")] == 2) {
					$s++;
					$user_name = $user_arr[$recpRow[csf("updated_by")]];
					$update_dateTime = date("H:s:i d-M-Y", strtotime($recpRow[csf("update_date")]));
					if ($msg == "")
						$msg = "Recipe No- " . $recpRow[csf("id")] . " by " . $user_name . " on " . $update_dateTime;
					else
						$msg .= ", Recipe No- " . $recpRow[csf("id")] . " by " . $user_name . " on " . $update_dateTime;
				}
			}
			if ($s <= 1) {
				echo "document.getElementById('last_update_message').innerHTML 	= 'After Requisition Recipe has been changed by $user_name on $update_dateTime To Revise Requisition Click Apply Last Update Button and Update.';\n";
			} else {
				echo "document.getElementById('last_update_message').innerHTML 	= 'After Requisition Recipe has been changed " . $msg . " To Revise Requisition Click Apply Last Update Button and Update.';\n";
			}
		} else {
			echo "document.getElementById('last_update_message').innerHTML 		= '';\n";
		}

		echo "get_php_form_data('" . $row[csf("recipe_id")] . "', 'populate_data_from_recipe_popup', 'requires/chemical_dyes_issue_requisition_controller' );\n";

		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_chemical_dyes_issue_requisition',1);\n";
		exit();
	}
}

if ($action == "labdip_popup") {
	echo load_html_head_contents("Labdip No Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>

		var selected_id = new Array();
		var prevsubprocess_id = '';
		var prevseq_no = '';
		var prevbooking_type = '';
		var preventry_form = '';
		var prevstore_id = '';

        /*function check_all_data()
         {
         var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;

         tbl_row_count = tbl_row_count-1;
         for( var i = 1; i <= tbl_row_count; i++ ) {
         js_set_value( i );
         }
     }*/

     function toggle(x, origColor) {
     	var newColor = 'yellow';
     	if (x.style) {
     		x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
     	}
     }

     function set_all() {
     	var old = document.getElementById('txt_recipe_row_id').value;
     	if (old != "") {
     		old = old.split(",");
     		for (var k = 0; k < old.length; k++) {
     			js_set_value(old[k])
     		}
     	}
     }

     function js_set_value(str) {
     	var currsubprocess_id = $('#subprocess_id' + str).val();
     	var currseq_no = $('#seq_no' + str).val();
     	var booking_type = $('#booking_type' + str).val();
     	var entry_form = $('#entry_form' + str).val();
		var store_id = $('#store_id' + str).val();
		var approval_need_chk = $('#approval_need_chk' + str).val();
		//alert(approval_need_chk);
		//alert(prevsubprocess_id+'='+selected_id.length);
		if(approval_need_chk==1)
		{
			alert('Please Approved First');
			return;
		}
     	if (prevsubprocess_id == '' || selected_id.length == 0)
		{
     		prevsubprocess_id = $('#subprocess_id' + str).val();
     		prevseq_no = $('#seq_no' + str).val();
     		prevbooking_type = $('#booking_type' + str).val();
     		preventry_form = $('#entry_form' + str).val();
			prevstore_id = $('#store_id' + str).val();
     	}
     	else
		{

				//alert(currsubprocess_id+'='+prevsubprocess_id+','+currseq_no+'='+prevseq_no+','+entry_form+'='+preventry_form+','+store_id+'='+prevstore_id);
				//|| booking_type == prevbooking_type
				//alert(currseq_no+'='+prevseq_no);
				var items="\n Following Items: \n 1. Process \n 2. Sequence \n 3. Batch Entry Form\n 4. Store";
				if(currsubprocess_id != prevsubprocess_id || currseq_no != prevseq_no || entry_form != preventry_form || store_id != prevstore_id)
				{
     			alert("Item and Sub Process of Selected Recipe Not Uniformed. And With Order and Without Order Mix Not Allowed"+items);
     			return;
     			}
     	}

     	toggle(document.getElementById('search' + str), '#FFFFCC');

     	if (jQuery.inArray($('#recipe_id' + str).val(), selected_id) == -1) {
     		selected_id.push($('#recipe_id' + str).val());
     	}
     	else {
     		for (var i = 0; i < selected_id.length; i++) {
     			if (selected_id[i] == $('#recipe_id' + str).val()) break;
     		}
     		selected_id.splice(i, 1);
     	}

     	var id = '';
     	for (var i = 0; i < selected_id.length; i++) {
     		id += selected_id[i] + ',';
     	}

     	id = id.substr(0, id.length - 1);

     	$('#hidden_recipe_id').val(id);
     	$('#hidden_subprocess_id').val(currsubprocess_id);
		//alert(store_id);
		$('#hidden_store_id').val(store_id);

     }
	 function recipe_check(type)
	 {
		if ( document.getElementById('fin_recipe_ckh').checked==true)
		{
			document.getElementById('fin_recipe_ckh').value=1;
			//set_button_status(0, permission, 'fnc_recipe_entry',1,1);
			//alert(chk );
		}
		else if(document.getElementById('fin_recipe_ckh').checked==false)
		{
			document.getElementById('fin_recipe_ckh').value=2;
		}
	 }
 </script>
	</head>

	<body>
		<div align="center" style="width:1035px;">
			<form name="searchlabdipfrm" id="searchlabdipfrm">
				<fieldset style="width:1030px;">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" width="880" border="1" rules="all" class="rpt_table">
						<thead>
                            <tr>
                                <th colspan="5"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
                            </tr>
                            <tr>
                                <th>Recipe Date Range</th>
                                 <th>
                                 Only Finishing Recipe
                                </th>
                                <th>Search By</th>
                                <th width="250" id="search_by_td_up">Enter Recipe System No</th>

                                <th>
                                    <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton"/>
                                    <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                                    <input type="hidden" name="hidden_recipe_id" id="hidden_recipe_id" class="text_boxes" value="">
                                    <input type="hidden" name="hidden_subprocess_id" id="hidden_subprocess_id" class="text_boxes" value="">
                                    <input type="hidden" name="hidden_store_id" id="hidden_store_id" class="text_boxes" value="">
                                </th>
                            </tr>

						</thead>
						<tr class="general">
							<td>
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
								style="width:70px;">To<input type="text" name="txt_date_to" id="txt_date_to"
								class="datepicker" style="width:70px;">
							</td>
                             <td>
                            <input type="checkbox" name="fin_recipe_ckh" id="fin_recipe_ckh" onClick="recipe_check(1)" value="2"  >
                            </td>
							<td>
								<?
								$search_by_arr = array(1 => "Recipe System No", 2 => "Labdip No", 3 => "Batch No");//,3=>"Recipe Description"
								$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td id="search_by_td">
								<input type="text" style="width:130px;" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>

							<td>
								<input type="button" name="button2" class="formbutton" value="Show"
								onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+'<? echo $recipe_id; ?>'+'_'+document.getElementById('fin_recipe_ckh').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_recipe_search_list_view', 'search_div', 'chemical_dyes_issue_requisition_controller', 'set_all();setFilterGrid(\'tbl_list_search\',-1);')"
								style="width:100px;"/>
							</td>
						</tr>
						<tr>
							<td colspan="5" align="center" height="40"
							valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</table>
					<div style="width:100%; margin-top:5px; margin-left:3px;" id="search_div" align="left"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if ($action == "create_recipe_search_list_view") {
	//echo $data;die;
	$con = connect();
	$data = explode("_", $data);
	$search_string = trim(str_replace("'","",$data[0]));
	$search_by = trim(str_replace("'","",$data[1]));
	$start_date = trim(str_replace("'","",$data[2]));
	$end_date = trim(str_replace("'","",$data[3]));
	$company_id = $data[4];
	$recipe_id = trim(str_replace("'","",$data[5]));
	$fin_recipe_check = trim(str_replace("'","",$data[6]));
	$search_type = trim(str_replace("'","",$data[7]));
	//echo $fin_recipe_check.test;die;
	if($start_date=="" && $end_date=="" && $search_string=="")
	{
		echo "Please Select Specific Reference Or Date Range";
		disconnect($con);
		die;
	}
	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and a.recipe_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and a.recipe_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-", 1) . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-", 1) . "'";
		}
	} else {
		$date_cond = "";
	}
	if (trim($search_string) != "") {
        if($search_type == 1){
            if ($search_by == 1)
                $search_field_cond = " and a.id = $search_string";
            else if ($search_by == 2)
                $search_field_cond = " and a.labdip_no = '" . $search_string . "'";
            else if ($search_by == 3)
                $search_field_cond = "and c.batch_no = '" . $search_string . "'";
        }elseif($search_type == 2){
            if ($search_by == 1)
                $search_field_cond = "and a.id like '$search_string%'";
            else if ($search_by == 2)
                $search_field_cond = "and a.labdip_no like '" . $search_string . "%'";
            else if ($search_by == 3)
                $search_field_cond = "and c.batch_no like '" . $search_string . "%'";
        }elseif($search_type == 3){
            if ($search_by == 1)
                $search_field_cond = "and a.id like '%$search_string'";
            else if ($search_by == 2)
                $search_field_cond = "and a.labdip_no like '%" . $search_string . "'";
            else if ($search_by == 3)
                $search_field_cond = "and c.batch_no like '%" . $search_string . "'";
        }else{
            if ($search_by == 1)
                $search_field_cond = "and a.id like '%$search_string%'";
            else if ($search_by == 2)
                $search_field_cond = "and a.labdip_no like '%" . $search_string . "%'";
            else if ($search_by == 3)
                $search_field_cond = "and c.batch_no like '%" . $search_string . "%'";
        }
	} else {
		$search_field_cond = "";
	}
	if($fin_recipe_check==1) //Finishing
	{
		$dyeing_fin_recp_cond="and a.entry_form in(445,468)";
	}
	else $dyeing_fin_recp_cond="";

	$approval_allow=sql_select("select b.id, b.page_id, b.approval_need, b.allow_partial, b.validate_page,a.setup_date from approval_setup_mst a,approval_setup_dtls b
	where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and b.page_id=34 and b.status_active=1 and b.is_deleted=0 order by b.id desc ");
	$approval_need=2;
	if($approval_allow[0][csf("approval_need")]==1)
	{
		$approval_need=$approval_allow[0][csf("approval_need")];
	}

	if ($db_type == 0)
	{
		$sql = "select A.ID,A.ENTRY_FORM,A.APPROVED, A.LABDIP_NO, A.RECIPE_DATE, A.ORDER_SOURCE, A.STYLE_OR_ORDER, A.BATCH_ID, A.COLOR_ID, A.COLOR_RANGE, sum(b.total_liquor) as TOTAL_LIQUOR, group_concat(b.sub_process_id order by b.id) as SUB_PROCESS_ID, group_concat(concat_ws('**',b.sub_process_id,b.prod_id,b.seq_no) order by b.id) as SEQ_NO, max(b.store_id) as STORE_ID
		from pro_recipe_entry_mst a, pro_recipe_entry_dtls b, pro_batch_create_mst c
		where a.id=b.mst_id and a.entry_form <> 151 and a.batch_id=c.id and a.working_company_id='$company_id' and b.ratio>0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $date_cond $dyeing_fin_recp_cond
		group by A.ID";
	}
	else
	{
		$sql = "select A.ID, A.ENTRY_FORM, A.APPROVED, A.LABDIP_NO, A.RECIPE_DATE, A.ORDER_SOURCE, A.STYLE_OR_ORDER, A.BATCH_ID, A.COLOR_ID, A.COLOR_RANGE, b.id as DTLS_ID, b.total_liquor as TOTAL_LIQUOR, b.PROD_ID as PROD_ID, b.SUB_SEQ,b.sub_process_id as SUB_PROCESS_ID, b.sub_process_id || '**' || b.prod_id || '**' || b.seq_no as SEQ_NO, b.store_id as STORE_ID, b.RATIO
		from pro_recipe_entry_mst a, pro_recipe_entry_dtls b, pro_batch_create_mst c
		where a.id=b.mst_id and a.entry_form <> 151 and a.batch_id=c.id and a.working_company_id='$company_id' and b.RATIO>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $date_cond $dyeing_fin_recp_cond order by a.id ,b.SUB_SEQ,b.prod_id,b.seq_no asc";
	}
	//echo $sql; die;
	$nameArray = sql_select($sql);
	if(count($nameArray)<1) {echo "No Data Found";die;}
	$receipe_id_arr=array();$batch_ids_arr=array();$all_pord_ids_arr=array();$recepi_data=array();
	foreach($nameArray as $row)
	{
		$receipe_id_arr[$row["ID"]]=$row["ID"];
		$batch_ids_arr[$row["BATCH_ID"]]=$row["BATCH_ID"];

		$recepi_data[$row["ID"]]["ID"]=$row["ID"];
		$recepi_data[$row["ID"]]["ENTRY_FORM"]=$row["ENTRY_FORM"];
		$recepi_data[$row["ID"]]["APPROVED"]=$row["APPROVED"];
		$recepi_data[$row["ID"]]["RECIPE_DATE"]=$row["RECIPE_DATE"];
		$recepi_data[$row["ID"]]["ORDER_SOURCE"]=$row["ORDER_SOURCE"];
		$recepi_data[$row["ID"]]["STYLE_OR_ORDER"]=$row["STYLE_OR_ORDER"];
		$recepi_data[$row["ID"]]["COLOR_ID"]=$row["COLOR_ID"];
		$recepi_data[$row["ID"]]["COLOR_RANGE"]=$row["COLOR_RANGE"];
		$recepi_data[$row["ID"]]["STORE_ID"]=$row["STORE_ID"];
		$recepi_data[$row["ID"]]["BATCH_ID"]=$row["BATCH_ID"];
		if($dtls_id_ceck[$row["DTLS_ID"]]=="")
		{
			$dtls_id_ceck[$row["DTLS_ID"]]=$row["DTLS_ID"];
			$recepi_data[$row["ID"]]["TOTAL_LIQUOR"]+=$row["TOTAL_LIQUOR"];
			$recepi_data[$row["ID"]]["PROD_ID"].=$row["PROD_ID"].",";
			$recepi_data[$row["ID"]]["SUB_PROCESS_ID"].=$row["SUB_PROCESS_ID"].",";
			$recepi_data[$row["ID"]]["SEQ_NO"].=$row["SEQ_NO"].",";
			$all_pord_ids_arr[$row["PROD_ID"]]=$row["PROD_ID"];
		}
	}
	unset($nameArray);
	//echo "<pre>";print_r($batch_ids_arr);die;
	$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (156)");
	oci_commit($con);
	if(count($batch_ids_arr)>0) fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 156, 1, $batch_ids_arr, $empty_arr);
	if(count($prod_id_arr)>0) fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 156, 2, $prod_id_arr, $empty_arr);
	if(count($receipe_id_arr)>0) fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 156, 3, $receipe_id_arr, $empty_arr);
	$batch_arr = array();
	$batchData = sql_select("select A.ID, A.BATCH_NO, A.BOOKING_WITHOUT_ORDER, A.EXTENTION_NO, A.ENTRY_FORM from pro_batch_create_mst a, GBL_TEMP_ENGINE b where a.id=b.REF_VAL and b.ENTRY_FORM=156 and b.USER_ID=$user_id and b.REF_FROM=1");
	foreach ($batchData as $batchRow) {
		if ($batchRow['ENTRY_FORM'] == 36) $entry_form = 0; else $entry_form = $batchRow['ENTRY_FORM'];
		$batch_arr[$batchRow['ID']]['no'] = $batchRow['BATCH_NO'];
		$batch_arr[$batchRow['ID']]['bwo'] = $batchRow['BOOKING_WITHOUT_ORDER'];
		$batch_arr[$batchRow['ID']]['ex'] = $batchRow['EXTENTION_NO'];
		$batch_arr[$batchRow['ID']]['ef'] = $entry_form;
	}
	$po_arr = sql_select("select B.ID, C.MST_ID AS BATCH_ID, B.FILE_NO, B.GROUPING
	from wo_po_break_down b, wo_po_details_master a, pro_batch_create_dtls c, GBL_TEMP_ENGINE d
	where a.id=b.job_id and b.id=c.po_id and c.mst_id=d.REF_VAL and d.ENTRY_FORM=156 and d.USER_ID=$user_id and d.REF_FROM=1 and a.company_name=$company_id and b.status_active=1 and b.is_deleted=0");
	$file_ref_array = array();
	$po_array = array();
	foreach ($po_arr as $row) {
		$file_ref_array[$row['ID']]['ref'] = $row['GROUPING'];
		$file_ref_array[$row['ID']]['file'] = $row['FILE_NO'];
		$po_array[$row['BATCH_ID']]['po'] .= $row['ID'] . ",";
	}

	//echo "select b.RECIPE_ID from dyes_chem_issue_requ_mst a, dyes_chem_issue_requ_dtls b, GBL_TEMP_ENGINE c
//	where a.id=b.mst_id and b.RECIPE_ID in(select cast(c.REF_VAL as varchar(4000)) as REF_VAL from GBL_TEMP_ENGINE c where c.ENTRY_FORM=156 and c.USER_ID=1 and c.REF_FROM=3 ) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.entry_form=156";die;
	//$sql_issue_req = sql_select("select b.RECIPE_ID from dyes_chem_issue_requ_mst a, dyes_chem_issue_requ_dtls b, GBL_TEMP_ENGINE c
//	where a.id=b.mst_id and b.PRODUCT_ID=c.REF_VAL and c.ENTRY_FORM=156 and c.USER_ID=$user_id and c.REF_FROM=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.entry_form=156");
	// $sql_issue_req = sql_select("select a.RECIPE_ID from DYES_CHEM_ISSUE_REQU_DTLS_CHILD a, GBL_TEMP_ENGINE b
	// where a.RECIPE_ID=b.REF_VAL and b.ENTRY_FORM=156 and b.USER_ID=$user_id and b.REF_FROM=3 and a.status_active=1 and a.is_deleted=0");
	$sql_issue_req = sql_select("select a.RECIPE_ID from DYES_CHEM_ISSUE_REQU_DTLS_CHILD a,DYES_CHEM_ISSUE_REQU_MST c, GBL_TEMP_ENGINE b
	where a.mst_id=c.id and a.RECIPE_ID=b.REF_VAL and b.ENTRY_FORM=156 and b.USER_ID=$user_id and b.REF_FROM=3 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0");

	$issue_req_arr = array();
	foreach ($sql_issue_req as $row) {
		$issue_req_arr[$row['RECIPE_ID']]= $row['RECIPE_ID'];
	}

	$color_arr = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (156)");
	oci_commit($con);
	disconnect($con);
	//echo $sql;
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1090" class="rpt_table">
		<thead>
			<th width="35">SL</th>
			<th width="60">Recipe No</th>
            <th width="60">Recipe From</th>
			<th width="60">Labdip No</th>
			<th width="70">Recipe Date</th>
			<th width="160">Sub Process</th>
			<th width="70">File No</th>
			<th width="80">Ref. No</th>
			<th width="80">Batch No</th>
			<th width="50">Ext. No</th>
			<th width="100">Booking No</th>
			<th width="75">Color</th>
			<th width="75">Color Range</th>
			<th>Total Liquor</th>
		</thead>
	</table>
	<div style="width:1090px; overflow-y:scroll; max-height:220px;" id="buyer_list_view" align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1110" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		$recipe_row_id = '';
		$hidden_recipe_id = explode(",", $recipe_id);

		foreach ($recepi_data as $selectResult)
		{
			$recipe_ids=$issue_req_arr[$selectResult['ID']];

			if($issue_req_arr[$selectResult['ID']]=='')
			{
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				$subprocess = '';
				$sub_process_id = array_unique(explode(",", chop($selectResult['SUB_PROCESS_ID'],",")));
				foreach ($sub_process_id as $process_id) {
					$subprocess .= $dyeing_sub_process[$process_id] . ",";
				}
				$po_id = array_unique(explode(",", $po_array[$selectResult['BATCH_ID']]['po']));
				$file_no = '';
				$ref_no = '';
				foreach ($po_id as $pid) {
					if ($file_no == '') $file_no = $file_ref_array[$pid]['file']; else $file_no .= "," . $file_ref_array[$pid]['file'];
					if ($ref_no == '') $ref_no = $file_ref_array[$pid]['ref']; else $ref_no .= "," . $file_ref_array[$pid]['ref'];
				}

				//echo $approval_need.'='.$selectResult['ENTRY_FORM'].',';
				if($approval_need==1 && $selectResult['APPROVED']!=1 && $selectResult['ENTRY_FORM']==60)
				{
					$approval_need_chk=$approval_need;
				}
				else
				{
					$approval_need_chk=2;
				}
				//echo $msg;
					//echo $subprocess;
					//print_r($po_id);
					//if($file_no==0 || $file_no=='') $file_no='';else $file_no=$file_no; if($ref_no==0) $ref_no='';else $ref_no=$ref_no;
					//$ref_no=$file_ref_array[$po_id]['ref'];
				$seq_no = array_unique(explode(",", chop($selectResult['SEQ_NO'],",")));
				if($selectResult['ENTRY_FORM']==445 || $selectResult['ENTRY_FORM']==468)
				{
					$recipeFrom="Finishing";
				}
				else{
					$recipeFrom="Dyeing";
				}

				if (in_array($selectResult['ID'], $hidden_recipe_id)) {
					if ($recipe_row_id == "") $recipe_row_id = $i; else $recipe_row_id .= "," . $i;
				}

				//$sub_process_id = array_unique(explode(",", $process_id_arr[$selectResult['ID']]));
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
					<td width="35" align="center"><? echo $i; ?>
						<input type="hidden" name="recipe_id" id="recipe_id<? echo $i; ?>" value="<? echo $selectResult['ID']; ?>"/>
						<input type="hidden" name="subprocess_id" id="subprocess_id<? echo $i; ?>" value="<? echo implode(",", $sub_process_id); ?>"/>
						<input type="hidden" name="seq_no" id="seq_no<? echo $i; ?>" value="<? echo implode(",", $seq_no); ?>"/>
						<input type="hidden" name="booking_type" id="booking_type<? echo $i; ?>" value="<? echo $batch_arr[$selectResult['BATCH_ID']]['bwo']; ?>"/>
						<input type="hidden" name="entry_form" id="entry_form<? echo $i; ?>" value="<? echo $batch_arr[$selectResult['BATCH_ID']]['ef']; ?>"/>
						<input type="hidden" name="store_id" id="store_id<? echo $i; ?>" value="<? echo $selectResult['STORE_ID']; ?>"/>
                        <input type="hidden" name="approval_need_chk" id="approval_need_chk<? echo $i; ?>" value="<? echo $approval_need_chk; ?>"/>
					</td>
					<td width="60"><p><? echo $selectResult['ID']; ?></p></td>
                     <td width="60"><p><? echo $recipeFrom; ?></p></td>
					<td width="60"><p><? echo $selectResult['LABDIP_NO']; ?></p></td>

					<td width="70" align="center"><? echo change_date_format($selectResult['RECIPE_DATE']); ?>
						&nbsp;</td>
						<td width="160"><p><? echo substr($subprocess, 0, -1); ?></p></td>
						<td width="70"><p><? echo substr($file_no, 0, -1); ?></p></td>
						<td width="80"><p><? echo substr($ref_no, 0, -1); ?></p></td>
						<td width="80"><p><? echo $batch_arr[$selectResult['BATCH_ID']]['no']; ?>&nbsp;</p></td>
						<td width="50"><p><? echo $batch_arr[$selectResult['BATCH_ID']]['ex']; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $selectResult['STYLE_OR_ORDER']; ?>&nbsp;</p></td>
						<td width="75"><p><? echo $color_arr[$selectResult['COLOR_ID']]; ?>&nbsp;</p></td>
						<td width="75"><p><? echo $color_range[$selectResult['COLOR_RANGE']]; ?>&nbsp;</p></td>
						<td align="right"><? echo number_format($selectResult['TOTAL_LIQUOR'], 2, '.', ''); ?></td>
                </tr>
				<?
                $i++;
            }
		}
		?>
	</table>
	</div>
	<table width="950">
		<tr>
			<td align="center">
				<input type="button" name="close" class="formbutton" value="Close" id="main_close"
				onClick="parent.emailwindow.hide();" style="width:100px"/>
				<input type="hidden" name="txt_recipe_row_id" id="txt_recipe_row_id"
				value="<?php echo $recipe_row_id; ?>"/>
			</td>
		</tr>
	</table>
	<?
	exit();
}

if ($action == "multiple_requisition_popup") {
    echo load_html_head_contents("Multiple Requisition Popup", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    ?>
    <script>

        var selected_id = new Array();

        function toggle(x, origColor) {
            var newColor = 'yellow';
            if (x.style) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
            }
        }

        // function set_all() {
        //     var old = document.getElementById('txt_recipe_row_id').value;
        //     if (old != "") {
        //         old = old.split(",");
        //         for (var k = 0; k < old.length; k++) {
        //             js_set_value(old[k])
        //         }
        //     }
        // }

        function js_set_value(str) {
            var conArr = str.split('_');
            var mst_id = conArr[0];
            var indexPos = conArr[1];
            toggle(document.getElementById('search' + indexPos), '#FFFFCC');
            if (jQuery.inArray(mst_id, selected_id) == -1) {
                selected_id.push(mst_id);
            }
            else {
                for (var i = 0; i < selected_id.length; i++) {
                    if (selected_id[i] == mst_id) break;
                }
                selected_id.splice(i, 1);
            }
            var id = '';
            for (var i = 0; i < selected_id.length; i++) {
                id += selected_id[i] + ',';
            }
            id = id.substr(0, id.length - 1);

            $('#hidden_requ_id').val(id);
        }
    </script>
    </head>

    <body>
    <div align="center" style="width:100%;">
        <form name="searchlabdipfrm" id="searchlabdipfrm">
            <table cellpadding="0" cellspacing="0" width="742" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                        <th width="280">Requisition Date Range</th>
<!--                        <th width="200">Search By</th>-->
                        <th width="280">Enter Requisition No.</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton"/>
                            <input type="hidden"  id="hidden_requ_id" value=""/>
                            <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                        </th>
                    </tr>
                </thead>
                <tr class="general">
                    <td>
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;">To<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;">
                    </td>
<!--                    <td>-->
                        <?
                        //$search_by_arr = array(1 => "Requisition for Dyeing Recipe", 2 => "Requisition for Finishing Recipe");//,3=>"Recipe Description"
                        //echo create_drop_down("cbo_search_by", 200, $search_by_arr, "", 1, "--Select--", "", "", 0);
                        ?>
<!--                    </td>-->
                    <td>
                        <input type="text" style="width:180px;" class="text_boxes" name="txt_search_common" id="txt_search_common"/>
                    </td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show"  onClick="show_list_view ( document.getElementById('txt_search_common').value+'__'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value, 'create_multiple_requisition_list_view', 'search_div', 'chemical_dyes_issue_requisition_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"  style="width:100px;"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
            </table>
            <div style="width:100%; margin-top:5px;" id="search_div"></div>
        </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}

if ($action == "create_multiple_requisition_list_view") {
    $data = explode("_", $data);
    $search_string = trim(str_replace("'","",$data[0]));
    $search_by = trim(str_replace("'","",$data[1]));
    $start_date = trim(str_replace("'","",$data[2]));
    $end_date = trim(str_replace("'","",$data[3]));
    $company_id = $data[4];
    $company_arr = return_library_array("select id,company_name from lib_company", "id", "company_name");

    if ($start_date != "" && $end_date != "") {
        if ($db_type == 0) {
            $date_cond = " and requisition_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-") . "'";
        } else {
            $date_cond = " and requisition_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-", 1) . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-", 1) . "'";
        }
    } else {
        $date_cond = "";
    }
    if ($search_string != "") {
        $search_field_cond = " and requ_prefix_num like '%".$search_string."%'";
    } else {
        $search_field_cond = "";
    }
    $sql = "select id, requ_no, requ_prefix_num, company_id, requisition_date from dyes_chem_issue_requ_mst where company_id=$company_id and requisition_basis=8 and entry_form=156 and status_active=1 $date_cond $search_field_cond order by id DESC";
    //echo $sql; ///die;
    $result = sql_select($sql);
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="460" class="rpt_table" align="center">
        <thead>
            <tr>
                <th width="40">SL</th>
                <th width="170">Company</th>
                <th width="90">Requisition Date</th>
                <th>Requisition No</th>
            </tr>
        </thead>
    </table>
    <div style="width:460px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="center">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="440" class="rpt_table" id="tbl_list_search" align="left">
            <?
            $i = 1;
            foreach ($result as $row) {
                if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                ?>
                <tr id="search<?=$i?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."_".$i; ?>');">
                    <td width="40" align="center"><? echo $i; ?></td>
                    <td width="170"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                    <td width="90" align="center"><p><? echo change_date_format($row[csf('requisition_date')]); ?></p></td>
                    <td ><p><? echo $row[csf('requ_no')]; ?></p></td>
                </tr>
                <?
                $i++;
            }
            ?>
        </table>
    </div>
    <table width="710">
        <tr>
            <td align="center">
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px"/>
            </td>
        </tr>
    </table>
    <?
    exit();
}

if ($action == 'get_subprocess_id') {
	$sub_process_id = '';
	if ($db_type == 0) {
		$sql = "select a.id, group_concat(b.sub_process_id order by b.id) as sub_process_id from pro_recipe_entry_mst a, pro_recipe_entry_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.id in($data) group by a.id";
	} else {
		// $sql = "select a.id, LISTAGG(b.sub_process_id, ',') WITHIN GROUP (ORDER BY b.id) as sub_process_id from pro_recipe_entry_mst a, pro_recipe_entry_dtls b where a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.id in($data) group by a.id";
		$sql = "select a.id, b.sub_process_id as sub_process_id from pro_recipe_entry_mst a, pro_recipe_entry_dtls b where a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.id in($data)";
	}
	$selectResult = sql_select($sql);
	foreach($selectResult as $row)
	{
		$subprocssArr[$row[csf('sub_process_id')]]=$row[csf('sub_process_id')];
	}
	$sub_process_id = implode(",", $subprocssArr);
	echo $sub_process_id;
	exit();
}


if ($action == 'populate_data_from_recipe_popup') {
	/*if($db_type==0)
	{
		$data_array=sql_select("select group_concat(id) as recipe_id, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor from pro_recipe_entry_mst where id in($data)");
	}
	else
	{
		$data_array=sql_select("select listagg(id,',') within group (order by id) as recipe_id, listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor from pro_recipe_entry_mst where id in($data)");
	}*/
	$recipe_id = '';
	$total_liquor = 0;
	$batch_new_qty = 0;
	$batch_id = '';
	$all_batch_id = '';
	$data_array = sql_select("select id, entry_form, batch_id, total_liquor, new_batch_weight,batch_qty from pro_recipe_entry_mst where id in($data)");
	foreach ($data_array as $row) {
		$total_liquor += $row[csf("total_liquor")];
		if ($row[csf("entry_form")] == 60) {
			$batch_new_qty += $row[csf("new_batch_weight")];
		}
		else if ($row[csf("entry_form")] == 59 || $row[csf("entry_form")] == 468 || $row[csf("entry_form")] == 445) //New Add from Recipe page
		{
			$batch_new_qty += $row[csf("batch_qty")];
		}
		else {
			$batch_id .= $row[csf("batch_id")] . ",";
		}
		$all_batch_id .= $row[csf("batch_id")] . ",";
		$recipe_id .= $row[csf("id")] . ",";


	}
	//$batch_id=implode(",",array_unique(explode(",",$data_array[0][csf("batch_id")])));
	$recipe_id = chop($recipe_id, ',');
	$batch_id = chop($batch_id, ',');
	$all_batch_id = chop($all_batch_id, ',');
	if ($batch_id == "") $batch_id = 0;
	if ($db_type == 0) {
		$batchdata_array = sql_select("select group_concat(batch_no) as batch_no, sum(case when id in($batch_id) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($all_batch_id)");
	} else {
		// $batchdata_array = sql_select("select listagg(CAST(batch_no  AS VARCHAR2(4000)),',') within group (order by id) as batch_no, sum(case when id in($batch_id) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($all_batch_id)");
		$batchdata_array = sql_select("SELECT batch_no as batch_no from pro_batch_create_mst where id in($all_batch_id)");
	}
	$batch_weight_sum=0;
	foreach($batchdata_array  as $row)
	{
		if($row[csf("batch_weight")])
		{
			//$batch_weight_sum+=$row[csf("batch_weight")];
		}

	}
	//$batch_weight += $batch_weight_sum;
	echo "document.getElementById('txt_recipe_id').value 				= '" . $recipe_id . "';\n";
	echo "document.getElementById('txt_recipe_no').value 				= '" . $recipe_id . "';\n";
	echo "document.getElementById('txt_batch_no').value 				= '" . $batchdata_array[0][csf("batch_no")] . "';\n";
	echo "document.getElementById('txt_batch_id').value 				= '" . $all_batch_id . "';\n";
	echo "document.getElementById('txt_tot_liquor').value 				= '" . $total_liquor . "';\n";
	//echo "document.getElementById('txt_batch_weight').value 			= '".$batch_weight."';\n";
	echo "document.getElementById('txt_batch_weight').value 			= '" . number_format($batch_new_qty,2,'.','') . "';\n";

	exit();
}

if ($action == "item_details") {

	$data = explode("**", $data);
	$company_id = $data[0];
	$sub_process_id = trim($data[1]);
	$recipe_id = $data[2];
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1350" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="83">Sub Process</th>
			<th width="50">Prod. ID</th>
            <th width="50">Lot</th>
			<th width="80">Item Category</th>
			<th width="90">Group</th>
			<th width="80">Sub Group</th>
			<th width="110">Item Description</th>
			<th width="40">UOM</th>
            <th width="80">Stock</th>
			<th width="50">Seq. No.</th>
			<th width="75">Dose Base</th>
			<th width="72">Ratio</th>
			<th width="80">Recipe Qnty.</th>
			<th width="53">Adj%.</th>
			<th width="87">Adj. Type</th>
			<th width="80">Reqn. Qnty.</th>
			<th>Comment</th>
		</thead>
	</table>
	<div style="width:1350px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1330" class="rpt_table" id="tbl_list_search" align="left">
		<tbody>
			<?
			$item_group_arr = return_library_array("select id, item_name from lib_item_group", "id", "item_name");
			//$batchWeight_arr = return_library_array("select id, batch_weight from pro_batch_create_mst", "id", "batch_weight");

			$sql_comment="select b.sub_process_id,b.prod_id as id ,b.item_lot,b.comments from pro_recipe_entry_dtls b where mst_id in (select c.id from pro_recipe_entry_mst c where c.recipe_id in($recipe_id))";
			//echo $sql_comment;
			$res_comment=sql_select($sql_comment);

			$topping_data=array();
			foreach ($res_comment as $row) {

				if(!empty($row[csf('comments')]))
				{
					$topping_data[$row[csf('sub_process_id')]][$row[csf('id')]][$row[csf('item_lot')]].=$row[csf('comments')]."***";
				}
			}

			$sql_comment="select b.sub_process_id,b.prod_id as id ,b.item_lot,b.comments from pro_recipe_entry_dtls b where mst_id in (select c.id from pro_recipe_entry_mst c where c.copy_from in($recipe_id))";
			//echo $sql_comment;
			$res_comment=sql_select($sql_comment);

			$copy_data=array();
			foreach ($res_comment as $row) {
				if(!empty($row[csf('comments')]))
				{
					$copy_data[$row[csf('sub_process_id')]][$row[csf('id')]][$row[csf('item_lot')]].=$row[csf('comments')]."***";
				}


			}
			//new dev
			$recId = array();
			$getEntryFrom = sql_select("select entry_form from pro_recipe_entry_mst where id in($recipe_id)");
			foreach ($getEntryFrom as $dataEntryF) {
				$recId = $dataEntryF;
			}
			$uniqEntrF = array_unique($recId);
			if($subprocessforwashin=="") $subprocessforwashin=0;
			if (in_array(59, $uniqEntrF)) {
				 $sql = "(select p.total_liquor,p.recipe_type,p.surplus_solution,p.pickup, p.batch_id, p.entry_form,p.batch_qty,c.batch_no, a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.sub_process_id, b.dose_base, b.ratio, b.seq_no,b.sub_seq, b.new_batch_weight, b.new_total_liquor,b.liquor_ratio as liquor_ratio_dtls,b.total_liquor as total_liquor_dtls, b.item_lot, b.store_id ,b.comments
				from pro_recipe_entry_mst p, product_details_master a, pro_recipe_entry_dtls b,pro_batch_create_mst c
				where p.id=b.mst_id and a.id=b.prod_id and p.batch_id=c.id and b.mst_id in($recipe_id) and b.sub_process_id in($sub_process_id) and b.ratio>0  and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0)
				union
				(
				select p.total_liquor,p.recipe_type,p.surplus_solution,p.pickup, p.batch_id, p.entry_form,p.batch_qty,c.batch_no, b.prod_id as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.sub_process_id, b.dose_base, b.ratio, b.seq_no,b.sub_seq, b.new_batch_weight, b.new_total_liquor,b.liquor_ratio as liquor_ratio_dtls,b.total_liquor as total_liquor_dtls, b.item_lot, b.store_id ,b.comments
				from pro_recipe_entry_mst p, pro_recipe_entry_dtls b,pro_batch_create_mst c
				where p.id=b.mst_id and p.batch_id=c.id and b.mst_id in($recipe_id) and b.sub_process_id in($sub_process_id)   and b.status_active=1 and b.is_deleted=0 and p.working_company_id='$company_id'  and b.status_active=1 and b.is_deleted=0 and b.prod_id=0 and b.sub_process_id in($subprocessforwashin) and p.status_active=1 and p.is_deleted=0) order by sub_seq,seq_no";
			}
			elseif (in_array(445, $uniqEntrF)) {
				 $sql = "(select p.total_liquor,p.recipe_type,p.surplus_solution,p.pickup,p.batch_qty, p.batch_id, p.entry_form,p.batch_qty,c.batch_no, a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.sub_process_id, b.dose_base, b.ratio, b.seq_no,b.sub_seq, b.new_batch_weight, b.new_total_liquor,b.liquor_ratio as liquor_ratio_dtls,b.total_liquor as total_liquor_dtls, b.item_lot, b.store_id ,b.comments
				from pro_recipe_entry_mst p, product_details_master a, pro_recipe_entry_dtls b,pro_batch_create_mst c
				where p.id=b.mst_id and a.id=b.prod_id and p.batch_id=c.id and b.mst_id in($recipe_id) and b.sub_process_id in($sub_process_id) and b.ratio>0  and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' and a.item_category_id in(5,6,7,23) and a.status_active=1 and a.is_deleted=0)
				union
				(
				select p.total_liquor,p.recipe_type,p.surplus_solution,p.pickup,p.batch_qty, p.batch_id, p.entry_form,p.batch_qty,c.batch_no, b.prod_id as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.sub_process_id, b.dose_base, b.ratio, b.seq_no,b.sub_seq, b.new_batch_weight, b.new_total_liquor,b.liquor_ratio as liquor_ratio_dtls,b.total_liquor as total_liquor_dtls, b.item_lot, b.store_id ,b.comments
				from pro_recipe_entry_mst p, pro_recipe_entry_dtls b,pro_batch_create_mst c
				where p.id=b.mst_id and p.batch_id=c.id and b.mst_id in($recipe_id) and b.sub_process_id in($sub_process_id)   and b.status_active=1 and b.is_deleted=0 and p.working_company_id='$company_id'  and b.status_active=1 and b.is_deleted=0 and b.prod_id=0 and b.sub_process_id in($subprocessforwashin) and p.status_active=1 and p.is_deleted=0) order by sub_seq,seq_no";
			}
			elseif (in_array(468, $uniqEntrF)) {
				 $sql = "select p.total_liquor,p.recipe_type,p.surplus_solution,p.pickup,p.batch_qty, p.batch_id, p.entry_form,p.batch_qty,c.batch_no,a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.sub_process_id, b.dose_base, b.ratio, b.seq_no, b.new_batch_weight, b.new_total_liquor,b.liquor_ratio as liquor_ratio_dtls,b.total_liquor as total_liquor_dtls, b.item_lot, b.store_id,b.comments
				from pro_recipe_entry_mst p, product_details_master a, pro_recipe_entry_dtls b, pro_batch_create_mst c
				where p.id=b.mst_id and a.id=b.prod_id and p.batch_id=c.id and b.mst_id in($recipe_id) and b.sub_process_id in($sub_process_id) and b.ratio>0 and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' and a.item_category_id in(5,6,7,23)  and a.status_active=1 and a.is_deleted=0
				order by b.sub_seq,b.seq_no";
				}
			 else {
				 $sql = "(select p.total_liquor,p.recipe_type,p.surplus_solution,p.pickup,p.batch_qty, p.batch_id, p.entry_form,p.batch_qty,c.batch_no,a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size,a.current_stock, a.unit_of_measure, b.sub_process_id, b.dose_base, b.ratio, b.seq_no,b.sub_seq, b.new_batch_weight, b.new_total_liquor,b.liquor_ratio as liquor_ratio_dtls,b.total_liquor as total_liquor_dtls, b.item_lot, b.store_id,b.comments
				from pro_recipe_entry_mst p, product_details_master a, pro_recipe_entry_dtls b, pro_batch_create_mst c
				where p.id=b.mst_id and a.id=b.prod_id and p.batch_id=c.id and b.mst_id in($recipe_id) and b.sub_process_id in($sub_process_id) and b.ratio>0 and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' and a.item_category_id in(5,6,7,23)  and a.status_active=1 and a.is_deleted=0 and b.is_checked=1)
				union
				(
				select p.total_liquor,p.recipe_type,p.surplus_solution,p.pickup,p.batch_qty, p.batch_id, p.entry_form,p.batch_qty,c.batch_no, b.prod_id as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size,null as current_stock, null as unit_of_measure, b.sub_process_id, b.dose_base, b.ratio, b.seq_no,b.sub_seq, b.new_batch_weight, b.new_total_liquor,b.liquor_ratio as liquor_ratio_dtls,b.total_liquor as total_liquor_dtls, b.item_lot, b.store_id ,b.comments
				from pro_recipe_entry_mst p, pro_recipe_entry_dtls b,pro_batch_create_mst c
				where p.id=b.mst_id and p.batch_id=c.id and b.mst_id in($recipe_id) and b.sub_process_id in($sub_process_id)   and b.status_active=1 and b.is_deleted=0 and p.working_company_id='$company_id'  and b.status_active=1 and b.is_deleted=0 and b.prod_id=0 and b.sub_process_id in($subprocessforwashin) and p.status_active=1 and p.is_deleted=0) order by sub_seq,seq_no";
			}
			 //echo $sql;
			$i = 1;
			$subprocessDataArr = array();
			$subprocessProdQntyArr = array();
			$prodDataArr = array();
			$nameArray = sql_select($sql);
			foreach ($nameArray as $selectResult) {

				$current_stock = $selectResult[csf('current_stock')];
				$recipe_type = $selectResult[csf('recipe_type')];
				$surplus_solution = $selectResult[csf('surplus_solution')];
				$pickup = $selectResult[csf('pickup')];
				$batch_wgt = $selectResult[csf('batch_qty')];//pickup,p.batch_qty
				$current_stock_check=number_format($current_stock,7,'.','');
				//(Batch weight*Pick Up/100)+Surplus Solution;
				$total_solution=($batch_wgt*$pickup/100)+$surplus_solution;
				//if($current_stock_check>0)
				//{
					$subprocessDataArr[$selectResult[csf('sub_process_id')]] .= $selectResult[csf('id')] . ",";
					$prodDataArr[$selectResult[csf('id')]] = $selectResult[csf('item_category_id')] . "**" . $selectResult[csf('item_group_id')] . "**" . $selectResult[csf('sub_group_name')] . "**" . $selectResult[csf('item_description')] . "**" . $selectResult[csf('item_size')] . "**" . $selectResult[csf('unit_of_measure')];
					$ratio = $selectResult[csf('ratio')];
					if ($selectResult[csf('dose_base')] == 1)
					{
						if ($selectResult[csf('entry_form')] == 60) { //Dyeing Re Process
							//$perc_calculate_qnty=$selectResult[csf('new_total_liquor')];
							$perc_calculate_qnty = $selectResult[csf('total_liquor_dtls')];

						} else {
							$perc_calculate_qnty = $selectResult[csf('total_liquor_dtls')];
						}
						if($recipe_type==2) //CBP
						{
							$recipe_qnty = ($total_solution * $ratio) / 1000;
						}
						else
						{
						$recipe_qnty = ($perc_calculate_qnty * $ratio) / 1000;
						}
					}
					else if ($selectResult[csf('dose_base')] == 2)
					{
						if ($selectResult[csf('entry_form')] == 60) {
							$perc_calculate_qnty = $selectResult[csf('new_batch_weight')];
						} else if ($selectResult[csf('entry_form')] == 59 || $selectResult[csf('entry_form')] == 468) {
							$perc_calculate_qnty = $selectResult[csf('batch_qty')];
						} else {
							$perc_calculate_qnty = $selectResult[csf('batch_qty')];
						}
						if($recipe_type==2) //CBP
						{
							$recipe_qnty = ($total_solution * $ratio) / 1000;
						}
						else
						{
						$recipe_qnty = ($perc_calculate_qnty * $ratio) / 100;
						}
					}

					$subprocessProdQntyArr[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]]['dosebase'] .= $selectResult[csf('dose_base')] . ",";
					$subprocessProdQntyArr[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]]['comments'] .= $selectResult[csf('comments')] . "***";
					/*
					if(!empty($topping_data[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]]))
					{
						$subprocessProdQntyArr[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]]['comments'] .= $topping_data[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]] . "***";
					}
					if(!empty($copy_data[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]]))
					{
						$subprocessProdQntyArr[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]]['comments'] .= $copy_data[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]] . "***";
					}*/
					$subprocessProdQntyArr[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]]['recipe_qnty'] += $recipe_qnty;
					$subprocessProdQntyArr[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]]['lq_or_bw_qnty'] += $perc_calculate_qnty;
					$subprocessProdQntyArr[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]]['seq_no'] = $selectResult[csf('seq_no')];
					$subprocessProdQntyArr[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]]['sub_seq'] = $selectResult[csf('sub_seq')];
					$subprocessProdQntyArr[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]]['store_id'] = $selectResult[csf('store_id')];
					$subprocessProdQntyArr[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]]['total_liquor_dtls'] = $selectResult[csf('total_liquor_dtls')];
					$subprocessProdQntyArr[$selectResult[csf('sub_process_id')]][$selectResult[csf('id')]][$selectResult[csf('item_lot')]]['current_stock'] = $selectResult[csf('current_stock')];
					$prodIds_arr[$selectResult[csf('id')]]=$selectResult[csf('id')];

				//}
			}
			$StoreProdIds_cond="";
			if($db_type==2 && count($prodIds_arr)>1000)
			{
				$StoreProdIds_cond=" and (";
				$prodIdsArr=array_chunk($prodIds_arr,999);
				foreach($prodIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$StoreProdIds_cond.=" prod_id in($ids) or ";
				}
				$StoreProdIds_cond=chop($StoreProdIds_cond,'or ');
				$StoreProdIds_cond.=")";
			}
			else
			{
				$StoreProdIds_cond=" and prod_id in (".implode(",",$prodIds_arr).")";
			}
			$con = connect();
			$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (156)");
			oci_commit($con);

			if(count($prodIds_arr)>0) fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 156, 4, $prodIds_arr, $empty_arr);

			//   $sql_prod_store = "SELECT id, prod_id, store_id, cons_qty, lot  from inv_store_wise_qty_dtls where status_active=1 and is_deleted=0 $StoreProdIds_cond";
			$sql_prod_store = "SELECT a.id, a.prod_id, a.store_id, a.cons_qty, a.lot  from inv_store_wise_qty_dtls a,GBL_TEMP_ENGINE b  where a.prod_id=b.REF_VAL and b.ENTRY_FORM=156 and b.USER_ID=$user_id and b.REF_FROM=4  and a.status_active=1 and a.is_deleted=0 ";

			//echo $sql_prod_store;
			$sql_prod_store_result = sql_select($sql_prod_store);
			foreach ($sql_prod_store_result as $row)
			{
				$prod_store_data_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("lot")]]= $row[csf("cons_qty")];
			}
			unset($sql_prod_store_result);

			$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (156)");
			oci_commit($con);
			disconnect($con);

			//echo "<pre>";print_r($subprocessProdQntyArr);die;
			//echo $sub_process_id;
			$sub_process_id = explode(",", $sub_process_id);
			$dosebase_mismatch_prod_id_arr = array();
			foreach ($subprocessProdQntyArr as $process_id=>$subprocess_data) {
				//$subprocessData = array_unique(explode(",", substr($subprocessDataArr[$process_id], 0, -1)));
				foreach ($subprocess_data as $prod_id=>$prod_data) {
					foreach ($prod_data as $item_lot=>$item_data) {
						$current_stock = $subprocessProdQntyArr[$process_id][$prod_id][$item_lot]['current_stock'];
						$current_stock_check=number_format($current_stock,7,'.','');
						$store_wise_stock=$prod_store_data_array[$prod_id][$item_data['store_id']][$item_lot];
						//if($current_stock_check>0)
						//{
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

							$subprocessData = explode("**", $prodDataArr[$prod_id]);
							$item_category_id = $subprocessData[0];
							$item_group_id = $subprocessData[1];
							$sub_group_name = $subprocessData[2];
							$item_description = $subprocessData[3] . " " . $subprocessData[4];
							$unit_of_measure = $subprocessData[5];
							$seq_no = $subprocessProdQntyArr[$process_id][$prod_id][$item_lot]['seq_no'];

							$dosebaseData = array_unique(explode(",", substr($subprocessProdQntyArr[$process_id][$prod_id][$item_lot]['dosebase'], 0, -1)));
							if (count($dosebaseData) > 1) {
								$dosebase = 0;
								$ratio = 0;
								$recipe_qnty = 0;
								$dosebase_mismatch_prod_id[$process_id] .= $prod_id . ",";
								//echo "KKF";
							} else {
								$dosebase = implode(",", $dosebaseData);
								$recipe_qnty = number_format($subprocessProdQntyArr[$process_id][$prod_id][$item_lot]['recipe_qnty'], 6, '.', '');
								$lq_or_bw_qnty = $subprocessProdQntyArr[$process_id][$prod_id][$item_lot]['lq_or_bw_qnty'];
								if ($dosebase == 1) {

									$ratio = number_format(($recipe_qnty * 1000) / $lq_or_bw_qnty, 6, '.', '');

								} else {
									$ratio = number_format(($recipe_qnty * 100) / $lq_or_bw_qnty, 6, '.', '');

								}
							}
							if ($ratio == 'nan' || $ratio == '') $ratio = 0;
							$total_liquor_dtls = $subprocessProdQntyArr[$process_id][$prod_id][$item_lot]['total_liquor_dtls'];
							$title="GPLL=Recipe Qty(".$recipe_qnty.")*1000/LQ or bw Qnty(".$lq_or_bw_qnty.")".' and '."% On BW==Recipe Qty(".$recipe_qnty.")*100/LQ or bw Qnty(".$lq_or_bw_qnty.")";
							if($process_id==93 || $process_id==94 || $process_id==95 || $process_id==96 || $process_id==97 || $process_id==98 || $process_id==140 || $process_id==141 || $process_id==142 || $process_id==143)
							{
								$ratio = 0;$recipe_qnty=0;
							}
							else  $ratio =$ratio;$recipe_qnty=$recipe_qnty;
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="vertical-align:middle">
								<td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
								<td width="83" id="subprocess_id_<? echo $i; ?>"><? echo $dyeing_sub_process[$process_id]; ?>
								<input type="hidden" name="txt_subprocess_id[]" id="txt_subprocess_id_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $process_id; ?>">
								</td>
								<td width="50" id="product_id_<? echo $i; ?>" align="center"><? echo $prod_id; ?>
								<input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px" value="<? echo $prod_id; ?>">
								</td>
								<td width="50" id="lot_<? echo $i; ?>" align="center" style="word-break:break-all"><p><? echo $item_lot; ?>
								<input type="hidden" name="txt_lot[]" id="txt_lot_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px" value="<? echo $item_lot; ?>"></p>
								</td>
								<td width="80"><p><? echo $item_category[$item_category_id]; ?></p>
								<input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px" value="<? echo $item_category_id; ?>">
								</td>
								<td width="90" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$item_group_id]; ?> &nbsp;</p></td>
								<td width="80" id="sub_group_name_<? echo $i; ?>"><p><? echo $sub_group_name; ?>&nbsp;</p></td>
								<td width="110" id="item_description_<? echo $i; ?>"><p><? echo $item_description; ?></p></td>
								<td width="40" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$unit_of_measure]; ?></td>
                                <td width="80" align="right" id="stock_<? echo $i; ?>"><? echo number_format($store_wise_stock,2); ?></td>
								<td width="50" align="center" id="seq_no_<? echo $i; ?>"><? echo $seq_no; ?></td>
								<td width="75" align="center"
								id="dose_base_<? echo $i; ?>"><? echo create_drop_down("cbo_dose_base_$i", 70, $dose_base, "", 1, "- Select Dose Base -", $dosebase, "", 1); ?></td>
								<td width="72" align="center" title="<? echo 'Total Liquor ' . $total_liquor_dtls.' And '.$title; ?>" id="ratio_<? echo $i; ?>">
                                <input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" value="<? echo $ratio; ?>" disabled>
								</td>
								<td width="80" align="center" id="recipe_qnty_<? echo $i; ?>">
									<input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:67px" value="<? echo $recipe_qnty; ?>" disabled>
								</td>
								<td width="53" align="center" id="adj_per_<? echo $i; ?>">
									<input type="text" name="txt_adj_per[]" id="txt_adj_per_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" value="" onKeyUp="calculate_requs_qty(<? echo $i; ?>)">
								</td>
								<td width="87" align="center"
								id="adj_type_<? echo $i; ?>"><? echo create_drop_down("cbo_adj_type_$i", 80, $increase_decrease, "", 1, "- Select -", "", "calculate_requs_qty($i)"); ?></td>
								<td align="center" id="reqn_qnty_<? echo $i; ?>" width="80">
									<input type="text" name="reqn_qnty_edit[]" id="reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $recipe_qnty; ?>" style="width:68px" disabled>
									<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="">
									<input type="hidden" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $recipe_qnty; ?>">
									<input type="hidden" name="txt_seq_no[]" id="txt_seq_no_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $seq_no; ?>">
                                    <input type="hidden" name="txt_sub_seq_no[]" id="txt_sub_seq_no_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $item_data['sub_seq']; ?>">
								</td>
								<td>
									<p style="max-width: 110px;word-wrap: break-word;">
										<?php
											$comments= implode(", ", array_filter(array_unique(explode("***", chop($item_data['comments'],"***")))));
										?>
										<input class="text_boxes" type="text" name="comment_<? echo $i; ?>" id="txt_comment_<? echo $i; ?>" value="<?=$comments;?>" style="width: 109px;">
									</p>
								</td>
							</tr>
							<?
							$i++;
						}
					}
				}
				?>
			</tbody>
		</table>
	</div>
	<div>
	</div>
	<?
	exit();
}

if ($action == "item_details_for_update")
{
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1350" class="rpt_table" align="left">
		<thead>
			<th width="30">SL</th>
			<th width="83">Sub Process</th>
			<th width="50">Prod. ID</th>
            <th width="50">Lot</th>
			<th width="80">Item Category</th>
			<th width="90">Group</th>
			<th width="80">Sub Group</th>
			<th width="110">Item Description</th>
			<th width="40">UOM</th>
            <th width="80">Stock</th>
			<th width="50">Seq. No.</th>
			<th width="75">Dose Base</th>
			<th width="72">Ratio</th>
			<th width="80">Recipe Qnty.</th>
			<th width="53">Adj%.</th>
			<th width="87">Adj. Type</th>
			<th width="80">Reqn. Qnty.</th>
			<th >Comments</th>
		</thead>
	</table>
	<div style="width:1350px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1330" class="rpt_table" id="tbl_list_search" align="left">
			<tbody>
			<?php
			//ini_set('display_errors', 1);
			if($subprocessforwashin=="") $subprocessforwashin=0;
			$item_group_arr = return_library_array("select id, item_name from lib_item_group", "id", "item_name");
			$sql = "(select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.current_stock, a.unit_of_measure, b.id as dtls_id, b.sub_process, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit, b.seq_no,b.sub_seq, b.recipe_id, b.item_lot, b.store_id,b.comments
			from product_details_master a, dyes_chem_issue_requ_dtls b
			where a.id=b.product_id and b.mst_id=$data and b.status_active=1 and b.is_deleted=0 and  b.ratio>0  and a.item_category_id in(5,6,7,23)  and a.status_active=1 and a.is_deleted=0)
			union
			(
			select null as id, null as item_category_id, null as item_group_id, null as sub_group_name, null as item_description, null as item_size, null as current_stock, null as unit_of_measure, b.id as dtls_id, b.sub_process, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit, b.seq_no,b.sub_seq, b.recipe_id, b.item_lot, b.store_id,b.comments
			from dyes_chem_issue_requ_dtls b
			where b.sub_process in($subprocessforwashin) and b.mst_id=$data and b.status_active=1 and b.is_deleted=0
			) order by sub_seq,seq_no";

			$liqour_ratio_rec_arr = array();
			//$recipe_sql = sql_select("select mst_id,sub_process_id,total_liquor from pro_recipe_entry_dtls where status_active=1 and is_deleted=0");
			//print_r($recipe_sql);
			/*foreach ($recipe_sql as $recipe_row) {
				$liqour_ratio_rec_arr[$recipe_row[csf('mst_id')]][$recipe_row[csf('sub_process_id')]] = $row[csf('total_liquor')];

			}*/

			$i = 1;
			$nameArray = sql_select($sql);
			foreach ($nameArray as $selectResult)
			{
				$prodIds_arr[$selectResult[csf('id')]]=$selectResult[csf('id')];
			}
			$StoreProdIds_cond="";
			if($db_type==2 && count($prodIds_arr)>1000)
			{
				$StoreProdIds_cond=" and (";
				$prodIdsArr=array_chunk($prodIds_arr,999);
				foreach($prodIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$StoreProdIds_cond.=" prod_id in($ids) or ";
				}
				$StoreProdIds_cond=chop($StoreProdIds_cond,'or ');
				$StoreProdIds_cond.=")";
			}
			else
			{
				$StoreProdIds_cond=" and prod_id in (".implode(",",$prodIds_arr).")";
			}
			$con = connect();
			$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (156)");
			oci_commit($con);

			if(count($prodIds_arr)>0) fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 156, 4, $prodIds_arr, $empty_arr);

			// $sql_prod_store = "SELECT id, prod_id, store_id, cons_qty, lot  from inv_store_wise_qty_dtls where status_active=1 and is_deleted=0 $StoreProdIds_cond";
			//echo $sql_prod_store;
			$sql_prod_store = "SELECT a.id, a.prod_id, a.store_id, a.cons_qty, a.lot  from inv_store_wise_qty_dtls a,GBL_TEMP_ENGINE b  where a.prod_id=b.REF_VAL and b.ENTRY_FORM=156 and b.USER_ID=$user_id and b.REF_FROM=4  and a.status_active=1 and a.is_deleted=0 ";
			$sql_prod_store_result = sql_select($sql_prod_store);
			foreach ($sql_prod_store_result as $row)
			{
				$prod_store_data_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("lot")]]= $row[csf("cons_qty")];
			}
			unset($sql_prod_store_result);

			$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (156)");
			oci_commit($con);
			disconnect($con);

			foreach ($nameArray as $selectResult)
			{
				$current_stock = $selectResult[csf('current_stock')];
				$current_stock_check=number_format($current_stock,7,'.','');
				$store_wise_stock=$prod_store_data_array[$selectResult[csf('id')]][$selectResult[csf('store_id')]][$selectResult[csf('item_lot')]];
				//if($current_stock_check>0)
				//{
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					$total_liqour = $liqour_ratio_rec_arr[$selectResult[csf('recipe_id')]][$selectResult[csf('sub_process')]]['total_ratio'];

					$dosebase = $selectResult[csf('dose_base')];
					$recipe_qnty = $selectResult[csf('recipe_qnty')];
					$ratio = $selectResult[csf('ratio')];
					if ($ratio == 'nan' || $ratio == '') $ratio = 0;
					$title="GPLL=Recipe Qty*1000/LQ or bw Qnty".' and '."% On BW==Recipe Qty*100/LQ or bw Qnty";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="vertical-align:middle">
						<td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
						<td width="83"
						id="subprocess_id_<? echo $i; ?>"><p><? echo $dyeing_sub_process[$selectResult[csf('sub_process')]]; ?>
						<input type="hidden" name="txt_subprocess_id[]" id="txt_subprocess_id_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $selectResult[csf('sub_process')]; ?>"></p>
                        </td>
                        <td width="50" id="product_id_<? echo $i; ?>" align="center"><p><? echo $selectResult[csf('id')]; ?>
                        <input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px" value="<? echo $selectResult[csf('id')]; ?>"></p>
                        </td>
                        <td width="50" id="lot_<? echo $i; ?>" align="center"><p><? echo $selectResult[csf('item_lot')]; ?>
                        <input type="hidden" name="txt_lot[]" id="txt_lot_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px" value="<? echo $selectResult[csf('item_lot')]; ?>"></p>
                        </td>
                        <td width="80"><p><? echo $item_category[$selectResult[csf('item_category_id')]]; ?>
                            <input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px" value="<? echo $selectResult[csf('item_category_id')]; ?>"></p>
                        </td>
                        <td width="90" id="item_group_id_<? echo $i; ?>">
						<p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]; ?>&nbsp;</p></td>
						<td width="80" id="sub_group_name_<? echo $i; ?>"><p><? echo $selectResult[csf('sub_group_name')]; ?>&nbsp;</p></td>
                        <td width="110" id="item_description_<? echo $i; ?>"><p><? echo $selectResult[csf('item_description')] . " " . $selectResult[csf('item_size')]; ?></p></td>
                        <td width="40" align="center" id="uom_<? echo $i; ?>"><p><? echo $unit_of_measurement[$selectResult[csf('unit_of_measure')]]; ?></p></td>
                        <td width="80" align="right" id="stock_<? echo $i; ?>"><p><? echo number_format($store_wise_stock,2); ?></p></td>
                        <td width="50" align="center" id="seq_no_<? echo $i; ?>"><p><? echo $selectResult[csf('seq_no')]; ?></p></td>
                        <td width="75" align="center" id="dose_base_<? echo $i; ?>"><p><? echo create_drop_down("cbo_dose_base_$i", 70, $dose_base, "", 1, "- Select Dose Base -", $dosebase, "", 1); ?></p></td>
                        <td width="72" align="center" title="<? echo 'Total Liqour ' . $total_liqour.' And '.$title; ?>" id="ratio_<? echo $i; ?>"><p>
                        <input type="text" name="txt_ratio[]" id="txt_ratio_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" value="<? echo number_format($ratio, 6, '.', ''); ?>" disabled></p>
                        </td>
                        <td width="80" align="center" id="recipe_qnty_<? echo $i; ?>"><p>
                        <input type="text" name="txt_recipe_qnty[]" id="txt_recipe_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:67px" value="<? echo number_format($recipe_qnty, 6, '.', ''); ?>" disabled></td>
                        <td width="53" align="center" id="adj_per_<? echo $i; ?>">
                        <input type="text" name="txt_adj_per[]" id="txt_adj_per_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" value="<? echo $selectResult[csf('adjust_percent')]; ?>" onKeyUp="calculate_requs_qty(<? echo $i; ?>)"></p>
                        </td>
                        <td width="87" align="center"
                        id="adj_type_<? echo $i; ?>"><p><? echo create_drop_down("cbo_adj_type_$i", 80, $increase_decrease, "", 1, "- Select -", $selectResult[csf('adjust_type')], "calculate_requs_qty($i)"); ?></p></td>
                        <td align="center" id="reqn_qnty_<? echo $i; ?>" width="80"><p>
                            <input type="text" name="reqn_qnty_edit[]" id="reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo number_format($selectResult[csf('req_qny_edit')], 6, '.', ''); ?>" style="width:68px" disabled>
                            <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $selectResult[csf('dtls_id')]; ?>">
                            <input type="hidden" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $selectResult[csf('required_qnty')]; ?>">
                            <input type="hidden" name="txt_seq_no[]" id="txt_seq_no_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $selectResult[csf('seq_no')]; ?>">
                            <input type="hidden" name="txt_sub_seq_no[]" id="txt_sub_seq_no_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $selectResult[csf('sub_seq')]; ?>"></p>
                        </td>
                        <td>
                        	<p style="max-width: 110px; word-wrap: break-word;">
                        		<input type="text" class="text_boxes" name="txt_comment_<? echo $i; ?>" id="txt_comment_<? echo $i; ?>" value="<?=$selectResult[csf('comments')]?>" style="width: 108px;">
                        	</p>
                        </td>
                    </tr>
                    <?
                    $i++;
                    //}
				}
				?>
				</tbody>
			</table>
		</div>
		<?
	exit();
}

if ($action == "save_update_delete")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$mst_id = "";
		$requ_no = "";

		if (str_replace("'", "", $update_id) == "") {
			if ($db_type == 0) $year_cond = "YEAR(insert_date)";
			else if ($db_type == 2) $year_cond = "to_char(insert_date,'YYYY')";
			else $year_cond = "";//defined Later

			$new_requ_no = explode("*", return_mrr_number(str_replace("'", "", $cbo_company_name), '', 'DCIR', date("Y", time()), 5, "select requ_no_prefix,requ_prefix_num from dyes_chem_issue_requ_mst where company_id=$cbo_company_name and requisition_basis=8 and entry_form in(156,157)  and $year_cond=" . date('Y', time()) . " order by id desc ", "requ_no_prefix", "requ_prefix_num"));
			$id = return_next_id("id", "dyes_chem_issue_requ_mst", 1);
			$field_array = "id,requ_no,requ_no_prefix,requ_prefix_num,company_id,location_id,requisition_date,requisition_basis,batch_id,recipe_id,method,machine_id,inserted_by,insert_date,entry_form,store_id";
			$data_array = "(" . $id . ",'" . $new_requ_no[0] . "','" . $new_requ_no[1] . "'," . $new_requ_no[2] . "," . $cbo_company_name . "," . $cbo_location_name . "," . $txt_requisition_date . "," . $cbo_receive_basis . "," . $txt_batch_id . "," . $txt_recipe_id . "," . $cbo_method . "," . $machine_id . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',156," . $cbo_store_name . ")";

			$mst_id = $id;
			$requ_no = $new_requ_no[0];
		} else {
			$field_array_up = "location_id*requisition_date*recipe_id*batch_id*method*machine_id*updated_by*update_date";
			$data_array_up = "" . $cbo_location_name . "*" . $txt_requisition_date . "*" . $txt_recipe_id . "*" . $txt_batch_id . "*" . $cbo_method . "*" . $machine_id . "*" . $user_id . "*'" . $pc_date_time . "'";
			$mst_id = str_replace("'", "", $update_id);
			$requ_no = str_replace("'", "", $txt_mrr_no);
		}

		$id_att = return_next_id("id", "dyes_chem_requ_recipe_att", 1);
		$field_array_att = "id,mst_id,recipe_id";
		$recipe_id_all = explode(",", str_replace("'", "", $txt_recipe_id));
		foreach ($recipe_id_all as $recipe_id) {
			if ($data_array_att != "") $data_array_att .= ",";
			$data_array_att .= "(" . $id_att . "," . $mst_id . "," . $recipe_id . ")";
			$id_att = $id_att + 1;
		}

		$id_dtls = return_next_id("id", "dyes_chem_issue_requ_dtls", 1);
		$field_array_dtls = "id,mst_id,requ_no,batch_id,recipe_id,requisition_basis,sub_process,product_id,item_category,ratio,dose_base, recipe_qnty, adjust_percent, adjust_type, required_qnty,req_qny_edit,seq_no,sub_seq,inserted_by,insert_date,store_id,item_lot,comments";
		$id_dtls_child = return_next_id("id", "dyes_chem_issue_requ_dtls_child", 1);
		$field_array_dtls_child = "id, mst_id, dtls_id, batch_id, recipe_id, inserted_by, insert_date";
		$data_array_dtls_child = $data_array_dtls = "";
		for ($i = 1; $i <= $total_row; $i++)
		{
			$txt_prod_id = "txt_prod_id_" . $i;
			$txt_item_cat = "txt_item_cat_" . $i;
			$cbo_dose_base = "cbo_dose_base_" . $i;
			$txt_ratio = "txt_ratio_" . $i;
			$txt_recipe_qnty = "txt_recipe_qnty_" . $i;
			$txt_adj_per = "txt_adj_per_" . $i;
			$cbo_adj_type = "cbo_adj_type_" . $i;
			$txt_reqn_qnty = "txt_reqn_qnty_" . $i;
			$txt_reqn_qnty_edit = "reqn_qnty_edit_" . $i;
			$updateIdDtls = "updateIdDtls_" . $i;
			$txt_subprocess_id = "txt_subprocess_id_" . $i;
			$txt_seq_no = "txt_seq_no_" . $i;
			$txt_sub_seq_no = "txt_sub_seq_no_" . $i;
			$txt_lot = "txt_lot_" . $i;
			$txt_comment = "txt_comment_" . $i;
			$txt_ratio = str_replace("'", "", $$txt_ratio);

			if ($data_array_dtls != "") $data_array_dtls .= ",";
			$data_array_dtls .= "(" . $id_dtls . "," . $mst_id . ",'" . $requ_no . "'," . $txt_batch_id . "," . $txt_recipe_id . "," . $cbo_receive_basis . "," . $$txt_subprocess_id . "," . $$txt_prod_id . "," . $$txt_item_cat . ",'" . $txt_ratio . "'," . $$cbo_dose_base . "," . $$txt_recipe_qnty . "," . $$txt_adj_per . "," . $$cbo_adj_type . "," . $$txt_reqn_qnty . "," . $$txt_reqn_qnty_edit . "," . $$txt_seq_no . "," . $$txt_sub_seq_no . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $cbo_store_name . "," . $$txt_lot .",'".str_replace("'", "", $$txt_comment). "')";
			$id_dtls = $id_dtls + 1;
			$txt_recipe_id_arr=array_unique(explode(",",str_replace("'","",$txt_recipe_id)));
			$txt_batch_id_arr=array_unique(explode(",",str_replace("'","",$txt_batch_id)));
			$p=0;
			foreach($txt_recipe_id_arr as $recp_id)
			{
				if ($data_array_dtls_child != "") $data_array_dtls_child .= ",";
				$data_array_dtls_child .= "(" . $id_dtls_child . "," . $mst_id . "," . $id_dtls . ",'" . $txt_batch_id_arr[$p] . "'," . $recp_id . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				$id_dtls_child++;$p++;
			}
		}

		if (str_replace("'", "", $update_id) == "") {
			//echo  "INSERT INTO dyes_chem_issue_requ_mst (".$field_array.") VALUES ".$data_array."";
			$rID = sql_insert("dyes_chem_issue_requ_mst", $field_array, $data_array, 1);
		} else {
			$rID = sql_update("dyes_chem_issue_requ_mst", $field_array_up, $data_array_up, "id", $update_id, 1);
		}

		$rID_att = sql_insert("dyes_chem_requ_recipe_att", $field_array_att, $data_array_att, 1);
		//echo  "10**INSERT INTO dyes_chem_issue_requ_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls.""; die;
		$rID_dtls = sql_insert("dyes_chem_issue_requ_dtls", $field_array_dtls, $data_array_dtls, 1);
		$rID_dtls_child = sql_insert("dyes_chem_issue_requ_dtls_child", $field_array_dtls_child, $data_array_dtls_child, 1);
		//echo "10**".($rID ."&&". $rID_att ."&&". $rID_dtls);die;
		//check_table_status( $_SESSION['menu_id'],0);
		if ($db_type == 0) {
			if ($rID && $rID_att && $rID_dtls && $rID_dtls_child) {
				mysql_query("COMMIT");
				echo "0**" . $requ_no . "**" . $mst_id;
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . $requ_no . "**" . $mst_id;
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID_att && $rID_dtls && $rID_dtls_child) {
				oci_commit($con);
				echo "0**" . $requ_no . "**" . $mst_id;
			} else {
				oci_rollback($con);
				echo "10**" . $requ_no . "**" . $mst_id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation == 1)  // Update Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}

		$last_update_arr = return_library_array("select recipe_id,is_apply_last_update from dyes_chem_requ_recipe_att where mst_id=$update_id", "recipe_id", "is_apply_last_update");
		$is_apply_last_update = str_replace("'", "", $is_apply_last_update);

		if ($is_apply_last_update == 1) {
			$field_array_up = "location_id*requisition_date*recipe_id*batch_id*method*machine_id*is_apply_last_update*updated_by*update_date";
			$data_array = $cbo_location_name . "*" . $txt_requisition_date . "*" . $txt_recipe_id . "*" . $txt_batch_id . "*" . $cbo_method . "*" . $machine_id . "*0*" . $user_id . "*'" . $pc_date_time . "'";
		} else {
			$field_array_up = "location_id*requisition_date*recipe_id*batch_id*method*machine_id*updated_by*update_date";
			$data_array = $cbo_location_name . "*" . $txt_requisition_date . "*" . $txt_recipe_id . "*" . $txt_batch_id . "*" . $cbo_method . "*" . $machine_id . "*" . $user_id . "*'" . $pc_date_time . "'";
		}

		$mst_id = str_replace("'", "", $update_id);
		$requ_no = str_replace("'", "", $txt_mrr_no);

		$id_att = return_next_id("id", "dyes_chem_requ_recipe_att", 1);
		$field_array_att = "id,mst_id,recipe_id,is_apply_last_update";
		$recipe_id_all = explode(",", str_replace("'", "", $txt_recipe_id));
		foreach ($recipe_id_all as $recipe_id) {
			if ($is_apply_last_update == 1) {
				$apply_last_update = 0;
			} else {
				$apply_last_update = $last_update_arr[$recipe_id];
			}

			if ($data_array_att != "") $data_array_att .= ",";
			$data_array_att .= "(" . $id_att . "," . $mst_id . "," . $recipe_id . "," . $apply_last_update . ")";
			$id_att = $id_att + 1;
		}

		$id_dtls = return_next_id("id", "dyes_chem_issue_requ_dtls", 1);
		$field_array_dtls = "id,mst_id,requ_no,batch_id,recipe_id,requisition_basis,sub_process,product_id,item_category,dose_base,ratio, recipe_qnty, adjust_percent, adjust_type, required_qnty,req_qny_edit,seq_no,sub_seq,inserted_by,insert_date,store_id,item_lot,comments";
		$id_dtls_child = return_next_id("id", "dyes_chem_issue_requ_dtls_child", 1);
		$field_array_dtls_child = "id, mst_id, dtls_id, batch_id, recipe_id, inserted_by, insert_date";
		$data_array_dtls_child = $data_array_dtls = "";
		for ($i = 1; $i <= $total_row; $i++)
		{
			$txt_prod_id = "txt_prod_id_" . $i;
			$txt_item_cat = "txt_item_cat_" . $i;
			$cbo_dose_base = "cbo_dose_base_" . $i;
			$txt_ratio = "txt_ratio_" . $i;
			$txt_recipe_qnty = "txt_recipe_qnty_" . $i;
			$txt_adj_per = "txt_adj_per_" . $i;
			$cbo_adj_type = "cbo_adj_type_" . $i;
			$txt_reqn_qnty = "txt_reqn_qnty_" . $i;
			$txt_reqn_qnty_edit = "reqn_qnty_edit_" . $i;
			$updateIdDtls = "updateIdDtls_" . $i;
			$txt_subprocess_id = "txt_subprocess_id_" . $i;
			$txt_seq_no = "txt_seq_no_" . $i;
			$txt_sub_seq_no = "txt_sub_seq_no_" . $i;
			$txt_lot = "txt_lot_" . $i;
			$txt_comment = "txt_comment_" . $i;

			if ($data_array_dtls != "") $data_array_dtls .= ",";
			$data_array_dtls .= "(" . $id_dtls . "," . $mst_id . ",'" . $requ_no . "'," . $txt_batch_id . "," . $txt_recipe_id . "," . $cbo_receive_basis . "," . $$txt_subprocess_id . "," . $$txt_prod_id . "," . $$txt_item_cat . "," . $$cbo_dose_base . "," . $$txt_ratio . "," . $$txt_recipe_qnty . "," . $$txt_adj_per . "," . $$cbo_adj_type . "," . $$txt_reqn_qnty . "," . $$txt_reqn_qnty_edit . "," . $$txt_seq_no . "," . $$txt_sub_seq_no . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $cbo_store_name . "," . $$txt_lot .",'".str_replace("'", "", $$txt_comment). "')";
			$id_dtls = $id_dtls + 1;

			/*if(str_replace("'",'',$$updateIdDtls)!="")
			{
				$id_arr[]=str_replace("'",'',$$updateIdDtls);
				$data_array_up[str_replace("'",'',$$updateIdDtls)] =explode("*",("".$$txt_adj_per."*".$$cbo_adj_type."*".$$txt_reqn_qnty."*".$$txt_reqn_qnty_edit."*".$$txt_seq_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

			}
			else
			{
				if($data_array_dtls!="") $data_array_dtls .=",";
				$data_array_dtls .="(".$id_dtls.",".$mst_id.",'".$requ_no."',".$txt_batch_id.",".$txt_recipe_id.",".$cbo_receive_basis.",".$$txt_subprocess_id.",".$$txt_prod_id.",".$$txt_item_cat.",".$$cbo_dose_base.",".$$txt_ratio.",".$$txt_recipe_qnty.",".$$txt_adj_per.",".$$cbo_adj_type.",".$$txt_reqn_qnty.",".$$txt_reqn_qnty_edit.",".$$txt_seq_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id_dtls=$id_dtls+1;
			}*/
			$txt_recipe_id_arr=array_unique(explode(",",str_replace("'","",$txt_recipe_id)));
			$txt_batch_id_arr=array_unique(explode(",",str_replace("'","",$txt_batch_id)));
			$p=0;
			foreach($txt_recipe_id_arr as $recp_id)
			{
				if ($data_array_dtls_child != "") $data_array_dtls_child .= ",";
				$data_array_dtls_child .= "(" . $id_dtls_child . "," . $mst_id . "," . $id_dtls . ",'" . $txt_batch_id_arr[$p] . "'," . $recp_id . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				$id_dtls_child++;$p++;
			}
		}

		$rID = sql_update("dyes_chem_issue_requ_mst", $field_array_up, $data_array, "id", $update_id, 0);
		$delete_att = execute_query("delete from dyes_chem_requ_recipe_att where mst_id=$update_id", 0);
		$delete_dtls = execute_query("delete from dyes_chem_issue_requ_dtls where mst_id=$update_id", 0);
		//echo "10**INSERT INTO dyes_chem_requ_recipe_att (".$field_array_att.") VALUES ".$data_array_att."";oci_rollback($con);disconnect($con);die;
		$rID_att = sql_insert("dyes_chem_requ_recipe_att", $field_array_att, $data_array_att, 0);
		$delete_dtls_child = execute_query("delete from dyes_chem_issue_requ_dtls_child where mst_id=$update_id", 0);
		$rID_dtls_child = sql_insert("dyes_chem_issue_requ_dtls_child", $field_array_dtls_child, $data_array_dtls_child, 1);

		$rID_dtls = true;
		if ($data_array_dtls != "") {
			//echo "INSERT INTO dyes_chem_issue_requ_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls."";
			$rID_dtls = sql_insert("dyes_chem_issue_requ_dtls", $field_array_dtls, $data_array_dtls, 1);
		}

		/*$rID_dtls_update=true;
		if(count($data_array_up)>0)
		{
			//echo bulk_update_sql_statement( "dyes_chem_issue_requ_dtls", "id", $field_array_dtls_update, $data_array_up, $id_arr );
			$rID_dtls_update=execute_query(bulk_update_sql_statement("dyes_chem_issue_requ_dtls", "id", $field_array_dtls_update, $data_array_up, $id_arr ));
		}*/
		//oci_rollback($con);
		//echo "10**".$rID."**".$rID_att."**".$rID_dtls."**".$delete_att."**".$delete_dtls."**".$delete_dtls_child."**".$rID_dtls_child;oci_rollback($con);disconnect($con);die;
		//check_table_status( $_SESSION['menu_id'],0);
		if ($db_type == 0) {
			if ($rID && $rID_att && $rID_dtls && $delete_att && $delete_dtls && $delete_dtls_child && $rID_dtls_child) {
				mysql_query("COMMIT");
				echo "1**" . $requ_no . "**" . $mst_id;
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . $requ_no . "**" . $mst_id;
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID_att && $rID_dtls && $delete_att && $delete_dtls && $delete_dtls_child && $rID_dtls_child) {
				oci_commit($con);
				echo "1**" . $requ_no . "**" . $mst_id;
			} else {
				oci_rollback($con);
				echo "10**" . $requ_no . "**" . $mst_id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation == 2)  //Delete
	{
			$con = connect();
			if ($db_type == 0) {
				mysql_query("BEGIN");
			}
		$mst_id = str_replace("'", "", $update_id);
		$requ_no = str_replace("'", "", $txt_mrr_no);

		//$sql_issue="select a.issue_number from inv_issue_master a, dyes_chem_issue_dtls b, dyes_chem_requ_recipe_att c where a.req_no=c.mst_id and a.id=b.mst_id and a.entry_form=5 and a.issue_basis=7 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.req_no='$mst_id'";
		//echo "13**0**".$sql_issue;disconnect($con);die;

		$issue_number =return_field_value("a.issue_number as issue_number","inv_issue_master a, dyes_chem_issue_dtls b, dyes_chem_requ_recipe_att c","a.req_no=c.mst_id and a.id=b.mst_id and a.entry_form=5 and a.issue_basis=7 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.req_no='$mst_id'", "issue_number");
		if($issue_number !="" )
		{
			echo "13**0**".$issue_number;
			disconnect($con);
			die;
		}

		$field_array="updated_by*update_date*status_active*is_deleted";
		$field_array_dtls="status_active*is_deleted";
		$data_array="'".$user_id."'*'".$pc_date_time."'*0*1";
		$data_array_dtls="0*1";
		$rID=sql_update("dyes_chem_issue_requ_mst",$field_array,$data_array,"id",$mst_id,0);
		$dtlsrID=sql_update("dyes_chem_issue_requ_dtls",$field_array,$data_array,"mst_id",$mst_id,0);
		$dtlsrID_att=sql_update("dyes_chem_requ_recipe_att",$field_array_dtls,$data_array_dtls,"mst_id",$mst_id,0);
		$dtlsrID_child=sql_update("dyes_chem_issue_requ_dtls_child",$field_array_dtls,$data_array_dtls,"mst_id",$mst_id,0);


		// echo "10**".$rID.'='.$dtlsrID.'='.$dtlsrID_att;die;

		if($db_type==0)
		{
			if($rID && $dtlsrID && $dtlsrID_att)
			{
				mysql_query("COMMIT");
				echo "2**" . $requ_no . "**" . $mst_id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**" . $requ_no . "**" . $mst_id;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
		 if($rID && $dtlsrID && $dtlsrID_att)
		     {
				oci_commit($con);
				echo "2**" . $requ_no . "**" . $mst_id;
		     }
		else
			{
				oci_rollback($con);
				echo "10**" . $requ_no . "**" . $mst_id;
			}
		}


		disconnect($con);
		die;
	}
}


if ($action == "data_exchange") {
		$process = array(&$_POST);
		extract(check_magic_quote_gpc($process));
	if ($operation == 0)  // Insert Here
	{

		$dyeing_sub_process = array(1=>"Demineralisation",10=>"Pretreatment",20=>"Neutralisation-1",21=>"Neutralisation-2",22=>"Neutralisation-3",23=>"Neutralisation-4",30=>"Biopolish",40=>"Dyestuff",50=>"Dyeing Bath",60=>"After Treatment 1",70=>"Color Remove",90=>"Other",91=>"Leveling",92=>"Finishing Process",93=>"Wash 1",94=>"Wash 2",95=>"Wash 3",96=>"Wash 4",97=>"Wash 5",98=>"Wash 6",99=>"After Treatment 2",100=>"After Treatment 3",101=>"After Treatment 4",102=>"Desizing",103=>"Enzyme",104=>"PP Bleach",105=>"Bleach",106=>"PP Bleach Neutral",107=>"Bleach Neutral",108=>"Cleaning",109=>"PP Neutral",110=>"Tint",111=>"Fixing",112=>"Softener",113=>"Acid Wash",114=>"Towel Bleach",115=>"Scouring",116=>"Resign Spray",117=>"Dyeing",118=>"Soaping",119=>"Silicon");

		$dyeing_sub_process_map=array(
		1=>1,10=>2,20=>3,21=>4,22=>5,23=>6,30=>7,40=>8,50=>9,60=>10,70=>11,90=>12,91=>13,92=>14,93=>15,94=>16,95=>17,96=>18,97=>19,98=>20,99=>21,100=>22,101=>23,102=>24,103=>25,104=>26,105=>27,106=>28,107=>29,108=>30,109=>31,110=>32,111=>33,112=>34,113=>35,114=>36,115=>37,116=>38,117=>39,118=>40,119=>41
		);

		$recipe_id=str_replace("'","",$txt_recipe_no);
		//echo "select a.requ_no,a.sub_process,a.product_id,a.required_qnty,a.req_qny_edit,a.seq_no,machine_id from dyes_chem_issue_requ_dtls a , dyes_chem_issue_requ_mst b where a.mst_id=b.id and id=$update_id";
		$sql_batch=sql_select("select a.batch_against,a.id as batch_id from pro_batch_create_mst a,pro_recipe_entry_mst b where a.id=b.batch_id and b.id in($recipe_id) and a.status_active=1 and b.status_active=1");
		foreach($sql_batch as $row){
			$batch_dataArr[$row[csf('batch_id')]]=$row[csf('batch_against')];
		}

		/*$sql=sql_select("SELECT a.requ_no,a.sub_process,a.product_id,a.required_qnty,a.req_qny_edit,a.seq_no,a.item_category,b.machine_id,c.batch_against from dyes_chem_issue_requ_dtls a , dyes_chem_issue_requ_mst b,pro_batch_create_mst c where a.mst_id=b.id and a.batch_id   like ('%' || to_char(c.id) || '%')  and b.batch_id like ('%' || to_char(c.id) || '%') and b.id=$update_id and a.required_qnty > 0    and c.batch_against>0 and a.product_id > 0 group by a.requ_no,a.sub_process,a.product_id,a.required_qnty,a.req_qny_edit,a.seq_no,a.item_category,b.machine_id,c.batch_against");*/

		//$sql=sql_select("SELECT a.requ_no,a.sub_process,a.product_id,a.required_qnty,a.req_qny_edit,a.seq_no,a.item_category,b.batch_id,b.machine_id from dyes_chem_issue_requ_dtls a , dyes_chem_issue_requ_mst b,pro_batch_create_mst c where a.mst_id=b.id and b.id=$update_id and a.required_qnty > 0   and a.product_id > 0 and a.status_active=1 and b.status_active=1 group by a.requ_no,a.sub_process,a.product_id,a.required_qnty,a.req_qny_edit,a.seq_no,a.item_category,b.machine_id,b.batch_id");

		$sql=sql_select("select min(y.id) as product_id, x.requ_no,x.sub_process,x.required_qnty,x.req_qny_edit,x.seq_no,x.item_category,x.batch_id,x.machine_id
		from(
		SELECT d.id as product_id,a.requ_no,a.sub_process,a.required_qnty,a.req_qny_edit,a.seq_no,a.item_category,b.batch_id,b.machine_id,
		d.company_id, d.item_category_id, d.item_group_id, d.sub_group_name, d.item_description, d.item_size, d.model, d.item_number, d.item_code
		from product_details_master d, dyes_chem_issue_requ_dtls a , dyes_chem_issue_requ_mst b
		where d.id=a.product_id and a.mst_id=b.id  and b.id=$update_id and a.required_qnty > 0  and d.item_category_id in(5,6,7,23) and a.product_id > 0 and a.status_active=1 and b.status_active=1
		group by d.id, a.requ_no,a.sub_process,a.required_qnty,a.req_qny_edit,a.seq_no,a.item_category,b.machine_id,b.batch_id,d.company_id, d.item_category_id, d.item_group_id, d.sub_group_name, d.ITEM_DESCRIPTION, d.item_size, d.model, d.item_number, d.ITEM_CODE
		) x, product_details_master y
		where x.company_id=y.company_id and x.item_category_id=y.item_category_id and x.item_group_id=y.item_group_id and nvl(x.sub_group_name,0)=nvl(y.sub_group_name,0) and x.item_description=y.item_description and nvl(x.item_size,0)=nvl(y.item_size,0) and nvl(x.model,0)=nvl(y.model,0) and nvl(x.item_number,0)=nvl(y.item_number,0) and nvl(x.item_code,0)=nvl(y.item_code,0)
		group by x.requ_no,x.sub_process,x.required_qnty,x.req_qny_edit,x.seq_no,x.item_category,x.batch_id,x.machine_id");

        $i=1;
		$datas=array();
		foreach($sql as $sqlRow){
			$required_qnty=number_format($sqlRow[csf('required_qnty')],3,".","");
			$type=0;
			/*if($sqlRow[csf('batch_against')]==2){
				$type=1;
			}*/
			$batch_ids=array_unique(explode(",",$sqlRow[csf('batch_id')]));
			$batch_against_id=0;
			foreach($batch_ids as $bid)
			{
				$batch_against_id=$batch_dataArr[$bid];
				if($batch_against_id==2){
				$type=1;
				}
			}

			$itemCategory=0;
			if($sqlRow[csf('item_category')]==6){
				$itemCategory=1;
			}
			$data=str_pad($dyeing_sub_process_map[$sqlRow[csf('sub_process')]],2,"0",STR_PAD_LEFT)."".str_pad($sqlRow[csf('product_id')],8," ",STR_PAD_RIGHT)."".str_pad($required_qnty,10," ",STR_PAD_LEFT)."".str_pad($sqlRow[csf('machine_id')],6," ",STR_PAD_RIGHT).$type;

			$datas[]=$data;

		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		if($i==1){
			$rID_de1=execute_query( "delete from dispensing_import where  requisition_no =$txt_mrr_no",0);
		}
			$field_array = "requisition_no,data_sequence,data,item_category";
			$data_array = "('" . $sqlRow[csf('requ_no')]. "','" . $sqlRow[csf('seq_no')]. "','" . $data . "','" . $itemCategory . "')";
			$rID = sql_insert("dispensing_import", $field_array, $data_array, 1);
			//echo "10**insert into dispensing_import (".$field_array.") values ".$data_array;die;
			//die;
			if ($db_type == 0) {
				if ($rID) {
					mysql_query("COMMIT");
					echo "0**";
				} else {
					mysql_query("ROLLBACK");
					echo "10**";
				}
			}
			else if ($db_type == 2 || $db_type == 1) {
				if ($rID) {
					oci_commit($con);
					echo "0**";
				} else {
					oci_rollback($con);
					echo "10**";
				}
			}
           disconnect($con);
		   $i++;
		}
		die;

		//print_r($datas);





		/*if (str_replace("'", "", $update_id) == "") {
			$rID = sql_insert("dyes_chem_issue_requ_mst", $field_array, $data_array, 1);
		} else {
			$rID = sql_update("dyes_chem_issue_requ_mst", $field_array_up, $data_array_up, "id", $update_id, 1);
		}

		$rID_att = sql_insert("dyes_chem_requ_recipe_att", $field_array_att, $data_array_att, 1);
		$rID_dtls = sql_insert("dyes_chem_issue_requ_dtls", $field_array_dtls, $data_array_dtls, 1);
		if ($db_type == 0) {
			if ($rID && $rID_att && $rID_dtls) {
				mysql_query("COMMIT");
				echo "0**" . $requ_no . "**" . $mst_id;
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . $requ_no . "**" . $mst_id;
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID_att && $rID_dtls) {
				oci_commit($con);
				echo "0**" . $requ_no . "**" . $mst_id;
			} else {
				oci_rollback($con);
				echo "10**" . $requ_no . "**" . $mst_id;
			}
		}
		disconnect($con);*/
		die;
	}
}

if ($action == "report_artexport_text_file_bk") // 06/03/2022 for issue id 4272
{
	// var_dump($_REQUEST);
	$data = explode("***", $data);
	$update_id=$data[0];
	$recipe_no=$data[1];
	$batch_no=$data[2];
	$batch_id=$data[3];
	header('Content-Type: text/csv; charset=utf-8');

	$machine_no_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');
	$sqlRes="SELECT a.id, b.batch_id,b.recipe_id, a.requ_no,a.sub_process,a.product_id,a.required_qnty,a.req_qny_edit,a.seq_no,a.item_category,b.machine_id,c.item_description as fab_desc,c.unit_of_measure ,c.item_code from dyes_chem_issue_requ_dtls a , dyes_chem_issue_requ_mst b,product_details_master c where a.mst_id=b.id  and c.id=a.product_id  and b.id=$update_id and a.required_qnty > 0   and a.product_id > 0   group by a.id, b.batch_id,b.recipe_id, a.requ_no,a.sub_process,a.product_id,a.required_qnty,a.req_qny_edit,a.seq_no,a.item_category,c.item_description,b.machine_id,c.unit_of_measure,c.item_code order by a.sub_process,a.seq_no "; //and a.item_category in(6)
	$sqlResult=sql_select($sqlRes);
	$requ_no=$sqlResult[0][csf("requ_no")];
	$requ_no=explode("-",$requ_no);
	$requ_noA=$requ_no[0].'-'.$requ_no[1].'-'.$requ_no[2].'-'.$requ_no[3];
	/*$all_prod_id_array=array();
	$all_sub_process_array=array();
	foreach($sqlResult as $v)
	{
		$all_prod_id_array[$v[csf("product_id")]]=$v[csf("product_id")];
		$all_sub_process_array[$v[csf("sub_process")]]=$v[csf("sub_process")];
	}
	$all_prod_ids=implode(",", $all_prod_id_array);
	$batch_ids=$sqlResult[0][csf("batch_id")];
	//echo $batch_ids.'DSDSDSD';
	$recipe_ids=$sqlResult[0][csf("recipe_id")];
	$machine_ids=$sqlResult[0][csf("machine_id")];
	$requ_no=$sqlResult[0][csf("requ_no")];
	$requ_no=explode("-",$requ_no);
	$requ_noA=$requ_no[0].'-'.$requ_no[1].'-'.$requ_no[2].'-'.$requ_no[3];
	if(!$machine_ids)$machine_ids=0;
	if(!$all_prod_ids)$all_prod_ids=0;
	if(!$recipe_ids)$recipe_ids=0;
	//echo "SELECT id,batch_no from  pro_batch_create_mst where id in($batch_ids) and  status_active=1 ";
	$batch_no_lib=return_library_array("SELECT id,batch_no from  pro_batch_create_mst where id in($batch_ids) and  status_active=1 ", "id", "batch_no");

	$batch_nos=implode(",", $batch_no_lib);
	//echo $batch_nos;
	$batch_weight=implode(",", $batch_weight_lib);
	$machine_sql=sql_select("SELECT id,machine_no,machine_group from  lib_machine_name where id in($machine_ids) and  status_active=1 ");
	if($machine_no) $machine_no=$machine_no;else $machine_no=0;*/
	$i = 1;
	$year = date("y");
	$path = $requ_noA.'_'.$user_id.'.txt';
	$myfile = fopen($path, "w") or die("Unable to open file!");
	$txt = "";
	$sub_process_chk=array();
	$step=0;
	foreach($sqlResult as $sqlRow)
	{
		/*$sub_process=$sqlRow[csf('sub_process')];
		if (in_array($sub_process, $sub_process_chk))
		{
		  	$step=$step;
		}
		else
		{
			$step++;
		  	$sub_process_chk[$sqlRow[csf('sub_process')]]=$sqlRow[csf('sub_process')];
		}
		$seq_no=$step;*/
		$seq_no=$sqlRow[csf('seq_no')];
		//$product_id=$sqlRow[csf('product_id')];
		$product_id=$sqlRow[csf('item_code')];
		$required_qnty=$sqlRow[csf('required_qnty')];
		$machine_id=$sqlRow[csf('machine_id')];
		if($seq_no=='') $seqNo=''; else $seqNo=$seq_no;
		if($product_id=='') $productId=''; else $productId=$product_id;
		if($required_qnty=='') $requiredQnty=''; else $requiredQnty=$required_qnty;
		//if($machine_id=='') $machineId=''; else $machineId=$machine_no_arr[$machine_id];
		$machineId='';
		$seqNo=str_pad($seqNo,2,"0",STR_PAD_LEFT);
		$productId=str_pad($productId,8," ",STR_PAD_RIGHT);
		$reqQnty=explode('.', $requiredQnty);
		if($reqQnty[0]=='') $reqbeforeDecQnty=0; else $reqbeforeDecQnty=$reqQnty[0];
		//$a=strval($reqQnty[1]);
		$aftDecQnty=substr(strval($reqQnty[1]), 0, 3);
		$reqbefDecQnty=str_pad($reqbeforeDecQnty,6," ",STR_PAD_LEFT);
		$reqAftDecQnty=str_pad($aftDecQnty,3,"0",STR_PAD_LEFT);
		$machineId=str_pad($machineId,6," ",STR_PAD_LEFT);
		$type=0;

		//$txt .= $batch_nos.';'.$machine_no.';'.$item_category_val .';'.$Req_amount.','.$Req_amount_fraction.';'.$sqlRow[csf('seq_no')] . "\r\n";
		//$txt .= '00'.$product_id.' '.$Req_amount.'.'.$Req_amount_fraction.'00 0'."\r\n";
		$txt .= $seqNo.''.$productId.''.$reqbefDecQnty.'.'.$reqAftDecQnty.''.$machineId.''.$type."\r\n";

		$i++;
	}

	fwrite($myfile, $txt);
	rewind($myfile);   // reset file pointer so as output file from the beginning

	// `basename` and `filesize` accept path to file, not file descriptor
	header('Cache-Control: public');
	header('Content-Description: File Transfer');
	header('Content-Disposition: attachment; filename='.$myfile);
	header('Content-Type: text/plain');
	header('Content-Transfer-Encoding: binary');

	foreach (glob($user_id."*.txt") as $filename)
	{
		@unlink($filename);
	}
	echo $path;
	//echo $requ_noA."_".$user_id; //str_replace(".zip","",$file_folder);//"norsel_bundle";
	exit();
}

if ($action == "report_artexport_text_file")
{
	// var_dump($_REQUEST);
	$data = explode("***", $data);
	$update_id=$data[0];
	$recipe_no=$data[1];
	$batch_no=$data[2];
	$batch_id=$data[3];
	header('Content-Type: text/csv; charset=utf-8');

	$machine_no_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');
	$sqlRes="SELECT a.id, b.batch_id,b.recipe_id, a.requ_no,a.sub_process,a.product_id,a.required_qnty,a.req_qny_edit,a.sub_seq,a.seq_no,a.item_category,b.machine_id,c.item_description as fab_desc,c.unit_of_measure,c.item_code  from dyes_chem_issue_requ_dtls a , dyes_chem_issue_requ_mst b,product_details_master c where a.mst_id=b.id  and c.id=a.product_id  and b.id=$update_id and a.required_qnty > 0   and a.product_id > 0   group by a.id, b.batch_id,b.recipe_id, a.requ_no,a.sub_process,a.product_id,a.required_qnty,a.req_qny_edit,a.sub_seq,a.seq_no,a.item_category,c.item_description,b.machine_id,c.unit_of_measure,c.item_code order by a.sub_seq,a.seq_no "; //and a.item_category in(6)  a.sub_process,
	$sqlResult=sql_select($sqlRes);
	$requ_no=$sqlResult[0][csf("requ_no")];
	$requ_no=explode("-",$requ_no);
	$requ_noA=$requ_no[0].'-'.$requ_no[1].'-'.$requ_no[2].'-'.$requ_no[3];
	/*$all_prod_id_array=array();
	$all_sub_process_array=array();
	foreach($sqlResult as $v)
	{
		$all_prod_id_array[$v[csf("product_id")]]=$v[csf("product_id")];
		$all_sub_process_array[$v[csf("sub_process")]]=$v[csf("sub_process")];
	}
	$all_prod_ids=implode(",", $all_prod_id_array);
	$batch_ids=$sqlResult[0][csf("batch_id")];
	//echo $batch_ids.'DSDSDSD';
	$recipe_ids=$sqlResult[0][csf("recipe_id")];
	$machine_ids=$sqlResult[0][csf("machine_id")];
	$requ_no=$sqlResult[0][csf("requ_no")];
	$requ_no=explode("-",$requ_no);
	$requ_noA=$requ_no[0].'-'.$requ_no[1].'-'.$requ_no[2].'-'.$requ_no[3];
	if(!$machine_ids)$machine_ids=0;
	if(!$all_prod_ids)$all_prod_ids=0;
	if(!$recipe_ids)$recipe_ids=0;
	//echo "SELECT id,batch_no from  pro_batch_create_mst where id in($batch_ids) and  status_active=1 ";
	$batch_no_lib=return_library_array("SELECT id,batch_no from  pro_batch_create_mst where id in($batch_ids) and  status_active=1 ", "id", "batch_no");

	$batch_nos=implode(",", $batch_no_lib);
	//echo $batch_nos;
	$batch_weight=implode(",", $batch_weight_lib);
	$machine_sql=sql_select("SELECT id,machine_no,machine_group from  lib_machine_name where id in($machine_ids) and  status_active=1 ");
	if($machine_no) $machine_no=$machine_no;else $machine_no=0;*/
	$i = 1;


	$f_path = $requ_noA;
	foreach (glob("*.txt") as $filename)
	{
		@unlink($filename);
	}

	$year = date("y");
	// $path = $requ_noA.'_'.$user_id.'.txt';
	$path = $requ_noA.'.txt';
	$myfile = fopen($path, "w") or die("Unable to open file!");
	$txt = "";
	$sub_process_chk=array();
	$step=0;
	foreach($sqlResult as $sqlRow)
	{
		/*$sub_process=$sqlRow[csf('sub_process')];
		if (in_array($sub_process, $sub_process_chk))
		{
		  	$step=$step;
		}
		else
		{
			$step++;
		  	$sub_process_chk[$sqlRow[csf('sub_process')]]=$sqlRow[csf('sub_process')];
		}
		$seq_no=$step;*/
		$seq_no=$sqlRow[csf('sub_seq')];
		//$product_id=$sqlRow[csf('product_id')];
		$product_id=$sqlRow[csf('item_code')];
		$required_qnty=$sqlRow[csf('required_qnty')]*1000;
		$machine_id=$sqlRow[csf('machine_id')];
		if($seq_no=='') $seqNo=''; else $seqNo=$seq_no;
		if($product_id=='') $productId=''; else $productId=$product_id;
		if($required_qnty=='') $requiredQnty=''; else $requiredQnty=$required_qnty;
		//if($machine_id=='') $machineId=''; else $machineId=$machine_no_arr[$machine_id];
		$machineId='';
		$seqNo=str_pad($seqNo,2,"0",STR_PAD_LEFT);
		$productId=str_pad($productId,8," ",STR_PAD_RIGHT);
		$reqQnty=explode('.', $requiredQnty);
		if($reqQnty[0]=='') $reqbeforeDecQnty=0; else $reqbeforeDecQnty=$reqQnty[0];
		//$a=strval($reqQnty[1]);
		$aftDecQnty=substr(strval($reqQnty[1]), 0, 3);
		$reqbefDecQnty=str_pad($reqbeforeDecQnty,6," ",STR_PAD_LEFT);
		$reqAftDecQnty=str_pad($aftDecQnty,3,"0",STR_PAD_RIGHT);
		$machineId=str_pad($machineId,6," ",STR_PAD_BOTH);
		$type=0;

		//$txt .= $batch_nos.';'.$machine_no.';'.$item_category_val .';'.$Req_amount.','.$Req_amount_fraction.';'.$sqlRow[csf('seq_no')] . "\r\n";
		//$txt .= '00'.$product_id.' '.$Req_amount.'.'.$Req_amount_fraction.'00 0'."\r\n";
		$txt .= $seqNo.''.$productId.''.$reqbefDecQnty.'.'.$reqAftDecQnty.''.$machineId.''.$type."\r\n";

		$i++;
	}

	fwrite($myfile, $txt);
	rewind($myfile);   // reset file pointer so as output file from the beginning

	// `basename` and `filesize` accept path to file, not file descriptor
	header('Cache-Control: public');
	header('Content-Description: File Transfer');
	header('Content-Disposition: attachment; filename='.$myfile);
	header('Content-Type: text/plain');
	header('Content-Transfer-Encoding: binary');

	/*foreach (glob($f_path."*.txt") as $filename)
	{
		@unlink($filename);
	}*/
	echo $path;
	//echo $requ_noA."_".$user_id; //str_replace(".zip","",$file_folder);//"norsel_bundle";
	exit();
}
if ($action == "report_artexport2_text_file")
{
	// var_dump($_REQUEST);
	$data = explode("***", $data);
	$update_id=$data[0];
	$recipe_no=$data[1];
	$batch_no=$data[2];
	$batch_id=$data[3];
	header('Content-Type: text/csv; charset=utf-8');

	$machine_no_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');
	  $sqlRes="SELECT a.id, b.batch_id,b.recipe_id, a.requ_no,a.sub_process,a.product_id,a.required_qnty,a.req_qny_edit,a.sub_seq,a.seq_no,a.item_category,b.machine_id,c.item_description as fab_desc,c.unit_of_measure,c.item_code  from dyes_chem_issue_requ_dtls a , dyes_chem_issue_requ_mst b,product_details_master c where a.mst_id=b.id  and c.id=a.product_id  and b.id=$update_id and a.required_qnty > 0   and a.product_id > 0   group by a.id, b.batch_id,b.recipe_id, a.requ_no,a.sub_process,a.product_id,a.required_qnty,a.req_qny_edit,a.sub_seq,a.seq_no,a.item_category,c.item_description,b.machine_id,c.unit_of_measure,c.item_code order by a.sub_seq,a.seq_no "; //and a.item_category in(6)  a.sub_process,
	$sqlResult=sql_select($sqlRes);
	$requ_no=$sqlResult[0][csf("requ_no")];
	$requ_no=explode("-",$requ_no);
	$requ_noA=$requ_no[0].'-'.$requ_no[1].'-'.$requ_no[2].'-'.$requ_no[3];
	/*$all_prod_id_array=array();
	$all_sub_process_array=array();
	foreach($sqlResult as $v)
	{
		$all_prod_id_array[$v[csf("product_id")]]=$v[csf("product_id")];
		$all_sub_process_array[$v[csf("sub_process")]]=$v[csf("sub_process")];
	}
	$all_prod_ids=implode(",", $all_prod_id_array);
	$batch_ids=$sqlResult[0][csf("batch_id")];
	//echo $batch_ids.'DSDSDSD';
	$recipe_ids=$sqlResult[0][csf("recipe_id")];
	$machine_ids=$sqlResult[0][csf("machine_id")];
	$requ_no=$sqlResult[0][csf("requ_no")];
	$requ_no=explode("-",$requ_no);
	$requ_noA=$requ_no[0].'-'.$requ_no[1].'-'.$requ_no[2].'-'.$requ_no[3];
	if(!$machine_ids)$machine_ids=0;
	if(!$all_prod_ids)$all_prod_ids=0;
	if(!$recipe_ids)$recipe_ids=0;
	//echo "SELECT id,batch_no from  pro_batch_create_mst where id in($batch_ids) and  status_active=1 ";
	$batch_no_lib=return_library_array("SELECT id,batch_no from  pro_batch_create_mst where id in($batch_ids) and  status_active=1 ", "id", "batch_no");

	$batch_nos=implode(",", $batch_no_lib);
	//echo $batch_nos;
	$batch_weight=implode(",", $batch_weight_lib);
	$machine_sql=sql_select("SELECT id,machine_no,machine_group from  lib_machine_name where id in($machine_ids) and  status_active=1 ");
	if($machine_no) $machine_no=$machine_no;else $machine_no=0;*/
	$i = 1;


	$f_path = $requ_noA;
	foreach (glob("*.txt") as $filename)
	{
		@unlink($filename);
	}

	$year = date("y");
	// $path = $requ_noA.'_'.$user_id.'.txt';
	$path = $requ_noA.'.txt';
	$myfile = fopen($path, "w") or die("Unable to open file!");
	$txt = "";
	$sub_process_chk=array();
	$step=0;
	foreach($sqlResult as $sqlRow)
	{
		/*$sub_process=$sqlRow[csf('sub_process')];
		if (in_array($sub_process, $sub_process_chk))
		{
		  	$step=$step;
		}
		else
		{
			$step++;
		  	$sub_process_chk[$sqlRow[csf('sub_process')]]=$sqlRow[csf('sub_process')];
		}
		$seq_no=$step;*/
		$seq_no=$sqlRow[csf('sub_seq')];
		//$product_id=$sqlRow[csf('product_id')];
		$product_id=$sqlRow[csf('item_code')];
		$required_qnty=$sqlRow[csf('required_qnty')]*1000;
		$machine_id=$sqlRow[csf('machine_id')];
		if($seq_no=='') $seqNo=''; else $seqNo=$seq_no;
		if($product_id=='') $productId=''; else $productId=$product_id;
		if($required_qnty=='') $requiredQnty=''; else $requiredQnty=$required_qnty;
		 if($machine_id=='') $machineId=''; else $machineId=$machine_no_arr[$machine_id];
		//$machineId='';
		$seqNo=str_pad($seqNo,2,"0",STR_PAD_LEFT);
		$productId=str_pad($productId,8," ",STR_PAD_RIGHT);
		$reqQnty=explode('.', $requiredQnty);
		if($reqQnty[0]=='') $reqbeforeDecQnty=0; else $reqbeforeDecQnty=$reqQnty[0];
		//$a=strval($reqQnty[1]);
		$aftDecQnty=substr(strval($reqQnty[1]), 0, 3);
		$reqbefDecQnty=str_pad($reqbeforeDecQnty,6," ",STR_PAD_LEFT);
		$reqAftDecQnty=str_pad($aftDecQnty,3,"0",STR_PAD_RIGHT);
		//$machineId=str_pad($machineId,6," ",STR_PAD_BOTH);
		$type=0;

		//$txt .= $batch_nos.';'.$machine_no.';'.$item_category_val .';'.$Req_amount.','.$Req_amount_fraction.';'.$sqlRow[csf('seq_no')] . "\r\n";
		//$txt .= '00'.$product_id.' '.$Req_amount.'.'.$Req_amount_fraction.'00 0'."\r\n";
		$txt .= $seqNo.''.$productId.''.$reqbefDecQnty.'.'.$reqAftDecQnty.' '.$machineId.''.$type."\r\n";

		$i++;
	}

	fwrite($myfile, $txt);
	rewind($myfile);   // reset file pointer so as output file from the beginning

	// `basename` and `filesize` accept path to file, not file descriptor
	header('Cache-Control: public');
	header('Content-Description: File Transfer');
	header('Content-Disposition: attachment; filename='.$myfile);
	header('Content-Type: text/plain');
	header('Content-Transfer-Encoding: binary');

	/*foreach (glob($f_path."*.txt") as $filename)
	{
		@unlink($filename);
	}*/
	echo $path;
	//echo $requ_noA."_".$user_id; //str_replace(".zip","",$file_folder);//"norsel_bundle";
	exit();
}


if ($action == "report_artexport_text_file_old")
{
	// var_dump($_REQUEST);
	$data = explode("***", $data);
	$update_id=$data[0];
	$recipe_no=$data[1];
	$batch_no=$data[2];
	$batch_id=$data[3];

	/*$content = "some text here";
	$fp = fopen($_SERVER['DOCUMENT_ROOT'] . "inventory/chemical_dyes/requires/myText.txt","wb");
	fwrite($fp,$content);
	fclose($fp);


	echo "myText";*/
	header('Content-Type: text/csv; charset=utf-8');

	$machine_no_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');
	$machine_brand_arr = return_library_array("select id, brand from lib_machine_name", 'id', 'brand'); // Temporary

	$sqlRes="SELECT a.id, b.batch_id,b.recipe_id, a.requ_no,a.sub_process,a.product_id,a.required_qnty,a.req_qny_edit,a.seq_no,a.item_category,b.machine_id,c.item_description as fab_desc,c.unit_of_measure  from dyes_chem_issue_requ_dtls a , dyes_chem_issue_requ_mst b,product_details_master c where a.mst_id=b.id  and c.id=a.product_id  and b.id=$update_id and a.required_qnty > 0   and a.product_id > 0   group by a.id, b.batch_id,b.recipe_id, a.requ_no,a.sub_process,a.product_id,a.required_qnty,a.req_qny_edit,a.seq_no,a.item_category,c.item_description,b.machine_id,c.unit_of_measure order by a.sub_process,a.seq_no "; //and a.item_category in(6)
	$sqlResult=sql_select($sqlRes);

		$all_prod_id_array=array();
		$all_sub_process_array=array();
		foreach($sqlResult as $v)
		{
			$all_prod_id_array[$v[csf("product_id")]]=$v[csf("product_id")];
			$all_sub_process_array[$v[csf("sub_process")]]=$v[csf("sub_process")];
		}
		$all_prod_ids=implode(",", $all_prod_id_array);
		$batch_ids=$sqlResult[0][csf("batch_id")];
		//echo $batch_ids.'DSDSDSD';
		$recipe_ids=$sqlResult[0][csf("recipe_id")];
		$machine_ids=$sqlResult[0][csf("machine_id")];
		$requ_no=$sqlResult[0][csf("requ_no")];
		$requ_no=explode("-",$requ_no);
		$requ_noA=$requ_no[0].'-'.$requ_no[1].'-'.$requ_no[2].'-'.$requ_no[3];
		if(!$machine_ids)$machine_ids=0;
		if(!$all_prod_ids)$all_prod_ids=0;
		if(!$recipe_ids)$recipe_ids=0;
		//echo "SELECT id,batch_no from  pro_batch_create_mst where id in($batch_ids) and  status_active=1 ";
		$batch_no_lib=return_library_array("SELECT id,batch_no from  pro_batch_create_mst where id in($batch_ids) and  status_active=1 ", "id", "batch_no");

		$batch_nos=implode(",", $batch_no_lib);
		//echo $batch_nos;
		$batch_weight=implode(",", $batch_weight_lib);
		$machine_sql=sql_select("SELECT id,machine_no,machine_group from  lib_machine_name where id in($machine_ids) and  status_active=1 ");
		//echo "SELECT id,machine_no,machine_group from  lib_machine_name where id in($machine_ids) and  status_active=1 ";die;
		//echo "SELECT id,machine_no,machine_group from  lib_machine_name where id in($machine_ids) and  status_active=1 ";
		$machine_no=$machine_sql[0][csf("machine_no")];
		//$machine_group=$machine_sql[0][csf("machine_group")];
		//$mc_name=$machine_no.'-'.$machine_group;
		if($machine_no) $machine_no=$machine_no;else $machine_no=0;

	/*foreach (glob($batch_nos.'_'.$user_id."*.txt") as $filename) {
		@unlink($filename);
	}*/
	//echo $within_group;die;
	//exit;
	$i = 1;
	/*$zip = new ZipArchive();            // Load zip library
	//$filename = str_replace(".sql", ".zip", "files/".$_SESSION['logic_erp']['user_id']."/norsel_bundle.sql");            // Zip name
	$filename = str_replace(".sql",".zip",$batch_nos.'_'.$user_id.".sql");			// Zip name
	if ($zip->open($filename, ZIPARCHIVE::CREATE) !== TRUE) {        // Opening zip file to load files
		$error .= "* Sorry ZIP creation failed at this time<br/>";
		echo $error;
	}*/

	$i = 1;
	$year = date("y");

	//$created_files=array();

		//$file_name =$user_id."_";
	//	$created_files[]=$file_name.".txt";
		//$myfile = fopen($file_name . ".txt", "w") or die("Unable to open file!");

		$path = $requ_noA.'_'.$user_id.'.txt';
		$myfile = fopen($path, "w") or die("Unable to open file!");
		//$txt="lorem ipsusdsdm ds";



		//echo $file_name.'d';;die;
		$txt = "";
		foreach($sqlResult as $sqlRow)
		{

			$itemCategory=0;
			if($sqlRow[csf('item_category')]==6)
			{
				$itemCategory=1;
			}
			$item_category_val=strtoupper($item_category[$sqlRow[csf('item_category')]]);
			//if($sqlRow[csf('item_category')]==5) $item_category_val="CHEMICAL";
			//if($sqlRow[csf('item_category')]==6)
			$item_category_val=trim($sqlRow[csf('fab_desc')]);
			$uom_id=$sqlRow[csf('unit_of_measure')];
			if($uom_id==12) //KG
			{
				$required_qnty=$sqlRow[csf('required_qnty')]*1000;
			}
			else if($uom_id==40) //Ltr
			{
				$required_qnty=$sqlRow[csf('required_qnty')]*1000;
			}
			else if($uom_id==15) //LBS
			{
				$lbs=453.592369999995;
				$required_qnty=$sqlRow[csf('required_qnty')]*$lbs;
			}
			else
			{
				$required_qnty=$sqlRow[csf('required_qnty')]*1000;
			}



		$product_id=$sqlRow[csf('product_id')];
		$Req_amountArr=explode(".",$required_qnty);
		$Req_amount=$Req_amountArr[0];
		if($Req_amount) $Req_amount=$Req_amount;else $Req_amount=0;
		$Req_amount_fraction=$Req_amountArr[1];
		if($Req_amount_fraction=='') $Req_amount_fraction=0;

		//$txt .= $batch_nos.';'.$machine_no.';'.$item_category_val .';'.$Req_amount.','.$Req_amount_fraction.';'.$sqlRow[csf('seq_no')] . "\r\n";
		$txt .= '00'.$product_id.' '.$Req_amount.'.'.$Req_amount_fraction.'00 0'."\r\n";

		$i++;
	}

		fwrite($myfile, $txt);
		rewind($myfile);   // reset file pointer so as output file from the beginning

		// `basename` and `filesize` accept path to file, not file descriptor
		header('Cache-Control: public');
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename='.$myfile);
		header('Content-Type: text/plain');
		header('Content-Transfer-Encoding: binary');
		//readfile($file);

		//fpassthru($file);



	//echo $txt;die;  $file_name = "NORSEL-IMPORT_".$userid."_" . $i;
	/*foreach (glob($user_id."*.txt") as $filenames)
	{
		$zip->addFile($file_folder.$filenames);
	}
	$zip->close();*/

	foreach (glob($user_id."*.txt") as $filename)
	{
		@unlink($filename);
	}
	echo $path;
	//echo $requ_noA."_".$user_id; //str_replace(".zip","",$file_folder);//"norsel_bundle";
	exit();
}


if ($action == "chemical_dyes_issue_requisition_print")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	// print_r ($data[5])."**";
	//  print_r ($data);
	if ($data[3]==4) // Print report 1
	{
		$sql = "select a.id, a.requ_no, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id from dyes_chem_issue_requ_mst a where a.id='$data[1]' and a.company_id='$data[0]'";
		$dataArray = sql_select($sql);
		$recipe_id = $dataArray[0][csf('recipe_id')];
		$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
		$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
		$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
		$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
		//$entry_form_arr = return_library_array("select id, entry_form from pro_batch_create_mst", "id", "entry_form");
		//$order_id_arr=return_library_array( "select id, booking_id from  pro_recipe_entry_mst", "id", "booking_id");
		//$order_no_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
		//$job_no_arr=return_library_array( "select id, job_no from wo_booking_mst", "id", "job_no");
		$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
		$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
		$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");

		$sales_order_result = sql_select("select id,job_no,within_group,sales_booking_no,style_ref_no from fabric_sales_order_mst where status_active=1 and is_deleted=0");
		$sales_arr = array();
		foreach ($sales_order_result as $sales_row)
		{
			$sales_arr[$sales_row[csf("id")]]["sales_order_no"] 	= $sales_row[csf("job_no")];
			$sales_arr[$sales_row[csf("id")]]["sales_booking_no"] 	= $sales_row[csf("sales_booking_no")];
			$sales_arr[$sales_row[csf("id")]]["style_ref_no"] 	    = $sales_row[csf("style_ref_no")];
		}

		$batch_arr_no=sql_select("select id, batch_no from pro_batch_create_mst where batch_no='$data[5]'");
		$batch_id_arr='';
		foreach($batch_arr_no as $v){
			// if($batch_id_arr ==''){$batch_id_arr .= strval($v[csf("id")]);} else {$batch_id_arr .= ",".strval($v[csf("id")]);}
			if($batch_id_arr ==''){$batch_id_arr .= "'".$v[csf("id")]."'";} else {$batch_id_arr .= ",'".$v[csf("id")]."'";}
		}
		$batch_arr_count=sql_select("select id, batch_id from dyes_chem_issue_requ_mst where batch_id in ($batch_id_arr) and status_active=1 and is_deleted=0 " );
		$batch_count= count($batch_arr_count);

		$batch_weight = 0;
		if ($db_type == 0) {
			$data_array = sql_select("select group_concat(buyer_id) as buyer_id,group_concat(id) as recipe_id, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor, group_concat(concat_ws(':',batch_ratio,liquor_ratio) order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, group_concat(case when entry_form<>60 then batch_id end) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
		} else {
			$data_array = sql_select("select listagg(buyer_id,',') within group (order by buyer_id) as buyer_id,listagg(id,',') within group (order by id) as recipe_id, listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor, listagg(batch_ratio || ':' || liquor_ratio,',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(case when entry_form<>60 then batch_id end,',') within group (order by batch_id) as batch_id_rec_main, listagg(case when entry_form<>60 then labdip_no end,',') within group (order by labdip_no) as labdip_no from pro_recipe_entry_mst where id in($recipe_id)");
		}

		$batch_weight = $data_array[0][csf("batch_weight")];
		$batch_id = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id")])));

		$batch_id_rec_main = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id_rec_main")])));
		if ($batch_id_rec_main == "") $batch_id_rec_main = 0;

		if ($db_type == 0) {
			$batchdata_array = sql_select("select group_concat(batch_no) as batch_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(distinct color_id) as color_id from pro_batch_create_mst where id in($batch_id)");
		} else {
			$batchdata_array = sql_select("select listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, listagg(color_id ,',') within group (order by id) as color_id, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($batch_id)");
		}

		$entry_form_arr = return_library_array("select id, entry_form from pro_batch_create_mst where id in($batch_id)", "id", "entry_form");


		$po_no = '';
		$job_no = '';
		$buyer_name = '';
		$style_ref_no = '';
		foreach (explode(",", $data_array[0][csf("batch_id")]) as $b_id) {
			if ($entry_form_arr[$b_id] == 36) {
				//echo "select distinct b.order_no,b.cust_style_ref, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(".$b_id.") ";
				$po_data = sql_select("select distinct b.order_no,b.cust_style_ref, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ");
				foreach ($po_data as $row) {
					$po_no .= $row[csf('order_no')] . ",";
					$job_no .= $row[csf('subcon_job')] . ",";
					if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
					$buyer_name .= $buyer_library[$row[csf('party_id')]] . ",";
				}
			} else {
				// echo "select distinct a.is_sales,a.po_id, b.po_number, c.job_no,c.style_ref_no, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ";
				$po_data = sql_select("select distinct a.is_sales,a.po_id, b.po_number, c.job_no,c.style_ref_no, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ");
				$job_nos="";
				foreach ($po_data as $row) {
					if($row[csf("is_sales")] == 1)
					{
						$po_no .= $sales_arr[$row[csf("po_id")]]["sales_order_no"] . ",";
						$job_no .= $sales_arr[$row[csf("po_id")]]["sales_booking_no"] . ",";
						//$job_nos .= $row[csf('job_no')] . ",";
						if ($style_ref_no == '') $style_ref_no = $sales_arr[$row[csf("po_id")]]["style_ref_no"]; else $style_ref_no .= "," . $sales_arr[$row[csf("po_id")]]["style_ref_no"];
					}else
					{
						$po_no .= $row[csf('po_number')] . ",";
						$job_no .= $row[csf('job_no')] . ",";
						//$job_nos .= $row[csf('job_no')] . ",";
						if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
					}



					if ($job_nos == '') $job_nos ="'".$row[csf('job_no')]."'"; else $job_nos.= ","."'".$row[csf('job_no')]."'";
					//$buyer_name.=$buyer_library[$row[csf('buyer_name')]].",";
				}
				foreach (explode(",", $data_array[0][csf("buyer_id")]) as $buyer_id) {
					$buyer_name .= $buyer_library[$buyer_id] . ",";
				}
			}
		}
		$jobNos=rtrim($job_nos,',');
		$jobnos=rtrim($job_no,',');

		//echo "select job_no, exchange_rate from wo_pre_cost_mst $job_nos_con";
		if($job_nos!='')
		{
			if($job_nos!='') $job_nos_con="where  job_no in(".$job_nos.")";//else $job_nos_con="where  job_no in('')";
			//echo "select job_no, exchange_rate from wo_pre_cost_mst $job_nos_con";
			$exchange_rate_arr = return_library_array("select job_no, exchange_rate from wo_pre_cost_mst $job_nos_con", 'job_no', 'exchange_rate');
		}
		//print_r($exchange_rate_arr);
		if($job_nos!='')
		{
		$condition= new condition();
		 $condition->company_name("=$data[0]");
		 if(str_replace("'","",$job_nos) !=''){
			  $condition->job_no("in($job_nos)");
		 }
		 $condition->init();

		$conversion= new conversion($condition);
		//echo $conversion->getQuery(); die;
		$conversion_qty_arr_process=$conversion->getQtyArray_by_jobAndProcess();
		$conversion_costing_arr_process=$conversion->getAmountArray_by_jobAndProcess();
		}
		// print_r($conversion_costing_arr_process);
		$dyeing_charge_arr=array(25,26,30,31,32,33,34,39,60,62,63,64,65,66,67,68,69,70,71,82,83,84,85,86,89,90,91,92,93,94,129,135,136,137,138,139,140,141,142,143,146);
		$jobnos=array_unique(explode(",",$jobnos));
		$tot_avg_dyeing_charge=0;$avg_dyeing_charge=0;$tot_dyeing_qty=$tot_dyeing_cost=0;//$tot_dyeing_cost=0;$tot_dyeing_qty=0;
		foreach($jobnos as $job)
		{
			$exchange_rate=$exchange_rate_arr[$job];
			foreach($dyeing_charge_arr as $process_id)
			{
				$dyeing_cost=array_sum($conversion_costing_arr_process[$job][$process_id]);
				//echo $job.'=='.$dyeing_cost.'='.$exchange_rate.'<br/>';
				$totdyeing_cost=$dyeing_cost;
				if($totdyeing_cost>0)
				{
					$tot_dyeing_qty+=array_sum($conversion_qty_arr_process[$job][$process_id]);
					$tot_dyeing_cost+=$dyeing_cost;
					//echo $tot_dyeing_cost.'='.$tot_dyeing_qty.'**'.$exchange_rate.'**'.$process_id.'<br>';
					//$avg_dyeing_charge+=$tot_dyeing_cost/$tot_dyeing_qty;
				}
			}
			//echo $tot_dyeing_cost.'<br>'.$tot_dyeing_qty;
			$avg_dyeing_charge+=$tot_dyeing_cost/$tot_dyeing_qty;
			$tot_avg_dyeing_charge+=$avg_dyeing_charge*$exchange_rate;
		}

		//echo $avg_dyeing_charge.'='.$tot_dyeing_cost.'='.$dyeing_cost;
		//echo $style_ref_no;
		/*if ($db_type==0)
		{
			$sql_yarn_dtls="select b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
		}
		elseif ($db_type==2)
		{
			$sql_yarn_dtls="select b.po_breakdown_id, b.prod_id, listagg((cast(a.yarn_lot as varchar2(500))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(a.brand_id,',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(500))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
		}*/

		/*$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, a.yarn_lot as yarn_lot,a.brand_id as brand_id,a.yarn_count  as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and  b.prod_id=p.prod_id  and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
		//echo $sql_yarn_dtls;die;
		$yarn_dtls_array = array();
		$result_sql_yarn_dtls = sql_select($sql_yarn_dtls);
		foreach ($result_sql_yarn_dtls as $row)
		{
			$yarn_lot = $row[csf('yarn_lot')];//implode(", ", array_unique(explode(",", $row[csf('yarn_lot')])));
			$brand_id =$row[csf('brand_id')];// array_unique(explode(",", $row[csf('brand_id')]));
			//$brand_name = "";
			foreach ($brand_id as $val)
			{
				//if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= ", " . $brand_arr[$val];
			}

			$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
			$count_name = "";
			foreach ($yarn_count as $val) {
				if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
			}
			$yarn_dtls_array[$row[csf('prod_id')]]['yarn_lot'].=$yarn_lot.',';
			$yarn_dtls_array[$row[csf('prod_id')]]['brand_name'] .= $brand_arr[$row[csf('brand_id')]].',';
			$yarn_dtls_array[$row[csf('prod_id')]]['count_name'] .= $count_name.',';
		}*/
		//var_dump($yarn_dtls_array);
		$po_no = implode(", ", array_unique(explode(",", substr($po_no, 0, -1))));
		$job_no = implode(", ", array_unique(explode(",", substr($job_no, 0, -1))));
		$buyer_name = implode(",", array_unique(explode(",", substr($buyer_name, 0, -1))));

		$color_id = array_unique(explode(",", $batchdata_array[0][csf('color_id')]));
		$color_name = '';
		foreach ($color_id as $color) {
			$color_name .= $color_arr[$color] . ",";
		}
		$color_name = substr($color_name, 0, -1);
		//var_dump($recipe_color_arr);

		//$recipe_id=$data_array[0][csf("recipe_id")];
		$recipe_arr=sql_select("select a.working_company_id,a.company_id, b.batch_weight from pro_recipe_entry_mst a, pro_batch_create_mst b where a.batch_id=b.id and  a.id in($recipe_id) ");
		$owner_company_id=$recipe_arr[0][csf('company_id')];
		$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
		$group_id=$nameArray[0][csf('group_id')];
		?>
		<div style="width:1250px;">
			<table width="1000" cellspacing="0" align="center">
				<tr>
					<td colspan="10" align="center" style="font-size:xx-large">
						<strong><? echo 'Group Name :'.$group_library[$group_id].'<br/>'.'Working Company : '.$company_library[$data[0]];//$company_library[$data[0]]; ?></strong></td>
					</tr>
					<tr class="form_caption">
						<td colspan="6" align="center" style="font-size:14px">
							<?

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
						<td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo 'Owner Company : '.$company_library[$owner_company_id].'<br>'.$data[2]; ?>
							Report</u></strong></td>
						</tr>
					</table>
					<table width="950" cellspacing="0" align="center">
						<tr>
							<td width="90"><strong>Req. ID </strong></td>
							<td width="160px"><? echo $dataArray[0][csf('requ_no')]; ?></td>
							<td width="100"><strong>Req. Date</strong></td>
							<td width="150px"><? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
							<td width="150"><strong>Buyer</strong></td>
							<td width="160px"><? echo implode(",", array_unique(explode(",", $buyer_name))); ?></td>
						</tr>
						<tr>
							<td><strong>Order No</strong></td>
							<td><? echo $po_no; ?></td>
							<td><strong>Job No</strong></td>
							<td><? echo $job_no; ?></td>
							<td><strong>Issue Basis</strong></td>
							<td><? echo $receive_basis_arr[$dataArray[0][csf('requisition_basis')]]; ?></td>
						</tr>
						<tr>
							<td><strong>Batch No</strong></td>
							<td><? echo $batchdata_array[0][csf('batch_no')]; ?></td>
							<td><strong>Batch Weight</strong></td>
							<td><? $tot_batch_weight=$batch_weight+$batchdata_array[0][csf('batch_weight')];echo number_format($tot_batch_weight,4); ?></td>
							<td><strong>Color</strong></td>
							<td><? echo $color_name; ?></td>
						</tr>
						<tr>
							<td><strong>Recipe No</strong></td>
							<td><? echo $data_array[0][csf("recipe_id")]; ?></td>
							<td><strong>Machine No</strong></td>
							<td>
								<?
								$machine_data = sql_select("select machine_no, floor_id from lib_machine_name where id='" . $dataArray[0][csf("machine_id")] . "'");
								echo $machine_data[0][csf('machine_no')];
								?>
							</td>
	                        <td><strong>Floor Name</strong></td>
							<td colspan="">
								<?
								$floor_name = return_field_value("floor_name", "lib_prod_floor", "id='" . $machine_data[0][csf('floor_id')] . "'");
								echo $floor_name;
								?>
							</td>
						</tr>
						<tr>

							<td><strong> Labdip No</strong></td>
							<td><? echo $data_array[0][csf("labdip_no")]; ?></td>
	                        <td><strong>Style Ref.</strong></td>
							<td ><p>
								<?
								echo implode(",", array_unique(explode(",", $style_ref_no)));
								?>
							</p>
							</td>
							<td>Method</td>
							<td><? echo $dyeing_method[$dataArray[0][csf("method")]]; ?></td>

						<tr>
						   <td><strong>No. of Req. of this Batch</strong></td>
								<td colspan="">
								<?
								echo $batch_count;
								?>
							</td>
						</tr>	                   						</tr>
						<tr> <td colspan="6" id="show_barcode_image" align="center">
	                    </td>
						</tr>

				</table>
	            <script type="text/javascript" src="../../../js/jquery.js"></script>
			 <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	         <script>

	                function generateBarcode( valuess ){

	                        var value = valuess;
	                        var btype = 'code39';
	                        var renderer ='bmp';
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
	                        $("#show_barcode_image").show().barcode(value, btype, settings);
	                    }
	                   generateBarcode('<? echo $dataArray[0][csf('requ_no')]; ?>');
	         </script>


				<br>
				<?
				$j = 1;
				$entryForm = $entry_form_arr[$batch_id_qry[0]];
				?>
				<table width="1000" cellspacing="0" align="center" class="rpt_table" border="1" rules="all">
					<thead bgcolor="#dddddd" align="center">
						<?
						if ($entryForm == 74) {
							?>
							<tr bgcolor="#CCCCFF">
								<th colspan="4" align="center"><strong>Fabrication</strong></th>
							</tr>
							<tr>
								<th width="50">SL</th>
								<th width="350">Gmts. Item</th>
								<th width="110">Gmts. Qty</th>
								<th>Batch Qty.</th>
							</tr>
							<?
						} else {
							?>
							<tr bgcolor="#CCCCFF">
								<th colspan="8" align="center"><strong>Fabrication</strong></th>
							</tr>
							<tr>
								<th width="30">SL</th>
								<th width="100">Dia/ W. Type</th>
								<th width="100">Yarn Lot</th>
								<th width="100">Brand</th>
								<th width="100">Count</th>
								<th width="300">Constrution & Composition</th>
								<th width="70">Gsm</th>
								<th width="70">Dia</th>
							</tr>
							<?
						}
						?>
					</thead>
					<tbody>
						<?
						$batch_id_qry = array_unique(explode(",", $dataArray[0][csf('batch_id')]));
						if($db_type==0)
						{
							$selsect_barcode=" group_concat(barcode_no) as barcode_no";
							$batch_query = "SELECT a.po_id, a.prod_id, a.item_description, a.width_dia_type, sum(a.roll_no) as gmts_qty, sum(a.batch_qnty) as batch_qnty, group_concat( c.yarn_lot) as yarn_lot, group_concat(c.yarn_count) as yarn_count, group_concat(c.brand_id) as brand_id
							from pro_batch_create_dtls a
							left join pro_roll_details b on a.barcode_no=b.barcode_no and b.entry_form=2
							left join pro_grey_prod_entry_dtls c on b.dtls_id=c.id
							where a.mst_id in(".implode(",",$batch_id_qry).") and a.status_active=1 and a.is_deleted=0
							group by a.po_id, a.prod_id, a.item_description, a.width_dia_type ";
						}
						else
						{
							$batch_query = "select a.po_id, a.prod_id, a.item_description, a.width_dia_type, sum(a.roll_no) as gmts_qty, sum(a.batch_qnty) as batch_qnty, listagg( cast(c.yarn_lot as varchar(4000)), ',') within group(order by c.yarn_lot) as yarn_lot, listagg( cast(c.yarn_count as varchar(4000)), ',') within group(order by c.yarn_count) as yarn_count, listagg( cast(c.brand_id as varchar(4000)), ',') within group(order by c.brand_id) as brand_id
							from pro_batch_create_dtls a
							left join pro_roll_details b on a.barcode_no=b.barcode_no and b.entry_form=2
							left join pro_grey_prod_entry_dtls c on b.dtls_id=c.id
							where a.mst_id in(".implode(",",$batch_id_qry).") and a.status_active=1 and a.is_deleted=0
							group by a.po_id, a.prod_id, a.item_description, a.width_dia_type ";
						}

						//echo $batch_query;
						$result_batch_query = sql_select($batch_query);
						$all_barcodes="";
						foreach ($result_batch_query as $row)
						{
							$all_barcodes .=implode(",",array_unique(explode(",",$row[csf("barcode_no")]))).",";
						}
						$all_barcodes=chop($all_barcodes,",");
						$lot_brand_sql="select a.yarn_lot, a.yarn_count, a.brand_id from ";
						//echo $all_barcodes;
						foreach ($result_batch_query as $rows) {
							if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
							$fabrication_full = $rows[csf("item_description")];
							$fabrication = explode(',', $fabrication_full);

							/*$yarn_lot=rtrim($yarn_dtls_array[$rows[csf('prod_id')]]['yarn_lot'],',');
							$yarn_lots=implode(", ",array_unique(explode(",",$yarn_lot)));
							$brand_name=rtrim($yarn_dtls_array[$rows[csf('prod_id')]]['brand_name'],',');
							$brands_name=implode(", ",array_unique(explode(",",$brand_name)));
							$count_name=rtrim($yarn_dtls_array[$rows[csf('prod_id')]]['count_name'],',');
							$counts_name=implode(", ",array_unique(explode(",",$count_name)));
							$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
							$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
							*/
							$yarn_lots=implode(", ",array_unique(explode(",",$rows[csf("yarn_lot")])));
							$brand_name_arr=array_unique(explode(",",$rows[csf("brand_id")]));
							foreach($brand_name_arr as $brand_id)
							{
								$brands_name .=$brand_arr[$brand_id].",";
							}
							$brands_name=chop($brands_name,",");
							$count_name_arr=array_unique(explode(",",$rows[csf("yarn_count")]));
							foreach($count_name_arr as $count_id)
							{
								$counts_name .=$count_arr[$count_id].",";
							}
							$counts_name=chop($counts_name,",");

							if ($entry_form_arr[$b_id] == 36) {

								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td align="center"><? echo $j; ?></td>
									<td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
							<td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['yarn_lot'];
								?></td>
							<td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['brand_name'];
								?></td>
							<td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['count_name'];
								?></td>
								<td align="left"><? echo $fabrication[0]; ?></td>
								<td align="center"><? echo $fabrication[1]; ?></td>
								<td align="center"><? echo $fabrication[3]; ?></td>
							</tr>
							<?
						} else if ($entry_form_arr[$b_id] == 74) {
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td align="center"><? echo $j; ?></td>
								<td align="left"><? echo $garments_item[$rows[csf('prod_id')]]; ?></td>
								<td align="right"><? echo $rows[csf('gmts_qty')]; ?></td>
								<td align="right"><? echo number_format($rows[csf('batch_qnty')], 2); ?></td>
							</tr>
							<?
						} else {
						//echo $entry_form_arr[$b_id].'aaa';
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td align="center"><? echo $j; ?></td>
								<td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
								<td width="100" align="left"><p style="word-wrap:break-word;"><? echo $yarn_lots; ?></p></td>
								<td   width="100" align="left"><p style="word-wrap:break-word;"><? echo $brands_name; ?></p></td>
								<td  width="100" align="left"><p style="word-wrap:break-word;"><? echo $counts_name; ?></p></td>
								<td align="left"><? echo $fabrication[0] . ", " . $fabrication[1]; ?></td>
								<td align="center"><? echo $fabrication[2]; ?></td>
								<td align="center"><? echo $fabrication[3]; ?></td>
							</tr>
							<?
						}
						$j++;
					}


	                ?>
	            </tbody>
	        </table>
	        <div style="width:1300px; margin-top:10px">
	        	<table align="right" cellspacing="0" width="1300" border="1" rules="all" class="rpt_table">
	        		<thead bgcolor="#dddddd" align="center">
	        			<tr bgcolor="#CCCCFF">
	        				<th colspan="20" align="center"><strong>Dyes And Chemical Issue Requisition</strong></th>
	        			</tr>
	        		</thead>
	        		<?


					/*$sql_rec="select a.id, a.item_group_id, b.sub_process_id as sub_process_id, b.prod_id, b.dose_base, b.adj_type, b.comments, b.process_remark
		 from pro_recipe_entry_dtls b left join product_details_master a on b.prod_id=a.id and a.item_category_id in(5,6,7,23)
		 where b.mst_id in($recipe_id)  and b.status_active=1 and b.is_deleted=0 ";*/

		 $group_arr = return_library_array("select id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0", 'id', 'item_name');
		 $sql_rec = "select b.sub_process_id as sub_process_id, b.prod_id, b.dose_base, b.adj_type, b.comments, b.process_remark, b.item_lot
		 from pro_recipe_entry_dtls b
		 where b.mst_id in($recipe_id)  and b.status_active=1 and b.is_deleted=0";
		 $nameArray = sql_select($sql_rec);
		 foreach ($nameArray as $row) {
		 	$all_prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
		 	$item_lot_arr[$row[csf("prod_id")]] .= $row[csf("item_lot")] . ",";
		 }

		 $item_grop_id_arr = return_library_array("select id, item_group_id from product_details_master where id in(" . implode(",", $all_prod_id_arr) . ")", 'id', 'item_group_id');

		 $process_array = array();
		 $process_array_remark = array();
		 foreach ($nameArray as $row) {
		 	$process_array[$row[csf("sub_process_id")]] = $row[csf("process_remark")];
		 	$process_array_remark[$row[csf("sub_process_id")]][$row[csf("prod_id")]][$item_grop_id_arr[$row[csf("prod_id")]]][$row[csf("dose_base")]]["comments"] = $row[csf("comments")];
		 	if ($row[csf("sub_process_id")] > 92 && $row[csf("sub_process_id")] < 99) {
		 		$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"] = $row[csf("process_remark")];
		 	}

		 }


		/*$sql_rec_remark="select b.sub_process_id as sub_process_id,b.comments,b.process_remark from pro_recipe_entry_dtls b  where  b.mst_id in($recipe_id) and b.sub_process_id in(93,94,95,96,97,98) and b.status_active=1 and b.is_deleted=0";

		$nameArray_re=sql_select( $sql_rec_remark );
		foreach($nameArray_re as $row)
		{
			$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"]=$row[csf("process_remark")];
		}*/


					/*if($db_type==0)
		{
			$item_lot_arr=return_library_array("select b.prod_id,group_concat(b.item_lot) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot" );
		}
		else
		{
			$item_lot_arr=return_library_array("select b.prod_id,listagg((cast(b.item_lot as varchar2(500))),',') within group (order by b.prod_id) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot" );
		}



		$sql_dtls = "select a.requ_no, a.batch_id, a.recipe_id, b.id,
		b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
		c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id
		from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
		where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id=$data[1] order by b.id, b.seq_no";*/


		$sql_dtls = "(select a.requ_no, a.batch_id, a.recipe_id, b.id,
		b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
		c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id, b.item_lot, b.store_id
		from dyes_chem_issue_requ_mst a, dyes_chem_issue_requ_dtls b, product_details_master c
		where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id=$data[1])
		union
		(
		select a.requ_no, a.batch_id, a.recipe_id, b.id,
		b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
		null as item_description, null as item_group_id, null as sub_group_name, null as item_size, null as unit_of_measure,null as avg_rate_per_unit,null as prod_id, b.item_lot, b.store_id
		from dyes_chem_issue_requ_mst a, dyes_chem_issue_requ_dtls b
		where a.id=b.mst_id and b.sub_process in($subprocessforwashin) and a.id=$data[1]
		) order by id";
					// echo $sql_dtls;//die;
		$sql_result = sql_select($sql_dtls);

		$sub_process_array = array();
		$sub_process_tot_rec_array = array();
		$sub_process_tot_req_array = array();
		$sub_process_tot_value_array = array();

		foreach ($sql_result as $row) {
			$sub_process_tot_rec_array[$row[csf("sub_process")]] += $row[csf("recipe_qnty")];
			$sub_process_tot_req_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
			$sub_process_tot_value_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")] * $row[csf("avg_rate_per_unit")];
			//$sub_process_tot_ratio_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
			$prodIds_arr[$row[csf('prod_id')]]=$row[csf('prod_id')];
		}
		$StoreProdIds_cond="";
		if($db_type==2 && count($prodIds_arr)>1000)
		{
			$StoreProdIds_cond=" and (";
			$prodIdsArr=array_chunk($prodIds_arr,999);
			foreach($prodIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$StoreProdIds_cond.=" prod_id in($ids) or ";
			}
			$StoreProdIds_cond=chop($StoreProdIds_cond,'or ');
			$StoreProdIds_cond.=")";
		}
		else
		{
			$StoreProdIds_cond=" and prod_id in (".implode(",",$prodIds_arr).")";
		}
		// $sql_prod_store = "SELECT id, prod_id, store_id, cons_qty, lot  from inv_store_wise_qty_dtls where status_active=1 and is_deleted=0 $StoreProdIds_cond";
		$con = connect();
		$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (156)");
		oci_commit($con);

		if(count($prodIds_arr)>0) fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 156, 4, $prodIds_arr, $empty_arr);

		$sql_prod_store = "SELECT a.id, a.prod_id, a.store_id, a.cons_qty, a.lot  from inv_store_wise_qty_dtls a,GBL_TEMP_ENGINE b
		where a.prod_id=b.REF_VAL and b.ENTRY_FORM=156 and b.USER_ID=$user_id and b.REF_FROM=4  and a.status_active=1 and a.is_deleted=0 $StoreProdIds_cond";
		//echo $sql_prod_store;
		$sql_prod_store_result = sql_select($sql_prod_store);
		foreach ($sql_prod_store_result as $row)
		{
			$prod_store_data_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("lot")]]= $row[csf("cons_qty")];
		}
		unset($sql_prod_store_result);
		execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (156)");
		oci_commit($con);
		disconnect($con);

		$recipe_id=$data_array[0][csf("recipe_id")];
		$ratio_arr=array();
		$prevRatioData=sql_select( "select mst_id,sub_process_id, liquor_ratio from pro_recipe_entry_dtls where mst_id in ($recipe_id) and status_active=1 and ratio>0");
		foreach($prevRatioData as $prevRow)
		{
			if($process_array_liquor_check[$prevRow[csf("mst_id")]][$prevRow[csf("sub_process_id")]]=="")
				{

						$ratio_arr[$prevRow[csf('sub_process_id')]]['ratio']+=$prevRow[csf('liquor_ratio')];
						$process_array_liquor_check[$prevRow[csf("mst_id")]][$prevRow[csf("sub_process_id")]]=888;
				}


		}
					//var_dump($sub_process_tot_req_array);
		$i = 1;
		$k = 1;
		$recipe_qnty_sum = 0;
		$req_qny_edit_sum = 0;
		$recipe_qnty = 0;
		$req_qny_edit = 0;
		$req_value_sum = 0;
		$req_value_grand = 0;
		$recipe_qnty_grand = 0;
		$req_qny_edit_grand = 0;

		foreach ($sql_result as $row) {
			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";
			if (!in_array($row[csf("sub_process")], $sub_process_array))
			{
				$sub_process_array[] = $row[csf('sub_process')];
				if ($k != 1) {
					?>
					<tr>
						<td colspan="9" align="right"><strong>Total :</strong></td>
						<td align="right"><?php echo number_format($recipe_qnty_sum, 6, '.', ''); ?></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right"><?php echo number_format($req_qny_edit_sum, 6, '.', ''); ?></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right"><?php echo number_format($req_value_sum, 6, '.', ''); ?></td>
					</tr>
					<?
				}
				$recipe_qnty_sum = 0;
				$req_qny_edit_sum = 0;
				$req_value_sum = 0;
				$k++;
        //$subprocessforwashin
				//if ($row[csf("sub_process")] == 93 || $row[csf("sub_process")] == 94 || $row[csf("sub_process")] == 95 || $row[csf("sub_process")] == 96 || $row[csf("sub_process")] == 97 || $row[csf("sub_process")] == 98) {
				if (in_array($row[csf("sub_process")],$subprocessForWashArr))
				{
					$pro_remark = $process_array_remark[$row[csf("sub_process")]]["wash_comments"];
				}
				else $pro_remark='';

				?>
				<tr bgcolor="#CCCCCC">
					<th colspan="20">
						<strong><?

						$batch_ratio=explode(":",$data_array[0][csf("ratio")]);
						$lqr_ratio=$batch_ratio[0];
						$ratio_qty=$ratio_arr[$row[csf('sub_process')]]['ratio'];
						if ($pro_remark == '') $pro_remark = ''; else $pro_remark = $pro_remark;
						echo $dyeing_sub_process[$row[csf("sub_process")]] . ', ' . $pro_remark.'&nbsp;Liquor:Ratio &nbsp;'.$ratio_qty; ?></strong>
					</th>
				</tr>
				<tr bgcolor="#EFEFEF">
					<th width="30">SL</th>
					<th width="80">Item Cat.</th>
					<th width="80">Item Group</th>
					<th width="100">Item Description</th>
					<th width="50">Dyes Lot</th>
					<th width="50">UOM</th>
                    <th width="60">Stock</th>
					<th width="90">Dose Base</th>
					<th width="40">Ratio</th>
					<th width="60">Recipe Qty.</th>
					<th width="50">Adj%</th>
					<th width="60">Adj Type</th>
					<th width="60">Adj Qty.</th>
					<th width="80">Iss. Qty.</th>
					<th width="60">KG</th>
					<th width="60">GM</th>
					<th width="60">MG</th>
					<th width="90">Comments</th>
					<th width="70">Avg. Rate</th>
					<th>Issue Value</th>
				</tr>
				<?
			}

			/*$iss_qnty=$row[csf("req_qny_edit")]*10000;
			$iss_qnty_kg=floor($iss_qnty/10000);
			$lkg=round($iss_qnty-$iss_qnty_kg*10000);
			$iss_qnty_gm=floor($lkg/10);
			$iss_qnty_mg=$lkg%10;*/
			/*$iss_qnty_gm=substr((string)$iss_qnty[1],0,3);
			$iss_qnty_mg=substr($iss_qnty[1],3);*/

			$req_qny_edit = explode(".", (string)$row[csf("req_qny_edit")]);
			$iss_qnty_kg = $req_qny_edit[0];
			if ($iss_qnty_kg == "") $iss_qnty_kg = 0;

			//$mg=($row[csf("req_qny_edit")]-$iss_qnty_kg)*1000*1000;
			//$iss_qnty_gm=floor($mg/1000);
			//$iss_qnty_mg=round($mg-$iss_qnty_gm*1000);

			//echo "gm=".substr($req_qny_edit[1],0,3)."; mg=".substr($req_qny_edit[1],3,3);


			//$num=(string)$req_qny_edit[1];
			//$next_number= str_pad($num,6,"0",STR_PAD_RIGHT);// ."000";
			//$rem= explode(".",($next_number/1000) );
			//echo $rem ."=";
			$iss_qnty_gm = substr($req_qny_edit[1], 0, 3);//$rem[0]; // floor($mg/1000);
			$iss_qnty_mg = substr($req_qny_edit[1], 3, 3);//$rem[1];//round($mg-$iss_qnty_gm*1000);
			$iss_qnty_gm = str_pad($iss_qnty_gm, 3, "0", STR_PAD_RIGHT);
			$iss_qnty_mg = str_pad($iss_qnty_mg, 3, "0", STR_PAD_RIGHT);

			//echo "mg ".$req_qny_edit[1]."<br>";
			$adjQty = ($row[csf("adjust_percent")] * $row[csf("recipe_qnty")]) / 100;
			$comment = $process_array_remark[$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"];
			$store_wise_stock=$prod_store_data_array[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("item_lot")]];
			?>
			<tbody>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center"><? echo $i; ?></td>
				<td><? echo $item_category[$row[csf("item_category")]];  //echo $row[csf("sub_process")];
					?></td>
					<td><strong><? echo $group_arr[$row[csf("item_group_id")]]; ?></strong></td>
					<!--<td><? echo $row[csf("sub_group_name")]; ?></td>-->
					<td><? echo $row[csf("item_description")] . ' ' . $row[csf("item_size")]; ?></td>
					<td><? echo $row[csf("item_lot")];//chop($item_lot_arr[$row[csf("prod_id")]], ","); ?></td>
					<td align="center"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
					<td align="right"><? echo number_format($store_wise_stock,2); ?></td>
					<td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
					<td align="center"><? echo number_format($row[csf("ratio")], 6, '.', ''); ?></td>
					<td align="right"><strong><? echo number_format($row[csf("recipe_qnty")], 6, '.', ''); ?></strong></td>
					<td align="center"><? echo $row[csf("adjust_percent")]; ?></td>
					<td><? echo $increase_decrease[$row[csf("adjust_type")]]; ?></td>
					<td align="right"><? echo number_format($adjQty, 6, '.', ''); ?></td>
					<td align="right"><strong><? echo number_format($row[csf("req_qny_edit")], 6, '.', ''); ?></strong></td>
					<td align="right"><? echo $iss_qnty_kg; ?></td>
					<td align="right"><? echo $iss_qnty_gm; ?></td>
					<td align="right"><? echo $iss_qnty_mg; ?></td>
					<td align="right"><? echo $comment; ?></td>
					<td align="right"><? echo number_format($row[csf("avg_rate_per_unit")], 6, '.', ''); ?></td>
					<td align="right"><? $req_value = $row[csf("req_qny_edit")] * $row[csf("avg_rate_per_unit")]; echo number_format($req_value, 6, '.', ''); ?></td>
					</tr>
				</tbody>
				<? $i++;
				$recipe_qnty_sum += $row[csf('recipe_qnty')];
				$req_qny_edit_sum += $row[csf('req_qny_edit')];
				$req_value_sum += $req_value;

				$recipe_qnty_grand += $row[csf('recipe_qnty')];
				$req_qny_edit_grand += $row[csf('req_qny_edit')];
				$req_value_grand += $req_value;
			}
			foreach ($sub_process_tot_rec_array as $val_rec) {
				$totval_rec = $val_rec;
			}
			foreach ($sub_process_tot_req_array as $val_req) {
				$totval_req = $val_req;
			}
			foreach ($sub_process_tot_value_array as $req_value) {
				$tot_req_value = $req_value;
			}

			//$recipe_qnty_grand +=$val_rec;
			//$req_qny_edit_grand +=$val_req;
			//$req_value_grand +=$req_value;
			?>
			<tr>
				<td colspan="9" align="right"><strong>Total :</strong></td>
				<td align="right"><?php echo number_format($totval_rec, 6, '.', ''); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td align="right"><?php echo number_format($totval_req, 6, '.', ''); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td align="right"><?php echo number_format($tot_req_value, 6, '.', ''); ?></td>
			</tr>
			<tr>
				<td colspan="9" align="right"><strong> Grand Total :</strong></td>
				<td align="right"><?php echo number_format($recipe_qnty_grand, 6, '.', ''); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td align="right"><?php echo number_format($req_qny_edit_grand, 6, '.', ''); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td align="right"><?php echo number_format($req_value_grand, 6, '.', ''); ?></td>
			</tr>
			<tr>
				<td colspan="18" align="right"><strong> Cost Per Kg :</strong></td>
				<td colspan="2"
				align="right"><?php
				$cost_per_kg=$req_value_grand / ($batch_weight + $batchdata_array[0][csf('batch_weight')]);
				echo number_format($cost_per_kg, 6, '.', ''); ?></td>
			</tr>
			<tr>
				<td colspan="18" align="right"><strong> Total Revenue :</strong></td>
				<td colspan="2"
				align="right" title="<? echo 'Dyeing Charge='.$tot_avg_dyeing_charge; ?>"><?php $total_revenue=$tot_batch_weight*$tot_avg_dyeing_charge;echo number_format($total_revenue, 6, '.', ''); ?></td>
			</tr>
			 <tr>
				<td colspan="18" align="right"><strong> Chemical Cost as % of Revenue :</strong></td>
				<td colspan="2"
				align="right"><?php echo number_format(($req_value_grand/$total_revenue)*100,6, '.', ''); ?></td>
			</tr>
		</table>
		<br>
		<?
		echo signature_table(15, $data[0], "1200px");
		?>
    	</div>
        </div>
        <?
        exit();
	}
	else if($data[3]==5) // Print report 2
	{
		$receipe_arr = array();
		$receipeData = sql_select("select id,labdip_no, total_liquor from pro_recipe_entry_mst where status_active=1 and is_deleted=0");
		foreach ($receipeData as $row) {
			$receipe_arr[$row[csf("id")]] = $row[csf("labdip_no")];
		}
		if ($data[4]==1) // ok to open without issue value;
		{
			$sql = "select a.id, a.requ_no, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id
			from dyes_chem_issue_requ_mst a where a.id='$data[1]' and a.company_id='$data[0]'";
			$dataArray = sql_select($sql);

			$recipe_id = $dataArray[0][csf('recipe_id')];

			$receipe_arr = array();
			$receipeData = sql_select("select b.id as batch_id,b.entry_form,a.id,a.labdip_no, a.total_liquor from pro_recipe_entry_mst a,pro_batch_create_mst b where a.batch_id=b.id and a.status_active=1 and a.is_deleted=0 and a.id in($recipe_id)");
			foreach ($receipeData as $row) {
				$receipe_arr[$row[csf("id")]] = $row[csf("labdip_no")];
				$entry_form_arr[$row[csf("batch_id")]] = $row[csf("entry_form")];
			}


			$lib_floor = sql_select("select floor_id from lib_machine_name where id='" . $dataArray[0][csf("machine_id")] . "'");
			$floor_name = return_field_value("floor_name", "lib_prod_floor", "id='" . $lib_floor[0][csf('floor_id')] . "'");

			$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
			$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
			$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
			$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
			//$entry_form_arr = return_library_array("select id, entry_form from pro_batch_create_mst", "id", "entry_form");
			//$order_id_arr=return_library_array( "select id, booking_id from  pro_recipe_entry_mst", "id", "booking_id");
			//$order_no_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
			//$job_no_arr=return_library_array( "select id, job_no from wo_booking_mst", "id", "job_no");
			$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
			$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
			$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");

			$batch_weight = 0;
			if ($db_type == 0) {
				$data_array = sql_select("select group_concat(buyer_id) as buyer_id,group_concat(id) as recipe_id, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor, group_concat(concat_ws(':',batch_ratio,liquor_ratio) order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, group_concat(case when entry_form<>60 then batch_id end) as batch_id_rec_main, group_concat(labdip_no) as labdip_no from pro_recipe_entry_mst where id in($recipe_id)");
			} else {
				$data_array = sql_select("select listagg(cast(buyer_id as varchar(4000)),',') within group (order by buyer_id) as buyer_id, listagg(cast(id as varchar(4000)),',') within group (order by id) as recipe_id, sum(total_liquor) as total_liquor, listagg(cast(batch_ratio || ':' || liquor_ratio as varchar(4000)),',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(cast(case when entry_form<>60 then batch_id end as varchar(4000)),',') within group (order by batch_id) as batch_id_rec_main, listagg(cast(batch_id as varchar(4000)),',') within group (order by batch_id) as batch_id, listagg(cast(labdip_no as varchar(4000)), ',') within group (order by id) as labdip_no from pro_recipe_entry_mst where id in($recipe_id)");

				/*$data_array = sql_select("select listagg(buyer_id,',') within group (order by buyer_id) as buyer_id,listagg(id,',') within group (order by id) as recipe_id, listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor, listagg(batch_ratio || ':' || liquor_ratio,',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(case when entry_form<>60 then batch_id end,',') within group (order by batch_id) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");*/	// old query
			}

			$batch_weight = $data_array[0][csf("batch_weight")];
			$batch_id = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id")])));

			$batch_id_rec_main = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id_rec_main")])));
			if ($batch_id_rec_main == "") $batch_id_rec_main = 0;
			$booking_no_sql="select distinct booking_no from pro_batch_create_mst where id in($batch_id) and is_deleted=0 and status_active=1 ";
			$booking_no_arr=sql_select($booking_no_sql);
			foreach ($booking_no_arr as $row)
			{
		        $booking_arr[$row[csf("booking_no")]]=$row[csf("booking_no")];
		    }
					if ($db_type == 0) {
				$batchdata_array = sql_select("select group_concat(batch_no) as batch_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(distinct color_id) as color_id from pro_batch_create_mst where id in($batch_id)");
			} else {
				$batchdata_array = sql_select("select listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, listagg(color_id ,',') within group (order by id) as color_id, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight
					from pro_batch_create_mst where id in($batch_id)");
			}


			$po_no = '';
			$job_no = '';
			$buyer_name = '';
			$style_ref_no = '';$int_ref_no  = '';
			foreach (explode(",", $data_array[0][csf("batch_id")]) as $b_id)
			{
				if ($entry_form_arr[$b_id] == 36)
				{
					//echo "select distinct b.order_no,b.cust_style_ref, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(".$b_id.") ";
					$po_data = sql_select("select distinct b.order_no,b.cust_style_ref, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ");
					foreach ($po_data as $row) {
						$po_no .= $row[csf('order_no')] . ",";
						$job_no .= $row[csf('subcon_job')] . ",";
						if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
						$buyer_name .= $buyer_library[$row[csf('party_id')]] . ",";
					}
				}
				else
				{
					//echo "select distinct b.po_number, c.job_no,c.style_ref_no, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(".$b_id.") ";
					$po_data = sql_select("select distinct b.po_number,b.grouping, c.job_no,c.style_ref_no, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ");
					$job_nos="";
					foreach ($po_data as $row) {
						$po_no .= $row[csf('po_number')] . ",";
						$job_no .= $row[csf('job_no')] . ",";
						//$job_nos .= $row[csf('job_no')] . ",";
						if ($int_ref_no == '') $int_ref_no = $row[csf('grouping')]; else $int_ref_no .= "," . $row[csf('grouping')];
						if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
						if ($job_nos == '') $job_nos ="'".$row[csf('job_no')]."'"; else $job_nos.= ","."'".$row[csf('job_no')]."'";
						//$buyer_name.=$buyer_library[$row[csf('buyer_name')]].",";
					}
					foreach (explode(",", $data_array[0][csf("buyer_id")]) as $buyer_id) {
						$buyer_name .= $buyer_library[$buyer_id] . ",";
					}

				}
			}
			$jobNos=rtrim($job_nos,',');
			$jobnos=rtrim($job_no,',');

			//echo "select job_no, exchange_rate from wo_pre_cost_mst $job_nos_con";
			if($job_nos!='')
			{
				if($job_nos!='') $job_nos_con="where  job_no in(".$job_nos.")";//else $job_nos_con="where  job_no in('')";
				//echo "select job_no, exchange_rate from wo_pre_cost_mst $job_nos_con";
				$exchange_rate_arr = return_library_array("select job_no, exchange_rate from wo_pre_cost_mst $job_nos_con", 'job_no', 'exchange_rate');
			}
			//print_r($exchange_rate_arr);
			if($job_nos!='')
			{
			$condition= new condition();
			 $condition->company_name("=$data[0]");
			 if(str_replace("'","",$job_nos) !=''){
				  $condition->job_no("in($job_nos)");
			 }
			 $condition->init();

			$conversion= new conversion($condition);
			//echo $conversion->getQuery(); die;
			$conversion_qty_arr_process=$conversion->getQtyArray_by_jobAndProcess();
			$conversion_costing_arr_process=$conversion->getAmountArray_by_jobAndProcess();
			}
			// print_r($conversion_costing_arr_process);
			$dyeing_charge_arr=array(25,26,30,31,32,33,34,39,60,62,63,64,65,66,67,68,69,70,71,82,83,84,85,86,89,90,91,92,93,94,129,135,136,137,138,139,140,141,142,143,146);
			$jobnos=array_unique(explode(",",$jobnos));
			$tot_avg_dyeing_charge=0;$avg_dyeing_charge=0;$tot_dyeing_qty=$tot_dyeing_cost=0;//$tot_dyeing_cost=0;$tot_dyeing_qty=0;
			foreach($jobnos as $job)
			{
				$exchange_rate=$exchange_rate_arr[$job];

				foreach($dyeing_charge_arr as $process_id)
				{

					$dyeing_cost=array_sum($conversion_costing_arr_process[$job][$process_id]);
					//echo $job.'=='.$dyeing_cost.'='.$exchange_rate.'<br/>';
					$totdyeing_cost=$dyeing_cost;


					if($totdyeing_cost>0)
					{

						$tot_dyeing_qty+=array_sum($conversion_qty_arr_process[$job][$process_id]);
						$tot_dyeing_cost+=$dyeing_cost;
						//echo $tot_dyeing_cost.'='.$tot_dyeing_qty.'**'.$exchange_rate.'**'.$process_id.'<br>';
						//$avg_dyeing_charge+=$tot_dyeing_cost/$tot_dyeing_qty;
					}
				}
				//echo $tot_dyeing_cost.'<br>'.$tot_dyeing_qty;
				$avg_dyeing_charge+=$tot_dyeing_cost/$tot_dyeing_qty;
				$tot_avg_dyeing_charge+=$avg_dyeing_charge*$exchange_rate;
			}

			//echo $avg_dyeing_charge.'='.$tot_dyeing_cost.'='.$dyeing_cost;
			//echo $style_ref_no;
			/*if ($db_type==0)
			{
				$sql_yarn_dtls="select b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
			}
			elseif ($db_type==2)
			{
				$sql_yarn_dtls="select b.po_breakdown_id, b.prod_id, listagg((cast(a.yarn_lot as varchar2(500))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(a.brand_id,',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(500))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
			}*/

			/*if ($db_type == 0)
			{
				$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
			}
			else if ($db_type == 2)
			{

				$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, a.yarn_lot, a.brand_id, a.yarn_count
					from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b
					where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
					group by b.po_breakdown_id, b.prod_id , a.yarn_lot, a.brand_id, a.yarn_count";
			}

			$yarn_dtls_array = array();
			$result_sql_yarn_dtls = sql_select($sql_yarn_dtls);
			foreach ($result_sql_yarn_dtls as $row)
			{
				//$yarn_lot = implode(", ", array_unique(explode(",", $row[csf('yarn_lot')])));
				$yarn_id = array_unique(explode(",", $row[csf('yarn_lot')]));
				$yarn_lot = "";
				foreach ($yarn_id as $val) {
					if ($yarn_lot == "") $yarn_lot = $val; else $yarn_lot .= ", " . $val;
				}

				$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
				$brand_name = "";
				foreach ($brand_id as $val) {
					if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= ", " . $brand_arr[$val];
				}

				$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
				$count_name = "";
				foreach ($yarn_count as $val) {
					if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
				}
				$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['yarn_lot'] = $yarn_lot;
				$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['brand_name'] = $brand_name;
				$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['count_name'] = $count_name;
			}*/

			$roll_maintained = return_field_value("fabric_roll_level", "variable_settings_production", "company_name ='$data[0]' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");

			$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, a.yarn_lot as yarn_lot,a.brand_id as brand_id,a.yarn_count  as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and  b.prod_id=p.prod_id  and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			//echo $sql_yarn_dtls;
			$yarn_dtls_array = array();
			$result_sql_yarn_dtls = sql_select($sql_yarn_dtls);
			foreach ($result_sql_yarn_dtls as $row)
			{
				$yarn_lot = $row[csf('yarn_lot')];//implode(", ", array_unique(explode(",", $row[csf('yarn_lot')])));
				$brand_id =$row[csf('brand_id')];// array_unique(explode(",", $row[csf('brand_id')]));
				//$brand_name = "";
				foreach ($brand_id as $val) {
					//if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= ", " . $brand_arr[$val];
				}

				$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
				$count_name = "";
				foreach ($yarn_count as $val) {
					if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
				}
				if($yarn_lot!='')
				{
				$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['yarn_lot'].=$yarn_lot.',';
				}
				if($row[csf('brand_id')]!='' || $row[csf('brand_id')]!=0)
				{
				$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['brand_name'] .= $brand_arr[$row[csf('brand_id')]].',';
				}
				if($count_name!='')
				{
				$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['count_name'] .= $count_name.',';
				}
			}
			//var_dump($yarn_dtls_array);
			$po_no = implode(", ", array_unique(explode(",", substr($po_no, 0, -1))));
			$job_no = implode(", ", array_unique(explode(",", substr($job_no, 0, -1))));
			$int_ref_nos = implode(", ", array_unique(explode(",", substr($int_ref_no, 0, -1))));

			$buyer_name = implode(",", array_unique(explode(",", substr($buyer_name, 0, -1))));

			$color_id = array_unique(explode(",", $batchdata_array[0][csf('color_id')]));
			$color_name = '';
			foreach ($color_id as $color) {
				$color_name .= $color_arr[$color] . ",";
			}
			$color_name = substr($color_name, 0, -1);
			//var_dump($recipe_color_arr);

			//$recipe_id=$data_array[0][csf("recipe_id")];
			$recipe_arr=sql_select("select a.working_company_id,a.company_id, b.batch_weight from pro_recipe_entry_mst a, pro_batch_create_mst b where a.batch_id=b.id and a.id=$recipe_id");
			$owner_company_id=$recipe_arr[0][csf('company_id')];
			$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
			$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
			$group_id=$nameArray[0][csf('group_id')];
			?>
			<div style="width:950px;">
				<table width="950" cellspacing="0" align="center">

							<tr>
								<td colspan="10" align="center" style="font-size:xx-large">
									<strong><? echo $company_library[$data[0]];//$company_library[$data[0]]; ?></strong></td>
							</tr>
							<tr class="form_caption">
								<td colspan="6" align="center" style="font-size:14px">
									<?

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
								<td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Report</u></strong></td>
							</tr>
						</table>
						<table align="center" cellspacing="0" width="950" border="1" rules="all" class="rpt_table">
							<tr>
								<td border="1"><strong>Booking No </strong></td>
								<td><? echo implode(',',$booking_arr); ?></td>
								<td><strong>Order No</strong></td>
								<td><? echo $po_no; ?></td>
								<td><strong>Req. Date</strong></td>
								<td><? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
							</tr>
							<tr>
								<td><strong>Buyer</strong></td>
								<td><? echo implode(",", array_unique(explode(",", $buyer_name))); ?></td>
								<td><strong>Batch No</strong></td>
								<td><? echo $batchdata_array[0][csf('batch_no')]; ?></td>
								<td><strong>Batch Weight</strong></td>
								<td><? $tot_batch_weight=$batch_weight+$batchdata_array[0][csf('batch_weight')];echo number_format($tot_batch_weight,4); ?></td>
							</tr>
							<tr>
								<td><strong>Color</strong></td>
								<td><? echo $color_name; ?></td>
								<td><strong>Lab Dip</strong></td>
								<td>
	        						<?
	        						$labdip_no = '';
	        						$recipe_ids = explode(",", $data_array[0][csf("recipe_id")]);
	        						foreach ($recipe_ids as $recp_id) {
	        							$labdip_no .= $receipe_arr[$recp_id] . ",";
	        						}
	        						echo chop($labdip_no, ',');
	        						// or-> echo $data_array[0][csf("labdip_no")];
	        						?>
	        					</td>
								<td><strong>Machine No</strong></td>
								<td>
									<?
									$machine_data = sql_select("select machine_no, floor_id from lib_machine_name where id='" . $dataArray[0][csf("machine_id")] . "'");
									echo $machine_data[0][csf('machine_no')];
									?>
								</td>
							</tr>
							<tr>
		                        <td><strong>Style Ref.</strong></td>
								<td><p><? echo implode(",", array_unique(explode(",", $style_ref_no))); ?></p></td>
								<td><strong>Req. ID </strong></td>
								<td><? echo $dataArray[0][csf('requ_no')]; ?></td>
								<td border="1"><strong>Job No </strong></td>
								<td><? echo $job_no; ?></td>
							</tr>
							<tr>
		                        <td><strong>Int. Ref. No.</strong></td>
								<td ><p><? echo implode(",", array_unique(explode(",", $int_ref_nos))); ?></p></td>
								<td><strong>Recipe NO </strong></td>
								<td><? echo $dataArray[0][csf('recipe_id')]; ?></td>
								<td border="1"><strong>Floor Name </strong></td>
								<td><? echo $floor_name; ?></td>
							</tr>
				</table>
		            <script type="text/javascript" src="../../../js/jquery.js"></script>
				 <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
		         <script>

		                function generateBarcode( valuess ){

		                        var value = valuess;
		                        var btype = 'code39';
		                        var renderer ='bmp';
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
		                        $("#show_barcode_image").show().barcode(value, btype, settings);
		                    }
		                   generateBarcode('<? echo $dataArray[0][csf('requ_no')]; ?>');
		         </script>


					<br>
					<?
					$batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
					$j = 1;
					$entryForm = $entry_form_arr[$batch_id_qry[0]];
					?>
					<table width="950" cellspacing="0" align="center" class="rpt_table" border="1" rules="all">
						<thead bgcolor="#dddddd" align="center">
							<?
							if ($entryForm == 74)
							{
								?>
								<tr bgcolor="#CCCCFF">
									<th colspan="4" align="center"><strong>Fabrication</strong></th>
								</tr>
								<tr>
									<th width="50">SL</th>
									<th width="350">Gmts. Item</th>
									<th width="110">Gmts. Qty</th>
									<th>Batch Qty.</th>
								</tr>
								<?
							}
							else
							{
								?>
								<tr bgcolor="#CCCCFF">
									<th colspan="8" align="center"><strong>Fabrication</strong></th>
								</tr>
								<tr>
									<th width="30">SL</th>
									<th width="100">Dia/ W. Type</th>
									<th width="100">Yarn Lot</th>
									<th width="100">Brand</th>
									<th width="100">Count</th>
									<th width="300">Constrution & Composition</th>
									<th width="70">Gsm</th>
									<th width="70">Dia</th>
								</tr>
								<?
							}
							?>
						</thead>
						<tbody>
							<?

							/*foreach ($batch_id_qry as $b_id)
							{}*/

							$b_id = implode(",", explode(",", $dataArray[0][csf('batch_id')])) ;
							if ($roll_maintained==1)
							{
								if ($db_type==0)
								{
									$batch_query="SELECT b.item_description, b.width_dia_type, b.prod_id, sum(b.roll_no) as gmts_qty, sum(b.batch_qnty) as batch_qnty, group_concat(d.yarn_lot) as yarn_lot, group_concat(d.yarn_count as yarn_count, group_concat(d.brand_id) as brand_id,b.gsm
									from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d
									where a.id=b.mst_id and b.barcode_no=c.barcode_no and c.dtls_id=d.id and a.company_id=$data[0] and a.id in($b_id) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and
									a.is_deleted=0 and c.entry_form in(2,22)
									group by b.item_description, b.width_dia_type, b.prod_id,b.gsm";
								}
								else
								{
									$batch_query="SELECT b.item_description, b.width_dia_type, b.prod_id, sum(b.roll_no) as gmts_qty, sum(b.batch_qnty) as batch_qnty,
									LISTAGG(CAST(d.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) as yarn_lot,
									LISTAGG(CAST(d.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_count) as yarn_count,
									LISTAGG(CAST(d.brand_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.brand_id) as brand_id, b.gsm, b.grey_dia
									from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d
									where a.id=b.mst_id and b.barcode_no=c.barcode_no and c.dtls_id=d.id and a.company_id=$data[0] and a.id in($b_id) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and
									a.is_deleted=0 and c.entry_form in(2,22)
									group by b.item_description, b.width_dia_type, b.prod_id,b.gsm,b.grey_dia";
								}
							}
							else
							{
								$batch_query="SELECT po_id, prod_id, item_description, width_dia_type, sum(roll_no)  as gmts_qty, sum(batch_qnty) as  batch_qnty,gsm,grey_dia
								from pro_batch_create_dtls
								where mst_id in($b_id) and status_active=1 and is_deleted=0
								group by po_id, prod_id, item_description, width_dia_type,gsm,grey_dia";
							}
							// echo $batch_query;

							$result_batch_query = sql_select($batch_query);
							foreach ($result_batch_query as $rows)
							{
								if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								$fabrication_full = $rows[csf("item_description")];
								$fabrication = explode(',', $fabrication_full);

								if ($roll_maintained==1)
								{
									$yarn_id = array_unique(explode(",", $rows[csf('yarn_lot')]));
									$yarn_lots = "";
									foreach ($yarn_id as $val) {
										if ($yarn_lots == "") $yarn_lots = $val; else $yarn_lots .= ", " . $val;
									}

									$yarn_lots=rtrim($yarn_lots, ", ");
									$brand_id = array_unique(explode(",", $rows[csf('brand_id')]));
									$brand_names = "";
									foreach ($brand_id as $val) {
										if ($brand_names == "") $brand_names = $brand_arr[$val]; else $brand_names .= ", " . $brand_arr[$val];
									}

									$yarn_count = array_unique(explode(",", $rows[csf('yarn_count')]));
									$count_names = "";
									foreach ($yarn_count as $val) {
										if ($count_names == "") $count_names = $count_arr[$val]; else $count_names .= ", " . $count_arr[$val];
									}
								}
								else
								{
									$yarn_lot=$yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['yarn_lot'];
									$yarn_lots = implode(", ", array_unique(explode(",", substr($yarn_lot, 0, -1))));
									$brand_name=$yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['brand_name'];
									$brand_names = implode(", ", array_unique(explode(",", substr($brand_name, 0, -1))));
									$count_name=$yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['count_name'];
									$count_names = implode(", ", array_unique(explode(",", substr($count_name, 0, -1))));
								}



								if ($entry_form_arr[$b_id] == 36)
								{
									?>
									<tr bgcolor="<? echo $bgcolor; ?>">
										<td align="center"><? echo $j; ?></td>
										<td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
			                            <td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['yarn_lot'];
			                            	?></td>
			                            <td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['brand_name'];
			                            	?></td>
			                            <td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['count_name'];
			                            	?></td>
		                            	<td align="left"><? echo $fabrication[0]; ?></td>
		                            	<td align="center"><? echo $rows[csf("gsm")]; ?></td>
		                            	<td align="center"><? echo $rows[csf("grey_dia")]; ?></td>
		                            </tr>
	                            	<?
	                        	}
		                        else if ($entry_form_arr[$b_id] == 74)
		                        {
		                        	?>
		                        	<tr bgcolor="<? echo $bgcolor; ?>">
		                        		<td align="center"><? echo $j; ?></td>
		                        		<td align="left"><? echo $garments_item[$rows[csf('prod_id')]]; ?></td>
		                        		<td align="right"><? echo $rows[csf('gmts_qty')]; ?></td>
		                        		<td align="right"><? echo number_format($rows[csf('batch_qnty')], 2); ?></td>
		                        	</tr>
		                        	<?
		                        }
		                        else
		                        {
		                        	?>
		                        	<tr bgcolor="<? echo $bgcolor; ?>">
		                        		<td align="center"><? echo $j; ?></td>
		                        		<td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
		                        		<td width="100" align="left"><p style="word-wrap:break-word;"><? echo $yarn_lots; ?></p></td>
		                        		<td align="left"><? echo $brand_names; ?></td>
		                        		<td align="left"><? echo $count_names; ?></td>
		                        		<td align="left"><? echo $fabrication[0] . ", " . $fabrication[1]; ?></td>
		                        		<td align="center"><? echo $fabrication[2];//$rows[csf("gsm")]; ?></td>
		                        		<td align="center"><? echo  $fabrication[3];//$rows[csf("grey_dia")]; ?></td>
		                        	</tr>
		                        	<?
		                        }
		                        $j++;
	                    	}
		                ?>
		            </tbody>
		        </table>

		        <div style="width:950px; margin-top:10px">
		        	<table align="center" cellspacing="0" width="950" border="1" rules="all" class="rpt_table">
		        		<thead bgcolor="#dddddd" align="center">
		        			<tr bgcolor="#CCCCFF">
		        				<th colspan="19" align="center"><strong>Dyes And Chemical Issue Requisition</strong></th>
		        			</tr>
		        		</thead>
		        		<?


			/*$sql_rec="select a.id, a.item_group_id, b.sub_process_id as sub_process_id, b.prod_id, b.dose_base, b.adj_type, b.comments, b.process_remark
			 from pro_recipe_entry_dtls b left join product_details_master a on b.prod_id=a.id and a.item_category_id in(5,6,7,23)
			 where b.mst_id in($recipe_id)  and b.status_active=1 and b.is_deleted=0 ";*/

			 $group_arr = return_library_array("select id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0", 'id', 'item_name');
			 $sql_rec = "select b.sub_process_id as sub_process_id, b.prod_id, b.dose_base, b.adj_type, b.comments, b.process_remark, b.item_lot
			 from pro_recipe_entry_dtls b
			 where b.mst_id in($recipe_id)  and b.status_active=1 and b.is_deleted=0";
			 $nameArray = sql_select($sql_rec);
			 foreach ($nameArray as $row) {
			 	$all_prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
			 	$item_lot_arr[$row[csf("prod_id")]] .= $row[csf("item_lot")] . ",";
			 }

			 $item_grop_id_arr = return_library_array("select id, item_group_id from product_details_master where id in(" . implode(",", $all_prod_id_arr) . ")", 'id', 'item_group_id');

			 $process_array = array();
			 $process_array_remark = array();
			 foreach ($nameArray as $row) {
			 	$process_array[$row[csf("sub_process_id")]] = $row[csf("process_remark")];
			 	$process_array_remark[$row[csf("sub_process_id")]][$row[csf("prod_id")]][$item_grop_id_arr[$row[csf("prod_id")]]][$row[csf("dose_base")]]["comments"] = $row[csf("comments")];
			 	if ($row[csf("sub_process_id")] > 92 && $row[csf("sub_process_id")] < 99) {
			 		$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"] = $row[csf("process_remark")];
			 	}

			 }

			/*$sql_rec_remark="select b.sub_process_id as sub_process_id,b.comments,b.process_remark from pro_recipe_entry_dtls b  where  b.mst_id in($recipe_id) and b.sub_process_id in(93,94,95,96,97,98) and b.status_active=1 and b.is_deleted=0";

			$nameArray_re=sql_select( $sql_rec_remark );
			foreach($nameArray_re as $row)
			{
				$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"]=$row[csf("process_remark")];
			}*/


			/*if($db_type==0)
			{
				$item_lot_arr=return_library_array("select b.prod_id,group_concat(b.item_lot) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot" );
			}
			else
			{
				$item_lot_arr=return_library_array("select b.prod_id,listagg((cast(b.item_lot as varchar2(500))),',') within group (order by b.prod_id) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot" );
			}



			$sql_dtls = "select a.requ_no, a.batch_id, a.recipe_id, b.id,
			b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
			c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id
			from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
			where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id=$data[1] order by b.id, b.seq_no";*/

			$sql_dtls = "(select a.requ_no, a.batch_id, a.recipe_id, b.id,
			b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
			c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id
			from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
			where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id=$data[1])
			union
			(
			select a.requ_no, a.batch_id, a.recipe_id, b.id,
			b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
			null as item_description, null as item_group_id,  null as sub_group_name, null as item_size, null as unit_of_measure,null as avg_rate_per_unit,null as prod_id
			from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b
			where a.id=b.mst_id and b.sub_process in($subprocessforwashin) and   a.id=$data[1]
			) order by id";
			// echo $sql_dtls;//die;
			$sql_result = sql_select($sql_dtls);

			$sub_process_array = array();
			$sub_process_tot_rec_array = array();
			$sub_process_tot_req_array = array();
			$sub_process_tot_value_array = array();

			foreach ($sql_result as $row) {
				$sub_process_tot_rec_array[$row[csf("sub_process")]] += $row[csf("recipe_qnty")];
				$sub_process_tot_req_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
				$sub_process_tot_value_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")] * $row[csf("avg_rate_per_unit")];
				//$sub_process_tot_ratio_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
			}
			$recipe_id=$data_array[0][csf("recipe_id")];
			$ratio_arr=array();$recipe_check_array=array();
			$r=1;
			$prevRatioData=sql_select( "select mst_id,sub_process_id, prod_id,total_liquor, liquor_ratio from pro_recipe_entry_dtls where mst_id in($recipe_id) and status_active=1 and ratio>0");
			/*echo "select mst_id,sub_process_id, prod_id,total_liquor, liquor_ratio from pro_recipe_entry_dtls where mst_id in($recipe_id)";*/
			foreach($prevRatioData as $prevRow)
			{
				/*$sub_process_data=$prevRow[csf('mst_id')].$prevRow[csf("sub_process_id")].$prevRow[csf("prod_id")];
				if (!in_array($sub_process_data,$recipe_check_array))
				{
					$r++;
					$recipe_check_array[]=$sub_process_data;
					$ratio_arr[$prevRow[csf('sub_process_id')]]['ratio']+=$prevRow[csf('liquor_ratio')];
					$ratio_arr[$prevRow[csf('sub_process_id')]]['total_liquor']+=$prevRow[csf('total_liquor')];
				}
				else
				{
					 	$ratio_arr[$prevRow[csf('sub_process_id')]]['ratio']=0;
						$ratio_arr[$prevRow[csf('sub_process_id')]]['total_liquor']=0;
				}*/
				if($process_array_liquor_check[$prevRow[csf("mst_id")]][$prevRow[csf("sub_process_id")]]=="")
				{
						$ratio_arr[$prevRow[csf('sub_process_id')]]['ratio']+=$prevRow[csf('liquor_ratio')];
						$ratio_arr[$prevRow[csf('sub_process_id')]]['total_liquor']+=$prevRow[csf('total_liquor')];
						$process_array_liquor_check[$prevRow[csf("mst_id")]][$prevRow[csf("sub_process_id")]]=888;
				}
			}
			//var_dump($sub_process_tot_req_array);
			$i = 1;
			$k = 1;
			$recipe_qnty_sum = 0;
			$req_qny_edit_sum = 0;
			$recipe_qnty = 0;
			$req_qny_edit = 0;
			$req_value_sum = 0;
			$req_value_grand = 0;
			$recipe_qnty_grand = 0;
			$req_qny_edit_grand = 0;

				foreach ($sql_result as $row) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
				if (!in_array($row[csf("sub_process")], $sub_process_array))
				{
					$sub_process_array[] = $row[csf('sub_process')];
					if ($k != 1) {
						?>
						<tr>
							<td colspan="6" align="right"><strong>Total :</strong></td>
							<td align="right"><strong><?php echo number_format($req_qny_edit_sum, 6, '.', ''); ?></strong></td>
						</tr>
						<?
					}
					$recipe_qnty_sum = 0;
					$req_qny_edit_sum = 0;
					$k++;

				//	if ($row[csf("sub_process")] == 93 || $row[csf("sub_process")] == 94 || $row[csf("sub_process")] == 95 || $row[csf("sub_process")] == 96 || $row[csf("sub_process")] == 97 || $row[csf("sub_process")] == 98) {
					if(in_array($row[csf("sub_process")],$subprocessForWashArr))
					{
						$pro_remark = $process_array_remark[$row[csf("sub_process")]]["wash_comments"];
					}

					?>
					<tr bgcolor="#CCCCCC">
						<th colspan="19">
							<strong><?

							$batch_ratio=explode(":",$data_array[0][csf("ratio")]);
							$lqr_ratio=$batch_ratio[0];
							//$ratio_qty=$lqr_ratio.':'.$ratio_arr[$row[csf('sub_process')]]['ratio'];
							$ratio_qty=$ratio_arr[$row[csf('sub_process')]]['ratio'];
							$total_liq = $ratio_arr[$row[csf('sub_process')]]['total_liquor'];
							$total_liquor_format = number_format($total_liq, 3, '.', '');
							$total_liquor='Total liquor(ltr)' . ": " . $total_liquor_format;
							if ($pro_remark == '') $pro_remark = ''; else $pro_remark .= $pro_remark . ', ';
							echo $dyeing_sub_process[$row[csf("sub_process")]] . ', ' . $pro_remark.'&nbsp;Liquor:Ratio &nbsp;'.$ratio_qty . ', ' .$total_liquor; ?></strong>
						</th>
					</tr>
					<tr bgcolor="#EFEFEF">
						<th width="30">SL</th>
						<th width="100">Item Group</th>
						<th width="270">Item Description</th>
						<th width="40">Ratio</th>
						<th width="60">GPL/%</th>
                        <th width="50">Adj-1</th>
						<th width="50">Adj. Type</th>
						<th width="80">Issue Qty.(KG)</th>
					</tr>
					<?
				}

							/*$iss_qnty=$row[csf("req_qny_edit")]*10000;
							$iss_qnty_kg=floor($iss_qnty/10000);
							$lkg=round($iss_qnty-$iss_qnty_kg*10000);
							$iss_qnty_gm=floor($lkg/10);
							$iss_qnty_mg=$lkg%10;*/
							/*$iss_qnty_gm=substr((string)$iss_qnty[1],0,3);
							$iss_qnty_mg=substr($iss_qnty[1],3);*/

							$req_qny_edit = explode(".", (string)$row[csf("req_qny_edit")]);
							$iss_qnty_kg = $req_qny_edit[0];
							if ($iss_qnty_kg == "") $iss_qnty_kg = 0;

							//$mg=($row[csf("req_qny_edit")]-$iss_qnty_kg)*1000*1000;
							//$iss_qnty_gm=floor($mg/1000);
							//$iss_qnty_mg=round($mg-$iss_qnty_gm*1000);

							//echo "gm=".substr($req_qny_edit[1],0,3)."; mg=".substr($req_qny_edit[1],3,3);


							//$num=(string)$req_qny_edit[1];
							//$next_number= str_pad($num,6,"0",STR_PAD_RIGHT);// ."000";
							//$rem= explode(".",($next_number/1000) );
							//echo $rem ."=";
							$iss_qnty_gm = substr($req_qny_edit[1], 0, 3);//$rem[0]; // floor($mg/1000);
							$iss_qnty_mg = substr($req_qny_edit[1], 3, 3);//$rem[1];//round($mg-$iss_qnty_gm*1000);
							$iss_qnty_gm = str_pad($iss_qnty_gm, 3, "0", STR_PAD_RIGHT);
							$iss_qnty_mg = str_pad($iss_qnty_mg, 3, "0", STR_PAD_RIGHT);

							//echo "mg ".$req_qny_edit[1]."<br>";
							$adjQty = ($row[csf("adjust_percent")] * $row[csf("recipe_qnty")]) / 100;
							$comment = $process_array_remark[$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"];
							?>
							<tbody>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td align="center"><? echo $i; ?></td>
		                        	<td><strong><? echo $group_arr[$row[csf("item_group_id")]]; ?></strong></td>
		                        	<!--<td><? echo $row[csf("sub_group_name")]; ?></td>-->
		                        	<td><? echo $row[csf("item_description")] . ' ' . $row[csf("item_size")]; ?></td>
		                        	<td align="center"><? echo number_format($row[csf("ratio")], 6, '.', ''); ?></td>
		                        	<td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
                                    <td align="center"><? echo $row[csf("adjust_percent")]; ?></td>
		                        	<td align="center"><? echo $increase_decrease[$row[csf("adjust_type")]]; ?></td>
		                        	<td align="right"><? echo number_format($row[csf("req_qny_edit")], 6, '.', ''); ?></td>
		                        </tr>
		                    </tbody>
		                        				<? $i++;
		                        				$recipe_qnty_sum += $row[csf('recipe_qnty')];
		                        				$req_qny_edit_sum += $row[csf('req_qny_edit')];

		                        				$recipe_qnty_grand += $row[csf('recipe_qnty')];
		                        				$req_qny_edit_grand += $row[csf('req_qny_edit')];
		                        			}
		                        			foreach ($sub_process_tot_rec_array as $val_rec) {
		                        				$totval_rec = $val_rec;
		                        			}
		                        			foreach ($sub_process_tot_req_array as $val_req) {
		                        				$totval_req = $val_req;
		                        			}

									//$recipe_qnty_grand +=$val_rec;
									//$req_qny_edit_grand +=$val_req;
									//$req_value_grand +=$req_value;
		                        			?>
		                        			<tr>
		                        				<td colspan="5" align="right"><strong>Total :</strong></td>
                                                <td>&nbsp;</td>
		                        				<td>&nbsp;</td>
		                        				<td align="right"><strong><?php echo number_format($totval_req, 6, '.', ''); ?></strong></td>
		                        			</tr>
		                        			<tr>
		                        				<td colspan="5" align="right"><strong> Grand Total :</strong></td>
                                                <td>&nbsp;</td>
		                        				<td>&nbsp;</td>
		                        				<td align="right"><strong><?php echo number_format($req_qny_edit_grand, 6, '.', ''); ?></strong></td>
		                        			</tr>
		                        		</table>
		                        		<br>
		                        		<?
		                        		echo signature_table(15, $data[0], "900px");
		                        		?>
		                        	</div>
		                        </div>
		                        <?
		                        exit();
		}
		else  // cancel to open with issue value
		{
			$sql = "select a.id, a.requ_no, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id
			from dyes_chem_issue_requ_mst a where a.id='$data[1]' and a.company_id='$data[0]'";
			$dataArray = sql_select($sql);

			$recipe_id = $dataArray[0][csf('recipe_id')];

			$receipe_arr = array();
			$receipeData = sql_select("select b.id as batch_id,b.entry_form,a.id,a.labdip_no, a.total_liquor from pro_recipe_entry_mst a,pro_batch_create_mst b where a.batch_id=b.id and a.status_active=1 and a.is_deleted=0 and a.id in($recipe_id)");
			foreach ($receipeData as $row) {
				//$receipe_arr[$row[csf("id")]] = $row[csf("labdip_no")];
				$entry_form_arr[$row[csf("batch_id")]] = $row[csf("entry_form")];
			}


			$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
			$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
			$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
			$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
			//$entry_form_arr = return_library_array("select id, entry_form from pro_batch_create_mst", "id", "entry_form");
			//$order_id_arr=return_library_array( "select id, booking_id from  pro_recipe_entry_mst", "id", "booking_id");
			//$order_no_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
			//$job_no_arr=return_library_array( "select id, job_no from wo_booking_mst", "id", "job_no");
			$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
			$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
			$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");

			$lib_floor = sql_select("select floor_id from lib_machine_name where id='" . $dataArray[0][csf("machine_id")] . "'");
			$floor_name = return_field_value("floor_name", "lib_prod_floor", "id='" . $lib_floor[0][csf('floor_id')] . "'");

			$batch_weight = 0;
			if ($db_type == 0) {
				$data_array = sql_select("select group_concat(buyer_id) as buyer_id,group_concat(id) as recipe_id, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor, group_concat(concat_ws(':',batch_ratio,liquor_ratio) order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, group_concat(case when entry_form<>60 then batch_id end) as batch_id_rec_main, group_concat(labdip_no) as labdip_no from pro_recipe_entry_mst where id in($recipe_id)");
			} else {
				$data_array = sql_select("select listagg(cast(buyer_id as varchar(4000)),',') within group (order by buyer_id) as buyer_id, listagg(cast(id as varchar(4000)),',') within group (order by id) as recipe_id, sum(total_liquor) as total_liquor, listagg(cast(batch_ratio || ':' || liquor_ratio as varchar(4000)),',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(cast(case when entry_form<>60 then batch_id end as varchar(4000)),',') within group (order by batch_id) as batch_id_rec_main, listagg(cast(batch_id as varchar(4000)),',') within group (order by batch_id) as batch_id, listagg(cast(labdip_no as varchar(4000)), ',') within group (order by id) as labdip_no from pro_recipe_entry_mst where id in($recipe_id)");

				/*$data_array = sql_select("select listagg(buyer_id,',') within group (order by buyer_id) as buyer_id,listagg(id,',') within group (order by id) as recipe_id, listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor, listagg(batch_ratio || ':' || liquor_ratio,',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(case when entry_form<>60 then batch_id end,',') within group (order by batch_id) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");*/	// old query
			}

			$batch_weight = $data_array[0][csf("batch_weight")];
			$batch_id = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id")])));

			$batch_id_rec_main = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id_rec_main")])));
			if ($batch_id_rec_main == "") $batch_id_rec_main = 0;

			$booking_no_sql="select distinct booking_no from pro_batch_create_mst where id in($batch_id) and is_deleted=0 and status_active=1 ";
			$booking_no_arr=sql_select($booking_no_sql);
			foreach ($booking_no_arr as $row)
			{
		        $booking_arr[$row[csf("booking_no")]]=$row[csf("booking_no")];
		    }

			if ($db_type == 0) {
				$batchdata_array = sql_select("select group_concat(batch_no) as batch_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(distinct color_id) as color_id from pro_batch_create_mst where id in($batch_id)");
			} else {
				$batchdata_array = sql_select("select listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, listagg(color_id ,',') within group (order by id) as color_id, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight
					from pro_batch_create_mst where id in($batch_id)");
			}


			$po_no = '';
			$job_no = '';
			$buyer_name = '';
			$style_ref_no = '';
			foreach (explode(",", $data_array[0][csf("batch_id")]) as $b_id) {
				if ($entry_form_arr[$b_id] == 36) {
					//echo "select distinct b.order_no,b.cust_style_ref, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(".$b_id.") ";
					$po_data = sql_select("select distinct b.order_no,b.cust_style_ref, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ");
					foreach ($po_data as $row) {
						$po_no .= $row[csf('order_no')] . ",";
						$job_no .= $row[csf('subcon_job')] . ",";
						if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
						$buyer_name .= $buyer_library[$row[csf('party_id')]] . ",";
					}
				} else {
					//echo "select distinct b.po_number, c.job_no,c.style_ref_no, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(".$b_id.") ";
					$po_data = sql_select("select distinct b.po_number, c.job_no,c.style_ref_no, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ");
					$job_nos="";
					foreach ($po_data as $row) {
						$po_no .= $row[csf('po_number')] . ",";
						$job_no .= $row[csf('job_no')] . ",";
						//$job_nos .= $row[csf('job_no')] . ",";
						if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
						if ($job_nos == '') $job_nos ="'".$row[csf('job_no')]."'"; else $job_nos.= ","."'".$row[csf('job_no')]."'";
						//$buyer_name.=$buyer_library[$row[csf('buyer_name')]].",";
					}
					foreach (explode(",", $data_array[0][csf("buyer_id")]) as $buyer_id) {
						$buyer_name .= $buyer_library[$buyer_id] . ",";
					}

				}
			}
			$jobNos=rtrim($job_nos,',');
			$jobnos=rtrim($job_no,',');

			//echo "select job_no, exchange_rate from wo_pre_cost_mst $job_nos_con";
			if($job_nos!='')
			{
				if($job_nos!='') $job_nos_con="where  job_no in(".$job_nos.")";//else $job_nos_con="where  job_no in('')";
				//echo "select job_no, exchange_rate from wo_pre_cost_mst $job_nos_con";
				$exchange_rate_arr = return_library_array("select job_no, exchange_rate from wo_pre_cost_mst $job_nos_con", 'job_no', 'exchange_rate');
			}
			//print_r($exchange_rate_arr);
			if($job_nos!='')
			{
			$condition= new condition();
			 $condition->company_name("=$data[0]");
			 if(str_replace("'","",$job_nos) !=''){
				  $condition->job_no("in($job_nos)");
			 }
			 $condition->init();

			$conversion= new conversion($condition);
			//echo $conversion->getQuery(); die;
			$conversion_qty_arr_process=$conversion->getQtyArray_by_jobAndProcess();
			$conversion_costing_arr_process=$conversion->getAmountArray_by_jobAndProcess();
			}
			// print_r($conversion_costing_arr_process);
			$dyeing_charge_arr=array(25,26,30,31,32,33,34,39,60,62,63,64,65,66,67,68,69,70,71,82,83,84,85,86,89,90,91,92,93,94,129,135,136,137,138,139,140,141,142,143,146);
			$jobnos=array_unique(explode(",",$jobnos));
			$tot_avg_dyeing_charge=0;$avg_dyeing_charge=0;$tot_dyeing_qty=$tot_dyeing_cost=0;//$tot_dyeing_cost=0;$tot_dyeing_qty=0;
			foreach($jobnos as $job)
			{
				$exchange_rate=$exchange_rate_arr[$job];

				foreach($dyeing_charge_arr as $process_id)
				{

					$dyeing_cost=array_sum($conversion_costing_arr_process[$job][$process_id]);
					//echo $job.'=='.$dyeing_cost.'='.$exchange_rate.'<br/>';
					$totdyeing_cost=$dyeing_cost;


					if($totdyeing_cost>0)
					{

						$tot_dyeing_qty+=array_sum($conversion_qty_arr_process[$job][$process_id]);
						$tot_dyeing_cost+=$dyeing_cost;
						//echo $tot_dyeing_cost.'='.$tot_dyeing_qty.'**'.$exchange_rate.'**'.$process_id.'<br>';
						//$avg_dyeing_charge+=$tot_dyeing_cost/$tot_dyeing_qty;
					}
				}
				//echo $tot_dyeing_cost.'<br>'.$tot_dyeing_qty;
				$avg_dyeing_charge+=$tot_dyeing_cost/$tot_dyeing_qty;
				$tot_avg_dyeing_charge+=$avg_dyeing_charge*$exchange_rate;
			}

			//echo $avg_dyeing_charge.'='.$tot_dyeing_cost.'='.$dyeing_cost;
			//echo $style_ref_no;
			/*if ($db_type==0)
			{
				$sql_yarn_dtls="select b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
			}
			elseif ($db_type==2)
			{
				$sql_yarn_dtls="select b.po_breakdown_id, b.prod_id, listagg((cast(a.yarn_lot as varchar2(500))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(a.brand_id,',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(500))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
			}*/

			$roll_maintained = return_field_value("fabric_roll_level", "variable_settings_production", "company_name ='$data[0]' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");

			if ($db_type == 0) {
				$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
			}
			else if ($db_type == 2)
			{
				/*echo $sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, listagg((cast(a.yarn_lot as varchar2(4000))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(a.brand_id,',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(4000))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id";*/

				$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, a.yarn_lot, a.brand_id, a.yarn_count
					from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b
					where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
					group by b.po_breakdown_id, b.prod_id , a.yarn_lot, a.brand_id, a.yarn_count";
			}

			$yarn_dtls_array = array();
			$result_sql_yarn_dtls = sql_select($sql_yarn_dtls);
			foreach ($result_sql_yarn_dtls as $row)
			{
				//$yarn_lot = implode(", ", array_unique(explode(",", $row[csf('yarn_lot')])));
				$yarn_id = array_unique(explode(",", $row[csf('yarn_lot')]));
				$yarn_lot = "";
				foreach ($yarn_id as $val) {
					if ($yarn_lot == "") $yarn_lot = $val; else $yarn_lot .= ", " . $val;
				}

				$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
				$brand_name = "";
				foreach ($brand_id as $val) {
					if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= ", " . $brand_arr[$val];
				}

				$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
				$count_name = "";
				foreach ($yarn_count as $val) {
					if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
				}
				$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['yarn_lot'] = $yarn_lot;
				$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['brand_name'] = $brand_name;
				$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['count_name'] = $count_name;
			}
			//var_dump($yarn_dtls_array);
			$po_no = implode(", ", array_unique(explode(",", substr($po_no, 0, -1))));
			$job_no = implode(", ", array_unique(explode(",", substr($job_no, 0, -1))));
			$buyer_name = implode(",", array_unique(explode(",", substr($buyer_name, 0, -1))));

			$color_id = array_unique(explode(",", $batchdata_array[0][csf('color_id')]));
			$color_name = '';
			foreach ($color_id as $color) {
				$color_name .= $color_arr[$color] . ",";
			}
			$color_name = substr($color_name, 0, -1);
			//var_dump($recipe_color_arr);

			//$recipe_id=$data_array[0][csf("recipe_id")];
			$recipe_arr=sql_select("select a.working_company_id,a.company_id, b.batch_weight from pro_recipe_entry_mst a, pro_batch_create_mst b where a.batch_id=b.id and a.id=$recipe_id");
			$owner_company_id=$recipe_arr[0][csf('company_id')];
			$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
			$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
			$group_id=$nameArray[0][csf('group_id')];
			?>
			<div style="width:950px;">
			    <table width="950" cellspacing="0" align="center">

					<tr>
						<td colspan="10" align="center" style="font-size:xx-large">
							<strong><? echo $company_library[$data[0]];//$company_library[$data[0]]; ?></strong></td>
					</tr>
					<tr class="form_caption">
						<td colspan="6" align="center" style="font-size:14px">
							<?

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
						<td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Report</u></strong></td>
					</tr>
				</table>
				<table align="center" cellspacing="0" width="950" border="1" rules="all" class="rpt_table">
					<tr>
						<td border="1"><strong>Booking No </strong></td>
						<td><? echo implode(',',$booking_arr); ?></td>
						<td><strong>Order No</strong></td>
						<td><? echo $po_no; ?></td>
						<td><strong>Req. Date</strong></td>
						<td><? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
					</tr>
					<tr>
						<td><strong>Buyer</strong></td>
						<td><? echo implode(",", array_unique(explode(",", $buyer_name))); ?></td>
						<td><strong>Batch No</strong></td>
						<td><? echo $batchdata_array[0][csf('batch_no')]; ?></td>
						<td><strong>Batch Weight</strong></td>
						<td><? $tot_batch_weight=$batch_weight+$batchdata_array[0][csf('batch_weight')];echo number_format($tot_batch_weight,4); ?></td>
					</tr>
					<tr>
						<td><strong>Color</strong></td>
						<td><? echo $color_name; ?></td>
						<td><strong>Lab Dip</strong></td>
						<td>
    						<?
    						$labdip_no = '';
    						$recipe_ids = explode(",", $data_array[0][csf("recipe_id")]);
    						foreach ($recipe_ids as $recp_id) {
    							$labdip_no .= $receipe_arr[$recp_id] . ",";
    						}
    						echo chop($labdip_no, ',');
    						// or-> echo $data_array[0][csf("labdip_no")];
    						?>
    					</td>
						<td><strong>Machine No</strong></td>
						<td>
							<?
							$machine_data = sql_select("select machine_no, floor_id from lib_machine_name where id='" . $dataArray[0][csf("machine_id")] . "'");
							echo $machine_data[0][csf('machine_no')];
							?>
						</td>
					</tr>
					<tr>
                        <td><strong>Style Ref.</strong></td>
						<td ><p><? echo implode(",", array_unique(explode(",", $style_ref_no))); ?></p></td>
						<td width="90"><strong>Req. ID </strong></td>
						<td width="160px"><? echo $dataArray[0][csf('requ_no')]; ?></td>
						<td border="1"><strong>Job No </strong></td>
						<td><? echo $job_no; ?></td>
					</tr>
					<tr>
                        <td><strong> Recipe No</strong></td>
						<td ><p><? echo $dataArray[0][csf('recipe_id')]; ?></p></td>
						<td width="90"><strong> Floor Name  </strong></td>
						<td width="160px"><? echo $floor_name; ?></td>
						<td border="1"></td>
						<td></td>
					</tr>
				</table>
		         <script type="text/javascript" src="../../../js/jquery.js"></script>
				 <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
		         <script>

		                function generateBarcode( valuess ){

		                        var value = valuess;
		                        var btype = 'code39';
		                        var renderer ='bmp';
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
		                        $("#show_barcode_image").show().barcode(value, btype, settings);
		                    }
		                   generateBarcode('<? echo $dataArray[0][csf('requ_no')]; ?>');
		         </script>


					<br>
					<?
					$batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
					$j = 1;
					$entryForm = $entry_form_arr[$batch_id_qry[0]];
					?>
					<table width="950" cellspacing="0" align="center" class="rpt_table" border="1" rules="all">
						<thead bgcolor="#dddddd" align="center">
							<?
							if ($entryForm == 74) {
								?>
								<tr bgcolor="#CCCCFF">
									<th colspan="4" align="center"><strong>Fabrication</strong></th>
								</tr>
								<tr>
									<th width="50">SL</th>
									<th width="350">Gmts. Item</th>
									<th width="110">Gmts. Qty</th>
									<th>Batch Qty.</th>
								</tr>
								<?
							} else {
								?>
								<tr bgcolor="#CCCCFF">
									<th colspan="8" align="center"><strong>Fabrication</strong></th>
								</tr>
								<tr>
									<th width="30">SL</th>
									<th width="100">Dia/ W. Type</th>
									<th width="100">Yarn Lot</th>
									<th width="100">Brand</th>
									<th width="100">Count</th>
									<th width="300">Constrution & Composition</th>
									<th width="70">Gsm</th>
									<th width="70">Dia</th>
								</tr>
								<?
							}
							?>
						</thead>
						<tbody>
							<?

							$b_id = implode(",", explode(",", $dataArray[0][csf('batch_id')])) ;

							/*foreach ($batch_id_qry as $b_id)
							{}*/
							if ($roll_maintained==1)
							{
								if ($db_type==0)
								{
									$batch_query="SELECT b.item_description, b.width_dia_type, b.prod_id, sum(b.roll_no) as gmts_qty, sum(b.batch_qnty) as batch_qnty, group_concat(d.yarn_lot) as yarn_lot, group_concat(d.yarn_count as yarn_count, group_concat(d.brand_id) as brand_id
									from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d
									where a.id=b.mst_id and b.barcode_no=c.barcode_no and c.dtls_id=d.id and a.company_id=$data[0] and a.id in($b_id) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and
									a.is_deleted=0 and c.entry_form in(2,22)
									group by b.item_description, b.width_dia_type, b.prod_id";
								}
								else
								{
									$batch_query="SELECT b.item_description, b.width_dia_type, b.prod_id, sum(b.roll_no) as gmts_qty, sum(b.batch_qnty) as batch_qnty,
									LISTAGG(CAST(d.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) as yarn_lot,
									LISTAGG(CAST(d.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_count) as yarn_count,
									LISTAGG(CAST(d.brand_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.brand_id) as brand_id, b.gsm, b.grey_dia
									from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d
									where a.id=b.mst_id and b.barcode_no=c.barcode_no and c.dtls_id=d.id and a.company_id=$data[0] and a.id in($b_id) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and
									a.is_deleted=0 and c.entry_form in(2,22)
									group by b.item_description, b.width_dia_type, b.prod_id,b.gsm,b.grey_dia";
								}
							}
							else
							{
								$batch_query = "SELECT po_id, prod_id, item_description, width_dia_type, sum(roll_no)  as gmts_qty, sum(batch_qnty) as  batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by po_id, prod_id, item_description, width_dia_type ";
							}
							$result_batch_query = sql_select($batch_query);
							foreach ($result_batch_query as $rows)
							{
								if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								$fabrication_full = $rows[csf("item_description")];
								$fabrication = explode(',', $fabrication_full);
								if ($roll_maintained==1)
								{
									$yarn_id = array_unique(explode(",", $rows[csf('yarn_lot')]));
									$yarnLot = "";
									foreach ($yarn_id as $val) {
										if ($yarnLot == "") $yarnLot = $val; else $yarnLot .= ", " . $val;
									}
									$yarnLot=rtrim($yarnLot, ", ");
									$brand_id = array_unique(explode(",", $rows[csf('brand_id')]));
									$brandName = "";
									foreach ($brand_id as $val) {
										if ($brandName == "") $brandName = $brand_arr[$val]; else $brandName .= ", " . $brand_arr[$val];
									}

									$yarn_count = array_unique(explode(",", $rows[csf('yarn_count')]));
									$countName = "";
									foreach ($yarn_count as $val) {
										if ($countName == "") $countName = $count_arr[$val]; else $countName .= ", " . $count_arr[$val];
									}
								}
								else
								{
									$yarnLot=$yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['yarn_lot'];
									$brandName=$yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['brand_name'];
									$countName=$yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['count_name'];
								}

								if ($entry_form_arr[$b_id] == 36)
								{
									?>
									<tr bgcolor="<? echo $bgcolor; ?>">
										<td align="center"><? echo $j; ?></td>
										<td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
	                            		<td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['yarn_lot']; ?></td>
	                            		<td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['brand_name']; ?></td>
	                            		<td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['count_name']; ?></td>
	                            		<td align="left"><? echo $fabrication[0]; ?></td>
	                            		<td align="center"><? echo $rows[csf("gsm")]; ?></td>
	                            		<td align="center"><? echo $rows[csf("grey_dia")]; ?></td>
	                            	</tr>
	                            	<?
	                        	}
	                        	else if ($entry_form_arr[$b_id] == 74)
	                        	{
		                        	?>
		                        	<tr bgcolor="<? echo $bgcolor; ?>">
		                        		<td align="center"><? echo $j; ?></td>
		                        		<td align="left"><? echo $garments_item[$rows[csf('prod_id')]]; ?></td>
		                        		<td align="right"><? echo $rows[csf('gmts_qty')]; ?></td>
		                        		<td align="right"><? echo number_format($rows[csf('batch_qnty')], 2); ?></td>
		                        	</tr>
		                        	<?
	                        	}
	                        	else
	                        	{
									//echo $entry_form_arr[$b_id].'aaa';
		                        	?>
		                        	<tr bgcolor="<? echo $bgcolor; ?>">
		                        		<td align="center"><? echo $j; ?></td>
		                        		<td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
		                        		<td width="100" align="left"><p style="word-wrap:break-word;"><? echo $yarnLot; ?></p></td>
		                        		<td align="left"><? echo $brandName; ?></td>
		                        		<td align="left"><? echo $countName; ?></td>
		                        		<td align="left"><? echo $fabrication[0] . ", " . $fabrication[1]; ?></td>
		                        		<td align="center"><? echo $fabrication[2]; ?></td>
		                        		<td align="center"><? echo $fabrication[3]; ?></td>
		                        	</tr>
		                        	<?
	                        	}
	                        	$j++;
	                    	}



		                ?>
		            </tbody>
		        </table>

		        <div style="width:950px; margin-top:10px">
		        	<table align="center" cellspacing="0" width="950" border="1" rules="all" class="rpt_table">
		        		<thead bgcolor="#dddddd" align="center">
		        			<tr bgcolor="#CCCCFF">
		        				<th colspan="19" align="center"><strong>Dyes And Chemical Issue Requisition</strong></th>
		        			</tr>
		        		</thead>
		        		<?


			/*$sql_rec="select a.id, a.item_group_id, b.sub_process_id as sub_process_id, b.prod_id, b.dose_base, b.adj_type, b.comments, b.process_remark
			 from pro_recipe_entry_dtls b left join product_details_master a on b.prod_id=a.id and a.item_category_id in(5,6,7,23)
			 where b.mst_id in($recipe_id)  and b.status_active=1 and b.is_deleted=0 ";*/

			 $group_arr = return_library_array("select id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0", 'id', 'item_name');
			 $sql_rec = "select b.sub_process_id as sub_process_id, b.prod_id, b.dose_base, b.adj_type, b.comments, b.process_remark, b.item_lot
			 from pro_recipe_entry_dtls b
			 where b.mst_id in($recipe_id)  and b.status_active=1 and b.is_deleted=0";
			 $nameArray = sql_select($sql_rec);
			 foreach ($nameArray as $row) {
			 	$all_prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
			 	$item_lot_arr[$row[csf("prod_id")]] .= $row[csf("item_lot")] . ",";
			 }

			 $item_grop_id_arr = return_library_array("select id, item_group_id from product_details_master where id in(" . implode(",", $all_prod_id_arr) . ")", 'id', 'item_group_id');

			 $process_array = array();
			 $process_array_remark = array();
			 foreach ($nameArray as $row) {
			 	$process_array[$row[csf("sub_process_id")]] = $row[csf("process_remark")];
			 	$process_array_remark[$row[csf("sub_process_id")]][$row[csf("prod_id")]][$item_grop_id_arr[$row[csf("prod_id")]]][$row[csf("dose_base")]]["comments"] = $row[csf("comments")];
			 	if ($row[csf("sub_process_id")] > 92 && $row[csf("sub_process_id")] < 99) {
			 		$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"] = $row[csf("process_remark")];
			 	}

			 }

			/*$sql_rec_remark="select b.sub_process_id as sub_process_id,b.comments,b.process_remark from pro_recipe_entry_dtls b  where  b.mst_id in($recipe_id) and b.sub_process_id in(93,94,95,96,97,98) and b.status_active=1 and b.is_deleted=0";

			$nameArray_re=sql_select( $sql_rec_remark );
			foreach($nameArray_re as $row)
			{
				$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"]=$row[csf("process_remark")];
			}*/


			/*if($db_type==0)
			{
				$item_lot_arr=return_library_array("select b.prod_id,group_concat(b.item_lot) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot" );
			}
			else
			{
				$item_lot_arr=return_library_array("select b.prod_id,listagg((cast(b.item_lot as varchar2(500))),',') within group (order by b.prod_id) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot" );
			}



			$sql_dtls = "select a.requ_no, a.batch_id, a.recipe_id, b.id,
			b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
			c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id
			from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
			where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id=$data[1] order by b.id, b.seq_no";*/

			$sql_dtls = "(select a.requ_no, a.batch_id, a.recipe_id, b.id,
			b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
			c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id
			from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
			where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id=$data[1])
			union
			(
			select a.requ_no, a.batch_id, a.recipe_id, b.id,
			b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
			null as item_description, null as item_group_id,  null as sub_group_name, null as item_size, null as unit_of_measure,null as avg_rate_per_unit,null as prod_id
			from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b
			where a.id=b.mst_id and b.sub_process in($subprocessforwashin) and   a.id=$data[1]
			) order by id";
			// echo $sql_dtls;//die;
			$sql_result = sql_select($sql_dtls);

			$sub_process_array = array();
			$sub_process_tot_rec_array = array();
			$sub_process_tot_req_array = array();
			$sub_process_tot_value_array = array();

			foreach ($sql_result as $row) {
				$sub_process_tot_rec_array[$row[csf("sub_process")]] += $row[csf("recipe_qnty")];
				$sub_process_tot_req_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
				$sub_process_tot_value_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")] * $row[csf("avg_rate_per_unit")];
				//$sub_process_tot_ratio_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
			}
			$recipe_id=$data_array[0][csf("recipe_id")];
			$ratio_arr=array();
			$prevRatioData=sql_select( "select sub_process_id, total_liquor, liquor_ratio from pro_recipe_entry_dtls where mst_id=$recipe_id and status_active=1 and ratio>0");
			foreach($prevRatioData as $prevRow)
			{
				$ratio_arr[$prevRow[csf('sub_process_id')]]['ratio']=$prevRow[csf('liquor_ratio')];
				$ratio_arr[$prevRow[csf('sub_process_id')]]['total_liquor']=$prevRow[csf('total_liquor')];
			}
						//var_dump($sub_process_tot_req_array);
			$i = 1;
			$k = 1;
			$recipe_qnty_sum = 0;
			$req_qny_edit_sum = 0;
			$recipe_qnty = 0;
			$req_qny_edit = 0;
			$req_value_sum = 0;
			$req_value_grand = 0;
			$recipe_qnty_grand = 0;
			$req_qny_edit_grand = 0;

			foreach ($sql_result as $row) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
				if (!in_array($row[csf("sub_process")], $sub_process_array))
				{
					$sub_process_array[] = $row[csf('sub_process')];
					if ($k != 1) {
						?>
						<tr>
							<td colspan="5" align="right"><strong>Total :</strong></td>
							<td>&nbsp;</td>
							<td align="right"><strong><?php echo number_format($req_qny_edit_sum, 6, '.', ''); ?></strong></td>
							<td>&nbsp;</td>
							<td align="right"><strong><?php echo number_format($req_value_sum, 6, '.', ''); ?></strong></td>
						</tr>
						<?
					}
					$recipe_qnty_sum = 0;
					$req_qny_edit_sum = 0;
					$req_value_sum = 0;
					$k++;

					//if ($row[csf("sub_process")] == 93 || $row[csf("sub_process")] == 94 || $row[csf("sub_process")] == 95 || $row[csf("sub_process")] == 96 || $row[csf("sub_process")] == 97 || $row[csf("sub_process")] == 98)

				 if(in_array($row[csf("sub_process")],$subprocessForWashArr))
				 {
						$pro_remark = $process_array_remark[$row[csf("sub_process")]]["wash_comments"];
				 }

					?>
					<th colspan="19">
							<strong><?

							$batch_ratio=explode(":",$data_array[0][csf("ratio")]);
							$lqr_ratio=$batch_ratio[0];
							$ratio_qty=$lqr_ratio.':'.$ratio_arr[$row[csf('sub_process')]]['ratio'];
							$total_liq = $ratio_arr[$row[csf('sub_process')]]['total_liquor'];
							$total_liquor_format = number_format($total_liq, 3, '.', '');
							$total_liquor='Total liquor(ltr)' . ": " . $total_liquor_format;
							if ($pro_remark == '') $pro_remark = ''; else $pro_remark .= $pro_remark . ', ';
							echo $dyeing_sub_process[$row[csf("sub_process")]] . ', ' . $pro_remark.'&nbsp;Liquor:Ratio &nbsp;'.$ratio_qty . ', ' .$total_liquor; ?></strong>
						</th>
					</tr>
					<tr bgcolor="#EFEFEF">
						<th width="30">SL</th>
						<th width="100">Item Group</th>
						<th width="170">Item Description</th>
						<th width="40">Ratio</th>
						<th width="60">GPL/%</th>
						<th width="50">Adj-1</th>
                        <th width="50">Adj. Type</th>
						<th width="80">Issue Qty.(KG)</th>
						<th width="50">Avg. Rate</th>
						<th width="60">Issue Value</th>
					</tr>
					<?
				}

							/*$iss_qnty=$row[csf("req_qny_edit")]*10000;
							$iss_qnty_kg=floor($iss_qnty/10000);
							$lkg=round($iss_qnty-$iss_qnty_kg*10000);
							$iss_qnty_gm=floor($lkg/10);
							$iss_qnty_mg=$lkg%10;*/
							/*$iss_qnty_gm=substr((string)$iss_qnty[1],0,3);
							$iss_qnty_mg=substr($iss_qnty[1],3);*/

							$req_qny_edit = explode(".", (string)$row[csf("req_qny_edit")]);
							$iss_qnty_kg = $req_qny_edit[0];
							if ($iss_qnty_kg == "") $iss_qnty_kg = 0;

							//$mg=($row[csf("req_qny_edit")]-$iss_qnty_kg)*1000*1000;
							//$iss_qnty_gm=floor($mg/1000);
							//$iss_qnty_mg=round($mg-$iss_qnty_gm*1000);

							//echo "gm=".substr($req_qny_edit[1],0,3)."; mg=".substr($req_qny_edit[1],3,3);


							//$num=(string)$req_qny_edit[1];
							//$next_number= str_pad($num,6,"0",STR_PAD_RIGHT);// ."000";
							//$rem= explode(".",($next_number/1000) );
							//echo $rem ."=";
							$iss_qnty_gm = substr($req_qny_edit[1], 0, 3);//$rem[0]; // floor($mg/1000);
							$iss_qnty_mg = substr($req_qny_edit[1], 3, 3);//$rem[1];//round($mg-$iss_qnty_gm*1000);
							$iss_qnty_gm = str_pad($iss_qnty_gm, 3, "0", STR_PAD_RIGHT);
							$iss_qnty_mg = str_pad($iss_qnty_mg, 3, "0", STR_PAD_RIGHT);

							//echo "mg ".$req_qny_edit[1]."<br>";
							$adjQty = ($row[csf("adjust_percent")] * $row[csf("recipe_qnty")]) / 100;
							$comment = $process_array_remark[$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"];
							?>
							<tbody>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td align="center"><? echo $i; ?></td>
		                        	<td><strong><? echo $group_arr[$row[csf("item_group_id")]]; ?></strong></td>
		                        	<!--<td><? echo $row[csf("sub_group_name")]; ?></td>-->
		                        	<td><? echo $row[csf("item_description")] . ' ' . $row[csf("item_size")]; ?></td>
		                        	<td align="center"><? echo number_format($row[csf("ratio")], 6, '.', ''); ?></td>
		                        	<td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
		                        	<td align="center"><? echo $row[csf("adjust_percent")]; ?></td>
                                    <td align="center"><? echo $increase_decrease[$row[csf("adjust_type")]]; ?></td>
		                        	<td align="right"><? echo number_format($row[csf("req_qny_edit")], 6, '.', ''); ?></td>
                					<td align="right"><? echo number_format($row[csf("avg_rate_per_unit")], 6, '.', ''); ?></td>
                					<td align="right"><? $req_value = $row[csf("req_qny_edit")] * $row[csf("avg_rate_per_unit")];
                						echo number_format($req_value, 6, '.', ''); ?></td>
                					</tr>
                				</tbody>
                				<? $i++;
                				$recipe_qnty_sum += $row[csf('recipe_qnty')];
                				$req_qny_edit_sum += $row[csf('req_qny_edit')];
                				$req_value_sum += $req_value;

                				$recipe_qnty_grand += $row[csf('recipe_qnty')];
                				$req_qny_edit_grand += $row[csf('req_qny_edit')];
                				$req_value_grand += $req_value;
                			}
                			foreach ($sub_process_tot_rec_array as $val_rec) {
                				$totval_rec = $val_rec;
                			}
                			foreach ($sub_process_tot_req_array as $val_req) {
                				$totval_req = $val_req;
                			}
                			foreach ($sub_process_tot_value_array as $req_value) {
                				$tot_req_value = $req_value;
                			}

									//$recipe_qnty_grand +=$val_rec;
									//$req_qny_edit_grand +=$val_req;
									//$req_value_grand +=$req_value;
		                        			?>
		                        			<tr>
		                        				<td colspan="5" align="right"><strong>Total :</strong></td>
                                                <td>&nbsp;</td>
		                        				<td>&nbsp;</td>
		                        				<td align="right"><strong><?php echo number_format($totval_req, 6, '.', ''); ?></strong></td>
		                        				<td>&nbsp;</td>
		                        				<td align="right"><strong><?php echo number_format($tot_req_value, 6, '.', ''); ?></strong></td>
		                        			</tr>
		                        			<tr>
		                        				<td colspan="5" align="right"><strong> Grand Total :</strong></td>
                                                <td>&nbsp;</td>
		                        				<td>&nbsp;</td>
		                        				<td align="right"><strong><?php echo number_format($req_qny_edit_grand, 6, '.', ''); ?></strong></td>
		                        				<td>&nbsp;</td>
		                        				<td align="right"><strong><?php echo number_format($req_value_grand, 6, '.', ''); ?></strong></td>
		                        			</tr>
		                        			<tr>
		                        				<td colspan="8" align="right"><strong> Cost Per Kg :</strong></td>
		                        				<td colspan="2"
		                        				align="right"><strong><?php
												$cost_per_kg=$req_value_grand / ($batch_weight + $batchdata_array[0][csf('batch_weight')]);
												echo number_format($cost_per_kg, 6, '.', ''); ?></strong></td>
		                        			</tr>
		                                    <tr>
		                        				<td colspan="8" align="right"><strong> Total Revenue :</strong></td>
		                        				<td colspan="2"
		                        				align="right" title="<? echo 'Dyeing Charge='.$tot_avg_dyeing_charge; ?>"><strong><?php $total_revenue=$tot_batch_weight*$tot_avg_dyeing_charge;echo number_format($total_revenue, 6, '.', ''); ?></strong></td>
		                        			</tr>
		                                     <tr>
		                        				<td colspan="8" align="right"><strong> Chemical Cost as % of Revenue :</strong></td>
		                        				<td colspan="2"
		                        				align="right"><strong><?php echo number_format(($req_value_grand/$total_revenue)*100,6, '.', ''); ?></strong></td>
		                        			</tr>
		                        		</table>
		                        		<br>
		                        		<?
		                        		echo signature_table(15, $data[0], "900px");
		                        		?>
		                        	</div>
		                        </div>
		                        <?
		                        exit();
		}
	}
}

if ($action == "chemical_dyes_issue_requisition_print3")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$sql = "select a.id, a.requ_no, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id from dyes_chem_issue_requ_mst a where a.id='$data[1]' and a.company_id='$data[0]'";
	$dataArray = sql_select($sql);
	$recipe_id = $dataArray[0][csf('recipe_id')];
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	//$entry_form_arr = return_library_array("select id, entry_form from pro_batch_create_mst", "id", "entry_form");
	//$batch_against = return_library_array("select id, batch_against from pro_batch_create_mst", "id", "batch_against");
	//$batch_booking = return_library_array("select id, booking_no from pro_batch_create_mst where batch_against=3", "id", "booking_no");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");
	$sub_proces_arr = return_library_array("SELECT (a.batch_qty*b.liquor_ratio) as water, b.sub_process_id from pro_recipe_entry_mst a, pro_recipe_entry_dtls b where a.id=b.mst_id and a.id='" . $dataArray[0][csf("recipe_id")] . "'", "sub_process_id", "water");

	$batch_weight = 0;
	if ($db_type == 0) {
		$data_array = sql_select("select group_concat(buyer_id) as buyer_id,group_concat(id) as recipe_id, group_concat(dyeing_re_process) as dyeing_re_process, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor, group_concat(concat_ws(':',batch_ratio,liquor_ratio) order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, group_concat(case when entry_form<>60 then batch_id end) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
	} else {
		$data_array = sql_select("select listagg(buyer_id,',') within group (order by buyer_id) as buyer_id, listagg(buyer_id,',') within group (order by buyer_id) as buyer_id,listagg(id,',') within group (order by id) as recipe_id, listagg(dyeing_re_process,',') within group (order by id) as dyeing_re_process, listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor, listagg(batch_ratio || ':' || liquor_ratio,',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(case when entry_form<>60 then batch_id end,',') within group (order by batch_id) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");

	}

	$batch_weight = $data_array[0][csf("batch_weight")];
	$batch_id = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id")])));

	$sql_batch=sql_select("select b.id,b.entry_form, b.booking_no,b.batch_against from pro_batch_create_mst b where  where id in($batch_id)");
	foreach ($sql_batch as $row)
	{
		$entry_form_arr[$row[csf('id')]]=$row[csf('entry_form')];
		$batch_booking[$row[csf('id')]]=$row[csf('booking_no')];
		if($row[csf('batch_against')]==3)
		{
			$batch_against[$row[csf('id')]]=$row[csf('batch_against')];
		}

	}

	$batch_id_rec_main = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id_rec_main")])));
	if ($batch_id_rec_main == "") $batch_id_rec_main = 0;

	if ($db_type == 0) {
		$batchdata_array = sql_select("select group_concat(batch_no) as batch_no, group_concat(extention_no) as extention_no, group_concat(process_id) as process_id, group_concat(batch_type_id) as batch_type, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(distinct color_id) as color_id,  group_concat(distinct booking_no) as booking_no from pro_batch_create_mst where id in($batch_id)");
	} else {
		$batchdata_array = sql_select("select listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, LISTAGG(CAST(extention_no AS VARCHAR2(4000)), ',') WITHIN GROUP(ORDER BY id) AS extention_no, LISTAGG(CAST(process_id AS VARCHAR2(4000)), ',') WITHIN GROUP(ORDER BY id) AS process_id, LISTAGG(CAST(batch_type_id AS VARCHAR2(4000)), ',') WITHIN GROUP(ORDER BY id) AS batch_type, listagg(color_id ,',') within group (order by id) as color_id,  listagg(booking_no ,',') within group (order by id) as booking_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($batch_id)");
	}
    //echo "select listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, LISTAGG(CAST(batch_type_id AS VARCHAR2(4000)), ',') WITHIN GROUP(ORDER BY id) AS batch_type, listagg(color_id ,',') within group (order by id) as color_id,  listagg(booking_no ,',') within group (order by id) as booking_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($batch_id)";
    $batchType = '';
    $batchTypeArray = array();
    $batchTypeId = explode(',', $batchdata_array[0][csf('batch_type')]);
    foreach (array_unique($batchTypeId) as $batch){
        if($batch > 0){
            array_push($batchTypeArray, $batch_type_arr[$batch]);
        }
    }
    if(count($batchTypeArray) > 0){
        $batchType = implode(', ', $batchTypeArray);
    }
    $noOfReq = array(0);
    $explodeBtach = explode(',', $batch_id);
    if(count($explodeBtach) > 0){
        foreach ($explodeBtach as $ids){
            $getReqCounter = sql_select("SELECT count(id) as COUNTER FROM dyes_chem_issue_requ_mst where INSTR(',' || batch_id || ',', ',$ids,') > 0 and status_active = 1 and is_deleted = 0");
            array_push($noOfReq, isset($getReqCounter[0]) ? $getReqCounter[0]['COUNTER'] : 0);
        }
    }

	$po_no = '';
	$job_no = '';
	$buyer_name = '';
	$style_ref_no = '';
	foreach (explode(",", $data_array[0][csf("batch_id")]) as $b_id)
	{

		if ($entry_form_arr[$b_id] == 36)
		{
			$po_data = sql_select("select distinct b.order_no, b.cust_style_ref, b.buyer_buyer, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ");
			foreach ($po_data as $row) {
				$po_no .= $row[csf('order_no')] . ",";
				$job_no .= $row[csf('subcon_job')] . ",";
				if ($style_ref_no == '') $style_ref_no = $row[csf('cust_style_ref')]; else $style_ref_no .= "," . $row[csf('cust_style_ref')];
				$buyer_name .= $buyer_library[$row[csf('party_id')]] . ",";
			}
		}
		else
		{
			//foreach (explode(",", $data_array[0][csf("buyer_id")]) as $buyer_id)
//			{
//				if($buyer_id)
//				{
//					$buyer_name .= $buyer_library[$buyer_id] . ",";
//				}
//			}
			//echo "=".$buyer_name."=test";
			$po_data = sql_select("select distinct b.po_number, c.job_no,c.style_ref_no, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.IS_SALES<>1 and a.mst_id in(" . $b_id . ")
			union all
			select distinct b.job_no as po_number, b.job_no, null as style_ref_no, b.po_buyer as buyer_name from pro_batch_create_dtls a, FABRIC_SALES_ORDER_MST b
			where a.po_id=b.id and a.status_active=1 and a.is_deleted=0 and a.IS_SALES=1 and a.mst_id in(" . $b_id . ")");
			$job_nos="";
			foreach ($po_data as $row)
			{
				$po_no .= $row[csf('po_number')] . ",";
				$job_no .= $row[csf('job_no')] . ",";
				//$job_nos .= $row[csf('job_no')] . ",";
				if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
				if ($job_nos == '') $job_nos ="'".$row[csf('job_no')]."'"; else $job_nos.= ","."'".$row[csf('job_no')]."'";
				//if($buyer_name=="")
				$buyer_name.=$buyer_library[$row[csf('buyer_name')]].",";
				//echo $row[csf('buyer_name')]."test";
			}

			if($batch_against[$b_id]==3 && $style_ref_no=='')
			{
				$booking_no_b=$batch_booking[$b_id];
				$sql_style=sql_select("select style_ref_no from sample_development_mst where id in (select style_id from wo_non_ord_samp_booking_dtls where status_active=1 and booking_no ='$booking_no_b' group by style_id)");
				foreach ($sql_style as $row) {
					$style_ref_no.=$row[csf('style_ref_no')].',';
				}
			}
		}
	}
	$buyer_name=implode(",",array_unique(explode(",",chop($buyer_name,","))));
    $process_id = explode(',', $data_array[0][csf('dyeing_re_process')]);
    $process_str = '';
    foreach (array_unique($process_id) as $k => $process){
        if($k == 0){
            $process_str .= $dyeing_re_process[$process];
        }else{
            $process_str .= ','.$dyeing_re_process[$process];
        }
    }

	$jobNos=rtrim($job_nos,',');
	$jobnos=rtrim($job_no,',');
	$style_ref_no=chop($style_ref_no,",");

	if($job_nos!='')
	{
		if($job_nos!='') $job_nos_con="where  job_no in(".$job_nos.")";//else $job_nos_con="where  job_no in('')";
		$exchange_rate_arr = return_library_array("select job_no, exchange_rate from wo_pre_cost_mst $job_nos_con", 'job_no', 'exchange_rate');
	}
	//print_r($exchange_rate_arr);
	if($job_nos!='')
	{
	$condition= new condition();
	 $condition->company_name("=$data[0]");
	 if(str_replace("'","",$job_nos) !=''){
		  $condition->job_no("in($job_nos)");
	 }
	 $condition->init();

	$conversion= new conversion($condition);
	$conversion_qty_arr_process=$conversion->getQtyArray_by_jobAndProcess();
	$conversion_costing_arr_process=$conversion->getAmountArray_by_jobAndProcess();
	}
	$dyeing_charge_arr=array(25,26,30,31,32,33,34,39,60,62,63,64,65,66,67,68,69,70,71,82,83,84,85,86,89,90,91,92,93,94,129,135,136,137,138,139,140,141,142,143,146);
	$jobnos=array_unique(explode(",",$jobnos));
	$tot_avg_dyeing_charge=0;$avg_dyeing_charge=0;$tot_dyeing_qty=$tot_dyeing_cost=0;//$tot_dyeing_cost=0;$tot_dyeing_qty=0;
	foreach($jobnos as $job)
	{
		$exchange_rate=$exchange_rate_arr[$job];
		foreach($dyeing_charge_arr as $process_id)
		{
			$dyeing_cost=array_sum($conversion_costing_arr_process[$job][$process_id]);
			//echo $job.'=='.$dyeing_cost.'='.$exchange_rate.'<br/>';
			$totdyeing_cost=$dyeing_cost;
			if($totdyeing_cost>0)
			{
				$tot_dyeing_qty+=array_sum($conversion_qty_arr_process[$job][$process_id]);
				$tot_dyeing_cost+=$dyeing_cost;
			}
		}
		$avg_dyeing_charge+=$tot_dyeing_cost/$tot_dyeing_qty;
		$tot_avg_dyeing_charge+=$avg_dyeing_charge*$exchange_rate;
	}

	$po_no = implode(", ", array_unique(explode(",", substr($po_no, 0, -1))));
	$job_no = implode(", ", array_unique(explode(",", substr($job_no, 0, -1))));
	$buyer_name = implode(",", array_unique(explode(",", substr($buyer_name, 0, -1))));

	$color_id = array_unique(explode(",", $batchdata_array[0][csf('color_id')]));
	$color_name = '';
	foreach ($color_id as $color) {
		$color_name .= $color_arr[$color] . ",";
	}
	$color_name = substr($color_name, 0, -1);

	$recipe_arr=sql_select("select a.working_company_id,a.company_id, b.batch_weight from pro_recipe_entry_mst a, pro_batch_create_mst b where a.batch_id=b.id and  a.id in($recipe_id) ");
	$owner_company_id=$recipe_arr[0][csf('company_id')];
	$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
	$group_id=$nameArray[0][csf('group_id')];

    $batch_counter = 0;
    $batch_id_for_counter = array_unique(explode(",", $data_array[0][csf("batch_id")]));
    $booking_no = array_unique(explode(",", $batchdata_array[0][csf("booking_no")]));
    if(count($batch_id_for_counter) == 1){
//        echo "SELECT count(id) as COUNTER from pro_batch_create_mst where  company_id=$data[0] and color_id = $color_id[0] and booking_no = '$booking_no[0]' and  status_active=1 and is_deleted=0";
        $total_batch_sql= sql_select("SELECT count(id) as COUNTER from pro_batch_create_mst where  company_id=$data[0] and color_id = $color_id[0] and booking_no = '$booking_no[0]' and  status_active=1 and is_deleted=0");
        $batch_counter = isset($total_batch_sql[0]['COUNTER']) ? $total_batch_sql[0]['COUNTER'] : 0;
    }
	?>
	<div style="width:1250px;">
        <div style="width: 1200px;text-align: right;position: absolute;top: 25px;"><span id="show_barcode_image"></span></div>
		<table width="1000" cellspacing="0" align="center">
			<tr>
				<td colspan="10" align="center" style="font-size:xx-large">
					<strong><? echo $company_library[$data[0]];//$company_library[$data[0]]; ?></strong></td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Report</u></strong></td>
            </tr>
        </table>
        <table width="950" cellspacing="0" align="center">
            <tr>
                <td width="90"><strong>Req. ID</strong></td>
                <td width="160px"><strong>:  </strong><? echo $dataArray[0][csf('requ_no')]; ?></td>
                <td width="100"><strong>Req. Date</strong></td>
                <td width="160px"><strong>:  </strong><? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
                <td width="90"><strong>Buyer</strong></td>
                <td width="160px"><strong>:  </strong><? echo implode(",", array_unique(explode(",", $buyer_name))); ?></td>
            </tr>
            <tr>
                <td><strong>Order No</strong></td>
                <td><strong>:  </strong><? echo $po_no; ?></td>
                <td><strong>Job No</strong></td>
                <td><strong>:  </strong><? echo $job_no; ?></td>
                <td><strong>Issue Basis</strong></td>
                <td><strong>:  </strong><? echo $receive_basis_arr[$dataArray[0][csf('requisition_basis')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Batch No <?=$batch_counter > 0 ? $batch_counter : ""?></strong></td>
                <td><strong>:  </strong><? echo $batchdata_array[0][csf('batch_no')]; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Ext. : </strong><? echo $batchdata_array[0][csf('extention_no')]; ?></td>
                <td><strong>Batch Weight</strong></td>
                <td><strong>:  </strong><? $tot_batch_weight=$batch_weight+$batchdata_array[0][csf('batch_weight')];echo number_format($tot_batch_weight,4); ?></td>
                <td><strong>Color</strong></td>
                <td><strong>:  </strong><? echo $color_name; ?></td>
            </tr>
            <tr>
                <td><strong>Recipe No</strong></td>
                <td><strong>:  </strong><? echo $data_array[0][csf("recipe_id")]; ?></td>
                <td><strong>Machine No</strong></td>
                <td><strong>:  </strong>
                    <?
                    $machine_data = sql_select("select machine_no, floor_id from lib_machine_name where id='" . $dataArray[0][csf("machine_id")] . "'");
                    echo $machine_data[0][csf('machine_no')];
                    ?>
                </td>
                <td><strong>Floor Name</strong></td>
                <td colspan=""><strong>:  </strong>
                    <?
                    $floor_name = return_field_value("floor_name", "lib_prod_floor", "id='" . $machine_data[0][csf('floor_id')] . "'");
                    echo $floor_name;
                    ?>
                </td>
            </tr>
            <tr>
                <td><strong>Method</strong></td>
                <td><strong>:  </strong><? echo $dyeing_method[$dataArray[0][csf("method")]]; ?></td>
                <td><strong>Style Ref.</strong></td>
                <td ><p><strong>:  </strong><? echo implode(",", array_unique(explode(",", $style_ref_no)));?></p></td>
				<td><strong>Scouring Water</strong></td>
                <td><strong>:  </strong><? echo $sub_proces_arr[115]; ?></td>
            </tr>
            <tr>
                <td><strong>Batch Type</strong></td>
                <td><strong>:  </strong><? echo $batchType ?></td>
                <td><strong>No. of Req. of this Batch</strong></td>
                <td><strong>:  </strong><? echo array_sum($noOfReq); ?></td>
				<td><strong>Dyeing Water</strong></td>
                <td><strong>:  </strong><? echo $sub_proces_arr[117]; ?></td>
            </tr>
            <tr>
                <td><strong>Re Process</strong></td>
                <td colspan="5"><strong>:  </strong><? echo $process_str; ?></td>
            </tr>
        </table>
        <script type="text/javascript" src="../../js/jquery.js"></script>
     	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
		<script>
		function generateBarcode( valuess ){

			var value = valuess;
			var btype = 'code39';
			var renderer ='bmp';
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
            $("#show_barcode_image").barcode(value, btype, settings);
		}
		generateBarcode('<? echo $dataArray[0][csf('requ_no')]; ?>');
        </script>
        <br>
        <?
        $j = 1;
        $entryForm = $entry_form_arr[$batch_id_qry[0]];
        ?>
        <table width="1000" cellspacing="0" align="center" class="rpt_table" border="1" rules="all">
            <thead bgcolor="#dddddd" align="center">
                <?
                if ($entryForm == 74) {
                    ?>
                    <tr bgcolor="#CCCCFF">
                        <th colspan="4" align="center"><strong>Fabrication</strong></th>
                    </tr>
                    <tr>
                        <th width="50">SL</th>
                        <th width="350">Gmts. Item</th>
                        <th width="110">Gmts. Qty</th>
                        <th>Batch Qty.</th>
                    </tr>
                    <?
                } else {
                    ?>
                    <tr bgcolor="#CCCCFF">
                        <th colspan="8" align="center"><strong>Fabrication</strong></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Dia/ W. Type</th>
                        <th width="100">Yarn Lot</th>
                        <th width="100">Brand</th>
                        <th width="100">Count</th>
                        <th width="300">Constrution & Composition</th>
                        <th width="70">Gsm</th>
                        <th width="70">Dia</th>
                    </tr>
                    <?
                }
                ?>
            </thead>
            <tbody>
                <?
                $batch_id_qry = array_unique(explode(",", $dataArray[0][csf('batch_id')]));
                if($db_type==0)
                {
                    $selsect_barcode=" group_concat(barcode_no) as barcode_no";
                    $batch_query = "select a.po_id, a.prod_id, a.item_description, a.width_dia_type, sum(a.roll_no) as gmts_qty, sum(a.batch_qnty) as batch_qnty, group_concat( c.yarn_lot) as yarn_lot, group_concat(c.yarn_count) as yarn_count, group_concat(c.brand_id) as brand_id
                    from pro_batch_create_dtls a, pro_roll_details b, pro_grey_prod_entry_dtls c
                    where a.barcode_no=b.barcode_no and b.dtls_id=c.id and b.entry_form=2 and a.mst_id in(".implode(",",$batch_id_qry).") and a.status_active=1 and a.is_deleted=0
                    group by a.po_id, a.prod_id, a.item_description, a.width_dia_type ";
                }
                else
                {
                    $batch_query = "select a.po_id, a.prod_id, a.item_description, a.width_dia_type, sum(a.roll_no) as gmts_qty, sum(a.batch_qnty) as batch_qnty, listagg( cast(c.yarn_lot as varchar(4000)), ',') within group(order by c.yarn_lot) as yarn_lot, listagg( cast(c.yarn_count as varchar(4000)), ',') within group(order by c.yarn_count) as yarn_count, listagg( cast(c.brand_id as varchar(4000)), ',') within group(order by c.brand_id) as brand_id
                    from pro_batch_create_dtls a, pro_roll_details b, pro_grey_prod_entry_dtls c
                    where a.barcode_no=b.barcode_no and b.dtls_id=c.id and b.entry_form=2 and a.mst_id in(".implode(",",$batch_id_qry).") and a.status_active=1 and a.is_deleted=0
                    group by a.po_id, a.prod_id, a.item_description, a.width_dia_type ";
                }

                //echo $batch_query;
                $result_batch_query = sql_select($batch_query);
                $all_barcodes="";
                foreach ($result_batch_query as $row)
                {
                    $all_barcodes .=implode(",",array_unique(explode(",",$row[csf("barcode_no")]))).",";
                }
                $all_barcodes=chop($all_barcodes,",");
                $lot_brand_sql="select a.yarn_lot, a.yarn_count, a.brand_id from ";
                //echo $all_barcodes;
                foreach ($result_batch_query as $rows) {
                    if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                    $fabrication_full = $rows[csf("item_description")];
                    $fabrication = explode(',', $fabrication_full);

                    /*$yarn_lot=rtrim($yarn_dtls_array[$rows[csf('prod_id')]]['yarn_lot'],',');
                    $yarn_lots=implode(", ",array_unique(explode(",",$yarn_lot)));
                    $brand_name=rtrim($yarn_dtls_array[$rows[csf('prod_id')]]['brand_name'],',');
                    $brands_name=implode(", ",array_unique(explode(",",$brand_name)));
                    $count_name=rtrim($yarn_dtls_array[$rows[csf('prod_id')]]['count_name'],',');
                    $counts_name=implode(", ",array_unique(explode(",",$count_name)));
                    $brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
                    */
                    $yarn_lots=implode(", ",array_unique(explode(",",$rows[csf("yarn_lot")])));
                    $brand_name_arr=array_unique(explode(",",$rows[csf("brand_id")]));
                    foreach($brand_name_arr as $brand_id)
                    {
                        $brands_name .=$brand_arr[$brand_id].",";
                    }
                    $brands_name=chop($brands_name,",");
                    $count_name_arr=array_unique(explode(",",$rows[csf("yarn_count")]));
                    foreach($count_name_arr as $count_id)
                    {
                        $counts_name .=$count_arr[$count_id].",";
                    }
                    $counts_name=chop($counts_name,",");

                    if ($entry_form_arr[$b_id] == 36) {

                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>">
                            <td align="center"><? echo $j; ?></td>
                            <td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
                    <td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['yarn_lot'];
                        ?></td>
                    <td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['brand_name'];
                        ?></td>
                    <td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['count_name'];
                        ?></td>
                        <td align="left"><? echo $fabrication[0]; ?></td>
                        <td align="center"><? echo $fabrication[1]; ?></td>
                        <td align="center"><? echo $fabrication[3]; ?></td>
                    </tr>
                    <?
                } else if ($entry_form_arr[$b_id] == 74) {
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $j; ?></td>
                        <td align="left"><? echo $garments_item[$rows[csf('prod_id')]]; ?></td>
                        <td align="right"><? echo $rows[csf('gmts_qty')]; ?></td>
                        <td align="right"><? echo number_format($rows[csf('batch_qnty')], 2); ?></td>
                    </tr>
                    <?
                } else {
                //echo $entry_form_arr[$b_id].'aaa';
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $j; ?></td>
                        <td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
                        <td width="100" align="left"><p style="word-wrap:break-word;"><? echo $yarn_lots; ?></p></td>
                        <td   width="100" align="left"><p style="word-wrap:break-word;"><? echo $brands_name; ?></p></td>
                        <td  width="100" align="left"><p style="word-wrap:break-word;"><? echo $counts_name; ?></p></td>
                        <td align="left"><? echo $fabrication[0] . ", " . $fabrication[1]; ?></td>
                        <td align="center"><? echo $fabrication[2]; ?></td>
                        <td align="center"><? echo $fabrication[3]; ?></td>
                    </tr>
                    <?
                }
                $j++;
            }


            ?>
        </tbody>
    </table>
		<div style="width:1250px; margin-top:10px">
			<table align="right" cellspacing="0" width="1250" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center">
					<tr bgcolor="#CCCCFF">
						<th colspan="19" align="center"><strong>Dyes And Chemical Issue Requisition</strong></th>
					</tr>
				</thead>
				<?
				/*$sql_rec="select a.id, a.item_group_id, b.sub_process_id as sub_process_id, b.prod_id, b.dose_base, b.adj_type, b.comments, b.process_remark
				 from pro_recipe_entry_dtls b left join product_details_master a on b.prod_id=a.id and a.item_category_id in(5,6,7,23)
	 where b.mst_id in($recipe_id)  and b.status_active=1 and b.is_deleted=0 ";*/

	 $group_arr = return_library_array("select id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0", 'id', 'item_name');
	 $sql_rec = "select b.sub_process_id as sub_process_id, b.prod_id, b.dose_base, b.adj_type, b.comments, b.process_remark, b.item_lot
	 from pro_recipe_entry_dtls b
	 where b.mst_id in($recipe_id)  and b.status_active=1 and b.is_deleted=0";
	 $nameArray = sql_select($sql_rec);
	 foreach ($nameArray as $row) {
		$all_prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
		$item_lot_arr[$row[csf("prod_id")]] .= $row[csf("item_lot")] . ",";
	 }

	 $item_grop_id_arr = return_library_array("select id, item_group_id from product_details_master where id in(" . implode(",", $all_prod_id_arr) . ")", 'id', 'item_group_id');

	 $process_array = array();
	 $process_array_remark = array();
	 foreach ($nameArray as $row) {
		$process_array[$row[csf("sub_process_id")]] = $row[csf("process_remark")];
		$process_array_remark[$row[csf("sub_process_id")]][$row[csf("prod_id")]][$item_grop_id_arr[$row[csf("prod_id")]]][$row[csf("dose_base")]]["comments"] = $row[csf("comments")];
		if ($row[csf("sub_process_id")] > 92 && $row[csf("sub_process_id")] < 99) {
			$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"] = $row[csf("process_remark")];
		}

	 }


	/*$sql_rec_remark="select b.sub_process_id as sub_process_id,b.comments,b.process_remark from pro_recipe_entry_dtls b  where  b.mst_id in($recipe_id) and b.sub_process_id in(93,94,95,96,97,98) and b.status_active=1 and b.is_deleted=0";

	$nameArray_re=sql_select( $sql_rec_remark );
	foreach($nameArray_re as $row)
	{
		$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"]=$row[csf("process_remark")];
	}*/


				/*if($db_type==0)
	{
		$item_lot_arr=return_library_array("select b.prod_id,group_concat(b.item_lot) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot" );
	}
	else
	{
		$item_lot_arr=return_library_array("select b.prod_id,listagg((cast(b.item_lot as varchar2(500))),',') within group (order by b.prod_id) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot" );
	}



	$sql_dtls = "select a.requ_no, a.batch_id, a.recipe_id, b.id,
	b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
	c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id
	from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
	where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id=$data[1] order by b.id, b.seq_no";*/


	$sql_dtls = "(select a.requ_no, a.batch_id, a.recipe_id, b.id,
	b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
	c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id, b.item_lot
	from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
	where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id=$data[1])
	union
	(
	select a.requ_no, a.batch_id, a.recipe_id, b.id,
	b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
	null as item_description, null as item_group_id,  null as sub_group_name, null as item_size, null as unit_of_measure,null as avg_rate_per_unit,null as prod_id, b.item_lot
	from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b
	where a.id=b.mst_id and b.sub_process in($subprocessforwashin) and   a.id=$data[1]
	) order by id";
				// echo $sql_dtls;//die;
	$sql_result = sql_select($sql_dtls);

	$sub_process_array = array();
	$sub_process_tot_rec_array = array();
	$sub_process_tot_req_array = array();
	$sub_process_tot_value_array = array();

	foreach ($sql_result as $row) {
		$sub_process_tot_rec_array[$row[csf("sub_process")]] += $row[csf("recipe_qnty")];
		$sub_process_tot_req_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
		$sub_process_tot_value_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")] * $row[csf("avg_rate_per_unit")];
		//$sub_process_tot_ratio_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
	}
	$recipe_id=$data_array[0][csf("recipe_id")];
	$ratio_arr=array();
	$prevRatioData=sql_select( "select sub_process_id, liquor_ratio from pro_recipe_entry_dtls where mst_id in ($recipe_id) and status_active=1 and ratio>0");
	foreach($prevRatioData as $prevRow)
	{
		$ratio_arr[$prevRow[csf('sub_process_id')]]['ratio']=$prevRow[csf('liquor_ratio')];
	}
				//var_dump($sub_process_tot_req_array);
	$i = 1;
	$k = 1;
	$recipe_qnty_sum = 0;
	$req_qny_edit_sum = 0;
	$recipe_qnty = 0;
	$req_qny_edit = 0;
	$req_value_sum = 0;
	$req_value_grand = 0;
	$recipe_qnty_grand = 0;
	$req_qny_edit_grand = 0;

				foreach ($sql_result as $row) {
		if ($i % 2 == 0)
			$bgcolor = "#E9F3FF";
		else
			$bgcolor = "#FFFFFF";
		if (!in_array($row[csf("sub_process")], $sub_process_array)) {
			$sub_process_array[] = $row[csf('sub_process')];
			if ($k != 1) {
				?>
				<tr>
					<td colspan="8" align="right"><strong>Total :</strong></td>
					<td align="right"><strong><?php echo number_format($recipe_qnty_sum, 5, '.', ''); ?></strong></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align="right"><strong><?php echo number_format($req_qny_edit_sum, 5, '.', ''); ?></strong></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
                </tr>
				<?
			}
			$recipe_qnty_sum = 0;
			$req_qny_edit_sum = 0;
			$req_value_sum = 0;
			$k++;

			//if ($row[csf("sub_process")] == 93 || $row[csf("sub_process")] == 94 || $row[csf("sub_process")] == 95 || $row[csf("sub_process")] == 96 || $row[csf("sub_process")] == 97 || $row[csf("sub_process")] == 98)
			if(in_array($row[csf("sub_process")],$subprocessForWashArr))
			 {
				$pro_remark = $process_array_remark[$row[csf("sub_process")]]["wash_comments"];
			}

			?>
			<tr bgcolor="#CCCCCC">
				<th colspan="19">
					<strong><?

					$batch_ratio=explode(":",$data_array[0][csf("ratio")]);
					$lqr_ratio=$batch_ratio[0];
					$ratio_qty=$lqr_ratio.':'.$ratio_arr[$row[csf('sub_process')]]['ratio'];
					if ($pro_remark == '') $pro_remark = ''; else $pro_remark .= $pro_remark . ', ';
					echo $dyeing_sub_process[$row[csf("sub_process")]] . ', ' . $pro_remark.'&nbsp;Liquor:Ratio &nbsp;'.$ratio_qty; ?></strong>
				</th>
			</tr>
			<? if($i==1){?>
			<tr bgcolor="#EFEFEF">
				<th width="30">SL</th>
				<th width="80">Item Cat.</th>
				<th width="80">Item Group</th>
				<th width="250">Item Description</th>
				<th width="50">Dyes Lot</th>
				<th width="50">UOM</th>
				<th width="100">Dose Base</th>
				<th width="40">Ratio</th>
				<th width="60">Recipe Qty.</th>
				<th width="50">Adj%</th>
				<th width="60">Adj Type</th>
				<th width="60">Adj Qty.</th>
				<th width="80">Iss. Qty.</th>
				<th width="60">KG</th>
				<th width="60">GM</th>
				<th width="60">MG</th>
				<th width="100">Comments</th>
			</tr>
			<?
			}
		}



					$req_qny_edit = explode(".", (string)$row[csf("req_qny_edit")]);
					$iss_qnty_kg = $req_qny_edit[0];
					if ($iss_qnty_kg == "") $iss_qnty_kg = 0;

					$iss_qnty_gm = substr($req_qny_edit[1], 0, 3);//$rem[0]; // floor($mg/1000);
					$iss_qnty_mg = substr($req_qny_edit[1], 3, 3);//$rem[1];//round($mg-$iss_qnty_gm*1000);
					$iss_qnty_gm = str_pad($iss_qnty_gm, 3, "0", STR_PAD_RIGHT);
					$iss_qnty_mg = str_pad($iss_qnty_mg, 3, "0", STR_PAD_RIGHT);

					//echo "mg ".$req_qny_edit[1]."<br>";
					$adjQty = ($row[csf("adjust_percent")] * $row[csf("recipe_qnty")]) / 100;
					$comment = $process_array_remark[$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"];
					?>
					<tbody>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $i; ?></td>
						<td><? echo $item_category[$row[csf("item_category")]];  //echo $row[csf("sub_process")];
							?></td>
							<td><? echo $group_arr[$row[csf("item_group_id")]]; ?></td>
							<!--<td><? echo $row[csf("sub_group_name")]; ?></td>-->
							<td><? echo $row[csf("item_description")] . ' ' . $row[csf("item_size")]; ?></td>
							<td><? echo $row[csf("item_lot")];//chop($item_lot_arr[$row[csf("prod_id")]], ","); ?></td>
							<td align="center"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
							<td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
							<td align="center"><? echo number_format($row[csf("ratio")], 5, '.', ''); ?></td>
							<td align="right"><strong><? echo number_format($row[csf("recipe_qnty")], 5, '.', ''); ?>
								</strong></td>
									<td align="center"><? echo $row[csf("adjust_percent")]; ?></td>
									<td><? echo $increase_decrease[$row[csf("adjust_type")]]; ?></td>
									<td align="right"><? echo number_format($adjQty, 5, '.', ''); ?></td>
									<td align="right"><strong><? echo number_format($row[csf("req_qny_edit")], 5, '.', ''); ?>
										</strong></td>
											<td align="right"><? echo $iss_qnty_kg; ?></td>
											<td align="right"><? echo $iss_qnty_gm; ?></td>
											<td align="right"><? echo $iss_qnty_mg; ?></td>
											<td align="right"><? echo $comment; ?></td>
											</tr>
										</tbody>
										<? $i++;
										$recipe_qnty_sum += $row[csf('recipe_qnty')];
										$req_qny_edit_sum += $row[csf('req_qny_edit')];
										$req_value_sum += $req_value;

										$recipe_qnty_grand += $row[csf('recipe_qnty')];
										$req_qny_edit_grand += $row[csf('req_qny_edit')];
										$req_value_grand += $req_value;
									}
				foreach ($sub_process_tot_rec_array as $val_rec) {
					$totval_rec = $val_rec;
				}
				foreach ($sub_process_tot_req_array as $val_req) {
					$totval_req = $val_req;
				}
				foreach ($sub_process_tot_value_array as $req_value) {
					$tot_req_value = $req_value;
				}

				//$recipe_qnty_grand +=$val_rec;
				//$req_qny_edit_grand +=$val_req;
				//$req_value_grand +=$req_value;
				?>
				<tr>
					<td colspan="8" align="right"><strong>Total :</strong></td>
					<td align="right"><strong><?php echo number_format($totval_rec, 5, '.', ''); ?></strong></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align="right"><strong><?php echo number_format($totval_req, 5, '.', ''); ?></strong></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="8" align="right"><strong> Grand Total :</strong></td>
					<td align="right"><strong><?php echo number_format($recipe_qnty_grand, 5, '.', ''); ?></strong></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align="right"><strong><?php echo number_format($req_qny_edit_grand, 5, '.', ''); ?></strong></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
                </tr>
			</table>
			<br>
			<?
			echo signature_table(15, $data[0], "1250px");
			?>
		</div>
	</div>
	<?
	exit();

}


if ($action == "chemical_dyes_issue_requisition_for_multi")
{
    extract($_REQUEST);
    $data = explode('*', $data);
    $sql = "select a.id, a.requ_no, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id, a.requ_prefix_num from dyes_chem_issue_requ_mst a where a.id in ($data[1]) and a.company_id='$data[0]' order by a.id ";
    $dataArray = sql_select($sql);
    $recipe_id_arr = array(); $requ_no_arr = array();
    foreach($dataArray as $key => $val){
        $recipe_id_arr[$val[csf('recipe_id')]] = $val[csf('recipe_id')];
        $requ_no_arr[$val[csf('requ_prefix_num')]] = $val[csf('requ_prefix_num')];
    }
    $recipe_id = implode(',',$recipe_id_arr);
    $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
    $color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
    //$entry_form_arr = return_library_array("select id, entry_form from pro_batch_create_mst", "id", "entry_form");
    $group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");
    $sub_proces_arr = return_library_array("SELECT (a.batch_qty*b.liquor_ratio) as water, b.sub_process_id from pro_recipe_entry_mst a, pro_recipe_entry_dtls b where a.id=b.mst_id and a.id in ($recipe_id)", "sub_process_id", "water");

    $batch_weight = 0;
    if ($db_type == 0) {
        $data_array = sql_select("select  batch_id as batch_id, case when entry_form=60 then new_batch_weight end as batch_weight, case when entry_form<>60 then batch_id end as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
    } else {
        $data_array = sql_select("select batch_id, case when entry_form=60 then new_batch_weight end as batch_weight, case when entry_form<>60 then batch_id end as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
    }
    $batch_weight_arr = array(); $batch_id_arr = array(); $batch_id_rec = array();
    foreach ($data_array as $datab){
        $batch_id_arr[$datab[csf("batch_id")]] = $datab[csf("batch_id")];
        if($datab[csf("batch_id_rec_main")] != '') {
            $batch_id_rec[$datab[csf("batch_id_rec_main")]] = $datab[csf("batch_id_rec_main")];
        }
    }
    $batch_id = implode(",", $batch_id_arr);

    $batch_id_rec_main = implode(",", $batch_id_rec);
    if ($batch_id_rec_main == "") $batch_id_rec_main = 0;

    if ($db_type == 0) {
        $batchdata_array = sql_select("select id, batch_no as batch_no, case when id in($batch_id_rec_main) then batch_weight end as batch_weight from pro_batch_create_mst where id in($batch_id)");
    } else {
        $batchdata_array = sql_select("select id, batch_no as batch_no, case when id in($batch_id_rec_main) then batch_weight end as batch_weight from pro_batch_create_mst where id in($batch_id)");
        //echo "select id, batch_no as batch_no, case when id in($batch_id_rec_main) then batch_weight end as batch_weight from pro_batch_create_mst where id in($batch_id)";
    }
    $batch_no_arr = [];
    foreach ($batchdata_array as $batch){
        $batch_no_arr[$batch[csf('id')]] = $batch[csf('batch_no')];
        $batch_weight_arr[$batch[csf('id')]] = $batch[csf("batch_weight")];
    }
    ?>
    <div style="width:1250px;">
        <div style="width: 1200px;text-align: right;position: absolute;top: 25px;"><span id="show_barcode_image"></span></div>
        <table width="1000" cellspacing="0" align="center">
            <tr>
                <td colspan="10" align="center" style="font-size:xx-large">
                    <strong><? echo $company_library[$data[0]];//$company_library[$data[0]]; ?></strong></td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Report</u></strong></td>
            </tr>
        </table>
        <table width="1250" cellspacing="0" align="left">
            <tr>
                <td width="120"><strong>Requisition ID</strong></td>
                <td><strong>:  </strong><? echo implode(', ', $requ_no_arr); ?></td>
            </tr>
            <tr>
                <td><strong>No. Of Batch</strong></td>
                <td><strong>:  </strong><? echo count($batch_no_arr); ?></td>
            </tr>
            <tr>
                <td><strong>Total Batch Weight</strong></td>
                <td><strong>:  </strong><? echo array_sum($batch_weight_arr); ?></td>
            </tr>
        </table>

        <div style="width:1250px; margin-top:10px">
            <table align="right" cellspacing="0" width="1250" border="1" rules="all" class="rpt_table">
                <thead bgcolor="#dddddd" align="center">
                <tr bgcolor="#CCCCFF">
                    <th colspan="19" align="center"><strong>Dyes And Chemical Issue Requisition</strong></th>
                </tr>
                </thead>
                <?
                $group_arr = return_library_array("select id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0", 'id', 'item_name');
                $sql_rec = "select b.sub_process_id as sub_process_id, b.prod_id, b.dose_base, b.adj_type, b.comments, b.process_remark, b.item_lot from pro_recipe_entry_dtls b where b.mst_id in($recipe_id)  and b.status_active=1 and b.is_deleted=0";
                $nameArray = sql_select($sql_rec);
                foreach ($nameArray as $row) {
                    $all_prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
                    $item_lot_arr[$row[csf("prod_id")]] .= $row[csf("item_lot")] . ",";
                }
                $item_grop_id_arr = return_library_array("select id, item_group_id from product_details_master where id in(" . implode(",", $all_prod_id_arr) . ")", 'id', 'item_group_id');
                $process_array = array();
                $process_array_remark = array();
                foreach ($nameArray as $row) {
                    $process_array[$row[csf("sub_process_id")]] = $row[csf("process_remark")];
                    $process_array_remark[$row[csf("sub_process_id")]][$row[csf("prod_id")]][$item_grop_id_arr[$row[csf("prod_id")]]][$row[csf("dose_base")]]["comments"] = $row[csf("comments")];
                    if ($row[csf("sub_process_id")] > 92 && $row[csf("sub_process_id")] < 99) {
                        $process_array_remark[$row[csf("sub_process_id")]]["wash_comments"] = $row[csf("process_remark")];
                    }

                }
                $sql_dtls = "(select a.requ_no, a.batch_id, a.recipe_id, b.id, b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
                c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id, b.item_lot from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
                where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id in ($data[1]))
                    union
                (select a.requ_no, a.batch_id, a.recipe_id, b.id, b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
                null as item_description, null as item_group_id,  null as sub_group_name, null as item_size, null as unit_of_measure,null as avg_rate_per_unit,null as prod_id, b.item_lot
                from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b where a.id=b.mst_id and b.sub_process in($subprocessforwashin) and   a.id in ($data[1])) order by id";
                //echo $sql_dtls;//die;
                $sql_result = sql_select($sql_dtls);

                $sub_process_array = array();
                $sub_process_tot_rec_array = array();
                $sub_process_tot_req_array = array();
                $sub_process_tot_value_array = array();

                foreach ($sql_result as $row) {
                    $sub_process_tot_rec_array[$row[csf("sub_process")]] += $row[csf("recipe_qnty")];
                    $sub_process_tot_req_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
                    $sub_process_tot_value_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")] * $row[csf("avg_rate_per_unit")];
                    //$sub_process_tot_ratio_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
                }
                $ratio_arr=array();
                $prevRatioData=sql_select( "select sub_process_id, liquor_ratio from pro_recipe_entry_dtls where mst_id in ($recipe_id) and status_active=1 and ratio>0");
                foreach($prevRatioData as $prevRow)
                {
                    $ratio_arr[$prevRow[csf('sub_process_id')]]['ratio']=$prevRow[csf('liquor_ratio')];
                }
                //var_dump($sub_process_tot_req_array);
                $i = 1;
                $k = 1;
                $recipe_qnty_sum = 0;
                $req_qny_edit_sum = 0;
                $recipe_qnty = 0;
                $req_qny_edit = 0;
                $req_value_sum = 0;
                $req_value_grand = 0;
                $recipe_qnty_grand = 0;
                $req_qny_edit_grand = 0;

                foreach ($sql_result as $row) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";
                    if (!in_array($row[csf("sub_process")], $sub_process_array)) {
                        $sub_process_array[] = $row[csf('sub_process')];
                        if ($k != 1) {
                            ?>
                            <tr>
                                <td colspan="6" align="right"><strong>Total :</strong></td>
                                <td align="right"><strong><?php echo number_format($req_qny_edit_sum, 5, '.', ''); ?></strong></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <?
                        }
                        $recipe_qnty_sum = 0;
                        $req_qny_edit_sum = 0;
                        $req_value_sum = 0;
                        $k++;

                      //  if ($row[csf("sub_process")] == 93 || $row[csf("sub_process")] == 94 || $row[csf("sub_process")] == 95 || $row[csf("sub_process")] == 96 || $row[csf("sub_process")] == 97 || $row[csf("sub_process")] == 98)

							if(in_array($row[csf("sub_process")],$subprocessForWashArr))
							{
                            $pro_remark = $process_array_remark[$row[csf("sub_process")]]["wash_comments"];
                            }

                        ?>
                        <tr bgcolor="#CCCCCC">
                            <th colspan="19">
                                <strong><?
                                    echo $dyeing_sub_process[$row[csf("sub_process")]];?></strong>
                            </th>
                        </tr>
                        <? if($i==1){?>
                            <tr bgcolor="#EFEFEF">
                                <th width="30">SL</th>
                                <th width="80">Item Cat.</th>
                                <th width="80">Item Group</th>
                                <th width="250">Item Description</th>
                                <th width="50">Dyes Lot</th>
                                <th width="50">UOM</th>
                                <th width="80">Iss. Qty.</th>
                                <th width="60">KG</th>
                                <th width="60">GM</th>
                                <th width="60">MG</th>
                                <th width="100">Comments</th>
                            </tr>
                            <?
                        }
                    }
                    $req_qny_edit = explode(".", (string)$row[csf("req_qny_edit")]);
                    $iss_qnty_kg = $req_qny_edit[0];
                    if ($iss_qnty_kg == "") $iss_qnty_kg = 0;

                    $iss_qnty_gm = substr($req_qny_edit[1], 0, 3);//$rem[0]; // floor($mg/1000);
                    $iss_qnty_mg = substr($req_qny_edit[1], 3, 3);//$rem[1];//round($mg-$iss_qnty_gm*1000);
                    $iss_qnty_gm = str_pad($iss_qnty_gm, 3, "0", STR_PAD_RIGHT);
                    $iss_qnty_mg = str_pad($iss_qnty_mg, 3, "0", STR_PAD_RIGHT);

                    $comment = $process_array_remark[$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"];
                    ?>
                    <tbody>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td><? echo $item_category[$row[csf("item_category")]];  //echo $row[csf("sub_process")];
                            ?></td>
                        <td><? echo $group_arr[$row[csf("item_group_id")]]; ?></td>
                        <!--<td><? echo $row[csf("sub_group_name")]; ?></td>-->
                        <td><? echo $row[csf("item_description")] . ' ' . $row[csf("item_size")]; ?></td>
                        <td><? echo $row[csf("item_lot")];//chop($item_lot_arr[$row[csf("prod_id")]], ","); ?></td>
                        <td align="center"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
                        <td align="right"><strong><? echo number_format($row[csf("req_qny_edit")], 5, '.', ''); ?>
                        <td align="right"><? echo $iss_qnty_kg; ?></td>
                        <td align="right"><? echo $iss_qnty_gm; ?></td>
                        <td align="right"><? echo $iss_qnty_mg; ?></td>
                        <td align="right"><? echo $comment; ?></td>
                    </tr>
                    </tbody>
                    <? $i++;
                    $recipe_qnty_sum += $row[csf('recipe_qnty')];
                    $req_qny_edit_sum += $row[csf('req_qny_edit')];
                    $req_value_sum += $req_value;

                    $recipe_qnty_grand += $row[csf('recipe_qnty')];
                    $req_qny_edit_grand += $row[csf('req_qny_edit')];
                    $req_value_grand += $req_value;
                }
                foreach ($sub_process_tot_rec_array as $val_rec) {
                    $totval_rec = $val_rec;
                }
                foreach ($sub_process_tot_req_array as $val_req) {
                    $totval_req = $val_req;
                }
                foreach ($sub_process_tot_value_array as $req_value) {
                    $tot_req_value = $req_value;
                }

                ?>
                <tr>
                    <td colspan="6" align="right"><strong>Total :</strong></td>
                    <td align="right"><strong><?php echo number_format($totval_req, 5, '.', ''); ?></strong></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="6" align="right"><strong> Grand Total :</strong></td>
                    <td align="right"><strong><?php echo number_format($req_qny_edit_grand, 5, '.', ''); ?></strong></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
            <br>
            <?
            echo signature_table(15, $data[0], "1250px");
            ?>
        </div>
    </div>
    <?
    exit();

}

if ($action == "print_adding_topping")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	//print_r ($data);

	$sql = "select a.id, a.requ_no, a.company_id, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id from dyes_chem_issue_requ_mst a where a.id='$data[0]'";
	$dataArray = sql_select($sql);

	$recipe_id = $dataArray[0][csf('recipe_id')];
	$company_id = $dataArray[0][csf('company_id')];

	$company_name = return_field_value("company_name", "lib_company", "id=$company_id");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");
	// $receipe_arr = array();
	// $receipeData = sql_select("select id,labdip_no, total_liquor from pro_recipe_entry_mst where status_active=1 and is_deleted=0");
	// foreach ($receipeData as $row) {
	// 	$receipe_arr[$row[csf("id")]] = $row[csf("labdip_no")];
	// }
	// $entry_form_arr = array();
	// $batch_weight_arr = array();
	// $batchData = sql_select("select id, entry_form, batch_weight from pro_batch_create_mst");
	// foreach ($batchData as $rowB) {
	// 	$entry_form_arr[$rowB[csf('id')]] = $rowB[csf('entry_form')];
	// 	$batch_weight_arr[$rowB[csf('id')]] = $rowB[csf('batch_weight')];
	// }
	$receipe_arr = array();	$entry_form_arr = array();
	$batch_weight_arr = array();
	$receipeData = sql_select("select c.id as batch_id,c.entry_form,c.batch_weight,b.id,b.labdip_no, b.total_liquor from pro_recipe_entry_mst b,pro_batch_create_mst c where c.id=b.batch_id and b.status_active=1 and b.is_deleted=0 and b.id in($recipe_id)");
	foreach ($receipeData as $row) {
		$receipe_arr[$row[csf("id")]] = $row[csf("labdip_no")];
		$entry_form_arr[$rowB[csf('batch_id')]] = $rowB[csf('entry_form')];
		$batch_weight_arr[$rowB[csf('batch_id')]] = $rowB[csf('batch_weight')];
	}

	$batch_weight = 0;
	if ($db_type == 0) {
		$data_array = sql_select("select group_concat(buyer_id) as buyer_id,group_concat(id) as recipe_id, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor, group_concat(concat_ws(':',batch_ratio,liquor_ratio) order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, group_concat(case when entry_form<>60 then batch_id end) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
	} else {
		$data_array = sql_select("select listagg(buyer_id,',') within group (order by buyer_id) as buyer_id,listagg(id,',') within group (order by id) as recipe_id, listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor, listagg(batch_ratio || ':' || liquor_ratio,',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(case when entry_form<>60 then batch_id end,',') within group (order by batch_id) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
	}

	$batch_weight = $data_array[0][csf("batch_weight")];
	$batch_id = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id")])));

	$batch_id_rec_main = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id_rec_main")])));
	if ($batch_id_rec_main == "") $batch_id_rec_main = 0;

	if ($db_type == 0) {
		$batchdata_array = sql_select("select group_concat(batch_no) as batch_no, group_concat(booking_no) as booking_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(color_id) as color_id,group_concat(extention_no ) as extention_no  from pro_batch_create_mst where id in($batch_id)");
	} else {
		$batchdata_array = sql_select("select listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, listagg(CAST(booking_no AS VARCHAR2(4000)),',') within group (order by id) as booking_no, listagg(color_id ,',') within group (order by id) as color_id,listagg(extention_no ,',') within group (order by id) as extention_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($batch_id)");
	}


	$po_no = '';
	$job_no = '';
	$style_ref = '';
	$buyer_name = '';
	foreach (explode(",", $data_array[0][csf("batch_id")]) as $b_id) {
		if ($entry_form_arr[$b_id] == 36) {
			$po_data = sql_select("select b.order_no, b.cust_style_ref, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") group by b.order_no, b.cust_style_ref, c.subcon_job, c.party_id");
			foreach ($po_data as $row) {
				$po_no .= $row[csf('order_no')] . ",";
				$job_no .= $row[csf('subcon_job')] . ",";
				$style_ref .= $row[csf('cust_style_ref')] . ",";
				$buyer_name .= $buyer_library[$row[csf('party_id')]] . ",";
			}
		} else {
			$po_data = sql_select("select b.po_number, c.job_no, c.buyer_name, c.style_ref_no from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") group by b.po_number, c.job_no, c.buyer_name, c.style_ref_no");
			foreach ($po_data as $row) {
				$po_no .= $row[csf('po_number')] . ",";
				$job_no .= $row[csf('job_no')] . ",";
				$style_ref .= $row[csf('style_ref_no')] . ",";
				//$buyer_name.=$buyer_library[$row[csf('buyer_name')]].",";
			}
			foreach (explode(",", $data_array[0][csf("buyer_id")]) as $buyer_id) {
				$buyer_name .= $buyer_library[$buyer_id] . ",";
			}
		}
	}

	if ($db_type == 0) {
	$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
	} else if ($db_type == 2) {
	$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, listagg((cast(a.yarn_lot as varchar2(4000))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(a.brand_id,',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(4000))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id";
	}
	$recipe_id=$data_array[0][csf("recipe_id")];
	$ratio_arr=array();
	$prevRatioData=sql_select( "select sub_process_id, liquor_ratio from pro_recipe_entry_dtls where mst_id=$recipe_id and status_active=1 and ratio>0 and status_active=1 and ratio>0");
	foreach($prevRatioData as $prevRow)
	{
	$ratio_arr[$prevRow[csf('sub_process_id')]]['ratio']=$prevRow[csf('liquor_ratio')];
	}

	$yarn_dtls_array = array();
	$result_sql_yarn_dtls = sql_select($sql_yarn_dtls);
	foreach ($result_sql_yarn_dtls as $row) {
	$yarn_lot = implode(", ", array_unique(explode(",", $row[csf('yarn_lot')])));
	$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
	$brand_name = "";
	foreach ($brand_id as $val) {
		if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= ", " . $brand_arr[$val];
	}

	$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
	$count_name = "";
	foreach ($yarn_count as $val) {
		if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
	}
	$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['yarn_lot'] = $yarn_lot;
	$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['brand_name'] = $brand_name;
	$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['count_name'] = $count_name;
	}
	//var_dump($yarn_dtls_array);
	$po_no = implode(", ", array_unique(explode(",", substr($po_no, 0, -1))));
	$job_no = implode(", ", array_unique(explode(",", substr($job_no, 0, -1))));
	$style_ref = implode(", ", array_unique(explode(",", substr($style_ref, 0, -1))));
	$buyer_name = implode(",", array_unique(explode(",", substr($buyer_name, 0, -1))));
	$booking_no = implode(",", array_unique(array_filter(explode(",", $batchdata_array[0][csf('booking_no')]))));
	$extention_no = implode(",", array_unique(array_filter(explode(",", $batchdata_array[0][csf('extention_no')]))));

	$color_id = array_unique(explode(",", $batchdata_array[0][csf('color_id')]));
	$color_name = '';
	foreach ($color_id as $color) {
	$color_name .= $color_arr[$color] . ",";
	}
	$color_name = substr($color_name, 0, -1);
	//var_dump($recipe_color_arr);
	$recipe_id=$data_array[0][csf("recipe_id")];
	$recipe_arr=sql_select("select a.working_company_id,a.company_id, b.batch_weight from pro_recipe_entry_mst a, pro_batch_create_mst b where a.batch_id=b.id and a.id=$recipe_id");
	$owner_company_id=$recipe_arr[0][csf('company_id')];
	$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_id");	$group_id=$nameArray[0][csf('group_id')];
	?>
	<div style="width:1000px;">
	<table width="1000" cellspacing="0" align="center">
		<tr>
			<td colspan="10" align="center" style="font-size:xx-large"><strong><? echo 'Group Name :'.$group_library[$group_id].'<br/>'.'Working Company : '.$company_name;//$company_name; ?></strong>
			</td>
		</tr>
		<tr class="form_caption">
			<td colspan="6" align="center" style="font-size:14px">
			<?

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
		<td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo 'Owner Company : '.$company_library[$owner_company_id].'<br>'.$data[1]; ?> Report
			(Adding/Topping)</u></strong></td>
		</tr>
	</table>
	<table width="950" cellspacing="0" align="center">
		<tr>
			<td width="90"><strong>Req. ID </strong></td>
			<td width="160px"><? echo $dataArray[0][csf('requ_no')]; ?></td>
			<td width="100"><strong>Req. Date</strong></td>
			<td width="160px"><? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
			<td width="90"><strong>Buyer</strong></td>
			<td width="160px"><? echo $buyer_name; ?></td>
		</tr>
		<tr>
			<td><strong>Order No</strong></td>
			<td><? echo $po_no; ?></td>
			<td><strong>Style No</strong></td>
			<td><? echo $style_ref; ?></td>
			<td><strong>Booking No</strong></td>
			<td><? echo $booking_no; ?></td>
		</tr>
		<tr>
			<td><strong>Batch No</strong></td>
			<td><? echo $batchdata_array[0][csf('batch_no')]; ?></td>
			<td><strong>Recipe No</strong></td>
			<td><? echo $data_array[0][csf("recipe_id")]; ?></td>

			<td><strong>Color</strong></td>
			<td><? echo $color_name; ?></td>
		</tr>
		<tr>
			<td><strong>Ext. No</strong></td>
			<td><? echo $extention_no; ?></td>
			<td>Liquor Ratio</td>
			<td><? //echo $data_array[0][csf("ratio")]; ?></td>
			<td><strong>Total Liq.(ltr)</strong></td>
			<td><? echo $data_array[0][csf("total_liquor")]; ?></td>
		</tr>
		<tr>
			<td><strong>Batch Weight</strong></td>
			<td><? echo $batch_weight + $batchdata_array[0][csf('batch_weight')]; ?></td>
			<td><strong>Machine No</strong></td>
			<td>
				<?
				$machine_data = sql_select("select machine_no, floor_id from lib_machine_name where id='" . $dataArray[0][csf("machine_id")] . "'");
				echo $machine_data[0][csf('machine_no')];
				?>
			</td>
			<td><strong>Floor Name</strong></td>
			<td colspan="">
				<?
				$floor_name = return_field_value("floor_name", "lib_prod_floor", "id='" . $machine_data[0][csf('floor_id')] . "'");
				echo $floor_name;
				?>
			</td>
		</tr>

		<tr>
			<td><strong>Labdip No</strong></td>
			<td>
				<?
				$labdip_no = '';
				$recipe_ids = explode(",", $data_array[0][csf("recipe_id")]);
				foreach ($recipe_ids as $recp_id) {
					$labdip_no .= $receipe_arr[$recp_id] . ",";
				}
				echo chop($labdip_no, ',');
				?>
			</td>
			<td><strong>Job No</strong></td>
			<td><? echo $job_no; ?></td>
			<td><strong>Issue Basis</strong></td>
			<td><? echo $receive_basis_arr[$dataArray[0][csf('requisition_basis')]]; ?></td>
		</tr>
		<tr>

			<td>Method</td>
			<td><? echo $dyeing_method[$dataArray[0][csf("method")]]; ?></td>
		</tr>
	</table>
	<br>
	<? $batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
	$j = 1;
	$entryForm = $entry_form_arr[$batch_id_qry[0]]; ?>
	<table width="1000" cellspacing="0" align="center" class="rpt_table" border="1" rules="all">
		<thead bgcolor="#dddddd" align="center">
			<?
			if ($entryForm == 74) {
				?>
				<tr bgcolor="#CCCCFF">
					<th colspan="4" align="center"><strong>Fabrication</strong></th>
				</tr>
				<tr>
					<th width="50">SL</th>
					<th width="350">Gmts. Item</th>
					<th width="110">Gmts. Qty</th>
					<th>Batch Qty.</th>
				</tr>
				<?
			} else {
				?>
				<tr bgcolor="#CCCCFF">
					<th colspan="8" align="center"><strong>Fabrication</strong></th>
				</tr>
				<tr>
					<th width="30">SL</th>
					<th width="100">Dia/ W. Type</th>
					<th width="100">Yarn Lot</th>
					<th width="100">Brand</th>
					<th width="100">Count</th>
					<th width="300">Constrution & Composition</th>
					<th width="70">Gsm</th>
					<th width="70">Dia</th>
				</tr>
				<?
			}
			?>
		</thead>
		<tbody>
			<?
			foreach ($batch_id_qry as $b_id) {
				//$batch_query="select id, po_id, prod_id, item_description, width_dia_type, roll_no as gmts_qty, batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0";
				$batch_query = "select po_id, prod_id, item_description, width_dia_type, count(roll_no) as gmts_qty, sum(batch_qnty) as batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by  po_id, prod_id, item_description, width_dia_type";
				$result_batch_query = sql_select($batch_query);
				foreach ($result_batch_query as $rows) {
					if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					$fabrication_full = $rows[csf("item_description")];
					$fabrication = explode(',', $fabrication_full);
					if ($entry_form_arr[$b_id] == 36) {
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $j; ?></td>
							<td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
                            <td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['yarn_lot'];
                            	?></td>
                            <td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['brand_name'];
                            	?></td>
                            <td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['count_name'];
                            	?></td>
                            	<td align="left"><? echo $fabrication[0]; ?></td>
                            	<td align="center"><? echo $fabrication[1]; ?></td>
                            	<td align="center"><? echo $fabrication[3]; ?></td>
                            </tr>
                            <?
                        } else if ($entry_form_arr[$b_id] == 74) {
                        	?>
                        	<tr bgcolor="<? echo $bgcolor; ?>">
                        		<td align="center"><? echo $j; ?></td>
                        		<td align="left"><? echo $garments_item[$rows[csf('prod_id')]]; ?></td>
                        		<td align="right"><? echo $rows[csf('gmts_qty')]; ?></td>
                        		<td align="right"><? echo number_format($rows[csf('batch_qnty')], 2); ?></td>
                        	</tr>
                        	<?
                        } else {
                        	?>
                        	<tr bgcolor="<? echo $bgcolor; ?>">
                        		<td align="center"><? echo $j; ?></td>
                        		<td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
                        		<td align="left"><? echo $yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['yarn_lot']; ?></td>
                        		<td align="left"><? echo $yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['brand_name']; ?></td>
                        		<td align="left"><? echo $yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['count_name']; ?></td>
                        		<td align="left"><? echo $fabrication[0] . ", " . $fabrication[1]; ?></td>
                        		<td align="center"><? echo $fabrication[2]; ?></td>
                        		<td align="center"><? echo $fabrication[3]; ?></td>
                        	</tr>
                        	<?
                        }
                        $j++;
                    }
                }
                ?>
            </tbody>
        </table>
        <div style="width:1000px; margin-top:10px">
        	<table align="right" cellspacing="0" width="1000" border="1" rules="all" class="rpt_table">
        		<thead bgcolor="#dddddd" align="center">
        			<tr bgcolor="#CCCCFF">
        				<th colspan="17" align="center"><strong>Dyes And Chemical Issue Requisition</strong></th>
        			</tr>

        			<?

        			$group_arr = return_library_array("select id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0", 'id', 'item_name');
        			$item_lot_arr = array();
        			$recipeData_arr = array();
        			$old_recp_id_arr = array();
				/*if($db_type==0)
            	{
            		$item_lot_arr=return_library_array("select b.prod_id,group_concat(b.item_lot) as yarn_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yarn_lot" );
            	}
            	else
            	{
            		$item_lot_arr=return_library_array("select b.prod_id,listagg((cast(b.item_lot as varchar2(500))),',') within group (order by b.prod_id) as yarn_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yarn_lot" );
            	}*/

				$recipeData = sql_select("select a.id, a.recipe_id, a.entry_form, a.batch_id, a.total_liquor, b.sub_process_id, b.prod_id, b.item_lot, b.ratio, b.adj_perc, b.new_item, b.dose_base from pro_recipe_entry_mst a, pro_recipe_entry_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.id in ($recipe_id)");// and a.id in ($recipe_id)
				foreach ($recipeData as $rowR) {
					$item_lot_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]] .= $rowR[csf('item_lot')] . ",";
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['entry_form'] = $rowR[csf('entry_form')];
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['recipe_id'] = $rowR[csf('recipe_id')];
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['batch_id'] = $rowR[csf('batch_id')];
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['ratio'] = $rowR[csf('ratio')];
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['new_item'] = $rowR[csf('new_item')];
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['adj_perc'] = $rowR[csf('adj_perc')];
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['dose_base'] = $rowR[csf('dose_base')];
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['total_liquor'] = $rowR[csf('total_liquor')];
					$old_recp_id_arr[$rowR[csf('id')]] = $rowR[csf('recipe_id')];
				}
				$sql_rec_remark = "select b.sub_process_id as sub_process_id,b.comments,b.process_remark from pro_recipe_entry_dtls b  where  b.mst_id in($recipe_id) and b.sub_process_id in($subprocessforwashin) and b.status_active=1 and b.is_deleted=0";
				$nameArray_re = sql_select($sql_rec_remark);
				foreach ($nameArray_re as $row) {
					$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"] = $row[csf("process_remark")];
				}

				//print_r($recipeData_arr[276]);
				$sql_dtls = "(select a.requ_no, a.batch_id, a.recipe_id, b.id, b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit, c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit, c.id as prod_id
				from dyes_chem_issue_requ_mst a, dyes_chem_issue_requ_dtls b, product_details_master c
				where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit>0 and c.item_category_id in (5,6,7,23) and a.id=$data[0])
				union
				(
				select a.requ_no, a.batch_id, a.recipe_id, b.id, b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit, null as item_description,  null as item_group_id, null as sub_group_name,  null as item_size,  null as unit_of_measure, null as avg_rate_per_unit, null as prod_id
				from dyes_chem_issue_requ_mst a, dyes_chem_issue_requ_dtls b
				where a.id=b.mst_id  and  b.sub_process in ($subprocessforwashin) and a.id=$data[0]
				) order by id";
				// echo $sql_dtls;//die;
				$sql_result = sql_select($sql_dtls);

				//var_dump($sub_process_tot_req_array);
				$i = 1;
				$k = 1;
				$recipe_qnty_sum = 0;
				$req_qny_edit_sum = 0;
				$recipe_qnty = 0;
				$req_qny_edit = 0;
				$req_value_sum = 0;
				$req_value_grand = 0;
				$recipe_qnty_grand = 0;
				$req_qny_edit_grand = 0;
				$sub_process_array = array();

				foreach ($sql_result as $row)
				{
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";

					if (!in_array($row[csf("sub_process")], $sub_process_array))
					{
						$sub_process_array[] = $row[csf('sub_process')];
						if ($k != 1) {
							?>
							<tr>
								<td colspan="8" align="right"><strong>Total :</strong></td>
								<td align="right"><?php echo number_format($recipe_qnty_sum, 6, '.', ''); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right"><?php echo number_format($req_qny_edit_sum, 6, '.', ''); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right"><?php echo number_format($req_value_sum, 6, '.', ''); ?></td>
							</tr>
							<?
							$recipe_qnty_sum = 0;
							$req_qny_edit_sum = 0;
							$req_value_sum = 0;
						}
						$k++;


						//if ($row[csf("sub_process")] == 93 || $row[csf("sub_process")] == 94 || $row[csf("sub_process")] == 95 || $row[csf("sub_process")] == 96 || $row[csf("sub_process")] == 97 || $row[csf("sub_process")] == 98) {
						if(in_array($row[csf("sub_process")],$subprocessForWashArr))
						{
							$pro_remark = $process_array_remark[$row[csf("sub_process")]]["wash_comments"];
						}

						$batch_ratio=explode(":",$data_array[0][csf("ratio")]);
						$lqr_ratio=$batch_ratio[0];
						$ratio_qty=$lqr_ratio.':'.$ratio_arr[$row[csf('sub_process')]]['ratio'];
						if ($pro_remark == '') $pro_remark = ''; else $pro_remark .= $pro_remark . ', ';
						?>
						<tr bgcolor="#CCCCCC">
							<th colspan="17">
								<strong><? echo $dyeing_sub_process[$row[csf("sub_process")]] . ', ' . $pro_remark.$ratio_qty; ?></strong>
							</th>
						</tr>
						<tr>
							<th width="30">SL</th>
							<th width="80">Item Cat.</th>
							<th width="80">Item Group</th>
							<th width="100">Item Description</th>
							<th width="50">Dyes Lot</th>
							<th width="50">UOM</th>
							<th width="100">Dose Base</th>
							<th width="40">Old Ratio</th>
							<th width="60">Old Recipe Qty.</th>
							<th width="50">New Adj%</th>
							<th width="60">New Ratio</th>
							<th width="80">New Recipe Qty.</th>
							<th width="60">KG</th>
							<th width="60">GM</th>
							<th width="60">MG</th>
							<th width="70">Avg. Rate</th>
							<th width="80">Issue Vaule</th>
						</tr>
					</thead>
					<?
				}

				$req_qny_edit = explode(".", (string)$row[csf("req_qny_edit")]);
				$iss_qnty_kg = $req_qny_edit[0];
				if ($iss_qnty_kg == "") $iss_qnty_kg = 0;

				$iss_qnty_gm = substr($req_qny_edit[1], 0, 3);//$rem[0]; // floor($mg/1000);
				$iss_qnty_mg = substr($req_qny_edit[1], 3, 3);//$rem[1];//round($mg-$iss_qnty_gm*1000);
				$iss_qnty_gm = str_pad($iss_qnty_gm, 3, "0", STR_PAD_RIGHT);
				$iss_qnty_mg = str_pad($iss_qnty_mg, 3, "0", STR_PAD_RIGHT);

				$adj_perc = $recipeData_arr[$row[csf('recipe_id')]][$row[csf('sub_process')]][$row[csf('prod_id')]]['adj_perc'];
				$oldRecpId = $old_recp_id_arr[$row[csf('recipe_id')]];
				$old_ratio = $recipeData_arr[$oldRecpId][$row[csf('sub_process')]][$row[csf('prod_id')]]['ratio'];
				$selected_dose = $recipeData_arr[$oldRecpId][$row[csf('sub_process')]][$row[csf('prod_id')]]['dose_base'];
				$actual_total_liquor = $recipeData_arr[$oldRecpId][$row[csf('sub_process')]][$row[csf('prod_id')]]['total_liquor'];
				$actual_batch_weight = $batch_weight_arr[$recipeData_arr[$row[csf('recipe_id')]][$row[csf('sub_process')]][$row[csf('prod_id')]]['batch_id']];

				$prev_recipe_qty = 0;
				if ($selected_dose == 1) {
					$prev_recipe_qty = number_format(($actual_total_liquor * $old_ratio) / 1000, 4);
				} else if ($selected_dose == 2) {
					$prev_recipe_qty = number_format(($actual_batch_weight * $old_ratio) / 100, 4);
				}

				?>
				<tbody>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td align="center"><? echo $i; ?></td>
                    <td><? echo $item_category[$row[csf("item_category")]];  //echo $row[csf("sub_process")];
                    	?></td>
                    	<td><? echo $group_arr[$row[csf("item_group_id")]]; ?></td>
                    	<td><? echo $row[csf("item_description")] . ' ' . $row[csf("item_size")]; ?></td>
                    	<td><? echo implode(",", array_unique(array_filter(explode(",", $item_lot_arr[$row[csf('recipe_id')]][$row[csf("sub_process")]][$row[csf("prod_id")]])))); ?></td>
                    	<td align="center"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
                    	<td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
                    	<td align="center"><? echo number_format($old_ratio, 6, '.', ''); ?></td>
                    	<td align="right"><? echo number_format($prev_recipe_qty, 6, '.', ''); ?></td>
                    	<td align="right"><? echo $adj_perc; ?></td>
                    	<td align="right"
                    	title="<? echo "New Ratio: " . $row[csf("ratio")] . ", Adj Perc: " . $row[csf("adjust_percent")]; ?>"><? echo number_format($row[csf("ratio")], 6, '.', ''); ?></td>
                    	<td align="right"><? echo number_format($row[csf("req_qny_edit")], 6, '.', ''); ?></td>
                    	<td align="right"><? echo $iss_qnty_kg; ?></td>
                    	<td align="right"><? echo $iss_qnty_gm; ?></td>
                    	<td align="right"><? echo $iss_qnty_mg; ?></td>
                    	<td align="right"><? echo number_format($row[csf("avg_rate_per_unit")], 6, '.', ''); ?></td>
                    	<td align="right"><? $req_value = $row[csf("req_qny_edit")] * $row[csf("avg_rate_per_unit")];
                    		echo number_format($req_value, 6, '.', ''); ?></td>
                    	</tr>
                    </tbody>
                    <?
                    $i++;
                    $recipe_qnty_sum += $prev_recipe_qty;
                    $req_qny_edit_sum += $row[csf('req_qny_edit')];
                    $req_value_sum += $req_value;

                    $recipe_qnty_grand += $prev_recipe_qty;
                    $req_qny_edit_grand += $row[csf('req_qny_edit')];
                    $req_value_grand += $req_value;
                }
                ?>
                <tr>
                	<td colspan="8" align="right"><strong>Total :</strong></td>
                	<td align="right"><?php echo number_format($recipe_qnty_sum, 6, '.', ''); ?></td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td align="right"><?php echo number_format($req_qny_edit_sum, 6, '.', ''); ?></td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td align="right"><?php echo number_format($req_value_sum, 6, '.', ''); ?></td>
                </tr>
                <tr>
                	<td colspan="8" align="right"><strong> Grand Total :</strong></td>
                	<td align="right"><?php echo number_format($recipe_qnty_grand, 6, '.', ''); ?></td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td align="right"><?php echo number_format($req_qny_edit_grand, 6, '.', ''); ?></td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td align="right"><?php echo number_format($req_value_grand, 6, '.', ''); ?></td>
                </tr>
                <tr>
                	<td colspan="15" align="right"><strong> Cost Per Kg :</strong></td>
                	<td colspan="2"
                	align="right"><?php echo number_format($req_value_grand / ($batch_weight + $batchdata_array[0][csf('batch_weight')]), 6, '.', ''); ?></td>
                </tr>
            </table>
            <br>
            <?
            echo signature_table(15, $company_id, "900px");
            ?>
        </div>
    </div>
    <?
    exit();
}
if ($action == "print_adding_topping_without_rate_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	//print_r ($data);

	$sql = "select a.id, a.requ_no, a.company_id, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id from dyes_chem_issue_requ_mst a where a.id='$data[0]'";
	$dataArray = sql_select($sql);

	$recipe_id = $dataArray[0][csf('recipe_id')];
	$company_id = $dataArray[0][csf('company_id')];

	$company_name = return_field_value("company_name", "lib_company", "id=$company_id");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	// $receipe_arr = array();
	// $receipeData = sql_select("select id,labdip_no, total_liquor from pro_recipe_entry_mst where status_active=1 and is_deleted=0");
	// foreach ($receipeData as $row) {
	// 	$receipe_arr[$row[csf("id")]] = $row[csf("labdip_no")];
	// }
	// $entry_form_arr = array();
	// $batch_weight_arr = array();
	// $batchData = sql_select("select id, entry_form, batch_weight from pro_batch_create_mst");
	// foreach ($batchData as $rowB) {
	// 	$entry_form_arr[$rowB[csf('id')]] = $rowB[csf('entry_form')];
	// 	$batch_weight_arr[$rowB[csf('id')]] = $rowB[csf('batch_weight')];
	// }
	$receipe_arr = array();	$entry_form_arr = array();
	$batch_weight_arr = array();
	$receipeData = sql_select("select c.id as batch_id,c.entry_form,c.batch_weight,b.id,b.labdip_no, b.total_liquor from pro_recipe_entry_mst b,pro_batch_create_mst c where c.id=b.batch_id and b.status_active=1 and b.is_deleted=0 and b.id in($recipe_id)");
	foreach ($receipeData as $row) {
		$receipe_arr[$row[csf("id")]] = $row[csf("labdip_no")];
		$entry_form_arr[$rowB[csf('batch_id')]] = $rowB[csf('entry_form')];
		$batch_weight_arr[$rowB[csf('batch_id')]] = $rowB[csf('batch_weight')];
	}

	$batch_weight = 0;
	if ($db_type == 0) {
		$data_array = sql_select("select group_concat(buyer_id) as buyer_id,group_concat(id) as recipe_id, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor, group_concat(concat_ws(':',batch_ratio,liquor_ratio) order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, group_concat(case when entry_form<>60 then batch_id end) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
	} else {
		$data_array = sql_select("select listagg(buyer_id,',') within group (order by buyer_id) as buyer_id,listagg(id,',') within group (order by id) as recipe_id, listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor, listagg(batch_ratio || ':' || liquor_ratio,',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(case when entry_form<>60 then batch_id end,',') within group (order by batch_id) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
	}

	$batch_weight = $data_array[0][csf("batch_weight")];
	$batch_id = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id")])));

	$batch_id_rec_main = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id_rec_main")])));
	if ($batch_id_rec_main == "") $batch_id_rec_main = 0;

	if ($db_type == 0) {
		$batchdata_array = sql_select("select group_concat(batch_no) as batch_no, group_concat(booking_no) as booking_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(color_id) as color_id,group_concat(extention_no) as extention_no  from pro_batch_create_mst where id in($batch_id)");
	} else {
		$batchdata_array = sql_select("select listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, listagg(CAST(booking_no AS VARCHAR2(4000)),',') within group (order by id) as booking_no, listagg(color_id ,',') within group (order by id) as color_id,listagg(extention_no ,',') within group (order by id) as extention_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($batch_id)");
	}

	$po_no = '';
	$job_no = '';
	$style_ref = '';
	$buyer_name = '';
	foreach (explode(",", $data_array[0][csf("batch_id")]) as $b_id) {
		if ($entry_form_arr[$b_id] == 36) {
			$po_data = sql_select("select b.order_no, b.cust_style_ref, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") group by b.order_no, b.cust_style_ref, c.subcon_job, c.party_id");
			foreach ($po_data as $row) {
				$po_no .= $row[csf('order_no')] . ",";
				$job_no .= $row[csf('subcon_job')] . ",";
				$style_ref .= $row[csf('cust_style_ref')] . ",";
				$buyer_name .= $buyer_library[$row[csf('party_id')]] . ",";
			}
		} else {
			$po_data = sql_select("select b.po_number, c.job_no, c.buyer_name, c.style_ref_no from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") group by b.po_number, c.job_no, c.buyer_name, c.style_ref_no");
			foreach ($po_data as $row) {
				$po_no .= $row[csf('po_number')] . ",";
				$job_no .= $row[csf('job_no')] . ",";
				$style_ref .= $row[csf('style_ref_no')] . ",";
				//$buyer_name.=$buyer_library[$row[csf('buyer_name')]].",";
			}
			foreach (explode(",", $data_array[0][csf("buyer_id")]) as $buyer_id) {
				$buyer_name .= $buyer_library[$buyer_id] . ",";
			}
		}
	}

	if ($db_type == 0) {
		$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
	} else if ($db_type == 2) {
		$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, listagg((cast(a.yarn_lot as varchar2(4000))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(a.brand_id,',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(4000))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id";
	}

	$yarn_dtls_array = array();
	$result_sql_yarn_dtls = sql_select($sql_yarn_dtls);
	foreach ($result_sql_yarn_dtls as $row) {
		$yarn_lot = implode(",", array_unique(explode(",", $row[csf('yarn_lot')])));
		$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
		$brand_name = "";
		foreach ($brand_id as $val) {
			if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= ", " . $brand_arr[$val];
		}

		$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
		$count_name = "";
		foreach ($yarn_count as $val) {
			if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
		}
		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['yarn_lot'] = $yarn_lot;
		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['brand_name'] = $brand_name;
		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['count_name'] = $count_name;
	}
	//var_dump($yarn_dtls_array);
	$po_no = implode(", ", array_unique(explode(",", substr($po_no, 0, -1))));
	$job_no = implode(", ", array_unique(explode(",", substr($job_no, 0, -1))));
	$style_ref = implode(", ", array_unique(explode(",", substr($style_ref, 0, -1))));
	$buyer_name = implode(",", array_unique(explode(",", substr($buyer_name, 0, -1))));
	$booking_no = implode(",", array_unique(array_filter(explode(",", $batchdata_array[0][csf('booking_no')]))));
	$extention_no = implode(",", array_unique(array_filter(explode(",", $batchdata_array[0][csf('extention_no')]))));


	$color_id = array_unique(explode(",", $batchdata_array[0][csf('color_id')]));
	$color_name = '';
	foreach ($color_id as $color) {
		$color_name .= $color_arr[$color] . ",";
	}
	$color_name = substr($color_name, 0, -1);
	//var_dump($recipe_color_arr);
	$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_id");
	//echo "select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_id";
	$group_id=$nameArray[0][csf('group_id')];
	$recipe_id=$data_array[0][csf("recipe_id")];
	$recipe_arr=sql_select("select a.working_company_id,a.company_id, b.batch_weight from pro_recipe_entry_mst a, pro_batch_create_mst b where a.batch_id=b.id and a.id=$recipe_id");
	//echo "select a.working_company_id,a.company_id, b.batch_weight from pro_recipe_entry_mst a, pro_batch_create_mst b where a.batch_id=b.id and a.id=$recipe_id";
	$owner_company_id=$recipe_arr[0][csf('company_id')];
	//echo $owner_company_id.'DSDS';
	?>
	<div style="width:1000px;">
		<table width="1000" cellspacing="0" align="center">
			<tr>
				<td colspan="10" align="center" style="font-size:xx-large"><strong><? echo 'Group Name :'.$group_library[$group_id].'<br/>'.'Working Company : '.$company_name;//$company_name; ?></strong>
				</td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">
					<?

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
					$print_name=$data[1].' Report (Adding/Topping)';
					?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo 'Owner Company : '.$company_library[$owner_company_id].'<br/>'.$print_name; ?> </u></strong></td>
				</tr>
			</table>
			<table width="950" cellspacing="0" align="center">
				<tr>
					<td width="90"><strong>Req. ID </strong></td>
					<td width="160px"><? echo $dataArray[0][csf('requ_no')]; ?></td>
					<td width="100"><strong>Req. Date</strong></td>
					<td width="160px"><? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
					<td width="90"><strong>Buyer</strong></td>
					<td width="160px"><? echo $buyer_name; ?></td>
				</tr>
				<tr>
					<td><strong>Order No</strong></td>
					<td><? echo $po_no; ?></td>
					<td><strong>Style No</strong></td>
					<td><? echo $style_ref; ?></td>
					<td><strong>Booking No</strong></td>
					<td><? echo $booking_no; ?></td>
				</tr>
				<tr>
					<td><strong>Batch No</strong></td>
					<td><? echo $batchdata_array[0][csf('batch_no')]; ?></td>
					<td><strong>Recipe No</strong></td>
					<td><? echo $data_array[0][csf("recipe_id")]; ?></td>

					<td><strong>Color</strong></td>
					<td><? echo $color_name; ?></td>
				</tr>
				<tr>
					<td><strong>Ext. No</strong></td>
					<td><? echo $extention_no; ?></td>
					<td><strong>Batch Weight</strong></td>
					<td><? $tot_bathc_weight=$batch_weight + $batchdata_array[0][csf('batch_weight')];echo $tot_bathc_weight; ?></td>
					<td><strong>Machine No</strong></td>
					<td>
						<?
						$machine_data = sql_select("select machine_no, floor_id from lib_machine_name where id='" . $dataArray[0][csf("machine_id")] . "'");
						echo $machine_data[0][csf('machine_no')];
						?>
					</td>

				</tr>
				<tr>

                <!-- <td>Liquor Ratio</td><td><? echo $data_array[0][csf("ratio")]; ?></td>
                <td><strong>Total Liq.(ltr)</strong></td><td><? echo $data_array[0][csf("total_liquor")]; ?></td>-->

                <td><strong>Floor Name</strong></td>
                <td colspan="">
                	<?
                	$floor_name = return_field_value("floor_name", "lib_prod_floor", "id='" . $machine_data[0][csf('floor_id')] . "'");
                	echo $floor_name;
                	?>
                </td>
                <td>Method</td>
                <td><? echo $dyeing_method[$dataArray[0][csf("method")]]; ?></td>
                <td><strong>Labdip No</strong></td>
                <td>
                	<?
                	$labdip_no = '';
                	$recipe_ids = explode(",", $data_array[0][csf("recipe_id")]);
                	foreach ($recipe_ids as $recp_id) {
                		$labdip_no .= $receipe_arr[$recp_id] . ",";
                	}
                	echo chop($labdip_no, ',');
                	?>
                </td>
            </tr>
            <tr>


            	<td><strong>Job No</strong></td>
            	<td><? echo $job_no; ?></td>
            	<td><strong>Issue Basis</strong></td>
            	<td><? echo $receive_basis_arr[$dataArray[0][csf('requisition_basis')]]; ?></td>
            </tr>

        </table>
        <br>
        <? $batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
        $j = 1;
        $entryForm = $entry_form_arr[$batch_id_qry[0]]; ?>
        <table width="1000" cellspacing="0" align="center" class="rpt_table" border="1" rules="all">
        	<thead bgcolor="#dddddd" align="center">
        		<?
        		if ($entryForm == 74) {
        			?>
        			<tr bgcolor="#CCCCFF">
        				<th colspan="4" align="center"><strong>Fabrication</strong></th>
        			</tr>
        			<tr>
        				<th width="50">SL</th>
        				<th width="350">Gmts. Item</th>
        				<th width="110">Gmts. Qty</th>
        				<th>Batch Qty.</th>
        			</tr>
        			<?
        		} else {
        			?>
        			<tr bgcolor="#CCCCFF">
        				<th colspan="8" align="center"><strong>Fabrication</strong></th>
        			</tr>
        			<tr>
        				<th width="30">SL</th>
        				<th width="100">Dia/ W. Type</th>
        				<th width="100">Yarn Lot</th>
        				<th width="100">Brand</th>
        				<th width="100">Count</th>
        				<th width="300">Constrution & Composition</th>
        				<th width="70">Gsm</th>
        				<th width="70">Dia</th>
        			</tr>
        			<?
        		}
        		?>
        	</thead>
        	<tbody>
        		<?
        		foreach ($batch_id_qry as $b_id) {
				//$batch_query="select id, po_id, prod_id, item_description, width_dia_type, roll_no as gmts_qty, batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0";
        			$batch_query = "select po_id, prod_id, item_description, width_dia_type, count(roll_no) as gmts_qty, sum(batch_qnty) as batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by  po_id, prod_id, item_description, width_dia_type";
        			$result_batch_query = sql_select($batch_query);
        			foreach ($result_batch_query as $rows) {
        				if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
        				$fabrication_full = $rows[csf("item_description")];
        				$fabrication = explode(',', $fabrication_full);
        				if ($entry_form_arr[$b_id] == 36) {
        					?>
        					<tr bgcolor="<? echo $bgcolor; ?>">
        						<td align="center"><? echo $j; ?></td>
        						<td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
                            <td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['yarn_lot'];
                            	?></td>
                            <td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['brand_name'];
                            	?></td>
                            <td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['count_name'];
                            	?></td>
                            	<td align="left"><? echo $fabrication[0]; ?></td>
                            	<td align="center"><? echo $fabrication[1]; ?></td>
                            	<td align="center"><? echo $fabrication[3]; ?></td>
                            </tr>
                            <?
                        } else if ($entry_form_arr[$b_id] == 74) {
                        	?>
                        	<tr bgcolor="<? echo $bgcolor; ?>">
                        		<td align="center"><? echo $j; ?></td>
                        		<td align="left"><? echo $garments_item[$rows[csf('prod_id')]]; ?></td>
                        		<td align="right"><? echo $rows[csf('gmts_qty')]; ?></td>
                        		<td align="right"><? echo number_format($rows[csf('batch_qnty')], 2); ?></td>
                        	</tr>
                        	<?
                        } else {
                        	?>
                        	<tr bgcolor="<? echo $bgcolor; ?>">
                        		<td align="center"><? echo $j; ?></td>
                        		<td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
                        		<td align="left"><? echo $yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['yarn_lot']; ?></td>
                        		<td align="left"><? echo $yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['brand_name']; ?></td>
                        		<td align="left"><? echo $yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['count_name']; ?></td>
                        		<td align="left"><? echo $fabrication[0] . ", " . $fabrication[1]; ?></td>
                        		<td align="center"><? echo $fabrication[2]; ?></td>
                        		<td align="center"><? echo $fabrication[3]; ?></td>
                        	</tr>
                        	<?
                        }
                        $j++;
                    }
                }
                ?>
            </tbody>
        </table>
        <div style="width:1000px; margin-top:10px">
        	<table align="right" cellspacing="0" width="1000" border="1" rules="all" class="rpt_table">
        		<thead bgcolor="#dddddd" align="center">
        			<tr bgcolor="#CCCCFF">
        				<th colspan="17" align="center"><strong>Dyes And Chemical Issue Requisition</strong></th>
        			</tr>

        			<?

        			$group_arr = return_library_array("select id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0", 'id', 'item_name');
        			$item_lot_arr = array();
        			$recipeData_arr = array();
        			$old_recp_id_arr = array();
				/*if($db_type==0)
	{
		$item_lot_arr=return_library_array("select b.prod_id,group_concat(b.item_lot) as yarn_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yarn_lot" );
	}
	else
	{
		$item_lot_arr=return_library_array("select b.prod_id,listagg((cast(b.item_lot as varchar2(500))),',') within group (order by b.prod_id) as yarn_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yarn_lot" );
	}*/

				$recipeData = sql_select("select a.id, a.recipe_id, a.entry_form, a.batch_id, a.total_liquor, b.sub_process_id, b.prod_id, b.item_lot, b.ratio, b.adj_perc, b.new_item, b.dose_base from pro_recipe_entry_mst a, pro_recipe_entry_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0");// and a.id in ($recipe_id)
				foreach ($recipeData as $rowR) {
					$item_lot_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]] .= $rowR[csf('item_lot')] . ",";
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['entry_form'] = $rowR[csf('entry_form')];
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['recipe_id'] = $rowR[csf('recipe_id')];
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['batch_id'] = $rowR[csf('batch_id')];
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['ratio'] = $rowR[csf('ratio')];
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['new_item'] = $rowR[csf('new_item')];
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['adj_perc'] = $rowR[csf('adj_perc')];
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['dose_base'] = $rowR[csf('dose_base')];
					$recipeData_arr[$rowR[csf('id')]][$rowR[csf('sub_process_id')]][$rowR[csf('prod_id')]]['total_liquor'] = $rowR[csf('total_liquor')];
					$old_recp_id_arr[$rowR[csf('id')]] = $rowR[csf('recipe_id')];
				}


				//print_r($recipeData_arr[276]);
				$sql_dtls = "(select a.requ_no, a.batch_id, a.recipe_id, b.id, b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit, c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit, c.id as prod_id
				from dyes_chem_issue_requ_mst a, dyes_chem_issue_requ_dtls b, product_details_master c
				where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit>0 and c.item_category_id in (5,6,7,23) and a.id=$data[0])
				union
				(
				select a.requ_no, a.batch_id, a.recipe_id, b.id, b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit, null as item_description,  null as item_group_id, null as sub_group_name,  null as item_size,  null as unit_of_measure, null as avg_rate_per_unit, null as prod_id
				from dyes_chem_issue_requ_mst a, dyes_chem_issue_requ_dtls b
				where a.id=b.mst_id  and  b.sub_process in ($subprocessforwashin) and a.id=$data[0]
				) order by sub_process";
				// echo $sql_dtls;//die;
				$sql_result = sql_select($sql_dtls);

				//var_dump($sub_process_tot_req_array);
				$i = 1;
				$k = 1;
				$recipe_qnty_sum = 0;
				$req_qny_edit_sum = 0;
				$recipe_qnty = 0;
				$req_qny_edit = 0;
				$req_value_sum = 0;
				$req_value_grand = 0;
				$recipe_qnty_grand = 0;
				$req_qny_edit_grand = 0;
				$sub_process_array = array();

				$sql_rec = "select a.id,a.item_group_id,b.liquor_ratio,b.total_liquor,b.sub_process_id as sub_process_id,b.prod_id,b.dose_base,b.adj_type,b.comments,b.process_remark from pro_recipe_entry_dtls b,product_details_master a where a.id=b.prod_id and b.mst_id in($recipe_id) and a.item_category_id in(5,6,7,23) and b.ratio>0 and b.status_active=1 and b.is_deleted=0";
				$nameArray = sql_select($sql_rec);
				$process_array = array();
				$process_array_remark = array();
				foreach ($nameArray as $row) {

					$process_array_liquor[$row[csf("sub_process_id")]]['total_liquor'] = $row[csf("total_liquor")];
					$process_array_liquor[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
					$process_array[$row[csf("sub_process_id")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"] = $row[csf("comments")];

				}
				$sql_rec_remark = "select b.sub_process_id as sub_process_id,b.comments,b.process_remark,b.liquor_ratio,b.total_liquor from pro_recipe_entry_dtls b  where  b.mst_id in($recipe_id) and b.sub_process_id in($subprocessforwashin) and b.status_active=1 and b.is_deleted=0";
				$nameArray_re = sql_select($sql_rec_remark);
				foreach ($nameArray_re as $row) {
					$process_array_liquor[$row[csf("sub_process_id")]]['total_liquor'] = $row[csf("total_liquor")];
					$process_array_liquor[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
					$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"] = $row[csf("process_remark")];
				}
				foreach ($sql_result as $row)
				{
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";

					if (!in_array($row[csf("sub_process")], $sub_process_array))
					{
						$sub_process_array[] = $row[csf('sub_process')];
						if ($k != 1) {
							?>
							<tr>
								<td colspan="8" align="right"><strong>Total :</strong></td>
								<td align="right"><?php echo number_format($recipe_qnty_sum, 6, '.', ''); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right"><?php echo number_format($req_qny_edit_sum, 6, '.', ''); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>

								<td align="right"><?php echo number_format($req_value_sum, 6, '.', ''); ?></td>
							</tr>
							<?
							$recipe_qnty_sum = 0;
							$req_qny_edit_sum = 0;
							$req_value_sum = 0;
						}
						$k++;


						//if ($row[csf("sub_process")] == 93 || $row[csf("sub_process")] == 94 || $row[csf("sub_process")] == 95 || $row[csf("sub_process")] == 96 || $row[csf("sub_process")] == 97 || $row[csf("sub_process")] == 98) {
							if(in_array($row[csf("sub_process")],$subprocessForWashArr))
							{
							$pro_remark = $process_array_remark[$row[csf("sub_process")]]["wash_comments"];
						   }
						if ($pro_remark != '') $pro_remark = "," . $pro_remark; else $pro_remark = "";
						$total_liquor = 'Total liquor(ltr)' . ": " . $process_array_liquor[$row[csf("sub_process")]]['total_liquor'];
						$liquor_ratio = 'Liquor Ratio' . ": " . $process_array_liquor[$row[csf("sub_process")]]['liquor_ratio'];

						$liquor_ratio_process=$process_array_liquor[$row[csf("sub_process")]]['liquor_ratio'];
						$leveling_water=$tot_bathc_weight*($liquor_ratio_process-1.5);

						?>
						<tr bgcolor="#CCCCCC">
							<th colspan="17">
								<strong><? echo $dyeing_sub_process[$row[csf("sub_process")]] . ', ' . $liquor_ratio . ', ' . $total_liquor . $pro_remark.','.'Levelling  Water(Ltr): '.$leveling_water; ?></strong>
							</th>
						</tr>
						<tr>
							<th width="30">SL</th>
							<th width="80">Item Cat.</th>
							<th width="80">Item Group</th>
							<th width="100">Item Description</th>
							<th width="50">Dyes Lot</th>
							<th width="50">UOM</th>
							<th width="100">Dose Base</th>
							<th width="40">Old Ratio</th>
							<th width="60">Old Recipe Qty.</th>
							<th width="50">New Adj%</th>
							<th width="60">New Ratio</th>
							<th width="80">New Recipe Qty.</th>
							<th width="60">KG</th>
							<th width="60">GM</th>
							<th width="60">MG</th>
							<th width="150">Comments</th>

						</tr>
					</thead>
					<?
				}

				$req_qny_edit = explode(".", (string)$row[csf("req_qny_edit")]);
				$iss_qnty_kg = $req_qny_edit[0];
				if ($iss_qnty_kg == "") $iss_qnty_kg = 0;

				$iss_qnty_gm = substr($req_qny_edit[1], 0, 3);//$rem[0]; // floor($mg/1000);
				$iss_qnty_mg = substr($req_qny_edit[1], 3, 3);//$rem[1];//round($mg-$iss_qnty_gm*1000);
				$iss_qnty_gm = str_pad($iss_qnty_gm, 3, "0", STR_PAD_RIGHT);
				$iss_qnty_mg = str_pad($iss_qnty_mg, 3, "0", STR_PAD_RIGHT);
				$comment = $process_array[$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"];
				$adj_perc = $recipeData_arr[$row[csf('recipe_id')]][$row[csf('sub_process')]][$row[csf('prod_id')]]['adj_perc'];
				$oldRecpId = $old_recp_id_arr[$row[csf('recipe_id')]];
				$old_ratio = $recipeData_arr[$oldRecpId][$row[csf('sub_process')]][$row[csf('prod_id')]]['ratio'];
				$selected_dose = $recipeData_arr[$oldRecpId][$row[csf('sub_process')]][$row[csf('prod_id')]]['dose_base'];
				$actual_total_liquor = $recipeData_arr[$oldRecpId][$row[csf('sub_process')]][$row[csf('prod_id')]]['total_liquor'];
				$actual_batch_weight = $batch_weight_arr[$recipeData_arr[$row[csf('recipe_id')]][$row[csf('sub_process')]][$row[csf('prod_id')]]['batch_id']];

				$prev_recipe_qty = 0;
				if ($selected_dose == 1) {
					$prev_recipe_qty = number_format(($actual_total_liquor * $old_ratio) / 1000, 4);
				} else if ($selected_dose == 2) {
					$prev_recipe_qty = number_format(($actual_batch_weight * $old_ratio) / 100, 4);
				}

				?>
				<tbody>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td align="center"><? echo $i; ?></td>
                    <td><? echo $item_category[$row[csf("item_category")]];  //echo $row[csf("sub_process")];
                    	?></td>
                    	<td><? echo $group_arr[$row[csf("item_group_id")]]; ?></td>
                    	<td><? echo $row[csf("item_description")] . ' ' . $row[csf("item_size")]; ?></td>
                    	<td><? echo implode(",", array_unique(array_filter(explode(",", $item_lot_arr[$row[csf('recipe_id')]][$row[csf("sub_process")]][$row[csf("prod_id")]])))); ?></td>
                    	<td align="center"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
                    	<td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
                    	<td align="center"><? echo number_format($old_ratio, 6, '.', ''); ?></td>
                    	<td align="right"><? echo number_format($prev_recipe_qty, 6, '.', ''); ?></td>
                    	<td align="right"><? echo $adj_perc; ?></td>
                    	<td align="right"
                    	title="<? echo "New Ratio: " . $row[csf("ratio")] . ", Adj Perc: " . $row[csf("adjust_percent")]; ?>"><? echo number_format($row[csf("ratio")], 6, '.', ''); ?></td>
                    	<td align="right"><? echo number_format($row[csf("req_qny_edit")], 6, '.', ''); ?></td>
                    	<td align="right"><? echo $iss_qnty_kg; ?></td>
                    	<td align="right"><? echo $iss_qnty_gm; ?></td>
                    	<td align="right"><? echo $iss_qnty_mg;
                    		$comment ?></td>
                    		<td align="right"><? echo $comment; ?></td>
                    	</tr>
                    </tbody>
                    <?
                    $i++;
                    $recipe_qnty_sum += $prev_recipe_qty;
                    $req_qny_edit_sum += $row[csf('req_qny_edit')];
                    $req_value_sum += $req_value;

                    $recipe_qnty_grand += $prev_recipe_qty;
                    $req_qny_edit_grand += $row[csf('req_qny_edit')];
                    $req_value_grand += $req_value;
                }
                ?>
                <tr>
                	<td colspan="8" align="right"><strong>Total :</strong></td>
                	<td align="right"><?php echo number_format($recipe_qnty_sum, 6, '.', ''); ?></td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td align="right"><?php echo number_format($req_qny_edit_sum, 6, '.', ''); ?></td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>

                	<td align="right"></td>
                </tr>
                <tr>
                	<td colspan="8" align="right"><strong> Grand Total :</strong></td>
                	<td align="right"><?php echo number_format($recipe_qnty_grand, 6, '.', ''); ?></td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td align="right"><?php echo number_format($req_qny_edit_grand, 6, '.', ''); ?></td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>

                	<td align="right"></td>
                </tr>

            </table>
            <br>
            <?
            echo signature_table(15, $company_id, "900px");
            ?>
        </div>
    </div>
    <?
    exit();
}
if ($action == "chemical_dyes_issue_requisition_without_rate_print_urmi") {
	extract($_REQUEST);
	echo load_html_head_contents("Chemical Dyes Issue Requisition Print", "../../../", 1, 1, '', '', '');
	$data = explode('*', $data);
	//print_r ($data);

	$sql = "select a.id, a.requ_no, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id from dyes_chem_issue_requ_mst a where a.id='$data[1]' and a.company_id='$data[0]'";
	$dataArray = sql_select($sql);

	$recipe_id = $dataArray[0][csf('recipe_id')];
	//echo $recipe_id.'FF';
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
//	$entry_form_arr = return_library_array("select id, entry_form from pro_batch_create_mst", "id", "entry_form");
	//$order_id_arr=return_library_array( "select id, booking_id from  pro_recipe_entry_mst", "id", "booking_id");
	//$order_no_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
	//$job_no_arr=return_library_array( "select id, job_no from wo_booking_mst", "id", "job_no");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");
	$party_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

	$batch_weight = 0;
	if ($db_type == 0) {
		$data_array = sql_select("select group_concat(buyer_id) as buyer_id,group_concat(id) as recipe_id, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor, group_concat(concat_ws(':',batch_ratio,liquor_ratio) order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, group_concat(case when entry_form<>60 then batch_id end) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
	} else {
		$data_array = sql_select("select listagg(buyer_id,',') within group (order by buyer_id) as buyer_id,listagg(id,',') within group (order by id) as recipe_id, listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor, listagg(batch_ratio || ':' || liquor_ratio,',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(case when entry_form<>60 then batch_id end,',') within group (order by batch_id) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
	}
	$batch_weight = $data_array[0][csf("batch_weight")];
	$batch_id = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id")])));

	$batch_id_rec_main = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id_rec_main")])));
	if ($batch_id_rec_main == "") $batch_id_rec_main = 0;

	if ($db_type == 0) {
		$batchdata_array = sql_select("select group_concat(batch_no) as batch_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(distinct color_id) as color_id from pro_batch_create_mst where id in($batch_id)");
	} else {
		$batchdata_array = sql_select("select listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, listagg(color_id ,',') within group (order by id) as color_id, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($batch_id)");
	}

	$recipe_sql="select a.dyeing_re_process,a.working_company_id,a.company_id,b.booking_no, b.batch_weight,b.extention_no,b.is_sales,c.po_id,b.entry_form from pro_recipe_entry_mst a, pro_batch_create_mst b,pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.batch_id=c.mst_id and a.id in($recipe_id) group by a.working_company_id,a.company_id, b.batch_weight,b.is_sales ,b.booking_no,c.po_id,b.extention_no,a.dyeing_re_process,b.entry_form";
	$result_recipe=sql_select($recipe_sql);
	foreach($result_recipe as $row)
	{
		if($row[csf('is_sales')]==1)
		{
			$fso_mst_id.=$row[csf('po_id')].',';
		}elseif($row[csf('entry_form')]==36){
			$sub_batch_id.=$row[csf('po_id')].',';
			$entry_form_id=$row[csf('entry_form')];
		}
		$owner_company_id=$row[csf('company_id')];
		$extention_no=$row[csf('extention_no')];
		$dyeing_re_process=$dyeing_re_process[$row[csf('dyeing_re_process')]];
	}
	//echo $extention_no.'DFD';
	$fso_mst_id=rtrim($fso_mst_id ,',');
	$sub_batch_id=rtrim($sub_batch_id ,',');
	if($fso_mst_id!='') $fso_mst_cond="and id in($fso_mst_id)";else $fso_mst_cond="and id in(0)";

	 $sales_order_sql = "select id,buyer_id,job_no,within_group,sales_booking_no from fabric_sales_order_mst where status_active=1 and is_deleted=0 $fso_mst_cond";
	$sales_order_result=sql_select($sales_order_sql);
	$sales_no_arr = array();
	$sales_booking_no='';$booking_nos='';
	foreach ($sales_order_result as $sales_row) {
		$sales_booking_no .= "'".$sales_row[csf('sales_booking_no')]."'". ",";
		$booking_nos= $sales_row[csf('sales_booking_no')];
		$sales_fso_no= $sales_row[csf("job_no")];
		$within_group= $sales_row[csf("within_group")];
		$fso_buyer=$buyer_library[$sales_row[csf("buyer_id")]];

	}

	$sales_booking_no=rtrim($sales_booking_no ,',');
	if($sales_booking_no!='') $booking_no_all_cond="and b.booking_no in($sales_booking_no)";else $booking_no_all_cond="and b.booking_no in('0')";

	$without_booking_sql = "select a.buyer_id,a.booking_no,a.booking_type from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no  and b.status_active=1 and b.is_deleted=0 $booking_no_all_cond group by a.buyer_id,a.booking_no,a.booking_type";
	$without_booking_array=sql_select($without_booking_sql);
	 foreach($without_booking_array as $row)
	 {
		 if($row[csf('booking_type')]==4)// Sample Fabric
		 {
			$main_fab_booking='Without Sample Booking';
		 }
		 $buyer_id_arr[$row[csf('booking_no')]]=$row[csf('buyer_id')];
	 }

	// $booking_array = "select  a.buyer_id,a.entry_form,b.po_break_down_id,b.job_no,b.is_short,b.booking_no,b.booking_type from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no  and b.booking_type=1  and b.status_active=1 and b.is_deleted=0 $booking_no_all_cond group by a.entry_form,a.buyer_id,b.po_break_down_id,b.job_no,b.is_short,b.booking_no,b.booking_type";
	$booking_array = "select  a.buyer_id,a.entry_form,b.po_break_down_id,b.job_no,b.is_short,b.booking_no,b.booking_type from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no  and b.booking_type=1  and a.status_active=1 $booking_no_all_cond group by a.entry_form,a.buyer_id,b.po_break_down_id,b.job_no,b.is_short,b.booking_no,b.booking_type";
	$result_booking=sql_select($booking_array);
	$po_break_down_id='';
	foreach($result_booking as $row)
	{
		if($row[csf('booking_type')]==1)// Fabric
		{
			if($row[csf('is_short')]==1)
			{
				$main_fab_booking='Short Booking';
			}
			else if($row[csf('is_short')]==2)
			{
				$main_fab_booking='Main Booking';
			}
			else if($row[csf('is_short')]==2 && $row[csf('entry_form')]==108 )
			{
				$main_fab_booking='Partial Booking';
			}
		}
		else if($row[csf('booking_type')]==4)// Sample Fabric
		{
			$main_fab_booking='Sample Booking';
		}
		$buyer_id_arr[$row[csf('booking_no')]]=$row[csf('buyer_id')];
		$po_break_down_id.=$row[csf('po_break_down_id')].',';

	}
		$po_id=rtrim($po_break_down_id,',');
		if($po_id!="")
	{
		$po_data = sql_select("select  c.job_no,c.style_ref_no, c.buyer_name from  wo_po_break_down b, wo_po_details_master c where  b.job_no_mst=c.job_no and b.status_active=1 and b.is_deleted=0 and b.id in(" .$po_id. ") group by  c.job_no,c.style_ref_no, c.buyer_name");
		foreach($po_data as $row)
		{
			if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
		}
	}
		/*$style_ref_no = '';
	$po_data = sql_select("select  c.job_no,c.style_ref_no, c.buyer_name from  wo_po_break_down b, wo_po_details_master c where  b.job_no_mst=c.job_no and b.status_active=1 and b.is_deleted=0 and b.id in(" . $po_id . ") group by  c.job_no,c.style_ref_no, c.buyer_name");
	 foreach($po_data as $row)
	 {
		if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
	}
	*/
	if ($db_type == 0) {
		$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
	} else if ($db_type == 2) {
		$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, listagg((cast(a.yarn_lot as varchar2(4000))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(a.brand_id,',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(4000))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id";
	}

	$yarn_dtls_array = array();
	$result_sql_yarn_dtls = sql_select($sql_yarn_dtls);
	foreach ($result_sql_yarn_dtls as $row) {
		$yarn_lot = implode(", ", array_unique(explode(",", $row[csf('yarn_lot')])));
		$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
		$brand_name = "";
		foreach ($brand_id as $val) {
			if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= ", " . $brand_arr[$val];
		}

		$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
		$count_name = "";
		foreach ($yarn_count as $val) {
			if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
		}
		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['yarn_lot'] = $yarn_lot;
		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['brand_name'] = $brand_name;
		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['count_name'] = $count_name;
	}
	//var_dump($yarn_dtls_array);
	//$po_no = implode(", ", array_unique(explode(",", substr($po_no, 0, -1))));
	//$job_no = implode(", ", array_unique(explode(",", substr($job_no, 0, -1))));
	//$buyer_name = implode(",", array_unique(explode(",", substr($buyer_name, 0, -1))));
	$party_buy_sql=sql_select("select a.party_id, b.id, b.main_process_id, b.order_no from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in ($sub_batch_id)");
	$book_buyer_name=$buyer_id_arr[$booking_nos];

	if($within_group==2) //No
	{
		// $buyer_name_cond= $buyer_library[$fso_buyer];
		$buyer_name_cond= $fso_buyer;
	}elseif($entry_form_id==36){
		$buyer_name_cond=$party_arr[$party_buy_sql[0][csf('party_id')]];
	}
	else
	{
		 $buyer_name_cond=$buyer_library[$book_buyer_name];

	}



	$color_id = array_unique(explode(",", $batchdata_array[0][csf('color_id')]));
	$color_name = '';
	foreach ($color_id as $color) {
		$color_name .= $color_arr[$color] . ",";
	}
	$color_name = substr($color_name, 0, -1);
	//var_dump($recipe_color_arr);
	$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
	$group_id=$nameArray[0][csf('group_id')];
	$recipe_id=$data_array[0][csf("recipe_id")];


	?>
	<div style="width:1000px;">
		<table width="1000" cellspacing="0" align="center">
			<tr>
				<td colspan="9" align="center" style="font-size:xx-large;">
					<strong><? echo $company_library[$data[0]];//$company_library[$data[0]]; ?></strong></td>
				<td colspan="3" id="barcode_img_id">&nbsp;</td>
		  </tr>
				<tr class="form_caption">
					<td colspan="6" align="center" style="font-size:14px">
						<?
						/*
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
						}*/
						?>
					</td>

				</tr>
				<tr>
					<td colspan="6" align="center" style="font-size: large"><strong><u><? echo $data[2]; ?>
						Report</u></strong></td>
					</tr>
				</table>
				<table width="950" cellspacing="0" align="center">
					<tr>
						<td width="90"><strong>Req. ID </strong></td>
						<td width="160px"><? echo $dataArray[0][csf('requ_no')]; ?></td>
						<td width="100"><strong>Req. Date</strong></td>
						<td width="160px"><? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
						<td width="90"><strong>Buyer</strong></td>
						<td width="160px"><? echo $buyer_name_cond; ?></td>
					</tr>
					<tr>
						<td><strong>FSO No</strong></td>
						<td><? echo $sales_fso_no; ?></td>
						<td><strong>Booking Type</strong></td>
						<td><? echo $main_fab_booking; ?></td>
						<td><strong>Booking No</strong></td>
						<td><? echo $booking_nos; ?></td>

					</tr>
					<tr>
						<td><strong>Batch No</strong></td>
						<td><? echo $batchdata_array[0][csf('batch_no')]; ?></td>
						<td><strong>Batch Weight</strong></td>
						<td><? $tot_bathc_weight=$batch_weight + $batchdata_array[0][csf('batch_weight')];echo number_format($tot_bathc_weight,2,'.',''); ?></td>
						<td><strong>Exten. No</strong></td>
						<td><? echo $extention_no; ?></td>
					</tr>
					<tr>
						<td><strong>Color</strong></td>
						<td><? echo $color_name; ?></td>
						<td><strong>Recipe No</strong></td>
						<td><? echo $data_array[0][csf("recipe_id")]; ?></td>
                		<td><strong>Machine No</strong></td>
						<td>
							<?
							$machine_data = sql_select("select machine_no, floor_id from lib_machine_name where id='" . $dataArray[0][csf("machine_id")] . "'");
							echo $machine_data[0][csf('machine_no')];
							?>
						</td>
					</tr>
					<tr>
						<td><strong>Floor Name</strong></td>
						<td><?
							$floor_name = return_field_value("floor_name", "lib_prod_floor", "id='" . $machine_data[0][csf('floor_id')] . "'");
							echo $floor_name;
							?></td>
						<td><strong>Method</strong></td>
						<td><? echo $dyeing_method[$dataArray[0][csf("method")]];; ?></td>
                		<td><strong>Style Ref.</strong></td>
						<td>
							<?

							echo implode(",", array_unique(explode(",", $style_ref_no)));
							?>
						</td>
					</tr>


			  <tr>
				  <td   width="120"><strong>Dyeing Reprocess</strong></td>
				  <td><? echo $dyeing_re_process; ?></td>

				<td><strong>Issue Basis</strong></td>
				<td><? echo $receive_basis_arr[$dataArray[0][csf('requisition_basis')]]; ?></td>
			  </tr>

    </table>
    <br>
    <?
    $batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
    $j = 1;
    $entryForm = $entry_form_arr[$batch_id_qry[0]];
    ?>
    <table width="1000" cellspacing="0" align="center" class="rpt_table" border="1" style="font-size:18px" rules="all">
    	<thead bgcolor="#dddddd" align="center">

    			<tr bgcolor="#CCCCFF">
    				<th colspan="5" align="center"><strong>Fabrication</strong></th>
    			</tr>
    			<tr>
    				<th width="30">SL</th>
    				<th width="100">Dia/ W. Type</th>

    				<th width="300">Constrution & Composition</th>
    				<th width="70">Gsm</th>
    				<th width="70">Dia</th>
    			</tr>
    	</thead>
    	<tbody>
    		<?
    		foreach ($batch_id_qry as $b_id) {
    			$batch_query = "Select  po_id, prod_id, item_description, width_dia_type, count(roll_no) as gmts_qty, sum(batch_qnty) batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by po_id, prod_id, item_description, width_dia_type";
    			$result_batch_query = sql_select($batch_query);
    			foreach ($result_batch_query as $rows) {
    				if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
    				$fabrication_full = $rows[csf("item_description")];
    				$fabrication = explode(',', $fabrication_full);

					?>
                        	<tr bgcolor="<? echo $bgcolor; ?>">
                        		<td align="center"><? echo $j; ?></td>
                        		<td align="center"><b><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></b></td>

                        		<td align="left"><b><? echo $fabrication[0] . ", " . $fabrication[1]; ?></b></td>
                        		<td align="center"><? echo $fabrication[2]; ?></td>
                        		<td align="center"><? echo $fabrication[3]; ?></td>
                        	</tr>
                        	<?
                        $j++;
                    }
                }
                ?>
            </tbody>
        </table>
        <div style="width:1120px; margin-top:10px">
        	<table align="right" cellspacing="0" width="1120" border="1" rules="all" style="font-size:18px" class="rpt_table">
        		<thead bgcolor="#dddddd" align="center">
        			<tr bgcolor="#CCCCFF">
        				<th colspan="11" align="center"><strong>Dyes And Chemical Issue Requisition Without Rate</strong>
        				</th>
        			</tr>

        			<?

        			$sql_rec = "select a.id,b.mst_id,a.item_group_id, b.total_liquor,b.liquor_ratio,b.sub_process_id as sub_process_id,b.prod_id,b.dose_base,b.adj_type,b.comments,b.process_remark from pro_recipe_entry_dtls b,product_details_master a where a.id=b.prod_id and b.mst_id in($recipe_id) and a.item_category_id in(5,6,7,23) and b.status_active=1 and b.is_deleted=0";
        			$nameArray = sql_select($sql_rec);
        			$process_array = array();$subprocess_chk_arr=array();
        			$process_array_remark = array();
					$m=0;
        			foreach ($nameArray as $row) {
						$subprocess_data=$row[csf("sub_process_id")].$row[csf("mst_id")];
						if (!in_array($subprocess_data,$subprocess_chk_arr))
						{ $m++;
							 $subprocess_chk_arr[]=$subprocess_data;
							 $total_liquor=$row[csf("total_liquor")];
							 $liquor_ratio=$row[csf("liquor_ratio")];
						}
						else
						{
							 $total_liquor=0;
							 $liquor_ratio=0;
						}
						$process_array_liquor[$row[csf("sub_process_id")]]['total_liquor']+= $total_liquor;
        				$process_array_liquor[$row[csf("sub_process_id")]]['liquor_ratio']+= $liquor_ratio;
        				//$process_array[$row[csf("sub_process_id")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"] = $row[csf("comments")];

        			}
        			$sql_rec_remark = "select b.mst_id,b.sub_process_id as sub_process_id,b.comments,b.process_remark,b.total_liquor,b.liquor_ratio from pro_recipe_entry_dtls b  where  b.mst_id in($recipe_id) and b.sub_process_id in($subprocessforwashin) and b.status_active=1 and b.is_deleted=0";
        			$nameArray_re = sql_select($sql_rec_remark);
        			foreach ($nameArray_re as $row) {

						$subprocess_data=$row[csf("sub_process_id")].$row[csf("mst_id")];
						if (!in_array($subprocess_data,$subprocess_chk_arr))
						{ $m++;
							 $subprocess_chk_arr[]=$subprocess_data;
							 $total_liquor=$row[csf("total_liquor")];
							 $liquor_ratio=$row[csf("liquor_ratio")];
						}
						else
						{
							 $total_liquor=0;
							 $liquor_ratio=0;
						}

        				$process_array_liquor[$row[csf("sub_process_id")]]['total_liquor']+=$total_liquor;
        				$process_array_liquor[$row[csf("sub_process_id")]]['liquor_ratio']+= $liquor_ratio;
        				$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"] = $row[csf("process_remark")];
        			}

        			$group_arr = return_library_array("select id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0", 'id', 'item_name');
        			if ($db_type == 0) {
        				$item_lot_arr = return_library_array("select b.prod_id,group_concat(b.item_lot) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot");
        			} else {
        				$item_lot_arr = return_library_array("select b.prod_id,listagg((cast(b.item_lot as varchar2(500))),',') within group (order by b.prod_id) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot");
        			}
				/*$sql_dtls = "select a.requ_no, a.batch_id, a.recipe_id, b.id,
		b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
	  	c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id
	    from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
	    where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id=$data[1] order by b.id, b.seq_no";*/
	    $sql_dtls = "(select a.requ_no, a.batch_id, a.recipe_id, b.id,
	    b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty,c.current_stock, b.req_qny_edit,
	    c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id,b.item_lot,b.comments
	    from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
	    where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and  b.ratio>0  and c.stock_value>0 and c.item_category_id in (5,6,7,23) and a.id=$data[1])
	    union
	    (
	    select a.requ_no, a.batch_id, a.recipe_id, b.id,
	    b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, null as current_stock, b.req_qny_edit,
	    null as item_description, null as  item_group_id,  null as  sub_group_name,  null as  item_size, null as  unit_of_measure,null as  avg_rate_per_unit,null as prod_id,b.item_lot,b.comments
	    from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b
	    where a.id=b.mst_id and b.sub_process in ($subprocessforwashin) and a.id=$data[1]
	    )
	    order by id";
				// echo $sql_dtls;//die;

	    $sql_result = sql_select($sql_dtls);

	    $sub_process_array = array();
	    $sub_process_tot_rec_array = array();
	    $sub_process_tot_req_array = array();
	    $sub_process_tot_value_array = array();

	    foreach ($sql_result as $row) {
	    	$sub_process_tot_rec_array[$row[csf("sub_process")]] += $row[csf("recipe_qnty")];
	    	$sub_process_tot_req_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
	    	$sub_process_tot_value_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")] * $row[csf("avg_rate_per_unit")];
	    }

				//var_dump($sub_process_tot_req_array);
	    $i = 1;
	    $k = 1;
	    $recipe_qnty_sum = 0;
	    $req_qny_edit_sum = 0;
	    $recipe_qnty = 0;
	    $req_qny_edit = 0;
	    $req_value_sum = 0;
	    $req_value_grand = 0;
	    $recipe_qnty_grand = 0;
	    $req_qny_edit_grand = 0;$total_cost=$grand_tot_ratio_sum=0;

	    foreach ($sql_result as $row)
	    {
	    	$current_stock = $row[csf('current_stock')];
			$current_stock_check=number_format($current_stock,7,'.','');
			if($current_stock_check>0)
			{
	    	if ($i % 2 == 0)
	    		$bgcolor = "#E9F3FF";
	    	else
	    		$bgcolor = "#FFFFFF";
	    	if (!in_array($row[csf("sub_process")], $sub_process_array))
	    	{
	    		$sub_process_array[] = $row[csf('sub_process')];
	    		if ($k != 1) {
	    			?>
	    			<tr>
	    				<td colspan="5" align="right"><strong>Total :</strong></td>
	    				<td align="center"><?php echo number_format($tot_ratio_sum, 6, '.', ''); ?></td>

	    				<td align="right"><?php echo number_format($req_qny_edit_sum, 6, '.', ''); ?></td>
	    				<td>&nbsp;</td>
	    				<td>&nbsp;</td>
	    				<td>&nbsp;</td>
	    				<td>&nbsp;</td>

	    			</tr>
	    			<? }
	    			$tot_ratio_sum = 0;
	    			$req_qny_edit_sum = 0;
	    			$req_value_sum = 0;
	    			$k++;

	    			//if ($row[csf("sub_process")] == 93 || $row[csf("sub_process")] == 94 || $row[csf("sub_process")] == 95 || $row[csf("sub_process")] == 96 || $row[csf("sub_process")] == 97 || $row[csf("sub_process")] == 98) {
					if(in_array($row[csf("sub_process")],$subprocessForWashArr))
					{
	    				$pro_remark = $process_array_remark[$row[csf("sub_process")]]["wash_comments"];
	    			} else {
	    				$pro_remark = "";
	    			}
	    			if ($pro_remark != '') $pro_remark = "," . $pro_remark; else $pro_remark = "";
	    			$total_liquor = 'Total liquor(ltr)' . ": " . number_format($process_array_liquor[$row[csf("sub_process")]]['total_liquor'],2,'.','');
	    			$liquor_ratio = 'Liquor Ratio' . ": " . number_format($process_array_liquor[$row[csf("sub_process")]]['liquor_ratio'],2,'.','');
					$liquor_ratio_process=$process_array_liquor[$row[csf("sub_process")]]['liquor_ratio'];
					//$leveling_water=", Levelling Water(Ltr) ".$tot_bathc_weight*($liquor_ratio_process-1.5);
	    			?>
	    			<tr bgcolor="#CCCCCC">
	    				<th colspan="11">
	    					<strong><? echo $dyeing_sub_process[$row[csf("sub_process")]]. ', ' .$liquor_ratio.', ' .$total_liquor . $pro_remark; ?></strong>
	    				</th>
	    			</tr>
	    			<tr>
	    				<th width="30">SL</th>
	    				<th width="80">Item Cat.</th>
	    				<th width="80">Lot</th>
	    				<th width="100">Item Description</th>
	    				<th width="100">Dose Base</th>
	    				<th width="70">Ratio</th>
	    				<th width="80">Iss. Qty.</th>
	    				<th width="60">KG</th>
	    				<th width="50">GM</th>
	    				<th width="50">MG</th>
	    				<th >comments</th>

	    			</tr>
	    		</thead>
	    		<?
	    	}

				$req_qny_edit = explode(".", (string)$row[csf("req_qny_edit")]);
				$iss_qnty_kg = $req_qny_edit[0];
				if ($iss_qnty_kg == "") $iss_qnty_kg = 0;
				$iss_qnty_gm = substr($req_qny_edit[1], 0, 3);//$rem[0]; // floor($mg/1000);
				$iss_qnty_mg = substr($req_qny_edit[1], 3, 3);//$rem[1];//round($mg-$iss_qnty_gm*1000);
				$iss_qnty_gm = str_pad($iss_qnty_gm, 3, "0", STR_PAD_RIGHT);
				$iss_qnty_mg = str_pad($iss_qnty_mg, 3, "0", STR_PAD_RIGHT);
				//$comment = $process_array[$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"]
				$total_cost+=$row[csf("avg_rate_per_unit")]*$row[csf("req_qny_edit")];
				?>
				<tbody>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td align="center"><? echo $i; ?></td>
                    	<td><b><? echo $item_category[$row[csf("item_category")]];  //echo $row[csf("sub_process")];
                    	?></b></td>
                    	<td><b><? echo $row[csf("item_lot")]; ?></b></td>
                    	<td><b><? echo $row[csf("item_description")] . ' ' . $row[csf("item_size")]; ?></b></td>

                    	<td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
                    	<td align="center"><? echo number_format($row[csf("ratio")], 6, '.', ''); ?></td>
                    	<td align="right" title="Avg Rate: <? echo $row[csf("avg_rate_per_unit")];?>" ><b><? echo number_format($row[csf("req_qny_edit")], 6, '.', ''); ?></b></td>
                    	<td align="right"><? echo $iss_qnty_kg; ?></td>
                    	<td align="right"><? echo $iss_qnty_gm; ?></td>
                    	<td align="right"><? echo $iss_qnty_mg; ?></td>
                    	<td align="left"><p style="max-width: 300px;word-wrap: break-word;"><? echo $row[csf('comments')]; ?></p></td>
                    </tr>
                </tbody>
                <? $i++;
                $recipe_qnty_sum += $row[csf('recipe_qnty')];
                $req_qny_edit_sum += $row[csf('req_qny_edit')];
                $req_value_sum += $req_value;
				$tot_ratio_sum += $row[csf("ratio")];
				$grand_tot_ratio_sum += $row[csf("ratio")];

                $recipe_qnty_grand += $row[csf('recipe_qnty')];
                $req_qny_edit_grand += $row[csf('req_qny_edit')];
                $req_value_grand += $req_value;
            }
        }
            foreach ($sub_process_tot_rec_array as $val_rec) {
            	$totval_rec = $val_rec;
            }
            foreach ($sub_process_tot_req_array as $val_req) {
            	$totval_req = $val_req;
            }
            foreach ($sub_process_tot_value_array as $req_value) {
            	$tot_req_value = $req_value;
            }

				//$recipe_qnty_grand +=$val_rec;
				//$req_qny_edit_grand +=$val_req;
				//$req_value_grand +=$req_value;
            ?>
            <tr>
            	<td colspan="4" align="right"></td>
            	<td align="right"><strong>Total :</strong></td>

            	<td align="center"><?php echo number_format($tot_ratio_sum, 6, '.', ''); ?></td>
            	<td align="right"><?php echo number_format($totval_req, 6, '.', ''); ?></td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
            </tr>
            <tr>
            	<td colspan="4" align="right"></td>
            	<td align="right"><strong> Grand Total :</strong></td>

            	<td align="right"><?php echo number_format($grand_tot_ratio_sum, 6, '.', ''); ?></td>
            	<td align="right"><?php echo number_format($req_qny_edit_grand, 6, '.', ''); ?></td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
            </tr>
			<tr>
            	<td colspan="4" align="right"></td>

            	<td align="right"><strong> Total Cost :</strong></td>
				<td align="right"><?php echo number_format($total_cost, 6, '.', ''); ?></td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
            </tr>

        </table>
        <br>
        <?
        echo signature_table(15, $data[0], "900px");
		$requ_no=$dataArray[0][csf('requ_no')];
        ?>
    </div>



	</div>
		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
			<script>
				function generateBarcode(valuess) {
				//alert(valuess);
						var value = valuess;//$("#barcodeValue").val();
						var btype = 'code39';//$("input[name=btype]:checked").val();
						var renderer = 'bmp';// $("input[name=renderer]:checked").val();
						var settings = {
							output: renderer,
							bgColor: '#FFFFFF',
							color: '#000000',
							barWidth: 1,
							barHeight: 70,
							moduleSize: 5,
							posX: 10,
							posY: 20,
							addQuietZone: 1
						};
						$("#barcode_img_id").html('11');
						value = {code: value, rect: false};
						$("#barcode_img_id").show().barcode(value, btype, settings);
					}
					generateBarcode('<? echo $requ_no; ?>');
				</script>
	<?
	exit();
}
if($action == "chemical_dyes_issue_requisition_without_rate_print_3") //For Group
{
	extract($_REQUEST);
	echo load_html_head_contents("Chemical Dyes Issue Requisition Print", "../../../", 1, 1, '', '', '');
	$data = explode('*', $data);
	//print_r ($data);

	$sql = "select a.id, a.requ_no, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id from dyes_chem_issue_requ_mst a where a.id='$data[1]' and a.company_id='$data[0]'";
	$dataArray = sql_select($sql);

	$recipe_id = $dataArray[0][csf('recipe_id')];
	//echo $recipe_id.'FF';
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	//$entry_form_arr = return_library_array("select id, entry_form from pro_batch_create_mst", "id", "entry_form");
	//$order_id_arr=return_library_array( "select id, booking_id from  pro_recipe_entry_mst", "id", "booking_id");
	//$order_no_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
	//$job_no_arr=return_library_array( "select id, job_no from wo_booking_mst", "id", "job_no");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");

	$batch_weight = 0;
	if ($db_type == 0) {
		$data_array = sql_select("select group_concat(buyer_id) as buyer_id,group_concat(id) as recipe_id, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor, group_concat(concat_ws(':',batch_ratio,liquor_ratio) order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, group_concat(case when entry_form<>60 then batch_id end) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
	} else {
		$data_array = sql_select("select listagg(buyer_id,',') within group (order by buyer_id) as buyer_id,listagg(id,',') within group (order by id) as recipe_id, listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor, listagg(batch_ratio || ':' || liquor_ratio,',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(case when entry_form<>60 then batch_id end,',') within group (order by batch_id) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
	}
	$batch_weight = $data_array[0][csf("batch_weight")];
	$batch_id = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id")])));

	$batch_id_rec_main = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id_rec_main")])));
	if ($batch_id_rec_main == "") $batch_id_rec_main = 0;

	if ($db_type == 0) {
		$batchdata_array = sql_select("select group_concat(batch_no) as batch_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(distinct color_id) as color_id from pro_batch_create_mst where id in($batch_id)");
	} else {
		$batchdata_array = sql_select("select listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, listagg(color_id ,',') within group (order by id) as color_id, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($batch_id)");
	}

  	$recipe_sql="select a.dyeing_re_process,a.working_company_id,a.company_id,b.booking_no,b.entry_form, b.batch_weight,b.extention_no,b.is_sales,c.po_id from pro_recipe_entry_mst a, pro_batch_create_mst b,pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.batch_id=c.mst_id and a.id in($recipe_id) group by a.working_company_id,a.company_id, b.batch_weight,b.is_sales ,b.booking_no,b.entry_form,c.po_id,b.extention_no,a.dyeing_re_process";
	$result_recipe=sql_select($recipe_sql);
	$po_break_down_id='';
	foreach($result_recipe as $row)
	{
		if($row[csf('is_sales')]==1)
		{
			$fso_mst_id.=$row[csf('po_id')].',';
		}
		else  $po_break_down_id.=$row[csf('po_id')].',';

		$owner_company_id=$row[csf('company_id')];
		$booking_no=$row[csf('booking_no')];
		$entry_form=$row[csf('entry_form')];
		$extention_no=$row[csf('extention_no')];
		$dyeing_re_process_name=$dyeing_re_process[$row[csf('dyeing_re_process')]];
	}
		//echo $po_break_down_id.'DFD';

	//$booking_no_cond=$booking_no;
	if($booking_no!='') $booking_no_cond="and b.booking_no in('$booking_no')";else $booking_no_cond="and b.booking_no in('0')";

	$without_booking_array = "select a.buyer_id,b.booking_no,b.booking_type from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and b.booking_type=1  and b.status_active=1 and b.is_deleted=0 $booking_no_cond";
	foreach($without_booking_array as $row)
	{
		if($row[csf('booking_type')]==4)// Sample Fabric
		{
		$main_fab_booking='Without Sample Booking';
		}
		$buyer_id_arr[$row[csf('booking_no')]]=$row[csf('buyer_id')];
	}
 	$booking_array = "select  a.buyer_id,a.entry_form,b.po_break_down_id,b.job_no,b.is_short,b.booking_no,b.booking_type from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no  and b.booking_type=1  and b.status_active=1 and b.is_deleted=0 $booking_no_cond group by a.entry_form,a.buyer_id,b.po_break_down_id,b.job_no,b.is_short,b.booking_no,b.booking_type";
	$result_booking=sql_select($booking_array);

	foreach($result_booking as $row)
	{
		if($row[csf('booking_type')]==1)// Fabric
		{
			if($row[csf('is_short')]==1)
			{
				$main_fab_booking='Short Booking';
			}
			else if($row[csf('is_short')]==2)
			{
				$main_fab_booking='Main Booking';
			}
			else if($row[csf('is_short')]==2 && $row[csf('entry_form')]==108 )
			{
				$main_fab_booking='Partial Booking';
			}
		}
		else if($row[csf('booking_type')]==4)// Sample Fabric
		{
			$main_fab_booking='Sample Booking';
		}
		$buyer_id_arr[$row[csf('booking_no')]]=$row[csf('buyer_id')];
		//$po_break_down_id.=$row[csf('po_break_down_id')].',';

	}
	$po_id=rtrim($po_break_down_id,',');
	$book_buyer_name=$buyer_library[$buyer_id_arr[$booking_no]];

	$style_ref_no = '';
	if($entry_form==36)
	{
		$po_data = sql_select("select b.order_no, b.cust_style_ref, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and b.id in(" . $po_id . ") group by b.order_no, b.cust_style_ref, c.subcon_job, c.party_id");
	}
	else
	{
		$po_data = sql_select("select  c.job_no,c.style_ref_no, c.buyer_name from  wo_po_break_down b, wo_po_details_master c where  b.job_no_mst=c.job_no and b.status_active=1 and b.is_deleted=0 and b.id in(" . $po_id . ") group by  c.job_no,c.style_ref_no, c.buyer_name");

		if( empty($po_id))
		{

			$sql_style=sql_select("select style_ref_no,buyer_name from sample_development_mst where id in (select style_id from wo_non_ord_samp_booking_dtls where status_active=1 and booking_no ='$booking_no' group by style_id)");
			foreach ($sql_style as $row) {
				$style_ref_no.=$row[csf('style_ref_no')].',';
				$book_buyer_name.=$buyer_library[$row[csf('buyer_name')]].",";
			}
			$style_ref_no=chop($style_ref_no,",");
			$book_buyer_name=chop($book_buyer_name,",");
		}
		//echo "select  c.job_no,c.style_ref_no, c.buyer_name from  wo_po_break_down b, wo_po_details_master c where  b.job_no_mst=c.job_no and b.status_active=1 and b.is_deleted=0 and b.id in(" . $po_id . ") group by  c.job_no,c.style_ref_no, c.buyer_name";
	}
	foreach($po_data as $row)
	{
		if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
		//if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
	}

	if ($db_type == 0) {
		$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
	} else if ($db_type == 2) {
		$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, listagg((cast(a.yarn_lot as varchar2(4000))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(a.brand_id,',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(4000))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id";
	}

	$yarn_dtls_array = array();
	$result_sql_yarn_dtls = sql_select($sql_yarn_dtls);
	foreach ($result_sql_yarn_dtls as $row) {
		$yarn_lot = implode(", ", array_unique(explode(",", $row[csf('yarn_lot')])));
		$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
		$brand_name = "";
		foreach ($brand_id as $val) {
			if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= ", " . $brand_arr[$val];
		}

		$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
		$count_name = "";
		foreach ($yarn_count as $val) {
			if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
		}
		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['yarn_lot'] = $yarn_lot;
		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['brand_name'] = $brand_name;
		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['count_name'] = $count_name;
	}
	//var_dump($yarn_dtls_array);
	//$po_no = implode(", ", array_unique(explode(",", substr($po_no, 0, -1))));
	//$job_no = implode(", ", array_unique(explode(",", substr($job_no, 0, -1))));
	//$buyer_name = implode(",", array_unique(explode(",", substr($buyer_name, 0, -1))));





	$color_id = array_unique(explode(",", $batchdata_array[0][csf('color_id')]));
	$color_name = '';
	foreach ($color_id as $color) {
		$color_name .= $color_arr[$color] . ",";
	}
	$color_name = substr($color_name, 0, -1);
	//var_dump($recipe_color_arr);
	$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
	$group_id=$nameArray[0][csf('group_id')];
	$recipe_id=$data_array[0][csf("recipe_id")];

	$k=0;
	$copy_no=array(1,2); //for Dynamic Copy here
	foreach($copy_no as $cid)
	{
		?>
		<div data-print=section class="print6_data">
			<div style="width:1020px;">
				<table width="1020" cellspacing="0" align="center" id="table_<? echo $cid;?>">
					<tr>
						<td width="500" align="center" style="font-size:xx-large;">
							<strong><? echo $company_library[$data[0]];//$company_library[$data[0]]; ?></strong></td>
						<td width="400" id="barcode_img_id_<? echo $cid;?>" class="barcode_img">&nbsp;</td>
						<td width="130" id="number_of_copy">
							<?
								if($cid==1){
									echo "<h3>Store Copy</h3>";
								}
								else if($cid==2){
									echo "<h3>Dyeing Copy</h3>";
								}
							?>
						</td>
					</tr>
					<tr class="form_caption">
						<td colspan="6" align="center" style="font-size:14px">
							<?
							/*
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
							}*/
							?>
						</td>
						<td></td>
					</tr>
					<tr>
						<td colspan="6" align="center" style="font-size: large">
							<strong><u><? echo $data[2]; ?>	Report</u></strong>
						</td>
						<td></td>
					</tr>
				</table>
				<table width="950" cellspacing="0" align="center">
					<tr>
						<td width="90"><strong>Req. ID </strong></td>
						<td width="160px"><? echo $dataArray[0][csf('requ_no')]; ?></td>
						<td width="100"><strong>Req. Date</strong></td>
						<td width="160px"><? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
						<td width="90"><strong>Buyer</strong></td>
						<td width="160px"><? echo $book_buyer_name; ?></td>
					</tr>
					<tr>
						<td><strong>Batch No</strong></td>
						<td><? echo $batchdata_array[0][csf('batch_no')]; ?></td>
						<td><strong>Booking Type</strong></td>
						<td><? echo $main_fab_booking; ?></td>
						<td><strong>Booking No</strong></td>
						<td><? echo $booking_no; ?></td>

					</tr>
					<tr>
						<td><strong>Color</strong></td>
						<td><? echo $color_name; ?></td>
						<td><strong>Batch Weight</strong></td>
						<td><? $tot_bathc_weight=$batch_weight + $batchdata_array[0][csf('batch_weight')];echo number_format($tot_bathc_weight,2,'.',''); ?></td>
						<td><strong>Exten. No</strong></td>
						<td><? echo $extention_no; ?></td>
					</tr>
					<tr>
						<td><strong>Issue Basis</strong></td>
						<td><? echo $receive_basis_arr[$dataArray[0][csf('requisition_basis')]]; ?></td>
						<td><strong>Recipe No</strong></td>
						<td><? echo $data_array[0][csf("recipe_id")]; ?></td>
						<td><strong>Machine No</strong></td>
						<td>
							<?
							$machine_data = sql_select("select machine_no, floor_id from lib_machine_name where id='" . $dataArray[0][csf("machine_id")] . "'");
							echo $machine_data[0][csf('machine_no')];
							?>
						</td>
					</tr>
					<tr>
						<td   width="120"><strong>Dyeing Reprocess</strong></td>
						<td><? echo $dyeing_re_process_name; ?></td>
						<td><strong>Method</strong></td>
						<td><? echo $dyeing_method[$dataArray[0][csf("method")]];; ?></td>
						<td><strong>Style Ref.</strong></td>
						<td>
							<?

							echo implode(",", array_unique(explode(",", $style_ref_no)));
							?>
						</td>
					</tr>
					<tr>
						<td><strong>Floor Name</strong></td>
						<td>
							<?
								$floor_name = return_field_value("floor_name", "lib_prod_floor", "id='" . $machine_data[0][csf('floor_id')] . "'");
								echo $floor_name;
							?>
						</td>
					</tr>

				</table>
				<br>
				<?
				$batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
				$j = 1;
				$entryForm = $entry_form_arr[$batch_id_qry[0]];

				$sql_rec = "select a.id,a.item_group_id,b.mst_id, b.total_liquor,b.liquor_ratio,b.sub_process_id as sub_process_id,b.prod_id,b.dose_base,b.adj_type,b.comments,b.process_remark from pro_recipe_entry_dtls b,product_details_master a where a.id=b.prod_id and b.mst_id in($recipe_id) and a.item_category_id in(5,6,7,23) and b.status_active=1 and b.is_deleted=0";
				$nameArray = sql_select($sql_rec);
				$process_array = array();
				$process_array_remark = array();
				foreach ($nameArray as $row)
				{

					if($process_array_liquor_check[$row[csf("mst_id")]][$row[csf("sub_process_id")]]=="")
					{
						$process_array_liquor[$row[csf("sub_process_id")]]['total_liquor'] += $row[csf("total_liquor")];
						$process_array_liquor_check[$row[csf("mst_id")]][$row[csf("sub_process_id")]]=888;
					}
					$process_array_liquor[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
					//$process_array_liquor[$row[csf("sub_process_id")]]['comments'] = $row[csf("comments")];
					$process_array[$row[csf("sub_process_id")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"] = $row[csf("comments")];
				}
				$sql_rec_remark = "select b.mst_id,b.sub_process_id as sub_process_id,b.comments,b.process_remark,b.total_liquor,b.liquor_ratio from pro_recipe_entry_dtls b  where  b.mst_id in($recipe_id) and b.sub_process_id in($subprocessforwashin) and b.status_active=1 and b.is_deleted=0";
				$nameArray_re = sql_select($sql_rec_remark);
				foreach ($nameArray_re as $row)
				{
					if($process_array_liquor_check[$row[csf("mst_id")]][$row[csf("sub_process_id")]]=="")
					{
						$process_array_liquor[$row[csf("sub_process_id")]]['total_liquor'] += $row[csf("total_liquor")];
						$process_array_liquor_check[$row[csf("mst_id")]][$row[csf("sub_process_id")]]=999;
					}
					$process_array_liquor[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
					$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"] = $row[csf("process_remark")];
					//$process_array_remark[$row[csf("sub_process_id")]]["comments"] = $row[csf("comments")];
				}

				$group_arr = return_library_array("select id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0", 'id', 'item_name');
				if ($db_type == 0) {
					$item_lot_arr = return_library_array("select b.prod_id,group_concat(b.item_lot) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot");
				} else {
					// $item_lot_arr = return_library_array("select b.prod_id,listagg((cast(b.item_lot as varchar2(500))),',') within group (order by b.prod_id) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot");
					// $sql_lot=sql_select("select b.prod_id, b.item_lot as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) ");
					foreach ($sql_lot as $row)
					{
						$item_lot_arr[$row[csf("prod_id")]]=$row[csf("yean_lot")];
					}
				}
				/*$sql_dtls = "select a.requ_no, a.batch_id, a.recipe_id, b.id,b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit, c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id
				from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
				where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id=$data[1] order by b.id, b.seq_no";*/
				if($subprocessforwashin=="") $subprocessforwashin=0;
				$sql_dtls = "(select a.requ_no, a.batch_id, a.recipe_id, b.id, b.sub_seq, b.sub_process as sub_process, b.seq_no, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty,c.current_stock, b.req_qny_edit, c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id
				from dyes_chem_issue_requ_mst a, dyes_chem_issue_requ_dtls b, product_details_master c
				where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and  b.ratio>0  and c.stock_value>0 and c.item_category_id in (5,6,7,23) and a.id=$data[1])
				union
				(select a.requ_no, a.batch_id, a.recipe_id, b.id,b.sub_seq,b.sub_process as sub_process,b.seq_no, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, null as current_stock, b.req_qny_edit, null as item_description, null as  item_group_id,  null as  sub_group_name,null as  item_size, null as  unit_of_measure,null as  avg_rate_per_unit,null as prod_id
				from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b
				where a.id=b.mst_id and b.sub_process in ($subprocessforwashin) and a.id=$data[1])
				order by id";
				//echo $sql_dtls;//die;

				$sql_result = sql_select($sql_dtls);

				$sub_process_array = array();
				$sub_process_tot_rec_array = array();
				$sub_process_tot_req_array = array();
				$sub_process_tot_value_array = array();
				$summery_data = array();
				foreach ($sql_result as $row)
				{
					$sub_process_tot_rec_array[$row[csf("sub_process")]] += $row[csf("recipe_qnty")];
					$sub_process_tot_req_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
					$sub_process_tot_value_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")] * $row[csf("avg_rate_per_unit")];

					if($row[csf("item_category")]==7) $row[csf("item_category")]=5;
					$summery_data[$row[csf("item_category")]]["ratio"]+=$row[csf("ratio")];
					$summery_data[$row[csf("item_category")]]["issue_qnty"]+=$row[csf("req_qny_edit")];
					$iss_value = $row[csf("req_qny_edit")]*$row[csf("avg_rate_per_unit")];
					$summery_data[$row[csf("item_category")]]["issue_value"]+=$iss_value;
				}
				?>

				<table width="920" cellspacing="0" align="left" class="rpt_table" border="1" style="font-size:16px" rules="all">
                    <thead bgcolor="#dddddd" align="center">
                        <tr bgcolor="#CCCCFF">
                            <th colspan="6" align="center"><strong>Fabrication</strong></th>
                        </tr>
                        <tr>
                            <th width="50">SL</th>
                            <th width="150">Dia/ W. Type</th>
                            <th width="450">Constrution & Composition</th>
                            <th width="80">Gsm</th>
                            <th width="90">Dia</th>
                            <th>Qty(kg)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?
                        foreach ($batch_id_qry as $b_id)
                        {
                            /*$batch_query = "SELECT  po_id, prod_id, item_description, width_dia_type, count(roll_no) as gmts_qty, sum(batch_qnty) batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by po_id, prod_id, item_description, width_dia_type";*/
                            $batch_query = "SELECT a.entry_form, b.po_id, b.prod_id, b.item_description, b.width_dia_type, count(b.roll_no) as gmts_qty, sum(b.batch_qnty) batch_qnty, b.gsm,b.grey_dia
                            from pro_batch_create_mst a, pro_batch_create_dtls b
                            where a.id=b.mst_id and b.mst_id in($b_id) and b.status_active=1 and b.is_deleted=0
                            group by a.entry_form, b.po_id, b.prod_id, b.item_description, b.width_dia_type, b.gsm,b.grey_dia";
                            // echo $batch_query;
                            $result_batch_query = sql_select($batch_query);
                            foreach ($result_batch_query as $rows)
                            {
                                if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                                $fabrication_full = $rows[csf("item_description")];
                                $fabrication = explode(',', $fabrication_full);
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>">
                                    <td align="center"><? echo $j; ?></td>
                                    <td align="center"><b><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></b></td>
                                    <td align="left"><b><? echo $fabrication[0] . ", " . $fabrication[1]; ?></b></td>
                                    <?
                                    if ($entry_form_arr[$b_id] == 36)
                                    {
                                    ?>
                                    <td align="center"><? echo $rows[csf("gsm")]; ?></td>
                                    <td align="center"><? echo $rows[csf("grey_dia")]; ?></td>
                                    <?} else {?>

                                    <td align="center"><? echo $fabrication[2]; ?></td>
                                    <td align="center"><? echo $fabrication[3]; ?></td>
                                    <?}?>
                                    <td align="center"><? echo $rows[csf("batch_qnty")]; ?></td>
                                </tr>
                                <?
                                $j++;
                            }
                        }
                        ?>
                    </tbody>
                </table>
				<div style="width:920px; margin-top:10px">
					<table align="left" cellspacing="0" width="920" border="1" rules="all" style="font-size:16px; margin-top:25px;" class="rpt_table">
						<thead bgcolor="#dddddd" align="center">
							<tr bgcolor="#CCCCFF">
								<th colspan="13" align="center"><strong>Dyes And Chemical Issue Requisition</strong>
								</th>
							</tr>
							<?
								$i = 1;$k = 1;
								$recipe_qnty_sum = 0;$req_qny_edit_sum = 0;$recipe_qnty = 0;$req_qny_edit = 0;$req_value_sum = 0;$req_value_grand = 0;$recipe_qnty_grand = 0;$req_qny_edit_grand = 0;$total_cost=$grand_tot_ratio_sum=0;

								foreach ($sql_result as $row)
								{
									$current_stock = $row[csf('current_stock')];
									$current_stock_check=number_format($current_stock,7,'.','');

									if($current_stock_check>0)
									{
										if ($i % 2 == 0)
											$bgcolor = "#E9F3FF";
										else
											$bgcolor = "#FFFFFF";
										if (!in_array($row[csf("sub_process")], $sub_process_array))
										{
											$sub_process_array[] = $row[csf('sub_process')];
											if ($k != 1)
											{
												?>
												<tr bgcolor="#ffd4c4">
													<td colspan="4" align="right"><strong>Total :</strong></td>
													<td align="center"><?php echo number_format($tot_ratio_sum, 4, '.', ''); $total_issue_val+=$issue_val?></td>
													<td>&nbsp;</td>

													<td align="right"><?php echo number_format($req_qny_edit_sum, 4, '.', ''); ?></td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td align="right"><?php echo number_format($tat_issue_value, 2, '.', ''); ?></td>
													<td>&nbsp;</td>
												</tr>
                                                <tr bgcolor="#e8edd3">
													<td colspan="11" align="right"><strong>Cost per KG:</strong></td>
													<td align="right"><?php echo number_format($tat_issue_value/$tot_bathc_weight, 2, '.', ''); ?></td>
													<td>&nbsp;</td>
												</tr>
												<?
												$tat_issue_value=0;
											}
											$tot_ratio_sum = 0;
											$req_qny_edit_sum = 0;
											$req_value_sum = 0;
											$k++;

											//if ($row[csf("sub_process")] == 93 || $row[csf("sub_process")] == 94 || $row[csf("sub_process")] == 95 || $row[csf("sub_process")] == 96 || $row[csf("sub_process")] == 97 || $row[csf("sub_process")] == 98) {
											if(in_array($row[csf("sub_process")],$subprocessForWashArr))
											{
													$pro_remark = $process_array_remark[$row[csf("sub_process")]]["wash_comments"];
											}
											else {
												$pro_remark = "";
											}
											if ($pro_remark != '') $pro_remark = "," . $pro_remark; else $pro_remark = "";
											$total_liquor = 'Total liquor(ltr)' . ": " . number_format($process_array_liquor[$row[csf("sub_process")]]['total_liquor'],2,'.','');
											$liquor_ratio = 'Liquor Ratio' . ": " . $process_array_liquor[$row[csf("sub_process")]]['liquor_ratio'];
											$liquor_ratio_process=$process_array_liquor[$row[csf("sub_process")]]['liquor_ratio'];
											$leveling_water=", Levelling Water(Ltr) ".$tot_bathc_weight*($liquor_ratio_process-1.5);
											?>
											<tr bgcolor="#CCCCCC">
												<th colspan="13">
													<strong><? echo $dyeing_sub_process[$row[csf("sub_process")]]. ', ' .$liquor_ratio.', ' .$total_liquor . $pro_remark . $leveling_water; ?></strong>
												</th>
											</tr>
											<tr>
												<th width="30">SL</th>

												<th width="80">Item Group</th>
												<th width="100">Item Description</th>
												<th width="100">Dose Base</th>
												<th width="70">Ratio</th>
												<th width="80">Adj-1</th>
												<th width="80">Iss. Qty.</th>
												<th width="60">KG</th>
												<th width="50">GM</th>
												<th width="50">MG</th>
												<th width="50">AVG Rate</th>
												<th width="50">Issue Value</th>
												<th width="100">Comments</th>
											</tr>
											</thead>
											<tbody>
											<?
										}

										$req_qny_edit = explode(".", (string)$row[csf("req_qny_edit")]);
										$iss_qnty_kg = $req_qny_edit[0];
										if ($iss_qnty_kg == "") $iss_qnty_kg = 0;
										$iss_qnty_gm = substr($req_qny_edit[1], 0, 3);//$rem[0]; // floor($mg/1000);
										$iss_qnty_mg = substr($req_qny_edit[1], 3, 3);//$rem[1];//round($mg-$iss_qnty_gm*1000);
										$iss_qnty_gm = str_pad($iss_qnty_gm, 3, "0", STR_PAD_RIGHT);
										$iss_qnty_mg = str_pad($iss_qnty_mg, 3, "0", STR_PAD_RIGHT);
										$comment = $process_array[$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"];
										$iss_value = $row[csf("req_qny_edit")]*$row[csf("avg_rate_per_unit")];
										$tat_issue_value +=$iss_value;
										$avg_rate = $iss_value/$row[csf("req_qny_edit")];
										$total_cost+=$row[csf("avg_rate_per_unit")]*$row[csf("req_qny_edit")];
										?>

										<tr bgcolor="<? echo $bgcolor; ?>">
											<td align="center"><? echo $i; ?></td>
												<!--<td><b><? //echo $item_category[$row[csf("item_category")]];  //echo $row[csf("sub_process")];
											?></b></td>-->
											<td><b><? echo $group_arr[$row[csf("item_group_id")]]; ?></b></td>
											<td><b><? echo $row[csf("item_description")] . ' ' . $row[csf("item_size")]; ?></b></td>

											<td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
											<td align="center"><? echo number_format($row[csf("ratio")], 6, '.', ''); ?></td>
											<td align="center"><? echo $row[csf("adjust_percent")]; ?></td>
											<td align="right" title="Avg Rate: <? echo $row[csf("avg_rate_per_unit")];?>" ><b><? echo number_format($row[csf("req_qny_edit")], 4, '.', ''); ?></b></td>
											<td align="right"><? echo $iss_qnty_kg; ?></td>
											<td align="right"><? echo $iss_qnty_gm; ?></td>
											<td align="right"><? echo $iss_qnty_mg; ?></td>
											<td align="right"><? echo number_format(($avg_rate>0) ? $avg_rate : "0",2); ?></td>
											<td align="right"><? echo number_format($iss_value,2); $total_issue_value+=$iss_value;?></td>
											<td align="center"><? echo $comment; ?></td>
										</tr>

										<?
										$i++;
										$recipe_qnty_sum += $row[csf('recipe_qnty')];
										$req_qny_edit_sum += $row[csf('req_qny_edit')];
										$req_value_sum += $req_value;
										$tot_ratio_sum += $row[csf("ratio")];
										$grand_tot_ratio_sum += $row[csf("ratio")];

										$recipe_qnty_grand += $row[csf('recipe_qnty')];
										$req_qny_edit_grand += $row[csf('req_qny_edit')];
										$req_value_grand += $req_value;
										$cost_per_kg = $total_cost/$tot_bathc_weight;
									}
								}


								foreach ($sub_process_tot_rec_array as $val_rec)
								{
									$totval_rec = $val_rec;
								}
								foreach ($sub_process_tot_req_array as $val_req)
								{
									$totval_req = $val_req;
								}
								foreach ($sub_process_tot_value_array as $req_value) {
									$tot_req_value = $req_value;
								}

									//$recipe_qnty_grand +=$val_rec;
									//$req_qny_edit_grand +=$val_req;
									//$req_value_grand +=$req_value;
							?>
						</tbody>
						<!-- <tfoot> -->
							<tr bgcolor="#fffcc7">
								<td colspan="4" align="right"><strong>Total :</strong></td>
								<td align="center"><?php echo number_format($tot_ratio_sum, 4, '.', ''); ?></td>
								<td align="center"><?php //echo number_format($tot_ratio_sum, 6, '.', ''); ?></td>
								<td align="right"><?php echo number_format($totval_req, 4, '.', ''); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right"><?php echo number_format($tat_issue_value, 4, '.', ''); $grand_total_iss_value +=$total_issue_value; ?></td>
								<td>&nbsp;</td>
							</tr>
                            <tr bgcolor="#e8edd3">
                                <td colspan="11" align="right"><strong>Cost per KG:</strong></td>
                                <td align="right"><?php echo number_format($tat_issue_value/$tot_bathc_weight, 2, '.', ''); ?></td>
                                <td>&nbsp;</td>
                            </tr>
							<tr bgcolor="#f5ffc7">
								<td colspan="4" align="right"><strong> Grand Total :</strong></td>
								<td align="right"><?php echo number_format($grand_tot_ratio_sum, 4, '.', ''); ?></td>
								<td align="center"><?php //echo number_format($tot_ratio_sum, 6, '.', ''); ?></td>
								<td align="right"><?php echo number_format($req_qny_edit_grand, 4, '.', ''); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right"><?php echo number_format($grand_total_iss_value, 4, '.', ''); ?></td>
								<td>&nbsp;</td>
							</tr>
							<tr bgcolor="#e8edd3">
								<td colspan="4"  align="right"><strong> Total Cost :</strong></td>
								<td align="right"><?php echo number_format($total_cost, 4, '.', ''); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<tr bgcolor="#def1b1">
								<td colspan="11"  align="right"><strong> Cost per KG:</strong></td>
								<td align="right"><?php echo number_format($cost_per_kg, 2, '.', ''); ?></td>
								<td>&nbsp;</td>
							</tr>
					</table>
                    <table width="920" cellspacing="0" border="0">
                        <tr>
                            <td width="720">
                                <table width="720" cellspacing="0" align="left" class="rpt_table" border="1" style="font-size:16px" rules="all">
                                    <thead bgcolor="#dddddd" align="center">
                                        <tr bgcolor="#CCCCFF">
                                            <th colspan="5" align="center"><strong>Dyes & Chemical Summary</strong></th>
                                        </tr>
                                        <tr>
                                            <th width="200">Description</th>
                                            <th width="100">Ratio/Shade %</th>
                                            <th width="100">Issue Qty.</th>
                                            <th width="100">Issue Value</th>
                                            <th>Per KG Cost</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?
                                        $gt_ratio=$gt_issue_qnty=$gt_issue_value=$gt_pre_kg_cost=0;
                                        foreach ($summery_data as $item_cat=>$val)
                                        {
                                            if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                                            $pre_kg_cost=$val["issue_value"]/$tot_bathc_weight;
                                            ?>
                                            <tr bgcolor="<? echo $bgcolor; ?>">
                                                <td align="center"><? echo $item_category[$item_cat]." Cost"; ?></td>
                                                <td align="right"><? echo number_format($val["ratio"],2); ?></td>
                                                <td align="right"><? echo number_format($val["issue_qnty"],2); ?></td>
                                                <td align="right"><? echo number_format($val["issue_value"],2); ?></td>
                                                <td align="right"><? echo number_format($pre_kg_cost,2); ?></td>
                                            </tr>
                                            <?
                                            $j++;
                                            $gt_ratio+=$val["ratio"];
                                            $gt_issue_qnty+=$val["issue_qnty"];
                                            $gt_issue_value+=$val["issue_value"];
                                            $gt_pre_kg_cost+=$pre_kg_cost;
                                        }
                                        ?>
                                        <tr bgcolor="#CCCCCC">
                                            <td>Grand Total:</td>
                                            <td align="right"><? echo number_format($gt_ratio,2); ?></td>
                                            <td align="right"><? echo number_format($gt_issue_qnty,2); ?></td>
                                            <td align="right"><? echo number_format($gt_issue_value,2); ?></td>
                                            <td align="right"><? echo number_format($gt_pre_kg_cost,2); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td valign="top">
                                <table width="200" border="1" align="left">
                                    <tr>
                                        <td style="font-size:14px; font-weight:bold">Shade Category </td>
                                    </tr>
                                    <tr>
                                        <td>
                                        <?
                                        if($summery_data[6]["ratio"]>0)
                                        {
                                            if($summery_data[6]["ratio"]<0.51) echo "Light";
                                            else if($summery_data[6]["ratio"]>0.5 && $summery_data[6]["ratio"]<1.5) echo "Medium";
                                            else if($summery_data[6]["ratio"]>1.51 && $summery_data[6]["ratio"]<4) echo "Dark";
                                            else  echo "Extra Dark";
                                        }

                                        ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

					<div class="signature_table_container1">
					<?
						echo signature_table(15, $data[0], "1000px");
						$requ_no=$dataArray[0][csf('requ_no')];
					?>
					</div>
				</div>
			</div>
		</div>
		<?
		 $k++;
		 $grand_total_iss_value=0;$tat_issue_value=0;$total_issue_value=0;
	}
	?>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			//alert(valuess);
			var value = valuess;//$("#barcodeValue").val();
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer = 'bmp';// $("input[name=renderer]:checked").val();
			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 70,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			var barcode_numbers = $("[class*='barcode_img']").length;
			//alert(barcode_numbers);
				//$("#barcode_img_id_"+i).html('11');
			//for(var i=1; i<=barcode_numbers; i++){
				value = {code: value, rect: false};
				$("#barcode_img_id_1").show().barcode(value, btype, settings);
				$("#barcode_img_id_2").show().barcode(value, btype, settings);
			//}

		}
			generateBarcode('<? echo $requ_no; ?>');
	</script>

	<script type="text/javascript">
		var staticHeight = 0;
		var pageHeight = 900;
		$('.print6_data').each(function() {
		    staticHeight += $(this).filter(':visible').outerHeight(true);
		    //console.log(staticHeight)
		    if (staticHeight > pageHeight) {//<p style="page-break-after:always;"></p>
		        $(this).after( '<p style="page-break-after:always;"></p>');
		        staticHeight = 0;
				$('.print6_data').addClass("devider").css({
					'margin-top':"80px",
					'margin-left':"50px"
				});
		    }
		    else{
		    	/*$(this).after( '<hr class="hr" style="border-top: dotted 2px #000">');*/
		    	$(this).after( '<p style="page-break-after:always;"></p>');
		    	$(this).css({
					/*'width':"950px",*/
					'margin-left':"50px",
					'margin-top': "50px"
				});
		    	$('.print6_data').addClass("devider");
		    }

		});
		$(".signature_table_container").css({
			'position' : "relative",
			'top' : "25px",
			'height' : "190px",
			'overflow' : "hidden",
			'width' : "100%",
			'clear' : "both",
			'vertical-align' : "baseline"
		});
	</script>
	<style type="text/css">
		/* .devider:nth-of-type(2){
			margin-top: 160px;
			margin-left: 50px;
		} */
		@media print{

			.devider{
				margin-top:3.2333cm;
			}
		}
	</style>
	<?
	exit();
}
if ($action == "chemical_dyes_issue_requisition_without_rate_print_4") //For Group
{
	extract($_REQUEST);
	echo load_html_head_contents("Chemical Dyes Issue Requisition Print", "../../../", 1, 1, '', '', '');
	$data = explode('*', $data);
	//print_r ($data);

	$sql = "select a.id, a.job_no, a.requ_no, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id from dyes_chem_issue_requ_mst a where a.id='$data[1]' and a.company_id='$data[0]'";

	//echo $sql; die;
	$dataArray = sql_select($sql);

	$recipe_id = $dataArray[0][csf('recipe_id')];
	//echo $recipe_id.'FF';
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
//	$entry_form_arr = return_library_array("select id, entry_form from pro_batch_create_mst", "id", "entry_form");
	//$order_id_arr=return_library_array( "select id, booking_id from  pro_recipe_entry_mst", "id", "booking_id");
	//$order_no_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
	//$job_no_arr=return_library_array( "select id, job_no from wo_booking_mst", "id", "job_no");

	$season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");

	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");

	$batch_weight = 0;
	if ($db_type == 0) {
		$data_array = sql_select("select group_concat(buyer_id) as buyer_id,group_concat(id) as recipe_id,group_concat(color_range) as color_range, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor, group_concat(concat_ws(':',batch_ratio,liquor_ratio) order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, group_concat(case when entry_form<>60 then batch_id end) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
	} else {
		$data_array = sql_select("select listagg(buyer_id,',') within group (order by buyer_id) as buyer_id,listagg(id,',') within group (order by id) as recipe_id,listagg(color_range,',') within group (order by id) as color_range, listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor, listagg(batch_ratio || ':' || liquor_ratio,',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(case when entry_form<>60 then batch_id end,',') within group (order by batch_id) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
	}
	$batch_weight = $data_array[0][csf("batch_weight")];
	$batch_id = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id")])));

	$batch_id_rec_main = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id_rec_main")])));
	if ($batch_id_rec_main == "") $batch_id_rec_main = 0;

	if ($db_type == 0) {
		$batchdata_array = sql_select("select group_concat(batch_no) as batch_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(distinct color_id) as color_id from pro_batch_create_mst where id in($batch_id)");
	} else {
		$batchdata_array = sql_select("select listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, listagg(color_id ,',') within group (order by id) as color_id, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($batch_id)");
	}

  	$recipe_sql="select a.dyeing_re_process,a.working_company_id,a.company_id,b.booking_no,b.entry_form, b.batch_weight,b.extention_no,b.is_sales,c.po_id from pro_recipe_entry_mst a, pro_batch_create_mst b,pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.batch_id=c.mst_id and a.id in($recipe_id) group by a.working_company_id,a.company_id, b.batch_weight,b.is_sales ,b.booking_no,b.entry_form,c.po_id,b.extention_no,a.dyeing_re_process";
	$result_recipe=sql_select($recipe_sql);
	$po_break_down_id='';
	foreach($result_recipe as $row)
	{
		if($row[csf('is_sales')]==1)
		{
			$fso_mst_id.=$row[csf('po_id')].',';
		}
		else  $po_break_down_id.=$row[csf('po_id')].',';

		$owner_company_id=$row[csf('company_id')];
		$booking_no=$row[csf('booking_no')];
		$entry_form=$row[csf('entry_form')];
		$extention_no=$row[csf('extention_no')];
		$dyeing_re_process_name=$dyeing_re_process[$row[csf('dyeing_re_process')]];
	}
		//echo $po_break_down_id.'DFD';

	//$booking_no_cond=$booking_no;
	if($booking_no!='') $booking_no_cond="and b.booking_no in('$booking_no')";else $booking_no_cond="and b.booking_no in('0')";

	$without_booking_array = "select a.buyer_id,b.booking_no,b.booking_type from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and b.booking_type=1  and b.status_active=1 and b.is_deleted=0 $booking_no_cond";
	foreach($without_booking_array as $row)
	{
		if($row[csf('booking_type')]==4)// Sample Fabric
		{
		$main_fab_booking='Without Sample Booking';
		}
		$buyer_id_arr[$row[csf('booking_no')]]=$row[csf('buyer_id')];
	}
 	$booking_array = "select  a.buyer_id,a.entry_form,b.po_break_down_id,b.job_no,b.is_short,b.booking_no,b.booking_type from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no  and b.booking_type=1  and b.status_active=1 and b.is_deleted=0 $booking_no_cond group by a.entry_form,a.buyer_id,b.po_break_down_id,b.job_no,b.is_short,b.booking_no,b.booking_type";
	$result_booking=sql_select($booking_array);

	foreach($result_booking as $row)
	{
		if($row[csf('booking_type')]==1)// Fabric
		{
			if($row[csf('is_short')]==1)
			{
				$main_fab_booking='Short Booking';
			}
			else if($row[csf('is_short')]==2)
			{
				$main_fab_booking='Main Booking';
			}
			else if($row[csf('is_short')]==2 && $row[csf('entry_form')]==108 )
			{
				$main_fab_booking='Partial Booking';
			}
		}
		else if($row[csf('booking_type')]==4)// Sample Fabric
		{
			$main_fab_booking='Sample Booking';
		}
		$buyer_id_arr[$row[csf('booking_no')]]=$row[csf('buyer_id')];
		//$po_break_down_id.=$row[csf('po_break_down_id')].',';

	}
	$po_id=rtrim($po_break_down_id,',');
	$book_buyer_name=$buyer_library[$buyer_id_arr[$booking_no]];

	$job_no = '';
	$style_ref_no = '';
	if($entry_form==36)
	{
		$po_data = sql_select("select b.order_no, b.cust_style_ref, c.subcon_job as job_no, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and b.id in(" . $po_id . ") group by b.order_no, b.cust_style_ref, c.subcon_job, c.party_id");
		foreach($po_data as $po_d)
		{
			$book_buyer_name.=$buyer_library[$po_d[csf('party_id')]].",";
		}
		$book_buyer_name=chop($book_buyer_name,",");
	}
	else
	{
		$po_data = sql_select("select  c.job_no,c.style_ref_no, c.season_buyer_wise, c.buyer_name from  wo_po_break_down b, wo_po_details_master c where  b.job_id=c.job_id and b.status_active=1 and b.is_deleted=0 and b.id in(" . $po_id . ") group by  c.job_no,c.style_ref_no, c.season_buyer_wise, c.buyer_name");

		if( empty($po_id))
		{

			$sql_style=sql_select("select style_ref_no,buyer_name from sample_development_mst where id in (select style_id from wo_non_ord_samp_booking_dtls where status_active=1 and booking_no ='$booking_no' group by style_id)");
			foreach ($sql_style as $row) {
				$style_ref_no.=$row[csf('style_ref_no')].',';
				$book_buyer_name.=$buyer_library[$row[csf('buyer_name')]].",";
			}
			$style_ref_no=chop($style_ref_no,",");
			$book_buyer_name=chop($book_buyer_name,",");
		}
		//echo "select  c.job_no,c.style_ref_no, c.buyer_name from  wo_po_break_down b, wo_po_details_master c where  b.job_no_mst=c.job_no and b.status_active=1 and b.is_deleted=0 and b.id in(" . $po_id . ") group by  c.job_no,c.style_ref_no, c.buyer_name";
	}
	foreach($po_data as $row)
	{
		$job_no .= $row[csf('job_no')] . ",";
		//$season .= $row[csf('season_buyer_wise')] . ",";
		if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
		//if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
	}

	if ($db_type == 0) {
		$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
	} else if ($db_type == 2) {
		$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, listagg((cast(a.yarn_lot as varchar2(4000))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(a.brand_id,',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(4000))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id";
	}

	$yarn_dtls_array = array();
	$result_sql_yarn_dtls = sql_select($sql_yarn_dtls);
	foreach ($result_sql_yarn_dtls as $row) {
		$yarn_lot = implode(", ", array_unique(explode(",", $row[csf('yarn_lot')])));
		$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
		$brand_name = "";
		foreach ($brand_id as $val) {
			if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= ", " . $brand_arr[$val];
		}

		$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
		$count_name = "";
		foreach ($yarn_count as $val) {
			if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
		}
		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['yarn_lot'] = $yarn_lot;
		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['brand_name'] = $brand_name;
		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['count_name'] = $count_name;
	}
	//var_dump($yarn_dtls_array);
	//$po_no = implode(", ", array_unique(explode(",", substr($po_no, 0, -1))));
	$job_no = implode(", ", array_unique(explode(",", substr($job_no, 0, -1))));
	//$season = implode(",", array_unique(explode(",", substr($season, 0, -1))));





	$color_id = array_unique(explode(",", $batchdata_array[0][csf('color_id')]));
	$color_name = '';
	foreach ($color_id as $color) {
		$color_name .= $color_arr[$color] . ",";
	}
	$color_name = substr($color_name, 0, -1);
	//var_dump($recipe_color_arr);
	$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
	$group_id=$nameArray[0][csf('group_id')];
	$recipe_id=$data_array[0][csf("recipe_id")];

	$k=0;
	$copy_no=array(1,2); //for Dynamic Copy here
	foreach($copy_no as $cid)
	{
		?>
		<div data-print=section class="print6_data">
			<div style="width:1020px;">
				<table width="1020" cellspacing="0" align="center" id="table_<? echo $cid;?>">
					<tr>
						<td width="500" align="center" style="font-size:xx-large;">
							<strong><? echo $company_library[$data[0]];//$company_library[$data[0]]; ?></strong></td>
						<td width="400" id="barcode_img_id_<? echo $cid;?>" class="barcode_img">&nbsp;</td>
						<td width="130" id="number_of_copy">
							<?
								if($cid==1){
									echo "<h3>Store Copy</h3>";
								}
								else if($cid==2){
									echo "<h3>Dyeing Copy</h3>";
								}
							?>
						</td>
					</tr>
					<tr class="form_caption">
						<td colspan="6" align="center" style="font-size:14px">
							<?
							/*
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
							}*/
							?>
						</td>
						<td></td>
					</tr>
					<tr>
						<td colspan="6" align="center" style="font-size: large">
							<strong><u><? echo $data[2]; ?>	Report</u></strong>
						</td>
						<td></td>
					</tr>
				</table>
				<table width="980" cellspacing="0" align="center">
					<tr>
						<td width="90"><strong>Req. ID </strong></td>
						<td width="160px"><? echo $dataArray[0][csf('requ_no')]; ?></td>
						<td width="100"><strong>Req. Date</strong></td>
						<td width="190px"><? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
						<td width="90"><strong>Buyer</strong></td>
						<td width="160px"><? echo $book_buyer_name; ?></td>
					</tr>
					<tr>
						<td><strong>Batch No</strong></td>
						<td><? echo $batchdata_array[0][csf('batch_no')]; ?></td>
						<td><strong>Booking Type</strong></td>
						<td><? echo $main_fab_booking; ?></td>
						<td><strong>Booking No</strong></td>
						<td><? echo $booking_no; ?></td>

					</tr>
					<tr>
						<td><strong>Color</strong></td>
						<td><? echo $color_name; ?></td>
						<td><strong>Batch Weight</strong></td>
						<td><? $tot_bathc_weight=$batch_weight + $batchdata_array[0][csf('batch_weight')];echo number_format($tot_bathc_weight,2,'.',''); ?></td>
						<td><strong>Exten. No</strong></td>
						<td><? echo $extention_no; ?></td>
					</tr>
					<tr>
						<td><strong>Issue Basis</strong></td>
						<td><? echo $receive_basis_arr[$dataArray[0][csf('requisition_basis')]]; ?></td>
						<td><strong>Recipe No</strong></td>
						<td><? echo $data_array[0][csf("recipe_id")]; ?></td>
						<td><strong>Machine No</strong></td>
						<td>
							<?
							$machine_data = sql_select("select machine_no, floor_id from lib_machine_name where id='" . $dataArray[0][csf("machine_id")] . "'");
							echo $machine_data[0][csf('machine_no')];
							?>
						</td>
					</tr>
					<tr>
						<td   width="120"><strong>Dyeing Reprocess</strong></td>
						<td><? echo $dyeing_re_process_name; ?></td>
						<td><strong>Method</strong></td>
						<td><? echo $dyeing_method[$dataArray[0][csf("method")]];; ?></td>
						<td><strong>Style Ref.</strong></td>
						<td>
							<?

							echo implode(",", array_unique(explode(",", $style_ref_no)));
							?>
						</td>
					</tr>
					<tr>
						<td><strong>Floor Name</strong></td>
						<td>
							<?
								$floor_name = return_field_value("floor_name", "lib_prod_floor", "id='" . $machine_data[0][csf('floor_id')] . "'");
								echo $floor_name;
							?>
						</td>
						<td><strong>Job No</strong></td>
						<td><? echo $job_no;  ?></td>
						<td><strong>Season</strong></td>
						<td><?php echo $season_arr[$po_data[0][csf('season_buyer_wise')]]; ?></td>
					</tr>
					<tr>
						<td><strong>Color Range</strong></td>
						<td colspan="5">
							<?
								$color_range_text='';

								$color_range_ids=$data_array[0][csf("color_range")];
								$color_range_id=explode(",", $color_range_ids);
								$color_range_id=array_unique($color_range_id);
								for($ci=0;$ci<count($color_range_id);$ci++)
								{
									if($ci>0) $color_range_text.=",";
									$color_range_text.=$color_range[$color_range_id[$ci]];
								}

								echo $color_range_text;
							?>
						</td>

					</tr>

				</table>
				<br>
				<?
					$batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
					$j = 1;
					$entryForm = $entry_form_arr[$batch_id_qry[0]];
				?>
				<table width="1020" cellspacing="0" align="left" class="rpt_table" border="1" style="font-size:16px" rules="all">
					<thead bgcolor="#dddddd" align="center">

							<tr bgcolor="#CCCCFF">
								<th colspan="5" align="center"><strong>Fabrication</strong></th>
							</tr>
							<tr>
								<th width="30">SL</th>
								<th width="80">Dia/ W. Type</th>

								<th width="300">Constrution & Composition</th>
								<th width="70">Gsm</th>
								<th width="70">Dia</th>
							</tr>
					</thead>
					<tbody>
						<?
							foreach ($batch_id_qry as $b_id) {
								$batch_query = "Select  po_id, prod_id, item_description, width_dia_type, count(roll_no) as gmts_qty, sum(batch_qnty) batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by po_id, prod_id, item_description, width_dia_type";
								$result_batch_query = sql_select($batch_query);
								foreach ($result_batch_query as $rows) {
									if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
									$fabrication_full = $rows[csf("item_description")];
									$fabrication = explode(',', $fabrication_full);
						?>
									<tr bgcolor="<? echo $bgcolor; ?>">
										<td align="center"><? echo $j; ?></td>
										<td align="center"><b><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></b></td>
										<td align="left"><b><? echo $fabrication[0] . ", " . $fabrication[1]; ?></b></td>
										<td align="center"><? echo $fabrication[2]; ?></td>
										<td align="center"><? echo $fabrication[3]; ?></td>
									</tr>
						<?
									$j++;
								}
							}
						?>
					</tbody>
				</table>
				<div style="width:1020px; margin-top:10px">
					<table align="left" cellspacing="0" width="1020" border="1" rules="all" style="font-size:16px; margin-top:25px;" class="rpt_table">
						<thead bgcolor="#dddddd" align="center">
							<tr bgcolor="#CCCCFF">
								<th colspan="13" align="center"><strong>Dyes And Chemical Issue Requisition</strong>
								</th>
							</tr>
							<?
								$sql_rec = "select a.id,a.item_group_id,b.mst_id, b.total_liquor,b.liquor_ratio,b.sub_process_id as sub_process_id,b.prod_id,b.dose_base,b.adj_type,b.comments,b.process_remark from pro_recipe_entry_dtls b,product_details_master a where a.id=b.prod_id and b.mst_id in($recipe_id) and a.item_category_id in(5,6,7,23) and b.status_active=1 and b.is_deleted=0";
								$nameArray = sql_select($sql_rec);
								$process_array = array();
								$process_array_remark = array();
								foreach ($nameArray as $row) {

									if($process_array_liquor_check[$row[csf("mst_id")]][$row[csf("sub_process_id")]]=="")
									{
									$process_array_liquor[$row[csf("sub_process_id")]]['total_liquor'] += $row[csf("total_liquor")];
									$process_array_liquor_check[$row[csf("mst_id")]][$row[csf("sub_process_id")]]=888;
									}
									$process_array_liquor[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
									//$process_array_liquor[$row[csf("sub_process_id")]]['comments'] = $row[csf("comments")];
									$process_array[$row[csf("sub_process_id")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"] = $row[csf("comments")];

								}
								$sql_rec_remark = "select b.mst_id,b.sub_process_id as sub_process_id,b.comments,b.process_remark,b.total_liquor,b.liquor_ratio from pro_recipe_entry_dtls b  where  b.mst_id in($recipe_id) and b.sub_process_id in($subprocessforwashin) and b.status_active=1 and b.is_deleted=0";
								$nameArray_re = sql_select($sql_rec_remark);
								foreach ($nameArray_re as $row) {
									if($process_array_liquor_check[$row[csf("mst_id")]][$row[csf("sub_process_id")]]=="")
									{
									$process_array_liquor[$row[csf("sub_process_id")]]['total_liquor'] += $row[csf("total_liquor")];
									$process_array_liquor_check[$row[csf("mst_id")]][$row[csf("sub_process_id")]]=999;
									}
									$process_array_liquor[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
									$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"] = $row[csf("process_remark")];
									//$process_array_remark[$row[csf("sub_process_id")]]["comments"] = $row[csf("comments")];
								}

								$group_arr = return_library_array("select id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0", 'id', 'item_name');
								if ($db_type == 0) {
									$item_lot_arr = return_library_array("select b.prod_id,group_concat(b.item_lot) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot");
								} else {
									$item_lot_arr = return_library_array("select b.prod_id,listagg((cast(b.item_lot as varchar2(500))),',') within group (order by b.prod_id) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot");
								}
								/*$sql_dtls = "select a.requ_no, a.batch_id, a.recipe_id, b.id,b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit, c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id
								from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
								where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id=$data[1] order by b.id, b.seq_no";*/
								$sql_dtls = "(select a.requ_no, a.batch_id, a.recipe_id, b.id,b.sub_seq,b.sub_process as sub_process,b.seq_no, b.item_category, b.dose_base, b.item_lot, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty,c.current_stock, b.req_qny_edit, c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id
								from dyes_chem_issue_requ_mst a, dyes_chem_issue_requ_dtls b, product_details_master c
								where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and  b.ratio>0  and c.stock_value>0 and c.item_category_id in (5,6,7,23) and a.id=$data[1])
								union
								(select a.requ_no, a.batch_id, a.recipe_id, b.id,b.sub_seq,b.sub_process as sub_process,b.seq_no, b.item_category, b.dose_base, b.item_lot, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, null as current_stock, b.req_qny_edit, null as item_description, null as  item_group_id,  null as  sub_group_name,null as  item_size, null as  unit_of_measure,null as  avg_rate_per_unit,null as prod_id
								from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b
								where a.id=b.mst_id and b.sub_process in ($subprocessforwashin) and a.id=$data[1])
								order by id";
								//echo $sql_dtls;//die;

								$sql_result = sql_select($sql_dtls);

								$sub_process_array = array();
								$sub_process_tot_rec_array = array();
								$sub_process_tot_req_array = array();
								$sub_process_tot_value_array = array();

								foreach ($sql_result as $row) {
									$sub_process_tot_rec_array[$row[csf("sub_process")]] += $row[csf("recipe_qnty")];
									$sub_process_tot_req_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
									$sub_process_tot_value_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")] * $row[csf("avg_rate_per_unit")];
								}

								//var_dump($sub_process_tot_req_array);
								$i = 1;
								$k = 1;
								$recipe_qnty_sum = 0;
								$req_qny_edit_sum = 0;
								$recipe_qnty = 0;
								$req_qny_edit = 0;
								$req_value_sum = 0;
								$req_value_grand = 0;
								$recipe_qnty_grand = 0;
								$req_qny_edit_grand = 0;$total_cost=$grand_tot_ratio_sum=0;

								foreach ($sql_result as $row)
								{
									$current_stock = $row[csf('current_stock')];
									$current_stock_check=number_format($current_stock,7,'.','');

									if($current_stock_check>0)
									{
									if ($i % 2 == 0)
										$bgcolor = "#E9F3FF";
									else
										$bgcolor = "#FFFFFF";
									if (!in_array($row[csf("sub_process")], $sub_process_array))
									{
										$sub_process_array[] = $row[csf('sub_process')];
										if ($k != 1) {
							?>
								<tr bgcolor="#ffd4c4">
									<td colspan="4" align="right"><strong>Total :</strong></td>
									<td align="center"><?php echo number_format($tot_ratio_sum, 4, '.', ''); $total_issue_val+=$issue_val?></td>
									<td>&nbsp;</td>

									<td align="right"><?php echo number_format($req_qny_edit_sum, 4, '.', ''); ?></td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right"><?php echo number_format($tat_issue_value, 2, '.', ''); ?></td>
								</tr>
							<? 			$tat_issue_value=0;
										}
										$tot_ratio_sum = 0;
										$req_qny_edit_sum = 0;
										$req_value_sum = 0;
										$k++;

										//if ($row[csf("sub_process")] == 93 || $row[csf("sub_process")] == 94 || $row[csf("sub_process")] == 95 || $row[csf("sub_process")] == 96 || $row[csf("sub_process")] == 97 || $row[csf("sub_process")] == 98) {
									if(in_array($row[csf("sub_process")],$subprocessForWashArr))
										{
												$pro_remark = $process_array_remark[$row[csf("sub_process")]]["wash_comments"];
										} else {
											$pro_remark = "";
										}
										if ($pro_remark != '') $pro_remark = "," . $pro_remark; else $pro_remark = "";
										$total_liquor = 'Total liquor(ltr)' . ": " . number_format($process_array_liquor[$row[csf("sub_process")]]['total_liquor'],2,'.','');
										$liquor_ratio = 'Liquor Ratio' . ": " . $process_array_liquor[$row[csf("sub_process")]]['liquor_ratio'];
										$liquor_ratio_process=$process_array_liquor[$row[csf("sub_process")]]['liquor_ratio'];
										$leveling_water=", Levelling Water(Ltr) ".$tot_bathc_weight*($liquor_ratio_process-1.5);
							?>
							<tr bgcolor="#CCCCCC">
								<th colspan="12">
									<strong><? echo $dyeing_sub_process[$row[csf("sub_process")]]. ', ' .$liquor_ratio.', ' .$total_liquor . $pro_remark . $leveling_water; ?></strong>
								</th>
							</tr>
							<tr>
								<th width="30">SL</th>

								<th width="150">Item Group</th>
								<th width="150">Item Description</th>
								<th width="70">Dyes Lot</th>
								<th width="80">Dose Base</th>
								<th width="70">Ratio</th>
								<th width="80">Adj-1</th>
								<th width="80">Requisition Qty</th>
								<th width="60">KG</th>
								<th width="50">GM</th>
								<th width="50">MG</th>

								<th width="100">Comments</th>
							</tr>
						</thead>
						<tbody>
							<?
									}

										$req_qny_edit = explode(".", (string)$row[csf("req_qny_edit")]);
										$iss_qnty_kg = $req_qny_edit[0];
										if ($iss_qnty_kg == "") $iss_qnty_kg = 0;
										$iss_qnty_gm = substr($req_qny_edit[1], 0, 3);//$rem[0]; // floor($mg/1000);
										$iss_qnty_mg = substr($req_qny_edit[1], 3, 3);//$rem[1];//round($mg-$iss_qnty_gm*1000);
										$iss_qnty_gm = str_pad($iss_qnty_gm, 3, "0", STR_PAD_RIGHT);
										$iss_qnty_mg = str_pad($iss_qnty_mg, 3, "0", STR_PAD_RIGHT);
										$comment = $process_array[$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"];
										$iss_value = $row[csf("req_qny_edit")]*$row[csf("avg_rate_per_unit")];
										$tat_issue_value +=$iss_value;
										$avg_rate = $iss_value/$row[csf("req_qny_edit")];
										$total_cost+=$row[csf("avg_rate_per_unit")]*$row[csf("req_qny_edit")];
							?>

							<tr bgcolor="<? echo $bgcolor; ?>">
								<td align="center"><? echo $i; ?></td>
									<!--<td><b><? //echo $item_category[$row[csf("item_category")]];  //echo $row[csf("sub_process")];
								?></b></td>-->
								<td><b><? echo $group_arr[$row[csf("item_group_id")]]; ?></b></td>
								<td><b><? echo $row[csf("item_description")] . ' ' . $row[csf("item_size")]; ?></b></td>

								<td><? echo $row[csf("item_lot")]; ?></td>

								<td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
								<td align="center"><? echo number_format($row[csf("ratio")], 6, '.', ''); ?></td>
								<td align="center"><? echo $row[csf("adjust_percent")]; ?></td>
								<td align="right" title="Avg Rate: <? echo $row[csf("avg_rate_per_unit")];?>" ><b><? echo number_format($row[csf("req_qny_edit")], 4, '.', ''); ?></b></td>
								<td align="right"><? echo $iss_qnty_kg; ?></td>
								<td align="right"><? echo $iss_qnty_gm; ?></td>
								<td align="right"><? echo $iss_qnty_mg; ?></td>

								<td align="center"><? echo $comment; ?></td>
							</tr>

							<? 			$i++;
										$recipe_qnty_sum += $row[csf('recipe_qnty')];
										$req_qny_edit_sum += $row[csf('req_qny_edit')];
										$req_value_sum += $req_value;
										$tot_ratio_sum += $row[csf("ratio")];
										$grand_tot_ratio_sum += $row[csf("ratio")];

										$recipe_qnty_grand += $row[csf('recipe_qnty')];
										$req_qny_edit_grand += $row[csf('req_qny_edit')];
										$req_value_grand += $req_value;
										$cost_per_kg = $total_cost/$tot_bathc_weight;
									}
								}
								foreach ($sub_process_tot_rec_array as $val_rec) {
									$totval_rec = $val_rec;
								}
								foreach ($sub_process_tot_req_array as $val_req) {
									$totval_req = $val_req;
								}
								foreach ($sub_process_tot_value_array as $req_value) {
									$tot_req_value = $req_value;
								}

									//$recipe_qnty_grand +=$val_rec;
									//$req_qny_edit_grand +=$val_req;
									//$req_value_grand +=$req_value;
							?>
						</tbody>
						<!-- <tfoot> -->
							<tr bgcolor="#fffcc7">
								<td colspan="5" align="right"><strong>Total :</strong></td>

								<td align="center"><?php echo number_format($tot_ratio_sum, 4, '.', ''); ?></td>
								<td align="center"><?php //echo number_format($tot_ratio_sum, 6, '.', ''); ?></td>
								<td align="right"><?php echo number_format($totval_req, 4, '.', ''); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right"><?php echo number_format($tat_issue_value, 4, '.', ''); $grand_total_iss_value +=$total_issue_value; ?></td>

							</tr>
							<tr bgcolor="#f5ffc7">
								<td colspan="5" align="right"><strong> Grand Total :</strong></td>

								<td align="right"><?php echo number_format($grand_tot_ratio_sum, 4, '.', ''); ?></td>
								<td align="center"><?php //echo number_format($tot_ratio_sum, 6, '.', ''); ?></td>
								<td align="right"><?php echo number_format($req_qny_edit_grand, 4, '.', ''); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>

							</tr>
							<tr bgcolor="#e8edd3">
								<td colspan="5"  align="right"><strong> Total Cost :</strong></td>
								<td align="right"><?php echo number_format($total_cost, 4, '.', ''); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>

							</tr>
							<tr bgcolor="#def1b1">
								<td colspan="5"  align="right"><strong> Cost per KG:</strong></td>
								<td align="right"><?php echo number_format($cost_per_kg, 2, '.', ''); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>


						<!-- </tfoot> -->
					</table>

					<div class="signature_table_container1">


					<?
						echo signature_table(15, $data[0], "1000px");
						$requ_no=$dataArray[0][csf('requ_no')];
					?>
					</div>
				</div>
			</div>
		</div>
		<?
			 $k++;
			 $grand_total_iss_value=0;$tat_issue_value=0;$total_issue_value=0;
	}
	?>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			//alert(valuess);
			var value = valuess;//$("#barcodeValue").val();
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer = 'bmp';// $("input[name=renderer]:checked").val();
			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 70,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			var barcode_numbers = $("[class*='barcode_img']").length;
			//alert(barcode_numbers);
				//$("#barcode_img_id_"+i).html('11');
			//for(var i=1; i<=barcode_numbers; i++){
				value = {code: value, rect: false};
				$("#barcode_img_id_1").show().barcode(value, btype, settings);
				$("#barcode_img_id_2").show().barcode(value, btype, settings);
			//}

		}
			generateBarcode('<? echo $requ_no; ?>');
	</script>

	<script type="text/javascript">
		var staticHeight = 0;
		var pageHeight = 900;
		$('.print6_data').each(function() {
		    staticHeight += $(this).filter(':visible').outerHeight(true);
		    //console.log(staticHeight)
		    if (staticHeight > pageHeight) {//<p style="page-break-after:always;"></p>
		        $(this).after( '<p style="page-break-after:always;"></p>');
		        staticHeight = 0;
				$('.print6_data').addClass("devider").css({
					'margin-top':"80px",
					'margin-left':"50px"
				});
		    }
		    else{
		    	/*$(this).after( '<hr class="hr" style="border-top: dotted 2px #000">');*/
		    	$(this).after( '<p style="page-break-after:always;"></p>');
		    	$(this).css({
					/*'width':"950px",*/
					'margin-left':"50px",
					'margin-top': "50px"
				});
		    	$('.print6_data').addClass("devider");
		    }

		});
		$(".signature_table_container").css({
			'position' : "relative",
			'top' : "25px",
			'height' : "190px",
			'overflow' : "hidden",
			'width' : "100%",
			'clear' : "both",
			'vertical-align' : "baseline"
		});
	</script>
	<style type="text/css">
		/* .devider:nth-of-type(2){
			margin-top: 160px;
			margin-left: 50px;
		} */
		@media print{

			.devider{
				margin-top:3.2333cm;
			}
		}
	</style>
	<?
	exit();
}


if ($action == "chemical_dyes_issue_requisition_without_rate_print_5") //For Group
{
	extract($_REQUEST);
	echo load_html_head_contents("Chemical Dyes Issue Requisition Print", "../../../", 1, 1, '', '', '');
	$data = explode('*', $data);
	//print_r ($data);

	$sql = "select a.id, a.job_no, a.requ_no, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id from dyes_chem_issue_requ_mst a where a.id='$data[1]' and a.company_id='$data[0]'";

	//echo $sql; die;
	$dataArray = sql_select($sql);

	$recipe_id = $dataArray[0][csf('recipe_id')];
	//echo $recipe_id.'FF';
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	//$entry_form_arr = return_library_array("select id, entry_form from pro_batch_create_mst", "id", "entry_form");
	//$order_id_arr=return_library_array( "select id, booking_id from  pro_recipe_entry_mst", "id", "booking_id");
	//$order_no_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
	//$job_no_arr=return_library_array( "select id, job_no from wo_booking_mst", "id", "job_no");

	$season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");

	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");

	$batch_weight = 0;
	if ($db_type == 0) {
		$data_array = sql_select("select group_concat(buyer_id) as buyer_id,group_concat(id) as recipe_id,group_concat(color_range) as color_range, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor, group_concat(concat_ws(':',batch_ratio,liquor_ratio) order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, group_concat(case when entry_form<>60 then batch_id end) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
	} else {
		$data_array = sql_select("select listagg(buyer_id,',') within group (order by buyer_id) as buyer_id,listagg(id,',') within group (order by id) as recipe_id,listagg(color_range,',') within group (order by id) as color_range, listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor, listagg(batch_ratio || ':' || liquor_ratio,',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(case when entry_form<>60 then batch_id end,',') within group (order by batch_id) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
	}
	$batch_weight = $data_array[0][csf("batch_weight")];
	$batch_id = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id")])));

	$batch_id_rec_main = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id_rec_main")])));
	if ($batch_id_rec_main == "") $batch_id_rec_main = 0;

	if ($db_type == 0) {
		$batchdata_array = sql_select("select group_concat(batch_no) as batch_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(distinct color_id) as color_id from pro_batch_create_mst where id in($batch_id)");
	} else {
		$batchdata_array = sql_select("select listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, listagg(color_id ,',') within group (order by id) as color_id, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($batch_id)");
	}

  	$recipe_sql="select a.dyeing_re_process,a.working_company_id,a.company_id,b.booking_no,b.entry_form, b.batch_weight,b.extention_no,b.is_sales,c.po_id from pro_recipe_entry_mst a, pro_batch_create_mst b,pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.batch_id=c.mst_id and a.id in($recipe_id) group by a.working_company_id,a.company_id, b.batch_weight,b.is_sales ,b.booking_no,b.entry_form,c.po_id,b.extention_no,a.dyeing_re_process";
	$result_recipe=sql_select($recipe_sql);
	$po_break_down_id='';
	foreach($result_recipe as $row)
	{
		if($row[csf('is_sales')]==1)
		{
			$fso_mst_id.=$row[csf('po_id')].',';
		}
		else  $po_break_down_id.=$row[csf('po_id')].',';

		$owner_company_id=$row[csf('company_id')];
		$booking_no=$row[csf('booking_no')];
		$entry_form=$row[csf('entry_form')];
		$extention_no=$row[csf('extention_no')];
		$dyeing_re_process_name=$dyeing_re_process[$row[csf('dyeing_re_process')]];
	}
	$fso_mst_id=rtrim($fso_mst_id,',');
	$fso_mst_ids=implode(",",array_unique(explode(",",$fso_mst_id)));
		//echo $po_break_down_id.'DFD';

	//$booking_no_cond=$booking_no;
	if($booking_no!='') $booking_no_cond="and b.booking_no in('$booking_no')";else $booking_no_cond="and b.booking_no in('0')";

	$without_booking_array = "select a.buyer_id,b.booking_no,b.booking_type from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and b.booking_type=1  and b.status_active=1 and b.is_deleted=0 $booking_no_cond";
	foreach($without_booking_array as $row)
	{
		if($row[csf('booking_type')]==4)// Sample Fabric
		{
		$main_fab_booking='Without Sample Booking';
		}
		$buyer_id_arr[$row[csf('booking_no')]]=$row[csf('buyer_id')];
	}
 	$booking_array = "select  a.buyer_id,a.entry_form,b.po_break_down_id,b.job_no,b.is_short,b.booking_no,b.booking_type from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no  and b.booking_type=1  and b.status_active=1 and b.is_deleted=0 $booking_no_cond group by a.entry_form,a.buyer_id,b.po_break_down_id,b.job_no,b.is_short,b.booking_no,b.booking_type";
	$result_booking=sql_select($booking_array);

	foreach($result_booking as $row)
	{
		if($row[csf('booking_type')]==1)// Fabric
		{
			if($row[csf('is_short')]==1)
			{
				$main_fab_booking='Short Booking';
			}
			else if($row[csf('is_short')]==2)
			{
				$main_fab_booking='Main Booking';
			}
			else if($row[csf('is_short')]==2 && $row[csf('entry_form')]==108 )
			{
				$main_fab_booking='Partial Booking';
			}
		}
		else if($row[csf('booking_type')]==4)// Sample Fabric
		{
			$main_fab_booking='Sample Booking';
		}
		$buyer_id_arr[$row[csf('booking_no')]]=$row[csf('buyer_id')];
		$po_break_down_id.=$row[csf('po_break_down_id')].',';

	}
	$po_id=rtrim($po_break_down_id,',');
	$book_buyer_name=$buyer_library[$buyer_id_arr[$booking_no]];

	$job_no = ''; $int_ref_no = '';
	$style_ref_no = '';
	if($entry_form==36)
	{
		$po_data = sql_select("select b.order_no, b.cust_style_ref, c.subcon_job as job_no, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and b.id in(" . $po_id . ") group by b.order_no, b.cust_style_ref, c.subcon_job, c.party_id");
	}
	else
	{
		$po_data = sql_select("select  c.job_no,c.style_ref_no, c.season_buyer_wise, c.buyer_name, b.grouping from  wo_po_break_down b, wo_po_details_master c where  b.job_no_mst=c.job_no and b.status_active=1 and b.is_deleted=0 and b.id in(" . $po_id . ") group by  c.job_no,c.style_ref_no, c.season_buyer_wise, c.buyer_name, b.grouping");

		if( empty($po_id))
		{

			$sql_style=sql_select("select style_ref_no,buyer_name from sample_development_mst where id in (select style_id from wo_non_ord_samp_booking_dtls where status_active=1 and booking_no ='$booking_no' group by style_id)");
			foreach ($sql_style as $row) {
				$style_ref_no.=$row[csf('style_ref_no')].',';
				$book_buyer_name.=$buyer_library[$row[csf('buyer_name')]].",";
			}
			$style_ref_no=chop($style_ref_no,",");
			$book_buyer_name=chop($book_buyer_name,",");
		}
		if($fso_mst_ids!="")
		{
			$sales_booking_array = sql_select("select a.job_no,a.customer_buyer from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0  and a.id in($fso_mst_ids)");
			foreach($sales_booking_array as $row)
			{
				$book_buyer_name.=$buyer_library[$row[csf('customer_buyer')]].",";
			}
			$book_buyer_name=chop($book_buyer_name,",");
		}
		//echo "select  c.job_no,c.style_ref_no, c.buyer_name from  wo_po_break_down b, wo_po_details_master c where  b.job_no_mst=c.job_no and b.status_active=1 and b.is_deleted=0 and b.id in(" . $po_id . ") group by  c.job_no,c.style_ref_no, c.buyer_name";
	}
	foreach($po_data as $row)
	{
		$job_no .= $row[csf('job_no')] . ",";
        $int_ref_no .= $row[csf('grouping')] . ",";
		//$season .= $row[csf('season_buyer_wise')] . ",";
		if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
		//if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
	}

	if ($db_type == 0) {
		$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
	} else if ($db_type == 2) {
		// $sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, listagg((cast(a.yarn_lot as varchar2(4000))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(a.brand_id,',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(4000))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id";
	}

	$yarn_dtls_array = array();
	$result_sql_yarn_dtls = sql_select($sql_yarn_dtls);
	foreach ($result_sql_yarn_dtls as $row) {
		$yarn_lot = implode(", ", array_unique(explode(",", $row[csf('yarn_lot')])));
		$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
		$brand_name = "";
		foreach ($brand_id as $val) {
			if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= ", " . $brand_arr[$val];
		}

		$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
		$count_name = "";
		foreach ($yarn_count as $val) {
			if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
		}
		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['yarn_lot'] = $yarn_lot;
		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['brand_name'] = $brand_name;
		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['count_name'] = $count_name;
	}
	//var_dump($yarn_dtls_array);
	//$po_no = implode(", ", array_unique(explode(",", substr($po_no, 0, -1))));
	$job_no = implode(", ", array_unique(explode(",", substr($job_no, 0, -1))));
    $int_ref_no = implode(", ", array_unique(explode(",", substr($int_ref_no, 0, -1))));
	//$season = implode(",", array_unique(explode(",", substr($season, 0, -1))));





	$color_id = array_unique(explode(",", $batchdata_array[0][csf('color_id')]));
	$color_name = '';
	foreach ($color_id as $color) {
		$color_name .= $color_arr[$color] . ",";
	}
	$color_name = substr($color_name, 0, -1);
	//var_dump($recipe_color_arr);
	$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
	$group_id=$nameArray[0][csf('group_id')];
	$recipe_id=$data_array[0][csf("recipe_id")];

	$k=0;
	$copy_no=array(1,2); //for Dynamic Copy here
	foreach($copy_no as $cid)
	{
	?>
	<div data-print=section class="print6_data">
		<div style="width:1020px;">
			<table width="1020" cellspacing="0" align="center" id="table_<? echo $cid;?>">
				<tr>
					<td width="500" align="center" style="font-size:xx-large;">
						<strong><? echo $company_library[$data[0]];//$company_library[$data[0]]; ?></strong></td>
					<td width="400" id="barcode_img_id_<? echo $cid;?>" class="barcode_img">&nbsp;</td>
					<td width="130" id="number_of_copy">
						<?
							if($cid==1){
								echo "<h3>Store Copy</h3>";
							}
							else if($cid==2){
								echo "<h3>Dyeing Copy</h3>";
							}
						?>
					</td>
				</tr>
				<tr class="form_caption">
					<td colspan="6" align="center" style="font-size:14px">
						<?
						/*
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
						}*/
						?>
					</td>
					<td></td>
				</tr>
				<tr>
					<td colspan="6" align="center" style="font-size: large">
						<strong><u><? echo $data[2]; ?>	Report</u></strong>
					</td>
					<td></td>
				</tr>
			</table>
			<table width="950" cellspacing="0" align="center">
				<tr>
					<td width="90"><strong>Req. ID </strong></td>
					<td width="160px"><? echo $dataArray[0][csf('requ_no')]; ?></td>
					<td width="100"><strong>Req. Date</strong></td>
					<td width="160px"><? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
					<td width="90"><strong>Buyer</strong></td>
					<td width="160px"><? echo $book_buyer_name; ?></td>
				</tr>
				<tr>
					<td><strong>Batch No</strong></td>
					<td><? echo $batchdata_array[0][csf('batch_no')]; ?></td>
					<td><strong>Booking Type</strong></td>
					<td><? echo $main_fab_booking; ?></td>
					<td><strong>Booking No</strong></td>
					<td><? echo $booking_no; ?></td>

				</tr>
				<tr>
					<td><strong>Color</strong></td>
					<td><? echo $color_name; ?></td>
					<td><strong>Batch Weight</strong></td>
					<td><? $tot_bathc_weight=$batch_weight + $batchdata_array[0][csf('batch_weight')];echo number_format($tot_bathc_weight,2,'.',''); ?></td>
					<td><strong>Exten. No</strong></td>
					<td><? echo $extention_no; ?></td>
				</tr>
				<tr>
					<td><strong>Issue Basis</strong></td>
					<td><? echo $receive_basis_arr[$dataArray[0][csf('requisition_basis')]]; ?></td>
					<td><strong>Recipe No</strong></td>
					<td><? echo $data_array[0][csf("recipe_id")]; ?></td>
					<td><strong>Machine No</strong></td>
					<td>
						<?
						$machine_data = sql_select("select machine_no, floor_id from lib_machine_name where id='" . $dataArray[0][csf("machine_id")] . "'");
						echo $machine_data[0][csf('machine_no')];
						?>
					</td>
				</tr>
				<tr>
					<td   width="120"><strong>Dyeing Reprocess</strong></td>
					<td><? echo $dyeing_re_process_name; ?></td>
					<td><strong>Method</strong></td>
					<td><? echo $dyeing_method[$dataArray[0][csf("method")]];; ?></td>
					<td><strong>Style Ref.</strong></td>
					<td>
						<?

						echo implode(",", array_unique(explode(",", $style_ref_no)));
						?>
					</td>
				</tr>
				<tr>
					<td><strong>Floor Name</strong></td>
					<td>
						<?
							$floor_name = return_field_value("floor_name", "lib_prod_floor", "id='" . $machine_data[0][csf('floor_id')] . "'");
							echo $floor_name;
						?>
					</td>
					<td><strong>Job No</strong></td>
					<td><? echo $job_no;  ?></td>
					<td><strong>Season</strong></td>
					<td><?php echo $season_arr[$po_data[0][csf('season_buyer_wise')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Color Range</strong></td>
					<td>
						<?
							$color_range_text='';

							$color_range_ids=$data_array[0][csf("color_range")];
							$color_range_id=explode(",", $color_range_ids);
							$color_range_id=array_unique($color_range_id);
							for($ci=0;$ci<count($color_range_id);$ci++)
							{
								if($ci>0) $color_range_text.=",";
								$color_range_text.=$color_range[$color_range_id[$ci]];
							}

							echo $color_range_text;
						?>
					</td>
                    <td><strong>Int. Ref no</strong></td>
                    <td><?php echo $int_ref_no; ?></td>

				</tr>

			</table>
			<br>
			<?
				$batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
				$j = 1;
				$entryForm = $entry_form_arr[$batch_id_qry[0]];
			?>
			<table width="920" cellspacing="0" align="left" class="rpt_table" border="1" style="font-size:16px" rules="all">
				<thead bgcolor="#dddddd" align="center">

						<tr bgcolor="#CCCCFF">
							<th colspan="5" align="center"><strong>Fabrication</strong></th>
						</tr>
						<tr>
							<th width="30">SL</th>
							<th width="80">Dia/ W. Type</th>

							<th width="200">Constrution & Composition</th>
							<th width="70">Gsm</th>
							<th width="70">Dia</th>
						</tr>
				</thead>
				<tbody>
					<?
						foreach ($batch_id_qry as $b_id) {
							$batch_query = "Select  po_id, prod_id, item_description, width_dia_type, count(roll_no) as gmts_qty, sum(batch_qnty) batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by po_id, prod_id, item_description, width_dia_type";
							$result_batch_query = sql_select($batch_query);
							foreach ($result_batch_query as $rows) {
								if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								$fabrication_full = $rows[csf("item_description")];
								$fabrication = explode(',', $fabrication_full);
					?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td align="center"><? echo $j; ?></td>
									<td align="center"><b><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></b></td>
									<td align="left"><b><? echo $fabrication[0] . ", " . $fabrication[1]; ?></b></td>
									<td align="center"><? echo $fabrication[2]; ?></td>
									<td align="center"><? echo $fabrication[3]; ?></td>
								</tr>
					<?
								$j++;
							}
						}
					?>
				</tbody>
			</table>
			<div style="width:920px; margin-top:10px">
				<table align="left" cellspacing="0" width="920" border="1" rules="all" style="font-size:16px; margin-top:25px;" class="rpt_table">
					<thead bgcolor="#dddddd" align="center">
						<tr bgcolor="#CCCCFF">
							<th colspan="13" align="center"><strong>Dyes And Chemical Issue Requisition</strong>
							</th>
						</tr>
						<?
							$sql_rec = "select a.id,a.item_group_id,b.mst_id, b.total_liquor,b.liquor_ratio,b.sub_process_id as sub_process_id,b.prod_id,b.dose_base,b.adj_type,b.comments,b.process_remark from pro_recipe_entry_dtls b,product_details_master a where a.id=b.prod_id and b.mst_id in($recipe_id) and a.item_category_id in(5,6,7,23) and b.status_active=1 and b.is_deleted=0";
							$nameArray = sql_select($sql_rec);
							$process_array = array();
							$process_array_remark = array();
							foreach ($nameArray as $row) {

								if($process_array_liquor_check[$row[csf("mst_id")]][$row[csf("sub_process_id")]]=="")
								{
								$process_array_liquor[$row[csf("sub_process_id")]]['total_liquor'] += $row[csf("total_liquor")];
								$process_array_liquor_check[$row[csf("mst_id")]][$row[csf("sub_process_id")]]=888;
								}
								$process_array_liquor[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
								//$process_array_liquor[$row[csf("sub_process_id")]]['comments'] = $row[csf("comments")];
								$process_array[$row[csf("sub_process_id")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"] = $row[csf("comments")];
								$process_array_remark[$row[csf("sub_process_id")]]["remark"] = $row[csf("process_remark")];

							}
							$sql_rec_remark = "select b.mst_id,b.sub_process_id as sub_process_id,b.comments,b.process_remark,b.total_liquor,b.liquor_ratio from pro_recipe_entry_dtls b  where  b.mst_id in($recipe_id) and b.sub_process_id in($subprocessforwashin) and b.status_active=1 and b.is_deleted=0";
							$nameArray_re = sql_select($sql_rec_remark);
							foreach ($nameArray_re as $row) {
								if($process_array_liquor_check[$row[csf("mst_id")]][$row[csf("sub_process_id")]]=="")
								{
								$process_array_liquor[$row[csf("sub_process_id")]]['total_liquor'] += $row[csf("total_liquor")];
								$process_array_liquor_check[$row[csf("mst_id")]][$row[csf("sub_process_id")]]=999;
								}
								$process_array_liquor[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
								$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"] = $row[csf("process_remark")];
								//$process_array_remark[$row[csf("sub_process_id")]]["comments"] = $row[csf("comments")];
								$process_array_remark[$row[csf("sub_process_id")]]["remark"] = $row[csf("process_remark")];
							}

							$group_arr = return_library_array("select id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0", 'id', 'item_name');
							if ($db_type == 0) {
								$item_lot_arr = return_library_array("select b.prod_id,group_concat(b.item_lot) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot");
							} else {
								// $item_lot_arr = return_library_array("select b.prod_id,listagg((cast(b.item_lot as varchar2(500))),',') within group (order by b.prod_id) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot");
							}
							/*$sql_dtls = "select a.requ_no, a.batch_id, a.recipe_id, b.id,b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit, c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id
							from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
							where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id=$data[1] order by b.id, b.seq_no";*/
							//and c.stock_value>0 ->cut
							$sql_dtls = "(select a.requ_no, a.batch_id, a.recipe_id, b.id,b.sub_seq,b.sub_process as sub_process,b.seq_no, b.item_category, b.dose_base, b.item_lot, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty,c.current_stock, b.req_qny_edit, c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id
							from dyes_chem_issue_requ_mst a, dyes_chem_issue_requ_dtls b, product_details_master c
							where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and  b.ratio>0   and c.item_category_id in (5,6,7,23) and a.id=$data[1])
							union
							(select a.requ_no, a.batch_id, a.recipe_id, b.id,b.sub_seq,b.sub_process as sub_process,b.seq_no, b.item_category, b.dose_base, b.item_lot, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, null as current_stock, b.req_qny_edit, null as item_description, null as  item_group_id,  null as  sub_group_name,null as  item_size, null as  unit_of_measure,null as  avg_rate_per_unit,null as prod_id
							from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b
							where a.id=b.mst_id and b.sub_process in ($subprocessforwashin) and a.id=$data[1])
							order by id";
							//echo $sql_dtls;//die;

							$sql_result = sql_select($sql_dtls);
							$sub_process_array = array();
							$sub_process_tot_rec_array = array();
							$sub_process_tot_req_array = array();
							$sub_process_tot_value_array = array();

							foreach ($sql_result as $row) {
								$sub_process_tot_rec_array[$row[csf("sub_process")]] += $row[csf("recipe_qnty")];
								$sub_process_tot_req_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
								$sub_process_tot_value_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")] * $row[csf("avg_rate_per_unit")];
							}

							//var_dump($sub_process_tot_req_array);
							$i = 1;
							$k = 1;
							$recipe_qnty_sum = 0;
							$req_qny_edit_sum = 0;
							$recipe_qnty = 0;
							$req_qny_edit = 0;
							$req_value_sum = 0;
							$req_value_grand = 0;
							$recipe_qnty_grand = 0;
							$req_qny_edit_grand = 0;$total_cost=$grand_tot_ratio_sum=0;

							foreach ($sql_result as $row)
							{
								$current_stock = $row[csf('current_stock')];
								$current_stock_check=number_format($current_stock,7,'.','');

								// if($current_stock_check>0)
								// {
										if ($i % 2 == 0)
											$bgcolor = "#E9F3FF";
										else
											$bgcolor = "#FFFFFF";
								if (!in_array($row[csf("sub_process")], $sub_process_array))
								{
									$sub_process_array[] = $row[csf('sub_process')];
									if ($k != 1) {
						?>
							<tr bgcolor="#ffd4c4">
								<td colspan="5" align="right"><strong>Total :</strong></td>
								<td align="center"><?php echo number_format($tot_ratio_sum, 6, '.', ''); $total_issue_val+=$issue_val?></td>
								<td>&nbsp;</td>

								<td align="right"><?php echo number_format($req_qny_edit_sum, 4, '.', ''); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>

								<td>&nbsp;</td>
								<td align="right"><?php echo number_format($tat_issue_value, 2, '.', ''); ?></td>
							</tr>
						<? 			$tat_issue_value=0;
									}
									$tot_ratio_sum = 0;
									$req_qny_edit_sum = 0;
									$req_value_sum = 0;
									$k++;

									//if ($row[csf("sub_process")] == 93 || $row[csf("sub_process")] == 94 || $row[csf("sub_process")] == 95 || $row[csf("sub_process")] == 96 || $row[csf("sub_process")] == 97 || $row[csf("sub_process")] == 98) {
								if(in_array($row[csf("sub_process")],$subprocessForWashArr))
									{
											$pro_remark = $process_array_remark[$row[csf("sub_process")]]["wash_comments"];
									} else {
										$pro_remark = "";
									}
									if ($pro_remark != '') $pro_remark = "," . $pro_remark; else $pro_remark = "";
									$total_liquor = 'Total liquor(ltr)' . ": " . number_format($process_array_liquor[$row[csf("sub_process")]]['total_liquor'],2,'.','');
									$liquor_ratio = 'Liquor Ratio' . ": " . $process_array_liquor[$row[csf("sub_process")]]['liquor_ratio'];
									$liquor_ratio_process=$process_array_liquor[$row[csf("sub_process")]]['liquor_ratio'];
									$leveling_water=", Levelling Water(Ltr) ".number_format($tot_bathc_weight*($liquor_ratio_process-1.5),2,'.','');

									$remark = $process_array_remark[$row[csf("sub_process")]]["remark"];
									//echo $remark;
									if ($remark == '') $remark = ''; else $remark = $remark . ', ';
						?>
						<tr bgcolor="#CCCCCC">
							<th colspan="12">
								<strong><? echo $dyeing_sub_process[$row[csf("sub_process")]]. ', ' .$remark .$liquor_ratio.', ' .$total_liquor . $pro_remark . $leveling_water; ?></strong>
							</th>
						</tr>
						<tr>
							<th width="30">SL</th>

							<th width="80">Item Group</th>
							<th width="100">Item Description</th>
							<th width="70">Dyes Lot</th>
							<th width="100">Dose Base</th>
							<th width="70">Ratio</th>
							<th width="80">Adj-1</th>
							<th width="80">Iss. Qty.</th>
							<th width="60">KG</th>
							<th width="50">GM</th>
							<th width="50">MG</th>

							<th width="100">Comments</th>
						</tr>
					</thead>
					<tbody>
						<?
								}

									$req_qny_edit = explode(".", (string)$row[csf("req_qny_edit")]);
									$iss_qnty_kg = $req_qny_edit[0];
									if ($iss_qnty_kg == "") $iss_qnty_kg = 0;
									$iss_qnty_gm = substr($req_qny_edit[1], 0, 3);//$rem[0]; // floor($mg/1000);
									$iss_qnty_mg = substr($req_qny_edit[1], 3, 3);//$rem[1];//round($mg-$iss_qnty_gm*1000);
									$iss_qnty_gm = str_pad($iss_qnty_gm, 3, "0", STR_PAD_RIGHT);
									$iss_qnty_mg = str_pad($iss_qnty_mg, 3, "0", STR_PAD_RIGHT);
									$comment = $process_array[$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"];
									$iss_value = $row[csf("req_qny_edit")]*$row[csf("avg_rate_per_unit")];
									$tat_issue_value +=$iss_value;
									$avg_rate = $iss_value/$row[csf("req_qny_edit")];
									$total_cost+=$row[csf("avg_rate_per_unit")]*$row[csf("req_qny_edit")];
						?>

						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $i; ?></td>
								<!--<td><b><? //echo $item_category[$row[csf("item_category")]];  //echo $row[csf("sub_process")];
							?></b></td>-->
							<td><b><? echo $group_arr[$row[csf("item_group_id")]]; ?></b></td>
							<td><b><? echo $row[csf("item_description")] . ' ' . $row[csf("item_size")]; ?></b></td>

							<td><? echo $row[csf("item_lot")]; ?></td>

							<td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
							<td align="center"><? echo number_format($row[csf("ratio")], 6, '.', ''); ?></td>
							<td align="center"><? echo $row[csf("adjust_percent")]; ?></td>
							<td align="right" title="Avg Rate: <? echo $row[csf("avg_rate_per_unit")];?>" ><b><? echo number_format($row[csf("req_qny_edit")], 4, '.', ''); ?></b></td>
							<td align="right"><? echo $iss_qnty_kg; ?></td>
							<td align="right"><? echo $iss_qnty_gm; ?></td>
							<td align="right"><? echo $iss_qnty_mg; ?></td>

							<td align="center"><? echo $comment; ?></td>
						</tr>

						<? 			$i++;
									$recipe_qnty_sum += $row[csf('recipe_qnty')];
									$req_qny_edit_sum += $row[csf('req_qny_edit')];
									$req_value_sum += $req_value;
									$tot_ratio_sum += $row[csf("ratio")];
									$grand_tot_ratio_sum += $row[csf("ratio")];

									$recipe_qnty_grand += $row[csf('recipe_qnty')];
									$req_qny_edit_grand += $row[csf('req_qny_edit')];
									$req_value_grand += $req_value;
									$cost_per_kg = $total_cost/$tot_bathc_weight;
								// }
							}
							foreach ($sub_process_tot_rec_array as $val_rec) {
								$totval_rec = $val_rec;
							}
							foreach ($sub_process_tot_req_array as $val_req) {
								$totval_req = $val_req;
							}
							foreach ($sub_process_tot_value_array as $req_value) {
								$tot_req_value = $req_value;
							}

								//$recipe_qnty_grand +=$val_rec;
								//$req_qny_edit_grand +=$val_req;
								//$req_value_grand +=$req_value;
						?>
					</tbody>
					<!-- <tfoot> -->
						<tr bgcolor="#fffcc7">
							<td colspan="5" align="right"><strong>Total :</strong></td>

							<td align="center"><?php echo number_format($tot_ratio_sum, 6, '.', ''); ?></td>
							<td align="center"><?php //echo number_format($tot_ratio_sum, 6, '.', ''); ?></td>
							<td align="right"><?php echo number_format($totval_req, 4, '.', ''); ?></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td align="right"><?php echo number_format($tat_issue_value, 4, '.', ''); $grand_total_iss_value +=$total_issue_value; ?></td>

						</tr>
						<tr bgcolor="#f5ffc7">
							<td colspan="5" align="right"><strong> Grand Total :</strong></td>

							<td align="right"><?php echo number_format($grand_tot_ratio_sum, 4, '.', ''); ?></td>
							<td align="center"><?php //echo number_format($tot_ratio_sum, 6, '.', ''); ?></td>
							<td align="right"><?php echo number_format($req_qny_edit_grand, 4, '.', ''); ?></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>

						</tr>
						<tr bgcolor="#e8edd3">
							<td colspan="5"  align="right"><strong> Total Cost :</strong></td>
							<td align="right"><?php echo number_format($total_cost, 4, '.', ''); ?></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>

						</tr>
						<tr bgcolor="#def1b1">
							<td colspan="5"  align="right"><strong> Cost per KG:</strong></td>
							<td align="right"><?php echo number_format($cost_per_kg, 2, '.', ''); ?></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>


					<!-- </tfoot> -->
				</table>

				<div class="signature_table_container1">


				<?
					echo signature_table(15, $data[0], "1000px");
					$requ_no=$dataArray[0][csf('requ_no')];
				?>
				</div>
			</div>
		</div>
	</div>
	<?
		 $k++;
		 $grand_total_iss_value=0;$tat_issue_value=0;$total_issue_value=0;
	}
	?>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			//alert(valuess);
			var value = valuess;//$("#barcodeValue").val();
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer = 'bmp';// $("input[name=renderer]:checked").val();
			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 70,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			var barcode_numbers = $("[class*='barcode_img']").length;
			//alert(barcode_numbers);
				//$("#barcode_img_id_"+i).html('11');
			//for(var i=1; i<=barcode_numbers; i++){
				value = {code: value, rect: false};
				$("#barcode_img_id_1").show().barcode(value, btype, settings);
				$("#barcode_img_id_2").show().barcode(value, btype, settings);
			//}

		}
			generateBarcode('<? echo $requ_no; ?>');
	</script>

	<script type="text/javascript">
		var staticHeight = 0;
		var pageHeight = 900;
		$('.print6_data').each(function() {
		    staticHeight += $(this).filter(':visible').outerHeight(true);
		    //console.log(staticHeight)
		    if (staticHeight > pageHeight) {//<p style="page-break-after:always;"></p>
		        $(this).after( '<p style="page-break-after:always;"></p>');
		        staticHeight = 0;
				$('.print6_data').addClass("devider").css({
					'margin-top':"80px",
					'margin-left':"50px"
				});
		    }
		    else{
		    	/*$(this).after( '<hr class="hr" style="border-top: dotted 2px #000">');*/
		    	$(this).after( '<p style="page-break-after:always;"></p>');
		    	$(this).css({
					/*'width':"950px",*/
					'margin-left':"50px",
					'margin-top': "50px"
				});
		    	$('.print6_data').addClass("devider");
		    }

		});
		$(".signature_table_container").css({
			'position' : "relative",
			'top' : "25px",
			'height' : "190px",
			'overflow' : "hidden",
			'width' : "100%",
			'clear' : "both",
			'vertical-align' : "baseline"
		});
	</script>
	<style type="text/css">
		/* .devider:nth-of-type(2){
			margin-top: 160px;
			margin-left: 50px;
		} */
		@media print{

			.devider{
				margin-top:3.2333cm;
			}
		}
	</style>
	<?
	exit();
}

if ($action == "chemical_dyes_issue_requisition_without_rate_print_11") //Auko-Tex Group
{
	extract($_REQUEST);
	echo load_html_head_contents("Chemical Dyes Issue Requisition Print", "../../../", 1, 1, '', '', '');
	$data = explode('*', $data);
	//print_r ($data);

	$sql = "select a.id, a.job_no, a.requ_no, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id from dyes_chem_issue_requ_mst a where a.id='$data[1]' and a.company_id='$data[0]'";

	//echo $sql; die;
	$dataArray = sql_select($sql);

	$recipe_id = $dataArray[0][csf('recipe_id')];
	//echo $recipe_id.'FF';
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	//$entry_form_arr = return_library_array("select id, entry_form from pro_batch_create_mst", "id", "entry_form");
	//$order_id_arr=return_library_array( "select id, booking_id from  pro_recipe_entry_mst", "id", "booking_id");
	//$order_no_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
	//$job_no_arr=return_library_array( "select id, job_no from wo_booking_mst", "id", "job_no");
	$season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");
	$buyer_brand_arr = return_library_array("select id, brand_name from lib_buyer_brand", 'id', 'brand_name');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");

	$batch_weight = 0;
	if ($db_type == 0) {
		$data_array = sql_select("select group_concat(buyer_id) as buyer_id,group_concat(id) as recipe_id,group_concat(color_range) as color_range, group_concat(batch_id) as batch_id, group_concat(concat_ws(':',batch_ratio,liquor_ratio) order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, group_concat(case when entry_form<>60 then batch_id end) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
	} else {
		$data_array = sql_select("SELECT listagg(buyer_id,',') within group (order by buyer_id) as buyer_id,listagg(id,',') within group (order by id) as recipe_id,listagg(color_range,',') within group (order by id) as color_range, listagg(batch_id,',') within group (order by batch_id) as batch_id, listagg(batch_ratio || ':' || liquor_ratio,',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(case when entry_form<>60 then batch_id end,',') within group (order by batch_id) as batch_id_rec_main, LISTAGG (CASE WHEN entry_form = 60 THEN new_batch_weight WHEN entry_form != 60 THEN batch_qty END, ',') WITHIN GROUP (ORDER BY batch_qty) AS batch_qty from pro_recipe_entry_mst where id in($recipe_id)");
	}
	$batch_weight = $data_array[0][csf("batch_weight")];
	$batch_id = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id")])));

	$batch_id_rec_main = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id_rec_main")])));
	if ($batch_id_rec_main == "") $batch_id_rec_main = 0;

	if ($db_type == 0) {
		$batchdata_array = sql_select("select group_concat(batch_no) as batch_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(distinct color_id) as color_id from pro_batch_create_mst where id in($batch_id)");
	} else {
		$batchdata_array = sql_select("select listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, listagg(color_id ,',') within group (order by id) as color_id, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($batch_id)");
	}

  	$recipe_sql="select a.dyeing_re_process,a.working_company_id,a.company_id,b.booking_no,b.entry_form, b.batch_weight,b.extention_no,b.is_sales,c.po_id from pro_recipe_entry_mst a, pro_batch_create_mst b,pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.batch_id=c.mst_id and a.id in($recipe_id) group by a.working_company_id,a.company_id, b.batch_weight,b.is_sales ,b.booking_no,b.entry_form,c.po_id,b.extention_no,a.dyeing_re_process";
	$result_recipe=sql_select($recipe_sql);
	$po_break_down_id='';
	foreach($result_recipe as $row)
	{
		if($row[csf('is_sales')]==1)
		{
			$fso_mst_id.=$row[csf('po_id')].',';
		}
		else  $po_break_down_id.=$row[csf('po_id')].',';

		$owner_company_id=$row[csf('company_id')];
		$booking_no=$row[csf('booking_no')];
		$entry_form=$row[csf('entry_form')];
		$extention_no=$row[csf('extention_no')];
		$dyeing_re_process_name=$dyeing_re_process[$row[csf('dyeing_re_process')]];
	}
	$fso_mst_id=rtrim($fso_mst_id,',');
	$fso_mst_ids=implode(",",array_unique(explode(",",$fso_mst_id)));
		//echo $po_break_down_id.'DFD';

	//$booking_no_cond=$booking_no;
	if($booking_no!='') $booking_no_cond="and b.booking_no in('$booking_no')";else $booking_no_cond="and b.booking_no in('0')";

	$without_booking_array = "select a.buyer_id,b.booking_no,b.booking_type from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and  and b.status_active=1 and b.is_deleted=0 $booking_no_cond";

	$without_booking_array=sql_select($without_booking_array);

	foreach($without_booking_array as $row)
	{
		if($row[csf('booking_type')]==4)// Sample Fabric
		{
		$main_fab_booking='Without Sample Booking';
		}
		$buyer_id_arr[$row[csf('booking_no')]]=$row[csf('buyer_id')];
	}
 	$booking_array = "select  a.buyer_id,a.entry_form,b.po_break_down_id,b.job_no,b.is_short,b.booking_no,b.booking_type from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.status_active=1 and b.is_deleted=0 $booking_no_cond group by a.entry_form,a.buyer_id,b.po_break_down_id,b.job_no,b.is_short,b.booking_no,b.booking_type";
	$result_booking=sql_select($booking_array);

	foreach($result_booking as $row)
	{
		if($row[csf('booking_type')]==1)// Fabric
		{
			if($row[csf('is_short')]==1)
			{
				$main_fab_booking='Short Booking';
			}
			else if($row[csf('is_short')]==2)
			{
				$main_fab_booking='Main Booking';
			}
			else if($row[csf('is_short')]==2 && $row[csf('entry_form')]==108 )
			{
				$main_fab_booking='Partial Booking';
			}
		}
		else if($row[csf('booking_type')]==4)// Sample Fabric
		{
			$main_fab_booking='Sample Booking';
		}
		$buyer_id_arr[$row[csf('booking_no')]]=$row[csf('buyer_id')];
		$po_break_down_id.=$row[csf('po_break_down_id')].',';

	}
	$po_id=rtrim($po_break_down_id,',');
	$book_buyer_name=$buyer_library[$buyer_id_arr[$booking_no]];

	$job_no = ''; $int_ref_no = '';	$brand_id='';
	$style_ref_no = '';
	if($entry_form==36)
	{
		$po_data = sql_select("select b.order_no, b.cust_style_ref, c.subcon_job as job_no, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and b.id in(" . $po_id . ") group by b.order_no, b.cust_style_ref, c.subcon_job, c.party_id");
	}
	else
	{
	 	$po_data = sql_select("select  c.job_no,c.style_ref_no, c.season_buyer_wise, c.buyer_name, b.grouping,c.brand_id from  wo_po_break_down b, wo_po_details_master c where  b.job_no_mst=c.job_no and b.status_active=1 and b.is_deleted=0 and b.id in(" . $po_id . ") group by  c.job_no,c.style_ref_no, c.season_buyer_wise, c.buyer_name, b.grouping, c.brand_id");

		if( empty($po_id))
		{
			$sql_style=sql_select("select style_ref_no, internal_ref, brand_id,buyer_name from sample_development_mst where id in (select style_id from wo_non_ord_samp_booking_dtls where status_active=1 and booking_no ='$booking_no' group by style_id)");
			foreach ($sql_style as $row) {
				$style_ref_no.=$row[csf('style_ref_no')].',';
				$book_buyer_name.=$buyer_library[$row[csf('buyer_name')]].",";
				$brand_id_mst=$row[csf('brand_id')].',';
				$int_ref_no=$row[csf('internal_ref')].',';
			}
			$style_ref_no=chop($style_ref_no,",");
			$book_buyer_name=chop($book_buyer_name,",");
		}
	}

	foreach($po_data as $row)
	{
		$job_no .= $row[csf('job_no')] . ",";
        $int_ref_no .= $row[csf('grouping')] . ",";
        $brand_id_mst .= $row[csf('brand_id')] . ",";
		if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
		//if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
	}
	$sql_yarn_dtls = "select  c.po_breakdown_id as po_id,d.yarn_lot as yarn_lot,d.yarn_count as yarn_count,d.brand_id as brand_id,d.prod_id
	from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
	where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id
	and a.id in($batch_id) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22) group by  c.po_breakdown_id,d.yarn_lot,d.yarn_count,d.brand_id,d.prod_id";
	//echo $sql_yarn_dtls; die;

	$yarn_dtls_array = array();
	$result_sql_yarn_dtls = sql_select($sql_yarn_dtls);
	foreach ($result_sql_yarn_dtls as $row)
	{
		//$yarn_lot = implode(", ", array_unique(explode(",", $row[csf('yarn_lot')])));
		$yarn_id = $row[csf('yarn_lot')];//array_unique(explode(",", $row[csf('yarn_lot')]));
		$yarn_lot = "";
		foreach ($yarn_id as $val) {
		//	if ($yarn_lot == "") $yarn_lot = $val; else $yarn_lot .= ", " . $val;
		}

		$brand_id = $row[csf('brand_id')];//array_unique(explode(",", $row[csf('brand_id')]));
		$brand_name = "";
		foreach ($brand_id as $val) {
			//if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= ", " . $brand_arr[$val];
		}

		$yarn_count =$row[csf('yarn_count')];// array_unique(explode(",", $row[csf('yarn_count')]));
		$count_name = "";
		foreach ($yarn_count as $val) {
		//	if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
		}
		$yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['yarn_lot'] .= $yarn_id. "";
		$yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['brand_name'] .= $brand_arr[$brand_id]. "";
		$yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['count_name'] .= $count_name[$yarn_count]. "";
	}

	//var_dump($yarn_dtls_array);
	//$po_no = implode(", ", array_unique(explode(",", substr($po_no, 0, -1))));
	$job_no = implode(", ", array_unique(explode(",", substr($job_no, 0, -1))));
    $int_ref_no = implode(", ", array_unique(explode(",", substr($int_ref_no, 0, -1))));
    $brand_id_mst = implode(", ", array_unique(explode(",", substr($brand_id_mst, 0, -1))));
	$color_id = array_unique(explode(",", $batchdata_array[0][csf('color_id')]));
	$color_name = '';
	foreach ($color_id as $color) {
		$color_name .= $color_arr[$color] . ",";
	}
	$color_name = substr($color_name, 0, -1);
	//var_dump($recipe_color_arr);
	$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
	$group_id=$nameArray[0][csf('group_id')];
	$recipe_id=$data_array[0][csf("recipe_id")];
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$data[0]'", "image_location");
	$k=0;
	$copy_no=array(1,2,3); //for Dynamic Copy here

	foreach($copy_no as $cid)
	{

	?>
	<div data-print=section>
		<div style="width:950px;">
			<table width="950" cellspacing="0" align="center"  border="0">
				<?php /*?><tr>
					<td width="500" align="center" style="font-size:xx-large;">
						<strong><? echo $company_library[$data[0]];//$company_library[$data[0]]; ?></strong></td>
					<td width="400" id="barcode_img_id_<? echo $cid;?>" class="barcode_img">&nbsp;</td>
					<td width="130" id="number_of_copy">
						<?
							if($cid==1){
								echo "<h3>Store Copy</h3>";
							}
							else if($cid==2){
								echo "<h3>Dyeing Copy</h3>";
							}
							else if($cid==3){
								echo "<h3>Recipe Copy</h3>";
							}
						?>
					</td>
				</tr><?php */?>

                <tr>
	            	<td rowspan="2"  align="left">
                    <img src="../../<? echo $image_location; ?>" height="65"></td>
	            	<td colspan="1" align="center"  style="font-size:28px;"><strong><? echo $company_library[$data[0]];?></strong></td>
                    <td style="font-size:x-large; padding-left:40px;">&nbsp; <strong><?php if($cid==1) { echo 'Store Copy';} elseif($cid==2) { echo  'Dyeing Copy';} elseif($cid==3){ echo 'Recipe Copy';} else { echo 'th';}?></strong></td>
	        	</tr>

				<!--<tr class="form_caption">
					<td colspan="1" align="center" style="font-size:14px">
						<?
						/*
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
						}*/
						?>
					</td>
				</tr>-->
				<tr>
					<td  align="center" style="font-size:large; vertical-align:text-top">
						<u><? echo $data[2]; ?>	Report</u>
					</td>
					<td align="center" id="barcode_img_id_<? echo $cid;?>" class="barcode_img"></td>
				</tr>
			</table>
            <table>
            <tr style="font-size:1px;"><td colspan="6">&nbsp;</td> </tr>
            </table>
			<table width="950" cellspacing="0" align="center" border="1">
				<tr>
					<td width="130"><strong>Req. ID </strong></td>
					<td width="190px">: <? echo $dataArray[0][csf('requ_no')]; ?></td>
					<td width="100"><strong>Req. Date</strong></td>
					<td width="160px">: <? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
					<td width="100"><strong>Buyer</strong></td>
					<td >: <? echo $book_buyer_name; ?></td>
				</tr>
				<tr>
					<td><strong>Batch No</strong></td>
					<td>: <? echo $batchdata_array[0][csf('batch_no')]; ?></td>
					<td><strong>Booking Type</strong></td>
					<td>: <? echo $main_fab_booking; ?></td>
					<td><strong>Booking No</strong></td>
					<td>: <? echo $booking_no; ?></td>

				</tr>
				<tr>
					<td><strong>Color</strong></td>
					<td>: <? echo $color_name; ?></td>
					<td><strong>Batch Weight</strong></td>
					<td>: <? $tot_bathc_weight=$batch_weight + $batchdata_array[0][csf('batch_weight')];echo number_format($tot_bathc_weight,2,'.',''); ?></td>
					<td><strong>Exten. No</strong></td>
					<td>: <? echo $extention_no; ?></td>
				</tr>
				<tr>
					<td><strong>Issue Basis</strong></td>
					<td>: <? echo $receive_basis_arr[$dataArray[0][csf('requisition_basis')]]; ?></td>
					<td><strong>Recipe No</strong></td>
					<td>: <? echo $data_array[0][csf("recipe_id")]; ?></td>
					<td><strong>Machine No</strong></td>
					<td>
						: <?
						$machine_data = sql_select("select machine_no, floor_id from lib_machine_name where id='" . $dataArray[0][csf("machine_id")] . "'");
						echo $machine_data[0][csf('machine_no')];
						?>
					</td>
				</tr>
				<tr>
					<td width="120"><strong>Dyeing Reprocess</strong></td>
					<td>: <? echo $dyeing_re_process_name; ?></td>
					<td><strong>Net Wt</strong></td>
					<td>: <? echo $data_array[0][csf("batch_qty")];//$batch_weight ?></td>
					<td><strong>Style Ref.</strong></td>
					<td>
						: <?

						echo implode(",", array_unique(explode(",", $style_ref_no)));
						?>
					</td>
				</tr>
				<tr>
					<td><strong>Floor Name</strong></td>
					<td>
						: <?
							$floor_name = return_field_value("floor_name", "lib_prod_floor", "id='" . $machine_data[0][csf('floor_id')] . "'");
							echo $floor_name;
						?>
					</td>
					<td><strong>Job No</strong></td>
					<td>: <? echo $job_no;  ?></td>
					<td><strong>Season</strong></td>
					<td>: <?php echo $season_arr[$po_data[0][csf('season_buyer_wise')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Color Range</strong></td>
					<td>
						: <?
							$color_range_text='';

							$color_range_ids=$data_array[0][csf("color_range")];
							$color_range_id=explode(",", $color_range_ids);
							$color_range_id=array_unique($color_range_id);
							for($ci=0;$ci<count($color_range_id);$ci++)
							{
								if($ci>0) $color_range_text.=",";
								$color_range_text.=$color_range[$color_range_id[$ci]];
							}

							echo $color_range_text;
						?>
					</td>
                    <td><strong>Int. Ref no</strong></td>
                    <td>: <?php echo $int_ref_no; ?></td>
					<td><strong>Buyer Brand</strong></td>
                    <td>: <?php echo  $buyer_brand_arr[$brand_id_mst]; ?></td>

				</tr>

			</table>
			<br>
					<?
					$batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
					$j = 1;
					$entryForm = $entry_form_arr[$batch_id_qry[0]];
					?>
					<table width="950" cellspacing="0"  class="rpt_table" border="1" rules="all">
						<thead bgcolor="#dddddd" align="center">
							<?
							if ($entryForm == 74)
							{
								?>
								<tr bgcolor="#CCCCFF">
									<th colspan="4" align="center"><strong>Fabrication</strong></th>
								</tr>
								<tr>
									<th width="50">SL</th>
									<th width="350">Gmts. Item</th>
									<th width="110">Gmts. Qty</th>
									<th>Batch Qty.</th>
								</tr>
								<?
							}
							else
							{
								?>
								<tr bgcolor="#CCCCFF">
									<th colspan="9" align="center"><strong>Fabrication</strong></th>
								</tr>
								<tr>
									<th width="30">SL</th>
									<th width="100">Dia/ W. Type</th>
									<th width="100">Yarn Lot</th>
									<th width="100">Brand</th>
									<th width="100">Count</th>
									<th width="300">Constrution & Composition</th>
									<th width="70">Gsm</th>
									<th width="70">Dia</th>
									<th width="70">Qty</th>
								</tr>
								<?
							}
							?>
						</thead>
						<tbody>
							<?


							foreach ($batch_id_qry as $b_id)
							{
								$batch_query = "select po_id, prod_id, item_description, width_dia_type, sum(roll_no)  as gmts_qty, sum(batch_qnty) as  batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by po_id, prod_id, item_description, width_dia_type ";
								//echo $batch_query;
								//echo "<pre>";print_r($yarn_dtls_array);
								$result_batch_query = sql_select($batch_query);
								foreach ($result_batch_query as $rows)
								{
									if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
									$fabrication_full = $rows[csf("item_description")];

									$fabrication = explode(',', $fabrication_full);
									if ($entry_form_arr[$b_id] == 36)
									{
										?>
										<tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:14px">
											<td align="center"><? echo $j; ?></td>
											<td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
				                            <td align="center"><? echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['yarn_lot'];
				                            	?></td>
				                            <td align="center"><? echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['brand_name'];
				                            	?></td>
				                            <td align="center"><? echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['count_name'];
				                            	?></td>
			                            	<td align="left"><? echo $fabrication[0]; ?></td>
			                            	<td align="center"><? echo $fabrication[1]; ?></td>
			                            	<td align="center"><? echo $fabrication[3]; ?></td>
											<td align="right"><? echo number_format($rows[csf('batch_qnty')], 2); ?></td>
			                            </tr>
		                            <?
									}
									else if ($entry_form_arr[$b_id] == 74)
									{
										?>
										<tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:14px">
											<td align="center"><? echo $j; ?></td>
											<td align="left"><? echo $garments_item[$rows[csf('prod_id')]]; ?></td>
											<td align="right"><? echo $rows[csf('gmts_qty')]; ?></td>
											<td align="right"><? echo number_format($rows[csf('batch_qnty')], 2); ?></td>
										</tr>
										<?
									}
									else
									{
									//echo $entry_form_arr[$b_id].'aaa';
										?>
										<? //echo $yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['yarn_lot']; ?>
										<tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:14px">
											<td align="center"><? echo $j; ?></td>
											<td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
											<td width="100" align="left"><p style="word-break:break-all;"><? echo $yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['yarn_lot']; ?></p></td>
											<td align="left"><? echo $yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['brand_name']; ?></td>
											<td align="left"><? echo $yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['count_name']; ?></td>
											<td align="left"><? echo $fabrication[0] . ", " . $fabrication[1]; ?></td>
											<td align="center"><? echo $fabrication[2]; ?></td>
											<td align="center"><? echo $fabrication[3]; ?></td>
											<td align="right"><? echo number_format($rows[csf('batch_qnty')], 2); ?></td>
										</tr>
										<?
									}
									$j++;
								}
							}


		                ?>
		            </tbody>
		        </table>
			<br>
			<div style="width:950px; margin-top:10px">
	        	<table align="right" cellspacing="0" width="950" border="1" rules="all" class="rpt_table">
	        		<thead bgcolor="#dddddd" align="center">
	        			<tr bgcolor="#CCCCFF">
	        				<th colspan="19" align="center"><strong>Dyes And Chemical Issue Requisition</strong></th>
	        			</tr>
	        		</thead>
	        		<?


					/*$sql_rec="select a.id, a.item_group_id, b.sub_process_id as sub_process_id, b.prod_id, b.dose_base, b.adj_type, b.comments, b.process_remark
		 from pro_recipe_entry_dtls b left join product_details_master a on b.prod_id=a.id and a.item_category_id in(5,6,7,23)
		 where b.mst_id in($recipe_id)  and b.status_active=1 and b.is_deleted=0 ";*/

		 $group_arr = return_library_array("select id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0", 'id', 'item_name');


		 $sql_rec = "select a.id,a.item_group_id,b.mst_id, b.total_liquor,b.liquor_ratio,b.sub_process_id as sub_process_id,b.prod_id,b.dose_base,b.adj_type,b.comments,b.process_remark from pro_recipe_entry_dtls b,product_details_master a where a.id=b.prod_id and b.mst_id in($recipe_id) and a.item_category_id in(5,6,7,23) and b.status_active=1 and b.is_deleted=0";
			$nameArray = sql_select($sql_rec);
			$process_array = array();
			$process_array_remark = array();
			foreach ($nameArray as $row) {

				if($process_array_liquor_check[$row[csf("mst_id")]][$row[csf("sub_process_id")]]=="")
				{
				$process_array_liquor[$row[csf("sub_process_id")]]['total_liquor'] += $row[csf("total_liquor")];
				$process_array_liquor_check[$row[csf("mst_id")]][$row[csf("sub_process_id")]]=888;
				}
				$process_array_liquor[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
				//$process_array_liquor[$row[csf("sub_process_id")]]['comments'] = $row[csf("comments")];
				$process_array[$row[csf("sub_process_id")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"] = $row[csf("comments")];
				$process_array_remark[$row[csf("sub_process_id")]]["remark"] = $row[csf("process_remark")];

			}

		$sql_dtls = "(select a.requ_no, a.batch_id, a.recipe_id, b.id,b.sub_seq,b.sub_process as sub_process,b.seq_no, b.item_category, b.dose_base, b.item_lot, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty,c.current_stock, b.req_qny_edit, c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id
		from dyes_chem_issue_requ_mst a, dyes_chem_issue_requ_dtls b, product_details_master c
		where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and  b.ratio>0   and c.item_category_id in (5,6,7,23) and a.id=$data[1])
		union
		(select a.requ_no, a.batch_id, a.recipe_id, b.id,b.sub_seq,b.sub_process as sub_process,b.seq_no, b.item_category, b.dose_base, b.item_lot, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, null as current_stock, b.req_qny_edit, null as item_description, null as  item_group_id,  null as  sub_group_name,null as  item_size, null as  unit_of_measure,null as  avg_rate_per_unit,null as prod_id
		from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b
		where a.id=b.mst_id and b.sub_process in ($subprocessforwashin) and a.id=$data[1])
		order by id";
		//echo $sql_dtls;//die;

		$sql_result = sql_select($sql_dtls);
		$sub_process_array = array();
		$sub_process_tot_rec_array = array();
		$sub_process_tot_req_array = array();
		$sub_process_tot_value_array = array();

		foreach ($sql_result as $row) {
			$sub_process_tot_rec_array[$row[csf("sub_process")]] += $row[csf("recipe_qnty")];
			$sub_process_tot_req_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
			$sub_process_tot_value_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")] * $row[csf("avg_rate_per_unit")];
		}

		//var_dump($sub_process_tot_req_array);
		$i = 1;
		$k = 1;
		$recipe_qnty_sum = 0;
		$req_qny_edit_sum = 0;
		$recipe_qnty = 0;
		$req_qny_edit = 0;
		$req_value_sum = 0;
		$req_value_grand = 0;
		$recipe_qnty_grand = 0;
		$req_qny_edit_grand = 0;$total_cost=$grand_tot_ratio_sum=0;

		foreach ($sql_result as $row)
		{
			$current_stock = $row[csf('current_stock')];
			$current_stock_check=number_format($current_stock,7,'.','');

			// if($current_stock_check>0)
			// {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
			if (!in_array($row[csf("sub_process")], $sub_process_array))
			{
				$sub_process_array[] = $row[csf('sub_process')];
				if ($k != 1) {
	?>
		<tr bgcolor="#ffd4c4">
			<td colspan="5" align="right"><strong>Total :</strong></td>
			<td align="center"><?php echo number_format($tot_ratio_sum, 6, '.', ''); $total_issue_val+=$issue_val?></td>
			<!-- <td>&nbsp;</td> -->

			<td align="right"><?php echo number_format($req_qny_edit_sum, 4, '.', ''); ?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>

			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<!-- <td align="right"><?php //echo number_format($tat_issue_value, 2, '.', ''); ?></td> -->
		</tr>



	<?php /*?><? 			$tat_issue_value=0;
				}
				$tot_ratio_sum = 0;
				$req_qny_edit_sum = 0;
				$req_value_sum = 0;
				$k++;

				//if ($row[csf("sub_process")] == 93 || $row[csf("sub_process")] == 94 || $row[csf("sub_process")] == 95 || $row[csf("sub_process")] == 96 || $row[csf("sub_process")] == 97 || $row[csf("sub_process")] == 98) {
			if(in_array($row[csf("sub_process")],$subprocessForWashArr))
				{
						$pro_remark = $process_array_remark[$row[csf("sub_process")]]["wash_comments"];
				} else {
					$pro_remark = "";
				}
				if ($pro_remark != '') $pro_remark = "," . $pro_remark; else $pro_remark = "";
				$total_liquor = 'Total liquor(ltr)' . ": " . number_format($process_array_liquor[$row[csf("sub_process")]]['total_liquor'],2,'.','');
				$liquor_ratio = 'Liquor Ratio' . ": " . $process_array_liquor[$row[csf("sub_process")]]['liquor_ratio'];
				$liquor_ratio_process=$process_array_liquor[$row[csf("sub_process")]]['liquor_ratio'];
				$leveling_water=", Levelling Water(Ltr) ".number_format($tot_bathc_weight*($liquor_ratio_process-1.5),2,'.','');

				$remark = $process_array_remark[$row[csf("sub_process")]]["remark"];
				//echo $remark;
				if ($remark == '') $remark = ''; else $remark = $remark . ', ';
			?><?php */?>


    		<? 			$tat_issue_value=0;
									}
									$tot_ratio_sum = 0;
									$req_qny_edit_sum = 0;
									$req_value_sum = 0;
									$k++;

									//if ($row[csf("sub_process")] == 93 || $row[csf("sub_process")] == 94 || $row[csf("sub_process")] == 95 || $row[csf("sub_process")] == 96 || $row[csf("sub_process")] == 97 || $row[csf("sub_process")] == 98) {
								if(in_array($row[csf("sub_process")],$subprocessForWashArr))
									{
											$pro_remark = $process_array_remark[$row[csf("sub_process")]]["wash_comments"];
									} else {
										$pro_remark = "";
									}
									if ($pro_remark != '') $pro_remark = "," . $pro_remark; else $pro_remark = "";
									$total_liquor = 'Total liquor(ltr)' . ": " . number_format($process_array_liquor[$row[csf("sub_process")]]['total_liquor'],2,'.','');
									$liquor_ratio = 'Liquor Ratio' . ": " . $process_array_liquor[$row[csf("sub_process")]]['liquor_ratio'];
									$liquor_ratio_process=$process_array_liquor[$row[csf("sub_process")]]['liquor_ratio'];
									$leveling_water=", Levelling Water(Ltr) ".number_format($tot_bathc_weight*($liquor_ratio_process-1.5),2,'.','');

									$remark = $process_array_remark[$row[csf("sub_process")]]["remark"];
									//echo $remark;
									if ($remark == '') $remark = ''; else $remark = $remark . ', ';
						?>







			<tr bgcolor="#CCCCCC">
				<th colspan="11">
					<strong><? echo $dyeing_sub_process[$row[csf("sub_process")]]. ', ' .$remark .$liquor_ratio.', ' .$total_liquor . $pro_remark . $leveling_water; ?></strong>
				</th>
			</tr>
			<tr>
                    <th width="30">SL</th>
                    <th width="120">Item Group</th>
                    <th>Item Description</th>
                    <th width="70">Dyes Lot</th>
                    <th width="70">Dose Base</th>
                    <th width="70">Ratio</th>
                    <!-- <th width="80">Adj-1</th> -->
                    <th width="65">Issue Qty</th>
                    <th width="38">KG</th>
                    <th width="38">GM</th>
                    <th width="38">MG</th>
                    <th width="100">Comments</th>
  			 </tr>
		</thead>
		<tbody>
			<?
					}

						$req_qny_edit = explode(".", (string)$row[csf("req_qny_edit")]);
						$iss_qnty_kg = $req_qny_edit[0];
						if ($iss_qnty_kg == "") $iss_qnty_kg = 0;
						$iss_qnty_gm = substr($req_qny_edit[1], 0, 3);//$rem[0]; // floor($mg/1000);
						$iss_qnty_mg = substr($req_qny_edit[1], 3, 3);//$rem[1];//round($mg-$iss_qnty_gm*1000);
						$iss_qnty_gm = str_pad($iss_qnty_gm, 3, "0", STR_PAD_RIGHT);
						$iss_qnty_mg = str_pad($iss_qnty_mg, 3, "0", STR_PAD_RIGHT);
						$comment = $process_array[$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"];
						$iss_value = $row[csf("req_qny_edit")]*$row[csf("avg_rate_per_unit")];
						$tat_issue_value +=$iss_value;
						$avg_rate = $iss_value/$row[csf("req_qny_edit")];
						$total_cost+=$row[csf("avg_rate_per_unit")]*$row[csf("req_qny_edit")];
			?>

			<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:15px">
				<td align="center"><? echo $i; ?></td>
					<!--<td><b><? //echo $item_category[$row[csf("item_category")]];  //echo $row[csf("sub_process")];
				?></b></td>-->
				<td><b><? echo $group_arr[$row[csf("item_group_id")]]; ?></b></td>
				<td><b><? echo $row[csf("item_description")] . ' ' . $row[csf("item_size")]; ?></b></td>

				<td><? echo $row[csf("item_lot")]; ?></td>

				<td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
				<td align="center"><? echo number_format($row[csf("ratio")], 6, '.', ''); ?></td>
				<!-- <td align="center"><?// echo $row[csf("adjust_percent")]; ?></td> -->
				<td align="right" title="Avg Rate: <? echo $row[csf("avg_rate_per_unit")];?>" ><b><? echo number_format($row[csf("req_qny_edit")], 4, '.', ''); ?></b></td>
				<td align="right"><? echo $iss_qnty_kg; ?></td>
				<td align="right"><? echo $iss_qnty_gm; ?></td>
				<td align="right"><? echo $iss_qnty_mg; ?></td>

				<td align="center"><? echo $comment; ?></td>
			</tr>

			<? 			$i++;
						$recipe_qnty_sum += $row[csf('recipe_qnty')];
						$req_qny_edit_sum += $row[csf('req_qny_edit')];
						$req_value_sum += $req_value;
						$tot_ratio_sum += $row[csf("ratio")];
						$grand_tot_ratio_sum += $row[csf("ratio")];

						$recipe_qnty_grand += $row[csf('recipe_qnty')];
						$req_qny_edit_grand += $row[csf('req_qny_edit')];
						$req_value_grand += $req_value;
						$cost_per_kg = $total_cost/$tot_bathc_weight;
					// }
				}
				foreach ($sub_process_tot_rec_array as $val_rec) {
					$totval_rec = $val_rec;
				}
				foreach ($sub_process_tot_req_array as $val_req) {
					$totval_req = $val_req;
				}
				foreach ($sub_process_tot_value_array as $req_value) {
					$tot_req_value = $req_value;
				}

					//$recipe_qnty_grand +=$val_rec;
					//$req_qny_edit_grand +=$val_req;
					//$req_value_grand +=$req_value;
			?>
		</tbody>
		<!-- <tfoot> -->
			<tr bgcolor="#fffcc7">
				<td colspan="5" align="right"><strong>Total :</strong></td>

				<td align="center"><?php echo number_format($tot_ratio_sum, 6, '.', ''); ?></td>
				<!-- <td align="center"><?php //echo number_format($tot_ratio_sum, 6, '.', ''); ?></td> -->
				<td align="right"><?php echo number_format($totval_req, 4, '.', ''); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>

			</tr>
			<tr bgcolor="#f5ffc7">
				<td colspan="5" align="right"><strong> Grand Total :</strong></td>

				<td align="right"><?php echo number_format($grand_tot_ratio_sum, 4, '.', ''); ?></td>
				<!-- <td align="center"><?php //echo number_format($tot_ratio_sum, 6, '.', ''); ?></td> -->
				<td align="right"><?php echo number_format($req_qny_edit_grand, 4, '.', ''); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>

			</tr>
			<tr bgcolor="#e8edd3">
				<td colspan="5"  align="right"><strong> Total Cost :</strong></td>
				<td align="right"><?php echo number_format($total_cost, 4, '.', ''); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<!-- <td>&nbsp;</td> -->

			</tr>
			<tr bgcolor="#def1b1">
				<td colspan="5"  align="right"><strong> Cost per KG:</strong></td>
				<td align="right"><?php echo number_format($cost_per_kg, 2, '.', ''); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<!-- <td>&nbsp;</td> -->
			</tr>


		<!-- </tfoot> -->
		</table>

				<div class="signature_table_container1">


				<?
					echo signature_table(15, $data[0], "950px",'',0);
					$requ_no=$dataArray[0][csf('requ_no')];
				?>
				</div>
			</div>
		</div>
	</div>
	<?
		 $k++;
		 $grand_total_iss_value=0;$tat_issue_value=0;$total_issue_value=0;
	}
	?>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			//alert(valuess);
			var value = valuess;//$("#barcodeValue").val();
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer = 'bmp';// $("input[name=renderer]:checked").val();
			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 70,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			var barcode_numbers = $("[class*='barcode_img']").length;
			//alert(barcode_numbers);
				//$("#barcode_img_id_"+i).html('11');
			//for(var i=1; i<=barcode_numbers; i++){
				value = {code: value, rect: false};
				$("#barcode_img_id_1").show().barcode(value, btype, settings);
				$("#barcode_img_id_2").show().barcode(value, btype, settings);
				$("#barcode_img_id_3").show().barcode(value, btype, settings);
			//}

		}
			generateBarcode('<? echo $requ_no; ?>');
	</script>

	<script type="text/javascript">
		var staticHeight = 0;
		var pageHeight = 900;
		$('.print6_data').each(function() {
		    staticHeight += $(this).filter(':visible').outerHeight(true);
		    //console.log(staticHeight)
		    if (staticHeight > pageHeight) {//<p style="page-break-after:always;"></p>
		        $(this).after( '<p style="page-break-after:always;"></p>');
		        staticHeight = 0;
				$('.print6_data').addClass("devider").css({
					'margin-top':"5px",
					// 'margin-left':"50px",
					'font-size': "16px"
				});
		    }
		    else{
		    	/*$(this).after( '<hr class="hr" style="border-top: dotted 2px #000">');*/
		    	$(this).after( '<p style="page-break-after:always;"></p>');
		    	$(this).css({
					/*'width':"950px",*/
					// 'margin-left':"50px",
					'margin-top': "30px",
					'font-size': "16px"
				});
		    	$('.print6_data').addClass("devider");
		    }

		});
		$(".signature_table_container").css({
			'position' : "relative",
			'top' : "25px",
			'height' : "190px",
			'overflow' : "hidden",
			'width' : "100%",
			'clear' : "both",
			'vertical-align' : "baseline"
		});
	</script>
	<style type="text/css">
		/* .devider:nth-of-type(2){
			margin-top: 160px;
			margin-left: 50px;
		} */
		@media print{

			.devider{
				margin-top:3.2333cm;
			}
		}
	</style>
	<?
	exit();
}
if ($action == "chemical_dyes_issue_requisition_without_rate_print_10000000--------Z") //For Palmal
{
     
	// extract($_REQUEST);
    // echo load_html_head_contents("Chemical Dyes Issue Requisition Print", "../../../", 1, 1, '', '', '');
	//require('../../../ext_resource/pdf/code128.php');
	//define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');

	//$process = array( &$_POST );
	//extract(check_magic_quote_gpc( $process ));
	extract($_REQUEST);
	echo load_html_head_contents("Chemical Dyes Issue Requisition Print", "../../../", 1, 1, '', '', '');
    $data = explode('*', $data);
    //print_r ($data);

    $sql = "SELECT a.id, a.job_no, a.requ_no, a.requ_prefix_num, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id from dyes_chem_issue_requ_mst a where a.id='$data[1]' and a.company_id='$data[0]'";

    // echo $sql; die;
    $dataArray = sql_select($sql);

    $recipe_id = $dataArray[0][csf('recipe_id')];
	$requ_no_barcode = $dataArray[0][csf('requ_no')];
    //echo $recipe_id.'FF';
    $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
    $buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
    $country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
    $color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
    $entry_form_arr = return_library_array("select id, entry_form from pro_batch_create_mst", "id", "entry_form");
    //$order_id_arr=return_library_array( "select id, booking_id from  pro_recipe_entry_mst", "id", "booking_id");
    //$order_no_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
  //  $job_no_arr=return_library_array( "select id, job_no from wo_booking_mst", "id", "job_no");

    $season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");

    $brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
    $count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
    $group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");

    $batch_weight = 0;
    if ($db_type == 0)
    {
        $data_array = sql_select("SELECT group_concat(buyer_id) as buyer_id,group_concat(id) as recipe_id,group_concat(color_range) as color_range, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor, group_concat(concat_ws(':',batch_ratio,liquor_ratio) order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, group_concat(case when entry_form<>60 then batch_id end) as batch_id_rec_main
        from pro_recipe_entry_mst where id in($recipe_id)");
    }
    else
    {
        $data_array = sql_select("SELECT listagg(buyer_id,',') within group (order by buyer_id) as buyer_id,
        listagg(id,',') within group (order by id) as recipe_id,
        listagg(labdip_no,',') within group (order by labdip_no) as labdip_no,
        listagg(color_range,',') within group (order by id) as color_range,
        listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor,
        listagg(batch_ratio || ':' || liquor_ratio,',') within group (order by id) as ratio,
        sum(case when entry_form=60 then new_batch_weight end) as batch_weight,
        listagg(case when entry_form<>60 then batch_id end,',') within group (order by batch_id) as batch_id_rec_main
        from pro_recipe_entry_mst where id in($recipe_id)");
    }
    $batch_weight = $data_array[0][csf("batch_weight")];
    $batch_id = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id")])));

    $batch_id_rec_main = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id_rec_main")])));
    if ($batch_id_rec_main == "") $batch_id_rec_main = 0;

    if ($db_type == 0) {
        $batchdata_array = sql_select("select group_concat(batch_no) as batch_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(distinct color_id) as color_id from pro_batch_create_mst where id in($batch_id)");
    } else {
        $batchdata_array = sql_select("SELECT listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, listagg(color_id ,',') within group (order by id) as color_id, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($batch_id)");
    }

    $recipe_sql="SELECT a.dyeing_re_process,a.working_company_id,a.company_id,a.labdip_no,a.order_source,a.pickup,a.surplus_solution,a.remarks,a.recipe_description,b.booking_no,b.entry_form, b.batch_weight,b.extention_no,b.is_sales,c.po_id, c.item_description,sum(c.batch_qnty) as batch_qnty, sum(b.total_trims_weight) as total_trims_weight
    from pro_recipe_entry_mst a, pro_batch_create_mst b,pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.batch_id=c.mst_id and a.id in($recipe_id)
    group by a.working_company_id,a.company_id,a.labdip_no,a.order_source,a.pickup,a.surplus_solution,a.remarks,a.recipe_description, b.batch_weight,b.is_sales ,b.booking_no,b.entry_form,c.po_id,b.extention_no,a.dyeing_re_process, c.item_description";
    // echo $recipe_sql;
    $result_recipe=sql_select($recipe_sql);
    $po_break_down_id='';$fabrication = array();$batch_weight_dtls=0;$total_trims_weight=0;
    foreach($result_recipe as $row)
    {
        if($row[csf('is_sales')]==1)
        {
            $fso_mst_id.=$row[csf('po_id')].',';
        }
        else  $po_break_down_id.=$row[csf('po_id')].',';

        $owner_company_id=$row[csf('company_id')];
        $booking_no=$row[csf('booking_no')];
        $labdip_no=$row[csf('labdip_no')];
        $orderSource=$row[csf('order_source')];
        $pickup=$row[csf('pickup')];
        $surplus_solution=$row[csf('surplus_solution')];
        $remarks=$row[csf('remarks')];
        $recipe_description=$row[csf('recipe_description')];
        $batch_weight_dtls+=$row[csf('batch_qnty')];
        $total_trims_weight+=$row[csf('total_trims_weight')];
        $entry_form=$row[csf('entry_form')];
        $extention_no=$row[csf('extention_no')];
        $dyeing_re_process_name=$dyeing_re_process[$row[csf('dyeing_re_process')]];
        $fabrication_full = $row[csf("item_description")];
        $fabrication = explode(',', $fabrication_full);
    }
    $fso_mst_id=rtrim($fso_mst_id,',');
    $fso_mst_ids=implode(",",array_unique(explode(",",$fso_mst_id)));
        //echo $po_break_down_id.'DFD';

    //$booking_no_cond=$booking_no;
    if($booking_no!='') $booking_no_cond="and b.booking_no in('$booking_no')";else $booking_no_cond="and b.booking_no in('0')";

    $without_booking_array = "select a.buyer_id,b.booking_no,b.booking_type from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and b.booking_type=1  and b.status_active=1 and b.is_deleted=0 $booking_no_cond";
    foreach($without_booking_array as $row)
    {
        if($row[csf('booking_type')]==4)// Sample Fabric
        {
        $main_fab_booking='Without Sample Booking';
        }
        $buyer_id_arr[$row[csf('booking_no')]]=$row[csf('buyer_id')];
    }
    $booking_array = "select  a.buyer_id,a.entry_form,b.po_break_down_id,b.job_no,b.is_short,b.booking_no,b.booking_type from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no  and b.booking_type=1  and b.status_active=1 and b.is_deleted=0 $booking_no_cond group by a.entry_form,a.buyer_id,b.po_break_down_id,b.job_no,b.is_short,b.booking_no,b.booking_type";
    $result_booking=sql_select($booking_array);

    foreach($result_booking as $row)
    {
        if($row[csf('booking_type')]==1)// Fabric
        {
            if($row[csf('is_short')]==1)
            {
                $main_fab_booking='Short Booking';
            }
            else if($row[csf('is_short')]==2)
            {
                $main_fab_booking='Main Booking';
            }
            else if($row[csf('is_short')]==2 && $row[csf('entry_form')]==108 )
            {
                $main_fab_booking='Partial Booking';
            }
        }
        else if($row[csf('booking_type')]==4)// Sample Fabric
        {
            $main_fab_booking='Sample Booking';
        }
        $buyer_id_arr[$row[csf('booking_no')]]=$row[csf('buyer_id')];
        $po_break_down_id.=$row[csf('po_break_down_id')].',';

    }
    $po_id=rtrim($po_break_down_id,',');
    $book_buyer_name=$buyer_library[$buyer_id_arr[$booking_no]];

    $job_no = ''; $int_ref_no = '';
    $style_ref_no = '';
    if($entry_form==36)
    {
        $po_data = sql_select("select b.order_no, b.cust_style_ref, c.subcon_job as job_no, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and b.id in(" . $po_id . ") group by b.order_no, b.cust_style_ref, c.subcon_job, c.party_id");
    }
    else
    {
        $po_data = sql_select("select  c.job_no,c.style_ref_no, c.season_buyer_wise, c.buyer_name, b.grouping from  wo_po_break_down b, wo_po_details_master c where  b.job_no_mst=c.job_no and b.status_active=1 and b.is_deleted=0 and b.id in(" . $po_id . ") group by  c.job_no,c.style_ref_no, c.season_buyer_wise, c.buyer_name, b.grouping");

        if( empty($po_id))
        {

            $sql_style=sql_select("select style_ref_no,buyer_name from sample_development_mst where id in (select style_id from wo_non_ord_samp_booking_dtls where status_active=1 and booking_no ='$booking_no' group by style_id)");
            foreach ($sql_style as $row) {
                $style_ref_no.=$row[csf('style_ref_no')].',';
                $book_buyer_name.=$buyer_library[$row[csf('buyer_name')]].",";
            }
            $style_ref_no=chop($style_ref_no,",");
            $book_buyer_name=chop($book_buyer_name,",");
        }
        if($fso_mst_ids!="")
        {
            $sales_booking_array = sql_select("select a.job_no,a.customer_buyer from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0  and a.id in($fso_mst_ids)");
            foreach($sales_booking_array as $row)
            {
                $book_buyer_name.=$buyer_library[$row[csf('customer_buyer')]].",";
            }
            $book_buyer_name=chop($book_buyer_name,",");
        }
        //echo "select  c.job_no,c.style_ref_no, c.buyer_name from  wo_po_break_down b, wo_po_details_master c where  b.job_no_mst=c.job_no and b.status_active=1 and b.is_deleted=0 and b.id in(" . $po_id . ") group by  c.job_no,c.style_ref_no, c.buyer_name";
    }
    foreach($po_data as $row)
    {
        $job_no .= $row[csf('job_no')] . ",";
        $int_ref_no .= $row[csf('grouping')] . ",";
        //$season .= $row[csf('season_buyer_wise')] . ",";
        if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
        //if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
    }

    if ($db_type == 0) {
        $sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
    } else if ($db_type == 2) {
        $sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, listagg((cast(a.yarn_lot as varchar2(4000))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(a.brand_id,',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(4000))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id";
    }

    $yarn_dtls_array = array();
    $result_sql_yarn_dtls = sql_select($sql_yarn_dtls);
    foreach ($result_sql_yarn_dtls as $row) {
        $yarn_lot = implode(", ", array_unique(explode(",", $row[csf('yarn_lot')])));
        $brand_id = array_unique(explode(",", $row[csf('brand_id')]));
        $brand_name = "";
        foreach ($brand_id as $val) {
            if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= ", " . $brand_arr[$val];
        }

        $yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
        $count_name = "";
        foreach ($yarn_count as $val) {
            if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
        }
        $yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['yarn_lot'] = $yarn_lot;
        $yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['brand_name'] = $brand_name;
        $yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['count_name'] = $count_name;
    }
    //var_dump($yarn_dtls_array);
    //$po_no = implode(", ", array_unique(explode(",", substr($po_no, 0, -1))));
    $job_no = implode(", ", array_unique(explode(",", substr($job_no, 0, -1))));
    $int_ref_no = implode(", ", array_unique(explode(",", substr($int_ref_no, 0, -1))));
    //$season = implode(",", array_unique(explode(",", substr($season, 0, -1))));





    $color_id = array_unique(explode(",", $batchdata_array[0][csf('color_id')]));
    $color_name = '';
    foreach ($color_id as $color) {
        $color_name .= $color_arr[$color] . ",";
    }
    $color_name = substr($color_name, 0, -1);
	$batch_idR=$dataArray[0][csf('batch_id')];
    //var_dump($recipe_color_arr);
    $nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
    $group_id=$nameArray[0][csf('group_id')];
    $recipe_id=$data_array[0][csf("recipe_id")];
	// define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	// require('../../../ext_resource/pdf/code39.php');
	// $pdf = new PDF_Code39('P', 'mm', 'a10');
	// $pdf->AddPage();
	// $nnn=$pdf->Code39(1 + 1.3, 1 + 23, $requ_no_barcode);
	// echo $nnn.'=AAAAAAAAA';die;

	require('../../../ext_resource/mpdf60/mpdf.php');
	$mpdf = new mPDF('', 'A4', '', '', 10, 10, 10, 35, 3, 3);	
	$mpdf->writeBarcode(232434343);
	
 
	
	
	//$mpdf->WriteHTML($reportBody,2);
	// $mpdf->writeBarcode($requ_no);
	$user_id=$_SESSION['logic_erp']['user_id'];
	$REAL_FILE_NAME = "chemiDyesReq_".$user_id.'.pdf';
	$file_path=$REAL_FILE_NAME;
	$mpdf->Output($file_path, 'F');



    exit();
	
}

if ($action == "chemical_dyes_issue_requisition_without_rate_print_10") //For Palmal
{
     
	// extract($_REQUEST);
    // echo load_html_head_contents("Chemical Dyes Issue Requisition Print", "../../../", 1, 1, '', '', '');
	//require('../../../ext_resource/pdf/code128.php');
	//define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');

	//$process = array( &$_POST );
	//extract(check_magic_quote_gpc( $process ));
	extract($_REQUEST);
	echo load_html_head_contents("Chemical Dyes Issue Requisition Print", "../../../", 1, 1, '', '', '');
    $data = explode('*', $data);
    //print_r ($data);

    $sql = "SELECT a.id, a.job_no, a.requ_no, a.requ_prefix_num, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id from dyes_chem_issue_requ_mst a where a.id='$data[1]' and a.company_id='$data[0]'";

    // echo $sql; die;
    $dataArray = sql_select($sql);

    $recipe_id = $dataArray[0][csf('recipe_id')];
	$requ_no_barcode = $dataArray[0][csf('requ_no')];
    //echo $recipe_id.'FF';
    $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
    $buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
    $country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
    $color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
   
    //$order_id_arr=return_library_array( "select id, booking_id from  pro_recipe_entry_mst", "id", "booking_id");
    //$order_no_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
  //  $job_no_arr=return_library_array( "select id, job_no from wo_booking_mst", "id", "job_no");

    $season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");

    $brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
    $count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
    $group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");

    $batch_weight = 0;
    if ($db_type == 0)
    {
        $data_array = sql_select("SELECT group_concat(buyer_id) as buyer_id,group_concat(id) as recipe_id,group_concat(color_range) as color_range, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor, group_concat(concat_ws(':',batch_ratio,liquor_ratio) order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, group_concat(case when entry_form<>60 then batch_id end) as batch_id_rec_main
        from pro_recipe_entry_mst where id in($recipe_id)");
    }
    else
    {
        $data_array = sql_select("SELECT listagg(buyer_id,',') within group (order by buyer_id) as buyer_id,
        listagg(id,',') within group (order by id) as recipe_id,
        listagg(labdip_no,',') within group (order by labdip_no) as labdip_no,
        listagg(color_range,',') within group (order by id) as color_range,
        listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor,
        listagg(batch_ratio || ':' || liquor_ratio,',') within group (order by id) as ratio,
        sum(case when entry_form=60 then new_batch_weight end) as batch_weight,
        listagg(case when entry_form<>60 then batch_id end,',') within group (order by batch_id) as batch_id_rec_main
        from pro_recipe_entry_mst where id in($recipe_id)");
    }
    $batch_weight = $data_array[0][csf("batch_weight")];
    $batch_id = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id")])));

	$entry_form_arr = return_library_array("select id, entry_form from pro_batch_create_mst where id in($batch_id)", "id", "entry_form");

    $batch_id_rec_main = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id_rec_main")])));
    if ($batch_id_rec_main == "") $batch_id_rec_main = 0;

    if ($db_type == 0) {
        $batchdata_array = sql_select("select group_concat(batch_no) as batch_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(distinct color_id) as color_id from pro_batch_create_mst where id in($batch_id)");
    } else {
        $batchdata_array = sql_select("SELECT listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, listagg(color_id ,',') within group (order by id) as color_id, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($batch_id)");
    }

    $recipe_sql="SELECT a.dyeing_re_process,a.working_company_id,a.company_id,a.labdip_no,a.order_source,a.pickup,a.surplus_solution,a.remarks,a.recipe_description,b.booking_no,b.entry_form, b.batch_weight,b.extention_no,b.is_sales,c.po_id, c.item_description,sum(c.batch_qnty) as batch_qnty, sum(b.total_trims_weight) as total_trims_weight
    from pro_recipe_entry_mst a, pro_batch_create_mst b,pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.batch_id=c.mst_id and a.id in($recipe_id)
    group by a.working_company_id,a.company_id,a.labdip_no,a.order_source,a.pickup,a.surplus_solution,a.remarks,a.recipe_description, b.batch_weight,b.is_sales ,b.booking_no,b.entry_form,c.po_id,b.extention_no,a.dyeing_re_process, c.item_description";
    // echo $recipe_sql;
    $result_recipe=sql_select($recipe_sql);
    $po_break_down_id='';$fabrication = array();$batch_weight_dtls=0;$total_trims_weight=0;
    foreach($result_recipe as $row)
    {
        if($row[csf('is_sales')]==1)
        {
            $fso_mst_id.=$row[csf('po_id')].',';
        }
        else  $po_break_down_id.=$row[csf('po_id')].',';

        $owner_company_id=$row[csf('company_id')];
        $booking_no=$row[csf('booking_no')];
        $labdip_no=$row[csf('labdip_no')];
        $orderSource=$row[csf('order_source')];
        $pickup=$row[csf('pickup')];
        $surplus_solution=$row[csf('surplus_solution')];
        $remarks=$row[csf('remarks')];
        $recipe_description=$row[csf('recipe_description')];
        $batch_weight_dtls+=$row[csf('batch_qnty')];
        $total_trims_weight+=$row[csf('total_trims_weight')];
        $entry_form=$row[csf('entry_form')];
        $extention_no=$row[csf('extention_no')];
        $dyeing_re_process_name=$dyeing_re_process[$row[csf('dyeing_re_process')]];
        $fabrication_full = $row[csf("item_description")];
        $fabrication = explode(',', $fabrication_full);
    }
    $fso_mst_id=rtrim($fso_mst_id,',');
    $fso_mst_ids=implode(",",array_unique(explode(",",$fso_mst_id)));
        //echo $po_break_down_id.'DFD';

    //$booking_no_cond=$booking_no;
    if($booking_no!='') $booking_no_cond="and b.booking_no in('$booking_no')";else $booking_no_cond="and b.booking_no in('0')";

      $without_booking_array = "select a.buyer_id,b.booking_no,b.booking_type from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and b.booking_type=1  and b.status_active=1 and b.is_deleted=0 $booking_no_cond";
    foreach($without_booking_array as $row)
    {
        if($row[csf('booking_type')]==4)// Sample Fabric
        {
        $main_fab_booking='Without Sample Booking';
        }
        $buyer_id_arr[$row[csf('booking_no')]]=$row[csf('buyer_id')];
    }
    $booking_array = "select  a.buyer_id,a.entry_form,b.po_break_down_id,b.job_no,b.is_short,b.booking_no,b.booking_type from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no  and b.booking_type=1  and b.status_active=1 and b.is_deleted=0 $booking_no_cond group by a.entry_form,a.buyer_id,b.po_break_down_id,b.job_no,b.is_short,b.booking_no,b.booking_type";
    $result_booking=sql_select($booking_array);

    foreach($result_booking as $row)
    {
        if($row[csf('booking_type')]==1)// Fabric
        {
            if($row[csf('is_short')]==1)
            {
                $main_fab_booking='Short Booking';
            }
            else if($row[csf('is_short')]==2)
            {
                $main_fab_booking='Main Booking';
            }
            else if($row[csf('is_short')]==2 && $row[csf('entry_form')]==108 )
            {
                $main_fab_booking='Partial Booking';
            }
        }
        else if($row[csf('booking_type')]==4)// Sample Fabric
        {
            $main_fab_booking='Sample Booking';
        }
        $buyer_id_arr[$row[csf('booking_no')]]=$row[csf('buyer_id')];
        $po_break_down_id.=$row[csf('po_break_down_id')].',';

    }
    $po_id=rtrim($po_break_down_id,',');
    $book_buyer_name=$buyer_library[$buyer_id_arr[$booking_no]];

    $job_no = ''; $int_ref_no = '';
    $style_ref_no = '';
    if($entry_form==36)
    {
        $po_data = sql_select("select b.order_no, b.cust_style_ref, c.subcon_job as job_no, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and b.id in(" . $po_id . ") group by b.order_no, b.cust_style_ref, c.subcon_job, c.party_id");
    }
    else
    {
        $po_data = sql_select("select  c.job_no,c.style_ref_no, c.season_buyer_wise, c.buyer_name, b.grouping from  wo_po_break_down b, wo_po_details_master c where  b.job_no_mst=c.job_no and b.status_active=1 and b.is_deleted=0 and b.id in(" . $po_id . ") group by  c.job_no,c.style_ref_no, c.season_buyer_wise, c.buyer_name, b.grouping");

        if( empty($po_id))
        {

            $sql_style=sql_select("select style_ref_no,buyer_name from sample_development_mst where id in (select style_id from wo_non_ord_samp_booking_dtls where status_active=1 and booking_no ='$booking_no' group by style_id)");
            foreach ($sql_style as $row) {
                $style_ref_no.=$row[csf('style_ref_no')].',';
                $book_buyer_name.=$buyer_library[$row[csf('buyer_name')]].",";
            }
            $style_ref_no=chop($style_ref_no,",");
            $book_buyer_name=chop($book_buyer_name,",");
        }
        if($fso_mst_ids!="")
        {
            $sales_booking_array = sql_select("select a.job_no,a.customer_buyer from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0  and a.id in($fso_mst_ids)");
            foreach($sales_booking_array as $row)
            {
                $book_buyer_name.=$buyer_library[$row[csf('customer_buyer')]].",";
            }
            $book_buyer_name=chop($book_buyer_name,",");
        }
        //echo "select  c.job_no,c.style_ref_no, c.buyer_name from  wo_po_break_down b, wo_po_details_master c where  b.job_no_mst=c.job_no and b.status_active=1 and b.is_deleted=0 and b.id in(" . $po_id . ") group by  c.job_no,c.style_ref_no, c.buyer_name";
    }
    foreach($po_data as $row)
    {
        $job_no .= $row[csf('job_no')] . ",";
        $int_ref_no .= $row[csf('grouping')] . ",";
        //$season .= $row[csf('season_buyer_wise')] . ",";
        if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
        //if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
    }

    if ($db_type == 0) {
        $sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
    } else if ($db_type == 2) {
        $sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, listagg((cast(a.yarn_lot as varchar2(4000))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(a.brand_id,',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(4000))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id";
    }

    $yarn_dtls_array = array();
    $result_sql_yarn_dtls = sql_select($sql_yarn_dtls);
    foreach ($result_sql_yarn_dtls as $row) {
        $yarn_lot = implode(", ", array_unique(explode(",", $row[csf('yarn_lot')])));
        $brand_id = array_unique(explode(",", $row[csf('brand_id')]));
        $brand_name = "";
        foreach ($brand_id as $val) {
            if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= ", " . $brand_arr[$val];
        }

        $yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
        $count_name = "";
        foreach ($yarn_count as $val) {
            if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
        }
        $yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['yarn_lot'] = $yarn_lot;
        $yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['brand_name'] = $brand_name;
        $yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['count_name'] = $count_name;
    }
    //var_dump($yarn_dtls_array);
    //$po_no = implode(", ", array_unique(explode(",", substr($po_no, 0, -1))));
    $job_no = implode(", ", array_unique(explode(",", substr($job_no, 0, -1))));
    $int_ref_no = implode(", ", array_unique(explode(",", substr($int_ref_no, 0, -1))));
    //$season = implode(",", array_unique(explode(",", substr($season, 0, -1))));





    $color_id = array_unique(explode(",", $batchdata_array[0][csf('color_id')]));
    $color_name = '';
    foreach ($color_id as $color) {
        $color_name .= $color_arr[$color] . ",";
    }
    $color_name = substr($color_name, 0, -1);
    //var_dump($recipe_color_arr);
    $nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
    $group_id=$nameArray[0][csf('group_id')];
    $recipe_id=$data_array[0][csf("recipe_id")];

	require('../../../ext_resource/mpdf60/mpdf.php');
	$mpdf = new mPDF('', 'A4', '', '', 10, 10, 10, 35, 3, 3);	
	//  echo $mpdf->writeBarcode($requ_no_barcode).'==A';die;
	
	ob_start();
    $k=0;$dd=1;
    $copy_no=array(1,2); //for Dynamic Copy here
    foreach($copy_no as $cid)
    {
		$dd++;
    ?>
	<!-- <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
		
        function generateBarcode(valuess) {
             alert(valuess);return;
            var value = valuess;//$("#barcodeValue").val();
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();
            var settings = {
                output: renderer,
                bgColor: '#FFFFFF',
                color: '#000000',
                barWidth: 1,
                barHeight: 70,
                moduleSize: 5,
                posX: 10,
                posY: 20,
                addQuietZone: 1
            };
            var barcode_numbers = $("[class*='barcode_img']").length;
             alert(barcode_numbers);
                //$("#barcode_img_id_"+i).html('11');
            //for(var i=1; i<=barcode_numbers; i++){
                value = {code: value, rect: false};
                $("#barcode_img_id_1").show().barcode(value, btype, settings);
                $("#barcode_img_id_2").show().barcode(value, btype, settings);
            //}

        }
             generateBarcode('<? //echo $requ_no_barcode; ?>');
			
    </script> -->
	
	
    <div data-print=section class="print6_data" style="margin-top: 0px;">
        <div style="width:1020px;">
            <table width="1020" cellspacing="0" align="center" id="table_<? echo $cid;?>">
                <tr>
                    <td width="500" align="center" style="font-size:xx-large;">
                        <strong><? echo $company_library[$data[0]];//$company_library[$data[0]]; ?></strong></td>
                    <td width="400" id="barcode_img_id_<? echo $cid;?>" >&nbsp;<? echo $mpdf->writeBarcode($requ_no_barcode);?></td>
                    <td width="130" id="number_of_copy">
                        <?
                            if($cid==1){
                                echo "<h3>Store Copy</h3>";
                            }
                            else if($cid==2){
                                echo "<h3>Machine Copy</h3>";
                            }
							 
                        ?>
                    </td>
                </tr>
                <tr class="form_caption">
                    <td colspan="6" align="center" style="font-size:14px">
                        <?
                        /*
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
                        }*/
                        ?>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="6" align="center" style="font-size: large">
                        <strong><u><? echo $data[2]; ?> Report</u></strong>
                    </td>
                    <td></td>
                </tr>
            </table>
            <table width="1020" cellspacing="0" align="center">
                <tr>
                    <td width="70"><strong>Requisition No</strong></td>
                    <td width="80"><? echo $dataArray[0][csf('requ_prefix_num')]; ?></td>
                    <td width="130"><strong>Req. Date</strong></td>
                    <td width="80"><? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
                    <td width="130"><strong>Labdip No</strong></td>
                    <td width="50"><? echo $labdip_no; ?></td>
                    <td width="130"><strong>Batch No</strong></td>
                    <td width="90"><? echo $batchdata_array[0][csf('batch_no')]; ?></td>
                    <td width="130"><strong>Batch Weight</strong></td>
                    <td width="160" style="font-size:15px" ><b><? $tot_bathc_weight=$batch_weight + $batchdata_array[0][csf('batch_weight')];echo number_format($tot_bathc_weight,2,'.',''); ?></b></td>
                </tr>
                <tr>
                    <td><strong>Ord Source</strong></td>
                    <td><? echo $order_source[$orderSource]; ?></td>
                    <td><strong>Recipe No</strong></td>
                    <td><? echo $data_array[0][csf("recipe_id")]; ?></td>
                    <td><strong>Job No</strong></td>
                    <td><? echo $booking_no; ?></td>
                    <td><strong>No OF Tube</strong></td>
                    <td>
                        <?
                        $machine_data = sql_select("select machine_no, no_of_feeder, cycle_time, floor_id from lib_machine_name where id='" . $dataArray[0][csf("machine_id")] . "'");
                        $cycle_time = $machine_data[0][csf('cycle_time')];
                        $machine_no = $machine_data[0][csf('machine_no')];
                        echo $no_of_tube=$machine_data[0][csf('no_of_feeder')];
                        ?>
                    </td>
                    <td><strong>Rope Length</strong></td>
                    <td title="<?echo $batch_weight_dtls.'='.$fabrication[2].'='.$fabrication[3];?>"><? $rope_length = ($batch_weight_dtls*1000)/($fabrication[2]*$fabrication[3]*2.54/100); echo number_format($rope_length,3); ?></td>
                </tr>
                <tr>
                    <td><strong>Clr. Range:</strong></td>
                    <td>
                        <?
                            $color_range_text='';

                            $color_range_ids=$data_array[0][csf("color_range")];
                            $color_range_id=explode(",", $color_range_ids);
                            $color_range_id=array_unique($color_range_id);
                            for($ci=0;$ci<count($color_range_id);$ci++)
                            {
                                if($ci>0) $color_range_text.=",";
                                $color_range_text.=$color_range[$color_range_id[$ci]];
                            }

                            echo $color_range_text;
                        ?>
                    </td>
                    <td><strong>Cust Buyer</strong></td>
                    <td><? echo $book_buyer_name; ?></td>
                    <td><strong>Batch Fab. Wgt</strong></td>
                    <td><? echo $batch_weight_dtls; ?></td>
                    <td><strong>Trims Wgt</strong></td>
                    <td><? echo $total_trims_weight; ?></td>
                    <td><strong>Elongation Rope</strong></td>
                    <td><? $elongation = $rope_length + ($rope_length*20)/100; echo number_format($elongation,3); ?></td>
                </tr>
                <tr>
                    <td><strong>Elongation %</strong></td>
                    <td><? echo $pickup; ?></td>
                    <td><strong>Carry Over</strong></td>
                    <td><? echo $surplus_solution; ?></td>
                    <td colspan="2" style="border-top: 2px solid; border-left: 2px solid; border-right: 2px solid;"></td>
                    <td><strong>1st Alkali Wtr.</strong></td>
                    <td><? echo number_format($tot_bathc_weight*.4,2);?></td>
                    <td><strong>Winch Speed</strong></td>
                    <td title="<? echo $elongation.'/'.$cycle_time.'/'.$no_of_tube; ?>"><? if($cycle_time=='' || $no_of_tube==''){ $winch_speed = 0;}else{ $winch_speed = ($elongation/$cycle_time)/$no_of_tube; } echo number_format($winch_speed,3); ?></td>
                </tr>
                <tr>
                    <td width="120"><strong>Leveling Wtr.</strong></td>
                    <td></td>
                    <td><strong>Carry Over Wtr.</strong></td>
                    <td><? echo number_format($surplus_solution*$tot_bathc_weight,2); ?></td>
                    <td colspan="2" style="font-size: 25px; border-left: 2px solid;border-right: 2px solid;" align="center"><strong><? echo $recipe_description; ?></strong></td>
                    <td><strong>2nd Alkali Wtr.</strong></td>
                    <td><? echo number_format($tot_bathc_weight*.5,2);?></td>
                    <td><strong>Cycle Time</strong></td>
                    <td><? echo $cycle_time;?></td>
                </tr>
                <tr>
                    <td><strong>MC No</strong></td>
                    <td><? echo $machine_no; ?></td>
                    <td><strong>Yarn Lot</strong></td>
                    <td><? $sql_prod = sql_select("SELECT  d.yarn_lot as yarn_lot,d.yarn_count as yarn_count,d.brand_id as brand_id
                        from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
                        where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id
                        and a.id=$batch_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22) group by  d.yarn_lot,d.yarn_count,d.brand_id");
                        echo $yarn_lot = $sql_prod[0][csf('yarn_lot')]; ?>
                    </td>
                    <td colspan="2" style="border-bottom: 2px solid;border-left: 2px solid;border-right: 2px solid;"></td>
                    <td><strong>Batch Color</strong></td>
                    <td colspan="2" style="font-size:15px" ><b><? echo $color_name; ?></b></td>
                </tr>
                <tr>
                    <td><strong>Dyes Wtr.</strong></td>
                    <td><? echo number_format($tot_bathc_weight*.4,2);?></td>
                    <td><strong>Remark</strong></td>
                    <td colspan="4"><? echo $remarks; ?></td>
                </tr>

            </table>
            <br>
            <?
                $batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
                $j = 1;
                $entryForm = $entry_form_arr[$batch_id_qry[0]];
            ?>
            <table width="950" cellspacing="0" align="left" class="rpt_table" border="1" style="font-size:16px" rules="all">
                <thead bgcolor="#dddddd" align="center">
                    <tr bgcolor="#CCCCFF">
                        <th colspan="5" align="center"><strong>Fabrication</strong></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="80">Dia/ W. Type</th>

                        <th width="200">Constrution & Composition</th>
                        <th width="70">Gsm</th>
                        <th width="70">Dia</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                    foreach ($batch_id_qry as $b_id)
                    {
                        $batch_query = "Select  po_id, prod_id, item_description, width_dia_type, count(roll_no) as gmts_qty, sum(batch_qnty) batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by po_id, prod_id, item_description, width_dia_type";
                        $result_batch_query = sql_select($batch_query);
                        foreach ($result_batch_query as $rows)
                        {
                            if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                            $fabrication_full = $rows[csf("item_description")];
                            $fabrication = explode(',', $fabrication_full);
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>">
                                <td align="center"><? echo $j; ?></td>
                                <td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
                                <td align="left"><? echo $fabrication[0] . ", " . $fabrication[1]; ?></td>
                                <td align="center"><? echo $fabrication[2]; ?></td>
                                <td align="center"><? echo $fabrication[3]; ?></td>
                            </tr>
                            <?
                            $j++;
                        }
                    }
                    ?>
                </tbody>
            </table>
            <div style="width:950px; margin-top:10px">
                <table align="left" cellspacing="0" width="950" border="1" rules="all" style="font-size:16px; margin-top:25px;" class="rpt_table">
                    <thead bgcolor="#dddddd" align="center"> -->
                        <tr bgcolor="#CCCCFF">
                            <th colspan="13" align="center"><strong>Dyes And Chemical Issue Requisition</strong>
                            </th> 
                        </tr>
				</thead>
                        <?
                            $sql_rec = "SELECT a.id,a.item_group_id,b.mst_id, b.total_liquor,b.liquor_ratio,b.sub_process_id as sub_process_id,b.prod_id,b.dose_base,b.adj_type,b.comments,b.process_remark from pro_recipe_entry_dtls b,product_details_master a where a.id=b.prod_id and b.mst_id in($recipe_id) and a.item_category_id in(5,6,7,23)  and b.ratio>0 and b.status_active=1 and b.is_deleted=0";
                            $nameArray = sql_select($sql_rec);
                            $process_array = array();
                            $process_array_remark = array();
                            foreach ($nameArray as $row)
                            {
                                if($process_array_liquor_check[$row[csf("mst_id")]][$row[csf("sub_process_id")]]=="")
                                {
                                $process_array_liquor[$row[csf("sub_process_id")]]['total_liquor'] += $row[csf("total_liquor")];
                                $process_array_liquor_check[$row[csf("mst_id")]][$row[csf("sub_process_id")]]=888;
                                }
                                $process_array_liquor[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
                                //$process_array_liquor[$row[csf("sub_process_id")]]['comments'] = $row[csf("comments")];
                                $process_array[$row[csf("sub_process_id")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"] = $row[csf("comments")];
                                $process_array_remark[$row[csf("sub_process_id")]]["remark"] = $row[csf("process_remark")];
                            }
                            $sql_rec_remark = "select b.mst_id,b.sub_process_id as sub_process_id,b.comments,b.process_remark,b.total_liquor,b.liquor_ratio from pro_recipe_entry_dtls b  where  b.mst_id in($recipe_id) and b.sub_process_id in(93,94,95,96,97,98,140,141,142,143,160,161,162,163,164,165,166,167,168,169) and b.status_active=1 and b.is_deleted=0";
                            $nameArray_re = sql_select($sql_rec_remark);
                            foreach ($nameArray_re as $row)
                            {
                                if($process_array_liquor_check[$row[csf("mst_id")]][$row[csf("sub_process_id")]]=="")
                                {
                                $process_array_liquor[$row[csf("sub_process_id")]]['total_liquor'] += $row[csf("total_liquor")];
                                $process_array_liquor_check[$row[csf("mst_id")]][$row[csf("sub_process_id")]]=999;
                                }
                                $process_array_liquor[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
                                $process_array_remark[$row[csf("sub_process_id")]]["wash_comments"] = $row[csf("process_remark")];
                                //$process_array_remark[$row[csf("sub_process_id")]]["comments"] = $row[csf("comments")];
                                $process_array_remark[$row[csf("sub_process_id")]]["remark"] = $row[csf("process_remark")];
                            }

                            $group_arr = return_library_array("select id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0", 'id', 'item_name');
                            if ($db_type == 0) {
                                $item_lot_arr = return_library_array("select b.prod_id,group_concat(b.item_lot) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot");
                            } else {
                                $item_lot_arr = return_library_array("select b.prod_id,listagg((cast(b.item_lot as varchar2(500))),',') within group (order by b.prod_id) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot");
                            }
                            /*$sql_dtls = "select a.requ_no, a.batch_id, a.recipe_id, b.id,b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit, c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id
                            from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
                            where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id=$data[1] order by b.id, b.seq_no";*/
                            //and c.stock_value>0 ->cut 
                            $sql_dtls = "SELECT a.requ_no, a.batch_id, a.recipe_id, b.id,b.sub_seq,b.sub_process as sub_process,b.seq_no, b.item_category, b.dose_base, b.item_lot, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty,c.current_stock, b.req_qny_edit, c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id , b.comments
                            from dyes_chem_issue_requ_mst a, dyes_chem_issue_requ_dtls b, product_details_master c
                            where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and  b.ratio>0   and c.item_category_id in (5,6,7,23) and a.id=$data[1]
                            UNION
                            select a.requ_no, a.batch_id, a.recipe_id, b.id,b.sub_seq,b.sub_process as sub_process,b.seq_no, b.item_category, b.dose_base, b.item_lot, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, null as current_stock, b.req_qny_edit, null as item_description, null as  item_group_id,  null as  sub_group_name,null as  item_size, null as  unit_of_measure,null as  avg_rate_per_unit,null as prod_id ,b.comments
                            from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b
                            where a.id=b.mst_id and b.sub_process in ($subprocessforwashin) and a.id=$data[1] order by sub_seq";
                            // echo $sql_dtls;//die;

                            $sql_result = sql_select($sql_dtls);
                            $sub_process_array = array();
                            $sub_process_tot_rec_array = array();
                            $sub_process_tot_req_array = array();
                            $sub_process_tot_value_array = array();

                            foreach ($sql_result as $row)
                            {
                                $sub_process_tot_rec_array[$row[csf("sub_process")]] += $row[csf("recipe_qnty")];
                                $sub_process_tot_req_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
                                $sub_process_tot_value_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")] * $row[csf("avg_rate_per_unit")];
                            }

                            //var_dump($sub_process_tot_req_array);
                            $i = 1;
                            $k = 1;
                            $recipe_qnty_sum = 0;
                            $req_qny_edit_sum = 0;
                            $recipe_qnty = 0;
                            $req_qny_edit = 0;
                            $req_value_sum = 0;
                            $req_value_grand = 0;
                            $recipe_qnty_grand = 0;
                            $req_qny_edit_grand = 0;$total_cost=$grand_tot_ratio_sum=0;
                            $wash_sub_process_arr =$subprocessForWashArr;// array('93' => 93,'94' => 94,'95' => 95,'96' => 96,'97' => 97,'98' => 98,'140' => 140,'141' => 141,'142' => 142,'143' => 143 );

						    foreach ($sql_result as $row)
                            {
                                $current_stock = $row[csf('current_stock')];
                                $current_stock_check=number_format($current_stock,7,'.','');

                                // if($current_stock_check>0)
                                // {
                                if ($i % 2 == 0)
                                    $bgcolor = "#E9F3FF";
                                else
                                    $bgcolor = "#FFFFFF";
                                if (!in_array($row[csf("sub_process")], $sub_process_array))
                                {
                                    $sub_process_array[] = $row[csf('sub_process')];
                                    if ($k != 1)
                                    {
                                        //if (!in_array($row[csf("sub_process")], $wash_sub_process_arr))
                                        //{
                                            //echo $row[csf("sub_process")].'<br>';
                                        if ($tot_ratio_sum>0)
                                        {

                                        ?>
                                        <tr bgcolor="#ffd4c4">
                                            <td colspan="5" align="right"><strong>Total <?//=$k.'='.$row[csf("sub_process")];?>:</strong></td>
                                            <td align="center"><?php echo number_format($tot_ratio_sum, 3, '.', ''); $total_issue_val+=$issue_val?></td>
                                            <td>&nbsp;</td>
                                            <td align="right"><?php echo number_format($req_qny_edit_sum, 3, '.', ''); ?></td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td align="right"><?php //echo number_format($tat_issue_value, 3, '.', ''); ?></td>
                                        </tr>
                                        <?
                                        $tat_issue_value=0;
                                        $tot_ratio_sum = 0;
                                        $req_qny_edit_sum = 0;
                                        $req_value_sum = 0;
                                        }
                                    }
                                    $k++;

                                  //  if ($row[csf("sub_process")] == 93 || $row[csf("sub_process")] == 94 || $row[csf("sub_process")] == 95 || $row[csf("sub_process")] == 96 || $row[csf("sub_process")] == 97 || $row[csf("sub_process")] == 98) {
									 if(in_array($row[csf("sub_process")],$subprocessForWashArr))
									 {
                                            $pro_remark = $process_array_remark[$row[csf("sub_process")]]["wash_comments"];
                                    } else {
                                        $pro_remark = "";
                                    }
                                    if ($pro_remark != '') $pro_remark = "," . $pro_remark; else $pro_remark = "";
                                    $total_liquor = 'Total liquor(ltr)' . ": " . number_format($process_array_liquor[$row[csf("sub_process")]]['total_liquor'],2,'.','');
                                    $liquor_ratio = 'Liquor Ratio' . ": " . number_format($process_array_liquor[$row[csf("sub_process")]]['liquor_ratio'],2);
                                    $liquor_ratio_process=$process_array_liquor[$row[csf("sub_process")]]['liquor_ratio'];
                                    $leveling_water=", Levelling Water(Ltr) ".number_format($tot_bathc_weight*($liquor_ratio_process-1.5),2,'.','');

                                    $remark = $process_array_remark[$row[csf("sub_process")]]["remark"];
                                    //echo $remark;
                                    if ($remark == '') $remark = ''; else $remark = $remark . ', ';

                                    ?>
                                    <tr bgcolor="#CCCCCC">
                                        <th colspan="12" title="<?=$row[csf("sub_process")];?>">
                                            <strong><? echo 'Seq.No.: '.$row[csf("sub_seq")].', ' .$dyeing_sub_process[$row[csf("sub_process")]]. ', ' .$remark .$liquor_ratio.', ' .$total_liquor . $leveling_water; ?></strong>
                                        </th>
                                    </tr>
                                    <?if (!in_array($row[csf("sub_process")], $wash_sub_process_arr))
                                    {?>
                                    <tr>
                                        <th width="30">SL</th>

                                        <th width="130">Item Group</th>
                                        <th width="250">Item Description</th>
                                        <th width="200">Dyes Lot</th>
                                        <th width="120">Dose Base</th>
                                        <th width="30">Ratio</th>
                                        <th width="80">Adj-1</th>
                                        <th width="80">Iss. Qty.</th>
                                        <th width="60">KG</th>
                                        <th width="50">GM</th>
                                        <th width="50">MG</th>

                                        <th width="100">Comments</th>
                                    </tr>
                                    <!-- </thead> -->
                                    <tbody>
                                    <?
                                    }
                                }
                                $req_qny_edit = explode(".", (string)$row[csf("req_qny_edit")]);
                                $iss_qnty_kg = $req_qny_edit[0];
                                if ($iss_qnty_kg == "") $iss_qnty_kg = 0;
                                $iss_qnty_gm = substr($req_qny_edit[1], 0, 3);//$rem[0]; // floor($mg/1000);
                                $iss_qnty_mg = substr($req_qny_edit[1], 3, 3);//$rem[1];//round($mg-$iss_qnty_gm*1000);
                                $iss_qnty_gm = str_pad($iss_qnty_gm, 3, "0", STR_PAD_RIGHT);
                                $iss_qnty_mg = str_pad($iss_qnty_mg, 3, "0", STR_PAD_RIGHT);
                                $comment = $process_array[$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"];
                                $iss_value = $row[csf("req_qny_edit")]*$row[csf("avg_rate_per_unit")];
                                $tat_issue_value +=$iss_value;
                                $avg_rate = $iss_value/$row[csf("req_qny_edit")];
                                $total_cost+=$row[csf("avg_rate_per_unit")]*$row[csf("req_qny_edit")];
                                if (!in_array($row[csf("sub_process")], $wash_sub_process_arr))
                                {
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>">
                                    <td align="center"><? echo $i; ?></td>
                                        <!--<td><b><? //echo $item_category[$row[csf("item_category")]];  //echo $row[csf("sub_process")];
                                    ?></b></td>-->
                                    <td><? echo $group_arr[$row[csf("item_group_id")]]; ?></td>
                                    <td style="font-size:larger"><b><? echo $row[csf("item_description")] . ' ' . $row[csf("item_size")]; ?></b></td>

                                    <td><? echo $row[csf("item_lot")]; ?></td>

                                    <td><b><? echo $dose_base[$row[csf("dose_base")]]; ?></b></td>
                                    <td align="center"><? echo number_format($row[csf("ratio")], 4, '.', ''); ?></td>
                                    <td align="center"><? echo $row[csf("adjust_percent")]; ?></td>
                                    <td align="right" title="Avg Rate: <? echo $row[csf("avg_rate_per_unit")];?>" ><b><? echo number_format($row[csf("req_qny_edit")], 4, '.', ''); ?></b></td>
                                    <td align="right"><? echo $iss_qnty_kg; ?></td>
                                    <td align="right"><? echo $iss_qnty_gm; ?></td>
                                    <td align="right"><? echo $iss_qnty_mg; ?></td>

                                    <td align="center"><? echo $row[csf("comments")]; ?></td>
                                </tr>

                                <?
                                $i++;
                                }
                                if (in_array($row[csf("sub_process")], $wash_sub_process_arr))
                                {
                                    $row[csf("ratio")]=0;
                                }
                                $recipe_qnty_sum += $row[csf('recipe_qnty')];
                                $req_qny_edit_sum += $row[csf('req_qny_edit')];
                                $req_value_sum += $req_value;
                                $tot_ratio_sum += $row[csf("ratio")];
                                $grand_tot_ratio_sum += $row[csf("ratio")];

                                $recipe_qnty_grand += $row[csf('recipe_qnty')];
                                $req_qny_edit_grand += $row[csf('req_qny_edit')];
                                $req_value_grand += $req_value;
                                $cost_per_kg = $total_cost/$tot_bathc_weight;
                                // }
                            }
                            foreach ($sub_process_tot_rec_array as $val_rec) {
                                $totval_rec = $val_rec;
                            }
                            foreach ($sub_process_tot_req_array as $val_req) {
                                $totval_req = $val_req;
                            }
                            foreach ($sub_process_tot_value_array as $req_value) {
                                $tot_req_value = $req_value;
                            }

                            //$recipe_qnty_grand +=$val_rec;
                            //$req_qny_edit_grand +=$val_req;
                            //$req_value_grand +=$req_value;
                        ?>
                    </tbody>
                    <!-- <tfoot> -->
                        <?//if (!in_array($row[csf("sub_process")], $wash_sub_process_arr))
                        //{
                        if ($tot_ratio_sum>0)
                        {?>
                        <tr bgcolor="#fffcc7">
                            <td colspan="5" align="right"><strong>Total :</strong></td>

                            <td align="center"><?php echo number_format($tot_ratio_sum, 3, '.', ''); ?></td>
                            <td align="center"><?php //echo number_format($tot_ratio_sum, 6, '.', ''); ?></td>
                            <td align="right"><?php echo number_format($totval_req, 3, '.', ''); ?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td align="right"><?php number_format($tat_issue_value, 3, '.', ''); $grand_total_iss_value +=$total_issue_value; ?></td>

                        </tr>
                        <?}?>
                        <tr bgcolor="#f5ffc7">
                            <td colspan="5" align="right"><strong> Grand Total :</strong></td>

                            <td align="right"><?php echo number_format($grand_tot_ratio_sum, 3, '.', ''); ?></td>
                            <td align="center"><?php //echo number_format($tot_ratio_sum, 6, '.', ''); ?></td>
                            <td align="right"><?php echo number_format($req_qny_edit_grand, 3, '.', ''); ?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>

                        </tr>
                        <tr bgcolor="#e8edd3">
                            <td colspan="5"  align="right"><strong> Total Cost :</strong></td>
                            <td align="right"><?php echo number_format($total_cost, 3, '.', ''); ?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>

                        </tr>
                        <tr bgcolor="#def1b1">
                            <td colspan="5"  align="right"><strong> Cost per KG:</strong></td>
                            <td align="right"><?php echo number_format($cost_per_kg, 3, '.', ''); ?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    <!-- </tfoot> -->
                </table>
                <div class="signature_table_container1">
                <?
                    echo signature_table(15, $data[0], "950px");
                    $requ_no=$dataArray[0][csf('requ_no')];
                ?>
                </div>
            </div>
        </div>
    </div>
	<?php
	if($dd==2)
	{ ?>
		<div style='page-break-after:always'></div>
	<? }
	?>
	
    <?
         $k++;
         $grand_total_iss_value=0;$tat_issue_value=0;$total_issue_value=0;
    }
    ?>
      <!-- <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
		
        function generateBarcode(valuess) {
            alert(valuess);
            var value = valuess;//$("#barcodeValue").val();
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();
            var settings = {
                output: renderer,
                bgColor: '#FFFFFF',
                color: '#000000',
                barWidth: 1,
                barHeight: 70,
                moduleSize: 5,
                posX: 10,
                posY: 20,
                addQuietZone: 1
            };
            var barcode_numbers = $("[class*='barcode_img']").length;
             alert(barcode_numbers);
                //$("#barcode_img_id_"+i).html('11');
            //for(var i=1; i<=barcode_numbers; i++){
                value = {code: value, rect: false};
                $("#barcode_img_id_1").show().barcode(value, btype, settings);
                $("#barcode_img_id_2").show().barcode(value, btype, settings);
            //}

        }
             generateBarcode('<? //echo $requ_no; ?>'); -->
			
    </script>  

    <!-- <script type="text/javascript">
        var staticHeight = 0;
        var pageHeight = 900;
        $('.print6_data').each(function() {
            staticHeight += $(this).filter(':visible').outerHeight(true);
            //console.log(staticHeight)
            if (staticHeight > pageHeight) {//<p style="page-break-after:always;"></p>
                $(this).after( '<p style="page-break-after:always;"></p>');
                staticHeight = 0;
                $('.print6_data').addClass("devider").css({
                    'margin-top':"0px",
                    'margin-left':"50px"
                });
            }
            else{
                /*$(this).after( '<hr class="hr" style="border-top: dotted 2px #000">');*/
                $(this).after( '<p style="page-break-after:always;"></p>');
                $(this).css({
                    /*'width':"950px",*/
                    'margin-left':"50px",
                    'margin-top': "50px"
                });
                $('.print6_data').addClass("devider");
            }

        });
        $(".signature_table_container").css({
            'position' : "relative",
            'top' : "25px",
            'height' : "190px",
            'overflow' : "hidden",
            'width' : "100%",
            'clear' : "both",
            'vertical-align' : "baseline"
        });
    </script> -->
    <!-- <style type="text/css">
        /* .devider:nth-of-type(2){
            margin-top: 160px;
            margin-left: 50px;
        } */
        @media print{

            .devider{
                margin-top:3.2333cm;
            }
        }
    </style> -->
    <?
//	$reportBody=ob_get_contents();
	//$reportBody=trim($html);
	$user_id=$_SESSION['logic_erp']['user_id'];
	foreach (glob("chemiDyesReq_.$user_id*.pdf") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	
	
	$mpdf->WriteHTML(ob_get_contents(),2);
	// $mpdf->writeBarcode($requ_no);
	$user_id=$_SESSION['logic_erp']['user_id'];
	$REAL_FILE_NAME = "chemiDyesReq_".$user_id.'.pdf';
	$file_path=$REAL_FILE_NAME;
	$mpdf->Output($file_path, 'F');



    exit();
	
}

if ($action == "chemical_dyes_issue_requisition_without_rate_print_6") //For Group
{
    extract($_REQUEST);
    echo load_html_head_contents("Chemical Dyes Issue Requisition Print", "../../../", 1, 1, '', '', '');
    $data = explode('*', $data);
    //print_r ($data);

    $sql = "select a.id, a.job_no, a.requ_no, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id from dyes_chem_issue_requ_mst a where a.id='$data[1]' and a.company_id='$data[0]'";

    //echo $sql; die;
    $dataArray = sql_select($sql);

    $recipe_id = $dataArray[0][csf('recipe_id')];
    //echo $recipe_id.'FF';
    $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
    $buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
    $country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
    $color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
    $entry_form_arr = return_library_array("select id, entry_form from pro_batch_create_mst", "id", "entry_form");
    //$order_id_arr=return_library_array( "select id, booking_id from  pro_recipe_entry_mst", "id", "booking_id");
    //$order_no_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
    $job_no_arr=return_library_array( "select id, job_no from wo_booking_mst", "id", "job_no");

    $season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");

    $brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
    $count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
    $group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");

    $batch_weight = 0;
    if ($db_type == 0) {
        $data_array = sql_select("select group_concat(buyer_id) as buyer_id,group_concat(id) as recipe_id,group_concat(color_range) as color_range, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor, group_concat(concat_ws(':',batch_ratio,liquor_ratio) order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, group_concat(case when entry_form<>60 then batch_id end) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
    } else {
        $data_array = sql_select("select listagg(buyer_id,',') within group (order by buyer_id) as buyer_id,listagg(id,',') within group (order by id) as recipe_id,listagg(color_range,',') within group (order by id) as color_range, listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor, listagg(batch_ratio || ':' || liquor_ratio,',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(case when entry_form<>60 then batch_id end,',') within group (order by batch_id) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
    }
    $batch_weight = $data_array[0][csf("batch_weight")];
    $batch_id = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id")])));

    $batch_id_rec_main = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id_rec_main")])));
    if ($batch_id_rec_main == "") $batch_id_rec_main = 0;

    if ($db_type == 0) {
        $batchdata_array = sql_select("select group_concat(batch_no) as batch_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(distinct color_id) as color_id from pro_batch_create_mst where id in($batch_id)");
    } else {
        $batchdata_array = sql_select("select listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, listagg(color_id ,',') within group (order by id) as color_id, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($batch_id)");
    }

    $recipe_sql="select a.dyeing_re_process,a.working_company_id,a.company_id,b.booking_no,b.entry_form, b.batch_weight,b.extention_no,b.is_sales,c.po_id from pro_recipe_entry_mst a, pro_batch_create_mst b,pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.batch_id=c.mst_id and a.id in($recipe_id) group by a.working_company_id,a.company_id, b.batch_weight,b.is_sales ,b.booking_no,b.entry_form,c.po_id,b.extention_no,a.dyeing_re_process";
    $result_recipe=sql_select($recipe_sql);
    $po_break_down_id='';
    foreach($result_recipe as $row)
    {
        if($row[csf('is_sales')]==1)
        {
            $fso_mst_id.=$row[csf('po_id')].',';
        }
        else  $po_break_down_id.=$row[csf('po_id')].',';

        $owner_company_id=$row[csf('company_id')];
        $booking_no=$row[csf('booking_no')];
        $entry_form=$row[csf('entry_form')];
        $extention_no=$row[csf('extention_no')];
        $dyeing_re_process_name=$dyeing_re_process[$row[csf('dyeing_re_process')]];
    }
    //echo $po_break_down_id.'DFD';

    //$booking_no_cond=$booking_no;
    if($booking_no!='') $booking_no_cond="and b.booking_no in('$booking_no')";else $booking_no_cond="and b.booking_no in('0')";

    $without_booking_array = "select a.buyer_id,b.booking_no,b.booking_type from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and b.booking_type=1  and b.status_active=1 and b.is_deleted=0 $booking_no_cond";
    foreach($without_booking_array as $row)
    {
        if($row[csf('booking_type')]==4)// Sample Fabric
        {
            $main_fab_booking='Without Sample Booking';
        }
        $buyer_id_arr[$row[csf('booking_no')]]=$row[csf('buyer_id')];
    }
    $booking_array = "select  a.buyer_id,a.entry_form,b.po_break_down_id,b.job_no,b.is_short,b.booking_no,b.booking_type from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no  and b.booking_type=1  and b.status_active=1 and b.is_deleted=0 $booking_no_cond group by a.entry_form,a.buyer_id,b.po_break_down_id,b.job_no,b.is_short,b.booking_no,b.booking_type";
    $result_booking=sql_select($booking_array);

    foreach($result_booking as $row)
    {
        if($row[csf('booking_type')]==1)// Fabric
        {
            if($row[csf('is_short')]==1)
            {
                $main_fab_booking='Short Booking';
            }
            else if($row[csf('is_short')]==2)
            {
                $main_fab_booking='Main Booking';
            }
            else if($row[csf('is_short')]==2 && $row[csf('entry_form')]==108 )
            {
                $main_fab_booking='Partial Booking';
            }
        }
        else if($row[csf('booking_type')]==4)// Sample Fabric
        {
            $main_fab_booking='Sample Booking';
        }
        $buyer_id_arr[$row[csf('booking_no')]]=$row[csf('buyer_id')];
        //$po_break_down_id.=$row[csf('po_break_down_id')].',';

    }
    $po_id=rtrim($po_break_down_id,',');
    $book_buyer_name=$buyer_library[$buyer_id_arr[$booking_no]];

    $job_no = '';
    $style_ref_no = '';
    if($entry_form==36)
    {
        $po_data = sql_select("select b.order_no, b.cust_style_ref, c.subcon_job as job_no, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and b.id in(" . $po_id . ") group by b.order_no, b.cust_style_ref, c.subcon_job, c.party_id");
        foreach($po_data as $po_d)
        {
            $book_buyer_name.=$buyer_library[$po_d[csf('party_id')]].",";
        }
        $book_buyer_name=chop($book_buyer_name,",");
    }
    else
    {
        $po_data = sql_select("select  c.job_no,c.style_ref_no, c.season_buyer_wise, c.buyer_name from  wo_po_break_down b, wo_po_details_master c where  b.job_no_mst=c.job_no and b.status_active=1 and b.is_deleted=0 and b.id in(" . $po_id . ") group by  c.job_no,c.style_ref_no, c.season_buyer_wise, c.buyer_name");

        if( empty($po_id))
        {

            $sql_style=sql_select("select style_ref_no,buyer_name from sample_development_mst where id in (select style_id from wo_non_ord_samp_booking_dtls where status_active=1 and booking_no ='$booking_no' group by style_id)");
            foreach ($sql_style as $row) {
                $style_ref_no.=$row[csf('style_ref_no')].',';
                $book_buyer_name.=$buyer_library[$row[csf('buyer_name')]].",";
            }
            $style_ref_no=chop($style_ref_no,",");
            $book_buyer_name=chop($book_buyer_name,",");
        }
        //echo "select  c.job_no,c.style_ref_no, c.buyer_name from  wo_po_break_down b, wo_po_details_master c where  b.job_no_mst=c.job_no and b.status_active=1 and b.is_deleted=0 and b.id in(" . $po_id . ") group by  c.job_no,c.style_ref_no, c.buyer_name";
    }
    foreach($po_data as $row)
    {
        $job_no .= $row[csf('job_no')] . ",";
        //$season .= $row[csf('season_buyer_wise')] . ",";
        if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
        //if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
    }

    if ($db_type == 0) {
        $sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
    } else if ($db_type == 2) {
        $sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, listagg((cast(a.yarn_lot as varchar2(4000))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(a.brand_id,',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(4000))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id";
    }

    $yarn_dtls_array = array();
    $result_sql_yarn_dtls = sql_select($sql_yarn_dtls);
    foreach ($result_sql_yarn_dtls as $row) {
        $yarn_lot = implode(", ", array_unique(explode(",", $row[csf('yarn_lot')])));
        $brand_id = array_unique(explode(",", $row[csf('brand_id')]));
        $brand_name = "";
        foreach ($brand_id as $val) {
            if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= ", " . $brand_arr[$val];
        }

        $yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
        $count_name = "";
        foreach ($yarn_count as $val) {
            if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
        }
        $yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['yarn_lot'] = $yarn_lot;
        $yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['brand_name'] = $brand_name;
        $yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['count_name'] = $count_name;
    }
    //var_dump($yarn_dtls_array);
    //$po_no = implode(", ", array_unique(explode(",", substr($po_no, 0, -1))));
    $job_no = implode(", ", array_unique(explode(",", substr($job_no, 0, -1))));
    //$season = implode(",", array_unique(explode(",", substr($season, 0, -1))));





    $color_id = array_unique(explode(",", $batchdata_array[0][csf('color_id')]));
    $color_name = '';
    foreach ($color_id as $color) {
        $color_name .= $color_arr[$color] . ",";
    }
    $color_name = substr($color_name, 0, -1);
    //var_dump($recipe_color_arr);
    $nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
    $group_id=$nameArray[0][csf('group_id')];
    $recipe_id=$data_array[0][csf("recipe_id")];

    $k=0;
    $copy_no=array(1,2); //for Dynamic Copy here
    foreach($copy_no as $cid)
    {
        ?>
        <div data-print=section class="print6_data">
            <div style="width:1020px;">
                <table width="1020" cellspacing="0" align="center" id="table_<? echo $cid;?>">
                    <tr>
                        <td width="500" align="center" style="font-size:xx-large;">
                            <strong><? echo $company_library[$data[0]];//$company_library[$data[0]]; ?></strong></td>
                        <td width="400" id="barcode_img_id_<? echo $cid;?>" class="barcode_img">&nbsp;</td>
                        <td width="130" id="number_of_copy">
                            <?
                            if($cid==1){
                                echo "<h3>Store Copy</h3>";
                            }
                            else if($cid==2){
                                echo "<h3>Dyeing Copy</h3>";
                            }
                            ?>
                        </td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="6" align="center" style="font-size:14px">
                            <?
                            /*
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
                            }*/
                            ?>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="6" align="center" style="font-size: large">
                            <strong><u><? echo $data[2]; ?>	Report</u></strong>
                        </td>
                        <td></td>
                    </tr>
                </table>
                <table width="980" cellspacing="0" align="center">
                    <tr>
                        <td width="90"><strong>Req. ID </strong></td>
                        <td width="160px"><? echo $dataArray[0][csf('requ_no')]; ?></td>
                        <td width="100"><strong>Req. Date</strong></td>
                        <td width="190px"><? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
                        <td width="90"><strong>Buyer</strong></td>
                        <td width="160px"><? echo $book_buyer_name; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Batch No</strong></td>
                        <td><? echo $batchdata_array[0][csf('batch_no')]; ?></td>
                        <td><strong>Booking Type</strong></td>
                        <td><? echo $main_fab_booking; ?></td>
                        <td><strong>Booking No</strong></td>
                        <td><? echo $booking_no; ?></td>

                    </tr>
                    <tr>
                        <td><strong>Color</strong></td>
                        <td><? echo $color_name; ?></td>
                        <td><strong>Batch Weight</strong></td>
                        <td><? $tot_bathc_weight=$batch_weight + $batchdata_array[0][csf('batch_weight')];echo number_format($tot_bathc_weight,2,'.',''); ?></td>
                        <td><strong>Exten. No</strong></td>
                        <td><? echo $extention_no; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Issue Basis</strong></td>
                        <td><? echo $receive_basis_arr[$dataArray[0][csf('requisition_basis')]]; ?></td>
                        <td><strong>Recipe No</strong></td>
                        <td><? echo $data_array[0][csf("recipe_id")]; ?></td>
                        <td><strong>Machine No</strong></td>
                        <td>
                            <?
                            $machine_data = sql_select("select machine_no, floor_id from lib_machine_name where id='" . $dataArray[0][csf("machine_id")] . "'");
                            echo $machine_data[0][csf('machine_no')];
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td   width="120"><strong>Dyeing Reprocess</strong></td>
                        <td><? echo $dyeing_re_process_name; ?></td>
                        <td><strong>Method</strong></td>
                        <td><? echo $dyeing_method[$dataArray[0][csf("method")]];; ?></td>
                        <td><strong>Style Ref.</strong></td>
                        <td>
                            <?

                            echo implode(",", array_unique(explode(",", $style_ref_no)));
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Floor Name</strong></td>
                        <td>
                            <?
                            $floor_name = return_field_value("floor_name", "lib_prod_floor", "id='" . $machine_data[0][csf('floor_id')] . "'");
                            echo $floor_name;
                            ?>
                        </td>
                        <td><strong>Job No</strong></td>
                        <td><? echo $job_no;  ?></td>
                        <td><strong>Season</strong></td>
                        <td><?php echo $season_arr[$po_data[0][csf('season_buyer_wise')]]; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Color Range</strong></td>
                        <td colspan="5">
                            <?
                            $color_range_text='';

                            $color_range_ids=$data_array[0][csf("color_range")];
                            $color_range_id=explode(",", $color_range_ids);
                            $color_range_id=array_unique($color_range_id);
                            for($ci=0;$ci<count($color_range_id);$ci++)
                            {
                                if($ci>0) $color_range_text.=",";
                                $color_range_text.=$color_range[$color_range_id[$ci]];
                            }

                            echo $color_range_text;
                            ?>
                        </td>

                    </tr>

                </table>
                <br>
                <?
                $batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
                $j = 1;
                $entryForm = $entry_form_arr[$batch_id_qry[0]];
                ?>
                <table width="1020" cellspacing="0" align="left" class="rpt_table" border="1" style="font-size:16px" rules="all">
                    <thead bgcolor="#dddddd" align="center">

                    <tr bgcolor="#CCCCFF">
                        <th colspan="5" align="center"><strong>Fabrication</strong></th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="80">Dia/ W. Type</th>

                        <th width="300">Constrution & Composition</th>
                        <th width="70">Gsm</th>
                        <th width="70">Dia</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?
                    foreach ($batch_id_qry as $b_id) {
                        $batch_query = "Select  po_id, prod_id, item_description, width_dia_type, count(roll_no) as gmts_qty, sum(batch_qnty) batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by po_id, prod_id, item_description, width_dia_type";
                        $result_batch_query = sql_select($batch_query);
                        foreach ($result_batch_query as $rows) {
                            if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                            $fabrication_full = $rows[csf("item_description")];
                            $fabrication = explode(',', $fabrication_full);
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>">
                                <td align="center"><? echo $j; ?></td>
                                <td align="center"><b><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></b></td>
                                <td align="left"><b><? echo $fabrication[0] . ", " . $fabrication[1]; ?></b></td>
                                <td align="center"><? echo $fabrication[2]; ?></td>
                                <td align="center"><? echo $fabrication[3]; ?></td>
                            </tr>
                            <?
                            $j++;
                        }
                    }
                    ?>
                    </tbody>
                </table>
                <div style="width:1020px; margin-top:10px">
                    <table align="left" cellspacing="0" width="1020" border="1" rules="all" style="font-size:16px; margin-top:25px;" class="rpt_table">
                        <thead bgcolor="#dddddd" align="center">
                        <tr bgcolor="#CCCCFF">
                            <th colspan="13" align="center"><strong>Dyes And Chemical Issue Requisition</strong>
                            </th>
                        </tr>
                        <?
                        $sql_rec = "select a.id,a.item_group_id,b.mst_id, b.total_liquor,b.liquor_ratio,b.sub_process_id as sub_process_id,b.prod_id,b.dose_base,b.adj_type,b.comments,b.process_remark from pro_recipe_entry_dtls b,product_details_master a where a.id=b.prod_id and b.mst_id in($recipe_id) and a.item_category_id in(5,6,7,23) and b.status_active=1 and b.is_deleted=0";
                        $nameArray = sql_select($sql_rec);
                        $process_array = array();
                        $process_array_remark = array();
                        foreach ($nameArray as $row) {

                            if($process_array_liquor_check[$row[csf("mst_id")]][$row[csf("sub_process_id")]]=="")
                            {
                                $process_array_liquor[$row[csf("sub_process_id")]]['total_liquor'] += $row[csf("total_liquor")];
                                $process_array_liquor_check[$row[csf("mst_id")]][$row[csf("sub_process_id")]]=888;
                            }
                            $process_array_liquor[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
                            //$process_array_liquor[$row[csf("sub_process_id")]]['comments'] = $row[csf("comments")];
                            $process_array[$row[csf("sub_process_id")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"] = $row[csf("comments")];

                        }
                        $sql_rec_remark = "select b.mst_id,b.sub_process_id as sub_process_id,b.comments,b.process_remark,b.total_liquor,b.liquor_ratio from pro_recipe_entry_dtls b  where  b.mst_id in($recipe_id) and b.sub_process_id in($subprocessforwashin) and b.status_active=1 and b.is_deleted=0";
                        $nameArray_re = sql_select($sql_rec_remark);
                        foreach ($nameArray_re as $row) {
                            if($process_array_liquor_check[$row[csf("mst_id")]][$row[csf("sub_process_id")]]=="")
                            {
                                $process_array_liquor[$row[csf("sub_process_id")]]['total_liquor'] += $row[csf("total_liquor")];
                                $process_array_liquor_check[$row[csf("mst_id")]][$row[csf("sub_process_id")]]=999;
                            }
                            $process_array_liquor[$row[csf("sub_process_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
                            $process_array_remark[$row[csf("sub_process_id")]]["wash_comments"] = $row[csf("process_remark")];
                            //$process_array_remark[$row[csf("sub_process_id")]]["comments"] = $row[csf("comments")];
                        }

                        $group_arr = return_library_array("select id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0", 'id', 'item_name');
                        if ($db_type == 0) {
                            $item_lot_arr = return_library_array("select b.prod_id,group_concat(b.item_lot) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot");
                        } else {
                            $item_lot_arr = return_library_array("select b.prod_id,listagg((cast(b.item_lot as varchar2(500))),',') within group (order by b.prod_id) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot");
                        }
                        /*$sql_dtls = "select a.requ_no, a.batch_id, a.recipe_id, b.id,b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit, c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id
                        from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
                        where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id=$data[1] order by b.id, b.seq_no";*/
                        $sql_dtls = "(select a.requ_no, a.batch_id, a.recipe_id, b.id,b.sub_seq,b.sub_process as sub_process,b.seq_no, b.item_category, b.dose_base, b.item_lot, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty,c.current_stock, b.req_qny_edit, c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id
								from dyes_chem_issue_requ_mst a, dyes_chem_issue_requ_dtls b, product_details_master c
								where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and  b.ratio>0  and c.stock_value>0 and c.item_category_id in (5,6,7,23) and a.id=$data[1])
								union
								(select a.requ_no, a.batch_id, a.recipe_id, b.id,b.sub_seq,b.sub_process as sub_process,b.seq_no, b.item_category, b.dose_base, b.item_lot, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, null as current_stock, b.req_qny_edit, null as item_description, null as  item_group_id,  null as  sub_group_name,null as  item_size, null as  unit_of_measure,null as  avg_rate_per_unit,null as prod_id
								from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b
								where a.id=b.mst_id and b.sub_process in ($subprocessforwashin) and a.id=$data[1])
								order by id";
                        //echo $sql_dtls;//die;

                        $sql_result = sql_select($sql_dtls);

                        $sub_process_array = array();
                        $sub_process_tot_rec_array = array();
                        $sub_process_tot_req_array = array();
                        $sub_process_tot_value_array = array();

                        foreach ($sql_result as $row) {
                            $sub_process_tot_rec_array[$row[csf("sub_process")]] += $row[csf("recipe_qnty")];
                            $sub_process_tot_req_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
                            $sub_process_tot_value_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")] * $row[csf("avg_rate_per_unit")];
                        }

                        //var_dump($sub_process_tot_req_array);
                        $i = 1;
                        $k = 1;
                        $recipe_qnty_sum = 0;
                        $req_qny_edit_sum = 0;
                        $recipe_qnty = 0;
                        $req_qny_edit = 0;
                        $req_value_sum = 0;
                        $req_value_grand = 0;
                        $recipe_qnty_grand = 0;
                        $req_qny_edit_grand = 0;$total_cost=$grand_tot_ratio_sum=0;

                        foreach ($sql_result as $row)
                        {
                        $current_stock = $row[csf('current_stock')];
                        $current_stock_check=number_format($current_stock,7,'.','');

                        if($current_stock_check>0)
                        {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";
                        if (!in_array($row[csf("sub_process")], $sub_process_array))
                        {
                        $sub_process_array[] = $row[csf('sub_process')];
                        if ($k != 1) {
                            ?>
                            <tr bgcolor="#ffd4c4">
                                <td colspan="4" align="right"><strong>Total :</strong></td>
                                <td align="center"><?php echo number_format($tot_ratio_sum, 4, '.', ''); $total_issue_val+=$issue_val?></td>
                                <td>&nbsp;</td>

                                <td align="right"><?php echo number_format($req_qny_edit_sum, 4, '.', ''); ?></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td align="right"><?php echo number_format($tat_issue_value, 2, '.', ''); ?></td>
                            </tr>
                            <? 			$tat_issue_value=0;
                        }
                        $tot_ratio_sum = 0;
                        $req_qny_edit_sum = 0;
                        $req_value_sum = 0;
                        $k++;

                       // if ($row[csf("sub_process")] == 93 || $row[csf("sub_process")] == 94 || $row[csf("sub_process")] == 95 || $row[csf("sub_process")] == 96 || $row[csf("sub_process")] == 97 || $row[csf("sub_process")] == 98) {
						if(in_array($row[csf("sub_process")],$subprocessForWashArr))
						 {
                            $pro_remark = $process_array_remark[$row[csf("sub_process")]]["wash_comments"];
                        }
						 else {
                            $pro_remark = "";
                        }
                        if ($pro_remark != '') $pro_remark = "," . $pro_remark; else $pro_remark = "";
                        $total_liquor = 'Total liquor(ltr)' . ": " . number_format($process_array_liquor[$row[csf("sub_process")]]['total_liquor'],2,'.','');
                        $liquor_ratio = 'Liquor Ratio' . ": " . $process_array_liquor[$row[csf("sub_process")]]['liquor_ratio'];
                        $liquor_ratio_process=$process_array_liquor[$row[csf("sub_process")]]['liquor_ratio'];
                        $leveling_water=", Levelling Water(Ltr) ".$tot_bathc_weight*($liquor_ratio_process-1.5);
                        ?>

                        <tr>
                            <th width="30">SL</th>

                            <th width="150">Item Group</th>
                            <th width="150">Item Description</th>
                            <th width="70">Dyes Lot</th>
                            <th width="80">Dose Base</th>
                            <th width="70">Ratio</th>
                            <th width="80">Adj-1</th>
                            <th width="80">Requisition Qty</th>
                            <th width="60">KG</th>
                            <th width="50">GM</th>
                            <th width="50">MG</th>

                            <th width="100">Comments</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?
                        }

                        $req_qny_edit = explode(".", (string)$row[csf("req_qny_edit")]);
                        $iss_qnty_kg = $req_qny_edit[0];
                        if ($iss_qnty_kg == "") $iss_qnty_kg = 0;
                        $iss_qnty_gm = substr($req_qny_edit[1], 0, 3);//$rem[0]; // floor($mg/1000);
                        $iss_qnty_mg = substr($req_qny_edit[1], 3, 3);//$rem[1];//round($mg-$iss_qnty_gm*1000);
                        $iss_qnty_gm = str_pad($iss_qnty_gm, 3, "0", STR_PAD_RIGHT);
                        $iss_qnty_mg = str_pad($iss_qnty_mg, 3, "0", STR_PAD_RIGHT);
                        $comment = $process_array[$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"];
                        $iss_value = $row[csf("req_qny_edit")]*$row[csf("avg_rate_per_unit")];
                        $tat_issue_value +=$iss_value;
                        $avg_rate = $iss_value/$row[csf("req_qny_edit")];
                        $total_cost+=$row[csf("avg_rate_per_unit")]*$row[csf("req_qny_edit")];
                        ?>

                        <tr bgcolor="<? echo $bgcolor; ?>">
                            <td align="center"><? echo $i; ?></td>
                            <!--<td><b><? //echo $item_category[$row[csf("item_category")]];  //echo $row[csf("sub_process")];
                            ?></b></td>-->
                            <td><b><? echo $group_arr[$row[csf("item_group_id")]]; ?></b></td>
                            <td><b><? echo $row[csf("item_description")] . ' ' . $row[csf("item_size")]; ?></b></td>

                            <td><? echo $row[csf("item_lot")]; ?></td>

                            <td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
                            <td align="right"><? echo number_format($row[csf("ratio")], 5, '.', ''); ?></td>
                            <td align="center"><? echo $row[csf("adjust_percent")]; ?></td>
                            <td align="right" title="Avg Rate: <? echo $row[csf("avg_rate_per_unit")];?>" ><b><? echo number_format($row[csf("req_qny_edit")], 4, '.', ''); ?></b></td>
                            <td align="right"><? echo $iss_qnty_kg; ?></td>
                            <td align="right"><? echo $iss_qnty_gm; ?></td>
                            <td align="right"><? echo $iss_qnty_mg; ?></td>

                            <td align="center"><? echo $comment; ?></td>
                        </tr>

                        <? 			$i++;
                        $recipe_qnty_sum += $row[csf('recipe_qnty')];
                        $req_qny_edit_sum += $row[csf('req_qny_edit')];
                        $req_value_sum += $req_value;
                        $tot_ratio_sum += $row[csf("ratio")];
                        $grand_tot_ratio_sum += $row[csf("ratio")];

                        $recipe_qnty_grand += $row[csf('recipe_qnty')];
                        $req_qny_edit_grand += $row[csf('req_qny_edit')];
                        $req_value_grand += $req_value;
                        $cost_per_kg = $total_cost/$tot_bathc_weight;
                        }
                        }
                        foreach ($sub_process_tot_rec_array as $val_rec) {
                            $totval_rec = $val_rec;
                        }
                        foreach ($sub_process_tot_req_array as $val_req) {
                            $totval_req = $val_req;
                        }
                        foreach ($sub_process_tot_value_array as $req_value) {
                            $tot_req_value = $req_value;
                        }

                        //$recipe_qnty_grand +=$val_rec;
                        //$req_qny_edit_grand +=$val_req;
                        //$req_value_grand +=$req_value;
                        ?>
                        </tbody>
                        <!-- <tfoot> -->
                        <tr bgcolor="#fffcc7">
                            <td colspan="5" align="right"><strong>Total :</strong></td>

                            <td align="right"><?php echo number_format($tot_ratio_sum, 5, '.', ''); ?></td>
                            <td align="center"><?php //echo number_format($tot_ratio_sum, 6, '.', ''); ?></td>
                            <td align="right"><?php echo number_format($totval_req, 4, '.', ''); ?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td align="right"><?php echo number_format($tat_issue_value, 4, '.', ''); $grand_total_iss_value +=$total_issue_value; ?></td>

                        </tr>
                        <tr bgcolor="#f5ffc7">
                            <td colspan="5" align="right"><strong> Grand Total :</strong></td>

                            <td align="right"><?php echo number_format($grand_tot_ratio_sum, 5, '.', ''); ?></td>
                            <td align="center"><?php //echo number_format($tot_ratio_sum, 6, '.', ''); ?></td>
                            <td align="right"><?php echo number_format($req_qny_edit_grand, 4, '.', ''); ?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>

                        </tr>
                        <tr bgcolor="#e8edd3">
                            <td colspan="5"  align="right"><strong> Total Cost :</strong></td>
                            <td align="right"><?php echo number_format($total_cost, 4, '.', ''); ?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>

                        </tr>
                        <tr bgcolor="#def1b1">
                            <td colspan="5"  align="right"><strong> Cost per KG :</strong></td>
                            <td align="right"><?php echo number_format($cost_per_kg, 2, '.', ''); ?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>


                        <!-- </tfoot> -->
                    </table>

                    <div class="signature_table_container">


                        <?
                        echo signature_table(15, $data[0], "1000px");
                        $requ_no=$dataArray[0][csf('requ_no')];
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?
        $k++;
        $grand_total_iss_value=0;$tat_issue_value=0;$total_issue_value=0;
    }
    ?>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
        function generateBarcode(valuess) {
            //alert(valuess);
            var value = valuess;//$("#barcodeValue").val();
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();
            var settings = {
                output: renderer,
                bgColor: '#FFFFFF',
                color: '#000000',
                barWidth: 1,
                barHeight: 70,
                moduleSize: 5,
                posX: 10,
                posY: 20,
                addQuietZone: 1
            };
            var barcode_numbers = $("[class*='barcode_img']").length;
            //alert(barcode_numbers);
            //$("#barcode_img_id_"+i).html('11');
            //for(var i=1; i<=barcode_numbers; i++){
            value = {code: value, rect: false};
            $("#barcode_img_id_1").show().barcode(value, btype, settings);
            $("#barcode_img_id_2").show().barcode(value, btype, settings);
            //}

        }
        generateBarcode('<? echo $requ_no; ?>');
    </script>

    <script type="text/javascript">
        var staticHeight = 0;
        var pageHeight = 900;
        $('.print6_data').each(function() {
            staticHeight += $(this).filter(':visible').outerHeight(true);
            //console.log(staticHeight)
            if (staticHeight > pageHeight) {//<p style="page-break-after:always;"></p>
                $(this).after( '<p style="page-break-after:always;"></p>');
                staticHeight = 0;
                $('.print6_data').addClass("devider").css({
                    'margin-top':"80px",
                    'margin-left':"50px"
                });
            }
            else{
                /*$(this).after( '<hr class="hr" style="border-top: dotted 2px #000">');*/
                $(this).after( '<p style="page-break-after:always;"></p>');
                $(this).css({
                    /*'width':"950px",*/
                    'margin-left':"50px",
                    'margin-top': "50px"
                });
                $('.print6_data').addClass("devider");
            }

        });
        $(".signature_table_container").css({
            'position' : "relative",
            'top' : "25px",
            'height' : "190px",
            'overflow' : "hidden",
            'width' : "100%",
            'clear' : "both",
            'vertical-align' : "baseline"
        });
    </script>
    <style type="text/css">
        /* .devider:nth-of-type(2){
            margin-top: 160px;
            margin-left: 50px;
        } */
        @media print{

            .devider{
                margin-top:3.2333cm;
            }
        }
    </style>
    <?
    exit();
}

if ($action == "chemical_dyes_issue_requisition_without_rate_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	//print_r ($data);

	$sql = "select a.id, a.requ_no, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id from dyes_chem_issue_requ_mst a where a.id='$data[1]' and a.company_id='$data[0]'";
	$dataArray = sql_select($sql);

	$recipe_id = $dataArray[0][csf('recipe_id')];

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$entry_form_arr = return_library_array("select id, entry_form from pro_batch_create_mst", "id", "entry_form");
	//$order_id_arr=return_library_array( "select id, booking_id from  pro_recipe_entry_mst", "id", "booking_id");
	//$order_no_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
	//$job_no_arr=return_library_array( "select id, job_no from wo_booking_mst", "id", "job_no");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");

	$batch_weight = 0;
	if ($db_type == 0) {
		$data_array = sql_select("select group_concat(buyer_id) as buyer_id,group_concat(id) as recipe_id, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor, group_concat(concat_ws(':',batch_ratio,liquor_ratio) order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, group_concat(case when entry_form<>60 then batch_id end) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
	} else {
		$data_array = sql_select("select listagg(buyer_id,',') within group (order by buyer_id) as buyer_id,listagg(id,',') within group (order by id) as recipe_id, listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor, listagg(batch_ratio || ':' || liquor_ratio,',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(case when entry_form<>60 then batch_id end,',') within group (order by batch_id) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
	}
	$batch_weight = $data_array[0][csf("batch_weight")];
	$batch_id = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id")])));

	$booking_no_sql="select  booking_no,booking_without_order from pro_batch_create_mst where id in($batch_id) and is_deleted=0 and status_active=1 group by booking_no,booking_without_order";
	$booking_no_arr=sql_select($booking_no_sql);
	foreach ($booking_no_arr as $row){
        $booking_arr[$row[csf("booking_no")]]=$row[csf("booking_no")];
		 $booking_no=$row[csf("booking_no")];
		 $booking_without_order=$row[csf("booking_without_order")];
    }
 // echo "<pre>";
    //print($booking_without_order);
	//    print_r($booking_no);
	$batch_id_rec_main = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id_rec_main")])));
	if ($batch_id_rec_main == "") $batch_id_rec_main = 0;

	if ($db_type == 0) {
		$batchdata_array = sql_select("select group_concat(batch_no) as batch_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(distinct color_id) as color_id from pro_batch_create_mst where id in($batch_id)");
	} else {
		$batchdata_array = sql_select("select listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, listagg(color_id ,',') within group (order by id) as color_id, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($batch_id)");
	}

	$po_no = '';
	$job_no = '';$prod_ids = '';$po_ids = '';
	$buyer_name = '';
	$style_ref_no = '';
	foreach (explode(",", $data_array[0][csf("batch_id")]) as $b_id) {
		if ($entry_form_arr[$b_id] == 36) {
			//$po_data=sql_select("select distinct b.order_no, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(".$b_id.") ");
			$po_data = sql_select("select distinct b.order_no,b.id as po_id,a.prod_id,b.cust_style_ref, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ");
			foreach ($po_data as $row) {
				$po_no .= $row[csf('order_no')] . ",";
				$job_no .= $row[csf('subcon_job')] . ",";
				$prod_ids .= $row[csf('prod_id')] . ",";
				if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
				$buyer_name .= $buyer_library[$row[csf('party_id')]] . ",";
			}
		} else {
			//$po_data=sql_select("select distinct b.po_number, c.job_no, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(".$b_id.") ");
			$po_data = sql_select("select distinct b.po_number,b.id as po_id,a.prod_id, c.job_no,c.style_ref_no, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ");
			foreach ($po_data as $row) {
				$po_no .= $row[csf('po_number')] . ",";
				$job_no .= $row[csf('job_no')] . ",";
				$prod_ids .= $row[csf('prod_id')] . ",";

				if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
				//$buyer_name.=$buyer_library[$row[csf('buyer_name')]].",";
			}
			foreach (explode(",", $data_array[0][csf("buyer_id")]) as $buyer_id) {
				$buyer_name .= $buyer_library[$buyer_id] . ",";
			}

		}
	}
	$all_prod_ids=rtrim($prod_ids,',');
	$all_prod_ids = implode(",", array_unique(explode(",", $all_prod_ids)));
	if($all_prod_ids!='') $all_prod_ids=$all_prod_ids;else $all_prod_ids=0;

	/*
	if ($db_type == 0) {
		$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, a.yarn_lot as yarn_lot, a.brand_id as brand_id, a.yarn_count as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.prod_id in($all_prod_ids) group by b.po_breakdown_id, b.prod_id
		$ordr_without
		 ";
	} else if ($db_type == 2) {
		$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, a.yarn_lot  as yarn_lot, a.brand_id as brand_id,a.yarn_count as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.prod_id in($all_prod_ids) group by b.po_breakdown_id, b.prod_id, a.yarn_lot, a.brand_id,a.yarn_count
		$ordr_without
		";
	}*/
	$sql_yarn_dtls = "select  c.po_breakdown_id as po_id,d.yarn_lot as yarn_lot,d.yarn_count as yarn_count,d.brand_id as brand_id,d.prod_id
	from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
	where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id
 	and a.id in($batch_id) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22) group by  c.po_breakdown_id,d.yarn_lot,d.yarn_count,d.brand_id,d.prod_id";
	//echo $sql_yarn_dtls;
	$yarn_dtls_array = array();
	$result_sql_yarn_dtls = sql_select($sql_yarn_dtls);
	foreach ($result_sql_yarn_dtls as $row) {
		/*$yarn_lot = implode(", ", array_unique(explode(",", $row[csf('yarn_lot')])));
		$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
		$brand_name = "";
		foreach ($brand_id as $val) {
			if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= ", " . $brand_arr[$val];
		}*/

		$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
		$count_name = "";
		foreach ($yarn_count as $val) {
			if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
		}

			//echo $yarn_lot.'DD';
			$yarn_dtls_array[$row[csf('prod_id')]]['yarn_lot'] .=$row[csf('yarn_lot')].',';
			$yarn_dtls_array[$row[csf('prod_id')]]['brand_name'] .= $brand_arr[$row[csf('brand_id')]].',';
			$yarn_dtls_array[$row[csf('prod_id')]]['count_name'] .= $count_name.',';


	}
	//var_dump($yarn_dtls_array);
	$po_no = implode(", ", array_unique(explode(",", substr($po_no, 0, -1))));
	$job_no = implode(", ", array_unique(explode(",", substr($job_no, 0, -1))));
	$buyer_name = implode(",", array_unique(explode(",", substr($buyer_name, 0, -1))));

	$color_id = array_unique(explode(",", $batchdata_array[0][csf('color_id')]));
	$color_name = '';
	foreach ($color_id as $color) {
		$color_name .= $color_arr[$color] . ",";
	}
	$color_name = substr($color_name, 0, -1);
	//var_dump($recipe_color_arr);
	$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
	$group_id=$nameArray[0][csf('group_id')];
	$recipe_id=$data_array[0][csf("recipe_id")];
	$recipe_arr=sql_select("select a.recipe_type,a.working_company_id,a.company_id, b.batch_weight from pro_recipe_entry_mst a, pro_batch_create_mst b where a.batch_id=b.id and a.id=$recipe_id");
	$owner_company_id=$recipe_arr[0][csf('company_id')];
	$recipe_type=$recipe_arr[0][csf('recipe_type')];
	//echo $recipe_type.'DD';
	?>
	<div style="width:1000px;">
		<table width="1000" cellspacing="0" align="center">
			<tr>
				<td colspan="10" align="center" style="font-size:xx-large">
					<strong><? echo 'Group Name :'.$group_library[$group_id].'<br/>'.'Working Company : '.$company_library[$data[0]];//$company_library[$data[0]]; ?></strong></td>
				</tr>
				<tr class="form_caption">
					<td colspan="6" align="center" style="font-size:14px">
						<?

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
					<td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo 'Owner Company : '.$company_library[$owner_company_id].'<br>'.$data[2]; ?>
						Report</u></strong></td>
					</tr>
				</table>
				<table width="950" cellspacing="0" align="center">
					<tr>
						<td width="90"><strong>Req. ID </strong></td>
						<td width="160px"><? echo $dataArray[0][csf('requ_no')]; ?></td>
						<td width="100"><strong>Req. Date</strong></td>
						<td width="160px"><? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
						<td width="90"><strong>Buyer</strong></td>
						<td width="160px"><? echo implode(",", array_unique(explode(",", $buyer_name))); ?></td>
					</tr>
					<tr>
						<td><strong>Order No</strong></td>
						<td><? echo $po_no; ?></td>
						<td><strong>Job No</strong></td>
						<td><? echo $job_no; ?></td>
						<td><strong>Issue Basis</strong></td>
						<td><? echo $receive_basis_arr[$dataArray[0][csf('requisition_basis')]]; ?></td>
					</tr>
					<tr>
						<td><strong>Batch No</strong></td>
						<td><? echo $batchdata_array[0][csf('batch_no')]; ?></td>
						<td><strong>Batch Weight</strong></td>
						<td><? $tot_bathc_weight=$batch_weight + $batchdata_array[0][csf('batch_weight')];echo $tot_bathc_weight; ?></td>
						<td><strong>Color</strong></td>
						<td><? echo $color_name; ?></td>
					</tr>
					<tr>
						<td><strong>Recipe No</strong></td>
						<td><? echo $data_array[0][csf("recipe_id")]; ?></td>
                <!--<td><strong>Total Liq.(ltr)</strong></td><td><? echo $data_array[0][csf("total_liquor")]; ?></td>
                <td>Liquor Ratio</td><td><? echo $data_array[0][csf("ratio")]; ?></td>-->
                <td><strong>Machine No</strong></td>
                <td>
                	<?
                	$machine_data = sql_select("select machine_no, floor_id from lib_machine_name where id='" . $dataArray[0][csf("machine_id")] . "'");
                	echo $machine_data[0][csf('machine_no')];
                	?>
                </td>
                <td><strong>Floor Name</strong></td>
                <td colspan="">
                	<?
                	$floor_name = return_field_value("floor_name", "lib_prod_floor", "id='" . $machine_data[0][csf('floor_id')] . "'");
                	echo $floor_name;
                	?>
                </td>
            </tr>
            <tr>

            	<td>Method</td>
            	<td><? echo $dyeing_method[$dataArray[0][csf("method")]]; ?></td>
            	<td><strong>Style Ref.</strong></td>
            	<td><p>
            		<?

            		echo implode(",", array_unique(explode(",", $style_ref_no)));
            		?>
            	</p>
            </td>
                <td><strong>Booking No</strong></td>
                <td><p>
                        <?
                           /* $fic='';
                            foreach ($booking_no as $item) {
                                if($fic==''){
                                    $fic=$item;
                                }else{
                                    $fic=",".$item;
                                }

                            }
                            echo $fic;*/
							 echo implode(',',$booking_arr);

                        ?>
                    </p>
                </td>
        </tr>

    </table>
    <br>
    <?
    $batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
    $j = 1;
    $entryForm = $entry_form_arr[$batch_id_qry[0]];
    ?>
    <table width="1000" cellspacing="0" align="center" class="rpt_table" border="1" rules="all">
    	<thead bgcolor="#dddddd" align="center">
    		<?
    		if ($entryForm == 0) {
    			?>
    			<tr bgcolor="#CCCCFF">
    				<th colspan="8" align="center"><strong>Fabrication</strong></th>
    			</tr>
    			<tr>
    				<th width="30">SL</th>
    				<th width="100">Dia/ W. Type</th>
    				<th width="100">Yarn Lot</th>
    				<th width="100">Brand</th>
    				<th width="100">Count</th>
    				<th width="300">Constrution & Composition</th>
    				<th width="70">Gsm</th>
    				<th width="70">Dia</th>
    			</tr>
    			<?
    		} else {
    			?>
    			<tr bgcolor="#CCCCFF">
    				<th colspan="4" align="center"><strong>Fabrication</strong></th>
    			</tr>
    			<tr>
    				<th width="50">SL</th>
    				<th width="350">Gmts. Item</th>
    				<th width="110">Gmts. Qty</th>
    				<th>Batch Qty.</th>
    			</tr>
    			<?
    		}
    		?>
    	</thead>
    	<tbody>
    		<?
    		foreach ($batch_id_qry as $b_id) {
    			$batch_query = "Select  po_id, prod_id, item_description, width_dia_type, count(roll_no) as gmts_qty, sum(batch_qnty) batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by po_id, prod_id, item_description, width_dia_type";
    			$result_batch_query = sql_select($batch_query);
    			foreach ($result_batch_query as $rows) {
    				if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
    				$fabrication_full = $rows[csf("item_description")];
    				$fabrication = explode(',', $fabrication_full);

					$yarn_lot=rtrim($yarn_dtls_array[$rows[csf('prod_id')]]['yarn_lot'],',');
					$brand_name=rtrim($yarn_dtls_array[$rows[csf('prod_id')]]['brand_name'],',');
					$count_name=rtrim($yarn_dtls_array[$rows[csf('prod_id')]]['count_name'],',');
					//echo $entry_form_arr[$b_id].'DDD';
    				if ($entry_form_arr[$b_id] == 36) {

    					?>
    					<tr bgcolor="<? echo $bgcolor; ?>">
    						<td align="center"><? echo $j; ?></td>
    						<td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
                            <td align="center"><? echo implode(", ", array_unique(explode(",", $yarn_lot)));
                            	?></td>
                            <td align="center"><? echo implode(", ", array_unique(explode(",", $brand_name)));
                            	?></td>
                            <td align="center"><? echo implode(", ", array_unique(explode(",", $count_name)));
                            	?></td>
                            	<td align="left"><? echo $fabrication[0] ?></td>
                            	<td align="center"><? echo $fabrication[1]; ?></td>
                            	<td align="center"><? echo $fabrication[3]; ?></td>
                            </tr>
                            <?
                        } else if ($entry_form_arr[$b_id] == 74) {
                        	?>
                        	<tr bgcolor="<? echo $bgcolor; ?>">
                        		<td align="center"><? echo $j; ?></td>
                        		<td align="left"><? echo $garments_item[$rows[csf('prod_id')]]; ?></td>
                        		<td align="right"><? echo $rows[csf('gmts_qty')]; ?></td>
                        		<td align="right"><? echo number_format($rows[csf('batch_qnty')], 2); ?></td>
                        	</tr>
                        	<?
                        } else {
                        	?>
                        	<tr bgcolor="<? echo $bgcolor; ?>">
                        		<td align="center"><? echo $j; ?></td>
                        		<td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
                        		<td width="100" align="left"><p style="word-wrap:break-word;"><? echo implode(", ", array_unique(explode(",", $yarn_lot))); ?></td>
                        		<td align="left"><? echo implode(", ", array_unique(explode(",", $brand_name))); ?></td>
                        		<td align="left"><? echo implode(", ", array_unique(explode(",", $count_name))); ?></td>
                        		<td align="left"><? echo $fabrication[0] . ", " . $fabrication[1]; ?></td>
                        		<td align="center"><? echo $fabrication[2]; ?></td>
                        		<td align="center"><? echo $fabrication[3]; ?></td>
                        	</tr>
                        	<?
                        }
                        $j++;
                    }
                }
                ?>
            </tbody>
        </table>
        <div style="width:1000px; margin-top:10px">
        	<table align="right" cellspacing="0" width="1000" border="1" rules="all" class="rpt_table">
        		<thead bgcolor="#dddddd" align="center">
        			<tr bgcolor="#CCCCFF">
        				<th colspan="16" align="center"><strong>Dyes And Chemical Issue Requisition Without Rate</strong>
        				</th>
        			</tr>

        			<?
        			$sql_rec = "select a.id,a.item_group_id, b.total_liquor,b.liquor_ratio,b.sub_process_id as sub_process_id,b.prod_id,b.dose_base,b.adj_type,b.comments,b.process_remark from pro_recipe_entry_dtls b,product_details_master a where a.id=b.prod_id and b.mst_id in($recipe_id) and a.item_category_id in(5,6,7,23) and b.status_active=1 and b.is_deleted=0";
        			$nameArray = sql_select($sql_rec);
        			$process_array = array();
        			$process_array_remark = array();
        			foreach ($nameArray as $row) {

        				$process_array_liquor[$row[csf("sub_process_id")]][$row[csf("prod_id")]]['total_liquor'] = $row[csf("total_liquor")];
        				$process_array_liquor[$row[csf("sub_process_id")]][$row[csf("prod_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
        				$process_array[$row[csf("sub_process_id")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"] = $row[csf("comments")];

        			}

        			$sql_rec_remark = "select b.sub_process_id as sub_process_id,b.comments,b.process_remark,b.total_liquor,b.liquor_ratio from pro_recipe_entry_dtls b  where  b.mst_id in($recipe_id) and b.sub_process_id in($subprocessforwashin) and b.status_active=1 and b.is_deleted=0";
        			$nameArray_re = sql_select($sql_rec_remark);
        			foreach ($nameArray_re as $row) {
        				$process_array_liquor[$row[csf("sub_process_id")]][$row[csf("prod_id")]]['total_liquor'] += $row[csf("total_liquor")];
        				$process_array_liquor[$row[csf("sub_process_id")]][$row[csf("prod_id")]]['liquor_ratio'] += $row[csf("liquor_ratio")];
        				$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"] = $row[csf("process_remark")];
        			}

        			$group_arr = return_library_array("select id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0", 'id', 'item_name');
        			//if ($db_type == 0) {
        				//$item_lot_arr = return_library_array("select b.prod_id,group_concat(b.item_lot) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot");
        			//} else {
        				//$item_lot_arr = return_library_array("select b.prod_id,listagg((cast(b.item_lot as varchar2(500))),',') within group (order by b.prod_id) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot");
        			//}
				/*$sql_dtls = "select a.requ_no, a.batch_id, a.recipe_id, b.id,
		b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
	  	c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id
	    from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
	    where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id=$data[1] order by b.id, b.seq_no";*/
	    $sql_dtls = "(select a.requ_no, a.batch_id, a.recipe_id, b.id, b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit, c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit, c.id as prod_id, b.item_lot
	    from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
	    where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and c.stock_value>0 and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id=$data[1])
	    union
	    (
	    select a.requ_no, a.batch_id, a.recipe_id, b.id, b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit, null as item_description, null as  item_group_id, null as sub_group_name, null as item_size, null as  unit_of_measure,null as avg_rate_per_unit, null as prod_id, b.item_lot
	    from dyes_chem_issue_requ_mst a, dyes_chem_issue_requ_dtls b
	    where a.id=b.mst_id and b.sub_process in ($subprocessforwashin) and a.id=$data[1]
	    )
	    order by id";
				// echo $sql_dtls;//die;

	    $sql_result = sql_select($sql_dtls);

	    $sub_process_array = array();
	    $sub_process_tot_rec_array = array();
	    $sub_process_tot_req_array = array();
	    $sub_process_tot_value_array = array();

	    foreach ($sql_result as $row) {
	    	$sub_process_tot_rec_array[$row[csf("sub_process")]] += $row[csf("recipe_qnty")];
	    	$sub_process_tot_req_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
	    	$sub_process_tot_value_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")] * $row[csf("avg_rate_per_unit")];
	    }

				//var_dump($sub_process_tot_req_array);
	    $i = 1;
	    $k = 1;
	    $recipe_qnty_sum = 0;
	    $req_qny_edit_sum = 0;
	    $recipe_qnty = 0;
	    $req_qny_edit = 0;
	    $req_value_sum = 0;
	    $req_value_grand = 0;
	    $recipe_qnty_grand = 0;
	    $req_qny_edit_grand = 0;

	    foreach ($sql_result as $row)
	    {
	    	if ($i % 2 == 0)
	    		$bgcolor = "#E9F3FF";
	    	else
	    		$bgcolor = "#FFFFFF";
	    	if (!in_array($row[csf("sub_process")], $sub_process_array))
	    	{
	    		$sub_process_array[] = $row[csf('sub_process')];
	    		if ($k != 1) {
	    			?>
	    			<tr>
	    				<td colspan="7" align="right"><strong>Total :</strong></td>
	    				<td align="right"><?php echo number_format($recipe_qnty_sum, 6, '.', ''); ?></td>
	    				<td>&nbsp;</td>
	    				<td>&nbsp;</td>
	    				<td>&nbsp;</td>
	    				<td align="right"><?php echo number_format($req_qny_edit_sum, 6, '.', ''); ?></td>
	    				<td>&nbsp;</td>
	    				<td>&nbsp;</td>
	    				<td>&nbsp;</td>
	    				<td>&nbsp;</td>

	    			</tr>
	    			<? }
	    			$recipe_qnty_sum = 0;
	    			$req_qny_edit_sum = 0;
	    			$req_value_sum = 0;
	    			$k++;

	    			//if ($row[csf("sub_process")] == 93 || $row[csf("sub_process")] == 94 || $row[csf("sub_process")] == 95 || $row[csf("sub_process")] == 96 || $row[csf("sub_process")] == 97 || $row[csf("sub_process")] == 98) {
					 if(in_array($row[csf("sub_process")],$subprocessForWashArr))
					 {
	    				$pro_remark = $process_array_remark[$row[csf("sub_process")]]["wash_comments"];
	    			} else {
	    				$pro_remark = "";
	    			}
	    			if ($pro_remark != '') $pro_remark = "," . $pro_remark; else $pro_remark = "";
	    			$total_liquor = 'Total liquor(ltr)' . ": " . $process_array_liquor[$row[csf("sub_process")]][$row[csf("prod_id")]]['total_liquor'];
	    			$liquor_ratio = 'Liquor Ratio' . ": " . $process_array_liquor[$row[csf("sub_process")]][$row[csf("prod_id")]]['liquor_ratio'];
					$liquor_ratio_process=$process_array_liquor[$row[csf("sub_process")]][$row[csf("prod_id")]]['liquor_ratio'];
					$leveling_water=$tot_bathc_weight*($liquor_ratio_process-1.5);
	    			?>
	    			<tr bgcolor="#CCCCCC">
	    				<th colspan="16" title="Leveling=Tot Batch Weight*(Liquor Ratio-1.5)">
	    				<strong><? echo $dyeing_sub_process[$row[csf("sub_process")]]. ', ' .$liquor_ratio.', ' .$total_liquor . $pro_remark.', '.'Levelling  Water(Ltr): '.$leveling_water; ?></strong>
	    				</th>
	    			</tr>
	    			<tr>
	    				<th width="30">SL</th>
	    				<th width="80">Item Cat.</th>
	    				<th width="80">Item Group</th>
	    				<!--<th width="100">Sub Group</th>-->
	    				<th width="100">Item Description</th>
	    				<th width="50">Dyes Lot</th>
	    				<th width="50">UOM</th>
	    				<th width="100">Dose Base</th>
	    				<th width="40">Ratio</th>
	    				<th width="60">Recipe Qty.</th>
	    				<th width="50">Adj%</th>
	    				<th width="60">Adj Type</th>
	    				<th width="80">Iss. Qty.</th>
	    				<th width="60">KG</th>
	    				<th width="60">GM</th>
	    				<th width="60">MG</th>
	    				<th width="100">Comments</th>

	    			</tr>
	    		</thead>
	    		<?
	    	}

				/*$iss_qnty=$row[csf("req_qny_edit")]*10000;
		$iss_qnty_kg=floor($iss_qnty/10000);
		$lkg=round($iss_qnty-$iss_qnty_kg*10000);
		$iss_qnty_gm=floor($lkg/10);
		$iss_qnty_mg=$lkg%10;*/
				/*$iss_qnty_gm=substr((string)$iss_qnty[1],0,3);
				$iss_qnty_mg=substr($iss_qnty[1],3);*/

				$req_qny_edit = explode(".", (string)$row[csf("req_qny_edit")]);
				$iss_qnty_kg = $req_qny_edit[0];
				if ($iss_qnty_kg == "") $iss_qnty_kg = 0;

				//$mg=($row[csf("req_qny_edit")]-$iss_qnty_kg)*1000*1000;
				//$iss_qnty_gm=floor($mg/1000);
				//$iss_qnty_mg=round($mg-$iss_qnty_gm*1000);

				//echo "gm=".substr($req_qny_edit[1],0,3)."; mg=".substr($req_qny_edit[1],3,3);


				//$num=(string)$req_qny_edit[1];
				//$next_number= str_pad($num,6,"0",STR_PAD_RIGHT);// ."000";
				//$rem= explode(".",($next_number/1000) );
				//echo $rem ."=";
				$iss_qnty_gm = substr($req_qny_edit[1], 0, 3);//$rem[0]; // floor($mg/1000);
				$iss_qnty_mg = substr($req_qny_edit[1], 3, 3);//$rem[1];//round($mg-$iss_qnty_gm*1000);
				$iss_qnty_gm = str_pad($iss_qnty_gm, 3, "0", STR_PAD_RIGHT);
				$iss_qnty_mg = str_pad($iss_qnty_mg, 3, "0", STR_PAD_RIGHT);
				$comment = $process_array[$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"]

				//echo "mg ".$req_qny_edit[1]."<br>";
				// if($recipe_type==2)



				?>
				<tbody>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td align="center"><? echo $i; ?></td>
                    <td><? echo $item_category[$row[csf("item_category")]];  //echo $row[csf("sub_process")];
                    	?></td>
                    	<td><b><? echo $group_arr[$row[csf("item_group_id")]]; ?></b></td>
                    	<!--<td><? echo $row[csf("sub_group_name")]; ?></td>-->
                    	<td><? echo $row[csf("item_description")] . ' ' . $row[csf("item_size")]; ?></td>
                    	<td><? echo $row[csf("item_lot")]; //$item_lot_arr[$row[csf("prod_id")]]; ?></td>
                    	<td align="center"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
                    	<td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
                    	<td align="center"><? echo number_format($row[csf("ratio")], 6, '.', ''); ?></td>
                    	<td align="right"><b><? echo number_format($row[csf("recipe_qnty")], 6, '.', ''); ?></b></td>
                    	<td align="center"><? echo $row[csf("adjust_percent")]; ?></td>
                    	<td><? echo $increase_decrease[$row[csf("adjust_type")]]; ?></td>
                    	<td align="right"><b><? echo number_format($row[csf("req_qny_edit")], 6, '.', ''); ?></b></td>
                    	<td align="right"><? echo $iss_qnty_kg; ?></td>
                    	<td align="right"><? echo $iss_qnty_gm; ?></td>
                    	<td align="right"><? echo $iss_qnty_mg; ?></td>
                    	<td align="right"><? echo $comment; ?></td>
                    </tr>
                </tbody>
                <? $i++;
                $recipe_qnty_sum += $row[csf('recipe_qnty')];
                $req_qny_edit_sum += $row[csf('req_qny_edit')];
                $req_value_sum += $req_value;

                $recipe_qnty_grand += $row[csf('recipe_qnty')];
                $req_qny_edit_grand += $row[csf('req_qny_edit')];
                $req_value_grand += $req_value;
            }
            foreach ($sub_process_tot_rec_array as $val_rec) {
            	$totval_rec = $val_rec;
            }
            foreach ($sub_process_tot_req_array as $val_req) {
            	$totval_req = $val_req;
            }
            foreach ($sub_process_tot_value_array as $req_value) {
            	$tot_req_value = $req_value;
            }

				//$recipe_qnty_grand +=$val_rec;
				//$req_qny_edit_grand +=$val_req;
				//$req_value_grand +=$req_value;
            ?>
            <tr>
            	<td colspan="7" align="right"><strong>Total :</strong></td>
            	<td align="right"><?php echo number_format($totval_rec, 6, '.', ''); ?></td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            	<td align="right"><?php echo number_format($totval_req, 6, '.', ''); ?></td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            </tr>
            <tr>
            	<td colspan="7" align="right"><strong> Grand Total :</strong></td>
            	<td align="right"><?php echo number_format($recipe_qnty_grand, 6, '.', ''); ?></td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            	<td align="right"><?php echo number_format($req_qny_edit_grand, 6, '.', ''); ?></td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            </tr>
        </table>
        <br>
        <?
        echo signature_table(15, $data[0], "900px");
        ?>
    </div>
</div>
<?
exit();
}

if ($action == "chemical_dyes_issue_requisition_without_rate_print_2") {
	extract($_REQUEST);
	$data = explode('*', $data);
	//print_r ($data);

	$sql = "select a.id, a.requ_no, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id from dyes_chem_issue_requ_mst a where a.id='$data[1]' and a.company_id='$data[0]'";
	$dataArray = sql_select($sql);

	$recipe_id = $dataArray[0][csf('recipe_id')];

  	$supplier_library = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	//$entry_form_arr = return_library_array("select id, entry_form from pro_batch_create_mst", "id", "entry_form");
	//$order_id_arr=return_library_array( "select id, booking_id from  pro_recipe_entry_mst", "id", "booking_id");
	//$order_no_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
	//$job_no_arr=return_library_array( "select id, job_no from wo_booking_mst", "id", "job_no");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");


	$batch_weight = 0;
	if ($db_type == 0) {
		$data_array = sql_select("SELECT group_concat(buyer_id) as buyer_id,group_concat(id) as recipe_id, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor, group_concat(concat_ws(':',batch_ratio,liquor_ratio) order by id) as ratio,sum(batch_qty) as batch_qty, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, group_concat(case when entry_form<>60 then batch_id end) as batch_id_rec_main, group_concat(labdip_no) as labdip_no from pro_recipe_entry_mst where id in($recipe_id)");
	} else {
		$data_array = sql_select("SELECT listagg(buyer_id,',') within group (order by buyer_id) as buyer_id,listagg(id,',') within group (order by id) as recipe_id, listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor, listagg(batch_ratio || ':' || liquor_ratio,',') within group (order by id) as ratio,sum(batch_qty) as batch_qty, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(case when entry_form<>60 then batch_id end,',') within group (order by batch_id) as batch_id_rec_main, listagg(labdip_no,',') within group (order by labdip_no) as labdip_no from pro_recipe_entry_mst where id in($recipe_id)");
	}
	$batch_weight = $data_array[0][csf("batch_weight")];
	$recipe_batch_weight = $data_array[0][csf("batch_qty")]+$batch_weight;
	//echo $recipe_batch_weight.'DDD';
	$batch_id = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id")])));
	$labdip_no = implode(", ",array_unique(explode(",",$data_array[0]["LABDIP_NO"])));

	$batch_id_rec_main = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id_rec_main")])));
	if ($batch_id_rec_main == "") $batch_id_rec_main = 0;

	// if ($db_type == 0) {
	// 	$batchdata_array = sql_select("select group_concat(batch_no) as batch_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(distinct color_id) as color_id from pro_batch_create_mst where id in($batch_id)");
	// } else {
	// 	$batchdata_array = sql_select("select listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, listagg(color_id ,',') within group (order by id) as color_id, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($batch_id)");
	// }
	if ($db_type == 0) {
		$batchdata_array = sql_select("select group_concat(batch_no) as batch_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(distinct color_id) as color_id, group_concat(distinct color_range_id) as color_range_id from pro_batch_create_mst where id in($batch_id)");
	} else {
		$batchdata_array = sql_select("select listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, listagg(color_id ,',') within group (order by id) as color_id, listagg(color_range_id ,',') within group (order by id) as color_range_id, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($batch_id)");
	}

	$po_no = '';
	$job_no = '';
	$buyer_name = '';
	$style_ref_no = '';$int_ref_no = '';
	foreach (explode(",", $data_array[0][csf("batch_id")]) as $b_id) {
		if ($entry_form_arr[$b_id] == 36) {
			//$po_data=sql_select("select distinct b.order_no, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(".$b_id.") ");
			$po_data = sql_select("select distinct b.order_no,b.cust_style_ref, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ");
			foreach ($po_data as $row) {
				$po_no .= $row[csf('order_no')] . ",";
				$job_no .= $row[csf('subcon_job')] . ",";
				if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
				$buyer_name .= $buyer_library[$row[csf('party_id')]] . ",";
			}
		} else {
			//$po_data=sql_select("select distinct b.po_number, c.job_no, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(".$b_id.") ");
			$po_data = sql_select("select distinct b.po_number,b.grouping, c.job_no,c.style_ref_no, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ");
			foreach ($po_data as $row) {
				$po_no .= $row[csf('po_number')] . ",";
				$job_no .= $row[csf('job_no')] . ",";
				if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
				if ($int_ref_no == '') $int_ref_no = $row[csf('grouping')]; else $int_ref_no .= "," . $row[csf('grouping')];
				//$buyer_name.=$buyer_library[$row[csf('buyer_name')]].",";
			}
			foreach (explode(",", $data_array[0][csf("buyer_id")]) as $buyer_id) {
				$buyer_name .= $buyer_library[$buyer_id] . ",";
			}

		}
	}

	/*if ($db_type==0)
	{
		$sql_yarn_dtls="select b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
	}
	elseif ($db_type==2)
	{
		$sql_yarn_dtls="select b.po_breakdown_id, b.prod_id, listagg((cast(a.yarn_lot as varchar2(500))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(a.brand_id,',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(500))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
	}*/
	/*if ($db_type == 0) {
		$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
	} else if ($db_type == 2) {
		$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, listagg((cast(a.yarn_lot as varchar2(4000))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(a.brand_id,',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(4000))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id";

	}

	$yarn_dtls_array = array(); $prodID="";
	$result_sql_yarn_dtls = sql_select($sql_yarn_dtls);
	foreach ($result_sql_yarn_dtls as $row) {

		$prodID=$row[csf('prod_id')];

		$yarn_lot = implode(", ", array_unique(explode(",", $row[csf('yarn_lot')])));
		$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
		$brand_name = "";
		foreach ($brand_id as $val) {
			if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= ", " . $brand_arr[$val];
		}

		$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
		$count_name = "";
		foreach ($yarn_count as $val) {
			if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
		}
		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['yarn_lot'] = $yarn_lot;
		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['brand_name'] = $brand_name;
		$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['count_name'] = $count_name;
	}*/
	$sql_yarn_dtls = "select  e.knitting_company,e.knitting_source,c.po_breakdown_id as po_id,d.yarn_lot as yarn_lot,d.yarn_count as yarn_count,d.brand_id as brand_id,d.prod_id
	from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
	where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id
 	and a.id in($batch_id) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22) group by  c.po_breakdown_id,d.yarn_lot,d.yarn_count,d.brand_id,d.prod_id,e.knitting_company,e.knitting_source";
	//echo $sql_yarn_dtls;
	$yarn_dtls_array = array();
	$result_sql_yarn_dtls = sql_select($sql_yarn_dtls);
	foreach ($result_sql_yarn_dtls as $row) {
		$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
		$count_name = "";$prod_ids = "";
		foreach ($yarn_count as $val) {
			if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
		}
			//echo $yarn_lot.'DD';
			$yarn_dtls_array[$row[csf('prod_id')]]['yarn_lot'] .=$row[csf('yarn_lot')].',';
			$yarn_dtls_array[$row[csf('prod_id')]]['brand_name'] .= $brand_arr[$row[csf('brand_id')]].',';
			$yarn_dtls_array[$row[csf('prod_id')]]['count_name'] .= $count_name.',';
			if($row[csf('knitting_source')]==3)
			{
				$yarn_dtls_array[$row[csf('prod_id')]]['knitting_company']=$row[csf('knitting_company')];
			}
			if($prod_ids=="") $prod_ids=$row[csf('prod_id')];else $prod_ids.=",".$row[csf('prod_id')];
	}
	//var_dump($yarn_dtls_array);
	$po_no = implode(", ", array_unique(explode(",", substr($po_no, 0, -1))));
	$job_no = implode(", ", array_unique(explode(",", substr($job_no, 0, -1))));
	$buyer_name = implode(",", array_unique(explode(",", substr($buyer_name, 0, -1))));

	$color_id = array_unique(explode(",", $batchdata_array[0][csf('color_id')]));
	$color_name = '';
	foreach ($color_id as $color) {
		$color_name .= $color_arr[$color] . ",";
	}
	$color_name = substr($color_name, 0, -1);

	$color_range_id = array_unique(explode(",", $batchdata_array[0][csf('color_range_id')]));
	$color_range_name = '';
	foreach ($color_range_id as $color) {
		$color_range_name .= $color_range[$color] . ",";
	}
	$color_range_name = substr($color_range_name, 0, -1);
	//var_dump($recipe_color_arr);
	$supliID_arr=array();
	$supplierID_sql=sql_select("select id,supplier_id from product_details_master where id in($prod_ids)  and status_active=1 and is_deleted=0");
	foreach($supplierID_sql as $row){
		$supliID_arr[$row[csf('id')]]['supplier_id']=$row[csf('supplier_id')];
	}
	//$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
	$group_id=$nameArray[0][csf('group_id')];
	$recipe_id=$data_array[0][csf("recipe_id")];
	$recipe_arr=sql_select("select a.batch_qty,a.working_company_id,a.company_id,b.batch_weight from pro_recipe_entry_mst a, pro_batch_create_mst b where a.batch_id=b.id and a.id in($recipe_id)");
	$owner_company_id=$recipe_arr[0][csf('company_id')];
	$batch_weight_recipe=$recipe_arr[0][csf('batch_qty')];


	?>
	<div style="width:1000px;">
        <table width="1000" cellspacing="0" align="center">
            <tr>
                <td colspan="10" align="center" style="font-size:x-large"><strong><? echo 'Group Name :'.$group_library[$group_id].'<br/>'.'Working Company : '.$company_library[$data[0]];// $company_library[$data[0]]; ?></strong></td>
                </tr>
                <tr class="form_caption">
                	<td colspan="6" align="center" style="font-size:14px"><? echo show_company($data[0],'',''); ?></td>
                </tr>
                <tr>
                	<td colspan="6" align="center" style="font-size:20px"><strong><u><? echo 'Owner Company : '.$company_library[$owner_company_id].'<br>'.$data[2]; ?> Report</u></strong></td>
            </tr>
        </table>
        <table width="950" cellspacing="0" align="center">
            <tr>
                <td width="90"><strong>Req. ID </strong></td>
                <td width="160px"><? echo $dataArray[0][csf('requ_no')]; ?></td>
                <td width="100"><strong>Req. Date</strong></td>
                <td width="160px"><? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
                <td width="90"><strong>Buyer</strong></td>
                <td width="160px"><? echo implode(",", array_unique(explode(",", $buyer_name))); ?></td>
            </tr>
            <tr>
                <td><strong>Order No</strong></td>
                <td><? echo $po_no; ?></td>
                <td><strong>Job No</strong></td>
                <td><? echo $job_no; ?></td>
                <td><strong>Issue Basis</strong></td>
                <td><? echo $receive_basis_arr[$dataArray[0][csf('requisition_basis')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Batch No</strong></td>
                <td><? echo $batchdata_array[0][csf('batch_no')]; ?></td>
                <td><strong>Batch Weight</strong></td>
                <td><? echo $recipe_batch_weight;//$batch_weight + $batchdata_array[0][csf('batch_weight')]; ?></td>
                <td><strong>Color</strong></td>
                <td><? echo $color_name; ?></td>
            </tr>
            <tr>
                <td><strong>Recipe No</strong></td>
                <td><? echo $data_array[0][csf("recipe_id")]; ?></td>
                <!--<td><strong>Total Liq.(ltr)</strong></td><td><? echo $data_array[0][csf("total_liquor")]; ?></td>
                <td>Liquor Ratio</td><td><? echo $data_array[0][csf("ratio")]; ?></td>-->
                <td><strong>Machine No</strong></td>
                <td>
                <?
                $machine_data = sql_select("select machine_no, floor_id from lib_machine_name where id='" . $dataArray[0][csf("machine_id")] . "'");
                echo $machine_data[0][csf('machine_no')];
                ?>
                </td>
				<td><strong>Color Range</strong></td>
				<td><?echo $color_range_name ;?></td>


            </tr>
            <tr>
                <td><strong>Method</strong></td>
                <td><? echo $dyeing_method[$dataArray[0][csf("method")]]; ?></td>
                <td><strong>Style Ref.</strong></td>
                <td ><p><? echo implode(",", array_unique(explode(",", $style_ref_no))); ?></p>
                </td>
				<td><strong>Floor Name</strong></td>
                <td colspan="">
                <?
                $floor_name = return_field_value("floor_name", "lib_prod_floor", "id='" . $machine_data[0][csf('floor_id')] . "'");
                echo $floor_name;
                ?>
                </td>
            </tr>
            <tr>
                <td><strong>Int. Ref. No</strong></td>
                <td ><p><? echo implode(",", array_unique(explode(",", $int_ref_no))); ?></p></td>
                <td><strong>Labdip No</strong></td>
                <td colspan="4"><p><? echo $labdip_no; ?></p></td>
            </tr>
        </table>
    <br>
    <?
    $batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
    $j = 1;
    $entryForm = $entry_form_arr[$batch_id_qry[0]];
    ?>
    <table width="1000" cellspacing="0" align="center" class="rpt_table" border="1" rules="all">
    	<thead bgcolor="#dddddd" align="center">
    		<?
				//echo $entryForm.'A';
    		if ($entryForm == 0) {
    			?>
    			<tr bgcolor="#CCCCFF">
    				<th colspan="8" align="center"><strong>Fabrication</strong></th>
    			</tr>
    			<tr>
    				<th width="30">SL</th>
    				<th width="100">Dia/ W. Type</th>
    				<th width="100">Yarn Lot</th>
    				<th width="100">Supplier</th>
    				<th width="100">Count</th>
    				<th width="300">Constrution & Composition</th>
    				<th width="70">Gsm</th>
    				<th width="70">Dia</th>
    			</tr>
    			<?
    		} else {
    			?>
    			<tr bgcolor="#CCCCFF">
    				<th colspan="4" align="center"><strong>Fabrication</strong></th>
    			</tr>
    			<tr>
    				<th width="50">SL</th>
    				<th width="350">Gmts. Item</th>
    				<th width="110">Gmts. Qty</th>
    				<th>Batch Qty.</th>
    			</tr>
    			<?
    		}
    		?>
    	</thead>
    	<tbody>
    		<?
    		foreach ($batch_id_qry as $b_id) {
    			$batch_query = "Select  po_id, prod_id, item_description, width_dia_type, count(roll_no) as gmts_qty, sum(batch_qnty) batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by po_id, prod_id, item_description, width_dia_type";
    			$result_batch_query = sql_select($batch_query);
    			foreach ($result_batch_query as $rows) {
    				if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
    				$fabrication_full = $rows[csf("item_description")];
					$yarn_lot=$yarn_dtls_array[$rows[csf('prod_id')]]['yarn_lot'];
					$brand_name=$yarn_dtls_array[$rows[csf('prod_id')]]['brand_name'];
					$count_name=$yarn_dtls_array[$rows[csf('prod_id')]]['count_name'];
					$yarn_lots = implode(", ", array_unique(explode(",", substr($yarn_lot, 0, -1))));
					$brand_names = implode(", ", array_unique(explode(",", substr($brand_name, 0, -1))));
					$count_names = implode(", ", array_unique(explode(",", substr($count_name, 0, -1))));


    				$fabrication = explode(',', $fabrication_full);
					//echo $entry_form_arr[$b_id].'B';
    				if ($entry_form_arr[$b_id] == 0) {
    					?>
    					<tr bgcolor="<? echo $bgcolor; ?>">
    						<td align="center"><? echo $j; ?></td>
    						<td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
                            <td align="center"><? echo $yarn_lots;
                            	?></td>
                            <td align="center"><? echo $supplier_library[$knitting_company];
                            	?></td>
                            <td align="center"><? echo $count_names;
                            	?></td>
                            	<td align="left"><? echo $fabrication[0] ?></td>
                            	<td align="center"><? echo $fabrication[2]; ?></td>
                            	<td align="center"><? echo $fabrication[3]; ?></td>
                            </tr>
                            <?
                        } else if ($entry_form_arr[$b_id] == 74 || $entry_form_arr[$b_id] == 36) {
                        	?>
                        	<tr bgcolor="<? echo $bgcolor; ?>">
                        		<td align="center"><? echo $j; ?></td>
                        		<td align="left"><? echo $garments_item[$rows[csf('prod_id')]]; ?></td>
                        		<td align="right"><? echo $rows[csf('gmts_qty')]; ?></td>
                        		<td align="right"><? echo number_format($rows[csf('batch_qnty')], 2); ?></td>
                        	</tr>
                        	<?
                        } else {
                        	?>
                        	<tr bgcolor="<? echo $bgcolor; ?>">
                        		<td align="center"><? echo $j; ?></td>
                        		<td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
                        		<td width="100" align="left"><p style="word-wrap:break-word;"><? echo $yarn_lots; ?></td>
                        		<td align="left"><? echo $supplier_library[$supliID_arr[$rows[csf('prod_id')]]['supplier_id']];//$yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['brand_name']; ?></td>
                        		<td align="left"><? echo $count_names; ?></td>
                        		<td align="left"><? echo $fabrication[0] . ", " . $fabrication[1]; ?></td>
                        		<td align="center"><? echo $fabrication[2]; ?></td>
                        		<td align="center"><? echo $fabrication[3]; ?></td>
                        	</tr>
                        	<?
                        }
                        $j++;
                    }
                }
                ?>
            </tbody>
        </table>
        <div style="width:1000px; margin-top:10px">
        	<table align="right" cellspacing="0" width="1000" border="1" rules="all" class="rpt_table">
        		<thead bgcolor="#dddddd" align="center">
        			<tr bgcolor="#CCCCFF">
        				<th colspan="8" align="center"><strong>Dyes And Chemical Issue Requisition Without Rate</strong>
        				</th>
        			</tr>

        			<?
        		$sql_rec = "select a.id, b.mst_id,a.item_group_id, b.total_liquor,b.liquor_ratio,b.sub_process_id as sub_process_id,b.prod_id,b.dose_base,b.adj_type,b.comments,b.process_remark from pro_recipe_entry_dtls b,product_details_master a where a.id=b.prod_id and b.mst_id in($recipe_id) and a.item_category_id in(5,6,7,23) and b.status_active=1 and b.is_deleted=0";
        			$nameArray = sql_select($sql_rec);
        			$process_array = array();
        			$process_array_remark = array();$recipe_check_array = array();
					$r=1;
        			foreach ($nameArray as $row) {

        					$sub_process_data=$row[csf('mst_id')].$row[csf("sub_process_id")].$row[csf("prod_id")];
						if (!in_array($sub_process_data,$recipe_check_array))
							{ $r++;


									$recipe_check_array[]=$sub_process_data;
									$process_array_liquor[$row[csf("sub_process_id")]][$row[csf("prod_id")]]['total_liquor'] += $row[csf("total_liquor")];
        							$process_array_liquor[$row[csf("sub_process_id")]][$row[csf("prod_id")]]['liquor_ratio'] += $row[csf("liquor_ratio")];
									$process_array_liquor[$row[csf("sub_process_id")]][$row[csf("prod_id")]]['total_liquor_count'] .= $row[csf("total_liquor")].',';
							}
							else
							{
								 	$process_array_liquor[$row[csf("sub_process_id")]][$row[csf("prod_id")]]['total_liquor'] =0;
        							$process_array_liquor[$row[csf("sub_process_id")]][$row[csf("prod_id")]]['liquor_ratio'] = 0;
							}


        				$process_array[$row[csf("sub_process_id")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"] = $row[csf("comments")];
						$prod_lot_array[$row[csf("prod_id")]]["lot"] = $row[csf("lot")];

        			}
        			$sql_rec_remark = "select b.sub_process_id as sub_process_id,b.comments,b.process_remark,b.total_liquor,b.liquor_ratio from pro_recipe_entry_dtls b  where  b.mst_id in($recipe_id) and b.sub_process_id in(93,94,95,96,97,98,140,141,142,143) and b.status_active=1 and b.is_deleted=0";
        			$nameArray_re = sql_select($sql_rec_remark);
        			foreach ($nameArray_re as $row) {
        				$process_array_liquor[$row[csf("sub_process_id")]][$row[csf("prod_id")]]['total_liquor'] = $row[csf("total_liquor")];
        				$process_array_liquor[$row[csf("sub_process_id")]][$row[csf("prod_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
        				$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"] = $row[csf("process_remark")];
        			}

        			$group_arr = return_library_array("select id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0", 'id', 'item_name');
        			if ($db_type == 0) {
        				$item_lot_arr = return_library_array("select b.prod_id,group_concat(b.item_lot) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) and b.ratio>0 group by b.prod_id", "prod_id", "yean_lot");
        			} else {
        			//	$item_lot_arr = return_library_array("select b.prod_id,listagg((cast(b.item_lot as varchar2(500))),',') within group (order by b.prod_id) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) and b.ratio>0 group by b.prod_id", "prod_id", "yean_lot");
					$sql_lot=sql_select("select b.prod_id, b.item_lot  as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) and b.ratio>0 ");
						foreach ($sql_lot as $row)
						{
							$item_lot_arr[$row[csf("prod_id")]].=$row[csf("yean_lot")].',';
						}
        			}
				/*$sql_dtls = "select a.requ_no, a.batch_id, a.recipe_id, b.id,
		b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
	  	c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id
	    from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
	    where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id=$data[1] order by b.id, b.seq_no";*/ // dyes_chem_issue_requ_dtls  comments
	    $sql_dtls = "(select a.requ_no, a.batch_id, a.recipe_id, b.id,
	    b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
	    c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id, b.comments
	    from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
	    where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and c.stock_value>0 and c.item_category_id in (5,6,7,23) and a.id=$data[1])
	    union
	    (
	    select a.requ_no, a.batch_id, a.recipe_id, b.id,
	    b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
	    null as item_description, null as  item_group_id,  null as  sub_group_name,  null as  item_size, null as  unit_of_measure,null as  avg_rate_per_unit,null as prod_id , b.comments
	    from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b
	    where a.id=b.mst_id and b.sub_process in ($subprocessforwashin) and a.id=$data[1]
	    )
	    order by id";
				// echo $sql_dtls;//die;

	    $sql_result = sql_select($sql_dtls);

	    $sub_process_array = array();
	    $sub_process_tot_rec_array = array();
	    $sub_process_tot_req_array = array();
	    $sub_process_tot_value_array = array();

	    foreach ($sql_result as $row) {
	    	$sub_process_tot_rec_array[$row[csf("sub_process")]] += $row[csf("recipe_qnty")];
	    	$sub_process_tot_req_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
	    	$sub_process_tot_value_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")] * $row[csf("avg_rate_per_unit")];
	    }

				//var_dump($sub_process_tot_req_array);
	    $i = 1;
	    $k = 1;
	    $recipe_qnty_sum = 0;
	    $req_qny_edit_sum = 0;
	    $recipe_qnty = 0;
	    $req_qny_edit = 0;
	    $req_value_sum = 0;
	    $req_value_grand = 0;
	    $recipe_qnty_grand = 0;
	    $req_qny_edit_grand = 0;

	    foreach ($sql_result as $row)
	    {
	    	if ($i % 2 == 0)
	    		$bgcolor = "#E9F3FF";
	    	else
	    		$bgcolor = "#FFFFFF";
	    	if (!in_array($row[csf("sub_process")], $sub_process_array))
	    	{
	    		$sub_process_array[] = $row[csf('sub_process')];
	    		if ($k != 1) {
	    			?>
	    			<tr>
                        <td colspan="5" align="right"><strong>Sub Total:</strong></td>
	    				<td align="right"><b><?php echo number_format($req_qny_edit_sum, 6, '.', ''); ?></b></td>
	    				<td>&nbsp;</td>
	    			</tr>
	    			<? }
	    			$recipe_qnty_sum = 0;
	    			$req_qny_edit_sum = 0;
	    			$req_value_sum = 0;
	    			$k++;

	    			//if ($row[csf("sub_process")] == 93 || $row[csf("sub_process")] == 94 || $row[csf("sub_process")] == 95 || $row[csf("sub_process")] == 96 || $row[csf("sub_process")] == 97 || $row[csf("sub_process")] == 98) {
					 if(in_array($row[csf("sub_process")],$subprocessForWashArr))
					 {
	    				$pro_remark = $process_array_remark[$row[csf("sub_process")]]["wash_comments"];
	    			} else {
	    				$pro_remark = "";
	    			}
	    			if ($pro_remark != '') $pro_remark = "," . $pro_remark; else $pro_remark = "";
	    			$total_liquor = 'Total liquor(ltr)' . ": " . $process_array_liquor[$row[csf("sub_process")]][$row[csf("prod_id")]]['total_liquor'];
	    			$liquor_ratio = 'Liquor Ratio' . ": " . number_format($process_array_liquor[$row[csf("sub_process")]][$row[csf("prod_id")]]['total_liquor']/$recipe_batch_weight,2);

					$total_liquor_count= rtrim($process_array_liquor[$row[csf("sub_process")]][$row[csf("prod_id")]]['total_liquor_count'],',');

	    			?>
	    			<tr bgcolor="#CCCCCC">
	    				<th colspan="8" title="Liquor Ratio=Total Liquor(Ltr)/Total Batch Weight<? //echo $recipe_batch_weight;?>; &nbsp;Total Liquor=<? echo $total_liquor_count;?>">
	    					<strong><? echo $dyeing_sub_process[$row[csf("sub_process")]] . ', ' . $liquor_ratio . ', ' . $total_liquor . $pro_remark; ?></strong>
	    				</th>
	    			</tr>
	    			<tr>
	    				<th width="30">SL</th>
	    				<th width="180">Item Description</th>
						<th width="230">Lot</th>
	    				<th width="60">Dose Base</th>
	    				<th width="80">Ratio</th>
	    				<th width="80">Iss. Qty.</th>
	    				<th width="150">Comments</th>
	    			</tr>
	    		</thead>
	    		<?
	    	}

				/*$iss_qnty=$row[csf("req_qny_edit")]*10000;
		$iss_qnty_kg=floor($iss_qnty/10000);
		$lkg=round($iss_qnty-$iss_qnty_kg*10000);
		$iss_qnty_gm=floor($lkg/10);
		$iss_qnty_mg=$lkg%10;*/
				/*$iss_qnty_gm=substr((string)$iss_qnty[1],0,3);
				$iss_qnty_mg=substr($iss_qnty[1],3);*/

				$req_qny_edit = explode(".", (string)$row[csf("req_qny_edit")]);
				$iss_qnty_kg = $req_qny_edit[0];
				if ($iss_qnty_kg == "") $iss_qnty_kg = 0;

				//$mg=($row[csf("req_qny_edit")]-$iss_qnty_kg)*1000*1000;
				//$iss_qnty_gm=floor($mg/1000);
				//$iss_qnty_mg=round($mg-$iss_qnty_gm*1000);

				//echo "gm=".substr($req_qny_edit[1],0,3)."; mg=".substr($req_qny_edit[1],3,3);


				//$num=(string)$req_qny_edit[1];
				//$next_number= str_pad($num,6,"0",STR_PAD_RIGHT);// ."000";
				//$rem= explode(".",($next_number/1000) );
				//echo $rem ."=";
				$iss_qnty_gm = substr($req_qny_edit[1], 0, 3);//$rem[0]; // floor($mg/1000);
				$iss_qnty_mg = substr($req_qny_edit[1], 3, 3);//$rem[1];//round($mg-$iss_qnty_gm*1000);
				$iss_qnty_gm = str_pad($iss_qnty_gm, 3, "0", STR_PAD_RIGHT);
				$iss_qnty_mg = str_pad($iss_qnty_mg, 3, "0", STR_PAD_RIGHT);
				$comment = $process_array[$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"];
				$lot_no=rtrim($item_lot_arr[$row[csf("prod_id")]],',');
				$lot = implode(",",array_unique(explode(",",$lot_no)));

				//echo "mg ".$req_qny_edit[1]."<br>";

				?>
				<tbody>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td width="30" align="center"><? echo $i; ?></td>
                    	<td width="180" style="word-break: break-all;"><p><? echo $row[csf("item_description")] . ' ' . $row[csf("item_size")]; ?></p></td>
						<td width="230" style="word-break: break-all;"><p><? echo $lot; ?></p></td>
                    	<td width="60"><p><? echo $dose_base[$row[csf("dose_base")]]; ?></p></td>
                    	<td width="80" align="center"><p><? echo number_format($row[csf("ratio")], 6, '.', ''); ?></p></td>
                    	<td width="80" align="right"><p><? echo number_format($row[csf("req_qny_edit")], 6, '.', ''); ?></p></td>
                    	<td width="150" style="word-break: break-all;" align="right"><p><? echo $row[csf("comments")]; ?></p></td>
                    </tr>
                </tbody>
                <? $i++;
                $recipe_qnty_sum += $row[csf('recipe_qnty')];
                $req_qny_edit_sum += $row[csf('req_qny_edit')];
                $req_value_sum += $req_value;

                $recipe_qnty_grand += $row[csf('recipe_qnty')];
                $req_qny_edit_grand += $row[csf('req_qny_edit')];
                $req_value_grand += $req_value;
            }
            foreach ($sub_process_tot_rec_array as $val_rec) {
            	$totval_rec = $val_rec;
            }
            foreach ($sub_process_tot_req_array as $val_req) {
            	$totval_req = $val_req;
            }
            foreach ($sub_process_tot_value_array as $req_value) {
            	$tot_req_value = $req_value;
            }

				//$recipe_qnty_grand +=$val_rec;
				//$req_qny_edit_grand +=$val_req;
				//$req_value_grand +=$req_value;
            ?>
            <tr>
                <td colspan="5" align="right"><strong>Sub Total:</strong></td>
            	<td align="right"><b><?php echo number_format($req_qny_edit_sum, 6, '.', ''); ?></b></td>
            	<td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="5" align="right"><strong> Grand Total :</strong></td>
            	<td align="right"><b><?php echo number_format($req_qny_edit_grand, 6, '.', ''); ?></b></td>
            	<td>&nbsp;</td>
            </tr>
        </table>
        <br>
        <?=signature_table(15, $data[0], "1000px"); ?>
    </div>
    </div>
    <?
    exit();
}

if ($action == "data_exchange_libas")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$con = connect();
	if ($db_type == 0)
	{
		mysql_query("BEGIN");
	}
	if ($operation == 0)  // Insert Here
	{

		$dyeing_sub_process = array(1=>"Demineralisation",10=>"Pretreatment",20=>"Neutralisation-1",21=>"Neutralisation-2",22=>"Neutralisation-3",23=>"Neutralisation-4",30=>"Biopolish",40=>"Dyestuff",50=>"Dyeing Bath",60=>"After Treatment 1",70=>"Color Remove",90=>"Other",91=>"Leveling",92=>"Finishing Process",93=>"Wash 1",94=>"Wash 2",95=>"Wash 3",96=>"Wash 4",97=>"Wash 5",98=>"Wash 6",99=>"After Treatment 2",100=>"After Treatment 3",101=>"After Treatment 4",102=>"Desizing",103=>"Enzyme",104=>"PP Bleach",105=>"Bleach",106=>"PP Bleach Neutral",107=>"Bleach Neutral",108=>"Cleaning",109=>"PP Neutral",110=>"Tint",111=>"Fixing",112=>"Softener",113=>"Acid Wash",114=>"Towel Bleach",115=>"Scouring",116=>"Resign Spray",117=>"Dyeing",118=>"Soaping",119=>"Silicon");

		$dyeing_sub_process_map=array(
		1=>1,10=>2,20=>3,21=>4,22=>5,23=>6,30=>7,40=>8,50=>9,60=>10,70=>11,90=>12,91=>13,92=>14,93=>15,94=>16,95=>17,96=>18,97=>19,98=>20,99=>21,100=>22,101=>23,102=>24,103=>25,104=>26,105=>27,106=>28,107=>29,108=>30,109=>31,110=>32,111=>33,112=>34,113=>35,114=>36,115=>37,116=>38,117=>39,118=>40,119=>41
		);

		$sql=sql_select("SELECT a.id, b.batch_id,b.recipe_id, a.requ_no,a.sub_process,a.product_id,a.required_qnty,a.req_qny_edit,a.seq_no,a.item_category,b.machine_id  from dyes_chem_issue_requ_dtls a , dyes_chem_issue_requ_mst b where a.mst_id=b.id   and b.id=$update_id and a.required_qnty > 0   and a.product_id > 0 group by a.id, b.batch_id,b.recipe_id, a.requ_no,a.sub_process,a.product_id,a.required_qnty,a.req_qny_edit,a.seq_no,a.item_category,b.machine_id order by a.sub_process,a.seq_no ");

		$all_prod_id_array=array();
		$all_sub_process_array=array();
		foreach($sql as $v)
		{
			$all_prod_id_array[$v[csf("product_id")]]=$v[csf("product_id")];
			$all_sub_process_array[$v[csf("sub_process")]]=$v[csf("sub_process")];
		}
		$all_prod_ids=implode(",", $all_prod_id_array);
		$batch_ids=$sql[0][csf("batch_id")];
		$recipe_ids=$sql[0][csf("recipe_id")];
		$machine_ids=$sql[0][csf("machine_id")];
		if(!$machine_ids)$machine_ids=0;
		if(!$all_prod_ids)$all_prod_ids=0;
		if(!$recipe_ids)$recipe_ids=0;

		$batch_no_lib=return_library_array("SELECT id,batch_no from  pro_batch_create_mst where id in($batch_ids) and  status_active=1 ", "id", "batch_no");

		$batch_weight_lib=return_library_array("SELECT id,sum(batch_weight) as qnty from  pro_batch_create_mst where id in($batch_ids) and  status_active=1 group by id", "id", "qnty");

		$batch_nos=implode("|", $batch_no_lib);
		$batch_weight=implode(",", $batch_weight_lib);


		$product_uom_lib=return_library_array("SELECT id,unit_of_measure from  product_details_master where id in($all_prod_ids) and  status_active=1 ", "id", "unit_of_measure");

		$product_category_lib=return_library_array("SELECT id,item_category_id from  product_details_master where id in($all_prod_ids) and  status_active=1 ", "id", "item_category_id");

		$product_gsm_lib=return_library_array("SELECT id,gsm from  product_details_master where id in($all_prod_ids) and  status_active=1 and  gsm>0", "id", "gsm");

		$gsm_all=implode("|", $product_gsm_lib);
		$subprocess_names="";

		$machine_sql=sql_select("SELECT id,machine_no,machine_group from  lib_machine_name where id in($machine_ids) and  status_active=1 ");
		$machine_no=$machine_sql[0][csf("machine_no")];
		$machine_group=$machine_sql[0][csf("machine_group")];
		if($machine_group) $machine_group=$machine_group;else $machine_group=0;

		$recipe_dtls="SELECT b.liquor_ratio,a.method,a.batch_ratio, b.mst_id, b.sub_process_id,   b.prod_id, b.dose_base, b.ratio,  b.seq_no,  b.total_liquor  FROM pro_recipe_entry_mst a,pro_recipe_entry_dtls b Where a.id=b.mst_id and  b.MST_ID in($recipe_ids) and a.status_active=1 and b.status_active=1";
		$recipe_liq=0;
		$recipe_ratio="";$method="";
		$recipe_process_chk=array();
		$rID_de1=execute_query( "delete from dispensing_import where  requisition_no =$txt_mrr_no and form_type=3 ",0);

		foreach(sql_select($recipe_dtls) as $val)
		{
			if( $recipe_process_chk[$val[csf("mst_id")]][$val[csf("sub_process_id")]]==""  )
			{
				$recipe_liq=$val[csf("total_liquor")];
				$method=$val[csf("method")];
				if($recipe_ratio=="") $recipe_ratio=$val[csf("batch_ratio")].":".$val[csf("liquor_ratio")];
				else $recipe_ratio .='|'.$val[csf("batch_ratio")].":".$val[csf("liquor_ratio")];
				$recipe_process_chk[$val[csf("mst_id")]][$val[csf("sub_process_id")]] =$val[csf("sub_process_id")];
			}
			if($sub_pro_arr[$val[csf("sub_process_id")]]=="")
			{
				if($subprocess_names)
					$subprocess_names .="|".$dyeing_sub_process[$val[csf("sub_process_id")]];
			    else
			    	$subprocess_names =$dyeing_sub_process[$val[csf("sub_process_id")]];
				$sub_pro_arr[$val[csf("sub_process_id")]]=$val[csf("sub_process_id")];
			}

		}
		//echo "$subprocess_names";
		$chemical_amount=0;

		foreach($sql as $v)
		{
			if($product_category_lib[$v[csf("product_id")]]==5)
			{
				$chemical_amount+=$v[csf("required_qnty")];
			}

		}
		//echo "$recipe_ratio string";die;
		/*$programs="";
		foreach($all_sub_process_array as $v)
		{
			if($programs=="") $programs.=$v."#".$batch_weight;
			else $programs.='|'.$v."#".$batch_weight;
		}*/
		$programs=$method."#".$recipe_liq;


        $i=1;
		$datas=array();
		$data_mst="";
		$data_dtls="";
		//$rID_de1=execute_query( "delete from dispensing_import where  requisition_no =$txt_mrr_no",0);


		//$field_array_mst = "requisition_no,data,requisition_id,form_type,sub_process,data_sequence,item_category";
		//$data_mst=$batch_nos.",".$machine_group.",".$machine_ids.",".$programs.",".$batch_weight.","."0".","."0".","."0".","."0".",".$recipe_liq.",".$recipe_ratio.","."0".","."0".",".$chemical_amount.","."0".","."0".","."0".","."0".",".$subprocess_names.","."0".",".$gsm_all;
		$data_mst=$batch_nos.",".$machine_group.",".$machine_ids.",".$programs.",".$batch_weight.","."0".","."0".","."0".","."0".",".$recipe_liq.","."0".","."0".","."0".",".number_format($chemical_amount,6).","."0,0,0,0,0,0,0,0,0,0";

		//$data_array_mst = "('" . $sql[0][csf('requ_no')]. "','" . $data_mst . "',$update_id,1,0,0,0)";
		//$rID_mst = sql_insert("dispensing_import", $field_array_mst, $data_array_mst, 1);
		$field_array_new="requisition_no,mst_data,dtls_data,requisition_id,form_type";
	    //echo "5**"."INSERT INTO dispensing_import(".$field_array_mst.")VALUES ".$data_array_mst; die;
		$dtls_all_data="";
		if ($db_type == 0)
		{
			mysql_query("BEGIN");
		}
		$con = connect();
		foreach($sql as $sqlRow)
		{
			$required_qnty=number_format($sqlRow[csf('required_qnty')],6,".","");
			$itemCategory=0;
			if($sqlRow[csf('item_category')]==6)
			{
				$itemCategory=1;
			}
			$item_category_val=strtoupper($item_category[$sqlRow[csf('item_category')]]);
			if($sqlRow[csf('item_category')]==5)$item_category_val="CHEMICAL";
			if($sqlRow[csf('item_category')]==6)$item_category_val="DYESTUFF";

 			$data_dtls =$sqlRow[csf('product_id')].",".$required_qnty.",".$unit_of_measurement[$product_uom_lib[$sqlRow[csf('product_id')]]].",".$sqlRow[csf('seq_no')].","."0".","."0".","."0".",".$method.",".$item_category_val .";";

 			$dtls_all_data.=$data_dtls;

			$datas[]=$data_dtls;


			//$field_array = "requisition_no,data_sequence,data,item_category,requisition_id,form_type,sub_process";
			//$data_array = "('" . $sqlRow[csf('requ_no')]. "','" . $sqlRow[csf('seq_no')]. "','" . $data_dtls . "','" . $itemCategory . "',$update_id,2,'" . $sqlRow[csf('sub_process')]. "')";


			//echo "10** insert into dispensing_import $field_array_new $data_array_new  ";die;






			//$rID = sql_insert("dispensing_import", $field_array, $data_array, 1);



		}
		$dtls_all_data=rtrim($dtls_all_data,';');
		$data_array_new = "('" . $sql[0][csf('requ_no')]. "','" . $data_mst . "','" . $dtls_all_data . "',$update_id,3)";
		$rID_mst = sql_insert("dispensing_import", $field_array_new, $data_array_new, 1);



		if ($db_type == 0)
		{
			if ($rID_mst && $rID_de1)
			{
				mysql_query("COMMIT");
				echo "0**";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if ($db_type == 2 || $db_type == 1)
		{
			if ($rID_mst && $rID_de1)
			{
				oci_commit($con);
				echo "0**";
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}

        disconnect($con);
		die;

		//print_r($datas);





		/*if (str_replace("'", "", $update_id) == "") {
			$rID = sql_insert("dyes_chem_issue_requ_mst", $field_array, $data_array, 1);
		} else {
			$rID = sql_update("dyes_chem_issue_requ_mst", $field_array_up, $data_array_up, "id", $update_id, 1);
		}

		$rID_att = sql_insert("dyes_chem_requ_recipe_att", $field_array_att, $data_array_att, 1);
		$rID_dtls = sql_insert("dyes_chem_issue_requ_dtls", $field_array_dtls, $data_array_dtls, 1);
		if ($db_type == 0) {
			if ($rID && $rID_att && $rID_dtls) {
				mysql_query("COMMIT");
				echo "0**" . $requ_no . "**" . $mst_id;
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . $requ_no . "**" . $mst_id;
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID_att && $rID_dtls) {
				oci_commit($con);
				echo "0**" . $requ_no . "**" . $mst_id;
			} else {
				oci_rollback($con);
				echo "10**" . $requ_no . "**" . $mst_id;
			}
		}
		disconnect($con);*/
		//die;
	}
}
if ($action == "report_sendtoprint_text_file")
{
	// var_dump($_REQUEST);
	$data = explode("***", $data);
	$update_id=$data[0];
	$recipe_no=$data[1];
	$batch_no=$data[2];
	$batch_id=$data[3];

	/*$content = "some text here";
	$fp = fopen($_SERVER['DOCUMENT_ROOT'] . "inventory/chemical_dyes/requires/myText.txt","wb");
	fwrite($fp,$content);
	fclose($fp);

	echo "myText";*/
	header('Content-Type: text/csv; charset=utf-8');

	$machine_no_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');
	$machine_brand_arr = return_library_array("select id, brand from lib_machine_name", 'id', 'brand'); // Temporary

	   $sqlRes="SELECT a.id, b.batch_id,b.recipe_id, a.requ_no,a.sub_process,a.product_id,a.required_qnty,a.req_qny_edit,a.seq_no,a.item_category,b.machine_id,c.item_description as fab_desc,c.unit_of_measure  from dyes_chem_issue_requ_dtls a , dyes_chem_issue_requ_mst b,product_details_master c where a.mst_id=b.id  and c.id=a.product_id  and b.id=$update_id and a.required_qnty > 0   and a.product_id > 0  and a.item_category in(6) group by a.id, b.batch_id,b.recipe_id, a.requ_no,a.sub_process,a.product_id,a.required_qnty,a.req_qny_edit,a.seq_no,a.item_category,c.item_description,b.machine_id,c.unit_of_measure order by a.sub_process,a.seq_no ";
	$sqlResult=sql_select($sqlRes);

		$all_prod_id_array=array();
		$all_sub_process_array=array();
		foreach($sqlResult as $v)
		{
			$all_prod_id_array[$v[csf("product_id")]]=$v[csf("product_id")];
			$all_sub_process_array[$v[csf("sub_process")]]=$v[csf("sub_process")];
		}
		$all_prod_ids=implode(",", $all_prod_id_array);
		$batch_ids=$sqlResult[0][csf("batch_id")];
		//echo $batch_ids.'DSDSDSD';
		$recipe_ids=$sqlResult[0][csf("recipe_id")];
		$machine_ids=$sqlResult[0][csf("machine_id")];
		$requ_no=$sqlResult[0][csf("requ_no")];
		$requ_no=explode("-",$requ_no);
		$requ_noA=$requ_no[2].'-'.$requ_no[3];
		if(!$machine_ids)$machine_ids=0;
		if(!$all_prod_ids)$all_prod_ids=0;
		if(!$recipe_ids)$recipe_ids=0;
		//echo "SELECT id,batch_no from  pro_batch_create_mst where id in($batch_ids) and  status_active=1 ";
		$batch_no_lib=return_library_array("SELECT id,batch_no from  pro_batch_create_mst where id in($batch_ids) and  status_active=1 ", "id", "batch_no");

		$batch_nos=implode(",", $batch_no_lib);
		//echo $batch_nos;
		$batch_weight=implode(",", $batch_weight_lib);
		$machine_sql=sql_select("SELECT id,machine_no,machine_group from  lib_machine_name where id in($machine_ids) and  status_active=1 ");
		//echo "SELECT id,machine_no,machine_group from  lib_machine_name where id in($machine_ids) and  status_active=1 ";die;
		//echo "SELECT id,machine_no,machine_group from  lib_machine_name where id in($machine_ids) and  status_active=1 ";
		$machine_no=$machine_sql[0][csf("machine_no")];
		//$machine_group=$machine_sql[0][csf("machine_group")];
		//$mc_name=$machine_no.'-'.$machine_group;
		if($machine_no) $machine_no=$machine_no;else $machine_no=0;

	/*foreach (glob($batch_nos.'_'.$user_id."*.txt") as $filename) {
		@unlink($filename);
	}*/
	//echo $within_group;die;
	//exit;
	$i = 1;
	/*$zip = new ZipArchive();            // Load zip library
	//$filename = str_replace(".sql", ".zip", "files/".$_SESSION['logic_erp']['user_id']."/norsel_bundle.sql");            // Zip name
	$filename = str_replace(".sql",".zip",$batch_nos.'_'.$user_id.".sql");			// Zip name
	if ($zip->open($filename, ZIPARCHIVE::CREATE) !== TRUE) {        // Opening zip file to load files
		$error .= "* Sorry ZIP creation failed at this time<br/>";
		echo $error;
	}*/

	$i = 1;
	$year = date("y");

	//$created_files=array();

		//$file_name =$user_id."_";
	//	$created_files[]=$file_name.".txt";
		//$myfile = fopen($file_name . ".txt", "w") or die("Unable to open file!");

		 $path = $requ_noA.'_'.$user_id.'.txt';
		$myfile = fopen($path, "w") or die("Unable to open file!");
		//$txt="lorem ipsusdsdm ds";



		//echo $file_name.'d';;die;
		$txt = "";
		foreach($sqlResult as $sqlRow)
		{

			$itemCategory=0;
			if($sqlRow[csf('item_category')]==6)
			{
				$itemCategory=1;
			}
			$item_category_val=strtoupper($item_category[$sqlRow[csf('item_category')]]);
			//if($sqlRow[csf('item_category')]==5) $item_category_val="CHEMICAL";
			//if($sqlRow[csf('item_category')]==6)
			$item_category_val=trim($sqlRow[csf('fab_desc')]);
			$uom_id=$sqlRow[csf('unit_of_measure')];
			if($uom_id==12) //KG
			{
				$required_qnty=$sqlRow[csf('required_qnty')]*1000;
			}
			else if($uom_id==40) //Ltr
			{
				$required_qnty=$sqlRow[csf('required_qnty')]*1000;
			}
			else if($uom_id==15) //LBS
			{
				$lbs=453.592369999995;
				$required_qnty=$sqlRow[csf('required_qnty')]*$lbs;
			}


		$Req_amountArr=explode(".",$required_qnty);
		$Req_amount=$Req_amountArr[0];
		if($Req_amount) $Req_amount=$Req_amount;else $Req_amount=0;
		$Req_amount_fraction=$Req_amountArr[1];
		if($Req_amount_fraction=='') $Req_amount_fraction=0;

		$txt .= $batch_nos.';'.$machine_no.';'.$item_category_val .';'.$Req_amount.','.$Req_amount_fraction.';'.$sqlRow[csf('seq_no')] . "\r\n";


		$i++;
	}

		fwrite($myfile, $txt);
		rewind($myfile);   // reset file pointer so as output file from the beginning

		// `basename` and `filesize` accept path to file, not file descriptor
		header('Cache-Control: public');
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename='.$myfile);
		header('Content-Type: text/plain');
		header('Content-Transfer-Encoding: binary');
		//readfile($file);

		//fpassthru($file);



	//echo $txt;die;  $file_name = "NORSEL-IMPORT_".$userid."_" . $i;
	/*foreach (glob($user_id."*.txt") as $filenames)
	{
		$zip->addFile($file_folder.$filenames);
	}
	$zip->close();*/

	foreach (glob($user_id."*.txt") as $filename)
	{
		@unlink($filename);
	}
	echo $path;
	//echo $requ_noA."_".$user_id; //str_replace(".zip","",$file_folder);//"norsel_bundle";
	exit();
}



if ($action == "report_artexport_text_file222222")
{
	// var_dump($_REQUEST);
	$data = explode("***", $data);
	$update_id=$data[0];
	$recipe_no=$data[1];
	$batch_no=$data[2];
	$batch_id=$data[3];

	/*$content = "some text here";
	$fp = fopen($_SERVER['DOCUMENT_ROOT'] . "inventory/chemical_dyes/requires/myText.txt","wb");
	fwrite($fp,$content);
	fclose($fp);

	echo "myText";*/
	header('Content-Type: text/csv; charset=utf-8');

	$machine_no_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');
	$machine_brand_arr = return_library_array("select id, brand from lib_machine_name", 'id', 'brand'); // Temporary

	   $sqlRes="SELECT a.id, b.batch_id,b.recipe_id, a.requ_no,a.sub_process,a.product_id,a.required_qnty,a.req_qny_edit,a.seq_no,a.item_category,b.machine_id,c.item_description as fab_desc,c.unit_of_measure  from dyes_chem_issue_requ_dtls a , dyes_chem_issue_requ_mst b,product_details_master c where a.mst_id=b.id  and c.id=a.product_id  and b.id=$update_id and a.required_qnty > 0   and a.product_id > 0  and a.item_category in(6) group by a.id, b.batch_id,b.recipe_id, a.requ_no,a.sub_process,a.product_id,a.required_qnty,a.req_qny_edit,a.seq_no,a.item_category,c.item_description,b.machine_id,c.unit_of_measure order by a.sub_process,a.seq_no ";
	$sqlResult=sql_select($sqlRes);

		$all_prod_id_array=array();
		$all_sub_process_array=array();
		foreach($sqlResult as $v)
		{
			$all_prod_id_array[$v[csf("product_id")]]=$v[csf("product_id")];
			$all_sub_process_array[$v[csf("sub_process")]]=$v[csf("sub_process")];
		}
		$all_prod_ids=implode(",", $all_prod_id_array);
		$batch_ids=$sqlResult[0][csf("batch_id")];
		//echo $batch_ids.'DSDSDSD';
		$recipe_ids=$sqlResult[0][csf("recipe_id")];
		$machine_ids=$sqlResult[0][csf("machine_id")];
		$requ_no=$sqlResult[0][csf("requ_no")];
		$requ_no=explode("-",$requ_no);
		$requ_noA=$requ_no[2].'-'.$requ_no[3];
		if(!$machine_ids)$machine_ids=0;
		if(!$all_prod_ids)$all_prod_ids=0;
		if(!$recipe_ids)$recipe_ids=0;
		//echo "SELECT id,batch_no from  pro_batch_create_mst where id in($batch_ids) and  status_active=1 ";
		$batch_no_lib=return_library_array("SELECT id,batch_no from  pro_batch_create_mst where id in($batch_ids) and  status_active=1 ", "id", "batch_no");

		$batch_nos=implode(",", $batch_no_lib);
		//echo $batch_nos;
		$batch_weight=implode(",", $batch_weight_lib);
		$machine_sql=sql_select("SELECT id,machine_no,machine_group from  lib_machine_name where id in($machine_ids) and  status_active=1 ");
		//echo "SELECT id,machine_no,machine_group from  lib_machine_name where id in($machine_ids) and  status_active=1 ";die;
		//echo "SELECT id,machine_no,machine_group from  lib_machine_name where id in($machine_ids) and  status_active=1 ";
		$machine_no=$machine_sql[0][csf("machine_no")];
		//$machine_group=$machine_sql[0][csf("machine_group")];
		//$mc_name=$machine_no.'-'.$machine_group;
		if($machine_no) $machine_no=$machine_no;else $machine_no=0;

	/*foreach (glob($batch_nos.'_'.$user_id."*.txt") as $filename) {
		@unlink($filename);
	}*/
	//echo $within_group;die;
	//exit;
	$i = 1;
	/*$zip = new ZipArchive();            // Load zip library
	//$filename = str_replace(".sql", ".zip", "files/".$_SESSION['logic_erp']['user_id']."/norsel_bundle.sql");            // Zip name
	$filename = str_replace(".sql",".zip",$batch_nos.'_'.$user_id.".sql");			// Zip name
	if ($zip->open($filename, ZIPARCHIVE::CREATE) !== TRUE) {        // Opening zip file to load files
		$error .= "* Sorry ZIP creation failed at this time<br/>";
		echo $error;
	}*/

	$i = 1;
	$year = date("y");

	//$created_files=array();

		//$file_name =$user_id."_";
	//	$created_files[]=$file_name.".txt";
		//$myfile = fopen($file_name . ".txt", "w") or die("Unable to open file!");

		 $path = $requ_noA.'_'.$user_id.'.txt';
		$myfile = fopen($path, "w") or die("Unable to open file!");
		//$txt="lorem ipsusdsdm ds";



		//echo $file_name.'d';;die;
		$txt = "00262       2817.000      0\n00216       6010.000      0\n00216       7512.000      0\n00154812    3756.000      0\n00185711    7512.000      0\n00231095    3756.000      0\n00231100    7512.000      0\n00559646    1409.000      0\n00617765     939.000      0\n00354712    5634.000      0\n0077689     2254.000      0\n00138827    3756.000      0\n00213     433818.000      0\n00438993    3756.000      0\n00214     131460.000      0\n00250        939.000      0\n00231094    1502.000      0\n02237       2254.000      0\n02559642    5634.000      0\n02354712    5634.000      0\n02217       7512.000      0\n02218       9390.000      0\n02559591    1409.000      0\n03217       9390.000      0\n03470873    9390.000      0\n04216       5634.000      0\n34231094    1502.000      0\n34628824    4507.000      0\n00598227    1972.000      0\n0017015    17653.000      0\n00562397    1014.000      0\n0017017     3564.000      0\n0017018    11446.000      0\n00576312    1362.000      0";


		fwrite($myfile, $txt);
		rewind($myfile);   // reset file pointer so as output file from the beginning

		// `basename` and `filesize` accept path to file, not file descriptor
		header('Cache-Control: public');
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename='.$myfile);
		header('Content-Type: text/plain');
		header('Content-Transfer-Encoding: binary');
		//readfile($file);

		//fpassthru($file);



	//echo $txt;die;  $file_name = "NORSEL-IMPORT_".$userid."_" . $i;
	/*foreach (glob($user_id."*.txt") as $filenames)
	{
		$zip->addFile($file_folder.$filenames);
	}
	$zip->close();*/

	foreach (glob($user_id."*.txt") as $filename)
	{
		@unlink($filename);
	}
echo $path;
	//echo $requ_noA."_".$user_id; //str_replace(".zip","",$file_folder);//"norsel_bundle";
	exit();
}

if ($action == "report_sendtoprint_text_file2")
{
	// var_dump($_REQUEST);
	$data = explode("***", $data);
	$update_id=$data[0];
	$recipe_no=$data[1];
	$batch_no=$data[2];
	$batch_id=$data[3];

	/*$content = "some text here";
	$fp = fopen($_SERVER['DOCUMENT_ROOT'] . "inventory/chemical_dyes/requires/myText.txt","wb");
	fwrite($fp,$content);
	fclose($fp);

	echo "myText";*/

	$file_name =$user_id."_" . $i;
		$created_files[]=$file_name.".txt";
		$myfile = fopen($file_name . ".txt", "w") or die("Unable to open file!");
		$txt = "";

 $path = "test.txt";
	$file = fopen($path, "w") or die("Unable to open file!");
	fwrite($file, "lorem ipsum");
	//fclose($file); // do not close the file
	rewind($file);   // reset file pointer so as output file from the beginning

	// `basename` and `filesize` accept path to file, not file descriptor
	header('Cache-Control: public');
	header('Content-Description: File Transfer');
	header('Content-Disposition: attachment; filename='.$file);
	header('Content-Type: text/plain');
	header('Content-Transfer-Encoding: binary');
	//readfile($file);

	//fpassthru($file);

	echo "test";
	foreach (glob($user_id."*.txt") as $filename)
		{
			@unlink($filename);
		}

	exit();
}


if ($action == "chemical_dyes_issue_requisition_without_rate_print_7")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	//print_r ($data);

	$sql = "select a.id, a.requ_no, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id from dyes_chem_issue_requ_mst a where a.id='$data[1]' and a.company_id='$data[0]'";
	$dataArray = sql_select($sql);
	$recipe_id = $dataArray[0][csf('recipe_id')];
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	//$entry_form_arr = return_library_array("select id, entry_form from pro_batch_create_mst", "id", "entry_form");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");

	$batch_weight = 0;
	if ($db_type == 0) {
		$data_array = sql_select("select group_concat(buyer_id) as buyer_id,group_concat(id) as recipe_id, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor, group_concat(concat_ws(':',batch_ratio,liquor_ratio) order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, group_concat(case when entry_form<>60 then batch_id end) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
	} else {
		$data_array = sql_select("select listagg(buyer_id,',') within group (order by buyer_id) as buyer_id,listagg(id,',') within group (order by id) as recipe_id, listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor, listagg(batch_ratio || ':' || liquor_ratio,',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(case when entry_form<>60 then batch_id end,',') within group (order by batch_id) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");
	}
	$batch_weight = $data_array[0][csf("batch_weight")];
	$batch_id = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id")])));

	$booking_no_sql="select  id,entry_form,booking_no,booking_without_order from pro_batch_create_mst where id in($batch_id) and is_deleted=0 and status_active=1 ";
	$booking_no_arr=sql_select($booking_no_sql);
	foreach ($booking_no_arr as $row){
        $booking_arr[$row[csf("booking_no")]]=$row[csf("booking_no")];
		 $booking_no=$row[csf("booking_no")];
		 $booking_without_order=$row[csf("booking_without_order")];
		 $entry_form_arr[$row[csf("id")]]=$row[csf("entry_form")];
    }

	$batch_id_rec_main = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id_rec_main")])));
	if ($batch_id_rec_main == "") $batch_id_rec_main = 0;

	if ($db_type == 0) {
		$batchdata_array = sql_select("select group_concat(batch_no) as batch_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(distinct color_id) as color_id from pro_batch_create_mst where id in($batch_id)");
	} else {
		$batchdata_array = sql_select("select listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, listagg(color_id ,',') within group (order by id) as color_id, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($batch_id)");
	}

	$po_no = '';
	$job_no = '';$prod_ids = '';$po_ids = '';
	$buyer_name = '';
	$style_ref_no=$file_no=$int_ref_no= '';
    foreach (explode(",", $data_array[0][csf("batch_id")]) as $b_id) {
        if ($entry_form_arr[$b_id] == 136) {
            $po_data = sql_select("select distinct b.po_number,b.id as po_id, b.file_no, b.grouping, c.job_no,c.style_ref_no, c.buyer_name from pro_batch_create_mst a, wo_po_break_down b, wo_po_details_master c where a.job_no=b.job_no_mst and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.id in(" . $b_id . ") ");
            foreach ($po_data as $row) {
                $po_no .= $row[csf('po_number')] . ",";
                $job_no .= $row[csf('job_no')] . ",";

                if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
                if ($file_no == '') $file_no = $row[csf('file_no')]; else $file_no .= "," . $row[csf('file_no')];
                if ($int_ref_no == '') $int_ref_no = $row[csf('grouping')]; else $int_ref_no .= "," . $row[csf('grouping')];
            }
            foreach (explode(",", $data_array[0][csf("buyer_id")]) as $buyer_id) {
                $buyer_name .= $buyer_library[$buyer_id] . ",";
            }
        } else {
            $po_data = sql_select("SELECT distinct b.po_number,b.id as po_id, b.file_no, b.grouping, a.prod_id, c.job_no,c.style_ref_no, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ");
            foreach ($po_data as $row) {
                $po_no .= $row[csf('po_number')] . ",";
                $job_no .= $row[csf('job_no')] . ",";
                $prod_ids .= $row[csf('prod_id')] . ",";

                if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
                if ($file_no == '') $file_no = $row[csf('file_no')]; else $file_no .= "," . $row[csf('file_no')];
                if ($int_ref_no == '') $int_ref_no = $row[csf('grouping')]; else $int_ref_no .= "," . $row[csf('grouping')];
            }
            foreach (explode(",", $data_array[0][csf("buyer_id")]) as $buyer_id) {
                $buyer_name .= $buyer_library[$buyer_id] . ",";
            }

        }
    }
	$all_prod_ids=rtrim($prod_ids,',');
	$all_prod_ids = implode(",", array_unique(explode(",", $all_prod_ids)));
	if($all_prod_ids!='') $all_prod_ids=$all_prod_ids;else $all_prod_ids=0;

	$sql_yarn_dtls = "select  c.po_breakdown_id as po_id,d.yarn_lot as yarn_lot,d.yarn_count as yarn_count,d.brand_id as brand_id,d.prod_id
	from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
	where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id
 	and a.id in($batch_id) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22) group by  c.po_breakdown_id,d.yarn_lot,d.yarn_count,d.brand_id,d.prod_id";
	//echo $sql_yarn_dtls;
	$yarn_dtls_array = array();
	$result_sql_yarn_dtls = sql_select($sql_yarn_dtls);
	foreach ($result_sql_yarn_dtls as $row) {
		$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
		$count_name = "";
		foreach ($yarn_count as $val) {
			if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
		}
		$yarn_dtls_array[$row[csf('prod_id')]]['yarn_lot'] .=$row[csf('yarn_lot')].',';
		$yarn_dtls_array[$row[csf('prod_id')]]['brand_name'] .= $brand_arr[$row[csf('brand_id')]].',';
		$yarn_dtls_array[$row[csf('prod_id')]]['count_name'] .=$count_arr[$yarn_count].',';
	}
	//var_dump($yarn_dtls_array);
	$po_no = implode(", ", array_unique(explode(",", substr($po_no, 0, -1))));
	$job_no = implode(", ", array_unique(explode(",", substr($job_no, 0, -1))));
	$buyer_name = implode(",", array_unique(explode(",", substr($buyer_name, 0, -1))));
	$color_id = array_unique(explode(",", $batchdata_array[0][csf('color_id')]));
	$color_name = '';
	foreach ($color_id as $color) {
		$color_name .= $color_arr[$color] . ",";
	}
	$color_name = substr($color_name, 0, -1);
	//var_dump($recipe_color_arr);

	$recipe_id=$data_array[0][csf("recipe_id")];
	$recipe_arr=sql_select("SELECT a.recipe_type,a.working_company_id,a.company_id, b.batch_weight from pro_recipe_entry_mst a, pro_batch_create_mst b where a.batch_id=b.id and a.id in ($recipe_id)");
	$owner_company_id=$recipe_arr[0][csf('company_id')];
	$recipe_type=$recipe_arr[0][csf('recipe_type')];
	//echo $recipe_type.'DD';
	?>
	<style>
		.borderless{
			border-left:none;
			border-right:none;
		}
		.borderlessall{
			border:none;
		}
	</style>
	<div style="width:1000px;">
	<!-- position: absolute;top: 25px; -->
		<table width="950" cellspacing="0" align="center">
			<tr>
				<td colspan="4" align="center" width="700" style="font-size:x-large"><strong><? echo $company_library[$owner_company_id]; ?></strong></td>
				<td colspan="2" align="center" ><div style="text-align: right;"><span id="show_barcode_image"></span></div></td>
			</tr>
		</table>
		<table width="950" cellspacing="0" align="center">
			<tr>
				<td colspan="6" align="center" style="font-size : 18px; padding-bottom: 16px;"><strong><?=$data[2]." Report";?></strong></td>
			</tr>
			<tr>
				<td width="90"><strong>Buyer</strong></td>
				<td width="160px"><? echo implode(",", array_unique(explode(",", $buyer_name))); ?></td>
				<td width="100"><strong>Req. Date</strong></td>
				<td width="160px"><? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
				<td width="90"><strong>Req. ID </strong></td>
				<td width="160px"><? echo $dataArray[0][csf('requ_no')]; ?></td>
			</tr>
			<!-- <tr>
				<td><strong>Order No</strong></td>
				<td><? echo $po_no; ?></td>
				<td><strong>Job No</strong></td>
				<td><? echo $job_no; ?></td>
				<td><strong>Issue Basis</strong></td>
				<td><? echo $receive_basis_arr[$dataArray[0][csf('requisition_basis')]]; ?></td>
			</tr> -->
			<tr>
				<td><strong>Style Ref.</strong></td>
				<td><p><?echo implode(",", array_unique(explode(",", $style_ref_no)));?></p></td>
				<td><strong>File No</strong></td>
				<td><? echo implode(",", array_unique(explode(",", $file_no)));?></td>
				<td><strong>Int. Ref. No</strong></td>
				<td><? echo implode(",", array_unique(explode(",", $int_ref_no))); ?></td>

			</tr>
			<tr>
				<td><strong>Batch No</strong></td>
				<td><? echo $batchdata_array[0][csf('batch_no')]; ?></td>
				<td><strong>Batch Weight</strong></td>
				<td><? $tot_bathc_weight=$batch_weight + $batchdata_array[0][csf('batch_weight')];echo $tot_bathc_weight; ?></td>
				<td><strong>Color</strong></td>
				<td><? echo $color_name; ?></td>
			</tr>
			<tr>
				<td><strong>LD No</strong></td>
				<td>
                    <?
                    $recipe_entry_data = sql_select("SELECT LABDIP_NO,COLOR_RANGE from pro_recipe_entry_mst where id in (".$dataArray[0][csf('recipe_id')].") order by id asc");
                    $color_range_temp = array();
                    $lab_dip_temp = array();
                    foreach ($recipe_entry_data as $value){
                        array_push($lab_dip_temp, $value['LABDIP_NO'] );
                        array_push($color_range_temp, $color_range[$value['COLOR_RANGE']] );
                    }
                    echo implode(',', array_unique($lab_dip_temp));
                    ?>
				</td>
				<!-- <td><? echo $data_array[0][csf("recipe_id")]; ?></td> -->
				<td><strong>Machine No</strong></td>
				<td>
					<?
					$machine_data = sql_select("select machine_no, floor_id from lib_machine_name where id='" . $dataArray[0][csf("machine_id")] . "'");
					echo $machine_data[0][csf('machine_no')];
					?>
				</td>
				<td><strong>Color Range</strong></td>
				<td>
                    <?
                    echo implode(',', array_unique($color_range_temp));
                    ?>
				</td>
			</tr>
			<!-- <tr>
				<td>Method</td>
				<td><? echo $dyeing_method[$dataArray[0][csf("method")]]; ?></td>

				<td><strong>Booking No</strong></td>
				<td><p><? echo implode(',',$booking_arr); ?></p> </td>
			</tr> -->
		</table>
		<br>
		<?
		$batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
		$j = 1;
		$entryForm = $entry_form_arr[$batch_id_qry[0]];
		?>
		<table width="1000" cellspacing="0" align="center" class="rpt_table" border="1" rules="all">
			<thead bgcolor="#dddddd" align="center">
				<?
				if ($entryForm == 0)
				{
					?>
					<!-- <tr bgcolor="#CCCCFF">
						<th colspan="8" align="center"><strong>Fabrication</strong></th>
					</tr> -->
					<tr>
						<th class="borderless" width="30">SL</th>
						<th class="borderless" width="100">Dia/ W. Type</th>
						<th class="borderless" width="100">Yarn Lot</th>
						<th class="borderless" width="100">Brand</th>
						<th class="borderless" width="100">Count</th>
						<th class="borderless" width="300">Con & Com</th>
						<th class="borderless" width="70">Gsm</th>
						<th class="borderless" width="70">Dia</th>
					</tr>
					<?
				}
				else
				{
					?>
					<!-- <tr bgcolor="#CCCCFF">
						<th colspan="4" align="center"><strong>Fabrication</strong></th>
					</tr> -->
					<tr>
						<th class="borderless" width="50">SL</th>
						<th class="borderless" width="350">Gmts. Item</th>
						<th class="borderless" width="110">Gmts. Qty</th>
						<th class="borderless">Batch Qty.</th>
					</tr>
					<?
				}
				?>
			</thead>
			<tbody>
				<?
				foreach ($batch_id_qry as $b_id)
				{
					$batch_query = "Select  po_id, prod_id, item_description, width_dia_type, count(roll_no) as gmts_qty, sum(batch_qnty) batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by po_id, prod_id, item_description, width_dia_type";
					$result_batch_query = sql_select($batch_query);
					foreach ($result_batch_query as $rows)
					{
						if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						$fabrication_full = $rows[csf("item_description")];
						$fabrication = explode(',', $fabrication_full);

						$yarn_lot=rtrim($yarn_dtls_array[$rows[csf('prod_id')]]['yarn_lot'],',');
						$brand_name=rtrim($yarn_dtls_array[$rows[csf('prod_id')]]['brand_name'],',');
						$count_name=rtrim($yarn_dtls_array[$rows[csf('prod_id')]]['count_name'],',');
						//echo $entry_form_arr[$b_id].'DDD';
						if ($entry_form_arr[$b_id] == 36)
						{

							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td class="borderlessall" align="center"><? echo $j; ?></td>
								<td class="borderlessall" align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
								<td class="borderlessall" align="center"><? echo implode(", ", array_unique(explode(",", $yarn_lot)));?></td>
								<td class="borderlessall" align="center"><? echo implode(", ", array_unique(explode(",", $brand_name)));?></td>
								<td class="borderlessall" align="center"><? echo implode(", ", array_unique(explode(",", $count_name)));?></td>
								<td class="borderlessall" align="left"><? echo $fabrication[0] ?></td>
								<td class="borderlessall" align="center"><? echo $fabrication[1]; ?></td>
								<td class="borderlessall" align="center"><? echo $fabrication[3]; ?></td>
							</tr>
								<?
						}
						else if ($entry_form_arr[$b_id] == 74)
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td class="borderlessall" align="center"><? echo $j; ?></td>
								<td class="borderlessall" align="left"><? echo $garments_item[$rows[csf('prod_id')]]; ?></td>
								<td class="borderlessall" align="right"><? echo $rows[csf('gmts_qty')]; ?></td>
								<td class="borderlessall" align="right"><? echo number_format($rows[csf('batch_qnty')], 2); ?></td>
							</tr>
							<?
						}
						else
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td class="borderlessall" align="center"><? echo $j; ?></td>
								<td class="borderlessall" align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
								<td class="borderlessall" width="100" align="left"><p style="word-wrap:break-word;"><? echo implode(", ", array_unique(explode(",", $yarn_lot))); ?></td>
								<td class="borderlessall" align="left"><? echo implode(", ", array_unique(explode(",", $brand_name))); ?></td>
								<td class="borderlessall" align="left"><? echo implode(", ", array_unique(explode(",", $count_name))); ?></td>
								<td class="borderlessall" align="left"><? echo $fabrication[0] . ", " . $fabrication[1]; ?></td>
								<td class="borderlessall" align="center"><? echo $fabrication[2]; ?></td>
								<td class="borderlessall" align="center"><? echo $fabrication[3]; ?></td>
							</tr>
							<?
						}
						$j++;
					}
				}
				?>
			</tbody>
		</table>
        <div style="width:1000px; margin-top:10px">
        	<table align="right" cellspacing="0" width="1000" border="1" rules="all" class="rpt_table">
        		<thead bgcolor="#dddddd" align="center">
        			<!-- <tr bgcolor="#CCCCFF">
        				<th colspan="16" align="center"><strong>Dyes And Chemical Issue Requisition Without Rate</strong>
        				</th>
        			</tr> -->
        			<?
        			$sql_rec = "SELECT a.id,a.item_group_id, b.total_liquor,b.liquor_ratio,nvl(b.ratio, 0) as ratio,b.sub_process_id as sub_process_id,b.prod_id,b.dose_base,b.adj_type,b.comments,b.process_remark from pro_recipe_entry_dtls b,product_details_master a where a.id=b.prod_id and b.mst_id in($recipe_id) and a.item_category_id in(5,6,7,23) and b.status_active=1 and b.is_deleted=0";
//        			echo $sql_rec;
                    $nameArray = sql_select($sql_rec);
        			$process_array = array();
        			$process_array_remark = array();
        			foreach ($nameArray as $row) {
        				$process_array_liquor[$row[csf("sub_process_id")]][$row[csf("prod_id")]]['total_liquor'] = $row[csf("total_liquor")];
        				$process_array_liquor[$row[csf("sub_process_id")]][$row[csf("prod_id")]]['liquor_ratio'] = $row[csf("liquor_ratio")];
                        $process_array[$row[csf("sub_process_id")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]][number_format($row[csf("ratio")], 2)]["comments"] = $row[csf("comments")];

//        				$process_array[$row[csf("sub_process_id")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"] = $row[csf("comments")];
        			}
//                    print_r($process_array);
        			$sql_rec_remark = "SELECT b.sub_process_id as sub_process_id,b.comments,b.process_remark,b.total_liquor,b.liquor_ratio from pro_recipe_entry_dtls b  where  b.mst_id in($recipe_id) and b.sub_process_id in($subprocessforwashin) and b.status_active=1 and b.is_deleted=0";
        			$nameArray_re = sql_select($sql_rec_remark);
        			foreach ($nameArray_re as $row) {
        				$process_array_liquor[$row[csf("sub_process_id")]][$row[csf("prod_id")]]['total_liquor'] += $row[csf("total_liquor")];
        				$process_array_liquor[$row[csf("sub_process_id")]][$row[csf("prod_id")]]['liquor_ratio'] += $row[csf("liquor_ratio")];
        				$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"] = $row[csf("process_remark")];
        			}

        			$group_arr = return_library_array("SELECT id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0", 'id', 'item_name');
					$sql_dtls = "SELECT a.requ_no, a.batch_id, a.recipe_id, b.id, b.sub_process, b.item_category, b.dose_base, nvl(b.ratio, 0) as ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit, c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit, c.id as prod_id, b.item_lot, b.comments
					from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
					where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and c.stock_value>0 and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id=$data[1]
					union
					select a.requ_no, a.batch_id, a.recipe_id, b.id, b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit, null as item_description, null as  item_group_id, null as sub_group_name, null as item_size, null as  unit_of_measure,null as avg_rate_per_unit, null as prod_id, b.item_lot, b.comments
					from dyes_chem_issue_requ_mst a, dyes_chem_issue_requ_dtls b
					where a.id=b.mst_id and b.sub_process in ($subprocessforwashin) and a.id=$data[1]
					order by id";
					// echo $sql_dtls;//die;
					$sql_result = sql_select($sql_dtls);

					$sub_process_array = array();
					//var_dump($sub_process_tot_req_array);
					$i = 1;
					$k = 1;
					$recipe_qnty_sum = 0;
					$req_qny_edit_sum = 0;
					$recipe_qnty = 0;
					$req_qny_edit = 0;
					$req_value_sum = 0;
					$req_value_grand = 0;
					$recipe_qnty_grand = 0;
					$req_qny_edit_grand = 0;

					foreach ($sql_result as $row)
					{
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
						if (!in_array($row[csf("sub_process")], $sub_process_array))
						{
							$sub_process_array[] = $row[csf('sub_process')];
							/* if ($k != 1)
							{
								?>
								<tr>
									<td class="borderless" colspan="5" align="right"><strong>Total :</strong></td>
									<td class="borderless" align="right"><?php echo number_format($req_qny_edit_sum,3, '.', ''); ?></td>
									<td class="borderless">&nbsp;</td>

								</tr>
								<?
							}
								$recipe_qnty_sum = 0;
								$req_qny_edit_sum = 0;
								$req_value_sum = 0; */
								$k++;

								//if ($row[csf("sub_process")] == 93 || $row[csf("sub_process")] == 94 || $row[csf("sub_process")] == 95 || $row[csf("sub_process")] == 96 || $row[csf("sub_process")] == 97 || $row[csf("sub_process")] == 98) {
								 if(in_array($row[csf("sub_process")],$subprocessForWashArr))
								 {
									$pro_remark = $process_array_remark[$row[csf("sub_process")]]["wash_comments"];
								} else {
									$pro_remark = "";
								}
								if ($pro_remark != '') $pro_remark = "," . $pro_remark; else $pro_remark = "";
								$total_liquor = 'Total liquor(ltr)' . ": " . number_format($process_array_liquor[$row[csf("sub_process")]][$row[csf("prod_id")]]['total_liquor'],3);
								$liquor_ratio = 'Liquor Ratio' . ": " . $process_array_liquor[$row[csf("sub_process")]][$row[csf("prod_id")]]['liquor_ratio'];
								$liquor_ratio_process=$process_array_liquor[$row[csf("sub_process")]][$row[csf("prod_id")]]['liquor_ratio'];
								$leveling_water=$tot_bathc_weight*($liquor_ratio_process-1.5);
								?>
								<tr bgcolor="#CCCCCC">
									<th colspan="7" title="Leveling=Tot Batch Weight*(Liquor Ratio-1.5)">
									<strong><? echo $dyeing_sub_process[$row[csf("sub_process")]]. ', ' .$liquor_ratio.', ' .$total_liquor . $pro_remark.', '.'Levelling  Water(Ltr): '.$leveling_water; ?></strong>
									</th>
								</tr>
								<?
								if($i==1)
								{
									?>
									<tr>
										<th class="borderless" width="30">SL</th>
										<th class="borderless" width="250">Item Description</th>
										<th class="borderless" width="100">Lot</th>
										<th class="borderless" width="100">Dose</th>
										<th class="borderless" width="80">Ratio</th>
										<th class="borderless" width="100">Iss Qty(Gm)</th>
										<th class="borderless" >Comments</th>
									</tr>
									<?
								}?>
							</thead>
							<?
						}
                            $comment = $process_array[$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]][number_format($row[csf("ratio")], 2)]["comments"];


                            //$comment = $process_array[$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"];
						if($row[csf("unit_of_measure")]==12)
						{
							$iss_qnty_gm=$row[csf("req_qny_edit")]*1000;
						}
						else
						{
							$iss_qnty_gm=$row[csf("req_qny_edit")];
						}
						?>
						<tbody>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td class="borderlessall" align="center"><? echo $i; ?></td>
								<td class="borderlessall"><? echo $row[csf("item_description")] . ' ' . $row[csf("item_size")]; ?></td>
								<td class="borderlessall"><? echo $row[csf("item_lot")]; ?></td>
								<td class="borderlessall"><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
								<td class="borderlessall" align="center"><? echo number_format($row[csf("ratio")], 6, '.', ''); ?></td>
								<td class="borderlessall" align="right"><b><? echo number_format($iss_qnty_gm,3, '.', ''); ?></b></td>
								<td class="borderlessall" align="right"><? echo $row[csf("comments")]; ?></td>
							</tr>
						</tbody>
						<? $i++;
						$recipe_qnty_sum += $row[csf('recipe_qnty')];
						$req_qny_edit_sum += $iss_qnty_gm;
						$req_value_sum += $req_value;

						$recipe_qnty_grand += $row[csf('recipe_qnty')];
						$req_qny_edit_grand += $iss_qnty_gm;
						$req_value_grand += $req_value;
					}
				?>
				<!-- <tr>
					<td class="borderless" colspan="5" align="right"><strong>Total :</strong></td>
					<td class="borderless" align="right"><?php echo number_format($totval_req,3, '.', ''); ?></td>
					<td class="borderless">&nbsp;</td>
				</tr>
				<tr>
					<td class="borderless" colspan="5" align="right"><strong> Grand Total :</strong></td>
					<td class="borderless" align="right"><?php echo number_format($req_qny_edit_grand,3, '.', ''); ?></td>
					<td class="borderless">&nbsp;</td>
				</tr> -->
        	</table>
			<br>
		</div>
	</div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess ){
			var value = valuess;
			var btype = 'code39';
			var renderer ='bmp';
			var settings = {
				output:renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 50,
				moduleSize:5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			value = {code:value, rect: false};
            $("#show_barcode_image").barcode(value, btype, settings);
		}
		generateBarcode('<? echo $dataArray[0][csf('requ_no')]; ?>');
    </script>
	<?
	exit();
}


if ($action == "chemical_dyes_issue_requisition_print_scandex")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	//print ($data[3]);die;
	//print_r ($data);
	if($data[3]==7) // Print Scandex
	{
		//echo $data[4];
		if ($data[4]==1) // ok to open without issue value;
		{
			$sql = "select a.id, a.requ_no, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id
			from dyes_chem_issue_requ_mst a where a.id='$data[1]' and a.company_id='$data[0]'";
			$dataArray = sql_select($sql);

			$recipe_id = $dataArray[0][csf('recipe_id')];

			$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
			$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
			$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
			$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
			//$entry_form_arr = return_library_array("select id, entry_form from pro_batch_create_mst", "id", "entry_form");
			//$order_id_arr=return_library_array( "select id, booking_id from  pro_recipe_entry_mst", "id", "booking_id");
			//$order_no_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
			//$job_no_arr=return_library_array( "select id, job_no from wo_booking_mst", "id", "job_no");
			$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
			$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
			$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");

			$batch_weight = 0;
			if ($db_type == 0) {
				$data_array = sql_select("select group_concat(buyer_id) as buyer_id,group_concat(id) as recipe_id, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor, group_concat(concat_ws(':',batch_ratio,liquor_ratio) order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, group_concat(case when entry_form<>60 then batch_id end) as batch_id_rec_main, group_concat(labdip_no) as labdip_no from pro_recipe_entry_mst where id in($recipe_id)");
			} else {
				$data_array = sql_select("select listagg(cast(buyer_id as varchar(4000)),',') within group (order by buyer_id) as buyer_id, listagg(cast(id as varchar(4000)),',') within group (order by id) as recipe_id, sum(total_liquor) as total_liquor, listagg(cast(batch_ratio || ':' || liquor_ratio as varchar(4000)),',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(cast(case when entry_form<>60 then batch_id end as varchar(4000)),',') within group (order by batch_id) as batch_id_rec_main, listagg(cast(batch_id as varchar(4000)),',') within group (order by batch_id) as batch_id, listagg(cast(labdip_no as varchar(4000)), ',') within group (order by id) as labdip_no from pro_recipe_entry_mst where id in($recipe_id)");

				/*$data_array = sql_select("select listagg(buyer_id,',') within group (order by buyer_id) as buyer_id,listagg(id,',') within group (order by id) as recipe_id, listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor, listagg(batch_ratio || ':' || liquor_ratio,',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(case when entry_form<>60 then batch_id end,',') within group (order by batch_id) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");*/	// old query
			}
			$recipe_id_arr = explode(",",$recipe_id);
			$recipe_des = "";
			foreach($recipe_id_arr as $recipe)
			{
				$recipe_des_arr = sql_select("select recipe_description from pro_recipe_entry_mst where ID = '$recipe'");
				$recipe_des .= $recipe_des_arr[0]['RECIPE_DESCRIPTION'].",";
			}
			$recipe_description = rtrim($recipe_des, ",");

			$batch_weight = $data_array[0][csf("batch_weight")];
			$batch_id = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id")])));

			$batch_id_rec_main = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id_rec_main")])));
			if ($batch_id_rec_main == "") $batch_id_rec_main = 0;
			$booking_no_sql="select distinct booking_no, is_sales,id, entry_form from pro_batch_create_mst where id in($batch_id) and is_deleted=0 and status_active=1 ";
			$booking_no_arr=sql_select($booking_no_sql);
            // echo $booking_no_arr[0][csf('is_sales')];
            // echo "<pre>";print_r($booking_no_arr);die;
			foreach ($booking_no_arr as $row)
			{
		        $booking_arr[$row[csf("booking_no")]]=$row[csf("booking_no")];
				$entry_form_arr[$row[csf("id")]]=$row[csf("entry_form")];
		    }
					if ($db_type == 0) {
				$batchdata_array = sql_select("select group_concat(batch_no) as batch_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(distinct color_id) as color_id from pro_batch_create_mst where id in($batch_id)");
			} else {
				$batchdata_array = sql_select("select listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, listagg(color_id ,',') within group (order by id) as color_id, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight
					from pro_batch_create_mst where id in($batch_id)");
			}


			$po_no = '';
			$job_no = '';
			$buyer_name = '';
			$style_ref_no = '';
			foreach (explode(",", $data_array[0][csf("batch_id")]) as $b_id)
            {
                if ($booking_no_arr[0][csf('is_sales')]==1)
                {
                    $po_sql = "SELECT b.po_job_no, b.style_ref_no, b.customer_buyer as party_id, d.po_number
                    from pro_batch_create_mst a, fabric_sales_order_mst b, wo_booking_dtls c, wo_po_break_down d
                    where a.sales_order_id=b.id and b.booking_id=c.booking_mst_id and c.po_break_down_id=d.id and a.id in(" . $b_id . ")
                    and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.booking_type in(1,4)
                    group by b.po_job_no, b.style_ref_no, b.customer_buyer, d.po_number";
                    $po_data = sql_select($po_sql);
                    foreach ($po_data as $row)
                    {
                        $po_no .= $row[csf('po_number')] . ",";
                        $job_no .= $row[csf('po_job_no')] . ",";
                        if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
                        $buyer_name .= $buyer_library[$row[csf('party_id')]] . ",";
                    }
                }
                else
                {
                    if ($entry_form_arr[$b_id] == 36)
                    {
                        //echo "select distinct b.order_no,b.cust_style_ref, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(".$b_id.") ";
                        $po_data = sql_select("select distinct b.order_no,b.cust_style_ref, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ");
                        foreach ($po_data as $row) {
                            $po_no .= $row[csf('order_no')] . ",";
                            $job_no .= $row[csf('subcon_job')] . ",";
                            if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
                            $buyer_name .= $buyer_library[$row[csf('party_id')]] . ",";
                        }
                    }
                    else
                    {
                        //echo "select distinct b.po_number, c.job_no,c.style_ref_no, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(".$b_id.") ";
                        $po_data = sql_select("select distinct b.po_number, c.job_no,c.style_ref_no, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ");
                        $job_nos="";
                        foreach ($po_data as $row)
                        {
                            $po_no .= $row[csf('po_number')] . ",";
                            $job_no .= $row[csf('job_no')] . ",";
                            //$job_nos .= $row[csf('job_no')] . ",";
                            if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
                            if ($job_nos == '') $job_nos ="'".$row[csf('job_no')]."'"; else $job_nos.= ","."'".$row[csf('job_no')]."'";
                            //$buyer_name.=$buyer_library[$row[csf('buyer_name')]].",";
                        }
                        foreach (explode(",", $data_array[0][csf("buyer_id")]) as $buyer_id) {
                            $buyer_name .= $buyer_library[$buyer_id] . ",";
                        }
                    }
                }

			}
			$jobNos=rtrim($job_nos,',');
			$jobnos=rtrim($job_no,',');

			//echo "select job_no, exchange_rate from wo_pre_cost_mst $job_nos_con";
			if($job_nos!='')
			{
				if($job_nos!='') $job_nos_con="where  job_no in(".$job_nos.")";//else $job_nos_con="where  job_no in('')";
				//echo "select job_no, exchange_rate from wo_pre_cost_mst $job_nos_con";
				$exchange_rate_arr = return_library_array("select job_no, exchange_rate from wo_pre_cost_mst $job_nos_con", 'job_no', 'exchange_rate');
			}
			//print_r($exchange_rate_arr);
			if($job_nos!='')
			{
                $condition= new condition();
                $condition->company_name("=$data[0]");
                if(str_replace("'","",$job_nos) !=''){
                    $condition->job_no("in($job_nos)");
                }
                $condition->init();

                $conversion= new conversion($condition);

                //echo $conversion->getQuery(); die;
                $conversion_qty_arr_process=$conversion->getQtyArray_by_jobAndProcess();
                $conversion_costing_arr_process=$conversion->getAmountArray_by_jobAndProcess();
			}
			// print_r($conversion_costing_arr_process);
			$dyeing_charge_arr=array(25,26,30,31,32,33,34,39,60,62,63,64,65,66,67,68,69,70,71,82,83,84,85,86,89,90,91,92,93,94,129,135,136,137,138,139,140,141,142,143,146);
			$jobnos=array_unique(explode(",",$jobnos));
			$tot_avg_dyeing_charge=0;$avg_dyeing_charge=0;$tot_dyeing_qty=$tot_dyeing_cost=0;//$tot_dyeing_cost=0;$tot_dyeing_qty=0;
			foreach($jobnos as $job)
			{
				$exchange_rate=$exchange_rate_arr[$job];

				foreach($dyeing_charge_arr as $process_id)
				{

					$dyeing_cost=array_sum($conversion_costing_arr_process[$job][$process_id]);
					//echo $job.'=='.$dyeing_cost.'='.$exchange_rate.'<br/>';
					$totdyeing_cost=$dyeing_cost;


					if($totdyeing_cost>0)
					{

						$tot_dyeing_qty+=array_sum($conversion_qty_arr_process[$job][$process_id]);
						$tot_dyeing_cost+=$dyeing_cost;
						//echo $tot_dyeing_cost.'='.$tot_dyeing_qty.'**'.$exchange_rate.'**'.$process_id.'<br>';
						//$avg_dyeing_charge+=$tot_dyeing_cost/$tot_dyeing_qty;
					}
				}
				//echo $tot_dyeing_cost.'<br>'.$tot_dyeing_qty;
				$avg_dyeing_charge+=$tot_dyeing_cost/$tot_dyeing_qty;
				$tot_avg_dyeing_charge+=$avg_dyeing_charge*$exchange_rate;
			}

			//echo $avg_dyeing_charge.'='.$tot_dyeing_cost.'='.$dyeing_cost;
			//echo $style_ref_no;
			/*if ($db_type==0)
			{
				$sql_yarn_dtls="select b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
			}
			elseif ($db_type==2)
			{
				$sql_yarn_dtls="select b.po_breakdown_id, b.prod_id, listagg((cast(a.yarn_lot as varchar2(500))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(a.brand_id,',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(500))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
			}*/

			/*---------------------------------------------------------------my close
			if ($db_type == 0)
			{
				$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
			}
			else if ($db_type == 2)
			{
				/*echo $sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, listagg((cast(a.yarn_lot as varchar2(4000))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(a.brand_id,',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(4000))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id";*/

			/*	$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, a.yarn_lot, a.brand_id, a.yarn_count
					from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b
					where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
					group by b.po_breakdown_id, b.prod_id , a.yarn_lot, a.brand_id, a.yarn_count";
			}
			*/  //----------------------------------------------------- my close ending
			$sql_yarn_dtls = "select  c.po_breakdown_id as po_id,d.yarn_lot as yarn_lot,d.yarn_count as yarn_count,d.brand_id as brand_id,d.prod_id
			from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
			where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id
			and a.id in($batch_id) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)  ";
			//echo $sql_yarn_dtls; die;

			$yarn_dtls_array = array();
			$result_sql_yarn_dtls = sql_select($sql_yarn_dtls);
			foreach ($result_sql_yarn_dtls as $row)
			{
				//$yarn_lot = implode(", ", array_unique(explode(",", $row[csf('yarn_lot')])));
				$yarn_id = array_unique(explode(",", $row[csf('yarn_lot')]));
				$yarn_lot = "";
				foreach ($yarn_id as $val) {
					if ($yarn_lot == "") $yarn_lot = $val; else $yarn_lot .= ", " . $val;
				}

				$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
				$brand_name = "";
				foreach ($brand_id as $val) {
					if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= ", " . $brand_arr[$val];
				}

				$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
				$count_name = "";
				foreach ($yarn_count as $val) {
					if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
				}
				$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['yarn_lot'] .= $row[csf('yarn_lot')].',';
				$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['brand_name'] .=  $brand_arr[$row[csf('brand_id')]].',';
				$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['count_name'] .= $count_arr[$row[csf('yarn_count')]].',';
			}
			//var_dump($yarn_dtls_array);
			$po_no = implode(", ", array_unique(explode(",", substr($po_no, 0, -1))));
			$job_no = implode(", ", array_unique(explode(",", substr($job_no, 0, -1))));
			$buyer_name = implode(",", array_unique(explode(",", substr($buyer_name, 0, -1))));
			$color_id = array_unique(explode(",", $batchdata_array[0][csf('color_id')]));
			$color_name = '';
			foreach ($color_id as $color) {
				$color_name .= $color_arr[$color] . ",";
			}
			$color_name = substr($color_name, 0, -1);
			//var_dump($recipe_color_arr);
			$job_no_arr = explode(",", $job_no);
			$int_ref = "";
			foreach($job_no_arr as $job)
			{
				$job = trim($job);
				$internal_ref_arr = sql_select("select GROUPING from WO_PO_BREAK_DOWN where JOB_NO_MST = '$job'");
				$int_ref .= $internal_ref_arr[0]['GROUPING'].",";
			}
			$int_ref = rtrim($int_ref, ",");

			//$recipe_id=$data_array[0][csf("recipe_id")];
			$recipe_arr=sql_select("select a.working_company_id,a.company_id, b.batch_weight from pro_recipe_entry_mst a, pro_batch_create_mst b where a.batch_id=b.id and a.id=$recipe_id");
			$owner_company_id=$recipe_arr[0][csf('company_id')];
			$working_company_id=$recipe_arr[0][csf('working_company_id')];
			$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
			$group_id=$nameArray[0][csf('group_id')];
			?>
			<div style="width:950px;">

				        <table width="950" cellspacing="0" align="center">
						    <tr>
							    <td align="center" style="font-size:x-large;width: 680px;"><strong><u><? echo $company_library[$working_company_id].'<br>'."Dyes And Chemical Issue Requisition"//.$data[2]; ?>
								Report</u></strong></td>
                                <td><span id="show_barcode_image"></span></td>
							</tr>
						</table>
						<table align="center" cellspacing="0" width="950" border="1" rules="all" class="rpt_table">
							<tr>
								<td border="1"><strong>Booking No </strong></td>
								<td><? echo implode(',',$booking_arr); ?></td>
								<td><strong>Order No</strong></td>
								<td><? echo $po_no; ?></td>
								<td><strong>Req. Date</strong></td>
								<td><? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
							</tr>
							<tr>
								<td><strong>Buyer</strong></td>
								<td><? echo implode(",", array_unique(explode(",", $buyer_name))); ?></td>
								<td><strong>Batch No</strong></td>
								<td><? echo $batchdata_array[0][csf('batch_no')]; ?></td>
								<td><strong>Batch Weight</strong></td>
								<td><strong><? $tot_batch_weight=$batch_weight+$batchdata_array[0][csf('batch_weight')];echo number_format($tot_batch_weight,4); ?></strong></td>
							</tr>
							<tr>
								<td><strong>Color</strong></td>
								<td><? echo $color_name; ?></td>
								<td><strong>Lab Dip</strong></td>
								<td>
	        						<?
	        						/*$labdip_no = '';
	        						$recipe_ids = explode(",", $data_array[0][csf("recipe_id")]);
	        						foreach ($recipe_ids as $recp_id)
                                    {
	        							$labdip_no .= $receipe_arr[$recp_id] . ",";
	        						}*/
                                    $labdip_no=implode(",", array_unique(explode(",", $data_array[0][csf("LABDIP_NO")])));
	        						echo chop($labdip_no, ',');
	        						// or-> echo $data_array[0][csf("labdip_no")];
	        						?>
	        					</td>
								<td><strong>Machine No</strong></td>
								<td>
									<?
									$machine_data = sql_select("select machine_no, floor_id from lib_machine_name where id='" . $dataArray[0][csf("machine_id")] . "'");
									echo $machine_data[0][csf('machine_no')];
									?>
								</td>
							</tr>
							<tr>
		                        <td><strong>Style Ref.</strong></td>
								<td><p><? echo implode(",", array_unique(explode(",", $style_ref_no))); ?></p></td>
								<td><strong>Req. ID </strong></td>
								<td><? echo $dataArray[0][csf('requ_no')]; ?></td>
								<td border="1"><strong>Job No </strong></td>
								<td><? echo $job_no; ?></td>
							</tr>
							<tr>
		                        <td><strong>Recipe Des.</strong></td>
								<td><p><? echo $recipe_description; ?></p></td>
								<td><strong>Internal Ref No </strong></td>
								<td><? echo $int_ref; ?></td>
								<td border="1"><strong></strong></td>
								<td></td>
							</tr>
					    </table>
		            <script type="text/javascript" src="../../js/jquery.js"></script>
    				<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    		        <script>
		                function generateBarcode( valuess ){
		                        var value = valuess;
		                        var btype = 'code39';
		                        var renderer ='bmp';
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
		                        $("#show_barcode_image").show().barcode(value, btype, settings);
		                    }
		                   generateBarcode('<? echo $dataArray[0][csf('requ_no')]; ?>');
    		        </script>


					<br>
					<?
					$batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
					$j = 1;
					$entryForm = $entry_form_arr[$batch_id_qry[0]];
					?>
					<table width="950" cellspacing="0" align="center" class="rpt_table" border="1" rules="all">
						<thead bgcolor="#dddddd" align="center">
							<?
							if ($entryForm == 74)
							{
								?>
								<tr bgcolor="#CCCCFF">
									<th colspan="4" align="center"><strong>Fabrication</strong></th>
								</tr>
								<tr>
									<th width="50">SL</th>
									<th width="350">Gmts. Item</th>
									<th width="110">Gmts. Qty</th>
									<th>Batch Qty.</th>
								</tr>
								<?
							}
							else
							{
								?>
								<tr bgcolor="#CCCCFF">
									<th colspan="9" align="center"><strong>Fabrication</strong></th>
								</tr>
								<tr>
									<th width="30">SL</th>
									<th width="100">Dia/ W. Type</th>
									<th width="100">Yarn Lot</th>
									<th width="100">Brand</th>
									<th width="100">Count</th>
									<th width="300">Constrution & Composition</th>
									<th width="70">Gsm</th>
									<th width="70">Dia</th>
									<th width="70">Qty</th>
								</tr>
								<?
							}
							?>
						</thead>
						<tbody>
							<?


							foreach ($batch_id_qry as $b_id)
							{
								$batch_query = "select po_id, prod_id, item_description, width_dia_type, sum(roll_no)  as gmts_qty, sum(batch_qnty) as  batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by po_id, prod_id, item_description, width_dia_type ";
								//echo $batch_query;
								//echo "<pre>";print_r($yarn_dtls_array);
								$result_batch_query = sql_select($batch_query);
								foreach ($result_batch_query as $rows)
								{
									if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
									$fabrication_full = $rows[csf("item_description")];

									$fabrication = explode(',', $fabrication_full);
									if ($entry_form_arr[$b_id] == 36)
									{
										?>
										<tr bgcolor="<? echo $bgcolor; ?>">
											<td align="center"><? echo $j; ?></td>
											<td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
				                            <td align="center"><? echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['yarn_lot'];
				                            	?></td>
				                            <td align="center"><? echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['brand_name'];
				                            	?></td>
				                            <td align="center"><? echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['count_name'];
				                            	?></td>
			                            	<td align="left"><? echo $fabrication[0]; ?></td>
			                            	<td align="center"><? echo $fabrication[1]; ?></td>
			                            	<td align="center"><? echo $fabrication[3]; ?></td>
											<td align="right"><? echo number_format($rows[csf('batch_qnty')], 2); ?></td>
			                            </tr>
		                            <?
									}
									else if ($entry_form_arr[$b_id] == 74)
									{
										?>
										<tr bgcolor="<? echo $bgcolor; ?>">
											<td align="center"><? echo $j; ?></td>
											<td align="left"><? echo $garments_item[$rows[csf('prod_id')]]; ?></td>
											<td align="right"><? echo $rows[csf('gmts_qty')]; ?></td>
											<td align="right"><? echo number_format($rows[csf('batch_qnty')], 2); ?></td>
										</tr>
										<?
									}
									else
									{
									//echo $entry_form_arr[$b_id].'aaa';
										?>
										<? //echo $yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['yarn_lot']; ?>
										<tr bgcolor="<? echo $bgcolor; ?>">
											<td align="center"><? echo $j; ?></td>
											<td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
											<td width="100" align="left"><p style="word-break:break-all;"><? echo $yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['yarn_lot']; ?></p></td>
											<td align="left"><? echo $yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['brand_name']; ?></td>
											<td align="left"><? echo $yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['count_name']; ?></td>
											<td align="left"><? echo $fabrication[0] . ", " . $fabrication[1]; ?></td>
											<td align="center"><? echo $fabrication[2]; ?></td>
											<td align="center"><? echo $fabrication[3]; ?></td>
											<td align="right"><? echo number_format($rows[csf('batch_qnty')], 2); ?></td>
										</tr>
										<?
									}
									$j++;
								}
							}


		                ?>
		            </tbody>
		            </table>

		        <div style="width:950px; margin-top:10px">
		        	<table align="center" cellspacing="0" width="950" border="1" rules="all" class="rpt_table">
		        		<thead bgcolor="#dddddd" align="center">
		        			<tr bgcolor="#CCCCFF">
		        				<th colspan="19" align="center"><strong>Dyes And Chemical Issue Requisition</strong></th>
		        			</tr>
		        		</thead>
		        		<?


			/*$sql_rec="select a.id, a.item_group_id, b.sub_process_id as sub_process_id, b.prod_id, b.dose_base, b.adj_type, b.comments, b.process_remark
			 from pro_recipe_entry_dtls b left join product_details_master a on b.prod_id=a.id and a.item_category_id in(5,6,7,23)
			 where b.mst_id in($recipe_id)  and b.status_active=1 and b.is_deleted=0 ";*/

			 $group_arr = return_library_array("select id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0", 'id', 'item_name');
			 $sql_rec = "select b.sub_process_id as sub_process_id, b.prod_id, b.dose_base, b.adj_type, b.comments, b.process_remark, b.item_lot
			 from pro_recipe_entry_dtls b
			 where b.mst_id in($recipe_id)  and b.status_active=1 and b.is_deleted=0";
			 $nameArray = sql_select($sql_rec);
			 foreach ($nameArray as $row) {
			 	$all_prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
			 	$item_lot_arr[$row[csf("prod_id")]] .= $row[csf("item_lot")] . ",";
			 }

			 $item_grop_id_arr = return_library_array("select id, item_group_id from product_details_master where id in(" . implode(",", $all_prod_id_arr) . ")", 'id', 'item_group_id');

			 $process_array = array();
			 $process_array_remark = array();
			 foreach ($nameArray as $row) {
			 	$process_array[$row[csf("sub_process_id")]] = $row[csf("process_remark")];
			 	$process_array_remark[$row[csf("sub_process_id")]][$row[csf("prod_id")]][$item_grop_id_arr[$row[csf("prod_id")]]][$row[csf("dose_base")]]["comments"] = $row[csf("comments")];
			 	if ($row[csf("sub_process_id")] > 92 && $row[csf("sub_process_id")] < 99) {
			 		$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"] = $row[csf("process_remark")];
			 	}

			 }

			/*$sql_rec_remark="select b.sub_process_id as sub_process_id,b.comments,b.process_remark from pro_recipe_entry_dtls b  where  b.mst_id in($recipe_id) and b.sub_process_id in(93,94,95,96,97,98) and b.status_active=1 and b.is_deleted=0";

			$nameArray_re=sql_select( $sql_rec_remark );
			foreach($nameArray_re as $row)
			{
				$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"]=$row[csf("process_remark")];
			}*/


			/*if($db_type==0)
			{
				$item_lot_arr=return_library_array("select b.prod_id,group_concat(b.item_lot) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot" );
			}
			else
			{
				$item_lot_arr=return_library_array("select b.prod_id,listagg((cast(b.item_lot as varchar2(500))),',') within group (order by b.prod_id) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot" );
			}



			$sql_dtls = "select a.requ_no, a.batch_id, a.recipe_id, b.id,
			b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
			c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id
			from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
			where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id=$data[1] order by b.id, b.seq_no";*/

			$sql_dtls = "(select a.requ_no, a.batch_id, a.recipe_id, b.id,
			b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
			c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id, b.item_lot
			from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
			where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id=$data[1])
			union
			(
			select a.requ_no, a.batch_id, a.recipe_id, b.id,
			b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
			null as item_description, null as item_group_id,  null as sub_group_name, null as item_size, null as unit_of_measure,null as avg_rate_per_unit,null as prod_id, b.item_lot
			from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b
			where a.id=b.mst_id and b.sub_process in($subprocessforwashin) and   a.id=$data[1]
			) order by id";
			// echo $sql_dtls;//die;
			$sql_result = sql_select($sql_dtls);

			$sub_process_array = array();
			$sub_process_tot_rec_array = array();
			$sub_process_tot_req_array = array();
			$sub_process_tot_value_array = array();

			foreach ($sql_result as $row) {
				$sub_process_tot_rec_array[$row[csf("sub_process")]] += $row[csf("recipe_qnty")];
				$sub_process_tot_req_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
				$sub_process_tot_value_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")] * $row[csf("avg_rate_per_unit")];
				//$sub_process_tot_ratio_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
			}
			$recipe_id=$data_array[0][csf("recipe_id")];
			$ratio_arr=array();
			$prevRatioData=sql_select( "select sub_process_id, total_liquor, liquor_ratio from pro_recipe_entry_dtls where mst_id=$recipe_id");
			foreach($prevRatioData as $prevRow)
			{
				$ratio_arr[$prevRow[csf('sub_process_id')]]['ratio']=$prevRow[csf('liquor_ratio')];
				$ratio_arr[$prevRow[csf('sub_process_id')]]['total_liquor']=$prevRow[csf('total_liquor')];
			}
						//var_dump($sub_process_tot_req_array);
			$i = 1;
			$k = 1;
			$recipe_qnty_sum = 0;
			$req_qny_edit_sum = 0;
			$recipe_qnty = 0;
			$req_qny_edit = 0;
			$req_value_sum = 0;
			$req_value_grand = 0;
			$recipe_qnty_grand = 0;
			$req_qny_edit_grand = 0;
			$fontst = "";
			$font_end = "";
			foreach ($sql_result as $row) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				if (!in_array($row[csf("sub_process")], $sub_process_array))
				{
					$sub_process_array[] = $row[csf('sub_process')];
					if ($k != 1) {
						?>
						<tr>
							<td colspan="6" align="right"><strong>Total :</strong></td>
							<td align="right"><strong><?php echo number_format($req_qny_edit_sum, 6, '.', ''); ?></strong></td>
						</tr>
						<?
					}
					$recipe_qnty_sum = 0;
					$req_qny_edit_sum = 0;
					$k++;

					//if ($row[csf("sub_process")] == 93 || $row[csf("sub_process")] == 94 || $row[csf("sub_process")] == 95 || $row[csf("sub_process")] == 96 || $row[csf("sub_process")] == 97 || $row[csf("sub_process")] == 98) {
						 if(in_array($row[csf("sub_process")],$subprocessForWashArr))
						 {
						$pro_remark = $process_array_remark[$row[csf("sub_process")]]["wash_comments"];
					}


					?>
					<tr bgcolor="#CCCCCC">
						<th colspan="19">
							<?

							$batch_ratio=explode(":",$data_array[0][csf("ratio")]);
							$lqr_ratio=$batch_ratio[0];
							$ratio_qty=$lqr_ratio.':'.$ratio_arr[$row[csf('sub_process')]]['ratio'];
							$total_liq = $ratio_arr[$row[csf('sub_process')]]['total_liquor'];
							$total_liquor_format = number_format($total_liq, 3, '.', '');
							$total_liquor='Total liquor(ltr)' . ": " . $total_liquor_format;
							if ($pro_remark == '') $pro_remark = ''; else $pro_remark .= $pro_remark . ', ';
							echo $dyeing_sub_process[$row[csf("sub_process")]] . ', ' . $pro_remark.'&nbsp;Liquor:Ratio &nbsp;'.$ratio_qty . ', ' .$total_liquor; ?>
						</th>
					</tr>
					<tr bgcolor="#EFEFEF">
						<th width="30">SL</th>
						<th width="150">Item Group</th>
                        <th width="70">Item Lot</th>
						<th width="180">Item Description</th>
						<th width="60">Ratio</th>
						<th width="70">GPL/%</th>
						<th width="80">Issue(KG)</th>
						<th width="50">Add-1</th>
						<th width="50">Add-2</th>
						<th width="50">Add-3</th>
						<th width="50">Add-4</th>
						<th>Total</th>
					</tr>
					<?
				}

							/*$iss_qnty=$row[csf("req_qny_edit")]*10000;
							$iss_qnty_kg=floor($iss_qnty/10000);
							$lkg=round($iss_qnty-$iss_qnty_kg*10000);
							$iss_qnty_gm=floor($lkg/10);
							$iss_qnty_mg=$lkg%10;*/
							/*$iss_qnty_gm=substr((string)$iss_qnty[1],0,3);
							$iss_qnty_mg=substr($iss_qnty[1],3);*/

							$req_qny_edit = explode(".", (string)$row[csf("req_qny_edit")]);
							$iss_qnty_kg = $req_qny_edit[0];
							if ($iss_qnty_kg == "") $iss_qnty_kg = 0;

							//$mg=($row[csf("req_qny_edit")]-$iss_qnty_kg)*1000*1000;
							//$iss_qnty_gm=floor($mg/1000);
							//$iss_qnty_mg=round($mg-$iss_qnty_gm*1000);

							//echo "gm=".substr($req_qny_edit[1],0,3)."; mg=".substr($req_qny_edit[1],3,3);


							//$num=(string)$req_qny_edit[1];
							//$next_number= str_pad($num,6,"0",STR_PAD_RIGHT);// ."000";
							//$rem= explode(".",($next_number/1000) );
							//echo $rem ."=";
							$iss_qnty_gm = substr($req_qny_edit[1], 0, 3);//$rem[0]; // floor($mg/1000);
							$iss_qnty_mg = substr($req_qny_edit[1], 3, 3);//$rem[1];//round($mg-$iss_qnty_gm*1000);
							$iss_qnty_gm = str_pad($iss_qnty_gm, 3, "0", STR_PAD_RIGHT);
							$iss_qnty_mg = str_pad($iss_qnty_mg, 3, "0", STR_PAD_RIGHT);

							$tdbgcolor = "#fff633";

							//echo "mg ".$req_qny_edit[1]."<br>";
							$adjQty = ($row[csf("adjust_percent")] * $row[csf("recipe_qnty")]) / 100;
							$comment = $process_array_remark[$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"];
							?>
							<tbody>
								<tr bgcolor="<? IF($row[csf("sub_process")]==40) {echo $tdbgcolor;} else echo $bgcolor; ?>">

									<td align="center"><? echo $i; ?></td>
		                        	<td>
										<?
											IF($row[csf("sub_process")]==40){ echo "<strong>";
												echo $group_arr[$row[csf("item_group_id")]];
												echo "</strong>";}
										else{
										echo $group_arr[$row[csf("item_group_id")]];}
									?>
									</td>
		                        	<td><? echo $row[csf("item_lot")]; ?></td>
		                        	<td><? echo $row[csf("item_description")] . ' ' . $row[csf("item_size")]; ?></td>
		                        	<td align="center"><? echo number_format($row[csf("ratio")], 6, '.', ''); ?></td>
		                        	<td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
		                        	<!--<td align="center"><? //echo $row[csf("adjust_percent")]; ?></td>-->
		                        	<td align="right"><? echo number_format($row[csf("req_qny_edit")], 6, '.', ''); ?></td>
									<td align="right"></td>
									<td align="right"></td>
									<td align="right"></td>
									<td align="right"></td>
									<td align="right"></td>
		                        </tr>
		                    </tbody>
		                        				<? $i++;
		                        				$recipe_qnty_sum += $row[csf('recipe_qnty')];
		                        				$req_qny_edit_sum += $row[csf('req_qny_edit')];

		                        				$recipe_qnty_grand += $row[csf('recipe_qnty')];
		                        				$req_qny_edit_grand += $row[csf('req_qny_edit')];
		                        			}
		                        			foreach ($sub_process_tot_rec_array as $val_rec) {
		                        				$totval_rec = $val_rec;
		                        			}
		                        			foreach ($sub_process_tot_req_array as $val_req) {
		                        				$totval_req = $val_req;
		                        			}

									//$recipe_qnty_grand +=$val_rec;
									//$req_qny_edit_grand +=$val_req;
									//$req_value_grand +=$req_value;
		                        			?>
		                        			<tr>
		                        				<td colspan="6" align="right"><strong>Total :</strong></td>

		                        				<td colspan="1" align="right"><strong><?php echo number_format($totval_req, 6, '.', ''); ?></strong></td>
		                        			</tr>
		                        			<tr>
		                        				<td colspan="6" align="right"><strong> Grand Total :</strong></td>

		                        				<td colspan="1" align="right"><strong><?php echo number_format($req_qny_edit_grand, 6, '.', ''); ?></strong></td>
		                        			</tr>
		                        		</table>
		                        		<br>
		                        		<?
		                        		echo signature_table(15, $data[0], "900px");
		                        		?>
		                        	</div>
		                        </div>
		                        <?
		                        exit();
		}
		else  // cancel to open with issue value
		{
			$sql = "select a.id, a.requ_no, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id
			from dyes_chem_issue_requ_mst a where a.id='$data[1]' and a.company_id='$data[0]'";
			$dataArray = sql_select($sql);

			$recipe_id = $dataArray[0][csf('recipe_id')];

			$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
			$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
			$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
			$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
			//$entry_form_arr = return_library_array("select id, entry_form from pro_batch_create_mst", "id", "entry_form");
			//$order_id_arr=return_library_array( "select id, booking_id from  pro_recipe_entry_mst", "id", "booking_id");
			//$order_no_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
			//$job_no_arr=return_library_array( "select id, job_no from wo_booking_mst", "id", "job_no");
			$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
			$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
			$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");

			$batch_weight = 0;
			if ($db_type == 0) {
				$data_array = sql_select("select group_concat(buyer_id) as buyer_id,group_concat(id) as recipe_id, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor, group_concat(concat_ws(':',batch_ratio,liquor_ratio) order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, group_concat(case when entry_form<>60 then batch_id end) as batch_id_rec_main, group_concat(labdip_no) as labdip_no from pro_recipe_entry_mst where id in($recipe_id)");
			} else {
				$data_array = sql_select("select listagg(cast(buyer_id as varchar(4000)),',') within group (order by buyer_id) as buyer_id, listagg(cast(id as varchar(4000)),',') within group (order by id) as recipe_id, sum(total_liquor) as total_liquor, listagg(cast(batch_ratio || ':' || liquor_ratio as varchar(4000)),',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(cast(case when entry_form<>60 then batch_id end as varchar(4000)),',') within group (order by batch_id) as batch_id_rec_main, listagg(cast(batch_id as varchar(4000)),',') within group (order by batch_id) as batch_id, listagg(cast(labdip_no as varchar(4000)), ',') within group (order by id) as labdip_no from pro_recipe_entry_mst where id in($recipe_id)");

				/*$data_array = sql_select("select listagg(buyer_id,',') within group (order by buyer_id) as buyer_id,listagg(id,',') within group (order by id) as recipe_id, listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor, listagg(batch_ratio || ':' || liquor_ratio,',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(case when entry_form<>60 then batch_id end,',') within group (order by batch_id) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");*/	// old query
			}

			$batch_weight = $data_array[0][csf("batch_weight")];
			$batch_id = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id")])));

			$batch_id_rec_main = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id_rec_main")])));
			if ($batch_id_rec_main == "") $batch_id_rec_main = 0;

			$booking_no_sql="select distinct booking_no,id, entry_form, is_sales from pro_batch_create_mst where id in($batch_id) and is_deleted=0 and status_active=1 ";
			$booking_no_arr=sql_select($booking_no_sql);
			foreach ($booking_no_arr as $row)
			{
		        $booking_arr[$row[csf("booking_no")]]=$row[csf("booking_no")];
				$entry_form_arr[$row[csf("id")]]=$row[csf("entry_form")];
		    }

			if ($db_type == 0) {
				$batchdata_array = sql_select("select group_concat(batch_no) as batch_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(distinct color_id) as color_id from pro_batch_create_mst where id in($batch_id)");
			} else {
				$batchdata_array = sql_select("select listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, listagg(color_id ,',') within group (order by id) as color_id, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight
					from pro_batch_create_mst where id in($batch_id)");
			}


			$po_no = '';
			$job_no = '';
			$buyer_name = '';
			$style_ref_no = '';
			foreach (explode(",", $data_array[0][csf("batch_id")]) as $b_id)
            {
                if ($booking_no_arr[0][csf('is_sales')]==1)
                {
                    $po_sql = "SELECT b.po_job_no, b.style_ref_no, b.customer_buyer as party_id, d.po_number
                    from pro_batch_create_mst a, fabric_sales_order_mst b, wo_booking_dtls c, wo_po_break_down d
                    where a.sales_order_id=b.id and b.booking_id=c.booking_mst_id and c.po_break_down_id=d.id and a.id in(" . $b_id . ")
                    and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.booking_type in(1,4)
                    group by b.po_job_no, b.style_ref_no, b.customer_buyer, d.po_number";
                    $po_data = sql_select($po_sql);
                    foreach ($po_data as $row)
                    {
                        $po_no .= $row[csf('po_number')] . ",";
                        $job_no .= $row[csf('po_job_no')] . ",";
                        if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
                        $buyer_name .= $buyer_library[$row[csf('party_id')]] . ",";
                    }
                }
                else
                {
                   if ($entry_form_arr[$b_id] == 36)
                    {
                        //echo "select distinct b.order_no,b.cust_style_ref, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(".$b_id.") ";
                        $po_data = sql_select("select distinct b.order_no,b.cust_style_ref, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ");
                        foreach ($po_data as $row) {
                            $po_no .= $row[csf('order_no')] . ",";
                            $job_no .= $row[csf('subcon_job')] . ",";
                            if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
                            $buyer_name .= $buyer_library[$row[csf('party_id')]] . ",";
                        }
                    }
                    else
                    {
                        //echo "select distinct b.po_number, c.job_no,c.style_ref_no, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(".$b_id.") ";
                        $po_data = sql_select("select distinct b.po_number, c.job_no,c.style_ref_no, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ");
                        $job_nos="";
                        foreach ($po_data as $row) {
                            $po_no .= $row[csf('po_number')] . ",";
                            $job_no .= $row[csf('job_no')] . ",";
                            //$job_nos .= $row[csf('job_no')] . ",";
                            if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
                            if ($job_nos == '') $job_nos ="'".$row[csf('job_no')]."'"; else $job_nos.= ","."'".$row[csf('job_no')]."'";
                            //$buyer_name.=$buyer_library[$row[csf('buyer_name')]].",";
                        }
                        foreach (explode(",", $data_array[0][csf("buyer_id")]) as $buyer_id) {
                            $buyer_name .= $buyer_library[$buyer_id] . ",";
                        }
                    }
                }

			}
			$jobNos=rtrim($job_nos,',');
			$jobnos=rtrim($job_no,',');

			//echo "select job_no, exchange_rate from wo_pre_cost_mst $job_nos_con";
			if($job_nos!='')
			{
				if($job_nos!='') $job_nos_con="where  job_no in(".$job_nos.")";//else $job_nos_con="where  job_no in('')";
				//echo "select job_no, exchange_rate from wo_pre_cost_mst $job_nos_con";
				$exchange_rate_arr = return_library_array("select job_no, exchange_rate from wo_pre_cost_mst $job_nos_con", 'job_no', 'exchange_rate');
			}
			//print_r($exchange_rate_arr);
			if($job_nos!='')
			{
			$condition= new condition();
			 $condition->company_name("=$data[0]");
			 if(str_replace("'","",$job_nos) !=''){
				  $condition->job_no("in($job_nos)");
			 }
			 $condition->init();

			$conversion= new conversion($condition);
			//echo $conversion->getQuery(); die;
			$conversion_qty_arr_process=$conversion->getQtyArray_by_jobAndProcess();
			$conversion_costing_arr_process=$conversion->getAmountArray_by_jobAndProcess();
			}
			// print_r($conversion_costing_arr_process);
			$dyeing_charge_arr=array(25,26,30,31,32,33,34,39,60,62,63,64,65,66,67,68,69,70,71,82,83,84,85,86,89,90,91,92,93,94,129,135,136,137,138,139,140,141,142,143,146);
			$jobnos=array_unique(explode(",",$jobnos));
			$tot_avg_dyeing_charge=0;$avg_dyeing_charge=0;$tot_dyeing_qty=$tot_dyeing_cost=0;//$tot_dyeing_cost=0;$tot_dyeing_qty=0;
			foreach($jobnos as $job)
			{
				$exchange_rate=$exchange_rate_arr[$job];

				foreach($dyeing_charge_arr as $process_id)
				{

					$dyeing_cost=array_sum($conversion_costing_arr_process[$job][$process_id]);
					//echo $job.'=='.$dyeing_cost.'='.$exchange_rate.'<br/>';
					$totdyeing_cost=$dyeing_cost;


					if($totdyeing_cost>0)
					{

						$tot_dyeing_qty+=array_sum($conversion_qty_arr_process[$job][$process_id]);
						$tot_dyeing_cost+=$dyeing_cost;
						//echo $tot_dyeing_cost.'='.$tot_dyeing_qty.'**'.$exchange_rate.'**'.$process_id.'<br>';
						//$avg_dyeing_charge+=$tot_dyeing_cost/$tot_dyeing_qty;
					}
				}
				//echo $tot_dyeing_cost.'<br>'.$tot_dyeing_qty;
				$avg_dyeing_charge+=$tot_dyeing_cost/$tot_dyeing_qty;
				$tot_avg_dyeing_charge+=$avg_dyeing_charge*$exchange_rate;
			}

			//echo $avg_dyeing_charge.'='.$tot_dyeing_cost.'='.$dyeing_cost;
			//echo $style_ref_no;
			/*if ($db_type==0)
			{
				$sql_yarn_dtls="select b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
			}
			elseif ($db_type==2)
			{
				$sql_yarn_dtls="select b.po_breakdown_id, b.prod_id, listagg((cast(a.yarn_lot as varchar2(500))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(a.brand_id,',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(500))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
			}*/

			if ($db_type == 0) {
				$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
			}
			else if ($db_type == 2)
			{
				/*echo $sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, listagg((cast(a.yarn_lot as varchar2(4000))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(a.brand_id,',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(4000))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id";*/

				$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, a.yarn_lot, a.brand_id, a.yarn_count
					from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b
					where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				";
			}

			$yarn_dtls_array = array();
			$result_sql_yarn_dtls = sql_select($sql_yarn_dtls);
			foreach ($result_sql_yarn_dtls as $row)
			{
				//$yarn_lot = implode(", ", array_unique(explode(",", $row[csf('yarn_lot')])));
				$yarn_id = array_unique(explode(",", $row[csf('yarn_lot')]));
				$yarn_lot = "";
				foreach ($yarn_id as $val) {
					if ($yarn_lot == "") $yarn_lot = $val; else $yarn_lot .= ", " . $val;
				}

				$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
				$brand_name = "";
				foreach ($brand_id as $val) {
					if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= ", " . $brand_arr[$val];
				}

				$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
				$count_name = "";
				foreach ($yarn_count as $val) {
					if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
				}
				$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['yarn_lot'] .= $row[csf('yarn_lot')].',';
				$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['brand_name'] .=  $brand_arr[$row[csf('brand_id')]].',';
				$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['count_name'] .= $count_arr[$row[csf('yarn_count')]].',';
			}
			//var_dump($yarn_dtls_array);
			$po_no = implode(", ", array_unique(explode(",", substr($po_no, 0, -1))));
			$job_no = implode(", ", array_unique(explode(",", substr($job_no, 0, -1))));
			$buyer_name = implode(",", array_unique(explode(",", substr($buyer_name, 0, -1))));

			$color_id = array_unique(explode(",", $batchdata_array[0][csf('color_id')]));
			$color_name = '';
			foreach ($color_id as $color) {
				$color_name .= $color_arr[$color] . ",";
			}
			$color_name = substr($color_name, 0, -1);
			//var_dump($recipe_color_arr);

			//$recipe_id=$data_array[0][csf("recipe_id")];
			$recipe_arr=sql_select("select a.working_company_id,a.company_id, b.batch_weight from pro_recipe_entry_mst a, pro_batch_create_mst b where a.batch_id=b.id and a.id=$recipe_id");
			$owner_company_id=$recipe_arr[0][csf('company_id')];
			$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
			$group_id=$nameArray[0][csf('group_id')];
			?>
			<div style="width:950px;">
				<table width="950" cellspacing="0" align="center">
						<tr>
							<td colspan="6" align="center" style="font-size:x-large;width: 680px;"><strong><u><? echo $company_library[$owner_company_id].'<br>'.$data[2]; ?>
								Report</u></strong></td>
                            <td><span id="show_barcode_image"></span></td>
						</tr>
						</table>
						<table align="center" cellspacing="0" width="950" border="1" rules="all" class="rpt_table">
							<tr>
								<td border="1"><strong>Booking No </strong></td>
								<td><? echo implode(',',$booking_arr); ?></td>
								<td><strong>Order No</strong></td>
								<td><? echo $po_no; ?></td>
								<td><strong>Req. Date</strong></td>
								<td><? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
							</tr>
							<tr>
								<td><strong>Buyer</strong></td>
								<td><? echo implode(",", array_unique(explode(",", $buyer_name))); ?></td>
								<td><strong>Batch No</strong></td>
								<td><? echo $batchdata_array[0][csf('batch_no')]; ?></td>
								<td><strong>Batch Weight</strong></td>
								<td><? $tot_batch_weight=$batch_weight+$batchdata_array[0][csf('batch_weight')];echo number_format($tot_batch_weight,4); ?></td>
							</tr>
							<tr>
								<td><strong>Color</strong></td>
								<td><? echo $color_name; ?></td>
								<td><strong>Lab Dip</strong></td>
								<td>
	        						<?
	        						/*$labdip_no = '';
	        						$recipe_ids = explode(",", $data_array[0][csf("recipe_id")]);
	        						foreach ($recipe_ids as $recp_id) {
	        							$labdip_no .= $receipe_arr[$recp_id] . ",";
	        						}*/
                                    $labdip_no=implode(",", array_unique(explode(",", $data_array[0][csf("LABDIP_NO")])));
	        						echo chop($labdip_no, ',');
	        						// or-> echo $data_array[0][csf("labdip_no")];
	        						?>
	        					</td>
								<td><strong>Machine No</strong></td>
								<td>
									<?
									$machine_data = sql_select("select machine_no, floor_id from lib_machine_name where id='" . $dataArray[0][csf("machine_id")] . "'");
									echo $machine_data[0][csf('machine_no')];
									?>
								</td>
							</tr>
							<tr>
		                        <td><strong>Style Ref.</strong></td>
								<td ><p><? echo implode(",", array_unique(explode(",", $style_ref_no))); ?></p></td>
								<td width="90"><strong>Req. ID </strong></td>
								<td width="160px"><? echo $dataArray[0][csf('requ_no')]; ?></td>
								<td border="1"><strong>Job No </strong></td>
								<td><? echo $job_no; ?></td>
							</tr>

					</table>
		            <script type="text/javascript" src="../../js/jquery.js"></script>
				 <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
		         <script>

		                function generateBarcode( valuess ){

		                        var value = valuess;
		                        var btype = 'code39';
		                        var renderer ='bmp';
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
		                        $("#show_barcode_image").show().barcode(value, btype, settings);
		                    }
		                   generateBarcode('<? echo $dataArray[0][csf('requ_no')]; ?>');
		         </script>


					<br>
					<?
					$batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
					$j = 1;
					$entryForm = $entry_form_arr[$batch_id_qry[0]];
					?>
					<table width="950" cellspacing="0" align="center" class="rpt_table" border="1" rules="all">
						<thead bgcolor="#dddddd" align="center">
							<?
							if ($entryForm == 74) {
								?>
								<tr bgcolor="#CCCCFF">
									<th colspan="4" align="center"><strong>Fabrication</strong></th>
								</tr>
								<tr>
									<th width="50">SL</th>
									<th width="350">Gmts. Item</th>
									<th width="110">Gmts. Qty</th>
									<th>Batch Qty.</th>
								</tr>
								<?
							} else {
								?>
								<tr bgcolor="#CCCCFF">
									<th colspan="8" align="center"><strong>Fabrication</strong></th>
								</tr>
								<tr>
									<th width="30">SL</th>
									<th width="100">Dia/ W. Type</th>
									<th width="100">Yarn Lot</th>
									<th width="100">Brand</th>
									<th width="100">Count</th>
									<th width="300">Constrution & Composition</th>
									<th width="70">Gsm</th>
									<th width="70">Dia</th>
								</tr>
								<?
							}
							?>

						</thead>
						<tbody>
							<?


							foreach ($batch_id_qry as $b_id) {
								$batch_query = "select po_id, prod_id, item_description, width_dia_type, sum(roll_no)  as gmts_qty, sum(batch_qnty) as  batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by po_id, prod_id, item_description, width_dia_type ";
								$result_batch_query = sql_select($batch_query);
								foreach ($result_batch_query as $rows) {
									if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
									$fabrication_full = $rows[csf("item_description")];
									$fabrication = explode(',', $fabrication_full);
									if ($entry_form_arr[$b_id] == 36) {

										?>
										<tr bgcolor="<? echo $bgcolor; ?>">
											<td align="center"><? echo $j; ?></td>
											<td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
		                            <td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['yarn_lot'];
		                            	?></td>
		                            <td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['brand_name'];
		                            	?></td>
		                            <td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['count_name'];
		                            	?></td>
		                            	<td align="left"><? echo $fabrication[0]; ?></td>
		                            	<td align="center"><? echo $fabrication[1]; ?></td>
		                            	<td align="center"><? echo $fabrication[3]; ?></td>
		                            </tr>
		                            <?
		                        } else if ($entry_form_arr[$b_id] == 74) {
		                        	?>
		                        	<tr bgcolor="<? echo $bgcolor; ?>">
		                        		<td align="center"><? echo $j; ?></td>
		                        		<td align="left"><? echo $garments_item[$rows[csf('prod_id')]]; ?></td>
		                        		<td align="right"><? echo $rows[csf('gmts_qty')]; ?></td>
		                        		<td align="right"><? echo number_format($rows[csf('batch_qnty')], 2); ?></td>
		                        	</tr>
		                        	<?
		                        } else {
								//echo $entry_form_arr[$b_id].'aaa';
		                        	?>
		                        	<tr bgcolor="<? echo $bgcolor; ?>">
		                        		<td align="center"><? echo $j; ?></td>
		                        		<td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
		                        		<td width="100" align="left"><p style="word-break:break-all;"><? echo $yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['yarn_lot']; ?></p></td>
		                        		<td align="left"><? echo $yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['brand_name']; ?></td>
		                        		<td align="left"><? echo $yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['count_name']; ?></td>
		                        		<td align="left"><? echo $fabrication[0] . ", " . $fabrication[1]; ?></td>
		                        		<td align="center"><? echo $fabrication[2]; ?></td>
		                        		<td align="center"><? echo $fabrication[3]; ?></td>
		                        	</tr>
		                        	<?
		                        }
		                        $j++;
		                    }
		                }


		                ?>
		            </tbody>
		        </table>


		        <div style="width:950px; margin-top:10px">
		        	<table align="center" cellspacing="0" width="950" border="1" rules="all" class="rpt_table">
		        		<thead bgcolor="#dddddd" align="center">
		        			<tr bgcolor="#CCCCFF">
		        				<th colspan="19" align="center"><strong>Dyes And Chemical Issue Requisition</strong></th>
		        			</tr>
		        		</thead>
		        		<?


			/*$sql_rec="select a.id, a.item_group_id, b.sub_process_id as sub_process_id, b.prod_id, b.dose_base, b.adj_type, b.comments, b.process_remark
			 from pro_recipe_entry_dtls b left join product_details_master a on b.prod_id=a.id and a.item_category_id in(5,6,7,23)
			 where b.mst_id in($recipe_id)  and b.status_active=1 and b.is_deleted=0 ";*/

			 $group_arr = return_library_array("select id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0", 'id', 'item_name');
			 $sql_rec = "select b.sub_process_id as sub_process_id, b.prod_id, b.dose_base, b.adj_type, b.comments, b.process_remark, b.item_lot
			 from pro_recipe_entry_dtls b
			 where b.mst_id in($recipe_id)  and b.status_active=1 and b.is_deleted=0";
			 $nameArray = sql_select($sql_rec);
			 foreach ($nameArray as $row) {
			 	$all_prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
			 	$item_lot_arr[$row[csf("prod_id")]] .= $row[csf("item_lot")] . ",";
			 }

			 $item_grop_id_arr = return_library_array("select id, item_group_id from product_details_master where id in(" . implode(",", $all_prod_id_arr) . ")", 'id', 'item_group_id');

			 $process_array = array();
			 $process_array_remark = array();
			 foreach ($nameArray as $row) {
			 	$process_array[$row[csf("sub_process_id")]] = $row[csf("process_remark")];
			 	$process_array_remark[$row[csf("sub_process_id")]][$row[csf("prod_id")]][$item_grop_id_arr[$row[csf("prod_id")]]][$row[csf("dose_base")]]["comments"] = $row[csf("comments")];
			 	if ($row[csf("sub_process_id")] > 92 && $row[csf("sub_process_id")] < 99) {
			 		$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"] = $row[csf("process_remark")];
			 	}

			 }

			/*$sql_rec_remark="select b.sub_process_id as sub_process_id,b.comments,b.process_remark from pro_recipe_entry_dtls b  where  b.mst_id in($recipe_id) and b.sub_process_id in(93,94,95,96,97,98) and b.status_active=1 and b.is_deleted=0";

			$nameArray_re=sql_select( $sql_rec_remark );
			foreach($nameArray_re as $row)
			{
				$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"]=$row[csf("process_remark")];
			}*/


			/*if($db_type==0)
			{
				$item_lot_arr=return_library_array("select b.prod_id,group_concat(b.item_lot) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot" );
			}
			else
			{
				$item_lot_arr=return_library_array("select b.prod_id,listagg((cast(b.item_lot as varchar2(500))),',') within group (order by b.prod_id) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot" );
			}



			$sql_dtls = "select a.requ_no, a.batch_id, a.recipe_id, b.id,
			b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
			c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id
			from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
			where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id=$data[1] order by b.id, b.seq_no";*/

			$sql_dtls = "(select a.requ_no, a.batch_id, a.recipe_id, b.id,
			b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
			c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id, b.item_lot
			from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
			where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id=$data[1])
			union
			(
			select a.requ_no, a.batch_id, a.recipe_id, b.id,
			b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
			null as item_description, null as item_group_id,  null as sub_group_name, null as item_size, null as unit_of_measure,null as avg_rate_per_unit,null as prod_id, b.item_lot
			from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b
			where a.id=b.mst_id and b.sub_process in($subprocessforwashin) and   a.id=$data[1]
			) order by id";
			// echo $sql_dtls;//die;
			$sql_result = sql_select($sql_dtls);

			$sub_process_array = array();
			$sub_process_tot_rec_array = array();
			$sub_process_tot_req_array = array();
			$sub_process_tot_value_array = array();

			foreach ($sql_result as $row) {
				$sub_process_tot_rec_array[$row[csf("sub_process")]] += $row[csf("recipe_qnty")];
				$sub_process_tot_req_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
				$sub_process_tot_value_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")] * $row[csf("avg_rate_per_unit")];
				//$sub_process_tot_ratio_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
			}
			$recipe_id=$data_array[0][csf("recipe_id")];
			$ratio_arr=array();
			$prevRatioData=sql_select( "select sub_process_id, total_liquor, liquor_ratio from pro_recipe_entry_dtls where mst_id=$recipe_id");
			foreach($prevRatioData as $prevRow)
			{
				$ratio_arr[$prevRow[csf('sub_process_id')]]['ratio']=$prevRow[csf('liquor_ratio')];
				$ratio_arr[$prevRow[csf('sub_process_id')]]['total_liquor']=$prevRow[csf('total_liquor')];
			}
						//var_dump($sub_process_tot_req_array);
			$i = 1;
			$k = 1;
			$recipe_qnty_sum = 0;
			$req_qny_edit_sum = 0;
			$recipe_qnty = 0;
			$req_qny_edit = 0;
			$req_value_sum = 0;
			$req_value_grand = 0;
			$recipe_qnty_grand = 0;
			$req_qny_edit_grand = 0;
			$fontst = "";
			$font_end = "";
			foreach ($sql_result as $row) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				if (!in_array($row[csf("sub_process")], $sub_process_array))
				{
					$sub_process_array[] = $row[csf('sub_process')];
					if ($k != 1) {
						?>
						<tr>
							<td colspan="6" align="right"><strong>Total :</strong></td>
							<td align="right"><strong><?php echo number_format($req_qny_edit_sum, 6, '.', ''); ?></strong></td>
						</tr>
						<?
					}
					$recipe_qnty_sum = 0;
					$req_qny_edit_sum = 0;
					$k++;

					//if ($row[csf("sub_process")] == 93 || $row[csf("sub_process")] == 94 || $row[csf("sub_process")] == 95 || $row[csf("sub_process")] == 96 || $row[csf("sub_process")] == 97 || $row[csf("sub_process")] == 98) {
					if(in_array($row[csf("sub_process")],$subprocessForWashArr))
					{
						$pro_remark = $process_array_remark[$row[csf("sub_process")]]["wash_comments"];
					}


					?>
					<tr bgcolor="#CCCCCC">
						<th colspan="19">
							<?

							$batch_ratio=explode(":",$data_array[0][csf("ratio")]);
							$lqr_ratio=$batch_ratio[0];
							$ratio_qty=$lqr_ratio.':'.$ratio_arr[$row[csf('sub_process')]]['ratio'];
							$total_liq = $ratio_arr[$row[csf('sub_process')]]['total_liquor'];
							$total_liquor_format = number_format($total_liq, 3, '.', '');
							$total_liquor='Total liquor(ltr)' . ": " . $total_liquor_format;
							if ($pro_remark == '') $pro_remark = ''; else $pro_remark .= $pro_remark . ', ';
							echo $dyeing_sub_process[$row[csf("sub_process")]] . ', ' . $pro_remark.'&nbsp;Liquor:Ratio &nbsp;'.$ratio_qty . ', ' .$total_liquor; ?>
						</th>
					</tr>
					<tr bgcolor="#EFEFEF">
						<th width="30">SL</th>
						<th width="150">Item Group</th>
                        <th width="70">Item Lot</th>
						<th width="180">Item Description</th>
						<th width="60">Ratio</th>
						<th width="70">GPL/%</th>
						<th width="80">Issue(KG)</th>
						<th width="50">Add-1</th>
						<th width="50">Add-2</th>
						<th width="50">Add-3</th>
						<th width="50">Add-4</th>
						<th>Total</th>
					</tr>
					<?
				}

							/*$iss_qnty=$row[csf("req_qny_edit")]*10000;
							$iss_qnty_kg=floor($iss_qnty/10000);
							$lkg=round($iss_qnty-$iss_qnty_kg*10000);
							$iss_qnty_gm=floor($lkg/10);
							$iss_qnty_mg=$lkg%10;*/
							/*$iss_qnty_gm=substr((string)$iss_qnty[1],0,3);
							$iss_qnty_mg=substr($iss_qnty[1],3);*/

							$req_qny_edit = explode(".", (string)$row[csf("req_qny_edit")]);
							$iss_qnty_kg = $req_qny_edit[0];
							if ($iss_qnty_kg == "") $iss_qnty_kg = 0;

							//$mg=($row[csf("req_qny_edit")]-$iss_qnty_kg)*1000*1000;
							//$iss_qnty_gm=floor($mg/1000);
							//$iss_qnty_mg=round($mg-$iss_qnty_gm*1000);

							//echo "gm=".substr($req_qny_edit[1],0,3)."; mg=".substr($req_qny_edit[1],3,3);


							//$num=(string)$req_qny_edit[1];
							//$next_number= str_pad($num,6,"0",STR_PAD_RIGHT);// ."000";
							//$rem= explode(".",($next_number/1000) );
							//echo $rem ."=";
							$iss_qnty_gm = substr($req_qny_edit[1], 0, 3);//$rem[0]; // floor($mg/1000);
							$iss_qnty_mg = substr($req_qny_edit[1], 3, 3);//$rem[1];//round($mg-$iss_qnty_gm*1000);
							$iss_qnty_gm = str_pad($iss_qnty_gm, 3, "0", STR_PAD_RIGHT);
							$iss_qnty_mg = str_pad($iss_qnty_mg, 3, "0", STR_PAD_RIGHT);

							$tdbgcolor = "#fff633";

							//echo "mg ".$req_qny_edit[1]."<br>";
							$adjQty = ($row[csf("adjust_percent")] * $row[csf("recipe_qnty")]) / 100;
							$comment = $process_array_remark[$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"];
							?>
							<tbody>
								<tr bgcolor="<? IF($row[csf("sub_process")]==40) {echo $tdbgcolor;} else echo $bgcolor; ?>">

									<td align="center"><? echo $i; ?></td>
		                        	<td>
										<?
											IF($row[csf("sub_process")]==40){ echo "<strong>";
												echo $group_arr[$row[csf("item_group_id")]];
												echo "</strong>";}
										else{
										echo $group_arr[$row[csf("item_group_id")]];}
									?>
									</td>
		                        	<td><? echo $row[csf("item_lot")]; ?></td>
		                        	<td><? echo $row[csf("item_description")] . ' ' . $row[csf("item_size")]; ?></td>
		                        	<td align="center"><? echo number_format($row[csf("ratio")], 6, '.', ''); ?></td>
		                        	<td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
		                        	<!--<td align="center"><? //echo $row[csf("adjust_percent")]; ?></td>-->
		                        	<td align="right"><? echo number_format($row[csf("req_qny_edit")], 6, '.', ''); ?></td>
									<td align="right"></td>
									<td align="right"></td>
									<td align="right"></td>
									<td align="right"></td>
									<td align="right"></td>

		                        </tr>
		                    </tbody>
		                        				<? $i++;
		                        				$recipe_qnty_sum += $row[csf('recipe_qnty')];
		                        				$req_qny_edit_sum += $row[csf('req_qny_edit')];

		                        				$recipe_qnty_grand += $row[csf('recipe_qnty')];
		                        				$req_qny_edit_grand += $row[csf('req_qny_edit')];
		                        			}
		                        			foreach ($sub_process_tot_rec_array as $val_rec) {
		                        				$totval_rec = $val_rec;
		                        			}
		                        			foreach ($sub_process_tot_req_array as $val_req) {
		                        				$totval_req = $val_req;
		                        			}

									//$recipe_qnty_grand +=$val_rec;
									//$req_qny_edit_grand +=$val_req;
									//$req_value_grand +=$req_value;
		                        			?>
		                        			<tr>
		                        				<td colspan="6" align="right"><strong>Total :</strong></td>

		                        				<td colspan="1" align="right"><strong><?php echo number_format($totval_req, 6, '.', ''); ?></strong></td>
		                        			</tr>
		                        			<tr>
		                        				<td colspan="6" align="right"><strong> Grand Total :</strong></td>

		                        				<td colspan="1" align="right"><strong><?php echo number_format($req_qny_edit_grand, 6, '.', ''); ?></strong></td>
		                        			</tr>
		                        		</table>
		                        		<br>
		                        		<?
		                        		echo signature_table(15, $data[0], "900px");
		                        		?>
		                        	</div>
		                        </div>
		                        <?
		                        exit();
		}
	}
}

if ($action == "chemical_dyes_issue_requisition_print7")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	//print ($data[3]);die;
	//print_r ($data);
	if($data[3]==9)
	{
		//echo $data[4];
		if ($data[4]==1) // ok to open without issue value;
		{
			$sql = "select a.id, a.requ_no, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id
			from dyes_chem_issue_requ_mst a where a.id='$data[1]' and a.company_id='$data[0]'";
			$dataArray = sql_select($sql);

			$recipe_id = $dataArray[0][csf('recipe_id')];

			$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
			$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
			$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
			$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
			//$entry_form_arr = return_library_array("select id, entry_form from pro_batch_create_mst", "id", "entry_form");
			//$order_id_arr=return_library_array( "select id, booking_id from  pro_recipe_entry_mst", "id", "booking_id");
			//$order_no_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
			//$job_no_arr=return_library_array( "select id, job_no from wo_booking_mst", "id", "job_no");
			$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
			$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
			$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");

			$batch_weight = 0;
			if ($db_type == 0) {
				$data_array = sql_select("select group_concat(buyer_id) as buyer_id,group_concat(id) as recipe_id, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor, group_concat(concat_ws(':',batch_ratio,liquor_ratio) order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, group_concat(case when entry_form<>60 then batch_id end) as batch_id_rec_main, group_concat(labdip_no) as labdip_no from pro_recipe_entry_mst where id in($recipe_id)");
			} else {
				$data_array = sql_select("select listagg(cast(buyer_id as varchar(4000)),',') within group (order by buyer_id) as buyer_id, listagg(cast(id as varchar(4000)),',') within group (order by id) as recipe_id, sum(total_liquor) as total_liquor, listagg(cast(batch_ratio || ':' || liquor_ratio as varchar(4000)),',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(cast(case when entry_form<>60 then batch_id end as varchar(4000)),',') within group (order by batch_id) as batch_id_rec_main, listagg(cast(batch_id as varchar(4000)),',') within group (order by batch_id) as batch_id, listagg(cast(labdip_no as varchar(4000)), ',') within group (order by id) as labdip_no from pro_recipe_entry_mst where id in($recipe_id)");

				/*$data_array = sql_select("select listagg(buyer_id,',') within group (order by buyer_id) as buyer_id,listagg(id,',') within group (order by id) as recipe_id, listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor, listagg(batch_ratio || ':' || liquor_ratio,',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(case when entry_form<>60 then batch_id end,',') within group (order by batch_id) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");*/	// old query
			}

			$batch_weight = $data_array[0][csf("batch_weight")];
			$batch_id = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id")])));

			$batch_id_rec_main = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id_rec_main")])));
			if ($batch_id_rec_main == "") $batch_id_rec_main = 0;
			$booking_no_sql="select distinct booking_no, is_sales,id, entry_form from pro_batch_create_mst where id in($batch_id) and is_deleted=0 and status_active=1 ";
			$booking_no_arr=sql_select($booking_no_sql);
            // echo $booking_no_arr[0][csf('is_sales')];
            // echo "<pre>";print_r($booking_no_arr);die;
			foreach ($booking_no_arr as $row)
			{
		        $booking_arr[$row[csf("booking_no")]]=$row[csf("booking_no")];
				$entry_form_arr[$row[csf("id")]]=$row[csf("entry_form")];
		    }
					if ($db_type == 0) {
				$batchdata_array = sql_select("select group_concat(batch_no) as batch_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(distinct color_id) as color_id from pro_batch_create_mst where id in($batch_id)");
			} else {
				$batchdata_array = sql_select("select listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, listagg(color_id ,',') within group (order by id) as color_id, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight
					from pro_batch_create_mst where id in($batch_id)");
			}


			$po_no = '';
			$job_no = '';
			$buyer_name = '';
			$style_ref_no = '';
			foreach (explode(",", $data_array[0][csf("batch_id")]) as $b_id)
            {
                if ($booking_no_arr[0][csf('is_sales')]==1)
                {
                    $po_sql = "SELECT b.po_job_no, b.style_ref_no, b.customer_buyer as party_id, d.po_number
                    from pro_batch_create_mst a, fabric_sales_order_mst b, wo_booking_dtls c, wo_po_break_down d
                    where a.sales_order_id=b.id and b.booking_id=c.booking_mst_id and c.po_break_down_id=d.id and a.id in(" . $b_id . ")
                    and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.booking_type in(1,4)
                    group by b.po_job_no, b.style_ref_no, b.customer_buyer, d.po_number";
                    $po_data = sql_select($po_sql);
                    foreach ($po_data as $row)
                    {
                        $po_no .= $row[csf('po_number')] . ",";
                        $job_no .= $row[csf('po_job_no')] . ",";
                        if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
                        $buyer_name .= $buyer_library[$row[csf('party_id')]] . ",";
                    }
                }
                else
                {
                    if ($entry_form_arr[$b_id] == 36)
                    {
                        //echo "select distinct b.order_no,b.cust_style_ref, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(".$b_id.") ";
                        $po_data = sql_select("select distinct b.order_no,b.cust_style_ref, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ");
                        foreach ($po_data as $row) {
                            $po_no .= $row[csf('order_no')] . ",";
                            $job_no .= $row[csf('subcon_job')] . ",";
                            if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
                            $buyer_name .= $buyer_library[$row[csf('party_id')]] . ",";
                        }
                    }
                    else
                    {
                        //echo "select distinct b.po_number, c.job_no,c.style_ref_no, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(".$b_id.") ";
                        $po_data = sql_select("select distinct b.po_number, c.job_no,c.style_ref_no, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ");
                        $job_nos="";
                        foreach ($po_data as $row)
                        {
                            $po_no .= $row[csf('po_number')] . ",";
                            $job_no .= $row[csf('job_no')] . ",";
                            //$job_nos .= $row[csf('job_no')] . ",";
                            if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
                            if ($job_nos == '') $job_nos ="'".$row[csf('job_no')]."'"; else $job_nos.= ","."'".$row[csf('job_no')]."'";
                            //$buyer_name.=$buyer_library[$row[csf('buyer_name')]].",";
                        }
                        foreach (explode(",", $data_array[0][csf("buyer_id")]) as $buyer_id) {
                            $buyer_name .= $buyer_library[$buyer_id] . ",";
                        }
                    }
                }

			}
			$jobNos=rtrim($job_nos,',');
			$jobnos=rtrim($job_no,',');

			//echo "select job_no, exchange_rate from wo_pre_cost_mst $job_nos_con";
			if($job_nos!='')
			{
				if($job_nos!='') $job_nos_con="where  job_no in(".$job_nos.")";//else $job_nos_con="where  job_no in('')";
				//echo "select job_no, exchange_rate from wo_pre_cost_mst $job_nos_con";
				$exchange_rate_arr = return_library_array("select job_no, exchange_rate from wo_pre_cost_mst $job_nos_con", 'job_no', 'exchange_rate');
			}
			//print_r($exchange_rate_arr);
			if($job_nos!='')
			{
                $condition= new condition();
                $condition->company_name("=$data[0]");
                if(str_replace("'","",$job_nos) !=''){
                    $condition->job_no("in($job_nos)");
                }
                $condition->init();

                $conversion= new conversion($condition);

                //echo $conversion->getQuery(); die;
                $conversion_qty_arr_process=$conversion->getQtyArray_by_jobAndProcess();
                $conversion_costing_arr_process=$conversion->getAmountArray_by_jobAndProcess();
			}
			// print_r($conversion_costing_arr_process);
			$dyeing_charge_arr=array(25,26,30,31,32,33,34,39,60,62,63,64,65,66,67,68,69,70,71,82,83,84,85,86,89,90,91,92,93,94,129,135,136,137,138,139,140,141,142,143,146);
			$jobnos=array_unique(explode(",",$jobnos));
			$tot_avg_dyeing_charge=0;$avg_dyeing_charge=0;$tot_dyeing_qty=$tot_dyeing_cost=0;//$tot_dyeing_cost=0;$tot_dyeing_qty=0;
			foreach($jobnos as $job)
			{
				$exchange_rate=$exchange_rate_arr[$job];

				foreach($dyeing_charge_arr as $process_id)
				{

					$dyeing_cost=array_sum($conversion_costing_arr_process[$job][$process_id]);
					//echo $job.'=='.$dyeing_cost.'='.$exchange_rate.'<br/>';
					$totdyeing_cost=$dyeing_cost;


					if($totdyeing_cost>0)
					{

						$tot_dyeing_qty+=array_sum($conversion_qty_arr_process[$job][$process_id]);
						$tot_dyeing_cost+=$dyeing_cost;
						//echo $tot_dyeing_cost.'='.$tot_dyeing_qty.'**'.$exchange_rate.'**'.$process_id.'<br>';
						//$avg_dyeing_charge+=$tot_dyeing_cost/$tot_dyeing_qty;
					}
				}
				//echo $tot_dyeing_cost.'<br>'.$tot_dyeing_qty;
				$avg_dyeing_charge+=$tot_dyeing_cost/$tot_dyeing_qty;
				$tot_avg_dyeing_charge+=$avg_dyeing_charge*$exchange_rate;
			}

			//echo $avg_dyeing_charge.'='.$tot_dyeing_cost.'='.$dyeing_cost;
			//echo $style_ref_no;
			/*if ($db_type==0)
			{
				$sql_yarn_dtls="select b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
			}
			elseif ($db_type==2)
			{
				$sql_yarn_dtls="select b.po_breakdown_id, b.prod_id, listagg((cast(a.yarn_lot as varchar2(500))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(a.brand_id,',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(500))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
			}*/

			/*---------------------------------------------------------------my close
			if ($db_type == 0)
			{
				$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
			}
			else if ($db_type == 2)
			{
				/*echo $sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, listagg((cast(a.yarn_lot as varchar2(4000))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(a.brand_id,',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(4000))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id";*/

			/*	$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, a.yarn_lot, a.brand_id, a.yarn_count
					from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b
					where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
					group by b.po_breakdown_id, b.prod_id , a.yarn_lot, a.brand_id, a.yarn_count";
			}
			*/  //----------------------------------------------------- my close ending
			$sql_yarn_dtls = "select  c.po_breakdown_id as po_id,d.yarn_lot as yarn_lot,d.yarn_count as yarn_count,d.brand_id as brand_id,d.prod_id
			from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
			where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id
			and a.id in($batch_id) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)  ";
			//echo $sql_yarn_dtls; die;

			$yarn_dtls_array = array();
			$result_sql_yarn_dtls = sql_select($sql_yarn_dtls);
			foreach ($result_sql_yarn_dtls as $row)
			{
				//$yarn_lot = implode(", ", array_unique(explode(",", $row[csf('yarn_lot')])));
				$yarn_id = array_unique(explode(",", $row[csf('yarn_lot')]));
				$yarn_lot = "";
				foreach ($yarn_id as $val) {
					if ($yarn_lot == "") $yarn_lot = $val; else $yarn_lot .= ", " . $val;
				}

				$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
				$brand_name = "";
				foreach ($brand_id as $val) {
					if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= ", " . $brand_arr[$val];
				}

				$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
				$count_name = "";
				foreach ($yarn_count as $val) {
					if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
				}
				$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['yarn_lot'] .= $row[csf('yarn_lot')].',';
				$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['brand_name'] .=  $brand_arr[$row[csf('brand_id')]].',';
				$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['count_name'] .= $count_arr[$row[csf('yarn_count')]].',';
			}
			//var_dump($yarn_dtls_array);
			$po_no = implode(", ", array_unique(explode(",", substr($po_no, 0, -1))));
			$job_no = implode(", ", array_unique(explode(",", substr($job_no, 0, -1))));
			$buyer_name = implode(",", array_unique(explode(",", substr($buyer_name, 0, -1))));

			$color_id = array_unique(explode(",", $batchdata_array[0][csf('color_id')]));
			$color_name = '';
			foreach ($color_id as $color) {
				$color_name .= $color_arr[$color] . ",";
			}
			$color_name = substr($color_name, 0, -1);
			//var_dump($recipe_color_arr);

			//$recipe_id=$data_array[0][csf("recipe_id")];
			$recipe_arr=sql_select("select a.working_company_id,a.company_id, b.batch_weight from pro_recipe_entry_mst a, pro_batch_create_mst b where a.batch_id=b.id and a.id=$recipe_id");
			$owner_company_id=$recipe_arr[0][csf('company_id')];
			$working_company_id=$recipe_arr[0][csf('working_company_id')];
			$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
			$group_id=$nameArray[0][csf('group_id')];
			?>
			<div style="width:950px;">

				        <table width="950" cellspacing="0" align="center">
						    <tr>
							    <td align="center" style="font-size:x-large;width: 680px;"><strong><u><? echo $company_library[$working_company_id].'<br>'."Dyes And Chemical Issue Requisition"//.$data[2]; ?>
								Report</u></strong></td>
                                <td><span id="show_barcode_image"></span></td>
							</tr>
						</table>
						<table align="center" cellspacing="0" width="950" border="1" rules="all" class="rpt_table">
							<tr>
								<td border="1"><strong>Booking No </strong></td>
								<td><? echo implode(',',$booking_arr); ?></td>
								<td><strong>Order No</strong></td>
								<td><? echo $po_no; ?></td>
								<td><strong>Req. Date</strong></td>
								<td><? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
							</tr>
							<tr>
								<td><strong>Buyer</strong></td>
								<td><? echo implode(",", array_unique(explode(",", $buyer_name))); ?></td>
								<td><strong>Batch No</strong></td>
								<td><? echo $batchdata_array[0][csf('batch_no')]; ?></td>
								<td><strong>Batch Weight</strong></td>
								<td><strong><? $tot_batch_weight=$batch_weight+$batchdata_array[0][csf('batch_weight')];echo number_format($tot_batch_weight,4); ?></strong></td>
							</tr>
							<tr>
								<td><strong>Color</strong></td>
								<td><? echo $color_name; ?></td>
								<td><strong>Lab Dip</strong></td>
								<td>
	        						<?
	        						/*$labdip_no = '';
	        						$recipe_ids = explode(",", $data_array[0][csf("recipe_id")]);
	        						foreach ($recipe_ids as $recp_id)
                                    {
	        							$labdip_no .= $receipe_arr[$recp_id] . ",";
	        						}*/
                                    $labdip_no=implode(",", array_unique(explode(",", $data_array[0][csf("LABDIP_NO")])));
	        						echo chop($labdip_no, ',');
	        						// or-> echo $data_array[0][csf("labdip_no")];
	        						?>
	        					</td>
								<td><strong>Machine No</strong></td>
								<td>
									<?
									$machine_data = sql_select("select machine_no, floor_id from lib_machine_name where id='" . $dataArray[0][csf("machine_id")] . "'");
									echo $machine_data[0][csf('machine_no')];
									?>
								</td>
							</tr>
							<tr>
		                        <td><strong>Style Ref.</strong></td>
								<td><p><? echo implode(",", array_unique(explode(",", $style_ref_no))); ?></p></td>
								<td><strong>Req. ID </strong></td>
								<td><? echo $dataArray[0][csf('requ_no')]; ?></td>
								<td border="1"><strong>Job No </strong></td>
								<td><? echo $job_no; ?></td>
							</tr>
					    </table>
		            <script type="text/javascript" src="../../js/jquery.js"></script>
    				<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    		        <script>
		                function generateBarcode( valuess ){
		                        var value = valuess;
		                        var btype = 'code39';
		                        var renderer ='bmp';
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
		                        $("#show_barcode_image").show().barcode(value, btype, settings);
		                    }
		                   generateBarcode('<? echo $dataArray[0][csf('requ_no')]; ?>');
    		        </script>


					<br>
				

		        <div style="width:950px; margin-top:10px">
		        	<table align="center" cellspacing="0" width="950" border="1" rules="all" class="rpt_table">
		        		<thead bgcolor="#dddddd" align="center">
		        			<tr bgcolor="#CCCCFF">
		        				<th colspan="19" align="center"><strong>Dyes And Chemical Issue Requisition</strong></th>
		        			</tr>
		        		</thead>
		        		<?


			/*$sql_rec="select a.id, a.item_group_id, b.sub_process_id as sub_process_id, b.prod_id, b.dose_base, b.adj_type, b.comments, b.process_remark
			 from pro_recipe_entry_dtls b left join product_details_master a on b.prod_id=a.id and a.item_category_id in(5,6,7,23)
			 where b.mst_id in($recipe_id)  and b.status_active=1 and b.is_deleted=0 ";*/

			 $group_arr = return_library_array("select id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0", 'id', 'item_name');
			 $sql_rec = "select b.sub_process_id as sub_process_id, b.prod_id, b.dose_base, b.adj_type, b.comments, b.process_remark, b.item_lot
			 from pro_recipe_entry_dtls b
			 where b.mst_id in($recipe_id)  and b.status_active=1 and b.is_deleted=0";
			 $nameArray = sql_select($sql_rec);
			 foreach ($nameArray as $row) {
			 	$all_prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
			 	$item_lot_arr[$row[csf("prod_id")]] .= $row[csf("item_lot")] . ",";
			 }

			 $item_grop_id_arr = return_library_array("select id, item_group_id from product_details_master where id in(" . implode(",", $all_prod_id_arr) . ")", 'id', 'item_group_id');

			 $process_array = array();
			 $process_array_remark = array();
			 foreach ($nameArray as $row) {
			 	$process_array[$row[csf("sub_process_id")]] = $row[csf("process_remark")];
			 	$process_array_remark[$row[csf("sub_process_id")]][$row[csf("prod_id")]][$item_grop_id_arr[$row[csf("prod_id")]]][$row[csf("dose_base")]]["comments"] = $row[csf("comments")];
			 	if ($row[csf("sub_process_id")] > 92 && $row[csf("sub_process_id")] < 99) {
			 		$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"] = $row[csf("process_remark")];
			 	}

			 }

			/*$sql_rec_remark="select b.sub_process_id as sub_process_id,b.comments,b.process_remark from pro_recipe_entry_dtls b  where  b.mst_id in($recipe_id) and b.sub_process_id in(93,94,95,96,97,98) and b.status_active=1 and b.is_deleted=0";

			$nameArray_re=sql_select( $sql_rec_remark );
			foreach($nameArray_re as $row)
			{
				$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"]=$row[csf("process_remark")];
			}*/


			/*if($db_type==0)
			{
				$item_lot_arr=return_library_array("select b.prod_id,group_concat(b.item_lot) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot" );
			}
			else
			{
				$item_lot_arr=return_library_array("select b.prod_id,listagg((cast(b.item_lot as varchar2(500))),',') within group (order by b.prod_id) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot" );
			}



			$sql_dtls = "select a.requ_no, a.batch_id, a.recipe_id, b.id,
			b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
			c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id
			from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
			where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id=$data[1] order by b.id, b.seq_no";*/

			$sql_dtls = "(select a.requ_no, a.batch_id, a.recipe_id, b.id,
			b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
			c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id, b.item_lot
			from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
			where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id=$data[1])
			union
			(
			select a.requ_no, a.batch_id, a.recipe_id, b.id,
			b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
			null as item_description, null as item_group_id,  null as sub_group_name, null as item_size, null as unit_of_measure,null as avg_rate_per_unit,null as prod_id, b.item_lot
			from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b
			where a.id=b.mst_id and b.sub_process in($subprocessforwashin) and   a.id=$data[1]
			) order by id";
			// echo $sql_dtls;//die;
			$sql_result = sql_select($sql_dtls);

			$sub_process_array = array();
			$sub_process_tot_rec_array = array();
			$sub_process_tot_req_array = array();
			$sub_process_tot_value_array = array();

			foreach ($sql_result as $row) {
				$sub_process_tot_rec_array[$row[csf("sub_process")]] += $row[csf("recipe_qnty")];
				$sub_process_tot_req_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
				$sub_process_tot_value_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")] * $row[csf("avg_rate_per_unit")];
				//$sub_process_tot_ratio_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
			}
			$recipe_id=$data_array[0][csf("recipe_id")];
			$ratio_arr=array();
			$prevRatioData=sql_select( "select sub_process_id, total_liquor, liquor_ratio from pro_recipe_entry_dtls where mst_id=$recipe_id");
			foreach($prevRatioData as $prevRow)
			{
				$ratio_arr[$prevRow[csf('sub_process_id')]]['ratio']=$prevRow[csf('liquor_ratio')];
				$ratio_arr[$prevRow[csf('sub_process_id')]]['total_liquor']=$prevRow[csf('total_liquor')];
			}
						//var_dump($sub_process_tot_req_array);
			$i = 1;
			$k = 1;
			$recipe_qnty_sum = 0;
			$req_qny_edit_sum = 0;
			$recipe_qnty = 0;
			$req_qny_edit = 0;
			$req_value_sum = 0;
			$req_value_grand = 0;
			$recipe_qnty_grand = 0;
			$req_qny_edit_grand = 0;
			$fontst = "";
			$font_end = "";
			foreach ($sql_result as $row) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				if (!in_array($row[csf("sub_process")], $sub_process_array))
				{
					$sub_process_array[] = $row[csf('sub_process')];
					if ($k != 1) {
						?>
						<tr>
							<td colspan="6" align="right"><strong>Total :</strong></td>
							<td align="right"><strong><?php echo number_format($req_qny_edit_sum, 6, '.', ''); ?></strong></td>
						</tr>
						<?
					}
					$recipe_qnty_sum = 0;
					$req_qny_edit_sum = 0;
					$k++;

					//if ($row[csf("sub_process")] == 93 || $row[csf("sub_process")] == 94 || $row[csf("sub_process")] == 95 || $row[csf("sub_process")] == 96 || $row[csf("sub_process")] == 97 || $row[csf("sub_process")] == 98) {
						 if(in_array($row[csf("sub_process")],$subprocessForWashArr))
						 {
						$pro_remark = $process_array_remark[$row[csf("sub_process")]]["wash_comments"];
					}


					?>
					<tr bgcolor="#CCCCCC">
						<th colspan="19">
							<?

							$batch_ratio=explode(":",$data_array[0][csf("ratio")]);
							$lqr_ratio=$batch_ratio[0];
							$ratio_qty=$lqr_ratio.':'.$ratio_arr[$row[csf('sub_process')]]['ratio'];
							$total_liq = $ratio_arr[$row[csf('sub_process')]]['total_liquor'];
							$total_liquor_format = number_format($total_liq, 3, '.', '');
							$total_liquor='Total liquor(ltr)' . ": " . $total_liquor_format;
							if ($pro_remark == '') $pro_remark = ''; else $pro_remark .= $pro_remark . ', ';
							echo $dyeing_sub_process[$row[csf("sub_process")]] . ', ' . $pro_remark.'&nbsp;Liquor:Ratio &nbsp;'.$ratio_qty . ', ' .$total_liquor; ?>
						</th>
					</tr>
					<tr bgcolor="#EFEFEF">
						<th width="30">SL</th>
						<th width="150">Item Group</th>
                        <th width="70">Item Lot</th>
						<th width="180">Item Description</th>
						<th width="60">Ratio</th>
						<th width="70">GPL/%</th>
						<th width="80">Issue(KG)</th>
						<th width="50">Add-1</th>
						<th width="50">Add-2</th>
						<th width="50">Add-3</th>
						<th width="50">Add-4</th>
						<th>Total</th>
					</tr>
					<?
				}

							/*$iss_qnty=$row[csf("req_qny_edit")]*10000;
							$iss_qnty_kg=floor($iss_qnty/10000);
							$lkg=round($iss_qnty-$iss_qnty_kg*10000);
							$iss_qnty_gm=floor($lkg/10);
							$iss_qnty_mg=$lkg%10;*/
							/*$iss_qnty_gm=substr((string)$iss_qnty[1],0,3);
							$iss_qnty_mg=substr($iss_qnty[1],3);*/

							$req_qny_edit = explode(".", (string)$row[csf("req_qny_edit")]);
							$iss_qnty_kg = $req_qny_edit[0];
							if ($iss_qnty_kg == "") $iss_qnty_kg = 0;

							//$mg=($row[csf("req_qny_edit")]-$iss_qnty_kg)*1000*1000;
							//$iss_qnty_gm=floor($mg/1000);
							//$iss_qnty_mg=round($mg-$iss_qnty_gm*1000);

							//echo "gm=".substr($req_qny_edit[1],0,3)."; mg=".substr($req_qny_edit[1],3,3);


							//$num=(string)$req_qny_edit[1];
							//$next_number= str_pad($num,6,"0",STR_PAD_RIGHT);// ."000";
							//$rem= explode(".",($next_number/1000) );
							//echo $rem ."=";
							$iss_qnty_gm = substr($req_qny_edit[1], 0, 3);//$rem[0]; // floor($mg/1000);
							$iss_qnty_mg = substr($req_qny_edit[1], 3, 3);//$rem[1];//round($mg-$iss_qnty_gm*1000);
							$iss_qnty_gm = str_pad($iss_qnty_gm, 3, "0", STR_PAD_RIGHT);
							$iss_qnty_mg = str_pad($iss_qnty_mg, 3, "0", STR_PAD_RIGHT);

							$tdbgcolor = "#fff633";

							//echo "mg ".$req_qny_edit[1]."<br>";
							$adjQty = ($row[csf("adjust_percent")] * $row[csf("recipe_qnty")]) / 100;
							$comment = $process_array_remark[$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"];
							?>
							<tbody>
								<tr bgcolor="<? IF($row[csf("sub_process")]==40) {echo $tdbgcolor;} else echo $bgcolor; ?>">

									<td align="center"><? echo $i; ?></td>
		                        	<td>
										<?
											IF($row[csf("sub_process")]==40){ echo "<strong>";
												echo $group_arr[$row[csf("item_group_id")]];
												echo "</strong>";}
										else{
										echo $group_arr[$row[csf("item_group_id")]];}
									?>
									</td>
		                        	<td><? echo $row[csf("item_lot")]; ?></td>
		                        	<td><? echo $row[csf("item_description")] . ' ' . $row[csf("item_size")]; ?></td>
		                        	<td align="center"><? echo number_format($row[csf("ratio")], 6, '.', ''); ?></td>
		                        	<td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
		                        	<!--<td align="center"><? //echo $row[csf("adjust_percent")]; ?></td>-->
		                        	<td align="right"><? echo number_format($row[csf("req_qny_edit")], 6, '.', ''); ?></td>
									<td align="right"></td>
									<td align="right"></td>
									<td align="right"></td>
									<td align="right"></td>
									<td align="right"></td>
		                        </tr>
		                    </tbody>
		                        				<? $i++;
		                        				$recipe_qnty_sum += $row[csf('recipe_qnty')];
		                        				$req_qny_edit_sum += $row[csf('req_qny_edit')];

		                        				$recipe_qnty_grand += $row[csf('recipe_qnty')];
		                        				$req_qny_edit_grand += $row[csf('req_qny_edit')];
		                        			}
		                        			foreach ($sub_process_tot_rec_array as $val_rec) {
		                        				$totval_rec = $val_rec;
		                        			}
		                        			foreach ($sub_process_tot_req_array as $val_req) {
		                        				$totval_req = $val_req;
		                        			}

									//$recipe_qnty_grand +=$val_rec;
									//$req_qny_edit_grand +=$val_req;
									//$req_value_grand +=$req_value;
		                        			?>
		                        			<tr>
		                        				<td colspan="6" align="right"><strong>Total :</strong></td>

		                        				<td colspan="1" align="right"><strong><?php echo number_format($totval_req, 6, '.', ''); ?></strong></td>
		                        			</tr>
		                        			<tr>
		                        				<td colspan="6" align="right"><strong> Grand Total :</strong></td>

		                        				<td colspan="1" align="right"><strong><?php echo number_format($req_qny_edit_grand, 6, '.', ''); ?></strong></td>
		                        			</tr>
		                        		</table>
		                        		
		                        		<?
		                        		 echo signature_table(15, $data[0], "900px", "", 20);
		                        		?>
		                        	</div>
		                        </div>
		                        <?
		                        exit();
		}
		else  // cancel to open with issue value
		{
			$sql = "select a.id, a.requ_no, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id
			from dyes_chem_issue_requ_mst a where a.id='$data[1]' and a.company_id='$data[0]'";
			$dataArray = sql_select($sql);

			$recipe_id = $dataArray[0][csf('recipe_id')];

			$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
			$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
			$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
			$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
			//$entry_form_arr = return_library_array("select id, entry_form from pro_batch_create_mst", "id", "entry_form");
			//$order_id_arr=return_library_array( "select id, booking_id from  pro_recipe_entry_mst", "id", "booking_id");
			//$order_no_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
			//$job_no_arr=return_library_array( "select id, job_no from wo_booking_mst", "id", "job_no");
			$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
			$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
			$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");

			$batch_weight = 0;
			if ($db_type == 0) {
				$data_array = sql_select("select group_concat(buyer_id) as buyer_id,group_concat(id) as recipe_id, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor, group_concat(concat_ws(':',batch_ratio,liquor_ratio) order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, group_concat(case when entry_form<>60 then batch_id end) as batch_id_rec_main, group_concat(labdip_no) as labdip_no from pro_recipe_entry_mst where id in($recipe_id)");
			} else {
				$data_array = sql_select("select listagg(cast(buyer_id as varchar(4000)),',') within group (order by buyer_id) as buyer_id, listagg(cast(id as varchar(4000)),',') within group (order by id) as recipe_id, sum(total_liquor) as total_liquor, listagg(cast(batch_ratio || ':' || liquor_ratio as varchar(4000)),',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(cast(case when entry_form<>60 then batch_id end as varchar(4000)),',') within group (order by batch_id) as batch_id_rec_main, listagg(cast(batch_id as varchar(4000)),',') within group (order by batch_id) as batch_id, listagg(cast(labdip_no as varchar(4000)), ',') within group (order by id) as labdip_no from pro_recipe_entry_mst where id in($recipe_id)");

				/*$data_array = sql_select("select listagg(buyer_id,',') within group (order by buyer_id) as buyer_id,listagg(id,',') within group (order by id) as recipe_id, listagg(batch_id,',') within group (order by batch_id) as batch_id, sum(total_liquor) as total_liquor, listagg(batch_ratio || ':' || liquor_ratio,',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(case when entry_form<>60 then batch_id end,',') within group (order by batch_id) as batch_id_rec_main from pro_recipe_entry_mst where id in($recipe_id)");*/	// old query
			}

			$batch_weight = $data_array[0][csf("batch_weight")];
			$batch_id = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id")])));

			$batch_id_rec_main = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id_rec_main")])));
			if ($batch_id_rec_main == "") $batch_id_rec_main = 0;

			$booking_no_sql="select distinct booking_no,id, entry_form, is_sales from pro_batch_create_mst where id in($batch_id) and is_deleted=0 and status_active=1 ";
			$booking_no_arr=sql_select($booking_no_sql);
			foreach ($booking_no_arr as $row)
			{
		        $booking_arr[$row[csf("booking_no")]]=$row[csf("booking_no")];
				$entry_form_arr[$row[csf("id")]]=$row[csf("entry_form")];
		    }

			if ($db_type == 0) {
				$batchdata_array = sql_select("select group_concat(batch_no) as batch_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(distinct color_id) as color_id from pro_batch_create_mst where id in($batch_id)");
			} else {
				$batchdata_array = sql_select("select listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, listagg(color_id ,',') within group (order by id) as color_id, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight
					from pro_batch_create_mst where id in($batch_id)");
			}


			$po_no = '';
			$job_no = '';
			$buyer_name = '';
			$style_ref_no = '';
			foreach (explode(",", $data_array[0][csf("batch_id")]) as $b_id)
            {
                if ($booking_no_arr[0][csf('is_sales')]==1)
                {
                    $po_sql = "SELECT b.po_job_no, b.style_ref_no, b.customer_buyer as party_id, d.po_number
                    from pro_batch_create_mst a, fabric_sales_order_mst b, wo_booking_dtls c, wo_po_break_down d
                    where a.sales_order_id=b.id and b.booking_id=c.booking_mst_id and c.po_break_down_id=d.id and a.id in(" . $b_id . ")
                    and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.booking_type in(1,4)
                    group by b.po_job_no, b.style_ref_no, b.customer_buyer, d.po_number";
                    $po_data = sql_select($po_sql);
                    foreach ($po_data as $row)
                    {
                        $po_no .= $row[csf('po_number')] . ",";
                        $job_no .= $row[csf('po_job_no')] . ",";
                        if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
                        $buyer_name .= $buyer_library[$row[csf('party_id')]] . ",";
                    }
                }
                else
                {
                   if ($entry_form_arr[$b_id] == 36)
                    {
                        //echo "select distinct b.order_no,b.cust_style_ref, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(".$b_id.") ";
                        $po_data = sql_select("select distinct b.order_no,b.cust_style_ref, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ");
                        foreach ($po_data as $row) {
                            $po_no .= $row[csf('order_no')] . ",";
                            $job_no .= $row[csf('subcon_job')] . ",";
                            if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
                            $buyer_name .= $buyer_library[$row[csf('party_id')]] . ",";
                        }
                    }
                    else
                    {
                        //echo "select distinct b.po_number, c.job_no,c.style_ref_no, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(".$b_id.") ";
                        $po_data = sql_select("select distinct b.po_number, c.job_no,c.style_ref_no, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ");
                        $job_nos="";
                        foreach ($po_data as $row) {
                            $po_no .= $row[csf('po_number')] . ",";
                            $job_no .= $row[csf('job_no')] . ",";
                            //$job_nos .= $row[csf('job_no')] . ",";
                            if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
                            if ($job_nos == '') $job_nos ="'".$row[csf('job_no')]."'"; else $job_nos.= ","."'".$row[csf('job_no')]."'";
                            //$buyer_name.=$buyer_library[$row[csf('buyer_name')]].",";
                        }
                        foreach (explode(",", $data_array[0][csf("buyer_id")]) as $buyer_id) {
                            $buyer_name .= $buyer_library[$buyer_id] . ",";
                        }
                    }
                }

			}
			$jobNos=rtrim($job_nos,',');
			$jobnos=rtrim($job_no,',');

			//echo "select job_no, exchange_rate from wo_pre_cost_mst $job_nos_con";
			if($job_nos!='')
			{
				if($job_nos!='') $job_nos_con="where  job_no in(".$job_nos.")";//else $job_nos_con="where  job_no in('')";
				//echo "select job_no, exchange_rate from wo_pre_cost_mst $job_nos_con";
				$exchange_rate_arr = return_library_array("select job_no, exchange_rate from wo_pre_cost_mst $job_nos_con", 'job_no', 'exchange_rate');
			}
			//print_r($exchange_rate_arr);
			if($job_nos!='')
			{
			$condition= new condition();
			 $condition->company_name("=$data[0]");
			 if(str_replace("'","",$job_nos) !=''){
				  $condition->job_no("in($job_nos)");
			 }
			 $condition->init();

			$conversion= new conversion($condition);
			//echo $conversion->getQuery(); die;
			$conversion_qty_arr_process=$conversion->getQtyArray_by_jobAndProcess();
			$conversion_costing_arr_process=$conversion->getAmountArray_by_jobAndProcess();
			}
			// print_r($conversion_costing_arr_process);
			$dyeing_charge_arr=array(25,26,30,31,32,33,34,39,60,62,63,64,65,66,67,68,69,70,71,82,83,84,85,86,89,90,91,92,93,94,129,135,136,137,138,139,140,141,142,143,146);
			$jobnos=array_unique(explode(",",$jobnos));
			$tot_avg_dyeing_charge=0;$avg_dyeing_charge=0;$tot_dyeing_qty=$tot_dyeing_cost=0;//$tot_dyeing_cost=0;$tot_dyeing_qty=0;
			foreach($jobnos as $job)
			{
				$exchange_rate=$exchange_rate_arr[$job];

				foreach($dyeing_charge_arr as $process_id)
				{

					$dyeing_cost=array_sum($conversion_costing_arr_process[$job][$process_id]);
					//echo $job.'=='.$dyeing_cost.'='.$exchange_rate.'<br/>';
					$totdyeing_cost=$dyeing_cost;


					if($totdyeing_cost>0)
					{

						$tot_dyeing_qty+=array_sum($conversion_qty_arr_process[$job][$process_id]);
						$tot_dyeing_cost+=$dyeing_cost;
						//echo $tot_dyeing_cost.'='.$tot_dyeing_qty.'**'.$exchange_rate.'**'.$process_id.'<br>';
						//$avg_dyeing_charge+=$tot_dyeing_cost/$tot_dyeing_qty;
					}
				}
				//echo $tot_dyeing_cost.'<br>'.$tot_dyeing_qty;
				$avg_dyeing_charge+=$tot_dyeing_cost/$tot_dyeing_qty;
				$tot_avg_dyeing_charge+=$avg_dyeing_charge*$exchange_rate;
			}

			//echo $avg_dyeing_charge.'='.$tot_dyeing_cost.'='.$dyeing_cost;
			//echo $style_ref_no;
			/*if ($db_type==0)
			{
				$sql_yarn_dtls="select b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
			}
			elseif ($db_type==2)
			{
				$sql_yarn_dtls="select b.po_breakdown_id, b.prod_id, listagg((cast(a.yarn_lot as varchar2(500))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(a.brand_id,',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(500))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
			}*/

			if ($db_type == 0) {
				$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
			}
			else if ($db_type == 2)
			{
				/*echo $sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, listagg((cast(a.yarn_lot as varchar2(4000))),',') within group (order by a.yarn_lot) as yarn_lot, listagg(a.brand_id,',') within group (order by a.brand_id) as brand_id, listagg((cast(a.yarn_count as varchar2(4000))),',') within group (order by a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id";*/

				$sql_yarn_dtls = "select b.po_breakdown_id, b.prod_id, a.yarn_lot, a.brand_id, a.yarn_count
					from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b
					where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				";
			}

			$yarn_dtls_array = array();
			$result_sql_yarn_dtls = sql_select($sql_yarn_dtls);
			foreach ($result_sql_yarn_dtls as $row)
			{
				//$yarn_lot = implode(", ", array_unique(explode(",", $row[csf('yarn_lot')])));
				$yarn_id = array_unique(explode(",", $row[csf('yarn_lot')]));
				$yarn_lot = "";
				foreach ($yarn_id as $val) {
					if ($yarn_lot == "") $yarn_lot = $val; else $yarn_lot .= ", " . $val;
				}

				$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
				$brand_name = "";
				foreach ($brand_id as $val) {
					if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= ", " . $brand_arr[$val];
				}

				$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
				$count_name = "";
				foreach ($yarn_count as $val) {
					if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
				}
				$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['yarn_lot'] .= $row[csf('yarn_lot')].',';
				$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['brand_name'] .=  $brand_arr[$row[csf('brand_id')]].',';
				$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['count_name'] .= $count_arr[$row[csf('yarn_count')]].',';
			}
			//var_dump($yarn_dtls_array);
			$po_no = implode(", ", array_unique(explode(",", substr($po_no, 0, -1))));
			$job_no = implode(", ", array_unique(explode(",", substr($job_no, 0, -1))));
			$buyer_name = implode(",", array_unique(explode(",", substr($buyer_name, 0, -1))));

			$color_id = array_unique(explode(",", $batchdata_array[0][csf('color_id')]));
			$color_name = '';
			foreach ($color_id as $color) {
				$color_name .= $color_arr[$color] . ",";
			}
			$color_name = substr($color_name, 0, -1);
			//var_dump($recipe_color_arr);

			//$recipe_id=$data_array[0][csf("recipe_id")];
			$recipe_arr=sql_select("select a.working_company_id,a.company_id, b.batch_weight from pro_recipe_entry_mst a, pro_batch_create_mst b where a.batch_id=b.id and a.id=$recipe_id");
			$owner_company_id=$recipe_arr[0][csf('company_id')];
			$nameArray = sql_select("select plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
			$group_id=$nameArray[0][csf('group_id')];
			?>
			<div style="width:950px;">
				<table width="950" cellspacing="0" align="center">
						<tr>
							<td colspan="6" align="center" style="font-size:x-large;width: 680px;"><strong><u><? echo $company_library[$owner_company_id].'<br>'.$data[2]; ?>
								Report</u></strong></td>
                            <td><span id="show_barcode_image"></span></td>
						</tr>
						</table>
						<table align="center" cellspacing="0" width="950" border="1" rules="all" class="rpt_table">
							<tr>
								<td border="1"><strong>Booking No </strong></td>
								<td><? echo implode(',',$booking_arr); ?></td>
								<td><strong>Order No</strong></td>
								<td><? echo $po_no; ?></td>
								<td><strong>Req. Date</strong></td>
								<td><? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
							</tr>
							<tr>
								<td><strong>Buyer</strong></td>
								<td><? echo implode(",", array_unique(explode(",", $buyer_name))); ?></td>
								<td><strong>Batch No</strong></td>
								<td><? echo $batchdata_array[0][csf('batch_no')]; ?></td>
								<td><strong>Batch Weight</strong></td>
								<td><? $tot_batch_weight=$batch_weight+$batchdata_array[0][csf('batch_weight')];echo number_format($tot_batch_weight,4); ?></td>
							</tr>
							<tr>
								<td><strong>Color</strong></td>
								<td><? echo $color_name; ?></td>
								<td><strong>Lab Dip</strong></td>
								<td>
	        						<?
	        						/*$labdip_no = '';
	        						$recipe_ids = explode(",", $data_array[0][csf("recipe_id")]);
	        						foreach ($recipe_ids as $recp_id) {
	        							$labdip_no .= $receipe_arr[$recp_id] . ",";
	        						}*/
                                    $labdip_no=implode(",", array_unique(explode(",", $data_array[0][csf("LABDIP_NO")])));
	        						echo chop($labdip_no, ',');
	        						// or-> echo $data_array[0][csf("labdip_no")];
	        						?>
	        					</td>
								<td><strong>Machine No</strong></td>
								<td>
									<?
									$machine_data = sql_select("select machine_no, floor_id from lib_machine_name where id='" . $dataArray[0][csf("machine_id")] . "'");
									echo $machine_data[0][csf('machine_no')];
									?>
								</td>
							</tr>
							<tr>
		                        <td><strong>Style Ref.</strong></td>
								<td ><p><? echo implode(",", array_unique(explode(",", $style_ref_no))); ?></p></td>
								<td width="90"><strong>Req. ID </strong></td>
								<td width="160px"><? echo $dataArray[0][csf('requ_no')]; ?></td>
								<td border="1"><strong>Job No </strong></td>
								<td><? echo $job_no; ?></td>
							</tr>

					</table>
		            <script type="text/javascript" src="../../js/jquery.js"></script>
				 <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
		         <script>

		                function generateBarcode( valuess ){

		                        var value = valuess;
		                        var btype = 'code39';
		                        var renderer ='bmp';
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
		                        $("#show_barcode_image").show().barcode(value, btype, settings);
		                    }
		                   generateBarcode('<? echo $dataArray[0][csf('requ_no')]; ?>');
		         </script>


					<br>
					<?
					$batch_id_qry = explode(",", $dataArray[0][csf('batch_id')]);
					$j = 1;
					$entryForm = $entry_form_arr[$batch_id_qry[0]];
					?>
					<table width="950" cellspacing="0" align="center" class="rpt_table" border="1" rules="all">
						<thead bgcolor="#dddddd" align="center">
							<?
							if ($entryForm == 74) {
								?>
								<tr bgcolor="#CCCCFF">
									<th colspan="4" align="center"><strong>Fabrication</strong></th>
								</tr>
								<tr>
									<th width="50">SL</th>
									<th width="350">Gmts. Item</th>
									<th width="110">Gmts. Qty</th>
									<th>Batch Qty.</th>
								</tr>
								<?
							} else {
								?>
								<tr bgcolor="#CCCCFF">
									<th colspan="8" align="center"><strong>Fabrication</strong></th>
								</tr>
								<tr>
									<th width="30">SL</th>
									<th width="100">Dia/ W. Type</th>
									<th width="100">Yarn Lot</th>
									<th width="100">Brand</th>
									<th width="100">Count</th>
									<th width="300">Constrution & Composition</th>
									<th width="70">Gsm</th>
									<th width="70">Dia</th>
								</tr>
								<?
							}
							?>

						</thead>
						<tbody>
							<?


							foreach ($batch_id_qry as $b_id) {
								$batch_query = "select po_id, prod_id, item_description, width_dia_type, sum(roll_no)  as gmts_qty, sum(batch_qnty) as  batch_qnty from pro_batch_create_dtls where mst_id in($b_id) and status_active=1 and is_deleted=0 group by po_id, prod_id, item_description, width_dia_type ";
								$result_batch_query = sql_select($batch_query);
								foreach ($result_batch_query as $rows) {
									if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
									$fabrication_full = $rows[csf("item_description")];
									$fabrication = explode(',', $fabrication_full);
									if ($entry_form_arr[$b_id] == 36) {

										?>
										<tr bgcolor="<? echo $bgcolor; ?>">
											<td align="center"><? echo $j; ?></td>
											<td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
		                            <td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['yarn_lot'];
		                            	?></td>
		                            <td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['brand_name'];
		                            	?></td>
		                            <td align="center"><? //echo $yarn_dtls_array[$row[csf('po_id')]][$row[csf('prod_id')]]['count_name'];
		                            	?></td>
		                            	<td align="left"><? echo $fabrication[0]; ?></td>
		                            	<td align="center"><? echo $fabrication[1]; ?></td>
		                            	<td align="center"><? echo $fabrication[3]; ?></td>
		                            </tr>
		                            <?
		                        } else if ($entry_form_arr[$b_id] == 74) {
		                        	?>
		                        	<tr bgcolor="<? echo $bgcolor; ?>">
		                        		<td align="center"><? echo $j; ?></td>
		                        		<td align="left"><? echo $garments_item[$rows[csf('prod_id')]]; ?></td>
		                        		<td align="right"><? echo $rows[csf('gmts_qty')]; ?></td>
		                        		<td align="right"><? echo number_format($rows[csf('batch_qnty')], 2); ?></td>
		                        	</tr>
		                        	<?
		                        } else {
								//echo $entry_form_arr[$b_id].'aaa';
		                        	?>
		                        	<tr bgcolor="<? echo $bgcolor; ?>">
		                        		<td align="center"><? echo $j; ?></td>
		                        		<td align="center"><? echo $fabric_typee[$rows[csf("width_dia_type")]]; ?></td>
		                        		<td width="100" align="left"><p style="word-break:break-all;"><? echo $yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['yarn_lot']; ?></p></td>
		                        		<td align="left"><? echo $yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['brand_name']; ?></td>
		                        		<td align="left"><? echo $yarn_dtls_array[$rows[csf('po_id')]][$rows[csf('prod_id')]]['count_name']; ?></td>
		                        		<td align="left"><? echo $fabrication[0] . ", " . $fabrication[1]; ?></td>
		                        		<td align="center"><? echo $fabrication[2]; ?></td>
		                        		<td align="center"><? echo $fabrication[3]; ?></td>
		                        	</tr>
		                        	<?
		                        }
		                        $j++;
		                    }
		                }


		                ?>
		            </tbody>
		        </table>


		        <div style="width:950px; margin-top:10px">
		        	<table align="center" cellspacing="0" width="950" border="1" rules="all" class="rpt_table">
		        		<thead bgcolor="#dddddd" align="center">
		        			<tr bgcolor="#CCCCFF">
		        				<th colspan="19" align="center"><strong>Dyes And Chemical Issue Requisition</strong></th>
		        			</tr>
		        		</thead>
		        		<?


			/*$sql_rec="select a.id, a.item_group_id, b.sub_process_id as sub_process_id, b.prod_id, b.dose_base, b.adj_type, b.comments, b.process_remark
			 from pro_recipe_entry_dtls b left join product_details_master a on b.prod_id=a.id and a.item_category_id in(5,6,7,23)
			 where b.mst_id in($recipe_id)  and b.status_active=1 and b.is_deleted=0 ";*/

			 $group_arr = return_library_array("select id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0", 'id', 'item_name');
			 $sql_rec = "select b.sub_process_id as sub_process_id, b.prod_id, b.dose_base, b.adj_type, b.comments, b.process_remark, b.item_lot
			 from pro_recipe_entry_dtls b
			 where b.mst_id in($recipe_id)  and b.status_active=1 and b.is_deleted=0";
			 $nameArray = sql_select($sql_rec);
			 foreach ($nameArray as $row) {
			 	$all_prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
			 	$item_lot_arr[$row[csf("prod_id")]] .= $row[csf("item_lot")] . ",";
			 }

			 $item_grop_id_arr = return_library_array("select id, item_group_id from product_details_master where id in(" . implode(",", $all_prod_id_arr) . ")", 'id', 'item_group_id');

			 $process_array = array();
			 $process_array_remark = array();
			 foreach ($nameArray as $row) {
			 	$process_array[$row[csf("sub_process_id")]] = $row[csf("process_remark")];
			 	$process_array_remark[$row[csf("sub_process_id")]][$row[csf("prod_id")]][$item_grop_id_arr[$row[csf("prod_id")]]][$row[csf("dose_base")]]["comments"] = $row[csf("comments")];
			 	if ($row[csf("sub_process_id")] > 92 && $row[csf("sub_process_id")] < 99) {
			 		$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"] = $row[csf("process_remark")];
			 	}

			 }

			/*$sql_rec_remark="select b.sub_process_id as sub_process_id,b.comments,b.process_remark from pro_recipe_entry_dtls b  where  b.mst_id in($recipe_id) and b.sub_process_id in(93,94,95,96,97,98) and b.status_active=1 and b.is_deleted=0";

			$nameArray_re=sql_select( $sql_rec_remark );
			foreach($nameArray_re as $row)
			{
				$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"]=$row[csf("process_remark")];
			}*/


			/*if($db_type==0)
			{
				$item_lot_arr=return_library_array("select b.prod_id,group_concat(b.item_lot) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot" );
			}
			else
			{
				$item_lot_arr=return_library_array("select b.prod_id,listagg((cast(b.item_lot as varchar2(500))),',') within group (order by b.prod_id) as yean_lot from pro_recipe_entry_dtls b, pro_recipe_entry_mst a where a.id=b.mst_id and a.id in ($recipe_id) group by b.prod_id", "prod_id", "yean_lot" );
			}



			$sql_dtls = "select a.requ_no, a.batch_id, a.recipe_id, b.id,
			b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
			c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id
			from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
			where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id=$data[1] order by b.id, b.seq_no";*/

			$sql_dtls = "(select a.requ_no, a.batch_id, a.recipe_id, b.id,
			b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
			c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id, b.item_lot
			from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
			where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id=$data[1])
			union
			(
			select a.requ_no, a.batch_id, a.recipe_id, b.id,
			b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
			null as item_description, null as item_group_id,  null as sub_group_name, null as item_size, null as unit_of_measure,null as avg_rate_per_unit,null as prod_id, b.item_lot
			from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b
			where a.id=b.mst_id and b.sub_process in($subprocessforwashin) and   a.id=$data[1]
			) order by id";
			// echo $sql_dtls;//die;
			$sql_result = sql_select($sql_dtls);

			$sub_process_array = array();
			$sub_process_tot_rec_array = array();
			$sub_process_tot_req_array = array();
			$sub_process_tot_value_array = array();

			foreach ($sql_result as $row) {
				$sub_process_tot_rec_array[$row[csf("sub_process")]] += $row[csf("recipe_qnty")];
				$sub_process_tot_req_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
				$sub_process_tot_value_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")] * $row[csf("avg_rate_per_unit")];
				//$sub_process_tot_ratio_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
			}
			$recipe_id=$data_array[0][csf("recipe_id")];
			$ratio_arr=array();
			$prevRatioData=sql_select( "select sub_process_id, total_liquor, liquor_ratio from pro_recipe_entry_dtls where mst_id=$recipe_id");
			foreach($prevRatioData as $prevRow)
			{
				$ratio_arr[$prevRow[csf('sub_process_id')]]['ratio']=$prevRow[csf('liquor_ratio')];
				$ratio_arr[$prevRow[csf('sub_process_id')]]['total_liquor']=$prevRow[csf('total_liquor')];
			}
						//var_dump($sub_process_tot_req_array);
			$i = 1;
			$k = 1;
			$recipe_qnty_sum = 0;
			$req_qny_edit_sum = 0;
			$recipe_qnty = 0;
			$req_qny_edit = 0;
			$req_value_sum = 0;
			$req_value_grand = 0;
			$recipe_qnty_grand = 0;
			$req_qny_edit_grand = 0;
			$fontst = "";
			$font_end = "";
			foreach ($sql_result as $row) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				if (!in_array($row[csf("sub_process")], $sub_process_array))
				{
					$sub_process_array[] = $row[csf('sub_process')];
					if ($k != 1) {
						?>
						<tr>
							<td colspan="6" align="right"><strong>Total :</strong></td>
							<td align="right"><strong><?php echo number_format($req_qny_edit_sum, 6, '.', ''); ?></strong></td>
						</tr>
						<?
					}
					$recipe_qnty_sum = 0;
					$req_qny_edit_sum = 0;
					$k++;

					//if ($row[csf("sub_process")] == 93 || $row[csf("sub_process")] == 94 || $row[csf("sub_process")] == 95 || $row[csf("sub_process")] == 96 || $row[csf("sub_process")] == 97 || $row[csf("sub_process")] == 98) {
					if(in_array($row[csf("sub_process")],$subprocessForWashArr))
					{
						$pro_remark = $process_array_remark[$row[csf("sub_process")]]["wash_comments"];
					}


					?>
					<tr bgcolor="#CCCCCC">
						<th colspan="19">
							<?

							$batch_ratio=explode(":",$data_array[0][csf("ratio")]);
							$lqr_ratio=$batch_ratio[0];
							$ratio_qty=$lqr_ratio.':'.$ratio_arr[$row[csf('sub_process')]]['ratio'];
							$total_liq = $ratio_arr[$row[csf('sub_process')]]['total_liquor'];
							$total_liquor_format = number_format($total_liq, 3, '.', '');
							$total_liquor='Total liquor(ltr)' . ": " . $total_liquor_format;
							if ($pro_remark == '') $pro_remark = ''; else $pro_remark .= $pro_remark . ', ';
							echo $dyeing_sub_process[$row[csf("sub_process")]] . ', ' . $pro_remark.'&nbsp;Liquor:Ratio &nbsp;'.$ratio_qty . ', ' .$total_liquor; ?>
						</th>
					</tr>
					<tr bgcolor="#EFEFEF">
						<th width="30">SL</th>
						<th width="150">Item Group</th>
                        <th width="70">Item Lot</th>
						<th width="180">Item Description</th>
						<th width="60">Ratio</th>
						<th width="70">GPL/%</th>
						<th width="80">Issue(KG)</th>
						<th width="50">Add-1</th>
						<th width="50">Add-2</th>
						<th width="50">Add-3</th>
						<th width="50">Add-4</th>
						<th>Total</th>
					</tr>
					<?
				}

							/*$iss_qnty=$row[csf("req_qny_edit")]*10000;
							$iss_qnty_kg=floor($iss_qnty/10000);
							$lkg=round($iss_qnty-$iss_qnty_kg*10000);
							$iss_qnty_gm=floor($lkg/10);
							$iss_qnty_mg=$lkg%10;*/
							/*$iss_qnty_gm=substr((string)$iss_qnty[1],0,3);
							$iss_qnty_mg=substr($iss_qnty[1],3);*/

							$req_qny_edit = explode(".", (string)$row[csf("req_qny_edit")]);
							$iss_qnty_kg = $req_qny_edit[0];
							if ($iss_qnty_kg == "") $iss_qnty_kg = 0;

							//$mg=($row[csf("req_qny_edit")]-$iss_qnty_kg)*1000*1000;
							//$iss_qnty_gm=floor($mg/1000);
							//$iss_qnty_mg=round($mg-$iss_qnty_gm*1000);

							//echo "gm=".substr($req_qny_edit[1],0,3)."; mg=".substr($req_qny_edit[1],3,3);


							//$num=(string)$req_qny_edit[1];
							//$next_number= str_pad($num,6,"0",STR_PAD_RIGHT);// ."000";
							//$rem= explode(".",($next_number/1000) );
							//echo $rem ."=";
							$iss_qnty_gm = substr($req_qny_edit[1], 0, 3);//$rem[0]; // floor($mg/1000);
							$iss_qnty_mg = substr($req_qny_edit[1], 3, 3);//$rem[1];//round($mg-$iss_qnty_gm*1000);
							$iss_qnty_gm = str_pad($iss_qnty_gm, 3, "0", STR_PAD_RIGHT);
							$iss_qnty_mg = str_pad($iss_qnty_mg, 3, "0", STR_PAD_RIGHT);

							$tdbgcolor = "#fff633";

							//echo "mg ".$req_qny_edit[1]."<br>";
							$adjQty = ($row[csf("adjust_percent")] * $row[csf("recipe_qnty")]) / 100;
							$comment = $process_array_remark[$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"];
							?>
							<tbody>
								<tr bgcolor="<? IF($row[csf("sub_process")]==40) {echo $tdbgcolor;} else echo $bgcolor; ?>">

									<td align="center"><? echo $i; ?></td>
		                        	<td>
										<?
											IF($row[csf("sub_process")]==40){ echo "<strong>";
												echo $group_arr[$row[csf("item_group_id")]];
												echo "</strong>";}
										else{
										echo $group_arr[$row[csf("item_group_id")]];}
									?>
									</td>
		                        	<td><? echo $row[csf("item_lot")]; ?></td>
		                        	<td><? echo $row[csf("item_description")] . ' ' . $row[csf("item_size")]; ?></td>
		                        	<td align="center"><? echo number_format($row[csf("ratio")], 6, '.', ''); ?></td>
		                        	<td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
		                        	<!--<td align="center"><? //echo $row[csf("adjust_percent")]; ?></td>-->
		                        	<td align="right"><? echo number_format($row[csf("req_qny_edit")], 6, '.', ''); ?></td>
									<td align="right"></td>
									<td align="right"></td>
									<td align="right"></td>
									<td align="right"></td>
									<td align="right"></td>

		                        </tr>
		                    </tbody>
		                        				<? $i++;
		                        				$recipe_qnty_sum += $row[csf('recipe_qnty')];
		                        				$req_qny_edit_sum += $row[csf('req_qny_edit')];

		                        				$recipe_qnty_grand += $row[csf('recipe_qnty')];
		                        				$req_qny_edit_grand += $row[csf('req_qny_edit')];
		                        			}
		                        			foreach ($sub_process_tot_rec_array as $val_rec) {
		                        				$totval_rec = $val_rec;
		                        			}
		                        			foreach ($sub_process_tot_req_array as $val_req) {
		                        				$totval_req = $val_req;
		                        			}

									//$recipe_qnty_grand +=$val_rec;
									//$req_qny_edit_grand +=$val_req;
									//$req_value_grand +=$req_value;
		                        			?>
		                        			<tr>
		                        				<td colspan="6" align="right"><strong>Total :</strong></td>

		                        				<td colspan="1" align="right"><strong><?php echo number_format($totval_req, 6, '.', ''); ?></strong></td>
		                        			</tr>
		                        			<tr>
		                        				<td colspan="6" align="right"><strong> Grand Total :</strong></td>

		                        				<td colspan="1" align="right"><strong><?php echo number_format($req_qny_edit_grand, 6, '.', ''); ?></strong></td>
		                        			</tr>
		                        		</table>
		                        		
		                        		<?
		                        		 echo signature_table(15, $data[0], "900px", "", 20);
		                        		?>
		                        	</div>
		                        </div>
		                        <?
		                        exit();
		}
	}
}

if ($action == "chemical_dyes_issue_requisition_print6")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	//print ($data[3]);die;
	//print_r ($data);
	if($data[3]==8) // Print Scandex
	{
		//echo $data[4];
		if ($data[4]==1) // ok to open without issue value;
		{
			$sql = "SELECT a.id, a.requ_no, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id
			from dyes_chem_issue_requ_mst a where a.id=$data[1] and a.company_id=$data[0]";
			//echo $sql;
			$dataArray = sql_select($sql);

			$recipe_id = $dataArray[0][csf('recipe_id')];

			$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
			$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
			$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
			$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
			$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
			$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
			$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");

			$batch_weight = 0;
			if ($db_type == 0)
			{
				$data_array = sql_select("SELECT group_concat(buyer_id) as buyer_id,group_concat(id) as recipe_id, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor, group_concat(concat_ws(':',batch_ratio,liquor_ratio) order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, group_concat(case when entry_form<>60 then batch_id end) as batch_id_rec_main, group_concat(labdip_no) as labdip_no from pro_recipe_entry_mst where id in($recipe_id)");
			}
			else
			{
				$data_array = sql_select("SELECT listagg(cast(buyer_id as varchar(4000)),',') within group (order by buyer_id) as buyer_id, listagg(cast(id as varchar(4000)),',') within group (order by id) as recipe_id, sum(total_liquor) as total_liquor, listagg(cast(batch_ratio || ':' || liquor_ratio as varchar(4000)),',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(cast(case when entry_form<>60 then batch_id end as varchar(4000)),',') within group (order by batch_id) as batch_id_rec_main, listagg(cast(batch_id as varchar(4000)),',') within group (order by batch_id) as batch_id, listagg(cast(labdip_no as varchar(4000)), ',') within group (order by id) as labdip_no from pro_recipe_entry_mst where id in($recipe_id)");
			}

			$batch_weight = $data_array[0][csf("batch_weight")];
			$batch_id = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id")])));

			$batch_id_rec_main = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id_rec_main")])));
			if ($batch_id_rec_main == "") $batch_id_rec_main = 0;

			$booking_no_sql="SELECT distinct booking_no, is_sales,id, entry_form from pro_batch_create_mst where id in($batch_id) and is_deleted=0 and status_active=1 ";
			//echo $booking_no_sql;
			$booking_no_arr=sql_select($booking_no_sql);
            // echo $booking_no_arr[0][csf('is_sales')];
            // echo "<pre>";print_r($booking_no_arr);die;
			foreach ($booking_no_arr as $row)
			{
		        $booking_arr[$row[csf("booking_no")]]=$row[csf("booking_no")];
				$entry_form_arr[$row[csf("id")]]=$row[csf("entry_form")];
		    }

			if ($db_type == 0)
			{
				$batchdata_array = sql_select("SELECT group_concat(batch_no) as batch_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(distinct color_id) as color_id from pro_batch_create_mst where id in($batch_id)");
			}
			else
			{
				$batchdata_array = sql_select("SELECT listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, listagg(color_id ,',') within group (order by id) as color_id, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($batch_id)");
			}

			$po_no = '';
			$job_no = '';
			$buyer_name = '';
			$style_ref_no = '';
			foreach (explode(",", $data_array[0][csf("batch_id")]) as $b_id)
            {
                if ($booking_no_arr[0][csf('is_sales')]==1)
                {
                    $po_sql = "SELECT b.po_job_no, b.style_ref_no, b.customer_buyer as party_id, d.po_number
                    from pro_batch_create_mst a, fabric_sales_order_mst b, wo_booking_dtls c, wo_po_break_down d
                    where a.sales_order_id=b.id and b.booking_id=c.booking_mst_id and c.po_break_down_id=d.id and a.id in(" . $b_id . ")
                    and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.booking_type in(1,4)
                    group by b.po_job_no, b.style_ref_no, b.customer_buyer, d.po_number";
					//echo $po_sql;
                    $po_data = sql_select($po_sql);
                    foreach ($po_data as $row)
                    {
                        $po_no .= $row[csf('po_number')] . ",";
                        $job_no .= $row[csf('po_job_no')] . ",";
                        if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
                        $buyer_name .= $buyer_library[$row[csf('party_id')]] . ",";
                    }
                }
                else
                {
                    if ($entry_form_arr[$b_id] == 36)
                    {
                        //echo "select distinct b.order_no,b.cust_style_ref, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(".$b_id.") ";
                        $po_data = sql_select("SELECT distinct b.order_no,b.cust_style_ref, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ");
                        foreach ($po_data as $row)
						{
                            $po_no .= $row[csf('order_no')] . ",";
                            $job_no .= $row[csf('subcon_job')] . ",";
                            if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
                            $buyer_name .= $buyer_library[$row[csf('party_id')]] . ",";
                        }
                    }
                    else
                    {
                        $po_data = sql_select("SELECT distinct b.po_number, c.job_no,c.style_ref_no, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ");
                        $job_nos="";
                        foreach ($po_data as $row)
                        {
                            $po_no .= $row[csf('po_number')] . ",";
                            $job_no .= $row[csf('job_no')] . ",";
                            //$job_nos .= $row[csf('job_no')] . ",";
                            if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
                            if ($job_nos == '') $job_nos ="'".$row[csf('job_no')]."'"; else $job_nos.= ","."'".$row[csf('job_no')]."'";
                            //$buyer_name.=$buyer_library[$row[csf('buyer_name')]].",";
                        }

                        foreach (explode(",", $data_array[0][csf("buyer_id")]) as $buyer_id)
						{
                            $buyer_name .= $buyer_library[$buyer_id] . ",";
                        }
                    }
                }
			}
			$jobNos=rtrim($job_nos,',');
			$jobnos=rtrim($job_no,',');

			//echo "select job_no, exchange_rate from wo_pre_cost_mst $job_nos_con";
			if($job_nos!='')
			{
				if($job_nos!='') $job_nos_con="where  job_no in(".$job_nos.")";//else $job_nos_con="where  job_no in('')";
				//echo "select job_no, exchange_rate from wo_pre_cost_mst $job_nos_con";
				$exchange_rate_arr = return_library_array("select job_no, exchange_rate from wo_pre_cost_mst $job_nos_con", 'job_no', 'exchange_rate');
			}
			//print_r($exchange_rate_arr);
			if($job_nos!='')
			{
                $condition= new condition();
                $condition->company_name("=$data[0]");
                if(str_replace("'","",$job_nos) !=''){
                    $condition->job_no("in($job_nos)");
                }
                $condition->init();

                $conversion= new conversion($condition);

                //echo $conversion->getQuery(); die;
                $conversion_qty_arr_process=$conversion->getQtyArray_by_jobAndProcess();
                $conversion_costing_arr_process=$conversion->getAmountArray_by_jobAndProcess();
			}
			//print_r($conversion_costing_arr_process);
			$dyeing_charge_arr=array(25,26,30,31,32,33,34,39,60,62,63,64,65,66,67,68,69,70,71,82,83,84,85,86,89,90,91,92,93,94,129,135,136,137,138,139,140,141,142,143,146);
			$jobnos=array_unique(explode(",",$jobnos));
			$tot_avg_dyeing_charge=0;$avg_dyeing_charge=0;$tot_dyeing_qty=$tot_dyeing_cost=0;//$tot_dyeing_cost=0;$tot_dyeing_qty=0;
			foreach($jobnos as $job)
			{
				$exchange_rate=$exchange_rate_arr[$job];

				foreach($dyeing_charge_arr as $process_id)
				{
					$dyeing_cost=array_sum($conversion_costing_arr_process[$job][$process_id]);
					//echo $job.'=='.$dyeing_cost.'='.$exchange_rate.'<br/>';
					$totdyeing_cost=$dyeing_cost;

					if($totdyeing_cost>0)
					{
						$tot_dyeing_qty+=array_sum($conversion_qty_arr_process[$job][$process_id]);
						$tot_dyeing_cost+=$dyeing_cost;
						//echo $tot_dyeing_cost.'='.$tot_dyeing_qty.'**'.$exchange_rate.'**'.$process_id.'<br>';
						//$avg_dyeing_charge+=$tot_dyeing_cost/$tot_dyeing_qty;
					}
				}
				//echo $tot_dyeing_cost.'<br>'.$tot_dyeing_qty;
				$avg_dyeing_charge+=$tot_dyeing_cost/$tot_dyeing_qty;
				$tot_avg_dyeing_charge+=$avg_dyeing_charge*$exchange_rate;
			}

			 //----------------------------------------------------- my close ending
			$sql_yarn_dtls = "SELECT  c.po_breakdown_id as po_id,d.yarn_lot as yarn_lot,d.yarn_count as yarn_count,d.brand_id as brand_id,d.prod_id
			from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
			where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id
			and a.id in($batch_id) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)  ";
			//echo $sql_yarn_dtls; die;

			$yarn_dtls_array = array();
			$result_sql_yarn_dtls = sql_select($sql_yarn_dtls);
			foreach ($result_sql_yarn_dtls as $row)
			{
				//$yarn_lot = implode(", ", array_unique(explode(",", $row[csf('yarn_lot')])));
				$yarn_id = array_unique(explode(",", $row[csf('yarn_lot')]));
				$yarn_lot = "";
				foreach ($yarn_id as $val)
				{
					if ($yarn_lot == "") $yarn_lot = $val; else $yarn_lot .= ", " . $val;
				}

				$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
				$brand_name = "";
				foreach ($brand_id as $val)
				{
					if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= ", " . $brand_arr[$val];
				}

				$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
				$count_name = "";
				foreach ($yarn_count as $val)
				{
					if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
				}
				$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['yarn_lot'] .= $row[csf('yarn_lot')].',';
				$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['brand_name'] .=  $brand_arr[$row[csf('brand_id')]].',';
				$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['count_name'] .= $count_arr[$row[csf('yarn_count')]].',';
			}
			//var_dump($yarn_dtls_array);
			$po_no = implode(", ", array_unique(explode(",", substr($po_no, 0, -1))));
			$job_no = implode(", ", array_unique(explode(",", substr($job_no, 0, -1))));
			$buyer_name = implode(",", array_unique(explode(",", substr($buyer_name, 0, -1))));

			$color_id = array_unique(explode(",", $batchdata_array[0][csf('color_id')]));
			$color_name = '';
			foreach ($color_id as $color)
			{
				$color_name .= $color_arr[$color] . ",";
			}

			$color_name = substr($color_name, 0, -1);

			//$recipe_id=$data_array[0][csf("recipe_id")];

			$recipe_arr=sql_select("SELECT a.working_company_id,a.company_id, b.batch_weight from pro_recipe_entry_mst a, pro_batch_create_mst b where a.batch_id=b.id and a.id=$recipe_id");

			$owner_company_id=$recipe_arr[0][csf('company_id')];
			$working_company_id=$recipe_arr[0][csf('working_company_id')];

			$nameArray = sql_select("SELECT plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");

			$group_id=$nameArray[0][csf('group_id')];
			?>
			<style>
				.fontWeight{font-weight: bold;}
			</style>
			<div style="width:950px;">

				<table width="950" cellspacing="0" align="center">
					<tr>
						<td align="center" style="font-size:28px;width: 680px;"><strong><u><? echo $company_library[$working_company_id].'<br>'."Dyes And Chemical Issue Requisition"//.$data[2]; ?>
						Report</u></strong></td>
						<td><span id="show_barcode_image"></span></td>
					</tr>
				</table>
				<table align="center" cellspacing="0" width="950" border="1" rules="all" class="rpt_table">
					<tr>
						<td border="1"><strong>Booking No </strong></td>
						<td><? echo implode(',',$booking_arr); ?></td>
						<td><strong>Order No</strong></td>
						<td><? echo $po_no; ?></td>
						<td><strong>Req. Date</strong></td>
						<td><? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
					</tr>
					<tr>
						<td><strong>Buyer</strong></td>
						<td><? echo implode(",", array_unique(explode(",", $buyer_name))); ?></td>
						<td><strong>Batch No</strong></td>
						<td><? echo $batchdata_array[0][csf('batch_no')]; ?></td>
						<td><strong>Batch Weight</strong></td>
						<td><strong><? $tot_batch_weight=$batch_weight+$batchdata_array[0][csf('batch_weight')];echo number_format($tot_batch_weight,4); ?></strong></td>
					</tr>
					<tr>
						<td><strong>Color</strong></td>
						<td><? echo $color_name; ?></td>
						<td><strong>Lab Dip</strong></td>
						<td>
							<?
							$labdip_no=implode(",", array_unique(explode(",", $data_array[0][csf("LABDIP_NO")])));
							echo chop($labdip_no, ',');
							?>
						</td>
						<td><strong>Machine No</strong></td>
						<td>
							<?
							$machine_data = sql_select("SELECT machine_no, floor_id from lib_machine_name where id='" . $dataArray[0][csf("machine_id")] . "'");
							echo $machine_data[0][csf('machine_no')];
							?>
						</td>
					</tr>
					<tr>
						<td><strong>Style Ref.</strong></td>
						<td><p><? echo implode(",", array_unique(explode(",", $style_ref_no))); ?></p></td>
						<td><strong>Req. ID </strong></td>
						<td><? echo $dataArray[0][csf('requ_no')]; ?></td>
						<td border="1"><strong>Job No </strong></td>
						<td><? echo $job_no; ?></td>
					</tr>
				</table>
				<script type="text/javascript" src="../../js/jquery.js"></script>
				<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
				<script>
					function generateBarcode( valuess )
					{
						var value = valuess;
						var btype = 'code39';
						var renderer ='bmp';
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
						$("#show_barcode_image").show().barcode(value, btype, settings);
					}
					generateBarcode('<? echo $dataArray[0][csf('requ_no')]; ?>');
				</script>
				<br>

		        <div style="width:950px; margin-top:10px">
		        	<table align="center" cellspacing="0" width="950" border="1" rules="all" class="rpt_table">
		        		<thead bgcolor="#dddddd" align="center">
		        			<tr bgcolor="#CCCCFF">
		        				<th colspan="19" align="center" style="font-size: 20px;"><strong>Dyes And Chemical Issue Requisition</strong></th>
		        			</tr>
		        		</thead>
		        		<?

						$group_arr = return_library_array("SELECT id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0", 'id', 'item_name');

						$sql_rec = "SELECT b.sub_process_id as sub_process_id, b.prod_id, b.dose_base, b.adj_type, b.comments, b.process_remark, b.item_lot from pro_recipe_entry_dtls b where b.mst_id in($recipe_id)  and b.status_active=1 and b.is_deleted=0";
						//echo $sql_rec;
						$nameArray = sql_select($sql_rec);
						foreach ($nameArray as $row)
						{
							$all_prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
							$item_lot_arr[$row[csf("prod_id")]] .= $row[csf("item_lot")] . ",";
						}

						$item_grop_id_arr = return_library_array("SELECT id, item_group_id from product_details_master where id in(" . implode(",", $all_prod_id_arr) . ")", 'id', 'item_group_id');

						$process_array = array();
						$process_array_remark = array();
						foreach ($nameArray as $row)
						{
							$process_array[$row[csf("sub_process_id")]] = $row[csf("process_remark")];
							$process_array_remark[$row[csf("sub_process_id")]][$row[csf("prod_id")]][$item_grop_id_arr[$row[csf("prod_id")]]][$row[csf("dose_base")]]["comments"] = $row[csf("comments")];

							if ($row[csf("sub_process_id")] > 92 && $row[csf("sub_process_id")] < 99)
							{
								$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"] = $row[csf("process_remark")];
							}
						}

						$sql_dtls = "(SELECT a.requ_no, a.batch_id, a.recipe_id, b.id,
						b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
						c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id, b.item_lot
						from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
						where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id=$data[1])
						union
						(
						SELECT a.requ_no, a.batch_id, a.recipe_id, b.id,
						b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
						null as item_description, null as item_group_id,  null as sub_group_name, null as item_size, null as unit_of_measure,null as avg_rate_per_unit,null as prod_id, b.item_lot
						from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b
						where a.id=b.mst_id and b.sub_process in($subprocessforwashin) and   a.id=$data[1]
						) order by id";
						//echo $sql_dtls;//die;
						$sql_result = sql_select($sql_dtls);

						$sub_process_array = array();
						$sub_process_tot_rec_array = array();
						$sub_process_tot_req_array = array();
						$sub_process_tot_value_array = array();

						foreach ($sql_result as $row)
						{
							$sub_process_tot_rec_array[$row[csf("sub_process")]] += $row[csf("recipe_qnty")];
							$sub_process_tot_req_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
							$sub_process_tot_value_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")] * $row[csf("avg_rate_per_unit")];
							//$sub_process_tot_ratio_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
						}
						$recipe_id=$data_array[0][csf("recipe_id")];
						$ratio_arr=array();

						$prevRatioData=sql_select("SELECT sub_process_id, total_liquor, liquor_ratio from pro_recipe_entry_dtls where mst_id=$recipe_id");

						foreach($prevRatioData as $prevRow)
						{
							$ratio_arr[$prevRow[csf('sub_process_id')]]['ratio']=$prevRow[csf('liquor_ratio')];
							$ratio_arr[$prevRow[csf('sub_process_id')]]['total_liquor']=$prevRow[csf('total_liquor')];
						}
									//var_dump($sub_process_tot_req_array);
						$i = 1;
						$k = 1;
						$recipe_qnty_sum = 0;
						$req_qny_edit_sum = 0;
						$recipe_qnty = 0;
						$req_qny_edit = 0;
						$req_value_sum = 0;
						$req_value_grand = 0;
						$recipe_qnty_grand = 0;
						$req_qny_edit_grand = 0;
						$fontst = "";
						$font_end = "";
						foreach ($sql_result as $row)
						{
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							if (!in_array($row[csf("sub_process")], $sub_process_array))
							{
								$sub_process_array[] = $row[csf('sub_process')];
								if ($k != 1)
								{
									?>
									<tr>
										<td colspan="6" align="right"><strong>Total :</strong></td>
										<td align="right"><strong><?php echo number_format($req_qny_edit_sum, 6, '.', ''); ?></strong></td>
									</tr>
									<?
								}
								$recipe_qnty_sum = 0;
								$req_qny_edit_sum = 0;
								$k++;

								//if ($row[csf("sub_process")] == 93 || $row[csf("sub_process")] == 94 || $row[csf("sub_process")] == 95 || $row[csf("sub_process")] == 96 || $row[csf("sub_process")] == 97 || $row[csf("sub_process")] == 98) {
								if(in_array($row[csf("sub_process")],$subprocessForWashArr))
								{
									$pro_remark = $process_array_remark[$row[csf("sub_process")]]["wash_comments"];
								}

								?>
								<tr bgcolor="#CCCCCC">
									<th colspan="19" style="font-size: 20px;">
										<?
										$batch_ratio=explode(":",$data_array[0][csf("ratio")]);
										$lqr_ratio=$batch_ratio[0];
										$ratio_qty=$lqr_ratio.':'.$ratio_arr[$row[csf('sub_process')]]['ratio'];
										$total_liq = $ratio_arr[$row[csf('sub_process')]]['total_liquor'];
										$total_liquor_format = number_format($total_liq, 3, '.', '');
										$total_liquor='Total liquor(ltr)' . ": " . $total_liquor_format;
										if ($pro_remark == '') $pro_remark = ''; else $pro_remark .= $pro_remark . ', ';
										echo $dyeing_sub_process[$row[csf("sub_process")]] . ', ' . $pro_remark.'&nbsp;Liquor:Ratio &nbsp;'.$ratio_qty . ', ' .$total_liquor; ?>
									</th>
								</tr>
								<tr bgcolor="#EFEFEF">
									<th width="30">SL</th>
									<th width="150">Function Name</th>
									<th width="70">Item Lot</th>
									<th width="180">Item Description</th>
									<th width="60">Ratio</th>
									<th width="70">GPL/%</th>
									<th width="80">Issue(KG)</th>
									<th width="50">Add-1</th>
									<th width="50">Add-2</th>
									<th width="50">Add-3</th>
									<th width="50">Add-4</th>
									<th>Total</th>
								</tr>
								<?
							}

								/*$iss_qnty=$row[csf("req_qny_edit")]*10000;
								$iss_qnty_kg=floor($iss_qnty/10000);
								$lkg=round($iss_qnty-$iss_qnty_kg*10000);
								$iss_qnty_gm=floor($lkg/10);
								$iss_qnty_mg=$lkg%10;*/
								/*$iss_qnty_gm=substr((string)$iss_qnty[1],0,3);
								$iss_qnty_mg=substr($iss_qnty[1],3);*/

								$req_qny_edit = explode(".", (string)$row[csf("req_qny_edit")]);
								$iss_qnty_kg = $req_qny_edit[0];
								if ($iss_qnty_kg == "") $iss_qnty_kg = 0;

								//$mg=($row[csf("req_qny_edit")]-$iss_qnty_kg)*1000*1000;
								//$iss_qnty_gm=floor($mg/1000);
								//$iss_qnty_mg=round($mg-$iss_qnty_gm*1000);

								//echo "gm=".substr($req_qny_edit[1],0,3)."; mg=".substr($req_qny_edit[1],3,3);


								//$num=(string)$req_qny_edit[1];
								//$next_number= str_pad($num,6,"0",STR_PAD_RIGHT);// ."000";
								//$rem= explode(".",($next_number/1000) );
								//echo $rem ."=";
								$iss_qnty_gm = substr($req_qny_edit[1], 0, 3);//$rem[0]; // floor($mg/1000);
								$iss_qnty_mg = substr($req_qny_edit[1], 3, 3);//$rem[1];//round($mg-$iss_qnty_gm*1000);
								$iss_qnty_gm = str_pad($iss_qnty_gm, 3, "0", STR_PAD_RIGHT);
								$iss_qnty_mg = str_pad($iss_qnty_mg, 3, "0", STR_PAD_RIGHT);

								$tdbgcolor = "#fff633";

								//echo "mg ".$req_qny_edit[1]."<br>";
								$adjQty = ($row[csf("adjust_percent")] * $row[csf("recipe_qnty")]) / 100;
								$comment = $process_array_remark[$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"];
								?>

								<tbody>
									<tr bgcolor="<? if($row[csf("sub_process")]==40) {echo $tdbgcolor;} else echo $bgcolor; ?>" class="<? if($row[csf("item_category")]==6) {echo 'fontWeight';}; ?>">

										<td align="center"><? echo $i; ?></td>
										<td title="<? echo $row[csf("item_category")];?>">
											<?
											if($row[csf("sub_process")]==40)
											{
												echo "<strong>";
												echo $row[csf("sub_group_name")];
												echo "</strong>";
											}
											else
											{
												echo $row[csf("sub_group_name")];
											}
											?>
										</td>
										<td><? echo $row[csf("item_lot")]; ?></td>
										<td><? echo $row[csf("item_description")] . ' ' . $row[csf("item_size")]; ?></td>
										<td align="center"><? echo number_format($row[csf("ratio")], 6, '.', ''); ?></td>
										<td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
										<td align="right"><? echo number_format($row[csf("req_qny_edit")], 6, '.', ''); ?></td>
										<td align="right"></td>
										<td align="right"></td>
										<td align="right"></td>
										<td align="right"></td>
										<td align="right"></td>
									</tr>
								</tbody>
								<? $i++;
								$recipe_qnty_sum += $row[csf('recipe_qnty')];
								$req_qny_edit_sum += $row[csf('req_qny_edit')];

								$recipe_qnty_grand += $row[csf('recipe_qnty')];
								$req_qny_edit_grand += $row[csf('req_qny_edit')];
						}

						foreach ($sub_process_tot_rec_array as $val_rec)
						{
							$totval_rec = $val_rec;
						}

						foreach ($sub_process_tot_req_array as $val_req)
						{
							$totval_req = $val_req;
						}

						//$recipe_qnty_grand +=$val_rec;
						//$req_qny_edit_grand +=$val_req;
						//$req_value_grand +=$req_value;
						?>
						<tr>
							<td colspan="6" align="right"><strong>Total :</strong></td>

							<td colspan="1" align="right"><strong><?php echo number_format($totval_req, 6, '.', ''); ?></strong></td>
						</tr>
						<tr>
							<td colspan="6" align="right"><strong> Grand Total :</strong></td>

							<td colspan="1" align="right"><strong><?php echo number_format($req_qny_edit_grand, 6, '.', ''); ?></strong></td>
						</tr>
					</table>
					<br>
					<?
					echo signature_table(15, $data[0], "900px");
					?>
				</div>
			</div>
			<?
			exit();
		}
		else  // cancel to open with issue value
		{
			$sql = "SELECT a.id, a.requ_no, a.location_id, a.requisition_date,a.method, a.requisition_basis, a.batch_id, a.recipe_id, a.machine_id
			from dyes_chem_issue_requ_mst a where a.id=$data[1] and a.company_id=$data[0]";
			$dataArray = sql_select($sql);

			$recipe_id = $dataArray[0][csf('recipe_id')];

			$company_library = return_library_array("SELECT id, company_name from lib_company", "id", "company_name");
			$buyer_library = return_library_array("SELECT id, buyer_name from lib_buyer", "id", "buyer_name");
			$country_arr = return_library_array("SELECT id, country_name from lib_country", "id", "country_name");
			$color_arr = return_library_array("SELECT id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
			$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
			$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
			$group_library = return_library_array("select id, group_name from lib_group", "id", "group_name");

			$batch_weight = 0;
			if ($db_type == 0)
			{
				$data_array = sql_select("SELECT group_concat(buyer_id) as buyer_id,group_concat(id) as recipe_id, group_concat(batch_id) as batch_id, sum(total_liquor) as total_liquor, group_concat(concat_ws(':',batch_ratio,liquor_ratio) order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, group_concat(case when entry_form<>60 then batch_id end) as batch_id_rec_main, group_concat(labdip_no) as labdip_no from pro_recipe_entry_mst where id in($recipe_id)");
			}
			else
			{
				$data_array = sql_select("SELECT listagg(cast(buyer_id as varchar(4000)),',') within group (order by buyer_id) as buyer_id, listagg(cast(id as varchar(4000)),',') within group (order by id) as recipe_id, sum(total_liquor) as total_liquor, listagg(cast(batch_ratio || ':' || liquor_ratio as varchar(4000)),',') within group (order by id) as ratio, sum(case when entry_form=60 then new_batch_weight end) as batch_weight, listagg(cast(case when entry_form<>60 then batch_id end as varchar(4000)),',') within group (order by batch_id) as batch_id_rec_main, listagg(cast(batch_id as varchar(4000)),',') within group (order by batch_id) as batch_id, listagg(cast(labdip_no as varchar(4000)), ',') within group (order by id) as labdip_no from pro_recipe_entry_mst where id in($recipe_id)");
			}

			$batch_weight = $data_array[0][csf("batch_weight")];
			$batch_id = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id")])));

			$batch_id_rec_main = implode(",", array_unique(explode(",", $data_array[0][csf("batch_id_rec_main")])));
			if ($batch_id_rec_main == "") $batch_id_rec_main = 0;

			$booking_no_sql="SELECT distinct booking_no,id, entry_form, is_sales from pro_batch_create_mst where id in($batch_id) and is_deleted=0 and status_active=1 ";
			$booking_no_arr=sql_select($booking_no_sql);
			foreach ($booking_no_arr as $row)
			{
		        $booking_arr[$row[csf("booking_no")]]=$row[csf("booking_no")];
				$entry_form_arr[$row[csf("id")]]=$row[csf("entry_form")];
		    }

			if ($db_type == 0)
			{
				$batchdata_array = sql_select("SELECT group_concat(batch_no) as batch_no, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight, group_concat(distinct color_id) as color_id from pro_batch_create_mst where id in($batch_id)");
			}
			else
			{
				$batchdata_array = sql_select("SELECT listagg(CAST(batch_no AS VARCHAR2(4000)),',') within group (order by id) as batch_no, listagg(color_id ,',') within group (order by id) as color_id, sum(case when id in($batch_id_rec_main) then batch_weight end) as batch_weight from pro_batch_create_mst where id in($batch_id)");
			}


			$po_no = '';
			$job_no = '';
			$buyer_name = '';
			$style_ref_no = '';
			foreach (explode(",", $data_array[0][csf("batch_id")]) as $b_id)
            {
                if ($booking_no_arr[0][csf('is_sales')]==1)
                {
                    $po_sql = "SELECT b.po_job_no, b.style_ref_no, b.customer_buyer as party_id, d.po_number
                    from pro_batch_create_mst a, fabric_sales_order_mst b, wo_booking_dtls c, wo_po_break_down d
                    where a.sales_order_id=b.id and b.booking_id=c.booking_mst_id and c.po_break_down_id=d.id and a.id in(" . $b_id . ")
                    and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.booking_type in(1,4)
                    group by b.po_job_no, b.style_ref_no, b.customer_buyer, d.po_number";
                    $po_data = sql_select($po_sql);
                    foreach ($po_data as $row)
                    {
                        $po_no .= $row[csf('po_number')] . ",";
                        $job_no .= $row[csf('po_job_no')] . ",";
                        if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
                        $buyer_name .= $buyer_library[$row[csf('party_id')]] . ",";
                    }
                }
                else
                {
                   if ($entry_form_arr[$b_id] == 36)
                    {
                        $po_data = sql_select("select distinct b.order_no,b.cust_style_ref, c.subcon_job, c.party_id from pro_batch_create_dtls a, subcon_ord_dtls b, subcon_ord_mst c where a.po_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ");
                        foreach ($po_data as $row)
						{
                            $po_no .= $row[csf('order_no')] . ",";
                            $job_no .= $row[csf('subcon_job')] . ",";
                            if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
                            $buyer_name .= $buyer_library[$row[csf('party_id')]] . ",";
                        }
                    }
                    else
                    {
                        $po_data = sql_select("select distinct b.po_number, c.job_no,c.style_ref_no, c.buyer_name from pro_batch_create_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.mst_id in(" . $b_id . ") ");
                        $job_nos="";
                        foreach ($po_data as $row)
						{
                            $po_no .= $row[csf('po_number')] . ",";
                            $job_no .= $row[csf('job_no')] . ",";
                            //$job_nos .= $row[csf('job_no')] . ",";
                            if ($style_ref_no == '') $style_ref_no = $row[csf('style_ref_no')]; else $style_ref_no .= "," . $row[csf('style_ref_no')];
                            if ($job_nos == '') $job_nos ="'".$row[csf('job_no')]."'"; else $job_nos.= ","."'".$row[csf('job_no')]."'";
                            //$buyer_name.=$buyer_library[$row[csf('buyer_name')]].",";
                        }

                        foreach (explode(",", $data_array[0][csf("buyer_id")]) as $buyer_id)
						{
                            $buyer_name .= $buyer_library[$buyer_id] . ",";
                        }
                    }
                }
			}
			$jobNos=rtrim($job_nos,',');
			$jobnos=rtrim($job_no,',');

			//echo "select job_no, exchange_rate from wo_pre_cost_mst $job_nos_con";
			if($job_nos!='')
			{
				if($job_nos!='') $job_nos_con="where  job_no in(".$job_nos.")";//else $job_nos_con="where  job_no in('')";
				//echo "select job_no, exchange_rate from wo_pre_cost_mst $job_nos_con";
				$exchange_rate_arr = return_library_array("SELECT job_no, exchange_rate from wo_pre_cost_mst $job_nos_con", 'job_no', 'exchange_rate');
			}
			//print_r($exchange_rate_arr);
			if($job_nos!='')
			{
				$condition= new condition();
				$condition->company_name("=$data[0]");

				if(str_replace("'","",$job_nos) !='')
				{
				$condition->job_no("in($job_nos)");
				}
				$condition->init();

				$conversion= new conversion($condition);
				//echo $conversion->getQuery(); die;
				$conversion_qty_arr_process=$conversion->getQtyArray_by_jobAndProcess();
				$conversion_costing_arr_process=$conversion->getAmountArray_by_jobAndProcess();
			}
			// print_r($conversion_costing_arr_process);
			$dyeing_charge_arr=array(25,26,30,31,32,33,34,39,60,62,63,64,65,66,67,68,69,70,71,82,83,84,85,86,89,90,91,92,93,94,129,135,136,137,138,139,140,141,142,143,146);

			$jobnos=array_unique(explode(",",$jobnos));

			$tot_avg_dyeing_charge=0;$avg_dyeing_charge=0;$tot_dyeing_qty=$tot_dyeing_cost=0;//$tot_dyeing_cost=0;$tot_dyeing_qty=0;
			foreach($jobnos as $job)
			{
				$exchange_rate=$exchange_rate_arr[$job];

				foreach($dyeing_charge_arr as $process_id)
				{
					$dyeing_cost=array_sum($conversion_costing_arr_process[$job][$process_id]);
					//echo $job.'=='.$dyeing_cost.'='.$exchange_rate.'<br/>';
					$totdyeing_cost=$dyeing_cost;

					if($totdyeing_cost>0)
					{
						$tot_dyeing_qty+=array_sum($conversion_qty_arr_process[$job][$process_id]);
						$tot_dyeing_cost+=$dyeing_cost;
						//echo $tot_dyeing_cost.'='.$tot_dyeing_qty.'**'.$exchange_rate.'**'.$process_id.'<br>';
						//$avg_dyeing_charge+=$tot_dyeing_cost/$tot_dyeing_qty;
					}
				}
				//echo $tot_dyeing_cost.'<br>'.$tot_dyeing_qty;
				$avg_dyeing_charge+=$tot_dyeing_cost/$tot_dyeing_qty;
				$tot_avg_dyeing_charge+=$avg_dyeing_charge*$exchange_rate;
			}

			//echo $avg_dyeing_charge.'='.$tot_dyeing_cost.'='.$dyeing_cost;
			//echo $style_ref_no;

			if ($db_type == 0)
			{
				$sql_yarn_dtls = "SELECT b.po_breakdown_id, b.prod_id, group_concat(a.yarn_lot) as yarn_lot, group_concat(a.brand_id) as brand_id, group_concat(a.yarn_count) as yarn_count from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, b.prod_id ";
			}
			else if ($db_type == 2)
			{
				$sql_yarn_dtls = "SELECT b.po_breakdown_id, b.prod_id, a.yarn_lot, a.brand_id, a.yarn_count
					from pro_grey_prod_entry_dtls a, pro_batch_create_dtls p, order_wise_pro_details b
					where a.prod_id=p.prod_id and a.id=b.dtls_id and p.mst_id in($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			}

			$yarn_dtls_array = array();
			$result_sql_yarn_dtls = sql_select($sql_yarn_dtls);

			foreach ($result_sql_yarn_dtls as $row)
			{
				//$yarn_lot = implode(", ", array_unique(explode(",", $row[csf('yarn_lot')])));
				$yarn_id = array_unique(explode(",", $row[csf('yarn_lot')]));
				$yarn_lot = "";
				foreach ($yarn_id as $val)
				{
					if ($yarn_lot == "") $yarn_lot = $val; else $yarn_lot .= ", " . $val;
				}

				$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
				$brand_name = "";

				foreach ($brand_id as $val)
				{
					if ($brand_name == "") $brand_name = $brand_arr[$val]; else $brand_name .= ", " . $brand_arr[$val];
				}

				$yarn_count = array_unique(explode(",", $row[csf('yarn_count')]));
				$count_name = "";
				foreach ($yarn_count as $val)
				{
					if ($count_name == "") $count_name = $count_arr[$val]; else $count_name .= ", " . $count_arr[$val];
				}

				$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['yarn_lot'] .= $row[csf('yarn_lot')].',';
				$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['brand_name'] .=  $brand_arr[$row[csf('brand_id')]].',';
				$yarn_dtls_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['count_name'] .= $count_arr[$row[csf('yarn_count')]].',';
			}

			//var_dump($yarn_dtls_array);
			$po_no = implode(", ", array_unique(explode(",", substr($po_no, 0, -1))));
			$job_no = implode(", ", array_unique(explode(",", substr($job_no, 0, -1))));
			$buyer_name = implode(",", array_unique(explode(",", substr($buyer_name, 0, -1))));

			$color_id = array_unique(explode(",", $batchdata_array[0][csf('color_id')]));
			$color_name = '';
			foreach ($color_id as $color)
			{
				$color_name .= $color_arr[$color] . ",";
			}

			$color_name = substr($color_name, 0, -1);


			//$recipe_id=$data_array[0][csf("recipe_id")];
			$recipe_arr=sql_select("SELECT a.working_company_id,a.company_id, b.batch_weight from pro_recipe_entry_mst a, pro_batch_create_mst b where a.batch_id=b.id and a.id=$recipe_id");

			$owner_company_id=$recipe_arr[0][csf('company_id')];

			$nameArray = sql_select("SELECT plot_no,group_id,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");

			$group_id=$nameArray[0][csf('group_id')];

			?>
			<style>
				.fontWeight{font-weight: bold;}
			</style>
			<div style="width:950px;">
				<table width="950" cellspacing="0" align="center">
					<tr>
						<td colspan="6" align="center" style="font-size:x-large;width: 680px;"><strong><u><? echo $company_library[$owner_company_id].'<br>'.$data[2]; ?>
							Report</u></strong></td>
						<td><span id="show_barcode_image"></span></td>
					</tr>
				</table>
				<table align="center" cellspacing="0" width="950" border="1" rules="all" class="rpt_table">
					<tr>
						<td border="1"><strong>Booking No </strong></td>
						<td><? echo implode(',',$booking_arr); ?></td>
						<td><strong>Order No</strong></td>
						<td><? echo $po_no; ?></td>
						<td><strong>Req. Date</strong></td>
						<td><? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
					</tr>
					<tr>
						<td><strong>Buyer</strong></td>
						<td><? echo implode(",", array_unique(explode(",", $buyer_name))); ?></td>
						<td><strong>Batch No</strong></td>
						<td><? echo $batchdata_array[0][csf('batch_no')]; ?></td>
						<td><strong>Batch Weight</strong></td>
						<td><? $tot_batch_weight=$batch_weight+$batchdata_array[0][csf('batch_weight')];echo number_format($tot_batch_weight,4); ?></td>
					</tr>
					<tr>
						<td><strong>Color</strong></td>
						<td><? echo $color_name; ?></td>
						<td><strong>Lab Dip</strong></td>
						<td>
							<?
							/*$labdip_no = '';
							$recipe_ids = explode(",", $data_array[0][csf("recipe_id")]);
							foreach ($recipe_ids as $recp_id) {
								$labdip_no .= $receipe_arr[$recp_id] . ",";
							}*/
							$labdip_no=implode(",", array_unique(explode(",", $data_array[0][csf("LABDIP_NO")])));
							echo chop($labdip_no, ',');
							// or-> echo $data_array[0][csf("labdip_no")];
							?>
						</td>
						<td><strong>Machine No</strong></td>
						<td>
							<?
							$machine_data = sql_select("select machine_no, floor_id from lib_machine_name where id='" . $dataArray[0][csf("machine_id")] . "'");
							echo $machine_data[0][csf('machine_no')];
							?>
						</td>
					</tr>
					<tr>
						<td><strong>Style Ref.</strong></td>
						<td ><p><? echo implode(",", array_unique(explode(",", $style_ref_no))); ?></p></td>
						<td width="90"><strong>Req. ID </strong></td>
						<td width="160px"><? echo $dataArray[0][csf('requ_no')]; ?></td>
						<td border="1"><strong>Job No </strong></td>
						<td><? echo $job_no; ?></td>
					</tr>
				</table>
				<script type="text/javascript" src="../../js/jquery.js"></script>
				<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
				<script>

					function generateBarcode( valuess )
					{
						var value = valuess;
						var btype = 'code39';
						var renderer ='bmp';
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
						$("#show_barcode_image").show().barcode(value, btype, settings);
					}
					generateBarcode('<? echo $dataArray[0][csf('requ_no')]; ?>');
				</script>

		        <div style="width:950px; margin-top:10px">
		        	<table align="center" cellspacing="0" width="950" border="1" rules="all" class="rpt_table">
		        		<thead bgcolor="#dddddd" align="center">
		        			<tr bgcolor="#CCCCFF">
		        				<th colspan="19" align="center" style="font-size: 20px;"><strong>Dyes And Chemical Issue Requisition</strong></th>
		        			</tr>
		        		</thead>
		        		<?

						$group_arr = return_library_array("SELECT id,item_name from lib_item_group where item_category in (5,6,7,23) and status_active=1 and is_deleted=0", 'id', 'item_name');
						$sql_rec = "select b.sub_process_id as sub_process_id, b.prod_id, b.dose_base, b.adj_type, b.comments, b.process_remark, b.item_lot
						from pro_recipe_entry_dtls b
						where b.mst_id in($recipe_id)  and b.status_active=1 and b.is_deleted=0";

						$nameArray = sql_select($sql_rec);

						foreach ($nameArray as $row)
						{
							$all_prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
							$item_lot_arr[$row[csf("prod_id")]] .= $row[csf("item_lot")] . ",";
						}

						$item_grop_id_arr = return_library_array("SELECT id, item_group_id from product_details_master where id in(" . implode(",", $all_prod_id_arr) . ")", 'id', 'item_group_id');

						$process_array = array();
						$process_array_remark = array();
						foreach ($nameArray as $row)
						{
							$process_array[$row[csf("sub_process_id")]] = $row[csf("process_remark")];
							$process_array_remark[$row[csf("sub_process_id")]][$row[csf("prod_id")]][$item_grop_id_arr[$row[csf("prod_id")]]][$row[csf("dose_base")]]["comments"] = $row[csf("comments")];

							if ($row[csf("sub_process_id")] > 92 && $row[csf("sub_process_id")] < 99)
							{
								$process_array_remark[$row[csf("sub_process_id")]]["wash_comments"] = $row[csf("process_remark")];
							}
						}

						$sql_dtls = "(SELECT a.requ_no, a.batch_id, a.recipe_id, b.id,
						b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
						c.item_description, c.item_group_id, c.sub_group_name, c.item_size, c.unit_of_measure,c.avg_rate_per_unit,c.id as prod_id, b.item_lot
						from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b, product_details_master c
						where a.id=b.mst_id and b.product_id=c.id and b.item_category in (5,6,7,23) and b.req_qny_edit!=0 and c.item_category_id in (5,6,7,23) and a.id=$data[1])
						union
						(
						SELECT a.requ_no, a.batch_id, a.recipe_id, b.id,
						b.sub_process, b.item_category, b.dose_base, b.ratio, b.recipe_qnty, b.adjust_percent, b.adjust_type, b.required_qnty, b.req_qny_edit,
						null as item_description, null as item_group_id,  null as sub_group_name, null as item_size, null as unit_of_measure,null as avg_rate_per_unit,null as prod_id, b.item_lot
						from dyes_chem_issue_requ_mst a,  dyes_chem_issue_requ_dtls b
						where a.id=b.mst_id and b.sub_process in($subprocessforwashin) and   a.id=$data[1]
						) order by id";
						// echo $sql_dtls;//die;

						$sql_result = sql_select($sql_dtls);

						$sub_process_array = array();
						$sub_process_tot_rec_array = array();
						$sub_process_tot_req_array = array();
						$sub_process_tot_value_array = array();

						foreach ($sql_result as $row)
						{
							$sub_process_tot_rec_array[$row[csf("sub_process")]] += $row[csf("recipe_qnty")];
							$sub_process_tot_req_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
							$sub_process_tot_value_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")] * $row[csf("avg_rate_per_unit")];
							//$sub_process_tot_ratio_array[$row[csf("sub_process")]] += $row[csf("req_qny_edit")];
						}
						$recipe_id=$data_array[0][csf("recipe_id")];
						$ratio_arr=array();

						$prevRatioData=sql_select("SELECT sub_process_id, total_liquor, liquor_ratio from pro_recipe_entry_dtls where mst_id=$recipe_id");

						foreach($prevRatioData as $prevRow)
						{
							$ratio_arr[$prevRow[csf('sub_process_id')]]['ratio']=$prevRow[csf('liquor_ratio')];
							$ratio_arr[$prevRow[csf('sub_process_id')]]['total_liquor']=$prevRow[csf('total_liquor')];
						}
						//var_dump($sub_process_tot_req_array);
						$i = 1;
						$k = 1;
						$recipe_qnty_sum = 0;
						$req_qny_edit_sum = 0;
						$recipe_qnty = 0;
						$req_qny_edit = 0;
						$req_value_sum = 0;
						$req_value_grand = 0;
						$recipe_qnty_grand = 0;
						$req_qny_edit_grand = 0;
						$fontst = "";
						$font_end = "";
						foreach ($sql_result as $row)
						{
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							if (!in_array($row[csf("sub_process")], $sub_process_array))
							{
								$sub_process_array[] = $row[csf('sub_process')];
								if ($k != 1)
								{
									?>
									<tr>
										<td colspan="6" align="right"><strong>Total :</strong></td>
										<td align="right"><strong><?php echo number_format($req_qny_edit_sum, 6, '.', ''); ?></strong></td>
									</tr>
									<?
								}
								$recipe_qnty_sum = 0;
								$req_qny_edit_sum = 0;
								$k++;

								if(in_array($row[csf("sub_process")],$subprocessForWashArr))
								{
									$pro_remark = $process_array_remark[$row[csf("sub_process")]]["wash_comments"];
								}

								?>
								<tr bgcolor="#CCCCCC">
									<th colspan="19" style="font-size: 20px;">
										<?

										$batch_ratio=explode(":",$data_array[0][csf("ratio")]);
										$lqr_ratio=$batch_ratio[0];
										$ratio_qty=$lqr_ratio.':'.$ratio_arr[$row[csf('sub_process')]]['ratio'];
										$total_liq = $ratio_arr[$row[csf('sub_process')]]['total_liquor'];
										$total_liquor_format = number_format($total_liq, 3, '.', '');
										$total_liquor='Total liquor(ltr)' . ": " . $total_liquor_format;
										if ($pro_remark == '') $pro_remark = ''; else $pro_remark .= $pro_remark . ', ';
										echo $dyeing_sub_process[$row[csf("sub_process")]] . ', ' . $pro_remark.'&nbsp;Liquor:Ratio &nbsp;'.$ratio_qty . ', ' .$total_liquor; ?>
									</th>
								</tr>
								<tr bgcolor="#EFEFEF">
									<th width="30">SL</th>
									<th width="150">Function Name</th>
									<th width="70">Item Lot</th>
									<th width="180">Item Description</th>
									<th width="60">Ratio</th>
									<th width="70">GPL/%</th>
									<th width="80">Issue(KG)</th>
									<th width="50">Add-1</th>
									<th width="50">Add-2</th>
									<th width="50">Add-3</th>
									<th width="50">Add-4</th>
									<th>Total</th>
								</tr>
								<?
							}

							/*$iss_qnty=$row[csf("req_qny_edit")]*10000;
							$iss_qnty_kg=floor($iss_qnty/10000);
							$lkg=round($iss_qnty-$iss_qnty_kg*10000);
							$iss_qnty_gm=floor($lkg/10);
							$iss_qnty_mg=$lkg%10;*/
							/*$iss_qnty_gm=substr((string)$iss_qnty[1],0,3);
							$iss_qnty_mg=substr($iss_qnty[1],3);*/

							$req_qny_edit = explode(".", (string)$row[csf("req_qny_edit")]);
							$iss_qnty_kg = $req_qny_edit[0];
							if ($iss_qnty_kg == "") $iss_qnty_kg = 0;

							//$mg=($row[csf("req_qny_edit")]-$iss_qnty_kg)*1000*1000;
							//$iss_qnty_gm=floor($mg/1000);
							//$iss_qnty_mg=round($mg-$iss_qnty_gm*1000);

							//echo "gm=".substr($req_qny_edit[1],0,3)."; mg=".substr($req_qny_edit[1],3,3);


							//$num=(string)$req_qny_edit[1];
							//$next_number= str_pad($num,6,"0",STR_PAD_RIGHT);// ."000";
							//$rem= explode(".",($next_number/1000) );
							//echo $rem ."=";
							$iss_qnty_gm = substr($req_qny_edit[1], 0, 3);//$rem[0]; // floor($mg/1000);
							$iss_qnty_mg = substr($req_qny_edit[1], 3, 3);//$rem[1];//round($mg-$iss_qnty_gm*1000);
							$iss_qnty_gm = str_pad($iss_qnty_gm, 3, "0", STR_PAD_RIGHT);
							$iss_qnty_mg = str_pad($iss_qnty_mg, 3, "0", STR_PAD_RIGHT);

							$tdbgcolor = "#fff633";

							//echo "mg ".$req_qny_edit[1]."<br>";
							$adjQty = ($row[csf("adjust_percent")] * $row[csf("recipe_qnty")]) / 100;
							$comment = $process_array_remark[$row[csf("sub_process")]][$row[csf("prod_id")]][$row[csf("item_group_id")]][$row[csf("dose_base")]]["comments"];
							?>
							<tbody>
								<tr bgcolor="<? if($row[csf("sub_process")]==40) {echo $tdbgcolor;} else echo $bgcolor; ?>" class="<? if($row[csf("item_category")]==6) {echo 'fontWeight';}; ?>">

									<td align="center"><? echo $i; ?></td>
									<td title="<? echo $row[csf("item_category")];?>">
										<?
											if($row[csf("sub_process")]==40)
											{
												echo "<strong>";
												echo $row[csf("sub_group_name")];
												echo "</strong>";
											}
											else
											{
												echo $row[csf("sub_group_name")];
											}
										?>
									</td>
									<td><? echo $row[csf("item_lot")]; ?></td>
									<td><? echo $row[csf("item_description")] . ' ' . $row[csf("item_size")]; ?></td>
									<td align="center"><? echo number_format($row[csf("ratio")], 6, '.', ''); ?></td>
									<td><? echo $dose_base[$row[csf("dose_base")]]; ?></td>
									<td align="right"><? echo number_format($row[csf("req_qny_edit")], 6, '.', ''); ?></td>
									<td align="right"></td>
									<td align="right"></td>
									<td align="right"></td>
									<td align="right"></td>
									<td align="right"></td>
								</tr>
							</tbody>
							<? $i++;
							$recipe_qnty_sum += $row[csf('recipe_qnty')];
							$req_qny_edit_sum += $row[csf('req_qny_edit')];

							$recipe_qnty_grand += $row[csf('recipe_qnty')];
							$req_qny_edit_grand += $row[csf('req_qny_edit')];
						}

						foreach ($sub_process_tot_rec_array as $val_rec)
						{
							$totval_rec = $val_rec;
						}

						foreach ($sub_process_tot_req_array as $val_req)
						{
							$totval_req = $val_req;
						}

						//$recipe_qnty_grand +=$val_rec;
						//$req_qny_edit_grand +=$val_req;
						//$req_value_grand +=$req_value;
						?>
						<tr>
							<td colspan="6" align="right"><strong>Total :</strong></td>
							<td colspan="1" align="right"><strong><?php echo number_format($totval_req, 6, '.', ''); ?></strong></td>
						</tr>
						<tr>
							<td colspan="6" align="right"><strong> Grand Total :</strong></td>
							<td colspan="1" align="right"><strong><?php echo number_format($req_qny_edit_grand, 6, '.', ''); ?></strong></td>
						</tr>
					</table>
					<br>
					<?
					echo signature_table(15, $data[0], "900px");
					?>
				</div>
			</div>
			<?
			exit();
		}
	}
}

?>