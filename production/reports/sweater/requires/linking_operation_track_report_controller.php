<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$location_arr=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );


if ($action=="load_drop_down_buyer")
{	
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   	 
} 

 
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 100, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' ","id,location_name", 1, "-- Select --", $selected, "" );     	 
	exit();
}

if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1)";
if($db_type==2) $insert_year="extract( year from b.insert_date)";

if ($action == "operator_search_popup") 
{
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1, '', '', '');

	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array;
		var selected_idcard = new Array;
		var selected_name = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++)
			{
				$('#tr_' + i).trigger('click');
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value_job_bk(str) {
			

			if (str != "")
				str = str.split("_");

			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

			if (jQuery.inArray(str[1], selected_id) == -1) {
				selected_id.push(str[1]);
				selected_idcard.push(str[2]);
				selected_name.push(str[3]);

			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == str[1])
						break;
				}
				selected_id.splice(i, 1);
				selected_idcard.splice(i, 1);
				selected_name.splice(i, 1);
			}
			var id = '';
			var idcard = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				idcard += selected_idcard[i] + '*';
				name += selected_name[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			idcard = idcard.substr(0, idcard.length - 1);
			name = name.substr(0, name.length - 1);

			$('#hidden_operator_id').val(id);
			$('#hidden_operator_idcard').val(idcard);
			$('#hidden_operator_name').val(name);
		}
		function js_set_value_job(str)
		{
			$("#hidden_emp_number").val(str);
			parent.emailwindow.hide(); 
		}

	</script>

	</head>

	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:780px;">
				<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Company</th>
						<th>Location</th>
						<th>Division</th>
						<th>Department</th>
						<th>Section</th>
						<th>Employee Code</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
						<input type="hidden" name="hidden_operator_name" id="hidden_operator_name" value="" />
						<input type="hidden" name="hidden_operator_id" id="hidden_operator_id" value="" />
						<input type="hidden" name="hidden_operator_idcard" id="hidden_operator_idcard" value="" />
						<input type="hidden" name="hidden_emp_number" id="hidden_emp_number" value="" />
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								echo create_drop_down("company_id", 110, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0  order by company_name", "id,company_name", 1, "--Select--", $company, "load_drop_down( 'linking_operation_track_report_controller',this.value, 'load_drop_down_location', 'location_td' );", 0);
								?>
							</td>                 
							<td align="center" id="location_td">	
								<?
								echo create_drop_down("cbo_location_id", 110, $blank_array, "", 1, "--Select--", "", $dd, 0);
								?>
							</td>                 
							<td align="center">	
								<?
								echo create_drop_down("cbo_division_id", 110, "select id,division_name from lib_division comp where status_active =1 and is_deleted=0  order by division_name", "id,division_name", 1, "--Select--", $selected, "load_drop_down( 'linking_operation_track_report_controller',this.value, 'load_drop_down_department', 'department_td' );", 0);
								?>
							</td>                  
							<td align="center" id="department_td">	
								<?
								echo create_drop_down("cbo_department_id", 110, $blank_array, "", 1, "--Select--", "", $dd, 0);
								?>
							</td>                  
							<td align="center" id="section_td">	
								<?
								echo create_drop_down("cbo_section_id", 110, $blank_array, "", 1, "--Select--", "", $dd, 0);
								?>
							</td>  
							<td align="center">
								<input type="text" name="txt_emp_code" id="txt_emp_code" class="text_boxes">
							</td>	
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('company_id').value + '**' + document.getElementById('cbo_location_id').value + '**' + document.getElementById('cbo_division_id').value + '**' + document.getElementById('cbo_department_id').value + '**' + document.getElementById('cbo_section_id').value + '**' + document.getElementById('txt_emp_code').value, 'create_operator_search_list_view', 'search_div', 'linking_operation_track_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
						<!-- <tr>
							<td colspan="7" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr> -->
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "create_operator_search_list_view") 
{
	$data = explode('**', $data);
	$company_id = $data[0];
	$location_id = $data[1];
	$division_id = $data[2];
	$department_id = $data[3];
	$section_id = $data[4];
	$emp_code = $data[5];

	$new_conn=integration_params(2);

	$sql_con='';
	if(empty($company_id))
	{
		echo "Select Company First";die;
	}
	$sql_con .= ($company_id !=0) ? " and company_id=$company_id" : "";
	$sql_con .= ($location_id !=0) ? " and location_id=$location_id" : "";
	$sql_con .= ($division_id !=0) ? " and division_id=$division_id" : "";
	$sql_con .= ($department_id !=0) ? " and department_id=$department_id" : "";
	$sql_con .= ($section_id !=0) ? " and section_id=$section_id" : "";
	$sql_con .= ($emp_code !="") ? " and emp_code='$emp_code'" : "";

	// echo $sql_con;die();

	$arr = array(3=>$designation_arr, 4 => $company_arr, 5 => $location_arr,6=>$division_arr,7=>$department_arr,8=>$section_arr); 

  	// $sql = "SELECT  id,emp_code ,id_card_no,first_name,designation_id, company_id,location_id,division_id,department_id,section_id from lib_employee where status_active=1 $sql_con"; 
  	$sql = "SELECT emp_code,id_card_no,(first_name||' '||middle_name|| '  ' || last_name) as emp_name,designation_id, company_id, location_id, division_id,department_id,section_id from hrm_employee where status_active=1 and is_deleted=0 $sql_con";
  	// echo $sql;

	$conclick="emp_code,id_card_no,emp_name";
    echo create_list_view("tbl_list_search", "Emp Code,ID Card,Emp Name,Designation,Company,Location,Division,Department,Section", "100,100,100,100,100,100,100,100,100", "940", "320", 0, $sql, "js_set_value_job", $conclick, "", 1, "0,0,0,designation_id,company_id,location_id,division_id,department_id,section_id", $arr, "emp_code,id_card_no,emp_name,designation_id,company_id,location_id,division_id,department_id,section_id", "", '', '0,0,0,0,0,0,0,0', '', '', $new_conn);
    exit();
}

if ($action == "job_no_search_popup") 
{
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1, '', '', '');

	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array;
		var selected_name = new Array;
		var selected_style = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++)
			{
				$('#tr_' + i).trigger('click');
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value_job(str) {
			

			if (str != "")
				str = str.split("_");

			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

			if (jQuery.inArray(str[1], selected_id) == -1) {
				selected_id.push(str[1]);
				selected_name.push(str[2]);
				selected_style.push(str[3]);

			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == str[1])
						break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
				selected_style.splice(i, 1);
			}
			var id = '';
			var name = '';
			var style = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
				style += selected_style[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);
			style = style.substr(0, style.length - 1);

			$('#hide_job_id').val(id);
			$('#hide_job_no').val(name);
			$('#hide_style').val(style);
		}

	</script>

	</head>

	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:780px;">
				<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Company</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Job No</th>
						<th> Date</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
						<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
						<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
						<input type="hidden" name="hide_style" id="hide_style" value="" />
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								echo create_drop_down("company_id", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0  order by company_name", "id,company_name", 1, "--Select--", $company, "", 0);
								?>
							</td>                 
							<td align="center">	
								<?
								$search_by_arr = array(1 => "Job No", 2 => "Style Ref",3=> "Lot Ratio No");
								$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>     
							<td align="center" id="search_by_td">				
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
							</td> 
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
							</td>	
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('company_id').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+'**'+<?echo $style; ?>, 'create_job_no_search_list_view', 'search_div', 'linking_operation_track_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "create_job_no_search_list_view") 
{
	$data = explode('**', $data);
	$company_id = $data[0];
	$cbo_year = "";

	$company_con='';
	if(empty($company_id))
	{
		echo "Select Company First";die;
	}else{
		$company_con=" and b.company_name=$company_id";
	}

	$search_by = $data[1];
	$search_string = "'%" . trim($data[2]) . "%'";
	$search_field='';
	if(!empty($data[2]))
	{
		if ($search_by == 1)
			$search_field = " and b.job_no_prefix_num =$data[2]";
		else if ($search_by == 2)
			$search_field = " and b.style_ref_no like ".$search_string;
		else if($search_by == 3)
			$search_field = " and c.cut_num_prefix_no like ".$search_string;
	}
	

	$start_date = $data[3];
	$end_date = $data[4];

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = " and a.insert_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
		} else {
			$date_cond = " and a.insert_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	$arr = array(0 => $company_arr, 1 => $buyer_short_library);
	if ($db_type == 0)
	{
		$year_field = "YEAR(a.insert_date) as year";
    //$year_cond = " and YEAR(a.insert_date) = $cbo_year ";
	}
	else if ($db_type == 2)
	{
		$year_field = "to_char(a.insert_date,'YYYY') as year";
		
    //$year_cond = " and to_char(a.insert_date,'YYYY') = $cbo_year ";
	}
	else
	{$year_field = "";
   // $year_cond = "";
    } //defined Later
    
  
  	if($search_by == 3)
  	{
  		$sql = "SELECT  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name,$year_field
		    from wo_po_break_down a, wo_po_details_master b,ppl_cut_lay_mst c where a.job_no_mst=b.job_no and  a.is_deleted=0 and a.status_active=1 and 
	        b.status_active=1 and b.is_deleted=0 $company_con $date_cond   $search_field group by  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name ,a.insert_date order by job_no";
  	}
  	else
  	{
  		$sql = "SELECT  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name,$year_field
		    from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and  a.is_deleted=0 and a.status_active=1 and 
	        b.status_active=1 and b.is_deleted=0 $company_con $date_cond   $search_field group by  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name ,a.insert_date order by job_no";

  	}	

	 //echo $sql;

	$conclick="id,job_no_prefix_num,style_ref_no";
	/*$style=$data[5];
	if($style==1)
	{
		$conclick="id,style_ref_no";
	}*/

    echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "150,130,140,100", "760", "320", 0, $sql, "js_set_value_job", $conclick, "", 1, "company_name,buyer_name,0,0,0", $arr, "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "", '', '0,0,0,0,0,0,3', '',1);
    exit();
}


if($action=="generate_report")
{ 
    $process = array( &$_POST );

   	// print_r($process);die;
    extract(check_magic_quote_gpc( $process ));

    $company_id 	= str_replace("'","",$cbo_company_name);
    $cbo_location_id 	= str_replace("'","",$cbo_location_id);
    $operator_name 	= str_replace("'","",$txt_operator_name);
    $operator_id 	= str_replace("'","",$hidden_operator_id);
	$hidden_operator_id_card 	= str_replace("'","",$hidden_operator_id_card);
    $buyer_name 	= str_replace("'","",$cbo_buyer_name);
    $style_no 		= str_replace("'","",$txt_style_no);
    $job_no 		= str_replace("'","",$txt_job_no);
    $hidden_job_id 	= str_replace("'","",$hidden_job_id);  

    
    $date_from 		= str_replace("'","",$txt_date_from);
    $date_to 		= str_replace("'","",$txt_date_to);
    $type 			= str_replace("'","",$type);
	$style_no_arr = explode("*",$style_no);
	$style_nos = "'".implode("','",$style_no_arr)."'";

	$job_no = implode(",", explode("*",$job_no));


	if($operator_name!=""){
		$operator_id=$operator_name;
	}else{
		$operator_id=$hidden_operator_id_card;
	}

	// echo $job_no."=>".$hidden_job_id."=>".$style_no."=>".$operator_id;die;
     $new_conn=integration_params(2);
     $employee_arr=return_library_array( "select id_card_no, (first_name||' '||middle_name|| '  ' || last_name) as emp_name from hrm_employee where status_active=1 and is_deleted=0",'id_card_no','emp_name',$new_conn);

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');

	$sql_cond = "";
	$sql_cond .= ($company_id !=0) ? " and g.working_company=$company_id" : "";
	$sql_cond .= ($cbo_location_id !=0) ? " and g.working_location=$cbo_location_id" : "";
	$sql_cond .= ($operator_id !="") ? " and g.operator_id='$operator_id'" : "";
	$sql_cond .= ($buyer_name !=0) ? " and f.buyer_name=$buyer_name" : "";
	$sql_cond .= ($job_no !="") ? " and f.job_no_prefix_num in($job_no)" : "";
	$sql_cond .= ($style_no !="") ? " and f.style_ref_no in($style_nos)" : "";
	$sql_cond .= ($hidden_job_id !="") ? " and f.id in($hidden_job_id)" : "";
	
		 	 
	if($date_from!="" || $date_to!="")
	{
		$production_date=" and g.operation_date between $txt_date_from and $txt_date_to";
	}

	if ($db_type == 0) $year_field = ",YEAR(a.insert_date) as YEAR";
	else if ($db_type == 2) $year_field = ",to_char(a.insert_date,'YYYY') as YEAR";
	else $year_field = "";

    // ================================ get po from lot ratio ====================================
    $sql="SELECT   b.operation_id,sum(c.production_qnty) as production_qnty, d.id, d.job_no_mst,e.id as order_id, e.po_number, f.buyer_name, f.style_ref_no ,g.operation_date,g.operator_id from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f, ppl_cut_lay_bundle_operation b,pro_linking_operation_mst g,pro_linking_operation_dtls h where a.id=c.mst_id and g.id=h.mst_id and b.barcode_no=h.ticket_no  and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no  and c.barcode_no=b.mst_barcode_no and a.production_type=55 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0	  and b.status_active=1 and b.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0  and f.status_active=1 and f.is_deleted=0 $production_date $sql_cond group by  b.operation_id,  d.id, d.job_no_mst, d.country_id, d.color_number_id, d.item_number_id, d.size_number_id, e.id, e.po_number, f.buyer_name, f.style_ref_no,g.operation_date,g.operator_id order by  g.operation_date asc";

	// echo $sql;

	$data_array=sql_select($sql);
	
	foreach($data_array as $row)
	{
		$date_wise_arr[$row[csf("operation_date")]][$row[csf("job_no_mst")]][$row[csf("operator_id")]]['prod_date']=$row[csf("operation_date")];
		$date_wise_arr[$row[csf("operation_date")]][$row[csf("job_no_mst")]][$row[csf("operator_id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$date_wise_arr[$row[csf("operation_date")]][$row[csf("job_no_mst")]][$row[csf("operator_id")]]['buyer_name']=$row[csf("buyer_name")];
		$date_wise_arr[$row[csf("operation_date")]][$row[csf("job_no_mst")]][$row[csf("operator_id")]]['job_no']=$row[csf("job_no_mst")];
		$date_wise_arr[$row[csf("operation_date")]][$row[csf("job_no_mst")]][$row[csf("operator_id")]]['production_qnty']=$row[csf("production_qnty")];
		$date_wise_arr[$row[csf("operation_date")]][$row[csf("job_no_mst")]][$row[csf("operator_id")]]['operation_id']=$row[csf("operation_id")];
		$date_wise_arr[$row[csf("operation_date")]][$row[csf("job_no_mst")]][$row[csf("operator_id")]]['operation_name']=$employee_arr[$row[csf("operation_id")]];
		$operation_id_arr[$row[csf('operation_id')]]=$row[csf('operation_id')];
	
		// $ot_wise_op_arr[$row[csf("operation_date")]][$row[csf("operation_id")]]=$row[csf("operation_id")];
		$ot_wise_op_prod_qty[$row[csf("operation_date")]][$row[csf("job_no_mst")]][$row[csf("operator_id")]][$row[csf("operation_id")]]['prod_qty']+=$row[csf("production_qnty")];
	
	//=================================summery================================================

		$operation_data[$row[csf("operator_id")]]['prod_date']=$row[csf("operation_date")];
		$operation_data[$row[csf("operator_id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$operation_data[$row[csf("operator_id")]]['buyer_name']=$row[csf("buyer_name")];
		$operation_data[$row[csf("operator_id")]]['job_no']=$row[csf("job_no_mst")];
		$operation_data[$row[csf("operator_id")]]['production_qnty']=$row[csf("production_qnty")];
		$operation_data[$row[csf("operator_id")]]['operation_id']=$row[csf("operation_id")];
		$job_ot_wise_op_prod_qty[$row[csf("operator_id")]][$row[csf("operation_id")]]['prod_qty']+=$row[csf("production_qnty")];
	}
	//echo "<pre>"; print_r($operation_id_arr);die;
	//echo count($ot_wise_op_arr);
 
	$operation_name_arr=return_library_array( "select id, operation_name from lib_sewing_operation_entry where id in (".implode(',', $operation_id_arr).")",'id','operation_name');	

	ob_start();  
	$col_width=count($operation_id_arr);
	$width=(100*$col_width)+600;
	$width2=(100*$col_width)+520;
	?>

	  <fieldset style="width: <?=$width+100;?>px;margin: 0 auto;">
			<div class="title-part" style="margin: 0 auto;text-align: center;font-size: 14px;">				
				<h4>
					Linking Operation Track Report<br>
					Company : <?=$company_arr[$company_id]; ?><br>
					Date : <?=change_date_format($date_from); ?>To<?=change_date_format($date_to); ?>
				</h4>
			
			</div>
		<?php
		if($type==1){?>
			<div class="report-container-part">
				<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="<?=$width;?>"   align="center">
	             	<thead>
	             		<tr>
							<th width="40">SL</th>
							<th width="80">Production Date</th>
							<th width="80">Operator Id Card No.</th>
							<th width="100">Operator Name</th>
							<th width="100">Buyer</th>						
							<th width="100">Job</th>
							<th width="100">Style</th>
							<?
								foreach($operation_id_arr as $val){?>
									<th width="100"><?=$operation_name_arr[$val];?>&nbsp</th>
							<? } ?>			
	             		</tr>

	             	</thead>
	             </table>
	             <div style=" max-height:300px; width:<?=$width+20;?>px; overflow-y:scroll;margin-left:17px;" id="scroll_body">
					<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="<?=$width;?>"  align="center" id="table_body">
		             	<tbody>
		             		<?
		             	 	$i=1;
							$grand_total = 0;
		             		foreach($date_wise_arr as $production_date => $job_data)
							{
								foreach($job_data as $job_id => $operation_data)
								{
									$sub_tot = 0;
									foreach($operation_data as $operator_id => $row)
									{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";					
							        	?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
											<td width="40" align="center"><? echo $i; ?></td>
											<td width="80" align="center"><? echo change_date_format($production_date); ?></td>
											<td width="80" style="word-break:break-all"><? echo $operator_id; ?></td>
											<td width="100" style="word-break:break-all"><? echo $employee_arr[$operator_id]; ?></td>
											<td width="100" style="word-break:break-all"><?php echo $buyer_arr[$row['buyer_name']]; ?></td>
											<td width="100" style="word-break:break-all"><? echo $row['job_no']; ?></td>
											<td width="100" style="word-break:break-all"><? echo $row['style_ref_no']; ?></td>
											<?php
											foreach($operation_id_arr as $val)
											{
												$qty=$ot_wise_op_prod_qty[$production_date][$job_id][$operator_id][$val]['prod_qty'];
												$sub_total_arr[$production_date][$val]+=$qty;
												$grand_total_arr[$val]+=$qty;
												$grand_total+=$qty;
												?>
												<td width="100" align="right" style="word-break:break-all"><? echo $qty; ?></td>		
												<?
											}
											?>
										</tr>
										<?
										$i++;	
									}		
								}
								?>
								<tr>
									<td colspan="7" align="right"><b>Sub Total</b></td>						
									<? foreach($operation_id_arr as $vals)
									{
										?>
										<td width="100" align="right"><b><?=number_format($sub_total_arr[$production_date][$vals],0); ?></b></td>	
										<?					
									}
									?>  
								</tr>
						 	<?
							}	
				            ?>
		             	</tbody>
		            </table>	
					<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="<?=$width;?>"  align="center">
	             	<tfoot>
	             		<tr>
						 <th width="40">&nbsp;</th>
						 <th width="80">&nbsp;</th>
						 <th width="80">&nbsp;</th>
						 <th width="100">&nbsp;</th>
						 <th width="100">&nbsp;</th>
						 <th width="100">&nbsp;</th>	             			
	             		 <th width="100">Grand Total</th>	             			
	             			<? foreach($operation_id_arr as $val)
							{
								?>
								<th width="100" align="right"><?=number_format($grand_total_arr[$val],0); ?></th>	
								<?
							}
							?>      			     			
	             		</tr>
	             	</tfoot>
	            </table>	              	
	            </div>
				
			</div>
		<? } else { ?>
			<div class="report-container-part">
				<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="<?=$width2;?>"   align="center">
	             	<thead>
	             		<tr>
							<th width="40">SL</th>							
							<th width="80">Operator Id Card No.</th>
							<th width="100">Operator Name</th>
							<th width="100">Buyer</th>						
							<th width="100">Job</th>
							<th width="100">Style</th>
							<?					
								foreach($operation_id_arr as $val){?>									
									<th width="100"><?=$operation_name_arr[$val];?>&nbsp</th>
							<? } ?>			
	             		</tr>

	             	</thead>
	             </table>
	             <div style=" max-height:300px; width:<?=$width2+20;?>px; overflow-y:scroll;margin-left:17px;" id="scroll_body">
					<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="<?=$width2;?>"  align="center">
		             	<tbody>
		             		<?
							$i=1;
							foreach($operation_data as $operator_id => $row){
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";					
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
									<td width="40" align="center"><? echo $i; ?></td>										
									<td width="80" style="word-break:break-all"><? echo $operator_id; ?></td>
									<td width="100" style="word-break:break-all"><? echo $employee_arr[$operator_id]; ?></td>
									<td width="100" style="word-break:break-all"><?php echo $buyer_arr[$row['buyer_name']]; ?></td>
									<td width="100" style="word-break:break-all"><? echo $row['job_no']; ?></td>
									<td width="100" style="word-break:break-all"><? echo $row['style_ref_no']; ?></td>
									<?php
									foreach($operation_id_arr as $val){
										$qty=$job_ot_wise_op_prod_qty[$operator_id][$val]['prod_qty'];
										$sub_total_arr[$operator_id][$val]+=$qty;
										$grand_total_arr[$val]+=$qty;
										?>
										<td width="100" align="right"><? echo $qty; ?></td>
									<? } ?>						
								</tr>
								<?
								$i++;	
								?>
								<tr>
									<td colspan="6" align="right"><b>Sub Total</b></td>						
									<? foreach($operation_id_arr as $vals){?>
										<td width="100" align="right"><b><?=number_format($sub_total_arr[$operator_id][$vals],0); ?></b></td>	
									<? } ?>             			
								</tr>
						 	<?
							}	
				            ?>
		             	</tbody>
		            </table>	             	
	            </div>
				<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="<?=$width2;?>"  align="center">
	             	<tfoot>
	             		<tr>
							<td width="40">&nbsp;</td>
							<td width="80">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="100">&nbsp;</td>							
	             			<th width="100" align="right">Grand Total</th>
	             			<? foreach($operation_id_arr as $val){?>
									<th width="100" align="right"><?=number_format($grand_total_arr[$val],0); ?></th>	
							<? } ?>             			     			
	             		</tr>
	             	</tfoot>
	            </table>	 
			</div>
		<? } ?>
	</fieldset>
 	<?	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}




