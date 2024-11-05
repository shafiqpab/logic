<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$colorname_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$location_arr=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
$division_arr=return_library_array( "select id,division_name from lib_division", "id", "division_name"  );
$department_arr=return_library_array( "select id,department_name from lib_department", "id", "department_name"  );
$section_arr=return_library_array( "select id,section_name from lib_section", "id", "section_name"  );
$designation_arr=return_library_array( "select id,system_designation from lib_designation", "id", "system_designation"  );

if ($action=="load_drop_down_buyer")
{	
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   	 
} 

if ($action=="load_drop_down_location")
{	
	echo create_drop_down( "cbo_location_id", 110, "select id,location_name from lib_location where company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );   	 
}  

if ($action=="load_drop_down_department")
{	
	echo create_drop_down( "cbo_department_id", 110, "select id,department_name from lib_department where division_id='$data' order by department_name","id,department_name", 1, "-- Select --", $selected, "load_drop_down( 'operator_wise_knitting_production_report_controller',this.value, 'load_drop_down_section', 'section_td' );" );  	 
} 

if ($action=="load_drop_down_section")
{	
	echo create_drop_down( "cbo_section_id", 110, "select id,section_name from lib_section where department_id='$data' order by section_name","id,section_name", 1, "-- Select --", $selected, "" );   	 
} 

if ($action=="print_report_button_setting")
{	
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id=59 and is_deleted=0 and status_active=1");
	echo $print_report_format; 	
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
								echo create_drop_down("company_id", 110, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0  order by company_name", "id,company_name", 1, "--Select--", $company, "load_drop_down( 'operator_wise_knitting_production_report_controller',this.value, 'load_drop_down_location', 'location_td' );", 0);
								?>
							</td>                 
							<td align="center" id="location_td">	
								<?
								echo create_drop_down("cbo_location_id", 110, $blank_array, "", 1, "--Select--", "", $dd, 0);
								?>
							</td>                 
							<td align="center">	
								<?
								echo create_drop_down("cbo_division_id", 110, "select id,division_name from lib_division comp where status_active =1 and is_deleted=0  order by division_name", "id,division_name", 1, "--Select--", $selected, "load_drop_down( 'operator_wise_knitting_production_report_controller',this.value, 'load_drop_down_department', 'department_td' );", 0);
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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('company_id').value + '**' + document.getElementById('cbo_location_id').value + '**' + document.getElementById('cbo_division_id').value + '**' + document.getElementById('cbo_department_id').value + '**' + document.getElementById('cbo_section_id').value + '**' + document.getElementById('txt_emp_code').value, 'create_operator_search_list_view', 'search_div', 'operator_wise_knitting_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('company_id').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+'**'+<?echo $style; ?>, 'create_job_no_search_list_view', 'search_div', 'operator_wise_knitting_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
  		$sql = "SELECT  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name
		    from wo_po_break_down a, wo_po_details_master b,ppl_cut_lay_mst c where a.job_no_mst=b.job_no and  a.is_deleted=0 and a.status_active=1 and 
	        b.status_active=1 and b.is_deleted=0 $company_con $date_cond   $search_field group by  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name order by job_no";
  	}
  	else
  	{
  		$sql = "SELECT  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name
		    from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and  a.is_deleted=0 and a.status_active=1 and 
	        b.status_active=1 and b.is_deleted=0 $company_con $date_cond   $search_field group by  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name order by job_no";

  	}	

	// echo $sql;

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
    $wo_company_id 	= str_replace("'","",$cbo_working_company);
    $operator_name 	= str_replace("'","",$txt_operator_name);
    $operator_id 	= str_replace("'","",$hidden_operator_id_card);
    $buyer_name 	= str_replace("'","",$cbo_buyer_name);
    $style_no 		= str_replace("'","",$txt_style_no);
    $job_no 		= str_replace("'","",$txt_job_no);
    $hidden_job_id 	= str_replace("'","",$hidden_job_id);
    $lot_ratio_no 	= str_replace("'","",$txt_lot_ratio_no);
    $ship_status 	= str_replace("'","",$cbo_shipment_status);
    $date_from 		= str_replace("'","",$txt_date_from);
    $date_to 		= str_replace("'","",$txt_date_to);
    $type 			= str_replace("'","",$type);

    $new_conn=integration_params(2);
    $employee_arr=return_library_array( "select id_card_no, (first_name||' '||middle_name|| '  ' || last_name) as emp_name from hrm_employee where status_active=1 and is_deleted=0",'id_card_no','emp_name',$new_conn);
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
	$supplier_arr=return_library_array( "select id,supplier_name from lib_supplier ",'id','supplier_name');
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );

	$sql_cond = "";
	$sql_cond .= ($company_id !=0) ? " and a.company_name=$company_id" : "";
	$sql_cond .= ($wo_company_id !=0) ? " and d.serving_company=$wo_company_id" : "";
	$sql_cond .= ($operator_id !="") ? " and e.operator_id=$operator_id" : "";
	$sql_cond .= ($buyer_name !=0) ? " and a.buyer_name=$buyer_name" : "";
	$sql_cond .= ($job_no !="") ? " and a.job_no_prefix_num='$job_no'" : "";
	$sql_cond .= ($style_no !="") ? " and a.style_ref_no='$style_no'" : "";
	$sql_cond .= ($hidden_job_id !="") ? " and a.id=$hidden_job_id" : "";
	$sql_cond .= ($ship_status !=0) ? " and b.shipping_status=$ship_status" : "";
		 	 
	if($date_from!="" || $date_to!="")
	{
		$production_date=" and d.production_date between $txt_date_from and $txt_date_to";
	}

	if ($db_type == 0)
	{
		$year_field = ",YEAR(a.insert_date) as YEAR";
	}
	else if ($db_type == 2)
	{
		$year_field = ",to_char(a.insert_date,'YYYY') as YEAR";
	}
	else
	{	
		$year_field = "";
   		
    } 
    // ================================ get po from lot ratio ==================================
    if($lot_ratio_no !="")
    {
    	// $lot_ratio_po_arr = return_library_array("SELECT c.order_id,c.order_id as orderID from ppl_cut_lay_mst a,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.cut_num_prefix_no=$lot_ratio_no","order_id","orderID");
    	$lot_ratio_po_arr = return_library_array("SELECT a.cutting_no,a.cutting_no as cutno from ppl_cut_lay_mst a where a.status_active=1 and a.is_deleted=0 and a.cut_num_prefix_no=$lot_ratio_no","cutting_no","cutno");

	    // print_r($lot_ratio_po_arr);die();
	    if(count($lot_ratio_po_arr)>0)
	    {
		    $cutting_no = "'".implode("','", array_filter($lot_ratio_po_arr))."'";
		    if($cutting_no !="")
		    {
		    	$sql_cond .= " and e.cut_no in($cutting_no)";
		    }
		}

    }

	$sql="SELECT  a.COMPANY_NAME $year_field,a.style_ref_no as STYLE,a.job_no_prefix_num as JOB_NO,b.id as PO_ID,b.PO_NUMBER,c.order_quantity as ORDER_QTY,c.plan_cut_qnty as PLAN_QTY,a.BUYER_NAME,
	  c.COUNTRY_SHIP_DATE,c.color_number_id as COLOR_ID,c.item_number_id as ITEM_ID,c.size_number_id as SIZE_ID,d.PRODUCTION_DATE,e.OPERATOR_ID,e.PRODUCTION_QNTY,d.PRODUCTION_TYPE,d.SERVING_COMPANY,d.CUT_NO,e.BUNDLE_QTY
	  from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
	  where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and b.id=d.po_break_down_id and c.id=e.color_size_break_down_id and d.id=e.mst_id and a.is_deleted=0 and a.status_active=1 and 
	  b.is_deleted=0 and	  
	  b.status_active=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.production_type in(50,51)  $sql_cond $production_date order by d.production_date";
	// echo $sql;die();
    $sql_res=sql_select ($sql);

    $data_array = array();
    $qty_array = array();
    $bundle_qty_array = array();
    foreach ($sql_res as $val) 
    {
    	$date = change_date_format($val['PRODUCTION_DATE']);
    	$data_array[$date][$val['OPERATOR_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']][$val['SIZE_ID']]['company_name'] = $val['COMPANY_NAME'];
    	$data_array[$date][$val['OPERATOR_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']][$val['SIZE_ID']]['year'] = $val['YEAR'];
    	$data_array[$date][$val['OPERATOR_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']][$val['SIZE_ID']]['buyer_name'] = $val['BUYER_NAME'];
    	$data_array[$date][$val['OPERATOR_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']][$val['SIZE_ID']]['style'] = $val['STYLE'];
    	$data_array[$date][$val['OPERATOR_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']][$val['SIZE_ID']]['job_no'] = $val['JOB_NO'];
    	$data_array[$date][$val['OPERATOR_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']][$val['SIZE_ID']]['serving_company'] = $val['SERVING_COMPANY'];
    	$data_array[$date][$val['OPERATOR_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']][$val['SIZE_ID']]['ship_date'] = $val['COUNTRY_SHIP_DATE'];
    	$data_array[$date][$val['OPERATOR_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']][$val['SIZE_ID']]['cut_no'] = $val['CUT_NO'];
    	$qty_array[$date][$val['OPERATOR_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']][$val['SIZE_ID']][$val['PRODUCTION_TYPE']] += $val['PRODUCTION_QNTY'];
    	$bundle_qty_array[$date][$val['OPERATOR_ID']][$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']][$val['SIZE_ID']][$val['PRODUCTION_TYPE']] += $val['BUNDLE_QTY'];
    }
    // echo "<pre>";print_r($qty_array);die();

	ob_start();  

	?>
	<fieldset style="width:1890px;">
		<!-- =========================== title part ====================== -->
	   	<table  cellspacing="0" style="justify-content: center;text-align: center;width: 1870px;" >
            <tr class="form_caption" style="border:none;justify-content: center;text-align: center;">
                   <td colspan="29" align="center" style="border:none;font-size:14px; font-weight:bold" >Operator Wise Knitting Production Report</td>
            </tr>
            <tr style="border:none;justify-content: center;text-align: center;">
                   <td colspan="29" align="center" style="border:none; font-size:16px; font-weight:bold">
                    Company Name:<? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>                                
                   </td>
             </tr>
             <tr style="border:none;justify-content: center;text-align: center;">
                   <td colspan="29" align="center" style="border:none;font-size:12px; font-weight:bold">
                    <? echo "Production date ".change_date_format(str_replace("'","",$txt_date_from))." to ". change_date_format(str_replace("'","",$txt_date_to)) ;?>
                   </td>
             </tr>
      	</table>
        <!-- ========================== report heading =================== -->
        <table cellspacing="0" border="1" class="rpt_table" width="1860" rules="all" align="left">
 			<thead>
 				<tr >
                   <th width="40">SL</th>
                   <th width="80">Production Date</th>
                   <th width="80">Operator Id Card No.</th>
                   <th width="140">Operator Name</th>
                   <th width="130">Company</th>
                   <th width="130">Working Company</th>
                   <th width="130">Buyer</th>
                   <th width="110">Style</th>
                   <th width="70"> Job Year</th>
                   <th width="120">Job</th>
                   <th width="130">GMT Item</th>
                   <th width="80">C. Ship Date</th>
                   <th width="100">Gmts. Color</th>                        
                   <th width="80">Size</th>
                   <th width="115">Lot Ratio No</th>
                   <th width="80">Knitting Issue<br> ( Pcs)</th>
                   <th width="80">Knitting Receive<br> (Pcs)</th>
                   <th width="80">Knitting Receive  Weight<br> (Lbs)</th>
                   <th width="68">Knitting  Receive Balance</th>
                  
                </tr>
 			</thead>
 		</table>
 		<!-- =============================== report body ============================== -->
        <div style="width:1880px;float: left;">     		
            <table cellspacing="0" border="1" class="rpt_table"  width="1860" rules="all" id="scroll_body"  align="left">
                <tbody >
          			<?       
             		$i=1;					
					foreach($data_array as $production_date => $date_data)	
					{
						foreach($date_data as $operator_id => $operator_data)	
						{
							foreach($operator_data as $po_id => $po_data)	
							{
								foreach ($po_data as $item_id => $item_data) 
								{
									foreach ($item_data as $color_id => $color_data) 
									{
										foreach ($color_data as $size_id => $row) 
										{
											if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";						
											$cut_no_ex = explode("-", $row['cut_no']);
											$lot_ratio_no = $cut_no_ex[2];

											$knitting_issue = $qty_array[$production_date][$operator_id][$po_id][$item_id][$color_id][$size_id][50];
											$knitting_receive = $qty_array[$production_date][$operator_id][$po_id][$item_id][$color_id][$size_id][51];
											$knitting_receive_weight = $bundle_qty_array[$production_date][$operator_id][$po_id][$item_id][$color_id][$size_id][51];
											$balance = $knitting_issue - $knitting_receive;
							                ?>
							                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
							                    <td width="40" align="left"><? echo $i; ?></td>
							                    <td width="80" align="center"><p>&nbsp;<? echo change_date_format($production_date);?></p></td>
							                    <td width="80" align="center"><p><? echo $operator_id;?></p></td>
							                    <td width="140" align="center"><p><? echo $employee_arr[$operator_id];?></p></td>
							                    <td width="130" align="left"><?php echo $company_arr[$row['company_name']]; ?></td>
							                    <td width="130" align="left"><?php  echo $company_arr[$row['serving_company']]; ?></td>
							                   
							                    <td width="130" align="left"><p><? echo $buyer_arr[$row['buyer_name']]; ?></p></td>
							                    <td width="110" align="left"><p><? echo $row['style'];?></p></td>
							                    <td width="70" align="center"><p><? echo $row['year'];?></p></td>
							                    <td width="120" align="center"><p><? echo  $row['job_no']; ?>  </p></td>
							                    <td  width="130" align="left"><p><? echo  $garments_item[$item_id]; ?> </p></td>
							                    <td width="80" align="left"><p><? echo change_date_format($row['ship_date']);?></p></td>
							                    <td width="100" align="left"> <p><?php echo $color_library[$color_id]; ?> </p></td>
							                    <td width="80" align="left"> <p><?php echo $size_library[$size_id]; ?> </p></td>
							                    <td width="115"  align="center"> <p><?php echo ltrim($lot_ratio_no,0); ?></p></td>
							                    <td width="80" align="right"> <?php echo number_format($knitting_issue,0) ?></td>
							                    <td width="80" align="right"> <?php echo number_format($knitting_receive,0) ?></td>
							                    <td width="80" align="right"> <?php echo number_format($knitting_receive_weight,0) ?></td>
							                    <td width="68" align="right"> <?php echo number_format($balance,0) ?></td>					                   
							        	  	</tr>
											<?
											$i++;	
											$knitting_issue_total+=$knitting_issue;
									   		$knitting_receive_total+=$knitting_receive;
									   		$knitting_receive_weight_total+=$knitting_receive_weight;
									   		$balance_total+=$balance;
										}
									}
								}	
							}
						}						
					}				
					?>
                </tbody>							    
            </table> 
        </div>  
        <!-- =================================== report footer ========================== -->
        <table cellspacing="0" border="1" class="rpt_table" width="1860" rules="all"  align="left">        	
        	<tfoot>
            	<tr>                  		
              		<th width="1535" colspan="15" style="justify-content: right;text-align: right;">Total</th>
              		
              		
              		<th width="80" style="justify-content: right;text-align: right;" id="knitting_issue"> <?php echo number_format($knitting_issue_total,2) ?></th>
              		<th width="80" style="justify-content: right;text-align: right;" id="knitting_receive"><?php echo number_format($knitting_receive_total,2) ?></th>
              		<th width="80" style="justify-content: right;text-align: right;" id="knitting_receive_weight"><?php echo number_format($knitting_receive_weight_total,2) ?></th>
              		<th width="68" style="justify-content: right;text-align: right;" id="balance"><?php echo number_format($balance_total,2) ?></th>
              	</tr>  
            </tfoot>
        </table> 
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




