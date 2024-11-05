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
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

if ($action=="load_drop_down_buyer")
{
	extract($_REQUEST);
    $choosenCompany = $choosenCompany; 
	echo create_drop_down( "cbo_buyer_name", 120, "select distinct buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($choosenCompany) $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );
	
  exit();	 
}





if($db_type==2) $insert_year="extract( year from b.insert_date)";

if ($action == "style_no_search_popup") 
{
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array;
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

		function js_set_value_style(str) {
			

			if (str != "")
				str = str.split("_");

			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

			if (jQuery.inArray(str[1], selected_id) == -1) {
				selected_id.push(str[1]);
				selected_name.push(str[2]);

			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == str[1])
						break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#hide_style_id').val(id);
			$('#hide_style_no').val(name);
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
						<input type="hidden" name="hide_style_id" id="hide_style_id" value="" />
						<input type="hidden" name="hide_style_no" id="hide_style_no" value="" />
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
								$search_by_arr = array(1 => "Job No", 2 => "Style Ref");
								$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('company_id').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+'**'+'<?echo $working_company; ?>'+'**'+'<?echo $year; ?>', 'create_style_no_search_list_view', 'search_div', 'production_status_report_sweater_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if ($action == "create_style_no_search_list_view") 
{
	$data = explode('**', $data);
	$company_id = $data[0];
    $w_company_id = $data[5];
    // echo "<pre>";print_r($data);die;
	$cbo_year = $data[6];

	$company_con='';
	if($company_id)$company_con=" and b.company_name in ($company_id)";

    
	$w_company_id_con='';
	if($w_company_id)$w_company_id_con=" and b.style_owner in ($w_company_id)";


	$search_by = $data[1];
	$search_string = "'%" . trim($data[2]) . "%'";
	$search_field='';
	if(!empty($data[2]))
	{
		if ($search_by == 1)
			$search_field = " and b.job_no_prefix_num =$data[2]";
		else if ($search_by == 2)
			$search_field = " and b.style_ref_no like ".$search_string;
	}
	

	$start_date = $data[3];
	$end_date = $data[4];

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = " and b.insert_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
		} else {
			$date_cond = " and b.insert_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	$arr = array(0 => $company_arr, 1 => $buyer_short_library);
    if(str_replace("'","",$cbo_year)!=0) $year_cond=" and extract (year from b.insert_date)=".str_replace("'","",$cbo_year).""; else $year_cond="";
    
  

	$sql = "SELECT  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name, b.style_owner,$insert_year as year from wo_po_break_down a, wo_po_details_master b where  a.job_id = b.id
	and  a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  $company_con $date_cond   $search_field $w_company_id_con  $year_cond  order by b.id desc";

	// echo $sql;die;

	$conclick="id,style_ref_no";
	

    echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "150,130,140,100", "760", "320", 0, $sql, "js_set_value_style", $conclick, "", 1, "company_name,buyer_name,0,0,0", $arr, "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "", '', '0,0,0,0,0,0,3', '',1);
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

			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == str[1])
						break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#hide_job_id').val(id);
			$('#hide_job_no').val(name);
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
								$search_by_arr = array(1 => "Job No", 2 => "Style Ref");
								$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('company_id').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+'**'+'<?echo $working_company; ?>'+'**'+'<?echo $year; ?>', 'create_job_no_search_list_view', 'search_div', 'production_status_report_sweater_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
    $w_company_id = $data[5];
    // echo "<pre>";print_r($data);die;
	$cbo_year = $data[6];
	$company_con ="";
	if($company_id)$company_con=" and b.company_name in ($company_id)";

    $w_company_id_con='';
	if($w_company_id)$w_company_id_con=" and b.style_owner in ($w_company_id)";

	$search_by = $data[1];
	$search_string = "'%" . trim($data[2]) . "%'";
	$search_field='';
	if(!empty($data[2]))
	{
		if ($search_by == 1)
			$search_field = " and b.job_no_prefix_num =$data[2]";
		else if ($search_by == 2)
			$search_field = " and b.style_ref_no like ".$search_string;
	}
	

	$start_date = $data[3];
	$end_date = $data[4];

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = " and b.insert_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
		} else {
			$date_cond = " and b.insert_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	$arr = array(0 => $company_arr, 1 => $buyer_short_library);
    if(str_replace("'","",$cbo_year)!=0) $year_cond=" and extract (year from b.insert_date)=".str_replace("'","",$cbo_year).""; else $year_cond="";
    
  

	$sql = "SELECT  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name,b.style_owner, b.buyer_name,$insert_year as year from wo_po_break_down a, wo_po_details_master b where  a.job_id = b.id
     and  a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0   $company_con $date_cond   $search_field $w_company_id_con  $year_cond  order by a.id desc";

	// echo $sql;die;

	$conclick="id,job_no_prefix_num";
	 $style=$data[5];
	if($style==1)
	{
		$conclick="id,style_ref_no";
	}

    echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "150,130,140,100", "760", "320", 0, $sql, "js_set_value_job", $conclick, "", 1, "company_name,buyer_name,0,0,0", $arr, "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "", '', '0,0,0,0,0,0,3', '',1);
    exit();
}

if ($action == "po_no_search_popup") 
{
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array;
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

		function js_set_value_po(str) {
			

			if (str != "")
				str = str.split("_");

			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

			if (jQuery.inArray(str[1], selected_id) == -1) {
				selected_id.push(str[1]);
				selected_name.push(str[2]);

			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == str[1])
						break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#hide_po_id').val(id);
			$('#hide_po_no').val(name);
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
						<th id="search_by_td_up" width="170">Please Enter PO No</th>
						<th> Date</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
						<input type="hidden" name="hide_po_id" id="hide_po_id" value="" />
						<input type="hidden" name="hide_po_no" id="hide_po_no" value="" />
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
								$search_by_arr = array(1 => "Po No", 2 => "Job NO",3 => "Style Ref");
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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('company_id').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+'**'+'<?echo $working_company; ?>'+'**'+'<?echo $year; ?>', 'create_po_no_search_list_view', 'search_div', 'production_status_report_sweater_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if ($action == "create_po_no_search_list_view") 
{
	$data = explode('**', $data);
	// print_r($data);die;
	$company_id = $data[0];
    $w_company_id = $data[5];
	$cbo_year = $data[6];

	$company_con='';
	
	if($company_id)$company_con=" and b.company_name in ($company_id)";

    $w_company_id_con='';
	if($w_company_id)$w_company_id_con=" and b.style_owner in ($w_company_id)";

	$search_by = $data[1];
	$search_string = "'%" . trim($data[2]) . "%'";
	$search_field='';
	if(!empty($data[2]))
	{
		if ($search_by == 1)
			$search_field = " and a.po_number ='$data[2]'";
		else if ($search_by == 2)
			$search_field = " and b.job_no_prefix_num like ".$search_string;
		else if ($search_by == 2)
			$search_field = " and b.style_ref_no like ".$search_string;
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
    
  

	$sql = "SELECT  a.id ,a.po_number,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name, b.style_owner,$insert_year as year from wo_po_break_down a, wo_po_details_master b where  a.job_id = b.id
   and  a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  $company_con $date_cond   $search_field $w_company_id_con  $year_cond  order by a.id desc";


	// echo $sql;die;

	$conclick="id,po_number";
	//  $style=$data[5];
	// if($style==1)
	// {
	// 	$conclick="id,style_ref_no";
	// }

    echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,PO.NO,Style Ref. No", "150,130,140,100", "760", "320", 0, $sql, "js_set_value_po", $conclick, "", 1, "company_name,buyer_name,0,0,0", $arr, "company_name,buyer_name,job_no_prefix_num,po_number,style_ref_no", "", '', '0,0,0,0,0,0,3', '',1);
    exit();
}



if($action=="generate_report")
{ 
    $process = array( &$_POST );
    // print_r($process);die;
    extract(check_magic_quote_gpc( $process ));

	$cbo_company_name=str_replace("'","", $cbo_company_name);
	$cbo_working_company=str_replace("'","", $cbo_working_company);
    $cbo_buyer_name=str_replace("'","", $cbo_buyer_name);
	$txt_style_no=str_replace("'","", $txt_style_no);
    $txt_job_no=str_replace("'","", $txt_job_no);
    $hidden_job_id=str_replace("'","", $hidden_job_id);
    $txt_po_no=str_replace("'","", $txt_po_no);
    // $hidd_po_id=str_replace("'","", $hidd_po_id);
	$hidden_po_id=str_replace("'","", $hidden_po_id);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	$cbo_year=str_replace("'","", $cbo_year);
	$cbo_ship_status=str_replace("'","", $cbo_ship_status);
	

	if($cbo_company_name)$company_cond=" and a.company_name in ($cbo_company_name)";
    if($cbo_working_company)$working_company_cond=" and d.serving_company in ($cbo_working_company)";
	if($cbo_buyer_name)$buyer_name_cond=" and a.buyer_name=$cbo_buyer_name";
    if($hidden_job_id)$job_id_cond=" and a.id=$hidden_job_id";
	if($txt_job_no)$job_cond=" and a.job_no_prefix_num like '%$txt_job_no%'";
	if($txt_style_no)$style_cond=" and a.style_ref_no like '%$txt_style_no%'";
	if($txt_po_no)$po_cond=" and b.po_number like '%$txt_po_no%'";
    if($hidden_po_id)$po_id_cond=" and b.id=$hidden_po_id";	

	if($txt_date_from !="" && $txt_date_to !="" )
    {
		$date_cond=" and d.production_date between'$txt_date_from' and '$txt_date_to'";
	}  

	$year_field_con=" and to_char(a.insert_date,'YYYY')";
    if($cbo_year!=0) $year_cond="$year_field_con=$cbo_year"; ($cbo_year="");  

	$ship_status_cond="";
    if($cbo_ship_status==1) $ship_status_cond="and b.shiping_status in (3)"; else if($cbo_ship_status==2) $ship_status_cond="and b.shiping_status in (1,2)";

	if($cbo_working_company)$ex_working_company_cond=" and g.delivery_company_id=$cbo_working_company";
	if($txt_date_from !="" && $txt_date_to !="" )
    {
		$ex_date_cond=" and g.delivery_date between'$txt_date_from' and '$txt_date_to'";
	}  

	if($cbo_working_company)$insp_working_company_cond=" and d.working_company=$cbo_working_company";
	if($txt_date_from !="" && $txt_date_to !="" )
    {
		$insp_date_cond=" and d.inspection_date between'$txt_date_from' and '$txt_date_to'";
	}  
    
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$floor_arr=return_library_array( "select id, FLOOR_NAME from LIB_PROD_FLOOR where status_active=1 and is_deleted=0",'id','FLOOR_NAME');
    $company_arr=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0",'id','company_name');
    $company_short_arr=return_library_array( "select id, company_short_name from lib_company where status_active=1 and is_deleted=0",'id','company_short_name');
	
	  /* =============================================================================================/
    /                                        Main Query                                             /
    / ============================================================================================ */
	$sql=("SELECT a.ID as JOB_ID,a.COMPANY_NAME,a.BUYER_NAME,a.JOB_NO,a.JOB_NO_PREFIX_NUM,a.STYLE_REF_NO,B.ID as PO_ID,b.PO_NUMBER,b.SHIPING_STATUS,c.COLOR_NUMBER_ID,d.SERVING_COMPANY,d.PRODUCTION_DATE,d.PRODUCTION_TYPE,d.PO_BREAK_DOWN_ID,e.PRODUCTION_QNTY,
	(CASE WHEN d.PRODUCTION_TYPE =1  THEN e.PRODUCTION_QNTY ELSE 0 END) AS KNITTING_QNTY,  
	(CASE WHEN d.PRODUCTION_TYPE =116  THEN e.PRODUCTION_QNTY ELSE 0 END) AS DISTRIBUTION_QNTY,  
	(CASE WHEN d.PRODUCTION_TYPE =4  THEN e.PRODUCTION_QNTY ELSE 0 END) AS LINKING_COMPLETE,  
	(CASE WHEN d.PRODUCTION_TYPE =117  THEN e.PRODUCTION_QNTY ELSE 0 END) AS LINKING_QNTY,  
	(CASE WHEN d.PRODUCTION_TYPE =111  THEN e.PRODUCTION_QNTY ELSE 0 END) AS TRIMMING_QNTY,  
	(CASE WHEN d.PRODUCTION_TYPE =118  THEN e.PRODUCTION_QNTY ELSE 0 END) AS WASH_RCV_QTY,  
	(CASE WHEN d.PRODUCTION_TYPE =3 THEN e.PRODUCTION_QNTY ELSE 0 END) AS WASH_COMPLETE_QTY,  
	(CASE WHEN d.PRODUCTION_TYPE =5 THEN e.PRODUCTION_QNTY ELSE 0 END) AS SEWING_COMPLETE_QTY,
	(CASE WHEN d.PRODUCTION_TYPE =67 THEN e.PRODUCTION_QNTY ELSE 0 END) AS IRON_QTY,
	(CASE WHEN d.PRODUCTION_TYPE =8 THEN e.PRODUCTION_QNTY ELSE 0 END) AS FINISH_QTY
	FROM WO_PO_DETAILS_MASTER a,WO_PO_BREAK_DOWN b,WO_PO_COLOR_SIZE_BREAKDOWN c,PRO_GARMENTS_PRODUCTION_MST d,PRO_GARMENTS_PRODUCTION_DTLS e
	WHERE a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and b.id=d.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.garments_nature=100 and e.PRODUCTION_QNTY!=0 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and d.STATUS_ACTIVE=1 and d.IS_DELETED=0 and e.STATUS_ACTIVE=1 and e.IS_DELETED=0 $company_cond $working_company_cond $buyer_name_cond $job_cond $job_id_cond $style_cond $po_cond $po_id_cond $date_cond $year_cond $ship_status_cond ");
	//echo $sql;die;
	$sql_result=sql_select($sql);     
	if(count($sql_result)==0)
	{
		?>
		<div style="text-align: center;color:red;font-weight:bold">Data not found</div>
		<?
		die;
	} 
	
	$data_array=array();
	$po_id_arr=array();
	foreach ($sql_result as $r) 
	{
		$data_array[$r['BUYER_NAME']][$r['JOB_ID']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['company_name']=$r['COMPANY_NAME'];
        $data_array[$r['BUYER_NAME']][$r['JOB_ID']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['serving_company']=$r['SERVING_COMPANY'];
		$data_array[$r['BUYER_NAME']][$r['JOB_ID']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['job_no']=$r['JOB_NO'];
		$data_array[$r['BUYER_NAME']][$r['JOB_ID']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['style_ref_no']=$r['STYLE_REF_NO'];
		$data_array[$r['BUYER_NAME']][$r['JOB_ID']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['po_number']=$r['PO_NUMBER'];
		
		$data_array[$r['BUYER_NAME']][$r['JOB_ID']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['knitting_qnty'] +=$r['KNITTING_QNTY'];
		$data_array[$r['BUYER_NAME']][$r['JOB_ID']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['distribution_qnty'] +=$r['DISTRIBUTION_QNTY'];
		$data_array[$r['BUYER_NAME']][$r['JOB_ID']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['linking_complete'] +=$r['LINKING_COMPLETE'];
		$data_array[$r['BUYER_NAME']][$r['JOB_ID']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['linking_qnty'] +=$r['LINKING_QNTY'];
		$data_array[$r['BUYER_NAME']][$r['JOB_ID']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['trimming_qnty'] +=$r['TRIMMING_QNTY'];
		$data_array[$r['BUYER_NAME']][$r['JOB_ID']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['wash_rcv_qty'] +=$r['WASH_RCV_QTY'];
		$data_array[$r['BUYER_NAME']][$r['JOB_ID']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['wash_complete_qty'] +=$r['WASH_COMPLETE_QTY'];
		$data_array[$r['BUYER_NAME']][$r['JOB_ID']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['sewing_qty'] +=$r['SEWING_COMPLETE_QTY'];
		$data_array[$r['BUYER_NAME']][$r['JOB_ID']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['iron_qty'] +=$r['IRON_QTY'];	
		$data_array[$r['BUYER_NAME']][$r['JOB_ID']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['inspection_qty'] +=$r['INSPECTION_QNTY'];	
		$data_array[$r['BUYER_NAME']][$r['JOB_ID']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['finish_qty'] +=$r['FINISH_QTY'];	

		$po_id_arr[$r['PO_ID']]=$r['PO_ID'];
		
	}
	 // echo"<pre>";print_r($data_array);die;	
			$con = connect();
			execute_query("DELETE from GBL_TEMP_ENGINE where user_id=$user_id and entry_form =128 and ref_from in(1,2)");
			oci_commit($con);

			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 128, 1,$po_id_arr, $empty_arr);

			$color_po_wise_qnty_sql=("SELECT a.PO_BREAK_DOWN_ID as PO_ID,a.ORDER_QUANTITY,a.COLOR_NUMBER_ID FROM WO_PO_COLOR_SIZE_BREAKDOWN a, GBL_TEMP_ENGINE b where  a.STATUS_ACTIVE=1 and a.IS_DELETED=0  and a.po_break_down_id=b.ref_val and b.user_id=$user_id  and b.entry_form =128");
			// echo $color_po_wise_qnty_sql;die;
			$color_po_wise_qnty_result=sql_select($color_po_wise_qnty_sql);
			$color_po_wise_qnty_arr=array();
			foreach ($color_po_wise_qnty_result as $v) 
			{
				$color_po_wise_qnty_arr[$v['PO_ID']][$v['COLOR_NUMBER_ID']]['order_quantity'] +=$v['ORDER_QUANTITY'];
			}

			// echo"<pre>";print_r($color_po_wise_qnty_arr);die;	


	//----------------------------------------------INSPECTION_QUERY------------------------------------------------------//
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 128, 2,$po_id_arr, $empty_arr);

	$sql_inspection=("SELECT c.color_id,c.INS_QTY,d.PO_BREAK_DOWN_ID as PO_ID
	FROM PRO_BUYER_INSPECTION_BREAKDOWN c,PRO_BUYER_INSPECTION d, GBL_TEMP_ENGINE e
	WHERE  d.id=c.mst_id and c.INS_QTY!=0 and  c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and d.STATUS_ACTIVE=1 and d.IS_DELETED=0 and d.po_break_down_id=e.ref_val and e.user_id=$user_id  and e.entry_form =128 ");
	// echo $sql_inspection;die;
	$result_inspection=sql_select($sql_inspection);
	$isp_data_array=array();
	foreach ($result_inspection as $v) 
	{
		$isp_data_array[$v['PO_ID']][$v['COLOR_ID']]['inspection_qnty']=$v['INS_QTY'];
		
	}
	// echo"<pre>";print_r($isp_data_array);die;	



	//---------------------------------------EX-FACTORY_QUERY------------------------------------------------------//

	$sql_delivery=" SELECT a.ID AS JOB_ID,a.COMPANY_NAME,a.BUYER_NAME,b.ID AS PO_ID,c.COLOR_NUMBER_ID,d.PRODUCTION_QNTY,g.DELIVERY_DATE,g.DELIVERY_COMPANY_ID,f.TOTAL_CARTON_QNTY,f.id as EX_FACTORY_ID
	FROM WO_PO_DETAILS_MASTER a,WO_PO_BREAK_DOWN b,WO_PO_COLOR_SIZE_BREAKDOWN c,PRO_EX_FACTORY_MST f,PRO_EX_FACTORY_DELIVERY_MST  g,PRO_EX_FACTORY_DTLS d
	WHERE a.id = c.job_id AND a.id = b.job_id AND b.id = c.po_break_down_id AND b.id = f.po_break_down_id AND g.id = f.delivery_mst_id and c.id=d.color_size_break_down_id  and f.id=d.mst_id and f.garments_nature=100 and d.PRODUCTION_QNTY!=0  AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND d.status_active = 1  AND d.is_deleted = 0  AND f.is_deleted = 0 AND f.status_active = 1 AND g.is_deleted = 0 AND g.status_active = 1   $company_cond $ex_working_company_cond $buyer_name_cond $job_cond $job_id_cond $style_cond $po_cond $po_id_cond $ex_date_cond $year_cond $ship_status_cond  order by a.id,b.id,c.id  ";
	// echo $sql_delivery;die;
	$delivery_result=sql_select($sql_delivery);
	// echo"<pre>";print_r($data_array);die;	

	$ex_data_array=array();
	foreach ($delivery_result as $v) 
	{

		$ex_data_array[$v['BUYER_NAME']][$v['JOB_ID']][$v['PO_ID']][$v['COLOR_NUMBER_ID']]['shipment_qty'] +=$v['PRODUCTION_QNTY'];	
		if(!$ex_id_arr[$v['EX_FACTORY_ID']])
		{
		$ex_data_array[$v['BUYER_NAME']][$v['JOB_ID']][$v['PO_ID']][$v['COLOR_NUMBER_ID']]['ctn_qty'] =$v['TOTAL_CARTON_QNTY'];
		}
		$ex_id_arr[$v['EX_FACTORY_ID']]= $v['EX_FACTORY_ID'];
		
	}
	//  echo"<pre>";print_r($ex_data_array);die;	

	execute_query("DELETE from GBL_TEMP_ENGINE where user_id=$user_id and entry_form =128 and ref_from in(1,2)");
	oci_commit($con);
	disconnect($con);	

	ob_start();

  	if($type==1)
  	{
   		 ?>
  		<fieldset style="width:1810px;">
        	   <table width="1000"  cellspacing="0"  align="center" >
                    <tr class="form_caption" style="border:none;justify-content: center;text-align: center;">
                           <td colspan="29" align="center" style="border:none;font-size:14px; font-weight:bold" > Production Status Report </td>
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
             <br />	
           
             <div style="width:1810px;" >
                    <table cellspacing="0" border="1" class="rpt_table"  width="1790px" rules="all" id="table_body"  style="max-height: 400px;overflow-y: auto;overflow-x: hidden;"  id="scroll_body">
					
						<thead>
							<tr>
								<th width="30">Sl.</th>
                                <th width="80">Company Name</th>
                                <th width="80">Working Company</th>
								<th width="130">Buyer Name</th>
								<th width="130">Job No</th>
								<th width="130">Style</th>
								<th width="130">Po No</th>
								<th width="100">Color</th>
								<th width="60">Order Qty</th>
								<th width="60"><p>Knitted Qty</p></th>
								<th width="60" title="Distribution Received Qty"><p>Dist.Rcvd.Qty</p></th>
								<th width="70" title="P2P Transfer Qty"><p>P2P Trns Qty</p></th>
								<th width="70" title="Linking Qty"><p>Linking Qty</p></th>
								<th width="60" title="Trimming/Mending Qty"><p>TRM/MND.Qty</p></th>
								<th width="65" title="Wash Recived Qty"><p>Wash Rcvd.Qty</p></th>
								<th width="65" title="Wash Complete Qty"><p>Wash Cmpl.Qty</p></th>
								<th width="90" title="Special Operation(Sewing) Qty"><p>Spl.Op.(Sewing)Qty</p></th>
								<th width="70" title="Iron Qty"><p>Iron.Qty</p></th>
								<th width="70" title="Inspection/Metal Pass Qty"><p>Ins.Pass.Qty</p></th>
								<th width="105" title="Finishing (Folding/Poly)"><p>Finis.(Folding/Poly)Qty</p></th>
								<th width="70" title="Pack(CTN)"><p>Pack(CTN)</p></th>
								<th width="75" title="Shipment Qty"><p>Shipment Qty</p></th>
								
							</tr>
						
						</thead>
						<tbody>						
							<?
								$i=1;
								$ttl_order_qty=0;
								$ttl_knitting_qnty=0;
								$ttl_distribution_qty=0;
								$ttl_linking_qty=0;
								$ttl_linking_complete_qty=0;
								$ttl_trimming_qty=0;
								$ttl_wash_rcv_qty=0;
								$ttl_wash_complete_qty=0;
								$ttl_sewing_qty = 0 ;
								$ttl_iron_qty=0;
								$ttl_inspection_qty=0;
								$ttl_finish_qty=0;
								$ttl_ctn_qty=0;
								$ttl_shipment_qty=0;
								
								foreach ($data_array as $buyer => $buyer_id) 
								{
									foreach ($buyer_id as $job => $job_id) 
									{
										foreach ($job_id as $po => $po_id) 
										{
											foreach ($po_id as $color => $val)
											{
												$shipment_qty=$ex_data_array[$buyer][$job][$po][$color]['shipment_qty'];
												$ctn_qty=$ex_data_array[$buyer][$job][$po][$color]['ctn_qty'];
												$inspection_qty=$isp_data_array[$po][$color]['inspection_qnty'];

												if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";																								
												?>                         
												<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
													<td width="30"><p><?=$i?></p></td>
                                                    <td width="80"><p><?=$company_short_arr[$val['company_name']];?></p></td>
                                                    <td width="80"><p><?=$company_short_arr[$val['serving_company']];?></p></td>
													<td width="130"><p><?=$buyer_arr[$buyer];?></p></td>
													<td width="130"><p><?=$val['job_no'];?></p></td>
													<td width="130"><p><?=$val['style_ref_no'];?></p></td>
													<td width="130"><p><?=$val['po_number'];?></p></td>
													<td width="130"><p><?=$color_arr[$color];?></p></td>
													<td width="60" align="right"><p><?=$color_po_wise_qnty_arr[$po][$color]['order_quantity'];?></p></td>
													<td width="60" align="right"><p><?=$val['knitting_qnty'];?></p></td>
													<td width="60" align="right"><p><?=$val['distribution_qnty'];?></p></td>
													<td width="70" align="right"><p><?=$val['linking_qnty'];?></p></td>
													<td width="70" align="right"><p><?=$val['linking_complete'];?></p></td>
													<td width="60" align="right"><p><?=$val['trimming_qnty'];?></p></td>
													<td width="65" align="right"><p><?=$val['wash_rcv_qty'];?></p></td>
													<td width="65" align="right"><p><?=$val['wash_complete_qty'];?></p></td>
													<td width="90" align="right"><p><?=$val['sewing_qty'];?></p></td>
													<td width="70" align="right"><p><?=$val['iron_qty'];?></p></td>
													<td width="70" align="right"><p><?=$inspection_qty;?></p></p></td>
													<td width="105" align="right"><p><?=$val['finish_qty'];?></p></td>
													<td width="70" align="right"><p><?=$ctn_qty;?></p></td>
													<td width="75" align="right"><p><?=$shipment_qty;?></p></td>
													
													
												</tr>
												<?
												$i++;
												$ttl_order_qty += $color_po_wise_qnty_arr[$po][$color]['order_quantity'];
												$ttl_knitting_qnty += $val['knitting_qnty'];
												$ttl_distribution_qty += $val['distribution_qnty'] ;
												$ttl_linking_qty += $val['linking_qnty'] ;
												$ttl_linking_complete_qty += $val['linking_complete'] ;
												$ttl_trimming_qty += $val['trimming_qnty'] ;
												$ttl_wash_rcv_qty += $val['wash_rcv_qty'] ;
												$ttl_wash_complete_qty += $val['wash_complete_qty'] ;
												$ttl_iron_qty += $val['iron_qty'];
												$ttl_inspection_qty += $inspection_qty;
												$ttl_sewing_qty += $val['sewing_qty'] ;
												$ttl_finish_qty += $val['finish_qty'] ;
												$ttl_ctn_qty += $ctn_qty;
												$ttl_shipment_qty += $shipment_qty;											
												
											}
											
										}
										
									}
									
								}
							?>								
						</tbody>		
						<tfoot>
							<tr>
								<th width="30"></th>
                                <th width="80"></th>
								<th width="80"></th>
								<th width="130"></th>
								<th width="130"></th>
								<th width="130"></th>
								<th width="130"></th>
								<th width="100">Total</th>
								<th width="60"><p><?=$ttl_order_qty;?></p></th>
								<th width="60"><p><?=$ttl_knitting_qnty;?></p></th>
								<th width="60"><p><?=$ttl_distribution_qty;?></p></th>
								<th width="70"><p><?=$ttl_linking_qty;?></p></th>
								<th width="70"><p><?=$ttl_linking_complete_qty;?></p></th>
								<th width="60"><p><?=$ttl_trimming_qty;?></p></th>
								<th width="65"><p><?=$ttl_wash_rcv_qty;?></p></th>
								<th width="65"><p><?=$ttl_wash_complete_qty;?></p></th>
								<th width="90"><p><?=$ttl_sewing_qty;?></p></th>
								<th width="70"><p><?=$ttl_iron_qty;?></p></th>
								<th width="70"><p><?=$ttl_inspection_qty;?></p></th>
								<th width="105"><p><?=$ttl_finish_qty;?></p></th>
								<th width="70"><p><?=$ttl_ctn_qty;?></p></th>
								<th width="75"><p><?=$ttl_shipment_qty;?></p></th>
										
							</tr>

						</tfoot>
						
	                </table>        
	  		</div>
	  	</fieldset>
	 	<?
	}
	else if($type==2)
	{
		
		$sql=("SELECT a.ID as JOB_ID,a.COMPANY_NAME,a.BUYER_NAME,a.JOB_NO,a.JOB_NO_PREFIX_NUM,a.STYLE_REF_NO,a.GAUGE,B.ID as PO_ID,b.PO_NUMBER,b.SHIPING_STATUS,b.PUB_SHIPMENT_DATE,c.COLOR_NUMBER_ID,d.SERVING_COMPANY,d.PRODUCTION_DATE,d.PRODUCTION_TYPE,d.PO_BREAK_DOWN_ID,d.FLOOR_ID,e.PRODUCTION_QNTY,

			(CASE WHEN d.PRODUCTION_TYPE =1  THEN e.PRODUCTION_QNTY ELSE 0 END) AS KNITTING_QNTY,  
			(CASE WHEN d.PRODUCTION_TYPE =1  and d.PRODUCTION_DATE ='$txt_date_to' THEN e.PRODUCTION_QNTY ELSE 0 END) AS TODAY_KNIT_QNTY,  
			(CASE WHEN d.PRODUCTION_TYPE =1  THEN d.FLOOR_ID ELSE 0 END) AS KINT_FLOOR_ID,  

			(CASE WHEN d.PRODUCTION_TYPE =116  THEN e.PRODUCTION_QNTY ELSE 0 END) AS DISTRIBUTION_QNTY,  
			(CASE WHEN d.PRODUCTION_TYPE =116  and d.PRODUCTION_DATE ='$txt_date_to' THEN e.PRODUCTION_QNTY ELSE 0 END) AS TODAY_DIS_QNTY,

			(CASE WHEN d.PRODUCTION_TYPE =86  THEN e.PRODUCTION_QNTY ELSE 0 END) AS DIS_ISSUE_QNTY,  
			(CASE WHEN d.PRODUCTION_TYPE =86  and d.PRODUCTION_DATE ='$txt_date_to' THEN e.PRODUCTION_QNTY ELSE 0 END) AS TODAY_DIS_ISSUE_QNTY,

			(CASE WHEN d.PRODUCTION_TYPE =86  THEN d.FLOOR_ID ELSE 0 END) AS P2P_FLOOR_ID,  

			(CASE WHEN d.PRODUCTION_TYPE =4  THEN e.PRODUCTION_QNTY ELSE 0 END) AS LINKING_COMPLETE,  
			(CASE WHEN d.PRODUCTION_TYPE =4  and d.PRODUCTION_DATE ='$txt_date_to' THEN e.PRODUCTION_QNTY ELSE 0 END) AS TODAY_LINKING_COMPLETE,

			(CASE WHEN d.PRODUCTION_TYPE =111  THEN e.PRODUCTION_QNTY ELSE 0 END) AS TRIMMING_QNTY,  
			(CASE WHEN d.PRODUCTION_TYPE =111  and d.PRODUCTION_DATE ='$txt_date_to' THEN e.PRODUCTION_QNTY ELSE 0 END) AS TODAY_TRIMMING_QNTY,

			(CASE WHEN d.PRODUCTION_TYPE =3 THEN e.PRODUCTION_QNTY ELSE 0 END) AS WASH_QTY,  
			(CASE WHEN d.PRODUCTION_TYPE =3  and d.PRODUCTION_DATE ='$txt_date_to' THEN e.PRODUCTION_QNTY ELSE 0 END) AS TODAY_WASH_QTY,

			(CASE WHEN d.PRODUCTION_TYPE =119 THEN e.PRODUCTION_QNTY ELSE 0 END) AS FINISHING_QTY,  
			(CASE WHEN d.PRODUCTION_TYPE =119  and d.PRODUCTION_DATE ='$txt_date_to' THEN e.PRODUCTION_QNTY ELSE 0 END) AS TODAY_FINISHING_QTY,

			(CASE WHEN d.PRODUCTION_TYPE =113 THEN e.PRODUCTION_QNTY ELSE 0 END) AS IRON_QTY,
			(CASE WHEN d.PRODUCTION_TYPE =113  and d.PRODUCTION_DATE ='$txt_date_to' THEN e.PRODUCTION_QNTY ELSE 0 END) AS TODAY_IRON_QTY,
			
			(CASE WHEN d.PRODUCTION_TYPE =5 THEN e.PRODUCTION_QNTY ELSE 0 END) AS SEWING_QTY,
			(CASE WHEN d.PRODUCTION_TYPE =5 and d.PRODUCTION_DATE ='$txt_date_to' THEN e.PRODUCTION_QNTY ELSE 0 END) AS TODAY_SEWING_QTY,

			(CASE WHEN d.PRODUCTION_TYPE =8 THEN e.PRODUCTION_QNTY ELSE 0 END) AS PACK_QTY,
			(CASE WHEN d.PRODUCTION_TYPE =8 and d.PRODUCTION_DATE ='$txt_date_to' THEN e.PRODUCTION_QNTY ELSE 0 END) AS TODAY_PACK_QTY

			FROM WO_PO_DETAILS_MASTER a,WO_PO_BREAK_DOWN b,WO_PO_COLOR_SIZE_BREAKDOWN c,PRO_GARMENTS_PRODUCTION_MST d,PRO_GARMENTS_PRODUCTION_DTLS e
			WHERE a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and b.id=d.po_break_down_id  and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.garments_nature=100 and e.PRODUCTION_QNTY!=0 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and d.STATUS_ACTIVE=1 and d.IS_DELETED=0 and e.STATUS_ACTIVE=1 and e.IS_DELETED=0 
			$company_cond $working_company_cond $buyer_name_cond $job_cond $job_id_cond $style_cond $po_cond $po_id_cond $date_cond $year_cond $ship_status_cond  order by c.id");
			// echo $sql;die;
			$sql_result=sql_select($sql);     
			if(count($sql_result)==0)
			{
				?>
				<div style="text-align: center;color:red;font-weight:bold">Data not found</div>
				<?
				die;
			} 
	
			$data_array=array();
			$po_id_arr=array();
			foreach ($sql_result as $r) 
			{
				$data_array[$r['BUYER_NAME']][$r['STYLE_REF_NO']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['gauge']=$r['GAUGE'];
				$data_array[$r['BUYER_NAME']][$r['STYLE_REF_NO']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['pub_ship_date']=$r['PUB_SHIPMENT_DATE'];
				
				$data_array[$r['BUYER_NAME']][$r['STYLE_REF_NO']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['po_number']=$r['PO_NUMBER'];

				$data_array[$r['BUYER_NAME']][$r['STYLE_REF_NO']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['kint_floor_id'] .=$r['KINT_FLOOR_ID'].",";

				$data_array[$r['BUYER_NAME']][$r['STYLE_REF_NO']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['knitting_qnty'] +=$r['KNITTING_QNTY'];
				$data_array[$r['BUYER_NAME']][$r['STYLE_REF_NO']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['today_knit_qnty'] +=$r['TODAY_KNIT_QNTY'];

				$data_array[$r['BUYER_NAME']][$r['STYLE_REF_NO']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['distribution_qnty'] +=$r['DISTRIBUTION_QNTY'];
				$data_array[$r['BUYER_NAME']][$r['STYLE_REF_NO']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['today_dis_qnty'] +=$r['TODAY_DIS_QNTY'];

				
				$data_array[$r['BUYER_NAME']][$r['STYLE_REF_NO']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['p2p_floor_id'].=$r['P2P_FLOOR_ID'].",";

				$data_array[$r['BUYER_NAME']][$r['STYLE_REF_NO']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['dis_issue_qnty'] +=$r['DIS_ISSUE_QNTY'];
				$data_array[$r['BUYER_NAME']][$r['STYLE_REF_NO']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['today_dis_issue_qnty'] +=$r['TODAY_DIS_ISSUE_QNTY'];

				$data_array[$r['BUYER_NAME']][$r['STYLE_REF_NO']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['linking_complete'] +=$r['LINKING_COMPLETE'];
				$data_array[$r['BUYER_NAME']][$r['STYLE_REF_NO']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['today_link_complete'] +=$r['TODAY_LINKING_COMPLETE'];

				$data_array[$r['BUYER_NAME']][$r['STYLE_REF_NO']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['trimming_qnty'] +=$r['TRIMMING_QNTY'];
				$data_array[$r['BUYER_NAME']][$r['STYLE_REF_NO']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['today_trimming_qnty'] +=$r['TODAY_TRIMMING_QNTY'];

				$data_array[$r['BUYER_NAME']][$r['STYLE_REF_NO']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['wash_qty'] +=$r['WASH_QTY'];
				$data_array[$r['BUYER_NAME']][$r['STYLE_REF_NO']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['today_wash_qty'] +=$r['TODAY_WASH_QTY'];

				$data_array[$r['BUYER_NAME']][$r['STYLE_REF_NO']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['finishing_qty'] +=$r['FINISHING_QTY'];
				$data_array[$r['BUYER_NAME']][$r['STYLE_REF_NO']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['today_finishing_qty'] +=$r['TODAY_FINISHING_QTY'];

				$data_array[$r['BUYER_NAME']][$r['STYLE_REF_NO']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['iron_qty'] +=$r['IRON_QTY'];
				$data_array[$r['BUYER_NAME']][$r['STYLE_REF_NO']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['today_iron_qty'] +=$r['TODAY_IRON_QTY'];

				$data_array[$r['BUYER_NAME']][$r['STYLE_REF_NO']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['sewing_qty'] +=$r['SEWING_QTY'];
				$data_array[$r['BUYER_NAME']][$r['STYLE_REF_NO']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['today_sewing_qty'] +=$r['TODAY_SEWING_QTY'];

				$data_array[$r['BUYER_NAME']][$r['STYLE_REF_NO']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['pack_qty'] +=$r['PACK_QTY'];	
				$data_array[$r['BUYER_NAME']][$r['STYLE_REF_NO']][$r['PO_ID']][$r['COLOR_NUMBER_ID']]['today_pack_qty'] +=$r['TODAY_PACK_QTY'];	

				$po_id_arr[$r['PO_ID']]=$r['PO_ID'];
				
			}
				//echo"<pre>";print_r($data_array);die;	
					$con = connect();
					execute_query("DELETE from GBL_TEMP_ENGINE where user_id=$user_id and entry_form =128 and ref_from in(1)");
					oci_commit($con);

					fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 128, 1,$po_id_arr, $empty_arr);

					$color_po_wise_qnty_sql=("SELECT a.PO_BREAK_DOWN_ID as PO_ID,a.ORDER_QUANTITY,a.COLOR_NUMBER_ID,a.PLAN_CUT_QNTY FROM WO_PO_COLOR_SIZE_BREAKDOWN a, GBL_TEMP_ENGINE b where  a.STATUS_ACTIVE=1 and a.IS_DELETED=0  and a.po_break_down_id=b.ref_val and b.user_id=$user_id  and b.entry_form =128");
					//echo $color_po_wise_qnty_sql;die;
					$color_po_wise_qnty_result=sql_select($color_po_wise_qnty_sql);
					$color_po_wise_qnty_arr=array();
					foreach ($color_po_wise_qnty_result as $v) 
					{
						$color_po_wise_qnty_arr[$v['PO_ID']][$v['COLOR_NUMBER_ID']]['order_quantity'] +=$v['ORDER_QUANTITY'];
						$color_po_wise_qnty_arr[$v['PO_ID']][$v['COLOR_NUMBER_ID']]['plan_cut_qnty'] +=$v['PLAN_CUT_QNTY'];
					}

					// echo"<pre>";print_r($color_po_wise_qnty_arr);die;	

			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 128, 2,$po_id_arr, $empty_arr);


		?>
		<fieldset style="width:2610px;">
			 <table width="1000"  cellspacing="0"  align="center" >
				  <tr class="form_caption" style="border:none;justify-content: center;text-align: center;">
						 <td colspan="29" align="center" style="border:none;font-size:14px; font-weight:bold" > Production Status Report </td>
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
		   <br />	
		 
		   <div style="width:2610px;" >
				  <table cellspacing="0" border="1" class="rpt_table"  width="2590px" rules="all" id="table_body"  style="max-height: 400px;overflow-y: auto;overflow-x: hidden;"  id="scroll_body">
					  <thead>
							<tr>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th colspan="3">Knitting</th>
								<th colspan="3">Penel Recvd</th>
								<th colspan="3">P2P</th>
								<th width="80" rowspan="2"  title="(Cum.Penal Rcv + Today Penal Rcv) - (Cum. P2P Delivery - Today P2P Delivery)">Penel WIP</th>

								<th colspan="3">Linking Production</th>
								<th colspan="3">Trim/Mending Production</th>
								<th colspan="4">Washing</th>
								<th colspan="3">Finishing Input</th>
								<th colspan="3">P2P-Iron</th>
								<th  width="80" rowspan="2" title="((Cum. Wash Pcs + Today. Wash Pcs) - (Cum.Iron - Today Iron))">Iron Wip</th>
								<th colspan="3">P2P-Finishing/Metal Pass</th>
								<th colspan="3">Carton</th>
								<th width="80" rowspan="2" title="((Cum.Iron + Today Iron) - (Cum. Carton - Today  Carton))" >CTN WIP</th>

							</tr>
						  <tr>
							  <th width="30">Sl.</th>
							  <th width="150">Buyer</th>
							  <th width="200">Style</th>
							  <th width="150">Color</th>
							  <th width="200">Order No.</th> 
							  <th width="100">Order Qty.</th>
							  <th width="100">GG</th>
							  <th width="100">Knit Floor</th>
							  <th width="100">P2P Floor</th>
							  <th width="120">Plan RFI</th>
							  <th width="100">Order Plan Qty.</th>

							  <th width="80">Today</th>
							  <th width="80">Cum.Knitting</th>
							  <th width="80" title="Order Plan Qty -Today Knitting - Cum.Knitting">Knitting BL</th>

							  <th width="80">Today</th>
							  <th width="80">Cum.Penal Rcv.</th>
							  <th width="80" title="(Cum.Knitting + Today Knitting) - (Cum.Penal Rcv. - Today Penal Rcv)">Rcvd BL</th>

							  <th width="80">Today</th>
							  <th width="80">Cum. P2P Delivery</th>
							  <th width="80" title="(Cum.Penal Rcv + Today Penal Rcv) - (Cum. P2P Delivery - Today P2P Delivery)">Delivery BL</th>

							  <th width="80">Today</th>
							  <th width="80">Cum. Linking Pcs</th>
							  <th width="80" title="((Cum. P2P Delivery + Today P2P Delivery) - (Cum. Linking Pcs - Today Linking Pcs))" >Linking BL</th>

							  <th width="80">Today</th>
							  <th width="80">Cum. Trim/Men Pcs</th>
							  <th width="80" title="((Cum. Linking Pcs + Today Linking Pcs) - (Cum. Trim - TodayTrim))">Trim.BL</th>

							  <th width="80">Today</th>
							  <th width="80">Cum. Wash Pcs</th>
							  <th width="80" title="((Cum. Trim + Today Trim) - (Cum. Wash Pcs - Today. Wash Pcs))">Wash BL</th>
							  <th width="80" title="((Cum. P2P Delivery + Today P2P Delivery) - (Cum. Wash Pcs - Today. Wash Pcs))">Assemly WIP</th>

							  <th width="80">Today</th>
							  <th width="80">Cum.Sew./Finis.Input</th>
							  <th width="80" title="((Cum. Wash Pcs + Today. Wash Pcs) - (Cum.Sew. + Today Sew.))">Input BL</th>

							  <th width="80">Today</th>
							  <th width="80">Cum.Iron Pcs</th>
							  <th width="80" title="(( (Cum.Sew. + Today Sew.) - (Cum.Iron - Today Iron))">Iron BL</th>

							  <th width="80">Today</th>
							  <th width="80">Cum. Packing Pcs</th>
							  <th width="80" title="((Cum.Iron + Today Iron) - (Cum.Packing - Today Packing))">Metal Pass BL</th>

							  <th width="80">Today</th>
							  <th width="80">Cum. Carton Pcs</th>
							  <th width="80" title="((Cum.Packing + Today Packing) - (Cum. Carton - Today  Carton))">CTN BL</th>  
						  </tr> 
					  </thead>
					   <tbody>						
						  <?
							  $i=1;
							
							  $ttl_plan_qty=0;
							  $ttl_knitting_today_qnty=0;
							  $ttl_knitting_qnty=0;
							  $ttl_kint_bl=0;

							  $ttl_today_dis_qnty =0;
							  $ttl_dis_qnty = 0;
							  $ttl_dis_bl =0;

							  $ttl_today_dis_issue_qnty =0;
							  $ttl_dis_issue_qnty =0;
							  $ttl_delivery_bl =0;
							  $ttl_panel_wip =0;

							  $ttl_today_linking_qnty =0;
							  $ttl_linking_qnty =0;
							  $ttl_linking_bl =0;

							  $ttl_today_trimming_qnty =0;
							  $ttl_trimming_qnty =0;
							  $ttl_trimming_bl =0;
							  
							  $ttl_today_wash_qnty =0;
							  $ttl_wash_qnty  =0;
							  $ttl_wash_bl  =0;
							  $ttl_assemly_wip  =0;

							  $ttl_today_finishing_qnty  =0;
							  $ttl_finishing_qnty  =0;
							  $ttl_input_bl  =0;

							  $ttl_today_iron_qnty =0;
							  $ttl_iron_qnty =0;
							  $ttl_iron_bl =0;
							  $ttl_iron_wip  =0;

							  $ttl_today_pack_qnty =0;
							  $ttl_pack_qnty =0;
							  $ttl_ctn_bl =0;
							  $ttl_ctn_wip  =0;
							  
							  foreach ($data_array as $buyer => $buyer_id) 
							  {
									
								  foreach ($buyer_id as $style => $style_val) 
								  {
										$style_wise_plan_qty=0;

										$style_wise_knit_today_qnty=0;
										$style_wise_knit_qnty=0;
										$style_wise_knit_bl=0;

										$style_today_dis_qnty =0;
										$style_wise_dis_qnty =0;
										$style_wise_dis_bl =0;
										
										$style_today_dis_issue_qnty =0;
										$style_dis_issue_qnty =0;
										$style_wise_delivery_bl =0;
										$style_wise_panel_wip =0;
										
										$style_today_linking_qnty =0;
										$style_wise_linking_qnty =0;
										$style_wise_linking_bl =0;

										$style_today_trimming_qnty =0;
										$style_wise_trimming_qnty =0;
										$style_trim_bl =0;

										$style_wash_today_qnty =0;
										$style_wash_qnty =0;
										$style_wash_bl =0;
										$style_wise_assemly_wip =0;

										$style_today_finishing_qnty  =0;
										$style_wise_finishing_qnty =0;
										$style_input_bl =0;

										$style_today_iron_qnty  =0;
										$style_wise_iron_qnty =0;
										$style_iron_bl  =0;
										$style_iron_wip  =0;

										$style_today_sewing_qnty  =0;
										$style_wise_sewing_qnty  =0;
										$style_metal_pass_bl  =0;

										$ttl_today_sewing_qnty  =0;
										$ttl_sewing_qnty  =0;
										$ttl_metal_pass_bl =0;

										$style_today_pack_qnty  =0;
										$style_wise_pack_qnty =0;
										$style_ctn_bl  =0;
										$style_ctn_wip  =0;										

									  foreach ($style_val as $po => $po_id) 
									  {
										  foreach ($po_id as $color => $val)
										  {
											  $knitting_bl=(($color_po_wise_qnty_arr[$po][$color]['plan_cut_qnty']-$val['knitting_qnty'])-$val['today_knit_qnty']);

											  $rcvd_bl=((($val['knitting_qnty']+$val['today_knit_qnty'])-($val['distribution_qnty'])-$val['today_dis_qnty']));

											  $delivery_bl=((($val['distribution_qnty']+$val['today_dis_qnty'])-($val['dis_issue_qnty'])-$val['today_dis_issue_qnty']));

											  $panel_wip=((($val['distribution_qnty']+$val['today_dis_qnty'])-($val['dis_issue_qnty'])-$val['today_dis_issue_qnty']));
											  $linking_bl=((($val['dis_issue_qnty']+$val['today_dis_issue_qnty'])-($val['linking_complete'])-$val['today_link_complete']));

											  $trim_bl=((($val['linking_complete']+$val['today_link_complete'])-($val['trimming_qnty'])-$val['today_trimming_qnty']));
											  $wash_bl=((($val['trimming_qnty']+$val['today_trimming_qnty'])-($val['wash_qty'])-$val['today_wash_qty']));
											  $assemly_wip=((($val['dis_issue_qnty']+$val['today_dis_issue_qnty'])-($val['wash_qty'])-$val['today_wash_qty']));

											  $input_bl=((($val['wash_qty']+$val['today_wash_qty'])-($val['finishing_qty'])-$val['today_finishing_qty']));

											  $iron_bl=((($val['finishing_qty']+$val['today_finishing_qty'])-($val['iron_qty'])-$val['today_iron_qty']));

											  $iron_wip=((($val['wash_qty']+$val['today_wash_qty'])-($val['iron_qty'])-$val['today_iron_qty']));

											  $metal_pass_bl=((($val['iron_qty']+$val['today_iron_qty'])-($val['sewing_qty'])-$val['today_sewing_qty']));
											 $ctn_bl=((($val['sewing_qty']+$val['today_sewing_qty'])-($val['pack_qty'])-$val['today_pack_qty']));

											  $ctn_wip=((($val['iron_qty']+$val['today_iron_qty'])-($val['pack_qty'])-$val['today_pack_qty']));

											 	$kint_floor_id=""; 
												$kint_floor_id_arr=array_unique(array_filter(explode(",",$val['kint_floor_id']))); 
												foreach($kint_floor_id_arr as $floor_id)
												{
													if($kint_floor_id!="") $kint_floor_id .=", ";
													$kint_floor_id .=$floor_arr[$floor_id];
												}

												$p2p_floor_id=""; 
												$p2p_floor_id_arr=array_unique(array_filter(explode(",",$val['p2p_floor_id']))); 
												foreach($p2p_floor_id_arr as $p2p_id)
												{
													if($p2p_floor_id!="") $p2p_floor_id .=", ";
													$p2p_floor_id .=$floor_arr[$p2p_id];
												}
											//echo $kint_floor_id;
											 

											  if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";																								
											  ?>                         
											                    
											  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
												<td width="30"><?=$i?></td>
												<td width="150"><?=$buyer_arr[$buyer];?></td>
												<td width="200"><?=$style;?></td>
												<td width="150"><?=$color_arr[$color];?></td>
												<td width="200"><?=$val['po_number'];?></td> 
												<td width="100" align="right"><?=$color_po_wise_qnty_arr[$po][$color]['order_quantity'];?></td>
												<td width="100" align="right"><?=$val['gauge'];?></td>
												<td width="100"><?=chop($kint_floor_id,',');?></td>
												<td width="100"><?=chop($p2p_floor_id,',');?></td>
												<td width="120"><?=Change_date_format($val['pub_ship_date']);?></td>
												<td width="80" align="right"><?=$color_po_wise_qnty_arr[$po][$color]['plan_cut_qnty'];?></td>
												<td width="80" align="right"><?=$val['today_knit_qnty'];?></td>
												<td width="80" align="right"><?=$val['knitting_qnty'];?></td>
												<td width="80" align="right"><?=$knitting_bl;?></td>

												<td width="80" align="right"><?=$val['today_dis_qnty'];?></td>
												<td width="80" align="right"><?=$val['distribution_qnty'];?></td>
												<td width="80" align="right"><?=$rcvd_bl?></td>

												<td width="80" align="right"><?=$val['today_dis_issue_qnty'];?></td>
												<td width="80" align="right"><?=$val['dis_issue_qnty'];?></td>
												<td width="80" align="right"><?=$delivery_bl;?></td>
												<td width="80" align="right"><?=$panel_wip;?></td>

												<td width="80" align="right"><?=$val['today_link_complete'];?></td>
												<td width="80" align="right"><?=$val['linking_complete'];?></td>
												<td width="80" align="right"><?=$linking_bl;?></td>

												<td width="80" align="right"><?=$val['today_trimming_qnty'];?></td>
												<td width="80" align="right"><?=$val['trimming_qnty'];?></td>
												<td width="80" align="right"><?=$trim_bl;?></td>

												<td width="80" align="right"><?=$val['today_wash_qty'];?></td>
												<td width="80" align="right"><?=$val['wash_qty'];?></td>
												<td width="80" align="right"><?=$wash_bl;?></td>
												<td width="80" align="right"><?=$assemly_wip;?></td>

												<td width="80" align="right"><?=$val['today_finishing_qty'];?></td>
												<td width="80" align="right"><?=$val['finishing_qty'];?></td>
												<td width="80" align="right"><?=$input_bl;?></td>

												<td width="80" align="right"><?=$val['today_iron_qty'];?></td>
												<td width="80" align="right"><?=$val['iron_qty'];?></td>
												<td width="80" align="right"><?=$iron_bl;?></td>
												<td width="80" align="right"><?=$iron_wip;?></td>

												<td width="80" align="right"><?=$val['today_sewing_qty'];?></td>
												<td width="80" align="right"><?=$val['sewing_qty'];?></td>
												<td width="80" align="right"><?=$metal_pass_bl;?></td>

												<td width="80" align="right"><?=$val['today_pack_qty'];?></td>  
												<td width="80" align="right"><?=$val['pack_qty'];?></td>
												<td width="80" align="right"><?=$ctn_bl;?></td>
												<td width="80" align="right"><?=$ctn_wip;?></td>
												
						 					 </tr> 
												<?
												$i++;		
												$style_wise_plan_qty += $color_po_wise_qnty_arr[$po][$color]['plan_cut_qnty'] ;
												$ttl_plan_qty += $color_po_wise_qnty_arr[$po][$color]['plan_cut_qnty'];

												$style_wise_knit_today_qnty +=$val['today_knit_qnty'];
												$style_wise_knit_qnty +=$val['knitting_qnty'];
												$style_wise_knit_bl +=$knitting_bl;

												$ttl_knitting_today_qnty +=$val['today_knit_qnty'];
												$ttl_knitting_qnty += $val['knitting_qnty'];
												$ttl_kint_bl +=$knitting_bl;

												$style_today_dis_qnty +=$val['today_dis_qnty'];
												$style_wise_dis_qnty +=$val['distribution_qnty'];
												$style_wise_dis_bl +=$rcvd_bl;

												$ttl_today_dis_qnty +=$val['today_dis_qnty'];
												$ttl_dis_qnty += $val['distribution_qnty'];
												$ttl_dis_bl +=$rcvd_bl;

												$style_today_dis_issue_qnty +=$val['today_dis_issue_qnty'];
												$style_dis_issue_qnty +=$val['dis_issue_qnty'];
												$style_wise_delivery_bl +=$delivery_bl;
												$style_wise_panel_wip +=$panel_wip;

												$ttl_today_dis_issue_qnty +=$val['today_dis_issue_qnty'];
												$ttl_dis_issue_qnty += $val['dis_issue_qnty'];
												$ttl_delivery_bl +=$delivery_bl;
												$ttl_panel_wip +=$panel_wip;

												$style_today_linking_qnty +=$val['today_link_complete'];
												$style_wise_linking_qnty +=$val['linking_complete'];
												$style_wise_linking_bl +=$linking_bl;

												$ttl_today_linking_qnty +=$val['today_link_complete'];
												$ttl_linking_qnty += $val['linking_complete'];
												$ttl_trim_bl +=$linking_bl;

												$style_today_trimming_qnty +=$val['today_trimming_qnty'];
												$style_wise_trimming_qnty +=$val['trimming_qnty'];
												$style_trim_bl +=$trim_bl;

												$ttl_today_trimming_qnty +=$val['today_link_complete'];
												$ttl_trimming_qnty += $val['trimming_qnty'];
												$ttl_trimming_bl +=$trim_bl;

												$style_wash_today_qnty +=$val['today_wash_qty'];
												$style_wash_qnty +=$val['wash_qty'];
												$style_wash_bl +=$wash_bl;
												$style_wise_assemly_wip +=$assemly_wip;

												$ttl_today_wash_qnty +=$val['today_wash_qty'];
												$ttl_wash_qnty += $val['wash_qty'];
												$ttl_wash_bl +=$wash_bl;
												$ttl_assemly_wip +=$assemly_wip;

												$style_today_finishing_qnty +=$val['today_finishing_qty'];
												$style_wise_finishing_qnty +=$val['finishing_qty'];
												$style_input_bl +=$input_bl;

												$ttl_today_finishing_qnty +=$val['today_finishing_qty'];
												$ttl_finishing_qnty += $val['finishing_qty'];
												$ttl_input_bl +=$input_bl;
												
												$style_today_iron_qnty +=$val['today_iron_qty'];
												$style_wise_iron_qnty +=$val['iron_qty'];
												$style_iron_bl +=$iron_bl;
												$style_iron_wip +=$iron_wip;

												$ttl_today_iron_qnty +=$val['today_iron_qty'];
												$ttl_iron_qnty += $val['iron_qty'];
												$ttl_iron_bl +=$iron_bl;
												$ttl_iron_wip  +=$iron_wip;

												$style_today_sewing_qnty +=$val['today_sewing_qty'];
												$style_wise_sewing_qnty +=$val['sewing_qty'];
												$style_metal_pass_bl +=$metal_pass_bl;

												$ttl_today_sewing_qnty +=$val['today_sewing_qty'];
												$ttl_sewing_qnty += $val['sewing_qty'];
												$ttl_metal_pass_bl +=$metal_pass_bl;


												$style_today_pack_qnty +=$val['today_pack_qty'];
												$style_wise_pack_qnty +=$val['pack_qty'];
												$style_ctn_bl +=$ctn_bl;
												$style_ctn_wip +=$ctn_wip;

												$ttl_today_pack_qnty +=$val['today_pack_qty'];
												$ttl_pack_qnty += $val['pack_qty'];
												$ttl_ctn_bl +=$ctn_bl;
												$ttl_ctn_wip  +=$ctn_wip;
											 

											 								
											  
										  }
										  
									  }
									 	 	?>				
												<tr  bgcolor="#cddcdc">
													<td colspan="10" align="right"><strong>Style Wise Total</strong></td>
													<td width="100" align="right"><strong><p><?=$style_wise_plan_qty;?></p></strong></td>

													<td width="100" align="right"><strong><p><?=$style_wise_knit_today_qnty;?></p></strong></td>
													<td width="100" align="right"><strong><p><?=$style_wise_knit_qnty;?></p></strong></td>
													<td width="80" align="right"><strong><p><?=$style_wise_knit_bl;?></p></strong></td>

													<td width="80" align="right"><strong><p><?=$style_today_dis_qnty;?></p></strong></td>
													<td width="80" align="right"><strong><p><?=$style_wise_dis_qnty;?></p></strong></td>
													<td width="80" align="right"><strong><p><?=$style_wise_dis_bl;?></p></strong></td>

													<td width="80" align="right"><strong><p><?=$style_today_dis_issue_qnty;?></p></strong></td>
													<td width="80" align="right"><strong><p><?=$style_dis_issue_qnty;?></p></strong></td>
													<td width="80" align="right"><strong><p><?=$style_wise_delivery_bl;?></p></strong></td>
													<td width="80"align="right"><strong><p><?=$style_wise_panel_wip;?></p></strong></td>

													<td width="80" align="right"><strong><p><?=$style_today_linking_qnty;?></p></strong></td>
													<td width="80" align="right"><strong><p><?=$style_wise_linking_qnty;?></p></strong></td>
													<td width="80" align="right"><strong><p><?=$style_wise_linking_bl;?></p></strong></td>

													<td width="80" align="right"><strong><p><?=$style_today_trimming_qnty;?></p></strong></td>
													<td width="80" align="right"><strong><p><?=$style_wise_trimming_qnty;?></p></strong></td>
													<td width="80" align="right"><strong><p><?=$style_trim_bl;?></p></strong></td>

													<td width="80" align="right"><strong><p><?=$style_wash_today_qnty;?></p></strong></td>
													<td width="80" align="right"><strong><p><?=$style_wash_qnty;?></p></strong></td>
													<td width="80" align="right"><strong><p><?=$style_wash_bl;?></p></strong></td>
													<td width="80" align="right"><strong><p><?=$style_wise_assemly_wip;?></p></strong></td>

													<td width="80" align="right"><strong><p><?=$style_today_finishing_qnty;?></p></strong></td>
													<td width="80" align="right"><strong><p><?=$style_wise_finishing_qnty;?></p></strong></td>
													<td width="80" align="right"><strong><p><?=$style_input_bl;?></p></strong></td>

													<td width="80" align="right"><strong><p><?=$style_today_iron_qnty;?></p></strong></td>
													<td width="80" align="right"><strong><p><?=$style_wise_iron_qnty;?></p></strong></td>
													<td width="80" align="right"><strong><p><?=$style_iron_bl;?></p></strong></td>
													<td width="80" align="right"><strong><p><?=$style_iron_wip;?></p></strong></td>

													<td width="80" align="right"><strong><p><?=$style_today_sewing_qnty;?></p></strong></td>
													<td width="80" align="right"><strong><p><?=$style_wise_sewing_qnty;?></p></strong></td>
													<td width="80" align="right"><strong><p><?=$style_metal_pass_bl;?></p></strong></td>

													<td width="80" align="right"><strong><p><?=$style_today_pack_qnty;?></p></strong></td>
													<td width="80" align="right"><strong><p><?=$style_wise_pack_qnty;?></p></strong></td>
													<td width="80" align="right"><strong><p><?=$style_ctn_bl;?></p></strong></td>
													<th width="80" align="right"><strong><p><?=$style_ctn_wip;?></p></strong></th>
												</tr>
											<?	  
								  }
							  }
						  ?>										  													
					  </tbody>		
					  <tfoot>
						<tr>
							<th colspan="10" align="right"><strong>Grand Total</strong></th>
							<th width="100"><?=$ttl_plan_qty; ?></th>

							<th width="100"><?=$ttl_knitting_today_qnty?></th>
							<th width="100"><?=$ttl_knitting_qnty; ?></th>
							<th width="80"><?=$ttl_kint_bl; ?></th>

							<th width="80"><?=$ttl_today_dis_qnty; ?></th>
							<th width="80"><?=$ttl_dis_qnty; ?></th>
							<th width="80"><?=$ttl_dis_bl; ?></th>
							<th width="80"><?=$ttl_today_dis_issue_qnty; ?></th>
							<th width="80"><?=$ttl_dis_issue_qnty; ?></th>
							<th width="80"><?=$ttl_delivery_bl; ?></th>
							<th width="80"><?=$ttl_panel_wip; ?></th>

							<th width="80"><?=$ttl_today_linking_qnty; ?></th>
							<th width="80"><?=$ttl_linking_qnty; ?></th>
							<th width="80"><?=$ttl_linking_bl; ?></th>

							<th width="80"><?=$ttl_today_trimming_qnty; ?></th>
							<th width="80"><?=$ttl_trimming_qnty; ?></th>
							<th width="80"><?=$ttl_trimming_bl; ?></th>

							<th width="80"><?=$ttl_today_wash_qnty; ?></th>
							<th width="80"><?=$ttl_wash_qnty; ?></th>
							<th width="80"><?=$ttl_wash_bl; ?></th>
							<th width="80"><?=$ttl_assemly_wip; ?></th>

							<th width="80"><?=$ttl_today_finishing_qnty; ?></th>
							<th width="80"><?=$ttl_finishing_qnty; ?></th>
							<th width="80"><?=$ttl_input_bl; ?></th>

							<th width="80"><?=$ttl_today_iron_qnty; ?></th>
							<th width="80"><?=$ttl_iron_qnty; ?></th>
							<th width="80"><?=$ttl_iron_bl; ?></th>
							<th width="80"><?=$ttl_iron_wip; ?></th>

							<th width="80"><?=$ttl_today_sewing_qnty; ?></th>
							<th width="80"><?=$ttl_sewing_qnty; ?></th>
							<th width="80"><?=$ttl_metal_pass_bl; ?></th>

							<th width="80"><?=$ttl_today_pack_qnty; ?></th>
							<th width="80"><?=$ttl_pack_qnty; ?></th>
							<th width="80"><?=$ttl_ctn_bl; ?></th>
							<th width="80"><?=$ttl_ctn_wip; ?></th>
						</tr>

				     </tfoot> 
				  </table>        
		  </div>
		</fieldset>
	   <?
	}
	?>
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




